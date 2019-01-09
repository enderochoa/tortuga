<?php

class Reportes extends Controller
{
	var $cargo=0;
	var $opciones=array();
	
	function Reportes(){
		parent::Controller();
		$this->load->library("rapyd");
		$this->opciones=array('PDF'=>'pdf','XLS'=>'xls');
	}

	function index(){
		$repo =$this->uri->segment(3);
		$data['pre']=$repo;
		$data['titu']="Listados $repo";
		$data['repo']=$repo;
		$this->load->view('view_repoframe',$data);
	}
	function ver(){
		//$this->load->library('XLSReporte');
		
		$this->rapyd->load("datafilter2");
		$repo =$this->uri->segment(3);
		$esta =$this->uri->segment(4);
		$mSQL= "SELECT proteo FROM reportes WHERE nombre='$repo'";
		$mc  = $this->datasis->dameval($mSQL);
		$mc  = str_replace('<?php','',$mc);
		$nombre =strtolower($repo).'.pdf';
		if(!empty($mc)){
			$data["regresar"]='<a href='.site_url("/reportes/enlistar/$esta").'>'.image('go-previous.png','Regresar',array('border'=>0)).'Regresar'.'</a>';
			
			$_formato=$this->input->post('salformat');
			if($_formato or !empty($_formato))
				$_mclase=$_formato.'Reporte';
			else 
				$_mclase='PDFReporte';
			$this->load->library($_mclase);
			
			$this->db->_escape_char='';
			$this->db->_protect_identifiers=false;
			eval($mc);
		}else{
			echo 'Reporte '.$repo.' no definido para Tortuga <br>';
			echo '<a href='.site_url("/reportes/enlistar/$esta").'>Regresar</a>';
		}
	}

	function enlistar(){
		//$this->session->_flashdata_sweep();
		//echo '<pre>';print_r($this->session->userdata);echo '</pre>';
		$repo =$this->uri->segment(3);
		$this->rapyd->load("datatable");
		$this->rapyd->config->set_item("theme","clean");   
		
		if($repo){
			$repo=strtoupper($repo);
			
			$grid = new DataTable();
			//$grid->db->select('titulo, mensaje, SUBSTR(SUBSTRING_INDEX(REPLACE(ejecutar, "\'",\'"\'), \'"\', 2),10) AS nombre');  
			//$grid->db->from("tmenus");
			//$grid->db->where("modulo='".$repo."LIS' AND TRIM(ejecutar) LIKE 'REPOSQL(%)'");
			$grid->db->select('a.titulo, a.mensaje, a.nombre'); 
			$grid->db->from("intrarepo AS a");
			$grid->db->join("reportes AS b","a.nombre=b.nombre");
			$grid->db->where('a.modulo',$repo);
			$grid->db->where('a.activo','S');
			
			$grid->per_row = 3; 
			$grid->use_function("substr","strtoupper");
			$grid->cell_template = '
			<div style="padding:4px">
			  <div style="color:#119911; font-weight:bold">'.anchor('reportes/ver/<#nombre#>/'.$repo,"<#titulo#>",array('onclick'=>"parent.navegador.afiltro()")).'</div>
			   <htmlspecialchars><#mensaje#></htmlspecialchars>
			</div>'; 
			$grid->build();
			//echo $grid->db->last_query();
		}
		if($repo AND $grid->recordCount>0) $data['forma'] = $grid->output; else $data['forma'] ='<p class="mainheader">No se encontrar&oacute;n reportes.</p>';
		//echo $grid->db->last_query();
		
		$data['head']=$this->rapyd->get_head();
		$data['titulo'] = "<center><h2>Listados Disponibles</h2></center>";
		$data['repo']=$repo;
		//$CI->session->set_userdata('estaba', $CI->uri->uri_string());
		$this->load->view('view_reportes', $data);
		
	}
	function cabeza(){
		$data['repo']  =$this->uri->segment(3);
		$data['nombre']=$this->uri->segment(4);
		
		$this->load->view('view_repoCabeza',$data);
	}
	function consulstatus(){
		echo 'esto es una prueba';
	}
	
	function sinvlineas(){
		if (!empty($_POST["dpto"])){ 
			$departamento=$_POST["dpto"];
		}elseif (!empty($_POST["depto"])){
 			$departamento=$_POST["depto"];
		}
		
		$this->rapyd->load("fields");  
		$where = "";  
		$sql = "SELECT linea, descrip FROM line $where";
		$linea = new dropdownField("Subcategoria", "linea");  
		
		if (!empty($departamento)){  
		  $where = "WHERE depto = ".$this->db->escape($departamento);
		  $sql = "SELECT linea, descrip FROM line $where";
		  $linea->option("","");  
			$linea->options($sql); 
		}else{
			 $linea->option("","Seleccione Un Departamento"); 
		} 
		$linea->status   = "modify";
		$linea->onchange = "get_grupo();";
		$linea->build();
		echo $linea->output;
	}
	
	function sinvgrupos(){
		$this->rapyd->load("fields");  
		$where = "WHERE ";
		
		$grupo = new dropdownField("Subcategoria", "grupo");
		if (!empty($_POST["linea"]) AND !empty($_POST["dpto"])) {
			if($_POST["dpto"]!='T')$where .= "depto = ".$this->db->escape($_POST["dpto"]).' AND ';
			$where .= "linea = ".$this->db->escape($_POST["linea"]);
			$sql = "SELECT grupo, nom_grup FROM grup $where";
			$grupo->option("",""); 
			$grupo->options($sql);
		}else{
			$grupo->option("","Seleccione una l&iacute;nea"); 
		} 
		$grupo->status = "modify";  
		$grupo->build();
		echo $grupo->output; 
	}
	function modelos(){
		$this->rapyd->load("fields");  
		$where = "";  
		$sql = "SELECT id,modelo FROM modelos $where";
		$modelo = new dropdownField("Subcategoria", "modelo");  
		
		if (!empty($_POST["marca"])){  
		  $where = "WHERE marca = ".$this->db->escape($_POST["marca"]);
		  $sql = "SELECT id, modelo FROM modelos $where";
		  $modelo->option("","");  
			$modelo->options($sql); 
		}else{
			 $modelo->option("","Seleccione Una Marca"); 
		} 
		$modelo->status   = "modify";
		//$linea->onchange = "get_grupo();";
		$modelo->build();
		echo $modelo->output;
		 
	}
	
	function ver2(){
		//$this->load->library('XLSReporte');
		
		$this->rapyd->load("datafilter2");
		$repo =$this->uri->segment(3);
		$esta =$this->uri->segment(4);
		$mSQL= "SELECT proteo FROM reportes WHERE nombre='$repo'";
		$mc  = $this->datasis->dameval($mSQL);
		$mc  = file_get_contents('/srv/www/htdocs/tortuga/system/application/controllers/treporte.php');
		$mc  = str_replace('<?php','',$mc);
		$nombre =strtolower($repo).'.pdf';
		if(!empty($mc)){
			$data["regresar"]='<a href='.site_url("/reportes/enlistar/$esta").'>'.image('go-previous.png','Regresar',array('border'=>0)).'Regresar'.'</a>';
			
			$_formato=$this->input->post('salformat');
			if($_formato or !empty($_formato))
				$_mclase=$_formato.'Reporte';
			else 
				$_mclase='PDFReporte';
			$this->load->library($_mclase);
			
			$this->db->_escape_char='';
			$this->db->_protect_identifiers=false;
			eval($mc);
		}else{
			echo 'Reporte '.$repo.' no definido para Tortuga <br>';
			echo '<a href='.site_url("/reportes/enlistar/$esta").'>Regresar</a>';
		}
	}
	
	function instalar(){
		$mSQL="ALTER TABLE `reportes` ADD `proteo` TEXT NULL";
		$this->db->simple_query($mSQL);
		$mSQL="ALTER TABLE `reportes` ADD `harbour` TEXT NULL";
		$this->db->simple_query($mSQL);
	}
}
?>
