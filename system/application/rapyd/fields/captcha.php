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
 * captchaField
 *
 * @package    rapyd.components.fields
 * @author     Felice Ostuni
 * @access     public
 */
class captchaField extends objField{

  var $type = "captcha";
  var $rule = "required|captcha";
  
  var $css_class = "input";

	function captchaField($label, $name)
	{
		parent::objField($label, $name);	
  	//MYFW install
  	//Si la session n'est pas demare par la session rapyd (utilisation sans les session rapyd )
		
		if (session_id() == "") session_start();
		//$_SESSION['captcha_bg_path'] = $_SERVER["DOCUMENT_ROOT"].substr($this->rapyd->get_elements_path('captcha/background.png'),1);
	}


  function _getValue(){
    parent::_getValue();

  }
  
  function _getNewValue(){
    parent::_getNewValue();
  }

  function build(){
    if(!isset($this->size)){
      $this->size = 10;
    }
    $this->_getValue();
    
    $output = "";
    
    switch ($this->status){
    
      case "disabled":
      case "show":
      
         $output = "";
        break;

      case "create":
      case "modify":
      
        $value = "";

        $attributes = array(
          'name'        => $this->name,
          'id'          => $this->name,
          'type'        => "text",          
          'value'       => $value,
          'maxlength'   => $this->maxlength,
          'size'        => $this->size,
          'onclick'     => $this->onclick,
          'onchange'    => $this->onchange,
          'class'       => $this->css_class,
          'style'       => $this->style
          );
        $output  = '<img src="'.RAPYD_LIBRARIES.'captcha/captchaimg.php?'.time().'" style="vertical-align:middle;" />';
        $output .= form_input($attributes) . $this->extra_output;
        break;
      
      case "hidden":
        $attributes = array(
          'name'        => $this->name,
          'id'          => $this->name,
          'type'        => "hidden",          
          'value'       => $this->value);
        $output = form_input($attributes);     

        break;

        
      default:
    }
    $this->output = "\n".$output."\n";
  }
    
}
?>