<?php
require_once(BASEPATH.'application/controllers/presupuesto/common.php');
class Pagonom extends Common {
	var $titp='Ordenes de Pago de N&oacute;mina';
	var $tits='Orden de Pago de N&oacute;mina';
	var $url ='presupuesto/pagonom/';

	function Pagonom(){
		parent::Controller();
		$this->load->library("rapyd");
	}

	function index(){
		redirect($this->url."filteredgrid");
	}

	function filteredgrid(){
		$this->datasis->modulo_id(190,1);
		$this->rapyd->load("datafilter2","datagrid");
		//$this->rapyd->uri->keep_persistence();
				
		$filter = new DataFilter2("",'v_pagonom');
		
		$filter->opago = new inputField("Orden de Pago", "opago");
		$filter->opago->db_name="opago";
		$filter->opago->size   =12;
		//$filter->opago->clause="likerigth";
		
		$filter->numero = new inputField("N&oacute;mina", "numero");
		$filter->numero->db_name="numero";
		$filter->numero->size   =12;
		$filter->numero->db_name='numero';
		//$filter->numero->clause="likerigth";
		
		$filter->descrip = new inputField("Descripci&oacute;n", "descrip");
		$filter->descrip->db_name="descrip";
		$filter->descrip->size  =10;
		
		$filter->fecha = new dateonlyField("Fecha", "fecha");
		$filter->fecha->db_name="fecha";
		$filter->fecha->dbformat = 'Y-m-d';
		$filter->fecha->size=12;
		
		$filter->status = new dropdownField("Estado","status");
		$filter->status->db_name="status";		
		$filter->status->option("" ,""              );
		$filter->status->option("P","Pendiente"     );
		$filter->status->option("C","Comprometido"  );		
		$filter->status->option("D","Orden Asignada");
		$filter->status->option("K1","Por Causar" );
		$filter->status->option("K2","Causado"    );		
		$filter->status->option("k3","Pagado"     );
		$filter->status->style="width:150px";
		
		$filter->buttons("reset","search");
		
		$filter->build();

		$atts = array(
		 'width'      => '800',
		 'height'     => '600',
		 'scrollbars' => 'yes',
		 'status'     => 'yes',
		 'resizable'  => 'yes',
		 'screenx'    => '0',
		 'screeny'    => '0'
		 );
		 
		$uri = anchor_popup('nomina/nomi/dataedit/show/<#numero#>','<#numero#>',$atts);
		
		function pago($opago,$status,$nomi){
			$uri="";
			
			if( in_array($status,array('K','K1','K2','K3','KA'))  ){
				$uri = anchor("presupuesto/pagonom/dataedit/show/$opago",$opago);
			}elseif($status =='C'){
				$uri = anchor("presupuesto/pagonom/creapago/$nomi/",'Pagar');
			}elseif($status =='P'){
				$uri = '';
			}
			
			return $uri;
		}
		
		//n anchor('presupuesto/pagonom/creapago/<#opago#>','<#opago#>');
		
		function sta($status){
			switch($status){
				case "P":return "Pendiente"       ;break;
				case "C":return "Comprometido"    ;break;
				case "D":return "Orden Asignada"  ;break;
				case "K":return "Orden Asignada"  ;break;
				case "K1":return "Por Causar"     ;break;
				case "K2":return "Causado"        ;break;
				case "K3":return "Pagado"         ;break;
				case "KA":return "Anulado"        ;break;
				
			}
			return $status;
		}
		
		$iralfiltro = "";
		
		$grid = new DataGrid($iralfiltro);
		//$grid->order_by(" fecha desc,opago desc,numero desc");
		$grid->per_page = 20;
		$grid->use_function('substr','str_pad','sta','pago');
		
		$grid->column_orderby("Orden de Pago"    ,"<pago><#opago#>|<#status#>|<#numero#></pago>","opago"         );
		$grid->column_orderby("N&oacute;mina"    ,$uri                                          ,"numero"        );
		$grid->column_orderby("Fecha"            ,"<dbdate_to_human><#fecha#></dbdate_to_human>","fecha"         ,"align='center'"      );
		$grid->column_orderby("Asignaciones"     ,"<nformat><#asig#></nformat>"                 ,"asig"          ,"align='right'       ");
		$grid->column_orderby("Deducciones"      ,"<nformat><#rete#></nformat>"                 ,"rete"          ,"align='left'  NOWRAP");
		$grid->column_orderby("Estado"           ,"<sta><#status#></sta>"                       ,"status"        ,"align='center'NOWRAP");
		//$grid->column(" "                ,"<action><#status#>|<#numero#></action>"      ,"align='center'");
		
		
		
		$grid->build();
		//echo $grid->db->last_query();
		
		$data['filtro']  = $filter->output;
		$data['content'] = $grid->output;
		$data['script'] = script("jquery.js")."\n";
		//$data['content'] = $filter->output.$grid->output;
		$data['title']   = "$this->titp";
		$data["head"]    = $this->rapyd->get_head();
		$this->load->view('view_ventanas', $data);
	}
	
	function dataedit(){
		//$this->datasis->modulo_id(116,1);
		$this->rapyd->load('dataobject','datadetails');

		$mSPRV=array(
			'tabla'   =>'sprv',
			'columnas'=>array(
				'proveed' =>'C&oacute;odigo',
				'nombre'  =>'Nombre',
				'grupo'   =>'Grupo',
				'rif'     =>'Rif',
				'contacto'=>'Contacto'
			),
			'filtro'  =>array(
				'proveed'=>'C&oacute;digo',
				'nombre' =>'Nombre'       ,
				'rif'    =>'Rif'          ,
				'grupo'   =>'Grupo'
			),
			'retornar'=>array(
				'proveed'=>'cod_prov',
				'nombre' =>'nombrep'
			),
			'titulo'  =>'Buscar Beneficiario');
		
		$bSPRV=$this->datasis->modbus($mSPRV );
			
		$do = new DataObject("odirect");
		$do->pointer('sprv'   ,'sprv.proveed = odirect.cod_prov','sprv.nombre as nombrep','LEFT');
		$do->rel_one_to_many('itodirect', 'itodirect', array('numero'=>'numero'));
		
		//exit('hello world');	
		$edit = new DataDetails($this->tits, $do);
		$edit->back_url = site_url($this->url."filteredgrid/index");
		
		$edit->set_rel_title('itodirect','Rubro <#o#>');
		//$edit->makerel  = true;
	
		$edit->pre_process('update'  ,'_valida');
		$edit->pre_process('insert'  ,'_valida');
		$edit->post_process('insert','_post_insert');
		$edit->post_process('update','_post_update');
		$edit->post_process('delete','_post_delete');
	
		$edit->numero  = new inputField("N&uacute;mero", "numero");
		//$edit->numero->mode="autohide";
		if($this->datasis->traevalor('USANODIRECT')=='S'){
			$edit->numero->when=array('show');
			$edit->numero->mode="autohide";
		}else{
			$edit->numero->when=array('show','create','modify');
		}

		//$numero=$edit->_dataobject->get('numero');

		//$USANODIRECT = $this->datasis->traevalor('USANODIRECT');
		//
		//$edit->controlfac  = new inputField("Numero de Orden de Pago", "controlfac");
		//$edit->controlfac->size = 10;
		//if($USANODIRECT=='S'){
		//	$edit->controlfac->status="hidden";	
		//	$edit->controlfac->when=array();	
		//}
		
		
		//$edit->_dataobject->data['numeroante']=$numero;
		//$edit->_dataobject->set('controlfac',$numero);

		//echo "ass".$controlfac=$edit->getval('controlfac')."as";
	
		$edit->tipo = new dropdownField("Orden de ", "tipo");
		//$edit->tipo->option("Compra"  ,"Compra");
		//$edit->tipo->option("Servicio","Servicio");
		//$edit->tipo->option("T","Transferencia");
		$edit->tipo->option("N","Nomina");
		$edit->tipo->style="width:100px;";
		
		$edit->nomina = new inputField("N&oacute;mina", 'nomina');		
		$edit->nomina->size     = 15;
		
		$edit->observa = new inputField("", 'denomin');
		$edit->observa->size     = 60;
		
	
		$edit->fecha = new  dateonlyField("Fecha",  "fecha");
		$edit->fecha->insertValue = date('Y-m-d');
		$edit->fecha->size =12;
		//$edit->fecha->mode = "autohide";
		//$edit->fecha->when = array('show',);
	
		$edit->uejecutora = new dropdownField("Unidad Ejecutora", "uejecutora");
		$edit->uejecutora->option("","Seccionar");
		$edit->uejecutora->options("SELECT codigo, nombre FROM uejecutora ORDER BY nombre");
		//$edit->uejecutora->onchange = "get_uadmin();";
		$edit->uejecutora->rule = "required";
		$edit->uejecutora->style = "width:200px";
		$edit->uejecutora->when=array();
	
		$edit->cod_prov = new inputField("Beneficiario", 'cod_prov');
		$edit->cod_prov->db_name  = "cod_prov";
		$edit->cod_prov->size     = 4;
		$edit->cod_prov->rule     = "required";
		$edit->cod_prov->readonly =true;
		$edit->cod_prov->append($bSPRV);
	
		$edit->nombrep = new inputField("Nombre", 'nombrep');
		$edit->nombrep->size = 60;
		//$edit->nombrep->readonly = true;
		$edit->nombrep->pointer = true;
		
		$edit->observa = new textAreaField("Concepto", 'observa');
		$edit->observa->cols = 70;
		$edit->observa->rows = 3;
		
		$edit->total= new inputField("Monto a Pagar", 'total');
		$edit->total->size = 8;
		$edit->total->css_class='inputnum';
		
		$edit->retenomina= new inputField("Deducciones Nomina", 'retenomina');
		$edit->retenomina->size = 8;
		$edit->retenomina->css_class='inputnum';
		$edit->retenomina->onchange ='cal_total();';
		$edit->retenomina->value = 0;
		$edit->retenomina->mode   = "autohide";
	
		$edit->subtotal = new inputField("Sub Total", 'subtotal');
		$edit->subtotal->css_class='inputnum';
		$edit->subtotal->size = 8;
		$edit->subtotal->readonly=true;
		$edit->subtotal->mode   = "autohide";
	
		$edit->total2 = new inputField("Total", 'total2');
		$edit->total2->css_class='inputnum';
		$edit->total2->size = 8;
		$edit->total2->mode   = "autohide";
		
		$edit->itcodigoadm = new inputField("Estructura	Administrativa","codigoadm_<#i#>");
		$edit->itcodigoadm->db_name='codigoadm';
		$edit->itcodigoadm->rel_id ='itodirect';				
		$edit->itcodigoadm->rule   ='required';
		$edit->itcodigoadm->size   =15;
		$edit->itcodigoadm->type   = "inputhidden";
		       
		$edit->itfondo = new inputField("(<#o#>) Fondo","fondo_<#i#>");
		$edit->itfondo->rule   ='required';
		$edit->itfondo->db_name='fondo';
		$edit->itfondo->rel_id ='itodirect';
		$edit->itfondo->size     =15;
		$edit->itfondo->type   = "inputhidden";
	
		$edit->itpartida = new inputField("(<#o#>) Partida", "partida_<#i#>");
		//$edit->itpartida->rule='callback_itpartida';
		$edit->itpartida->size=12;
		$edit->itpartida->db_name='partida';
		$edit->itpartida->rel_id ='itodirect';
		//$edit->itpartida->readonly =true;
		$edit->itpartida->type   = "inputhidden";
		
		$edit->itdescripcion = new inputField("(<#o#>) Descripci&oacute;n", "descripcion_<#i#>");
		$edit->itdescripcion->db_name  ='descripcion';
		$edit->itdescripcion->maxlength=80;
		$edit->itdescripcion->size     =15;
		//$edit->itdescripcion->rule     = 'required';
		$edit->itdescripcion->rel_id   ='itodirect';
		$edit->itdescripcion->type   = "inputhidden";
	
		$edit->itimporte = new inputField("(<#o#>) Importe", "importe_<#i#>");
		$edit->itimporte->css_class='inputnum';
		$edit->itimporte->db_name  ='importe';
		$edit->itimporte->rel_id   ='itodirect';
		$edit->itimporte->rule     ='numeric';
		//$edit->itimporte->readonly =true;
		$edit->itimporte->size     = 15;
		$edit->itimporte->type   = "inputhidden";
				
		$edit->status = new dropdownField("Estado","status");		
		$edit->status->option("","");
		$edit->status->option("K2","Actualizado");
		$edit->status->option("K1","Sin Actualizar");		
		$edit->status->option("K3","Pagado");
		$edit->status->option("KA","Anulado");
		$edit->status->when = array('show');
		$edit->status->style="width:150px";
	
		$status=$edit->get_from_dataobjetct('status');
		if($status=='K1'){
			$action = "javascript:window.location='" .site_url($this->url.'actualizar/'.$edit->rapyd->uri->get_edited_id()). "'";
			$edit->button_status("btn_status",'Causar/Ordenar Pago',$action,"TR","show");
			$action = "javascript:btn_anulaf('".$edit->rapyd->uri->get_edited_id()."')";
			$edit->buttons("delete","modify","save");
		}elseif($status=='K2'){
			$action = "javascript:window.location='" .site_url($this->url.'modconc/modify/'.$edit->rapyd->uri->get_edited_id()). "'";
			$edit->button_status("btn_moconc",'Modificar Concepto',$action,"TR","show");
			
			if($this->datasis->puede(438)){
				$action = "javascript:window.location='" .site_url($this->url.'reversar/'.$edit->rapyd->uri->get_edited_id()). "'";
				$edit->button_status("btn_status",'Reversar Causado',$action,"TR","show");
			}
			
			$action = "javascript:btn_anulaf('".$edit->rapyd->uri->get_edited_id()."')";
			//if($this->datasis->puede(156))
			$edit->button_status("btn_anular",'Anular',$action,"TR","show");
		}elseif($status=="K"){
			$edit->buttons("modify","save","delete");
		}else{
			$edit->buttons("save");
		}
		
		$edit->buttons("undo","back");
		$edit->build();	
		
		$smenu['link']   =barra_menu('173');
		$data['smenu']   = $this->load->view('view_sub_menu', $smenu,true);
		$conten["form"]  =&  $edit;
		$data['content'] = $this->load->view('view_pagonom', $conten,true);
		//$data['content'] = $edit->output;
		$data['title']   = " $this->tits ";
		$data["head"]    = $this->rapyd->get_head().script('jquery.js').script("plugins/jquery.numeric.pack.js").script("plugins/jquery.json.min.js");
		$this->load->view('view_ventanas', $data);
	}
	
	function creapago($nomi){
		$this->rapyd->load('dataobject');
	
		$error='';

		$do = new DataObject("nomi");
		$do->rel_one_to_many('asignomi', 'asignomi', array('numero'=>'numero'));
		$do->rel_one_to_many('retenomi', 'retenomi', array('numero'=>'numero'));
		$do->load($nomi);
		
		$nnomi   = $do->get('numero');
		$descrip = $do->get('descrip');
		
		
		if($nnomi != $nomi)
			$error.="<div class='alert'>No se pudo cargar la nomina numero $nomi</div>";
		
		$odirect = new DataObject("odirect");
		$odirect->rel_one_to_many('itodirect', 'itodirect', array('numero'=>'numero'));

		$status = $do->get('status');
		
		$ntransac = $this->datasis->fprox_numero('ntransac');
		$odirect->set('numero','_'.$ntransac);
		$odirect->set('observa'    ,$descrip     );

		$tasig=0;$b=array();
		if($status=="C"){
			for($i=0;$i < $do->count_rel('asignomi');$i++){
				$ordinal   = $do->get_rel('asignomi','ordinal'    ,$i);
				$codigopres= $do->get_rel('asignomi','codigopres' ,$i);
				$codigoadm = $do->get_rel('asignomi','codigoadm'  ,$i);
				$fondo     = $do->get_rel('asignomi','fondo'      ,$i);
				$monto     = $do->get_rel('asignomi','monto'      ,$i);
							
				$odirect->set_rel('itodirect','id'    ,''     ,$i);
				$odirect->set_rel('itodirect','ordinal'    ,$ordinal     ,$i);
				$odirect->set_rel('itodirect','partida'    ,$codigopres  ,$i);
				$odirect->set_rel('itodirect','codigoadm'  ,$codigoadm   ,$i);
				$odirect->set_rel('itodirect','fondo'      ,$fondo       ,$i);
				$odirect->set_rel('itodirect','importe'    ,$monto       ,$i);
				$odirect->set_rel('itodirect','precio'     ,$monto       ,$i);
				$odirect->set_rel('itodirect','cantidad'   ,1            ,$i);
				$odirect->set_rel('itodirect','iva'        ,0            ,$i);
				$odirect->set_rel('itodirect','unidad'     ,'Monto'      ,$i);
				$odirect->set_rel('itodirect','numero'     ,'_'.$ntransac,$i);
				$tasig  += $monto;
			}
			
			$trete = 0;$monto=0;
			for($j=0;$j < $do->count_rel('retenomi');$j++){
				$monto  = $do->get_rel('retenomi','monto',$j);
				$trete += $monto;
			}
			
			
			$odirect->set('subtotal'   ,$tasig       ,$i);
			$odirect->set('total'      ,$tasig-$trete,$i);
			$odirect->set('total2'     ,$tasig       ,$i);
			
			$odirect->set('status'     ,'K'          ,$i);
			$odirect->set('nomina'     ,$nomi        ,$i);
			$odirect->set('retenomina' ,$trete       ,$i);
			$odirect->set('tipo'       ,'N'          ,$i);
			$odirect->set('fecha'      ,date('Ymd')  ,$i);
			$odirect->set('multiple'   ,'N'          ,$i);
			
			$odirect->set('numero','_'.$ntransac);
			//$odirect->pk=array('numero'=>'_'.$ntransac);
			//print_r($odirect->get_all());
			
			
			$odirect->save();
			$numero = $odirect->get('numero');
			
			
			if(empty($numero))
				$error.="<div calss='alert'>No se Pudo Crear la Orden de Pago</div>";
		}else{
			$error.="No se puede realizar la operacion para la nomina $nomi";	
		}
		
		if(empty($error)){
			$do->set('opago' ,$numero);
			$do->set('status','D');
			//for($i=0;$i < $do->count_rel('retenomi');$i++){
			//	$do->set_rel('asignomi','status' , 'D');
			//}
			$do->save();
			
		}
		
				
		if(empty($error)){
			logusu('nomi',"Creo orden de pago ($numero) de nomina Nro $nomi");
			redirect($this->url."dataedit/show/$numero");
		}else{
			logusu('nomi',"Intento crear la orden de pago de la nomina $nomi ERROR:$error ");
			$data['content'] = $error.anchor($this->url."filteredgrid",'Regresar');
			$data['title']   = " $this->tits ";
			$data["head"]    = $this->rapyd->get_head();
			$this->load->view('view_ventanas', $data);
		}
	}
	
	function _valida($do){
		$error = '';
		$do->set('status','K1');
		$numero     = $do->get('numero');
		$numeroe     = $this->db->escape($numero);
		if(empty($error)){
			$USANODIRECT = $this->datasis->traevalor("USANODIRECT");
			if($USANODIRECT=='S'){
				if(substr($numero,0,1)=='_'){
					$contador = $this->datasis->fprox_numero('nodirect');
					$do->set('numero',$contador);
					$do->pk=array('numero'=>$contador);
					
					
					$this->db->query("UPDATE itodirect SET numero='$contador' WHERE numero=$numeroe");
					$this->db->query("UPDATE nomi SET opago='$contador' WHERE opago=$numeroe");
				}
			}else{
				$numeroviejo =$this->uri->segment('5');
				$numeroviejoe=$this->db->escape($numeroviejo);
				$this->db->query("UPDATE nomi SET opago=$numeroe WHERE opago=$numeroviejoe");
			}
		}
		//$numeroante=$numero;
		
		
		if(!empty($error)){
			$do->error_message_ar['pre_ins']=$error;
			$do->error_message_ar['pre_upd']=$error;
			return false;
		}
		
		//print_r($do->get_all());
		//exit($numero.$numeroante);
		
	}
	
	function actualizar($numero){
	
		$this->rapyd->load('dataobject');
		
		$error='';
	
		$odirect = new DataObject("odirect");
		$odirect->rel_one_to_many('itodirect', 'itodirect', array('numero'=>'numero'));
		$odirect->load($numero);
		
		$nnumero = $odirect->get('numero');
		if(strpos($nnumero,'_')===0){
			if($this->datasis->traevalor('USANODIRECT')!='S'){
				$error.="<div class='alert'>Debe Introducir un numero de Orden de Pago</div>";
			}else{
				$contador = $this->datasis->fprox_numero('nodirect');
				$odirect->set('numero',$contador);
				//$odirect->pk=array('numero'=>$contador);
				for($i=0;$i < $odirect->count_rel('itodirect');$i++){
					$odirect->set_rel('itodirect','id'    ,''       ,$i);
					$odirect->set_rel('itodirect','numero',$contador,$i);
				}
				$nnumero=$numero=$contador;
			}
		}
		
		if($nnumero !=$numero)
			$error.="<div class='alert'>No se pudo cargar la orden de pago</div>";
			
		$ostatus = $odirect->get('status');
		if($ostatus !='K1' )
			$error.="<div class='alert'>No es Posible Realizar la operaci&oacute;n para la orden de pago </div>";

		$nomina = $odirect->get('nomina');
		if(empty($nomina))
			$error.="<div class='alert'>La orden de pago no tiene asignada alguna nomina asignada</div>";

		$do = new DataObject("nomi");

		$do->rel_one_to_many('asignomi', 'asignomi', array('numero'=>'numero'));		
		$do->rel_one_to_many('retenomi', 'retenomi', array('numero'=>'numero'));
		$do->load($nomina);
		
		$status=$do->get('status');	
			
		if($status !='D')
			$error.="<div class='alert'>No es Posible Realizar la operaci&oacute;n para la n&oacute;mina </div>";
		
		$status = $do->get('status');

		if(empty($error)){
			$tasig=0;$b=array();$error='';
			if($status=="D"){
				for($i=0;$i < $do->count_rel('asignomi');$i++){
					$ordinal   = $do->get_rel('asignomi','ordinal'    ,$i);
					$codigopres= $do->get_rel('asignomi','codigopres' ,$i);
					$codigoadm = $do->get_rel('asignomi','codigoadm'  ,$i);
					$fondo     = $do->get_rel('asignomi','fondo'      ,$i);
					$monto     = $do->get_rel('asignomi','monto'      ,$i);
					
					$error.=$this->chequeapresup($codigoadm,$fondo,$codigopres,$ordinal,$monto,0,'round($monto,2) > round(($comprometido-$causado),2)',"El Monto ($monto) es mayor al disponible para la partida ($codigoadm) ($fondo) ($codigopres)");//
				}
				
				if(empty($error)){
					
					for($i=0;$i < $do->count_rel('asignomi');$i++){
						$ordinal   = $do->get_rel('asignomi','ordinal'    ,$i);
						$codigopres= $do->get_rel('asignomi','codigopres' ,$i);
						$codigoadm = $do->get_rel('asignomi','codigoadm'  ,$i);
						$fondo     = $do->get_rel('asignomi','fondo'      ,$i);
						$monto     = $do->get_rel('asignomi','monto'      ,$i);
						
						$error.=$this->afectapresup($codigoadm,$fondo,$codigopres,$ordinal,$monto, 0 , 1 ,array("causado","opago"));
					}
					
					if(empty($error)){
						$do->set('status','O');
						
						for($i=0;$i < $do->count_rel('retenomi');$i++){
							$do->set_rel('retenomi','status' , 'O',$i);
						}
						$odirect->set('fopago',date('Ymd'));
						$odirect->set('status','K2');
						$odirect->save();
						$do->save();
					}
				}
			}else{
				$error.="No se puede realizar la operacion para la nomina $numero";	
			}	
	
			$this->sp_presucalc($codigoadm);
		}
			
		if(empty($error)){
			logusu('nomi',"Actualizo Orden de Pago de nomina Nro $numero");
			redirect($this->url."dataedit/show/$numero");
		}else{
			logusu('nomi',"Actualizo nomina Nro $numero ERROR:$error ");
			$data['content'] = $error.anchor($this->url."dataedit/show/$numero",'Regresar');
			$data['title']   = " $this->tits ";
			$data["head"]    = $this->rapyd->get_head();
			$this->load->view('view_ventanas', $data);
		}
	}
	
	function reversar($numero,$anular='N'){
	
		$this->rapyd->load('dataobject');
		
		$error='';
	
		$odirect = new DataObject("odirect");
		$odirect->rel_one_to_many('itodirect', 'itodirect', array('numero'=>'numero'));
		$odirect->load($numero);
		
		$nnumero = $odirect->get('numero');
		if($nnumero !=$numero)
			$error.="<div class='alert'>No se pudo cargar la orden de pago</div>";
			
		$ostatus = $odirect->get('status');
		if($ostatus !='K2')
			$error.="<div class='alert'>No es Posible Realizar la operaci&oacute;n para la orden de pago </div>";

		$nomina = $odirect->get('nomina');
		if(empty($nomina))
			$error.="<div class='alert'>La orden de pago no tiene asignada alguna nomina asignada</div>";
			
		$do = new DataObject("nomi");

		$do->rel_one_to_many('asignomi', 'asignomi', array('numero'=>'numero'));		
		$do->rel_one_to_many('retenomi', 'retenomi', array('numero'=>'numero'));
		$do->load($nomina);	
			
		echo "--".$status = $do->get('status');
		if($status !='O' )
			$error.="<div class='alert'>No es Posible Realizar la operaci&oacute;n para la n&oacute;mina </div>";
		
		$status = $do->get('status');

		if(empty($error)){
			$tasig=0;$b=array();$error='';
			if($status=="O"){
				for($i=0;$i < $do->count_rel('asignomi');$i++){
					
					$codigopres= $do->get_rel('asignomi','codigopres' ,$i);
					$codigoadm = $do->get_rel('asignomi','codigoadm'  ,$i);
					$fondo     = $do->get_rel('asignomi','fondo'      ,$i);
					$monto     = $do->get_rel('asignomi','monto'      ,$i);
					
					$error.=$this->chequeapresup($codigoadm,$fondo,$codigopres,$ordinal,$monto,0,'round($monto,2) > round(($opago-$pagado),2)',"El Monto ($monto) es mayor al disponible para la partida ($codigoadm) ($fondo) ($codigopres)");//
				}
				
				if(empty($error)){
					
					for($i=0;$i < $do->count_rel('asignomi');$i++){
						
						$codigopres= $do->get_rel('asignomi','codigopres' ,$i);
						$codigoadm = $do->get_rel('asignomi','codigoadm'  ,$i);
						$fondo     = $do->get_rel('asignomi','fondo'      ,$i);
						$monto     = $do->get_rel('asignomi','monto'      ,$i);
						
						$error.=$this->afectapresup($codigoadm,$fondo,$codigopres,$ordinal,$monto, 0 , -1 ,array("causado","opago"));
					}
					
					if(empty($error)){
						
						if($anular=='S'){
							$odirect->set('status','KA');
							$odirect->set('fanulado',date('Ymd'));
							$do->set('opago' ,'');
							$do->set('status','C');
						}else{
							$odirect->set('status','K1');
							$do->set('status','D');
						}
						$odirect->save();
						
						for($i=0;$i < $do->count_rel('retenomi');$i++){
							$do->set_rel('retenomi','status' , 'C',$i);
						}
						$do->save();
					}
				}
			}else{
				$error.="No se puede realizar la operacion para la nomina $numero";	
			}	
	
			
		}
			
		if(empty($error)){
			logusu('nomi',"Reverso Orden de Pago de nomina Nro $numero");
			redirect($this->url."dataedit/show/$numero");
		}else{
			logusu('nomi',"Reverso Orden de Pago de nomina Nro $numero ERROR:$error ");
			$data['content'] = $error.anchor($this->url."dataedit/show/$numero",'Regresar');
			$data['title']   = " $this->tits ";
			$data["head"]    = $this->rapyd->get_head();
			$this->load->view('view_ventanas', $data);
		}
	}

  function anula($id){
	     
  }
  
	 function modconc($status='',$numero){
		$this->rapyd->load("dataobject","dataedit");
	
		$edit = new DataEdit($this->tits, "odirect");
		$edit->back_url = site_url($this->url."/dataedit/show/$numero");
		$edit->back_cancel=true;
		$edit->back_cancel_save=true;
		
		$edit->pre_process('update'  ,'_valida_mod');
		$edit->post_process('update','_post_update_mod');
		
		$edit->numero  = new inputField("N&uacute;mero", "numero");
		$edit->numero->mode="autohide";
		
		$edit->observa = new textAreaField("Observaciones", 'observa');
		$edit->observa->cols = 70;
		$edit->observa->rows = 3;
		
		$edit->buttons("undo","back","save");
		$edit->build();

		$data['content'] = $edit->output;
		$data['title']   = "Modificar Concepto de Orden de Pago";
		$data["head"]    = $this->rapyd->get_head().script('jquery.js').script("plugins/jquery.numeric.pack.js").script("plugins/jquery.json.min.js");
		$this->load->view('view_ventanas', $data);
	}
        
        function _post_update_mod($do){
            $numero=$do->get('numero');
            redirect($this->url."dataedit/show/$numero");
        }
	
        function _valida_update_mod($do){
            $status=$do->get('status');
            
            if($status!='K2')
            $error.="No se puede cambiarel concepto para esta orden";
            
            if(!empty($error)){	
                    $do->error_message_ar['pre_ins']=$error;
                    $do->error_message_ar['pre_upd']=$error;
                    return false;
            }
        }
	
	function _post_insert($do){
		$numero = $do->get('numero');
		logusu('odirect',"Creo Orden de Pago de nomina Nro $numero");
		//redirect($this->url."actualizar/$numero");
	}
	function _post_update($do){
		$numero = $do->get('numero');
		logusu('odirect'," Modifico Orden de Pago de nomina Nro $numero");
		//redirect($this->url."actualizar/$numero");
	}
	
	function _post_delete($do){
		$this->rapyd->load('dataobject');
		
		$numero = $do->get('numero');
		$nomina = $do->get('nomina');
		
		$nomi = new DataObject("nomi");
		$nomi->load($nomina);
		$nomi->set('opago','');
		$nomi->set('status','C');
		$nomi->save();
		
		logusu('odirect'," Elimino Orden de Pago de nomina Nro $numero");
	}
}
