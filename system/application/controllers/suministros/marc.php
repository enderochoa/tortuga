<?php
class Marc extends Controller{
	
	function marc(){
		parent::Controller(); 
		$this->load->library("rapyd");
		$this->datasis->modulo_id(73,1);
	}

	function index(){		
		redirect("suministros/marc/filteredgrid");
	}
	
	function filteredgrid(){
		$this->rapyd->load("datafilter","datagrid");
		
		$filter = new DataFilter("Filtro de Marcas", 'marc');

		$filter->grupo = new inputField("Marca","marca");
		$filter->grupo->size=10;

		$filter->buttons("reset","search");
		$filter->build();

		$uri = anchor('suministros/marc/dataedit/show/<raencode><#marca#></raencode>','<#marca#>');

		$grid = new DataGrid("Lista de Marcas");
		$grid->order_by("marca","asc");
		$grid->per_page = 20;

		$grid->column("Marca",$uri);

		$grid->add("suministros/marc/dataedit/create");
		$grid->build();
		
		$data['content'] = $filter->output.$grid->output;
		$data['title']   = " Marcas ";
		$data["head"]    = $this->rapyd->get_head();
		$this->load->view('view_ventanas', $data);	
	}
	
	function dataedit()
 	{
		$this->rapyd->load("dataedit");

		$edit = new DataEdit("Marcas", "marc");
		
		$edit->post_process('insert','_post_insert');
		$edit->post_process('update','_post_update');
		$edit->post_process('delete','_post_delete');

		$edit->back_url = site_url("suministros/marc/filteredgrid");
		
		$edit->marca =  new inputField("Marca", "marca");
		$edit->marca->size = 15;
		$edit->marca->maxlength=30;
		$edit->marca->rule = "strtoupper|required|trim";
		
		$edit->buttons("add","modify", "save", "undo", "delete", "back");
		$edit->build();

		$data['content'] = $edit->output;
    $data['title']   = " Marca ";
    $data['head']    = $this->rapyd->get_head();
    $this->load->view('view_ventanas', $data);
    
	}
	
	function _post_insert($do){
		$marca=$do->get('marca');
		logusu('marc',"MARCA $marca CREADA");
	}
	
	function _post_update($do){
		$marca=$do->get('marca');
		logusu('marc',"MARCA $marca MODIFICADA");
	}
	
	function _post_delete($do){
		$marca=$do->get('marca');
		logusu('marc',"MARCA $marca ELIMINADA");
	}
}
?>