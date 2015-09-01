<?php
/**
 * dateField buided on jscalendar lib
 *
 * @package rapyd.components.fields
 * @author Felice Ostuni
 * @license http://www.fsf.org/licensing/licenses/lgpl.txt LGPL
 * @version 0.9.6
 */
 

require_once("datetime.php");    

/**
 * dateonlyField
 *
 * @package    rapyd.components.fields
 * @author     Felice Ostuni 
 * @author     FJozef Hribik
 * @access     public
 */

class dateonlyField extends dateField{
	var $dbformat="Ymd";
	
  function _getNewValue(){
    $this->_getValue();
    if (isset($this->request[$this->name])){
	if(!empty($this->value))
	$this->newValue = date($this->dbformat,timestampFromInputDate($this->value, $this->format));
	else
	$this->newValue =null;
    }
  }
    
}
?>