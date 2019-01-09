<?php
class R_otrospagos extends Controller {
	var $titp='Otros Pagos';
	var $tits='Otros Pagos';
	var $url ='recaudacion/r_otrospagos/';
	function R_otrospagos(){
		parent::Controller();
		$this->load->library("rapyd");
		//$this->datasis->modulo_id(216,1);
	}
	function index(){
		redirect($this->url."filteredgrid");
	}
	function filteredgrid(){
		$this->rapyd->load('datafilter','datagrid');

		$filter = new DataFilter($this->titp, 'r_otrospagos');

		$filter->id = new inputField('Id','id');
		$filter->id->rule      ='trim';
		$filter->id->size      =22;
		$filter->id->maxlength =20;

		$filter->numero = new inputField('Numero','numero');
		$filter->numero->rule      ='trim';
		$filter->numero->size      =17;
		$filter->numero->maxlength =15;

		$filter->fecha = new dateField('Fecha','fecha');
		$filter->fecha->rule      ='chfecha';
		$filter->fecha->size      =10;
		$filter->fecha->maxlength =8;

		$filter->rifci = new inputField('Rifci','rifci');
		$filter->rifci->rule      ='trim';
		$filter->rifci->size      =22;
		$filter->rifci->maxlength =20;

		$filter->nombre = new inputField('Nombre','nombre');
		$filter->nombre->rule      ='trim';
		$filter->nombre->size      =257;
		$filter->nombre->maxlength =255;

		$filter->concepto = new inputField('Concepto','concepto');
		$filter->concepto->rule      ='trim';
		$filter->concepto->size      =257;
		$filter->concepto->maxlength =255;

		$filter->monto = new inputField('Monto','monto');
		$filter->monto->rule      ='trim|numeric';
		$filter->monto->css_class ='inputnum';
		$filter->monto->size      =21;
		$filter->monto->maxlength =19;

		$filter->observa = new textareaField('Observa','observa');
		$filter->observa->rule      ='trim';
		$filter->observa->cols      = 70;
		$filter->observa->rows      = 4;

		$filter->buttons('reset', 'search');
		$filter->build();

		$uri = anchor($this->url.'dataedit/show/<raencode><#id#></raencode>','<#id#>');

		$grid = new DataGrid('');
		$grid->order_by('id','desc');
		$grid->per_page = 40;

		$grid->column_orderby('Id'                ,"$uri"                                             ,'id'           ,'align="left"');
		$grid->column_orderby('Numero'            ,"numero"                                           ,'numero'       ,'align="left"');
		$grid->column_orderby('Fecha'             ,"<dbdate_to_human><#fecha#></dbdate_to_human>"     ,'fecha'        ,'align="center"');
		$grid->column_orderby('Rifci'             ,"rifci"                                            ,'rifci'        ,'align="left"');
		$grid->column_orderby('Nombre'            ,"nombre"                                           ,'nombre'       ,'align="left"');
		$grid->column_orderby('Concepto'          ,"concepto"                                         ,'concepto'     ,'align="left"');
		$grid->column_orderby('Monto'             ,"<nformat><#monto#></nformat>"                     ,'monto'        ,'align="right"');
		$grid->column_orderby('Observa'           ,"observa"                                          ,'observa'      ,'align="left"');

		$grid->add($this->url.'dataedit/create');
		$grid->build();

		$data['filtro']  = $filter->output;
		$data['content'] = $grid->output;
		$data['head']    = $this->rapyd->get_head().script('jquery.js');
		$data['title']   = $this->titp;
		$this->load->view('view_ventanas', $data);

	}
	function dataedit(){
		$this->rapyd->load('dataobject','dataedit');

		$script='
			$(document).ready(function(){
				$(".inputnum").numeric(".");
			});
			';

		$do = new DataObject('r_otrospagos');

		$edit = new DataEdit($this->tits, $do);

		$edit->back_url = site_url($this->url."filteredgrid");

		$edit->script($script,'create');
		$edit->script($script,'modify');

		$edit->pre_process('insert','_valida');
		$edit->pre_process('update','_valida');
		$edit->pre_process('delete','_pre_delete');
		$edit->post_process('insert','_post_insert');
		$edit->post_process('update','_post_update');
		$edit->post_process('delete','_post_delete');

		$edit->id = new inputField('Id','id');
		$edit->id->rule     ='trim';
		$edit->id->size      =22;
		$edit->id->maxlength =20;
		$edit->id->mode      = 'autohide';
		$edit->id->when      =array('show','modify');

		$edit->numero = new inputField('Numero','numero');
		$edit->numero->rule     ='trim';
		$edit->numero->size      =17;
		$edit->numero->maxlength =15;

		$edit->fecha = new dateField('Fecha','fecha');
		$edit->fecha->rule     ='chfecha';
		$edit->fecha->size      =10;
		$edit->fecha->maxlength =8;

		$edit->rifci = new inputField('Rifci','rifci');
		$edit->rifci->rule     ='trim';
		$edit->rifci->size      =22;
		$edit->rifci->maxlength =20;

		$edit->nombre = new inputField('Nombre','nombre');
		$edit->nombre->rule     ='trim';
		$edit->nombre->size      =257;
		$edit->nombre->maxlength =255;

		$edit->concepto = new inputField('Concepto','concepto');
		$edit->concepto->rule     ='trim';
		$edit->concepto->size      =257;
		$edit->concepto->maxlength =255;

		$edit->monto = new inputField('Monto','monto');
		$edit->monto->rule     ='trim|numeric';
		$edit->monto->css_class='inputnum';
		$edit->monto->size      =21;
		$edit->monto->maxlength =19;

		$edit->observa = new textareaField('Observa','observa');
		$edit->observa->rule     ='trim';
		$edit->observa->cols      = 70;
		$edit->observa->rows      = 4;

		$edit->buttons( 'back');
		$edit->build();
		$data['content'] = $edit->output;
		$data['head']    = $this->rapyd->get_head().script('jquery.js').script('jquery-ui.js').script('plugins/jquery.numeric.pack.js').script('plugins/jquery.meiomask.js').style('vino/jquery-ui.css');
		$data['title']   = $this->tits;
		$this->load->view('view_ventanas', $data);

	}

	function _valida($do){
		$error = '';

		if(!empty($error)){
			$do->error_message_ar['pre_ins']=$error;
			$do->error_message_ar['pre_upd']=$error;
			return false;
		}
	}

	function _pre_delete($do){
		$error = '';

		if(!empty($error)){
			$do->error_message_ar['pre_ins']=$error;
			$do->error_message_ar['pre_upd']=$error;
			return false;
		}
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
		$query="CREATE TABLE `r_otrospagos` (
		  `id` bigint(20) NOT NULL AUTO_INCREMENT,
		  `numero` varchar(15) DEFAULT NULL,
		  `fecha` date DEFAULT NULL,
		  `rifci` varchar(20) DEFAULT NULL,
		  `nombre` varchar(255) DEFAULT NULL,
		  `concepto` varchar(255) DEFAULT NULL,
		  `monto` decimal(19,2) DEFAULT '0.00',
		  `observa` text,
		  PRIMARY KEY (`id`),
		  KEY `rifci` (`rifci`)
		) ENGINE=MyISAM AUTO_INCREMENT=152610 DEFAULT CHARSET=utf8";
		$this->db->simple_query($query);
	}

}
?>
