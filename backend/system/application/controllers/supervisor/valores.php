<?php
class valores extends Controller {
	function valores(){
		parent::Controller(); 
		$this->load->library("rapyd");

	}
	function index(){
		redirect("supervisor/valores/filteredgrid");
	}
	function filteredgrid(){

		$this->rapyd->load("datafilter","datagrid");
		$this->rapyd->uri->keep_persistence();
		
		$filter = new DataFilter("", 'valores');
				
		$filter->nombre = new inputField("Nombre","nombre");
		$filter->nombre->size=35;

		$filter->buttons("reset","search");
		$filter->build();
		
		$uri = anchor('supervisor/valores/dataedit/show/<raencode><#nombre#></raencode>','<#nombre#>');
		
		$grid = new DataGrid("");
		$grid->order_by("nombre","asc");

		$grid->column_orderby("Nombre",$uri    ,"nombre");
		$grid->column_orderby("Valor" , "valor","valor" );		
		$grid->column_orderby("Descripci&oacute;n"       ,"descrip","descrip","align='left'NOWRAP");

		$grid->add("supervisor/valores/dataedit/create");
		$grid->build();
		
		//$data['content'] = $filter->output.$grid->output;
		$data['filtro']  = $filter->output;
		$data['content'] = $grid->output;
		$data['script'] = script("jquery.js")."\n";
		$data['title']   = " Valores ";
		$data["head"]    = $this->rapyd->get_head();
		$this->load->view('view_ventanas', $data);	
}

	function dataedit(){ 
		$this->rapyd->load("dataedit");
		
		$edit = new DataEdit("Valor","valores");
		$edit->back_url = site_url("supervisor/valores/filteredgrid");
				
		$edit->nombre = new inputField("Nombre", "nombre");
		$edit->nombre->size=35;
		$edit->nombre->mode='autohide';
		
		$edit->valor = new inputField("Valor", "valor");
		$edit->valor->size=45;
		
		$edit->descrip = new inputField("Descripción", "descrip");
		$edit->descrip->size=45;                         
		
		$edit->buttons("modify", "save", "undo", "back");		
		$edit->build();
			
		$data['content'] = $edit->output;           
		$data['title']   = " Valores ";        
		$data["head"]    = $this->rapyd->get_head();
		$this->load->view('view_ventanas', $data);  
	}
	
	function instalar(){
			$query="ALTER TABLE `valores` CHANGE COLUMN `nombre` `nombre` VARCHAR(100) NOT NULL DEFAULT '' FIRST";
			$this->db->simple_query($query);
	}
}
?>
