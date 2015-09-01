<?php
/**
 * containerField - is a plain-text container (of fields) for forms
 *
 * @package rapyd.components.fields
 * @author Felice Ostuni <felix@rapyd.com>
 * @license http://www.fsf.org/licensing/licenses/lgpl.txt LGPL
 * @copyright Copyright (c) 2006 Felice Ostuni - http://www.rapyd.com
 * @version 1.0
 */
 
 
 /**
 * containerField
 *
 * @package    rapyd.components.fields
 * @author     Felice Ostuni <felix@rapyd.com>
 * @access     public
 * @version    1.0.1
 */
class containerField extends objField{

  var $type = "container";
  var $content_pattern = "";

  function containerField($name, $content=""){
    $label = $name;
    parent::objField($label, $name);
    $this->db_name = null;
    $this->content_pattern = $content;
    
    if (strpos($content,"#>")>0){
      $this->_parsePattern($content);
    }
  }


  function _getValue(){
  
    parent::_getValue();
    
    if(count($this->parsed_fields)>0)
    {

      if(isset($this->data) && $this->data->loaded)
      {
        
        $data = $this->data->get_all();
        foreach ($this->parsed_fields as $field_name)
        {

          $field_rel_regex  = '#^(.+)\[(.+)\]$#';
          if (preg_match($field_rel_regex, $field_name, $match)){
            if (isset($data[$match[1]][$match[2]])){
              $replace = $data[$match[1]][$match[2]];
            } else {
              $replace = RAPYD_FIELD_SYMBOL_NULL;
            }
            $this->content_pattern = str_replace("<#$field_name#>",$replace,$this->content_pattern);
          }
          
          if(isset($data[$field_name]))
          {
            $this->content_pattern = str_replace("<#$field_name#>",$data[$field_name],$this->content_pattern);
          }
        }
      }
    }
    $this->value = replaceFunctions($this->content_pattern);
  }

  function build(){

    $this->_getValue();
    
    $output = "";
    
    switch ($this->status){
    
      case "show":
      case "create":
      case "modify":
      
        $output = $this->value;
        break;
        
      case "hidden":
      
        $output = "";

        break;
        
      default:
    }
    $this->output = "\n".$output."\n";
  }
    
}
?>