<?php
class clase extends Controller {
	var $titp='Clases de Vehiculos';
	var $tits='Clase de Vehiculo';
	var $url ='ingresos/clase/';
	function clase(){
		parent::Controller();
		$this->load->library("rapyd");
		$this->datasis->modulo_id(348,1);
	}
	function index(){
		redirect($this->url."filteredgrid");
	}
	function filteredgrid(){
		$this->rapyd->load('datafilter','datagrid');

		$filter = new DataFilter($this->titp, 'clase');

		$filter->codigo = new inputField('codigo','codigo');
		$filter->codigo->rule      ='max_length[1]';
		$filter->codigo->size      =3;
		$filter->codigo->maxlength =1;

		$filter->nombre = new inputField('nombre','nombre');
		$filter->nombre->rule      ='max_length[20]';
		$filter->nombre->size      =22;
		$filter->nombre->maxlength =20;

		$filter->monto = new inputField('monto','monto');
		$filter->monto->rule      ='max_length[8]';
		$filter->monto->size      =10;
		$filter->monto->maxlength =8;

		$filter->buttons('reset', 'search');
		$filter->build();

		$uri = anchor($this->url.'dataedit/show/<raencode><#codigo#></raencode>','<#codigo#>');

		$grid = new DataGrid('');
		$grid->order_by('codigo');
		$grid->per_page = 40;

		$grid->column_orderby('Codigo',"$uri",'codigo');
		$grid->column_orderby('Nombre',"nombre",'nombre');
		$grid->column_orderby('Monto',"<nformat><#monto#></nformat>",'monto');

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

		$edit = new DataEdit($this->tits, 'clase');

		$edit->back_url = site_url($this->url."filteredgrid");

		$edit->post_process('insert','_post_insert');
		$edit->post_process('update','_post_update');
		$edit->post_process('delete','_post_delete');

		$edit->codigo = new inputField('C&oacute;digo','codigo');
		$edit->codigo->rule='trim|required';
		$edit->codigo->size =5;
		$edit->codigo->maxlength =10;
		$edit->codigo->mode='autohide';
		//$edit->codigo->when=array('show','modify');

		$edit->nombre = new inputField('Nombre','nombre');
		$edit->nombre->rule='trim|required';
		$edit->nombre->size =40;
		$edit->nombre->maxlength =100;

		$edit->monto = new inputField('Monto','monto');
		$edit->monto->rule='max_length[8]';
		$edit->monto->size =10;
		$edit->monto->maxlength =8;

		$edit->buttons('add','modify', 'save', 'undo', 'delete', 'back');
		$edit->build();
		$data['content'] = $edit->output;
		$data['head']    = $this->rapyd->get_head();
		$data['title']   = $this->tits;
		$this->load->view('view_ventanas', $data);

	}

	function montotri(){
		$codigo     =$this->input->post('codigo');
		$ano        =$this->input->post('a');
		$utributaria=$this->datasis->dameval("SELECT valor FROM utribu WHERE ano='$ano' LIMIT 1");
		$monto      =$this->datasis->dameval("SELECT monto FROM clase WHERE codigo='$codigo'");
		echo round(($utributaria*$monto)/4,2);
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
		$mSQL="CREATE TABLE `clase` (
		`codigo` char(1) NOT NULL DEFAULT '',
		`nombre` char(20) DEFAULT NULL,
		`monto` double DEFAULT NULL,
		PRIMARY KEY (`codigo`)
	  ) ENGINE=MyISAM DEFAULT CHARSET=utf8";
		$this->db->simple_query($mSQL);
		
		$query="ALTER TABLE `clase` CHANGE COLUMN `codigo` `codigo` VARCHAR(10) NOT NULL DEFAULT '' FIRST";
		$this->db->simple_query($query);
		$query="ALTER TABLE `clase`  CHANGE COLUMN `nombre` `nombre` VARCHAR(100) NULL DEFAULT NULL AFTER `codigo`";
		$this->db->simple_query($query);
		$query="ALTER TABLE `clase` CHANGE COLUMN `monto` `monto` DECIMAL(19,2) NULL DEFAULT '0' AFTER `nombre";
		$this->db->simple_query($query);
	}

}
?>
