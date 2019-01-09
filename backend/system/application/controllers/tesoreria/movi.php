<?php
class movi extends Controller {
	 
	var $titp='Anticipos de Gastos';
	var $tits='Anticipo de Gasto';
	var $url ='tesoreria/movi/';

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
		
		$filter = new DataFilter("Filtro de $this->titp","mbanc");
		
		//$filter->tipo = new dropdownField("Orden de ", "tipo");
		//$filter->tipo->option("","");
		//$filter->tipo->option("A","Anticipo");
		//$filter->tipo->option("R","Reintegro");
		//$filter->tipo->style="width:100px;";

		$filter->fecha = new dateonlyField("Fecha", "fecha");
		$filter->fecha->size=12;
		    
		$filter->uejecutora = new inputField("Unidad Ejecutora", "uejecutora");
		$filter->uejecutora->size=12;    
		
		//$filter->cod_prov = new inputField("Beneficiario", 'cod_prov');
		//$filter->cod_prov->size = 6;
		//$filter->cod_prov->append($bSPRV);
		//$filter->cod_prov->rule = "required";
		
		$filter->benefi = new inputField("Beneficiario", "benefi");
		$filter->benefi->size=60;
		
		//$filter->status = new dropdownField("Estado","status");
		//$filter->status->option("","");
		//$filter->status->option("A","Procesado");
		//$filter->status->option("B","Sin Procesar");
		//$filter->status->style="width:150px";
		
		$filter->db->where("tipo","C");
		
		$filter->buttons("reset","search");
		$filter->build();

		$uri = anchor($this->url.'dataedit/show/<#id#>','<str_pad><#id#>|8|0|'.STR_PAD_LEFT.'</str_pad>');

		$grid = new DataGrid("Lista de ".$this->titp);
		$grid->order_by("numero","desc");
		$grid->per_page = 20;
		$grid->use_function('substr','str_pad');
		
		$grid->column("N&uacute;mero"    ,$uri);
		//$grid->column("Tipo"             ,"tipo"                                        ,"align='center'");
		$grid->column("Fecha"            ,"<dbdate_to_human><#fecha#></dbdate_to_human>","align='center'");
		$grid->column("Unidad Ejecutora" ,"uejecutora");
		//$grid->column("Beneficiario"        ,"cod_prov");
		//$grid->column("Beneficiario"     ,"beneficiario");
		$grid->column("Monto"            ,"<number_format><#monto#>|2|,|.</number_format>","align='right'");
		//$grid->column("Demostrado"       ,"<number_format><#saldo#>|2|,|.</number_format>","align='right'");
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
					'codbanc'=>'codbanc' ),//'banco'=>'nombreb'
				'where'=>'activo = "S"',
				'titulo'  =>'Buscar Bancos');
			
		$bBANC=$this->datasis->p_modbus($mBANC,"banc");
		
		//$mbanc = new DataObject("ocompra");
		$script='
			$(".inputnum").numeric(".");
		';
		
		$edit = new DataEdit($this->tits, "mbanc");
		
		$edit->back_url = site_url($this->url."filteredgrid");
		
		$edit->script($script,"create");
		$edit->script($script,"modify");
		
		$edit->post_process('insert'  ,'_actualiza');
		
		//$edit->id  = new inputField("N&uacute;mero", "id");
		//$edit->id->mode="autohide";
		//$edit->id->when=array('show');
		
		//$edit->tipo = new dropdownField("Orden de", "tipo");
		//$edit->tipo->option("A","Anticipo");
		//$edit->tipo->option("R","Reintegro"); 
		//$edit->tipo->style="width:100px;";
		
		$edit->tipo = new inputField("","tipo");
		$edit->tipo-> insertValue = "C";
		$edit->tipo->mode         = "autohide";
		$edit->tipo->when=array('');
		
		//$edit->status = new inputField("","tipo");
		//$edit->status-> insertValue = "C1";
		//$edit->status->mode         = "autohide";
		//$edit->status->when=array('');
		
		//$edit->status = new inputField("","status");
		//$edit->status-> insertValue = "P";
		//$edit->status->mode         = "autohide";
		//$edit->status->when=array('');

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
		
		$edit->benefi = new inputField("Beneficiario", 'benefi');
		$edit->benefi->size       = 50;
		$edit->benefi->maxlength  = 50;
		$edit->benefi->rule       = 'required';
		
		$edit->observa = new textAreaField("Observaciones", 'observa');
		$edit->observa->cols       = 106;
		$edit->observa->rows       = 3;
		$edit->observa->rule       = 'required';
		
		$edit->codbanc = new inputField("Banco", 'codbanc');
		$edit->codbanc->size     = 6;		
		$edit->codbanc->rule     = "required";
		$edit->codbanc->append($bBANC);
		$edit->codbanc->readonly=true;
		
		//$edit->nombreb = new inputField("Nombre", 'nombreb');
		//$edit->nombreb->size     = 50;
		//$edit->nombreb->readonly = true;
		//$edit->nombreb->in       ="codbanc";

		$edit->monto = new inputField("Monto", 'monto');
		$edit->monto ->size      = 20;
		$edit->monto ->css_class = 'inputnum';
		$edit->monto->rule       = 'required|callback_positivo';

		$status=$edit->_dataobject->get("status");		
		if(empty($status)){
			//$action = "javascript:window.location='" .site_url($this->url.'actualizar/'.$edit->rapyd->uri->get_edited_id()). "'";
			//$edit->button_status("btn_status",'Actualizar',$action,"TR","show");
			$edit->buttons("save");
		}
		//elseif($status=='C'){
		//	//$action = "javascript:window.location='" .site_url($this->url.'reversar/'.$edit->rapyd->uri->get_edited_id()). "'";
		//	$edit->button_status("btn_rever",'Reversar',$action,"TR","show");        
		//}else{
		//	$edit->buttons("save");
		//}
		
		$edit->buttons("undo", "back");
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
	
	function _actualiza($mbanc){
		$this->rapyd->load('dataobject');
		//$mbanc  = new DataObject("mbanc");
		//$mbanc->load($id);
		
		$banc = new DataObject("banc");
				
		$monto    = $mbanc->get('monto');
		$codbanc  = $mbanc->get('codbanc');
		$tipo     = $mbanc->get('tipo');
		$status   = $mbanc->get('status');
		$id       = $mbanc->get('id');

		$banc->load($codbanc);
		$saldo    = $banc->get('saldo' );
		$activo   = $banc->get('activo');
		$error='';
		
		if($activo!='S')
			$error.="<div class='alert'><p>El Banco ($codbanc) esta inactivo</p></div>";
		
		if($tipo!='C')
			$error.="<div class='alert'><p>No se puede actualizar el registro n&uacute;mero ($id)</p></div>";
			
		//if($status!='C1')
		//	$error.="<div class='alert'><p>No se puede Realizar la operaci&oacute;n</p></div>";
			
		if($monto > $saldo)
			$error.="<div class='alert'><p>El monto del anticipo ($monto) es mayor que el saldo ($saldo) del banco ($codbanc)</p></div>";
		
		      
		if(empty($error)){
			$saldo  -=$monto;
			$mbanc->set('status','C2');
			//$mbanc->set('abonado' ,$monto);
			$mbanc->save();
			
			$banc->set('saldo'  ,$saldo);
			$banc->save();
			redirect($this->url."dataedit/show/$id");
		}else{
			$data['content'] = $error.anchor($this->url."dataedit/show/$id",'Regresar');
			$data['title']   = " $this->tits ";
			$data["head"]    = $this->rapyd->get_head().script('jquery.js').script("plugins/jquery.numeric.pack.js");
			$this->load->view('view_ventanas', $data);
		}
		
	}
	
/*	function reversar($id){
		$this->rapyd->load('dataobject');
		$mbanc  = new DataObject("movi");
		$banc = new DataObject("banc");

		$mbanc->load($id);
		$monto    = $mbanc->get('monto');
		$codbanc  = $mbanc->get('codbanc');
		$tipo     = $mbanc->get('tipo');
		$status   = $mbanc->get('status');
		
		
		$banc->load($codbanc);
		$saldo    = $banc->get('saldo' );
		$activo   = $banc->get('activo');
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
			$mbanc->set('status','P');
			$banc->set('saldo',$saldo);
			$mbanc->save();
			$banc->save();
			redirect($this->url."dataedit/show/$id");
		}else{
			$data['content'] = $error.anchor($this->url."dataedit/show/$id",'Regresar');
			$data['title']   = " $this->tits ";
			$data["head"]    = $this->rapyd->get_head().script('jquery.js').script("plugins/jquery.numeric.pack.js");
			$this->load->view('view_ventanas', $data);
		}
	}*/
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
	
