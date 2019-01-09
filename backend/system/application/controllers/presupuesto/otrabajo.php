<?php
require_once(BASEPATH.'application/controllers/presupuesto/common.php');
class otrabajo extends Common {

	var $url   ="presupuesto/otrabajo/";
	var $tits  ="Orden de Trabajo";
	var $titp  ="Ordenes de Trabajo";

	function otrabajo(){
		parent::Controller();
		$this->load->library("rapyd");
		
		//$this->datasis->modulo_id(70,1);
		
	}

	function index(){
		redirect("presupuesto/otrabajo/filteredgrid");
	}

	function filteredgrid(){

		$this->rapyd->load("datafilter2","datagrid");
		//$this->rapyd->uri->keep_persistence();

		$filter = new DataFilter2("");

		$filter->db->select("a.compromiso,b.codigoadm,b.fondo,b.partida,b.ordinal,a.reverso reverso,a.numero numero,a.fecha fecha,a.tipo tipo,a.status status,a.cod_prov cod_prov,a.beneficiario beneficiario,c.nombre proveed");
		$filter->db->from("otrabajo a");
		$filter->db->join("itotrabajo b" ,"a.numero=b.numero"    ,"LEFT");
		$filter->db->join("sprv c"       ,"c.proveed=a.cod_prov" ,"LEFT");
		$filter->db->groupby("a.numero");
		
		$filter->numero = new inputField("Numero", 'numero');
		$filter->numero->size = 6;
		$filter->numero->db_name='a.numero';
		
		$filter->fecha = new dateonlyField("Fecha", "fecha");
		$filter->fecha->size=12;
		$filter->fecha->dbformat = "Y-m-d";
		$filter->fecha->db_name='a.fecha';

		$filter->cod_prov = new inputField("Beneficiario", 'cod_prov');
		$filter->cod_prov->size   = 6;
		$filter->cod_prov->db_name='a.cod_prov';
		$filter->cod_prov->type   ='inputhidden';
		$filter->cod_prov->clause   = 'where';
		$filter->cod_prov->operator = '=';

		$filter->reverso = new inputField("Reverso de", "reverso");
		$filter->reverso->size=20;

		$filter->observa = new inputField("Concepto", "observa");
		$filter->observa->size=20;

		$filter->status = new dropdownField("Estado","status");
		$filter->status->option("","");
		$filter->status->option("P","Sin Comprometer");
		$filter->status->option("C","Comprometido");
		$filter->status->option("T","Causado");
		$filter->status->option("O","Ordenado Pago");
		$filter->status->option("E","Pagado");
		$filter->status->option("A","Anulado");
		$filter->status->option("X","Reversado");
		$filter->status->option("M","Sin Terminar");
		$filter->status->option("p","Por Modificar");
		$filter->status->style="width:150px";

		$filter->buttons("reset","search");
		$filter->build();

		$uri = anchor('presupuesto/otrabajo/dataedit/show/<#numero#>','<str_pad><#numero#>|8|0|STR_PAD_LEFT</str_pad>');
		$uri_2 = anchor('presupuesto/otrabajo/dataedit/S/create/<#numero#>','Duplicar');

		function sta($status){
			switch($status){
				case "P":return "Por Terminar";break;
				case "C":return "Terminada";break;
				case "A":return "Anulado";break;
			}
		}

		$grid = new DataGrid("");
		if($this->datasis->puede(25))
			$grid->order_by("status = 'P',numero ","desc");
		else
			$grid->order_by("numero","desc");

		$grid->per_page = 20;
		$grid->use_function('substr','str_pad','sta');

		$grid->column_orderby("N&uacute;mero"   ,$uri                                          ,"numero"                                 );
		$grid->column_orderby("Tipo"            ,"tipo"                                        ,"tipo"           ,"align='center'"       );
		$grid->column_orderby("Fecha"           ,"<dbdate_to_human><#fecha#></dbdate_to_human>","fecha"          ,"align='center'"       );
		$grid->column_orderby("Beneficiario"    ,"proveed"                                     ,"proveed"                                );
		$grid->column_orderby("Estado"          ,"<sta><#status#></sta>"                       ,"status"         ,"align='center' "      );
		$grid->column("Duplicar"                ,$uri_2                                        ,"align='center'"                         );

		$grid->add("presupuesto/otrabajo/dataedit/create");

		$grid->build();

		$salida='';

		$data['filtro']  = $filter->output;
		$data['content'] = $salida.$grid->output;
		$data['script']  = script("jquery.js");
		$data['title']   = $this->titp;
		$data["head"]    = $this->rapyd->get_head();
		$this->load->view('view_ventanas', $data);
	}

	function dataedit(){
		//$this->datasis->modulo_id(70,1);
		$this->rapyd->load('dataobject','datadetails');
		
		$mSPRV=array(
				'tabla'   =>'sprv',
				'columnas'=>array(
				'proveed' =>'C&oacute;odigo',
				'rif'     =>'RIF',
				'nombre'  =>'Nombre',
				'contacto'=>'Contacto'),
				'filtro'  =>array('proveed'=>'C&oacute;digo','nombre'=>'Nombre','rif'=>'RIF'),
				'retornar'=>array('proveed'=>'cod_prov', 'nombre'=>'nombrep','reteiva'=>'reteiva_prov','rif'=>'rif' ),
				'script'  =>array('cal_lislr()','cal_total()'),
				'titulo'  =>'Buscar Beneficiario');

		$bSPRV=$this->datasis->p_modbus($mSPRV,"proveed");

		$do = new DataObject("otrabajo");
		$do->order_by('itotrabajo','itotrabajo.id','desc');
		$do->rel_one_to_many('itotrabajo', 'itotrabajo', array('numero'=>'numero'));
		$do->pointer('sprv' ,'sprv.proveed=otrabajo.cod_prov','sprv.nombre as nombrep, sprv.rif as rif','LEFT');

		$edit = new DataDetails("Orden de Compra", $do);
		$edit->back_url = site_url("presupuesto/otrabajo/filteredgrid");
		$edit->set_rel_title('itotrabajo','Rubro <#o#>');

		$status=$edit->get_from_dataobjetct('status');

		$edit->pre_process('insert'  ,'_valida');
		$edit->pre_process('update'  ,'_valida');
		//$edit->pre_process('delete'  ,'_pre_delete');
        //
		//$edit->post_process('insert','_post_insert');
		//$edit->post_process('update','_post_update');
		//$edit->post_process('delete','_post_delete');
		
		$a='';
		switch($status){
			case 'P':$a="Sin Comprometer";break;
			case 'C':$a="Comprometida";break;
			case 'T':$a="Causada";break;
			case 'O':$a="Ordenado Pago";break;
			case 'E':$a="Pagado";break;
			case 'E':$a="No Terminada";break;
		}
		$edit->status = new freeField("Estado", 'estado',$a);

		$edit->numero  = new inputField("N&uacute;mero", "numero");
		$edit->numero->mode="autohide";
		$edit->numero->when=array('show');
		
		$edit->fecha = new  dateonlyField("Fecha",  "fecha");
		$edit->fecha->dbformat ='Ymd';
		$edit->fecha->insertValue = date('Ymd');
		$edit->fecha->size =12;
		if($status=='P')
		$edit->fecha->readonly = true;
		//$edit->fecha->readonly = true;
		//$edit->fecha->mode="autohide";
		//$edit->fecha->when = array("show","modify");
		$edit->fecha->rule = "callback_chfecha";

		$edit->status = new dropdownField("Estado","status");
		$edit->status->option("","");
		$edit->status->option("P","Sin Comprometer");
		$edit->status->option("C","Comprometido");
		$edit->status->option("T","Causado");
		$edit->status->option("O","Ordenado Pago");
		$edit->status->option("E","Pagado");
		$edit->status->option("A","Anulado");
		$edit->status->option("R","Reversado");
		$edit->status->option("M","Sin Terminar");
		$edit->status->option("p","Por Modificar");
		$edit->status->when=array('show');
		if($status=='P')
		$edit->status->readonly = true;
		//$edit->status->readonly = true;

		$edit->usolicita = new dropdownField("Unidad Solicitante", "usolicita");
		$edit->usolicita->options("SELECT codigo, nombre FROM uejecutora ORDER BY nombre");
		$edit->usolicita->style="width:250px";
		
		$lsnc='<a href="javascript:consulsprv();" title="Proveedor" onclick="">Consulta/Agrega BENEFICIARIO</a>';
		$edit->cod_prov = new inputField("Beneficiario", 'cod_prov');
		$edit->cod_prov->size     = 4;
		$edit->cod_prov->rule     = "trim|required";
		$edit->cod_prov->append($bSPRV);
		//$edit->cod_prov->readonly=true;
		if($status=='P')
		$edit->cod_prov->readonly = true;
		$edit->cod_prov->append($lsnc);
		$edit->cod_prov->onchange = "cal_nprov();";
		//$edit->cod_prov->mode="autohide";

		$edit->nombrep = new inputField("Nombre Beneficiario", 'nombrep');
		$edit->nombrep->size = 20;
		//$edit->nombrep->readonly = true;
		$edit->nombrep->pointer = true;
		if($status=='P')
		$edit->nombrep->readonly = true;
		//$edit->nombrep->readonly = true;

		$edit->reteiva_prov  = new inputField("% R.IVA", "reteiva_prov");
		$edit->reteiva_prov->size=2;
		//$edit->reteiva_prov->mode="autohide";
		$edit->reteiva_prov->when=array('modify','create','show');
		$edit->reteiva_prov->readonly = true;
		if($status=='P')
		$edit->reteiva_prov->readonly = true;

		$edit->rif  = new inputField("RIF", "rif");
		$edit->rif->size=10;
		$edit->rif->pointer = true;
		if($status=='P')
		$edit->rif->readonly = true;

		$edit->creten = new dropdownField("Codigo ISLR: ","creten");
		//$edit->creten->option("","");
		$edit->creten->options("SELECT codigo,CONCAT_WS(' ',codigo,activida) FROM rete ORDER BY codigo");
		$edit->creten->style="width:300px;";
		$edit->creten->onchange ='cal_total();';
		if($status=='P')
		$edit->creten->readonly = true;
		
		$edit->condiciones = new textAreaField("Condiciones Especiales", 'condiciones');
		$edit->condiciones->rule = "trim";
		$edit->condiciones->cols = 25;
		$edit->condiciones->rows = 2;
		
		$edit->lentrega = new textAreaField("Lugar de Entrega", 'lentrega');
		$edit->lentrega->cols     = 25;
		$edit->lentrega->rows     = 2;
		if($status=='P')
		$edit->lentrega->readonly = true;

		if($this->datasis->traevalor("USAOCOMPRAPROCED")=='S'){
			$edit->proced = new inputField("Procedimiento","proced");
			$edit->proced->size=20;
			//$edit->proced->typ;='inputhidden';
		}

		if($this->datasis->traevalor("USACOMPEFP")=='S'){
			$edit->pentret = new dropdownField("Plazo Entrega","pentret");
			$edit->pentret->option("M","Meses");
			$edit->pentret->option("H","Dias Habiles");
			$edit->pentret->option("C","Dias Continuos");
			$edit->pentret->style="width:150px;";
			if($status=='P')
			$edit->pentret->readonly = true;

			$edit->pentrec = new inputField("", 'pentrec');
			$edit->pentrec->size = 5;
			$edit->pentrec->css_class='inputnum';
			$edit->pentrec->rule     ='required|numeric';
			if($status=='P')
			$edit->pentrec->readonly = true;
		}

		$edit->subtotal = new inputField("Total Base Imponible", 'subtotal');
		$edit->subtotal->css_class='inputnum';
		$edit->subtotal->size = 8;
		if($status=='P')
		$edit->subtotal->readonly = true;
		//$edit->subtotal->mode="autohide";

		$edit->ivaa = new inputField("IVA Sobre Tasa", 'ivaa');
		$edit->ivaa->css_class='inputnum';
		$edit->ivaa->size = 8;
		if($status=='P')
		$edit->ivaa->readonly = true;
		//$edit->ivaa->mode="autohide";

		$edit->ivag = new inputField("IVA Tasa General", 'ivag');
		$edit->ivag->css_class='inputnum';
		$edit->ivag->size = 8;
		if($status=='P')
		$edit->ivag->readonly = true;
		//$edit->ivag->mode="autohide";

		$edit->ivar = new inputField("IVA Tasa reducida", 'ivar');
		$edit->ivar->css_class='inputnum';
		$edit->ivar->size = 8;
		if($status=='P')
		$edit->ivar->readonly = true;
		//$edit->ivar->mode="autohide";

		$edit->exento = new inputField("Exento", 'exento');
		$edit->exento->css_class='inputnum';
		$edit->exento->size = 8;
		if($status=='P')
		$edit->exento->readonly = true;
		//$edit->exento->mode="autohide";

		$edit->reteiva = new inputField("Retencion de IVA", 'reteiva');
		$edit->reteiva->css_class='inputnum';
		$edit->reteiva->size = 8;
		if($status=='P')
		$edit->reteiva->readonly = true;
		//$edit->reteiva->mode="autohide";

		$edit->reten = new inputField("Retencion de ISLR", 'reten');
		$edit->reten->css_class='inputnum';
		$edit->reten->size = 8;
		if($status=='P')
		$edit->reten->readonly = true;
		//$edit->reten->mode="autohide";

		$edit->total2 = new inputField("Total", 'total2');
		$edit->total2->css_class='inputnum';
		$edit->total2->size = 8;
		if($status=='P')
		$edit->total2->readonly = true;
		//$edit->total2->mode="autohide";

		$edit->itdescripcion = new textareaField("(<#o#>) Descripci&oacute;n", "descripcion_<#i#>");
		$edit->itdescripcion->db_name  ='descripcion';
		$edit->itdescripcion->cols=30;
		$edit->itdescripcion->rows=2;
		//$edit->itdescripcion->rule     = 'required';
		$edit->itdescripcion->rel_id   ='itotrabajo';
		//$edit->itdescripcion->mode="autohide";

		$edit->itunidad = new dropdownField("(<#o#>) Unidad", "unidad_<#i#>");
		$edit->itunidad->db_name= 'unidad';
		$edit->itunidad->rule   = 'required';
		$edit->itunidad->rel_id = 'itotrabajo';
		$edit->itunidad->options("SELECT unidades AS id,unidades FROM unidad ORDER BY unidades");
		$edit->itunidad->style="width:70px";
		//$edit->itunidad->mode="autohide";

		$edit->itcantidad = new inputField("(<#o#>) Cantidad", "cantidad_<#i#>");
		$edit->itcantidad->css_class='inputnum';
		$edit->itcantidad->db_name  ='cantidad';
		$edit->itcantidad->rel_id   ='itotrabajo';
		$edit->itcantidad->rule     ='numeric';
		$edit->itcantidad->onchange ='cal_importe(<#i#>);';
		$edit->itcantidad->size     =4;
		//$edit->itcantidad->mode="autohide";

		$edit->itprecio = new inputField("(<#o#>) Precio", "precio_<#i#>");
		$edit->itprecio->css_class='inputnum';
		$edit->itprecio->db_name  ='precio';
		$edit->itprecio->rel_id   ='itotrabajo';
		$edit->itprecio->rule     ='callback_positivo';
		$edit->itprecio->onchange ='cal_importe(<#i#>);';
		$edit->itprecio->size     =6;
		//$edit->itprecio->mode="autohide";
		
		$edit->itiva = new dropdownField("(<#o#>) IVA", "iva_<#i#>");
		$edit->itiva->db_name  ='iva';
		$edit->itiva->rel_id   ='itotrabajo';
		$edit->itiva->onchange ='cal_importe(<#i#>);';
		$edit->itiva->options($this->_ivaplica());
		$edit->itiva->option("0"  ,"0%");
		$edit->itiva->style    ="width:80px";

		$edit->itimporte = new inputField("(<#o#>) Importe", "importe_<#i#>");
		$edit->itimporte->css_class='inputnum';
		$edit->itimporte->db_name  ='importe';
		$edit->itimporte->rel_id   ='itotrabajo';
		$edit->itimporte->rule     ='numeric';
		$edit->itimporte->onchange ='cal_importep(<#i#>);';
		//$edit->itimporte->readonly =true;
		$edit->itimporte->size     =8;
		if($status=='P')
		$edit->itimporte->readonly = true;
		//$edit->itimporte->mode="autohide";

		$edit->redondear = new dropdownField("Redondear","redondear");
		$edit->redondear->option("R2","Sumar Redondear 2 Decimales");
		$edit->redondear->option("R0","Sumar SIN Redondear 2 Decimales");
		//$edit->redondear->onchange = "cal_total();";

		if($status=='P'){
			$edit->buttons("modify");
			$edit->buttons("save");
			$action = "javascript:window.location='" .site_url('presupuesto/otrabajo/terminada/'.$edit->rapyd->uri->get_edited_id()). "'";
			$edit->button_status("btn_status",'Marcar Como terminada',$action,"TR","show");
			
			$action = "javascript:btn_anular('" .$edit->rapyd->uri->get_edited_id()."')";
			$edit->button_status("btn_anular",'Anular',$action,"TR","show");	
		}elseif($status=='C'){
			$action = "javascript:btn_noterminada('" .$edit->rapyd->uri->get_edited_id()."')";
			$edit->button_status("btn_rever",'Marcar como NO Terminada',$action,"TR","show");
		}elseif($status=='A'){
			$edit->buttons("delete");
		}else{
			$edit->buttons("modify");
			$edit->buttons("save");
		}

		$edit->buttons("undo","back","add_rel","add");
		$edit->build();

		$ivaplica           =$this->ivaplica2();
		$conten['ivar']     = $ivaplica['redutasa'];
		$conten['ivag']     = $ivaplica['tasa'];
		$conten['ivaa']     = $ivaplica['sobretasa'];
		$conten['title2']   = $this->tits;

		$smenu['link']   = barra_menu('12A');
		$data['smenu']   = $this->load->view('view_sub_menu', $smenu,true);
		$conten["form"]  = &$edit;
		$data['content'] = $this->load->view('view_otrabajo', $conten,true);
		$data['title']   = $this->tits;

		$data["head"]    = script('jquery.js').script('jquery-ui.js').script('plugins/jquery.numeric.pack.js').script('plugins/jquery.floatnumber.js').script('plugins/jquery.meiomask.js').style('vino/jquery-ui.css').$this->rapyd->get_head();
		$this->load->view('view_ventanas', $data);
	}

	function ivaplica2($mfecha=NULL){
		if(empty($mfecha)) $mfecha=date('Ymd');
		//$CI =& get_instance();
		$qq = $this->db->query("SELECT tasa, redutasa, sobretasa FROM civa WHERE fecha < '$mfecha' ORDER BY fecha DESC LIMIT 1");
		$rr = $qq->row_array();
		//$aa = each($rr);
		return $rr;
	}

	function chfecha($fecha){

		return true;
		$date  = DateTime::createFromFormat('d/m/Y',$fecha);
		$fecha = $date->format('Ymd');
		$now   = date('Ymd',now());

		if($fecha > $now){
			$this->validation->set_message('chfecha',"La fecha es incorrecta, No es V�lida una Fecha Futura. </br>La fecha del servidor es:".dbdate_to_human($now));
			return false;
		}

		$f = $this->datasis->dameval("SELECT fecha FROM otrabajo WHERE fecha >$fecha ORDER BY fecha DESC LIMIT 1");
		if(!empty($f)){
			$this->validation->set_message('chfecha',"La fecha es incorrecta, debe ser mayor o igual a ".dbdate_to_human($f));
			return false;

		}
	}

	function terminada($id){
		$this->rapyd->load('dataobject');

		$do = new DataObject("otrabajo");
		$do->rel_one_to_many('itotrabajo', 'itotrabajo', array('numero'=>'numero'));
		$do->load($id);
		
		$numero = $do->get('numero');
		
		if(strpos($numero,'_')===0){
			$contador = $this->datasis->fprox_numero('notrabajo');
			$do->set('numero',$contador);
			
			for($i=0;$i < $do->count_rel('itotrabajo');$i++){
				$do->set_rel('itotrabajo','id'    ,''       ,$i);
				$do->set_rel('itotrabajo','numero',$contador,$i);
			}
			$this->db->query("DELETE FROM itotrabajo WHERE numero='$numero'");
		}else{
				$contador=$id;
		}

		if(empty($error)){
			$do->set('status','C');
			$do->save();
		}

		if(empty($error)){
			logusu('otrabajo',"Marco como terminada orden de trabajo Nro $id");
			redirect("presupuesto/otrabajo/dataedit/show/$contador");
		}else{
			logusu('otrabajo',"Marco como terminada orden de trabajo Nro $id. con ERROR:$error ");
			$data['content'] = $error.anchor("presupuesto/otrabajo/dataedit/show/$id",'Regresar');
			$data['title']   = " Orden de Trabajo ";
			$data["head"]    = $this->rapyd->get_head().script('jquery.js').script("plugins/jquery.numeric.pack.js");
			$this->load->view('view_ventanas', $data);
		}
	}

	function noterminada($id){
		$this->rapyd->load('dataobject');

		$do = new DataObject("otrabajo");
		$do->load($id);

		if(empty($error)){
			$do->set('status','P');
			$do->save();
		}

		if(empty($error)){
			logusu('otrabajo',"Marco como NO terminada orden de trabajo Nro $id");
			redirect("presupuesto/otrabajo/dataedit/show/$id");
		}else{
			logusu('otrabajo',"Marco como NO terminada orden de trabajo Nro $id. con ERROR:$error ");
			$data['content'] = $error.anchor("presupuesto/otrabajo/dataedit/show/$id",'Regresar');
			$data['title']   = " Orden de Trabajo ";
			$data["head"]    = $this->rapyd->get_head().script('jquery.js').script("plugins/jquery.numeric.pack.js");
			$this->load->view('view_ventanas', $data);
		}
	}

	function anular($id){
		$this->rapyd->load('dataobject');

		$do = new DataObject("otrabajo");
		$do->load($id);
		$do->set('status','A');

		$do->save();

		logusu('otrabajo',"Anulo Orden de Compra Nro $id");
		redirect("presupuesto/otrabajo/dataedit/show/$id");
	}

	function _ivaplica($mfecha=NULL){
		if(empty($mfecha)) $mfecha=date('Ymd');
		$qq = $this->datasis->damerow("SELECT tasa AS g, redutasa AS r, sobretasa AS a FROM civa WHERE fecha < '$mfecha' ORDER BY fecha DESC LIMIT 1");
		$rr=array();
		$rr['0']='0%';
		foreach ($qq AS $val){
			$rr[$val]=$val.'%';
		}

		return $rr;
	}

	function validac(&$do){
		$error        = '';
		$numero       = $do->get('numero'      );
		//$nocompra     = $do->get('nocompra'    );
		$redondear    = $do->get('redondear'   );
		$rr           = $this->ivaplica2();
		
		//$nocomprae    = $this->db->escape($nocompra);
		//$ocompra=$this->datasis->damerow("SELECT cod_prov,usolicita,proced FROM ocompra WHERE numero=$nocomprae");
		//
		//$do->set('cod_prov'  ,$ocompra['cod_prov' ]);
		//$do->set('usolicita' ,$ocompra['usolicita']);
		//$do->set('proced'    ,$ocompra['proced'   ]);
		
		if(empty($numero)){
			$ntransac = $this->datasis->fprox_numero('ntransac');
			$do->set('numero','_'.$ntransac);
			$do->pk    =array('numero'=>'_'.$ntransac);
		}
		
		$usr=$this->session->userdata('usuario');
		$name = $this->datasis->dameval("SELECT us_nombre FROM usuario WHERE us_codigo ='$usr' ");
		$do->set('user',$usr);
		$do->set('username',$name);

		$error= '';

		$tretener=$giva=$aiva=$riva=$exento=$reteiva=$subtotal=$mivag=$mivar=$mivaa=$tivag=$tivar=$tivaa=$subt=$treten=$ivasm=$totiva=0;

		$admfondo=array();$admfondop=array();$borrarivas=array();$ivasm=0;$totiva=0;
		for($i=0;$i < $do->count_rel('itotrabajo');$i++){
			if(empty($numero)){
				$do->set_rel('itotrabajo','numero','_'.$ntransac,$i);
			}
			
			$piva       = $do->get_rel('itotrabajo','iva'        ,$i);
			$cantidad   = $do->get_rel('itotrabajo','cantidad'   ,$i);
			$precio     = $do->get_rel('itotrabajo','precio'     ,$i);
			if($redondear=='R0'){
				$importe=$precio*$cantidad;
//				$importe    = $do->get_rel('itotrabajo','importe'    ,$i);
				$ivan       = ($importe*$piva)/100;
			}else{
				//$importe    = $do->get_rel('itotrabajo','importe'    ,$i);
				$importe=round($precio*$cantidad,2);
				$ivan       = $importe*$piva/100;
			}

			$totiva+=$ivan;
			$a=$cantidad*$precio;
			
			$do->set_rel('itotrabajo','importe' ,$importe,$i);

			$subtotal =$importe+round($subtotal,2);

			if($redondear=='R0')
			$ivan  = $ivan    ;
			else
			$ivan  = round($ivan,2);



			if($piva==$rr['tasa']     ){
				if($redondear=='R0')
				$giva  = ($rr['tasa'] *$importe)/100 + $giva;
				else
				$giva  = round(($rr['tasa'] *$importe)/100 + $giva,2);

				$mivag = $importe                    + $mivag;
			}
			if($piva==$rr['redutasa'] ){
				if($redondear=='R0')
				$riva  =($rr['redutasa'] *$importe)/100+$riva;
				else
				$riva  =round($rr['redutasa'] *$importe,2)/100+$riva;

				$mivar =$importe                       +$mivar;
			}
			if($piva==$rr['sobretasa']){
				if($redondear=='R0')
				$aiva =($rr['sobretasa']*$importe)/100+$aiva;
				else
				$aiva =round($rr['sobretasa']*$importe,2)/100+$aiva;
				$mivaa=$importe                       +$mivaa;
			}
			if($piva==0)$exento+=$importe;
		}
		
		$total2=$giva+$riva+$aiva+$subtotal;
		$total =round($total2,2);
		
		$do->set('ivag'          , $giva                );
		$do->set('ivar'          , $riva                );
		$do->set('ivaa'          , $aiva                );
		$do->set('tivag'         , $rr['tasa']          );
		$do->set('tivar'         , $rr['redutasa']      );
		$do->set('tivaa'         , $rr['sobretasa']     );
		$do->set('mivag'         , $mivag               );
		$do->set('mivar'         , $mivar               );
		$do->set('mivaa'         , $mivaa               );
		$do->set('subtotal'      , $subtotal            );
		$do->set('exento'        , $exento              );
		$do->set('total'         , $total               );
		$do->set('total2'        , $total2              );
		$do->set('status'        , 'P'                  );

		if(!empty($error)){
			return $error;
		}
	}

	function _valida($do){
		$error = $this->validac($do);

		if(!empty($error)){
			$do->error_message_ar['pre_ins']=$error;
			$do->error_message_ar['pre_upd']=$error;
			return false;
		}
	}

	function positivo($valor){
		if ($valor <= 0){
			$this->validation->set_message('positivo',"El campo Precio debe ser positivo");
			return FALSE;
		}
		return TRUE;
	}

	function _post($do){
		$id=$do->get('numero');
		redirect("presupuesto/otrabajo/actualizar/$id");
	}



	function _pre_delete($do){
		$error  ='';
		$numero =$do->get('numero');
		$c      =$this->datasis->dameval("SELECT COUNT(*) FROM pacom WHERE compra='$numero'");

		if($c>0)
		$error.="ERROR. El Registro no puede ser eliminado debido a que tiene un Orden de Pago relacionada";

		if(!empty($error)){
			$do->error_message_ar['pre_del']=$error;
			return false;
		}
	}

	function _post_insert($do){
		$numero = $do->get('numero');
		logusu('otrabajo',"Creo Orden de Compra Nro $numero");
		//redirect($this->url."actualizar/$numero");
	}

	function _post_update($do){
		$numero = $do->get('numero');
		logusu('otrabajo'," Modifico Orden de Compra Nro $numero");
		//redirect($this->url."actualizar/$numero");
	}
	function _post_delete($do){
		$numero = $do->get('numero');
		logusu('otrabajo'," Elimino Orden de Compra Nro $numero");
	}

	function prueba(){
		$total=77760;
		for($i=69000;$i<=$total;++$i){
			//echo "</br>".
			$t=$i+($i*12/100);//
			if($t>=$total)echo $i."</br>";
		}
	}

	function instalar(){
		$query="ALTER TABLE `ocompra` ADD COLUMN `nocompra` VARCHAR(12) NULL AFTER `condiciones";
		$this->db->simple_query($query);
		
	}
}
?>
