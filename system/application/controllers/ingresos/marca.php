<?php
class marca extends Controller {
	var $titp='Marcas de Vehiculos';
	var $tits='Marca de Vehiculo';
	var $url ='ingresos/marca/';
	function marca(){
		parent::Controller();
		$this->load->library("rapyd");
		$this->datasis->modulo_id(321,1);
	}
	function index(){
		redirect($this->url."filteredgrid");
	}
	function filteredgrid(){
		$this->rapyd->load('datafilter','datagrid');

		$filter = new DataFilter($this->titp, 'marca');

		$filter->marca = new inputField('Marca','marca');
		$filter->marca->rule      ='max_length[30]';
		$filter->marca->size      =32;
		$filter->marca->maxlength =30;

		$filter->buttons('reset', 'search');
		$filter->build();

		$uri = anchor($this->url.'dataedit/show/<raencode><#marca#></raencode>','<#marca#>');

		$grid = new DataGrid('');
		$grid->order_by('marca');
		$grid->per_page = 40;

		$grid->column_orderby('Marca',"$uri",'marca');

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

		$edit = new DataEdit($this->tits, 'marca');

		$edit->back_url = site_url($this->url."filteredgrid");

		$edit->post_process('insert','_post_insert');
		$edit->post_process('update','_post_update');
		$edit->post_process('delete','_post_delete');

		$edit->marca = new inputField('Marca','marca');
		$edit->marca->rule='max_length[30]';
		$edit->marca->size =32;
		$edit->marca->maxlength =30;
		$edit->marca->mode='autohide';
		$edit->marca->when=array('show','modify','create');

		$edit->buttons('add','modify', 'save', 'undo', 'delete', 'back');
		$edit->build();
		$data['content'] = $edit->output;
		$data['head']    = $this->rapyd->get_head();
		$data['title']   = $this->tits;
		$this->load->view('view_ventanas', $data);

	}
	
	function autocomplete(){
		
		$arreglo= $this->datasis->consularray('SELECT marca id,marca FROM marca  ORDER BY marca');
		foreach($arreglo as $key=>$value)
			foreach($value as $key2=>$value2) 
			$arreglo[$key][$key2] = ($value2);
			
		$salida=json_encode($arreglo);
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
		$mSQL="CREATE TABLE `marca` (
		`marca` char(30) NOT NULL DEFAULT '',
		PRIMARY KEY (`marca`)
		  ) ENGINE=MyISAM DEFAULT CHARSET=utf8";
		$this->db->simple_query($mSQL);
	}

}
?>
