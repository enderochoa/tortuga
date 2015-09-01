<?php
require_once(BASEPATH.'application/controllers/presupuesto/common.php');
class Su_trasla extends Common {
	
	var $url="suministros/su_trasla/";
	var $titp="Traslados de Suministros";
	var $tits="Traslado de Suministros";

	function Su_trasla(){
		parent::Controller();
		$this->load->library("rapyd");
	}
	function index(){
		redirect($this->url."filteredgrid");
	}


	function filteredgrid(){
		//$this->datasis->modulo_id(75,1);
		$this->rapyd->load("datafilter2","datagrid");
		
		$filter = new DataFilter2("","su_trasla");

		$filter->numero = new inputField("N&uacute;mero", "numero");
		$filter->numero->size=15;
		
		$filter->fecha = new dateonlyField("Fecha", "fecha");
		$filter->fecha->dbformat = "Y-m-d";
		$filter->fecha->size=12;
		
		$filter->de = new dropdownField("De","de");
		$filter->de->option("","");
		$filter->de->options("SELECT codigo,descrip FROM su_caub");
		
		$filter->para = new dropdownField("Para","para");
		$filter->para->option("","");
		$filter->para->options("SELECT codigo,descrip FROM su_caub");

		$filter->concepto = new inputField("Concepto", "concepto");
		$filter->concepto->size=40;		
		
		$filter->status = new dropdownField("Estado","status");
		$filter->status->option("","");
		$filter->status->option("P","Sin Ejecutar");
		$filter->status->option("C","Ejecutado");
		$filter->status->style="width:100px";
		
		$filter->buttons("reset","search");    
		$filter->build();
		
		$uri = anchor($this->url.'dataedit/show/<#numero#>','<str_pad><#numero#>|8|0|STR_PAD_LEFT</str_pad>');
		
		function sta($status){
			switch($status){
				case "P":return "Sin Ejecutar";break;
				case "C":return "Ejecutado";break;
			}
		}
		
		$grid = new DataGrid("");
		$grid->order_by("numero","desc");
		$grid->per_page = 20;
		$grid->use_function('substr','str_pad');
		$grid->use_function('sta');
		
		$grid->column_orderby("N&uacute;mero"  ,$uri,"numero");
		$grid->column_orderby("Fecha"          ,"<dbdate_to_human><#fecha#></dbdate_to_human>","fecha"    ,"align='center'"      );
		$grid->column_orderby("De"             ,"de"                                          ,"de"       ,"align='right'"       );
		$grid->column_orderby("Para"           ,"para"                                        ,"para"     ,"align='right'"       );
		$grid->column_orderby("Concepto"       ,"concepto"                                    ,"concepto" ,"align='left'  NOWRAP");
		$grid->column_orderby("Total"          ,"total"                                       ,"total"    ,"align='right'"       );
		$grid->column_orderby("Estado"         ,"<sta><#status#></sta>"                       ,"status"   ,"align='center'NOWRAP");
		
		$grid->add($this->url."dataedit/create");
		$grid->build();
		
		$data['filtro']  = $filter->output;
		$data['content'] = $grid->output;
		$data['script'] = script("jquery.js")."\n";
		$data['title']   = $this->titp;
		$data["head"]    = script("jquery.js").$this->rapyd->get_head();
		$this->load->view('view_ventanas', $data);	
	}
	
	function dataedit(){
		//$this->datasis->modulo_id(75,1);
		$this->rapyd->load('dataobject','datadetails');
		
		$modbus=array(
			'tabla'   =>'view_su_itsumi',
			'columnas'=>array(
				'codigo' =>'C&oacute;digo',
				'descrip'=>'Descripcion',
				'almacen'=>'Almacen',
				'cantidad'=>'Cantidad'),
			'filtro'  =>array('codigo' =>'C&oacute;digo','descrip'=>'Descripcion'),
			'retornar'=>array('codigo'=>'codigo_<#i#>','descrip'=>'descrip2_<#i#>'),
			'p_uri'=>array(4=>'<#i#>',5=>'<#caub#>'),
			'where'=>'alma = <#caub#>',
			'titulo'  =>'Busqueda de Articulos');

		$btn=$this->datasis->p_modbus($modbus,'<#i#>/<#caub#>');
		$btn='<img src="/tortuga/assets/default/images/system-search.png"  alt="Busqueda de Suministros" title="Busqueda de Suministros" border="0" onclick="modbusdepen(<#i#>)"/>';
		
		$do = new DataObject("su_trasla");
		$do->rel_one_to_many('su_ittrasla', 'su_ittrasla', array('numero'=>'numero'));
		$do->rel_pointer('su_ittrasla','sumi' ,'su_ittrasla.codigo=sumi.codigo',"sumi.descrip as descrip2");

		$edit = new DataDetails("Datos de Traslados de Partidas", $do);
		$edit->back_url = site_url($this->url."filteredgrid");
		$edit->set_rel_title('su_ittrasla','Rubro <#o#>');
		/*
		$edit->pre_process('insert'  ,'_mayor');
		$edit->pre_process('update'  ,'_mayor');
		$edit->pre_process('insert'  ,'_valida');
		$edit->pre_process('update'  ,'_valida');
		*/
		$edit->numero   = new inputField("N&uacute;mero", "numero");
		$edit->numero->mode="autohide";
		$edit->numero->when=array('show');
		
		$edit->fecha = new  dateonlyField("Fecha",  "fecha");
		$edit->fecha->insertValue = date('Y-m-d');
		$edit->fecha->size =12;
		
		$edit->de = new dropdownField("De","de");
		$edit->de->options("SELECT codigo,descrip FROM su_caub");

		$edit->para = new dropdownField("Para","para");
		$edit->para->options("SELECT codigo,descrip FROM su_caub");
		
		$edit->concepto = new textareaField("Concepto", "concepto");
		$edit->concepto->rows=4;
		$edit->concepto->cols=80;
		$edit->concepto->rule='required';
		
		$edit->total = new inputField("Total", "total");
		$edit->total->readonly=true;
		$edit->total->size=10;
		$edit->total->css_class  ='inputnum';
		
		$edit->codigo = new inputField("Codigo","codigo_<#i#>");
		$edit->codigo->size         =12;
		$edit->codigo->db_name      ='codigo';
		$edit->codigo->rel_id       ='su_ittrasla';
		$edit->codigo->rule         ='required';
		$edit->codigo->autocomplete =false;
		$edit->codigo->append($btn);
		
		$edit->descrip2 = new inputField("(<#o#>) ", "descrip2_<#i#>");
		$edit->descrip2->size         =50;
		$edit->descrip2->db_name      ='descrip2';
		$edit->descrip2->rel_id       ='su_ittrasla';
		$edit->descrip2->autocomplete =false;
		//$edit->descrip2->readonly     =true;
		$edit->descrip2->pointer      =true;
		
		$edit->cant = new inputField("(<#o#>) ", "cant_<#i#>");
		$edit->cant->rule       ='required|callback_positivo';
		$edit->cant->db_name    ='cant';
		$edit->cant->rel_id     ='su_ittrasla';
		$edit->cant->size       =10;
		$edit->cant->css_class  ='inputnum';
		$edit->cant->onchange   ='cal_total();';
		$edit->cant->insertValue='0';

		$status=$edit->get_from_dataobjetct('status');
		if($status=='P'){
			$action = "javascript:window.location='" .site_url($this->url.'actualizar/'.$edit->rapyd->uri->get_edited_id()). "'";
			$edit->button_status("btn_status",'Actualizar',$action,"TR","show");
			$edit->buttons("modify","save","delete");
		}elseif($status=='C'){
			$action = "javascript:window.location='" .site_url($this->url.'reversar/'.$edit->rapyd->uri->get_edited_id()). "'";
			$edit->button_status("btn_rever",'Anular',$action,"TR","show");
		}else{
			$edit->buttons("save");
		}
		
		$edit->buttons("add","undo","back","add_rel"); // 
		$edit->build();

		$smenu['link']=barra_menu('124');
		$data['smenu'] = $this->load->view('view_sub_menu', $smenu,true);
		$conten["form"]  =&  $edit;

		$data['content'] = $this->load->view('view_su_trasla', $conten,true); 
		$data['title']   = "Traslados de Partidas";
		$data["head"]    = script('jquery.js').script('jquery-ui.js').script("plugins/jquery.numeric.pack.js").script('plugins/jquery.meiomask.js').style('vino/jquery-ui.css').$this->rapyd->get_head();
		$this->load->view('view_ventanas', $data);
	}
	
	function actualizar($id){
		$this->_actsumi($id,'+','C');
	}
	
	function reversar($id){
		$this->_actsumi($id,'-','P');
	}
	
	function _actsumi($id,$oper,$status){
		$this->rapyd->load('dataobject');
		
		if($oper=='-')
		$oper2='+';
		else
		$oper2='-';
		
		$error="";
		$do = new DataObject("su_trasla");
		$do->rel_one_to_many('su_ittrasla', 'su_ittrasla', array('numero'=>'numero'));
		$do->load($id);
		$de   = $do->get('de');
		$dee  =$this->db->escape($de);
		$para = $do->get('para');
		$parae=$this->db->escape($para);
		
		$sta    = $do->get('sta');
		
		if($sta!=$status){
			for($i=0;$i < $do->count_rel('su_ittrasla');$i++){
				$codigo  = $do->get_rel('su_ittrasla','codigo'   ,$i);
				$cantidad= $do->get_rel('su_ittrasla','cant'     ,$i);
				
				$cantidad=1*$cantidad;
				$codigo=$this->db->escape($codigo);
				
				if(is_numeric($cantidad)){
					
					$c=$this->datasis->dameval("SELECT cantidad FROM su_itsumi WHERE codigo=$codigo AND alma=$dee");
					if($cantidad>$c)
					$error.="La Cantidad en el Almacen $dee es mayor al monto del traslado";
				}else
					$error.='La cantidad no es numerica';
			}
			if(empty($error)){
				for($i=0;$i < $do->count_rel('su_ittrasla');$i++){
					$codigo  = $do->get_rel('su_ittrasla','codigo'   ,$i);
					$cantidad= $do->get_rel('su_ittrasla','cant'     ,$i);
					
					$cantidad=1*$cantidad;
					$codigo=$this->db->escape($codigo);
					
					$this->db->query("INSERT IGNORE INTO su_itsumi (`codigo`,`alma`) value ($codigo,$parae)");
					
					if(is_numeric($cantidad)){
						$this->db->query("UPDATE sumi SET existen=existen $oper $cantidad WHERE codigo=$codigo");
						$this->db->simple_query("UPDATE su_itsumi SET cantidad=cantidad $oper $cantidad WHERE codigo=$codigo AND alma=$parae");
						$this->db->simple_query("UPDATE su_itsumi SET cantidad=cantidad $oper2 $cantidad WHERE codigo=$codigo AND alma=$dee");
					}else
						$error.='La cantidad no es numerica';
					//echo "UPDATE sumi SET existen=existen $oper $cantidad,pond=pond $oper $precio WHERE codigo=$codigo";
					//exit($precio);
				}
				logusu('su_trasla',"Marco Traslado Nro $id como $status");
			}
			
		}else{
			$error.="No se puede realizar la operacion para la nota de recepcion";
		}
		
		if(empty($error)){
			$do->set('status',$status);
			$do->save();
			logusu('su_trasla',"Marco traslado Nro $id como $status");
			redirect($this->url."/dataedit/show/$id");
		}else{
			logusu('su_trasla',"Marco traslado Nro $id como $status . con ERROR:$error");
			$data['content'] = "<div class='alert'>".$error."</div>".anchor($this->url."/dataedit/show/$id",'Regresar');
			$data['title']   = $this->tits;
			$data["head"]    = $this->rapyd->get_head();
			$this->load->view('view_ventanas', $data);
		}
	}
	
	function repetido($partida){
		if(isset($this->__rpartida)){
			if(in_array($partida, $this->__rpartida)){
				$this->validation->set_message('repetido',"El rublo %s ($partida) esta repetido");
				return false;	
			}
		}
		$this->__rpartida[]=$partida;
		return true;
	}
	
	function positivo($valor){
		if ($valor < 0){
			$this->validation->set_message('positivo',"Los campos disminucion y aumento deben ser positivos");
			return FALSE;
		}
		return TRUE;
	}
	
	function _valida($do){
		//$__rpartida = array();
		//
		//$error='';
		//for($i=0;$i < $do->count_rel('ittrasla');$i++){
		//	$ordinal          = '';
		//	$codigopres       = $do->get_rel('ittrasla','codigopres' ,$i);
		//	$monto            = $do->get_rel('ittrasla','monto'      ,$i);
		//	$ordinal          = $do->get_rel('ittrasla','ordinal'    ,$i);
		//	$codigoadm        = $do->get_rel('ittrasla','codigoadm'  ,$i);
		//	$fondo            = $do->get_rel('ittrasla','fondo'      ,$i);
		//	
		//	$error.=$this->itpartida($codigoadm,$fondo,$codigopres,$ordinal);
		//				
		//	if(in_array($codigopres.$fondo.$codigopres.$ordinal, $__rpartida)){
		//		$error.="La partida ($codigopres) ($fondo) ($codigoadm) ($ordinal) Esta repetida";
		//	}
                //
		//	$__rpartida[]=$codigoadm.$fondo.$codigopres.$ordinal;
		//}
		//
		//if(!empty($error)){
		//	$do->error_message_ar['pre_ins']=$error;
		//	$do->error_message_ar['pre_upd']=$error;
		//	return false;
		//}
		
	}
	
	
	function instalar(){
		$query="CREATE TABLE `su_ittrasla` (
			`id` INT(11) NOT NULL AUTO_INCREMENT,
			`numero` INT(11) UNSIGNED NULL DEFAULT NULL,
			`codigo` VARCHAR(4) NULL DEFAULT NULL,
			`cant` DECIMAL(19,2) NULL DEFAULT '0.00',
			PRIMARY KEY (`id`)
		)
		COLLATE='utf8_general_ci'
		ENGINE=MyISAM
		ROW_FORMAT=DEFAULT";
		$this->db->simple_query($query);
		
		$query="CREATE TABLE `su_trasla` (
			`numero` INT(11) NOT NULL AUTO_INCREMENT,
			`fecha` DATE NULL DEFAULT NULL,
			`concepto` VARCHAR(200) NULL DEFAULT NULL,
			`total` DECIMAL(19,2) NULL DEFAULT '0.00',
			`status` CHAR(1) NULL DEFAULT 'P',
			`de` VARCHAR(4) NULL DEFAULT NULL,
			`para` VARCHAR(4) NULL DEFAULT NULL,
			PRIMARY KEY (`numero`)
		)
		COLLATE='utf8_general_ci'
		ENGINE=MyISAM
		ROW_FORMAT=DEFAULT";
		
		$this->db->simple_query($query);
	}
} 
?>
