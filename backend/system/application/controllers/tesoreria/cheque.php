<?php
class cheque extends Controller {
	var $titp='Cheques Anulados';
	var $tits='Cheque Anulado';
	var $url ='tesoreria/cheque/';

	function cheque(){
		parent::Controller();
		$this->load->library("rapyd");
	}

	function index(){
		redirect($this->url."filteredgrid");
	}

	function filteredgrid(){
		$this->datasis->modulo_id(138,1);

		$mSPRV=array(
				'tabla'   =>'sprv',
				'columnas'=>array(
				'proveed' =>'C&oacute;odigo',
				'nombre'=>'Nombre',
				'contacto'=>'Contacto'),
				'filtro'  =>array('proveed'=>'C&oacute;digo','nombre'=>'Nombre'),
				'retornar'=>array('proveed'=>'cod_prov' ),
				'titulo'  =>'Buscar Beneficiario');

		$this->rapyd->load("datafilter","datagrid");

		$bSPRV=$this->datasis->p_modbus($mSPRV,"proveed");

		$mBANC=array(
				'tabla'   =>'banc',
				'columnas'=>array(
					'codbanc' =>'C&oacute;odigo',
					'banco'=>'Banco',
					'numcuent'=>'Cuenta',
					'saldo'=>'Saldo'),
				'filtro'  =>array(
					'codbanc' =>'C&oacute;odigo',
					'banco'=>'Banco',
					'numcuent'=>'Cuenta',
					'saldo'=>'Saldo'),
				'retornar'=>array(
					'codbanc'=>'codbanc',
					 ),
				'where'=>'activo = "S"',
				'titulo'  =>'Buscar Bancos');

		$bBANC=$this->datasis->p_modbus($mBANC,"banc");


		$filter = new DataFilter("");
//		$filter->db->select(array("fecha","a.tipo tipo","a.cod_prov cod_prov","a.codbanc codbanc1","cheque","a.id id","a.monto","a.observa observa","a.benefi benefi"));
		$filter->db->from("mbanc");
		//$filter->db->join("banc b" ,"a.codbanc=b.codbanc","left");
		//$filter->db->join("sprv c" ,"a.cod_prov=c.proveed","left");
		$filter->db->where("(status = 'A' OR status='AN' OR status='A2' OR status='NC'  OR status='AN') ");
		$filter->db->_escape_char='';
		$filter->db->_protect_identifiers=false;

		$filter->id = new inputField("Ref.", 'id');

		$filter->fecha = new dateonlyField("Fecha", "fecha");
		$filter->fecha->dbformat = "Y-m-d";
		$filter->fecha->size=12;

		$filter->cod_prov = new inputField("Beneficiario", 'cod_prov');
//		$filter->cod_prov->db_name = "a.cod_prov";
		$filter->cod_prov->size = 6;
		$filter->cod_prov->append($bSPRV);
		$filter->cod_prov->rule = "required";

		$filter->codbanc =  new inputField("Banco", 'codbanc');
//		$filter->codbanc->db_name = "a.codbanc";
		$filter->codbanc-> size     = 5;
		$filter->codbanc-> append($bBANC);

		$filter->cheque = new inputField("Cheque", "cheque");
//		$filter->cheque->size   = 60;
//		$filter->cheque->db_name = "a.cheque";

		$filter->buttons("reset","search");
		$filter->build();

		$uri = anchor($this->url.'dataedit/show/<#id#>','<str_pad><#id#>|8|0|'.STR_PAD_LEFT.'</str_pad>');

		$grid = new DataGrid("");
		$grid->db->_escape_char='';
		$grid->db->_protect_identifiers=false;
		$grid->order_by("numero","asc");
		$grid->per_page = 20;
		$grid->use_function('substr','str_pad');

		$grid->column_orderby("N&uacute;mero",$uri,"id");
		$grid->column_orderby("Fecha"        ,"<dbdate_to_human><#fecha#></dbdate_to_human>"  ,"fecha"   ,"align='center'    ");
//		$grid->column_orderby("Banco"        ,"nombanco"                                      ,"nombanco","align='left'NOWRAP");
		$grid->column_orderby("Cheque"       ,"cheque"                                        ,"cheque"  );
		$grid->column_orderby("Monto"        ,"<number_format><#monto#>|2|,|.</number_format>","monto"   ,"align='right'     ");
//		$grid->column_orderby("Beneficiario" ,"proveed1"                                      ,"proveed1"  ,"align='left'NOWRAP");
		$grid->column_orderby("Observasion"  ,"observa"                                       ,"observa" ,"align='left'NOWRAP");
		//$grid->column("Tipo"             ,"tipo"                                        ,"align='center'");
		//$grid->column("Estado"           ,"status"                                        ,"align='center'");

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
		$this->rapyd->load('dataedit');

		$mBANC=array(
				'tabla'   =>'banc',
				'columnas'=>array(
					'codbanc' =>'C&oacute;odigo',
					'banco'=>'Banco',
					'numcuent'=>'Cuenta',
					'saldo'=>'Saldo'),
				'filtro'  =>array(
					'codbanc' =>'C&oacute;odigo',
					'banco'=>'Banco',
					'numcuent'=>'Cuenta',
					'saldo'=>'Saldo'),
				'retornar'=>array(
					'codbanc'=>'codbanc',
					'banco'=>'nombreb'
					 ),
				'where'=>'activo = "S"',
				'titulo'  =>'Buscar Bancos');

		$bBANC=$this->datasis->p_modbus($mBANC,"banc");

		$mSPRV=array(
				'tabla'   =>'sprv',
				'columnas'=>array(
				'proveed' =>'C&oacute;odigo',
				'nombre'=>'Nombre',
				'contacto'=>'Contacto'),
				'filtro'  =>array('proveed'=>'C&oacute;digo','nombre'=>'Nombre'),
				'retornar'=>array('proveed'=>'cod_prov','nombre'=>'nombre' ),
				'titulo'  =>'Buscar Beneficiario');

		$this->rapyd->load("datafilter","datagrid");

		$bSPRV=$this->datasis->p_modbus($mSPRV,"proveed");

		$script='
			$(".inputnum").numeric(".");
		';

		$do = new DataObject("mbanc");
		$do->pointer('sprv' ,'sprv.proveed=mbanc.cod_prov','sprv.nombre as nombre','LEFT');
		$do->pointer('banc' ,'banc.codbanc=mbanc.codbanc','banc.banco as nombreb','LEFT');

		$edit = new DataEdit($this->tits, $do);

		$edit->back_url = site_url($this->url."filteredgrid");

		$edit->script($script,"create");
		$edit->script($script,"modify");

		$edit->pre_process('insert'  ,'_valida');
		$edit->post_process('insert','_post_insert');
		$edit->post_process('update','_post_update');
		$edit->post_process('delete','_post_delete');

		$edit->id = new inputField("id", 'id');
		$edit->id->size      = 50;
		$edit->id->mode      = "autohide";


		$edit->cod_prov = new inputField("Beneficiario", 'cod_prov');
		$edit->cod_prov->size      = 5;
		$edit->cod_prov->append($bSPRV);
		$edit->cod_prov->when  = array('show');

		$edit->nombre = new inputField("Nombre", 'nombre');
		$edit->nombre->size      = 50;
		$edit->nombre->in        = "cod_prov";

		$edit->cheque = new inputField("Cheque Nro.", 'cheque');
		$edit->cheque->size      = 15;
		$edit->cheque->maxlength = 40;
		$edit->cheque->rule      = "required";//|callback_chexiste_cheque

		$edit->codbanc =  new inputField("Banco", 'codbanc');
		$edit->codbanc-> size     = 5;
		$edit->codbanc-> rule     = "required";
		$edit->codbanc-> append($bBANC);

		$edit->nombreb = new inputField("Nombre", 'nombreb');
		$edit->nombreb->size      = 50;
		$edit->nombreb->in        = "codbanc";

		$edit->fecha = new  dateonlyField("Fecha",  "fecha");
		$edit->fecha->rule ="required";
		//$edit->fecha->mode  = "autohide";

		$edit->observa = new textAreaField("Observaci&oacute;nes", 'observa');
		//$edit->observa->mode = "autohide";
		$edit->observa->rows  = 4;
		$edit->observa->cols = 70;

		$edit->monto = new inputField("Monto", 'monto');
		//$edit->monto ->mode ="autohide";
		$edit->monto ->css_class ="inputnum";
		$edit->monto->size = 15;

		$edit->buttons("add","modify","save","delete","undo", "back");
		$edit->build();

		$data['content'] = $edit->output;
		$data['title']   = "$this->tits";
		$data["head"]    = script("jquery.pack.js").script("plugins/jquery.numeric.pack.js").script("plugins/jquery.floatnumber.js").$this->rapyd->get_head();
		$this->load->view('view_ventanas', $data);
	}

	function _valida($do){
		$error.="";

		$tipo_doc = $do->get('tipo_doc');
		$cheque   = $do->get('cheque'  );
		$id       = $do->get('id'      );

		$this->chexiste_cheque($cheque,$tipo_doc,$id,$e);
		$error.=$e;
		if(empty($error)){
			$do->set("tipo_doc","CH");
			$do->set("tipo"   ,"AN");
			$do->set("status" ,"AN");
			$do->set("anulado","S");
		}else{
			$do->error_message_ar['pre_ins']=$error;
			$do->error_message_ar['pre_upd']=$error;
			return false;
		}

	}

	function _post_insert($do){
		$cheque     = $do->get('cheque');
		$id         = $do->get('id');
		logusu('mbanc',"Creo cheque anulado Nro $cheque movimento $id");
		
	}
	function _post_update($do){
		$cheque     = $do->get('cheque');
		$id         = $do->get('id');
		logusu('mbanc',"Modifico cheque anulado Nro $cheque movimento $id");
		
	}
	function _post_delete($do){
		$cheque     = $do->get('cheque');
		$id         = $do->get('id');
		logusu('mbanc',"Elimino cheque anulado Nro $cheque movimento $id");
	}

	//function chexiste_cheque($cheque){
	//	$tipo_doc = $this->db->escape($this->input->post('tipo_doc'));
	//	$cheque   = $this->db->escape($cheque);
	//	$cana=$this->datasis->dameval("SELECT id FROM mbanc WHERE cheque=$cheque AND tipo_doc=$tipo_doc");
	//	if($cana>0){
	//		$this->validation->set_message('chexiste_cheque',"El Cheque ya Existe para el desembolso $cana");
	//		return false;
	//	}
	//}

	function chexiste_cheque($cheque,$tipo_doc,$id,&$error){
		$cheque     = $this->db->escape($cheque       );
		$tipo_doc   = $this->db->escape($tipo_doc     );

		if($id>0)$query="SELECT id FROM mbanc WHERE cheque=$cheque AND tipo_doc=$tipo_doc AND id<>$id";
		else $query="SELECT id FROM mbanc WHERE cheque=$cheque AND tipo_doc=$tipo_doc";

		$cana=$this->datasis->dameval($query);
		if($cana>0)
			$error="El Cheque ya Existe para el desembolso $cana";
	}
}
