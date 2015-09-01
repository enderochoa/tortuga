<?php
class pmov extends Controller {

	var $titp='Fondos en Anticipo';
	var $tits='Fondo en Anticipo';
	var $url ='tesoreria/pmov/';

	function pmov(){
		parent::Controller();
		$this->load->library("rapyd");
	}

	function index(){
		redirect($this->url."filteredgrid");
	}

	function filteredgrid(){
		//$this->datasis->modulo_id(101,1);
		$this->rapyd->load("datafilter","datagrid");

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

		$filter = new DataFilter("Filtro de $this->titp","pmov");

		$filter->numero = new inputField("Numero", "numero");
		$filter->numero->size=15;

		$filter->orden = new inputField("Orden de Pago", "orden");
		$filter->orden->size=15;

		$filter->fecha1 = new dateonlyField("Fecha Cheque", "fecha1");
		$filter->fecha1->size=12;

		$filter->fecha2 = new dateonlyField("Fecha Deposito", "fecha2");
		$filter->fecha2->size=12;

		$filter->buttons("reset","search");
		$filter->build();

		$uri = anchor($this->url.'dataedit/show/<#numero#>','<str_pad><#numero#>|8|0|'.STR_PAD_LEFT.'</str_pad>');

		$grid = new DataGrid("Lista de ".$this->titp);
		$grid->order_by("numero","desc");
		$grid->per_page = 20;
		$grid->use_function('substr','str_pad');

		$grid->column("N&uacute;mero"    ,$uri);
		$grid->column("Orden de Pago"    ,"orden"                                          ,"align='center'");
		$grid->column("Banco Emisor"     ,"bancemi"                                                         );
		$grid->column("Cheque"           ,"cheque"                                                          );
		$grid->column("Fecha cheque"     ,"<dbdate_to_human><#fecha#></dbdate_to_human>"  ,"align='center'");
		$grid->column("Monto"            ,"<number_format><#monto#>|2|,|.</number_format>" ,"align='right'" );
		$grid->column("Banco deposito"   ,"codbanc"                                                          );
		$grid->column("Deposito Nº"      ,"deposito"                                                        );
		

		$grid->add($this->url."dataedit/create");
		$grid->build();

		$data['content'] = $filter->output.$grid->output;
		$data['title']   = " $this->titp ";
		$data["head"]    = $this->rapyd->get_head();
		$this->load->view('view_ventanas', $data);
	}

	function dataedit(){
		//$this->datasis->modulo_id(101,1);
		$this->rapyd->load('dataedit','dataobject');

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
				'where'=>'activo = "S"',
				'titulo'  =>'Buscar Bancos');

		$bBANC=$this->datasis->p_modbus($mBANC,"banc");
		
		$mSPRV=array(
				'tabla'   =>'sprv',
				'columnas'=>array(
				'proveed' =>'C&oacute;odigo',
				'nombre'=>'Nombre',
		    'rif'=>'Rif',
				'contacto'=>'Contacto'),
				'filtro'  =>array('proveed'=>'C&oacute;digo','nombre'=>'Nombre','rif'=>'Rif'),
				'retornar'=>array('proveed'=>'cod_prov','nombre'=>'nombre'),
				'titulo'  =>'Buscar Proveedor');

		$bSPRV=$this->datasis->p_modbus($mSPRV,"proveed");

		$script='
			$(".inputnum").numeric(".");
		';

		$do = new DataObject("pmov");
		//$do->pointer('sprv' ,'sprv.proveed = pmov.cod_prov',' sprv.nombre as nombre ',' LEFT ');
		
		$edit = new DataEdit($this->tits, $do);

		$edit->back_url = site_url($this->url."filteredgrid");

		$edit->script($script,"create");
		$edit->script($script,"modify");

		//$edit->pre_process('insert'  ,'_valida');
		//$edit->pre_process('update'  ,'_valida');

		$edit->numero        = new inputField("N&uacute;mero", "numero");
		$edit->numero->mode  = "autohide";
		$edit->numero->when  = array('show');

		$edit->orden = new inputField("Orden de Pago","orden");
		$edit->orden->size      = 10;
		$edit->orden->maxlength = 12;
		//$edit->orden->mode    = "autohide";
		//$edit->orden->when    = array('show');
		
		$edit->cod_prov = new inputField("Proveedor", 'cod_prov');
		$edit->cod_prov->size     = 6;
		$edit->cod_prov->rule     = "required";
		$edit->cod_prov->append($bSPRV);
		
		$edit->nombre = new inputField("Nombre", 'nombre');
		$edit->nombre->db_name   = ' ';
		$edit->nombre->size      = 50;
		$edit->nombre->readonly  = true;
		$edit->nombre->pointer   = true;
		$edit->nombre->in        = "cod_prov";
		
		$edit->observa = new textAreaField("Observaciones", 'observa');
		$edit->observa->cols = 60;
		$edit->observa->rows = 3;		
		
		$edit->banc_doc = new dropdownField("Banco Emisor", "banc_doc");
		$edit->banc_doc->option("","Seccionar");
		$edit->banc_doc->options("SELECT cod_banc, CONCAT_WS(' ',cod_banc,nomb_banc) FROM tban ORDER BY cod_banc");
//		$edit->banc_doc->group = "Cheque";

		$edit->tipo_doc = new dropdownField("Tipo Documento","tipo_doc");
    $edit->tipo_doc->option("CH","Cheque"         );
    $edit->tipo_doc->option("NC","Nota de Credito");
    //$edit->tipo_doc->option("ND","Nota de Debito" );
    $edit->tipo_doc->option("DP","Deposito"         );
    //$edit->tipo_doc->option("CH","Cheque"         );
    $edit->tipo_doc->style="width:200px";
    
    $edit->numero_doc = new inputField("N&uacute;mero Documento", 'numero_doc');
		$edit->numero_doc->size      = 30;
		$edit->numero_doc->maxlength = 30;
		$edit->numero_doc->rule        = "required";
		//$edit->numero_doc->group     = "Deposito";
		
		$edit->fecha_doc = new  dateonlyField("Fecha Documento",  "fecha_doc");
		$edit->fecha_doc->insertValue = date('Y-m-d');
		$edit->fecha_doc->size        =12;
		//$edit->fecha_doc->group       = "Cheque/Deposito/Nota de Cr&eacute;dito";
		$edit->fecha_doc->rule        = "required";
		
		//$edit->cheque = new inputField("Cheque Nº", 'cheque');
		//$edit->cheque->size      = 30;
		//$edit->cheque->maxlength = 30;
		//$edit->cheque->group     = "Cheque";

		$edit->monto = new inputField("Monto", 'monto');
		$edit->monto ->size      = 20;
		$edit->monto ->css_class = 'inputnum';
		$edit->monto->rule       = 'required|callback_positivo';
		//$edit->monto->group = "";
		
		$edit->fecha = new  dateonlyField("Fecha",  "fecha");
		$edit->fecha->insertValue = date('Y-m-d');
		$edit->fecha->size        =12;
		//$edit->fecha->group       = "Cheque/Deposito/Nota de Cr&eacute;dito";
		$edit->fecha->rule        = "required";

		$edit->codbanc = new inputField("Banco*", 'codbanc');
		$edit->codbanc->size     = 6;
		//$edit->codbanc->rule     = "required";
		$edit->codbanc->append($bBANC);
		$edit->codbanc->readonly=true;
		//$edit->codbanc->group = "Deposito";

		$edit->nombreb = new inputField("", 'nombreb');
		$edit->nombreb->db_name     = 'nombreb';
		$edit->nombreb->size        = 30;
		//$edit->nombreb->group       = "Deposito";
		$edit->nombreb->in          = "codbanc";
		
		$status=$edit->_dataobject->get("status");
		if($status=='P'){
			$action = "javascript:window.location='" .site_url($this->url.'actualizar/'.$edit->rapyd->uri->get_edited_id()). "'";
			$edit->button_status("btn_status",'Actualizar',$action,"TR","show");
			$edit->buttons("modify","delete","save");
		}elseif($status=='C'){
			$action = "javascript:window.location='" .site_url($this->url.'reversar/'.$edit->rapyd->uri->get_edited_id()). "'";
			$edit->button_status("btn_rever",'Reversar',$action,"TR","show");
		}else{
			$edit->buttons("save");
		}

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

	function actualizar($id){
		$this->rapyd->load('dataobject');
		
		$do  = new DataObject("pmov");
		$do->load($id);
		$codbanc     = $do->get('codbanc'     );
		$status      = $do->get('status'      );		
		$monto       = $do->get('monto'       );
		$tipo_doc    = $do->get('tipo_doc'    );
		$fecha_doc   = $do->get('fecha_doc'   );
		$numero_doc  = $do->get('numero_doc'  );
		$observa     = $do->get('observa'     );
		$fecha       = $do->get('fecha'       );
		$nombreb     = $do->get('nombreb'     );

		$do2 = new DataObject("banc");
		$do2->load($codbanc);
		
		$saldo    = $do2->get('saldo' );
		$activo   = $do2->get('activo');
		$error='';

		if($activo!='S')
			$error.="<div class='alert'><p>El Banco ($codbanc) esta inactivo</p></div>";
			
		if($status!='P')
			$error.="<div class='alert'><p>No se puede Realizar la operaci&oacute;n</p></div>";

		//if($monto > $saldo)
		//	$error.="<div class='alert'><p>El monto del anticipo ($monto) es mayor que el saldo ($saldo) del banco ($codbanc)</p></div>";
			
		//if(empty($deposito))
		//	$error.="<div class='alert'><p>El campo deposito no puede estar en blanco</p></div>";

		if(empty($error)){
			$saldo  +=$monto;
			$do->set('status','C');			
			$do->save();
			
			$do2->set('saldo',$saldo);
			$do2->save();
			
			$mbanc  = new DataObject("mbanc");
			$mbanc->load_where('numero',$id);
			$mbanc->set('monto'   ,$monto);
			$mbanc->set('numero'  ,$id);
			$mbanc->set('tipo'    ,'A');
			$mbanc->set('status'  ,'A2');
			$mbanc->set('fecha'   ,$do->get('fecha'));
			$mbanc->set('codbanc' ,$do->get('codbanc'));
			$mbanc->set('tipo_doc',$do->get('tipo_doc'));
			$mbanc->set('cheque'  ,$do->get('numero_doc' ));
			$mbanc->set('observa' ,$do->get('observa'));
			$mbanc->set('cod_prov',$do->get('cod_prov'));
			$mbanc->save();
			
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
		$do  = new DataObject("pmov");
		$do->load($id);
		$codbanc  = $do->get('codbanc');
		$status   = $do->get('status');
		$deposito = $do->get('deposito');		
		$monto    = $do->get('monto');

		$do2 = new DataObject("banc");
		$do2->load($codbanc);
		
		$saldo    = $do2->get('saldo' );
		$activo   = $do2->get('activo');
		$error='';

		if($activo!='S')
			$error.="<div class='alert'><p>El Banco ($codbanc) esta inactivo</p></div>";
			
		if($status!='C')
			$error.="<div class='alert'><p>No se puede Realizar la operaci&oacute;n</p></div>";

		if($monto > $saldo)
			$error.="<div class='alert'><p>El monto del anticipo ($monto) es mayor que el saldo ($saldo) del banco ($codbanc)</p></div>";
			
		//if(empty($deposito))
		//	$error.="<div class='alert'><p>El campo deposito no puede estar en blanco</p></div>";

		if(empty($error)){
			$saldo  -=$monto;
			$do->set('status','P');			
			$do->save();
			
			$do2->set('saldo',$saldo);
			$do2->save();
			
			$mbanc  = new DataObject("mbanc");
			$mbanc->load_where('numero',$id);
			$mbanc->set('monto'   ,$monto);
			$mbanc->set('numero'  ,$id);
			$mbanc->set('tipo'    ,'A');
			$mbanc->set('status'  ,'A1');
			$mbanc->set('fecha'   ,$do->get('fecha2'));
			$mbanc->set('codbanc' ,$do->get('codbanc'));
			$mbanc->save();
			
			redirect($this->url."dataedit/show/$id");
		}else{
			$data['content'] = $error.anchor($this->url."dataedit/show/$id",'Regresar');
			$data['title']   = " $this->tits ";
			$data["head"]    = $this->rapyd->get_head().script('jquery.js').script("plugins/jquery.numeric.pack.js");
			$this->load->view('view_ventanas', $data);
		}
	}
}