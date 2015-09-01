<?php
class partida extends Controller {
	
	function partida(){
		parent::Controller();
		$this->load->library("rapyd");
	}
	
	function  index(){
		redirect("presupuesto/partida/filteredgrid");
	}
	
	function filteredgrid(){
		$this->datasis->modulo_id(106,1);
		
		$this->rapyd->load("datafilter","datagrid");//
		
		$filter = new DataFilter("Filtro por Partidas","partida");
		
		$filter->id_part = new inputField("Identificador", "id_part");
		$filter->id_part->size=5;
		
		$filter->part_pres = new inputField("C&oacute;digo", "part_pres");
		$filter->part_pres->size=5;
		
		$filter->nomb_part = new inputField("Nombre", "nomb_part");
		$filter->nomb_part->size=40;
		
		$filter->conf_part = new dropdawnField("Condici&oacute;n","conf_part");
		$filter->conf_part =option('S','Si');
		$filter->conf_part =option('N','Ni');  
		
		$filter->nro_cta -> style='width:300px;';
		
		$filter->buttons("reset","search");
		
		$filter->build();
		
		$uri = anchor('presupuesto/partida/dataedit/show/<#id_unid#>','<#id_unid#>');
		
		$grid = new DataGrid("Lista de Unidades Ejecutoras");
		
		$grid->order_by("cod_unid","asc");
		
		$grid->column("Identificador"       ,$uri,"align='left'");
		$grid->column("Unidades Ejecutoras" ,"cod_unid","align='left'");
		$grid->column("Nombre"              ,"nomb_unid","align='left'");
		
		$grid->add("presupuesto/partida/dataedit/create");
		
		$grid->build();
		
		$data['content'] = $filter->output.$grid->output;
		$data['title']   = " Partidas ";
		$data["head"]    = $this->rapyd->get_head();
		$this->load->view('view_ventanas', $data);
	}
	
	function dataedit(){
		$this->datasis->modulo_id(105,1);
		
		$this->rapyd->load("dataedit");
		
		$script='
			$(".inputnum").numeric(".");
		';
		
		$edit = new DataEdit("Partidas", "partida");
		$edit->script($script,"create");
		
		$edit->back_url = site_url("presupuesto/partida/filteredgrid");
			 
		$edit->cod_unid = new inputField("C&oacute;digo", "cod_unid");
		$edit->cod_unid->size=5;
		$edit->cod_unid->maxlength=2;
		$edit->cod_unid->mode="autohide";
		$edit->cod_unid->css_class='inputnum';
		 
		$edit->nomb_unid = new inputField("Nombre", "nomb_unid");
		$edit->nomb_unid->size=40;
		$edit->nomb_unid->maxlength=80;
		$edit->nomb_unid->rule='required';
		
		$edit->buttons("modify", "save", "undo", "delete", "back");
		 
		$edit->build();

		$data['content'] = $edit->output;
		$data['title']   = " Partidas ";
		$data["head"]    = script("jquery.pack.js").$this->rapyd->get_head();
		$this->load->view('view_ventanas', $data);
		
	}

}
?>