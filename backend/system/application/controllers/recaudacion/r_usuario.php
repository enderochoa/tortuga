<?php
class R_usuario extends Controller {
	var $titp='Asignar Cajeros';
	var $tits='Asignar Cajero';
	var $url ='recaudacion/r_usuario/';
	function R_usuario(){
		parent::Controller();
		$this->load->library("rapyd");
		$this->datasis->modulo_id(427,1);
	}
	function index(){
		redirect($this->url."filteredgrid");
	}
	function filteredgrid(){
		$this->rapyd->load('datafilter','datagrid');

		$filter = new DataFilter($this->titp, 'usuario');
		$filter->db->join("r_caja","usuario.caja=r_caja.id","LEFT");
		$filter->db->select(array("usuario.us_codigo","usuario.us_nombre","usuario.caja","r_caja.nombre cajap"));

		$filter->us_codigo = new inputField('Codigo','us_codigo');
		$filter->us_codigo->rule      ='trim';
		$filter->us_codigo->size      =14;
		$filter->us_codigo->maxlength =12;

		$filter->us_nombre = new inputField('Nombre','us_nombre');
		$filter->us_nombre->rule      ='trim';
		$filter->us_nombre->size      =32;
		$filter->us_nombre->maxlength =30;

		$filter->caja = new dropdownField("Caja","cajas");
		$filter->caja->option("","");
		$filter->caja->options("SELECT id,nombre FROM r_caja ");
		$filter->caja->rule     ='trim';
		$filter->caja->size      =13;
		$filter->caja->maxlength =11;
		$filter->caja->db_name   ='caja';

		$filter->buttons('reset', 'search');
		$filter->build();

		$uri = anchor($this->url.'dataedit/show/<raencode><#us_codigo#></raencode>','<#us_codigo#>');

		$grid = new DataGrid('');
		$grid->order_by('us_codigo');
		$grid->per_page = 40;

		$grid->column_orderby('Codigo'            ,"$uri"                                             ,'us_codigo'    ,'align="left"');
		$grid->column_orderby('Nombre'            ,"us_nombre"                                        ,'us_nombre'    ,'align="left"');
		$grid->column_orderby('Caja'              ,"cajap"                                            ,'cajap'        ,'align="right"');

		$grid->build();

		$data['filtro']  = $filter->output;
		$data['content'] = $grid->output;
		$data['head']    = $this->rapyd->get_head().script('jquery.js');
		$data['title']   = $this->titp;
		$this->load->view('view_ventanas', $data);

	}
	function dataedit(){
		$this->rapyd->load('dataedit');

		$edit = new DataEdit($this->tits, 'usuario');

		$edit->back_url = site_url($this->url."filteredgrid");

		$edit->post_process('insert','_post_insert');
		$edit->post_process('update','_post_update');
		$edit->post_process('delete','_post_delete');

		$edit->us_codigo = new inputField('Codigo'   ,'us_codigo');
		$edit->us_codigo->rule     ='trim';
		$edit->us_codigo->size      =14;
		$edit->us_codigo->maxlength =12;
		$edit->us_codigo->mode      ='autohide';

		$edit->us_nombre = new inputField('Nombre'   ,'us_nombre');
		$edit->us_nombre->rule     ='trim';
		$edit->us_nombre->size      =32;
		$edit->us_nombre->maxlength =30;
		$edit->us_nombre->mode      ='autohide';

		$edit->caja = new dropdownField("Caja","cajas");
		$edit->caja->option("","");
		$edit->caja->options("SELECT id,nombre FROM r_caja ");
		$edit->caja->rule     ='trim';
		$edit->caja->size      =13;
		$edit->caja->maxlength =11;
		$edit->caja->db_name   ='caja';

		$edit->buttons('modify', 'save', 'undo', 'back');
		$edit->build();
		$data['content'] = $edit->output;
		$data['head']    = $this->rapyd->get_head().script('jquery.js');
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
		$mSQL="ALTER TABLE `usuario` ADD COLUMN `caja` INT NULL DEFAULT NULL";
		$this->db->simple_query($mSQL);
	}
}
?>
