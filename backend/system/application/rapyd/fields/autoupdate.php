<?php
/**
 * autoUpdateField - is a field widhout output
 * use it for add easily auto-updated fields (widthout: $field->insertValue, $field->updateValue)
 *
 * @package rapyd.components.fields
 * @author Felice Ostuni <felix@rapyd.com>
 * @license http://www.fsf.org/licensing/licenses/lgpl.txt LGPL
 * @copyright Copyright (c) 2006 Felice Ostuni - http://www.rapyd.com
 * @version 1.0
 */
 
 
/**
 * autoUpdateField
 *
 * @package    rapyd.components.fields
 * @author     Felice Ostuni <felix@rapyd.com>
 * @access     public
 * @version    1.0.1
 */
class autoupdateField extends objField{

  var $type = "auto";
  
 /**
  * PHP4 constructor.
  *
  * @access   public
  * @param    string   $name         the field name/identifier
  * @param    string   $insertValue  the default insert value value
  * @param    string   $updateValue  the default update value value
  * @return   void
  */
  function autoupdateField($name, $insertValue=null, $updateValue=null, $mode=null){

    parent::objField("", $name);
    $this->mode = $mode;
    $this->insertValue = $insertValue;
    $this->updateValue = $updateValue;
    
  }

  function _getValue(){
    parent::_getValue();
  }
  
  function _getNewValue(){
    parent::_getNewValue();
  }


  function build(){
    $this->output = "";
  }
    
}
?>