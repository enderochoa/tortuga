<?php
class claseo extends Controller {
	var $titp='Clases de Inmuebles';
	var $tits='Clase de Inmueble';
	var $url ='ingresos/claseo/';
	function claseo(){
		parent::Controller();
		$this->load->library("rapyd");
		$this->datasis->modulo_id(345,1);
	}
	function index(){
		redirect($this->url."filteredgrid");
	}
	function filteredgrid(){
		$this->rapyd->load('datafilter','datagrid');

		$filter = new DataFilter($this->titp, 'claseo');

		$filter->codigo = new inputField('codigo','codigo');
		$filter->codigo->rule      ='max_length[1]';
		$filter->codigo->size      =3;
		$filter->codigo->maxlength =1;

		$filter->nombre = new inputField('nombre','nombre');
		$filter->nombre->rule      ='max_length[20]';
		$filter->nombre->size      =22;
		$filter->nombre->maxlength =20;

		$filter->buttons('reset', 'search');
		$filter->build();

		$uri = anchor($this->url.'dataedit/show/<raencode><#codigo#></raencode>','<#codigo#>');

		$grid = new DataGrid('');
		$grid->order_by('codigo');
		$grid->per_page = 40;

		$grid->column_orderby('codigo',"$uri",'codigo','align="left"');
		$grid->column_orderby('nombre',"nombre",'nombre','align="left"');

		$grid->add($this->url.'dataedit/create');
		$grid->build();

		$data['filtro']  = $filter->output;
		$data['content'] = $grid->output;
		$data['head']    = $this->rapyd->get_head().script('jquery.js');
		$data['title']   = $this->titp;
		$this->load->view('view_ventanas', $data);

	}
	function dataedit(){
		$this->rapyd->load('dataedit');

		$edit = new DataEdit($this->tits, 'claseo');

		$edit->back_url = site_url($this->url."filteredgrid");

		$edit->post_process('insert','_post_insert');
		$edit->post_process('update','_post_update');
		$edit->post_process('delete','_post_delete');

		$edit->codigo = new inputField('codigo','codigo');
		$edit->codigo->rule='max_length[1]';
		$edit->codigo->size =3;
		$edit->codigo->maxlength =1;

		$edit->nombre = new inputField('nombre','nombre');
		$edit->nombre->rule='max_length[20]';
		$edit->nombre->size =22;
		$edit->nombre->maxlength =20;

		$edit->buttons('add','modify', 'save', 'undo', 'delete', 'back');
		$edit->build();
		$data['content'] = $edit->output;
		$data['head']    = $this->rapyd->get_head();
		$data['title']   = $this->tits;
		$this->load->view('view_ventanas', $data);

	}

	function montodecla(){
		$codigo     =$this->input->post('codigo');
		$islr       =$this->input->post('decla');
		$ano        =$this->datasis->traevalor('EJERCICIO');
		$utributaria=$this->datasis->dameval("SELECT valor FROM utribu WHERE ano='$ano' LIMIT 1");
		$monto      =$this->datasis->dameval("SELECT utribu FROM claseo WHERE codigo='$codigo'");
		echo round($islr*$monto,2);
	}
	
	function montoaseo(){
		$codigo     =$this->input->post('codigo');
		$ano        =$this->input->post('a');
		$utributaria=$this->datasis->dameval("SELECT valor FROM utribu WHERE ano='$ano' LIMIT 1");
		$monto      =$this->datasis->dameval("SELECT utribu FROM claseo WHERE codigo='$codigo'");
		echo round($utributaria*$monto,2);
	}

	function _post_insert($do){
		$primary =implode(',',$do->pk);
		logusu($do->table,"Creo $this->tits $primary ");
	}
	function _post_update($do){
		$primary =implode(',',$do->pk);
		logusu($do->table,"Modifico $this->tits $primary ");
	}
	function _post_delete($do){
		$primary =implode(',',$do->pk);
		logusu($do->table,"Elimino $this->tits $primary ");
	}

	function instalar(){
		$mSQL="CREATE TABLE `claseo` (
		`codigo` char(1) NOT NULL DEFAULT '',
		`nombre` char(20) DEFAULT NULL,
		PRIMARY KEY (`codigo`)
		) ENGINE=MyISAM DEFAULT CHARSET=utf8";
		$this->db->simple_query($mSQL);
	}

}
?>
