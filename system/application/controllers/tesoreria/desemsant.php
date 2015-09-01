<?php
require_once(BASEPATH.'application/controllers/presupuesto/common.php');

class Desemsant extends Common {

	var $titp='Ordenes de Pago';
	var $tits='Orden de Pago';
	var $url ='tesoreria/desemsant/';

	function desemsant(){
		parent::Controller();
		$this->load->library("rapyd");
		
	}

	function index(){
		redirect($this->url."filteredgrid");
	}

	function filteredgrid(){
		$this->datasis->modulo_id(208,1);
		$this->rapyd->load("datafilter2","datagrid");
		$this->rapyd->uri->keep_persistence();
		
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
		
		
		$filter = new DataFilter2("Filtro de $this->titp","odirect");
		$filter->db->where('status !=','F1');
		$filter->db->where('status !=','F4');
		$filter->db->where('status !=','B1');
		$filter->db->where('status !=','B4');
		$filter->db->where('status !=','O1');
		$filter->db->where('status !=','O4');
		$filter->db->where('status !=','H1');
		$filter->db->where('MID(status,2,1) !=',"A");
		$filter->db->where('MID(status,2,1) !=',"X");
		
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
		
		$filter->cod_prov = new inputField("Beneficiario", 'cod_prov');
		$filter->cod_prov->size = 6;
		$filter->cod_prov->append($bSPRV);
		$filter->cod_prov->rule = "required";
		
		$filter->observa = new inputField("Observacion", "observa");
		$filter->observa->size=60;
		
		$filter->status = new dropdownField("Estado","status");		
		$filter->status->option("","");
		$filter->status->option("B2","Actualizado");
		$filter->status->option("B1","Sin Actualizar");		
		$filter->status->option("B3","Pagado");
		$filter->status->style="width:150px";
		
		$filter->buttons("reset","search");
		
		$filter->build();
		$uri = anchor($this->url.'dataedit/show/<#numero#>','<str_pad><#numero#>|8|0|STR_PAD_LEFT</str_pad>');
		
		function sta($status,$numero){
			switch(substr($status,1,1)){
				case "1":return "Sin Actualizar";break;
				case "2":return anchor("tesoreria/desemsant/add/create/$numero",'Desembolsar');break;
				case "3":return "Pagado";break;
				//case "O":return "Ordenado Pago";break;
				case "X":return "Reversado";break;
				case "A":return "Anulado";break;
			}
		}

		function action($status,$numero){
			$uri='';
			switch(substr($status,1,1)){
				case "2":$uri = anchor("tesoreria/desem/add/create/$numero",'Desembolsar');break;				
			}
			return $uri;
		}
		
		$grid = new DataGrid("Lista de ".$this->titp);
		$grid->order_by("numero","desc");
		$grid->per_page = 20;
		$grid->use_function('substr','str_pad','sta','action');
		
		$grid->column("N&uacute;mero"    ,$uri);
		$grid->column("Tipo"             ,"tipo"                                        ,"align='center'");
		$grid->column("Fecha"            ,"<dbdate_to_human><#fecha#></dbdate_to_human>","align='center'");
		$grid->column("Beneficiario"        ,"cod_prov");
		$grid->column("Observacion"      ,"observa");
		$grid->column("Pago"             ,"<number_format><#total2#>|2|,|.</number_format>","align='right'");
		$grid->column("Estado"          ,"<sta><#status#>|<#numero#></sta>"                       ,"align='center'");
		//$grid->column(" "                ,"<action><#status#>|<#numero#></action>"      ,"align='center'");
		
		//echo $grid->db->last_query();
		//$grid->add($this->url."dataedit/create");
		$grid->build();
		
		$data['content'] = $filter->output.$grid->output;
		$data['title']   = " $this->titp ";
		$data["head"]    = $this->rapyd->get_head();
		$this->load->view('view_ventanas', $data);
	}
	
	function dataedit(){
		$this->datasis->modulo_id(208,1);
		$this->rapyd->load('dataobject','datadetails');
		
		$this->rapyd->uri->keep_persistence();

		$do = new DataObject("odirect");

		$do->rel_one_to_many('pambanc', 'pambanc', array('numero'=>'pago'));		
		$do->pointer('sprv' ,'sprv.proveed=odirect.cod_prov','sprv.nombre as nombrep');
		$do->rel_pointer('pambanc','mbanc' ,'mbanc.id=pambanc.mbanc',"mbanc.tipo_doc as tipo_docp,mbanc.cheque as chequep,mbanc.fecha as fechap,mbanc.monto as montop,mbanc.observa as observap,mbanc.codbanc as codbancp,mbanc.status AS statusp");

		$edit = new DataDetails($this->tits, $do);
		$edit->back_url = site_url($this->url."filteredgrid");
		$edit->set_rel_title('pambanc','Rubro <#o#>');

		//$edit->pre_process('insert'  ,'_valida');
		//$edit->pre_process('update'  ,'_valida');
		//$edit->post_process('insert','_post_insert');
		//$edit->post_process('update','_post_update');
		//$edit->post_process('delete','_post_delete');
		
		$edit->numero  = new inputField("N&uacute;mero", "numero");
		$edit->numero->mode="autohide";
		$edit->numero->when=array('show');

		$edit->tipo = new dropdownField("Orden de ", "tipo");
		$edit->tipo->option("Compra"  ,"Compra");
		$edit->tipo->option("Servicio","Servicio");
		$edit->tipo->option("T"       ,"Transferencia");
		$edit->tipo->style="width:100px;";
	
		$edit->fecha = new  dateonlyField("Fecha",  "fecha");
		$edit->fecha->insertValue = date('Y-m-d');
		$edit->fecha->size =12;
	
		$edit->uejecutora = new dropdownField("Unidad Ejecutora", "uejecutora");
		$edit->uejecutora->option("","Seccionar");
		$edit->uejecutora->options("SELECT codigo, nombre FROM uejecutora ORDER BY nombre");
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
		if($estadmin!==false){
		$edit->fondo->options("SELECT tipo,tipo a  FROM presupuesto WHERE codigoadm='$estadmin' GROUP BY tipo");
		}else{
		$edit->fondo->option("","Seleccione Estructura Administrativa");
		}
	
		$edit->cod_prov = new inputField("Beneficiario", 'cod_prov');
		$edit->cod_prov->db_name  = "cod_prov";
		$edit->cod_prov->size     = 4;
	
		$edit->nombrep = new inputField("Nombre", 'nombrep');
		$edit->nombrep->size = 20;
		$edit->nombrep->readonly = true;
		$edit->nombrep->pointer = true;
		
		$edit->observa = new textAreaField("Observaciones", 'observa');
		$edit->observa->cols = 70;
		$edit->observa->rows = 3;

		$edit->itstatusp =  new dropdownField("(<#o#>) Banco", 'statusp_<#i#>');
		$edit->itstatusp->option("NC","Nota de Cr&eacute;dito"         );
    $edit->itstatusp->option("AN","Anulado");
    $edit->itstatusp->option("E2","Activo" );
		$edit->itstatusp->db_name   = 'statusp';
		$edit->itstatusp-> size     = 3;
		$edit->itstatusp-> readonly=true;
		$edit->itstatusp->rel_id   ='pambanc';
		$edit->itstatusp->pointer = true;
		
		$edit->itcodbancp =  new inputField("(<#o#>) Banco", 'codbancp_<#i#>');
		$edit->itcodbancp->db_name   = 'codbancp';
		$edit->itcodbancp-> size     = 3;
		$edit->itcodbancp-> readonly=true;
		$edit->itcodbancp->rel_id   ='pambanc';
		$edit->itcodbancp->pointer = true;
		       
		$edit->ittipo_docp = new dropdownField("(<#o#>) Tipo Documento","tipo_docp_<#i#>");
		$edit->ittipo_docp->db_name   = 'tipo_docp';
    $edit->ittipo_docp->option("CH","Cheque"         );
    $edit->ittipo_docp->option("NC","Nota de Credito");
    $edit->ittipo_docp->option("ND","Nota de Debito" );
    $edit->ittipo_docp->option("DP","Deposito"         );
    $edit->ittipo_docp->option("CH","Cheque"         );
    $edit->ittipo_docp->style="width:180px";
    $edit->ittipo_docp->rel_id   ='pambanc';
    $edit->ittipo_docp->pointer = true;
           
    $edit->itchequep =  new inputField("(<#o#>) Cheque", 'chequep_<#i#>');
    $edit->itchequep->db_name   ='chequep';
	  $edit->itchequep-> size  = 20;
	  $edit->itchequep->rule   = "required";//callback_chexiste_cheque|
	  $edit->itchequep->rel_id   ='pambanc';
	  $edit->itchequep->pointer = true;
		       
		$edit->itfechap = new  dateonlyField("(<#o#>) Fecha Cheque",  "fechap_<#i#>");
		$edit->itfechap->db_name   ='fechap';
		$edit->itfechap->size        =12;
		$edit->itfechap->rule        = 'required';
		$edit->itfechap->rel_id   ='pambanc';
		$edit->itfechap->pointer = true;
           
		$edit->itmontop = new inputField("(<#o#>) Total", 'montop_<#i#>');
		$edit->itmontop->db_name   ='montop';
		$edit->itmontop->mode     = 'autohide';
		$edit->itmontop->when     = array('show');
		$edit->itmontop->size = 8;
		$edit->itmontop->rel_id   ='pambanc';
		$edit->itmontop->pointer = true;
		
		$edit->itobservap = new textAreaField("(<#o#>) Observaciones", 'observap_<#i#>');
		$edit->itobservap->db_name   ='observap';
		$edit->itobservap->cols = 30;
		$edit->itobservap->rows = 3;
		$edit->itobservap->rel_id   ='pambanc';
		$edit->itobservap->pointer = true;
		
		$status   = $edit->get_from_dataobjetct('status'  );
		$numero   = $edit->get_from_dataobjetct('numero'  );
		//echo "*".$fentrega = $edit->get_from_dataobjetct('fentrega');

		if(substr($status,1,1)=='3'){	
			$mbanc= $this->datasis->damerow("SELECT id,fentrega FROM mbanc a JOIN pambanc b ON a.id=b.mbanc WHERE b.pago=".$edit->rapyd->uri->get_edited_id()." AND a.status='E2'");
			
			if(!empty($mbanc)){
				$a=$mbanc['fentrega'];
				
				$action = "javascript:window.location='" .site_url($this->url."anula/dataedit/modify/".$mbanc['id']."'");
				$edit->button_status("btn_rever",'Anular Desembolso',$action,"TR","show");
			}
		}elseif(substr($status,1,1)=='2'){
			$action = "javascript:window.location='" .site_url($this->url."add/create/".$numero)."'";
			$edit->button_status("btn_rever",'Desembolsar',$action,"TR","show");
			
			$edit->buttons("save");
		}
		
		$edit->buttons("undo","back");
		$edit->build();

		$smenu['link']   = barra_menu('208');
		$data['smenu']   = $this->load->view('view_sub_menu', $smenu,true);
		$conten["form"]  =&  $edit;
		$data['content'] = $this->load->view('view_desemsant', $conten,true);
		//$data['content'] = $edit->output;
		$data['title']   = " $this->tits ";
		$data["head"]    = $this->rapyd->get_head().script('jquery.js').script("plugins/jquery.numeric.pack.js").script("plugins/jquery.json.min.js");
		$this->load->view('view_ventanas', $data);
	}

	function add($var1='',$pago=''){
		$this->datasis->modulo_id(208,1);
		$this->rapyd->load('dataedit2','dataobject');
		
		$mBANC=array(
				'tabla'   =>'banc',
				'columnas'=>array(
					'codbanc' =>'C&oacute;odigo',
					'banco'=>'Banco',
					'saldo'=>'Saldo'),
				'filtro'  =>array(
					'codbanc' =>'C&oacute;odigo',
					'banco'=>'Banco',
					'saldo'=>'Saldo'),
				'retornar'=>array(
					'codbanc'=>'codbanc',
					'banco'=>'nombreb' ),
				'where'=>'activo = "S"',
				'titulo'  =>'Buscar Bancos');

		$bBANC=$this->datasis->p_modbus($mBANC,"banc");
		
		$script='
			$(".inputnum").numeric(".");
		';

		$do = new DataObject("mbanc");
		$do->rel_one_to_one('pambanc', 'pambanc', array('id'=>'mbanc'));
		//$do->pointer('sprv' ,'sprv.proveed = mbanc.cod_prov','sprv.nombre as nombrep','LEFT');
		//$do->pointer('bcta' ,'bcta.codigo =  mbanc.bcta','bcta.denominacion as bctad ','LEFT');
		//$do->pointer('banc' ,'banc.codbanc=mbanc.codbanc','banc.banco as nombreb','LEFT');
		//$do->pointer('odirect' ,'odirect.numero=.codbanc','banc.banco as nombreb','LEFT');
		//$do->set_rel('pambanc','pago',$pago,0);
				
		$do2 = new DataObject("odirect");
		$do2->load($pago);
		$total   = $do2->get('total');
		$observa = $do2->get('observa');
		
		$edit = new DataEdit2($this->tits, $do);

		$edit->back_url = site_url($this->url."filteredgrid");

		$edit->script($script,"create");
		$edit->script($script,"modify");

		$edit->pre_process('insert'  ,'_valida');
		$edit->pre_process('update'  ,'_valida');
		$edit->post_process('insert','_post');
		$edit->post_process('update','_post');
		//$edit->post_process('insert','_post_insert');
		//$edit->post_process('update','_post_update');
		//$edit->post_process('delete','_post_delete');

		$edit->id        = new inputField("N&uacute;mero", "id");
		$edit->id->mode  = "autohide";
		$edit->id->when  = array('show');
		
		//$edit->pago = new inputField("Beneficiario", 'cod_prov');
		//$edit->pago->size     = 6;
		//$edit->pago->rule     = "required";
		
		$edit->pago = new inputField("Orden de Pago", "pago");
		$edit->pago->db_name  ='pago';
		$edit->pago->rel_id   ='pambanc';
		$edit->pago->size     =8;
		$edit->pago->insertValue =$pago;	
		
		//$edit->cod_prov = new inputField("Beneficiario", 'cod_prov');
		//$edit->cod_prov->size     = 6;
		//$edit->cod_prov->rule     = "required";
		
		//$edit->cod_prov->readonly=true;

		//$edit->nombrep = new inputField("Nombre", 'nombrep');
		//$edit->nombrep->size      = 50;
		//$edit->nombrep->readonly  = true;
		//$edit->nombrep->pointer   = true;
		//$edit->nombrep->in        = "cod_prov";

		$edit->codbanc =  new inputField("Banco", 'codbanc');
		$edit->codbanc-> size     = 3;
		$edit->codbanc-> rule     = "required";
		$edit->codbanc-> append($bBANC);
		//$edit->codbanc-> readonly=true;
		$edit->codbanc->group    = "Transaccion";

    $edit->nombreb = new inputField("Nombre","nombreb");
    $edit->nombreb->size     = 20;
    $edit->nombreb->readonly = true;
    $edit->nombreb->pointer  = true;
    $edit->nombreb->in       = "codbanc";
    $edit->nombreb->group    = "Transaccion";
    
    $edit->tipo_doc = new dropdownField("Tipo Documento","tipo_doc");
    $edit->tipo_doc->option("CH","Cheque"         );
    //$edit->tipo_doc->option("NC","Nota de Credito");
    $edit->tipo_doc->option("ND","Nota de Debito" );
    //$edit->tipo_doc->option("DP","Deposito"         );
    $edit->tipo_doc->style="width:180px";
    $edit->tipo_doc->group  =  "Transaccion";

    $edit->cheque =  new inputField("Nro. Transacci&oacute;n", 'cheque');
	  $edit->cheque-> size  = 20;
	  $edit->cheque->rule   = "required";//callback_chexiste_cheque|
	  $edit->cheque->group    = "Transaccion";
	  
	  $edit->fecha = new  dateonlyField("Fecha Transacci&oacute;n",  "fecha");
		$edit->fecha->insertValue = date('Y-m-d');
		$edit->fecha->size    =12;
		$edit->fecha->rule    = 'required';
		$edit->fecha->group   = "Transaccion";

		$edit->monto = new inputField("Monto", 'monto');		
		$edit->monto->size     = 8;
		$edit->monto->group    = "Transaccion";
		$edit->monto->css_class='inputnum';
		$edit->monto->rule     = 'callback_positivo';
		$edit->monto->insertValue = $total;
		$edit->monto-> readonly=true;
		
		$edit->observa = new textAreaField("Concepto", 'observa');
		$edit->observa->cols = 60;
		$edit->observa->rows = 3;
		$edit->observa->group    = "Transaccion";
		$edit->observa->insertValue = $observa;
		
		//$status=$edit->_dataobject->get("status");
		//if($status=='J1'){
		//	$action = "javascript:window.location='" .site_url($this->url.'actualizar/'.$edit->rapyd->uri->get_edited_id()). "'";
		//	$edit->button_status("btn_status",'Actualizar',$action,"TR","show");
		//	$edit->buttons("modify","delete","save");
		//}elseif($status=='J2'){
		//	$action = "javascript:window.location='" .site_url($this->url.'anular/'.$edit->rapyd->uri->get_edited_id()). "'";
		//	$edit->button_status("btn_rever",'Anular',$action,"TR","show");
		//}else{
		//	$edit->buttons("save");
		//}
		
		$edit->buttons("save");

		$edit->buttons("undo", "back");
		$edit->build();

		$data['content'] = $edit->output;
    $data['title']   = " $this->tits ";
    $data["head"]    = script("jquery.pack.js").script("plugins/jquery.numeric.pack.js").script("plugins/jquery.floatnumber.js").$this->rapyd->get_head();
    $this->load->view('view_ventanas', $data);
	}
	
	function cambcheque($var1,$var2,$id){
	
		$this->datasis->modulo_id(208,1);
		$this->rapyd->load('dataedit2');
		
		$mBANC=array(
				'tabla'   =>'banc',
				'columnas'=>array(
					'codbanc' =>'C&oacute;odigo',
					'banco'=>'Banco',
					'saldo'=>'Saldo'),
				'filtro'  =>array(
					'codbanc' =>'C&oacute;odigo',
					'banco'=>'Banco',
					'saldo'=>'Saldo'),//39, 40
				'retornar'=>array(
					'codbanc'=>'codbanc','banco'=>'nombreb'
					 ),
				'where'=>'activo = "S"',
				'titulo'  =>'Buscar Bancos');

		$bBANC=$this->datasis->p_modbus($mBANC,"banc");
		
		$mBCTA=array(
				'tabla'   =>'bcta',
				'columnas'=>array(
					'codigo'       =>'C&oacute;odigo',
					'denominacion' =>'Denominacion',
					'cuenta'       =>'Cuenta'),
				'filtro'  =>array(
					'codigo'       =>'C&oacute;odigo',
					'denominacion' =>'Denominacion',
					'cuenta'       =>'Cuenta'),
				'retornar'=>array(
					'codigo'       =>'bcta',
					'denominacion' =>'bctad'),
				'titulo'  =>'Buscar Otros Ingresos');

		$bBCTA=$this->datasis->p_modbus($mBCTA,"bcta");
		
		$script='
			$(".inputnum").numeric(".");
			
			$(function() {
				$("#anulado").change(function(){
					if($("#anulado").attr("checked")==true){
						$("#tr_codbanc").show();
						$("#tr_tipo_doc").show();
						$("#tr_bcta").show();
					}else{
						$("#tr_codbanc").hide();
						$("#tr_tipo_doc").hide();
						$("#tr_bcta").hide();
					}
				});
				$(document).ready(function() {
					if($("#anulado").attr("checked")==true){
						$("#tr_codbanc").show();
						$("#tr_tipo_doc").show();
						$("#tr_bcta").show();
					}else{
						$("#tr_codbanc").hide();
						$("#tr_tipo_doc").hide();
						$("#tr_bcta").hide();
					}
				});
			});
		';
		
		$do2 = new DataObject("mbanc");
		//$do2->pointer('banc' ,'banc.codbanc=mbanc.codbanc','banc.banco as nombreb,banc.banco as nombrebt');

		$do2->load($id);
				
		$do = new DataObject("mbanc");
		$do->pointer('banc' ,'banc.codbanc=mbanc.codbanc','banc.banco as nombreb,banc.banco as nombrebt');
		$do->pointer('bcta' ,'bcta.codigo =  mbanc.bcta','bcta.denominacion as bctad ','LEFT'           );
						
		$edit = new DataEdit2("Cambiar Cheque", $do);
		
		$edit->back_url = site_url($this->url."filteredgrid/index");
				
		$edit->script($script,"create");
		$edit->script($script,"modify");
		
		$edit->pre_process('update'  ,'_validacheque');
		$edit->post_process('update' ,'_postcheque'  );
		
		$edit->codbanct =  new inputField("Banco", 'codbanct');
		$edit->codbanct->db_name = " ";
		$edit->codbanct-> size     = 5;
		$edit->codbanct->mode    = "autohide";
		$edit->codbanct-> value    = $do2->get('codbanc');
		$edit->codbanct->group   = "Datos Cheque Actual";
		              
		$edit->nombrebt = new inputField("Nombre", 'nombrebt');
		$edit->nombrebt->size      = 50;
		$edit->nombrebt->in        = "codbanct";
		$edit->nombrebt->pointer  = true;
		$edit->nombrebt->mode    = "autohide";
		$edit->nombrebt->group   = "Datos Cheque Actual";
		
		$edit->tipo_doct = new dropdownField("Tipo Documento","tipo_doct");
    $edit->tipo_doct->option("CH","Cheque"         );
    $edit->tipo_doct->option("ND","Nota de Debito" );
    $edit->tipo_doct->option("DP","Deposito"         );
    $edit->tipo_doct->style   = "width:200px";
    $edit->tipo_doct->mode    = "autohide";
		$edit->tipo_doct->group   = "Datos Cheque Actual";
		$edit->tipo_doct->value   = $do2->get('tipo_doc');
		$edit->tipo_doct->db_name = " ";
		
		$edit->chequet = new inputField("Cheque Actual Nro.", 'chequet');
		$edit->chequet->db_name = " ";
		$edit->chequet->mode    = "autohide";
		$edit->chequet->value   = $do2->get('cheque');
		$edit->chequet->group   = "Datos Cheque Actual";
		
		$edit->fechat = new  dateonlyField("Fecha Cheque",  "fechat");
		$edit->fechat->db_name = " ";		
		$edit->fechat->mode    = "autohide";
		$edit->fechat->value   = $do2->get('fecha');
		$edit->fechat->group   = "Datos Cheque Actual";
				
		$edit->montot = new inputField("Monto Nro.", 'montot');
		$edit->montot->db_name = " ";
		$edit->montot->mode    = "autohide";
		$edit->montot->value   = $do2->get('monto');
		$edit->montot->group   = "Datos Cheque Actual";
		
		$edit->cheque = new inputField("Cheque Nuevo Nro.", 'cheque');
		$edit->cheque->size      = 25;
		$edit->cheque->rule      = "required";//|callback_chexiste_cheque
		$edit->cheque->maxlength = 40;
		$edit->cheque->group     = "Datos Cheque Nuevo";		
		
		$edit->codbanc =  new inputField("Banco", 'codbanc');
		$edit->codbanc-> size     = 5;
		$edit->codbanc-> rule     = "required";
		$edit->codbanc-> append($bBANC);
		$edit->codbanc->group   = "Datos Cheque Nuevo";
		//$edit->codbanc->mode    = "autohide";
		
		$edit->tipo_doc = new dropdownField("Tipo Documento","tipo_doc");
    $edit->tipo_doc->option("CH","Cheque"         );
    $edit->tipo_doc->option("ND","Nota de Debito" );
    $edit->tipo_doc->option("DP","Deposito"         );
    $edit->tipo_doc->style   = "width:220px";
		$edit->tipo_doc->group   = "Datos Cheque Nuevo";
		//$edit->tipo_doc->mode    = "autohide";
		
		$edit->nombreb = new inputField("Nombre", 'nombreb');
		$edit->nombreb->size      = 50;
		$edit->nombreb->in        = "codbanc";
		$edit->nombreb->pointer   = true;
		$edit->nombreb->group     = "Datos Cheque Nuevo";
		//$edit->nombreb->mode    = "autohide";

		$edit->fecha = new  dateonlyField("Fecha Cheque",  "fecha");		
		//$edit->fecha->mode    = "autohide";
		$edit->fecha->group   = "Datos Cheque Nuevo";
						
		$edit->observa = new textAreaField("Observaci&oacute;nes", 'observa');
		//$edit->observa->mode    = "autohide";
		$edit->observa->rows    = 4;
		$edit->observa->cols    = 70;
		$edit->observa->group   = "Datos Cheque Nuevo";
		
		$edit->monto = new inputField("Monto", 'monto');
		$edit->monto ->mode ="autohide";
		$edit->monto ->css_class ="inputnum";
		$edit->monto->size       = 15;
		$edit->monto->group      = "Datos Cheque Nuevo";
		
		$edit->anulado = new checkboxField("Cambiar Cheque", "anulado" ,"S");   
		$edit->anulado->value = "S";
		$edit->anulado->group   = "Datos Cheque Actual";
		
		$edit->bcta = new inputField("Motivo Movimiento", 'bcta');
		$edit->bcta->size     = 6;
		//$edit->bcta->rule     = "required";
		$edit->bcta->append($bBCTA);
		$edit->bcta->readonly=true;
		//$edit->bcta->group = "Deposito";
    
		$edit->bctad = new inputField("", 'bctad');
		$edit->bctad->size        = 50;
		//$edit->bctad->group       = "Deposito";
		$edit->bctad->in          = "bcta";
		$edit->bctad->pointer     = true;
		$edit->bctad->readonly    = true;
		
		$edit->buttons("modify","save","delete","undo", "back");
		$edit->build();
		
		$data['content'] = $edit->output;
    $data['title']   = " Cambiar Cheque ";
    $data["head"]    = script("jquery.pack.js").script("plugins/jquery.numeric.pack.js").script("plugins/jquery.floatnumber.js").$this->rapyd->get_head();
    $this->load->view('view_ventanas', $data);
	}
	
	function creambanc($codbanc,$monto,$cheque,$tipo_doc,$observa,$fecha,$cod_prov,$status,$id,$bcta=''){
		$this->rapyd->load('dataobject');
		$mbanc = new DataObject("mbanc");
		$mbanc->set('codbanc' ,$codbanc );
		$mbanc->set('monto'   ,$monto   );
		$mbanc->set('cheque'  ,$cheque  );
		$mbanc->set('tipo_doc',$tipo_doc);
		$mbanc->set('observa' ,$observa );
		$mbanc->set('fecha'   ,$fecha   );
		$mbanc->set('cod_prov',$cod_prov);
		$mbanc->set('status'  ,$status  );
		$mbanc->set('bcta'    ,$bcta    );
		$mbanc->save();
		$mid  = $mbanc->get('id');
		
		$this->db->query("INSERT INTO pambanc values ((SELECT b.pago FROM mbanc a JOIN pambanc b ON a.id = b.mbanc WHERE b.mbanc=$id),$mid)"); 
	}
	
	function _validacheque($do){
		$this->rapyd->load('dataobject');
		$error = '';
		
		$cod_prov=$do->get('cod_prov');
		$codbanc =$do->get('codbanc' );
		$monto   =$do->get('monto'   );
		$cheque  =$do->get('cheque'  );
		$tipo_doc=$do->get('tipo_doc');
		$observa =$do->get('observa' );
		$id      =$do->get('id'      );
		$fecha   =$do->get('fecha'   );
		$bcta    =$do->get('bcta'    );
		$anulado =$do->get('anulado' );
		
		$mbanc = new DataObject("mbanc");
		$mbanc->load($id);
		
		$tcodbanc =$mbanc->get('codbanc' );
		$tmonto   =$mbanc->get('monto'   );
		$tcheque  =$mbanc->get('cheque'  );
		$ttipo_doc=$mbanc->get('tipo_doc');
		$tobserva =$mbanc->get('observa' );
		$tfecha   =$mbanc->get('fecha'   );	
		
		$do->set('fecha',$tfecha);
		if($anulado=='S'){
			if(date('m',strtotime($fecha)) != date('m',strtotime($tfecha))){
				 
				if($codbanc != $tcodbanc){
					$this->chexiste_cheque($codbanc,$cheque,$tipo_doc,$id,$e);
					$error.=$e;
					
					$banc   = new DataObject("banc");
					$banc   ->load($codbanc        );
					$saldo  = $banc->get('saldo'   );
					$activo = $banc->get('activo'  );
					$banco  = $banc->get('banco'   );
					
					if($activo != 'S' )
						$error.="<div class='alert'><p>El banco ($banco) esta inactivo</p></div>";
						
					if($monto > $saldo )
						$error.="<div class='alert'><p>El Monto ($monto) del cheque es mayor al disponible ($saldo) en el banco ($banco)</p></div>";
						
					if(empty($error)){
						$banc->set('saldo',$saldo-$monto);
						$banc->save();
												
						$banc   ->load($tcodbanc);
						$saldo  = $banc->get('saldo');
						$banc->set('saldo',$saldo+$monto);
						$banc->save();
						
						if(empty($error)){
							$do->set('tipo_doc',$ttipo_doc);
							$do->set('cheque'  ,$tcheque );
							$do->set('codbanc' ,$tcodbanc);
							$do->set('status','AN');
							
							$this->creambanc($codbanc,$monto,$cheque,$tipo_doc,$observa,$fecha,$cod_prov,'E2',$id,$bcta);
							
							$this->creambanc($codbanc,$monto,$cheque,'NC',"Creada para respaldar cambio de cheque $cheque",$fecha,$cod_prov,'NC',$id,'');							
						}
					}
				}else{
					$this->chexiste_cheque($codbanc,$cheque,$tipo_doc,$id,$e);
					$error.=$e;
					if(empty($error)){
						$do->set('tipo_doc',$ttipo_doc);
						$do->set('cheque'  ,$tcheque );
						$do->set('codbanc' ,$tcodbanc);
						$do->set('status','AN');
						
						$this->creambanc($codbanc,$monto,$cheque,$tipo_doc,$observa,$fecha,$cod_prov,'E2',$id,'');
						
						$this->creambanc($codbanc,$monto,$cheque,'NC',"Creada para respaldar cambio de cheque $cheque",$fecha,$cod_prov,'NC',$id,'');
					}
				}
			}else{
				if($codbanc != $tcodbanc){
					$this->chexiste_cheque($codbanc,$cheque,$tipo_doc,$id,$e);
					$error.=$e;
					
					$banc   = new DataObject("banc");
					$banc   ->load($codbanc);
					$saldo  = $banc->get('saldo');
					$activo = $banc->get('activo');
					$banco  = $banc->get('banco');
					
					if($activo != 'S' )
						$error.="<div class='alert'><p>El banco ($banco) esta inactivo</p></div>";
						
					if($monto > $saldo )
						$error.="<div class='alert'><p>El Monto ($monto) del cheque es mayor al disponible ($saldo) en el banco ($banco)</p></div>";
					
					if(empty($error)){
						$banc->set('saldo',$saldo-$monto);
						$banc->save();
												
						$banc   ->load($tcodbanc);
						$saldo  = $banc->get('saldo');
						$banc->set('saldo',$saldo+$monto);
						$banc->save();
						
						if(empty($error)){
							$do->set('tipo_doc',$ttipo_doc);
							$do->set('cheque'  ,$tcheque );
							$do->set('codbanc' ,$tcodbanc);
							$do->set('status','AN');
							
							$this->creambanc($codbanc,$monto,$cheque,$tipo_doc,$observa,$fecha,$cod_prov,'E2',$id,'');										
						}
					}
				}else{
					$this->chexiste_cheque($codbanc,$cheque,$tipo_doc,$id,$e);
					$error.=$e;
					
					if(empty($error)){
						$do->set('tipo_doc',$ttipo_doc);
						$do->set('cheque'  ,$tcheque );
						$do->set('codbanc' ,$tcodbanc);
						$do->set('status','AN');
						}
						$this->creambanc($codbanc,$monto,$cheque,$tipo_doc,$observa,$fecha,$cod_prov,'E2',$id,'');
				}				
			}
		}else{
			$this->chexiste_cheque($tcodbanc,$tcheque,$ttipo_doc,$id,$e);
			$error.=$e;
		}
		
		if(empty($error)){
			logusu('ppro',"cambio datos cheque/banco $ttipo_doc Nro $tcheque por $tipo_doc Nro $cheque movimento $id");
		}else{
			$do->error_message_ar['pre_ins']=$error;
			$do->error_message_ar['pre_upd']=$error;
			logusu('ppro',"cambio datos cheque/banco $ttipo_doc Nro $tcheque por $tipo_doc Nro $cheque movimento $id con error $error");		
			return false;
		}
	}
	
	function _postcheque($do){
		$id=$do->get('id');
		$pago=$this->datasis->dameval("SELECT b.pago FROM mbanc a JOIN pambanc b ON a.id=b.mbanc WHERE b.mbanc=$id");
		redirect($this->url."dataedit/show/$pago");
	}
	
	function anula($id){
		$this->rapyd->load('dataedit2','dataobject');
		$this->datasis->modulo_id(208,1);
		
		$mBCTA=array(
				'tabla'   =>'bcta',
				'columnas'=>array(
					'codigo'       =>'C&oacute;odigo',
					'denominacion' =>'Denominacion',
					'cuenta'       =>'Cuenta'),
				'filtro'  =>array(
					'codigo'       =>'C&oacute;odigo',
					'denominacion' =>'Denominacion',
					'cuenta'       =>'Cuenta'),
				'retornar'=>array(
					'codigo'       =>'bcta',
					'denominacion' =>'bctad'),
				'titulo'  =>'Buscar Otros Ingresos');

		$bBCTA=$this->datasis->p_modbus($mBCTA,"bcta");

		$do = new DataObject("mbanc");
		//$do->pointer('banc' ,'banc.codbanc=mbanc.codbanc','banc.banco as nombreb,banc.banco as nombreb');
		
		$edit = new DataEdit2("Anular Cheque", "mbanc");
				
		$edit->back_url = site_url($this->url."filteredgrid/index");
		
		$edit->pre_process('update'  ,'_valida_anula');
		$edit->post_process('update' ,'_post_anula'  );
		
		$edit->codbanc =  new inputField("Banco", 'codbanc');
		$edit->codbanc-> size     = 3;
		$edit->codbanc-> rule     = "required";
		$edit->codbanc->mode="autohide";
		//$edit->codbanc-> readonly=true;
		$edit->codbanc->group    = "Transaccion";

    $edit->nombreb = new inputField("Nombre","nombreb");
    $edit->nombreb->size     = 20;
    $edit->nombreb->readonly = true;
    $edit->nombreb->pointer  = true;
    $edit->nombreb->in       = "codbanc";
    $edit->nombreb->group    = "Transaccion";
    $edit->nombreb->mode="autohide";
    
    $edit->tipo_doc = new dropdownField("Tipo Documento","tipo_doc");
    $edit->tipo_doc->option("CH","Cheque"         );
    //$edit->tipo_doc->option("NC","Nota de Credito");
    $edit->tipo_doc->option("ND","Nota de Debito" );
    //$edit->tipo_doc->option("DP","Deposito"         );
    $edit->tipo_doc->style="width:180px";
    $edit->tipo_doc->group  =  "Transaccion";
    $edit->tipo_doc->mode="autohide";

    $edit->cheque =  new inputField("Nro. Transacci&oacute;n", 'cheque');
	  $edit->cheque-> size  = 20;
	  $edit->cheque->rule   = "required";//callback_chexiste_cheque|
	  $edit->cheque->group    = "Transaccion";
	  $edit->cheque->mode="autohide";
	  
	  $edit->fecha = new  dateonlyField("Fecha Transacci&oacute;n",  "fecha");
		$edit->fecha->insertValue = date('Y-m-d');
		$edit->fecha->size    =12;
		$edit->fecha->rule    = 'required';
		$edit->fecha->group   = "Transaccion";
		$edit->fecha->mode="autohide";

		$edit->monto = new inputField("Monto", 'monto');		
		$edit->monto->size     = 8;
		$edit->monto->group    = "Transaccion";
		$edit->monto->css_class='inputnum';
		$edit->monto->rule     = 'callback_positivo';
		//$edit->monto->insertValue = $total;
		$edit->monto-> readonly=true;
		$edit->monto->mode="autohide";
		
		$edit->observa = new textAreaField("Concepto", 'observa');
		$edit->observa->cols = 60;
		$edit->observa->rows = 3;
		//$edit->observa->group    = "Transaccion";
		//$edit->observa->insertValue = $observa;
		//$edit->observa->mode="autohide";
		
		$edit->bcta = new inputField("Motivo Anulaci&oacute;n del cheque", 'bcta');
		$edit->bcta->size     = 6;
		$edit->bcta->rule     = "required";
		$edit->bcta->append($bBCTA);
		$edit->bcta->readonly=true;
		//$edit->bcta->group = "Deposito";
    
		$edit->bctad = new inputField("", 'bctad');
		$edit->bctad->size        = 50;
		//$edit->bctad->group       = "Deposito";
		$edit->bctad->in          = "bcta";
		$edit->bctad->pointer     = true;
		$edit->bctad->readonly    = true;
		
		$edit->buttons("modify","save","undo", "back");
		$edit->build();
		
		$data['content'] = $edit->output;
    $data['title']   = " Anular Cheque ";
    $data["head"]    = $this->rapyd->get_head();
    $this->load->view('view_ventanas', $data);
		
	}
	
	function _valida_anula($do){
		$this->rapyd->load('dataobject');
		
		$error='';
		
		$fecha  = $do->get('fecha'  );
		$codbanc= $do->get('codbanc');
		$monto  = $do->get('monto'  );
		$id     = $do->get('id'     );
		$cheque = $do->get('cheque' );
		$pago   = $this->datasis->dameval("SELECT pago FROM pambanc WHERE mbanc=$id");
		
		$banc   = new DataObject("banc");
		
		$banc   ->load($codbanc        );
		$saldo  = $banc->get('saldo'   );
		$activo = $banc->get('activo'  );
		$banco  = $banc->get('banco'   );
		
		if($activo != 'S' )
			$error.="<div class='alert'><p>El banco ($banco) esta inactivo</p></div>";
		
		if(empty($error)){
			$banc->set('saldo',$saldo+$monto);
			$banc->save();
			if(date('m') != date('m',strtotime($fecha)))
				$this->creambanc($codbanc,$monto,$cheque,'NC',"Creada para respaldar cambio de cheque $cheque",$fecha,$cod_prov,'NC',$id,'');
				
			$this->anular      = true;
			$this->redirect    = false;
			$this->reverpresup($pago);//echo $pago;exit('hola mundo');
		
			$do->set('status','AN');
			
			$odirect= new DataObject("odirect");
			
			$odirect->load($pago);
			$stat = $odirect->get('status');
			$odirect->set('status',substr($stat,0,1).'2');
			$odirect->save();
		}
		
		if(empty($error)){
			logusu('desem',"Anulo cheque $cheque");
		}else{
			$do->error_message_ar['pre_ins']=$error;
			$do->error_message_ar['pre_upd']=$error;
			logusu('desem',"Anulo cheque con error $error");		
			return false;
		}
	}
	
	function _post_anula($do){
	
		$id     = $do->get('id'     );
		$pago   = $this->datasis->dameval("SELECT pago FROM pambanc WHERE mbanc=$id ");
		$id     = $do->get('id'     );
		
		//print_r($do->get_all());
		//exit();
		
		logusu('desem',"Anulo Desembolso Nro $id");
		redirect("tesoreria/desem/dataedit/show/$pago");
	
	}
	
	function anular($id){
		$this->rapyd->load('dataobject');
	
		$do = new DataObject("mbanc");
		$do-> load($id);
	
		$error='';
		$this->anular      = true;
		$this->redirect    = false;
		$this->reverpresup($id);
		//$this->redirect    = true;
		
		$fecha  = $do->get('fecha'  );
		$codbanc= $do->get('codbanc');
		$monto  = $do->get('monto'  );
		
		$banc   = new DataObject("banc");
		$banc   ->load($codbanc        );
		$saldo  = $banc->get('saldo'   );
		$activo = $banc->get('activo'  );
		$banco  = $banc->get('banco'   );
		
		if($activo != 'S' )
			$error.="<div class='alert'><p>El banco ($banco) esta inactivo</p></div>";
		
		if(empty($error)){
			$banc->set('saldo',$saldo+$monto);
			$banc->save();
			if(date('m') != date('m',strtotime($fecha)))
				$this->creambanc($codbanc,$monto,$cheque,'NC',"Creada para respaldar cambio de cheque $cheque",$fecha,$cod_prov,'NC',$id,'');
				
			$do->set('status','AN');
		}
		
		if(empty($error)){
			$do->save();
			$pago = $this->datasis->dameval("SELECT b.pago FROM mbanc a JOIN pambanc b ON a.id=b.mbanc WHERE b.mbanc=$id");
			logusu('desem',"Anulo Desembolso Nro $id");
			redirect("tesoreria/desem/dataedit/show/$pago");
			
		}else{
			logusu('desem',"Anulo Desembolso Nro $id con $error");
			$data['content'] = $error.anchor("presupuesto/ocompra/dataedit/show/$id",'Regresar');
			$data['title']   = " Desembolso ";
			$data["head"]    = $this->rapyd->get_head();
			$this->load->view('view_ventanas', $data);
		}	
	}
	
	function _valida($do){
		$this->rapyd->load('dataobject');
	
		
		$error       = '';
		$codbanc     = $do->get('codbanc'     );
		$tipo_doc    = $do->get('tipo_doc'    );
		$fecha       = $do->get('fecha'       );
		$cheque      = $do->get('cheque'      );
		$pago        = $do->get_rel('pambanc','pago',0);
		$do->set('status','E2');
		$id=0;
		
		$do2 = new DataObject("odirect");
		$do2->load($pago);
		$total   = $do2->get('total');
		$observa = $do2->get('observa');
		$cod_prov= $do2->get('cod_prov');
		
		$do->set('monto'   ,$total  );
		$do->set('observa' ,$observa);
		$do->set('cod_prov',$cod_prov);
		
		$status = $do2->get('status');
		
		if(substr($status,1,1)=="2"){
			$do3 = new DataObject("banc");
			$do3->load($codbanc);
			
			$saldo    = $do3->get('saldo' );
			$activo   = $do3->get('activo');
	
			if($activo!='S')
				$error.="<div class='alert'><p>El Banco ($codbanc) esta inactivo</p></div>";
				
			if($total > $saldo)
				$error.="<div class='alert'><p>El Monto es Mayor Al Saldo del Banco</p></div>";
				
			if(empty($error)){			
				$this->chexiste_cheque($codbanc,$cheque,$tipo_doc,$id,$e);
				$error.=$e;
			}
		}else{
			$error.="<div class='alert'><p>No se Puede Pagar la orden de Pago ($pago)</p></div>";
		}
		
		if(!empty($error)){
			$do->error_message_ar['pre_ins']=$error;
			return false;
		}echo "por qui paso";		
	}
	
	function _post($do){
		echo "por qui tambien";
		$this->rapyd->load('dataobject');
	
		$error       = '';
		$codbanc     = $do->get('codbanc'     );
		$tipo_doc    = $do->get('tipo_doc'    );
		$fecha       = $do->get('fecha'       );
		$monto       = $do->get('monto'       );
		$cheque      = $do->get('cheque'      );
		$pago        = $do->get_rel('pambanc','pago');
		
		$do3 = new DataObject("banc");
		$do3->load($codbanc);
		$saldo    = $do3->get('saldo' );
						
		$saldo-=$monto;
		
		$do3->set('saldo',$saldo);
		$do3->save();
		
		$id = $do->get('id');
		//exit('hellovene');
		echo "aqui si";
		$this->actpresup($id);
		

		//$do2 = new DataObject("odirect");
		//$do2->load($pago);
		//$status = $do2->get('status');
		//$t      =substr($status,0,1);
		//$do2->set('status',$t.'3');

		//$do2->save();
echo "aqui";

		redirect($this->url."dataedit/show/$pago");
	
	}
	
	function actpresup($id){
		$this->rapyd->load('dataobject');

		$error='';
		echo $id."-";
		$ord     = new DataObject("ordinal");

		$mbanc     =  new DataObject("mbanc");
		$mbanc     -> rel_one_to_many('pambanc', 'pambanc', array('id'=>'mbanc'));
		$mbanc     -> load($id);
		$pago      =  $mbanc->get_rel('pambanc','pago'   );
		$codbanc   =  $mbanc->get_rel('pambanc','codbanc');
		
		echo "pago".$pago;
		$odirect = new DataObject("odirect");
		$odirect -> rel_one_to_many('pacom'    , 'pacom'    , array('numero'=>'pago'));
		$odirect -> rel_one_to_many('itodirect', 'itodirect', array('numero'=>'numero'));
		$odirect -> rel_one_to_many('itfac'    , 'itfac'    , array('numero'=>'numero'));
		$odirect ->load($pago);
		echo "parece qu quiere";
		
		$status      =  $odirect->get('status'          );		      
		$fechafac    =	$odirect->get('fechafac'        );      
		$controlfac  =	$odirect->get('controlfac'      );      
		$cod_prov    =	$odirect->get('cod_prov'        );      
		$exento      =	$odirect->get('exento'          );
		$tivag       =	$odirect->get('tivag'           );
		$mivag       =	$odirect->get('mivag'           );
		$ivag        =	$odirect->get('ivag'            );
		$tivaa       =	$odirect->get('tivaa'           );
		$mivag       =	$odirect->get('mivag'           );
		$ivaa        =	$odirect->get('ivaa'            );
		$tivar       =	$odirect->get('tivar'           );
		$mivar       =	$odirect->get('mivar'           );
		$ivar        =	$odirect->get('ivar'            );
		$subtotal    =	$odirect->get('subtotal'        );
		$reteiva     =	$odirect->get('reteiva'         );
		$reteiva_prov=	$odirect->get('reteiva_prov'    );
		$codigoadm   =  $odirect->get('estadmin'        );
		$fondo       =  $odirect->get('fondo'           );
		
		$ocompra = new DataObject("ocompra");
		$ocompra->rel_one_to_many('itocompra', 'itocompra', array('numero'=>'numero'));

		$presup  = new DataObject("presupuesto");
		
		$partidaiva=$this->datasis->traevalor("PARTIDAIVA");
		
	 	if($status == "F2" ){
	 	echo "F";
			for($g=0;$g < $odirect->count_rel('pacom');$g++){
				$p_t       = $odirect->get_rel('pacom','total' ,$g);
				$p_compra  = $odirect->get_rel('pacom','compra',$g);
echo "p_compra".$p_compra;
				$ocompra->load($p_compra);
echo "des_p_compra";
				$codigoadm     = $ocompra->get('estadmin'  );
				$fondo         = $ocompra->get('fondo'     );
				$status        = $ocompra->get('status'    );
				$creten        = $ocompra->get('creten'    );
				$fechafac      = $ocompra->get('fechafac'  );
				$factura       = $ocompra->get('factura'   );
				$controlfac    = $ocompra->get('contofac'  );
				$exento        = $ocompra->get('exento');
				$tivaa         = $ocompra->get('tivaa');
				$tivag         = $ocompra->get('tivag');
				$tivar         = $ocompra->get('tivar');
				$ivaa          = $ocompra->get('ivaa');
				$ivag          = $ocompra->get('ivag');
				$ivar          = $ocompra->get('ivar');
				$mivaa         = $ocompra->get('mivaa');
				$mivag         = $ocompra->get('mivag');
				$mivar         = $ocompra->get('mivar');
				$subtotal      = $ocompra->get('subtotal');
				$reteiva       = $ocompra->get('reteiva');
				$tislr=$reten  = $ocompra->get('reten');				
				$total         = $ocompra->get('total');
				$reteiva_prov  = $ocompra->get('reteiva_prov');
				$ivan          = $ivag+$ivar+$ivaa;

				if(true){//$total==$pagado
					$pk=array('codigoadm'=>$codigoadm,'tipo'=>$fondo,'codigopres'=>$partidaiva);
					echo "va a ser";
					$presup->load($pk);
		echo "aqui fue";
					$pasignacion   = $presup->get("asignacion");
						$totiva=0;
						for($k=0;$k < $ocompra->count_rel('itocompra');$k++){
							$islrid = '';
							$codigopres  = $ocompra->get_rel('itocompra','partida',$k);
							$importe     = $ocompra->get_rel('itocompra','importe',$k);
							$iva         = $ocompra->get_rel('itocompra','iva'    ,$k);
							$ordinal     = $ocompra->get_rel('itocompra','ordinal',$k);								
							
							if($pasignacion>0){
								$mont    = $importe;
								$totiva += ($importe*$iva/100);
							}
							else
								$mont    = $importe + ($importe*$iva/100);
								
							$pk=array('codigoadm'=>$codigoadm,'tipo'=>$fondo,'codigopres'=>$codigopres);

							$presup->load($pk);
							$pagado=$presup->get("pagado");
							$pagado=$pagado+$mont;

							$presup->set("pagado",$pagado);
							$presup->save();

							if(!empty($ordinal)){
								$pk = array("codigoadm"=>$codigoadm,"fondo"=>$fondo,"codigopres"=>$codigopres,"ordinal"=>$ordinal);
								
								$ord->load($pk);											
								$pagago =$ord->get("pagado" );    
								$pagago += $mont;
								$ord->set("pagado",$pagado);								
								$ord->save();																			
							}	
						}

						if($pasignacion>0){
							$pk=array('codigoadm'=>$codigoadm,'tipo'=>$fondo,'codigopres'=>$partidaiva);
							$presup->load($pk);
	
							$pagado =$presup->get("pagado");
							$pagado+=$totiva;
							$presup->set("pagado",$pagado);
							$presup->save();
						}
						
						if($reteiva > 0)
							$this->riva($p_compra,$pago,$itfact='',$factura,$controlfac,$fechafac,$cod_prov,$exento,$tivag,$mivag,$tivaa,$ivaa,$tivar,$mivar,$reteiva,$codbanc,$id,$reteiva_prov);
					
					$ocompra->set('status','E');
					$ocompra->save();
					$this->sp_presucalc($codigoadm);
				}				
			}
			$odirect->set('status','F3');
			$odirect->save();
		}elseif($status == "B2"){

			$pk=array('codigoadm'=>$codigoadm,'tipo'=>$fondo,'codigopres'=>$partidaiva);
			$presup->load($pk);
			
			$pasignacion   =$presup->get("asignacion");
			$totiva = 0;
			for($g=0;$g   <  $odirect->count_rel('itodirect');$g++){

				$codigopres  = $odirect->get_rel('itodirect','partida',$g);
				$importe     = $odirect->get_rel('itodirect','importe',$g);
				$iva         = $odirect->get_rel('itodirect','iva'    ,$g);
				$ordinal     = $odirect->get_rel('itodirect','ordinal',$g);
				
				if($pasignacion>0){
					$mont    = $importe;
					$totiva += ($importe*$iva/100);
				}
				else
					$mont    = $importe + ($importe*$iva/100);

				$pk=array("codigoadm"=>$codigoadm,"tipo"=>$fondo,"codigopres"=>$codigopres);

				$presup->load($pk);
				$pagado=$presup->get("pagado");
				$pagado=$pagado+$mont;
				
				$presup->set("pagado",$pagado);
			
				$presup->save();

				if(!empty($ordinal)){
					$ord->load(array("codigoadm"=>$codigoadm,"fondo"=>$fondo,"codigopres"=>$codigopres,"ordinal"=>$ordinal));

					$pagado   =$ord->get("pagado" );
					$pagado  +=$mont;
					$ord->set("pagado"  ,$pagado  );
					$ord->save();
				}
			}
		
			if($reteiva >0){				
				if($odirect->get('multiple')=='N'){
						$this->riva('',$pago,$itfact='',$factura,$controlfac,$fechafac,$cod_prov,$exento,$tivag,$mivag,$tivaa,$ivaa,$tivar,$mivar,$reteiva,$codbanc,$id,$reteiva_prov);					
				}elseif($odirect->get('multiple')=='S'){
					for($l=0;$l < $odirect->count_rel('itfac');$l++){
						$iditfac     = $odirect->get_rel('itfac','id',$l        );
						$factura     = $odirect->get_rel('itfac','factura'   ,$l);   
						$fechafac    = $odirect->get_rel('itfac','fechafac'  ,$l);  
						$controlfac  = $odirect->get_rel('itfac','controlfac',$l);
						$exento      = $odirect->get_rel('itfac','exento'    ,$l);                               
						$ivag        = $odirect->get_rel('itfac','ivag'      ,$l);							                              
						$ivaa        = $odirect->get_rel('itfac','ivaa'      ,$l);							                              
						$ivar        = $odirect->get_rel('itfac','ivar'      ,$l);
						$reteiva     = $odirect->get_rel('itfac','reteiva'   ,$l);
						$mivag       = $ivag*100/$tivag;
						$mivar       = $ivar*100/$tivar;
						$mivaa       = $ivaa*100/$tivaa;													
						if($reteiva>0){
							$this->riva('',$pago,$iditfac,$factura,$controlfac,$fechafac,$cod_prov,$exento,$tivag,$mivag,$tivaa,$ivaa,$tivar,$mivar,$reteiva,$codbanc,$id,$reteiva_prov);
						}
					}
				}
			}
			
			if($pasignacion>0){
				$pk['codigopres'] = $partidaiva;
				$presup->load($pk);

				$pagado =$presup->get("pagado");
				$pagado+=$totiva;
				$presup->set("pagado",$pagado);
				$presup->save();
			}
			
			$odirect->set('status','B3');
			$odirect->save();

			$this->sp_presucalc($codigoadm);		
		}elseif($status == "I2"){	
			$odirect->set('status','I3');
			$odirect->save();
		}elseif($status == "M2"){
			if($status=='M2')
				$odirect->set('status','M3');
			$odirect->save();					
		}elseif($status=='S2'){
			$odirect->set('status','S3');
			$odirect->save();
		}
		elseif($status=='R2'){
			$odirect->set('status','R3');
			$odirect->save();
		}elseif($status=='G2'){
			$odirect->set('status','G3');
			$odirect->save();
		}elseif($status=='H2'){
			$odirect->set('status','H3');
			$odirect->save();
		}elseif($status=='O2'){
			$obr     = $odirect->get('obr'     );
			$iva     = $odirect->get('iva'     );
			$total2  = $odirect->get('total2'  );
			$amortiza= $odirect->get('amortiza');
			$mont    = $total2-$amortiza;
			
			$obra = new DataObject("obra");		
			$obra->load($obr);
			
			$codigoadm  = $obra->get('codigoadm' );
			$fondo      = $obra->get('fondo'     );
			$codigopres = $obra->get('codigopres');
			$ordinal    = $obra->get('ordinal'   );
			
			$pk = array("codigoadm"=>$codigoadm,"tipo"=>$fondo,"codigopres"=>$codigopres);				
			$presup->load($pk);
			$pag    =$presup->get("pagado"   );							
			$pag   += $mont;							
			$presup->set("pagado"   ,$pag    );			
			$presup->save();
			
			if(!empty($ordinal)){
				$pk = array("codigoadm"=>$codigoadm,"fondo"=>$fondo,"codigopres"=>$codigopres,"ordinal"=>$ordinal);
				$ord->load($pk);
				
				$pagado   =$ord->get("pagado"   );							
				$pagado  += $mont;							
				$ord->set("pagado"   ,$pagado   );
				$ord->save();
			}
			if($reteiva>0)
				$this->riva('',$pago,$itfact='',$factura,$controlfac,$fechafac,$cod_prov,$exento,$tivag,$mivag,$tivaa,$ivaa,$tivar,$mivar,$reteiva,$codbanc,$id,$reteiva_prov);
			
			$odirect->set('status','O3');
			$odirect->save();
			
		}else{
			
		}
		//$this->sp_presucalc($codigoadm);
	}
	
	function reverpresup($pago){
		$this->rapyd->load('dataobject');

		$error='';
		
		$ord     = new DataObject("ordinal");

		//$mbanc     =  new DataObject("mbanc");
		//$mbanc     -> rel_one_to_many('pambanc', 'pambanc', array('id'=>'mbanc'));
		//$mbanc     -> load($id);
		//$pago      =  $mbanc->get_rel('pambanc','pago'   );
		//$codbanc   =  $mbanc->get_rel('pambanc','codbanc');
		
		$odirect = new DataObject("odirect");
		//$odirect -> rel_one_to_many('pacom'    , 'pacom'    , array('numero'=>'pago'));
		//$odirect -> rel_one_to_many('itodirect', 'itodirect', array('numero'=>'numero'));
		//$odirect -> rel_one_to_many('itfac'    , 'itfac'    , array('numero'=>'numero'));
		$odirect ->load($pago);
		
		
		$status      =  $odirect->get('status'          );		      
		$fechafac    =	$odirect->get('fechafac'        );      
		$controlfac  =	$odirect->get('controlfac'      );      
		$cod_prov    =	$odirect->get('cod_prov'        );      
		$exento      =	$odirect->get('exento'          );
		$tivag       =	$odirect->get('tivag'           );
		$mivag       =	$odirect->get('mivag'           );
		$ivag        =	$odirect->get('ivag'            );
		$tivaa       =	$odirect->get('tivaa'           );
		$mivag       =	$odirect->get('mivag'           );
		$ivaa        =	$odirect->get('ivaa'            );
		$tivar       =	$odirect->get('tivar'           );
		$mivar       =	$odirect->get('mivar'           );
		$ivar        =	$odirect->get('ivar'            );
		$subtotal    =	$odirect->get('subtotal'        );
		$reteiva     =	$odirect->get('reteiva'         );
		$reteiva_prov=	$odirect->get('reteiva_prov'    );
		$codigoadm   =  $odirect->get('estadmin'        );
		$fondo       =  $odirect->get('fondo'           );
		
		$ocompra = new DataObject("ocompra");
		$ocompra->rel_one_to_many('itocompra', 'itocompra', array('numero'=>'numero'));

		$presup  = new DataObject("presupuesto");
		
		$partidaiva=$this->datasis->traevalor("PARTIDAIVA");
		
	 	if($status == "F3" ){
	 	
		 	$odirect = new DataObject("odirect");
			$odirect -> rel_one_to_many('pacom'    , 'pacom'    , array('numero'=>'pago'));
			$odirect -> rel_one_to_many('itodirect', 'itodirect', array('numero'=>'numero'));
			$odirect -> rel_one_to_many('itfac'    , 'itfac'    , array('numero'=>'numero'));
			$odirect ->load($pago);
		
			for($g=0;$g < $odirect->count_rel('pacom');$g++){
				$p_t       = $odirect->get_rel('pacom','total' ,$g);
				$p_compra  = $odirect->get_rel('pacom','compra',$g);

				$ocompra->load($p_compra);

				$codigoadm     = $ocompra->get('estadmin'  );
				$fondo         = $ocompra->get('fondo'     );
				$status        = $ocompra->get('status'    );
				$creten        = $ocompra->get('creten'    );
				$fechafac      = $ocompra->get('fechafac'  );
				$factura       = $ocompra->get('factura'   );
				$controlfac    = $ocompra->get('contofac'  );
				$exento        = $ocompra->get('exento');
				$tivaa         = $ocompra->get('tivaa');
				$tivag         = $ocompra->get('tivag');
				$tivar         = $ocompra->get('tivar');
				$ivaa          = $ocompra->get('ivaa');
				$ivag          = $ocompra->get('ivag');
				$ivar          = $ocompra->get('ivar');
				$mivaa         = $ocompra->get('mivaa');
				$mivag         = $ocompra->get('mivag');
				$mivar         = $ocompra->get('mivar');
				$subtotal      = $ocompra->get('subtotal');
				$reteiva       = $ocompra->get('reteiva');
				$tislr=$reten  = $ocompra->get('reten');				
				$total         = $ocompra->get('total');
				$reteiva_prov  = $ocompra->get('reteiva_prov');
				$ivan          = $ivag+$ivar+$ivaa;

				if(true){//$total==$pagado
					$pk=array('codigoadm'=>$codigoadm,'tipo'=>$fondo,'codigopres'=>$partidaiva);
					$presup->load($pk);
		
					$pasignacion   = $presup->get("asignacion");
						$totiva=0;
						for($k=0;$k < $ocompra->count_rel('itocompra');$k++){
							$islrid = '';
							$codigopres  = $ocompra->get_rel('itocompra','partida',$k);
							$importe     = $ocompra->get_rel('itocompra','importe',$k);
							$iva         = $ocompra->get_rel('itocompra','iva'    ,$k);
							$ordinal     = $ocompra->get_rel('itocompra','ordinal',$k);								
							
							if($pasignacion>0){
								$mont    = $importe;
								$totiva += ($importe*$iva/100);
							}
							else
								$mont    = $importe + ($importe*$iva/100);
								
							$pk=array('codigoadm'=>$codigoadm,'tipo'=>$fondo,'codigopres'=>$codigopres);

							$presup->load($pk);
							$pagado=$presup->get("pagado");
							$pagado=$pagado-$mont;

							$presup->set("pagado",$pagado);
							$presup->save();

							if(!empty($ordinal)){
								$apk = array("codigoadm"=>$codigoadm,"fondo"=>$fondo,"codigopres"=>$codigopres,"ordinal"=>$ordinal);
								$ord->load($pk);											
								$pagago =$ord->get("pagado" );    
								$pagago -= $mont;
								$ord->set("pagado",$pagado);								
								$ord->save();																			
							}	
						}

						if($pasignacion>0){
							$pk=array('codigoadm'=>$codigoadm,'tipo'=>$fondo,'codigopres'=>$partidaiva);
							$presup->load($pk);
	
							$pagado =$presup->get("pagado");
							$pagado-=$totiva;
							$presup->set("pagado",$pagado);
							$presup->save();
						}
						if($reteiva > 0)
							$this->riva_an($p_compra,$pago,$itfact='');
					
					$ocompra->set('status','O');
					$ocompra->save();
					$this->sp_presucalc($codigoadm);
				}
			}
			$odirect->set('status','F2');
			$odirect->save();
			//$this->po_anular($pago,false);
		}elseif($status == "B3"){
		
			//$odirect = new DataObject("odirect");
			////$odirect -> rel_one_to_many('pacom'    , 'pacom'    , array('numero'=>'pago'));
			//$odirect -> rel_one_to_many('itodirect', 'itodirect', array('numero'=>'numero'));
			////$odirect -> rel_one_to_many('itfac'    , 'itfac'    , array('numero'=>'numero'));
			//
			//$odirect ->load($pago);

			$pk=array('codigoadm'=>$codigoadm,'tipo'=>$fondo,'codigopres'=>$partidaiva);
			$presup->load($pk);
			
			$pasignacion   =$presup->get("asignacion");
			$totiva = 0;
			for($g=0;$g   <  $odirect->count_rel('itodirect');$g++){

				$codigopres  = $odirect->get_rel('itodirect','partida',$g);
				$importe     = $odirect->get_rel('itodirect','importe',$g);
				$iva         = $odirect->get_rel('itodirect','iva'    ,$g);
				$ordinal     = $odirect->get_rel('itodirect','ordinal',$g);
				
				if($pasignacion>0){
					$mont    = $importe;
					$totiva += ($importe*$iva/100);
				}
				else
					$mont    = $importe + ($importe*$iva/100);

				$pk=array("codigoadm"=>$codigoadm,"tipo"=>$fondo,"codigopres"=>$codigopres);

				$presup->load($pk);
				$pagado=$presup->get("pagado");
				$pagado=$pagado-$mont;

				$presup->set("pagado",$pagado);
			
				$presup->save();

				if(!empty($ordinal)){
					$ord->load(array("codigoadm"=>$codigoadm,"fondo"=>$fondo,"codigopres"=>$codigopres,"ordinal"=>$ordinal));

					$pagado   =$ord->get("pagado" );
					$pagado  -=$mont;
					$ord->set("pagado"  ,$pagado  );
					$ord->save();

				}
			}
		
			if($reteiva >0){				
				if($odirect->get('multiple')=='N'){
						$this->riva_an('',$pago,$itfact='');				
				}elseif($odirect->get('multiple')=='S'){
					//$odirect = new DataObject("odirect");
					////$odirect -> rel_one_to_many('pacom'    , 'pacom'    , array('numero'=>'pago'));
					////$odirect -> rel_one_to_many('itodirect', 'itodirect', array('numero'=>'numero'));
					//$odirect -> rel_one_to_many('itfac'    , 'itfac'    , array('numero'=>'numero'));
					//
					//$odirect ->load($pago);
					
				
					for($l=0;$l < $odirect->count_rel('itfac');$l++){
						$iditfac     = $odirect->get_rel('itfac','id',$l        );
						$factura     = $odirect->get_rel('itfac','factura'   ,$l);   
						$fechafac    = $odirect->get_rel('itfac','fechafac'  ,$l);  
						$controlfac  = $odirect->get_rel('itfac','controlfac',$l);
						$exento      = $odirect->get_rel('itfac','exento'    ,$l);                               
						$ivag        = $odirect->get_rel('itfac','ivag'      ,$l);							                              
						$ivaa        = $odirect->get_rel('itfac','ivaa'      ,$l);							                              
						$ivar        = $odirect->get_rel('itfac','ivar'      ,$l);
						$reteiva     = $odirect->get_rel('itfac','reteiva'   ,$l);
						$mivag       = $ivag*100/$tivag;
						$mivar       = $ivar*100/$tivar;
						$mivaa       = $ivaa*100/$tivaa;													
						if($reteiva>0){
							$this->riva_an('',$pago,$iditfac);
						}
					}
				}
			}
			
			if($pasignacion>0){
				$pk['codigopres'] = $partidaiva;
				$presup->load($pk);

				$pagado =$presup->get("pagado");
				$pagado-=$totiva;
				$presup->set("pagado",$pagado);
				$presup->save();
			}
			
			$odirect->set('status','B2');
			$odirect->save();
			//$this->op_anular($pago,false);

			$this->sp_presucalc($codigoadm);		
		}elseif($status == "I3"){	
			$odirect->set('status','I2');
			$odirect->save();
		}elseif($status == "M3"){
				$odirect->set('status','M2');
			$odirect->save();					
		}elseif($status=='S3'){
			$odirect->set('status','S2');
			$odirect->save();
		}
		elseif($status=='R3'){
			$odirect->set('status','R2');
			$odirect->save();
		}elseif($status=='G3'){
			$odirect->set('status','G2');
			$odirect->save();
		}elseif($status=='H3'){
			$odirect->set('status','H2');
			$odirect->save();
		}elseif($status=='O3'){
			$obr     = $odirect->get('obr'     );
			$iva     = $odirect->get('iva'     );
			$total2  = $odirect->get('total2'  );
			$amortiza= $odirect->get('amortiza');
			$mont    = $total2-$amortiza;
			
			$obra = new DataObject("obra");		
			$obra->load($obr);
			
			$codigoadm  = $obra->get('codigoadm' );
			$fondo      = $obra->get('fondo'     );
			$codigopres = $obra->get('codigopres');
			$ordinal    = $obra->get('ordinal'   );
			
			$pk = array("codigoadm"=>$codigoadm,"tipo"=>$fondo,"codigopres"=>$codigopres);				
			$presup->load($pk);
			$pag    =$presup->get("pagado"   );							
			$pag   -= $mont;							
			$presup->set("pagado"   ,$pag    );			
			$presup->save();
			
			if(!empty($ordinal)){
				$pk = array("codigoadm"=>$codigoadm,"fondo"=>$fondo,"codigopres"=>$codigopres,"ordinal"=>$ordinal);
				$ord->load($pk);
				
				$pagado   =$ord->get("pagado"   );							
				$pagado  -= $mont;							
				$ord->set("pagado"   ,$pagado   );
				$ord->save();
			}
			if($reteiva>0)
				$this->riva_an('',$pago,$itfact='');
			
			$odirect->set('status','O2');
			$odirect->save();
			//$this->po_anular($pago,false);
		}else{
			
		}
		//$this->sp_presucalc($codigoadm);
	}
	
	function riva($ocompra='',$odirect,$itfact='',$numero,$nfiscal,$ffactura,$clipro,$exento,$tasa,$general,$tasaadic,$adicional,$tasaredu,$reducida,$reiva,$codbanc,$mbanc,$reteiva_prov,$mbanc){
		$this->rapyd->load('dataobject');
		$riva = new dataobject("riva");
		
		$clipro2 = $this->db->escape($clipro );
		$codbanc2= $this->db->escape($codbanc);
		$sprv   = $this->datasis->damerow("SELECT nombre,rif FROM sprv WHERE proveed = $clipro2     ");
		$banc   = $this->datasis->damerow("SELECT banco,numcuent FROM banc WHERE codbanc = $codbanc2");

		$geneimpu  = $general*$tasa/1100      ;
		$adicimpu  = $tasaadic*$adicional/100 ;
		$reduimpu  = $tasaredu*$reducida/100  ;
		
		$riva->set('ocompra'       , $ocompra                         );
		$riva->set('odirect'       , $odirect                         );
		$riva->set('itfac'         , $itfac                           );
		$riva->set('emision'       , date('Ymd')                      );
		$riva->set('periodo'       , date('Ym')                       );
		$riva->set('tipo_doc'      , 'FC'                             );
		$riva->set('fecha'         , date('Ymd')                      );
		$riva->set('numero'        , $numero                          );
		$riva->set('ffactura'      , $ffactura                        );
		$riva->set('nfiscal'       , $nfiscal                         );
		$riva->set('clipro'        , $clipro                          );
		$riva->set('nombre'        , $sprv['nombre']                  );
		$riva->set('rif'           , $sprv['rif']                     );
		$riva->set('exento'        , $exento                          );
		$riva->set('tasa'          , $tasa                            );         
		$riva->set('general'       , $general                         );
		$riva->set('geneimpu'      , $geneimpu                        );
		$riva->set('tasaadic'      , $tasaadic                        );
		$riva->set('adicional'     , $adicional                       );
		$riva->set('adicimpu'      , $adicimpu                        );
		$riva->set('tasaredu'      , $tasaredu                        );
		$riva->set('reducida'      , $reducida                        );
		$riva->set('reduimpu'      , $reduimpu                        );
		$riva->set('stotal'        , $general+$adicional+$reducida    );
		$riva->set('impuesto'      , $geneimpu+$adicimpu+$reduimpu    );
		$riva->set('gtotal'        , $general+$adicional+$reducida+$geneimpu+$adicimpu+$reduimpu);
		$riva->set('reiva'         , $reiva                           );
		$riva->set('status'        , 'B'                              );
		$riva->set('banc'          , $banc['banco']                   );
		$riva->set('numcuent'      , $banc['numcuent']                );
		$riva->set('codbanc'       , $codbanc                         );
		$riva->set('mbanc'         , $mbanc                           );
		$riva->set('reteiva_prov'  , $reteiva_prov                    );
		$riva->save();
	}
	
	function riva_an($ocompra='',$odirect,$itfac=''){
		//$this->rapyd->load('dataobject');
		//$riva = new dataobject("riva");
		
		//if(!empty($odirect))$riva->load_where('odirect',$odirect);
		//if(!empty($ocompra))$riva->load_where('ocompra',$ocompra);
		//if(!empty($itfac  ))$riva->load_where('itfac'  ,$itfac  );
		
		$query="UPDATE riva SET tipo_doc='AN',status='AN' WHERE odirect=$odirect AND status='B' ";
		if(!empty($ocompra))
			$query.=" AND ocompra=$ocompra";
		elseif(!empty($itfac))
			$query.=" AND itfac=$itfac";
		
			//echo $query;
		$this->db->simple_query($query);
		
		//$riva->set('tipo_doc'  , 'AN'    );
		//$riva->set('status'    , 'AN'    );
		//$riva->save();
	}
	
	function chexiste_cheque($codbanc,$cheque,$tipo_doc,$id,&$error){
		$cheque     = $this->db->escape($cheque       );
		$tipo_doc   = $this->db->escape($tipo_doc     );
		$codbanc    = $this->db->escape($codbanc      );
		$error      = "";
		if($id>0)$query="SELECT id FROM mbanc WHERE codbanc=$codbanc AND cheque=$cheque AND tipo_doc=$tipo_doc AND id<>$id";
		else $query="SELECT id FROM mbanc WHERE codbanc=$codbanc AND cheque=$cheque AND tipo_doc=$tipo_doc";
		
		$cana=$this->datasis->dameval($query);
		if($cana>0){
			$pago = $this->datasis->dameval("SELECT b.pago FROM mbanc a JOIN pambanc b ON a.id = b.mbanc WHERE b.mbanc=$cana LIMIT 1");
			$error="El Cheque ya Existe para la orden de pago $pago";
		}
	}
	
	function cheque($pago){
		$id=$this->datasis->dameval("SELECT id FROM mbanc a JOIN pambanc b ON a.id=b.mbanc WHERE b.pago=".$pago." AND a.status='E2'");
		redirect("formatos/ver/CHEQUE2/$id");
	}
	
	function rivaa($pago){
		
		$cant = $this->datasis->dameval("SELECT COUNT(*) FROM riva WHERE odirect=$pago AND status='B'");
		if($cant==1){
			$nrocomp = $this->datasis->dameval("SELECT nrocomp FROM riva WHERE odirect=$pago AND status='B'");
			redirect("formatos/ver/RIVA/$nrocomp");
		}elseif($cant>1){
			redirect("formatos/ver/RIVAM/$pago");
		}else{
		
			$data['content'] = "No Existen retenciones de IVA para la Orden de Pago ($pago)";
			$data['title']   = "  ";
			$data["head"]    = $this->rapyd->get_head();
			$this->load->view('view_ventanas', $data);
			
		}
	}
	
	function sp_presucalc($codigoadm){
		$this->db->query("CALL sp_presucalc('$codigoadm')");
		return true;
	}
	
	function _post_insert($do){
		$tipo_doc   = $do->get('tipo_doc');
		$cheque     = $do->get('cheque');
		$id         = $do->get('id');
		logusu('mbanc',"Creo $tipo_doc Nro $cheque movimento $id");
		//redirect($this->url."actualizar/$id");
	}
	function _post_update($do){
		$tipo_doc   = $do->get('tipo_doc');
		$cheque     = $do->get('cheque');
		$id         = $do->get('id');
		logusu('mbanc',"modifico $tipo_doc Nro $cheque movimento $id");
		//redirect($this->url."actualizar/$id");
	}
	function _post_delete($do){
		$tipo_doc   = $do->get('tipo_doc');
		$cheque     = $do->get('cheque');
		$id         = $do->get('id');
		logusu('mbanc',"modifico $tipo_doc Nro $cheque movimento $id");
	}
}
