<?php
class r_negocio extends Controller {
	var $titp='Tipos de Negocio';
	var $tits='Tipo de Negocio';
	var $url ='recaudacion/r_negocio/';
	function r_negocio(){
		parent::Controller();
		$this->load->library("rapyd");
		$this->datasis->modulo_id(402,1);
	}
	function index(){
		redirect($this->url."filteredgrid");
	}
	function filteredgrid(){
		$this->rapyd->load('datafilter','datagrid');

		$filter = new DataFilter($this->titp, 'r_negocio');

		$filter->descrip = new inputField('Descripcion','descrip');
		$filter->descrip->rule      ='max_length[100]';
		$filter->descrip->size      =40;

		$filter->buttons('reset', 'search');
		$filter->build();

		$uri = anchor($this->url.'dataedit/show/<raencode><#id#></raencode>','<#id#>');

		$grid = new DataGrid('');
		$grid->order_by('id');
		$grid->per_page = 40;

		$grid->column_orderby('Ref'        ,"$uri",'id','align="left"');
		$grid->column_orderby('Descripcion',"descrip",'descrip','align="left"');
		$grid->column_orderby('Monto'      ,"<nformat><#monto#></nformat>" ,'monto','align="right"');
		$grid->column_orderby('Monto2'     ,"<nformat><#monto2#></nformat>",'monto2','align="right"');

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

		$edit = new DataEdit($this->tits, 'r_negocio');

		$edit->back_url = site_url($this->url."filteredgrid");

		$edit->post_process('insert','_post_insert');
		$edit->post_process('update','_post_update');
		$edit->post_process('delete','_post_delete');

		$edit->id = new inputField('Ref','id');
		$edit->id->rule='max_length[11]';
		$edit->id->size =13;
		$edit->id->maxlength =11;
		$edit->id->mode='autohide';
		$edit->id->when=array('show','modify');

		$edit->descrip = new inputField('Descripcion','descrip');
		$edit->descrip->rule='max_length[100]';
		$edit->descrip->size =40;
		$edit->descrip->maxlength =100;

		$edit->monto = new inputField('Monto','monto');
		$edit->monto->rule='max_length[19]|numeric';
		$edit->monto->css_class='inputnum';
		$edit->monto->size =21;
		$edit->monto->maxlength =19;
		
		$edit->monto2 = new inputField('Monto2','monto2');
		$edit->monto2->rule='max_length[19]|numeric';
		$edit->monto2->css_class='inputnum';
		$edit->monto2->size =21;
		$edit->monto2->maxlength =19;

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
		$mSQL="CREATE TABLE `r_negocio` (
		  `id` int(11) NOT NULL AUTO_INCREMENT,
		  `descrip` varchar(100) CHARACTER SET utf8 DEFAULT NULL,
		  `monto` decimal(19,2) DEFAULT '0.00',
		  PRIMARY KEY (`id`)
		) ENGINE=InnoDB AUTO_INCREMENT=47 DEFAULT CHARSET=utf32";
		$this->db->simple_query($mSQL);
		
		$query="ALTER TABLE `r_negocio` 	ADD COLUMN `codigo` VARCHAR(10) NULL DEFAULT NULL AFTER `id`";
		$this->db->simple_query($query);
		$query="ALTER TABLE `r_negocio` CHANGE COLUMN `descrip` `descrip` TEXT NULL DEFAULT NULL AFTER `id`";
		$this->db->simple_query($query);
		$query="ALTER TABLE `r_negocio` ADD COLUMN `monto2` DECIMAL(19,2) NULL DEFAULT '0.00' AFTER `monto`";
		$this->db->simple_query($query);
	}

}
?>
