<?php
class casise extends Controller {
	var $titp='Cerrar Meses';
	var $tits='Cerrar Mes';
	var $url ='contabilidad/casise/';
	function casise(){
		parent::Controller();
		$this->load->library("rapyd");
		$this->datasis->modulo_id(362,1);
	}
	function index(){
		redirect($this->url."filteredgrid");
	}
	function filteredgrid(){
		$this->rapyd->load('datafilter','datagrid');
                
		$filter = new DataFilter($this->titp);
                $filter->db->from("casise a");

		$filter->ano = new inputField('A&ntilde;o','ano');
		$filter->ano->size      =4;
		$filter->ano->maxlength =2;

		$filter->mes = new inputField('mes','mes');
		$filter->mes->rule      ='max_length[2]';
		$filter->mes->size      =4;
		$filter->mes->maxlength =2;

		$filter->buttons('reset', 'search');
		$filter->build();

		$uri = anchor($this->url.'dataedit/show/<raencode><#id#></raencode>','<#id#>');

		$grid = new DataGrid('');
		$grid->order_by('id');
		$grid->per_page = 40;

		$grid->column_orderby('Referencia'   ,$uri       ,'id'        ,'align="left"');
		$grid->column_orderby("A&ntilde;o"   ,"ano"      ,"ano"       ,"align='left'");
		$grid->column_orderby('Mes'          ,"mes"      ,'mes'       ,'align="left"');

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
                
		$edit = new DataEdit($this->tits, 'casise');

		$edit->back_url = site_url($this->url."filteredgrid");

		$edit->post_process('insert','_post_insert');
		$edit->post_process('update','_post_update');
		$edit->post_process('delete','_post_delete');

		$edit->id = new inputField('Referencia','id');
		$edit->id->mode ='autohide';
                $edit->id->when =array('show');
		
		$edit->ano = new inputField('A&ntilde;o','ano');
		$edit->ano->value=$this->datasis->traevalor("EJERCICIO");
		$edit->ano->size=4;

		$edit->mes = new dropdownField("Mes", 'mes');
		for($i=1; $i<=12; ++$i)
		$edit->mes->option(str_pad($i,2 ,"0" ,STR_PAD_LEFT),str_pad($i,2 ,"0" ,STR_PAD_LEFT));
		$edit->mes->mode     ="autohide";
		$edit->mes-> rule    = "required";

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

	function instala(){
		$mSQL="CREATE TABLE `casise` (
		`id` INT(10) NULL AUTO_INCREMENT,
		`ano` CHAR(4) NULL,
		`mes` CHAR(2) NULL,
		PRIMARY KEY (`id`)
		) COLLATE='utf8_general_ci' ENGINE=MyISAM ROW_FORMAT=DEFAULT";
		$this->db->simple_query($mSQL);
	}

}
?>
