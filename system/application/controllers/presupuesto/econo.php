<?php
require_once(BASEPATH.'application/controllers/presupuesto/common.php');
class econo extends Common {
	var $titp='Economias';
	var $tits='Economia';
	var $url ='presupuesto/econo/';
	function econo(){
		parent::Controller();
		$this->load->library("rapyd");
		//$this->datasis->modulo_id(216,1);
	}
	function index(){
		redirect($this->url."filteredgrid");
	}
	function filteredgrid(){
		$this->rapyd->load('datafilter','datagrid');

		$filter = new DataFilter($this->titp, 'econo');

		$filter->numero = new inputField('Numero','numero');
		$filter->numero->rule      ='max_length[11]';
		$filter->numero->size      =13;
		$filter->numero->maxlength =11;

		$filter->fecha = new dateField('Fecha','fecha');
		$filter->fecha->rule      ='chfecha';
		$filter->fecha->size      =10;
		$filter->fecha->maxlength =8;

		$filter->total = new inputField('Total','total');
		$filter->total->rule      ='max_length[19]|numeric';
		$filter->total->css_class ='inputnum';
		$filter->total->size      =21;
		$filter->total->maxlength =19;

		$filter->status = new dropDownField('Estado','status');
		$filter->status->option('','');
		$filter->status->option('P','Pendiente');
		$filter->status->option('C','Finalizado');
		$filter->status->option('A','Anulado');

		$filter->buttons('reset', 'search');
		$filter->build();

		$uri = anchor($this->url.'dataedit/show/<raencode><#numero#></raencode>','<#numero#>');

		function status($status=''){
			switch($status){
				case 'C':$status="Ejecutado";break;
				case 'A':$status="Anulado";break;
				case 'P':$status="Pendiente";break;
			}
			return $status;
		}

		$grid = new DataGrid('');
		$grid->order_by('numero');
		$grid->per_page = 40;
		$grid->use_function('status');

		$grid->column_orderby('Numero'      ,"$uri"                                        ,'numero'   ,'align="left"');
		$grid->column_orderby('Fecha'       ,"<dbdate_to_human><#fecha#></dbdate_to_human>",'fecha'    ,'align="center"');
		$grid->column_orderby('Concepto'    ,"concepto"                                    ,'concepto' ,'align="left"');
		$grid->column_orderby('total'       ,"<nformat><#total#></nformat>"                ,'total'    ,'align="right"');
		$grid->column_orderby('status'      ,"<status><#status#></status>"                 ,'status'   ,'align="left"');
		$grid->column_orderby('fondo'       ,"fondo"                                       ,'fondo'    ,'align="left"');

		$grid->add($this->url.'dataedit/create');
		$grid->build();

		$data['filtro']  = $filter->output;
		$data['content'] = $grid->output;
		$data['head']    = $this->rapyd->get_head().script('jquery.js');
		$data['title']   = $this->titp;
		$this->load->view('view_ventanas', $data);

	}
	function dataedit(){
		$this->rapyd->load('dataobject','datadetails');
		
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
				'denominacion'=>'itdenomi_<#i#>'),
			'p_uri'=>array(4=>'<#i#>',5=>'<#fondo#>'),
			'where'   =>'fondo =<#fondo#>',
			//'script'  =>array('cal_soli()'),
			'titulo'  =>'Busqueda de partidas');
		$btn=$this->datasis->p_modbus($modbus,'<#i#>/<#fondo#>');
		$btn='<img src="'.base_url().'assets/default/images/system-search.png"  alt="Busqueda de partidas" title="Busqueda de partidas" border="0" onclick="modbusdepen(<#i#>)"/>';
		
		$do = new DataObject("econo");
		$do->rel_one_to_many('itecono', 'itecono', array('numero'=>'numero'));
		$do->rel_pointer('itecono','v_presaldo' ,'itecono.codigoadm=v_presaldo.codigoadm AND itecono.fondo=v_presaldo.fondo AND itecono.codigopres=v_presaldo.codigo ',"v_presaldo.denominacion as denomi");

		$edit = new DataDetails($this->tits, $do);
		$edit->back_url = site_url($this->url."filteredgrid");
		$edit->set_rel_title('itecono','Rubro <#o#>');

		$edit->post_process('insert','_post_insert');
		$edit->post_process('update','_post_update');
		$edit->post_process('delete','_post_delete');
		$edit->pre_process('insert'  ,'_valida');
		$edit->pre_process('update'  ,'_valida');

		$edit->numero       = new inputField("N&uacute;mero", "numero");
		//$edit->numero->rule = "callback_chexiste";
		$edit->numero->mode="autohide";
		$edit->numero->when=array('show');

		$edit->fecha= new  dateonlyField("Fecha",  "fecha","d/m/Y");
		$edit->fecha->insertValue = date('Y-m-d');
		$edit->fecha->size        = 12;
		$edit->fecha->mode        = "autohide";
		//$edit->fecha->when        =array('show');
		
		$edit->concepto = new textareaField('Concepto','concepto');
		$edit->concepto->cols = 70;
		$edit->concepto->rows = 4;

		$edit->total = new inputField('total','total');
		$edit->total->rule='max_length[19]|numeric';
		$edit->total->css_class='inputnum';
		$edit->total->size =21;
		$edit->total->maxlength =19;
		$edit->total->readonly=true;

		$edit->status = new dropDownField('Estado','status');
		$edit->status->option('','');
		$edit->status->option('P','Pendiente');
		$edit->status->option('C','Finalizado');
		$edit->status->option('A','Anulado');
		$edit->status->when= array('show');

		$edit->fondo = new dropdownField("F. Financiamiento","fondo");
		$edit->fondo->rule   ='required';
		$edit->fondo->db_name='fondo';
		$edit->fondo->options("SELECT tipo,tipo a  FROM presupuesto WHERE tipo<>'' GROUP BY tipo ORDER BY tipo desc");
		$edit->fondo->style="width:100px;";
		
		//**************************INICIO DETALLE DE ASIGNACIONES  *****************************************************
		$edit->itcodigoadm = new inputField("Estructura	Administrativa","itcodigoadm_<#i#>");
		$edit->itcodigoadm->db_name      ='codigoadm';
		$edit->itcodigoadm->rel_id       ='itecono';
		$edit->itcodigoadm->rule         ='required';
		$edit->itcodigoadm->size         =10;
		$edit->itcodigoadm->autocomplete =false;
		 		
		$edit->itcodigopres = new inputField("(<#o#>) ", "itcodigopres_<#i#>");
		$edit->itcodigopres->rule         ='required|callback_itorden';
		$edit->itcodigopres->size         =15;
		$edit->itcodigopres->db_name      ='codigopres';
		$edit->itcodigopres->rel_id       ='itecono';
		$edit->itcodigopres->autocomplete =false;
		$edit->itcodigopres->append($btn);
				
		$edit->itdenomi= new inputField("(<#o#>) Denominacion","itdenomi_<#i#>");
		//$edit->itdenomi->rule   ='required';
		$edit->itdenomi->db_name  ='denomi';
		$edit->itdenomi->rel_id   ='itecono';
		$edit->itdenomi->pointer  =true;
		$edit->itdenomi->size     =40;
		$edit->itdenomi->readonly =true;
		
		$edit->itmonto = new inputField("(<#o#>) Monto", 'itmonto_<#i#>');
		$edit->itmonto->db_name   ='monto';
		$edit->itmonto->size      = 10;
		$edit->itmonto->rel_id    ='itecono';
		$edit->itmonto->css_class ='inputnum';
		$edit->itmonto->onchange = 'cal_total();';
		
		$status=$edit->get_from_dataobjetct('status');
		if($status=='P'){
			$action = "javascript:window.location='" .site_url($this->url.'actualizar/'.$edit->rapyd->uri->get_edited_id()). "'";
			$edit->button_status("btn_status",'Actualizar',$action,"TR","show");
			$edit->buttons("modify","delete","save");
		}elseif($status=='C'){
			$action = "javascript:window.location='" .site_url($this->url.'reversar/'.$edit->rapyd->uri->get_edited_id()). "'";
			$edit->button_status("btn_rever",'Reversar',$action,"TR","show");        
		}else{
			$edit->buttons("save");
		}

		$edit->buttons('add_rel','add','modify', 'save', 'undo', 'delete', 'back');
		$edit->build();
		
		
		$smenu['link']   = barra_menu('317');
		$data['smenu']   = $this->load->view('view_sub_menu', $smenu,true);
		$conten["form"]  =&  $edit;
		$data['content'] = $this->load->view('view_econo', $conten,true);
		$data["head"]    = script('jquery.js').script('jquery-ui.js').script('plugins/jquery.numeric.pack.js').script('plugins/jquery.floatnumber.js').script('plugins/jquery.meiomask.js').style('vino/jquery-ui.css').$this->rapyd->get_head();//.script('plugins/jquery.autocomplete.js').style('jquery.autocomplete.css')
		$data['title']   = $this->tits;
		$this->load->view('view_ventanas', $data);

	}
	
	function _valida($do){
		$error  = '';
		$numero = $do->get('numero');
		$fondo  = $do->get('fondo');
		$do->set('status','P');
		
		if(empty($numero)){
			$ntransac = $this->datasis->fprox_numero('ntransac');
			$do->set('numero','_'.$ntransac);
			$do->pk    =array('numero'=>'_'.$ntransac);
			$importes=array(); $ivas=array();
			for($i=0;$i < $do->count_rel('itecono');$i++){
				$do->set_rel('itecono','numero','_'.$ntransac,$i);
			}
		}
		
		$importes=array();$total=0;
		for($i=0;$i < $do->count_rel('itecono');$i++){
			$do->set_rel('itecono','fondo'     ,$fondo,$i);
			$codigoadm    = $do->get_rel('itecono','codigoadm' ,$i);
			$codigopres   = $do->get_rel('itecono','codigopres',$i);
			$total+=$monto= $do->get_rel('itecono','monto'      ,$i);
		}
		
		$do->set('total',$total);
		
		if(!empty($error)){
			$do->error_message_ar['pre_ins']=$error;
			$do->error_message_ar['pre_upd']=$error;
			return false;
		}
	}
	
	function actualizar($numero){
		$this->rapyd->load('dataobject');
		
		$do = new DataObject("econo");
		$do->rel_one_to_many('itecono', 'itecono', array('numero'=>'numero'));
		$do->load($numero);
		
		$sta=$do->get('status');
		
		$error    ='';
		
		if($sta=='P'){
			for($i=0;$i < $do->count_rel('itecono');$i++){
				$codigopres       = $do->get_rel('itecono','codigopres' ,$i);
				$monto            = $do->get_rel('itecono','monto'      ,$i);
				$codigoadm        = $do->get_rel('itecono','codigoadm'  ,$i);
				$fondo            = $do->get_rel('itecono','fondo'      ,$i);
				
				$error.=$this->chequeapresup($codigoadm,$fondo,$codigopres,null,$monto,0,'round($monto,2) > round(($comprometido-$causado),2)',"El Monto ($monto) es mayor al MONTO DE COMPROMETIDO POR CAUSAR para la partida ($codigoadm) ($fondo) ($codigopres)");
			}
			
			if(empty($error)){
				for($i=0;$i < $do->count_rel('itecono');$i++){
					$codigopres       = $do->get_rel('itecono','codigopres' ,$i);
					$monto            = $do->get_rel('itecono','monto'      ,$i);
					$codigoadm        = $do->get_rel('itecono','codigoadm'  ,$i);
					$fondo            = $do->get_rel('itecono','fondo'      ,$i);
					
					$error.=$this->afectapresup($codigoadm,$fondo,$codigopres,null,$monto,0, -1 ,array('comprometido'));
				}
				
				
			}
		}else{
			$error.="<div class='alert'><p>No se puede realizar la operacion para esta economia</p></div>";
		}
		
		if(empty($error)){
			if(strpos($numero,'_')===0){
				$contador = $this->datasis->fprox_numero('necono');
				$do->set('numero',$contador);
				
				for($i=0;$i < $do->count_rel('itecono');$i++){
					$do->set_rel('itecono','id'    ,''       ,$i);
					$do->set_rel('itecono','numero',$contador,$i);
				}
				$this->db->query("DELETE FROM itecono WHERE numero='$numero'");
				$numero=$contador;
			}
			
			if(empty($error)){
				$do->set('status','C');
				$do->save();
			}

			logusu('econo',"actualizo economia numero $numero");
			redirect($this->url."/dataedit/show/$numero");
		}else{
			logusu('econo',"actualizo economia numero $numero con error $error");
			$data['content'] = $error.anchor($this->url."/dataedit/show/$numero",'Regresar');
			$data['title']   = " Economias ";
			$data["head"]    = $this->rapyd->get_head();
			$this->load->view('view_ventanas', $data);
		}
	}
	
	function reversar($numero){
		$this->rapyd->load('dataobject');
		
		$do = new DataObject("econo");
		$do->rel_one_to_many('itecono', 'itecono', array('numero'=>'numero'));
		$do->load($numero);
		
		$sta=$do->get('status');
		
		$error    ='';
		
		if($sta=='C'){
			for($i=0;$i < $do->count_rel('itecono');$i++){
				$codigopres       = $do->get_rel('itecono','codigopres' ,$i);
				$monto            = $do->get_rel('itecono','monto'      ,$i);
				$codigoadm        = $do->get_rel('itecono','codigoadm'  ,$i);
				$fondo            = $do->get_rel('itecono','fondo'      ,$i);
				
				$error.=$this->chequeapresup($codigoadm,$fondo,$codigopres,null,$monto,0,'round($monto,2) > round(($presupuesto-$comprometido),2)',"El Monto ($monto) es mayor al MONTO DISPONIBLE para la partida ($codigoadm) ($fondo) ($codigopres)");
			}
			
			if(empty($error)){
				for($i=0;$i < $do->count_rel('itecono');$i++){
					$codigopres       = $do->get_rel('itecono','codigopres' ,$i);
					$monto            = $do->get_rel('itecono','monto'      ,$i);
					$codigoadm        = $do->get_rel('itecono','codigoadm'  ,$i);
					$fondo            = $do->get_rel('itecono','fondo'      ,$i);
					
					$error.=$this->afectapresup($codigoadm,$fondo,$codigopres,null,$monto,0, 1 ,array('comprometido'));
				}
				
				if(empty($error)){
					$do->set('status','P');
					$do->save();
				}
			}
		}else{
			$error.="<div class='alert'><p>No se puede realizar la operacion para esta economia</p></div>";
		}
		
		if(empty($error)){
			logusu('econo',"reverso economia numero $numero");
			redirect($this->url."/dataedit/show/$numero");
		}else{
			logusu('econo',"reverso economia numero $numero con error $error");
			$data['content'] = $error.anchor($this->url."/dataedit/show/$numero",'Regresar');
			$data['title']   = " Economias ";
			$data["head"]    = $this->rapyd->get_head();
			$this->load->view('view_ventanas', $data);
		}
	}

	function _post_insert($do){
		$primary =implode(',',$do->pk);
		logusu($do->table,"Creo $this->tits $primary ");
	}
	function _post_update($do){
		$primary =implode(',',$do->pk);
		logusu($do->table,"Modifico $this->tits $primary ");
	}
	function _post_delete($do){
		$primary =implode(',',$do->pk);
		logusu($do->table,"Elimino $this->tits $primary ");
	}

	function instalar(){
		$mSQL="CREATE TABLE `econo` (
		  `numero` varchar(11) NOT NULL DEFAULT '',
		  `fecha` date DEFAULT NULL,
		  `concepto` TEXT NULL,
		  `total` decimal(19,2) DEFAULT '0.00',
		  `status` char(2) DEFAULT 'P',
		  `fondo` varchar(20) DEFAULT NULL,
		  PRIMARY KEY (`numero`)
		) ENGINE=MyISAM DEFAULT CHARSET=utf8";
		$this->db->simple_query($mSQL);
		
		$mSQL="CREATE TABLE `itecono` (
			`id` int(11) NOT NULL AUTO_INCREMENT,
			`numero` varchar(11) DEFAULT NULL,
			`codigoadm` varchar(12) DEFAULT NULL,
			`fondo` varchar(20) DEFAULT NULL,
			`codigopres` varchar(17) DEFAULT NULL,
			`monto` decimal(19,2) DEFAULT NULL,
			PRIMARY KEY (`id`)
		) ENGINE=MyISAM AUTO_INCREMENT=3 DEFAULT CHARSET=utf8";
		$this->db->simple_query($mSQL);
	}

}
?>
