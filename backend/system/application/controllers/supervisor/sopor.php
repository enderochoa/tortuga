<?php
class Sopor extends Controller {
	
	function Sopor(){
		parent::Controller(); 
		$this->load->library("rapyd");
		define ("THISFILE",   APPPATH."controllers/supervisor". $this->uri->segment(2).EXT);
   }

	##### index #####
	function index(){
		redirect("supervisor/sopor/filteredgrid");
	}

	##### DataFilter + DataGrid #####
	function filteredgrid(){
		$this->rapyd->load("datafilter","datagrid");
		$this->rapyd->uri->keep_persistence();

		//filter
		$filter = new DataFilter("Filtro de soporte", 'soporte');
		$filter->usuario = new inputField("Usuario", "usuario");
		$filter->estampa = new inputField("Estampa", "estampa");
		$filter->titulo = new inputField("Titulo", "titulo");
		$filter->comentario = new inputField("Comentario", "comentario");
		$filter->buttons("reset","search");
		$filter->build();

		$uri = "supervisor/sopor/dataedit/show/<#id#>";

		$grid = new DataGrid("Filtro de soporte");
		//$grid->order_by("nombre","asc");
		$grid->per_page = 20;
		$grid->column_detail("Usuario","usuario", $uri);
		$grid->column("Estampa","estampa");
		$grid->column("Titulo","titulo");
		$grid->column("Comentario","comentario");
		$grid->add("supervisor/soporte/dataedit/create");
		$grid->build();
		//grid

		$data["crud"] = $filter->output . $grid->output;
		$data["titulo"] = 'Soporte';

		$content["content"]   = $this->load->view('rapyd/crud', $data, true);
		$content["rapyd_head"] = $this->rapyd->get_head();
		$content["code"] = '';
		$content["lista"] = "
			<h3>Editar o Agregar</h3>
			<div>Con esta pantalla se puede editar o agregar datos a la tabla scli del Modulo de N&oacute;mina</div>
			<div class='line'></div>
			<a href='#' onclick='window.close()'>Cerrar</a>
			<div class='line'></div>\n<br><br><br>\n";
		$this->load->view('rapyd/tmpsolo', $content);

	}
	
	##### dataedit ##### 
	function dataedit(){
		$this->rapyd->load("dataedit");
		$edit = new DataEdit("soporte", "soporte");
		$edit->back_url = site_url("supervisor/sopor/filteredgrid");
		//$edit->pre_process('delete','_pre_del');
		
		$edit->usuario =  new inputField("Usuario", "usuario");
		$edit->usuario->mode="autohide";
		//$edit->rif->rule = "required";
		//$edit->rif->maxlength=13;
		$edit->usuario->size = 14;
		
		$edit->estampa =  new dateField("Estampa", "estampa","d/m/Y");
		$edit->estampa->size = 10;
		$edit->estampa->mode="autohide";
		
		$edit->titulo =  new inputField("Titulo", "titulo");
		
		$edit->comentario =  new textareaField("Comentario", "comentario");
		$edit->comentario->cols = 70;
		$edit->comentario->rows = 10;
		 
		
		
		if ($this->uri->segment(4)==="1")
			$edit->buttons("modify", "save", "undo", "back");
		else 
			$edit->buttons("modify", "save", "undo", "delete", "back");
		
		$edit->build();
		//echo $edit->_dataobject->db->last_query();;

		$data["edit"] = $edit->output;
		$data["modulo"] = "";

		$content["content"] = $this->load->view('rapyd/dataedit', $data, true);    
		$content["rapyd_head"] = $this->rapyd->get_head();
		$content["code"]  = "";
		$content["lista"] = "
			<h3>Editar o Agregar</h3>
			<div>La identificacion del Grupo debe ser unica, la clase puede ser Cliente, Interna u Otros, en la Descripcion colocar que se quiere agrupar y la cuenta contable para el enlace contable</div>
			<div class='line'></div>
			<a href='#' onclick='window.close()'>Cerrar</a>
			<div class='line'></div>\n<br><br><br>\n";
    
    $this->load->view('rapyd/tmpsolo', $content);
	}

}




?>