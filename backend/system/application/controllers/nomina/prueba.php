<?php
class prueba extends Controller {
	
		function prueba(){
		parent::Controller(); 
		$this->load->library("rapyd");
	}
	     function index(){
    	redirect("nomina/prueba/filteredgrid");
    }

	function filteredgrid(){
		$this->rapyd->load("datafilter","datagrid");
		$this->rapyd->uri->keep_persistence();

		$filter = new DataFilter("Filtro por Grupo o Codigo o Descripcion", 'sinv');
		$filter->grupo = new inputField("Grupo", "grupo");
		$filter->codigo = new inputField("Codigo", "codigo");
		$filter->descrip = new inputField("Descripcion", "descrip");
		$filter->buttons("reset","search");
		$filter->build();

		$uri = anchor('nomina/prueba/dataedit/show/<#codigo#>','<#codigo#>');

		$grid = new DataGrid("Lista de Inventario");
		$grid->order_by("codigo","asc");
		$grid->per_page = 20;

		$grid->column("Grupo",$uri);
		$grid->column("Codigo","codigo");
		$grid->column("Descripcion","descrip");
		$grid->add("nomina/prueba/dataedit/create");
		$grid->build();
		
		$data['content'] = $filter->output.$grid->output;
		$data['title']   = " Inventario ";
		$data["head"]    = $this->rapyd->get_head();
		$this->load->view('view_ventanas', $data);	

	}

	function dataedit() 	{
		$this->rapyd->load("dataedit");
		
		$edit = new DataEdit("Inventario", "sinv");
		$edit->back_url = site_url("nomina/pers/filteredgrid");
		
		$edit->codigo =  new inputField("Codigo", "codigo");
		$edit->grupo =  new inputField("Grupo", "grupo");
		$edit->descrip =    new inputField("Descripcion", "descrip");
		$edit->descrip2 =  new inputField("Descripcion2", "descrip2");
		$edit->unidad = new inputField("Unidad", "unidad");
		$edit->ubica =   new inputField("Ubicaci&oacute;n", "ubica");
		$edit->tipo =   new inputField(".", "tipo");
		$edit->pvp_bs =   new inputField("Pvp_bs", "pvp_bs");
		$edit->pvpprc = new inputField("Pvpprc", "pvpprc");
		$edit->contbs = new DateField("Contbs", "contbs");
		$edit->contprc =  new inputField("Contprc", "contprc");
		$edit->mayobs = new DateField("Mayoobs", "mayobs");
		$edit->mayoprc =  new inputField("Mayoprc", "mayoprc");    
		
		$edit->buttons("modify", "save", "undo", "delete", "back");
		$edit->build();

		$data['content'] = $edit->output;           
    $data['title']   = " Inventario ";        
    $data["head"]    = $this->rapyd->get_head();
    $this->load->view('view_ventanas', $data);  
	}
}
?>