<?php
class tiketp extends Controller{

	function tiketp(){
		parent::Controller(); 
		$this->load->library("rapyd");
		//$this->load->library("menues");
		$this->load->database();
	}

	function index(){
		redirect("supervisor/tiketp/filteredgrid");
	}

	function filteredgrid(){
		$this->rapyd->load("datafilter","datagrid");
		
	 $mSCLId=array(
			'tabla'   =>'scli',
			'columnas'=>array(
			'cliente' =>'Código Cliente',
			'nombre'=>'Nombre', 
			'cirepre'=>'Rif/Cedula',
			'dire11'=>'Dirección'),
			'filtro'  =>array('cliente'=>'Código Cliente','nombre'=>'Nombre'),
			'retornar'=>array('cliente'=>'empresa','nombre'=>'nombre'),
			'titulo'  =>'Buscar Cliente');
		
		$boton =$this->datasis->modbus($mSCLId);
		
		$pers=array(
		'tabla'   =>'pers',
		'columnas'=>array(
		'codigo'  =>'Codigo',
		'cedula'  =>'Cedula',
		'nombre'  =>'Nombre',
		'apellido' =>'Apellido'),
		'filtro'  =>array('codigo'=>'C&oacute;digo','cedula'=>'Cedula'),
		'retornar'=>array('codigo'=>'codigo'),
		'titulo'  =>'Buscar Personal');
					  
		$boton1=$this->datasis->modbus($pers);

		$filter = new DataFilter("Filtro de Ticket Pendientes", 'tiketp');

		$filter->fechad = new dateonlyField("Fecha Desde", "fechad",'d/m/Y');
		$filter->fechad->clause  ="where";
		$filter->fechad->db_name ="fecha";
		$filter->fechad->operator=">=";
		$filter->fechad->size=12;
		
		$filter->fechah = new dateonlyField("Fecha Hasta", "fechah",'d/m/Y');
		$filter->fechah->clause="where";
		$filter->fechah->db_name="fecha";
		$filter->fechah->operator="<=";
		$filter->fechah->size=12;
		
		$filter->codigo = new inputField("Codigo", "codigo");
		$filter->codigo->size=20;
		$filter->codigo->append($boton1);
		
		$filter->asignacion= new inputField("Asignacion","asignacion");
		$filter->asignacion->size=20;
		$filter->asignacion->append($boton);
		
		$filter->prioridad = new dropdownField("Prioridad", "prioridad");
		$filter->prioridad->option("","Todos");
		$filter->prioridad->option("Alta","Alta");
		$filter->prioridad->option("Muy Alta","Muy Alta");
		$filter->prioridad->option("Media","Media");
		$filter->prioridad->option("Baja","Baja");
		$filter->prioridad->option("Muy Baja","Muy Baja");
		$filter->prioridad->style="width:150px";
		
		$filter->status= new dropdownField("Status","status");
		$filter->status->style="width:150px";
		$filter->status->option("","Todos");
		$filter->status->option("Pendiente","Pendiente");
		$filter->status->option("Resuelto","Resuelto");

		$filter->buttons("reset","search");
		$filter->build();

		$uri = anchor('supervisor/tiketp/dataedit/show/<#id#>','<#id#>');

		$grid = new DataGrid("Lista de Reportes de Trabajo");
		$grid->order_by("id","asc");
		$grid->per_page=15;
		
		$grid->column("Numero",$uri);
		$grid->column("Fecha"			,"<dbdate_to_human><#fecha#></dbdate_to_human>","align='center'");
		$grid->column("Empresa","empresa");
		$grid->column("Tiket","tiket");
		$grid->column("Asignacion","nombre");

		$grid->add("supervisor/tiketp/dataedit/create");
		$grid->build();

		$data['content'] = $filter->output.$grid->output;
		$data['title']   = " Ticket Pendientes ";
		$data["head"]    = $this->rapyd->get_head();	
		$this->load->view('view_ventanas', $data);
	}

	function dataedit(){ 
		$this->rapyd->load("dataedit");
		
		$pers=array(
		'tabla'   =>'pers',
		'columnas'=>array(
		'codigo'  =>'Codigo',
		'cedula'  =>'Cedula',
		'nombre'  =>'Nombre',
		'apellido' =>'Apellido'),
		'filtro'  =>array('codigo'=>'C&oacute;digo','cedula'=>'Cedula'),
		'retornar'=>array('codigo'=>'asignacion','nombre'=>'nombre'),
		'titulo'  =>'Buscar Personal');
					  
		$boton=$this->datasis->modbus($pers);
		
		$mSCLId=array(
			'tabla'   =>'scli',
			'columnas'=>array(
			'cliente' =>'Código Cliente',
			'nombre'=>'Nombre', 
			'cirepre'=>'Rif/Cedula',
			'dire11'=>'Dirección'),
			'filtro'  =>array('cliente'=>'Código Cliente','nombre'=>'Nombre'),
			'retornar'=>array('cliente'=>'codigo','nombre'=>'empresa'),
			'titulo'  =>'Buscar Cliente');
		
		$boton1 =$this->datasis->modbus($mSCLId);
		
		$edit = new DataEdit("Agregar", "tiketp");
		$edit->back_url = site_url("supervisor/tiketp/filteredgrid");
	
		$edit->fecha = new dateonlyField("Fecha", "fecha",'d/m/Y');
		$edit->fecha->size=12;
		
		$edit->codigo = new inputField("Codigo", "codigo");
		$edit->codigo->size=20;
		$edit->codigo->append($boton1);
		
		$edit->empresa = new inputField("Empresa","empresa");
		$edit->empresa->size=50;
		
		$edit->usuario = new inputField("Usuario","usuario");
		$edit->usuario->size=20;
		$edit->usuario->rule = "strtoupper|required|trim";
		
		$edit->tiket = new textareaField("ticket","tiket");  
		$edit->tiket->cols = 80;                                   
		$edit->tiket->rows = 10;                                   
		$edit->tiket->rule = "strtoupper|required|trim";
		
		$edit->prioridad = new dropdownField("Prioridad", "prioridad");
		$edit->prioridad->option("","Todos");
		$edit->prioridad->option("Alta","Alta");
		$edit->prioridad->option("Muy Alta","Muy Alta");
		$edit->prioridad->option("Media","Media");
		$edit->prioridad->option("Baja","Baja");
		$edit->prioridad->option("Muy Baja","Muy Baja");
		$edit->prioridad->style="width:150px";

		$edit->status= new dropdownField("Status","status");
		$edit->status->style="width:150px";
		$edit->status->option("","Todos");
		$edit->status->option("Pendiente","Pendiente");
		$edit->status->option("Resuelto","Resuelto");

		$edit->asignacion= new inputField("Asignacion","asignacion");
		$edit->asignacion->size=20;
		$edit->asignacion->append($boton);
		
		$edit->nombre= new inputField("Nombre","nombre");
		$edit->nombre->size=50;
		  				    
		$edit->buttons("modify", "save", "undo", "delete", "back");
		$edit->build();
		
		//$smenu['link']=barra_menu('912');		
		$data['content'] = $edit->output;
		//$data['smenu']   = $this->load->view('view_sub_menu', $smenu,true);
		$data['title']   = " Ticket Pendientes ";
		$data["head"]    = script("jquery.pack.js").script("plugins/jquery.numeric.pack.js").script("plugins/jquery.floatnumber.js").$this->rapyd->get_head();
		$this->load->view('view_ventanas', $data);
	}
	function instalar(){
		$mSQL="CREATE TABLE IF NOT EXISTS `tiketp` (`id` TINYINT AUTO_INCREMENT, `estampa` TIMESTAMP, `fecha` DATE, `codigo` VARCHAR (20),`empresa` VARCHAR (100), `tiket` TEXT,`usuario` VARCHAR (20),`status` VARCHAR (20), `asignacion` VARCHAR (20),`nombre` VARCHAR (50),PRIMARY KEY(`id`)) TYPE = MyISAM";
		$this->db->simple_query($mSQL);
	}
}
?>