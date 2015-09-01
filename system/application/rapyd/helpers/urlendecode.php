<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

function raencode($parametro){
	$parametro = urlencode($parametro);
	$parametro = str_replace("%", "-porce-", $parametro);
	return $parametro;
}

function radecode($parametro){
	$parametro = str_replace("-porce-","%", $parametro);
	$parametro = urldecode($parametro);
	return $parametro;
}

function racheck($parametro){
  return 1;
}
?>