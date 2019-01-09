<?php
require_once(BASEPATH.'application/controllers/presupuesto/common.php');
class causacion extends Common {

	var $url="presupuesto/causacion/";
	function causacion(){
		parent::Controller();
		$this->load->library("rapyd");
		$this->datasis->modulo_id(71,1);
	}
	function index(){
		redirect("presupuesto/causacion/filteredgrid");
	}

	function filteredgrid(){
		$this->datasis->modulo_id(71,1);
		$this->rapyd->load("datafilter","datagrid");
		$this->rapyd->uri->keep_persistence();

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

		$filter->db->select("a.compromiso compromiso,a.reverso reverso,a.numero numero,a.fecha fecha,a.tipo tipo,a.status status,a.cod_prov cod_prov,a.beneficiario beneficiario,b.nombre uejecuta2,c.nombre proveed");
		$filter->db->from("ocompra a");
		$filter->db->join("uejecutora b" ,"a.uejecutora=b.codigo");
		$filter->db->join("sprv c"       ,"c.proveed=a.cod_prov");
		//$filter->db->where("a.status !=", "P");
		//$filter->db->where("a.status !=", "A");
		//$filter->db->where("a.status !=", "M");
		//$filter->db->where("a.status !=", "O");
		//$filter->db->where("a.status !=", "E");

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

		$filter->uejecutora = new dropdownField("U.Ejecutora", "uejecutora");
		$filter->uejecutora->option("","Seccionar");
		$filter->uejecutora->options("SELECT codigo,nombre FROM uejecutora ORDER BY nombre");
		$filter->uejecutora->onchange = "get_uadmin();";
		$filter->uejecutora->rule = "required";

		$filter->cod_prov = new inputField("Beneficiario", 'cod_prov');
		$filter->cod_prov->db_name="a.cod_prov";
		$filter->cod_prov->size = 6;
		$filter->cod_prov->append($bSPRV);
		$filter->cod_prov->rule = "required";
		$filter->cod_prov->clause   = 'where';
		$filter->cod_prov->operator = '=';

		$filter->reverso = new inputField("Reverso de", "reverso");
		$filter->reverso->size=20;

		$filter->status = new dropdownField("Estado","status");
		$filter->status->option("","");
		$filter->status->option("C","Comprometido");
		$filter->status->option("P","Sin Comprometer");
		$filter->status->option("T","Causado");
		$filter->status->option("O","Ordenado Pago");
		$filter->status->option("E","Pagado");
		$filter->status->option("A","Anulado");
		$filter->status->option("X","Reversado");

		$filter->status->style="width:150px";

		$filter->buttons("reset","search");
		$filter->build();
		$uri = anchor('presupuesto/causacion/dataedit/modify/<#numero#>','<str_pad><#numero#>|8|0|STR_PAD_LEFT</str_pad>');

		function sta($status){
			switch($status){
				case "P":return "Sin Comprometer";break;
				case "C":return "Comprometido";break;
				case "T":return "Causado";break;
				case "O":return "Ordenado Pago";break;
				case "E":return "Pagado";break;
				case "X":return "Reversado";break;
				case "M":return "Sin Terminar";break;
			}
		}

		$grid = new DataGrid("");

		$grid->order_by("status","asc");


		$grid->per_page = 20;
		$grid->use_function('substr','str_pad');
		$grid->use_function('sta');

		$grid->column_orderby("N&uacute;mero"   ,$uri,"numero");
		$grid->column_orderby("Compromiso"      ,"compromiso","compromiso");
		$grid->column_orderby("Fecha"           ,"<dbdate_to_human><#fecha#></dbdate_to_human>","fecha","align='center'");
		$grid->column_orderby("Tipo"            ,"tipo"  ,"tipo"                                        ,"align='center'");
		$grid->column_orderby("Unidad Ejecutora","uejecuta2"                                            ,"uejecuta2    ","align='left' NOWRAP");
		$grid->column_orderby("Beneficiario"    ,"proveed"                                     ,"proveed","align='left' NOWRAP");
		//$grid->column("Beneficiario"    ,"beneficiario");
		$grid->column_orderby("Estado"          ,"<sta><#status#></sta>"                       ,"status","align='center'");
		$grid->column_orderby("Reverso de"      ,"reverso"                                     ,"reverso","align='center'");



		$grid->build();
//		echo $grid->db->last_query();

		$data['filtro']  = $filter->output;
		$data['content'] = $grid->output;
		$data['script'] = script("jquery.js")."\n";
		//$data['title']   = " Causar Ordenes ";//" $this->titp ";
		//$data['content'] = $filter->output.$grid->output;
		$data['title']   = "Causaciones ";
		$data["head"]    = $this->rapyd->get_head();
		$this->load->view('view_ventanas', $data);
	}

	function dataedit(){
		$this->datasis->modulo_id(71,1);
		$this->rapyd->load('dataobject','datadetails');
		$this->rapyd->uri->keep_persistence();

		$mSPRV=array(
				'tabla'   =>'sprv',
				'columnas'=>array(
				'proveed' =>'C&oacute;odigo',
				'rif'     =>'RIF',
				'nombre'  =>'Nombre',
				'grupo'   =>'Grupo',
				'contacto'=>'Contacto'),
				'filtro'  =>array('proveed'=>'C&oacute;digo','nombre'=>'Nombre','rif'=>'RIF','grupo'   =>'Grupo'),
				'retornar'=>array('proveed'=>'cod_prov', 'nombre'=>'nombrep','reteiva'=>'reteiva_prov'),
//				'script'=>array('cal_lislr()','cal_total()'),
				'titulo'  =>'Buscar Beneficiario');

		$bSPRV=$this->datasis->modbus($mSPRV,"sprv");

 		$do = new DataObject("ocompra");
		$do->rel_one_to_many('itocompra', 'itocompra', array('numero'=>'numero'));
		$do->pointer('sprv' ,'sprv.proveed = ocompra.cod_prov','sprv.nombre as nombrep' ,'LEFT');

		$edit = new DataDetails("Orden ", $do);

		$edit->set_rel_title('itocompra','Rubro <#o#>');

		$edit->back_url = "presupuesto/causacion/filteredgrid";

		$status=$edit->get_from_dataobjetct('status');
		$ivaplica=$this->ivaplica2();

		$edit->pre_process('insert'  ,'_valida');
		$edit->pre_process('update'  ,'_valida');
		$edit->post_process('insert','_post_insert');
		$edit->post_process('update','_post_update');
		$edit->post_process('delete','_post_delete');

		$edit->numero  = new inputField("N&uacute;mero", "numero");
		$edit->numero->mode="autohide";
		$edit->numero->when=array('show');

		$edit->status  = new inputField("Estado", "status");
		$edit->status->mode="autohide";
		$edit->status->when=array('');

		$edit->factura  = new inputField("Factura", "factura");
		$edit->factura->size=15;
		//$edit->factura->rule="required";
		//if($status=='O')
		//$edit->factura->mode="autohide";

		if($this->datasis->traevalor("USACERTIFICADO")=='S'){
			$edit->certificado  = new inputField("Cert. Disp. Presupuestaria", "certificado");
			$edit->certificado->size=15;
			if($status=='O')
			$edit->certificado->mode="autohide";
		}
		
		if($this->datasis->traevalor("USACOMPROMISO")=='S'){
			$edit->compromiso  = new inputField("Nro Compromiso", "compromiso");
			$edit->compromiso->size=15;
			if($status=='O')
			$edit->compromiso->mode="autohide";
		}

		$edit->controlfac  = new inputField("Control Fiscal", "controlfac");
		$edit->controlfac->size=15;
		//$edit->controlfac->rule="required";
		//if($status=='O')
		//$edit->controlfac->mode="autohide";

		$edit->fechafac = new  dateonlyField("Fecha de Factura",  "fechafac");
		$edit->fechafac->insertValue = date('Y-m-d');
		$edit->fechafac->size =12;
		//$edit->fechafac->rule="required";
		//if($status=='O')
		//$edit->fechafac->mode="autohide";

		$tipo = $edit->get_from_dataobjetct('tipo');
		$edit->cod_prov = new inputField("Beneficiario", 'cod_prov');
		$edit->cod_prov->size  = 6;
		$edit->cod_prov->append($bSPRV);
		if($tipo!='Compromiso')
		$edit->cod_prov->mode  ="autohide";


		$edit->creten = new dropdownField("Cod ISLR: ","creten");
		//$edit->creten->mode   = "autohide";
		//$edit->creten->option("","");
		$edit->creten->options("SELECT codigo,CONCAT_WS(' ',codigo,activida) FROM rete ORDER BY codigo");
		$edit->creten->style="width:350px;";
		$edit->creten->onchange ='cal_islr();';

		$edit->reteiva_prov = new dropdownField("Retenci&oacute;n de IVA %","reteiva_prov");
		$edit->reteiva_prov->option("100","100%");
		$edit->reteiva_prov->option("75" ,"75%");
		$edit->reteiva_prov->style="width:70px;";
		$edit->reteiva_prov->onchange ='cal_total();';

		$edit->nombrep= new inputField("Nombre","nombrep");
		$edit->nombrep->size = 60;
		$edit->nombrep->readonly = true;
		$edit->nombrep->pointer = true;
		if($tipo!='Compromiso')
		$edit->nombrep->mode = "autohide";

		$edit->fecha = new dateonlyField("Fecha O. Compra", 'fecha');
		$edit->fecha->size  = 6;
		$edit->fecha->mode  ="autohide";

		$edit->subtotal = new inputField("Sub Total", 'subtotal');
		$edit->subtotal->size     = 12;
		$edit->subtotal->readonly =true;

		$edit->ivaa = new inputField("IVA ".$ivaplica['sobretasa']."%", 'ivaa');
		$edit->ivaa->size = 12;
		$edit->ivaa->css_class='inputnum';
		$edit->ivaa->rule     ='numeric';
		$edit->ivaa->onchange ='cal_total();';

		$edit->ivag = new inputField("IVA ".$ivaplica['tasa']."%", 'ivag');
		$edit->ivag->size = 12;
		$edit->ivag->css_class='inputnum';
		$edit->ivag->rule     ='numeric';
		$edit->ivag->onchange ='cal_total();';

		$edit->ivar = new inputField("IVA ".$ivaplica['redutasa']."%", 'ivar');
		$edit->ivar->size = 12;
		$edit->ivar->css_class='inputnum';
		$edit->ivar->rule     ='numeric';
		$edit->ivar->onchange ='cal_total();';

		$edit->mivaa = new inputField("Base ".$ivaplica['sobretasa']."%: ", 'mivaa');
		$edit->mivaa->size     = 12;
		$edit->mivaa->css_class='inputnum';
		$edit->mivaa->rule     ='numeric';
		$edit->mivaa->onchange ='cal_mivaa();';

		$edit->mivag = new inputField("Base ".$ivaplica['tasa']."%: ", 'mivag');
		$edit->mivag->size     = 12;
		$edit->mivag->css_class='inputnum';
		$edit->mivag->rule     ='numeric';
		$edit->mivag->onchange ='cal_mivag();';

		$edit->mivar = new inputField("Base ".$ivaplica['redutasa']."%: ", 'mivar');
		$edit->mivar->size     = 12;
		$edit->mivar->css_class='inputnum';
		$edit->mivar->rule     ='numeric';
		$edit->mivar->onchange ='cal_mivar();';

		$edit->mexento = new inputField("Exento a Retener: ", 'mexento');
		$edit->mexento->size = 12;
		$edit->mexento->css_class='inputnum';
		$edit->mexento->rule     ='numeric';
		$edit->mexento->onchange ='cal_total();';

		$edit->exento = new inputField("Exento", 'exento');
		$edit->exento->size = 12;
		$edit->exento->css_class='inputnum';
		$edit->exento->rule     ='numeric';
		$edit->exento->onchange ='cal_total();';
		//if($tipo!='Compromiso')
		//$edit->exento->readonly =true;

		$edit->reteiva = new inputField("Retencion de IVA", 'reteiva');
		$edit->reteiva->size = 12;
		//$edit->reteiva->readonly = true;
		//$edit->reteiva->mode ="autohide";
		$edit->reteiva->css_class='inputnum';
		$edit->reteiva->rule     ='numeric';
		$edit->reteiva->onchange ='cal_total();';

		$edit->reten = new inputField("Retencion de ISLR", 'reten');
		$edit->reten->size = 12;
		//$edit->reten->readonly = true;
		//$edit->reten->mode ="autohide";
		$edit->reten->css_class='inputnum';
		$edit->reten->rule     ='numeric';
		$edit->reten->onchange ='cal_total();';

		$edit->total = new inputField("Total a Pagar", 'total');
		$edit->total->size     = 12;
		$edit->total->readonly = true;

		$edit->total2 = new inputField("Total", 'total2');
		$edit->total2->size     = 12;
		$edit->total2->readonly =true;

		$edit->otrasrete = new inputField("Otras Retenciones", 'otrasrete');
		$edit->otrasrete->size     = 12;
		$edit->otrasrete->onchange ='cal_total();';

		$edit->simptimbre = new checkboxField("1X1000", "simptimbre", "S","N");
		$edit->simptimbre->insertValue = "N";
		$edit->simptimbre->onchange    = 'cal_timbre();';

		$edit->imptimbre= new inputField("Impuesto 1X1000", 'imptimbre');
		$edit->imptimbre->size = 12;
		$edit->imptimbre->css_class='inputnum';
		$edit->imptimbre->onchange ='cal_total();';
		//$edit->imptimbre->readonly = true;

		$edit->itesiva = new inputField("P.IVA","itesiva_<#i#>");
		$edit->itesiva->rule   ='required';
		$edit->itesiva->db_name='esiva';
		$edit->itesiva->rel_id ='itocompra';
		$edit->itesiva->readonly = true;
		$edit->itesiva->size     =3;
//detalle
		$edit->itfondo = new inputField("F. Financiamiento","itfondo_<#i#>");
		$edit->itfondo->size   =10;
		$edit->itfondo->rule   ='required';
		$edit->itfondo->db_name='fondo';
		$edit->itfondo->rel_id ='itocompra';
		$edit->itfondo->readonly = true;

		$edit->itcodigoadm = new inputField("Estructura	Administrativa","itcodigoadm_<#i#>");
		$edit->itcodigoadm->size   =10;
		$edit->itcodigoadm->db_name='codigoadm';
		$edit->itcodigoadm->rel_id ='itocompra';
		$edit->itcodigoadm->rule   ='required';
		$edit->itcodigoadm->readonly = true;

		$edit->itpartida = new inputField("(<#o#>) Partida", "partida_<#i#>");
		$edit->itpartida->size=12;
		$edit->itpartida->db_name='partida';
		$edit->itpartida->rel_id ='itocompra';
		$edit->itpartida->readonly = true;

		$edit->itordinal = new inputField("(<#o#>) Ordinal", "ordinal_<#i#>");
		$edit->itordinal->db_name  ='ordinal';
		$edit->itordinal->maxlength=3;
		$edit->itordinal->size     =2;
		$edit->itordinal->rel_id   ='itocompra';
		$edit->itordinal->readonly = true;

		$edit->itdescripcion = new inputField("(<#o#>) Descripci&oacute;n", "descripcion_<#i#>");
		$edit->itdescripcion->db_name  ='descripcion';
		$edit->itdescripcion->size     =20;
		$edit->itdescripcion->rel_id   ='itocompra';
		//$edit->itdescripcion->mode   ='autohide';
		$edit->itdescripcion->readonly = true;

		$edit->itunidad = new hiddenField("(<#o#>) Unidad", "unidad_<#i#>");
		$edit->itunidad->db_name  = 'unidad';
		$edit->itunidad->rel_id   = 'itocompra';
		//$edit->itunidad->mode     ='autohide';
		$edit->itunidad->size     =10;
		$edit->itunidad->readonly = true;

		$edit->itcantidad = new hiddenField("(<#o#>) Cantidad", "cantidad_<#i#>");
		$edit->itcantidad->db_name  ='cantidad';
		$edit->itcantidad->rel_id   ='itocompra';
		$edit->itcantidad->size     =4;
		//$edit->itcantidad->mode     ='autohide';
		$edit->itcantidad->readonly = true;

		$edit->itprecio = new hiddenField("(<#o#>) Precio", "precio_<#i#>");
		$edit->itprecio->db_name  ='precio';
		$edit->itprecio->rel_id   ='itocompra';
		$edit->itprecio->size     =8;
		//$edit->itprecio->mode     ='autohide';
		$edit->itprecio->readonly = true;

		$edit->itusaislr = new dropdownField("(<#o#>) Islr", "usaislr_<#i#>");
		$edit->itusaislr->db_name     = 'usaislr';
		$edit->itusaislr->rel_id      = 'itocompra';
		$edit->itusaislr->insertValue = "N";
		$edit->itusaislr->onchange ='cal_islr();';
		$edit->itusaislr->option("N","No");
		$edit->itusaislr->option("S","Si");
		$edit->itusaislr->style="width:45px";

		$edit->itislr = new inputField("(<#o#>) Islr", "islr_<#i#>");
		$edit->itislr->css_class='inputnum';
		$edit->itislr->db_name  ='islr';
		$edit->itislr->rel_id   ='itocompra';
		$edit->itislr->rule     ='numeric';
		$edit->itislr->readonly =true;
		$edit->itislr->size     =5;

		$edit->itiva = new inputField("(<#o#>) IVA", "iva_<#i#>");
		$edit->itiva->db_name  ='iva';
		$edit->itiva->rel_id   ='itocompra';
		$edit->itiva->size     =8;
		//$edit->itiva->mode     ='autohide';
		$edit->itiva->readonly = true;

		$edit->itimporte = new inputField("(<#o#>) Importe", "importe_<#i#>");
		$edit->itimporte->db_name  ='importe';
		$edit->itimporte->rel_id   ='itocompra';
		$edit->itimporte->size     =12;
		//$edit->itimporte->mode     ='autohide';
		$edit->itimporte->readonly = true;

		//$status=$edit->get_from_dataobject('status');
		$status=$edit->getval('status');

		if($status=='C'){
			if($edit->_status!='modify'){
				$action = "javascript:window.location='" .site_url('presupuesto/causacion/actualizar/'.$edit->rapyd->uri->get_edited_id()). "'";
				$edit->button_status("btn_status",'Causar',$action,"TR","show");
			}
			$edit->buttons("modify","save");
		}elseif($status=='T'){
			if($this->datasis->puede(361)){
				$action = "javascript:window.location='" .site_url('presupuesto/causacion/reversar/'.$edit->rapyd->uri->get_edited_id()). "'";
				$edit->button_status("btn_rever",'Anular Solo Causado',$action,"TR","show");
			}
			$action = "javascript:btn_anulaf('" .$edit->rapyd->uri->get_edited_id(). "')";
			if($this->datasis->puede(226))$edit->button_status("btn_anular",'Anular',$action,"TR","show");
		}elseif($status=='O'){
			$edit->buttons("modify");
			$action = "javascript:window.location='" .site_url($this->url.'camfac/dataedit/modify/'.$edit->rapyd->uri->get_edited_id()). "'";
			$edit->button_status("btn_camfac",'Modificar Factura',$action,"TR","show");
		}elseif($status=='E'){
			//$edit->buttons("save");
			$action = "javascript:window.location='" .site_url($this->url.'camfac/dataedit/modify/'.$edit->rapyd->uri->get_edited_id()). "'";
			$edit->button_status("btn_camfac",'Modificar Factura',$action,"TR","show");
		}


		$edit->buttons("save","undo","back");
		$edit->build();

		//$smenu['link']=barra_menu('103');
		//$data['smenu'] = $this->load->view('view_sub_menu', $smenu,true);
		$query = $this->db->query('SELECT codigo,base1,tari1,pama1,tipo FROM rete');

		$rt=array();
		foreach ($query->result_array() as $row){
			$pivot=array('base1'=>$row['base1'],
			             'tari1'=>$row['tari1'],
			             'pama1'=>$row['pama1'],
									 'tipo'=>$row['tipo']);
			$rt['_'.$row['codigo']]=$pivot;
		}
		$rete=json_encode($rt);

		if($status=='O' AND ($this->datasis->puede(257) || $this->datasis->essuper()))
			$titulo="Modificar Retenciones de ordenes por Pagar";
		else
			$titulo="Causar";


		$conten['rete']     =$rete;
		$conten['ivar']     = $ivaplica['redutasa'];
		$conten['ivag']     = $ivaplica['tasa'];
		$conten['ivaa']     = $ivaplica['sobretasa'];
		$conten['imptimbre']=$this->datasis->traevalor('IMPTIMBRE');
		$conten["form"]     =&  $edit;
		$data['content']    = $this->load->view('view_causacion', $conten,true);
		$data["head"]       = script('jquery.pack.js').script('plugins/jquery.numeric.pack.js').script('plugins/jquery.floatnumber.js').script('plugins/jquery.autocomplete.js').style('jquery.autocomplete.css').$this->rapyd->get_head();
		$data['title']      = $titulo;
		$this->load->view('view_ventanas', $data);
	}

	function ivaplica2($mfecha=NULL){
		if(empty($mfecha)) $mfecha=date('Ymd');
		//$CI =& get_instance();
		$qq = $this->db->query("SELECT tasa, redutasa, sobretasa FROM civa WHERE fecha < '$mfecha' ORDER BY fecha DESC LIMIT 1");
		$rr = $qq->row_array();
		//$aa = each($rr);
		return $rr;
	}

	function reversar($id){
		$error=$this->ca_reversar($id);
//exit($error.'__');
		if(empty($error)){
			logusu('causacion',"Anulo Causado Orden de Compra Nro $id");
			if($this->redirect)redirect("presupuesto/causacion/dataedit/show/$id");
		}else{
			logusu('causacion',"Anulo Causado de Compra Nro $id. con ERROR:$error ");
			$data['content'] = $error.anchor("/presupuesto/causacion/dataedit/show/$id",'Regresar');
			$data['title']   = " Anular Causado ";
			$data["head"]    = $this->rapyd->get_head();
			$this->load->view('view_ventanas', $data);
		}
	}

	function actualizar($id){
			$this->rapyd->load('dataobject');

			$do = new DataObject("ocompra");
			$do->rel_one_to_many('itocompra', 'itocompra', array('numero'=>'numero'));
			$do->load($id);


			$error='';
			$factura     = $do->get('factura'   );
			$numero      = $do->get('numero'    );
			$cod_prov    = $do->get('cod_prov'  );
			$controlfac  = $do->get('controlfac');
			$fechafac    = $do->get('fechafac'  );
			$imptimbre    = $do->get('imprtimbre'  );
			$reten        = $do->get('reten'       );
			$ivag         = $do->get('ivag'        );
			$ivar         = $do->get('ivar'        );
			$ivaa         = $do->get('ivaa'        );
			$iva          = $ivar+$ivaa+$ivag;
			$ide          =$this->db->escape($id);

			if($iva>0 || $reten>0 || $imptimbre>0){
				if(empty($factura) || empty($controlfac) ){
					$cant = $this->db->simple_query("SELECT COUNT(*) FROM itfac WHERE numero=$ide");
					if($cant==0)
					$error.="<div class='alert'><p>Los Campos Factura, Control Fiscal y Fecha de Factura, no pueden estar en blanco</p></div>";
				}else{
					$this->chexiste_factura($numero,$factura,$controlfac,$cod_prov,'F',$e);
		  		$error.=$e;
				}
			}

			if(empty($error)){

				$sta=$do->get('status');
				if($sta=='C'){

					$partidaiva=$this->datasis->traevalor("PARTIDAIVA");

					$ivan=0;$importes=array(); $ivas=array();$admfondo=array();
					for($i=0;$i < $do->count_rel('itocompra');$i++){
						$codigoadm   = $do->get_rel('itocompra','codigoadm',$i);
						$fondo       = $do->get_rel('itocompra','fondo'    ,$i);
						$codigopres  = $do->get_rel('itocompra','partida'  ,$i);
						$importe     = $do->get_rel('itocompra','importe'  ,$i);
						$iva         = $do->get_rel('itocompra','iva'      ,$i);
						$ordinal     = $do->get_rel('itocompra','ordinal'  ,$i);
						$ivan        = $importe*$iva/100;

						$error.=$this->itpartida($codigoadm,$fondo,$codigopres,$ordinal);

						$cadena = $codigoadm.'_._'.$fondo.'_._'.$codigopres;//.'_._'.$ordinal.'_._'.$iva;
						if(array_key_exists($cadena,$importes)){
							$importes[$cadena]+=$importe;
							//$ivas[$cadena]     =$iva;
						}else{
							$importes[$cadena]  =$importe;
							//$ivas[$cadena]      =$iva;
						}
						$cadena2 = $codigoadm.'_._'.$fondo;
						$admfondo[$cadena2]=(array_key_exists($cadena2,$admfondo)?$admfondo[$cadena2]+=$ivan:$admfondo[$cadena2] =$ivan);
					}

					if(empty($error)){
						//foreach($admfondo AS $cadena=>$monto){
						//	$temp  = explode('_._',$cadena);
						//	$error.=$this->chequeapresup($temp[0],$temp[1],'PARTIDAIVA','',$monto,0,'round($monto,2) > $disponible=round(($comprometido-$causado),2)','El Monto ($monto) es mayor al disponible (round(($disponible),2)) para causar para la partida de IVA, ('.$temp[0].')('.$temp[1].') ');
						//}

						foreach($importes AS $cadena=>$monto){
							$temp  = explode('_._',$cadena);
							$iva   = $ivas[$cadena];
							$error.=$this->chequeapresup($temp[0],$temp[1],$temp[2],'',$monto,0,'round($monto,2) > $disponible=round(($comprometido-$causado),2)','El Monto ($monto) es mayor al disponible ($disponible) para la partida ('.$temp[0].' ('.$temp[1].') ('.$temp[2].')');
						}
					}

					if(empty($error)){
						//$ivan=0;
						//for($i=0;$i < $do->count_rel('itocompra');$i++){
						//	$codigopres  = $do->get_rel('itocompra','partida',$i);
						//	$importe     = $do->get_rel('itocompra','importe',$i);
						//	$iva         = $do->get_rel('itocompra','iva'    ,$i);
						//	$ordinal     = $do->get_rel('itocompra','ordinal',$i);
						//	$ivan       += $importe*$iva/100;
						//
						//	$error.=$this->afectapresup($codigoadm,$fondo,$codigopres,$ordinal,$importe,$iva, 1 ,array("causado"));
						//}

						foreach($importes AS $cadena=>$monto){
							$temp  = explode('_._',$cadena);
							//$iva   = $ivas[$cadena];
							$error.=$this->afectapresup($temp[0],$temp[1],$temp[2],'',$monto,0, 1 ,array("causado"));
						}

						//if(empty($error)){
						//	foreach($admfondo AS $cadena=>$monto){
						//		$temp  = explode('_._',$cadena);
						//		$error.=$this->afectapresup($temp[0],$temp[1],'PARTIDAIVA','',$monto,0, 1 ,array("causado"));
						//	}
						//}

						if(empty($error)){
							$do->set('status','T');
							$do->set('fcausado',date('Ymd'));
							$do->save();
						}
					}
				}else{
					$error.="<div class='alert'><p>No se puede Causar esta orden de pago</p></div>";
				}
			}

		if(empty($error)){
			redirect("presupuesto/causacion/dataedit/show/$id");
			logusu('causacion',"Causo Orden de Compra Nro $id");
		}else{
			logusu('causacion',"Causo Orden de Compra Nro $id. con ERROR:$error ");
			$data['content'] = $error.anchor("presupuesto/causacion/dataedit/show/$id",'Regresar');
			$data['title']   = " Causar Orden de Compra ";
			$data["head"]    = $this->rapyd->get_head().script('jquery.js').script("plugins/jquery.numeric.pack.js");
			$this->load->view('view_ventanas', $data);
		}
	}

	function camfac($var1,$var2,$id){
		$this->rapyd->load('dataedit2');

		$edit = new DataEdit2("Cambiar datos de Factura","ocompra");

		$edit->back_url = $this->url."/dataedit/show/$id";

		$edit->pre_process('update'  ,'_validafac');
		$edit->post_process('update' ,'_postfac'   );

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

		$this->chexiste_factura($numero,$factura,$controlfac,$cod_prov,'F',$e);
		$error=$e;

		$riva = new DataObject("riva");
		$riva->load_where('ocompra',$numero);

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
			logusu('ocompra',"Cambio datos Factura $factura Nro $controlfac fecha $fechafac,orden de compra $numero con error $error");
			return false;
		}
	}

	function _postfac($do){
		$factura    = $do->get('factura'   );
	  $controlfac = $do->get('controlfac');
	  $fechafac   = $do->get('fechafac'  );
	  $numero     = $do->get('numero'    );

		logusu('ocompra',"Cambio datos Factura $factura Nro $controlfac fecha $fechafac, orden de compra $numero");
		redirect($this->url."dataedit/show/$numero");
	}

	function _valida($do){
		$error        = '';
		$rr           = $this->ivaplica2();
		$reteiva_prov = $do->get('reteiva_prov');

		$creten       = $do->get('creten'      );
		$tipo         = $do->get('tipo'        );
		$status       = $do->get('status'      );
		$cod_prov     = $do->get('cod_prov'    );
		$controlfac   = $do->get('controlfac'  );
		$factura      = $do->get('factura'     );
		$fechafac     = $do->get('fechafac'    );
		$numero       = $do->get('numero'      );
		$imptimbre    = $do->get('imptimbre'   );
		$reten        = $do->get('reten'       );
		$ivag         = $do->get('ivag'        );
		$ivar         = $do->get('ivar'        );
		$ivaa         = $do->get('ivaa'        );
		$reteiva      = $do->get('reteiva'     );
		$otrasrete    = $do->get('otrasrete'   );
		$iva          = round($ivar+$ivaa+$ivag,2);
		$basei        =0;

		if($iva>0 || $reten>0 || $imptimbre>0){

			/*if(empty($factura) || empty($controlfac) ){
				$error.="Los Campos Factura, Control Fiscal y Fecha de Factura, no pueden estar en blanco";
			}else{
				$this->chexiste_factura($numero,$factura,$controlfac,$cod_prov,'F',$e);
	  		$error.=$e;
			}
			*/
		}

		$cod_prov2    = $this->db->escape($cod_prov);
		$pr           = $this->datasis->damerow("SELECT proveed,nombre,reteiva FROM sprv WHERE proveed =$cod_prov2 ");
		if(count($pr)>0){
			if(round($pr['reteiva'],2) > round($reteiva_prov,2)){
				$error .= ("No se puede aplicar $reteiva_prov % de retenci&oacute; de IVA al proveedor ".$pr['nombre']." ya que es menor al porcentaje asignado para dicho proveedor.");
			}
		}else{
			$error .= ("El Proveedor ($cod_prov) no esta registrado. Por Favor Registrelo");
		}

		//if($tipo == 'Compra' ){
		//	$do->set('creten','');
		//	$do->set('reten' ,0);
		//}

		$giva=$aiva=$riva=$exento=$subtotal=$mivag=$mivar=$mivaa=$tivag=$tivar=$tivaa=$subt=$treten=0;
		$rete=$this->datasis->damerow("SELECT base1,tari1,pama1 FROM rete WHERE codigo='$creten'");

		for($i=0;$i < $do->count_rel('itocompra');$i++){
			$esiva      =       $do->get_rel('itocompra','esiva'      ,$i);
			$importe    = round($do->get_rel('itocompra','importe'    ,$i),2);
		
			if($esiva=='N'){
				if($rete && $do->get_rel('itocompra','usaislr',$i)=="S"){
					$basei+=$importe;
					//if(substr($creten,0,1)=='1')$reten=round($importe*$rete['base1']*$rete['tari1']/10000,2);
					//else $reten=round(($importe-$rete['pama1'])*$rete['base1']*$rete['tari1']/10000,2);
				    //
					//if($reten < 0)$reten=0;
					//$treten+=$reten;
					//$do->set_rel('itocompra','preten' , $rete['tari1']  ,$i );
					//$do->set_rel('itocompra','islr'   , $reten          ,$i );
				}else{
					//$do->set_rel('itocompra','islr'   , 0 ,$i );
				}
			}
		}

		//$reten = 0;
		//$reteiva=(($giva+$riva+$aiva)*$reteiva_prov)/100;
		//$total2=$giva+$riva+$aiva+$subtotal;
		//$total =$total2-$reteiva-$treten;

		//$impm=$impt=0;

		if($tipo=='T' || $tipo=='N'){
			$do->set('reten'         , 0    );
			$do->set('reteiva'       , 0    );
			$do->set('factura'       , ''   );
			$do->set('controlfac'    , ''   );
			$do->set('fechafac'      , ''   );
		}

		$do->set('tivag'         , $rr['tasa']          );
		$do->set('tivar'         , $rr['redutasa']      );
		$do->set('tivaa'         , $rr['sobretasa']     );
		if(empty($error) ){
			$ivag     = $do->get('ivag'    );
			$ivar     = $do->get('ivar'    );
			$ivaa     = $do->get('ivaa'    );
			$mivag    = $do->get('mivag'   );
			$mivar    = $do->get('mivar'   );
			$mivaa    = $do->get('mivaa'   );
			$exento   = $do->get('exento'  );
			$mexento  = $do->get('mexento' );
			$total2   = $do->get('total2'  );

			$tmivag    =$ivag/round($rr['tasa']     *(100-$rr['tasa'])     ,2)+round($ivag,2);
			$tmivar    =$ivar/round($rr['redutasa'] *(100-$rr['redutasa']) ,2)+round($ivar,2);
			$tmivaa    =$ivaa/round($rr['sobretasa']*(100-$rr['sobretasa']),2)+round($ivaa,2);
			if($basei<=0)
			$basei     =round($mivag+$mivar+$mivaa+$mexento,2);
			
			$aa        =round($mivag+$mivar+$mivaa,2);
			$bb        =round($ivag+$ivar+$ivaa,2);
			$tot       =$mivag+$mivar+$mivaa+$ivaa+$ivar+$ivag+$exento;
			$total2    =$tot;

			//if(round($mivag*$rr['tasa']/100,2)-round($ivag,2)>0.02 || round($ivag,2)-round($mivag*$rr['tasa']/100,2)>0.02)
			//$error.="El IVA General introducido ($ivag) no corresponde a la base ($mivag) </br>";
			//
			//if(round($mivar*$rr['redutasa']/100,2)-round($ivar,2)>0.02 || round($ivar,2)-round($mivar*$rr['redutasa']/100,2)>0.02)
			//$error.="El IVA Reducido introducido ($ivar) no corresponde a la base ($mivar) </br>";
			//
			//if(round($mivaa*$rr['sobretasa']/100,2)-round($ivaa,2)>0.02 || round($ivaa,2)-round($mivaa*$rr['sobretasa']/100,2)>0.02)
			//$error.="El IVA Adicional introducido ($ivaa) no corresponde a la base ($mivaa) </br>";

			$subtotal =$mivaa+$mivar+$mivag+$exento;
			if($this->datasis->traevalor('CAUSACIONVALIDARETE','S','valida que los montos de las retenciones al causar sean correctos')=='S'){
				if(substr($creten,0,1)=='1')$reten_cal=round($basei*$rete['base1']*$rete['tari1']/10000,2);
						else $reten_cal=round(($basei-$rete['pama1'])*$rete['base1']*$rete['tari1']/10000,2);

				if(round($reten,2)-round($reten_cal,2)>0.02 || round($reten_cal,2)-round($reten,2)>0.02)
					$error.="El ISLR introducido($reten) es distinta al calculado ($reten_cal)</br>";

				if(round($tot,2)-round($total2,2)>0.02 || round($total2,2)-round($tot,2)>0.02)
					$error.="La suma de las bases($aa) + ivas($bb) + exento($exento):($tot), es distinta al total del compromiso ($total2)</br>";

				$reteiva_cal  =round((($ivaa+$ivar+$ivag)*$reteiva_prov)/100,2);

				if(round($reteiva,2)-round($reteiva_cal,2)>0.02 || round($reteiva_cal,2)-round($reteiva,2)>0.02)
				$error.="El IVA retenido introducido($reteiva) es distinto al calculado ($reteiva_cal)</br>";

				$impt =0;
				if($do->get('simptimbre')=='S')
					$impt=round(($subtotal  *$this->datasis->traevalor('IMPTIMBRE')/100),2);

				if(round($imptimbre,2)-round($impt,2)>0.02 || round($impt,2)-round($imptimbre,2)>0.02)
				$error.="El Impuesto 1X1000 retenido introducido($imptimbre) es distinto al calculado ($impt)</br>";
			}
			

			if(empty($error)){

				$total    =$total2-$reteiva-$reten-$imptimbre-$otrasrete;

				$do->set('mivag'        ,$mivag    );
				$do->set('mivar'        ,$mivar    );
				$do->set('mivaa'        ,$mivaa    );
				$do->set('ivag'         ,$ivag     );
				$do->set('ivar'         ,$ivar     );
				$do->set('ivaa'         ,$ivaa     );
				//$do->set('reteiva'      ,$reteiva  );
				$do->set('total'        ,$total    );
				//$do->set('total2'        ,$total2    );
				//$do->set('subtotal'     ,$subtotal );
				$do->set('imptimbre'    ,$imptimbre     );
				//$do->set('reten'        ,$reten    );
			}
		}else{

			$do->set('ivag'          , $giva                );
			$do->set('ivar'          , $riva                );
			$do->set('ivaa'          , $aiva                );
			$do->set('exento'        , $exento              );
			$do->set('reteiva'       , $reteiva             );
			//$do->set('total'         , $total               );
			//$do->set('total2'        , $total2              );
		}

		if(!empty($error)){
			$do->error_message_ar['pre_ins']=$error;
			$do->error_message_ar['pre_upd']=$error;
			return false;
		}
	}

	function _post($do){
		$id=$do->get('numero');
		redirect("presupuesto/causacion/actualizar/$id");
	}

	function _post_insert($do){
		$numero = $do->get('numero');
		logusu('causacion',"Creo Causacion de orden de compras Nro $numero");
		//redirect($this->url."actualizar/$numero");
	}
	function _post_update($do){
		$numero = $do->get('numero');
		logusu('causacion'," Modifico Causacion de orden de compras Nro $numero");
		$status = $do->get('status');
		if($status=='O'){
		$numero = $this->db->escape($numero);
		$pago = $this->datasis->dameval("SELECT g.numero FROM ocompra e JOIN pacom f ON e.numero = f.compra JOIN odirect g ON f.pago=g.numero WHERE g.status ='F2' AND e.numero=$numero");
		$pago = $this->db->escape($pago);
		$query="SELECT SUM(a.subtotal) subtotal,SUM(a.exento) exento,SUM(a.ivag) ivag,SUM(a.mivag) mivag,SUM(a.ivar) ivar,SUM(a.mivar) mivar,SUM(a.ivaa) ivaa,SUM(a.mivaa) mivaa,SUM(a.total) total,SUM(a.reteiva) reteiva ,SUM(a.reten) reten,SUM(a.impmunicipal) impmunicipal,SUM(a.imptimbre) imptimbre,SUM(a.total2) total2
			FROM ocompra a
			JOIN pacom c ON a.numero = c.compra
			JOIN odirect b ON c.pago=b.numero
			WHERE b.numero=$pago AND a.status='O'
			GROUP BY b.numero
		";
		$d=$this->datasis->damerow($query);

		$query="UPDATE odirect SET subtotal=".$d['subtotal'].",exento=".$d['exento'].",ivag=".$d['ivag'].",mivag=".$d['mivag'].",ivar=".$d['ivar'].",mivar=".$d['mivar'].",ivaa=".$d['ivaa'].",mivaa=".$d['mivaa'].",total=".$d['total'].",reteiva=".$d['reteiva'].",reten=".$d['reten'].",impmunicipal=".$d['impmunicipal'].",imptimbre=".$d['imptimbre'].",total2=".$d['total2']." WHERE numero=$pago ";
		$this->db->query($query);
		}
		//redirect($this->url."actualizar/$numero");
	}
	function _post_delete($do){
		$numero = $do->get('numero');
		logusu('causacion'," Elimino Causacion de orden de compras Nro $numero");
	}

	function reversarall(){
		$query = $this->db->query("SELECT * FROM ocompra WHERE status = 'T' ");
		$result = $query->result();
		 foreach ($result AS $items){
		 	$numero =$items->numero;
		 	$this->reversar($numero);
		 }
	}

	function actualizarall(){
		$query = $this->db->query("SELECT * FROM ocompra WHERE status = 'C' ");
		$result = $query->result();
		 foreach ($result AS $items){
		 $numero =$items->numero;
		 	$this->actualizar($numero);
		 }
	}

	function instalar(){
		$query="ALTER TABLE `ocompra`  ADD COLUMN `mexento` DECIMAL(19,2) NULL DEFAULT 0";
		$this->db->simple_query($query);
	}
}
?>
