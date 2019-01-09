<?php
class solvencia extends Controller {
	var $titp='Solvencias';
	var $tits='Solvencia';
	var $url ='ingresos/solvencia/';
	function solvencia(){
		parent::Controller();
		$this->load->library("rapyd");
		$this->datasis->modulo_id(389,1);
	}
	function index(){
		redirect($this->url."filteredgrid");
	}
	function filteredgrid(){
		$this->rapyd->load('datafilter','datagrid');

		$filter = new DataFilter($this->titp, 'solvencia');

		$filter->id = new inputField('id','id');
		$filter->id->rule      ='max_length[10]';
		$filter->id->size      =12;
		$filter->id->maxlength =10;
		
		$filter->tipo = new dropdownField("Tipo","tipo");
		$filter->tipo->db_name = 'tipo';
		$filter->tipo->option(""      ,""           );
		$filter->tipo->option("REGISTRO"      ,"REGISTRO"           );
		$filter->tipo->option("ADMINISTRATIVA","ADMINISTRATIVA"     );
		$filter->tipo->style="width:200px";
		
		$filter->numero = new inputField('Solvencia Numero','numero');
		$filter->numero->size =20;
		
		$filter->nombre = new inputField('nombre','nombre');
		$filter->nombre->rule      ='max_length[100]';
		$filter->nombre->size      =102;
		$filter->nombre->maxlength =100;

		$filter->cedula = new inputField('cedula','cedula');
		$filter->cedula->rule      ='max_length[100]';
		$filter->cedula->size      =102;
		$filter->cedula->maxlength =100;

		$filter->rif = new inputField('rif','rif');
		$filter->rif->rule      ='max_length[100]';
		$filter->rif->size      =102;
		$filter->rif->maxlength =100;

		$filter->direccion = new inputField('direccion','direccion');
		$filter->direccion->rule      ='max_length[100]';
		$filter->direccion->size      =102;
		$filter->direccion->maxlength =100;

		$filter->concepto = new inputField('concepto','concepto');
		$filter->concepto->rule      ='max_length[200]';
		$filter->concepto->size      =202;
		$filter->concepto->maxlength =200;

		$filter->fecha = new dateField('fecha','fecha');
		$filter->fecha->rule      ='chfecha';
		$filter->fecha->size      =10;
		$filter->fecha->maxlength =8;

		$filter->contribu = new inputField('contribu','contribu');
		$filter->contribu->rule      ='max_length[6]';
		$filter->contribu->size      =8;
		$filter->contribu->maxlength =6;

		$filter->buttons('reset', 'search');
		$filter->build();

		$uri = anchor($this->url.'dataedit/show/<raencode><#id#></raencode>','<#id#>');

		$grid = new DataGrid('');
		$grid->order_by('id', 'desc');
		$grid->per_page = 40;

		$grid->column_orderby('id',"$uri",'id','align="left"');
		$grid->column_orderby('nombre',"nombre",'nombre','align="left"');
		$grid->column_orderby('cedula',"cedula",'cedula','align="left"');
		$grid->column_orderby('rif',"rif",'rif','align="left"');
		$grid->column_orderby('direccion',"direccion",'direccion','align="left"');
		$grid->column_orderby('concepto',"concepto",'concepto','align="left"');
		$grid->column_orderby('fecha',"<dbdate_to_human><#fecha#></dbdate_to_human>",'fecha','align="center"');
		$grid->column_orderby('contribu',"contribu",'contribu','align="left"');

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
		
		$mCONTRIBU=array(
				'tabla'   =>'contribu',
				'columnas' =>array(
					'codigo'   =>'C&oacute;digo',
					'nacionali'=>'',
					'rifci'    =>'RIF',
					'nombre'   =>'Nombre',
					'direccion'=>'Direcci&oacute;n',
					'telefono' =>'Telefono'
					),
				'filtro'  =>array(
					'codigo'   =>'C&oacute;digo',
					'rifci'    =>'RIF',
					'nacionali'=>'Nacionalidad',
					'nombre'   =>'Nombre',
					'direccion'=>'Direcci&oacute;n',
					'telefono' =>'Telefono'
					),
				'retornar'=>array(
				'codigo'   =>'contribu' ,
				'rifci'    =>'cedula' ,
				'nombre'   =>'nombre'    ,
				'direccion'=>'direccion'  ,
				),
				'script'  =>array('cal_nacionali()'),
				'titulo'  =>'Buscar Contribuyente');
			
		$bCONTRIBU=$this->datasis->modbus($mCONTRIBU);

		$edit = new DataEdit($this->tits, 'solvencia');

		$edit->back_url = site_url($this->url."filteredgrid");

		$edit->post_process('insert','_post_insert');
		$edit->post_process('update','_post_update');
		$edit->post_process('delete','_post_delete');
		
		$edit->tipo = new dropdownField("Tipo","tipo");
		$edit->tipo->db_name = 'tipo';
		$edit->tipo->option("REGISTRO"      ,"REGISTRO"           );
		$edit->tipo->option("ADMINISTRATIVA","ADMINISTRATIVA"     );
		$edit->tipo->style="width:200px";
		
		$edit->numero = new inputField('Solvencia Numero','numero');
		$edit->numero->rule='max_length[20]';
		$edit->numero->size =20;
		$edit->numero->maxlength =20;
		
		$edit->contribu = new inputField('Contribuyente','contribu');
		$edit->contribu->rule='max_length[6]';
		$edit->contribu->size =8;
		$edit->contribu->maxlength =6;
		$edit->contribu->append($bCONTRIBU);

		$edit->id = new inputField('Ref.','id');
		$edit->id->rule='max_length[10]';
		$edit->id->size =12;
		$edit->id->maxlength =10;
		$edit->id->mode='autohide';
		$edit->id->when=array('show','modify');

		$edit->nombre = new inputField('Nombre','nombre');
		$edit->nombre->rule='max_length[60]|required';
		$edit->nombre->size =40;
		$edit->nombre->maxlength =100;

		$edit->cedula = new inputField('Cedula','cedula');
		$edit->cedula->rule='max_length[100]';
		$edit->cedula->size =20;
		$edit->cedula->maxlength =100;

		$edit->rif = new inputField('Rif','rif');
		$edit->rif->rule='max_length[100]';
		$edit->rif->size =20;
		$edit->rif->maxlength =100;

		$edit->direccion = new inputField('Direccion','direccion');
		$edit->direccion->rule='max_length[100]';
		$edit->direccion->size =80;
		$edit->direccion->maxlength =100;

		$edit->concepto = new inputField('Concepto','concepto');
		$edit->concepto->rule='max_length[200]';
		$edit->concepto->size =80;
		$edit->concepto->maxlength =200;

		$edit->fecha = new dateField('Fecha','fecha');
		$edit->fecha->rule='chfecha';
		$edit->fecha->size =10;
		$edit->fecha->maxlength =8;

		$edit->buttons('add','modify', 'save', 'undo', 'delete', 'back');
		$edit->build();
		
		$smenu['link']=barra_menu('814');
		$data['content'] = $edit->output;
		$data['smenu']   = $this->load->view('view_sub_menu', $smenu,true); 
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
		$mSQL="CREATE TABLE `solvencia` (
		  `id` int(10) NOT NULL AUTO_INCREMENT,
		  `nombre` varchar(100) COLLATE utf8_general_ci DEFAULT NULL,
		  `cedula` varchar(100) COLLATE utf8_general_ci DEFAULT NULL,
		  `rif` varchar(100) COLLATE utf8_general_ci DEFAULT NULL,
		  `direccion` varchar(100) COLLATE utf8_general_ci DEFAULT NULL,
		  `concepto` varchar(200) COLLATE utf8_general_ci DEFAULT NULL,
		  `fecha` date DEFAULT NULL,
		  `contribu` varchar(6) COLLATE utf8_general_ci DEFAULT NULL,
		  PRIMARY KEY (`id`)
		) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci";
		$this->db->simple_query($mSQL);
		
		$query="ALTER TABLE `solvencia` ADD COLUMN `numero` VARCHAR(20) NULL DEFAULT NULL AFTER `contribu`";
		$this->db->simple_query($query);
		$query="ALTER TABLE `solvencia` ADD COLUMN `tipo` VARCHAR(20) NULL DEFAULT NULL AFTER `id`";
		$this->db->simple_query($query);
	}

}
?>
