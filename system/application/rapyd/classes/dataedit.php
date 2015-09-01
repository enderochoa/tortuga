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
 * ancestor 
 */
require_once("dataform.php");

/**
 * DataEdit base class.
 *
 * @package    rapyd.components
 * @author     Felice Ostuni
 * @author     Thierry Rey
 * @access     public
 */
class DataEdit extends DataForm{

  //deprecated could be removed in the 1.0
  var $back_url = "";
  
  //new property
  var $back_uri = "";
  
  var $check_pk = true;

  var $_postprocess_uri = "";

  var $_undo_uri = "";
  var $_buttons = array();
  var $_pkey =0;
  
  var $back_save = false;
  var $back_delete = true;
  var $back_cancel = false;

  var $back_cancel_save = false;
  var $back_cancel_delete = false;
  var $on_save_redirect=true;

  
 /**
  * PHP4 constructor.
  *
  * @access   public
  * @param    string   $title  widget title
  * @param    mixed   $table  db-tablename to be edited / or a dataobject instance
  * @return   void
  */
  function DataEdit($title, $table){
      
    if (is_object($table) && is_a($table, "DataObject"))
    {
      $dataobject =& $table;
    } else {
    
      $dataobject = new DataObject($table);

    }
    parent::DataForm(null, $dataobject);

    $this->session =& $this->rapyd->session; 

    $this->_pkey = count($this->_dataobject->pk);
    
    
    if ($this->rapyd->uri->get("osp",1)==""){
      $this->rapyd->uri->un_set("osp");
    }
    $this->_sniff_status();
    $this->title($title);
  }



 /**
  * transforn a PK array in the same format of the one used in DO->load() function ie: array(pk1=>value1, pk2=>value2) 
  * in a string formated as we attent at the end (pk part)of the URI (as explain in conventions)=>/pk1_name/pk1_value/pk2_name/pk2_value/...
  * @access   private
  * @param    array   
  * @return   string
  */
	function pk_to_URI($pk)
	{
		  $result="";
		  foreach ($pk as $keyfield => $keyvalue){
		  	$result.= "/".raencode($keyvalue);
			}
			return $result;
	}
	/**
  * rebuild the PK array in the same format of the one used in DO->load() function ie: array(pk1=>value1, pk2=>value2) 
  * from the string formated as we attent at the end (pk part)of the URI (as explain in conventions)=>/pk1_name/pk1_value/pk2_name/pk2_value/...
  * @access   private
  * @param    string   
  * @return   array
  */
	function URI_to_pk($id_str , $do)
	{
		  $result=array();
	
		  //check and remove for '/' in first and last position for that explode work fine.
			$tmp_ar = explode("/",$id_str);
			$keys = array_keys($do->pk);
		 	for($i=0;$i <= count($tmp_ar)-1;$i++){
		 		$result[$keys[$i]]=radecode($tmp_ar[$i]);
		 	}

		 	return $result;
	}
  
	/**
	* rebuild the string formated as we attent at the end (pk part)of the URI (as explain in conventions)=>/pk1_name/pk1_value/pk2_name/pk2_value/...
	* without the first slash 
  * from the segment_array 
  * @access   private
  * @param    array    
  * @return   string
  */	
	function segment_id_str($segment_ar)
	{
		$id_segment = array_slice($segment_ar,-($this->_pkey));
		return join('/',$id_segment);
	}


  function _sniff_status(){
   
    $this->_status = "idle";

    $segment_array = $this->uri->segment_array();

    $id_str = $this->segment_id_str($segment_array);
    
    //The following var is unsuded?? it seams to be an old test remaining code??
    //$uri_array = $this->rapyd->uri->explode_uri($this->uri->uri_string());
  
    ///// show /////
    if ($this->rapyd->uri->is_set("show") && (count($this->rapyd->uri->get("show")) == $this->_pkey+1) ){
    
        $this->_status = "show";

        $this->_process_uri = "";
        
        $result = $this->_dataobject->load($this->URI_to_pk($id_str,$this->_dataobject));
        
        if (!$result){
          $this->_status = "unknow_record";
        } 
     
    ///// modify /////
    } elseif ($this->rapyd->uri->is_set("modify")  && (count($this->rapyd->uri->get("modify")) == $this->_pkey+1)){
    
        $this->_status = "modify";
    
        $this->_process_uri = $this->rapyd->uri->change_clause($this->rapyd->uri->uri_array, "modify", "update");
    
        $result = $this->_dataobject->load($this->URI_to_pk($id_str,$this->_dataobject));
        if (!$result){
          $this->_status = "unknow_record";
        }
        
    
    ///// create /////
    } elseif ($this->rapyd->uri->is_set("create")){
    
        $this->_status = "create";
    
        $this->_process_uri = $this->rapyd->uri->change_clause($this->rapyd->uri->uri_array, "create", "insert");
        
    
    ///// delete /////
    } elseif ($this->rapyd->uri->is_set("delete") && (count($this->rapyd->uri->get("delete")) == $this->_pkey+1)){
    
        $this->_status = "delete";
        
        $this->_process_uri = $this->rapyd->uri->change_clause($this->rapyd->uri->uri_array, "delete", "do_delete");
        $this->_undo_uri    = $this->rapyd->uri->change_clause($this->rapyd->uri->uri_array, "delete", "show");

        $result = $this->_dataobject->load($this->URI_to_pk($id_str,$this->_dataobject));
        if (!$result){
          $this->_status = "unknow_record";
        }
    }
  }


  function _sniff_action(){
          
    $segment_array = $this->uri->segment_array();
    $id_str = $this->segment_id_str($segment_array);
    
    ///// insert /////
    if ($this->rapyd->uri->is_set("insert")){

      $this->_action = "insert";
      $this->_postprocess_uri =  $this->rapyd->uri->change_clause($this->rapyd->uri->uri_array, "insert", "show");

    ///// update /////
    } elseif ($this->rapyd->uri->is_set("update")){
    
      $this->_action = "update";

      //this uri is completed in the "process" method
      $this->_postprocess_uri = $this->rapyd->uri->unset_clause($this->rapyd->uri->uri_array, "update");

      $this->_dataobject->load($this->URI_to_pk($id_str,$this->_dataobject));
      
    ///// delete /////
    } elseif ($this->rapyd->uri->is_set("do_delete")){
    
      $this->_action = "delete";
      $result = $this->_dataobject->load($this->URI_to_pk($id_str,$this->_dataobject));
      if (!$result){
        $this->_status = "unknow_record";
      } 
  
    }
  }



  function is_valid(){
    $result = parent::is_valid();

    if (!$this->check_pk) return $result;

    if ($this->_action=="update" || $this->_action=="insert"){
      $pk_check=array();
      $pk_error = "";
      $hiddens = array();
      
      //pk fields mode can setted to "autohide" or "readonly" (so pk integrity violation check isn't needed)
      foreach ($this->_fields as $field_name => $field_copy){
        //reference
        $field =& $this->$field_name;
        $field->_getValue();
        if (!$field->apply_rules){
          $hiddens[$field->db_name] = $field->value;
        }
      }
          
      //We build a pk array from the form value that is submit if its a writing action (update & insert)
      foreach ($this->_dataobject->pk as $keyfield => $keyvalue){
        if (isset($this->validation->$keyfield)){
          $pk_check[$keyfield] = $this->validation->$keyfield;
        // detect that a pk is hidden, so no integrity check needed
        } elseif (array_key_exists($keyfield,$hiddens)){
          $pk_check[$keyfield] = $hiddens[$keyfield];
        }
      }
      
      if (sizeof($pk_check) != $this->_pkey){
      //If PK is Autoincrement we don't need to check PK integrity, But its supose that for a none AutoIcrement PK the form always contain the right PK fields
        if (sizeof($this->_dataobject->pk)==1 && sizeof($pk_check)==0)return $result;
      }
      // this check the unicity of PK with the new DO function
      if ($result && !$this->_dataobject->are_unique($pk_check)){
        $result = false;
        $pk_error .= RAPYD_MSG_0210."<br />";
      }

    }
    $this->error_string = $pk_error.$this->error_string;
    return $result;
  }



  function process(){
  
    $result = parent::process();
    
   
    switch($this->_action){
      case "update":
        if ($this->on_error()){
	  
          $this->_status = "modify";
          $this->_process_uri = $this->rapyd->uri->uri_string();
          $this->_sniff_fields();
          $this->_build_buttons();
          $this->build_form();
        }
        if ($this->on_success()){

          
          $this->_postprocess_uri .= "/". $this->rapyd->uri->build_clause("show".$this->pk_to_URI($this->_dataobject->pk));
  
	  

	  if($this->on_save_redirect){
	    if ($this->back_save){
	      header("Refresh:0;url=".$this->back_url);
	    } else {
	      redirect("/".$this->_postprocess_uri,'refresh');
	    }
	  }
          
          
        }
      break;
      
      case "insert":  
        if ($this->on_error()){
          $this->_status = "create";
          $this->_process_uri = $this->rapyd->uri->uri_string();
          $this->_sniff_fields();
          $this->_build_buttons();
          $this->build_form();
        }
        if ($this->on_success()){

          $this->_postprocess_uri .= $this->pk_to_URI($this->_dataobject->pk);
          
	  if($this->on_save_redirect){
	    if($this->back_save){
	      header("Refresh:0;url=".$this->back_url);
	    } else {
	      redirect($this->_postprocess_uri,'refresh');
	    }
	  }
          
        }
      break;
      
      case "delete": 
        if ($this->on_error()){
          $this->_build_buttons();
          if(empty($this->_dataobject->error_message_ar['pre_del']))
          	$this->build_message_form(RAPYD_MSG_0206);
          else
          	$this->build_message_form($this->_dataobject->error_message_ar['pre_del']);
        }
        if ($this->on_success()){
          $this->_build_buttons();
          
          if ($this->back_delete){
            header("Refresh:0;url=".$this->back_url);
          } else {
            $this->build_message_form(RAPYD_MSG_0202);
          }
        }
      break;
      
    }
    
    switch($this->_status){
    
      case "show":      
      case "modify":
      case "create":
        $this->_build_buttons();
        $this->build_form();
      break;
      case "delete":
        $this->_build_buttons();
        $this->build_message_form(RAPYD_MSG_0209);
      break;
      case "unknow_record":
        $this->_build_buttons();
        $this->build_message_form(RAPYD_MSG_0208);
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
  * append the default "modify" button, modify is the button that appears in the top-right corner when the status is "show"
  *
  * @access   public
  * @param    string $caption  the label of the button (if not set, the default labels will used)
  * @return   void
  */
  function _build_modify_button($caption=RAPYD_BUTTON_MODIFY)
  {
    if ($this->_status == "show"  && $this->rapyd->uri->is_set("show"))
    {
      $modify_uri = $this->rapyd->uri->change_clause($this->rapyd->uri->uri_array, "show", "modify");
      
      $action = "javascript:window.location='" . site_url($modify_uri) . "'";
      $this->button("btn_modify", $caption, $action, "TR"); 
    }
  }

 /**
  * append the default "delete" button, delete is the button that appears in the top-right corner when the status is "show"
  *
  * @access   public
  * @param    string  $caption  the label of the button (if not set, the default labels will used)
  * @return   void
  */
  function _build_delete_button($caption=RAPYD_BUTTON_DELETE){

    if ($this->_status == "show"  && $this->rapyd->uri->is_set("show"))
    {
      $delete_uri = $this->rapyd->uri->change_clause($this->rapyd->uri->uri_array, "show", "delete");

      $action = "javascript:window.location='" . site_url($delete_uri) . "'";
      $this->button("btn_delete", $caption, $action, "TR"); 

    } elseif($this->_status == "delete") {

      $action = "javascript:window.location='" . site_url($this->_process_uri) . "'";
      $this->button("btn_delete", $caption, $action, "BL"); 
    }
  }


 /**
  * append the default "save" button,  save is the button that appears in the bottom-left corner when the status is "create" or "modify"
  *
  * @access   public
  * @param    string  $caption  the label of the button (if not set, the default labels will used)
  * @return   void
  */
  function _build_save_button($caption=RAPYD_BUTTON_SAVE){
    if (($this->_status == "create") || ($this->_status == "modify")){  
      $this->submit("btn_submit", $caption, "TR");   // ANTES bl
    }
  }
  

 /**
  * append the default "undo" button, undo is the button that appears in the top-right corner when the status is "create" or "modify"
  *
  * @access   public
  * @param    string  $caption  the label of the button (if not set, the default labels will used)
  * @return   void
  */
  function _build_undo_button($caption=RAPYD_BUTTON_UNDO){
  
    if ($this->_status == "create"){
    
      $action = "javascript:window.location='{$this->back_url}'";
      $this->button("btn_undo", $caption, $action, "TL"); 
     
    } elseif($this->_status == "modify") {
    
    if (($this->back_cancel_save === FALSE) || ($this->back_cancel === FALSE)){
    
        //is modify
        if ($this->rapyd->uri->is_set("modify"))
        {
          $undo_uri = $this->rapyd->uri->change_clause($this->rapyd->uri->uri_array, "modify", "show");

        //is modify on error
        } elseif ($this->rapyd->uri->is_set("update")){
        
          $undo_uri = $this->rapyd->uri->change_clause($this->rapyd->uri->uri_array, "update", "show");
        }

        $action = "javascript:window.location='" . site_url($undo_uri) . "'"; 
      } else {
        $action = "javascript:window.location='{$this->back_url}'";
      }
      
      $this->button("btn_undo", $caption, $action, "TL"); 
      
    } elseif($this->_status == "delete") {

      if(($this->back_cancel_delete === FALSE) || ($this->back_cancel === FALSE)){
        $undo_uri = site_url($this->_undo_uri);
        $action = "javascript:window.location='$undo_uri'";
      } else{
        $action = "javascript:window.location='{$this->back_url}'";
      }      
      
      $this->button("btn_undo", $caption, $action, "TL"); 
    }
  }

 /**
  * append the default "back" button, back is the button that appears in the bottom-left corner when the status is "show"
  *
  * @access   public
  * @param    string  $caption  the label of the button (if not set, the default labels will used)
  * @return   void
  */
  function _build_back_button($caption=RAPYD_BUTTON_BACK){
    if (($this->_status == "show") || ($this->_status == "unknow_record") || ($this->_action == "delete")){
      $action = "javascript:window.location='{$this->back_url}'";
      $this->button("btn_back", $caption, $action, "TL");  //ANTES BL
    }
  }

 /**
  * append the default "backerror" button
  *
  * @access   public
  * @param    string  $caption  the label of the button (if not set, the default labels will used)
  * @return   void
  */
  function _build_backerror_button($caption=RAPYD_BUTTON_BACKERROR){
    if (($this->_action == "do_delete") && ($this->_on_error)){   
      $action = "javascript:window.history.back()";
      $this->button("btn_backerror", $caption, $action, "TR");       
    }
  }
  
 
 /**
  * process , main build method, it lunch process() method
  *
  * @access   public
  * @return   void
  */
  function build(){
  
  
    //temp. back compatibility
    if (site_url("")!="/"){
      $this->back_uri = ($this->back_uri != "")? $this->back_uri :  trim(str_replace(site_url(""),"",str_replace($this->ci->config->item('url_suffix') ,"",$this->back_url)), "/");
    } else {
      $this->back_uri = ($this->back_uri != "")? $this->back_uri : trim($this->back_url, "/");
    }
    
    if (($this->back_uri == "") && isset($this->_buttons["back"])){
      show_error('you must give a correct "BACK URI": $edit->back_uri');
    }

  
    //sniff and build fields
    $this->_sniff_fields();
    
    //sniff and perform action
    $this->_sniff_action();

    //build back_url 
    $persistence = $this->rapyd->session->get_persistence($this->back_uri, $this->rapyd->uri->gfid);
    
    
    if ( isset($persistence["back_post"]) ){
      $this->back_url = site_url($persistence["back_uri"]);
    } else {
      $this->back_url = site_url($this->back_uri);
    }


    $this->_built = true;
    
    $this->process();
  }
  
	function rel_count(){
    if (($this->requestRefill == true)){
   
    } elseif (($this->status == "create")){
      return 1;
    } elseif (($this->status == "modify")){
      
    } elseif (($this->_dataobject->loaded) && (!isset($this->request[$this->name])) ){
  		return $this->_dataobject->count_rel('itstra');
    }
  }
}


?>