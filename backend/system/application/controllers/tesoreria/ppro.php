<?php
require_once(BASEPATH.'application/controllers/presupuesto/common.php');

class Ppro extends Common {

	var $titp='Desembolsos';
	var $tits='Desembolso';
	var $url='tesoreria/ppro/';
	var $anular = false;

	function ppro(){
		parent::Controller();
		$this->load->library("rapyd");
		$this->formatopres =$this->datasis->traevalor('FORMATOPRES');
		$this->flongpres   =strlen(trim($this->formatopres));
		//$this->datasis->modulo_id(208,1);
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
		    'rif'=>'Rif',
				'contacto'=>'Contacto'),
				'filtro'  =>array('proveed'=>'C&oacute;digo','nombre'=>'Nombre','rif'=>'Rif'),
				'retornar'=>array('proveed'=>'cod_prov' ),
				'titulo'  =>'Buscar Beneficiario');
		

		$bSPRV=$this->datasis->p_modbus($mSPRV,"proveed");

		$filter = new DataFilter("Filtro de $this->titp");

		$filter->db->select("a.cheque cheque,a.id id,a.fecha fecha,a.tipo tipo,a.status status,a.cod_prov cod_prov,a.benefi benefi,a.monto monto,b.nombre proveed");
		$filter->db->from("mbanc a");
		$filter->db->join("sprv b"    ,"b.proveed=a.cod_prov", "LEFT");
		//$filter->db->where("a.tipo ","E");
		//$filter->db->where("a.tipo !=", "A");
		//$filter->db->where("a.tipo !=", "B");
		//$filter->db->where("a.tipo !=", "C");
		//$filter->db->where("a.tipo !=", "D");

		$filter->id = new inputField("N&uacute;mero", "id");
		$filter->id->db_name="a.id";
		$filter->id->size  =10;
		
		$filter->cheque = new inputField("Cheque", "cheque");
		$filter->cheque->db_name="a.cheque";
		$filter->cheque->size  =10;

		$filter->fecha = new dateonlyField("Fecha", "fecha");
		$filter->fecha->size=12;

		$filter->cod_prov = new inputField("Beneficiario", 'cod_prov');
		$filter->cod_prov->size = 6;
		$filter->cod_prov->append($bSPRV);

		$filter->status = new dropdownField("Estado","status");
		$filter->status->option("","");
		$filter->status->option("E1","Sin Ejecutar");
		$filter->status->option("E2","Ejecutado");
		$filter->status->style="width:150px";

		$filter->buttons("reset","search");

		$filter->build();
		$uri = anchor($this->url.'dataedit/show/<#id#>','<str_pad><#id#>|8|0|STR_PAD_LEFT</str_pad>');

		function sta($status){
			switch($status){
				case "E1":return "Sin Ejecutar";break;
				case "E2":return "Ejecutado";break;
				case "A":return "Anulado";break;
			}
		}

		$grid = new DataGrid("Lista de ".$this->titp);
		$grid->order_by("id","desc");
		$grid->per_page = 20;
		$grid->use_function('substr','str_pad');
		$grid->use_function('sta');

		$grid->column("N&uacute;mero"    ,$uri);
		$grid->column("Cheque"           ,"cheque" );
		$grid->column("Fecha"            ,"<dbdate_to_human><#fecha#></dbdate_to_human>","align='center'");
		$grid->column("Beneficiario"        ,"proveed");
		$grid->column("Pago"             ,"<number_format><#monto#>|2|,|.</number_format>","align='right'");
		$grid->column("Estado"           ,"<sta><#status#></sta>"                       ,"align='center'");

		$grid->add($this->url."dataedit/create");
		$grid->build();
		//echo $grid->db->last_query();

		$data['content'] = $filter->output.$grid->output;
		$data['title']   = " $this->titp ";
		$data["head"]    = $this->rapyd->get_head();
		$this->load->view('view_ventanas', $data);
	}

	function dataedit(){
		//$this->datasis->modulo_id(101,1);
		$this->rapyd->load('dataobject','datadetails');
		$this->rapyd->uri->keep_persistence();

		$mSPRV=array(
				'tabla'   =>'sprv',
				'columnas'=>array(
				'proveed' =>'C&oacute;odigo',
				'nombre'=>'Nombre',
		    'rif'=>'Rif',
				'contacto'=>'Contacto'),
				'filtro'  =>array('proveed'=>'C&oacute;digo','nombre'=>'Nombre','rif'=>'Rif'),
				'retornar'=>array('proveed'=>'cod_prov'     , 'nombre'=>'nombrep'),
				'titulo'  =>'Buscar Beneficiario');

		$bSPRV=$this->datasis->p_modbus($mSPRV,"proveed");

		$modbus=array(
			'tabla'   =>'odirect',
			'columnas'=>array(
				'numero'  =>'N&uacute;mero',
				'fecha'   =>'Fecha',
				'tipo'    =>'Tipo',
				'cod_prov'    =>'Beneficiario'
				
				),
			'filtro'  =>array(
				'numero'  =>'N&uacute;mero',
				'fecha'   =>'fecha',
				'tipo'    =>'tipo'),
			'retornar'=>array(
				  'numero'    =>'orden_<#i#>',
				  'total'     =>'monto_<#i#>',
					'cod_prov'  =>'cod_prov',
					'observa'   =>'temp'),//,					
			'p_uri'=>array(
				  4=>'<#i#>',
				  5=>'<#cod_prov#>'),
			'where' =>'(status="O2" OR status = "H2" OR status = "M2" OR status = "N2" OR status = "F2" OR status = "B2" OR status = "R2" OR status = "G2" OR status = "I2" OR status = "S2") AND IF(<#cod_prov#>=".....",cod_prov LIKE "%",cod_prov = <#cod_prov#>) ',//			
			'script'=>array('cal_observa()'),			
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
					'saldo'=>'Saldo'),//39, 40
				'retornar'=>array(
					'codbanc'=>'codbanc','banco'=>'nombreb'
					 ),
				'where'=>'activo = "S"',
				'titulo'  =>'Buscar Bancos');

		$bBANC=$this->datasis->p_modbus($mBANC,"banc");

		$do = new DataObject("mbanc");

		$do->rel_one_to_many('itppro', 'itppro', array('id'=>'mbanc'));
		$do->pointer('banc' ,'banc.codbanc=mbanc.codbanc','banc.banco as nombreb','LEFT');
		$do->pointer('sprv' ,'sprv.proveed=mbanc.cod_prov','sprv.nombre as nombrep','LEFT');

		$edit = new DataDetails($this->tits, $do);
		$edit->back_url = site_url($this->url."filteredgrid");
		$edit->set_rel_title('itppro','Rubro <#o#>');

		$edit->pre_process('insert'  ,'_valida');
		$edit->pre_process('update'  ,'_valida');
		$edit->post_process('insert','_post_insert');
		$edit->post_process('update','_post_update');
		$edit->post_process('delete','_post_delete');

		$edit->id  = new inputField("N&uacute;mero", "id");
		$edit->id->mode="autohide";
		$edit->id->when=array('show');
		
		$edit->temp  = new inputField("N&uacute;mero", "temp");

		$edit->fecha = new  dateonlyField("Fecha Cheque",  "fecha");
		//$edit->fecha->insertValue = date('Y-m-d');
		$edit->fecha->size        =12;
		$edit->fecha->rule        = 'required';//|timestampFromDBDate
		
		
		$edit->fechapago = new  dateonlyField("Fecha Desembolso",  "fechapago");
		$edit->fechapago->insertValue = date('Y-m-d');
		$edit->fechapago->size        =12;
		$edit->fechapago->rule        = 'required';

		$edit->tipo = new inputField("","tipo");
		$edit->tipo-> insertValue = "E";
		$edit->tipo->mode         = "autohide";
		$edit->tipo->when=array('');

		$edit->cod_prov = new inputField("Beneficiario", 'cod_prov');
		$edit->cod_prov->size     = 6;
		$edit->cod_prov->rule     = "required";
		$edit->cod_prov->append($bSPRV);
		//$edit->cod_prov->readonly=true;

		$edit->nombrep = new inputField("Nombre", 'nombrep');
		$edit->nombrep->size      = 60;
		$edit->nombrep->readonly  = true;
		$edit->nombrep->pointer   = true;

		$edit->observa = new textAreaField("Observaciones", 'observa');
		$edit->observa->cols = 30;
		$edit->observa->rows = 3;

		$edit->codbanc =  new inputField("Banco", 'codbanc');
		$edit->codbanc-> size     = 3;
		$edit->codbanc-> rule     = "required";
		$edit->codbanc-> append($bBANC);
		$edit->codbanc-> readonly=true;

    $edit->nombreb = new inputField("Nombre","nombreb");
    $edit->nombreb->size     = 20;
    $edit->nombreb->readonly = true;
    $edit->nombreb->pointer  = true;
    
    $edit->tipo_doc = new dropdownField("Tipo Documento","tipo_doc");
    $edit->tipo_doc->option("CH","Cheque"         );
    //$edit->tipo_doc->option("NC","Nota de Credito");
    $edit->tipo_doc->option("ND","Nota de Debito" );
    //$edit->tipo_doc->option("DP","Deposito"         );
    //$edit->tipo_doc->option("CH","Cheque"         );
    $edit->tipo_doc->style="width:180px";

    $edit->cheque =  new inputField("Cheque", 'cheque');
	  $edit->cheque-> size  = 20;
	  $edit->cheque->rule   = "required";//callback_chexiste_cheque|

		$edit->monto = new inputField("Total", 'monto');
		$edit->monto->mode     = 'autohide';
		$edit->monto->when     = array('show');
		$edit->monto->size = 8;

		$edit->itorden = new inputField("(<#o#>) ", "orden_<#i#>");
		$edit->itorden->rule     ='callback_repetido|required|callback_itorden';
		$edit->itorden->size     =15;
		$edit->itorden->db_name  ='orden';
		$edit->itorden->rel_id   ='itppro';
		//$edit->itorden->readonly =true;
		$edit->itorden->append('<img src="/tortuga/assets/default/images/system-search.png"  alt="Busqueda de Ordenes de Pago" title="Busqueda de Ordenes de Pago" border="0" onclick="modbusdepen(<#i#>)"/>');

		$edit->itmonto = new inputField("(<#o#>) Abonar", "monto_<#i#>");
		$edit->itmonto->db_name  = 'monto';
		$edit->itmonto->rel_id   = 'itppro';
		//$edit->itmonto->mode     = 'autohide';
		$edit->itmonto->when     = array('show','modify');
		$edit->itmonto->size     = 8;
	//	$edit->itmonto->readonly = true;

		$status=$edit->get_from_dataobjetct('status');
		if($status=='E1'){
			$action = "javascript:window.location='" .site_url($this->url.'/actualizar/'.$edit->rapyd->uri->get_edited_id()). "'";
			$edit->button_status("btn_status",'Actualizar',$action,"TR","show");
			$edit->buttons("modify","delete","save");
		}elseif($status=='E2'){
			$action = "javascript:window.location='" .site_url($this->url.'cambcheque/dataedit/modify/'.$edit->rapyd->uri->get_edited_id()). "'";
			$edit->button_status("btn_rever",'Cambiar Banco/Cheque',$action,"TR","show");
			
			$action = "javascript:window.location='" .site_url('tesoreria/ppro/anular/'.$edit->rapyd->uri->get_edited_id()). "'";
			$edit->button_status("btn_anular",'Anular',$action,"TR","show");
				
			if($this->datasis->puede('2083')){
				$action = "javascript:window.location='" .site_url($this->url.'/reversar/'.$edit->rapyd->uri->get_edited_id()). "'";
				$edit->button_status("btn_rever",'Reversar',$action,"TR","show");
			}
		}else{
			$edit->buttons("save");
		}
		
		$edit->buttons("undo","back","add_rel");
		$edit->build();

		$smenu['link']   = barra_menu('208');
		$data['smenu']   = $this->load->view('view_sub_menu', $smenu,true);
		$conten["form"]  =&  $edit;
		$data['content'] = $this->load->view('view_ppro', $conten,true);
		//$data['content'] = $edit->output;
		$data['title']   = " $this->tits ";
		$data["head"]    = $this->rapyd->get_head().script('jquery.js').script("plugins/jquery.numeric.pack.js").script("plugins/jquery.json.min.js");
		$this->load->view('view_ventanas', $data);
	}

	function _valida($do){
	
		$this->rapyd->load('dataobject');

		$odirect = new DataObject("odirect");		

		$cheque  =$do->get('cheque'  );
		$tipo_doc=$do->get('tipo_doc');
		$id      =$do->get('id'      );
		
		$error='';
		
		$this->chexiste_cheque($cheque,$tipo_doc,$id,$e);
		$error.=$e;
		$tot=0;
		for($i=0;$i < $do->count_rel('itppro');$i++){
			$orden      = $do->get_rel('itppro','orden'   ,$i);

			$odirect->load($orden);

			$ivaa      =  $odirect->get('ivaa');
			$ivag      =  $odirect->get('ivag');
			$ivar      =  $odirect->get('ivar');
			$iva       =  $odirect->get('iva');
			$subtotal  =  $odirect->get('subtotal');
			$reteiva   =  $odirect->get('reteiva');
			$reten     =  $odirect->get('reten');
			$ivan      =  $ivag+$ivar+$ivaa+$iva;
			$total     =  $odirect->get('total');
			//$total     =  ($subtotal-$reten)+($ivan-($reteiva));

			$do->set_rel('itppro' ,'monto' ,$total ,$i);

			$tot  +=$total;
		}
		
		$codbanc =  $do->get('codbanc');

		$banc   = new DataObject("banc");
		$banc   ->load($codbanc);
		$saldo  = $banc->get('saldo');
		$activo = $banc->get('activo');
		$banco  = $banc->get('banco' );

		if($activo != 'S' )$error.="<div class='alert'><p>El banco ($banco) esta inactivo</p></div>";

		if($tot > $saldo )$error.="<div class='alert'><p>El Monto ($tot) del cheque es mayor al disponible ($saldo) en el banco ($banco)</p></div>";

		$proveed = $do->get('cod_prov');
		$nombre = $this->datasis->dameval("SELECT nombre FROM sprv WHERE proveed ='$proveed' ");
		
		$do->set('estampa', date('Y-m-d') );
		$do->set('monto'  , $tot );
		$do->set('tipo'   , 'E'  );
		$do->set('status' , 'E1' );
		$do->set('benefi' , $nombre);

		if(!empty($error)){
			$do->error_message_ar['pre_ins']=$error;
			$do->error_message_ar['pre_upd']=$error;
			return false;
		}
	}

	function positivo($valor){
		if ($valor <= 0){
			$this->validation->set_message('ittotal',"El campo Monto debe ser positivo");
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
		if($cod_prov != "''"){
			$cana=$this->datasis->dameval("SELECT COUNT(*) FROM odirect WHERE cod_prov=$cod_prov AND numero=$orden");// AND status='O'
		}else{
			$cana=$this->datasis->dameval("SELECT COUNT(*) FROM odirect WHERE numero=$orden");// AND status='O'
		}

		if($cana>0){
			return true;
		}else{
			$this->validation->set_message('itorden',"La orden ($orden) No pertenece al proveedor ($cod_prov)");
			return false;
		}
	}

	function actualizar($id){

		$this->rapyd->load('dataobject');

		$error='';
		
		$ord     = new DataObject("ordinal");

		$mbanc     =  new DataObject("mbanc");
		$mbanc     -> rel_one_to_many('itppro', 'itppro', array('id'=>'mbanc'));
		$mbanc     -> load($id);
		$m_codbanc =  $mbanc->get('codbanc');
		$m_monto   =  $mbanc->get('monto');

		$banc   = new DataObject("banc");
		$banc   ->load($m_codbanc);
		$saldo  = $banc->get('saldo');
		$activo = $banc->get('activo');

		$odirect = new DataObject("odirect");
		$odirect -> rel_one_to_many('pacom', 'pacom', array('numero'=>'pago'));
		$odirect -> rel_one_to_many('itodirect', 'itodirect', array('numero'=>'numero'));
		$odirect -> rel_one_to_many('itfac'    , 'itfac'    , array('numero'=>'numero'));
		//$odirect -> rel_one_to_many('islr', 'islr', array('numero'=>'odirect'));
		//$odirect->pointer('sprv' ,'sprv.proveed = odirect.cod_prov','sprv.nomfis as nom_prov, sprv.rif as rif_prov','LEFT');

		$ocompra = new DataObject("ocompra");
		$ocompra->rel_one_to_many('itocompra', 'itocompra', array('numero'=>'numero'));
		//$ocompra->pointer('sprv' ,'sprv.proveed=ocompra.cod_prov','sprv.nombre as nom_prov, sprv.rif as rif_prov');

		$riva    =  new DataObject("riva");

		$presup = new DataObject("presupuesto");
		$presupante  = new DataObject("presupuestoante");
		$ordinalante = new DataObject("ordinalante");
		
		//$islr   = new DataObject("islr");
		
		$partidaiva=$this->datasis->traevalor("PARTIDAIVA");

		if($activo != 'S' )$error.="<div class='alert'><p>El banco ($banco) esta inactivo</p></div>";

		if($m_monto > $saldo )$error.="<div class='alert'><p>El Monto ($tot) del cheque es mayor al disponible ($saldo) en el banco ($banco)</p></div>";

		$sta=$mbanc->get('status');
		if(($sta=="E1")){
			$mbanc->set('status','E2');
			$m_benefi = $mbanc->get('benefi');
			$t=0;

			if(empty($error)){
			$tislr =0;

				for($j=0;$j < $mbanc->count_rel('itppro');$j++){

					$orden    = $mbanc->get_rel('itppro','orden',$j);
					$it_monto = $mbanc->get_rel('itppro','monto',$j);

		//			echo $orden;
					
					$odirect->load($orden);
					//$odirect->load_where('numero',$orden);
					
	//				print_r($odirect->get_all());
//exit();
					$status       =  $odirect->get('status'     );
					$od_numero    =  $odirect->get('numero'     );
					$od_estadmin  =  $odirect->get('estadmin'   );
					$od_fondo     =  $odirect->get('fondo'      );
					$od_subtotal  =  $odirect->get('subtotal'   );
					$od_ivag      =  $odirect->get('ivag'       );
					$od_ivaa      =  $odirect->get('ivaa'       );
					$od_ivar      =  $odirect->get('ivar'       );
					$od_reten     =  $odirect->get('reten'      );
					$od_reteiva   =  $odirect->get('reteiva'    );
					$od_fechafac  =  $odirect->get('fechafac'   );
					$od_cod_prov  =  $odirect->get('cod_prov'    );
					$od_creten    =  $odirect->get('creten'      );
					$od_multiple  =  $odirect->get('multiple'    );
					$od_pr        =  $od_reten*100/$od_subtotal;
					//echo $od_numero;
					//exit($status);
echo $status."----";

				  if($status == "F2" ){
echo "F2";

						for($g=0;$g < $odirect->count_rel('pacom');$g++){
							$p_t       = $odirect->get_rel('pacom','total' ,$g);
							$p_compra  = $odirect->get_rel('pacom','compra',$g);
//echo $p_compra;
							$ocompra->load($p_compra);
						//	print_r($ocompra->get_all());
//exit();
							$oc_codigoadm  = $ocompra->get('estadmin'  );
							$oc_fondo      = $ocompra->get('fondo'     );
							$oc_status     = $ocompra->get('status'    );
							$oc_cod_prov   = $ocompra->get('cod_prov'  );
							$oc_creten     = $ocompra->get('creten'    );
							$oc_fechafac   = $ocompra->get('fechafac'  );
//echo "SELECT SUM(a.monto) FROM itppro a JOIN mbanc d ON d.id=a.mbanc JOIN odirect b ON a.orden=b.numero JOIN pacom c ON b.numero=c.pago WHERE c.compra=$p_compra AND d.status='E2'";
							$pagado=$this->datasis->dameval("SELECT SUM(a.monto) FROM itppro a JOIN mbanc d ON d.id=a.mbanc JOIN odirect b ON a.orden=b.numero JOIN pacom c ON b.numero=c.pago WHERE c.compra=$p_compra AND d.status='E2'");
							$pagado+=$it_monto;
//echo $pagado;
							$ivaa         =  $ocompra->get('ivaa');
							$ivag         =  $ocompra->get('ivag');
							$ivar         =  $ocompra->get('ivar');
							$subtotal     =  $ocompra->get('subtotal');
							$reteiva      =  $ocompra->get('reteiva');
							$impmunicipal =  $ocompra->get('imptimbre');
							$imptimbre    =  $ocompra->get('impmunicipal');
							$tislr=$reten =  $ocompra->get('reten');
							$ivan         =  $ivag+$ivar+$ivaa;
							//$total        =  ($subtotal - $reten)+($ivan-($reteiva))-$impmunicipal -$imptimbre;
							echo $total        =  $ocompra->get('total');
	//exit();						
							//$pr = $reten*100/$subtotal;
							
							echo $total."AAAAAAAAAAAAAAAAASSSSSSSSSSSSSSSSSSSSDFDDDDDDDDDDDDDDDdd";

							if($total){//==$pagado
							
						//	echo "entro";
								
								$pk=array('codigoadm'=>$oc_codigoadm,'tipo'=>$oc_fondo);
								
								$partidaiva=$this->datasis->traevalor("PARTIDAIVA");
								$pk['codigopres'] = $partidaiva;
								$presup->load($pk);
					
								$pasignacion   =$presup->get("asignacion");
						
								for($h=0;$h < $odirect->count_rel('pacom');$h++){
									$p_compra  = $odirect->get_rel('pacom','compra',$h);
									echo $p_compra."-----";
									$ocompra->load($p_compra);
									$oc_codigoadm  = $ocompra->get('estadmin'    );
									$oc_fondo      = $ocompra->get('fondo'       );
									$reteiva_prov  = $ocompra->get('reteiva_prov');
									
									for($k=0;$k < $ocompra->count_rel('itocompra');$k++){
										$islrid = '';
										$codigopres  = $ocompra->get_rel('itocompra','partida',$k);
										$importe     = $ocompra->get_rel('itocompra','importe',$k);
										$iva         = $ocompra->get_rel('itocompra','iva'    ,$k);
										$ordinal     = $ocompra->get_rel('itocompra','ordinal',$k);
										//$islr        = $ocompra->get_rel('itocompra','islr'   ,$k);
										//$imptimbre   = $ocompra->get_rel('itocompra','imptimbre',$k);
										//$impmunicipal= $ocompra->get_rel('itocompra','impmunicipal',$k);										
										
										if($pasignacion>0)
											$mont    = $importe;//-$islr-$imptimbre-$impmunicipal;
										else
											$mont    = $importe + (($importe*$iva/100)-((($importe*$iva/100)*$reteiva_prov)/100));//-$islr-$imptimbre-$impmunicipal;
											
											
										$pk=array('codigoadm'=>$oc_codigoadm,'tipo'=>$oc_fondo,'codigopres'=>$codigopres);
										
										echo "***".$mont;
										$presup->load($pk);
										print_r($presup->get_all());
										$pagado=$presup->get("pagado");
										$pagado=$pagado+$mont;

										$presup->set("pagado",$pagado);
										$presup->save();
										
										if(!empty($ordinal)){
											$arr = array("codigoadm"=>$oc_codigoadm,"fondo"=>$oc_fondo,"codigopres"=>$codigopres,"ordinal"=>$ordinal);
											//print_r($arr);
											
											$ord->load($arr);
											
											print_r($ord->get_all());
											$pag =$ord->get("pagado" );    
											$pag += $mont;
											$ord->set("pagado",$pag);
											
											$ord->save();
																			
										}	
									}
									$ii = $ivan-($reteiva);
			
									if($pasignacion>0){
										$pk['codigopres'] = $partidaiva;
										$presup->load($pk);
				
										$pagado =$presup->get("pagado");
										$pagado+=$ii;
										$presup->set("pagado",$pagado);
										$presup->save();
									}
									
									$this->sp_presucalc($oc_codigoadm);
									
									if($reteiva > 0){
									
										$riva = new dataobject("riva");
										//$riva->load_where('ocompra',$p_compra);
										
										$prov=$this->datasis->damerow("SELECT nombre,rif FROM sprv WHERE proveed = '".$ocompra->get('cod_prov')."'");
	
										$riva->set('ocompra'       , $p_compra                        );
										$riva->set('odirect'       , $od_numero                       );
										$riva->set('emision'       , date('Ymd')                      );
										$riva->set('periodo'       , date('Ym')                       );
										$riva->set('tipo_doc'      , ''                               );
										$riva->set('fecha'         , date('Ymd')                      );
										$riva->set('numero'        , $ocompra->get('factura'   )      );
										$riva->set('ffactura'      , $ocompra->get('fechafac'  )      );
										$riva->set('nfiscal'       , $ocompra->get('controlfac')      );
										$riva->set('clipro'        , $ocompra->get('cod_prov'  )      );
										$riva->set('nombre'        , $prov['nombre']                  );
										$riva->set('rif'           , $prov['rif']                     );
										$riva->set('exento'        , $ocompra->get('exento'          ));
										$riva->set('tasa'          , $ocompra->get('tivag'           ));
										$riva->set('general'       , $ocompra->get('mivag'           ));
										$riva->set('geneimpu'      , $ocompra->get('ivag'            ));
										$riva->set('tasaadic'      , $ocompra->get('tivaa'           ));
										$riva->set('adicional'     , $ocompra->get('mivag'           ));
										$riva->set('adicimpu'      , $ocompra->get('ivaa'            ));
										$riva->set('tasaredu'      , $ocompra->get('tivar'           ));
										$riva->set('reducida'      , $ocompra->get('mivar'           ));
										$riva->set('reduimpu'      , $ocompra->get('ivar'            ));
										$riva->set('stotal'        , $ocompra->get('subtotal'        ));
										$riva->set('impuesto'      , $ocompra->get('ivag')+$ocompra->get('ivar')+$ocompra->get('ivaa')                          );
										$riva->set('gtotal'        , $ocompra->get('ivag')+$ocompra->get('ivar')+$ocompra->get('ivaa')+$ocompra->get('subtotal'));
										$riva->set('reiva'         , $ocompra->get('reteiva'         ));
										$riva->set('status'        , 'B'                              );
										$riva->set('banc'          , $banc->get('banco')              );
										$riva->set('numcuent'      , $banc->get('numcuent')           );
										$riva->set('codbanc'       , $banc->get('codbanc')            );
										$riva->set('odirect'       , $od_numero                       );
										$riva->set('mbanc'         , $id                              );
										$riva->set('reteiva_prov'  , $ocompra->get('reteiva_prov')    );
										$riva->save();
									}
								}
								$ocompra->set('status','E');
								$ocompra->save();
								//$this->db->simple_query("UPDATE ocompra SET status = 'E' WHERE numero = $ ");
								//$ocompra->save();
							}
							//exit('aaaa');

							$odirect->set('status','F3');
							$odirect->save();
						}
						$odirect->save();

					}elseif($status == "B2"){
				
					echo "B2".$orden."---";
						$ivaa         =  $od_ivaa;
						$ivag         =  $od_ivag;
						$ivar         =  $od_ivar;
						$subtotal     =  $od_subtotal;
						$reteiva      =  $od_reteiva;
						
						$tislr=$reten =  $od_reten;
						$ivan         =  $ivag+$ivar+$ivaa;
						$total        =  ($subtotal - $reten)+($ivan-($reteiva));
						
						$pr = $reten*100/$subtotal;
						$pk=array('codigoadm'=>$od_estadmin,'tipo'=>$od_fondo);
						
						$partidaiva=$this->datasis->traevalor("PARTIDAIVA");
						$pk['codigopres'] = $partidaiva;
						$presup->load($pk);
						
						$pasignacion   =$presup->get("asignacion");
						
						$reteiva_prov = $odirect->get('reteiva_prov');

						for($g=0;$g   <  $odirect->count_rel('itodirect');$g++){
							$islrid = '';
							$codigopres  = $odirect->get_rel('itodirect','partida',$g);
							$importe     = $odirect->get_rel('itodirect','importe',$g);
							$piva        = $odirect->get_rel('itodirect','iva'    ,$g);
							$islrid      = $odirect->get_rel('itodirect','islrid' ,$g);
							$ordinal     = $odirect->get_rel('itodirect','ordinal',$g);
							
							$i_islr      = $importe*$od_pr/100;
							
							if($pasignacion>0)
								$mont        = $importe;
							else
								$mont        =$importe+(($importe*$iva/100)-(($importe*$iva/100)*$reteiva_prov)/100);;

							$pk['codigopres'] = $codigopres;

							$presup->load($pk);
							$pagado=$presup->get("pagado");
							$pagado=$pagado+($mont-$i_islr);

							$presup->set("pagado",$pagado);
							
							$presup->save();
							
							if(!empty($ordinal)){
						
								$ord->load(array("codigoadm"=>$od_estadmin,"fondo"=>$od_fondo,"codigopres"=>$codigopres,"ordinal"=>$ordinal));

								print_r($ordinal);
								
								$opa   =$ord->get("pagado" );
								$opa  +=$mont-$i_islr;
								$ord->set("pagado"  ,$opa  );
								$ord->save();			
							}	
							//exit('cafe');
							//if($status == "B2"){
							//
							//	$islr   = new DataObject("islr");
							//	if(!empty($islrid))
							//		$islr->load($islrid);
							//				
							//	$islr->set('estadmin'      , $od_estadmin        );
							//	$islr->set('fondo'         , $od_fondo           );
							//	$islr->set('partida'       , $codigopres         );
							//	$islr->set('codprov'       , $od_cod_prov        );
							//	$islr->set('fechafac'      , $od_fechafac        );
							//	$islr->set('benefi'        , $m_benefi           );
							//	$islr->set('porcen'        , $pr                 );
							//	//$islr->set('sustraendo'    ,                     );
							//	$islr->set('islr'          , $i_islr             );
							//	$islr->set('fecha'         , date('Ymd')         );
							//	$islr->set('creten'        , $od_creten          );
							//	$islr->set('odirect'       , $islrid             );
							//	$islr->set('status'        , 'B'                 );
							//	$islr->save();
							//	$islrid = $islr->get('id');	
	            //
							//	$odirect->set_rel('itodirect','islrid',$islrid ,$g);
						  //
							//}
						}
						
						if($status=='I2')
							$odirect->set('status','I3');
					
						if($status == "B2"){
							$prov=$this->datasis->damerow("SELECT nombre,rif FROM sprv WHERE proveed = '".$odirect->get('cod_prov')."'");
							
							if($odirect->get('reteiva') >0){
							
								if($odirect->get('multiple')=='N'){
								
									$reteiva = $odirect->get('reteiva');
									if($reteiva>0){
										//$riva->load_where('odirect',$od_numero);
										
										$riva->set('odirect'       , $od_numero                        );
										$riva->set('emision'       , date('Ymd')                      );
										$riva->set('periodo'       , date('Ym')                       );
										$riva->set('tipo_doc'      , ''                               );
										$riva->set('fecha'         , date('Ymd')                      );
										$riva->set('numero'        , $odirect->get('factura'   )      );
										$riva->set('ffactura'      , $odirect->get('fechafac'  )      );
										$riva->set('nfiscal'       , $odirect->get('controlfac')      );
										$riva->set('clipro'        , $odirect->get('cod_prov'  )      );
										$riva->set('nombre'        , $prov['nombre']);
										$riva->set('rif'           , $prov['rif']);
										$riva->set('exento'        , $odirect->get('exento'          ));
										$riva->set('tasa'          , $odirect->get('tivag'           ));
										$riva->set('general'       , $odirect->get('mivag'           ));
										$riva->set('geneimpu'      , $odirect->get('ivag'            ));
										$riva->set('tasaadic'      , $odirect->get('tivaa'           ));
										$riva->set('adicional'     , $odirect->get('mivag'           ));
										$riva->set('adicimpu'      , $odirect->get('ivaa'            ));
										$riva->set('tasaredu'      , $odirect->get('tivar'           ));
										$riva->set('reducida'      , $odirect->get('mivar'           ));
										$riva->set('reduimpu'      , $odirect->get('ivar'            ));
										$riva->set('stotal'        , $odirect->get('subtotal'        ));
										$riva->set('impuesto'      , $odirect->get('ivag')+$odirect->get('ivar')+$odirect->get('ivaa')                          );
										$riva->set('gtotal'        , $odirect->get('ivag')+$odirect->get('ivar')+$odirect->get('ivaa')+$odirect->get('subtotal'));
										$riva->set('reiva'         , $odirect->get('reteiva'         ));
										$riva->set('status'        , 'B'                              );
										$riva->set('mbanc'         , $id                              );
										$riva->set('reteiva_prov'  , $odirect->get('reteiva_prov')    );
										$riva->set('banc'          , $banc->get('banco')              );
										$riva->set('numcuent'      , $banc->get('numcuent')           );
										$riva->set('codbanc'       , $banc->get('codbanc')            );
										
										$riva->save();
									}
								
								}elseif($odirect->get('multiple')=='S'){
								
							
									for($l=0;$l < $odirect->count_rel('itfac');$l++){
									//echo "----";
										echo $iditfac = $odirect->get_rel('itfac','id',$l);
									//echo "----";
									
										$reteiva = $odirect->get_rel('itfac','reteiva');
										if($reteiva>0){

											$riva = new DataObject('riva');
											//$riva->load_where('itfac',$iditfac);
											
											//print_r($riva->get_all());
			
											$riva->set('odirect'       , $od_numero                       );
											$riva->set('itfac'         , $iditfac                         );
											$riva->set('emision'       , date('Ymd')                      );
											$riva->set('periodo'       , date('Ym')                       );
											$riva->set('tipo_doc'      , ''                               );
											$riva->set('fecha'         , date('Ymd')                      );
											$riva->set('numero'        , $odirect->get_rel('itfac','factura',$l));
											$riva->set('ffactura'      , $odirect->get_rel('itfac','fechafac',$l));
											$riva->set('nfiscal'       , $odirect->get_rel('itfac','controlfac',$l));
											$riva->set('clipro'        , $odirect->get('cod_prov'  )      );
											$riva->set('nombre'        , $prov['nombre'] );
											$riva->set('rif'           , $prov['rif']    );
											$riva->set('exento'        , $odirect->get_rel('itfac','exento',$l ));
											$riva->set('tasa'          , $odirect->get('tivag'           ));
											$riva->set('general'       , $odirect->get_rel('itfac','ivag',$l)*100/$odirect->get('tivag'));
											$riva->set('geneimpu'      , $odirect->get_rel('itfac','ivag',$l));
											$riva->set('tasaadic'      , $odirect->get('tivaa'           ));
											$riva->set('adicional'     , $odirect->get_rel('itfac','ivaa',$l)*100/$odirect->get('tivaa'));
											$riva->set('adicimpu'      , $odirect->get_rel('itfac','ivaa',$l));
											$riva->set('tasaredu'      , $odirect->get('tivar'           ));
											$riva->set('reducida'      , $odirect->get_rel('itfac','ivar',$l)*100/$odirect->get('tivar'));
											$riva->set('reduimpu'      , $odirect->get_rel('itfac','ivar',$l));
											$riva->set('stotal'        , $odirect->get_rel('itfac','subtotal',$l));
											$riva->set('impuesto'      , $odirect->get_rel('itfac','ivag',$l)+$odirect->get_rel('itfac','ivar',$l)+$odirect->get_rel('itfac','ivaa',$l)                          );
											$riva->set('gtotal'        , $odirect->get_rel('itfac','ivag',$l)+$odirect->get_rel('itfac','ivar',$l)+$odirect->get_rel('itfac','ivaa',$l)+$odirect->get_rel('itfac','subtotal',$l));
											$riva->set('reiva'         , $odirect->get_rel('itfac','reteiva',$l));
											$riva->set('status'        , 'B'                              );
											$riva->set('mbanc'         , $id                              );
											$riva->set('reteiva_prov'  , $odirect->get('reteiva_prov')    );
											$riva->set('banc'          , $banc->get('banco')              );
											$riva->set('numcuent'      , $banc->get('numcuent')           );
											$riva->set('codbanc'       , $banc->get('codbanc')            );
											
											$riva->save();
										}	
									
									}
									//echo $odirect->get('multiple');
									//echo $odirect->get('reteiva');
									//exit('-----');
								}
							}
							
						}
							
							$ii = ($od_ivaa+$od_ivag+$od_ivar)-($od_reteiva);
							
							if($pasignacion>0){
								$pk['codigopres'] = $partidaiva;
								$presup->load($pk);
		
								$pagado =$presup->get("pagado");
								$pagado+=$ii;
								$presup->set("pagado",$pagado);
								$presup->save();
							}
							
							$odirect->set('status','B3');
							
							$this->sp_presucalc($od_estadmin);
						
						$odirect->save();
						
					}elseif($status == "N2"){
										
						echo "N2";
						$ivaa         =  $od_ivaa;
						$ivag         =  $od_ivag;
						$ivar         =  $od_ivar;
						$subtotal     =  $od_subtotal;
						$reteiva      =  $od_reteiva;
						$tislr=$reten =  $od_reten;
						$ivan         =  $ivag+$ivar+$ivaa;
						$total        =  ($subtotal - $reten)+($ivan-($reteiva));
						
						$pr = $reten*100/$subtotal;
						$pk=array('codigoadm'=>$od_estadmin,'tipo'=>$od_fondo);
						
						$partidaiva=$this->datasis->traevalor("PARTIDAIVA");
						$pk['codigopres'] = $partidaiva;
						$presupante->load($pk);
						
						$pasignacion   =$presupante->get("asignacion");
						
						$reteiva_prov = $odirect->get('reteiva_prov');

						for($g=0;$g   <  $odirect->count_rel('itodirect');$g++){
							$islrid = '';
							$codigopres  = $odirect->get_rel('itodirect','partida',$g);
							$importe     = $odirect->get_rel('itodirect','importe',$g);
							$piva        = $odirect->get_rel('itodirect','iva'    ,$g);
							$islrid      = $odirect->get_rel('itodirect','islrid' ,$g);
							$ordinal     = $odirect->get_rel('itodirect','ordinal',$g);
							
							$i_islr      = $importe*$od_pr/100;
							
							if($pasignacion>0)
								$mont        = $importe;
							else
								$mont        = $importe+(($importe*$iva/100)-(($importe*$iva/100)*$reteiva_prov)/100);

							$pk['codigopres'] = $codigopres;

							$presupante->load($pk);
							$pagado=$presupante->get("pagado");
							$pagado=$pagado+($mont-$i_islr);

							$presupante->set("pagado",$pagado);
							
							$presupante->save();
							
							if(!empty($ordinal)){
						
								$ordinalante->load(array("codigoadm"=>$od_estadmin,"fondo"=>$od_fondo,"codigopres"=>$codigopres,"ordinal"=>$ordinal));

								print_r($ordinal);
								
								$opa   =$ordinalante->get("pagado" );
								$opa  +=$mont-$i_islr;
								$ordinalante->set("pagado"  ,$opa  );
								$ordinalante->save();			
							}
							
							$reteiva = $odirect->get('reteiva');
							if($reteiva>0){
								//$riva->load_where('odirect',$od_numero);
								
								$riva->set('odirect'       , $od_numero                        );
								$riva->set('emision'       , date('Ymd')                      );
								$riva->set('periodo'       , date('Ym')                       );
								$riva->set('tipo_doc'      , ''                               );
								$riva->set('fecha'         , date('Ymd')                      );
								$riva->set('numero'        , $odirect->get('factura'   )      );
								$riva->set('ffactura'      , $odirect->get('fechafac'  )      );
								$riva->set('nfiscal'       , $odirect->get('controlfac')      );
								$riva->set('clipro'        , $odirect->get('cod_prov'  )      );
								$riva->set('nombre'        , $prov['nombre']);
								$riva->set('rif'           , $prov['rif']);
								$riva->set('exento'        , $odirect->get('exento'          ));
								$riva->set('tasa'          , $odirect->get('tivag'           ));
								$riva->set('general'       , $odirect->get('mivag'           ));
								$riva->set('geneimpu'      , $odirect->get('ivag'            ));
								$riva->set('tasaadic'      , $odirect->get('tivaa'           ));
								$riva->set('adicional'     , $odirect->get('mivag'           ));
								$riva->set('adicimpu'      , $odirect->get('ivaa'            ));
								$riva->set('tasaredu'      , $odirect->get('tivar'           ));
								$riva->set('reducida'      , $odirect->get('mivar'           ));
								$riva->set('reduimpu'      , $odirect->get('ivar'            ));
								$riva->set('stotal'        , $odirect->get('subtotal'        ));
								$riva->set('impuesto'      , $odirect->get('ivag')+$odirect->get('ivar')+$odirect->get('ivaa')                          );
								$riva->set('gtotal'        , $odirect->get('ivag')+$odirect->get('ivar')+$odirect->get('ivaa')+$odirect->get('subtotal'));
								$riva->set('reiva'         , $odirect->get('reteiva'         ));
								$riva->set('status'        , 'B'                              );
								$riva->set('mbanc'         , $id                              );
								$riva->set('reteiva_prov'  , $odirect->get('reteiva_prov')    );
								$riva->set('banc'          , $banc->get('banco')              );
								$riva->set('numcuent'      , $banc->get('numcuent')           );
								$riva->set('codbanc'       , $banc->get('codbanc')            );
								
								$riva->save();
							}

							
							//exit('cafe');
							//if($status == "B2"){
							//
							//	$islr   = new DataObject("islr");
							//	if(!empty($islrid))
							//		$islr->load($islrid);
							//				
							//	$islr->set('estadmin'      , $od_estadmin        );
							//	$islr->set('fondo'         , $od_fondo           );
							//	$islr->set('partida'       , $codigopres         );
							//	$islr->set('codprov'       , $od_cod_prov        );
							//	$islr->set('fechafac'      , $od_fechafac        );
							//	$islr->set('benefi'        , $m_benefi           );
							//	$islr->set('porcen'        , $pr                 );
							//	//$islr->set('sustraendo'    ,                     );
							//	$islr->set('islr'          , $i_islr             );
							//	$islr->set('fecha'         , date('Ymd')         );
							//	$islr->set('creten'        , $od_creten          );
							//	$islr->set('odirect'       , $islrid             );
							//	$islr->set('status'        , 'B'                 );
							//	$islr->save();
							//	$islrid = $islr->get('id');	
	            //
							//	$odirect->set_rel('itodirect','islrid',$islrid ,$g);
						  //
							//}
						}					
						if($status == "B2"){
							$prov=$this->datasis->damerow("SELECT nombre,rif FROM sprv WHERE proveed = '".$odirect->get('cod_prov')."'");
							
							if($odirect->get('reteiva') >0){
							
								if($odirect->get('multiple')=='N'){
								
									$reteiva = $odirect->get('reteiva');
									if($reteiva>0){
								
										//$riva->load_where('odirect',$od_numero);
		
										$riva->set('odirect'       , $od_numero                        );
										$riva->set('emision'       , date('Ymd')                      );
										$riva->set('periodo'       , date('Ym')                       );
										$riva->set('tipo_doc'      , ''                               );
										$riva->set('fecha'         , date('Ymd')                      );
										$riva->set('numero'        , $odirect->get('factura'   )      );
										$riva->set('ffactura'      , $odirect->get('fechafac'  )      );
										$riva->set('nfiscal'       , $odirect->get('controlfac')      );
										$riva->set('clipro'        , $odirect->get('cod_prov'  )      );
										$riva->set('nombre'        , $prov['nombre']);
										$riva->set('rif'           , $prov['rif']);
										$riva->set('exento'        , $odirect->get('exento'          ));
										$riva->set('tasa'          , $odirect->get('tivag'           ));
										$riva->set('general'       , $odirect->get('mivag'           ));
										$riva->set('geneimpu'      , $odirect->get('ivag'            ));
										$riva->set('tasaadic'      , $odirect->get('tivaa'           ));
										$riva->set('adicional'     , $odirect->get('mivag'           ));
										$riva->set('adicimpu'      , $odirect->get('ivaa'            ));
										$riva->set('tasaredu'      , $odirect->get('tivar'           ));
										$riva->set('reducida'      , $odirect->get('mivar'           ));
										$riva->set('reduimpu'      , $odirect->get('ivar'            ));
										$riva->set('stotal'        , $odirect->get('subtotal'        ));
										$riva->set('impuesto'      , $odirect->get('ivag')+$odirect->get('ivar')+$odirect->get('ivaa')                          );
										$riva->set('gtotal'        , $odirect->get('ivag')+$odirect->get('ivar')+$odirect->get('ivaa')+$odirect->get('subtotal'));
										$riva->set('reiva'         , $odirect->get('reteiva'         ));
										$riva->set('status'        , 'B'                              );
										$riva->set('mbanc'         , $id                              );
										$riva->set('reteiva_prov'  , $odirect->get('reteiva_prov')    );
										$riva->set('banc'          , $banc->get('banco')              );
										$riva->set('numcuent'      , $banc->get('numcuent')           );
										$riva->set('codbanc'       , $banc->get('codbanc')            );
										
										$riva->save();
									}
								
								}elseif($odirect->get('multiple')=='S'){
								
							
									for($l=0;$l < $odirect->count_rel('itfac');$l++){
									//echo "----";
										echo $iditfac = $odirect->get_rel('itfac','id',$l);
									//echo "----";
									
										$reteiva = $odirect->get_rel('itfac','reteiva');
										if($reteiva>0){

											$riva = new DataObject('riva');
											//$riva->load_where('itfac',$iditfac);
											
											//print_r($riva->get_all());
			
											$riva->set('odirect'       , $od_numero                       );
											$riva->set('itfac'         , $iditfac                         );
											$riva->set('emision'       , date('Ymd')                      );
											$riva->set('periodo'       , date('Ym')                       );
											$riva->set('tipo_doc'      , ''                               );
											$riva->set('fecha'         , date('Ymd')                      );
											$riva->set('numero'        , $odirect->get_rel('itfac','factura',$l));
											$riva->set('ffactura'      , $odirect->get_rel('itfac','fechafac',$l));
											$riva->set('nfiscal'       , $odirect->get_rel('itfac','controlfac',$l));
											$riva->set('clipro'        , $odirect->get('cod_prov'  )      );
											$riva->set('nombre'        , $prov['nombre'] );
											$riva->set('rif'           , $prov['rif']    );
											$riva->set('exento'        , $odirect->get_rel('itfac','exento',$l ));
											$riva->set('tasa'          , $odirect->get('tivag'           ));
											$riva->set('general'       , $odirect->get_rel('itfac','ivag',$l)*100/$odirect->get('tivag'));
											$riva->set('geneimpu'      , $odirect->get_rel('itfac','ivag',$l));
											$riva->set('tasaadic'      , $odirect->get('tivaa'           ));
											$riva->set('adicional'     , $odirect->get_rel('itfac','ivaa',$l)*100/$odirect->get('tivaa'));
											$riva->set('adicimpu'      , $odirect->get_rel('itfac','ivaa',$l));
											$riva->set('tasaredu'      , $odirect->get('tivar'           ));
											$riva->set('reducida'      , $odirect->get_rel('itfac','ivar',$l)*100/$odirect->get('tivar'));
											$riva->set('reduimpu'      , $odirect->get_rel('itfac','ivar',$l));
											$riva->set('stotal'        , $odirect->get_rel('itfac','subtotal',$l));
											$riva->set('impuesto'      , $odirect->get_rel('itfac','ivag',$l)+$odirect->get_rel('itfac','ivar',$l)+$odirect->get_rel('itfac','ivaa',$l)                          );
											$riva->set('gtotal'        , $odirect->get_rel('itfac','ivag',$l)+$odirect->get_rel('itfac','ivar',$l)+$odirect->get_rel('itfac','ivaa',$l)+$odirect->get_rel('itfac','subtotal',$l));
											$riva->set('reiva'         , $odirect->get_rel('itfac','reteiva'         ));
											$riva->set('status'        , 'B'                              );
											$riva->set('mbanc'         , $id                              );
											$riva->set('reteiva_prov'  , $odirect->get('reteiva_prov')    );
											$riva->set('banc'          , $banc->get('banco')              );
											$riva->set('numcuent'      , $banc->get('numcuent')           );
											$riva->set('codbanc'       , $banc->get('codbanc')            );
										
											$riva->save();	
										}
									
									}
									//echo $odirect->get('multiple');
									//echo $odirect->get('reteiva');
									//exit('-----');
								}
							}
							
						}
							
							$ii = ($od_ivaa+$od_ivag+$od_ivar)-($od_reteiva);
							
							if($pasignacion>0){
								$pk['codigopres'] = $partidaiva;
								$presupante->load($pk);
		
								$pagado =$presupante->get("pagado");
								$pagado+=$ii;
								$presupante->set("pagado",$pagado);
								$presupante->save();
							}
							
							$odirect->set('status','N3');
							
							$this->sp_presucalc($od_estadmin);
						
						$odirect->save();
						
					}elseif($status == "I2"){
					
						if($status=='I2')
							$odirect->set('status','I3');
						$odirect->save();
					
					}elseif($status == "M2"){
					
						if($status=='M2')
							$odirect->set('status','M3');
						$odirect->save();
					
					}elseif($status=='S2'){
					echo "S2";	
						$pk=array('codigoadm'=>$od_estadmin,'tipo'=>$od_fondo);

						for($g=0;$g   <  $odirect->count_rel('islr');$g++){
							$islrid = '';
							$codigopres  = $odirect->get_rel('islr','partida' ,$g);
							$i_islr      = $odirect->get_rel('islr','islr'    ,$g);
							
							$pk['codigopres'] = $codigopres;

							$presup->load($pk);
							$pagado=$presup->get("pagado");
							$pagado+=$i_islr;

							$presup->set("pagado",$pagado);
							$presup->save();
							
							$this->sp_presucalc($od_estadmin);
							
							$codigopres  = $odirect->set_rel('islr','status','C' ,$g);
						}
						$odirect->set('status','S3');
						$odirect->save();
						
					}
					elseif($status=='R2'){
					echo "R2";	
						$odirect->set('status','R3');
						$odirect->save();
					}elseif($status=='G2'){
					echo "G2";	
						$odirect->set('status','G3');
						$odirect->save();
					}elseif($status=='H2'){
						$odirect->set('status','H3');
						$odirect->save();
					}elseif($status=='O2'){

						$obr = $odirect->get('obr');
						$iva = $odirect->get('iva');
						$total2  = $odirect->get('total2'  );
						$amortiza= $odirect->get('amortiza');
						
						$obra = new DataObject("obra");		
						$obra->load($obr);
						
						$codigoadm  = $obra->get('codigoadm' );
						$fondo      = $obra->get('fondo'     );
						$codigopres = $obra->get('codigopres');
						$ordinal    = $obra->get('ordinal'   );
												
						$mont       = $total2-$amortiza;
						
						
						if(empty($error)){
						
							
							if(!empty($ordinal)){
								$pk = array("codigoadm"=>$codigoadm,"fondo"=>$fondo,"codigopres"=>$codigopres,"ordinal"=>$ordinal);
								$ord->load($pk);
								
								$pag    =$ord->get("pagado"   );							
								$pag   += $mont;							
								$ord->set("pagado"   ,$pag    );
								$ord->save();	
							}else{
								$pk = array("codigoadm"=>$codigoadm,"tipo"=>$fondo,"codigopres"=>$codigopres);
							
								$presup->load($pk);
								$pag    =$presup->get("pagado"   );							
								$pag   += $mont;							
								$presup->set("pagado"   ,$pag    );
								
								$presup->save();
							}
							
							if($odirect->get('reteiva')>0){
								$prov=$this->datasis->damerow("SELECT nombre,rif FROM sprv WHERE proveed = '".$odirect->get('cod_prov')."'");
								
								//$riva->load_where('odirect',$od_numero);
		
								$riva->set('odirect'       , $od_numero                        );
								$riva->set('emision'       , date('Ymd')                      );
								$riva->set('periodo'       , date('Ym')                       );
								$riva->set('tipo_doc'      , 'FC'                               );
								$riva->set('fecha'         , date('Ymd')                      );
								$riva->set('numero'        , $odirect->get('factura'   )      );
								$riva->set('ffactura'      , $odirect->get('fechafac'  )      );
								$riva->set('nfiscal'       , $odirect->get('controlfac')      );
								$riva->set('clipro'        , $odirect->get('cod_prov'  )      );
								$riva->set('nombre'        , $prov['nombre']);
								$riva->set('rif'           , $prov['rif']);
								$riva->set('exento'        , $odirect->get('exento'          ));
								$riva->set('tasa'          , $odirect->get('tivag'           ));
								$riva->set('general'       , $odirect->get('mivag'           ));
								$riva->set('geneimpu'      , $odirect->get('ivag'            ));
								$riva->set('tasaadic'      , $odirect->get('tivaa'           ));
								$riva->set('adicional'     , $odirect->get('mivag'           ));
								$riva->set('adicimpu'      , $odirect->get('ivaa'            ));
								$riva->set('tasaredu'      , $odirect->get('tivar'           ));
								$riva->set('reducida'      , $odirect->get('mivar'           ));
								$riva->set('reduimpu'      , $odirect->get('ivar'            ));
								$riva->set('stotal'        , $odirect->get('subtotal'        ));
								$riva->set('impuesto'      , $odirect->get('ivag')+$odirect->get('ivar')+$odirect->get('ivaa')                          );
								$riva->set('gtotal'        , $odirect->get('ivag')+$odirect->get('ivar')+$odirect->get('ivaa')+$odirect->get('subtotal')   );
								$riva->set('reiva'         , $odirect->get('reteiva'         ));
								$riva->set('status'        , 'B'                              );
								$riva->set('mbanc'         , $id                              );
								$riva->set('reteiva_prov'  , $odirect->get('reteiva_prov')    );
								$riva->set('banc'          , $banc->get('banco')              );
								$riva->set('numcuent'      , $banc->get('numcuent')           );
								$riva->set('codbanc'       , $banc->get('codbanc')            );
								
								$riva->save();
							}
						}
						
						$odirect->set('status','O3');
						$odirect->save();
					}else{
						$error.="<div class='alert'><p>N se puede realizar la operacion para la orden de pago ($od_numero)</p></div>";						
					}
				}
				
				$islrid = $mbanc->get('islrid');
				
				$islr = new DataObject("islr");
				if (!(empty($islrid)))				
					$islr->load($islrid);
				
				if(date('d')>15)$q='02'; else $q='01';
				$islr->set('emision'       , date('Ymd')         );
				$islr->set('periodo'       , $q.date('m')        );
				$islr->set('clipro'        , $od_cod_prov        );
				$islr->set('total'         , $tislr              );
				$islr->set('status'        , 'B'                 );
				$islr->save();
				$islrid = $islr->get('nrocomp');

				$mbanc->set('islrid',$islrid);
				
			}else
				$error.="<div class='alert'><p>Error aun no determinado</p></div>";
		}else{
			$error.="<div class='alert'><p>No se Puede Completar la operacion s</p></div>";
		}
		
		if(empty($error)){
			$mbanc->set('status','E2');
			$mbanc->save();

			$saldo-=$m_monto;
			$banc->set('saldo',$saldo);
			$banc->save();
			logusu('ppro',"Actualizo movimiento Nro $id");
			redirect($this->url."dataedit/show/$id");
		}else{
			logusu('ppro',"Actualizo movimiento Nro $id");
			$data['content'] = $error.anchor($this->url."/dataedit/show/$id",'Regresar');
			$data['title']   = " ".$this->tits." ";
			$data["head"]    = $this->rapyd->get_head().script('jquery.js').script("plugins/jquery.numeric.pack.js");
			$this->load->view('view_ventanas', $data);
		}
	}

	function reversar($id){
		$this->rapyd->load('dataobject');
		
		$error='';
		
		$ord       =  new DataObject("ordinal"  );

		$mbanc     =  new DataObject("mbanc");
		$mbanc     -> rel_one_to_many('itppro', 'itppro', array('id'=>'mbanc'));
		$mbanc     -> load($id);
		$m_codbanc =  $mbanc->get('codbanc');
		$m_monto   =  $mbanc->get('monto');

		$banc   = new DataObject("banc");
		$banc   ->load($m_codbanc);
		$saldo  = $banc->get('saldo');
		$activo = $banc->get('activo');

		$odirect = new DataObject("odirect");
		$odirect -> rel_one_to_many('pacom', 'pacom', array('numero'=>'pago'));
		$odirect -> rel_one_to_many('itodirect', 'itodirect', array('numero'=>'numero'));
		$odirect -> rel_one_to_many('islr', 'islr', array('numero'=>'odirect'));

		$ocompra = new DataObject("ocompra");
		$ocompra->rel_one_to_many('itocompra', 'itocompra', array('numero'=>'numero'));

		$riva    =  new DataObject("riva");

		$presup      = new DataObject("presupuesto");
		$presupante  = new DataObject("presupuestoante" );
		$ordinalante = new DataObject("ordinalante");
		
		$partidaiva=$this->datasis->traevalor("PARTIDAIVA");

		if($activo != 'S' )$error.="<div class='alert'><p>El banco ($banco) esta inactivo</p></div>";

		$sta=$mbanc->get('status');
		if(($sta=="E2")){
			$t=0;
			if(empty($error)){

				for($j=0;$j < $mbanc->count_rel('itppro');$j++){

					$orden    = $mbanc->get_rel('itppro','orden',$j);
					$it_monto = $mbanc->get_rel('itppro','monto',$j);

					$odirect->load($orden);
					$status       =  $odirect->get('status'     );
					$od_numero    =  $odirect->get('numero'     );
					$od_estadmin  =  $odirect->get('estadmin'   );
					$od_fondo     =  $odirect->get('fondo'      );
					$od_ivag      =  $odirect->get('ivag'       );
					$od_ivaa      =  $odirect->get('ivaa'       );
					$od_ivar      =  $odirect->get('ivar'       );
					$od_reten     =  $odirect->get('reten'      );
					$od_reteiva   =  $odirect->get('reteiva'    );
					$od_subtotal  =  $odirect->get('subtotal'   );
					$od_pr        =  $od_reten*100/$od_subtotal;

					if($status == "F3" ){

						for($g=0;$g < $odirect->count_rel('pacom');$g++){
							$p_t       = $odirect->get_rel('pacom','total' ,$g);
							$p_compra  = $odirect->get_rel('pacom','compra',$g);

							$ocompra->load($p_compra);

							$oc_codigoadm  = $ocompra->get('estadmin'  );
							$oc_fondo      = $ocompra->get('fondo'     );
							$oc_status     = $ocompra->get('status'    );

							//$pagado=$this->datasis->dameval("SELECT SUM(a.monto) FROM itppro a JOIN mbanc d ON d.id=a.mbanc JOIN odirect b ON a.orden=b.numero JOIN pacom c ON b.numero=c.pago WHERE c.compra=$p_compra AND d.status='E2'");
							//$pagado+=$it_monto;
	            //
							$ivaa      =  $ocompra->get('ivaa');
							$ivag      =  $ocompra->get('ivag');
							$ivar      =  $ocompra->get('ivar');
							//$subtotal  =  $ocompra->get('subtotal');
							$reteiva   =  $ocompra->get('reteiva');
							$reteiva_prov =  $ocompra->get('reteiva_prov');
							$reten     =  $ocompra->get('reten');
							$ivan      =  $ivag+$ivar+$ivaa;
							//$total     =  $subtotal+($ivan-($reteiva+$reten));
	
							//if($total==$pagado){

								$pk=array('codigoadm'=>$oc_codigoadm,'tipo'=>$oc_fondo);
								
								$partidaiva=$this->datasis->traevalor("PARTIDAIVA");
								$pk['codigopres'] = $partidaiva;
								$presup->load($pk);
					
								$pasignacion   =$presup->get("asignacion");
						
								$odirect->count_rel('pacom');
								for($h=0;$h < $odirect->count_rel('pacom');$h++){
								  $p_compra  = $odirect->get_rel('pacom','compra',$h);
								  
									for($k=0;$k < $ocompra->count_rel('itocompra');$k++){
										$codigopres  = $ocompra->get_rel('itocompra','partida',$k);
										$importe     = $ocompra->get_rel('itocompra','importe',$k);
										$islrid      = $ocompra->get_rel('itocompra','islrid' ,$k);
										$ordinal     = $ocompra->get_rel('itocompra','ordinal',$k);		
										$iva         = $ocompra->get_rel('itocompra','iva'    ,$k);
										
										if($pasignacion>0)
											$mont        = $importe;
										else
											$mont        = $importe+(($importe*$iva/100)-(($importe*$iva/100)*$reteiva_prov)/100);
																			
										$pk=array('codigoadm'=>$oc_codigoadm,'tipo'=>$oc_fondo,'codigopres'=>$codigopres);

										$presup->load($pk);
										$pagado=$presup->get("pagado");
										$pagado=$pagado-$mont;

										$presup->set("pagado",$pagado);
										$presup->save();
										
										if(!empty($ordinal)){
			
											$ord->load(array("codigoadm"=>$oc_codigoadm,"fondo"=>$oc_fondo,"codigopres"=>$codigopres,"ordinal"=>$ordinal));
											
											$opa =$ord->get("pagado" );    
											$opa -= $mont;
											$ord->set("pagado",$opa);
											$ord->save();
										}	
									}
									
									if($pasignacion>0){
										$pk['codigopres'] = $partidaiva;
										$presup->load($pk);
	
										$pagado =$presup->get("pagado");
										$pagado-=$ivan-($reteiva);
										$presup->set("pagado",$pagado);
										$presup->save();
									}
									$this->sp_presucalc($oc_codigoadm);
									
									if($reteiva >0){
										$riva->load_where('ocompra',$p_compra);
										$riva->set('status'  , 'AN' );
										$riva->save();
									}
								}
								$ocompra->set('status','O');
								$ocompra->save();

							$odirect->set('status','F2');
							$odirect->save();
						}
						
						if($this->anular)$this->op_anular($orden,false);
						//echo $this->anular.'*';
						//exit('helloword');

					}elseif($status == "B3" || $status == "I3" ){

						$pk=array('codigoadm'=>$od_estadmin,'tipo'=>$od_fondo);
						
						$partidaiva=$this->datasis->traevalor("PARTIDAIVA");
						$pk['codigopres'] = $partidaiva;
						$presup->load($pk);
						
						$pasignacion   =$presup->get("asignacion");
						
						$reteiva_prov  = $odirect->get('reteiva_prov');

						for($g=0;$g   <  $odirect->count_rel('itodirect');$g++){
							$codigopres  = $odirect->get_rel('itodirect','partida',$g);
							$importe     = $odirect->get_rel('itodirect','importe',$g);
							$piva        = $odirect->get_rel('itodirect','iva'    ,$g);
							$islrid      = $odirect->get_rel('itodirect','islrid' ,$g);
							$ordinal     = $odirect->get_rel('itodirect','ordinal',$g);
							$i_islr      = $importe*$od_pr/100;
										//exit('12');		
							
							if($pasignacion>0)
								$mont        = $importe;
							else
								$mont        = $importe+(($importe*$piva/100)-(($importe*$piva/100)*$reteiva_prov)/100);

							$pk['codigopres'] = $codigopres;

							$presup->load($pk);
							$pagado=$presup->get("pagado");
							$pagado=$pagado-($mont-$i_islr);

							$presup->set("pagado",$pagado);
							$presup->save();
							
							if(!empty($ordinal)){
						
								$ord->load(array("codigoadm"=>$od_estadmin,"fondo"=>$od_fondo,"codigopres"=>$codigopres,"ordinal"=>$ordinal));
								
								$opa   =$ord->get("pagado" );
								$opa  -=$mont-$i_islr;
								$ord->set("pagado"  ,$opa  );
								$ord->save();			
							}	
						}

						if($status == "B3"){
						
							if($odirect->get('reteiva') >0){
							
								if($odirect->get('multiple')=='N'){
									$reteiva=$odirect->get('reteiva');
									if($reteiva>0){
										$riva->load_where('odirect',$od_numero);
										$riva->set('status' , 'AN'  );
										$riva->save();
									}
							
								}elseif($odirect->get('multiple')=='S'){
							
									for($l=0;$l < $odirect->count_rel('itfac');$l++){
										$iditfac = $odirect->get_rel('itfac','id',$l);

										$reteiva = $odirect->get_rel('itfac','reteiva');
										if($reteiva>0){
											$riva = new DataObject('riva');
											$riva->load_where('itfac',$iditfac);
											$riva->set('status' , 'AN'  );
											$riva->save();
										}
									}
								}
							}		
							
							$ii = ($od_ivaa+$od_ivag+$od_ivar)-($od_reteiva);
							if($pasignacion>0){
								$pk['codigopres'] = $partidaiva;
								$presup->load($pk);
		
								$pagado =$presup->get("pagado");
								$pagado-=$ii;
								$presup->set("pagado",$pagado);
								$presup->save();
							}
							$odirect->set('status','B2');
							$odirect->save();
							$this->sp_presucalc($od_estadmin);
							
							
							//exit('holamundo');
						}
						$this->pd_anular($orden,false);

					}elseif($status=='N3'){
					
						$pk=array('codigoadm'=>$od_estadmin,'tipo'=>$od_fondo);
						
						$partidaiva=$this->datasis->traevalor("PARTIDAIVA");
						$pk['codigopres'] = $partidaiva;
						$presupante->load($pk);
						
						$pasignacion   =$presupante->get("asignacion");
						
						$reteiva_prov  = $odirect->get('reteiva_prov');

						for($g=0;$g   <  $odirect->count_rel('itodirect');$g++){
							$codigopres  = $odirect->get_rel('itodirect','partida',$g);
							$importe     = $odirect->get_rel('itodirect','importe',$g);
							$piva        = $odirect->get_rel('itodirect','iva'    ,$g);
							$islrid      = $odirect->get_rel('itodirect','islrid' ,$g);
							echo $ordinal     = $odirect->get_rel('itodirect','ordinal',$g);
							$i_islr      = $importe*$od_pr/100;
										//exit('12');		
							
							if($pasignacion>0)
								$mont        = $importe;
							else
								$mont        = $importe+(($importe*$iva/100)-(($importe*$iva/100)*$reteiva_prov)/100);

							$pk['codigopres'] = $codigopres;

							$presupante->load($pk);
							$pagado    = $presupante->get("pagado");
							$pagado    = $pagado-($mont-$i_islr);

							$presupante->set("pagado",$pagado);
							$presupante->save();
							
							if(!empty($ordinal)){

								$ordinalante->load(array("codigoadm"=>$od_estadmin,"fondo"=>$od_fondo,"codigopres"=>$codigopres,"ordinal"=>$ordinal));

								
								$opa   =$ordinalante->get("pagado" );
								$opa  -=$mont-$i_islr;
								$ordinalante->set("pagado"  ,$opa  );
								$ordinalante->save();			
							}	
						}
						
							if($odirect->get('reteiva') >0){
							
								if($odirect->get('multiple')=='N'){

									$riva->load_where('odirect',$od_numero);
									$riva->set('status' , 'AN'  );
									$riva->save();
							
								}elseif($odirect->get('multiple')=='S'){
							
									for($l=0;$l < $odirect->count_rel('itfac');$l++){
										$iditfac = $odirect->get_rel('itfac','id',$l);

										$riva = new DataObject('riva');
										$riva->load_where('itfac',$iditfac);
										$riva->set('status' , 'AN'  );
										$riva->save();
									}
								}
							}		
							
							$ii = ($od_ivaa+$od_ivag+$od_ivar)-($od_reteiva);
							if($pasignacion>0){
								$pk['codigopres'] = $partidaiva;
								$presupante->load($pk);
		
								$pagado =$presupante->get("pagado");
								$pagado-=$ii;
								$presupante->set("pagado",$pagado);
								$presupante->save();
							}
							$odirect->set('status','N2');
							$odirect->save();
							$this->sp_presucalc($od_estadmin);
	
					}elseif($status=='S3'){
						
						$pk=array('codigoadm'=>$od_estadmin,'tipo'=>$od_fondo);

						for($g=0;$g   <  $odirect->count_rel('islr');$g++){
							$islrid = '';
							$codigopres  = $odirect->get_rel('islr','partida' ,$g);
							$i_islr      = $odirect->get_rel('islr','islr'    ,$g);
							
							$pk['codigopres'] = $codigopres;

							$presup->load($pk);
							$pagado=$presup->get("pagado");
							$pagado-=$i_islr;

							$presup->set("pagado",$pagado);
							$presup->save();
							
							$codigopres  = $odirect->set_rel('islr','status','B' ,$g);
						}
						
						$odirect->set('status','S2');
						$odirect->save();
						$this->sp_presucalc($codigoadm);
						
					}elseif($status == "M3"){
					
						if($status=='M3')
							$odirect->set('status','M2');
						$odirect->save();
					
					}elseif($status=='R3'){
						$odirect->set('status','R2');
					}elseif($status=='H3'){
						$odirect->set('status','H2');
					}elseif($status=='G3'){
						$odirect->set('status','G2');
					}elseif($status=='O3'){
						$obr = $odirect->get('obr');
						$iva = $odirect->get('iva');
						$total2  = $odirect->get('total2'  );
						$amortiza= $odirect->get('amortiza');
						
						$obra = new DataObject("obra");		
						$obra->load($obr);
						
						$codigoadm  = $obra->get('codigoadm' );
						$fondo      = $obra->get('fondo'     );
						$codigopres = $obra->get('codigopres');
						$ordinal    = $obra->get('ordinal'   );
							
						$mont        = $total2-$amortiza;
							
						if(empty($error)){
							
							if(!empty($ordinal)){
								$pk = array("codigoadm"=>$codigoadm,"fondo"=>$fondo,"codigopres"=>$codigopres,"ordinal"=>$ordinal);
								$ord->load($pk);
								
								$pag    =$ord->get("pagado"   );							
								$pag   -= $mont;							
								$ord->set("pagado"   ,$pag    );
								$ord->save();	
							}else{
								$pk = array("codigoadm"=>$codigoadm,"tipo"=>$fondo,"codigopres"=>$codigopres);
							
								$presup->load($pk);
								$pag    =$presup->get("pagado"   );							
								$pag   -= $mont;							
								$presup->set("pagado"   ,$pag    );
								
								$presup->save();
							}
							
							if($odirect->get('reteiva')>0){								
								$riva->load_where('odirect',$od_numero);		
								$riva->set('status'  , 'AN'  );								
								$riva->save();
							}							
						}
						
						$odirect->set('status','O2');
						$odirect->save();
						
						$this->po_anular($orden,false);
					}else{
						$error.="<div class='alert'><p>No se puede realizar la operacion para la orden de pago ($od_numero)</p></div>";
					}
				}
				$odirect->save();
			}else
				$error.="<div class='alert'><p>Error aun no determinado</p></div>";
		}else{
			$error.="<div class='alert'><p>No se Puede Coersar($id);mpletar la operacion s</p></div>";
		}
		
		if(empty($error)){
			$mbanc->set('status','E1');
			$mbanc->save();

			$saldo+=$m_monto;
			$banc->set('saldo',$saldo);
			$banc->save();
			logusu('ppro',"Reverso movimiento Nro $id");
			if($this->redirect)redirect($this->url."dataedit/show/$id");
		}else{
			logusu('ppro',"Reverso movimiento Nro $id con ERROR:$error");
			$data['content'] = $error.anchor($this->url."/dataedit/show/$id",'Regresar');
			$data['title']   = " ".$this->tits." ";
			$data["head"]    = $this->rapyd->get_head().script('jquery.js').script("plugins/jquery.numeric.pack.js");
			$this->load->view('view_ventanas', $data);
		}
	}
		

	function anular($id){
		$this->rapyd->load('dataobject');
	
		$do = new DataObject("mbanc");
		$do-> load($id);
	
		$this->anular      = true;
		$this->redirect    = false;
		$this->reversar($id);
		$this->redirect    = true;
		
		$do->set('status','A');
		$do->save();
				
		logusu('ppro',"Anulo Desembolso de Nro $id");
		redirect("tesoreria/ppro/dataedit/show/$id");	
	
	}
	
		
	function cambcheque($var1,$var2,$id){
	
		//$this->datasis->modulo_id(101,1);
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
		';
		
		$do2 = new DataObject("mbanc");
		$do2->pointer('banc' ,'banc.codbanc=mbanc.codbanc','banc.banco as nombreb,banc.banco as nombrebt','LEFT');
		
		$do2->load($id);
		
		$do = new DataObject("mbanc");		
		$do->pointer('banc' ,'banc.codbanc=mbanc.codbanc','banc.banco as nombreb,banc.banco as nombrebt','LEFT');
		$do->pointer('bcta' ,'bcta.codigo =  mbanc.bcta','bcta.denominacion as bctad ','LEFT');
				
		$edit = new DataEdit2("Cambiar Cheque", $do);
		
		$edit->back_url = site_url($this->url."dataedit/show/$id");
		
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
		
		//$edit->anulado = new checkboxField("Anular este Cheque", "anulado" ,"S");   
		//$edit->anulado->value = "S";
		//$edit->anulado->group   = "Datos Cheque Actual";
		
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
		$edit->codbanc->mode    = "autohide";
		
		$edit->tipo_doc = new dropdownField("Tipo Documento","tipo_doc");
    $edit->tipo_doc->option("CH","Cheque"         );
    $edit->tipo_doc->option("ND","Nota de Debito" );
    $edit->tipo_doc->option("DP","Deposito"         );
    $edit->tipo_doc->style   = "width:220px";
		$edit->tipo_doc->group   = "Datos Cheque Nuevo";
		$edit->tipo_doc->mode    = "autohide";
		
		$edit->nombreb = new inputField("Nombre", 'nombreb');
		$edit->nombreb->size      = 50;
		$edit->nombreb->in        = "codbanc";
		$edit->nombreb->pointer   = true;
		$edit->nombreb->group     = "Datos Cheque Nuevo";
		$edit->nombreb->mode    = "autohide";

		$edit->fecha = new  dateonlyField("Fecha Cheque",  "fecha");		
		//$edit->fecha->mode    = "autohide";
		$edit->fecha->group   = "Datos Cheque Nuevo";
						
		$edit->observa = new textAreaField("Observaci&oacute;nes", 'observa');
		//$edit->observa->mode    = "autohide";
		$edit->observa->rows    = 4;
		$edit->observa->cols    = 70;
		$edit->observa->group   = "Datos Cheque Nuevo";
		$edit->observa->mode    = "autohide";

		$edit->monto = new inputField("Monto", 'monto');
		$edit->monto ->mode ="autohide";
		$edit->monto ->css_class ="inputnum";
		$edit->monto->size       = 15;
		$edit->monto->group      = "Datos Cheque Nuevo";
		
		//$edit->bcta = new inputField("Motivo Movimiento", 'bcta');
		//$edit->bcta->size     = 6;
		////$edit->bcta->rule     = "required";
		//$edit->bcta->append($bBCTA);
		//$edit->bcta->readonly=true;
		////$edit->bcta->group = "Deposito";
    //
		//$edit->bctad = new inputField("", 'bctad');
		//$edit->bctad->size        = 50;
		////$edit->bctad->group       = "Deposito";
		//$edit->bctad->in          = "bcta";
		//$edit->bctad->pointer     = true;
		//$edit->bctad->readonly    = true;
		
		$edit->buttons("modify","save","delete","undo", "back");
		$edit->build();
		
		$data['content'] = $edit->output;
    $data['title']   = " Cambiar Cheque ";
    $data["head"]    = script("jquery.pack.js").script("plugins/jquery.numeric.pack.js").script("plugins/jquery.floatnumber.js").$this->rapyd->get_head();
    $this->load->view('view_ventanas', $data);
		
	}
	
	function _validacheque($do){
		$this->rapyd->load('dataobject');
		$error = '';
		
		//$codbanc =$do->get('codbanc' );
		//$monto   =$do->get('monto'   );
		//$cheque  =$do->get('cheque'  );
		//$tipo_doc=$do->get('tipo_doc');
		//$observa =$do->get('observa' );
		//$id      =$do->get('id'      );
		//$fecha   =$do->get('fecha'   );
		//$bcta    =$do->get('bcta'    );
		//$anulado =$do->get('anulado' );
		
		//$banc   = new DataObject("banc");
		//$banc   ->load($codbanc);
		//$saldo  = $banc->get('saldo');
		//$activo = $banc->get('activo');
		//$banco  = $banc->get('banco');
		//
		//if($activo != 'S' )
		//	$error.="<div class='alert'><p>El banco ($banco) esta inactivo</p></div>";
		//	
		//if($monto > $saldo )
		//	$error.="<div class='alert'><p>El Monto ($monto) del cheque es mayor al disponible ($saldo) en el banco ($banco)</p></div>";
    //
		//$mbanc = new DataObject("mbanc");
		//$mbanc->load($id);
		//
		//$tcodbanc =$mbanc->get('codbanc' );
		//$tmonto   =$mbanc->get('monto'   );
		//$tcheque  =$mbanc->get('cheque'  );
		//$ttipo_doc=$mbanc->get('tipo_doc');
		//$tobserva =$mbanc->get('observa' );
		//$tfecha   =$mbanc->get('fecha'   );
		//	
		//$banc   ->load($tcodbanc);
		//$activo = $banc->get('activo');
		//$banco  = $banc->get('banco' );
		//$saldo  = $banc->get('saldo' );
		//
		//if($activo != 'S' )
		//	$error.="<div class='alert'><p>El banco ($banco) esta inactivo</p></div>";
			
		$this->chexiste_cheque($cheque,$tipo_doc,$id,$e);
		$error.=$e;
		
		if(empty($error)){
			//$banc->set('saldo',$saldo-$monto);
			//$banc->save();
			//
			//$banc   ->load($codbanc);
			//$saldo  = $banc->get('saldo');
			//$banc->set('saldo',$saldo+$monto);
			//$banc->save();
			//
			//
			//$do->set('bcta'    ,'');
			//
			//$mbanc = new DataObject("mbanc");
			//
			//$mbanc->set('fecha'      ,$tfecha    );
			//$mbanc->set('observa'    ,$tobserva  );
			//$mbanc->set('codbanc'    ,$tcodbanc  );
			//$mbanc->set('tipo_doc'   ,$ttipo_doc );
			//$mbanc->set('cheque'     ,$tcheque   );
			//$mbanc->set('monto'      ,$tmonto    );				
			//$mbanc->set('anulado'    ,'S'        );
			//$mbanc->set('anuladopor' ,$cheque    );
			//$mbanc->save();
			//
			//$mbanc = new DataObject("mbanc");
			//
			//$mbanc->set('fecha'      ,$fecha     );
			//$mbanc->set('observa'    ,'nota de credito creada para respaldar el nuevo cheque'  );
			//$mbanc->set('codbanc'    ,$tcodbanc  );
			//$mbanc->set('tipo_doc'   ,'NC'       );
			//$mbanc->set('cheque'     ,'NC'.$cheque);
			//$mbanc->set('monto'      ,$monto     );				
			//$mbanc->set('anulado'    ,''         );
			//$mbanc->set('anuladopor' ,''         );
			//$mbanc->set('bcta'       ,$bcta      );
			//$mbanc->save();
			
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
		redirect($this->url."dataedit/show/$id");
	}
	
	function chexiste_cheque($cheque,$tipo_doc,$id,&$error){
		$cheque     = $this->db->escape($cheque       );
		$tipo_doc   = $this->db->escape($tipo_doc     );
		
		if($id>0)$query="SELECT id FROM mbanc WHERE cheque=$cheque AND tipo_doc=$tipo_doc AND id<>$id";
		else $query="SELECT id FROM mbanc WHERE cheque=$cheque AND tipo_doc=$tipo_doc";
		
		$cana=$this->datasis->dameval($query);
		if($cana>0)
			$error="El Cheque ya Existe para el desembolso $cana";
	}
	
	function _post($do){
		$id=$do->get('id');
		redirect($this->url."actualizar/$id");
	}
	
	function _post_insert($do){
		$tipo_doc   = $do->get('tipo_doc');
		$cheque     = $do->get('cheque');
		$id         = $do->get('id');
		logusu('ppro',"Creo $tipo_doc Nro $cheque movimento $id");
		redirect($this->url."actualizar/$id");
	}
	function _post_update($do){
		$tipo_doc   = $do->get('tipo_doc');
		$cheque     = $do->get('cheque');
		$id         = $do->get('id');
		logusu('ppro',"modifico $tipo_doc Nro $cheque movimento $id");
		redirect($this->url."actualizar/$id");
	}
	
	function _post_delete($do){
		$tipo_doc   = $do->get('tipo_doc');
		$cheque     = $do->get('cheque');
		$id         = $do->get('id');
		logusu('ppro',"elimino $tipo_doc Nro $cheque movimento $id");
	}
	
	function reversarall(){
		$query = $this->db->query("SELECT * FROM mbanc WHERE status = 'E2' ");
		$result = $query->result();
		 foreach ($result AS $items){ 
		 	$numero =$items->id;
		 	$this->reversar($numero);
		 }
	}
	
	function actualizarall(){
		$query = $this->db->query("SELECT * FROM mbanc WHERE status = 'E1' ");
		$result = $query->result();
		 foreach ($result AS $items){ 
		 $numero =$items->id;
		 	$this->actualizar($numero);
		 }
	}
	
	

}
?>




