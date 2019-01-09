<?php
/**
 * selectField - is full implementation of select field
 * it has methods to load options from DB.. or you can pass the options by an array.. or you can mix
 *
 * @package rapyd.components.fields
 * @author Felice Ostuni <felix@rapyd.com>
 * @license http://www.fsf.org/licensing/licenses/lgpl.txt LGPL
 * @version 0.9.6
 */
 
 
/**
 * selectField
 *
 * @package    rapyd.components.fields
 * @author     Felice Ostuni
 * @access     public
 */
class checkboxField extends objField{

  var $checked = false;
  var $type = "checkbox";
  var $style = "vertical-align:middle";
  
  //costruttore
  function checkboxField($label, $name, $true_value, $false_value=""){
  
    parent::objField($label, $name);
    
    $this->true_value = $true_value;
    $this->false_value = $false_value;
  }
  
  function _getValue(){
    parent::_getValue();

    if ($_POST && !isset($this->request[$this->name])){
     $this->value = $this->false_value;
    }
    
    if ($this->value == $this->true_value){
      $this->checked = true;
    } else {
      $this->checked = false;    
    }

  }
  
  function _getNewValue(){
    parent::_getNewValue();
    if (!isset($this->request[$this->name])){
     $this->newValue = $this->false_value;
    }

  }


  function build(){
    
    $output = "";
    
    $this->_getValue();
  
    switch ($this->status){
      case "disabled":
      case "show":
        if (!isset($this->value)){
          $output = RAPYD_FIELD_SYMBOL_NULL;
        } elseif ($this->value == ""){
          $output = "";
        } else {  
          $output = $this->value;
        }
        break;
        
      case "create":
      case "modify":
	      $onchange = "";
        $onclick = "";
        if ($this->onchange!=""){
          $onchange = ' onchange="'.$this->onchange.'"';
        }
        if ($this->onclick!=""){
          $onclick = ' onclick="'.$this->onclick.'"';
        }
        $id = ' id="'.$this->name.'"';
        
        $output = form_checkbox($this->name, $this->true_value , $this->checked, $id.$onchange.$onclick).$this->extra_output;
        break;
        
      case "hidden":
      
        $output = form_hidden($this->name, $this->value);
        
        break;
        
      default:
    }
    $this->output = $output;
  }
    
}
?>