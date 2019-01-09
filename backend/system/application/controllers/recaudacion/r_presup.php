<?php
class r_presup extends Controller {
	var $titp='Presupuesto de Ingresos';
	var $tits='Partida';
	var $url ='recaudacion/r_presup/';
	function r_presup(){
		parent::Controller();
		$this->load->library("rapyd");
		$this->datasis->modulo_id(396,1);
	}
	function index(){
		redirect($this->url."filteredgrid");
	}
	function filteredgrid(){
		$this->rapyd->load('datafilter','datagrid');

		$filter = new DataFilter($this->titp, 'r_presup');

		$filter->id = new inputField('Ref.','id');
		$filter->id->rule      ='max_length[11]';
		$filter->id->size      =13;
		$filter->id->maxlength =11;

		$filter->partida = new inputField('Partida','partida');
		$filter->partida->rule      ='max_length[13]';
		$filter->partida->size      =15;
		$filter->partida->maxlength =13;

		$filter->denomi = new inputField('Denominacion','denomi');
		$filter->denomi->rule      ='max_length[80]';
		$filter->denomi->size      =82;
		$filter->denomi->maxlength =80;

		$filter->estimado = new inputField('Estimado','estimado');
		$filter->estimado->rule      ='max_length[19]|numeric';
		$filter->estimado->css_class ='inputnum';
		$filter->estimado->size      =21;
		$filter->estimado->maxlength =19;

		$filter->buttons('reset', 'search');
		$filter->build();

		$uri = anchor($this->url.'dataedit/show/<raencode><#id#></raencode>','<#id#>');

		$grid = new DataGrid('');
		$grid->order_by('id');
		$grid->per_page = 40;

		$grid->column_orderby('Ref.'        ,"$uri"                           ,'id'      ,'align="left"');
		$grid->column_orderby('Partida'     ,"partida"                        ,'partida' ,'align="left"');
		$grid->column_orderby('Denominacion',"denomi"                         ,'denomi'  ,'align="left"');
		$grid->column_orderby('Estimado'    ,"<nformat><#estimado#></nformat>",'estimado','align="right"');

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
		
		$qformato=$this->qformato=$this->datasis->formato_cpla();
		
		$mCPLA=array(
			'tabla'   =>'cpla',
			'columnas'=>array(
				'codigo' =>'C&oacute;digo',
				'denominacion'=>'Descripci&oacute;n'),
			'filtro'  =>array('codigo'=>'C&oacute;digo','denominacion'=>'Denominaci&oacute;n'),
			'retornar'=>array('codigo'=>'<#i#>'),
			'titulo'  =>'Buscar Cuenta',
			'p_uri'   =>array(4=>'<#i#>'),
			'where'=>"codigo LIKE \"$qformato\"",
			);

		$bcpla  =$this->datasis->p_modbus($mCPLA,'cuenta');

		$edit = new DataEdit($this->tits, 'r_presup');

		$edit->back_url = site_url($this->url."filteredgrid");

		$edit->post_process('insert','_post_insert');
		$edit->post_process('update','_post_update');
		$edit->post_process('delete','_post_delete');

		$edit->id = new inputField('Ref.','id');
		$edit->id->rule='max_length[11]';
		$edit->id->size =13;
		$edit->id->maxlength =11;
		$edit->id->mode='autohide';
		$edit->id->when=array('show','modify');

		$edit->partida = new inputField('Partida','partida');
		$edit->partida->rule='max_length[13]';
		$edit->partida->size =15;
		$edit->partida->maxlength =13;

		$edit->denomi = new inputField('Denominacion','denomi');
		$edit->denomi->rule='max_length[80]';
		$edit->denomi->size =82;
		$edit->denomi->maxlength =80;

		$edit->estimado = new inputField('Estimado','estimado');
		$edit->estimado->rule='max_length[19]|numeric';
		$edit->estimado->css_class='inputnum';
		$edit->estimado->size =21;
		$edit->estimado->maxlength =19;
		
		$edit->cuenta = new inputField("Cuenta. Contable", "cuenta");
		$edit->cuenta->rule='callback_chcuentac|trim';
		$edit->cuenta->size =20;
		$edit->cuenta->readonly=true;
		$edit->cuenta->append($bcpla);

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
		$mSQL="CREATE TABLE `r_presup` (
		  `id` int(11) NOT NULL AUTO_INCREMENT,
		  `partida` varchar(13) CHARACTER SET utf32 NOT NULL,
		  `denomi` varchar(80) CHARACTER SET utf32 NOT NULL,
		  `estimado` decimal(19,2) NOT NULL DEFAULT '0.00',
		  `cuenta` VARCHAR(50) NULL DEFAULT NULL,
		  PRIMARY KEY (`id`),
		  KEY `partida` (`partida`)
		) ENGINE=InnoDB AUTO_INCREMENT=33 DEFAULT CHARSET=utf8";
		$this->db->simple_query($mSQL);
	}

}
?>
