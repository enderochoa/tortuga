<?php if (!defined('BASEPATH')) exit('No direct script access allowed');


require_once("datafilter.php");

/**
 * DataEdit base class.
 *
 * @package    rapyd.components
 * @author     Andres Hocevar
 * @access     public
 */
class DataFilter2 extends DataFilter{

  function DataFilter2($title=null, $table=null){
    parent::DataFilter($title, $table);
  }

  function process(){
  
    $result = parent::process();
   
    switch($this->_action){
      
      case "search":

        // prepare the WHERE clause
        foreach ($this->_fields as $fieldname=>$field){
        
          if ($field->value!=""){
                        
            if (strpos($field->name,"_copy")>0){
              $name = substr($field->db_name,0,strpos($field->db_name,"_copy"));
            } else {
              $name = $field->db_name;
            }
            
            $field->_getValue();
            $field->_getNewValue();
            $value = $field->newValue;
           
            switch ($field->clause){  
                case "in":
                    $this->db->where("'$value' IN $name");
                break;
                
                case "likesensitive":
                    $this->db->where("$name LIKE '%$value%' COLLATE ".$this->db->char_set."_bin");
                break;
                
                case "likerigth":
                    $this->db->where("$name LIKE '$value%'");
                break;
                case "likeonly":
                    $this->db->where("$name LIKE '$value'");
                break;
                case "likeleft":
                    $this->db->where("$name LIKE '%$value'");
                break;
              //..
            
            }
            
          }
        }
        
        $this->_build_buttons();
        $this->build_form();
      break;
      
      case "reset":
        //pulire sessioni 
        
        $this->_build_buttons();
        $this->build_form();
      break;
      
      default:
        $this->_build_buttons();
        $this->build_form();
      break;      
    }
    
  }
  
  function getval($obj){
	 	$name=$this->$obj->name;
	 	$requestValue = $this->ci->input->post($name);

	  return $requestValue;
	}

}

?>