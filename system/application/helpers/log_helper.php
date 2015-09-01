<?php  if (!defined('BASEPATH')) exit('No direct script access allowed');

function logusu($modulo,$comentario){
	if(empty($modulo) OR empty($comentario))return FALSE; 
	$CI =& get_instance();
	$usr=$CI->session->userdata('usuario');
	$mSQL="INSERT INTO logusu (usuario,fecha,hora,modulo,comenta) VALUES ('$usr',CURDATE(),CURTIME(),'$modulo','$comentario')";
	return $CI->db->simple_query($mSQL);
	
}

function memowrite($comentario=NULL,$nfile='salida',$modo='wb'){
	if(empty($comentario)) return FALSE; 
	$CI =& get_instance();
	$CI->load->helper('file');
	if (!write_file("./system/logs/$nfile.log", $comentario,$modo)){
		return FALSE; 
	}
	return TRUE;
}
?>