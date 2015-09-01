<?php
class barras extends Controller {
	function barras(){
		parent::Controller(); 
		$this->load->library("rapyd");
	}

	function index(){
		$this->datasis->modulo_id(312,1);
		redirect("inventario/barras/filteredgrid");
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

		$filter = new DataFilter2("Filtro por Producto", 'sinv');
		$filter->codigo = new inputField("C&oacute;digo", "codigo");

		$filter->descrip = new inputField("Descripci&oacute;n", "descrip");
		$filter->descrip->db_name='CONCAT_WS(" ",descrip,descrip2)';

		$filter->clave = new inputField("Clave", "clave");

		$filter->proveed = new inputField("Beneficiario", "proveed");
		$filter->proveed->append($bSPRV);
		$filter->proveed->clause ="in";
		$filter->proveed->db_name='( prov1, prov2, prov3 )';

		$filter->marca = new dropdownField("Marca", "marca");
		$filter->marca->option("","Todas");
		$filter->marca->options("SELECT TRIM(marca) AS clave, TRIM(marca) AS valor FROM marc ORDER BY marca"); 

		$filter->buttons("reset","search");
		$filter->build();

		$link=anchor('/inventario/barras/dataedit/modify/<#id#>','<#codigo#>');
		$grid = new DataGrid("Lista de Art&iacute;culos");
		$grid->order_by("codigo","asc");
		$grid->per_page = 20;

		$grid->use_function('str_replace');
		$grid->column("C&oacute;digo",$link);
		$grid->column("Descripci&oacute;n","descrip");
		$grid->column("Precio 1","precio1");
		$grid->column("Barras","barras");

		$grid->build();

		$data['content'] = $filter->output.$grid->output;
		$data['title']   = " Lista de Art&iacute;culos ";
		$data["head"]    = $this->rapyd->get_head();
		$this->load->view('view_ventanas', $data);
	}

	function dataedit() {
		$this->rapyd->load('dataedit');

		$edit = new DataEdit("barras de Inventario", "sinv");
		$edit->back_url = site_url("inventario/barras/filteredgrid/search/osp");

		$edit->barras = new inputField("Barras", "barras");
		$edit->barras->size      =  15;
		$edit->barras->maxlength =  15;
		$edit->barras->rule      =  "trim";

		$edit->codigo = new inputField("C&oacute;digo", "codigo");
		$edit->codigo->size       =  15;
		$edit->codigo->maxlength  =  15;
		$edit->codigo->rule 			= "required|trim";
		$edit->codigo->mode				="autohide";
		$edit->codigo->mode				= "readonly";

		$edit->descrip = new inputField("Descripci&oacute;n", "descrip");
		$edit->descrip->size			=  40;
		$edit->descrip->maxlength =  45;
		$edit->descrip->mode			= "autohide";
		$edit->descrip->rule			=  "trim";
				
		$edit->descrip2 = new inputField("Descripci&oacute;n Corta", "descrip2");
		$edit->descrip2->size					=20;
		$edit->descrip2->maxlength		=45;
		$edit->descrip2->mode					= "autohide";	
		$edit->descrip2->rule					="trim";		
				
		$edit->precio1 = new inputField("Precio", "precio1");
		$edit->precio1->size			=	15;
		$edit->precio1->maxlength	=14;		
		$edit->precio1->rule			="trim";
		$edit->precio1->mode			= "autohide";
		

		$edit->buttons("modify", "save", "undo", "back");
		$edit->build();

		$data['content'] = $edit->output;
		$data['title']   = " C&oacute;digo Barras de Inventario ";
		$data["head"]    = $this->rapyd->get_head();
		$this->load->view('view_ventanas', $data);
	}
}
?>