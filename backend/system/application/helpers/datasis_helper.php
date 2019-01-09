<?php  if (!defined('BASEPATH')) exit('No direct script access allowed');

function phpscript($file){
	$thisobject =& get_instance();
	$charset=$thisobject->config->item('charset');
	$path2file=site_url('recursos/scripts/'.$file);
	return '<script src="'. $path2file .'" type="text/javascript" charset="'.$charset.'"></script>' . "\n";
}

function nformat($numero,$num=null,$centimos=null,$miles=null){
	if(is_null($centimos)) $centimos = (is_null(constant("RAPYD_DECIMALS"))) ? ',' : RAPYD_DECIMALS;
	if(is_null($miles))    $miles    = (is_null(constant("RAPYD_THOUSANDS")))? '.' : RAPYD_THOUSANDS;
	if(is_null($num))      $num      = (is_null(constant("RAPYD_NUM")))      ?  2  : RAPYD_NUM;
	if(!$numero>0)
	$numero=0;
	if(is_numeric($numero))
	return number_format($numero,$num,$centimos,$miles);

}

function des_nformat($numero,$num=null,$centimos=null,$miles=null){
	if(is_null($centimos)) $centimos = (is_null(constant("RAPYD_DECIMALS"))) ? ',' : RAPYD_DECIMALS;
	if(is_null($miles))    $miles    = (is_null(constant("RAPYD_THOUSANDS")))? '.' : RAPYD_THOUSANDS;
	$numero=str_replace($miles,'',$numero);
	$numero=str_replace($centimos,'.',$numero);
	return floatval($numero);
}

function moneyformat($numero){
	return nformat($numero,2);
}

function des_moneyformat($numero){
	return des_nformat($numero);
}

function js_escape($string){
    $string=str_replace("\r",'',$string);
    $string=str_replace("\n",'',$string);
    $string=preg_replace('/\s\s+/', ' ', $string);
    $string=addslashes($string);
    $string=str_replace('<','\<',$string);
    $string=str_replace('>','\>',$string);
    $string=str_replace(';','\;',$string);
    //$string=str_replace('<',"'+String.fromCharCode(60)+'",$string);
    //$string=str_replace('>',"'+String.fromCharCode(62)+'",$string);
    $string='\''.$string.'\'';
    return $string;
}

	function citorif($rif) {
		if(strlen($rif)<9){
			$rif = substr($rif,0,1).str_pad(substr($rif,1),8,'0',STR_PAD_LEFT);
		}
		
        $retorno = preg_match("/^([VEJPG]{1})([0-9]{8}$)/", $rif);
        
        if ($retorno) {
            $digitos = str_split($rif);
           
            $digitos[8] *= 2; 
            $digitos[7] *= 3; 
            $digitos[6] *= 4; 
            $digitos[5] *= 5; 
            $digitos[4] *= 6; 
            $digitos[3] *= 7; 
            $digitos[2] *= 2; 
            $digitos[1] *= 3; 
            
            // Determinar dígito especial según la inicial del RIF
            // Regla introducida por el SENIAT
            switch ($digitos[0]) {
                case 'V':
                    $digitoEspecial = 1;
                    break;
                case 'E':
                    $digitoEspecial = 2;
                    break;
                case 'J':
                    $digitoEspecial = 3;
                    break;
                case 'P':
                    $digitoEspecial = 4;
                    break;
                case 'G':
                    $digitoEspecial = 5;
                    break;
            }
            
            $suma = (array_sum($digitos) ) + ($digitoEspecial*4);
            $residuo = $suma % 11;
            $resta = 11 - $residuo;
            
            $digitoVerificador = ($resta >= 10) ? 0 : $resta;
            
            return $rif.$digitoVerificador;
        }
        
        return $retorno;
    }
?>
