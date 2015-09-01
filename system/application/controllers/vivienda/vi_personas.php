<?php
class vi_personas extends Controller {
	var $titp='Personas';
	var $tits='Persona';
	var $url ='vivienda/vi_personas/';
	function vi_personas(){
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
		
		$filter->db->select(array('nacional','cedula','nombre1','nombre2','apellido1','apellido2','telefono','dir1','dir2','pcedula','mcedula','vi_parroquia.nombre parroquia','vi_ccomunal.nombre ccomunal','vi_ocivh.nombre ocivh'));
		$filter->db->from('vi_personas');
		$filter->db->join('vi_parroquia'   ,'vi_personas.id_parroquia=vi_parroquia.id'                   ,'LEFT');
		$filter->db->join('vi_ccomunal'    ,'vi_personas.id_ccomunal =vi_ccomunal.id'                    ,'LEFT');
		$filter->db->join('vi_ocivh'       ,'vi_personas.id_ocivh    =vi_ocivh.id'                       ,'LEFT');

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
		$filter->id_parroquia->options("SELECT id,nombre FROM vi_parroquia ORDER BY nombre");

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
		$filter->id_ccomunal->options("SELECT id,nombre FROM vi_ccomunal ORDER BY nombre");

		$filter->id_ocivh = new inputField('OCIVH','id_ocivh');
		$filter->id_ocivh->option('','');
		$filter->id_ocivh->options("SELECT id,nombre FROM vi_ocivh ORDER BY nombre");

		$filter->buttons('reset', 'search');
		$filter->build();

		$uri = anchor($this->url.'dataedit/show/<raencode><#cedula#></raencode>','<#cedula#>');

		$grid = new DataGrid('');
		$grid->order_by('cedula');
		$grid->per_page = 40;

		
		$grid->column_orderby('Cedula'                           ,"$uri"                            ,'cedula'          ,'align="left"');
		//$grid->column_orderby('Nacionalidad'                     ,"$uri"                            ,'nacional'        ,'align="left"');
		$grid->column_orderby('Primer Nombre'                    ,"nombre1"                         ,'nombre1'         ,'align="left"');
		$grid->column_orderby('Segundo Nombre'                   ,"nombre2"                         ,'nombre2'         ,'align="left"');
		$grid->column_orderby('Primer Apellido'                  ,"apellido1"                       ,'apellido1'       ,'align="left"');
		$grid->column_orderby('Segundo Apellido'                 ,"apellido2"                       ,'apellido2'       ,'align="left"');
		$grid->column_orderby('Parroquia'                        ,"parroquia"                       ,'parroquia'       ,'align="right"');
		$grid->column_orderby('Direcci&oacute;n 1'               ,"dir1"                            ,'dir1'            ,'align="left"');
		//$grid->column_orderby('Direcci&oacute;n 2'               ,"dir2"                            ,'dir2'            ,'align="left"');
		$grid->column_orderby('C&eacute;dula Padre'              ,"pcedula"                         ,'pcedula'         ,'align="left"');
		$grid->column_orderby('C&eacute;dula Madre'               ,"mcedula"                         ,'mcedula'         ,'align="left"');
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
	function dataedit($action,$cedula=''){
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
				damenombre();
				
				$("#mcedula").change(function(){
					mcedula=$("#mcedula").val();
					$.post("'.site_url($this->url.'damerne').'",{ cedula:mcedula },function(data){
						rne=jQuery.parseJSON(data);
						$("#madren").val(rne[0].nombre1+" "+rne[0].nombre2+" "+rne[0].apellido1+" "+rne[0].apellido2);
						$("#madren_val").html(rne[0].nombre1+" "+rne[0].nombre2+" "+rne[0].apellido1+" "+rne[0].apellido2);
					});
				});
				
				$("#pcedula").change(function(){
					pcedula=$("#pcedula").val();
					$.post("'.site_url($this->url.'damerne').'",{ cedula:pcedula },function(data){
						rne=jQuery.parseJSON(data);
						$("#padren").val(rne[0].nombre1+" "+rne[0].nombre2+" "+rne[0].apellido1+" "+rne[0].apellido2);
						$("#padren_val").html(rne[0].nombre1+" "+rne[0].nombre2+" "+rne[0].apellido1+" "+rne[0].apellido2);
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
				
				function visibletrabaja(){
						trabaja = $("#trabaja").val();
						if(trabaja=="Si"){
								$("#tr_trabajad").show();
								$("#tr_sueldo").show();
						}else{
							$("#tr_trabajad").hide();
							$("#tr_sueldo").hide();
						}
				}
				visibletrabaja();
				$("#trabaja").change(function(){
						visibletrabaja();
				});
				
				function visiblefaov(){
						faov = $("#faov").val();
						if(faov=="Si"){
							$("#tr_faovultimo").show();
						}else{
							$("#tr_faovultimo").hide();
						}
				}
				visiblefaov();
				$("#faov").change(function(){
						visiblefaov();
				});
				
			});
		';

		$edit = new DataEdit($this->tits, 'vi_personas');

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
		$edit->cedula->append(" Escribir en formato solo n&uacute;meros. Ejemplo:12345678");
		$edit->cedula->group = "Datos Personales";
		if($cedula)
		$edit->cedula->value=$cedula;
		

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
		$edit->sexo->option('F','Femenino');
		$edit->sexo->option('M','Masculino');
		$edit->sexo->group = "Datos Personales";
		$edit->sexo->style = "width:100px";
		
		$edit->estadocivil = new dropDownField('Estado Civil','estadocivil');
		$edit->estadocivil->option($a='Soltero'             ,$a);
		$edit->estadocivil->option($a='Casado'              ,$a);
		$edit->estadocivil->option($a='Divorciado'          ,$a);
		$edit->estadocivil->option($a='Viudo'               ,$a);
		$edit->estadocivil->option($a='Union Estable'       ,$a);
		$edit->estadocivil->group = "Datos Personales";
		$edit->estadocivil->style = "width:200px";

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
		
		$edit->vivienda = new dropDownField('Posee Vivienda','vivienda');
		$edit->vivienda->option("Si","Si");
		$edit->vivienda->option("No","No");
		$edit->vivienda->option("No Inf"   ,"No Inf"   );
		$edit->vivienda->style = "width:50px";
		$edit->vivienda->group = "Datos Personales";
		
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
		$edit->padren->readonly = true;
		$edit->padren->type = 'inputhidden';
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
		$edit->madren->readonly = true;
		$edit->madren->type = 'inputhidden';
		$edit->madren->group = "Datos Familiares";
		
		$edit->hijos = new inputField('Cantidad de Hijos','hijos');
		$edit->hijos->rule      ='max_length[4]';
		$edit->hijos->size      =6;
		$edit->hijos->maxlength =4;
		$edit->hijos->css_class = 'inputnum';
		$edit->hijos->group = "Datos Familiares";
		

		$edit->telefono = new inputField('Tel&eacute;fono','telefono');
		$edit->telefono->rule      ='trim|max_length[11]|numeric';
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
		
		$edit->twitter = new inputField('Twitter','twitter');
		$edit->twitter->size      =40;
		$edit->twitter->maxlength =50;
		$edit->twitter->group = "Datos De Contacto";

		$edit->id_parroquia = new dropDownField('Parroquia','id_parroquia');
		$edit->id_parroquia->options("SELECT id,nombre FROM vi_parroquia ORDER BY nombre");
		$edit->id_parroquia->group = "Datos De Contacto";
		$edit->id_parroquia->style = "width:180px";

		$edit->dir1 = new inputField('Direcci&oacute;n 1','dir1');
		$edit->dir1->rule='max_length[255]';
		$edit->dir1->size =40;
		$edit->dir1->maxlength =255;
		$edit->dir1->append("Urbanizacion, Barrio, Sector");
		$edit->dir1->group = "Datos De Contacto";

		$edit->dir2 = new inputField('Direcci&oacute;n 2','dir2');
		$edit->dir2->rule='max_length[255]';
		$edit->dir2->size =40;
		$edit->dir2->maxlength =255;
		$edit->dir2->append("Calle, avenida");
		$edit->dir2->group = "Datos De Contacto";

		$edit->dir3 = new inputField('Direcci&oacute;n 3','dir3');
		$edit->dir3->rule='max_length[255]';
		$edit->dir3->size =40;
		$edit->dir3->maxlength =255;
		$edit->dir3->append("Con Calle o avenida");
		$edit->dir3->group = "Datos De Contacto";

		$edit->dir4 = new inputField('Direcci&oacute;n 4','dir4');
		$edit->dir4->rule='max_length[255]';
		$edit->dir4->size =40;
		$edit->dir4->maxlength =255;
		$edit->dir4->append("Casa #, o apto #");
		$edit->dir4->group = "Datos De Contacto";
		
		$edit->dirdesde = new dateonlyField('Vive en la Comunidad Desde','dirdesde');
		$edit->dirdesde->rule      ='chfecha';
		$edit->dirdesde->size      =10;
		$edit->dirdesde->maxlength =8;
		$edit->dirdesde->group = "Datos De Contacto";
		
		$edit->id_ccomunal = new dropDownField('Consejo Comunal','id_ccomunal');
		$edit->id_ccomunal->option('','');
		$edit->id_ccomunal->options("SELECT id,nombre FROM vi_ccomunal ORDER BY nombre");
		$edit->id_ccomunal->group = "Datos De Contacto";

		$edit->id_ocivh = new dropDownField('OCIVH','id_ocivh');
		$edit->id_ocivh->option('','');
		$edit->id_ocivh->options("SELECT id,nombre FROM vi_ocivh ORDER BY nombre");
		$edit->id_ocivh->group = "Datos De Contacto";

		$edit->gmvv = new inputField('Nro Mision Vivienda Venezuela','gmvv');
		$edit->gmvv->size      =20;
		$edit->gmvv->maxlength =50;
		$edit->gmvv->group = "Datos De Contacto";
		
		$edit->mihogar = new inputField('Nro 0800 MiHogar','mihogar');
		$edit->mihogar->size      =20;
		$edit->mihogar->maxlength =50;
		$edit->mihogar->group = "Datos De Contacto";

		$edit->trabaja = new dropDownField('Trabaja Actualmente','trabaja');
		$edit->trabaja->option('No','No');
		$edit->trabaja->option('Si','Si');
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
		
		$edit->sueldo = new inputField('Sueldo Mensual','sueldo');
		$edit->sueldo->rule='trim|numeric';
		$edit->sueldo->size =10;
		$edit->sueldo->maxlength =255;
		$edit->sueldo->group = "Datos De laborales";
		$edit->sueldo->css_class = 'inputnum';

		$edit->faov = new dropDownField('Cotiza FAOV','faov');
		$edit->faov->option('No','No');
		$edit->faov->option('Si','Si');
		$edit->faov->group = "Datos De laborales";

		$edit->faovultimo = new dateonlyField('Cotiza FAOV Ultimo Mes','faovultimo');
		$edit->faovultimo->rule='chfecha';
		$edit->faovultimo->size =10;
		$edit->faovultimo->maxlength =8;
		$edit->faovultimo->group = "Datos De laborales";
		
		$edit->pension = new inputField('Pension Bs','pension');
		$edit->pension->rule='trim|numeric';
		$edit->pension->size =10;
		$edit->pension->maxlength =255;
		$edit->pension->group = "Datos De laborales";
        $edit->pension->css_class = 'inputnum';
                        
		$edit->amormayor = new inputField('Amor Mayor  Bs','amormayor');
		$edit->amormayor->rule='trim|numeric';
		$edit->amormayor->size =10;
		$edit->amormayor->maxlength =255;
		$edit->amormayor->group = "Datos De laborales";
        $edit->amormayor->css_class = 'inputnum';
                          
		$edit->madresbarrios = new inputField('Madres del Barrio  Bs','madresbarrios');
		$edit->madresbarrios->rule='trim|numeric';
		$edit->madresbarrios->size =10;
		$edit->madresbarrios->maxlength =255;
		$edit->madresbarrios->group = "Datos De laborales";
        $edit->madresbarrios->css_class = 'inputnum';
                              
		$edit->ribas = new inputField('Mision Ribas  Bs','ribas');
		$edit->ribas->rule='trim|numeric';
		$edit->ribas->size =10;
		$edit->ribas->maxlength =255;
		$edit->ribas->group = "Datos De laborales";
        $edit->ribas->css_class = 'inputnum';
                      
		$edit->fundaayacucho = new inputField('Funda Ayacucho  Bs','fundaayacucho');
		$edit->fundaayacucho->rule='trim|numeric';
		$edit->fundaayacucho->size =10;
		$edit->fundaayacucho->maxlength =255;
		$edit->fundaayacucho->group = "Datos De laborales";
        $edit->fundaayacucho->css_class = 'inputnum';
		
		$edit->hijosvzla = new inputField('Hijos Vzla  Bs','hijosvzla');
		$edit->hijosvzla->rule='trim|numeric';
		$edit->hijosvzla->size =10;
		$edit->hijosvzla->maxlength =255;
		$edit->hijosvzla->group = "Datos De laborales";
        $edit->hijosvzla->css_class = 'inputnum';
                          
		$edit->jubilado = new inputField('Jubilado  Bs','jubilado');
		$edit->jubilado->rule='trim|numeric';
		$edit->jubilado->size =10;
		$edit->jubilado->maxlength =255;
		$edit->jubilado->group = "Datos De laborales";
		$edit->jubilado->css_class = 'inputnum';
		
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
	
	function damepersona(){
		$cedula = $this->input->post("cedula");
					
		$arreglo=array();
		if(is_numeric($cedula)){
			$query  ="SELECT
			nombre1,
			nombre2,
			apellido1,
			apellido2
			FROM vi_personas
			WHERE cedula=$cedula";
			
			//$mSQL   = $this->db->query($query);
			//$arreglo= $mSQL->result_array($query);
			//foreach($arreglo as $key=>$value)
			//	foreach($value as $key2=>$value2) 
			//	$arreglo[$key][$key2] = htmlentities($value2);
			$arreglo = $this->datasis->damerow($query);
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
		$mSQL="CREATE TABLE `vi_personas` (
			`nacional` CHAR(1) NOT NULL DEFAULT 'V',
			`cedula` INT(8) NOT NULL,
			`nombre1` VARCHAR(50) NULL DEFAULT NULL,
			`nombre2` VARCHAR(50) NULL DEFAULT NULL,
			`apellido1` VARCHAR(50) NOT NULL,
			`apellido2` VARCHAR(50) NULL DEFAULT NULL,
			`sexo` CHAR(1) NULL DEFAULT NULL,
			`fnacimiento` DATE NULL DEFAULT NULL,
			`telefono` BIGINT(20) NULL DEFAULT NULL,
			`hijos` TINYINT(4) NULL DEFAULT NULL,
			`email` VARCHAR(100) NULL DEFAULT NULL,
			`id_parroquia` SMALLINT(6) NOT NULL,
			`dir1` VARCHAR(255) NULL DEFAULT NULL,
			`dir2` VARCHAR(255) NULL DEFAULT NULL,
			`dir3` VARCHAR(255) NULL DEFAULT NULL,
			`dir4` VARCHAR(255) NULL DEFAULT NULL,
			`dirdesde` DATE NULL DEFAULT NULL,
			`trabaja` CHAR(2) NULL DEFAULT NULL,
			`trabajad` DATE NULL DEFAULT NULL,
			`oficio` VARCHAR(255) NULL DEFAULT NULL,
			`discapacidad` CHAR(2) NULL DEFAULT 'NO',
			`discapacidadd` VARCHAR(255) NULL DEFAULT NULL,
			`padren` VARCHAR(255) NULL DEFAULT 'V',
			`pcedula` INT(8) NULL DEFAULT NULL,
			`madren` VARCHAR(255) NULL DEFAULT 'V',
			`mcedula` INT(8) NULL DEFAULT NULL,
			`faov` CHAR(1) NULL DEFAULT NULL,
			`faovdesde` DATE NULL DEFAULT NULL,
			`id_ccomunal` INT(11) NULL DEFAULT NULL,
			`id_ocivh` INT(11) NULL DEFAULT NULL,
			`sueldo` DECIMAL(19,2) NULL DEFAULT '0.00',
			`twitter` VARCHAR(50) NULL DEFAULT NULL,
			PRIMARY KEY (`cedula`),
			INDEX `cedula` (`cedula`),
			INDEX `id_parroquia` (`id_parroquia`)
		)
		COLLATE='utf8_general_ci'
		ENGINE=MyISAM;
";
		$this->db->simple_query($mSQL);
		$mSQL="ALTER TABLE `vi_personas` 	ADD COLUMN `estadocivil` VARCHAR(50) NULL DEFAULT NULL AFTER `vivienda`";
		$this->db->simple_query($mSQL);


		$mSQL="ALTER TABLE `vi_personas`	CHANGE COLUMN `pension` `pension` DECIMAL(19,2) NULL DEFAULT '0' AFTER `estadocivil`";
		$this->db->simple_query($mSQL);
		$mSQL="ALTER TABLE `vi_personas`  CHANGE COLUMN `amormayor` `amormayor` DECIMAL(19,2) NULL DEFAULT '0' AFTER `pension`";
		$this->db->simple_query($mSQL);
		$mSQL="ALTER TABLE `vi_personas` 	CHANGE COLUMN `madresbarrios` `madresbarrios` DECIMAL(19,2) NULL DEFAULT '0' AFTER `amormayor`";
		$this->db->simple_query($mSQL);
		$mSQL="ALTER TABLE `vi_personas`  CHANGE COLUMN `ribas` `ribas` DECIMAL(19,2) NULL DEFAULT '0' AFTER `madresbarrios`";
		$this->db->simple_query($mSQL);
		$mSQL="ALTER TABLE `vi_personas`	CHANGE COLUMN `fundaayacucho` `fundaayacucho` DECIMAL(19,2) NULL DEFAULT '0' AFTER `ribas`";
		$this->db->simple_query($mSQL);
		$mSQL="ALTER TABLE `vi_personas`  CHANGE COLUMN `hijosvzla` `hijosvzla` DECIMAL(19,2) NULL DEFAULT '0' AFTER `fundaayacucho`";
		$this->db->simple_query($mSQL);
		$mSQL="ALTER TABLE `vi_personas`	CHANGE COLUMN `fubilado` `jubilado` DECIMAL(19,2) NULL DEFAULT '0' AFTER `hijosvzla`";
		$this->db->simple_query($mSQL);

	}
}
?>

