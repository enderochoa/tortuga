<?php
class Ocomrapid extends Controller {

	var $titp='Orden de Gasolina';
	var $tits='Orden de Gasolina';
	var $url ='presupuesto/ocomrapid/';

	function ocomrapid(){
		parent::Controller();
		$this->load->library("rapyd");
	}

	function index(){
		redirect($this->url."filteredgrid");
	}

	function filteredgrid(){
		$this->datasis->modulo_id(168,1);
		$this->rapyd->load("datafilter","datagrid");
		//$this->rapyd->uri->keep_persistence();

		$mSPRV=array(
				'tabla'   =>'sprv',
				'columnas'=>array(
				'proveed' =>'C&oacute;odigo',
				'nombre'=>'Nombre',
		    'rif'=>'Rif',
				'contacto'=>'Contacto'),
				'filtro'  =>array('proveed'=>'C&oacute;digo','nombre'=>'Nombre','rif'=>'Rif'),
				'retornar'=>array('proveed'=>'cod_prov'),//,'nombre'=>'nombrep' 
				'titulo'  =>'Buscar Beneficiario');
		

		$bSPRV=$this->datasis->p_modbus($mSPRV,"sprv");

		$filter = new DataFilter("");

		$filter->db->select("ocomrapid.*,sprv.nombre proveed");
		$filter->db->from("ocomrapid");
		$filter->db->join("sprv"    ,"ocomrapid.cod_prov=sprv.proveed");

		$filter->numero = new inputField("N&uacute;mero", "numero");
		$filter->numero->size  =10;
		
		$filter->placa = new inputField("PLaca", "placa");
		$filter->placa->size  =10;

		$filter->fecha = new dateonlyField("Fecha", "fecha");
		$filter->fecha->size=12;

		$filter->cod_prov = new inputField("Beneficiario", 'cod_prov');
		$filter->cod_prov->size = 6;
		$filter->cod_prov->append($bSPRV);

		$filter->status = new dropdownField("Estado","status");
		$filter->status->option("","");
		$filter->status->option("C","Cancelado");
		$filter->status->option("P","Pendiente");
		$filter->status->option("A","Anulado");
		$filter->status->style="width:150px";

		$filter->buttons("reset","search");

		$filter->build();
		$uri = anchor($this->url.'dataedit/show/<#numero#>','<str_pad><#numero#>|8|0|STR_PAD_LEFT</str_pad>');

		function sta($status){
			switch($status){
				case "C":return "Cancelado";break;
				case "P":return "Pendiente";break;
				case "A":return "Anulado";break;
			}
		}
		
		function cancela($status,$numero){
			if($status=='P'){
				return anchor('presupuesto/ocomrapid/cancelar/'.$numero,'Cancelar');
			}else
				return "";
		}

		$grid = new DataGrid("");
		$grid->order_by("numero","desc");
		$grid->per_page = 20;
		$grid->use_function('substr','str_pad','sta','cancela');

		$grid->column_orderby("N&uacute;mero"    ,$uri                                             ,"numero"     );
		$grid->column_orderby("Placa"            ,"placa"                                          ,"placa"      ,"align='center'NOWRAP");
		$grid->column_orderby("Fecha"            ,"<dbdate_to_human><#fecha#></dbdate_to_human>"   ,"fecha"      ,"align='center'NOWRAP");
		$grid->column_orderby("Beneficiario"     ,"proveed"                                        ,"proveed"    ,"align='center'NOWRAP");
		$grid->column_orderby("Solicitante"      ,"solicitante"                                    ,"solicitante","align='center'NOWRAP");
		$grid->column_orderby("Litros"           ,"<number_format><#litros#>|2|,|.</number_format>","litros"     ,"align='right'" );
		$grid->column_orderby("Monto"            ,"<number_format><#monto#>|2|,|.</number_format>" ,"monto"      ,"align='right'" );
		$grid->column_orderby("Estado"           ,"<sta><#status#></sta>"                          ,"status"     ,"align='center'NOWRAP");
		$grid->column_orderby("Acci&oacute;n"    ,"<cancela><#status#>|<#numero#></cancela>"       ,"numero"     ,"align='center'");

		$grid->add($this->url."dataedit/create");
		$grid->build();
		
		$data['filtro']  = $filter->output;
		$data['content'] = $grid->output;
		$data['script'] = script("jquery.js")."\n";
		$data['title']   = "Orden De Gasolina ";//" $this->titp";
		//$data['content'] = $filter->output.$grid->output;
		//$data['title']   = " $this->titp ";
		$data["head"]    = $this->rapyd->get_head();
		$this->load->view('view_ventanas', $data);
	}

	function dataedit(){
		//$this->datasis->modulo_id(101,1);
		$this->rapyd->load('dataedit','dataobject');
		
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

		$bSPRV=$this->datasis->p_modbus($mSPRV,"proveed");
		
		$pers=array(
		'tabla'   =>'pers',
		'columnas'=>array(
		'codigo'  =>'Codigo',
		'cedula'  =>'Cedula',
		'nombre'  =>'Nombre',
		'apellido'=>'Apellido'),
		'filtro'  =>array('codigo'=>'C&oacute;digo','cedula'=>'Cedula'),
		'retornar'=>array('nombre'=>'solicitante'),
		'titulo'  =>'Buscar Personal');
					  
		$bpers=$this->datasis->modbus($pers);

		$script='
			function btn_anulaf(i){
				if(!confirm("Esta Seguro que desea Anular la Orden de Pago Directo"))
					return false;
				else
					window.location="'.site_url($this->url.'anular').'/"+i
			}
		
			$(".inputnum").numeric(".");
		';

		$do = new DataObject("ocomrapid");
		$do->pointer('sprv' ,'sprv.proveed = ocomrapid.cod_prov','sprv.nombre as nombrep');

		$edit = new DataEdit($this->tits, $do);

		$edit->back_url = site_url($this->url."filteredgrid");

		$edit->script($script,"create");
		$edit->script($script,"modify");
		$edit->script($script,"show");

		$edit->pre_process('insert'  ,'_valida');
		$edit->pre_process('update'  ,'_valida');
		$edit->post_process('insert','_post_insert');
		$edit->post_process('update','_post_update');
		$edit->post_process('delete','_post_delete');

		$edit->numero        = new inputField("N&uacute;mero", "numero");
		$edit->numero->mode  = "autohide";
		$edit->numero->when  = array('show');
		
		$edit->fecha = new  dateonlyField("Fecha",  "fecha");
		$edit->fecha->insertValue = date('Y-m-d');
		$edit->fecha->size        =12;
		$edit->fecha->rule        = 'required';

		$edit->cod_prov = new inputField("Beneficiario", 'cod_prov');
		$edit->cod_prov->size     = 6;
		$edit->cod_prov->rule     = "required";
		$edit->cod_prov->append($bSPRV);
		//$edit->cod_prov->readonly=true;

		$edit->nombrep = new inputField("Nombre", 'nombrep');
		$edit->nombrep->size      = 50;
		$edit->nombrep->readonly  = true;
		$edit->nombrep->pointer   = true;
		$edit->nombrep->in        = "cod_prov";

    $edit->placa =  new inputField("Placa", 'placa');
	  $edit->placa-> size  = 20;
	  
	  $edit->solicitante =  new inputField("Solicitante", 'solicitante');
	  $edit->solicitante-> size  = 40;
	  //$edit->solicitante->rule     = "required";
	  $edit->solicitante->append($bpers);

		//$edit->litros = new inputField("Litros", 'litros');		
		//$edit->litros->size     = 8;
		//$edit->litros->css_class='inputnum';

		$edit->monto = new inputField("Monto", 'monto');		
		$edit->monto->size     = 8;
		$edit->monto->css_class='inputnum';
		$edit->monto->rule     = "required|callback_positivo";

		$edit->concepto = new textAreaField("Concepto", 'concepto');
		$edit->concepto->cols = 60;
		$edit->concepto->rows = 3;

		$status=$edit->_dataobject->get("status");
		if($status=='P'){
			$action = "javascript:btn_anulaf('" .$edit->rapyd->uri->get_edited_id(). "')";
			$edit->button_status("btn_status",'Anular',$action,"TR","show");	
			$edit->buttons("modify");
		}

		$edit->buttons("save","undo", "back","add");
		$edit->build();

		$smenu['link']   = barra_menu('102');
		$data['smenu']   = $this->load->view('view_sub_menu', $smenu,true);
		$data['content'] = $edit->output;
		
    $data['title']   = "$this->tits";
    $data["head"]    = script("jquery.pack.js").script("plugins/jquery.numeric.pack.js").script("plugins/jquery.floatnumber.js").$this->rapyd->get_head();
    $this->load->view('view_ventanas', $data);
	}
	
	function _valida($do){
		$do->set('status','P');
	}
		
	function anular($id){
		$this->rapyd->load('dataobject');
	
		$do = new DataObject("ocomrapid");
		$do->load($id);
		$do->set('status','AN');
		$do->save();
		logusu('ocomrapid',"Anulo orden de servicio de gasolina $id");
		redirect($this->url."dataedit/show/$id");
	}

	function positivo($valor){
		if ($valor < 0){
			$this->validation->set_message('positivo',"El campo monto debe ser positivo");
			return FALSE;
		}
  	return TRUE;
	}
	
	function _post_insert($do){
		$numero     = $do->get('numero'  );
		$placa      = $do->get('placa'   );
		$solicitante= $do->get('solicitante' );
		logusu('ocomrapid',"Creo orden de servicio de gasolina $numero para el vehiculo $placa solicitado pos $solicitante");
		//redirect($this->url."actualizar/$id");
	}
	function _post_update($do){
		$numero     = $do->get('numero'  );
		$placa      = $do->get('placa'   );
		$solicitante= $do->get('solicitante' );
		logusu('ocomrapid',"Modifico orden de servicio de gasolina $numero para el vehiculo $placa solicitado pos $solicitante");
		//redirect($this->url."actualizar/$id");
	}
	
	function cancelar($numero){
		$numero=$this->db->escape($numero);
		$data = array('status'=>'C');
		$where = "numero = $numero";
		$mSQL = $this->db->update_string('ocomrapid', $data, $where);
		//echo $mSQL;
		$this->db->simple_query($mSQL);
		logusu('ocomrapid',"Marcado como cancelado orden de servicio de gasolina $numero el ".date('YmdHms'));
		redirect($this->url.'index');
	}
}