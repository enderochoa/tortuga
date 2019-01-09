<?php
/**
 * memoField - is a field that can contain a multiline text.
 * in his "create" and "modify" state is a textarea field
 * it prevent html conflict by htmlentities function..
 * if you need to acquire an html you can use textEditor field instead this one
 *
 * @package rapyd.components.fields
 * @author Felice Ostuni 
 * @license http://www.fsf.org/licensing/licenses/lgpl.txt LGPL
 * @version 0.9.6
 */

/**
 * memoField (textarea)
 *
 * @package    rapyd.components.fields
 * @author     Felice Ostuni
 * @access     public
 */
class textareaField extends objField{

  var $type = "textarea";
  var $readonly=FALSE;
  var $css_class = "textarea";
  var $tip       = "";
  
  function _getValue(){
    parent::_getValue();
  }
  
  function _getNewValue(){
    parent::_getNewValue();
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
          $output = '<span style="font-size:9px; width: 100%; height:100px; overflow: auto">'.nl2br(htmlspecialchars($this->value)).'</span>';  //I know I know.. 
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
          'style'       => $this->style,
        	'title'       => $this->tip
        );
        if($this->readonly) $attributes['readonly']='readonly';
        $output = form_textarea($attributes, $this->value) .$this->extra_output;
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