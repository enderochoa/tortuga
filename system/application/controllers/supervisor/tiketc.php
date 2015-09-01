<?php
class tiketc extends Controller {

	var $estado;
 	var $prioridad;
                    
	function tiketc(){ 
		parent::Controller(); 
		$this->load->library("rapyd");
		$this->load->library("menues");
		$this->datasis->modulo_id(109,1);
		$this->estado=array(
		 "N"=>"Nuevo",
     "P"=>"Pendiente",
     "R"=>"Resueltos",
     "C"=>"Cerrado"); 
 	  
		$this->prioridad=array(
		 "1"=>"Muy Alta",
     "2"=>"Alta",
     "3"=>"Media",
     "4"=>"Baja",
     "5"=>"Muy baja");
	}

	function index(){ 
		redirect("supervisor/tiketc/filteredgrid");
	}

	function filteredgrid(){
		$this->rapyd->load("datafilter","datagrid2");
		
		$scli=array(
	  'tabla'   =>'scli',
	  'columnas'=>array(
		'cliente' =>'Código Cliente',
		'nombre'  =>'Nombre',
		'contacto'=>'Contacto'),
	  'filtro'  =>array('cliente'=>'Código Cliente','nombre'=>'Nombre'),
	  'retornar'=>array('cliente'=>'cliente'),
	  'titulo'  =>'Buscar Cliente');
		
		$boton=$this->datasis->modbus($scli);
 		
 		
		$filter = new DataFilter("Filtro de Control de Tiket");
		$select=array("a.id as idm","a.sucursal","a.asignacion","a.idt as idt","a.padre","a.pertenece","a.prioridad","a.usuario as usuarios","a.contenido","a.estampa","a.actualizado","a.estado",
		"b.id","b.cliente as clientes","b.ubicacion","b.url","b.basededato","b.puerto","b.puerto","b.usuario","b.clave","b.observacion","c.nombre","a.minutos"); 
		$filter->db->select($select);
		$filter->db->from("tiketc AS a");   
		$filter->db->join("tiketconec AS b","a.sucursal=b.id");
		$filter->db->join("scli AS c","b.cliente=c.cliente");

		$filter->fechad = new dateonlyField("Fecha Desde", "fechad",'d/m/Y');
		$filter->fechad->clause  ="where";
		$filter->fechad->db_name ="estampa";
		$filter->fechad->operator=">=";
		$filter->fechad->size=12;
		
		$filter->fechah = new dateonlyField("Fecha Hasta", "fechah",'d/m/Y');
		$filter->fechah->clause="where";
		$filter->fechah->db_name="estampa";
		$filter->fechah->operator="<=";
		$filter->fechah->size=12;
		
		$filter->estado = new dropdownField("Estado", "estado");
		$filter->estado->option("","Todos");
		$filter->estado->options($this->estado);

		$filter->prioridad = new dropdownField("Prioridad", "prioridad");
		$filter->prioridad->option("","Todos");
		$filter->prioridad->options($this->prioridad);

		$filter->contenido = new inputField("Contenido", "contenido");
		//$filter->contenido->clause ="likesensitive";
		//$filter->contenido->append("Sencible a las Mayusc&uacute;las");
		
		$filter->asignacion = new dropdownField("Asignacion","asignacion");
		$filter->asignacion->option("","Todos");
		$filter->asignacion->options("SELECT codigo, codigo as value FROM tiketservi");
		$filter->asignacion->style = "width:150px";
		
		$filter->cliente = new inputField("Cliente","cliente");
		$filter->cliente->size=20;
		$filter->cliente->db_name="c.cliente";
		$filter->cliente->append($boton);
		
		$filter->idt = new inputField("Numero","idt");
		$filter->idt->size=20;
		$filter->idt->db_name="idt";

		$filter->buttons("reset","search");
		$filter->build();

		$grid = new DataGrid2("Lista de Control de Tiket");
		$grid->order_by("a.id","desc");
		$grid->per_page = 20;
		$link=anchor("supervisor/tiketc/dataedit/modify/<#idm#>", "<#idm#>");

		$grid->column("Id",$link);
		$grid->column("Numero tiket","idt");
		$grid->column("Fecha de Ingreso","<dbdate_to_human><#estampa#>|d/m/Y h:m:s</dbdate_to_human>");
		$grid->column("Cliente","nombre");
		$grid->column("Usuario","usuarios");
		$grid->column("Contenido","contenido");
		$grid->column("Prioridad","prioridad");
		$grid->column("Estado","estado");
		$grid->column("Asignacion","asignacion");
		$grid->column("Tiempo","minutos");
    //$grid->column("URL","url");
    //$resp=anchor("http://merida.matloca.com/proteoerp/supervisor/tiket/dataedit/pertenece/<#idt#>/create"", 'IR');
		$resp = '<a href="http://<#url#>/proteoerp/supervisor/tiket/dataedit/pertenece/<#idt#>/create">Ir</a>';
		//$resp='http://merida.matloca.com/proteoerp/supervisor/tiket/dataedit/pertenece/67/create';
		$grid->column("Responder",$resp);
		
		$grid->totalizar('minutos');
		//$grid->add("supervisor/tiketc/dataedit/create");
		$grid->build();
		//echo $grid->db->last_query();

		$data['content'] =$filter->output.$grid->output;
		$data["head"]    = $this->rapyd->get_head();
		$data['title']   =' Control de Tiket ';
		$this->load->view('view_ventanas', $data);
	}

	function dataedit(){ 
		$parametros = $this->uri->uri_to_assoc(4);
		$this->rapyd->load("dataedit");
		
		$mSCLId=array(
			'tabla'   =>'tiketservi',
			'columnas'=>array(
			'codigo' =>'Código',
			'nombre'=>'Nombre'),
			'filtro'  =>array('codigo'=>'Código','nombre'=>'Nombre'),
			'retornar'=>array('codigo'=>'asignacion'),
			'titulo'  =>'Buscar Tecnico');
			
		$boton =$this->datasis->modbus($mSCLId);

		$edit = new DataEdit("Tiket", "tiketc");
		
		$edit->idt = new inputField("Numero de Tiket","idt");
		$edit->idt->size=10;

		$edit->contenido = new textareaField("Contenido", "contenido");
		$edit->contenido->rule = "required";
		$edit->contenido->rows = 6;
		$edit->contenido->cols = 90;

		$edit->padre = new inputField(" ", "padre");
		$edit->padre->style='display: none;';
		$edit->padre->type='hidden';
		$edit->padre->when= array("create");

		//if(!array_key_exists('pertenece',$parametros)) {

		$edit->back_uri="supervisor/tiketc/filteredgrid";
		$edit->padre->insertValue='S';

		$edit->prioridad = new dropdownField("Prioridad", "prioridad");
		$edit->prioridad->options($this->prioridad);
		$edit->prioridad->insertValue=5;

		$edit->estado = new dropdownField("Estado", "estado");
		$edit->estado->option("N","N");
		$edit->estado->option("R","R");
		$edit->estado->style="width:50px";

		//	$edit->estado = new inputField(" ", "estado");
		//	$edit->estado->style='display: none;';
		//	$edit->estado->type='hidden';
		//	$edit->estado->when= array("create");
		//	$edit->estado->insertValue='N';
		//}else{
		//	$edit->back_uri="supervisor/tiketc/ver/".$parametros['pertenece'];
		//	$edit->padre->insertValue='N';

		//$edit->pertenece = new inputField(" ", "pertenece");
		//$edit->pertenece->option("Muy Alta","Muy Alta");
		//$edit->pertenece->option("Alta","Alta");
		//$edit->pertenece->option("Media","Media");
		//$edit->pertenece->option("Baja","Baja");
    //$edit->pertenece->option("Muy baja","Muy baja");
    //$edit->pertenece->style="width:100px";

		$edit->asignacion = new inputField("Asignacion","asignacion");
		$edit->asignacion->size=20;
		$edit->asignacion->append($boton);
		
		$edit->catalogado = new dropdownField("Catalogado","catalogado");
		$edit->catalogado->option("","Seleccione una Opcion");
		$edit->catalogado->option("DF","Defecto en el Sistema");
		$edit->catalogado->option("NR","Nuevo Requerimiento");                                               
		$edit->catalogado->option("EP","Error en Procedimiento");
		$edit->catalogado->option("ES","En Proceso");
		$edit->catalogado->style="width:200px";
		
		$edit->minutos = new dropdownField("Minutos","minutos");
		$edit->minutos->option("0","0");                           
		$edit->minutos->options("SELECT minutos,minutos as value FROM tiempo ORDER BY minutos");
		$edit->minutos->style="width:50px";
		$edit->minutos->append('   hh:mm');
		
		$edit->buttons("modify", "save", "undo", "delete",'back');
		$edit->build();

		$data['content'] =$edit->output;
		$data["head"]    = $this->rapyd->get_head();
		$data['title']   =' Crontrol de Tiket ';
		$this->load->view('view_ventanas', $data);
	}
	
	function estapriori($status,$id=NULL){ 
		$this->rapyd->load("dataedit");

		$edit = new DataEdit("tiketc", "tiketc");
		$edit->post_process("update","_post_update");
		$edit->back_url = site_url("supervisor/tiketc/ver/$id");

		$edit->prioridad = new dropdownField("Prioridad", "prioridad");
		$edit->prioridad->options($this->prioridad);

		$edit->estado = new dropdownField("Estado", "estado");
		$edit->estado->options($this->estado);

		$edit->buttons("modify", "save", "undo", "back");
		$edit->build();

		$data['content'] =$edit->output;
		$data["head"]    = $this->rapyd->get_head();
		$data['title']   =' Cambiar estado o prioridad ';
		$this->load->view('view_ventanas', $data);
	}

	function ver($id=NULL){ 
		if(empty($id)) redirect("supervisor/tiketc/filteredgrid");
		$this->rapyd->load("datatable");
		$query = $this->db->query("SELECT prioridad,estado FROM tiketc WHERE $id=$id");
		$estado=$prioridad='';
		if ($query->num_rows() > 0){
			$row = $query->row();
			$prioridad = $row->prioridad;
			$estado    = $row->estado;
		} 
		$link=($this->datasis->puede(908001))? anchor('/supervisor/tiketc/dataedit/delete/<#id#>','borrar'):'';

		$table = new DataTable(null);
		$table->cell_attributes = 'style="vertical-align:middle;"';
		$select=array("usuario","contenido","prioridad","estado","estampa","id","padre");

		$table->db->select($select);
		$table->db->from("tiketc");
		$table->db->where("id=$id OR pertenece=$id");
		$table->db->orderby("id");

		$table->per_row  = 1;
		$table->per_page = 50;
		$table->cell_template = "<div class='marco1' ><#contenido#><br><b class='mininegro'>&nbsp;<dbdate_to_human><#estampa#>|d/m/Y h:m:s</dbdate_to_human> Usuario: <#usuario#> $link</b></div><br>";
		$table->build();

		$prop=array('type'=>'button','value'=>'Agregar','name'=>'mas'  ,'onclick' => "javascript:window.location='".site_url("supervisor/tiketc/dataedit/pertenece/$id/create")."'");
		$form=form_input($prop);

		$prop2=array('type'=>'button','value'=>'Cambiar estado o prioridad','name'=>'mas'  ,'onclick' => "javascript:window.location='".site_url("supervisor/tiketc/estapriori/modify/$id")."'");
		$form2=form_input($prop2);

		$prop3=array('type'=>'button','value'=>'Regresar','name'=>'mas'  ,'onclick' => "javascript:window.location='".site_url("supervisor/tiketc/filteredgrid")."'");
		$form3=form_input($prop3);

		$data['content'] = $table->output.$form.$form2.$form3;
		$data["head"]    = $this->rapyd->get_head().style('marcos.css').style('estilos.css');
		$data['title']   = " tiketc N&uacute;mero: $id  Prioridad: <b>".$this->prioridad[$prioridad]."</b>, Estado: <b>".$this->estado[$estado]."</b><br>";
		$this->load->view('view_ventanas', $data);
	}

function instalar(){
		$mSQL="CREATE TABLE `tiketc` (
		  `id` bigint(20) unsigned NOT NULL auto_increment,
		  `padre` char(1) default NULL,
		  `pertenece` bigint(20) unsigned default NULL,
		  `prioridad` smallint(5) unsigned default NULL,
		  `usuario` varchar(50) default NULL,
		  `contenido` text,
		  `estampa` timestamp NOT NULL default CURRENT_TIMESTAMP on update CURRENT_TIMESTAMP,
		  `actualizado` timestamp NULL default NULL,
		  `estado` char(1) default 'N',
		  PRIMARY KEY  (`id`),
		  KEY `id` (`id`)
		) ENGINE=MyISAM AUTO_INCREMENT=10 DEFAULT CHARSET=utf8"; 
		$mSQL2="CREATE TABLE `tiempo` (`hora` INT, `minutos` INT, `id` INT AUTO_INCREMENT, PRIMARY KEY(`id`), INDEX(`id`))";
		$this->db->simple_query($mSQL);
	}
	function tiempo(){
		$mSQL="CREATE TABLE `tiempo` (`minutos` INT, `hora` INT AUTO_INCREMENT, PRIMARY KEY(`id`), INDEX(`id`))";
		$this->db->simple_query($mSQL);
	}
}
?>
