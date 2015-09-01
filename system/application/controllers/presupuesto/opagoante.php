<?php
require_once(BASEPATH.'application/controllers/presupuesto/common.php');
class Opagoante extends Common {

	var $titp  = 'Ordenes de Pago Ejercicio Anterior';
	var $tits  = 'Orden de Pago Ejercicio Anterior';
	var $url   = 'presupuesto/opagoante/';

	function Opagoante(){
		parent::Controller();
		$this->load->library("rapyd");
		$this->formatopres =$this->datasis->traevalor('FORMATOPRES');
		$this->flongpres   =strlen(trim($this->formatopres));
		$this->datasis->modulo_id(116,1);
}

	function index(){
		$this->db->simple_query("ALTER TABLE `itodirect` CHANGE COLUMN `partida` `partida` VARCHAR(25) NULL DEFAULT NULL COMMENT 'Partida Presupuestaria'  ;");
		redirect($this->url."filteredgrid");
	}

	function filteredgrid(){
		$this->rapyd->load("datafilter2","datagrid");
		//$this->rapyd->uri->keep_persistence();

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
			'titulo'  =>'Buscar Beneficiario');

		$bSPRV=$this->datasis->p_modbus($mSPRV,"proveed");

		$filter = new DataFilter2("");
		$filter->db->select(array("a.reverso","b.codigoadm","b.fondo","b.partida","b.ordinal","a.numero","a.fecha","a.tipo","a.compra","a.uejecutora","a.estadmin","a.fondo","a.cod_prov","a.nombre","a.beneficiario","a.pago","a.total2","a.status","MID(a.observa,1,50) observa","c.nombre nombre2"));
		$filter->db->from("odirect a");
		$filter->db->join("itodirect b" ,"a.numero=b.numero");
		$filter->db->join("sprv c"      ,"c.proveed =a.cod_prov");
		$filter->db->where('MID(status,1,1) ','N');
		$filter->db->groupby("a.numero");

		$filter->numero = new inputField("N&uacute;mero", "numero");
		$filter->numero->size=12;
		//$filter->numero->clause="likerigth";
		$filter->numero->db_name = 'a.numero';

		$filter->tipo = new dropdownField("Orden de ", "a.tipo");
		$filter->tipo->option("","");
		$filter->tipo->option("Compra"  ,"Compra");
		$filter->tipo->option("Servicio","Servicio");
		$filter->tipo->style="width:100px;";
		$filter->tipo->db_name = 'a.tipo';

		$filter->fecha = new dateonlyField("Fecha", "fecha");
		$filter->fecha->size=12;
		$filter->fecha->db_name = 'a.fecha';
		$filter->fecha->dbformat='Y-m-d';

		$filter->uejecutora = new inputField("Unidad Ejecutora", "uejecutora");
		$filter->uejecutora->size=12;

		$filter->cod_prov = new inputField("Beneficiario", 'cod_prov');
		$filter->cod_prov->size = 6;
		$filter->cod_prov->append($bSPRV);
		$filter->cod_prov->rule = "required";
		$filter->cod_prov->db_name = 'a.cod_prov';

		$filter->estadmin = new dropdownField("Est. Administrativa", "estadmin");
		$filter->estadmin->option("","Seccionar");
		$filter->estadmin->options("SELECT b.codigo, CONCAT_WS(' ',b.codigo,b.denominacion) AS val FROM presupuesto AS a JOIN estruadm AS b ON a.codigoadm=b.codigo  GROUP BY b.codigo");
		$filter->estadmin->onchange = "get_uadmin();";
		$filter->estadmin->db_name = 'b.codigoadm';

		$filter->fondo = new dropdownField("Fondo", "fondo");
		$filter->fondo->option("","");
		$filter->fondo->options("SELECT fondo,CONCAT_WS(' ',fondo,descrip)a FROM fondo");
		$filter->fondo->db_name = 'b.fondo';

		$filter->partida = new inputField("Partida", "partida");
		//$filter->partida-> db_name ="codigopres";
		$filter->partida->clause   ="likerigth";
		$filter->partida->size     = 25;
		$filter->partida->db_name = 'b.partida';

		$filter->ordinal = new inputField("Ordinal", "ordinal");
		$filter->ordinal->size     = 5;
		$filter->ordinal->clause ="likerigth";
		$filter->ordinal->db_name = 'b.ordinal';

		$filter->observa = new inputField("Observacion", "observa");
		$filter->observa->size=20;

		$filter->reverso = new inputField("Reverso de", "reverso");
		$filter->reverso->size=20;

		$filter->total2 = new inputField("Monto", "total2");
		$filter->total2->size=20;

		$filter->status = new dropdownField("Estado","status");
		$filter->status->option("","");
		$filter->status->option("N","Pendiente");
		$filter->status->option("NY","Reverso");
		$filter->status->option("N2","Ejecutado");
		$filter->status->option("N1","Sin Ejecucion");
		$filter->status->option("N3","Pagado");
		$filter->status->option("NA","Anulado");
		$filter->status->style="width:150px";
		$filter->status->db_name = 'a.status';

		$filter->buttons("reset","search");

		$filter->build();
		$uri = anchor($this->url.'dataedit/show/<#numero#>','<str_pad><#numero#>|8|0|STR_PAD_LEFT</str_pad>');
		$uri_2 = anchor('presupuesto/odirect/dataedit/create/<#numero#>','Duplicar');

		function sta($status){
			switch($status){
				case "N1":return "Sin Ejecucion";break;
				case "N2":return "Ejecutado";break;
				case "N3":return "Pagado";break;
				case "N":return "Pendiente";break;
				case "NY":return "Reverso";break;
				case "NA":return "Anulado";break;
			}
		}

		function tipo($tipo){
			switch($tipo){
				case "T":return "Transferencia";break;
				case "N":return "Nomina";break;
			}
		}

		$grid = new DataGrid($this->titp);
		$grid->order_by("numero","desc");
		$grid->per_page = 20;
		$grid->use_function('substr','str_pad','sta','tipo');

		$grid->column_orderby("N&uacute;mero"    ,$uri                                             ,"numero"                            );
		$grid->column_orderby("Tipo"             ,"<tipo><#tipo#></tipo>"                          ,"tipo"           ,"align='center'"  );
		$grid->column_orderby("Fecha"            ,"<dbdate_to_human><#fecha#></dbdate_to_human>"   ,"fecha"          ,"align='center'"  );
		$grid->column_orderby("Est. Adm"         ,"codigoadm"                                      ,"estamdin"       ,"NOWRAP"          );
		$grid->column_orderby("Fondo"            ,"fondo"                                          ,"fondo"          ,"NOWRAP"          );
		$grid->column_orderby("Partida"          ,"partida"                                        ,"partida"        ,"NOWRAP"          );
		$grid->column_orderby("Ordinal"          ,"ordinal"                                        ,"ordinal"        ,"NOWRAP"          );
		$grid->column_orderby("Beneficiario"     ,"nombre2"                                        ,"c.nombre"                          );//,"NOWRAP"
		//$grid->column_orderby("Observacion"      ,"observa"                                        ,"observa"               );//,"NOWRAP"
		$grid->column_orderby("Pago"             ,"<number_format><#total2#>|2|,|.</number_format>","total2"         ,"align='right'"   );
		$grid->column_orderby("Estado"           ,"<sta><#status#></sta>"                          ,"status"         ,"align='center' " );//NOWRAP
		$grid->column_orderby("Reverso de"       ,"reverso"                                        ,"reverso"        ,"align='center' " );//NOWRAP
		$grid->column("Duplicar"                 ,$uri_2                                           ,"align='center'");

		$grid->add($this->url."dataedit/create");
		$grid->build();
//		echo $grid->db->last_query();

		$data['filtro']  = $filter->output;
		$data['content'] = $grid->output;
		$data['script'] = script("jquery.js")."\n";
		$data['title']   = "";//" $this->titp ";
		//$data['content'] = $filter->output.$grid->output;
		$data['title']   = "$this->titp";
		$data["head"]    = $this->rapyd->get_head();
		$this->load->view('view_ventanas', $data);
	}

	function dataedit($status='',$numero=''){
		//$this->datasis->modulo_id(116,1);
		$this->rapyd->load('dataobject','datadetails');

		$mSPRV=array(
			'tabla'   =>'sprv',
			'columnas'=>array(
			'proveed' =>'C&oacute;odigo',
			'nombre'=>'Nombre',
			'rif'=>'Rif',
			'contacto'=>'Contacto'),
			'filtro'  =>array('proveed'=>'C&oacute;digo','nombre'=>'Nombre','rif'=>'Rif'),
			'retornar'=>array('proveed'=>'cod_prov','nombre'=>'nombrep','reteiva'=>'reteiva_prov'),
			'script'=>array('cal_total()'),
			'titulo'  =>'Buscar Beneficiario');

		$bSPRV =$this->datasis->p_modbus($mSPRV ,"<#i#>");

		$bSPRV2=$this->datasis->modbus($mSPRV ,"sprv");

		$modbus=array(
			'tabla'   =>'v_presaldoante',
			'columnas'=>array(
				'codigoadm'   =>'Est. Admin',
				//'fondo'       =>'F. Financiamiento',
				'codigo'      =>'Partida',

				'denominacion'=>'Denominaci&oacute;n',
				'saldo'       =>'Saldo'
				),
			'filtro'  =>array(
				'codigoadm'   =>'Est. Admin',
				//'fondo'       =>'F. Financiamiento',
				'codigo'      =>'Partida',
				'denominacion'=>'Denominaci&oacute;n'
				),
			'retornar'=>array(
				'codigoadm'   =>'itcodigoadm_<#i#>',
				//'fondo'       =>'itfondo_<#i#>',
				'codigo'      =>'partida_<#i#>'),
			'where'=>'fondo = <#fondo#> AND codigo LIKE "4.%"',
			'p_uri'=>array(4=>'<#i#>',5=>'<#fondo#>'),
			'titulo'  =>'Busqueda de partidas');
		
		//$btn=$this->datasis->p_modbus($modbus,'<#i#>');
		$btn=$this->datasis->p_modbus($modbus,'<#i#>/<#fondo#>');
		$btn='<img src="/tortuga/assets/default/images/system-search.png"  alt="Busqueda de partidas" title="Busqueda de partidas" border="0" onclick="modbusdepen(<#i#>)"/>';

		$mNOMINA=array(
		'tabla'    =>'nomina',
		'columnas' =>array(
			'nomi'   =>'N&uacute;mero',
			'denomi' =>'Descripcion',
			'total'  =>'Total'),
		'filtro'  =>array(
			'nomi'   =>'N&uacute;mero',
			'denomi' =>'Descripcion',
			'total'  =>'Total'),
		'retornar'=>array(
			'nomi'   =>'nomina',
			'denomi' =>'denomin',
			'total'  =>'retenomina'),
		'titulo'  =>'Buscar Nominas');

		$bNOMINA=$this->datasis->p_modbus($mNOMINA,"nomina");

		$do = new DataObject("odirect");
		$do->pointer('sprv'   ,'sprv.proveed = odirect.cod_prov','sprv.nombre as nombrep, sprv.rif rif','LEFT');
		$do->rel_one_to_many('itodirect', 'itodirect', array('numero'=>'numero'));

		if($status=="create" && !empty($numero)){
			$do->load($numero);
			$do->set('status', 'N1');
			$do->unset_pk();

			/*$do->set('numero', '');
			$do->pk    =array('numero'=>'');
			//$do->loaded=0;
			for($i=0;$i < $do->count_rel('itodirect');$i++){
				$do->set_rel('itodirect','id'    ,'',$i);
				$do->set_rel('itodirect','numero','',$i);
			}
			*/
		}

		$edit = new DataDetails($this->tits, $do);
		$edit->back_url = site_url($this->url."filteredgrid/index");
		$edit->set_rel_title('itodirect','Rubro <#o#>');

		$edit->pre_process('update'  ,'_valida');
		$edit->pre_process('insert'  ,'_valida');
		$edit->post_process('insert'  ,'_post');
		$edit->post_process('update'  ,'_post');
		$edit->post_process('insert','_post_insert');
		$edit->post_process('update','_post_update');
		$edit->post_process('delete','_post_delete');
		
		$ivaplica=$this->ivaplica2();

		$edit->numero  = new inputField("N&uacute;mero", "numero");
		$edit->numero->mode="autohide";
		if($this->datasis->traevalor('USANODIRECT')=='S'){
			$edit->numero->when=array('show');
		}else{
			$edit->numero->when=array('show','create');
		}

		$edit->tipo = new dropdownField("Orden de ", "tipo");
		$edit->tipo->option("Compra"  ,"Compra");
		$edit->tipo->option("Servicio","Servicio");
		$edit->tipo->option("T","Transferencia");
		$edit->tipo->option("N","Nomina");
		$edit->tipo->style="width:100px;";

		$edit->fecha = new  dateonlyField("Fecha",  "fecha");
		$edit->fecha->insertValue = date('Y-m-d');
		$edit->fecha->size =12;

		$edit->uejecutora = new dropdownField("Unidad Ejecutora", "uejecutora");
		$edit->uejecutora->option("","Seccionar");
		$edit->uejecutora->options("SELECT codigo, nombre FROM uejecutora ORDER BY nombre");
		//$edit->uejecutora->onchange = "get_uadmin();";
		$edit->uejecutora->rule = "required";
		$edit->uejecutora->style = "width:200px";

		$lsnc='<a href="javascript:consulsprv();" title="Proveedor" onclick="">Consulta/Agrega BENEFICIARIO</a>';
		$edit->cod_prov = new inputField("Beneficiario", 'cod_prov');
		$edit->cod_prov->db_name  = "cod_prov";
		$edit->cod_prov->size     = 4;
		$edit->cod_prov->append($bSPRV2);
		$edit->cod_prov->append($lsnc);
		$edit->cod_prov->rule  = "required";

		$edit->nombrep = new inputField("Nombre", 'nombrep');
		$edit->nombrep->size = 30;
		//$edit->nombrep->readonly = true;
		$edit->nombrep->pointer = true;

		$edit->rif  = new inputField("RIF", "rif");
		$edit->rif->size=10;
		$edit->rif->pointer = true;
		if($status=='P')
		$edit->rif->readonly = true;

		$edit->reteiva_prov  = new inputField("% R.IVA", "reteiva_prov");
		$edit->reteiva_prov->size=2;
		//$edit->reteiva_prov->mode="autohide";
		$edit->reteiva_prov->when=array('modify','create');

		$edit->observa = new textAreaField("Observaciones", 'observa');
		$edit->observa->cols = 70;
		$edit->observa->rows = 3;

		$edit->factura  = new inputField("Factura", "factura");
		$edit->factura->size =15;
		//$edit->factura->rule ="callback_chexiste_factura";
		//$edit->factura->rule="required";

		$edit->controlfac  = new inputField("Control Fiscal", "controlfac");
		$edit->controlfac->size=15;
		//$edit->controlfac->rule="required";

		$edit->fechafac = new  dateonlyField("Fecha de Factura",  "fechafac");
		$edit->fechafac->insertValue = date('Y-m-d');
		$edit->fechafac->size =12;
		//$edit->fechafac->rule="required";

		$edit->simptimbre = new checkboxField("1X1000", "simptimbre", "S","N");
		$edit->simptimbre->insertValue = "N";
		$edit->simptimbre->onchange ='cal_timbre();';

		$edit->simpmunicipal = new checkboxField("I.Municipal", "simpmunicipal", "S","N");
		$edit->simpmunicipal->insertValue = "N";
		$edit->simpmunicipal->onchange ='cal_municipal();';

		$edit->imptimbre= new inputField("Impuesto 1X1000", 'imptimbre');
		$edit->imptimbre->size = 8;
		$edit->imptimbre->css_class='inputnum';
		$edit->imptimbre->value=0;

		$unsolofondo=$this->datasis->traevalor('UNSOLOFONDO','S','Indica si se utiliza una sola fuente de financiamiento');
		if($unsolofondo=='S'){
			$edit->fondo = new dropdownField("F. Financiamiento","fondo");
			$edit->fondo->rule   ='required';
			$edit->fondo->db_name='fondo';
			$edit->fondo->options("SELECT tipo,tipo a  FROM presupuesto WHERE tipo<>'' GROUP BY tipo ORDER BY tipo desc");
			$edit->fondo->style="width:100px;";
		}

		$edit->total= new inputField("Monto a Pagar", 'total');
		$edit->total->size = 8;
		$edit->total->css_class='inputnum';
		$edit->total->value = 0;

		$edit->retenomina= new inputField("Deducciones Nomina", 'retenomina');
		$edit->retenomina->size = 8;
		$edit->retenomina->css_class='inputnum';
		$edit->retenomina->onchange ='cal_total();';
		$edit->retenomina->value = 0;

		$edit->impmunicipal= new inputField("Impuesto Municipal", 'impmunicipal');
		$edit->impmunicipal->size = 8;
		$edit->impmunicipal->css_class='inputnum';
		$edit->impmunicipal->value = 0;

		$edit->subtotal = new inputField("Total Base Imponible", 'subtotal');
		$edit->subtotal->css_class='inputnum';
		$edit->subtotal->size = 8;
		$edit->subtotal->readonly=true;

		$edit->iva = new inputField("IVA", 'iva');
		$edit->iva->css_class='inputnum';
		$edit->iva->size = 8;
		$edit->iva->readonly=true;
		$edit->iva->value = 0;

		$edit->ivaa = new inputField("IVA Adicional", 'ivaa');
		$edit->ivaa->css_class='inputnum';
		$edit->ivaa->size = 8;
		$edit->ivaa->value = 0;

		$edit->ivag = new inputField("IVA General", 'ivag');
		$edit->ivag->css_class='inputnum';
		$edit->ivag->size = 8;
		$edit->ivag->value = 0;

		$edit->ivar = new inputField("IVA Reducido", 'ivar');
		$edit->ivar->css_class='inputnum';
		$edit->ivar->size = 8;
		$edit->ivar->value = 0;

		$edit->exento = new inputField("Exento", 'exento');
		$edit->exento->css_class='inputnum';
		$edit->exento->size = 8;
		$edit->exento->value = 0;
		
		$edit->mivaa = new inputField("Base ".$ivaplica['sobretasa']."%: ", 'mivaa');
		$edit->mivaa->size     = 12;
		$edit->mivaa->css_class='inputnum';
		$edit->mivaa->rule     ='numeric';
		$edit->mivaa->onchange ='cal_mivaa();';
		$edit->mivaa->value    =0;

		$edit->mivag = new inputField("Base ".$ivaplica['tasa']."%: ", 'mivag');
		$edit->mivag->size     = 12;
		$edit->mivag->css_class='inputnum';
		$edit->mivag->rule     ='numeric';
		$edit->mivag->onchange ='cal_mivag();';
		$edit->mivag->value    =0;

		$edit->mivar = new inputField("Base ".$ivaplica['redutasa']."%: ", 'mivar');
		$edit->mivar->size     = 12;
		$edit->mivar->css_class='inputnum';
		$edit->mivar->rule     ='numeric';
		$edit->mivar->onchange ='cal_mivar();';
		$edit->mivar->value    =0;

		$edit->mexento = new inputField("Exento a Retener: ", 'mexento');
		$edit->mexento->size = 12;
		$edit->mexento->css_class='inputnum';
		$edit->mexento->rule     ='numeric';
		$edit->mexento->onchange ='cal_total();';
		$edit->mexento->value    =0;

		$edit->reteiva = new inputField("Retencion IVA", 'reteiva');
		$edit->reteiva->css_class='inputnum';
		$edit->reteiva->size = 8;
		$edit->reteiva->value    =0;

		$edit->creten = new dropdownField("Codigo ISLR","creten");
		$edit->creten->options("SELECT codigo,CONCAT_WS(' ',codigo,activida) FROM rete ORDER BY codigo");
		$edit->creten->style="width:300px;";
		$edit->creten->onchange ='cal_total();';

		$edit->reten = new inputField("Retenci&oacute;n ISLR", 'reten');
		$edit->reten->css_class='inputnum';
		$edit->reten->size = 8;
		$edit->reten->value=0;
		
		$edit->otrasrete = new inputField("Otras Deducciones", 'otrasrete');
		$edit->otrasrete->css_class='inputnum';
		$edit->otrasrete->size = 8;
		$edit->otrasrete->insertValue=0;
		$edit->otrasrete->onchange ='cal_total();';
		$edit->otrasrete->value=0;
		
		$edit->amortiza  = new inputField("Amortizacion", "amortiza");
		$edit->amortiza->size = 8;
		$edit->amortiza->value = 0;
		$edit->amortiza->onchange ='cal_total();';

		$edit->total2 = new inputField("Total", 'total2');
		$edit->total2->css_class='inputnum';
		$edit->total2->size = 8;

		$edit->multiple = new dropdownField("Factura Multiple", 'multiple');
		$edit->multiple->option("N","NO");
		$edit->multiple->option("S","SI");
		$edit->multiple->style="width:50px;";

		$edit->itesiva = new dropdownField("P.IVA","itesiva_<#i#>");
		$edit->itesiva->rule   ='required';
		$edit->itesiva->db_name='esiva';
		$edit->itesiva->rel_id ='itodirect';
		$edit->itesiva->option("N","No");
		$edit->itesiva->option("S","Si");
		$edit->itesiva->option("A","Auto");
		$edit->itesiva->style="width:45px;";

		if($unsolofondo!='S'){
			$edit->itfondo = new dropdownField("Fondo","itfondo_<#i#>");
			$edit->itfondo->size   =10;
			$edit->itfondo->rule   ='required';
			$edit->itfondo->db_name='fondo';
			$edit->itfondo->rel_id ='itodirect';
			$edit->itfondo->options("SELECT tipo,tipo a  FROM presupuesto WHERE tipo<>'' GROUP BY tipo ORDER BY tipo desc");
			$edit->itfondo->style="width:100px;";
		}

		$edit->itcodigoadm = new inputField("Estructura	Administrativa","itcodigoadm_<#i#>");
		$edit->itcodigoadm->size   =10;
		$edit->itcodigoadm->db_name='codigoadm';
		$edit->itcodigoadm->rel_id ='itodirect';
		$edit->itcodigoadm->rule   ='required';
		$edit->itcodigoadm->autocomplete=false;

		$edit->itpartida = new inputField("(<#o#>) Partida", "partida_<#i#>");
		//$edit->itpartida->rule='|required';
		$edit->itpartida->size=15;
		$edit->itpartida->append($btn);
		$edit->itpartida->db_name='partida';
		$edit->itpartida->rel_id ='itodirect';
		$edit->itpartida->autocomplete=false;
		//$edit->itpartida->readonly =true;

		//$edit->itordinal = new inputField("(<#o#>) Ordinal", "ordinal_<#i#>");
		//$edit->itordinal->db_name  ='ordinal';
		//$edit->itordinal->maxlength=3;
		//$edit->itordinal->size     =5;
		//$edit->itordinal->rel_id   ='itodirect';

		$edit->itdescripcion = new inputField("(<#o#>) Descripci&oacute;n", "descripcion_<#i#>");
		$edit->itdescripcion->db_name  ='descripcion';
		$edit->itdescripcion->maxlength=80;
		$edit->itdescripcion->size     =15;
		//$edit->itdescripcion->rule     = 'required';
		$edit->itdescripcion->rel_id   ='itodirect';

		$edit->itunidad = new dropdownField("(<#o#>) Unidad", "unidad_<#i#>");
		$edit->itunidad->db_name= 'unidad';
		//$edit->itunidad->rule   = 'required';
		$edit->itunidad->rel_id = 'itodirect';
		$edit->itunidad->options("SELECT unidades AS id,unidades FROM unidad ORDER BY unidades");
		$edit->itunidad->style="width:60px";

		$edit->itcantidad = new inputField("(<#o#>) Cantidad", "cantidad_<#i#>");
		$edit->itcantidad->css_class='inputnum';
		$edit->itcantidad->db_name  ='cantidad';
		$edit->itcantidad->rel_id   ='itodirect';
		$edit->itcantidad->rule     ='numeric';
		$edit->itcantidad->onchange ='cal_importe(<#i#>);';
		$edit->itcantidad->size     =5;

		$edit->itprecio = new inputField("(<#o#>) Precio", "precio_<#i#>");
		$edit->itprecio->css_class='inputnum';
		$edit->itprecio->db_name  ='precio';
		$edit->itprecio->rel_id   ='itodirect';
		$edit->itprecio->rule     ='callback_positivo';
		$edit->itprecio->onchange ='cal_importe(<#i#>);';
		$edit->itprecio->size     =8;

		$edit->itusaislr = new dropdownField("(<#o#>) Islr", "usaislr_<#i#>");
		$edit->itusaislr->db_name     = 'usaislr';
		$edit->itusaislr->rel_id      = 'itodirect';
		$edit->itusaislr->insertValue = "N";
		$edit->itusaislr->onchange ='cal_total();';
		$edit->itusaislr->option("N","No");
		$edit->itusaislr->option("S","Si");
		$edit->itusaislr->style="width:45px";

		//$edit->itusaislr = new checkboxField("(<#o#>) Islr", "usaislr_<#i#>","Y","N");
		//$edit->itusaislr->db_name     = 'usaislr';
		//$edit->itusaislr->rel_id      = 'itodirect';
		//$edit->itusaislr->insertValue = "N";
		//$edit->itusaislr->when        = array("modify","create");
		//$edit->itusaislr->onchange ='cal_total();';

		$edit->itislr = new inputField("(<#o#>) Islr", "islr_<#i#>");
		$edit->itislr->css_class='inputnum';
		$edit->itislr->db_name  ='islr';
		$edit->itislr->rel_id   ='itodirect';
		$edit->itislr->rule     ='numeric';
		$edit->itislr->readonly =true;
		$edit->itislr->size     =5;

		$edit->itiva = new dropdownField("(<#o#>) IVA", "iva_<#i#>");
		$edit->itiva->db_name  = 'iva';
		$edit->itiva->rel_id   = 'itodirect';
		$edit->itiva->onchange = 'cal_importe(<#i#>);';
		$edit->itiva->options($this->_ivaplica());
		$edit->itiva->option("0"  ,"Exento");
		$edit->itiva->style    = "width:80px";

		$edit->itimporte = new inputField("(<#o#>) Importe", "importe_<#i#>");
		$edit->itimporte->css_class='inputnum';
		$edit->itimporte->db_name  ='importe';
		$edit->itimporte->rel_id   ='itodirect';
		$edit->itimporte->rule     ='numeric';
		//$edit->itimporte->readonly =true;
		$edit->itimporte->size     =8;
		$edit->itimporte->onchange = 'cal_importep(<#i#>);';

		$edit->status = new dropdownField("Estado","status");
		$edit->status->option("","");
		$edit->status->option("N2","Actualizado");
		$edit->status->option("N1","Sin Actualizar");
		$edit->status->option("N3","Pagado");
		$edit->status->when = array('show');
		$edit->status->style="width:150px";

		$status=$edit->get_from_dataobjetct('status');
		if($status=='N1'){
			$action = "javascript:window.location='" .site_url($this->url.'actualizar/'.$edit->rapyd->uri->get_edited_id()). "'";
			$edit->button_status("btn_status",'Actualizar',$action,"TR","show");
			$action = "javascript:btn_anulaf('".$edit->rapyd->uri->get_edited_id()."')";
			$edit->button_status("btn_anular",'Anular',$action,"TR","show");
			$edit->buttons("modify","save");
		}elseif($status=='N2'){
			$action = "javascript:window.location='" .site_url('presupuesto/opago/modconc/odirect/modify/'.$edit->rapyd->uri->get_edited_id()). "'";
			$edit->button_status("btn_moconc",'Modificar Concepto',$action,"TR","show");
			//$action = "javascript:window.location='" .site_url($this->url.'reversar/'.$edit->rapyd->uri->get_edited_id()). "'";
			//$edit->button_status("btn_rever",'Reversar',$action,"TR","show");
			$action = "javascript:btn_anulaf('".$edit->rapyd->uri->get_edited_id()."')";
			$edit->button_status("btn_anular",'Anular',$action,"TR","show");
		}elseif($status=='N3'){
			$multiple=$edit->get_from_dataobjetct('multiple');
			if($multiple=="N"){
				$action = "javascript:window.location='" .site_url($this->url.'camfac/dataedit/modify/'.$edit->rapyd->uri->get_edited_id()). "'";
				$edit->button_status("btn_camfac",'Modificar Factura',$action,"TR","show");
			}
		}elseif($status=="N"){
			$edit->buttons("modify","save");
		}else{
			$edit->buttons("save");
		}

		$edit->buttons("undo","back","add_rel","add");
		$edit->build();

		//SELECT codigo,base1,tari1,pama1 FROM rete
		$query = $this->db->query('SELECT codigo,base1,tari1,pama1 FROM rete');

		$rt=array();
		foreach ($query->result_array() as $row){
			$pivot=array('base1'=>$row['base1'],
			             'tari1'=>$row['tari1'],
			             'pama1'=>$row['pama1']);
			$rt['_'.$row['codigo']]=$pivot;
		}
		$rete=json_encode($rt);

		$conten['rete']=$rete;
		$ivaplica=$this->ivaplica2();
		$conten['ivar']=$ivaplica['redutasa'];
		$conten['ivag']=$ivaplica['tasa'];
		$conten['ivaa']=$ivaplica['sobretasa'];
		$conten['imptimbre']=$this->datasis->traevalor('IMPTIMBRE');
		$conten['impmunicipal']=$this->datasis->traevalor('IMPMUNICIPAL');


		$smenu['link']   =barra_menu('121');
		$data['smenu']   = $this->load->view('view_sub_menu', $smenu,true);
		$conten["form"]  =&  $edit;
		$data['content'] = $this->load->view('view_opagoante', $conten,true);
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

		$this->rapyd->load('dataobject');

		$do = new DataObject("odirect");
		$do->rel_one_to_many('itodirect', 'itodirect', array('numero'=>'numero'));
		$do->load($id);

		$error      = "";
		$multiple   = $do->get('multiple'     );
		$factura    = $do->get('factura'      );
		$controlfac = $do->get('controlfac'   );
		$fechafac   = $do->get('fechafac'     );
		$reteiva    = $do->get('reteiva'      );

		if($multiple == 'N'){
			if($reteiva > 0 && (empty($factura) || empty($controlfac) || empty($fechafac)))
				$error.="<div class='alert'><p> Los campos Nro. Factura, Nro Control y Fecha factura no pueden estar en blanco</p></div>";
		}else{
			$facs = $this->datasis->dameval("SELECT COUNT(*) FROM itfac WHERE numero=$id ");
			if($facs <= 0)
				$error.="<div class='alert'><p> Debe ingresar las factura por el modulo de factura multiple primero</p></div>";
		}

		if(empty($error)){
			$sta=$do->get('status');
			if($sta=="N1"){
				$importes=array(); $ivas=array();$admfondo=array();
				for($i=0;$i  < $do->count_rel('itodirect');$i++){
					$codigoadm   = $do->get_rel('itodirect','codigoadm',$i);
					$fondo       = $do->get_rel('itodirect','fondo'    ,$i);
					$codigopres  = $do->get_rel('itodirect','partida'  ,$i);
					$iva         = $do->get_rel('itodirect','iva'      ,$i);
					$importe     = $do->get_rel('itodirect','importe'  ,$i);
					$ordinal     = $do->get_rel('itodirect','ordinal'  ,$i);
					$ivan        = $importe*$iva/100;

					//$error.=$this->itpartida($codigoadm,$fondo,$codigopres,$ordinal);

					$cadena = $codigoadm.'_._'.$fondo.'_._'.$codigopres;
					if(array_key_exists($cadena,$importes)){
						$importes[$cadena]+=$importe;
						//$ivas[$cadena.'_._'.$iva]     =$iva;
					}else{
						$importes[$cadena]  =$importe;
						//$ivas[$cadena.'_._'.$iva]      =$iva;
					}
					$cadena2 = $codigoadm.'_._'.$fondo;
					$admfondo[$cadena2]=(array_key_exists($cadena2,$admfondo)?$admfondo[$cadena2]+=$ivan:$admfondo[$cadena2] =$ivan);
				}

				if(empty($error)){
					//foreach($admfondo AS $cadena=>$monto){
					//	$temp  = explode('_._',$cadena);
					//	$error.=$this->chequeapresup($temp[0],$temp[1],'PARTIDAIVA','',$monto,0,'round($monto,2) > $disponible=round(($presupuesto-$comprometido),2)','El Monto ($monto) es mayor al disponible (round(($disponible),2)) para la partida de IVA, ('.$temp[0].')('.$temp[1].') ');
					//}

					foreach($importes AS $cadena=>$monto){
						$temp  = explode('_._',$cadena);
						//$iva   = $ivas[$cadena];

						//$error.=$this->chequeapresup($temp[0],$temp[1],$temp[2],'',$monto,0,'round($monto,2) > $disponible=round(($presupuesto-$comprometido),2)','El Monto ($monto) es mayor al disponible ($disponible) para la partida ('.$temp[0].') ('.$temp[1].') ('.$temp[2].') ');
					}
				}

				if(empty($error)){
					foreach($importes AS $cadena=>$monto){
						$temp  = explode('_._',$cadena);
						//$iva   = $ivas[$cadena];
						//$error.=$this->afectapresup($temp[0],$temp[1],$temp[2],'',$monto,0, 1 ,array("comprometido","causado","opago"));
					}
					//if(empty($error)){
					//	foreach($admfondo AS $cadena=>$monto){
					//		$temp  = explode('_._',$cadena);
					//		$error.=$this->afectapresup($temp[0],$temp[1],'PARTIDAIVA','',$monto,0, 1 ,array("comprometido","causado","opago"));
					//	}
					//}

					if(empty($error)){
						$do->set('fopago',date('Ymd'));
						$do->set('status','N2');
						$do->save();
					}
				}
			}
		}

		if(empty($error)){
			logusu('odirect',"Actualizo Orden de Pago Ejercicio Anterior Nro $id");
			redirect($this->url."dataedit/show/$id");
		}else{
			logusu('odirect',"Actualizo Orden de Pago Ejercicio Anterior Nro $id. con ERROR:$error ");
			$data['content'] = $error.anchor($this->url."dataedit/show/$id",'Regresar');
			$data['title']   = " $this->tits ";
			$data["head"]    = $this->rapyd->get_head().script('jquery.js').script("plugins/jquery.numeric.pack.js");
			$this->load->view('view_ventanas', $data);
		}
	}

	function _ivaplica($mfecha=NULL){
		if(empty($mfecha)) $mfecha=date('Ymd');
		$qq = $this->datasis->damerow("SELECT tasa AS g, redutasa AS r, sobretasa AS a FROM civa WHERE fecha < '$mfecha' ORDER BY fecha DESC LIMIT 1");
		$rr=array();
		foreach ($qq AS $val){
			$rr[$val]=$val.'%';
		}
		$rr['0']='0%';
		return $rr;
	}

	function _valida($do){

		$this->rapyd->load('dataobject');
		$error        = '';
		$rr           = $this->ivaplica2();
		$reteiva_prov = $do->get('reteiva_prov');
		$creten       = $do->get('creten'      );
		$cod_prov     = $do->get('cod_prov'    );
		$tipo         = $do->get('tipo'        );
		$factura      = $do->get('factura'     );
		$controlfac   = $do->get('controlfac'  );
		$fechafac     = $do->get('fechafac'    );
		$multiple     = $do->get('multiple'    );
		$numero       = $do->get('numero'      );
		$retenomina   = $do->get('retenomina'  );
		$nomina       = $do->get('nomina'      );
		$fondo        = $do->get('fondo'       );
		$otrasrete    = $do->get('otrasrete'   );
		$reten        = $do->get('reten'       );
		$reteiva      = $do->get('reteiva'     );
		$imptimbre    = $do->get('imptimbre'   );
		$do->set('status'        , 'N1'                 );
		$obligapiva   = $this->datasis->traevalor('obligapiva');
		$partidaiva   = $this->datasis->traevalor("PARTIDAIVA");
		$numero       = $do->get('numero'      );

		if($multiple=="S"){
			$do->set('controlfac','');
		  $do->set('factura'   ,'');
		  $do->set('fechafac'  ,'');

		}
		/*
		if($tipo == 'Compra'){
			$do->set('creten','');
			$do->set('reten' ,0);
		}
		*/

		$rete['tari1'] = 0;
		$rete=$this->datasis->damerow("SELECT base1,tari1,pama1 FROM rete WHERE codigo='$creten'");
		if($reteiva_prov!=75)$reteiva_prov=100;

		$giva=$aiva=$riva=$exento=$subtotal=$subtotal2=$tiva=$mivag=$mivar=$mivaa=$tivag=$tivar=$tivaa=$treten=0;
		$admfondo=array();$admfondop=array();$borrarivas=array();$ivasm=0;$totiva=0;
		for($i=0;$i < $do->count_rel('itodirect');$i++){
			$do->set_rel('itodirect','fondo' ,$fondo       ,$i);
			$cantidad   = round($do->get_rel('itodirect'  ,'cantidad'     ,$i),2);
			$precio     = round($do->get_rel('itodirect'  ,'precio'       ,$i),2);
			$piva       = round($do->get_rel('itodirect'  ,'iva'          ,$i),2);
			$importe    = round($do->get_rel('itodirect'  ,'importe'      ,$i),2);
			$codigoadm  =       $do->get_rel('itodirect'  ,'codigoadm'    ,$i);
			$fondo      =       $do->get_rel('itodirect'  ,'fondo'        ,$i);
			$partida    =       $do->get_rel('itodirect'  ,'partida'      ,$i);
			$codprov    =       $do->get_rel('itodirect'  ,'codprov'      ,$i);
			$ordinal    =       $do->get_rel('itodirect'  ,'ordinal'      ,$i);
			$esiva      =       $do->get_rel('itodirect'  ,'esiva'        ,$i);
			$ivan       = $importe*$piva/100;

			//$error.=$this->itpartida($codigoadm,$fondo,$partida);

			if($esiva=='S'){
				$ivasm+=$importe;
			}elseif($esiva=='A'){
				$borrarivas[$i]=$i;
			}else{
				$totiva+=$ivan;
				$a = $cantidad * $precio;
				if((($a-$importe)>0.05) || (($importe-$a) > 0.05))
					$error.="<div class='alert'>El Importe Introducido es incorrecto.</div>";

				if($tipo=='T' || $tipo=='N'){
					$do->set_rel('itodirect'  ,'cantidad',1        ,$i);
					$do->set_rel('itodirect'  ,'precio'  ,$importe ,$i);
					$do->set_rel('itodirect'  ,'iva'     ,0        ,$i);
					$subtotal  += $importe;
					$giva  =0;
					$mivag =0;
					$riva  =0;
					$mivar =0;
					$aiva  =0;
					$mivaa =0;
				}else{
					$importe    = $precio * $cantidad;

					$subtotal  += $importe;
					if($piva==$rr['tasa']     ){
						$giva+=round(($rr['tasa']     *$importe)/100,2);
						$mivag+=$importe;
					}
					if($piva==$rr['redutasa'] ){
						$riva+=round($rr['redutasa'] * $importe/100,2);
						$mivar+=$importe;
					}
					if($piva==$rr['sobretasa']){
						$aiva+=round(($rr['sobretasa']* $importe)/100,2);
						$mivaa+=$importe;
					}

					if($piva==0)$exento+=$importe;
				}

				$do->set_rel('itodirect','importe' ,$importe,$i);

				/*

				if($rete && $tipo == 'Servicio' && $do->get_rel('itodirect','usaislr',$i)=="S"){
				//exit('HELLO WORLD');
					if(substr($creten,0,1)=='1')$reten=round($importe*$rete['base1']*$rete['tari1']/10000,2);
					else $reten=round(($importe-$rete['pama1'])*$rete['base1']*$rete['tari1']/10000,2);

					if($reten < 0)$reten=0;

					$treten+=$reten;
					$do->set_rel('itodirect','preten' , $rete['tari1']  ,$i );
					$do->set_rel('itodirect','islr'   , $reten          ,$i );
				}else{
					$do->set_rel('itodirect','islr'   , 0 ,$i );
				}
				//}
				*/
				if($tipo=='T' || $tipo=='N'){
					 $do->get_rel('itodirect'  ,'cantidad',1  ,$i);
					 $do->get_rel('itodirect'  ,'precio'  ,$precio  ,$i);
				}
				$presupiva=$this->datasis->dameval("SELECT (aumento+asignacion-disminucion+traslados-(comprometido)) FROM presupuestoante WHERE codigoadm='$codigoadm' AND tipo='$fondo' AND codigopres='$partidaiva'");
				if($presupiva>0)
					$partida2=$partidaiva;
				else
					$partida2=$partida;

				if($obligapiva!='S'){
					$cadena3 = $codigoadm.'_._'.$fondo.'_._'.$partida2;
					$admfondop[$cadena3]=(array_key_exists($cadena3,$admfondop)?$admfondop[$cadena3]+=$ivan:$admfondop[$cadena3] =$ivan);
				}else{
					$cadena2 = $codigoadm.'_._'.$fondo;
					$admfondo[$cadena2]=(array_key_exists($cadena2,$admfondo)?$admfondo[$cadena2]+=$ivan:$admfondo[$cadena2] =$ivan);
				}
			}
		}

		if($ivasm>0){
			if($totiva!=$ivasm)
				$error.="La suma de las partidas a descontar el IVA ($ivasm) debe ser igual a la suma del IVA del IVA total ($totiva)";
		}else{
			$admfondo2 = ($obligapiva!='S'?$admfondop:$admfondo);
			foreach($admfondo2 AS $cadena=>$monto){
				if($monto>0){
					$temp  = explode('_._',$cadena);
					$p = ($obligapiva!='S'?$temp[2]:$partidaiva);
					$i++;
					$do->set_rel('itodirect','codigoadm'   ,$temp[0]    ,$i);
					$do->set_rel('itodirect','fondo'       ,$temp[1]    ,$i);
					$do->set_rel('itodirect','partida'     ,$p          ,$i);
					$do->set_rel('itodirect','descripcion' ,'IVA'       ,$i);
					$do->set_rel('itodirect','unidad'      ,'MONTO'     ,$i);
					$do->set_rel('itodirect','iva'         , 0          ,$i);
					$do->set_rel('itodirect','esiva'       ,'A'         ,$i);
					$do->set_rel('itodirect','importe'     ,$monto      ,$i);
					$do->set_rel('itodirect','cantidad'    ,1           ,$i);
					$do->set_rel('itodirect','precio'      ,$monto      ,$i);
				}
			}
		}

		foreach($borrarivas AS $value)
			array_splice($do->data_rel['itodirect'],$value,1);

		//$reten = 0;
		/*
		if(!empty($cod_prov)){
			$reteiva=(($giva+$riva+$aiva)*$reteiva_prov)/100;
			$do->set('reten'     ,    $treten     );
		}else{
			$reteiva=0;
		}
		*/

		$total2=$giva+$riva+$aiva+$subtotal;
		$total =$total2-$reteiva-$treten-$retenomina-$otrasrete;

		$impm=$impt=0;
		if($do->get('simptimbre')=='S')
			$total       -=$impt=($subtotal /$this->datasis->traevalor('IMPTIMBRE'));

		if($do->get('simpmunicipal')=='S')
			$total       -=$impm= ($subtotal * $this->datasis->traevalor('IMPMUNICIPAL')/100);

		if($reteiva>0 && $multiple=="N" && (empty($fechafac) || empty($factura) || empty($controlfac))){
			$error.="<div class='alert'><p>Los Campos Factura, Control Fiscal y fecha factura no pueden estar en blanco</p></div>";
		}

		if( ($reteiva+$treten >0) AND $multiple=='N'){
			$this->chexiste_factura($numero,$factura,$controlfac,$cod_prov,'B',$e);
			$error.=$e;
		}

		if($tipo=='T' || $tipo=='N'){
			$impm     = 0;
			$impt     = 0;
			$tiva     = 0;
			$giva     = 0;
			$riva     = 0;
			$aiva     = 0;
			$mivag    = 0;
			$mivar    = 0;
			$mivaa    = 0;
			$exento   = 0;
			$reteiva  = 0;
			$total    = $subtotal;
			$total2   = $subtotal;
			//$do->set('reten'         , 0    );
			$do->set('reteiva'       , 0    );
			$do->set('factura'       , ''   );
			$do->set('controlfac'    , ''   );
			$do->set('fechafac'      , ''   );
		}

		//$do->set('impmunicipal'  , $impm                );
		//$do->set('imptimbre'     , $impt                );
		//$do->set('iva'           , $tiva                );
		//$do->set('ivag'          , $giva                );
		//$do->set('ivar'          , $riva                );
		//$do->set('ivaa'          , $aiva                );
		//$do->set('tivag'         , $rr['tasa']          );
		//$do->set('tivar'         , $rr['redutasa']      );
		//$do->set('tivaa'         , $rr['sobretasa']     );
		//$do->set('mivag'         , $mivag               );
		//$do->set('mivar'         , $mivar               );
		//$do->set('mivaa'         , $mivaa               );
		//$do->set('subtotal'      , $subtotal            );
		//$do->set('exento'        , $exento              );
		//$do->set('reteiva'       , $reteiva             );
		$do->set('total'         , $total               );
		$do->set('total2'        , $total2              );
		$do->set('status'        , 'N1'                 );
		//if($reten>0)
		//$do->set('breten'        , $rete['tari1']       );

		//$error.="<span class='alert'>esto seria lo primero con espan</span></span class='alert'>esto debria estar en la misma lineas</span>";

		if(empty($error) && empty($do->loaded)){
			if(empty($numero)){
				if($this->datasis->traevalor('USANODIRECT')=='S'){
					$nodirect = $this->datasis->fprox_numero('nodirectante');
					$do->set('numero','C'.$nodirect);
					$do->pk=array('numero'=>'C'.$nodirect);
				}else
					$error.="Debe introducir un numero de orden de pago</br>";
			}elseif($this->datasis->traevalor('USANODIRECT')!='S'){
				$numeroe = $this->db->escape($numero);
				$chk     = $this->datasis->dameval("SELECT COUNT(*) FROM odirect WHERE numero=$numeroe");
				if($chk>0)
					$error.="Error el numero de orden de pago ya existe</br>";
			}
		}

		if($this->datasis->traevalor('USANOMINA')=='N' && !empty($nomina)){
			$chk = $this->datasis->dameval("SELECT COUNT(*) FROM nomina WHERE nomi=$nomina");
			if($chk <= 0){
				$this->db->simple_query("INSERT INTO `nomina` (`nomi`) VALUES($nomina)");
			}
		}
		if(!empty($error)){
			$do->error_message_ar['pre_ins']=$error;
			$do->error_message_ar['pre_upd']=$error;
			return false;
		}
	}

	function tari1(){
		$creten=$this->db->escape($this->input->post('creten'));
		$a=$this->datasis->damerow("SELECT base1,tari1,pama1 FROM rete WHERE codigo=$creten");
		echo json_encode($a);
	}


	function camfac($var1,$var2,$id){
		$this->rapyd->load('dataedit2');

		$edit = new DataEdit2("Cambiar datos de Factura","odirect");

		$edit->back_url = $this->url."/dataedit/show/$id";

		$edit->pre_process('update'  ,'_validafac');
		$edit->post_process('update','_postfac');

		$edit->numero  = new inputField("N&uacute;mero", "numero");
		$edit->numero->mode="autohide";
		$edit->numero->when=array('show');

		$edit->factura  = new inputField("Factura", "factura");
		$edit->factura->size =15;
		//$edit->factura->rule ="callback_chexiste_factura";
		$edit->factura->rule="required";

		$edit->controlfac  = new inputField("Control Fiscal", "controlfac");
		$edit->controlfac->size=15;
		$edit->controlfac->rule="required";

		$edit->fechafac = new  dateonlyField("Fecha de Factura",  "fechafac");
		$edit->fechafac->insertValue = date('Y-m-d');
		$edit->fechafac->size =12;

		$edit->buttons("save", "undo", "back");
		$edit->build();

		$data['content'] = $edit->output;
		$data["head"]    = $this->rapyd->get_head();
		$data['title']   = ' Cambiar Datos de Factura ';
		$this->load->view('view_ventanas', $data);
	}

	function _validafac($do){
		$this->rapyd->load('dataobject');

		$factura    = $do->get('factura'   );
	  $controlfac = $do->get('controlfac');
	  $fechafac   = $do->get('fechafac'  );
	  $numero     = $do->get('numero'    );
	  $cod_prov   = $do->get('cod_prov'  );

	  $this->chexiste_factura($numero,$factura,$controlfac,$cod_prov,'B',$e);
	  $error=$e;

	  $riva = new DataObject("riva");
	  $riva->load_where(array('odirect'=>$numero,'status <>'=>'A','status <>'=>'AN'));

	  $status  = $riva->get('status');
	  $nrocomp = $riva->get('nrocomp');

	  if((!empty($nrocomp)) && !($status=='B' || $status=='A')){
	  	$error.="No se puede cambiar el numero de factura debido a que la retencion de iva ($nrocomp) ya fue declarada";
	  }

	  if(empty($error)){
	 		$riva->set('numero'   , $factura       );
	    $riva->set('nfiscal'  , $controlfac    );
	    $riva->set('ffactura' , $fechafac      );
	    $riva->save();
	  }else{
			$do->error_message_ar['pre_ins']=$error;
			$do->error_message_ar['pre_upd']=$error;
			logusu('odirect',"Cambio datos Factura $factura Nro $controlfac fecha $fechafac,orden de pago $numero con error $error");
			return false;
		}
	}

	function _postfac($do){
		$factura    = $do->get('factura'   );
	  $controlfac = $do->get('controlfac');
	  $fechafac   = $do->get('fechafac'  );
	  $numero     = $do->get('numero'    );

		logusu('odirect',"Cambio datos Factura $factura Nro $controlfac fecha $fechafac, orden de pago $numero");
		redirect($this->url."dataedit/show/$numero");
	}

	function reversarall(){
		$query = $this->db->query("SELECT * FROM odirect WHERE status = 'B2' ");
		$result = $query->result();
		 foreach ($result AS $items){
		 	$numero =$items->numero;
		 	$this->reversar($numero);
		 }
	}
	function actualizarall(){
		$query = $this->db->query("SELECT * FROM odirect WHERE status = 'B1' ");
		$result = $query->result();
		 foreach ($result AS $items){
		 $numero =$items->numero;
		 	$this->actualizar($numero);
		 }
	}

	function selformato($id){

		$this->rapyd->load('dataobject');

		$do = new DataObject("odirect");
		$do->load($id);

		$error  = "";
		$tipo   = $do->get('tipo' );

		if($tipo=="N")redirect("formatos/ver/OPNOMI/$id");
		else
			redirect("formatos/ver/ODIRECT/$id");
	}

	function _post_insert($do){
		$numero = $do->get('numero');
		logusu('odirect',"Creo Orden de Pago Directo Nro $numero");
		//redirect($this->url."actualizar/$numero");
	}
	function _post_update($do){
		$numero = $do->get('numero');
		logusu('odirect'," Modifico Orden de Pago Directo Nro $numero");
		//redirect($this->url."actualizar/$numero");
	}
	function _post_delete($do){
		$numero = $do->get('numero');
		logusu('odirect'," Elimino Orden de Pago Directo Nro $numero");
	}

	function instalar(){
			echo $this->db->simple_query("ALTER TABLE `itodirect` CHANGE COLUMN `partida` `partida` VARCHAR(25) NULL DEFAULT NULL COMMENT 'Partida Presupuestaria'");
			echo $this->db->simple_query("ALTER TABLE `odirect` CHANGE COLUMN `numero` `numero` VARCHAR(12) NOT NULL COMMENT 'Nro de La Orden Pago'  ");
			echo $this->db->simple_query("ALTER TABLE `itodirect` CHANGE COLUMN `numero` `numero` VARCHAR(12) NULL DEFAULT NULL COMMENT 'Numero de la Orden'  ");
			echo $this->db->simple_query("ALTER TABLE `nomi`  CHANGE COLUMN `opago` `opago` VARCHAR(12) NULL DEFAULT NULL AFTER `fcomprome`");
			echo $this->db->simple_query("ALTER TABLE `odirect`  CHANGE COLUMN `nomina` `nomina` VARCHAR(12) NULL DEFAULT NULL");
			echo $this->db->simple_query("ALTER TABLE `odirect`  ADD COLUMN `cod_prov2` VARCHAR(5) NULL DEFAULT NULL AFTER `mcrs`");


	}
}
?>
