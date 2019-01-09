<?php 
class Unidad extends Controller{
	
	function unidad(){
		parent::Controller(); 
		$this->load->library("rapyd");
		$this->datasis->modulo_id(74,1);		
	}
	
	function index(){		
		redirect("suministros/unidad/filteredgrid");
	}

	function filteredgrid(){
		$this->rapyd->load("datafilter","datagrid");
		
		$filter = new DataFilter("", 'unidad');
		
		$filter->unidades = new inputField("Unidad","unidades");
		$filter->unidades->size=10;

		$filter->buttons("reset","search");
		$filter->build();

		$uri = anchor('suministros/unidad/dataedit/show/<raencode><#unidades#></raencode>','<#unidades#>');

		$grid = new DataGrid("Lista de Unidades ");
		$grid->order_by("unidades","asc");
		$grid->per_page = 20;
		                                  
		$grid->column_orderby("Unidades",$uri,"unidades");

		$grid->add("suministros/unidad/dataedit/create");
		$grid->build();
		
		//$data['content'] = $filter->output.$grid->output;
		$data['filtro']  = $filter->output;
		$data['content'] = $grid->output;
		$data['script'] = script("jquery.js")."\n";
		$data['title']   = "Unidades";
		$data["head"]    = $this->rapyd->get_head();
		$this->load->view('view_ventanas', $data);	
	}
	
	function dataedit()
 	{
		$this->rapyd->load("dataedit");

		$edit = new DataEdit("Unidad","unidad");
		$edit->post_process('insert','_post_insert');
		$edit->post_process('update','_post_update');
		$edit->post_process('delete','_post_delete');

		$edit->back_url = site_url("suministros/unidad/filteredgrid");
		
		$edit->unidades =  new inputField("Unidad",'unidades');		
		$edit->unidades ->size = 15;
		$edit->unidades ->maxlength=30;
		$edit->unidades ->rule = "strtoupper|required|trim";
		
		$edit->buttons("add","modify", "save", "undo", "delete", "back");
		$edit->build();

		$data['content'] = $edit->output;           
		$data['title']   = "Unidad";
		$data['head']    = $this->rapyd->get_head();
		$this->load->view('view_ventanas', $data);
    
	}
	
	
	
	function _post_insert($do){
		$unidades=$do->get('unidades');
		logusu('unidad',"MARCA $unidades CREADA");
	}
	
	function _post_update($do){
		$unidades=$do->get('unidades');
		logusu('unidad',"MARCA $unidades MODIFICADA");
	}

	function _post_delete($do){
		$unidades=$do->get('unidades');
		logusu('unidad',"MARCA $unidades ELIMINADA");
	}
}
?>