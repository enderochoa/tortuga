<?php
class ajusinv extends Controller {
 
	function ajusinv(){
		parent::Controller(); 
		$this->load->library("rapyd");
		$this->load->library("menues");
		//I use THISFILE, instead __FILE__ to prevent some documented php-bugs with higlight_syntax()&__FILE__
		define ("THISFILE",   APPPATH."controllers/inventario/". $this->uri->segment(2).EXT);
		$this->datasis->modulo_id(906,1);
	}

	function index(){
		redirect("inventario/ajusinv/filteredgrid");
	}

	function filteredgrid(){

		$this->rapyd->load("datafilter","datagrid");
		$this->rapyd->uri->keep_persistence();

		$filter = new DataFilter("Filtro de Ajustes de Inventario",'ssal');
				
		$filter->numero = new inputField("Número", "numero");
		$filter->us_codigo->size=20;		
		
		$filter->fecha = new dateonlyField("Fecha", "fecha");
		$filter->fecha->size=20;
		
		$filter->tipo = new inputField("Tipo", "tipo");
		$filter->tipo->size=20;

		$filter->buttons("reset","search");
		$filter->build();
		
		$uri = anchor('inventario/ajusinv/dataedit/show/<#numero#>','<#numero#>');

		$grid = new DataGrid("Lista de Ajustes de Inventario");
		$grid->order_by("numero","asc");
		//$grid->use_function("dbdate_to_human");
		$grid->per_page = 20;

		$grid->column("Número",$uri);
		//$grid->column_orderby("Nombre","us_nombre","us_nombre");
		$grid->column("Fecha","<dbdate_to_human><#fecha#></dbdate_to_human>");
		$grid->column("Tipo","tipo");
		$grid->column("Almacen","alamacen");
		$grid->column("Cargo","cargo");
		$grid->column("Descripción","descrip");
		$grid->column("Uasuario","usuario");
		                                                                    
		$grid->add("inventario/ajusinv/dataedit/create");
		$grid->build();

		$data['content'] = $filter->output.$grid->output;
		$data['title']   = " Ajustes de Inventario ";
		$data["head"]    = $this->rapyd->get_head();
		$this->load->view('view_ventanas', $data);
	}

	function dataedit(){ 
		$this->rapyd->load("dataedit");

		$edit = new DataEdit("Ajustes Inventario", "ssal");
		$edit->back_url = site_url("inventario/ajusinv/filteredgrid");
		$edit->pre_process('insert','_pre_insert');
		$edit->pre_process('update','_pre_update');
				
		$edit->numero = new inputField("N&uacute;mero", "numero");
		$edit->numero->rule = "required|trim";
		$edit->numero->size = 14;
		$edit->numero->maxlength = 8;
		
		$edit->fecha   = new dateonlyField("Fecha", "fecha");
		$edit->fecha  ->size=14;		
		$edit->fecha  ->rule = "required";
		
		$edit->tipo = new inputField("Tipo","tipo");
		$edit->tipo->size= 6;
		$edit->tipo->maxlength= 1;
		$edit->tipo->rule= "trim";
		
		
		$edit->almacen= new dropdownField("Almacen", "almacen");
		$edit->almacen->option("","");
		$edit->almacen->options("SELECT ubica, ubides FROM caub ORDER BY ubides ");
		$edit->almacen->style="width:105px";		
		
		$edit->cargo = new inputField("Cargo", "cargo");
		$edit->cargo->size= 14;
		$edit->cargo->maxlength= 8;
		$edit->cargo->rule= "trim";
		
		$edit->descrip = new inputField("Descripci&oacute;n", "descrip"); 
    $edit->descrip->size= 32;
    $edit->descrip->maxlength= 30;
    $edit->descrip->rule= "trim";
    
    $edit->motivo  = new inputField("Motivo", "motivo"); 
    $edit->motivo ->size= 32;
    $edit->motivo ->maxlength= 40; 
    $edit->motivo->rule= "trim";
		                                                                   
		$edit->buttons("modify", "save", "undo", "delete", "back");
		$edit->build();
		
		$data['content'] = $edit->output;           
    $data['title']   = " Ajustes de Inventario ";        
    $data["head"]    = $this->rapyd->get_head();
    $this->load->view('view_ventanas', $data);  
	}
	function _pre_insert($do){
		$do->set('usuario', $this->session->userdata('usuario'));
	}
	function _pre_update($do){
		$do->set('usuario', $this->session->userdata('usuario'));
	}
	}
?>