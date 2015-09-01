<?php

require_once(BASEPATH.'application/controllers/presupuesto/common.php');
class Opago extends Common {

	var $titp='Orden de Pago';
	var $tits='Orden de Pago';
	var $url='presupuesto/opago/';

	function opago(){
		parent::Controller();
		$this->load->library("rapyd");
		$this->formatopres =$this->datasis->traevalor('FORMATOPRES');
		$this->flongpres   =strlen(trim($this->formatopres));
		$this->datasis->modulo_id(77,1);
	}

	function index(){
		redirect($this->url."filteredgrid");
	}

	function filteredgrid(){
		//$this->datasis->modulo_id(101,1);
		$this->rapyd->load("datafilter","datagrid");
		$this->rapyd->uri->keep_persistence();

		$mSPRV=array(
						'tabla'   =>'sprv',
						'columnas'=>array(
							'proveed' =>'C&oacute;odigo',
							'nombre'  =>'Nombre',
                                                        'rif'     =>'Rif',
							'contacto'=>'Contacto'),
						'filtro'  =>array(
							'proveed'=>'C&oacute;digo',
							'nombre' =>'Nombre',
                                                        'rif'    =>'Rif'),
						'retornar'=>array(
							'proveed'=>'<#i#>'),
                                                'p_uri'   =>array(
                                                                  4=>'<#i#>'
                                                                  ),
						'titulo'  =>'Buscar Beneficiario');

		$bSPRV=$this->datasis->p_modbus($mSPRV,"cod_prov");

		$filter = new DataFilter("");

		$filter->db->select(array("d.compromiso","c.compra","a.numero","a.fecha","a.tipo","a.uejecutora","a.estadmin","a.fondo","a.cod_prov","a.status","a.total","b.nombre proveed","e.nombre autor"));
		$filter->db->from("odirect a");
		$filter->db->join("pacom c"   ,"a.numero=c.pago"     ,"left");
		$filter->db->join("ocompra d"   ,"d.numero=c.compra"     ,"left");
		$filter->db->join("sprv b"    ,"b.proveed=a.cod_prov","left");
                $filter->db->join("sprv e"    ,"e.proveed=a.cod_prov2","left");
		$filter->db->where('MID(a.status,1,1) ','F');
		$filter->db->groupby("a.numero");

		$filter->numero = new inputField("N&uacute;mero", "numero");
		$filter->numero->db_name="a.numero";
		$filter->numero->size  =10;

		$filter->compra = new inputField("Orden de Compra", "compra");
		$filter->compra->db_name="c.compra";
		$filter->compra->size  =10;

		$filter->compromiso = new inputField("Compromiso", "compromiso");
		$filter->compromiso->db_name="d.compromiso";
		$filter->compromiso->size  =10;

		$filter->fecha = new dateonlyField("Fecha", "fecha");
		$filter->fecha->size=12;

		$filter->cod_prov = new inputField("Beneficiario", 'cod_prov');
		$filter->cod_prov->append($bSPRV);
        $filter->cod_prov->db_name  ="a.cod_prov";
		$filter->cod_prov->size     = 6;
		$filter->cod_prov->clause   = 'where';
		$filter->cod_prov->operator = '=';

		$filter->status = new dropdownField("Estado","status");
		$filter->status->db_name="a.status";
		$filter->status->option("","");
		$filter->status->option("F1","Sin Ejecutar");
		$filter->status->option("F2","Ejecutado");
		$filter->status->option("F3","Pagado");
		$filter->status->style="width:150px";

		$filter->buttons("reset","search");

		$filter->build();
		$uri = anchor($this->url.'dataedit/show/<#numero#>','<#numero#>');

		function sta($status){
			switch($status){
				case "F1":return "Sin Ejecutar";break;
				case "F2":return "Ejecutado";break;
				case "F3":return "Pagado";break;
				case "FA":return "Anulado";break;
			}
		}

		$grid = new DataGrid("");
		$grid->order_by("numero","desc");
		$grid->per_page = 20;
		$grid->use_function('substr','str_pad');
		$grid->use_function('sta');

		$grid->column_orderby("N&uacute;mero"    ,$uri                                            ,"numero");
		$grid->column_orderby("Compras"          ,"compra"                                        ,"compra");
		$grid->column_orderby("Compromiso"       ,"compromiso"                                    ,"compromiso");
		$grid->column_orderby("Fecha"            ,"<dbdate_to_human><#fecha#></dbdate_to_human>"  ,"fecha"         ,"align='center'");
		$grid->column_orderby("Beneficiario"     ,"proveed"                                       ,"proveed"       );
        if($this->datasis->traevalor('USA2COD_PROVENODIREC')=='S')
        $grid->column_orderby(""                 ,"autor"                                       ,"autor"         ,"align='left'");
		$grid->column_orderby("Estado"           ,"<sta><#status#></sta>"                         ,"status"        ,"align='center'");
		$grid->column_orderby("Pago"             ,"<number_format><#total#>|2|,|.</number_format>","total"          ,"align='right'" );

		
		$grid->add($this->url."dataedit/create");
		$grid->build();
		
		//echo $grid->db->last_query();

		$data['filtro']  = $filter->output;
		$data['content'] = $grid->output;
		$data['script'] = script("jquery.js")."\n";
		$data['title']   = "";//" $this->titp ";
		//$data['content'] = $filter->output.$grid->output;
		$data['title']   = "$this->titp";
		$data["head"]    = $this->rapyd->get_head();
		$this->load->view('view_ventanas', $data);
	}

	function dataedit($back=''){
		//$this->datasis->modulo_id(101,1);
		$this->rapyd->load('dataobject','datadetails');

		$mSPRV=array(
				'tabla'   =>'sprv',
				'columnas'=>array(
				'proveed' =>'C&oacute;odigo',
				'nombre'=>'Nombre',
				'grupo'   =>'Grupo',
				'contacto'=>'Contacto'),
				'filtro'  =>array('proveed'=>'C&oacute;digo','nombre'=>'Nombre','grupo'   =>'Grupo'),
				'retornar'=>array('proveed'=>'cod_prov'     , 'nombre'=>'nombrep'),

				'titulo'  =>'Buscar Beneficiario');

		$bSPRV=$this->datasis->p_modbus($mSPRV,"proveed");


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
					'numero'     =>'compra_<#i#>',
					'total'      =>'totalo_<#i#>',
					'otrasrete'  =>'otrasreteo_<#i#>',
					'total2'     =>'total2o_<#i#>',
					'reten'      =>'reteno_<#i#>',
					'reteiva'    =>'reteivao_<#i#>',
					'imptimbre'  =>'imptimbreo_<#i#>',
					'cod_prov'   =>'cod_prov'//,
					//'observa'    =>'observat',
					),
			'p_uri'=>array(
				  4=>'<#i#>',
				  5=>'<#cod_prov#>'),
			'where' =>'( status = "T" ) AND IF(<#cod_prov#> = ".....", cod_prov LIKE "%" ,cod_prov = <#cod_prov#>)',//AND ( status = "T" OR status = "P" )  OR status = "C"
			'script'=>array('cal_concepto(<#i#>)','cal_total(<#i#>)'),
				'titulo'  =>'Buscar Ordenes de Compra');

		$pOCOMPRA=$this->datasis->p_modbus($mOCOMPRA,'<#i#>/<#cod_prov#>');

		$do = new DataObject("odirect");
		$do->pointer('sprv'  ,'sprv.proveed=odirect.cod_prov','sprv.nombre as nombrep','LEFT');
        $do->pointer('sprv AS sprv2','sprv2.proveed=odirect.cod_prov2','sprv2.nombre as nombrep2','LEFT');
		$do->rel_one_to_many('pacom', 'pacom', array('numero'=>'pago'));
		$do->rel_pointer('pacom','ocompra' ,'pacom.compra=ocompra.numero',"ocompra.total AS totalo,ocompra.total2 AS total2o,ocompra.reteiva AS reteivao,ocompra.reten AS reteno,ocompra.certificado AS certificadoo,ocompra.imptimbre AS imptimbreo,ocompra.otrasrete AS otrasreteo");

		$edit = new DataDetails($this->tits, $do);
		if($back=='opagof')
		$edit->back_url = site_url("presupuesto/opagof/filteredgrid");
		else
		$edit->back_url = site_url($this->url."filteredgrid/index");
		
		$edit->set_rel_title('pacom','Rubro <#o#>');

		$edit->pre_process('insert'  ,'_valida');
		$edit->pre_process('update'  ,'_valida');
		////$edit->post_process('insert'  ,'_post');
		////$edit->post_process('update'  ,'_post');
		$edit->post_process('insert','_post_insert');
		$edit->post_process('update','_post_update');
		$edit->post_process('delete','_post_delete');


		$edit->numero  = new inputField("N&uacute;mero", "numero");
		$edit->numero->mode="autohide";

		if($this->datasis->traevalor('USANODIRECT')=='S'){
			$edit->numero->when=array('show');
		}else{
			$edit->numero->when=array('show','create');
		}

		$edit->fecha = new  dateonlyField("Fecha",  "fecha");
		$edit->fecha->insertValue = date('Y-m-d');
		$edit->fecha->size        =12;
		$edit->fecha->rule        = 'required';

		$edit->cod_prov = new inputField("Beneficiario", 'cod_prov');
		$edit->cod_prov->size     = 6;
		$edit->cod_prov->rule     = "required";
		$edit->cod_prov->append($bSPRV);

		//$edit->cod_prov->readonly=true;
                if($this->datasis->traevalor('USA2COD_PROVENODIREC')=='S'){
                    $edit->cod_prov2 = new inputField("Beneficiario", 'cod_prov2');
                    $edit->cod_prov2->size       = 4;
                    $edit->cod_prov2->readonly   =true;

                }

		$edit->tipoc = new dropdownField("Tipo de Pago","tipoc");
		$edit->tipoc->option("OT","Otro");
		$edit->tipoc->option("FA","Fondo en anticipo");

		$edit->nombrep = new inputField("Nombre", 'nombrep');
		$edit->nombrep->db_name   = 'nombrep';
		$edit->nombrep->size      = 30;
		$edit->nombrep->readonly  = true;
		$edit->nombrep->pointer   = true;
		$edit->nombrep->in        = "cod_prov";

                $edit->nombrep2 = new inputField("Nombre", 'nombrep2');
		$edit->nombrep2->db_name   = 'nombrep2';
		$edit->nombrep2->size      = 30;
		$edit->nombrep2->readonly  = true;
		$edit->nombrep2->pointer   = true;
		$edit->nombrep2->in        = "cod_prov2";

		$edit->observa = new textAreaField("Observaciones", 'observa');
		$edit->observa->cols = 70;
		$edit->observa->rows = 3;

		$campos = array('total2','otrasrete','imptimbre','reten','reteiva','total');
		foreach($campos AS $campo=>$objeto){
			$edit->$objeto = new inputField("", $objeto);
			$edit->$objeto->size     = 10;
			$edit->$objeto->readonly = true;
		}
		//////////////////////////////////// DETALLE ///////////////////////////////////////////////////////////////

		$edit->itcompra = new inputField("(<#o#>) ", "compra_<#i#>");
		$edit->itcompra->rule     ='callback_repetido|required|callback_itorden';//
		$edit->itcompra->size     =15;
		$edit->itcompra->db_name  ='compra';
		$edit->itcompra->rel_id   ='pacom';
		$edit->itcompra->readonly =true;
		$edit->itcompra->append('<img src="'.base_url().'assets/default/images/system-search.png"  alt="Busqueda de Ordenes de Pago" title="Busqueda de Ordenes de Pago" border="0" onclick="modbusdepen(<#i#>)"/>');

		$campos = array('certificadoo','total2o','otrasreteo','imptimbreo','reteno','reteivao','totalo');//,'totalo'
		foreach($campos AS $campo=>$objeto){
			$objeto2 = 'it'.$objeto;
			$edit->$objeto2 = new inputField("(<#o#>) Total", $objeto."_<#i#>");
			$edit->$objeto2->db_name  = $objeto;
			$edit->$objeto2->rel_id   = 'pacom';
			$edit->$objeto2->size     = 10;
			$edit->$objeto2->readonly = true;
			$edit->$objeto2->pointer  = true;
			$edit->$objeto2->css_class= 'inputnum';
			$edit->$objeto2->rule     = 'callback_positivo';
			//if($status == 'D2' || $status == 'D3')$edit->$objeto2->mode     = "autohide";
		}

		$status=$edit->get_from_dataobjetct('status');
		if($status=='F1'){
			$action = "javascript:window.location='" .site_url($this->url.'/actualizar/'.$edit->rapyd->uri->get_edited_id()). "'";
			$edit->button_status("btn_status",'Ordenar Pago',$action,"TR","show");
			$edit->buttons("delete");
			$action = "javascript:btn_anula('".$edit->rapyd->uri->get_edited_id()."')";
			if($this->datasis->puede(216))$edit->button_status("btn_anular",'Anular',$action,"TR","show");

			$edit->buttons("modify","save");
		}elseif($status=='F2'){
			$action = "javascript:window.location='" .site_url($this->url.'modconc/modify/'.$edit->rapyd->uri->get_edited_id()). "'";
			$edit->button_status("btn_moconc",'Modificar Concepto',$action,"TR","show");
			$action = "javascript:btn_anula('".$edit->rapyd->uri->get_edited_id()."')";
			if($this->datasis->puede(216))$edit->button_status("btn_anular",'Anular',$action,"TR","show");
		}elseif($status=='FA'){
			$edit->buttons("delete");
        }else{
		    $edit->buttons("save");
		}

		$edit->buttons("undo","back","add_rel","add");
		$edit->build();

		$smenu['link']   = barra_menu('120');
		$data['smenu']   = $this->load->view('view_sub_menu', $smenu,true);
		$conten["form"]  =&  $edit;
		$data['content'] = $this->load->view('view_opago', $conten,true);
		//$data['content'] = $edit->output;
		$data['title']   = "$this->tits";
		$data["head"]    = $this->rapyd->get_head().script('jquery.js').script("plugins/jquery.numeric.pack.js");
		$this->load->view('view_ventanas', $data);
	}

	function _valida($do){

		$this->rapyd->load('dataobject');

		$ocompra   =  new DataObject("ocompra");
		$ocompra   -> rel_one_to_many('pacom', 'pacom', array('numero'=>'compra'));

		$error ='';$tot=0;
		$tivaa =0;
		$tivag  =0;$tivar  =0;$ttivaa =0;$ttivag =0;$ttivar =0;$tmivaa =0;$tmivag =0;$tmivar =0;$tabo = 0;
		$anticipo = $do->get("anticipo");
		$status   = $do->get("status");

		if($this->datasis->traevalor('USA2COD_PROVENODIREC')=='S'){
			$cod_provv  = $do->get('cod_prov');
			$cod_prov2e = $this->db->escape($cod_provv);
			$cod_prov   = $this->datasis->dameval("SELECT cod_prov FROM sprv WHERE proveed=$cod_prov2e");

			if(!empty($cod_prov)){
					$do->set('cod_prov' ,$cod_prov);
					$do->set('cod_prov2',$cod_provv);
			}else{
					$do->set('cod_prov',$cod_provv);
			}
		}

		$subt=$rti=$t2=$t=$impt=$impm=0;$ban=0;$bsta='';$aa=$reten2=$otrasrete2=0;
		for($i=0;$i < $do->count_rel('pacom');$i++){

			$id         = $do->get_rel('pacom','id'       ,$i);
			$compra     = $do->get_rel('pacom','compra'   ,$i);
			$monto      = $do->get_rel('pacom','monto'    ,$i);

			$tot+=$monto;

			$ocompra ->load($compra);

			$tivaa +=$ivaa    = $ocompra->get('ivaa');
			$tivag +=$ivag    = $ocompra->get('ivag');
			$tivar +=$ivar    = $ocompra->get('ivar');
			$ttivaa+=$tivaa   = $ocompra->get('tivaa');
			$ttivag+=$tivag   = $ocompra->get('tivag');
			$ttivar+=$tivar   = $ocompra->get('tivar');
			$tmivaa+=$mivaa   = $ocompra->get('mivaa');
			$tmivag+=$mivag   = $ocompra->get('mivag');
			$tmivar+=$mivar   = $ocompra->get('mivar');
			$subt+=$subtotal  = $ocompra->get('subtotal');
			$rti+=$reteiva    = $ocompra->get('reteiva');
			$reten2+=$reten   = $ocompra->get('reten');

			$ivan             = $ivag+$ivar+$ivaa;
			$t2+=$total2      = $ocompra->get('total2' );
			$t+=$total        = $ocompra->get('total'  );
			$stat             = $ocompra->get('status' );
			$impm            += $ocompra->get('impmunicipal');
			$impt            += $ocompra->get('imptimbre'   );
			$tabo+=$abonado   = $ocompra->get('abonado');

			$otrasrete2+=$otrasrete   = $ocompra->get('otrasrete');

			if($stat != $bsta){
				$bsta = $stat;
				$ban++;
			}

			$aa+=$a = $total-($abonado);
			if($monto > $a ){
				$error.="<div class='alert'><p>No se puede abonar mas de lo adeudado ($a)  para la orden de compra ($compra)</p></div>";
			}

			if($monto <= 0 && $abonado=0){
				$error.="<div class='alert'><p>El monto de cero(0) no es v&aacute;lido para la orden de compra ($compra)</p></div>";
				$error.="<div class='alert'><p>El monto adeudado para la orden de compra ($compra) es de $a</p></div>";
			}

			if($anticipo=="N" && ($a!=$monto) ){
				$error.="<div class='alert'><p>El monto debe ser igual a la orden de compra para la numero:$compra </p></div>";
			}
		}
		if($ban>1)
			$error.="<div class='alert'><p>No se puede Mezclar Ordenes de Compra Comprometidas con Ordenes Causadas</p></div>";

			//echo $anticipo."</br>".$aa."</br>".$tot;
			if(empty($error)){
				//if($anticipo=="N"){
					if($t == $tabo && $anticipo=="N")$t = 0;
				//echo "</br>".$t;
				//echo "</br>".$tabo;
				//echo "</br>".$anticipo;
				//echo "</br>".$status;
					//exit();
					$observa = $do->get('observa');

					$patrones = array();
					$patrones[0] = '/\n/';
					$sustituciones = array();
					$sustituciones[0] = '';
					$observa = preg_replace($patrones, $sustituciones, $observa);

					$do->set('observa'      , $observa );
					$do->set('subtotal'     , $subt    );
					$do->set('total2'       , $t2      );
					$do->set('reteiva'      , $rti     );
					$do->set('total'        , $t       );
					$do->set('status'       , 'F1'     );
					$do->set('ivaa'         ,$tivaa    );
					$do->set('ivag'         ,$tivag    );
					$do->set('ivar'         ,$tivar    );
					$do->set('tivaa'        ,$ttivaa   );
					$do->set('tivag'        ,$ttivag   );
					$do->set('tivar'        ,$ttivar   );
					$do->set('mivaa'        ,$tmivaa   );
					$do->set('mivag'        ,$tmivag   );
					$do->set('mivar'        ,$tmivar   );
					$do->set('reten'        ,$reten2   );
					$do->set('impmunicipal' ,$impm     );
					$do->set('imptimbre'    ,$impt     );
					$do->set('otrasrete'    ,$otrasrete2);


				//}else{
				//	$do->set('subtotal'     , 0         );
				//	$do->set('reteiva'      , 0         );
				//	$do->set('total2'       , $tot      );
				//	$do->set('total'        , $tot      );
				//	$do->set('status'       , 'H1'      );
				//}
			}

		$numero = $do->get('numero');
		if(empty($error) && empty($do->loaded)){
			if(empty($numero)){
				if($this->datasis->traevalor('USANODIRECT')=='S'){
					$nodirect = $this->datasis->fprox_numero('nodirect');
					$do->set('numero',$nodirect);
					$do->pk=array('numero'=>$nodirect);
				}else
					$error.="Debe introducir un numero de orden de pago</br>";
			}elseif($this->datasis->traevalor('USANODIRECT')!='S'){
				$numeroe = $this->db->escape($numero);
				$chk     = $this->datasis->dameval("SELECT COUNT(*) FROM odirect WHERE numero=$numeroe");
				if($chk>0)
					$error.="Error el numero de orden de pago ya existe</br>";
			}
		}

		if(!empty($error)){
			$do->error_message_ar['pre_ins']=$error;
			$do->error_message_ar['pre_upd']=$error;
			return false;
		}
	}

	function debe(){
		$this->rapyd->load('dataobject');

		$id=$this->input->post('id');

		$ocompra   =  new DataObject("ocompra");
		$ocompra   -> rel_one_to_many('pacom', 'pacom', array('numero'=>'compra'));

		$ocompra ->load($id);

		$total     =  $ocompra->get('total');
		$abonado   =  $ocompra->get('abonado');

		echo $total-$abonado;
	}

	function _positivo($valor){
		if ($valor <= 0){
			$this->validation->set_message('itmonto',"El campo monto debe ser positivo");
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
		$cod_prov2   = $this->input->post('cod_prov2');
		$orden       = $this->db->escape($orden);
		if(empty($cod_prov2)){
		    $cana=$this->datasis->dameval("SELECT COUNT(*) FROM ocompra WHERE cod_prov=$cod_prov AND numero=$orden");
		    $c=$cod_prov;
		}else{
		    $cod_prov2=$this->db->escape($cod_prov2);
		    $cana=$this->datasis->dameval("SELECT COUNT(*) FROM ocompra WHERE cod_prov=$cod_prov2 AND numero=$orden");
		    $c=$cod_prov2;
		}
		if($cana>0){
			return true;
		}else{
			$this->validation->set_message('itorden',"La orden %s ($orden) No pertenece al proveedor ($c)");
			return false;
		}
	}

	function actualizar($id){

		$this->rapyd->load('dataobject');

		$odirect  = new DataObject("odirect");
		$odirect -> rel_one_to_many('pacom', 'pacom', array('numero'=>'pago'));
		$odirect -> load($id);

		$ocompra =  new DataObject("ocompra");
		$ocompra -> rel_one_to_many('pacom'    , 'pacom'     ,array('numero'=>'compra'));
		$ocompra -> rel_one_to_many('itocompra', 'itocompra' ,array('numero'=>'numero'));

		$error  = "";

		$sta=$odirect->get('status');
		if(($sta=="F1")){
		 	$ivan=0;$importes=array(); $ivas=array(); $ordenes=array();
		 	$p_ivaa=$p_ivag=$p_ivar=$p_reteiva=$p_reten=$p_total=$p_exento=0;
			for($i=0;$i   < $odirect->count_rel('pacom');$i++){
				$compra     = $odirect->get_rel('pacom','compra' ,$i);
				$ordenes[]  = $compra;

				$ocompra ->load($compra);

				$status    =  $ocompra->get('status');
				if($status != 'T')
					$error.="<div class='alert'><p>No se puede ordenar el pago de la orden de compra (".str_pad($compra,8,'0',STR_PAD_LEFT).") debido a que no esta causada</p></div>";

				if(empty($error)){
					$p_ivaa      += $ivaa           =  $ocompra->get('ivaa');
					$p_ivag      += $ivag           =  $ocompra->get('ivag');
					$p_ivar      += $ivar           =  $ocompra->get('ivar');
					$p_reteiva   += $reteiva        =  $ocompra->get('reteiva');
					$p_reten     += $reten          =  $ocompra->get('reten');
					$p_exento    += $exento         =  $ocompra->get('exento');
					$reteiva_prov   =  $ocompra->get('reteiva_prov');
					$creten         =  $ocompra->get('creten');
					$status         =  $ocompra->get('status');
					$total          =  $ocompra->get('total' );
					$subtotal       =  $ocompra->get('subtotal');
					//$ivan           =  $ivag+$ivar+$ivaa;
					$ivan=0;$admfondo=array();
					for($j=0;$j    < $ocompra->count_rel('itocompra');$j++){
						$codigoadm   = $ocompra->get_rel('itocompra','codigoadm',$j);
						$fondo       = $ocompra->get_rel('itocompra','fondo'    ,$j);
						$codigopres  = $ocompra->get_rel('itocompra','partida'  ,$j);
						$importe     = $ocompra->get_rel('itocompra','importe'  ,$j);
						$iva         = $ocompra->get_rel('itocompra','iva'      ,$j);
						$ordinal     = $ocompra->get_rel('itocompra','ordinal'  ,$j);
						$ivan        = $importe *$iva/100;

						$error.=$this->itpartida($codigoadm,$fondo,$codigopres,$ordinal);

						$cadena = $codigoadm.'_._'.$fondo.'_._'.$codigopres;
						if(array_key_exists($cadena,$importes)){
							$importes[$cadena]+=$importe;
							//$ivas[$cadena]     =$iva;
						}else{
							$importes[$cadena]  =$importe;
							//$ivas[$cadena]      =$iva;
						}
						$cadena2 = $codigoadm.'_._'.$fondo;
						$admfondo[$cadena2]=(array_key_exists($cadena2,$admfondo)?$admfondo[$cadena2]+=$ivan:$admfondo[$cadena2] =$ivan);
					}
				}
			}
			//print_r($importes);
			//print_r($ivas);

			if(empty($error)){
				//foreach($admfondo AS $cadena=>$monto){
				//	$temp  = explode('_._',$cadena);
				//	$error.=$this->chequeapresup($temp[0],$temp[1],'PARTIDAIVA','',$monto,0,'round($monto,2) > $disponible=round(($causado-$opago),2)','El Monto ($monto) es mayor al disponible (round(($disponible),2)) para ordenar pago para la partida de IVA, ('.$temp[0].')('.$temp[1].') ');
				//}

				foreach($importes AS $cadena=>$monto){
					$temp  = explode('_._',$cadena);
					$iva   = $ivas[$cadena];
					$error.=$this->chequeapresup($temp[0],$temp[1],$temp[2],'',$monto,'','round($monto,2) > $disponible=round(($causado-$opago),2)','El Monto ($monto) es mayor al posible a ordenar pago ($disponible) para la partida ('.$temp[0].' ('.$temp[1].') ('.$temp[2].')');
				}
			}

			if(empty($error)){
				foreach($importes AS $cadena=>$monto){
					$temp  = explode('_._',$cadena);
					//$iva   = $ivas[$cadena];
					$error.=$this->afectapresup($temp[0],$temp[1],$temp[2],'',$monto,0, 1 ,array("opago"));
				}

				//if(empty($error)){
				//	foreach($admfondo AS $cadena=>$monto){
				//		$temp  = explode('_._',$cadena);
				//		$error.=$this->afectapresup($temp[0],$temp[1],'PARTIDAIVA','',$monto,0, 1 ,array("opago"));
				//	}
				//}
			}

			if(empty($error)){
				$odirect->set('ivag'    , $p_ivag    );
				$odirect->set('ivar'    , $p_ivar    );
				$odirect->set('ivaa'    , $p_ivaa    );
				$odirect->set('reten'   , $p_reten   );
				$odirect->set('creten'  , $creten    );
				$odirect->set('reteiva' , $p_reteiva );
				$odirect->set('exento'  , $p_exento  );
				$odirect->set('status'  , 'F2'       );
				$odirect->set('fopago'  , date('Ymd'));

				$odirect->save();
				$ordenes = implode("','",$ordenes);
				$this->db->simple_query("UPDATE ocompra SET status='O' WHERE numero IN ('$ordenes')");
			}

		}elseif($sta=="H1"){

			for($i=0;$i   < $odirect->count_rel('pacom');$i++){
				$compra     = $odirect->get_rel('pacom','compra' ,$i);
				$monto      = $odirect->get_rel('pacom','monto'  ,$i);

				$ocompra ->load($compra);

				$status    =  $ocompra->get('status' );
				$total     =  $ocompra->get('total'  );
				$abonado   =  $ocompra->get('abonado');
				if($monto > $total-$abonado)
					$error.="<div class='alert'><p>El monto adeudado para la orden de Compra (".str_pad($compra,8,'0',STR_PAD_LEFT).") es menor al monto a abonar en la orden de pago</p></div>";

				if($status != 'C')
					$error.="<div class='alert'><p>No se pueder ordenar el pago de la orden de compra (".str_pad($compra,8,'0',STR_PAD_LEFT).") debido a que no esta comprometida</p></div>";
			}

			if(empty($error)){
				for($i=0;$i   < $odirect->count_rel('pacom');$i++){
					$compra     = $odirect->get_rel('pacom','compra' ,$i);
					$monto      = $odirect->get_rel('pacom','monto'  ,$i);

					$ocompra ->load($compra);

					$status    =  $ocompra->get('status');
					$abonado   =  $ocompra->get('abonado');

					$this->db->simple_query("UPDATE ocompra SET abonado=abonado+$monto WHERE numero=$compra");

					//$ocompra->save();
				}
			}

			$odirect->set('status' , 'H2' );
			$odirect->set('fopago'  , date('Ymd'));
			$odirect->save();
		}else{
			$error.="<div class='alert'><p>No se Puede Completar la operacion s</p></div>";
		}


		if(empty($error)){
			logusu('opago',"Actualizo Orden de Pago (de Compra) Nro $id");
			redirect($this->url."dataedit/show/$id");
		}else{
			logusu('opago',"Actualizo Orden de Pago (de Compra) Nro $id. con ERROR:$error ");
			$data['content'] = $error.anchor($this->url."/dataedit/show/$id",'Regresar');
			$data['title']   = " ".$this->tits." ";
			$data["head"]    = $this->rapyd->get_head().script('jquery.js').script("plugins/jquery.numeric.pack.js");
			$this->load->view('view_ventanas', $data);
		}
	}

	function _post($do){
		$id = $do->get('numero');
		redirect($this->url."actualizar/$id");
	}

	function concepto(){
		$numero  = $this->input->post('numero');
		$numeroe=$this->db->escape($numero);

		echo $this->datasis->dameval("SELECT observa FROM ocompra WHERE numero = $numeroe");

	}

        function modconc($redirect='',$status='',$numero){
		$this->rapyd->load("dataobject","dataedit");

		$edit = new DataEdit($this->tits, "odirect");
		echo $redirect;
		if($redirect)
		$edit->back_url = site_url("presupuesto/odirect/dataedit/show/$numero");
		else
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
		$status=$do->get('status');
		$numero=$do->get('numero');
		if(substr($status,0,1)=='B')
		redirect("presupuesto/odirect/dataedit/show/$numero");
		else
		redirect($this->url."dataedit/show/$numero");
        }

        function _valida_update_mod($do){
            $status=$do->get('status');

            if(substr($status,1,1)!='2')
            $error.="No se puede cambiarel concepto para esta orden";

            if(!empty($error)){
                    $do->error_message_ar['pre_ins']=$error;
                    $do->error_message_ar['pre_upd']=$error;
                    return false;
            }
        }



	function reversarall(){
		$query = $this->db->query("SELECT * FROM odirect WHERE status = 'F2' ");
		$result = $query->result();
		 foreach ($result AS $items){
		 	$numero =$items->numero;
		 	$this->reversar($numero);
		 }
	}
	function actualizarall(){
		$query = $this->db->query("SELECT * FROM odirect WHERE status = 'F1' ");
		$result = $query->result();
		 foreach ($result AS $items){
		 $numero =$items->numero;
		 	$this->actualizar($numero);
		 }
	}
	function _post_insert($do){
		$numero = $do->get('numero');
		logusu('opago',"Creo Orden de Pago (de compras) Nro $numero");
		redirect($this->url."dataedit/show/$numero");
	}
	function _post_update($do){
		$numero = $do->get('numero');
		logusu('opago'," Modifico Orden de Pago (de compras) Nro $numero");
		redirect($this->url."dataedit/show/$numero");
	}
	function _post_delete($do){
		$numero = $do->get('numero');
		logusu('opago'," Elimino Orden de Pago (de compras) Nro $numero");
	}

	function instalar(){
		$query="ALTER TABLE `pacom` CHANGE COLUMN `pago` `pago` VARCHAR(12) NULL DEFAULT NULL";
		$this->db->simple_query($query);
        $query="ALTER TABLE `odirect`  ADD COLUMN `tipoc` VARCHAR(2) NULL DEFAULT NULL";
        $this->db->simple_query($query);
		$query="ALTER TABLE `odirect`  ADD INDEX `numero` (`numero`)  ";
		$this->db->simple_query($query);
		$query="ALTER TABLE `pacom`    ADD INDEX `pago`   (`pago`)    ";
		$this->db->simple_query($query);
		$query="ALTER TABLE `pacom`    ADD INDEX `compra`   (`compra`)";
		$this->db->simple_query($query);
		$query="ALTER TABLE `ocompra`  ADD INDEX `numero` (`numero`)  ";
		$this->db->simple_query($query);
		$query="ALTER TABLE `sprv`     ADD INDEX `proveed` (`proveed`)";
		$this->db->simple_query($query);
		$query="ALTER TABLE `odirect`  ADD COLUMN `otrasrete` DECIMAL(19,2) NULL DEFAULT NULL";
		$this->db->simple_query($query);
		$query="ALTER TABLE `odirect`  ADD COLUMN `fanulado` DATE NULL DEFAULT NULL";
		$this->db->simple_query($query);
		$query="ALTER TABLE `odirect` ADD COLUMN `observacaj` TEXT NULL DEFAULT NULL";
		$this->db->simple_query($query);
		$query="ALTER TABLE `odirect` ADD COLUMN `fapagado` DATE NULL DEFAULT NULL AFTER `observacaj`";
		$this->db->simple_query($query);
		$query="ALTER TABLE `odirect` ADD COLUMN `facaduca` DATE NULL DEFAULT NULL AFTER `fapagado`";
		$this->db->simple_query($query);
	}
	
	function prueba(){
		echo date('Ymd');
		
		
	}
}
?>
