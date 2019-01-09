<?php
class Fotos extends Controller {
	var $upload_path;
	function Fotos(){
		parent::Controller(); 
		$this->load->library("rapyd");
		$this->load->library("path");
		$path=new Path();
		$path->setPath($this->config->item('uploads_dir'));
		$path->append('/inventario/Image');
		$this->upload_path =$path->getPath().'/';
	}

	function index(){
		$this->datasis->modulo_id(310,1);
		redirect("inventario/fotos/filteredgrid/index");
  }

	function filteredgrid(){
		$this->rapyd->load("datafilter2","datagrid");
		
		rapydlib("prototype");
		$ajax_onchange = '
			  function get_linea(){
			    var url = "'.site_url('reportes/sinvlineas').'";
			    var pars = "dpto="+$F("depto");
			    var myAjax = new Ajax.Updater("td_linea", url, { method: "post", parameters: pars });
			  }
			  function get_grupo(){
			    var url = "'.site_url('reportes/sinvgrupos').'";
			    var pars = "dpto="+$F("depto")+"&linea="+$F("linea");
			    var myAjax = new Ajax.Updater("td_grupo", url, { method: "post", parameters: pars });
			  }';

		$mSPRV=array(
			'tabla'   =>'sprv',
			'columnas'=>array(
			'proveed' =>'C�digo',
			'nombre'=>'Nombre',
			'contacto'=>'Contacto'),
			'filtro'  =>array('proveed'=>'C�digo','nombre'=>'Nombre'),
			'retornar'=>array('proveed'=>'proveed'),
			'titulo'  =>'Buscar Beneficiario');
			
		$bSPRV=$this->datasis->modbus($mSPRV);
		
		$filter = new DataFilter2("Filtro por Producto");
		
		if ($this->input->post("fotos"))
	    	$ddire='';
		else
			$ddire='left';
		
		$filter->db->select("a.codigo as scodigo,a.descrip,a.grupo,b.codigo,a.id,a.precio1,a.precio2,a.precio3,a.precio4"); 
		$filter->db->from("sinv AS a");   
		$filter->db->join("sinvfot AS b","a.codigo=b.codigo",$ddire);
		$filter->db->groupby("a.codigo");
		$filter->script($ajax_onchange);

		$filter->codigo = new inputField("C�digo", "a.codigo");
		$filter->codigo->size=15;
		
		$filter->clave = new inputField("Clave", "clave");
		$filter->clave->size=15;
		
		$filter->proveed = new inputField("Beneficiario", "proveed");
		$filter->proveed->append($bSPRV);
		$filter->proveed->clause ="in";
		$filter->proveed->db_name='( a.prov1, a.prov2, a.prov3 )';
		$filter->proveed->size=15;
		
		$filter->descrip = new inputField("Descripci&oacute;n", "a.descrip");
		$filter->descrip->db_name='CONCAT_WS(" ",descrip,descrip2)';
		$filter->descrip->size=34;
		
		$filter->dpto = new dropdownField("Departamento", "depto");
		$filter->dpto->clause='';
		$filter->dpto->option("","");
		$filter->dpto->options("SELECT depto, descrip FROM dpto ORDER BY descrip");
		$filter->dpto->onchange = "get_linea();";
		//$filter->dpto->style = "width:220px";

		$filter->linea = new dropdownField("L�nea","linea");
		$filter->linea->clause='';
		$filter->linea->option("","Seleccione un departamento");
		$filter->linea->onchange = "get_grupo();";
		//$filter->linea->style = "width:300px";

		$filter->grupo = new dropdownField("Grupo","grupo");
		$filter->grupo->db_name="a.grupo";
		$filter->grupo->option("","Seleccione una L�nea");
		//$filter->grupo->style = "width:220px";
		
		$filter->marca = new dropdownField("Marca", "marca");
		$filter->marca->option("","");  
		$filter->marca->options("SELECT TRIM(marca) AS clave, TRIM(marca) AS valor FROM marc ORDER BY marca");
		$filter->marca->style = "width:140px"; 
			  
		$filter->fotos = new checkboxField("Mostrar solo productos con fotos", "fotos", "y","n"); 
		$filter->fotos->clause='';  
		$filter->fotos->insertValue = "n";                                 
		
		$filter->buttons("reset","search");
		$filter->build();

		$grid = new DataGrid("Lista de Art&iacute;culos");
		$grid->order_by("a.codigo","asc");
		$grid->per_page = 20;
		$link=anchor('/inventario/fotos/dataedit/<#id#>/create/','<#scodigo#>');

		$grid->use_function('str_replace');
		$grid->column("C&oacute;digo",$link);
		$grid->column("Descripci&oacute;n","descrip");
		$grid->column("Precio 1","<number_format><#precio1#>|2|,|.</number_format>",'align=Right');
		$grid->column("Precio 2","<number_format><#precio2#>|2|,|.</number_format>",'align=Right');
		$grid->column("Precio 3","<number_format><#precio3#>|2|,|.</number_format>",'align=Right');
		$grid->column("Precio 4","<number_format><#precio4#>|2|,|.</number_format>",'align=Right');	

		$grid->build();
    //echo $grid->db->last_query();
		$data['content'] = $filter->output.$grid->output;
		$data["head"]    = script("tabber.js").script("prototype.js").$this->rapyd->get_head().script("scriptaculous.js").script("effects.js");
		$data['title']   = ' Lista de Art&iacute;culos ';
		$this->load->view('view_ventanas', $data);
		
	}

	function dataedit($id=NULL){
		if($id!=NULL OR $id!='create' OR $id!='show')
			$codigo=$this->datasis->dameval("SELECT codigo FROM sinv WHERE id=$id");

		$this->rapyd->load("dataedit");
		$sinv=array(
		  'tabla'   =>'sinv',
		  'columnas'=>array(
		  'codigo' =>'C&acute;digo',
		  'descrip'=>'Descripci&oacuten'),
		  'filtro'  =>array('codigo' =>'C&oacute;digo','descrip'=>'descrip'),
		  'retornar'=>array('codigo'=>'mcod'),
		  'titulo'  =>'Buscar Art&iacute;culo',
		  'script'=>array('agregar()'));
		$bSINV=$this->datasis->modbus($sinv);

		$edit = new DataEdit("Fotos de Inventario", "sinvfot");
		$edit->pre_process("insert","_pre_insert");
		$edit->pre_process("update","_pre_modifi");
		$edit->post_process("delete","_post_delete");
		$edit->back_url = site_url("inventario/fotos/filteredgrid/index");

		$edit->codigo = new inputField("C&oacute;digo", "codigo");
		$edit->codigo->size=30;
		$edit->codigo->rule = "required";
		$edit->codigo->append($bSINV.'Seleccione todos los codigos asociados a la foto separados por punto y coma (;)');
		$edit->codigo->insertValue = $codigo;

		//echo $edit->codigo->value;
		$edit->foto = new uploadField("Imagen", "nombre");
		$edit->foto->rule          = "required";
		$edit->foto->upload_path   = $this->upload_path;
		$edit->foto->allowed_types = "jpg";
		$edit->foto->delete_file   =false;
		$edit->foto->append('Solo imagenes JPG');
		$edit->foto->file_name = url_title($codigo).'_.jpg';
		
		$edit->principal = new dropdownField("Principal","principal");
		$edit->principal->option("N","N");
		$edit->principal->option("S","S");
		$edit->principal->style = "width:50px";
		$edit->principal->rule='required|callback_principal';
		  
		$edit->evaluacion = new textareaField("Comentario", "comentario");
		$edit->evaluacion->rows = 6;
		$edit->evaluacion->cols=70;
		//$edit->evaluacion->when=array('show');

		$edit->iframe = new iframeField("related", "inventario/fotos/verfotos/$id","210");
		$edit->iframe->when= array("create");

		$pk=$edit->_dataobject->pk;
		$pk=$pk["id"];
		$edit->miframe = new iframeField("related", "inventario/fotos/asocfotos/$pk","210");  
		$edit->miframe->when = array("modify","show");

		$edit->buttons("modify", "save", "undo", "delete", "back");
		$edit->build();

		$fhidden = array(
			'name' => 'mcod',
			'id'   => 'mcod',
			'type' => "hidden");

		$data['script']  ='<script language="javascript" type="text/javascript">
			function agregar(){
				add=document.getElementById("mcod").value;
				codigo=document.getElementById("codigo");
				if (add.length>0)
					codigo.value=codigo.value+";"+add;
				else
					codigo.value=add;
			}
		</script>';
		$data['content'] = form_input($fhidden).$edit->output;
		$data["head"]    = $this->rapyd->get_head();
		$data['title']   = ' Carga de Fotos ';
		$this->load->view('view_ventanas', $data);
	}

	function asocfotos($id=''){
		$this->rapyd->load("datagrid");
		
		$nombre='';
		$sinv_id='';
		$query = $this->db->query("SELECT nombre,sinv_id FROM sinvfot WHERE id='$id'");
		if ($query->num_rows() > 0){
			$row = $query->row();
			$nombre  = $row->nombre;
			$sinv_id = $row->sinv_id ;
		}

		$grid = new DataGrid("Lista de Art&iacute;culos");
		$grid->db->select(array('a.codigo','a.id','a.estampa','b.descrip','b.precio1','b.precio2','b.precio3','b.precio4'));
		$grid->db->from('sinvfot AS a');
		$grid->db->join('sinv AS b','a.codigo=b.codigo');
		$grid->db->where("nombre='$nombre'");
		$grid->order_by("codigo","asc");
		$grid->per_page = 20;
		$link='<a href="'.site_url("/inventario/fotos/dataedit/$sinv_id/show/<#id#>").'" target="_parent"><#codigo#></a>';
		
		$grid->use_function('str_replace');
		$grid->column("c&oacute;digo",$link);
		$grid->column("Descripci&oacute;n","descrip");
		$grid->column("Precio 1","precio1");
		$grid->column("Precio 2","precio2");
		$grid->column("Precio 3","precio3");
		$grid->column("Precio 4","precio4");
		$grid->build();
		$grid->db->last_query();

		$data['content'] = $grid->output;
		$data["head"]    = $this->rapyd->get_head();
		$data['title']   = '';
		$this->load->view('view_ventanas_sola', $data);
	}

	function verfotos($sinv_id){
		$this->rapyd->load("datatable");

		$table = new DataTable(null);
		$table->cell_attributes = 'style="vertical-align:middle; text-align: center;"';
		
		$table->db->select(array('nombre','id'));
		$table->db->from("sinvfot");
		$table->db->where("sinv_id='$sinv_id'");

		$table->per_row = 4;
		$table->per_page = 16;
		$table->cell_template = "<a href='".site_url("/inventario/fotos/dataedit/$sinv_id/show/<#id#>")."' target='_parent' ><img src='".$this->upload_path."/<#nombre#>'  width=150 border=0></a>";
		$table->build();

		$data['content'] = $table->output;
		$data['title']   = "";
		$data["head"]   = style("ventanas.css").style("estilos.css").$this->rapyd->get_head();
		$this->load->view('view_ventanas_sola', $data);
	}

	function _pre_insert($do){
		$codigos=explode(';',$do->get('codigo'));
		
		$id=$this->datasis->dameval("SELECT id FROM sinv WHERE codigo='$codigos[0]'");
		$do->set('codigo' , $codigos[0]);
		$do->set('ruta'   , $this->upload_path);
		$do->set('sinv_id', $id);
		$c=false;
		foreach($codigos AS $codigo){
			if($c){
				$id=$this->datasis->dameval("SELECT id FROM sinv WHERE codigo='$codigo'");
				$mSQL="INSERT INTO sinvfot (sinv_id,codigo,nombre,ruta,comentario) VALUES (?,?,?,?,?)";
				$this->db->query($mSQL, array($id, $codigo, $do->get('nombre'),$this->upload_path,$do->get('comentario')));
			}else{
				$c=true;
			}
		}
	}

	function _pre_modifi($do){
		$codigos=explode(';',$do->get('codigo'));
		$nombre=$do->get('nombre');
		$id=$this->datasis->dameval("SELECT id FROM sinv WHERE codigo='$codigos[0]'");
		$do->set('codigo' , $codigos[0]);
		$do->set('ruta'   , $this->upload_path);
		$do->set('sinv_id', $id);
		$c=false;
		foreach($codigos AS $codigo){
			if($c){
				$cant=$this->datasis->dameval("SELECT COUNT(*) FROM sinvfot WHERE codigo='$codigo' AND nombre='$nombre'");
				if($cant==0){
					$id=$this->datasis->dameval("SELECT id FROM sinv WHERE codigo='$codigo'");
					$mSQL="INSERT INTO sinvfot (sinv_id,codigo,nombre,ruta,comentario) VALUES (?,?,?,?,?)";
					$this->db->query($mSQL, array($id, $codigo, $do->get('nombre'),$this->upload_path,$do->get('comentario')));
				}
			}else{
				$c=true;
			}
		}
	}
	
	function ver($id){
		$this->rapyd->load("datatable");

		$table = new DataTable(null);
		$table->cell_attributes = 'style="vertical-align:middle; text-align: center;"';
		
		$table->db->select(array('nombre','comentario'));
		$table->db->from("sinvfot");
		$table->db->where("sinv_id='$id'");

		$table->per_row = 1;
		$table->per_page = 1;
		$table->cell_template = "<img src='".$this->upload_path."<#nombre#>' width='300' border=0><br><#comentario#>";
		$table->build();

		$data['content'] = '<center>'.$table->output.'</center>';
		$data['title']   = "";
		$data["head"]   = style("ventanas.css").style("estilos.css").$this->rapyd->get_head();
		$this->load->view('view_ventanas_sola', $data);
	}

	function obtener($id){
	    $nombre=$this->datasis->dameval("SELECT nombre FROM sinvfot WHERE sinv_id='$id' limit 1");
		$this->mostrar($nombre);                
	
	    /*
	    $config['image_library'] = 'gd2';
	    $config['source_image'] = $path->getPath();
	    $config['create_thumb'] = TRUE;
	    $config['maintain_ratio'] = TRUE;
	    $config['width'] = 75;
	    $config['height'] = 50;
	
	    $this->load->library('image_lib', $config);
	    $this->image_lib->resize();*/
	}
	
	function mostrar($nombre){
		$path=new Path();
	    $path->setPath($_SERVER['DOCUMENT_ROOT']);
	    $path->append($this->upload_path);
	    $path->append($nombre);
	
	    if (!empty($nombre) AND file_exists($path->getPath())){
	            header('Content-type: image/jpg');
	            $data = file_get_contents($path->getPath());
	    }else{
	            header('Content-type: image/gif');
	            $path=new Path();
	            $path->setPath($_SERVER['DOCUMENT_ROOT']);           
	            $path->append($this->config->item('base_url'));
	            $path->append('images/ndisp.gif');
	            $data = file_get_contents($path->getPath());
	    }
	    echo $data;
	}
	
	function principal($codigo){
		//$id=$this->input->post('id');
		$codigo=$this->input->post('codigo');
		$chek=$this->datasis->dameval("SELECT COUNT(*) FROM sinvfot WHERE codigo='$codigo' and principal='S'");
		if ($chek > 0){
			$mSQL_1=$this->db->query("SELECT id FROM sinvfot WHERE codigo='$codigo' and principal='S'");
			$row = $mSQL_1->row();
			$ids =$row->id;
			$mSQL_2=$this->db->query("UPDATE sinvfot SET principal='N' WHERE id='$ids'");
			//return FALSE;
		}else {
  		return TRUE;
		}
	}
	
	function _post_delete($do){
		$nombre=$do->get('nombre');
		$chek=$this->datasis->dameval("SELECT COUNT(*) FROM sinvfot WHERE nombre='$nombre'");
		if($chek<=0){
			$path=new Path();
            $path->setPath($_SERVER['DOCUMENT_ROOT']);
            $path->append($this->upload_path);
            $path->append($nombre);
			unlink($path->getPath());
		}
	}
	
	function instalar(){
		$mSQL='CREATE TABLE IF NOT EXISTS `sinvfot` (
		  `id` int(10) unsigned NOT NULL auto_increment,
		  `codigo` varchar(15) default NULL,
		  `nombre` varchar(50) default NULL,
		  `alto_px` smallint(5) unsigned default NULL,
		  `ancho_px` smallint(6) default NULL,
		  `ruta` varchar(100) default NULL,
		  `comentario` text,
		  `estampa` timestamp NULL default NULL,
		  PRIMARY KEY  (`id`),
		  UNIQUE KEY `id` (`id`),
		  UNIQUE KEY `foto` (`codigo`,`nombre`),
		  KEY `id_2` (`id`,`codigo`)
		) ENGINE=MyISAM AUTO_INCREMENT=3 DEFAULT CHARSET=utf8';
		$this->db->simple_query($mSQL);
		$mSQL='ALTER TABLE `sinvfot` ADD `sinv_id` INT UNSIGNED NOT NULL AFTER `id`';
		$this->db->simple_query($mSQL);
		$mSQL='ALTER TABLE `sinvfot` ADD INDEX `sinv_id` (`sinv_id`)';
		$this->db->simple_query($mSQL);
		$mSQL='ALTER TABLE `sinvfot` CHANGE `estampa` `estampa` TIMESTAMP NOT NULL';
		$this->db->simple_query($mSQL);
		$mSQL='UPDATE sinvfot AS a JOIN sinv AS b ON a.codigo=b.codigo SET a.sinv_id=b.id';
		$this->db->simple_query($mSQL);
		$mSQL='ALTER TABLE `sinvfot` ADD `principal` VARCHAR(3) NULL';
		$this->db->simple_query($mSQL);
	}
}
?>
