<?php
//require_once(BASEPATH.'application/controllers/presupuesto/common.php');
class Ingresos extends Controller {

	var $url  = "ingresos/ingresos/";
	var $tits = "Ingresos Diarios";
	var $titp = "Ingresos Diarios";

	function Ingresos(){
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
			
		$modbus2=array(
			'tabla'   =>'mbanc',
			'columnas'=>array(
				'id'       =>'ID',
				'codbanc'  =>'Banco',
				'tipo_doc' =>'Tipo Doc.',
				'cheque'   =>'Nro. Documento',
				'monto'    =>'Monto'),
			'filtro'  =>array(
				'cheque'=>'Nro. Documento',
				'monto'=>'Monto'),
			'retornar'=>array('id'=>'mbanc_id'),
			'titulo'  =>'Buscar Movimiento Bancario',
			);
			
		$btn   =$this->datasis->p_modbus($modbus,'<#i#>');
		$bmbanc=$this->datasis->modbus($modbus2);

		$do = new DataObject("ingresos");
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
		
		$edit->mbanc_id  = new inputField("ID Cheque", "mbanc_id");
		$edit->mbanc_id->size     = 10;
		$edit->mbanc_id->css_class='inputnum';
		$edit->mbanc_id->append($bmbanc);
		//************************** FIN   ENCABEZADO********************************************************************
		
		//**************************INICIO DETALLE DE ASIGNACIONES  *****************************************************
		
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
		$edit->itdenomi->cols     =50;
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
		//************************** FIN   DETALLE DE ORDENES DEPAGO*****************************************************
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
		
		$edit->buttons("add_rel","save","undo", "back","add");
		$edit->build();

		$smenu['link']   = barra_menu('304');
		$data['smenu']   = $this->load->view('view_sub_menu', $smenu,true);
		$conten["form"]  =&  $edit;
		$data['content'] = $this->load->view('view_ingresos', $conten,true);
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
			$total+=$monto= $do->get_rel('itingresos','soli'      ,$i);
			
			$c=$this->datasis->dameval("SELECT COUNT(*) FROM v_ingresos WHERE codigo='$codigopres'");
			if($c <=0)$error.="La partida ($codigopres) no existe";
			$cadena = $codigopres;
			if(array_key_exists($cadena,$importes)){
				$error.='La partida ($codigopres) esta repetida';
			}else{
				$importes[$cadena]  =0;
			}
		}
		
		$do->set('total' ,$total );
		$do->set('status','P'    );
		
		if(!empty($error)){
			$do->error_message_ar['pre_ins']=$error;
			$do->error_message_ar['pre_upd']=$error;
			return false;
		}
	}
	
	function termina($numero){
		$this->rapyd->load('dataobject');
		
		$error='';
	
		$do = new DataObject("ingresos");
		$do->load($numero);
		
		$status = $do->get('status');
	
		if($status=='P'){
			$do->set('status','C');
		}else{
			$error.= "<div class='alert'>No se puede realizar la operacion para el certificado</div>";
		}
		
		if(empty($error)){
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
		$this->db->query("UPDATE ingresos SET status='A'");
		logusu('ingresos',"Anulo ingreso nro $numero");
		redirect($this->url."dataedit/show/$numero");
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
		$query="ALTER TABLE `ingresos` ADD COLUMN `recibido` TEXT NULL    ";
		$this->db->simple_query($query);
		$query="ALTER TABLE `ingresos` ADD COLUMN `fpago` VARCHAR(50) NULL";
		$this->db->simple_query($query);
		$query="ALTER TABLE `ingresos` ADD COLUMN `tipo` VARCHAR(50) NULL ";
		$this->db->simple_query($query);
		$query="ALTER TABLE `ingresos` ADD COLUMN `npago` TEXT NULL       ";
		$this->db->simple_query($query);
		$query="ALTER TABLE `ingresos`  ADD COLUMN `recibo` VARCHAR(50) NULL";
		$this->db->simple_query($query);
		$query="ALTER TABLE `ingresos`  ADD COLUMN `segun` TEXT NULL";
		$this->db->simple_query($query);
	}
} 
?>