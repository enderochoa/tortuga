<?php
require_once(BASEPATH.'application/controllers/presupuesto/common.php');
class Obra extends Common {

var $titp  = 'Obras';
var $tits  = 'Obra';
var $url   = 'presupuesto/obra/';

function obra(){
	parent::Controller();
	$this->load->library("rapyd");
	$this->datasis->modulo_id(136,1);
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
						'titulo'  =>'Buscar Beneficiario');
		
		$bSPRV=$this->datasis->p_modbus($mSPRV,"proveed");
		
		$filter = new DataFilter2("");
		$filter->db->select(array("a.numero numero","a.fecha fecha","a.cod_prov cod_prov","a.status status","a.monto monto","c.nombre provee"));
		$filter->db->from("obra a");                  
		$filter->db->join("sprv c"       ,"c.proveed=a.cod_prov");
		//$filter->db->where('status !=','F2');
		//$filter->db->where('status !=','F3');
		//$filter->db->where('status !=','F1');
		
		$filter->numero = new inputField("N&uacute;mero", "numero");
		$filter->numero->size=12;
		$filter->numero->clause="likerigth";
		
		$filter->fecha = new dateonlyField("Fecha", "fecha");
		$filter->fecha->dbformat = "Y-m-d";
		$filter->fecha->size=12;
		
		$filter->cod_prov = new inputField("Beneficiario", 'cod_prov');
		$filter->cod_prov->size = 6;
		$filter->cod_prov->append($bSPRV);
		$filter->cod_prov->rule = "required";
		
//		$filter->status = new dropdownField("Estado","status");		
//		$filter->status->option("","");
//		$filter->status->option("B2","Actualizado");
//		$filter->status->option("B1","Sin Actualizar");		
//		$filter->status->option("B3","Pagado");
//		$filter->status->style="width:150px";
		
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
				//case "A":return "Anulado";break;
			}
		}
		
		$grid = new DataGrid("");
		$grid->order_by("numero","desc");
		$grid->per_page = 20;
		$grid->use_function('substr','str_pad','sta');
		
		$grid->column_orderby("N&uacute;mero"    ,$uri                                            ,"numero");	
		$grid->column_orderby("Fecha"            ,"<dbdate_to_human><#fecha#></dbdate_to_human>"  ,"fecha"   ,"align='center'    ");
		$grid->column_orderby("Beneficiario"     ,"provee"                                        ,"provee"  ,"align='left'NOWRAP");
		$grid->column_orderby("Monto"            ,"<number_format><#monto#>|2|,|.</number_format>","monto"   ,"align='right'");
		//$grid->column("Estado"           ,"<sta><#status#></sta>"                       ,"align='center'");
		
		//echo $grid->db->last_query();
		$grid->add($this->url."dataedit/create");
		$grid->build();
		
		//$data['content'] = $filter->output.$grid->output;
		$data['filtro']  = $filter->output;
		$data['content'] = $grid->output;
		$data['script'] = script("jquery.js")."\n";
		$data['title']   = "$this->titp";
		$data["head"]    = $this->rapyd->get_head();
		$this->load->view('view_ventanas', $data);
	}

	function dataedit(){
		
		$this->rapyd->load('dataobject','datadetails',"dataedit");

		$mSPRV=array(
			'tabla'   =>'sprv',
			'columnas'=>array(
			'proveed' =>'C&oacute;odigo',
			'nombre'=>'Nombre',
			'rif'=>'Rif',
			'contacto'=>'Contacto'),
			'filtro'  =>array('proveed'=>'C&oacute;digo','nombre'=>'Nombre','rif'=>'Rif'),
			'retornar'=>array('proveed'=>'cod_prov','nombre'=>'nombre','reteiva'=>'reteiva_prov'),
			'titulo'  =>'Buscar Beneficiario');		
		
		$bSPRV2=$this->datasis->p_modbus($mSPRV ,"sprv");
		
		$modbus=array(
			'tabla'   =>'v_presaldo',
			'columnas'=>array(
				'codigoadm'  =>'Est Admin',
				'fondo'      =>'Fondo',
				'codigo'     =>'C&oacute;digo',
				'ordinal'    =>'Ordinal',
				'denominacion'=>'Denominaci&oacute;n',
				'saldo'       =>'Saldo'
				),
			'filtro'  =>array(
				'codigoadm'  =>'Est Admin',
				'fondo'      =>'Fondo',
				'codigo'      =>'C&oacute;digo',
				'denominacion'=>'Denominaci&oacute;n'
				),
			'retornar'=>array(
				'codigo'    =>'codigopres',
				'fondo'    =>'fondo',
				'codigoadm' =>'codigoadm',
				'ordinal'   =>'ordinal'),
			'where'=>'movimiento = "S" AND saldo > 0 ',
			'titulo'  =>'Busqueda de partidas');

		$mod=$this->datasis->p_modbus($modbus,'v_presaldo');
		
		$script='
			$(".inputnum").numeric(".");
			$(function() {
			});
			$(document).ready(function() {
				$("#tr_reteiva_prov").hide();
			});
			
		';
		
		$do = new DataObject("obra");
		$do->pointer('sprv' ,'sprv.proveed=obra.cod_prov','sprv.nombre as nombre','LEFT');
	
		//$do->rel_one_to_many('itobra', 'itobra', array('numero'=>'obr'));
	
		$edit = new DataDetails($this->tits, $do);
		$edit->back_url = site_url($this->url."/index");
		//$edit->set_rel_title('itobra','Rubro <#o#>');
	
		$edit->script($script,"create");
		$edit->script($script,"modify");
			
		$edit->pre_process('update'  ,'_valida');
		$edit->pre_process('insert'  ,'_valida');
		//$edit->post_process('insert'  ,'_post');
		//$edit->post_process('update'  ,'_post');
	
		$edit->numero  = new inputField("N&uacute;mero", "numero");
		$edit->numero->mode="autohide";
		$edit->numero->when=array('show');
		
		$edit->contrato = new inputField("Contrato", "contrato");
		$edit->contrato->rule='required';
		$edit->contrato->size=10;
	
		$edit->fecha = new  dateonlyField("Fecha",  "fecha");
		$edit->fecha->insertValue = date('Y-m-d');
		$edit->fecha->size =12;
	
		$edit->codigoadm = new inputField("Partida Presupuestaria", "codigoadm");		
		$edit->codigoadm->size=10;
		$edit->codigoadm->rule     = 'required';

		$edit->fondo = new inputField("Fondo", "fondo");
		$edit->fondo->size     = 8;
		$edit->fondo->rule     = 'required';
		$edit->fondo->in       = "codigoadm";
		
		$edit->codigopres = new inputField("Partida", "codigopres");
		$edit->codigopres->rule='required';
		$edit->codigopres->size=10;
		$edit->codigopres->in  = "codigoadm";
		
		$edit->ordinal = new inputField("Ordinal", "ordinal");
		//$edit->ordinal->rule='required';
		$edit->ordinal->size=10;
		$edit->ordinal->append($mod);
		$edit->ordinal->in   = "codigoadm";
		
		$edit->uejecutora = new dropdownField("Unidad Ejecutora", "uejecutora");
		$edit->uejecutora->option("","Seccionar");
		$edit->uejecutora->options("SELECT codigo, nombre FROM uejecutora ORDER BY nombre");
		//$edit->uejecutora->onchange = "get_uadmin();";
		$edit->uejecutora->rule = "required";
		$edit->uejecutora->style = "width:500px";
	
		$edit->cod_prov = new inputField("Beneficiario", 'cod_prov');
		$edit->cod_prov->size     = 4;
		$edit->cod_prov->rule     = "required";
		$edit->cod_prov->append($bSPRV2);
	
		$edit->nombre = new inputField("Nombre", 'nombre');
		$edit->nombre->size = 20;
		$edit->nombre->readonly = true;
		$edit->nombre->pointer = TRUE;
		$edit->nombre->in   = "cod_prov";
	
		$edit->reteiva_prov  = new inputField("reteiva_prov", "reteiva_prov");
		$edit->reteiva_prov->size=1;
		//$edit->reteiva_prov->mode="autohide";
		$edit->reteiva_prov->when=array('modify','create');
		
		$edit->observa = new textAreaField("Observaciones", 'observa');
		$edit->observa->cols = 70;
		$edit->observa->rows = 3;
		
		$edit->porcent = new inputField("Porcentaje Anticipo", "porcent");
		$edit->porcent->rule='required|callback_porcent';
		$edit->porcent->css_class='inputnum';
		$edit->porcent->size=10;
				
		$edit->monto= new inputField("Monto a Total", 'monto');
		$edit->monto->rule='required|callback_positivo';
		$edit->monto->size = 15;
		$edit->monto->css_class = 'inputnum';
		
		$edit->pagado= new inputField("Pagado", 'pagado');
		$edit->pagado->size = 8;
		$edit->pagado->css_class='inputnum';
		$edit->pagado->mode="autohide";
		$edit->pagado->when=array('show');
		
		$edit->pagoviejo= new inputField("Abonado Viejo", 'pagoviejo');
		//$edit->pagoviejo->rule='rumeric';
		$edit->pagoviejo->size = 8;
		$edit->pagoviejo->css_class='inputnum';
		//$edit->pagoviejo->mode="autohide";
		//$edit->pagoviejo->when=array('show');
		
		$edit->demostrado= new inputField("Demostrado", 'demostrado');
		$edit->demostrado->size = 8;
		$edit->demostrado->css_class='inputnum';
		$edit->demostrado->mode="autohide";
		$edit->demostrado->when=array('show');
		
		$status=$edit->get_from_dataobjetct('status');
		if($status=='O1'){
			$action = "javascript:window.location='" .site_url($this->url.'actualizar/'.$edit->rapyd->uri->get_edited_id()). "'";
			$edit->button_status("btn_status",'Actualizar',$action,"TR","show");
			$edit->buttons("modify","delete","save");
		}elseif($status=='O2'){
			$action = "javascript:window.location='" .site_url($this->url.'reversar/'.$edit->rapyd->uri->get_edited_id()). "'";
			$edit->button_status("btn_rever",'Reversar',$action,"TR","show");
		}else{
			$edit->buttons("save");
		}
	
		$edit->buttons("undo","back");
	
		$edit->build();
	
		//SELECT codigo,base1,tari1,pama1 FROM rete
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
		$conten['ivar']=$ivaplica['redutasa'];
		$conten['ivag']=$ivaplica['tasa'];
		$conten['ivaa']=$ivaplica['sobretasa'];
		$conten['imptimbre']=$this->datasis->traevalor('IMPTIMBRE');
		$conten['impmunicipal']=$this->datasis->traevalor('IMPMUNICIPAL');
		
		$smenu['link']=barra_menu('119');
		$data['smenu'] = $this->load->view('view_sub_menu', $smenu,true);
		//$conten["form"]  =&  $edit;
		//$data['content'] = $this->load->view('view_obra', $conten,true);
		$data['content'] = $edit->output;
		$data['title']   = "$this->tits";
		$data["head"]    = $this->rapyd->get_head().script('jquery.js').script("plugins/jquery.numeric.pack.js").script("plugins/jquery.json.min.js");
		$this->load->view('view_ventanas', $data);
	}
	
	function _valida($do){
	
		$besanticipo='N';
		$error      ='';	
		
		$monto      = $do->get('monto'     );
		$porcent    = $do->get('porcent'   );
		$codigoadm  = $do->get('codigoadm' );
		$fondo      = $do->get('fondo'     );
		$codigopres = $do->get('codigopres');
		$ordinal    = $do->get('ordinal'   );
		$cod_prov   = $do->get('cod_prov'  );
		$pagoviejo  = $do->get('pagoviejo' );
		$pagado     = $do->get('pagado'    );
		
		$do->set('status','O1');
		$do->set('pagado',$pagado);
		
		if(empty($error)){
			$anticipo = $monto * $porcent /100;
			$do->set('anticipo',$anticipo);
		}
	}
	
	function actualizar($id){
		$this->rapyd->load('dataobject');
		
		$do = new DataObject("obra");
		$do->load($id);

		$error      = '';
		
		$monto      = $do->get('monto'     );
		$porcent    = $do->get('porcent'   );
		$codigoadm  = $do->get('codigoadm' );
		$fondo      = $do->get('fondo'     );
		$codigopres = $do->get('codigopres');
		$ordinal    = $do->get('ordinal'   );
		$status     = $do->get('status'    );
		$cod_prov   = $do->get('cod_prov'  );
		$uejecutora = $do->get('uejecutora');
		$pagoviejo  = $do->get('pagoviejo' );
		
		$mont = $monto -$pagoviejo;
		$reteiva_prov = $this->datasis->dameval("SELECT reteiva FROM sprv WHERE proveed = '$cod_prov'");
		$do->set('reteiva_prov',$reteiva_prov);
		
		if($status == "O1"){
			$error.=$this->chequeapresup($codigoadm,$fondo,$codigopres,$ordinal,$mont,0,'round($monto,2) > $disponible=round(($presupuesto-$comprometido),2)','El Monto ($monto) es mayor al disponible ($disponible) para la partida ('.$codigoadm.') ('.$fondo.') ('.$codigopres.') ('.$ordinal.')') ;
			//$error.=$this->chequeapresup($codigoadm,$fondo,$codigopres,$ordinal,$mont,0,'$monto > ($presupuesto-$comprometido)',"El Monto ($mont) es mayor al disponible para la partida ($codigoadm) ($fondo) ($codigopres)");

			if(empty($error)){
				$error.=$this->afectapresup($codigoadm,$fondo,$codigopres,$ordinal,$mont,0, 1 ,array("comprometido"));
				
				if(empty($error) && ($pagoviejo <= 0)){
					$antiodirect = $do->get('antiodirect');
					$nodirect=$this->datasis->fprox_numero('nodirect');
				
					$odirect = new DataObject("odirect");
					$odirect->rel_one_to_many('itodirect', 'itodirect', array('numero'=>'numero'));
					$odirect->load($antiodirect);
					
					$m = $monto*$porcent /100;
					
					$odirect->set('numero'        , $nodirect          );
					$odirect->set('obr'           , $id          );
					$odirect->set('observa'       , "Anticipo"   );
					$odirect->set('subtotal'      , $m       );
					$odirect->set('total'         , $m       );
					$odirect->set('total2'        , $m       );
					$odirect->set('exento'        , $m       );
					$odirect->set('cod_prov'      , $cod_prov    );
					$odirect->set('estadmin'      , $codigoadm   );
					$odirect->set('fondo'         , $fondo       );
					$odirect->set('uejecutora'    , $uejecutora  );
					$odirect->set('fecha'         , date('Ymd')  );
					$odirect->set('tipo'          , 'Compra'     );
					$odirect->set('status'        , 'O1'         );
					$odirect->set('multiple'      , 'N'          );
					//$odirect->set_rel('itodirect' ,'partida'   , $codigopres,0);
					//$odirect->set_rel('itodirect' ,'ordinal'   , $ordinal   ,0);
					//$odirect->set_rel('itodirect' ,'importe'   , $m         ,0);				
					//$odirect->set_rel('itodirect' ,'precio'    , $m         ,0);
					//$odirect->set_rel('itodirect' ,'cantidad'  , 1          ,0);
					$odirect->save();
					
					$antiodirect = $odirect->get('numero');
				}
				if(empty($error)){
					$do->set('antiodirect',$antiodirect);
					$do->set('fcomprome',date('Ymd'));
					$do->set('status','O2');
					$do->save();
				}
			}
		}else{
			$error.="<div class='alert'><p>la Obra No puede ser Actualizada</p></div>";
		}
		
		if(empty($error)){
			redirect($this->url."dataedit/show/$id");
		}else{
			$data['content'] = $error.anchor($this->url."dataedit/show/$id",'Regresar');
			$data['title']   = " $this->tits ";
			$data["head"]    = $this->rapyd->get_head().script('jquery.js').script("plugins/jquery.numeric.pack.js");
			$this->load->view('view_ventanas', $data);
		}
	}
	
	function reversar($id){
		$this->rapyd->load('dataobject');
		
		$do = new DataObject("obra");
		$do->load($id);

		$error      = '';
		
		$monto      = $do->get('monto'     );
		$porcent    = $do->get('porcent'   );
		$codigoadm  = $do->get('codigoadm' );
		$fondo      = $do->get('fondo'     );
		$codigopres = $do->get('codigopres');
		$ordinal    = $do->get('ordinal'   );
		$status     = $do->get('status'    );
		$cod_prov   = $do->get('cod_prov'  );
		$uejecutora = $do->get('uejecutora');
		$pagoviejo  = $do->get('pagoviejo' );
		
		$mont = $monto - $pagoviejo;
		
		if($status == "O2"){
			$error.=$this->chequeapresup($codigoadm,$fondo,$codigopres,$ordinal,$mont,0,'$monto > ($comprometido-$causado)',"El Monto ($monto) es mayor al disponible para la partida ($codigoadm) ($fondo) ($codigopres)");
		
			if(empty($error))
				$error.=$this->afectapresup($codigoadm,$fondo,$codigopres,$ordinal,$mont,0, -1 ,array("comprometido"));
			
			if(empty($error)){
				$do->set('status','O1');
				$do->save();
			}
		}else{
			$error.="<div class='alert'><p>la Obra No puede ser Reversada</p></div>";
		}
		
		if(empty($error)){
			redirect($this->url."dataedit/show/$id");
		}else{
			$data['content'] = $error.anchor($this->url."dataedit/show/$id",'Regresar');
			$data['title']   = " $this->tits ";
			$data["head"]    = $this->rapyd->get_head().script('jquery.js').script("plugins/jquery.numeric.pack.js");
			$this->load->view('view_ventanas', $data);
		}
	}
	
	function ivaplica2($mfecha=NULL){
		if(empty($mfecha)) $mfecha=date('Ymd');
		$CI =& get_instance();
		$qq = $CI->db->query("SELECT tasa, redutasa, sobretasa FROM civa WHERE fecha < '$mfecha' ORDER BY fecha DESC LIMIT 1");
		$rr = $qq->row_array();
		//$aa = each($rr);
		return $rr;
	}
	
	function _ivaplica($mfecha=NULL){
		if(empty($mfecha)) $mfecha=date('sYmd');
		$qq = $this->datasis->damerow("SELECT tasa AS g, redutasa AS r, sobretasa AS a FROM civa WHERE fecha < '$mfecha' ORDER BY fecha DESC LIMIT 1");
		$rr=array();
		foreach ($qq AS $val){
			$rr[$val]=$val.'%';
		}
		$rr['0']='0%';
		return $rr;
	}
	
	function tari1(){
		$creten=$this->db->escape($this->input->post('creten'));
		$a=$this->datasis->damerow("SELECT base1,tari1,pama1 FROM rete WHERE codigo=$creten");
		echo json_encode($a);
	}
	
	function porcent($valor){
		if($valor >= 0 && $valor <=100){
			return true;
		}else{
			$this->validation->set_message('porcent',"El porcentaje del anticipo no es valido");
			return false;	
		}
	}
	
	function positivo($valor){
		if($valor > 0 ){
			return true;
		}else{
			$this->validation->set_message("monto","El Monto de la Obra debe ser positivo");
			return false;	
		}
	}
}
