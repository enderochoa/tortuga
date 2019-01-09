<?php
if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

class MY_Validation extends CI_Validation
{

	var $_dataobject;

	

	/**
	 * Unique
	 *
	 * @access	public
	 * @param	string
	 * @return	bool
	 */
	function unique($str)
	{
		
    return ( $this->_dataobject->is_unique($this->_current_field, $str));
	}
	

	/**
	 * captcha
	 *
	 * @access	public
	 * @param	string
	 * @return	bool
	 */
	function captcha($str)
	{
    return ( strtolower($_SESSION["captcha"]) == strtolower($str) );
	}

	function chfecha($validar,$format=null,$fname=null){
		$formato= (empty($format))? RAPYD_DATE_FORMAT: $format;
		$fnombre= (empty($fname)) ? 'chfecha' : $fname;
		$formato=preg_quote($formato,'/');

		$search[] = "d"; $replace[] = "(0[1-9]|[1-2][0-9]|3[0-1])";
		$search[] = "j"; $replace[] = "([1-9]|[1-2][0-9]|3[0-1])";
		$search[] = "m"; $replace[] = "(0[1-9]|1[0-2])";
		$search[] = "n"; $replace[] = "([1-9]|1[0-2])";
		$search[] = "Y"; $replace[] = "([0-9]{4})";
		$search[] = "y"; $replace[] = "([0-9]{2})";
		$search[] = "H"; $replace[] = "([0-1][0-9]|2[0-4])";
		$search[] = "i"; $replace[] = "(0[0-9]|[1-5][0-9]|60)";
		$search[] = "s"; $replace[] = "(0[0-9]|[1-5][0-9]|60)";
		$pattern = str_replace($search, $replace, $formato);
		$pattern = '/'.$pattern.'/';
		$replace = $search = array();

		if(preg_match($pattern,$validar)>0){
			$search[] = "j"; $replace[] = "(?P<i>\d+)";
			$search[] = "d"; $replace[] = "(?P<i>\d+)";
			$search[] = "m"; $replace[] = "(?P<e>\d+)";
			$search[] = "n"; $replace[] = "(?P<e>\d+)";
			$search[] = "Y"; $replace[] = "(?P<a>\d+)";
			$search[] = "y"; $replace[] = "(?P<a>\d+)";

			$pattern = str_replace($search, $replace, $formato);
			$pattern = '/'.$pattern.'/';

			preg_match($pattern,$validar,$matches);

			$dia =(isset($matches['i']))? $matches['i'] : 1;
			$mes =(isset($matches['e']))? $matches['e'] : 1;
			$anio=(isset($matches['a']))? $matches['a'] : 1;

			if(!checkdate($mes,$dia,$anio)){
				$this->set_message($fnombre, "La fecha introducida en el campo <b>%s</b> no es v&aacute;lida");
				return false;
			}
		}else{
			$this->set_message($fnombre, "La fecha introducida en el campo <b>%s</b> no coincide con el formato");
			return false;
		}
		return true;
	}
	
	
}

?>
