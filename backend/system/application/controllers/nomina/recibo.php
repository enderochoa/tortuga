<?php
class Recibo extends Controller {

var $titp  = 'Recibos de Nomina';
var $tits  = 'Recibo de Nomina';
var $url   = 'nomina/recibo/';

function recibo(){
	parent::Controller();
	$this->load->library("rapyd");
	$this->datasis->modulo_id(174,1);
}

	function index(){
		redirect($this->url."filteredgrid");
	}
	
	function filteredgrid(){		
		$this->rapyd->load("datafilter2","datagrid");
		$this->rapyd->uri->keep_persistence();
		
		$script ='
		$(function() {
			$(".inputnum").numeric(".");
		});
		';
				
		$filter = new DataFilter2("","pers");
		$filter->script($script, "create");
		$filter->script($script, "modify");		
		
		$filter->codigo = new inputField("C&oacute;digo", "codigo");
		$filter->codigo->size=10;
		$filter->codigo->css_class='inputnum';
		
		$filter->cedula = new inputField("C&eacute;dula", "cedula");
		$filter->cedula->size=10;
		$filter->cedula->css_class='inputnum';
		
		$filter->nombre = new inputField("Nombre", "nombre");
		$filter->nombre->size=35;
		
		$filter->apellido = new inputField("Apellido", "apellido");
		$filter->apellido->size=35;
		
		$filter->contrato = new dropdownField("Contrato", "contrato");
		$filter->contrato->option("","");
		$filter->contrato->options('SELECT codigo, CONCAT_WS(" * ",codigo,nombre) a FROM noco ORDER BY nombre');
		$filter->contrato->style = "width:300px;";
		
		$filter->buttons("reset","search");
		$filter->build();
		
		$uri = anchor('forma/ver/RECIBO/2/<#codigo#>','Imprimir');

		$grid = new DataGrid("");
		$grid->order_by("codigo","asc");
		$grid->per_page = 20;

		$grid->column_orderby("C&oacute;digo"   ,"codigo"   ,"codigo"  ,"align='center'"    );
		$grid->column_orderby("C&eacute;dula"   ,"cedula"   ,"cedula"  ,"align='center'"    );
		$grid->column_orderby("Nombre"          ,"nombre"   ,"nombre"  ,"align='left'NOWRAP");
		$grid->column_orderby("Apellidos"       ,"apellido" ,"apellido","align='left'NOWRAP");
		$grid->column_orderby("Contrato"        ,"contrato" ,"contrato");
		$grid->column_orderby("Recibo"          ,$uri       ,"codigo"  );
		
		$grid->build();
        		
		//$data['content'] = $filter->output.$grid->output;
		$data['filtro']  = $filter->output;
		$data['content'] = $grid->output;
		$data['script'] = script("jquery.js")."\n";
		$data['title']   = "Recibos de Pago";
		$data["head"]    = $this->rapyd->get_head();
		$this->load->view('view_ventanas', $data);
	}
}