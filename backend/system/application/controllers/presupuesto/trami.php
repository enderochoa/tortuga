<?php
require_once(BASEPATH.'application/controllers/presupuesto/common.php');
class Trami extends Common {

	var $titp  = 'Constancia de Tramitaci&oacute;n';
	var $tits  = 'Constancia de Tramitaci&oacute;n';
	var $url   = 'presupuesto/trami/';

	function trami(){
		parent::Controller();
		$this->load->library("rapyd");
		$this->formatopres =$this->datasis->traevalor('FORMATOPRES');
		$this->flongpres   =strlen(trim($this->formatopres));
		$this->datasis->modulo_id(324,1);
	}

	function index(){
		redirect($this->url."filteredgrid");
	}

	function filteredgrid(){
		$this->rapyd->load("datafilter","datagrid");

		$filter = new DataFilter("");
		$filter->db->select(array("a.status","a.numero numero","a.compromiso compromiso","a.fecha fecha","a.concepto concepto","a.monto monto","cod_prov"));
		$filter->db->from("trami a");

		$filter->compromiso = new inputField("Compromiso","compromiso");		
		$filter->compromiso->size =12;
	
		$filter->fecha = new dateonlyField("Fecha", "fecha");
		$filter->fecha->size=12;
		$filter->fecha->dbformat = "Y-m-d";

		$filter->buttons("reset","search");

		$filter->build();
		$uri = anchor($this->url.'dataedit/show/<#numero#>','<str_pad><#numero#>|8|0|STR_PAD_LEFT</str_pad>');

		$grid = new DataGrid("");
		$grid->order_by("numero","desc");
		$grid->per_page = 20;
		$grid->use_function('substr','str_pad','sta');

		$grid->column_orderby("N&uacute;mero"    ,$uri                                               ,"numero"                        );
		$grid->column_orderby("Compromiso"       ,"compromiso"                                       ,"compromiso"   ,"align='left'"  );
		$grid->column_orderby("Fecha"            ,"<dbdate_to_human><#fecha#></dbdate_to_human>"     ,"fecha"        ,"align='center'");
		$grid->column_orderby("Concepto"         ,"concepto"                                         ,"concepto"     ,"align='left'"  );
		$grid->column_orderby("Monto"            ,"<number_format><#monto#>|2|,|.</number_format>"   ,"monto"        ,"align='right'" );
		$grid->column_orderby("Estado"           ,"status"                                           ,"status"       ,"align='left'" );
		
		if($this->datasis->puede(331))
		$grid->add($this->url."dataedit/create");
		$grid->build();

		//$data['content'] = $filter->output.$grid->output;
		$data['filtro']  = $filter->output;
		$data['content'] = $grid->output;
		$data['script']  = script("jquery.js")."\n";
		$data['title']   = "$this->titp";
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
			'rif'=>'Rif',
			'contacto'=>'Contacto'),
			'filtro'  =>array('proveed'=>'C&oacute;digo','nombre'=>'Nombre','rif'=>'Rif'),
			'retornar'=>array('proveed'=>'cod_prov'),
			'titulo'  =>'Buscar Beneficiario');
		
		$bSPRV =$this->datasis->p_modbus($mSPRV ,"sprv");

		$modbus=array(
			'tabla'   =>'v_presaldo',
			'columnas'=>array(
				'codigoadm'   =>'Est. Admin',
				//'fondo'       =>'F. Financiamiento',
				'codigo'      =>'Partida',
				'ordinal'     =>'Ordinal',
				'denominacion'=>'Denominaci&oacute;n',
				'saldo'       =>'Saldo'
				),
			'filtro'  =>array(
				'codigoadm'   =>'Est. Admin',
				//'fondo'       =>'F. Financiamiento',
				'codigo'      =>'Partida',
				'ordinal'     =>'Ord',
				'denominacion'=>'Denominaci&oacute;n'
				),
			'retornar'=>array(
				'codigoadm'   =>'codigoadm_<#i#>',
				'codigo'      =>'codigopres_<#i#>',
				'denominacion'=>'denominacion_<#i#>'),
			'where'=>'movimiento = "S" AND saldo>0 AND fondo=<#fondo#> AND codigo LIKE "4.%"',
			'p_uri'=>array(4=>'<#i#>',5=>'<#fondo#>'),
			'titulo'  =>'Busqueda de partidas');
		
		$btn=$this->datasis->p_modbus($modbus,'<#i#>/<#fondo#>');
		$btn='<img src="'.base_url().'assets/default/images/system-search.png"  alt="Busqueda de partidas" title="Busqueda de partidas" border="0" onclick="modbusdepen(<#i#>)"/>';

		$do = new DataObject("trami");
		$do->rel_one_to_many('ittrami', 'ittrami', array('numero'=>'numero'));
		$do->rel_pointer('ittrami','v_presaldo' ,'ittrami.codigoadm=v_presaldo.codigoadm AND ittrami.fondo=v_presaldo.fondo AND ittrami.codigopres=v_presaldo.codigo ',"v_presaldo.denominacion as denomi2");
//		$do->pointer('sprv' ,'sprv.proveed=trami.cod_prov ',"sprv.nombre nombrep");

		$edit = new DataDetails($this->tits, $do);
		$edit->back_url = site_url($this->url."filteredgrid");
		$edit->set_rel_title('ittrami','Rubro <#o#>');

		$edit->pre_process('insert'  ,'_valida');
		$edit->pre_process('update'  ,'_valida');
		//$edit->post_process('insert'  ,'_paiva');
		//$edit->post_process('update'  ,'_paiva');
		$edit->post_process('insert','_post_insert');
		$edit->post_process('update','_post_update');

		$edit->numero  = new inputField("N&uacute;mero", "numero");
		$edit->numero->mode="autohide";
		$edit->numero->when=array('show');

		$edit->compromiso = new inputField("Compromiso", 'compromiso');
		$edit->compromiso->size     = 10;

		$edit->status = new inputField("status", 'status');
		$edit->status->size     = 10;
		$edit->status->when=array('show');

		$edit->cod_prov = new inputField("Beneficiario", 'cod_prov');
		$edit->cod_prov->db_name  = "cod_prov";
		$edit->cod_prov->size     = 4;         
		$edit->cod_prov->rule     = "required";
		$edit->cod_prov->readonly =true;       
		$edit->cod_prov->append($bSPRV);       
		
		$edit->nombrep = new inputField("Nombre", 'nombrep');
		$edit->nombrep->size     = 20;
		$edit->nombrep->readonly = true;
		$edit->nombrep->pointer  = TRUE;
		$edit->nombrep->in       = "codprov";
		

		$edit->concepto = new textareaField("Concepto", 'concepto');
		$edit->concepto->rows     = 3;
		$edit->concepto->cols     = 50;
		
		$edit->fondo = new dropdownField("F. Financiamiento","fondo");
		$edit->fondo->rule   ='required';
		$edit->fondo->db_name='fondo';
		$edit->fondo->options("SELECT tipo,tipo a  FROM presupuesto WHERE tipo<>'' GROUP BY tipo ORDER BY tipo desc");
		$edit->fondo->style="width:300px;";
        
		$edit->fecha = new  dateonlyField("Fecha",  "fecha");
		$edit->fecha->insertValue = date('Y-m-d');
		$edit->fecha->size =12;
		if(!$this->datasis->puede(328))
		$edit->fecha->mode='autohide';
		
		$edit->fcomprome = new  dateonlyField("F. Compromiso",  "fcomprome");
		$edit->fcomprome->insertValue = date('Y-m-d');
		$edit->fcomprome->size =12;
		if(!$this->datasis->puede(327))
		$edit->fcomprome->mode='autohide';
		
		$edit->fpagado = new  dateonlyField("F. Pagado",  "fpagado");
		$edit->fpagado->insertValue = date('Y-m-d');
		$edit->fpagado->size        = 12;
		if(!$this->datasis->puede(330))
		$edit->fpagado->mode       ='autohide';

		$edit->monto = new inputField("Total", 'monto');
		$edit->monto->readonly=true;
		$edit->monto->size = 15;
		$edit->monto->rule     ='numeric';
		$edit->monto->css_class='inputnum';

		//detalles
		$edit->itcodigoadm = new inputField("(<#o#>) Partida", "codigoadm_<#i#>");
		$edit->itcodigoadm->size        =10;		
		$edit->itcodigoadm->db_name     ='codigoadm';
		$edit->itcodigoadm->rel_id      ='ittrami';
		$edit->itcodigoadm->autocomplete=false;

		$edit->itcodigopres = new inputField("(<#o#>) Partida", "codigopres_<#i#>");
		$edit->itcodigopres->rule        ='required';
		$edit->itcodigopres->size        =10;		
		$edit->itcodigopres->db_name     ='codigopres';
		$edit->itcodigopres->rel_id      ='ittrami';
		$edit->itcodigopres->append($btn);
		$edit->itcodigopres->autocomplete=false;
		
		$edit->itdenominacion = new inputField("(<#o#>) Denominacion", "denominacion_<#i#>");
		$edit->itdenominacion->db_name  ='denomi2';
		$edit->itdenominacion->size     = 58;
		$edit->itdenominacion->rel_id   ='ittrami';
		$edit->itdenominacion->pointer  =true;

		$edit->importe = new inputField("(<#o#>) Importe", "importe_<#i#>");
		$edit->importe->db_name  ='importe';
		$edit->importe->rel_id   ='ittrami';
		$edit->importe->size     =15;
		$edit->importe->rule     ='numeric|callback_positivo';
		$edit->importe->insertValue=0;
		$edit->importe->css_class='inputnum';
		$edit->importe->onchange ='cal_total();';

		$status=$edit->get_from_dataobjetct('status');
		$v=$t=0;
		switch($status){
			case 'P':{
				$action = "javascript:window.location='" .site_url($this->url.'/presup/'.$edit->rapyd->uri->get_edited_id()). "/C2'";
				if($this->datasis->puede(327))
				$edit->button_status("btn_status",'Comprometer',$action,"TR","show");
				$edit->buttons("modify","delete");
				break;
			}
			case 'C2':{
				$action = "javascript:window.location='" .site_url($this->url.'/presup/'.$edit->rapyd->uri->get_edited_id()). "/T2'";
				if($this->datasis->puede(328))
				$edit->button_status("btn_status",'Causar',$action,"TR","show");
				$action = "javascript:window.location='" .site_url($this->url.'/presup/'.$edit->rapyd->uri->get_edited_id()). "/C1'";
				if($this->datasis->puede(327))
				$edit->button_status("btn_status",'Reversar Compromiso',$action,"TR","show");
				break;
			}
			case 'T2':{
				$action = "javascript:window.location='" .site_url($this->url.'/presup/'.$edit->rapyd->uri->get_edited_id()). "/O2'";
				if($this->datasis->puede(329))
				$edit->button_status("btn_status",'Ordenado Pago',$action,"TR","show");
				$action = "javascript:window.location='" .site_url($this->url.'/presup/'.$edit->rapyd->uri->get_edited_id()). "/T1'";
				if($this->datasis->puede(328))
				$edit->button_status("btn_status",'Reversar Causado',$action,"TR","show");
				break;
			}
			case 'O2':{
				$action = "javascript:window.location='" .site_url($this->url.'/presup/'.$edit->rapyd->uri->get_edited_id()). "/E2'";
				if($this->datasis->puede(330))
				$edit->button_status("btn_status",'Pagar',$action,"TR","show");
				$action = "javascript:window.location='" .site_url($this->url.'/presup/'.$edit->rapyd->uri->get_edited_id()). "/O1'";
				if($this->datasis->puede(329))
				$edit->button_status("btn_status",'Reversar Ordenado Pago',$action,"TR","show");
				break;
			}
			case 'E2':{
				$action = "javascript:window.location='" .site_url($this->url.'/presup/'.$edit->rapyd->uri->get_edited_id()). "/E1'";
				if($this->datasis->puede(330))
				$edit->button_status("btn_status",'Reversar Pagado',$action,"TR","show");
				break;
			}
			case 'E1':{
				$action = "javascript:window.location='" .site_url($this->url.'/presup/'.$edit->rapyd->uri->get_edited_id()). "/E2'";
				if($this->datasis->puede(330))
				$edit->button_status("btn_status",'Pagar',$action,"TR","show");
				$action = "javascript:window.location='" .site_url($this->url.'/presup/'.$edit->rapyd->uri->get_edited_id()). "/O1'";
				if($this->datasis->puede(329))
				$edit->button_status("btn_status",'Reversar Ordenado pago',$action,"TR","show");
				break;
			}
			case 'T1':{
				$action = "javascript:window.location='" .site_url($this->url.'/presup/'.$edit->rapyd->uri->get_edited_id()). "/T2'";
				if($this->datasis->puede(328))
				$edit->button_status("btn_status",'Causar',$action,"TR","show");
				$action = "javascript:window.location='" .site_url($this->url.'/presup/'.$edit->rapyd->uri->get_edited_id()). "/C1'";
				if($this->datasis->puede(327))
				$edit->button_status("btn_status",'Reversar Compromiso',$action,"TR","show");
				break;
			}
			case 'O1':{
				$action = "javascript:window.location='" .site_url($this->url.'/presup/'.$edit->rapyd->uri->get_edited_id()). "/O2'";
				if($this->datasis->puede(329))
				$edit->button_status("btn_status",'Ordenar Pago',$action,"TR","show");
				$action = "javascript:window.location='" .site_url($this->url.'/presup/'.$edit->rapyd->uri->get_edited_id()). "/T1'";
				if($this->datasis->puede(328))
				$edit->button_status("btn_status",'Reversar Causado',$action,"TR","show");
				break;
			}
			case 'C1':{
				$action = "javascript:window.location='" .site_url($this->url.'/presup/'.$edit->rapyd->uri->get_edited_id()). "/C2'";
				if($this->datasis->puede(327))
				$edit->buttons("modify","delete");
				$edit->button_status("btn_status",'Comprometer',$action,"TR","show");
				break;
				$v = 'A';
				$t = 'ANULAR';
				break;
			}
		}
		
		if($status!='E2')
		$edit->buttons("modify");

		$edit->buttons("add","undo","back","add_rel","save");
		$edit->build();

		$smenu['link']=barra_menu('310');
		$data['smenu'] = $this->load->view('view_sub_menu', $smenu,true);
		$conten["form"]  =&  $edit;
		$data['content'] = $this->load->view('view_trami', $conten,true);
		//$data['content'] = $edit->output;
		$data['title']   = "$this->tits";
		$data["head"]    = $this->rapyd->get_head().script('jquery.js').script('jquery-ui.js').script("plugins/jquery.numeric.pack.js").script('plugins/jquery.meiomask.js').style('vino/jquery-ui.css');
		$this->load->view('view_ventanas', $data);
	}

	function _valida($do){
		$error   ='';
		$siva    =$ssub=$total=0;
		$fondo   = $do->get('fondo');
		$status  = $do->get('status');
			
		for($i=0;$i < $do->count_rel('ittrami');$i++){
			$do->set_rel('ittrami','fondo' ,$fondo       ,$i);
			$importe      = $do->get_rel('ittrami','importe'    ,$i);
			$codigoadm    = $do->get_rel('ittrami','codigoadm'  ,$i);
			$codigopres   = $do->get_rel('ittrami','codigopres' ,$i);
			$total       += $importe;
		
			$error.=$this->itpartida($codigoadm,$fondo,$codigopres);
		}
		
		if(!empty($error)){
			$do->error_message_ar['pre_upd']=$error;
			$do->error_message_ar['pre_ins']=$error;				
			return false;		
		}
		if(empty($status))
		$do->set('status'    ,    'P' );
		$do->set('monto'     ,    $total );
	}
	
	function presup($id,$accion){
		$this->rapyd->load('dataobject');
		
		$do = new DataObject("trami");
		$do->rel_one_to_many('ittrami', 'ittrami', array('numero'=>'numero'));
		$do->load($id);
		
		$status   =$do->get('status');
		$error    ='';
		$factor   = 1;
		$formula  ='round($monto,2) > $disponible = ';
		
		switch($accion){
			case 'E2':{
				$factor  =1;
				$campo   ='pagado';
				$formula .='round($opago-$pagado,2)';
				if($status!='O2' && $status !='E1')
				$error  .='ERROR. No puede realizar la operacion para el documento en este momento.';
				break;
			}
			case 'O2':{
				$factor  =1;
				$campo   ='opago';
				$formula.='round($causado-$opago,2)';
				if($status!='T2' && $status!='O1')
				$error  .='ERROR. No puede realizar la operacion para el documento en este momento.';
				break;
			}
			case 'T2':{
				$factor  =1;
				$campo ='causado';
				$formula.='round$(comprometido-$causado,2)';
				if($status!='C2' && $status!='T1')
				$error  .='ERROR. No puede realizar la operacion para el documento en este momento.';
				break;
			}
			case 'C2':{
				$factor  =1;
				$campo   ='comprometido';
				$formula.='round($presupuesto-$comprometido,2)';
				if($status!='C1' && $status!='P')
				$error  .='ERROR. No puede realizar la operacion para el documento en este momento.';
				break;
			}
			case 'E1':{
				$factor  =-1;
				$campo   ='pagado';
				$formula .='round($opago-$pagado)';
				if($status!='E2' && $status!='O1')
				$error  .='ERROR. No puede realizar la operacion para el documento en este momento.';
				break;
			}
			case 'O1':{
				$factor  =-1;
				$campo   ='opago';
				$formula.='round($pagado-$opago,2)';
				if($status!='O2' && $status!='E1')
				$error  .='ERROR. No puede realizar la operacion para el documento en este momento.';
				break;
			}
			case 'T1':{
				$factor  =-1;
				$campo   ='causado';
				$formula.='round($opago-$causado,2)';
				if($status!='T2' && $status!='O1')
				$error  .='ERROR. No puede realizar la operacion para el documento en este momento.';
				break;
			}
			case 'C1':{
				$factor  =-1;
				$campo   ='comprometido';
				$formula.='round($comprometido-$causado,2)';
				if($status!='C2' && $status!='T1')
				$error  .='ERROR. No puede realizar la operacion para el documento en este momento.';
				break;
			}
		}
		
		if(empty($error)){
			
			for($i=0;$i       <   $do->count_rel('ittrami');$i++){
				$codigopres = $do->get_rel(  'ittrami','codigopres' ,$i);
				$monto      = $do->get_rel(  'ittrami','monto'      ,$i);
				$codigoadm  = $do->get_rel(  'ittrami','codigoadm'  ,$i);
				$fondo      = $do->get_rel(  'ittrami','fondo'      ,$i);
				
				$error.=$this->chequeapresup($codigoadm,$fondo,$codigopres,'',$monto,0,$formula,$formula);
			}
			
			if(empty($error)){
				if($accion=='C2'){
					if(strpos($id,'_')==0){
						$contador = $this->datasis->fprox_numero('ntrami');
						$do->set('numero',$contador);
						$id=$contador;
					}
				}
				
				for($i=0;$i < $do->count_rel('ittrami');$i++){
					if($accion=='C2'){
						$do->set_rel('ittrami','id'    ,''       ,$i);
						$do->set_rel('ittrami','numero',$contador,$i);
					}
					$codigopres = $do->get_rel('ittrami','codigopres' ,$i);
					$monto      = $do->get_rel('ittrami','monto'      ,$i);
					$codigoadm  = $do->get_rel('ittrami','codigoadm'  ,$i);
					$fondo      = $do->get_rel('ittrami','fondo'      ,$i);
					
					$error.=$this->afectapresup($codigoadm,$fondo,$codigopres,'',$monto,0, $factor ,array($campo));
				}
				if(empty($error)){
					$do->set('status',$accion);
					$do->save();
				}
			}
		}else{
			$error="<div class='alert'><p>$error</p></div>";
		}
		
		if(empty($error)){
			logusu('audis',"actualizo $campo numero $id");
			redirect($this->url."/dataedit/show/$id");
		}else{
			logusu('audis',"actualizo $campo numero $id con error $error");
			$data['content'] = $error.anchor($this->url."/dataedit/show/$id",'Regresar');
			$data['title']   = " Aumentos y Disminuciones ";
			$data["head"]    = $this->rapyd->get_head();
			$this->load->view('view_ventanas', $data);
		}
	}

	function positivo($valor){
		if ($valor <= 0){
			$this->validation->set_message('positivo',"El Subtotal debe ser positivo");
			return FALSE;
		}
		return TRUE;
	}
	
	
	function _post_insert($do){
		$numero     = $do->get('numero'  );
		logusu('trami',"Creo constancia de tramitacion $numero");
		//redirect($this->url."actualizar/$id");
	}
	
	function _post_update($do){
		$numero     = $do->get('numero'  );
		logusu('trami' ,"Modifico constancia de tramitacion $numero");
		//redirect($this->url."actualizar/$id");
	}
	
	function instalar(){
		$query="CREATE TABLE `trami` (
		`numero` INT(11) NOT NULL AUTO_INCREMENT,
		`compromiso` CHAR(12) NOT NULL,
		`fecha` DATE NOT NULL,
		`cod_prov` CHAR(5) NOT NULL,
		`concepto` VARCHAR(50) NOT NULL,
		`monto` DECIMAL(19,2) NOT NULL,
		PRIMARY KEY (`numero`)
		)
		COLLATE='utf8_general_ci'
		ENGINE=MyISAM
		ROW_FORMAT=DEFAULT
		AUTO_INCREMENT=1
		";
		$this->db->simple_query($query);
		
		$query="CREATE TABLE `ittrami` (
		`numero` INT(11) NULL DEFAULT NULL,
		`id` INT(11) NOT NULL AUTO_INCREMENT,
		`codigoadm` VARCHAR(12) NULL DEFAULT NULL,
		`fondo` VARCHAR(20) NULL DEFAULT NULL,
		`codigopres` VARCHAR(17) NULL DEFAULT NULL,
		`ordinal` CHAR(3) NULL DEFAULT NULL,
		`descripcion` VARCHAR(80) NULL DEFAULT NULL,
		`importe` DECIMAL(19,2) NULL DEFAULT NULL,
		PRIMARY KEY (`id`)
		)
		COLLATE='utf8_general_ci'
		ENGINE=MyISAM
		ROW_FORMAT=DEFAULT
		AUTO_INCREMENT=1
		";
		$this->db->simple_query($query);
		
		$query="ALTER TABLE `trami`  ADD COLUMN `status` CHAR(2) NOT NULL DEFAULT 'P'";
		$this->db->simple_query($query);
		$query="ALTER TABLE `trami`  ADD COLUMN `fondo` VARCHAR(20) NOT NULL";
		$this->db->simple_query($query);
		$query="ALTER TABLE `trami` ADD COLUMN `fcomprome` DATE NOT NULL ";
		$this->db->simple_query($query);
		$query="ALTER TABLE `trami` ADD COLUMN `fpagado` DATE NOT NULL";
		$this->db->simple_query($query);
	}
}
?>
