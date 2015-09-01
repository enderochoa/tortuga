<?php
class ttributo extends Controller {
	
	function ttributo(){
		parent::Controller();
		$this->load->library("rapyd");
	}
	
	function  index(){
		redirect("ingresos/ttributo/filteredgrid");
	}
	
	function filteredgrid(){
		//$this->datasis->modulo_id(40,1);
		
		$this->rapyd->load("datafilter2","datagrid");//
		
		$filter = new DataFilter2("","ttributo");
		
		$filter->codigo = new inputField("C&oacute;digo", "codigo");
		$filter->codigo->size=5;
		//$filter->codigo->clause="likerigth";
		
		$filter->nombre = new inputField("Nombre", "nombre");
		$filter->nombre->size=40;
		$filter->nombre->clause="likerigth";
			
		$filter->cancelado = new dropdownField("Cancelado","cancelado");		
		//$filter->codigoejec->rule='required';
		$filter->cancelado->option("","");
		$filter->cancelado->option("s","Si");
		$filter->cancelado->option("n","No");
		
		$filter->mensaje = new inputField("Mensaje", "mensaje");
		$filter->mensaje->size=20;
		$filter->mensaje->clause="likerigth";
		
		$filter->momto = new inputField("Monto", "monto");
		$filter->momto->size=10;                        
		$filter->momto->clause="likerigth";            
		
		$filter->emireci = new dropdownField("Emitio Recivo","emireci");		
		//$filter->codigoejec->rule='required';
		$filter->emireci->option("","");
		$filter->emireci->option("s","Si");
		$filter->emireci->option("n","No");
		
		$filter->deudante = new dropdownField("Deuda Anterior","deudante");		
		//$filter->codigoejec->rule='required';
		$filter->deudante->option("","");
		$filter->deudante->option("s","Si");
		$filter->deudante->option("n","No");
		
		$filter->agua = new inputField("Agua", "agua");
		$filter->agua->size=10;                        
		$filter->agua->clause="likerigth";
		
		$filter->buttons("reset","search");
		
		$filter->build();
		
		$grid = new DataGrid("");
		
		$grid->order_by("codigo","asc");
		
		$grid->column_orderby("C&oacute;digo" ,"codigo"    ,"codigo"     ,"align='left'       ");
		$grid->column_orderby("Nombre"        ,"nombre"    ,"nombre"     ,"align='left' NOWRAP");
		$grid->column_orderby("Cancelado"     ,"cancelado" ,"cancelado"  ,"align='left' NOWRAP");
		$grid->column_orderby("Mensaje"       ,"mensaje"   ,"mensaje"    ,"align='left' NOWRAP");
		$grid->column_orderby("Monto"         ,"monto"     ,"monto"      ,"align='right'NOWRAP");
		$grid->column_orderby("E.Recibo"      ,"emireci"   ,"emireci"    ,"align='left' NOWRAP");
		$grid->column_orderby("D.Anterior"    ,"deudante"  ,"deudante"   ,"align='left' NOWRAP");
		$grid->column_orderby("Agua"          ,"agua"      ,"agua"       ,"align='right'NOWRAP");
		
		$grid->add("ingresos/ttributo/dataedit/create");
		
		$grid->build();
		
		$data['content'] = $grid->output;
		$data['filtro']  = $filter->output;
		$data['title']   = "Tributos o Impuestos"; //"  ";
		$data["script"]  = script("jquery.js")."\n"; 
		$data["head"]    = $this->rapyd->get_head();
		$this->load->view('view_ventanas', $data);
	}
	
	function dataedit(){
		//$this->datasis->modulo_id(105,1);
		
		$this->rapyd->load("dataedit");
		
		$script='
			$(".inputnum").numeric(".");
		';
		
		$edit = new DataEdit("Tributos o impuestos", "ttributo");
		$edit->script($script,"create");
		
		$edit->back_url = site_url("ingresos/ttributo/filteredgrid");
			 
		 
		$edit->nombre = new inputField("Nombre", "nombre");
		$edit->nombre->size=40;
		$edit->nombre->maxlength=80;
		$edit->nombre->rule='required';
		
		$edit->cancelado = new dropdownField("Cancelado","cancelado");		
		$edit->cancelado->option("","");
		$edit->cancelado->option("s","Si");
		$edit->cancelado->option("n","No");
		
		$edit->mensaje = new inputField("Mensaje", "mensaje");
		$edit->mensaje->size=20;
		
		$edit->momto = new inputField("Monto", "monto");
		$edit->momto->size=10;                        
		$edit->momto->clause="likerigth";            
		
		$edit->emireci = new dropdownField("Emitio Recivo","emireci");		
		$edit->emireci->option("","");
		$edit->emireci->option("s","Si");
		$edit->emireci->option("n","No");
		
		$edit->deudante = new dropdownField("Deuda Anterior","deudante");		
		$edit->deudante->option("","");
		$edit->deudante->option("s","Si");
		$edit->deudante->option("n","No");
		
		$edit->agua = new inputField("Agua", "agua");
		$edit->agua->size=10;                        
		$edit->agua->clause="likerigth";
		
		
		$edit->buttons("modify", "save", "undo", "delete", "back");
		 
		$edit->build();

		$data['content'] = $edit->output;
		$data['title']   = "Tributo o Impuesto";
		$data["head"]    = script("jquery.pack.js").$this->rapyd->get_head();
		$this->load->view('view_ventanas', $data);
		
	}

}
?>