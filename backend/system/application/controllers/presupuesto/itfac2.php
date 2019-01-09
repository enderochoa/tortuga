<?php
class itfac2 extends Controller {

var $titp  = 'Ingresar Facturas';
var $tits  = 'Ingresar Facturas';
var $url   = 'presupuesto/itfac2/';

function itfac2(){
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
		$filter->db->select(array("d.pago","a.numero","a.fecha","a.tipo","a.uejecutora","a.cod_prov","a.total2","a.status","c.nombre nombre2"));
		$filter->db->from("ocompra a");
		$filter->db->join("pacom d"      ,"a.numero=d.compra","left");
		$filter->db->join("sprv c"       ,"c.proveed =a.cod_prov","left");
		$filter->db->where('status in ("P","C","T","O","E")');
		//$filter->db->where('multiple =','S');
		
		$filter->numero = new inputField("N&uacute;mero", "numero");
		$filter->numero->size=12;
		
		$filter->pago = new inputField("Orden de Pago", "pago");
		$filter->pago->size=12;
		
		$filter->fecha = new dateonlyField("Fecha", "fecha");
		$filter->fecha->size=12;
				
		$filter->cod_prov = new inputField("Beneficiario", 'cod_prov');
		$filter->cod_prov->db_name="a.cod_prov";
		$filter->cod_prov->size = 6;
		//$filter->cod_prov->append($bSPRV);
		$filter->cod_prov->rule = "required";
		
		$filter->buttons("reset","search");
		
		$filter->build();
		$uri = anchor($this->url.'dataedit/modify/<#numero#>','<str_pad><#numero#>|8|0|STR_PAD_LEFT</str_pad>');
		
		$grid = new DataGrid("");
		$grid->order_by("numero","desc");
		$grid->per_page = 20;
		$grid->use_function('substr','str_pad');
		
		$grid->column_orderby("N&uacute;mero"    ,$uri  ,"numero");
		$grid->column_orderby("O.Pago"           ,"pago","pago"  );
		$grid->column_orderby("Fecha"            ,"<dbdate_to_human><#fecha#></dbdate_to_human>"     ,"fecha"      ,"align='center'"  );
		$grid->column_orderby("Beneficiario"     ,"nombre2"                                          ,"c.nombre"     ,"NOWRAP"        );
		$grid->column_orderby("Pago"             ,"<number_format><#total2#>|2|,|.</number_format>"  ,"total"      ,"align='right'"   );
		$grid->column_orderby("Estado"           ,"status"                                           ,"status"                        );
		
		//$grid->add($this->url."dataedit/create");
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

	function dataedit($status='',$numero=''){
		//$this->datasis->modulo_id(119,1);
		$this->rapyd->load('dataobject','datadetails');

		$do = new DataObject("ocompra");
		$do->pointer('sprv' ,'sprv.proveed=ocompra.cod_prov','sprv.nombre as nombrep','LEFT');
	
		$do->rel_one_to_many('itfac', 'itfac', array('numero'=>'nocompra'));
			
		$edit = new DataDetails($this->tits, $do);
		$edit->back_url = site_url($this->url."filteredgrid");
		$edit->set_rel_title('itodirect','Rubro <#o#>');
	
		$edit->pre_process('update'  ,'_valida');
		$edit->pre_process('insert'  ,'_insert');
		$edit->post_process('update','_post_update');
	
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
		
		$edit->creten = new dropdownField("Cod ISLR: ","creten");
		//$edit->creten->mode   = "autohide";
		//$edit->creten->option("","");
		$edit->creten->options("SELECT codigo,CONCAT_WS(' ',codigo,activida) FROM rete ORDER BY codigo");
		$edit->creten->style="width:350px;";		
		$edit->creten->onchange ='cal_reten();';
	
		$edit->uejecutora = new dropdownField("Unidad Ejecutora", "uejecutora");
		$edit->uejecutora->option("","Seccionar");
		$edit->uejecutora->options("SELECT codigo, nombre FROM uejecutora ORDER BY nombre");
		//$edit->uejecutora->onchange = "get_uadmin();";
		$edit->uejecutora->rule = "required";
		$edit->uejecutora->style = "width:200px";
		$edit->uejecutora->mode   = 'autohide';
		//$edit->uejecutora->when=array('modify');
	
		//$edit->estadmin = new dropdownField("Estructura Administrativa","estadmin");
		//$edit->estadmin->option("","Seleccione");
		//$edit->estadmin->rule='required';
		//$edit->estadmin->options("SELECT b.codigo, CONCAT_WS(' ',b.codigo,b.denominacion) AS val FROM presupuesto AS a JOIN estruadm AS b ON a.codigoadm=b.codigo  GROUP BY b.codigo");
		//$edit->estadmin->style="width:200px";
		//$edit->estadmin->mode   = 'autohide';
		////$edit->estadmin->when=array('modify');
	        //
		//$edit->fondo = new dropdownField("Fondo", "fondo");
		//$edit->fondo->rule = "required";
		//$edit->fondo->style = "width:220px";
		//$edit->fondo->mode   = 'autohide';
		////$edit->fondo->when=array('modify');
		//$estadmin=$edit->getval('estadmin');
		//if($estadmin!==false){
		//$edit->fondo->options("SELECT tipo,tipo a  FROM presupuesto WHERE codigoadm='$estadmin' GROUP BY tipo");
		//}else{
		//$edit->fondo->option("","Seleccione Estructura Administrativa");
		//}
	
		$edit->codprov_sprv = new inputField("Beneficiario", 'codprov_sprv');
		$edit->codprov_sprv->db_name  = "cod_prov";
		$edit->codprov_sprv->size     = 1;
		//$edit->codprov_sprv->rule     = "required";
		//$edit->codprov_sprv->readonly =true;
		//$edit->codprov_sprv->append($bSPRV2);
		$edit->codprov_sprv->mode   = 'autohide';
		//$edit->codprov_sprv->when=array('modify');
			
		$edit->nombrep = new inputField("Nombre", 'nombrep');
		$edit->nombrep->size     = 20;
		$edit->nombrep->readonly = true;
		$edit->nombrep->pointer  = TRUE;
		$edit->nombrep->mode     = 'autohide';
		//$edit->nomfis->when=array('modify');
		
		$edit->reteiva_prov = new dropdownField("Retenci&oacute;n de IVA %","reteiva_prov");
		$edit->reteiva_prov->option("75" ,"75%");
		$edit->reteiva_prov->option("100","100%");
		$edit->reteiva_prov->style="width:70px;";
		$edit->reteiva_prov->onchange ='cal_reten();';
				
		$edit->observa = new textAreaField("Observaciones", 'observa');
		$edit->observa->cols = 70;
		$edit->observa->rows = 3;
		$edit->observa->mode   = 'autohide';
		//$edit->observa->when=array('modify');
	
		$edit->reteiva = new inputField("Retencion IVA", 'reteiva');
		$edit->reteiva->css_class='inputnum';
		$edit->reteiva->size = 8;
		$edit->reteiva->mode   = 'autohide';
		
		$edit->reten = new inputField("Retencion IVA", 'reten');
		$edit->reten->css_class='inputnum';
		$edit->reten->size     = 8;
		$edit->reten->mode     = 'autohide';
		
		$edit->imptimbre = new inputField("", 'imptimbre');
		$edit->imptimbre->css_class='inputnum';
		$edit->imptimbre->size     = 8;
		$edit->imptimbre->mode     = 'autohide';
	
		$edit->total2 = new inputField("Total", 'total2');
		$edit->total2->css_class='inputnum';
		$edit->total2->size = 8;
		$edit->total2->mode   = 'autohide';
		
		$edit->total = new inputField("Total", 'total');
		$edit->total->css_class='inputnum';
		$edit->total->size = 8;
		$edit->total->mode   = 'autohide';
		
		$edit->tivag = new inputField("","tivag");
		//$edit->tivag->size = 8 ;
		$edit->tivag->mode = "autohide";
		//$edit->tivag->when=array('modify');
		
		$edit->tivar = new inputField("","tivar");
		//$edit->tivar->size = 8 ;
		$edit->tivar->mode = "autohide";
		//$edit->tivar->when=array('modify');
		
		$edit->tivaa = new inputField("","tivaa");
		//$edit->tivaa->size = 8 ;
		$edit->tivaa->mode = "autohide";
		//$edit->tivaa->when=array('modify');
		
		$edit->ivag = new inputField("","ivag");
		$edit->ivag->size = 8 ;
		$edit->ivag->mode = "autohide";
		//$edit->ivag->when=array('modify');
		
		$edit->ivar = new inputField("","ivar");
		$edit->ivar->size = 8 ;
		$edit->ivar->mode = "autohide";
		//$edit->ivar->when=array('modify');
		
		$edit->ivaa = new inputField("","ivaa");
		$edit->ivaa->size = 8 ;
		$edit->ivaa->mode = "autohide";
		//$edit->ivaa->when=array('modify');
		
		$edit->subtotal = new inputField("","subtotal");
		$edit->subtotal->size = 8 ;
		$edit->subtotal->mode = "autohide";
		//$edit->subtotal->when=array('modify');
		
		$edit->exento = new inputField("","exento");
		$edit->exento->size = 8 ;
		$edit->exento->mode = "autohide";
		//$edit->exento->when=array('modify');
		
		$edit->simptimbre = new checkboxField("1X1000", "simptimbre", "S","N");   
		$edit->simptimbre->insertValue = "N";
		$edit->simptimbre->onchange ='cal_simpt();';
		//if($status=='P')
		//$edit->simptimbre->mode="autohide";
	
		///////VISUALES  INICIO ////////////////

		$edit->tsubtotal = new inputField("","tsubtotal");
		$edit->tsubtotal->readonly = true;
		$edit->tsubtotal->size = 8 ;
		$edit->tsubtotal->when=array('modify');
		
		$edit->texento = new inputField("","texento");
		//$edit->texento->readonly = true;
		$edit->texento->size = 8 ;
		$edit->texento->when=array('modify');
		
		$edit->trivag = new inputField("","trivag");
		//$edit->trivag->readonly = true;
		$edit->trivag->size = 8 ;
		$edit->trivag->when=array('modify');
		$edit->trivag->onchange ='cal_totales();';
		         
		$edit->trivar = new inputField("","trivar");
		//$edit->trivar->readonly = true;
		$edit->trivar->size = 8 ;
		$edit->trivar->when=array('modify');
		$edit->trivar->onchange ='cal_totales();';
		         
		$edit->trivaa = new inputField("","trivaa");
		//$edit->trivaa->readonly = true;
		$edit->trivaa->size = 8 ;
		$edit->trivaa->when=array('modify');
		$edit->trivaa->onchange ='cal_totales();';
		
		$edit->treteiva = new inputField("","treteiva");
		//$edit->treteiva->readonly = true;
		$edit->treteiva->size = 8 ;
		$edit->treteiva->when=array('modify');
		$edit->treteiva->onchange ='cal_totales();';
		
		$edit->treten = new inputField("","treten");
		//$edit->treten->readonly = true;
		$edit->treten->size = 8 ;
		$edit->treten->when=array('modify');
		$edit->treten->onchange ='cal_totales();';
		
		$edit->timptimbre = new inputField("","timptimbre");
		//$edit->timptimbre->readonly = true;
		$edit->timptimbre->size = 8 ;
		$edit->timptimbre->when=array('modify');
		$edit->timptimbre->onchange ='cal_totales();';
		
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
		$edit->itcontrolfac->rule     ='required';
	
		$edit->itfechafac = new dateonlyField("(<#o#>) Fecha Factura", "fechafac_<#i#>");
		$edit->itfechafac->db_name  ='fechafac';
		$edit->itfechafac->insertValue = date('Y-m-d');
		//$edit->itfechafac->maxlength=80;
		$edit->itfechafac->size     =8;
		$edit->itfechafac->rule     = 'required';
		$edit->itfechafac->rel_id   ='itfac';
		
		$edit->itsubtotal = new inputField("(<#o#>) Total", "subtotal_<#i#>");
		$edit->itsubtotal->size=8;
		$edit->itsubtotal->db_name= 'subtotal';
		$edit->itsubtotal->rel_id = 'itfac';
		$edit->itsubtotal->onchange ='cal_subtotal(<#i#>,true);';
		$edit->itsubtotal->css_class = "inputnum";
		if($status=="E")$edit->itsubtotal->mode = "autohide";
		
		$edit->itexento = new inputField("(<#o#>) Exento", "exento_<#i#>");
		$edit->itexento->size=8;
		$edit->itexento->db_name='exento';
		$edit->itexento->rel_id ='itfac';
		$edit->itexento->css_class = "inputnum";
		//$edit->itexento->rule ='required';
		if($status=="E")$edit->itexento->mode = "autohide";
		
		$edit->ituivag = new dropdownField("(<#o#>)", "uivag_<#i#>");
		$edit->ituivag->rel_id ='itfac';
		$edit->ituivag->db_name='uivag';
		$edit->ituivag->onchange ='cal_subtotal(<#i#>,true);';
		$edit->ituivag->option("S","Si");
		$edit->ituivag->option("N","No");
		$edit->ituivag->style="width:35px;";
		
		$edit->itivag = new inputField("(<#o#>) % IVA General", "ivag_<#i#>");
		$edit->itivag->size=8;		
		$edit->itivag->db_name= 'ivag';
		//$edit->itivag->rule   = 'required';
		$edit->itivag->rel_id = 'itfac';
		//$edit->itivag->insertValue = 0;
		$edit->itivag->onchange ='cal_subtotal2(<#i#>,true);';
		$edit->itivag->css_class = "inputnum";
		if($status=="E")$edit->itivag->mode = "autohide";
		
		$edit->ituivar = new dropdownField("", "uivar_<#i#>");
		$edit->ituivar->rel_id ='itfac';
		$edit->ituivar->db_name='uivar';
		$edit->ituivar->onchange ='cal_subtotal(<#i#>,true);';
		$edit->ituivar->option("S","Si");
		$edit->ituivar->option("N","No");
		$edit->ituivar->style="width:35px;";
		
		$edit->itivar = new inputField("(<#o#>) % IVA Reducido", "ivar_<#i#>");
		$edit->itivar->size=8;		
		$edit->itivar->db_name= 'ivar';
		//$edit->itivar->rule   = 'required';
		$edit->itivar->rel_id = 'itfac';
		//$edit->itivar->insertValue = 0;
		$edit->itivar->onchange ='cal_subtotal2(<#i#>,true);';
		$edit->itivar->css_class = "inputnum";
		if($status=="E")$edit->itivar->mode = "autohide";
		
		$edit->ituivaa = new dropdownField("", "uivaa_<#i#>");
		$edit->ituivaa->rel_id ='itfac';
		$edit->ituivaa->db_name='uivaa';
		$edit->ituivaa->onchange ='cal_subtotal(<#i#>,true);';
		$edit->ituivaa->option("S","Si");
		$edit->ituivaa->option("N","No");
		$edit->ituivaa->style="width:35px;";
		
		$edit->itivaa = new inputField("(<#o#>) % IVA Adicional", "ivaa_<#i#>");
		$edit->itivaa->size=8;		
		$edit->itivaa->db_name= 'ivaa';
		$edit->itivaa->rel_id = 'itfac';
		$edit->itivaa->onchange ='cal_subtotal2(<#i#>,true);';
		$edit->itivaa->css_class = "inputnum";
		if($status=="E")$edit->itivaa->mode = "autohide";
		
		$edit->itreteiva = new inputField("(<#o#>) % IVA Adicional", "reteiva_<#i#>");
		$edit->itreteiva->size=8;		
		$edit->itreteiva->db_name= 'reteiva';
		$edit->itreteiva->rel_id = 'itfac';
		//$edit->itreteiva->readonly = true;
		$edit->itreteiva->onchange ='cal_subtotal2(<#i#>,true);';
		if($status=="E")$edit->itreteiva->mode = "autohide";
		
		$edit->itureten = new dropdownField("", "ureten_<#i#>");
		$edit->itureten->rel_id ='itfac';
		$edit->itureten->db_name='ureten';
		$edit->itureten->onchange ='cal_subtotal(<#i#>,true);';
		$edit->itureten->option("S","Si");
		$edit->itureten->option("N","No");
		$edit->itureten->style="width:35px;";
		
		$edit->itreten = new inputField("(<#o#>) ISLR", "rete_<#i#>");
		$edit->itreten->size=8;		
		$edit->itreten->db_name= 'reten';
		$edit->itreten->rel_id = 'itfac';
		$edit->itreten->readonly = true;
		if($status=="E")$edit->itreten->mode = "autohide";
		
		$edit->ituimptimbre = new dropdownField("", "uimptimbre_<#i#>");
		$edit->ituimptimbre->rel_id ='itfac';
		$edit->ituimptimbre->db_name='uimptimbre';
		$edit->ituimptimbre->onchange ='cal_subtotal(<#i#>,true);';
		$edit->ituimptimbre->option("S","Si");
		$edit->ituimptimbre->option("N","No");
		$edit->ituimptimbre->style="width:35px;";
		
		$edit->itimptimbre = new inputField("(<#o#>) ISLR", "imptimbre_<#i#>");
		$edit->itimptimbre->size=8;		
		$edit->itimptimbre->db_name= 'imptimbre';
		$edit->itimptimbre->rel_id = 'itfac';
		$edit->itimptimbre->readonly = true;
		if($status=="E")$edit->itreten->mode = "autohide";
		
		$edit->ittotal = new inputField("(<#o#>) % IVA Adicional", "total_<#i#>");
		$edit->ittotal->size=8;
		$edit->ittotal->db_name= 'total';
		$edit->ittotal->rel_id = 'itfac';
		$edit->ittotal->readonly = true;
		if($status=="E")$edit->ittotal->mode = "autohide";
		
		$edit->ittotal2 = new inputField("(<#o#>) % IVA Adicional", "total2_<#i#>");
		$edit->ittotal2->size=8;		
		$edit->ittotal2->db_name= 'total2';
		$edit->ittotal2->rel_id = 'itfac';
		$edit->ittotal2->readonly = true;
		if($status=="E")$edit->ittotal2->mode = "autohide";
				
		$status=$edit->get_from_dataobjetct('status');
		
		$action = "javascript:window.location='" .site_url('presupuesto/itfac2/load/'.$edit->rapyd->uri->get_edited_id()). "'";
		$edit->button_status("btn_cargar",'Cargar desde .xls',$action,"TL","show");
		
		if($status=='C' || $status=='T' || $status=='O'){
			$edit->buttons("modify","save","undo","back","add_rel");
		}else{
			$edit->buttons("save","undo","back");
		}
		$edit->buttons("add_rel");
		
		$edit->build();
		
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
		
		$ivaplica=$this->ivaplica2();
		$conten['ivar']    =$ivaplica['redutasa'];
		$conten['ivag']    =$ivaplica['tasa'];
		$conten['ivaa']    =$ivaplica['sobretasa'];
		$conten['status']  =$status;
		$conten['imptimbre']=$this->datasis->traevalor('IMPTIMBRE',0);
		$conten['rete']    =$rete;
		
		$smenu['link']=barra_menu('211');
		$data['smenu'] = $this->load->view('view_sub_menu', $smenu,true);
		$conten["form"]  =&  $edit;
		$data['content'] = $this->load->view('view_itfac2', $conten,true);
		//$data['content'] = $edit->output;
		$data['title']   = "$this->tits";
		$data["head"]    = $this->rapyd->get_head().script('jquery.js').script("plugins/jquery.numeric.pack.js").script("plugins/jquery.json.min.js");
		$this->load->view('view_ventanas', $data);
	}

	function _valida($do){
		$numero       = $do->get('numero');
		$cod_prov     = $do->get('cod_prov');
		
		$texento    = $this->input->post('texento');
		$trivag     = $this->input->post('trivag');
		$trivaa     = $this->input->post('trivaa');
		$trivar     = $this->input->post('trivar');
		$tsubtotal  = $this->input->post('tsubtotal');
		$treteiva   = $this->input->post('treteiva');
		$treten     = $this->input->post('treten');
		$timptimbre = $this->input->post('timptimbre');
		$ttotal     = $this->input->post('ttotal');
		$ttotal2    = $this->input->post('ttotal2');
		
			
		$subtotal=$ivaa=$ivag=$ivar=$total=$exento=$total2=0;$error='';
		for($i=0;$i < $do->count_rel('itfac');$i++){
			$subtotal  += round($do->get_rel('itfac','subtotal'    ,$i),2);
			$ivaa      += round($do->get_rel('itfac','ivaa'        ,$i),2);
			$ivag      += round($do->get_rel('itfac','ivag'        ,$i),2);
			$ivar      += round($do->get_rel('itfac','ivar'        ,$i),2);
			$exento    += round($do->get_rel('itfac','exento'      ,$i),2);
			$total     += round($do->get_rel('itfac','total'       ,$i),2);
			$total2    += round($do->get_rel('itfac','total2'      ,$i),2);
			$factura    = $do->get_rel('itfac','factura'     ,$i);
			$controlfac = $do->get_rel('itfac','controlfac'  ,$i);
			$fechafac   = $do->get_rel('itfac','fechafac'    ,$i);
			$id         = $do->get_rel('itfac','id'          ,$i);
			
			$this->chexiste_factura($numero,$id,$factura,$controlfac,$cod_prov,$e);
			$error.=$e;
		}
		
		$s  = $do->get('subtotal');
		$a  = $do->get('ivaa'    );
		$g  = $do->get('ivag'    );
		$r  = $do->get('ivar'    );
		$e  = $do->get('exento'  );
		$t  = $do->get('total'   );
		$t2 = $do->get('total2'  );
	  
		if(((round((round($subtotal,2) -round($tsubtotal ,2)),2) > 0.5))|| (round(round($tsubtotal,2)-(round($subtotal ,2)),2) > 0.5) )$error.="<div class='alert'><p>La Suma de los Subtotales ($subtotal) de las facturas es diferente al subtotal ($tsubtotal) Introducido</p></div>";
		if(((round((round($ivaa    ,2) -round($trivaa    ,2)),2) > 0.5))|| (round(round($trivaa   ,2)-(round($ivaa     ,2)),2) > 0.5) )$error.="<div class='alert'><p>La Suma de los IVA Adicionales ($ivaa) de las facturas es diferente al IVA adicional ($tivaa) Introducido</p></div>";
		if(((round((round($ivar    ,2) -round($trivar    ,2)),2) > 0.5))|| (round(round($trivar   ,2)-(round($ivar     ,2)),2) > 0.5) )$error.="<div class='alert'><p>La Suma de los IVA Reducidos ($ivar) de las facturas es diferente al IVA reducido ($tivar) Introducido</p></div>";
		if(((round((round($ivag    ,2) -round($trivag    ,2)),2) > 0.5))|| (round(round($trivag   ,2)-(round($ivag     ,2)),2) > 0.5) )$error.="<div class='alert'><p>La Suma de los IVA Generales ($ivag) de las facturas es diferente al IVA general ($tivag) Introducido</p></div>";
		if(((round((round($exento  ,2) -round($texento   ,2)),2) > 0.5))|| (round(round($texento  ,2)-(round($exento   ,2)),2) > 0.5) )$error.="<div class='alert'><p>La Suma de los Exentos ($exento) de las facturas es diferente al exento ($texento) Introducido</p></div>";
		if(((round((round($total   ,2) -round($ttotal    ,2)),2) > 0.5))|| (round(round($ttotal   ,2)-(round($total    ,2)),2) > 0.5) )$error.="<div class='alert'><p>La Suma de los M. a pagar ($total) de las facturas es diferente al total ($ttotal) Introducido</p></div>";
		if(((round((round($t2      ,2) -round($total2    ,2)),2) > 0.5))||  round($total2,2)>$t2 )$error.="<div class='alert'><p>La Suma de los Totales ($total2) de las facturas es diferente al total ($t2) de la orden de pago</p></div>";
	  
		if(!empty($error)){
			logusu('itfac2',"Intento Modificar varias Factura de Orden de Pago Nro $numero cpn ERROR:$error");
			$do->error_message_ar['pre_ins']=$error;
			$do->error_message_ar['pre_upd']=$error;
			return false;
			
			//$data['content'] = $error.anchor($this->url."dataedit/show/$id",'Regresar');
			//$data['title'alert("hola"+total);]   = " Error ";
			//$data["head"]    = $this->rapyd->get_head().script('jquery.js').script("plugins/jquery.numeric.pack.js");
			//$this->load->view('view_ventanas', $data);
			//exit();
		}else{
			$do->set('subtotal'   ,$tsubtotal);
			$do->set('ivaa'       ,$trivaa);
			$do->set('ivag'       ,$trivag);
			$do->set('ivar'       ,$trivar);
			$do->set('exento'     ,$texento);
			$do->set('reteiva'    ,$treteiva);
			$do->set('reten'      ,$treten);
			$do->set('imptimbre'  ,$timptimbre);
			$do->set('total'      ,$ttotal);
		}
	}
	
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
	
	function load($numero){
		$this->rapyd->load("dataform");
		$this->load->library("path");
		$path=new Path();
		$path->setPath($this->config->item('uploads_dir'));
		$path->append('/archivos');
		$this->upload_path =$path->getPath().'/';
		
		$link=site_url('supervisor/subexls/deshacer');

		$form = new DataForm("supervisor/subexls/read/ITFAC2/$numero");
		$form->title('Cargar Archivo de Retenciones (xls)');

		$form->archivo = new uploadField("Archivo","archivo");
		$form->archivo->upload_path   = $this->upload_path;    
		$form->archivo->allowed_types = "xls";
		$form->archivo->delete_file   =false;
		$form->archivo->rule   ="required";

		$form->submit("btnsubmit","Cargar");
		$form->build_form();

		$salida=anchor($this->url."dataedit/show/$numero","Ir Atras");

		$data['content'] = $form->output.$salida;
		$data['title']   = "<h1>Cargar archivo de retenciones</h1>";
		$data["head"]    = script("jquery.pack.js").$this->rapyd->get_head();
		$this->load->view('view_ventanas', $data);
	}
	
	function instalar(){
		$query="ALTER TABLE `itfac` ADD COLUMN `uivaa`      CHAR(1)          NULL DEFAULT NULL";
		$this->db->simple_query($query);
		$query="ALTER TABLE `itfac` ADD COLUMN `uivag`      CHAR(1)          NULL DEFAULT NULL";
		$this->db->simple_query($query);
		$query="ALTER TABLE `itfac` ADD COLUMN `uivar`      CHAR(1)          NULL DEFAULT NULL";
		$this->db->simple_query($query);
		$query="ALTER TABLE `itfac` ADD COLUMN `ureten`     CHAR(1)          NULL DEFAULT NULL";
		$this->db->simple_query($query);
		$query="ALTER TABLE `itfac` ADD COLUMN `uimptimbre` CHAR(1)          NULL DEFAULT NULL";
		$this->db->simple_query($query);
		$query="ALTER TABLE `itfac` ADD COLUMN `preteiva_prov` DECIMAL(19,2) NULL DEFAULT NULL";
		$this->db->simple_query($query);
		$query="ALTER TABLE `ocompra` 	ADD COLUMN `concepto` TEXT NULL DEFAULT NULL AFTER `nocompra`";
		$this->db->simple_query($query);
	}
}
?>
