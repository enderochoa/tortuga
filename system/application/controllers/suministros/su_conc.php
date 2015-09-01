<?php
class su_conc extends Controller {
	var $titp='Concepto de Entradas/Salidas de Suministros';
	var $tits='Concepto';
	var $url ='suministros/su_conc/';
	function su_conc(){
		parent::Controller();
		$this->load->library("rapyd");
		$this->datasis->modulo_id(369,1);
	}
	function index(){
		redirect($this->url."filteredgrid");
	}
	function filteredgrid(){
		$this->rapyd->load('datafilter','datagrid');

		$filter = new DataFilter($this->titp, 'su_conc');

		$filter->id = new inputField('id','id');
		$filter->id->rule      ='max_length[10]';
		$filter->id->size      =12;
		$filter->id->maxlength =10;

		$filter->descrip = new textareaField('descrip','descrip');
		$filter->descrip->rule      ='max_length[8]';
		$filter->descrip->cols = 70;
		$filter->descrip->rows = 4;

		$filter->tipo = new inputField('tipo','tipo');
		$filter->tipo->rule      ='max_length[1]';
		$filter->tipo->size      =3;
		$filter->tipo->maxlength =1;

		$filter->buttons('reset', 'search');
		$filter->build();

		$uri = anchor($this->url.'dataedit/show/<raencode><#id#></raencode>','<#id#>');

		$grid = new DataGrid('');
		$grid->order_by('id');
		$grid->per_page = 40;

		$grid->column_orderby('Ref.'                ,"$uri",'id','align="left"');
		$grid->column_orderby('Descripci&oacute;n'  ,"descrip",'descrip','align="left"');
		$grid->column_orderby('Tipo'                ,"tipo",'tipo','align="left"');

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

		$edit = new DataEdit($this->tits, 'su_conc');

		$edit->back_url = site_url($this->url."filteredgrid");

		$edit->post_process('insert','_post_insert');
		$edit->post_process('update','_post_update');
		$edit->post_process('delete','_post_delete');

		$edit->id = new inputField('id','id');
		$edit->id->rule='max_length[10]';
		$edit->id->size =12;
		$edit->id->maxlength =10;
		$edit->id->mode ="autohide";
		$edit->id->when =array('show','modify');

		$edit->descrip = new textareaField('Descripcion','descrip');
		$edit->descrip->cols = 70;
		$edit->descrip->rows = 4;

		$edit->tipo = new dropdownField('Tipo','tipo');
		$edit->tipo->option("E","Entrada");
		$edit->tipo->option("S","Salida");

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
		$mSQL="CREATE TABLE `su_conc` (
			`id` int(10) NOT NULL AUTO_INCREMENT,
			`descrip` text,
			`tipo` char(1) DEFAULT NULL,
			PRIMARY KEY (`id`)
			) ENGINE=MyISAM DEFAULT CHARSET=utf8";
		$this->db->simple_query($mSQL);
	}

}
?>
