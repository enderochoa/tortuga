<?php
class r_inmueble extends Controller {
	var $titp='Inmuebles';
	var $tits='Inmueble';
	var $url ='recaudacion/r_inmueble/';
	function r_inmueble(){
		parent::Controller();
		$this->load->library("rapyd");
		$this->datasis->modulo_id(401,1);
	}
	function index(){
		redirect($this->url."filteredgrid");
	}
	function filteredgrid(){
		$this->rapyd->load('datafilter','datagrid');
		
		$modbus=array(
			'tabla'   =>'r_contribu',
			'columnas'=>array(
				'id'    =>'Ref.',
				'rifci' =>'Rif/CI',
				'nombre'=>'Nombre'
				),
			'filtro'  =>array(
				'id'    =>'Ref.',
				'rifci' =>'Rif/CI',
				'nombre'=>'Nombre'
			),
			'retornar'=>array(
				'id'=>'id_contribu'
			),
			'titulo'  =>'Buscar Contribuyente'
		);

		$button  = $this->datasis->modbus($modbus);

		$filter = new DataFilter($this->titp, 'r_inmueble');
		$filter->db->select(array('r_inmueble.id','r_inmueble.catastro','r_inmueble.dir1','r_inmueble.dir2','r_inmueble.dir3','r_inmueble.dir4','mt2',"IF(techo='A','ZINC',IF(techo='B','PLATABANDA',IF(techo='C','2 PLANTAS',IF(techo='D','RANCHO',techo)))) techo","r_contribu.nombre","r_contribu.rifci"));
		$filter->db->join('r_contribu','r_inmueble.id_contribu=r_contribu.id','LEFT');
		//$filter->db->join('vi_parroquia','r_inmueble.id_parroquia=vi_parroquia.id','LEFT');
		//$filter->db->join('r_zona','r_inmueble.id_zona=r_zona.id','LEFT');
		
		$filter->id = new inputField('Ref.','id');
		$filter->id->rule      ='max_length[11]';
		$filter->id->size      =13;
		$filter->id->maxlength =11;
		$filter->id->db_name='r_inmueble.id';
		$filter->id->clause='where';
		$filter->id->operator='=';

		$filter->id_contribu = new inputField('Ref. Contribu','id_contribu');
		$filter->id_contribu->rule      ='max_length[11]';
		$filter->id_contribu->size      =5;
		$filter->id_contribu->maxlength =11;
		$filter->id_contribu->append($button);
		$filter->id_contribu->clause    ='where';
		$filter->id_contribu->operator  ='=';
		
		$filter->rifci = new inputField('R.I.F./C.I','rifci');
		$filter->rifci->rule      ='max_length[11]';
		$filter->rifci->size      =13;
		$filter->rifci->maxlength =11;
		$filter->rifci->db_name='r_contribu.rifci';
		
		$filter->catastro = new inputField('Codigo Catastral','catastro');
		$filter->catastro->rule='max_length[255]';
		$filter->catastro->size =20;

		$filter->techo = new dropDownField('Techo','techo');
		$filter->techo->option("","");
		$filter->techo->option("A","ZINC");
		$filter->techo->option("B","PLATABANDA");
		$filter->techo->option("C","2 PLANTAS");
		$filter->techo->option("D","RANCHO");
		$filter->techo->style='width:200px';

		$filter->mt2 = new inputField('mt2','mt2');
		$filter->mt2->rule      ='max_length[19]|numeric';
		$filter->mt2->css_class ='inputnum';
		$filter->mt2->size      =21;
		$filter->mt2->maxlength =19;

		$filter->id_parroquia = new dropDownField('Parroquia','id_parroquia');
		$filter->id_parroquia->option('','');
		$filter->id_parroquia->options("SELECT id,nombre FROM vi_parroquia ORDER BY nombre");
		$filter->id_parroquia->group = "Datos De Ubicacion";

		$filter->id_zona = new dropDownField('Zona','id_zona');
		$filter->id_zona->option('','');
		$filter->id_zona->options("SELECT id,descrip FROM r_zona ORDER BY descrip");
		$filter->id_zona->group = "Datos De Ubicacion";

		$filter->dir1 = new inputField('Direcci&oacute;n 1','dir1');
		$filter->dir1->rule='max_length[255]';
		$filter->dir1->size =40;
		$filter->dir1->maxlength =255;
		$filter->dir1->append("Urbanizacion, Barrio, Sector");
		$filter->dir1->group = "Datos De Ubicacion";
		$filter->dir1->db_name="r_inmueble.dir1";

		$filter->dir2 = new inputField('Direcci&oacute;n 2','dir2');
		$filter->dir2->rule='max_length[255]';
		$filter->dir2->size =40;
		$filter->dir2->maxlength =255;
		$filter->dir2->append("Calle, avenida");
		$filter->dir2->group = "Datos De Ubicacion";

		$filter->dir3 = new inputField('Direcci&oacute;n 3','dir3');
		$filter->dir3->rule='max_length[255]';
		$filter->dir3->size =40;
		$filter->dir3->maxlength =255;
		$filter->dir3->append("Con Calle o avenida");
		$filter->dir3->group = "Datos De Ubicacion";

		$filter->dir4 = new inputField('Direcci&oacute;n 4','dir4');
		$filter->dir4->rule='max_length[255]';
		$filter->dir4->size =40;
		$filter->dir4->maxlength =255;
		$filter->dir4->append("Casa #, o apto #");
		$filter->dir4->group = "Datos De Ubicacion";

		$filter->buttons('reset', 'search');
		$filter->build();

		$uri = anchor($this->url.'dataedit/show/<raencode><#id#></raencode>','<#id#>');

		$grid = new DataGrid('');
		$grid->order_by('r_inmueble.id','desc');
		$grid->per_page = 40;

		$grid->column_orderby('Ref.'           ,"$uri"                               ,'id','align="left"');
		$grid->column_orderby('Cod. Catastro'  ,"catastro"                           ,'catastro','align="left"');
		$grid->column_orderby('Direccion 1'    ,"dir1"                               ,'dir1','align="left"');
		$grid->column_orderby('Direccion 2'    ,"dir2"                               ,'dir2','align="left"');
		$grid->column_orderby('Direccion 3'    ,"dir3"                               ,'dir3','align="left"');
		$grid->column_orderby('Direccion 4'    ,"dir4"                               ,'dir4','align="left"');
		$grid->column_orderby('Techo'          ,"techo"                              ,'techo','align="left"');
		$grid->column_orderby('Mts2'           ,"<nformat><#mt2#></nformat>"         ,'mt2','align="right"');
		$grid->column_orderby('Contribuyente'  ,"nombre"                             ,'techo','align="left"');
		$grid->column_orderby('Rif/CI'         ,"rifci"                              ,'rifci','align="left"');
		

		$grid->add($this->url.'dataedit/create');
		$grid->build();
		//echo $grid->db->last_query();

		$data['filtro']  = $filter->output;
		$data['content'] = $grid->output;
		$data['head']    = $this->rapyd->get_head().script('jquery.js');
		$data['title']   = $this->titp;
		$this->load->view('view_ventanas', $data);

	}
	function dataedit($status='',$id_contribu=null){
		$this->rapyd->load('dataedit','dataobject');
		
		$script='
			$(".inputnumc").numeric(".");
			$(document).ready(function(){
				if("'.$status.'"=="create" && "'.$id_contribu.'".length >0){					
					$.post("'.site_url('recaudacion/r_contribu/damecontribuporid').'",{ id:"'.$id_contribu.'" },function(data){
							contribu=jQuery.parseJSON(data);
							$( "#nombrep").val( contribu[0].nombre );
							$( "#rifcip").val(  contribu[0].rifci );
						
					});
				}
				
				$.post("'.site_url('recaudacion/r_contribu/autocompleteui').'",{ partida:"" },function(data){
					sprv=jQuery.parseJSON(data);
					jQuery.each(sprv, function(i, val) {
						val.label=val.rifci;
						
					});
					
					
					$("#rifcip").autocomplete({
						//autoFocus: true,
						delay: 0,
						minLength: 3,
						source: sprv,
						focus: function( event, ui ){
							return false;
						},
						select: function( event, ui ){
							$( "#nombrep").val( ui.item.nombre );
							$( "#rifcip").val( ui.item.rifci );
							$( "#id_contribu").val( ui.item.id );
							return false;
						}
					})
					.data( "autocomplete" )._renderItem = function( ul, item ) {
						return $( "<li></li>" )
						.data( "item.autocomplete", item )
						.append( "<a>" + item.rifci + " "  + item.nombre + "</a>" )
						.appendTo( ul );
					};
				});
			});
		';
		
		$modbus=array(
			'tabla'   =>'r_contribu',
			'columnas'=>array(
				'id'    =>'Ref.',
				'rifci' =>'Rif/CI',
				'nombre'=>'Nombre'
				),
			'filtro'  =>array(
				'id'    =>'Ref.',
				'rifci' =>'Rif/CI',
				'nombre'=>'Nombre'
			),
			'retornar'=>array(
				'id'=>'id_contribu',
				'nombre'=>'nombrep',
			),
			'titulo'  =>'Buscar Contribuyente',
		);

		$button  = $this->datasis->modbus($modbus);
		
		$modbusnegocio=array(
			'tabla'   =>'r_negocio',
			'columnas'=>array(
				'id'       =>'Ref.',
				'descrip'  =>'Descripcion',
				'monto'    =>'Monto',
				'monto2'   =>'Monto2'  ,
				'aforo'    =>'Aforo'   ,
				'mintribu' =>'Minimo Tributable'
				),
			'filtro'  =>array(
				'id'       =>'Ref.',
				'descrip'  =>'Descripcion',
				'monto'    =>'Monto',
				'monto2'   =>'Monto2'  ,
				'aforo'    =>'Aforo'   ,
				'mintribu' =>'Minimo Tributable'
			),
			'retornar'=>array(
				'id'=>'id_negocio',
				'descrip'=>'negociop'
			),
			'titulo'  =>'Buscar Negocio'
		);

		$buttonnegocio  = $this->datasis->modbus($modbusnegocio);
		
		if($id_contribu && $status=='create'){
			$id_contribue = $this->db->escape($id_contribu);
			$CONTRIBU=$this->datasis->damerow("SELECT id_parroquia,id_zona,dir1,dir2,dir3,dir4 FROM r_contribu WHERE id=$id_contribue");
		}

		$do = new DataObject("r_inmueble");
		//$do->pointer('r_negocio' ,'r_inmueble.id_negocio=r_negocio.id',"r_negocio.descrip negociop","LEFT");
		//$do->pointer('r_contribu' ,'r_inmueble.id_contribu=r_contribu.id',"r_contribu.nombre nombrep,r_contribu.rifci rifcip","LEFT");

		$edit = new DataEdit($this->tits, $do);

		$edit->back_url = site_url($this->url."filteredgrid");

		$edit->post_process('insert','_post_insert');
		$edit->post_process('update','_post_update');
		$edit->post_process('delete','_post_delete');
		
		$edit->script($script,"create");
		$edit->script($script,"modify");

		$edit->id = new inputField('Ref','id');
		$edit->id->rule='max_length[11]';
		$edit->id->size =13;
		$edit->id->maxlength =11;
		$edit->id->mode='autohide';
		$edit->id->when=array('show','modify');

		$edit->id_contribu = new inputField('Contribuyente','id_contribu');
		$edit->id_contribu->rule='required';
		$edit->id_contribu->size =5;
		$edit->id_contribu->readonly=true;
		if($id_contribu && $status=='create')
		$edit->id_contribu->insertValue=$id_contribu;
		
		$edit->nombrep = new inputField('c','nombrep');
		$edit->nombrep->size =40;
		$edit->nombrep->readonly=true;
		$edit->nombrep->pointer =true;
		$edit->nombrep->in='id_contribu';
		$edit->nombrep->append($button);
		
		$edit->rifcip = new inputField('R.I.F./C.I.','rifcip');
		$edit->rifcip->size =40;
		//$edit->rifcip->readonly=true;
		$edit->rifcip->pointer =true;
		
		$edit->catastro = new inputField('Codigo Catastral','catastro');
		$edit->catastro->rule='max_length[255]';
		$edit->catastro->size =20;
		
		$edit->tipoi = new dropDownField('Tipo','tipoi');
		$edit->tipoi->rule  ='required';
		$edit->tipoi->option("","");
		$edit->tipoi->option("V","Vivienda");
		$edit->tipoi->option("I","Terreno" );
		$edit->tipoi->option("C","Comercio");
		$edit->tipoi->option("N","Industria");
		
		$edit->id_clase = new dropDownField('Clase','id_clase');
		//$edit->id_clase->rule  ='required';
		$edit->id_clase->option("","");
		$edit->id_clase->options("SELECT id,nombre FROM ri_clase ORDER BY nombre");
		
		$edit->id_clasea = new dropDownField('Clase Aseo','id_clasea');
		$edit->id_clasea->option("","");
		$edit->id_clasea->options("SELECT id,nombre FROM ri_clasea ORDER BY nombre");

		$edit->techo = new dropDownField('Techo','techo');
		//$edit->techo->rule='required';
		$edit->techo->option("","");
		$edit->techo->option("A","ZINC");
		$edit->techo->option("B","PLATABANDA");
		$edit->techo->option("C","2 PLANTAS");
		$edit->techo->option("D","RANCHO");
		$edit->techo->style='width:200px';

		$edit->mt2 = new inputField('Mts2','mt2');
		$edit->mt2->rule='max_length[19]|numeric';
		$edit->mt2->css_class='inputnum';
		$edit->mt2->size =10;
		$edit->mt2->maxlength =19;

		$edit->monto = new inputField('Monto Inmueble','monto');
		$edit->monto->rule='max_length[19]|numeric';
		$edit->monto->css_class='inputnum';
		$edit->monto->size =15;
		$edit->monto->maxlength =19;
		
		$edit->id_negocio = new inputField('Negocio','id_negocio');
		//$edit->id_negocio->option('','');
		//$edit->id_negocio->options("SELECT id,descrip FROM r_negocio ORDER BY descrip");
		$edit->id_negocio->size='5';
		$edit->id_negocio->append($buttonnegocio);
		$edit->id_negocio->readonly=true;
		
		$edit->negociop = new inputField('Negocio','negociop');
		$edit->negociop->size='60';
		$edit->negociop->readonly=true;
		$edit->negociop->pointer=true;
		$edit->negociop->in      ="id_negocio";

		$edit->id_parroquia = new dropDownField('Parroquia','id_parroquia');
		$edit->id_parroquia->option("","");
		$edit->id_parroquia->options("SELECT id,nombre FROM vi_parroquia ORDER BY nombre");
		$edit->id_parroquia->group = "Datos De Ubicacion";
		if($id_contribu && $status=='create')
		$edit->id_parroquia->insertValue=$CONTRIBU['id_parroquia'];
		if($this->datasis->traevalor('R_INMUEBLE_OBLIGA_PARROQUIA','N')=='S')
		$edit->id_parroquia->rule      = "required";

		$edit->id_zona = new dropDownField('Zona','id_zona');
		$edit->id_zona->option("","");
		$edit->id_zona->options("SELECT id,descrip FROM r_zona ORDER BY descrip");
		$edit->id_zona->group = "Datos De Ubicacion";
		if($id_contribu && $status=='create')
		$edit->id_zona->insertValue=$CONTRIBU['id_zona'];
		if($this->datasis->traevalor('R_INMUEBLE_OBLIGA_ZONA','N')=='S')
		$edit->id_zona->rule      = "required";
		

		$edit->dir1 = new inputField('Direcci&oacute;n 1','dir1');
		$edit->dir1->rule='max_length[255]';
		$edit->dir1->size =40;
		$edit->dir1->maxlength =255;
		$edit->dir1->append("Urbanizacion, Barrio, Sector");
		$edit->dir1->group = "Datos De Ubicacion";
		if($id_contribu && $status=='create')
		$edit->dir1->insertValue=$CONTRIBU['dir1'];
		if($this->datasis->traevalor('R_INMUEBLE_OBLIGA_DIR1','N')=='S')
		$edit->dir1->rule      = "required";

		$edit->dir2 = new inputField('Direcci&oacute;n 2','dir2');
		$edit->dir2->rule='max_length[255]';
		$edit->dir2->size =40;
		$edit->dir2->maxlength =255;
		$edit->dir2->append("Calle, avenida");
		$edit->dir2->group = "Datos De Ubicacion";
		if($id_contribu && $status=='create')
		$edit->dir2->insertValue=$CONTRIBU['dir2'];
		if($this->datasis->traevalor('R_INMUEBLE_OBLIGA_DIR2','N')=='S')
		$edit->dir2->rule      = "required";

		$edit->dir3 = new inputField('Direcci&oacute;n 3','dir3');
		$edit->dir3->rule='max_length[255]';
		$edit->dir3->size =40;
		$edit->dir3->maxlength =255;
		$edit->dir3->append("Con Calle o avenida");
		$edit->dir3->group = "Datos De Ubicacion";
		if($id_contribu && $status=='create')
		$edit->dir3->insertValue=$CONTRIBU['dir3'];
		if($this->datasis->traevalor('R_INMUEBLE_OBLIGA_DIR3','N')=='S')
		$edit->dir3->rule      = "required";

		$edit->dir4 = new inputField('Direcci&oacute;n 4','dir4');
		$edit->dir4->rule='max_length[255]';
		$edit->dir4->size =40;
		$edit->dir4->maxlength =255;
		$edit->dir4->append("Casa #, o apto #");
		$edit->dir4->group = "Datos De Ubicacion";
		if($id_contribu && $status=='create')
		$edit->dir4->insertValue=$CONTRIBU['dir4'];
		if($this->datasis->traevalor('R_INMUEBLE_OBLIGA_DIR4','N')=='S')
		$edit->dir4->rule      = "required";

		$edit->buttons('add','modify', 'save', 'undo', 'delete', 'back');
		$edit->build();
		$data['content'] = $edit->output;
		$data["head"]    = script('jquery.js').script('jquery-ui.js').script('plugins/jquery.numeric.pack.js').script('plugins/jquery.floatnumber.js').script('plugins/jquery.meiomask.js').style('vino/jquery-ui.css').$this->rapyd->get_head();
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
		$mSQL="CREATE TABLE `r_inmueble` (
		  `id` int(11) NOT NULL AUTO_INCREMENT,
		  `id_contribu` int(11) DEFAULT NULL,
		  `id_parroquia` int(11) DEFAULT NULL,
		  `id_zona` int(11) DEFAULT NULL,
		  `techo` char(1) DEFAULT NULL,
		  `mt2` decimal(19,2) DEFAULT '0.00',
		  `monto` decimal(19,2) DEFAULT '0.00',
		  `dir1` varchar(255) DEFAULT NULL,
		  `dir2` varchar(255) DEFAULT NULL,
		  `dir3` varchar(255) DEFAULT NULL,
		  `dir4` varchar(255) DEFAULT NULL,
		  PRIMARY KEY (`id`)
		) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf32";
		$this->db->simple_query($mSQL);
		$query="ALTER TABLE `r_inmueble` ADD COLUMN `catastro` VARCHAR(50) NULL DEFAULT NULL";
		$this->db->simple_query($query);
		$query="ALTER TABLE `r_inmueble` ADD COLUMN `tipoi` VARCHAR(255) NULL DEFAULT NULL";
		$this->db->simple_query($query);
		$query="ALTER TABLE `r_inmueble` 	ADD COLUMN `id_clase` INT NULL DEFAULT NULL";
		$this->db->simple_query($query);
		$query="ALTER TABLE `r_inmueble` ADD COLUMN `id_clasea` INT(11) NULL DEFAULT NULL`";
		$this->db->simple_query($query);
		$query="ALTER TABLE `r_inmueble` 	ADD COLUMN `id_sector` INT(11) NULL DEFAULT NULL AFTER `id_clasea`";
		$this->db->simple_query($query);
		$query="ALTER TABLE `r_inmueble` ADD COLUMN `id_negocio` INT(11) NULL DEFAULT NULL AFTER `id_clasea";
		$this->db->simple_query($query);
		
	}

}
?>
