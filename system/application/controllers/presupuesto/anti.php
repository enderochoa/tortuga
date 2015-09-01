<?php
require_once(BASEPATH.'application/controllers/presupuesto/common.php');
class anti extends Common {
	var $genesal=true;
	var $titp='Anticipos de Gastos';
	var $tits='Anticipo de Gasto';
	var $url ='presupuesto/anti/';

	function anti(){
		parent::Controller();
		$this->load->library("rapyd");
		//$this->datasis->modulo_id(117,1);
	}
	
	function index(){
		redirect($this->url."filteredgrid");
	}
	
	function filteredgrid(){
		
		$this->rapyd->load("datafilter","datagrid");
		
		$mSPRV=array(
						'tabla'   =>'sprv',
						'columnas'=>array(
							'proveed' =>'C&oacute;odigo',
							'nombre'=>'Nombre',
							'contacto'=>'Contacto'),
						'filtro'  =>array(
							'proveed'=>'C&oacute;digo',
							'nombre'=>'Nombre'),
						'retornar'=>array(
							'proveed'=>'cod_prov'),
						'titulo'  =>'Buscar Beneficiario');
		
		$bSPRV=$this->datasis->p_modbus($mSPRV,"proveed");
		
//		$filter = new DataFilter("","odirect");
//		$filter->db->where('MID(status,1,1) ','G');
		$filter = new DataFilter("");
		$filter->db->select("a.reverso reverso,a.total,a.numero numero,a.fecha fecha,a.tipo tipo,a.status status,a.cod_prov cod_prov,a.beneficiario beneficiario,b.nombre uejecuta2,c.nombre proveed");
		$filter->db->from("odirect a");                  
		$filter->db->join("uejecutora b" ,"a.uejecutora=b.codigo");
		$filter->db->join("sprv c"       ,"c.proveed=a.cod_prov");
		$filter->db->where('MID(status,1,1) ','G');
		
		$filter->numero = new inputField("N&uacute;mero", "numero");
		$filter->numero->size=12;
		
		$filter->fecha = new dateonlyField("Fecha", "fecha");
		$filter->fecha->dbformat = "Y-m-d";
		$filter->fecha->size=12;
		
		//$filter->uejecutora = new inputField("Unidad Ejecutora", "uejecutora");
		//$filter->uejecutora->size=12;
		
		$filter->cod_prov = new inputField("Beneficiario", 'cod_prov');
		$filter->cod_prov->size = 6;
		$filter->cod_prov->append($bSPRV);
		$filter->cod_prov->rule = "required";
		
		$filter->total = new inputField("Pago", "total");
		$filter->total->size=12;
		
		$filter->status = new dropdownField("Estado","status");
		$filter->status->option("","");
		$filter->status->option("G1","Sin Ejecutar");
		$filter->status->option("G2","Ejecutado"   );
		$filter->status->option("G3","Pagado"      );
		$filter->status->style="width:94px";
		
		$filter->buttons("reset","search");
		
		$filter->build();
		
		function sta($status){
			switch($status){
				case "G1":return "Sin Ejecutar";break;
				case "G2":return "Ejecutado"   ;break;
				case "G3":return "Pagado"      ;break;
				case "GA":return "Anulado"      ;break;
			}
		}	
		
		$uri = anchor($this->url.'dataedit/show/<#numero#>','<str_pad><#numero#>|8|0|STR_PAD_LEFT</str_pad>');
		
		$grid = new DataGrid("");
		$grid->order_by("numero","desc");
		$grid->per_page = 20;
		$grid->use_function('substr','str_pad','sta');
		
		$grid->column_orderby("N&uacute;mero"    ,$uri     ,"numero");
		$grid->column_orderby("Fecha"            ,"<dbdate_to_human><#fecha#></dbdate_to_human>"   ,"fecha"         ,"align='center'"      );
		//$grid->column("Unidad Ejecutora" ,"uejecutora");
		$grid->column_orderby("Beneficiario"     ,"proveed"                                        ,"proveed"       ,"align='left'  NOWRAP");
		$grid->column_orderby("Pago"             ,"<number_format><#total#>|2|,|.</number_format>" ,"total"         ,"align='right'"       );
		$grid->column_orderby("Estado"           ,"<sta><#status#></sta>"                          ,"status"        ,"align='center'NOWRAP");
		
		//echo $grid->db->last_query();
		$grid->add($this->url."dataedit/create");
		$grid->build();
		//echo $grid->db->last_query();
		
		//$data['content'] = $filter->output.$grid->output;
		$data['filtro']  = $filter->output;        
		$data['content'] = $grid->output;         
		$data['title']   = "Fondo en Avance";
		$data['script'] = script("jquery.js")."\n";
		$data["head"]    = $this->rapyd->get_head();
		$this->load->view('view_ventanas', $data);
	}
	
	function dataedit(){
	
		//$this->datasis->modulo_id(101,1);
		$this->rapyd->load('dataedit','dataobject');
		
		$script='
		$(function() {
			$(".inputnum").numeric(".");
			$("#estadmin").change(function(){
				$.post("'.site_url('presupuesto/presupuesto/get_tipo').'",{ codigoadm:$("#estadmin").val() },function(data){$("#fondo").html(data);})
			});
		});
		
		function btn_anulaf(i){
			if(!confirm("Esta Seguro que desea Anular El Fondo en Avance ?"))
				return false;
			else
				window.location="'.site_url($this->url.'anular').'/"+i
		}
		
		';
		
		$mSPRV=array(
			'tabla'   =>'sprv',
			'columnas'=>array(
			'proveed' =>'C&oacute;odigo',
			'nombre'=>'Nombre',
			'rif'=>'Rif',
			'contacto'=>'Contacto'),
			'filtro'  =>array('proveed'=>'C&oacute;digo','nombre'=>'Nombre','rif'=>'Rif'),
			'retornar'=>array('proveed'=>'codprov','nombre'=>'nombrep'),
			'titulo'  =>'Buscar Beneficiario');
		
		$bSPRV =$this->datasis->p_modbus($mSPRV ,"sprv");
		
		$do = new DataObject("odirect");
		$do->pointer('sprv' ,'sprv.proveed=odirect.cod_prov','sprv.nombre as nombrep','LEFT');
		
		$edit = new DataEdit($this->tits, $do);
		
		$edit->back_url = site_url($this->url."filteredgrid");
		
		$edit->script($script,"create");
		$edit->script($script,"modify");
		$edit->script($script,"show"  );
		
		$edit->pre_process('insert'  ,'_valida');
		$edit->pre_process('update'  ,'_valida');
		$edit->post_process('insert'  ,'_post');
		$edit->post_process('update'  ,'_post');
		
		$edit->numero  = new inputField("N&uacute;mero", "numero");
		$edit->numero->mode="autohide";
//		$edit->numero->when=array('show');
		 
		$edit->fecha = new dateonlyField("Fecha", "fecha");
		$edit->fecha->insertValue = date('Y-m-d');
		$edit->fecha->size        =12;     
		$edit->fecha->rule        = 'required';
		
		$edit->uejecutora = new dropdownField("Unidad Ejecutora", "uejecutora");
		$edit->uejecutora->option("","Seccionar");
		$edit->uejecutora->options("SELECT codigo, nombre FROM uejecutora ORDER BY nombre");
		//$edit->uejecutora->onchange = "get_uadmin();";
		$edit->uejecutora->rule = "required";
		
		$edit->codprov = new inputField("Beneficiario", 'codprov');
		$edit->codprov->db_name  = "cod_prov";
		$edit->codprov->size     = 4;
		$edit->codprov->rule     = "required";
		$edit->codprov->readonly =true;
		$edit->codprov->append($bSPRV);
	
		$edit->nombrep = new inputField("Nombre", 'nombrep');
		$edit->nombrep->size     = 20;
		$edit->nombrep->readonly = true;
		$edit->nombrep->pointer  = TRUE;
		$edit->nombrep->in       = "codprov";
		
		//$edit->beneficiario = new inputField("Beneficiario", 'beneficiario');
		//$edit->beneficiario->size = 50;
		//$edit->beneficiario->rule = "required";
		
		$edit->observa = new textAreaField("Observaciones", 'observa');
		$edit->observa->cols = 60;
		$edit->observa->rows = 4;
		
		$edit->total = new inputField("Monto", 'total');
		$edit->total->rule     ='required|callback_positivo';
		$edit->total->css_class='inputnum';
		$edit->total->size     = 15;
		
		$status=$edit->_dataobject->get('status');
		if($status=='G1'){
			
			$action = "javascript:window.location='" .site_url($this->url.'actualizar/'.$edit->rapyd->uri->get_edited_id()). "'";
			$edit->button_status("btn_status",'Actualizar',$action,"TR","show");
			$action = "javascript:btn_anulaf('".$edit->rapyd->uri->get_edited_id(). "')";
			if($this->datasis->puede(168))$edit->button_status("btn_anular",'Anular',$action,"TR","show");
			$edit->buttons("modify","save");
		}elseif($status=='G2'){
			//$action = "javascript:window.location='" .site_url($this->url.'reversar/'.$edit->rapyd->uri->get_edited_id()). "'";
			//$edit->button_status("btn_rever",'Reversar',$action,"TR","show");
			$action = "javascript:btn_anulaf('".$edit->rapyd->uri->get_edited_id(). "')";
			if($this->datasis->puede(168))$edit->button_status("btn_anular",'Anular',$action,"TR","show");	
		
		}elseif($status=='G'){
			$edit->buttons("modify","save");
			
		}else{
			$edit->buttons("save");
		}
				
		$edit->buttons("undo", "back");
		$edit->build();
		
		if($this->genesal){
			$smenu['link']   = barra_menu('171');
			$data['smenu']   = $this->load->view('view_sub_menu', $smenu,true);
			$data['content'] = $edit->output;		
			$data['title']   = "$this->tits";
			$data["head"]    = script("jquery.pack.js").script("plugins/jquery.numeric.pack.js").script("plugins/jquery.floatnumber.js").$this->rapyd->get_head();
			$this->load->view('view_ventanas', $data);
		}else{
			$edit->error_string;
		}
	}
	
	function exterin(){

		if(count($_POST))
		foreach($_POST AS $key =>$value)
			$_POST[$key]=$value;
		
		$this->genesal=false;
		$this->dataedit();
	}

	function actualizar($id){
		$this->rapyd->load('dataobject');
		$do  = new DataObject("odirect");	
		
		$do->load($id);
		
		$error = "";
		
		$status = $do->get('status');
		$numero = $do->get('numero');
		if($status == "G1"){
			$do->set('status','G2');
			$total  = $do->get("total");
			
			$cod_prov = $do->get('cod_prov');
		
			$SPRV= new DataObject("sprv");
			$SPRV->load($cod_prov);

			$anti   = $SPRV->get('anti'  );
			$maximo = $SPRV->get('maximo');
			$demos  = $SPRV->get('demos' );
			$nombre = $SPRV->get('nombre');
			$saldo  = $maximo - ($anti - $demos   );
						
			if($total>$saldo)
				$error.="<div class='alert'><p>El monto del anticipo ($total) es mayor al disponible (".($saldo).") para el proveedor $nombre</p></div>";
			
			if(empty($error)){
				$SPRV->set('anti' ,$anti+$total);
				$SPRV->save();
				$do->save();
			}
		}else{
			$error.="<div class='alert'><p>No se puede Realizar la operaci&oacute;n</p></div>";
		}
		
		if(empty($error)){
			$do->set('fopago',date('Ymd'));
			$do->save();
			
			logusu('anti',"Actualizo Fondo en Avance $numero");
			redirect($this->url."dataedit/show/$id");
		}else{
			logusu('anti',"Actualizo Fondo en Avance $numero con error $error");
			$data['content'] = $error.anchor($this->url."dataedit/show/$id",'Regresar');
			$data['title']   = " $this->tits ";
			$data["head"]    = $this->rapyd->get_head().script('jquery.js').script("plugins/jquery.numeric.pack.js");
			$this->load->view('view_ventanas', $data);
		}
	}
	
	function reversar($id){
	
		$this->rapyd->load('dataobject');
		$do  = new DataObject("odirect");	
		
		$do->load($id);
		
		$error = "";
		
		$status = $do->get('status');
		$numero = $do->get('numero');
		if($status == "G2"){
			
			$total  = $do->get("total");
			
			$cod_prov = $do->get('cod_prov');
			$fopago   = $do->get('fopago'  );
		
			$SPRV= new DataObject("sprv");
			$SPRV->load($cod_prov);

			$anti   = $SPRV->get('anti'  );
			$maximo = $SPRV->get('maximo');
			$demos  = $SPRV->get('demos' );
			$nombre = $SPRV->get('nombre');
			$saldo  = ($anti - $demos    );
						
			if($total>$saldo)
				$error.="<div class='alert'><p>El monto ($total) es mayor al disponible para reversar (".($saldo).") para el proveedor $nombre</p></div>";
			
			if(empty($error)){
				if(date('m',strtotime($fopago)) != date('m')){
					$odirect  = new DataObject("odirect");
					$odirect  = $do;
					$odirect->unset_pk();
					$odirect->loaded=0;					
					$odirect->set('status'   ,'GX'       );
					$odirect->set('reverso'  ,$id        );
					$odirect->set('fopago'   ,date('Ymd'));
					$odirect->save();
				}
				$SPRV->set('anti' ,$anti-$total);
				$SPRV->save();
			}
		}else{
			$error.="<div class='alert'><p>No se puede Realizar la operaci&oacute;n</p></div>";
		}
		
		if(empty($error)){
			return '';
		}else{	
			return $error;
		}
	}
	
	function anular($id,$redi=true){
		$this->rapyd->load('dataobject');

		$odirect  = new DataObject("odirect");
		$odirect -> load($id);
		$fopago   = $odirect->get('fopago');
		$status   = $odirect->get('status');

		$this->redirect = false;
		if($status=="G2")
		$error=$this->reversar($id);

		if(empty($error)){
			if(date('m',strtotime($fopago)) != date('m')){
				$odirect->set('status','GY');
			}else{
				$odirect->set('status','GA');
			}
			
			$odirect->save();
		}

		if(empty($error)){
			logusu('odirect',"Anulo Fondo en Avance Nro $id");
			if($redi)redirect("presupuesto/anti/dataedit/show/$id");
		}else{
			logusu('odirect',"Anulo Fondo en Avance Nro $id. con ERROR:$error ");
			$data['content'] = $error.anchor("presupuesto/anti/dataedit/show/$id",'Regresar');
			$data['title']   = " Fondo en Avance ";
			$data["head"]    = $this->rapyd->get_head();
			$this->load->view('view_ventanas', $data);
		}
	}
	
	function _valida($do){
		//return false;exit('aa');
		$error='';
		$total  = $do->get("total");
		
		$do->set('subtotal',$total);
		$do->set('total2'  ,$total);

		$do->set('status'  ,'G1' );
		if(empty($error)){
			
			if(empty($do->loaded)){
				$nodirect=$this->datasis->fprox_numero('nodirect');
				$do->set('numero',$nodirect);
				$do->pk=array('numero'=>$nodirect);
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
	
	function _post($do){
		$id = $do->get('numero');
		logusu('anti',"Cambio/creo Fondo en Avance $id");
		//redirect($this->url."actualizar/$id");
	}
}