<?php
class programas extends Controller {
	
	function programas(){
		parent::Controller();
		$this->load->library("rapyd");
	}
	
	function  index(){
		redirect("presupuesto/programas/filteredgrid");
	}
	
	function filteredgrid(){
		$this->datasis->modulo_id(102,1);
		
		$this->rapyd->load("datafilter","datagrid");
		
		$filter = new DataFilter("Filtro por Programas","programas");
		
		$filter->sect_pres = new dropdownField("Sector", "sect_pres");
		$filter->sect_pres->option("","");
		$filter->sect_pres->options("SELECT sect_pres, CONCAT_WS(' ',sect_pres,nomb_sect) AS nomb_sect FROM sectores ORDER BY sect_pres"); 
		$filter->sect_pres -> style='width:300px;';
		
		$filter->id_prog = new inputField("Identificador", "id_prog");
		$filter->id_prog->size=5;
		
		$filter->prog_pres = new inputField("C&oacute;digo Programa", "prog_pres");
		$filter->prog_pres->size=5;
		$filter->prog_pres->maxlength=2;
		
		$filter->nomb_prog = new inputField("Nombre Programa", "nomb_prog");
		$filter->nomb_prog->size=40;
		$filter->nomb_prog->maxlength=80;
		
		$filter->buttons("reset","search");
		
		$filter->build();
		
		$uri = anchor('presupuesto/programas/dataedit/show/<#id_prog#>','<#id_prog#>');
		
		$grid = new DataGrid("Lista de Programas");   
		
		$grid->order_by("prog_pres","asc");
		
		$grid->column("Identificador" ,$uri,"align='left'");
		$grid->column("Sector"        ,"sect_pres","align='left'");
		$grid->column("Programa "     ,"prog_pres"       ,"align='left'");
		$grid->column("Nombre"        ,"nomb_prog","align='left'");
		
		$grid->add("presupuesto/programas/dataedit/create");
		
		$grid->build();
		
		$data['content'] = $filter->output.$grid->output;
		$data['title']   = " Programas ";
		$data["head"]    = $this->rapyd->get_head();
		$this->load->view('view_ventanas', $data);
	}
	
	function dataedit(){
		$this->datasis->modulo_id(102,1);
		
		$this->rapyd->load("dataedit");
		
		$script='
			$(".inputnum").numeric(".");
		';
		
		$edit = new DataEdit("Programas", "programas");
		$edit->script($script,"create");
		
		$edit->back_url = site_url("presupuesto/programas/filteredgrid");
			 
		$edit->sect_pres = new dropdownField("Sector", "sect_pres");
		$edit->sect_pres->options("SELECT sect_pres, CONCAT_WS(' ',sect_pres,nomb_sect)AS nomb_sect FROM sectores ORDER BY sect_pres"); 
		$edit->sect_pres -> style='width:300px;';
		
		$filter->id_prog = new inputField("Identificador", "id_prog");
		$filter->id_prog->size=5;
		
		$edit->prog_pres = new inputField("C&oacute;digo Programa", "prog_pres");
		$edit->prog_pres->size=5;
		$edit->prog_pres->maxlength=2;
		$edit->prog_pres->mode="autohide";
		$edit->prog_pres->css_class='inputnum';
		$edit->prog_pres->rule='required';
		 
		$edit->nomb_prog = new inputField("Nombre Programa", "nomb_prog");
		$edit->nomb_prog->size=40;
		$edit->nomb_prog->maxlength=80;
		$edit->nomb_prog->rule='required';
		
		$edit->buttons("modify", "save", "undo", "delete", "back");
		 
		$edit->build();

		$data['content'] = $edit->output;
		$data['title']   = " Programas ";
		$data["head"]    = script("jquery.pack.js").$this->rapyd->get_head();
		$this->load->view('view_ventanas', $data);
		
	}
	
	function get_programa(){
    $sect_pres=$this->input->post('sect_pres');
   	if(!empty($sect_pres)){
   		$mSQL=$this->db->query("SELECT prog_pres, CONCAT_WS(' ',prog_pres,nomb_prog) AS nomb_prog FROM programas WHERE sect_pres ='$sect_pres' ORDER BY prog_pres");
   		if($mSQL){
   			echo "<option value=''>Seleccione un Programa</option>";
   			foreach($mSQL->result() AS $fila ){
   				echo "<option value='".$fila->prog_pres."'>".$fila->nomb_prog."</option>";
   			}
   		}
   	}else{
   		echo "<option value=''>Seleccione un Sector primero</option>";
   	}
	}

}
?>