<?php
/**
 * textEditor - is a field that can be used as wysiwyg editor.
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
 * wysiwyg editor, js replacement of textarea field.
 * based on tinyMCE
 *
 * @package    rapyd.components.fields
 * @author     Felice Ostuni
 * @access     public
 */
class editorField extends objField{
  
  var $type = "editor";
	var $upload_path = '/uploads/';
	var $content_css ="";

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
  }


 /**
  * build (only) the field (widhout labels or borders)
  *
  * @access   public
  * @return   void
  */
  function build(){
  
    $output = "";
    $GLOBALS["Editor_UserFilesPath"]=$this->upload_path;
    $GLOBALS["content_css"]=($this->content_css!="")? $this->content_css : RAPYD_LIBRARIES.'tinymce/custom.css';
    rapydlib("tinymce");

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
          $output = '<div style="font-size:9px; width: 100%; height:100px; overflow: auto">'.(htmlspecialchars($this->value)).'</div>';
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
          'class'       => "mceEditor",
          'style'       => $this->style);
        $output = form_textarea($attributes, $this->value);
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