<?php
class sectores extends Controller {
	
	function sectores(){
		parent::Controller();
		$this->load->library("rapyd");
	}
	
	function  index(){
		redirect("presupuesto/sectores/filteredgrid");
	}
	
	function filteredgrid(){
		$this->datasis->modulo_id(101,1);
		
		$this->rapyd->load("datafilter","datagrid");//
		
		$filter = new DataFilter("Filtro por Sectores","sectores");
		
		$filter->id_sect = new inputField("Identificador", "id_sect");
		$filter->id_sect->size=5;
		
		$filter->sect_pres = new inputField("C&oacute;digo Sector", "sect_pres");
		$filter->sect_pres->size=5;
		
		$filter->nomb_sect = new inputField("Nombre", "nomb_sect");
		$filter->nomb_sect->size=40;
		
		$filter->buttons("reset","search");
		
		$filter->build();
		
		$uri = anchor('presupuesto/sectores/dataedit/show/<#id_sect#>','<#id_sect#>');
		
		$grid = new DataGrid("Lista de Sectores");
		
		$grid->order_by("sect_pres","asc");
		
		$grid->column("Identificador" ,$uri,"align='left'");
		$grid->column("Sector" ,"sect_pres","align='left'");
		$grid->column("Nombre"        ,"nomb_sect","align='left'");
		
		$grid->add("presupuesto/sectores/dataedit/create");
		
		$grid->build();
		
		$data['content'] = $filter->output.$grid->output;
		$data['title']   = " Sectores ";
		$data["head"]    = $this->rapyd->get_head();
		$this->load->view('view_ventanas', $data);
	}
	
	function dataedit(){
		$this->datasis->modulo_id(101,1);
		
		$this->rapyd->load("dataedit");
		
		$script='
			$(".inputnum").numeric(".");
		';
		
		$edit = new DataEdit("Sector", "sectores");
		$edit->script($script,"create");
		
		$edit->back_url = site_url("presupuesto/sectores/filteredgrid");
			 
		$edit->sect_pres = new inputField("C&oacute;digo", "sect_pres");
		$edit->sect_pres->size=5;
		$edit->sect_pres->maxlength=2;
		$edit->sect_pres->mode="autohide";
		$edit->sect_pres->css_class='inputnum';
		 
		$edit->nomb_sect = new inputField("Nombre", "nomb_sect");
		$edit->nomb_sect->size=40;
		$edit->nomb_sect->maxlength=80;
		$edit->nomb_sect->rule='required';
		
		$edit->buttons("modify", "save", "undo", "delete", "back");
		 
		$edit->build();

		$data['content'] = $edit->output;
		$data['title']   = " Sectores ";
		$data["head"]    = script("jquery.pack.js").$this->rapyd->get_head();
		$this->load->view('view_ventanas', $data);
		
	}

}
?>