<?php
class opago extends Controller {

	var $titp='Ordenes de Pago';
	var $tits='Orden de pago';
	var $url='presupuesto/opago/';

	function opago(){
		parent::Controller();
		$this->load->library("rapyd");
		//$this->datasis->modulo_id(302,1);
	}
	function index(){
		redirect($this->url."/filteredgrid");
	}

	function filteredgrid(){
		//$this->datasis->modulo_id(103,1);
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

		$filter = new DataFilter("Filtro de ".$this->titp);
		
		$filter->db->select("a.numero numero,a.fecha fecha,a.tipo tipo,a.status status,a.cod_prov cod_prov,a.beneficiario beneficiario,b.nombre uejecuta2,c.nombre proveed");//
		$filter->db->from("odirect a");                  
		$filter->db->join("uejecutora b" ,"a.uejecutora=b.codigo", "LEFT");
		$filter->db->join("sprv c"       ,"c.proveed=a.cod_prov", "LEFT");
		//$filter->db->where("a.status !=", "P");
		//$filter->db->where("a.status !=", "C");

		$filter->tipo = new dropdownField("Orden de ", "tipo");
		$filter->tipo->db_name = 'a.tipo';
		$filter->tipo->option("","");
		$filter->tipo->option("Compra"  ,"Compra");
		$filter->tipo->option("Servicio","Servicio");
		$filter->tipo->style="width:100px;";
		
		$filter->fecha = new dateonlyField("Fecha", "fecha");
		$filter->fecha->size=12;
		
		$filter->uejecutora = new dropdownField("U.Ejecutora", "uejecutora");
		$filter->uejecutora->option("","Seccionar");
		$filter->uejecutora->options("SELECT codigo,nombre FROM uejecutora ORDER BY nombre");
		$filter->uejecutora->onchange = "get_uadmin();";
		$filter->uejecutora->rule = "required";
		
		$filter->cod_prov = new inputField("Beneficiario", 'cod_prov');
		$filter->cod_prov->size = 6;
		$filter->cod_prov->append($bSPRV);
		$filter->cod_prov->rule = "required";
		$filter->cod_prov->clause   = 'where';
		$filter->cod_prov->operator = '=';
		
		$filter->beneficiario = new inputField("Beneficiario", "beneficiario");
		$filter->beneficiario->size=60;
		
		$filter->status = new dropdownField("Estado","status");
		$filter->status->option("","");
		$filter->status->option("T","Causado");
		$filter->status->option("O","Ordenado Pago");
		$filter->status->style="width:150px";
		
		$filter->buttons("reset","search");
		$filter->build();
		$uri = anchor($this->url.'dataedit/show/<#numero#>','<str_pad><#numero#>|8|0|STR_PAD_LEFT</str_pad>');

		function sta($status){
			switch($status){
				case "T":return "Causado";break;
				case "O":return "Ordenado Pago";break;
				//case "A":return "Anulado";break;
			}
		}

		$grid = new DataGrid("Lista de ".$this->titp);

		$grid->order_by("numero","desc");//status='P'
		$grid->per_page = 20;
		$grid->use_function('substr','str_pad');
		$grid->use_function('sta');

		$grid->column("N&uacute;mero"   ,$uri);
		$grid->column("Fecha"           ,"<dbdate_to_human><#fecha#></dbdate_to_human>","align='center'");
		$grid->column("Tipo"            ,"tipo"                                        ,"align='center'");
		$grid->column("Unidad Ejecutora","uejecuta2");
		$grid->column("Beneficiario"       ,"proveed");
		$grid->column("Beneficiario"    ,"beneficiario");
		$grid->column("Estado"          ,"<sta><#status#></sta>"                       ,"align='center'");

		//$grid->db->where("status !=",'P');
		//$grid->db->where("status !=",'C');

		$grid->add($this->url."dataedit/create");
		$grid->build();
		//echo "asasa".$grid->db->last_query();

		$data['content'] = $filter->output.$grid->output;
		$data['title']   = " ".$this->titp." ";
		$data["head"]    = $this->rapyd->get_head();
		$this->load->view('view_ventanas', $data);
	}

	function dataedit(){
		//$this->datasis->modulo_id(101,1);
		$this->rapyd->load('dataobject','dataedit');
		
		$mOCOMPRA=array(
				'tabla'    =>'ocompra',
				'columnas' =>array(
					'numero'     =>'N&uacute;mero',
					'tipo'       =>'Tipo',
					'uejecutora' =>'uejecutora',
					'cod_prov'   =>'Beneficiario'),
				'filtro'  =>array(
					'numero'     =>'N&uacute;mero',
					'tipo'       =>'Tipo',
					'uejecutora' =>'uejecutora',
					'cod_prov'   =>'Beneficiario'),
				'retornar'=>array(
					'numero'     =>'compra',
					'tipo'       =>'tipo',
					'uejecutora' =>'uejecutora',
					'cod_prov'   =>'cod_prov',
					'fechafac'   =>'fechafac',
					'factura'    =>'factura',
					'controlfac' =>'controlfac',
					'total'      =>'ototal',  
					'abonado'    =>'oabonado',),
				  'where'=>'total > abonado',
				'titulo'  =>'Buscar Ordenes de Compra');
				
		$pOCOMPRA=$this->datasis->p_modbus($mOCOMPRA,"ocompra");

		$do = new DataObject("odirect");
		$do->rel_one_to_many('pacom', 'pacom', array('numero'=>'pago'));
		
		$edit = new DataDetails($this->tits, $do);
		$edit->back_url = site_url($this->url."filteredgrid");
		$edit->set_rel_title('pago','Rubro <#o#>');

		//$edit->pre_process('update'   ,'_valida');
		//$edit->pre_process( 'insert'  ,'_valida');
		//$edit->post_process('insert'  ,'_post');
		//$edit->post_process('update'  ,'_post');

		$edit->id  = new inputField("N&uacute;mero", "id");
		$edit->id->mode  ="autohide";
		$edit->id->when  =array('show');
		$edit->id->group ="Pago";

		$edit->itcompra = new  inputField("(<#o#>) Numero O. Compra",  "compra_<#i#>");		
		$edit->itcompra->append($bOCOMPRA);
		$edit->itcompra->rel_id   ='pacom';

		$edit->factura = new  inputField("Factura",  "factura");
		$edit->factura->mode ="autohide";
		$edit->factura->group="Orden De Compra";

		$edit->controlfac = new  inputField("Control Fiscal",  "controlfac");
		$edit->controlfac->mode ="autohide";
		$edit->controlfac->group="Orden De Compra";

		$edit->fechafac = new  inputField("Fecha Causaci&oacute;n",  "fechafac");
		$edit->fechafac->mode        = "autohide";
		$edit->fechafac->group       = "Orden De Compra";

		$edit->tipo = new inputField("Orden de", "tipo");
		$edit->tipo->mode ="autohide";
		$edit->tipo->group="Orden De Compra";

		$edit->uejecutora = new inputField("Unidad Ejecutora", "uejecutora");
		$edit->uejecutora->mode  = "autohide";
		$edit->uejecutora->group = "Orden De Compra";
  	
		$edit->cod_prov = new inputField("Beneficiario", 'cod_prov');
		$edit->cod_prov->size     = 6;
		$edit->cod_prov->group    = "Orden De Compra";
		$edit->cod_prov->mode     = "autohide";
		
		$edit->oabonado = new inputField("Abonado", 'oabonado');
		$edit->oabonado->db_name  = ' ';
		$edit->oabonado->size     = 8;
		$edit->oabonado->mode     = "autohide";
	  $edit->oabonado->group    = "Orden De Compra";
	  $edit->oabonado->when     = array('create');

		$edit->ototal = new inputField("Total O. Compra", 'ototal');
		$edit->ototal->db_name  = ' ';
		$edit->ototal->size     = 8;
		$edit->ototal->mode     = "autohide";
		$edit->ototal->group    ="Orden De Compra";
		$edit->ototal->when     = array('create');
		
		$edit->fecha = new  dateonlyField("Fecha de Pago",  "fecha");
		$edit->fecha->insertValue = date('Y-m-d');
		$edit->fecha->size =12;
		$edit->fecha->rule ="required";
		$edit->fecha->group="Pago";
		
		$edit->pago = new inputField("Pagar", 'pago');
		$edit->pago->size     = 8;
		$edit->pago->mode     = "autohide";
		$edit->pago->group    = "Pago";
		
  
		$n=$edit->_dataobject->get('numero');

		$status=$edit->_dataobject->get("status");
		if($status=='T'){
			//$action = "javascript:window.location='" .site_url($this->url.'actualizar/'.$edit->rapyd->uri->get_edited_id()). "'";
			//$action = "javascript:window.location='" .site_url($this->url.'actualizar/'.$n)."'";
			//$edit->button_status("btn_status",'Ordenar Pago',$action,"TR","show");
			$edit->buttons("modify","save");
		}elseif($status=='O'){
			$action = "javascript:window.location='" .site_url($this->url.'reversar/'.$edit->rapyd->uri->get_edited_id()). "'";
			//$action = "javascript:window.location='" .site_url($this->url.'reversar/'.$n). "'";
			$edit->button_status("btn_rever",'Deshacer Ordenar Pago',$action,"TR","show");
		}else{
			$edit->buttons("save");
		}

		$edit->buttons("undo", "back");
		$edit->build();

		$data['content'] = $edit->output;
    $data['title']   = " $this->tits ";
		//$data['content'] = $edit->output;
    //$data['title']   = " $this->tits ";
    $data["head"]    = script("jquery.pack.js").script("plugins/jquery.numeric.pack.js").script("plugins/jquery.floatnumber.js").$this->rapyd->get_head();
    $this->load->view('view_ventanas', $data);
	}

	function _valida($do){
		$this->rapyd->load('dataobject');
		
		$error='';

		$compra = $do->get('compra');
		$pago   = $do->get('pago'  );

		$ocompra = new DataObject("ocompra");

		if(!empty($compra))$ocompra->load($compra);

		$do->set('tipo'         , $ocompra->get('tipo'        ));
		$do->set('uejecutora'   , $ocompra->get('uejecutora'  ));
		$do->set('estadmin'     , $ocompra->get('estadmin'    ));
		$do->set('fondo'        , $ocompra->get('fondo'       ));
		$do->set('cod_prov'     , $ocompra->get('cod_prov'    ));
		$do->set('beneficiario' , $ocompra->get('beneficiario'));
		$do->set('factura'      , $ocompra->get('factura'     ));
		$do->set('controlfac'   , $ocompra->get('controlfac'  ));
		$do->set('fechafac'     , $ocompra->get('fechafac'    ));
		
		$o_subtotal = $ocompra->get('subtotal');
		$o_ivag     = $ocompra->get('ivag'    );
		$o_ivar     = $ocompra->get('ivar'    );
		$o_ivaa     = $ocompra->get('ivaa'    );
		$o_total    = $ocompra->get('total'   );
		$o_reten    = $ocompra->get('reten'   );
		$o_reteiva  = $ocompra->get('reteiva' );
		$o_abonado  = $ocompra->get('abonado' );

		$o_iva      = $o_ivaa+$o_ivag+$o_ivar;
		$o_debe     = ($o_subtotal + ($o_iva - ($o_reten + $o_reteiva)))-$o_abonado;

		if($pago  > $o_debe)
			$error.="<div class='alert'><p>El monto de la orden de pago($pago) es mayor al monto adeudado ($o_debe)</p></div>";

		$do->set('status' ,'T');
		$do->set('compra'  , $compra);

		if(!empty($error)){
			$do->error_message_ar['pre_upd']=$error;
      $do->error_message_ar['pre_ins']=$error;
      return false;
    }
	}

	function actualizar($id){
		
		$this->rapyd->load('dataobject');

		$error='';

		$odirect = new DataObject("odirect");
		
		$odirect->load($id);

		$status = $odirect->get('status');
		$compra = $odirect->get('compra');
		$pago   = $odirect->get('pago' );
		
		//print_r($odirect->get_all());
		//
		//echo "-".$status;
		//exit;
		if($status == 'T'){
			$ocompra   = new DataObject("ocompra");
			$ocompra   ->rel_one_to_many('odirect'	, 'odirect'  , array('numero'=>'compra'));
			$ocompra   ->load($compra);

			$o_subtotal = $ocompra->get('subtotal');			                      
			$o_ivag     = $ocompra->get('ivag'    );
			$o_ivar     = $ocompra->get('ivar'    );
			$o_ivaa     = $ocompra->get('ivaa'    );
			$o_reten    = $ocompra->get('reten'   );
			$o_reteiva  = $ocompra->get('reteiva' );
			$o_abonado  = $ocompra->get('abonado' );

			$o_iva      = $o_ivaa+$o_ivag+$o_ivar;			
			$o_debe     = $o_subtotal + ($o_iva - ($o_reten + $o_reteiva));
			
			$tot=0;
			for($i=0;$i < $ocompra->count_rel('odirect');$i++){
				$r_pago   = $ocompra->get_rel('odirect','pago' ,$i);
				$tot   += $r_pago;
			}


			$a = $o_debe-($tot-$pago);
			if($pago > $a)
				$error.="<div class='alert'><p>El monto de la orden de pago($pago) es mayor al monto adeudado ($a)</p></div>";
				
		}else{
			$error.="<div class='alert'><p>No se Puede Completar la operacion</p></div>";
		}
	
		if(empty($error)){
			$ocompra -> set('abonado',$tot);
			$odirect -> set('status' ,'O' );
			
			if($tot = $o_debe){
				$odirect->set('ivag'    ,$ocompra->get('ivag'    ));
				$odirect->set('ivar'    ,$ocompra->get('ivar'    ));
				$odirect->set('ivaa'    ,$ocompra->get('ivaa'    ));
				$odirect->set('reten'   ,$ocompra->get('reten'   ));
				$odirect->set('creten'  ,$ocompra->get('creten'  ));
				$odirect->set('reteiva' ,$ocompra->get('reteiva' ));
				$odirect->set('exento'  ,$ocompra->get('exento'  ));
				
				$ocompra   ->rel_one_to_many('itocompra'	, 'itocompra'  , array('numero'=>'numero'));
				$ocompra   ->load($compra);
				
				$codigoadm  = $ocompra->get('estadmin');
				$fondo      = $ocompra->get('fondo');
				
				$presup = new DataObject("presupuesto");
				$pk=array('codigoadm'=>$codigoadm,'tipo'=>$fondo);
				
				$partidaiva=$this->datasis->traevalor("PARTIDAIVA");
				
				for($i=0;$i < $do->count_rel('itocompra');$i++){
						$codigopres  = $do->get_rel('itocompra','partida',$i);					
						$importe     = $do->get_rel('itocompra','importe',$i);
						$iva         = $do->get_rel('itocompra','iva'    ,$i);
						$mont        = $importe;
										
						$pk['codigopres'] = $codigopres;
						
						$presup->load($pk);
						$opago=$presup->get("opago");
						$opago=$opago+$mont;
						
						$presup->set("opago",$opago);
						
						$presup->save();
					}
					
					$pk['codigopres'] = $partidaiva;
					$presup->load($pk);
					
					$opago =$presup->get("opago");
					$opago+=$ivan;
					$presup->set("opago",$opago);
					$presup->save();
			}
			
			$ocompra -> save();
			$odirect -> save();
		}else{
			$odirect -> delete();	
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

		$error='';

		$odirect = new DataObject("odirect");
		
		$odirect->load($id);

		$status = $odirect->get('status');
		$compra = $odirect->get('compra');
		$pago   = $odirect->get('pago' );
		
		//print_r($odirect->get_all());
		//
		//echo "-".$status;
		//exit;
		if($status == 'O'){
			$ocompra   = new DataObject("ocompra");
			$ocompra   ->rel_one_to_many('odirect'	, 'odirect'  , array('numero'=>'compra'));
			$ocompra   ->load($compra);

			$o_subtotal = $ocompra->get('subtotal');			                      
			$o_ivag     = $ocompra->get('ivag'    );
			$o_ivar     = $ocompra->get('ivar'    );
			$o_ivaa     = $ocompra->get('ivaa'    );
			$o_reten    = $ocompra->get('reten'   );
			$o_reteiva  = $ocompra->get('reteiva' );
			$o_abonado  = $ocompra->get('abonado' );

			$o_iva      = $o_ivaa+$o_ivag+$o_ivar;			
			$o_debe     = $o_subtotal + ($o_iva - ($o_reten + $o_reteiva));
			
			$tot=0;
			for($i=0;$i < $ocompra->count_rel('odirect');$i++){
				$r_pago   = $ocompra->get_rel('odirect','pago' ,$i);
				$tot   += $r_pago;
			}
			//$a = $o_debe-($tot-$pago);
			//if($pago > $a)
			//	$error.="<div class='alert'><p>El monto de la orden de pago($pago) es mayor al monto adeudado ($a)</p></div>";
				
		}else{
			$error.="<div class='alert'><p>No se Puede Completar la operacion</p></div>";
		}
		
	
		if(empty($error)){
			$ocompra -> set('abonado',$tot-$pago);
			$odirect -> set('status' ,'T' );
			
			if($tot = $o_debe){
				$odirect->set('ivag'    , 0 );
				$odirect->set('ivar'    , 0 );
				$odirect->set('ivaa'    , 0 );
				$odirect->set('reten'   , 0 );
				$odirect->set('creten'  , 0 );    
				$odirect->set('reteiva' , 0 );
				$odirect->set('exento'  , 0 );
			}
			
			$ocompra -> save();
			$odirect -> save();
		}else{
			//$odirect -> delete();	
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

	function _post($do){
		echo "en post";
		$id      = $do->get("numero");
		redirect($this->url."actualizar/$id");
	}
}
