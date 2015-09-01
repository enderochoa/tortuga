<?php
class Prenom extends Controller {

	function Prenom(){
		parent::Controller();
		$this->load->library("rapyd");

		$this->titulo='Generar Prenomina';
	}

	function index(){
		$this->rapyd->load("dataform");
		$form = new DataForm('nomina/prenom/index/process');

		$form->contrato = new dropdownField("Contrato", "contrato");
		$form->contrato->option("","Seleccionar");
		$form->contrato->options("SELECT codigo,nombre FROM noco ORDER BY nombre");
		$form->contrato->rule='required';

		$form->fechac = new dateonlyField("Fecha de corte", "fechac");
		$form->fechac->rule='required|chfecha';
		$form->fechac->insertValue = date("Y-m-d");
		$form->fechac->size=12;

		$form->fechap = new dateonlyField("Fecha de pago", "fechap");
		$form->fechap->rule='required|chfecha';
		$form->fechap->insertValue = date("Y-m-d");
		$form->fechap->size=12;

		$form->submit("btnsubmit","Generar");
		$form->build_form();

		if ($form->on_success()){
			$this->load->dbforge();

			$tabla   ='prenom';
			$tablap  ='pretab';
			$this->db->simple_query("TRUNCATE $tabla");
			$this->db->simple_query("TRUNCATE $tablap");
			$contrato=$this->db->escape($form->contrato->newValue);
			$fechac  =$form->fechac->newValue;
			$fechap  =$form->fechap->newValue;

			$mSQL  = "INSERT IGNORE INTO $tabla (contrato, codigo,nombre, concepto, grupo, tipo, descrip, formula, monto, fecha, fechap,cuota,cuotat,pprome,trabaja) ";
			$mSQL .= "SELECT $contrato, b.codigo, CONCAT(RTRIM(b.apellido),'/',b.nombre) nombre, ";
			$mSQL .= "a.concepto, a.grupo, a.tipo, a.descrip, a.formula, 0, $fechac, $fechap , 0, 0, 0, $contrato ";
			$mSQL .= "FROM conc a JOIN itnoco c ON a.concepto=c.concepto ";
			$mSQL .= "JOIN pers b ON b.contrato=c.codigo WHERE c.codigo=$contrato AND b.status='A' ";

			$this->db->simple_query($mSQL);

			$fields = $this->db->list_fields($tablap);
			$ii=count($fields);
			for($i=5;$i<$ii;$i++)
				$this->dbforge->drop_column($tablap,$fields[$i]);
			unset($fields);

			$query = $this->db->query("SELECT concepto FROM itnoco WHERE codigo=$contrato ORDER BY concepto");
			foreach ($query->result() as $row){
				$ind    = 'c'.trim($row->concepto);
				$fields[$ind]=array('type' => 'decimal(17,2)','default' => 0);
			}
			//print_r($fields);//FERARULA
			$this->dbforge->add_column($tablap, $fields);
			unset($fields);

			$frec=$this->datasis->dameval("SELECT tipo FROM noco WHERE codigo=$contrato");
			$query = $this->db->query("SELECT codigo,CONCAT(RTRIM(apellido),'/',nombre) AS nombre FROM pers WHERE contrato=$contrato");
			foreach ($query->result() as $row){
				$data['codigo'] = $row->codigo;
				$data['frec']   = $frec;
				$data['fecha']  = $fechac;
				$data['nombre'] = $row->nombre;
				$data['total']  = 0;
				$mSQL = $this->db->insert_string($tablap, $data);
				$this->db->simple_query($mSQL);
			}

			/*$query = $this->db->query("SELECT FROM pers JOIN ON WHERE");
			if ($query->num_rows() > 0){
				foreach ($query->result() as $row){
					$data['contrato'] ='';
					$data['codigo']   ='';
					$data['nombre']   ='';
					$data['concepto'] ='';
					$data['tipo']     ='';
					$data['descrip']  ='';
					$data['grupo']    ='';
					$data['formula']  ='';
					$data['monto']    ='';
					$data['fecha']    ='';
					$data['cuota']    ='';
					$data['cuotat']   ='';
					$data['valor']    ='';
					$data['adicional']='';
					$data['fechap']   ='';
					$data['trabaja']  ='';
					$data['pprome']   ='';
				}
			}*/
		}

		$data['content'] = $form->output;
		$data['title']   = '<h1>'.$this->titulo.'</h1>';
		$data["head"]    = $this->rapyd->get_head();
		$this->load->view('view_ventanas', $data);
	}

	function montos(){
		$this->rapyd->load('datagrid','fields','datafilter');

		/*$this->load->library('form_validation');

		if ($this->form_validation->run() == FALSE){
			$this->load->view('myform');
		}else{
			$this->load->view('formsuccess');
		}*/

		$error='';
		if($this->input->post('pros')!==FALSE){
			$concepto =$this->db->escape($this->input->post('concepto'));
			$pmontos  =$this->input->post('monto');
			foreach($pmontos AS $cod=>$cant){
				$cod = $this->db->escape($cod);
				if(!is_numeric($cant)){
					$error.="$cant no es un valor numerico<br>";
				}else{
					$data  = array('monto' => $cant);
					$where = "codigo = $cod  AND concepto =$concepto ";
					$mSQL  = $this->db->update_string('prenom', $data, $where);
					$this->db->simple_query($mSQL);
				}
			}
		}

		$filter = new DataFilter("&nbsp;", 'prenom');
		$filter->error_string=$error;

		$filter->concepto = new dropdownField("Concepto", "concepto");
		$filter->concepto->option("","Seleccionar");
		$filter->concepto->options("SELECT concepto,descrip FROM prenom GROUP BY concepto ORDER BY descrip");
		$filter->concepto->clause  ="where";
		$filter->concepto->operator="=";
		$filter->concepto->rule    = "required";

		$filter->buttons("reset","search");
		$filter->build();

		$ggrid='';
		if ($filter->is_valid()){
			$ggrid =form_open('/nomina/prenom/montos/search/osp');
			$ggrid.=form_hidden('concepto', $filter->concepto->newValue);

			$monto = new inputField("Monto", "monto");
			$monto->grid_name='monto[<#codigo#>]';
			$monto->status   ='modify';
			$monto->size     =12;
			$monto->css_class='inputnum';

			$grid = new DataGrid("Concepto (".$filter->concepto->newValue.") ".$filter->concepto->options[$filter->concepto->newValue]);
			//$grid->db->where('concepto','015');
			//$grid->per_page = $filter->db->num_rows() ;
			$grid->column("C&oacute;digo", "codigo");
			$grid->column("Nombre", "nombre");
			$grid->column("Monto" , $monto,'align=\'right\'');
			$grid->submit('pros', 'Guardar',"BR");
			$grid->build();
			$ggrid.=$grid->output;
			$ggrid.=form_close();

		}
		$script ='
		<script type="text/javascript">
		$(function() {
			$(".inputnum").numeric(".");
		});
		</script>';
		$data['content'] = $filter->output.$ggrid;
		$data['title']   = '<h1>Asignaci&oacute;n de montos</h1>';
		$data['script']  = $script;
		$data["head"]    = $this->rapyd->get_head().script("jquery.pack.js").script("plugins/jquery.numeric.pack.js").script("plugins/jquery.floatnumber.js");
		$this->load->view('view_ventanas', $data);
	}

	function formulas(){
		$this->load->library('pnomina');
		$this->pnomina->CODIGO='002';
		$this->pnomina->MONTO =2500;

		$query = $this->db->query('SELECT * FROM conc LIMIT 37');
		if ($query->num_rows() > 0){
			foreach ($query->result() as $row){
				echo $row->formula." = ";
				echo $this->pnomina->evalform($row->formula);
				echo "\n";
				//echo $this->pnomina->_traduce($row->formula)."\n\n";
			}
		}
	}

	function prueba(){
		$string='navalIF(kavraIF(ta';
		$pos=strrpos($string,'IF(',0);
		echo $pos;
		//$variable = -p()+($x>10?1:2)*10+1;


/*
		if(end($arr)==null){
			echo 'es nulo';
		}

		if (preg_match("/I/", "I")) {
			echo "A match was found.";
		}else{
			echo "A match was not found.";
		}
*/
	}

}
?>