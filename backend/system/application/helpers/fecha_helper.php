<?php  if (!defined('BASEPATH')) exit('No direct script access allowed');

function mesLetra($mes){
	$CI =& get_instance();      
	$CI->lang->load('calendar');
	$mes=intval($mes);

	$mod=abs($mes%12);
	$ind= ($mod==0) ? ($mes>12 OR $mes==0)? 12: $mes : $mod;
	$meses=array('january','february','march','april','mayl','june','july','august','september','october','november','december');
	return $CI->lang->line('cal_'.$meses[$ind-1]);
}
?>