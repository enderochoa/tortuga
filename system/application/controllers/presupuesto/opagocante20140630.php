
<?php
require_once(BASEPATH.'application/controllers/presupuesto/common.php');
class Opagoc extends Common {

	var $titp  = 'Ordenes de Pago de Contratos';
	var $tits  = 'Orden de Pago de Contratos';
	var $url   = 'presupuesto/opagoc/';

	function Opagoc(){
		parent::Controller();
		$this->load->library("rapyd");
		$this->formatopres =$this->datasis->traevalor('FORMATOPRES');
		$this->flongpres   =strlen(trim($this->formatopres));
		//$this->datasis->modulo_id(116,1);
	}

	function index(){
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
		$filter->db->select(array("b.ocompra","a.reverso","b.codigoadm","b.fondo","b.partida","b.ordinal","a.numero","a.fecha","a.tipo","a.compra","a.uejecutora","a.estadmin","a.fondo","a.cod_prov","a.nombre","a.beneficiario","a.pago","a.total2","a.status","MID(a.observa,1,50) observa","c.nombre nombre2"));
		$filter->db->from("odirect a");
		$filter->db->join("itodirect b" ,"a.numero=b.numero");
		$filter->db->join("sprv c"      ,"c.proveed =a.cod_prov");
		$filter->db->where('MID(status,1,1) ','C');
		$filter->db->groupby("a.numero");

		$filter->numero = new inputField("N&uacute;mero", "numero");
		$filter->numero->size=12;
		//$filter->numero->clause="likerigth";
		$filter->numero->db_name = 'a.numero';
		
		$filter->ocompra = new inputField("Compromiso Ref", "ocompra");
		$filter->ocompra->size=12;
		$filter->ocompra->db_name = 'b.ocompra';

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

		$filter->cod_prov = new inputField("Beneficiario", 'cod_prov');
		$filter->cod_prov->size = 6;
		$filter->cod_prov->append($bSPRV);
		$filter->cod_prov->rule = "required";
		$filter->cod_prov->db_name = 'a.cod_prov';
		$filter->cod_prov->clause   = 'where';
		$filter->cod_prov->operator = '=';

		$filter->codigo = new inputField("Codigo Presupuestario", "codigo");
		$filter->codigo->db_name = 'CONCAT(b.codigoadm,".",b.partida)';

		$filter->fondo = new dropdownField("Fondo", "fondo");
		$filter->fondo->option("","");
		$filter->fondo->options("SELECT fondo,CONCAT_WS(' ',fondo,descrip)a FROM fondo");
		$filter->fondo->db_name = 'b.fondo';

		$filter->observa = new inputField("Observacion", "observa");
		$filter->observa->size=20;
		$filter->observa->db_name='a.observa';

		$filter->total2 = new inputField("Monto", "total2");
		$filter->total2->size=20;

		$filter->status = new dropdownField("Estado","status");
		$filter->status->option("","");
		$filter->status->option("C","Pendiente");
		$filter->status->option("C2","Causado");
		$filter->status->option("C1","Por Causar");
		$filter->status->option("C3","Pagado");
		$filter->status->option("CA","Anulado");
		$filter->status->style="width:150px";
		$filter->status->db_name = 'a.status';

		$filter->buttons("reset","search");

		$filter->build();
		$uri = anchor($this->url.'dataedit/show/<#numero#>','<#numero#>');
		$uri_2 = anchor($this->url.'dataedit/create/<#numero#>','Duplicar');

		function sta($status){
			switch($status){
				case "C1":return "Por Causar";break;
				case "C2":return "Causado";break;
				case "C3":return "Pagado";break;
				case "C":return "Pendiente";break;
				case "CA":return "Anulado";break;
			}
		}

		function tipo($tipo){
			switch($tipo){
				case "C":return "Contrato";break;
				case "T":return "Transferencia";break;
				case "N":return "N&oacute;mina";break;
			}
		}

		$grid = new DataGrid($this->titp);
		$grid->order_by("numero","desc");
		$grid->per_page = 20;
		$grid->use_function('substr','str_pad','sta','tipo');

		$grid->column_orderby("N&uacute;mero"    ,$uri                                             ,"numero"                            );
		$grid->column_orderby("Tipo"             ,"<tipo><#tipo#></tipo>"                          ,"tipo"           ,"align='center'"  );
		$grid->column_orderby("Fecha"            ,"<dbdate_to_human><#fecha#></dbdate_to_human>"   ,"fecha"          ,"align='center'"  );
		$grid->column_orderby("Compromiso Ref"   ,"ocompra"                                        ,"ocompra"        ,"NOWRAP"          );
		$grid->column_orderby("Est. Adm"         ,"codigoadm"                                      ,"estamdin"       ,"NOWRAP"          );
		$grid->column_orderby("Fondo"            ,"fondo"                                          ,"fondo"          ,"NOWRAP"          );
		$grid->column_orderby("Partida"          ,"partida"                                        ,"partida"        ,"NOWRAP"          );
		$grid->column_orderby("Beneficiario"     ,"nombre2"                                        ,"c.nombre"                          );//,"NOWRAP"
		//$grid->column_orderby("Observacion"      ,"observa"                                        ,"observa"               );//,"NOWRAP"
		$grid->column_orderby("Pago"             ,"<number_format><#total2#>|2|,|.</number_format>","total2"         ,"align='right'"   );
		$grid->column_orderby("Estado"           ,"<sta><#status#></sta>"                          ,"status"         ,"align='center' " );//NOWRAP
		$grid->column("Duplicar"                 ,$uri_2                                           ,"align='center'");

		$grid->add($this->url."selectoc");
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

		$bSPRV2=$this->datasis->modbus($mSPRV ,"sprv");

		$do = new DataObject("odirect");
		$do->pointer('sprv'   ,'sprv.proveed = odirect.cod_prov','sprv.nombre as nombrep, sprv.rif rifp','LEFT');
		$do->rel_one_to_many('itodirect', 'itodirect', array('numero'=>'numero'));
		$do->rel_pointer('itodirect','v_presaldo' ,'itodirect.codigoadm=v_presaldo.codigoadm AND itodirect.fondo=v_presaldo.fondo AND itodirect.partida=v_presaldo.codigo ',"v_presaldo.denominacion as pdenominacion");

		if($status=="create" && !empty($numero)){
			$do->load($numero);
			$do->set('status', 'C1');
			$do->unset_pk();
		}

		$edit = new DataDetails($this->tits, $do);
		$edit->back_url = site_url($this->url."filteredgrid/index");
		$edit->set_rel_title('itodirect','Rubro <#o#>');

		$edit->pre_process('update'  ,'_valida');
		$edit->pre_process('insert'  ,'_valida');
		$edit->pre_process('delete'  ,'_pre_delete');
		$edit->post_process('insert'  ,'_post');
		$edit->post_process('update'  ,'_post');
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

		$edit->tipo = new hiddenField("Orden de ", "tipo");
		$edit->tipo->value ="Contrato";

		$edit->fecha = new  dateonlyField("Fecha",  "fecha");
		$edit->fecha->insertValue = date('Y-m-d');
		$edit->fecha->size =12;

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

		$edit->rifp  = new inputField("RIF", "rifp");
		$edit->rifp->size=10;
		$edit->rifp->pointer = true;
		$edit->rifp->db_name='rifp';
		//if($status=='P')
		//$edit->rif->readonly = true;

		$edit->reteiva_prov  = new inputField("% R.IVA", "reteiva_prov");
		$edit->reteiva_prov->size=2;
		$edit->reteiva_prov->readonly=true;
		$edit->reteiva_prov->when=array('modify','create');
		$edit->reteiva_prov->onchange ='cal_total();';

		$edit->observa = new textAreaField("Concepto", 'observa');
		$edit->observa->cols = 70;
		$edit->observa->rows = 3;
		
		$edit->tipoc = new dropdownField("Tipo de Pago","tipoc");
		$edit->tipoc->option("OT","Otro");
		$edit->tipoc->option("FA","Fondo en anticipo");

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
		$edit->imptimbre->onchange ='cal_total();';

		$edit->fondo = new dropdownField("F. Financiamiento","fondo");
		$edit->fondo->rule   ='required';
		$edit->fondo->db_name='fondo';
		$edit->fondo->options("SELECT tipo,tipo a  FROM presupuesto WHERE tipo<>'' GROUP BY tipo ORDER BY tipo desc");
		$edit->fondo->style="width:100px;";

		$edit->total= new inputField("Monto a Pagar", 'total');
		$edit->total->size = 8;
		$edit->total->css_class='inputnum';

		$edit->impmunicipal= new inputField("Impuesto Municipal", 'impmunicipal');
		$edit->impmunicipal->size = 8;
		$edit->impmunicipal->css_class='inputnum';
		$edit->impmunicipal->onchange ='cal_total();';

		$edit->subtotal = new inputField("Total Base Imponible", 'subtotal');
		$edit->subtotal->css_class='inputnum';
		$edit->subtotal->size = 8;
		//$edit->subtotal->readonly=true;

		$edit->iva = new inputField("IVA", 'iva');
		$edit->iva->css_class='inputnum';
		$edit->iva->size = 8;
		$edit->iva->readonly=true;

		$edit->ivaa = new inputField("IVA Adicional", 'ivaa');
		$edit->ivaa->css_class='inputnum';
		$edit->ivaa->size = 8;
		$edit->ivaa->onchange ='cal_total();';

		$edit->ivag = new inputField("IVA General", 'ivag');
		$edit->ivag->css_class='inputnum';
		$edit->ivag->size = 8;
		$edit->ivag->onchange ='cal_total();';

		$edit->ivar = new inputField("IVA Reducido", 'ivar');
		$edit->ivar->css_class='inputnum';
		$edit->ivar->size = 8;
		$edit->ivar->onchange ='cal_total();';

		$edit->exento = new inputField("Exento", 'exento');
		$edit->exento->css_class='inputnum';
		$edit->exento->size = 8;
		$edit->exento->onchange ='cal_total();';

		$edit->reteiva = new inputField("Retencion IVA", 'reteiva');
		$edit->reteiva->css_class='inputnum';
		$edit->reteiva->size = 8;
		$edit->reteiva->onchange ='cal_total();';

		$edit->creten = new dropdownField("Codigo ISLR","creten");
		$edit->creten->options("SELECT codigo,CONCAT_WS(' ',codigo,activida) FROM rete ORDER BY codigo");
		$edit->creten->style="width:300px;";
		$edit->creten->onchange ='cal_total();';

		$edit->reten = new inputField("Retenci&oacute;n ISLR", 'reten');
		$edit->reten->css_class='inputnum';
		$edit->reten->size = 8;
		$edit->reten->onchange ='cal_total();';

		$edit->otrasrete = new inputField("Otras Deducciones", 'otrasrete');
		$edit->otrasrete->css_class='inputnum';
		$edit->otrasrete->size = 8;
		$edit->otrasrete->insertValue=0;
		$edit->otrasrete->onchange ='cal_total();';

		$edit->total2 = new inputField("Total", 'total2');
		$edit->total2->css_class='inputnum';
		$edit->total2->size = 8;

		$edit->multiple = new dropDownField("Factura Multiple", 'multiple');
		$edit->multiple->option('N','NO');

		$edit->itocompra = new inputField("(<#o#>) Descripci&oacute;n", "ocompra_<#i#>");
		$edit->itocompra->db_name  ='ocompra';
		$edit->itocompra->size     =15;
		$edit->itocompra->rel_id   ='itodirect';
		//$edit->itocompra->readonly =true;
		$edit->itocompra->type     ='inputhidden';

		$edit->itcodigoadm = new inputField("Estructura	Administrativa","itcodigoadm_<#i#>");
		$edit->itcodigoadm->type     ='inputhidden';
		$edit->itcodigoadm->db_name='codigoadm';
		$edit->itcodigoadm->rel_id ='itodirect';
		$edit->itcodigoadm->rule   ='required';
		$edit->itcodigoadm->autocomplete=false;

		$edit->itpartida = new inputField("(<#o#>) Partida", "partida_<#i#>");
		//$edit->itpartida->rule='|required';
		$edit->itpartida->type     ='inputhidden';
		$edit->itpartida->db_name='partida';
		$edit->itpartida->rel_id ='itodirect';
		$edit->itpartida->autocomplete=false;
		//$edit->itpartida->readonly =true;

		$edit->itdenominacion = new inputField("(<#o#>) Descripci&oacute;n", "denominacion_<#i#>");
		$edit->itdenominacion->db_name  ='pdenominacion';
		$edit->itdenominacion->type     ='inputhidden';
		$edit->itdenominacion->rel_id   ='itodirect';
		$edit->itdenominacion->pointer  =true;
		$edit->itdenominacion->readonly =true;

		$edit->itprecio = new inputField("(<#o#>) Precio", "precio_<#i#>");
		$edit->itprecio->css_class='inputnum';
		$edit->itprecio->db_name  ='precio';
		$edit->itprecio->rel_id   ='itodirect';
		$edit->itprecio->rule     ='callback_positivo';
		$edit->itprecio->onchange ='cal_importe(<#i#>);';
		$edit->itprecio->size     =8;

		$edit->status = new dropdownField("Estado","status");
		$edit->status->option("C" ,"Por Elaborar");
		$edit->status->option("C2","Causado");
		$edit->status->option("C1","Por Causar");
		$edit->status->option("C3","Pagado");
		$edit->status->option("CA","Anulada");
		$edit->status->style="width:150px";
		$edit->status->mode ='autohide';

		$status=$edit->get_from_dataobjetct('status');
		if($status=='C1'){
			$action = "javascript:window.location='" .site_url($this->url.'actualizar/'.$edit->rapyd->uri->get_edited_id()). "'";
			$edit->button_status("btn_status",'Causar',$action,"TR","show");
			$action = "javascript:btn_anulaf('".$edit->rapyd->uri->get_edited_id()."')";
			if($this->datasis->puede(156))$edit->button_status("btn_anular",'Anular',$action,"TR","show");
			$edit->buttons("modify","save","delete");
		}elseif($status=='C2'){
			$action = "javascript:window.location='" .site_url('presupuesto/opago/modconc/odirect/modify/'.$edit->rapyd->uri->get_edited_id()). "'";
			$edit->button_status("btn_moconc",'Modificar Concepto',$action,"TR","show");
			//$action = "javascript:window.location='" .site_url($this->url.'reversar/'.$edit->rapyd->uri->get_edited_id()). "'";
			//$edit->button_status("btn_rever",'Reversar',$action,"TR","show");
			$action = "javascript:btn_anulaf('".$edit->rapyd->uri->get_edited_id()."')";
			if($this->datasis->puede(156))$edit->button_status("btn_anular",'Anular',$action,"TR","show");
		}elseif($status=='C3'){
			$multiple=$edit->get_from_dataobjetct('multiple');
			if($multiple=="N"){
				$action = "javascript:window.location='" .site_url($this->url.'camfac/dataedit/modify/'.$edit->rapyd->uri->get_edited_id()). "'";
				$edit->button_status("btn_camfac",'Modificar Factura',$action,"TR","show");
			}
		}elseif($status=="C"){
			$edit->buttons("modify","save","delete");
		}elseif($status=="CA"){
			$edit->buttons("delete");
		}else{
			$edit->buttons("save");
		}

		$edit->buttons("undo","back","add_rel");
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

		$smenu['link']   =barra_menu('129');
		$data['smenu']   = $this->load->view('view_sub_menu', $smenu,true);
		$conten["form"]  =&  $edit;
		$data['content'] = $this->load->view('view_opagoc', $conten,true);
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

		//if($multiple == 'N'){
		//	if($reteiva > 0 && (empty($factura) || empty($controlfac) || empty($fechafac)))
		//		$error.="<div class='alert'><p> Los campos Nro. Factura, Nro Control y Fecha factura no pueden estar en blanco</p></div>";
		//}else{
		//	$facs = $this->datasis->dameval("SELECT COUNT(*) FROM itfac WHERE numero=$id ");
		//	if($facs <= 0)
		//		$error.="<div class='alert'><p> Debe ingresar las factura por el modulo de factura multiple primero</p></div>";
		//}

		if(empty($error)){
			$sta=$do->get('status');
			if($sta=="C1"){
				$importes=array(); $ivas=array();$admfondo=array();$ordenes=array();
				for($i=0;$i  < $do->count_rel('itodirect');$i++){
					$codigoadm   = $do->get_rel('itodirect','codigoadm',$i);
					$fondo       = $do->get_rel('itodirect','fondo'    ,$i);
					$codigopres  = $do->get_rel('itodirect','partida'  ,$i);
					$iva         = $do->get_rel('itodirect','iva'      ,$i);
					$importe     = $do->get_rel('itodirect','importe'  ,$i);
					$ordinal     = $do->get_rel('itodirect','ordinal'  ,$i);
					$ocompra     = $do->get_rel('itodirect','ocompra'  ,$i);
					$ivan        = $importe*$iva/100;

					$error.=$this->itpartida($codigoadm,$fondo,$codigopres,$ordinal);

					$cadena = $codigoadm.'_._'.$fondo.'_._'.$codigopres;
					if(array_key_exists($cadena,$importes)){
						$importes[$cadena]+=$importe;
					}else{
						$importes[$cadena]  =$importe;
					}
					
					$cadena2 = $codigoadm.'_._'.$fondo;
					$admfondo[$cadena2]=(array_key_exists($cadena2,$admfondo)?$admfondo[$cadena2]+=$ivan:$admfondo[$cadena2] =$ivan);
					
					$cadena3 = $ocompra.'_._'.$codigoadm.'_._'.$fondo.'_._'.$codigopres;
					if(array_key_exists($cadena3,$ordenes)){
						$ordenes[$cadena3]+=$importe;
					}else{
						$ordenes[$cadena3]  =$importe;
					}
				}

				if(empty($error)){
					//foreach($admfondo AS $cadena=>$monto){
					//	$temp  = explode('_._',$cadena);
					//	$error.=$this->chequeapresup($temp[0],$temp[1],'PARTIDAIVA','',$monto,0,'round($monto,2) > $disponible=round(($presupuesto-$comprometido),2)','El Monto ($monto) es mayor al disponible (round(($disponible),2)) para la partida de IVA, ('.$temp[0].')('.$temp[1].') ');
					//}

					foreach($importes AS $cadena=>$monto){
						$temp  = explode('_._',$cadena);
						//$iva   = $ivas[$cadena];

						$error.=$this->chequeapresup($temp[0],$temp[1],$temp[2],'',$monto,0,'round($monto,2) > $disponible=round(($comprometido-$causado),2)','El Monto ($monto) es mayor al disponible ($disponible) para la partida ('.$temp[0].') ('.$temp[1].') ('.$temp[2].') ');
					}
				}
				
				if(empty($error)){
					foreach($ordenes as $k=>$v){
						$temp  = explode('_._',$v);
						
						$query ="SELECT SUM(a.importe) 
						FROM itodirect a
						JOIN odirect b ON a.numero=b.numero
						WHERE a.ocompra='".$temp[0]."' AND a.codigoadm='".$temp[1]."' AND a.fondo='".$temp[2]."' AND a.partida='".$temp[3]."' 
						AND b.status IN ('C2','C3')
						";
						$totcau=$this->datasis->dameval($query);
						
						
						
					}
				}

				if(empty($error)){
					foreach($importes AS $cadena=>$monto){
						$temp  = explode('_._',$cadena);
						//$iva   = $ivas[$cadena];
						$error.=$this->afectapresup($temp[0],$temp[1],$temp[2],'',$monto,0, 1 ,array("causado","opago"));
					}

					if(empty($error)){
						$do->set('fopago',date('Ymd'));
						$do->set('status','C2');
						$do->save();
					}
				}
			}
		}


		if(empty($error)){
			logusu('odirect',"Actualizo Orden de Pago Directo Nro $id");
			redirect($this->url."dataedit/show/$id");
		}else{
			logusu('odirect',"Actualizo Orden de Pago Directo Nro $id. con ERROR:$error ");
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
		$obligapiva   = $this->datasis->traevalor('obligapiva');
		$partidaiva   = $this->datasis->traevalor("PARTIDAIVA");
		$numero       = $do->get('numero'      );
		$redondear    = $do->get('redondear'   );
		$ivag         = $do->get('ivag'   );
		$ivar         = $do->get('ivar'   );
		$ivaa         = $do->get('ivaa'   );
		$reten        = $do->get('reten'  );
		$impm         =$do->get('impmunicipal');
		$impt         =$do->get('imptimbre'   );
		
		$ODIRECTMODIFICARETE=$this->datasis->traevalor('ODIRECTMODIFICARETE','N');

		$rete['tari1'] = 0;
		$rete=$this->datasis->damerow("SELECT base1,tari1,pama1 FROM rete WHERE codigo='$creten'");
		if($reteiva_prov!=75)$reteiva_prov=100;

		$total2=$giva=$aiva=$riva=$exento=$reteiva=$subtotal=$subtotal2=$tiva=$mivag=$mivar=$mivaa=$tivag=$tivar=$tivaa=$treten=0;
		$admfondo=array();$admfondop=array();$borrarivas=array();$ivasm=0;$totiva=0;
		
		for($i=0;$i < $do->count_rel('itodirect');$i++){
			$do->set_rel('itodirect','fondo' ,$fondo       ,$i);
			$cantidad        = round($do->get_rel('itodirect'  ,'cantidad'     ,$i),2);
			$total2+=$importe=$precio = round($do->get_rel('itodirect'  ,'precio'       ,$i),2);
			$piva            = round($do->get_rel('itodirect'  ,'iva'          ,$i),2);

			$codigoadm       =       $do->get_rel('itodirect'  ,'codigoadm'    ,$i);
			$fondo           =       $do->get_rel('itodirect'  ,'fondo'        ,$i);
			$partida         =       $do->get_rel('itodirect'  ,'partida'      ,$i);
			$codprov         =       $do->get_rel('itodirect'  ,'codprov'      ,$i);
			$ordinal         =       $do->get_rel('itodirect'  ,'ordinal'      ,$i);
			$esiva           =       $do->get_rel('itodirect'  ,'esiva'        ,$i);

			$do->set_rel('itodirect'  ,'importe',$importe          ,$i);

			$error.=$this->itpartida($codigoadm,$fondo,$partida);

		}

		$subtotal = $total2 -$giva - $riva - $aiva;

		//$total2=$giva+$riva+$aiva+$subtotal;
		$total =$total2-$reteiva-$treten-$retenomina-$otrasrete;

		if($reteiva>0 && $multiple=="N" && (empty($fechafac) || empty($factura) || empty($controlfac))){
			$error.="<div class='alert'><p>Los Campos Factura, Control Fiscal y fecha factura no pueden estar en blanco</p></div>";
		}

		if(($reteiva+$treten >0) AND $multiple=='N'){
			$this->chexiste_factura($numero,$factura,$controlfac,$cod_prov,'B',$e);
			$error.=$e;
		}
		
		$mivag = round(100 * $ivag / $rr['tasa'       ],2);
		$mivar = round(100 * $ivar / $rr['redutasa']  ,2);
		$mivaa = round(100 * $ivaa / $rr['sobretasa'] ,2);
		
		$exento= $subtotal - $mivag -$mivar -$mivaa ;

		//$do->set('impmunicipal'  , $impm                );
		//$do->set('imptimbre'     , $impt                );
		//$do->set('reteiva'       , $reteiva             );
		//$do->set('iva'           , $tiva                );
		//$do->set('ivag'          , $giva                );
		//$do->set('ivar'          , $riva                );
		//$do->set('ivaa'          , $aiva                );
		$do->set('tivag'         , $rr['tasa']          );
		$do->set('tivar'         , $rr['redutasa']      );
		$do->set('tivaa'         , $rr['sobretasa']     );
		//$do->set('mivag'         , $mivag               );
		//$do->set('mivar'         , $mivar               );
		//$do->set('mivaa'         , $mivaa               );
		//$do->set('subtotal'      , $subtotal            );
		//$do->set('exento'        , $exento              );

		//$do->set('total'         , $total               );
		//$do->set('total2'        , $total2              );
		$do->set('status'        , 'C1'                 );
		$do->set('tipoc'         , 'OT'                 );
		if($reten>0)
		$do->set('breten'        , $rete['tari1']       );

		///if($ODIRECTMODIFICARETE=='S'){
		///	$impmunicipal=$do->get('impmunicipal');
		///	$imptimbre   =$do->get('imptimbre'   );
		///	$reteiva     =$do->get('reteiva'     );
		///	$reten       =$do->get('reten'       );
        ///
		///	$total  = $total2-$otrasrete-$impmunicipal-$imptimbre-$reteiva-$reten;
		///}

		//$error.="<span class='alert'>esto seria lo primero con espan</span></span class='alert'>esto debria estar en la misma lineas</span>";

		if(empty($error) && (empty($do->loaded) || substr($numero,0,1)=='_') ){
			if(empty($numero) || substr($numero,0,1)=='_'){
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

	function selectoc(){
		//$this->datasis->modulo_id(71,1);
		$this->rapyd->load("datafilter","datagrid");
		$this->load->helper('form');
		//$this->rapyd->uri->keep_persistence();

		$mSPRV=array(
				'tabla'   =>'sprv',
				'columnas'=>array(
				'proveed' =>'C&oacute;odigo',
				'nombre'=>'Nombre',
				'contacto'=>'Contacto'),
				'filtro'  =>array('proveed'=>'C&oacute;digo','nombre'=>'Nombre'),
				'retornar'=>array('proveed'=>'cod_prov'),
				'titulo'  =>'Buscar Beneficiario');

		$bSPRV=$this->datasis->p_modbus($mSPRV,"proveed");
		
		

		$filter = new DataFilter("");

		$filter->db->select("a.certificado,a.compromiso compromiso,a.reverso reverso,a.numero numero,a.fecha fecha,a.tipo tipo,a.status status,a.cod_prov cod_prov,a.beneficiario beneficiario,c.nombre proveed,total2,SUM(d.pagos) pagos,SUM(d.xcausar) xcausar");
		$filter->db->from("ocompra a");
		$filter->db->join("sprv c"       ,"c.proveed=a.cod_prov","LEFT");
		$filter->db->join("v_comproxcausar_encab d"  ,"a.numero=d.ocompra");
		$filter->db->where("a.status =", "C");
		$filter->db->groupby("a.numero");
		//$filter->db->where("a.tipo =", "Trabajo");

		$filter->numero = new inputField("Numero", 'numero');
		$filter->numero->size = 6;

		$filter->compromiso = new inputField("Compromiso", 'compromiso');
		$filter->compromiso->size = 6;

		$filter->tipo = new dropdownField("Orden de ", "tipo");
		$filter->tipo->db_name = 'a.tipo';
		$filter->tipo->option("","");
		$filter->tipo->option("Compra"  ,"Compra");
		$filter->tipo->option("Servicio","Servicio");
		$filter->tipo->style="width:100px;";

		$filter->fecha = new dateonlyField("Fecha", "fecha");
		$filter->fecha->size=12;
		$filter->fecha->clause='where';
		$filter->fecha->operator='=';

		$filter->cod_prov = new inputField("Beneficiario", 'cod_prov');
		$filter->cod_prov->db_name="a.cod_prov";
		$filter->cod_prov->size = 6;
		$filter->cod_prov->append($bSPRV);
		$filter->cod_prov->rule = "required";

		$filter->buttons("reset","search");
		$filter->build();

		function sel($numero){
			return form_checkbox('data[]', $numero);
		}

		$iralfiltropagoc = anchor($this->url.'filteredgrid','Ir al Filtro');

		$grid = new DataGrid($iralfiltropagoc);
		$grid->order_by("status","asc");

		$grid->per_page = 100;
		$grid->use_function('substr','str_pad','sel','nformat');

		$grid->column(""              ,"<sel><#numero#></sel>");
		$grid->column_orderby("N&uacute;mero"   ,"numero","numero");
		if($this->datasis->traevalor("USACERTIFICADO")=='S')
		$grid->column_orderby("Certificado"     ,"certificado","certificado");
		if($this->datasis->traevalor("USACOMPROMISO")=='S')
		$grid->column_orderby("Compromiso"      ,"compromiso","compromiso");
		$grid->column_orderby("Fecha"           ,"<dbdate_to_human><#fecha#></dbdate_to_human>","fecha"  ,"align='center'");
		$grid->column_orderby("Tipo"            ,"tipo"                                        ,"tipo"   ,"align='center'");
		$grid->column_orderby("Beneficiario"    ,"proveed"                                     ,"proveed","align='left'  ");
		$grid->column_orderby("Total"           ,"<nformat><#total2#></nformat>"               ,"total2" ,"align='right' ");
		$grid->column_orderby("Causado"         ,"<nformat><#pagos#></nformat>"                ,"pagos" ,"align='right' ");
		$grid->column_orderby("Por Causar"      ,"<nformat><#xcausar#></nformat>"              ,"xcausar" ,"align='right' ");

		$grid->build();

		$salida =form_open($this->url.'guarda');
		$salida.=$grid->output;
		$salida.=form_submit('Crear Orden de Pago', 'Crear Orden de Pago');
		$salida.=form_close();

		$data['filtro']  = $filter->output;
		$data['content'] = $salida;
		$data['script']  = script("jquery.js")."\n";
		//$data['title']   = " Causar Ordenes ";//" $this->titp ";
		//$data['content'] = $filter->output.$grid->output;
		$data['title']   = "Seleccione las Ordenes de Trabajo ";
		$data["head"]    = $this->rapyd->get_head();
		$this->load->view('view_ventanas', $data);
	}

	function guarda(){
		$data    =$this->input->post('data');
		$ntransac=$this->datasis->fprox_numero('ntransac');
		$numero  ='_'.$ntransac;

		$query="
		INSERT INTO itodirect (numero,ocompra,codigoadm,partida,fondo,importe,cantidad,precio,unidad)
		SELECT '$numero',a.ocompra,a.codigoadm,a.codigopres,a.fondo,SUM(a.xcausar) importe,1,SUM(a.xcausar),'MONTO'
		FROM v_comproxcausar a
		WHERE ocompra IN ('".implode("','",$data)."')
		GROUP BY a.ocompra,a.codigoadm,a.codigopres,a.fondo";
		$this->db->query($query);

		//$ordenes=$this->datasis->damerow("SELECT SUM(a.xcausar) importe,a.fondo,b.cod_prov,b.observa,b.reteiva_prov
		//FROM v_comproxcausar a
		//JOIN ocompra b ON a.ocompra=b.numero
		//WHERE a.ocompra IN ('".implode("','",$data)."')
		//GROUP BY a.ocompra
		//");
		$ordenes=$this->datasis->damerow("
		SELECT SUM(a.xcausar) importe,a.fondo,b.cod_prov,b.observa,b.reteiva_prov,b.creten
		,@porcent:=ROUND(SUM(a.pagos)*100/SUM(a.compras),2) porcent
		,ROUND(subtotal    * IF(SUM(a.pagos)>0,ROUND(SUM(a.pagos)*100/SUM(a.compras),2)/100,1),2) subtotal
		,ROUND(exento      * IF(SUM(a.pagos)>0,ROUND(SUM(a.pagos)*100/SUM(a.compras),2)/100,1),2) exento
		,ROUND(ivag        * IF(SUM(a.pagos)>0,ROUND(SUM(a.pagos)*100/SUM(a.compras),2)/100,1),2) ivag
		,ROUND(mivag       * IF(SUM(a.pagos)>0,ROUND(SUM(a.pagos)*100/SUM(a.compras),2)/100,1),2) mivag
		,ROUND(ivar        * IF(SUM(a.pagos)>0,ROUND(SUM(a.pagos)*100/SUM(a.compras),2)/100,1),2) ivar
		,ROUND(mivar       * IF(SUM(a.pagos)>0,ROUND(SUM(a.pagos)*100/SUM(a.compras),2)/100,1),2) mivar
		,ROUND(ivaa        * IF(SUM(a.pagos)>0,ROUND(SUM(a.pagos)*100/SUM(a.compras),2)/100,1),2) ivaa
		,ROUND(mivaa       * IF(SUM(a.pagos)>0,ROUND(SUM(a.pagos)*100/SUM(a.compras),2)/100,1),2) mivaa
		,ROUND(breten      * IF(SUM(a.pagos)>0,ROUND(SUM(a.pagos)*100/SUM(a.compras),2)/100,1),2) breten
		,ROUND(reteiva     * IF(SUM(a.pagos)>0,ROUND(SUM(a.pagos)*100/SUM(a.compras),2)/100,1),2) reteiva
		,ROUND(reten       * IF(SUM(a.pagos)>0,ROUND(SUM(a.pagos)*100/SUM(a.compras),2)/100,1),2) reten
		,ROUND(total       * IF(SUM(a.pagos)>0,ROUND(SUM(a.pagos)*100/SUM(a.compras),2)/100,1),2) total
		,ROUND(total2      * IF(SUM(a.pagos)>0,ROUND(SUM(a.pagos)*100/SUM(a.compras),2)/100,1),2) total2
		,ROUND(impmunicipal* IF(SUM(a.pagos)>0,ROUND(SUM(a.pagos)*100/SUM(a.compras),2)/100,1),2) impmunicipal
		,ROUND(imptimbre   * IF(SUM(a.pagos)>0,ROUND(SUM(a.pagos)*100/SUM(a.compras),2)/100,1),2) imptimbre
		,ROUND(otrasrete   * IF(SUM(a.pagos)>0,ROUND(SUM(a.pagos)*100/SUM(a.compras),2)/100,1),2) otrasrete


		FROM v_comproxcausar a
		JOIN ocompra b ON a.ocompra=b.numero
		WHERE a.ocompra IN ('".implode("','",$data)."')
		");

		$data = array(
			'numero'           => $numero                    ,
			'fecha'            => date('Ymd')                ,
			'tipo'             => 'C'                        ,
			'cod_prov'         => $ordenes['cod_prov'     ]  ,
			'status'           => 'C'                        ,
			'fondo'            => $ordenes['fondo'        ]  ,
			'creten'           => $ordenes['creten'       ]  ,
			'observa'          => $ordenes['observa'      ]  ,
			'subtotal'         => $ordenes['subtotal'     ]  ,
			'exento'           => $ordenes['exento'       ]  ,
			'ivag'             => $ordenes['ivag'         ]  ,
			'mivag'            => $ordenes['mivag'        ]  ,
			'ivar'             => $ordenes['ivar'         ]  ,
			'mivar'            => $ordenes['mivar'        ]  ,
			'ivaa'             => $ordenes['ivaa'         ]  ,
			'mivaa'            => $ordenes['mivaa'        ]  ,
			'breten'           => $ordenes['breten'       ]  ,
			'reteiva'          => $ordenes['reteiva'      ]  ,
			'reten'            => $ordenes['reten'        ]  ,
			'total'            => $ordenes['total'        ]  ,
			'total2'           => $ordenes['total2'       ]  ,
			'impmunicipal'     => $ordenes['impmunicipal' ]  ,
			'imptimbre'        => $ordenes['imptimbre'    ]  ,
			'otrasrete'        => $ordenes['otrasrete'    ]  
		);
		
		$this->db->insert('odirect', $data); 
		
		//$query   ="INSERT INTO odirect (numero,fecha,tipo,cod_prov,status,subtotal,total,total2,observa,reteiva_prov,fondo)
		//values ('$numero',now(),'C',".$this->db->escape($ordenes['cod_prov']).",'C',".$this->db->escape($ordenes['importe']).",".$this->db->escape($ordenes['importe']).",".$this->db->escape($ordenes['importe']).",".$this->db->escape($ordenes['observa']).",".$this->db->escape($ordenes['reteiva_prov']).",".$this->db->escape($ordenes['fondo']).")";
		//$this->db->query($query);

		redirect($this->url.'dataedit/modify/'.$numero);
	}
	
	

	function _post_insert($do){
		$numero = $do->get('numero');
		logusu('opagoc',"Creo Orden de Pago contrato Nro $numero");
		//redirect($this->url."actualizar/$numero");
	}
	function _post_update($do){
		$numero = $do->get('numero');
		logusu('opagoc'," Modifico Orden de Pago contrato Nro $numero");
		//redirect($this->url."actualizar/$numero");
	}
	function _post_delete($do){
		$status= $do->get('status');
		if(strstr($status,1,1)==2)
		$this->db->query("call sp_recalculo()");

		$numero = $do->get('numero');
		logusu('opagoc'," Elimino Orden de Pago contrato Nro $numero");
	}

	function _pre_delete($do){
		$status= $do->get('status');
		if(strstr($status,1,1)==3){
			$do->error_message_ar['pre_ins']="No puede eliminar una orden que esta pagada";
			$do->error_message_ar['pre_upd']="No puede eliminar una orden que esta pagada";
			return false;
		}



	}

	function instalar(){
		$this->db->simple_query("ALTER TABLE `itodirect` CHANGE COLUMN `partida` `partida` VARCHAR(25) NULL DEFAULT NULL COMMENT 'Partida Presupuestaria'");
		$this->db->simple_query("ALTER TABLE `odirect` CHANGE COLUMN `numero` `numero` VARCHAR(12) NOT NULL COMMENT 'Nro de La Orden Pago'  ");
		$this->db->simple_query("ALTER TABLE `itodirect` CHANGE COLUMN `numero` `numero` VARCHAR(12) NULL DEFAULT NULL COMMENT 'Numero de la Orden'  ");
		$this->db->simple_query("ALTER TABLE `nomi`  CHANGE COLUMN `opago` `opago` VARCHAR(12) NULL DEFAULT NULL AFTER `fcomprome`");
		$this->db->simple_query("ALTER TABLE `odirect`  CHANGE COLUMN `nomina` `nomina` VARCHAR(12) NULL DEFAULT NULL");
		$this->db->simple_query("ALTER TABLE `odirect`  ADD COLUMN `cod_prov2` VARCHAR(5) NULL DEFAULT NULL AFTER `mcrs`");
		$this->db->simple_query("ALTER TABLE `odirect`  ADD COLUMN `redondear` CHAR(2) NULL");
		$this->db->simple_query("ALTER TABLE `odirect`  ADD COLUMN `otrasrete` DECIMAL(19,2) NULL DEFAULT '0'			");
		$this->db->simple_query("ALTER TABLE `itodirect` ADD COLUMN `ocompra` VARCHAR(20) NULL DEFAULT NULL");
	}
}
?>
