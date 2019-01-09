<?php
class rv_clase extends Controller {
	var $titp='Clases de Vehiculos';
	var $tits='Clase de Vehiculo';
	var $url ='recaudacion/rv_clase/';
	function rv_clase(){
		parent::Controller();
		$this->load->library("rapyd");
		$this->datasis->modulo_id(403,1);
	}
	function index(){
		redirect($this->url."filteredgrid");
	}
	function filteredgrid(){
		$this->rapyd->load('datafilter','datagrid');

		$filter = new DataFilter($this->titp, 'rv_clase');

		$filter->id = new inputField('Ref','id');
		$filter->id->rule      ='max_length[11]';
		$filter->id->size      =13;
		$filter->id->maxlength =11;

		$filter->codigo = new inputField('Codigo','codigo');
		$filter->codigo->rule      ='max_length[10]';
		$filter->codigo->size      =12;
		$filter->codigo->maxlength =10;

		$filter->descrip = new inputField('Descripcion','descrip');
		$filter->descrip->rule      ='max_length[100]';
		$filter->descrip->size      =102;
		$filter->descrip->maxlength =100;

		$filter->monto = new inputField('Monto','monto');
		$filter->monto->rule      ='max_length[19]|numeric';
		$filter->monto->css_class ='inputnum';
		$filter->monto->size      =21;
		$filter->monto->maxlength =19;

		$filter->buttons('reset', 'search');
		$filter->build();

		$uri = anchor($this->url.'dataedit/show/<raencode><#id#></raencode>','<#id#>');

		$grid = new DataGrid('');
		$grid->order_by('id');
		$grid->per_page = 40;

		$grid->column_orderby('Ref.'        ,"$uri",'id','align="left"');
		$grid->column_orderby('Codigo'      ,"codigo",'codigo','align="left"');
		$grid->column_orderby('Descripcion' ,"descrip",'descrip','align="left"');
		$grid->column_orderby('Monto'       ,"<nformat><#monto#></nformat>",'monto','align="right"');

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

		$script='$(".inputnumc").numeric("0");';

		$edit = new DataEdit($this->tits, 'rv_clase');

		$edit->back_url = site_url($this->url."filteredgrid");
		
		$edit->script($script,"create");
		$edit->script($script,"modify");

		$edit->post_process('insert','_post_insert');
		$edit->post_process('update','_post_update');
		$edit->post_process('delete','_post_delete');

		$edit->id = new inputField('Ref','id');
		$edit->id->rule='max_length[11]';
		$edit->id->size =13;
		$edit->id->maxlength =11;
		$edit->id->mode='autohide';
		$edit->id->when=array('show','modify');

		$edit->codigo = new inputField('Codigo','codigo');
		$edit->codigo->rule='max_length[10]';
		$edit->codigo->size =12;
		$edit->codigo->maxlength =10;

		$edit->descrip = new inputField('Descripcion','descrip');
		$edit->descrip->rule='max_length[100]';
		$edit->descrip->size =40;
		$edit->descrip->maxlength =100;

		$edit->monto = new inputField('Monto','monto');
		$edit->monto->rule='max_length[19]|numeric';
		$edit->monto->css_class='inputnum';
		$edit->monto->size =10;
		$edit->monto->maxlength =19;

		$edit->buttons('add','modify', 'save', 'undo', 'delete', 'back');
		$edit->build();
		$data['content'] = $edit->output;
		$data["head"]    = $this->rapyd->get_head().script('jquery.js').script("plugins/jquery.numeric.pack.js");
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
		$mSQL="CREATE TABLE `rv_clase` (
		  `id` int(11) NOT NULL AUTO_INCREMENT,
		  `codigo` varchar(10) CHARACTER SET utf8 NOT NULL DEFAULT '',
		  `descrip` varchar(100) CHARACTER SET utf8 DEFAULT NULL,
		  `monto` decimal(19,2) DEFAULT '0.00',
		  PRIMARY KEY (`id`)
		) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=utf32";
		$this->db->simple_query($mSQL);
		
		$query="ALTER TABLE `rv_clase` 	ADD COLUMN `monto2` DECIMAL(19,2) NULL DEFAULT '0.00' AFTER `monto`";
		$this->db->simple_query($query);
	}
}
?>
