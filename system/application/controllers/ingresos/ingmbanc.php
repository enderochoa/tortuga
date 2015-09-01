<?php
//require_once(BASEPATH.'application/controllers/presupuesto/common.php');
class Ingmbanc extends Controller {

	var $url  = "ingresos/ingmbanc/";
	var $tits = "Ingresos Diarios";
	var $titp = "Ingresos Diarios";

	function Ingmbanc(){
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

		$link=site_url('presupuesto/requisicion/getadmin');
		$script='
			$(function() {
				$(".inputnum").numeric(".");
			});
			';

		$filter = new DataFilter("");

		$filter->script($script);

		$filter->db->select(array("a.numero numero","a.fecha fecha","a.total total"));
		$filter->db->from("ingresos a");
		//$filter->db->join("uejecutora b" ,"a.uejecuta=b.codigo");


		$filter->numero = new inputField("N&uacute;mero", "numero");
		$filter->numero->size=15;

		$filter->fecha = new dateonlyField("Fecha", "fecha");
		$filter->fecha->size=12;

		$filter->buttons("reset","search");
		$filter->build();

		$uri  = anchor($this->url.'dataedit/show/<#numero#>'  ,'<str_pad><#numero#>|8|0|STR_PAD_LEFT</str_pad>');

		$grid = new DataGrid("");
		$grid->order_by("numero","desc");
		$grid->per_page = 20;
		$grid->use_function('substr','str_pad');

		$grid->column_orderby("N&uacute;mero"  ,$uri                                             ,"numero"    );
		$grid->column_orderby("Fecha"          ,"<dbdate_to_human><#fecha#></dbdate_to_human>"   ,"fecha"   ,"align='center'" );
		$grid->column_orderby("Total"          ,"<nformat><#total#></nformat>"                   ,"total"   ,"align='right' " );

		$grid->add($this->url."dataedit/create");
		$grid->build();

		$data['filtro']  = $filter->output;
		$data['content'] = $grid->output;
		$data['title']   = $this->titp;
		$data['script']  = script("jquery.js")."\n";
		$data["head"]    = $this->rapyd->get_head();
		$this->load->view('view_ventanas', $data);
	}

	function dataedit(){
		//$this->datasis->modulo_id(115,1);
		$this->rapyd->load('dataobject','datadetails');

		$this->rapyd->uri->keep_persistence();

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
					'banco'   =>'Banco',
					'numcuent'=>'Cuenta',
					'saldo'   =>'Saldo'),
				'filtro'  =>array(
					'codbanc' =>'C&oacute;odigo',
					'banco'   =>'Banco',
					'numcuent'=>'Cuenta',
					'saldo'=>'Saldo'),
				'p_uri'=>array(
				  4=>'<#i#>'),
				'retornar'=>array(
					'codbanc'=>'codbancm_<#i#>'
					 ),
				'where'=>'activo = "S"',
				//'script'=>array('ultimoch(<#i#>)','cal_nombrech(<#i#>)'),
				'titulo'  =>'Buscar Bancos');

		$bBANC=$this->datasis->p_modbus($mBANC,"<#i#>");

		$do = new DataObject("ingresos");
		$do->rel_one_to_many('mbanc', 'mbanc', array('numero'=>'coding'));
		$do->rel_one_to_many('itingresos', 'itingresos', array('numero'=>'numero'));
		$do->rel_pointer('itingresos','v_ingresos' ,'v_ingresos.codigo=itingresos.codigopres',"v_ingresos.denominacion as denomi");

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

		$edit->total  = new inputField("Total", "total");
		$edit->total->size     = 10;
		$edit->total->readonly = true;
		$edit->total->css_class='inputnum';

		$edit->totalch  = new inputField("Total Movimientos Activos", "totalch");
		$edit->totalch->size     = 15;
		$edit->totalch->readonly = true;
		$edit->totalch->css_class='inputnum';

		//************************** FIN   ENCABEZADO********************************************************************

		//**************************INICIO DETALLE DE PRESUPUESTO  *****************************************************

		$edit->itcodigopres = new inputField("(<#o#>) ", "itcodigopres_<#i#>");
		$edit->itcodigopres->rule     ='required';
		$edit->itcodigopres->size     =20;
		$edit->itcodigopres->db_name  ='codigopres';
		$edit->itcodigopres->rel_id   ='itingresos';
		//$edit->itcodigopres->readonly =true;
		$edit->itcodigopres->append($btn);

		$edit->itdenomi= new textareaField("(<#o#>) Denominacion","itdenomi_<#i#>");
		//$edit->itdenomi->rule   ='required';
		$edit->itdenomi->db_name  ='denomi';
		$edit->itdenomi->rel_id   ='itingresos';
		$edit->itdenomi->pointer  =true;
		$edit->itdenomi->rows     =2;
		$edit->itdenomi->cols     =40;
		$edit->itdenomi->readonly =true;

		$edit->itreferen1 = new inputField("(<#o#>) Inicio", 'itreferen1_<#i#>');
		$edit->itreferen1->db_name   ='referen1';
		$edit->itreferen1->size      = 10;
		$edit->itreferen1->rel_id    ='itingresos';

		$edit->itreferen2 = new inputField("(<#o#>) Fin", 'itreferen2_<#i#>');
		$edit->itreferen2->db_name   ='referen2';
		$edit->itreferen2->size      = 10;
		$edit->itreferen2->rel_id    ='itingresos';

		$edit->itmonto = new inputField("(<#o#>) Monto", 'itmonto_<#i#>');
		$edit->itmonto->db_name   ='monto';
		$edit->itmonto->size      = 10;
		$edit->itmonto->rule      ='callback_positivo';
		$edit->itmonto->rel_id    ='itingresos';
		$edit->itmonto->css_class ='inputnum';
		$edit->itmonto->onchange = "cal_tot();";

		//$edit->itmontoa->mode      ="autohide";
		//************************** FIN   DETALLE DE PRESUPUESTO  *****************************************************

		//************************** INICIO  DETALLE DE BANCOS  *****************************************************

		$edit->itstatusm =  new dropdownField("(<#o#>) Banco", 'statusm_<#i#>');
		if($edit->_status=='show')$edit->itstatusm->option("NC","Nota de Cr&eacute;dito"   );
		$edit->itstatusm->option("E1","Pendiente" );
		$edit->itstatusm->option("E2","Activo"    );
		$edit->itstatusm->option("AN","Anulado"   );
		$edit->itstatusm->option("A2","Anulado." );
		$edit->itstatusm->db_name   = 'status';
		$edit->itstatusm-> size     = 3;
		$edit->itstatusm->rel_id    ='mbanc';
		$edit->itstatusm->style     ="width:100px;";
		$edit->itstatusm->onchange  = "cal_totalch();";
		$edit->itstatusm->when=array('show');

		//$edit->itstatusm->pointer = true;

		$edit->itcodbancm =  new inputField("(<#o#>) Banco", 'codbancm_<#i#>');
		$edit->itcodbancm->db_name   = 'codbanc';
		$edit->itcodbancm-> size     = 3;
		$edit->itcodbancm-> readonly =true;
		$edit->itcodbancm->rel_id    ='mbanc';
		$edit->itcodbancm->rule       = "required|callback_banco";
		$edit->itcodbancm->append($bBANC);
		//$edit->itcodbancm->pointer = true;
		$edit->itdestino = new dropdownField("(<#o#>) Destino","destino_<#i#>");
		$edit->itdestino->db_name = 'destino';
		$edit->itdestino->option("C","Caja"    );
		$edit->itdestino->option("I","Interno" );
		$edit->itdestino->style="width:50px";
		$edit->itdestino->rel_id   ='mbanc';

		$edit->ittipo_docm = new dropdownField("(<#o#>) Tipo Documento","tipo_docm_<#i#>");
		$edit->ittipo_docm->db_name   = 'tipo_doc';
		$edit->ittipo_docm->rel_id    ='mbanc';
		$edit->ittipo_docm->style     ="width:130px;";
		$edit->ittipo_docm->option("NC","Nota de Credito");
		$edit->ittipo_docm->option("DP","Deposito"       );

		$edit->itchequem =  new inputField("(<#o#>) Cheque", 'chequem_<#i#>');
		$edit->itchequem->db_name   ='cheque';
		$edit->itchequem-> size  = 10;
		$edit->itchequem->rule   = "required";//callback_chexiste_cheque|
		$edit->itchequem->rel_id   ='mbanc';
		//$edit->itchequem->pointer = true;

		$edit->itfecham = new  dateonlyField("(<#o#>) Fecha Cheque",  "fecham_<#i#>");
		$edit->itfecham->db_name   ='fecha';
		$edit->itfecham->size        =10;
		$edit->itfecham->rule        = 'required';
		$edit->itfecham->rel_id   ='mbanc';
		$edit->itfecham->insertValue = date('Ymd');
		//$edit->itfecham->pointer = true;

		$edit->itmontom = new inputField("(<#o#>) Total", 'montom_<#i#>');
		$edit->itmontom->db_name   ='monto';
		//$edit->itmontom->mode      = 'autohide';
		//$edit->itmontom->when     = array('show');
		$edit->itmontom->size      = 15;
		$edit->itmontom->rule      ='callback_positivo';
		$edit->itmontom->rel_id    ='mbanc';
		$edit->itmontom->css_class ='inputnum';
		$edit->itmontom->onchange  = "cal_totalch();";
		//$edit->itmontom->pointer = true;

		$edit->itbenefim = new inputField("(<#o#>) A Nombre de", 'benefim_<#i#>');
		$edit->itbenefim->db_name   = 'benefi';
		$edit->itbenefim->size      = 15;
		$edit->itbenefim->maxlenght = 40;
		$edit->itbenefim->rel_id    = 'mbanc';

		$edit->itobservam = new textAreaField("(<#o#>) Observaciones", 'observam_<#i#>');
		$edit->itobservam->db_name   ='observa';
		$edit->itobservam->cols = 20;
		$edit->itobservam->rows = 1;
		$edit->itobservam->rel_id   ='mbanc';

		//************************** FIN  DETALLE DE BANCOS  *****************************************************


		$status=$edit->get_from_dataobjetct('status');
		if($status=='P'){
			$action = "javascript:window.location='" .site_url($this->url.'termina/'.$edit->rapyd->uri->get_edited_id()). "'";

			$edit->button_status("btn_termina",'Marcar Ingreso como finalizado',$action,"TR","show");
			$edit->buttons("add_rel","modify", "save","delete");
		}elseif($status=='C'){
			$action = "javascript:btn_anular('" .$edit->rapyd->uri->get_edited_id()."')";
			$edit->button_status("btn_anula",'Anular',$action,"TR","show");
		}elseif($status=='O'){
			$edit->buttons("add_rel","modify", "save","delete");
		}

		$edit->button_status("btn_add_mbanc" ,'Agregar Deposito / Nota de Credito',"javascript:add_mbanc()","MB",'modify',"button_add_rel");
		$edit->button_status("btn_add_mbanc2",'Agregar Deposito / Nota de Credito',"javascript:add_mbanc()","MB",'create',"button_add_rel");
		$edit->button_status("btn_add_pades" ,'Agregar Rubro',"javascript:add_itingresos()","PA","create","button_add_rel");
		$edit->button_status("btn_add_pades2",'Agregar Rubro',"javascript:add_itingresos()","PA","modify","button_add_rel");

		$edit->buttons("save","undo", "back","add");
		$edit->build();

		$smenu['link']   = barra_menu('304');
		$data['smenu']   = $this->load->view('view_sub_menu', $smenu,true);
		$conten["form"]  =&  $edit;
		$data['content'] = $this->load->view('view_ingmbanc', $conten,true);
		//$data['content'] = $edit->output;
		$data['title']   = $this->tits;
		$data["head"]    = $this->rapyd->get_head().script('jquery.js').script("plugins/jquery.numeric.pack.js").script("plugins/jquery.json.min.js");
		$this->load->view('view_ventanas', $data);
	}

	function _valida($do){
		$error = '';
		$numero = $do->get('numero');

		$total=0;$importes=array();
		for($i=0;$i <   $do->count_rel('itingresos');$i++){
			$codigopres   = $do->get_rel('itingresos','codigopres',$i);
			$total+=$monto= $do->get_rel('itingresos','monto'     ,$i);

			$c=$this->datasis->dameval("SELECT COUNT(*) FROM v_ingresos WHERE codigo='$codigopres'");
			if($c <=0)$error.="La partida ($codigopres) no existe";
			$cadena = $codigopres;
			if(array_key_exists($cadena,$importes)){
				$error.='La partida ($codigopres) esta repetida';
			}else{
				$importes[$cadena]  =0;
			}
		}
		$totalch=0;
		for($i=0;$i <   $do->count_rel('mbanc');$i++){
			$totalch+=$monto= $do->get_rel('mbanc','monto'     ,$i);
			$do->set_rel('mbanc','status'  ,'E1'     ,$i);

		}

		$do->set('total' ,$total   );
		$do->set('totalch' ,$totalch );
		$do->set('status','P'      );


		if(!empty($error)){
			$do->error_message_ar['pre_ins']=$error;
			$do->error_message_ar['pre_upd']=$error;
			return false;
		}
	}

	function termina($numero){
		$this->rapyd->load('dataobject');

		$error='';

		$dop = new DataObject("ingpresup");

		$do = new DataObject("ingresos");
		$do->rel_one_to_many('mbanc', 'mbanc', array('ingresos.numero'=>'mbanc.coding'));
		$do->rel_one_to_many('itingresos', 'itingresos', array('numero'=>'numero'));
		$do->load($numero);

		$status = $do->get('status');

		if($status=='P'){
			for($i=0;$i <   $do->count_rel('itingresos');$i++){
				$codigopres   = $do->get_rel('itingresos','codigopres',$i);
				$monto        = $do->get_rel('itingresos','monto'     ,$i);
				$dop->load($codigopres);
				$recaudado    = $dop->get('recaudado');
				$dop->set('recaudado',$monto+$recaudado);
				$dop->save();
			}
		}else{
			$error.= "<div class='alert'>No se puede realizar la operacion para el Ingreso</div>";
		}

		if(empty($error)){
			$do->set('status','C');
			$do->save();
			logusu('ingresos',"Marco como terminado Ingreso nro $numero");
			redirect($this->url."dataedit/show/$numero");
		}else{
			logusu('cdisp',"Marco como terminado ingreso nro $numero con ERROR $error");
			$data['content'] = $error.anchor($this->url."dataedit/show/$numero",'Regresar');
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
		$do->rel_one_to_many('mbanc', 'mbanc', array('numero'=>'coding'));
		$do->rel_one_to_many('itingresos', 'itingresos', array('numero'=>'numero'));
		$do->load($numero);

		$status = $do->get('status');
		if($status=='C'){
			for($i=0;$i <   $do->count_rel('itingresos');$i++){
				$codigopres   = $do->get_rel('itingresos','codigopres',$i);
				$monto        = $do->get_rel('itingresos','monto'     ,$i);
				$dop->load($codigopres);
				$recaudado    = $do->get('recaudado');
				if($monto>$recaudado)
				$error.='El Monto a devolver es mayor al Recaudado';
			}
			if(empty($error)){
				for($i=0;$i <   $do->count_rel('itingresos');$i++){
					$codigopres   = $do->get_rel('itingresos','codigopres',$i);
					$monto        = $do->get_rel('itingresos','monto'     ,$i);
					$dop->load($codigopres);
					$recaudado    = $do->get('recaudado');
					$dop->set('recaudado',$recaudado-$monto);
					$dop->save();
				}
			}
		}else{
			$error.='No se puede anular el Ingreso';
		}

		if(empty($error)){
			$do->set('status','');
			$do->save();
			logusu('ingresos',"Anulo Ingreso nro $numero");
			redirect($this->url."dataedit/show/$numero");
		}else{
			logusu('cdisp',"Marco como terminado ingreso nro $numero con ERROR $error");
			$data['content'] = $error.anchor($this->url."dataedit/show/$numero",'Regresar');
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
		$query="CREATE TABLE `ingmbanc` (
			`ingreso` INT(11) NOT NULL,
			`codmbanc` INT(11) NOT NULL,
			`numero` INT(11) NOT NULL,
			`codbanc` VARCHAR(10) NOT NULL,
			`tipo_doc` CHAR(2) NOT NULL,
			`cheque` TEXT NOT NULL,
			`monto` DECIMAL(19,2) NOT NULL,
			`fecha` DATE NOT NULL,
			`id` INT(11) NOT NULL AUTO_INCREMENT,
			`destino` CHAR(1) NOT NULL,
			`benefi` TEXT NOT NULL,
			`observa` TEXT NOT NULL,
			PRIMARY KEY (`id`)
		)
		COLLATE='utf8_general_ci'
		ENGINE=MyISAM
		AUTO_INCREMENT=1;
";
	}
}
?>
