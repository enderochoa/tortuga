<?php  if (!defined('BASEPATH')) exit('No direct script access allowed');	


function form2uri($url,$parametros){
	if (substr($url, -1) != '/') $url=$url.'/';
	$out='';
	if (is_array($parametros)){
		foreach ($parametros as $value) {
  		$out .= "+this.form.$value.value+'/'";
		}
	}else
		$out="+this.form.$parametros.value+'/'";
	$out="'$url'$out";
	return (" location.href=$out;");
}

?>