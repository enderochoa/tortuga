<?php require_once(BASEPATH.'application/controllers/validaciones.php');
class repomenu extends validaciones {
	var $genesal=true;

	function repomenu(){
		parent::Controller(); 
		$this->load->library('rapyd');
		//$this->datasis->modulo_id(307,1);
	}

	function index(){
		redirect("supervisor/repomenu/filteredgrid");
	}

	function filteredgrid(){
		$this->rapyd->load('datafilter','datagrid');
		$this->rapyd->uri->keep_persistence();

		function llink($nombre,$alternativo,$modulo){
			if(!empty($nombre))
				$uri  = anchor("supervisor/repomenu/dataedit/show/$nombre/$modulo",$nombre);
			else
				$uri  = anchor("supervisor/repomenu/dataedit/$alternativo/create",$alternativo);
			return $uri;
		}

		function ractivo($nombre,$activo,$modulo){
			if(!empty($activo)){
				$bandera= ($activo=='S') ? 1: 0;
				$retorna = form_checkbox("$nombre|$modulo", 'accept', $bandera);
			}else{
				$retorna  = 'NI';
			}
			return $retorna ;
		}

		$filter = new DataFilter('Filtro por Menu de Reportes');
		$select=array('b.nombre AS alternativo','a.nombre','a.modulo','a.titulo','a.mensaje','a.activo','b.reporte','b.proteo','b.harbour');
		$filter->db->select($select);
		$filter->db->from('intrarepo AS a');
		$filter->db->join('reportes AS b','a.nombre=b.nombre','RIGHT');

		$filter->nombre = new inputField('Nombre', 'nombre');
		$filter->nombre->db_name='b.nombre';
		$filter->nombre->size=20;

		$filter->modulo = new dropdownField('Modulo','modulo');
		$filter->modulo->option('','Todos');
		$filter->modulo->options('SELECT modulo,modulo as value FROM intrarepo GROUP BY modulo');
		$filter->modulo->style='width:130px';

		$filter->titulo = new inputField('T&iacute;tulo','titulo');
		$filter->titulo->size=30;

		$filter->activo = new dropdownField('Activo','activo');
		$filter->activo->option('','Todos');
		$filter->activo->option('S','Si');
		$filter->activo->option('N','No');
		$filter->activo->style='width:80px';

		$filter->proteo = new inputField('Contenido','proteo');
		$filter->proteo->size=40;
		$filter->proteo->db_name='b.proteo';

		$filter->buttons('reset','search');
		$filter->build();

		$uri1 = anchor('supervisor/repomenu/reporte/modify/<#alternativo#>/' ,'Editar');
		$uri2 = anchor('supervisor/repomenu/rdatasis/modify/<#alternativo#>/','Editar');
		$uri3 = anchor('supervisor/repomenu/rharbour/modify/<#alternativo#>/','Editar');
		$uri5 = anchor('supervisor/repomenu/rtcpdf/modify/<#alternativo#>/','Editar');

		$atts = array(
		  'width'      => '800',
		  'height'     => '600',
		  'scrollbars' => 'yes',
		  'status'     => 'yes',
		  'resizable'  => 'yes',
		  'screenx'    => '0',
		  'screeny'    => '0'
		);

		$uri4=anchor_popup('reportes/ver/<#alternativo#>/<#modulo#>', 'Probar', $atts);

		$grid = new DataGrid('Lista de Menu de Reportes');
		$grid->use_function('llink','ractivo');
		$grid->order_by('nombre','asc');
		$grid->per_page = 15;


		$grid->column_orderby('Nombre'            ,'<llink><#nombre#>|<#alternativo#>|<#modulo#></llink>' ,'nombre'       ,'align="left"');
		$grid->column_orderby('Modulo'            ,"modulo"                                               ,'modulo'       ,'align="left"');
		$grid->column_orderby('Titulo'            ,"titulo"                                               ,'titulo'       ,'align="left"');
		$grid->column_orderby('Mensaje'           ,"mensaje"                                              ,'mensaje'      ,'align="left"');
		$grid->column_orderby('Activo'            ,"activo"                                               ,'activo'       ,'align="left"');
		$grid->column('Activo'  ,'<ractivo><#alternativo#>|<#activo#>|<#modulo#></ractivo>',"align='center'");
		$grid->column('Tortuga'  ,$uri1);
		$grid->column('Ejecutar',$uri4);
		

		$grid->add('supervisor/repomenu/dataedit/create');
		$grid->build();
		//echo $grid->db->last_query();

		$url=site_url('supervisor/repomenu/cactivo');
		$data['script']='<script type="text/javascript">
		$(document).ready(function() {
			$("form :checkbox").click(function () {
				$.ajax({
					type: "POST",
					url: "'.$url.'",
					data: "codigo="+this.name,
					success: function(msg){
						if (msg==0)
							alert("Ocurrio un problema");
						}
					});
			}).change();
		});
		</script>';
		$data['content'] = $filter->output.'<form>'.$grid->output.'</form>';
		$data['title']   = 'Menu de Reportes';
		$data['head']    = script("jquery.pack.js").$this->rapyd->get_head();
		$this->load->view('view_ventanas', $data);
	}

	function dataedit($nombre){
		$this->rapyd->load('dataedit');

		$edit = new DataEdit('Menu de Reportes', 'intrarepo');
		$edit->back_url = site_url('supervisor/repomenu/filteredgrid');
		$edit->post_process('insert','_post_insert');
		//$edit->post_process('delete','_post_delete');

		$edit->nombre = new inputField('Nombre','nombre');
		$edit->nombre->mode="autohide";
		$edit->nombre->rule= "strtoupper|required";
		$edit->nombre->size = 20;
		if($nombre!='create') $edit->nombre->insertValue = $nombre;

		$edit->modulo = new inputField("modulo","modulo");
		$edit->modulo->size =20;
		$edit->modulo->rule= "strtoupper|required";

		$edit->titulo=new inputField("Titulo","titulo");
		$edit->titulo->size =40;

		$edit->mensaje =new inputField("Mensaje", "mensaje");
		$edit->mensaje->size = 50;

		$edit->activo = new dropdownField("Activo","activo");
		$edit->activo->option("S","Si");
		$edit->activo->option("N","No");
		$edit->activo->style='width:60px';

		$edit->buttons("modify", "save", "undo", "delete", "back");
		$edit->build();

		$data['content'] = $edit->output;
		$data['title']   = 'Repomenu';
		$data['head']    = $this->rapyd->get_head();
		$this->load->view('view_ventanas', $data);
	}

	function reporte(){
		$this->rapyd->load('dataedit');
		$this->rapyd->uri->keep_persistence();
		$atts = array(
			'width'      => '800',
			'height'     => '600',
			'scrollbars' => 'yes',
			'status'     => 'yes',
			'resizable'  => 'yes',
			'screenx'    => '0',
			'screeny'    => '0'
		);

		$edit = new DataEdit('', 'reportes');
		$id=$edit->_dataobject->pk['nombre'];
		$uri2=anchor_popup('reportes/ver/'.$id, 'Probar reporte', $atts);
		$uri3=anchor_popup('supervisor/mantenimiento/centinelas', 'Centinela', $atts);
		$edit->title($uri2.' '.$uri3);

		$script='
		$(document).ready(function() {
			$("#proteo").tabby();
			$("#proteo").linedtextarea();

			$("#df1").submit(function(){
			$.post("'.site_url('supervisor/repomenu/gajax_proteo/update/'.$id).'", {nombre: "'.$id.'", proteo: $("#proteo").val()},
				function(data){
					alert("Reporte guardado" + data);
				},
				"application/x-www-form-urlencoded;charset='.$this->config->item('charset').'"
				);
				return false;
			});
		})';

		$edit->script($script,'modify');
		$edit->back_save  =true;
		$edit->back_cancel=true;
		$edit->back_cancel_save=true;
		$edit->back_url = site_url('supervisor/repomenu/filteredgrid');

		$edit->proteo= new textareaField('', 'proteo');
		$edit->proteo->rows =30;
		$edit->proteo->cols =180;
		$edit->proteo->css_class='codepress php linenumbers-on readonly-off';
		//$edit->proteo->when = array('create','modify');

		$edit->buttons('modify', 'save', 'undo', 'delete', 'back');
		$edit->build();

		if($this->genesal){
			$data['content'] = $edit->output;
			$data['title']   = '';
			$data['head']    = $this->rapyd->get_head().script('jquery.js');
			//$data['head']   .= script('codepress/codepress.js');
			$data['head']   .= script('plugins/jquery-linedtextarea.js').script('plugins/jquery.textarea.js').style('jquery-linedtextarea.css');
			$script='$(document).ready(function() {
				$("#proteo").tabby();
				$("#proteo").linedtextarea();
			})';

			$this->load->view('view_ventanas_sola', $data);
		}else{
			echo $edit->error_string;
		}

	}

	function gajax_proteo(){
		header('Content-Type: text/html; '.$this->config->item('charset'));
		$this->genesal=false;
		$nombre=$this->input->post('nombre');
		$proteo=$this->input->post('proteo');

		if($proteo!==false and $nombre!==false){
			if(stripos($this->config->item('charset'), 'utf')===false){
				$_POST['nombre']=utf8_decode($nombre);
				$_POST['proteo']=utf8_decode($proteo);
			}
			$this->reporte();
		}
	}

	function rtcpdf($status,$nombre){
		$this->rapyd->load('dataedit');

		$edit = new DataEdit('Editar TCPDF', 'reportes');
		$edit->back_url = site_url('supervisor/repomenu/filteredgrid');

		$edit->tcpdf= new textareaField('', 'tcpdf');
		$edit->tcpdf->rows =30;
		$edit->tcpdf->cols=130;
		$edit->tcpdf->when = array('create','modify');
		$edit->tcpdf->rule='trim';

		$edit->ttcpdf = new freeField('','free',$this->phpCode('<?php '.$edit->_dataobject->get('tcpdf').' ?>'));
		$edit->ttcpdf->when = array('show');

		$edit->buttons('modify','save', 'undo', 'delete', 'back');
		$edit->build();

		$data['content'] = $edit->output;
		$data['title']   = '<h1>Reporte TCPDF</h1>';
		$data['head']    = $this->rapyd->get_head();
		$this->load->view('view_ventanas_sola', $data);
	}

	function rdatasis($status,$nombre){
		$this->rapyd->load('dataedit');

		$edit = new DataEdit('Reporte DataSIS','reportes');
		$edit->back_url = site_url("supervisor/repomenu/filteredgrid");

		$edit->reporte= new textareaField('', 'reporte');
		$edit->reporte->rows =30;
		$edit->reporte->cols=130;
		$edit->reporte->rule = 'callback_eollw';

		$edit->buttons("modify", "save", "undo", "delete", "back");
		$edit->build();

		$data['content'] = $edit->output;
		$data['title']   = "<h1>Reporte Datasis</h1>";
		$data['head']    = $this->rapyd->get_head();
		$this->load->view('view_ventanas_sola', $data);
	}

	function rharbour($status,$nombre){
		$this->rapyd->load('dataedit');

		$edit = new DataEdit('Reporte DataSIS', "reportes");
		$edit->back_url = site_url("supervisor/repomenu/filteredgrid");

		$edit->reporte= new textareaField("", "harbour");
		$edit->reporte->rows =30;
		$edit->reporte->cols=130;
		$edit->reporte->rule = 'callback_eollw';

		$edit->buttons('modify','save','undo','delete','back');
		$edit->build();

		$data['content'] = $edit->output;
		$data['title']   = "<h1>Reporte Harbour</h1>";
		$data['head']    = $this->rapyd->get_head();
		$this->load->view('view_ventanas_sola', $data);
	}

	function cactivo(){
		$codigo=$this->input->post('codigo');
		if(!empty($codigo)){
			$pk=explode('|',$codigo);
			$mSQL="UPDATE intrarepo SET activo=IF(activo='S','N','S') WHERE nombre='$pk[0]' AND modulo='$pk[1]'";
			echo $this->db->simple_query($mSQL);
		}else{
			echo 0;
		}
	}

	function _post_insert($do){
		$nombre=$do->get('nombre');
		$mSQL="INSERT IGNORE INTO `reportes` (nombre) VALUES ('$nombre')";
		$this->db->simple_query($mSQL);
		logusu('REPOMENU',"CREADO EL REPORTE $nombre");
	}

	function _post_delete($do){
		$nombre=$do->get('nombre');
		$mSQL="DELETE FROM `reportes` WHERE `nombre`='$nombre'";
		$this->db->simple_query($mSQL);
		logusu('REPOMENU',"BORRADO EL REPORTE $nombre");
	}

	function instalar(){
		$mSQL="CREATE TABLE IF NOT EXISTS `intrarepo` (
		  `nombre` varchar(71) NOT NULL default '',
		  `modulo` varchar(10) NOT NULL default '',
		  `titulo` varchar(20) default NULL,
		  `mensaje` varchar(60) default NULL,
		  `activo` char(1) default 'S',
		  PRIMARY KEY  (`nombre`,`modulo`)
		) ENGINE=MyISAM DEFAULT CHARSET=utf8";
		$this->db->simple_query($mSQL);
		$query="ALTER TABLE `intrarepo` CHANGE COLUMN `titulo` `titulo` VARCHAR(100) NULL DEFAULT NULL";
		echo $this->db->simple_query($mSQL);
		$query="ALTER TABLE `intrarepo` CHANGE COLUMN `mensaje` `mensaje` VARCHAR(200) NULL DEFAULT NULL";
		echo $this->db->simple_query($mSQL);
	}
	
	
}
