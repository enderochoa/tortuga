<?php
class R_abonos extends Controller {
	var $titp='Cobranza';
	var $tits='Cobranza';
	var $url ='recaudacion/r_abonos/';
	var $cajan='';
	function R_abonos(){
		parent::Controller();
		$this->load->library("rapyd");
		$this->datasis->modulo_id(428,1);
		$user   = $this->session->userdata('usuario');
		$usere  = $this->db->escape($user);
		$this->cajan   = $this->datasis->dameval("SELECT r_caja.nombre FROM r_caja JOIN  usuario ON r_caja.id=usuario.caja WHERE us_codigo =$usere");
		
	}
	function index(){
		redirect($this->url."filteredgrid");
	}
	
	function filteredgrid(){
		$this->rapyd->load('datafilter','datagrid');
		
		
		$mBANC=array(
				'tabla'   =>'banc',
				'columnas'=>array(
					'codbanc' =>'C&oacute;odigo',
					'banco' =>'Banco',
					'tbanco'=>'T Banco',
					'numcuent'=>'Cuenta',
					'saldo'=>'Saldo'),
				'filtro'  =>array(
					'codbanc' =>'C&oacute;odigo',
					'banco'=>'Banco',
					'tbanco'=>'T Banco',
					'saldo'=>'Saldo'),
				'retornar'=>array(
					'codbanc'=>'codbanc' ),
				'where'=>'activo = "S"',
				'titulo'  =>'Buscar Bancos'
				);

		$bBANC=$this->datasis->p_modbus($mBANC,"banc");

		$filter = new DataFilter($this->titp);
		$filter->db->select(array('b.abono id','c.nombre','GROUP_CONCAT(c.numero SEPARATOR " ") numero'
		,'c.nombre','c.rifci','c.fecha','GROUP_CONCAT(c.id SEPARATOR " ") id_recibo','GROUP_CONCAT(d.cheque SEPARATOR " ") cheque','GROUP_CONCAT(IF(d.tipo_doc="EF","Efectivo",IF(d.tipo_doc="DP","Deposito",IF(d.tipo_doc="DB","T. Debito",IF(d.tipo_doc="CR","T. Credito",IF(d.tipo_doc="DF","Diferencia","")))))) tipo_doc'
		,'d.monto monto_banco'
		,'c.monto monto_recibo'));
		$filter->db->from('r_abonosit b' );
		$filter->db->join('r_recibo c','b.recibo=c.id'  );
		$filter->db->join('r_mbanc d' ,'b.abono=d.abono');
		$filter->db->groupby('b.abono'                  );
		
		$user          = $this->session->userdata('usuario');
		$usere         = $this->db->escape($user);
		$r_caja        = $this->datasis->damerow("SELECT r_caja.id,punto_codbanc FROM r_caja JOIN  usuario ON r_caja.id=usuario.caja WHERE us_codigo =$usere");
		if(count($r_caja)>0){
			$caja          = $r_caja['id'];
			$punto_codbanc = $r_caja['punto_codbanc'];
		}else{
			$caja=0;
		}
		if($caja>0){
				$filter->db->where('c.caja',$caja);
		}
		
		$filter->numero = new inputField('Recibo','numero');
		$filter->numero->rule      ='max_length[11]';
		$filter->numero->size      =13;
		$filter->numero->clause    ='where';
		$filter->numero->operator  ='=';
		$filter->numero->group     ='Datos Recibo';
		
		$filter->id2 = new inputField('Ref Recibo','id2');
		$filter->id2->rule      ='max_length[11]';
		$filter->id2->size      =13;
		$filter->id2->maxlength =11;
		$filter->id2->db_name="c.id";
		$filter->id2->clause    ='where';
		$filter->id2->operator  ='=';
		$filter->id2->group     ='Datos Recibo';
		
		$filter->nombre = new inputField('Nombre','nombre');
		$filter->nombre->size      =20;
		$filter->nombre->group     ="Contribuyente";
		$filter->nombre->group     ='Datos Recibo';

		$filter->rifci = new inputField('Rif/Ced','rifci');
		$filter->rifci->rule      ='max_length[13]';
		$filter->rifci->size      =15;
		$filter->rifci->maxlength =13;
		$filter->rifci->group     ="Contribuyente";
		$filter->rifci->group     ='Datos Recibo';
		
		$filter->monto = new inputField('Monto Recibo','monto');
		$filter->monto->rule      ='max_length[13]';
		$filter->monto->size      =15;
		$filter->monto->maxlength =13;
		$filter->monto->db_name   ="c.monto";
		$filter->monto->group     ='Datos Recibo';
		
		
		$filter->id = new inputField('Ref','id');
		$filter->id->rule      ='max_length[11]';
		$filter->id->size      =13;
		$filter->id->maxlength =11;
		$filter->id->db_name   ="b.abono";
		$filter->id->clause    ='where';
		$filter->id->operator  ='=';
		$filter->id->group     ='Datos Bancarios';
		
		$filter->codbanc = new inputField("Banco", 'codbanc');
		$filter->codbanc->size = 6;
		$filter->codbanc->append($bBANC);
		$filter->codbanc->db_name="d.codbanc";
		$filter->codbanc->group     ='Datos Bancarios';
		
		$filter->fecha = new dateonlyField('Fecha','fecha');
		$filter->fecha->db_name   ="d.fecha";
		//$filter->fecha->rule      ='chfecha';
		$filter->fecha->size      =10;
		$filter->fecha->maxlength =8;
		$filter->fecha->clause    ='where';
		$filter->fecha->operator  ='=';
		$filter->fecha->group     ='Datos Bancarios';
		
		$filter->tipo_doc = new dropdownField("Tipo Documento","tipo_doc");
		$filter->tipo_doc->db_name   = 'd.tipo_doc';
		$filter->tipo_doc->style     ="width:130px;";
		$filter->tipo_doc->option(""  ,"");
		$filter->tipo_doc->option("EF","Efectivo");
		$filter->tipo_doc->option("DP","Deposito"       );
		$filter->tipo_doc->option("DB","Tarjeta D&eacute;bito");
		$filter->tipo_doc->option("CR","Tarjeta Credito");
		$filter->tipo_doc->option("DF","Diferencia");
		
		$filter->cheque = new inputField("Transaccion", "cheque");
		$filter->cheque->db_name="d.cheque";
		$filter->cheque->size  =10;
		$filter->cheque->group     ='Datos Bancarios';
		
		$filter->montob = new inputField('Monto Banco','montob');
		$filter->montob->rule      ='max_length[13]';
		$filter->montob->size      =15;
		$filter->montob->maxlength =13;
		$filter->montob->db_name   ="d.monto";
		$filter->montob->group     ='Datos Bancarios';
		
		$filter->buttons('reset', 'search');
		$filter->build();

		$uri = anchor($this->url.'dataedit/show/<raencode><#id#></raencode>','<#id#>');

		$grid = new DataGrid('');
		$grid->order_by('b.abono ','desc');
		$grid->per_page = 40;

		$grid->column_orderby('Ref.'                              ,"$uri"                   ,'id'            ,'align="left"' );
		$grid->column_orderby('Recibo'                            ,"numero"                 ,'numero'        ,'align="left"' );
		$grid->column_orderby('Ref Recibo'                        ,"id_recibo"              ,'id_recibo'     ,'align="left"' );
		$grid->column_orderby('Nombre'                            ,"nombre"                 ,'nombre'        ,'align="left"' );
		$grid->column_orderby('Documento'                         ,"tipo_doc"               ,'tipo_doc'      ,'align="left"' );
		$grid->column_orderby('Transacciones'                     ,"cheque"                 ,'cheque'        ,'align="left"' );
		$grid->column_orderby('Monto de un Recibo'                ,"monto_recibo"           ,'c.monto'       ,'align="right"');
		$grid->column_orderby('Monto de una Transaccion Bancaria' ,"monto_banco"            ,'d.monto'       ,'align="right"');
		
		$action = "javascript:window.location='" .site_url('recaudacion/r_recibo/filteredgrid'). "'";
		$grid->button("ir_cobranza","Ir a Recibos",$action,"TL");
		
		$grid->add($this->url.'dataedit/create');
		$grid->build();

		$data['filtro']  = $filter->output;
		$data['content'] = $grid->output;
		$data['head']    = $this->rapyd->get_head().script('jquery.js');
		$data['title']   = $this->titp.($this->cajan?" CAJA $this->cajan":"");
		$this->load->view('view_ventanas', $data);
	}
	
	function dataedit($recibo=null){
		$this->rapyd->load('datadetails','dataobject');
		$this->load->helper('form');

		$mBANC=array(
				'tabla'   =>'banc',
				'columnas'=>array(
					'codbanc' =>'C&oacute;odigo',
					'tbanco'=>'T Banco',
					'banco'=>'Banco',
					'numcuent'=>'Cuenta',
					'saldo'=>'Saldo'),
				'filtro'  =>array(
					'tbanco'=>'T Banco',
					'banco'=>'Banco',
					'numcuent'=>'Cuenta'
					),
				'p_uri'=>array(
				  4=>'<#i#>'),
				'retornar'=>array(
					'codbanc'=>'codbanc_<#i#>'
					 ),
				'where'=>'activo = "S" ',
				//'script'=>array('ultimoch(<#i#>)','cal_nombrech(<#i#>)'),
				'titulo'  =>'Buscar Bancos');
		$bBANC=$this->datasis->p_modbus($mBANC,"<#i#>");
		
		$mRECIBO=array(
				'tabla'   =>'r_v_xcobrar',
				'columnas'=>array(
					'id'       =>'Ref.',
					'numero'   =>'Numero',
					'fecha'    =>'Fecha',
					'monto'    =>'Monto',
					'rifci'    =>'RIF/CI',
					'nombre'   =>'Nombre'
					),
				'filtro'  =>array(
					'id'       =>'Ref.',
					'numero'   =>'Numero',
					'fecha'    =>'Fecha',
					'monto'    =>'Monto',
					'rifci'    =>'RIF/CI',
					'nombre'   =>'Nombre'
					),
				'p_uri'=>array(
				  4=>'<#i#>'),
				'retornar'=>array(
					'id'                              =>'recibo_<#i#>',
					'numero'                          =>'numerop_<#i#>',
					'DATE_FORMAT(fecha,"%d/%m/%Y")'   =>'fechap_<#i#>',
					'monto'                           =>'montop_<#i#>',
					'nombre'                          =>'nombrep_<#i#>'
					),
				'script'=>array('cal_totr()'),
				'titulo'  =>'Buscar Recibos por Pagar');
		$bRECIBO=$this->datasis->p_modbus($mRECIBO,"<#i#>");

		$ABONOCODBANCDEFECTO = $this->datasis->traevalor('ABONOCODBANCDEFECTO');
		$user          = $this->session->userdata('usuario');
		$usere         = $this->db->escape($user);
		$r_caja        = $this->datasis->damerow("SELECT r_caja.id,punto_codbanc,defecto_codbanc FROM r_caja JOIN  usuario ON r_caja.id=usuario.caja WHERE us_codigo =$usere");

		if(count($r_caja)>0){
			$caja                = $r_caja['id'];
			$punto_codbanc       = $r_caja['punto_codbanc'];
			if(strlen($r_caja['defecto_codbanc'])>0)
				$ABONOCODBANCDEFECTO = $r_caja['defecto_codbanc'];
		}else{
			$caja=0;
			$punto_codbanc='';
		}

		$do = new DataObject("r_abonos");
		$do->rel_one_to_many('r_abonosit', 'r_abonosit', array('id'=>'abono'));
		$do->rel_one_to_many('r_mbanc'   , 'r_mbanc'   , array('id'=>'abono'));
		$do->rel_pointer('r_abonosit' ,'r_recibo' ,'r_abonosit.recibo=r_recibo.id'  ,'r_recibo.numero AS numerop,r_recibo.fecha AS fechap,r_recibo.monto AS montop,r_recibo.nombre AS nombrep,r_recibo.id AS idp','LEFT');

		$edit = new DataDetails($this->tits, $do);
		$edit->back_url = site_url($this->url."filteredgrid");
		$edit->set_rel_title('r_abonosit','Rubro <#o#>');
		$edit->set_rel_title('r_mbanc','Rubro <#o#>');

		$edit->back_url = site_url($this->url."filteredgrid");

		$edit->pre_process('insert' ,'_valida');
		$edit->pre_process('update' ,'_valida');
		$edit->pre_process('delete' ,'_pre_delete' );
		$edit->post_process('insert','_post_insert');
		$edit->post_process('update','_post_update');
		$edit->post_process('delete','_post_delete');

		$edit->id = new inputField('id','id');
		$edit->id->rule='max_length[11]';
		$edit->id->size =13;
		$edit->id->maxlength =11;
		$edit->id->mode='autohide';
		$edit->id->when=array('show','modify');
		
		$edit->totrecibos =  new inputField("Total Recibos", 'totrecibos');
		$edit->totrecibos-> size     = 10;
		$edit->totrecibos->readonly  =true;
		$edit->totrecibos->css_class ='inputnum';
		
		$edit->totmbanc =  new inputField("Total Bancos", 'totmbanc');
		$edit->totmbanc-> size     = 10;
		$edit->totmbanc->readonly  =true;
		$edit->totmbanc->css_class ='inputnum';

		$edit->estampa = new autoUpdateField('estampa' ,date('Ymd'), date('Ymd'));

/******** RECIBOS *************/
		$edit->itrecibo =  new inputField("(<#o#>) Ref. Recibo", 'recibo_<#i#>');
		$edit->itrecibo->db_name   = 'recibo';
		$edit->itrecibo->size      = 5;
		$edit->itrecibo->rel_id    ='r_abonosit';
		//$edit->itrecibo->readonly  =true;
		$edit->itrecibo->append($bRECIBO);
		$edit->itrecibo->rule      ='required';  

		$edit->itnumerop =  new inputField("(<#o#>) Numero", 'numerop_<#i#>');
		$edit->itnumerop->db_name   = 'numerop';
		$edit->itnumerop->size      = 10;
		$edit->itnumerop->rel_id    ='r_abonosit';
		$edit->itnumerop->pointer   =true;
		$edit->itnumerop->readonly  =true;
		
		$edit->itfechap =  new inputField("(<#o#>) Fecha", 'fechap_<#i#>');
		$edit->itfechap->db_name   = 'fechap';
		$edit->itfechap-> size     = 10;
		$edit->itfechap->rel_id    ='r_abonosit';
		$edit->itfechap->pointer   =true;
		$edit->itfechap->readonly  =true;
		
		$edit->itmontop =  new inputField("(<#o#>) Monto", 'montop_<#i#>');
		$edit->itmontop->db_name   = 'montop';
		$edit->itmontop->size      = 10;
		$edit->itmontop->rel_id    ='r_abonosit';
		$edit->itmontop->pointer   =true;
		$edit->itmontop->readonly  =true;
		$edit->itmontop->value     =0;
		$edit->itmontop->rule      ='numeric';
		$edit->itmontop->css_class ='inputnum';
		
		$edit->itnombrep =  new inputField("(<#o#>) Nombre", 'nombrep_<#i#>');
		$edit->itnombrep->db_name   = 'nombrep';
		$edit->itnombrep-> size     = 30;
		$edit->itnombrep->rel_id    ='r_abonosit';
		$edit->itnombrep->pointer   =true;
		$edit->itnombrep->readonly  =true;

/****** CHEQUES *********************/
		
		$edit->itcodbanc =  new inputField("(<#o#>) Banco", 'codbanc_<#i#>');
		$edit->itcodbanc->db_name   = 'codbanc';
		$edit->itcodbanc-> size     = 4;
		$edit->itcodbanc->rel_id    ='r_mbanc';
		$edit->itcodbanc->rule      = "required|callback_banco";
		$edit->itcodbanc->append($bBANC);
		$edit->itcodbanc->value     = $ABONOCODBANCDEFECTO;

		$edit->ittipo_doc = new dropdownField("(<#o#>) Tipo Documento","tipo_doc_<#i#>");
		$edit->ittipo_doc->db_name   = 'tipo_doc';
		$edit->ittipo_doc->rel_id    ='r_mbanc';
		$edit->ittipo_doc->style     ="width:130px;";
		
		if($this->datasis->traevalor('ABONOS_EF_DEFECTO')=='S'){
			$edit->ittipo_doc->option("EF","Efectivo");
			$edit->ittipo_doc->option("DP","Deposito"       );
			$edit->ittipo_doc->option("DB","Tarjeta D&eacute;bito");
			$edit->ittipo_doc->option("CR","Tarjeta Credito");
			$edit->ittipo_doc->option("DF","Diferencia");
		}else{
			$edit->ittipo_doc->option("DP","Deposito"       );
			$edit->ittipo_doc->option("DB","Tarjeta D&eacute;bito");
			$edit->ittipo_doc->option("CR","Tarjeta Credito");
			$edit->ittipo_doc->option("DF","Diferencia");
			$edit->ittipo_doc->option("EF","Efectivo");
		}
		
		$edit->itcheque =  new inputField("(<#o#>) Transacci&oacute;n", 'cheque_<#i#>');
		$edit->itcheque->db_name   ='cheque';
		$edit->itcheque->size      = 20;
		$edit->itcheque->rel_id    ='r_mbanc';

		$edit->itfecha = new  dateonlyField("(<#o#>) Fecha Cheque",  "fecha_<#i#>");
		$edit->itfecha->db_name     ='fecha';
		$edit->itfecha->size        =10;
		$edit->itfecha->rel_id      ='r_mbanc';
		$edit->itfecha->insertValue = date('Ymd');
		$edit->itfecha->rule        ='required';

		$edit->itmonto = new inputField("(<#o#>) Total", 'monto_<#i#>');
		$edit->itmonto->db_name   ='monto';
		$edit->itmonto->size      = 10;
		$edit->itmonto->rel_id    ='r_mbanc';
		$edit->itmonto->css_class ='inputnum';
		$edit->itmonto->onchange  = "cal_totm();";
		$edit->itmonto->value     =0;
		
		$edit->itid_mbancrel = new hiddenField('Id','id_mbancrel<#i#>');
		$edit->itid_mbancrel->rel_id ='r_mbanc';
		$edit->itid_mbancrel->db_name='id_mbancrel';
		
/**************** POR COBRAR ******************************************/
		
		
		if($caja>0){
			$query="select a.id AS id,a.id_contribu AS id_contribu,a.fecha AS fecha,a.numero AS numero,a.rifci AS rifci,a.nombre AS nombre,a.telefono AS telefono,a.monto AS monto,a.id_parroquia AS id_parroquia,a.parroquia AS parroquia,a.id_zona AS id_zona,a.zona AS zona,a.dir1 AS dir1,a.dir2 AS dir2,a.dir3 AS dir3,a.dir4 AS dir4,a.razon AS razon,a.solvencia AS solvencia,a.solvenciab AS solvenciab,a.licores AS licores,a.caja AS caja 
			from 
			r_recibo a 
			left join r_abonosit b on a.id = b.recibo
			where isnull(b.recibo) AND a.caja=$caja
			ORDER BY id desc";
		}else{
			$query="select a.id AS id,a.id_contribu AS id_contribu,a.fecha AS fecha,a.numero AS numero,a.rifci AS rifci,a.nombre AS nombre,a.telefono AS telefono,a.monto AS monto,a.id_parroquia AS id_parroquia,a.parroquia AS parroquia,a.id_zona AS id_zona,a.zona AS zona,a.dir1 AS dir1,a.dir2 AS dir2,a.dir3 AS dir3,a.dir4 AS dir4,a.razon AS razon,a.solvencia AS solvencia,a.solvenciab AS solvenciab,a.licores AS licores,a.caja AS caja 
			from 
			r_recibo a 
			left join r_abonosit b on a.id = b.recibo
			where isnull(b.recibo)
			ORDER BY id desc";
		}
		
		

		$porcobrar=$this->db->query($query);
		$porcobrar=$porcobrar->result_array();
		foreach($porcobrar as $key=>$value)
		$porcobrar[$key]['fecha']=dbdate_to_human($value['fecha']);

		$edit->button_status("btn_add_sfpa"     ,'Agregar Pago',"javascript:add_r_mbanc()","MB",'modify',"button_add_rel");
		$edit->button_status("btn_add_sfpa2"    ,'Agregar Pago',"javascript:add_r_mbanc()","MB",'create',"button_add_rel");
		$edit->button_status("btn_add_itabonos" ,'Agregar Recibo',"javascript:add_r_abonosit()","PA",'modify',"button_add_rel");
		$edit->button_status("btn_add_itabonos2",'Agregar Recibo',"javascript:add_r_abonosit()","PA",'create',"button_add_rel");
		
		if($this->datasis->puede(429))
		$edit->buttons('modify');
		
		if($this->datasis->puede(430))
		$edit->buttons('delete');
		
		$edit->buttons('add', 'save', 'undo', 'back');
		
		$action = "javascript:location.href='" .site_url('recaudacion/r_recibo/dataedit/create'). "'";
		$edit->button("add_r_recibo","Agregar Recibo",$action,"TL");
		
		$edit->build();
		
		$conten["recibo"]              =$recibo;
		$conten["form"]                =&$edit;		
		$conten['porcobrar']           =$porcobrar;
		$conten["punto_codbanc"]       =$punto_codbanc;
		$conten["ABONOCODBANCDEFECTO"] =$ABONOCODBANCDEFECTO;
		$conten['porcobrarj']=json_encode($porcobrar);
		$data['content']    = $this->load->view('recaudacion/r_abonos'  , $conten,true);
		$data['title']      = $this->tits.($this->cajan?" CAJA $this->cajan":"");
		$data["head"]    = $this->rapyd->get_head().script('jquery.js').script('jquery-ui.js').script("plugins/jquery.numeric.pack.js").script('plugins/jquery.meiomask.js').style('vino/jquery-ui.css');
		$this->load->view('view_ventanas', $data);

	}
	
	function _valida($do){
		$error='';
		$t=0;
		
		$recibosid=array();
		$id = $do->get('id');
		$m=0;
		for($i=0;$i<$do->count_rel('r_abonosit');$i++){
			$recibo   =$do->get_rel('r_abonosit','recibo'  ,$i);
			$m=$this->input->post('montop_'.$i);
			$fecha=$this->input->post('fechap_'.$i);
			$fechae=$this->db->escape($fecha);
			$t+=$m;
			
			$fechadb= $this->datasis->dameval("SELECT fecha FROM r_recibo WHERE id=$recibo");
			$fechadbe=$this->db->escape($fechadb);
			$cerrado     = $this->datasis->dameval("SELECT COUNT(*) FROM r_cerrar WHERE fecha=REPLACE($fechadbe,'-','')");
			
			
			if($cerrado>0)
			$error.="<div class='alert' >Error. El Dia ".dbdate_to_human($fecha)." ya se encuetra Cerrado para el recibo $recibo</div>";
			
			if(in_array($recibo,$recibosid)){
				$error.="ERROR. esta cobrando dos vences el mismo recibo Ref ($recibo)</br>";
			}else{
				$recibosid[]=$recibo;
			}
			
			$query="SELECT COUNT(*) FROM r_abonosit WHERE recibo=$recibo";
			if($id>0)
			$query.=" AND abono<>$id";
			$cant = $this->datasis->dameval($query);
			if($cant>0)
			$error.="ERROR. El Recibo ya esta cobrado Ref ($recibo)</br>";
		}
		
		$t2=$m2=0;
		for($i=0;$i<$do->count_rel('r_mbanc');$i++){
			//$m2=$this->input->post('monto_'.$i);
			
			$cheque    =$do->get_rel('r_mbanc','cheque'  ,$i);
			$tipo_doc  =$do->get_rel('r_mbanc','tipo_doc',$i);
			$codbanc   =$do->get_rel('r_mbanc','codbanc' ,$i);
			$fecha     =$do->get_rel('r_mbanc','fecha'   ,$i);
			$m2        =$do->get_rel('r_mbanc','monto'   ,$i);
			
			$t2+=$m2;
			$chequee   =$this->db->escape($cheque);
			$fechadbe  =$this->db->escape($fecha);
			
			//$cerrado     = $this->datasis->dameval("SELECT COUNT(*) FROM r_cerrar WHERE fecha=REPLACE($fechadbe,'-','')");
			//if($cerrado>0)
			//$error.="<div class='alert' >Error. El Dia ".dbdate_to_human($fecha)." ya se encuetra Cerrado para el Movimiento Bancario para $codbanc $tipo_doc $chequee</div>";
			
			if(strlen($cheque)>0){
				$codbance  = $this->db->escape($codbanc );
				$chequee   = $this->db->escape($cheque  );
				$tipo_doce = $this->db->escape($tipo_doc);
			
				$query ="SELECT COUNT(*) FROM r_mbanc WHERE codbanc=$codbance AND tipo_doc=$tipo_doce AND cheque=$chequee ";
				if($id>0)
				$query.=" AND abono<>$id";
				
				$c = 1*$this->datasis->dameval($query);
				
				if($c>0)
				$error.="ERROR. El $tipo_doce numero $chequee del $codbance ya esta registrado ";
			}
		}
		
		//echo "$t t </br>";
		if(round($t,2)<>round($t2,2))
		$error.="ERROR. Los Montos en Bancos y de recibos son diferentes";
		
		$do->set('totrecibos',$t );
		$do->set('totmbanc'  ,$t2);
		
		if(!empty($error)){
			$do->error_message_ar['pre_ins']="<div class='alert'>".$error."</div>";
			$do->error_message_ar['pre_upd']="<div class='alert'>".$error."</div>";
			return false;
		}
	}
	
	function _pre_delete($do){
		$error="";
		$id      = $do->get('id');

		$query="
		SELECT fecha FROM (
			SELECT b.fecha FROM r_abonosit a JOIN r_recibo b ON a.recibo=b.id WHERE a.abono=$id 
		)todo GROUP BY fecha
		";
		
		$query = $this->db->query($query);
		$query = $query->result();
		foreach($query as $row){
			$fechae  = $this->db->escape($row->fecha);
 			$cerrado = $this->datasis->dameval("SELECT COUNT(*) FROM r_cerrar WHERE fecha=REPLACE($fechae,'-','')");
			if($cerrado>0)
			$error.="<div class='alert' >Error. El Dia ".dbdate_to_human($row->fecha)." ya se encuetra Cerrado</div>";
		}

		if(!empty($error)){
			$error.="</br>".anchor($this->url."dataedit/show/$id","Ver Cobranza $id");
			$do->error_string=$error;
			$do->error_message_ar['pre_del']=$error;
			return false;
		}
	}
	
	function existe($codbanc,$tipo_doc,$cheque){
		$error='';
		$codbance=$this->db->escape($codbanc);
		$chequee =$this->db->escape($cheque);
		if(in_array($tipo_doc,array('DP'))){
			$c=$this->datasis->dameval("SELECT COUNT(*) FROM sfpa WHERE codbanc=$codbance AND cheque=$chequee AND tipo_doc='$tipo_doc'");
			if($c>0)
			$error.="Error. el $tipo_doc del Banco $codbanc ya existe";
		}
		return $error;
	}
	
	function damerecibojson(){
		$id = $this->input->post('numero');
		
		$porcobrar=array();
		if($id){
			$ide       = $this->db->escape($id);
			$porcobrar =$this->db->query("
			select a.id,a.numero,a.fecha,a.monto,a.nombre 
			from 
			r_recibo a 
			left join r_abonosit b on a.id = b.recibo
			where isnull(b.recibo) AND a.id=$ide
			ORDER BY id desc 			
			");
			$porcobrar =$porcobrar->result_array();
			foreach($porcobrar as $key=>$value)
			$porcobrar[$key]['fecha']=dbdate_to_human($value['fecha']);
		}
		
		echo json_encode($porcobrar);
	}
	
	function _post_save($do){
		$id = $do->get('id');
		
		$this->db->query("
		UPDATE recibo a
		JOIN itabonos b ON a.id=b.recibo
		SET a.`status`='P' 
		WHERE b.abono=$id");
		
		for($i=0;$i<$do->count_rel('itabonos');$i++){
			$recibo=$do->get_rel('itabonos','recibo',$i);
			
			$this->db->where('id', $recibo);
			$this->db->update('recibo', array('status'=>'C'));
		}
	}
	
	function _post_del($do){
		for($i=0;$i<$do->count_rel('itabonos');$i++){
			$recibo=$do->get_rel('itabonos','recibo',$i);
			$this->db->where('id', $recibo);
			$this->db->update('recibo', array('status'=>'P'));
		}
	}

	function _post_insert($do){
		$this->_post_save($do);
		$primary =implode(',',$do->pk);
		logusu($do->table,"Creo $this->tits $primary ");
	}
	function _post_update($do){
		$this->_post_save($do);
		$primary =implode(',',$do->pk);
		logusu($do->table,"Modifico $this->tits $primary ");
	}
	function _post_delete($do){
		$this->_post_del($do);
		$primary =implode(',',$do->pk);
		logusu($do->table,"Elimino $this->tits $primary ");
	}

	function instalar(){
		$mSQL="CREATE TABLE `r_abonos` (
			`id` int(11) NOT NULL AUTO_INCREMENT,
			`estampa` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
			PRIMARY KEY (`id`)
		      ) ENGINE=MyISAM DEFAULT CHARSET=utf8";
		$this->db->simple_query($mSQL);
		
		$query="
		CREATE TABLE `r_abonosit` (
			`id` INT(11) NOT NULL AUTO_INCREMENT,
			`abono` INT(11) NOT NULL,
			`recibo` INT(11) NULL DEFAULT NULL,
			PRIMARY KEY (`id`)
		)
		COLLATE='utf8_general_ci'
		ENGINE=MyISAM
		AUTO_INCREMENT=1";
		$this->db->simple_query($mSQL);
		

		$query="
		CREATE TABLE `r_mbanc` (
			`id` INT(11) NOT NULL AUTO_INCREMENT,
			`abono` INT(11) NOT NULL,
			`codmbanc` INT(11) NOT NULL,
			`codbanc` VARCHAR(10) NOT NULL,
			`tipo_doc` CHAR(2) NOT NULL,
			`cheque` TEXT NOT NULL,
			`monto` DECIMAL(19,2) NOT NULL,
			`fecha` DATE NOT NULL,
			`observa` TEXT NOT NULL,
			PRIMARY KEY (`id`)
		)
		COLLATE='utf8_general_ci'
		ENGINE=MyISAM
		AUTO_INCREMENT=1";
		$this->db->simple_query($query);
		
		$query="ALTER TABLE `r_abonos`	ADD COLUMN `totrecibos` DECIMAL(19,2) NULL DEFAULT '0'";
		$this->db->simple_query($query);
		$query="ALTER TABLE `r_abonos`  ADD COLUMN `totmbanc` DECIMAL(19,2) NULL DEFAULT '0' ";
		$this->db->simple_query($query);
		
		$query="CREATE TABLE `r_abonosit` (
			`id` INT(11) NOT NULL AUTO_INCREMENT,
			`abono` INT(11) NOT NULL,
			`recibo` INT(11) NULL DEFAULT NULL,
			PRIMARY KEY (`id`)
		)
		COLLATE='utf8_general_ci'
		ENGINE=MyISAM
		AUTO_INCREMENT=1";
		$this->db->simple_query($query);		
		$query="ALTER TABLE `r_mbanc` ADD COLUMN `id_mbancrel` INT NULL DEFAULT NULL";
		$this->db->simple_query($query);
		$query="ALTER TABLE `r_mbanc` ADD INDEX `id_mbancrel` (`id_mbancrel`)";
		$this->db->simple_query($query);
		
		$query="ALTER TABLE `r_mbanc` ADD COLUMN `id_mbanc` INT(11) NULL DEFAULT NULL ";
		$this->db->simple_query($query);
		$query="ALTER TABLE `r_abonos` ADD COLUMN `fecha` DATE NULL DEFAULT NULL";
		$this->db->simple_query($query);
	}

}
?>
