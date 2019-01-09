<?php
class Bitacora extends Controller {

	function Bitacora(){
		parent::Controller(); 
		$this->load->library("rapyd");
		$this->load->library("menues");
		//I use THISFILE, instead __FILE__ to prevent some documented php-bugs with higlight_syntax()&__FILE__
		define ("THISFILE",   APPPATH."controllers/ventas/". $this->uri->segment(2).EXT);
		$this->datasis->modulo_id(100,1);
	}

	function index(){ 
		redirect("supervisor/bitacora/filteredgrid");
	}

	function filteredgrid(){

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

		$atts = array(
		              'width'      => '800',
		              'height'     => '600',
		              'scrollbars' => 'yes',
		              'status'     => 'yes',
		              'resizable'  => 'yes',
		              'screenx'    => '0',
		              'screeny'    => '0'
		            );
		
		$link=anchor_popup('supervisor/bitacora/resumen', 'Ver Promedio de &eacute;xitos', $atts);

		$filter = new DataFilter2("Filtro de Bit&aacute;cora ($link)");
		$select=array("actividad","fecha","hora","nombre","comentario","actividad","id",'evaluacion', "if(revisado='P','Pendiente',if(revisado='B','Bueno',if(revisado='C','Consulta','Fallo'))) revisado");
		
		$filter->db->select($select);
		$filter->db->from('bitacora');
		$filter->db->orderby('fecha AND hora','desc');
		
		$filter->fecha = new dateonlyField("Fecha", "fecha");
		$filter->fecha->clause  ="where";
		$filter->fecha->operator="=";
		$filter->fecha->insertValue = date("Y-m-d");
		
		$filter->revisado = new dropdownField("Revisado", "revisado");
		$filter->revisado->option("","Todos");
		$filter->revisado->option("P","Pendiente");
		$filter->revisado->option("F","Fallos");
		$filter->revisado->option("B","Buenos"); 
		$filter->revisado->option("C","Consulta"); 
				
		$filter->usuario = new inputField("C&oacute;digo de usuario", "usuario");
		$filter->usuario->size=11;
		$filter->usuario->append($this->datasis->modbus($modbus));

		$filter->actividad = new inputField("Actividad", "actividad");
		$filter->actividad->clause ="likesensitive";
		$filter->actividad->append("Sencible a las Mayusc&uacute;las");
		
		$filter->buttons("reset","search");
		$filter->build();
		$uri = "supervisor/bitacora/dataedit/show/<#id#>";

		$grid = new DataGrid("Lista de Bitacora");
		$grid->order_by("fecha","desc");
		$grid->per_page = 20;
		$link=anchor($uri, "<dbdate_to_human><#fecha#></dbdate_to_human>");

		$grid->column("Fecha",$link);
		$grid->column("Hora","hora");
		$grid->column("Nombre","nombre");
		$grid->column("Actividad realizada","actividad");
		$grid->column("Resultado","evaluacion");
		$grid->column("Revisado","revisado");
		
		$grid->add("supervisor/bitacora/dataedit/create");
		$grid->build();

		$data["crud"] = $filter->output.$grid->output;
		$data["titulo"] = 'Bit&aacute;cora de Bitacora';
		
		//echo $filter->db->last_query();
		
		$data['content'] =$filter->output.$grid->output;
		$data["head"]    = $this->rapyd->get_head();
		$data['title']   =' Control de Bit&aacute;cora ';
		$this->load->view('view_ventanas', $data);
	}

	function dataedit(){ 
		$this->rapyd->load("dataedit");

		$edit = new DataEdit("Bitacora", "bitacora");
		
		$edit->_dataobject->db->set('nombre' , $this->session->userdata('nombre') );
		$edit->_dataobject->db->set('usuario', $this->session->userdata('usuario'));
		$edit->_dataobject->db->set('hora'   , 'CURRENT_TIME()', FALSE);
		$edit->_dataobject->db->set('fecha'  , 'NOW()', FALSE);
			
		$edit->back_url = site_url("supervisor/bitacora/filteredgrid");
		
		$edit->actividad = new textareaField("Activdad Realizada", "actividad");
		$edit->actividad->rule = "required";
		$edit->actividad->rows = 6;
		$edit->actividad->cols=90;
		
		if ($edit->_status=='show'){
			$edit->fecha = new dateonlyField("Fecha","fecha", "d/m/Y");
			$edit->fecha->when=array('show');
			$edit->fecha->mode='readonly';
			
			$edit->usuario = new inputField("Autor", "usuario");
			$edit->usuario->size = 90;
			$edit->usuario->when=array('show');
			$edit->usuario->mode='readonly';
		}
		
		$edit->comentario = new textareaField("Comentario", "comentario");
		//$edit->comentario->rule = "required";
		$edit->comentario->rows = 4;
		$edit->comentario->cols=90;
		
		$edit->evaluacion = new textareaField("Resultados", "evaluacion");
		$edit->evaluacion->rows = 6;
		$edit->evaluacion->cols=90;
		$edit->evaluacion->when=array('show');
		
		if($this->datasis->puede(907001)){
			$edit->evaluacion->when=array("show","create","modify");
			
			$edit->revisado = new dropdownField("Revisado", "revisado");
			$edit->revisado->option("P","Pendiente");
			$edit->revisado->option("F","Fallos");
			$edit->revisado->option("B","Buenos"); 
			$edit->revisado->option("C","Consulta"); 
		}
		
		$edit->buttons("modify", "save", "undo", "delete", "back");
		$edit->build();
		
		$acti=new myiframeField('acti_repo', '/supervisor/bitacora/actividad/'.$edit->_status.'/'.$this->uri->segment(5),true,"300","auto","0");
		$acti->status='show';
		$acti->build();
		
		$data['content'] =$edit->output.$acti->output;
		$data["head"]    = $this->rapyd->get_head();
		$data['title']   =' Crear registro en Bitacora ';
		$this->load->view('view_ventanas', $data);
	}
	function actividad($nid){
		$this->rapyd->load("datafilter2","datagrid");
		
		$filter = new DataFilter2("Actividades Relacionadas");
		$select=array("actividad","fecha","hora","usuario","comentario","actividad","id",'evaluacion', "if(revisado='P','Pendiente',if(revisado='B','Bueno',if(revisado='C','Consulta','Fallo'))) revisado");
		
		$id=$this->uri->segment(3);
		$filter->db->select($select);
		$filter->db->from('bitacora');
		if($this->uri->segment(4)!='create')
			$filter->db->where("id <> $id");
		$filter->db->orderby('fecha','desc');

		$filter->actividad = new inputField("Actividad", "actividad");
		$filter->actividad->clause ="likesensitive";
		$filter->actividad->append("Sencible a las Mayusc&uacute;las");

		$filter->buttons("search");
		$filter->build();
		$uri = "supervisor/bitacora/dataedit/show/<#id#>";
		$salida=$filter->output;
		if (!empty($filter->actividad->newValue)){
			$grid = new DataGrid("Lista de Bitacora");
			$grid->order_by("fecha","desc");
			$grid->per_page = 20;
    	
			$grid->column("Fecha","<dbdate_to_human><#fecha#></dbdate_to_human>");
			$grid->column("Usuario","usuario");
			$grid->column("Actividad realizada","actividad");
			$grid->column("Comentario","comentario");
			$grid->column("Resultado","evaluacion");
			$grid->column("Revisado","revisado");
			$grid->build();
			$salida.=$grid->output;
		}
		$data['content'] =$salida;
		$data["head"]    = $this->rapyd->get_head();
		$data['title']   ='';
		$this->load->view('view_ventanas_sola', $data);
	}
	function resumen(){
		
		$this->rapyd->load("datafilter","datagrid2");
		
		$grid = new DataGrid2("Res&uacute;men de Bitacora");
		$grid->agrupar('Autor: ', 'usr');
		$grid->db->select(array('UPPER(usuario) AS usr',"if(revisado='P','Pendientes',if(revisado='B','Buenos',if(revisado='C','Consultas','Fallos'))) AS resul",'COUNT(*) AS cant'));
		$grid->db->from('bitacora');
		$grid->db->where("usuario<>'coicoi'");
		$grid->db->groupby('UPPER(usuario),revisado');
		
		$grid->column("Resultado","resul");
		$grid->column("Cantidad","cant");
		$grid->build();
		//echo $grid->db->last_query();
		//echo '<pre>'; print_r($grid->data); echo '</pre>';
		
		$totales=$buenos=$promedio=array();
		foreach($grid->data AS $colum){
			$revisado=substr($colum['resul'], 0, 1);
			if($revisado=='B' OR $revisado=='F' ){
				if (!isset($totales[$colum['usr']])) $totales[$colum['usr']]=0;
				if($revisado=='B') $buenos[$colum['usr']]=$colum['cant'];
				$totales[$colum['usr']]+=$colum['cant'];
			}
		}
		foreach($totales AS $ind=>$tot){
			$promedio[$ind]=round (($buenos[$ind]/$tot)*100,2);
		}
		$out='<table align="center">';
		foreach($promedio AS $usuario=>$prome)
			$out.="<tr><td>$usuario:</td><td> $prome %</td></tr>";
		$out.='</table>';
		
		$data['content'] =$grid->output.'<h3>Promedio de &Eacute;xitos </h3>'.$out;
		$data["head"]    =$this->rapyd->get_head();
		$data['title']   ='';
		$this->load->view('view_ventanas', $data);
	}
	
	function _pre_del($do) {
		$codigo=$do->get('us_codigo');
		if ($codigo==$this->session->userdata('usuario')){
			$do->error_message_ar['pre_del'] = $do->error_message_ar['delete']='No se puede borrar usted mismo';
			return False;
		}
		return True;
	}
	function _pos_del($do){
		$codigo=$do->get('us_codigo');
		$mSQL="DELETE FROM intrasida WHERE usuario='$codigo'";
		$this->db->query($mSQL);
		return True;
	}
	function instalar(){
		$mSQL="CREATE TABLE `bitacora` (
		  `id` bigint(20) unsigned NOT NULL auto_increment,
		  `usuario` varchar(50) default NULL,
		  `nombre` varchar(100) default NULL,
		  `fecha` date default NULL,
		  `hora` time default NULL,
		  `actividad` text,
		  `comentario` text,
		  `revisado` char(1) default 'P',
		  `evaluacion` text,
		  PRIMARY KEY  (`id`)
		) ENGINE=MyISAM AUTO_INCREMENT=524 DEFAULT CHARSET=utf8";
		$this->db->simple_query($mSQL);
	}
}
?>
