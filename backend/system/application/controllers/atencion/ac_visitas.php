<?php
class ac_visitas extends Controller {
	var $titp='Control de Visitas';
	var $tits='Control de Visitas';
	var $url ='atencion/ac_visitas/';
	function ac_visitas(){
		parent::Controller();
		$this->load->library("rapyd");
		$this->datasis->modulo_id(392,1);
	}
	function index(){
		redirect($this->url."filteredgrid");
	}
	function filteredgrid(){
		$this->rapyd->load('datafilter','datagrid');

		$filter = new DataFilter($this->titp);
		$filter->db->select(array('ac_visitas.control','ac_visitas.tipo','ac_visitas.id','ac_visitas.cedula','ac_visitas.estampa','ac_visitas.user','ac_visitas.observa','CONCAT_WS(" ",vi_personas.nombre1,vi_personas.apellido1) nombre'));
		$filter->db->from('ac_visitas');
		$filter->db->join('vi_personas','ac_visitas.cedula=vi_personas.cedula');

		$filter->id = new inputField('Ref.','id');
		$filter->id->rule      ='max_length[11]';
		$filter->id->size      =13;
		$filter->id->maxlength =11;

		$filter->cedula = new inputField('C&eacute;dula','cedula');
		$filter->cedula->rule      ='max_length[11]';
		$filter->cedula->size      =13;
		$filter->cedula->maxlength =11;
		$filter->cedula->db_name   ='ac_visitas.cedula';

		$filter->user = new dropDownField('Usuario','user');
		$filter->user->option('','');
		$filter->user->options("SELECT us_codigo,us_nombre FROM usuario ORDER BY us_nombre");

		$filter->observa = new inputField('Observaci&oacute;n','observa');
		$filter->observa->rule      ='max_length[8]';
		
		$filter->tipo = new dropDownField('Tipo de Solicitud','tipo');
		$filter->tipo->option(""  ,"");
		$filter->tipo->option("S" ,"Solicitud"         );
		$filter->tipo->option("E" ,"Entrevista"        );
		$filter->tipo->option("I" ,"Inspeccci&oacute;n");
		$filter->tipo->option("F" ,"Informaci&oacute;n");
		$filter->tipo->style = "width:150px";
		
		$filter->control = new dropDownField('Control','control');
		$filter->control->option("","");
		$filter->control->option("P" ,"Presidencia"         );
		$filter->control->option("A" ,"Control de Visitas"  );
		$filter->control->style = "width:150px";

		$filter->fechad = new dateOnlyField("Desde", "fechad",'d/m/Y');
		$filter->fechah = new dateOnlyField("Hasta", "fechah",'d/m/Y');
		$filter->fechad->clause  =$filter->fechah->clause="where";
		$filter->fechad->db_name =$filter->fechah->db_name="estampa";
		$filter->fechad->operator=">=";
		$filter->fechah->operator="<=";
		$filter->fechah->group = "Fecha";
		$filter->fechad->group = "Fecha";

		$filter->buttons('reset', 'search');
		$filter->build();

		$uri = anchor($this->url.'dataedit/show/<raencode><#id#></raencode>','<#id#>');

		function stipo($status){
			switch($status){
				case "S":return "Solicitud"         ;
				case "E":return "Entrevista"        ;
				case "I":return "Inspeccci&oacute;n";
				case "F":return "Informaci&oacute;n";
			}
		}
		
		function scontrol($status){
			switch($status){
				case "C" :"Control de Visitas" ;
				case "P" :"Presidencia"        ;
				case "S" :"Dpto Social"        ;
				case "T" :"Dpto Tecnico"       ;
				case "A" :"Administracion"     ;
			}
		}

		$grid = new DataGrid('');
		$grid->order_by('id','desc');
		$grid->per_page = 40;
		$grid->use_function('stipo','scontrol');

		$grid->column_orderby('Ref'                 ,"$uri"                                    ,'id'        ,'align="left"');
		$grid->column_orderby('C&eacute;dula'       ,"cedula"                                  ,'cedula'    ,'align="right"');
		$grid->column_orderby('Nombre'              ,"nombre"                                  ,'nombre'   ,'align="left"');
		$grid->column_orderby('Observaci&oacute;n'  ,"observa"                                 ,'observa'   ,'align="left"');
		$grid->column_orderby('Estampa'             ,"<dbdate_to_human><#estampa#></dbdate_to_human>"        ,'estampa'   ,'align="left"');
		$grid->column_orderby('Usuario'             ,"user"                                    ,'user'      ,'align="left"');
		$grid->column_orderby('Tipo'                ,"<stipo><#tipo#></stipo>"                 ,'tipo'      ,'align="left"');
		$grid->column_orderby('Control'             ,"<scontrol><#control#></scontrol>"        ,'control'   ,'align="left"');

		$grid->add($this->url.'dataedit/create');
		$grid->build();

		$data['filtro']  = $filter->output;
		$data['content'] = $grid->output;
		$data['head']    = $this->rapyd->get_head().script('jquery.js');
		$data['title']   = $this->titp;
		$this->load->view('view_ventanas', $data);

	}
	function dataedit(){
		$this->rapyd->load('dataobject','dataedit');
		
		$script='
		$(".inputnum").numeric(".");
		
		$(document).ready(function(){
			$("#cedula").change(function (){
				
				rcedula=$("#cedula").val();
				$.post("'.site_url('vivienda/vi_personas/damepersona').'",{ cedula:rcedula },function(data){
					persona=jQuery.parseJSON(data);
					if(jQuery.isEmptyObject( persona )){
						agregarpersona(rcedula);
					}else{
						nombrecompleto = persona.nombre1+" "+persona.nombre2+" "+persona.apellido1+" "+persona.apellido2;
						$("#p_nombres"     ).val( nombrecompleto);
						$("#p_nombres_val" ).html( nombrecompleto);
					}
				});			
			});
			
			function agregarpersona(cedula){
				window.open("'.site_url('vivienda/vi_personas/dataedit/create').'/"+cedula,"Persona","height=720,width=1024,scrollbars=yes");
			}
			
		});
		';
		
		$do = new DataObject("ac_visitas");
		$do->pointer('vi_personas'  ,'ac_visitas.cedula=vi_personas.cedula' ,"CONCAT_WS(' ',vi_personas.nombre1,vi_personas.nombre2,vi_personas.apellido1,vi_personas.apellido2) p_nombres");
		$do->db->_escape_char='';
		$do->db->_protect_identifiers=false;
		
		$edit = new DataEdit($this->tits, $do);
		$edit->script($script,"create");
		$edit->script($script,"modify");
		
		$edit->pre_process('insert','_valida');
		$edit->pre_process('update','_valida');

		$edit->back_url = site_url($this->url."filteredgrid");

		$edit->post_process('insert','_post_insert');
		$edit->post_process('update','_post_update');
		$edit->post_process('delete','_post_delete');

		$edit->id = new inputField('id','id');
		$edit->id->rule='max_length[11]|required';
		$edit->id->size =13;
		$edit->id->maxlength =11;
		$edit->id->mode = 'autohide';
		$edit->id->when = array('show');

		$edit->cedula = new inputField('C&eacute;dula','cedula');
		$edit->cedula->rule      ='trim|required';
		$edit->cedula->size      =13;
		$edit->cedula->maxlength =11;
		$edit->cedula->css_class = 'inputnum';	
		
		$edit->p_nombres = new inputField('Nombre','p_nombres');
		$edit->p_nombres->size      =40;
		$edit->p_nombres->maxlength =50;
		$edit->p_nombres->pointer   =true;
		$edit->p_nombres->type      ='inputhidden';
		$edit->p_nombres->in        ='cedula';
		
		$edit->tipo = new dropDownField('Tipo de Visita','tipo');
		$edit->tipo->option("S" ,"Solicitud"         );
		$edit->tipo->option("E" ,"Entrevista"        );
		$edit->tipo->option("I" ,"Inspeccci&oacute;n");
		$edit->tipo->option("F" ,"Informaci&oacute;n");
		$edit->tipo->style = "width:150px";
		
		$edit->observa = new textareaField('Observaci&oacute;n','observa');
		$edit->observa->rule = 'required';
		$edit->observa->cols = 50;
		$edit->observa->rows = 4;
		
		$edit->control = new dropDownField('Control','control');
		$edit->control->option("C" ,"Control de Visitas"  );
		$edit->control->option("P" ,"Presidencia"         );
		$edit->control->option("S" ,"Dpto Social"         );
		$edit->control->option("T" ,"Dpto Tecnico"        );
		$edit->control->option("A" ,"Administracion"      );
		$edit->control->style = "width:150px";

		$edit->estampa = new autoUpdateField('estampa' ,date('Ymd'), date('Ymd'));
		
		$edit->user = new inputField('Usuario','user');
		$edit->user->rule='max_length[50]';
		$edit->user->size =52;
		$edit->user->maxlength =50;
		$edit->user->when = array('show');
		
		

		$edit->buttons('add','modify', 'save', 'undo', 'delete', 'back');
		$edit->build();
		
		$data['content'] = $edit->output;
		$data["head"]    = $this->rapyd->get_head().script('jquery.js').script("plugins/jquery.numeric.pack.js");
		$data['title']   = $this->tits;
		$this->load->view('view_ventanas', $data);

	}
	
	function _valida($do){
		
		$error  ='';
		$numero = $do->get('numero');
		$cedula = $do->get('cedula');
		$cedulae= $this->db->escape($cedula);
		
		$query="SELECT COUNT(*) FROM vi_personas WHERE cedula=$cedulae";
		$c    = $this->datasis->dameval($query);
		
		if(!($c>0))
			$error.="Error. Debe registrar primero a la persona, por el modulo personas";
			
		$user       = $this->session->userdata('usuario');
		$do->set('user',$user);
		
		if(!empty($error)){
			$do->error_message_ar['pre_ins']="<div class='alert'>".$error."</div>";
			$do->error_message_ar['pre_upd']="<div class='alert'>".$error."</div>";
			return false;
		}
	}

	function _post_insert($do){
		$primary =implode(',',$do->pk);
		logusu($do->table,"Creo $this->tits $primary ");
	}
	function _post_update($do){
		$primary =implode(',',$do->pk);
		logusu($do->table,"Modifico $this->tits $primary ");
	}
	function _post_delete($do){
		$primary =implode(',',$do->pk);
		logusu($do->table,"Elimino $this->tits $primary ");
	}

	function instalar(){
		$mSQL="CREATE TABLE `ac_visitas` (
		  `id` int(11) NOT NULL AUTO_INCREMENT,
		  `cedula` int(11) NOT NULL,
		  `user` varchar(50) NOT NULL,
		  `observa` text NOT NULL,
		  `estampa` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
		  PRIMARY KEY (`id`),
		  KEY `cedula` (`cedula`),
		  KEY `user` (`user`)
		) ENGINE=MyISAM AUTO_INCREMENT=4 DEFAULT CHARSET=utf32";
		$this->db->simple_query($mSQL);
		
		$mSQL="ALTER TABLE `ac_visitas` ADD COLUMN `tipo` CHAR(1) NOT NULL AFTER `estampa`";
		$this->db->simple_query($mSQL);
		
		$mSQL="ALTER TABLE `ac_visitas` ADD COLUMN `control` VARCHAR(50) NOT NULL AFTER `tipo`";
		$this->db->simple_query($mSQL);
	}

}
?>
