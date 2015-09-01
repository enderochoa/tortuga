<?php
require_once(BASEPATH.'application/controllers/validaciones.php');
//almacenes
class bi_edificio extends Controller {


	function bi_edificio(){
		parent::Controller();
		$this->load->library("rapyd");
		$this->datasis->modulo_id(262,1);
	}

	function index(){
		redirect("bienes/bi_edificio/filteredgrid");
	}

	function filteredgrid(){
		$this->rapyd->load("datafilter","datagrid");
		$this->rapyd->uri->keep_persistence();

		$filter = new DataFilter("Filtro por Expediente","bi_edificio");

		$filter->expediente = new inputField("Numero expediente", "expediente");
		$filter->expediente->size=20;
		
		$filter->denominacion = new inputField("Denominci&oacute;n", "denominacion");
		$filter->denominacion->size=20;

		$filter->buttons("reset","search");
		$filter->build();

		$uri = anchor('bienes/bi_edificio/dataedit/show/<#id#>','<#id#>');

		$grid = new DataGrid("Lista de edificios");
		$grid->order_by("expediente","asc");
		$grid->per_page = 20;

		$grid->column_orderby("Id"               ,$uri              ,"id"               ,"align='center'");
		$grid->column_orderby("Expediente"       ,"expediente"      ,'expediente'       ,"align='center'");
		$grid->column_orderby("Est.Propietario"  ,"est_propietario" ,"est_propietario"  ,"align='left'"  );
		$grid->column_orderby("Denominacion"     ,"denominacion"    ,"denominacion"     ,"align='left'"  );
		$grid->column_orderby("Uso","uso"        ,"uso"             ,"align='center'"                    );
		$grid->column_orderby("Estado"           ,"estado"          ,"estado"           ,"align='center'");
		$grid->column_orderby("Municipio"        , "municipio"      , "municipio"       ,"align='center'");
		$grid->column_orderby("Direccion"        ,"direccion"       ,"direccion"        ,"align='left'"  );

		$grid->add("bienes/bi_edificio/dataedit/create");
		$grid->build();

		$data['filtro']  = $filter->output;
		$data['content'] = $grid->output;
		$data['title']   = "Edificios";
		$data["head"]    = $this->rapyd->get_head();
		$this->load->view('view_ventanas', $data);
	}

	function dataedit()
	{
		$this->rapyd->load("dataobject","dataedit2");
		//$this->rapyd->uri->keep_persistence();
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
		
		$link=site_url('bienes/bi_edificio/ultimo');

		$edit = new DataEdit2("Edificio", "bi_edificio");
		$edit->back_url = site_url("bienes/bi_edificio/filteredgrid");
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
		$edit->expediente->maxlength=10;
		//$edit->expediente->rule="required";

		$edit->est_propietario = new inputField(" Estado (o municipio) Propietario", "est_propietario");
		$edit->est_propietario->cols =50;
		$edit->est_propietario->rows =50;
		//$edit->est_propietario->rule="required";

		$edit->denominacion = new inputField(" Denominaci&oacute;n del inmuebles", "denominacion");
		$edit->denominacion->rows     =70;
		$edit->denominacion->cols     =3;
		$edit->denominacion->rule="required";

		$edit->uso=new textareaField("Clasificaci&oacute;n funcional del inmuebles (Uso principal al que esta destinado", "uso");
		$edit->uso->rows=3;
		$edit->uso->cols=50;
		//$edit->uso->rule="required";

		$edit->estado=new inputField("Estado", "estado");
		$edit->estado->size=30;
		$edit->estado->maxlength=30;
		//$edit->estado->rule="required";

		$edit->municipio=new inputField("Municipio", "municipio");
		$edit->municipio->size=30;
		$edit->municipio->maxlength=30;
		//$edit->municipio->rule="required";

		$edit->direccion=new inputField("Direcci&oacute;n", "direccion");
		$edit->direccion->rows     =3;
		$edit->direccion->cols     =70;
		//$edit->direccion->rule="required";

		$edit->area_terre=new inputField("Area total del terreno(mt2)", "area_terre");
		$edit->area_terre->size=10;
		$edit->area_terre->maxlength=10;
		$edit->area_terre->css_class ='inputnum';

		$edit->area_ocup=new inputField("Area de Constucci&oacute;n(mt2)", "area_ocup");
		$edit->area_ocup->size=10;
		$edit->area_ocup->maxlength=10;
		$edit->area_ocup->css_class ='inputnum';

		$edit->num_pisos=new inputField("N&uacute;mero de pisos", "num_pisos");
		$edit->num_pisos->size=10;
		$edit->num_pisos->maxlength=10;
		$edit->num_pisos->css_class ='inputnum';

		$edit->area_tpisos=new inputField("Area total de la construcci&oacute;n( Total de los pisos )", "area_tpisos");
		$edit->area_tpisos->size=10;
		$edit->area_tpisos->maxlength=10;
		$edit->area_tpisos->css_class ='inputnum';

		$edit->area_anexa=new inputField("Area total de las anexidades  (jardines, patios, etc)", "area_anexa");
		$edit->area_anexa->size=10;
		$edit->area_anexa->maxlength=10;
		$edit->area_anexa->css_class ='inputnum';

		$edit->l_descrip_in = new freeField("Descripci&oacute;n del inmueble ","l_descrip_in","Seleccione las estucturas y materiales predominantes");

		$edit->l_estruc = new freeField("Tipos ","l_estruc","De Estructura");

		$edit->pared_carga=new dropdownField("Paredes de carga", "pared_carga");
		$edit->pared_carga->option("","No");
		$edit->pared_carga->option("X","Si");
		$edit->pared_carga->style='width:80px';

		$edit->madera=new dropdownField("Madera  ", "madera");
		$edit->madera->option("","No");
		$edit->madera->option("X","Si");
		$edit->madera->style='width:80px';

		$edit->metalica=new dropdownField("Metalica  ", "metalica");
		$edit->metalica->option("","No");
		$edit->metalica->option("X","Si");
		$edit->metalica->style='width:80px';

		$edit->concreto=new dropdownField("Concreto Armado   ", "concreto");
		$edit->concreto->option("","No");
		$edit->concreto->option("X","Si");
		$edit->concreto->style='width:80px';

		$edit->otro_estruc=new inputField("Otros(Estructura)   ", "otro_estruc");
		$edit->otro_estruc->size=20;
		$edit->otro_estruc->maxlength=20;

		$edit->l_pisos = new freeField("Tipos ","l_pisos","De Pisos");

		$edit->tierra=new dropdownField("Tierra   ", "tierra");
		$edit->tierra->option("","No");
		$edit->tierra->option("X","Si");
		$edit->tierra->style='width:80px';

		$edit->cemento=new dropdownField("Cemento   ", "cemento");
		$edit->cemento->option("","No");
		$edit->cemento->option("X","Si");
		$edit->cemento->style='width:80px';

		$edit->ladrillo=new dropdownField("Ladrillo   ", "ladrillo");
		$edit->ladrillo->option("","No");
		$edit->ladrillo->option("X","Si");
		$edit->ladrillo->style='width:80px';

		$edit->mosaico=new dropdownField("Mosaico   ", "mosaico");
		$edit->mosaico->option("","No");
		$edit->mosaico->option("X","Si");
		$edit->mosaico->style='width:80px';

		$edit->granito=new dropdownField("Granito  ", "granito");
		$edit->granito->option("","No");
		$edit->granito->option("X","Si");
		$edit->granito->style='width:80px';

		$edit->otro_pisos=new inputField("Otros(Pisos)   ", "otro_pisos");
		$edit->otro_pisos->size=20;
		$edit->otro_pisos->maxlength=20;

		$edit->l_pared = new freeField("Tipos ","l_pared","De Paredes");

		$edit->bloques_arci=new dropdownField("Bloque de arcilla   ", "bloques_arci");
		$edit->bloques_arci->option("","No");
		$edit->bloques_arci->option("X","Si");
		$edit->bloques_arci->style='width:80px';

		$edit->bloques_conc=new dropdownField("Bloque de concreto   ", "bloques_conc");
		$edit->bloques_conc->option("","No");
		$edit->bloques_conc->option("X","Si");
		$edit->bloques_conc->style='width:80px';

		$edit->ladrillo=new dropdownField("Ladrillo ", "ladrillos");
		$edit->ladrillo->option("","No");
		$edit->ladrillo->option("X","Si");
		$edit->ladrillo->style='width:80px';

		$edit->p_madera=new dropdownField("Madera   ", "p_madera");
		$edit->p_madera->option("","No");
		$edit->p_madera->option("X","Si");
		$edit->p_madera->style='width:80px';

		$edit->p_metalica=new dropdownField("Metalica   ", "p_metalica");
		$edit->p_metalica->option("","No");
		$edit->p_metalica->option("X","Si");
		$edit->p_metalica->style='width:80px';

		$edit->otro_pared=new inputField("Otros(Pared)   ", "otro_pared");
		$edit->otro_pared->size=20;
		$edit->otro_pared->maxlength=20;

		$edit->l_techo = new freeField("Tipos ","l_pared","De Techos");

		$edit->t_metalico=new dropdownField("Metalicos   ", "t_metalico");
		$edit->t_metalico->option("","No");
		$edit->t_metalico->option("X","Si");
		$edit->t_metalico->style='width:80px';

		$edit->asbesto=new dropdownField("Asbesto  ", "asbesto");
		$edit->asbesto->option("","No");
		$edit->asbesto->option("X","Si");
		$edit->asbesto->style='width:80px';

		$edit->teja_concreto=new dropdownField("Teja de arcilla sobre losa de concreto ", "teja_concreto");
		$edit->teja_concreto->option("","No");
		$edit->teja_concreto->option("X","Si");
		$edit->teja_concreto->style='width:80px';

		$edit->teja_cana_ar=new dropdownField("Teja de arcilla sobre ca&ntild;a amarga o similar   ", "teja_cana_ar");
		$edit->teja_cana_ar->option("","No");
		$edit->teja_cana_ar->option("X","Si");
		$edit->teja_cana_ar->style='width:80px';

		$edit->platabanda=new dropdownField("Platabanda  ", "platabanda");
		$edit->platabanda->option("","No");
		$edit->platabanda->option("X","Si");
		$edit->platabanda->style='width:80px';

		$edit->otro_techo=new inputField("Otros(techos)   ", "otro_techo");
		$edit->otro_techo->size=20;
		$edit->otro_techo->maxlength=20;

		$edit->l_puertas = new freeField("Tipos ","l_puertas","De Puertas y Ventanas");

		$edit->pu_madera=new dropdownField("Madera   ", "pu_madera");
		$edit->pu_madera->option("","No");
		$edit->pu_madera->option("X","Si");
		$edit->pu_madera->style='width:80px';

		$edit->pu_metalico=new dropdownField("Metalicas  ", "pu_metalico");
		$edit->pu_metalico->option("","No");
		$edit->pu_metalico->option("X","Si");
		$edit->pu_metalico->style='width:80px';

		$edit->l_servicios = new freeField("Tipos ","l_servicios","De Sevicios");

		$edit->sanitarios=new dropdownField("Sanitarios  ", "sanitarios");
		$edit->sanitarios->option("","No");
		$edit->sanitarios->option("X","Si");
		$edit->sanitarios->style='width:80px';

		$edit->cocinas=new dropdownField("Cocinas  ", "cocinas");
		$edit->cocinas->option("","No");
		$edit->cocinas->option("X","Si");
		$edit->cocinas->style='width:80px';

		$edit->agua=new dropdownField("Agua corriente  ", "agua");
		$edit->agua->option("","No");
		$edit->agua->option("X","Si");
		$edit->agua->style='width:80px';

		$edit->electri=new dropdownField("Electricidad  ", "electri");
		$edit->electri->option("","No");
		$edit->electri->option("X","Si");
		$edit->electri->style='width:80px';

		$edit->telefono=new dropdownField("Telefonos ", "telefono");
		$edit->telefono->option("","No");
		$edit->telefono->option("X","Si");
		$edit->telefono->style='width:80px';

		$edit->aire_acon=new dropdownField("Aire acondicionado   ", "aire_acon");
		$edit->aire_acon->option("","No");
		$edit->aire_acon->option("X","Si");
		$edit->aire_acon->style='width:80px';

		$edit->ascensores=new dropdownField("Ascensores  ", "ascensores");
		$edit->ascensores->option("","No");
		$edit->ascensores->option("X","Si");
		$edit->ascensores->style='width:80px';

		$edit->otro_servicios=new inputField("Otros(servicios)   ", "otro_servicios");
		$edit->otro_servicios->size=20;
		$edit->otro_servicios->maxlength=20;

		$edit->l_anexo = new freeField("Tipos ","l_anexo","De otras anexidades del edificio");

		$edit->patios=new dropdownField("Patios ", "patios");
		$edit->patios->option("","No");
		$edit->patios->option("X","Si");
		$edit->patios->style='width:80px';

		$edit->jardines=new dropdownField("Jardines   ", "jardines");
		$edit->jardines->option("","No");
		$edit->jardines->option("X","Si");
		$edit->jardines->style='width:80px';

		$edit->estaciona=new dropdownField("Estacionamiento  ", "estaciona");
		$edit->estaciona->option("","No");
		$edit->estaciona->option("X","Si");
		$edit->estaciona->style='width:80px';

		$edit->otro_anexo=new inputField("Otros(anexos)   ", "otro_anexo");
		$edit->otro_anexo->size=10;
		$edit->otro_anexo->maxlength=10;

		$edit->l_linderos = new freeField(" ","l_linderos","Linderos");

		$edit->linderos=new textareaField("Linderos", "linderos");
		$edit->linderos->rows=2;
		$edit->linderos->cols=50;

		$edit->estudio_legal=new textareaField("ESTUDIO LEGAL DE LA PROPIEDAD: (OBTENER DEL PROCURADOR DEL ESTADO O DEL SINDICO PROCU", "estudio_legal");
		$edit->estudio_legal->rows=2;
		$edit->estudio_legal->cols=50;

		$edit->l_valor = new freeField("Valor ","l_valor","con que figura la contabilidad");

		$edit->fecha_adqu=new dateonlyField("Fecha Adquisici&oacute;n   ", "fecha_adqu","d-m-Y");
		$edit->fecha_adqu->size=10;
		$edit->fecha_adqu->maxlength=10;

		$edit->valor_adqu=new inputField("Valor de adquisici&oacute;n   ", "valor_adqu");
		$edit->valor_adqu->size=10;
		$edit->valor_adqu->maxlength=10;
		$edit->valor_adqu->css_class ='inputnum';

		$edit->fecha_cont=new dateonlyField("Fecha Mejoras   ", "fecha_cont","d-m-Y");
		$edit->fecha_cont->size=10;
		$edit->fecha_cont->maxlength=10;

		$edit->valor_mejoras=new inputField("Mas mejoras y adicionales   ", "valor_mejoras");
		$edit->valor_mejoras->size=50;
		$edit->valor_mejoras->maxlength=50;
		$edit->valor_mejoras->css_class ='inputnum';

		$edit->valor_contable=new inputField("Total  ", "valor_contable");
		$edit->valor_contable->size=20;
		$edit->valor_contable->maxlength=20;
		$edit->valor_contable->css_class ='inputnum';

		$edit->avaluo_pro =new textareaField("Avaluo Provicional de la comisi&oacute;n:  (Para construcci&oacute;n y el area de terreno acupada por la misma", "avaluo_pro");
		$edit->avaluo_pro->rows=2;
		$edit->avaluo_pro->cols=50;

		$edit->planos =new textareaField("PLlanos esquemas y fotografias:( los que acompa&ntilde;en con menci&oacute de la oficina en donde se encuentren los restantes ", "planos");
		$edit->planos->rows=2;
		$edit->planos->cols=50;
	

		$edit->realizado=new inputField("Preparado por", "realizado");
		$edit->realizado->size=30;
		$edit->realizado->maxlength=30;

		$edit->fecha=new dateonlyField("Fecha  ", "fecha","d-m-Y");
		$edit->fecha->size=10;
		$edit->fecha->maxlength=10;

		$edit->buttons("modify", "save", "undo", "delete", "back");
		$edit->build();

		$data['content'] = $edit->output;
		$smenu['link']   = barra_menu('111');
		$data['smenu']   = $this->load->view('view_sub_menu', $smenu,true);
		$data['title']   = "Edificio";
		$data["head"]    = script('jquery.js').script('jquery-ui.js').script('plugins/jquery.numeric.pack.js').script('plugins/jquery.floatnumber.js').script('plugins/jquery.meiomask.js').style('vino/jquery-ui.css').$this->rapyd->get_head();
		$this->load->view('view_ventanas', $data);
	}

	function _valida($do){
		$error = '';
		$numero = $do->get('id');

		if(empty($numero)){
			$ntransac = $this->datasis->fprox_id('bi_id','edif');
			$do->set('id','E'.$ntransac);
			$do->pk    =array('id'=>'E'.$ntransac);
		}
	}

	function instalar(){
		$mSQL="CREATE TABLE IF NOT EXISTS `bi_edificio` (
				`id` CHAR(10) NOT NULL DEFAULT '' COLLATE 'utf8_general_ci',
				`expediente` CHAR(10) NOT NULL DEFAULT '' COLLATE 'utf8_general_ci',
				`est_propietario` CHAR(50) NULL DEFAULT NULL COLLATE 'utf8_general_ci',
				`denominacion` CHAR(50) NULL DEFAULT NULL COLLATE 'utf8_general_ci',
				`uso` VARCHAR(150) NULL DEFAULT NULL COLLATE 'utf8_general_ci',
				`estado` CHAR(50) NULL DEFAULT NULL COLLATE 'utf8_general_ci',
				`municipio` CHAR(50) NULL DEFAULT NULL COLLATE 'utf8_general_ci',
				`direccion` CHAR(50) NULL DEFAULT NULL COLLATE 'utf8_general_ci',
				`area_terre` CHAR(20) NULL DEFAULT NULL COLLATE 'utf8_general_ci',
				`area_ocup` CHAR(20) NULL DEFAULT NULL COLLATE 'utf8_general_ci',
				`num_pisos` CHAR(3) NULL DEFAULT NULL COLLATE 'utf8_general_ci',
				`area_tpisos` CHAR(20) NULL DEFAULT NULL COLLATE 'utf8_general_ci',
				`area_anexa` CHAR(20) NULL DEFAULT NULL COLLATE 'utf8_general_ci',
				`pared_carga` CHAR(3) NULL DEFAULT NULL COLLATE 'utf8_general_ci',
				`madera` CHAR(3) NULL DEFAULT NULL COLLATE 'utf8_general_ci',
				`metalica` CHAR(3) NULL DEFAULT NULL COLLATE 'utf8_general_ci',
				`concreto` CHAR(3) NULL DEFAULT NULL COLLATE 'utf8_general_ci',
				`otro_estruc` CHAR(50) NULL DEFAULT NULL COLLATE 'utf8_general_ci',
				`tierra` CHAR(3) NULL DEFAULT NULL COLLATE 'utf8_general_ci',
				`cemento` CHAR(3) NULL DEFAULT NULL COLLATE 'utf8_general_ci',
				`ladrillo` CHAR(3) NULL DEFAULT NULL COLLATE 'utf8_general_ci',
				`mosaico` CHAR(3) NULL DEFAULT NULL COLLATE 'utf8_general_ci',
				`granito` CHAR(3) NULL DEFAULT NULL COLLATE 'utf8_general_ci',
				`otro_pisos` CHAR(50) NULL DEFAULT NULL COLLATE 'utf8_general_ci',
				`bloques_arci` CHAR(3) NULL DEFAULT NULL COLLATE 'utf8_general_ci',
				`bloques_conc` CHAR(3) NULL DEFAULT NULL COLLATE 'utf8_general_ci',
				`ladrillos` CHAR(3) NULL DEFAULT NULL COLLATE 'utf8_general_ci',
				`p_madera` CHAR(3) NULL DEFAULT NULL COLLATE 'utf8_general_ci',
				`p_metalica` CHAR(3) NULL DEFAULT NULL COLLATE 'utf8_general_ci',
				`otro_pared` CHAR(50) NULL DEFAULT NULL COLLATE 'utf8_general_ci',
				`t_metalico` CHAR(3) NULL DEFAULT NULL COLLATE 'utf8_general_ci',
				`asbesto` CHAR(3) NULL DEFAULT NULL COLLATE 'utf8_general_ci',
				`teja_concreto` CHAR(3) NULL DEFAULT NULL COLLATE 'utf8_general_ci',
				`teja_cana_ar` CHAR(3) NULL DEFAULT NULL COLLATE 'utf8_general_ci',
				`platabanda` CHAR(3) NULL DEFAULT NULL COLLATE 'utf8_general_ci',
				`otro_techo` CHAR(50) NULL DEFAULT NULL COLLATE 'utf8_general_ci',
				`pu_madera` CHAR(3) NULL DEFAULT NULL COLLATE 'utf8_general_ci',
				`pu_metalico` CHAR(3) NULL DEFAULT NULL COLLATE 'utf8_general_ci',
				`sanitarios` CHAR(3) NULL DEFAULT NULL COLLATE 'utf8_general_ci',
				`cocinas` CHAR(3) NULL DEFAULT NULL COLLATE 'utf8_general_ci',
				`agua` CHAR(3) NULL DEFAULT NULL COLLATE 'utf8_general_ci',
				`electri` CHAR(3) NULL DEFAULT NULL COLLATE 'utf8_general_ci',
				`telefono` CHAR(3) NULL DEFAULT NULL COLLATE 'utf8_general_ci',
				`aire_acon` CHAR(3) NULL DEFAULT NULL COLLATE 'utf8_general_ci',
				`ascensores` CHAR(3) NULL DEFAULT NULL COLLATE 'utf8_general_ci',
				`otro_servicios` CHAR(50) NULL DEFAULT NULL COLLATE 'utf8_general_ci',
				`patios` CHAR(3) NULL DEFAULT NULL COLLATE 'utf8_general_ci',
				`jardines` CHAR(3) NULL DEFAULT NULL COLLATE 'utf8_general_ci',
				`estaciona` CHAR(3) NULL DEFAULT NULL COLLATE 'utf8_general_ci',
				`otro_anexo` CHAR(50) NULL DEFAULT NULL COLLATE 'utf8_general_ci',
				`linderos` VARCHAR(100) NULL DEFAULT NULL COLLATE 'utf8_general_ci',
				`estudio_legal` VARCHAR(200) NULL DEFAULT NULL COLLATE 'utf8_general_ci',
				`valor_contable` VARCHAR(100) NULL DEFAULT NULL COLLATE 'utf8_general_ci',
				`fecha_adqu` DATE NULL DEFAULT NULL,
				`fecha_cont` DATE NULL DEFAULT NULL,
				`valor_adqu` CHAR(20) NULL DEFAULT NULL COLLATE 'utf8_general_ci',
				`avaluo_pro` VARCHAR(100) NULL DEFAULT NULL COMMENT 'para construccion y area de terreno ocupada' COLLATE 'utf8_general_ci',
				`planos` VARCHAR(200) NULL DEFAULT NULL COMMENT 'esquemas y fotografias' COLLATE 'utf8_general_ci',
				`valor_mejoras` CHAR(20) NULL DEFAULT NULL COLLATE 'utf8_general_ci',
				`realizado` CHAR(20) NULL DEFAULT NULL COLLATE 'utf8_general_ci',
				`fecha` DATE NULL DEFAULT NULL,
				PRIMARY KEY (`id`),
				INDEX `expediente` (`expediente`)
			)
			COLLATE='utf8_general_ci'
			ENGINE=MyISAM
			ROW_FORMAT=DEFAULT";
		$this->db->simple_query($mSQL);
                     
		$this->db->simple_query("ALTER TABLE `bi_edificio` CHANGE COLUMN `est_propietario` `est_propietario` TINYTEXT CHARACTER SET 'utf8' COLLATE 'utf8_general_ci' NULL DEFAULT NULL  ;");
                     
		$this->db->simple_query("ALTER TABLE `bi_edificio` CHANGE COLUMN `denominacion` `denominacion` TINYTEXT CHARACTER SET 'utf8' COLLATE 'utf8_general_ci' NULL DEFAULT NULL  ");
		                 
		$this->db->simple_query("ALTER TABLE `tortuga`.`bi_edificio` CHANGE COLUMN `direccion` `direccion` TINYTEXT CHARACTER SET 'utf8' COLLATE 'utf8_general_ci' NULL DEFAULT NULL  ");
	}

}
?>
