<?php
class r_vehiculo extends Controller {
	var $titp='Vehiculos';
	var $tits='Vehiculo';
	var $url ='recaudacion/r_vehiculo/';
	function r_vehiculo(){
		parent::Controller();
		$this->load->library("rapyd");
		$this->datasis->modulo_id(407,1);
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

		$filter = new DataFilter($this->titp, 'r_vehiculo');
		$filter->db->select(array(
		"r_vehiculo.id",
		"r_vehiculo.placa",
		"r_vehiculo.ano",
		"r_vehiculo.color",
		"rv_marca.descrip marca",
		"rv_modelo.descrip modelo",
		"rv_tipo.descrip tipo",
		"rv_clase.descrip clase",
		"r_contribu.nombre","r_contribu.rifci"
		));
		$filter->db->join('r_contribu','r_vehiculo.id_contribu=r_contribu.id','LEFT');
		$filter->db->join('rv_marca'  ,'r_vehiculo.id_marca=rv_marca.id','LEFT');
		$filter->db->join('rv_modelo' ,'r_vehiculo.id_modelo=rv_modelo.id','LEFT');
		$filter->db->join('rv_tipo'   ,'r_vehiculo.id_tipo=rv_tipo.id','LEFT');
		$filter->db->join('rv_clase'  ,'r_vehiculo.id_clase=rv_clase.id','LEFT');

		$filter->id = new inputField('Ref.','id');
		$filter->id->size      =10;
		$filter->id->db_name   ='r_vehiculo.id';

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
		
		$filter->id_tipo = new dropDownField('Tipo','id_tipo');
		$filter->id_tipo->option('','');
		$filter->id_tipo->options("SELECT id,descrip FROM rv_tipo ORDER BY descrip");

		$filter->id_marca = new dropDownField('Marca','id_marca');
		$filter->id_marca->option('','');
		$filter->id_marca->options("SELECT id,descrip FROM rv_marca ORDER BY descrip");

		$filter->id_modelo = new dropDownField('Modelo','id_modelo');
		$filter->id_modelo->option('','');
		$filter->id_modelo->options("SELECT id,descrip FROM rv_modelo ORDER BY descrip");		
		
		//$filter->descrip = new inputField('Descripcion','descrip');
		//$filter->descrip->size =20;
		//$filter->descrip->maxlength =12;
		//$filter->descrip->dbname='r_vehiculo.descrip';

		$filter->id_clase = new dropDownField('Clase','id_clase');
		$filter->id_clase->option('','');
		$filter->id_clase->options("SELECT id,descrip FROM rv_clase ORDER BY descrip");

		$filter->placa = new inputField('Placa','placa');
		$filter->placa->rule='trim|max_length[12]|required|unique';
		$filter->placa->size =20;
		$filter->placa->maxlength =12;

		$filter->color = new inputField('Color','color');
		$filter->color->rule      ='max_length[50]';
		$filter->color->size      =20;
		$filter->color->maxlength =50;

		$filter->ano = new inputField('A&ntilde;o','ano');
		$filter->ano->rule      ='max_length[6]';
		$filter->ano->size      =5;

		$filter->serialc = new inputField('Serial Carroceria','serialc');
		$filter->serialc->rule      ='max_length[15]';
		$filter->serialc->size      =20;

		$filter->serialm = new inputField('Serial Motor','serialm');
		$filter->serialm->rule      ='max_length[20]';
		$filter->serialm->size      =20;

		$filter->buttons('reset', 'search');
		$filter->build();

		$uri = anchor($this->url.'dataedit/show/<raencode><#id#></raencode>','<#id#>');

		$grid = new DataGrid('');
		$grid->order_by('r_vehiculo.id');
		$grid->per_page = 40;
	
		$grid->column_orderby('Ref.'           ,"$uri"       ,'r_vehiculo.id'    ,'align="left"');
		$grid->column_orderby('Placa'          ,"placa"      ,'placa'            ,'align="left"');
		$grid->column_orderby('A&ntilde;o'     ,"ano"        ,'ano'              ,'align="left"');
		$grid->column_orderby('Color'          ,"color"      ,'color'            ,'align="left"');
		$grid->column_orderby('Marca'          ,"marca"      ,'id_marca'         ,'align="left"');
		$grid->column_orderby('Modelo'         ,"modelo"     ,'id_modelo'        ,'align="left"');
		$grid->column_orderby('Tipo'           ,"tipo"       ,'id_tipo'          ,'align="left"');
		$grid->column_orderby('Clase'          ,"clase"      ,'id_tipo'          ,'align="left"');
		$grid->column_orderby('Contribuyente'  ,"nombre"     ,'techo'            ,'align="left"');
		$grid->column_orderby('Rif/CI'         ,"rifci"      ,'rifci'            ,'align="left"');

		$grid->add($this->url.'dataedit/create');
		$grid->build();
		//echo $grid->db->last_query();

		$data['filtro']  = $filter->output;
		$data['content'] = $grid->output;
		$data['head']    = $this->rapyd->get_head().script('jquery.js');
		$data['title']   = $this->titp;
		$this->load->view('view_ventanas', $data);

	}
	
	function dataedit($status=null,$id_contribu=null){
		$this->rapyd->load('dataedit2');
		
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
		
		$link  =site_url('recaudacion/r_vehiculo/get_modelo');
		$link2 =site_url('recaudacion/r_vehiculo/add_modelo');
		$link3 =site_url('recaudacion/r_vehiculo/get_linea');
		
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
				
				$("#id_marca").change(function(){
					$.post("'.$link.'",{ id_marca:$(this).val() },function(data){
						$("#id_modelo").html(data);
					});
				});
				
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
			
			function add_modelo(){
					marcaval=$("#id_marca").val();
					if(marcaval==""){
						alert("Debe seleccionar una Marca al cual agregar el Modelo");
					}else{
						modelo=prompt("Introduza el nombre del MODELO a agregar a la MARCA seleccionada");
						if(modelo==null){
						}else{
							$.ajax({
							 type: "POST",
							 processData:false,
								url: "'.$link2.'",
								data: "valor="+modelo+"&&valor2="+marcaval,
								success: function(msg){
									if(msg=="Y.a-Existe"){
										alert("Ya existe una marca con esa Descripcion");
									}
									else{
										if(msg=="N.o-SeAgrego"){
											alert("Disculpe. En este momento no se ha podido agregar el modelo, por favor intente mas tarde");
										}else{
											$.post("'.$link3.'",{ marca:marcaval,modelodes:modelo },function(data){
												$("#id_modelo").html(data);
												$("#id_modelo").val(msg);
											})
										}
									}
								}
							});
						}
					}
				}
				
			function add_marca(){
				marca=prompt("Introduza el nombre de la MARCA a agregar");
				if(marca==null){
				}else{
					$.ajax({
					type: "POST",
					processData:false,
					url: "'.site_url('recaudacion/r_vehiculo/add_marca').'",
					data: "valor="+marca ,
					success: function(msg){
						if(msg=="Y.a-Existe"){
							alert("Ya existe una marca con esa Descripcion");
						}else{
							if(msg=="N.o-SeAgrego"){
								alert("Disculpe. En este momento no se ha podido agregar la marca, por favor intente mas tarde");
							}else{
								$("#id_marca").html("<option value="+msg+">"+marca+"</option>");
								$("#id_marca").val(msg);
							}
						}
					}
					});
				}
			}
		';

		$do = new DataObject("r_vehiculo");
		//$do->pointer('r_contribu' ,'r_vehiculo.id_contribu=r_contribu.id',"r_contribu.nombre nombrep,r_contribu.rifci rifcip","LEFT");

		$edit = new DataEdit2($this->tits, $do);
		
		$edit->script($script,"create");
		$edit->script($script,"modify");

		$edit->back_url = site_url($this->url."filteredgrid");

		$edit->post_process('insert','_post_insert');
		$edit->post_process('update','_post_update');
		$edit->post_process('delete','_post_delete');

		$edit->id = new inputField('Ref','id');
		$edit->id->rule='max_length[11]';
		$edit->id->size =13;
		$edit->id->maxlength =11;
		$edit->id->mode='autohide';
		$edit->id->when=array('show','modify');
		$edit->id->db_name='r_vehivulo.id';

		$edit->id_contribu = new inputField('Contribuyente','id_contribu');
		$edit->id_contribu->rule='required';
		$edit->id_contribu->size =5;
		$edit->id_contribu->readonly=true;
		if($id_contribu)
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

		$edit->id_tipo = new dropDownField('Tipo','id_tipo');
		$edit->id_tipo->option('','');
		$edit->id_tipo->options("SELECT id,descrip FROM rv_tipo ORDER BY descrip");
		//$edit->id_tipo->rule='required';

		$AddMarca='<a href="javascript:add_marca();" title="Haz clic para Agregar un nueva Marca">Agregar Marca</a>';
		$edit->id_marca = new dropDownField('Marca','id_marca');
		$edit->id_marca->option('','');
		$edit->id_marca->options("SELECT id,descrip FROM rv_marca ORDER BY descrip");
		//$edit->id_marca->rule='required';
		$edit->id_marca->append($AddMarca);

		$AddModelo='<a href="javascript:add_modelo();" title="Haz clic para Agregar un nuevo Modelo;">Agregar Modelo</a>';
		$edit->id_modelo = new dropDownField('Modelo','id_modelo');
		//$edit->id_modelo->option('','');
		$edit->id_modelo->append($AddModelo);
		$id_modelo=$edit->getval('id_modelo');
		if($id_modelo!==FALSE)
			$edit->id_modelo->options("SELECT id,descrip FROM rv_modelo ORDER BY descrip");
		
		$edit->descrip = new inputField('Descripcion','descrip');
		$edit->descrip->rule='trim|max_length[12]|';
		$edit->descrip->size =20;
		$edit->descrip->maxlength =12;
		$edit->descrip->mode='autohide';

		$edit->id_clase = new dropDownField('Clase','id_clase');
		$edit->id_clase->rule = 'required';
		$edit->id_clase->option('','');
		$edit->id_clase->options("SELECT id,CONCAT(codigo,' ',descrip) FROM rv_clase ORDER BY codigo");
		//$edit->id_clase->rule='required';

		$edit->placa = new inputField('Placa','placa');
		$edit->placa->rule='trim|max_length[12]|required|unique';
		$edit->placa->size =20;
		$edit->placa->maxlength =12;

		$edit->color = new inputField('Color','color');
		$edit->color->rule='max_length[50]';
		$edit->color->size =20;
		$edit->color->maxlength =50;

		$edit->capacidad = new inputField('Capacidad','capacidad');
		$edit->capacidad->rule='trim|max_length[11]|numeric';
		$edit->capacidad->size =5;
		$edit->capacidad->maxlength =11;
		$edit->capacidad->css_class='inputnum';

		$edit->ejes = new inputField('Ejes','ejes');
		$edit->ejes->rule='trim|max_length[11]|numeric';
		$edit->ejes->size =5;
		$edit->ejes->maxlength =11;
		$edit->ejes->css_class='inputnum';

		$edit->ano = new inputField('A&ntilde;o','ano');
		$edit->ano->rule='trim|max_length[6]|numeric';
		$edit->ano->size =8;
		$edit->ano->maxlength =6;
		$edit->ano->css_class='inputnum';

		$edit->peso = new inputField('Peso','peso');
		$edit->peso->rule='trim|max_length[19]|numeric';
		$edit->peso->size =21;
		$edit->peso->maxlength =19;
		$edit->peso->css_class='inputnum';

		$edit->serialc = new inputField('Serial Carroceria','serialc');
		$edit->serialc->size =20;
		$edit->serialc->maxlength =20;

		$edit->serialm = new inputField('Serial Motor','serialm');
		$edit->serialm->size =20;
		$edit->serialm->maxlength =20;

		$edit->buttons('add','modify', 'save', 'undo', 'delete', 'back');
		$edit->build();
		$data['content'] = $edit->output;
		$data["head"]    = script('jquery.js').script('jquery-ui.js').script('plugins/jquery.numeric.pack.js').script('plugins/jquery.floatnumber.js').script('plugins/jquery.meiomask.js').style('vino/jquery-ui.css').$this->rapyd->get_head();
		$data['title']   = $this->tits;
		$this->load->view('view_ventanas', $data);

	}
	
	function get_modelo(){
		
	    $id_marca=$this->input->post('id_marca');
	    if(!empty($id_marca)){
	    	$mSQL=$this->db->query("SELECT id,descrip FROM rv_modelo WHERE id_marca=$id_marca");
			foreach($mSQL->result() AS $fila ){
				echo "<option value='".$fila->id."'>".$fila->descrip."</option>";
	    	}
	    }
	    
	}
	
	function get_linea(){//usado por sinv
		echo "<option value=''>Seleccione un Modelo</option>";
	    $marca=$this->input->post('marca');
	    $modelo=$this->input->post('modelodes');
	    $modeloe=$this->db->escape($modelo);
	    if(!empty($marca)){
	    	$mSQL=$this->db->query("SELECT id,descrip FROM rv_modelo WHERE id_marca =$marca ORDER BY id_marca<>$marca AND descrip<>$modeloe");
	    	if($mSQL){
	    		foreach($mSQL->result() AS $fila ){
	    			echo "<option value='".$fila->id."'>".$fila->descrip."</option>";
	    		}
	    	}
	    }
	}
	
	function add_modelo()//usado por sinv
	{
		if(isset($_POST['valor']) && isset($_POST['valor2'])){
			$valor=$_POST['valor'];
			$valore = $this->db->escape($valor);
			$valor2=$_POST['valor2'];
			
			$existe=$this->datasis->dameval("SELECT COUNT(descrip) FROM rv_modelo WHERE descrip=$valore AND id_marca=$valor2");
			if($existe>0){
				echo "Y.a-Existe";
			}else{
				$agrego=$this->db->query("INSERT INTO rv_modelo (id,id_marca,descrip) VALUES ('',$valor2,$valore)");
				$id = $this->db->last_insert_id();
				if($agrego)echo $id;
					else echo "N.o-SeAgrego";
			}
		}
	}
	
	function add_marca()//usado por sinv
	{
		if(isset($_POST['valor'])){
			$valor=$_POST['valor'];
			$valore = $this->db->escape(trim($valor));
			
			$existe=$this->datasis->dameval("SELECT COUNT(descrip) FROM rv_marca WHERE TRIM(descrip)=$valore ");
			if($existe>0){
				echo "Y.a-Existe";
			}else{
				$agrego=$this->db->query("INSERT INTO rv_marca (id,descrip) VALUES ('',$valore)");
				
				if($agrego){
					$id = $this->datasis->dameval("SELECT MAX(id) FROM rv_marca");
					echo $id;
				}else{
					 echo "N.o-SeAgrego";
				 }
			}
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
		$mSQL="CREATE TABLE `r_vehiculo` (
		  `id` int(11) NOT NULL AUTO_INCREMENT,
		  `id_contribu` int(11) NOT NULL,
		  `id_tipo` int(11) DEFAULT NULL,
		  `id_marca` int(11) DEFAULT NULL,
		  `id_modelo` int(11) DEFAULT NULL,
		  `id_clase` int(11) NOT NULL,
		  `placa` varchar(12) DEFAULT NULL,
		  `color` varchar(50) DEFAULT NULL,
		  `capacidad` int(11) DEFAULT NULL,
		  `ejes` int(11) DEFAULT NULL,
		  `ano` smallint(6) DEFAULT NULL,
		  `peso` decimal(19,2) DEFAULT NULL,
		  `serialc` varchar(15) DEFAULT NULL,
		  `serialm` varchar(20) DEFAULT NULL,
		  PRIMARY KEY (`id`)
		) ENGINE=MyISAM AUTO_INCREMENT=2 DEFAULT CHARSET=utf8";
		$this->db->simple_query($mSQL);
		
		$query="ALTER TABLE `r_vehiculo` 	CHANGE COLUMN `serialc` `serialc` VARCHAR(20) NULL DEFAULT NULL AFTER `peso`";
		$this->db->simple_query($query);
	}

}
?>
