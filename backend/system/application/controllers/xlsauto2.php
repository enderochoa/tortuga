<?php
	class xlsauto2 extends Controller{
		var $sql;
		
		function xlsauto2(){
		parent::Controller(); 
		$this->load->library("rapyd");
			}
		function index(){		
			redirect("/xlsauto2/repoauto");
		}
		function repoauto(){
			exit("hello");
			$this->load->library("XLSReporte");
			$this->rapyd->load("dataform","datagrid");
		
		$filter = new DataForm("xlsauto2/repoauto/process");  
		
		$filter->sql = new textareaField("Consulta SQL", "sql");
		$filter->sql->cols = 80;
		$filter->sql->rows = 6;
		$filter->sql->rule = "required";
		
		$filter->submit("btnsubmit","Ejecutar");      
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
				if (preg_match('/[Ss][Ee][Ll][Ee][Cc][Tt]/', $mSQL)>0){
					$num_rows  = mysql_num_rows($result);					
				}else{
					$num_rows  = 0;					
				}

				if ($num_rows>0){
					$colunas   =mysql_num_fields($result);
					while ($row = mysql_fetch_assoc($result)){
	 					$data[]=$row;
					}
					
					$xls = new XLSReporte($mSQL);
					
					$xls->setTitulo("TARJETA");
					$xls->setSubTitulo("Sub Titulo de Tarjeta");
					$xls->setSobreTabla("Este es el titulo d la tabla");
					$xls->setHeadValores('TITULO1');
					$xls->setSubHeadValores('TITULO2','TITULO3'); 					
					
					foreach ($data[0] as $campos=>$value)				
						$xls->AddCol($campos,$campos);
					$xls->Table();
					$xls->Output();
				}else{
					$salida="Su consulta no arrojo algun resultado";
					}
			}
		}
		
		$data['content'] = $filter->output.$salida;
		$data['title']   = "<h1>Consulta SQL que muestra los resultados en extencion \".xls\"</h1>";
		$data["head"]    = $this->rapyd->get_head();
		$this->load->view('view_ventanas', $data);
	  }
	  
	  function loquedebetener(){
	  	$mSQL='SELECT * FROM tardet';
	  	$xls = new XLSReporte($mSQL);
			$xls->Tcols();
			$xls->Output();
	  }
	}
?>