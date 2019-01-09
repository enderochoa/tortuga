<?php
/**
 * selectField - is full implementation of select field
 * it has methods to load options from DB.. or you can pass the options by an array.. or you can mix
 *
 * @package rapyd.components.fields
 * @author Felice Ostuni
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
class dropdownField extends objField{

  var $type = "select";
  var $description = "";
  var $clause = "where";
  var $tip       = "";
  
  var $css_class = "select";

  //costruttore
  function dropdownField($label, $name, $options=array(), $selected=""){

    parent::objField($label, $name);

    if ( (count($this->options)>0) && (count($options)>0) ){
      $this->options = array_merge($this->options, $options);
    } else {
      $this->options = $options;
    } 
    
  }
  
  function _getValue(){
    parent::_getValue();
    foreach ($this->options as $value=>$description){
      if ($this->value == $value){
        $this->description = $description;
      }
    }
  }
  
  function _getNewValue(){
    parent::_getNewValue();
  }


  function build(){
    if(!isset($this->style)){
			$this->style = "width:290px;";
    }

		
    $this->_getValue();
    $output = "";
  
    switch ($this->status){
      case "disabled":
      case "show":
        if (!isset($this->value)){
          $output = RAPYD_FIELD_SYMBOL_NULL;
        } else {  
          $output = $this->description;
        }
        break;
        
      case "create":
      case "modify":
        $onchange = "";
				$style = "";
				
        if ($this->onchange!=""){
          $onchange = ' onchange="'.$this->onchange.'"';
        }
        $id = 'id="'.$this->name.'"';
				
        if ($this->style!=""){
          $style = ' style="'.$this->style.'"';
          
        }
         $class = ' class="select"';
         $title = " title='".$this->tip."'";
         $output = form_dropdown($this->name, $this->options, $this->value, $id.$onchange.$style.$class.$title). $this->extra_output;

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