<?php
class r_cxc extends Controller {
	var $titp='Cuentas por Cobrar';
	var $tits='Cuenta por Cobrar';
	var $url ='recaudacion/r_cxc/';
	function r_cxc(){
		parent::Controller();
		$this->load->library("rapyd");
		$this->datasis->modulo_id(444,1);
	}
	function index(){
		redirect($this->url."filteredgrid");
	}
	function filteredgrid(){
		$this->rapyd->load('datafilter','datagrid');
		$filter = new DataFilter($this->titp, 'r_cxc');
		
		//$user          = $this->session->userdata('usuario');
		//$usere         = $this->db->escape($user);
		//$r_caja        = $this->datasis->damerow("SELECT r_caja.id,punto_codbanc FROM r_caja JOIN  usuario ON r_caja.id=usuario.caja WHERE us_codigo =$usere");
		//
		//if(count($r_caja)>0){
		//	$caja          = $r_caja['id'];
		//	$punto_codbanc = $r_caja['punto_codbanc'];
		//}else{
		//	$caja=0;
		//}
		//if($caja>0){
		//		$filter->db->where('caja',$caja);
		//}

		$filter->id = new inputField('Ref.','id');
		$filter->id->rule      ='max_length[11]';
		$filter->id->size      =13;
		$filter->id->maxlength =11;
		$filter->id->clause    ='where';
		$filter->id->operator  ='=';

		$filter->fecha = new dateField('Fecha','fecha');
		$filter->fecha->rule      ='chfecha';
		$filter->fecha->size      =10;
		$filter->fecha->maxlength =8;
		$filter->fecha->clause    ='where';
		$filter->fecha->operator  ='=';
		
		$filter->numero = new inputField('Numero.','numero');
		$filter->numero->rule      ='max_length[11]';
		$filter->numero->size      =13;
		$filter->numero->maxlength =11;

		$filter->rifci = new inputField('rifci','rifci');
		$filter->rifci->rule      ='max_length[12]';
		$filter->rifci->size      =14;
		$filter->rifci->maxlength =12;

		$filter->nombre = new inputField('Nombre','nombre');
		$filter->nombre->rule      ='max_length[100]';
		$filter->nombre->size      =40;
		$filter->nombre->maxlength =100;
		
		$filter->monto = new inputField('Monto.','monto');
		$filter->monto->rule      ='max_length[11]';
		$filter->monto->size      =13;
		$filter->monto->maxlength =11;
		
		//$filter->caja = new dropdownField("Caja","cajas");
		//$filter->caja->option("","");
		//$filter->caja->options("SELECT id,nombre FROM r_caja ");
		//$filter->caja->db_name   ='r_recibo.caja';


		$filter->buttons('reset', 'search');
		$filter->build();

		$uri = anchor($this->url.'dataedit/show/<raencode><#id#></raencode>','<#id#>');

		$grid = new DataGrid('');
		$grid->order_by('id','desc');
		$grid->per_page = 40;

		$grid->column_orderby('Ref.'    ,"$uri"                                        ,'id'      ,'align="left"');
		$grid->column_orderby('Numero'  ,"numero"                                      ,'numero'  ,'align="left"');
		$grid->column_orderby('Fecha'   ,"<dbdate_to_human><#fecha#></dbdate_to_human>",'fecha'   ,'align="center"');
		$grid->column_orderby('Rif/CI'  ,"rifci"                                       ,'rifci'   ,'align="left"');
		$grid->column_orderby('Nombre'  ,"nombre"                                      ,'nombre'  ,'align="left"');
		$grid->column_orderby('Monto'   ,"<numbre_format><#monto#></numbre_format>"    ,'monto'   ,'align="left"');
		$grid->column_orderby('Caja'    ,"caja"                                        ,'caja'    ,'align="left"');
		
		//$action = "javascript:window.location='" .site_url('recaudacion/r_abonos/filteredgrid'). "'";
		//$grid->button("ir_cobranza","Ir a Cobranza",$action,"TL");
		if($this->datasis->puede(476))
		$grid->add($this->url.'dataedit/create');
		
		$grid->build();

		$data['filtro']  = $filter->output;
		$data['content'] = $grid->output;
		$data['head']    = $this->rapyd->get_head().script('jquery.js');
		$data['title']   = $this->titp;
		$this->load->view('view_ventanas', $data);

	}
	function dataedit($id_contribu=false){
		$this->rapyd->load('datadetails','dataobject');
		
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
				'id'    =>'id_contribu',
				'nombre'=>'nombre',
				'rifci' =>'rifci'
			),
			'titulo'  =>'Buscar Contribuyente'
		);

		$button  = $this->datasis->modbus($modbus);
		
		$modbus=array(
			'tabla'   =>'r_v_conc',
			'columnas'=>array(
				'id'          =>'Ref'         ,           
				'ano'         =>'A&ntilde;o'  ,        
				'acronimo'    =>'Acronimo'    ,     
				'denomi'      =>'Denominacion', 
				'denomiconc'  =>'Denomi Padre', 
				'partida'     =>'Partida'     ,     
			),
			'filtro'  =>array(
				'denomi'      =>'Denominacion', 
				'denomiconc'  =>'Denomi Padre', 
				'partida'     =>'Partida'     ,     
			),
			'retornar'=>array(
				'id'          =>'id_concit_<#i#>',
				'ano'         =>'ano_<#i#>'      ,
				'denomi'      =>'denomi_<#i#>'   ,
				'requiere'    =>'requiere_<#i#>' ,
				'modo'        =>'modo_<#i#>'
			),
			'titulo'  =>'Buscar Concepto',
			'script'  =>array('post_conc(<#i#>,0)'),
			'p_uri'=>array(
				4=>'<#i#>'
			)
		);

		$buttonconc  = $this->datasis->p_modbus($modbus,'<#i#>' );
		
		$mVEHICULO=array(
				'tabla'   =>'r_v_vehiculo',
				'columnas' =>array(
					'id'       =>'Ref.',
					'placa'    =>'Placa',
					'marca'    =>'Marca',
					'modelo'   =>'Modelo',
					'color'    =>'Color',
					'tipo'     =>'Tipo' ,
					'clase'    =>'Clase'
					),
				'filtro'  =>array(
					'id'       =>'Ref.',
					'placa'    =>'Placa',
					'marca'    =>'Marca',
					'modelo'   =>'Modelo',
					'color'    =>'Color',
					'tipo'     =>'Tipo' ,
					'clase'    =>'Clase'
					),
				'retornar'=>array(
					'id'       =>'id_vehiculo_<#i#>',
					'placa'    =>'v_placa_<#i#>'
					),
				'p_uri'  =>array(
					4=>'<#id_contribu#>',
					5=>'<#i#>'
					),
				'where'  =>'id_contribu = <#id_contribu#>',
				'script' =>array('post_conc(<#i#>,1)'),
				'titulo' =>'Buscar Vehiculo');
		
		$bVEHICULO=$this->datasis->p_modbus($mVEHICULO,'<#id_contribu#>/<#i#>');
		$bVEHICULO='<img id="modbusv_<#i#>" src="'.base_url().'assets/default/images/system-search.png"  alt="Busqueda de Vehiculos" title="Busqueda de Vehiculos" border="0" onclick="modbusdepenv(<#i#>)"/>';
		
		$mINMUEBLE=array(
				'tabla'   =>'r_v_inmueble',
				'columnas' =>array(
					'id'       =>'Ref.',
					'catastro' =>'Catastro',
					'direccion'=>'Direccion',
					'mt2'      =>'Mts2',
					'techo'    =>'Techo',
					),
				'filtro'  =>array(
					'id'       =>'Ref.',
					'catastro' =>'Catastro',
					'direccion'=>'Direccion',
					'mt2'      =>'Mts2',
					'techo'    =>'Techo',
					),
				'retornar'=>array(
					'id'       =>'id_inmueble_<#i#>',
					'catastro' =>'i_catastro_<#i#>',
					'direccion'=>'observa_<#i#>',
					),
				'p_uri'  =>array(
					4=>'<#id_contribu#>',
					5=>'<#i#>'
					),
				'where'  =>'id_contribu = <#id_contribu#>',
				'script' =>array('post_conc(<#i#>,1)'),
				'titulo' =>'Buscar Inmueble');
		
		$bINMUEBLE=$this->datasis->p_modbus($mINMUEBLE,'<#id_contribu#>/<#i#>');
		$bINMUEBLE='<img id="modbusi_<#i#>" src="/tortuga/assets/default/images/system-search.png"  alt="Busqueda de Vehiculos" title="Busqueda de Vehiculos" border="0" onclick="modbusdepeni(<#i#>)"/>';
		
		$mPUBLICIDAD=array(
				'tabla'   =>'r_v_publicidad',
				'columnas' =>array(
					'id'       =>'Ref.',
					'direccion'=>'Direccion',
					'ancho'    =>'Ancho',
					'alto'     =>'Alto', 
					'descrip'  =>'Descripcion',
					),
				'filtro'  =>array(
					'id'       =>'Ref.',
					'direccion'=>'Direccion',
					'ancho'    =>'Ancho',
					'alto'     =>'Alto', 
					'descrip'  =>'Descripcion',
					),
				'retornar'=>array(
					'id'       =>'id_publicidad_<#i#>',
					'CONCAT(ancho,"X",alto," mts2")'=>'observa_<#i#>',
					),
				'p_uri'  =>array(
					4=>'<#id_contribu#>',
					5=>'<#i#>'
					),
				'where'  =>'id_contribu = <#id_contribu#>',
				'script' =>array('traemonto(<#i#>)'),
				'titulo' =>'Buscar Vehiculo');
		
		$bPUBLICIDAD=$this->datasis->p_modbus($mPUBLICIDAD,'<#id_contribu#>/<#i#>');
		$bPUBLICIDAD='<img id="modbusp_<#i#>" src="/tortuga/assets/default/images/system-search.png"  alt="Busqueda de Publicidades" title="Busqueda de Publicidad" border="0" onclick="modbusdepenp(<#i#>)"/>';
		
		$mINMUEBLET=array(
				'tabla'   =>'r_v_inmueble',
				'columnas' =>array(
					'id'       =>'Ref.',
					'catastro' =>'Catastro',
					'direccion'=>'Direccion',
					'mt2'      =>'Mts2',
					'tipoi'    =>'Tipo',
					),
				'filtro'  =>array(
					'id'       =>'Ref.',
					'catastro' =>'Catastro',
					'direccion'=>'Direccion',
					'mt2'      =>'Mts2',
					'tipoi'    =>'Tipo',
					),
				'retornar'=>array(
					'id'       =>'id_inmueble'
					),
				'p_uri'  =>array(
					4=>'<#id_contribu#>'
					),
				'where'  =>'id_contribu = <#id_contribu#>',
				'script' =>array('post_inmueblet()'),
				'titulo' =>'Buscar Inmueble');
		
		$bINMUEBLET=$this->datasis->modbus($mINMUEBLET,'r_v_inmueblet',800,600,'r_v_inmueblet');
		$bINMUEBLET='<img id="modbusit" src="/tortuga/assets/default/images/system-search.png"  alt="Busqueda de Inmuebles" title="Busqueda de Inmuebles" border="0" onclick="modbusdepenit()"/>';
		
		$mVEHICULOT=array(
				'tabla'   =>'r_v_vehiculo',
				'columnas' =>array(
					'id'       =>'Ref.',
					'placa'    =>'Placa',
					'marca'    =>'Marca',
					'modelo'   =>'Modelo',
					'color'    =>'Color',
					'tipo'     =>'Tipo' ,
					'clase'    =>'Clase'
					),
				'filtro'  =>array(
					'id'       =>'Ref.',
					'placa'    =>'Placa',
					'marca'    =>'Marca',
					'modelo'   =>'Modelo',
					'color'    =>'Color',
					'tipo'     =>'Tipo' ,
					'clase'    =>'Clase'
					),
				'retornar'=>array(
					'id'       =>'id_vehiculo'
					),
				'p_uri'  =>array(
					4=>'<#id_contribu#>'
					),
				'where'  =>'id_contribu = <#id_contribu#>',
				'script' =>array('post_vehiculot()'),
				'titulo' =>'Buscar Vehiculos');
		
		$bVEHICULOT=$this->datasis->modbus($mVEHICULOT,'r_v_vehiculot',800,600,'r_v_vehiculot');
		$bVEHICULOT='<img id="modbusvt" src="/tortuga/assets/default/images/system-search.png"  alt="Busqueda de Vehiculos" title="Busqueda de Vehiculos" border="0" onclick="modbusdepenvt()"/>';
		
		$contribu=array();
		$contribu['id']     ='';
		$contribu['nombre'] ='';
		$contribu['rifci']  ='';
		if($id_contribu>0){
				$contribu=$this->datasis->damerow("SELECT id,rifci,nombre FROM r_contribu WHERE id=$id_contribu");
		}
		
		$do = new DataObject("r_cxc");
		$do->rel_one_to_many('r_cxcit'   , 'r_cxcit'   , array('id' =>'id_cxc'));
		$do->order_by('r_cxcit',"ano",'asc');
		$do->order_by('r_cxcit',"freval",'asc');

		$edit = new DataDetails($this->tits, $do);

		$edit->back_url = site_url($this->url."filteredgrid");

		$edit->pre_process('insert','_valida');
		$edit->pre_process('update','_valida');

		$edit->post_process('insert','_post_insert');
		$edit->post_process('update','_post_update');
		$edit->post_process('delete','_post_delete');

		$edit->id = new inputField('Ref','id');
		$edit->id->rule='max_length[11]';
		$edit->id->size =2;
		$edit->id->maxlength =11;
		$edit->id->mode='autohide';
		$edit->id->when=array('show','modify');
		
		$edit->id_contribu = new inputField('Contribuyente','id_contribu');
		$edit->id_contribu->rule='required';
		$edit->id_contribu->size =5;
		$edit->id_contribu->maxlength =11;
		$edit->id_contribu->append($button);
		$edit->id_contribu->readonly=true;
		$edit->id_contribu->value   =$contribu['id'] ;

		$crea = '<a href="javascript:creacontribu();" title="Agregar Contribuyente">'.image('add.png','#',array("border"=>0)).'</a>';
		$edit->rifci = new inputField('rifci','rifci');
		$edit->rifci->rule='max_length[12]';
		$edit->rifci->size =14;
		$edit->rifci->maxlength =12;
		$edit->rifci->append($crea);
		$edit->rifci->autocomplete=false;
		$edit->rifci->value   =$contribu['rifci'] ;

		$edit->nombre = new inputField('nombre','nombre');
		$edit->nombre->rule='max_length[100]';
		$edit->nombre->size =40;
		$edit->nombre->maxlength =100;
		$edit->nombre->value   =$contribu['nombre'] ;
		
		$edit->fecha = new dateOnlyField('Fecha','fecha');
		$edit->fecha->rule='chfecha';
		$edit->fecha->size =10;
		$edit->fecha->maxlength =8;
		$edit->fecha->insertValue=date('Y-m-d');
		
		$user   = $this->session->userdata('usuario');
		$usere  = $this->db->escape($user);
		$numero = $this->datasis->dameval("SELECT r_contador.proxnumero FROM r_contador JOIN r_caja ON r_contador.id=r_caja.id_contador JOIN  usuario ON r_caja.id=usuario.caja WHERE us_codigo =$usere");
		
		$edit->numero = new inputField('Numero','numero');
		$edit->numero->rule='trim|numeric';
		$edit->numero->size =10;
		$edit->numero->maxlength =8;
		$edit->numero->append("Proximo:".$numero);
		//$edit->numero->type="inputhidden";
		//$edit->numero->when=array('show','modify');
		$edit->numero->css_class='inputnum';
		$edit->numero->readonly = true;
		
		$edit->monto = new inputField('Monto','monto');
		$edit->monto->readonly=true;
		$edit->monto->size =5;
		$edit->monto->css_class ="inputnum";
		
		$edit->solvencia = new inputField('Solvencia','solvencia');
		$edit->solvencia->mode='autohide';
		$edit->solvencia->size =5;
		$edit->solvencia->when=array('show','modify');
		
		$edit->id_inmueble = new hiddenField('id_inmueble','id_inmueble');
		$edit->id_inmueble->rule='max_length[11]';
		$edit->id_inmueble->size =6;
		$edit->id_inmueble->maxlength =11;
		$edit->id_inmueble->db_name=' ';
		$edit->id_inmueble->append($bINMUEBLET);
		
		$edit->id_vehiculo = new hiddenField('id_vehiculo','id_vehiculo');
		$edit->id_vehiculo->rule='max_length[11]';
		$edit->id_vehiculo->size =6;
		$edit->id_vehiculo->maxlength =11;
		$edit->id_vehiculo->db_name=' ';
		$edit->id_vehiculo->append($bVEHICULOT);
		
		/*
		 * DETALLE
		 * 
		 * */
		 
		$edit->itid =  new inputField("(<#o#>) Referencia", 'id_<#i#>');
		$edit->itid->db_name   = 'id';
		$edit->itid->rel_id    ='r_cxcit';
		//$edit->itid->when     =array('show');
		$edit->itid->readonly=true;
		$edit->itid->size      =1;
		$edit->itid->type      ='hidden';
		
		$edit->itid_conc =  new inputField("(<#o#>) Id Concepto", 'id_conc_<#i#>');
		$edit->itid_conc->db_name   = 'id_conc';
		$edit->itid_conc->rel_id    = 'r_cxcit';
		$edit->itid_conc->readonly  = true;
		$edit->itid_conc->size      = 1;
		$edit->itid_conc->type      = 'hidden';
		 
		$edit->itid_concit = new inputField('id_concit','id_concit_<#i#>');
		$edit->itid_concit->rule='max_length[11]';
		$edit->itid_concit->size =3;
		$edit->itid_concit->maxlength =11;
		$edit->itid_concit->db_name='id_concit';
		$edit->itid_concit->rel_id ='r_cxcit';
		$edit->itid_concit->append($buttonconc);
		$edit->itid_concit->readonly=true;
		
		$edit->itrequiere = new hiddenField('requiere','requiere_<#i#>');
		$edit->itrequiere->rule='max_length[11]';
		$edit->itrequiere->size =3;
		$edit->itrequiere->maxlength =11;
		$edit->itrequiere->db_name='requiere';
		$edit->itrequiere->rel_id ='r_cxcit';
		
		$edit->itmodo = new hiddenField('modo','modo_<#i#>');
		$edit->itmodo->rule='max_length[11]';
		$edit->itmodo->size =3;
		$edit->itmodo->maxlength =11;
		$edit->itmodo->db_name='modo';
		$edit->itmodo->rel_id ='r_cxcit';
		
		$edit->itdenomi = new inputField('denomi','denomi_<#i#>');
		$edit->itdenomi->rule='max_length[80]';
		$edit->itdenomi->size =20;
		$edit->itdenomi->maxlength =80;
		$edit->itdenomi->db_name='denomi';
		$edit->itdenomi->rel_id ='r_cxcit';
		$edit->itdenomi->autocomplete=false;
		
		$edit->itano = new inputField('ano','ano_<#i#>');
		$edit->itano->rule='max_length[11]';
		$edit->itano->size =5;
		$edit->itano->maxlength =11;
		$edit->itano->db_name='ano';
		$edit->itano->rel_id ='r_cxcit';
		$edit->itano->type = 'inputhidden';
		
		$edit->itfrecuencia = new inputField('(<#o#>) frecuencia','frecuencia_<#i#>');
		//$edit->itfrecuencia->option('0','');
		//$edit->itfrecuencia->option('1','A&ntilde;o'  );
		//$edit->itfrecuencia->option('2','Semestre'    );
		//$edit->itfrecuencia->option('3','Trimestre'   );
		//$edit->itfrecuencia->option('4','Mes'         );
		$edit->itfrecuencia->style="width:70px;";
		$edit->itfrecuencia->db_name='frecuencia';
        $edit->itfrecuencia->rel_id ='r_cxcit';
        $edit->itfrecuencia->type = 'inputhidden';        
        
        $edit->itfreval = new inputField('(<#o#>) Valor Frecuencia','freval_<#i#>');
		$edit->itfreval->option('0','');
		for($i=1;$i<=12;$i++)
		$edit->itfreval->option($i,$i);
		$edit->itfreval->style="width:50px;";
		$edit->itfreval->db_name='freval';
        $edit->itfreval->rel_id ='r_cxcit';
        $edit->itfreval->type = 'inputhidden';
		
		$edit->itobserva = new textAreaField('observa','observa_<#i#>');
		$edit->itobserva->rule='max_length[255]';
		$edit->itobserva->cols =20;
		$edit->itobserva->rows =1;
		$edit->itobserva->maxlength =255;
		$edit->itobserva->db_name='observa';
		$edit->itobserva->rel_id ='r_cxcit';
		$edit->itobserva->style="height:20px;";
		
		$edit->itbase = new inputField('Base','base_<#i#>');
		$edit->itbase->rule='max_length[19]|numeric';
		$edit->itbase->css_class='inputnum';
		$edit->itbase->size =5;
		$edit->itbase->maxlength =19;
		$edit->itbase->db_name='base';
		$edit->itbase->rel_id ='r_cxcit';
		$edit->itbase->onchange = "cal_base(<#i#>);";

		$edit->itmonto = new inputField('monto','monto_<#i#>');
		$edit->itmonto->rule='max_length[19]|numeric';
		$edit->itmonto->css_class='inputnum';
		$edit->itmonto->size =5;
		$edit->itmonto->maxlength =19;
		$edit->itmonto->db_name='monto';
		$edit->itmonto->rel_id ='r_cxcit';
		$edit->itmonto->onchange = "cal_total();";
		$edit->itmonto->readonly=true;

		$crea = '<a id="creav_<#i#>" href="javascript:creavehiculoid(<#i#>);" title="Agregar/modificar Vehiculo">'.image('add.png','#',array("border"=>0)).'</a>';
		$edit->itid_vehiculo = new inputField('id_vehiculo','id_vehiculo_<#i#>');
		$edit->itid_vehiculo->rule='max_length[11]';
		$edit->itid_vehiculo->size =6;
		$edit->itid_vehiculo->maxlength =11;
		$edit->itid_vehiculo->db_name='id_vehiculo';
		$edit->itid_vehiculo->rel_id ='r_cxcit';
		$edit->itid_vehiculo->append($bVEHICULO);
		$edit->itid_vehiculo->append($crea);
		
		$edit->itv_placa = new inputField('v_placa','v_placa_<#i#>');
		$edit->itv_placa->rule='max_length[12]';
		$edit->itv_placa->size =8;
		$edit->itv_placa->maxlength =12;
		$edit->itv_placa->db_name='v_placa';
		$edit->itv_placa->rel_id ='r_cxcit';

		$crea = '<a id="creai_<#i#>" href="javascript:creainmuebleid(<#i#>);" title="Agregar/modificar Inmueble">'.image('add.png','#',array("border"=>0)).'</a>';
		$edit->itid_inmueble = new inputField('id_inmueble','id_inmueble_<#i#>');
		$edit->itid_inmueble->rule='max_length[11]';
		$edit->itid_inmueble->size =6;
		$edit->itid_inmueble->maxlength =11;
		$edit->itid_inmueble->db_name='id_inmueble';
		$edit->itid_inmueble->rel_id ='r_cxcit';
		$edit->itid_inmueble->append($bINMUEBLE);
		$edit->itid_inmueble->append($crea);
		
		$edit->iti_catastro = new inputField('v_placa','i_catastro_<#i#>');
		$edit->iti_catastro->rule='max_length[12]';
		$edit->iti_catastro->size =6;
		$edit->iti_catastro->maxlength =12;
		$edit->iti_catastro->db_name='i_catastro';
		$edit->iti_catastro->rel_id ='r_cxcit';
		
		$crea = '<a id="creap_<#i#>" href="javascript:creapublicidadid(<#i#>);" title="Agregar/modificar Publicidad">'.image('add.png','#',array("border"=>0)).'</a>';
		$edit->itid_publicidad = new inputField('id_publicidad','id_publicidad_<#i#>');
		$edit->itid_publicidad->rule='max_length[11]';
		$edit->itid_publicidad->size =3;
		$edit->itid_publicidad->maxlength =11;
		$edit->itid_publicidad->db_name='id_publicidad';
		$edit->itid_publicidad->rel_id ='r_cxcit';
		$edit->itid_publicidad->append($bPUBLICIDAD);
		$edit->itid_publicidad->append($crea);
		
		$id     =$edit->get_from_dataobjetct('id');
		
		if($id>0){
			$id_recibo = $this->datasis->dameval("SELECT r_recibo.id
			FROM r_cxc
			JOIN r_cxcit ON r_cxc.id=r_cxcit.id_cxc
			LEFT JOIN r_reciboit ON r_cxcit.id=r_reciboit.id_cxcit
			LEFT JOIN r_recibo ON r_reciboit.id_recibo=r_recibo.id 
			LEFT JOIN r_abonosit ON r_recibo.id=r_abonosit.recibo 
			LEFT JOIN r_abonos ON r_abonos.id=r_abonosit.abono 
			WHERE r_cxc.id=$id LIMIT 1");
			if($id_recibo>0){
				$action = "javascript:location.href='" .site_url('recaudacion/r_cxc/dataedit/show/'.$id_recibo). "'";
				$edit->button_status("add_r_recibo","Ver Recibo $id",$action,"TL","show");
			}else{
				if($this->datasis->puede(477))
				$edit->buttons('modify','save');
				
				if($this->datasis->puede(478))
				$edit->buttons('delete');
			}
		}
		
		//$id_contribu = $edit->get_from_dataobjetct('id_contribu');
		//$action      = "javascript:window.location='" .site_url($this->url.'dataedit/'.$id_contribu.'/create'."'");
		//$edit->button_status("btn_crearsimiliar",'Agregar Similar' ,$action,"TL","show");
		
		$action = "javascript:analizar()";
		$edit->button_status("btn_statusam",'Analizar Contribuyente',$action,"TL","modify");
		$edit->button_status("btn_statusac",'Analizar Contribuyente',$action,"TL","create");
		
		if($this->datasis->puede(469)){
			$action      = "javascript:window.location='" .site_url($this->url.'actdeuda/'."$id'");
			$edit->button_status("btn_actdeuda",'Actualizar Deuda',$action,"TR","show");
		}

		if($this->datasis->puede(476))
		$edit->buttons('add','add_rel','save');
		
		$edit->buttons('undo', 'back');

		//$edit->buttons('add_rel','add', 'save', 'undo', 'back');
		$edit->build();
		
		$smenu['link']   = barra_menu('523');
		$data['smenu']   = $this->load->view('view_sub_menu', $smenu,true);
		$conten["form"]  =&$edit;
		$data['content'] = $this->load->view('recaudacion/r_cxc', $conten,true);
		$data['title']   = $this->tits;
		$data["head"]    = script('jquery.js').script('jquery-ui.js').script('plugins/jquery.numeric.pack.js').script('plugins/jquery.floatnumber.js').script('plugins/jquery.meiomask.js').style('vino/jquery-ui.css').$this->rapyd->get_head();
		$this->load->view('view_ventanas', $data);

	}
	
	function _valida($do){
		
		$error="";
		$id          = $do->get('id');
		$id_contribu = $do->get('id_contribu');
		$numero      = $do->get('numero');
		$fecha       = $do->get('fecha' );
		
		$contribu    =$this->datasis->damerow("SELECT rifci,nombre,id_parroquia,parroquia,id_zona,zona,dir1,dir2,dir3,dir4 FROM r_v_contribu WHERE id='$id_contribu' LIMIT 1");
		
		$do->set('nombre'        ,$contribu['nombre'         ]);
		$do->set('rifci'         ,$contribu['rifci'          ] );
		$do->set('id_parroquia'  ,$contribu['id_parroquia'   ] );
		$do->set('parroquia'     ,$contribu['parroquia'      ] );
		$do->set('id_zona'       ,$contribu['id_zona'        ] );
		$do->set('zona'          ,$contribu['zona'           ] );
		$do->set('dir1'          ,$contribu['dir1'           ] );
		$do->set('dir2'          ,$contribu['dir2'           ] );
		$do->set('dir3'          ,$contribu['dir3'           ] );
		$do->set('dir4'          ,$contribu['dir4'           ] );
		
		/**************TRAE CONCEPTOS INTERESES*****************/
		$query="SELECT a.id id_concit,a.id_conc,a.denomi,a.formula,b.id_presup,b.denomi conc_denomi,c.partida,c.denomi partida_denomi,0 monto,a.requiere
		FROM r_concit a
		JOIN r_conc b ON a.id_conc=b.id
		JOIN r_presup c ON b.id_presup=c.id
		WHERE a.requiere='INTERESES' AND deleted=0";
		
		$intereses=$this->db->query($query);
		$intereses=$intereses->result_array();
		
		/***********BORRAR INTERESES******************************/
		for($i=0;$i < $do->count_rel('r_cxcit');$i++){
			$requiere    = $do->get_rel('r_cxcit','requiere'   ,$i);
			if($requiere=='INTERESES'){
				array_splice($do->data_rel['r_cxcit'],$i,1);
			}
		}
		
		/************** TRAE CONCEPTOS DESCUENTOS *****************/
		$query="SELECT a.id id_concit,a.id_conc,a.denomi,a.formula,b.id_presup,b.denomi conc_denomi,c.partida,c.denomi partida_denomi,0 monto,a.requiere
		FROM r_concit a
		JOIN r_conc b ON a.id_conc=b.id
		JOIN r_presup c ON b.id_presup=c.id
		WHERE a.requiere='DESCUENTO' AND deleted=0";
		
		$descuentos=$this->db->query($query);
		$descuentos=$descuentos->result_array();
		
		/***********BORRAR DESCUENTOS******************************/
		for($i=0;$i < $do->count_rel('r_cxcit');$i++){
			$requiere    = $do->get_rel('r_cxcit','requiere'   ,$i);
			if($requiere=='DESCUENTO'){
				array_splice($do->data_rel['r_cxcit'],$i,1);
			}
		}
		
		/********************
		 * INICIO VERIFICA PERIODOS SEGUROS
		 * *****************/
		
		if(!($this->datasis->puede(468))){
			$minimos=array();
			$maximos=array();
			$id_concits=array();
			for($i=0;$i < $do->count_rel('r_cxcit');$i++){
				$id_concit   = $do->get_rel('r_cxcit','id_concit'  ,$i);
				$id_conc     = $do->get_rel('r_cxcit','id_conc'    ,$i);
				$frecuencia  = $do->get_rel('r_cxcit','frecuencia' ,$i);
				$freval      = $do->get_rel('r_cxcit','freval'     ,$i);
				$ano         = $do->get_rel('r_cxcit','ano'        ,$i);
				$requiere    = $do->get_rel('r_cxcit','requiere'   ,$i);
				$id_inmueble = $do->get_rel('r_cxcit','id_inmueble',$i);
				$id_vehiculo = $do->get_rel('r_cxcit','id_vehiculo',$i);
				
				$valor      =1*(''.$ano.str_pad($freval,2,0,STR_PAD_LEFT));
				$cadena     ="";
				if($requiere=='INMUEBLE')
				$cadena='INMUEBLE_._'.$id_inmueble.'_._'.$id_conc;
				
				if($requiere=='VEHICULO')
				$cadena='VEHICULO_._'.$id_vehiculo.'_._'.$id_conc;
				
				$id_concits[$cadena][]=$id_concit;
				
				if($frecuencia>0 && ($requiere=='VEHICULO' || $requiere=='INMUEBLE')){
					if(array_key_exists($cadena,$maximos)){
						if($valor>$maximos[$cadena]){
							$maximos[$cadena]=$valor;
						}
					}else{
							$maximos[$cadena]=$valor;
					}
				}
			}
			
			
			if($this->datasis->traevalor('R_CXC_VALIDAFRECUENCIASFALTANTES','N')){
				foreach($maximos as $k=>$v){
					$maximo = explode('_._',$k);
					
					if($maximo[0]=='INMUEBLE'){
						$query="
						SELECT MAX(1*CONCAT(a.ano,LPAD(a.freval,2,0)))
						FROM r_reciboit a
						WHERE a.id_inmueble=".$maximo[1]." AND a.id_conc=".$maximo[2]."";
						//JOIN r_abonosit b ON a.id_recibo=b.recibo
						
						$max = $this->datasis->dameval($query);
						
						$query="
						SELECT c.id,c.ano,IF(c.frecuencia=1,'Año',IF(c.frecuencia=2,'Semestre',IF(c.frecuencia=3,'Trimestre',IF(c.frecuencia=4,'MES','')))) frecuencia,c.freval,c.denomi
						FROM r_concit c
						LEFT JOIN r_reciboit a ON a.id_concit=c.id AND a.id_inmueble=".$maximo[1]."
						WHERE c.id_conc=".$maximo[2]." AND a.id IS NULL
						AND 1*CONCAT(c.ano,LPAD(c.freval,2,0)) < 1*CONCAT(".substr($v,0,4).",LPAD(".substr($v,4,2).",2,0)) 
						AND deleted=0
						";
						
						if($max>0)
						$query.=" AND 1*CONCAT(c.ano,LPAD(c.freval,2,0)) > $max ";
						
						$debecobrar=$this->db->query($query);
						$debecobrar=$debecobrar->result_array();
						
						foreach($debecobrar as $row){
							if(!(in_array($row['id'],$id_concits[$k])))
								$error.="ERROR. Esta omitiendo el cobro de (".$row['denomi']." ".$row['ano']." ".$row['frecuencia']." ".$row['freval'].") para ".$maximo[1].", por favor verifiquelo y empiece de nuevo</br>";
						}
					}
					
					
					
					if($maximo[0]=='VEHICULO'){
						//$where2='';
						//
						//$where ="a.id_vehiculo=".$maximo[1];
						//	
						////$ano=$this->datasis->dameval("SELECT ano FROM r_vehiculo WHERE id=".$maximo[1]);
						////if($ano>0)
						////$where2=" AND c.ano>= $ano";
						//
						//$query="
						//SELECT c.id,c.ano,IF(c.frecuencia=1,'Año',IF(c.frecuencia=2,'Semestre',IF(c.frecuencia=3,'Trimestre',IF(c.frecuencia=4,'MES','')))) frecuencia,c.freval,c.denomi
						//FROM r_concit c
						//LEFT JOIN r_reciboit a ON a.id_concit=c.id AND $where
						//LEFT JOIN r_abonosit b ON a.id_recibo=b.recibo 
						//WHERE c.id_conc=".$maximo[2]." AND a.id IS NULL
						//AND 1*CONCAT(c.ano,LPAD(c.freval,2,0)) < 1*CONCAT(".substr($v,0,4).",LPAD(".substr($v,4,2).",2,0)) 
						//AND deleted=0
						//";
						//
						//$debecobrar=$this->db->query($query);
						//$debecobrar=$debecobrar->result_array();
						//
						//foreach($debecobrar as $row){
						//	if(!(in_array($row['id'],$id_concits[$k])))
						//		$error.="ERROR. Esta omitiendo el cobro de (".$row['denomi']." ".$row['ano']." ".$row['frecuencia']." ".$row['freval'].") para ".$maximo[1].", por favor verifiquelo y empiece de nuevo</br>";
						//}
					}
				}
			}
		}
		
		/********************
		 * FIN VERIFICA PERIODOS SEGUROS
		 * *****************/
		
		$total=0;$interes=0;
		for($i=0;$i < $do->count_rel('r_cxcit');$i++){
			$requiere   = $do->get_rel('r_cxcit','requiere'  ,$i);
			$id_conc    = $do->get_rel('r_cxcit','id_conc'   ,$i);
			$id_concit  = $do->get_rel('r_cxcit','id_concit' ,$i);
			$frecuencia = $do->get_rel('r_cxcit','frecuencia',$i);
			$freval     = $do->get_rel('r_cxcit','freval'    ,$i);
			$ano        = $do->get_rel('r_cxcit','ano'       ,$i);
			
			if($requiere=='INMUEBLE'){
				$id_inmueble = $do->get_rel('r_cxcit','id_inmueble',$i);
				if($id_inmueble>0){
					$inmueble = $this->datasis->damerow("SELECT * FROM r_v_inmueble WHERE id=$id_inmueble");
					$do->set_rel('r_cxcit','i_catastro'     ,$inmueble['catastro'    ],$i);
					$do->set_rel('r_cxcit','i_id_parroquia' ,$inmueble['id_parroquia'],$i);
					$do->set_rel('r_cxcit','i_parroquia'    ,$inmueble['parroquia'   ],$i);
					$do->set_rel('r_cxcit','i_id_zona'      ,$inmueble['id_zona'     ],$i);
					$do->set_rel('r_cxcit','i_zona'         ,$inmueble['zona'        ],$i);
					$do->set_rel('r_cxcit','i_dir1'         ,$inmueble['dir1'],$i);
					$do->set_rel('r_cxcit','i_dir2'         ,$inmueble['dir2'],$i);
					$do->set_rel('r_cxcit','i_dir3'         ,$inmueble['dir3'],$i);
					$do->set_rel('r_cxcit','i_dir4'         ,$inmueble['dir4'],$i);
					
					if($inmueble['id_contribu']!=$id_contribu)
						$error.="<div class='alert' >Error el Inmueble $id_inmueble no pertenece al contribuyente</div>";
				}else{
					$error.="<div class='alert' >Error. debe seleccionar un inmuble</div>";
				}
			}
			
			if($requiere=='VEHICULO'){
				$id_vehiculo = $do->get_rel('r_cxcit','id_vehiculo',$i);
				if($id_vehiculo>0){
				$vehiculo = $this->datasis->damerow("SELECT * FROM r_v_vehiculo WHERE id=$id_vehiculo");
				$do->set_rel('r_cxcit','v_placa'   ,$vehiculo['placa'    ],$i);
				$do->set_rel('r_cxcit','v_marca'   ,$vehiculo['marca'    ],$i);
				$do->set_rel('r_cxcit','v_modelo'  ,$vehiculo['modelo'   ],$i);
				
				if($vehiculo['id_contribu']!=$id_contribu)
					$error.="<div class='alert' >Error el Vehiculo $id_vehiculo no pertenece al contribuyente</div>";
				}else{
					$error.="<div class='alert' >Error. Debe seleccionar un Vehiculo</div>";
				}
			}
			
			if($requiere=='PUBLICIDAD'){
				$id_publicidad = $do->get_rel('r_cxcit','id_publicidad',$i);
				if($id_publicidad>0){
					$publicidad = $this->datasis->damerow("SELECT * FROM r_v_publicidad WHERE id=$id_publicidad");
					$do->set_rel('r_cxcit','id_publicidad'   ,$publicidad['id'          ],$i);
					$do->set_rel('r_cxcit','p_id_tipo'       ,$publicidad['id_tipo'     ],$i);
					$do->set_rel('r_cxcit','p_tipo_descrip'  ,$publicidad['descrip'     ],$i);
					$do->set_rel('r_cxcit','i_id_parroquia'  ,$publicidad['id_parroquia'],$i);
					$do->set_rel('r_cxcit','i_parroquia'     ,$publicidad['parroquia'   ],$i);
					$do->set_rel('r_cxcit','i_id_zona'       ,$publicidad['id_zona'     ],$i);
					$do->set_rel('r_cxcit','i_zona'          ,$publicidad['zona'        ],$i);
					$do->set_rel('r_cxcit','i_dir1'          ,$publicidad['dir1'        ],$i);
					$do->set_rel('r_cxcit','i_dir2'          ,$publicidad['dir2'        ],$i);
					$do->set_rel('r_cxcit','i_dir3'          ,$publicidad['dir3'        ],$i);
					$do->set_rel('r_cxcit','i_dir4'          ,$publicidad['dir4'        ],$i);
					
					if($publicidad['id_contribu']!=$id_contribu)
						$error.="<div class='alert' >Error. La publicidad $id_publicidad no pertenece al contribuyente</div>";
				}else{
					$error.="<div class='alert' >Error. Debe seleccionar un Vehiculo</div>";
				}
			}
			
			$monto = $do->get_rel('r_cxcit','monto',$i);
			$total+=$monto;
			
			if(!($id_concit>0))
				$error.="Error. Debe Seleccionar un Concepto</br>";
			
			if($id_concit){
				$r_v_conc = $this->datasis->damerow("SELECT id_conc,partida,denopart,denomiconc,expira FROM r_v_conc WHERE id=$id_concit");
				$do->set_rel('r_cxcit','id_conc',$r_v_conc['id_conc'],$i);
				$do->set_rel('r_cxcit','partida',$r_v_conc['partida'],$i);
				$do->set_rel('r_cxcit','partida_denomi',$r_v_conc['denopart']     ,$i);
				$do->set_rel('r_cxcit','conc_denomi'   ,$r_v_conc['denomiconc']   ,$i);
				$do->set_rel('r_cxcit','expira'        ,$r_v_conc['expira'    ]   ,$i);
			}
			
			//echo "inmueble:".$id_inmueble."</br>";
			/* CALCULO DE INTERESES*/
			foreach($intereses as $k=>$v){
				$a                       = eval($intereses[$k]['formula']);
				$intereses[$k]['monto'] += $a;
			}
			
			/* CALCULO DE DESCUENTOS*/
			foreach($descuentos as $k=>$v){
				
				$a                        = eval($descuentos[$k]['formula']);
				echo $descuentos[$k]['formula'].":".$a."</br>"; 
				$descuentos[$k]['monto'] += $a;
			}
		}
		
		/*
		 * CREA ITEM DE INTERESES
		 */
		foreach($intereses as $k=>$v){
			if($intereses[$k]['monto'] >0){
				$i++;
				$do->set_rel('r_cxcit','monto'          ,$intereses[$k]['monto'           ],$i);
				$do->set_rel('r_cxcit','id_conc'        ,$intereses[$k]['id_conc'         ],$i);
				$do->set_rel('r_cxcit','id_concit'      ,$intereses[$k]['id_concit'       ],$i);
				$do->set_rel('r_cxcit','denomi'         ,$intereses[$k]['denomi'          ],$i);
				$do->set_rel('r_cxcit','requiere'       ,$intereses[$k]['requiere'        ],$i);
				$do->set_rel('r_cxcit','partida'        ,$intereses[$k]['partida'         ],$i);
				$do->set_rel('r_cxcit','partida_denomi' ,$intereses[$k]['partida_denomi'  ],$i);
				$do->set_rel('r_cxcit','conc_denomi'    ,$intereses[$k]['conc_denomi'     ],$i);
				$total+=$intereses[$k]['monto' ];
			}
		}
		
		/*
		 * CREA ITEM DE DESCUENTOS
		 */
		foreach($descuentos as $k=>$v){
			if($descuentos[$k]['monto'] <0){
				$i++;
				$do->set_rel('r_cxcit','monto'          ,$descuentos[$k]['monto'           ],$i);
				$do->set_rel('r_cxcit','id_conc'        ,$descuentos[$k]['id_conc'         ],$i);
				$do->set_rel('r_cxcit','id_concit'      ,$descuentos[$k]['id_concit'       ],$i);
				$do->set_rel('r_cxcit','denomi'         ,$descuentos[$k]['denomi'          ],$i);
				$do->set_rel('r_cxcit','requiere'       ,$descuentos[$k]['requiere'        ],$i);
				$do->set_rel('r_cxcit','partida'        ,$descuentos[$k]['partida'         ],$i);
				$do->set_rel('r_cxcit','partida_denomi' ,$descuentos[$k]['partida_denomi'  ],$i);
				$do->set_rel('r_cxcit','conc_denomi'    ,$descuentos[$k]['conc_denomi'     ],$i);
				$total+=$descuentos[$k]['monto' ];
			}
		}
		
		print_r($do->get_all());
		
		
		
		//exit();
		
		if(empty($error)){
			$do->set('monto',$total);
			if(empty($numero)){
			}
		}
		
		if(!empty($error)){
			$do->error_string=$error;
			$do->error_message_ar['pre_ins']=$error;
			$do->error_message_ar['pre_upd']=$error;
			return false;
		}
	}
	
	function dameinteres($base,$frecibo,$ano,$frecuencia,$freval,$modo=1){
		/*
		 * Modo 1: Calcula Intereses Sobre Intereses
		 * Modo 2: Calcula Sin Intereses Sobre intereses
		 * */
		
		
		switch($frecuencia){
			case 1:{
				$tasas="
				SELECT ano,ROUND(AVG(monto*1.2),2) tasa,MAX(LAST_DAY(CONCAT(ano,'-',mes,'-01'))) fin
				FROM r_interes
				GROUP BY ano
				HAVING fin<$frecibo AND fin >='$ano-12-31'
				ORDER BY ano
				";
				$tasas    =$this->db->query($tasas);
				$tasas    =$tasas->result_array();
			
				$impuesto=0;
				foreach($tasas as $row){
					if($modo==1)
						$impuesto+=($base+$impuesto)*$row['tasa']/100;
					elseif($modo==2)
						$impuesto+=($base)*$row['tasa']/100;
				}
			}break;
			case 2:{
				$tasas="
				SELECT ano,IF(mes>=1 AND mes<=6,1,2) semestre,ROUND(AVG(monto*1.2)/2,2) tasa,MAX(LAST_DAY(CONCAT(ano,'-',mes,'-01'))) fin
				FROM r_interes
				GROUP BY ano,semestre
				HAVING fin<$frecibo AND fin >=IF($freval=2,'$ano-12-31','$ano-06-30')
				ORDER BY ano,semestre
				";
				
				$tasas    =$this->db->query($tasas);
				$tasas    =$tasas->result_array();
				
				
				$impuesto=0;
				foreach($tasas as $row){
					if($modo==1){
						//echo "$ano	SEMESTRE $freval	AL ".$row['fin'].";$base; $impuesto;".($row['tasa']/100).";".(($base+$impuesto)*$row['tasa']/100)."</br>";
						$impuesto+=($base+$impuesto)*$row['tasa']/100;
						
					}elseif($modo==2){
						$impuesto+=($base)*$row['tasa']/100;
					}
						
					
					
				}
			}break;
			case 3:{
			}break;
			case 4:{
				$tasas="
				SELECT ano,mes,ROUND(monto*1.2,2) tasa,LAST_DAY(CONCAT(ano,'-',mes,'-01')) fin
				FROM r_interes
				HAVING fin<$frecibo AND fin >='$ano-12-31'
				ORDER BY ano,mes
				";
				$tasas    =$this->db->query($tasas);
				$tasas    =$tasas->result_array();
				
				$impuesto=0;
				foreach($tasas as $row){
					if($modo==1)
						$impuesto+=($base+$impuesto)*$row['tasa']/100;
					elseif($modo==2)
						$impuesto+=($base)*$row['tasa']/100;
				}
			}break;
		}
		
		return $impuesto;
	}
	
	function prueba(){
		
		echo br().$fechainicial=$this->ultimo_pago(2,1);
		$fechainicial = new DateTime($fechainicial);
		$fechafinal = new DateTime('2015-07-29');
		$diferencia = $fechainicial->diff($fechafinal);
		echo br().$meses = ( $diferencia->y * 12 ) + $diferencia->m;

	}
	
	function ultimo_pago($id_conc,$id_inmueble=null,$id_vehiculo=null){
		$id_conce    = $this->db->escape($id_conc    );
		$id_inmueblee= $this->db->escape($id_inmueble);
		$id_vehiculoe= $this->db->escape($id_vehiculo);
		$where       ='';
				
		$query="SELECT MAX(LAST_DAY(1*CONCAT(a.ano,LPAD(a.freval,2,0),'01'))) 
				FROM r_reciboit a
				JOIN r_abonosit b ON a.id_recibo=b.recibo
				WHERE  a.id_conc=$id_conce ";
				
		if($id_inmueble)
		$query.=$where=" AND a.id_inmueble=$id_inmueblee ";
		if($id_vehiculo)
		$query.=$where=" AND a.id_vehiculo=$id_vehiculoe ";
		
		$val = $this->datasis->dameval($query);
		
		if(!$val){
			$query="SELECT MIN(LAST_DAY(1*CONCAT(ano,LPAD(freval,2,0),'01'))) FROM r_concit WHERE id_conc=$id_conce ";
			$val = $this->datasis->dameval($query);
		}
		
		return $val;
	}
	
	function damedeudainmueble($id_contribu,$idreq){
		
		$where ='';
		$where1='';
		if($idreq>0){
			$where .=" AND id_inmueble=$idreq ";
			$where1.=" AND a.id=$idreq ";
		}
		
		
		$query="
		select b.id_conc,`b`.`id` AS `id`,`b`.`ano` AS `ano`,`b`.`acronimo` AS `acronimo`,`b`.`denomi` AS `denomi`,`b`.`requiere` AS `requiere`,`a`.`id` AS `id_inmueble`,`a`.`catastro` AS `catastro`,NULL AS `id_vehiculo`,NULL AS `placa`,`a`.`id_contribu` AS `id_contribu`,CONCAT_WS('',a.dir1,dir2,dir3,dir4) AS `observa`,`b`.`formula` AS `formula` ,b.frecuencia,b.freval,b.modo
		from `r_inmueble` `a` 
		join `r_concit` `b` on 1 = 1
		LEFT JOIN (
			SELECT id_inmueble,id_conc ,MAX(ano) ano,MAX(CONCAT(ano,LPAD(freval,2,0))) anofreval
			FROM r_reciboit
			JOIN r_recibo ON r_reciboit.id_recibo=r_recibo.id
			WHERE id_inmueble>0 AND id_contribu=$id_contribu $where
			GROUP BY  id_inmueble,id_conc
		)maximo ON b.id_conc=maximo.id_conc AND a.id=maximo.id_inmueble 
		where ((`b`.`requiere` = 'INMUEBLE') and (CONCAT(b.ano,LPAD(b.freval,2,0)) > 0) ) 
		AND a.id_contribu=$id_contribu
		AND b.deleted=0
		AND (maximo.ano>0 OR maximo.ano IS NULL)
		AND CONCAT(b.ano,LPAD(b.freval,2,0))>IF(maximo.id_inmueble>0,maximo.anofreval,0)
		$where1
		";
		return $query;
	}
	
	function damedeudapublicidad($id_contribu,$idreq){
		
		$where ='';
		$where1='';
		if($idreq>0){
			$where .=" AND id_publicidad=$idreq ";
			$where1.=" AND a.id=$idreq ";
		}
		
		$query="
		SELECT b.id_conc,`b`.`id` AS `id`,`b`.`ano` AS `ano`,`b`.`acronimo` AS `acronimo`,`b`.`denomi` AS 
		`denomi`,`b`.`requiere` AS `requiere`,null AS `id_inmueble`,null AS `catastro`,NULL AS
		`id_vehiculo`,NULL AS `placa`,`a`.`id_contribu` AS `id_contribu`,CONCAT_WS(' ',a.ancho,'X',a.alto) AS `observa`,`b`.`formula` AS `formula` ,b.frecuencia,b.freval,b.modo,a.id id_publicidad
		from `r_publicidad` `a` 
		join `r_concit` `b` on 1 = 1
		LEFT JOIN (
			SELECT id_publicidad,id_conc ,MAX(ano) ano,MAX(CONCAT(ano,LPAD(freval,2,0))) anofreval
			FROM r_reciboit
			JOIN r_recibo ON r_reciboit.id_recibo=r_recibo.id
			WHERE id_publicidad>0 AND id_contribu=$id_contribu $where
			GROUP BY  id_publicidad,id_conc
		)maximo ON b.id_conc=maximo.id_conc AND a.id=maximo.id_publicidad
		where ((`b`.`requiere` = 'PUBLICIDAD') and (CONCAT(b.ano,LPAD(b.freval,2,0)) > 0) ) 
		AND a.id_contribu=$id_contribu
		AND b.deleted=0
		AND (maximo.ano>0 OR maximo.ano IS NULL)
		AND CONCAT(b.ano,LPAD(b.freval,2,0))>IF(maximo.id_publicidad>0,maximo.anofreval,0)
		$where1
		";
		
		return $query;
	}
	
	function damedeudavehiculo($id_contribu,$idreq=null){
		
		$where ='';
		$where1='';
		if($idreq>0){
			$where .=" AND id_vehiculo=$idreq ";
			$where1.=" AND a.id=$idreq ";
		}
		
		$query="
		select b.id_conc,`b`.`id` AS `id`,`b`.`ano` AS `ano`,`b`.`acronimo` AS `acronimo`,`b`.`denomi` AS `denomi`,`b`.`requiere` AS `requiere`,NULL AS `id_inmueble`,NULL AS `catastro`,`a`.`id` AS `id_vehiculo`,`a`.`placa` AS `placa`,`a`.`id_contribu` AS `id_contribu`,'' AS `observa`,`b`.`formula` AS `formula` ,b.frecuencia,b.freval,b.modo,null id_publicidad
		from `r_vehiculo` `a` 
		join `r_concit` `b` on 1 = 1
		LEFT JOIN (
			SELECT id_vehiculo,id_conc ,MAX(ano) ano,MAX(CONCAT(ano,LPAD(freval,2,0))) anofreval
			FROM r_reciboit
			JOIN r_recibo ON r_reciboit.id_recibo=r_recibo.id
			WHERE id_vehiculo>0 AND id_contribu=$id_contribu $where
			GROUP BY  id_vehiculo,id_conc
		)maximo ON b.id_conc=maximo.id_conc AND a.id=maximo.id_vehiculo 
		where ((`b`.`requiere` = 'VEHICULO') and (CONCAT(b.ano,LPAD(b.freval,2,0)) > 0) ) 
		AND b.ano >= a.ano
		AND a.id_contribu=$id_contribu
		AND b.deleted=0
		AND maximo.ano>0
		AND CONCAT(b.ano,LPAD(b.freval,2,0))>IF(maximo.id_vehiculo>0,maximo.anofreval,0)
		$where1
		
		UNION ALL 
		
		select b.id_conc,`b`.`id` AS `id`,`b`.`ano` AS `ano`,`b`.`acronimo` AS `acronimo`,`b`.`denomi` AS `denomi`,`b`.`requiere` AS `requiere`,NULL AS `id_inmueble`,NULL AS `catastro`,`a`.`id` AS `id_vehiculo`,`a`.`placa` AS `placa`,`a`.`id_contribu` AS `id_contribu`,'' AS `observa`,`b`.`formula` AS `formula` ,b.frecuencia,b.freval,b.modo,null id_publicidad
		from `r_vehiculo` `a` 
		join `r_concit` `b` on 1 = 1
		where `b`.`requiere` = 'VEHICULO'
		AND a.id_contribu=$id_contribu
		AND b.deleted=0
		AND (SELECT count(*) FROM r_reciboit WHERE r_reciboit.id_vehiculo=a.id)=0
		AND (b.ano=0 OR b.ano=(SELECT valor FROM valores WHERE nombre='EJERCICIO'))
		$where1
		";
		
		return $query;
	}
	
	function damedeudapatente($id_contribu){
		$query="
		select b.id_conc,`b`.`id` AS `id`,`b`.`ano` AS `ano`,`b`.`acronimo` AS `acronimo`,`b`.`denomi` AS `denomi`,`b`.`requiere` AS `requiere`,NULL AS `id_inmueble`,NULL AS `catastro`,NULL AS `id_vehiculo`,NULL AS `placa`,`a`.`id` AS `id_contribu`,'' AS `observa`,`b`.`formula` AS `formula` ,b.frecuencia,b.freval,b.modo,null id_publicidad
		from (((`r_contribu` `a` 
		join `r_concit` `b` on((1 = 1))) 
		left join `r_reciboit` `c` on((`b`.`id` = `c`.`id_concit`))) 
		left join `r_recibo` `d` on(((`c`.`id_recibo` = `d`.`id`) and (`a`.`id` = `d`.`id_contribu`)))) 
		where ((`b`.`requiere` = 'PETENTE') and (`b`.`ano` > 0) and isnull(`d`.`id`) and a.patente='S')
		AND a.id=$id_contribu
		AND b.deleted=0
		";
		return $query;
	}
	
	function damedeudatodos($id_contribu){
		$query="select b.id_conc,`b`.`id` AS `id`,`b`.`ano` AS `ano`,`b`.`acronimo` AS `acronimo`,`b`.`denomi` AS `denomi`,`b`.`requiere` AS `requiere`,NULL AS `id_inmueble`,NULL AS `catastro`,NULL AS `id_vehiculo`,NULL AS `placa`,`a`.`id` AS `id_contribu`,'' AS `observa`,`b`.`formula` AS `formula` ,b.frecuencia,b.freval,b.modo,null id_publicidad
		from (((`r_contribu` `a` 
		join `r_concit` `b` on((1 = 1))) 
		left join `r_reciboit` `c` on((`b`.`id` = `c`.`id_concit`))) 
		left join `r_recibo` `d` on(((`c`.`id_recibo` = `d`.`id`) and (`a`.`id` = `d`.`id_contribu`)))) 
		where ((length(`b`.`requiere`) = 0) and (`b`.`ano` > 0) and isnull(`d`.`id`))
		AND a.id=$id_contribu
		AND b.deleted=0";
		return $query;
	}
	
	function damedeuda(){
		$id_contribu = $this->input->post('id_contribu');
		$tipo        = $this->input->post('tipo');
		$idreq       = $this->input->post('id_requiere');
		$querys=array();
		
		if($idreq>0)
			$idreq=$idreq;
		else
			$idreq=null;
		
		if($tipo=='INMUEBLE' || empty($tipo))
			$querys[]=$this->damedeudainmueble($id_contribu,$idreq);
			
		if($tipo=='PATENTE' || empty($tipo))
			$querys[]=$this->damedeudapatente($id_contribu);
		
		if($tipo=='VEHICULO' || empty($tipo))
			$querys[]=$this->damedeudavehiculo($id_contribu,$idreq);
			
		if($tipo=='PUBLICIDAD' || empty($tipo))
			$querys[]=$this->damedeudapublicidad($id_contribu,$idreq);
			
		if($tipo=='TODOS' || empty($tipo))
			$querys[]=$this->damedeudatodos($id_contribu);
		
		$query=implode(" UNION ALL ",$querys);
		
		$query.=" ORDER BY id_inmueble,id_vehiculo,ano,frecuencia, freval ";
		
		
		$mSQL   = $this->db->query($query);
		$arreglo= $mSQL->result_array($query);
		foreach($arreglo as $key=>$row){
			$id=null;
			switch($row['requiere']){
					case 'INMUEBLE'  :$id=$row['id_inmueble'];break;
					case 'VEHICULO'  :$id=$row['id_vehiculo'];break;
					case 'PUBLICIDAD':$id=$row['id_publicidad'];break;
			}
			$arreglo[$key]['monto']=$this->calculamonto($row['formula'],$row['ano'],$id,$id_contribu);
		}
		
		foreach($arreglo as $key=>$value)
			foreach($value as $key2=>$value2) 
			$arreglo[$key][$key2] = ($value2);

		echo json_encode($arreglo);
	}
	
	function damemonto(){
		$id_concit   = $this->input->post('id_concit'  );
		$id          = $this->input->post('id'         );
		$id_contribu = $this->input->post('id_contribu');
		$base        = $this->input->post('base'       );
		
		$formula     = $this->datasis->damerow("SELECT formula,ano FROM r_concit WHERE id=$id_concit");
		echo $this->calculamonto($formula['formula'],$formula['ano'],$id,$id_contribu,$base);
	}
	
	function calculamonto($formula,$ano=null,$id=null,$id_contribu=null,$base=null){
		$XX=array();
		$anoe=$this->db->escape($ano);
		
		if(!(strpos( $formula,'XX_UTRIBUACTUAL')===false)){
			$XX['XX_UTRIBUACTUAL']=$this->datasis->dameval("SELECT valor FROM utribu WHERE ano=(SELECT MAX(ano) FROM utribu)");
		}
		
		if(!(strpos( $formula,'XX_UTRIBUANO')===false)){
			$XX['XX_UTRIBUANO']=$this->datasis->dameval("SELECT valor FROM utribu WHERE ano=$anoe");
		}
		
		if(!(strpos( $formula,'XX_INMUEBLE_')===false)){
			$query="SELECT zona,techo,mt2,monto,zona_monto,clase_monto,tipoi,clasea_monto FROM r_v_inmueble WHERE id=$id";
			$row=$this->datasis->damerow($query);
			foreach($row as $k=>$v)
				$XX["XX_INMUEBLE_".strtoupper($k)]=$v;
		}

		if(!(strpos( $formula,'XX_VEHICULO_')===false)){
			$query="SELECT a.capacidad,a.ejes,a.ano,a.peso,b.monto clase_monto
			FROM r_vehiculo a
			JOIN rv_clase b ON a.id_clase=b.id
			WHERE a.id=$id";
			$row=$this->datasis->damerow($query);
			foreach($row as $k=>$v)
				$XX["XX_VEHICULO_".strtoupper($k)]=$v;
		}
		
		if(!(strpos( $formula,'XX_PUBLICIDAD_')===false)){
			$query="SELECT alto,ancho,dimension,monto tipo_monto,rp_tipos.codigo tipo_codigo FROM r_publicidad JOIN rp_tipos ON  r_publicidad.id_tipo=rp_tipos.id WHERE r_publicidad.id=$id";
			$row=$this->datasis->damerow($query);
			foreach($row as $k=>$v){
				$XX["XX_PUBLICIDAD_".strtoupper($k)]=$v;
			}
		}
		
		if(!(strpos( $formula,'XX_CONTRIBU_')===false) && $id_contribu>0){
			$query="SELECT id_negocio,negocio_monto,negocio_monto2 FROM r_v_contribu WHERE id=$id_contribu";
			$row=$this->datasis->damerow($query);
			foreach($row as $k=>$v)
				$XX["XX_CONTRIBU_".strtoupper($k)]=$v;
		}
		
		if(!(strpos( $formula,'XX_BASE')===false)){
				$XX["XX_BASE"]=$base;
		}
		
		$monto=$this->evaluaformula($formula,$XX);
		return $monto;
	}
	
	function evaluaformula($formula,$XX){
		foreach($XX as $k=>$v){
			$formula=str_replace($k,'$'.$k,$formula);
			$formula=str_replace("$$","$",$formula);
			$$k=$v;
		}
		
		return eval($formula);
	}
	
	function dameconc(){
		$query  ="SELECT id,ano,acronimo,denomi,requiere,modo,frecuencia,IF(frecuencia=1,'Año',IF(frecuencia=2,'Semestre',IF(frecuencia=3,'Trimestre',IF(frecuencia=4,'MES','')))) frecuenciatexto
		,freval FROM r_v_conc";
		$mSQL   = $this->db->query($query);
		$arreglo= $mSQL->result_array($query);
		
		foreach($arreglo as $key=>$value)
			foreach($value as $key2=>$value2) 
			$arreglo[$key][$key2] = ($value2);

		echo json_encode($arreglo);
	}
	
	function anular($id){
		$error='';
		if($id>0){
			$id_abono = $this->datasis->dameval("SELECT b.id FROM r_abonosit a JOIN r_abonos b ON a.abono=b.id WHERE a.recibo=$id LIMIT 1");
			if($id_abono>0){
					$error .='El Recibo esta Cobrado, debe eliminar la cobranza primero';
			}else{
				$this->db->query("DELETE FROM r_reciboit WHERE id_recibo=$id");
				$this->db->query("UPDATE r_recibo SET id_contribu=-1, rifci='ANULADO',nombre='ANULADO',monto=0 WHERE id=$id ");
				$this->db->query("INSERT INTO r_reciboit (id,id_recibo,id_conc,id_concit,denomi,monto) VALUES('',$id,-1,-1,'ANULADO',0)");
			}
		}else{
			$error .='Faltan parametros';
		}
		
		if(empty($error)){
			logusu('r_recibo',"anulo recibo ref $id");
			redirect($this->url."/dataedit/show/".$id);
		}else{
			$error="<div class='alert'>".$error."</div>";
			logusu('r_recibo',"anulo recibo ref $id con error $error");
			$data['content'] = $error.anchor($this->url."/dataedit/show/$id",'Regresar');
			$data['title']   = " Recibos ";
			$data["head"]    = $this->rapyd->get_head();
			$this->load->view('view_ventanas', $data);
		}
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
		
		$tablas ='<table width=\'100%\'>';
		$tablas.='<tr><td scrollbar="yes" width="100%" height="50px">';
		$tablas.=str_replace('mainbackgroundtable','',$grid->output);
		$tablas.='</td></tr>';
		$tablas.='<tr><td scrollbar="yes" width="100%" height="50px">';
		$tablas.=str_replace('mainbackgroundtable','',$grid2->output);
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
	
	function dataprint_solvencia($tipo='A',$st,$uid){
		$this->rapyd->load('dataedit');

		$edit = new DataEdit('Imprimir Solvencia', 'r_recibo');
		//$id=$edit->get_from_dataobjetct('id');
		//$urlid=$edit->pk_URI();
		$id   =$uid;
		$urlid=$uid;
		$ide  =$this->db->escape($id);
		
		if($tipo=='A'){
			$R_RECIBO_IDCONCIT_SOLVENCIA_A = $this->datasis->traevalor("R_RECIBO_IDCONCIT_SOLVENCIA_A",26,"ID DE CONCEPTOIT DE SOLCENVIA A");
			$url=site_url('formatos/descargar/R_SOLVENCI/A/'.$urlid);
			$c = $this->datasis->dameval("SELECT COUNT(*) FROM r_reciboit WHERE id_concit=".$R_RECIBO_IDCONCIT_SOLVENCIA_A." AND id_recibo=$ide");
		}else{
			$R_RECIBO_IDCONCIT_SOLVENCIA_B = $this->datasis->traevalor("R_RECIBO_IDCONCIT_SOLVENCIA_B",16,"ID DE CONCEPTOIT DE SOLCENVIA B");
			$url=site_url('formatos/descargar/R_SOLVENCI/B/'.$urlid);
			$c = $this->datasis->dameval("SELECT COUNT(*) FROM r_reciboit WHERE id_concit=".$R_RECIBO_IDCONCIT_SOLVENCIA_B." AND id_recibo=$ide");
		}
		
		$edit->back_url = site_url($this->url.'dataedit/show/'.$uid);

		$edit->back_save   = true;
		$edit->back_delete = true;
		$edit->back_cancel = true;
		$edit->back_cancel_save   = true;
		$edit->back_cancel_delete = true;
		//$edit->on_save_redirect   = false;

		$edit->post_process('update','_post_print_solvencia_update');

		//$edit->container = new containerField('impresion','La descarga se realizara en 1 segundos, en caso de no hacerlo haga click '.anchor('formatos/descargar/R_SOLVENCIA/'.$urlid,'aqui'));

		if($tipo=='A' && $c>0){
			$edit->solvencia = new inputField('Solvencia N&uacute;mero A','solvencia');
			$edit->solvencia->rule        ='max_length[12]|required';
			$edit->solvencia->size        =14;
			$edit->solvencia->maxlength   =12;
			$edit->solvencia->autocomplete=false;
		}elseif($tipo=='B' && $c>0){
			$edit->solvenciab = new inputField('Solvencia N&uacute;mero B','solvenciab');
			$edit->solvenciab->rule        ='max_length[12]|required';
			$edit->solvenciab->size        =14;
			$edit->solvenciab->maxlength   =12;
			$edit->solvenciab->autocomplete=false;
		}
		
		$edit->numero = new inputField('Recibo N&uacute;mero','numero');
		$edit->numero->rule        ='max_length[12]|required';
		$edit->numero->size        =14;
		$edit->numero->maxlength   =12;
		$edit->numero->autocomplete=false;
		$edit->numero->mode='autohide';

		$edit->id = new inputField('Ref','numero');
		$edit->id->rule='max_length[8]';
		$edit->id->mode='autohide';
		$edit->id->size =10;
		$edit->id->maxlength =8;

		$edit->fecha = new dateField('Fecha','fecha');
		$edit->fecha->rule = 'chfecha';
		$edit->fecha->mode = 'autohide';
		$edit->fecha->size = 10;
		$edit->fecha->maxlength =8;
		
		$edit->nombre = new inputField('Nombre','nombre');
		$edit->nombre->rule='required|max_length[200]';
		$edit->nombre->size =50;
		$edit->nombre->maxlength =200;
		$edit->nombre->autocomplete=false;
		$edit->nombre->mode='autohide';

		$edit->rifci = new inputField('Rif/CI','rifci');
		$edit->rifci->rule      ='required|max_length[13]';
		$edit->rifci->size      =15;
		$edit->rifci->maxlength =13;
		$edit->rifci->mode='autohide';

		$edit->monto = new inputField('Monto','monto');
		$edit->monto->rule='max_length[12]|numeric';
		$edit->monto->css_class='inputnum';
		$edit->monto->size =14;
		$edit->monto->showformat='decimal';
		$edit->monto->mode='autohide';
		$edit->monto->maxlength =12;

		if($c>0)
		$edit->buttons('save');
		$edit->buttons( 'undo','back');
		$edit->build();

		if($c>0){
			$script= '<script type="text/javascript" >
			$(function() {
				setTimeout(\'window.location="'.$url.'"\',01);
			});
			</script>';
			$title='IMPRIMIR SOLVENCIA '.$tipo;
		}else{
				$script='';
				$title ='ERROR. DEBE PAGAR LA SOLVENCIA TIPO '.$tipo.' PARA IMPRIMIRLA';
		}

		
		$data['content'] = "<div class='alert'>".'ERROR. DEBE PAGAR LA SOLVENCIA TIPO '.$tipo.' PARA IMPRIMIRLA'."</div>".$edit->output;
		$data['head']    = $this->rapyd->get_head();
		$data["head"]    = $this->rapyd->get_head().script('jquery.js').script('jquery-ui.js').script("plugins/jquery.numeric.pack.js").script('plugins/jquery.meiomask.js').style('vino/jquery-ui.css');
		$data['script'] .= $script;
		$data['title']   = $title;
		$this->load->view('view_ventanas', $data);
	}
	
	function _post_print_solvencia_update($do){
			$id =$do->get('id');
			redirect($this->url."dataedit/show/$id");
	}
	
	function inmueble_cant(){
		$id_contribu = $this->input->post('id_contribu');
		$id_contribue = $this->db->escape($id_contribu);
		$query="SELECT COUNT(*) FROM r_inmueble WHERE id_contribu=$id_contribue ";
		echo $this->datasis->dameval($query);
	}
	
	function inmueble_get(){
		$id_contribu = $this->input->post('id_contribu');
		$id_contribue = $this->db->escape($id_contribu);
		$query="SELECT id,catastro,CONCAT_WS(' ',dir1,dir2,dir3,dir4) direccion FROM r_inmueble WHERE id_contribu=$id_contribue ";
		$query=$this->db->query($query);
		$arreglo= $query->result_array();
		
		foreach($arreglo as $key=>$value)
			foreach($value as $key2=>$value2) 
			$arreglo[$key][$key2] = ($value2);

		echo json_encode($arreglo);
	}
	
	function vehiculo_cant(){
		$id_contribu = $this->input->post('id_contribu');
		$id_contribue = $this->db->escape($id_contribu);
		$query="SELECT COUNT(*) FROM r_vehiculo WHERE id_contribu=$id_contribue ";
		echo $this->datasis->dameval($query);
	}
	
	function vehiculo_get(){
		$id_contribu = $this->input->post('id_contribu');
		$id_contribue = $this->db->escape($id_contribu);
		$query="SELECT id,placa FROM r_vehiculo WHERE id_contribu=$id_contribue ";
		$query=$this->db->query($query);
		$arreglo= $query->result_array();
		
		foreach($arreglo as $key=>$value)
			foreach($value as $key2=>$value2) 
			$arreglo[$key][$key2] = ($value2);

		echo json_encode($arreglo);
	}
	
	function actdeuda($id_cxc){
		$id_cxce = $this->db->escape($id_cxc);
		$this->db->trans_start();

		$query="INSERT INTO r_recibo(id,id_contribu,fecha,numero,rifci,nombre,telefono,monto,id_parroquia,parroquia,id_zona,zona,dir1,dir2,dir3,dir4,razon,solvencia,solvenciab,licores,caja)
		SELECT '',id_contribu,19870602,numero,rifci,nombre,telefono,monto,id_parroquia,parroquia,id_zona,zona,dir1,dir2,dir3,dir4,razon,solvencia,solvenciab,licores,'ACT'
		FROM r_cxc
		WHERE id=$id_cxc";
		
		$this->db->query($query);
		$id_recibo=$this->db->insert_id();
		
		$query="
		INSERT INTO r_reciboit(id,id_recibo,id_concit,id_conc,id_cxcit,id_vehiculo,id_inmueble,id_publicidad,ano,frecuencia,freval,base,monto,observa,acronimo,denomi,i_id_parroquia,i_parroquia,i_id_zona,i_zona,i_dir1,i_dir2,i_dir3,i_dir4,v_placa,i_catastro,requiere,modo,partida,v_marca,v_modelo,partida_denomi,conc_denomi,p_id_tipo,p_tipo_descrip)
		SELECT '', $id_recibo ,id_concit,id_conc,id_cxc,id_vehiculo,id_inmueble,id_publicidad,ano,frecuencia,freval,base,monto,observa,acronimo,denomi,i_id_parroquia,i_parroquia,i_id_zona,i_zona,i_dir1,i_dir2,i_dir3,i_dir4,v_placa,i_catastro,requiere,modo,partida,v_marca,v_modelo,partida_denomi,conc_denomi,p_id_tipo,p_tipo_descrip
		FROM r_cxcit
		WHERE id_cxc=$id_cxc
		";
		
		$this->db->query($query);
		
		$query="INSERT INTO r_abonos(id,estampa) values('',19870602)";
		$this->db->query($query);
		
		$id_abono=$this->db->insert_id();
		
		$query="INSERT INTO r_abonosit(id,abono,recibo) VALUES('',$id_abono,$id_recibo)";
		$this->db->query($query);
		
		$query="INSERT INTO r_mbanc (id,abono,codbanc,tipo_doc,fecha) VALUES ('',$id_abono,'ACT','EF',19870602)";
		$this->db->query($query);
		
		$this->db->trans_complete();
		redirect($this->url."dataedit/show/$id_cxc");
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
		$mSQL="CREATE TABLE `r_recibo` (
		  `id` int(11) NOT NULL AUTO_INCREMENT,
		  `id_contribu` int(11) NOT NULL,
		  `fecha` date NOT NULL,
		  `rifci` varchar(12) DEFAULT NULL,
		  `nombre` varchar(100) DEFAULT NULL,
		  `telefono` varchar(50) DEFAULT NULL,
		  `id_parroquia` int(11) DEFAULT NULL,
		  `parroquia` varchar(100) DEFAULT NULL,
		  `id_zona` int(11) DEFAULT NULL,
		  `zona` varchar(100) DEFAULT NULL,
		  `dir1` varchar(255) DEFAULT NULL,
		  `dir2` varchar(255) DEFAULT NULL,
		  `dir3` varchar(255) DEFAULT NULL,
		  `dir4` varchar(255) DEFAULT NULL,
		  PRIMARY KEY (`id`)
		) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8";
		$this->db->simple_query($mSQL);
		
		$query="
		CREATE TABLE `r_reciboit` (
			`id` INT(11) NOT NULL AUTO_INCREMENT,
			`id_recibo` INT(11) NULL DEFAULT NULL,
			`id_concit` INT(11) NULL DEFAULT NULL,
			`id_conc` INT(11) NULL DEFAULT NULL,
			`id_vehiculo` INT(11) NULL DEFAULT NULL,
			`id_inmueble` INT(11) NULL DEFAULT NULL,
			`ano` INT(11) NULL DEFAULT NULL,
			`monto` DECIMAL(19,2) NULL DEFAULT '0.00',
			`observa` VARCHAR(255) NULL DEFAULT NULL,
			`acronimo` VARCHAR(50) NOT NULL,
			`denomi` VARCHAR(80) NOT NULL,
			`i_id_parroquia` INT(11) NULL DEFAULT NULL,
			`i_parroquia` VARCHAR(100) NULL DEFAULT NULL,
			`i_id_zona` INT(11) NULL DEFAULT NULL,
			`i_zona` VARCHAR(100) NULL DEFAULT NULL,
			`i_dir1` VARCHAR(255) NULL DEFAULT NULL,
			`i_dir2` VARCHAR(255) NULL DEFAULT NULL,
			`i_dir3` VARCHAR(255) NULL DEFAULT NULL,
			`i_dir4` VARCHAR(255) NULL DEFAULT NULL,
			`v_placa` VARCHAR(12) NULL DEFAULT NULL,
			`i_catastro` VARCHAR(20) NULL DEFAULT NULL,
			`requiere` VARCHAR(20) NULL DEFAULT NULL,
			`partida` VARCHAR(20) NULL DEFAULT NULL,
			`v_marca` VARCHAR(50) NULL DEFAULT NULL,
			`v_modelo` VARCHAR(50) NULL DEFAULT NULL,
			`partida_denomi` VARCHAR(100) NULL DEFAULT NULL,
			`conc_denomi` VARCHAR(100) NULL DEFAULT NULL,
			PRIMARY KEY (`id`)
		)
		COLLATE='utf8_general_ci'
		ENGINE=InnoDB
		AUTO_INCREMENT=1
		";
		$this->db->simple_query($mSQL);
		$mSQL="ALTER TABLE `r_recibo` 	ADD COLUMN `caja` INT NULL DEFAULT NULL";
		$this->db->simple_query($mSQL);
		$query="ALTER TABLE `r_recibo` ADD COLUMN `numero` VARCHAR(12) NULL DEFAULT NULL ";
		$this->db->simple_query($query);
		$query="ALTER TABLE `r_recibo` ADD COLUMN `monto` DECIMAL(19,2) NULL DEFAULT '0'";
		$this->db->simple_query($query);
		$query="ALTER TABLE `r_recibo` ADD COLUMN `razon` VARCHAR(255) NULL DEFAULT NULL";
		$this->db->simple_query($query);
		$query="ALTER TABLE `r_recibo` ADD COLUMN `solvencia` VARCHAR(10) NULL DEFAULT NULL ";
		$this->db->simple_query($query);
		$query="ALTER TABLE `r_recibo` ADD COLUMN `solvenciab` VARCHAR(10) NULL DEFAULT NULL ";
		$this->db->simple_query($query);
		$query="ALTER TABLE `r_recibo` ADD COLUMN `licores` VARCHAR(10) NULL DEFAULT NULL";
		$this->db->simple_query($query);
		$query="
		CREATE TABLE `r_otrospagos` (
			`id` BIGINT(20) NOT NULL AUTO_INCREMENT,
			`numero` VARCHAR(15) NULL DEFAULT NULL,
			`fecha` DATE NULL DEFAULT NULL,
			`rifci` VARCHAR(20) NULL DEFAULT NULL,
			`nombre` VARCHAR(255) NULL DEFAULT NULL,
			`concepto` VARCHAR(255) NULL DEFAULT NULL,
			`monto` DECIMAL(19,2) NULL DEFAULT '0.00',
			`observa` TEXT NULL,
			PRIMARY KEY (`id`)
		)
		ENGINE=MyISAM
		";
		$this->db->simple_query($query);
		
		$query="ALTER TABLE `r_otrospagos` 	ADD INDEX `rifci` (`rifci`)";
		$this->db->simple_query($query);
		
		$query="ALTER TABLE `r_cxcit`	ADD COLUMN `frecuencia` SMALLINT NULL DEFAULT '0' AFTER `ano`";
		$this->db->simple_query($query);
		$query="ALTER TABLE `r_cxcit`	ADD COLUMN `freval` SMALLINT NULL DEFAULT NULL AFTER `frecuencia`";
		$this->db->simple_query($query);
		$query="ALTER TABLE `r_cxcit` 	ADD COLUMN `base` DECIMAL(19,2) NULL DEFAULT '0' AFTER `freval`";
		$this->db->simple_query($query);
		$query="ALTER TABLE `r_cxcit` ADD COLUMN `id_publicidad` INT(11) NULL DEFAULT NULL AFTER `id_inmueble`";
		$this->db->simple_query($query);
		$query="ALTER TABLE `r_cxcit` ADD COLUMN `p_id_tipo` INT NULL DEFAULT NULL AFTER `conc_denomi`";
		$this->db->simple_query($query);
		$query="ALTER TABLE `r_cxcit` ADD COLUMN `p_tipo_descrip` VARCHAR(100) NULL DEFAULT NULL AFTER `p_id_tipo`";
		$this->db->simple_query($query);
		$query="ALTER TABLE `r_reciboit` ADD COLUMN `base` DECIMAL(19,2) NULL DEFAULT '0' AFTER `freval`";
		$this->db->simple_query($query);
		$query="ALTER TABLE `r_cxcit` 	ADD COLUMN `modo` VARCHAR(10) NULL DEFAULT NULL AFTER `requiere`";
		$this->db->simple_query($query);
		$query="ALTER TABLE `r_cxcit` ADD COLUMN `expira` CHAR(1) NULL DEFAULT NULL AFTER `p_tipo_descrip`";
		$this->db->simple_query($query);
		$query="ALTER TABLE `r_cxc` ADD COLUMN `estampa` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP AFTER `caja`";
		$this->db->simple_query($query);
	}
}
?>
