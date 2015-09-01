<?php
/**
 * passwordField - is common input field (type=password) 
 * with ss encryption and frontend obsuration by *** chars
 *
 * @package rapyd.components.fields
 * @author Felice Ostuni
 * @license http://www.fsf.org/licensing/licenses/lgpl.txt LGPL
 * @version 0.9.6
 */
 
 
/**
 * passwordField
 *
 * @package    rapyd.components.fields
 * @author     Nick Crossland
 * @access     public
 */
class passwordField extends objField{

  var $type = "password";
  var $mode = "optionalupdate";
  
  var $encrypt = true;
  // We can choose not to encrypt the value by using $form->password->encrypt = false;
  var $show_null = '** Not set (null) **';
  var $show_empty = '** Not set **';
  var $show_mask_encrypted = '** Encrypted **';
  var $show_mask_hidden = '** Hidden **';

  var $css_class = "password";
  var $html=null;

  function _getValue(){
    parent::_getValue();
    
  }
  
  function _getNewValue(){
    parent::_getNewValue();
    if (isset($this->request[$this->name]) && $this->request[$this->name] != '' ){
        
        if ($this->encrypt)
        {
          $ci =& get_instance();
          $this->newValue = $ci->encrypt->hash($this->newValue, 'md5');
        } else {
          show_error("you must load the CI 'encrtypt' library");
        }
    } else {

      $this->db_name = null;
    }
  }



  function build(){
	  $this->html = new Html();
    if(!isset($this->size)){
      $this->size = 45;
    }
    //$this->style .= ";display:none";

    
    $this->_getValue();
    
    $output = "";
    
    switch ($this->status){
    
      case "disabled":
      case "show":
        if ( (!isset($this->value)) ){
          $output = $this->show_null;
        } elseif ($this->value == ''){
          $output = $this->show_empty;
        } else {  
          $output = (($this->encrypt)?$this->show_mask_encrypted:$this->show_mask_hidden);
        }
        break;

      case "create":
      
        $value = '';

        $attributes = array(
          'name'        => $this->name,
          'id'          => $this->name,
          'type'        => $this->type,          
          'value'       => '', // Do not show the value in modify form
          'maxlength'   => $this->maxlength,
          'size'        => $this->size,
          'onclick'     => $this->onclick,
          'onchange'    => $this->onchange,
          'class'       => $this->css_class,
          'style'       => $this->style);
        $output = form_input($attributes) . $this->extra_output;
        break;
      
      
      
      case "modify":
        rapydlib("prototype");
          
        $value = '';
      
        $output = '<span id="'.$this->name.'_message">'.(($this->encrypt)?$this->show_mask_encrypted:$this->show_mask_hidden)."</span>";
        $attributes = array(
          'name'        => $this->name,
          'id'          => $this->name,
          'type'        => $this->type,          
          'value'       => '', // Do not show the value in modify form
          'maxlength'   => $this->maxlength,
          'size'        => $this->size,
          'onclick'     => $this->onclick,
          'onchange'    => $this->onchange,
          'class'       => $this->css_class,
          'style'       => $this->style);
        $output .= '<span id="'.$this->name.'_field">'.form_input($attributes)."</span>";
        unset($attributes);
        
        $attributes = array(
          'name'        => $this->name . "CheckBox",
          'id'          => $this->name . "CheckBox",
          'value'       => 'True',
          'checked'     => (isset($_POST[$this->name . "CheckBox"])),
          'style'       => "vertical-align:middle;",
          'onchange'    => "javascript:".$this->name."_swich();"
          );
          
        $output .= '<span id="'.$this->name.'_checkbox">'.form_checkbox($attributes)." ".RAPYD_FIELD_TEXT_PASSWORD_CHANGE."</span>";
        
        $func = $this->name."_swich()";
        $massege_span = $this->name."_message";
        $field_span = $this->name."_field";
        $checkbox_span = $this->name."_checkbox";
        $checkbox = $this->name."CheckBox";

        
        $output .= $this->html->javascriptTag(
          "function $func {
             if ($('$checkbox').checked) {
              \$('$massege_span').hide()
              \$('$field_span').show()
             } else {
              \$('$massege_span').show()
              \$('$field_span').hide()
             }
           }
          $func");
        $output .= $this->extra_output;
        break;
        
      case "hidden":
      
        $output = form_hidden($this->name, $this->value);

        break;
        
      default:
    }
    $this->output = "\n".$output."\n";
  }
    
}
?>
