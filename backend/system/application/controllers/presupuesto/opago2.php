<?php

class opago2 extends Controller {


	function opago2(){
		parent::Controller();
		$this->load->library("rapyd");
		$this->formatopres=$this->datasis->traevalor('FORMATOPRES');
		$this->flongpres  =strlen(trim($this->formatopres));
		//$this->datasis->modulo_id(302,1);
	}
	function index(){
		redirect("presupuesto/opago2/filteredgrid");
	}


	function filteredgrid(){
		$this->datasis->modulo_id(102,1);
		$this->rapyd->load("datafilter","datagrid");
		
		
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
		
		$filter = new DataFilter("Filtro de Ordenes de Pago","opago");

		$filter->numero = new inputField("N&uacute;mero", "numero");
		$filter->numero->size=15;
		
		$filter->fecha = new dateonlyField("Fecha", "fecha");
		$filter->fecha->size=12;
		
		$filter->uejecutora = new inputField("Unidad Ejecutora", "uejecutora");
		$filter->uejecutora->size=12;
		//
		//$filter->cod_prov = new inputField("Beneficiario", 'cod_prov');
		//$filter->cod_prov->size = 6;
		//$filter->cod_prov->append($bSPRV);
		//$filter->cod_prov->rule = "required";
		//
		//$filter->beneficiario = new inputField("Beneficiario", "beneficiario");
		//$filter->beneficiario->size=12;
		
		$filter->observa = new inputField("Observaci&oacute;n", "observa");
		$filter->observa->size=12;
		
		$filter->status = new dropdownField("Estado","status");
		$filter->status->option("","");
		$filter->status->option("P","Pendiente");
		$filter->status->option("C","Pagado");
		$filter->status->style="width:100px";
		
		$filter->buttons("reset","search");
		$filter->build();
		$uri = anchor('presupuesto/opago2/dataedit/show/<#numero#>','<str_pad><#numero#>|8|0|STR_PAD_LEFT</str_pad>');
		
		$grid = new DataGrid("Lista de Ordenes de Pago");
		$grid->order_by("numero","desc");
		$grid->per_page = 20;
		$grid->use_function('substr','str_pad');
		
		$grid->column("N&uacute;mero"      ,$uri);
		$grid->column("Fecha"             ,"<dbdate_to_human><#fecha#></dbdate_to_human>","align='center'");
		$grid->column("Unidad Ejecutora","uejecutora");
		//$grid->column("Beneficiario"       ,"cod_prov");
		//$grid->column("Beneficiario"    ,"beneficiario");
		$grid->column("Observaci&oacute;n","observa");
		$grid->column("Estado"            ,"status"                                       ,"align='center'");
		
		//echo $grid->db->last_query();
		$grid->add("presupuesto/opago2/dataedit/create");
		$grid->build();
		
		$data['content'] = $filter->output.$grid->output;
		$data['title']   = " Ordenes de Pago ";
		$data["head"]    = $this->rapyd->get_head();
		$this->load->view('view_ventanas', $data);	
	}

	function dataedit($status='',$numero=''){
		
		$this->datasis->modulo_id(102,1);
		
		$this->rapyd->load('dataobject','datadetails');
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
		
		$mOCOMPRA=array(
				'tabla'   =>'ocompra',
				'columnas'=>array(
					'numero'      =>'C&oacute;odigo',
					'tipo'        =>'Tipo',
					'uejecutora'  =>'U. Ejecutora',
					'cod_prov'    =>'Beneficiario',
					'beneficiario'=>'Beneficiario'),
				'filtro'  =>array(
					'numero'      =>'C&oacute;odigo',
					'tipo'        =>'Tipo',
					'uejecutora'  =>'U. Ejecutora',
					'beneficiario'=>'Beneficiario'),
				'p_uri'=>array(4=>'<#i#>',5=>'<#cod_prov#>',),
				'where'=>'status="T" AND cod_prov=<#cod_prov#>',
				'script'=>array('cal_pago(<#i#>)'),
				'retornar'=>array('numero'=>'orden_<#i#>','total'=>'total_<#i#>','abono'=>'abono_<#i#>','reten'=>'treten','reteiva'=>'treteiva'),
				'titulo'  =>'Buscar Orden de Compra');
			
		$bOCOMPRA=$this->datasis->p_modbus($mOCOMPRA,"<#i#>/<#cod_prov#>");

		$modbus=array(
			'tabla'   =>'ppla',
			'columnas'=>array(
				'codigo'      =>'C&oacute;digo',
				'denominacion'=>'Denominaci&oacute;n'),
			'filtro'  =>array('codigo' =>'C&oacute;digo','denominacion'=>'Denominaci&oacute;n'),
			'retornar'=>array('codigo'=>'partida_<#i#>','denominacion'=>'descripcion_<#i#>'),
			'p_uri'=>array(4=>'<#i#>',5=>'<#fondo#>',6=>'<#estadmin#>',),
			'where'=>'tipo=<#fondo#> AND codigoadm=<#estadmin#> AND LENGTH(ppla.codigo)='.$this->flongpres,
			'join' =>array('presupuesto','presupuesto.codigopres=ppla.codigo',''),
			'titulo'  =>'Busqueda de partidas');
		$btn=$this->datasis->p_modbus($modbus,'<#i#>/<#fondo#>/<#estadmin#>');

		$do = new DataObject("opago");
		
		//if($status=="create" &&!empty($numero)){
		//	
		//	$temp = new DataObject("ocompra");
		//	$temp->load($numero);
		//	$numero;
		//	$estadmin     =  $temp->get('estadmin');
		//	$fondo        =  $temp->get('fondo');
		//	$beneficiario =  $temp->get('beneficiario');
		//	$do->load(99999999);
    //
		//	$do->set('estadmin'       ,$estadmin    );
		//	$do->set('fondo'          ,$fondo       );
		//	$do->set('beneficiario'   ,$beneficiario);
		//	$do->set('cod_prov'       ,$cod_prov    );
		//	
		//	for($i=0;$i < $do->count_rel('itocompra');$i++){
		//		$codigopres  = $do->get_rel('itopago','partida',$i);					
		//		$pago        = $do->get_rel('itopago','pago',$i);
		//		
		//		$pk['codigopres'] = $codigopres;
		//		$presup->load($pk);
		//		$causado=$presup->get("causado");
		//		if($pago > $causado)
		//			$error.="<div class='alert'><p>El monto a pagar ($pago) es mayor al monto causado ($causado)</p></div>";
		//	}
		//	
		//}
		
		$do->rel_one_to_many('itopago', 'itopago', array('numero'=>'numero'));
		
	
		$edit = new DataDetails("Orden de Pago", $do);
		
		$edit->pre_process('insert'  ,'_valida');
		$edit->pre_process('update'  ,'_valida');
		
		$edit->back_url = site_url("presupuesto/opago2/filteredgrid");
		$edit->set_rel_title('itopago','Rubro <#o#>');

		$edit->numero  = new inputField("N&uacute;mero", "numero");
		$edit->numero->mode="autohide";
		$edit->numero->when=array('show');

		$edit->fecha = new  dateonlyField("Fecha",  "fecha");
		$edit->fecha->insertValue = date('Y-m-d');
		$edit->fecha->size =12;

		//$edit->uejecutora = new dropdownField("Unidad Ejecutora", "uejecutora");
		//$edit->uejecutora->option("","Seccionar");
		//$edit->uejecutora->options("SELECT codigo, nombre FROM uejecutora ORDER BY nombre");
		//$edit->uejecutora->onchange = "get_uadmin();";
		//$edit->uejecutora->rule = "required";
		
		$edit->estadmin = new dropdownField("Estructura Administrativa","estadmin");
		$edit->estadmin->option("","Seleccione");
		//$edit->estadmin->rule='required';
		$edit->estadmin->options("SELECT b.codigo, CONCAT_WS(' ',b.codigo,b.denominacion) AS val FROM presupuesto AS a JOIN estruadm AS b ON a.codigoadm=b.codigo  GROUP BY b.codigo");
		
		$edit->fondo = new dropdownField("Fondo", "fondo");
		//$edit->fondo->rule = "required";
		$estadmin=$edit->getval('estadmin');
		if($estadmin!==false){
			$edit->fondo->options("SELECT tipo,tipo a  FROM presupuesto WHERE codigoadm='$estadmin' GROUP BY tipo");
		}else{
			$edit->fondo->option("","Seleccione una estructura administrativa primero");
		}
		
		$edit->observa = new textareaField("Observaci&oacute;n", "observa");
		$edit->observa->rows=4;
		$edit->observa->cols=100;
		
		$edit->cod_prov = new inputField("Beneficiario", 'cod_prov');
		$edit->cod_prov->size = 15;
		$edit->cod_prov->append($bSPRV);
		$edit->cod_prov->rule = "required";

		$edit->beneficiario = new inputField("Beneficiario", 'beneficiario');
		$edit->beneficiario->size = 100;
		//$edit->beneficiario->rule = "required";
		
		$edit->total = new inputField("Total", 'total');
		$edit->total->css_class='inputnum';
		$edit->total->size = 8;
		
		//$edit->itpartida = new inputField("(<#o#>) Partida", "partida_<#i#>");
		//$edit->itpartida->rule='callback_repetido|required|callback_itpartida';
		//$edit->itpartida->size=15;
		//$edit->itpartida->append('<img src="/tortuga/assets/default/images/system-search.png"  alt="Busqueda de partidas" title="Busqueda de partidas" border="0" onclick="modbusdepen(<#i#>)"/>');
		//$edit->itpartida->db_name='partida';
		//$edit->itpartida->rel_id ='itopago';
		////$edit->itpartida->readonly =true;
		
		$edit->itorden = new inputField("(<#o#>) Orden", "orden_<#i#>");
		$edit->itorden->rule='callback_repetido|required|callback_itocompra';
		$edit->itorden->size=15;
		$edit->itorden->append('<img src="/tortuga/assets/default/images/system-search.png"  alt="Busqueda de Ordenes de Compra" title="Busqueda de Ordenes de Compra" border="0" onclick="modbusdepen(<#i#>)"/>');
		$edit->itorden->db_name='orden';
		$edit->itorden->rel_id ='itopago';
		//$edit->itorden->append($bOCOMPRA);
		
		
		$edit->itdescripcion = new inputField("(<#o#>) Descripci&oacute;n", "descripcion_<#i#>");
		$edit->itdescripcion->db_name  ='descripcion';
		$edit->itdescripcion->maxlength=80;
		$edit->itdescripcion->size     =40;
		$edit->itdescripcion->rule     = 'required';
		$edit->itdescripcion->rel_id   ='itopago';
		//$edit->itdescripcion->readonly =true;
		
		$edit->itreten = new inputField("(<#o#>) reten", "treten");
		//$edit->itreten->css_class='inputnum';
		$edit->itreten->db_name  =null;
		//$edit->itreten->rel_id   ='itopago';
		//$edit->itreten->mode     ='autohide';
		$edit->itreten->size     =1;
		$edit->itreten->when =array('modify',"create");
		
		$edit->itreteiva = new inputField("(<#o#>) Pago", "treteiva");
		//$edit->itreteiva->css_class='inputnum';
		$edit->itreteiva->db_name  =null;                               
		//$edit->itreteiva->rel_id   ='itopago';                               
		//$edit->itreteiva->mode     ='autohide';
		$edit->itreteiva->size     =1;
		$edit->itreteiva->when =array('modify',"create");
		
		//$edit->ittotal = new inputField("(<#o#>) Pago", "ttotal");
		////$edit->ittotal->css_class='inputnum';
		//$edit->ittotal->db_name  =null;
		////$edit->ittotal->rel_id   ='itopago';
		////$edit->ittotal->mode     ='autohide';
		//$edit->ittotal->size     =1;
		//$edit->ittotal->when =array('modify',"create");
		
		$edit->ittotal = new inputField("(<#o#>) ", "total_<#i#>");			
		$edit->ittotal->db_name  =null;
		$edit->ittotal->rel_id   ='itopago';
		$edit->ittotal->readonly=true;
		$edit->ittotal->size     =8;
		
		$edit->itabono = new inputField("(<#o#>) ", "abono_<#i#>");			
		$edit->itabono->db_name  =null;
		$edit->itabono->rel_id   ='itopago';
		$edit->itabono->readonly=true;
		$edit->itabono->size     =8;
		
		$edit->itpago = new inputField("(<#o#>) Pago", "pago_<#i#>");
		$edit->itpago->css_class='inputnum';
		$edit->itpago->db_name  ='pago';
		$edit->itpago->rel_id   ='itopago';
		$edit->itpago->rule     ='numeric';
		$edit->itpago->size     =8;
		$edit->itpago->onchange ='cal_total(<#i#>);';
		
		$status=$edit->get_from_dataobjetct('status');
		if($status=='P'){
			$action = "javascript:window.location='" .site_url('presupuesto/opago2/actualizar/'.$edit->rapyd->uri->get_edited_id()). "'";
			$edit->button_status("btn_status",'Ordenar Pago',$action,"TR","show");
			$edit->buttons("modify","delete","save");
		}elseif($status=='C'){
			$action = "javascript:window.location='" .site_url('presupuesto/opago2/reversar/'.$edit->rapyd->uri->get_edited_id()). "'";
			$edit->button_status("btn_rever",'Deshacer Ordenar Pago',$action,"TR","show");
		}else{
			$edit->buttons("save");
		}

		$edit->buttons("undo","back","add_rel");
		$edit->build();

		$smenu['link']=barra_menu('102');
		$data['smenu'] = $this->load->view('view_sub_menu', $smenu,true);
		$conten["form"]  =&  $edit;
		$data['content'] = $this->load->view('view_opago2', $conten,true); 
		//$data['content'] = $edit->output;
		$data['title']   = " Orden de Pago ";
		$data["head"]    = $this->rapyd->get_head().script('jquery.js').script("plugins/jquery.numeric.pack.js");
		$this->load->view('view_ventanas', $data);
	}
		
	
	function actualizar($id){
		$this->rapyd->load('dataobject');
		
		$do = new DataObject("opago");
		$do->rel_one_to_many('itopago', 'itopago', array('numero'=>'numero'));
		$do->load($id);
		
		$sta=$do->get('status');
		if($sta=='P'){
			

			$ordc=new DataObject("ocompra");
			$ordc->rel_one_to_many('itocompra', 'itocompra', array('numero'=>'numero'));

			$error='';
			for($i=0;$i < $do->count_rel('itopago');$i++){
				$orden   = $do->get_rel('itopago','orden',$i);
				$pago    = $do->get_rel('itopago','pago' ,$i);
				
				$pk2    =array('numero'=> $orden);
				$ordc ->load($pk2);
				$debe   =$ordc->get('total');
				$reten  =$ordc->get('reten');
				$reteiva=$ordc->get('reteiva');
				$debet  =$debe-$reten-$reteiva;
				$status =$ordc->get('status');
				
				if($status!='T')
					$error.="<div class='alert'><p>Orden Compra ($orden): No ha sido Procesada</p></div>";
			}
		}else{
			$error.="<div class='alert'><p>Operaci&oacute;n no permitida</p></div>";
		}
			
		
		if(empty($error)){
			$presup = new DataObject("presupuesto");
			
			for($i=0;$i < $do->count_rel('itopago');$i++){
				$orden = $do->get_rel('itopago','orden',$i);
				$pago  = $do->get_rel('itopago','pago' ,$i);
				
				$pk2=array('numero'=> $orden);
				$ordc ->load($pk2);
				$debe        = $ordc->get('total');
				$abono       = $ordc->get('abono');
				$codigoadm   = $ordc->get('estadmin');
				$fondo       = $ordc->get('fondo');
				$debet       = $debe-$reten-$reteiva;
				//$subt=$ordc->get('subt');
				if($debet==$pago){
					$pk=array('codigoadm'=>$codigoadm,'tipo'=>$fondo);
			  	$partidaiva=$this->datasis->traevalor("PARTIDAIVA");
					for($i=0;$i < $ordc->count_rel('itocompra');$i++){
						$codigopres  = $ordc->get_rel('itocompra','partida',$i);
						$iva         = $ordc->get_rel('itocompra','iva',$i);
						$importe     = $ordc->get_rel('itocompra','importe',$i);
						$m           = ($importe*(($iva+100)/100))-$importe;
						
						$mont        = $importe;
						$pk['codigopres'] = $codigopres;
        	
						$presup->load($pk);
					
						$opago =$presup->get("opago");
						$opago+=$mont;
						$presup->set("opago",$opago);
						$presup->save();
						
						$pk['codigopres'] = $partidaiva;
						$presup->load($pk);
						$opago      = $presup->get("opago");
						$opago      +=$m;
						$presup->set("opago",$opago);
						$presup->save();
					}
					$ordc->set('status','R');
					$ordc->save();
				}
				
				
				//$do->set('total',$subt);
				$do->set('status','C');
				$do->save();
			}			
			redirect("presupuesto/opago2/dataedit/show/$id");
		}else{
			$data['content'] = $error.anchor("presupuesto/opago2/dataedit/show/$id",'Regresar');
			$data['title']   = " Orden de Pago ";
			$data["head"]    = $this->rapyd->get_head().script('jquery.js').script("plugins/jquery.numeric.pack.js");
			$this->load->view('view_ventanas', $data);
		}
	}
	
	function reversar($id){
	$this->rapyd->load('dataobject');
		
		$do = new DataObject("opago");
		$do->rel_one_to_many('itopago', 'itopago', array('numero'=>'numero'));
		$do->load($id);
		
		$sta=$do->get('status');
		if($sta=='C'){
			$codigoadm   = $do->get('estadmin');
			$fondo       = $do->get('fondo');

			$ordc=new DataObject("ocompra");
			$ordc->rel_one_to_many('itocompra', 'itocompra', array('numero'=>'numero'));

			$error='';
			for($i=0;$i < $do->count_rel('itopago');$i++){
				$orden   = $do->get_rel('itopago','orden',$i);
				$pago    = $do->get_rel('itopago','pago' ,$i);
				
				$pk2=array('numero'=> $orden);
				$ordc ->load($pk2);
				$debe=$ordc->get('total');
				$status=$ordc->get('status');
				
				if($status!='R')
					$error.="<div class='alert'><p>Orden Compra ($orden): No ha sido Procesada</p></div>";
			}
		}
		
		if(empty($error)){
			$presup = new DataObject("presupuesto");
			
			for($i=0;$i < $do->count_rel('itopago');$i++){
				$orden = $do->get_rel('itopago','orden',$i);
				$pago  = $do->get_rel('itopago','pago' ,$i);
				
				$pk2   =array('numero'=> $orden);
				$ordc ->load($pk2);
				$debe        = $ordc->get('total');
				$codigoadm   = $ordc->get('estadmin');
				$fondo       = $ordc->get('fondo');
				$abono       = $ordc->get('abono');
				
				
				
			
				$pk=array('codigoadm'=>$codigoadm,'tipo'=>$fondo);
			
				for($i=0;$i < $ordc->count_rel('itocompra');$i++){
					$codigopres = $ordc->get_rel('itocompra','partida',$i);
					$importe    = $ordc->get_rel('itocompra','importe',$i);
					$iva        = $ordc->get_rel('itocompra','iva'    ,$i);
					$mont       = $importe*(($iva+100)/100);
					
					$pk['codigopres'] = $codigopres;
					$presup->load($pk);
					$causado =$presup->get("causado");
					$causado-=$mont;
					$presup->set("opago",$causado);
					$presup->save();
				}
				$ordc->set('status','T');
				$ordc->save();
				$do->set('status','P');
				$do->save();
			}
			redirect("presupuesto/opago2/dataedit/show/$id");
		}else{
			$data['content'] = $error.anchor("presupuesto/opago2/dataedit/show/$id",'Regresar');
			$data['title']   = " Orden de Pago ";
			$data["head"]    = $this->rapyd->get_head().script('jquery.js').script("plugins/jquery.numeric.pack.js");
			$this->load->view('view_ventanas', $data);
		}
	}
	
	function _valida($do){
		$this->rapyd->load('dataobject');
		
		$temp = new DataObject("ocompra");
		
		$tot=0;
		$error='';
		for($i=0;$i < $do->count_rel('itopago');$i++){
			$orden   = $do->get_rel('itopago'  ,'orden'   ,$i);
			$pago    = $do->get_rel('itopago'  ,'pago'    ,$i);
			$pk=array('numero'=>$orden);
			$temp->load($pk);
			$total   = $temp->get('total');
			$abono   = $temp->get('abono');
			$reten   = $temp->get('reten');
			$reteiva = $temp->get('reteiva');
			
			$debe=$total-$reten-$reteiva-$abono;
			
			if($pago <= 0)$error.="<div class='alert'><p>Partida $codigopres :El pago debe ser positivo </p></div>";
			if($debe < $pago)$error.="<div class='alert'><p>El monto a pagar ($pago) es mayor que el monto causado ($debe) para la orden de compra ($orden) </p></div>";
			
			$tot+=$pago;
		}
		if(empty($error)){
			$do->set('total',$tot);
		}else{
			$do->error_message_ar['pre_ins']=$error;
			$do->error_message_ar['pre_upd']=$error;
			return false;
		}
	}
	
	function itocompra($ocompra){
		$cod_prov = $this->db->escape($this->input->post('cod_prov'));
		
		$cana=$this->datasis->dameval("SELECT COUNT(*) FROM ocompra WHERE status='T' AND cod_prov=$cod_prov AND numero=$ocompra");
		if($cana>0){
			return true;
		}else{
			$this->validation->set_message('itocompra',"La Orden de compra %s ($ocompra) No pertenece al proveedor seleccionado");
			return false;	
		}
	}
	
	function repetido($ocompra){
		if(isset($this->__rpartida)){
			if(in_array($ocompra, $this->__rpartida)){
				$this->validation->set_message('repetido',"El rublo %s ($ocompra) esta repetido");
				return false;	
			}
		}
		$this->__rpartida[]=$ocompra;
		return true;
	}
}
?>