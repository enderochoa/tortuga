<?php
class uadministra extends Controller {
	
	var $titp  = 'Unidades Administrativas';
	var $tits  = 'Unidad Administrativa';
	var $url   = 'presupuesto/uadministra/';
	
	function uadministra(){
		parent::Controller();
		$this->load->library("rapyd");
	}
	
	function  index(){
		redirect("presupuesto/uadministra/filteredgrid");
	}
	
	function filteredgrid(){
		$this->datasis->modulo_id(83,1);
		
		$this->rapyd->load("datafilter2","datagrid");//
		
		$filter = new DataFilter2(""); //"Filtro por ".$this->titp,"uadministra");
		$filter->db->select(array("a.codigoejec codigoejec","a.codigo codigo","a.nombre nombre","a.director director","b.nombre ejecutor"));
		$filter->db->from("uadministra a");  
		$filter->db->join("uejecutora b" ,"a.codigoejec=b.codigo");
		
		$filter->codigoejec = new dropdownField("Unidad Ejecutora","codigoejec");		
		$filter->codigoejec->rule='required';
		$filter->codigoejec->option("","");
		$filter->codigoejec->options("SELECT codigo, CONCAT_WS(' ',codigo,nombre)as a FROM uejecutora ORDER BY nombre");
		
		$filter->codigo = new inputField("C&oacute;digo", "codigo");
		$filter->codigo->size=5;
		$filter->codigo->clause="likerigth";
		
		$filter->nombre = new inputField("Nombre", "nombre");
		$filter->nombre->size=40;
		$filter->nombre->clause="likerigth";
		
		$filter->director = new inputField("Director", "a.director");
		$filter->director->size=40;
		$filter->director->clause="likerigth";		
		
		$filter->buttons("reset","search");
		
		$filter->build();
		
		$uri = anchor('presupuesto/uadministra/dataedit/show/<raencode><#codigoejec#></raencode>/<raencode><#codigo#></raencode>','<#codigo#>');
		
		$grid = new DataGrid("");
		
		$grid->order_by("codigo","asc");
				
		
		$grid->column_orderby("Unidades Administrativas" ,$uri         ,"codigo"      ,"align='left'");
		$grid->column_orderby("Unidades Ejecutoras"      ,"ejecutor"   ,"ejecutor"  ,"align='left'");
		$grid->column_orderby("Nombre"                   ,"nombre"     ,"nombre"      ,"align='left'");
		$grid->column_orderby("Director"                 ,"director"   ,"director"    ,"align='left'");
		
		$grid->add("presupuesto/uadministra/dataedit/create");
		
		$grid->build();
		
		$data['filtro']  = $filter->output;
		$data['content'] = $grid->output;
		$data['script'] = script("jquery.js")."\n";
		$data['title']   = "$this->titp";//" $this->titp ";
		$data["head"]    = $this->rapyd->get_head();
		$this->load->view('view_ventanas', $data);
	}
	
	function dataedit(){
		$this->datasis->modulo_id(83,1);
		
		$this->rapyd->load("dataedit");
		
		$link2=site_url($this->url.'/sugerir');
		$script='
			$(".inputnum").numeric(".");
			
			function sugerir(){
				$.ajax({
						url: "'.$link2.'",
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
		';
		
		$edit = new DataEdit($this->tits, "uadministra");
		$edit->script($script,"create");
		
		$edit->back_url = site_url("presupuesto/uadministra/filteredgrid");
		
		
		$edit->codigoejec = new dropdownField("Unidad Ejecutora","codigoejec");		
		$edit->codigoejec->rule='required';
		$edit->codigoejec->options("SELECT codigo, CONCAT_WS(' ',codigo,nombre)as a FROM uejecutora ORDER BY nombre");
			 
		$sugerir='<a href="javascript:sugerir();" title="Sugerir un Codigo aleatorio">Sugerir C&oacute;digo </a>';
		$edit->codigo = new inputField("C&oacute;digo", "codigo");
		$edit->codigo->size=5;
		$edit->codigo->maxlength=4;
		$edit->codigo->mode="autohide";
		$edit->codigo->css_class='inputnum';
		$edit->codigo->rule='required';
		$edit->codigo->append($sugerir);
		 
		$edit->nombre = new inputField("Nombre", "nombre");
		$edit->nombre->size=40;
		$edit->nombre->maxlength=80;
		$edit->nombre->rule='required';
		
		$edit->director = new inputField("Director", "director");
		$edit->director->size=60;
		$edit->director->maxlength=100;
		
		$edit->funciones =new textareaField("Funciones","funciones");
		$edit->funciones->rows=8;
		$edit->funciones->cols=60;
		
		$edit->buttons("modify", "save", "undo", "delete", "back");
		 
		$edit->build();

		$data['content'] = $edit->output;
		$data['title']   = "$this->tits";
		$data["head"]    = script("jquery.js").$this->rapyd->get_head();
		$this->load->view('view_ventanas', $data);
		
	}
	
	function sugerir(){
		$ultimo=$this->datasis->dameval("SELECT LPAD(hexa,4,0) FROM serie LEFT JOIN uadministra ON LPAD(codigo,4,0)=LPAD(hexa,4,0) WHERE valor<65535 AND codigo IS NULL LIMIT 1");
		echo $ultimo;
	}

}
?>