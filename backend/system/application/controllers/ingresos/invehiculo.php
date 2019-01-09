<?php
class invehiculo extends Controller {
	
	var $titp  = 'Unidades Administrativas';
	var $tits  = 'Unidad Administrativa';
	var $url   = 'presupuesto/uadministra/';
	
	function invehiculo(){
		parent::Controller();
		$this->load->library("rapyd");
	}
	
	function  index(){
		redirect("ingresos/invehiculo/filteredgrid");
	}
	
	function filteredgrid(){
		//$this->datasis->modulo_id(83,1);
		
		$this->rapyd->load("datafilter2","datagrid");
		
		$filter = new DataFilter2(""); 
		$filter->db->select(array("a.codvehiculo codvehi","a.codclase codclase","a.codmarca codmarca",
		"a.asocia asocia","a.tipo tipo","a.modelo modelo","a.color color","a.capaci capaci","a.monto monto",
		"a.ultanio ultanio","a.serialmot serialmot","a.panterior placant","a.pactual placact","a.anio anio",
		"a.peso peso","a.serialcar serialcar","a.deudant deudant","b.nombre nomclase","c.marca nommarca","d.nombre nomaso"));
		
		$filter->db->from("invehiculo a");  
		$filter->db->join("clasevehi b" ,"a.codclase=b.codigo","left");
		$filter->db->join("marcavehi c" ,"a.codmarca=c.codigo","left");
		$filter->db->join("asovehi d"   ,"a.asocia=d.codigo"  ,"left");
		
		$filter->codigo = new inputField("C&oacute;digo", "codvehi");
		$filter->codigo->clause="likerigth";
		$filter->codigo->maxlength = 6;
		$filter->codigo->size=6;
		
		$filter->placa = new inputField("Placa", "placact");
		$filter->placa->clause="likerigth";
		$filter->placa->maxlength = 20;
		$filter->placa->size=20;
		
		$filter->codigoclase = new dropdownField("Marca","codclase");	
		$filter->codigoclase->option("","");
		$filter->codigoclase->options("SELECT codigo, CONCAT_WS(' ',codigo,marca)as a FROM marcavehi ORDER BY codigo");
		
		$filter->codigomarca = new dropdownField("Clase","codmarca");		
		$filter->codigomarca->option("","");
		$filter->codigomarca->options("SELECT codigo, CONCAT_WS(' ',codigo,nombre)as a FROM clasevehi ORDER BY codigo");
		
		$filter->asocia = new dropdownField("Asociaci&oacute;n","asocia");		
		$filter->asocia->option("","");
		$filter->asocia->options("SELECT codigo, CONCAT_WS(' ',codigo,nombre)as a FROM asovehi ORDER BY codigo");		
		
		$filter->buttons("reset","search");
		
		$filter->build();

		$grid = new DataGrid("");
		
		$grid->order_by("codvehiculo","asc");
		
		$grid->column_orderby("C&oacute;digo"       ,"codvehi"    ,"codvehi"   ,"align='center'    ");
		$grid->column_orderby("Clase"               ,"nomclase"   ,"nomclase"  ,"align='left'NOWRAP");
		$grid->column_orderby("Marca"               ,"nommarca"   ,"nommarca"  ,"align='left'NOWRAP");		
		$grid->column_orderby("Tipo"                ,"tipo"       ,"tipo"      ,"align='center'    ");
		$grid->column_orderby("Modelo"              ,"modelo"     ,"modelo"    ,"align='left'NOWRAP");
		$grid->column_orderby("Color"               ,"color"      ,"color"     ,"align='center'    ");
		$grid->column_orderby("Capacidad"           ,"capaci"     ,"capaci"    ,"align='center'    ");
		$grid->column_orderby("Monto"               ,"monto"      ,"monto"     ,"align='right'     ");
		$grid->column_orderby("Ultimo A."           ,"ultanio"    ,"ultanio"   ,"align='center'    ");
		$grid->column_orderby("Serial Motor"        ,"serialmot"  ,"serialmot" ,"align='left'NOWRAP");
		$grid->column_orderby("P.Anterior"          ,"placant"    ,"placant"   ,"align='left'NOWRAP");
		$grid->column_orderby("P.Actual"            ,"placact"    ,"placact"   ,"align='left'NOWRAP");
		$grid->column_orderby("A&ntilde;o"          ,"anio"       ,"anio"      ,"align='center'    ");
		$grid->column_orderby("Asociaci&oacute;n"   ,"nomaso"     ,"nomaso"    ,"align='left'NOWRAP");
		$grid->column_orderby("Peso"                ,"peso"       ,"peso"      ,"align='center'    ");
		$grid->column_orderby("Serial Carroceria"   ,"serialcar"  ,"serialcar" ,"align='left'NOWRAP");
		$grid->column_orderby("Deuda Ant."          ,"deudant"    ,"deudant"   ,"align='right'     ");
		
		$grid->column_orderby("Placa"                 ,"placact"   ,"placact"    ,"align='left'");
		
		$grid->add("ingresos/invehiculo/dataedit/create");
		
		$grid->build();
		
		$data['filtro']  = $filter->output;
		$data['content'] = $grid->output;
		$data['script'] = script("jquery.js")."\n";
		$data['title']   = "Vehiculo";//" $this->titp ";
		$data["head"]    = $this->rapyd->get_head();
		$this->load->view('view_ventanas', $data);
	}
	
	function dataedit(){
		//$this->datasis->modulo_id(111,1);
		
		$this->rapyd->load("dataedit");
		
		$script='
			$(".inputnum").numeric(".");
		';
		
		$edit = new DataEdit("Vehiculo", "invehiculo");
		$edit->script($script,"create");
		
		$edit->back_url = site_url("ingresos/invehiculo/filteredgrid");
		
		$edit->codigoclase = new dropdownField("Clase","codclase");		
		$edit->codigoclase->option("","");
		$edit->codigoclase->rule='required';
		$edit->codigoclase->options("SELECT codigo, CONCAT_WS(' ',codigo,nombre)as a FROM clasevehi ORDER BY codigo");
		
		$edit->codigomarca = new dropdownField("Marca","codmarca");		
		$edit->codigomarca->option("","");
		$edit->codigomarca->rule='required';
		$edit->codigomarca->options("SELECT codigo, CONCAT_WS(' ',codigo,marca)as a FROM marcavehi ORDER BY codigo");
		
		$edit->asocia = new dropdownField("Asociacion","asocia");		
		$edit->asocia->option("","");
		$edit->asocia->options("SELECT codigo, CONCAT_WS(' ',codigo,nombre)as a FROM asovehi ORDER BY codigo");
		
		$edit->modelo = new inputField("Modelo", "modelo");
		$edit->modelo->mode="autohide";
		$edit->modelo->maxlength=50;
		$edit->modelo->size=30;
		
		$edit->tipo = new inputField("Tipo", "tipo");
		$edit->tipo->mode="autohide";
		$edit->tipo->maxlength=30;
		$edit->tipo->size=20;
		
		$edit->color = new inputField("Color", "color");
		$edit->color->mode="autohide";
		$edit->color->maxlength=30;
		$edit->color->size=20;
		
		$edit->serialm = new inputField("Serial Motor", "serialmot");
		$edit->serialm->mode="autohide";
		$edit->serialm->maxlength=30;
		$edit->serialm->size=20;
		
		$edit->serialc = new inputField("Serial Carroceria", "serialcar");
		$edit->serialc->maxlength=30;
		$edit->serialc->size=20;
		
		$edit->pant = new inputField("Placa Anterio", "panterior");
		$edit->pant->mode="autohide";
		$edit->pant->maxlength=20;
		$edit->pant->size=20;
		
		$edit->pact = new inputField("Placa Actual", "pactual");
		$edit->pact->mode="autohide";
		$edit->pact->maxlength=20;
		$edit->pact->size=20;
		
		$edit->anio = new inputField("A&ntilde;o", "anio");
		$edit->anio->mode="autohide";
		$edit->anio->maxlength=10;
		$edit->anio->size=10;
		
		$edit->monto = new inputField("Monto", "monto");
		$edit->monto->mode="autohide";
		$edit->monto->maxlength=10;
		$edit->monto->size=10;
		
		$edit->ultanio = new inputField("Ultimo A&ntilde;o", "ultanio");
		$edit->ultanio->mode="autohide";
		$edit->ultanio->maxlength=10;
		$edit->ultanio->size=10;

		$edit->deudant =new inputField("Deuda Anterior","deudant");
		$edit->deudant->maxlength=10;
		$edit->deudant->size=10;
		
		$edit->capaci = new inputField("Capacidad", "capaci");
		$edit->capaci->mode="autohide";
		$edit->capaci->maxlength= 4;
		$edit->capaci->size= 4;
		
		$edit->peso = new inputField("Peso", "peso");
		$edit->peso->maxlength= 4;
		$edit->peso->size= 4;
		
		$edit->buttons("modify", "save", "undo", "delete", "back");
		 
		$edit->build();

		$data['content'] = $edit->output;
		$data['title']   = "Vehiculo";
		$data["head"]    = script("jquery.pack.js").$this->rapyd->get_head();
		$this->load->view('view_ventanas', $data);
		
	}

}
?>