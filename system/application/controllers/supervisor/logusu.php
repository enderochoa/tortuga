<?php
class logusu extends Controller {
	
	function logusu(){
		parent::Controller(); 
		$this->load->library("rapyd");
		
	}
	
	function index(){
		redirect("supervisor/logusu/filteredgrid");
	}

	function filteredgrid(){
		$this->rapyd->load("datafilter","datagrid");

		$filter = new DataFilter("", 'logusu');
    
		$filter->usuario = new  dropdownField("Usuario","usuario");
		$filter->usuario->option('','Todos');
		$filter->usuario->options("Select usuario, usuario as value from logusu group by usuario");    
		$filter->usuario->style='width:150px;';
		
		$filter->fechad = new dateonlyField("Desde", "fechad",'d/m/Y');
		$filter->fechah = new dateonlyField("Hasta", "fechah",'d/m/Y');
		$filter->fechad->clause  =$filter->fechah->clause="where";
		$filter->fechad->db_name =$filter->fechah->db_name="fecha";
		$filter->fechad->size=$filter->fechah->size=12;
		$filter->fechad->insertValue = date("Y-m-d",mktime(0, 0, 0, date("m"), date("d")-30,   date("Y")));
		$filter->fechah->insertValue = date("Y-m-d"); 
		$filter->fechad->operator=">="; 
		$filter->fechah->operator="<="; 

		$filter->modulo = new inputField("Modulo","modulo");
		$filter->modulo->size=60;
		
		$filter->referencia = new inputField("Referencia","comenta");
		$filter->referencia->size=60;
		
		$filter->buttons("reset","search");
		$filter->build();
    
		
			$grid = new DataGrid("");                       
			$grid->per_page = 20;
    	
			$grid->column_orderby("Usuario","usuario","usuario","align='left'NOWRAP");
			$grid->column_orderby("Fecha","<b><dbdate_to_human><#fecha#></dbdate_to_human></b> <#hora#>","fecha","align='center'");
			$grid->column_orderby("M&oacute;dulo","modulo","modulo","align='left'NOWRAP");
			$grid->column_orderby("Acci&oacute;n","comenta","comenta","align='left'NOWRAP");
    	    		
			$grid->build();
 			//echo $grid->db->last_query();
			$tabla=$grid->output;
			$data['content'] = $grid->output;
		
		
		$data['content'] = $filter->output.$tabla;
		//$data['filtro']  = $filter->output;
		
		$data['script'] = script("jquery.js")."\n";
		$data['title']   = " Log de Usuarios ";
		$data["head"]    = $this->rapyd->get_head();
		$this->load->view('view_ventanas', $data);
	}
}
?>
