<?php
class consulta extends Controller {

	var $url ='suministros/consulta/';
	var $titp='Busqueda de Articulos';
	var $tits='Busqueda de Articulos';

	function consulta(){
		parent::Controller();
		$this->load->library("rapyd");
		//$this->datasis->modulo_id(88,1);
	}
	
	function index(){
		redirect($this->url."/filteredgrid");
	}

	function filteredgrid(){
		$this->rapyd->load("datafilter2","datagrid");

		$script='';

		$filter = new DataFilter2("");
		$filter->db->from("view_sumi_saldo");

		$filter->script($script);

		$filter->codigo = new inputField("C&oacute;digo", "codigo");
		$filter->codigo->size=15;

		$filter->descrip = new inputField("Descripci&oacute;n", "descrip");
		$filter->descrip->size=40;
		
		$filter->unidad = new inputField("Unidad", "unidad");
		$filter->unidad->size=40;

		$filter->buttons("reset","search");
		$filter->build();
		
		$uri = anchor($this->url.'redi/<raencode><#codigo#></raencode>','<#codigo#>');

		$grid = new DataGrid("");
		$grid->order_by("codigo","asc");
		$grid->per_page = 100;
		$grid->use_function('substr','str_pad','sta');

		$grid->column_orderby("C&oacute;digo"         ,$uri                                          ,"codigo");
		$grid->column_orderby("Descripci&oacute;n"    ,"descrip"                                     ,"descrip"    ,"align='left'        ");
		$grid->column_orderby("Unidad"                ,"unidad"                                      ,"unidad"     ,"align='left'      ");
		$grid->column_orderby("Cantidad"              ,"cantidad"                                    ,"cantidad"   ,"align='left'  NOWRAP");

		//$grid->add($this->url."/dataedit/create");
		$grid->build();

		//$data['content'] = $filter->output.$grid->output;
		$data['filtro']  = $filter->output;
		$data['content'] = $grid->output;
		$data['title']   = $this->tits;
		$data['script']  = script("jquery.js")."\n";
		$data["head"]    = script("jquery.js").$this->rapyd->get_head();
		$this->load->view('view_ventanas', $data);
	}
	
	function redi($codigo=''){
		$codigoe = $this->db->escape(radecode($codigo)); 
		$id      = $this->datasis->dameval("SELECT id FROM COSTOS WHERE codigo=$codigoe ORDER BY id desc LIMIT 1");
		redirect($this->url.'dataedit/show/'.$id);
	}
	
	function dataedit(){
		$this->rapyd->load("dataobject","dataedit");

		$edit = new DataEdit($this->tits,"COSTOS");
		$edit->back_url = site_url($this->url."/filteredgrid");
		
		$edit->id = new inputField('id','id');
		$edit->id->mode='autohide';
		$edit->id->size =4;
		$edit->id->rule ="trim|strtoupper|required";
		$edit->id->maxlength=2;
		$edit->id->when=array("");

		$edit->fecha = new dateField('Fecha','fecha');
		$edit->fecha->rule='chfecha';
		$edit->fecha->size =10;
		$edit->fecha->maxlength =8;

		$edit->codigo = new inputField('C&oacute;digo','codigo');
		$edit->codigo->rule='max_length[15]';
		$edit->codigo->size =17;
		$edit->codigo->maxlength =15;

		$edit->cantidad = new inputField('Ultima opaeraci&oacute;n','cantidad');
		$edit->cantidad->rule='max_length[20]|numeric';
		$edit->cantidad->css_class='inputnum';
		$edit->cantidad->size =22;
		$edit->cantidad->maxlength =20;

		$edit->acumulado = new inputField('Existencia','acumulado');
		$edit->acumulado->rule='max_length[38]|numeric';
		$edit->acumulado->css_class='inputnum';
		$edit->acumulado->size =40;
		$edit->acumulado->maxlength =38;

		$edit->promedio = new inputField('Precio','promedio');
		$edit->promedio->rule='max_length[38]|numeric';
		$edit->promedio->css_class='inputnum';
		$edit->promedio->size =40;
		$edit->promedio->maxlength =38;

		//$edit->cant_anteri = new inputField('cant_anteri','cant_anteri');
		//$edit->cant_anteri->rule='max_length[38]|numeric';
		//$edit->cant_anteri->css_class='inputnum';
		//$edit->cant_anteri->size =40;
		//$edit->cant_anteri->maxlength =38;
		
		$edit->buttons("back");
		$edit->build();
 
		$data['content'] = $edit->output;           
		$data['title']   = $this->tits;        
		$data["head"]    = $this->rapyd->get_head();
		$this->load->view('view_ventanas', $data);  
	}
}
?>
