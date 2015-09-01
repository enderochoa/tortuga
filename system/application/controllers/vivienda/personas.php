<?php
class personas extends Controller {
	var $titp='Personas';
	var $tits='Persona';
	var $url ='vivienda/personas/';
	function personas(){
		parent::Controller();
		$this->load->library("rapyd");
		$this->datasis->modulo_id(385,1);
	}
	function index(){
		redirect($this->url."filteredgrid");
	}
	function filteredgrid(){
		$this->rapyd->load('datafilter','datagrid');

		$filter = new DataFilter($this->titp);
		
		$filter->db->select(array('nacional','cedula','nombre1','nombre2','apellido1','apellido2','telefono','dir1','dir2','pcedula','mcedula','parroquia.nombre parroquia','ccomunal.nombre ccomunal','ocivh.nombre ocivh'));
		$filter->db->from('personas');
		$filter->db->join('parroquia'   ,'personas.id_parroquia=parroquia.id'                   ,'LEFT');
		$filter->db->join('ccomunal'    ,'personas.id_ccomunal=ccomunal.id'                     ,'LEFT');
		$filter->db->join('ocivh'       ,'personas.id_ocivh=ocivh.id'                           ,'LEFT');

		$filter->nacional = new dropDownField('Nacionalidad'   ,'nacional');
		$filter->nacional->option('' ,'' );
		$filter->nacional->option('V','V');
		$filter->nacional->option('E','E');

		$filter->cedula = new inputField('C&eacute;dula','cedula');
		$filter->cedula->rule      ='max_length[8]|numeric';
		$filter->cedula->size      =10;
		$filter->cedula->maxlength =10;
		$filter->cedula->css_class = 'inputnum';

		$filter->nombre1 = new inputField('Primer Nombre','nombre1');
		$filter->nombre1->rule      ='max_length[50]';
		$filter->nombre1->size      =20;
		$filter->nombre1->maxlength =50;

		$filter->nombre2 = new inputField('Segundo Nombre','nombre2');
		$filter->nombre2->rule      ='max_length[50]';
		$filter->nombre2->size      =20;
		$filter->nombre2->maxlength =50;

		$filter->apellido1 = new inputField('Primer Apellido','apellido1');
		$filter->apellido1->rule      ='max_length[50]';
		$filter->apellido1->size      =20;
		$filter->apellido1->maxlength =50;

		$filter->apellido2 = new inputField('Segundo Apellido','apellido2');
		$filter->apellido2->rule      ='max_length[50]';
		$filter->apellido2->size      =20;
		$filter->apellido2->maxlength =50;

		$filter->telefono = new inputField('Tel&eacute;fono','telefono');
		$filter->telefono->rule      ='max_length[11]';
		$filter->telefono->size      =20;
		$filter->telefono->maxlength =11;

		$filter->id_parroquia = new dropDownField('Parroquia','id_parroquia');
		$filter->id_parroquia->option('','');
		$filter->id_parroquia->options("SELECT id,nombre FROM parroquia ORDER BY nombre");

		$filter->dir1 = new inputField('Direcci&oacute;n 1','dir1');
		$filter->dir1->rule      ='max_length[255]';
		$filter->dir1->size      =40;
		$filter->dir1->maxlength =255;

		$filter->dir2 = new inputField('Direcci&oacute;n 1','dir2');
		$filter->dir2->rule      ='max_length[255]';
		$filter->dir2->size      =40;
		$filter->dir2->maxlength =255;

		$filter->discapacidad = new inputField('Discapacidad','discapacidad');
		$filter->discapacidad->option('' ,'' );
		$filter->discapacidad->option('S','S');
		$filter->discapacidad->option('N','N');

		$filter->pcedula = new inputField('C&eacute;edula Padre','pcedula');
		$filter->pcedula->rule      ='max_length[8]';
		$filter->pcedula->size      =10;
		$filter->pcedula->maxlength =8;

		$filter->mcedula = new inputField('C&eacute;dula Madre','mcedula');
		$filter->mcedula->rule      ='max_length[8]';
		$filter->mcedula->size      =10;
		$filter->mcedula->maxlength =8;

		$filter->id_ccomunal = new inputField('Consejo Comunal','id_ccomunal');
		$filter->id_ccomunal->option('','');
		$filter->id_ccomunal->options("SELECT id,nombre FROM ccomunal ORDER BY nombre");

		$filter->id_ocivh = new inputField('OCIVH','id_ocivh');
		$filter->id_ocivh->option('','');
		$filter->id_ocivh->options("SELECT id,nombre FROM ocivh ORDER BY nombre");

		$filter->buttons('reset', 'search');
		$filter->build();

		$uri = anchor($this->url.'dataedit/show/<raencode><#cedula#></raencode>','<#cedula#>');

		$grid = new DataGrid('');
		$grid->order_by('cedula');
		$grid->per_page = 40;

		
		$grid->column_orderby('Cedula'                           ,"<nformat><#cedula#></nformat>"   ,'cedula'          ,'align="right"');
		//$grid->column_orderby('Nacionalidad'                     ,"$uri"                            ,'nacional'        ,'align="left"');
		$grid->column_orderby('Primer Nombre'                    ,"nombre1"                         ,'nombre1'         ,'align="left"');
		$grid->column_orderby('Segundo Nombre'                   ,"nombre2"                         ,'nombre2'         ,'align="left"');
		$grid->column_orderby('Primer Apellido'                  ,"apellido1"                       ,'apellido1'       ,'align="left"');
		$grid->column_orderby('Segundo Apellido'                 ,"apellido2"                       ,'apellido2'       ,'align="left"');
		$grid->column_orderby('Parroquia'                        ,"parroquia"                       ,'parroquia'       ,'align="right"');
		$grid->column_orderby('Direcci&oacute;n 1'               ,"dir1"                            ,'dir1'            ,'align="left"');
		//$grid->column_orderby('Direcci&oacute;n 2'               ,"dir2"                            ,'dir2'            ,'align="left"');
		$grid->column_orderby('C&eacute;dula Padre'              ,"pcedula"                         ,'pcedula'         ,'align="right"');
		$grid->column_orderby('C&eacute;dula Madre'               ,"mcedula"                         ,'mcedula'         ,'align="right"');
		//$grid->column_orderby('OCIVH'                            ,"ocivh"                           ,'ocivh'           ,'align="right"');
		//$grid->column_orderby('Consejo Comunal'                  ,"ccomunal"                        ,'ccomunal'        ,'align="right"');

		$grid->add($this->url.'dataedit/create');
		$grid->build();

		$data['filtro']  = $filter->output;
		$data['content'] = $grid->output;
		$data['head']    = $this->rapyd->get_head().script('jquery.js');
		$data['title']   = $this->titp;
		$this->load->view('view_ventanas', $data);

	}
	function dataedit(){
		$this->rapyd->load('dataedit');
		
		$script='
			$(".inputnum").numeric(".");
			
			$(document).ready(function(){
				
				$("#cedula").focus();
				function damenombre(){
					cedula2=$("#cedula").val();
					$.post("'.site_url($this->url.'damerne').'",{ cedula:cedula2 },function(data){
						rne=jQuery.parseJSON(data);
						$("#nombre1").val(rne[0].nombre1);
						$("#nombre2").val(rne[0].nombre2);
						$("#apellido1").val(rne[0].apellido1);
						$("#apellido2").val(rne[0].apellido2);
					});
				}
				
				$("#cedula").change(function(){
						damenombre();
				});
				
				$("#mcedula").change(function(){
					mcedula=$("#mcedula").val();
					$.post("'.site_url($this->url.'damerne').'",{ cedula:mcedula },function(data){
						rne=jQuery.parseJSON(data);
						$("#madren").val(rne[0].nombre1+" "+rne[0].nombre2+" "+rne[0].apellido1+" "+rne[0].apellido2);
					});
				});
				
				$("#pcedula").change(function(){
					pcedula=$("#pcedula").val();
					$.post("'.site_url($this->url.'damerne').'",{ cedula:pcedula },function(data){
						rne=jQuery.parseJSON(data);
						$("#padren").val(rne[0].nombre1+" "+rne[0].nombre2+" "+rne[0].apellido1+" "+rne[0].apellido2);
					});
				});
				
				function visiblediscapacidad(){
						discapacidad = $("#discapacidad").val();
						if(discapacidad=="Si"){
								$("#tr_discapacidadd").show();
						}else{
							$("#tr_discapacidadd").hide();
						}
				}
				visiblediscapacidad();
				$("#discapacidad").change(function(){
						visiblediscapacidad();
				});
				
			});
		';

		$edit = new DataEdit($this->tits, 'personas');

		$edit->back_url = site_url($this->url."filteredgrid");

		$edit->post_process('insert','_post_insert');
		$edit->post_process('update','_post_update');
		$edit->post_process('delete','_post_delete');
		
		$edit->script($script,"create");
		$edit->script($script,"modify");

		$edit->nacional = new dropDownField('Nacionalidad','nacional');
		$edit->nacional->option('V','Venezolana');
		$edit->nacional->option('E','Extranjera');
		$edit->nacional->group = "Datos Personales";
		$edit->nacional->style = "width:100px";

		$edit->cedula = new inputField('C&eacute;dula','cedula');
		$edit->cedula->rule      ='trim|max_length[8]|numeric|unique|required';
		$edit->cedula->size      =10;
		$edit->cedula->maxlength =10;
		$edit->cedula->css_class = 'inputnum';
		$edit->cedula->group = "Datos Personales";

		$edit->nombre1 = new inputField('Nombres','nombre1');
		$edit->nombre1->rule      ='max_length[50]|required';
		$edit->nombre1->size      =20;
		$edit->nombre1->maxlength =50;
		$edit->nombre1->group = "Datos Personales";

		$edit->nombre2 = new inputField('Segundo Nombre','nombre2');
		$edit->nombre2->rule      ='max_length[50]';
		$edit->nombre2->size      =20;
		$edit->nombre2->maxlength =50;
		$edit->nombre2->in        ='nombre1';
		$edit->nombre2->group = "Datos Personales";

		$edit->apellido1 = new inputField('Apellidos','apellido1');
		$edit->apellido1->rule      ='max_length[50]|required';
		$edit->apellido1->size      =20;
		$edit->apellido1->maxlength =50;
		$edit->apellido1->group = "Datos Personales";

		$edit->apellido2 = new inputField('Segundo Apellido','apellido2');
		$edit->apellido2->rule      ='max_length[50]';
		$edit->apellido2->size      =20;
		$edit->apellido2->maxlength =50;
		$edit->apellido2->in        ='apellido1';
		$edit->apellido2->group = "Datos Personales";

		$edit->sexo = new dropDownField('Sexo','sexo');
		$edit->sexo->option('M','Masculino');
		$edit->sexo->option('F','Femenino');
		$edit->sexo->group = "Datos Personales";
		$edit->sexo->style = "width:100px";

		$edit->fnacimiento = new dateonlyField('Fecha de Nacimiento','fnacimiento');
		$edit->fnacimiento->rule='chfecha';
		$edit->fnacimiento->size =10;
		$edit->fnacimiento->maxlength =8;
		$edit->fnacimiento->group = "Datos Personales";
		
		$edit->discapacidad = new dropDownField('Posee Discapacidad','discapacidad');
		$edit->discapacidad->option('No','No');
		$edit->discapacidad->option('Si','Si');
		$edit->discapacidad->group = "Datos Personales";
		$edit->discapacidad->style = "width:100px";
		
		$edit->discapacidadd = new inputField('Indique Discapacidad','discapacidadd');
		$edit->discapacidadd->rule      ='max_length[50]';
		$edit->discapacidadd->size      =40;
		$edit->discapacidadd->maxlength =255;
		$edit->discapacidadd->group = "Datos Personales";
		
		$edit->pcedula = new inputField('C&eacute;dula Padre','pcedula');
		$edit->pcedula->rule='trim|max_length[8]|numeric';
		$edit->pcedula->size =10;
		$edit->pcedula->maxlength =8;
		$edit->pcedula->css_class = 'inputnum';
		$edit->pcedula->group = "Datos Familiares";
		
		$edit->padren = new inputField('Nombre Padre','padren');
		$edit->padren->size =40;
		$edit->padren->maxlength =255;
		$edit->padren->in  ='pcedula';
		$edit->padren->group = "Datos Familiares";

		$edit->mcedula = new inputField('C&eacute;dula Madre','mcedula');
		$edit->mcedula->rule='trim|max_length[8]|numeric';
		$edit->mcedula->size =10;
		$edit->mcedula->maxlength =8;
		$edit->mcedula->css_class = 'inputnum';
		$edit->mcedula->group = "Datos Familiares";
		
		$edit->madren = new inputField('madren','madren');
		$edit->madren->size =40;
		$edit->madren->maxlength =255;
		$edit->madren->in    = 'mcedula';
		$edit->madren->group = "Datos Familiares";
		
		$edit->hijos = new inputField('Cantidad de Hijos','hijos');
		$edit->hijos->rule      ='max_length[4]';
		$edit->hijos->size      =6;
		$edit->hijos->maxlength =4;
		$edit->hijos->css_class = 'inputnum';
		$edit->hijos->group = "Datos Familiares";
		

		$edit->telefono = new inputField('Tel&eacute;fono','telefono');
		$edit->telefono->rule      ='trim|max_length[11]|numeric|required';
		$edit->telefono->size      =20;
		$edit->telefono->maxlength =11;
		$edit->telefono->milength  =11;
		$edit->telefono->css_class = 'inputnum';
		$edit->telefono->group = "Datos De Contacto";

		$edit->email = new inputField('Email','email');
		$edit->email->rule='max_length[100]';
		$edit->email->size =40;
		$edit->email->maxlength =100;
		$edit->email->group = "Datos De Contacto";

		$edit->id_parroquia = new dropDownField('Parroquia','id_parroquia');
		$edit->id_parroquia->options("SELECT id,nombre FROM parroquia ORDER BY nombre");
		$edit->id_parroquia->group = "Datos De Contacto";
		$edit->id_parroquia->style = "width:180px";

		$edit->dir1 = new inputField('Direcci&oacute;n 1','dir1');
		$edit->dir1->rule='max_length[255]';
		$edit->dir1->size =40;
		$edit->dir1->maxlength =255;
		$edit->dir1->group = "Datos De Contacto";

		$edit->dir2 = new inputField('Direcci&oacute;n 2','dir2');
		$edit->dir2->rule='max_length[255]';
		$edit->dir2->size =40;
		$edit->dir2->maxlength =255;
		$edit->dir2->group = "Datos De Contacto";

		$edit->dir3 = new inputField('Direcci&oacute;n 3','dir3');
		$edit->dir3->rule='max_length[255]';
		$edit->dir3->size =40;
		$edit->dir3->maxlength =255;
		$edit->dir3->group = "Datos De Contacto";

		$edit->dir4 = new inputField('Direcci&oacute;n 4','dir4');
		$edit->dir4->rule='max_length[255]';
		$edit->dir4->size =40;
		$edit->dir4->maxlength =255;
		$edit->dir4->group = "Datos De Contacto";
		
		$edit->dirdesde = new dateonlyField('Vive en la Comunidad Desde','dirdesde');
		$edit->dirdesde->rule      ='chfecha';
		$edit->dirdesde->size      =10;
		$edit->dirdesde->maxlength =8;
		$edit->dirdesde->group = "Datos De Contacto";
		
		$edit->id_ccomunal = new dropDownField('Consejo Comunal','id_ccomunal');
		$edit->id_ccomunal->option('','');
		$edit->id_ccomunal->options("SELECT id,nombre FROM ccomunal ORDER BY nombre");
		$edit->id_ccomunal->group = "Datos De Contacto";

		$edit->id_ocivh = new dropDownField('OCIVH','id_ocivh');
		$edit->id_ocivh->option('','');
		$edit->id_ocivh->options("SELECT id,nombre FROM ocivh ORDER BY nombre");
		$edit->id_ocivh->group = "Datos De Contacto";


		$edit->trabaja = new dropDownField('Trabaja Actualmente','trabaja');
		$edit->trabaja->option('Si','Si');
		$edit->trabaja->option('No','No');
		$edit->trabaja->group = "Datos De laborales";

		$edit->trabajad = new dateonlyField('Fecha de Ingreso al trabajo','trabajad');
		$edit->trabajad->rule='chfecha';
		$edit->trabajad->size =10;
		$edit->trabajad->maxlength =8;
		$edit->trabajad->group = "Datos De laborales";

		$edit->oficio = new inputField('Profesi&oacute;n u Oficio','oficio');
		$edit->oficio->rule='max_length[255]';
		$edit->oficio->size =40;
		$edit->oficio->maxlength =255;
		$edit->oficio->group = "Datos De laborales";

		$edit->faov = new dropDownField('Cotiza FAOV','faov');
		$edit->faov->option('Si','Si');
		$edit->faov->option('No','No');
		$edit->faov->group = "Datos De laborales";

		$edit->faovdesde = new dateonlyField('Cotiza FAOV desde','faovdesde');
		$edit->faovdesde->rule='chfecha';
		$edit->faovdesde->size =10;
		$edit->faovdesde->maxlength =8;
		$edit->faovdesde->group = "Datos De laborales";
		
		$edit->sueldo = new inputField('Sueldo Mensual','sueldo');
		$edit->sueldo->rule='trim|max_length[255]|numeric';
		$edit->sueldo->size =10;
		$edit->sueldo->maxlength =255;
		$edit->sueldo->group = "Datos De laborales";
		$edit->sueldo->css_class = 'inputnum';
		

		$edit->buttons('add','modify', 'save', 'undo', 'delete', 'back');
		$edit->build();
		$data['content'] = $edit->output;
		$data["head"]    = $this->rapyd->get_head().script('jquery.js').script("plugins/jquery.numeric.pack.js");
		$data['title']   = $this->tits;
		$this->load->view('view_ventanas', $data);

	}
	
	function damerne(){
		$cedula = $this->input->post("cedula");
		$cedula = str_replace('.','',$cedula);
		$cedula = trim($cedula);
			
		$arreglo=array();
		if(is_numeric($cedula)){
			
			$query  ="select 
			rne.primer_nombre nombre1,
			rne.segundo_nombre nombre2,
			rne.primer_apellido apellido1,
			rne.segundo_apellido apellido2,
			nacionalidad
			from rne.rne
			 where cedula=$cedula";
			
			$mSQL   = $this->db->query($query);
			$arreglo= $mSQL->result_array($query);
			foreach($arreglo as $key=>$value)
				foreach($value as $key2=>$value2) 
				$arreglo[$key][$key2] = ($value2);
		}
		echo json_encode($arreglo);
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
		$mSQL="CREATE TABLE `personas` (
		  `nacional` char(1) NOT NULL DEFAULT 'V',
		  `cedula` int(8) NOT NULL,
		  `nombre1` varchar(50) DEFAULT NULL,
		  `nombre2` varchar(50) DEFAULT NULL,
		  `apellido1` varchar(50) NOT NULL,
		  `apellido2` varchar(50) DEFAULT NULL,
		  `sexo` char(1) DEFAULT NULL,
		  `fnacimiento` date DEFAULT NULL,
		  `telefono` int(11) DEFAULT NULL,
		  `hijos` tinyint(4) DEFAULT NULL,
		  `email` varchar(100) DEFAULT NULL,
		  `id_parroquia` smallint(6) NOT NULL,
		  `dir1` varchar(255) DEFAULT NULL,
		  `dir2` varchar(255) DEFAULT NULL,
		  `dir3` varchar(255) DEFAULT NULL,
		  `dir4` varchar(255) DEFAULT NULL,
		  `dirdesde` date DEFAULT NULL,
		  `trabaja` char(2) DEFAULT NULL,
		  `trabajad` date DEFAULT NULL,
		  `oficio` varchar(255) DEFAULT NULL,
		  `discapacidad` char(2) DEFAULT 'NO',
		  `discapacidadd` varchar(255) DEFAULT NULL,
		  `padren` varchar(255) DEFAULT 'V',
		  `pcedula` int(8) DEFAULT NULL,
		  `madren` varchar(255) DEFAULT 'V',
		  `mcedula` int(8) DEFAULT NULL,
		  `faov` char(1) DEFAULT NULL,
		  `faovdesde` date DEFAULT NULL,
		  `id_ccomunal` int(11) DEFAULT NULL,
		  `id_ocivh` int(11) DEFAULT NULL,
		  `sueldo` decimal(19,2) DEFAULT '0.00',
		  PRIMARY KEY (`cedula`),
		  KEY `cedula` (`cedula`),
		  KEY `id_parroquia` (`id_parroquia`)
		) ENGINE=MyISAM DEFAULT CHARSET=utf8";
		$this->db->simple_query($mSQL);		
	}
}
?>

