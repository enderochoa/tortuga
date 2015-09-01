<?php
class noticias  extends Controller {
	function noticias(){ 
		parent::Controller(); 
		$this->load->library("rapyd");
		//$this->load->library("menues");
		//$this->datasis->modulo_id(908,1);
	}
	function index(){ 
		redirect("supervisor/noticias/ver");
	}
	function ver($id=NULL){ 
		$this->rapyd->load("datatable");

		$table = new DataTable(null);
		$table->cell_attributes = 'style="vertical-align:middle;"';
		$select=array("envia","mensaje","recibe");
		
		$table->db->select($select);
		$table->db->from("muro");
	  $table->db->where("recibe='Todos'");
	  $table->db->orderby("codigo DESC");
		
		$table->per_row  = 1;
		$table->per_page = 20;
		$table->cell_template = "<div class='marco1' ><#mensaje#><br><b class='mininegro'>Usuario: <#envia#></b></div><br>";
		$table->build();

		if($this->datasis->login()){
			$prop=array('type'=>'button','value'=>'Agregar','name'=>'mas'  ,'onclick' => "javascript:window.location='".site_url("supervisor/noticias/dataedit/create")."'");
			$form=form_input($prop);
						
		}else{
			$form='';
		}
				
		$data['content'] = $table->output.$form;
		$data["head"]    = $this->rapyd->get_head().style('marcos.css').style('estilos.css');
		$data['title']   = " Noticias ";
		$this->load->view('view_ventanas', $data);
		}
		function dataedit(){
			$this->rapyd->load("dataedit");
			
			$edit = new DataEdit("Muro","muro");
			$edit->back_url = site_url("supervisor/noticias/");
			
			$edit->recibe = new dropdownField("Recibe","recibe");
			$edit->recibe->option("Todos","Todos");
			$edit->recibe->options("SELECT us_codigo, us_nombre FROM usuario ORDER BY us_nombre");
			$edit->recibe->style = "width:200px";
			$edit->recibe->rule="required";
			
			$edit->mensaje = new textareaField("Mensaje", "mensaje");
			$edit->mensaje->cols = 80;
			$edit->mensaje->rows =3;
					  
			$edit->buttons("save", "undo", "back");
			$edit->build();
			
			$data['content'] = $edit->output; 		
			$data['title']   = " Muro ";
      $data["head"]    = $this->rapyd->get_head();
			$this->load->view('view_ventanas', $data);
	}
}
?>