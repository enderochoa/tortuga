<?php
class concepto extends Controller {
	function concepto(){
		parent::Controller(); 
		$this->load->library("rapyd");
		//I use THISFILE, instead __FILE__ to prevent some documented php-bugs with higlight_syntax()&__FILE__
		define ("THISFILE",   APPPATH."controllers/inventario/". $this->uri->segment(2).EXT);
	}
 
	function index(){
		redirect("inventario/concepto/filteredgrid");
	}

	function filteredgrid(){
	
		$this->rapyd->load("datafilter","datagrid");
		$this->rapyd->uri->keep_persistence();
		
		$filter = new DataFilter("Filtro de Conceptos", 'icon');
		
		$filter->codigo = new inputField("Código", "codigo");	
		$filter->codigo->size=20;		
		
		$filter->cedula = new inputField("Concepto", "concepto");
		$filter->cedula->size=20;

		$filter->buttons("reset","search");
		$filter->build();
		
		$uri = anchor('inventario/concepto/dataedit/show/<#codigo#>','<#codigo#>');
	
		$grid = new DataGrid("Lista de Conceptos");
		$grid->order_by("codigo","asc");
		$grid->per_page = 20;

		$grid->column("Código",$uri);
		$grid->column("Concepto ","concepto");
		$grid->column("Gasto" ,"gasto");
		$grid->column("Gastos de:","gastode");
		$grid->column("Ingreso", "ingreso");
		$grid->column("Ingresos de:", "ingresod");
		
		$grid->add("hospitalidad/concepto/dataedit/create");
		$grid->build();
		
    $data['content'] = $filter->output.$grid->output;
		$data['title']   = " Otros Conceptos ";
		$data["head"]    = $this->rapyd->get_head();
		$this->load->view('view_ventanas', $data);	
	}

	function dataedit(){ 
		$this->rapyd->load("dataedit");
		
		$edit = new DataEdit("Conceptos", "icon");
		$edit->back_url = site_url("inventario/concepto/filteredgrid");
				
		$edit->codigo = new inputField("Código", "codigo");
		$edit->codigo->rule 			= "required|trim";		
		$edit->codigo->size 			= 10;
		$edit->codigo->maxlength	= 6;
			
		$edit->concepto = new inputField("Conceptos", "concepto");
		$edit->concepto->rule 			= "required|trim";
		$edit->concepto->size 			= 30;
		$edit->concepto->maxlength 	= 30;
		
		$edit->gasto = new inputField("Gastos", "gasto");
		$edit->gasto->size 				= 10;
		$edit->gasto->maxlength 	= 6;
		$edit->gasto->rule 				= "trim";
		
		$edit->gastode = new inputField("Gasto de:", "gastode");		
		$edit->gastode->size 				= 30;
		$edit->gastode->maxlength 	= 30;
		$edit->gastode->rule 				= "trim";
		
		$edit->ingreso = new inputField("Ingreso", "ingreso");
		$edit->ingreso->size 				= 10;
		$edit->ingreso->maxlength 	= 5;
		$edit->ingreso->rule 				= "trim";
		
		$edit->ingresod = new inputField("Ingreso de:", "ingresod"); 
		$edit->ingresod->size 				= 30;
		$edit->ingresod->maxlength 		= 30;
		$edit->ingresod->rule 				= "trim";
		
		$edit->buttons("modify", "save", "undo", "back");		
		$edit->build();
				
		$data['content'] = $edit->output;           
    $data['title']   = " Otros Conceptos ";        
    $data["head"]    = $this->rapyd->get_head();
    $this->load->view('view_ventanas', $data);  
    }
}
?>