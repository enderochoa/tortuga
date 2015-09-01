<?php
class R_caja extends Controller {
	var $titp='Cajas';
	var $tits='Caja';
	var $url ='recaudacion/r_caja/';
	function R_caja(){
		parent::Controller();
		$this->load->library("rapyd");
		$this->datasis->modulo_id(426,1);
	}
	function index(){
		redirect($this->url."filteredgrid");
	}
	function filteredgrid(){
		$this->rapyd->load('datafilter','datagrid');

		$filter = new DataFilter($this->titp, 'r_caja');
		

		$filter->id = new inputField('Id','id');
		$filter->id->rule      ='trim';
		$filter->id->size      =13;
		$filter->id->maxlength =11;

		$filter->nombre = new inputField('Nombre','nombre');
		$filter->nombre->rule      ='trim';
		$filter->nombre->size      =52;
		$filter->nombre->maxlength =50;

		$filter->buttons('reset', 'search');
		$filter->build();

		$uri = anchor($this->url.'dataedit/show/<raencode><#id#></raencode>','<#id#>');

		$grid = new DataGrid('');
		$grid->order_by('id');
		$grid->per_page = 40;

		$grid->column_orderby('Id'                ,"$uri"                                             ,'id'           ,'align="left"');
		$grid->column_orderby('Nombre'            ,"nombre"                                           ,'nombre'       ,'align="left"');
		$grid->column_orderby('Contador'          ,"id_contador"                                      ,'id_contador'  ,'align="left"');

		$grid->add($this->url.'dataedit/create');
		$grid->build();

		$data['filtro']  = $filter->output;
		$data['content'] = $grid->output;
		$data['head']    = $this->rapyd->get_head().script('jquery.js');
		$data['title']   = $this->titp;
		$this->load->view('view_ventanas', $data);

	}
	function dataedit(){
		$this->rapyd->load('dataedit');

		$edit = new DataEdit($this->tits, 'r_caja');

		$edit->back_url = site_url($this->url."filteredgrid");

		$edit->post_process('insert','_post_insert');
		$edit->post_process('update','_post_update');
		$edit->post_process('delete','_post_delete');

		$edit->id = new inputField('Id','id');
		$edit->id->rule     ='trim';
		$edit->id->size      =13;
		$edit->id->maxlength =11;
		$edit->id->mode      ='autohide';
		$edit->id->when      =array('show','modify');

		$edit->nombre = new inputField('Nombre','nombre');
		$edit->nombre->rule     ='trim';
		$edit->nombre->size      =52;
		$edit->nombre->maxlength =50;

		$edit->id_contador = new dropdownField('Contador','id_contador');
		$edit->id_contador->rule     ='trim|required';
		$edit->id_contador->options("SELECT id,nombre FROM r_contador");
		
		$edit->punto_codbanc = new dropdownField('Banco por Defecto para Punto de Venta','punto_codbanc');
		$edit->punto_codbanc->rule     ='';
		$edit->punto_codbanc->option("","");
		$edit->punto_codbanc->options("SELECT codbanc,CONCAT(codbanc,' ',banco) FROM banc ORDER BY codbanc");
		

		$edit->buttons('add','modify', 'save', 'undo', 'delete', 'back');
		$edit->build();
		$data['content'] = $edit->output;
		$data['head']    = $this->rapyd->get_head();
		$data['title']   = $this->tits;
		$this->load->view('view_ventanas', $data);

	}

	function _post_insert($do){
		$primary =implode(',',$do->pk);
		logusu($do->table,"Creo $this->tits $primary ");
	}
	function _post_update($do){
		$primary =implode(',',$do->pk);
		logusu($do->table,"Modifico $this->tits $primary ");
	}
	function _post_delete($do){
		$primary =implode(',',$do->pk);
		logusu($do->table,"Elimino $this->tits $primary ");
	}

	function instalar(){
		$mSQL="CREATE TABLE `r_caja` (
		  `id` int(11) NOT NULL AUTO_INCREMENT,
		  `nombre` varchar(50) DEFAULT NULL,
		  `proxnumero` int(11) DEFAULT '1',
		  PRIMARY KEY (`id`)
		) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8";
		$this->db->simple_query($mSQL);
		
		$mSQL="ALTER TABLE `usuario` ADD COLUMN `caja` INT NULL DEFAULT NULL AFTER `internet`";
		$this->db->simple_query($mSQL);
		$mSQL="ALTER TABLE `r_caja` ADD COLUMN `id_contador` INT(11) NULL DEFAULT NULL AFTER `id`";
		$this->db->simple_query($mSQL);
		$mSQL="ALTER TABLE `r_caja` 	ADD COLUMN `punto_codbanc` VARCHAR(5) NULL DEFAULT NULL";
		$this->db->simple_query($mSQL);
	}
}
?>
