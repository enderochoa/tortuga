<?php
require_once(BASEPATH.'application/controllers/validaciones.php');
//almacenes
class bi_moto extends Controller {


	function bi_moto(){
		parent::Controller();
		$this->load->library("rapyd");
		$this->datasis->modulo_id(265,1);
	}

	function index(){
		redirect("bienes/bi_moto/filteredgrid");
	}

	function filteredgrid(){
		$this->rapyd->load("datafilter","datagrid");
		$this->rapyd->uri->keep_persistence();

		$filter = new DataFilter("Filtro por Expediente","bi_moto");

		$filter->espediente = new inputField("Numero expediente", "expediente");
		$filter->espediente->size=20;

		$filter->marca = new inputField("Marca", "marca");
		$filter->marca->size=20;

		$filter->modelo = new inputField("Modelo", "modelo");
		$filter->modelo->size=20;

		$filter->placa = new inputField("Placa", "placa");
		$filter->placa->size=20;

		$filter->buttons("reset","search");
		$filter->build();

		$uri = anchor('bienes/bi_moto/dataedit/show/<#id#>','<#id#>');

		$grid = new DataGrid("Lista de Motos");
		$grid->order_by("expediente","asc");
		$grid->per_page = 20;

		$grid->column("Id"                 ,$uri                      ,"align='center'");
		$grid->column("Expediente"         ,"expediente"              ,"align='center'");
		$grid->column_orderby("Marca"      ,"marca"       ,"marca"    ,"align='left'"  );
		$grid->column_orderby("Modelo"     ,"modelo"      ,"modelo"   ,"align='center'");
		$grid->column_orderby("A&ntilde;o" ,"anio"        ,"anio"     ,"align='center'");
		$grid->column_orderby("Color"      ,"color"       ,"color"    ,"align='center'");
		$grid->column_orderby("Placa"      , "placa"      , "placa"   ,"align='center'");
		$grid->column_orderby("Tipo"       ,"tipo"        ,"tipo"     ,"align='left'"  );
		//		$grid->column_orderby("Duplicar"             ,$uri_2                        ,"align='center'");

		$grid->add("bienes/bi_moto/dataedit/create");
		$grid->build();

		$data['content'] = $filter->output.$grid->output;
		$data['title']   = "Motos";
		$data["head"]    = $this->rapyd->get_head();
		$this->load->view('view_ventanas', $data);
	}

	function dataedit()
	{
		$this->rapyd->load("dataobject","dataedit2");
		
		$link  =site_url("/bienes/common/get_subgrupo/");
		$link2 =site_url("/bienes/common/get_seccion/");

		$script='
		$(function(){
			$(".inputnum").numeric(".");
			$("#grupo").change(function(){
				 $.post("'.$link.'",{ grupo:$(this).val() },function(data){$("#subgrupo").html(data);$("#seccion").html("");})
			});
			
			$("#subgrupo").change(function(){
				 $.post("'.$link2.'",{ grupo:$("#grupo").val(),subgrupo:$("#subgrupo").val() },function(data){$("#seccion").html(data);})
			});
		})
		';
		
		$edit = new DataEdit2("Motos", "bi_moto");
		$edit->back_url = site_url("bienes/bi_moto/filteredgrid");
		$edit->script($script, "create");
		$edit->script($script, "modify");

		$edit->pre_process('insert'  ,'_valida');
		$edit->pre_process('update'  ,'_valida');

		$edit->id= new inputField("Id", "id");
		$edit->id->mode ="autohide";
		$edit->id->when =array('show');
		$edit->id->group="IDENTIFICACION";
		
		$edit->codigo = new inputField("Codigo", "codigo");
		$edit->codigo->size     =10;
		$edit->codigo->maxlength=8;
		$edit->codigo->group    ="IDENTIFICACION";
		
		$edit->grupo = new dropdownField("Grupo","grupo");
		$edit->grupo->rule='required';
		$edit->grupo->option("",""            );
		$edit->grupo->option("2","2 Muebles"  );
		$edit->grupo->option("1","1 Inmuebles");
		$edit->grupo->group="IDENTIFICACION";
		
		
		$grupo   =$edit->getval('grupo');
		$edit->subgrupo = new dropdownField("Sub-Grupo","subgrupo");
		$edit->subgrupo->rule ="required";
		$edit->subgrupo->group="IDENTIFICACION";
		$edit->subgrupo->style='width:650px;';
		if($grupo!==FALSE){
			$edit->subgrupo->options("SELECT codigo, CONCAT_WS(' ',codigo,descrip) v FROM bi_subgrupo WHERE grupo='$grupo' ORDER BY codigo");
		}else{
			$edit->subgrupo->option("","Seleccione un Grupo primero");
		}
		
		$subgrupo=$edit->getval('subgrupo');
		$edit->seccion = new dropdownField("Seccion","seccion");
		$edit->seccion->rule ="required";
		$edit->seccion->group="IDENTIFICACION";
		$edit->seccion->style='width:650px;';
		if($grupo!==FALSE && $subgrupo!==FALSE){
			$edit->seccion->options("SELECT codigo, CONCAT_WS(' ',codigo,descrip) v FROM bi_seccion WHERE grupo='$grupo' AND subgrupo='$subgrupo' ORDER BY codigo");//WHERE 
		}else{
			$edit->seccion->option("","Seleccione un Sub-Grupo primero");
		}
		
		$edit->numero = new inputField("Numero", "numero");
		$edit->numero->size     =5;
		$edit->numero->maxlength=4;
		$edit->numero->group    ="IDENTIFICACION";
		
		$edit->alma = new dropdownField("Almacen", "alma");
		$edit->alma->options("SELECT codigo,CONCAT_WS(' ',codigo,descrip) valor FROM alma WHERE codigo='0000' ");
		$edit->alma->mode = "autohide";
		
		$edit->monto = new inputField("Monto", "monto");
		$edit->monto->size      =15;
		$edit->monto->maxlength =15;
		$edit->monto->css_class ='inputnum';
		$edit->monto->rule      ='numeric';

		$edit->expediente = new inputField("N&uacute;mero de expediente", "expediente");
		$edit->expediente->size = 10;
		$edit->expediente->maxlength=4;
		//$edit->expediente->rule="required";

		$edit->marca = new inputField("Marca", "marca");
		$edit->marca->size =30;
		$edit->marca->maxlength=30;
		//$edit->marca->rule="required";

		$edit->modelo = new inputField("Modelo", "modelo");
		$edit->modelo->size=30;
		$edit->modelo->maxlength=50;
		//$edit->modelo->rule="required";

		$edit->anio=new dateonlyField("A&ntilde;o", "anio","Y");
		$edit->anio->size=10;
		$edit->anio->maxlength=10;
		//$edit->anio->rule="required";

		$edit->color=new inputField("Color", "color");
		$edit->color->size=20;
		$edit->color->maxlength=20;
		//$edit->color->rule="required";

		$edit->placa=new inputField("Placa", "placa");
		$edit->placa->size=20;
		$edit->placa->maxlength=20;
		//$edit->placa->rule="required";

		$edit->tipo=new inputField("Tipo", "tipo");
		$edit->tipo->size=30;
		$edit->tipo->maxlength=30;
		//$edit->tipo->rule="required";

		$edit->serial_car=new inputField("Serial De Carroceria", "serial_car");
		$edit->serial_car->size=50;
		$edit->serial_car->maxlength=50;
		//$edit->serial_car->rule="required";

		$edit->serial_motor=new inputField("Serial De Motor", "serial_motor");
		$edit->serial_motor->size=50;
		$edit->serial_motor->maxlength=50;
		//$edit->serial_motor->rule="required";

		$edit->ubica=new inputField("Ubicaci&oacute;n", "ubica");
		$edit->ubica->size=50;
		$edit->ubica->maxlength=100;
		//$edit->ubica->rule="required";

		$edit->depende=new inputField("Dependencia", "depende");
		$edit->depende->size=50;
		$edit->depende->maxlength=100;
		//$edit->depende->rule="required";

		$edit->fecha=new dateonlyField("Fecha", "fecha","d-m-Y");
		$edit->fecha->size=15;
		$edit->fecha->maxlength=15;
		//$edit->fecha->rule="required";

		$opt="Bueno / Regular / Malo / Faltante / NO POSEE";
		$edit->l_mecanica = new freeField("MECANICA","l_mecanica",$opt);

		$nombre=array("sistema_e"=>"Sistema De Encendido",
						"bobina"=>"Bobina",
						"bujias"=>"Bujias",
						"carburador"=>"Carburador",
						"filtro_aire"=>"Filtro De Aire",
						"filtro_gaso"=>"Filtro De Gasolina",
						"motor"=>"Motor",
						"regulador"=>"Regulador De Corriente",
						"frenos_d"=>"Frenos Delanteros",
						"frenos_t"=>"Frenos Traseros",
						"embrague"=>"Embrague",
						"pulmon"=>"Pulmon",
						"bateria"=>"Bateria",
						"cambios"=>"Cambios",
						"memoria"=>"Cdi Memoria",
						"gua_c"=>"Guaya De Clouch",
						"gua_f"=>"Guaya De Freno",
						"pedal"=>"Pedal De Encendido",
						"bomba"=>"Bomba De Aceite",
						"sistema_r"=>"Sistema De Rodamiento",
						"cadena"=>"Cadena",
						"tacometro"=>"Tacometros");
		foreach($nombre as $nom=>$val){
			$edit->$nom=new dropdownField("$val", "$nom");
			$edit->$nom->option("B","Bueno");
			$edit->$nom->option("R","Regular");
			$edit->$nom->option("M","Malo");
			$edit->$nom->option("F","Faltante");
			$edit->$nom->option("N","No Posee");
			$edit->$nom->style='width:100px';
			//$edit->$nom->rule="required";
		}

		$edit->l_cauchos = new freeField("CAUCHOS","l_cauchos",$opt);

		$nombre=array("Delantero"=>"delantero",
						"Trasero"=>"trasero",
						"Rin Delantero"=> "rin_d",
						"Rin Trasero"=> "rin_t");

		foreach($nombre as $val=>$nom){
			$edit->$nom=new dropdownField("$val", "$nom");
			$edit->$nom->option("B","Bueno");
			$edit->$nom->option("R","Regular");
			$edit->$nom->option("M","Malo");
			$edit->$nom->option("F","Faltante");
			$edit->$nom->option("N","No Posee");
			$edit->$nom->style='width:100px';
			//$edit->$nom->rule="required";
		}

		$edit->l_acceso = new freeField("ACCESORIOS","l_acceso",$opt);

		$nombre=array("Pito"=>"pito","Sirena"=>"sirena",
						"Reloj De Tablero"=> "reloj_t","Casco"=> "casco",
						"Alarma"=>"alarma");
			
		foreach($nombre as $val=>$nom){
			$edit->$nom=new dropdownField("$val", "$nom");
			$edit->$nom->option("B","Bueno");
			$edit->$nom->option("R","Regular");
			$edit->$nom->option("M","Malo");
			$edit->$nom->option("F","Faltante");
			$edit->$nom->option("N","No Posee");
			$edit->$nom->style='width:100px';
			//$edit->$nom->rule="required";
		}

		$edit->l_latoneria = new freeField("LATONERIA","l_latoneria",$opt);

		$nombre=array("Levas"=> "levas",
					"Base De Levas"=> "bases_l",
					"Protectores De Levas"=> "protec_l",
					"Tapa De Aceite"=> "tapa_acei",
					"Tubo De Escape"=> "tubo_esc",
					"Tapas Laterales"=> "tapas_lat",
					"Latoneria General"=> "latoneria",
					"Guarda Barro Delantero"=> "guarda_d",
					"Guarda Barro Trasero"=> "guarda_t",
					"Tanque De Gasolina"=> "tanque",
					"Tapa Del Tanque De Gasolina"=> "tapa_tan",
					"Bastones"=> "bastones",
					"Pintura General"=> "pintura");

		foreach($nombre as $val=>$nom){
			$edit->$nom=new dropdownField("$val", "$nom");
			$edit->$nom->option("B","Bueno");
			$edit->$nom->option("R","Regular");
			$edit->$nom->option("M","Malo");
			$edit->$nom->option("F","Faltante");
			$edit->$nom->option("N","No Posee");
			$edit->$nom->style='width:100px';
			//$edit->$nom->rule="required";
		}

		$edit->l_micas = new freeField("MICAS","l_micas",$opt);

		$nombre=array("Cruce Delantero Derecho"=> "cruce_dd",
					"Cruce Delantero Izquierdo"=> "cruce_di",
					"Cruce Trasero Derecho"=> "cruce_td",
					"Cruce Trasero Izquierdo"=> "cruce_ti",
					"Silvin"=>"silvin",
					"STOP Trasero"=>"stop_t");

		foreach($nombre as $val=>$nom){
			$edit->$nom=new dropdownField("$val", "$nom");
			$edit->$nom->option("B","Bueno");
			$edit->$nom->option("R","Regular");
			$edit->$nom->option("M","Malo");
			$edit->$nom->option("F","Faltante");
			$edit->$nom->option("N","No Posee");
			$edit->$nom->style='width:100px';
			//$edit->$nom->rule="required";
		}

		$edit->l_tapice = new freeField("TAPICERIA","l_tapice",$opt);

		$nombre=array("Cojin"=> "cojin");

		foreach($nombre as $val=>$nom){
			$edit->$nom=new dropdownField("$val", "$nom");
			$edit->$nom->option("B","Bueno");
			$edit->$nom->option("R","Regular");
			$edit->$nom->option("M","Malo");
			$edit->$nom->option("F","Faltante");
			$edit->$nom->option("N","No Posee");
			$edit->$nom->style='width:100px';
			//$edit->$nom->rule="required";
		}

		$edit->l_vidrio = new freeField("VIDRIOS","l_vidrio",$opt);

		$nombre=array("Retrovisor Derecho"=> "retrovisor_d",
					"Retrovisor Izquierdo"=> "retrovisor_i");

		foreach($nombre as $val=>$nom){
			$edit->$nom=new dropdownField("$val", "$nom");
			$edit->$nom->option("B","Bueno");
			$edit->$nom->option("R","Regular");
			$edit->$nom->option("M","Malo");
			$edit->$nom->option("F","Faltante");
			$edit->$nom->option("N","No Posee");
			$edit->$nom->style='width:100px';
			//$edit->$nom->rule="required";
		}

		$edit->l_luces = new freeField("LUCES","l_luces",$opt);

		$nombre=array("Delantera Alta"=> "luces_da",
					"Delantera Baja"=> "luces_db",
					"Stop Trasero"=> "luces_stop",
					"Cruce Delantero Derecho"=> "luces_cruce_dd",
					"Cruce Delantero Izquierdo"=> "luces_cruce_di",
					"Cruce Trasero Derecho"=> "luces_cruce_td",
					"Cruce Trasero Izquierdo"=> "luces_cruce_ti",
					"Estrobert"=> "estrobert",
					"Freno"=> "luces_freno");

		foreach($nombre as $val=>$nom){
			$edit->$nom=new dropdownField("$val", "$nom");
			$edit->$nom->option("B","Bueno");
			$edit->$nom->option("R","Regular");
			$edit->$nom->option("M","Malo");
			$edit->$nom->option("F","Faltante");
			$edit->$nom->option("N","No Posee");
			$edit->$nom->style='width:100px';
			//$edit->$nom->rule="required";
		}

		$edit->kilo = new inputField("Kilometraje", "kilo");
		$edit->kilo->size =20;
		$edit->kilo->maxlength=20;
		//$edit->kilo->rule="required";

		$edit->l_bateria = new freeField("BATERIA","l_bateria",'');

		$edit->bat_marca = new inputField("Marca", "bat_marca");
		$edit->bat_marca->size =50;
		$edit->bat_marca->maxlength=50;
		//$edit->bat_marca->rule="required";

		$edit->bat_serial = new inputField("Serial", "bat_serial");
		$edit->bat_serial->size =50;
		$edit->bat_serial->maxlength=50;
		//$edit->bat_serial->rule="required";

		$edit->estado_vehi=new dropdownField("Estado De La Unidad Motorizada", "estado_moto");
		$edit->estado_vehi->option("OP","Operativo");
		$edit->estado_vehi->option("IN","Inoperativo");
		$edit->estado_vehi->style='width:150px';
		//$edit->estado_vehi->rule="required";

		$edit->observa =new textareaField("Observaci&oacute;n", "observa");
		$edit->observa->rows= 4;
		$edit->observa->cols=50;

		$edit->inspector = new inputField("Inspector De La Unidad Vehicular", "inspector");
		$edit->inspector->size =50;
		$edit->inspector->maxlength=50;
		//$edit->inspector->rule="required";

		$edit->conductor = new inputField("Conductor De La Dependencia", "conductor");
		$edit->conductor->size =50;
		$edit->conductor->maxlength=50;
		//$edit->conductor->rule="required";

		$edit->jefe_uv = new inputField("Jefe De La Unidad Vehicular", "jefe_uv");
		$edit->jefe_uv->size =50;
		$edit->jefe_uv->maxlength=50;
		//$edit->jefe_uv->rule="required";

		$edit->jefe_depen = new inputField("Director o Jefe De La Dependencia", "jefe_depen");
		$edit->jefe_depen->size =50;
		$edit->jefe_depen->maxlength=50;
		//$edit->jefe_depen->rule="required";

		$edit->buttons("modify", "save", "undo", "delete", "back");
		$edit->build();

		$smenu['link']   = barra_menu('115');
		$data['smenu']   = $this->load->view('view_sub_menu', $smenu,true);
		$data['content'] = $edit->output;
		$data['title']   = "Motos";
		$data["head"]    = script('jquery.js').script('jquery-ui.js').script('plugins/jquery.numeric.pack.js').script('plugins/jquery.floatnumber.js').script('plugins/jquery.meiomask.js').style('vino/jquery-ui.css').$this->rapyd->get_head();
		$this->load->view('view_ventanas', $data);
	}


	function _valida($do){
		$error = '';
		$numero = $do->get('id');

		if(empty($numero)){
			$ntransac = $this->datasis->fprox_id('bi_id','moto');
			$do->set('id','M'.$ntransac);
			$do->pk    =array('id'=>'M'.$ntransac);
		}
	}

	function instalar(){
		$mSQL="CREATE TABLE IF NOT EXISTS `bi_moto` (
			`id` CHAR(8) NOT NULL DEFAULT '' COLLATE 'utf8_general_ci',
			`expediente` CHAR(8) NOT NULL DEFAULT '' COLLATE 'utf8_general_ci',
			`marca` CHAR(30) NULL DEFAULT NULL COLLATE 'utf8_general_ci',
			`modelo` CHAR(50) NULL DEFAULT NULL COLLATE 'utf8_general_ci',
			`anio` CHAR(4) NULL DEFAULT NULL COLLATE 'utf8_general_ci',
			`color` CHAR(30) NULL DEFAULT NULL COLLATE 'utf8_general_ci',
			`placa` CHAR(12) NULL DEFAULT NULL COLLATE 'utf8_general_ci',
			`tipo` CHAR(12) NULL DEFAULT NULL COLLATE 'utf8_general_ci',
			`serial_car` CHAR(50) NULL DEFAULT NULL COLLATE 'utf8_general_ci',
			`serial_motor` CHAR(50) NULL DEFAULT NULL COLLATE 'utf8_general_ci',
			`ubica` CHAR(100) NULL DEFAULT NULL COLLATE 'utf8_general_ci',
			`depende` CHAR(100) NULL DEFAULT NULL COLLATE 'utf8_general_ci',
			`fecha` DATE NULL DEFAULT NULL,
			`sistema_e` CHAR(2) NULL DEFAULT NULL COLLATE 'utf8_general_ci',
			`bobina` CHAR(2) NULL DEFAULT NULL COLLATE 'utf8_general_ci',
			`bujias` CHAR(2) NULL DEFAULT NULL COLLATE 'utf8_general_ci',
			`carburador` CHAR(2) NULL DEFAULT NULL COLLATE 'utf8_general_ci',
			`filtro_aire` CHAR(2) NULL DEFAULT NULL COLLATE 'utf8_general_ci',
			`filtro_gaso` CHAR(2) NULL DEFAULT NULL COLLATE 'utf8_general_ci',
			`motor` CHAR(2) NULL DEFAULT NULL COLLATE 'utf8_general_ci',
			`regulador` CHAR(2) NULL DEFAULT NULL COLLATE 'utf8_general_ci',
			`frenos_d` CHAR(2) NULL DEFAULT NULL COLLATE 'utf8_general_ci',
			`frenos_t` CHAR(2) NULL DEFAULT NULL COLLATE 'utf8_general_ci',
			`embrague` CHAR(2) NULL DEFAULT NULL COLLATE 'utf8_general_ci',
			`pulmon` CHAR(2) NULL DEFAULT NULL COLLATE 'utf8_general_ci',
			`bateria` CHAR(2) NULL DEFAULT NULL COLLATE 'utf8_general_ci',
			`cambios` CHAR(2) NULL DEFAULT NULL COLLATE 'utf8_general_ci',
			`memoria` CHAR(2) NULL DEFAULT NULL COLLATE 'utf8_general_ci',
			`gua_c` CHAR(2) NULL DEFAULT NULL COLLATE 'utf8_general_ci',
			`gua_f` CHAR(2) NULL DEFAULT NULL COLLATE 'utf8_general_ci',
			`pedal` CHAR(2) NULL DEFAULT NULL COLLATE 'utf8_general_ci',
			`bomba` CHAR(2) NULL DEFAULT NULL COLLATE 'utf8_general_ci',
			`sistema_r` CHAR(2) NULL DEFAULT NULL COLLATE 'utf8_general_ci',
			`cadena` CHAR(2) NULL DEFAULT NULL COLLATE 'utf8_general_ci',
			`tacometro` CHAR(2) NULL DEFAULT NULL COLLATE 'utf8_general_ci',
			`delantero` CHAR(2) NULL DEFAULT NULL COLLATE 'utf8_general_ci',
			`trasero` CHAR(2) NULL DEFAULT NULL COLLATE 'utf8_general_ci',
			`rin_d` CHAR(2) NULL DEFAULT NULL COLLATE 'utf8_general_ci',
			`rin_t` CHAR(2) NULL DEFAULT NULL COLLATE 'utf8_general_ci',
			`pito` CHAR(2) NULL DEFAULT NULL COLLATE 'utf8_general_ci',
			`sirena` CHAR(2) NULL DEFAULT NULL COLLATE 'utf8_general_ci',
			`reloj_t` CHAR(2) NULL DEFAULT NULL COLLATE 'utf8_general_ci',
			`casco` CHAR(2) NULL DEFAULT NULL COLLATE 'utf8_general_ci',
			`alarma` CHAR(2) NULL DEFAULT NULL COLLATE 'utf8_general_ci',
			`levas` CHAR(2) NULL DEFAULT NULL COLLATE 'utf8_general_ci',
			`bases_l` CHAR(2) NULL DEFAULT NULL COLLATE 'utf8_general_ci',
			`protec_l` CHAR(2) NULL DEFAULT NULL COLLATE 'utf8_general_ci',
			`tapa_acei` CHAR(2) NULL DEFAULT NULL COLLATE 'utf8_general_ci',
			`tubo_esc` CHAR(2) NULL DEFAULT NULL COLLATE 'utf8_general_ci',
			`tapas_lat` CHAR(2) NULL DEFAULT NULL COLLATE 'utf8_general_ci',
			`latoneria` CHAR(2) NULL DEFAULT NULL COLLATE 'utf8_general_ci',
			`guarda_d` CHAR(2) NULL DEFAULT NULL COLLATE 'utf8_general_ci',
			`guarda_t` CHAR(2) NULL DEFAULT NULL COLLATE 'utf8_general_ci',
			`tanque` CHAR(2) NULL DEFAULT NULL COLLATE 'utf8_general_ci',
			`tapa_tan` CHAR(2) NULL DEFAULT NULL COLLATE 'utf8_general_ci',
			`bastones` CHAR(2) NULL DEFAULT NULL COLLATE 'utf8_general_ci',
			`pintura` CHAR(2) NULL DEFAULT NULL COLLATE 'utf8_general_ci',
			`cruce_dd` CHAR(2) NULL DEFAULT NULL COLLATE 'utf8_general_ci',
			`cruce_di` CHAR(2) NULL DEFAULT NULL COLLATE 'utf8_general_ci',
			`cruce_td` CHAR(2) NULL DEFAULT NULL COLLATE 'utf8_general_ci',
			`cruce_ti` CHAR(2) NULL DEFAULT NULL COLLATE 'utf8_general_ci',
			`silvin` CHAR(2) NULL DEFAULT NULL COLLATE 'utf8_general_ci',
			`stop_t` CHAR(2) NULL DEFAULT NULL COLLATE 'utf8_general_ci',
			`cojin` CHAR(2) NULL DEFAULT NULL COLLATE 'utf8_general_ci',
			`retrovisor_d` CHAR(2) NULL DEFAULT NULL COLLATE 'utf8_general_ci',
			`retrovisor_i` CHAR(2) NULL DEFAULT NULL COLLATE 'utf8_general_ci',
			`luces_da` CHAR(2) NULL DEFAULT NULL COLLATE 'utf8_general_ci',
			`luces_db` CHAR(2) NULL DEFAULT NULL COLLATE 'utf8_general_ci',
			`luces_stop` CHAR(2) NULL DEFAULT NULL COLLATE 'utf8_general_ci',
			`luces_cruce_dd` CHAR(2) NULL DEFAULT NULL COLLATE 'utf8_general_ci',
			`luces_cruce_di` CHAR(2) NULL DEFAULT NULL COLLATE 'utf8_general_ci',
			`luces_cruce_td` CHAR(2) NULL DEFAULT NULL COLLATE 'utf8_general_ci',
			`luces_cruce_ti` CHAR(2) NULL DEFAULT NULL COLLATE 'utf8_general_ci',
			`estrobert` CHAR(2) NULL DEFAULT NULL COLLATE 'utf8_general_ci',
			`luces_freno` CHAR(2) NULL DEFAULT NULL COLLATE 'utf8_general_ci',
			`kilo` CHAR(20) NULL DEFAULT NULL COLLATE 'utf8_general_ci',
			`bat_marca` CHAR(50) NULL DEFAULT NULL COLLATE 'utf8_general_ci',
			`bat_serial` CHAR(50) NULL DEFAULT NULL COLLATE 'utf8_general_ci',
			`estado_moto` CHAR(2) NULL DEFAULT NULL COLLATE 'utf8_general_ci',
			`observa` TEXT NULL COLLATE 'utf8_general_ci',
			`inspector` CHAR(50) NULL DEFAULT NULL COLLATE 'utf8_general_ci',
			`conductor` CHAR(50) NULL DEFAULT NULL COLLATE 'utf8_general_ci',
			`jefe_uv` CHAR(50) NULL DEFAULT NULL COLLATE 'utf8_general_ci',
			`jefe_depen` CHAR(50) NULL DEFAULT NULL COLLATE 'utf8_general_ci',
			PRIMARY KEY (`id`),
			INDEX `expediente` (`expediente`)
		)
		COLLATE='utf8_general_ci'
		ENGINE=MyISAM
		ROW_FORMAT=DEFAULT";
		$this->db->simple_query($mSQL);
	}

}
?>
