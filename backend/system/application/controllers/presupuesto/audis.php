<?php
require_once(BASEPATH.'application/controllers/presupuesto/common.php');
class Audis extends Common {

	function audis(){
		parent::Controller();
		$this->load->library("rapyd");
		$this->formatopres=$this->datasis->traevalor('FORMATOPRES');
		$this->flongpres  =strlen(trim($this->formatopres));
		$this->formatoadm=$this->datasis->traevalor('FORMATOESTRU');
		$this->flongadm  =strlen(trim($this->formatoadm));

		$this->datasis->modulo_id(72,1);
	}
	function index(){
		redirect("presupuesto/audis/filteredgrid");
	}
	
	function filteredgrid(){
		$this->rapyd->load("datafilter2","datagrid");
		
		$script='
		$(function() {
				$("#estadmin").change(function(){
					$.post("'.site_url('presupuesto/presupuesto/get_tipo').'",{ codigoadm:$("#estadmin").val() },function(data){$("#fondo").html(data);})
				});
		});
		';
		
		$filter = new DataFilter2("","audis");
		
		$filter->script($script);

		$filter->numero = new inputField("N&uacute;mero", "numero");
		$filter->numero->size=15;
		//$filter->numero->clause="likerigth";
		
		$filter->nrooficio = new inputField("Decreto", "nrooficio");
		$filter->nrooficio->size=15;
		
		$filter->tipo = new dropdownField("Tipo","tipo");
		$filter->tipo->option("","Aumentos y Disminuciones");
		$filter->tipo->option("AUMENTO","Aumentos");
		$filter->tipo->option("DISMINUCION","Disminuciones");

		$filter->fecha = new dateonlyField("Fecha", "fecha");
		$filter->fecha-> dbformat = "Y-m-d";
		$filter->fecha->size=12;
		
		$filter->estadmin = new dropdownField("E. Administrativa","estadmin");		
		$filter->estadmin->rule='required';
		$filter->estadmin->option("","");
		$filter->estadmin->options("SELECT b.codigo, CONCAT_WS(' ',b.codigo,b.denominacion) AS val FROM presupuesto AS a JOIN estruadm AS b ON a.codigoadm=b.codigo  GROUP BY b.codigo");
		
		$filter->fondo = new dropdownField("Fondos","fondo");
				
		$filter->motivo = new inputField("Motivo", "motivo");
		$filter->motivo->size=40;
		
		$filter->status = new dropdownField("Estado","status");
		$filter->status->option("","");
		$filter->status->option("P","Sin Ejecutar");
		$filter->status->option("C","Ejecutado");
		$filter->status->style="width:100px";
		
//		$filter->total = new inputField("Total", "total");
//		$filter->total->size=15;
		
		$filter->buttons("reset","search");    
		$filter->build();
		
		$uri = anchor('presupuesto/audis/dataedit/show/<#numero#>','<str_pad><#numero#>|8|0|STR_PAD_LEFT</str_pad>');
		
		function sta($status){
			switch($status){
				case "P":return "Sin Ejecutar";break;
				case "C":return "Ejecutado";break;
				//case "O":return "Ordenado Pago";break;
				//case "A":return "Anulado";break;
			}
		}
		
		$grid = new DataGrid("");
		$grid->order_by("numero","desc");
		$grid->per_page = 20;
		$grid->use_function('substr','str_pad');
		$grid->use_function('sta');
		
		$grid->column_orderby("N&uacute;mero"     ,$uri                                          ,"numero"     );
		$grid->column_orderby("Decreto"           ,"nrooficio"                                   ,"nrooficio"  );
		$grid->column_orderby("Tipo"              ,"tipo"                                        ,"tipo"       );
		$grid->column_orderby("Fecha"             ,"<dbdate_to_human><#fecha#></dbdate_to_human>","fecha"      ,"align='center'"      );
		$grid->column_orderby("Motivo"            ,"motivo"                                      ,"motivo"      );
		$grid->column_orderby("Total"             ,"total"                                       ,"total"      ,"align='right'" );
		$grid->column_orderby("Estado"            ,"<sta><#status#></sta>"                       ,"status"     ,"align='center'NOWRAP");

		$grid->add("presupuesto/audis/dataedit/create");
		$grid->build();

		$data['filtro']  = $filter->output;
		$data['content'] = $grid->output;
		$data['script'] = script("jquery.js")."\n";
		$data['title']   = "Aumentos y Disminuciones";
		//$data['content'] = $filter->output.$grid->output;
		//$data['title']   = " Aumentos y Disminuciones ";
		$data["head"]    = script("jquery.js").$this->rapyd->get_head();
		$this->load->view('view_ventanas', $data);	
	}

	function dataedit(){
		$this->rapyd->load('dataobject','datadetails');
		//SELECT a.codigopres, CONCAT_WS("-",a.codigopres,b.denominacion) AS val FROM presupuesto AS a JOIN ppla AS b ON a.codigopres=b.codigo WHERE tipo=<#fondo#> AND codigoadm=<#estadmin#> AND LENGTH(a.codigopres)='.$this->flongpres
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
				
		$do = new DataObject("audis");
		$do->rel_one_to_many('itaudis', 'itaudis', array('numero'=>'numero'));
		$do->rel_pointer('itaudis','v_presaldo' ,'itaudis.codigoadm=v_presaldo.codigoadm AND itaudis.fondo=v_presaldo.fondo AND itaudis.codigopres=v_presaldo.codigo',"v_presaldo.denominacion as denomi2");
		$do->order_by('itaudis','itaudis.id','asc');
		
		$edit = new DataDetails("Datos de Aumentos y Disminuciones", $do);
		$edit->back_url = site_url("presupuesto/audis/filteredgrid");
		$edit->pre_process('update' ,'_pre_process');
		$edit->pre_process('delete' ,'_pre_process');
		$edit->set_rel_title('itaudis','Rubro <#o#>');
		
		$edit->pre_process('update'  ,'_valida');
		$edit->pre_process('insert'  ,'_valida');
		
		$edit->status = new autoupdateField('status','P');
		$edit->status->apply_rules=true;
		$edit->status->rule = 'callback_chstatus';
		
		$edit->numero = new inputField("N&uacute;mero", "numero");
		$edit->numero->mode="autohide";
		$edit->numero->when=array('show');
		
		$edit->nrooficio = new inputField("N&uacute;mero de Decreto", "nrooficio");
		$edit->nrooficio->size =12;
		
		$edit->resolu = new inputField("Resoluci&oacute;n", "resolu");
		$edit->resolu->size =40;
		
		$edit->fresolu = new  dateonlyField("Fecha Resoluci&oacute;n",  "fresolu");
		$edit->fresolu->insertValue = date('Y-m-d');
		$edit->fresolu->size =12;
		
		$edit->tipo = new dropdownField("Tipo","tipo");
		$edit->tipo->option("AUMENTO","Aumento");
		$edit->tipo->option("DISMINUCION","Disminuci&oacute;n");

		$edit->fecha = new  dateonlyField("Fecha",  "fecha");
		$edit->fecha->insertValue = date('Y-m-d');
		$edit->fecha->size =12;
		
		$edit->motivo = new textareaField("Motivo", "motivo");
		$edit->motivo->rows=4;
		$edit->motivo->cols=100;
		$edit->motivo->rule='required';
		
		$edit->uejecutora = new dropdownField("U.Ejecutora", "uejecutora");
		$edit->uejecutora->option("","Seccionar");
		$edit->uejecutora->options("SELECT codigo,nombre FROM uejecutora ORDER BY nombre");
		$edit->uejecutora->onchange = "get_uadmin();";
		$edit->uejecutora->rule = "required";

		$edit->uadministra = new dropdownField("U.Administrativa", "uadministra");
		$edit->uadministra->option("","Ninguna");
		$ueje=$edit->getval('uejecuta');
		if($ueje!==false){
			$edit->uadministra->options("SELECT codigo,nombre FROM uadministra WHERE codigoejec='$ueje' ORDER BY nombre");
		}else{
			$edit->uadministra->option("","Seleccione una unidad ejecutora primero");
		}
		
		$edit->fondo = new dropdownField("Fondo","fondo_<#i#>");
		$edit->fondo->size   =10;
		$edit->fondo->rule   ='required';
		$edit->fondo->db_name='fondo';
		$edit->fondo->rel_id ='itaudis';
		$edit->fondo->options("SELECT tipo,tipo a  FROM presupuesto WHERE tipo<>'' GROUP BY tipo ORDER BY tipo desc");
		//$edit->fondo->options("SELECT fondo,fondo a  FROM fondo GROUP BY fondo ORDER BY fondo desc");
		$edit->fondo->style="width:100px;";

		$edit->codigoadm = new inputField("Estructura	Administrativa","codigoadm_<#i#>");
		$edit->codigoadm->size  =10;
		$edit->codigoadm->db_name='codigoadm';
		$edit->codigoadm->rel_id ='itaudis';
		$edit->codigoadm->rule   ='required';

		$edit->codigopres = new inputField("(<#o#>) Partida", "codigopres_<#i#>");
		$edit->codigopres->rule='callback_repetido|required';
		$edit->codigopres->size=10;
		$edit->codigopres->append($btn);
		$edit->codigopres->db_name='codigopres';
		$edit->codigopres->rel_id ='itaudis';
		$edit->codigopres->insertValue ="4";
		//$edit->partida->readonly =true;

		$edit->denomi = new inputField("(<#o#>) Denominaci&oacute;n", "denomi_<#i#>");
		$edit->denomi->db_name ='denomi2';
		$edit->denomi->rel_id  ='itaudis';
		$edit->denomi->cols    =20;
		$edit->denomi->rows    =1;
		$edit->denomi->readonly=true;
		$edit->denomi->pointer =true;

		$edit->monto = new inputField("(<#o#>) monto", "monto_<#i#>");
		$edit->monto->rule='required|callback_positivo';
		$edit->monto->db_name='monto';
		$edit->monto->rel_id ='itaudis';
		$edit->monto->size   =15;
		$edit->monto->css_class='inputnum';
		$edit->monto->onchange ='cal_total(<#i#>);';
		
		$edit->total = new inputField("Cantidad total", "total");
		$edit->total->css_class='inputnum';
		$edit->total->readonly =true;
		$edit->total->rule     ='numeric';
		$edit->total->size     =15;
		
		$status=$edit->get_from_dataobjetct('status');
		if($status=='P'){
			$action = "javascript:window.location='" .site_url('presupuesto/audis/actualizar/'.$edit->rapyd->uri->get_edited_id()). "'";
			$edit->button_status("btn_status",'Actualizar',$action,"TR","show");
			$edit->buttons("modify","delete","save");
		}elseif($status=='C'){
			$action = "javascript:window.location='" .site_url('presupuesto/audis/reversar/'.$edit->rapyd->uri->get_edited_id()). "'";
			$edit->button_status("btn_rever",'Anular',$action,"TR","show");        
		}else{
			$edit->buttons("save");
		}
		
		$edit->buttons("add","undo" , "back","add_rel"); 
		$edit->build();

		$smenu['link']   =barra_menu('330');
		$data['smenu']   = $this->load->view('view_sub_menu', $smenu,true);
		$conten["form"]  =&  $edit;
		$data['content'] = $this->load->view('view_audis', $conten,true); 
		//$data['content'] = $edit->output;
		$data['title']   = "Aumentos y Disminuciones";
    
		//$data["head"]    = $this->rapyd->get_head().script('jquery.js').script("plugins/jquery.numeric.pack.js").script('plugins/jquery.autocomplete.js').script('plugins/jquery.meiomask.js').style('jquery.autocomplete.css');
		$data["head"]    = $this->rapyd->get_head().script('jquery.js').script('jquery-ui.js').script("plugins/jquery.numeric.pack.js").script('plugins/jquery.meiomask.js').style('vino/jquery-ui.css');
		$this->load->view('view_ventanas', $data);
	}
	
	function _pre_process($do){
		$error='Documento ya procesado, no se puede modificar';
		$do->error_message_ar['pre_upd']=$error;
		$do->error_message_ar['pre_del']=$error;
		$status=$do->get('status');
		if($status!='P'){
			return false;	
		}
	}



	function actualizar($id){
		$this->rapyd->load('dataobject');
		
		$do = new DataObject("audis");
		$do->rel_one_to_many('itaudis', 'itaudis', array('numero'=>'numero'));
		$do->load($id);
		
		$sta=$do->get('status');
		
		
		$error    ='';
		$tipo     =$do->get('tipo');
		$campo    =($tipo=='AUMENTO') ? 'aumento':'disminucion';
		if($sta=='P'){
		
			$factor   = 1;
			if($tipo!='AUMENTO'){
				for($i=0;$i < $do->count_rel('itaudis');$i++){
					$codigopres       = $do->get_rel('itaudis','codigopres' ,$i);
					$monto            = $do->get_rel('itaudis','monto'      ,$i);
					$ordinal          = $do->get_rel('itaudis','ordinal'    ,$i);
					$codigoadm        = $do->get_rel('itaudis','codigoadm'  ,$i);
					$fondo            = $do->get_rel('itaudis','fondo'      ,$i);
					
					$error.=$this->chequeapresup($codigoadm,$fondo,$codigopres,$ordinal,$monto,0,'round($monto,2) > round(($presupuesto-$comprometido),2)',"El Monto ($monto) es mayor al disponible para la partida ($codigoadm) ($fondo) ($codigopres)");
				}
				if(empty($error))
					$factor = -1;
			}
			
			
			if(empty($error)){
				for($i=0;$i < $do->count_rel('itaudis');$i++){
					$codigopres       = $do->get_rel('itaudis','codigopres' ,$i);
					$monto            = $do->get_rel('itaudis','monto'      ,$i);
					$ordinal          = $do->get_rel('itaudis','ordinal'    ,$i);
					$codigoadm        = $do->get_rel('itaudis','codigoadm'  ,$i);
					$fondo            = $do->get_rel('itaudis','fondo'      ,$i);
					
					$error.=$this->afectapresup($codigoadm,$fondo,$codigopres,$ordinal,$monto,0, (1*$factor) ,array($campo));
				}
				//exit('prueba');
				if(empty($error)){
					$do->set('status','C');
					$do->set('faudis',date('Ymd'));
					
					$do->save();
					$this->sp_presucalc($codigoadm);
				}
			}
		}else{
			$error.="<div class='alert'><p>No se puede realizar la operacion para este aumento &oacute; disminucion</p></div>";
		}
		
		if(empty($error)){
			logusu('audis',"actualizo $campo numero $id");
			redirect("presupuesto/audis/dataedit/show/$id");
		}else{
			logusu('audis',"actualizo $campo numero $id con error $error");
			$data['content'] = $error.anchor("presupuesto/audis/dataedit/show/$id",'Regresar');
			$data['title']   = " Aumentos y Disminuciones ";
			$data["head"]    = $this->rapyd->get_head();
			$this->load->view('view_ventanas', $data);
		}
	}
	
	function reversar($id){
		$this->rapyd->load('dataobject');
		
		$do = new DataObject("audis");
		$do->rel_one_to_many('itaudis', 'itaudis', array('numero'=>'numero'));
		$do->load($id);
		
		$sta=$do->get('status');
		if($sta=='C'){			
			$tipo     =$do->get('tipo');
			$campo    =($tipo=='AUMENTO') ? 'aumento':'disminucion';
			$factor   = 1;
			
			$error='';
			if($tipo=='AUMENTO'){
				for($i=0;$i < $do->count_rel('itaudis');$i++){
					$codigopres       = $do->get_rel('itaudis','codigopres' ,$i);
					$monto            = $do->get_rel('itaudis','monto'      ,$i);
					$ordinal          = $do->get_rel('itaudis','ordinal'    ,$i);
					$codigoadm        = $do->get_rel('itaudis','codigoadm'  ,$i);
					$fondo            = $do->get_rel('itaudis','fondo'      ,$i);
					
					$error.=$this->chequeapresup($codigoadm,$fondo,$codigopres,$ordinal,$monto,0,'$monto > ($presupuesto-$comprometido)',"El Monto ($monto) es mayor al disponible para la partida ($codigoadm) ($fondo) ($codigopres)");
				}
				if(empty($error))
					$factor = -1;
			}
			
			if(empty($error)){
				for($i=0;$i < $do->count_rel('itaudis');$i++){
					$codigopres       = $do->get_rel('itaudis','codigopres' ,$i);
					$monto            = $do->get_rel('itaudis','monto'      ,$i);
					$ordinal          = $do->get_rel('itaudis','ordinal'    ,$i);
					$codigoadm        = $do->get_rel('itaudis','codigoadm'  ,$i);
					$fondo            = $do->get_rel('itaudis','fondo'      ,$i);
					
					$error.=$this->afectapresup($codigoadm,$fondo,$codigopres,$ordinal,$monto,0, (1*$factor) ,array($campo));
				}
				if(empty($error)){
					$do->set('status','A');
					$do->save();
					$this->sp_presucalc($codigoadm);
				}
			}
		}else{
			$error.="<div class='alert'><p>No se puede realizar la operacion para este aumento � disminucion</p></div>";
		}
		
		if(empty($error)){
			logusu('audis',"reverso $campo numero $id");
			redirect("presupuesto/audis/dataedit/show/$id");
		}else{
			logusu('audis',"reverso $campo numero $id con error $error");
			$data['content'] = $error.anchor("presupuesto/audis/dataedit/show/$id",'Regresar');
			$data['title']   = " Aumentos y Disminuciones ";
			$data["head"]    = $this->rapyd->get_head();
			$this->load->view('view_ventanas', $data);
		}
	}
	
	function _valida($do){
		$__rpartida = array();
		
		
		$error='';$tot=0;
		for($i=0;$i < $do->count_rel('itaudis');$i++){
			$ordinal          = '';
			$codigopres       = $do->get_rel('itaudis','codigopres' ,$i);
			$tot+=$monto      = $do->get_rel('itaudis','monto'      ,$i);
			$ordinal          = $do->get_rel('itaudis','ordinal'    ,$i);
			$codigoadm        = $do->get_rel('itaudis','codigoadm'  ,$i);
			$fondo            = $do->get_rel('itaudis','fondo'      ,$i);
			
			$error.=$this->itpartida($codigoadm,$fondo,$codigopres,$ordinal);
			
			if(in_array($codigoadm.$fondo.$codigopres, $__rpartida)){
				$error.="La partida ($codigopres) ($fondo) ($codigoadm)  Esta repetida";
			}
			
			$__rpartida[]=$codigoadm.$fondo.$codigopres;
		}
		$do->set('total',$tot);
		
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
	
	function instalar(){
		$query="ALTER TABLE `itaudis`  CHANGE COLUMN `codigopres` `codigopres` VARCHAR(25) NULL DEFAULT NULL AFTER `numero`;";
		$this->db->simple_query($query);
		$query="ALTER TABLE `audis`  ADD COLUMN `resolu` VARCHAR(20) NULL";
		$this->db->simple_query($query);
		$query="ALTER TABLE `audis`  ADD COLUMN `fresolu` DATE NULL";
		$this->db->simple_query($query);
		$query="ALTER TABLE `audis` CHANGE COLUMN `motivo` `motivo` TEXT NULL DEFAULT NULL AFTER `fondo`";
		$this->db->simple_query($query);
	}
}
?>
