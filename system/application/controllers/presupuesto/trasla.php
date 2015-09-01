<?php
require_once(BASEPATH.'application/controllers/presupuesto/common.php');
class trasla extends Common {

	function trasla(){
		parent::Controller();
		$this->load->library("rapyd");
		$this->formatopres=$this->datasis->traevalor('FORMATOPRES');
		$this->flongpres  =strlen(trim($this->formatopres));
		$this->formatoadm=$this->datasis->traevalor('FORMATOESTRU');
		$this->flongadm  =strlen(trim($this->formatoadm));
	}
	function index(){
		redirect("presupuesto/trasla/filteredgrid");
	}


	function filteredgrid(){
		$this->datasis->modulo_id(75,1);
		$this->rapyd->load("datafilter2","datagrid");
		
		$script='
		$(function() {
				$("#estadmin").change(function(){
					$.post("'.site_url('presupuesto/presupuesto/get_tipo').'",{ codigoadm:$("#estadmin").val() },function(data){$("#fondo").html(data);})
				});
		});
		';
		
		$filter = new DataFilter2("","trasla");
		
		$filter->script($script);

		$filter->numero = new inputField("N&uacute;mero", "numero");
		$filter->numero->size=15;
		
		$filter->nrooficio = new inputField("Decreto", "nrooficio");
		$filter->nrooficio->size=15;
		
		$filter->fecha = new dateonlyField("Fecha", "fecha");
		$filter->fecha->dbformat = "Y-m-d";
		$filter->fecha->size=12;

		$filter->motivo = new inputField("Motivo", "motivo");
		$filter->motivo->size=40;		
		
		$filter->status = new dropdownField("Estado","status");
		$filter->status->option("","");
		$filter->status->option("P","Sin Ejecutar");
		$filter->status->option("C","Ejecutado");
		$filter->status->style="width:100px";
		
		$filter->buttons("reset","search");    
		$filter->build();
		
		$uri = anchor('presupuesto/trasla/dataedit/show/<#numero#>','<str_pad><#numero#>|8|0|STR_PAD_LEFT</str_pad>');
		
		function sta($status){
			switch($status){
				case "P":return "Sin Ejecutar";break;
				case "C":return "Ejecutado";break;
				//case "O":return "Ordenado Pago";break;
				case "A":return "Anulado";break;
			}
		}
		
		$grid = new DataGrid("");
		$grid->order_by("numero","desc");
		$grid->per_page = 20;
		$grid->use_function('substr','str_pad');
		$grid->use_function('sta');
		
		$grid->column_orderby("N&uacute;mero"  ,$uri,"numero");
		$grid->column_orderby("Decreto"           ,"nrooficio"                                   ,"nrooficio"  );
		$grid->column_orderby("Fecha"          ,"<dbdate_to_human><#fecha#></dbdate_to_human>","fecha"    ,"align='center'"      );		
		$grid->column_orderby("Motivo"         ,"<#motivo#>|50|\n|true</wordwrap>","motivo"   ,"align='left'  ");
		$grid->column_orderby("Disminucion"       ,"tdisminucion"                             ,"tdisminucion"      ,"align='right'" );
		$grid->column_orderby("Aumento"            ,"taumento"                                ,"taumento"      ,"align='right'" );
		$grid->column_orderby("Estado"         ,"<sta><#status#></sta>"                       ,"status"   ,"align='center'");
		//$grid->column("E. Administrativa."   ,"estadmin");
		//$grid->column("Fondo"             ,"fondo");
		
		$grid->add("presupuesto/trasla/dataedit/create");
		$grid->build();
		
		$data['filtro']  = $filter->output;
		$data['content'] = $grid->output;
		$data['script'] = script("jquery.js")."\n";
		$data['title']   = "Traslados de Partidas";
		//$data['content'] = $filter->output.$grid->output;
		//$data['title']   = " Traslado de Partidas ";
		$data["head"]    = script("jquery.js").$this->rapyd->get_head();
		$this->load->view('view_ventanas', $data);	
	}
	
	function dataedit(){
		$this->datasis->modulo_id(75,1);
		$this->rapyd->load('dataobject','datadetails');
		
		$partidaiva=$this->datasis->traevalor('PARTIDAIVA');
    		
	 $modbus=array(
			'tabla'   =>'v_presaldo',
			'columnas'=>array(
				'codigoadm'   =>'Est. Admin',
				'fondo'       =>'Fondo',
				'codigo'      =>'Partida',
				'ordinal'     =>'Ordinal',
				'denominacion'=>'Denominaci&oacute;n',
				'saldo'       =>'Saldo'
				),
			'filtro'  =>array(
				'codigoadm'   =>'Est. Admin',
				'fondo'       =>'Fondo',
				'codigo'      =>'Partida',
				'ordinal'     =>'Ord',
				'denominacion'=>'Denominaci&oacute;n'
				),
			'retornar'=>array(
				'codigoadm'   =>'codigoadm_<#i#>',
				'fondo'       =>'fondo_<#i#>',
				'codigo'      =>'codigopres_<#i#>',
				'ordinal'     =>'ordinal_<#i#>',
				'denominacion'=>'denomi_<#i#>'),
			'where'=>'movimiento = "S"',
			'p_uri'=>array(4=>'<#i#>',),
			'titulo'  =>'Busqueda de partidas');
		$btn=$this->datasis->p_modbus($modbus,'<#i#>');

		$do = new DataObject("trasla");
		$do->rel_one_to_many('ittrasla', 'ittrasla', array('numero'=>'numero'));
		//$do->rel_pointer('ittrasla','v_presaldo' ,'ittrasla.codigoadm=v_presaldo.codigoadm AND ittrasla.fondo=v_presaldo.fondo AND ittrasla.codigopres=v_presaldo.codigo AND ittrasla.ordinal=IF(ittrasla.ordinal>0,v_presaldo.ordinal,ittrasla.ordinal)',"v_presaldo.denominacion as denomi2");
		$do->order_by('ittrasla','ittrasla.id','asc');

		$edit = new DataDetails("Datos de Traslados de Partidas", $do);
		$edit->back_url = site_url("presupuesto/trasla/filteredgrid");
		$edit->set_rel_title('ittrasla','Rubro <#o#>');
		
		$edit->pre_process('insert'  ,'_mayor');
		$edit->pre_process('update'  ,'_mayor');
		$edit->pre_process('insert'  ,'_valida');
		$edit->pre_process('update'  ,'_valida');
		
		$edit->numero   = new inputField("N&uacute;mero", "numero");
		$edit->numero->mode="autohide";
		$edit->numero->when=array('show');
		
		$edit->fecha = new  dateonlyField("Fecha",  "fecha");
		$edit->fecha->insertValue = date('Y-m-d');
		$edit->fecha->size =12;
		
		$edit->nrooficio = new inputField("N&uacute;mero de Decreto", "nrooficio");
		$edit->nrooficio->size =12;
		
		$edit->resolu = new inputField("Resoluci&oacute;n", "resolu");
		$edit->resolu->size =40;
		
		$edit->fresolu = new  dateonlyField("Fecha Resoluci&oacute;n",  "fresolu");
		$edit->fresolu->insertValue = date('Y-m-d');
		$edit->fresolu->size =12;
		
		$edit->motivo = new textareaField("Motivo", "motivo");
		$edit->motivo->rows=4;
		$edit->motivo->cols=80;
		$edit->motivo->rule='required';
		
		$edit->fondo = new dropdownField("Fondo","fondo_<#i#>");
		$edit->fondo->size   =10;
		$edit->fondo->rule   ='required';
		$edit->fondo->db_name='fondo';
		$edit->fondo->rel_id ='ittrasla';
		$edit->fondo->options("SELECT tipo,tipo a  FROM presupuesto WHERE tipo<>'' GROUP BY tipo ORDER BY tipo desc");
		$edit->fondo->style="width:100px;";
		
		$edit->codigoadm = new inputField("Estructura	Administrativa","codigoadm_<#i#>");
		$edit->codigoadm->size         =12;
		$edit->codigoadm->db_name      ='codigoadm';
		$edit->codigoadm->rel_id       ='ittrasla';
		$edit->codigoadm->rule         ='required';
		$edit->codigoadm->autocomplete =false; 
		
		$edit->codigopres = new inputField("(<#o#>) Partida", "codigopres_<#i#>");
		$edit->codigopres->rule='required';
		$edit->codigopres->size=15;
		$edit->codigopres->append($btn);
		$edit->codigopres->db_name='codigopres';
		$edit->codigopres->rel_id ='ittrasla';
		$edit->codigopres->autocomplete =false;
		//$edit->partida->readonly =true;
		
		$edit->ordinal = new inputField("(<#o#>) Ordinal", "ordinal_<#i#>");
		$edit->ordinal->db_name  ='ordinal';
		$edit->ordinal->maxlength=1;
		$edit->ordinal->size     =5;		
		$edit->ordinal->rel_id   ='ittrasla';

		$edit->denomi = new textareaField("(<#o#>) Denominaci&oacute;n", "denomi_<#i#>");
		$edit->denomi->db_name ='denomi2';
		$edit->denomi->rel_id  ='ittrasla';
		$edit->denomi->cols    =20;
		$edit->denomi->rows    =1;
		$edit->denomi->readonly=true;
		$edit->denomi->pointer =true;

		$edit->disminucion = new inputField("(<#o#>) disminucion", "disminucion_<#i#>");
		$edit->disminucion->rule='required|callback_positivo';
		$edit->disminucion->db_name='disminucion';
		$edit->disminucion->rel_id ='ittrasla';
		$edit->disminucion->size=15;
		$edit->disminucion->css_class='inputnum';
		$edit->disminucion->onchange ='cal_totald(<#i#>);';
		$edit->disminucion->insertValue='0';

		$edit->aumento = new inputField("(<#o#>) aumento", "aumento_<#i#>");
		$edit->aumento->rule='required|callback_positivo';
		$edit->aumento->db_name='aumento';
		$edit->aumento->rel_id ='ittrasla';
		$edit->aumento->size=15;
		$edit->aumento->css_class='inputnum';
		$edit->aumento->onchange ='cal_totala(<#i#>);';
		$edit->aumento->insertValue='0';
		
		$edit->tdisminucion = new inputField("Total Disminuci&oacute;n", "tdisminucion");
		$edit->tdisminucion->css_class='inputnum';
		$edit->tdisminucion->readonly =true;
		$edit->tdisminucion->rule     ='numeric';
		$edit->tdisminucion->size     =15;
		$edit->tdisminucion->insertValue='0';
		
		$edit->taumento = new inputField("Total Aumento", "taumento");
		$edit->taumento->css_class='inputnum';
		$edit->taumento->readonly =true;
		$edit->taumento->rule     ='numeric';
		$edit->taumento->size     =15;
		$edit->taumento->insertValue='0';
		
		$status=$edit->get_from_dataobjetct('status');
		if($status=='P'){
			$action = "javascript:window.location='" .site_url('presupuesto/trasla/actualizar/'.$edit->rapyd->uri->get_edited_id()). "'";
			$edit->button_status("btn_status",'Actualizar',$action,"TR","show");
			$action = "javascript:window.location='" .site_url('presupuesto/trasla/apartar/'.$edit->rapyd->uri->get_edited_id()). "'";
			$edit->button_status("btn_apartar",'Apartar',$action,"TR","show");
			$edit->buttons("delete","modify","save");
		}elseif($status=='C'){
			$action = "javascript:window.location='" .site_url('presupuesto/trasla/reversar/'.$edit->rapyd->uri->get_edited_id()). "'";
			$edit->button_status("btn_rever",'Anular',$action,"TR","show");
		}elseif($status=='H'){
			$action = "javascript:window.location='" .site_url('presupuesto/trasla/desapartar/'.$edit->rapyd->uri->get_edited_id()). "'";
			$edit->button_status("btn_desap",'Desapartar',$action,"TR","show");
		}else{
			$edit->buttons("save");
		}
		
		$edit->buttons("add","undo","back","add_rel"); //"modify", "save",  "delete", 
		$edit->build();

		$smenu['link']=barra_menu('331');
		$data['smenu'] = $this->load->view('view_sub_menu', $smenu,true);
		$conten["form"]  =&  $edit;

		$data['content'] = $this->load->view('view_trasla', $conten,true); 
		$data['title']   = "Traslados de Partidas";
		$data["head"]    = script('jquery.js').script('jquery-ui.js').script("plugins/jquery.numeric.pack.js").script('plugins/jquery.meiomask.js').style('vino/jquery-ui.css').$this->rapyd->get_head();
		$this->load->view('view_ventanas', $data);
	}
	
	function _mayor($do){
		//print_r($do);
		
		$error='';
		$taumento=$tdisminucion=0;
		for($i=0;$i < $do->count_rel('ittrasla');$i++){
			$partida     = $do->get_rel('ittrasla','codigopres'  ,$i);
			$aumento     = $do->get_rel('ittrasla','aumento'  ,$i);
			$disminucion = $do->get_rel('ittrasla','disminucion' ,$i);
			
			$taumento+=$aumento;
			$tdisminucion+=$disminucion;
			
			if($disminucion*$aumento==0){
				if($aumento<0 or $disminucion<0){
					$error.="<div class='alert'><p>Ni disminucion ni aumento pueden ser negativos</p></div>";
				}
			}else{
				$error.="<div class='alert'><p>Error en la partida $partida: Uno de los campos disminucion o aumento debe contener un valor positivo y el otro cero(0)</p></div>";
				
			}
		}
		if($tdisminucion!=$taumento){
			$error.="<div class='alert'><p>El Total de disminuciones no puede ser diferente al total de aumentos</p></div>";
		}
		if(empty($error)){
			$do->set('taumento'    ,$taumento);
			$do->set('tdisminucion',$tdisminucion);
		}else{
			$do->error_message_ar['pre_ins']=$error;
			$do->error_message_ar['pre_upd']=$error;
			return false;
		}
	}
	
	
	function actualizar($id){
		$this->actpresup($id);
	}
	
	function reversar($id){
		$this->actpresup($id);
	}
	
	function apartar($id){
		$this->actpresup2($id,'H');
	}
	
	function desapartar($id){
		$this->actpresup2($id,'I');
	}
	
	function actpresup($id,$apartar=null){
		$this->rapyd->load('dataobject');
		
		$do = new DataObject("trasla");
		$do->rel_one_to_many('ittrasla', 'ittrasla', array('numero'=>'numero'));
		$do->load($id);
		
		$sta=$do->get('status');
		if($sta=='P' || $sta=='C' || $sta=='H'){
			$tdisminucion = $do->get('tdisminucion'); 
			$taumento     = $do->get('taumento'    );	
			
			$error='';
			if($tdisminucion==$taumento){
				$montos=array();
				for($i=0;$i < $do->count_rel('ittrasla');$i++){
					$ordinal          = $do->get_rel('ittrasla','ordinal'    ,$i);
					$codigopres       = $do->get_rel('ittrasla','codigopres' ,$i);
					$codigoadm        = $do->get_rel('ittrasla','codigoadm'  ,$i);
					$fondo            = $do->get_rel('ittrasla','fondo'      ,$i);
					$disminucion      = $do->get_rel('ittrasla','disminucion',$i);
					$aumento          = $do->get_rel('ittrasla','aumento'    ,$i);
					$monto            = $aumento-$disminucion;
					if($sta=='C')$monto=-1*$monto;
					$error.=$this->itpartida($codigoadm,$fondo,$codigopres,$ordinal);
					
					$cadena = $codigoadm.'_._'.$fondo.'_._'.$codigopres.'_._'.$ordinal;
					if(array_key_exists($cadena,$montos)){
						$montos[$cadena]    +=$monto;
					}else{
						$montos[$cadena]     =$monto;
					}
				}
			
				if(empty($error)){
					foreach($montos AS $cadena=>$monto){
						$temp  = explode('_._',$cadena);
						if($monto<0)
						$error.= $this->chequeapresup($temp[0],$temp[1],$temp[2],$temp[3],abs($monto),0,'round($monto,2) > $disponible=round(($presupuesto-($comprometido+$apartado)),2)','El Monto ($monto) es mayor al disponible ($disponible) para la partida ('.$temp[0].') ('.$temp[1].') ('.$temp[2].') ('.$temp[3].')') ;
					}
				}
				
				if(empty($error)){
					foreach($montos AS $cadena=>$monto){
						$temp  = explode('_._',$cadena);
						$error.=$this->afectapresup($temp[0],$temp[1],$temp[2],$temp[3],$monto,0, 1 ,array("traslados"));
					}
				}
			}else{
				$error.="<div class='alert'><p>No se puede realizar la Transferencia si El total de Disminuci&oacute;nes ($tdisminucion) es Diferente al total de Aumentos ($taumento) </p></div>";
			}
			
			if(empty($error)){
				if($sta=='P'){
					$do->set('status' ,'C');
					$do->set('ftrasla',date('Ymd'));
					logusu('trasla',"Actualizo traslado $id");
				}
				
				if($sta=='C'){
					$do->set('status' ,'A');
					logusu('trasla',"Anulo traslado $id");
				}
				
				$do->save();
			}
		}else{
			$error.="<div class='alert'><p>No se puede realizar la operacion para la transferencia $id </p></div>";
		}
		
		if(empty($error)){
			redirect("presupuesto/trasla/dataedit/show/$id");
		}else{
			$data['content'] = $error.anchor("presupuesto/trasla/dataedit/show/$id",'Regresar');
			$data['title']   = "Traslados";
			$data["head"]    = $this->rapyd->get_head();
			$this->load->view('view_ventanas', $data);
		}
	}


	function actpresup2($id,$apartar=null){
		$this->rapyd->load('dataobject');
		
		$do = new DataObject("trasla");
		$do->rel_one_to_many('ittrasla', 'ittrasla', array('numero'=>'numero'));
		$do->load($id);
		
		$sta=$do->get('status');
		if($sta=='P' || $sta=='H'){
			$tdisminucion = $do->get('tdisminucion'); 
			$taumento     = $do->get('taumento'    );	
			
			$error='';
			if($tdisminucion==$taumento){
				$montos=array();
				for($i=0;$i < $do->count_rel('ittrasla');$i++){
					$ordinal          = $do->get_rel('ittrasla','ordinal'    ,$i);
					$codigopres       = $do->get_rel('ittrasla','codigopres' ,$i);
					$codigoadm        = $do->get_rel('ittrasla','codigoadm'  ,$i);
					$fondo            = $do->get_rel('ittrasla','fondo'      ,$i);
					$disminucion      = $do->get_rel('ittrasla','disminucion',$i);
					$aumento          = $do->get_rel('ittrasla','aumento'    ,$i);
					$monto            = $aumento-$disminucion;
					if($sta=='H')$monto=-1*$monto;
					$error.=$this->itpartida($codigoadm,$fondo,$codigopres,$ordinal);
					
					$cadena = $codigoadm.'_._'.$fondo.'_._'.$codigopres.'_._'.$ordinal;
					if(array_key_exists($cadena,$montos)){
						$montos[$cadena]    +=$monto;
					}else{
						$montos[$cadena]     =$monto;
					}
				}
			
				if(empty($error)){
					foreach($montos AS $cadena=>$monto){
						$temp  = explode('_._',$cadena);
						if($monto<0)
						$error.= $this->chequeapresup($temp[0],$temp[1],$temp[2],$temp[3],abs($monto),0,'round($monto,2) > $disponible=round(($presupuesto-($comprometido+$apartado)),2)','El Monto ($monto) es mayor al disponible ($disponible) para la partida ('.$temp[0].') ('.$temp[1].') ('.$temp[2].') ('.$temp[3].')') ;
					}
				}
				
				if(empty($error)){
					foreach($montos AS $cadena=>$monto){
						$temp  = explode('_._',$cadena);
						if($sta=='P'){
							if($monto<0)
							$error.=$this->afectapresup($temp[0],$temp[1],$temp[2],$temp[3],abs($monto),0, 1 ,array("apartado"));
						}elseif($sta=='H'){
							if($monto>0)
							$error.=$this->afectapresup($temp[0],$temp[1],$temp[2],$temp[3],abs($monto),0, -1 ,array("apartado"));
						}
					}
				}
			}else{
				$error.="<div class='alert'><p>No se puede realizar la Transferencia si El total de Disminuci&oacute;nes ($tdisminucion) es Diferente al total de Aumentos ($taumento) </p></div>";
			}
			
			if(empty($error)){
				if($sta=='P'){
					$do->set('status' ,'H');
					logusu('trasla',"Aparto traslado $id");
				}
				
				if($sta=='H'){
					$do->set('status' ,'P');
					logusu('trasla',"desaparto traslado $id");
				}
				$do->save();
			}
		}else{
			$error.="<div class='alert'><p>No se puede realizar la operacion para la transferencia $id </p></div>";
		}
		
		if(empty($error)){
			redirect("presupuesto/trasla/dataedit/show/$id");
		}else{
			$data['content'] = $error.anchor("presupuesto/trasla/dataedit/show/$id",'Regresar');
			$data['title']   = "Traslados";
			$data["head"]    = $this->rapyd->get_head();
			$this->load->view('view_ventanas', $data);
		}
	}
	
	
	function positivo($valor){
		if ($valor < 0){
			$this->validation->set_message('positivo',"Los campos disminucion y aumento deben ser positivos");
			return FALSE;
		}
  	return TRUE;
	}
	
	function _valida($do){
		$__rpartida = array();
		
		$error='';
		for($i=0;$i < $do->count_rel('ittrasla');$i++){
			$ordinal          = '';
			$codigopres       = $do->get_rel('ittrasla','codigopres' ,$i);
			$monto            = $do->get_rel('ittrasla','monto'      ,$i);
			$codigoadm        = $do->get_rel('ittrasla','codigoadm'  ,$i);
			$fondo            = $do->get_rel('ittrasla','fondo'      ,$i);
			
			$error.=$this->itpartida($codigoadm,$fondo,$codigopres,$ordinal);

			if(in_array($codigoadm.$fondo.$codigopres, $__rpartida)){
				$error.="La partida ($codigopres) ($fondo) ($codigoadm) Esta repetida";
			}

			$__rpartida[]=$codigoadm.$fondo.$codigopres;
		}
		
		if(!empty($error)){
			$do->error_message_ar['pre_ins']=$error;
			$do->error_message_ar['pre_upd']=$error;
			return false;
		}
		
	}
	
	function sp_presucalc($codigoadm){
		//$this->db->simple_query("CALL sp_presucalc($codigoadm)");
		return true;
	}
	
	function instalar(){
		$query="ALTER TABLE `ittrasla`  CHANGE COLUMN `codigopres` `codigopres` VARCHAR(25) NULL DEFAULT NULL AFTER `fondo`";
		$this->db->simple_query($query);
		$query="ALTER TABLE `trasla`  ADD COLUMN `nrooficio` VARCHAR(50) NULL DEFAULT NULL";
		$this->db->simple_query($query);
		$query="ALTER TABLE `trasla` ADD COLUMN `resolu` VARCHAR(50) NULL DEFAULT NULL ";
		$this->db->simple_query($query);
		$query="ALTER TABLE `trasla` ADD COLUMN `fresolu` DATE NULL DEFAULT NULL";
		$this->db->simple_query($query);
		$query="ALTER TABLE `ittrasla`  CHANGE COLUMN `codigopres` `codigopres` VARCHAR(25) NULL DEFAULT NULL";
		$this->db->simple_query($query);
		$query="ALTER TABLE `trasla` CHANGE COLUMN `motivo` `motivo` TEXT NULL DEFAULT NULL AFTER `fecha`";
		$this->db->simple_query($query);
	}
} 
?>
