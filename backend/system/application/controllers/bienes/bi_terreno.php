<?php
require_once(BASEPATH.'application/controllers/validaciones.php');
//almacenes
class bi_terreno extends Controller {
	function bi_terreno(){
		parent::Controller();
		$this->load->library("rapyd");
	}

	function index(){
		redirect("bienes/bi_terreno/filteredgrid");
	}

	function filteredgrid(){
		$this->rapyd->load("datafilter","datagrid");
//		$this->rapyd->uri->keep_persistence();

		$filter = new DataFilter("Filtro por Expediente","bi_terreno");

		$filter->expediente = new inputField("Numero expediente", "expediente");
		$filter->expediente->size=20;
		
		$filter->denominacion = new inputField("Denominaci&oacute;n", "denominacion");
		$filter->denominacion->size=20;

		$filter->buttons("reset","search");
		$filter->build();

		$uri = anchor('bienes/bi_terreno/dataedit/show/<#id#>','<#id#>');

		$grid = new DataGrid("Lista de terrenos");
		$grid->order_by("expediente","asc");
		$grid->per_page = 20;

		$grid->column("Id"                          ,$uri                ,"align='center'"                      );
		$grid->column("Expediente"                  ,"expediente"        ,"align='center'"                      );
		$grid->column_orderby("Est.Propietario"     ,"est_propietario"   ,"est_propietario" ,"align='left'"     );
		$grid->column_orderby("Denominaci&oacute;n" ,"denominacion"      ,"denominacion"                        );
		//		$grid->column_orderby("Uso","uso" ,"uso"                    ,"align='center'");
		//		$grid->column_orderby("Estado"          ,"estado","estado"                     ,"align='center'");
		$grid->column_orderby("Municipio"         , "municipio"        , "municipio"      ,"align='center'"   );
		$grid->column_orderby("Direccion"         ,"direccion"         ,"direccion"       ,"align='left'"     );


		$grid->add("bienes/bi_terreno/dataedit/create");
		$grid->build();

		$data['content'] = $filter->output.$grid->output;
		$data['title']   = "Terrenos";
		$data["head"]    = $this->rapyd->get_head();
		$this->load->view('view_ventanas', $data);
	}

	function dataedit(){
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

		$this->rapyd->load("dataobject","dataedit");

		$edit = new DataEdit2("Terrenos", "bi_terreno");
		$edit->back_url = site_url("bienes/bi_terreno/filteredgrid");
		$edit->script($script, "create");
		$edit->script($script, "modify");

		$edit->pre_process('insert'  ,'_valida');
		$edit->pre_process('update'  ,'_valida');

		$edit->id= new inputField("Id", "id");
		$edit->id->mode="autohide";
		$edit->id->when=array('show');
		$edit->id->group="IDENTIFICACION";
		
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
		$edit->numero->group="IDENTIFICACION";
		
		$edit->alma = new dropdownField("Almacen", "alma");
		$edit->alma->options("SELECT codigo,CONCAT_WS(' ',codigo,descrip) valor FROM alma WHERE codigo='0000' ");
		$edit->alma->mode = "autohide";
		
		$edit->monto = new inputField("Monto", "monto");
		$edit->monto->size     =15;
		$edit->monto->maxlength=15;
		$edit->monto->css_class ='inputnum';
		$edit->monto->rule      ='numeric';

		$edit->expediente = new inputField("N&uacute;mero de expediente", "expediente");
		$edit->expediente->size = 6;
		$edit->expediente->maxlength=4;
		//$edit->expediente->rule="required";

		$edit->est_propietario = new inputField("1) ESTADO (O MUICIPIO) PROPIETARIO", "est_propietario");
		$edit->est_propietario->size =30;
		$edit->est_propietario->maxlength=30;
		//$edit->est_propietario->rule="required";

		$edit->denominacion = new inputField("2) DENOMINACION DEL INMUEBLE", "denominacion");
		$edit->denominacion->size=50;
		$edit->denominacion->maxlength=100;
		$edit->denominacion->rule="required";

		$edit->l_clase_in = new freeField("3. CLSIFICACION FUNCIONAL DEL INMUEBLE :","l_lsr_in","Seleccione uso predominantes");

		$edit->u_agri=new dropdownField("Agricultura", "u_agri");
		$edit->u_agri->option("","No");
		$edit->u_agri->option("X","Si");

		$edit->u_gana=new dropdownField("Ganaderia", "u_gana");
		$edit->u_gana->option("","No");
		$edit->u_gana->option("X","Si");

		$edit->u_misto=new dropdownField("Mixto Agropecuario", "u_misto");
		$edit->u_misto->option("","No");
		$edit->u_misto->option("X","Si");

		$edit->otro_uso=new textareaField("Otros usos", "otro_uso");
		$edit->otro_uso->rows=2;
		$edit->otro_uso->cols=50;

		$edit->l_ubica = new freeField("4.  UB ICACION GEOGRAFICA :","l_ubica"," ESTADOS O TERRITORIOS");

		$edit->muncipio=new inputField("Municipio", "municipio");
		$edit->muncipio->size=20;
		$edit->muncipio->maxlength=30;
		$edit->municipio->rule="required";

		$edit->direc=new textareaField("Lugar y Direcci&oacute;n ", "direccion");
		$edit->direc->rows=2;
		$edit->direc->cols=50;
		//$edit->direc->rule="required";

		$edit->l_area = new freeField("5. AREA TOTAL DEL TERRENO","l_area"," ");

		$edit->area_terre=new inputField("Area total del terreno(mt2)", "metros");
		$edit->area_terre->size=10;
		$edit->area_terre->maxlength=10;
		$edit->area_terre->css_class ='inputnum';

		$edit->hectarea=new inputField("Hectareas", "hectarea");
		$edit->hectarea->size=10;
		$edit->hectarea->maxlength=10;
		$edit->hectarea->css_class ='inputnum';

		$edit->area_cons=new inputField("6.AREA DE LAS CONSTRUCCIONES:  m2 ", "area_const");
		$edit->area_cons->size=10;
		$edit->area_cons->maxlength=10;
		$edit->area_cons->css_class ='inputnum';

		$edit->l_des = new freeField("7. DESCRIPCION DEL TERRENO","l_de","Tama&ntilde;o    &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;        Porcentaje hectareas(%)");

		$edit->t_plana=new inputField("Plana", "t_plana");
		$edit->t_plana->size=10;
		$edit->t_plana->maxlength=10;
		$edit->t_plana->css_class ='inputnum';

		$edit->p_plana=new inputField("Porcentaje", "p_plana");
		$edit->p_plana->in="t_plana";
		$edit->p_plana->size=10;
		$edit->p_plana->maxlength=4;
		$edit->p_plana->css_class ='inputnum';

		$edit->t_splana=new inputField("Semi - plana", "t_splana");
		$edit->t_splana->size=10;
		$edit->t_splana->maxlength=10;
		$edit->t_splana->css_class ='inputnum';

		$edit->p_splana=new inputField("Porcentaje", "p_splana");
		$edit->p_splana->in="t_splana";
		$edit->p_splana->size=10;
		$edit->p_splana->maxlength=4;
		$edit->p_splana->css_class ='inputnum';

		$edit->t_pendi=new inputField("Pendiente", "t_pendi");
		$edit->t_pendi->size=10;
		$edit->t_pendi->maxlength=10;
		$edit->t_pendi->css_class ='inputnum';

		$edit->p_pendi=new inputField("Porcentaje", "p_pendi");
		$edit->p_pendi->in="t_pendi";
		$edit->p_pendi->size=10;
		$edit->p_pendi->maxlength=4;
		$edit->p_pendi->css_class ='inputnum';

		$edit->t_mpendi=new inputField("Muy pendiente ", "t_mpendi");
		$edit->t_mpendi->size=10;
		$edit->t_mpendi->maxlength=10;
		$edit->t_mpendi->css_class ='inputnum';

		$edit->p_mpendi=new inputField("Porcentaje", "p_mpendi");
		$edit->p_mpendi->in="t_mpendi";
		$edit->p_mpendi->size=10;
		$edit->p_mpendi->maxlength=4;
		$edit->p_mpendi->css_class ='inputnum';

		$edit->topo_total=new inputField("Total de topografia ", "topo_total");
		$edit->topo_total->size=10;
		$edit->topo_total->maxlength=10;
		$edit->topo_total->css_class ='inputnum';

		$edit->topo_ptotal=new inputField("Porcentaje", "topo_ptotal");
		$edit->topo_ptotal->in="topo_total";
		$edit->topo_ptotal->size=10;
		$edit->topo_ptotal->maxlength=4;
		$edit->topo_ptotal->css_class ='inputnum';

		$edit->permanencia=new inputField("Permanencia : Frutales y Maderables  ", "permanencia");
		$edit->permanencia->size=10;
		$edit->permanencia->maxlength=10;
		$edit->permanencia->css_class ='inputnum';

		$edit->p_permanencia=new inputField("Porcentaje", "p_permanencia");
		$edit->p_permanencia->in="permanencia";
		$edit->p_permanencia->size=10;
		$edit->p_permanencia->maxlength=4;
		$edit->p_permanencia->css_class ='inputnum';

		$edit->a_defores=new inputField("Area Deforestada  ", "a_defores");
		$edit->a_defores->size=10;
		$edit->a_defores->maxlength=10;
		$edit->a_defores->css_class ='inputnum';

		$edit->p_defores=new inputField("Porcentaje", "p_defores");
		$edit->p_defores->in="a_defores";
		$edit->p_defores->size=10;
		$edit->p_defores->maxlength=4;
		$edit->p_defores->css_class ='inputnum';

		$edit->bosques=new inputField("Bosques   ", "bosques");
		$edit->bosques->size=10;
		$edit->bosques->maxlength=10;
		$edit->bosques->css_class ='inputnum';

		$edit->p_bosques=new inputField("Porcentaje", "p_bosques");
		$edit->p_bosques->in="bosques";
		$edit->p_bosques->size=10;
		$edit->p_bosques->maxlength=4;
		$edit->p_bosques->css_class ='inputnum';

		$edit->incultas=new inputField("Tierras Incultas  ", "incultas");
		$edit->incultas->size=10;
		$edit->incultas->maxlength=10;
		$edit->incultas->css_class ='inputnum';

		$edit->p_incultas=new inputField("Porcentaje", "p_incultas");
		$edit->p_incultas->in="incultas";
		$edit->p_incultas->size=10;
		$edit->p_incultas->maxlength=4;
		$edit->p_incultas->css_class ='inputnum';

		$edit->no_aprove=new inputField("No Aprovechadas  ", "no_aprove");
		$edit->no_aprove->size=10;
		$edit->no_aprove->maxlength=10;
		$edit->no_aprove->css_class ='inputnum';

		$edit->pno_aprove=new inputField("Porcentaje", "pno_aprove");
		$edit->pno_aprove->in="no_aprove";
		$edit->pno_aprove->size=10;
		$edit->pno_aprove->maxlength=4;
		$edit->pno_aprove->css_class ='inputnum';

		$edit->naturales=new inputField("Naturales  ", "naturales");
		$edit->naturales->size=10;
		$edit->naturales->maxlength=10;
		$edit->naturales->css_class ='inputnum';

		$edit->p_naturales=new inputField("Porcentaje", "p_naturales");
		$edit->p_naturales->in="naturales";
		$edit->p_naturales->size=10;
		$edit->p_naturales->maxlength=4;
		$edit->p_naturales->css_class ='inputnum';

		$edit->cultivos=new inputField("Cultivados  ", "cultivos");
		$edit->cultivos->size=10;
		$edit->cultivos->maxlength=10;
		$edit->cultivos->css_class ='inputnum';

		$edit->p_cultivos=new inputField("Porcentaje", "p_cultivos");
		$edit->p_cultivos->in="cultivos";
		$edit->p_cultivos->size=10;
		$edit->p_cultivos->maxlength=4;
		$edit->p_cultivos->css_class ='inputnum';

		$edit->pot_total=new inputField("Total potreros  ", "pot_total");
		$edit->pot_total->size=10;
		$edit->pot_total->maxlength=10;
		$edit->pot_total->css_class ='inputnum';

		$edit->pot_ptotal=new inputField("Porcentaje", "pot_ptotal");
		$edit->pot_ptotal->in="pot_total";
		$edit->pot_ptotal->size=10;
		$edit->pot_ptotal->maxlength=4;
		$edit->pot_ptotal->css_class ='inputnum';

		$edit->rios=new inputField("Cursos de agua ( rios y quebradas )   ", "rios");
		$edit->rios->size=50;
		$edit->rios->maxlength=50;

		$edit->manantial=new inputField("Manantiales ", "manantial");
		$edit->manantial->size=50;
		$edit->manantial->maxlength=50;

		$edit->canales=new inputField("Canales y acequias  ", "canales");
		$edit->canales->size=50;
		$edit->canales->maxlength=50;

		$edit->embalse=new inputField("Embalses y lagunas", "embalse");
		$edit->embalse->size=50;
		$edit->embalse->maxlength=50;

		$edit->pozo=new inputField("Pozos y aljibes ", "pozo");
		$edit->pozo->size=50;
		$edit->pozo->maxlength=50;

		$edit->acued=new inputField("Acueductos ", "acued");
		$edit->acued->size=50;
		$edit->acued->maxlength=50;

		$edit->otro_agua=new textareaField("Otros recursos de agua", "otro_agua");
		$edit->otro_agua->rows=2;
		$edit->otro_agua->cols=50;
			
		$edit->l_cerca = new freeField("Cercas","l_cerca","");

		$edit->c_long=new inputField("Longitud", "c_long");
		$edit->c_long->size=20;
		$edit->c_long->maxlength=50;
		$edit->c_long->css_class ='inputnum';

		$edit->c_estan=new inputField(" Estantes de ", "c_estan");
		$edit->c_estan->size=50;
		$edit->c_estan->maxlength=50;

		$edit->c_material=new inputField(" Material", "c_material");
		$edit->c_material->size=50;
		$edit->c_material->maxlength=50;

		$edit->l_vias = new freeField("Vias Interiores","l_vias","");

		$edit->v_interiores=new textareaField("Longitud y Especificaciones", "v_interiores");
		$edit->v_interiores->rows=3;
		$edit->v_interiores->cols=50;

		$edit->l_bien = new freeField("Otras","l_bien","en resumen. El detalle de los edificios se anotara en la HOJA DE TRABAJO Nº 1  y el de las instalaciones fijas en la HOJA DE TRABAJO Nº 3");

		$edit->otra_bien=new textareaField("Bienhechurias", "otra_bien");
		$edit->otra_bien->rows=5;
		$edit->otra_bien->cols=50;

		$edit->l_linderos = new freeField(" ","l_linderos","Linderos");

		$edit->linderos=new textareaField("8. LINDEROS", "linderos");
		$edit->linderos->rows=2;
		$edit->linderos->cols=50;

		$edit->estudio_legal=new textareaField("9.ESTUDIO LEGAL DE LA PROPIEDAD: (OBTENER DEL PROCURADOR DEL ESTADO O DEL SINDICO PROCU", "estudio_legal");
		$edit->estudio_legal->rows=4;
		$edit->estudio_legal->cols=50;

		$edit->l_valor = new freeField("Valor ","l_valor","con que figura la contabilidad");

		$edit->fecha_adq=new dateonlyField("Fecha Adquisici&oacute;n   ", "fecha_adq","d-m-Y");
		$edit->fecha_adq->size=10;
		$edit->fecha_adq->maxlength=10;

		$edit->valor_adq=new inputField("Valor de adquisici&oacute;n   ", "valor_adq");
		$edit->valor_adq->size=10;
		$edit->valor_adq->maxlength=10;
		$edit->valor_adq->css_class ='inputnum';

		$edit->mejoras=new textareaField("Mas adicionales y mejoras  ", "mejoras");
		$edit->mejoras->rows=2;
		$edit->mejoras->cols=50;

		$edit->fecha_m=new dateonlyField("Fecha Mejoras   ", "fecha_m","d-m-Y");
		$edit->fecha_m->size=10;
		$edit->fecha_m->maxlength=10;

		$edit->valor_m=new inputField("Valor mejoras y adicionales   ", "valor_m");
		$edit->valor_m->size=10;
		$edit->valor_m->maxlength=10;
		$edit->valor_m->css_class ='inputnum';

		$edit->valor_conta=new inputField("Valor Total   ", "valor_conta");
		$edit->valor_conta->size=10;
		$edit->valor_conta->maxlength=10;
		$edit->valor_conta->css_class ='inputnum';

		$edit->avaluo =new textareaField("11.AVALUO DE LA COMISION ( PARA LOS TERRENOS SOLAMENTE )", "avaluo");
		$edit->avaluo->rows=4;
		$edit->avaluo->cols=50;

		$edit->planos =new textareaField("12. PLANOS, ESQUEMAS Y FOTOGRAFIAS: ( LOS QUE SE ACOMPAÑEN, CON MENSION DE LA OFICINA EN DONDE SE ENCUENTREN LOS RESTANTES)", "planos");
		$edit->planos->rows=2;
		$edit->planos->cols=50;

		$edit->realizado=new inputField("Preparado por", "preparado");
		$edit->realizado->size=10;
		$edit->realizado->maxlength=10;

		$edit->buttons("modify", "save", "undo", "delete", "back");
		$edit->build();

		$data['content'] = $edit->output;
		$smenu['link']   = barra_menu('112');
		$data['smenu']   = $this->load->view('view_sub_menu', $smenu,true);
		$data['title']   = "Terreno";
		$data["head"]    = script('jquery.js').script('jquery-ui.js').script('plugins/jquery.numeric.pack.js').script('plugins/jquery.floatnumber.js').script('plugins/jquery.meiomask.js').style('vino/jquery-ui.css').$this->rapyd->get_head();
		$this->load->view('view_ventanas', $data);
	}

	function _valida($do){
		$error = '';
		$numero = $do->get('id');

		if(empty($numero)){
			$ntransac = $this->datasis->fprox_id('bi_id','bien');
			$do->set('id','T'.$ntransac);
			$do->pk    =array('id'=>'T'.$ntransac);
		}
	}

		
	function instalar(){
		$mSQL="CREATE TABLE IF NOT EXISTS `bi_terreno` (
				`id` CHAR(10) NOT NULL DEFAULT '' COLLATE 'utf8_general_ci',
				`expediente` CHAR(10) NOT NULL DEFAULT '' COLLATE 'utf8_general_ci',
				`est_propietario` CHAR(50) NULL DEFAULT NULL COLLATE 'utf8_general_ci',
				`denominacion` CHAR(100) NULL DEFAULT NULL COLLATE 'utf8_general_ci',
				`u_agri` CHAR(5) NULL DEFAULT NULL COLLATE 'utf8_general_ci',
				`u_gana` CHAR(5) NULL DEFAULT NULL COLLATE 'utf8_general_ci',
				`u_misto` CHAR(5) NULL DEFAULT NULL COLLATE 'utf8_general_ci',
				`otro_uso` TEXT NULL COLLATE 'utf8_general_ci',
				`municipio` CHAR(50) NULL DEFAULT NULL COLLATE 'utf8_general_ci',
				`direccion` CHAR(200) NULL DEFAULT NULL COLLATE 'utf8_general_ci',
				`hectarea` CHAR(20) NULL DEFAULT NULL COLLATE 'utf8_general_ci',
				`metros` CHAR(20) NULL DEFAULT NULL COLLATE 'utf8_general_ci',
				`area_const` CHAR(20) NULL DEFAULT NULL COLLATE 'utf8_general_ci',
				`t_plana` CHAR(20) NULL DEFAULT NULL COLLATE 'utf8_general_ci',
				`p_plana` CHAR(4) NULL DEFAULT NULL COLLATE 'utf8_general_ci',
				`t_splana` CHAR(20) NULL DEFAULT NULL COLLATE 'utf8_general_ci',
				`p_splana` CHAR(4) NULL DEFAULT NULL COLLATE 'utf8_general_ci',
				`t_pendi` CHAR(20) NULL DEFAULT NULL COLLATE 'utf8_general_ci',
				`p_pendi` CHAR(4) NULL DEFAULT NULL COLLATE 'utf8_general_ci',
				`t_mpendi` CHAR(20) NULL DEFAULT NULL COLLATE 'utf8_general_ci',
				`p_mpendi` CHAR(4) NULL DEFAULT NULL COLLATE 'utf8_general_ci',
				`topo_total` CHAR(20) NULL DEFAULT NULL COLLATE 'utf8_general_ci',
				`topo_ptotal` CHAR(20) NULL DEFAULT NULL COLLATE 'utf8_general_ci',
				`permanencia` CHAR(20) NULL DEFAULT NULL COLLATE 'utf8_general_ci',
				`p_permanencia` CHAR(4) NULL DEFAULT NULL COLLATE 'utf8_general_ci',
				`a_defores` CHAR(20) NULL DEFAULT NULL COLLATE 'utf8_general_ci',
				`p_defores` CHAR(4) NULL DEFAULT NULL COLLATE 'utf8_general_ci',
				`bosques` CHAR(20) NULL DEFAULT NULL COLLATE 'utf8_general_ci',
				`p_bosques` CHAR(4) NULL DEFAULT NULL COLLATE 'utf8_general_ci',
				`incultas` CHAR(20) NULL DEFAULT NULL COLLATE 'utf8_general_ci',
				`p_incultas` CHAR(4) NULL DEFAULT NULL COLLATE 'utf8_general_ci',
				`no_aprove` CHAR(20) NULL DEFAULT NULL COLLATE 'utf8_general_ci',
				`pno_aprove` CHAR(4) NULL DEFAULT NULL COLLATE 'utf8_general_ci',
				`naturales` CHAR(20) NULL DEFAULT NULL COLLATE 'utf8_general_ci',
				`p_naturales` CHAR(4) NULL DEFAULT NULL COLLATE 'utf8_general_ci',
				`cultivos` CHAR(20) NULL DEFAULT NULL COLLATE 'utf8_general_ci',
				`p_cultivos` CHAR(4) NULL DEFAULT NULL COLLATE 'utf8_general_ci',
				`pot_total` CHAR(20) NULL DEFAULT NULL COLLATE 'utf8_general_ci',
				`pot_ptotal` CHAR(4) NULL DEFAULT NULL COLLATE 'utf8_general_ci',
				`rios` CHAR(50) NULL DEFAULT NULL COLLATE 'utf8_general_ci',
				`manantial` CHAR(50) NULL DEFAULT NULL COLLATE 'utf8_general_ci',
				`canales` CHAR(50) NULL DEFAULT NULL COLLATE 'utf8_general_ci',
				`embalse` CHAR(50) NULL DEFAULT NULL COLLATE 'utf8_general_ci',
				`pozo` CHAR(50) NULL DEFAULT NULL COLLATE 'utf8_general_ci',
				`acued` CHAR(50) NULL DEFAULT NULL COLLATE 'utf8_general_ci',
				`otro_agua` CHAR(50) NULL DEFAULT NULL COLLATE 'utf8_general_ci',
				`c_long` CHAR(20) NULL DEFAULT NULL COLLATE 'utf8_general_ci',
				`c_estan` CHAR(50) NULL DEFAULT NULL COLLATE 'utf8_general_ci',
				`c_material` CHAR(50) NULL DEFAULT NULL COLLATE 'utf8_general_ci',
				`v_interiores` CHAR(200) NULL DEFAULT NULL COLLATE 'utf8_general_ci',
				`otra_bien` TEXT NULL COLLATE 'utf8_general_ci',
				`linderos` TEXT NULL COLLATE 'utf8_general_ci',
				`estudio_legal` TEXT NULL COLLATE 'utf8_general_ci',
				`fecha_adq` DATE NULL DEFAULT NULL,
				`valor_adq` CHAR(20) NULL DEFAULT NULL COLLATE 'utf8_general_ci',
				`fecha_m` DATE NULL DEFAULT NULL,
				`valor_m` CHAR(20) NULL DEFAULT NULL COLLATE 'utf8_general_ci',
				`mejoras` TEXT NULL COLLATE 'utf8_general_ci',
				`valor_conta` CHAR(20) NULL DEFAULT NULL COLLATE 'utf8_general_ci',
				`avaluo` TEXT NULL COLLATE 'utf8_general_ci',
				`planos` TEXT NULL COLLATE 'utf8_general_ci',
				`preparado` CHAR(50) NULL DEFAULT NULL COLLATE 'utf8_general_ci',
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
