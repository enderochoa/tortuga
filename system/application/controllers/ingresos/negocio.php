<?php
class negocio extends Controller {
	var $titp='Tipos de Negocio';
	var $tits='Tipo de Negocio';
	var $url ='ingresos/negocio/';
	function negocio(){
		parent::Controller();
		$this->load->library("rapyd");
		//$this->datasis->modulo_id(402,1);
	}
	function index(){
		redirect($this->url."filteredgrid");
	}
	function filteredgrid(){
		$this->rapyd->load('datafilter','datagrid');

		$filter = new DataFilter($this->titp, 'negocio');

		$filter->codigo = new inputField('Codigo','codigo');
		$filter->codigo->rule      ='max_length[5]';
		$filter->codigo->size      =7;
		$filter->codigo->maxlength =5;

		$filter->nombre = new inputField('Nombre','nombre');
		$filter->nombre->rule      ='max_length[20]';
		$filter->nombre->size      =22;
		$filter->nombre->maxlength =20;

		$filter->monto = new inputField('Monto','monto');
		$filter->monto->rule      ='max_length[8]';
		$filter->monto->size      =10;
		$filter->monto->maxlength =8;

		$filter->buttons('reset', 'search');
		$filter->build();

		$uri = anchor($this->url.'dataedit/show/<raencode><#codigo#></raencode>','<#codigo#>');

		$grid = new DataGrid('');
		$grid->order_by('codigo');
		$grid->per_page = 40;

		$grid->column_orderby('Codigo',"$uri",'codigo','align="left"');
		$grid->column_orderby('Nombre',"nombre",'nombre','align="left"');
		$grid->column_orderby('Monto',"<nformat><#monto#></nformat>",'monto','align="right"');

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

		$edit = new DataEdit($this->tits, 'negocio');

		$edit->back_url = site_url($this->url."filteredgrid");

		$edit->post_process('insert','_post_insert');
		$edit->post_process('update','_post_update');
		$edit->post_process('delete','_post_delete');

		$edit->codigo = new inputField('Codigo','codigo');
		$edit->codigo->size =7;
		$edit->codigo->maxlength =5;
		$edit->codigo->rule ='trim|required|unique';

		$edit->nombre = new textAreaField('Nombre','nombre');
		$edit->nombre->rows  =2;
		$edit->nombre->cols  =80;
		

		$edit->monto = new inputField('Monto','monto');
		$edit->monto->rule='max_length[8]';
		$edit->monto->size =10;
		$edit->monto->maxlength =8;

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
		$mSQL="CREATE TABLE `negocio` (
                    `codigo` char(5) NOT NULL DEFAULT '',
                    `nombre` char(20) DEFAULT NULL,
                    `monto` double DEFAULT NULL,
                    PRIMARY KEY (`codigo`)
                  ) ENGINE=MyISAM DEFAULT CHARSET=utf8";
		$this->db->simple_query($mSQL);
		
		$query="ALTER TABLE `negocio` 	CHANGE COLUMN `nombre` `nombre` TEXT NULL DEFAULT NULL";
		$this->db->simple_query($query);
		
		$query="ALTER TABLE `patente` ADD COLUMN `fcreacion` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP";
		$this->db->simple_query($query);
	}

}
?>
