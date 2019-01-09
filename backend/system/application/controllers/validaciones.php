<?php if (!defined('BASEPATH')) exit('No direct script access allowed');
//********************************************
// Definiciones de reglas para validar campos
//********************************************

class validaciones extends Controller {

	//Validar la cedula
	function chci($rifci){
		if (preg_match("/((^[VEJG][0-9]+[[:blank:]]*$)|(^[P][A-Z0-9]+[[:blank:]]*$))|(^[[:blank:]]*$)/", $rifci)>0){
			return TRUE;
		}else {
			$this->validation->set_message('chci', "El campo <b>%s</b> debe tener el siguiente formato V=Venezolano(a), E=Extranjero(a), G=Gobierno, P=Pasaporte o J=Juridico Como primer caracter seguido del n&uacute;mero de documento. Ej: V123456, J5555555, P56H454");
			return FALSE;
		}
	}

	function chcuentac($cuenta){
		$cuenta=trim($cuenta);
		if(strlen($cuenta)==0) return TRUE;
		$retorna=$this->datasis->dameval("SELECT COUNT(*) FROM cpla WHERE codigo='$cuenta'");
		if($retorna==0){
			$this->validation->set_message('chcuentac', 'La cuenta contable no es v&aacute;lida');
			return FALSE; 
		}else {
			return TRUE;
		}
	}

	function chrif($rif){
		if (preg_match("/(^[VEJG][0-9]{9}[[:blank:]]*$)|(^[[:blank:]]*$)/", $rif)>0){
			return TRUE;
		}else {
			$this->validation->set_message('chrif', "El campo <b>%s</b> debe tener el siguiente formato V=Venezolano(a), G=Gobierno, J=Juridico Como primer caracter seguido del n&uacute;mero de documento. Ej: V123456789, J123456789");
			return FALSE;
		}
	}

	function chhora($hora){
		if (preg_match("/(^([01][0-9]|2[0-3]):[0-5][0-9]:[0-5][0-9][[:blank:]]*$)|(^[[:blank:]]*$)/", $hora)>0){
			return TRUE;
		}else {
			$this->validation->set_message('chhora', "El dato introducido ('$hora') en el campo <b>%s</b> parece no corresponder con el formato [00-23]:[00-59]:[00-59]");
			return FALSE;
		}
	}
	
	function ipcaja($ubica){
		if (preg_match("/^([1-9]|[1-9][0-9]|1[0-9]{2}|2[0-4][0-9]|25[0-5])(\\.([0-9]{1,2}|1[0-9]{2}|2[0-4][0-9]|25[0-5])){3}$/", $ubica)>0){
			$cant=$this->datasis->dameval("SELECT COUNT(*) FROM caja WHERE ubica='$ubica'");
			if($cant!=0){
			 $this->validation->set_message('ipcaja', "La ip dada en el campo <b>%s</b> ya fue asignada a otro registro");
			 return FALSE;
			}
		}
		return TRUE;
	}
	
	function chporcent($porcen){
		if ($porcen<=100 AND $porcen>=0)
			return TRUE;
		$this->validation->set_message('chporcent', "El valor del campo <b>%s</b> debe estar entre 0 y 100");
		return FALSE;
	}
	                       
	function chfecha($validar,$formato=RAPYD_DATE_FORMAT){
		return TRUE;
	}
	
	//**********************
	//METODOS FUNCIONALES
	//**********************
	
	//Cambia el formato de fin de linea de linux a windows
	function eollw($cont){
		$_POST[$this->validation->_current_field]=preg_replace("/[\r]*\n/","\r\n",$cont);
		return true;
	}
	
	//Cambia el formato de fin de linea de windows a linux
	function eolwl($cont){
		$_POST[$this->validation->_current_field]=str_replace("\r\n","\n",$cont);
		return true;
	}

	function phpCode($code){
		$codeE = explode("\n", $code);
		$count_lines = count($codeE);
		$code='';
		foreach($codeE as $line =>$c){
			$l=str_pad($line+1,3,' ',STR_PAD_LEFT);
			$code.="$l: $c \n";
		}
		return highlight_string($code,1);
	}
}
