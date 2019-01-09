<?php
require_once(BASEPATH.'application/controllers/presupuesto/common.php');
class odirectmulti extends Common {

var $titp  = 'Ordenes de Pago Directo';
var $tits  = 'Orden de Pago Directo';
var $url   = 'presupuesto/odirectmulti/';

function odirectmulti(){
	parent::Controller();
	$this->load->library("rapyd");
	$this->formatopres =$this->datasis->traevalor('FORMATOPRES');
	$this->flongpres   =strlen(trim($this->formatopres));
	//$this->datasis->modulo_id(119,1);
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
						'titulo'  =>'Buscar Proveedor');
		
		$bSPRV=$this->datasis->p_modbus($mSPRV,"proveed");
		
		
		$filter = new DataFilter2("Filtro de $this->titp","odirect");
		$filter->db->where('status !=','F2');
		$filter->db->where('status !=','F3');
		$filter->db->where('status !=','F1');
		
		$filter->numero = new inputField("N&uacute;mero", "numero");
		$filter->numero->size=12;
		$filter->numero->clause="likerigth";
		
		$filter->tipo = new dropdownField("Orden de ", "tipo");
		$filter->tipo->option("","");
		$filter->tipo->option("Compra"  ,"Compra");
		$filter->tipo->option("Servicio","Servicio");
		$filter->tipo->style="width:100px;";
		
		$filter->fecha = new dateonlyField("Fecha", "fecha");
		$filter->fecha->size=12;
		
		$filter->uejecutora = new inputField("Unidad Ejecutora", "uejecutora");
		$filter->uejecutora->size=12;
		
		$filter->cod_prov = new inputField("Proveedor", 'cod_prov');
		$filter->cod_prov->size = 6;
		$filter->cod_prov->append($bSPRV);
		$filter->cod_prov->rule = "required";
		
		$filter->observa = new inputField("Observacion", "observa");
		$filter->observa->size=60;
		
		$filter->total2 = new inputField("Monto", "total2");
		$filter->total2->size=60;
		
		$filter->status = new dropdownField("Estado","status");		
		$filter->status->option("","");
		$filter->status->option("B2","Actualizado");
		$filter->status->option("B1","Sin Actualizar");		
		$filter->status->option("B3","Pagado");
		$filter->status->style="width:150px";
		
		$filter->buttons("reset","search");
		
		$filter->build();
		$uri = anchor($this->url.'dataedit/show/<#numero#>','<str_pad><#numero#>|8|0|STR_PAD_LEFT</str_pad>');
		
	function sta($status){
			switch($status){
				case "B1":return "Sin Actualizar";break;
				case "B2":return "Actualizado";break;
				case "B3":return "Pagado";break;
				//case "O":return "Ordenado Pago";break;
				//case "E":return "Pagado";break;
				case "A":return "Anulado";break;
			}
		}
		
		$grid = new DataGrid("Lista de ".$this->titp);
		$grid->order_by("numero","desc");
		$grid->per_page = 20;
		$grid->use_function('substr','str_pad','sta');
		
		$grid->column("N&uacute;mero"    ,$uri);
		$grid->column("Tipo"             ,"tipo"                                        ,"align='center'");
		$grid->column("Fecha"            ,"<dbdate_to_human><#fecha#></dbdate_to_human>","align='center'");
		$grid->column("Unidad Ejecutora" ,"uejecutora");
		$grid->column("Proveedor"        ,"cod_prov");
		$grid->column("Observacion"      ,"observa");
		$grid->column("Pago"             ,"<number_format><#total2#>|2|,|.</number_format>","align='right'");
		$grid->column("Estado"          ,"<sta><#status#></sta>"                       ,"align='center'");
		
		//echo $grid->db->last_query();
		$grid->add($this->url."dataedit/create");
		$grid->build();
		
		$data['content'] = $filter->output.$grid->output;
		$data['title']   = " $this->titp ";
		$data["head"]    = $this->rapyd->get_head();
		$this->load->view('view_ventanas', $data);
	}

	function dataedit(){
		//$this->datasis->modulo_id(119,1);
		$this->rapyd->load('dataobject','datadetails');
		
		$mSPRV=array(
			'tabla'   =>'sprv',
			'columnas'=>array(
			'proveed' =>'C&oacute;odigo',
			'nombre'=>'Nombre',
			'rif'=>'Rif',
			'contacto'=>'Contacto'),
			'filtro'  =>array('proveed'=>'C&oacute;digo','nombre'=>'Nombre','rif'=>'Rif'),
			'p_uri'=>array(4=>'<#i#>',),
			'retornar'=>array('proveed'=>'codprov_<#i#>','nombre'=>'nombrep','reteiva'=>'reteiva_prov'),
			
			//'retornar'=>'ca_total',
			'titulo'  =>'Buscar Proveedor');
		
		$bSPRV =$this->datasis->p_modbus($mSPRV ,"<#i#>");
		
		$bSPRV2=$this->datasis->p_modbus($mSPRV ,"sprv");
		
		$modbus=array(
			'tabla'   =>'v_presaldo',
			'columnas'=>array(
				'codigo'      =>'C&oacute;digo',
				'ordinal'     =>'Ord',
				'denominacion'=>'Denominaci&oacute;n',
				'saldo'       =>'Saldo'),
			'filtro'  =>array('codigo' =>'C&oacute;digo','denominacion'=>'Denominaci&oacute;n'),
			'retornar'=>array('codigo'=>'partida_<#i#>','ordinal'=>'ordinal_<#i#>'),//,'denominacion'=>'denomi_<#i#>'
			'p_uri'=>array(4=>'<#i#>',5=>'<#fondo#>',6=>'<#estadmin#>',),
			'where'=>'fondo = <#fondo#> AND codigoadm = <#estadmin#> AND movimiento = "S" AND saldo > 0',
			'titulo'  =>'Busqueda de partidas');
		$btn=$this->datasis->p_modbus($modbus,'<#i#>/<#fondo#>/<#estadmin#>');
		
		$mMBANC=array(
		'tabla'    =>'mbanc',
		'columnas' =>array(
			'id'     =>'C&oacute;odigo',
			'codbanc'=>'Banco',
			'monto'  =>'Monto'),
		'filtro'  =>array(
			'id'     =>'C&oacute;odigo',
			'codbanc'=>'Banco',
			'monto'  =>'Monto'),
		'retornar'=>array(
			'id' =>'mbanc',),
		'where'=>'tipo = "C"',
		'titulo'  =>'Buscar Anticipos de Gastos');
		
		$bMBANC=$this->datasis->p_modbus($mMBANC,"mbanc");
		
		$do = new DataObject("odirect");
		$do->pointer('sprv' ,'sprv.proveed = odirect.cod_prov','sprv.nombre as nombrep','LEFT');
		$do->rel_one_to_many('itodirect', 'itodirect', array('numero'=>'numero'));
		$do->rel_one_to_many('itfac', 'itfac', array('numero'=>'numero'));
		
		$edit = new DataDetails($this->tits, $do);
		$edit->back_url = site_url($this->url."filteredgrid/index");
		$edit->set_rel_title('itodirect','Rubro <#o#>');
		
		$edit->pre_process('update'  ,'_valida');
		$edit->pre_process('insert'  ,'_valida');
		$edit->post_process('insert','_post_insert');
		$edit->post_process('update','_post_update');
		$edit->post_process('delete','_post_delete');
		
		$edit->numero  = new inputField("N&uacute;mero", "numero");
		$edit->numero->mode="autohide";
		$edit->numero->when=array('show');
		
		$edit->tipo = new dropdownField("Orden de ", "tipo");
		$edit->tipo->option("Compra"  ,"Compra");
		$edit->tipo->option("Servicio","Servicio");
		$edit->tipo->option("T","Transferencia");
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
		
		$edit->estadmin = new dropdownField("Estructura Administrativa","estadmin");
		$edit->estadmin->option("","Seleccione");
		$edit->estadmin->rule='required';
		$edit->estadmin->options("SELECT b.codigo, CONCAT_WS(' ',b.codigo,b.denominacion) AS val FROM presupuesto AS a JOIN estruadm AS b ON a.codigoadm=b.codigo  GROUP BY b.codigo");
		$edit->estadmin->style="width:200px";
		
		$edit->fondo = new dropdownField("Fondo", "fondo");
		$edit->fondo->rule = "required";
		$edit->fondo->style = "width:220px";
		$estadmin=$edit->getval('estadmin');
		if($estadmin!==false)
			$edit->fondo->options("SELECT tipo,tipo a  FROM presupuesto WHERE codigoadm='$estadmin' GROUP BY tipo");
		else
			$edit->fondo->option("","Seleccione Estructura Administrativa");
		
		
		$edit->codprov_sprv = new inputField("Proveedor", 'codprov_sprv');
		$edit->codprov_sprv->db_name  = "cod_prov";
		$edit->codprov_sprv->size     = 4;
		$edit->codprov_sprv->append($bSPRV2);
		
		$edit->nombrep = new inputField("Nombre", 'nombrep');
		$edit->nombrep->size = 20;
		$edit->nombrep->readonly = true;
		$edit->nombrep->pointer = true;
		
		$edit->reteiva_prov  = new inputField("reteiva_prov", "reteiva_prov");
		$edit->reteiva_prov->size=1;
		$edit->reteiva_prov->when=array('modify','create');
		
		$edit->observa = new textAreaField("Observaciones", 'observa');
		$edit->observa->cols = 70;
		$edit->observa->rows = 3;
		
		$edit->fechafac = new  dateonlyField("Fecha de Factura",  "fechafac");
		$edit->fechafac->insertValue = date('Y-m-d');
		$edit->fechafac->size =12;
		
		$edit->simptimbre = new checkboxField("1X1000", "simptimbre", "S","N");   
		$edit->simptimbre->insertValue = "N";
		$edit->simptimbre->onchange ='cal_timbre();';
		
		$edit->simpmunicipal = new checkboxField("I.Municipal", "simpmunicipal", "S","N");   
		$edit->simpmunicipal->insertValue = "N";
		$edit->simpmunicipal->onchange ='cal_municipal();';
		
		$edit->imptimbre= new inputField("Impuesto 1X1000", 'imptimbre');
		$edit->imptimbre->size = 8;
		$edit->imptimbre->css_class='inputnum';
		
		$edit->total= new inputField("Monto a Pagar", 'total');
		$edit->total->size = 8;
		$edit->total->css_class='inputnum';
		
		$edit->retenomina= new inputField("Deducciones Nomina", 'retenomina');
		$edit->retenomina->size = 8;
		$edit->retenomina->css_class='inputnum';
		$edit->retenomina->onchange ='cal_total();';
		$edit->retenomina->value = 0;
		
		$edit->impmunicipal= new inputField("Impuesto Municipal", 'impmunicipal');
		$edit->impmunicipal->size = 8;
		$edit->impmunicipal->css_class='inputnum';
		
		$edit->subtotal = new inputField("Sub Total", 'subtotal');
		$edit->subtotal->css_class='inputnum';
		$edit->subtotal->size = 5;
		$edit->subtotal->readonly=true;
		
		$edit->iva = new inputField("IVA", 'iva');
		$edit->iva->css_class='inputnum';
		$edit->iva->size = 8;
		$edit->iva->readonly=true;
		
		$edit->ivaa = new inputField("IVA Adicional", 'ivaa');
		$edit->ivaa->css_class='inputnum';
		$edit->ivaa->size = 8;
		
		$edit->ivag = new inputField("IVA General", 'ivag');
		$edit->ivag->css_class='inputnum';
		$edit->ivag->size = 8;
		
		$edit->ivar = new inputField("IVA Reducido", 'ivar');
		$edit->ivar->css_class='inputnum';
		$edit->ivar->size = 8;
		
		$edit->exento = new inputField("Exento", 'exento');
		$edit->exento->css_class='inputnum';
		$edit->exento->size = 8;
		
		$edit->reteiva = new inputField("Retencion IVA", 'reteiva');
		$edit->reteiva->css_class='inputnum';
		$edit->reteiva->size = 8;
		
		$edit->creten = new dropdownField("Codigo ISLR","creten");
		$edit->creten->option("","");
		$edit->creten->options("SELECT codigo,CONCAT_WS(' ',codigo,activida) FROM rete ORDER BY codigo");
		$edit->creten->style="width:150px;";
		$edit->creten->onchange ='cal_islr();';
		
		$edit->reten = new inputField("Retenci&oacute;n ISLR", 'reten');
		$edit->reten->css_class='inputnum';
		$edit->reten->size = 8;
		
		$edit->total2 = new inputField("Total", 'total2');
		$edit->total2->css_class='inputnum';
		$edit->total2->size = 8;
		
		$edit->multiple = new autoupdateField('S', 'multiple');
		
		$edit->itpartida = new inputField("(<#o#>) Partida", "partida_<#i#>");
		$edit->itpartida->rule='callback_itpartida';
		$edit->itpartida->size=12;
		$edit->itpartida->append('<img src="/tortuga/assets/default/images/system-search.png"  alt="Busqueda de partidas" title="Busqueda de partidas" border="0" onclick="modbusdepen(<#i#>)"/>');
		$edit->itpartida->db_name='partida';
		$edit->itpartida->rel_id ='itodirect';
		
		$edit->itordinal = new inputField("(<#o#>) Ordinal", "ordinal_<#i#>");
		$edit->itordinal->db_name  ='ordinal';
		$edit->itordinal->maxlength=3;
		$edit->itordinal->size     =5;		
		$edit->itordinal->rel_id   ='itodirect';
		
		$edit->itdescripcion = new inputField("(<#o#>) Descripci&oacute;n", "descripcion_<#i#>");
		$edit->itdescripcion->db_name  ='descripcion';
		$edit->itdescripcion->maxlength=80;
		$edit->itdescripcion->size     =15;
		$edit->itdescripcion->rel_id   ='itodirect';
		
		$edit->itunidad = new dropdownField("(<#o#>) Unidad", "unidad_<#i#>");		
		$edit->itunidad->db_name= 'unidad';
		$edit->itunidad->rel_id = 'itodirect';
		$edit->itunidad->options("SELECT unidades AS id,unidades FROM unidad ORDER BY unidades");
		$edit->itunidad->style="width:80px";
		
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
		
		$edit->itiva = new dropdownField("(<#o#>) IVA", "iva_<#i#>");
		$edit->itiva->db_name  = 'iva';
		$edit->itiva->rel_id   = 'itodirect';
		$edit->itiva->onchange = 'cal_importe(<#i#>);';
		$edit->itiva->options($this->_ivaplica());
		$edit->itiva->option("0"  ,"Excento");
		$edit->itiva->style    = "width:80px";
		
		$edit->itimporte = new inputField("(<#o#>) Importe", "importe_<#i#>");
		$edit->itimporte->css_class='inputnum';
		$edit->itimporte->db_name  ='importe';
		$edit->itimporte->rel_id   ='itodirect';
		$edit->itimporte->rule     ='numeric';
		$edit->itimporte->readonly =true;
		$edit->itimporte->size     =8;
		
		//relacion itfac
		$status=$edit->get_from_dataobjetct('status');
		
		$edit->tivag = new inputField("","tivag");
		$edit->tivag->mode = "autohide";
		$edit->tivag->size =5;
		
		$edit->tivar = new inputField("","tivar");
		$edit->tivar->mode = "autohide";
		$edit->tivar->size =5;
		
		$edit->tivaa = new inputField("","tivaa");
		$edit->tivaa->mode = "autohide";
		$edit->tivaa->size =5;
		
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
		
		$edit->itfactura = new inputField("(<#o#>) Factura", "factura_<#i#>");
		$edit->itfactura->size=10;
		$edit->itfactura->db_name='factura';
		$edit->itfactura->rel_id ='itfac';
		$edit->itfactura->rule ='required';
		
		$edit->itcontrolfac = new inputField("(<#o#>) Control Fiscal", "controlfac_<#i#>");
		$edit->itcontrolfac->db_name  ='controlfac';
		$edit->itcontrolfac->size     =10;		
		$edit->itcontrolfac->rel_id   ='itfac';
		$edit->itcontrolfac->rule   ='required';
		
		$edit->itfechafac = new dateonlyField("(<#o#>) Fecha Factura", "fechafac_<#i#>");
		$edit->itfechafac->db_name  ='fechafac';
		$edit->itfechafac->insertValue = date('Y-m-d');
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
		if($status=="B3")$edit->itexento->mode = "autohide";
		
		$edit->itivag = new inputField("(<#o#>) % IVA General", "ivag_<#i#>");
		$edit->itivag->size=8;		
		$edit->itivag->db_name= 'ivag';
		$edit->itivag->rel_id = 'itfac';
		$edit->itivag->onchange ='cal_itivag(<#i#>);';
		$edit->itivag->css_class = "inputnum";
		if($status=="B3")$edit->itivag->mode = "autohide";
		
		$edit->itivar = new inputField("(<#o#>) % IVA Reducido", "ivar_<#i#>");
		$edit->itivar->size=8;		
		$edit->itivar->db_name= 'ivar';
		$edit->itivar->rel_id = 'itfac';
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
		
		//fin relacion itfac
		
		if($status=='B1'){
			$action = "javascript:window.location='" .site_url($this->url.'actualizar/'.$edit->rapyd->uri->get_edited_id()). "'";
			$edit->button_status("btn_status",'Actualizar',$action,"TR","show");
			$edit->buttons("modify","save");
		}elseif($status=='B2'){
			$action = "javascript:window.location='" .site_url('presupuesto/common/pd_anular/'.$edit->rapyd->uri->get_edited_id()). "'";
			if($this->datasis->puede('1015'))$edit->button_status("btn_anular",'Anular',$action,"TR","show");	
		}elseif($status=='B3'){
			$multiple=$edit->get_from_dataobjetct('multiple');
			if($multiple=="N"){
				$action = "javascript:window.location='" .site_url($this->url.'camfac/dataedit/modify/'.$edit->rapyd->uri->get_edited_id()). "'";
				$edit->button_status("btn_camfac",'Modificar Factura',$action,"TR","show");
			}
		}else{
			$edit->buttons("save");
		}
		$edit->buttons("undo","back","add_rel");
		$edit->build();
		
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
		$conten['status']=$status;
		$conten['ivar']=$ivaplica['redutasa'];
		$conten['ivag']=$ivaplica['tasa'];
		$conten['ivaa']=$ivaplica['sobretasa'];
		$conten['imptimbre']=$this->datasis->traevalor('IMPTIMBRE');
		$conten['impmunicipal']=$this->datasis->traevalor('IMPMUNICIPAL');
		
		$smenu['link']=barra_menu('119');
		$data['smenu'] = $this->load->view('view_sub_menu', $smenu,true);
		$conten["form"]  =&  $edit;
		$data['content'] = $this->load->view('view_odirectmulti', $conten,true);
		$data['title']   = " $this->tits ";
		$data["head"]    = $this->rapyd->get_head().script('jquery.js').script("plugins/jquery.numeric.pack.js").script("plugins/jquery.json.min.js");
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

	function itpartida($partida){
		$estadmin = $this->db->escape($this->input->post('estadmin'));
		$fondo    = $this->db->escape($this->input->post('fondo'));
		$partida    = $this->db->escape($partida);
		$partidaiva = $this->datasis->traevalor("PARTIDAIVA");
		$cana=$this->datasis->dameval("SELECT COUNT(*) FROM presupuesto WHERE (asignacion+aumento-disminucion+(traslados))>0 AND codigoadm=$estadmin AND codigopres=$partida AND tipo=$fondo  ");
		if($cana > 0){
			return true;
		}else{
			$this->validation->set_message('itpartida',"La partida %s ($partida) No pertenece al la estructura administrativa o al fondo seleccionado");
			return false;
		}
	}

	function actualizar($id){	
	
		$this->rapyd->load('dataobject');
	
		$ord = new DataObject("ordinal");
		
		$do = new DataObject("odirect");
		$do->rel_one_to_many('itodirect', 'itodirect', array('numero'=>'numero'));
		$do->load($id);
		
		$error      = "";
		$codigoadm  = $do->get('estadmin');
		$fondo      = $do->get('fondo'   );
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
		
		
		
		$presup = new DataObject("presupuesto");
		$pk=array('codigoadm'=>$codigoadm,'tipo'=>$fondo);
		
		$partidaiva=$this->datasis->traevalor("PARTIDAIVA");
		$pk['codigopres'] = $partidaiva;
		$presup->load($pk);
		
		$asignacion   =$presup->get("asignacion");
		$pasignacion   =$presup->get("asignacion");		
		$aumento      =$presup->get("aumento");
		$disminucion  =$presup->get("disminucion");
		$traslados    =$presup->get("traslados");
		$comprometido =$presup->get("comprometido");
		
		//print_r($presup->get_all());
		//
		//echo $pasignacion;
		//exit('assasa');
		if($pasignacion >0){
			$disp=(($asignacion+$aumento-$disminucion)+($traslados))-$comprometido;
			
			$ivaa    =  $do->get('ivaa');
			$ivag    =  $do->get('ivag');
			$ivar    =  $do->get('ivar');
			$iva     =  $do->get('iva');
			$reteiva =  $do->get('reteiva');
			$ivan    =  $ivag+$ivar+$ivaa+$iva;
		
			if($ivan > $disp)
				$error.="<div class='alert'><p>El monto de iva ($ivan) de la orden de pago directa, es mayor al monto disponible ($disp) para la partida de iva ($partidaiva)</p></div>";
		}
		if(empty($error)){

			$sta=$do->get('status');
			if($sta=="B1"){
				for($i=0;$i  < $do->count_rel('itodirect');$i++){
				
					$codigopres  = $do->get_rel('itodirect','partida',$i);
					$piva        = $do->get_rel('itodirect','iva'    ,$i);
					$importe     = $do->get_rel('itodirect','importe',$i);
					$ordinal     = $do->get_rel('itodirect','ordinal',$i);
					
					if($pasignacion>0)
						$mont        = $importe;
					else
						$mont        = $importe+($importe*$piva/100);
						
					//echo $mont;
					//exit();
					
					$pk['codigopres'] = $codigopres;
					
					$presup->load($pk);
					$asignacion   =$presup->get("asignacion");
					$aumento      =$presup->get("aumento");
					$disminucion  =$presup->get("disminucion");
					$traslados    =$presup->get("traslados");
					$comprometido =$presup->get("comprometido");
					
					$disponible=(($asignacion+$aumento-$disminucion)+($traslados))-$comprometido;
					//if($mont > $disponible){
					//	$error.="<div class='alert'><p>No se Puede Completar la Transaccion debido a que el monto de la $this->tits ($mont) es mayor al monto disponible($disponible) para la partida: $codigopres</p></div>";
					//}
					
					if(!empty($ordinal)){
					//echo "codigoadm:".$codigoadm." fondo:".$fondo." codigopres:".$codigopres." ordinal:".$ordinal."</br>";
						
						$ord->load(array("codigoadm"=>$codigoadm,"fondo"=>$fondo,"codigopres"=>$codigopres,"ordinal"=>$ordinal));
						//print_r($ord->get_all());
						
						$asignacion   =$ord->get("asignacion"  );
						$aumento      =$ord->get("aumento"     );
						$disminucion  =$ord->get("disminucion" );
						$traslados    =$ord->get("traslados"   );
						$comprometido =$ord->get("comprometido");
						$disponible=(($asignacion+$aumento-$disminucion)+($traslados))-$comprometido;
						if($mont > $disponible){
							$error.="<div class='alert'><p>No se Puede Completar la Transaccion debido a que el monto ($mont) de la orden de pago directo ($id)   es mayor al monto disponible($disponible) para la partida: $codigopres y el ordinal ($ordinal)</p></div>";
						}			
					}	
				}
				
				if(empty($error)){
					
					for($i=0;$i  < $do->count_rel('itodirect');$i++){
					
						$codigopres  = $do->get_rel('itodirect','partida',$i);
						$piva        = $do->get_rel('itodirect','iva'    ,$i);
						$importe     = $do->get_rel('itodirect','importe',$i);
						$ordinal     = $do->get_rel('itodirect','ordinal',$i);
					
						if($pasignacion>0)
							$mont   = $importe;
						else
							$mont  = $importe+($importe*$piva/100);
							
					
						//exit($mont);
						$pk['codigopres'] = $codigopres;
						
						$presup->load($pk);
						$comprometido=$presup->get("comprometido");
						$causado     =$presup->get("causado"     );
						$opago       =$presup->get("opago"       );
						
						$comprometido+=$mont;
						$causado     +=$mont;
						$opago       +=$mont;
						
						$presup->set("comprometido",$comprometido);
						$presup->set("causado"     ,$causado     );
						$presup->set("opago"       ,$opago       );
						
						$presup->save();
						
						if(!empty($ordinal)){
						
								$ord->load(array("codigoadm"=>$codigoadm,"fondo"=>$fondo,"codigopres"=>$codigopres,"ordinal"=>$ordinal));
								
								$compr  = $ord->get("comprometido");
								$cau    =$ord->get("causado"     );
								$opa    =$ord->get("opago"       );						
								$compr += $mont;
								$cau   += $mont;
								$opa   += $mont;
								$ord->set("comprometido",$compr   );
								$ord->set("causado"     ,$cau     );
								$ord->set("opago"       ,$opa       );
								
								$ord->save();
							}
					}
					
					if($pasignacion >0){
						$pk['codigopres'] = $partidaiva;
						$presup->load($pk);
						
						$comprometido=$presup->get("comprometido");
						$causado     =$presup->get("causado"     );
						$opago       =$presup->get("opago"       );	
						
						$comprometido+=$ivan-$reteiva;
						$causado     +=$ivan-$reteiva;
						$opago       +=$ivan-$reteiva;
						
						$presup->set("comprometido",$comprometido);
						$presup->set("causado"     ,$causado     );
						$presup->set("opago"       ,$opago       );
						//echo "aca";
						$presup->save();
						
					}
					$do->set('status','B2');
					$do->save();
				}
			}
		}
		
		$this->sp_presucalc($codigoadm);
		
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
		$fondo        = $do->get('fondo'       );
		$tipo         = $do->get('tipo'        );
		$estadmin     = $do->get('estadmin'    );
		$factura      = $do->get('factura'     );
		$controlfac   = $do->get('controlfac'  );
		$fechafac     = $do->get('fechafac'    );
		$multiple     = $do->get('multiple'    );
		$numero       = $do->get('numero'      );
		
		if($multiple=="S"){
			$do->set('controlfac','');	
		  $do->set('factura'   ,'');
		  $do->set('fechafac'  ,'');
		
		}
		if($tipo == 'Compra'){
			$do->set('creten','');
			$do->set('reten' ,0);
		}
		
		$rete['tari1'] = 0;
		$rete=$this->datasis->damerow("SELECT base1,tari1,pama1 FROM rete WHERE codigo='$creten'");
		if($reteiva_prov!=75)$reteiva_prov=100;
		
		
		
		
		$presup = new DataObject("presupuesto");
		//$do->rel_one_to_many('ordinal', 'ordinal', array('codigopres'=>'ppla'));
		$pk=array('codigoadm'=>$estadmin,'tipo'=>$fondo);
		
		$partidaiva=$this->datasis->traevalor("PARTIDAIVA");
		$pk['codigopres'] = $partidaiva;
		$presup->load($pk);
		
		$asignacion    = $presup->get("asignacion");
		$pasignacion   = $presup->get("asignacion");
		
		
		$giva=$aiva=$riva=$exento=$reteiva=$subtotal=$subtotal2=$tiva=$mivag=$mivar=$mivaa=$tivag=$tivar=$tivaa=0;
		for($i=0;$i < $do->count_rel('itodirect');$i++){			
			$cantidad   = $do->get_rel('itodirect'  ,'cantidad'     ,$i);
			$precio     = $do->get_rel('itodirect'  ,'precio'       ,$i);
			$piva       = $do->get_rel('itodirect'  ,'iva'          ,$i);
			$partida    = $do->get_rel('itodirect'  ,'partida'      ,$i);
			$codprov    = $do->get_rel('itodirect'  ,'codprov'      ,$i);
			$ordinal    = $do->get_rel('itodirect'  ,'ordinal'      ,$i);
			
			$importe    = $precio * $cantidad;
			
				$subtotal  += $importe;
				if($piva==$rr['tasa']     ){
					$giva+=($rr['tasa']     *$importe)/100;
					$mivag+=$importe;
				}
				if($piva==$rr['redutasa'] ){
					$riva+=($rr['redutasa'] *$importe)/100;
					$mivar+=$importe;
				}
				if($piva==$rr['sobretasa']){
					$aiva+=($rr['sobretasa']*$importe)/100;
					$mivaa+=$importe;
				}
				
				if($piva==0)$exento+=$importe;
				
				$do->set_rel('itodirect','importe' ,$importe,$i);
				
				if($rete && $tipo == 'Servicio'){
					if(substr($creten,0,1)=='1')$reten=round($importe*$rete['base1']*$rete['tari1']/10000,2);
					else $reten=round(($importe-$rete['pama1'])*$rete['base1']*$rete['tari1']/10000,2);		
					if($reten < 0)$reten=0;
					$do->set_rel('itodirect','preten'  , $reten ,$i);
				}
			//}
			if(!empty($ordinal)){
			
				$cana=$this->datasis->dameval("SELECT COUNT(*) FROM presupuesto a JOIN ordinal c ON (((`c`.`codigoadm` = `a`.`codigoadm`) and (`a`.`tipo` = `c`.`fondo`) and (`a`.`codigopres` = `c`.`codigopres`))) WHERE (a.asignacion+a.aumento-a.disminucion+(a.traslados))>0 AND a.codigoadm='$estadmin' AND a.codigopres='$partida' AND a.tipo='$fondo' AND c.ordinal = '$ordinal'  ");
				if($cana<=0){
					
					$error = ("El Ordinal ($ordinal) No pertenece a la partida seleccionada ($partida)");
					$do->error_message_ar['pre_upd']=$error;
					$do->error_message_ar['pre_ins']=$error;				
					return false;
				}
			}
		}
		
		$reten = 0;
		if(!empty($cod_prov)){
			$reteiva=(($giva+$riva+$aiva)*$reteiva_prov)/100;
			if($rete){
				if(substr($creten,0,1)=='1')$reten=round($subtotal*$rete['base1']*$rete['tari1']/10000,2);
				else $reten=round(($subtotal-$rete['pama1'])*$rete['base1']*$rete['tari1']/10000,2);		
				if($reten < 0)$reten=0;
				$do->set('reten'     ,    $reten     );
			}
		}else{
			$reteiva=0;
		}
		
		$total2=$giva+$riva+$aiva+$subtotal;
		$total =$total2-$reteiva-$reten;
		
		$impm=$impt=0;
		if($do->get('simptimbre')=='S')
			$total       -=$impt=($subtotal /$this->datasis->traevalor('IMPTIMBRE'));
		
		if($do->get('simpmunicipal')=='S')
			$total       -=$impm= ($subtotal * $this->datasis->traevalor('IMPMUNICIPAL')/100);
			
		if($reteiva>0 && $multiple=="N" && (empty($fechafac) || empty($factura) || empty($controlfac))){
			$error.="<div class='alert'><p>Los Campos Factura, Control Fiscal y fecha factura no pueden estar en blanco</p></div>";
			if(strlen($numero) > 0){
				$this->chexiste_factura($numero,$factura,$controlfac,$cod_prov,$e);
				$error.=$e;		
			}
		}
		
		$do->set('impmunicipal'  , $impm                );
		$do->set('imptimbre'     , $impt                );	
		$do->set('iva'           , $tiva                        );
		$do->set('ivag'          , $giva                        );
		$do->set('ivar'          , $riva                        );
		$do->set('ivaa'          , $aiva                        );
		$do->set('tivag'         , $rr['tasa']                  );
		$do->set('tivar'         , $rr['redutasa']              );
		$do->set('tivaa'         , $rr['sobretasa']             );
		$do->set('mivag'         , $mivag                       );
		$do->set('mivar'         , $mivar                       );
		$do->set('mivaa'         , $mivaa                       );
		$do->set('subtotal'      , $subtotal                    );
		$do->set('exento'        , $exento                      );
		$do->set('reteiva'       , $reteiva                     );
		$do->set('total'         , $total                       );
		$do->set('total2'        , $total2                      );
		$do->set('status'        , 'B1'                         );
		if($reten>0)
		$do->set('breten'        , $rete['tari1']               );
		
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

	function sp_presucalc($codigoadm){
		//$this->db->simple_query("CALL sp_presucalc($codigoadm)");
		return true;
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
	  
	  $this->chexiste_factura($numero,$factura,$controlfac,$cod_prov,$e);
	  $error=$e;
	  
	  $riva = new DataObject("riva");
	  $riva->load_where('odirect',$numero);
	  
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
	
	function chexiste_factura($numero,$factura,$controlfac,$codprov_sprv,&$error){
		$controlfac = $this->db->escape($controlfac   );
		$cod_prov   = $this->db->escape($codprov_sprv );
		$factura    = $this->db->escape($factura      );
		
		$query = "SELECT SUM(a) FROM (
			SELECT COUNT(*)a FROM odirect WHERE (controlfac =$controlfac OR factura=$factura) AND cod_prov=$cod_prov AND numero<>$numero
			UNION ALL
			SELECT COUNT(*) FROM ocompra WHERE (controlfac =$controlfac OR factura=$factura) AND cod_prov=$cod_prov
			UNION ALL
			SELECT COUNT(*) FROM itfac JOIN odirect ON odirect.numero = itfac.numero WHERE (itfac.controlfac =$controlfac OR itfac.factura=$factura) AND cod_prov=$cod_prov AND odirect.numero<>$numero
			UNION ALL
			SELECT COUNT(*) FROM itrendi WHERE (controlfac =$controlfac OR numfac=$factura) AND cod_prov=$cod_prov
			)a";
		
		$cana=$this->datasis->dameval($query);
		
		if($cana>0){
			$nombre = $this->datasis->dameval("SELECT nombre FROM sprv WHERE proveed=$cod_prov");
			$error="La Factura o el Control Fiscal Ya existen para el Proveedor ($cod_prov) $nombre ";
		}		
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
	
	function _post_insert($do){
		$numero = $do->get('numero');
		logusu('odirect',"Creo Orden de Pago Directo Nro $numero");
		redirect($this->url."actualizar/$numero");
	}
	function _post_update($do){
		$numero = $do->get('numero');
		logusu('odirect'," Modifico Orden de Pago Directo Nro $numero");
		redirect($this->url."actualizar/$numero");
	}
	function _post_delete($do){
		$numero = $do->get('numero');
		logusu('odirect'," Elimino Orden de Pago Directo Nro $numero");
	}
}
?>

