<?php

class Menu extends Controller
{
	function Menu(){
		parent::Controller();
	}

	function index(){
		$this->datasis->modulo_id(16);
		$this->session->set_userdata('panel', 9);
		if ($this->uri->segment(3) === FALSE) $mod = FALSE; else $mod = $this->uri->segment(3);
		$data['lista']   = $this->_lista($mod);
		$data['titulo1'] = " Bienvenido a Men&uacute; \n";
		$data['forma']  ='Forma';
		$this->layout->buildPage('menu/mostrar', $data);
	}
	
	function editar(){
		if ($this->uri->segment(3) === FALSE) $mod = FALSE; else $mod = $this->uri->segment(3);
		$data['titulo1'] = " Editar Men&uacute; \n";
		$data['forma'] = form_open("menu/guardar");
		$data['forma'] .= "<table width='100%' class='bordetabla'>\n<tr bgcolor=\"#7799BE\"><td colspan=\"2\" class=\"titulo_tabla\">Propiedades</td>\n</tr><tr>\n";
		$data['forma'] .=	"<th>M&oacute;dulo: </th><td>";
		if ($mod){
			$mSQL="SELECT titulo, modulo, mensaje, panel, ejecutar,target,visible FROM intramenu WHERE codigo=$mod";
			$mc = $this->db->query($mSQL);
			$sal = $mc->row(); 
			$data['forma'] .=	form_hidden("mcodigo",$mod);
			$introdato=array(
					'titulo'  => $sal->titulo, 
					'mensaje' => $sal->mensaje, 
					'panel'   => $sal->panel,
					'ejecutar'=> $sal->ejecutar,
					'target'  => $sal->target,
					'visible' => $sal->visible);
		}else{
			$introdato=array(
					'titulo'  => '',
					'mensaje' => '',
					'panel'   => '',
					'ejecutar'=> '',
					'target'  => '',
					'visible' => '');
		}
		if($introdato['visible']=='S') $introdato['visible']=TRUE; else $introdato['visible']=FALSE;
		$opcion = array("_self"=>"Ventana Actual","_blank"=>"Nueva Ventana","NO"=>"No Mostrar");
		$data['forma'] .=form_input(array('name'=>'mmodulo', 'maxlength'=>'10', 'value'=>$mod,'size'=>'7')).' Visible:'.form_checkbox('mvisible', 'S', $introdato['visible'])."</td>\n";
		$data['forma'] .="</tr><tr bgcolor=\"#EEEEEE\"><th>Titulo:</th><td>";
		$data['forma'] .=form_input(array("name"=>"mtitulo", "maxlength"=>"30", "value"=>$introdato['titulo'], "size"=>"40"))."</td>\n";
		$data['forma'] .="</tr><tr><th>Mensaje:</th>\n<td>";
		$data['forma'] .=form_input(array("name"=>"mmensaje", "maxlength"=>"60", "value"=>$introdato['mensaje'], "size"=>"40"))."</td>\n";
		$data['forma'] .="</tr>";
		if ( strlen($mod) > 1) { 
			$data['forma'] .='<tr  bgcolor="#EEEEEE"><th>Objetivo:</th><td>';
			$data['forma'] .=form_dropdown("mtarget", $opcion, $introdato['target'])."</td>\n";
			$data['forma'] .="</tr><tr><th>Panel: </th>\n";
			$data['forma'] .="<td>".form_input(array("name"=>"mpanel", "maxlength"=>"80", "value"=>$introdato['panel'], "size"=>"40"))."</td>\n";
			$data['forma'] .='</tr><tr><th bgcolor="#EEEEEE">Ejecutar: </th>';
			$data['forma'] .="<td>".form_input(array("name"=>"mejecutar", "maxlength"=>"80", "value"=>$introdato['ejecutar'], "size"=>"40"))."</td>\n";
			$data['forma'] .="</tr>";
		}
		$data['forma'] .="<tr><td colspan=\"2\" align=\"right\">\n";
		if ( $mod ){ 
		    $data['forma'] .=form_submit("accion", "Guardar"); 
		} else {
		    $data['forma'] .= form_submit("accion", "Agregar");
		}
		$data['forma'] .=	"\n</td></tr></table>\n";
		$data['forma'] .= form_close();
		$this->layout->buildPage('menu/agregar', $data);
	}
	
	function agregar(){
		if ($this->uri->segment(3) === FALSE) $cod = FALSE; else $cod = $this->uri->segment(3);
		$codis= $this->_coddisp($cod);
		$data['titulo1'] = " Agregar bajo el c&oacute;digo: $codis \n";
		
		//**
		$introdato=array(
				'titulo'  => null,
				'mensaje' => null,
				'panel'   => null,
				'ejecutar'=> null,
				'target'  => null);
		$opcion = array("_self"=>"Ventana Actual","_blank"=>"Nueva Ventana","NO"=>"No Mostrar");
		
		$data['forma']  = form_open("menu/guardar").form_hidden('mcodigo',$codis).form_hidden('mper',$cod);
		$data['forma'] .= "<table width='100%' class='bordetabla'>\n<tr bgcolor=\"#7799BE\"><td colspan=\"2\" class=\"titulo_tabla\">Propiedades</td>\n</tr><tr>\n";
		$data['forma'] .=	"<th>M&oacute;dulo: </th><td>$cod";
		$data['forma'] .= form_input(array("name"=>"mmodulo", "maxlength"=>"3","size"=>"3")).'* Visible '.form_checkbox('mvisible', 'S', TRUE);"</td>\n";
		$data['forma'] .= "</tr><tr bgcolor=\"#EEEEEE\"><th>Titulo:</th><td>";
		$data['forma'] .= form_input(array("name"=>"mtitulo", "maxlength"=>"30", "value"=>$introdato['titulo'], "size"=>"40"))."</td>\n";
		$data['forma'] .= "</tr><tr><th>Mensaje:</th>\n<td>";
		$data['forma'] .= form_input(array("name"=>"mmensaje", "maxlength"=>"60", "value"=>$introdato['mensaje'], "size"=>"40"))."</td>\n";
		$data['forma'] .= "</tr>";
		if ($cod!=FALSE) { 
			$data['forma'] .='<tr  bgcolor="#EEEEEE"><th>Objetivo:</th><td>';
			$data['forma'] .=form_dropdown("mtarget", $opcion, $introdato['target'])."</td>\n";
			$data['forma'] .="</tr><tr><th>Panel: </th>\n";
			$data['forma'] .="<td>".form_input(array("name"=>"mpanel", "maxlength"=>"80", "value"=>$introdato['panel'], "size"=>"40"))."</td>\n";
			$data['forma'] .='</tr><tr><th bgcolor="#EEEEEE">Ejecutar: </th>';
			$data['forma'] .="<td>".form_input(array("name"=>"mejecutar", "maxlength"=>"80", "value"=>$introdato['ejecutar'], "size"=>"40"))."</td>\n";
			$data['forma'] .="</tr>";
		}   
		$data['forma'] .="<tr><td colspan=\"2\" align=\"right\">\n";
		$data['forma'] .= form_submit("accion", "Agregar");
		$data['forma'] .=	"\n</td></tr></table>\n";
		$data['forma'] .= form_close();

		$this->layout->buildPage('menu/agregar', $data);
	}
	function eliminar(){
		$cod=$this->uri->segment(3);
		$mSQL="DELETE FROM intramenu WHERE codigo like '$cod%'";
		$this->db->simple_query($mSQL);
		redirect('/menu');
	}
	function guardar(){
		$this->datasis->modulo_id(16);
		if(!isset($_POST['mtarget']))   $_POST['mtarget']  =null;
		if(!isset($_POST['mpanel']))    $_POST['mpanel']   =null;
		if(!isset($_POST['mejecutar'])) $_POST['mejecutar']=null;
		if(!isset($_POST['mvisible']))  $_POST['mtarget']  =null;		
		$data = array('modulo'  => $_POST['mper'].$_POST['mmodulo'], 
									'titulo'  => $_POST['mtitulo'],
									'mensaje' => $_POST['mmensaje'],
									'target'  => $_POST['mtarget'],
									'panel'   => $_POST['mpanel'],
									'visible' => $_POST['mvisible'],
									'ejecutar'=> $_POST['mejecutar']);
									
		if($_POST['accion']=='Agregar'){
			$data['codigo'] = $_POST['mcodigo'];
			if (empty($_POST['mmodulo']))
				$data['modulo']=$_POST['mcodigo'];
			$mSQL = $this->db->insert_string('intramenu', $data);
		}else{
			$where = 'codigo='.$_POST['mcodigo'];
			$mSQL = $this->db->update_string('intramenu', $data, $where);
		}
		$data['mSQL']=$mSQL;
		$this->db->simple_query($mSQL);
		//$this->layout->buildPage('menu/guardar', $data);
		redirect('/menu');
	}
//*******************
//	Metodos Privados
//*******************

//genera la lista de modulos
	function _lista($mod=FALSE){
		$mSQL="SELECT titulo, modulo, codigo FROM intramenu WHERE MID(modulo,1,1)!= '0' ORDER BY modulo";
		$mc = $this->db->query($mSQL);
		if( strlen($mod)>1) $esde=$mod[0]; else $esde=$mod;
		$out  =  "<ul><li><b>Men&uacute;</b> <a href='".base_url()."index.php/menu/agregar'>".image('list-add.png','Agregar',array('border'=>'0'))."</a>".'</li><ul>';
		$o=$i=$u=0;
		foreach( $mc->result() as $row ){
			if(strlen($row->modulo)==1){
				if($i>0){ $out .= '</ul>';}
				if($u){ $out .= '</ul>'; $u=0; }
				if($esde===$row->codigo) $visible=''; else $visible='style="display: none;"';
				$out  .= "<li><a href='".base_url()."index.php/menu/editar/$row->codigo'>".image('editor.png','Editar',array('border'=>'0'))."</a> <a href='".base_url()."index.php/menu/agregar/$row->codigo'>".image('list-add.png','Agregar',array('border'=>'0'))."</a><a href='".base_url()."index.php/menu/eliminar/$row->codigo'>".image('list-remove.png','Eliminar',array('border'=>'0')).'</a>'.anchor('#',$row->codigo.'-'.$row->titulo,"onclick=\"Effect.toggle('ml$o', 'appear'); return false;\"")."</li><ul id='ml$o' $visible>";
				$o++;
			}elseif(strlen($row->modulo)>3){
				if (!$u){$out .= '<ul>';  }
				$u=1;
				$out .= "<li><a href='".base_url()."index.php/menu/editar/$row->codigo'>".image('editor.png','Editar',array('border'=>'0'))."</a><a href='".base_url()."index.php/menu/eliminar/$row->codigo'>".image('list-remove.png','Eliminar',array('border'=>'0')).'</a>'.$row->codigo.'-'.$row->titulo.'</li>';
			}else{
				if($u){ $out .= '</ul>'; $u=0; }
				$out .= "<li><a href='".base_url()."index.php/menu/editar/$row->codigo'>".image('editor.png','Editar',array('border'=>'0'))."</a> <a href='".base_url()."index.php/menu/agregar/$row->codigo'>".image('list-add.png','Agregar',array('border'=>'0'))."</a><a href='".base_url()."index.php/menu/eliminar/$row->codigo'>".image('list-remove.png','Eliminar',array('border'=>'0')).'</a>'.$row->codigo.'-'.$row->titulo.'</li>';
				$i++;
				$o++;
			}
		}
		$out .= '</ul></ul></ul>';
		return ($out);
	}
// Devuelve codigo disponible
	function _coddisp($mod=FALSE){
		$ac=FALSE;
		if($mod){
			$mSQl = "SELECT codigo FROM intramenu WHERE modulo LIKE '$mod%' AND CHAR_LENGTH(modulo) BETWEEN (CHAR_LENGTH('$mod')+2) AND (CHAR_LENGTH('$mod')+4) ORDER BY modulo";
			$mc = $this->db->query($mSQl);
			$cant = $mc->num_rows();
			if ($cant>0){
				$i = 1;	
				foreach ( $mc->result() as $sal ){
					$modis=$mod.str_pad($i, (strlen($mod)+3)/2, '0', STR_PAD_LEFT);
					if( $modis != $sal->codigo ){ $ac=TRUE;  break; }
					$i++;
				}   
			}else{
				$modis=$mod.str_pad(1, (strlen($mod)+3)/2, '0', STR_PAD_LEFT);
				$ac=TRUE;
			}
		}else{
			$mSQL = 'SELECT codigo FROM intramenu WHERE CHAR_LENGTH(modulo)=1 order by modulo';
			$limite=15;
			$cursor = $this->db->query($mSQL);
			$cant = $cursor->num_rows();
			$modis = 1;	
			if ( $cant > 0 ) {
				foreach ( $cursor->result() as $sal ){
					if( $modis != $sal->codigo ){ $ac=TRUE;  break; }
					$modis++;
				}
			}
		}
		if ($ac)
			return ($modis);
		else
			return ($modis + 1);
	}
}
?>
