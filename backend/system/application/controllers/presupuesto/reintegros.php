<?php
class reintegros extends Controller {
	
	var $titp='Reintegros';
	var $tits='Reintegro';
	var $url ='presupuesto/reintegros/';

	function reintegros(){
		parent::Controller();
		$this->load->library("rapyd");
		$this->datasis->modulo_id(76,1);
	}
	
	function index(){
		redirect($this->url."filteredgrid");
	}
	
	function filteredgrid(){
		//$this->datasis->modulo_id(101,1);
		$this->rapyd->load("datafilter","datagrid");

		$mSPRV=array(
				'tabla'   =>'sprv',
				'columnas'=>array(
				'proveed' =>'C&oacute;odigo',
				'nombre'=>'Nombre',
				'contacto'=>'Contacto'),
				'filtro'  =>array('proveed'=>'C&oacute;digo','nombre'=>'Nombre'),
				'retornar'=>array('proveed'=>'cod_prov' ),
				'titulo'  =>'Buscar Beneficiario');

		$bSPRV=$this->datasis->p_modbus($mSPRV,"proveed");

		$filter = new DataFilter("","rendi");
		$filter->db->where('MID(status,1,1) ','R');
	
		$filter->fecha = new dateonlyField("Fecha", "fecha");
		$filter->fecha->dbformat = "Y-m-d";
		$filter->fecha->size = 12;

		$filter->buttons("reset","search");

		$filter->build();
		$uri = anchor($this->url.'dataedit/show/<#numero#>','<str_pad><#numero#>|8|0|STR_PAD_LEFT</str_pad>');

		$grid = new DataGrid("");
		$grid->order_by("numero","desc");
		$grid->per_page = 20;
		$grid->use_function('substr','str_pad');

		$grid->column_orderby("N&uacute;mero"    ,$uri                                             ,"numero");
		$grid->column_orderby("Fecha"            ,"<dbdate_to_human><#fecha#></dbdate_to_human>"   ,"fecha"        ,"align='center'");
		$grid->column_orderby("Pago"             ,"<number_format><#total#>|2|,|.</number_format>" ,"total"        ,"align='right'");

		$grid->add($this->url."dataedit/create");
		$grid->build();

		//$data['content'] = $filter->output.$grid->output;
		$data['filtro']  = $filter->output;
		$data['content'] = $grid->output;
		$data['script'] = script("jquery.js")."\n";
		$data['title']   = "$this->titp";
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
				if(!confirm("Esta Seguro que desea Anular El Reintegro ?"))
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
			'retornar'=>array('proveed'=>'cod_prov','nombre'=>'nombrep'),
			'titulo'  =>'Buscar Beneficiario');
		
		$bSPRV =$this->datasis->p_modbus($mSPRV ,"sprv");
		
		$do = new DataObject("rendi");
		$do->pointer('sprv' ,'sprv.proveed=rendi.cod_prov','sprv.nombre as nombrep');
		
		$edit = new DataEdit($this->tits,$do);
		
		$edit->back_url = site_url($this->url."filteredgrid");
		
		$edit->script($script,"create");
		$edit->script($script,"modify");
		$edit->script($script,"show"  );
		
		$edit->pre_process('update'   ,'_valida');
		$edit->pre_process('insert'   ,'_valida');
		//$edit->post_process('insert'  ,'_post');
		//$edit->post_process('update'  ,'_post');
		
		$edit->numero  = new inputField("N&uacute;mero", "numero");
		$edit->numero->mode="autohide";
		$edit->numero->when=array('show');
		
		$edit->cod_prov = new inputField("Beneficiario", 'cod_prov');
		$edit->cod_prov->db_name  = "cod_prov";
		$edit->cod_prov->size     = 4;//$edit->anticipo  = new inputField("Anticipo", "anticipo");
		$edit->cod_prov->rule     = "required";//$edit->anticipo->size=10;
		$edit->cod_prov->readonly =true;////$edit->anticipo->rule="required";
		$edit->cod_prov->append($bSPRV);//$edit->anticipo->append($bODIRECT);
		
		$edit->nombrep = new inputField("Nombre", 'nombrep');
		$edit->nombrep->size     = 40;
		$edit->nombrep->readonly = true;
		$edit->nombrep->pointer  = TRUE;
		$edit->nombrep->in       = "cod_prov";
		 
		$edit->fecha = new dateonlyField("Fecha", "fecha");
		$edit->fecha->insertValue = date('Y-m-d');
		$edit->fecha->size        =12;     
		$edit->fecha->rule        = 'required';
		
		$edit->observa = new textAreaField("Observaciones", 'observa');
		$edit->observa->cols = 60;
		$edit->observa->rows = 3;
		
		$edit->total = new inputField("Monto", 'total');
		$edit->total->rule     ='required|callback_positivo';
		$edit->total->css_class='inputnum';
		$edit->total->size = 15;
		
		$status=$edit->_dataobject->get('status');
		if($status=='R1'){
			$action = "javascript:window.location='" .site_url($this->url.'actualizar/'.$edit->rapyd->uri->get_edited_id()). "'";
			$edit->button_status("btn_status",'Actualizar',$action,"TR","show");
			$edit->buttons("modify","delete","save");
		}elseif($status=='R2'){
			$action = "javascript:btn_anulaf('".$edit->rapyd->uri->get_edited_id()."')";
			$edit->button_status("btn_rever",'Anular',$action,"TR","show");        
		}else{
			$edit->buttons("save");
		}
				
		$edit->buttons("undo", "back");
		$edit->build();
		
		$data['content'] = $edit->output;
    $data['title']   = "$this->tits";
    $data["head"]    = script("jquery.pack.js").script("plugins/jquery.numeric.pack.js").script("plugins/jquery.floatnumber.js").$this->rapyd->get_head();
    $this->load->view('view_ventanas', $data);
	}
	
	function positivo($valor){
		if ($valor < 0){
			$this->validation->set_message('positivo',"El monto debe ser positivo");
			return FALSE;
		}
  	return TRUE;
	}
	
	function actualizar($id){

		$this->rapyd->load('dataobject');
		$do = new DataObject("rendi");
		$do->load($id);
		
		$error      = "";
		
		$cod_prov = $do->get('cod_prov');
		$ttotal   = $do->get('total');
		
		$SPRV= new DataObject("sprv");
		$SPRV->load($cod_prov);

		$anti   = $SPRV->get('anti'  );
		$maximo = $SPRV->get('maximo');
		$demos  = $SPRV->get('demos' );
		$nombre = $SPRV->get('nombre');
		$saldo  = ($anti - $demos    );
		
		if(($ttotal)>($saldo))
			$error.="<div class='alert'><p>El monto del $this->tits ($ttotal) es mayor que el monto adeudado ($saldo) del proveedor $nombre</p></div>";
		
		$status = $do->get('status');
		if($status == "R1" && empty($error)){
			$SPRV ->set('demos',$demos+$ttotal);
			$SPRV ->save();		
			$do->set('status'  ,'R2');
			$do->save();
		}else{
			$error.="<div class='alert'><p>No se puede Realizar la operaci&oacute;n</p></div>";
		}
		
		if(empty($error)){
			logusu('reinte',"Actualizo reintegro Nro $id");
			redirect($this->url."dataedit/show/$id");
		}else{
			logusu('reinte',"Actualizo reintegro Nro $id con $error");
			$data['content'] = $error.anchor($this->url."dataedit/show/$id",'Regresar');
			$data['title']   = " $this->tits ";
			$data["head"]    = $this->rapyd->get_head();
			$this->load->view('view_ventanas', $data);
		}
	}
	
	function anular($id){
		$this->rapyd->load('dataobject');
		$do = new DataObject("rendi"   );
		$do->load($id);
		
		$error      = "";
		
		$cod_prov = $do->get('cod_prov');
		$ttotal   = $do->get('total');
		
		$SPRV= new DataObject("sprv");
		$SPRV->load($cod_prov);

		$anti   = $SPRV->get('anti'  );
		$maximo = $SPRV->get('maximo');
		$demos  = $SPRV->get('demos' );
		$nombre = $SPRV->get('nombre');
		$saldo  = $demos;
		
		if(($ttotal)>($saldo))
			$error.="<div class='alert'><p>El monto del $this->tits ($ttotal) es mayor que el monto posible a reversar ($saldo) del proveedor $nombre</p></div>";
		
		$status = $do->get('status');
		if($status == "R2" && empty($error)){
			$SPRV ->set('demos',$demos-$ttotal);
			$SPRV ->save();		
			$do->set('status'  ,'AN');
			$do->save();
		}else{
			$error.="<div class='alert'><p>No se puede Realizar la operaci&oacute;n</p></div>";
		}
		
		if(empty($error)){
			logusu('reinte',"Actualizo reintegro Nro $id");
			redirect($this->url."dataedit/show/$id");
		}else{
			logusu('reinte',"Actualizo reintegro Nro $id con $error");
			$data['content'] = $error.anchor($this->url."dataedit/show/$id",'Regresar');
			$data['title']   = " $this->tits ";
			$data["head"]    = $this->rapyd->get_head();
			$this->load->view('view_ventanas', $data);
		}
	}
	
	function _valida($do){	
		
		$do->set('status'  ,'R1'  );
	}

	function _post($do){
		$id = $do->get('numero');
		redirect($this->url."actualizar/$id");
	}
}