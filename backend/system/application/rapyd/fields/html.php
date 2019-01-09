<?php
/**
 * htmlField - is a field that can be used as code editor.
 * It's a replacer of textarea buided in javascript on tinyMCE library.<br />
 * It can acquire xhtml formatted text, and it's the ideal field for aquire an "article description"
 *
 * Important Note.. for keep compact the rapyd package.. tinyMCE (and his 2.000 files) is NOT included... you can download it from the autor website
 *
 * @package rapyd.components.fields
 * @author Felice Ostuni
 * @license http://www.fsf.org/licensing/licenses/lgpl.txt LGPL
 * @version 0.9.6
 */


/**
 * code editor, js replacement of textarea field.
 * based on tinyMCE
 *
 * @package    rapyd.components.fields
 * @author     Felice Ostuni
 * @access     public
 */
class htmlField extends objField{
  
  var $type = "html";

  var $css_class = "input";
  
 /**
  * if a data source (dataobject) exist, it get the current value of field
  * this class override this method for "formatting" purposes
  *
  * @access   private
  * @return   void
  */
  function _getValue(){
    parent::_getValue();
  }

 /**
  * if detect a $_POST["fieldname"] it acquire the new value
  * this class override this method for "formatting" purposes
  *
  * @access   private
  * @return   void
  */
  function _getNewValue(){
    parent::_getNewValue();
    if (isset($this->request[$this->name])){
      $this->newValue = entities_to_ascii($this->newValue); 
    }

  }


 /**
  * build (only) the field (widhout labels or borders)
  *
  * @access   public
  * @return   void
  */
  function build(){
    $output = "";
    
    if(!isset($this->cols)){
      $this->cols = 42;
    }
    if(!isset($this->rows)){
      $this->rows = 15;
    }
    
    $this->_getValue();
   
    switch ($this->status){
      case "disabled":
      case "show":
        if (!isset($this->value)) {
          $output = RAPYD_FIELD_SYMBOL_NULL;
        } elseif ($this->value == ""){
          $output = "";
        } else {  
          $output = '<div style=" font: 11px \'courier new\',tahoma; color: #111; width: 100%; overflow: auto"><pre>'.(htmlspecialchars($this->value)).'</pre></div>';
        }
        break;

      case "create":
      case "modify":
        
        $attributes = array(
          'name'        => $this->name,
          'id'          => $this->name,
          'cols'        => $this->cols,
          'rows'        => $this->rows,          
          'onclick'     => $this->onclick,
          'onchange'    => $this->onchange,
          'class'       => $this->css_class,
          'style'       => 'font-size:9px;font: 11px \'courier new\',tahoma; color: #111;');
        $output = form_textarea($attributes, $this->value) .$this->extra_output;
        break;
        
      case "hidden":
      
        $output = "";//form_hidden($this->name, $this->value);
        break;
        
      default:
    }
    $this->output = $output;
  }
    
}
?>