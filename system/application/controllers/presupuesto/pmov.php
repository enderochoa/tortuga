<?php
class pmov extends Controller {

	var $titp='Fondos en Anticipo';
	var $tits='Fondo en Anticipo';
	var $url ='presupuesto/pmov/';

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
		$grid->column("Orden de Pago"    ,"ooder"                                          ,"align='center'");
		$grid->column("Banco Emisor"     ,"bancemi"                                                         );
		$grid->column("Cheque"           ,"cheque"                                                          );
		$grid->column("Fecha cheque"     ,"<dbdate_to_human><#fecha1#></dbdate_to_human>"  ,"align='center'");
		$grid->column("Monto"            ,"<number_format><#monto#>|2|,|.</number_format>" ,"align='right'" );
		$grid->column("Banco deposito"   ,"codban"                                                          );
		$grid->column("Deposito Nº"      ,"deposito"                                                        );
		$grid->column("Fecha Deposito"   ,"<dbdate_to_human><#fecha2#></dbdate_to_human>"  ,"align='center'");

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

		$script='
			$(".inputnum").numeric(".");
		';

		$edit = new DataEdit($this->tits, "pmov");

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
		
		$edit->bancemi = new dropdownField("Banco Emisor", "bancemi");
		$edit->bancemi->option("","Seccionar");
		$edit->bancemi->options("SELECT cod_banc, CONCAT_WS(' ',cod_banc,nomb_banc) FROM tban ORDER BY cod_banc");
		$edit->bancemi->group = "Cheque";
		
		$edit->cheque = new inputField("Cheque Nº", 'cheque');
		$edit->cheque->size      = 30;
		$edit->cheque->maxlength = 30;
		$edit->cheque->group     = "Cheque";
           
		$edit->fecha1 = new  dateonlyField("Fecha Cheque",  "fecha1");
		$edit->fecha1->insertValue = date('Y-m-d');
		$edit->fecha1->size        =12;
		$edit->fecha1->group       = "Cheque";
		//$edit->fecha1->rule        = "required";

		$edit->monto = new inputField("Monto", 'monto');
		$edit->monto ->size      = 20;
		$edit->monto ->css_class = 'inputnum';
		$edit->monto->rule       = 'required|callback_positivo';
		//$edit->monto->group = "Cheque";

		$edit->deposito = new inputField("Deposito Nº*", 'deposito');
		$edit->deposito->size      = 30;
		$edit->deposito->maxlength = 30;
		$edit->deposito->group     = "Deposito";

		$edit->fecha2 = new  dateonlyField("Fecha Deposito*",  "fecha2");
		$edit->fecha2->insertValue = date('Y-m-d');
		$edit->fecha2->size        =12;
		//$edit->fecha2->rule        = "required";
		$edit->fecha2->group       = "Deposito";

		$edit->codbanc = new inputField("Banco*", 'codbanc');
		$edit->codbanc->size     = 6;
		//$edit->codbanc->rule     = "required";
		$edit->codbanc->append($bBANC);
		$edit->codbanc->readonly=true;
		$edit->codbanc->group = "Deposito";

		$edit->nombreb = new inputField("", 'nombreb');
		$edit->nombreb->db_name     = 'nombreb';
		$edit->nombreb->size        = 30;
		$edit->nombreb->group       = "Deposito";
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
			
		if($status!='P')
			$error.="<div class='alert'><p>No se puede Realizar la operaci&oacute;n</p></div>";

		if($monto > $saldo)
			$error.="<div class='alert'><p>El monto del anticipo ($monto) es mayor que el saldo ($saldo) del banco ($codbanc)</p></div>";
			
		if(empty($deposito))
			$error.="<div class='alert'><p>El campo deposito no puede estar en blanco</p></div>";

		if(empty($error)){
			$saldo  +=$monto;
			$do->set('status','C');			
			$do->save();
			
			$do2->set('saldo',$saldo);
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
			
		if(empty($deposito))
			$error.="<div class='alert'><p>El campo deposito no puede estar en blanco</p></div>";

		if(empty($error)){
			$saldo  -=$monto;
			$do->set('status','P');			
			$do->save();
			
			$do2->set('saldo',$saldo);
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