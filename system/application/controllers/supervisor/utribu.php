<?php
class Utribu extends Controller {
	var $titp='Unidades Tributarias';
	var $tits='Unidad Tributaria';
	var $url ='supervisor/utribu/';

	function Utribu(){
		parent::Controller();
		$this->load->library("rapyd");
		//$this->datasis->modulo_id(216,1);
	}
	function index(){
		redirect($this->url."filteredgrid");
	}
	function filteredgrid(){
		$this->rapyd->load('datafilter','datagrid');

		$filter = new DataFilter($this->titp, 'utribu');

		$filter->ano = new inputField('Ano','ano');
		$filter->ano->rule      ='trim';
		$filter->ano->size      =12;
		$filter->ano->maxlength =10;

		$filter->valor = new inputField('Valor','valor');
		$filter->valor->rule      ='trim|numeric';
		$filter->valor->css_class ='inputnum';
		$filter->valor->size      =21;
		$filter->valor->maxlength =19;

		$filter->buttons('reset', 'search');
		$filter->build();

		$uri = anchor($this->url.'dataedit/show/<raencode><#ano#></raencode>','<#ano#>');

		$grid = new DataGrid('');
		$grid->order_by('ano');
		$grid->per_page = 40;

		$grid->column_orderby('Ano'               ,"$uri"                                             ,'ano'          ,'align="left"');
		$grid->column_orderby('Valor'             ,"<nformat><#valor#></nformat>"                     ,'valor'        ,'align="right"');

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

		$do = new DataObject('utribu');

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

		$edit->ano = new inputField('Ano','ano');
		$edit->ano->rule     ='trim';
		$edit->ano->size      =12;
		$edit->ano->maxlength =10;
		$edit->ano->mode      = 'autohide';
		$edit->ano->when      =array('show','modify');

		$edit->valor = new inputField('Valor','valor');
		$edit->valor->rule     ='trim|numeric';
		$edit->valor->css_class='inputnum';
		$edit->valor->size      =21;
		$edit->valor->maxlength =19;

		$edit->buttons('add','modify', 'save', 'undo', 'delete', 'back');
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
		$query="CREATE TABLE `utribu` (
		  `ano` varchar(10) NOT NULL,
		  `valor` decimal(19,2) DEFAULT NULL,
		  PRIMARY KEY (`ano`)
		) ENGINE=MyISAM DEFAULT CHARSET=utf8";
		$this->db->simple_query($query);
	}

}
?>
