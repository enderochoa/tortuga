<?php
/**
 * textField - is common input field (type=text)
 *
 * @package rapyd.components.fields
 * @author Felice Ostuni
 * @license http://www.fsf.org/licensing/licenses/lgpl.txt LGPL
 * @version 0.9.6
 */
 
 
 /**
 * textField
 *
 * @package    rapyd.components.fields
 * @author     Felice Ostuni
 * @access     public
 */
class inputField2 extends objField{

  var $type = "text";
  var $readonly=FALSE;
  var $css_class = "input";

  function _getValue(){
    parent::_getValue();
  }
  
  function _getNewValue(){
    parent::_getNewValue();
  }

  function build(){
    if(!isset($this->size)){
      $this->size = 45;
    }
    $this->_getValue();
    
    $output = "";
    
    switch ($this->status){
    
      case "disabled":
      case "show":
        if ( (!isset($this->value)) ){
          $output = RAPYD_FIELD_SYMBOL_NULL;
        } elseif ($this->value == ""){
          $output = "";
        } else {  
          $output = nl2br(htmlspecialchars($this->value));
        }
        break;

      case "create":
      case "modify":
      
        $value = ($this->type == "password")? "": $this->value;

        $attributes = array(
          'name'        => $this->name,
          'id'          => $this->name,
          'type'        => $this->type,          
          'value'       => $value,
          'maxlength'   => $this->maxlength,
          'size'        => $this->size,
          'onclick'     => $this->onclick,
          'onchange'    => $this->onchange,
          'class'       => $this->css_class,
          'style'       => $this->style
          );
		if($this->readonly) $attributes['readonly']='readonly';
        $output = form_input($attributes) . $this->extra_output;
        break;

      case "hidden":
        $attributes = array(
          'name'        => $this->name,
          'id'          => $this->name,
          'type'        => "hidden",          
          'value'       => $this->value);
        $output = form_input($attributes) . $this->extra_output;     

        break;

        
      default:
    }
    $this->output = "\n".$output."\n";
  }
    
}
?>