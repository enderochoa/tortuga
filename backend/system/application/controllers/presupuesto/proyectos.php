<?php
class proyectos extends Controller {
	
	function proyectos(){
		parent::Controller();
		$this->load->library("rapyd");
	}
	
	function  index(){
		redirect("presupuesto/proyectos/filteredgrid");
	}
	
	function filteredgrid(){
		$this->datasis->modulo_id(103,1);
		
		$this->rapyd->load("datafilter2","datagrid");
		
		$link=site_url('presupuesto/programas/get_programa');
		
		$script='
			$(document).ready(function(){
				$("#sect_pres").change(function(){
				
					$.post("'.$link.'",{ sect_pres:$(this).val() },function(data){$("#prog_pres").html(data);})
				});
			});
		';
		
		$filter = new DataFilter2("Filtro por Proyectos","proyectos");
		
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
		
		$filter->id_proy = new inputField("Identificador Proyecto", "id_proy");
		$filter->id_proy->size=5;
		$filter->id_proy->maxlength=2;
		
		$filter->proy_pres = new inputField("C&oacute;digo Proyecto", "proy_pres");
		$filter->proy_pres->size=5;
		$filter->proy_pres->maxlength=2;
		
		$filter->nomb_proy = new inputField("Nombre Proyecto", "nomb_proy");
		$filter->nomb_proy->size=40;
		$filter->nomb_proy->maxlength=80;
		
		$filter->buttons("reset","search");
		
		$filter->build();
		
		$uri = anchor('presupuesto/proyectos/dataedit/show/<#id_proy#>','<#id_proy#>');
		
		$grid = new DataGrid("Lista de Proyectos");    
		
		$grid->order_by("proy_pres","asc");
		
		$grid->column("Identificador"   ,$uri,"align='center'");
		$grid->column("Sector"          ,"sect_pres","align='left'");
		$grid->column("Programa"        ,"prog_pres","align='left'");
		$grid->column("Proyecto"        ,"proy_pres","align='left'");
		$grid->column("Nombre"          ,"nomb_proy","align='left'");
		
		$grid->add("presupuesto/proyectos/dataedit/create");
		
		$grid->build();
		
		$data['content'] = $filter->output.$grid->output;
		$data['title']   = " Proyectos ";
		$data["head"]    = script("jquery.pack.js").$this->rapyd->get_head();
		$this->load->view('view_ventanas', $data);
	}
	
	function dataedit(){
		$this->datasis->modulo_id(103,1);
		
		$this->rapyd->load("dataedit2");
		
		$link=site_url('presupuesto/programas/get_programa');
		
		$script='
			$(document).ready(function(){
				$("#sect_pres").change(function(){
					$.post("'.$link.'",{ sect_pres:$(this).val() },function(data){$("#prog_pres").html(data);})
				});
			});
		';
		
		$edit = new DataEdit2("Proyectos", "proyectos");
		$edit->script($script,"create");
		$edit->script($script,"modify");
		
		$edit->back_url = site_url("presupuesto/proyectos/filteredgrid");
		
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
		
		$edit->proy_pres = new inputField("C&oacute;digo Proyecto", "proy_pres");
		$edit->proy_pres->size=5;
		$edit->proy_pres->maxlength=2;
		$edit->proy_pres->mode="autohide";
		$edit->proy_pres->css_class='inputnum';
		$edit->proy_pres->rule='required';
		 
		$edit->nomb_proy = new inputField("Nombre Proyecto", "nomb_proy");
		$edit->nomb_proy->size=40;
		$edit->nomb_proy->maxlength=80;
				
		$edit->buttons("modify", "save", "undo", "delete", "back");
		 
		$edit->build();

		$data['content'] = $edit->output;
		$data['title']   = " Proyectos ";
		$data["head"]    = script("jquery.pack.js").$this->rapyd->get_head();
		$this->load->view('view_ventanas', $data);
		
	}
	
	function get_proyectos(){
    $sect_pres=$this->input->post('sect_pres');
   	if(!empty($sect_pres)){
   		$mSQL=$this->db->query("SELECT proy_pres, CONCAT_WS(' ',proy_pres,nomb_proy) AS nomb_proy FROM proyectos WHERE sect_pres ='$sect_pres' ORDER BY proy_pres");
   		if($mSQL){
   			echo "<option value=''>Seleccione un Programa</option>";
   			foreach($mSQL->result() AS $fila ){
   				echo "<option value='".$fila->proy_pres."'>".$fila->nomb_proy."</option>";
   			}
   		}
   	}else{
   		echo "<option value=''>Seleccione un Sector primero</option>";
   	}
	}

}
?>