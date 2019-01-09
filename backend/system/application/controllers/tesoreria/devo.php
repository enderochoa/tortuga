<?php
class devo extends Controller {
	 
	var $titp='Reintegros de Gastos';
	var $tits='Reintegro de Gasto';
	var $url ='tesoreria/devo/';

	function devo(){
		parent::Controller();
		$this->load->library("rapyd");
	}
	
	function index(){
		redirect($this->url."filteredgrid");
	}

	function filteredgrid(){
		//$this->datasis->modulo_id(101,1);
		$this->rapyd->load("datafilter2","datagrid");
		
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
		
		$filter->id = new inputField("C&oacute;digo", "id");
		$filter->id->size   = 12;
		$filter->id->clause = "likerigth";
		
		$filter->devo = new inputField("Anticipo", "devo");
		$filter->devo->size = 12;
		
		$filter->fecha = new dateonlyField("Fecha", "fecha");
		$filter->fecha->size=12;
		
		//$filter->status = new dropdownField("Estado","status");
		//$filter->status->option("","");
		//$filter->status->option("A","Procesado");
		//$filter->status->option("B","Sin Procesar");
		//$filter->status->style="width:150px";
		
		$filter->db->where("tipo","D");
		
		$filter->buttons("reset","search");
		$filter->build();

		$uri = anchor($this->url.'dataedit/show/<#id#>','<str_pad><#id#>|8|0|'.STR_PAD_LEFT.'</str_pad>');

		$grid = new DataGrid("Lista de ".$this->titp);
		$grid->order_by("numero","asc");
		$grid->per_page = 20;
		$grid->use_function('substr','str_pad');
		
		$grid->column("N&uacute;mero"    ,$uri);
		$grid->column("Anticipo"         ,"<str_pad><#devo#>|8|0|STR_PAD_LEFT</str_pad>" ,"align='center'");
		$grid->column("Fecha"            ,"<dbdate_to_human><#fecha#></dbdate_to_human>","align='center'");
		$grid->column("Monto"            ,"<number_format><#monto#>|2|,|.</number_format>","align='right'");

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

		$mODIRECT=array(
				'tabla'   =>'odirect',
				'columnas'=>array(
					'numero'        =>'C&oacute;odigo',
					'fecha'         =>'Fecha'         ,
					'beneficiario'  =>'Beneficiario'  ,
					'total'         =>'Monto')        ,
				'filtro'  =>array(
					'numero'        =>'C&oacute;odigo',
					'fecha'         =>'Fecha'         ,
					'benefi'        =>'Beneficiario'  ,
					'total'         =>'Monto'),
				'retornar'=>array(
					'numero'        =>'devo'  ),
				'where'   =>'status = "G3"',
				'titulo'  =>'Buscar Anticipos de Gastos');

		$bODIRECT=$this->datasis->p_modbus($mODIRECT,"odirect");
		
		$mBANC=array(
				'tabla'   =>'banc',
				'columnas'=>array(
					'codbanc' =>'C&oacute;odigo',
					'banco'=>'Banco',
					'saldo'=>'Saldo'),
				'filtro'  =>array(
					'codbanc' =>'C&oacute;odigo',
					'banco'=>'Banco',
					'saldo'=>'Saldo'),//39, 40
				'retornar'=>array(
					'codbanc'=>'codbanc','banco'=>'nombanc'
					 ),
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
		
		$edit->pre_process('insert'  ,'_actualiza');
			
		$edit->tipo = new inputField("","tipo");
		$edit->tipo-> insertValue = "D";
		$edit->tipo->mode         = "autohide";
		$edit->tipo->when=array('');
				
		$edit->devo = new inputField("Anticipo de Gasto", 'devo');
		$edit->devo->size     = 10;		
		$edit->devo->rule     = "required";
		$edit->devo->append($bODIRECT);
		$edit->devo->readonly=true;

		$edit->fecha = new  dateonlyField("Fecha",  "fecha");
		$edit->fecha->insertValue = date('Y-m-d');
		$edit->fecha->size =12;  
		$edit->fecha->rule = "required";
		
		$edit->codbanc =  new inputField("Banco", 'codbanc');
	  $edit->codbanc-> size     = 3;
	  $edit->codbanc-> rule     = "required";
	  $edit->codbanc-> append($bBANC);
    $edit->codbanc-> readonly=true;

    $edit->nombanc = new inputField("Nombre","nombanc");
    $edit->nombanc->size  =30;
    $edit->nombanc->readonly=true;
    $edit->nombanc->db_name =" ";
				
		$edit->observa = new textAreaField("Observaci&oacute;nes", 'observa');
		$edit->observa->cols       = 70;
		$edit->observa->rows       = 3;
		$edit->observa->rule       = 'required';	

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
		if ($valor <= 0){
			$this->validation->set_message('positivo',"El campo monto debe ser positivos");
			return FALSE;
		}
  	return TRUE;
	}
	
	function _actualiza($mbanc){
		$this->rapyd->load('dataobject');
		
		$banc     =  new DataObject("banc");
		
		$monto    = $mbanc->get('monto');
		$devo     = $mbanc->get('devo');
		$codbanc  = $mbanc->get('codbanc');
		//$fecha    = $mbanc->get('fecha');
		//$observa  = $mbanc->get('observa');
		
		$row      = $this->datasis->damerow("SELECT total,abonado FROM odirect WHERE numero=$devo");
		$abonado  = $row['abonado'];
		$total    = $row['total'];
//	$codbanc  = $row['codbanc'];

		$banc->load($codbanc);
		$saldo     = $banc->get('saldo' );
		$activo    = $banc->get('activo');
		$error='';
		
		if($activo!='S')
			$error.="<div class='alert'><p>El Banco ($codbanc) esta inactivo</p></div>";
		
		//if($tipo!='D')
		//	$error.="<div class='alert'><p>No se puede actualizar el registro n&uacute;mero ($id)</p></div>";
			
		//if($status!='D1')
		//	$error.="<div class='alert'><p>No se puede Realizar la operaci&oacute;n</p></div>";

		$a=$total-$abonado;
		if($monto > $a)
			$error.="<div class='alert'><p>El monto del reintegro ($monto) es mayor que el monto adeudado ($a) del Anticipo ($devo)</p></div>";

		if(empty($error)){
			$saldo   += $monto;
			$abonado += $monto;
			
			$this->db->simple_query("UPDATE odirect SET abonado=$abonado WHERE numero=$devo");
			
			$mbanc->set('status','D2');
			
			$banc->set('saldo'  ,$saldo);
			$banc->save();
		}else{
			$mbanc->error_message_ar['pre_ins']=$error;
			$mbanc->error_message_ar['pre_upd']=$error;
			return false;
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
	
