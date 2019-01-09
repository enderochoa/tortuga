<?php
class vehiculo extends Controller {
	var $titp='Vehiculos';
	var $tits='Vehiculo';
	var $url ='ingresos/vehiculo/';
	function vehiculo(){
		parent::Controller();
		$this->load->library("rapyd");
		$this->datasis->modulo_id(350,1);
	}
	function index(){
		redirect($this->url."filteredgrid");
	}
	function filteredgrid(){
		$this->rapyd->load('datafilter','datagrid');

		$contribu=array(
				'tabla'   =>'contribu',
				'columnas'=>array(
					'codigo' =>'C&oacute;odigo',
					'nombre'  =>'Nombre',
					'rifci'     =>'Rif'),
				'filtro'  =>array(
					'codigo' =>'C&oacute;odigo',
					'nombre'  =>'Nombre',
					'rif'     =>'Rif'),
				'retornar'=>array(
					'codigo'=>'contribu'
					 ),
				
				'titulo'  =>'Buscar Contribuyentes');

		$contribu=$this->datasis->modbus($contribu);

		$filter = new DataFilter('Titulo', 'vehiculo');
		$filter->db->select(array('id','(SELECT nombre FROM contribu WHERE vehiculo.contribu=codigo) contribu','(SELECT nombre FROM clase WHERE vehiculo.clase=codigo) clase','marca','tipo','modelo','color','capacidad','serial_m','placa_ant','placa_act','ano','peso','serial_c','monto','deuda','ult_ano','registrado','asovehi','tri1','tri2','tri3','tri4','deudacan','total'));

		$filter->id = new inputField('Referencia','id');
		$filter->id->rule      ='max_length[11]';
		$filter->id->size      =13;
		$filter->id->maxlength =11;

		$filter->contribu = new inputField('Contribuyente','contribu');
		$filter->contribu->rule      ='max_length[6]';
		$filter->contribu->size      =8;
		$filter->contribu->maxlength =6;
		$filter->contribu->append($contribu);

		$filter->clase = new dropdownField('Clase','clase');
		$filter->clase->option('','');
		$filter->clase->options("SELECT codigo,nombre FROM clase ORDER BY nombre");

		$filter->marca = new dropdownField('Marca','marca');
		$filter->marca->option('','');
		$filter->marca->options("SELECT marca,marca m FROM marca ORDER BY marca");

		$filter->tipo = new dropdownField('Tipo','tipo');
		$filter->tipo->option('','');
		$filter->tipo->options("SELECT tipo,tipo m FROM tipo ORDER BY tipo");

		$filter->modelo = new inputField('Modelo','modelo');
		$filter->modelo->rule      ='max_length[10]';
		$filter->modelo->size      =12;
		$filter->modelo->maxlength =10;

		$filter->color = new inputField('Color','color');
		$filter->color->rule      ='max_length[20]';
		$filter->color->size      =22;
		$filter->color->maxlength =20;

		$filter->capacidad = new inputField('Capacidad','capacidad');
		$filter->capacidad->rule      ='max_length[11]';
		$filter->capacidad->size      =13;
		$filter->capacidad->maxlength =11;

		$filter->serial_m = new inputField('Serial Motor','serial_m');
		$filter->serial_m->rule      ='max_length[15]';
		$filter->serial_m->size      =17;
		$filter->serial_m->maxlength =15;

		$filter->placa_ant = new inputField('Placa Anterior','placa_ant');
		$filter->placa_ant->rule      ='max_length[7]';
		$filter->placa_ant->size      =9;
		$filter->placa_ant->maxlength =7;

		$filter->placa_act = new inputField('Placa','placa_act');
		$filter->placa_act->rule      ='max_length[9]';
		$filter->placa_act->size      =11;
		$filter->placa_act->maxlength =9;

		$filter->ano = new inputField('A&ntilde;o','ano');
		$filter->ano->rule      ='max_length[4]';
		$filter->ano->size      =6;
		$filter->ano->maxlength =4;

		$filter->peso = new inputField('Peso','peso');
		$filter->peso->rule      ='max_length[8]';
		$filter->peso->size      =10;
		$filter->peso->maxlength =8;

		$filter->serial_c = new inputField('Serial Carroceria','serial_c');
		$filter->serial_c->rule      ='max_length[15]';
		$filter->serial_c->size      =17;
		$filter->serial_c->maxlength =15;

		$filter->buttons('reset', 'search');
		$filter->build();

		$uri = anchor($this->url.'dataedit/show/<raencode><#id#></raencode>','<#id#>');

		$grid = new DataGrid('');
		$grid->order_by('id');
		$grid->per_page = 40;

		$grid->column_orderby('Referencia'   ,"$uri",'id');
		$grid->column_orderby('Contribuyente',"contribu",'contribu');
		$grid->column_orderby('Clase',"clase",'clase');
		$grid->column_orderby('Marca',"marca",'marca');
		$grid->column_orderby('Tipo',"tipo",'tipo');
		$grid->column_orderby('Modelo',"modelo",'modelo');
		$grid->column_orderby('Color',"color",'color');
		$grid->column_orderby('Capacidad',"<nformat><#capacidad#></nformat>",'capacidad');
		$grid->column_orderby('Serial Motor',"serial_m",'serial_m');
		$grid->column_orderby('Placa Anterior',"placa_ant",'placa_ant');
		$grid->column_orderby('Placa',"placa_act",'placa_act');
		$grid->column_orderby('A&ntilde;o',"ano",'ano');
		$grid->column_orderby('Peso',"<nformat><#peso#></nformat>",'peso');
		$grid->column_orderby('Serial Carroceria',"serial_c",'serial_c');
		$grid->column_orderby('Monto',"<nformat><#monto#></nformat>",'monto');
		
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

		$contribu=array(
				'tabla'   =>'contribu',
				'columnas'=>array(
					'codigo' =>'C&oacute;odigo',
					'nombre'  =>'Nombre',
					'rifci'     =>'Rif'),
				'filtro'  =>array(
					'codigo' =>'C&oacute;odigo',
					'nombre'  =>'Nombre',
					'rif'     =>'Rif'),
				'retornar'=>array(
					'codigo'=>'contribu'
					 ),
				
				'titulo'  =>'Buscar Contribuyentes');

		$contribu=$this->datasis->modbus($contribu);

		$edit = new DataEdit($this->tits, 'vehiculo');

		$edit->back_url = site_url($this->url."filteredgrid");

		$edit->post_process('insert','_post_insert');
		$edit->post_process('update','_post_update');
		$edit->post_process('delete','_post_delete');

		$edit->id = new inputField('Referencia','id');
		$edit->id->rule='max_length[11]';
		$edit->id->size =13;
		$edit->id->maxlength =11;
		$edit->id->mode='autohide';
		$edit->id->when=array('show','modify');

		$edit->contribu = new inputField('Contribuyente','contribu');
		$edit->contribu->rule='max_length[6]';
		$edit->contribu->size =8;
		$edit->contribu->maxlength =6;
		$edit->contribu->append($contribu);

		$edit->clase = new dropdownField('Clase','clase');
		$edit->clase->options("SELECT codigo,nombre FROM clase ORDER BY nombre");

		$edit->marca = new dropdownField('Marca','marca');
		$edit->marca->options("SELECT marca,marca m FROM marca ORDER BY marca");

		$edit->tipo = new inputField('Tipo','tipo');
		$edit->tipo->options("SELECT tipo,tipo m FROM tipo ORDER BY tipo");

		$edit->modelo = new inputField('Modelo','modelo');
		$edit->modelo->rule='max_length[10]';
		$edit->modelo->size =12;
		$edit->modelo->maxlength =10;

		$edit->color = new inputField('Color','color');
		$edit->color->rule='max_length[20]';
		$edit->color->size =22;
		$edit->color->maxlength =20;

		$edit->capacidad = new inputField('Capacidad','capacidad');
		$edit->capacidad->rule='max_length[11]';
		$edit->capacidad->size =13;
		$edit->capacidad->maxlength =11;

		$edit->serial_m = new inputField('Serial Motor','serial_m');
		$edit->serial_m->rule='max_length[15]';
		$edit->serial_m->size =17;
		$edit->serial_m->maxlength =15;

		$edit->placa_ant = new inputField('Placa Anterior','placa_ant');
		$edit->placa_ant->rule='max_length[7]';
		$edit->placa_ant->size =9;
		$edit->placa_ant->maxlength =7;

		$edit->placa_act = new inputField('Placa','placa_act');
		$edit->placa_act->rule='max_length[9]';
		$edit->placa_act->size =11;
		$edit->placa_act->maxlength =9;

		$edit->ano = new inputField('A&ntilde;o','ano');
		$edit->ano->rule='max_length[4]';
		$edit->ano->size =6;
		$edit->ano->maxlength =4;

		$edit->peso = new inputField('Peso','peso');
		$edit->peso->rule='max_length[8]';
		$edit->peso->size =10;
		$edit->peso->maxlength =8;
		
		$edit->ejes = new inputField('Ejes','ejes');
		$edit->ejes->rule='max_length[8]';
		$edit->ejes->size =10;
		$edit->ejes->maxlength =8;

		$edit->serial_c = new inputField('Serial Carroceria','serial_c');
		$edit->serial_c->rule='max_length[15]';
		$edit->serial_c->size =17;
		$edit->serial_c->maxlength =15;
/*
		$edit->monto = new inputField('Monto','monto');
		$edit->monto->rule='max_length[8]';
		$edit->monto->size =10;
		$edit->monto->maxlength =8;

		$edit->deuda = new inputField('deuda','deuda');
		$edit->deuda->rule='max_length[8]';
		$edit->deuda->size =10;
		$edit->deuda->maxlength =8;

		$edit->ult_ano = new inputField('ult_ano','ult_ano');
		$edit->ult_ano->rule='max_length[4]';
		$edit->ult_ano->size =6;
		$edit->ult_ano->maxlength =4;

		$edit->registrado = new inputField('registrado','registrado');
		$edit->registrado->rule='max_length[1]';
		$edit->registrado->size =3;
		$edit->registrado->maxlength =1;

		$edit->asovehi = new inputField('asovehi','asovehi');
		$edit->asovehi->rule='max_length[2]';
		$edit->asovehi->size =4;
		$edit->asovehi->maxlength =2;

		$edit->tri1 = new inputField('tri1','tri1');
		$edit->tri1->rule='max_length[8]';
		$edit->tri1->size =10;
		$edit->tri1->maxlength =8;

		$edit->tri2 = new inputField('tri2','tri2');
		$edit->tri2->rule='max_length[8]';
		$edit->tri2->size =10;
		$edit->tri2->maxlength =8;

		$edit->tri3 = new inputField('tri3','tri3');
		$edit->tri3->rule='max_length[8]';
		$edit->tri3->size =10;
		$edit->tri3->maxlength =8;

		$edit->tri4 = new inputField('tri4','tri4');
		$edit->tri4->rule='max_length[8]';
		$edit->tri4->size =10;
		$edit->tri4->maxlength =8;

		$edit->deudacan = new inputField('deudacan','deudacan');
		$edit->deudacan->rule='max_length[8]';
		$edit->deudacan->size =10;
		$edit->deudacan->maxlength =8;

		$edit->total = new inputField('total','total');
		$edit->total->rule='max_length[8]';
		$edit->total->size =10;
		$edit->total->maxlength =8;
*/
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
		$mSQL="CREATE TABLE `vehiculo` (
		`id` int(11) NOT NULL AUTO_INCREMENT,
		`contribu` char(6) DEFAULT NULL,
		`clase` char(1) DEFAULT NULL,
		`marca` char(10) DEFAULT NULL,
		`tipo` char(10) DEFAULT NULL,
		`modelo` char(10) DEFAULT NULL,
		`color` char(20) DEFAULT NULL,
		`capaci` int(11) DEFAULT NULL,
		`serial_m` char(15) DEFAULT NULL,
		`placa_ant` char(7) DEFAULT NULL,
		`placa_act` char(9) DEFAULT NULL,
		`ano` char(4) DEFAULT NULL,
		`peso` double DEFAULT NULL,
		`serial_c` char(15) DEFAULT NULL,
		`monto` double DEFAULT NULL,
		`deuda` double DEFAULT NULL,
		`ult_ano` char(4) DEFAULT NULL,
		`registrado` char(1) DEFAULT NULL,
		`asovehi` char(2) DEFAULT NULL,
		`tri1` double DEFAULT NULL,
		`tri2` double DEFAULT NULL,
		`tri3` double DEFAULT NULL,
		`tri4` double DEFAULT NULL,
		`deudacan` double DEFAULT NULL,
		`total` double DEFAULT NULL,
		PRIMARY KEY (`id`)
	  ) ENGINE=MyISAM AUTO_INCREMENT=5686 DEFAULT CHARSET=utf8";
		$this->db->simple_query($mSQL);
		$query="ALTER TABLE `vehiculo`	ADD COLUMN `recibo` INT(11) NULL DEFAULT NULL";
		$this->db->simple_query($mSQL);
		$query="ALTER TABLE `vehiculo` 	CHANGE COLUMN `clase` `clase` CHAR(10) NULL DEFAULT NULL AFTER `contribu`";
		$this->db->simple_query($mSQL);
		$query="ALTER TABLE `vehiculo` 	CHANGE COLUMN `capaci` `capacidad` INT(11) NULL DEFAULT NULL AFTER `color`";
		$this->db->simple_query($mSQL);
		$query="ALTER TABLE `vehiculo` ADD COLUMN `ejes` INT(11) NULL DEFAULT NULL AFTER `capacidad`";
		$this->db->simple_query($mSQL);
		$query="ALTER TABLE `vehiculo` CHANGE COLUMN `peso` `peso` DECIMAL(19,2) NULL DEFAULT NULL AFTER `ano`";
		$this->db->simple_query($mSQL);
	}
	

}
?>
