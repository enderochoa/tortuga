<?php
class calendario extends Controller {
	
	function calendario(){
		parent::Controller(); 
		$this->load->library("rapyd");

   }

     function index(){
    	$this->datasis->modulo_id(56,1);
    	redirect("nomina/calendario/filteredgrid");
    }
 
	function filteredgrid(){
		$this->rapyd->load("datafilter","datagrid");
		$this->rapyd->uri->keep_persistence();

		$filter = new DataFilter("Filtro por Motivo", 'cale');
		$filter->motivo = new inputField("Motivo", "motivo");
		$filter->buttons("reset","search");
		$filter->build();

		$uri = anchor('nomina/calendario/dataedit/show/<#feria#>','<#feria#>');

		$grid = new DataGrid("Lista de Calendario");
		$grid->order_by("feria","asc");
		$grid->per_page = 20;
 
		$grid->column("D&iacute;a Feriado",$uri);
		$grid->column("Motivo","motivo");
		$grid->column("Fijo","fijo");
						
		$grid->add("nomina/calendario/dataedit/create");
		$grid->build();
		
		$data['content'] = $filter->output.$grid->output;
		$data['title']   = " Calendario ";
		$data["head"]    = $this->rapyd->get_head();
		$this->load->view('view_ventanas', $data);	

	}

	function dataedit()
 	{
 		
 		function _vfecha($strdate){
  		if ($strdate=='') return true;
  		if (ereg("([0-9]{2})/([0-9]{2})/([0-9]{4})", "$strdate", $arr)) {
  	  	if (date("d/m/Y",strtotime("$arr[2]/$arr[1]/$arr[3]"))==$strdate) return true;
  		} return false;
		} 
 		//$this->datasis->modulo_id(701001,1);
		$this->rapyd->load("dataedit");
		$edit = new DataEdit("Calendario", "cale");
		$edit->back_url = site_url("nomina/calendario/filteredgrid");
		$edit->feria =  new DateField("D&iacute;a Feriado", "feria","d/m/Y");
		$edit->feria->rule="required|callback_vfecha(";
		$edit->feria->mode="autohide";
		
		$edit->motivo =  new inputField("Motivo", "motivo");
		$edit->fijo = new inputField("Fijo","fijo");
						
		$edit->buttons("modify", "save", "undo", "delete", "back");
		$edit->build();

		$data['content'] = $edit->output;           
    $data['title']   = " Calendario ";        
    $data["head"]    = $this->rapyd->get_head();
    $this->load->view('view_ventanas', $data);  
	}
}
?>