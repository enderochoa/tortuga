<?php
class vi_solicitud extends Controller {
	var $titp='Solicitudes';
	var $tits='Solicitud';
	var $url ='vivienda/vi_solicitud/';
	function vi_solicitud(){
		parent::Controller();
		$this->load->library("rapyd");
		//$this->datasis->modulo_id(216,1);
	}
	function index(){
		redirect($this->url."filteredgrid");
	}
	function filteredgrid(){
		$this->rapyd->load('datafilter','datagrid');

		$filter = new DataFilter($this->titp);
		$filter->db->select(array('vi_solicitud.numero','vi_solicitud.status','vi_solicitud.cedula','vi_solicitud.tipo','vi_solicitud.fecha','vi_solicitud.fechainspeccion','CONCAT_WS(" ",vi_personas.nombre1,vi_personas.apellido1) nombre'));
		$filter->db->from('vi_solicitud');
		$filter->db->join('vi_personas','vi_solicitud.cedula=vi_personas.cedula');

		$filter->cedula = new inputField('cedula','cedula');
		$filter->cedula->rule      ='max_length[8]';
		$filter->cedula->size      =10;
		$filter->cedula->maxlength =8;
		$filter->cedula->db_name   ="vi_solicitud.cedula";
		
		$filter->tipo = new dropDownField('Tipo de Solicitud','tipo');
		$filter->tipo->option(""                          ,""                         );
		$filter->tipo->option("Adjudicacion de Vivienda"  ,"Adjudicacion de Vivienda" );
		$filter->tipo->option("Reubicacion de Vivienda"   ,"Reubicacion de Vivienda"  );
		$filter->tipo->option("Parcelas Aisladas"         ,"Parcelas Aisladas"        );
		$filter->tipo->option("Mejoramiento de Viviendas" ,"Mejoramiento de Viviendas");
		$filter->tipo->style = "width:250px";
		
		$filter->status = new dropDownField('Estado','status');
		$filter->status->option("","");
		$filter->status->option(6,"Expedientes Ejercicios Anteriores " );
		$filter->status->option(1,"Solicitud Recibida"                 );
		$filter->status->option(2,"Inspecci&oacute;n Asignada"         );
		$filter->status->option(3,"Inspecci&oacute;n Realizada"        );
		$filter->status->option(4,"Enviada a Reponsable"               );
		$filter->status->option(5,"Aprobada por Responsable"           );
		$filter->status->option(7,"Rechazada por Inmuvih"              );
		$filter->status->option(8,"Rechazada por Responsable"          );
		$filter->status->option(9,"Pendiente por Revision"             );
		
		$filter->status->style = "width:400px";
		
		$filter->fechad = new dateOnlyField("Desde", "fechad",'d/m/Y');
		$filter->fechah = new dateOnlyField("Hasta", "fechah",'d/m/Y');
		$filter->fechad->clause  =$filter->fechah->clause="where";
		$filter->fechad->db_name =$filter->fechah->db_name="fecha";
		$filter->fechad->operator=">=";
		$filter->fechah->operator="<=";
		$filter->fechad->group = $filter->fechah->group = "Fecha Solicitud";
		
		$filter->buttons('reset', 'search');
		$filter->build();

		$uri = anchor($this->url.'dataedit/show/<raencode><#numero#></raencode>','<#cedula#>');
		
		function sta($status){
			switch($status){
				case '6':return "Expedientes Ejercicios Anteriores " ;break;
				case '1':return "Solicitud Recibida"                 ;break;
				case '2':return "Inspecci&oacute;n Asignada"         ;break;
				case '3':return "Inspecci&oacute;n Realizada"        ;break;
				case '4':return "Enviada a Reponsable"               ;break;
				case '5':return "Aprobada por Responsable"           ;break;
				case '7':return "Rechazada por Inmuvih"              ;break;
				case '8':return "Rechazada por Responsable"          ;break;
				case '9':return "Pendiente por Revision"             ;break;
			}
		}

		$grid = new DataGrid('');
		$grid->order_by('numero','desc');
		$grid->per_page = 40;
		
		$grid->use_function('sta');

		$grid->column_orderby('C&eacute;dula'    ,"$uri"                                                  ,'cedula'           ,'align="left"'  );
		$grid->column_orderby('Nombre'           ,"nombre"                                                ,'nombre'           ,'align="left"'  );
		$grid->column_orderby('Tipo'             ,"tipo"                                                  ,'tipo'             ,'align="left"'  );
		$grid->column_orderby('fecha'            ,"<dbdate_to_human><#fecha#></dbdate_to_human>"          ,'fecha'            ,'align="center"');
		$grid->column_orderby('fechainspeccion'  ,"<dbdate_to_human><#fechainspeccion#></dbdate_to_human>",'fechainspeccion'  ,'align="center"');
		$grid->column_orderby('Estado'           ,"<sta><#status#></sta>"                                 ,'status'           ,'align="left"'  );
		
		$grid->add($this->url.'dataedit/create');
		$grid->build();

		$data['filtro']  = $filter->output;
		$data['content'] = $grid->output;
		$data['head']    = $this->rapyd->get_head().script('jquery.js');
		$data['title']   = $this->titp;
		$this->load->view('view_ventanas', $data);

	}
	function dataedit(){
		$this->rapyd->load('dataobject','datadetails');
		
		$modbus=array(
			'tabla'   =>'sumi',
			'columnas'=>array(
				'codigo' =>'C&oacute;digo',
				'descrip'=>'Descripcion',
				'unidad' =>'Unidad'),
			'filtro'  =>array('codigo' =>'C&oacute;digo','descrip'=>'Descripcion'),
			'retornar'=>array('codigo'=>'codigo_<#i#>','descrip'=>'descrip_<#i#>','unidad'=>'unidad_<#i#>'),
			'p_uri'=>array(4=>'<#i#>',),
			'titulo'  =>'Busqueda de Suministros');

		$bSUMI=$this->datasis->p_modbus($modbus,"<#i#>");

		$do = new DataObject("vi_solicitud");
		$do->pointer('vi_personas'  ,'vi_solicitud.cedula=vi_personas.cedula' ,"CONCAT_WS(' ',vi_personas.nombre1,vi_personas.nombre2,vi_personas.apellido1,vi_personas.apellido2) p_nombres");
		$do->pointer('vi_personas b'  ,'vi_solicitud.cedulapropietario=b.cedula' ,"CONCAT_WS(' ',b.nombre1,b.apellido1) p_nombresprop","LEFT");
		$do->rel_one_to_many('vi_solicitudit', 'vi_solicitudit', array('numero'=>'numero'),"LEFT");
		$do->rel_one_to_many('vi_solicitudm', 'vi_solicitudm', array('numero'=>'numero'),"LEFT");
		$do->db->_escape_char='';
		$do->db->_protect_identifiers=false;

		$edit = new DataDetails($this->tits, $do);
		$edit->back_url = site_url($this->url."filteredgrid");
		$edit->set_rel_title('vi_solicitudit','Pariente <#o#>');

		$edit->pre_process('insert','_valida');
		$edit->pre_process('update','_valida');

		$edit->post_process('insert','_post_insert');
		$edit->post_process('update','_post_update');
		$edit->post_process('delete','_post_delete');
		
		$edit->numero = new inputField('Ref.','numero');
		$edit->numero->rule='max_length[10]';
		$edit->numero->size =12;
		$edit->numero->maxlength =10;
		$edit->numero->mode='autohide';
		$edit->numero->when=array('show','modify');

		$edit->cedula = new inputField('C&eacute;dula','cedula');
		$edit->cedula->rule      = 'trim|max_length[8]|numeric|required';
		$edit->cedula->size      = 10;
		$edit->cedula->maxlength = 10;
		$edit->cedula->css_class =  'inputnum';
		$edit->cedula->db_name   = 'cedula';
		//$edit->cedula->append(" Escribir en formato solo n&uacute;meros. Ejemplo:12345678");
		
		$edit->p_nombres = new inputField('Nombre','p_nombres');
		$edit->p_nombres->size      =40;
		$edit->p_nombres->maxlength =50;
		$edit->p_nombres->pointer   =true;
		$edit->p_nombres->type      ='inputhidden';

		$edit->tipo = new dropDownField('Tipo de Solicitud','tipo');
		$edit->tipo->option("Adjudicacion de Vivienda"  ,"Adjudicacion de Vivienda" );		
		$edit->tipo->option("Reubicacion de Vivienda"   ,"Reubicacion de Vivienda"  );
		$edit->tipo->option("Parcelas Aisladas"         ,"Parcelas Aisladas"        );
		$edit->tipo->option("Mejoramiento de Viviendas" ,"Mejoramiento de Viviendas");
		$edit->tipo->option("No Inf"                    ,"No Inf" );
		$edit->tipo->style = "width:250px";
		
		$edit->fecha = new  dateonlyField("Fecha Solicitud",  "fecha");
		$edit->fecha->insertValue = date('Y-m-d');
		$edit->fecha->size        =12;
		$edit->fecha->rule        = 'required';
		
		$edit->status = new dropDownField('Estado','status');
		$edit->status->option(6,"Expedientes Ejercicios Anteriores " );
		$edit->status->option(1,"Solicitud Recibida"              );
		$edit->status->option(2,"Inspecci&oacute;n Asignada"      );
		$edit->status->option(3,"Inspecci&oacute;n Realizada"     );
		$edit->status->option(4,"Enviada a Reponsable"            );
		$edit->status->option(5,"Aprobada por Responsable"        );
		$edit->status->option(7,"Rechazada por Inmuvih"           );
		$edit->status->option(8,"Rechazada por Responsable"       );
		$edit->status->option(9,"Pendiente por Revision"          );
		$edit->status->style = "width:400px";
		
		$edit->fechainspeccion = new  dateonlyField("Fecha Inspecci&oacute;n",  "fechainspeccion");
		//$edit->fechainspeccion->insertValue = date('Y-m-d');
		$edit->fechainspeccion->size        =12;
		//$edit->fechainspeccion->rule        = 'required';
		
		$edit->banos = new inputField('Cantidad de Ba&ntilde;os','banos');
		$edit->banos->rule      = 'trim|numeric';
		$edit->banos->size      = 10;
		$edit->banos->maxlength = 10;
		$edit->banos->css_class =  'inputnum';
		
		$edit->habitaciones = new inputField('Cantidad de Habitaciones','habitaciones');
		$edit->habitaciones->rule      = 'trim|numeric';
		$edit->habitaciones->size      = 10;
		$edit->habitaciones->maxlength = 10;
		$edit->habitaciones->css_class =  'inputnum';
		
		$edit->mts2const = new inputField('Mt2 de Construcci&oacute;n','mts2const');
		$edit->mts2const->rule      = 'trim|numeric';
		$edit->mts2const->size      = 10;
		$edit->mts2const->maxlength = 10;
		$edit->mts2const->css_class =  'inputnum';		

		$edit->situacion = new dropDownField('Situacion Vivienda','situacion');
		$edit->situacion->option("Vivienda Familiar" ,"Vivienda Familiar" );
		$edit->situacion->option("Alquilado"         ,"Alquilado"         );
		$edit->situacion->option("Propio"            ,"Propio"            );
		$edit->situacion->option("Arrimado"          ,"Arrimado"          );
		$edit->situacion->option("Cuido"             ,"Cuido"             );
		$edit->situacion->option("Refugiado"         ,"Refugiado"         );
		$edit->situacion->style = "width:200px";
		
		$edit->estadovivienda = new dropDownField('Calidad Vivienda','estadovivienda');
		$edit->estadovivienda->option(""             ,"" );
		$edit->estadovivienda->option("Hacinamiento" ,"Hacinamiento" );
		$edit->estadovivienda->style = "width:200px";
		
		$edit->cedulapropietario = new inputField('C&eacute;dula Propietario','cedulapropietario');
		$edit->cedulapropietario->rule='trim|numeric';
		$edit->cedulapropietario->size =8;
		$edit->cedulapropietario->maxlength =8;
		$edit->cedulapropietario->css_class = 'inputnum';
		
		$edit->p_nombresprop = new inputField('Nombre','p_nombresprop');
		$edit->p_nombresprop->size      =20;
		$edit->p_nombresprop->maxlength =50;
		$edit->p_nombresprop->pointer   =true;
		$edit->p_nombresprop->type      ='inputhidden';
		
		$edit->riesgo = new dropDownField('Grado Riesgo','riesgo');
		$edit->riesgo->option("Ninguno"              ,"Ninguno");
		$edit->riesgo->option("Bajo"                 ,"Bajo"   );
		$edit->riesgo->option("Medio"                ,"Medio"  );
		$edit->riesgo->option("Alto"                 ,"Alto"   );
		$edit->riesgo->option("No Inf"   ,"No Inf"   );
		$edit->riesgo->style = "width:100px";

		$edit->techo = new dropDownField('Techo','techo');
		$edit->techo->option("No Inf"   ,"No Inf"   );
		$edit->techo->option("Platabanda"            ,"Platabanda");
		$edit->techo->option("Asbesto"               ,"Asbesto"   );
		$edit->techo->option("Zinc"                  ,"Zinc"      );
		$edit->techo->option("Teja"                  ,"Teja"      );
		$edit->techo->option("Acerolit"              ,"Acerolit"  );
		$edit->techo->option("Otro"                  ,"Otro"      );
		$edit->techo->option("No Tiene"              ,"No Tiene"  );
		$edit->techo->style = "width:250px";
		
		$edit->techoc = new dropDownField('Condici&oacute;n','techoc');
		$edit->techoc->option("No Inf" ,"No Inf" );
		$edit->techoc->option("Bueno"  ,"Bueno"  );
		$edit->techoc->option("Regular","Regular");
		$edit->techoc->option("Malo"   ,"Malo"   );
		$edit->techoc->style = "width:100px";

		$edit->piso = new dropDownField('Piso','piso');
		$edit->piso->option("No Inf"   ,"No Inf"   );
		$edit->piso->option("Cemento Pulido"    ,"Cemento Pulido"        );
		$edit->piso->option("Cemento Rustico"   ,"Cemento Rustico"       );
		$edit->piso->option("Tierra"            ,"Tierra"                );
		$edit->piso->option("Ceramica"          ,"Ceramica"              );
		$edit->piso->option("Granito"           ,"Granito"               );
		$edit->piso->option("Otro"              ,"Otro"                  );
		
		$edit->piso->style = "width:250px";

		$edit->pisoc = new dropDownField('Condici&oacute;n','pisoc');
		$edit->pisoc->option("No Inf" ,"No Inf" );
		$edit->pisoc->option("Bueno"  ,"Bueno"  );
		$edit->pisoc->option("Regular","Regular");
		$edit->pisoc->option("Malo"   ,"Malo"   );
		$edit->pisoc->style = "width:100px";

		$edit->pared = new dropDownField('Paredes','pared');
		$edit->pared->option("No Inf"   ,"No Inf"   );
		$edit->pared->option("Bloque, Prefabricadas (aptas)" ,"Bloque, Prefabricadas (aptas)" );
		$edit->pared->option("Materiales Livianos (no aptos)","Materiales Livianos (no aptos)");
		$edit->pared->style = "width:250px";

		$edit->paredc = new dropDownField('Condici&oacute;n','paredc');
		$edit->paredc->option("No Inf" ,"No Inf" );
		$edit->paredc->option("Bueno"  ,"Bueno"  );
		$edit->paredc->option("Regular","Regular");
		$edit->paredc->option("Malo"   ,"Malo"   );
		$edit->paredc->style = "width:100px";

		$edit->ablancas = new dropDownField('Aguas Blancas','ablancas');
		$edit->ablancas->option("No Inf"   ,"No Inf"   );
		$edit->ablancas->option("Acueducto" ,"Acueducto" );
		$edit->ablancas->option("Tanque"    ,"Tanque"    );
		$edit->ablancas->option("Pozo"      ,"Pozo"      );
		$edit->ablancas->style = "width:150px";

		$edit->ablancasc = new dropDownField('Condici&oacute;n','ablancasc');
		$edit->ablancasc->option("No Inf" ,"No Inf" );
		$edit->ablancasc->option("Bueno"  ,"Bueno"  );
		$edit->ablancasc->option("Regular","Regular");
		$edit->ablancasc->option("Malo"   ,"Malo"   );
		$edit->ablancasc->style = "width:100px";

		$edit->aservidas = new dropDownField('Aguas Servidas','aservidas');
		$edit->aservidas->option("No Inf"   ,"No Inf"   );
		$edit->aservidas->option("Cloacas"                   ,"Cloacas"                   );
		$edit->aservidas->option("Pozo S&eacute;ptico"       ,"Pozo S&eacute;ptico"       );
		$edit->aservidas->option("Disposici&oacute;n Abierta","Disposici&oacute;n Abierta");
		$edit->aservidas->style = "width:150px";

		$edit->aservidasc = new dropDownField('Condici&oacute;n','aservidasc');
		$edit->aservidasc->option("No Inf" ,"No Inf" );
		$edit->aservidasc->option("Bueno"  ,"Bueno"  );
		$edit->aservidasc->option("Regular","Regular");
		$edit->aservidasc->option("Malo"   ,"Malo"   );
		$edit->aservidasc->style = "width:100px";

		$edit->electrificacion = new dropDownField('Electrificaci&oacute;n','electrificacion');
		$edit->electrificacion->option("No Inf"   ,"No Inf"   );
		$edit->electrificacion->option("Cadafe"          ,"Cadafe"          );
		$edit->electrificacion->option("Colgado"         ,"Colgado"         );
		$edit->electrificacion->option("Planta Electrica","Planta Electrica");
		$edit->electrificacion->option("No Tiene"        ,"No Tiene"        );
		$edit->electrificacion->style = "width:150px";

		$edit->electrificacionc = new dropDownField('Condici&oacute;n','electrificacionc');
		$edit->electrificacionc->option("No Inf" ,"No Inf" );
		$edit->electrificacionc->option("Bueno"  ,"Bueno"  );
		$edit->electrificacionc->option("Regular","Regular");
		$edit->electrificacionc->option("Malo"   ,"Malo"   );
		$edit->electrificacionc->style = "width:100px";

		$edit->vialidad = new dropDownField('Vialidad','vialidad');
		$edit->vialidad->option("No Inf"   ,"No Inf"   );
		$edit->vialidad->option("Asfaltada"    ,"Asfaltada"    );
		$edit->vialidad->option("Concreto"     ,"Concreto"     );
		$edit->vialidad->option("Tierra"       ,"Tierra"       );
		$edit->vialidad->option("No hay Acceso","No hay Acceso");
		$edit->vialidad->style = "width:150px";

		$edit->vialidadc = new dropDownField('Condici&oacute;n','vialidadc');
		$edit->vialidadc->option("No Inf" ,"No Inf" );
		$edit->vialidadc->option("Bueno"  ,"Bueno"  );
		$edit->vialidadc->option("Regular","Regular");
		$edit->vialidadc->option("Malo"   ,"Malo"   );
		$edit->vialidadc->style = "width:100px";

		$edit->aseo = new dropDownField('Aseo Urbano','aseo');
		$edit->aseo->option("No Inf"   ,"No Inf"   );
		$edit->aseo->option("Si","Si");
		$edit->aseo->option("No","No");
		$edit->aseo->style = "width:50px";

		$edit->gas = new dropDownField('Gas' ,'gas');
		$edit->gas->option("No Inf"   ,"No Inf"   );
		$edit->gas->option("Si","Si");
		$edit->gas->option("No","No");
		$edit->gas->style = "width:50px";

		$edit->telefonia = new dropDownField('Telefon&iacute;a CANTV','telefonia');
		$edit->telefonia->option("No Inf"   ,"No Inf"   );
		$edit->telefonia->option("Si","Si");
		$edit->telefonia->option("No","No");
		$edit->telefonia->style = "width:50px";

		$edit->transporte = new dropDownField('Transporte','transporte');
		$edit->transporte->option("No Inf"   ,"No Inf"   );
		$edit->transporte->option("Si","Si");
		$edit->transporte->option("No","No");
		$edit->transporte->style = "width:50px";

		$edit->terrenopropio = new dropDownField('Terreno Propio','terrenopropio');
		$edit->terrenopropio->option("No Inf"   ,"No Inf"   );
		$edit->terrenopropio->option("No","No");
		$edit->terrenopropio->option("Si","Si");
		$edit->terrenopropio->style = "width:100px";
		
		$edit->id_parroquia_terreno = new dropDownField('Parroquia Terreno','id_parroquia_terreno');
		$edit->id_parroquia_terreno->option("","");
		$edit->id_parroquia_terreno->options("SELECT id,nombre FROM vi_parroquia ORDER BY nombre");
		$edit->id_parroquia_terreno->style = "width:180px";

		$edit->dimfrente = new inputField('Frente (en Metros) ','dimfrente');
		$edit->dimfrente->rule      ='numeric';
		$edit->dimfrente->size      =10;
		$edit->dimfrente->maxlength =10;
		$edit->dimfrente->css_class = 'inputnum';
		$edit->dimfrente->group     = "Dimenciones";

		$edit->dimfondo = new inputField('Fondo (en Metros)','dimfondo');
		$edit->dimfondo->rule      ='numeric';
		$edit->dimfondo->size      =10;
		$edit->dimfondo->maxlength =10;
		$edit->dimfondo->css_class = 'inputnum';
		$edit->dimfondo->group     = "Dimenciones";
		
		$edit->dimderecho = new inputField('Derecho (en Metros)','dimderecho');
		$edit->dimderecho->rule      ='numeric';
		$edit->dimderecho->size      =10;
		$edit->dimderecho->maxlength =10;
		$edit->dimderecho->css_class = 'inputnum';
		$edit->dimderecho->group     = "Dimenciones";
		
		$edit->dimizquierdo = new inputField('Izquierdo (en Metros)','dimizquiero');
		$edit->dimizquierdo->rule      ='numeric';
		$edit->dimizquierdo->size      =10;
		$edit->dimizquierdo->maxlength =10;
		$edit->dimizquierdo->css_class = 'inputnum';
		$edit->dimizquierdo->group     = "Dimenciones";
		
		$edit->condterreno = new dropDownField('Condicion Legal Terreno','condterreno');
		$edit->condterreno->option("" ,"");
		$edit->condterreno->option("R","Registrado");
		$edit->condterreno->option("N","Notariado");
		$edit->condterreno->option("S","Tutulo Supletorio");
		$edit->condterreno->style = "width:280px";
		
		$edit->observa = new textAreaField('Situaci&oacute;n Social','observa');
		$edit->observa->rule      ='trim';
		$edit->observa->rows      =2;
		$edit->observa->cols      =60;
		
		$edit->obsetecnica = new textAreaField('Observaci&oacute;n T&eacute;cnica','obsetecnica');
		$edit->obsetecnica->rule      ='trim';
		$edit->obsetecnica->rows      =2;
		$edit->obsetecnica->cols      =60;
		
		/************
		 * DETALLE
		 */
		
		$edit->itcedulap = new inputField("(<#o#>) Cedula Pariente", "cedulap_<#i#>");
		$edit->itcedulap->rule         ='trim|numeric';//|callback_itorden |callback_repetido|
		$edit->itcedulap->size         =10;
		$edit->itcedulap->db_name      ='cedulap';
		$edit->itcedulap->rel_id       ='vi_solicitudit';
		$edit->itcedulap->autocomplete =false;
		$edit->itcedulap->css_class    ='inputnum';
		
		$edit->itnombre = new inputField("(<#o#>) Nombre Pariente", "nombre_<#i#>");
		$edit->itnombre->db_name ='nombre';
		$edit->itnombre->rel_id  ='vi_solicitudit';
		$edit->itnombre->size    =50;
		//$edit->itnombre->readonly=true;
		//$edit->itnombre->pointer =true;
		//$edit->itnombre->type    ='inputhidden';
		
		$edit->itparentesco = new dropDownField("(<#o#>) Parentesco Pariente", "parentesco_<#i#>");
		$edit->itparentesco->rule         ='trim';//|callback_itorden |callback_repetido|
		$edit->itparentesco->size         =10;
		$edit->itparentesco->db_name      ='parentesco';
		$edit->itparentesco->rel_id       ='vi_solicitudit';
		$edit->itparentesco->option(""            ,""            );
		$edit->itparentesco->option("Padres"      ,"Padres      ");
		$edit->itparentesco->option("Hijos"       ,"Hijos       ");
		$edit->itparentesco->option("Cónyuge"     ,"Cónyuge     ");
		$edit->itparentesco->option("Suegros"     ,"Suegros     ");
		$edit->itparentesco->option("Yerno/Nuera" ,"Yerno/Nuera ");
		$edit->itparentesco->option("Abuelos"     ,"Abuelos     ");
		$edit->itparentesco->option("Nietos"      ,"Nietos      ");
		$edit->itparentesco->option("Hermanos"    ,"Hermanos    ");
		$edit->itparentesco->option("Cuñados"     ,"Cuñados     ");
		$edit->itparentesco->option("Amigo"       ,"Amigo       ");
		$edit->itparentesco->option("Otro"        ,"Otro        ");
		$edit->itparentesco->style = "width:150px";
	
		
		/**************
		 * 
		 * DETALLE DE SUMINISTROS
		 * 
		 * ************/
		 
		$edit->it2codigo = new inputField("(<#o#>) Codigo Suministro", "codigo_<#i#>");
		$edit->it2codigo->rule         ='trim';//|callback_itorden |callback_repetido|
		$edit->it2codigo->size         =10;
		$edit->it2codigo->db_name      ='codigo';
		$edit->it2codigo->rel_id       ='vi_solicitudm';
		$edit->it2codigo->readonly     =true;
		$edit->it2codigo->append($bSUMI);
		
		$edit->it2descrip = new inputField("(<#o#>) Descripci&oacute;n", "descrip_<#i#>");
		$edit->it2descrip->rule         ='trim';//|callback_itorden |callback_repetido|
		$edit->it2descrip->size         =50;
		$edit->it2descrip->db_name      ='descrip';
		$edit->it2descrip->rel_id       ='vi_solicitudm';
		$edit->it2descrip->readonly     =true;
		
		$edit->it2unidad = new inputField("(<#o#>) Unidad", "unidad_<#i#>");
		$edit->it2unidad->rule         ='trim';//|callback_itorden |callback_repetido|
		$edit->it2unidad->size         =10;
		$edit->it2unidad->db_name      ='unidad';
		$edit->it2unidad->rel_id       ='vi_solicitudm';
		$edit->it2unidad->readonly     =true;
		
		$edit->it2cantidad = new inputField("(<#o#>) Cantidad", "cantidad_<#i#>");
		$edit->it2cantidad->rule         ='trim';//|callback_itorden |callback_repetido|
		$edit->it2cantidad->size         =10;
		$edit->it2cantidad->db_name      ='cantidad';
		$edit->it2cantidad->rel_id       ='vi_solicitudm';
		$edit->it2cantidad->css_class    ='inputnum';
		$edit->it2cantidad->value        =0;
		
		$edit->rectecnicas = new textAreaField('Recomendaciones Tecnicas','rectecnicas');
		$edit->rectecnicas->rule      ='trim';
		$edit->rectecnicas->rows      =2;
		$edit->rectecnicas->cols      =60;
		
		
		$edit->button_status("btn_add_pariente" ,'Agregar Pariente',"javascript:add_vi_solicitudit()","PA",'modify',"button_add_rel");
		$edit->button_status("btn_add_pariente2",'Agregar Pariente',"javascript:add_vi_solicitudit()","PA",'create',"button_add_rel");
		$edit->button_status("btn_add_sumi" ,'Agregar Suministro',"javascript:add_vi_solicitudm()","SU","create","button_add_rel");
		$edit->button_status("btn_add_sumi2",'Agregar Suministro',"javascript:add_vi_solicitudm()","SU","modify","button_add_rel");
		
		$edit->buttons("add_rel",'add','modify', 'save', 'undo', 'delete', 'back');
		$edit->build();
		
		$smenu['link']   = barra_menu('198');
		$data['smenu']   = $this->load->view('view_sub_menu', $smenu,true);
		$conten["form"]  =&  $edit;
		$data['content'] = $this->load->view('vivienda/vi_solicitud', $conten,true);
		//$data['content'] = $edit->output;
		$data["head"]    = $this->rapyd->get_head().script('jquery.js').script('jquery-ui.js').script("plugins/jquery.numeric.pack.js").script('plugins/jquery.meiomask.js').style('vino/jquery-ui.css');
		$data['title']   = $this->tits;
		$this->load->view('view_ventanas', $data);

	}
	
	function _valida($do){
		$error  ='';
		$numero = $do->get('numero');
		$cedula = $do->get('cedula');
		$cedulae= $this->db->escape($cedula);
		
		if($numero>0){
			$query="SELECT COUNT(*) FROM vi_solicitud WHERE cedula=$cedulae AND $numero<>$numero";
		}else{
			$query="SELECT COUNT(*) FROM vi_solicitud WHERE cedula=$cedulae";
		}
		
		$query="SELECT COUNT(*) FROM vi_personas WHERE cedula=$cedulae";
		$c    = $this->datasis->dameval($query);
		
		if(!($c>0))
			$error.="Error. Debe registrar primero a la persona, por el modulo personas";
		
		$c = $this->datasis->dameval($query);
		if($c>0)
		$error.="ERROR. ya existe una solicituda para $cedula";
		
		
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
		$mSQL="CREATE TABLE `vi_solicitud` (
			`cedula` INT(8) NOT NULL,
			`tipo` VARCHAR(50) NULL DEFAULT NULL,
			`fecha` DATE NULL DEFAULT NULL,
			`situacion` VARCHAR(50) NULL DEFAULT NULL,
			`riesgo` VARCHAR(50) NULL DEFAULT NULL,
			`techo` VARCHAR(50) NULL DEFAULT NULL,
			`techoc` VARCHAR(50) NULL DEFAULT NULL,
			`piso` VARCHAR(50) NULL DEFAULT NULL,
			`pisoc` VARCHAR(50) NULL DEFAULT NULL,
			`pared` VARCHAR(50) NULL DEFAULT NULL,
			`paredc` VARCHAR(50) NULL DEFAULT NULL,
			`ablancas` VARCHAR(50) NULL DEFAULT NULL,
			`ablancasc` VARCHAR(50) NULL DEFAULT NULL,
			`aservidas` VARCHAR(50) NULL DEFAULT NULL,
			`aservidasc` VARCHAR(50) NULL DEFAULT NULL,
			`electrificacion` VARCHAR(50) NULL DEFAULT NULL,
			`electrificacionc` VARCHAR(50) NULL DEFAULT NULL,
			`vialidad` VARCHAR(50) NULL DEFAULT NULL,
			`vialidadc` VARCHAR(50) NULL DEFAULT NULL,
			`aseo` VARCHAR(50) NULL DEFAULT NULL,
			`gas` VARCHAR(50) NULL DEFAULT NULL,
			`telefonia` VARCHAR(50) NULL DEFAULT NULL,
			`transporte` VARCHAR(50) NULL DEFAULT NULL,
			`cedulapropietario` INT(8) NULL DEFAULT NULL,
			`terrenopropio` CHAR(2) NULL DEFAULT NULL,
			`id_parraoquia_terreno` INT(11) NULL DEFAULT NULL,
			`dim_ancho` DECIMAL(19,2) NULL DEFAULT '0.00',
			`dim_largo` DECIMAL(19,2) NULL DEFAULT '0.00',
			`observa` TEXT NULL,
			PRIMARY KEY (`cedula`)
		)
		COLLATE='utf8_general_ci'
		ENGINE=MyISAM;
";
		$this->db->simple_query($mSQL);
		
		$mSQL="
		CREATE TABLE `vi_solicitudm` (
			`id` INT(11) NOT NULL AUTO_INCREMENT,
			`numero` INT(8) NULL DEFAULT NULL,
			`codigo` VARCHAR(15) NULL DEFAULT NULL,
			`descrip` VARCHAR(255) NULL DEFAULT NULL,
			`unidad` VARCHAR(255) NULL DEFAULT NULL,
			`cantidad` DECIMAL(19,2) NULL DEFAULT '0.00',
			PRIMARY KEY (`id`),
			INDEX `numero` (`numero`),
			INDEX `codigo` (`codigo`)
		)
		COLLATE='utf32_general_ci'
		ENGINE=MyISAM;
		";
		$this->db->simple_query($mSQL);
		
		$mSQL="ALTER TABLE `vi_solicitud` ADD COLUMN `fechainspeccion` DATE NULL AFTER `observa`";
		$this->db->simple_query($mSQL);
		$mSQL="ALTER TABLE `vi_solicitud` ADD COLUMN `banos` INT NULL AFTER `fechainspeccion`";
		$this->db->simple_query($mSQL);
		$mSQL="ALTER TABLE `vi_solicitud` ADD COLUMN `habitaciones` INT NULL AFTER `banos`";
		$this->db->simple_query($mSQL);
		$mSQL="ALTER TABLE `vi_solicitud` ADD COLUMN `mts2const` INT NULL AFTER `habitaciones`";
		$this->db->simple_query($mSQL);
		$mSQL="ALTER TABLE `vi_solicitud` ADD COLUMN `status` CHAR(2) NULL AFTER `mts2const`";
		$this->db->simple_query($mSQL);
		$mSQL="ALTER TABLE `vi_solicitud` ADD COLUMN `estadovivienda` VARCHAR(50) NULL DEFAULT NULL";
		$this->db->simple_query($mSQL);
		$mSQL="ALTER TABLE `vi_solicitud` ADD COLUMN `rectecnicas` TEXT NULL DEFAULT NULL AFTER `estadovivienda`";
		$this->db->simple_query($mSQL);
		$mSQL="ALTER TABLE `vi_solicitud` 	ADD COLUMN `condterreno` VARCHAR(50) NULL AFTER `rectecnicas`";
		$this->db->simple_query($mSQL);
		$mSQL="ALTER TABLE `vi_solicitud`  ADD COLUMN `dimfrente` DECIMAL(19,2) NULL DEFAULT '0' AFTER `condterreno`";
		$this->db->simple_query($mSQL);
		$mSQL="ALTER TABLE `vi_solicitud` ADD COLUMN `dimfondo` DECIMAL(19,2) NULL DEFAULT '0' AFTER `dimfrente`";
		$this->db->simple_query($mSQL);
		$mSQL="ALTER TABLE `vi_solicitud` ADD COLUMN `dimderecho` DECIMAL(19,2) NULL DEFAULT '0' AFTER `dimfondo`";
		$this->db->simple_query($mSQL);
		$mSQL="ALTER TABLE `vi_solicitud` ADD COLUMN `dimizquierdo` DECIMAL(19,2) NULL DEFAULT '0' AFTER `dimderecho`";
		$this->db->simple_query($mSQL);
		$mSQL="ALTER TABLE `vi_solicitud` 	ADD COLUMN `obsetecnica` TEXT NULL AFTER `dimizquierdo`";
		$this->db->simple_query($mSQL);
	}

}

?>
