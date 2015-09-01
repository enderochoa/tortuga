<?php require_once(BASEPATH.'application/controllers/validaciones.php');
//proveed
class Sprv extends validaciones {
	
	function sprv(){
		parent::Controller(); 
		$this->load->library("rapyd");
		$this->datasis->modulo_id(3,1);
		//I use THISFILE, instead __FILE__ to prevent some documented php-bugs with higlight_syntax()&__FILE__
		define ("THISFILE",   APPPATH."controllers/compras/". $this->uri->segment(2).EXT);
	}
	function index(){
		redirect("presupuesto/sprv/filteredgrid");
	}
	function filteredgrid(){

		$this->rapyd->load("datafilter2","datagrid");
		//$this->rapyd->uri->keep_persistence();

		$filter = new DataFilter2("", "sprv");
		
		$filter->proveed = new inputField("C&oacute;digo", "proveed");
		$filter->proveed->size=13;
		$filter->proveed->maxlength=5;
		
		$filter->nombre = new inputField("Nombre", "nombre");
		$filter->nombre->size=13;
		$filter->nombre->maxlength=40;
		
		$filter->rif = new inputField("Rif", "rif");
		$filter->rif->size=13;
		$filter->rif->maxlength=12;
    
		$filter->grupo = new dropdownField("Grupo", "grupo");
		$filter->grupo->option("","");
		$filter->grupo->options("SELECT grupo,gr_desc,grupo FROM grpr ORDER BY gr_desc='01'");
		$filter->grupo->style = "width:290px";
		
		$filter->buttons("reset","search");
		$filter->build();

		$uri = anchor('presupuesto/sprv/dataedit/show/<#proveed#>','<#proveed#>');

		$grid = new DataGrid("");
		$grid->order_by("proveed","asc");
		$grid->per_page = 20;
		
		$grid->column_orderby("C&oacute;digo",$uri           ,"proveed" ,"align='center'"      );
		$grid->column_orderby("Nombre","nombre"       ,"nombre"  ,"align='left'NOWRAP");
		$grid->column_orderby("R.I.F.","rif"          ,"rif"     ,"align='center'"      );
		
		$grid->add("presupuesto/sprv/dataedit/create");
		$grid->build();

		//$data['content'] = $filter->output.$grid->output;
		$data['filtro']  = $filter->output;
		$data['content'] = $grid->output;
		$data['script'] = script("jquery.js")."\n";
		$data['title']   = "Beneficiarios";
		$data["head"]    = $this->rapyd->get_head();
		$this->load->view('view_ventanas', $data);	
	}
	function dataedit(){ 
		$this->rapyd->load("dataedit");
		
		$mSCLId=array(
		'tabla'   =>'scli',
		'columnas'=>array(
		'cliente' =>'Codigo Cliente',
		'nombre'=>'Nombre',
		'contacto'=>'Contacto',
		'nomfis'=>'Nom. Fiscal'),
		'filtro'  =>array('cliente'=>'Codigo Cliente','nombre'=>'Nombre'),
		'retornar'=>array('cliente'=>'cliente','nomfis'=>'nomfis'),
		'titulo'  =>'Buscar Cliente');
				
		$qformato=$this->qformato=$this->datasis->formato_cpla();
		
		$mCPLA=array(
			'tabla'   =>'cpla',
			'columnas'=>array(
				'codigo' =>'C&oacute;digo',
				'descrip'=>'Descripci&oacute;n'),
			'filtro'  =>array('codigo'=>'C&oacute;digo','denominacion'=>'Denominaci&oacute;n'),
			'retornar'=>array('codigo'=>'cuenta'),
			'titulo'  =>'Buscar Cuenta',
			'where'=>"codigo LIKE \"$qformato\"",
			);
		
		$bsclid =$this->datasis->modbus($mSCLId);
		$bcpla =$this->datasis->modbus($mCPLA);
		
		$link8=site_url('presupuesto/sprv/sugerir/');
		
		$mSPRV=array(
				'tabla'   =>'sprv',
				'columnas'=>array(
				'proveed' =>'C&oacute;odigo',
				'nombre'=>'Nombre',
		    'rif'=>'Rif',
				'contacto'=>'Contacto'),
				'filtro'  =>array('proveed'=>'C&oacute;digo','nombre'=>'Nombre','rif'=>'Rif'),
				'retornar'=>array('proveed'=>'cod_prov'),
				'titulo'  =>'Buscar Beneficiario');

		$bSPRV=$this->datasis->p_modbus($mSPRV,"proveed");
		
		$smenu['link']=barra_menu('131');
		$consulrif=$this->datasis->traevalor('CONSULRIF');
		$consulsnc=$this->datasis->traevalor('CONSULSNC');
		$link2    =site_url('presupuesto/sprv/traesnc');
		$link     =site_url('presupuesto/sprv/uproveed');
		$script ='
		$(function() {
			$("#tr_gr_desc").hide();
			$("#grupo").change(function(){grupo();}).change();
			$(".inputnum").numeric(".");
			$("#banco1").change(function () { acuenta(); }).change();
			$("#banco2").change(function () { acuenta(); }).change();
		});
		function grupo(){
			t=$("#grupo").val();			
			a=$("#grupo :selected").text();
			$("#gr_desc").val(a);
		}
		function acuenta(){
			for(i=1;i<=2;i++){
				vbanco=$("#banco"+i).val();
				if(vbanco.length>0){
					$("#tr_cuenta"+i).show();
				}else{
					$("#cuenta"+i).val("");
					$("#tr_cuenta"+i).hide();
				}
			}		
		}
		function anomfis(){
				vtiva=$("#tiva").val();
				if(vtiva=="C" || vtiva=="E" || vtiva=="R"){
					$("#tr_nomfis").show();
					$("#tr_riff").show();
				}else{
					$("#nomfis").val("");
					$("#rif").val("");
					$("#tr_nomfis").hide();
					$("#tr_rif").hide();
				}
		}
		
		function consulrif(){
				vrif=$("#rif").val();
				if(vrif.length==0){
					alert("Debe introducir primero un RIF");
				}else{
					vrif=vrif.toUpperCase();
					$("#rif").val(vrif);
					window.open("'.$consulrif.'"+"?p_rif="+vrif,"CONSULRIF","height=350,width=410");
				}
		}
		
		function consulsnc(){
				vrif=$("#rif").val();
				if(vrif.length==0){
					alert("Debe introducir primero un RIF");
				}else{
					vrif=vrif.toUpperCase();
					$("#rif").val(vrif);
					window.open("'.$consulsnc.'"+"p=1&rif="+vrif+"&search=RIF","CONSULSNC","height=350,width=1024");
				}
		}
		
		function ultimo(){
			$.ajax({
				url: "'.$link.'",
				success: function(msg){
				  alert( "El ultimo codigo ingresado fue: " + msg );
				}
			});
		}
		
		function sugerir(){
		
			$.ajax({
				url: "'.$link8.'",
				success: function(msg){
					if(msg){
						$("#proveed").val(msg);
					}
					else{
						alert("No es posible generar otra sugerencia. Coloque el c&oacute;digo manualmente");
					}
				}
			});
		}
		
		function traesnc(){
		
				vrif=$("#rif").val();
				if(vrif.length==0){
					alert("Debe introducir primero un RIF");
				}else{
				
					vrif=vrif.toUpperCase();
					$("#rif").val(vrif);
					$.getJSON("'.$link2.'/"+vrif,
						function(data){
							temp=data.existe;
							if(temp=="S"){
						
								temp=data.rnc;
								$("#rnc").val(temp);
								temp=data.nombre;
								$("#nombre").val(temp);
								temp=data.nomfis;
								$("#nomfis").val(temp);
								temp=data.rnc;
								$("#rnc").val(temp);
								temp=data.contacto;
								$("#contacto").val(temp);
								temp=data.telefono;
								$("#telefono").val(temp);
								temp=data.email;
								$("#email").val(temp);
								temp=data.direc1;
								$("#direc1").val(temp);
								temp=data.tipo;
								temp2=data.tipo2;
								$("#tipo").html("<option value="+temp+">"+temp2+"</option>");
								temp=data.url;
								$("#url").val(temp);
								temp=data.vencernc;
								$("#vencernc").val(temp);
								temp=data.objeto;
								$("#objeto").val(temp);
							}else{
								alert("El Rif no se Encuentra Registrado en el registro nacional de Contratistas");
							}
						})
				}
		}
		
		';
		
		$edit = new DataEdit("Beneficiarioes", "sprv");
		$edit->script($script, "create");
		$edit->script($script, "modify");
		$edit->back_url = site_url("presupuesto/sprv/filteredgrid");
			
		$edit->pre_process('delete','_pre_del');		
		//$edit->post_process('insert','_post_insert');
		//$edit->post_process('update','_post_update');
		//$edit->post_process('delete','_post_delete');
		
		$lproveed='<a href="javascript:ultimo();" title="Consultar ultimo codigo ingresado" onclick="">Consultar ultimo codigo</a>';
		$sugerir='<a href="javascript:sugerir();" title="Sugerir un C&oacute;digo aleatorio">Sugerir C&oacute;digo </a>';
				
		$edit->proveed  = new inputField("C&oacute;digo", "proveed");
		$edit->proveed->rule = "required|trim";//callback_chexiste|
		$edit->proveed->mode = "autohide";
		$edit->proveed->size = 13;
		$edit->proveed->maxlength =5;
		$edit->proveed->append($sugerir);
		$edit->proveed->group = "Datos del Beneficiario";
		
		$edit->nombre = new inputField("Nombre", "nombre");
		$edit->nombre->rule = "strtoupper|required|trim";
		$edit->nombre->size = 41;
		$edit->nombre->maxlength =200;
		$edit->nombre->group = "Datos del Beneficiario";
	
		$lriffis='<a href="javascript:consulrif();" title="Consultar RIF en el SENIAT" onclick="">Consultar RIF en el SENIAT</a>';
		$lsnc='<a href="javascript:consulsnc();" title="Consultar RIF en el Servicio Nacional de Contratistas (SNC)" onclick="">Consultar RIF en el Servicio Nacional de Contratistas (SNC)</a>';
		$traesnc='<a href="javascript:traesnc();" title="Traer Datos de RNC">Traer Datos de RNC</a>';
		$edit->rif =  new inputField("RIF o C&eacute;dula", "rif");
		//$edit->rif->mode="autohide";
		$edit->rif->rule = "strtoupper|trim";//|callback_chrif
		$edit->rif->append($lriffis." ".$lsnc." ".$traesnc);
		$edit->rif->maxlength=12;
		$edit->rif->size = 13;
		$edit->rif->group = "Datos del Beneficiario";
		
		//$edit->nit = new inputField("NIT", "nit");
		//$edit->nit->size =15;
		//$edit->nit->maxlength =12;
		//$edit->nit->group = "Datos del Beneficiario";
		
		
		
		$edit->contacto = new inputField("Contacto", "contacto");
		$edit->contacto->size =60;
		$edit->contacto->rule ="trim";
		$edit->contacto->maxlength =100;
		$edit->contacto->group = "Datos del Beneficiario";
		
		$edit->contaci =  new inputField("C&eacute;dula de Indentidad de COntacto", "contaci");		
		$edit->contaci->rule = "strtoupper|trim";//|callback_chrif
		$edit->contaci->maxlength=12;
		$edit->contaci->size = 13;
		$edit->contaci->group = "Datos del Beneficiario";
		//$edit->contaci->in2    ="contacto";
		
		$edit->tipo = new dropdownField("Tipo", "tipo");
		$edit->tipo->option("","");
		$edit->tipo->options(array("1"=> "Juridico Domiciliado","2"=>"Residente", "3"=>"Juridico No Domiciliado","4"=>"No Residente","5"=>"Excluido del Libro de Compras","0"=>"Inactivo"));
		$edit->tipo->style = "width:290px";
		$edit->tipo->group = "Datos del Beneficiario";
		
		$edit->grupo = new dropdownField("Grupo", "grupo");
		$edit->grupo->options("SELECT grupo,gr_desc,grupo FROM grpr ORDER BY gr_desc='01'");
		$edit->grupo->style = "width:290px";
		$edit->grupo->rule = "required";
		$edit->grupo->group = "Datos del Beneficiario";

		$edit->activo = new dropdownField("Activo", "activo");
		$edit->activo->option("S","Activo");
		$edit->activo->option("N","Inactivo");
		$edit->activo->style = "width:290px";
		$edit->activo->group = "Datos del Beneficiario";

		$edit->gr_desc = new inputField("gr_desc", "gr_desc");
		
		$edit->rnc = new inputField("Cod. RNC", "rnc");
		$edit->rnc->size =20;
		$edit->rnc->rule ="trim";
		$edit->rnc->maxlength =40;
		$edit->rnc->group = "Datos del Beneficiario";
		
		$edit->vencernc = new  dateonlyField("Vencimiento en RNC",  "vencernc");
		$edit->vencernc->size =12;
		$edit->vencernc->group = "Datos del Beneficiario";
		//$edit->vencernc->insertValue = date('Y-m-d'); 
		
		$edit->ingreso = new  dateonlyField("Fecha de Ingreso",  "ingreso");
		$edit->ingreso->size =12;
		$edit->ingreso->group = "Datos del Beneficiario";
		$edit->ingreso->insertValue = date('Y-m-d'); 
		
		$ano = date("Y");
		$mes = date("m");
		$dia = date("d");
		$edit->vence = new  dateonlyField("Fecha de Vencimiento",  "vence");
		$edit->vence->size =12;
		$edit->vence->group = "Datos del Beneficiario";
		$edit->vence->insertValue = date("Y-m-d", mktime(0, 0, 0, $dia, $mes, 1+$ano));
		
		$edit->direc1   = new textareaField("Dirección 1", "direc1");
		$edit->direc1->rule  = "trim";
		$edit->direc1->rows  = 5;
		$edit->direc1->cols  = 80;
		$edit->direc1->group = "Datos del Beneficiario";
		
		for($i=2;$i<=3;$i++){
			$obj="direc$i";
			$edit->$obj = new inputField("Direcci&oacute;n $i",$obj);
			$edit->$obj->size =41;
			$edit->$obj->rule ="trim";
			$edit->$obj->maxlength =40;
			$edit->$obj->group = "Datos del Beneficiario";
		}
		
		//$edit->direc1->rule = "required";
								
		$edit->telefono = new inputField("Tel&eacute;fono", "telefono");
		$edit->telefono->size = 41;
		$edit->telefono->rule = "trim";
		$edit->telefono->group = "Datos del Beneficiario";
		$edit->telefono->maxlength =40;
		
		$atts = array(
				'width'     =>'800',
				'height'    =>'600',
				'scrollbars'=>'yes',
				'status'    =>'yes',
				'resizable' =>'yes',
				'screenx'   =>'5',
				'screeny'   =>'5');
				
		$lcli=anchor_popup("/ventas/scli/dataedit/create","Agregar Cliente",$atts);		
		
		$edit->observa  = new inputField("Observaci&oacute;n", "observa");
		$edit->observa->group = "Datos del Beneficiario";
		$edit->observa->rule = "trim";
		$edit->observa->size = 41;
		
		$edit->email  = new inputField("Email", "email");
		$edit->email->rule = "valid_email|trim";
		$edit->email->size =41;
		$edit->email->maxlength =30;
		$edit->email->group = "Datos del Beneficiario";
		
		$edit->url   = new inputField("URL", "url");
		$edit->url->group = "Datos del Beneficiario";
		$edit->url->rule = "trim";
		$edit->url->size =41;
		$edit->url->maxlength =30;
		
		$edit->objeto   = new textareaField("Objeto Social", "objeto");
		$edit->objeto->group = "Datos del Beneficiario";
		$edit->objeto->rule  = "trim";
		$edit->objeto->rows  = 5;
		$edit->objeto->cols  = 80;
			
		$edit->reteiva  = new inputField("% de Retencion","reteiva");
		$edit->reteiva->size = 6;
		$edit->reteiva->css_class='inputnum';
		$edit->reteiva->insertValue = 75;
		
		$edit->maximo  = new inputField("Monto m&aacute;ximo de anticipo","maximo");
		$edit->maximo->rule      = "callback_positivo";
		$edit->maximo->size      = 6;
		$edit->maximo->css_class ='inputnum';
		
		$edit->anti  = new inputField("Anticipado","anti");
		$edit->anti->mode      = "autohide";
		$edit->anti->rule      = "callback_positivo";
		$edit->anti->size      = 6;
		$edit->anti->css_class ='inputnum';
		
		$edit->demos  = new inputField("Demostrado","demos");
		$edit->demos->mode      = "autohide";
		$edit->demos->rule      = "callback_positivo";
		$edit->demos->size      = 6;
		$edit->demos->css_class ='inputnum';
		
		$edit->cod_prov = new inputField("Beneficiario Relacionado", 'cod_prov');
		$edit->cod_prov->db_name  = "cod_prov";
		$edit->cod_prov->size     = 5;
		$edit->cod_prov->append($bSPRV);
		
		$edit->conceptof   = new textareaField("Formula para concepto", "conceptof");
		$edit->conceptof->rule  = "trim";
		$edit->conceptof->rows  = 5;
		$edit->conceptof->cols  = 80;
		
		$edit->concepto   = new textareaField("Concepto", "concepto");
		$edit->concepto->rule  = "trim";
		$edit->concepto->rows  = 10;
		$edit->concepto->cols  = 90;
		
		$edit->numcuent = new inputField("Cuenta Bancaria", 'numcuent');
		$edit->numcuent->size     = 25;
		
		$edit->buttons("modify", "save", "undo", "delete", "back");
		$edit->build();
		
		$smenu['link']=barra_menu('176');
		$data['content'] = $edit->output;
		$data['smenu']   = $this->load->view('view_sub_menu', $smenu,true);        
		$data['title']   = "Beneficiarios";        
		$data["head"]    = script("jquery.pack.js").script("plugins/jquery.numeric.pack.js").script("plugins/jquery.floatnumber.js").$this->rapyd->get_head();
		$this->load->view('view_ventanas', $data);  
	}
	//function _pre_del($do) {
	//	$codigo=$do->get('proveed');
	//	$chek =  $this->datasis->dameval("SELECT count(*) FROM sprm WHERE cod_prv='$codigo'");
	//	$chek += $this->datasis->dameval("SELECT count(*) FROM scst WHERE proveed='$codigo'");
	//	$chek += $this->datasis->dameval("SELECT count(*) FROM gser WHERE proveed='$codigo'");
	//	$chek += $this->datasis->dameval("SELECT count(*) FROM ords WHERE proveed='$codigo'");
	//	$chek += $this->datasis->dameval("SELECT count(*) FROM bmov WHERE clipro='P' AND codcp='$codigo'");	
	//	if ($chek > 0){
	//		$do->error_message_ar['pre_del'] = $do->error_message_ar['delete']='Cliente con Movimiento no puede ser Borrado';
	//		return False;
	//	}
	//	return True;
	//}
	//function _post_insert($do){
	//	$codigo=$do->get('proveed');
	//	$nombre=$do->get('nombre');
	//	logusu('sprv',"PROVEEDOR $codigo NOMBRE $nombre CREADO");
	//}
	//function _post_update($do){
	//	$codigo=$do->get('proveed');
	//	$nombre=$do->get('nombre');
	//	logusu('sprv',"PROVEEDOR $codigo NOMBRE $nombre MODIFICADO");
	//}
	//function _post_delete($do){
	//	$codigo=$do->get('proveed');
	//	$nombre=$do->get('nombre');
	//	logusu('sprv',"PROVEEDOR $codigo NOMBRE $nombre ELIMINADO");
	//}
	function chexiste(){
		$codigo=$this->input->post('proveed');
		$rif=$this->input->post('rif');
		if(strlen($rif)>0){
			$chek=$this->datasis->dameval("SELECT COUNT(*) FROM sprv WHERE proveed='$codigo'");
			if ($chek > 0){
				$nombre=$this->datasis->dameval("SELECT nombre FROM sprv WHERE proveed='$codigo'");
				$this->validation->set_message('chexiste',"El codigo $codigo ya existe para el proveedor $nombre");
				return FALSE;
			}else {
				$chek=$this->datasis->dameval("SELECT COUNT(*) FROM sprv WHERE rif='$rif'");
				if ($chek > 0){
					$nombre=$this->datasis->dameval("SELECT nombre FROM sprv WHERE rif='$rif'");
					$this->validation->set_message('chexiste',"El rif $rif ya existe para el proveedor $nombre");
					return FALSE;
				}else {
	  			return TRUE;
	  		}
			}
		}else{
			return TRUE;
		}
	}
	function _pre_insert($do){
		$rif=$do->get('rif');
		$chek=$this->datasis->dameval("SELECT COUNT(*) FROM sprv WHERE rif='$rif'");
		if($chek > 0){
			//$nombre=$this->datasis->dameval("SELECT nombre FROM sprv WHERE rif='$rif'");
			$do->error_message_ar['pre_insert'] = $do->error_message_ar['insert']='bobo';
			return FALSE;
		}else {
  		return TRUE;
		}	
	}
	function update(){
		$mSQL=$this->db->query("UPDATE sprv SET reteiva=75 WHERE reteiva<>100");
	}
	function uproveed(){
		$consulproveed=$this->datasis->dameval("SELECT proveed FROM sprv ORDER BY proveed DESC");
		echo $consulproveed;
	}
	
	function nombre(){
		$proveed = $this->input->post('proveed');
		echo $this->datasis->dameval("SELECT nombre FROM proveed WHERE proveed = '$proveed' ");
	
	}
	
	function sugerir(){
		$ultimo=$this->datasis->dameval("SELECT CONCAT(LPAD(hexa,4,0),'0') FROM serie LEFT JOIN sprv ON LPAD(proveed,4,0)=LPAD(hexa,4,0) WHERE valor<65535 AND proveed IS NULL LIMIT 1");
		echo $ultimo;
	}
	
	function traesnc($rif='J295780133'){
		//$rif=$this->input->post('rif');
		$html = http_get("http://rncenlinea.snc.gob.ve/reportes/resultado_busqueda?p=1&rif=$rif&search=RIF",array("timeout"=>60,"connecttimeout"=>60));
		
		$inicio = stripos($html,'<a href="/planilla/index');
		$fin    = stripos($html,'?anafinan=N&amp;anafinanpub=Y&amp;login=N&amp;mostrar=INF"');
		$cant   = ($fin-25)-$inicio;
		$codigo   = substr($html,(1*$inicio+25),(1*$cant));
		
		$html = http_get("http://rncenlinea.snc.gob.ve/planilla/index/$codigo?anafinan=N&anafinanpub=Y&login=N&mostrar=INF",array("timeout"=>60,"connecttimeout"=>60));
		
		$inicio = stripos($html,'Direcci&oacute;n Fiscal:');
		$fin    = stripos($html,'Objeto Social:');
		$cant = ($fin-199)-$inicio;
		
		if($codigo>0){
			$retorna['existe']='S';
			
			$direccion = substr($html,(1*$inicio+67),(1*$cant));
			
			$inicio = stripos($html,'Objeto Social:');
			$fin    = stripos($html,'Duraci&oacute;n de la Empresa Actual');
			$cant   = ($fin-199)-$inicio;
			$objeto = substr($html,(1*$inicio+57),(1*$cant+10));
			
			preg_match_all('/class="(fondoP_2|textoP_3)">(?P<cont>.*)<\/td>/',$html,$matches);
			
			$cana=count($matches['cont']);
			$valores=array();
			for($i=0;$i<$cana;$i++){
				$matches['cont'][$i]=str_replace('<br>','',$matches['cont'][$i]);
				if($i%2 == 0){
					$valores[$i]='';
				}else{
					$valores[$i-1]=html_entity_decode($matches['cont'][$i],ENT_COMPAT,'UTF-8');
				}
			}
			
			$retorna['rnc']        = $valores[0];
			$retorna['vencernc']   = $valores[4];
			$retorna['nombre']     = $valores[10];
			$retorna['nomfis']     = $valores[10];
			$retorna['tipo']       = ($matches['cont'][13]=='Persona Jur&iacute;dica'?1:'');
			$retorna['tipo2']      = $valores[12];
			$retorna['contacto']   = $valores[54];
			$retorna['telefono']   = $valores[56].','.$valores[58].','.$valores[60];
			$retorna['email']      = $valores[62];
			$retorna['url']        = $valores[64];
			$retorna['direc1']     = $direccion;
			$retorna['objeto']     = $objeto;
		}else{
			$retorna['existe']='N';
		}
		
		
		//print_r($retorna);
		//echo $valores[12]."__".($valores[12]=='Persona Jurï¿½dica'?'01':'s');
		echo json_encode($retorna);
	}
	
	function autocomplete($cod=FALSE){
		if($cod!==false){
			$mSQL="SELECT proveed AS c1,nombre AS c2 FROM sprv WHERE proveed LIKE '$cod%'";
			$query=$this->db->query($mSQL);
			if($query->num_rows() > 0){
				foreach($query->result() AS $row){
					echo $row->c1.'|'.$row->c2."\n";
				}
			}
		}
	}
	
	function autocomplete2($cod=FALSE){
		if($cod!==false){
			$mSQL="SELECT proveed AS c1,nombre AS c2 FROM sprv WHERE nombre LIKE '$cod%'";
			$query=$this->db->query($mSQL);
			if($query->num_rows() > 0){
				foreach($query->result() AS $row){
					echo $row->c2.'|'.$row->c1."\n";
				}
			}
		}
	}
	
	function nprov(){
		$cod_prov = $this->db->escape($this->input->post("cod_prov"));

		$nombre   = $this->datasis->dameval("SELECT nombre FROM sprv WHERE proveed=$cod_prov");
    echo $nombre;
		//$a=htmlentities($nombre);
		//echo $this->htmlspanishchars($nombre);
		//echo htmlspecialchars($nombre);
		//echo html_entity_decode($a);
		//echo $this->htmlspanishchars($nombre);

	}
	
	function rprov(){
		$cod_prov = $this->db->escape($this->input->post("cod_prov"));
		
		$reteiva_prov = $this->datasis->dameval("SELECT reteiva FROM sprv WHERE proveed=$cod_prov");
		echo $reteiva_prov;
	}
	
	function htmlspanishchars($str)
	{
		return str_replace(array("&lt;", "&gt;"), array("<", ">"), htmlspecialchars($str, ENT_NOQUOTES, "UTF-8"));
	} 
	
	function autocompleteui(){
		$query  ="SELECT proveed,nombre,rif,reteiva FROM sprv";
		$mSQL   = $this->db->query($query);
		$arreglo= $mSQL->result_array($query);
		foreach($arreglo as $key=>$value)
			foreach($value as $key2=>$value2) 
			$arreglo[$key][$key2] = ($value2);
			
		echo json_encode($arreglo);
	}
	
	function _pre_del($do){
		$proveed =$do->get('proveed');
		$proveed2=$this->db->escape($proveed);
		
		$c=$this->datasis->dameval("SELECT SUM(a) FROM (
		SELECT COUNT(*) a FROM ocompra WHERE cod_prov=$proveed2
		UNION ALL
		SELECT COUNT(*) a FROM odirect WHERE cod_prov=$proveed2
		UNION ALL
		SELECT COUNT(*) a FROM mbanc WHERE cod_prov=$proveed2
		UNION ALL
		SELECT COUNT(*) a FROM retenomi WHERE cod_prov=$proveed2
		)t");
		
		if($c>0){
			$do->error_message_ar['pre_del']="ERROR. No se Puede Borrar ya que el Beneficiario tiene movimientos relacionados";
			return false;
		}
	}
	
	

	function instalar(){
		$mSQL='ALTER TABLE `sprv`  ADD COLUMN `objeto` TEXT NULL DEFAULT NULL ';
		$this->db->simple_query($mSQL);
		$mSQL='ALTER TABLE `sprv`  ADD COLUMN `ingreso` DATE NULL DEFAULT NULL  ';
		$this->db->simple_query($mSQL);
		$mSQL='ALTER TABLE `sprv` ADD COLUMN `vence` DATE NULL DEFAULT NULL AFTER ';
		$this->db->simple_query($mSQL);
		$mSQL='ALTER TABLE `sprv`  ADD COLUMN `contaci` VARCHAR(20) NULL DEFAULT NULL ';
		$this->db->simple_query($mSQL);
		$mSQL='ALTER TABLE `sprv`  CHANGE COLUMN `direc1` `direc1` TINYTEXT NULL DEFAULT NULL ';
		$this->db->simple_query($mSQL);
		$mSQL="ALTER TABLE `sprv`  ADD COLUMN `cod_prov` VARCHAR(5) NULL DEFAULT NULL ";
		$this->db->simple_query($mSQL);
		$mSQL="ALTER TABLE `sprv` ADD COLUMN `concepto` TEXT NULL DEFAULT NULL";
		$this->db->simple_query($mSQL);
		$mSQL="ALTER TABLE `sprv` ADD COLUMN `conceptof` TEXT NULL DEFAULT NULL";
		$this->db->simple_query($mSQL);
		$mSQL="ALTER TABLE `sprv`  CHANGE COLUMN `contacto` `contacto` VARCHAR(100) NULL DEFAULT NULL";
		$this->db->simple_query($mSQL);
		$mSQL="ALTER TABLE `sprv`  ADD COLUMN `numcuent` VARCHAR(50) NULL";
		$this->db->simple_query($mSQL);
	}
}
?>
