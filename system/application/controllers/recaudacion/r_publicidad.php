<?php
class R_publicidad extends Controller {
	var $titp='Publicidades';
	var $tits='Publicidad';
	var $url ='/recaudacion/r_publicidad/';
	function R_publicidad(){
		parent::Controller();
		$this->load->library("rapyd");
		//$this->datasis->modulo_id(216,1);
	}
	function index(){
		redirect($this->url."filteredgrid");
	}
	function filteredgrid(){
		$this->rapyd->load('datafilter','datagrid');

		$filter = new DataFilter($this->titp, 'r_publicidad a');
		$filter->db->select(array('vi_parroquia.nombre parroquia','r_zona.descrip zona',"r_contribu.nombre","r_contribu.rifci","a.id","a.id_contribu","a.id_tipo","a.id_parroquia","a.id_zona","a.id_sector","a.dir1","a.dir2","a.dir3","a.dir4","a.alto","a.ancho","b.codigo","b.descrip","b.monto"));
		$filter->db->join('rp_tipos b','a.id_tipo=b.id','LEFT');
		$filter->db->join('r_contribu','a.id_contribu=r_contribu.id','LEFT');
		$filter->db->join('vi_parroquia','a.id_parroquia=vi_parroquia.id','LEFT');
		$filter->db->join('r_zona','a.id_zona=r_zona.id','LEFT');

		$filter->id = new inputField('Id','id');
		$filter->id->rule      ='trim';
		$filter->id->size      =13;
		$filter->id->maxlength =11;

		$filter->id_tipo = new inputField('Id_tipo','id_tipo');
		$filter->id_tipo->rule      ='trim';
		$filter->id_tipo->size      =13;
		$filter->id_tipo->maxlength =11;

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

		$filter->alto = new inputField('Alto','alto');
		$filter->alto->rule      ='trim|numeric';
		$filter->alto->css_class ='inputnum';
		$filter->alto->size      =12;
		$filter->alto->maxlength =10;

		$filter->ancho = new inputField('Ancho','ancho');
		$filter->ancho->rule      ='trim|numeric';
		$filter->ancho->css_class ='inputnum';
		$filter->ancho->size      =12;
		$filter->ancho->maxlength =10;

		$filter->buttons('reset', 'search');
		$filter->build();

		$uri = anchor($this->url.'dataedit/show/<raencode><#id#></raencode>','<#id#>');

		$grid = new DataGrid('');
		$grid->order_by('a.id','desc');
		$grid->per_page = 40;

		$grid->column_orderby('Id'                ,"$uri"                                             ,'id'           ,'align="left"');
		$grid->column_orderby('Id_tipo'           ,"descrip"                                          ,'id_tipo'      ,'align="right"');
		$grid->column_orderby('Id_parroquia'      ,"parroquia"                                        ,'id_parroquia' ,'align="right"');
		$grid->column_orderby('Id_zona'           ,"zona"                                             ,'id_zona'      ,'align="right"');
		$grid->column_orderby('Id_sector'         ,"sector"                                           ,'id_sector'    ,'align="right"');
		$grid->column_orderby('Dir1'              ,"dir1"                                             ,'dir1'         ,'align="left"');
		$grid->column_orderby('Dir2'              ,"dir2"                                             ,'dir2'         ,'align="left"');
		$grid->column_orderby('Dir3'              ,"dir3"                                             ,'dir3'         ,'align="left"');
		$grid->column_orderby('Dir4'              ,"dir4"                                             ,'dir4'         ,'align="left"');
		$grid->column_orderby('Alto'              ,"<nformat><#alto#></nformat>"                      ,'alto'         ,'align="right"');
		$grid->column_orderby('Ancho'             ,"<nformat><#ancho#></nformat>"                     ,'ancho'        ,'align="right"');
		$grid->column_orderby('Dimension'         ,"<nformat><#dimension#></nformat>"                 ,'dimension'    ,'align="right"');

		$grid->add($this->url.'dataedit/create');
		$grid->build();

		$data['filtro']  = $filter->output;
		$data['content'] = $grid->output;
		$data['head']    = $this->rapyd->get_head().script('jquery.js');
		$data['title']   = $this->titp;
		$this->load->view('view_ventanas', $data);

	}
	function dataedit($status='',$id_contribu=null){
		$this->rapyd->load('dataobject','dataedit');

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
		
		if($id_contribu && $status=='create'){
			$id_contribue = $this->db->escape($id_contribu);
			$CONTRIBU=$this->datasis->damerow("SELECT id_parroquia,id_zona,dir1,dir2,dir3,dir4 FROM r_contribu WHERE id=$id_contribue");
		}

		$script='
			
			$(document).ready(function(){
				$(".inputnum").numeric(".");
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
			
		

		$do = new DataObject('r_publicidad');

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

		$edit->id_tipo = new dropDownField('Tipo Publicidad','id_tipo');
		$edit->id_tipo->option("","");
		$edit->id_tipo->options("SELECT id,CONCAT(codigo,' ',descrip) descrip FROM rp_tipos ORDER BY id");
		$edit->id_tipo->rule='required';

		$edit->id_parroquia = new dropDownField('Parroquia','id_parroquia');
		$edit->id_parroquia->option("","");
		$edit->id_parroquia->options("SELECT id,nombre FROM vi_parroquia ORDER BY nombre");
		$edit->id_parroquia->group = "Datos De Ubicacion";
		if($id_contribu && $status=='create')
		$edit->id_parroquia->insertValue=$CONTRIBU['id_parroquia'];
		if($this->datasis->traevalor('R_PUBLICIDAD_OBLIGA_PARROQUIA','N')=='S')
		$edit->id_parroquia->rule      = "required";

		$edit->id_zona = new dropDownField('Zona','id_zona');
		$edit->id_zona->option("","");
		$edit->id_zona->options("SELECT id,descrip FROM r_zona ORDER BY descrip");
		$edit->id_zona->group = "Datos De Ubicacion";
		if($id_contribu && $status=='create')
		$edit->id_zona->insertValue=$CONTRIBU['id_zona'];
		if($this->datasis->traevalor('R_PUBLICIDAD_OBLIGA_ZONA','N')=='S')
		$edit->id_zona->rule      = "required";
		
		$edit->dir1 = new inputField('Direcci&oacute;n 1','dir1');
		$edit->dir1->rule='max_length[255]';
		$edit->dir1->size =40;
		$edit->dir1->maxlength =255;
		$edit->dir1->append("Urbanizacion, Barrio, Sector");
		$edit->dir1->group = "Datos De Ubicacion";
		if($id_contribu && $status=='create')
		$edit->dir1->insertValue=$CONTRIBU['dir1'];
		if($this->datasis->traevalor('R_PUBLICIDAD_OBLIGA_DIR1','N')=='S')
		$edit->dir1->rule      = "required";

		$edit->dir2 = new inputField('Direcci&oacute;n 2','dir2');
		$edit->dir2->rule='max_length[255]';
		$edit->dir2->size =40;
		$edit->dir2->maxlength =255;
		$edit->dir2->append("Calle, avenida");
		$edit->dir2->group = "Datos De Ubicacion";
		if($id_contribu && $status=='create')
		$edit->dir2->insertValue=$CONTRIBU['dir2'];
		if($this->datasis->traevalor('R_PUBLICIDAD_OBLIGA_DIR2','N')=='S')
		$edit->dir2->rule      = "required";

		$edit->dir3 = new inputField('Direcci&oacute;n 3','dir3');
		$edit->dir3->rule='max_length[255]';
		$edit->dir3->size =40;
		$edit->dir3->maxlength =255;
		$edit->dir3->append("Con Calle o avenida");
		$edit->dir3->group = "Datos De Ubicacion";
		if($id_contribu && $status=='create')
		$edit->dir3->insertValue=$CONTRIBU['dir3'];
		if($this->datasis->traevalor('R_PUBLICIDAD_OBLIGA_DIR3','N')=='S')
		$edit->dir3->rule      = "required";

		$edit->dir4 = new inputField('Direcci&oacute;n 4','dir4');
		$edit->dir4->rule='max_length[255]';
		$edit->dir4->size =40;
		$edit->dir4->maxlength =255;
		$edit->dir4->append("Casa #, o apto #");
		$edit->dir4->group = "Datos De Ubicacion";
		if($id_contribu && $status=='create')
		$edit->dir4->insertValue=$CONTRIBU['dir4'];
		if($this->datasis->traevalor('R_PUBLICIDAD_OBLIGA_DIR4','N')=='S')
		$edit->dir4->rule      = "required";

		$edit->alto = new inputField('Alto','alto');
		$edit->alto->rule     ='trim|numeric';
		$edit->alto->css_class='inputnum';
		$edit->alto->size      =12;
		$edit->alto->maxlength =10;
		//$edit->alto->onchange = "dimension();";

		$edit->ancho = new inputField('Ancho','ancho');
		$edit->ancho->rule     ='trim|numeric';
		$edit->ancho->css_class='inputnum';
		$edit->ancho->size      =12;
		$edit->ancho->maxlength =10;
		//$edit->ancho->onchange = "dimension();";
		
		$edit->dimension = new inputField('Dimension','dimension');
		$edit->dimension->rule     ='trim|numeric';
		$edit->dimension->css_class='inputnum';
		$edit->dimension->size      =12;
		$edit->dimension->maxlength =10;
		$edit->dimension->when  =array("show");
		
		$edit->descrip = new textAreaField('Descripcion','descrip');
		$edit->descrip->rows =2;
		$edit->descrip->cols =40;
		
		$edit->ultano = new inputField('Ultimo A&ntilde;o','ultano');
		$edit->ultano->rule     ='trim|numeric';
		$edit->ultano->css_class='inputnum';
		$edit->ultano->size      =12;
		$edit->ultano->maxlength =10;
		$edit->ultano->when  =array("show");

		$edit->buttons('add','modify', 'save', 'undo', 'delete', 'back');
		$edit->build();
		$data['content'] = $edit->output;
		$data['head']    = $this->rapyd->get_head().script('jquery.js').script('jquery-ui.js').script('plugins/jquery.numeric.pack.js').script('plugins/jquery.meiomask.js').style('vino/jquery-ui.css');
		$data['title']   = $this->tits;
		$this->load->view('view_ventanas', $data);

	}

	function _valida($do){
		$error = '';
		$ancho = $do->get('ancho');
		$alto  = $do->get('alto' );
		$do->set('dimension',round($ancho*$alto,2));

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
		$query="CREATE TABLE `r_publicidad` (
			`id` INT(11) NOT NULL AUTO_INCREMENT,
			`id_tipo` INT(11) NULL DEFAULT NULL,
			`id_parroquia` INT(11) NULL DEFAULT NULL,
			`id_zona` INT(11) NULL DEFAULT NULL,
			`id_sector` INT(11) NULL DEFAULT NULL,
			`dir1` VARCHAR(255) NULL DEFAULT NULL,
			`dir2` VARCHAR(255) NULL DEFAULT NULL,
			`dir3` VARCHAR(255) NULL DEFAULT NULL,
			`dir4` VARCHAR(255) NOT NULL,
			`alto` DECIMAL(10,2) NOT NULL DEFAULT '0.00',
			`ancho` DECIMAL(10,2) NOT NULL DEFAULT '0.00',
			PRIMARY KEY (`id`),
			INDEX `id_tipo` (`id_tipo`),
			INDEX `id_parroquia` (`id_parroquia`),
			INDEX `id_zona` (`id_zona`),
			INDEX `id_sector` (`id_sector`)
		)
		COLLATE='utf8_general_ci'
		ENGINE=MyISAM
		;
		";
		$this->db->simple_query($query);
		
		$query="ALTER TABLE `r_publicidad` ADD COLUMN `id_contribu` INT(11) NULL DEFAULT NULL AFTER `id`";
		$this->db->simple_query($query);
		$query="ALTER TABLE `r_publicidad` ADD INDEX `id_contribu` (`id_contribu`)";
		$this->db->simple_query($query);
		$query="ALTER TABLE `r_publicidad` ADD COLUMN `descrip` TEXT NULL DEFAULT NULL AFTER `ancho`";
		$this->db->simple_query($query);
		$query="ALTER TABLE `r_publicidad` ADD INDEX `id_contribu` (`id_contribu`)";
		$this->db->simple_query($query);
		$query="ALTER TABLE `r_publicidad` 	ADD COLUMN `dimension` DECIMAL(10,2) NOT NULL DEFAULT '0.00' AFTER `ancho`";
		$this->db->simple_query($query);
	}

}
?>
