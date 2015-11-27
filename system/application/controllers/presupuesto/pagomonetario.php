<?php
class pagomonetario extends Controller {

	var $titp  = 'Ordenes de Pago Sin Imputacion Presupuestaria';
	var $tits  = 'Orden de Pago Sin Imputacion Presupuestaria';
	var $url   = 'presupuesto/pagomonetario/';


	function pagomonetario(){
		parent::Controller();
		$this->load->library("rapyd");
		$this->datasis->modulo_id(178,1);
	}

	function index(){
		redirect($this->url."filteredgrid");
	}

	function filteredgrid(){
		
		$this->rapyd->load("datafilter2","datagrid");
		
		$mSPRV=array(
			'tabla'   =>'sprv',
			'columnas'=>array(
				'proveed' =>'C&oacute;odigo',
				'nombre'=>'Nombre',
				'contacto'=>'Contacto'),
			'filtro'  =>array(
				'proveed'=>'C&oacute;digo',
				'nombre'=>'Nombre'),
			'retornar'=>array(
				'proveed'=>'cod_prov'),
			'titulo'  =>'Buscar Beneficiario'
		);
		
		$bSPRV=$this->datasis->p_modbus($mSPRV,"proveed");
		
		
		$filter = new DataFilter2("");
		$filter->db->select("a.reverso reverso,a.total2,a.numero numero,a.fecha fecha,a.tipo tipo,a.status status,a.cod_prov cod_prov,a.beneficiario beneficiario,b.nombre uejecuta2,c.nombre proveed");
		$filter->db->from("odirect a");                  
		$filter->db->join("uejecutora b" ,"a.uejecutora=b.codigo",'LEFT');
		$filter->db->join("sprv c"       ,"c.proveed=a.cod_prov",'LEFT');
		$filter->db->where('MID(status,1,1) ','M');
				
		$filter->numero = new inputField("N&uacute;mero", "numero");
		$filter->numero->size=12;
		//$filter->numero->clause="likerigth";
		
		//$filter->tipo = new dropdownField("Orden de ", "tipo");
		//$filter->tipo->option("","");
		//$filter->tipo->option("Compra"  ,"Compra");
		//$filter->tipo->option("Servicio","Servicio");
		//$filter->tipo->style="width:100px;";
		
		$filter->fecha = new dateonlyField("Fecha", "fecha");
		$filter->fecha->dbformat = "Y-m-d";
		$filter->fecha->size=12;
		
		$filter->uejecutora = new inputField("Unidad Ejecutora", "uejecutora");
		$filter->uejecutora->size=12;
		
		$filter->cod_prov = new inputField("Beneficiario", 'cod_prov');
		$filter->cod_prov->size = 6;
		$filter->cod_prov->append($bSPRV);
		$filter->cod_prov->rule = "required";
		$filter->cod_prov->db_name='a.cod_prov';
		
		$filter->status = new dropdownField("Estado","status");		
		$filter->status->option("","");
		$filter->status->option("M2","Actualizado");
		$filter->status->option("M1","Sin Actualizar");		
		$filter->status->option("M3","Pagado");
		$filter->status->style="width:150px";
		
		$filter->buttons("reset","search");
		
		$filter->build();
		$uri = anchor($this->url.'dataedit/show/<#numero#>','<str_pad><#numero#>|8|0|STR_PAD_LEFT</str_pad>');
		
		function sta($status){
			switch($status){
				case "M1":return "Sin Actualizar";break;
				case "M2":return "Actualizado";break;
				case "M3":return "Pagado";break;
				//case "O":return "Ordenado Pago";break;
				case "MY":return "Reverso";break;
				case "MA":return "Anulado";break;
			}
		}
		
		$grid = new DataGrid("");
		
		
		
		$grid->order_by("numero","desc");
		$grid->per_page = 20;
		$grid->use_function('substr','str_pad','sta');
		
		$grid->column_orderby("N&uacute;mero"    ,$uri                                             ,"numero");
		//$grid->column_orderby("Tipo"             ,"tipo"                                           ,"tipo"          ,"align='center'");
		$grid->column_orderby("Fecha"            ,"<dbdate_to_human><#fecha#></dbdate_to_human>"   ,"fecha"         ,"align='center'");
		$grid->column_orderby("Unidad Ejecutora" ,"uejecuta2"                                      ,"uejecuta2"     ,"align='left'NOWRAP");
		$grid->column_orderby("Beneficiario"     ,"proveed"                                        ,"proveed"       ,"align='left'NOWRAP");
		$grid->column_orderby("Pago"             ,"<number_format><#total2#>|2|,|.</number_format>","total2"        ,"align='right'NOWRAP");
		$grid->column_orderby("Estado"           ,"<sta><#status#></sta>"                          ,"status"        ,"align='center'NOWRAP");
		
		
		$grid->add($this->url."dataedit/create");
		$grid->build();
		
		//echo $grid->db->last_query();
		
		//$data['content'] = $filter->output.$grid->output;
		$data['filtro']  = $filter->output;
		$data['content'] = $grid->output;
		$data['script'] = script("jquery.js")."\n";
		$data['title']   = "$this->titp";
		$data["head"]    = $this->rapyd->get_head();
		$this->load->view('view_ventanas', $data);
	}

	function dataedit($back='',$status='',$numero=''){
		$this->rapyd->load('dataobject','datadetails');
		
		$mSPRV=array(
			'tabla'   =>'sprv',
			'columnas'=>array(
			'proveed' =>'C&oacute;odigo',
			'nombre'=>'Nombre',
			'rif'=>'Rif',
			'contacto'=>'Contacto'),
			'filtro'  =>array('proveed'=>'C&oacute;digo','nombre'=>'Nombre','rif'=>'Rif'),
			'retornar'=>array('proveed'=>'cod_prov','nombre'=>'nombrep','rif'=>'rifp','reteiva'=>'reteiva_prov'),
			//'retornar'=>'ca_total',
			'titulo'  =>'Buscar Beneficiario');
			
		$bSPRV=$this->datasis->p_modbus($mSPRV ,"sprv");
		
		$mOCOMPRA=array(
				'tabla'    =>'ocompra',
				'columnas' =>array(
					'numero'     =>'N&uacute;mero',
					'tipo'       =>'Tipo',
					'uejecutora' =>'uejecutora',
					'cod_prov'   =>'Beneficiario'),
				'filtro'  =>array(
					'numero'     =>'N&uacute;mero',
					'tipo'       =>'Tipo',
					'uejecutora' =>'uejecutora',
					'cod_prov'   =>'Beneficiario'),
				'retornar'=>array(
					'numero'                                 =>'compra'       ,
					'cod_prov'                               =>'cod_prov'     ,
					'total2'                                 =>'montocontrato',
					'CONCAT("Pago de Anticipo ",observa)'    =>'observa'      ,
					'CONCAT("50")'                           =>'porcent'      ,
					'subtotal'                               =>'montob'
					),
			'p_uri'=>array(
				  4=>'<#cod_prov#>'),
			'where' =>'( status = "C" ) AND IF(<#cod_prov#> = ".....", cod_prov LIKE "%" ,cod_prov = <#cod_prov#>)',
			'script'=>array('cal_total()'),
			'titulo'  =>'Buscar Ordenes de Compra');

		$pOCOMPRA=$this->datasis->p_modbus($mOCOMPRA,'<#cod_prov#>');
		
		$rr        = $this->ivaplica2();
		$pimpm = $this->datasis->traevalor('IMPMUNICIPAL');
		$pimpt = $this->datasis->traevalor('IMPTIMBRE');
		$pcrs  = $this->datasis->traevalor('CRS');
		$site_url = site_url('presupuesto/pobra/islr');
		
		$do = new DataObject("odirect");
		$do->pointer('sprv'   ,'sprv.proveed = odirect.cod_prov','sprv.nombre as nombrep, sprv.rif rifp','LEFT');
		$do->rel_one_to_many('itfac', 'itfac', array('numero'=>'numero'));

		$edit = new DataDetails($this->tits, $do);
		if($back=='opagof')
		$edit->back_url = site_url("presupuesto/opagof/filteredgrid");
		else
		$edit->back_url = site_url($this->url."filteredgrid/index");
		
		$edit->set_rel_title('itfac','Factura <#o#>');
		
		$edit->pre_process('update'  ,'_valida');
		$edit->pre_process('insert'  ,'_valida');
		//$edit->post_process('insert'  ,'_post');
		//$edit->post_process('update'  ,'_post');
		$edit->post_process('insert','_post_insert');
		$edit->post_process('update','_post_update');
		$edit->post_process('delete','_post_delete');
	
		$edit->numero  = new inputField("N&uacute;mero", "numero");
		$edit->numero->rule = 'unique';
		if($this->datasis->traevalor('USANODIRECT')=='S'){
			$edit->numero->when=array('show');
		}else{
			$edit->numero->when=array('show','create','modify');
		}
	
		$edit->fecha = new  dateonlyField("Fecha",  "fecha");
		$edit->fecha->insertValue = date('Y-m-d');
		$edit->fecha->size =12;
		
		$edit->status = new dropdownField("Estado","status");
		$edit->status->option("M" ,"Por Elaborar");
		$edit->status->option("M2","Emitida");
		$edit->status->option("M1","Por Emitir");
		$edit->status->option("M3","Pagado");
		$edit->status->option("MA","Anulada");
		$edit->status->style="width:150px";
		$edit->status->mode ='autohide';
		
		//$edit->tipo = new dropdownField("Orden de ", "tipo");
		//$edit->tipo->option("Compra"  ,"Compra");
		//$edit->tipo->option("Servicio","Servicio");
		//$edit->tipo->option("T","Transferencia");
		//$edit->tipo->style="width:100px;";
		
		//$edit->uejecutora = new dropdownField("Unidad Ejecutora", "uejecutora");
		//$edit->uejecutora->option("","Seccionar");
		//$edit->uejecutora->options("SELECT codigo, nombre FROM uejecutora ORDER BY nombre");
		////$edit->uejecutora->onchange = "get_uadmin();";
		//$edit->uejecutora->rule = "required";
		//$edit->uejecutora->style = "width:400px";

		$lsnc='<a href="javascript:consulsprv();" title="Proveedor" onclick="">Consulta/Agrega BENEFICIARIO</a>';
		$edit->cod_prov = new inputField("Beneficiario", 'cod_prov');
		$edit->cod_prov->db_name  = "cod_prov";
		$edit->cod_prov->size     = 4;
		$edit->cod_prov->append($bSPRV);
		$edit->cod_prov->append($lsnc);
		$edit->cod_prov->rule  = "required";

		$edit->nombrep = new inputField("Nombre", 'nombrep');
		$edit->nombrep->size = 30;
		//$edit->nombrep->readonly = true;
		$edit->nombrep->pointer = true;

		$edit->rifp  = new inputField("RIF", "rifp");
		$edit->rifp->size=10;
		$edit->rifp->pointer = true;
		$edit->rifp->db_name='rifp';
		
		$edit->nombrep = new inputField("Nombre", 'nombrep');
		$edit->nombrep->size     = 50;
		$edit->nombrep->readonly = true;
		$edit->nombrep->pointer  = TRUE;
		$edit->nombrep->in       = "cod_prov";
		
		$edit->observa = new textAreaField("Observaciones", 'observa');
		$edit->observa->cols = 60;
		$edit->observa->rows = 3;
    
		$edit->total= new inputField("Monto a Pagar", 'total');
		$edit->total->size = 8;
		$edit->total->css_class='inputnum';
		$edit->total->value = 0;
		$edit->total->style = 'height:40px;width: 200px;font-size:16px;font-weight:bold';
		
		$ganticipo="Datos para Anticipos de Contratos";
		$edit->compra = new inputField("Compromiso", 'compra');
		$edit->compra->size     = 10;
		//$edit->compra->rule     = "required";
		$edit->compra->readonly =true;
		$edit->compra->append('<img src="'.base_url().'assets/default/images/system-search.png"  alt="Busqueda de Ordenes de Compromisos" title="Busqueda de Ordenes de Compromisos" border="0" onclick="modbusdepen()"/>');
		$edit->compra->group = $ganticipo;
		
		$edit->porcent= new inputField("Porcentaje", 'porcent');
		$edit->porcent->size = 10;
		$edit->porcent->css_class='inputnum';
		$edit->porcent->value = 0;
		$edit->porcent->group = $ganticipo;
		
		$edit->montocontrato= new inputField("Monto Contrato", 'montocontrato');
		$edit->montocontrato->size = 10;
		$edit->montocontrato->css_class='inputnum';
		$edit->montocontrato->value = 0;
		$edit->montocontrato->readonly =true;
		$edit->montocontrato->group = $ganticipo;
		
		$edit->montob= new hiddenField("", 'montob');
		$edit->montob->size = 10;
		$edit->montob->css_class='inputnum';
		$edit->montob->value = 0;
		$edit->montob->readonly =true;
		$edit->montob->group = $ganticipo;
		
		$edit->reteiva_prov  = new inputField("% R.IVA", "reteiva_prov");
		$edit->reteiva_prov->size=2;
		$edit->reteiva_prov->readonly=true;
		$edit->reteiva_prov->when=array('modify','create');
		$edit->reteiva_prov->onchange ='cal_total();';
		
		$edit->simptimbre = new checkboxField("1X1000", "simptimbre", "S","N");
		$edit->simptimbre->insertValue = "N";
		$edit->simptimbre->onchange ='cal_timbre();';

		$edit->simpmunicipal = new checkboxField("I.Municipal", "simpmunicipal", "S","N");
		$edit->simpmunicipal->insertValue = "N";
		$edit->simpmunicipal->onchange ='cal_municipal();';

		$edit->imptimbre= new inputField("Impuesto 1X1000", 'imptimbre');
		$edit->imptimbre->size = 8;
		$edit->imptimbre->css_class='inputnum';
		$edit->imptimbre->onchange ='cal_total();';
		
		$edit->impmunicipal= new inputField("Impuesto Municipal", 'impmunicipal');
		$edit->impmunicipal->size = 8;
		$edit->impmunicipal->css_class='inputnum';
		$edit->impmunicipal->onchange ='cal_total();';
		
		$edit->ivaa = new inputField("IVA Adicional", 'ivaa');
		$edit->ivaa->css_class='inputnum';
		$edit->ivaa->size = 8;
		$edit->ivaa->readonly=true;

		$edit->ivag = new inputField("IVA General", 'ivag');
		$edit->ivag->css_class='inputnum';
		$edit->ivag->size = 8;
		$edit->ivag->readonly=true;

		$edit->ivar = new inputField("IVA Reducido", 'ivar');
		$edit->ivar->css_class='inputnum';
		$edit->ivar->size = 8;
		$edit->ivar->readonly=true;

		$edit->exento = new inputField("Exento", 'exento');
		$edit->exento->css_class='inputnum';
		$edit->exento->size = 8;
		//$edit->exento->onchange ='cal_total();';
		$edit->exento->readonly=true;

		$edit->reteiva = new inputField("Retencion IVA", 'reteiva');
		$edit->reteiva->css_class='inputnum';
		$edit->reteiva->size = 8;
		$edit->reteiva->readonly=true;

		$edit->creten = new dropdownField("Codigo ISLR","creten");
		$edit->creten->options("SELECT codigo,CONCAT_WS(' ',codigo,activida) FROM rete ORDER BY codigo");
		$edit->creten->style="width:400px;";
		$edit->creten->onchange ='cal_reten();';

		$edit->reten = new inputField("Retenci&oacute;n ISLR", 'reten');
		$edit->reten->css_class='inputnum';
		$edit->reten->size = 8;
		$edit->reten->readonly=true;
		
		$edit->preten = new inputField("%", 'preten');
		$edit->preten->css_class='inputnum';
		$edit->preten->size = 8;
		$edit->preten->type='inputhidden';

		$edit->otrasrete = new inputField("Otras Deducciones", 'otrasrete');
		$edit->otrasrete->css_class='inputnum';
		$edit->otrasrete->size = 8;
		$edit->otrasrete->insertValue=0;
		$edit->otrasrete->onchange ='cal_total();';
		
		$edit->total2 = new inputField("Total", 'total2');
		$edit->total2->css_class='inputnum';
		$edit->total2->size = 8;
		$edit->total2->readonly=true;
		
		$edit->amortiza = new inputField("Amortizacion", 'amortiza');
		$edit->amortiza->css_class='inputnum';
		$edit->amortiza->size = 8;
		//$edit->amortiza->readonly=true;
		$edit->amortiza->insertValue=0;
		$edit->amortiza->onchange ='cal_total();';
		$edit->amortiza->rule='numeric';
		
		/*
		 * INICIO DETALLE DE FCTURAS
		 */
		 
		$edit->tivag = new inputField("","tivag");
		$edit->tivag->size       = 1 ;
		//$edit->tivag->mode       = "autohide";
		//$edit->tivag->insertValue=$ivaplica['tasa'];
		$edit->tivag->readonly   =true;
		//$edit->tivag->when=array('modify');
		//$edit->tivag->status='show';

		$edit->tivar = new inputField("","tivar");
		$edit->tivar->size       = 1 ;
		//$edit->tivar->mode       = "autohide";
		//$edit->tivar->insertValue=$ivaplica['redutasa'];
		$edit->tivar->readonly   =true;
		//$edit->tivar->when=array('modify');

		$edit->tivaa = new inputField("","tivaa");
		$edit->tivaa->size       = 1 ;
		//$edit->tivaa->mode       = "autohide";
		//$edit->tivaa->insertValue=$ivaplica['sobretasa'];
		$edit->tivaa->readonly   =true;
		//$edit->tivaa->when=array('modify');
		
		$edit->total22 = new inputField("Total", 'total22');
		$edit->total22->db_name  =' ';
		$edit->total22->css_class='inputnum';
		$edit->total22->size     = 15;
		//$edit->total22->mode     = 'autohide';
		$edit->total22->when     =array("create","modify");
		
		///////VISUALES  INICIO ////////////////

		$edit->tsubtotal = new inputField("","tsubtotal");
		$edit->tsubtotal->readonly = true;
		$edit->tsubtotal->size     = 8 ;
		$edit->tsubtotal->when     =array('modify');
		$edit->tsubtotal->css_class  = "inputnum";

		$edit->texento = new inputField("","texento");
		$edit->texento->readonly = true;
		$edit->texento->size = 6 ;
		$edit->texento->when =array('modify');
		$edit->texento->css_class  = "inputnum";

		$edit->trivag = new inputField("","trivag");
		$edit->trivag->readonly = true;
		$edit->trivag->size     = 10 ;
		$edit->trivag->when     =array('modify');
		//$edit->trivag->onchange ='cal_totales();';
		$edit->trivag->css_class  = "inputnum";

		$edit->trivar = new inputField("","trivar");
		$edit->trivar->readonly = true;
		$edit->trivar->size     = 8 ;
		$edit->trivar->when     =array('modify');
		//$edit->trivar->onchange ='cal_totales();';
		$edit->trivar->css_class  = "inputnum";

		$edit->trivaa = new inputField("","trivaa");
		$edit->trivaa->readonly = true;
		$edit->trivaa->size     = 8 ;
		$edit->trivaa->when     =array('modify');
		//$edit->trivaa->onchange ='cal_totales();';
		$edit->trivaa->css_class  = "inputnum";

		$edit->treteiva = new inputField("","treteiva");
		$edit->treteiva->readonly = true;
		$edit->treteiva->size     = 8 ;
		$edit->treteiva->when     =array('modify');
		//$edit->treteiva->onchange ='cal_totales();';
		$edit->treteiva->css_class  = "inputnum";

		$edit->breten = new inputField("","breten");
		$edit->breten->readonly = true;
		$edit->breten->size     = 4 ;
		$edit->breten->when     =array('modify','show');
		$edit->breten->css_class  = "inputnum";

		$edit->timptimbre = new inputField("","timptimbre");
		$edit->timptimbre->readonly = true;
		$edit->timptimbre->size     = 8 ;
		$edit->timptimbre->when     =array('modify');
		//$edit->timptimbre->onchange ='cal_totales();';
		$edit->timptimbre->css_class  = "inputnum";
		
		$edit->timpmunicipal = new inputField("","timpmunicipal");
		$edit->timpmunicipal->readonly = true;
		$edit->timpmunicipal->size     = 8 ;
		$edit->timpmunicipal->when     =array('modify');
		$edit->timpmunicipal->css_class  = "inputnum";

		$edit->ttotal2 = new inputField("","ttotal2");
		$edit->ttotal2->readonly = true;
		$edit->ttotal2->size     = 8;
		$edit->ttotal2->when     =array('modify');
		$edit->ttotal2->css_class  = "inputnum";
		///////FIN VISUALES ////////////
		 
		$edit->itfactura = new inputField("(<#o#>) Factura", "factura_<#i#>");
		$edit->itfactura->size   =7;
		$edit->itfactura->db_name='factura';
		$edit->itfactura->rel_id ='itfac';
		$edit->itfactura->rule   ='required';

		$edit->itcontrolfac = new inputField("(<#o#>) Control Fiscal", "controlfac_<#i#>");
		$edit->itcontrolfac->db_name  ='controlfac';
		//$edit->itcontrolfac->maxlength=3;
		$edit->itcontrolfac->size     =7;
		$edit->itcontrolfac->rel_id   ='itfac';
		$edit->itcontrolfac->rule     ='required';

		$edit->itfechafac = new dateonlyField("(<#o#>) Fecha Factura", "fechafac_<#i#>");
		$edit->itfechafac->db_name  ='fechafac';
		$edit->itfechafac->insertValue = date('Y-m-d');
		//$edit->itfechafac->maxlength=80;
		$edit->itfechafac->size     =8;
		$edit->itfechafac->rule     = 'required';
		$edit->itfechafac->rel_id   ='itfac';

		$edit->itsubtotal = new inputField("(<#o#>) Total", "subtotal_<#i#>");
		$edit->itsubtotal->size       =8;
		$edit->itsubtotal->db_name    = 'subtotal';
		$edit->itsubtotal->rel_id     = 'itfac';
		$edit->itsubtotal->onchange   ="cal_subtotal_subtotal(<#i#>);";
		$edit->itsubtotal->css_class  = "inputnum";
		$edit->itsubtotal->value      =0;
		//if($status=="E")$edit->itsubtotal->mode = "autohide";

		$edit->itexento = new inputField("(<#o#>) Exento", "exento_<#i#>");
		$edit->itexento->size       =6;
		$edit->itexento->db_name    ='exento';
		$edit->itexento->rel_id     ='itfac';
		$edit->itexento->css_class  = "inputnum";
		$edit->itexento->value      =0;
		$edit->itexento->onchange ='cal_subtotal_exento(<#i#>);';
		//$edit->itexento->rule ='required';
		//if($status=="E")$edit->itexento->mode = "autohide";

		$edit->ituivag = new dropdownField("(<#o#>)", "uivag_<#i#>");
		$edit->ituivag->rel_id ='itfac';
		$edit->ituivag->db_name='uivag';
		$edit->ituivag->onchange ="cal_subtotal_ivag(<#i#>,'CALC');";
		$edit->ituivag->option("S","Si");
		$edit->ituivag->option("N","No");
		$edit->ituivag->style="width:35px;";

		$edit->itivag = new inputField("(<#o#>) % IVA General", "ivag_<#i#>");
		$edit->itivag->size      =6;
		$edit->itivag->db_name   = 'ivag';
		$edit->itivag->rel_id    = 'itfac';
		//$edit->itivag->insertValue = 0;
		$edit->itivag->onchange  ="cal_subtotal_ivag(<#i#>,'NOCALC');";
		$edit->itivag->css_class = "inputnum";
		$edit->itivag->value     =0;
		//if($status=="E")$edit->itivag->mode = "autohide";

		$edit->ituivar = new dropdownField("", "uivar_<#i#>");
		$edit->ituivar->rel_id ='itfac';
		$edit->ituivar->db_name='uivar';
		$edit->ituivar->onchange ="cal_subtotal_ivar(<#i#>,'CALC');";
		$edit->ituivar->option("N","No");
		$edit->ituivar->option("S","Si");
		$edit->ituivar->style="width:35px;";

		$edit->itivar = new inputField("(<#o#>) % IVA Reducido", "ivar_<#i#>");
		$edit->itivar->size       =5;
		$edit->itivar->db_name    = 'ivar';
		$edit->itivar->rel_id     = 'itfac';
		//$edit->itivar->insertValue = 0;
		$edit->itivar->onchange   ="cal_subtotal_ivar(<#i#>,'NOCALC');";
		$edit->itivar->css_class  = "inputnum";
		$edit->itivar->value      =0;
		//if($status=="E")$edit->itivar->mode = "autohide";

		$edit->ituivaa = new dropdownField("", "uivaa_<#i#>");
		$edit->ituivaa->rel_id ='itfac';
		$edit->ituivaa->db_name='uivaa';
		$edit->ituivaa->onchange ="cal_subtotal_ivaa(<#i#>,'CALC');";
		$edit->ituivaa->option("N","No");
		$edit->ituivaa->option("S","Si");
		$edit->ituivaa->style="width:35px;";

		$edit->itivaa = new inputField("(<#o#>) % IVA Adicional", "ivaa_<#i#>");
		$edit->itivaa->size       =5;
		$edit->itivaa->db_name    = 'ivaa';
		$edit->itivaa->rel_id     = 'itfac';
		$edit->itivaa->onchange   ="cal_subtotal_ivaa(<#i#>,'NOCALC');";
		$edit->itivaa->css_class  = "inputnum";
		$edit->itivaa->value      =0;
		//if($status=="E")$edit->itivaa->mode = "autohide";

		$edit->itreteiva = new inputField("(<#o#>) % IVA Adicional", "reteiva_<#i#>");
		$edit->itreteiva->size       =5;
		$edit->itreteiva->db_name    = 'reteiva';
		$edit->itreteiva->rel_id     = 'itfac';
		//$edit->itreteiva->readonly = true;
		$edit->itreteiva->onchange   ="cal_totalfac();";
		$edit->itreteiva->value      =0;
		$edit->itreteiva->css_class  = "inputnum";
		//if($status=="E")$edit->itreteiva->mode = "autohide";

		$edit->itbreten = new inputField("(<#o#>)Base ISLR", "breten_<#i#>");
		$edit->itbreten->size       =4;
		$edit->itbreten->db_name    = 'breten';
		$edit->itbreten->rel_id     = 'itfac';
		//$edit->itbreten->readonly   = true;
		$edit->itbreten->value      =0;
		$edit->itbreten->css_class  = "inputnum";
		$edit->itbreten->onchange   = "cal_subtotal(<#i#>);";

		$edit->ituimptimbre = new dropdownField("", "uimptimbre_<#i#>");
		$edit->ituimptimbre->rel_id ='itfac';
		$edit->ituimptimbre->db_name='uimptimbre';
		$edit->ituimptimbre->onchange ='cal_subtotal(<#i#>);';
		$edit->ituimptimbre->option("N","No");
		$edit->ituimptimbre->option("S","Si");
		$edit->ituimptimbre->style="width:35px;";

		$edit->itimptimbre = new inputField("(<#o#>) ISLR", "imptimbre_<#i#>");
		$edit->itimptimbre->size       =4;
		$edit->itimptimbre->db_name    = 'imptimbre';
		$edit->itimptimbre->rel_id     = 'itfac';
		$edit->itimptimbre->readonly   = true;
		$edit->itimptimbre->value      =0;
		$edit->itimptimbre->css_class  = "inputnum";
		//if($status=="E")$edit->itreten->mode = "autohide";
		
		$edit->ituimpmunicipal = new dropdownField("", "uimpmunicipal_<#i#>");
		$edit->ituimpmunicipal->rel_id ='itfac';
		$edit->ituimpmunicipal->db_name='uimpmunicipal';
		$edit->ituimpmunicipal->onchange ='cal_subtotal(<#i#>);';
		$edit->ituimpmunicipal->option("N","No");
		$edit->ituimpmunicipal->option("S","Si");
		$edit->ituimpmunicipal->style="width:35px;";

		$edit->itimpmunicipal = new inputField("(<#o#>) ISLR", "impmunicipal_<#i#>");
		$edit->itimpmunicipal->size       =4;
		$edit->itimpmunicipal->db_name    = 'impmunicipal';
		$edit->itimpmunicipal->rel_id     = 'itfac';
		$edit->itimpmunicipal->readonly   = true;
		$edit->itimpmunicipal->value      =0;
		$edit->itimpmunicipal->css_class  = "inputnum";
		//if($status=="E")$edit->itreten->mode = "autohide";
		
		$edit->ittotal2 = new inputField("(<#o#>) Total", "total2_<#i#>");
		$edit->ittotal2->size       =8;
		$edit->ittotal2->db_name    = 'total2';
		$edit->ittotal2->rel_id     = 'itfac';
		$edit->ittotal2->readonly   = true;
		$edit->ittotal2->value      =0;
		$edit->ittotal2->css_class  = "inputnum";
		//if($status=="E")$edit->ittotal2->mode = "autohide";
		 
		 /*
		 * FIN DETALLE DE FCTURAS
		 */
	
		$status=$edit->get_from_dataobjetct('status');
		if($status=='M1'){
			$action = "javascript:window.location='" .site_url($this->url.'actualizar/'.$edit->rapyd->uri->get_edited_id()). "'";
			$edit->button_status("btn_status",'Actualizar',$action,"TR","show");
			$edit->buttons("modify","delete","save");
		}elseif($status=='M2'){
			$action = "javascript:btn_anulaf('".$edit->rapyd->uri->get_edited_id(). "')";
			$edit->button_status("btn_anular",'Anular',$action,"TR","show");
		}elseif($status=='M'){
			$edit->buttons("modify","save");
		}elseif($status=='MA'){
			$edit->buttons("delete");
		
		}else{
			$edit->buttons("save");
		}
		
		$edit->button_status("btn_add_itfac"      ,'Agregar Factura',"javascript:add_itfac()"    ,"FA",'modify',"button_add_rel");
		$edit->button_status("btn_add_itfac2"     ,'Agregar Factura',"javascript:add_itfac()"    ,"FA",'create',"button_add_rel");
	
		$edit->buttons("undo","back","add");
		$edit->build();
    
		
		$query = $this->db->query('SELECT * FROM rete ORDER BY codigo');

		$rt=array();
		foreach ($query->result_array() as $row){
			$pivot=array('tari1'=>$row['tari1'],
			             'pama1'=>$row['pama1'],
			             'tari2'=>$row['tari2'],
			             'pama2'=>$row['pama2'],
			             'tari3'=>$row['tari3'],
			             'pama3'=>$row['pama3'],
			             'porcentsustra'=>$row['porcentsustra']
			             );
			$rt['_'.$row['codigo']]=$pivot;
		}
		$rete=json_encode($rt);

		$ivaplica=$this->ivaplica2();
		$conten['rete']=$rete;
		$conten['ivar']=$ivaplica['redutasa'];
		$conten['ivag']=$ivaplica['tasa'];
		$conten['ivaa']=$ivaplica['sobretasa'];
		$conten['imptimbre']   =$this->datasis->traevalor('IMPTIMBRE');
		$conten['impmunicipal']=$this->datasis->traevalor('IMPMUNICIPAL');
		$conten['utribuactual']=$this->datasis->dameval('SELECT valor FROM utribu WHERE ano=(SELECT MAX(ano) FROM utribu)');
		
		
    
		$smenu['link']   =barra_menu('104');
		$data['smenu']   = $this->load->view('view_sub_menu', $smenu,true);
		$conten["form"]  =&  $edit;
		$data['content'] = $this->load->view('presupuesto/pagomonetario', $conten,true);
		//$data['content'] = $edit->output;
		$data['title']   = "$this->tits";
		$data["head"]    = script('jquery.js').script('jquery-ui.js').script('plugins/jquery.numeric.pack.js').script('plugins/jquery.floatnumber.js').script('plugins/jquery.meiomask.js').style('vino/jquery-ui.css').$this->rapyd->get_head();
		$this->load->view('view_ventanas', $data);
	}

	function ivaplica2($mfecha=NULL){
		if(empty($mfecha)) $mfecha=date('Ymd');
		$CI =& get_instance();
		$qq = $CI->db->query("SELECT tasa, redutasa, sobretasa FROM civa WHERE fecha < '$mfecha' ORDER BY fecha DESC LIMIT 1");
		$rr = $qq->row_array();
		//$aa = each($rr);
		return $rr;
	}

	function actualizar($id){	
		$error      = "";
		$this->rapyd->load('dataobject');
		
		$do  = new DataObject("odirect");
		$do->load($id);
		$status     = $do->get('status'   );
		
    				
		if(empty($error)){
			if($status == "M1" ){
				$do->set('status','M2');
				$do->save();
			}else{
				$error.="<div class='alert'><p>Este Pago No puede ser Actualizado</p></div>";
			}
		}
    		
		if(empty($error)){
		  logusu('pagomonetario','Actualizo pago monetario numero $id');
			redirect($this->url."dataedit/show/$id");
		}else{
			$data['content'] = $error.anchor($this->url."dataedit/show/$id",'Regresar');
			$data['title']   = " $this->tits ";
			$data["head"]    = $this->rapyd->get_head().script('jquery.js').script("plugins/jquery.numeric.pack.js");
			$this->load->view('view_ventanas', $data);
		}
	}
  
  function anular($id){ 
    $error      = "";
    $this->rapyd->load('dataobject');
    
    $do  = new DataObject("odirect");
    $do->load($id);
    $status     = $do->get('status'   );
            
    if(empty($error)){
      if($status == "M2" ){
        $do->set('status','MA');
        $do->save();
      }else{
        $error.="<div class='alert'><p>Este Pago No puede ser Anulado</p></div>";
      }
    }
        
    if(empty($error)){
      logusu('pagomonetario','anulo pago monetario numero $id');
      redirect($this->url."dataedit/show/$id");
    }else{
      $data['content'] = $error.anchor($this->url."dataedit/show/$id",'Regresar');
      $data['title']   = " $this->tits ";
      $data["head"]    = $this->rapyd->get_head().script('jquery.js').script("plugins/jquery.numeric.pack.js");
      $this->load->view('view_ventanas', $data);
    }
  }


	function _valida($do){
		
		$total = $do->get('total');
		$numero= $do->get('numero');
		//$do->set('total2' ,$total );
		$do->set('status' ,'M1' );
		$do->set('multiple','S');
		
		$rr           = $this->ivaplica2();
		$do->set('tivag'         , $rr['tasa']          );
		$do->set('tivar'         , $rr['redutasa']      );
		$do->set('tivaa'         , $rr['sobretasa']     );
		
		
		if(empty($error)){
			if(empty($error) && empty($do->loaded)){
				if(empty($numero)){
					if($this->datasis->traevalor('USANODIRECT')=='S'){
						$nodirect = $this->datasis->fprox_numero('nodirect');
						$do->set('numero',$nodirect);
						$do->pk=array('numero'=>$nodirect);
					}else
						$error.="Debe introducir un numero de orden de pago</br>";
				}elseif($this->datasis->traevalor('USANODIRECT')!='S'){
					$numeroe = $this->db->escape($numero);
					$chk     = $this->datasis->dameval("SELECT COUNT(*) FROM odirect WHERE numero=$numeroe");
					if($chk>0)
						$error.="Error el numero de orden de pago ya existe</br>";
				}
			}
		}
		
		if(!empty($error)){
			$do->error_message_ar['pre_ins']=$error;
			$do->error_message_ar['pre_upd']=$error;
			return false;
		}
	}
	
	function _post_insert($do){
		$numero = $do->get('numero');
		$this->db->simple_query("DELETE FROM itfac WHERE numero='$numero'  WHERE total2=0");
		logusu('pagomonetario',"ingreso pago monetario numero $numero");
	}
	
	  function _post_update($do){
		$numero = $do->get('numero');
		$this->db->simple_query("DELETE FROM itfac WHERE numero='$numero' WHERE total2=0");
		logusu('pagomonetario',"modifico pago monetario numero $numero");
	  }
  
	function instalar(){
		$query="	ALTER TABLE `odirect` CHANGE COLUMN `compra` `compra` VARCHAR(10) NULL DEFAULT NULL";
		$this->db->simple_query($query);
		$query="ALTER TABLE `odirect` ADD COLUMN `montocontrato` DECIMAL(19,2) UNSIGNED NULL DEFAULT '0.00' AFTER `retenomina`";
		$this->db->simple_query($query);
	}
}
?>

