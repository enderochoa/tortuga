<?php
class Bi_trasla extends Controller {

	var $tits='Incorporaci&oacute;n &oacute; Desincorporaci&oacute;n de Bienes';
	var $titp='Incorporaciones y Desincorporaciones de Bienes';
	var $url ='bienes/bi_trasla';
	var $tablas=array(
		"B"=>'bi_muebles',
		"M"=>'bi_moto',
		'T'=>'bi_terre',
		'E'=>'bi_edificio',
		'V'=>'bi_vehi'
	);

	function bi_trasla(){
		parent::Controller();
		$this->load->library("rapyd");
		$this->datasis->modulo_id(266,1);
	}
	function index(){
		redirect($this->url."/filteredgrid");
	}


	function filteredgrid(){
		
		$this->rapyd->load("datafilter2","datagrid");
		
		$script='
		$(function() {
		
		});
		';
		
		$filter = new DataFilter2("","bi_trasla");
		
		$filter->script($script);

		$filter->numero = new inputField("N&uacute;mero", "numero");
		$filter->numero->size=15;
		
		$filter->fecha = new dateonlyField("Fecha", "fecha");
		$filter->fecha->dbformat = "Y-m-d";
		$filter->fecha->size=12;

		$filter->concepto = new inputField("Concepto", "concepto");
		$filter->concepto->size=40;
		
		$filter->status = new dropdownField("Estado","status");
		$filter->status->option("","");
		$filter->status->option("P","Sin Ejecutar");
		$filter->status->option("C","Ejecutado");
		$filter->status->style="width:100px";
		
		$filter->buttons("reset","search");
		$filter->build();
		
		$uri = anchor($this->url.'/dataedit/show/<#numero#>','<str_pad><#numero#>|8|0|STR_PAD_LEFT</str_pad>');
		
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
		
		$grid->column_orderby("N&uacute;mero"  ,$uri,"numero");
		$grid->column_orderby("Fecha"          ,"<dbdate_to_human><#fecha#></dbdate_to_human>","fecha"   );
		$grid->column_orderby("Concepto"       ,"concepto"                                    ,"concepto");
		$grid->column_orderby("Estado"         ,"<sta><#status#></sta>"                       ,"status"  );
		
		$grid->add($this->url."/dataedit/create");
		$grid->build();
		
		$data['filtro']  = $filter->output;
		$data['content'] = $grid->output;
		$data['script'] = script("jquery.js")."\n";
		$data['title']   = $this->tits;
		$data["head"]    = script("jquery.js").$this->rapyd->get_head();
		$this->load->view('view_ventanas', $data);
	}
	
	function dataedit(){
		$this->rapyd->load('dataobject','datadetails');
		
	 $modbus=array(
			'tabla'   =>'v_bienes',
			'columnas'=>array(
				'id'          =>'Id'                ,
				'codigo'      =>'C&oacute;digo'     ,
				'grupo'       =>'Grupo'             ,
				'subgrupo'    =>'SubGrupo'          ,
				'seccion'     =>'Secci&oacute;n'    ,
				'numero'      =>'N&uacute;mero'     ,
				'descrip'     =>'Descripci&oacute;n',
				'monto'       =>'Monto'
				),
			'filtro'  =>array(
				'id'          =>'Id'                ,
				'codigo'      =>'C&oacute;digo'     ,
				'grupo'       =>'Grupo'             ,
				'subgrupo'    =>'SubGrupo'          ,
				'seccion'     =>'Secci&oacute;n'    ,
				'numero'      =>'N&uacute;mero'     ,
				'descrip'     =>'Descripci&oacute;n',
				),
			'retornar'=>array(
				'id'          =>'itbien_<#i#>',
				'codigo'      =>'itcodigo_<#i#>'    ,
				'grupo'       =>'itgrupo_<#i#>'     ,
				'subgrupo'    =>'itsubgrupo_<#i#>'  ,
				'seccion'     =>'itseccion_<#i#>'   ,
				'numero'      =>'itnumerob_<#i#>'    ,
				'descrip'     =>'itdescrip_<#i#>'   ,
				'monto'       =>'itmonto_<#i#>'
				),
			'p_uri'=>array(4=>'<#i#>',5=>'<#alma#>'),
			'where'=>"alma = 0000",
			'titulo'  =>'Busqueda de Bienes');
			
		$modbus1=$modbus;
		$modbus1['where']="alma = <#alma#>";
			
		$btn =$this->datasis->p_modbus($modbus,'<#i#>');
		$btn2=$this->datasis->p_modbus($modbus1,'<#i#>/<#alma#>',800,600,'v_bienes2');
		$btn2='<img src="/tortuga/assets/default/images/system-search.png"  alt="Busqueda de Bienes" title="Busqueda de Bienes" border="0" onclick="modbusdepen(<#i#>)"/></div>';
		
		$mconc=array(
			'tabla'   =>'bi_conc',
			'columnas'=>array(
				'codigo'      =>'C&oacute;digo'      ,
				'denomi'      =>'Denominaci&oacute;n',
				'tipo'        =>'Tipo' 
				),
			'filtro'  =>array(
				'codigo'      =>'C&oacute;digo'      ,
				'denomi'      =>'Denominaci&oacute;n',
				'tipo'        =>'Tipo'
				),
			'retornar'=>array(
				'codigo'      =>'itconcepto_<#i#>'
				),
			'p_uri'=>array(4=>'<#i#>',5=>'<#tipo#>'),
			'where'=>"tipo = <#tipo#>",
			'titulo'  =>'Busqueda de Concepto de Bienes');
		$btnconc =$this->datasis->p_modbus($mconc,'<#i#>/<#tipo#>');
		$btnconc ='<img src="/tortuga/assets/default/images/system-search.png"  alt="Busqueda de Conceptos de Bienes" title="Busqueda de Concepto de Bienes" border="0" onclick="modbusdepen2(<#i#>)"/>';

		$do = new DataObject("bi_trasla");
		$do->rel_one_to_many('bi_ittrasla', 'bi_ittrasla', array('numero'=>'numero'));
		//$do->rel_pointer('bi_ittrasla','v_bienes' ,'bi_ittrasla.bien=v_bienes.id',"codigo,grupo,subgrupo,seccion,descrip");

		$edit = new DataDetails("Traslado de Bienes", $do);
		$edit->back_url = site_url($this->url."/filteredgrid");
		$edit->set_rel_title('bi_ittrasla','Rubro <#o#>');
		
		$edit->pre_process('insert'  ,'_valida');
		$edit->pre_process('update'  ,'_valida');
		
		$edit->numero   = new inputField("N&uacute;mero", "numero");
		$edit->numero->mode="autohide";
		$edit->numero->when=array('show');
		
		$edit->status   = new dropdownField("Estado", "status");
		$edit->status->option("A","Anulado");
		$edit->status->option("C","Ejecutado");
		$edit->status->option("P","Pendiente");
		$edit->status->when=array('show');
		
		$edit->fecha = new  dateonlyField("Fecha",  "fecha");
		$edit->fecha->insertValue = date('Y-m-d');
		$edit->fecha->size =12;
		
		$edit->tipo = new dropdownField("Tipo", "tipo");
		$edit->tipo->option("I","Incorporaci&oacute;n");
		$edit->tipo->option("D","Desincorporaci&oacute;n");
		$edit->tipo->style    ="width:150px";
		
		$edit->alma = new dropdownField("Almac&eacute;n", "alma");
		$edit->alma->options("SELECT codigo,CONCAT_WS(' ',codigo,descrip) valor FROM alma WHERE codigo<>'0000'");
		
		//$edit->recibe = new dropdownField("Recibe", "recibe");
		//$edit->recibe->options("SELECT codigo,CONCAT_WS(' ',codigo,descrip) valor FROM alma ");
		
		$edit->concepto = new textareaField("Observaciones", "concepto");
		$edit->concepto->rows=2;
		$edit->concepto->cols=60;
		//$edit->concepto->rule='required';
		
		$edit->itbien = new inputField("(<#o#>) ID","itbien_<#i#>");
		$edit->itbien->size   =3;
		$edit->itbien->rule   ='required';
		$edit->itbien->db_name='bien';
		$edit->itbien->rel_id ='bi_ittrasla';
		$edit->itbien->append('<div  class="alma0">'.$btn.'</div>');
		$edit->itbien->append('<div  class="alma1">'.$btn2.'</div>');
		
		$edit->itconcepto = new inputField("(<#o#>) Concepto","itconcepto_<#i#>");
		$edit->itconcepto->size   =2;
		$edit->itconcepto->rule   ='required';
		$edit->itconcepto->db_name='concepto';
		$edit->itconcepto->rel_id ='bi_ittrasla';
		$edit->itconcepto->append($btnconc);
		
		$campos = array('codigo','grupo','subgrupo','seccion','numerob','descrip');
		foreach($campos AS $campo=>$objeto){
			$objeto2 = 'it'.$objeto;
			$edit->$objeto2 = new inputField("(<#o#>) ", 'it'.$objeto."_<#i#>");
			$edit->$objeto2->db_name  = $objeto;
			$edit->$objeto2->rel_id   = 'bi_ittrasla';
			$edit->$objeto2->readonly = true;
			//$edit->$objeto2->pointer  = true;
			$edit->$objeto2->size     = 6;
		}
		
		$edit->itdescrip = new textareaField('(<#o#>) ','itdescrip_<#i#>');
		$edit->itdescrip->db_name  = 'descrip';
		$edit->itdescrip->rel_id   = 'bi_ittrasla';
		$edit->itdescrip->readonly = true;
		//$edit->itdescrip->pointer  = true;
		$edit->itdescrip->rows     =1;
		$edit->itdescrip->cols     =20;
		
		$edit->itdescrip->size=40;
		$edit->itgrupo->size=2;
		$edit->itsubgrupo->size=4;
		$edit->itseccion->size=4;
		
		$edit->itmonto = new inputField("(<#o#>) Monto","itmonto_<#i#>");
		$edit->itmonto->size   =10;
		$edit->itmonto->rule   ='required';
		$edit->itmonto->db_name='monto';
		$edit->itmonto->rel_id ='bi_ittrasla';
		$edit->itmonto->readonly = true;
		
		$status=$edit->get_from_dataobjetct('status');
		if($status=='P'){
			$action = "javascript:window.location='" .site_url($this->url.'/actualizar/'.$edit->rapyd->uri->get_edited_id()). "'";
			$edit->button_status("btn_status",'Actualizar',$action,"TR","show");
			$edit->buttons("modify","save");
		}elseif($status=='C'){
			$action = "javascript:window.location='" .site_url($this->url.'/anular/'.$edit->rapyd->uri->get_edited_id()). "'";
			$edit->button_status("btn_rever",'Anular',$action,"TR","show");
		}else{
			$edit->buttons("save");
		}

		$edit->buttons("undo","back","add_rel");
		$edit->build();

		$smenu['link']=barra_menu('116');
		$data['smenu'] = $this->load->view('view_sub_menu', $smenu,true);
		$conten["form"]  =&  $edit;

		$data['content'] = $this->load->view('view_bi_trasla', $conten,true); 
		$data['title']   = $this->tits;
		$data["head"]    = script('jquery.js').script('jquery-ui.js').script("plugins/jquery.numeric.pack.js").script('plugins/jquery.meiomask.js').style('vino/jquery-ui.css').$this->rapyd->get_head();
		$this->load->view('view_ventanas', $data);
	}
	
	function actualizar($numero){
		$this->rapyd->load('dataobject');
		
		$error='';
		$do = new DataObject("bi_trasla");
		$do->rel_one_to_many('bi_ittrasla', 'bi_ittrasla', array('numero'=>'numero'));
		$do->load($numero);
		
		$tipo =$do->get('tipo');
		$alma =$do->get('alma');
		if($tipo=='I')
			$recibe=$alma;
		elseif($tipo=='D')
			$recibe='0000';
			
		$recibe=$this->db->escape($recibe);
		
		$sta=$do->get('status');
		
		if($sta=='P'){
			for($i=0;$i < $do->count_rel('bi_ittrasla');$i++){
				$bien = $do->get_rel('bi_ittrasla','bien' ,$i);
				$tabla= substr($bien,0,1);
				$bien = $this->db->escape($bien);
				
				$this->db->query("UPDATE ".$this->tablas[$tabla]." SET alma=$recibe WHERE id=$bien");
			}
		}else{
			$error.="<div class='alert'><p>No se puede realizar la operacion para la transferencia $numero </p></div>";
		}
		
		if(empty($error)){
			$do->set('status' ,'C');
			$do->save();
			logusu('bi_trasla',"Actualizo bi_traslado $numero");
			redirect($this->url."/dataedit/show/$numero");
		}else{
			$data['content'] = $error.anchor($this->url."/dataedit/show/$numero",'Regresar');
			$data['title']   = $this->tits;
			$data["head"]    = $this->rapyd->get_head();
			$this->load->view('view_ventanas', $data);
		}
	}
	
	function anular($numero){
		$this->rapyd->load('dataobject');
		
		$error='';
		$do = new DataObject("bi_trasla");
		$do->rel_one_to_many('bi_ittrasla', 'bi_ittrasla', array('numero'=>'numero'));
		$do->load($numero);
		
		$tipo =$do->get('tipo');
		$alma =$do->get('alma');
		if($tipo=='I')
			$recibe='0000';
		elseif($tipo=='D')
			$recibe=$alma;
			
		$recibe=$this->db->escape($recibe);
		
		$sta=$do->get('status');
		
		
		if($sta=='C'){
			for($i=0;$i < $do->count_rel('bi_ittrasla');$i++){
				$bien = $do->get_rel('bi_ittrasla','bien' ,$i);
				$tabla= substr($bien,0,1);
				$bien = $this->db->escape($bien);
				
				$this->db->query("UPDATE ".$this->tablas[$tabla]." SET alma=$recibe WHERE id=$bien");
			}
		}else{
			$error.="<div class='alert'><p>No se puede realizar la operacion para la transferencia $numero </p></div>";
		}
		
		if(empty($error)){
			$do->set('status' ,'A');
			$do->save();
			logusu('bi_trasla',"Anulo bi_traslado $numero");
			redirect($this->url."/dataedit/show/$numero");
		}else{
			$data['content'] = $error.anchor($this->url."/dataedit/show/$numero",'Regresar');
			$data['title']   = $this->tits;
			$data["head"]    = $this->rapyd->get_head();
			$this->load->view('view_ventanas', $data);
		}
	}
	
	function _valida($do){
		$__rbien = array();
		$error='';
		$do->set('status','P');
		$tipo =$do->get('tipo');
		$alma =$do->get('alma');
		if($tipo=='I')
			$envia='0000';
		elseif($tipo=='D')
			$envia=$alma;
			
		$envia=$this->db->escape($envia);
		for($i=0;$i < $do->count_rel('bi_ittrasla');$i++){
			$bien = $do->get_rel('bi_ittrasla','bien' ,$i);
			$tabla= substr($bien,0,1);
			$bien = $this->db->escape($bien);
				
			$c=$this->datasis->dameval("SELECT COUNT(*) FROM ".$this->tablas[$tabla]." WHERE id=$bien AND alma=$envia");
			
			if($c==0)$error.=" Error. el bien $bien no existe, o no pertenece a la Ubicacion seleccionada $envia</br>";
			
			if(in_array($bien,$__rbien))
				$error.="El bien ($bien) esta duplicado";
			$__rbien[]=$bien;
		}
		
		if(!empty($error)){
			$do->error_message_ar['pre_ins']=$error;
			$do->error_message_ar['pre_upd']=$error;
			return false;
		}
	}
} 
?>

