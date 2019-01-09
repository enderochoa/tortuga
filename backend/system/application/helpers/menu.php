<?php  if (!defined('BASEPATH')) exit('No direct script access allowed');

//Menu principal 1 nivel
function p_menu(){
	$CI =& get_instance();
	$CI->load->database('default',TRUE);
	if ($CI->session->userdata('logged_in')){
		$retval['menu'] .= "\t<ul>\n";
		$usr=$CI->session->userdata('usuario');
		if ($CI->datasis->essuper())
			$mSQL = "SELECT modulo, titulo, mensaje,target  FROM intramenu  WHERE  CHAR_LENGTH(modulo)=1 AND visible='S' ORDER BY modulo";
		else
			$mSQL = "SELECT a.modulo, a.titulo, a.mensaje, a.target  FROM intramenu AS a JOIN intrasida AS b ON a.modulo=b.modulo AND a.visible='S' WHERE CHAR_LENGTH(a.modulo)=1 AND b.usuario='$usr' AND b.acceso='S' ORDER BY a.modulo";
		$query = $CI->db->query($mSQL);
		
		$retorna=$query->result_array();
	}else{
		$retorna=array();
	}

	return $retorna;
}

//Sub Menu 2 nivel
function s_menu(){
	$CI =& get_instance();
	$CI->load->database('default',TRUE);
	if ($CI->session->userdata('logged_in')){
		$retval['menu'] .= "\t<ul>\n";
		$usr=$CI->session->userdata('usuario');
		if ($CI->datasis->essuper())
			$mSQL = "SELECT modulo, titulo, mensaje,target  FROM intramenu  WHERE  CHAR_LENGTH(modulo)=1 AND visible='S' ORDER BY modulo";
		else
			$mSQL = "SELECT a.modulo, a.titulo, a.mensaje, a.target  FROM intramenu AS a JOIN intrasida AS b ON a.modulo=b.modulo AND a.visible='S' WHERE CHAR_LENGTH(a.modulo)=1 AND b.usuario='$usr' AND b.acceso='S' ORDER BY a.modulo";
		$query = $CI->db->query($mSQL);
		
		$retorna=$query->result_array();
	}else{
		$retorna=array();
	}

	return $retorna;
}

//Menu Interno 3 nivel
function i_menu($modulo=NULL){
	$CI =& get_instance();
	$att = array(	'width' => 800,
			'heigth'     => 600,
			'scrollbars' => 'Yes',
			'status'     => 'Yes',
			'resizable'  => 'Yes',
			'screenx'    => "'+((screen.availWidth/2)-400)+'",
			'screeny'    => "'+((screen.availHeight/2)-300)+'" );
	$CI->load->database('default',TRUE);
	
	$bote = array();
	$usr=$CI->session->userdata('usuario');
	if ( strlen($modulo) == 1 ){
		if ($this->essuper())
			$mSQL = "SELECT modulo, titulo, mensaje, panel, ejecutar,target FROM intramenu WHERE MID(modulo,1,1) = '$modulo' AND CHAR_LENGTH(modulo)>1  AND CHAR_LENGTH(modulo)<4 AND visible='S' ORDER BY panel, modulo";
		else
			$mSQL = "SELECT a.modulo, a.titulo, a.mensaje, a.panel, a.ejecutar,target FROM intramenu as a JOIN intrasida as b ON a.modulo=b.modulo AND MID(a.modulo,1,1) = '$modulo' WHERE visible='S' AND CHAR_LENGTH(a.modulo)>1  AND CHAR_LENGTH(a.modulo)<4 AND b.acceso='S' AND b.usuario='$usr' ORDER BY a.panel, a.modulo";
	}else
		$mSQL = "SELECT modulo, titulo, mensaje, panel, ejecutar,target FROM intramenu WHERE MID(modulo,1,1) = '0' AND visible='S' ORDER BY panel, modulo";
	$query = $CI->db->query($mSQL);
	
	return;
}

?>