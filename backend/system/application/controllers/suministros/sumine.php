<?php
class sumine extends Controller {

	var $url ='suministros/sumine/';
	var $titp='Notas de Entrega';
	var $tits='Nota de Entrega';

	function sumine(){
		parent::Controller();
		$this->load->library("rapyd");
		$this->datasis->modulo_id(88,1);
	}
	function index(){
		redirect($this->url."/filteredgrid");
	}

	function filteredgrid(){
		$this->rapyd->load("datafilter2","datagrid");

		$modbus=array(
				'tabla'=> 'sprv',
				'columnas' => array('proveed'=>'Codigo', 'nombre'=>'Nombre' ),
				'filtro'   => array('proveed'=>'Codigo', 'nombre'=>'Nombre' ),
				'retornar' => array('proveed'=>'proveed' ),
				'titulo'   => 'Buscar Beneficiario'
		);
		$bproveed = $this->datasis->modbus($modbus);

		$script='';

		$filter = new DataFilter2("");
		$filter->db->from("sumine");
		$filter->db->join("su_alma","sumine.alma=su_alma.codigo",'LEFT');

		$filter->script($script);

		$filter->numero = new inputField("Numero", "numero");
		$filter->numero->size=15;

		$filter->fecha = new dateonlyField("Fecha", "fecha");
		$filter->fecha->dbformat = "Y-m-d";
		$filter->fecha->size=12;

		$filter->observacion = new inputField("Observaci&oacute;n", "observacion");
		$filter->observacion->size=40;

		$filter->status = new dropdownField("Estado","status");
		$filter->status->option("","");
		$filter->status->option("P","Por Entregar" );
		$filter->status->option("R","Por Solicitar");
		$filter->status->option("A","Anulado"      );
		$filter->status->option("C","Entregado"    );
		$filter->status->style="width:150px";

		$filter->buttons("reset","search");
		$filter->build();

		$uri = anchor('suministros/sumine/dataedit/show/<#numero#>','<str_pad><#numero#>|8|0|STR_PAD_LEFT</str_pad>');

		function sta($status){
			switch($status){
				case "P":return "Por Entregar" ;break;
				case "R":return "Por Solicitar";break;
				case "A":return "Anulado"      ;break;
				case "C":return "Entregado"    ;break;
			}
		}

		$grid = new DataGrid("");
		$grid->order_by("numero","desc");
		$grid->per_page = 20;
		$grid->use_function('substr','str_pad','sta');

		$grid->column_orderby("N&uacute;mero"         ,$uri                                              ,"numero");
		$grid->column_orderby("Receptor"              ,"descrip"                                         ,"descrip"    ,"align='left'        ");
		$grid->column_orderby("Fecha"                 ,"<dbdate_to_human><#fecha#></dbdate_to_human>"    ,"fecha"      ,"align='center'      ");
		$grid->column_orderby("Observaci&oacute;n"    ,"<wordwrap><#observacion#>|50|\n|true</wordwrap>" ,"observacion","align='left'         ");
		$grid->column_orderby("Estado"                ,"<sta><#status#></sta>"                           ,"status"     ,"align='center'      ");

		$grid->add($this->url."/dataedit/create");
		$grid->build();

		//$data['content'] = $filter->output.$grid->output;
		$data['filtro']  = $filter->output;
		$data['content'] = $grid->output;
		$data['title']   = "Notas de Entrega";
		$data['script']  = script("jquery.js")."\n";
		$data["head"]    = script("jquery.js").$this->rapyd->get_head();
		$this->load->view('view_ventanas', $data);
	}

	function dataedit(){
		$this->rapyd->load('dataobject','datadetails');

		$modbus=array(
			'tabla'   =>'view_sumi_saldo',
			'columnas'=>array(
				'codigo'  =>'C&oacute;digo',
				'descrip' =>'Descripcion',
				'unidad'  =>'Unidad',
				'caub'    =>'Almacen',
				'cantidad'=>'Cantidad'),
			'filtro'  =>array('codigo' =>'C&oacute;digo','descrip'=>'Descripcion'),
			'retornar'=>array('codigo'=>'codigo_<#i#>','descrip'=>'descripcion_<#i#>','unidad'=>'unidad_<#i#>'),
			'p_uri'=>array(4=>'<#i#>',5=>'<#caub#>'),
			'where'=>'caub = <#caub#>',
			'titulo'  =>'Busqueda de Articulos');

		$btn=$this->datasis->p_modbus($modbus,'<#i#>/<#caub#>');
		$btn='<img src="'.base_url().'assets/default/images/system-search.png"  alt="Busqueda de Suministros" title="Busqueda de Suministros" border="0" onclick="modbusdepen(<#i#>)"/>';

		//$script='	';
		//
		$do = new DataObject("sumine");
		$do->rel_one_to_many('itsumine', 'itsumine', array('numero'=>'numero'));

		$edit = new DataDetails("Nota de Entrega de Bienes o Suministro", $do);
		$edit->back_url = site_url($this->url."/filteredgrid");
		$edit->set_rel_title('itsumine','Rubro <#o#>');

		$edit->pre_process('insert','_valida');
		$edit->pre_process('update','_valida');

		$status=$edit->get_from_dataobjetct('status');

		$edit->numero   = new inputField("N&uacute;mero", "numero");
		$edit->numero->mode="autohide";
		$edit->numero->when=array('show');

		$edit->fecha = new  dateonlyField("Fecha",  "fecha");
		$edit->fecha->insertValue = date('Y-m-d');
		$edit->fecha->size =12;
		if($status=='P')
		$edit->fecha->mode="autohide";

		$edit->caub = new dropdownField("Almacen", "caub");
		$edit->caub->options("SELECT codigo,CONCAT_WS(' ',codigo,descrip) valor FROM su_caub");
		if($status=='P')
		$edit->caub->mode="autohide";
		
		$edit->conc = new dropdownField("Concepto", "conc");
		$edit->conc->options("SELECT id,descrip valor FROM su_conc WHERE tipo='S'" );

		$edit->alma = new dropdownField("Receptor", "alma");
		$edit->alma->options("SELECT codigo,CONCAT_WS(' ',codigo,descrip) valor FROM su_alma WHERE codigo<>'0000'");
		if($status=='P')
		$edit->alma->mode="autohide";

		$edit->status = new dropdownField("Estado", "status");
		$edit->status->option("A","Anulado"      );
		$edit->status->option("R","Por Solicitar");
		$edit->status->option("P","Por Entregar" );
		$edit->status->option("C","Entregado"    );
		$edit->status->when  =(array("show"));

		$edit->observacion = new textareaField("Observaci&oacute;n", "observacion");
		$edit->observacion->rows=2;
		$edit->observacion->cols=70;
		//$edit->observacion->rule='required';

		$edit->codigo = new inputField("(<#o#>) Codigo", "codigo_<#i#>");
		$edit->codigo->rel_id ='itsumine';
//		$edit->codigo->rule='callback_repetido';
		$edit->codigo->size=10;
		//$edit->codigo->append('<img src="/tortuga/assets/default/images/system-search.png"  alt="Busqueda de Articulos" title="Busqueda de Articulos" border="0" onclick="modbusdepen(<#i#>)"/>');
		$edit->codigo->db_name='codigo';
		$edit->codigo->autocomplete=false;

		$edit->codigo->append($btn);
		if($status=='P')
		$edit->codigo->readonly=true;

		$edit->descripcion = new inputField("Descripci&oacute;n", "descripcion_<#i#>");
		$edit->descripcion->rule   ='required';
		$edit->descripcion->db_name='descripcion';
		$edit->descripcion->rel_id ='itsumine';
		$edit->descripcion->size    =30;
		$edit->descripcion->readonly =true;

		$edit->unidad = new inputField("Unidad", "unidad_<#i#>");
		$edit->unidad->db_name  ='unidad';
		$edit->unidad->rel_id   ='itsumine';
		$edit->unidad->size     =10;
		$edit->unidad->readonly =true;

		$edit->solicitado = new inputField("Solicitado", "solicitado_<#i#>");
		$edit->solicitado->css_class='inputnum';
		//$edit->solicitado->rule     ='required|callback_positivo';
		$edit->solicitado->db_name  ='solicitado';
		$edit->solicitado->rel_id   ='itsumine';
		$edit->solicitado->size     =10;
		if($status=='P')
		$edit->solicitado->readonly=true;
		//$edit->solicitado->onchange ='cal_total(<#i#>);';

		$edit->cantidad = new inputField("Cantidad", "cantidad_<#i#>");
		$edit->cantidad->css_class='inputnum';
		$edit->cantidad->db_name='cantidad';
		$edit->cantidad->rel_id ='itsumine';
		$edit->cantidad->size =10;
		$edit->cantidad->onchange ='cal_total(<#i#>);';
//		$edit->descripcion->readonly=true;


		if($status=='R'){
			$edit->buttons("save","modify","delete","add_rel");
			$action = "javascript:window.location='" .site_url($this->url.'solicitar/'.$edit->rapyd->uri->get_edited_id()). "'";
			$edit->button_status("btn_status",'Solicitar',$action,"TR","show");
		}elseif($status=='P'){
			$edit->buttons("save","modify");
			$action = "javascript:window.location='" .site_url($this->url.'dessolicitar/'.$edit->rapyd->uri->get_edited_id()). "'";
			$edit->button_status("btn_status",'Reversar Solicitud',$action,"TR","show");
			$action = "javascript:window.location='" .site_url($this->url.'actualizar/'.$edit->rapyd->uri->get_edited_id()). "'";
			if($this->datasis->puede(274) || $this->datasis->essuper())$edit->button_status("btn_status",'Entregar',$action,"TR","show");
		}elseif($status=='C'){
			$action = "javascript:btn_anula('" .$edit->rapyd->uri->get_edited_id()."')";
			if($this->datasis->puede(275) || $this->datasis->essuper())$edit->button_status("btn_rever",'Reversar',$action,"TR","show");
		}elseif($status=='A'){

		}else{
			$edit->buttons("save","modify","delete","add_rel");
		}

		$edit->buttons("add",  "undo" , "back"); //"delete",
		$edit->build();

		$smenu['link']=barra_menu('192');
		$data['smenu'] = $this->load->view('view_sub_menu', $smenu,true);
		$conten["form"]  =&  $edit;
		$data['content'] = $this->load->view('view_sumine', $conten,true);
		//$data['content'] = $edit->output;
		$data['title']   = "Nota de Entrega";
		$data["head"]    = $this->rapyd->get_head().script('jquery.js').script('jquery-ui.js').script("plugins/jquery.numeric.pack.js").style('vino/jquery-ui.css');
		$this->load->view('view_ventanas', $data);
	}

	function repetido($codigo){
		if(isset($this->__rpartida)){
			if(in_array($codigo, $this->__rpartida)){
				$this->validation->set_message('repetido',"El rublo %s ($codigo) esta repetido");
				return false;
			}
		}
		$this->__rpartida[]=$codigo;
		return true;
	}



	function actualizar($id){
		$this->_actsumi($id,'-','C');
	}

	function reversar($id){
		$this->_actsumi($id,'+','P');
	}

	function _actsumi($id,$oper,$status){
		$this->rapyd->load('dataobject');
		$error='';
		$do = new DataObject("sumine");
		$do->rel_one_to_many('itsumine', 'itsumine', array('numero'=>'numero'));
		$do->load($id);
		$caub = $do->get('caub');
		$caube=$this->db->escape($caub);

		$sta = $do->get('sta');

		$sumi = new DataObject("sumi");

		if($sta!=$status){
			for($i=0;$i < $do->count_rel('itsumine');$i++){
				$codigo  = $do->get_rel('itsumine','codigo' ,$i);
				$cantidad= $do->get_rel('itsumine','cantidad' ,$i);
				$cantidad=1*$cantidad;
				$codigoe =$this->db->escape($codigo);

				if($oper=='-'){
					$sumi->load($codigo);
					$existen=$sumi->get('existen');
					//exit("SELECT cantidad FROM su_itsumi WHERE codigo=$codigoe AND alma=$caube");
					$existen2 = $this->datasis->dameval("SELECT cantidad FROM view_sumi_saldo WHERE codigo=$codigoe AND caub=$caube");

					if($existen2 < $cantidad || $existen2<$cantidad){
						$existen=number_format($existen2,2,",",".");
						$error.="<div class='alert'><p>No se puede entregar la cantidad de $cantidad suministros de codigo $codigo, porque solo hay $existen disponibles </p></div>";
					}
				}
			}

			if(empty($error)){
				for($i=0;$i < $do->count_rel('itsumine');$i++){
					$codigo  = $do->get_rel('itsumine','codigo' ,$i);
					$cantidad= $do->get_rel('itsumine','cantidad' ,$i);
					$cantidad=1*$cantidad;

					$sumi->load($codigo);
					$existen=$sumi->get('existen');

					$codigo=$this->db->escape($codigo);

					$costo = $this->datasis->dameval("SELECT (pond/existen) FROM sumi WHERE codigo=$codigo");
					$do->set_rel('itsumine','costo',$costo,$i);
					if(is_numeric($cantidad)){
						$this->db->simple_query("UPDATE sumi SET pond=pond $oper ((pond/existen)*$cantidad),existen=existen $oper $cantidad WHERE codigo=$codigo");
						$this->db->simple_query("UPDATE su_itsumi SET cantidad=cantidad $oper $cantidad WHERE codigo=$codigo AND alma=$caube");
					}
				}
			}


		}else{
			$error.="No se puede realizar la operacion para la nota de entrega";
		}


		if(empty($error)){
			$do->set('status',$status);
			$do->save();
			logusu('sumine',"Marco nota de entrega Nro $id como $status");
			redirect($this->url."/dataedit/show/$id");
		}else{
			logusu('sumine',"Marco nota de entrega Nro $id como $status . con ERROR:$error");
			$data['content'] = $error.anchor($this->url."/dataedit/show/$id",'Regresar');
			$data['title']   = $this->tits;
			$data["head"]    = $this->rapyd->get_head();
			$this->load->view('view_ventanas', $data);
		}
	}

	function solicitar($numero){
		$this->rapyd->load('dataobject');

		$do = new DataObject("sumine");
		$do->rel_one_to_many('itsumine', 'itsumine', array('numero'=>'numero'));
		$do->load($numero);
		$status = $do->get('status');
		$error  ='';

		if($status=="R"){

		}else{
			$error.="No se puede realizar la operaci&oacute;n para la nota de entrega";
		}

		if(empty($error)){
			$do->set('status','P');
			$do->save();
			logusu('sumine',"Solicito Entregar Nro $numero");
			redirect($this->url."/dataedit/show/$numero");
		}else{
			logusu('sumine',"Solicito Entregar Nro $numero. con ERROR:$error ");
			$data['content'] = $error.anchor($this->url."/dataedit/show/$numero",'Regresar');
			$data['title']   = $this->tits;
			$data["head"]    = $this->rapyd->get_head();
			$this->load->view('view_ventanas', $data);
		}
	}

	function dessolicitar($numero){
		$this->rapyd->load('dataobject');

		$do = new DataObject("sumine");
		$do->rel_one_to_many('itsumine', 'itsumine', array('numero'=>'numero'));
		$do->load($numero);
		$status = $do->get('status');
		$error  ='';

		if($status=="P"){

		}else{
			$error.="No se puede realizar la operaci&oacute;n para la nota de entrega";
		}

		if(empty($error)){
			$do->set('status','R');
			$do->save();
			logusu('sumine',"Reverso Solicitud de nota de Entrega Nro $numero");
			redirect($this->url."/dataedit/show/$numero");
		}else{
			logusu('sumine',"Reverso Solicitud de nota de Entrega. con ERROR:$error ");
			$data['content'] = $error.anchor($this->url."/dataedit/show/$numero",'Regresar');
			$data['title']   = $this->tits;
			$data["head"]    = $this->rapyd->get_head();
			$this->load->view('view_ventanas', $data);
		}
	}

	function _valida($do){
		$status=$do->get('status');
		if($status!='P')
		$do->set('status','R');

		$caub = $do->get('caub');
		$caube=$this->db->escape($caub);
		$error='';
		for($i=0;$i < $do->count_rel('itsumine');$i++){
			$codigo  = $do->get_rel('itsumine','codigo' ,$i);
			$codigoe = $this->db->escape($codigo);
			$cant    = $this->datasis->dameval("SELECT COUNT(*) FROM su_itsumi WHERE codigo=$codigoe AND alma=$caube");
			if($cant==0)$error.="<div class='alert'>El articulo $codigoe no pertenece al almacen $caube</div>";
			
			$d      = $this->datasis->dameval("SELECT descrip FROM sumi WHERE codigo=$codigoe");
			$do->set_rel('itsumine','descripcion',$d ,$i);
		}

		if(!empty($error)){
			$do->error_message_ar['pre_ins']=$error;
			$do->error_message_ar['pre_upd']=$error;
			return false;
		}
	}

	function instalar(){
		$this->db->simple_query("ALTER TABLE `sumine` ADD `status` CHAR( 1 ) NOT NULL DEFAULT 'P'");
		$this->db->simple_query("ALTER TABLE `sumine`  ADD COLUMN `conc` INT NULL DEFAULT NULL");
		$query="ALTER TABLE `sumine` ADD COLUMN `estampa` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP ";
		$this->db->simple_query($query);
	}


	//function _cantidad($do){
	//	for($i=0;$i < $do->count_rel('itsumine');$i++){
	//		$codigo= $do->get_rel('itsumine','codigo' ,$i);
	//		$cantidad= $do->get_rel('itsumine','cantidad' ,$i);
	//		$cantidad=1*$cantidad;
	//		if(is_numeric($cantidad))
	//			$this->db->simple_query("UPDATE sinv SET existen=existen - $cantidad WHERE codigo='$codigo'");
	//	}
	//}
	
	function prueba(){
		for($i=0;$i<=10000;++$i)
		echo "SELECT 'HOLA' UNION ALL ";
	}

}
?>
