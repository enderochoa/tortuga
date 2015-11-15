<?php
class R_cerrar extends Controller {
	var $titp='Cerrar Dias';
	var $tits='Cerrar Dia';
	var $url ='recaudacion/r_cerrar/';
	function R_cerrar(){
		parent::Controller();
		$this->load->library("rapyd");
		$this->datasis->modulo_id(479,1);
	}
	function index(){
		redirect($this->url."filteredgrid");
	}
	function filteredgrid(){
		$this->rapyd->load('datafilter','datagrid');

		$filter = new DataFilter($this->titp, 'r_cerrar');

		$filter->id = new inputField('Id','id');
		$filter->id->rule      ='trim';
		$filter->id->size      =13;
		$filter->id->maxlength =11;

		$filter->fecha = new dateField('Fecha','fecha');
		$filter->fecha->rule      ='chfecha';
		$filter->fecha->size      =10;
		$filter->fecha->maxlength =8;

		$filter->buttons('reset', 'search');
		$filter->build();

		$uri = anchor($this->url.'dataedit/show/<raencode><#id#></raencode>','<#id#>');

		$grid = new DataGrid('');
		$grid->order_by('id','desc');
		$grid->per_page = 40;

		$grid->column_orderby('Id'                ,"$uri"                                             ,'id'           ,'align="left"');
		$grid->column_orderby('Fecha'             ,"<dbdate_to_human><#fecha#></dbdate_to_human>"     ,'fecha'        ,'align="center"');

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

		$do = new DataObject('r_cerrar');

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
		$edit->id->size      =13;
		$edit->id->maxlength =11;
		$edit->id->mode      = 'autohide';
		$edit->id->when      =array('show','modify');

		$edit->fecha = new dateField('Fecha','fecha');
		$edit->fecha->rule     ='chfecha';
		$edit->fecha->size      =10;
		$edit->fecha->maxlength =8;

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
		$query="CREATE TABLE `r_cerrar` (
		  `id` int(11) NOT NULL AUTO_INCREMENT,
		  `fecha` date DEFAULT NULL,
		  PRIMARY KEY (`id`)
		) ENGINE=InnoDB DEFAULT CHARSET=utf8";
		$this->db->simple_query($query);
	}

}
?>
