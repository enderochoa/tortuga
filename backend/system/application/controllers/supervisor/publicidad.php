<?php
class Publicidad extends Controller {

	function Publicidad(){
		parent::Controller(); 
		$this->load->library("rapyd");
		$this->load->library("path");
		$path=new Path();
		$path->setPath($this->config->item('uploads_dir'));
		$path->append('publicidad');
		$this->upload_path =$path->getPath().'/';
		//$this->datasis->modulo_id(907,1);
	}

	function index(){
		redirect("supervisor/publicidad/filteredgrid");
	}

	function filteredgrid(){
		$this->rapyd->load("datafilter","datagrid");

		$filter = new DataFilter("Filtro de Publicidad", 'publicidad');
		
		$filter->descrip= new inputField("Descripci&oacute;n"  , "descrip");
		
		$filter->buttons("reset","search");
		$filter->build();

		$uri = anchor('supervisor/publicidad/dataedit/show/<#id#>','<#id#>');

		$grid = new DataGrid("Lista de Clientes");
		$grid->order_by("id","asc");
		$grid->per_page=15;
		
		$grid->column("Identificador"      ,$uri     );
		$grid->column("Archivo"            ,"archivo");
		$grid->column("Color de Fondo"     ,"bgcolor");
		$grid->column("Descripci&oacute;n" ,"descrip");
		$grid->column("Probabilidad"       ,"prob","align='center'");
		
		$grid->add("supervisor/publicidad/dataedit/create");
		$grid->build();

		$data['content'] = $filter->output.$grid->output;
		$data['title']   = " Publicidad ";
		$data["head"]    = $this->rapyd->get_head();
		
		$this->load->view('view_ventanas', $data);
	}
	
	function dataedit(){
		$this->rapyd->load("dataedit");

		$edit = new DataEdit("Carga de Publicidad","publicidad");
		$edit->back_url = site_url("supervisor/publicidad");

		$edit->archivo  = new uploadField("Archivo", "archivo");
		$edit->archivo->upload_path = $this->upload_path;
		$edit->archivo->allowed_types = "jpg|gif|swf|png";

		$edit->bgcolor = new inputField("Color de Fondo", "bgcolor");
		$edit->bgcolor->maxlength =7;
		$edit->bgcolor->size = 9;
		//$edit->bgcolor->rule="";

		$edit->prob = new inputField("Probabilidad de aparicion", "prob");
		$edit->prob->css_class='inputnum';
		$edit->prob->maxlength =8;
		$edit->prob->size = 6;
		//$edit->prob->rule="";

		$edit->descrip = new textareaField("Descripci&oacute;n", "descrip");
		$edit->descrip->rows = 5;

		$edit->buttons("modify", "save", "undo", "back");
		$edit->build();

		$data['content'] = $edit->output; 		
		$data['title']   = " Registro de publicidad ";
		$data["head"]    = $this->rapyd->get_head();
		$this->load->view('view_ventanas', $data);
	}
	
	function obtener($habia=null){
		$this->load->helper('file');
		//$mSQL="SELECT archivo FROM publicidad ORDER BY RAND() LIMIT 1";
		//$arch=$this->datasis->dameval($mSQL);
		
		if(!empty($habia))
			$where=" WHERE archivo<>'$habia'";
		else
			$where='';
		$tot=$this->datasis->dameval('SELECT SUM(prob) FROM publicidad $where');
		$mSQL="SELECT archivo AS nombre,prob/$tot AS rang FROM publicidad $where ORDER BY id";
		
		$query = $this->db->query($mSQL);
		$aleatorio=rand(0,100)/100;
		if ($query->num_rows() > 0){
			$init=0;
			foreach ($query->result() as $row){
				$init+=$row->rang;
				if ($aleatorio<=$init){
					break;
				}
			}
			$arch=$row->nombre;
		}
		$extension = substr(strrchr($arch, '.'), 1);

		switch ($extension){
			case 'swf':
				$retval= $this->_swf($this->upload_path.$arch);
				break;
			default:
				$retval= $this->_image($this->upload_path.$arch);
		}
		echo $retval;
	}
	
	function _swf($location){
		if (empty($location)) return;
		$retval ='<object classid="clsid:D27CDB6E-AE6D-11cf-96B8-444553540000" codebase="http://download.macromedia.com/pub/shockwave/cabs/flash/swflash.cab#version=6,0,29,0" width="32" height="32">';
	  $retval.='<param name="movie" value="'.$location.'" />';
	  $retval.='<param name="quality" value="high" />';
	  $retval.='<embed id="_ppro" src="'.$location.'" quality="high" pluginspage="http://www.macromedia.com/go/getflashplayer" type="application/x-shockwave-flash"></embed>';
		$retval.='</object>';
		return $retval;
	}
	
	function _image($file, $alt = '#', $attributes = null){
		$retval = '<img id="_ppro" src="'.$file.'" '. (isset($attr) ? $attr : null) .' alt="'. $alt .'" title="'. $alt .'" ';
		if (is_array($attributes)){
			foreach ($attributes as $key => $value) $retval .= "$key=\"$value\" ";
		}
		$retval .= "/>";
	
		return $retval;
	}

	function instalar(){
		$mSQL="CREATE TABLE `publicidad` (
		  `id` bigint(20) unsigned NOT NULL auto_increment,
		  `archivo` varchar(100) default NULL,
		  `bgcolor` varchar(7) default NULL,
		  `prob` float unsigned default NULL,
		  `estampa` timestamp NOT NULL default CURRENT_TIMESTAMP on update CURRENT_TIMESTAMP,
		  `descrip` varchar(200) default NULL,
		  PRIMARY KEY  (`id`),
		  UNIQUE KEY `id` (`id`,`archivo`),
		  KEY `id_2` (`id`)
		) ENGINE=MyISAM AUTO_INCREMENT=2 DEFAULT CHARSET=utf8";
		$this->db->simple_query($mSQL);
	}
} 
?>  
