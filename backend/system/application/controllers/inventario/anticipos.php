<?php
class anticipos extends Controller {
	
	function anticipos(){
		parent::Controller(); 
		$this->load->library("rapyd");
	 }

    function index(){    
    	redirect("inventario/anticipos/filteredgrid");
    }

	function filteredgrid(){
		$this->rapyd->load("datafilter","datagrid");
		$this->rapyd->uri->keep_persistence();

		$filter = new DataFilter("Filtro por Número", 'apan');
		$filter->tipo = new inputField("Número", "numero");
		$filter->buttons("reset","search");
		$filter->build();

		$uri = anchor('inventario/anticipos/dataedit/show/<#numero#>','<#numero#>');

		$grid = new DataGrid("Lista de Aplicación de Anticipos");
		$grid->order_by("numero","asc");
		$grid->per_page = 20;

		$grid->column("Número",$uri);
		$grid->column("Fecha","fecha");
		$grid->column("Tipo","tipo");
		$grid->column("Clipro","clipro");
		$grid->column("Nombre","nombre");
		$grid->column("Monto","monto");
		$grid->column("Reintegro","reinte");
		$grid->column("Observaciones","observa1");
		$grid->column(".","observa2");
		$grid->column("Transacc","transac");
		$grid->column("Estampa","estampa");
		$grid->column("Hora","hora");
		$grid->column("Usuario","usuario");
						
		$grid->add("inventario/anticipos/dataedit/create");
		$grid->build();
	
  	$data['content'] = $filter->output.$grid->output;
		$data['title']   = " Aplicación de Anticipos ";
		$data["head"]    = $this->rapyd->get_head();
		$this->load->view('view_ventanas', $data);	
	}
	function dataedit()
 	{
		$this->rapyd->load("dataedit");             
		
		$edit = new DataEdit("Aplicación de Anticipos", "apan");		
		$edit->back_url = site_url("inventario/anticipos/filteredgrid");
		$edit->pre_process('insert','_pre_insert');
		$edit->pre_process('update','_pre_update');
		
		$edit->numero =  new inputField("Número", "numero");
		$edit->numero -> mode="autohide";
		$edit->numero -> maxlength=8;
		$edit->numero -> size=12;
		$edit->numero -> rule="required|trim";
		
		$edit->fecha = new dateonlyField("Fecha", "fecha");
		$edit->fecha->size=12;
		$edit->fecha->insertValue = date("Y-m-d",mktime(0, 0, 0, date("m"), date("d")-30, date("Y")));
		$edit->fecha->rule = "required|trim";
		
		$edit->tipo   =  new inputField("Tipo", "tipo");
		$edit->tipo  ->  maxlength=1;
		$edit->tipo  ->  size=6;
		$edit->tipo  ->  rule="trim";
		
		$edit->clipro =  new inputField("Clipro", "clipro");
		$edit->clipro  ->  maxlength=5;
		$edit->clipro  ->  size=6;
		$edit->clipro  ->  rule="trim";
		
		$edit->nombre =  new inputField("Nombre", "nombre");
		$edit->nombre  ->  maxlength=30;
		$edit->nombre  ->  size=20;
		$edit->nombre  ->  rule="trim";
		
		$edit->monto  =  new inputField("Monto", "monto");
		$edit->monto  ->  size=12;
		$edit->monto  ->  rule="trim";
		
		$edit->reinte =  new inputField("Reintegro", "reinte");
		$edit->reinte  ->  maxlength=5;
		$edit->reinte  ->  size=6;
		$edit->reinte  ->  rule="trim";
		
		$edit->observa1 = new inputField("Observaciones", "observa1");
		$edit->observa1  ->  maxlength=50;
		$edit->observa1  ->  size=50;
		$edit->observa1  ->  rule="trim";
		
		$edit->observa2 =  new inputField("", "observa2");
		$edit->observa2  ->  maxlength=50;
		$edit->observa2  ->  size=50;
		$edit->observa2  ->  rule="trim";
		
		$edit->transac = new inputField("Transacción", "transac");
		
    $edit->hora = new inputField("Hora","hora");   
		
		$edit->buttons("modify", "save", "undo", "delete", "back");
		$edit->build();
 
		$data['content'] = $edit->output;           
    $data['title']   = " Aplicación de Anticipos ";        
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