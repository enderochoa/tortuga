<?php
class sucu extends Controller{
	
	function sucu(){
		parent::Controller();
		$this->load->library("rapyd");
	}

	function index(){
		redirect("supervisor/sucu/filteredgrid");
	}

	function filteredgrid(){
		$this->rapyd->load("datafilter","datagrid");

		$filter = new DataFilter("", 'sucu');
		
		$filter->codigo = new inputField("C&oacute;digo", "codigo");
		$filter->codigo->size=10;
		
		$filter->sucursal= new inputField("Sucursal","sucursal");
		$filter->sucursal->size=20;
			
		$filter->buttons("reset","search");
		$filter->build();

		$uri = anchor('supervisor/sucu/dataedit/show/<#codigo#>','<#codigo#>');

		$grid = new DataGrid("Lista de Sucursales");
		$grid->order_by("codigo","asc");
		$grid->per_page=15;
		
		$grid->column("Sucursal",$uri);
		$grid->column("Nombre","sucursal");
		$grid->column("URL","url");
		$grid->column("Prefijo","prefijo");
		$grid->column("Proteo","proteo");
		$grid->add("supervisor/sucu/dataedit/create");
		$grid->build();

		$data['content'] = $filter->output.$grid->output;
		$data['title']   = " Sucursal ";
		$data["head"]    = $this->rapyd->get_head();	
		$this->load->view('view_ventanas', $data);
	}

	function dataedit(){ 
		$this->rapyd->load("dataedit");
		
		$edit = new DataEdit("Sucursal","sucu");
		$edit->back_url = site_url("supervisor/sucu/filteredgrid");

		$edit->codigo = new inputField("C&oacute;digo", "codigo");
		$edit->codigo->rule = "required";
		$edit->codigo->mode = "autohide";
		$edit->codigo->size = 4;
		$edit->codigo->maxlength = 2;
		
		$edit->sucursal = new inputField("Sucursal","sucursal");
		$edit->sucursal->rule = "strtoupper";
		$edit->sucursal->size = 60;
		$edit->sucursal->maxlength = 45;
		
		$edit->url = new inputField("URL","url");
		$edit->url->size =80;
		$edit->url->maxlength =200;
		
		$edit->prefijo = new inputField("Prefijo","prefijo");
		$edit->prefijo->size = 5;
		$edit->prefijo->maxlength = 3;
	
		$edit->proteo = new inputField("Proteo","proteo");
		$edit->proteo->maxlength =50;
		$edit->proteo->size =70;
		  
		$edit->buttons("modify", "save", "undo", "delete", "back");
		$edit->build();
		
		$data['content'] = $edit->output; 		
		$data['title']   = " Sucursal ";
		$data["head"]    = script("jquery.pack.js").script("plugins/jquery.numeric.pack.js").script("plugins/jquery.floatnumber.js").$this->rapyd->get_head();
		$this->load->view('view_ventanas', $data);
	}
	function instalar(){
		$mSQL="ALTER TABLE `sucu` ADD `url` VARCHAR(200) NULL
		ALTER TABLE `sucu` ADD `prefijo` VARCHAR(3) NULL
		ALTER TABLE `sucu` ADD `proteo` VARCHAR(50) NULL;";
    $this->db->simple_query($mSQL);		
	}
}
?>