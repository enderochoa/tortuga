<?php
class local extends Controller {
	var $titp='Localizaciones';
	var $tits='Localizaci&oacute;n';
	var $url ='ingresos/local/';
	
	function local(){
		parent::Controller();
		$this->load->library("rapyd");
		$this->datasis->modulo_id(347,1);
	}
	
	function index(){
		redirect($this->url."filteredgrid");
	}
	
	function filteredgrid(){
		$this->rapyd->load('datafilter','datagrid');

		$filter = new DataFilter($this->titp, 'local');

		$filter->codigo = new inputField('C&oacute;digo','codigo');
		$filter->codigo->rule      ='max_length[2]';
		$filter->codigo->size      =4;
		$filter->codigo->maxlength =2;

		$filter->nombre = new inputField('Nombre','nombre');
		$filter->nombre->rule      ='max_length[20]';
		$filter->nombre->size      =22;
		$filter->nombre->maxlength =20;

		$filter->buttons('reset', 'search');
		$filter->build();

		$uri = anchor($this->url.'dataedit/show/<raencode><#codigo#></raencode>','<#codigo#>');

		$grid = new DataGrid('');
		$grid->order_by('codigo');
		$grid->per_page = 40;

		$grid->column_orderby('C&oacute;digo',"$uri",'codigo');
		$grid->column_orderby('Nombre',"nombre",'nombre');

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

		$edit = new DataEdit($this->tits, 'local');

		$edit->back_url = site_url($this->url."filteredgrid");

		$edit->post_process('insert','_post_insert');
		$edit->post_process('update','_post_update');
		$edit->post_process('delete','_post_delete');

		$edit->codigo = new inputField('C&oacute;digo','codigo');
		$edit->codigo->rule='trim|max_length[2]|unique|required';
		$edit->codigo->size =4;
		$edit->codigo->maxlength =2;
		$edit->codigo->mode='autohide';
		$edit->codigo->when=array('create','show','modify');

		$edit->nombre = new inputField('Nombre','nombre');
		$edit->nombre->rule='max_length[20]';
		$edit->nombre->size =22;
		$edit->nombre->maxlength =20;

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
		$mSQL="CREATE TABLE `local` (
		`codigo` char(2) NOT NULL DEFAULT '',
		`nombre` char(20) DEFAULT NULL,
		PRIMARY KEY (`codigo`)
	  ) ENGINE=MyISAM DEFAULT CHARSET=utf8";
		$this->db->simple_query($mSQL);
		$query="alter table local add column `municipio` varchar(50) DEFAULT ''";
		$this->db->simple_query($mSQL);
	}
	

}
?>
