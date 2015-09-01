<?php
class modulos extends Controller {
	 var $upload_path;
		
	function modulos(){
		parent::Controller(); 
		$this->load->library("rapyd");
		$this->load->library("path");
		$path=new Path();
		$path->setPath($this->config->item('uploads_dir'));
		$path->append('/graficos');
		$this->upload_path =$path->getPath().'/';
		
	}
	
	function index()
	{
		redirect("doc/modulos/filteredgrid");
	}

	function filteredgrid(){
		$this->rapyd->load("datafilter","datagrid");
		$this->rapyd->uri->keep_persistence();

		$filter = new DataFilter("Filtro de Modulos");
		$filter->db->select("a.modulo,a.referen,a.estado,b.titulo,a.ubicacion,b.ejecutar");
		$filter->db->from('doc_modulos AS a');
		$filter->db->join('intramenu AS b',"b.ejecutar LIKE CONCAT('%/',a.modulo) ",'LEFT');
		$filter->db->group_by('a.modulo');
		$filter->db->order_by('a.modulo','asc');   
				
		$filter->modulos = new inputField("Archivo","a.modulo");
		$filter->modulos->size=20;
		
		$filter->ubicacion = new inputField("Modulo","a.ubicacion");
		$filter->ubicacion->size=20;
		
		$filter->estado = new dropdownField("Estado","a.estado");  
		$filter->estado->option("","");
		$filter->estado->option("RC","RC");
	  $filter->estado->option("Beta","Beta");
	  $filter->estado->option("Alfa","Alfa");
	  $filter->estado->option("Proyec","Proyec");
	  $filter->estado->style='width:100px';
	  
	  //$filter->tipo = new dropdownField("Tipo","b.tipo");  
		//$filter->tipo->option("","Todos");
		//$filter->tipo->option("I","Inventario");
	  //$filter->tipo->option("S","Supermercado");
	  //$filter->tipo->option("H","Hospitalidad");
	  //$filter->tipo->style='width:150px';
	  
	  $filter->descrip = new inputField("Descripci&oacute;n","a.referen");
		$filter->descrip->size=40; 
	 		
		$filter->buttons("reset","search");
		$filter->build();
    
    $uri = anchor('doc/modulos/dataedit/show/<#modulo#>/<#ubicacion#>','<#modulo#>');
    $uri1 = anchor('<#ejecutar#>','<#ejecutar#>'); 

		$grid = new DataGrid("Lista de Modulos");
		$grid->db->select("a.modulo,a.referen,a.estado,b.titulo,a.ubicacion,b.ejecutar");
		$grid->db->from('doc_modulos AS a');
		$grid->db->join('intramenu AS b',"b.ejecutar LIKE CONCAT('%/',a.modulo) ",'LEFT');
		$grid->db->group_by('a.modulo');
		$grid->db->order_by('a.modulo','asc');                        
		$grid->per_page = 15;
		//SELECT a.*, b.titulo FROM doc_modulos AS a LEFT JOIN intramenu AS b ON b.ejecutar LIKE CONCAT('%',a.modulo,'%') 
		//ORDER BY a.modulo asc 

		$grid->column("Archivo",$uri );
		$grid->column("Descripci&oacute;n","referen");
		$grid->column("Titulo","titulo");
		$grid->column("Estado","estado");
		$grid->column("Ubicacion","ubicacion");
		$grid->column("Ver",$uri1);
        		
		$grid->add("doc/modulos/dataedit/create");
		$grid->build();
		//echo $grid->db->last_query();
		
		$data['content'] = $filter->output.$grid->output;
		$data['title']   = "Documentaci&oacute;n de Modulos";
		$data["head"]    = $this->rapyd->get_head();
		$this->load->view('view_ventanas', $data);
	}
	
	function dataedit()
 	{
		$this->rapyd->load("dataedit");
		$edit = new DataEdit("Modulos", "doc_modulos");
		$edit->back_url = site_url("doc/modulos/filteredgrid");
		
		$edit->modulos= new inputField("Modulo", "modulo");
		//$edit->modulos->mode="autohide";
		$edit->modulos->size = 25;
		$edit->modulos->rule= "required";
		
		$edit->descrip   = new inputField("Descripci&oacute;n", "referen");
		$edit->descrip->size = 55;
		//$edit->descrip->rule= "required";
		
		$edit->estado = new dropdownField("Estado","estado");
		$edit->estado->option("",""); 
		$edit->estado->option("RC","RC");
	  $edit->estado->option("Beta","Beta");
	  $edit->estado->option("Alfa","Alfa");
	  $edit->estado->option("Proyec","Proyec");
	  $edit->estado->style='width:100px'; 
	  $edit->estado->rule= "required";
	  
	  $edit->enlase= new inputField("Ubicaci&oacute;n","ubicacion");
		$edit->enlase->size=20;
	  $edit->enlase->rule= "required";
	  
		$edit->grafico = new uploadField("Grafico","grafico");
		//$edit->grafico->rule          = "required";
		$edit->grafico->upload_path   = $this->upload_path;
		$edit->grafico->allowed_types = "png";
		$edit->grafico->delete_file   =false;
		//$edit->grafico->append('Solo imagenes JPG');
		//$edit->grafico->file_name = url_title($codigo).'_.jpg';
  
	  $edit->implementacion= new textareaField("Implementacion","implementacion");
		$edit->implementacion->cols = 80;
		$edit->implementacion->rows =10;
	  //$edit->implementacion->rule= "required";
	  
		$edit->buttons("modify","save", "undo", "back");
		$edit->build();
		
    $data['content'] = $edit->output;           
    $data['title']   = "Documentaci&oacute;n de Modulos";
    $data["head"]    = $this->rapyd->get_head();
    $this->load->view('view_ventanas', $data);  
	}  
 }
?>


