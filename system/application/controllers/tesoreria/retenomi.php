<?php
require_once(BASEPATH.'application/controllers/presupuesto/common.php');

class Retenomi extends Common {

	var $titp='Deducciones de N&oacute;mina';
	var $tits='Deducciones de N&oacute;mina';
	var $url ='tesoreria/retenomi/';

	function Retenomi(){
		parent::Controller();
		$this->load->library("rapyd");
	}

	function index(){
		redirect($this->url."filteredgrid");
	}

	function filteredgrid(){
		
		$this->rapyd->load("datafilter2","datagrid");
		$this->rapyd->uri->keep_persistence();
				
		$filter = new DataFilter2("Filtro de $this->titp","nomi");

		$filter->numero = new inputField("N&uacute;mero", "numero");
		$filter->numero->db_name="a.numero";
		$filter->numero->size=12;
		$filter->numero->clause="likerigth";

		$filter->fecha = new dateonlyField("Fecha", "fecha");
		$filter->fecha->db_name="fecha";
		$filter->fecha->size=12;
		
		$filter->descrip = new inputField("Descripci&oacute;n", "descrip");
		$filter->descrip->db_name="descrip";
		$filter->descrip->size  =10;
		
		$filter->opago = new inputField("Orden de Pago", "opago");
		$filter->opago->db_name="opago";
		$filter->opago->size  =10;

		$filter->status = new dropdownField("Estado","status");
		$filter->status->db_name="a.status";		
		$filter->status->option("","");
		$filter->status->option("P","Pendiente");
		$filter->status->option("C","Comprometido");		
		$filter->status->option("O","Ordenado Pago");
		$filter->status->option("E","Pagado");
		$filter->status->style="width:150px";
		
		$filter->buttons("reset","search");
		
		$filter->build();
		$uri = anchor($this->url.'dataedit/show/<#numero#>','<str_pad><#numero#>|8|0|STR_PAD_LEFT</str_pad>');
		
		function sta($status){
			switch($status){
				case "P":return "Pendiente"    ;break;
				case "C":return "Comprometido" ;break;
				case "O":return "Ordenado Pago";break;
				case "E":return "Pagado"       ;break;
			}
			return $status;
		}

		$grid = new DataGrid("Lista de ".$this->titp);
		$grid->order_by("numero","desc");
		$grid->per_page = 20;
		$grid->use_function('substr','str_pad','sta','action');
		
		$grid->column("N&uacute;mero"    ,$uri);
		$grid->column("O. Pago"          ,"<str_pad><#opago#>|8|0|STR_PAD_LEFT</str_pad>"               );
		$grid->column("Descripcion"      ,"descrip"                                                      );
		$grid->column("Fecha"            ,"<dbdate_to_human><#fecha#></dbdate_to_human>","align='center'");
		$grid->column("Asignaciones"     ,"<nformat><#asig#>|2|,|.</nformat>","align='right'");
		$grid->column("Deducciones"      ,"<nformat><#rete#>|2|,|.</nformat>","align='right'");
		$grid->column("Estado"           ,"<sta><#status#></sta>"                       ,"align='center'");

		$grid->add($this->url."dataedit/create");
		$grid->build();
		
		$data['content'] = $filter->output.$grid->output;
		$data['title']   = " $this->titp ";
		$data["head"]    = $this->rapyd->get_head();
		$this->load->view('view_ventanas', $data);
	}
	
	
	function dataedit(){
		//$this->datasis->modulo_id(115,1);
		$this->rapyd->load('dataobject','datadetails');
		
		$this->rapyd->uri->keep_persistence();

		$do = new DataObject("nomi");
		$do->rel_one_to_many('asignomi', 'asignomi', array('numero'=>'numero'));
		$do->rel_one_to_many('retenomi', 'retenomi', array('numero'=>'numero'));
		$do->rel_pointer('asignomi','ppla' ,'asignomi.codigopres=ppla.codigo','ppla.denominacion as denomi');
		$do->rel_pointer('retenomi','ppla as a' ,'retenomi.codigopres=a.codigo','a.denominacion as denomip'   );

		//$do->load(14);
		//print_r($do->_rel_pointer_data);
		//exit();
		
		$edit = new DataDetails($this->tits, $do);
		$edit->back_url = site_url($this->url."filteredgrid");
		$edit->set_rel_title('pades','Rubro <#o#>');

		$edit->pre_process('insert'  ,'_valida');
		$edit->pre_process('update'  ,'_valida');
		//$edit->post_process('insert','_post_insert');
		//$edit->post_process('update','_post_update');
		//$edit->post_process('delete','_post_delete');
				
		$status=$edit->get_from_dataobjetct('status');
		//**************************INICIO ENCABEZADO********************************************************************
		$edit->numero  = new inputField("N&uacute;mero", "numero");
		$edit->numero->mode="autohide";
		$edit->numero->when=array('show');
	
		$edit->fecha= new  dateonlyField("Fecha",  "fecha");
		$edit->fecha->rule        = 'required|chfecha';
		$edit->fecha->insertValue = date('Ymd');
		$edit->fecha->size        =12;
		$edit->fecha->mode="autohide";
	
		$edit->descrip = new textareaField("Descripci&oacute;n", 'descrip');
		$edit->descrip->rows     = 3;
		$edit->descrip->cols    = 60;
		$edit->descrip->mode="autohide";
		
		$edit->asig  = new inputField("Total Asignaciones", "asig");
		$edit->asig->size = 15;
		$edit->asig->readonly = true;
		$edit->asig->css_class='inputnum';
		$edit->asig->mode="autohide";
		
		$edit->rete  = new inputField("Total Deducciones", "rete");
		$edit->rete->size     = 15;
		$edit->rete->readonly = true;
		$edit->rete->css_class='inputnum';		

		//************************** FIN   ENCABEZADO********************************************************************

		//**************************INICIO DETALLE DE ASIGNACIONES  *****************************************************
		$edit->itcodigoadm = new inputField("Estructura	Administrativa","codigoadm_<#i#>");
		$edit->itcodigoadm->db_name='codigoadm';
		$edit->itcodigoadm->rel_id ='asignomi';				
		$edit->itcodigoadm->rule   ='required';
		$edit->itcodigoadm->size   =15;
		$edit->itcodigoadm->mode   = "autohide";
		       
		$edit->itfondo = new inputField("(<#o#>) Fondo","fondo_<#i#>");
		$edit->itfondo->rule     ='required';
		$edit->itfondo->db_name  ='fondo';
		$edit->itfondo->rel_id   ='asignomi';
		$edit->itfondo->size     =15;
		$edit->itfondo->mode     = "autohide";
		
		$edit->itcodigopres = new inputField("(<#o#>) ", "codigopres_<#i#>");
		$edit->itcodigopres->rule     ='required|callback_itorden';
		$edit->itcodigopres->size     =15;
		$edit->itcodigopres->db_name  ='codigopres';
		$edit->itcodigopres->rel_id   ='asignomi';
		$edit->itcodigopres->readonly =true; 
		$edit->itcodigopres->mode     = "autohide";
		
		//if($status == 'D2' || $status == 'D3')$edit->itpago->mode     = "autohide";

		$edit->itordinal= new inputField("(<#o#>) Ordinal","ordinal_<#i#>");
		//$edit->itordinal->rule   ='required';
		$edit->itordinal->db_name  ='ordinal';
		$edit->itordinal->rel_id   ='asignomi';
		$edit->itordinal->readonly =true;
		$edit->itordinal->size     =15;
		$edit->itordinal->mode     = "autohide";
		
		$edit->itdenomi= new inputField("(<#o#>) Denominacion","denomi_<#i#>");
		//$edit->itdenomi->rule   ='required';
		$edit->itdenomi->db_name  ='denomi';
		$edit->itdenomi->rel_id   ='asignomi';
		$edit->itdenomi->readonly =true;
		$edit->itdenomi->pointer  =true;
		$edit->itdenomi->size     =40;
		$edit->itdenomi->mode     = "autohide";
		
		$edit->itmontoa = new inputField("(<#o#>) Monto", 'montoa_<#i#>');
		$edit->itmontoa->db_name   ='monto';
		$edit->itmontoa->size      = 15;
		$edit->itmontoa->rule      ='callback_positivo';
		$edit->itmontoa->rel_id    ='asignomi';
		$edit->itmontoa->css_class ='inputnum';
		$edit->itmontoa->onchange  = "cal_asig();";
		//$edit->itmontoa->readonly  = true;
		//$edit->itmontoa->mode     = "autohide";
		
				
		//************************** FIN   DETALLE DE ORDENES DEPAGO*****************************************************
		
		//**************************INICIO DETALLE DE DEDUCCIONES DE NOMINA*******************************************
		$edit->it2codigoadm = new inputField("Estructura	Administrativa","codigoadm_<#i#>");
		$edit->it2codigoadm->db_name='codigoadm';
		$edit->it2codigoadm->rel_id ='retenomi';				
		$edit->it2codigoadm->rule='required';
		$edit->it2codigoadm->size   =15;
		          
		$edit->it2fondo = new inputField("(<#o#>) Fondo","fondo_<#i#>");
		$edit->it2fondo->rule   ='required';
		$edit->it2fondo->db_name='fondo';
		$edit->it2fondo->rel_id ='retenomi';
		$edit->it2fondo->size     =15;

		$numero = $edit->rapyd->uri->get_edited_id();
		
		$modbus=array(
			'tabla'   =>'asignomi',
			'columnas'=>array(
				'codigoadm'   =>'Est. Admin',   
				'fondo'       =>'Fondo',        
				'codigopres'  =>'Partida',      
				'ordinal'     =>'Ordinal',
				'denominacion'=>'Denominaci&oacute;n'
				),
			'filtro'  =>array(
				'codigoadm'   =>'Est. Admin',   
				'fondo'       =>'Fondo',        
				'codigopre'   =>'Partida',      
				'ordinal'     =>'Ordinal',
				'denominacion'=>'Partida',),
			'retornar'=>array(
				'codigoadm'   =>'codigoadm_<#i#>',
				'fondo'       =>'fondo_<#i#>',
				'codigopres'  =>'codigopres_<#i#>',
				'ordinal'     =>'ordinal_<#i#>',
				'denominacion'=>'denomip_<#i#>'),
			'p_uri'=>array(4=>'<#i#>',),
			'where'=>"numero = $numero",// AND saldo > 0 AND movimiento = "S"
			'join' =>array('ppla','asignomi.codigopres = ppla.codigo','ppla.denominacion'),
			'groupby' => 'codigopres',
				
			'titulo'  =>'Busqueda de partidas');
		
		$btn=$this->datasis->p_modbus($modbus,"<#i#>/$numero");
		
		$edit->it2codigopres = new inputField("(<#o#>) ", "codigopres_<#i#>");
		$edit->it2codigopres->rule     ='required|callback_itorden';
		$edit->it2codigopres->size     =15;
		$edit->it2codigopres->db_name  ='codigopres';
		$edit->it2codigopres->rel_id   ='retenomi';
		$edit->it2codigopres->readonly =true; 
		$edit->it2codigopres->append($btn);
		          
		//if($sta2tus == 'D2' || $status == 'D3')$edit->itpago->mode     = "autohide";
              
		$edit->it2ordinal= new inputField("(<#o#>) Ordinal","ordinal_<#i#>");
		//$edit-> itordinal->rule   ='required';
		$edit->it2ordinal->db_name='ordinal';
		$edit->it2ordinal->rel_id ='retenomi';
		$edit->it2ordinal->readonly =true;
		$edit->it2ordinal->size     =15;
		          
		$edit->it2denomip= new inputField("(<#o#>) Denominacion","denomip_<#i#>");
		$edit->it2denomip->db_name='denomip';
		$edit->it2denomip->rel_id ='retenomi';
		$edit->it2denomip->readonly =true;
		$edit->it2denomip->pointer  =true;
		$edit->it2denomip->size     =40;
		           
		$edit->it2montor = new inputField("(<#o#>) Monto", 'montor_<#i#>');
		$edit->it2montor->db_name   ='monto';
		$edit->it2montor->size      = 15;
		$edit->it2montor->rule      ='callback_positivo';
		$edit->it2montor->rel_id    ='retenomi';
		$edit->it2montor->css_class ='inputnum';
		$edit->it2montor->onchange  = "cal_rete();";
	  
		//************************** FIN   DETALLE DE DE MOVIMIENTOS BANCARIOS*******************************************
			
		if($status=='O'){
			//$action = "javascript:window.location='" .site_url($this->url.'actualizar/'.$edit->rapyd->uri->get_edited_id()). "'";
			//$edit->button_status("btn_status",'Comprometer',$action,"TR","show");
			$edit->buttons("modify","save");
		}elseif($status == 'C'){
			$action = "javascript:window.location='" .site_url($this->url.'reversar/'.$edit->rapyd->uri->get_edited_id()). "'";
			$edit->button_status("btn_anular",'Descomprometer',$action,"TR","show");
		}else{
			$edit->buttons("save");
		}

		$edit->button_status("btn_add_asignomi" ,'Agregar Asigancion',"javascript:add_asignomi()","AS",'modify');
		$edit->button_status("btn_add_asignomi2",'Agregar Asigancion',"javascript:add_asignomi()","AS",'create');		
		$edit->button_status("btn_add_retenomi" ,'Agregar Deducciones',"javascript:add_retenomi()","RE","create");
		$edit->button_status("btn_add_retenomi2",'Agregar Deducciones',"javascript:add_retenomi()","RE","modify");
		$edit->buttons("undo","back");
		
		$edit->build();

		$smenu['link']   = barra_menu('208');
		$data['smenu']   = $this->load->view('view_sub_menu', $smenu,true);
		$conten["form"]  =&  $edit;
		$data['content'] = $this->load->view('view_retenomi', $conten,true);
		//$data['content'] = $edit->output;
		$data['title']   = " $this->tits ";
		$data["head"]    = $this->rapyd->get_head().script('jquery.js').script("plugins/jquery.numeric.pack.js").script("plugins/jquery.json.min.js");
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
		if ($valor <= 0){
			$this->validation->set_message('positivo',"El campo Monto dede ser mayor a cer0(0)");
			return FALSE;
		}
  	return TRUE;
	}
	
	function _valida($do){
		
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
		
		$status = $do->get('status');
				
		$tasig=0;$b=array();$error='';
		if($status=="P"){
				
			for($i=0;$i < $do->count_rel('asignomi');$i++){
				$ordinal   = $do->get_rel('asignomi','ordinal'    ,$i);
				$codigopres= $do->get_rel('asignomi','codigopres' ,$i);
				$codigoadm = $do->get_rel('asignomi','codigoadm'  ,$i);
				$fondo     = $do->get_rel('asignomi','fondo'      ,$i);
				$monto     = $do->get_rel('asignomi','monto'      ,$i);
				
				$error.=$this->chequeapresup($codigoadm,$fondo,$codigopres,$ordinal,$monto,0,'round($monto,2) > round(($presupuesto-$comprometido),2)',"El Monto ($monto) es mayor al disponible para la partida ($codigoadm) ($fondo) ($codigopres)");//
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
						$do->set_rel('asignomi','status' , 'C');
					}
					$do->save();
				}
			}
		}else{
			$error.="No se puede realizar la operacion para la nomina $numero";	
		}	

		$this->sp_presucalc($codigoadm);
		
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
						$do->set_rel('asignomi','status' , 'P');
					}
					$do->save();
				}
			}
		}else{
			$error.="No se puede realizar la operacion para la nomina $numero";	
		}	

		$this->sp_presucalc($codigoadm);
		
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
	
			
	function _post_insert($do){
		$numero = $do->get('numero');
		logusu('nomi',"Creo nomina $nomi");
		//redirect($this->url."actualizar/$id");
	}
	function _post_update($do){
				$numero = $do->get('numero');
		logusu('nomi',"Modifico nomina $nomi");
	}
	function _post_delete($do){
		$numero = $do->get('numero');
		logusu('nomi',"Elimino nomina $nomi");
	}
}
