<?php
//maestro
class Sinv extends Controller {
	
	function sinv(){
		parent::Controller(); 
		$this->load->library("rapyd");
		//$this->rapyd->set_connection('supermer');
		//$this->load->database('supermer',TRUE);
	}
	
	#### index #####
	function index(){
		//$this->datasis->modulo_id(309,1);
		redirect("inventario/sinv/filteredgrid");
	}
	
	##### DataFilter + DataGrid #####
	function filteredgrid(){
		$this->rapyd->load("datafilter2","datagrid");
		//$this->rapyd->uri->keep_persistence();
			
		rapydlib("prototype");
		$ajax_onchange = '
			  function get_linea(){
			    var url = "'.site_url('reportes/sinvlineas').'";
			    var pars = "dpto="+$F("depto");
			    var myAjax = new Ajax.Updater("td_linea", url, { method: "post", parameters: pars });
		  			    
			  }
			  
			  function get_grupo(){
			    var url = "'.site_url('reportes/sinvgrupos').'";
			    var pars = "dpto="+$F("depto")+"&linea="+$F("linea");
			    var myAjax = new Ajax.Updater("td_grupo", url, { method: "post", parameters: pars });
			  }';
			  
			  		
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

		//filter
		$filter = new DataFilter2("Filtro por Producto", 'sinv');
  	$filter->codigo = new inputField("C&oacute;digo", "codigo");
  	$filter->script($ajax_onchange);

		
		$filter->descrip = new inputField("Descripci&oacute;n", "descrip");
		$filter->descrip->db_name='CONCAT_WS(" ",descrip,descrip2)';
		
		$filter->tipo = new dropdownField("Tipo", "tipo");
		$filter->tipo->option("","Todos");
		$filter->tipo->option("Articulo","Art&iacute;culo");
		$filter->tipo->option("Servicio","Servicio");
		$filter->tipo->option("Descartar","Descartar");
		$filter->tipo->option("Consumo","Consumo");
		$filter->tipo->option("Fraccion","Fracci&oacute;n");
		
		$filter->clave = new inputField("Clave", "clave");

		$filter->activo = new dropdownField("Activo", "activo");
		$filter->activo->option("","");
		$filter->activo->option("S","Si");
		$filter->activo->option("N","No");
		
		$filter->proveed = new inputField("Beneficiario", "proveed");
		$filter->proveed->append($bSPRV);
		$filter->proveed->clause ="in";
		$filter->proveed->db_name='( s.prov1, s.prov2, s.prov3 )';
		
		$filter->dpto = new dropdownField("Departamento", "depto");
		$filter->dpto->option("","");
		$filter->dpto->options("SELECT depto, descrip FROM dpto ORDER BY descrip");
		$filter->dpto->onchange = "get_linea();";

		$filter->linea = new dropdownField("Linea","linea");
		$filter->linea->option("","Seleccione un departamento");
		$filter->linea->onchange = "get_grupo();";

		$filter->grupo = new dropdownField("Grupo", "grupo");
		$filter->grupo->option("","Seleccione una L&iacute;nea");
		
		$filter->marca = new dropdownField("Marca", "marca");
		$filter->marca->option("","");  
		$filter->marca->options("SELECT TRIM(marca) AS clave, TRIM(marca) AS valor FROM marc ORDER BY marca"); 
		
		$filter->buttons("reset","search");
		$filter->build();
		
		$uri = "inventario/sinv/dataedit/show/<#codigo#>";
		
		$grid = new DataGrid("Lista de Art&iacute;culos");
		$grid->order_by("codigo","asc");
		$grid->per_page = 20;
		$link=anchor('/inventario/sinv/dataedit/show/<#id#>','<#codigo#>');
		
				
		$grid->column("c&oacute;digo",$link);
		$grid->column("Descripci&oacute;n","descrip");
		$grid->column("Precio 1","precio1");
		$grid->column("Precio 2","precio2");
		$grid->column("Precio 3","precio3");
		$grid->column("Precio 4","precio4");
										
		$grid->add("inventario/sinv/dataedit/create");
		$grid->build();
		//grid
		
		$data["crud"] = $filter->output . $grid->output;
		$data["titulo"] = 'Lista de Artículos';

		$content["content"]   = $this->load->view('rapyd/crud', $data, true);
		$content["rapyd_head"] = $this->rapyd->get_head();
		$content["code"] = '';
		$content["lista"] = "
			<h3>Editar o Agregar</h3>
			<div>Con esta pantalla se puede editar o agregar datos a los Departamentos del M&oacute;dulo de Inventario</div>
			<div class='line'></div>
			<a href='#' onclick='window.close()'>Cerrar</a>
			<div class='line'></div>\n<br><br><br>\n";
		$this->load->view('rapyd/tmpsolo', $content);
	}

	function dataedit() {  
		$this->rapyd->load('dataedit'); 
		//rapydlib("prototype");
				$ajax_onchange = '
			  function get_linea(){
			    var url = "'.site_url('reportes/sinvlineas').'";
			    var pars = "dpto="+$F("depto");
			    var myAjax = new Ajax.Updater("td_linea", url, { method: "post", parameters: pars });
			   
			  }
			  
			  function get_grupo(){
			    var url = "'.site_url('reportes/sinvgrupos').'";
			    var pars = "dpto="+$F("depto")+"&linea="+$F("linea");
			    var myAjax = new Ajax.Updater("td_grupo", url, { method: "post", parameters: pars });
			  }';
		$ajax_onchange = '';
		$edit = new DataEdit("Maestro de Inventario", "sinv");
		//$edit->script($ajax_onchange);
		//$edit->script($ajax_onchange,"modify");
		$edit->back_url = site_url("inventario/sinv/filteredgrid");
		
		$edit->codigo = new inputField("C&oacute;digo", "codigo");
		$edit->codigo->size=15;
		$edit->codigo->rule = "required";
		$edit->codigo->mode="autohide";
		
		$edit->marca = new dropdownField("Marca", "marca");
		$edit->marca->style='width:110px;';
		$edit->marca->option("","");  
		$edit->marca->options("SELECT marca as codigo, marca FROM marc ORDER BY marca");  
		
		$edit->tipo = new dropdownField("Tipo", "tipo");
		$edit->tipo->style='width:110px;';
		$edit->tipo->option("Articulo","Art&iacute;culo" );
		$edit->tipo->option("Descartar","Descartar"  );
		$edit->tipo->option("Consumo","Consumo"  );
		$edit->tipo->option("Fraccion","Fracci&oacute;n");

		$edit->grupo = new dropdownField("Grupo", "grupo");
		
		$edit->descrip = new inputField("Descripci&oacute;n", "descrip");
		$edit->descrip->size=40;
		$edit->descrip->rule = "required";
		
		$edit->descrip2 = new inputField("Descripci&oacute;n Corta", "descrip2");
		$edit->descrip2->size=20;
		
		$edit->clave = new inputField("Clave", "clave");
		$edit->clave->size=15;
		
		$edit->serial = new dropdownField ("Serial", "serial");
    $edit->serial->style='width:80px;';
		$edit->serial->option("S","Si" );
		$edit->serial->option("N","No" ); 
	
		$edit->exmin = new inputField("Existencia Minima", "exmin");
		$edit->exmin->size=15;
		
		$edit->exmax = new inputField("Existencia Maxima", "exmax");
		$edit->exmax->size=15;
		
		$edit->exord = new inputField("Existencia Ordenada", "exord");
		$edit->exord->size=15;
		
		$edit->exdes = new inputField("Pedido", "exdes");
		$edit->exdes->size=15;
		
		$edit->ultimo = new inputField("Ultimo", "ultimo");
		$edit->ultimo->css_class='inputnum';
		$edit->ultimo->size=15;
		
		$edit->iva = new inputField("Iva", "iva");
		$edit->iva->css_class='inputnum';
		$edit->iva->size=15;
		$edit->iva->onchange = "calculos('I');";
		
		$edit->pond = new inputField("Promedio", "pond");
		$edit->pond->css_class='inputnum';
		$edit->pond->size=15;

		$edit->formcal = new dropdownField("Base C&aacute;lculo", "formcal");
		$edit->formcal->style='width:110px;';
		$edit->formcal->option("U","Ultimo" );
		$edit->formcal->option("P","Promedio" );     
		$edit->formcal->option("M","Mayor" );
		$edit->formcal->onchange = "calculos('I');";

		$edit->redecen = new dropdownField("Redondear", "redecen");
		$edit->redecen->style='width:110px;';
		$edit->redecen->option("NO","No");
		$edit->redecen->option("F","Fracción");
		$edit->redecen->option("D","Decena" );  
		$edit->redecen->option("C","Centena"  );
	  $edit->redecen->onchange = "redon();";
	
		$edit->barras = new inputField("C&oacute;digo Barras", "barras");
		$edit->barras->size=15;
		
    $edit->activo = new dropdownField("Activo", "activo");
		$edit->activo->style='width:80px;';
		$edit->activo->option("S","Si" );
		$edit->activo->option("N","No" ); 
		
		$edit->tdecimal = new dropdownField("Unidad Decimal", "tdecimal");
    $edit->tdecimal->style='width:80px;';
		$edit->tdecimal->option("S","Si" );
		$edit->tdecimal->option("N","No" ); 
		
		$edit->alterno = new inputField("C&oacute;digo Alterno", "alterno");
		$edit->alterno->size=15;  
		
		$edit->fracci  = new inputField("Unidad por Caja", "fracci");
		$edit->fracci ->size=15; 
		
		$edit->comision  = new inputField("Comisi&oacute;n", "comision");
		$edit->comision ->size=15;
		
		$edit->unidad  = new inputField("Unidad Medida", "unidad");
		$edit->unidad ->size=15;
		
		$edit->peso  = new inputField("Peso Kg.", "peso");
		$edit->peso ->size=15;
				
		$edit->modelo  = new inputField("Modelo", "alterno");
		$edit->modelo ->size=15;
		
		$edit->enlase  = new inputField("C&oacute;digo Caja", "enlace");
		$edit->enlase ->size=15;
		
		$edit->clase= new dropdownField("Clase", "clase");
		$edit->clase->style='width:50px;';
		$edit->clase->size=5;
		$edit->clase->option("A","A");
		$edit->clase->option("B","B");
		$edit->clase->option("C","C");
		$edit->clase->option("D","D");
				
		$edit->us = new inputField("US$", "dolar");
		$edit->us->size=15;
		
	  $edit->existen = new inputField("Existencia Actual", "existen");
		$edit->existen ->size=15;
		
		$edit->garantia = new inputField("Dias de Garantia", "garantia");
		$edit->garantia->size=15;
		
		$edit->fechav = new  dateonlyField("Ultima Venta", "fechav");
		$edit->fechav->size=15;
		
		for($i=1;$i<=4;$i++){	
			$objeto="margen$i";
			$edit->$objeto = new inputField("Margen $i", $objeto);
			$edit->$objeto->css_class='inputnum';
			$edit->$objeto->size=15;
			$edit->$objeto->onchange = "calculos('I');";

			$objeto="base$i";
			$edit->$objeto = new inputField("Base $i", $objeto);
			$edit->$objeto->css_class='inputnum';
			$edit->$objeto->size=15;
			$edit->$objeto->onchange = "cambiobase('I');";
			
			$objeto="precio$i";
			$edit->$objeto = new inputField("Precio $i", $objeto);
			$edit->$objeto->css_class='inputnum';
			$edit->$objeto->size=15;
			$edit->$objeto->onchange = "cambioprecio('I');";
		}
		
		for($i=1;$i<=4;$i++){
			$objeto="prov$i";
			$edit->$objeto = new inputField("Beneficiario $i", $objeto);
			$edit->$objeto->size=15;
			
			$objeto="pfecha$i";
			$edit->$objeto = new dateonlyField("Fecha Prv $i", $objeto);
			$edit->$objeto->size=10;
			       
			$objeto="prepro$i";
			$edit->$objeto = new inputField("Precio Prv $i", $objeto);
			$edit->$objeto->css_class='inputnum';
			$edit->$objeto->size=10;
		}
		
		$codigo=$edit->_dataobject->get("codigo");
		$edit->almacenes = new containerField('almacenes',$this->_detalle($codigo));
		$edit->almacenes->when = array("show","modify");
		
		//if($this->rapyd->uri->is_set("modify") or $this->rapyd->uri->is_set("create")){
			$depto  =$edit->_dataobject->get("depto");
			$linea  =$edit->_dataobject->get("linea");
			
			$edit->dpto = new dropdownField("Departamento", "depto");
			$edit->dpto->data = null;
			$edit->dpto->option("","");
			$edit->dpto->options("SELECT depto, descrip FROM dpto ORDER BY descrip");
			$edit->dpto->onchange = "get_linea();";

			$edit->linea = new dropdownField("Linea", "linea");
			$edit->linea->data = null;
			$edit->linea->onchange = "get_grupo();";
			
			$edit->linea->options("SELECT linea,descrip FROM line WHERE depto = '$depto' ORDER BY descrip");
			$edit->grupo->options("SELECT grupo, nom_grup FROM grup WHERE depto='$depto' AND linea='$linea'  ORDER BY nom_grup ");
			
			
		/*}else{
			/*
			$edit->linea->option("","Seleccione un departamento");
			$edit->grupo->option("","Seleccione una linea");
			
		}*/
		//$edit->buttons("modify", "save", "undo", "delete", "back");
		$edit->buttons("modify", "save", "undo", "back");
		$edit->build();
		
		
		//echo $edit->codigo->value;
		$link=site_url('inventario/sinv');
		$data['script']  =<<<script
		<script language="javascript" type="text/javascript">
		$(document).ready(function(){
			$.ajax({
			type: "POST",
			url: $link+"/sinvlineas",
			data: "dpto="+$('#depto').val(),
			success: function(msg){
				alert( "Data Saved: " + msg );
			}
		});
		</script>
script;
		
		$conten["form"]  =&  $edit;
		$data['content'] = $this->load->view('view_sinvmaestro', $conten,true);
		$data["head"]    = script("tabber.js").script("jquery.pack.js").script("plugins/jquery.numeric.pack.js").script("plugins/jquery.floatnumber.js").script("sinvmaes.js").$this->rapyd->get_head();
		$data['title']   = ' Maestro de Inventario ';
		$this->load->view('view_ventanas', $data);
	}  
  function _detalle($codigo){
  	$salida='';
  	/*
  	if(!empty($codigo)){
  		$this->rapyd->load('dataedit','datagrid'); 
			
			$grid = new DataGrid('Cantidad por almac&eacute;n');
			$grid->db->select('ubica,locali,cantidad,fraccion');
			$grid->db->from('ubic');
			$grid->db->where("codigo='$codigo'");
			
			$grid->column("Almacen"          ,"ubica" );
			$grid->column("Ubicaci&oacute;n" ,"locali");
			$grid->column("Cantidad"         ,"cantidad",'align="RIGHT"');
			$grid->column("Fracci&oacute;n"  ,"fraccion",'align="RIGHT"');
			
			$grid->build();
			$salida=$grid->output;
		}*/
		return $salida;
  }
     
	function sinvlineas(){  
		$this->rapyd->load("fields");
		$where = "";
		$sql = "SELECT linea,descrip FROM line ";
		$linea = new dropdownField("Linea", "linea");
		$dpto=$this->input->post('dpto');

		if ($dpto){
		  $where = "WHERE depto = ".$this->db->escape($dpto);
		  $sql = "SELECT linea,descrip FROM line $where ORDER BY descrip";
		  $linea->option("","");
			$linea->options($sql);
		}else{
			 $linea->option("","Seleccione Un Departamento");
		} 
		$linea->status   = "modify";
		$linea->onchange = "get_grupo();";
		$linea->build();
		echo $linea->output;
	}
	function sinvgrupos(){
		$this->rapyd->load("fields");  
		$where = "";  
		$line=$this->input->post('line');
		$dpto=$this->input->post('dpto'); 
		
		$grupo = new dropdownField("Grupo", "grupo");
		if ($line AND $dpto AND !(empty($line) OR empty($dpto))) {
			$where .= "WHERE depto = ".$this->db->escape($dpto);
			$where .= "AND linea = ".$this->db->escape($line);
			$sql = "SELECT grupo, nom_grup FROM grup $where";
			$grupo->option("","");
			$grupo->options($sql);
		}else{
			$grupo->option("","Seleccione una linea"); 
		} 
		$grupo->status = "modify";  
		$grupo->build();
		echo $grupo->output; 
	}
	function instalar(){
		$mSQL='ALTER TABLE `sinv` DROP PRIMARY KEY';
		$this->db->simple_query($mSQL);
		$mSQL='ALTER TABLE `sinv` ADD UNIQUE `codigo` (`codigo`)';
		$this->db->simple_query($mSQL);
		$mSQL='ALTER TABLE sinv ADD id INT AUTO_INCREMENT PRIMARY KEY';
		$this->db->simple_query($mSQL);
	}

	
}
?>