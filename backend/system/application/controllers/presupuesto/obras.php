<?php
require_once(BASEPATH.'application/controllers/presupuesto/common.php');
class Obras extends Common {
	
	function obras(){
		parent::Controller();
		$this->load->library("rapyd");
	} 
	
	function  index(){
		redirect("presupuesto/obras/filteredgrid");
	}
	
	function filteredgrid(){
		$this->datasis->modulo_id(104,1);
		
		$this->rapyd->load("datafilter2","datagrid");
		
		$link=site_url('presupuesto/programas/get_programa');
		$link2=site_url('presupuesto/proyectos/get_proyectos');
		
		$script='
			$(document).ready(function(){
				$("#sect_pres").change(function(){
					$.post("'.$link.'",{ sect_pres:$(this).val() },function(data){$("#prog_pres").html(data);})
					$.post("'.$link2.'",{ sect_pres:$(this).val() },function(data){$("#proy_pres").html(data);})
				});
			});
		';
		
		$filter = new DataFilter2("Filtro por Obras","obras");
		
		$filter->script($script,"create");
		$filter->script($script,"modify");
		
		$filter->sect_pres = new dropdownField("Sector", "sect_pres");
		$filter->sect_pres->option("","");
		$filter->sect_pres->options("SELECT sect_pres, CONCAT_WS(' ',sect_pres,nomb_sect) AS nomb_sect FROM sectores ORDER BY sect_pres"); 
		$filter->sect_pres -> style='width:300px;';
		
		$filter->prog_pres = new dropdownField("Programa", "prog_pres");
		$filter->prog_pres->option("","");
		$filter->prog_pres -> style='width:300px;';		
		$sect_pres=$filter->getval('sect_pres');
		if($sect_pres!==FALSE){
			$filter->prog_pres->options("SELECT prog_pres, CONCAT_WS(' ',prog_pres,nomb_prog) AS nomb_prog FROM programas WHERE sect_pres ='$sect_pres' ORDER BY prog_pres");
		}else{
			$filter->prog_pres->option("","Seleccione un Sector primero");
		}
		
		$filter->proy_pres = new dropdownField("Proyecto", "proy_pres");
		$filter->proy_pres->option("","");
		$filter->proy_pres -> style='width:300px;';
		$sect_pres=$filter->getval('sect_pres');
		if($sect_pres!==FALSE){
			$filter->prog_pres->options("SELECT proy_pres, CONCAT_WS(' ',proy_pres,nomb_proy) AS nomb_proy FROM proyectos WHERE sect_pres ='$sect_pres' ORDER BY proy_pres");
		}else{
			$filter->prog_pres->option("","Seleccione un Sector primero");
		}
		
		$filter->id_obra = new inputField("Identificador Actividad", "id_obra");
		$filter->id_obra->size=5;
		$filter->id_obra->maxlength=2;
		
		$filter->obr_pres = new inputField("C&oacute;digo Actividad", "obr_pres");
		$filter->obr_pres->size=5;
		$filter->obr_pres->maxlength=2;
		
		$filter->nomb_obra = new inputField("Nombre Actividad", "nomb_obra");
		$filter->nomb_obra->size=40;
		$filter->nomb_obra->maxlength=80;
				
		$filter->buttons("reset","search");
		
		$filter->build();
		
		$uri = anchor('presupuesto/obras/dataedit/show/<#id_obra#>','<#id_obra#>');
		
		$grid = new DataGrid("Lista de Obras");
		
		$grid->order_by("obr_pres","asc");
		
		$grid->column("Identificador"   ,$uri,"align='center'");
		$grid->column("Sector"          ,"sect_pres","align='left'");
		$grid->column("Programa"        ,"prog_pres","align='left'");
		$grid->column("Proyecto"        ,"proy_pres","align='left'");
		$grid->column("Actividad"       ,"obr_pres","align='left'");
		$grid->column("Nombre Actividad","nomb_obr","align='left'");
		
		$grid->add("presupuesto/obras/dataedit/create");
		
		$grid->build();
		
		$data['content'] = $filter->output.$grid->output;
		$data['title']   = " Obras ";
		$data["head"]    = script("jquery.pack.js").$this->rapyd->get_head();
		$this->load->view('view_ventanas', $data);
	}
	
	function dataedit(){
		$this->datasis->modulo_id(104,1);
		
		$this->rapyd->load("dataedit2");
		
		$link=site_url('presupuesto/programas/get_programa');
		
		$link2=site_url('presupuesto/proyectos/get_proyectos');
		$script='
			$(document).ready(function(){
				$("#sect_pres").change(function(){
					$.post("'.$link.'",{ sect_pres:$(this).val() },function(data){$("#prog_pres").html(data);})
					$.post("'.$link2.'",{ sect_pres:$(this).val() },function(data){$("#proy_pres").html(data);})
				});
			});
		';
		
		
		$edit = new DataEdit2("Obras", "obras");
		$edit->script($script,"create");
		$edit->script($script,"modify");
		
		$edit->back_url = site_url("presupuesto/obras/filteredgrid");
		
		$edit->sect_pres = new dropdownField("Sector", "sect_pres");
		$edit->sect_pres->option("","");
		$edit->sect_pres->options("SELECT sect_pres, CONCAT_WS(' ',sect_pres,nomb_sect) AS nomb_sect FROM sectores ORDER BY sect_pres"); 
		$edit->sect_pres -> style='width:300px;';
		$edit->sect_pres ->rule='required';
		
		$edit->prog_pres = new dropdownField("Programa", "prog_pres");
		$edit->prog_pres -> style='width:300px;';
		$edit->prog_pres ->rule='required';
		$sect_pres=$edit->getval('sect_pres');
		if($sect_pres!==FALSE){
			$edit->prog_pres->options("SELECT prog_pres, CONCAT_WS(' ',prog_pres,nomb_prog) AS nomb_prog FROM programas WHERE sect_pres ='$sect_pres' ORDER BY prog_pres");
		}else{
			$edit->prog_pres->option("","Seleccione un Sector primero");
		}
		
		$edit->proy_pres = new dropdownField("Proyecto", "proy_pres");
		$edit->proy_pres -> style='width:300px;';
		$edit->proy_pres ->rule='required';
		$sect_pres=$edit->getval('sect_pres');
		if($sect_pres!==FALSE){
			$edit->proy_pres->options("SELECT proy_pres, CONCAT_WS(' ',proy_pres,nomb_proy) AS nomb_prog FROM proyectos WHERE sect_pres ='$sect_pres' ORDER BY proy_pres");
		}else{
			$edit->proy_pres->option("","Seleccione un Sector primero");
		}
		
		$edit->obr_pres = new inputField("C&oacute;digo Actividad", "obr_pres");
		$edit->obr_pres->size=5;
		$edit->obr_pres->maxlength=2;
		$edit->obr_pres->mode="autohide";
		$edit->obr_pres->css_class='inputnum';
		$edit->obr_pres->rule='required';
		 
		$edit->nomb_obr = new inputField("Nombre Proyecto", "nomb_obr");
		$edit->nomb_obr->size=40;
		$edit->nomb_obr->maxlength=80;
				
		$edit->buttons("modify", "save", "undo", "delete", "back");
		 
		$edit->build();

		$data['content'] = $edit->output;
		$data['title']   = " Obras ";
		$data["head"]    = script("jquery.pack.js").$this->rapyd->get_head();
		$this->load->view('view_ventanas', $data);
		
	}
	
	function prueba(){
		
	}
}
?>