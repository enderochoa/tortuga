<?php require_once(BASEPATH.'application/controllers/validaciones.php');
class formatos extends validaciones {
	var $genesal=true;

	function formatos(){
		parent::Controller();
		$this->load->library('rapyd');
		//$this->datasis->modulo_id(307,1);
	}

	function index(){
		redirect('supervisor/formatos/filteredgrid');
	}

	function filteredgrid(){
		$this->rapyd->uri->keep_persistence();
		$this->rapyd->load('datafilter','datagrid');

		$filter = new DataFilter('Filtro por Menu de Formatos','formatos');
		$filter->nombre = new inputField('Nombre', 'nombre');
		$filter->nombre->db_name='nombre';
		$filter->nombre->size=20;

		$filter->proteo = new inputField('Contenido HTML','proteo');
		$filter->proteo->size=40;
		$filter->proteo->db_name='proteo';

		$filter->forma = new inputField('Contenido TCPDF','forma');
		$filter->forma->size=40;
		$filter->forma->db_name='forma';

		$filter->forma1 = new inputField('Contenido TCPDF2','forma1');
		$filter->forma1->size=40;
		$filter->forma1->db_name='forma1';

		$filter->buttons('reset','search');
		$filter->build();
		$uri  = anchor('supervisor/formatos/dataedit/show/<#nombre#>'   ,'<#nombre#>');
		$uri1 = anchor('supervisor/formatos/reporte/modify/<#nombre#>/' ,'Editar');
		$uri2 = anchor('supervisor/formatos/rdatasis/modify/<#nombre#>/','Editar');
		$uri3 = anchor('supervisor/formatos/rharbour/modify/<#nombre#>/','Editar');
		$uri4 = anchor('supervisor/formatos/observa/modify/<#nombre#>/' ,'Editar');
		$uri5 = anchor('supervisor/formatos/rtcpdf/modify/<#nombre#>/'  ,'Editar');
		$uri6 = anchor('supervisor/formatos/rtcpdf2/modify/<#nombre#>/'  ,'Editar');

		$grid = new DataGrid('Lista de Menu de Formatos');
		$grid->order_by('nombre','asc');
		$grid->per_page = 15;

		$grid->column('Nombre',    $uri);
		$grid->column('HTML'   ,$uri1);
		//$grid->column('DataSIS'  ,$uri2);
		//$grid->column('Harbour'  ,$uri3);
		$grid->column('TCPDF'    ,$uri5);
		$grid->column('TCPDF2'   ,$uri6);

		$grid->add('supervisor/formatos/dataedit/create');
		$grid->build();
		//echo $grid->db->last_query();

		$url=site_url('supervisor/formatos/cactivo');
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
		$data['title']   = 'Menu de Formatos';
		$data['head']    = $this->rapyd->get_head().script('jquery.pack.js');
		$this->load->view('view_ventanas', $data);
	}

	function observa($nombre){
		$this->rapyd->load('dataedit');

		$edit = new DataEdit('Agregar Observacion','formatos');
		$edit->back_url = site_url('supervisor/formatos/filteredgrid');

		$edit->observa= new textareaField('', 'observa');
		$edit->observa->rows =3;
		$edit->observa->cols=70;

		$edit->buttons("modify", "save", "undo","back");
		$edit->build();

		$data['content'] = $edit->output;
		$data['title']   = '<h1>Observaciones</h1>';
		$data['head']    = $this->rapyd->get_head();
		$this->load->view('view_ventanas', $data);
	}

	function reporte(){
		$this->rapyd->uri->keep_persistence();
		$this->rapyd->load('dataedit');

		$edit = new DataEdit('Proteo', 'formatos');
		$id=$edit->_dataobject->pk['nombre'];
		$script='
		$(document).ready(function() {
			$("#proteo").tabby();
			$("#proteo").linedtextarea();
			$("#df1").submit(function(){
			$.post("'.site_url('supervisor/formatos/gajax_proteo/update/'.$id).'", {nombre: "'.$id.'", proteo: $("#proteo").val()},
				function(data){
					alert("Reporte guardado" + data);
				}
				
				);
				return false;
			});
		});';

		$edit->script($script,'modify');
		$edit->back_save  =true;
		$edit->back_cancel=true;
		$edit->back_cancel_save=true;
		$edit->back_url = site_url('supervisor/formatos/filteredgrid');

		$edit->proteo= new htmlField('', 'proteo');
		$edit->proteo->rows =30;
		$edit->proteo->cols=130;
		$edit->proteo->css_class='codepress php linenumbers-on readonly-off';

		$edit->buttons('modify', 'save', 'undo','back');
		$edit->build();

		if($this->genesal){
			$data['content'] = $edit->output;
			$data['title']   = "Formato '$id'";
			$data['head']    = $this->rapyd->get_head().script('jquery.js');
			//$data['head']   .= script('codepress/codepress.js');
			$data['head']   .= script('plugins/jquery-linedtextarea.js').script('plugins/jquery.textarea.js').style('jquery-linedtextarea.css');

			$this->load->view('view_ventanas_sola', $data);
		}else{
			echo $edit->error_string;
		}
	}

	function gajax_proteo(){
		//header('Content-Type: text/html; '.$this->config->item('charset'));
		$this->genesal=false;
		$nombre=$this->input->post('nombre');
		$proteo=$this->input->post('proteo');

		if($proteo!==false and $nombre!==false){
			if(stripos($this->config->item('charset'), 'utf')===false){
				$_POST['nombre']=($nombre);
				$_POST['proteo']=($proteo);
			}
			$this->reporte();
		}
	}

	function rtcpdf(){
		$this->rapyd->load('dataedit');

		$edit = new DataEdit('Editar TCPDF', 'formatos');
		$id=$edit->_dataobject->pk['nombre'];
		$script='
		$(document).ready(function() {
			$("#forma").tabby();
			$("#forma").linedtextarea();
			$("#df1").submit(function(){
			$.post("'.site_url('supervisor/formatos/gajax_rtcpdf/update/'.$id).'", {nombre: "'.$id.'", forma: $("#forma").val()},
				function(data){
					alert("Reporte guardado" + data);
				},
				"application/x-www-form-urlencoded;charset='.$this->config->item('charset').'"
				);
				return false;
			});
		});';

		$edit->script($script,'modify');
		$edit->back_save  =true;
		$edit->back_cancel=true;
		$edit->back_cancel_save=true;
		$edit->back_url = site_url('supervisor/formatos/filteredgrid');

		$edit->forma= new textareaField('', 'forma');
		$edit->forma->rows=30;
		$edit->forma->cols=130;
		$edit->forma->css_class='codepress php linenumbers-on readonly-off';

		$edit->buttons('modify', 'save', 'undo', 'delete', 'back');
		$edit->build();

		if($this->genesal){
			$data['content'] = $edit->output;
			$data['title']   = '<h1>Reporte TCPDF</h1>';
			$data['head']    = $this->rapyd->get_head().script('jquery.js');
			//$data['head']   .= script('codepress/codepress.js');
			$data['head']   .= script('plugins/jquery-linedtextarea.js').script('plugins/jquery.textarea.js').style('jquery-linedtextarea.css');
			$this->load->view('view_ventanas_sola', $data);
		}else{
			echo $edit->error_string;
		}
	}

	function rtcpdf2(){
		$this->rapyd->load('dataedit');

		$edit = new DataEdit('Editar TCPDF', 'formatos');
		$id=$edit->_dataobject->pk['nombre'];
		$script='
		$(document).ready(function() {
			$("#forma1").tabby();
			$("#forma1").linedtextarea();
			$("#df1").submit(function(){
			$.post("'.site_url('supervisor/formatos/gajax_rtcpdf2/update/'.$id).'", {nombre: "'.$id.'", forma: $("#forma1").val()},
				function(data){
					alert("Reporte guardado" + data);
				},
				"application/x-www-form-urlencoded;charset='.$this->config->item('charset').'"
				);
				return false;
			});
		});';

		$edit->script($script,'modify');
		$edit->back_save  =true;
		$edit->back_cancel=true;
		$edit->back_cancel_save=true;
		$edit->back_url = site_url('supervisor/formatos/filteredgrid');

		$edit->forma1= new textareaField('', 'forma1');
		$edit->forma1->rows=30;
		$edit->forma1->cols=130;
		$edit->forma1->css_class='codepress php linenumbers-on readonly-off';

		$edit->buttons('modify', 'save', 'undo', 'delete', 'back');
		$edit->build();

		if($this->genesal){
			$data['content'] = $edit->output;
			$data['title']   = '<h1>Reporte TCPDF</h1>';
			$data['head']    = $this->rapyd->get_head().script('jquery.js');
			//$data['head']   .= script('codepress/codepress.js');
			$data['head']   .= script('plugins/jquery-linedtextarea.js').script('plugins/jquery.textarea.js').style('jquery-linedtextarea.css');
			$this->load->view('view_ventanas_sola', $data);
		}else{
			echo $edit->error_string;
		}
	}

	function gajax_rtcpdf(){
		header('Content-Type: text/html; '.$this->config->item('charset'));
		$this->genesal=false;
		$nombre=$this->input->post('nombre');
		$forma=$this->input->post('forma');

		if($forma!==false and $nombre!==false){
			$_POST['nombre'] =$nombre;	
			$_POST['forma']  =$forma ;
			$this->rtcpdf();
		}
	}

	function gajax_rtcpdf2(){
		header('Content-Type: text/html; '.$this->config->item('charset'));
		$this->genesal=false;
		$nombre=$this->input->post('nombre');
		$forma1=$this->input->post('forma');
		
		$_POST['nombre'] =$nombre;	
		$_POST['forma1'] =$forma1;

		$this->rtcpdf2();
		
	}

	function dataedit(){
		$this->rapyd->load('dataedit');

		$edit = new DataEdit('Formatos','formatos');
		$edit->back_url = site_url("supervisor/formatos/filteredgrid");

		$edit->nombre = new inputField('Nombre','nombre');
		$edit->nombre->rule= 'strtoupper|required';
		$edit->nombre->size = 20;

		$edit->buttons("modify", "save", "undo", "delete", "back");
		$edit->build();

		$data['content'] = $edit->output;
		$data['title']   = "<h1>Agregar Formatos</h1>";
		$data['head']    = $this->rapyd->get_head();
		$this->load->view('view_ventanas', $data);
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
		logusu('formatos',"CREADO EL REPORTE $nombre");
	}

	function _post_delete($do){
		$nombre=$do->get('nombre');
		$mSQL="DELETE FROM `reportes` WHERE `nombre`='$nombre'";
		$this->db->simple_query($mSQL);
		logusu('formatos',"BORRADO EL REPORTE $nombre");
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
		$this->db->simple_query("ALTER TABLE `formatos`  ADD COLUMN `forma` TEXT NULL ");
		$this->db->simple_query("ALTER TABLE `formatos`  ADD COLUMN `forma1` TEXT NULL ");
		$this->db->simple_query("ALTER TABLE `formatos` 	CHANGE COLUMN `nombre` `nombre` VARCHAR(100) NOT NULL DEFAULT '' FIRST");
	}
}
