<?php
class R_mbancrel extends Controller {
	var $titp='Relacionar Movimientos Bancarios';
	var $tits='Relacionar Movimientos Bancarios';
	var $url ='recaudacion/r_mbancrel/';
	function R_mbancrel(){
		parent::Controller();
		$this->load->library("rapyd");
		//$this->datasis->modulo_id(216,1);
	}
	
	function index(){
		redirect($this->url."filteredgrid");
	}
	
	function filteredgrid(){
		$this->rapyd->load('datafilter','datagrid');
		
		$mBANC=array(
				'tabla'   =>'banc',
				'columnas'=>array(
					'codbanc' =>'C&oacute;odigo',
					'banco'   =>'Banco',
					'numcuent'=>'Cuenta',
					'saldo'   =>'Saldo'
				),
				'filtro'  =>array(
					'codbanc' =>'C&oacute;odigo',
					'banco'   =>'Banco',
					'numcuent'=>'Cuenta'
				),
				'retornar'=>array(
					'codbanc'=>'codbanc' 
				),
				'where'=>'activo = "S"',
				'titulo'  =>'Buscar Bancos'
				);

		$bBANC=$this->datasis->modbus($mBANC);

		$filter = new DataFilter($this->titp, 'r_mbancrel a');
		$filter->db->select(array('a.id','a.codbanc','a.tipo_doc','a.cheque','a.monto','a.total','a.fecha','a.fechaing','a.concepto','GROUP_CONCAT(b.cheque SEPARATOR " ") transacciones'));
		$filter->db->join('r_mbanc b','a.id=b.id_mbancrel');

		$filter->id = new inputField('Id','id');
		$filter->id->rule      ='trim';
		$filter->id->size      =13;
		$filter->id->maxlength =11;
		$filter->id->db_name   ='a.id';

		$filter->codbanc = new inputField('Codbanc','codbanc');
		$filter->codbanc->rule      ='trim';
		$filter->codbanc->size      =12;
		$filter->codbanc->maxlength =10;
		$filter->codbanc->append($bBANC);
		$filter->codbanc->db_name   ='a.codbanc';

		$filter->tipo_doc = new dropdownField("Tipo Doc","tipo_doc");
		$filter->tipo_doc->option("","");
		$filter->tipo_doc->option("ND","Nota de Debito");
		$filter->tipo_doc->option("NC","Nota de Credito");
		$filter->tipo_doc->option("CH","Cheque");
		$filter->tipo_doc->option("DP","Deposito");	
		$filter->tipo_doc->db_name   ='a.tipo_doc';

		$filter->cheque = new inputField('Transaccion','cheque');
		$filter->cheque->rule      ='trim';
		$filter->cheque->size      =10;
		$filter->cheque->db_name   ='a.cheque';

		$filter->monto = new inputField('Monto','monto');
		$filter->monto->rule      ='trim|numeric';
		$filter->monto->css_class ='inputnum';
		$filter->monto->size      =21;
		$filter->monto->maxlength =19;
		$filter->monto->db_name   ='a.monto';

		$filter->total = new inputField('Total Items','total');
		$filter->total->rule      ='trim|numeric';
		$filter->total->css_class ='inputnum';
		$filter->total->size      =21;
		$filter->total->maxlength =19;
		$filter->total->db_name   ='a.total';

		$filter->fecha = new dateField('Fecha Transaccion','fecha');
		$filter->fecha->rule      ='chfecha';
		$filter->fecha->size      =10;
		$filter->fecha->maxlength =8;
		$filter->fecha->db_name   ='a.fecha';

		$filter->fechaing = new dateField('Fecha Ingreso','fechaing');
		$filter->fechaing->rule      ='chfecha';
		$filter->fechaing->size      =10;
		$filter->fechaing->maxlength =8;
		$filter->fechaing->db_name   ='a.fechaing';

		$filter->concepto = new inputField('Concepto','concepto');
		$filter->concepto->rule      ='trim';
		$filter->concepto->size      =20;
		$filter->concepto->db_name   ='a.concepto';
		
		$filter->transacciones = new inputField('Transaccion Detalle','transacciones');
		$filter->transacciones->rule      ='trim';
		$filter->transacciones->size      =10;
		$filter->transacciones->db_name   ='b.cheque';

		$filter->buttons('reset', 'search');
		$filter->build();

		$uri = anchor($this->url.'dataedit/show/<raencode><#id#></raencode>','<#id#>');

		$grid = new DataGrid('');
		$grid->order_by('id','desc');
		$grid->per_page = 40;

		$grid->column_orderby('Id'                ,"$uri"                                             ,'id'           ,'align="left"'  );
		$grid->column_orderby('Codbanc'           ,"codbanc"                                          ,'codbanc'      ,'align="left"'  );
		$grid->column_orderby('Tipo_doc'          ,"tipo_doc"                                         ,'tipo_doc'     ,'align="left"'  );
		$grid->column_orderby('Cheque'            ,"cheque"                                           ,'cheque'       ,'align="left"'  );
		$grid->column_orderby('Monto'             ,"<nformat><#monto#></nformat>"                     ,'monto'        ,'align="right"' );
		$grid->column_orderby('Total'             ,"<nformat><#total#></nformat>"                     ,'total'        ,'align="right"' );
		$grid->column_orderby('Fecha'             ,"<dbdate_to_human><#fecha#></dbdate_to_human>"     ,'fecha'        ,'align="center"');
		$grid->column_orderby('Fecha Ingreso'     ,"<dbdate_to_human><#fechaing#></dbdate_to_human>"  ,'fechaing'     ,'align="center"');
		$grid->column_orderby('Concepto'          ,"concepto"                                         ,'concepto'     ,'align="left"'  );

		$grid->add($this->url.'selectr_mbanc');
		$grid->build();

		$data['filtro']  = $filter->output;
		$data['content'] = $grid->output;
		$data['head']    = $this->rapyd->get_head().script('jquery.js');
		$data['title']   = $this->titp;
		$this->load->view('view_ventanas', $data);

	}
	
	function dataedit(){
		$this->rapyd->load('dataobject','datadetails');

		$mBANC=array(
				'tabla'   =>'banc',
				'columnas'=>array(
					'codbanc' =>'C&oacute;odigo',
					'banco'   =>'Banco',
					'numcuent'=>'Cuenta',
					'saldo'   =>'Saldo'
				),
				'filtro'  =>array(
					'codbanc' =>'C&oacute;odigo',
					'banco'   =>'Banco',
					'numcuent'=>'Cuenta'
				),
				'retornar'=>array(
					'codbanc'=>'codbanc' 
				),
				'where'=>'activo = "S"',
				'titulo'  =>'Buscar Bancos'
				);

		$bBANC=$this->datasis->modbus($mBANC);

		$script='
			$(document).ready(function(){
				$(".inputnum").numeric(".");
			});
			';

		$do = new DataObject('r_mbancrel');
		$do->rel_one_to_many('r_mbanc', 'r_mbanc', array('id'=>'id_mbancrel'));

		$edit = new DataDetails($this->tits, $do);

		$edit->back_url = site_url($this->url."filteredgrid");

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

		$edit->codbanc = new inputField('Codbanc','codbanc');
		$edit->codbanc->rule     ='trim|required';
		$edit->codbanc->size      =12;
		$edit->codbanc->maxlength =10;
		$edit->codbanc->append($bBANC);
		
		$edit->tipo_doc = new dropdownField("Tipo Documento","tipo_doc"             );
		$edit->tipo_doc->option("CH","Cheque"         );
		$edit->tipo_doc->option("NC","Nota de Credito");
		$edit->tipo_doc->option("ND","Nota de Debito" );
		$edit->tipo_doc->option("DP","Deposito"       );
		$edit->tipo_doc->style  ="width:180px";
		$edit->tipo_doc->rule   = 'required';

		$edit->cheque = new textareaField('Transaccion','cheque');
		$edit->cheque->rule     ='trim';
		$edit->cheque->size     =20;

		$edit->monto = new inputField('Monto','monto');
		$edit->monto->rule     ='trim|numeric';
		$edit->monto->css_class='inputnum';
		$edit->monto->size      =21;
		$edit->monto->maxlength =19;

		$edit->total = new inputField('Total','total');
		$edit->total->rule     ='trim|numeric';
		$edit->total->css_class='inputnum';
		$edit->total->size      =21;
		$edit->total->maxlength =19;
		$edit->total->mode      ='autohide';

		$edit->fecha = new dateField('Fecha Trasaccion','fecha');
		$edit->fecha->rule     ='chfecha';
		$edit->fecha->size      =10;
		$edit->fecha->maxlength =8;

		$edit->fechaing = new dateField('Fecha Ingreso','fechaing');
		$edit->fechaing->rule     ='chfecha';
		$edit->fechaing->size      =10;
		$edit->fechaing->maxlength =8;

		$edit->concepto = new textareaField('Concepto','concepto');
		$edit->concepto->rule     ='trim';
		$edit->concepto->cols      = 40;
		$edit->concepto->rows      = 2;
		
		/*
		 * DETALLE
		 * */
		 
		$edit->id = new hiddenField('Id','id_<#i#>');
		$edit->id->rel_id ='r_mbanc';
		$edit->id->db_name='id';
		
		$edit->abono = new hiddenField('Abono','abono_<#i#>');
		$edit->abono->rel_id ='r_mbanc';
		$edit->abono->db_name='abono';

		$edit->codmbanc = new hiddenField('Codmbanc','codmbanc_<#i#>');
		$edit->codmbanc->rel_id ='r_mbanc';
		$edit->codmbanc->db_name='codmbanc';

		$edit->codbanc = new inputField('Codbanc','codbanc_<#i#>');
		$edit->codbanc->rel_id    ='r_mbanc';
		$edit->codbanc->db_name   ='codbanc';
		$edit->codbanc->type      ="inputhidden";

		$edit->tipo_doc = new inputField('Tipo_doc','tipo_doc_<#i#>');
		$edit->tipo_doc->rel_id    ='r_mbanc';
		$edit->tipo_doc->db_name   ='tipo_doc';
		$edit->tipo_doc->type      ="inputhidden";
		
		$edit->fecha = new dateField('Fecha','fecha_<#i#>');
		$edit->fecha->rel_id    ='r_mbanc';
		$edit->fecha->db_name   ='fecha';
		$edit->fecha->type      ="inputhidden";

		$edit->cheque = new textareaField('Cheque','cheque_<#i#>');
		$edit->cheque->rel_id    ='r_mbanc';
		$edit->cheque->db_name   ='cheque';
		$edit->cheque->type      ="inputhidden";

		$edit->monto = new inputField('Monto','monto');
		$edit->monto->rule     ='trim|numeric';
		$edit->monto->css_class='inputnum';
		$edit->monto->size      =21;
		$edit->monto->maxlength =19;

		

		$edit->buttons('modify', 'save', 'undo', 'back');
		$edit->build();
		
		$conten["form"]  =&  $edit;
		$data['content'] = $this->load->view('recaudacion/r_mbancrel'  , $conten,true);
		$data['title']   = "$this->tits";
		$data["head"]    = script('jquery.js').script('jquery-ui.js').script('plugins/jquery.numeric.pack.js').script('plugins/jquery.floatnumber.js').script('plugins/jquery.meiomask.js').style('vino/jquery-ui.css').$this->rapyd->get_head();
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
		$query="CREATE TABLE `r_mbancrel` (
		  `id` int(11) NOT NULL AUTO_INCREMENT,
		  `codbanc` varchar(10) NOT NULL,
		  `tipo_doc` char(2) NOT NULL,
		  `cheque` text NOT NULL,
		  `monto` decimal(19,2) NOT NULL,
		  `total` decimal(19,2) NOT NULL,
		  `fecha` date NOT NULL,
		  `fechaing` date NOT NULL,
		  `concepto` text NOT NULL,
		  PRIMARY KEY (`id`)
		) ENGINE=MyISAM DEFAULT CHARSET=utf8";
		$this->db->simple_query($query);
	}

}
?>
