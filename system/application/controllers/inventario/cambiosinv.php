<?php
//almacenes
class cambiosinv extends Controller {
	 	
	function cambiosinv(){
		parent::Controller(); 

		$this->load->helper('url');
		$this->load->helper('text');
		$this->datasis->modulo_id(307,1);
		$this->load->library("rapyd");
		define ("THISFILE",   APPPATH."controllers/nomina". $this->uri->segment(2).EXT);
   }
 
    function index(){
    	//$this->datasis->modulo_id(307,1);
    	redirect("inventario/cambiosinv/filteredgrid");
    }
  
	function filteredgrid(){
		$this->rapyd->load("datafilter","datagrid");
		$this->rapyd->uri->keep_persistence();

		$filter = new DataFilter("Filtro por Articulos", 'sinv');
		
		$filter->codigo = new inputField("codigo", "codigo");
		$filter->codigo->size=15;
		
		$filter->descrip = new inputField("Descripcion", "descrip");	
		$filter->desrip->size=50;
		
		$filter->grupo = new dropdownField("Grupo", "grupo");
		$filter->grupo->option("","");
		$filter->grupo->options("SELECT grupo, nom_grup FROM grup ORDER BY grupo");
		$filter->grupo->onchange = "get_linea();";
		
		$filter->buttons("reset","search");
		$filter->build();

		$uri = anchor('inventario/cambiosinv/dataedit/modify/<#id#>','<#codigo#>');

		$grid = new DataGrid("Lista de Articulos");
		$grid->order_by("codigo","asc");
		$grid->per_page = 20;

		$grid->column("Codigo",$uri);
		$grid->column("Descripción","descrip");
		$grid->column("Grupo","grupo");
		$grid->column("Precio","pond");
		$grid->column("Existencia","existen");
		$grid->column("Existencia Maxima","exmax");
		$grid->column("Existencia Minima","exmin");
		$grid->column("Activo","activo");
								
		$grid->add("inventario/cambiosinv/dataedit/create");
		$grid->build();
		
    $data['content'] = $filter->output.$grid->output;
		$data['title']   = " Articulos ";
		$data["head"]    = $this->rapyd->get_head();
		$this->load->view('view_ventanas', $data);	
	}

	function dataedit()
 	{
		$this->rapyd->load("dataedit");
		$edit = new DataEdit("ARTICULO", "sinv");
		$edit->back_url = site_url("inventario/cambiosinv/filteredgrid");
		
		$edit->post_process('update','_post_update');
		
		$edit->codigo = new inputField("Codigo","codigo");
		$edit->codigo->size=25;
		$edit->codigo->mode = "autohide";
		
		$edit->descrip = new inputField("Descripcion","descrip");
		$edit->descrip->size=50;
		$edit->descrip->mode = "autohide";
				
		$edit->exmin = new inputField("Existencia Minima", "exmin");
		$edit->exmin->size=20;
		
		$edit->exmax = new inputField("Existencia Maxima", "exmax");
		$edit->exmax->size=20;
		
		$edit->existen = new inputField("Existencia", "existen");
		$edit->existen->size=20;
		
		$edit->activo = new dropdownField("Activo", "activo");
		$edit->activo->option("","");
		$edit->activo->option("S","Si");
		$edit->activo->option("N","No");
		$edit->activo->style = "width:50px";
    
		$edit->buttons("modify", "save", "undo",  "back");
		$edit->build();
 
		$data['content'] = $edit->output;
    $data['title']   = " Almacenes ";
    $data["head"]    = $this->rapyd->get_head();
    $this->load->view('view_ventanas', $data);
	}
	function _post_update($do){
		$codigo=$do->get('codigo');
		$descrip=$do->get('descrip');
	}
}
?>