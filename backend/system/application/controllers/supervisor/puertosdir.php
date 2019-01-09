<?php
class puertosdir extends Controller {
	
	function puertosdir(){
		parent::Controller(); 
		$this->load->library("rapyd");
	}
	
	function index(){
		redirect("supervisor/puertosdir/filteredgrid");
	}

	function filteredgrid(){
		$this->rapyd->load("datafilter","datagrid");

		function rtrunc($forma){
			$pos = strpos($forma,"\r\n");
			return substr($forma,0,$pos);
		}

		$filter = new DataFilter("Filtro de Busqueda");
		$filter->db->select(array("forma",'nombre'));
		$filter->db->from("formatos");
		$filter->db->orderby("nombre");
		//$filter->db->where("forma LIKE");

		$filter->nombre = new inputField("Nombre","nombre");
		$filter->nombre->size=20;
		
		$filter->forma = new inputField("Forma","forma");
		$filter->forma->size=20;

		$filter->buttons("reset","search");
		$filter->build();


		$form = new DataForm("supervisor/puertosdir/filteredgrid/process");
		$form->puerto = new inputField("Nuevo Puerto", "puerto");
		$form->puerto->rule = "required";
		$form->submit("btnsubmit","Cambiar");
		$form->build_form();
		if ($form->on_success()){
			echo 'pasamos';
		}
		
		$grid = new Datagrid("Resultados");
		$grid->use_function('rtrunc');
		$link=site_url('/supervisor/acdatasis/activar');
		$grid->per_page = 20;

		$grid->column("Nombre","nombre");
		$grid->column("Dirigido", "<rtrunc><#forma#></rtrunc>",'align="center"');
		$grid->build(); 			
		//echo $grid->db->last_query();
		$ssql=json_encode($grid->db->last_query());
		
		
		$data['script']="<script type='text/javascript'>
			$(document).ready(function() {
				sql=$ssql;
				
			});
			</script>";
		$data['content'] = $filter->output.$form->output.$grid->output;
		$data['title']   = " Redirecci&oacute;n de puertos en formatos DataSIS ";
		$data["head"]    = script("jquery.js").$this->rapyd->get_head();
		$this->load->view('view_ventanas', $data);
	}
	
	function redir(){
		$sql   =$this->input->post('sql');
		$puerto=$this->input->post('puerto');
		
		if($sql+$puerto!=2){
			return 0;
		}
		
		
	}
}
?>