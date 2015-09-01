<?php

//se puede pagar mas de lo causado????
//
//
//
//
//
//
//
///
class opago extends Controller {
	function opago(){
		parent::Controller();
		$this->load->library("rapyd");
		$this->formatopres=$this->datasis->traevalor('FORMATOPRES');
		$this->flongpres  =strlen(trim($this->formatopres));
		//$this->datasis->modulo_id(302,1);
	}
	function index(){
		redirect("presupuesto/opago/filteredgrid");
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
		$uri = anchor('presupuesto/opago/dataedit/show/<#numero#>','<str_pad><#numero#>|8|0|STR_PAD_LEFT</str_pad>');
		
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
		$grid->add("presupuesto/opago/dataedit/create");
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
		//	$cod_prov     =  $temp->get('cod_prov');
		//	
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
		
		$edit->back_url = site_url("presupuesto/opago/filteredgrid");
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
		$edit->estadmin->rule='required';
		$edit->estadmin->options("SELECT b.codigo, CONCAT_WS(' ',b.codigo,b.denominacion) AS val FROM presupuesto AS a JOIN estruadm AS b ON a.codigoadm=b.codigo  GROUP BY b.codigo");
		
		$edit->fondo = new dropdownField("Fondo", "fondo");
		$edit->fondo->rule = "required";
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
		$edit->beneficiario->rule = "required";
		
		$edit->total = new inputField("Total", 'total');
		$edit->total->css_class='inputnum';
		$edit->total->size = 8;
		
		$edit->itpartida = new inputField("(<#o#>) Partida", "partida_<#i#>");
		$edit->itpartida->rule='callback_repetido|required|callback_itpartida';
		$edit->itpartida->size=15;
		$edit->itpartida->append('<img src="/tortuga/assets/default/images/system-search.png"  alt="Busqueda de partidas" title="Busqueda de partidas" border="0" onclick="modbusdepen(<#i#>)"/>');
		$edit->itpartida->db_name='partida';
		$edit->itpartida->rel_id ='itopago';
		//$edit->itpartida->readonly =true;
		
		$edit->itdescripcion = new inputField("(<#o#>) Descripci&oacute;n", "descripcion_<#i#>");
		$edit->itdescripcion->db_name  ='descripcion';
		$edit->itdescripcion->maxlength=80;
		$edit->itdescripcion->size     =60;
		$edit->itdescripcion->rule     = 'required';
		$edit->itdescripcion->rel_id   ='itopago';
		//$edit->itdescripcion->readonly =true;
		
		$edit->itpago = new inputField("(<#o#>) Pago", "pago_<#i#>");
		$edit->itpago->css_class='inputnum';
		$edit->itpago->db_name  ='pago';
		$edit->itpago->rel_id   ='itopago';
		$edit->itpago->rule     ='numeric';
		$edit->itpago->size     =8;
		$edit->itpago->onchange ='cal_total(<#i#>);';
		
		$status=$edit->get_from_dataobjetct('status');
		if($status=='P'){
			$action = "javascript:window.location='" .site_url('presupuesto/opago/actualizar/'.$edit->rapyd->uri->get_edited_id()). "'";
			$edit->button_status("btn_status",'Actualizar',$action,"TR","show");
			$edit->buttons("modify","delete","save");
		}elseif($status=='C'){
			$action = "javascript:window.location='" .site_url('presupuesto/opago/reversar/'.$edit->rapyd->uri->get_edited_id()). "'";
			$edit->button_status("btn_rever",'Reversar',$action,"TR","show");
		}else{
			$edit->buttons("save");
		}
		
		
				
		$edit->buttons("undo","back","add_rel");
		$edit->build();

		$smenu['link']=barra_menu('102');
		$data['smenu'] = $this->load->view('view_sub_menu', $smenu,true);
		$conten["form"]  =&  $edit;
		$data['content'] = $this->load->view('view_opago', $conten,true); 
		//$data['content'] = $edit->output;
		$data['title']   = " Orden de Pago ";
		$data["head"]    = $this->rapyd->get_head().script('jquery.js').script("plugins/jquery.numeric.pack.js");
		$this->load->view('view_ventanas', $data);
	}
		
	function repetido($partida){
		if(isset($this->__rpartida)){
			if(in_array($partida, $this->__rpartida)){
				$this->validation->set_message('repetido',"El rublo %s ($partida) esta repetido");
				return false;	
			}
		}
		$this->__rpartida[]=$partida;
		return true;
	}
	
	function actualizar($id){
		$this->rapyd->load('dataobject');
		
		$do = new DataObject("opago");
		$do->rel_one_to_many('itopago', 'itopago', array('numero'=>'numero'));
		$do->load($id);
		
		$sta=$do->get('status');
		if($sta=='P'){
			$codigoadm   = $do->get('estadmin');
			$fondo       = $do->get('fondo');

			$presup = new DataObject("presupuesto");
			$pk=array('codigoadm'=>$codigoadm,'tipo'=>$fondo);
			$error='';
			for($i=0;$i < $do->count_rel('itopago');$i++){
				$codigopres  = $do->get_rel('itopago','partida',$i);					
				$pago        = $do->get_rel('itopago','pago',$i);
				$pk['codigopres'] = $codigopres;
				
				$presup->load($pk);
				$causado=$presup->get("causado");
				if($pago > $causado)
					$error.="<div class='alert'><p>El monto a pagar ($pago) es mayor al monto causado ($causado)</p></div>";
			}
		}
		
		if(empty($error)){
			for($i=0;$i < $do->count_rel('itopago');$i++){
				$codigopres  = $do->get_rel('itopago','partida',$i);
				$pago        = $do->get_rel('itopago','pago',$i);
				
				$pk['codigopres'] = $codigopres;
				
				$presup->load($pk);
				$pagado=$presup->get("pagado");
				$pagado=$pagado+($pago);
				
				$presup->set("pagado",$pagado);
				$presup->save();
			}
			$do->set('status','C');
			$do->save();
			redirect("presupuesto/opago/dataedit/show/$id");
		}else{
			$data['content'] = $error.anchor("presupuesto/opago/dataedit/show/$id",'Regresar');
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
						
			$presup = new DataObject("presupuesto");
			$pk=array('codigoadm'=>$codigoadm,'tipo'=>$fondo);

			for($i=0;$i < $do->count_rel('itopago');$i++){
				$codigopres  = $do->get_rel('itopago','partida',$i);					
				$pago        = $do->get_rel('itopago','pago',$i);
				
				$pk['codigopres'] = $codigopres;
				
				$presup->load($pk);
				$pagado=$presup->get("pagado");
				if($sta=="C")$pagado=$pagado-($pago);

				$presup->set("pagado",$pago);
				
				$presup->save();
			}
			if($sta=="P")$do->set('status','C');
			if($sta=="C")$do->set('status','P');
			
			$do->save();
			
		}
		
		if(empty($error)){
			redirect("presupuesto/opago/dataedit/show/$id");
		}else{
			$data['content'] = $error.anchor("presupuesto/opago/dataedit/show/$id",'Regresar');
			$data['title']   = " Orden de Pago ";
			$data["head"]    = $this->rapyd->get_head().script('jquery.js').script("plugins/jquery.numeric.pack.js");
			$this->load->view('view_ventanas', $data);
		}
	}
	
	function _valida($do){
		$total=0;
		$error='';
		for($i=0;$i < $do->count_rel('itopago');$i++){
			$codigopres = $do->get_rel('itopago','partida' ,$i);
			$pago       = $do->get_rel('itopago','pago'    ,$i);
			if($pago <= 0)$error.="<div class='alert'><p>Partida $codigopres :El pago debe ser positivo </p></div>";
			$total+=$pago;
		}
		if(empty($error)){
			$do->set('total',$total);
		}else{
			$do->error_message_ar['pre_ins']=$error;
			$do->error_message_ar['pre_upd']=$error;
			return false;
		}
	}
	
	function itpartida($partida){
		$estadmin = $this->db->escape($this->input->post('estadmin'));
		$fondo    = $this->db->escape($this->input->post('fondo'));
		$partida  = $this->db->escape($partida);
		$cana=$this->datasis->dameval("SELECT COUNT(*) FROM presupuesto WHERE codigoadm=$estadmin AND codigopres=$partida AND tipo=$fondo");
		if($cana>0){
			return true;
		}else{
			$this->validation->set_message('itpartida',"La partida %s ($partida) No pertenece al la estructura administrativa o al fondo seleccionado");
			return false;	
		}
	}
}
?>