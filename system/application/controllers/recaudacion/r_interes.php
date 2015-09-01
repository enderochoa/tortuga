<?php
class R_interes extends Controller {
	var $titp='Intereses';
	var $tits='Interes';
	var $url ='recaudacion/r_interes/';
	function R_interes(){
		parent::Controller();
		$this->load->library("rapyd");
		$this->datasis->modulo_id(458,1);
	}
	function index(){
		redirect($this->url."filteredgrid");
	}
	function filteredgrid(){
		$this->rapyd->load('datafilter','datagrid');

		$filter = new DataFilter($this->titp, 'r_interes');

		$filter->ano = new inputField('Ano','ano');
		$filter->ano->rule      ='trim';
		$filter->ano->size      =13;
		$filter->ano->maxlength =11;

		$filter->mes = new inputField('Mes','mes');
		$filter->mes->rule      ='trim';
		$filter->mes->size      =13;
		$filter->mes->maxlength =11;

		$filter->monto = new inputField('Monto','monto');
		$filter->monto->rule      ='trim|numeric';
		$filter->monto->css_class ='inputnum';
		$filter->monto->size      =21;
		$filter->monto->maxlength =19;

		$filter->buttons('reset', 'search');
		$filter->build();

		$uri = anchor($this->url.'dataedit/show/<raencode><#ano#></raencode>/<raencode><#mes#></raencode>','<#ano#>-<#mes#>');

		$grid = new DataGrid('');
		$grid->order_by(' ano desc, mes desc','');
		$grid->per_page = 40;

		$grid->column_orderby('Ano'               ,"$uri"                                             ,'ano'          ,'align="left"');
		$grid->column_orderby('Mes'               ,"<nformat><#mes#></nformat>"                       ,'mes'          ,'align="right"');
		$grid->column_orderby('Monto'             ,"<nformat><#monto#></nformat>"                     ,'monto'        ,'align="right"');

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

		$do = new DataObject('r_interes');

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
		$edit->ano->size      =13;
		$edit->ano->maxlength =11;
		//$edit->ano->mode      = 'autohide';
		//$edit->ano->when      =array('show','modify');

		$edit->mes = new dropDownField('Mes','mes');
		for($i=1;$i<=12;$i++)
		$edit->mes->option($i,$i);
		
		$edit->mes->rule     ='trim';
		$edit->mes->size      =13;
		$edit->mes->maxlength =11;
		//$edit->mes->mode      = 'autohide';
		//$edit->mes->when      =array('show','modify');

		$edit->monto = new inputField('Monto','monto');
		$edit->monto->rule     ='trim|numeric';
		$edit->monto->css_class='inputnum';
		$edit->monto->size      =21;
		$edit->monto->maxlength =19;

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
		$query="CREATE TABLE `r_interes` (
		  `ano` int(11) NOT NULL,
		  `mes` int(11) NOT NULL,
		  `monto` decimal(19,2) DEFAULT NULL,
		  PRIMARY KEY (`ano`,`mes`)
		) ENGINE=MyISAM DEFAULT CHARSET=utf8";
		$this->db->simple_query($query);
	}

}
?>
