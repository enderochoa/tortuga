<?php
class inmueble extends Controller {
	var $titp='Inmuebles';
	var $tits='Inmueble';
	var $url ='ingresos/inmueble/';
	function inmueble(){
		parent::Controller();
		$this->load->library("rapyd");
		$this->datasis->modulo_id(351,1);
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

		$filter = new DataFilter($this->titp, 'inmueble');
		$filter->db->select(array('id','(SELECT nombre FROM contribu WHERE inmueble.contribu=codigo) contribu','ctainos','direccion','no_predio','(SELECT nombre FROM local WHERE inmueble.sector=codigo) sector','tipo_in','no_hab','(SELECT nombre FROM claseo WHERE inmueble.clase=codigo) clase','tipo','monto','registrado','deuda','enero','febrero','marzo','abril','mayo','junio','julio','agosto','septiembre','octubre','noviembre','diciembre','deudacan','total','agua'));

		$filter->id = new inputField('Referencia','id');
		$filter->id->rule      ='max_length[11]';
		$filter->id->size      =13;
		$filter->id->maxlength =11;

		$filter->contribu = new inputField('Contribuyente','contribu');
		$filter->contribu->rule      ='max_length[6]';
		$filter->contribu->size      =8;
		$filter->contribu->maxlength =6;
		$filter->contribu->append($contribu);

		$filter->ctainos = new inputField('Cuenta inos','ctainos');
		$filter->ctainos->rule      ='max_length[7]';
		$filter->ctainos->size      =9;
		$filter->ctainos->maxlength =7;

		$filter->direccion = new inputField('Direcci&oacute;n','direccion');
		$filter->direccion->rule      ='max_length[50]';
		$filter->direccion->size      =52;
		$filter->direccion->maxlength =50;

		$filter->no_predio = new inputField('Nro. Promedio','no_predio');
		$filter->no_predio->rule      ='max_length[10]';
		$filter->no_predio->size      =12;
		$filter->no_predio->maxlength =10;

		$filter->sector = new dropdownField('Sector','sector');
		$filter->sector->option('','');
		$filter->sector->options("SELECT codigo,nombre FROM local ORDER BY nombre");

		$filter->tipo_in = new dropdownField('Tipo Inmueble','tipo_in');
		$filter->tipo_in->option('','');
		$filter->tipo_in->options("SELECT tipoin,tipoin d FROM tipoin ORDER BY tipoin");

		$filter->no_hab = new inputField('Nro. Habitaci&oacute;n','no_hab');
		$filter->no_hab->rule      ='max_length[11]';
		$filter->no_hab->size      =13;
		$filter->no_hab->maxlength =11;

		$filter->clase = new dropdownField('clase','clase');
		$filter->clase->option('','');
		$filter->clase->options("SELECT codigo,nombre FROM claseo ORDER BY nombre");

		$filter->tipo = new inputField('tipo','tipo');
		$filter->tipo->rule      ='max_length[1]';
		$filter->tipo->size      =3;
		$filter->tipo->maxlength =1;

		$filter->monto = new inputField('Monto','monto');
		$filter->monto->rule      ='max_length[8]';
		$filter->monto->size      =10;
		$filter->monto->maxlength =8;

		$filter->registrado = new inputField('Registrado','registrado');
		$filter->registrado->rule      ='max_length[1]';
		$filter->registrado->size      =3;
		$filter->registrado->maxlength =1;

		$filter->buttons('reset', 'search');
		$filter->build();

		$uri = anchor($this->url.'dataedit/show/<raencode><#id#></raencode>','<#id#>');

		$grid = new DataGrid('');
		$grid->order_by('id');
		$grid->per_page = 40;

		$grid->column_orderby('Referencia',"$uri",'id');
		$grid->column_orderby('Contribuyente',"contribu",'contribu');
		$grid->column_orderby('Cuenta Inos',"ctainos",'ctainos');
		$grid->column_orderby('Direcci&oacute;n',"direccion",'direccion');
		$grid->column_orderby('Nro Promedio',"no_predio",'no_predio');
		$grid->column_orderby('Sector',"sector",'sector');
		$grid->column_orderby('Tipo Inmueble',"tipo_in",'tipo_in');
		$grid->column_orderby('Nro. habitacion',"<nformat><#no_hab#></nformat>",'no_hab');
		$grid->column_orderby('Clase',"clase",'clase');
		$grid->column_orderby('Tipo',"tipo",'tipo');
		$grid->column_orderby('Monto',"<nformat><#monto#></nformat>",'monto');
		$grid->column_orderby('Registrado',"registrado",'registrado');

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

		$edit = new DataEdit($this->tits, 'inmueble');

		$edit->back_url = site_url($this->url."filteredgrid");

		$edit->post_process('insert','_post_insert');
		$edit->post_process('update','_post_update');
		$edit->post_process('delete','_post_delete');

		$edit->id = new inputField('id','id');
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

		$edit->ctainos = new inputField('Cuenta Inos','ctainos');
		$edit->ctainos->rule='max_length[7]';
		$edit->ctainos->size =9;
		$edit->ctainos->maxlength =7;

		$edit->direccion = new inputField('Direcci&oacute;n','direccion');
		$edit->direccion->rule='max_length[50]';
		$edit->direccion->size =52;
		$edit->direccion->maxlength =50;

		$edit->no_predio = new inputField('Nro. Promedio','no_predio');
		$edit->no_predio->rule='max_length[10]';
		$edit->no_predio->size =12;
		$edit->no_predio->maxlength =10;

		$edit->sector = new dropdownField('sector','sector');
		$edit->sector->options("SELECT codigo,nombre FROM local ORDER BY nombre");

		$edit->tipo_in = new dropdownField('Tipo Inmueble','tipo_in');
		$edit->tipo_in->options("SELECT tipoin,tipoin d FROM tipoin ORDER BY tipoin");

		$edit->no_hab = new inputField('Nro.Habitacion','no_hab');
		$edit->no_hab->rule='max_length[11]';
		$edit->no_hab->size =13;
		$edit->no_hab->maxlength =11;

		$edit->clase = new dropdownField('clase','clase');
		$edit->clase->options("SELECT codigo,nombre FROM claseo ORDER BY nombre");

		$edit->tipo = new inputField('Tipo','tipo');
		$edit->tipo->rule='max_length[1]';
		$edit->tipo->size =3;
		$edit->tipo->maxlength =1;

		$edit->monto = new inputField('Monto','monto');
		$edit->monto->rule='max_length[8]';
		$edit->monto->size =10;
		$edit->monto->maxlength =8;

		$edit->registrado = new inputField('Registrado','registrado');
		$edit->registrado->rule='max_length[1]';
		$edit->registrado->size =3;
		$edit->registrado->maxlength =1;

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
		$mSQL="CREATE TABLE `inmueble` (
		`id` int(11) NOT NULL DEFAULT '0',
		`contribu` char(6) DEFAULT NULL,
		`ctainos` char(7) DEFAULT NULL,
		`direccion` char(50) DEFAULT NULL,
		`no_predio` char(10) DEFAULT NULL,
		`sector` char(2) DEFAULT NULL,
		`tipo_in` char(25) DEFAULT NULL,
		`no_hab` int(11) DEFAULT NULL,
		`clase` char(1) DEFAULT NULL,
		`tipo` char(1) DEFAULT NULL,
		`monto` double DEFAULT NULL,
		`registrado` char(1) DEFAULT NULL,
		`deuda` double DEFAULT NULL,
		`enero` double DEFAULT NULL,
		`febrero` double DEFAULT NULL,
		`marzo` double DEFAULT NULL,
		`abril` double DEFAULT NULL,
		`mayo` double DEFAULT NULL,
		`junio` double DEFAULT NULL,
		`julio` double DEFAULT NULL,
		`agosto` double DEFAULT NULL,
		`septiembre` double DEFAULT NULL,
		`octubre` double DEFAULT NULL,
		`noviembre` double DEFAULT NULL,
		`diciembre` double DEFAULT NULL,
		`deudacan` double DEFAULT NULL,
		`total` double DEFAULT NULL,
		`agua` double DEFAULT NULL,
		PRIMARY KEY (`id`)
	  ) ENGINE=MyISAM DEFAULT CHARSET=utf8";
		$this->db->simple_query($mSQL);
		
		$query="alter table inmueble add column  `recibo` int(11) DEFAULT NULL";
		$this->db->simple_query($mSQL);
		$query="alter table inmueble add column  `codigo` char(6) DEFAULT NULL";
		$this->db->simple_query($mSQL);
		$query="alter table inmueble add column  `cedula` varchar(50) DEFAULT NULL";
		$this->db->simple_query($mSQL);
		$query="alter table inmueble add column `nombre` text";
		$this->db->simple_query($mSQL);
	}

}
?>
