<?php
class Catalogo extends Controller {
	var $upload_path;
	var $sinv=array(
		  'tabla'   =>'sinv',
		  'columnas'=>array(
		  	'codigo' =>'C&oacute;digo',
		  	'descrip'=>'Descripci&oacute;n'),
		  'filtro'  =>array('codigo' =>'C&oacute;digo','descrip'=>'descrip'),
		  'retornar'=>array('codigo'=>'codigo'),
		  'titulo'  =>'Buscar Art&iacute;culo');
	var $bSINV;

	function Catalogo(){
		parent::Controller(); 
		$this->load->library("rapyd");
		$this->load->library("path");
		$path=new Path();
		$path->setPath($this->config->item('uploads_dir'));
		$path->append('/inventario');
		$this->upload_path =$path->getPath();
	}
 
	function index(){
		$this->datasis->modulo_id(311,1);
		redirect("inventario/catalogo/filteredgrid");
  }

	function filteredgrid(){
		$this->bSINV=$this->datasis->modbus($this->sinv);
		$this->rapyd->load("datafilter2","datagrid");
		//$this->rapyd->uri->keep_persistence();
		
		rapydlib("prototype");

		$filter = new DataFilter2("Filtro por Producto", 'catalogo');
		
		$filter ->codigo = new inputField("C&oacute;digo", "codigo");
		$filter ->codigo->size=15;
		$filter ->codigo->append($this->bSINV);
		
		$filter->buttons("reset","search");
		$filter->build();
		
		$grid = new DataGrid("Lista de Art&iacute;culos");
		$grid->add("inventario/catalogo/dataedit/create");
		$grid->order_by("codigo","asc");
		$grid->per_page = 20;
		$link=anchor('/inventario/catalogo/dataedit/show/<#id#>','<#codigo#>');
		
		$grid->use_function('str_replace');
		$grid->column("C&oacute;digo",$link);
		$grid->column("Fecha","<dbdate_to_human><#estampa#></dbdate_to_human>");

		$grid->build();

		$data['content'] = $filter->output.$grid->output;
		$data["head"]    = script("tabber.js").script("prototype.js").$this->rapyd->get_head().script("scriptaculous.js").script("effects.js");
		$data['title']   = ' Lista de Art&iacute;culos ';
		$this->load->view('view_ventanas', $data);
	}

	function dataedit(){
		$msinv=$this->sinv;
		$msinv['retornar']= array('codigo'=>'mcodigo');
		$msinv['script']  = array('retocod()');

		$this->bSINV = $this->datasis->modbus($msinv);
		$this->rapyd->load("dataedit");
		$data = array(
		   'name'  => 'mcodigo',
		   'id'    => 'mcodigo',
		   'type'  => 'hidden'
		);

		$edit = new DataEdit("Catalogo de Inventario", "catalogo");
		$edit->pre_process("insert","_pre_insert");
		$edit->pre_process("update","_pre_insert");
		$edit->back_url = site_url("inventario/catalogo/filteredgrid");

		$edit->codigo = new inputField("C&oacute;digo", "codigo");
		$edit->codigo->rule = "required|trim";
		$edit->codigo->size = 16;		
		$edit->codigo->maxlength = 15;
		$edit->codigo->append($this->bSINV);
		//$edit->codigo->size=100;

		$edit->contenido = new editorField("Contenido", "contenido");
		$edit->contenido->rule = "required";
		$edit->contenido->rows = 20;
		$edit->contenido->upload_path  = $this->upload_path;
		$edit->contenido->cols=90;
		//$edit->contenido->when = array("create","modify");
		
		$edit->buttons("modify", "save", "undo", "delete", "back");
		$edit->build();
		
		$data['content'] = form_input($data).$edit->output;
		$data['script'] ='<script language="javascript" type="text/javascript">
			function retocod() {
				var mcodigo=document.getElementById("mcodigo");
				var  codigo=document.getElementById("codigo");
				if (codigo.value.length>0)
					codigo.value=codigo.value+";"+mcodigo.value;
				else 
					codigo.value=mcodigo.value;
			}
		</script>';//$table->db->where("codigo='$codigo'");
		$data["head"]    = script("tabber.js").script("prototype.js").$this->rapyd->get_head().script("scriptaculous.js").script("effects.js");
		$data['title']   = ' Catalogos de Inventarios ';
		$this->load->view('view_ventanas', $data);
	}

	function verCatalogo($codigo=NULL){
		$this->rapyd->load("datatable");
		$this->load->library('snoopy');
		$this->load->library('htmlsql');

		$table = new DataTable(null);
		$table->cell_attributes = 'style="vertical-align:middle; text-align: center;"';
		
		$table->db->select(array('nombre','id','comentario'));
		$table->db->from("sinvfot");
		//$table->db->where("codigo='$codigo'");

		$table->per_row = 1;
		$table->per_page = 16;
		$table->cell_template = "<#comentario#>";
		$table->build();

		$data['content'] = $table->output;
		$data['title']   = "";
		$data["head"]   = style("ventanas.css").style("estilos.css").$this->rapyd->get_head();
		$this->load->view('view_ventanas_sola', $data);
	}

	function _pre_insert($do){
		$this->load->library('snoopy');
		$this->load->library('htmlsql');
		$do->set('estampa', date('Ymdhis'));
		$codigos = explode(';',$do->get('codigo'));
		$codigos = array_unique($codigos);
		$nombre  = $do->get('nombre');
		$do->set('codigo', implode(';',$codigos));

		$html    = $do->get('contenido');
		$wsql    = new htmlsql();
		if (!$wsql->connect('string', $html)){
			print 'Error while connecting: ' . $wsql->error;
			exit;
		}

		if (!$wsql->query('SELECT src FROM img')){
			print "Query error: " . $wsql->error;
			exit;
		}

		foreach($wsql->fetch_array() as $row){
			foreach($codigos as $codigo){
				$cant=$this->datasis->dameval("SELECT COUNT(*) FROM sinvfot WHERE codigo='$codigo' AND nombre='$nombre'");
				if($cant==0){
					$id=$this->datasis->dameval("SELECT id FROM sinv WHERE codigo='$codigo'");
					$data = array('codigo' => $codigo, 'nombre' => basename($row['src']), 'ruta' => $this->upload_path.'Image', 'sinv_id' => $id);
					$mSQL = $this->db->insert_string('sinvfot', $data);
					$mSQL=str_replace('INSERT INTO','INSERT IGNORE INTO',$mSQL);
					$this->db->simple_query($mSQL);
				}
			}
		}
	}

	function instalar(){
		$mSQL='CREATE TABLE `catalogo` (
		  `id` int(10) unsigned NOT NULL auto_increment,
		  `codigo` varchar(200) default NULL,
		  `nombre` varchar(50) default NULL,
		  `contenido` text,
		  `estampa` timestamp NULL default NULL,
		  PRIMARY KEY  (`id`),
		  UNIQUE KEY `id` (`id`),
		  KEY `id_2` (`id`,`codigo`)
		) ENGINE=MyISAM AUTO_INCREMENT=2 DEFAULT CHARSET=utf8';
		$this->db->simple_query($mSQL);
	}
}
?>
