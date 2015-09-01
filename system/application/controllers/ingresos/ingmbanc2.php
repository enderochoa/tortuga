<?php
//octubre 150bs
//require_once(BASEPATH.'application/controllers/presupuesto/common.php');
class Ingmbanc2 extends Controller {

	var $url  = "ingresos/ingmbanc2/";
	var $tits = "Ingresos Diarios";
	var $titp = "Ingresos Diarios";

	function Ingmbanc2(){
		parent::Controller();
		$this->load->library("rapyd");
		//$this->datasis->modulo_id(223,1);
	}
	function index(){
		redirect($this->url."filteredgrid");
	}

	function filteredgrid(){
		//$this->datasis->modulo_id(24,1);
		$this->rapyd->load("datafilter","datagrid");
		$this->rapyd->uri->keep_persistence();
		$link=site_url('presupuesto/requisicion/getadmin');

		$filter = new DataFilter("");

		$filter->db->select(array("a.numero numero","a.fecha fecha","a.total total","concepto","status","recibo"));
		$filter->db->from("ingresos a");
		//$filter->db->join("uejecutora b" ,"a.uejecuta=b.codigo");

		$filter->numero = new inputField("N&uacute;mero", "numero");
		$filter->numero->size=15;

		$filter->recibo = new inputField("Recibo", "recibo");
		$filter->recibo->size=15;
		
		$filter->concepto = new inputField("Concepto", "concepto");
		$filter->concepto->size=15;		
		$filter->total = new inputField("Total", "total");
		$filter->total->size=15;

		$filter->fecha = new dateonlyField("Fecha", "fecha");
		$filter->fecha->size=12;
		$filter->fecha->clause='where';
		$filter->fecha->operator='=';

		$filter->status = new dropdownField("Estado","status");
		$filter->status->option("","");
		$filter->status->option("P","Pendiente");
		$filter->status->option("C","Terminado");
		$filter->status->option("A","Anulado"  );
		$filter->status->style="width:150px";

		$filter->buttons("reset","search");
		$filter->build();

		$uri  = anchor($this->url.'dataedit/show/<#numero#>'  ,'<str_pad><#numero#>|8|0|STR_PAD_LEFT</str_pad>');

		function sta($status){
			switch($status){
				case "P":return "Pendiente";break;
				case "C":return "Terminado";break;
				case "A":return "Anulado";break;
			}
		}

		$grid = new DataGrid("");
		$grid->order_by("numero","desc");
		$grid->per_page = 20;
		$grid->use_function('substr','str_pad','sta');

		$grid->column_orderby("N&uacute;mero"  ,$uri                                             ,"numero"    );
		$grid->column_orderby("Recibo"         ,"recibo"                                         ,"recibo"  ,"align='left'"   );
		$grid->column_orderby("Fecha"          ,"<dbdate_to_human><#fecha#></dbdate_to_human>"   ,"fecha"   ,"align='center'" );
		$grid->column_orderby("Concepto"       ,"<wordwrap><#concepto#>|50|\n|true</wordwrap>"                                       ,"concepto","align='left' "   );
		$grid->column_orderby("Total"          ,"<nformat><#total#></nformat>"                   ,"total"   ,"align='right' " );
		$grid->column_orderby("Estado"         ,"<sta><#status#></sta>"                          ,"status"  ,"align='center'" );

		$grid->add($this->url."dataedit/create");
		$grid->build();

		$data['filtro']  = $filter->output;
		$data['content'] = $grid->output;
		$data['title']   = $this->titp;
		$data['script']  = script("jquery.js");
		$data["head"]    = $this->rapyd->get_head();
		$this->load->view('view_ventanas', $data);
	}

	function dataedit(){
		//$this->datasis->modulo_id(115,1);
		$this->rapyd->load('dataobject','datadetails');

		//$this->rapyd->uri->keep_persistence();

		$modbus=array(
			'tabla'   =>'v_ingresos',
			'columnas'=>array(
				'codigo'      =>'C&oacute;digo',
				'denominacion'=>'Denominaci&oacute;n'),
			'filtro'  =>array('codigo'=>'C&oacute;digo','denominacion'=>'Denominacion'),
			'retornar'=>array('codigo'=>'itcodigopres_<#i#>','denominacion'=>'itdenomi_<#i#>'),//,'departa'=>'ccosto_<#i#>'
			'titulo'  =>'Buscar Cuenta Presupuestaria',
			'p_uri'   =>array(4=>'<#i#>'),
			);
		$btn   =$this->datasis->p_modbus($modbus,'<#i#>');

		$mBANC=array(
				'tabla'   =>'banc',
				'columnas'=>array(
					'codbanc' =>'C&oacute;odigo',
					'banco'=>'Banco',
					'numcuent'=>'Cuenta',
					'saldo'=>'Saldo'),
				'filtro'  =>array(
					'codbanc' =>'C&oacute;odigo',
					'banco'=>'Banco',
					'numcuent'=>'Cuenta',
					'saldo'=>'Saldo'),
				'p_uri'=>array(
				  4=>'<#i#>'),
				'retornar'=>array(
					'codbanc'=>'codbancm_<#i#>'
					 ),
				'where'=>'activo = "S" ',
				//'script'=>array('ultimoch(<#i#>)','cal_nombrech(<#i#>)'),
				'titulo'  =>'Buscar Bancos');
		$bBANC=$this->datasis->p_modbus($mBANC,"<#i#>");

		$modbus3=array(
			'tabla'   =>'v_mbanc',
	            	'columnas'=>array(
				'id'       =>'ID',
				'codbanc'  =>'Banco',
				'fecha'    =>'Fecha',
				'numcuent' =>'Cuenta',
				'banco'    =>'Denominacion',
				'observa'  =>'Concepto',
				'tipo_doc' =>'Tipo Doc.',
				'cheque'   =>'Nro. Documento',
				'monto'    =>'Monto'),
			'filtro'  =>array(
				'id'       =>'Ref.',
				'cheque'  =>'Nro. Documento',
				'monto'   =>'Monto',
				'tipo_doc'=>'Tipo',
				'codbanc' =>'Cod. Banco',
				'numcuent'=>'Cuenta',
				'banco'  =>'Denominacion',
				'observa'  =>'Concepto',
                                ),
			'retornar'=>array(
				'id'         =>'idm_<#i#>'         ,
				'codbanc'    =>'codbancm_<#i#>'    ,
				'tipo_doc'   =>'tipo_docm_<#i#>'   ,
				'cheque'     =>'chequem_<#i#>'     ,
				'fecha'      =>'fecham_<#i#>'      ,
				'benefi'     =>'benefim_<#i#>'     ,
				'observa'    =>'observam_<#i#>'    ,
				'monto'      =>'montom_<#i#>'      ,
				),
			'p_uri'=>array( 4=>'<#i#>'),
			'script'=>array('cal_totm()'),
			'where'=>'status IN ("A2","NC","J2") ',
			'titulo'  =>'Buscar Movimiento Bancario',
			);

		$v_mbanc=$this->datasis->p_modbus($modbus3,"<#i#>");
		
		$modbus4=array(
			'tabla'   =>'v_mbancm',
	            	'columnas'=>array(
				'multiple' =>'Multiple',
				'codbanc'  =>'Banco',
				'fecha'    =>'Fecha',
				'numcuent' =>'Cuenta',
				'monto'    =>'Monto',
				'banco'    =>'Denominacion',
				'observa'  =>'Concepto',
				'tipo_doc' =>'Tipo Doc.',
				'cheque'   =>'Nro. Documento'),
			'filtro'  =>array(
				'multiple'=>'Multiple',
				'cheque'  =>'Nro. Documento',
				'monto'   =>'Monto',
				'codbanc' =>'Cod. Banco',
				'numcuent'=>'Cuenta',
				'banco'   =>'Denominacion',
				'observa' =>'Concepto',
                                ),
			'retornar'=>array(
				'multiple'   =>'multiplem_<#i#>'   ,
				'codbanc'    =>'codbancm_<#i#>'    ,
				'tipo_doc'   =>'tipo_docm_<#i#>'   ,
				'cheque'     =>'chequem_<#i#>'     ,
				'fecha'      =>'fecham_<#i#>'      ,
				'benefi'     =>'benefim_<#i#>'     ,
				'observa'    =>'observam_<#i#>'    ,
				'monto'      =>'montom_<#i#>'      ,
				),
			'order_by'=>'multiple DESC',
			'p_uri'=>array( 4=>'<#i#>'),
			'script'=>array('cal_totm()'),
			'titulo'  =>'Buscar Movimiento Bancario',
			);

		$v_mbancm=$this->datasis->p_modbus($modbus4,"<#i#>");

		$do = new DataObject("ingresos");
		$do->rel_one_to_many('ingmbanc'  , 'ingmbanc'  , array('numero'=>'ingreso'));
		$do->rel_one_to_many('itingresos', 'itingresos', array('numero'=>'numero'));
		//$do->rel_pointer('ingmbanc','mbanc' ,'ingmbanc.codmbanc=mbanc.id',"mbanc.status statusm,mbanc.codbanc codbancm,mbanc.destino destinom,mbanc.tipo_doc tipo_docm,mbanc.cheque chequem,mbanc.fecha fecham,mbanc.monto montom,mbanc.benefi benefim,mbanc.observa observam");
		//$do->rel_pointer('itingresos','v_ingresos' ,'v_ingresos.codigo=itingresos.codigopres',"v_ingresos.denominacion as denomi");

		$edit = new DataDetails($this->tits, $do);
		$edit->back_url = site_url($this->url."filteredgrid");
		$edit->set_rel_title('itingresos','Rubro <#o#>');

		$status=$edit->get_from_dataobjetct('status');
		
		$edit->pre_process('insert'  ,'_valida');
		$edit->pre_process('update'  ,'_valida');

		$edit->post_process('insert','_post_insert');
		$edit->post_process('update','_post_update');
		$edit->post_process('delete','_post_delete');

		//**************************INICIO ENCABEZADO********************************************************************
		$edit->numero       = new inputField("N&uacute;mero", "numero");
		//$edit->numero->rule = "callback_chexiste";
		$edit->numero->mode="autohide";
		$edit->numero->when=array('show');

		$edit->fecha= new  dateonlyField("Fecha",  "fecha","d/m/Y");
		$edit->fecha->insertValue = date('Y-m-d');
		$edit->fecha->size        = 12;
		//$edit->fecha->mode        = "autohide";
		//$edit->fecha->when        =array('show');

		$edit->tipo  = new dropdownField("Tipo", "tipo");
		$edit->tipo->option('o','Ingreso Ordinario');
		$edit->tipo->option('e','Ingreso Extraordinario');
		$edit->tipo->option('r','Reversion de Pago');
		$edit->tipo->style="50px";
		$edit->tipo->when =array('show');
		
		$edit->tipod  = new dropdownField("Documento", "tipod");
		$edit->tipod->option('I','Ingreso');
		$edit->tipod->option('ND','Nota de Debito');
		$edit->tipod->option('NC','Nota de Credito');
		$edit->tipod->style="100px";
		
		$edit->recibo  = new inputField("Recibo", "recibo");
		$edit->recibo->size     =15;
		$edit->recibo->maxlnegth=50;

		$edit->fpago  = new inputField("Forma de Pago", "fpago");
		$edit->fpago->size     = 10;
		$edit->fpago->maxlnegth=50;

		$edit->npago  = new inputField("Numero de Pago", "npago");
		$edit->npago->size     = 10;

		$edit->recibido  = new inputField("Recibido Por", "recibido");
		$edit->recibido->size     = 60;
		
		$edit->planillas  = new textareaField("Planillas", "planillas");
		$edit->planillas->rows     = 2;
		$edit->planillas->cols     = 40;
		
		$edit->articulos  = new textareaField("Articulos", "articulos");
		$edit->articulos->rows     = 2;
		$edit->articulos->cols     = 40;
		
		$edit->quincena  = new dropdownField("", "quincena");
		$edit->quincena->option("","");
		$edit->quincena->option("Primera","Primera");
		$edit->quincena->option("Segunda","Segunda");
		$edit->quincena->style="80px";
		
		$edit->ano  = new dropdownField("A&ntilde;o", "ano");
		$edit->ano->option("","");
		for($i=2011;$i>=2000;$i--)
		$edit->ano->option($i,$i);
		$edit->ano->style="30px";
		
		$edit->mes  = new dropdownField("Mes", "mes");
		$edit->mes->option("","");
		for($i=1;$i<=12;$i++)
		$edit->mes->option(str_pad($i,2,'0',STR_PAD_LEFT),str_pad($i,2,'0',STR_PAD_LEFT));
		$edit->mes->style="30px";
		

		$edit->concepto  = new textareaField("Concepto", "concepto");
		$edit->concepto->rows     = 4;
		$edit->concepto->cols     = 90;

		$edit->total  = new inputField("Total", "total");
		$edit->total->size     = 10;
		$edit->total->readonly = true;
		$edit->total->css_class='inputnum';

		$edit->totalch  = new inputField("Total Movimientos Activos", "totalch");
		$edit->totalch->size     = 10;
		$edit->totalch->readonly = true;
		$edit->totalch->css_class='inputnum';

		$edit->tbruto  = new inputField("", "tbruto");
		$edit->tbruto->size     = 10;
		$edit->tbruto->readonly = true;
		$edit->tbruto->css_class='inputnum';

		$edit->tdcto  = new inputField("", "tdcto");
		$edit->tdcto->size     = 10;
		$edit->tdcto->readonly = true;
		$edit->tdcto->css_class='inputnum';

		//************************** FIN   ENCABEZADO********************************************************************

		//**************************INICIO DETALLE DE PRESUPUESTO  *****************************************************

		$edit->itcodigopres = new inputField("(<#o#>) ", "itcodigopres_<#i#>");
		//$edit->itcodigopres->rule     ='required';
		$edit->itcodigopres->size     =15;
		$edit->itcodigopres->db_name  ='codigopres';
		$edit->itcodigopres->rel_id   ='itingresos';
		if($status=='C')
		$edit->itcodigopres->readonly =true;
		else
		$edit->itcodigopres->append($btn);

		$edit->itdenomi= new textareaField("(<#o#>) Denominacion","itdenomi_<#i#>");
		//$edit->itdenomi->rule   ='required';
		$edit->itdenomi->db_name  ='denomi';
		$edit->itdenomi->rel_id   ='itingresos';
		//$edit->itdenomi->pointer  =true;
		$edit->itdenomi->rows     =1;
		$edit->itdenomi->cols     =25;
		$edit->itdenomi->readonly =true;

		$edit->itreferen1 = new inputField("(<#o#>) Inicio", 'itreferen1_<#i#>');
		$edit->itreferen1->db_name   ='referen1';
		$edit->itreferen1->size      = 10;
		$edit->itreferen1->rel_id    ='itingresos';
		if($status=='C')
		$edit->itreferen1->readonly =true;

		$edit->itreferen2 = new inputField("(<#o#>) Fin", 'itreferen2_<#i#>');
		$edit->itreferen2->db_name   ='referen2';
		$edit->itreferen2->size      = 10;
		$edit->itreferen2->rel_id    ='itingresos';
		if($status=='C')
		$edit->itreferen2->readonly  =true;

		$edit->itbruto = new inputField("(<#o#>) Monto Bruto", 'itbruto_<#i#>');
		$edit->itbruto->db_name   ='bruto';
		$edit->itbruto->size      = 10;
		//$edit->itbruto->rule      ='callback_positivo';
		$edit->itbruto->rel_id    ='itingresos';
		$edit->itbruto->css_class ='inputnum';
		$edit->itbruto->onchange = "cal_tot();";
		if($status=='C')
		$edit->itbruto->readonly =true;

		$edit->itdcto = new inputField("(<#o#>) Descuento", 'itdcto_<#i#>');
		$edit->itdcto->db_name   ='dcto';
		$edit->itdcto->size      = 10;
		$edit->itdcto->rule      ='callback_positivo';
		$edit->itdcto->rel_id    ='itingresos';
		$edit->itdcto->css_class ='inputnum';
		$edit->itdcto->onchange = "cal_tot();";
		if($status=='C')
		$edit->itdcto->readonly =true;

		$edit->itmonto = new inputField("(<#o#>) Monto", 'itmonto_<#i#>');
		$edit->itmonto->db_name   ='monto';
		$edit->itmonto->size      = 10;
		//$edit->itmonto->rule      ='callback_positivo';
		$edit->itmonto->rel_id    ='itingresos';
		$edit->itmonto->css_class ='inputnum';
		$edit->itmonto->onchange = "cal_tot();";
		if($status=='C')
		$edit->itmonto->readonly =true;

		//************************** FIN   DETALLE DE PRESUPUESTO  *****************************************************

		//************************** INICIO  DETALLE DE BANCOS  *****************************************************
		
		$edit->itmultiplem =  new inputField("(<#o#>) Multiple", 'multiplem_<#i#>');
		$edit->itmultiplem->db_name   = 'multiple';
		$edit->itmultiplem-> size     = 3;
		$edit->itmultiplem-> readonly = true;
		$edit->itmultiplem->rel_id    = 'ingmbanc';
		$edit->itmultiplem->append($v_mbancm);
		
		$edit->itidm =  new inputField("(<#o#>) Id", 'idm_<#i#>');
		$edit->itidm->db_name   = 'codmbanc';
		$edit->itidm-> size     = 3;
		$edit->itidm-> readonly = true;
		$edit->itidm->rel_id    = 'ingmbanc';
		$edit->itidm->append($v_mbanc);
		
		$edit->itstatusm =  new dropdownField("(<#o#>) Estado", 'statusm_<#i#>');
		if($edit->_status=='show')$edit->itstatusm->option("NC","Nota de Cr&eacute;dito"   );
		$edit->itstatusm->option("E1","Pendiente" );
		$edit->itstatusm->option("E2","Activo"    );
		$edit->itstatusm->option("AN","Anulado"   );
		$edit->itstatusm->option("A2","Anulado."  );
		$edit->itstatusm->db_name   = 'statusm';
		$edit->itstatusm-> size     = 3;
		$edit->itstatusm->rel_id    ='ingmbanc';
		$edit->itstatusm->style     ="width:100px;";
		$edit->itstatusm->onchange  = "cal_totalch();";
		$edit->itstatusm->when=array('show');
		$edit->itstatusm->pointer   = true;

		$edit->itcodbancm =  new inputField("(<#o#>) Banco", 'codbancm_<#i#>');
		$edit->itcodbancm->db_name   = 'codbanc';
		$edit->itcodbancm-> size     = 4;
		//$edit->itcodbancm-> readonly =true;
		$edit->itcodbancm->rel_id    ='ingmbanc';
		$edit->itcodbancm->rule      = "required|callback_banco";
		$edit->itcodbancm->append($bBANC);
		//$edit->itcodbancm->pointer   = true;

		$edit->ittipo_docm = new dropdownField("(<#o#>) Tipo Documento","tipo_docm_<#i#>");
		$edit->ittipo_docm->db_name   = 'tipo_doc';
		$edit->ittipo_docm->rel_id    ='ingmbanc';
		$edit->ittipo_docm->style     ="width:130px;";
		$edit->ittipo_docm->option("NC","Nota de Cr&eacute;dito");
		$edit->ittipo_docm->option("DP","Deposito"       );
		$edit->ittipo_docm->option("ND","Nota de D&eacute;bito" );
		$edit->ittipo_docm->option("CH","Cheque" );

		$edit->itchequem =  new textareaField("(<#o#>) Cheque", 'chequem_<#i#>');
		$edit->itchequem->db_name   ='cheque';
		$edit->itchequem->rows      = 2;
		$edit->itchequem->cols      = 10;
		$edit->itchequem->rel_id    ='ingmbanc';
		//$edit->itchequem->pointer   = true;
		//$edit->itchequem->readonly  =true;
		$edit->itchequem->rule      ='required';

		$edit->itfecham = new  dateonlyField("(<#o#>) Fecha Cheque",  "fecham_<#i#>");
		$edit->itfecham->db_name     ='fecha';
		$edit->itfecham->size        =10;
		$edit->itfecham->rel_id      ='ingmbanc';
		$edit->itfecham->insertValue = date('Ymd');
		//$edit->itfecham->pointer     = true;
		//$edit->itfecham->readonly =true;
		$edit->itfecham->rule      ='required';

		$edit->itmontom = new inputField("(<#o#>) Total", 'montom_<#i#>');
		$edit->itmontom->db_name   ='monto';
		//$edit->itmontom->mode      = 'autohide';
		//$edit->itmontom->when     = array('show');
		$edit->itmontom->size      = 10;
		$edit->itmontom->rel_id    ='ingmbanc';
		$edit->itmontom->css_class ='inputnum';
		//$edit->itmontom->pointer   = true;
		//$edit->itmontom->readonly  =true;
		$edit->itmontom->onchange = "cal_totm();";

		$edit->itbenefim = new inputField("(<#o#>) A Nombre de", 'benefim_<#i#>');
		$edit->itbenefim->db_name   = 'benefi';
		$edit->itbenefim->size      = 15;
		$edit->itbenefim->maxlenght = 40;
		$edit->itbenefim->rel_id    = 'ingmbanc';
		//$edit->itbenefim->pointer   = true;
		//$edit->itbenefim->readonly =true;

		$edit->itobservam = new textAreaField("(<#o#>) Observaciones", 'observam_<#i#>');
		$edit->itobservam->db_name   ='observa';
		$edit->itobservam->cols      = 20;
		$edit->itobservam->rows      = 1;
		$edit->itobservam->rel_id    ='ingmbanc';
		//$edit->itobservam->pointer   = true;
		//$edit->itobservam->readonly =true;

		//************************** FIN  DETALLE DE BANCOS  *****************************************************

		
		if($status=='P'){
			$action = "javascript:btn_anular('" .$edit->rapyd->uri->get_edited_id()."')";
			$edit->button_status("btn_anula",'Anular',$action,"TR","show");
			$action = "javascript:window.location='" .site_url($this->url.'recibo/modify/'.$edit->rapyd->uri->get_edited_id()). "'";
			$edit->button_status("btn_termina",'Marcar Ingreso como finalizado',$action,"TR","show");
			$edit->buttons("modify", "save","delete");
		}elseif($status=='C'){
			$edit->buttons("modify", "save");
			$action = "javascript:btn_anular('" .$edit->rapyd->uri->get_edited_id()."')";
			$edit->button_status("btn_anula",'Anular',$action,"TR","show");
		}elseif($status=='O'){
			$edit->buttons("modify", "save","delete");
		}elseif($status=='A'){
			$edit->buttons("delete");
		}
		
		$action = "javascript:window.location='" .site_url($this->url.'modconc/modify/'.$edit->rapyd->uri->get_edited_id()). "'";
		$edit->button_status("btn_moconc",'Modificar Concepto/Recibo',$action,"TR","show");

		$edit->button_status("btn_add_mbanc" ,'Agregar Deposito / Nota de Credito',"javascript:add_mbanc()","MB",'modify',"button_add_rel");
		$edit->button_status("btn_add_mbanc2",'Agregar Deposito / Nota de Credito',"javascript:add_mbanc()","MB",'create',"button_add_rel");
		$edit->button_status("btn_add_pades" ,'Agregar Rubro',"javascript:add_itingresos()","PA","create","button_add_rel");
		$edit->button_status("btn_add_pades2",'Agregar Rubro',"javascript:add_itingresos()","PA","modify","button_add_rel");

		$edit->buttons("save","undo", "back","add");
		$edit->build();

		$smenu['link']   = barra_menu('803');
		$data['smenu']   = $this->load->view('view_sub_menu', $smenu,true);
		$conten["form"]  =&  $edit;
		$data['content'] = $this->load->view('view_ingmbanc2', $conten,true);
		//$data['content'] = $edit->output;
		$data['title']   = $this->tits;
		$data["head"]    = $this->rapyd->get_head().script('jquery.js').script("plugins/jquery.numeric.pack.js").script("plugins/jquery.json.min.js");
		$this->load->view('view_ventanas', $data);
	}
	
	function modconc($status='',$numero){
		$this->rapyd->load("dataobject","dataedit");

		$edit = new DataEdit($this->tits, "ingresos");
		$edit->back_url = site_url($this->url."/dataedit/show/$numero");
		$edit->back_cancel=true;
		$edit->back_cancel_save=true;

		//$edit->pre_process('update'  ,'_valida_mod');
		$edit->post_process('update','_post_update_mod');

		$edit->numero  = new inputField("N&uacute;mero", "numero");
		$edit->numero->mode="autohide";
		
		$edit->recibo  = new inputField("Recibo", "recibo");
		//$edit->recibo->mode="autohide";

		$edit->concepto = new textAreaField("Concepto", 'concepto');
		$edit->concepto->cols = 70;
		$edit->concepto->rows = 3;

		$edit->buttons("undo","back","save");
		$edit->build();

		$data['content'] = $edit->output;
		$data['title']   = "Modificar Concepto de Orden de Pago";
		$data["head"]    = $this->rapyd->get_head().script('jquery.js').script("plugins/jquery.numeric.pack.js").script("plugins/jquery.json.min.js");
		$this->load->view('view_ventanas', $data);
	}
	
	 function _post_update_mod($do){
            $numero=$do->get('numero');
            redirect($this->url."dataedit/show/$numero");
        }

	function _valida($do){
		$error = '';
		$numero = $do->get('numero');
		$td     = $do->get('tipod');
		$status = $do->get('status');

		$total=$tbruto=$tdcto=0;$importes=array();
		$a=$do->get_all();
		$tipod=array();$tipod2='';
		for($i=0;$i < $do->count_rel('itingresos');$i++){
			$b=implode('',$a['itingresos'][$i]);
			
			$codigopres     = $do->get_rel('itingresos','codigopres',$i);
			$total  +=$monto= $do->get_rel('itingresos','monto'     ,$i);
			$tbruto +=$bruto= $do->get_rel('itingresos','bruto'     ,$i);
			$tdcto  +=$dcto = $do->get_rel('itingresos','dcto'      ,$i);
			$codigoprese=$this->db->escape($codigopres);

			$do->set_rel('itingresos','monto', ($bruto-$dcto) ,$i);

			if(strlen($b)>0){
				$c=$this->datasis->dameval("SELECT tipo FROM v_ingresos WHERE codigo=$codigoprese");
				if(strlen($c) <=0)$error.="La partida ($codigopres) no existe, o el tipo es erroneo</br>";
				
				$tipod[$c]=$c;
				$tipod2   =$c;
			}
			
			$cadena = $codigopres;
			if(array_key_exists($cadena,$importes)){
				//$error.='La partida ($codigopres) esta repetida';
			}else{
				$importes[$cadena]  =0;
			}
		}
		
		if(count($tipod)>1)
			$error.='No se pueden mezclar tipos de ingresos';
		
		if(empty($tipod2) && $td=='I' )
		$error.='La Clasificacion de Partidas es invalida';

		$totalch=0;
		$idms=array();
		for($i=0;$i < $do->count_rel('ingmbanc');$i++){
			$monto   = $this->input->post("montom_$i");
			$idms[]  = $this->input->post("idm_$i");
			$tipo_doc= $this->input->post("tipo_docm_$i");
		
			if($tipo_doc=='DP' || $tipo_doc=='NC')
			    $totalch+=$monto;
			else
			    $totalch-=$monto;
		}
		
		$idms=implode(',',$idms);
		$do->set('total'   ,$total   );
		$do->set('totalch' ,$totalch );

		switch($td){
			case 'I'  :{
				if(round(abs($total),2)<>round(abs($totalch),2))
				$error.="La Suma de los movimientos ($total) bancarios es distinta a la suma de los ingresos ($totalch)";
			}
			break;
			case 'ND' :{
				
				//if(abs($total)>0 && abs($total)<>abs($totalch))
				if(abs($total)<>abs($totalch))
				$error.="La Suma de los movimientos ($total) bancarios es distinta a la suma de los ingresos ($totalch)";
			}
			break;
			case 'NC' :{
				if(abs($total)>0 && abs($total)<>abs($totalch))
				if(abs($total)<>abs($totalch))
				$error.="La Suma de los movimientos ($total) bancarios es distinta a la suma de los ingresos ($totalch)";
			}
			break;
		}
		
		//3.02.303.001.002
		if(empty($error))
		$do->set('tipo',$tipod2);

		if(!empty($error)){
			$do->error_message_ar['pre_ins']=$error;
			$do->error_message_ar['pre_upd']=$error;
			return false;
		}else{
			if($status!='C')
			$do->set('status','P');
		}
	}

	function termina($numero){
		$this->rapyd->load('dataobject');

		$error='';

//		$dop = new DataObject("ingpresup");

		$do = new DataObject("ingresos");
		$do->rel_one_to_many('ingmbanc'  , 'ingmbanc'  , array('numero'=>'ingreso'));
		$do->rel_one_to_many('itingresos', 'itingresos', array('numero'=>'numero'));
		$do->rel_pointer('ingmbanc','mbanc' ,'ingmbanc.codmbanc=mbanc.id',"mbanc.status statusm,mbanc.codbanc codbancm,mbanc.destino destinom,mbanc.tipo_doc tipo_docm,mbanc.cheque chequem,mbanc.fecha fecham,mbanc.monto montom,mbanc.benefi benefim,mbanc.observa observam");
		$do->load($numero);

		$status = $do->get('status');

		if($status=='P'){
                    $ids=array();
                    for($i=0;$i < $do->count_rel('ingmbanc'); $i++){
                            $mstatus     = $do->get_rel_pointer('ingmbanc','statusm'    ,$i  );
                            $codbanc     = $do->get_rel_pointer('ingmbanc','codbancm'   ,$i  );
                            $tipo_doc    = $do->get_rel_pointer('ingmbanc','tipo_docm'  ,$i  );
                            $fecha       = $do->get_rel_pointer('ingmbanc','fecham'     ,$i  );
                            $monto       = $do->get_rel('ingmbanc','monto'     ,$i  );
                            $cheque      = $do->get_rel_pointer('ingmbanc','chequem'    ,$i  );
                            $mid         = $do->get_rel('ingmbanc','codmbanc'  ,$i  );
                            $staing      = $do->get_rel_pointer('ingmbanc','staing'     ,$i  );
                            $codbance    = $this->db->escape($codbanc );
                            $tipo_doce   = $this->db->escape($tipo_doc);
                            $chequee     = $this->db->escape($cheque  );

				$ids[]       = $mid;

                            //if($mstatus!='J2')
                            //$error.="Error, no se puede realizar la operacion para el movimiento $cheque</br>";

                            //$montoa=$this->datasis->dameval("SELECT SUM(a.monto) FROM ingmbanc a JOIN ingresos b ON a.ingreso=b.numero WHERE b.status='C' AND a.codbanc=$codbance AND a.tipo_doc=$tipo_doce AND a.cheque=$chequee");
                            //$montoa=($montoa == null ? 0 : $montoa);
                            //$montom=$this->datasis->dameval("SELECT monto FROM mbanc WHERE codbanc=$codbance AND tipo_doc=$tipo_doce AND cheque=$chequee LIMIT 1");

                            //if($monto+$montoa>$montom)
                            //$error.="Error, movimiento ($cheque) No se Puede abonar mas del monto del movimiento ($montom)</br>";
			    echo "paso";
                    }
		}else{
			$error.= "<div class='alert'>No se puede realizar la operacion para el Ingreso</div>";
		}

		if(empty($error)){
			for($i=0;$i <   $do->count_rel('itingresos');$i++){
				$codigopres   = $do->get_rel('itingresos','codigopres',$i);
				$monto        = $do->get_rel('itingresos','bruto'     ,$i);

				//$dop->load($codigopres);
				//$recaudado    = $dop->get('recaudado');
				//$dop->set('recaudado',$monto+$recaudado);
				//$dop->save();
			}
			$ids=implode("','",$ids);
			$this->db->query("UPDATE mbanc SET coding=NULL WHERE coding=$numero");
			$this->db->query("UPDATE mbanc SET staing='C',coding=$numero WHERE id IN ('$ids')");
			
		}
		
		if(empty($error)){
			$this->db->query("UPDATE ingresos SET status='C' WHERE numero=$numero");
			//$do->set('status','C');
			//$do->save();
			logusu('ingresos',"Marco como terminado Ingreso nro $numero");
			redirect($this->url."dataedit/show/$numero");
		}else{
			logusu('ingresos',"Marco como terminado ingreso nro $numero con ERROR $error");
			$data['content'] = '<div class="alert">'.$error.'</div></br>'.anchor($this->url."dataedit/show/$numero",'Regresar');
			$data['title']   = " $this->tits";
			$data["head"]    = $this->rapyd->get_head();
			$this->load->view('view_ventanas', $data);
		}
	}

	function anular($numero){
		$this->rapyd->load('dataobject');

		$error='';

		$dop = new DataObject("ingpresup");

		$do = new DataObject("ingresos");
		$do->rel_one_to_many('ingmbanc'  , 'ingmbanc'  , array('numero'=>'ingreso'));
		$do->rel_one_to_many('itingresos', 'itingresos', array('numero'=>'numero'));
		$do->rel_pointer('ingmbanc','mbanc' ,'ingmbanc.codmbanc=mbanc.id',"mbanc.status statusm,mbanc.codbanc codbancm,mbanc.destino destinom,mbanc.tipo_doc tipo_docm,mbanc.cheque chequem,mbanc.fecha fecham,mbanc.monto montom,mbanc.benefi benefim,mbanc.observa observam");
		$do->load($numero);

		$status = $do->get('status');
		if($status=='C'){
			for($i=0;$i <   $do->count_rel('itingresos');$i++){
				$codigopres   = $do->get_rel('itingresos','codigopres',$i);
				$monto        = $do->get_rel('itingresos','monto'     ,$i);

				$dop->load($codigopres);
				$recaudado    = $dop->get('recaudado');
				//if($monto>$recaudado)
				//$error.="El Monto ($monto) a devolver es mayor al Recaudado ($recaudado)";
			}

		    $ids=array();
                    for($i=0;$i < $do->count_rel('ingmbanc'); $i++){
                            $mstatus     = $do->get_rel_pointer('ingmbanc','statusm'    ,$i  );
                            $codbanc     = $do->get_rel_pointer('ingmbanc','codbancm'   ,$i  );
                            $tipo_doc    = $do->get_rel_pointer('ingmbanc','tipo_docm'  ,$i  );
                            $fecha       = $do->get_rel_pointer('ingmbanc','fecham'     ,$i  );
                            $monto       = $do->get_rel_pointer('ingmbanc','montom'     ,$i  );
                            $cheque      = $do->get_rel_pointer('ingmbanc','chequem'    ,$i  );
                            $ids[]=$mid  = $do->get_rel_pointer('ingmbanc','idm'        ,$i  );
                            $staing      = $do->get_rel_pointer('ingmbanc','staing'     ,$i  );

                            if($mstatus!='J2')
                            $error.="Error, no se puede realizar la operacion para el movimiento $cheque";

                            //if($staing!='C')
                            //$error.="Error, EL movimiento $cheque no ha sido utilizado para un ingreso";
                    }
		}elseif($status=='P'){

		}else{
			$error.='No se puede anular el Ingreso';
		}

		if(empty($error) && $status!='P'){
			for($i=0;$i <   $do->count_rel('itingresos');$i++){
				$codigopres   = $do->get_rel('itingresos','codigopres',$i);
				$monto        = $do->get_rel('itingresos','monto'     ,$i);
				$dop->load($codigopres);
				$recaudado=$dop->get('recaudado');
				$dop->set('recaudado',$recaudado-$monto);
				$dop->save();
			}
			$ids=implode("','",$ids);
			$this->db->simple_query("UPDATE mbanc SET staing='P' WHERE id IN ('$ids')");
		}

		if(empty($error)){
			$this->db->simple_query("UPDATE ingresos SET status='A' WHERE numero=$numero");
			//$do->set('status','A');
			//$do->save();
			logusu('ingresos',"Anulo Ingreso nro $numero");
			redirect($this->url."dataedit/show/$numero");
		}else{
			logusu('cdisp',"Marco como terminado ingreso nro $numero con ERROR $error");
			$data['content'] = '<div class="error">'.$error.'</div></br>'.anchor($this->url."dataedit/show/$numero",'Regresar');
			$data['title']   = " $this->tits";
			$data["head"]    = $this->rapyd->get_head();
			$this->load->view('view_ventanas', $data);
		}
	}

	function positivo($valor){
		if ($valor < 0){
			$this->validation->set_message('positivo',"El campo monto debe ser positivo");
			return FALSE;
		}
  	return TRUE;
	}

    function recibo($status='',$numero){
		$this->rapyd->load("dataobject","dataedit");

		$edit = new DataEdit($this->tits, "ingresos");
		$edit->back_url = site_url($this->url."/dataedit/show/$numero");

		$edit->back_cancel=true;
		$edit->back_cancel_save=true;

		$edit->pre_process('update'  ,'_valida_ing');
		$edit->post_process('update','_post_update_ing');

		$edit->numero  = new inputField("N&uacute;mero", "numero");
		$edit->numero->mode="autohide";

		$edit->recibo  = new inputField("Recibo", "recibo");
		$edit->recibo->size=15;

		$edit->buttons("undo","back","save");
		$edit->build();

		$data['content'] = $edit->output;
		$data['title']   = "Ingresar Recibo";
		$data["head"]    = $this->rapyd->get_head().script('jquery.js').script("plugins/jquery.numeric.pack.js").script("plugins/jquery.json.min.js");
		$this->load->view('view_ventanas', $data);
	}

        function _post_update_ing($do){
            $numero=$do->get('numero');
            redirect($this->url."termina/$numero");
        }

        function _valida_update_ing($do){
            $status =$do->get('status');
            $recibo =$do->get('recibo');
            $reciboe=$this->db->escape($recibo);

            if($status!='C')
            $error.="No se puede Ingresar el Numero de Factura para este documento";

            $cant=$this->datasis->dameval("SELECT COUNT(*) FROM ingresos WHERE recibo=$reciboe AND status='C'");
            if($cant>0)
            $error.="El numero de recibo ya existe para otro ingreso";

            if(!empty($error)){
                    $do->error_message_ar['pre_ins']=$error;
                    $do->error_message_ar['pre_upd']=$error;
                    return false;
            }
        }

	function _post_insert($do){
		$numero = $do->get('numero');
		logusu('ingresos',"Creo ingreso Nro $numero");
		//redirect($this->url."actualizar/$numero");
	}

	function _post_update($do){
		$numero = $do->get('numero');
		logusu('ingresos'," Modifico ingreso Nro $numero");
		//redirect($this->url."actualizar/$numero");
	}
	function _post_delete($do){
		$numero = $do->get('numero');
		logusu('ingresos'," Elimino ingreso Nro $numero");
	}

	function instalar(){
		$query="ALTER TABLE `mbanc`  ADD COLUMN `coding` INT(11) NULL DEFAULT NULL";
		$this->db->simple_query($query);
		$query="ALTER TABLE `ingresos`  CHANGE COLUMN `total` `total` DOUBLE(19,2) NULL DEFAULT NULL";
		$this->db->simple_query($query);
		$query="ALTER TABLE `ingresos` ADD COLUMN `totalch` DOUBLE(19,2) NULL ";
		$this->db->simple_query($query);
		$query="ALTER TABLE `ingresos`  ADD COLUMN `concepto` TEXT NULL";
		$this->db->simple_query($query);
		$query="ALTER TABLE `itingresos`  ADD COLUMN `denomi` TEXT NULL DEFAULT NULL";
		$this->db->simple_query($query);
		$query="ALTER TABLE `itingresos`  ADD COLUMN `bruto` DECIMAL(19,2) NULL";
		$this->db->simple_query($query);
		$query="ALTER TABLE `itingresos` ADD COLUMN `dcto` DECIMAL(19,2) NULL";
		$this->db->simple_query($query);
		$query="ALTER TABLE `ingresos`  ADD COLUMN `tbruto` DECIMAL(19,2) NULL DEFAULT NULL";
		$this->db->simple_query($query);
		$query="ALTER TABLE `ingresos`  ADD COLUMN `tdcto` DECIMAL(19,2) NULL DEFAULT NULL ";
		$this->db->simple_query($query);
		$query="ALTER TABLE `ingmbanc` ADD COLUMN `codbanc` VARCHAR(10) NOT NULL ";
		$this->db->simple_query($query);
		$query="ALTER TABLE `ingmbanc` ADD COLUMN `tipo_doc` CHAR(2) NOT NULL    ";
		$this->db->simple_query($query);
		$query="ALTER TABLE `ingmbanc` ADD COLUMN `cheque` TEXT NOT NULL         ";
		$this->db->simple_query($query);
		$query="ALTER TABLE `ingmbanc` ADD COLUMN `monto` DECIMAL(19,2) NOT NULL ";
		$this->db->simple_query($query);
		$query="ALTER TABLE `ingmbanc`  ADD COLUMN `fecha` DATE NOT NULL";
		$this->db->simple_query($query);
		$query="ALTER TABLE `ingresos`  ADD COLUMN `planillas` TEXT NULL DEFAULT NULL ,  ADD COLUMN `ano` VARCHAR(100) NULL DEFAULT NULL,  ADD COLUMN `mes` VARCHAR(100) NULL DEFAULT NULL";
		$this->db->simple_query($query);
		$query="ALTER TABLE `ingresos`  ADD COLUMN `quincena` VARCHAR(100) NULL DEFAULT NULL";
		$this->db->simple_query($query);
		$query="ALTER TABLE `ingresos`  ADD COLUMN `tipod` CHAR(2) NULL DEFAULT 'I'";
		$this->db->simple_query($query);
		$query="CREATE ALGORITHM = UNDEFINED VIEW `v_mbancm` AS SELECT a.multiple,a.codbanc,a.tipo_doc,GROUP_CONCAT(a.cheque) cheque,a.fecha,SUM(a.monto) monto,a.observa,b.numcuent,b.banco,a.benefi FROM mbanc a JOIN banc b ON a.codbanc=b.codbanc WHERE multiple >0 AND status IN ('J2','A2') GROUP BY multiple,tipo_doc,codbanc, fecha,observa ORDER BY multiple DESC ";
		$this->db->simple_query($query);
		$query="ALTER TABLE `ingmbanc`  ADD COLUMN `multiple` INT NOT NULL";
		$this->db->simple_query($query);
		$query="ALTER ALGORITHM = UNDEFINED DEFINER=`datasis`@`localhost` VIEW `v_mbancm` AS select `a`.`multiple` AS `multiple`,`a`.`codbanc` AS `codbanc`,`a`.`tipo_doc` AS `tipo_doc`,group_concat(`a`.`cheque` separator ',') AS `cheque`,date_format(a.`fecha`,'%d/%m/%Y') AS `fecha`,sum(`a`.`monto`) AS `monto`,`a`.`observa` AS `observa`,`b`.`numcuent` AS `numcuent`,`b`.`banco` AS `banco`,`a`.`benefi` AS `benefi` from (`mbanc` `a` join `banc` `b` on((`a`.`codbanc` = `b`.`codbanc`))) where ((`a`.`multiple` > 0) and (`a`.`status` in ('J2','A2'))) group by `a`.`multiple`,`a`.`tipo_doc`,`a`.`codbanc`,`a`.`fecha`,`a`.`observa` order by `a`.`multiple` desc";
		$this->db->simple_query($query);
	}
}
?>
