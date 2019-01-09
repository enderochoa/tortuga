<?php
class R_contador extends Controller {
	var $titp='Contadores';
	var $tits='Contador';
	var $url ='recaudacion/r_contador/';
	function R_contador(){
		parent::Controller();
		$this->load->library("rapyd");
		//$this->datasis->modulo_id(216,1);
	}
	function index(){
		redirect($this->url."filteredgrid");
	}
	function filteredgrid(){
		$this->rapyd->load('datafilter','datagrid');

		$filter = new DataFilter($this->titp, 'r_contador');

		$filter->id = new inputField('Id','id');
		$filter->id->rule      ='trim';
		$filter->id->size      =13;
		$filter->id->maxlength =11;

		$filter->nombre = new inputField('Nombre','nombre');
		$filter->nombre->rule      ='trim';
		$filter->nombre->size      =13;
		$filter->nombre->maxlength =11;

		$filter->proxnumero = new inputField('Proxnumero','proxnumero');
		$filter->proxnumero->rule      ='trim';
		$filter->proxnumero->size      =13;
		$filter->proxnumero->maxlength =11;

		$filter->buttons('reset', 'search');
		$filter->build();

		$uri = anchor($this->url.'dataedit/show/<raencode><#id#></raencode>','<#id#>');

		$grid = new DataGrid('');
		$grid->order_by('id');
		$grid->per_page = 40;

		$grid->column_orderby('Id'                ,"$uri"                                             ,'id'           ,'align="left"');
		$grid->column_orderby('Nombre'            ,"nombre"                                           ,'nombre'       ,'align="right"');
		$grid->column_orderby('Proxnumero'        ,"proxnumero"                                       ,'proxnumero'   ,'align="right"');
		$grid->column_orderby('Serie'             ,"serie"                                            ,'derie'        ,'align="left"' );

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

		$edit = new DataEdit($this->tits, 'r_contador');

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
		$edit->nombre->size      =13;
		$edit->nombre->maxlength =11;

		$edit->proxnumero = new inputField('Proxnumero','proxnumero');
		$edit->proxnumero->rule     ='trim';
		$edit->proxnumero->size      =13;
		$edit->proxnumero->maxlength =11;
		
		$edit->serie = new inputField('Serie','serie');
		$edit->serie->rule     ='trim';
		$edit->serie->size      =13;
		$edit->serie->maxlength =11;

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
		$mSQL="CREATE TABLE `r_contador` (
			`id` INT(11) NOT NULL AUTO_INCREMENT,
			`nombre` VARCHAR(50) NULL DEFAULT NULL,
			`proxnumero` INT(11) NULL DEFAULT NULL,
			PRIMARY KEY (`id`)
		)
		COLLATE='utf8_general_ci'
		ENGINE=MyISAM
		AUTO_INCREMENT=1
		";
		$this->db->simple_query($mSQL);
		
		$query="ALTER TABLE `r_contador` ADD COLUMN `serie` VARCHAR(5) NULL DEFAULT NULL";
		$this->db->simple_query($mSQL);
	}

}
?>
