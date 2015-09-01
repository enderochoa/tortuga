<?php
class tiketservi extends Controller {
	function tiketservi(){
		parent::Controller(); 
		$this->load->library("rapyd");

	}
	function index(){
		redirect("supervisor/tiketservi/filteredgrid");
	}
	function filteredgrid(){

		$this->rapyd->load("datafilter","datagrid");
		$this->rapyd->uri->keep_persistence();
		
		$filter = new DataFilter("Filtro de Servio Tecnico", 'tiketservi');
				
		$filter->codigo = new inputField("Codigo","codigo");
		$filter->codigo->size=15;
		
		$filter->nombre = new inputField("Nombre","nombre");
		$filter->nombre->size=35;

		$filter->buttons("reset","search");
		$filter->build();
		
		$uri = anchor('supervisor/tiketservi/dataedit/show/<#codigo#>','<#codigo#>');
		
		$grid = new DataGrid("Lista de Servicio Tecnico");
		$grid->order_by("codigo","asc");

		$grid->column("Codigo",$uri );
		$grid->column("Nombre", "nombre");		

		$grid->add("supervisor/tiketservi/dataedit/create");
		$grid->build();
		
		$data['content'] = $filter->output.$grid->output;
		$data['title']   = " Servicio Tecnico ";
		$data["head"]    = $this->rapyd->get_head();
		$this->load->view('view_ventanas', $data);	
}

	function dataedit(){ 
		$this->rapyd->load("dataedit");
		
		$edit = new DataEdit("Servicio Tecnico","tiketservi");
		$edit->back_url = site_url("supervisor/tiketservi/filteredgrid");
				
		$edit->codigo = new inputField("Codigo","codigo");
		$edit->codigo->size=15;

		$edit->nombre = new inputField("Nombre","nombre");
		$edit->nombre->size=45;
		                    
		$edit->buttons("modify", "save", "undo", "back");		
		$edit->build();
			
    $data['content'] = $edit->output;           
    $data['title']   = " Servicio Tecnico ";        
    $data["head"]    = $this->rapyd->get_head();
    $this->load->view('view_ventanas', $data);  
	}
}
?>