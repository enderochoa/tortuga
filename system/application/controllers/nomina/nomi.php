<?php
require_once(BASEPATH.'application/controllers/presupuesto/common.php');
class Nomi extends Common {

	var $titp='Compromiso de N&oacute;mina';
	var $tits='Compromiso de N&oacute;mina';
	var $url ='nomina/nomi/';

	function nomi(){
		parent::Controller();
		$this->load->library("rapyd");
	}

	function index(){
		redirect($this->url."filteredgrid");
	}

	function filteredgrid(){

		$this->rapyd->load("datafilter2","datagrid");
		//$this->rapyd->uri->keep_persistence();

		$filter = new DataFilter2("","nomi");

		$filter->numero = new inputField("N&uacute;mero", "numero");
		$filter->numero->db_name="numero";
		$filter->numero->size=12;
		$filter->numero->clause="likerigth";

		$filter->compromiso = new inputField("Compromiso", "compromiso");
		$filter->compromiso->db_name="compromiso";
		$filter->compromiso->size=12;

		$filter->fecha = new dateonlyField("Fecha", "fecha");
		$filter->fecha->dbformat = "Y-m-d";
		$filter->fecha->db_name="fecha";
		$filter->fecha->size=12;

		$filter->descrip = new inputField("Descripci&oacute;n", "descrip");
		$filter->descrip->db_name="descrip";
		$filter->descrip->size  =10;

		$filter->status = new dropdownField("Estado","status");
		$filter->status->db_name="status";
		$filter->status->option("","");
		$filter->status->option("P","Pendiente");
		$filter->status->option("C","Comprometido");
		$filter->status->option("O","Ordenado Pago");
		$filter->status->option("E","Pagado");
		$filter->status->option("D","Orden Asignada");
		$filter->status->style="width:150px";

		$filter->buttons("reset","search");

		$filter->build();
		$uri = anchor($this->url.'dataedit/show/<#numero#>','<#numero#>');


		function sta($status){
			switch($status){
				case "P":return "Pendiente"     ;break;
				case "C":return "Comprometido"  ;break;
				case "O":return "Ordenado Pago" ;break;
				case "E":return "Pagado"        ;break;
				case "D":return "Orden Asignada";break;
				case "p":return "Por Modificar";break;
			}
			return $status;
		}

		//function action($status,$numero){
		//	$uri='';
		//	switch(substr($status,1,1)){
		//		case "2":$uri = anchor("tesoreria/desem/add/create/$numero",'Desembolsar');break;
		//	}
		//	return $uri;
		//}

		$grid = new DataGrid("");
		$grid->order_by("numero","desc");
		$grid->per_page = 20;
		$grid->use_function('substr','str_pad','sta','action');

		$grid->column_orderby("N&uacute;mero"    ,$uri,"numero");
		$grid->column_orderby("Compromiso"       ,"compromiso"                                   ,"compromiso"    ,"align='left'  ");
		$grid->column_orderby("Descripcion"      ,"descrip"                                      ,"descrip"       ,"align='left'  ");
		$grid->column_orderby("Fecha"            ,"<dbdate_to_human><#fecha#></dbdate_to_human>" ,"fecha"         ,"align='center'");
		$grid->column_orderby("Asignaciones"     ,"<nformat><#asig#></nformat>"                  ,"asig"          ,"align='right' ");
		$grid->column_orderby("Deducciones"      ,"<nformat><#rete#></nformat>"                  ,"rete"          ,"align='right' ");
		$grid->column_orderby("Estado"           ,"<sta><#status#></sta>"                        ,"status"        ,"align='center'");
		//$grid->column(" "                ,"<action><#status#>|<#numero#></action>"      ,"align='center'");

		//echo $grid->db->last_query();
		if($this->datasis->puede(188))$grid->add($this->url."dataedit/create");
		$grid->build();

		$salida='';
		if($this->datasis->traevalor('USASIPRES')=='S' and ($this->datasis->puede(286) || $this->datasis->essuper()))
		$salida = anchor($this->url."sipresrnomi","Crear Compromiso basado en SIPRES");
		//$data['content'] = $filter->output.$grid->output;
		$data['filtro']  = $filter->output;
		$data['content'] = $salida.$grid->output;
		$data['script'] = script("jquery.js")."\n";
		$data['title']   = $this->titp;
		$data["head"]    = $this->rapyd->get_head();
		$this->load->view('view_ventanas', $data);
	}

	function dataedit(){
		//$this->datasis->modulo_id(115,1);
		$this->rapyd->load('dataobject','datadetails');

		$modbus=array(
			'tabla'   =>'v_presaldo',
			'columnas'=>array(
				'codigoadm'   =>'Est. Admin'         ,
				'fondo'       =>'Fondo'              ,
				'codigo'      =>'Partida'            ,
				'ordinal'     =>'Ordinal'            ,
				'denominacion'=>'Denominaci&oacute;n',
				'saldo'       =>'Saldo'
				),
			'filtro'  =>array(
				'codigoadm'   =>'Est. Admin',
				'fondo'       =>'Fondo'     ,
				'codigo'      =>'Partida'   ,
				'ordinal'     =>'Ordinal'   ,
				'denominacion'=>'Partida'   ,
				),
			'retornar'=>array(
				'codigoadm'   =>'codigoadm_<#i#>' ,
				'fondo'       =>'fondo_<#i#>'     ,
				'codigo'      =>'codigopres_<#i#>',
				'denominacion'=>'denomi_<#i#>'
				),
			'p_uri'=>array(4=>'<#i#>',),
			'where'=>'movimiento = "S" AND saldo >0',// AND saldo > 0 AND movimiento = "S"
			'titulo'  =>'Busqueda de partidas');
		$btn=$this->datasis->p_modbus($modbus,'<#i#>');

		$mSPRV=array(
			'tabla'   =>'sprv',
			'columnas'=>array(
			'proveed' =>'C&oacute;odigo',
			'nombre'=>'Nombre',
			'rif'=>'Rif',
			'contacto'=>'Contacto'),
			'filtro'  =>array('proveed'=>'C&oacute;digo','nombre'=>'Nombre','rif'=>'Rif'),
			'p_uri'=>array(4=>'<#i#>',),
			'retornar'=>array('proveed'=>'cod_prov_<#i#>','nombre'=>'nombre_<#i#>'),

			//'retornar'=>'ca_total',
			'titulo'  =>'Buscar Beneficiario');

		$bSPRV =$this->datasis->p_modbus($mSPRV ,"<#i#>");

		$do = new DataObject("nomi");
		$do->rel_one_to_many('asignomi' , 'asignomi' , array('numero'=>'numero'));
		$do->rel_one_to_many('retenomi' , 'retenomi' , array('numero'=>'numero'));
		$do->rel_one_to_many('otrosnomi', 'otrosnomi', array('numero'=>'numero'));
		//$do->rel_pointer('asignomi','presupuesto' ,'presupuesto.codigoadm=presupuesto.codigoadm AND asignomi.fondo=presupuesto.tipo AND presupuesto.codigopres=presupuesto.codigopres ',"presupuesto.denominacion as denomi2");
		//$do->rel_pointer('asignomi','ppla' ,'asignomi.codigopres=ppla.codigo','ppla.denominacion as denomi2');
		//$do->rel_pointer('retenomi','sprv' ,'sprv.proveed=retenomi.cod_prov','sprv.nombre as nombrep');


		//$do->rel_pointer('','odirect' ,'pades.pago=odirect.numero',"odirect.total AS totalo,odirect.total2 AS total2o,odirect.reteiva AS reteivao,odirect.reten AS reteno,odirect.imptimbre AS imptimbreo, odirect.impmunicipal AS impmunicipalo");
		//$do->pointer('sprv' ,'sprv.proveed=desem.cod_prov','sprv.nombre as nombrep');

		//$do->rel_pointer('mbanc','mbanc' ,'mbanc.desem=desem.numero',"mbanc.tipo_doc as tipo_docp,mbanc.cheque as chequep,mbanc.fecha as fechap,mbanc.monto as montop,mbanc.observa as observap,mbanc.codbanc as codbancp,mbanc.status AS statusp");
		//$do->load(1);
		//print_r($do->_rel_pointer_data);
		//print_r($do->get_all());

		$edit = new DataDetails($this->tits, $do);
		$edit->back_url = site_url($this->url."filteredgrid");
		$edit->set_rel_title('pades','Rubro <#o#>');

		$status=$edit->get_from_dataobjetct('status');

		$edit->pre_process('insert'  ,'_valida');
		$edit->pre_process('update'  ,'_valida');

		$edit->post_process('insert','_post_insert');
		$edit->post_process('update','_post_update');
		$edit->post_process('delete','_post_delete');

		//**************************INICIO ENCABEZADO********************************************************************
		$edit->numero       = new inputField("N&uacute;mero", "numero");
		$edit->numero->rule = "callback_chexiste";
		$edit->numero->mode="autohide";
		if($this->datasis->traevalor('USANODIRECT')=='S'){
			$edit->numero->when=array('show');
		}else{
			$edit->numero->when=array('show','create');
		}

		$edit->fecha= new  dateonlyField("Fecha",  "fecha");
		$edit->fecha->insertValue = date('Ymd');
		$edit->fecha->size        = 12;
		if($status== 'O' )
		$edit->fecha->mode        = "autohide";
		//}else
		//$edit->fecha->rule        = 'required|chfecha';

		$edit->descrip = new textareaField("Descripci&oacute;n", 'descrip');
		$edit->descrip->rows     = 3;
		$edit->descrip->cols    = 60;
		if($status=='O' )
		$edit->descrip->mode     ="autohide";

		$edit->asig  = new inputField("Total Asignaciones", "asig");
		$edit->asig->size = 15;
		$edit->asig->readonly = true;
		$edit->asig->css_class='inputnum';
		if($status=='O' )
		$edit->asig->mode     ="autohide";

		$edit->rete  = new inputField("Total Deducciones", "rete");
		$edit->rete->size     = 15;
		$edit->rete->readonly = true;
		$edit->rete->css_class='inputnum';

		$edit->otros  = new inputField("Total Otros", "otros");
		$edit->otros->size     = 15;
		$edit->otros->readonly = true;
		$edit->otros->css_class='inputnum';


		//if($this->datasis->traevalor("USACERTICOMPRO")=='S'){
			$edit->compromiso  = new inputField("Nro Compromiso", "compromiso");
			$edit->compromiso->size=15;
			if($status=='O' )
			$edit->compromiso->mode     ="autohide";
		//}

		//************************** FIN   ENCABEZADO********************************************************************

		//**************************INICIO DETALLE DE ASIGNACIONES  *****************************************************

		$edit->itfondo = new dropdownField("F. Financiamiento","fondo_<#i#>");
		$edit->itfondo->rule   ='required';
		$edit->itfondo->db_name='fondo';
		$edit->itfondo->rel_id ='asignomi';
		$edit->itfondo->options("SELECT tipo,tipo a  FROM presupuesto WHERE tipo<>'' GROUP BY tipo ORDER BY tipo desc");
		$edit->itfondo->style="width:100px;";
		if($status=='O'){
			$edit->itfondo = new inputField("F. Financiamiento","fondo_<#i#>");
			$edit->itfondo->readonly = true;
			$edit->itfondo->rule     ='required';
			$edit->itfondo->db_name  ='fondo';
			$edit->itfondo->size     = 10;
			$edit->itfondo->rel_id ='asignomi';
		}

		$edit->itcodigoadm = new inputField("Estructura	Administrativa","codigoadm_<#i#>");
		$edit->itcodigoadm->db_name     ='codigoadm';
		$edit->itcodigoadm->rel_id      ='asignomi';
		$edit->itcodigoadm->rule        ='required';
		$edit->itcodigoadm->size        =15;
		$edit->itcodigoadm->autocomplete=false;
		if($status=='O' )
		$edit->itcodigoadm->readonly = true;
		//$edit->itcodigoadm->mode    ="autohide";

		$edit->itcodigopres = new inputField("(<#o#>) ", "codigopres_<#i#>");
		$edit->itcodigopres->rule        ='required';
		$edit->itcodigopres->size        =15;
		$edit->itcodigopres->db_name     ='codigopres';
		$edit->itcodigopres->rel_id      ='asignomi';
		$edit->itcodigopres->autocomplete=false;
		//$edit->itcodigopres->readonly =true;

		if($status=='O' )
		$edit->itcodigopres->readonly = true;
		else
		$edit->itcodigopres->append($btn);
		//$edit->itcodigopres->mode    ="autohide";

		//if($status == 'D2' || $status == 'D3')$edit->itpago->mode     = "autohide";

		//$edit->itordinal= new inputField("(<#o#>) Ordinal","ordinal_<#i#>");
		////$edit->itordinal->rule   ='required';
		//$edit->itordinal->db_name='ordinal';
		//$edit->itordinal->rel_id ='asignomi';
		//$edit->itordinal->size     =5;
		//if($status=='O' )
		//$edit->itordinal->readonly =true;
		////$edit->itordinal->mode     ="autohide";

		$edit->itdenomi= new inputField("(<#o#>) Denominacion","denomi_<#i#>");
		//$edit->itdenomi->rule   ='required';
		$edit->itdenomi->db_name='denominacion';
		$edit->itdenomi->rel_id ='asignomi';
		//$edit->itdenomi->pointer  =true;
		$edit->itdenomi->size     =40;
		///if($status=='O' )
		$edit->itdenomi->readonly =true;
		//$edit->itdenomi->mode     ="autohide";

		$edit->itmontoa = new inputField("(<#o#>) Monto", 'montoa_<#i#>');
		$edit->itmontoa->db_name   ='monto';
		//$edit->itmonto->mode      = 'autohide';
		//$edit->itmonto->when     = array('show');
		$edit->itmontoa->size      = 15;
		$edit->itmontoa->rule      ='callback_positivo';
		$edit->itmontoa->rel_id    ='asignomi';
		$edit->itmontoa->css_class ='inputnum';
		$edit->itmontoa->onchange  = "cal_asig();";
		//$edit->itmontom->pointer = true;
		if($status=='O' )
		$edit->itmontoa->readonly = true;
		//$edit->itmontoa->mode      ="autohide";
		//************************** FIN   DETALLE DE ORDENES DEPAGO*****************************************************

		//**************************INICIO DETALLE DE DE MOVIMIENTOS BANCARIOS*******************************************
		$edit->itcod_prov = new inputField("(<#o#>) Proveedor", "cod_prov_<#i#>");
		$edit->itcod_prov->rule='callback_cod_prov';//required|
		$edit->itcod_prov->size=5;
		$edit->itcod_prov->append($bSPRV);
		$edit->itcod_prov->db_name='cod_prov';
		$edit->itcod_prov->rel_id ='retenomi';

		$edit->itnombre = new inputField("(<#o#>)Nombre", 'nombre_<#i#>');
		$edit->itnombre->db_name  = 'nombre';
		$edit->itnombre->size     = 50;
		//$edit->itnombre->readonly = true;
		//$edit->itnombre->pointer  = true;
		$edit->itnombre->rel_id   ='retenomi';

		$edit->itmontor = new inputField("(<#o#>) Monto", 'montor_<#i#>');
		$edit->itmontor->db_name   ='monto';
		//$edit->itmonto->mode      = 'autohide';
		//$edit->itmonto->when     = array('show');
		$edit->itmontor->size      = 15;
		$edit->itmontor->rule      ='callback_positivo';
		$edit->itmontor->rel_id    ='retenomi';
		$edit->itmontor->css_class ='inputnum';
		$edit->itmontor->onchange  = "cal_rete();";
		//$edit->itmontom->pointer = true;

		//**************************INICIO DETALLE APORTES *******************************************
		$edit->itcod_provo = new inputField("(<#o#>) Proveedor", "cod_provo_<#i#>");
		$edit->itcod_provo->rule='callback_cod_prov';//required|
		$edit->itcod_provo->size=5;
		$edit->itcod_provo->append($bSPRV);
		$edit->itcod_provo->db_name='cod_prov';
		$edit->itcod_provo->rel_id ='otrosnomi';

		$edit->itnombreo = new inputField("(<#o#>)Nombre", 'nombreo_<#i#>');
		$edit->itnombreo->db_name  = 'nombre';
		$edit->itnombreo->size     = 50;
		$edit->itnombreo->rel_id   ='otrosnomi';

		$edit->itmontoro = new inputField("(<#o#>) Monto", 'montoro_<#i#>');
		$edit->itmontoro->db_name   ='monto';
		$edit->itmontoro->size      = 15;
		//$edit->itmontoro->rule      ='callback_positivo';
		$edit->itmontoro->rel_id    ='otrosnomi';
		$edit->itmontoro->css_class ='inputnum';
		$edit->itmontor->onchange  = "cal_otros();";
		//$edit->itmontom->pointer = true;

		//************************** FIN   DETALLE DE DE MOVIMIENTOS BANCARIOS*******************************************

		if($status=='P'){
			$edit->button_status("btn_add_asignomi" ,'Agregar Asignacion',"javascript:add_asignomi()","AS",'modify');
			$edit->button_status("btn_add_asignomi2",'Agregar Asignacion',"javascript:add_asignomi()","AS",'create');
			$action = "javascript:window.location='" .site_url($this->url.'actualizar/'.$edit->rapyd->uri->get_edited_id()). "'";
			if($this->datasis->puede(195))$edit->button_status("btn_status",'Comprometer',$action,"TR","show");
			if($this->datasis->puede(188))$edit->buttons("modify","save","delete");
		}elseif($status == 'C'){
			$action = "javascript:window.location='" .site_url($this->url.'reversar/'.$edit->rapyd->uri->get_edited_id()). "'";
			if($this->datasis->puede(195))$edit->button_status("btn_anular",'Reversar Compromiso',$action,"TR","show");
		}elseif($status == 'O'){
			if($this->datasis->puede(188))$edit->buttons("modify","save");
		}elseif($status=='p'){
			if($this->datasis->puede(188))$edit->buttons("modify","save");
		}else{
			$edit->buttons("save");
		}

		$edit->button_status("btn_add_asignomi" ,'Agregar Asignacion',"javascript:add_asignomi()","AS",'modify');
		$edit->button_status("btn_add_asignomi2",'Agregar Asignacion',"javascript:add_asignomi()","AS",'create');
		$edit->button_status("btn_add_retenomi" ,'Agregar Deducciones',"javascript:add_retenomi()","RE","create");
		$edit->button_status("btn_add_retenomi2",'Agregar Deducciones',"javascript:add_retenomi()","RE","modify");
		$edit->button_status("btn_add_otrosnomi" ,'Agregar Otro Concepto',"javascript:add_otrosnomi()","OT","create");
		$edit->button_status("btn_add_otrosnomi2",'Agregar Otro Concepto',"javascript:add_otrosnomi()","OT","modify");
		$edit->buttons("undo","back");

		$edit->build();

		$data['status']  = $status;
		$smenu['link']   = barra_menu('40F');
		$data['smenu']   = $this->load->view('view_sub_menu', $smenu,true);
		$conten["form"]  =&  $edit;
		$data['content'] = $this->load->view('view_nomi', $conten,true);
		//$data['content'] = $edit->output;
		$data['title']   = " $this->tits ";
		$data["head"]    = script('jquery.js').script('jquery-ui.js').script('plugins/jquery.numeric.pack.js').script('plugins/jquery.floatnumber.js').script('plugins/jquery.meiomask.js').style('vino/jquery-ui.css').$this->rapyd->get_head();
		$this->load->view('view_ventanas', $data);
	}

	function cod_prov($cod_prov){

		$cod_prov = $this->db->escape($cod_prov);
		$cana=$this->datasis->dameval("SELECT COUNT(*) FROM sprv WHERE proveed=$cod_prov");// AND status='O'

		if($cana>0){
			return true;
		}else{
			$this->validation->set_message('cod_prov',"El Proveedor $cod_prov no existe");
			return false;
		}
	}

	function positivo($valor){
		
		if ($valor < 0){
			$this->validation->set_message('positivo',"El campo Monto dede ser mayor a cer0(0)");
			return FALSE;
		}
  	
	}

	function validac(&$do){
		$this->rapyd->load('dataobject');

		$odirect = new DataObject("odirect");

		$numero = $do->get('numero');

		$status = $do->get('status');
		if($status !='O')
			$do->set('status','P');

		$tasig=0;$b=array();$error='';$__rpartida = array();

		for($i=0;$i < $do->count_rel('asignomi');$i++){
			$ordinal   = $do->get_rel('asignomi','ordinal'    ,$i);
			$codigopres= $do->get_rel('asignomi','codigopres' ,$i);
			$codigoadm = $do->get_rel('asignomi','codigoadm'  ,$i);
			$fondo     = $do->get_rel('asignomi','fondo'      ,$i);
			$monto     = $do->get_rel('asignomi','monto'      ,$i);

			if(in_array($codigopres.$fondo.$codigopres.$ordinal, $__rpartida)){
				$error.="La partida ($codigopres) ($fondo) ($codigoadm) ($ordinal) Esta repetida</br>";
			}
			$__rpartida[]=$codigoadm.$fondo.$codigopres.$ordinal;

			$error.=$this->itpartida($codigoadm,$fondo,$codigopres,$ordinal);
			$tasig  += $monto;
		}

		$trete=0;$monto=0;
		for($i=0;$i < $do->count_rel('retenomi');$i++){
			$monto     =  $do->get_rel('retenomi','monto'  ,$i);
			$trete  +=$monto;
		}

		$do->set('rete',$trete);
		$do->set('asig',$tasig);

		if($trete > $tasig)
			$error.="<div class='alert' >El Total de Retenciones no puede ser mayor al total de asignaciones</div>";

		$opago  = $do->get('opago');
		if(empty($error) && !empty($opago)){
			$odirect->load($opago);
			$status = $odirect->get('status');
			if($status =='K2'){
				$total2 = $odirect->get('total2');
				$odirect->set('total',($total2-$trete));
				$odirect->save();
			}

		}
		return $error;
	}

	function _valida($do){
		$error=$this->validac($do);

		if(!empty($error)){
			$do->error_message_ar['pre_ins']=$error;
			$do->error_message_ar['pre_upd']=$error;
			return false;
		}
	}

	function _valida2($do){

		$this->rapyd->load('dataobject');
		$odirect = new DataObject("odirect");

		$numero = $do->get('numero');
		$opago  = $do->get('opago');

		//$do->set('status','P');

		$tasig=0;$b=array();$error='';
		for($i=0;$i < $do->count_rel('asignomi');$i++){
			$ordinal   = $do->get_rel('asignomi','ordinal'    ,$i);
			$codigopres= $do->get_rel('asignomi','codigopres' ,$i);
			$codigoadm = $do->get_rel('asignomi','codigoadm'  ,$i);
			$fondo     = $do->get_rel('asignomi','fondo'      ,$i);
			$monto     = $do->get_rel('asignomi','monto'      ,$i);

			if(!empty($ordinal))
				$cana=$this->datasis->dameval("SELECT COUNT(*) FROM asignomi WHERE codigopres='$codigopres' AND fondo='$fondo' AND codigoadm='$codigoadm' AND ordinal='$ordinal' AND numero = $numero");// AND status='O'
			else
				$cana=$this->datasis->dameval("SELECT COUNT(*) FROM asignomi WHERE codigopres='$codigopres' AND fondo='$fondo' AND codigoadm='$codigoadm'  AND numero = $numero");// AND status='O'

			if($cana<0)
				$error.="La orden $codigoadm $fondo $codigopres $ordinal no es v&aacute;lida";

			$tasig  += $monto;
		}

		$trete=0;$monto=0;
		for($i=0;$i < $do->count_rel('retenomi');$i++){
			$monto   =  $do->get_rel('retenomi','monto'  ,$i);
			$trete  += $monto;
		}

		$do->set('rete',$trete);

		if($trete > $tasig)
			$error.="<div class='alert' >El Total de Retenciones no puede ser mayor al total de asignaciones</div>";

		if(empty($error)){
		//exit($trete);
			$odirect->load($opago);
			$status = $odirect->get('status');
			if($status =='K2'){
				$total2 = $odirect->get('total2');
				$odirect->set('total',($total2-$trete));
				$odirect->save();
			}else{
				$error.="<div class='alert' >ERROR. No se pueden Puede Modificar las deducciones debido a que el estado de la orden de pago no es por pagar</div>";
			}
		}

		if(!empty($error)){
			$do->error_message_ar['pre_ins']=$error;
			$do->error_message_ar['pre_upd']=$error;
			return false;
		}
	}

	function actualizar($numero){

		$this->rapyd->load('dataobject');

		$do = new DataObject("nomi");
		$do->rel_one_to_many('asignomi', 'asignomi', array('numero'=>'numero'));
		$do->rel_one_to_many('retenomi', 'retenomi', array('numero'=>'numero'));
		$do->load($numero);
		
		$error='';
		$status = $do->get('status');

		$tasig=0;$b=array();$error='';
		if($status=="P"){

			for($i=0;$i < $do->count_rel('asignomi');$i++){
				$ordinal   = $do->get_rel('asignomi','ordinal'    ,$i);
				$codigopres= $do->get_rel('asignomi','codigopres' ,$i);
				$codigoadm = $do->get_rel('asignomi','codigoadm'  ,$i);
				$fondo     = $do->get_rel('asignomi','fondo'      ,$i);
				$monto     = $do->get_rel('asignomi','monto'      ,$i);
				if(!($monto>0))
					$error.="Error. Los montos deben ser positivos</br>";
				
				$error.=$this->chequeapresup($codigoadm,$fondo,$codigopres,$ordinal,$monto,0,'round($monto,2) > round(($presupuesto-($comprometido+$apartado)),2)',"El Monto ($monto) es mayor al disponible para la partida ($codigoadm) ($fondo) ($codigopres)");//
			}

			if(empty($error)){

				for($i=0;$i < $do->count_rel('asignomi');$i++){
					$ordinal   = $do->get_rel('asignomi','ordinal'    ,$i);
					$codigopres= $do->get_rel('asignomi','codigopres' ,$i);
					$codigoadm = $do->get_rel('asignomi','codigoadm'  ,$i);
					$fondo     = $do->get_rel('asignomi','fondo'      ,$i);
					$monto     = $do->get_rel('asignomi','monto'      ,$i);

					$error.=$this->afectapresup($codigoadm,$fondo,$codigopres,$ordinal,$monto, 0 , 1 ,array("comprometido"));
				}

				if(empty($error)){
					$do->set('status','C');
					$do->set('fcomprome',date('Ymd'));


					for($i=0;$i < $do->count_rel('retenomi');$i++){
						$do->set_rel('retenomi','status' , 'C',$i);
					}
					//print_r($do->get_all());
					//exit('hello world');
					$do->save();
				}
			}
		}else{
			$error.="No se puede realizar la operacion para la nomina $numero";
		}


		if(empty($error)){
			logusu('nomi',"Comprometio nomina Nro $numero");
			redirect($this->url."dataedit/show/$numero");
		}else{
			logusu('nomi',"Comprometio nomina Nro $numero ERROR:$error ");
			$data['content'] = $error.anchor($this->url."dataedit/show/$numero",'Regresar');
			$data['title']   = " $this->tits ";
			$data["head"]    = $this->rapyd->get_head();
			$this->load->view('view_ventanas', $data);
		}
	}

	function reversar($numero){

		$this->rapyd->load('dataobject');

		$do = new DataObject("nomi");

		$do->rel_one_to_many('asignomi', 'asignomi', array('numero'=>'numero'));
		$do->rel_one_to_many('retenomi', 'retenomi', array('numero'=>'numero'));
		$do->load($numero);

		$status = $do->get('status');

		$tasig=0;$b=array();$error='';
		if($status=="C"){

			for($i=0;$i < $do->count_rel('asignomi');$i++){
				$ordinal   = $do->get_rel('asignomi','ordinal'    ,$i);
				$codigopres= $do->get_rel('asignomi','codigopres' ,$i);
				$codigoadm = $do->get_rel('asignomi','codigoadm'  ,$i);
				$fondo     = $do->get_rel('asignomi','fondo'      ,$i);
				$monto     = $do->get_rel('asignomi','monto'      ,$i);

				$error.=$this->chequeapresup($codigoadm,$fondo,$codigopres,$ordinal,$monto,0,'round($monto,2) > round(($comprometido-$causado),2)',"El Monto ($monto) es mayor al disponible para descomprometer para la partida ($codigopres)");

			}

			if(empty($error)){

				for($i=0;$i < $do->count_rel('asignomi');$i++){
					$ordinal   = $do->get_rel('asignomi','ordinal'    ,$i);
					$codigopres= $do->get_rel('asignomi','codigopres' ,$i);
					$codigoadm = $do->get_rel('asignomi','codigoadm'  ,$i);
					$fondo     = $do->get_rel('asignomi','fondo'      ,$i);
					$monto     = $do->get_rel('asignomi','monto'      ,$i);

					$error.=$this->afectapresup($codigoadm,$fondo,$codigopres,$ordinal,$monto,0, -1 ,array("comprometido"));

				}

				if(empty($error)){
					$do->set('status','P');
					//$do->set('fcomprome',date('Ymd'));
					$do->save();

					for($i=0;$i < $do->count_rel('retenomi');$i++){
						$do->set_rel('retenomi','status' , 'P',$i);
					}
					$do->save();
				}
			}
		}else{
			$error.="No se puede realizar la operacion para la nomina $numero";
		}

		//$this->sp_presucalc($codigoadm);

		if(empty($error)){
			logusu('nomi',"Descomprometio nomina Nro $numero");
			redirect($this->url."dataedit/show/$numero");
		}else{
			logusu('nomi',"Descomprometio nomina Nro $numero ERROR:$error ");
			$data['content'] = $error.anchor($this->url."dataedit/show/$numero",'Regresar');
			$data['title']   = " $this->tits ";
			$data["head"]    = $this->rapyd->get_head();
			$this->load->view('view_ventanas', $data);
		}
	}

	function chexiste($codigo){
		if(!empty($codigo) && !(1*$codigo>0)){
			$this->validation->set_message('chexiste',"El numero de nomina debe ser un entero positivo");
			return false;
		}

		$chek=$this->datasis->dameval("SELECT COUNT(*) FROM nomi WHERE numero=$codigo");
		if ($chek > 0){
			$this->validation->set_message('chexiste',"El codigo $codigo ya existe para otra nomina");
			return FALSE;
		}else {
  		return TRUE;
		}
	}

	function sipresrnomi(){
		$this->rapyd->load("dataform");

		$filter = new DataForm($this->url."siprescrearnomi");

		$filter->compromiso = new inputField("Numero de Compromiso", "compromiso");
		$filter->compromiso->size =10;

		$filter->submit("btnsubmit","Crear Compromiso de nomina");

		$filter->build_form();

		$data['content'] = $filter->output.anchor($this->url,'Ir atras');
		$data['title']   = "Crear Compromiso de Nomina a partir de un compromiso de sipres";
		$data["head"]    = $this->rapyd->get_head();
		$this->load->view('view_ventanas', $data);
	}

	function siprescrearnomi(){
		$error       ='';
		$salida      =anchor($this->url.'sipresrnomi','Ir a Crear Compromiso de Nomina a partir de un compromiso de sipres');
		$compromiso  = $this->input->post("compromiso");
		$compromisoe = $this->db->escape($compromiso);
		$cant        = $this->datasis->dameval("SELECT COUNT(*) FROM view_sipres WHERE compromiso=$compromisoe");
		$cnomi       = $this->datasis->dameval("SELECT numero FROM nomi WHERE compromiso=$compromisoe AND status IN ('O','C','D','E') LIMIT 1");
		$cocompra    = $this->datasis->dameval("SELECT numero FROM ocompra WHERE compromiso=$compromisoe AND status IN ('C','T','O','E') LIMIT 1");

		if(strlen($cnomi)>0)
		$error.="El compromiso $compromisoe ya existe para el compromiso de nomina numero $cnomi </br>";

		if(strlen($cocompra)>0)
		$error.="El compromiso $compromisoe ya existe para el compromiso de ordenes numero $cocompra </br>";

		if(empty($error) && $cant>0){
			$query  ="INSERT INTO nomi (numero,compromiso,fecha,descrip,status) SELECT '',compromiso,fecha,concepto,'p' FROM view_sipres WHERE compromiso=$compromisoe GROUP BY compromiso";
			if(!$this->db->query($query))
			$error.="No se Pudo Guardar el Compromiso de Nomina";
			if(empty($error)){
				$numero = $this->db->insert_id();
				$query  ="INSERT INTO asignomi  (id,numero,codigoadm,fondo,codigopres,denominacion,monto,status) SELECT '' id,$numero,b.codigoadm,c.tipo,b.codigopres,c.denominacion,b.monto,'P' status FROM view_sipres b LEFT JOIN presupuesto c ON b.codigoadm=c.codigoadm AND b.codigopres=c.codigopres WHERE b.compromiso=$compromisoe";
				if(!$this->db->query($query))
				$error.="No se Pudieron guardar las asignaciones de Nomina";
			}
		}else{
			$error.="El numero de Compromiso no existe &oacute; no ha subido del sistema sipres";
		}

		if(empty($error)){
			$this->rapyd->load("dataobject");

			$do = new DataObject("nomi");

			$do->rel_one_to_many('asignomi', 'asignomi', array('numero'=>'numero'));
			$do->rel_one_to_many('retenomi', 'retenomi', array('numero'=>'numero'));
			$do->load($numero);

			$error.=$this->validac($do);

			if(empty($error)){
				$do->save();
				redirect($this->url."actualizar/$numero");
			}else{
				$salida=anchor($this->url."dataedit/show/$numero",'Ir al Compromiso de Nomina');
			}
		}

		if(empty($error)){

		}else{
			$data['content'] = "<div class='alert'>$error</div></br>".$salida;
			$data['title']   = "Crear Compromiso de Nomina a partir de un compromiso de sipres";
			$data["head"]    = $this->rapyd->get_head();
			$this->load->view('view_ventanas', $data);
		}
	}

	function _post_insert($do){
		$numero = $do->get('numero');
		logusu('nomi',"Creo nomina $numero");
		//redirect($this->url."actualizar/$id");
	}
	function _post_update($do){
				$numero = $do->get('numero');
		logusu('nomi',"Modifico nomina $numero");
	}
	function _post_delete($do){
		$numero = $do->get('numero');
		$this->db->query("DELETE FROM nomina WHERE numero=$numero");
		
		logusu('nomi',"Elimino nomina $numero");
	}

	function instalar(){
		$query="ALTER TABLE `asignomi`  CHANGE COLUMN `codigopres` `codigopres` VARCHAR(25) NULL DEFAULT NULL  AFTER `fondo`";
		$this->db->simple_query($query);

		$query="
				CREATE TABLE `dpresu04` (
			`row_id` INT(11) NOT NULL AUTO_INCREMENT,
			`codpre` CHAR(29) NULL DEFAULT NULL,
			`secuen` INT(11) NULL DEFAULT NULL,
			`nromov` CHAR(8) NULL DEFAULT NULL,
			`codmov` CHAR(2) NULL DEFAULT NULL,
			`nordpag` CHAR(8) NULL DEFAULT NULL,
			`fecmov` DATE NULL DEFAULT NULL,
			`concep` CHAR(60) NULL DEFAULT NULL,
			`modpre` DOUBLE NULL DEFAULT NULL,
			`crdact` DOUBLE NULL DEFAULT NULL,
			`moncomp` DOUBLE NULL DEFAULT NULL,
			`saldisp` DOUBLE NULL DEFAULT NULL,
			`moncaus` DOUBLE NULL DEFAULT NULL,
			`monpago` DOUBLE NULL DEFAULT NULL,
			`seccomp` INT(11) NULL DEFAULT NULL,
			`seccaus` INT(11) NULL DEFAULT NULL,
			`codusu` CHAR(4) NULL DEFAULT NULL,
			`regfec` DATE NULL DEFAULT NULL,
			`reghor` CHAR(8) NULL DEFAULT NULL,
			PRIMARY KEY (`row_id`)
		)
		ENGINE=MyISAM
		ROW_FORMAT=DEFAULT
		AUTO_INCREMENT=1";

		$this->db->simple_query($query);

		$query="ALTER TABLE `nomi`  ADD COLUMN `compromiso` VARCHAR(12) NULL ";
		$this->db->simple_query($query);

		$query="ALTER TABLE `nomi` 	ADD COLUMN `otros` DECIMAL(19,2) NULL DEFAULT '";
		$this->db->simple_query($query);

		$query="
		CREATE TABLE `otrosnomi` (
			`id` INT(11) NOT NULL AUTO_INCREMENT,
			`numero` INT(11) NOT NULL DEFAULT '0',
			`nomina` INT(11) NULL DEFAULT NULL,
			`codigoadm` VARCHAR(15) NULL DEFAULT NULL,
			`fondo` VARCHAR(20) NULL DEFAULT NULL,
			`codigopres` VARCHAR(17) NULL DEFAULT NULL,
			`ordinal` CHAR(3) NULL DEFAULT NULL,
			`cod_prov` VARCHAR(5) NULL DEFAULT NULL,
			`monto` DECIMAL(19,2) NULL DEFAULT NULL,
			`opago` INT(1) NULL DEFAULT NULL,
			`status` CHAR(1) NULL DEFAULT 'P',
			`nombre` VARCHAR(100) NULL DEFAULT NULL,
			`mbanc` INT(11) NULL DEFAULT NULL,
			PRIMARY KEY (`id`)
		)
		COLLATE='utf8_general_ci'
		ENGINE=MyISAM;
		";
		$this->db->simple_query($query);
	}

}
