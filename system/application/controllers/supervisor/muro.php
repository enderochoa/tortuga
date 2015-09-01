<?php
	class muro extends Controller{
		function muro(){
			parent::Controller();
			$this->load->library("rapyd");
			$this->datasis->modulo_id(5,1);
		}
		function index(){
			redirect("supervisor/muro/filteredgrid");	
		}
		function filteredgrid(){
			$this->rapyd->load("datafilter","datagrid");
			
			$filter = new DataFilter("Filtro de Muro",'muro');
				
			$filter->envia= new inputField("Envia","envia");
			$filter->envia->size=20;
			$filter->envia->maxlength=15;
			
			$filter->recibe= new inputField("Recibe","recibe");
			$filter->recibe->size=20;
			$filter->recibe->maxlength=15;
			
		  $filter->mensaje= new inputField("Mensaje","mensaje");
			$filter->mensaje->size=50;
						
			$filter->buttons("reset","search");
			$filter->build();
			
			$uri = anchor('supervisor/muro/dataedit/show/<#codigo#>','<#codigo#>');
			
			$grid = new DataGrid("Lista de Muro");
			$grid->per_page=15;
			
			$grid->column("Codigo",$uri);
			$grid->column("Envia","envia");
			$grid->column("Recibe","recibe");
			$grid->column("Mensaje","mensaje");
			
			$grid->add("supervisor/muro/dataedit/create");
			$grid->build();
			
			$data['content'] = $filter->output.$grid->output;
			$data['title']   = " Muro ";
			$data["head"]    = $this->rapyd->get_head();	
			$this->load->view('view_ventanas', $data);	
	}
	function dataedit(){
		$this->rapyd->load("dataedit");
		
		$edit = new DataEdit("Muro","muro");
		$edit->back_url = site_url("supervisor/muro/");
		$edit->pre_process('insert','_pre_insert');
		$edit->post_process('insert','_post_insert');
		$edit->post_process('update','_post_update');
		$edit->post_process('delete','_post_delete');
		
		$edit->recibe = new dropdownField("Recibe","recibe");
		$edit->recibe->option("Todos","Todos");
		$edit->recibe->options("SELECT us_codigo, us_nombre FROM usuario ORDER BY us_nombre");
		$edit->recibe->style = "width:200px";
		$edit->recibe->rule="required";
		
		$edit->mensaje = new textareaField("Mensaje", "mensaje");
		$edit->mensaje->cols = 80;
		$edit->mensaje->rows =3;
				  
		$edit->buttons("modify", "save", "undo", "delete", "back");
		$edit->build();
		
		$data['content'] = $edit->output; 		
		$data['title']   = " Muro ";
		$data["head"]    = $this->rapyd->get_head();
		$this->load->view('view_ventanas', $data);
	}
	
	function _pre_insert($do){
	 	$do->set('envia', $this->session->userdata('usuario'));
	}
	
	function _post_insert($do){
		$codigo=$do->get('codigo');				
		logusu('muro',"Mensaje de Muro $codigo CREADO");
	}
	
	function _post_update($do){
		$codigo=$do->get('codigo');				
		logusu('muro',"Mensaje de Muro $codigo MODIFICADO");
	}
	
	function _post_delete($do){
		$codigo=$do->get('codigo');				
		logusu('muro',"Mensaje de Muro $codigo ELIMINADO");
	}
	
	function instalar(){
		$mSQL="CREATE TABLE IF NOT EXISTS `muro` (
		  `codigo` int(11) NOT NULL auto_increment,
		  `envia` varchar(15) default NULL,
		  `recibe` varchar(15) default NULL,
		  `mensaje` text,
		  `estampa` timestamp NOT NULL default CURRENT_TIMESTAMP on update CURRENT_TIMESTAMP,
		  PRIMARY KEY  (`codigo`)
		) ENGINE=MyISAM AUTO_INCREMENT=0 DEFAULT CHARSET=utf8";
		$this->db->simple_query($mSQL);
	}
}
?>
