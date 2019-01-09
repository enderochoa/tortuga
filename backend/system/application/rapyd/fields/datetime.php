<?php
/**
 * dateField buided on jscalendar lib
 *
 * @package rapyd.components.fields
 * @author Felice Ostuni
 * @license http://www.fsf.org/licensing/licenses/lgpl.txt LGPL
 * @version 0.9.6
 */
 


require_once(RAPYD_PATH."helpers/datehelper.php");
require_once(RAPYD_PATH."helpers/html.php");

/**
 * dateField
 *
 * @package    rapyd.components.fields
 * @author     Felice Ostuni 
 * @author     FJozef Hribik
 * @access     public
 */

class dateField extends objField{

  var $type = "date";
  
  var $format;

  var $css_class = "input";
  
  var $readonly=FALSE;
  var $html=null;
  
  //costruttore
  function dateField($label, $name, $format=RAPYD_DATE_FORMAT){

    $format = locale_to_format($format);

    parent::objField($label, $name);
    $this->format = $format;
   
    $this->extra_output = ""; //RAPYD_DATE_FORMAT;
    $this->html= new Html();
  }


  function _getValue(){
    
    parent::_getValue();
    
  }
  
  function _getNewValue(){
    parent::_getNewValue();
    if (isset($this->request[$this->name])){
      $this->newValue = human_to_dbdate($this->newValue, $this->format); 
    }
  }



  function build(){
  
    $this->_getValue();  
    $output = "";
  
    rapydlib("jscalendar");
  

    if(!isset($this->size)){
      $this->size = 25;
    }

   
  
    switch ($this->status){
      case "show":
        if (!isset($this->value)) {
          $value = RAPYD_FIELD_SYMBOL_NULL;
        } elseif ($this->value == ""){
          $value = "";
        } else {  
          $value = dbdate_to_human($this->value, $this->format);
        }
        $output = $value;
        break;

      case "create":
      case "modify":
        
        $value = "";
        
        //jscalendar integration
        if ($this->value != ""){
           if ($this->is_refill){             
             $value = $this->value;
           } else {
             $value = dbdate_to_human($this->value, $this->format);
           }
        }

        $attributes = array(
          'name'        => $this->name,
          'id'          => $this->name,
          'value'       => $value,
          'size'        => $this->size,
          'onclick'     => $this->onclick,
          'onchange'    => $this->onchange,
          'class'       => $this->css_class,
          'style'       => $this->style);
        if($this->readonly) $attributes['readonly']='readonly';
        $output  = form_input($attributes); //'<div>'.
        $output .= ' <img src="'.RAPYD_LIBRARIES.'jscalendar/calender_icon.gif" id="'.$this->name.'_button" border="0" style="vertical-align:middle;" />'.$this->extra_output;
        $output .= $this->html->javascriptTag('
         Calendar.setup({
        inputField  : "'.$this->name.'",
        ifFormat    : "'.datestamp_from_format($this->format).'",
        button      : "'.$this->name.'_button",
        align       : "Bl",
        singleClick : false,
        mondayFirst : true,
        weekNumbers : false
       });');
        
        break;

        
        
      case "disabled":
      
        //versione encoded 
        $output = dbdate_to_human($this->value, $this->format);
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
