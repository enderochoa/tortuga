<?php
class ejecutasql extends Controller {

	function ejecutasql(){
		parent::Controller(); 
		$this->load->library('rapyd');
		//$this->rapyd->config->set_item('theme','limpio');
		//$this->datasis->modulo_id(308,1);
		if(!$this->datasis->essuper()) show_404();
	}

	function index(){
		redirect('/ejecutasql/filteredgrid');
	}

	function filteredgrid(){
		
		
		$this->rapyd->load('dataform','datagrid');
		$this->load->library('encrypt');

		$filter = new DataForm('ejecutasql/filteredgrid/process');

		$filter->sql = new textareaField('', 'sql');
		$filter->sql->cols = 90;
		$filter->sql->rows = 9;
		$filter->sql->rule = 'required';

		$filter->submit('btnsubmit','Ejecutar');
		$filter->build_form();

		$salida='';
		if ($filter->on_success()){
			$data=array();
			$mSQL=$filter->sql->value;

			$link = @mysql_connect($this->db->hostname, $this->db->username, $this->db->password) or die('Error de coneccion');
			mysql_select_db($this->db->database,$link) or die('Base de datos no seleccionable');
			$result = mysql_query($mSQL,$link);

			if (!$result) {
				$salida=mysql_errno($link) . ": " . mysql_error($link);
			}else{
				if (is_resource($result)){
					$num_rows  = mysql_num_rows($result);
					$afectados = 0;
				}elseif(is_bool($result)){
					$num_rows  = 0;
					$afectados = mysql_affected_rows();
				}else{
					$num_rows  = 0;
					$afectados = 0;
				}

				if ($num_rows>0){
					$colunas   =mysql_num_fields($result);
					while ($row = mysql_fetch_assoc($result)){
	 					$data[]=$row;
					}
					$grid = new DataGrid("Filas : $num_rows, Columnas : $colunas ,Afectados :$afectados",$data);
					$grid->per_page=100000;
					foreach ($data[0] as $campos=>$value)
						$grid->column($campos, $campos);
					$grid->build();
					$salida=$grid->output;

					if (stristr($mSQL, 'SELECT')){
						$mSQL2 = $this->encrypt->encode($mSQL);

 						$salida.="<form action='/../..".base_url()."xlsauto/repoauto2/'; method='post'>
 						<input size='100' type='hidden' name='mSQL' value='$mSQL2'>
 						<input type='submit' value='Descargar a Excel' name='boton'/>
 						</form>";

					}
				}elseif($afectados>0){
					$salida="Filas afectadas $afectados";
				}else{
					$salida='Esta consulta no genero resultados';
				}
			}
		}

		$data['content'] = $filter->output.$salida;
		$data['title']   = heading('Consulta SQL');
		$data['head']    = $this->rapyd->get_head();
		$this->load->view('view_ventanas', $data);
	}
}
