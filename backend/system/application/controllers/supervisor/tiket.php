<?php
class Tiket extends Controller {

	var $estado;
 	var $prioridad;
 	var $modulo;
                    
	function Tiket(){ 
		parent::Controller(); 
		$this->load->library("rapyd");
		$this->load->library("menues");
		$this->modulo=92;
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
		redirect("supervisor/tiket/filteredgrid");
	}

	function filteredgrid(){
		$this->datasis->modulo_id($this->modulo,1);
		$this->rapyd->load("datafilter2","datagrid");
		$this->rapyd->uri->keep_persistence();
 		$modbus=array(
			'tabla'   =>'usuario',
			'columnas'=>array(
				'us_codigo' =>'C&oacute;digo',
				'us_nombre'=>'Nombre'),
			'filtro'  =>array('us_nombre'=>'nombre'),
			'retornar'=>array('us_codigo'=>'usuario'),
			'titulo'  =>'Buscar Usuario');
			
			

		$filter = new DataFilter("");
		$select=array("id","estampa","usuario","contenido","prioridad","IF(estado='N','Nuevo',IF(estado='R','Resuelto',IF(estado='P','Pendiente','En Proceso')))as estado","estampa","id","actualizado");

		$filter->db->select($select);
		$filter->db->from('tiket');
		$filter->db->orderby('estampa','desc');
		$filter->db->where('padre',"S");

		$filter->estampa = new dateonlyField("Fecha", "estampa");
	//	$filter->estampa->dbformat = "d/m/Y h:m:s";
		$filter->estampa->clause  ="where";
		$filter->estampa->operator="=";
		//$filter->estampa->insertValue = date("Y-m-d");

		$filter->estado = new dropdownField("Estado", "estado");
		$filter->estado->option("","Todos");
		$filter->estado->options($this->estado);

		$filter->prioridad = new dropdownField("Prioridad", "prioridad");
		$filter->prioridad->option("","Todos");
		$filter->prioridad->options($this->prioridad);

		$filter->usuario = new inputField("C&oacute;digo de usuario", "usuario");
		$filter->usuario->size=11;
		$filter->usuario->append($this->datasis->modbus($modbus));

		$filter->contenido = new inputField("Contenido", "contenido");
		//$filter->contenido->clause ="likesensitive";
		//$filter->contenido->append("Sencible a las Mayusc&uacute;las");

		$filter->buttons("reset","search");
		$filter->build();

		$grid = new DataGrid("");
		$grid->per_page = 20;
		$link=anchor("supervisor/tiket/ver/<#id#>", "<#id#>");

		$grid->column_orderby("N&uacute;mero",$link,"id");
		$grid->column_orderby("Fecha de Ingreso","<dbdate_to_human><#estampa#>|d/m/Y h:m:s</dbdate_to_human>","estampa");
		$grid->column_orderby("Actualizado","<dbdate_to_human><#actualizado#>|d/m/Y h:m:s</dbdate_to_human>","actualizado");
		$grid->column_orderby("Usuario","usuario","usuario");
		$grid->column_orderby("Contenido","contenido","contenido");
		$grid->column_orderby("Prioridad","prioridad","prioridad");
		$grid->column_orderby("Estado","estado","estado");
		
		$grid->add("supervisor/tiket/dataedit/create");
		$grid->build();
		//echo $grid->db->last_query();
		
		//$data['content'] =$filter->output.$grid->output;
		$data['filtro']  = $filter->output;
		$data['content'] = $grid->output;
		$data['script'] = script("jquery.js")."\n";
		$data["head"]    = $this->rapyd->get_head();
		$data['title']   =' Control de Tikets ';
		$this->load->view('view_ventanas', $data);
	}

	function dataedit(){ 
		$parametros = $this->uri->uri_to_assoc(4);
		$this->datasis->modulo_id($this->modulo,1);
		$this->rapyd->load("dataedit");

		$edit = new DataEdit("Tiket", "tiket");
		$edit->pre_process('insert','_pre_insert');
		$edit->pre_process('delete','_pre_del');
		$edit->post_process("insert","_post_insert");
		$edit->post_process("update","_post_update");
		$edit->post_process("delete","_post_del");

		$edit->contenido = new textareaField("Contenido", "contenido");
		$edit->contenido->rule = "required";
		$edit->contenido->rows = 6;
		$edit->contenido->cols = 90;

		$edit->padre = new inputField(" ", "padre");
		$edit->padre->style='display: none;';
		$edit->padre->type='hidden';
		$edit->padre->when= array("create");

		if(!array_key_exists('pertenece',$parametros)) {

			//$edit->back_url = site_url("supervisor/tiket/filteredgrid");
			$edit->back_uri="supervisor/tiket/filteredgrid";
			$edit->padre->insertValue='S';

			$edit->prioridad = new dropdownField("Prioridad", "prioridad");
			$edit->prioridad->options($this->prioridad);
			$edit->prioridad->insertValue=5;

			$edit->estado = new inputField(" ", "estado");
			$edit->estado->style='display: none;';
			$edit->estado->type='hidden';
			$edit->estado->when= array("create");
			$edit->estado->insertValue='N';
		}else{
			//$edit->back_url = site_url("supervisor/tiket/ver/").$parametros['pertenece'];
			$edit->back_uri="supervisor/tiket/ver/".$parametros['pertenece'];
			$edit->padre->insertValue='N';

			$edit->pertenece = new inputField(" ", "pertenece");
			$edit->pertenece->style='display: none;';
			$edit->pertenece->type='hidden';
			$edit->pertenece->when= array("create");
			$edit->pertenece->insertValue=$parametros['pertenece'];
		}

		$edit->buttons("modify", "save", "undo", "delete",'back');
		$edit->build();

		$data['content'] =$edit->output;
		$data["head"]    = $this->rapyd->get_head();
		$data['title']   =' Crear Tiket ';
		$this->load->view('view_ventanas', $data);
	}
	
	function estapriori($status,$id=NULL){ 
		$this->rapyd->load("dataedit");
		$this->datasis->modulo_id($this->modulo,1);

		$edit = new DataEdit("Tiket", "tiket");
		$edit->post_process("update","_post_update");
		$edit->back_url = site_url("supervisor/tiket/ver/$id");

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
		$this->datasis->modulo_id($this->modulo,1);
		if(empty($id)) redirect("supervisor/tiket/filteredgrid");
		$this->rapyd->load("datatable");
		$query = $this->db->query("SELECT prioridad,estado FROM tiket WHERE $id=$id");
		$estado=$prioridad='';
		if ($query->num_rows() > 0){
			$row = $query->row();
			$prioridad = $row->prioridad;
			$estado    = $row->estado;
		} 
		$link=($this->datasis->puede(908001))? anchor('/supervisor/tiket/dataedit/delete/<#id#>','borrar'):'';

		$table = new DataTable(null);
		$table->cell_attributes = 'style="vertical-align:middle;"';
		$select=array("usuario","contenido","prioridad","estado","estampa","id","padre","pertenece");

		$table->db->select($select);
		//$table->db->select("usuario,contenido,prioridad,estado,estampa,id,padre,pertenece");
		$table->db->from("tiket");
		//$table->db->where("id",$id or 'pertenece',$id);
		$table->db->where('id',$id);
		$table->db->or_where('pertenece',$id);
		$table->db->orderby("id");
		$this->db->_escape_char='';
		$this->db->_protect_identifiers=false;

		$table->per_row  = 1;
		$table->per_page = 50;
		$table->cell_template = "<div class='marco1' ><#contenido#><br><b class='mininegro'>&nbsp;<dbdate_to_human><#estampa#>|d/m/Y h:m:s</dbdate_to_human> Usuario: <#usuario#> $link</b></div><br>";
		$table->build();
		//echo $table->db->last_query();

		$prop=array('type'=>'button','value'=>'Agregar','name'=>'mas'  ,'onclick' => "javascript:window.location='".site_url("supervisor/tiket/dataedit/pertenece/$id/create")."'");
		$form=form_input($prop);

		$prop2=array('type'=>'button','value'=>'Cambiar estado o prioridad','name'=>'mas'  ,'onclick' => "javascript:window.location='".site_url("supervisor/tiket/estapriori/modify/$id")."'");
		$form2=form_input($prop2);

		$prop3=array('type'=>'button','value'=>'Regresar','name'=>'mas'  ,'onclick' => "javascript:window.location='".site_url("supervisor/tiket/filteredgrid")."'");
		$form3=form_input($prop3);

		$data['content'] = $table->output.$form.$form2.$form3;
		$data["head"]    = $this->rapyd->get_head().style('marcos.css').style('estilos.css');
		$data['title']   = " Tiket N&uacute;mero: $id  Prioridad: <b>".$this->prioridad[$prioridad]."</b>, Estado: <b>".$this->estado[$estado]."</b><br>";
		$this->load->view('view_ventanas', $data);
	}

	function _post_insert($do) {
		$pertenece=$do->get('pertenece');
		$mSQL="UPDATE tiket SET estado='P', actualizado=NOW() WHERE id=$pertenece";
		$this->db->simple_query($mSQL);
	}

	function _post_update($do) {
		$pertenece=$do->get('pertenece');
		if(empty($pertenece)) $pertenece=$do->get('id');
		$mSQL="UPDATE tiket SET actualizado=NOW() WHERE id=$pertenece";
		$this->db->simple_query($mSQL);
	}

	function _pre_del($do) {
		$retorno=$this->datasis->puede(908001);
		return $retorno;
	}
	function _pre_insert($do) {
		$do->set('usuario', $this->session->userdata('usuario'));
	}

	function _post_del($do){
		$numero=$do->get('id');
		$sql = "DELETE FROM tiket WHERE pertenece=$numero";
		$this->db->query($sql);
	}
	function traertiket($codigoc=null){
		$this->datasis->modulo_id($this->modulo,1);
		$this->load->helper('url');
		if(empty($codigoc)){
			$host=$this->db->query("SELECT cliente,CONCAT_WS('/',url,sistema) AS valor,id FROM tiketconec");
		}else{
			$host=$this->db->query("SELECT cliente,CONCAT_WS('/',url,sistema) AS valor,id FROM tiketconec WHERE cliente='$codigoc'");
		}
		$row = $host->row();
		foreach($host->result() as  $row){
		
			$valor=$row->valor;
			$sucursal=$row->id;
			$cliente=$row->cliente;
			
			$server_url = "$valor/rpcserver";
			//$server_url =reduce_double_slashes($server_url);
			
			//$server_url = site_url('rpcserver');
			//$server_url = "http://192.168.0.99".$server_url;
			echo '<pre>';
			echo '-----'.$server_url;
			echo '</pre>';
			
			$this->load->library('xmlrpc');
			
			$this->xmlrpc->server($server_url, 80);
			$this->xmlrpc->method('ttiket');
			
			$fechad=$this->datasis->dameval("SELECT MAX(a.estampa) FROM tiketc AS a JOIN tiketconec   AS b  ON a.sucursal=b.id  WHERE b.cliente='$cliente'");
			IF (empty($fechad))$fechad=date("Ymd");
			$request = array($fechad);
			$this->xmlrpc->request($request);
			
			if (!$this->xmlrpc->send_request()){
				echo $this->xmlrpc->display_error();
			}
			else
			{
				$respuesta=$this->xmlrpc->display_response();
				foreach($respuesta AS $res){				
					$arr=unserialize($res);
				  $arr1="'".implode("','",$arr)."'";
					$mSQL=$this->db->query("INSERT IGNORE INTO tiketc (id,sucursal,asignacion,idt,padre,pertenece,prioridad,usuario,contenido,estampa,actualizado,estado) VALUES ('0 id','$sucursal','KATHI',$arr1)");
					//echo '<pre>';
					//print_r($arr);
					//echo '</pre>';
					
				}
			} 
		}
		//Redirect("supervisor/tiketc/filteredgrid");
	}
	function prueb(){
		$this->datasis->modulo_id($this->modulo,1);
		$this->load->helper('url');
					
			$server_url = site_url('rpcserver');
			$server_url = "http://briroca.fdns.net".$server_url;
			
			$this->load->library('xmlrpc');
			
			$this->xmlrpc->server($server_url, 80);
			$this->xmlrpc->method('prueba');
			
			$request=array('0');
			$this->xmlrpc->request($request);
			
			if (!$this->xmlrpc->send_request()){
				echo $this->xmlrpc->display_error();
			}
			else
			{
				$respuesta=$this->xmlrpc->display_response();
				echo '<pre>';
				print_r ($respuesta);			
				echo '</pre>';
			} 
	}
function traer(){
			$this->datasis->modulo_id($this->modulo,1);
		//$this->datasis->modulo_id(11D,1);
		$this->rapyd->load("dataform","datatable",'datagrid');
		$this->load->library('table');

		$scli=array(
	  'tabla'   =>'scli',
	  'columnas'=>array(
		'cliente' =>'C&oacute;digo Cliente',
		'nombre'  =>'Nombre',
		'contacto'=>'Contacto'),
	  'filtro'  =>array('cliente'=>'C&oacute;digo Cliente','nombre'=>'Nombre'),
	  'retornar'=>array('cliente'=>'cliente'),
	  'titulo'  =>'Buscar Cliente');
		
		$boton=$this->datasis->modbus($scli);
			
		$filter = new DataForm('supervisor/tiketrpc/tiket/process');
		$filter->title('Filtro de fecha');
				
		//$filter->fechad = new dateonlyField("Fecha Desde", "fechad",'Ymd');
		//$filter->fechad->insertValue = date("Y-m-d");
		//$filter->fechad->size=12;
		
		$filter->cliente = new inputField("Cliente", "cliente");
    $filter->cliente->size = 15;
		$filter->cliente->append($boton);
				
		//$filter->button("btnsubmit", "Consultar", '', $position="BL");
	  $filter->button("btnsubmit", "Traer Tikets", form2uri(site_url("/supervisor/tiket/traertiket"),array('cliente')), $position="BL");//
	  //$filter->button("btnsubmit", "Traer Tikets", form2uri(site_url("/supervisor/tiket/traertiket"), $position="BL");//    
		$filter->build_form();
 		
 		$data=array();
		$mSQL="SELECT a.id,a.cliente,a.ubicacion,a.url,a.basededato,a.puerto,a.usuario,a.clave,a.observacion, b.nombre FROM tiketconec AS a JOIN scli AS b ON a.cliente=b.cliente WHERE url REGEXP '^([[:alnum:]]+\.{0,1})+$' ORDER BY id";

		$query = $this->db->query($mSQL);	
		if ($query->num_rows() > 0){
			foreach ($query->result_array() as $row){
				$data[]=$row;
			}
		}
		$grid = new DataGrid("Clientes",$data);
		
		$grid->column("Cliente"    , '<b><#nombre#></b>'); 
		$grid->column("URL"        , 'url');
			
		$grid->build(); 
		
		$data['content'] = $filter->output.$grid->output;
		$data['title']   = " Traer tikets de clientes ";
		$data["head"]    = $this->rapyd->get_head().script("jquery.js").script("plugins/jquery.json.min.js");
		$this->load->view('view_ventanas', $data);
	}
	function instalar(){
		$mSQL="CREATE TABLE `tiket` (
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
		$this->db->simple_query($mSQL);
	}
}
?>
