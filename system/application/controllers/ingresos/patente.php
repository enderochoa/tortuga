<?php
class patente extends Controller {
	var $titp='Patentes';
	var $tits='Patente';
	var $url ='ingresos/patente/';
	function patente(){
		parent::Controller();
		$this->load->library("rapyd");
		//$this->datasis->modulo_id(216,1);
	}
	function index(){
		redirect($this->url."filteredgrid");
	}
	function filteredgrid(){
		$this->rapyd->load('datafilter','datagrid');

		$filter = new DataFilter($this->titp);
		$filter->db->from('patente');
		$filter->db->select(array('patente.archivo','patente.cedula','patente.id','patente.razon','patente.dir_neg','patente.local','patente.negocio','patente.observa','patente.repreced','patente.contribu','patente.repre','patente.nro','negocio.nombre n_nombre','local.nombre l_nombre','fexpedicion','fvencimiento'));
		$filter->db->join('negocio','patente.negocio=negocio.codigo','LEFT');
		$filter->db->join('local'  ,'patente.local=local.codigo'    ,'LEFT');

		$filter->id = new inputField('Ref','id');
		$filter->id->rule      ='max_length[11]';
		$filter->id->size      =13;
		$filter->id->maxlength =11;
		
		$filter->nro = new inputField('Patente','nro');
		$filter->nro->rule      ='max_length[50]';
		$filter->nro->size      =52;
		$filter->nro->maxlength =50;

		$filter->rif = new inputField('Rif','rif');
		$filter->rif->db_name   ='patente.cedula';
		$filter->rif->rule      ='max_length[50]';
		$filter->rif->size      =52;
		$filter->rif->maxlength =50;

		$filter->razon = new inputField('Razon Social','razon');
		$filter->razon->rule      ='max_length[50]';
		$filter->razon->size      =52;
		$filter->razon->maxlength =50;

		$filter->local = new dropdownField('Localizaci&oacute;n','local');
		$filter->local->db_name='patente.local';
		$filter->local->option('','');     
		$filter->local->options("SELECT codigo,nombre FROM local ORDER BY nombre");     

		$filter->negocio = new dropdownField('Negocio','negocio');
		$filter->negocio->db_name='patente.negocio';
		$filter->negocio->option('','');     
		$filter->negocio->options("SELECT codigo,nombre FROM negocio ORDER BY nombre");     
		$filter->negocio->style='width:400px';
	
		$filter->buttons('reset', 'search');
		$filter->build();

		$uri = anchor($this->url.'dataedit/show/<raencode><#id#></raencode>','<#id#>');
		$uri_2 = anchor($this->url.'dataedit/S/create/<#id#>','Duplicar');

		$grid = new DataGrid('');
		$grid->order_by('id');
		$grid->per_page = 40;

		$grid->column_orderby('Ref.'                     ,"$uri"                                                 ,'id'            ,'align="left"'  );
		$grid->column_orderby('Archivo'                  ,"archivo"                                              ,'archivo'       ,'align="left"'  );
		$grid->column_orderby('Patente'                  ,"nro"                                                  ,'nro'           ,'align="left"'  );
		$grid->column_orderby('Rif'                      ,"cedula"                                               ,'cedula'        ,'align="left"'  );
		$grid->column_orderby('Raz&oacute;n Social'      ,"razon"                                                ,'razon'         ,'align="left"'  );
		$grid->column_orderby('Direcci&oacute;n Negocio' ,"dir_neg"                                              ,'dir_neg'       ,'align="left"'  );
		$grid->column_orderby('Localizaci&oacute;n'      ,"l_nombre"                                             ,'l_nombre'      ,'align="left"'  );
		$grid->column_orderby('Negocio'                  ,"n_nombre"                                             ,'n_nombre'      ,'align="left"'  );
		$grid->column_orderby('Observaci&oacute;n'       ,"observa"                                              ,'observa'       ,'align="left"'  );
		$grid->column_orderby('Expedicion'               ,"<dbdate_to_human><#fexpedicion#></dbdate_to_human>"   ,'fexpedicion'   ,'align="left"'  );
		$grid->column_orderby('Vencimiento'              ,"<dbdate_to_human><#fvencimiento#></dbdate_to_human>"  ,'fvencimiento'  ,'align="left"'  );
		$grid->column("Duplicar"                         ,$uri_2                                                                  ,"align='center'");
		
		$grid->add($this->url.'dataedit/create');
		$grid->build();
		//echo $grid->db->last_query();

		$data['filtro']  = $filter->output;
		$data['content'] = $grid->output;
		$data['head']    = script('jquery.js').script('jquery-ui.js').script('plugins/jquery.numeric.pack.js').script('plugins/jquery.floatnumber.js').script('plugins/jquery.meiomask.js').style('vino/jquery-ui.css').$this->rapyd->get_head();
		$data['title']   = $this->titp;
		$this->load->view('view_ventanas', $data);

	}
	function dataedit($duplicar='S',$status='',$numero=''){
		$this->rapyd->load('dataedit','dataobject');

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
				'rifci'    =>'repreced' ,
				'nombre'   =>'repre'    ,
				'direccion'=>'dir_neg'  ,
				'telefono' =>'telefonos'
				),
				'script'  =>array('cal_nacionali()'),
				'titulo'  =>'Buscar Contribuyente');
			
		$bCONTRIBU=$this->datasis->modbus($mCONTRIBU);
		
		$do = new DataObject("patente");
		if($status=="create" && !empty($numero) && $duplicar=='S'){
			$do->load($numero);
			$do->set('id', '');
			$do->set('fexpedicion' , date('Y').'-01-01');
			$do->set('fvencimiento', date('Y').'-12-31');
			$do->pk    =array('id'=>'');
		}

		$edit = new DataEdit($this->tits, $do);

		$edit->back_url = site_url($this->url."filteredgrid");

		$edit->post_process('insert','_post_insert');
		$edit->post_process('update','_post_update');
		$edit->post_process('delete','_post_delete');

		$edit->id = new inputField('Ref.','id');
		$edit->id->rule='max_length[11]';
		$edit->id->size =13;
		$edit->id->maxlength =11;
		$edit->id->mode = 'autohide';
		$edit->id->when=array("show","modify");
		
		$edit->contribu = new inputField('Contribuyente','contribu');
		$edit->contribu->rule      ='max_length[25]';
		$edit->contribu->size      =27;
		$edit->contribu->maxlength =25;
		$edit->contribu->append($bCONTRIBU);

		$edit->tarjeta = new inputField('Tarjeta','tarjeta');
		$edit->tarjeta->rule='max_length[6]';
		$edit->tarjeta->size =8;
		$edit->tarjeta->maxlength =6;

		$edit->licencia = new inputField('Licencia','licencia');
		$edit->licencia->rule='max_length[6]';
		$edit->licencia->size =8;
		$edit->licencia->maxlength =6;
		
		$edit->nro = new inputField('Patente','nro');
		$edit->nro->rule='max_length[6]';
		$edit->nro->size =8;
		$edit->nro->maxlength =6;

		$edit->razon = new inputField('Raz&oacute;n Social','razon');
		$edit->razon->rule='required';
		$edit->razon->size =52;
		
		$edit->cedula = new inputField('Rif','cedula');
		$edit->cedula->rule='max_length[50]';
		$edit->cedula->size =52;
		$edit->cedula->maxlength =50;
		
		$edit->repre = new inputField('Representante','repre');
		$edit->repre->rule='max_length[50]|required';
		$edit->repre->size =52;
		$edit->repre->maxlength =50;
		
		$edit->repreced = new inputField('C.I. Representante','repreced');
		$edit->repreced->rule='max_length[50]|required';
		$edit->repreced->size =52;
		$edit->repreced->maxlength =50;

		$edit->dir_neg = new textAreaField('Direcci&oacute;n Negocio','dir_neg');
		$edit->dir_neg->rows      =2 ;
		$edit->dir_neg->size      =60;

		$edit->local = new dropdownField('Localizaci&oacute;n','local');
		$edit->local->options("SELECT codigo,nombre FROM local ORDER BY nombre");

		$edit->negocio = new dropdownField('Negocio','negocio');
		$edit->negocio->options("SELECT codigo,nombre FROM negocio ORDER BY nombre");
		$edit->negocio->style='width:600px';

		$edit->objeto = new textAreaField('Objeto','objeto');
		$edit->objeto->rows =2;
		$edit->objeto->cols =40;

		$edit->observa = new textAreaField('Observaci&oacute;n','observa');
		$edit->observa->rows =2;
		$edit->observa->cols =40;

		$edit->clase = new dropdownField('Clase','clase');
		$edit->clase->options("SELECT codigo,nombre FROM claseo ORDER BY nombre");

		$edit->archivo = new inputField('Archivo','archivo');
		$edit->archivo->rule='max_length[30]';
		$edit->archivo->size      =32;
		$edit->archivo->maxlength =15;
		
		$edit->telefonos = new inputField('Telefono','telefonos');
		$edit->telefonos->size      =32;
		$edit->telefonos->maxlength =15;
		
		$edit->utribu = new inputField('U. Tributaria','utribu');
		$edit->utribu->size      =32;
		$edit->utribu->maxlength =15;
		
		$edit->actual = new inputField('Actual','actual');
		$edit->actual->rule     ='numeric';
		$edit->actual->size     =15;
		$edit->actual->css_class='inputnum';
		
		$edit->ajustado = new inputField('Ajustado','ajustado');
		$edit->ajustado->rule     ='numeric';
		$edit->ajustado->size     =15;
		$edit->ajustado->css_class='inputnum';
		
		$edit->neto = new inputField('Neto','neto');
		$edit->neto->rule     ='numeric';
		$edit->neto->size     =15;
		$edit->neto->css_class='inputnum';
		
		$edit->fexpedicion = new dateField('F Expedici&oacute;n','fexpedicion');
		$edit->fexpedicion->rule='chfecha';
		$edit->fexpedicion->size =10;
		$edit->fexpedicion->maxlength =8;
		$edit->fexpedicion->value=date('Y').'-01-01';
		
		$edit->fvencimiento = new dateField('F Vencimiento','fvencimiento');
		$edit->fvencimiento->rule='chfecha';
		$edit->fvencimiento->size =10;
		$edit->fvencimiento->maxlength =8;
		$edit->fvencimiento->value=date('Y').'-12-31';
		
		$edit->fotorga = new dateField('F Otorgamiento','fotorga');
		$edit->fotorga->rule='chfecha|required';
		$edit->fotorga->size =10;
		$edit->fotorga->maxlength =8;
		
		$edit->factu = new dateField('F Actualizacion','factu');
		$edit->factu->rule='chfecha|required';
		$edit->factu->size =10;
		$edit->factu->maxlength =8;
		
		$edit->cantfol = new inputField('Cantidad de Folios','cantfol');
		$edit->cantfol->rule     ='numeric';
		$edit->cantfol->size     =15;
		$edit->cantfol->css_class='inputnum';

		$edit->buttons('add','modify', 'save', 'undo', 'delete', 'back');
		$edit->build();
		
		$smenu['link']=barra_menu('810');
		$data['content'] = $edit->output;
		$data['smenu']   = $this->load->view('view_sub_menu', $smenu,true); 
		//$data['content'] = $edit->output;
		$data['head']    = script('jquery.js').script('jquery-ui.js').script('plugins/jquery.numeric.pack.js').script('plugins/jquery.floatnumber.js').script('plugins/jquery.meiomask.js').style('vino/jquery-ui.css').$this->rapyd->get_head();
		$data['title']   = $this->tits;
		$this->load->view('view_ventanas', $data);
	}

	function _post_insert($do){
		$primary =implode(',',$do->pk);
		$id      =$do->get('id');
		logusu($do->table,"Creo $this->tits $primary ");
		redirect($this->url.'dataedit/show/'.$id);
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
		$mSQL="
		CREATE TABLE `patente` (
			`id` INT(11) NOT NULL AUTO_INCREMENT,
			`contribu` VARCHAR(6) NOT NULL,
			`tarjeta` CHAR(6) NULL DEFAULT NULL,
			`licencia` CHAR(6) NULL DEFAULT NULL,
			`nombre_pro` CHAR(25) NULL DEFAULT NULL,
			`cedula` CHAR(8) NULL DEFAULT NULL,
			`nacionali` CHAR(10) NULL DEFAULT NULL,
			`razon` CHAR(50) NULL DEFAULT NULL,
			`dir_neg` CHAR(50) NULL DEFAULT NULL,
			`dir_pro` CHAR(50) NULL DEFAULT NULL,
			`telefonos` CHAR(15) NULL DEFAULT NULL,
			`capital` DOUBLE NULL DEFAULT NULL,
			`monto` DOUBLE NULL DEFAULT NULL,
			`fecha_es` DATE NULL DEFAULT NULL,
			`oficio` CHAR(30) NULL DEFAULT NULL,
			`local` CHAR(2) NULL DEFAULT NULL,
			`negocio` CHAR(5) NULL DEFAULT NULL,
			`registrado` CHAR(1) NULL DEFAULT NULL,
			`deuda` DOUBLE NULL DEFAULT NULL,
			`enero` DOUBLE NULL DEFAULT NULL,
			`febrero` DOUBLE NULL DEFAULT NULL,
			`marzo` DOUBLE NULL DEFAULT NULL,
			`abril` DOUBLE NULL DEFAULT NULL,
			`mayo` DOUBLE NULL DEFAULT NULL,
			`junio` DOUBLE NULL DEFAULT NULL,
			`julio` DOUBLE NULL DEFAULT NULL,
			`agosto` DOUBLE NULL DEFAULT NULL,
			`septiembre` DOUBLE NULL DEFAULT NULL,
			`octubre` DOUBLE NULL DEFAULT NULL,
			`noviembre` DOUBLE NULL DEFAULT NULL,
			`diciembre` DOUBLE NULL DEFAULT NULL,
			`deudacan` DOUBLE NULL DEFAULT NULL,
			`total` DOUBLE NULL DEFAULT NULL,
			`observa` CHAR(20) NULL DEFAULT NULL,
			`clase` CHAR(1) NULL DEFAULT NULL,
			`tipo` CHAR(1) NULL DEFAULT NULL,
			`catastro` CHAR(10) NULL DEFAULT NULL,
			`publicidad` CHAR(30) NULL DEFAULT NULL,
			`recibo` INT(11) NULL DEFAULT NULL,
			`declaracion` DECIMAL(19,2) NULL DEFAULT NULL,
			`repre` TEXT NULL,
			`repreced` TEXT NULL,
			`expclasi` TEXT NULL,
			`exphor` TEXT NULL,
			`kardex` TEXT NULL,
			`nro` TEXT NULL,
			PRIMARY KEY (`id`)
		)
		COLLATE='utf8_general_ci'
		ENGINE=MyISAM
		AUTO_INCREMENT=1;
		";
		$this->db->simple_query($mSQL);
		$mSQL="ALTER TABLE `patente`  CHANGE COLUMN `repre` `repre` TEXT NULL DEFAULT ''";
		$this->db->simple_query($mSQL);
		$mSQL="ALTER TABLE `patente`  ADD COLUMN `repreced` TEXT NULL DEFAULT ''";
		$this->db->simple_query($mSQL);
		$mSQL="ALTER TABLE `patente`  ADD COLUMN `expclasi` TEXT NULL DEFAULT '' AFTER `repreced`,  ADD COLUMN `exphor` TEXT NULL DEFAULT '' AFTER `expclasi`";
		$this->db->simple_query($mSQL);
		$mSQL="ALTER TABLE `patente`  ADD COLUMN `kardex` TEXT NULL AFTER `exphor`";
		$this->db->simple_query($mSQL);
		$query="ALTER TABLE `patente` 	ADD COLUMN `actual` DECIMAL(19,2) NULL DEFAULT '0'  ";
		$this->db->simple_query($mSQL);
		$query="ALTER TABLE `patente` 	ADD COLUMN `ajustado` DECIMAL(19,2) NULL DEFAULT '0'";
		$this->db->simple_query($mSQL);
		$query="ALTER TABLE `patente` 	ADD COLUMN `neto` DECIMAL(19,2) NULL DEFAULT '0'    ";
		$this->db->simple_query($mSQL);
		$query="ALTER TABLE `patente` CHANGE COLUMN `observa` `observa` TEXT(20) NULL DEFAULT NULL";
		$this->db->simple_query($mSQL);
		$query="ALTER TABLE `patente` 	ADD COLUMN `objeto` TEXT NULL AFTER `neto`";
		$this->db->simple_query($mSQL);
		$query="ALTER TABLE `patente` ADD COLUMN `archivo` TEXT NULL";
		$this->db->simple_query($mSQL);
		$query="ALTER TABLE `patente` ADD COLUMN `utribu` VARCHAR(50) NULL";
		$this->db->simple_query($mSQL);
		$query="ALTER TABLE `patente` CHANGE COLUMN `cedula` `cedula` VARCHAR(20) NULL DEFAULT NULL";
		$this->db->simple_query($mSQL);
		$query="ALTER TABLE `patente` ADD COLUMN `fexpedicion` DATE NULL AFTER `nro`";
		$this->db->simple_query($mSQL);
		$query="ALTER TABLE `patente`	ADD COLUMN `fvencimiento` DATE NULL AFTER `fexpedicion`";
		$this->db->simple_query($mSQL);
		$query="ALTER TABLE `patente` ADD COLUMN `fotorga` DATE NULL DEFAULT NULL"; 
		$this->db->simple_query($mSQL);
		$query="ALTER TABLE `patente` ADD COLUMN `factu` DATE NULL DEFAULT NULL  ";
		$this->db->simple_query($mSQL);
		$query="ALTER TABLE `patente` ADD COLUMN `cantfol` INT NULL DEFAULT NULL  ";
		$this->db->simple_query($mSQL);
		$mSQL="ALTER TABLE `patente` CHANGE COLUMN `dir_neg` `dir_neg` TEXT NULL DEFAULT NULL AFTER `razon`  ";
		$this->db->simple_query($mSQL);
		$mSQL="ALTER TABLE `patente` CHANGE COLUMN `dir_pro` `dir_pro` TEXT NULL DEFAULT NULL AFTER `dir_neg`";
		$this->db->simple_query($mSQL);
		$mSQL="ALTER TABLE `patente` CHANGE COLUMN `razon` `razon` TEXT NULL DEFAULT NULL AFTER `nacionali`";
		$this->db->simple_query($mSQL);
	}
}
?>
