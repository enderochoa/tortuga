<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

require_once("dataobject2.php");

/**
 * dataobject2 base class.
 *
 * @package    rapyd.components
 * @author     Andres Hocevar
 * @access     public
 */
class DataObject2 extends DataObject{
	
	function DataObject2($title, $table){
		parent::DataObject($title, $table);
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