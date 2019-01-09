<?php
require_once(BASEPATH.'application/controllers/presupuesto/common.php');
class Anoprox extends Common {
	
	var $url  = "presupuesto/anoprox/";
	var $tits = "Proyecci&oacute;n de Compra de Bienes para el a&ntilde;o ";
	var $titp = "Proyecci&oacute;n de Compra de Bienes para el a&ntilde;o ";

	function Anoprox(){
		parent::Controller();
		$this->load->library("rapyd");
		$this->datasis->modulo_id(236,1);
		$this->titp.=(1+date('Y'));
		$this->tits.=(1+date('Y'));
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
				$("#mostrar").attr("title","Haz Click aqui para Mostrar el Filtro, y poder Buscar Documentos de una manera mas r&aacute;pida y cencilla");
				$("input[name=\"btn_add\"]").attr("title","Haz Click aqui para Agregar un nuevo Documento");
				
				$("#mostrar,.button,.enlace,.tuxhelp").tooltip({ 
				    track: true, 
				    delay: 0,
				    showURL: false, 
				    opacity: 1,
				    fixPNG: true, 
				    showBody: " - ", 
				    extraClass: "pretty fancy", 
				    top: -15,
				    left: 5
				});
			});
		
			function get_uadmin(){
				$.post("'.$link.'",{ uejecuta:$("#uejecuta").val() },function(data){$("#td_uadministra").html(data);})
			}
			';

		$filter = new DataFilter("");
		
		$filter->script($script);
		
		$filter->db->select(array("a.numero numero","a.fecha fecha","a.uejecuta","b.nombre uejecuta2","c.nombre uadministra2"));
		$filter->db->from("anoprox a");
		$filter->db->join("uejecutora b" ,"a.uejecuta=b.codigo");
		$filter->db->join("uadministra c","a.uadministra=b.codigo","LEFT");
		if($this->datasis->traevalor('ESPEJO')=="S" && !$this->datasis->essuper())
		$filter->db->where("usuario",$this->session->userdata('usuario'));
		
		$filter->numero = new inputField("N&uacute;mero", "numero");
		$filter->numero->size=15;
		
		$filter->fecha = new dateonlyField("Fecha", "fecha","d/m/Y");
		$filter->fecha->size=12;
		
		$filter->uejecuta = new dropdownField("U.Ejecutora", "uejecuta");
		$filter->uejecuta->option("","Seccionar");
		$filter->uejecuta->options("SELECT codigo,nombre FROM uejecutora ORDER BY nombre");
		$filter->uejecuta->onchange = "get_uadmin();";
		
		$filter->buttons("reset","search");    
		$filter->build();
		$uri  = anchor($this->url.'dataedit/show/<#numero#>'  ,'<str_pad><#numero#>|8|0|STR_PAD_LEFT</str_pad>',array("title"=>"Haz Click para ver el Documento de Proyecci&oacute;n N&uacute;mero <#numero#>","class"=>"enlace"));
		
		$grid = new DataGrid("Haz Click en cualquier n&uacute;mero de Documento para verlo");
		$grid->order_by("numero","desc");
		$grid->per_page = 20;
		$grid->use_function('substr','str_pad');
		
		$grid->column_orderby("N&uacute;mero"         ,$uri                                             ,"numero"        );
		$grid->column_orderby("Fecha"                 ,"<dbdate_to_human><#fecha#></dbdate_to_human>"   ,"fecha"         ,"align='center'"     );
		$grid->column_orderby("Unidad Ejecutora"      ,"uejecuta2"                                      ,"uejecuta2"     ,"align='left' NOWRAP");
		$grid->column_orderby("Unidad Administrativa" ,"uadministra2"                                   ,"uadministra2"  ,"align='left' NOWRAP");
		
		$grid->add($this->url."dataedit/create");
		$grid->build();
		
		$data['filtro']  = $filter->output;
		$data['content'] = $grid->output;
		$data['title']   = $this->titp;		
		$data["head"]    = $this->rapyd->get_head().script('jquery.js').script('jquery-ui.js').script("plugins/jquery.numeric.pack.js").script("plugins/jquery.json.min.js").script('plugins/jquery.tooltip.js').script('plugins/jquery.autocomplete.js').style('jquery.autocomplete.css').style('jquery.tooltip.css').style('tooltip.css').style('vino/jquery-ui.css');
		$this->load->view('view_ventanas', $data);
	}

	function dataedit(){
		//$this->datasis->modulo_id(115,1);
		$this->rapyd->load('dataobject','datadetails');
		
		$modbus=array(
			'tabla'   =>'ppla',
			'columnas'=>array(
				'codigo'       =>'Codigo',   
				'denominacion' =>'Denominaci&oacute;n'
				),
			'filtro'  =>array(
				'codigo'       =>'Codigo',   
				'denominacion' =>'Denominaci&oacute;n'
				),
			'retornar'=>array(
				'codigo'   =>'itcodigopres_<#i#>'
				),
			'p_uri'=>array(4=>'<#i#>',),
			'where'   =>'movimiento = "S" AND MID(codigo,1,1)="4"',// AND saldo > 0 AND movimiento = "S"
			'script'  =>array('cal_nppla(<#i#>)'),
			'titulo'  =>'Busqueda de partidas',
			'title'   =>'Haz click aqui para abrir una ventana nueva con el modulo de busqueda avanzada para seleccionar una partida');
		$btn=$this->datasis->p_modbus($modbus,'<#i#>');
		
		$do = new DataObject("anoprox");
		$do->rel_one_to_many('itanoprox', 'itanoprox', array('numero'=>'numero'));
		$do->rel_pointer('itanoprox','ppla' ,'ppla.codigo=itanoprox.codigopres',"ppla.denominacion as denomia");
		$do->order_by('itanoprox.codigopres');

		$msj = "ERROR: DEBE SOLUCIONAR LOS SIGUIENTES PROBLEMAS:</br>";
		if(!empty($do->error_message_ar['pre_ins']))
			$do->error_message_ar['pre_ins']=$msj.$do->error_message_ar['pre_ins'];
			
		if(!empty($do->error_message_ar['pre_upd']))
			$do->error_message_ar['pre_upd']=$msj.$do->error_message_ar['pre_upd'];

		$edit = new DataDetails($this->tits, $do);
		$edit->back_url = site_url($this->url."filteredgrid","assas");
		$edit->set_rel_title('itanoprox','Rubro <#o#>');
		
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
		$edit->numero->when=array('show','modify');
		
		$edit->responsable = new inputField("Responsable", "responsable");
		$edit->responsable->maxlenth = 249;
		$edit->responsable->tip      = "Completar este campo con el Nombre y Apellido del responsable o Director de la Direcci&oacute;n.</br></br> Ejemplo:Juan Perez";
	
		$edit->fecha= new  dateonlyField("Fecha",  "fecha","d/m/Y");
		$edit->fecha->insertValue = date('Y-m-d');
		$edit->fecha->size        = 12;
		$edit->fecha->mode        = "autohide";
		$edit->fecha->when        =array('show','modify');
					
		//$edit->concepto = new textareaField("Concepto", 'concepto');
		//$edit->concepto->rows    = 1;
		//$edit->concepto->cols    = 80;
		//$edit->concepto->tip     = "Introduzca alg&uacute;n concepto de ser necesario. Este es muy &uacute;til cuando se hacen varias proyecciones de una misma dependencia.</br>Ejemplo:Esta lista es de prioridad uno &oacute; Esta pertenece al departamento X";
		
		$edit->uejecuta = new dropdownField("Direcci&oacute;n", "uejecuta");
		$edit->uejecuta->option("","Seccionar");
		$edit->uejecuta->options("SELECT codigo,CONCAT(codigo,' ',nombre)a FROM uejecutora ORDER BY nombre");
		$edit->uejecuta->onchange = "get_uadmin();";
		$edit->uejecuta->rule     = "required";
		$edit->uejecuta->tip      = "Seleccione el nombre de la Direcci&oacute;n a la cual pertenece, haciendo click en la flecha del lado derecho del campo</br> Ejemplo: Direcci&oacute;n de Administraci&oacute;n";
		$edit->uejecuta->style    = "width:500px";

		//$edit->uadministra = new dropdownField("U.Administrativa", "uadministra");
		//$edit->uadministra->option("","Ninguna");
		//$ueje=$edit->getval('uejecuta');
		//if($ueje!==false){
		//	$edit->uadministra->options("SELECT codigo,nombre FROM uadministra WHERE codigoejec='$ueje' ORDER BY nombre");
		//}else{
		//	$edit->uadministra->option("","Seleccione una unidad ejecutora primero");
		//}
		//$edit->uadministra->tip      = "Seleccione el nombre de la unidad Administrativa de ser necesario haciendo click en la flecha del lado derecho del campo.</br>Ejemplo: Departamento de Compras";
		
		//************************** FIN   ENCABEZADO********************************************************************
		
		//**************************INICIO DETALLE DE ASIGNACIONES  *****************************************************

		$edit->itcodigoadm = new inputField("(<#o#>) Actividad", "itcodigoadm_<#i#>");
		$edit->itcodigoadm->rule     ='required';
		$edit->itcodigoadm->size     =2;
		$edit->itcodigoadm->db_name  ='codigoadm';
		$edit->itcodigoadm->rel_id   ='itanoprox';
		$edit->itcodigoadm->insertValue="51";
		$edit->itcodigoadm->tip      = "Trasncriba el numero de la actividad a la cual pertenece este item.</br></br>Ejemplo:52";
		
		$edit->itcodigopres = new inputField("(<#o#>) Partida", "itcodigopres_<#i#>");
		$edit->itcodigopres->rule     ='required|callback_itorden';
		$edit->itcodigopres->size     =12;
		$edit->itcodigopres->db_name  ='codigopres';
		$edit->itcodigopres->rel_id   ='itanoprox';
		$edit->itcodigopres->insertValue="4."; 
		$edit->itcodigopres->append($btn);
		$edit->itcodigopres->tip      = "Trasncriba, Seleccione o Busque por medio de la Lupa, La partida a la cual corresponde el bien.</br></br>Ejemplo:4.03.03.03";
					
		$edit->itdenomia= new textareaField("(<#o#>)","itdenomia_<#i#>");
		$edit->itdenomia->db_name  ='denomia';
		$edit->itdenomia->rel_id   ='itanoprox';
		$edit->itdenomia->pointer  =true;
		$edit->itdenomia->cols     =15;
		$edit->itdenomia->rows     =2;
		$edit->itdenomia->readonly = true;
		$edit->itdenomia->tip      = "Este campos es solo referencial, no debe de trancribir en el, el sistema lo har&aacute; automaticamente";
		
		$edit->itunidad = new dropdownField("(<#o#>) Unidad", "unidad_<#i#>");
		$edit->itunidad->db_name= 'unidad';
		$edit->itunidad->rule   = 'required';
		$edit->itunidad->rel_id = 'itanoprox';
		$edit->itunidad->options("SELECT unidades AS id,unidades FROM unidad ORDER BY unidades");
		$edit->itunidad->style  ="width:80px";
		$edit->itunidad->tip    = "Seleccione la unidad de medida del bien.</br>Ejemplo: Unidad";
		
		$edit->itdescrip = new inputField("(<#o#>) Bien", "itdescrip_<#i#>");
		$edit->itdescrip->rule     ='required';
		$edit->itdescrip->size     =20;
		$edit->itdescrip->db_name  ='descrip';
		$edit->itdescrip->rel_id   ='itanoprox';
		$edit->itdescrip->tip      = "Transcriba un nombre corto o referencial del bien</br></br>Ejemplo: Monitor"; 
		
		$edit->itdescripd= new textareaField("(<#o#>) Descripci&oacute;n Detallada","itdescripd_<#i#>");
		$edit->itdescripd->rule     ='required';
		$edit->itdescripd->db_name  ='descripd';
		$edit->itdescripd->rel_id   ='itanoprox';
		$edit->itdescripd->cols     =25;
		$edit->itdescripd->rows     =2;
		$edit->itdescripd->tip      = 'Transcriba la descripci&oacute;n detallada del bien.</br></br>Ejemplo: Monitor LCD 22 pulgadas con entrada de video para usar como TV';
		
		$edit->itcant = new inputField("(<#o#>) C&aacute;ntidad", "itcant_<#i#>");
		$edit->itcant->css_class='inputnum';
		$edit->itcant->rule     ='required|callback_positivo';
		$edit->itcant->size     =7;
		$edit->itcant->db_name  ='cant';
		$edit->itcant->rel_id   ='itanoprox';
		$edit->itcant->tip      = "Transcriba la cantidad de elementos ha adquirir.</br>Ejemplo: 2";
		
		//$edit->itmontoa->mode      ="autohide";	
		//************************** FIN   DETALLE DE ORDENES DEPAGO*****************************************************
		$status=$edit->get_from_dataobjetct('status');
		if($status=='H1'){
			$action = "javascript:window.location='" .site_url($this->url.'termina/'.$edit->rapyd->uri->get_edited_id()). "'";
			
			$edit->button_status("btn_add_anoprox" ,'AGREGAR BIEN',"javascript:add_itanoprox()","MB",'modify',"button_add_rel");
			$edit->button_status("btn_add_anoprox" ,'AGREGAR BIEN',"javascript:add_itanoprox()","MB",'create',"button_add_rel");
			$edit->button_status("btn_termina",'Marcar Documento Como Terminado',$action,"TR","show");
			$edit->buttons("modify", "save");
		}elseif($status=='H2'){
			$action = "javascript:btn_anular('" .$edit->rapyd->uri->get_edited_id()."')";
			$edit->button_status("btn_anula",'Anular',$action,"TR","show");
		}else{
			$edit->button_status("btn_add_anoprox" ,'AGREGAR BIEN',"javascript:add_itanoprox()","MB",'modify',"button_add_rel");
			$edit->button_status("btn_add_anoprox" ,'AGREGAR BIEN',"javascript:add_itanoprox()","MB",'create',"button_add_rel");
		}
		
		$edit->buttons("save","undo", "back");
		$edit->build();

		$smenu['link']   = barra_menu('10C');
		$data['smenu']   = $this->load->view('view_sub_menu', $smenu,true);
		$conten["form"]  =&  $edit;
		$data['content'] = $this->load->view('view_anoprox', $conten,true);
		//$data['content'] = $edit->output;
		$data['title']   = $this->tits;
		$data["head"]    = $this->rapyd->get_head().script('jquery.js').script('jquery-ui.js').script('plugins/jquery.meiomask.js').script("plugins/jquery.numeric.pack.js").script("plugins/jquery.json.min.js").script('plugins/jquery.tooltip.js').script('plugins/jquery.autocomplete.js').style('jquery.autocomplete.css').style('jquery.tooltip.css').style('tooltip.css').style('vino/jquery-ui.css');//
		
		$this->load->view('view_ventanas', $data);
	}

	function _valida($do){
		$error = '';
		$numero = $do->get('numero');
		$do->set('status','H1');
		$do->set('fecha',date('Y-m-d'));
		$usuario = $this->session->userdata('usuario');
		$do->set('usuario',$usuario);
		
		if(empty($numero)){
			$ntransac = $this->datasis->fprox_numero('ntransac');
			$do->set('numero','_'.$ntransac);
			$do->pk    =array('numero'=>'_'.$ntransac);
			for($i=0;$i < $do->count_rel('itanoprox');$i++){
				$do->set_rel('itanoprox','numero','_'.$ntransac,$i);
			}
		}

		$importes=array();
		for($i=0;$i < $do->count_rel('itanoprox');$i++){
			$codigopres   = $do->get_rel('itanoprox','codigopres',$i);
			$codigo       = $this->db->escape($codigopres);
			$denominacion = $this->datasis->dameval("SELECT denominacion FROM ppla WHERE codigo=$codigo AND MID(codigo,1,1)='4'");
			if($denominacion)
				$do->set_rel('itanoprox','denomi',$denominacion,$i);
			else
				$error.="La partida $codigo es inv&aacute;lida, por favor seleccione una v&aacute;lida";
		}

		if(!empty($error)){
			$do->error_message_ar['pre_ins']=$error;
			$do->error_message_ar['pre_upd']=$error;
			return false;
		}
	}
	
	function termina($numero){
		$this->rapyd->load('dataobject');
		
		$error='';
	
		$do = new DataObject("anoprox");
		$do->rel_one_to_many('itanoprox', 'itanoprox', array('numero'=>'numero'));
		$do->load($numero);
		$status = $do->get('status');
	
		if($status=='H1'){
						
		}else{
			$error.= "<div class='alert'>No se puede realizar la operacion para el certificado</div>";
		}
		
		if(empty($error)){
			$numero = $ncdisp = $this->datasis->fprox_numero('nanoprox');
			$do->set('numero',$ncdisp);
			//$do->pk    =array('numero'=>$ncdisp);
			for($i=0;$i < $do->count_rel('itanoprox');$i++){
				$do->set_rel('itanoprox','id'    ,''     ,$i);
				$do->set_rel('itanoprox','numero',$ncdisp,$i);
			}
		}
		
		//print_r($do->get_all());
		
		if(empty($error)){
			$do->set('status','H2');
			$do->save();
			logusu('canoprox',"Marco como terminado documento nro $numero");
			redirect($this->url."dataedit/show/$numero");
		}else{
			logusu('anoprox',"Marco como terminado dosumento nro $numero con ERROR $error");
			$data['content'] = $error.anchor($this->url."dataedit/show/$numero",'Regresar');
			$data['title']   = " $this->tits";
			$data["head"]    = $this->rapyd->get_head();
			$this->load->view('view_ventanas', $data);
		}
	}
	
	function anular($numero){
		$this->db->query("UPDATE anoprox SET status='AN' where numero='$numero'");
		logusu('anoprox',"Anulo documento nro $numero");
		redirect($this->url."dataedit/show/$numero");
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
	
	function autocomplete($campo,$cod=false){
		if($cod!==false){		
			$mSQL="SELECT LOWER($campo) c1 FROM itanoprox WHERE $campo LIKE '$cod%' GROUP BY LOWER($campo)";
						
			$query=$this->db->query($mSQL);
			$salida = '';
			if($query->num_rows() > 0){
				foreach($query->result() AS $row){
					$salida.=$row->c1."\n";
				}
			}
			echo htmlentities($salida);
		}
	}
		
	function _post_insert($do){
		$numero = $do->get('numero');
		logusu('anoprox',"Creo documento Nro $numero");
		//redirect($this->url."actualizar/$numero");
	}
	
	function _post_update($do){
		$numero = $do->get('numero');
		logusu('anoprox'," Modifico documento Nro $numero");
		//redirect($this->url."actualizar/$numero");
	}
	function _post_delete($do){
		$numero = $do->get('numero');
		logusu('anoprox'," Elimino documento Nro $numero");
	}
	
	function instalar(){
		$query="
		
		CREATE TABLE `anoprox` (
			`numero` VARCHAR(9) NOT NULL DEFAULT '',
			`fecha` DATE NULL DEFAULT NULL,
			`uejecuta` CHAR(4) NULL DEFAULT NULL,
			`uadministra` CHAR(4) NULL DEFAULT NULL,
			`concepto` TINYTEXT NULL,
			`responsable` VARCHAR(250) NULL DEFAULT NULL,
			`status` CHAR(2) NULL DEFAULT NULL ,
			`usuario` VARCHAR(12) NULL DEFAULT NULL,
			PRIMARY KEY (`numero`),
			INDEX `uejecuta` (`uejecuta`)
		)
		COMMENT='Preyeccion proximo ano'
		ENGINE=MyISAM
		ROW_FORMAT=DEFAULT
		";
		echo $this->db->simple_query($query);
		$query="
		CREATE TABLE `itanoprox` (
			`id` INT(11) NOT NULL AUTO_INCREMENT,
			`numero` VARCHAR(9) NULL DEFAULT NULL,
			`codigoadm` VARCHAR(12) NULL DEFAULT NULL,
			`fondo` VARCHAR(20) NULL DEFAULT NULL,
			`codigopres` VARCHAR(17) NULL DEFAULT NULL,
			`ordinal` CHAR(3) NULL DEFAULT NULL,
			`unidad` VARCHAR(20) NULL DEFAULT NULL,
			`denomi` TINYTEXT NULL,
			`descrip` TINYTEXT NULL,
			`descripd` TINYTEXT NULL,
			`cant` DECIMAL(19,2) NULL DEFAULT NULL,
			PRIMARY KEY (`id`),
			INDEX `numero` (`numero`)
		)
		ENGINE=MyISAM
		ROW_FORMAT=DEFAULT
		AUTO_INCREMENT=1
		";
		echo $this->db->simple_query($query);
	}
} 
?>
