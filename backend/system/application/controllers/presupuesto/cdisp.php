<?php
require_once(BASEPATH.'application/controllers/presupuesto/common.php');
class Cdisp extends Common {

	var $url  = "presupuesto/cdisp/";
	var $tits = "Certificado de Disponibilidad";
	var $titp = "Certificados de Disponibilidad";

	function Cdisp(){
		parent::Controller();
		$this->load->library("rapyd");
		$this->datasis->modulo_id(223,1);
	}
	
	function index(){
		
		redirect($this->url."filteredgrid");
	}
	
	
	function filteredgrid(){
		//$this->datasis->modulo_id(224,1);
		$this->rapyd->load("datafilter","datagrid");
		
		$link=site_url('presupuesto/requisicion/getadmin');
		$script='
			$(function() {
				$(".inputnum").numeric(".");
			});
		
			function get_uadmin(){
				$.post("'.$link.'",{ uejecuta:$("#uejecuta").val() },function(data){$("#td_uadministra").html(data);})
			}
			';

		$filter = new DataFilter("");
		
		$filter->script($script);
		
		$filter->db->select(array("a.status","a.numero numero","a.fecha fecha","a.uejecuta","b.nombre uejecuta2","c.nombre uadministra2","a.ano","tsoli","tdisp"));
		$filter->db->from("cdisp a");                  
		$filter->db->join("itcdisp d"    ,"a.numero=d.numero","LEFT");
		$filter->db->join("uejecutora b" ,"a.uejecuta=b.codigo","LEFT");
		$filter->db->join("uadministra c","a.uadministra=b.codigo","LEFT");
		$filter->db->groupby("a.numero");
		
		
		$filter->numero = new inputField("N&uacute;mero", "numero");
		$filter->numero->size=15;
		
		$filter->fecha = new dateonlyField("Fecha", "fecha");
		$filter->fecha->size=12;
		
		$filter->uejecuta = new dropdownField("U.Ejecutora", "uejecuta");
		$filter->uejecuta->option("","Seccionar");
		$filter->uejecuta->options("SELECT codigo,nombre FROM uejecutora ORDER BY nombre");
		$filter->uejecuta->onchange = "get_uadmin();";
		
		$filter->status = new dropdownField("Estado","status");
		$filter->status->option('' ,'');
		$filter->status->option('C','Pre-Comprometido');
		$filter->status->option('A','Anulado');
		$filter->status->option('F','Finalizado Pre-Compromiso');
		$filter->status->option('P','Pendiente');
		
		$filter->codigoadm = new dropdownField("Est. Administrativa", "codigoadm");
		$filter->codigoadm->option("","Seccionar");
		$filter->codigoadm->options("SELECT b.codigo, CONCAT_WS(' ',b.codigo,b.denominacion) AS val FROM presupuesto AS a JOIN estruadm AS b ON a.codigoadm=b.codigo  GROUP BY b.codigo");
		$filter->codigoadm->db_name = 'd.codigoadm';

		$filter->fondo = new dropdownField("Fuente de Financiamiento", "fondo");
		$filter->fondo->option("","");
		$filter->fondo->options("SELECT fondo,CONCAT_WS(' ',fondo,descrip)a FROM fondo");
		$filter->fondo->db_name = 'd.fondo';

		$filter->codigopres = new inputField("Partida", "codigopres");
		$filter->codigopres-> db_name ="d.codigopres";
		//$filter->codigopres->clause ="likerigth";
		$filter->codigopres->size     = 25;
				
		$filter->buttons("reset","search");    
		$filter->build();
		$uri  = anchor($this->url.'dataedit/show/<#numero#>'  ,'<#numero#>');
		
		function status($status=''){
			switch($status){
				case 'C':$status="Pre-Comprometido";break;
				case 'A':$status="Anulado";break;
				case 'F':$status="Finalizado";break;
				case 'P':$status="Pendiente";break;
			}
			return $status;
		}
		
		$grid = new DataGrid("");
		$grid->order_by("numero","desc");
		$grid->per_page = 20;
		$grid->use_function('substr','str_pad','status');
		
		$grid->column_orderby("N&uacute;mero"         ,$uri                                             ,"numero"        );
		$grid->column_orderby("Fecha"                 ,"<dbdate_to_human><#fecha#></dbdate_to_human>"   ,"fecha"         ,"align='center'"     );
		$grid->column_orderby("Unidad Ejecutora"      ,"uejecuta2"                                      ,"uejecuta2"     ,"align='left' NOWRAP");
		$grid->column_orderby("Unidad Administrativa" ,"uadministra2"                                   ,"uadministra2"  ,"align='left' NOWRAP");
		$grid->column_orderby("Disponible"            ,"<nformat><#tdisp#></nformat>"                   ,"tdisp"         ,"align='right' NOWRAP");
		$grid->column_orderby("Solicitado"            ,"<nformat><#tsoli#></nformat>"                   ,"tsoli"         ,"align='right' NOWRAP");
		$grid->column_orderby("Estado"                ,"<status><#status#></status>"                   ,"status"         ,"align='left'");
		
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
		
		//$this->rapyd->uri->keep_persistence();

		$modbus=array(
			'tabla'   =>'v_presaldo',
			'columnas'=>array(
				'codigoadm'   =>'Est. Admin',
				'codigo'      =>'Partida',
				'denominacion'=>'Denominaci&oacute;n',
				'apartado'    =>'Pre-Comprometido',
				'saldo'       =>'Saldo'
				),
			'filtro'  =>array(
				'codigoadm'   =>'Est. Admin',
				'codigo'      =>'Partida',
				'denominacion'=>'Denominacion',),
			'retornar'=>array(
				'codigoadm'   =>'itcodigoadm_<#i#>',
				'codigo'      =>'itcodigopres_<#i#>',
				'denominacion'=>'itdenomi_<#i#>',
				'saldo'       =>'itdisp_<#i#>'),
			'p_uri'=>array(4=>'<#i#>',5=>'<#fondo#>'),
			'where'   =>'movimiento = "S" AND saldo >0 AND fondo=<#fondo#> AND codigo LIKE "4.%"',
			'script'  =>array('cal_soli()'),
			'titulo'  =>'Busqueda de partidas');
		$btn=$this->datasis->p_modbus($modbus,'<#i#>/<#fondo#>');
		$btn='<img src="'.base_url().'assets/default/images/system-search.png"  alt="Busqueda de partidas" title="Busqueda de partidas" border="0" onclick="modbusdepen(<#i#>)"/>';
						
		$do = new DataObject("cdisp");
		$do->rel_one_to_many('itcdisp', 'itcdisp', array('numero'=>'numero'));
		$do->rel_pointer('itcdisp','v_presaldo' ,'itcdisp.codigoadm=v_presaldo.codigoadm AND itcdisp.fondo=v_presaldo.fondo AND itcdisp.codigopres=v_presaldo.codigo ',"v_presaldo.denominacion as denomi");
		//$do->rel_pointer('itcdisp','ppla' ,'ppla.codigo=itcdisp.codigopres',"ppla.denominacion as denomi");		

		$edit = new DataDetails($this->tits, $do);
		$edit->back_url = site_url($this->url."filteredgrid");
		$edit->set_rel_title('itcdisp','Rubro <#o#>');
		
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
		$edit->fecha->mode        = "autohide";
		//$edit->fecha->when        =array('show');
		
		$edit->status = new dropdownField("Estado","status");
		$edit->status->option('C','Pre-Comprometido');
		$edit->status->option('A','Anulado');
		$edit->status->option('F','Finalizado Pre-Compromiso');
		$edit->status->option('P','Pendiente');
		$edit->status->when =array('show');
		
		$edit->ano= new  dateonlyField("A&ntilde;o",  "ano","Y");
		$edit->ano->insertValue = date('Y');
		$edit->ano->size        = 12;
		$edit->ano->mode        = "autohide";
		$edit->ano->when        =array('show');
			
		$edit->reque = new textareaField("Requerimientos", 'reque');
		$edit->reque->rows     = 3;
		$edit->reque->cols     = 60;
		
		$edit->uejecuta = new dropdownField("U.Ejecutora", "uejecuta");
		$edit->uejecuta->option("","Seccionar");
		$edit->uejecuta->options("SELECT codigo,nombre FROM uejecutora ORDER BY nombre");
		$edit->uejecuta->onchange = "get_uadmin();";
		$edit->uejecuta->rule = "required";

		$edit->uadministra = new dropdownField("U.Administrativa", "uadministra");
		$edit->uadministra->option("","Ninguna");
		$ueje=$edit->getval('uejecuta');
		if($ueje!==false){
			$edit->uadministra->options("SELECT codigo,nombre FROM uadministra WHERE codigoejec='$ueje' ORDER BY nombre");
		}else{
			$edit->uadministra->option("","Seleccione una unidad ejecutora primero");
		}
		
		$edit->fondo = new dropdownField("F. Financiamiento","fondo");
		$edit->fondo->rule   ='required';
		$edit->fondo->db_name='fondo';
		$edit->fondo->options("SELECT tipo,tipo a  FROM presupuesto WHERE tipo<>'' GROUP BY tipo ORDER BY tipo desc");
		$edit->fondo->style="width:100px;";
		
		$edit->tdisp  = new inputField("Total Disponibilidad", "tdisp");
		$edit->tdisp->size = 10;
		$edit->tdisp->readonly = true;
		$edit->tdisp->css_class='inputnum';
		//$edit->tdisp->mode     ="autohide";

		$edit->tsoli  = new inputField("Total Solicitado", "tsoli");
		$edit->tsoli->size     = 10;
		$edit->tsoli->readonly = true;
		$edit->tsoli->css_class='inputnum';
		
		//************************** FIN   ENCABEZADO********************************************************************
		
		//**************************INICIO DETALLE DE ASIGNACIONES  *****************************************************
		$edit->itcodigoadm = new inputField("Estructura	Administrativa","itcodigoadm_<#i#>");
		$edit->itcodigoadm->db_name      ='codigoadm';
		$edit->itcodigoadm->rel_id       ='itcdisp';
		$edit->itcodigoadm->rule         ='required';
		$edit->itcodigoadm->size         =10;
		$edit->itcodigoadm->autocomplete =false;
		if($status=='O' )
		$edit->itcodigoadm->readonly = true;
		//$edit->itcodigoadm->mode    ="autohide";
		 		
		$edit->itcodigopres = new inputField("(<#o#>) ", "itcodigopres_<#i#>");
		$edit->itcodigopres->rule         ='required|callback_itorden';
		$edit->itcodigopres->size         =15;
		$edit->itcodigopres->db_name      ='codigopres';
		$edit->itcodigopres->rel_id       ='itcdisp';
		$edit->itcodigopres->autocomplete =false;
		//$edit->itcodigopres->readonly =true; 
		$edit->itcodigopres->append($btn);
		if($status=='O' )
		$edit->itcodigopres->readonly = true;
		//$edit->itcodigopres->mode    ="autohide";
		
		//$edit->itfondo = new inputField("(<#o#>) Fondo","itfondo_<#i#>");
		//$edit->itfondo->rule   ='required';
		//$edit->itfondo->db_name='fondo';
		//$edit->itfondo->rel_id ='itcdisp';
		//$edit->itfondo->size     =10;
		//if($status=='O' )
		//$edit->itfondo->readonly = true;
		//$edit->itfondo->mode     ="autohide";
				
		$edit->itdenomi= new inputField("(<#o#>) Denominacion","itdenomi_<#i#>");
		//$edit->itdenomi->rule   ='required';
		$edit->itdenomi->db_name  ='denomi';
		$edit->itdenomi->rel_id   ='itcdisp';
		$edit->itdenomi->pointer  =true;
		$edit->itdenomi->size     =40;
		$edit->itdenomi->readonly =true;
		
		//$edit->itdenomi->mode     ="autohide";
		
		$edit->itdisp = new inputField("(<#o#>) Disponible", 'itdisp_<#i#>');
		$edit->itdisp->db_name   ='disp';
		$edit->itdisp->size      = 10;
		$edit->itdisp->rel_id    ='itcdisp';
		$edit->itdisp->css_class ='inputnum';
		$edit->itdisp->readonly =true;
		
		$edit->itsoli = new inputField("(<#o#>) Solicitado", 'itsoli_<#i#>');
		$edit->itsoli->db_name   ='soli';
		$edit->itsoli->size      = 10;
		$edit->itsoli->rule      ='callback_positivo';
		$edit->itsoli->rel_id    ='itcdisp';
		$edit->itsoli->css_class ='inputnum';
		$edit->itsoli->onchange  = 'cal_soli();';
		if($status=='O')
		$edit->itsoli->readonly =true;
		
		//$edit->itmontoa->mode      ="autohide";	
		//************************** FIN   DETALLE DE ORDENES DEPAGO*****************************************************
		$status=$edit->get_from_dataobjetct('status');
		if($status=='P'){
			$action = "javascript:window.location='" .site_url($this->url.'cd_precomprometer/'.$edit->rapyd->uri->get_edited_id()). "'";
			$edit->button_status("btn_termina",'Pre-Comprometer',$action,"TR","show");
			$edit->buttons("add_rel","modify", "save","delete");
		}elseif($status=='C'){
			$action = "javascript:btn_anular('" .$edit->rapyd->uri->get_edited_id()."')";
			$edit->button_status("btn_anula",'Anular Pre-Compromiso',$action,"TR","show");
			
			$action = "javascript:window.location='" .site_url($this->url.'cd_finalizar/'.$edit->rapyd->uri->get_edited_id())."'";
			$edit->button_status("btn_deshapartar",'Finalizar Pre-Compromiso',$action,"TR","show");
		}
		
		$edit->buttons("add","add_rel","save","undo", "back");
		$edit->build();

		$smenu['link']   = barra_menu('304');
		$data['smenu']   = $this->load->view('view_sub_menu', $smenu,true);
		$conten["form"]  =&  $edit;
		$data['content'] = $this->load->view('view_cdisp', $conten,true);
		//$data['content'] = $edit->output;
		$data['title']   = $this->tits;
		$data["head"]    = script('jquery.js').script('jquery-ui.js').script('plugins/jquery.numeric.pack.js').script('plugins/jquery.floatnumber.js').script('plugins/jquery.meiomask.js').style('vino/jquery-ui.css').$this->rapyd->get_head();//.script('plugins/jquery.autocomplete.js').style('jquery.autocomplete.css')
		$this->load->view('view_ventanas', $data);
	}
			
	function _valida($do){
		$error = '';
		$numero = $do->get('numero');
		$tipo   = $do->get('tipo'  );
		$fondo  = $do->get('fondo' );
		$do->set('status','P');
		
		
		if(empty($numero)){
			$ntransac = $this->datasis->fprox_numero('ntransac');
			$do->set('numero','_'.$ntransac);
			$do->pk    =array('numero'=>'_'.$ntransac);
			$importes=array(); $ivas=array();
			for($i=0;$i < $do->count_rel('itcdisp');$i++){
				$do->set_rel('itcdisp','numero','_'.$ntransac,$i);
			}
		}
		
		$importes=array();$tdisp=$tsoli=0;
		for($i=0;$i < $do->count_rel('itcdisp');$i++){
			$do->set_rel('itcdisp','fondo'     ,$fondo,$i);
			$codigoadm    = $do->get_rel('itcdisp','codigoadm' ,$i);
			$codigopres   = $do->get_rel('itcdisp','codigopres',$i);
			$tsoli+=$soli = $do->get_rel('itcdisp','soli'      ,$i);
			
			$error.=$this->itpartida($codigoadm,$fondo,$codigopres);
			
			$tdisp+=$disponible = $this->datasis->dameval("SELECT saldo FROM v_presaldo WHERE codigoadm='$codigoadm' AND fondo='$fondo' AND codigo='$codigopres'");
			
			$do->set_rel('itcdisp','disp',$disponible,$i);
			
			$cadena = $codigoadm.'_._'.$fondo.'_._'.$codigopres;
			if(array_key_exists($cadena,$importes)){
				$importes[$cadena] +=$soli;
			}else{
				$importes[$cadena]  =$soli;
			}
		}
		
		$do->set('tsoli',$tsoli);
		$do->set('tdisp',$tdisp);
		
		if(empty($error)){
			foreach($importes AS $cadena=>$monto){
				$temp  = explode('_._',$cadena);
				$error.= $this->chequeapresup($temp[0],$temp[1],$temp[2],'',$monto,0,'round($monto,2) > $disponible=round(($presupuesto-($comprometido+$apartado)),2)','El Monto ($monto) es mayor al disponible ($disponible) para la partida ('.$temp[0].') ('.$temp[1].') ('.$temp[2].')' ) ;
			}
		}
		
		if(!empty($error)){
			$do->error_message_ar['pre_ins']=$error;
			$do->error_message_ar['pre_upd']=$error;
			return false;
		}
	}
	
	
	
	
	function positivo($valor){
		if ($valor < 0){
			$this->validation->set_message('positivo',"El campo monto debe ser positivo");
			return FALSE;
		}
  	return TRUE;
	}
	
	function chstatus($status){
		$this->validation->set_message('chstatus',"No lo puedes cambiar pq no quiero");
		return false;
	}
	
	function _post_insert($do){
		$numero = $do->get('numero');
		logusu('cdisp',"Creo certificado de disponibilidad Nro $numero");
		//redirect($this->url."actualizar/$numero");
	}
	
	function _post_update($do){
		$numero = $do->get('numero');
		logusu('cdisp'," Modifico certificado de disponibilidad Nro $numero");
		//redirect($this->url."actualizar/$numero");
	}
	function _post_delete($do){
		$numero = $do->get('numero');
		logusu('cdisp'," Elimino certificado de disponibilidad Nro $numero");
	}
	
	function prueba(){
			$numeros=$this->datasis->consularray("SELECT a.numero,a.numero 
	FROM odirect a
	JOIN pades b ON a.numero=b.pago
	JOIN desem c ON b.desem=c.numero
	GROUP BY a.numero
	HAVING COUNT(*)>1");
	
	echo implode(',',$numeros);
	}
	
	function instalar(){
		$query="CREATE TABLE `cdisp` (
			`numero` VARCHAR(11) NOT NULL DEFAULT '',
			`fecha` DATE NULL DEFAULT NULL,
			`ano` CHAR(4) NULL DEFAULT NULL,
			`uejecuta` CHAR(8) NULL DEFAULT NULL,
			`tipo` CHAR(1) NULL DEFAULT NULL,
			`uadministra` CHAR(8) NULL DEFAULT NULL,
			`reque` TEXT NULL,
			`tdisp` DECIMAL(19,2) NULL DEFAULT NULL,
			`tsoli` DECIMAL(19,2) NULL DEFAULT NULL,
			`status` CHAR(1) NULL DEFAULT NULL,
			`fondo` VARCHAR(20) NULL DEFAULT NULL,
			PRIMARY KEY (`numero`)
		)
		COLLATE='utf8_general_ci'
		ENGINE=MyISAM;
		";
		
		$this->db->simple_query($query);
		
		$query="ALTER TABLE `cdisp` CHANGE COLUMN `uejecuta` `uejecuta` CHAR(8) NULL DEFAULT NULL AFTER `ano`";
		$this->db->simple_query($query);
		$query="CHANGE COLUMN `uadministra` `uadministra` CHAR(8) NULL DEFAULT NULL AFTER `tipo`";
		$this->db->simple_query($query);
		
		$query="ALTER TABLE `cdisp` ADD COLUMN `fanulado` DATE NULL DEFAULT NULL AFTER `fondo`";
		$this->db->simple_query($query);
		
		$query="ALTER TABLE `cdisp` ADD COLUMN `ffinal` DATE NULL DEFAULT NULL AFTER `fanulado`";
		$this->db->simple_query($query);
	}
}
?>
