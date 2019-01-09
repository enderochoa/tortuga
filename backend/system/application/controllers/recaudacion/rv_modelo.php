<?php
class rv_modelo extends Controller {
	var $titp='Modelos de Vehiculos';
	var $tits='Modelo de Vehiculo';
	var $url ='recaudacion/rv_modelo/';
	function rv_modelo(){
		parent::Controller();
		$this->load->library("rapyd");
		$this->datasis->modulo_id(405,1);
	}
	function index(){
		redirect($this->url."filteredgrid");
	}
	function filteredgrid(){
		$this->rapyd->load('datafilter','datagrid');

		$filter = new DataFilter($this->titp, 'rv_modelo');
		$filter->db->select(array('rv_modelo.id','rv_modelo.descrip','rv_marca.descrip marca'));
		$filter->db->join("rv_marca","rv_modelo.id_marca=rv_marca.id");

		$filter->id = new inputField('id','id');
		$filter->id->rule      ='max_length[11]';
		$filter->id->size      =13;
		$filter->id->maxlength =11;

		$filter->id_marca = new dropDownField('id_marca','id_marca');
		$filter->id_marca->option("","");
		$filter->id_marca->options("SELECT id,descrip FROM rv_marca ORDER BY descrip");
		

		$filter->descrip = new inputField('descrip','descrip');
		$filter->descrip->rule      ='max_length[100]';
		$filter->descrip->size      =40;
		$filter->descrip->maxlength =100;

		$filter->buttons('reset', 'search');
		$filter->build();

		$uri = anchor($this->url.'dataedit/show/<raencode><#id#></raencode>','<#id#>');

		$grid = new DataGrid('');
		$grid->order_by('rv_modelo.id');
		$grid->per_page = 40;

		$grid->column_orderby('Ref.'        ,"$uri",'id','align="left"');
		$grid->column_orderby('Marca'       ,"marca"                          ,'id_marca','align="left"');
		$grid->column_orderby('Descripcion' ,"descrip",'descrip','align="left"');

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

		$edit = new DataEdit($this->tits, 'rv_modelo');

		$edit->back_url = site_url($this->url."filteredgrid");

		$edit->post_process('insert','_post_insert');
		$edit->post_process('update','_post_update');
		$edit->post_process('delete','_post_delete');

		$edit->id = new inputField('Ref','id');
		$edit->id->rule='max_length[11]';
		$edit->id->size =13;
		$edit->id->maxlength =11;
		$edit->id->mode='autohide';
		$edit->id->when=array('show','modify');

		$edit->id_marca = new dropDownField('Marca','id_marca');
		$edit->id_marca->options("SELECT id,descrip FROM rv_marca ORDER BY descrip");

		$edit->descrip = new inputField('Descripcion','descrip');
		$edit->descrip->rule='max_length[100]';
		$edit->descrip->size =40;
		$edit->descrip->maxlength =100;

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
		$mSQL="CREATE TABLE `rv_modelo` (
		  `id` int(11) NOT NULL AUTO_INCREMENT,
		  `id_marca` int(11) NOT NULL,
		  `descrip` varchar(100) DEFAULT NULL,
		  PRIMARY KEY (`id`)
		) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf32";
		$this->db->simple_query($mSQL);
	}

}
?>
