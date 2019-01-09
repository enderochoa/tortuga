<?php
class suminr extends Controller {
	
	var $url ='suministros/suminr/';
	var $titp='Notas de Recepcioacute;n';
	var $tits='Nota de Recepci&oacute;n';

	function suminr(){
		parent::Controller();
		$this->load->library("rapyd");
		$this->datasis->modulo_id(87,1);
	}
	function index(){
		redirect("suministros/suminr/filteredgrid");
	}

	function filteredgrid(){
		$this->rapyd->load("datafilter","datagrid");
		
		$script='';
		
		$filter = new DataFilter("Lista");
		$filter->db->from("suminr");
		$filter->db->join("sprv","suminr.proveed=sprv.proveed","LEFT");
		
		$filter->script($script);

		$filter->numero = new inputField("Numero", "numero");
		$filter->numero->size=15;

		$filter->fecha = new dateonlyField("Fecha", "fecha");
		$filter->fecha-> dbformat = "Y-m-d";
		$filter->fecha->size=12;
		
		$modbus=array(  'tabla'=> 'sprv',
				'columnas' => array('proveed'=>'Codigo', 'nombre'=>'Nombre' ),
				'filtro'   => array('proveed'=>'Codigo', 'nombre'=>'Nombre' ),
				'retornar' => array('proveed'=>'proveed' ),
				'titulo'   => 'Buscar Beneficiario'			
		);
		$bproveed = $this->datasis->modbus($modbus);
		
		$filter->proveed = new inputField("Beneficiario","proveed");
		$filter->proveed->append($bproveed);
		$filter->proveed->size=7;

		$filter->observacion = new inputField("Observaci&oacute;n", "observacion");
		$filter->observacion->size=40;
		
		$filter->buttons("reset","search");
		$filter->build();
		
		$uri = anchor('suministros/suminr/dataedit/show/<#numero#>','<str_pad><#numero#>|8|0|STR_PAD_LEFT</str_pad>');
		
		function sta($status){
			switch($status){
				case "P":return "Pendiente" ;break;
				case "A":return "Anulado"      ;break;
				case "C":return "Recibida"    ;break;
			}
		}
		
		$grid = new DataGrid();
		$grid->order_by("numero","desc");
		$grid->per_page = 20;
		$grid->use_function('substr','str_pad','sta');
		
		$grid->column_orderby("N&uacute;mero"         ,$uri                                              ,"numero");
		$grid->column_orderby("Fecha"                 ,"<dbdate_to_human><#fecha#></dbdate_to_human>"    ,"fecha"        ,"align='center'");
		$grid->column_orderby("Beneficiario"          ,"nombre"                                          ,"proveed"      ,"align='left'"  );
		$grid->column_orderby("Total"                 ,"total"                                           ,"total"        ,"align='rigth'" );
		$grid->column_orderby("Observaci&oacute;n"    ,"<wordwrap><#observacion#>|50|\n|true</wordwrap>" ,"observacion"  ,"align='left'"  );
		$grid->column_orderby("Estado"                ,"<sta><#status#></sta>"                       ,"status"     ,"align='center'      ");
		
		$grid->add("suministros/suminr/dataedit/create");
		$grid->build();
		
		//echo $grid->db->last_query();
		
//		$data['content'] = $filter->output.$grid->output;
		$data['filtro']  = $filter->output;        
		$data['content'] = $grid->output;          
		$data['script'] = script("jquery.js")."\n";

		$data['title']   = "Notas de Recepci&oacute;n";
		$data["head"]    = script("jquery.js").$this->rapyd->get_head();
		$this->load->view('view_ventanas', $data);	
	}
	
	function dataedit(){
		$this->rapyd->load('dataobject','datadetails');
		
		$modbus=array(
			'tabla'   =>'sumi',
			'columnas'=>array(
				'codigo' =>'C&oacute;digo',
				'descrip'=>'Descripcion',
				'unidad' =>'Unidad'),
			'filtro'  =>array('codigo' =>'C&oacute;digo','descrip'=>'Descripcion'),
			'retornar'=>array('codigo'=>'codigo_<#i#>','descrip'=>'descripcion_<#i#>'),
			'p_uri'=>array(4=>'<#i#>',),
			'titulo'  =>'Busqueda de Articulos');

		$btn=$this->datasis->p_modbus($modbus,'<#i#>');
		
		//$script='	';
		//
		$do = new DataObject("suminr");
		$do->rel_one_to_many('itsuminr', 'itsuminr', array('numero'=>'numero'));
//		$do->rel_pointer('itsuminr','sumi' ,'itsuminr.codigo=sumi.codigo',"sumi.descrip descrip2");
		$do->order_by('itsuminr','itsuminr.codigo',' ');

		$edit = new DataDetails("Nota de Recepci&oacute;n", $do);
		$edit->back_url = site_url("suministros/suminr/filteredgrid");
		$edit->set_rel_title('itsuminr','Rubro <#o#>');
		
		$edit->pre_process('insert','_valida');
		$edit->pre_process('update','_valida');
		//$edit->script($script,'create');
		//$edit->script($script,'modify');
		
		$edit->numero   = new inputField("N&uacute;mero", "numero");
		$edit->numero->mode="autohide";
		$edit->numero->when=array('show');
		
		$edit->conc = new dropdownField("Concepto", "conc");
		$edit->conc->options("SELECT id,descrip FROM su_conc WHERE tipo='E'" );
		
		$edit->fecha = new  dateonlyField("Fecha",  "fecha");
		$edit->fecha->insertValue = date('Y-m-d');
		$edit->fecha->size =12;
		
		$edit->alma = new dropdownField("Receptor", "alma");
		$edit->alma->options("SELECT codigo,CONCAT_WS(' ',codigo,descrip) valor FROM su_alma WHERE codigo<>'0000'");
		

		$modbusprv=array('tabla'=> 'sprv',
				'columnas' => array('proveed'=>'Codigo', 'nombre'=>'Nombre' ),
				'filtro'   => array('proveed'=>'Codigo', 'nombre'=>'Nombre' ),
				'retornar' => array('proveed'=>'proveed' ),
				'titulo'   => 'Buscar Beneficiario'			
		);

		$bproveed = $this->datasis->modbus($modbusprv);
		$edit->proveed = new inputField("Proveedor","proveed");
		//$edit->proveed->rule='required';
		$edit->proveed->append($bproveed);
		
		$edit->caub = new dropdownField("Almacen", "caub");
		$edit->caub->options("SELECT codigo,CONCAT_WS(' ',codigo,descrip) valor FROM su_caub");
		
		$edit->status = new dropdownField("Estado", "status");
		$edit->status->option("A","Anulado"      );
		$edit->status->option("P","Por Recibir" );
		$edit->status->option("C","Recibido"    );
		$edit->status->when = (array("show"));

		$edit->observacion = new textareaField("Observaci&oacute;n", "observacion");
		$edit->observacion->rows=1;
		$edit->observacion->cols=80;
//		$edit->observacion->rule='required';

		$edit->codigo = new inputField("(<#o#>) Codigo", "codigo_<#i#>");
		$edit->codigo->rel_id ='itsuminr';
		//$edit->codigo->rule='callback_repetido|required';
		$edit->codigo->size=6;
		//$edit->codigo->append('<img src="/tortuga/assets/default/images/system-search.png"  alt="Busqueda de Articulos" title="Busqueda de Articulos" border="0" onclick="modbusdepen(<#i#>)"/>');
		$edit->codigo->db_name='codigo';
		$edit->codigo->append($btn);
    
		$edit->descripcion = new inputField("Descripci&oacute;n", "descripcion_<#i#>");
		$edit->descripcion->db_name='descripcion';
		$edit->descripcion->rel_id ='itsuminr';
		$edit->descripcion->size     =40;
		$edit->descripcion->readonly =true;
//		$edit->descripcion->pointer=true;

		$edit->cantidad = new inputField("Cantidad", "cantidad_<#i#>");
		$edit->cantidad->rule     ='required|numeric';
		$edit->cantidad->css_class='inputnum';
		$edit->cantidad->db_name  ='cantidad';
		$edit->cantidad->rel_id   ='itsuminr';
		$edit->cantidad->size     =10;
		$edit->cantidad->onchange ='cal_cant(<#i#>);';

		$edit->precio = new inputField("(<#o#>) Precio", "precio_<#i#>");
		$edit->precio->css_class='inputnum';
		$edit->precio->db_name  ='precio';
		$edit->precio->rel_id   ='itsuminr';
		$edit->precio->rule     ='numeric|required';
		$edit->precio->onchange ='cal_total(<#i#>);';
		$edit->precio->size     =10;
		
		
		$edit->total = new inputField("(<#o#>) Total", "total_<#i#>");		
		$edit->total->db_name  ='total';
		$edit->total->rel_id   ='itsuminr';
		$edit->total->rule     ='numeric';
		$edit->total->readonly =true;
		$edit->total->size     =10;
		
    
		$edit->tcantidad = new inputField("Cantidad total", "tcantidad");
		$edit->tcantidad->db_name  ='tcantidad';
		$edit->tcantidad->css_class='inputnum';
		$edit->tcantidad->readonly =true;
		$edit->tcantidad->rule     ='numeric';
		$edit->tcantidad->size     =10;
		
		$edit->ttotal = new inputField("Precio total", "ttotal");
		$edit->ttotal->db_name  ='total';
		$edit->ttotal->css_class='inputnum';
		$edit->ttotal->readonly =true;
		$edit->ttotal->rule     ='numeric';
		$edit->ttotal->size     =10;
		
		$status=$edit->get_from_dataobjetct('status');
		if($status=='P'){
			$edit->buttons("save","modify","delete");
			
			$action = "javascript:window.location='" .site_url($this->url.'actualizar/'.$edit->rapyd->uri->get_edited_id()). "'";
			$edit->button_status("btn_status",'Marcar Como terminada',$action,"TR","show");
		}elseif($status=='C'){
			$action = "javascript:btn_anula('" .$edit->rapyd->uri->get_edited_id()."')";
			$edit->button_status("btn_rever",'Reversar',$action,"TR","show");
		}

		$edit->buttons("add", "save", "undo",  "back","add_rel");
		$edit->build();

		$smenu['link']   =barra_menu('193');
		$data['smenu']   = $this->load->view('view_sub_menu', $smenu,true);
		$conten["form"]  =&  $edit;
		$data['content'] = $this->load->view('view_suminr', $conten,true); 
		//$data['content'] = $edit->output;
		$data['title']   = "Nota de Recepci&oacute;n";
		$data["head"]    = script('jquery.js').script('jquery-ui.js').script('plugins/jquery.numeric.pack.js').script('plugins/jquery.floatnumber.js').script('plugins/jquery.meiomask.js').style('vino/jquery-ui.css').$this->rapyd->get_head();
		$this->load->view('view_ventanas', $data);
	}
	
	function repetido($codigo){
		if(isset($this->__rpartida)){
			if(in_array($partida, $this->__rpartida)){
				$this->validation->set_message('repetido',"El rublo %s ($partida) esta repetido");
				return false;	
			}
		}
		$this->__rpartida[]=$partida;
		return true;
	}
	
	function _valida($do){
		$error='';
		$do->set('status','P');
		$tcantidad=$total=0;
		for($i=0;$i < $do->count_rel('itsuminr');$i++){
			$codigo=$do->get_rel('itsuminr','codigo',$i);
			$cantidad=$do->get_rel('itsuminr','cantidad',$i);
			$precio=$do->get_rel('itsuminr','precio',$i);
			$tcantidad+=$cantidad;
			
			$total+=round($precio*$cantidad,2);
			$do->set_rel('itsuminr','total',round($precio*$cantidad,2),$i);
			
			$codigoe=$this->db->escape($codigo);
			$c=$this->datasis->dameval("SELECT COUNT(*) FROM sumi WHERE codigo=$codigoe");
			if(!($c>0))
			$error.="El $codigoe no existe";
		}
		
		
		$do->set('total',$total); 
		$do->set('tcantidad',$tcantidad);
		
		if(!empty($error)){
			$do->error_message_ar['pre_ins']=$error;
			$do->error_message_ar['pre_upd']=$error;
			return false;
		}
	}

	function actualizar($id){
		$this->_actsumi($id,'+','C');
	}
	
	function reversar($id){
		$this->_actsumi($id,'-','P');
	}
	
	function _actsumi($id,$oper,$status){
		$this->rapyd->load('dataobject');
		
		$error="";
		$do = new DataObject("suminr");
		$do->rel_one_to_many('itsuminr', 'itsuminr', array('numero'=>'numero'));
		$do->load($id);
		$caub = $do->get('caub');
		$caube=$this->db->escape($caub);
		$sta    = $do->get('sta');
		
		if($sta!=$status){
			for($i=0;$i < $do->count_rel('itsuminr');$i++){
				$codigo  = $do->get_rel('itsuminr','codigo'   ,$i);
				$cantidad= $do->get_rel('itsuminr','cantidad' ,$i);
				$precio  = $do->get_rel('itsuminr','precio'   ,$i);
				
				$cantidad=1*$cantidad;
				$codigo=$this->db->escape($codigo);
				
				$this->db->query("INSERT IGNORE INTO su_itsumi (`codigo`,`alma`) value ($codigo,$caube)");
				
				if(is_numeric($cantidad)){
					$this->db->query("UPDATE sumi SET existen=existen $oper $cantidad,pond=pond $oper ($precio*$cantidad) WHERE codigo=$codigo");
					$this->db->simple_query("UPDATE su_itsumi SET cantidad=cantidad $oper $cantidad WHERE codigo=$codigo AND alma=$caube");
				}else
					$error.='La cantidad no es numerica';
				//echo "UPDATE sumi SET existen=existen $oper $cantidad,pond=pond $oper $precio WHERE codigo=$codigo";
				//exit($precio);
			}
			
			logusu('suminr',"Marco nota de recepcion Nro $id como $status");
		}else{
			$error.="No se puede realizar la operacion para la nota de recepcion";
		}
		
		if(empty($error)){
			$do->set('status',$status);
			$do->save();
			logusu('suminr',"Marco nota de recepcion Nro $id como $status");
			redirect($this->url."/dataedit/show/$id");
		}else{
			logusu('sumine',"Marco nota de recepcion Nro $id como $status . con ERROR:$error");
			$data['content'] = $error.anchor($this->url."/dataedit/show/$id",'Regresar');
			$data['title']   = $this->tits;
			$data["head"]    = $this->rapyd->get_head();
			$this->load->view('view_ventanas', $data);
		}
	}
	
	function instalar(){
		$this->db->simple_query("ALTER TABLE `suminr` ADD `status` CHAR( 1 ) NOT NULL DEFAULT 'P'");
		$this->db->simple_query("ALTER TABLE `suminr`  ADD COLUMN `conc` INT NULL DEFAULT NULL");
		$query="ALTER TABLE `suminr` ADD COLUMN `estampa` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP ";
		$this->db->simple_query($query);
	}
}
?>
