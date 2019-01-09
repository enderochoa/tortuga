<?php
class tingresos extends Controller {
	var $titp='Conceptos de Recaudacion';
	var $tits='Concepto de Recaudacion';
	var $url ='ingresos/tingresos/';
	function tingresos(){
		parent::Controller();
		$this->load->library("rapyd");
		//$this->datasis->modulo_id(216,1);
	}
	function index(){
		redirect($this->url."filteredgrid");
	}
	function filteredgrid(){
		$this->rapyd->load('datafilter','datagrid');

		$filter = new DataFilter($this->titp, 'tingresos');

		$filter->codigo = new inputField('Codigo','codigo');
		$filter->codigo->rule      ='max_length[2]';
		$filter->codigo->size      =4;
		$filter->codigo->maxlength =2;

		$filter->descrip = new inputField('Descripci&oacute;n','descrip');
		$filter->descrip->rule      ='max_length[100]';
		$filter->descrip->size      =102;
		$filter->descrip->maxlength =100;

		$filter->grupo = new inputField('Grupo','grupo');
		$filter->grupo->rule      ='max_length[1]';
		$filter->grupo->size      =3;
		$filter->grupo->maxlength =1;

		$filter->buttons('reset', 'search');
		$filter->build();

		$uri = anchor($this->url.'dataedit/show/<raencode><#codigo#></raencode>','<#codigo#>');

		$grid = new DataGrid('');
		$grid->order_by('codigo');
		$grid->per_page = 40;

		$grid->column_orderby('C&oacute;digo'      ,"$uri",'codigo','align="left"');
		$grid->column_orderby('Descripci&oacute;n' ,"descrip",'descrip','align="left"');
		$grid->column_orderby('Grupo'              ,"grupo",'grupo','align="left"');

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

		$edit = new DataEdit($this->tits, 'tingresos');

		$edit->back_url = site_url($this->url."filteredgrid");

		$edit->post_process('insert','_post_insert');
		$edit->post_process('update','_post_update');
		$edit->post_process('delete','_post_delete');

		$edit->codigo = new inputField('C&oacute;digo','codigo');
		$edit->codigo->rule='trim|max_length[2]|required|unique';
		$edit->codigo->size =4;
		$edit->codigo->maxlength =2;

		$edit->descrip = new inputField('Descripci&oacute;n','descrip');
		$edit->descrip->rule='max_length[100]';
		$edit->descrip->size =102;
		$edit->descrip->maxlength =100;

		$edit->grupo = new inputField('Grupo','grupo');
		$edit->grupo->rule='max_length[1]';
		$edit->grupo->size =3;
		$edit->grupo->maxlength =1;
		
		$edit->activo = new dropdownField('Activo','activo');
		$edit->activo->option("S","SI");
		$edit->activo->option("N","NO");
		
		$edit->codigopres = new inputField('Codigo Presupuestario','codigopres');
		$edit->codigopres->rule='max_length[100]';
		$edit->codigopres->size =40;
		$edit->codigopres->maxlength =100;
		

		$edit->buttons('add','modify', 'save', 'undo', 'delete', 'back');
		$edit->build();
		$data['content'] = $edit->output;
		$data['head']    = $this->rapyd->get_head();
		$data['title']   = $this->tits;
		$this->load->view('view_ventanas', $data);

	}
	
	function grupo(){
		$conc=$this->input->post('conc');
		$conce=$this->db->escape($conc);
		$grupo=$this->datasis->dameval("SELECT grupo FROM tingresos WHERE codigo=$conce");
		echo $grupo;
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
		$mSQL="CREATE TABLE `tingresos` (
			`codigo` CHAR(2) NOT NULL,
			`descrip` VARCHAR(100) NULL DEFAULT NULL,
			`grupo` CHAR(1) NULL DEFAULT NULL,
			`descripcion` TEXT NULL,
			`titu1` TEXT NULL,
			`titu2` TEXT NULL,
			`codigopres` TEXT NULL,
			`contador` TEXT NULL,
			`prefijo` TEXT NULL,
			`monto` DECIMAL(19,2) NULL DEFAULT '0.00',
			`activo` CHAR(1) NULL DEFAULT 'S',
			PRIMARY KEY (`codigo`)
		)
		COLLATE='utf8_general_ci'
		ENGINE=MyISAM
		AUTO_INCREMENT=1";
		$this->db->simple_query($mSQL);
		
		$query="ALTER TABLE `tingresos` ADD COLUMN `formato` VARCHAR(20) NULL AFTER `activo`";
		$this->db->simple_query($query);
	}

}
?>
