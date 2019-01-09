<?php
include('common.php');
class sumi extends Controller {

	function sumi(){
		parent::Controller();
		$this->load->library("rapyd");
	}

	#### index #####
	function index(){
		$this->datasis->modulo_id(60,1);
		redirect("suministros/sumi/filteredgrid");
	}
	function filteredgrid(){
		$this->rapyd->load("datafilter2","datagrid");
		$mSPRV=array(
				'tabla'   =>'sprv',
				'columnas'=>array(
				'proveed' =>'C&oacute;odigo',
				'nombre'=>'Nombre',
				'contacto'=>'Contacto'),
				'filtro'  =>array('proveed'=>'C&oacute;digo','nombre'=>'Nombre'),
				'retornar'=>array('proveed'=>'proveed'),
				'titulo'  =>'Buscar Beneficiario');

		$bSPRV=$this->datasis->modbus($mSPRV);

		$link2=site_url('inventario/common/get_linea');
		$link3=site_url('inventario/common/get_grupo');

		$script='
		$(document).ready(function(){

			$("#depto").change(function(){
				depto();
				$.post("'.$link2.'",{ depto:$(this).val() },function(data){$("#linea").html(data);})
				$.post("'.$link3.'",{ linea:"" },function(data){$("#grupo").html(data);})
			});
			$("#linea").change(function(){
				linea();
				$.post("'.$link3.'",{ linea:$(this).val() },function(data){$("#grupo").html(data);})
			});

			$("#grupo").change(function(){
				grupo();
			});
			depto();
			linea();
			grupo();
		});

		function depto(){
			if($("#depto").val()!=""){
				$("#nom_depto").attr("disabled","disabled");
			}
			else{
				$("#nom_depto").attr("disabled","");
			}
		}

		function linea(){
			if($("#linea").val()!=""){
				$("#nom_linea").attr("disabled","disabled");
			}
			else{
				$("#nom_linea").attr("disabled","");
			}
		}

		function grupo(){
			if($("#grupo").val()!=""){
				$("#nom_grupo").attr("disabled","disabled");
			}
			else{
				$("#nom_grupo").attr("disabled","");
			}
		}
		';

		//filter
		$filter = new DataFilter2("");

		$filter->db->select("a.id,a.tipo AS tipo,codigo,a.descrip,b.nom_grup AS nom_grup,b.grupo AS grupoid,c.descrip AS nom_linea,c.linea AS linea,d.descrip AS nom_depto,d.depto AS depto");
		$filter->db->from("sumi AS a");
		$filter->db->join("grup AS b","a.grupo=b.grupo","LEFT");
		$filter->db->join("line AS c","b.linea=c.linea","LEFT");
		$filter->db->join("dpto AS d","c.depto=d.depto","LEFT");

		$filter->script($script);

		$filter->codigo = new inputField("C&oacute;digo", "codigo");
		$filter->codigo -> size=25;

		$filter->descrip = new inputField("Descripci&oacute;n", "descrip");
		$filter->descrip->db_name='a.descrip';
		$filter->descrip -> size=25;

		//$filter->tipo = new dropdownField("Tipo", "tipo");
		//$filter->tipo->db_name=("a.tipo");
		//$filter->tipo->option("","Todos");
		//$filter->tipo->option("Articulo","Art&iacute;culo");
		//$filter->tipo->option("Servicio","Servicio");
		//$filter->tipo->option("Descartar","Descartar");
		//$filter->tipo->option("Consumo","Consumo");
		//$filter->tipo->option("Fraccion","Fracci&oacute;n");
		//$filter->tipo ->style='width:220px;';
		//
		//$filter->clave = new inputField("Clave", "clave");
		//$filter->clave -> size=25;
		//
		//$filter->activo = new dropdownField("Activo", "activo");
		//$filter->activo->option("","");
		//$filter->activo->option("S","Si");
		//$filter->activo->option("N","No");
		//$filter->activo ->style='width:220px;';

		//$filter->proveed = new inputField("Beneficiario", "proveed");
		//$filter->proveed->append($bSPRV);
		//$filter->proveed->clause ="in";
		//$filter->proveed->db_name='( a.prov1, a.prov2, a.prov3 )';
		//$filter->proveed -> size=25;

		$filter->depto2 = new inputField("Departamento", "nom_depto");
		$filter->depto2->db_name="d.descrip";
		$filter->depto2 -> size=10;

		$filter->depto = new dropdownField("Departamento","depto");
		$filter->depto->db_name="d.depto";
		$filter->depto->option("","Seleccione un Departamento");
		$filter->depto->options("SELECT depto, descrip FROM dpto WHERE tipo='I' ORDER BY depto");
		$filter->depto->in="depto2";
		//
		$filter->linea = new inputField("Linea", "nom_linea");
		$filter->linea->db_name="c.descrip";
		$filter->linea -> size=10;

		$filter->linea2 = new dropdownField("L&iacute;nea","linea");
		$filter->linea2->db_name="c.linea";
		$filter->linea2->option("","Seleccione un Departamento primero");
		$filter->linea2->in="linea";
		$depto=$filter->getval('depto');
		if($depto!==FALSE){
			$filter->linea2->options("SELECT linea, descrip FROM line WHERE depto='$depto' ORDER BY descrip");
		}else{
			$filter->linea2->option("","Seleccione un Departamento primero");
		}

		$filter->grupo2 = new inputField("Grupo", "nom_grupo");
		$filter->grupo2->db_name="b.nom_grup";
		$filter->grupo2 -> size=10;

		$filter->grupo = new dropdownField("Grupo", "grupo");
		$filter->grupo->db_name="b.grupo";
		$filter->grupo->option("","");
		$filter->grupo->in="grupo2";
		$filter->grupo->options("SELECT grupo, nom_grup FROM grup  ORDER BY nom_grup");//WHERE linea='$linea'
		//$linea=$filter->getval('linea2');
		//if($linea!==FALSE){
		//	$filter->grupo->options("SELECT grupo, nom_grup FROM grup WHERE linea='$linea' ORDER BY nom_grup");
		//}else{
		//	$filter->grupo->option("","Seleccione un Departamento primero");
		//}

		//$filter->marca = new dropdownField("Marca", "marca");
		//$filter->marca->option("","");
		//$filter->marca->options("SELECT TRIM(marca) AS clave, TRIM(marca) AS valor FROM marc ORDER BY marca");
		//$filter->marca -> style='width:220px;';

		$filter->buttons("reset","search");
		$filter->build();

		$uri = "suministros/sumi/dataedit/show/<raencode><#codigo#></raencode>";

		$grid = new DataGrid("");
		$grid->order_by("codigo","asc");
		$grid->per_page = 20;
		$link=anchor('/suministros/sumi/dataedit/show/<#id#>','<#codigo#>');
		$uri_2 = anchor('suministros/sumi/dataedit/create/<raencode><#codigo#></raencode>','Duplicar');

		$grid->column_orderby("c&oacute;digo",$link      ,"codigo"                                              );
		$grid->column("Departamento","<#nom_depto#>"     ,'align=left'                                          );
		$grid->column("L&iacute;nea","<#nom_linea#>"     ,'align=left'                                          );
		$grid->column_orderby("Grupo"                    ,"<#nom_grup#>"     ,"nom_grup"   ,"align='left'NOWRAP");
		$grid->column_orderby("Descripci&oacute;n"       ,"descrip"          ,"descrip"    ,"align='left'NOWRAP");
		//$grid->column("Precio 1","<number_format><#precio1#>|2|,|.</number_format>",'align=right');
		//$grid->column("Precio 2","<number_format><#precio2#>|2|,|.</number_format>",'align=right');
		//$grid->column("Precio 3","<number_format><#precio3#>|2|,|.</number_format>",'align=right');
		//$grid->column("Precio 4","<number_format><#precio4#>|2|,|.</number_format>",'align=right');
		$grid->column("Duplicar",$uri_2     ,"align='center'");

		$grid->add("suministros/sumi/dataedit/create");
		$grid->build();

		//$data['content'] = $filter->output.$grid->output;
		$data['filtro']  = $filter->output;
		$data['content'] = $grid->output;
		$data['script'] = script("jquery.js")."\n";
		$data['title']   = "Maestro de Inventario";
		$data["head"]    = script("jquery.pack.js").script("plugins/jquery.numeric.pack.js").script("plugins/jquery.floatnumber.js").script("sinvmaes2.js").$this->rapyd->get_head();
		$this->load->view('view_ventanas', $data);
	}

	function dataedit($status='',$id='' ) {
		$this->rapyd->load('dataedit2','dataobject');

		$link  =site_url('suministros/common/add_marc');
		$link4 =site_url('suministros/common/get_marca');
		$link5 =site_url('suministros/common/add_unidad');
		$link6 =site_url('suministros/common/get_unidad');
		$link7 =site_url('suministros/sumi/ultimo');
		$link8 =site_url('suministros/sumi/sugerir');
		$link9 =site_url('suministros/common/add_depto');
		$link10=site_url('suministros/common/get_depto');
		$link11=site_url('suministros/common/add_linea');
		$link12=site_url('suministros/common/get_linea');
		$link13=site_url('suministros/common/add_grupo');
		$link14=site_url('suministros/common/get_grupo');

		$script='
		function dpto_change(){

			$.post("'.$link12.'",{ depto:$("#depto").val() },function(data){$("#linea").html(data);})
			$.post("'.$link14.'",{ linea:"" },function(data){$("#grupo").html(data);})
		}

		$(function(){
			$("#depto").change(function(){dpto_change(); });
			$("#linea").change(function(){ $.post("'.$link14.'",{ linea:$(this).val() },function(data){$("#grupo").html(data);}) });

			$("#tdecimal").change(function(){
				var clase;
				if($(this).attr("value")=="S") clase="inputnum"; else clase="inputonlynum";
				$("#exmin").unbind();$("#exmin").removeClass(); $("#exmin").addClass(clase);
				$("#exmax").unbind();$("#exmax").removeClass(); $("#exmax").addClass(clase);
				$("#exord").unbind();$("#exord").removeClass(); $("#exord").addClass(clase);
				$("#exdes").unbind();$("#exdes").removeClass(); $("#exdes").addClass(clase);



				$(".inputnum").numeric(".");
				$(".inputonlynum").numeric("0");
			});

			//requeridos(true);
		});

		function ultimo(){
			$.ajax({
				url: "'.$link7.'",
				success: function(msg){
				  alert( "El ultimo codigo ingresado fue: " + msg );
				}
			});
		}

		function sugerir(){
			$.ajax({
				url: "'.$link8.'",
				success: function(msg){
					if(msg){
						$("#codigo").val(msg);
					}
					else{
						alert("No es posible generar otra sugerencia. Coloque el c&oacute;digo manualmente");
					}
				}
			});
		}

		function add_marca(){
			marca=prompt("Introduza el nombre de la MARCA a agregar");
			if(marca==null){
			}else{
				$.ajax({
				 type: "POST",
				 processData:false,
					url: "'.$link.'",
					data: "valor="+marca,
					success: function(msg){
						if(msg=="s.i"){
							marca=marca.substr(0,30);
							$.post("'.$link4.'",{ x:"" },function(data){$("#marca").html(data);$("#marca").val(marca);})
						}
						else{
							alert("Disculpe. En este momento no se ha podido agregar la marca, por favor intente mas tarde");
						}
					}
				});
			}
		}

		function add_unidad(){
			unidad=prompt("Introduza el nombre de la UNIDAD a agregar");
			if(unidad==null){
			}else{
				$.ajax({
				 type: "POST",
				 processData:false,
					url: "'.$link5.'",
					data: "valor="+unidad,
					success: function(msg){
						if(msg=="s.i"){
							unidad=unidad.substr(0,8);
							$.post("'.$link6.'",{ x:"" },function(data){$("#unidad").html(data);$("#unidad").val(unidad);})
						}
						else{
							alert("Disculpe. En este momento no se ha podido agregar la unidad, por favor intente mas tarde");
						}
					}
				});
			}
		}

		function add_depto(){
			depto=prompt("Introduza el nombre del DEPARTAMENTO a agregar");
			if(depto==null){
			}else{
				$.ajax({
				 type: "POST",
				 processData:false,
					url: "'.$link9.'",
					data: "valor="+depto,
					success: function(msg){
						if(msg=="Y.a-Existe"){
							alert("Ya existe un Departamento con esa Descripcion");
						}
						else{
							if(msg=="N.o-SeAgrego"){
								alert("Disculpe. En este momento no se ha podido agregar el departamento, por favor intente mas tarde");
							}else{
								$.post("'.$link10.'",{ x:"" },function(data){$("#depto").html(data);$("#depto").val(msg);})
							}
						}
					}
				});
			}
		}

		function add_linea(){
			deptoval=$("#depto").val();
			if(deptoval==""){
				alert("Debe seleccionar un Departamento al cual agregar la linea");
			}else{
				linea=prompt("Introduza el nombre de la LINEA a agregar al DEPARTAMENTO seleccionado");
				if(linea==null){
				}else{
					$.ajax({
					 type: "POST",
					 processData:false,
						url: "'.$link11.'",
						data: "valor="+linea+"&&valor2="+deptoval,
						success: function(msg){
							if(msg=="Y.a-Existe"){
								alert("Ya existe una Linea con esa Descripcion");
							}
							else{
								if(msg=="N.o-SeAgrego"){
									alert("Disculpe. En este momento no se ha podido agregar la linea, por favor intente mas tarde");
								}else{
									$.post("'.$link12.'",{ depto:deptoval },function(data){$("#linea").html(data);$("#linea").val(msg);})
								}
							}
						}
					});
				}
			}
		}

		function add_gruposolo(){
			grupo=prompt("Introduza el nombre del GRUPO a agregar");
			if(grupo==null){
			}else{
				$.ajax({
				 type: "POST",
				 processData:false,
					url: "'.$link13.'",
					data: "valor="+depto,
					success: function(msg){
						if(msg=="Y.a-Existe"){
							alert("Ya existe un Departamento con esa Descripcion");
						}
						else{
							if(msg=="N.o-SeAgrego"){
								alert("Disculpe. En este momento no se ha podido agregar el departamento, por favor intente mas tarde");
							}else{
								$.post("'.$link10.'",{ x:"" },function(data){$("#depto").html(data);$("#depto").val(msg);})
							}
						}
					}
				});
			}
		}

		function add_grupo(){
			lineaval=$("#linea").val();
			deptoval=$("#depto").val();
			if(lineaval==""){
				alert("Debe seleccionar una Linea a la cual agregar el departamento");
			}else{
				grupo=prompt("Introduza el nombre del GRUPO a agregar a la LINEA seleccionada");
				if(grupo==null){
				}else{
					$.ajax({
					 type: "POST",
					 processData:false,
						url: "'.$link13.'",
						data: "valor="+grupo+"&&valor2="+lineaval+"&&valor3="+deptoval,
						success: function(msg){
							if(msg=="Y.a-Existe"){
								alert("Ya existe una Linea con esa Descripcion");
							}
							else{
								if(msg=="N.o-SeAgrego"){
									alert("Disculpe. En este momento no se ha podido agregar la linea, por favor intente mas tarde");
								}else{
									$.post("'.$link14.'",{ linea:lineaval },function(data){$("#grupo").html(data);$("#grupo").val(msg);})
								}
							}
						}
					});
				}
			}
		}
		';

		$do = new DataObject("sumi");
		if($status=="create" && !empty($id)){
			$do->load($id);
			$do->set('codigo', '');
		}

		$edit = new DataEdit2("Suministros", $do);
		$edit->script($script, "create");
		$edit->script($script, "modify");

		$edit->back_url = site_url("suministros/sumi/filteredgrid");

		$ultimo='<a href="javascript:ultimo();" title="Consultar ultimo c&oacute;digo ingresado"> Consultar ultimo c&oacute;digo</a>';
		$sugerir='<a href="javascript:sugerir();" title="Sugerir un C&oacute;digo aleatorio">Sugerir C&oacute;digo </a>';
		$edit->codigo = new inputField("C&oacute;digo", "codigo");
		$edit->codigo->size=20;
		$edit->codigo->maxlength=15;
		$edit->codigo->rule = "trim|required|strtoupper|callback_chexiste";
		$edit->codigo->mode="autohide";
		$edit->codigo->append($sugerir);
		$edit->codigo->append($ultimo);

		$edit->barras = new inputField("C&oacute;digo Barras", "barras");
		$edit->barras->size=20;
		$edit->barras->maxlength=15;
		$edit->barras->rule = "trim";



		$AddUnidad='<a href="javascript:add_unidad();" title="Haz clic para Agregar una unidad nueva">Agregar Unidad</a>';
		$edit->unidad = new dropdownField("Unidad","unidad");
		$edit->unidad->style='width:180px;';
		//$edit->unidad->option("","");
		$edit->unidad->options("SELECT unidades, unidades as valor FROM unidad ORDER BY unidades");
		$edit->unidad->append($AddUnidad);

		$AddDepto='<a href="javascript:add_depto();" title="Haz clic para Agregar un nuevo Departamento">Agregar Departamento</a>';
		$edit->depto = new dropdownField("Departamento", "depto");
		$edit->depto->rule ="required";
		//$edit->depto->onchange = "get_linea();";
		$edit->depto->option("","Seleccione un Departamento");
		$edit->depto->options("SELECT depto, descrip FROM dpto WHERE tipo='I' ORDER BY depto");
		$edit->depto->append($AddDepto);

		$AddLinea='<a href="javascript:add_linea();" title="Haz clic para Agregar una nueva Linea;">Agregar Linea</a>';
		$edit->linea = new dropdownField("L&iacute;nea","linea");
		$edit->linea->rule ="required";
		$edit->linea->append($AddLinea);
		$depto=$edit->getval('depto');
		if($depto!==FALSE){
			$edit->linea->options("SELECT linea, descrip FROM line WHERE depto='$depto' ORDER BY descrip");
		}else{
			$edit->linea->option("","Seleccione un Departamento primero");
		}

		/*if($edit->_status=='modify' or $edit->_status=='show' or $edit->_status=='idle' or $edit->_status=='create'){
			$depto = ($this->input->post('depto')===FALSE) ? $edit->_dataobject->get("depto") : $this->input->post('dpto');
			$edit->linea->options("SELECT linea, descrip FROM line WHERE depto='$depto' ORDER BY descrip");
		}else{
			$edit->linea->option("","Seleccione un Departamento primero");
		}*/

		$AddGrupo='<a href="javascript:add_grupo();" title="Haz clic para Agregar un nuevo Grupo;">Agregar Grupo</a>';
		$edit->grupo = new dropdownField("Grupo", "grupo");
		$edit->grupo->rule="required";
		$edit->grupo->append($AddGrupo);
		//$edit->grupo->options("SELECT grupo, nom_grup FROM grup nom_grup");//WHERE linea='$linea' ORDER BY
		$linea=$edit->getval('linea');
		if($linea!==FALSE){
			$edit->grupo->options("SELECT grupo, nom_grup FROM grup WHERE linea='$linea' ORDER BY nom_grup");
		}else{
			$edit->grupo->option("","Seleccione un Departamento primero");
		}

		$edit->activo = new dropdownField("Activo", "activo");
		$edit->activo->style='width:100px;';
		$edit->activo->option("S","Si" );
		$edit->activo->option("N","No" );

		$edit->tdecimal2 = new freeField("","free","Usa Decimales");
		$edit->tdecimal2->in="activo";

		$edit->tdecimal = new dropdownField("", "tdecimal");
		$edit->tdecimal->style='width:100px;';
		$edit->tdecimal->option("N","No" );
		$edit->tdecimal->option("S","Si" );
		$edit->tdecimal->in="activo";

		$edit->descrip = new textareaField("Descripci&oacute;n", "descrip");
		$edit->descrip->cols     =50;
		$edit->descrip->rows     =2;
		$edit->descrip->maxlength=45;
		$edit->descrip->rule     = "trim|required|strtoupper";

		$AddMarca='<a href="javascript:add_marca();" title="Haz clic para Agregar una marca nueva">Agregar Marca</a>';
		$edit->marca = new dropdownField("Marca", "marca");
		$edit->marca->style='width:180px;';
		$edit->marca->option("","");
		$edit->marca->options("SELECT marca as codigo, marca FROM marc ORDER BY marca");
		$edit->marca->append($AddMarca);

		$edit->modelo  = new inputField("Modelo", "modelo");
		$edit->modelo->size=20;
		$edit->modelo->maxlength=20;
		$edit->modelo->rule = "trim|strtoupper";

		$edit->exmin = new inputField("Existencia Minima", "exmin");
		$edit->exmin->size=10;
		$edit->exmin->maxlength=12;
		$edit->exmin->css_class='inputonlynum';
		$edit->exmin->rule='numeric|callback_positivo|trim';

		$edit->exmax = new inputField("Existencia Maxima", "exmax");
		$edit->exmax->size=10;
		$edit->exmax->maxlength=12;
		$edit->exmax->css_class='inputonlynum';
		$edit->exmax->rule='numeric|callback_positivo|trim';

		$edit->existen = new inputField("Existencia Actual","existen");
		$edit->existen->when =array("show");



		$edit->buttons("add","modify", "save", "undo", "delete", "back");
		$edit->build();

		$data['content'] = $edit->output;
		$data['title']   = "Maestro de Inventario";
		$data["head"]    = script("jquery.pack.js").script("plugins/jquery.numeric.pack.js").script("plugins/jquery.floatnumber.js").$this->rapyd->get_head();
		$this->load->view('view_ventanas', $data);
	}

	function sug($tabla=''){
		if($tabla=='dpto'){
			$valor=$this->datasis->dameval("SELECT LPAD(hexa,2,0) FROM serie LEFT JOIN dpto ON LPAD(depto,2,0)=LPAD(hexa,2,0) WHERE valor<255 AND depto IS NULL LIMIT 1");
		}elseif($tabla=='line'){
				$valor=$this->datasis->dameval("SELECT LPAD(hexa,2,0) FROM serie LEFT JOIN line ON LPAD(linea,2,0)=LPAD(hexa,2,0) WHERE valor<255 AND linea IS NULL LIMIT 1");
		}elseif($tabla=='grup'){
				$valor=$this->datasis->dameval("SELECT LPAD(hexa,4,0) FROM serie LEFT JOIN grup ON LPAD(grupo,4,0)=LPAD(hexa,4,0) WHERE valor<65535 AND grupo IS NULL LIMIT 1");
		}
		return $valor;
	}

	function ultimo(){
		$ultimo=$this->datasis->dameval("SELECT codigo FROM sumi ORDER BY codigo DESC");
		echo $ultimo;
	}

	function sugerir(){
		$ultimo=$this->datasis->dameval("SELECT LPAD(valor,4,0) FROM serie LEFT JOIN sumi ON LPAD(codigo,4,0)=LPAD(valor,4,0) WHERE valor<65535 AND codigo IS NULL LIMIT 1");
		echo $ultimo;
	}

	function chexiste($codigo){
		$codigo=$this->input->post('codigo');
		$chek=$this->datasis->dameval("SELECT COUNT(*) FROM sumi WHERE codigo='$codigo'");
		if ($chek > 0){
			$descrip=$this->datasis->dameval("SELECT descrip FROM sumi WHERE codigo='$codigo'");
			$this->validation->set_message('chexiste',"El codigo $codigo ya existe para el producto");
			return FALSE;
		}else {
  		 return TRUE;
		}
	}

	function chexiste2($alterno){
		$alterno=$this->input->post('codigo');
		$chek=$this->datasis->dameval("SELECT COUNT(*) FROM sumi WHERE alterno='$alterno'");
		if ($chek > 0){
			$descrip=$this->datasis->dameval("SELECT descrip FROM sumi WHERE alterno='$alterno'");
			$this->validation->set_message('chexiste',"El codigo alterno $alterno ya existe para el producto $desrip");
			return FALSE;
		}else {
  		return TRUE;
		}
	}
	
	function autocomplete(){
		$query  ="SELECT codigo label,codigo,descrip,unidad FROM sumi";
		
		$mSQL   = $this->db->query($query);
		$arreglo= $mSQL->result_array($query);
		foreach($arreglo as $key=>$value)
			foreach($value as $key2=>$value2) 
				$arreglo[$key][$key2] = ($value2);

		echo json_encode($arreglo);
	}
	
	function instalar(){
		$query="ALTER TABLE `sumi`	ADD COLUMN `id` INT NOT NULL AUTO_INCREMENT";
		$this->db->simple_query($query);
		$query="ALTER TABLE `sumi` DROP PRIMARY KEY";
		$this->db->simple_query($query);
		$query="ALTER TABLE `sumi` ADD PRIMARY KEY (`id`)";
		$this->db->simple_query($query);
	}
}
