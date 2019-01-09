<?php if (!defined('BASEPATH')) exit('No direct script access allowed');
/**
 * Rapyd Components
 *
 * An open source library for CodeIgniter application development framework for PHP 4.3.2 or newer
 *
 * @package		rapyd.components
 * @author		Felice Ostuni
 * @license		http://www.fsf.org/licensing/licenses/lgpl.txt LGPL
 * @version		0.9.8
 * @filesource
 */ 
 
/**
 * rapyd's commons functions inclusion
 */
require_once("dataobject.php");
require_once("fields.php");

/**
 * DataForm base class.
 *
 * @package    rapyd.components
 * @author     Felice Ostuni
 * @author     Thierry Rey 
 * @access     public
 */
class DataForm{

  //private
  var $_title = "";
  var $_status;
  var $_action = "idle";
  var $_dataobject;
  var $_fields = array();
  var $_multipart = false;
  var $_on_show = false;
  var $_on_error = false;
  var $_on_success = false;
  var $_built = false;
  var $_button_container = array( "TR"=>array(),"TL"=>array(), "BL"=>array(), "BR"=>array() );
  var $_button_status = array();
  var $_script = array( "show"=>"", "create"=>"", "modify"=>"", "idle"=>"", "reset"=>"");
  
  //public
  var $cid  = "df";
  var $data = array();
  var $errors = array();
  var $attributes = array();
  var $error_string = "";
  var $output = "";
  var $default_group;
    var $html=null;

  
 /**
  * PHP4 constructor.
  *
  * @access   public
  * @param    string   $process_uri  uri/post action, if DF is used in a controller uri "contoller/registration".. it must have one segment more: "controller/registration/process"
  * @param    object   $data  a dataobject instance, if it's loaded.. the form is pre-filled by record values, and exec an update, else it's empty and exec an insert, if dataobject "is null".. the dataform is just a form helper. (with CI validations)
  * @return   void
  */
  function DataForm($process_uri=null, $dataobject=null){
  
    $this->ci =& get_instance();
    $this->rapyd =& $this->ci->rapyd;
    $this->uri =& $this->ci->uri;


    //load needed libraries 
    if (!isset($this->ci->validation)) {
      $this->ci->load->library('validation');
    }
    $this->validation =& $this->ci->validation;
		
		/*
		  here we load the database object for the use of dataForm with DataFilter $this->db here will be used by the DG or DT
		  May be we can skip this loading  if(isset($dataobject)) because in this case Dataform work with DO or DE and no needs of $this->db??????
      $this->db->select('*');
      $this->db->from($table);
			into the datafilter code....
    */
     
		//If dataset is instantiate first we take the Rapyd shared database object 
    if(isset($this->rapyd->db))
		{
				$this->db =& $this->rapyd->db;
		}
		//If datafilter (dataform) is instantiate first we set the rapyd shared	database AR	in case that DS will be loaded in second....		
		elseif(isset($this->ci->db)){
			$this->db=&$this->ci->db;
			$this->rapyd->db=&$this->ci->db;
		}else{
			$data_conn =(isset($this->rapyd->data_conn))?$this->rapyd->data_conn:'';
			$this->db = $this->ci->load->database($data_conn,TRUE);
			$this->rapyd->db =& $this->db;		
		}
		
    //load needed helpers 
    if (!isset($this->ci->load->helpers['form_helper'])) { 
      $this->ci->load->helper('form_helper');
    }
    if (!isset($this->ci->load->helpers['url_helper'])) { 
      $this->ci->load->helper('url_helper');
    }
    if (!isset($this->ci->load->helpers['inflector'])) { 
      $this->ci->load->helper('inflector');
    }
    
    
    if (!isset($process_uri)){
       //prendere l'ultimo segmento.. e se ? process.. non aggiungere "/process" al corrente uri_string
       
      //The slash into "/process" is removed because uri_string() always ending by a slash 
      //(the CI previous version also returned with a slash) it was a little bug in the previous version.
      $this->_process_uri = $this->rapyd->uri->uri_string()."process";
    } else {
      $this->_process_uri = $process_uri;
    }
		

    //detect form status (output)
    if (isset($dataobject)){
			if (strtolower(get_class($dataobject))=="dataobject"){
        $this->_dataobject =& $dataobject;
			} else {
				$this->_dataobject = new DataObject($dataobject);
			}
      if ($this->_dataobject->loaded){
        $this->_status = "modify";
      } else {
        $this->_status = "create";
      }
    } else {
      $this->_dataobject = null;
      $this->_status = "create";
    }
    
    $this->validation->_dataobject =& $this->_dataobject;
    
    
    
    static $identifier = 0;
    $identifier++;
    $this->cid = $this->cid . (string)$identifier;   
    $this->html = new Html();
  }

  function title($title){
    $title = (lang($title)!="") ? lang($title) : $title;
    $this->_title = $title;
  }
  
 /**
  * useFunction add your custom(or php precompiled) functon/s to the list of "replace_functions" 
  * and enable the component to use a rapyd raplacing/formatting sintax  like these:
  * "..<customfunction><#body#>|0|100</customfunction>.."  (where <#body#> is a fieldname, and |0|100 are function parameters).
  *
  * @access   private
  * @return   void
  */
  function use_function(){
    $functions = func_get_args();
    foreach($functions as $function){
      if (!in_array(strtolower($function), $this->rapyd->config->item("replace_functions"))){
        array_push($this->rapyd->config->config["replace_functions"], strtolower($function));
      }
    }
  }

 /**
  * detect $form->field from properties, and populate an array
  *
  * @access   private
  * @return   void
  */ 
  function _sniff_fields(){
    $this->_fields = array();
    
    $object = (get_object_vars($this));
    foreach ($object as $property_name=>$property){
      if (is_object($property)){
        if (is_subclass_of($property, 'objField')){
          
          
          if ($property->type == "upload") {
            $this->_multipart = true;
          }

          if (isset($this->_dataobject)){

            $fields = $this->_dataobject->field_names;
            if (in_array($this->$property_name->db_name,$fields)||!$this->$property_name->db_name ){
              $this->$property_name->data =& $this->_dataobject;
            }
            
            if($this->$property_name->pointer){
              $this->$property_name->data =& $this->_dataobject;
            }elseif (isset($this->$property_name->rel_id)){
              $this->$property_name->data =& $this->_dataobject;
            }
            
          }
          $this->$property_name->status = $this->_status;
          
          if (isset($this->default_group) && !isset($this->$property_name->group)){
            $this->$property_name->group = $this->default_group;
          }
          
          if (isset($this->$property_name->rule)){
            if ((strpos($this->$property_name->rule,"required")!==false) && !isset($this->$property_name->no_star) ){
              $this->$property_name->_required = "*";
            }
          }
          
          $this->$property_name->build();

          $this->_fields[$property_name] =& $this->$property_name;
          
        }
      }
    }

  }


  function button_status($name, $caption, $action, $position="BL", $status="create", $class="button"){
     $this->_button_status[$status][$position][] = $this->html->button($name, $caption, $action, "button", $class);
  }

  function button($name, $caption, $action, $position="BL",$class="button"){
     $this->_button_container[$position][] = $this->html->button($name, $caption, $action, "button", $class);
  }

  function submit($name, $caption, $position="BL",$class="button"){
     $this->_button_container[$position][] =$this->html->button($name, $caption, "", "submit");
  }

  function script($script, $status="create"){
     $this->_script[$status] .= $script;
  }

  function pre_process($action,$function,$arr_values=array()){
    $this->_dataobject->pre_process($action,$function,$arr_values);
  }
  
  function post_process($action,$function,$arr_values=array()){
    $this->_dataobject->post_process($action,$function,$arr_values);
  }
  
  
 /**
  * build the form output with current rapyd component theme.
  * Note, this is optional, you can use $form->build() and field-by-field output:
  * $form->validation->error_string 
  * $form->form_open 
  * $form->article_title->output 
  * $form->article_body->output 
  * ...
  *
  * @access   private
  * @return   void
  */
  function build_form(){
       
    if (!$this->_built) $this->build();
    
    if (!array_key_exists("id", $this->attributes)) $this->attributes["id"] = $this->cid;

    if ($this->_multipart){
      $this->form_open = form_open_multipart($this->_process_uri, $this->attributes);
    } else {
      $this->form_open = form_open($this->_process_uri, $this->attributes);
    }
    
    $this->form_close = form_close();
		
		
		$this->rapyd->set_view_path();
		
		$data["title"] = "";
		$data["error_string"] = "";
		$data["form_scripts"] = "";
		$data["container_tr"] = "";
		$data["container_tl"] = "";
		$data["container_bl"] = "";
		$data["container_br"] = "";
		
		//title
		$data["title"] = $this->_title;


		//buttons
    if ( (count($this->_button_container["TR"])>0) || (isset($this->_button_status[$this->_status]["TR"])) ){
      if (isset($this->_button_status[$this->_status]["TR"])){
        foreach ($this->_button_status[$this->_status]["TR"] as $state_buttons){
          $this->_button_container["TR"][] = $state_buttons;
        }
      }
			$data["container_tr"] = join("&nbsp;", $this->_button_container["TR"]);
    }
	if ( (count($this->_button_container["TL"])>0) || (isset($this->_button_status[$this->_status]["TL"])) ){
      if (isset($this->_button_status[$this->_status]["TL"])){
        foreach ($this->_button_status[$this->_status]["TL"] as $state_buttons){
          $this->_button_container["TL"][] = $state_buttons;
        }
      }
			$data["container_tl"] = join("&nbsp;", $this->_button_container["TL"]);
    }
	
    if ( (count($this->_button_container["BL"])>0) || (isset($this->_button_status[$this->_status]["BL"])) ){
      if (isset($this->_button_status[$this->_status]["BL"])){
        foreach ($this->_button_status[$this->_status]["BL"] as $state_buttons){
          $this->_button_container["BL"][] = $state_buttons;
        }
      }
			$data["container_bl"] = join("&nbsp;", $this->_button_container["BL"]);
    }
    if ( (count($this->_button_container["BR"])>0) || (isset($this->_button_status[$this->_status]["BR"])) ){
      if (isset($this->_button_status[$this->_status]["BR"])){
        foreach ($this->_button_status[$this->_status]["BR"] as $state_buttons){
          $this->_button_container["BR"][] = $state_buttons;
        }
      }
			$data["container_br"] = join("&nbsp;", $this->_button_container["BR"]);
    }
    
		$data["form_scripts"] = $this->html->javascriptTag($this->_script[$this->_status]);
		$data["title"] = $this->_title;
		$data["error_string"] = $this->error_string;
		$data["form_begin"] = $this->form_open;
		$data["form_end"] = $this->form_close;


    //$prg=0;
    //nest fields (fields can be nested/joined with others)
    foreach ( $this->_fields as $field_name => $field_ref )
    {
      //$prg++;
      if (isset($field_ref->in)){
        $series_of_fields[$field_ref->in][] = $field_name; 
      } else {
      
        //if ($field_name==null) $field_name = "__".$prg;
        $series_of_fields[$field_name][] = $field_name;
      }
    }

    //group fields (fields can be organized in groups)
    foreach ( $this->_fields as $field_name => $field_ref )
    {
      if (!isset($field_ref->in)){
        if (isset($field_ref->group)){
          $ordered_fields[$field_ref->group][$field_name] = $series_of_fields[$field_name]; 
        } else {
          $ordered_fields["ungrouped"][$field_name] = $series_of_fields[$field_name];
        }
      }
    }
    unset($series_of_fields);
    

    foreach ($ordered_fields as $group=>$series_of_fields){
      
			unset($gr);
			if(strpos($group,'|')>0){
				$arr=explode('|',$group);
				$gid  =$arr[1];
				$gname=$arr[0];
			}else{
				$gname=$group;
				$gid  =underscore(strtolower($group));
			}
			
			$gr["group_name"] = $gname;
			$gr["group_tr"]   = 'id="tr_'.$gid.'"';
      
      foreach ($series_of_fields as $series_name=>$fields ) {
        unset($sr);
        $sr["is_hidden"] = false;
        $sr["series_name"] = $series_name;
        $sr["series_tr"] = 'id="tr_'.$series_name.'"';
        $sr["series_td"] = 'id="td_'.$series_name.'"';

        foreach ($fields as $field_name ) {
          unset($fld);
        
          $field_ref =& $this->$field_name;


          if (($field_ref->status == "hidden" ||  in_array($field_ref->type, array("hidden","auto"))))
          {
            $sr["is_hidden"] = true;
          }
          
          $fld["label"] = $field_ref->label.($this->_status=="show"?'':$field_ref->_required);
          $fld["field_td"] = 'id="td_'.$field_ref->name.'"';
          $fld["field"] = $field_ref->output;
          $fld["type"] = $field_ref->type;
          $fld["status"] = $field_ref->status;
          
          
          $sr["fields"][] = $fld;

        }
        $gr["series"][] = $sr;
      
      }
			$grps[] = $gr;

    }
    $data["groups"] = $grps;

		$this->output = $this->ci->load->view('dataform', $data, true);

		$this->rapyd->reset_view_path();

		return  $this->output;
    
  }




 /**
  *
  */
  function build_message_form($message){
      
    if (!$this->_built) $this->build();

    $this->form_open = form_open($this->_process_uri);
    $this->form_close = form_close();
    
		$data["title"] = "";
		$data["error_string"] = "";
		$data["form_scripts"] = "";
		$data["container_tr"] = "";
		$data["container_tl"] = "";
		$data["container_bl"] = "";
		$data["container_br"] = "";
		
		$this->rapyd->set_view_path();
		
		//title
		$data["title"] = $this->_title;

		//buttons
		if (count($this->_button_container["TR"])>0){
			$data["container_tr"] = join("&nbsp;", $this->_button_container["TR"]);
		}
		if (count($this->_button_container["TL"])>0){
			$data["container_tl"] = join("&nbsp;", $this->_button_container["TL"]);
		}
		if (count($this->_button_container["BL"])>0){
			$data["container_bl"] = join("&nbsp;", $this->_button_container["BL"]);
		}
		if (count($this->_button_container["BR"])>0){
			$data["container_br"] = join("&nbsp;", $this->_button_container["BR"]);
		}

		$data["message"] = $message;
		$data["form_begin"] = $this->form_open;
		$data["form_end"] = $this->form_close;
    
		$this->output = $this->ci->load->view('dataform', $data, true);

		$this->rapyd->reset_view_path();

		return  $this->output;
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
    
    if (!array_key_exists("id", $this->attributes)) $this->attributes["id"] = $this->cid;

    if ($this->_multipart){
      $this->form_open = form_open_multipart($this->_process_uri);
    } else {
      $this->form_open = form_open($this->_process_uri);
    }
    
    $this->form_close = form_close();


    //detect action
    //we call it with false because we don't want the added prefix and suffix slash...
    $current_uri = $this->rapyd->uri->uri_string(false);  
    
    if ( isset($_POST) && ($current_uri == $this->_process_uri) ){

      $this->_action = ($this->_status=="modify")?"update":"insert";
    }
    
    $this->form_scripts = $this->html->javascriptTag($this->_script[$this->_status]);
    
    $this->_built = true;
    
    //process
    $this->process();
    
  }



 /**
  * validation passed?
  *
  * @access   private
  * @return   bool  validation passed?
  */ 
  function is_valid(){
      $claves=array();
      
    //some fields mode can disable or change some rules.
    foreach ($this->_fields as $field_name => $field_copy){

      //reference
      $field =& $this->$field_name;
      $field->action = $this->_action;
      $field->_getMode();
      $claves[$field_name]=$field->name;
      
      if (isset($field->rule)){

        if (($field->type != "upload") && $field->apply_rules){
          $fieldnames[$field->name] = $field->label;
          $rules[$field->name]	= $field->rule;
        } else {
          $field->_required = "";
        }
      }

    }
    
    if (isset($rules))
    {
      $this->validation->set_rules($rules);
      $this->validation->set_fields($fieldnames); 
      
      
      if (count($_POST)<1){
        $_POST = array(1);
      }
      
      /*
      if (count($rules)<1){
        return true;
      } else {
        if (count($_POST)<1){
          $_POST = array(1);
        }
      } 
      */

    } else {
    
      return true;
    }
    
    

    $result = $this->validation->run();
 
    $this->error_string = $this->validation->error_string;
    
    //Resalta los campos con errores
    //$claves=array_keys($this->_fields);

    foreach($claves as $nobj=>$campo){
      $obj=$campo.'_error';

      if(isset($this->validation->$obj) && strlen($this->validation->$obj)>0){
        $this->_fields[$nobj]->style='border: 2px solid #FF3300;';
      }
    }
    
    
    return $result;
    
  }




 /**
  * process form, and perform dataobject action (update/insert)
  *
  * @access   public
  * @return   string   component html output
  */
  function process(){
    
    //database save
    switch($this->_action){
    
      case "update":
      case "insert":

        //validation failed
        
        if (!$this->is_valid()){
        
          $this->_on_show = false;
          $this->_on_success = false;
          $this->_on_error = true;
 
          foreach ($this->_fields as $field){
            $field->action = "idle";
          }
          return false;
          
        } else {
        
          $this->_on_show = false;
          $this->_on_success = true;
          $this->_on_error = false;
        }

        foreach ($this->_fields as $field){
          $field->action = $this->_action;
          $result = $field->autoUpdate(); 
          if (!$result){
            $this->_on_show = false;
            $this->_on_success = false;
            $this->_on_error = true;

            $this->error_string = $field->save_error;
            
            return false;
          }
          
        } 
        if (isset($this->_dataobject)){
         //die($this->is_valid());
          $return = $this->_dataobject->save();
        } else {
          $return = true;
        }
        
        if (!$return){
        	if($this->_dataobject->pre_process_result===false)$this->error_string .= ($this->_action=="update")?$this->_dataobject->error_message_ar['pre_upd']:$this->_dataobject->error_message_ar['pre_ins'];
          $this->_on_show = false;
          $this->_on_success = false;
          $this->_on_error = true;
        }
        
        return $return;
        
        break;
        
      case "delete":
        $return = $this->_dataobject->delete();
        
        if (!$return){
        	if($this->_dataobject->pre_process_result===false)$this->error_string .= $this->_dataobject->error_message_ar['pre_del'];
          $this->_on_show = false;
          $this->_on_success = false;
          $this->_on_error = true;
        } else {
          $this->_on_show = false;
          $this->_on_success = true;
          $this->_on_error = false;
        }
        
        break;
        
      case "idle":
          $this->_on_show = true;
          $this->_on_success = false;
          $this->_on_error = false;
          return true;
        break;
        
      default:
       return false;
    
    }
    
  }

  function on_show(){
    return $this->_on_show;
  }

  function on_error(){
    return $this->_on_error;
  }
  
  function on_success(){
    return $this->_on_success;
  }
  
	function _build_add_button($caption=RAPYD_BUTTON_ADD){
		$this->ci->load->library('datasis');
		$uri = $this->ci->datasis->get_uri();
	    if (($this->_status == "show") || ($this->_status == "modify") || ($this->_status == "create") || ($this->_status == "unknow_record") || ($this->_action == "delete")){
	      $action = "javascript:window.location='".site_url($uri."/create")."'";
	      $this->button("btn_add", $caption, $action, "TL");  //ANTES BL
	    } 
	}

}


?>
