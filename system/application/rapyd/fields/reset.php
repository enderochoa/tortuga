<?php
/**
 * resetField - is common submit input field
 *
 * @package rapyd.components.fields
 * @author Felice Ostuni <felix@rapyd.com>
 * @license http://www.fsf.org/licensing/licenses/lgpl.txt LGPL
 * @copyright Copyright (c) 2006 Felice Ostuni - http://www.rapyd.com
 * @version 1.0
 */
 
 
 /**
 * submitField
 *
 * @package    rapyd.components.fields
 * @author     Felice Ostuni <felix@rapyd.com>
 * @access     public
 * @version    1.0.1
 */
class resetField extends objField{

  var $type = "reset";
  

  function _getValue(){
  }
  
  function _getNewValue(){
  }

  function build(){
    
    $output = "";
    
    switch ($this->status){
    
      case "disabled":
      case "show":
        break;

      case "create":
      case "modify":

                  
        $output = '<input type="reset" value="'.form_prep($this->label).'">';//ci do not have form helper for resets
        break;
        
      case "hidden":
        break;
        
      default:
    }
    $this->output = "\n".$output."\n";
  }
    
}
?>