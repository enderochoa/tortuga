<?php
class R_sector extends Controller {
	var $titp='Sectores';
	var $tits='Sector';
	var $url ='recaudacion/r_sector/';
	function R_sector(){
		parent::Controller();
		$this->load->library("rapyd");
		$this->datasis->modulo_id(432,1);
	}
	function index(){
		redirect($this->url."filteredgrid");
	}
	function filteredgrid(){
		$this->rapyd->load('datafilter','datagrid');

		$filter = new DataFilter($this->titp, 'r_sector');

		$filter->id = new inputField('Id','id');
		$filter->id->rule      ='trim';
		$filter->id->size      =13;
		$filter->id->maxlength =11;

		$filter->descrip = new inputField('Descrip','descrip');
		$filter->descrip->rule      ='trim';
		$filter->descrip->size      =102;
		$filter->descrip->maxlength =100;

		$filter->buttons('reset', 'search');
		$filter->build();

		$uri = anchor($this->url.'dataedit/show/<raencode><#id#></raencode>','<#id#>');

		$grid = new DataGrid('');
		$grid->order_by('id');
		$grid->per_page = 40;

		$grid->column_orderby('Id'                ,"$uri"                                             ,'id'           ,'align="left"');
		$grid->column_orderby('Descrip'           ,"descrip"                                          ,'descrip'      ,'align="left"');

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

		$edit = new DataEdit($this->tits, 'r_sector');

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

		$edit->descrip = new inputField('Descrip','descrip');
		$edit->descrip->rule     ='trim';
		$edit->descrip->size      =40;
		$edit->descrip->maxlength =100;
		
		$edit->monto = new inputField('Monto','monto');
		$edit->monto->rule='max_length[19]|numeric';
		$edit->monto->css_class='inputnum';
		$edit->monto->size =10;
		$edit->monto->maxlength =19;

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
		$mSQL="CREATE TABLE `r_sector` (
		  `id` int(11) NOT NULL AUTO_INCREMENT,
		  `descrip` varchar(100) NOT NULL,
		  PRIMARY KEY (`id`)
		) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8";
		$this->db->simple_query($mSQL);
		
		$query="ALTER TABLE `r_sector` ADD COLUMN `monto` DECIMAL(19,2) NOT NULL DEFAULT '0'";
		$this->db->simple_query($mSQL);
	}

}
?>
