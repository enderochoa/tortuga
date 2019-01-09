<?php

require_once(APPPATH.'/controllers/ingresos/contribu.php'); 
class Recibo extends Controller {
	var $titp='Recibos';
	var $tits='Recibo';
	var $url ='ingresos/recibo/';
	var $temp=array('nacionalit','p_claseot','p_negociot','p_claset','p_localt','p_tipot','i_tipo_int','i_sectort','i_claset','i_tipot','v_marcat','v_tipot','v_claset');
	var $recibomodificontribu='';
	
	function Recibo(){
		parent::Controller();
		$this->load->library("rapyd");
		//$this->datasis->modulo_id(216,1);
		$this->recibomodificontribu= $this->datasis->traevalor('recibomodificontribu','S','Indica si desde el modulo de recibos se pueden agregar y/o modificar contribuyentes');
	}
	function index(){
		redirect($this->url."filteredgrid");
	}
	function filteredgrid(){
		$this->rapyd->load('datafilter','datagrid');

		$filter = new DataFilter($this->titp);
		$filter->db->select(array("a.id","a.numero","a.nombre","a.fecha","b.descrip","a.monto","a.contribu","a.nombre","a.status","a.solvencia","a.oper"));
		$filter->db->from('recibo a');
		$filter->db->join('tingresos b','a.tipo=b.codigo');

		$filter->id = new inputField('Ref.','id');
		$filter->id->rule      ='max_length[10]';
		$filter->id->size      =12;
		$filter->id->maxlength =10;
		
		$filter->numero = new inputField('Numero','numero');
		$filter->numero->rule      ='max_length[10]';
		$filter->numero->size      =12;
		$filter->numero->maxlength =10;

		$filter->fecha = new dateonlyField('Fecha','fecha');
		$filter->fecha->rule      ='chfecha';
		$filter->fecha->size      =10;
		$filter->fecha->maxlength =8;
		$filter->fecha->db_name='a.fecha';
		$filter->fecha->clause='where';
		$filter->fecha->operator='=';

		$filter->contribu = new inputField('Codigo','contribu');
		$filter->contribu->rule      ='max_length[6]';
		$filter->contribu->size      =8;
		$filter->contribu->maxlength =6;
		$filter->contribu->group     ="Contribuyente";
		
		$filter->nombre = new inputField('Nombre','nombre');
		$filter->nombre->size      =20;
		$filter->nombre->group     ="Contribuyente";

		$filter->rifci = new inputField('Rif/Ced','rifci');
		$filter->rifci->rule      ='max_length[13]';
		$filter->rifci->size      =15;
		$filter->rifci->maxlength =13;
		$filter->rifci->group     ="Contribuyente";

		$filter->tipo = new dropdownField('Concepto','tipo');
		$filter->tipo->option('','');
		$filter->tipo->options("SELECT codigo,descrip FROM tingresos ORDER BY descrip");
		
		$filter->oper = new dropdownField('Tipo Solvencia','oper');
		$filter->oper->option('','');
		$filter->oper->option('TRAMITES','TRAMITES');
		$filter->oper->option('REGISTRO','REGISTRO');

		$filter->buttons('reset', 'search');
		$filter->build();

		$uri  = anchor($this->url.'dataedit/show/<raencode><#id#></raencode>','<#id#>');
		$uri2 = anchor($this->url.'dataedit/show/<raencode><#id#></raencode>','<#numero#>');
		
		function sta($s){
			switch($s){
				case 'P':return 'Por Cancelar';
				case 'C':return 'Cancelado';
				case 'A':return 'Anulado';
			}
		}
		
		function solv($id,$solvencia,$oper){
			if($oper=='REGISTRO'){
				if(empty($solvencia)){
					return anchor('ingresos/recibo/dataprint_concsolv/modify/'.$id,'Solvencia Registro');
				}else{
					return anchor('ingresos/recibo/dataprint_concsolv/modify/'.$id,$solvencia);
				}
			}else{
				return $solvencia;
			}
		}

		$grid = new DataGrid('');
		$grid->order_by('id','desc');
		$grid->per_page = 40;
		$grid->use_function('sta','solv');

		$grid->column_orderby('Ref.'           ,"$uri"                                        ,'id'       , 'align="left"' );
		$grid->column_orderby('Numero'         ,"$uri2"                                       ,'numero'   , 'align="left"' );
		//$grid->column_orderby('Solvencia'      ,"<solv><#id#>|<#solvencia#>|<#oper#></solv>"  ,'solvencia', 'align="left"' );
		$grid->column_orderby('Fecha'          ,"<dbdate_to_human><#fecha#></dbdate_to_human>",'fecha'    ,'align="center"');
		$grid->column_orderby('Concepto'       ,"descrip"                                     ,'tipo'     ,'align="left"'  );
		$grid->column_orderby('Monto'          ,"<nformat><#monto#></nformat>"                ,'monto'    ,'align="right"' );
		$grid->column_orderby('Contribuyente'  ,"contribu"                                    ,'contribu' ,'align="left"'  );
		$grid->column_orderby('Nombre'         ,"nombre"                                      ,'nombre'   ,'align="left"'  );
		$grid->column_orderby('Estado'         ,"<sta><#status#></sta>"                       ,'status'   ,'align="left"'  );
		
		$grid->add($this->url.'dataedit/create');
		$grid->build();
		//echo $grid->db->last_query();

		$data['filtro']  = $filter->output;
		$data['content'] = $grid->output;
		$data['head']    = $this->rapyd->get_head().script('jquery.js');
		$data['title']   = $this->titp;
		$this->load->view('view_ventanas', $data);

	}
	
	function dataedit($action='',$id=''){
		$this->rapyd->load('datadetails','dataobject');
		
		$mCONTRIBU=array(
				'tabla'   =>'contribu',
				'columnas' =>array(
					'codigo'   =>'C&oacute;digo',
					'nacionali'=>'',
					'rifci'    =>'RIF',
					'nombre'   =>'Nombre',
					'direccion'=>'Direcci&oacute;n',
					'telefono' =>'Telefono'
					),
				'filtro'  =>array(
					'codigo'   =>'C&oacute;digo',
					'rifci'    =>'RIF',
					'nacionali'=>'Nacionalidad',
					'nombre'   =>'Nombre',
					'direccion'=>'Direcci&oacute;n',
					'telefono' =>'Telefono'
					),
				'retornar'=>array('codigo'=>'contribu', 'nombre'=>'nombre','rifci'=>'rifci','direccion'=>'direccion','telefono'=>'telefono','nacionali'=>'nacionalit' ),
				'script'  =>array('cal_nacionali()'),
				'titulo'  =>'Buscar Contribuyente');
			
		$bCONTRIBU=$this->datasis->modbus($mCONTRIBU);
		
		$mPATENTE=array(
				'tabla'   =>'v_patente_contri',
				'columnas' =>array(
					'id'       =>'Ref.',
					'tarjeta'  =>'Tarjeta',
					'local'    =>'Local',
					'licencia' =>'Licencia',
					'razon'    =>'Razon',
					'dir_neg'  =>'Direccion Negocio'
					),
				'filtro'  =>array(
					'id'       =>'Ref.',
					'tarjeta'  =>'Tarjeta',
					'licencia' =>'Licencia',
					'razon'    =>'Razon',
					'dir_neg'  =>'Direccion Negocio'
					),
				'retornar'=>array(
						'id'          =>'patente'     ,
						'tarjeta'     =>'p_tarjeta'     ,
						'licencia'    =>'p_licencia'    ,
						'razon'       =>'p_razon'       ,
						'dir_neg'     =>'p_dir_neg'     ,
						'oficio'      =>'p_oficio'      ,
						'observa'     =>'p_observa'     ,
						'capital'     =>'p_capital'     ,
						'catastro'    =>'p_catastro'    ,
						'publicidad'  =>'p_publicidad'  ,
						'DATE_FORMAT(FECHA_ES,"%d/%m/%Y")'    =>'p_fecha_es'    ,
						'local'       =>'p_localt'    ,
						'clase'       =>'p_claset'    ,
						'negocio'     =>'p_negociot'  ,
						'tipo'        =>'p_tipot'     ,
						'repre'       =>'p_repre'  ,
						'repreced'    =>'p_repreced',
						'expclasi'    =>'p_expclasi',
						'exphor'      =>'p_exphor',
						'nro'         =>'p_nro',
						'c_codigo'    =>'contribu',
						'c_nombre'    =>'nombre',
						'c_rifci'     =>'rifci',
						'c_direccion' =>'direccion',
						'c_telefono'  =>'telefono',
						'c_nacionali' =>'nacionalit'
					),
				'p_uri'  =>array(4=>'<#contri#>'),
				'where'  =>'IF(<#contri#> = ".....", contribu LIKE "%" ,contribu LIKE <#contri#>)',
				'script'  =>array('cal_patente()','cal_nacionali()'),
				'titulo'  =>'Buscar Patente');
			
		$bPATENTE=$this->datasis->p_modbus($mPATENTE,'<#contri#>');
		$bPATENTE='<img src="/tortuga/assets/default/images/system-search.png"  alt="Busqueda de Patentes" title="Busqueda de Patentes" border="0" onclick="modbusdepenp()"/>';
		
		$mINMUEBLE=array(
				'tabla'   =>'v_inmueble_contri',
				'columnas' =>array(
					'id'       =>'Ref.',
					'ctainos'  =>'CtaInos',
					'direccion'=>'Direccion',
					'tipo_in'  =>'Tipo'
					),
				'filtro'  =>array(
					'id'       =>'Ref.',
					'ctainos'  =>'CtaInos',
					'direccion'=>'Direccion',
					'tipo_in'  =>'Tipo'
					),
				'retornar'=>array(
					'id'         =>'inmueble',
					'ctainos'    =>'i_ctainos',
					'direccion'  =>'i_direccion',
					'tipo_in'    =>'i_tipo_int',
					'no_predio'  =>'i_no_predio',
					'sector'     =>'i_sectort',
					'clase'      =>'i_claset',
					'tipo'       =>'i_tipot',
					'no_predio'  =>'i_monto',
					'c_codigo'   =>'contribu',
					'c_nombre'   =>'nombre',
					'c_rifci'    =>'rifci',
					'c_direccion'=>'direccion',
					'c_telefono' =>'telefono',
					'c_nacionali'=>'nacionalit'
					),
				'p_uri'  =>array(4=>'<#contri#>'),
				'where'  =>'IF(<#contri#> = ".....", contribu LIKE "%" ,contribu LIKE <#contri#>)',
				'script'  =>array('cal_inmueble()','cal_nacionali()'),
				'titulo'  =>'Buscar Inmueble');
		
		$bINMUEBLE=$this->datasis->p_modbus($mINMUEBLE,'<#contri#>');
		$bINMUEBLE='<img src="/tortuga/assets/default/images/system-search.png"  alt="Busqueda de Inmuebles" title="Busqueda de Inmuebles" border="0" onclick="modbusdepeni()"/>';

		$mVEHICULO=array(
				'tabla'   =>'v_vehiculo_contri',
				'columnas' =>array(
					'id'       =>'Ref.',
					'placa_act'=>'Placa',
					'marca'    =>'Marca',
					'modelo'   =>'Modelo',
					'color'    =>'Color'
					),
				'filtro'  =>array(
					'id'       =>'Ref.',
					'placa_act'=>'Placa',
					'marca'    =>'Marca',
					'modelo'   =>'Modelo',
					'color'    =>'Color'
					),
				'retornar'=>array(
					'id'       =>'vehiculo',
					'clase'    =>'v_claset',
					'marca'    =>'v_marca',
					'tipo'     =>'v_tipo',
					'modelo'   =>'v_modelo',
					'color'    =>'v_color',
					'capaci'   =>'v_capaci',
					'serial_m' =>'v_serial_m',
					'placa_act'=>'v_placa_act',
					'ano'      =>'v_ano',
					'peso'     =>'v_peso',
					'serial_c' =>'v_serial_c',
					'codigo'   =>'contribu',
					'nombre'   =>'nombre',
					'rifci'    =>'rifci',
					'direccion'=>'direccion',
					'telefono' =>'telefono',
					'nacionali'=>'nacionalit'
					),
				'p_uri'  =>array(4=>'<#contri#>'),
				'where'  =>'IF(<#contri#> = ".....", contribu LIKE "%" ,contribu LIKE <#contri#>)',
				'script' =>array('cal_vehiculo()','cal_nacionali()'),
				'titulo' =>'Buscar Vehiculo');
		
		$bVEHICULO=$this->datasis->p_modbus($mVEHICULO,'<#contri#>');
		$bVEHICULO='<img src="/tortuga/assets/default/images/system-search.png"  alt="Busqueda de Vehiculos" title="Busqueda de Vehiculos" border="0" onclick="modbusdepenv()"/>';

		$do = new DataObject("recibo");
		$do->rel_one_to_many('itrecibo'   , 'itrecibo'   , array('id' =>'id_recibo'));
		$do->pointer('patente'  ,'recibo.patente=patente.id'  ,'patente.tarjeta AS p_tarjeta,patente.licencia AS p_licencia,patente.razon AS p_razon,patente.dir_neg AS p_dir_neg,patente.capital AS p_capital,patente.monto AS p_monto,patente.fecha_es AS p_fecha_es,patente.oficio AS p_oficio,patente.local AS p_local,patente.negocio AS p_negocio,patente.registrado AS p_registrado,patente.observa AS p_observa,patente.clase AS p_clase,patente.tipo AS p_tipo,patente.catastro AS p_catastro,patente.publicidad AS p_publicidad,patente.recibo AS p_recibo,patente.repre AS p_repre,patente.repreced AS p_repreced,patente.expclasi AS p_expclasi,patente.exphor AS p_exphor,patente.nro AS p_nro,patente.fexpedicion p_fexpedicion,patente.fvencimiento p_fvencimiento','LEFT');
		$do->pointer('inmueble' ,'recibo.inmueble=inmueble.id','inmueble.ctainos AS i_ctainos,inmueble.direccion AS i_direccion,inmueble.no_predio AS i_no_predio,inmueble.sector AS i_sector,inmueble.tipo_in AS i_tipo_in,inmueble.no_hab AS i_no_hab,inmueble.clase AS i_clase,inmueble.tipo AS i_tipo','LEFT');
		$do->pointer('vehiculo' ,'recibo.vehiculo=vehiculo.id','vehiculo.clase AS v_clase,vehiculo.marca AS v_marca,vehiculo.tipo AS v_tipo,vehiculo.modelo AS v_modelo,vehiculo.color AS v_color,vehiculo.capaci AS v_capaci,vehiculo.serial_m AS v_serial_m,vehiculo.placa_ant AS v_placa_ant,vehiculo.placa_act AS v_placa_act,vehiculo.ano AS v_ano,vehiculo.peso AS v_peso,vehiculo.serial_c AS v_serial_c,vehiculo.monto AS v_monto,vehiculo.asovehi AS v_asovehi','LEFT');

		$edit = new DataDetails($this->tits, $do);

		$edit->back_url = site_url($this->url."filteredgrid");

		$edit->pre_process('insert','_valida');
		$edit->pre_process('update','_valida');
		$edit->post_process('insert','_post_insert');
		$edit->post_process('update','_post_update');
		$edit->post_process('delete','_post_delete');

		$edit->numero = new inputField('Recibo N&uacute;mero','numero');
		$edit->numero->when      =array('show');
		$edit->numero->type      ='inputhidden';
		
		$edit->id = new inputField('','id');
		$edit->id->mode   ='autohide';
		$edit->id->when   =array('show');
		$edit->id->db_name   ='recibo.id';
		$edit->id->type      ='inputhidden';
		
		$edit->contribu = new inputField('C&oacute;digo','contribu');
		$edit->contribu->rule      ='max_length[6]';
		$edit->contribu->size      =8;
		$edit->contribu->maxlength =6;
		$edit->contribu->append($bCONTRIBU);
		
		if($this->recibomodificontribu!='S'){
			$edit->contribu->readonly  =true;
			$edit->contribu->rule      ='required|max_length[6]';
		}
		

		$edit->fecha = new dateonlyField('Fecha','fecha');
		$edit->fecha->rule='chfecha';
		$edit->fecha->size =10;
		$edit->fecha->maxlength =8;
		$edit->fecha->insertValue=date('Y-m-d');
		
		$edit->tipo = new dropdownField('Concepto','tipo');
		$edit->tipo->options("SELECT codigo,CONCAT(codigo,'-',descrip) FROM tingresos ORDER BY grupo,descrip");//WHERE activo='S' 
		$edit->tipo->onchange="cal_concepto()";
		$edit->tipo->style="width:350px;";

		$edit->monto = new inputField('Monto','monto');
		$edit->monto->rule      ='required|max_length[19]|numeric_positive';
		$edit->monto->css_class ='inputnum';
		$edit->monto->size      =21;
		$edit->monto->maxlength =19;
		$edit->monto->value     =0;
		
		$edit->declaracion = new inputField('Declaraci&oacute;n','declaracion');
		$edit->declaracion->rule      ='required';
		$edit->declaracion->css_class ='inputnum';
		$edit->declaracion->size      =21;
		$edit->declaracion->maxlength =19;
		$edit->declaracion->value     =0;
		$edit->declaracion->onchange="cal_claseo()";

		$edit->observa = new textareaField('Observaci&oacute;n','observa');
		$edit->observa->cols = 80;
		$edit->observa->rows = 1;

		//INICIO CONTRIBUYENTES
		$edit->direccion = new textareaField('Direcci&oacute;n','direccion');
		$edit->direccion->cols = 40;
		$edit->direccion->rows = 1;

		$edit->nombre = new inputField('Nombre','nombre');
		$edit->nombre->rule='required|max_length[200]';
		$edit->nombre->size =50;
		$edit->nombre->maxlength =200;
		$edit->nombre->autocomplete=false;

		$crea = '<a href="javascript:creacontribu();" title="Agregar Contribuyente">'.image('add.png','#',array("border"=>0)).'</a>';
		$edit->rifci = new inputField('Rif/CI','rifci');
		$edit->rifci->rule      ='required|max_length[13]';
		$edit->rifci->size      =15;
		$edit->rifci->maxlength =13;
		$edit->rifci->append($crea);

		$edit->nacionali = new dropdownField('Nacionalidad','nacionali');
		$edit->nacionali->option('V','VENEZOLANA');
		$edit->nacionali->option('E','EXTRANJERA');
		$edit->nacionali->style="width:120px;";
		
		$edit->telefono = new textareaField('Telefono','telefono');
		$edit->telefono->rule = 'max_length[50]';
		$edit->telefono->cols = 20;
		$edit->telefono->rows = 1;
		
		$edit->oper = new dropdownField('Tipo Solvencia','oper');
		$edit->oper->option('','');
		$edit->oper->option('TRAMITES','TRAMITES o ADMINISTRATIVA');
		$edit->oper->option('REGISTRO','REGISTRO');
		$edit->oper->style="width:250px;";
		
		$edit->razonsocial = new dropdownField('Razon Social','razonsocial');
		$edit->razonsocial->option('COMERCIAL'   ,'COMERCIAL'   );
		$edit->razonsocial->option('DOMICILIARIO','DOMICILIARIO');
		$edit->razonsocial->style="width:120px;";
		
		$edit->tasam = new inputField('Tasa Mensual','tasam');
		$edit->tasam->rule      ='max_length[19]';
		//$edit->tasam->css_class ='inputnum';
		$edit->tasam->size      =21;
		$edit->tasam->maxlength =19;
		$edit->tasam->value     =0;
		
		$edit->rif = new inputField('Rif','rif');
		$edit->rif->rule='max_length[200]';
		$edit->rif->size      =20;
		$edit->rif->maxlength =200;
		
		$edit->nomfis = new inputField('Empresa','nomfis');
		$edit->nomfis->rule='max_length[200]';
		$edit->nomfis->size      =30;
		$edit->nomfis->maxlength =200;
		
		$edit->efectos = new inputField('Efectos','efectos');
		$edit->efectos->rule='max_length[200]';
		$edit->efectos->size      =80;
		$edit->efectos->maxlength =200;
		
		$edit->efectos2 = new inputField('Efectos Linea 2','efectos2');
		$edit->efectos2->rule='max_length[200]';
		$edit->efectos2->size      =80;
		$edit->efectos2->maxlength =200;
		
		//INICIO DETALLE
		
		$edit->d_ano = new inputField('A&ntilde;o','d_ano_<#i#>');
		$edit->d_ano->size      =6;
		$edit->d_ano->maxlength =6;
		$edit->d_ano->db_name   ='ano';
		$edit->d_ano->rel_id    ='itrecibo';
		$edit->d_ano->style     ='width:100%';
		$edit->d_ano->readonly  =true;
		
		$edit->d_tipo = new inputField('Tipo','d_tipo_<#i#>');
		$edit->d_tipo->size      =10;
		$edit->d_tipo->maxlength =20;
		$edit->d_tipo->db_name   ='tipo';
		$edit->d_tipo->rel_id    ='itrecibo';
		$edit->d_tipo->style     ='width:100%';
		$edit->d_tipo->readonly  =true;
		
		$edit->d_nro = new inputField('Nro','d_nro_<#i#>');
		$edit->d_nro->size      =2;
		$edit->d_nro->maxlength =2;
		$edit->d_nro->db_name   ='nro';
		$edit->d_nro->rel_id    ='itrecibo';
		$edit->d_nro->style     ='width:100%';
		$edit->d_nro->readonly  =true;
		
		$edit->d_descrip = new inputField('Descripcion','d_descrip_<#i#>');
		$edit->d_descrip->size      =20;
		$edit->d_descrip->db_name   ='descrip';
		$edit->d_descrip->rel_id    ='itrecibo';
		$edit->d_descrip->style     ='width:100%';
		$edit->d_descrip->readonly  =true;
		
		$edit->d_monto = new inputField('Monto','d_monto_<#i#>');
		$edit->d_monto->size      =15;
		$edit->d_monto->maxlength =10;
		$edit->d_monto->db_name   ='monto';
		$edit->d_monto->rel_id    ='itrecibo';
		$edit->d_monto->style     ='width:100%;text-align:right;';
		$edit->d_monto->value     =0;
		$edit->d_monto->onchange  = 'cal_total();';
		
		//INICIO PATENTE
		$edit->patente = new inputField('Patente','patente');
		$edit->patente->size      =6;
		$edit->patente->maxlength =6;
		$edit->patente->append($bPATENTE);
		$edit->patente->db_name   ='patente';
		
		$edit->p_tarjeta = new inputField('Tarjeta','p_tarjeta');
		$edit->p_tarjeta->size      =6;
		$edit->p_tarjeta->maxlength =6;
		//$edit->p_tarjeta->db_name   ='tarjeta';
		//$edit->p_tarjeta->rel_id    ='patente';
		$edit->p_tarjeta->pointer   =true;
		
		$edit->p_licencia = new inputField('Licencia','p_licencia');
		$edit->p_licencia->size      =5;
		$edit->p_licencia->maxlength =6;
		//$edit->p_licencia->db_name   ='licencia';
		//$edit->p_licencia->rel_id    ='patente';
		$edit->p_licencia->pointer   =true;
		
		$edit->p_razon = new inputField('Raz&oacute;n','p_razon');
		$edit->p_razon->size      =100;
		//$edit->p_razon->db_name   ='licencia';
		//$edit->p_razon->rel_id    ='patente';
		$edit->p_razon->pointer   =true;
		
		$edit->p_dir_neg = new inputField('Direcci&oacute;n','p_dir_neg');
		$edit->p_dir_neg->size      =100;
		//$edit->p_dir_neg->db_name   ='dir_neg';
		//$edit->p_dir_neg->rel_id    ='patente';
		$edit->p_dir_neg->pointer   =true;
		
		$edit->p_local = new dropdownField('Localizaci&oacute;n','p_local');
		$edit->p_local->options("SELECT codigo,nombre FROM local ORDER BY nombre");
		$edit->p_local->style="width:180px;";
		//$edit->p_local->rel_id    ='patente';
		//$edit->p_local->db_name   ='tarjeta';
		$edit->p_local->pointer   =true;
		
		$edit->p_negocio = new dropdownField('Negocio','p_negocio');
		$edit->p_negocio->options("SELECT codigo,nombre FROM negocio ORDER BY nombre");
		$edit->p_negocio->style="width:180px;";
		//$edit->p_negocio->rel_id    ='patente';
		//$edit->p_negocio->db_name   ='tarjeta';
		$edit->p_negocio->pointer   =true;
		
		$edit->p_clase = new dropdownField('Clase','p_clase');
		$edit->p_clase->options("SELECT codigo,nombre FROM claseo ORDER BY nombre");
		$edit->p_clase->style="width:120px;";
		//$edit->p_clase->rel_id    ='patente';
		//$edit->p_clase->db_name   ='tarjeta';
		$edit->p_clase->pointer   =true;
		$edit->p_clase->onchange="cal_claseo()";
		
		$edit->p_tipo = new dropdownField('Tipo','p_tipo');
		$edit->p_tipo->options(array(''=>'','A'=>'A','B'=>'B'));
		$edit->p_tipo->style="width:120px;";
		//$edit->p_tipo->rel_id    ='patente';
		//$edit->p_tipo->db_name   ='tarjeta';
		$edit->p_tipo->pointer   =true;
		
		$edit->p_oficio = new inputField('Oficio','p_oficio');
		$edit->p_oficio->size      =20;
		//$edit->p_oficio->db_name   ='oficio';
		//$edit->p_oficio->rel_id    ='patente';
		$edit->p_oficio->pointer   =true;
		
		$edit->p_catastro = new inputField('Catastro','p_catastro');
		$edit->p_catastro->size      =20;
		//$edit->p_catastro->db_name   ='catastro';
		//$edit->p_catastro->rel_id    ='patente';
		$edit->p_catastro->pointer   =true;
		       
		$edit->p_publicidad = new inputField('Publicidad','p_publicidad');
		$edit->p_publicidad->size      =20;
		//$edit->p_publicidad->db_name   ='publicidad';
		//$edit->p_publicidad->rel_id    ='patente';
		$edit->p_publicidad->pointer   =true;
		
		$edit->p_observa = new inputField('Observaci&oacute;n','p_observa');
		$edit->p_observa->size      =100;
		//$edit->p_observa->db_name   ='oficio';
		//$edit->p_observa->rel_id    ='patente';
		$edit->p_observa->pointer   =true;
		
		$edit->p_repre = new inputField('Representante','p_repre');
		$edit->p_repre->size      =20;
		$edit->p_repre->pointer   =true;
		
		$edit->p_expclasi = new dropdownField('Clasificacion Expendido','p_expclasi');
		$edit->p_expclasi->option("EXPENDIDO AL MAYOR Y DETAL","EXPENDIDO AL MAYOR Y DETAL");
		$edit->p_expclasi->option("EXPENDIDO DE CONSUMO","EXPENDIDO DE CONSUMO");
		$edit->p_expclasi->size      =20;
		$edit->p_expclasi->pointer   =true;
		
		$edit->p_exphor = new inputField('Horario Espendido','p_exphor');
		$edit->p_exphor->size      =20;
		$edit->p_exphor->pointer   =true;
		
		$edit->p_repre = new inputField('Representante','p_repre');
		$edit->p_repre->size      =20;
		$edit->p_repre->pointer   =true;
		
		$edit->p_repreced = new inputField('Ced. Representante','p_repreced');
		$edit->p_repreced->size      =20;
		$edit->p_repreced->pointer   =true;
		       
		$edit->p_capital = new inputField('Capital','p_capital');
		$edit->p_capital->size      =20;
		//$edit->p_capital->db_name   ='capital';
		//$edit->p_capital->rel_id    ='patente';
		$edit->p_capital->css_class ='inputnum';
		$edit->p_capital->pointer   =true;
		       
		$edit->p_fecha_es = new dateonlyField('Fecha_es','p_fecha_es');
		$edit->p_fecha_es->rule='chfecha';
		$edit->p_fecha_es->size =10;
		$edit->p_fecha_es->maxlength =8;
		$edit->p_fecha_es->insertValue=date('Y-m-d');
		//$edit->p_fecha_es->db_name   ='fecha_es';
		//$edit->p_fecha_es->rel_id    ='patente';
		$edit->p_fecha_es->pointer   =true;
		/*
		$edit->p_kardex = new inputField('C&oacute;digo Kardex','p_kardex');
		$edit->p_kardex->size      =20;
		$edit->p_kardex->css_class ='inputnum';
		$edit->p_kardex->pointer   =true;
		*/
		$edit->p_nro = new inputField('Nro','p_nro');
		$edit->p_nro->size      =5;
		//$edit->p_nro->css_class ='inputnum';
		$edit->p_nro->pointer   =true;
		
		$edit->p_fexpedicion = new dateonlyField('Fecha Expedici&oacute;n','p_fexpedicion');
		$edit->p_fexpedicion->rule='chfecha';
		$edit->p_fexpedicion->size =10;
		$edit->p_fexpedicion->maxlength =8;
		$edit->p_fexpedicion->insertValue=date('Y-m-d');
		$edit->p_fexpedicion->pointer   =true;
		
		$edit->p_fvencimiento = new dateonlyField('Fecha Vencimiento','p_fvencimiento');
		$edit->p_fvencimiento->rule='chfecha';
		$edit->p_fvencimiento->size =10;
		$edit->p_fvencimiento->maxlength =8;
		$edit->p_fvencimiento->insertValue=date('Y-m-d');
		$edit->p_fvencimiento->pointer   =true;
		
		//INICIO INMUEBLE
		$edit->inmueble = new inputField('Inmueble','inmueble');
		$edit->inmueble->size      =6;
		$edit->inmueble->maxlength =6;
		$edit->inmueble->append($bINMUEBLE);
		$edit->inmueble->db_name   ='inmueble';
		       
		$edit->i_ctainos = new inputField('Cuenta Inos','i_ctainos');
		$edit->i_ctainos->rule      ='max_length[7]';
		$edit->i_ctainos->size      =9;
		$edit->i_ctainos->maxlength =7;
		//$edit->i_ctainos->db_name   ='ctainos';
		//$edit->i_ctainos->rel_id    ='inmueble';
		$edit->i_ctainos->pointer   =true;
                       
		$edit->i_direccion = new inputField('Direcci&oacute;n','i_direccion');
		$edit->i_direccion->rule='max_length[50]';
		$edit->i_direccion->size =100;
		$edit->i_direccion->maxlength =50;
		//$edit->i_direccion->db_name   ='direccion';
		//$edit->i_direccion->rel_id    ='inmueble';
		$edit->i_direccion->pointer   =true;
                     
		$edit->i_no_predio = new inputField('Nro. Promedio','i_no_predio');
		$edit->i_no_predio->rule='max_length[10]';
		$edit->i_no_predio->size =12;
		$edit->i_no_predio->maxlength =10;
		//$edit->i_no_predio->db_name   ='no_predio';
		//$edit->i_no_predio->rel_id    ='inmueble';
		$edit->i_no_predio->pointer   =true;
                       
		$edit->i_sector = new dropdownField('Sector','i_sector');
		$edit->i_sector->options("SELECT codigo,nombre FROM local ORDER BY nombre");
		//$edit->i_sector->db_name   ='sector';
		//$edit->i_sector->rel_id    ='inmueble';
		$edit->i_sector->pointer   =true;
                       
		$edit->i_tipo_in = new dropdownField('Tipo Inmueble','i_tipo_in');
		$edit->i_tipo_in->options("SELECT tipoin,tipoin d FROM tipoin ORDER BY tipoin");
		//$edit->i_tipo_in->db_name   ='tipo_in';
		//$edit->i_tipo_in->rel_id    ='inmueble';
		$edit->i_tipo_in->pointer   =true;
                       
		$edit->i_no_hab = new inputField('Nro.Habitacion','i_no_hab');
		$edit->i_no_hab->rule='max_length[11]';
		$edit->i_no_hab->size =13;
		$edit->i_no_hab->maxlength =11;
		//$edit->i_no_hab->db_name   ='no_hab';
		//$edit->i_no_hab->rel_id    ='inmueble';
		$edit->i_no_hab->pointer   =true;

		$edit->i_clase = new dropdownField('Clase','i_clase');
		$edit->i_clase->options("SELECT codigo,nombre FROM claseo ORDER BY nombre");
		//$edit->i_clase->db_name   ='clase';
		//$edit->i_clase->rel_id    ='inmueble';
		$edit->i_clase->pointer   =true;
		$edit->i_clase->onchange="cal_claseo()";
                       
		$edit->i_tipo = new dropdownField('Tipo','i_tipo');
		$edit->i_tipo->options(array(''=>'','A'=>'A','B'=>'B'));
		$edit->i_tipo->style="width:120px;";
		//$edit->i_tipo->db_name   ='tipo';
		//$edit->i_tipo->rel_id    ='inmueble';
		$edit->i_tipo->pointer   =true;
		       
		$edit->i_monto = new inputField('Monto','i_monto');
		$edit->i_monto->rule='max_length[8]';
		$edit->i_monto->size =10;
		$edit->i_monto->maxlength =8;
		//$edit->i_monto->db_name   ='monto';
		//$edit->i_monto->rel_id    ='inmueble';
		$edit->i_monto->pointer   =true;
		
		//INICIO VEHICULO
		$edit->vehiculo = new inputField('Vehiculo','vehiculo');
		$edit->vehiculo->size      =6;
		$edit->vehiculo->maxlength =6;
		$edit->vehiculo->append($bVEHICULO);
		$edit->vehiculo->db_name   ='vehiculo';
		$edit->vehiculo->readonly  = true;
		
		$edit->v_clase = new dropdownField('Clase','v_clase');
		$edit->v_clase->options("SELECT codigo,nombre FROM clase ORDER BY nombre");
		$edit->v_clase->style="width:200px;";
		//$edit->v_clase->db_name   ='clase';
		//$edit->v_clase->rel_id    ='vehiculo';
		$edit->v_clase->pointer   =true;

		$edit->v_marca = new inputField('Marca','v_marca');
		//$edit->v_marca->options("SELECT trim(marca),marca m FROM marca ORDER BY marca");
		//$edit->v_marca->style="width:120px;";
		//$edit->v_marca->db_name   ='marca';
		//$edit->v_marca->rel_id    ='vehiculo';
		$edit->v_marca->pointer   =true;
		$edit->v_marca->size      =20;
		$edit->v_marca->maxlength =30;
        
		$edit->v_tipo = new inputField('Tipo','v_tipo');
		//$edit->v_tipo->options("SELECT tipo,tipo m FROM tipo ORDER BY tipo");
		//$edit->v_tipo->style="width:120px;";
		//$edit->v_tipo->db_name   ='tipo';
		//$edit->v_tipo->rel_id    ='vehiculo';
		$edit->v_tipo->pointer   =true;
		$edit->v_tipo->size      =20;
		$edit->v_tipo->maxlength =30;

		$edit->v_modelo = new inputField('Modelo','v_modelo');
		$edit->v_modelo->rule='max_length[10]';
		$edit->v_modelo->size =12;
		$edit->v_modelo->maxlength =10;
		//$edit->v_modelo->db_name   ='modelo';
		//$edit->v_modelo->rel_id    ='vehiculo';
		$edit->v_modelo->pointer   =true;
        
		$edit->v_color = new inputField('Color','v_color');
		$edit->v_color->rule='max_length[20]';
		$edit->v_color->size =22;
		$edit->v_color->maxlength =20;
		//$edit->v_color->db_name   ='color';
		//$edit->v_color->rel_id    ='vehiculo';
		$edit->v_color->pointer   =true;
                       
		$edit->v_capaci = new inputField('Capacidad','v_capaci');
		$edit->v_capaci->rule='max_length[11]';
		$edit->v_capaci->size =13;
		$edit->v_capaci->maxlength =11;
		//$edit->v_capaci->db_name   ='capaci';
		//$edit->v_capaci->rel_id    ='vehiculo';
		$edit->v_capaci->pointer   =true;
                       
		$edit->v_serial_m = new inputField('Serial Motor','v_serial_m');
		$edit->v_serial_m->size      =40;
		//$edit->v_serial_m->db_name   ='serial_m';
		//$edit->v_serial_m->rel_id    ='vehiculo';
		$edit->v_serial_m->pointer   =true;
                       
		$edit->v_placa_ant = new inputField('Placa Anterior','v_placa_ant');
		$edit->v_placa_ant->rule='max_length[7]';
		$edit->v_placa_ant->size =9;
		$edit->v_placa_ant->maxlength =7;
		//$edit->v_placa_ant->db_name   ='placa_ant';
		//$edit->v_placa_ant->rel_id    ='vehiculo';
		$edit->v_placa_ant->pointer   =true;
                       
		$edit->v_placa_act = new inputField('Placa','v_placa_act');
		$edit->v_placa_act->rule='max_length[9]';
		$edit->v_placa_act->size =11;
		$edit->v_placa_act->maxlength =9;
		//$edit->v_placa_act->db_name   ='placa_act';
		//$edit->v_placa_act->rel_id    ='vehiculo';
		$edit->v_placa_act->pointer   =true;
                       
		$edit->v_ano = new inputField('A&ntilde;o','v_ano');
		$edit->v_ano->rule='max_length[4]';
		$edit->v_ano->size =6;
		$edit->v_ano->maxlength =4;
		//$edit->v_ano->db_name   ='ano';
		//$edit->v_ano->rel_id    ='vehiculo';
		$edit->v_ano->pointer   =true;
                       
		$edit->v_peso = new inputField('Peso','v_peso');
		$edit->v_peso->rule='max_length[8]';
		$edit->v_peso->size =10;
		$edit->v_peso->maxlength =8;
		//$edit->v_peso->db_name   ='peso';
		//$edit->v_peso->rel_id    ='vehiculo';
		$edit->v_peso->pointer   =true;
                       
		$edit->v_serial_c = new inputField('Serial Carroceria','v_serial_c');
		$edit->v_serial_c->size      =40;
		//$edit->v_serial_c->db_name   ='serial_c';
		//$edit->v_serial_c->rel_id    ='vehiculo';
		$edit->v_serial_c->pointer   =true;
		
		$edit->ano = new inputField('A&ntilde;o','ano');
		$edit->ano->insertValue=$this->datasis->traevalor('EJERCICIO');
		$edit->ano->size=4;
		$edit->ano->maxlength=4;
		
		$m=array('Enero','Febrero','Marzo','Abril','Mayo','Junio','Julio','Agosto','Septiembre','Octubre','Noviembre','Diciembre');
		for($i=1;$i<=12;$i++){
			$campo='m_'.str_pad($i,2,'0',STR_PAD_LEFT);
			$edit->$campo =new checkboxField($m[$i-1],$campo,'S','N');
			$edit->$campo->insertValue = "N";
			$edit->$campo->onchange="cal_ch('".str_pad($i,2,'0',STR_PAD_LEFT)."')";
		}
		
		$m=array('Trimestre 1','Trimestre 2','Trimestre 3','Trimestre 4');
		for($i=1;$i<=4;$i++){
			$campo='t_'.str_pad($i,2,'0',STR_PAD_LEFT);
			$edit->$campo =new checkboxField($m[$i-1],$campo,'S','N');
			$edit->$campo->insertValue = "N";
			$edit->$campo->onchange="cal_ch2('".str_pad($i,2,'0',STR_PAD_LEFT)."')";
		}
		
		//TEMPORALES
		foreach($this->temp as $k=>$v)
		$edit->$v = new hiddenField('',$v);
		
		if($edit->_status=='show'){
			$id  =$edit->get_from_dataobjetct('id');
			$tipo=$edit->get_from_dataobjetct('tipo');
			if($tipo=='15'){
				$action = "javascript:window.location='" .site_url($this->url.'/calcomania/'.$edit->rapyd->uri->get_edited_id()). "'";
				$edit->button_status("btn_calcomania",'Pagar Calcomania',$action,"TL","show");
			}
		}
		
		$status=$edit->get_from_dataobjetct('status');
		
		if($this->datasis->puede(387)){
			$print_url=site_url($this->url.'datarecibo/modify/'.$id);
			$action   = "javascript:window.location='${print_url}'";
			$edit->button('btn_recibo', 'Modificar Recibo', $action, 'TR');
		}
		
		if($status=='P'){
			$edit->buttons('modify','delete');
			if($this->datasis->traevalor('RECIBOUSABTNIMPRIMIR')=='S'){
				if ($edit->_status == 'show'){
					$print_url=site_url($this->url.'dataprint/modify/'.$id);
					$action   = "javascript:window.location='${print_url}'";
					$edit->button('btn_print', 'Imprimir Recibo', $action, 'TR');
					
					$print_url=site_url($this->url.'dataprint_solvencia/modify/'.$id);
					$action   = "javascript:window.location='${print_url}'";
					$edit->button('btn_print', 'Imprimir Solvencia', $action, 'TR');
				}
			}
			
			$action = "javascript:window.location='" .site_url($this->url.'/anular/'.$edit->rapyd->uri->get_edited_id()). "'";
			$edit->button_status("btn_anular",'Anular',$action,"TR","show");
			
			if($this->datasis->puede(389)){
				$edit->buttons('delete');
			}
		}
		
		$edit->buttons('add', 'save', 'undo', 'back');
		$edit->build();
		
		$conten["form"]  =&$edit;
		$smenu['link']   =barra_menu('80B');
		$data['smenu']   = $this->load->view('view_sub_menu', $smenu,true);
		$conten['temp']  =$this->temp;
		$data['content'] = $this->load->view('view_recibo', $conten,true);
		$data['title']   = $this->tits;
		$data["head"]    = script('jquery.js').script('jquery-ui.js').script('plugins/jquery.numeric.pack.js').script('plugins/jquery.floatnumber.js').script('plugins/jquery.meiomask.js').style('vino/jquery-ui.css').$this->rapyd->get_head();
		$this->load->view('view_ventanas', $data);
	}
	
	function _valida($do){
		$error  ='';
		$patente=$inmueble=$vehiculo=array();
		$d      =$do->get_all();
		$p      =$_POST;

		if($do->get('id')>0){
		}else{
			$usr=$this->session->userdata('usuario');
			$do->set('user',$usr);
		}
		
		if(!isset($d['id']))
		$d['id']=null;
		
		if($d['tipo']==6 && empty($d['oper']))
			$error.="<div class='alert' >Error debe seleccionar un tipo de solvencia</div>";
		
		$error.=$this->ch_contribu($d['rifci'],$d['contribu']);
		$error.=$this->ch_recibo($d['id'],$d['numero']);
		
		$patente =$this->extraepiso($p,'p_');
		$patente['id']   =$d['patente'];
		if(strlen($patente['tarjeta'])>0)
		$error  .=$this->ch_patente($patente,$d['contribu']);
		
		$inmueble =$this->extraepiso($p,'i_');
		$inmueble['id']  =$d['inmueble'];
		if(strlen($inmueble['direccion'])>0)
		$error   .=$this->ch_inmueble($inmueble,$d['contribu']);
		
		$vehiculo =$this->extraepiso($p,'v_');
		$vehiculo['id']  =$d['vehiculo'];
		if(strlen($vehiculo['placa_act'])>0)
		$error   .=$this->ch_vehiculo($vehiculo,$d['contribu']);
		
		if(strlen($d['contribu'])==6)
		$contri=$d['contribu'];
		else{
			$codigo=$this->datasis->dameval("SELECT codigo FROM contribu WHERE rifci=".$this->db->escape($d['rifci']));
			if(strlen($codigo)>0){
				$do->data['contribu']=$codigo;
				//$do->set('contribu',$codigo);
			}elseif($this->recibomodificontribu!='S'){
				$contri=$this->datasis->dameval("SELECT LPAD(valor,6,0) FROM serie LEFT JOIN contribu ON LPAD(codigo,6,0)=LPAD(valor,6,0) WHERE valor<65535 AND codigo IS NULL LIMIT 1");
				$do->set('contribu',$contri);
			}	
		}
		
		if(empty($error)){
			if($this->datasis->traevalor('RECIBOUSABTNIMPRIMIR')=='S'){
			
			}else{
				if(empty($d['numero'])){
					$tipoe=$this->db->escape($d['tipo']);
					$contador2=$this->datasis->dameval("SELECT contador FROM tingresos WHERE codigo=$tipoe");
					$prefijo  =$this->datasis->dameval("SELECT prefijo FROM tingresos WHERE codigo=$tipoe");
					$contador ='nr'.$contador2;
					$nrecibo  = $this->datasis->fprox_numero($contador);
					$do->set('numero',$prefijo.$nrecibo);
				}
			}
			

			foreach($this->temp as $k=>$v){
				unset($patente[substr($v,2,100)]);
				unset($inmueble[substr($v,2,100)]);
				unset($vehiculo[substr($v,2,100)]);
			}
			
			$contribu=array(
				'codigo'     =>$d['contribu'],
				'nombre'     =>$d['nombre'],
				'rifci'      =>$d['rifci'],
				'direccion'  =>$d['direccion'],
				'telefono'   =>$d['telefono'],
				'nacionali'  =>$d['nacionali']
				);
			
			$c_codigo=$this->g_contribu($contribu);
			$contribu['codigo']=$c_codigo;
			
			if(strlen($patente['tarjeta'])>0){
				$p_id   =$this->g_patente($patente,$contribu);
				$patente['id']=$p_id;
			}
			
			if(strlen($inmueble['direccion'])>0){
				$i_id   =$this->g_inmueble($inmueble,$contribu);
				$inmueble['id']=$i_id;
			}
			
			if(strlen($vehiculo['placa_act'])>0){
				$v_id   =$this->g_vehiculo($vehiculo,$contribu);
				$vehiculo['id']=$v_id;
			}
			
			if(empty($numero)){
				$do->set('patente' ,$patente['id']);
				$do->set('inmueble',$inmueble['id']);
				$do->set('vehiculo',$vehiculo['id']);
				
			}
		}else{
			$edit->error_string=$error;
			$do->error_message_ar['pre_ins']=$error;
			$do->error_message_ar['pre_upd']=$error;
			return false;
		}
		
	}
	
	function extraepiso($d,$s){
		$a=array();
		foreach($d as $k=>$v)
		if(substr($k,0,2)==$s)
		$a[substr($k,2,100)]=$v;
	
		return $a;
	}
	
	function ch_contribu($rifci,$codigo){
		$error='';
		$rifcie=$this->db->escape($rifci);
		
		if(empty($codigo))
		if($this->datasis->dameval("SELECT LENGTH(codigo) FROM contribu WHERE rifci=$rifcie LIMIT 1")>0)
			$error.="El RIF/CIya existe";
		
		return $error;
	}
	
	function ch_recibo($id,$numero){
		$error='';
		$numeroe=$this->db->escape($numero);
		
		if(empty($id) && !empty($numero)){
			$r_id=$this->datasis->dameval("SELECT id FROM recibo WHERE numero=$numeroe LIMIT 1");
			if($r_id>0)
			$error.="El Numero $numero ya existe para la Referencia $r_id";
		}
		return $error;
	}
	
	function ch_patente($d){
		$error='';
		foreach($d AS $key=>$value){
			$$key=trim("$value");
			$k=$key."e";
			$$k=$this->db->escape(trim($value));
		}
		
		if(empty($id) && strlen($tarjeta)>0){
			$p_id=$this->datasis->dameval("SELECT id FROM patente WHERE tarjeta=$tarjetae LIMIT 1");
			if($p_id>0)
			$error.="La tarjeta $tarjeta ya existe para la Referencia $p_id";
		}else{
			
		}
		return $error;
	}
	
	function ch_inmueble($d){
		$error='';
		//foreach($d AS $key=>$value){
		//	$$key=trim("$value");
		//	$k=$key."e";
		//	$$k=$this->db->escape(trim($value));
		//}
		//
		//if(empty($id) && strlen($tarjeta)>0){
		//	$p_id=$this->datasis->dameval("SELECT id FROM patente WHERE tarjeta=$tarjetae LIMIT 1");
		//	if($p_id>0)
		//	$error.="La tarjeta $tarjeta ya existe para la Referencia $p_id";
		//}else{
		//	
		//}
		return $error;
	}
	
	function ch_vehiculo($d){
		$error='';
		
		if(empty($d['placa_act']) && strlen($d['placa_act'])>0){
			$v_id=$this->datasis->dameval("SELECT id FROM vehiculo WHERE placa=".$this->db->escape($d['placa_act'])." LIMIT 1");
			if($v_id>0)
			$error.="La Placa ".$d['placa_act']." ya existe para la Referencia $v_id";
		}else{
			
		}
		return $error;
	}
	
	function g_contribu($d){
		foreach($d AS $key=>$value){
			$$key=trim($value);
			$k=$key."e";
			$$k=$this->db->escape(trim($value));
		}
		
		if(strlen($codigo)==6)
		$contri=$codigo;
		else
		$contri=$this->datasis->dameval("SELECT LPAD(valor,6,0) FROM serie LEFT JOIN contribu ON LPAD(codigo,6,0)=LPAD(valor,6,0) WHERE valor<65535 AND codigo IS NULL LIMIT 1");
		
		$contrie=$this->db->escape($contri);
		
		$sprv=$this->db->query("
		INSERT INTO contribu (`codigo`,`nombre`,`rifci`,`nacionali`,`direccion`,`telefono`) 
		VALUES ($contrie,$nombree,$rifcie,$nacionalie,$direccione,$telefonoe) 
		ON DUPLICATE KEY UPDATE nombre=$nombree,rifci=$rifcie,nacionali=$nacionalie,direccion=$direccione,telefono=$telefonoe
		");
		
		return $contri;
	}
	
	function g_recibo($id,$recibo,$contribu,$patente,$inmueble,$vahiculo){
		$data         =array();
		$data         =$recibo;
		$data['id']   =$id;
		$data['contribu']    =$contribu['codigo'   ];
		$data['nombre'    ]  =$contribu['nombre'   ];
		$data['rifci'     ]  =$contribu['rifci'    ];
		$data['nacionali' ]  =$contribu['nacionali'];
		$data['direccion' ]  =$contribu['direccion'];
		$data['telefono'  ]  =$contribu['telefono' ];
		$data['patente'   ]  =$patente['id'        ];
		$data['inmueble'  ]  =$inmueble['id'       ];
		$data['vehiculo'  ]  =$vehiculo['id'       ];
		
		if($id>0){
			$this->db->where('id', $id);
			$this->db->update('recibo', $data); 
		}else{
			$this->db->insert('recibo', $data);
			$id =$this->db->insert_id();
		}
		
	}
	
	function g_patente($d,$c){
		$id  =$d['id'];
		$data=$d;
		$data['contribu']    =$c['codigo'   ];
		$data['nombre_pro']  =$c['nombre'   ];
		$data['cedula'    ]  =$c['rifci'    ];
		$data['nacionali' ]  =$c['nacionali'];
		$data['dir_pro'   ]  =$c['direccion'];
		$data['telefonos' ]  =$c['telefono' ];
		$data['fecha_es' ]   =human_to_dbdate($data['fecha_es']);
		$data['fvencimiento']=human_to_dbdate($data['fvencimiento']);
		$data['fexpedicion'] =human_to_dbdate($data['fexpedicion']);
		
		if($id>0){
			$this->db->where('id', $id);
			$this->db->update('patente', $data); 
		}else{
			$this->db->insert('patente', $data);
			$id =$this->db->insert_id();
		}
		return $id;
	}
	
	function g_inmueble($d,$c){
		$id  =$d['id'];
		$data=$d;
		$data['contribu']    =$c['codigo'   ];
		
		if($id>0){
			$this->db->where('id', $id);
			$this->db->update('inmueble', $data); 
		}else{
			$this->db->insert('inmueble', $data);
			$id =$this->db->insert_id();
		}
		return $id;
	}
	
	function g_vehiculo($d,$c){
		$id  =$d['id'];
		$data=$d;
		$data['contribu']    =$c['codigo'   ];
		
		if($id>0){
			$this->db->where('id', $id);
			$this->db->update('vehiculo', $data); 
		}else{
			$this->db->insert('vehiculo', $data);
			$id =$this->db->insert_id();
		}
		return $id;
	}
	
	function creacalco($id){
		
	}
	

	function _post_insert($do){
		$n     =$do->get('numero');
		$id    =$do->get('id');
		$monto =$do->get('monto');
		$primary =implode(',',$do->pk);
		logusu($do->table,"Creo $this->tits $primary numero $n monto $monto id $id");
		if($this->datasis->traevalor('RECIBOUSABTNIMPRIMIR')=='S')
		redirect($this->url."dataprint/modify/$id");
	}
	function _post_update($do){
		$n     =$do->get('numero');
		$id    =$do->get('id');
		$monto =$do->get('monto');
		$primary =implode(',',$do->pk);
		logusu($do->table,"Modifico $this->tits $primary numero $n monto $monto  id $id");
		if($this->datasis->traevalor('RECIBOUSABTNIMPRIMIR')=='S')
		redirect($this->url."dataprint/modify/$id");
	}
	function _post_delete($do){
		$n     =$do->get('numero');
		$monto =$do->get('monto');
		$primary =implode(',',$do->pk);
		
		$this->db->simple_query("DELETE FROM itabonos WHERE recibo=$n");
		logusu($do->table,"Elimino $this->tits $primary numero $n monto $monto");
	}
	
	function calcomania($id){
		$ide    = $this->db->escape($id);
		$numero ='C'.$this->datasis->fprox_numero('nrC');
		$query="
		INSERT INTO recibo (`numero`,`id`,`fecha`,`contribu`,`tipo`,`monto`,`observa`,`direccion`,`nombre`,`rifci`,`nacionali`,`telefono`,`user`,`estampa`,`status`)
		SELECT '$numero', '' id, now(), `contribu`, 29 tipo,(SELECT SUM(valor) FROM valores WHERE nombre='MONTOCALCOMANIA') monto, 'PAGO DE CALCOMANIA', direccion,
		`nombre`, `rifci`, `nacionali`, `telefono`, 'TORTUGA', now(), 'P' FROM recibo WHERE id=$ide";
		
		$this->db->query($query);
		$idn=$this->db->insert_id();
		redirect($this->url.'dataedit/show/'.$idn);
	}
	
	function anular($id){
		$ide   =$this->db->escape($id);
		$status=$this->datasis->dameval("SELECT status FROM recibo WHERE id=$ide");
		$error ='';
		if($status=='P'){
			$this->db->update('recibo',array('status'=>'A'),array('id'=>$id));
		}else{
		}
		
		if(empty($error)){
			logusu('recibo',"Anulo ingreso id $id");
			redirect($this->url.'dataedit/show/'.$id);
		}else{
			logusu('recibo',"Anulo recibo id $id con ERROR $error");
			$data['content'] = '<div class="alert">'.$error.'</div></br>'.anchor($this->url."dataedit/show/$id",'Regresar');
			$data['title']   = " $this->tits";
			$data["head"]    = $this->rapyd->get_head();
			$this->load->view('view_ventanas', $data);
		}
	}

	function dataprint($st,$uid){
		$this->rapyd->load('dataedit');

		$edit = new DataEdit('Imprimir Recibo', 'recibo');
		//$id=$edit->get_from_dataobjetct('id');
		//$urlid=$edit->pk_URI();
		$id   =$uid;
		$urlid=$uid;
		
		
		$url=site_url('forma1/ver/IRECIBO/'.$urlid);
		$edit->back_url = site_url($this->url.'dataedit/show/'.$uid);

		$edit->back_save   = true;
		$edit->back_delete = true;
		$edit->back_cancel = true;
		$edit->back_cancel_save   = true;
		$edit->back_cancel_delete = true;
		//$edit->on_save_redirect   = false;

		$edit->post_process('update','_post_print_update');
		$edit->pre_process('insert' ,'_pre_print_insert');
		//$edit->pre_process('update' ,'_pre_print_update');
		$edit->pre_process('delete' ,'_pre_print_delete');

		$edit->container = new containerField('impresion','La descarga se realizara en 5 segundos, en caso de no hacerlo haga click '.anchor('forma1/ver/IRECIBO/'.$urlid,'aqui'));

		$edit->numero = new inputField('Recibo N&uacute;mero','numero');
		$edit->numero->rule        ='max_length[12]|required';
		$edit->numero->size        =14;
		$edit->numero->maxlength   =12;
		$edit->numero->autocomplete=false;

		$edit->id = new inputField('Ref','numero');
		$edit->id->rule='max_length[8]';
		$edit->id->mode='autohide';
		$edit->id->size =10;
		$edit->id->maxlength =8;

		$edit->fecha = new dateField('Fecha','fecha');
		$edit->fecha->rule = 'chfecha';
		$edit->fecha->mode = 'autohide';
		$edit->fecha->size = 10;
		$edit->fecha->maxlength =8;
		
		$edit->tipo = new dropdownField('Concepto','tipo');
		$edit->tipo->options("SELECT codigo,CONCAT(codigo,'-',descrip) FROM tingresos ORDER BY grupo,descrip");//WHERE activo='S' 
		$edit->tipo->onchange="cal_concepto()";
		$edit->tipo->style="width:350px;";
		$edit->tipo->mode='autohide';
		
		$edit->nombre = new inputField('Nombre','nombre');
		$edit->nombre->rule='required|max_length[200]';
		$edit->nombre->size =50;
		$edit->nombre->maxlength =200;
		$edit->nombre->autocomplete=false;
		$edit->nombre->mode='autohide';

		$edit->rifci = new inputField('Rif/CI','rifci');
		$edit->rifci->rule      ='required|max_length[13]';
		$edit->rifci->size      =15;
		$edit->rifci->maxlength =13;
		$edit->rifci->mode='autohide';

		$edit->monto = new inputField('Monto','monto');
		$edit->monto->rule='max_length[12]|numeric';
		$edit->monto->css_class='inputnum';
		$edit->monto->size =14;
		$edit->monto->showformat='decimal';
		$edit->monto->mode='autohide';
		$edit->monto->maxlength =12;

		$edit->buttons('save', 'undo','back');
		$edit->build();

		$script= '<script type="text/javascript" >
		$(function() {
			setTimeout(\'window.location="'.$url.'"\',01);
		});
		</script>';

		$data['content'] = $edit->output;
		$data['head']    = $this->rapyd->get_head();
		$data['script']  = script('jquery.js').script('plugins/jquery.numeric.pack.js').script('plugins/jquery.floatnumber.js');
		$data['script'] .= $script;
		$data['title']   = $this->tits;
		$this->load->view('view_ventanas', $data);
	}
	
	function datarecibo($st,$uid){
		$this->rapyd->load('dataedit');

		$edit = new DataEdit('Modificar Recibo', 'recibo');
		//$id=$edit->get_from_dataobjetct('id');
		//$urlid=$edit->pk_URI();
		$id   =$uid;
		$urlid=$uid;
		
		$edit->back_url = site_url($this->url.'dataedit/show/'.$uid);

		$edit->back_save   = true;
		$edit->back_delete = true;
		$edit->back_cancel = true;
		$edit->back_cancel_save   = true;
		$edit->back_cancel_delete = true;
		//$edit->on_save_redirect   = false;

		$edit->post_process('update','_post_print_update');
		$edit->pre_process('insert' ,'_pre_print_insert');
		//$edit->pre_process('update' ,'_pre_print_update');
		$edit->pre_process('delete' ,'_pre_print_delete');

		$edit->numero = new inputField('Recibo N&uacute;mero','numero');
		$edit->numero->rule        ='max_length[12]|required';
		$edit->numero->size        =14;
		$edit->numero->maxlength   =12;
		$edit->numero->autocomplete=false;

		$edit->id = new inputField('Ref','numero');
		$edit->id->rule='max_length[8]';
		$edit->id->mode='autohide';
		$edit->id->size =10;
		$edit->id->maxlength =8;

		$edit->fecha = new dateField('Fecha','fecha');
		$edit->fecha->rule = 'chfecha';
		//$edit->fecha->mode = 'autohide';
		$edit->fecha->size = 10;
		$edit->fecha->maxlength =8;
		
		$edit->tipo = new dropdownField('Concepto','tipo');
		$edit->tipo->options("SELECT codigo,CONCAT(codigo,'-',descrip) FROM tingresos ORDER BY grupo,descrip");//WHERE activo='S' 
		$edit->tipo->onchange="cal_concepto()";
		$edit->tipo->style="width:350px;";
		$edit->tipo->mode='autohide';
		
		$edit->nombre = new inputField('Nombre','nombre');
		$edit->nombre->rule='required|max_length[200]';
		$edit->nombre->size =50;
		$edit->nombre->maxlength =200;
		$edit->nombre->autocomplete=false;
		$edit->nombre->mode='autohide';

		$edit->rifci = new inputField('Rif/CI','rifci');
		$edit->rifci->rule      ='required|max_length[13]';
		$edit->rifci->size      =15;
		$edit->rifci->maxlength =13;
		$edit->rifci->mode='autohide';

		$edit->monto = new inputField('Monto','monto');
		$edit->monto->rule='max_length[12]|numeric';
		$edit->monto->css_class='inputnum';
		$edit->monto->size =14;
		$edit->monto->showformat='decimal';
		$edit->monto->mode='autohide';
		$edit->monto->maxlength =12;

		$edit->buttons('save', 'undo','back');
		$edit->build();

		$script= '<script type="text/javascript" >
		
		</script>';

		$data['content'] = $edit->output;
		$data['head']    = $this->rapyd->get_head();
		$data['title']   = $this->tits;
		$this->load->view('view_ventanas', $data);
	}
	
	
	function _pre_print_insert($do){ return false;}
	function _pre_print_delete($do){ return false;}
	function _post_print_update($do){
			$id =$do->get('id');
			redirect($this->url."dataedit/show/$id");
	}
	
	function dataprint_solvencia($st,$uid){
		$this->rapyd->load('dataedit');

		$edit = new DataEdit('Imprimir Solvencia', 'recibo');
		//$id=$edit->get_from_dataobjetct('id');
		//$urlid=$edit->pk_URI();
		$id   =$uid;
		$urlid=$uid;
		
		$url=site_url('forma1/ver/SOLVENCIA/'.$urlid);
		$edit->back_url = site_url($this->url.'dataedit/show/'.$uid);

		$edit->back_save   = true;
		$edit->back_delete = true;
		$edit->back_cancel = true;
		$edit->back_cancel_save   = true;
		$edit->back_cancel_delete = true;
		//$edit->on_save_redirect   = false;

		$edit->post_process('update','_post_print_solv_update');
		$edit->pre_process('insert' ,'_pre_print_solv_insert');
		//$edit->pre_process('update' ,'_pre_print_update');
		$edit->pre_process('delete' ,'_pre_print_solv_delete');

		$edit->container = new containerField('impresion','La descarga se realizara en 5 segundos, en caso de no hacerlo haga click '.anchor('forma1/ver/SOLVENCIA/'.$urlid,'aqui'));

		$edit->solvencia = new inputField('Solvencia N&uacute;mero','solvencia');
		$edit->solvencia->rule        ='max_length[12]|required';
		$edit->solvencia->size        =14;
		$edit->solvencia->maxlength   =12;
		$edit->solvencia->autocomplete=false;
		
		$edit->numero = new inputField('Recibo N&uacute;mero','numero');
		$edit->numero->rule        ='max_length[12]|required';
		$edit->numero->size        =14;
		$edit->numero->maxlength   =12;
		$edit->numero->autocomplete=false;
		$edit->numero->mode='autohide';

		$edit->id = new inputField('Ref','numero');
		$edit->id->rule='max_length[8]';
		$edit->id->mode='autohide';
		$edit->id->size =10;
		$edit->id->maxlength =8;

		$edit->fecha = new dateField('Fecha','fecha');
		$edit->fecha->rule = 'chfecha';
		$edit->fecha->mode = 'autohide';
		$edit->fecha->size = 10;
		$edit->fecha->maxlength =8;
		
		$edit->tipo = new dropdownField('Concepto','tipo');
		$edit->tipo->options("SELECT codigo,CONCAT(codigo,'-',descrip) FROM tingresos ORDER BY grupo,descrip");//WHERE activo='S' 
		$edit->tipo->onchange="cal_concepto()";
		$edit->tipo->style="width:350px;";
		$edit->tipo->mode='autohide';
		
		$edit->nombre = new inputField('Nombre','nombre');
		$edit->nombre->rule='required|max_length[200]';
		$edit->nombre->size =50;
		$edit->nombre->maxlength =200;
		$edit->nombre->autocomplete=false;
		$edit->nombre->mode='autohide';

		$edit->rifci = new inputField('Rif/CI','rifci');
		$edit->rifci->rule      ='required|max_length[13]';
		$edit->rifci->size      =15;
		$edit->rifci->maxlength =13;
		$edit->rifci->mode='autohide';

		$edit->monto = new inputField('Monto','monto');
		$edit->monto->rule='max_length[12]|numeric';
		$edit->monto->css_class='inputnum';
		$edit->monto->size =14;
		$edit->monto->showformat='decimal';
		$edit->monto->mode='autohide';
		$edit->monto->maxlength =12;

		$edit->buttons('save', 'undo','back');
		$edit->build();

		$script= '<script type="text/javascript" >
		$(function() {
			setTimeout(\'window.location="'.$url.'"\',01);
		});
		</script>';

		$data['content'] = $edit->output;
		$data['head']    = $this->rapyd->get_head();
		$data['script']  = script('jquery.js').script('plugins/jquery.numeric.pack.js').script('plugins/jquery.floatnumber.js');
		$data['script'] .= $script;
		$data['title']   = 'IMPRIMIR SOLVENCIA';
		$this->load->view('view_ventanas', $data);
	}
	
	function _pre_print_solv_insert($do){ return false;}
	function _pre_print_solv_delete($do){ return false;}
	function _post_print_solv_update($do){
			$id =$do->get('id');
			redirect($this->url."dataedit/show/$id");
	}
	
	function dataprint_concsolv($st,$uid){
		$this->rapyd->load('dataedit');

		$edit = new DataEdit('Concepto Solvencia', 'recibo');
		//$id=$edit->get_from_dataobjetct('id');
		//$urlid=$edit->pk_URI();
		$id   =$uid;
		$urlid=$uid;
		
		//$url=site_url('forma1/ver/SOLVENCIA/'.$urlid);
		$edit->back_url = site_url($this->url.'dataedit/show/'.$uid);

		$edit->back_save   = true;
		$edit->back_delete = true;
		$edit->back_cancel = true;
		$edit->back_cancel_save   = true;
		$edit->back_cancel_delete = true;
		//$edit->on_save_redirect   = false;

		$edit->post_process('update','_post_print_concsolv_update');
		//$edit->pre_process('insert' ,'_pre_print_solv_insert');
		//$edit->pre_process('update' ,'_pre_print_update');
		//$edit->pre_process('delete' ,'_pre_print_solv_delete');

		//$edit->container = new containerField('impresion','La descarga se realizara en 5 segundos, en caso de no hacerlo haga click '.anchor('forma1/ver/SOLVENCIA/'.$urlid,'aqui'));

		$edit->concsolv = new inputField('Concepto Solvencia','concsolv');
		$edit->concsolv->size        =60;

		$edit->solvencia = new inputField('Solvencia N&uacute;mero','solvencia');
		$edit->solvencia->rule        ='max_length[12]|required';
		$edit->solvencia->size        =14;
		$edit->solvencia->maxlength   =12;
		$edit->solvencia->autocomplete=false;
		$edit->solvencia->mode='autohide';
		
		$edit->numero = new inputField('Recibo N&uacute;mero','numero');
		$edit->numero->rule        ='max_length[12]|required';
		$edit->numero->size        =14;
		$edit->numero->maxlength   =12;
		$edit->numero->autocomplete=false;
		$edit->numero->mode='autohide';

		$edit->id = new inputField('Ref','numero');
		$edit->id->rule='max_length[8]';
		$edit->id->mode='autohide';
		$edit->id->size =10;
		$edit->id->maxlength =8;

		$edit->fecha = new dateField('Fecha','fecha');
		$edit->fecha->rule = 'chfecha';
		$edit->fecha->mode = 'autohide';
		$edit->fecha->size = 10;
		$edit->fecha->maxlength =8;
		
		$edit->tipo = new dropdownField('Concepto','tipo');
		$edit->tipo->options("SELECT codigo,CONCAT(codigo,'-',descrip) FROM tingresos ORDER BY grupo,descrip");//WHERE activo='S' 
		$edit->tipo->onchange="cal_concepto()";
		$edit->tipo->style="width:350px;";
		$edit->tipo->mode='autohide';
		
		$edit->nombre = new inputField('Nombre','nombre');
		$edit->nombre->rule='required|max_length[200]';
		$edit->nombre->size =50;
		$edit->nombre->maxlength =200;
		$edit->nombre->autocomplete=false;
		$edit->nombre->mode='autohide';

		$edit->rifci = new inputField('Rif/CI','rifci');
		$edit->rifci->rule      ='required|max_length[13]';
		$edit->rifci->size      =15;
		$edit->rifci->maxlength =13;
		$edit->rifci->mode='autohide';

		$edit->monto = new inputField('Monto','monto');
		$edit->monto->rule='max_length[12]|numeric';
		$edit->monto->css_class='inputnum';
		$edit->monto->size =14;
		$edit->monto->showformat='decimal';
		$edit->monto->mode='autohide';
		$edit->monto->maxlength =12;

		$edit->buttons('save', 'undo','back');
		$edit->build();

		$data['content'] = $edit->output;
		$data['head']    = $this->rapyd->get_head();
		$data['script']  = script('jquery.js').script('plugins/jquery.numeric.pack.js').script('plugins/jquery.floatnumber.js');
		
		$data['title']   = 'Concepto de  Solvencia';
		$this->load->view('view_ventanas', $data);
	}
	
	function _post_print_concsolv_update($do){
			$id =$do->get('id');
			redirect($this->url."dataprint_solvencia/modify/$id");
	}
	
	function damedeuda_trimestre(){
		$vehiculo = $this->input->post('vehiculo');
		$vehiculoe=$this->db->escape($vehiculo);
		$query="
		SELECT CONCAT(a.ano,a.nro) anonro,d.monto  
		FROM itrecibo a
		JOIN recibo b ON a.id_recibo=b.id
		JOIN vehiculo c ON b.vehiculo=c.id
		JOIN clase d ON c.clase=d.codigo
		WHERE b.`status` ='C' AND b.tipo=8 AND c.id=$vehiculoe
		";
		$vehiculorow = $this->datasis->damerow($query);
		
		$query  ="SELECT a.ano,a.tipo,a.nro,a.descrip,b.valor*".$vehiculorow['monto']." monto 
		FROM itrecibodebe a
		JOIN utribu b ON a.ano=b.ano
		WHERE 1*CONCAT(a.ano,a.nro) > ".$vehiculorow['anonro'];
		$mSQL   = $this->db->query($query);
		$arreglo= $mSQL->result_array($query);
		foreach($arreglo as $key=>$value)
			foreach($value as $key2=>$value2) 
			$arreglo[$key][$key2] = ($value2);

		echo json_encode($arreglo);
		
	}
	
	function instalar(){
		$mSQL="
		CREATE TABLE `recibo` (
			`numero` VARCHAR(10) NOT NULL,
			`id` INT(11) NOT NULL AUTO_INCREMENT,
			`fecha` DATE NULL DEFAULT NULL,
			`contribu` CHAR(6) NULL DEFAULT NULL,
			`tipo` CHAR(3) NULL DEFAULT NULL,
			`monto` DECIMAL(19,2) NOT NULL DEFAULT '0.00',
			`observa` TEXT NOT NULL,
			`direccion` TEXT NULL,
			`nombre` VARCHAR(200) NULL DEFAULT NULL,
			`rifci` VARCHAR(13) NULL DEFAULT NULL,
			`nacionali` VARCHAR(10) NULL DEFAULT NULL,
			`telefono` VARCHAR(50) NULL DEFAULT NULL,
			`user` VARCHAR(50) NULL DEFAULT NULL,
			`estampa` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
			`patente` INT(11) NULL DEFAULT NULL,
			`inmueble` INT(11) NULL DEFAULT NULL,
			`vehiculo` INT(11) NULL DEFAULT NULL,
			`declaracion` DECIMAL(19,2) NULL DEFAULT '0.00',
			`fexp` DATE NULL DEFAULT NULL,
			`fven` DATE NULL DEFAULT NULL,
			`abono` INT(11) NULL DEFAULT NULL,
			`oper` VARCHAR(50) NOT NULL DEFAULT '',
			`status` CHAR(1) NOT NULL DEFAULT 'P',
			`Column 24` CHAR(1) NOT NULL DEFAULT 'P',
			PRIMARY KEY (`id`)
		)
		COLLATE='utf8_general_ci'
		ENGINE=MyISAM
		AUTO_INCREMENT=1
		";
		$this->db->simple_query($mSQL);
		
		$query="
		CREATE TABLE `itrecibo` (
			`id_recibo` INT(11) NULL DEFAULT NULL,
			`id` INT(11) NOT NULL AUTO_INCREMENT,
			`ano` CHAR(4) NULL DEFAULT NULL,
			`tipo` VARCHAR(20) NULL DEFAULT NULL,
			`nro` INT(11) NULL DEFAULT NULL,
			`descrip` TEXT NULL,
			`monto` DECIMAL(19,2) NULL DEFAULT NULL,
			PRIMARY KEY (`id`)
		)
		COLLATE='utf8_general_ci'
		ENGINE=MyISAM
		AUTO_INCREMENT=1";
		
		$this->db->simple_query($mSQL);
		$quey="ALTER TABLE `recibo` ADD COLUMN `tasam` DECIMAL(19,2) NOT NULL DEFAULT '0' AFTER `status`";
		$this->db->simple_query($mSQL);
		$quey="ALTER TABLE `recibo` ADD COLUMN `rasonsocial` VARCHAR(50) NOT NULL AFTER `tasam`";
		$this->db->simple_query($mSQL);
		$query="ALTER TABLE `patente` CHANGE COLUMN `observa` `observa` TEXT NULL DEFAULT NULL AFTER `total`";
		$this->db->simple_query($mSQL);
		$query="ALTER TABLE `patente` ADD COLUMN `fexpedicion` DATE NULL DEFAULT NULL AFTER `nro`";
		$this->db->simple_query($mSQL);
		$query="ALTER TABLE `patente` ADD COLUMN `fvencimiento` DATE NULL DEFAULT NULL AFTER `fexpedicion`";
		$this->db->simple_query($mSQL);
		$query="ALTER TABLE `recibo` CHANGE COLUMN `razonsocial` `razonsocial` VARCHAR(50) NULL AFTER `tasam`";
		$this->db->simple_query($mSQL);
		$query="ALTER TABLE `recibo` ADD COLUMN `rif` VARCHAR(20) NULL AFTER `razonsocial`";
		$this->db->simple_query($mSQL);
		$query="ALTER TABLE `recibo` ADD COLUMN `nomfis` VARCHAR(100) NULL AFTER `rif`";
		$this->db->simple_query($mSQL);
		$query="ALTER TABLE `recibo` ADD COLUMN `efectos` VARCHAR(200) NULL AFTER `nomfis`";
		$this->db->simple_query($mSQL);
		$query="ALTER TABLE `recibo` ADD COLUMN `efectos2` VARCHAR(200) NULL AFTER `nomfis`";
		$this->db->simple_query($mSQL);	
		$query="ALTER TABLE `recibo` ADD COLUMN `recibo` VARCHAR(20) NULL DEFAULT NULL AFTER `efectos`";
		$this->db->simple_query($mSQL);
		$query="ALTER TABLE `recibo` ADD COLUMN `solvencia` VARCHAR(20) NULL DEFAULT NULL AFTER `recibo`";
		$this->db->simple_query($mSQL);
		$query="ALTER TABLE `recibo` ADD COLUMN `concsolv` TEXT NULL DEFAULT NULL AFTER `efectos2`";
		$this->db->simple_query($mSQL);
		$query="alter table recibo add column `patente` INT(11) NULL DEFAULT NULL ";
		$this->db->simple_query($mSQL);
		$query="alter table recibo add column `inmueble` INT(11) NULL DEFAULT NULL";
		$this->db->simple_query($mSQL);
		$query="alter table recibo add column `vehiculo` INT(11) NULL DEFAULT NULL";
		$this->db->simple_query($mSQL);
		$query="ALTER TABLE `recibo` ADD COLUMN `id` INT NOT NULL AUTO_INCREMENT AFTER `numero`,DROP PRIMARY KEY,ADD PRIMARY KEY (`id`)";
		$this->db->simple_query($mSQL);
		$query="ALTER TABLE `tingresos`  ADD COLUMN `descrip2` VARCHAR(250) NULL DEFAULT NULL";
		$this->db->simple_query($mSQL);		
		$query="ALTER TABLE `tingresos` ADD COLUMN `codigo2` VARCHAR(20) NULL DEFAULT NULL AFTER `descrip2`";
		$this->db->simple_query($mSQL);	
		$query="
			CREATE TABLE `utribu` (
				`ano` VARCHAR(10) NOT NULL,
				`valor` DECIMAL(19,2) NULL DEFAULT NULL,
				PRIMARY KEY (`ano`)
			)
			ENGINE=MyISAM
		";
		$this->db->simple_query($mSQL);	
	}
}
?>
