<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

require_once("dataedit.php");

/**
 * DataEdit base class.
 *
 * @package    rapyd.components
 * @author     Andres Hocevar
 * @access     public
 */
class DataEdit2 extends DataEdit{
	
	function DataEdit2($title, $table){
		parent::DataEdit($title, $table);
	}
	
	function getval($obj){
	 	$name=$this->$obj->name;
	 	$requestValue = $this->ci->input->post($name);
	  if($requestValue === FALSE AND $this->_dataobject->loaded){
	  	$requestValue =$this->_dataobject->get($this->$obj->db_name);
	  	if(empty($requestValue)) $requestValue=FALSE;
	  }
	  return $requestValue;
	}
	
}
?>