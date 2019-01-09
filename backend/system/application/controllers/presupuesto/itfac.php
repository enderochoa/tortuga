<?php
require_once(BASEPATH.'application/controllers/presupuesto/common.php');
class itfac extends Common {

var $titp  = 'Facturas Multiples';
var $tits  = 'Facturas Multiples';
var $url   = 'presupuesto/itfac/';

function itfac(){
	parent::Controller();
	$this->load->library("rapyd");
	//$this->datasis->modulo_id(119,1);
}

	function index(){
		redirect($this->url."filteredgrid");
	}

	function filteredgrid(){
		$this->rapyd->load("datafilter","datagrid");
		
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
		
		//$bSPRV=$this->datasis->p_modbus($mSPRV,"proveed");
		
		$filter = new DataFilter("");
		$filter->db->select(array("a.numero","a.fecha","a.tipo","a.uejecutora","a.estadmin","a.fondo","a.cod_prov","a.pago","a.total2","a.status","b.nombre nombre1","c.nombre nombre2"));
		$filter->db->from("odirect a");                  
		$filter->db->join("uejecutora b" ,"a.uejecutora=b.codigo");
		$filter->db->join("sprv c"       ,"c.proveed =a.cod_prov");
		$filter->db->where('MID(status,1,1) ','B');
		$filter->db->where('multiple =','S');
		
		$filter->numero = new inputField("N&uacute;mero", "numero");
		$filter->numero->size=12;
		
		$filter->tipo = new dropdownField("Orden de ", "tipo");
		$filter->tipo->option("","");
		$filter->tipo->option("Compra"  ,"Compra");
		$filter->tipo->option("Servicio","Servicio");
		$filter->tipo->style="width:100px;";
		
		$filter->fecha = new dateonlyField("Fecha", "fecha");
		$filter->fecha->size=12;
		
		//$filter->uejecutora = new inputField("Unidad Ejecutora", "uejecutora");
		//$filter->uejecutora->size=12;
		$filter->uejecutora = new dropdownField("U.Ejecutora", "uejecutora");
		$filter->uejecutora->option("","Seccionar");
		$filter->uejecutora->options("SELECT codigo,nombre FROM uejecutora ORDER BY nombre");
		$filter->uejecutora->onchange = "get_uadmin();";
		
		$filter->cod_prov = new inputField("Beneficiario", 'cod_prov');
		$filter->cod_prov->size = 6;
		//$filter->cod_prov->append($bSPRV);
		$filter->cod_prov->rule = "required";
		
		$filter->beneficiario = new inputField("Beneficiario", "beneficiario");
		$filter->beneficiario->size=60;
		
		$filter->buttons("reset","search");
		
		$filter->build();
		$uri = anchor($this->url.'dataedit/modify/<#numero#>','<str_pad><#numero#>|8|0|STR_PAD_LEFT</str_pad>');
		
		$grid = new DataGrid("");
		$grid->order_by("numero","desc");
		$grid->per_page = 20;
		$grid->use_function('substr','str_pad');
		
		$grid->column_orderby("N&uacute;mero"    ,$uri,"numero");
		$grid->column_orderby("Fecha"            ,"<dbdate_to_human><#fecha#></dbdate_to_human>"   ,"fecha"      ,"align='center'");
		$grid->column_orderby("Unidad Ejecutora" ,"nombre1"                                     ,"uejecutora" ,"NOWRAP"        );
		$grid->column_orderby("Beneficiario"    ,"nombre2"                                          ,"c.nombre"     ,"NOWRAP"        );
		$grid->column_orderby("Pago"            ,"<number_format><#total2#>|2|,|.</number_format>" ,"total"      ,"align='right'" );
		
		//$grid->column("Beneficiario"        ,"cod_prov"                                          ,"cod_prov"   );
		//$grid->column("Tipo"             ,"tipo"                                        ,"align='center'");
		//	$grid->column("Devoluci&oacute;n","<number_format><#devo#>|2|,|.</number_format>","align='rigth'");
	
		
		$grid->build();
		//echo $grid->db->last_query();
		
		$data['filtro']  = $filter->output;
		$data['content'] = $grid->output;
		$data['script'] = script("jquery.js")."\n";
		$data['title']   = "$this->titp";//"";
		//$data['content'] = $filter->output.$grid->output;
		//$data['title']   = " $this->titp ";
		$data["head"]    = $this->rapyd->get_head();
		$this->load->view('view_ventanas', $data);
	}

	function dataedit(){
		//$this->datasis->modulo_id(119,1);
		$this->rapyd->load('dataobject','datadetails');

		$do = new DataObject("odirect");
		$do->pointer('sprv' ,'sprv.proveed=odirect.cod_prov','sprv.nombre as nombre','LEFT');
	
		$do->rel_one_to_many('itfac', 'itfac', array('numero'=>'numero'));
			
		$edit = new DataDetails($this->tits, $do);
		$edit->back_url = site_url($this->url."filteredgrid");
		$edit->set_rel_title('itodirect','Rubro <#o#>');
	
		$edit->pre_process('update'  ,'_valida');
		$edit->pre_process('insert'  ,'_insert');
		//$edit->post_process('insert'  ,'_paiva');
		//$edit->post_process('update'  ,'_paiva');
		//$edit->post_process('insert','_post_insert');
		$edit->post_process('update','_post_update');
		//$edit->post_process('delete','_post_delete');
	
		$status=$edit->get_from_dataobjetct('status');
		
		$edit->numero  = new inputField("N&uacute;mero", "numero");
		$edit->numero->mode="autohide";
		$edit->numero->when=array('show');
	
		$edit->tipo = new dropdownField("Orden de ", "tipo");
		$edit->tipo->option("Compra"  ,"Compra");
		$edit->tipo->option("Servicio","Servicio");
		$edit->tipo->option("T","Transferencia");
		$edit->tipo->style="width:100px;";
		$edit->tipo->mode   = 'autohide';
		//$edit->tipo->when=array('modify');
	
		$edit->fecha = new  dateonlyField("Fecha",  "fecha");
		$edit->fecha->insertValue = date('Y-m-d');
		$edit->fecha->size =12;
		$edit->fecha->mode   = 'autohide';
		//$edit->fecha->when=array('modify');
	
		$edit->uejecutora = new dropdownField("Unidad Ejecutora", "uejecutora");
		$edit->uejecutora->option("","Seccionar");
		$edit->uejecutora->options("SELECT codigo, nombre FROM uejecutora ORDER BY nombre");
		//$edit->uejecutora->onchange = "get_uadmin();";
		$edit->uejecutora->rule = "required";
		$edit->uejecutora->style = "width:200px";
		$edit->uejecutora->mode   = 'autohide';
		//$edit->uejecutora->when=array('modify');
	
		$edit->estadmin = new dropdownField("Estructura Administrativa","estadmin");
		$edit->estadmin->option("","Seleccione");
		$edit->estadmin->rule='required';
		$edit->estadmin->options("SELECT b.codigo, CONCAT_WS(' ',b.codigo,b.denominacion) AS val FROM presupuesto AS a JOIN estruadm AS b ON a.codigoadm=b.codigo  GROUP BY b.codigo");
		$edit->estadmin->style="width:200px";
		$edit->estadmin->mode   = 'autohide';
		//$edit->estadmin->when=array('modify');
	
		$edit->fondo = new dropdownField("Fondo", "fondo");
		$edit->fondo->rule = "required";
		$edit->fondo->style = "width:220px";
		$edit->fondo->mode   = 'autohide';
		//$edit->fondo->when=array('modify');
		$estadmin=$edit->getval('estadmin');
		if($estadmin!==false){
		$edit->fondo->options("SELECT tipo,tipo a  FROM presupuesto WHERE codigoadm='$estadmin' GROUP BY tipo");
		}else{
		$edit->fondo->option("","Seleccione Estructura Administrativa");
		}
	
		$edit->codprov_sprv = new inputField("Beneficiario", 'codprov_sprv');
		$edit->codprov_sprv->db_name  = "cod_prov";
		$edit->codprov_sprv->size     = 4;
		//$edit->codprov_sprv->rule     = "required";
		//$edit->codprov_sprv->readonly =true;
		//$edit->codprov_sprv->append($bSPRV2);
		$edit->codprov_sprv->mode   = 'autohide';
		//$edit->codprov_sprv->when=array('modify');
			
		$edit->nombre = new inputField("Nombre", 'nombre');
		$edit->nombre->size = 20;
		$edit->nombre->readonly = true;
		$edit->nombre->pointer = TRUE;
		$edit->nombre->mode   = 'autohide';
		//$edit->nomfis->when=array('modify');
		
	
		$edit->reteiva_prov  = new inputField("reteiva_prov", "reteiva_prov");
		$edit->reteiva_prov->size=1;
		//$edit->reteiva_prov->mode="autohide";		
		//$edit->reteiva_prov->when=array('modify');
				
		$edit->observa = new textAreaField("Observaciones", 'observa');
		$edit->observa->cols = 70;
		$edit->observa->rows = 3;
		$edit->observa->mode   = 'autohide';
		//$edit->observa->when=array('modify');
	
		$edit->reteiva = new inputField("Retencion IVA", 'reteiva');
		$edit->reteiva->css_class='inputnum';
		$edit->reteiva->size = 8;
		$edit->reteiva->mode   = 'autohide';
	
		$edit->total2 = new inputField("Total", 'total2');
		$edit->total2->css_class='inputnum';
		$edit->total2->size = 8;
		$edit->total2->mode   = 'autohide';
		
		$edit->total = new inputField("Total", 'total');
		$edit->total->css_class='inputnum';
		$edit->total->size = 8;
		$edit->total->mode   = 'autohide';
		
		$edit->tivag = new inputField("","tivag");
		$edit->tivag->mode = "autohide";
		//$edit->tivag->when=array('modify');
		
		$edit->tivar = new inputField("","tivar");
		$edit->tivar->mode = "autohide";
		//$edit->tivar->when=array('modify');
		
		$edit->tivaa = new inputField("","tivaa");
		$edit->tivaa->mode = "autohide";
		//$edit->tivaa->when=array('modify');
		
		$edit->ivag = new inputField("","ivag");
		$edit->ivag->mode = "autohide";
		//$edit->ivag->when=array('modify');
		
		$edit->ivar = new inputField("","ivar");
		$edit->ivar->mode = "autohide";
		//$edit->ivar->when=array('modify');
		
		$edit->ivaa = new inputField("","ivaa");
		$edit->ivaa->mode = "autohide";
		//$edit->ivaa->when=array('modify');
		
		$edit->subtotal = new inputField("","subtotal");
		$edit->subtotal->mode = "autohide";
		//$edit->subtotal->when=array('modify');
		
		$edit->exento = new inputField("","exento");
		$edit->exento->mode = "autohide";
		//$edit->exento->when=array('modify');
	
		///////VISUALES  INICIO ////////////////

		$edit->tsubtotal = new inputField("","tsubtotal");
		$edit->tsubtotal->readonly = true;
		$edit->tsubtotal->size = 8 ;
		$edit->tsubtotal->when=array('modify');
		
		$edit->texento = new inputField("","texento");
		$edit->texento->readonly = true;
		$edit->texento->size = 8 ;
		$edit->texento->when=array('modify');
		
		$edit->trivag = new inputField("","trivag");
		$edit->trivag->readonly = true;
		$edit->trivag->size = 8 ;
		$edit->trivag->when=array('modify');
		         
		$edit->trivar = new inputField("","trivar");
		$edit->trivar->readonly = true;
		$edit->trivar->size = 8 ;
		$edit->trivar->when=array('modify');
		         
		$edit->trivaa = new inputField("","trivaa");
		$edit->trivaa->readonly = true;
		$edit->trivaa->size = 8 ;
		$edit->trivaa->when=array('modify');
		
		$edit->treteiva = new inputField("","treteiva");
		$edit->treteiva->readonly = true;
		$edit->treteiva->size = 8 ;
		$edit->treteiva->when=array('modify');
		
		$edit->ttotal = new inputField("","ttotal");
		$edit->ttotal->readonly = true;
		$edit->ttotal->size = 8 ;
		$edit->ttotal->when=array('modify');
		
		$edit->ttotal2 = new inputField("","ttotal2");
		$edit->ttotal2->readonly = true;
		$edit->ttotal2->size = 8;
		$edit->ttotal2->when=array('modify');
		///////FIN VISUALES ////////////////////
			
		$edit->itfactura = new inputField("(<#o#>) Factura", "factura_<#i#>");
		$edit->itfactura->size=10;
		$edit->itfactura->db_name='factura';
		$edit->itfactura->rel_id ='itfac';
		$edit->itfactura->rule ='required';
		
		$edit->itcontrolfac = new inputField("(<#o#>) Control Fiscal", "controlfac_<#i#>");
		$edit->itcontrolfac->db_name  ='controlfac';
		//$edit->itcontrolfac->maxlength=3;
		$edit->itcontrolfac->size     =10;		
		$edit->itcontrolfac->rel_id   ='itfac';
		$edit->itcontrolfac->rule   ='required';
	
		$edit->itfechafac = new dateonlyField("(<#o#>) Fecha Factura", "fechafac_<#i#>");
		$edit->itfechafac->db_name  ='fechafac';
		$edit->itfechafac->insertValue = date('Y-m-d');
		//$edit->itfechafac->maxlength=80;
		$edit->itfechafac->size     =10;
		$edit->itfechafac->rule     = 'required';
		$edit->itfechafac->rel_id   ='itfac';
		
		$edit->itsubtotal = new inputField("(<#o#>) Total", "subtotal_<#i#>");
		$edit->itsubtotal->size=8;
		$edit->itsubtotal->db_name= 'subtotal';
		$edit->itsubtotal->rel_id = 'itfac';
		$edit->itsubtotal->onchange ='cal_subtotal(<#i#>);';
		$edit->itsubtotal->css_class = "inputnum";
		if($status=="B3")$edit->itsubtotal->mode = "autohide";
		
		$edit->itexento = new inputField("(<#o#>) Exento", "exento_<#i#>");
		$edit->itexento->size=8;
		$edit->itexento->db_name='exento';
		$edit->itexento->rel_id ='itfac';
		$edit->itexento->css_class = "inputnum";
		//$edit->itexento->rule ='required';
		if($status=="B3")$edit->itexento->mode = "autohide";
		
		$edit->itivag = new inputField("(<#o#>) % IVA General", "ivag_<#i#>");
		$edit->itivag->size=8;		
		$edit->itivag->db_name= 'ivag';
		//$edit->itivag->rule   = 'required';
		$edit->itivag->rel_id = 'itfac';
		//$edit->itivag->insertValue = 0;
		$edit->itivag->onchange ='cal_itivag(<#i#>);';
		$edit->itivag->css_class = "inputnum";
		if($status=="B3")$edit->itivag->mode = "autohide";
		
		$edit->itivar = new inputField("(<#o#>) % IVA Reducido", "ivar_<#i#>");
		$edit->itivar->size=8;		
		$edit->itivar->db_name= 'ivar';
		//$edit->itivar->rule   = 'required';
		$edit->itivar->rel_id = 'itfac';
		//$edit->itivar->insertValue = 0;
		$edit->itivar->onchange ='cal_itivar(<#i#>);';
		$edit->itivar->css_class = "inputnum";
		if($status=="B3")$edit->itivar->mode = "autohide";
		
		$edit->itivaa = new inputField("(<#o#>) % IVA Adicional", "ivaa_<#i#>");
		$edit->itivaa->size=8;		
		$edit->itivaa->db_name= 'ivaa';
		$edit->itivaa->rel_id = 'itfac';
		$edit->itivaa->onchange ='cal_itivaa(<#i#>);';
		$edit->itivaa->css_class = "inputnum";
		if($status=="B3")$edit->itivaa->mode = "autohide";
		
		$edit->itreteiva = new inputField("(<#o#>) % IVA Adicional", "reteiva_<#i#>");
		$edit->itreteiva->size=8;		
		$edit->itreteiva->db_name= 'reteiva';
		$edit->itreteiva->rel_id = 'itfac';
		$edit->itreteiva->readonly = true;
		//$edit->itreteiva->onchange ='cal_subtotal2(<#i#>,TRUE);';
		if($status=="B3")$edit->itreteiva->mode = "autohide";
		
		$edit->ittotal = new inputField("(<#o#>) % IVA Adicional", "total_<#i#>");
		$edit->ittotal->size=8;		
		$edit->ittotal->db_name= 'total';
		$edit->ittotal->rel_id = 'itfac';
		$edit->ittotal->readonly = true;
		if($status=="B3")$edit->ittotal->mode = "autohide";
		
		$edit->ittotal2 = new inputField("(<#o#>) % IVA Adicional", "total2_<#i#>");
		$edit->ittotal2->size=8;		
		$edit->ittotal2->db_name= 'total2';
		$edit->ittotal2->rel_id = 'itfac';
		$edit->ittotal2->readonly = true;
		if($status=="B3")$edit->ittotal2->mode = "autohide";
				
		$status=$edit->get_from_dataobjetct('status');
		
		if($status=='B1'){
			$edit->buttons("modify","save","undo","back","add_rel");
			
		}elseif($status=='B2' ){
			
			$edit->buttons("undo","back");
		}elseif($status=='B3'){
			$multiple=$edit->get_from_dataobjetct('multiple');
			
			if($multiple=="S"){
			
				$edit->buttons("modify","save");
			}
			$edit->buttons("undo","back");
		}else{
			$edit->buttons("save","undo","back");
		}
				
		$edit->build();			
		
		$ivaplica=$this->ivaplica2();		
		$conten['ivar']    =$ivaplica['redutasa'];
		$conten['ivag']    =$ivaplica['tasa'];
		$conten['ivaa']    =$ivaplica['sobretasa'];
		$conten['status']  =$status;  
		
		$smenu['link']=barra_menu('119');
		$data['smenu'] = $this->load->view('view_sub_menu', $smenu,true);
		$conten["form"]  =&  $edit;
		$data['content'] = $this->load->view('view_itfac', $conten,true);
		//$data['content'] = $edit->output;
		$data['title']   = "$this->tits";
		$data["head"]    = $this->rapyd->get_head().script('jquery.js').script("plugins/jquery.numeric.pack.js").script("plugins/jquery.json.min.js");
		$this->load->view('view_ventanas', $data);
	}

	function _valida($do){
		
		$numero  = $do->get('numero');
		$cod_prov= $do->get('cod_prov');
		$status  = $do->get('status');
		
		$subtotal=$ivaa=$ivag=$ivar=$total=$exento=0;$error='';
		for($i=0;$i < $do->count_rel('itfac');$i++){
			$subtotal  += round($do->get_rel('itfac','subtotal'    ,$i),2);
			$ivaa      += round($do->get_rel('itfac','ivaa'        ,$i),2);
			$ivag      += round($do->get_rel('itfac','ivag'        ,$i),2);
			$ivar      += round($do->get_rel('itfac','ivar'        ,$i),2);
			$exento    += round($do->get_rel('itfac','exento'      ,$i),2);
			$total     += round($do->get_rel('itfac','total'       ,$i),2);
			$factura    = $do->get_rel('itfac','factura'     ,$i);
			$controlfac = $do->get_rel('itfac','controlfac'  ,$i);
			$fechafac   = $do->get_rel('itfac','fechafac'    ,$i);
			$id         = $do->get_rel('itfac','id'          ,$i);
			
			$this->chexiste_factura($numero,$factura,$controlfac,$cod_prov,substr($status,0,1),$e);
			$error.=$e;
		}
		
		$s  = $do->get('subtotal');
		$a  = $do->get('ivaa'    );
		$g  = $do->get('ivag'    );
		$r  = $do->get('ivar'    );
		$e  = $do->get('exento'  );
		$t  = $do->get('total'   );
	
	  
	  //se quito la tolerancia de decimales distintos
	  if(((round((round($s,2)-round($subtotal,2)),2) > 0))|| (round(round($subtotal,2)-(round($s,2)),2) > 0) )$error.="<div class='alert'><p>La Suma de los Subtotales ($subtotal) de las facturas es diferente al subtotal ($s) de la orden de pago</p></div>";
	  if(((round((round($a,2)-round($ivaa    ,2)),2) > 0))|| (round(round($ivaa    ,2)-(round($a,2)),2) > 0) )$error.="<div class='alert'><p>La Suma de los IVA Adicionales ($ivaa) de las facturas es diferente al IVA adicional ($a) de la orden de pago</p></div>";
	  if(((round((round($r,2)-round($ivar    ,2)),2) > 0))|| (round(round($ivar    ,2)-(round($r,2)),2) > 0) )$error.="<div class='alert'><p>La Suma de los IVA Reducidos ($ivar) de las facturas es diferente al IVA reducido ($r) de la orden de pago</p></div>";
	  if(((round((round($g,2)-round($ivag    ,2)),2) > 0))|| (round(round($ivag    ,2)-(round($g,2)),2) > 0) )$error.="<div class='alert'><p>La Suma de los IVA Generales ($ivag) de las facturas es diferente al IVA general ($g) de la orden de pago</p></div>";
	  if(((round((round($e,2)-round($exento  ,2)),2) > 0))|| (round(round($exento  ,2)-(round($e,2)),2) > 0) )$error.="<div class='alert'><p>La Suma de los Exentos ($exento) de las facturas es diferente al exento ($e) de la orden de pago</p></div>";
	  if(((round((round($t,2)-round($total   ,2)),2) > 0))|| (round(round($total   ,2)-(round($t,2)),2) > 0) )$error.="<div class='alert'><p>La Suma de los Totales ($total) de las facturas es diferente al total ($t) de la orden de pago</p></div>";
	  
		if(!empty($error)){
			logusu('itfac',"Intento Modificar varias Factura de Orden de Pago Nro $numero cpn ERROR:$error");
			$do->error_message_ar['pre_ins']=$error;
			$do->error_message_ar['pre_upd']=$error;
			return false;
			
			//$data['content'] = $error.anchor($this->url."dataedit/show/$id",'Regresar');
			//$data['title']   = " Error ";
			//$data["head"]    = $this->rapyd->get_head().script('jquery.js').script("plugins/jquery.numeric.pack.js");
			//$this->load->view('view_ventanas', $data);
			//exit();
		}
		
	}
	/*
	function chexiste_factura($numero,$id,$factura,$controlfac,$codprov_sprv,&$error){
		$controlfac = $this->db->escape($controlfac   );
		$cod_prov   = $this->db->escape($codprov_sprv );
		$factura    = $this->db->escape($factura      );
		
		$query = "SELECT SUM(a) FROM (
			SELECT COUNT(*)a FROM odirect WHERE (controlfac =$controlfac OR factura=$factura) AND cod_prov=$cod_prov
			UNION ALL
			SELECT COUNT(*) FROM ocompra WHERE (controlfac =$controlfac OR factura=$factura) AND cod_prov=$cod_prov
			UNION ALL
			SELECT COUNT(*) FROM itfac JOIN odirect ON odirect.numero = itfac.numero WHERE (itfac.controlfac =$controlfac OR itfac.factura=$factura)".(($id>0) ? " AND itfac.id<>$id ":" ")
			."UNION ALL
			SELECT COUNT(*) FROM itrendi WHERE (controlfac =$controlfac OR factura=$factura) AND cod_prov=$cod_prov
		)a";
			
		$query = "
			SELECT CONCAT_WS(' ','O Pago Normal ',numero) numero FROM odirect WHERE (controlfac =$controlfac OR factura=$factura) AND cod_prov=$cod_prov
			UNION ALL
			SELECT CONCAT_WS(' ','O Compra ',numero) numero FROM ocompra WHERE (controlfac =$controlfac OR factura=$factura) AND cod_prov=$cod_prov
			UNION ALL
			SELECT CONCAT_WS(' ','O Pago Varias Facturas ',id) numero FROM itfac JOIN odirect ON odirect.numero = itfac.numero WHERE (itfac.controlfac =$controlfac OR itfac.factura=$factura)".(($id>0) ? " AND itfac.id<>$id ":" ")
			."UNION ALL
			SELECT CONCAT_WS(' ','Rendicion de Cuentas ',numero) numero FROM itrendi WHERE (controlfac =$controlfac OR factura=$factura) AND cod_prov=$cod_prov
		";
		
		$cana=$this->datasis->dameval($query);
		
		if($cana>0){
			$nombre = $this->datasis->dameval("SELECT nombre FROM sprv WHERE proveed=$cod_prov");
			$error="La Factura($factura) o el Control Fiscal($controlfac) Ya existen para el Beneficiario ($cod_prov) $nombre | $cana</br>";
		}	
	}
	*/
	
	function _insert($do){
		$do->set("status","B1");
	
	}
	
	function ivaplica2($mfecha=NULL){
		if(empty($mfecha)) $mfecha=date('Ymd');
		$CI =& get_instance();
		$qq = $CI->db->query("SELECT tasa, redutasa, sobretasa FROM civa WHERE fecha < '$mfecha' ORDER BY fecha DESC LIMIT 1");
		$rr = $qq->row_array();
		//$aa = each($rr);
		return $rr;
	}
	
	function _post_update($do){
		$numero = $do->get('numero');
		logusu('itfac'," Modifico varias Factura de Orden de Pago Nro $numero");
		//redirect($this->url."actualizar/$numero");
	}
}
?>


