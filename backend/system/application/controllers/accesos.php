<?php

class Accesos extends Controller{
	function Accesos(){
		parent::Controller();
	}

	function index(){
		$this->session->set_userdata('panel', 9);
		$this->datasis->modulo_id(15,1);

		// Set the template valiables
		$mSQL='SELECT us_codigo, CONCAT( us_codigo,\' - \' ,us_nombre ) FROM usuario WHERE us_nombre != \'SUPERVISOR\' ORDER BY us_codigo';
		$dropdown=$this->datasis->consularray($mSQL);
		$data['content']  = form_open('accesos/crear');
		$data['content'] .= form_dropdown('usuario',$dropdown);
		$data['content'] .= form_submit('pasa','Aceptar');
		$data['content'] .= form_close();
		// Build the thing
		//$this->layout->buildPage('accesos/home', $data);

		$data['head']    = '';
		$data['title']   = 'Administraci&oacute;n de accesos';
		$this->load->view('view_ventanas', $data);
	}

	function crear(){
		$this->datasis->modulo_id(15,1);

		if (isset($_POST['usuario']))
			$usuario = $_POST['usuario'];
		else
			$usuario = $this->uri->segment(3);
		if (empty($usuario)) 
			redirect('/accesos');
		if(isset($_POST['copia']))
			$copia=$_POST['copia'];
		else
			$copia='';

		// Set the template valiables
		$mSQL='SELECT us_codigo, CONCAT( us_codigo,\' - \' ,us_nombre ) FROM usuario WHERE us_nombre != \'SUPERVISOR\' ORDER BY us_codigo';
		$dropdown=$this->datasis->consularray($mSQL);
		$data['title'] = ' Accesos del usuario: '.$usuario.' ';
		$data['content']  = form_open('accesos/crear');
		$data['content'] .= 'Copiar de: '.form_dropdown('copia',$dropdown,$copia);
		$data['content'] .= form_submit('pasa','Copiar');
		$data['content'] .= form_hidden('usuario',$usuario).form_close();

		$query = $this->db->query("SELECT us_nombre FROM usuario WHERE us_codigo='$usuario'");
		if($query->num_rows() == 1){
			if(!empty($copia))
				$acceso=$copia;
			else
				$acceso=$usuario;

			$mSQL="SELECT aa.modulo,aa.titulo, aa.acceso,bb.panel,aa.id FROM
			(SELECT a.modulo,a.titulo, IF(b.acceso IS NULL ,'N',b.acceso) AS acceso ,a.panel,a.id
			FROM intramenu AS a
			LEFT JOIN intrasida AS b ON a.id=b.id AND b.usuario=".$this->db->escape($acceso)."
			WHERE MID(a.modulo,1,1)!='0') AS aa
			LEFT JOIN intramenu AS bb ON MID(aa.modulo,1,3)=bb.modulo
			ORDER BY MID(aa.modulo,1,1), IF(LENGTH(aa.modulo)=1,0,1),bb.panel,MID(aa.modulo,2,2), MID(aa.modulo,2)";

			/*$mSQL="SELECT a.modulo,a.titulo, IFNULL(b.acceso,'N') AS acceso ,a.panel,a.id
			FROM intramenu AS a
			LEFT JOIN intrasida AS b ON a.id=b.id AND b.usuario='$acceso'
			WHERE MID(a.modulo,1,1)!=0 ORDER BY MID(a.modulo,1,1),CHAR_LENGTH(TRIM(a.modulo)) ,a.panel, MID(a.modulo,2,3)";*/
             
			$mc = $this->db->query($mSQL);
			$data['content'].=form_open('accesos/guardar').form_hidden('usuario',$usuario).'<div id=\'ContenedoresDeData\'><table width=100% cellspacing="0">';
			$i=0;
			$panel = '';
			foreach( $mc->result() as $row ){
				if($row->acceso=='S') $row->acceso=TRUE; else $row->acceso=FALSE;
				
				if(strlen($row->modulo)==1) {
					$data['content'] .= '<tr><th colspan=2>('.$row->id.') '.$row->titulo.form_checkbox('accesos['.$i.']',$row->id,$row->acceso).'</th></tr>';
					$panel = '';
					$i++;
				}elseif( strlen($row->modulo)==3 ) {
					if ($panel <> $row->panel ) {
					    $data['content'] .= '<tr><td colspan=2 bgcolor="#CCDDCC">'.$row->panel.'</td></tr>';
					    $panel = $row->panel ;
					};

					//$data['content'] .= '<tr><td>'.$row->titulo.'</td><td>'.form_checkbox('accesos['.$i.']',$row->modulo,$row->acceso).'</td></tr>';
					$data['content'] .= '<tr><td>('.$row->id.') '.$row->modulo.' '.$row->titulo.'</td><td>'.form_checkbox('accesos['.$i.']',$row->id,$row->acceso).'</td></tr>';
					$i++;
				}else{
					//$data['content'] .= '<tr><td><b>&nbsp;&nbsp;-&nbsp;</b>'.$row->titulo.'</td><td>'.form_checkbox('accesos['.$i.']',$row->modulo,$row->acceso).'</td></tr>';
					$data['content'] .= '<tr><td><b>&nbsp;&nbsp;-&nbsp;</b>('.$row->id.') '.$row->titulo.'</td><td>'.form_checkbox('accesos['.$i.']',$row->id,$row->acceso).'</td></tr>';
					$i++;
				}
			}
			$data['content'].='</table></div>';
			$data['content'].=form_hidden('usuario',$usuario).form_submit('pasa','Guardar').form_close().anchor('/accesos','Regresar');;     
		}else
			$data['content']='Usuario no V&aacute;lido, por favor selecione un usuario correcto.';

		$data['head']    = style('estilos.css');
		$data['title']   = " Administraci&oacute;n de accesos, usuario <b>$usuario</b> ";
		$this->load->view('view_ventanas', $data);
	}

	function guardar(){
		$this->datasis->modulo_id(18);
		$usuario = $_POST['usuario'];
		$desp=opciones_nivel(1);
		$modprin=null;
		$mSQL="DELETE FROM intrasida WHERE usuario='$usuario'";
		$this->db->simple_query($mSQL);

		if (count($_POST['accesos']) > 0 ){
			foreach( $_POST['accesos'] as $codigo ){
				//if($modprin != substr($codigo,0,$desp)){
				//	$modprin=substr($codigo,0,$desp);
				//	$mSQL="INSERT INTO intrasida (usuario,id,acceso) VALUES('$usuario','$modprin' ,'S')";
				//	$this->db->simple_query($mSQL);
				//	echo $mSQL."\n";
				//}
				$mSQL="INSERT INTO intrasida (usuario,id,acceso) VALUES('$usuario','$codigo' ,'S')";
				$this->db->simple_query($mSQL);
				//echo $mSQL."\n";
			}
		}

		$data['head']    = style('estilos.css');
		$data['title']   = " Accesos Guardados para el usuario: $usuario ";
		$data['content'] = anchor('/accesos','Regresar');
		$this->load->view('view_ventanas', $data);

		//$data['titulo1'] = " Accesos Guardados para el usuario: $usuario \n";
		//$data['vaina'] = $_POST;
		//// Build the thing
		//$this->layout->buildPage('accesos/guardar', $data);
	}
}


