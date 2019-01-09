<?php
class Menu extends Controller{
	var $niveles;
	
	function Menu(){
		parent::Controller();
		$this->load->library("rapyd");
		$this->niveles=$this->config->item('niveles_menu');
		
	}

	function index(){
		$this->datasis->modulo_id(16,1);
		if ($this->uri->segment(3) === FALSE) $mod = FALSE; else $mod = $this->uri->segment(3);
		
		//$mSQL="SELECT titulo, modulo, codigo FROM intramenu WHERE MID(modulo,1,1)!= '0' AND modulo REGEXP  '[[:digit:]]' ORDER BY modulo";
		//$mSQL="SELECT b.pertenece AS pertenece,IFNULL(b.modulo,a.modulo) AS modulo,IFNULL(b.titulo,a.titulo) AS titulo FROM intramenu AS a RIGHT JOIN intramenu AS b ON a.modulo= b.pertenece ORDER BY modulo";
		$mSQL='SELECT modulo,titulo,orden,id  FROM intramenu AS a ORDER BY modulo,orden';
		$mc  = $this->db->query($mSQL);
		$prop=array('border'=>'0');
		if( strlen($mod)>1) $esde=$mod[0]; else $esde=$mod;
		$out   ='<a href="'.site_url('supervisor/menu/dataedit/create').'">'.image('list-add.png','Agregar opcion',$prop).'</a><b>Men&uacute;</b>';
		$out .= '<ul id="tree">';
		//$out .= '<li><ul>';
		$out .= '<li><a href="'.site_url("supervisor/menu/dataedit/create/0").'" >'.image('list-add.png','Agregar'     ,$prop).'</a>0-Libres';
		$n=1;
		
		foreach( $mc->result() as $row ){
			//$data = array(
      //        'name'        => "orden[$row->modulo]",
      //        'value'       => $row->orden,
      //        'maxlength'   => '10',
      //        'size'        => '3'
      //      );
			
			if(strlen($row->modulo)==1){
				if($n==2){
					$out .= "</li></ul></li>";
				}elseif($n==1){
					$out .= '</li>';
				}elseif($n==3){
					$out .= '</ul></li></ul>';
				}
				
				$n=1;
				$out .= "\n<li>";
				$out .= '<a href="'.site_url("supervisor/menu/dataedit/modify/$row->id").'" >'.image('editor.png','Editar'       ,$prop).'</a> ';
				$out .= '<a href="'.site_url("supervisor/menu/dataedit/create/$row->modulo").'" >'.image('list-add.png','Agregar'  ,$prop).'</a> ';
				$out .= '<a href="'.site_url("supervisor/menu/dataedit/delete/$row->id").'" >'.image('list-remove.png','Eliminar',$prop).'</a> ';
				//$out .= form_input($data);
				$out .= $row->modulo.'- ('.$row->id.') '.$row->titulo;
			
			//nivel 2	
			}elseif(strlen($row->modulo)==3){
				if($n==1){
					$out .= "<ul>";
				}elseif($n==2){
					$out .= '</li>';
				}elseif($n==3){
					$out .= '</ul></li>';
				}
				
				$n=2;
				$out .= "\n  <li>";
				$out .= '<a href="'.site_url("supervisor/menu/dataedit/modify/$row->id").'" >'.image('editor.png','Editar'       ,$prop).'</a> ';
				$out .= '<a href="'.site_url("supervisor/menu/dataedit/create/$row->modulo").'" >'.image('list-add.png','Agregar'  ,$prop).'</a> ';
				$out .= '<a href="'.site_url("supervisor/menu/dataedit/delete/$row->id").'" >'.image('list-remove.png','Eliminar',$prop).'</a> ';
				//$out .= form_input($data);
				$out .= $row->modulo.'- ('.$row->id.') '.$row->titulo;
			
			//nivel 3	
			}else{
				if($n==2){
					$out .= "<ul>";
				}
				
				$n=3;
				$out .= "\n    <li>";
				$out .= '<a href="'.site_url("supervisor/menu/dataedit/modify/$row->id").'" >'.image('editor.png','Editar'       ,$prop).'</a> ';
				$out .= '<a href="'.site_url("supervisor/menu/dataedit/delete/$row->id").'" >'.image('list-remove.png','Eliminar',$prop).'</a> ';
				//$out .= form_input($data);
				$out .= $row->modulo.'- ('.$row->id.') '.$row->titulo;
				$out .='</li>';
			}
		}
		if($n==1){
			$out .= "</li>";
		}elseif($n==2){
			$out .= '</li></ul></li>';
		}elseif($n==3){
			$out .= '</ul></li>';
		}
		$out .= '</ul>';
		
		$data['script']  ='<script type="text/javascript">
		$(function() {
			$("#tree").treeview({
				collapsed: true,
				animated: "medium",
				control:"#sidetreecontrol",
				persist: "location"
			});
		})
		</script>';
		$data['content'] = '<div id="sidetreecontrol"><a href="?#">Contraer todos</a> | <a href="?#">Expandir todos</a> | <a href="?#">Invertir </a></div>'.$out;
		$data["head"]    = script("jquery.pack.js").script("jquery.treeview.pack.js").$this->rapyd->get_head().style('jquery.treeview.css');
		$data['title']   = ' Administraci&oacute;n del Men&uacute; ';
		$this->load->view('view_ventanas', $data);
		
	}
	
	function dataedit(){
		$this->rapyd->load("dataedit");

		$edit = new DataEdit(" ", "intramenu");
		$edit->back_url = site_url("supervisor/menu");
		$edit->pre_process('insert' ,'_pre_insert');
		$edit->post_process('delete',"_pos_del");
		$edit->post_process('insert',"_pos_insert");                                                                 
		
		//if ($pertenece!='m'){ 
		//	$edit->pertenece = new inputField2("Deriva de", "pertenece"); 
		//	$edit->pertenece->mode = "autohide"; 
		//	$edit->pertenece->size = 15; 
		//	$edit->pertenece->readonly=TRUE; 
		//	$edit->pertenece->insertValue=$pertenece; 
		//	$edit->pertenece->when = array("create"); 
		//}
		
		
		$edit->id =  new inputField('Ref','id');
		$edit->id->when=array("show");
		
		$edit->modulo = new inputField2("Modulo", "modulo"); 
		$edit->modulo->size = 15; 
		$edit->modulo->when = array("modify",'show'); 
		
		$edit->titulo = new inputField("Titulo", "titulo");
		$edit->titulo->rule = "required";
		$edit->titulo->size = 45;

		$edit->mensaje = new inputField("Mensaje", "mensaje");
		$edit->mensaje->size = 45;

		$edit->panel = new inputField("Panel", "panel");
		$edit->panel->size = 45;
		
		$edit->target= new dropdownField("Objetivo", "target");  
		$edit->target->option("self"     ,"Link en ventana actual");  
		$edit->target->option("popu"     ,"Link en Popup");
		$edit->target->option("javascript","Proceso Javascript"); 
		
		$edit->ejecutar = new inputField("Ejecutar", "ejecutar");
		$edit->ejecutar->rule='callback_ejecutar';
		$edit->ejecutar->size = 45;

		$edit->visible = new dropdownField("Visible", "visible");
		$edit->visible->option("S","Si");
		$edit->visible->option("N","No");
		
		$edit->orden = new inputField("Orden", "orden");
		$edit->orden->size = 8;
		
		$edit->ancho = new inputField("Ancho", "ancho");
		$edit->ancho->insertValue='1024'; 
		$edit->ancho->css_class='inputnum';
		$edit->ancho->rule     ='numeric';
		$edit->ancho->group    ='Ventana';
		$edit->ancho->size     =8;

		$edit->alto = new inputField("Alto", "alto");
		$edit->alto->insertValue='768';
		$edit->alto->css_class='inputnum';
		$edit->alto->rule     ='numeric';
		$edit->alto->group      ='Ventana';
		$edit->alto->size       =8;
		
		$edit->buttons("add","modify", "save", "undo", "delete", "back");
		$edit->build();
		
		$data['content'] = $edit->output;
		$data["head"]    = $this->rapyd->get_head();
		$data['title']   = ' Menu de opciones ';
		$this->load->view('view_ventanas', $data);
	}
	
	// Devuelve codigo disponible
	function _coddisp($mod=0){
		
		$dec=hexdec($mod);
		
		$mSQL="SELECT hexa FROM intramenu AS a RIGHT JOIN serie AS b ON a.modulo=b.hexa WHERE modulo IS NULL AND valor>=$dec LIMIT 1";
		$retorna=$this->datasis->dameval($mSQL);
		return $retorna;
	}

	function ejecutar($ejecutar){
		$resul=stripos($ejecutar,"'");
		if($resul===FALSE){
			return TRUE;
		}else{
			$this->validation->set_message('ejecutar', 'El campo ejecutar no puede tener comillas simples, use comillas dobles');
			return FALSE;
		}
	}

	function _pos_del($do) {
		$codigo=$do->get('modulo');
		$sql = "DELETE FROM intrasida WHERE modulo like '$codigo%'";
		$this->db->query($sql);
		$mSQL="DELETE FROM intramenu WHERE modulo like '$codigo%'";
		$this->db->simple_query($mSQL);
	}

	function _pre_insert($do){
		//$mod = $do->get('pertenece');
		$mod=$this->uri->segment(4);
		//echo   $mod;
		//exit();
		
		if($mod=='0'){
			$mSQL="SELECT hexa FROM intramenu AS a RIGHT JOIN serie AS b ON a.modulo=LPAD(b.hexa,3,'0') WHERE modulo IS NULL LIMIT 1";
			$retorna=$this->datasis->dameval($mSQL);
			if(strlen($retorna)>3){
    		$do->error_message_ar['pre_ins']="Se ha alcanzado el l&iacute;mite de opciones";
    		return FALSE;
    	}
			$modulo =str_pad($retorna,3,'0',STR_PAD_LEFT);
		}else{
			$niveles=explode(',',$this->niveles);
			$acu=0;
			foreach ($niveles AS $level){
				if($acu > strlen($mod))
					break;
				$acu+=$level;
			}
			
    	$mod   =str_pad($mod,$acu,'0');
    	
    	$modulo=$this->_coddisp($mod);
    	if(strlen($modulo)>$acu){
    		$do->error_message_ar['pre_ins']="Se ha alcanzado el l&iacute;mite de opciones";
    		return FALSE;
    	}
  	}
  	
    $do->set('modulo', $modulo);
    $this->session->set_userdata('menu_m', $modulo);
    return TRUE;
	}

	function _pos_insert($do){
		//$modulo=$this->session->userdata('menu_m');
		$modulo=$do->get('id');
		
		$this->session->unset_userdata('menu_m');
		if($modulo[0] != '0'){
			$usuario=$this->session->userdata('usuario');
			$mSQL="INSERT INTO intrasida (usuario,id,acceso) VALUES ('$usuario','$modulo','S')";
			$this->db->simple_query($mSQL);
		}
		//redirect('/supervisor/menu');
	}
	
	function instalar(){
		$mSQL="ALTER TABLE `intramenu` ADD COLUMN `orden` TINYINT(4) NULL DEFAULT NULL AFTER `pertenece`";
		echo (int) $this->db->simple_query($mSQL);
		$mSQL="ALTER TABLE `intramenu` ADD COLUMN `ancho` INT(10) UNSIGNED NULL DEFAULT '800' AFTER `orden`";
		echo (int) $this->db->simple_query($mSQL);
		$mSQL="ALTER TABLE `intramenu` ADD COLUMN `alto`  INT(10) UNSIGNED NULL DEFAULT '600' AFTER `ancho`";
		echo (int) $this->db->simple_query($mSQL);
		$mSQL="ALTER TABLE `intrasida`  ADD COLUMN `id` INT(12) UNSIGNED NOT NULL AFTER `usuario`";
		echo (int) $this->db->simple_query($mSQL);
		$mSQL="ALTER TABLE `intramenu`  DROP PRIMARY KEY";
		echo (int) $this->db->simple_query($mSQL);
		$mSQL='ALTER TABLE intramenu ADD id INT AUTO_INCREMENT PRIMARY KEY';
		echo (int) $this->db->simple_query($mSQL);
		$mSQL="UPDATE intrasida JOIN intramenu ON intramenu.modulo= intrasida.modulo SET intrasida.id = intramenu.id";
		echo (int) $this->db->simple_query($mSQL);
		$mSQL="ALTER TABLE `intrasida`  DROP PRIMARY KEY,  ADD PRIMARY KEY (`usuario`, `id`)";
		echo (int) $this->db->simple_query($mSQL);
		$mSQL="ADD UNIQUE INDEX `modulo` (`modulo`)";
		echo (int) $this->db->simple_query($mSQL);
		
		//$mSQL='ALTER TABLE `intramenu` DROP PRIMARY KEY';
		//$this->db->simple_query($mSQL);
		//$mSQL='ALTER TABLE `intramenu` ADD UNIQUE `modulo` (`modulo`)';
		//$this->db->simple_query($mSQL);

	} 
}   
?>  
    
    
