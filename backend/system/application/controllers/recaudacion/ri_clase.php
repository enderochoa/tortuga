<?php
class Ri_clase extends Controller {
	var $titp='Clases de Inmueble';
	var $tits='Clase de Inmueble';
	var $url ='recaudacion/ri_clase/';
	function Ri_clase(){
		parent::Controller();
		$this->load->library("rapyd");
		$this->datasis->modulo_id(433,1);
	}
	function index(){
		redirect($this->url."filteredgrid");
	}
	function filteredgrid(){
		$this->rapyd->load('datafilter','datagrid');

		$filter = new DataFilter($this->titp, 'ri_clase');

		$filter->id = new inputField('Id','id');
		$filter->id->rule      ='trim';
		$filter->id->size      =13;
		$filter->id->maxlength =11;

		$filter->nombre = new inputField('Nombre','nombre');
		$filter->nombre->rule      ='trim';
		$filter->nombre->size      =40;
		$filter->nombre->maxlength =255;

		$filter->monto = new inputField('Monto','monto');
		$filter->monto->rule      ='trim|numeric';
		$filter->monto->css_class ='inputnum';
		$filter->monto->size      =21;
		$filter->monto->maxlength =19;

		$filter->buttons('reset', 'search');
		$filter->build();

		$uri = anchor($this->url.'dataedit/show/<raencode><#id#></raencode>','<#id#>');

		$grid = new DataGrid('');
		$grid->order_by('id');
		$grid->per_page = 40;

		$grid->column_orderby('Id'                ,"$uri"                                             ,'id'           ,'align="left"');
		$grid->column_orderby('Nombre'            ,"nombre"                                           ,'nombre'       ,'align="left"');
		$grid->column_orderby('Monto'             ,"<nformat><#monto#></nformat>"                     ,'monto'        ,'align="right"');

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

		$edit = new DataEdit($this->tits, 'ri_clase');

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

		$edit->nombre = new inputField('Nombre','nombre');
		$edit->nombre->rule     ='trim';
		$edit->nombre->size      =40;
		$edit->nombre->maxlength =255;

		$edit->monto = new inputField('Monto','monto');
		$edit->monto->rule     ='trim|numeric';
		$edit->monto->css_class='inputnum';
		$edit->monto->size      =21;
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
		$mSQL="CREATE TABLE `ri_clase` (
		  `id` int(11) NOT NULL AUTO_INCREMENT,
		  `nombre` varchar(255) DEFAULT NULL,
		  `monto` decimal(19,2) DEFAULT '0.00',
		  PRIMARY KEY (`id`)
		) ENGINE=InnoDB DEFAULT CHARSET=utf8";
		$this->db->simple_query($mSQL);
		$query="ALTER TABLE `ri_clase` 	ADD COLUMN `monto2` DECIMAL(19,2) NULL DEFAULT '0.00'";
		$this->db->simple_query($query);
	}
	
	

}
?>
