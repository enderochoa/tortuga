<?php

class Sinvshow extends Controller {
	
	function sinvshow(){
		parent::Controller(); 
		$this->load->library("rapyd");
	}
	function dataedit() {  
		$this->rapyd->load('dataedit');

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
		
		$edit = new DataEdit("Maestro de Inventario", "sinv");
		$edit->script($ajax_onchange);
				
		$edit->back_url = site_url("inventario/sinv/filteredgrid");
		
		$edit->codigo = new inputField("C&oacute;digo", "codigo");
		$edit->codigo->size=17;
		$edit->codigo->maxlength=15;
		$edit->codigo->rule = "trim|required|strtoupper";
		$edit->codigo->mode="autohide";
		
		$edit->alterno = new inputField("C&oacute;digo Alterno", "alterno");
		$edit->alterno->size=17;  
		$edit->alterno->maxlength=15;
		$edit->alterno->rule = "trim";		
		
		$edit->enlase  = new inputField("C&oacute;digo Caja", "enlace");
		$edit->enlase ->size=17;
		$edit->enlase->maxlength=15;
		$edit->enlase->rule = "trim";
				
		$edit->barras = new inputField("C&oacute;digo Barras", "barras");
		$edit->barras->size=17;
	  $edit->barras->maxlength=15;
	  $edit->barras->rule = "trim";
	  
	  $edit->descrip = new inputField("Descripci&oacute;n", "descrip");
		$edit->descrip->size=48;
	  $edit->descrip->maxlength=45;
		$edit->descrip->rule = "trim|required|strtoupper";
	  		
		$edit->marca = new dropdownField("Marca", "marca");
		$edit->marca->style='width:150px;';
		$edit->marca->option("","");  
		$edit->marca->options("SELECT marca as codigo, marca FROM marc ORDER BY marca");  
		
		$edit->tipo = new dropdownField("Tipo", "tipo");
		$edit->tipo->style='width:150px;';
		$edit->tipo->option("Articulo","Art&iacute;culo" );
		$edit->tipo->option("Servicio","Servicio");
		$edit->tipo->option("Descartar","Descartar");
		$edit->tipo->option("Consumo","Consumo");
		$edit->tipo->option("Fraccion","Fracci&oacute;n");
    $edit->tipo->option("Lote","Lote");
    
		$edit->grupo = new dropdownField("Grupo", "grupo");
				
		$edit->descrip2 = new inputField("Descripci&oacute;n Corta", "descrip2");
		$edit->descrip2->size=20;
		
		$edit->clave = new inputField("Clave", "clave");
		$edit->clave->size=17;
	  $edit->clave->maxlength=15;
	  $edit->clave->rule = "trim";
		
		$edit->serial = new dropdownField ("Serial", "serial");
    $edit->serial->style='width:80px;';
		$edit->serial->option("S","Si" );
		$edit->serial->option("N","No" );
			
		$edit->unidad = new dropdownField("Unidad","unidad");
		$edit->unidad->style='width:150px;';
		$edit->unidad->option("","");  
		$edit->unidad->options("SELECT unidades, unidades as valor FROM unidad ORDER BY unidades");  
		
		$edit->tdecimal = new dropdownField("Unidad Decimal", "tdecimal");
    $edit->tdecimal->style='width:80px;';
		$edit->tdecimal->option("S","Si" );
		$edit->tdecimal->option("N","No" ); 
	
		$edit->exmin = new inputField("Existencia Minima", "exmin");
		$edit->exmin->size=15;
		$edit->exmin->when =array("show");
		
		$edit->exmax = new inputField("Existencia Maxima", "exmax");
		$edit->exmax->size=15;
		$edit->exmax->when =array("show");
		
		$edit->exord = new inputField("Existencia Ordenada", "exord");
		$edit->exord->size=15;
		$edit->exord->when =array("show");
		
		$edit->exdes = new inputField("Pedido", "exdes");
		$edit->exdes->size=15;
		$edit->exdes->when =array("show");
		
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
			
    $edit->activo = new dropdownField("Activo", "activo");
		$edit->activo->style='width:80px;';
		$edit->activo->option("S","Si" );
		$edit->activo->option("N","No" ); 
		
		$edit->fracci  = new inputField("Unidad por Caja", "fracci");
		$edit->fracci ->size=15; 
		
		$edit->comision  = new inputField("Comisi&oacute;n", "comision");
		$edit->comision ->size=5;
		$edit->comision->rule='numeric';
	  $edit->comision->maxlength=5;
				
		$edit->peso  = new inputField("Peso Kg.", "peso");
		$edit->peso->rule='numeric';
		$edit->peso ->size=15;
		$edit->peso->maxlength=12;
				
		$edit->modelo  = new inputField("Modelo", "alterno");
		$edit->modelo->size=17;  
		$edit->modelo->maxlength=15;
		$edit->modelo->rule = "trim";	
				
		$edit->clase= new dropdownField("Clase", "clase");
		$edit->clase->style='width:150px;';
		$edit->clase->option("A","Alta Rotacion");
		$edit->clase->option("B","Media Rotacion");
		$edit->clase->option("C","Baja Rotacion");
		$edit->clase->option("I","Importacion Propia");
				
		$edit->us = new inputField("US$", "dolar");
		$edit->us->size=15;
		
	  $edit->existen = new inputField("Existencia Actual", "existen");
		$edit->existen ->size=15;
	  $edit->existen->when =array("show");
		
		$edit->garantia = new inputField("Dias de Garantia", "garantia");
		$edit->garantia->size=5;
		$edit->garantia->maxlength=3;
		$edit->garantia->rule='numeric';
		
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
			$edit->$objeto->when =array("show");
			
			$objeto="pfecha$i";
			$edit->$objeto = new dateonlyField("Fecha Prv $i", $objeto);
			$edit->$objeto->size=10;
      $edit->$objeto->when =array("show");
      
			$objeto="prepro$i";
			$edit->$objeto = new inputField("Precio Prv $i", $objeto);
			$edit->$objeto->css_class='inputnum';
			$edit->$objeto->size=10;
			$edit->$objeto->when =array("show");
		}
		
		$codigo=$edit->_dataobject->get("codigo");
		$edit->almacenes = new containerField('almacenes',$this->_detalle($codigo));
		$edit->almacenes->when = array("show","modify");
		
		$edit->dpto = new dropdownField("Departamento", "depto");
		$edit->dpto->option("","");
		$edit->dpto->options("SELECT depto, descrip FROM dpto ORDER BY descrip");
		$edit->dpto->onchange = "get_linea();";

		$edit->linea = new dropdownField("Linea","linea");
		$edit->linea->option("","Seleccione un departamento");
		$edit->linea->onchange = "get_grupo();";

		$edit->grupo = new dropdownField("Grupo", "grupo");
		$edit->grupo->option("","Seleccione una L&iacute;nea");

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
    //$data["head"]    = script("jquery.pack.js").script("plugins/jquery.numeric.pack.js").script("plugins/jquery.floatnumber.js").$this->rapyd->get_head();
		$data['title']   = ' Maestro de Inventario ';
		$this->load->view('view_ventanas', $data);
	}
	function _detalle($codigo){
  	$salida='';  	
		return $salida;
  }  
}