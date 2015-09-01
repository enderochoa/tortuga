<?php
class movi extends Controller {
	 
	var $titp='Anticipos de Gastos';
	var $tits='Anticipo de Gasto';
	var $url ='presupuesto/movi/';

	function movi(){
		parent::Controller();
		$this->load->library("rapyd");
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
				'retornar'=>array('proveed'=>'cod_prov'),
				'titulo'  =>'Buscar Beneficiario');
			
		$bSPRV=$this->datasis->p_modbus($mSPRV,"proveed");
		
		$filter = new DataFilter("Filtro de $this->titp","movi");
		
		$filter->tipo = new dropdownField("Orden de ", "tipo");
		$filter->tipo->option("","");
		$filter->tipo->option("A","Anticipo");
		$filter->tipo->option("R","Reintegro");
		$filter->tipo->style="width:100px;";

		$filter->fecha = new dateonlyField("Fecha", "fecha");
		$filter->fecha->size=12;
		
		$filter->uejecutora = new inputField("Unidad Ejecutora", "uejecutora");
		$filter->uejecutora->size=12;
		
		$filter->cod_prov = new inputField("Beneficiario", 'cod_prov');
		$filter->cod_prov->size = 6;
		$filter->cod_prov->append($bSPRV);
		$filter->cod_prov->rule = "required";
		
		$filter->beneficiario = new inputField("Beneficiario", "beneficiario");
		$filter->beneficiario->size=60;
		
		//$filter->status = new dropdownField("Estado","status");
		//$filter->status->option("","");
		//$filter->status->option("A","Procesado");
		//$filter->status->option("B","Sin Procesar");
		//$filter->status->style="width:150px";
		
		$filter->buttons("reset","search");
		$filter->build();
		
		$uri = anchor($this->url.'dataedit/show/<#numero#>','<str_pad><#numero#>|8|0|'.STR_PAD_LEFT.'</str_pad>');
		
		$grid = new DataGrid("Lista de ".$this->titp);
		$grid->order_by("numero","desc");
		$grid->per_page = 20;
		$grid->use_function('substr','str_pad');
		
		$grid->column("N&uacute;mero"    ,$uri);
		$grid->column("Tipo"             ,"tipo"                                        ,"align='center'");
		$grid->column("Fecha"            ,"<dbdate_to_human><#fecha#></dbdate_to_human>","align='center'");
		$grid->column("Unidad Ejecutora" ,"uejecutora");
		$grid->column("Beneficiario"        ,"cod_prov");
		//$grid->column("Beneficiario"     ,"beneficiario");
		$grid->column("Monto"            ,"<number_format><#monto#>|2|,|.</number_format>","align='right'");
		$grid->column("Demostrado"       ,"<number_format><#saldo#>|2|,|.</number_format>","align='right'");
		//$grid->column("Estado"           ,"status"                                        ,"align='center'");

		$grid->add($this->url."dataedit/create");
		$grid->build();
		
		$data['content'] = $filter->output.$grid->output;
		$data['title']   = " $this->titp ";
		$data["head"]    = $this->rapyd->get_head();
		$this->load->view('view_ventanas', $data);	
	}
	
	function dataedit(){
		//$this->datasis->modulo_id(101,1);
		$this->rapyd->load('dataedit');
		
		$mSPRV=array(
				'tabla'   =>'sprv',
				'columnas'=>array(
				'proveed' =>'C&oacute;odigo',
				'nombre'=>'Nombre',
				'contacto'=>'Contacto'),
				'filtro'  =>array('proveed'=>'C&oacute;digo','nombre'=>'Nombre'),
				'retornar'=>array('proveed'=>'cod_prov', 'nombre'=>'nombre'),
				'titulo'  =>'Buscar Beneficiario');

		$bSPRV=$this->datasis->p_modbus($mSPRV,"proveed");

		$mBANC=array(
				'tabla'   =>'banc',
				'columnas'=>array(
					'codbanc' =>'C&oacute;odigo',
					'banco'=>'Banco',
					'saldo'=>'Saldo'),
				'filtro'  =>array(
					'codbanc' =>'C&oacute;odigo',
					'banco'=>'Banco',
					'saldo'=>'Saldo'),
				'retornar'=>array(
					'codbanc'=>'codbanc',
					'banco'=>'nombreb' ),
				'where'=>'activo="S"',
				'titulo'  =>'Buscar Bancos');
			
		$bBANC=$this->datasis->p_modbus($mBANC,"banc");
		
		//$do = new DataObject("ocompra");
		$script='
			$(".inputnum").numeric(".");
		';
		
		$edit = new DataEdit($this->tits, "movi");
		
		$edit->back_url = site_url($this->url."filteredgrid");
		
		$edit->script($script,"create");
		$edit->script($script,"modify");
		
		//$edit->pre_process('insert'  ,'_valida');
		//$edit->pre_process('update'  ,'_valida');
		
		//$edit->numero  = new inputField("N&uacute;mero", "numero");
		//$edit->numero->mode="autohide";
		//$edit->numero->when=array('show');
		
		//$edit->tipo = new dropdownField("Orden de", "tipo");
		//$edit->tipo->option("A","Anticipo");
		//$edit->tipo->option("R","Reintegro"); 
		//$edit->tipo->style="width:100px;";
		
		$edit->tipo = new inputField("","tipo");
		$edit->tipo-> insertValue = "A";
		$edit->tipo->mode         = "autohide";
		$edit->tipo->when=array('');
		
		$edit->status = new inputField("","status");
		$edit->status-> insertValue = "P";
		$edit->status->mode         = "autohide";
		$edit->status->when=array('');

		$edit->fecha = new  dateonlyField("Fecha",  "fecha");
		$edit->fecha->insertValue = date('Y-m-d');
		$edit->fecha->size =12;  
		$edit->fecha->rule = "required";

		$edit->uejecutora = new dropdownField("Unidad Ejecutora", "uejecutora");
		$edit->uejecutora->option("","Seccionar");
		$edit->uejecutora->options("SELECT codigo, nombre FROM uejecutora ORDER BY nombre");
		$edit->uejecutora->rule       = 'required';
		//$edit->uejecutora->onchange = "get_uadmin();";
		//$edit->uejecutora->rule = "required";
		
		//$edit->cod_prov = new inputField("Beneficiario", 'cod_prov');
		//$edit->cod_prov->size     = 6;		
		////$edit->cod_prov->rule     = "required";
		//$edit->cod_prov->append($bSPRV);
		//$edit->cod_prov->readonly=true;
    //
		//$edit->nombre = new inputField("Nombre", 'nombre');
		//$edit->nombre->size     = 50;
		//$edit->nombre->readonly = true;
		//$edit->nombre->in       ="cod_prov";
		
		$edit->beneficiario = new inputField("Beneficiario", 'beneficiario');
		$edit->beneficiario->size       = 50;
		$edit->beneficiario->maxlength  = 50;
		$edit->beneficiario->rule       = 'required';
		
		$edit->observa = new textAreaField("Observaci&oacute;nes", 'observa');
		$edit->observa->cols       = 106;
		$edit->observa->rows       = 3;
		$edit->observa->rule       = 'required';
		
		//$edit->codbanc = new inputField("Banco", 'codbanc');
		//$edit->codbanc->size     = 6;		
		//$edit->codbanc->rule     = "required";
		//$edit->codbanc->append($bBANC);
		//$edit->codbanc->readonly=true;
		//
		//$edit->nombreb = new inputField("Nombre", 'nombreb');
		//$edit->nombreb->size     = 50;
		//$edit->nombreb->readonly = true;
		//$edit->nombreb->in       ="codbanc";

		$edit->monto = new inputField("Monto", 'monto');
		$edit->monto ->size      = 20;
		$edit->monto ->css_class = 'inputnum';
		$edit->monto->rule       = 'required|callback_positivo';
		
	//	$status=$edit->_dataobject->get("status");		
	//	if($status=='P'){
	//		$action = "javascript:window.location='" .site_url($this->url.'actualizar/'.$edit->rapyd->uri->get_edited_id()). "'";
	//		$edit->button_status("btn_status",'Actualizar',$action,"TR","show");
	//		$edit->buttons("modify","delete","save");
	//	}elseif($status=='C'){
	//		$action = "javascript:window.location='" .site_url($this->url.'reversar/'.$edit->rapyd->uri->get_edited_id()). "'";
	//		$edit->button_status("btn_rever",'Reversar',$action,"TR","show");        
	//	}else{
	//		$edit->buttons("save");
	//	}
		
		$edit->buttons("modify","delete","save","undo", "back");
		$edit->build();
		
		$data['content'] = $edit->output;
    $data['title']   = " $this->tits ";
    $data["head"]    = script("jquery.pack.js").script("plugins/jquery.numeric.pack.js").script("plugins/jquery.floatnumber.js").$this->rapyd->get_head();
    $this->load->view('view_ventanas', $data);
	}
	
	function positivo($valor){
		if ($valor < 0){
			$this->validation->set_message('positivo',"El campo monto debe ser positivos");
			return FALSE;
		}
  	return TRUE;
	}
	
	function actualizar($id){
		$this->rapyd->load('dataobject');
		$do  = new DataObject("movi");
		$do->rel_one_to_many('itodirect', 'itodirect', array('numero'=>'numero'));
		$do->load($id);
		
		$do2 = new DataObject("banc");

		$do->load($id);
		$monto    = $do->get('monto');
		$codbanc  = $do->get('codbanc');
		$tipo     = $do->get('tipo');
		$status   = $do->get('status');
		
		
		$do2->load($codbanc);
		$saldo    = $do2->get('saldo' );
		$activo   = $do2->get('activo');
		$error='';
		
		if($activo!='S')
			$error.="<div class='alert'><p>El Banco ($codbanc) esta inactivo</p></div>";
		
		if($tipo!='A')
			$error.="<div class='alert'><p>No se puede actualizar el registro n&uacute;mero ($id)</p></div>";
			
		if($status!='P')
			$error.="<div class='alert'><p>No se puede Realizar la operaci&oacute;n</p></div>";
			
		if($monto > $saldo)
			$error.="<div class='alert'><p>El monto del anticipo ($monto) es mayor que el saldo ($saldo) del banco ($codbanc)</p></div>";
		
		      
		if(empty($error)){
			$saldo  -=$monto;
			$saldo;
			$do->set('status','C');
			$do2->set('saldo',$saldo);
			$do->save();
			$do2->save();
			redirect($this->url."dataedit/show/$id");
		}else{
			$data['content'] = $error.anchor($this->url."dataedit/show/$id",'Regresar');
			$data['title']   = " $this->tits ";
			$data["head"]    = $this->rapyd->get_head().script('jquery.js').script("plugins/jquery.numeric.pack.js");
			$this->load->view('view_ventanas', $data);
		}
		
	}
	
	function reversar($id){
		$this->rapyd->load('dataobject');
		$do  = new DataObject("movi");
		$do2 = new DataObject("banc");

		$do->load($id);
		$monto    = $do->get('monto');
		$codbanc  = $do->get('codbanc');
		$tipo     = $do->get('tipo');
		$status   = $do->get('status');
		
		
		$do2->load($codbanc);
		$saldo    = $do2->get('saldo' );
		$activo   = $do2->get('activo');
		$error='';
		
		if($activo!='S')
			$error.="<div class='alert'><p>El Banco ($codbanc) nesta inactivo</p></div>";
		
		if($tipo!='A')
			$error.="<div class='alert'><p>No se puede actualizar el registro n&uacute;mero ($id)</p></div>";
			
		if($status!='C')
			$error.="<div class='alert'><p>No se puede Realizar la operaci&oacute;n</p></div>";
			
		if($monto > $saldo)
			$error.="<div class='alert'><p>El monto del anticipo ($monto) es mayor que el saldo ($saldo) del banco ($codbanc)</p></div>";
		
		      
		if(empty($error)){
			$saldo  +=$monto;
			$saldo;
			$do->set('status','P');
			$do2->set('saldo',$saldo);
			$do->save();
			$do2->save();
			redirect($this->url."dataedit/show/$id");
		}else{
			$data['content'] = $error.anchor($this->url."dataedit/show/$id",'Regresar');
			$data['title']   = " $this->tits ";
			$data["head"]    = $this->rapyd->get_head().script('jquery.js').script("plugins/jquery.numeric.pack.js");
			$this->load->view('view_ventanas', $data);
		}
	}
}
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
		/*function actualizar($id){
		$this->rapyd->load('dataobject');
		$do = new DataObject("movi");
		$do->rel_one_to_one('banc', 'banc', array('codbanc'=>'codbanc'));
		$do->load($id);
		$monto    = $do->get('monto');
		$codbanc  = $do->get('codbanc');
		$tipo     = $do->get('tipo');
		$status   = $do->get('status');
		$saldo    = $do->get_rel('banc','saldo' ,0);
		$activo   = $do->get_rel('banc','activo',0);
		$error='';
		
		if($activo!='S')
			$error.="<div class='alert'><p>El Banco ($codbanc) nesta inactivo</p></div>";
		
		if($tipo!='A')
			$error.="<div class='alert'><p>No se puede actualizar el registro n&uacute;mero ($id)</p></div>";
			
		if($status!='P')
			$error.="<div class='alert'><p>No se puede Realizar la operaci&oacute;n</p></div>";
			
		if($monto > $saldo)
			$error.="<div class='alert'><p>El monto del anticipo ($monto) es mayor que el saldo ($saldo) del banco ($codbanc)</p></div>";
		
		
		if(empty($error)){
			$saldo  -=$monto;
			echo $saldo;
			//echo $do->set('status','C');
			echo $do->set_rel('banc','saldo',$saldo);
			$do->save();
			exit;
			redirect($this->url."dataedit/show/$id");
		}else{
			$data['content'] = $error.anchor($this->url."dataedit/show/$id",'Regresar');
			$data['title']   = " $this->tits ";
			$data["head"]    = $this->rapyd->get_head().script('jquery.js').script("plugins/jquery.numeric.pack.js");
			$this->load->view('view_ventanas', $data);
		}
		
	}*/
	
