<?php
class Internet extends Controller {

	function Internet(){
		parent::Controller(); 
		$this->load->library("rapyd");
		//$this->datasis->modulo_id(907,1);
	}

	function index(){
		//echo $_SERVER['SERVER_ADDR'];
		$cont =anchor('supervisor/internet/dataedit/show/IPACEPTADOS','Lista de ip aceptadas').'<br />';
		$cont.=anchor('supervisor/internet/dataedit/show/PAGINASNEGADAS','Lista de paginas negras');
		$data['content'] =$cont;
		$data["head"]    = $this->rapyd->get_head();
		$data['title']   =' Control de acceso a internet ';
		$this->load->view('view_ventanas', $data);
		
	}
	function dataedit(){
		$codigo=$this->uri->segment(5);
		$this->rapyd->load("dataedit");

		$edit = new DataEdit("Configuracion internet","internet");
		$edit->back_url = site_url("supervisor/internet");
		
		if($codigo=='IPACEPTADOS'){
			$edit->pre_process('update','_pre_update');
			$titulo='Lista de ip con acceso a internet';
		}else{
			$titulo='Lista de direcciones web prohibidas';
		}
		$edit->pre_process('insert','_pre_insert' );
		$edit->pre_process('delete','_pre_delete' );
		$edit->post_process('update','_post_update');

		$edit->lista = new textareaField("Lista", "lista");
		$edit->lista->rule = "required";
		$edit->lista->rows = 15;
		
		$edit->buttons("modify", "save", "undo", "back");
		$edit->build();
		
		$data['content'] = $edit->output; 		
		$data['title']   = " $titulo ";
		$data["head"]    = $this->rapyd->get_head();
		$this->load->view('view_ventanas', $data);
	}
	
	function _pre_update($do){
		$lista=$do->get('lista');
		$re = '/([1-9]|[1-9][0-9]|1[0-9]{2}|2[0-4][0-9]|25[0-5])\.(([0-9]{1,2}|1[0-9]{2}|2[0-4][0-9]|25[0-5])\.){2}(1[0-9]{2}|2[0-4][0-9]|25[0-5]|[0-9]{1,2})/';
		preg_match_all($re,$lista,$matches);
		$matches=array_unique($matches[0]);
		$lista=implode(' ',$matches);
		$do->set('lista', $lista);
	}
	
	function _pre_insert($do){
		return false;
	}
	
	function _pre_delete($do){
		return false;
	}
	
	function _post_update($do){
		$nombre=$do->get('nombre');
		$lista =$do->get('lista');
		logusu('internet'," Lista de nombre MODIFICADA $lista");
	}
	
	function instalar(){
		$mSQL="CREATE TABLE `internet` (
		  `nombre` varchar(20) NOT NULL default '',
		  `lista` text,
		  `descrip` varchar(100) default NULL,
		  PRIMARY KEY  (`nombre`)
		) ENGINE=MyISAM DEFAULT CHARSET=utf8";
		$this->db->simple_query($mSQL);
		$mSQL="INSERT INTO internet (nombre) VALUES ('IPACEPTADOS')";                        
		$this->db->simple_query($mSQL);
		$mSQL="INSERT INTO internet (nombre) VALUES ('PAGINASNEGADAS')";
		$this->db->simple_query($mSQL);
	}
} 
?>
  
  
