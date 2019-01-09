<?php if (!defined('BASEPATH')) exit('No direct script access allowed');
/**
 * Rapyd Components
 *
 * An open source library for CodeIgniter application development framework for PHP 4.3.2 or newer
 *
 * @package		rapyd.components
 * @author		Felice Ostuni
 * @license		http://www.fsf.org/licensing/licenses/lgpl.txt LGPL
 * @version		0.9.6
 * @filesource
 */
 
/**
 * required classes
 */
require_once("dataobject.php");
require_once("dataform.php");

/**
 * DataEdit base class.
 *
 * @package    rapyd.components
 * @author     Felice Ostuni
 * @access     public
 */
class DataFilter extends DataForm{

  var $_buttons = array();
  
 /**
  * PHP4 constructor.
  *
  * @access   public
  * @param    string   $title  output title
  * @param    string   $table  table-name to be filtered (you can leave it empty.. and preset BEFORE a complex join active-record query)
  * @return   void
  */
  function DataFilter($title=null, $table=null){
    parent::DataForm();
    
      
    //prepare active record query ("select" and "from")
    if(isset($table)){
      $this->db->select('*');
      $this->db->from($table);
    }    
    //assign an output title
    $this->title($title);
    
    $this->session =& $this->rapyd->session; 

    //sniff current action
    $this->_sniff_action();

  }


  function _sniff_action(){
    
    //for each action the "reset" and "process" uri can be built in the same way
    $base_reset_uri   = $this->rapyd->uri->implode_uri("base_uri","gfid");
    $this->_reset_uri  = $this->rapyd->uri->add_clause($base_reset_uri, "reset");
    
    $base_process_uri   = $this->rapyd->uri->implode_uri("base_uri","gfid","orderby");
    $this->_process_uri  = $this->rapyd->uri->add_clause($base_process_uri, "search");
    
    ///// search /////
    if ($this->rapyd->uri->is_set("search")){
    
      $this->_action = "search";

      ## persistence
      
      if (count($_POST)<1){
        $persistence = $this->rapyd->session->get_persistence($this->rapyd->uri->implode_uri("base_uri"), $this->rapyd->uri->gfid);
        
        if ( isset($persistence["back_post"]) ){
           $_POST = unserialize($persistence["back_post"]);
        }
      } 

      $page = array (
        "back_post" => serialize($_POST),
        "back_uri" => $this->rapyd->uri->implode_uri()
      );
      $this->rapyd->session->save_persistence($this->rapyd->uri->implode_uri("base_uri"), $page, $this->rapyd->uri->gfid);
      
    ///// uri_search /////
    } elseif ($this->rapyd->uri->is_set("uri_search")) {
  
      $page = array (
        "back_post" => serialize($this->rapyd->uri->assoc_uri_clause("uri_search")),
        "back_uri" => $this->rapyd->uri->implode_uri()
      );
      $this->rapyd->session->save_persistence($this->rapyd->uri->implode_uri("base_uri"), $page, $this->rapyd->uri->gfid);
      
      redirect($this->_process_uri,'refresh');

      
    ///// reset /////
    } elseif ($this->rapyd->uri->is_set("reset")) {
            
      $this->_action = "reset";
      
      $this->rapyd->session->clear_persistence($this->rapyd->uri->implode_uri("base_uri"), $this->rapyd->uri->gfid);
      
      $page = array (
        "back_uri" => $this->rapyd->uri->implode_uri("base_uri","gfid","orderby")
      );
      $this->rapyd->session->save_persistence($this->rapyd->uri->implode_uri("base_uri"), $page, $this->rapyd->uri->gfid);
      
      
    ///// show /////
    } else {

      $page = array (
        "back_uri" => $this->rapyd->uri->implode_uri()
      );
      $this->rapyd->session->save_persistence($this->rapyd->uri->implode_uri("base_uri"), $page, $this->rapyd->uri->gfid);

    }
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
            
                case "like":
                    //$this->db->like($name, $value,$field->like_side);
                    $comodin=$this->ci->datasis->traevalor('COMODIN');
                    if(!empty($comodin)){
                       $v = str_replace($comodin,'%',$value);
                    }else{
                       $v = $value;
                    }
                    
                    if ($field->like_side == 'before'){
                        $v=$this->db->escape('%'.$v);
                    }elseif ($field->like_side == 'after'){
                        $v=$this->db->escape($v.'%');
                    }else{
                        $v=$this->db->escape('%'.$v.'%');
                    }
                    
                    $strlike="$name LIKE $v";
                    $this->db->where($strlike);
                break;
                
                case "orlike":
                    $this->db->orlike($name, $value);
                break;
            
                case "where":{
                	if(is_numeric($value)){
                		$value =1*$value;
						$bool  =FALSE;
                	}else{
						$bool=TRUE;
					}
                    $this->db->where($name." ".$field->operator, $value,$bool);
				}
                break;
                   
                case "orwhere":{
                	if(is_numeric($value)){
                		$value =1*$value;
						$bool  =FALSE;
                	}else{
						$bool=TRUE;
					}
                    $this->db->orwhere($name." ".$field->operator, $value);
				}
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


 /**
  * append a default button
  *
  * @access   public
  * @param    string  $name     a default button name ('modify','save','undo','backedit','back')
  * @param    string  $caption  the label of the button (if not set, the default labels will used)
  * @return   void
  */ 
  function crud_button($name="",$caption=null){
    $this->_buttons[$name]=$caption;
  }

  
 /**
  * append a set of default buttons
  *
  * @access   public
  * @param    mixed  $names   a list of button names.  For example 'modify','save','undo','backedit','back'
  * @return   void
  */ 
  function buttons($names){
    $buttons = func_get_args();
    foreach($buttons as $button){
      $this->crud_button($button);
    }
  }


 /**
  * build the appended buttons
  *
  * @access   private
  * @return   void
  */ 
  function _build_buttons(){
    foreach($this->_buttons as $button=>$caption){
      $build_button = "_build_".$button."_button";
      if ($caption == null){
        $this->$build_button();
      } else {
        $this->$build_button($caption);      
      }
    }
    $this->_buttons = array();
  
  }


 /**
  * append the default "save" button,  save is the button that appears in the top-right corner when the status is "create" or "modify"
  *
  * @access   public
  * @param    string  $caption  the label of the button (if not set, the default labels will used)
  * @return   void
  */
  function _build_search_button($caption=RAPYD_BUTTON_SEARCH){
    $this->submit("btn_submit", $caption, "BL"); 
  }


 /**
  * append the default "back" button, back is the button that appears in the bottom-left corner when the status is "show"
  *
  * @access   public
  * @param    string  $caption  the label of the button (if not set, the default labels will used)
  * @return   void
  */
  function _build_reset_button($caption=RAPYD_BUTTON_CLEAR){
  
    $action = "javascript:window.location='".site_url($this->_reset_uri)."'";
    $this->button("btn_reset", $caption, $action, "BL");
  }

 /**
  * process , main build method, it lunch process() method
  *
  * @access   public
  * @return   void
  */
  function build(){

    //sniff and build fields
    $this->_sniff_fields();

    $this->_built = true;
    
    $this->process();
    

  }

}

?>