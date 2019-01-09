<?php
class tipoin extends Controller {
	var $titp='Tipos de Inmuebles';
	var $tits='Tipo de Inmueble';
	var $url ='ingresos/tipoin/';
	function tipoin(){
		parent::Controller();
		$this->load->library("rapyd");
		$this->datasis->modulo_id(344,1);
	}
	function index(){
		redirect($this->url."filteredgrid");
	}
	function filteredgrid(){
		$this->rapyd->load('datafilter','datagrid');

		$filter = new DataFilter($this->titp, 'tipoin');

		$filter->tipoin = new inputField('Tipo','tipoin');
		$filter->tipoin->rule      ='max_length[30]';
		$filter->tipoin->size      =32;
		$filter->tipoin->maxlength =30;

		$filter->buttons('reset', 'search');
		$filter->build();

		$uri = anchor($this->url.'dataedit/show/<raencode><#tipoin#></raencode>','<#tipoin#>');

		$grid = new DataGrid('');
		$grid->order_by('tipoin');
		$grid->per_page = 40;

		$grid->column_orderby('Tipo',"$uri",'tipoin');

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

		$edit = new DataEdit($this->tits, 'tipoin');

		$edit->back_url = site_url($this->url."filteredgrid");

		$edit->post_process('insert','_post_insert');
		$edit->post_process('update','_post_update');
		$edit->post_process('delete','_post_delete');

		$edit->tipoin = new inputField('Tipo','tipoin');
		$edit->tipoin->rule='trim|required|unique';
		$edit->tipoin->size =32;
		$edit->tipoin->maxlength =30;
		//$edit->tipoin->mode='autohide';
		//$edit->tipoin->when=array('show','modify');

		$edit->buttons('add','modify', 'save', 'undo', 'delete', 'back');
		$edit->build();
		$data['content'] = $edit->output;
		$data['head']    = $this->rapyd->get_head();
		$data['title']   = $this->tits;
		$this->load->view('view_ventanas', $data);

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
		$mSQL="CREATE TABLE `tipoin` (
		`tipoin` char(30) NOT NULL DEFAULT '',
		PRIMARY KEY (`tipoin`)
	  ) ENGINE=MyISAM DEFAULT CHARSET=utf8";
		$this->db->simple_query($mSQL);
	}

}
?>
