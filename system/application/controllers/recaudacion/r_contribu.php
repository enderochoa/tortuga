<?php
class r_contribu extends Controller {
	var $titp='Contribuyentes';
	var $tits='Contribuyente';
	var $url ='recaudacion/r_contribu/';
	function r_contribu(){
		parent::Controller();
		$this->load->library("rapyd");
		$this->datasis->modulo_id(397,1);
	}
	function index(){
		redirect($this->url."filteredgrid");
	}
	function filteredgrid(){
		$this->rapyd->load('datafilter','datagrid');

		$filter = new DataFilter($this->titp, 'r_contribu');
		$filter->db->select(array("r_contribu.id","rifci","nombre","telefono","patente","nro","id_negocio","objeto","archivo","r_negocio.descrip as negocio"));
		$filter->db->join("r_negocio","r_contribu.id_negocio=r_negocio.id","LEFT");

		$filter->id = new inputField('Ref.','id');
		$filter->id->rule      ='max_length[11]';
		$filter->id->size      =10;
		$filter->id->db_name   ='r_contribu.id';

		$filter->rifci = new inputField('Rif/CI','rifci');
		$filter->rifci->size      =15;
		$filter->rifci->db_name   ='r_contribu.rifci';

		$filter->nombre = new inputField('Nombre','nombre');
		$filter->nombre->size      =40;

		$filter->telefono = new inputField('Telefono','telefono');
		$filter->telefono->size      =40;
		
		$filter->razon = new inputField("Razon Social", 'razon');
		$filter->razon->size      = 60;
		$filter->razon->maxlenght = 100;

		$filter->id_parroquia = new dropDownField('Parroquia','id_parroquia');
		$filter->id_parroquia->option('','');
		$filter->id_parroquia->options("SELECT id,nombre FROM vi_parroquia ORDER BY nombre");
		$filter->id_parroquia->group = "Datos De Ubicacion";

		$filter->id_zona = new dropDownField('Zona','id_zona');
		$filter->id_zona->option('','');
		$filter->id_zona->options("SELECT id,descrip FROM r_zona ORDER BY descrip");
		$filter->id_zona->group = "Datos De Ubicacion";

		$filter->dir1 = new inputField('Direcci&oacute;n 1','dir1');
		$filter->dir1->rule='max_length[255]';
		$filter->dir1->size =40;
		$filter->dir1->maxlength =255;
		$filter->dir1->append("Urbanizacion, Barrio, Sector");
		$filter->dir1->group = "Datos De Ubicacion";

		$filter->dir2 = new inputField('Direcci&oacute;n 2','dir2');
		$filter->dir2->rule='max_length[255]';
		$filter->dir2->size =40;
		$filter->dir2->maxlength =255;
		$filter->dir2->append("Calle, avenida");
		$filter->dir2->group = "Datos De Ubicacion";

		$filter->dir3 = new inputField('Direcci&oacute;n 3','dir3');
		$filter->dir3->rule='max_length[255]';
		$filter->dir3->size =40;
		$filter->dir3->maxlength =255;
		$filter->dir3->append("Con Calle o avenida");
		$filter->dir3->group = "Datos De Ubicacion";

		$filter->dir4 = new inputField('Direcci&oacute;n 4','dir4');
		$filter->dir4->rule='max_length[255]';
		$filter->dir4->size =40;
		$filter->dir4->maxlength =255;
		$filter->dir4->append("Casa #, o apto #");
		$filter->dir4->group = "Datos De Ubicacion";

		$filter->patente = new dropDownField('Tiene patente','patente');
		$filter->patente->option('' ,'');
		$filter->patente->option('N','N');
		$filter->patente->option('S','S');
		$filter->patente->group="Datos de Patente";

		$filter->nro = new inputField('Patente Numero','nro');
		$filter->nro->rule='max_length[10]';
		$filter->nro->size =12;
		$filter->nro->maxlength =10;
		$filter->nro->group="Datos de Patente";

		$filter->id_negocio = new dropdownField('Negocio','id_negocio');
		$filter->id_negocio->option('','');
		$filter->id_negocio->options("SELECT id,descrip FROM r_negocio ORDER BY descrip");
		$filter->id_negocio->style='width:400px';
		$filter->id_negocio->group="Datos de Patente";

		$filter->objeto = new inputField('Objeto','objeto');
		$filter->objeto->size = 40;

		$filter->archivo = new inputField('Archivo','archivo');
		$filter->archivo->size      =40;

		$filter->buttons('reset', 'search');
		$filter->build();

		$uri = anchor($this->url.'dataedit/show/<raencode><#id#></raencode>','<#id#>');

		$grid = new DataGrid('');
		$grid->order_by('r_contribu.id','desc');
		$grid->per_page = 40;

		$grid->column_orderby('Ref.'            ,"$uri",'id','align="left"');
		$grid->column_orderby('Rif/CI'          ,"rifci",'rifci','align="left"');
		$grid->column_orderby('Nombre'          ,"nombre",'nombre','align="left"');
		$grid->column_orderby('Telefono'        ,"telefono",'telefono','align="left"');
		$grid->column_orderby('Patente'         ,"patente",'patente','align="left"');
		$grid->column_orderby('Patente Nro'     ,"nro",'nro','align="left"');
		$grid->column_orderby('Negocio'         ,"negocio",'negocio','align="left"');
		$grid->column_orderby('Objeto'          ,"objeto",'objeto','align="left"');
		$grid->column_orderby('Archivo'         ,"archivo",'archivo','align="left"');

		if($this->datasis->puede(473))
		$grid->add($this->url.'dataedit/create');
		$grid->build();

		$data['filtro']  = $filter->output;
		$data['content'] = $grid->output;
		$data['head']    = $this->rapyd->get_head().script('jquery.js');
		$data['title']   = $this->titp;
		$this->load->view('view_ventanas', $data);

	}
	
	function dataedit($status='',$c=''){
		$this->rapyd->load('datadetails','dataobject');
		
		$modbus2=array(
			'tabla'   =>'r_contribu',
			'columnas'=>array(
				'id'    =>'Ref.',
				'rifci' =>'Rif/CI',
				'nombre'=>'Nombre'
				),
			'filtro'  =>array(
				'id'    =>'Ref.',
				'rifci' =>'Rif/CI',
				'nombre'=>'Nombre'
			),
			'retornar'=>array(
				'id'=>'id_repre',
				'nombre'=>'nombrep'
			),
			'titulo'  =>'Buscar Contribuyente',
			'where'   =>'rifci LIKE "V%" '
		);

		$button  = $this->datasis->modbus($modbus2,'r_contribu2');
		
		$modbus=array(
			'tabla'   =>'r_contribu',
			'columnas'=>array(
				'id'    =>'Ref.',
				'rifci' =>'Rif/CI',
				'nombre'=>'Nombre'
				),
			'filtro'  =>array(
				'id'    =>'Ref.',
				'rifci' =>'Rif/CI',
				'nombre'=>'Nombre'
			),
			'retornar'=>array(
				'id'     =>'itid_contribuit_<#i#>',
				'rifci' =>'itrifcipit_<#i#>',
				'nombre'=>'itnombrepit_<#i#>',
			),
			'p_uri'=>array(
				4=>'<#i#>'
			),
			'titulo'  =>'Buscar Contribuyente',
			'script'  =>array('post_modbus_socios(<#i#>)'),
			//'where'   =>'rifci LIKE "V%" '
		);

		$buttonsocios  = $this->datasis->p_modbus($modbus,'<#i#>');
		
		$modbusnegocio=array(
			'tabla'   =>'r_negocio',
			'columnas'=>array(
				'id'       =>'Ref.',
				'descrip'  =>'Descripcion',
				'monto'    =>'Monto',
				'monto2'   =>'Monto2'  ,
				'aforo'    =>'Aforo'   ,
				'mintribu' =>'Minimo Tributable'
				),
			'filtro'  =>array(
				'id'       =>'Ref.',
				'descrip'  =>'Descripcion',
				'monto'    =>'Monto',
				'monto2'   =>'Monto2'  ,
				'aforo'    =>'Aforo'   ,
				'mintribu' =>'Minimo Tributable'
			),
			'retornar'=>array(
				'id'=>'id_negocio',
				'descrip'=>'negociop'
			),
			'titulo'  =>'Buscar Negocio'
		);

		$buttonnegocio  = $this->datasis->modbus($modbusnegocio);
		
		$do = new DataObject("r_contribu");
		$do->rel_one_to_many('r_contribuit'   , 'r_contribuit'   , array('id' =>'id_contribu'));
		$do->pointer('r_contribu b' ,'r_contribu.id_repre=b.id',"b.nombre nombrep","LEFT");
		$do->pointer('r_negocio' ,'r_contribu.id_negocio=r_negocio.id',"r_negocio.descrip negociop","LEFT");
		$do->rel_pointer('r_contribuit','r_contribu c' ,'r_contribuit.id_contribuit=c.id',"c.rifci rifcipit,c.nombre nombrepit","LEFT");

		$edit = new DataDetails($this->tits, $do);

		$edit->back_url = site_url($this->url."filteredgrid");

		$edit->pre_process('insert' ,'_valida');
		$edit->pre_process('update' ,'_valida');
		$edit->post_process('insert','_post_insert');
		$edit->post_process('update','_post_update');
		$edit->post_process('delete','_post_delete');

		$edit->id = new inputField('id','id');
		$edit->id->rule='max_length[11]';
		$edit->id->size =13;
		$edit->id->maxlength =11;
		$edit->id->mode='autohide';
		$edit->id->when=array('show','modify');
		
		$edit->tipo = new dropDownField('Tipo','tipo');
		$edit->tipo->option("S","Sencillo"         );
		$edit->tipo->option("M","Multiple"         );
		$edit->tipo->option("F","Firma Personal o Sucursales"   );
		$edit->tipo->append("Permite Guardar el RIF varias veces para uso de FIRMAS PERSONALES, la opcion MULTIPLE es utilizada para registros con varios dueños (no requiere RIF) ");
		$edit->tipo->style ="width:150px";

		$edit->rifci = new inputField("RIF / C&eacute;dula", 'rifci');
		$edit->rifci->size      = 15;
		$edit->rifci->maxlenght = 12;
		//$edit->rifci->rule      = "required";
		if($status=='create')
		$edit->rifci->value     = $c;
		$edit->rifci->append("Sin puntos ni guiones, Ejemplo:V18055344 o J401042970");
		
		$edit->nombre = new inputField("Nombre", 'nombre');
		$edit->nombre->size      = 60;
		$edit->nombre->maxlenght = 100;
		$edit->nombre->rule      = "required";
	
		$edit->telefono = new inputField("Telefono", 'telefono');
		$edit->telefono->size      = 40;
		$edit->telefono->maxlenght = 50;
		$edit->telefono->css_class ='inputonlynum';
		if($this->datasis->traevalor('R_CONTRIBU_OBLIGA_TELEFONO','N'));
		$edit->telefono->rule      = "required";
		
		$edit->email = new inputField("Correo Electronico", 'email');
		$edit->email->size      = 40;
		$edit->email->maxlenght = 50;
		if($this->datasis->traevalor('R_CONTRIBU_OBLIGA_EMAIL','N')=='S')
		$edit->email->rule      = "required";
		//$edit->email->css_class ='inputonlynum';
		
		$edit->activo = new dropDownField('Activo ','activo');
		$edit->activo->option("S","SI"   );
		$edit->activo->option("N","NO"   );
		$edit->activo->style ="width:150px";
		
		/*
		$edit->rif = new inputField("RIF Firma Personal", 'rif');
		$edit->rif->size      = 15;
		$edit->rif->maxlenght = 12;
		//$edit->rif->rule      = "required";
		$edit->rif->append("Sin puntos ni guiones, Ejemplo:V180553440");		
		
		$edit->nomfis = new inputField("Nombre Firma Personal", 'nomfis');
		$edit->nomfis->size      = 60;
		$edit->nomfis->maxlenght = 100;
		//$edit->nomfis->rule      = "required";
		*/
		
		$edit->id_parroquia = new dropDownField('Parroquia','id_parroquia');
		//$edit->id_parroquia->rule='required';
		$edit->id_parroquia->option("","");
		$edit->id_parroquia->options("SELECT id,nombre FROM vi_parroquia ORDER BY nombre");
		$edit->id_parroquia->group = "Datos De Ubicacion";
		if($this->datasis->traevalor('R_CONTRIBU_OBLIGA_PARROQUIA','N')=='S')
		$edit->id_parroquia->rule      = "required";

		$edit->id_zona = new dropDownField('Zona','id_zona');
//		$edit->id_zona->rule='required';
		$edit->id_zona->option("","");
		$edit->id_zona->options("SELECT id,descrip FROM r_zona ORDER BY descrip");
		$edit->id_zona->group = "Datos De Ubicacion";
		if($this->datasis->traevalor('R_CONTRIBU_OBLIGA_ZONA','N')=='S')
		$edit->id_zona->rule      = "required";

		$edit->dir1 = new inputField('Direcci&oacute;n 1','dir1');
		$edit->dir1->rule='max_length[255]';
		$edit->dir1->size =40;
		$edit->dir1->maxlength =255;
		$edit->dir1->append("Urbanizacion, Barrio, Sector");
		$edit->dir1->group = "Datos De Ubicacion";
		if($this->datasis->traevalor('R_CONTRIBU_OBLIGA_DIR1','N')=='S')
		$edit->dir1->rule      = "required";

		$edit->dir2 = new inputField('Direcci&oacute;n 2','dir2');
		$edit->dir2->rule='max_length[255]';
		$edit->dir2->size =40;
		$edit->dir2->maxlength =255;
		$edit->dir2->append("Calle, avenida, carrera");
		$edit->dir2->group = "Datos De Ubicacion";
		if($this->datasis->traevalor('R_CONTRIBU_OBLIGA_DIR2','N')=='S')
		$edit->dir2->rule      = "required";

		$edit->dir3 = new inputField('Direcci&oacute;n 3','dir3');
		$edit->dir3->rule='max_length[255]';
		$edit->dir3->size =40;
		$edit->dir3->maxlength =255;
		$edit->dir3->append("Con Calle, avenida o carrera");
		$edit->dir3->group = "Datos De Ubicacion";
		if($this->datasis->traevalor('R_CONTRIBU_OBLIGA_DIR3','N')=='S')
		$edit->dir3->rule      = "required";

		$edit->dir4 = new inputField('Direcci&oacute;n 4','dir4');
		$edit->dir4->rule='max_length[255]';
		$edit->dir4->size =40;
		$edit->dir4->maxlength =255;
		$edit->dir4->append("Casa #, o apto #");
		$edit->dir4->group = "Datos De Ubicacion";
		if($this->datasis->traevalor('R_CONTRIBU_OBLIGA_DIR4','N')=='S')
		$edit->dir4->rule      = "required";
		
		$edit->id_negocio = new inputField('Negocio','id_negocio');
		//$edit->id_negocio->option('','');
		//$edit->id_negocio->options("SELECT id,descrip FROM r_negocio ORDER BY descrip");
		$edit->id_negocio->size='5';
		$edit->id_negocio->group="Datos de Patente";
		$edit->id_negocio->append($buttonnegocio);
		$edit->id_negocio->readonly=true;
		
		$edit->negociop = new inputField('Negocio','negociop');
		$edit->negociop->size='60';
		$edit->negociop->group="Datos de Patente";
		$edit->negociop->readonly=true;
		$edit->negociop->pointer=true;
		$edit->negociop->in      ="id_negocio";
		
		$edit->observa = new textAreaField('Observaci&oacute;n','observa');
		$edit->observa->rows =2;
		$edit->observa->cols =40;
		//$edit->observa->group="Datos de Patente";
		

		if($this->datasis->puede(398)){

			$edit->patente = new dropDownField('Posee patente','patente');
			$edit->patente->option('N','NO');
			$edit->patente->option('S','SI');
			$edit->patente->group="Datos de Patente";

			$edit->nro = new inputField('Patente Numero','nro');
			$edit->nro->rule='max_length[10]';
			$edit->nro->size =12;
			$edit->nro->maxlength =10;
			$edit->nro->group="Datos de Patente";
			
			$edit->p_tipo = new dropDownField('Tipo de Actividad','p_tipo');
			$edit->p_tipo->option("","");
			$edit->p_tipo->option("Industrial","Industrial");
			$edit->p_tipo->option("Comercial" ,"Comercial" );
			$edit->p_tipo->option("Servicio"  ,"Servicio"  );
			$edit->p_tipo->group = "Datos de Patente";

			$edit->objeto = new textAreaField('Objeto','objeto');
			$edit->objeto->rows =2;
			$edit->objeto->cols =40;
			$edit->objeto->group="Datos de Patente";

			$edit->id_repre = new inputField('Representante','id_repre');
			$edit->id_repre->rule='max_length[11]';
			$edit->id_repre->size =13;
			$edit->id_repre->maxlength =11;
			$edit->id_repre->group="Datos de Patente";
			$edit->id_repre->readonly=true;
			
			$edit->nombrep = new inputField('Archivo','nombrep');
			$edit->nombrep->size =40;
			$edit->nombrep->readonly=true;
			$edit->nombrep->pointer =true;
			$edit->nombrep->in='id_repre';
			$edit->nombrep->append($button);
			$edit->nombrep->group="Datos de Patente";

			$edit->archivo = new inputField('Archivo','archivo');
			$edit->archivo->rule='max_length[50]';
			$edit->archivo->size =20;
			$edit->archivo->maxlength =50;
			$edit->archivo->group="Datos de Patente";
			
			$edit->id_sector = new dropDownField('Sector','id_sector');
			//$edit->id_sector->rule='required';
			$edit->id_sector->option("","");
			$edit->id_sector->options("SELECT id,descrip FROM r_sector ORDER BY descrip");
			$edit->id_sector->group = "Datos de Patente";
			
			$edit->reg_nro = new inputField('Registro Numero','reg_nro');
			$edit->reg_nro->rule='max_length[10]';
			$edit->reg_nro->size =12;
			$edit->reg_nro->maxlength =10;
			$edit->reg_nro->group="Datos de Registro";
			
			$edit->reg_tomo = new inputField('Registro Tomo','reg_tomo');
			$edit->reg_tomo->rule='max_length[10]';
			$edit->reg_tomo->size =12;
			$edit->reg_tomo->maxlength =10;
			$edit->reg_tomo->group="Datos de Registro";
			
			$edit->reg_fecha = new dateOnlyField('Registro Fecha','reg_fecha');
			$edit->reg_fecha->rule='chfecha';
			$edit->reg_fecha->size =10;
			$edit->reg_fecha->maxlength =8;
			//$edit->reg_fecha->insertValue=date('Y-m-d');
			$edit->reg_fecha->group="Datos de Registro";
		}
		
		
		$edit->itid_contribuit = new inputField('Contribuyente','itid_contribuit_<#i#>');
		$edit->itid_contribuit->rule='max_length[11]';
		$edit->itid_contribuit->size =3;
		$edit->itid_contribuit->maxlength =11;
		$edit->itid_contribuit->db_name='id_contribuit';
		$edit->itid_contribuit->rel_id ='r_contribuit';
		$edit->itid_contribuit->type ='inputhidden';
		
		$edit->itrifcipit = new inputField('Nombre','itrifcipit_<#i#>');
		$edit->itrifcipit->size =20;
		//$edit->itrifcipit->readonly=true;
		$edit->itrifcipit->pointer =true;
		$edit->itrifcipit->rel_id ='r_contribuit';
		$edit->itrifcipit->db_name='rifcipit';
		$edit->itrifcipit->append($buttonsocios);
		
		$edit->itnombrepit = new inputField('Nombre','itnombrepit_<#i#>');
		$edit->itnombrepit->size =60;
		//$edit->itnombrepit->readonly=true;
		$edit->itnombrepit->pointer =true;
		$edit->itnombrepit->rel_id ='r_contribuit';
		$edit->itnombrepit->db_name='nombrepit';
		//$edit->itnombrepit->type ='inputhidden';


		if($this->datasis->puede(473))
		$edit->buttons('add','add_rel','save');
		
		if($this->datasis->puede(474))
		$edit->buttons('modify','save');
		
		if($this->datasis->puede(475))
		$edit->buttons('delete');
		
		$edit->buttons('undo', 'back');
		$edit->build();
		
		$smenu['link']   =barra_menu('G13');
		$data['smenu']   = $this->load->view('view_sub_menu', $smenu,true);
		$conten["form"]  =&$edit;
		$data['content'] = $this->load->view('recaudacion/r_contribu', $conten,true);
		$data['title']   = $this->tits;
		$data["head"]    = $this->rapyd->get_head().script('jquery.js').script('jquery-ui.js').script("plugins/jquery.numeric.pack.js").script('plugins/jquery.meiomask.js').style('vino/jquery-ui.css');
		$this->load->view('view_ventanas', $data);
	}
	
	function _valida($do){
		$error='';
		$rifci  =$do->get('rifci');
		$rif    =$do->get('rif');
		$rifcie =$this->db->escape($rifci);
		$id     =$do->get('id');
		$tipo   =$do->get('tipo');
		
		if($tipo=='S' ){
			if(empty($rifci))
				$error.="ERROR. El Rif/CI es Obligatorio (No puede quedar en blanco)";
			$query="SELECT COUNT(*) FROM r_contribu WHERE rifci=$rifcie ";
			if($id>0)
			$query.=" AND id<>$id ";
			$chek=$this->datasis->dameval($query);
			if($chek > 0){
				$error.='Error. El Contribuyente ya se Encuentra Registrado</br>';
			}
		}
			
		if($tipo=='F'){
			if(strlen($rifci)!=10)
			$error.="ERROR. Si es una Firma Personal debe colocar el RIF, ejemplo:V180553440</br>";
			
			$cant_itcontribu =0;
			
			for($i=0;$i<$do->count_rel('r_contribuit');$i++){
				$id_contribu = $do->get_rel('r_contribuit','id_contribuit' ,$i);
				
				if($id_contribu>0)
					$cant_itcontribu++;
			}
			if($cant_itcontribu!=1)
				$error.="ERROR. Una Firma Personal debe tener un contribuyente Asociado</br>";
		}
			
		if($tipo=='M'){
			$do->set('rifci','');
			$cant_itcontribu =0;
			
			for($i=0;$i<$do->count_rel('r_contribuit');$i++){
				$id_contribu = $do->get_rel('r_contribuit','id_contribuit' ,$i);
				if($id_contribu>0)
					$cant_itcontribu++;
			}
			if($cant_itcontribu<=1)
				$error.="ERROR. Un Contribuyente Multiple debe tener al menos debe poseer dos contribuyentes asociados</br>";
		}
		
		if(empty($error) && $tipo!='M'){
			if(!in_array(substr($rifci,0,1),array('V','E','G','J','M')))
				$error.="ERROR. El Rif/CI debe Comenzar por V,E,G,J o M (M es utilizado para contribuyentes multiples)</br>";
				
			if(!preg_match("/^[0-9]+$/",substr($rifci,1)))
				$error.="ERROR. El Rif/CI debe tener el siguiente formato LETRA+SOLONUMEROS ejemplo: V18055344 o J401042970</br>";
		}
		
		if(!empty($error)){
			$do->error_message_ar['pre_ins']='<div class="error">'.$error.'</div>';
			$do->error_message_ar['pre_upd']='<div class="error">'.$error.'</div>';
			return false;
		}
	}
	
	function traeseniat(){
		$rifci = $this->input->post("rifcedula");
		if(substr($rifci,0,1)=='V')
		$rifci=$this->citorif($rifci);
		
		$response_json=array('code_result'=>'', 'seniat'=>array());
		
		if( function_exists('curl_init')){ // Comprobamos si hay soporte paracURL
				$url="http://contribuyente.seniat.gob.ve/getContribuyente/getrif?rif=".$rifci;
			   $ch = curl_init();
			   curl_setopt($ch, CURLOPT_URL, $url);
			   curl_setopt($ch, CURLOPT_TIMEOUT, 30);
			   curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
			   curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
			   $resultado = curl_exec ($ch);
			   if($resultado){
					   try{
							   if(substr($resultado,0,1)!='<')
										throw new Exception($resultado);
							   $xml = simplexml_load_string($resultado);
							   if(!is_bool($xml)){
									   $elements=$xml->children('rif');
									   $seniat=array();
									   $response_json['code_result']=1;
									   foreach($elements as $indice => $node){
											   $index=strtolower($node->getName());
											   $seniat[$index]=(string)$node;
									   }
									   $response_json['seniat']=$seniat;
							   }
					   }catch(Exception $e){
							   $result=explode(' ', $resultado, 2);
							   $response_json['code_result']=(int) $result[0];
					   }
			   }else
					   $response_json['code_result']=0;//No hay conexion a internet
		}else
			   $response_json['code_result']=-1;//No hay soporte a curl_php
		
		echo json_encode($response_json);
	}
	
	function damerne(){
		$cedula = $this->input->post("cedula");
		$cedula = str_replace('.','',$cedula);
		$cedula = str_replace('-','',$cedula);
		$cedula = str_replace('V','',$cedula);
		
			
		$arreglo=array();
		if(is_numeric($cedula)){
			
			$query  ="select CONCAT_WS(' ',
			rne.primer_nombre,
			rne.segundo_nombre,
			rne.primer_apellido,
			rne.segundo_apellido) as nombre,
			nacionalidad
			from rne.rne
			 where cedula=$cedula";
			
			$mSQL   = $this->db->query($query);
			$arreglo= $mSQL->result_array($query);
			foreach($arreglo as $key=>$value)
				foreach($value as $key2=>$value2) 
				$arreglo[$key][$key2] = ($value2);
		}
		echo json_encode($arreglo);
	}
	
	function damecontribu(){
		$rifci = $this->input->post('rifcedula');
		$rifcie=$this->db->escape($rifci);
		
		$query  ="SELECT id,nombre FROM r_contribu
		 WHERE rifci=$rifcie";
		
		$mSQL   = $this->db->query($query);
		$arreglo= $mSQL->result_array($query);
		foreach($arreglo as $key=>$value)
			foreach($value as $key2=>$value2) 
			$arreglo[$key][$key2] = ($value2);
		
		echo json_encode($arreglo);
	}
	
	function damecontribuporid(){
		$id = $this->input->post('id');
		$ide=$this->db->escape($id);
		
		$query  ="SELECT id,nombre,rifci FROM r_contribu
		 WHERE id=$ide";
		
		$mSQL   = $this->db->query($query);
		$arreglo= $mSQL->result_array($query);
		foreach($arreglo as $key=>$value)
			foreach($value as $key2=>$value2) 
			$arreglo[$key][$key2] = ($value2);
		
		echo json_encode($arreglo);
	}
	
	function citorif($rif) {
		return citorif($rif);
    }
    
    function damecitorif($rif) {
		echo $this->citorif($rif);
	}
    
    function autocompleteui(){
		$query  ="SELECT id,nombre, rifci FROM r_contribu ORDER BY rifci";
		$mSQL   = $this->db->query($query);
		$arreglo= $mSQL->result_array($query);
		foreach($arreglo as $key=>$value)
			foreach($value as $key2=>$value2) 
			$arreglo[$key][$key2] = ($value2);
			
		echo json_encode($arreglo);
	}
	
	function autocompleteui_nombre(){
		$term   = $this->input->post('term');
		$query  ="SELECT id,nombre, rifci FROM r_contribu WHERE nombre LIKE '%$term%' ORDER BY nombre NOT LIKE '$term%',nombre ";
		$mSQL   = $this->db->query($query);
		$arreglo= $mSQL->result_array($query);
		foreach($arreglo as $key=>$value)
			foreach($value as $key2=>$value2) 
			$arreglo[$key][$key2] = ($value2);
			
		echo json_encode($arreglo);
	}
	
	function autocompleteui_rifci(){
		$term   = $this->input->post('term');
		$query  ="SELECT id,nombre, rifci FROM r_contribu WHERE rifci LIKE '%$term%' ORDER BY rifci";
		$mSQL   = $this->db->query($query);
		$arreglo= $mSQL->result_array($query);
		foreach($arreglo as $key=>$value)
			foreach($value as $key2=>$value2) 
			$arreglo[$key][$key2] = ($value2);
			
		echo json_encode($arreglo);
	}
	
	function resumen_contribu($id=null){
		$this->rapyd->load('dataobject','datagrid');
		
		$atts = array(
		'width'     =>'1024',
		'height'    =>'720',
		'scrollbars'=>'yes',
		'status'    =>'yes',
		'resizable' =>'yes',
		'screenx'   =>'5',
		'screeny'   =>'5',
		'id'        =>'vehiculo' 
		);
		
		$data = $this->db->query("SELECT id,catastro,parroquia,zona,dir1,dir2,dir3,dir4,mt2,techo,techodecrip,monto,id_contribu,direccion,id_parroquia,id_zona,zona_monto,id_clase,clase,clase_monto,IF(tipoi='V','Vivienda',IF(tipoi='I','Terreno',IF(tipoi='C','Comercio',IF(tipoi='N','Industria',tipoi)))) tipoi,id_clasea,clasea,clasea_monto  FROM r_v_inmueble WHERE id_contribu=".$this->db->escape($id));
		$data = $data->result_array();
		$grid = new DataGrid("Inmuebles",$data);
		$grid->per_page = 3000;
		
		$uri = anchor_popup('recaudacion/r_inmueble/dataedit/show/<raencode><#id#></raencode>','<#id#>',$atts);

		$grid->column('Ref.'           ,"$uri"                               ,'id','align="left"');
		$grid->column('Cod. Catastro'  ,"catastro"                           ,'catastro','align="left"');
		$grid->column('Parroquia'      ,"parroquia"                          ,'id_parroquia','align="left"');
		$grid->column('Zona'           ,"zona"                               ,'id_zona','align="left"');
		$grid->column('Direccion 1'    ,"dir1"                               ,'dir1','align="left"');
		$grid->column('Direccion 2'    ,"dir2"                               ,'dir2','align="left"');
		$grid->column('Direccion 3'    ,"dir3"                               ,'dir3','align="left"');
		$grid->column('Direccion 4'    ,"dir4"                               ,'dir4','align="left"');
		$grid->column('Tipo'           ,"tipoi"                              ,'tipoi','align="left"');
		$grid->column('Mts2'           ,"<nformat><#mt2#></nformat>"         ,'mt2','align="right"');
		
		$grid->build();
		
		$data = $this->db->query("SELECT * FROM r_v_vehiculo WHERE id_contribu=".$this->db->escape($id));
		$data = $data->result_array();
		$grid2 = new DataGrid('Vehiculo',$data);
		$grid2->per_page = 3000;
		
		$uri = anchor_popup('recaudacion/r_vehiculo/dataedit/show/<raencode><#id#></raencode>','<#id#>',$atts);
	         
		$grid2->column('Ref.'           ,"$uri"       ,'id'        ,'align="left"');
		$grid2->column('Placa'          ,"placa"      ,'placa'     ,'align="left"');
		$grid2->column('A&ntilde;o'     ,"ano"        ,'ano'       ,'align="left"');
		$grid2->column('Color'          ,"color"      ,'color'     ,'align="left"');
		$grid2->column('Marca'          ,"marca"      ,'id_marca'  ,'align="left"');
		$grid2->column('Modelo'         ,"modelo"     ,'id_modelo' ,'align="left"');
		$grid2->column('Tipo'           ,"tipo"       ,'id_tipo'   ,'align="left"');
		$grid2->column('Clase'          ,"clase"      ,'id_tipo'   ,'align="left"');

		$grid2->build();
		
		
		$idsi=$this->datasis->dameval("SELECT GROUP_CONCAT(id) FROM ( SELECT id FROM r_v_inmueble WHERE id_contribu=".$this->db->escape($id)." LIMIT 1000 )todo");
		$idsv=$this->datasis->dameval("SELECT GROUP_CONCAT(id) FROM ( SELECT id FROM r_v_vehiculo WHERE id_contribu=".$this->db->escape($id)." LIMIT 1000 )todo");

		$query="SELECT  
		IF(frecuencia=1,'Año',IF(frecuencia=2,'Semestre',IF(frecuencia=3,'Trimestre',IF(frecuencia=4,'MES','')))) frecuencia
		,freval
		,r_recibo.id,numero, r_recibo.fecha,r_reciboit.id_concit, denomi,ano,v_placa,i_catastro,observa, (r_reciboit.monto) monto,id_vehiculo,id_inmueble
		FROM r_reciboit 
		JOIN r_recibo ON r_reciboit.id_recibo=r_recibo.id 
		JOIN r_abonosit ON r_recibo.id=r_abonosit.recibo
		JOIN r_abonos ON r_abonos.id=r_abonosit.abono
		WHERE id_contribu=".$this->db->escape($id);
		
		if(strlen($idsv)>0)
		$query.=" OR id_vehiculo IN (".$idsv.")";
		if(strlen($idsi)>0)
		$query.=" OR id_inmueble IN (".$idsi.")";
		
		$query.=" ORDER BY fecha desc,ano,requiere";
		
		$data = $this->db->query($query);
		$data = $data->result_array();
		$grid3 = new DataGrid('Pagos',$data);
		$grid3->per_page = 3000;
		
		$uri = anchor_popup('recaudacion/r_recibo/dataedit/show/<raencode><#id#></raencode>','<#id#>',$atts);
	         
		$grid3->column('Ref.'           ,"$uri"                                        ,'align="left"');
		$grid3->column('Numero'         ,"<#numero#>"                                  ,'align="left"');
		$grid3->column('Fecha'          ,"<dbdate_to_human><#fecha#></dbdate_to_human>",'align="center"');
		$grid3->column('Ref Conc'       ,"id_concit"                                   ,'align="left"');
		$grid3->column('Denominacion'   ,"denomi"                                      ,'align="left"');
		$grid3->column('A&ntilde;o'     ,"ano"                                         ,'align="left"');
		$grid3->column('Frecuencia'     ,"frecuencia"                                  ,'align="left"');
		$grid3->column('Valor'          ,"freval"                                      ,'align="left"');
		$grid3->column('Ref. Vehi'      ,"id_vehiculo"                                 ,'align="left"');
		$grid3->column('Placa'          ,"v_placa"                                     ,'align="left"');
		$grid3->column('Ref. Inmu'      ,"id_inmueble"                                 ,'align="left"');
		$grid3->column('Catastro'       ,"i_catastro"                                  ,'align="left"');
		$grid3->column('Observacion'    ,"observa"                                     ,'align="left"');
		$grid3->column('Monto'          ,"<nformat><#monto#></nformat>"                ,'align="right"');

		$grid3->build();
		
		
		/* CUENTAS POR COBRAR*/
		$query="SELECT  
		IF(r_cxcit.frecuencia=1,'Año',IF(r_cxcit.frecuencia=2,'Semestre',IF(r_cxcit.frecuencia=3,'Trimestre',IF(r_cxcit.frecuencia=4,'MES','')))) frecuencia
		,r_cxcit.freval
		,r_cxc.id,numero, r_cxc.fecha,r_cxcit.id_concit, r_cxcit.denomi,r_cxcit.ano,r_cxcit.v_placa,r_cxcit.i_catastro,r_cxcit.observa, (r_cxcit.monto) monto,r_cxcit.id_vehiculo,r_cxcit.id_inmueble,r_reciboit.id_recibo
		FROM r_cxcit 
		JOIN r_cxc ON r_cxcit.id_cxc=r_cxc.id 
		LEFT JOIN r_reciboit ON r_cxcit.id=r_reciboit.id_cxcit
		WHERE r_reciboit.id IS NULL AND  (r_cxc.id_contribu=".$this->db->escape($id);
		
		if(strlen($idsv)>0)
		$query.=" OR r_cxcit.id_vehiculo IN (".$idsv.")";
		if(strlen($idsi)>0)
		$query.=" OR r_cxcit.id_inmueble IN (".$idsi.")";
		
		$query.=" ) ORDER BY r_cxc.fecha desc,r_cxcit.ano,r_cxcit.requiere";
		
		$data = $this->db->query($query);
		$data = $data->result_array();
		$grid5 = new DataGrid('Cuentas por Cobrar',$data);
		$grid5->per_page = 3000;
		
		$uri = anchor_popup('recaudacion/r_cxc/dataedit/show/<raencode><#id#></raencode>','<#id#>',$atts);
		$uri2= anchor_popup('recaudacion/r_recibo/dataedit/show/<raencode><#id_recibo#></raencode>','<#id_recibo#>',$atts);
	         
		$grid5->column('Ref.'           ,"$uri"                                         ,'align="left"');
		$grid5->column('Fecha'          ,"<dbdate_to_human><#fecha#></dbdate_to_human>",'align="center"');
		$grid5->column('Ref Conc'       ,"id_concit"                                   ,'align="left"');
		$grid5->column('Denominacion'   ,"denomi"                                      ,'align="left"');
		$grid5->column('A&ntilde;o'     ,"ano"                                         ,'align="left"');
		$grid5->column('Frecuencia'     ,"frecuencia"                                  ,'align="left"');
		$grid5->column('Valor'          ,"freval"                                      ,'align="left"');
		$grid5->column('Ref. Vehi'      ,"id_vehiculo"                                 ,'align="left"');
		$grid5->column('Placa'          ,"v_placa"                                     ,'align="left"');
		$grid5->column('Ref. Inmu'      ,"id_inmueble"                                 ,'align="left"');
		$grid5->column('Catastro'       ,"i_catastro"                                  ,'align="left"');
		$grid5->column('Observacion'    ,"observa"                                     ,'align="left"');
		$grid5->column('Monto'          ,"<nformat><#monto#></nformat>"                ,'align="right"');
		$grid5->column('Recibo'         ,$uri2                                         ,'align="right"');

		$grid5->build();	
		/* CUENTAS POR COBRAR*/
		
		$rifci = $this->datasis->dameval("SELECT rifci FROM r_contribu WHERE id=".$this->db->escape($id));
		if(empty($rifci))
		$rifci=0;
		$rifcie=$this->db->escape('%'.$rifci.'%');
		
		$query ="SELECT * FROM r_otrospagos WHERE rifci like $rifcie ORDER BY fecha desc";
		$data = $this->db->query($query);
		$data = $data->result_array();
		$grid4 = new DataGrid('Otros Pagos',$data);
		$grid4->per_page = 3000;
		
		$grid4->column('Numero'         ,"numero"                                      ,'align="left"');
		$grid4->column('Fecha'          ,"<dbdate_to_human><#fecha#></dbdate_to_human>",'align="center"');
		$grid4->column('Nombre'         ,"nombre"                                      ,'align="center"');
		$grid4->column('Concepto'       ,"concepto"                                    ,'align="left"');
		$grid4->column('Observacion'    ,"observa"                                     ,'align="left"');
		$grid4->column('Monto'          ,"<nformat><#monto#></nformat>"                ,'align="right"');

		$grid4->build();
		
		$data = $this->db->query(
		"SELECT  
		a.id,b.codigo,b.descrip,a.dir1,a.dir2,a.dir3,a.dir4,a.alto,a.ancho,a.dimension,a.ultano
		FROM r_publicidad a
		LEFT JOIN rp_tipos b ON a.id_tipo=b.id
		WHERE  id_contribu=".$this->db->escape($id)
		);
		$data = $data->result_array();
		$grid5 = new DataGrid("Publicidades",$data);
		$grid5->per_page = 3000;
		
		$uri = anchor_popup('recaudacion/r_publicidad/dataedit/show/<raencode><#id#></raencode>','<#id#>',$atts);

		$grid5->column('Ref.'           ,"$uri"                               ,'id'          ,'align="left"' );
		$grid5->column('Codigo'         ,"codigo"                             ,'codigo'      ,'align="left"' );
		$grid5->column('Direccion 1'    ,"dir1"                               ,'dir1'        ,'align="left"' );
		$grid5->column('Direccion 2'    ,"dir2"                               ,'dir2'        ,'align="left"' );
		$grid5->column('Direccion 3'    ,"dir3"                               ,'dir3'        ,'align="left"' );
		$grid5->column('Direccion 4'    ,"dir4"                               ,'dir4'        ,'align="left"' );
		$grid5->column('Alto'           ,"alto"                               ,'alto'        ,'align="left"' );
		$grid5->column('Ancho'          ,"ancho"                              ,'ancho'       ,'align="right"');
		$grid5->column('Dimension'      ,"dimension"                          ,'dimension'   ,'align="left"' );
		$grid5->column('ua'             ,"ua"                                 ,'ua'          ,'align="right"');

		$grid5->build();
		
		$tablas ='<table width=\'100%\'>';
		$tablas.='<tr><td scrollbar="yes" width="100%" height="50px">';
		$tablas.=str_replace('mainbackgroundtable','',$grid->output);
		$tablas.='</td></tr>';
		$tablas.='<tr><td scrollbar="yes" width="100%" height="50px">';
		$tablas.=str_replace('mainbackgroundtable','',$grid2->output);
		$tablas.='</td></tr>';
		$tablas.='<tr><td scrollbar="yes" width="100%" height="50px">';
		$tablas.=str_replace('mainbackgroundtable','',$grid5->output);
		$tablas.='</td></tr>';
		$tablas.='<tr><td scrollbar="yes" width="100%" height="100px" bgcolor=#FFFFAA>';
		$tablas.=str_replace('mainbackgroundtable','',$grid3->output);
		$tablas.='</td></tr>';
		$tablas.='<tr><td scrollbar="yes" width="100%" height="100px" bgcolor=#FFAAFF>';
		$tablas.=str_replace('mainbackgroundtable','',$grid5->output);
		$tablas.='</td></tr>';
		$tablas.='<tr><td scrollbar="yes" width="100%" height="100px" bgcolor=#AAFFFF>';
		$tablas.=str_replace('mainbackgroundtable','',$grid4->output);
		$tablas.='</td></tr>';
		$tablas.='</table>';
		
		$data['content'] = $tablas;
		$data['head']    = $this->rapyd->get_head();
		$data['title']   = "";
		$this->load->view('view_ventanas', $data);
	}
	
	
	function eliminadupli(){
		$query="
		SELECT group_concat(id),rifci
		FROM r_contribu a
		GROUP BY rifci
		HAVING COUNT(*)>1
		";
		
		$contribu = $this->datasis->consularray($query);
		foreach($contribu as $in=>$rif){
			$ids=explode(',',$in);
			$id=$ids[0];
			
			$this->db->query("UPDATE r_vehiculo SET id_contribu=$id WHERE id_contribu IN ($in)");
			$this->db->query("UPDATE r_inmueble SET id_contribu=$id WHERE id_contribu IN ($in)");
			$this->db->query("UPDATE r_recibo SET id_contribu=$id WHERE id_contribu IN ($in)  ");
			$this->db->query("DELETE FROM r_contribu WHERE id IN ($in) AND id<>$id            ");
			
		}
	}

	function _post_insert($do){
		$primary =implode(',',$do->pk);
		logusu($do->table,"Creo $this->tits $primary ");
	}
	function _post_update($do){
		$primary =implode(',',$do->pk);
		logusu($do->table,"Modifico $this->tits $primary ");
	}
	function _post_delete($do){
		$primary =implode(',',$do->pk);
		logusu($do->table,"Elimino $this->tits $primary ");
	}

	function instalar(){
		$mSQL="CREATE TABLE `r_contribu` (
		  `id` int(11) NOT NULL AUTO_INCREMENT,
		  `rifci` varchar(12) DEFAULT NULL,
		  `nombre` varchar(100) DEFAULT NULL,
		  `telefono` varchar(50) DEFAULT NULL,
		  `id_parroquia` int(11) DEFAULT NULL,
		  `id_zona` int(11) DEFAULT NULL,
		  `dir1` varchar(255) DEFAULT NULL,
		  `dir2` varchar(255) DEFAULT NULL,
		  `dir3` varchar(255) DEFAULT NULL,
		  `dir4` varchar(255) NOT NULL,
		  `patente` char(1) DEFAULT 'N',
		  `nro` varchar(10) DEFAULT NULL,
		  `id_negocio` int(11) DEFAULT NULL,
		  `id_repre` int(11) DEFAULT NULL,
		  `objeto` text,
		  `observa` text,
		  `archivo` varchar(50) DEFAULT NULL,
		  PRIMARY KEY (`id`)
		) ENGINE=MyISAM AUTO_INCREMENT=1 DEFAULT CHARSET=utf8";
		$this->db->simple_query($mSQL);
		$mSQL="ALTER TABLE `r_contribu` ADD INDEX `rifci` (`rifci`)";
		$this->db->simple_query($mSQL);
		$mSQL="ALTER TABLE `r_contribu` ADD INDEX `nombre` (`nombre`)";
		$this->db->simple_query($mSQL);
		
		$query="CREATE TABLE `r_contribuit` (
			`id` INT(11) NOT NULL AUTO_INCREMENT,
			`id_contribu` INT(11) NULL DEFAULT NULL,
			`id_contribuit` INT(11) NULL DEFAULT NULL,
			PRIMARY KEY (`id`)
		)
		COLLATE='utf8_general_ci'
		ENGINE=MyISAM
		AUTO_INCREMENT=1";
		$this->db->simple_query($query);
		
		$query="ALTER TABLE `r_contribu` ADD COLUMN `rif` VARCHAR(12) NULL DEFAULT NULL    ";
		$this->db->simple_query($query);
		$query="ALTER TABLE `r_contribu` ADD COLUMN `nomfis` VARCHAR(100) NULL DEFAULT NULL";
		$this->db->simple_query($query);
		$query="ALTER TABLE `r_contribu` ADD COLUMN `tipo`  CHAR(1) NULL DEFAULT 'S'";
		$this->db->simple_query($query);
		$quey="ALTER TABLE `r_contribu` ADD COLUMN `email` VARCHAR(50) NULL DEFAULT NULL AFTER `telefono`";
		$this->db->simple_query($query);
		$query="ALTER TABLE `r_contribu` 	ADD COLUMN `activo` CHAR(1) NULL DEFAULT 'S'";
		$this->db->simple_query($query);

	}
	
	function prueba($var=''){
		echo $var;
		//$term = $this->input->get('term');
	}
}
?>
