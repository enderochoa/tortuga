<?php if (!defined('BASEPATH')) exit('No direct script access allowed');
/**
 * Rapyd Components
 *
 * An open source library for CodeIgniter application development framework for PHP 4.3.2 or newer
 *
 * @package		rapyd.components
 * @author		Felice Ostuni
 * @license		http://www.fsf.org/licensing/licenses/lgpl.txt LGPL
 * @version		0.9.6
 * @filesource
 */
//require highlight_code function (from CI text helper)

function highlight_code_file($path, $begin_str=null, $end_str=null) {
  $output = "";
  $content = file_get_contents($path);
   
  if (isset($begin_str) && isset($end_str)){
  
    $begin_pos = strpos($content, $begin_str);
    $begin_len = strlen($begin_str);
    $end_pos   = strpos($content, $end_str);
    $subcontent = substr($content, $begin_pos + $begin_len, $end_pos - $begin_pos - $begin_len);  
    $output =  highlight_code($subcontent);
    
  } else {
    $output = highlight_code($content);
  }
  return $output;

}



?>