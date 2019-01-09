<?php

//almacenes
class Bi_conc extends Controller {

	var $url  ='bienes/bi_conc';
	var $titp ='Conceptos de Incorporaciones y Desincorporaciones';
	var $tits ='Concepto de Incorporaci&oacute;n &oacute; Desincorporaci&oacute;n';
	
	function Bi_conc(){
		parent::Controller();
		$this->load->library("rapyd");
		$this->datasis->modulo_id(271,1);
	}

	function index(){
		redirect($this->url."/filteredgrid");
	}

	function filteredgrid(){
		$this->rapyd->load("datafilter","datagrid");

		$filter = new DataFilter("Filtro de ".$this->titp,"bi_conc");

		$filter->codigo = new inputField("C&oacute;digo", "codigo");
		$filter->codigo->size=10;

		$filter->denomi = new inputField("Denominaci&oacute;n", "denomi");
		$filter->denomi->size=20;
		
		$filter->tipo = new dropdownField("Tipo", "tipo");
		$filter->tipo->option("","");
		$filter->tipo->option("I","Incorporaci&oacute;n");
		$filter->tipo->option("D","Desincorporaci&oacute;n");
		$filter->tipo->style="width:150px";

		$filter->buttons("reset","search");
		$filter->build();

		function tipo($tipo){
			switch($tipo){
				case "I":return "Incorporaci&oacute;n";break;
				case "D":return "Desincorporaci&oacute;n";break;
			}
		}

		$uri = anchor($this->url.'/dataedit/show/<raencode><#codigo#></raencode>','<#codigo#>');

		$grid = new DataGrid("Lista de ".$this->titp);
		$grid->order_by("codigo","asc");
		$grid->per_page = 20;
		$grid->use_function('tipo');

		$grid->column_orderby("C&oacute;digo"       ,$uri                      ,"codigo"  );
		$grid->column_orderby("Denominaci&oacute;n" ,"denomi"                  ,"denomi"  );
		$grid->column_orderby("Tipo"                ,"<tipo><#tipo#></tipo>"   ,"tipo"    );
		
		$grid->add($this->url."/dataedit/create");
		$grid->build();
		
		$data['filtro']  = $filter->output;
		$data['content'] = $grid->output;
		$data['script']  = script("jquery.js");
		$data['title']   = $this->titp;
		$data["head"]    = $this->rapyd->get_head();
		$this->load->view('view_ventanas', $data);
	}

	function dataedit(){
		$this->rapyd->load("dataobject","dataedit");

		$edit = new DataEdit($this->tits, "bi_conc");
		$edit->back_url = site_url($this->url."/filteredgrid");

		$edit->post_process('insert','_post_insert');
		$edit->post_process('update','_post_update');
		$edit->post_process('delete','_post_delete');

		$edit->codigo= new inputField("C&oacute;digo", "codigo");
		$edit->codigo->mode     ="autohide";
		$edit->codigo->rule     ='required';
		$edit->codigo->maxlength=2;
		$edit->codigo->size     =2;

		$edit->denomi=new inputField("Denominaci&oacute;n", "denomi");
		$edit->denomi->size     =80;
		$edit->denomi->maxlength=200;
		$edit->denomi->rule     ="required";
		
		$edit->tipo = new dropdownField("Tipo", "tipo");
		$edit->tipo->option("I","Incorporaci&oacute;n");
		$edit->tipo->option("D","Desincorporaci&oacute;n");
		$edit->tipo->style="width:150px";

		$edit->buttons("modify", "save", "undo", "delete", "back");
		$edit->build();

		$data['content'] = $edit->output;
		$data['title']   = $this->tits;
		$data["head"]    = script("jquery.js").$this->rapyd->get_head();
		$this->load->view('view_ventanas', $data);
	}
	
	function _valida($do){
		
	}

	function _post_insert($do){
		$codigo=$do->get('codigo');
		logusu('bi_conc',$this->tits." $codigo CREADO");
	}
	
	function _post_update($do){
		$codigo=$do->get('codigo');
		logusu('bi_conc',$this->tits." $codigo  MODIFICADO");
	}
	
	function _post_delete($do){
		$codigo=$do->get('codigo');
		logusu('bi_conc',$this->tits." $codigo  ELIMINADO ");
	}
}
?>

