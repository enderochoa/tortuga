<?php
/**
 * submitField - is common submit input field
 *
 * @package rapyd.components.fields
 * @author Felice Ostuni
 * @license http://www.fsf.org/licensing/licenses/lgpl.txt LGPL
 * @version 0.9.6
 */
 
 
 /**
 * submitField
 *
 * @package    rapyd.components.fields
 * @author     Felice Ostuni
 * @access     public
 */
class submitField extends objField{

  var $type = "submit";
  

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

                  
        $output = form_submit($this->name, $this->label);
        break;
        
      case "hidden":
        break;
        
      default:
    }
    $this->output = "\n".$output."\n";
  }
    
}
?>