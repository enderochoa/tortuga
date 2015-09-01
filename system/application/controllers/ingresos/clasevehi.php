<?php
class clasevehi extends Controller {
	
	function clasevehi(){
		parent::Controller();
		$this->load->library("rapyd");
	}
	
	function  index(){
		redirect("ingresos/clasevehi/filteredgrid");
	}
	
	function filteredgrid(){
		$this->rapyd->load("datafilter2","datagrid");//
		
		$filter = new DataFilter2("","clasevehi");
		
		$filter->codigo = new inputField("C&oacute;digo", "codigo");
		$filter->codigo->size=5;
		//$filter->codigo->clause="likerigth";
		
		$filter->nombre = new inputField("Nombre", "nombre");
		$filter->nombre->size=40;
		$filter->nombre->clause="likerigth";
			
		
		$filter->buttons("reset","search");
		
		$filter->build();
		
		
		
		$grid = new DataGrid("");
		
		$grid->order_by("codigo","asc");
		
		$grid->column_orderby("C&oacute;digo" ,"codigo"    ,"codigo"     ,"align='left'      ");
		$grid->column_orderby("Nombre"        ,"nombre"    ,"nombre"     ,"align='left'NOWRAP");
		
		
		$grid->add("ingresos/clasevehi/dataedit/create");
		
		$grid->build();
		
		$data['content'] = $grid->output;
		$data['filtro']  = $filter->output;
		$data['title']   = "Clase de Vehiculo"; //"  ";
		$data["script"]  = script("jquery.js")."\n"; 
		$data["head"]    = $this->rapyd->get_head();
		$this->load->view('view_ventanas', $data);
	}
	
	function dataedit(){
		//$this->datasis->modulo_id(105,1);
		
		$this->rapyd->load("dataedit");
		
		$script='
			$(".inputnum").numeric(".");
		';
		
		$edit = new DataEdit("Clase de Vehiculo", "clasevehi");
		$edit->script($script,"create");
		
		$edit->back_url = site_url("ingresos/clasevehi/filteredgrid");
			 
		 
		$edit->nombre = new inputField("Nombre", "nombre");
		$edit->nombre->size=40;
		$edit->nombre->maxlength=80;
		$edit->nombre->rule='required';
		
		
		$edit->buttons("modify", "save", "undo", "delete", "back");
		 
		$edit->build();

		$data['content'] = $edit->output;
		$data['title']   = "Clase de Vehiculo";
		$data["head"]    = script("jquery.pack.js").$this->rapyd->get_head();
		$this->load->view('view_ventanas', $data);
		
	}

}
?>
