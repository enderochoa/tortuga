<?php
require_once(BASEPATH.'application/controllers/presupuesto/common.php');
class reinte extends Common {
	
	var $url  = "presupuesto/reinte/";
	var $tits = "Reintegro Presupuestario";
	var $titp = "Reintegros Presupuestarios";

	function reinte(){
		parent::Controller();
		$this->load->library("rapyd");
		$this->datasis->modulo_id(283,1);
	}
	
	function index(){
		redirect($this->url."filteredgrid");
	}
		
	function filteredgrid(){
		//$this->datasis->modulo_id(24,1);
		$this->rapyd->load("datafilter","datagrid");
		
		$link=site_url('presupuesto/requisicion/getadmin');
		$script='
			$(function() {
				$(".inputnum").numeric(".");
			
			function get_uadmin(){
				$.post("'.$link.'",{ uejecuta:$("#uejecuta").val() },function(data){$("#td_uadministra").html(data);})
			}
		';

		$filter = new DataFilter("");
		
		$filter->script($script);
		
		$filter->db->select(array("a.numero numero","a.fecha fecha","a.uejecuta","b.nombre uejecuta2","concepto","total","status"));
		$filter->db->from("reinte a");
		$filter->db->join("uejecutora b" ,"a.uejecuta=b.codigo","LEFT");
		$filter->db->join("uadministra c","a.uadministra=b.codigo","LEFT");
		
		$filter->numero = new inputField("N&uacute;mero", "numero");
		$filter->numero->size=15;
		
		$filter->fecha = new dateonlyField("Fecha", "fecha","d/m/Y");
		$filter->fecha->size=12;
		
		$filter->uejecuta = new dropdownField("U.Ejecutora", "uejecuta");
		$filter->uejecuta->option("","Seccionar");
		$filter->uejecuta->options("SELECT codigo,nombre FROM uejecutora ORDER BY nombre");
		$filter->uejecuta->onchange = "get_uadmin();";
		
		$filter->buttons("reset","search");
		$filter->build();
		$uri  = anchor($this->url.'dataedit/show/<#numero#>'  ,'<#numero#>');
		
		function sta($sta){
			switch($sta){
				case 'P':return 'Pendiente';
				case 'E2':return 'Pagado';
				case 'O2':return 'O. Pago';
				case 'T2':return 'Causado';
				case 'C2':return 'Comprometido';
				case 'C1':return 'Causado';
				case 'T1':return 'O. Pago';
				case 'O1':return 'Pagado';
//				case 'E1':return 'Comprometido';
			}
		}
		
		$grid = new DataGrid("Haz Click en cualquier n&uacute;mero de Documento para verlo");
		$grid->order_by("numero","desc");
		$grid->per_page = 20;
		$grid->use_function('substr','str_pad','sta');
		
		$grid->column_orderby("N&uacute;mero"         ,$uri                                             ,"numero"        );
		$grid->column_orderby("Fecha"                 ,"<dbdate_to_human><#fecha#></dbdate_to_human>"   ,"fecha"         ,"align='center'");
		$grid->column_orderby("Unidad Ejecutora"      ,"uejecuta2"                                      ,"uejecuta2"     ,"align='left'  ");
		$grid->column_orderby("Concepto"              ,"concepto"                                       ,"concepto"      ,"align='left'  ");
		$grid->column_orderby("Total"                 ,"total"                                          ,"total"         ,"align='right' ");
		$grid->column_orderby("Estado"                ,"<sta><#status#></sta>"                         ,"status"         ,"align='right' ");
		
		if($this->datasis->puede(319))
		$grid->add($this->url."dataedit/create");
		$grid->build();
		
		$data['filtro']  = $filter->output;
		$data['content'] = $grid->output;
		$data['title']   = $this->titp;
		$data["head"]    = $this->rapyd->get_head().script('jquery.js').script('jquery-ui.js').script("plugins/jquery.numeric.pack.js").script("plugins/jquery.json.min.js").script('plugins/jquery.tooltip.js').script('plugins/jquery.autocomplete.js').style('jquery.autocomplete.css').style('jquery.tooltip.css').style('tooltip.css').style('vino/jquery-ui.css');
		$this->load->view('view_ventanas', $data);
	}

	function dataedit(){
		//$this->datasis->modulo_id(115,1);
		$this->rapyd->load('dataobject','datadetails');
		
		$modbus=array(
			'tabla'   =>'v_presaldo',
			'columnas'=>array(
				'codigoadm'   =>'Est. Admin',
				'fondo'       =>'Fondo',
				'codigo'      =>'Partida',
				'ordinal'     =>'Ordinal',
				'denominacion'=>'Denominaci&oacute;n',
				'saldo'       =>'Saldo'
				),
			'filtro'  =>array(
				'codigoadm'   =>'Est. Admin',
				'fondo'       =>'Fondo',
				'codigo'      =>'Partida',
				'ordinal'     =>'Ord',
				'denominacion'=>'Denominaci&oacute;n'
				),
			'retornar'=>array(
				'codigoadm'   =>'codigoadm_<#i#>',
				'fondo'       =>'itfondo_<#i#>',
				'codigo'      =>'codigopres_<#i#>',
				'ordinal'     =>'ordinal_<#i#>',
				'denominacion'=>'denomi_<#i#>'),
			'where'=>'movimiento = "S"',
			'p_uri'=>array(4=>'<#i#>',),
			'titulo'  =>'Busqueda de partidas');
		$btn=$this->datasis->p_modbus($modbus,'<#i#>');
		
		$do = new DataObject("reinte");
		$do->rel_one_to_many('itreinte', 'itreinte', array('numero'=>'numero'));
		$do->rel_pointer('itreinte','v_presaldo' ,'itreinte.codigoadm=v_presaldo.codigoadm AND itreinte.fondo=v_presaldo.fondo AND itreinte.codigopres=v_presaldo.codigo AND itreinte.ordinal=IF(itreinte.ordinal>0,v_presaldo.ordinal,itreinte.ordinal)',"v_presaldo.denominacion as denomi2");
		//$do->order_by('itreinte.id');

		$edit = new DataDetails($this->tits, $do);
		$edit->back_url = site_url($this->url."filteredgrid");
		$edit->set_rel_title('itreinte','Rubro <#o#>');
		
		$status=$edit->get_from_dataobjetct('status');

		$edit->pre_process('insert' ,'_valida');
		$edit->pre_process('update' ,'_valida');
		$edit->post_process('insert','_post_insert');
		$edit->post_process('update','_post_update');
		$edit->post_process('delete','_post_delete');
		
		//**************************INICIO ENCABEZADO********************************************************************
		$edit->numero       = new inputField("N&uacute;mero", "numero");
		//$edit->numero->rule = "callback_chexiste";
		$edit->numero->mode="autohide";
		$edit->numero->when=array('show','modify');
		
		$edit->fecha= new  dateonlyField("Fecha",  "fecha","d/m/Y");
		$edit->fecha->insertValue = date('Y-m-d');
		$edit->fecha->size        = 12;
		//$edit->fecha->mode        = "autohide";
//		$edit->fecha->when        =array('show','modify');

		$edit->concepto = new textareaField("Concepto", 'concepto');
		$edit->concepto->rows    = 1;
		$edit->concepto->cols    = 80;
		
		$edit->comping = new inputField("Comprobante de Ingreso", "comping");
		//$edit->comping->when     =array('show');

		$edit->uejecuta = new dropdownField("Unidad Ejecutora", "uejecuta");
		$edit->uejecuta->options("SELECT codigo,CONCAT(codigo,' ',nombre)a FROM uejecutora ORDER BY nombre");
		$edit->uejecuta->onchange = "get_uadmin();";
		$edit->uejecuta->rule     = "required";
		$edit->uejecuta->tip      = "Seleccione el nombre de la Direcci&oacute;n a la cual pertenece, haciendo click en la flecha del lado derecho del campo</br> Ejemplo: Direcci&oacute;n de Administraci&oacute;n";
		$edit->uejecuta->style    = "width:500px";
		
		$edit->total = new inputField("Monto Total", "total");
		$edit->total->css_class='inputnum';
		$edit->total->readonly =true;
		$edit->total->rule     ='numeric';
		$edit->total->size     =15;
		
		$edit->status = new inputField("Estado", "status");
		$edit->status->when     =array('show');
		

		//**************************INICIO DETALLE **********************************************************************

		$edit->itfondo = new dropdownField("F. Financiamiento","itfondo_<#i#>");
		$edit->itfondo->size   =10;
		$edit->itfondo->rule   ='required';
		$edit->itfondo->db_name='fondo';
		$edit->itfondo->rel_id ='itreinte';
		$edit->itfondo->options("SELECT tipo,tipo a  FROM presupuesto WHERE tipo<>'' GROUP BY tipo ORDER BY tipo desc");
		$edit->itfondo->style="width:100px;";

		$edit->itcodigoadm = new inputField("Estructura	Administrativa","codigoadm_<#i#>");
		$edit->itcodigoadm->size   =15;
		$edit->itcodigoadm->db_name='codigoadm';
		$edit->itcodigoadm->rel_id ='itreinte';
		$edit->itcodigoadm->rule   ='required';

		$edit->itcodigopres = new inputField("(<#o#>) Partida", "codigopres_<#i#>");
		$edit->itcodigopres->rule='callback_repetido|required';
		$edit->itcodigopres->size=15;
		$edit->itcodigopres->append($btn);
		$edit->itcodigopres->db_name='codigopres';
		$edit->itcodigopres->rel_id ='itreinte';
		$edit->itcodigopres->insertValue ="4";
		
		$edit->itordinal = new inputField("(<#o#>) Ordinal", "ordinal_<#i#>");
		$edit->itordinal->db_name  ='ordinal';
		$edit->itordinal->maxlength=3;
		$edit->itordinal->size     =5;
		$edit->itordinal->rel_id   ='itreinte';

		$edit->denomi = new textareaField("(<#o#>) Denominaci&oacute;n", "denomi_<#i#>");
		$edit->denomi->db_name ='denomi2';
		$edit->denomi->rel_id  ='itreinte';
		$edit->denomi->cols    =20;
		$edit->denomi->rows    =1;
		$edit->denomi->readonly=true;
		$edit->denomi->pointer =true;

		$edit->itmonto = new inputField("(<#o#>) monto", "monto_<#i#>");
		$edit->itmonto->rule      ='required|callback_positivo';
		$edit->itmonto->db_name   ='monto';
		$edit->itmonto->rel_id    ='itreinte';
		$edit->itmonto->size      =15;
		$edit->itmonto->css_class ='inputnum';
		$edit->itmonto->onchange  ='cal_total(<#i#>);';
	
		//************************** FIN   DETALLE DE *******************************************************************
		$status=$edit->get_from_dataobjetct('status');
		$v=$t=0;
		switch($status){
			case 'P':{
					$edit->buttons("delete","modify");
					$action = "javascript:window.location='" .site_url($this->url.'/presup/'.$edit->rapyd->uri->get_edited_id()). "/E2'";
					if($this->datasis->puede(314))
					$edit->button_status("btn_status",'Reintegrar Pagado',$action,"TR","show");
					
					$action = "javascript:window.location='" .site_url($this->url.'/presup/'.$edit->rapyd->uri->get_edited_id()). "/F2'";
					if($this->datasis->puede(379))
					$edit->button_status("btn_status",'Reintegrar',$action,"TR","show");
				break;
			}
			case 'E2':{

				$action = "javascript:window.location='" .site_url($this->url.'/presup/'.$edit->rapyd->uri->get_edited_id()). "/O2'";
				if($this->datasis->puede(315))
				$edit->button_status("btn_status",'Reintegrar Ordenado Pago',$action,"TR","show");
				$action = "javascript:window.location='" .site_url($this->url.'/presup/'.$edit->rapyd->uri->get_edited_id()). "/E1'";
				if($this->datasis->puede(314))
				$edit->button_status("btn_status",'Anular Reintegro de Pagado',$action,"TR","show");
				break;
			}
			case 'O2':{
				$action = "javascript:window.location='" .site_url($this->url.'/presup/'.$edit->rapyd->uri->get_edited_id()). "/T2'";
				if($this->datasis->puede(316))
				$edit->button_status("btn_status",'Reintegrar Causado',$action,"TR","show");
				$action = "javascript:window.location='" .site_url($this->url.'/presup/'.$edit->rapyd->uri->get_edited_id()). "/O1'";
				if($this->datasis->puede(315))
				$edit->button_status("btn_status",'Reversar Reintegro de Ordenado Pago',$action,"TR","show");
				break;
			}
			case 'T2':{
				$action = "javascript:window.location='" .site_url($this->url.'/presup/'.$edit->rapyd->uri->get_edited_id()). "/C2'";
				if($this->datasis->puede(317))
				$edit->button_status("btn_status",'Reintegrar Compromiso',$action,"TR","show");
				$action = "javascript:window.location='" .site_url($this->url.'/presup/'.$edit->rapyd->uri->get_edited_id()). "/T1'";
				if($this->datasis->puede(316))
				$edit->button_status("btn_status",'Reversar Reintegro de Causado',$action,"TR","show");
				break;
			}
			case 'C2':{
				$action = "javascript:window.location='" .site_url($this->url.'/presup/'.$edit->rapyd->uri->get_edited_id()). "/C1'";
				if($this->datasis->puede(317))
				$edit->button_status("btn_status",'Reversar Reintegro de Compromiso',$action,"TR","show");
				
				$action = "javascript:window.location='" .site_url($this->url.'/presup/'.$edit->rapyd->uri->get_edited_id()). "/F1'";
				if($this->datasis->puede(379))
				$edit->button_status("btn_status",'Reversar',$action,"TR","show");
				
				break;
			}
			case 'C1':{
				$edit->buttons("delete","modify");
				$action = "javascript:window.location='" .site_url($this->url.'/presup/'.$edit->rapyd->uri->get_edited_id()). "/C2'";
				if($this->datasis->puede(317))
				$edit->button_status("btn_status",'Reintegrar Compromiso',$action,"TR","show");
				$action = "javascript:window.location='" .site_url($this->url.'/presup/'.$edit->rapyd->uri->get_edited_id()). "/T1'";
				if($this->datasis->puede(316))
				$edit->button_status("btn_status",'Reversar Reintegro de Causado',$action,"TR","show");
				break;
			}
			case 'T1':{
				$action = "javascript:window.location='" .site_url($this->url.'/presup/'.$edit->rapyd->uri->get_edited_id()). "/T2'";
				if($this->datasis->puede(316))
				$edit->button_status("btn_status",'Reintegrar Causado',$action,"TR","show");
				$action = "javascript:window.location='" .site_url($this->url.'/presup/'.$edit->rapyd->uri->get_edited_id()). "/O1'";
				if($this->datasis->puede(315))
				$edit->button_status("btn_status",'Reversar Reintegro de Ordenado Pago',$action,"TR","show");
				break;
			}
			case 'O1':{
				$action = "javascript:window.location='" .site_url($this->url.'/presup/'.$edit->rapyd->uri->get_edited_id()). "/O2'";
				if($this->datasis->puede(315))
				$edit->button_status("btn_status",'Reintegrar Ordenado Pago',$action,"TR","show");
				$action = "javascript:window.location='" .site_url($this->url.'/presup/'.$edit->rapyd->uri->get_edited_id()). "/E1'";
				if($this->datasis->puede(314))
				$edit->button_status("btn_status",'Reversar Reintegro de Pagado',$action,"TR","show");
				break;
			}
			case 'E1':{
				$v = 'A';
				$t = 'ANULAR';
				break;
			}
		}
		
		$edit->buttons("undo", "back","add_rel","save");
		if($this->datasis->puede(319))
		$edit->buttons("add");
//		if($status=='P')

		$edit->build();

		$smenu['link']   = barra_menu('322');
		$data['smenu']   = $this->load->view('view_sub_menu', $smenu,true);
		$conten["form"]  =&  $edit;
		$data['content'] = $this->load->view('view_reinte', $conten,true);
		//$data['content'] = $edit->output;
		$data['title']   = $this->tits;
		$data["head"]    = $this->rapyd->get_head().script('jquery.js').script('jquery-ui.js').script('plugins/jquery.meiomask.js').script("plugins/jquery.numeric.pack.js").script("plugins/jquery.json.min.js").script('plugins/jquery.tooltip.js').script('plugins/jquery.autocomplete.js').style('jquery.autocomplete.css').style('jquery.tooltip.css').style('tooltip.css').style('vino/jquery-ui.css');//

		$this->load->view('view_ventanas', $data);
	}

	function _valida($do){
		$error = '';
		$numero = $do->get('numero');
		$do->set('status','P');
		
		//$do->set('fecha',date('Y-m-d'));
		$usuario = $this->session->userdata('usuario');
		$do->set('usuario',$usuario);
		$__rpartida=array();
		$total=0;
		
		if(empty($numero)){
			$ntransac = $this->datasis->fprox_numero('ntransac');
			$do->set('numero','_'.$ntransac);
			$do->pk    =array('numero'=>'_'.$ntransac);
		}

		for($i=0;$i < $do->count_rel('itreinte');$i++){
			if(empty($numero))
			$do->set_rel('itreinte','numero','_'.$ntransac,$i);
			
			$codigopres       = $do->get_rel('itreinte','codigopres' ,$i);
			$ordinal          = $do->get_rel('itreinte','ordinal'    ,$i);
			$codigoadm        = $do->get_rel('itreinte','codigoadm'  ,$i);
			$fondo            = $do->get_rel('itreinte','fondo'      ,$i);
			$monto            = $do->get_rel('itreinte','monto'      ,$i);
			$total+=$monto;
			$error.=$this->itpartida($codigoadm,$fondo,$codigopres,$ordinal);
						
			if(in_array($codigoadm.$fondo.$codigopres.$ordinal, $__rpartida))
				$error.="La partida ($codigopres) ($fondo) ($codigoadm) ($ordinal) Esta repetida</br>";
			
			$__rpartida[]=$codigopres.$fondo.$codigopres.$ordinal;
		}
		
		$do->set("total",$total);
		
		if(!empty($error)){
			$do->error_message_ar['pre_ins']=$error;
			$do->error_message_ar['pre_upd']=$error;
			return false;
		}
	}
	
	function presup($id,$accion){
		$this->rapyd->load('dataobject');
		
		$do = new DataObject("reinte");
		$do->rel_one_to_many('itreinte', 'itreinte', array('numero'=>'numero'));
		$do->load($id);
		
		$status   =$do->get('status');
		$error    ='';
		$factor   = 1;
		$formula  ='round($monto,2) > $disponible = ';
		
		switch($accion){
			case 'F2':{
				$factor  =-1;
				$campo   =array('pagado','opago','causado','comprometido');
				$formula .='round($pagado,2)';
				if($status!='P')
				$error  .='ERROR. No puede realizar la operacion para el documento en este momento.';
				break;
			}
			case 'E2':{
				$factor  =-1;
				$campo   =array('pagado');
				$formula .='round($pagado,2)';
				if($status!='P')
				$error  .='ERROR. No puede realizar la operacion para el documento en este momento.';
				break;
			}
			case 'O2':{
				$factor  =-1;
				$campo   =array('opago');
				$formula.='round($opago-$pagado,2)';
				if($status!='E2' && $status!='O1')
				$error  .='ERROR. No puede realizar la operacion para el documento en este momento.';
				break;
			}
			case 'T2':{
				$factor  =-1;
				$campo =array('causado');
				$formula.='round($causado-$opago,2)';
				if($status!='O2' && $status!='T1')
				$error  .='ERROR. No puede realizar la operacion para el documento en este momento.';
				break;
			}
			case 'C2':{
				$factor  =-1;
				$campo   =array('comprometido');
				$formula.='round($comprometido-$causado,2)';
				if($status!='T2' && $status!='C1')
				$error  .='ERROR. No puede realizar la operacion para el documento en este momento.';
				break;
			}
			case 'E1':{
				$factor  =1;
				$campo   =array('pagado');
				$formula .='round($opago-$pagado)';
				if($status!='E2' && $status!='O1')
				$error  .='ERROR. No puede realizar la operacion para el documento en este momento.';
				break;
			}
			case 'O1':{
				$factor  =1;
				$campo   =array('opago');
				$formula.='round($causado-$opago,2)';
				if($status!='O2' && $status!='T1')
				$error  .='ERROR. No puede realizar la operacion para el documento en este momento.';
				break;
			}
			case 'T1':{
				$factor  =1;
				$campo   =array('causado');
				$formula.='round($comprometido-$causado,2)';
				if($status!='T2' && $status!='C1')
				$error  .='ERROR. No puede realizar la operacion para el documento en este momento.';
				break;
			}
			case 'C1':{
				$factor  =1;
				$campo   =array('comprometido');
				$formula.='round($presupuesto-$comprometido,2)';
				if($status!='C2')
				$error  .='ERROR. No puede realizar la operacion para el documento en este momento.';
				break;
			}
			case 'F1':{
				$factor  =1;
				$campo   =array('pagado','opago','causado','comprometido');
				$formula.='round($presupuesto-$comprometido,2)';
				if($status!='C2')
				$error  .='ERROR. No puede realizar la operacion para el documento en este momento.';
				break;
			}
			
		}
		
		if(empty($error)){
			for($i=0;$i <   $do->count_rel('itreinte');$i++){
				$codigopres = $do->get_rel(  'itreinte','codigopres' ,$i);
				$monto      = $do->get_rel(  'itreinte','monto'      ,$i);
				$ordinal    = $do->get_rel(  'itreinte','ordinal'    ,$i);
				$codigoadm  = $do->get_rel(  'itreinte','codigoadm'  ,$i);
				$fondo      = $do->get_rel(  'itreinte','fondo'      ,$i);
				
				$error.=$this->chequeapresup($codigoadm,$fondo,$codigopres,$ordinal,$monto,0,$formula,$formula." $fondo $codigoadm $codigopres");
			}
			
			if(empty($error)){
				if($accion=='E2' || $accion=='F2'){
					if(strpos($id,'_')===0){
						$contador = $this->datasis->fprox_numero('nreinte');
						$do->set('numero',$contador);
						$id=$contador;
					}
				}
				
				for($i=0;$i < $do->count_rel('itreinte');$i++){
					if($accion=='E2' || $accion=='F2'){
						$do->set_rel('itreinte','id'    ,''       ,$i);
						$do->set_rel('itreinte','numero',$contador,$i);
					}
					$codigopres = $do->get_rel('itreinte','codigopres' ,$i);
					$monto      = $do->get_rel('itreinte','monto'      ,$i);
					$ordinal    = $do->get_rel('itreinte','ordinal'    ,$i);
					$codigoadm  = $do->get_rel('itreinte','codigoadm'  ,$i);
					$fondo      = $do->get_rel('itreinte','fondo'      ,$i);
					
					$error.=$this->afectapresup($codigoadm,$fondo,$codigopres,$ordinal,$monto,0, $factor ,$campo);
				}
				if(empty($error)){
					 if($accion=='F2')
					 $accion='C2';
					 elseif($accion=='F1')
					 $accion='C1';
					 
					$do->set('status',$accion);
					$do->save();
				}
			}
		}else{
			$error="<div class='alert'><p>$error</p></div>";
		}
		
		if(empty($error)){
			logusu('audis',"actualizo $campo numero $id");
			redirect($this->url."/dataedit/show/$id");
		}else{
			logusu('audis',"actualizo $campo numero $id con error $error");
			$data['content'] = $error.anchor($this->url."/dataedit/show/$id",'Regresar');
			$data['title']   = " Aumentos y Disminuciones ";
			$data["head"]    = $this->rapyd->get_head();
			$this->load->view('view_ventanas', $data);
		}
	}
	
	function _post_insert($do){
		$numero = $do->get('numero');
		logusu('reinte',"Creo documento Nro $numero");
		//redirect($this->url."actualizar/$numero");
	}
	
	function _post_update($do){
		$numero = $do->get('numero');
		logusu('reinte'," Modifico documento Nro $numero");
		//redirect($this->url."actualizar/$numero");
	}
	
	function _post_delete($do){
		$numero = $do->get('numero');
		logusu('reinte'," Elimino documento Nro $numero");
	}
	
	function instalar(){
		$this->db->simple_query("
		CREATE TABLE `reinte` (
			`numero` VARCHAR(12) NOT NULL DEFAULT '',
			`fecha` DATE NULL DEFAULT NULL,
			`uejecuta` CHAR(4) NULL DEFAULT NULL COLLATE 'utf8_general_ci',
			`uadministra` CHAR(4) NULL DEFAULT NULL COLLATE 'utf8_general_ci',
			`concepto` TINYTEXT NULL COLLATE 'utf8_general_ci',
			`status` CHAR(2) NULL DEFAULT NULL COLLATE 'utf8_general_ci',
			`usuario` VARCHAR(12) NULL DEFAULT NULL COMMENT 'aa' COLLATE 'utf8_general_ci',
			`total` VARCHAR(45) NULL DEFAULT NULL COLLATE 'utf8_general_ci',
			`comping` VARCHAR(12) NULL,
			PRIMARY KEY (`numero`),
			INDEX `uejecuta` (`uejecuta`)
		)
		COMMENT='Reintegros presupuestarios'
		COLLATE='utf8_general_ci'
		ENGINE=MyISAM
		ROW_FORMAT=DEFAULT");
		$this->db->simple_query("
		CREATE TABLE `itreinte` (
			`id` INT(11) NOT NULL AUTO_INCREMENT,
			`numero` VARCHAR(12) NULL DEFAULT NULL,
			`codigoadm` VARCHAR(12) NULL DEFAULT NULL,
			`fondo` VARCHAR(20) NULL DEFAULT NULL,
			`codigopres` VARCHAR(17) NULL DEFAULT NULL,
			`ordinal` CHAR(3) NULL DEFAULT NULL,
			`monto` DECIMAL(19,2) NULL DEFAULT NULL,
			PRIMARY KEY (`id`),
			INDEX `numero` (`numero`),
			INDEX `codigopres` (`codigopres`)
		)
		COLLATE='utf8_general_ci'
		ENGINE=MyISAM
		ROW_FORMAT=DEFAULT
		AUTO_INCREMENT=0");
		
		$query="ALTER TABLE `reinte` CHANGE COLUMN `uejecuta` `uejecuta` VARCHAR(8) NULL DEFAULT NULL AFTER `fecha`";
		$this->db->simple_query($query);
		$query="ALTER TABLE `reinte` CHANGE COLUMN `uadministra` `uadministra` VARCHAR(8) NULL DEFAULT NULL AFTER `uejecuta`";
		$this->db->simple_query($query);
	}
}
?>
