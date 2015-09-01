<?php
class oreinte extends Controller {
	
	var $titp='Reintegros';
	var $tits='Reintegro';
	var $url='presupuesto/oreinte/';

	function oreinte(){
		parent::Controller();
		$this->load->library("rapyd");
		$this->formatopres=$this->datasis->traevalor('FORMATOPRES');
		$this->flongpres  =strlen(trim($this->formatopres));		
	}
	function index(){
		
		redirect($this->url."filteredgrid");
	}


	function filteredgrid(){
		//$this->datasis->modulo_id(101,1);
		$this->rapyd->load("datafilter","datagrid");
			
		$filter = new DataFilter("Filtro de $this->titp","oreinte");
						
		$filter->fecha = new dateonlyField("Fecha", "fecha");
		$filter->fecha->size=12;
		
		$filter->odirect = new dropdownField("Pago Directo","odirect");
		$filter->odirect->option("","");
		$filter->odirect->options("SELECT numero,numero a FROM odirect ORDER BY numero");
		
		$filter->observa = new inputField("Observaci&oacute;n", "observa");
		$filter->observa->size=60;
		
		$filter->buttons("reset","search");
		$filter->build();
		$uri = anchor($this->url.'dataedit/show/<#numero#>','<str_pad><#numero#>|8|0|STR_PAD_LEFT</str_pad>');
		
		$grid = new DataGrid("Lista de ".$this->titp);
		$grid->order_by("numero","desc");
		$grid->per_page = 20;
		$grid->use_function('substr','str_pad');
		
		$grid->column("N&uacute;mero"    ,$uri);
		$grid->column("Fecha"            ,"<dbdate_to_human><#fecha#></dbdate_to_human>","align='center'");
		$grid->column("Pago Directo"     ,"odirect");		
		//$grid->column("Monto"            ,"<number_format><#total#>|2|,|.</number_format>","align='rigth'");
		$grid->column("Observaci&oacute;n"     ,"observa");
		
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
				'retornar'=>array('proveed'=>'cod_prov', 'nombre'=>'nombre','reteiva'=>'reteiva_prov' ),
				'titulo'  =>'Buscar Beneficiario');
			
		$bSPRV=$this->datasis->p_modbus($mSPRV,"proveed");
		
		$modbus=array(
			'tabla'   =>'itodirect',
			'columnas'=>array(
				'partida'      =>'Partida',
				'descripcion'=>'Descripci&oacute;n'),
			'filtro'  =>array(
				'partida' =>'Partida',
				'descripcion'=>'Descripci&oacute;n'),
			'retornar'=>array(
				'partida'=>'partida_<#i#>',
				'importe'=>'reinte_<#i#>'),
			'p_uri'=>array(
				4=>'<#i#>',
				5=>'<#odirect#>'),
			'where'=>'itodirect.numero=<#odirect#>',			
			'titulo'  =>'Busqueda de partidas');
			
			
		$btn=$this->datasis->p_modbus($modbus,'<#i#>/<#odirect#>');
		
		$do = new DataObject("oreinte");
		
		$do->rel_one_to_many('itoreinte', 'itoreinte', array('numero'=>'numero'));

		$edit = new DataDetails($this->tits, $do);
		$edit->back_url = site_url($this->url."/filteredgrid");
		$edit->set_rel_title('itoreinte','Rubro <#o#>');
		
		$edit->pre_process('insert'  ,'_valida');
		$edit->pre_process('update'  ,'_valida');
		//$edit->post_process('insert'  ,'_paiva');
		//$edit->post_process('update'  ,'_paiva');
	
		$edit->numero  = new inputField("N&uacute;mero", "numero");
		$edit->numero->mode="autohide";
		$edit->numero->when=array('show');
		
		$edit->odirect =new dropdownField("Pago Directo","odirect");
		$edit->odirect->options("SELECT numero,numero a FROM odirect ORDER BY numero");
      
		$edit->fecha = new  dateonlyField("Fecha",  "fecha");
		$edit->fecha->insertValue = date('Y-m-d');
		$edit->fecha->size =12;

		$edit->observa = new textAreaField("Observaciones", 'observa');
		$edit->observa->cols = 106;
		$edit->observa->rows = 3;
		
		//$edit->tcantidad = new inputField("tcantidad", 'tcantidad');
		//$edit->tcantidad->size = 8;
		
		$edit->total = new inputField("Total", 'total');
		$edit->total->css_class='inputnum';
		$edit->total->size = 8;
		
		$edit->itpartida = new inputField("(<#o#>) Partida", "partida_<#i#>");
		$edit->itpartida->rule='callback_repetido|required|callback_itpartida';
		$edit->itpartida->size=15;
		$edit->itpartida->append('<img src="/tortuga/assets/default/images/system-search.png"  alt="Busqueda de partidas" title="Busqueda de partidas" border="0" onclick="modbusdepen(<#i#>)"/>');
		$edit->itpartida->db_name='partida';
		$edit->itpartida->rel_id ='itoreinte';
		//$edt->itpartida->readonly =true;
		
		$edit->itdescripcion = new inputField("(<#o#>) Descripci&oacute;n", "descripcion_<#i#>");
		$edit->itdescripcion->db_name  ='descripcion';
		$edit->itdescripcion->maxlength=80;
		$edit->itdescripcion->size     =30;
		$edit->itdescripcion->rule     = 'required';
		$edit->itdescripcion->rel_id   ='itoreinte';
		
		$edit->itreinte = new inputField("(<#o#>) Monto", "reinte_<#i#>");
		$edit->itreinte->css_class='inputnum';
		$edit->itreinte->db_name  ='reinte';
		$edit->itreinte->rel_id   ='itoreinte';
		$edit->itreinte->rule     ='numeric';
		//$edit->itreinte->onchange ='cal_importe(<#i#>);';
		$edit->itreinte->size     =8;
		//$edit->itprecio->insertValue=0;

	
		$status=$edit->get_from_dataobjetct('status');
		if($status=='P'){
			$action = "javascript:window.location='" .site_url('presupuesto/oreinte/actualizar/'.$edit->rapyd->uri->get_edited_id()). "'";
			$edit->button_status("btn_status",'Actualizar',$action,"TR","show");
			$edit->buttons("modify","delete","save");
		}elseif($status=='C'){
			$action = "javascript:window.location='" .site_url('presupuesto/oreinte/reversar/'.$edit->rapyd->uri->get_edited_id()). "'";
			$edit->button_status("btn_rever",'Reversar',$action,"TR","show");
		}else{
			$edit->buttons("save");
		}
				
		$edit->buttons("undo","back","add_rel");
		$edit->build();
	
		$smenu['link']=barra_menu('101');
		$data['smenu'] = $this->load->view('view_sub_menu', $smenu,true);
		$conten["form"]  =& $edit;
		$data['content'] = $this->load->view('view_oreinte', $conten,true); 
		//$data['content'] = $edit->output;
		$data['title']   = " $this->tits ";
		$data["head"]    = $this->rapyd->get_head().script('jquery.js').script("plugins/jquery.numeric.pack.js").script("plugins/jquery.json.min.js");
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
	
	function itpartida($partida){
		
		$odirect    = $this->db->escape($this->input->post('odirect'));
		$partida  = $this->db->escape($partida);
		$cana=$this->datasis->dameval("SELECT COUNT(*) FROM itodirect WHERE numero=$odirect");
		if($cana>0){
			return true;
		}else{
			$this->validation->set_message('itpartida',"La partida %s ($partida) No pertenece al Pago directo $odirect");
			return false;	
		}
	}
	
	
	function reversar($id){
		$this->rapyd->load('dataobject');
		
		$do = new DataObject("odirect");
		$do->rel_one_to_many('itodirect', 'itodirect', array('numero'=>'numero'));
		$do->load($id);
		
		$sta=$do->get('status');
		if($sta=='C'){
			$codigoadm   = $do->get('estadmin');
			$fondo       = $do->get('fondo');
						
			$presup = new DataObject("presupuesto");
			$pk=array('codigoadm'=>$codigoadm,'tipo'=>$fondo);
			$error='';			
			
			for($i=0;$i < $do->count_rel('itodirect');$i++){
				$codigopres  = $do->get_rel('itodirect','partida',$i);					
				$importe     = $do->get_rel('itodirect','importe',$i);
				$iva         = $do->get_rel('itodirect','iva'    ,$i);				
				$mont        = $importe*(($iva+100)/100);
				
				$pk['codigopres'] = $codigopres;
				
				$presup->load($pk);
				$comprometido=$presup->get("comprometido");
				$causado     =$presup->get("causado");
				$pagado      =$presup->get("pagado");
				
				$comprometido-=$mont;
				$causado     -=$mont;
				$pagado      -=$mont;
				
				$presup->set("comprometido",$comprometido);
				$presup->set("causado"     ,$causado);
				$presup->set("pagado"      ,$pagado);
				
				$presup->save();
			}
			$do->set('status','P');
			$do->save();
		}
		
		redirect($this->url."dataedit/show/$id");
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
		
		$error='';
		$numero=$do->get('numero');
		$itodirect = new DataObject('itodirect');
		$tot=0;
		for($i=0;$i < $do->count_rel('itoreinte');$i++){
			$partida = $do->get_rel('itoreinte','partida'    ,$i);
			$reinte  = $do->get_rel('itoreinte','reinte'     ,$i);
			
			$itodirect ->load_where('numero',$numero);
			$itodirect ->load_where('partida',$partida);
			$importe   = $itodirect->get('importe');
			
			if($importe < $reinte)
				$error.="<div class='alert'><p>Partida ($partida): El monto del reintegro ($reinte) es mayor a el pagado ($importe)</p></div>";
				
			$tot+=$reinte;
		}
		
		if(empty($error)){
			$itodirect->set('total',$tot);
		}else{
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
}
?>
