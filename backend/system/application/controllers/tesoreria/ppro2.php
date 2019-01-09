<?php
class Ppro extends Controller {

	var $titp='Pagos a Beneficiarioes';
	var $tits='Pago a Beneficiario';
	var $url='tesoreria/ppro/';

	function ppro(){
		parent::Controller();
		$this->load->library("rapyd");
		$this->formatopres =$this->datasis->traevalor('FORMATOPRES');
		$this->flongpres   =strlen(trim($this->formatopres));
	}

	function index(){
		redirect($this->url."filteredgrid");
	}

	function filteredgrid(){
		//$this->datasis->modulo_id(101,1);
		$this->rapyd->load("datafilter","datagrid");

		$mSPRV=array(
				'tabla'   =>'sprv',
				'columnas'=>array(
				'proveed' =>'C&oacute;odigo',
				'nombre'=>'Nombre',
				'contacto'=>'Contacto'),
				'filtro'  =>array('proveed'=>'C&oacute;digo','nomb_prov'=>''),
				'retornar'=>array('proveed'=>'cod_prov' ),
				'titulo'  =>'Buscar Beneficiario');

		$bSPRV=$this->datasis->p_modbus($mSPRV,"proveed");

		$filter = new DataFilter("Filtro de $this->titp","ppro");


		$filter->numero = new inputField("N&uacute;mero", "numero");
		$filter->numero->size  =10;

		//$filter->tipo = new dropdownField("Orden de ", "tipo");
		//$filter->tipo->option("","");
		//$filter->tipo->option("Compra"  ,"Compra");
		//$filter->tipo->option("Servicio","Servicio");
		//$filter->tipo->style="width:100px;";

		$filter->fecha = new dateonlyField("Fecha", "fecha");
		$filter->fecha->size=12;

		//$filter->uejecutora = new inputField("Unidad Ejecutora", "uejecutora");
		//$filter->uejecutora->size=12;

		$filter->cod_prov = new inputField("Beneficiario", 'cod_prov');
		$filter->cod_prov->size = 6;
		$filter->cod_prov->append($bSPRV);
		$filter->cod_prov->rule = "required";

		$filter->benefi= new inputField("Beneficiario", "benefi");
		$filter->benefi->size= 20;

		$filter->buttons("reset","search");

		$filter->build();
		$uri = anchor($this->url.'dataedit/show/<#numero#>','<str_pad><#numero#>|8|0|STR_PAD_LEFT</str_pad>');

		$grid = new DataGrid("Lista de ".$this->titp);
		$grid->order_by("numero","desc");
		$grid->per_page = 20;
		$grid->use_function('substr','str_pad');

		$grid->column("N&uacute;mero"    ,$uri);
		//$grid->column("Tipo"             ,"tipo"                                        ,"align='center'");
		$grid->column("Fecha"            ,"<dbdate_to_human><#fecha#></dbdate_to_human>","align='center'");
		//$grid->column("Unidad Ejecutora" ,"uejecutora");
		$grid->column("Beneficiario"        ,"cod_prov");
		$grid->column("Beneficiario"     ,"benefici");
		$grid->column("Pago"             ,"<number_format><#total#>|2|,|.</number_format>","align='rigth'");
	//	$grid->column("Devoluci&oacute;n","<number_format><#devo#>|2|,|.</number_format>","align='rigth'");
		//echo $grid->db->last_query();
		$grid->add($this->url."dataedit/create");
		$grid->build();

		$data['content'] = $filter->output.$grid->output;
		$data['title']   = " $this->titp ";
		$data["head"]    = $this->rapyd->get_head();
		$this->load->view('view_ventanas', $data);
	}

	function dataedit(){
		//$this->datasis->modulo_id(101,1);
		$this->rapyd->load('dataobject','datadetails');

		$mSPRV=array(
				'tabla'   =>'sprv',
				'columnas'=>array(
				'proveed' =>'C&oacute;odigo',
				'nombre'=>'Nombre',
				'contacto'=>'Contacto'),
				'filtro'  =>array('proveed'=>'C&oacute;digo','nombre'=>'Nombre'),
				'retornar'=>array('proveed'=>'cod_prov'     , 'nombre'=>'nomb_prov'),
				'titulo'  =>'Buscar Beneficiario');

		$bSPRV=$this->datasis->p_modbus($mSPRV,"proveed");

		$modbus=array(
			'tabla'   =>'odirect',
			'columnas'=>array(
				'numero'  =>'N&uacute;mero',
				'fecha'   =>'fecha',
				'tipo'    =>'tipo'
				),
			'filtro'  =>array('numero'  =>'N&uacute;mero',
				'fecha'   =>'fecha',
				'tipo'    =>'tipo'),
			'retornar'=>array(
				  'numero'=>'orden_<#i#>',
				  'total'=>'total_<#i#>',
				  'pago'=>'abonado_<#i#>'),
			'p_uri'=>array(
				  4=>'<#i#>',
				  5=>'<#cod_prov#>'),
			'where' =>'cod_prov = <#cod_prov#> AND status="O"',//
			'script'=>array('cal_debe(<#i#>)'),
			'titulo'=>'Busqueda de Ordenes de Pago');

		$btn=$this->datasis->p_modbus($modbus,'<#i#>/<#cod_prov#>');
		
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
					'codbanc'=>'codbanc'//,'banco'=>'nombreb'
					 ),
				'where'=>'activo = "S"',
				'titulo'  =>'Buscar Bancos');
			
		$bBANC=$this->datasis->p_modbus($mBANC,"banc");

		$do = new DataObject("ppro");

		$do->rel_one_to_many('itppro', 'itppro', array('numero'=>'numero'));

		$edit = new DataDetails($this->tits, $do);
		$edit->back_url = site_url($this->url."/filteredgrid");
		$edit->set_rel_title('itppro','Rubro <#o#>');

		$edit->pre_process('insert'  ,'_valida');
		$edit->pre_process('update'  ,'_valida');
		//$edit->post_process('insert'  ,'_paiva');
		//$edit->post_process('update'  ,'_paiva');

		$edit->numero  = new inputField("N&uacute;mero", "numero");
		$edit->numero->mode="autohide";
		$edit->numero->when=array('show');

		$edit->fecha = new  dateonlyField("Fecha",  "fecha");
		$edit->fecha->insertValue = date('Y-m-d');
		$edit->fecha->size =12;

		$edit->cod_prov = new inputField("Beneficiario", 'cod_prov');
		$edit->cod_prov->size     = 6;
		$edit->cod_prov->rule     = "required";
		$edit->cod_prov->append($bSPRV);
		$edit->cod_prov->readonly=true;

		$edit->nomb_prov = new inputField("Nombre", 'nomb_prov');
		$edit->nomb_prov->db_name   = ' ';
		$edit->nomb_prov->size      = 50;
		$edit->nomb_prov->readonly  = true;
		$edit->nomb_prov->in        = "cod_prov";

		$edit->benefi = new inputField("Beneficiario", 'benefi');
		$edit->benefi->size = 50;

		$edit->observa = new textAreaField("Observaciones", 'observa');
		$edit->observa->cols = 106;
		$edit->observa->rows = 3;
		
		$edit->codbanc =  new inputField("Banco", 'codbanc');
		$edit->codbanc-> size     = 3;
		$edit->codbanc-> rule     = "required";
		$edit->codbanc-> append($bBANC);
    $edit->codbanc-> readonly=true;
    
    $edit->cheque =  new inputField("Cheque", 'cheque');
		$edit->cheque-> size  = 20;
		$edit->cheque-> rule  = "required";
		          
		$edit->total = new inputField("Total", 'total');
		$edit->total->css_class='inputnum';
		$edit->total->size = 8;

		$edit->itorden = new inputField("(<#o#>) Orden", "orden_<#i#>");
		$edit->itorden->rule='callback_repetido|required|callback_itorden';
		$edit->itorden->size=15;
		$edit->itorden->append('<img src="/tortuga/assets/default/images/system-search.png"  alt="Busqueda de Ordenes de Pago" title="Busqueda de Ordenes de Pago" border="0" onclick="modbusdepen(<#i#>)"/>');
		$edit->itorden->db_name='orden';
		$edit->itorden->rel_id ='itppro';
		$edit->itorden->readonly =true;

		$edit->ittotal = new inputField("(<#o#>) Total", "total_<#i#>");
		$edit->ittotal->db_name  ='total';
		$edit->ittotal->rel_id   ='itppro';
		$edit->ittotal->rule     ='numeric';
		//$edit->ittotal->mode     ='autohide';
		$edit->ittotal->size     =8;

		$edit->itabonado = new inputField("(<#o#>) Abonado", "abonado_<#i#>");
		$edit->itabonado->db_name  ='abonado';
		$edit->itabonado->rel_id   ='itppro';
		$edit->itabonado->rule     ='numeric';
		//$edit->itabonado->mode     ='autohide';
		$edit->itabonado->size     =8;

		$edit->itmonto = new inputField("(<#o#>) Abonar", "monto_<#i#>");
		$edit->itmonto->css_class= 'inputnum';
		$edit->itmonto->db_name  = 'monto';
		$edit->itmonto->rel_id   = 'itppro';
		$edit->itmonto->rule     = 'callback_positivo';
		$edit->itmonto->onchange = 'cal_total(<#i#>);';
		$edit->itmonto->size     = 8;

		$status=$edit->get_from_dataobjetct('status');
		if($status=='P'){
			$action = "javascript:window.location='" .site_url($this->url.'/actualizar/'.$edit->rapyd->uri->get_edited_id()). "'";
			$edit->button_status("btn_status",'Actualizar',$action,"TR","show");
			$edit->buttons("modify","delete","save");
		}elseif($status=='C'){
			$action = "javascript:window.location='" .site_url($this->url.'/reversar/'.$edit->rapyd->uri->get_edited_id()). "'";
			$edit->button_status("btn_rever",'Reversar',$action,"TR","show");
		}else{
			$edit->buttons("save");
		}

		$edit->buttons("undo","back","add_rel");
		$edit->build();

		$smenu['link']   = barra_menu('101');
		$data['smenu']   = $this->load->view('view_sub_menu', $smenu,true);
		$conten["form"]  =&  $edit;
		$data['content'] = $this->load->view('view_ppro', $conten,true);
		//$data['content'] = $edit->output;
		$data['title']   = " $this->tits ";
		$data["head"]    = $this->rapyd->get_head().script('jquery.js').script("plugins/jquery.numeric.pack.js").script("plugins/jquery.json.min.js");
		$this->load->view('view_ventanas', $data);
	}

	function _valida($do){

		$error='';
		$tot=0;
		for($i=0;$i < $do->count_rel('itppro');$i++){
			$orden      = $do->get_rel('itppro','orden'   ,$i);
			$total      = $do->get_rel('itppro','total'   ,$i);
			$abonado    = $do->get_rel('itppro','abonado' ,$i);
			$monto      = $do->get_rel('itppro','monto'   ,$i);

			$debe       = $total - $abonado;
			$tot        = $monto;

			if($monto > $debe )$error.="<div class='alert'><p>No se puede abonar mas de lo adeudado para la orden de pago ($orden)</p></div>";
		}
		$do->set('total'      ,    $tot      );
		$do->set('status'     ,    'P'       );
		if(!empty($error)){
			$do->error_message_ar['pre_ins']=$error;
			$do->error_message_ar['pre_upd']=$error;
			return false;
		}
	}

	function positivo($valor){
		if ($valor <= 0){
			$this->validation->set_message('itmonto',"El campo Monto debe ser positivo");
			return FALSE;
		}
  	return TRUE;
	}

	function repetido($orden){
		if(isset($this->__rorden)){
			if(in_array($orden, $this->__rorden)){
				$this->validation->set_message('repetido',"El rublo %s ($orden) esta repetido");
				return false;
			}
		}
		$this->__rorden[]=$orden;
		return true;
	}
	
	function itorden($orden){
		$cod_prov    = $this->db->escape($this->input->post('cod_prov'));
		$orden      = $this->db->escape($orden);
		$cana=$this->datasis->dameval("SELECT COUNT(*) FROM odirect WHERE cod_prov=$cod_prov AND numero=$orden");// AND status='O'
		
		if($cana>0){
			return true;
		}else{
			$this->validation->set_message('itorden',"La orden %s ($orden) No pertenece al proveedor ($cod_prov)");
			return false;	
		}
	}
	
	function actualizar($id){
		
		$this->rapyd->load('dataobject');
		
		$ppro = new DataObject("ppro");
		$ppro->rel_one_to_many('itppro', 'itppro', array('numero'=>'numero'));
		$ppro->load($id);
		$codbanc=$ppro->get('codbanc');
		
		$banc = new DataObject("banc");
		$banc->load($codbanc);
		$saldo=$banc->get('saldo');
		
		$odirect = new DataObject("odirect");
    
		$ocompra = new DataObject("ocompra");
		$ocompra->rel_one_to_many('itocompra', 'itocompra', array('numero'=>'numero'));
		$error      = "";
		
		$presup = new DataObject("presupuesto");
		
		$partidaiva=$this->datasis->traevalor("PARTIDAIVA");
		//$pk['codigopres'] = $partidaiva;
		//$presup->load($pk);
    
		if(empty($error)){                
			$sta=$ppro->get('status');
			if(($sta=="P")){// AND $status=="T"
				$t=0;				
				for($i=0;$i < $do->count_rel('itocompra');$i++){
					$codigopres  = $do->get_rel('itocompra','partida',$i);
					$importe     = $do->get_rel('itocompra','importe',$i);
        
					$t += $importe;
					//if($importe > $causado)
					//	$error.="<div class='alert'><p>No se Puede Completar la Transaccion debido a que el monto de la orden de pago ($importe) es mayor al monto causado($causado) para la partida: $codigopres</p></div>";
				}
				
				if($t>$saldo)$error.="<div class='alert'><p>no hay saldo Suficiente en el Banco</p></div>";
				    
				if(empty($error)){
					for($j=0;$j < $ppro->count_rel('itppro');$j++){
						$orden  = $ppro->get_rel('itppro','orden',$j);
						$odirect->load($orden); 
						
						$ocompra->load_where('odirect'    ,   $orden);
						
						$codigoadm  = $ocompra->get('estadmin');
						$fondo      = $ocompra->get('fondo');
						$numero     = $ocompra->get('numero');
						$pk=array('codigoadm'=>$codigoadm,'tipo'=>$fondo);
						
						//$status = $odirect->get('status');
						
						for($i=0;$i < $ocompra->count_rel('itocompra');$i++){					
							$codigopres  = $ocompra->get_rel('itocompra','partida',$i);
							$importe     = $ocompra->get_rel('itocompra','importe',$i);
							$iva         = $ocompra->get_rel('itocompra','iva'    ,$i);
							//$mont        = $importe*(($iva+100)/100);
							$mont        = $importe;
          	
							$pk['codigopres'] = $codigopres;
          	
							$presup->load($pk);
							$pagado  =  $presup->get("pagado");
							$pagado  =  $pagado+$mont;
          	
							$presup->set("pagado",$pagado);
						
							$presup->save();
						}
          	
						$ivaa  =  $ocompra->get('ivaa');
						$ivag  =  $ocompra->get('ivag');
						$ivar  =  $ocompra->get('ivar');
						$ivan  =  $ivag+$ivar+$ivaa;

						$pk['codigopres'] = $partidaiva;
						$presup->load($pk);
      
          	
						$pagado =$presup->get("pagado");
						$pagado+=$ivan;
						$presup->set("pagado",$pagado);
						$presup->save();
          	
          //print_r($presup->get_all());
					//exit;
          	
						$ocompra->set('status','R');
						$ocompra->save();
						
						$odirect->set('status','R');
						$odirect->save();
					}
					
					$ppro->set('status','C');
					$ppro->save();
				}else{
					$error.="<div class='alert'><p>No se Puede Completar la operacion</p></div>";
				}
			}else{
				$error.="<div class='alert'><p>No se Puede Completar la operacion s</p></div>";
			}
		}
		
		if(empty($error)){
			redirect($this->url."dataedit/show/$id");
		}else{
			$data['content'] = $error.anchor($this->url."/dataedit/show/$id",'Regresar');
			$data['title']   = " ".$this->tits." ";
			$data["head"]    = $this->rapyd->get_head().script('jquery.js').script("plugins/jquery.numeric.pack.js");
			$this->load->view('view_ventanas', $data);
		}
	} 
	
	function reversar($id){
		
		$this->rapyd->load('dataobject');
		
		$ppro = new DataObject("ppro");
		$ppro->rel_one_to_many('itppro', 'itppro', array('numero'=>'numero'));
		$ppro->load($id);
		
		$odirect = new DataObject("odirect");
    
		$ocompra = new DataObject("ocompra");
		$ocompra->rel_one_to_many('itocompra', 'itocompra', array('numero'=>'numero'));
		
    
		$error      = "";
		
    
		$presup = new DataObject("presupuesto");
		
    
		$partidaiva=$this->datasis->traevalor("PARTIDAIVA");
		//$pk['codigopres'] = $partidaiva;
		//$presup->load($pk);
    
		if(empty($error)){                
			$sta=$ppro->get('status');
			if(($sta=="C")){// AND $status=="T"
				
				//for($i=0;$i < $do->count_rel('itocompra');$i++){
				//	$codigopres  = $do->get_rel('itocompra','partida',$i);
				//	$importe     = $do->get_rel('itocompra','importe',$i);
        //
				//	$pk['codigopres'] = $codigopres;
				//	$presup->load($pk);
        //
				//	$causado =$presup->get("causado");
        //
				//	//if($importe > $causado)
				//	//	$error.="<div class='alert'><p>No se Puede Completar la Transaccion debido a que el monto de la orden de pago ($importe) es mayor al monto causado($causado) para la partida: $codigopres</p></div>";
				//}
    
				if(empty($error)){
					for($j=0;$j < $ppro->count_rel('itppro');$j++){
						$orden  = $ppro->get_rel('itppro','orden',$j);
						$odirect->load($orden); 
						
						$ocompra->load_where('odirect'    ,   $orden);
						
						$codigoadm  = $ocompra->get('estadmin');
						$fondo      = $ocompra->get('fondo');
						$numero     = $ocompra->get('numero');
						$pk=array('codigoadm'=>$codigoadm,'tipo'=>$fondo);
						
						//$status = $odirect->get('status');
						
						for($i=0;$i < $ocompra->count_rel('itocompra');$i++){					
							$codigopres  = $ocompra->get_rel('itocompra','partida',$i);
							$importe     = $ocompra->get_rel('itocompra','importe',$i);
							$iva         = $ocompra->get_rel('itocompra','iva'    ,$i);
							//$mont        = $importe*(($iva+100)/100);
							$mont        = $importe;
          	
							$pk['codigopres'] = $codigopres;
          	
							$presup->load($pk);
							$pagado  =  $presup->get("pagado");
							$pagado  =  $pagado-$mont;
          	
							$presup->set("pagado",$pagado);
						
							$presup->save();
						}

						$ivaa  =  $ocompra->get('ivaa');
						$ivag  =  $ocompra->get('ivag');
						$ivar  =  $ocompra->get('ivar');
						$ivan  =  $ivag+$ivar+$ivaa;
						
						$pk['codigopres'] = $partidaiva;
						$presup->load($pk);
						
						$pagado =$presup->get("pagado");
						$pagado-=$ivan;
						$presup->set("pagado",$pagado);
						$presup->save();
          	
          //print_r($presup->get_all());
					//exit;
          	
						$ocompra->set('status','O');
						$ocompra->save();
						
						$odirect->set('status','O');
						$odirect->save();
					}
					
					$ppro->set('status','P');
					$ppro->save();
				}else{
					$error.="<div class='alert'><p>No se Puede Completar la operacion</p></div>";
				}
			}else{
				$error.="<div class='alert'><p>No se Puede Completar la operacion s</p></div>";
			}
		}
		
		if(empty($error)){
			redirect($this->url."dataedit/show/$id");
		}else{
			$data['content'] = $error.anchor($this->url."/dataedit/show/$id",'Regresar');
			$data['title']   = " ".$this->tits." ";
			$data["head"]    = $this->rapyd->get_head().script('jquery.js').script("plugins/jquery.numeric.pack.js");
			$this->load->view('view_ventanas', $data);
		}
	} 
	
	
}
?>

