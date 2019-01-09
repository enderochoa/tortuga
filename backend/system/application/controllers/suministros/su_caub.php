<?php

//almacenes
class su_caub extends Controller {

	var $url  ='suministros/su_caub';
	var $titp ='Almacenes';
	var $tits ='Almac&eacute;n';
	var $tabla='su_caub';
	
	function su_caub(){
		parent::Controller();
		$this->load->library("rapyd");
		$this->datasis->modulo_id(277,1);
	}

	function index(){
		redirect($this->url."/filteredgrid");
	}

	function filteredgrid(){
		$this->rapyd->load("datafilter","datagrid");

		$filter = new DataFilter("Filtro de ".$this->titp,$this->tabla);

		$filter->codigo = new inputField("C&oacute;digo", "codigo");
		$filter->codigo->size=10;

		$filter->descrip = new inputField("Descripci&oacute;n", "descrip");
		$filter->descrip->size=20;

		$filter->buttons("reset","search");
		$filter->build();

		$uri = anchor($this->url.'/dataedit/show/<raencode><#codigo#></raencode>','<#codigo#>');

		$grid = new DataGrid("Lista de ".$this->titp);
		$grid->order_by("codigo","asc");
		$grid->per_page = 20;

		$grid->column_orderby("C&oacute;digo"       ,$uri       ,"codigo"    );
		$grid->column_orderby("Descripci&oacute;n"  ,"descrip"  ,"descrip"   );
		
		$grid->add($this->url."/dataedit/create");
		$grid->build();

		$data['content'] = $filter->output.$grid->output;
		$data['title']   = $this->titp;
		$data["head"]    = $this->rapyd->get_head();
		$this->load->view('view_ventanas', $data);
	}

	function dataedit(){
		$this->rapyd->load("dataobject","dataedit");

		$edit = new DataEdit($this->tits, $this->tabla);
		$edit->back_url = site_url($this->url."/filteredgrid");

		//$edit->pre_process('insert'  ,'_valida');
		//$edit->pre_process('update'  ,'_valida');
		$edit->post_process('insert','_post_insert');
		$edit->post_process('update','_post_update');
		$edit->post_process('delete','_post_delete');

		$edit->codigo= new inputField("C&oacute;digo", "codigo");
		$edit->codigo->mode     ="autohide";
		$edit->codigo->rule     ='required';
		$edit->codigo->maxlength=4;
		$edit->codigo->size     =4;

		$edit->descrip=new textareaField("Descripci&oacute;n", "descrip");
		$edit->descrip->rows=4;
		$edit->descrip->cols=50;
		$edit->descrip->rule="required";
		
		$edit->buttons("add","modify", "save", "undo", "delete", "back");
		$edit->build();

		$data['content'] = $edit->output;
		$data['title']   = $this->tits;
		$data["head"]    = script("jquery.js").$this->rapyd->get_head();
		$this->load->view('view_ventanas', $data);
	}

	function _post_insert($do){
		$codigo=$do->get('codigo');
		logusu($this->tabla,$this->tits." $codigo CREADO");
	}
	function _post_update($do){
		$codigo=$do->get('codigo');
		logusu($this->tabla,$this->tits." $codigo  MODIFICADO");
	}
	function _post_delete($do){
		$codigo=$do->get('codigo');
		logusu($this->tabla,$this->tits." $codigo  ELIMINADO ");
	}

	function instalar(){
		
	}
}
?>

