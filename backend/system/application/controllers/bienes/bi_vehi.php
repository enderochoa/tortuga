<?php
require_once(BASEPATH.'application/controllers/validaciones.php');
//almacenes
class bi_vehi extends Controller {


	function bi_vehi(){
		parent::Controller();
		$this->load->library("rapyd");
		$this->datasis->modulo_id(264,1);
	}

	function index(){
		redirect("bienes/bi_vehi/filteredgrid");
	}

	function filteredgrid(){
		$this->rapyd->load("datafilter","datagrid");
		$this->rapyd->uri->keep_persistence();

		$filter = new DataFilter("Filtro por Expediente","bi_vehi");

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

		$uri = anchor('bienes/bi_vehi/dataedit/show/<#id#>','<#id#>');

		$grid = new DataGrid("Lista de edificios");
		$grid->order_by("expediente","asc");
		$grid->per_page = 20;

		$grid->column("Id"                  ,$uri                    ,"align='center'");
		$grid->column("Expediente"          ,"expediente"            ,"align='center'");
		$grid->column_orderby("Marca"       ,"marca"      ,"marca"   ,"align='left'"  );
		$grid->column_orderby("Modelo"      ,"modelo"     ,"modelo"  ,"align='center'");
		$grid->column_orderby("A&ntilde;o"  ,"anio"       ,"anio"    ,"align='center'");
		$grid->column_orderby("Color"       ,"color"      ,"color"   ,"align='center'");
		$grid->column_orderby("Placa"       , "placa"     , "placa"  ,"align='center'");
		$grid->column_orderby("Tipo"        ,"tipo"       ,"tipo"    ,"align='left'"  );
		//		$grid->column_orderby("Duplicar"             ,$uri_2                        ,"align='center'");

		$grid->add("bienes/bi_vehi/dataedit/create");
		$grid->build();

		$data['content'] = $filter->output.$grid->output;
		$data['title']   = "Vehiculos";
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

		$edit = new DataEdit2("Vehiculos", "bi_vehi");
		$edit->back_url = site_url("bienes/bi_vehi/filteredgrid");
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
		//		$edit->expediente->mode="autohide";
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
		$edit->placa->rule="required";

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

		$nombre=array("arranque"=>"Arranque",
						"alternador"=>"Alternador",
						"bobina"=>"Bobina",
						"inyectores"=>"Inyectores",
						"cable_distri"=>"Cable De Distribuci&oacute;n",
						"distri"=>"Distribuci&oacute;n",
						"bujias"=>"Bujias",
						"carburador"=>"Carburador",
						"filtro_aire"=>"Filtro De Aire",
						"filtro_gaso"=>"Filtro De Gasolina",
						"motor"=>"Motor",
						"diferencial"=>"Diferencial",
						"caja_veloci"=>"Caja De Velocidades",
						"bomba_frenos"=>"Bomba De Frenos",
						"bomba_direc"=>"Bomba De Direcci&oacute;n",
						"bomba_agua"=>"Bomba De Aguas",
						"bomba_gaso"=>"Bomba De Gasolina",
						"frenos_d"=>"Frenos Delanteros",
						"frenos_t"=>"Frenos Traseros",
						"embrague"=>"Embrague",
						"v_aceite_m"=>"Varilla De Aceite De Motor",
						"v_aceite_c"=>"Varilla De Aceite De Caja",
						"radiador"=>"Radiador",
						"tapas_radia"=>"Tapas De Radiador",
						"compresor"=>"Compresor de A/C",
						"bateria"=>"Bateria",
						"correas"=>"Correas",
						"carter"=>"Carter",
						"tren_d"=>"Tren Delantero");
		foreach($nombre as $nom=>$val){
			$edit->$nom=new dropdownField("$val", "$nom");
			$edit->$nom->option("B","Bueno");
			$edit->$nom->option("R","Regular");
			$edit->$nom->option("M","Malo");
			$edit->$nom->option("F","Faltante");
			$edit->$nom->option("N","No Posee");
			$edit->$nom->style='width:100px';
			$edit->$nom->rule="required";
		}

		$edit->l_cauchos = new freeField("CAUCHOS","l_cauchos",$opt);

		$nombre=array("Delantero Derecho"=>"delantero_d",
						"Delantero Izquierdo"=> "delantero_i",
						"Trasero Derecho"=>"trasero_d",
						"Trasero Izquierdo"=>"trasero_i",
						"Rin Delantero Derecho"=> "rin_dd",
						"Rin Delantero Izquierdo"=> "rin_di",
						"Rin Trasero Derecho"=> "rin_td",
						"Rin Trasero Izquierdo"=> "rin_ti",
						"Repuesto"=> "repuesto",
						"Rin De Repuesto"=> "rin_repu",
						"Triangulo De Seguridad"=> "trian",
						"Gato"=>"gato",
						"Llave De Cruz"=>"llave");

		foreach($nombre as $val=>$nom){
			$edit->$nom=new dropdownField("$val", "$nom");
			$edit->$nom->option("B","Bueno");
			$edit->$nom->option("R","Regular");
			$edit->$nom->option("M","Malo");
			$edit->$nom->option("F","Faltante");
			$edit->$nom->option("N","No Posee");
			$edit->$nom->style='width:100px';
			$edit->$nom->rule="required";
		}

		$edit->l_acceso = new freeField("ACCESORIOS","l_acceso",$opt);

		$nombre=array("Radio"=> "radio",
					"Reproductor"=> "repro",
					"Cornetas"=> "corneta",
					"Pito"=>"pito",
					"Sirena"=>"sirena",
					"Antena"=> "antena",
					"Alfombras"=> "alfombra",
					"Cables Auxiliares"=> "cables_aux",
					"Reloj De Tablero"=> "reloj_t",
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

		$nombre=array("Techo"=> "techo",
					"Capo"=> "capo",
					"Maleta"=> "maleta",
					"Pisos"=> "pisos",
					"Parrilla"=> "parrilla",
					"Platinas"=> "platinas",
					"Puerta Delantera Derecha"=> "puerta_dd",
					"Puerta Delantera Izquierda"=> "puerta_di",
					"Puerta Trasera Derecha"=> "puerta_td",
					"Puerta Trasera Izquierda"=> "puerta_ti",
					"Puerta Posterior"=> "puerta_pos",
					"GuardaF. Del. Derecho"=> "guarda_dd",
					"GuardaF. Del. Izquierdo"=> "guarda_di",
					"GuardaF. Tras. Derecho"=> "guarda_td",
					"GuardaF. Tras. Izquierdo"=> "guarda_ti",
					"Parachoque Delantero"=> "para_del",
					"Parachoque Trasero"=> "para_tra",
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
					"Tercer STOP"=>"ter_stop");

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

		$nombre=array("Asiento Delantero Derecho"=> "asiento_dd",
					"Asiento Delantero Izquierdo"=> "asiento_di",
					"Asiento trasero"=> "asiento_tra",
					"Techo"=> "t_techo",
					"Puerta Delantera Derecha"=> "t_puerta_dd",
					"Puerta Delantera Izquierda"=> "t_puerta_di",
					"Puerta Trasera Derecha"=> "t_puerta_td",
					"Puerta Trasera Izquierda"=> "t_puerta_ti",
					"Puerta Posterior"=> "t_puerta_pos");

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

		$nombre=array("Parabrisa Delantero"=> "parabrisa",
					"Vidrio Trasero"=> "v_trasero",
					"Puerta Delantera Derecha"=> "v_puerta_dd",
					"Puerta Delantera Izquierda"=> "v_puerta_di",
					"Puerta Trasera Derecha"=> "v_puerta_td",
					"Puerta Trasera Izquierda"=> "v_puerta_ti",
					"Puerta Posterior"=> "v_puerta_pos",
					"Lateral Delantero Derecho"=> "lateral_dd",
					"Lateral Delantero Izquierdo"=> "lateral_di",
					"Lateral Trasero Derecho"=> "lateral_td",
					"Lateral Trasero Izquierdo"=> "lateral_ti",
					"Retrovisor Derecho"=> "retrovisor_d",
					"Retrovisor Izquierdo"=> "retrovisor_i",
					"Retrovisor Central"=> "retrovisor_c",
					"Limpia Parabrisa Del. Derec."=> "l_parabrisa_d",
					"Limpia Parabrisa Del. Izqui."=> "l_parabrisa_i",
					"Limpia Parabrisa Trasero"=> "l_parabrisa_t");

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

		$nombre=array("Delantera Derecha Alta"=> "luces_dda",
					"Delantera Derecha Baja"=> "luces_ddb",
					"Delantera Izquierda Alta"=> "luces_dia",
					"Delantera Izquierda Baja"=> "luces_dib",
					"Stop Trasero Derecho"=> "luces_stop_td",
					"Stop Trasero Izquierdo"=> "luces_stop_ti",
					"Cruce Delantero Derecho"=> "luces_cruce_dd",
					"Cruce Delantero Izquierdo"=> "luces_cruce_di",
					"Cruce Trasero Derecho"=> "luces_cruce_td",
					"Cruce Trasero Izquierdo"=> "luces_cruce_ti",
					"Retroceso"=> "luces_retro",
					"Coctelera"=> "luces_coc",
					"Interna"=> "interna",
					"Emergencia"=> "emergencia",
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


		$edit->l_bateria = new freeField("BATERIA","l_bateria",'');

		$edit->bat_marca = new inputField("Marca", "bat_marca");
		$edit->bat_marca->size =50;
		$edit->bat_marca->maxlength=50;
		//$edit->bat_marca->rule="required";

		$edit->bat_serial = new inputField("Serial", "bat_serial");
		$edit->bat_serial->size =50;
		$edit->bat_serial->maxlength=50;
		//$edit->bat_serial->rule="required";

		$edit->estado_vehi=new dropdownField("Estado Del Vehiculo", "estado_vehi");
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

		$smenu['link']   = barra_menu('114');
		$data['smenu']   = $this->load->view('view_sub_menu', $smenu,true);
		$data['content'] = $edit->output;
		$data['title']   = "Vehiculos";
		$data["head"]    = script('jquery.js').script('jquery-ui.js').script('plugins/jquery.numeric.pack.js').script('plugins/jquery.floatnumber.js').script('plugins/jquery.meiomask.js').style('vino/jquery-ui.css').$this->rapyd->get_head();
		$this->load->view('view_ventanas', $data);
	}

	function _valida($do){
		$error = '';
		$numero = $do->get('id');

		if(empty($numero)){
			$ntransac = $this->datasis->fprox_id('bi_id','vehi');
			$do->set('id','V'.$ntransac);
			$do->pk    =array('id'=>'V'.$ntransac);
		}
	}


	function instalar(){
		$mSQL="CREATE TABLE IF NOT EXISTS `bi_vehi` (
				`id` CHAR(8) NOT NULL DEFAULT '',
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
				`arranque` CHAR(2) NULL DEFAULT NULL COLLATE 'utf8_general_ci',
				`alternador` CHAR(2) NULL DEFAULT NULL COLLATE 'utf8_general_ci',
				`bobina` CHAR(2) NULL DEFAULT NULL COLLATE 'utf8_general_ci',
				`inyectores` CHAR(2) NULL DEFAULT NULL COLLATE 'utf8_general_ci',
				`cable_distri` CHAR(2) NULL DEFAULT NULL COLLATE 'utf8_general_ci',
				`distri` CHAR(2) NULL DEFAULT NULL COLLATE 'utf8_general_ci',
				`bujias` CHAR(2) NULL DEFAULT NULL COLLATE 'utf8_general_ci',
				`carburador` CHAR(2) NULL DEFAULT NULL COLLATE 'utf8_general_ci',
				`filtro_aire` CHAR(2) NULL DEFAULT NULL COLLATE 'utf8_general_ci',
				`filtro_gaso` CHAR(2) NULL DEFAULT NULL COLLATE 'utf8_general_ci',
				`motor` CHAR(2) NULL DEFAULT NULL COLLATE 'utf8_general_ci',
				`diferencial` CHAR(2) NULL DEFAULT NULL COLLATE 'utf8_general_ci',
				`caja_veloci` CHAR(2) NULL DEFAULT NULL COLLATE 'utf8_general_ci',
				`bomba_frenos` CHAR(2) NULL DEFAULT NULL COLLATE 'utf8_general_ci',
				`bomba_direc` CHAR(2) NULL DEFAULT NULL COLLATE 'utf8_general_ci',
				`bomba_agua` CHAR(2) NULL DEFAULT NULL COLLATE 'utf8_general_ci',
				`bomba_gaso` CHAR(2) NULL DEFAULT NULL COLLATE 'utf8_general_ci',
				`frenos_d` CHAR(2) NULL DEFAULT NULL COLLATE 'utf8_general_ci',
				`frenos_t` CHAR(2) NULL DEFAULT NULL COLLATE 'utf8_general_ci',
				`embrague` CHAR(2) NULL DEFAULT NULL COLLATE 'utf8_general_ci',
				`v_aceite_m` CHAR(2) NULL DEFAULT NULL COLLATE 'utf8_general_ci',
				`v_aceite_c` CHAR(2) NULL DEFAULT NULL COLLATE 'utf8_general_ci',
				`radiador` CHAR(2) NULL DEFAULT NULL COLLATE 'utf8_general_ci',
				`tapas_radia` CHAR(2) NULL DEFAULT NULL COLLATE 'utf8_general_ci',
				`compresor` CHAR(2) NULL DEFAULT NULL COLLATE 'utf8_general_ci',
				`bateria` CHAR(2) NULL DEFAULT NULL COLLATE 'utf8_general_ci',
				`correas` CHAR(2) NULL DEFAULT NULL COLLATE 'utf8_general_ci',
				`carter` CHAR(2) NULL DEFAULT NULL COLLATE 'utf8_general_ci',
				`tren_d` CHAR(2) NULL DEFAULT NULL COLLATE 'utf8_general_ci',
				`delantero_d` CHAR(2) NULL DEFAULT NULL COLLATE 'utf8_general_ci',
				`delantero_i` CHAR(2) NULL DEFAULT NULL COLLATE 'utf8_general_ci',
				`trasero_d` CHAR(2) NULL DEFAULT NULL COLLATE 'utf8_general_ci',
				`trasero_i` CHAR(2) NULL DEFAULT NULL COLLATE 'utf8_general_ci',
				`rin_dd` CHAR(2) NULL DEFAULT NULL COLLATE 'utf8_general_ci',
				`rin_di` CHAR(2) NULL DEFAULT NULL COLLATE 'utf8_general_ci',
				`rin_td` CHAR(2) NULL DEFAULT NULL COLLATE 'utf8_general_ci',
				`rin_ti` CHAR(2) NULL DEFAULT NULL COLLATE 'utf8_general_ci',
				`repuesto` CHAR(2) NULL DEFAULT NULL COLLATE 'utf8_general_ci',
				`rin_repu` CHAR(2) NULL DEFAULT NULL COLLATE 'utf8_general_ci',
				`trian` CHAR(2) NULL DEFAULT NULL COLLATE 'utf8_general_ci',
				`gato` CHAR(2) NULL DEFAULT NULL COLLATE 'utf8_general_ci',
				`llave` CHAR(2) NULL DEFAULT NULL COLLATE 'utf8_general_ci',
				`radio` CHAR(2) NULL DEFAULT NULL COLLATE 'utf8_general_ci',
				`repro` CHAR(2) NULL DEFAULT NULL COLLATE 'utf8_general_ci',
				`corneta` CHAR(2) NULL DEFAULT NULL COLLATE 'utf8_general_ci',
				`pito` CHAR(2) NULL DEFAULT NULL COLLATE 'utf8_general_ci',
				`sirena` CHAR(2) NULL DEFAULT NULL COLLATE 'utf8_general_ci',
				`antena` CHAR(2) NULL DEFAULT NULL COLLATE 'utf8_general_ci',
				`alfombra` CHAR(2) NULL DEFAULT NULL COLLATE 'utf8_general_ci',
				`cables_aux` CHAR(2) NULL DEFAULT NULL COLLATE 'utf8_general_ci',
				`reloj_t` CHAR(2) NULL DEFAULT NULL COLLATE 'utf8_general_ci',
				`alarma` CHAR(2) NULL DEFAULT NULL COLLATE 'utf8_general_ci',
				`techo` CHAR(2) NULL DEFAULT NULL COLLATE 'utf8_general_ci',
				`capo` CHAR(2) NULL DEFAULT NULL COLLATE 'utf8_general_ci',
				`maleta` CHAR(2) NULL DEFAULT NULL COLLATE 'utf8_general_ci',
				`pisos` CHAR(2) NULL DEFAULT NULL COLLATE 'utf8_general_ci',
				`parrilla` CHAR(2) NULL DEFAULT NULL COLLATE 'utf8_general_ci',
				`platinas` CHAR(2) NULL DEFAULT NULL COLLATE 'utf8_general_ci',
				`puerta_dd` CHAR(2) NULL DEFAULT NULL COLLATE 'utf8_general_ci',
				`puerta_di` CHAR(2) NULL DEFAULT NULL COLLATE 'utf8_general_ci',
				`puerta_td` CHAR(2) NULL DEFAULT NULL COLLATE 'utf8_general_ci',
				`puerta_ti` CHAR(2) NULL DEFAULT NULL COLLATE 'utf8_general_ci',
				`puerta_pos` CHAR(2) NULL DEFAULT NULL COLLATE 'utf8_general_ci',
				`guarda_dd` CHAR(2) NULL DEFAULT NULL COLLATE 'utf8_general_ci',
				`guarda_di` CHAR(2) NULL DEFAULT NULL COLLATE 'utf8_general_ci',
				`guarda_td` CHAR(2) NULL DEFAULT NULL COLLATE 'utf8_general_ci',
				`guarda_ti` CHAR(2) NULL DEFAULT NULL COLLATE 'utf8_general_ci',
				`para_del` CHAR(2) NULL DEFAULT NULL COLLATE 'utf8_general_ci',
				`para_tra` CHAR(2) NULL DEFAULT NULL COLLATE 'utf8_general_ci',
				`pintura` CHAR(2) NULL DEFAULT NULL COLLATE 'utf8_general_ci',
				`cruce_dd` CHAR(2) NULL DEFAULT NULL COLLATE 'utf8_general_ci',
				`cruce_di` CHAR(2) NULL DEFAULT NULL COLLATE 'utf8_general_ci',
				`cruce_td` CHAR(2) NULL DEFAULT NULL COLLATE 'utf8_general_ci',
				`cruce_ti` CHAR(2) NULL DEFAULT NULL COLLATE 'utf8_general_ci',
				`ter_stop` CHAR(2) NULL DEFAULT NULL COLLATE 'utf8_general_ci',
				`asiento_dd` CHAR(2) NULL DEFAULT NULL COLLATE 'utf8_general_ci',
				`asiento_di` CHAR(2) NULL DEFAULT NULL COLLATE 'utf8_general_ci',
				`asiento_tra` CHAR(2) NULL DEFAULT NULL COLLATE 'utf8_general_ci',
				`t_techo` CHAR(2) NULL DEFAULT NULL COLLATE 'utf8_general_ci',
				`t_puerta_dd` CHAR(2) NULL DEFAULT NULL COLLATE 'utf8_general_ci',
				`t_puerta_di` CHAR(2) NULL DEFAULT NULL COLLATE 'utf8_general_ci',
				`t_puerta_td` CHAR(2) NULL DEFAULT NULL COLLATE 'utf8_general_ci',
				`t_puerta_ti` CHAR(2) NULL DEFAULT NULL COLLATE 'utf8_general_ci',
				`t_puerta_pos` CHAR(2) NULL DEFAULT NULL COLLATE 'utf8_general_ci',
				`parabrisa` CHAR(2) NULL DEFAULT NULL COLLATE 'utf8_general_ci',
				`v_trasero` CHAR(2) NULL DEFAULT NULL COLLATE 'utf8_general_ci',
				`v_puerta_dd` CHAR(2) NULL DEFAULT NULL COLLATE 'utf8_general_ci',
				`v_puerta_di` CHAR(2) NULL DEFAULT NULL COLLATE 'utf8_general_ci',
				`v_puerta_td` CHAR(2) NULL DEFAULT NULL COLLATE 'utf8_general_ci',
				`v_puerta_ti` CHAR(2) NULL DEFAULT NULL COLLATE 'utf8_general_ci',
				`v_puerta_pos` CHAR(2) NULL DEFAULT NULL COLLATE 'utf8_general_ci',
				`lateral_dd` CHAR(2) NULL DEFAULT NULL COLLATE 'utf8_general_ci',
				`lateral_di` CHAR(2) NULL DEFAULT NULL COLLATE 'utf8_general_ci',
				`lateral_td` CHAR(2) NULL DEFAULT NULL COLLATE 'utf8_general_ci',
				`lateral_ti` CHAR(2) NULL DEFAULT NULL COLLATE 'utf8_general_ci',
				`retrovisor_d` CHAR(2) NULL DEFAULT NULL COLLATE 'utf8_general_ci',
				`retrovisor_i` CHAR(2) NULL DEFAULT NULL COLLATE 'utf8_general_ci',
				`retrovisor_c` CHAR(2) NULL DEFAULT NULL COLLATE 'utf8_general_ci',
				`l_parabrisa_d` CHAR(2) NULL DEFAULT NULL COLLATE 'utf8_general_ci',
				`l_parabrisa_i` CHAR(2) NULL DEFAULT NULL COLLATE 'utf8_general_ci',
				`l_parabrisa_t` CHAR(2) NULL DEFAULT NULL COLLATE 'utf8_general_ci',
				`luces_dda` CHAR(2) NULL DEFAULT NULL COLLATE 'utf8_general_ci',
				`luces_ddb` CHAR(2) NULL DEFAULT NULL COLLATE 'utf8_general_ci',
				`luces_dia` CHAR(2) NULL DEFAULT NULL COLLATE 'utf8_general_ci',
				`luces_dib` CHAR(2) NULL DEFAULT NULL COLLATE 'utf8_general_ci',
				`luces_stop_td` CHAR(2) NULL DEFAULT NULL COLLATE 'utf8_general_ci',
				`luces_stop_ti` CHAR(2) NULL DEFAULT NULL COLLATE 'utf8_general_ci',
				`luces_cruce_dd` CHAR(2) NULL DEFAULT NULL COLLATE 'utf8_general_ci',
				`luces_cruce_di` CHAR(2) NULL DEFAULT NULL COLLATE 'utf8_general_ci',
				`luces_cruce_td` CHAR(2) NULL DEFAULT NULL COLLATE 'utf8_general_ci',
				`luces_cruce_ti` CHAR(2) NULL DEFAULT NULL COLLATE 'utf8_general_ci',
				`luces_retro` CHAR(2) NULL DEFAULT NULL COLLATE 'utf8_general_ci',
				`luces_coc` CHAR(2) NULL DEFAULT NULL COLLATE 'utf8_general_ci',
				`interna` CHAR(2) NULL DEFAULT NULL COLLATE 'utf8_general_ci',
				`emergencia` CHAR(2) NULL DEFAULT NULL COLLATE 'utf8_general_ci',
				`luces_freno` CHAR(2) NULL DEFAULT NULL COLLATE 'utf8_general_ci',
				`bat_marca` CHAR(50) NULL DEFAULT NULL COLLATE 'utf8_general_ci',
				`bat_serial` CHAR(50) NULL DEFAULT NULL COLLATE 'utf8_general_ci',
				`estado_vehi` CHAR(2) NULL DEFAULT NULL COLLATE 'utf8_general_ci',
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
