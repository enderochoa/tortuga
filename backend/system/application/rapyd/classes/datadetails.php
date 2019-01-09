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
class DataDetails extends DataForm{

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

    var $detail_fields=array();
    var $details_expand=false;
    var $details_templa=false;
    var $rel_ind_disp=array();


 /**
  * PHP4 constructor.
  *
  * @access   public
  * @param    string   $title  widget title
  * @param    mixed   $table  db-tablename to be edited / or a dataobject instance
  * @return   void
  */
  function DataDetails($title, $table){

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

/*	function one_to_many($ind, $tabla, $join){
		$this->_dataobject->rel_one_to_many($ind, $tabla, $join);
	}

	function one_to_one($ind, $tabla, $join){
		$this->_dataobject->rel_one_to_one($ind, $tabla, $join);
	}*/

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

			//si se elimina no se puede eliminar items de las relaciones
      $this->_dataobject->make_rel=false;
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
        //$field->_getValue();
        //if (!$field->apply_rules){
        //  $hiddens[$field->db_name] = $field->value;
        //}
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

      $pk_check_unique=array();
      foreach($pk_check AS $keyfield => $keyvalue){
      	if(!array_key_exists($keyfield,$hiddens)){
      		$pk_check_unique[$keyfield]=$keyvalue;
      	}
      }

      if(count($pk_check_unique)>0){
      	if ($result && !$this->_dataobject->are_unique($pk_check_unique)){
     	   $result = false;
     	   $pk_error .= RAPYD_MSG_0210."<br />";
     	 }
    	}

    }
    $this->error_string = $pk_error.$this->error_string;
    return $result;
  }



  function process(){
  	//$this->_details_fields();
    $result = parent::process();


    switch($this->_action){

      case "update":
        if ($this->on_error()){
          $this->_status = "modify";
          $this->_process_uri = $this->rapyd->uri->uri_string();
          $this->_details_fields();
          $this->_sniff_fields();
          $this->_build_buttons();
          $this->_rel_script();
          $this->build_form();
        }
        if ($this->on_success()){


          $this->_postprocess_uri .= "/". $this->rapyd->uri->build_clause("show".$this->pk_to_URI($this->_dataobject->pk));

          if ($this->back_save){
            header("Refresh:0;url=".$this->back_url);
          } else {
            redirect("/".$this->_postprocess_uri,'refresh');
          }

        }
      break;

      case "insert":
        if ($this->on_error()){
          $this->_status = "create";
          $this->_process_uri = $this->rapyd->uri->uri_string();
          $this->_details_fields();
          $this->_sniff_fields();
          $this->_build_buttons();
          $this->_rel_script();
          $this->build_form();
        }
        if ($this->on_success()){

          $this->_postprocess_uri .= $this->pk_to_URI($this->_dataobject->pk);

          if ($this->back_save){
            header("Refresh:0;url=".$this->back_url);
          } else {
            redirect($this->_postprocess_uri,'refresh');
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
      	//$this->_details_fields();
        $this->_build_buttons();
        $this->_rel_script();
        $this->build_form();
      break;
      case "delete":
      	//$this->_details_fields();
        $this->_build_buttons();
        $this->build_message_form(RAPYD_MSG_0209);
      break;
      case "unknow_record":
	      //$this->_details_fields();
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
  * append the default "delete" button, delete is the button that appears in the top-right corner when the status is "show"
  *
  * @access   public
  * @param    string  $caption  the label of the button (if not set, the default labels will used)
  * @return   void
  */
  function _build_add_rel_button($caption=RAPYD_BUTTON_ADD2){

    if (($this->_status == "create") || ($this->_status == "modify")){
      foreach($this->_dataobject->_rel_type AS $nrel=>$rel){
      	if($rel[0]==1){
      	  $titulo=$this->get_rel_title($nrel);
      	  if(!empty($titulo)){
      	    $titulo=trim(str_replace('<#i#>','',$titulo));
      	    $titulo=trim(str_replace('<#o#>','',$titulo));
      	    //$titulo=strtolow($titulo);
          }
          $caption.=' '.$titulo;
      	  $this->button("btn_add_$nrel", $caption,  'add_'.$nrel.'()', "BL");
      	}
      }

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
      $this->button("btn_backerror", $caption, $action, "TL");
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

		$this->_details_fields();
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
  /**
  * Expande los detalles
  *
  * @access   private
  * @return   void
  */
	function _details_fields(){
		$this->_fields = array();
		$max_count=0;
		$object = (get_object_vars($this));
		foreach ($object as $property_name=>$property){
			if (is_object($property)){
				if (is_subclass_of($property, 'objField')){
					if (isset($property->rel_id) AND isset($this->_dataobject->_rel_type[$property->rel_id])){
						if($this->_dataobject->_rel_type[$property->rel_id][0]==1){

							if($this->details_expand){
								$this->$property_name->status = $this->_status;
								$this->$property_name->request = $_POST;

							}else{
								//$this->_dataobject->data_rel=array();
								$max=$this->rel_count($this->$property_name->name,$property->rel_id);


								$this->max_rel_count[$property->rel_id]=$max;
								$max_count=$max_count+$max;
								for($i=0;$i<$max;$i++){
									$obj=$property_name."_$i";

									$group=$this->link_rel_delete($property->rel_id,$i).'|'.$property->rel_id.'_'.$i;

									$this->$obj          = clone $property;
									$this->$obj->ind     = $i;
									$this->$obj->name    = str_replace('<#i#>',$i,$property->name );
									$this->$obj->label   = str_replace('<#i#>',$i,$property->label);
									$this->$obj->label   = str_replace('<#o#>',$i+1,$this->$obj->label);
									$this->$obj->onchange= str_replace('<#i#>',$i,$property->onchange);
									$this->$obj->request = $_POST;
									$this->$obj->group   = $group;
									$this->$obj->extra_output=str_replace('<#i#>',$i,$this->$obj->extra_output);

									if(get_class($property)=='dropdownField' AND isset($property->msql) AND $this->details_expand==false){
										$mSQL=str_replace('<#i#>',$i,$property->msql );

										$template = $mSQL;
										$parsedcount = 0;
										while (strpos($template,"#>")>0) {
											$parsedcount++;
											$parsedfield = substr($template,strpos($template,"<#")+2,strpos($template,"#>")-strpos($template,"<#")-2);

											$valor=$this->getval($parsedfield);
											$valor=$this->ci->db->escape($valor);

											$template = str_replace("<#".$parsedfield ."#>",$valor,$template);
										}

										if(!empty($valor))
											$this->$obj->options($template);
										}
								}


							}
							$this->$property_name->apply_rules=false;

						}

						if($this->_status!='idle' AND $this->$property_name->ind<0){
							$this->$property_name->status  = $this->_status;
							$this->$property_name->db_name ='';
							$this->$property_name->build();
							$this->detail_fields[$this->$property_name->rel_id][$property_name]['label']=$this->$property_name->label;
							$this->detail_fields[$this->$property_name->rel_id][$property_name]['name'] =$this->$property_name->name;
							$this->detail_fields[$this->$property_name->rel_id][$property_name]['field']=$this->$property_name->output;
							$this->detail_fields[$this->$property_name->rel_id][$property_name]['type'] =$this->$property_name->type;

							unset($this->$property_name);
						}
					}
				}
			}
		}
		if($max_count==0)
			$this->_dataobject->save_rel=false;
		$this->details_expand=true;
	}

 /**
  * Link para eliminar el detalle
  *
  * @access   public
  * @return   void
  */

	function link_rel_delete($rel,$id){
		$rt=$this->get_rel_delete($rel);

		$rt=str_replace('<#i#>',$id  ,$rt);
		$rt=str_replace('<#o#>',$id+1,$rt);

		return $rt;
	}

	function get_rel_delete($rel){
    $titulo=$this->get_rel_title($rel);
    if(empty($titulo)){
    	$rt='<#o#>';
    }else{
    	$rt=$titulo;
    }

		if($this->_status=="modify" || $this->_status=="create" || $this->rapyd->uri->is_set("update") || $this->rapyd->uri->is_set("insert")){
      	$rt.=" <a href=\"#\" onclick=\"del_${rel}(<#i#>);return false;\">".RAPYD_BUTTON_DELETE."</a>";
    }
		return $rt;
	}


	function set_rel_title($id_rel,$title){
		$this->rel_title[$id_rel]=$title;
	}
	function get_rel_title($id_rel){
		if(isset($this->rel_title[$id_rel]))
			return $this->rel_title[$id_rel];
		return null;
	}


 /**
  * devuelve la plantilla de elementos detalle
  *
  * @access   public
  * @return   void
  */

	function template_details($rel){
		if(isset($this->detail_fields[$rel])){
			return $this->detail_fields[$rel];
		}
		return false;
	}


 /**
  * cuenta la cantidad de relaciones
  *
  * @access   public
  * @return   void
  */

	function rel_count($name,$rel_id){
		if($this->rapyd->uri->is_set("update") or $this->rapyd->uri->is_set("insert") or $this->_status == "create" or $this->_action=='update' or $this->_action=='insert'){
			$ind=array_keys($_POST);

			if(count($ind)>0){
				$dipon=$puesto=array();
				$cant=0;
				$pattern='/^'.str_replace('<#i#>','(?<indices>[0-9]+)',$name).'$/';
				foreach($ind AS $val){
					if(preg_match($pattern, $val,$matches)>0){
						$cant++;

						$puesto[]=$matches['indices'];
					}
				}

				$rango=range(0,$cant-1);
				$dipon=array_diff($rango, $puesto);

				if(count($puesto)>0){ //arregla los indices
					$rango=range(0,$cant-1);
					$dipon=array_diff($rango, $puesto);
					if(count($dipon)>0){
						sort($puesto,SORT_NUMERIC);
						foreach($dipon AS $ind){
							$dind=str_replace('<#i#>',$ind,$name);
							$ai  =array_pop($puesto);
							$aind=str_replace('<#i#>',$ai,$name);
							$_POST[$dind]=$_POST[$aind];
							unset($_POST[$aind]);
						}
					}
				}

			}else{
				$cant=1;
			}
			if($cant==0){
				$cant=$this->_dataobject->count_rel($rel_id);
			}
			return $cant;
		}
		return $this->_dataobject->count_rel($rel_id);
  }

  function details_view($nrel){
		$campos=$this->template_details($nrel);
		if($campos!==false){
			$group=array('group_name' => $this->get_rel_delete($nrel),'group_tr'   => 'id="tr_'.$nrel.'_<#i#>"');

			foreach($campos AS $campo){
					$group['series'][]=array('is_hidden' => false,
															'series_name' => $campo['name'],
															'series_tr' => 'id="tr_'.$campo['name'].'"',
															'series_td' => 'id="td_'.$campo['name'].'"',
															'fields' => array(array(
																					'label'    => $campo['label'],
																					'field_td' => 'id="td_'.$campo['name'].'"',
																					'field'    =>$campo['field'],
																					'type'     =>$campo['type'],
																					'status'   => 'modify'
																					),
																				),
															);

			}
			$path=RAPYD_PATH.'views/'.$this->ci->rapyd->config->item("theme").'/dataform.php';
			$lines = file($path);
			$cont='';
			$write=false;
			foreach ($lines as $line) {
				if(preg_match("/<\?php \/\/@EOFS \?>/",$line)>0) break;
				if($write) $cont.=trim($line);
				if(preg_match("/<\?php \/\/@BOFS \?>/",$line)>0) $write=true;
			}

			ob_start();
			echo eval('?>'.preg_replace("/;*\s*\?>/", "; ?>", str_replace('<?=', '<?php echo ', $cont)).'<?php ');
			$buffer = ob_get_contents();
			@ob_end_clean();
			return $buffer;
		}
		return null;
	}

	function js_escape($string){
		$string=str_replace("\r",'',$string);
		$string=str_replace("\n",'',$string);
		$string=preg_replace('/\s\s+/', ' ', $string);
		$string=addslashes($string);
		$string=str_replace('<','\<',$string);
		$string=str_replace('>','\>',$string);
		$string=str_replace(';','\;',$string);
		//$string=str_replace('<',"'+String.fromCharCode(60)+'",$string);
		//$string=str_replace('>',"'+String.fromCharCode(62)+'",$string);
		$string='\''.$string.'\'';
		return $string;
	}


	function _rel_script(){
		$script='';
		foreach($this->_dataobject->_rel_type AS $id_rel=>$type){
			if($type[0]==1){
				$html=$this->details_view($id_rel);
				$html=$this->js_escape($html);
				//$html=str_replace("\n",'',$html);
				//$html=$this->ci->db->escape($html);

				$num=$this->max_rel_count[$id_rel];
				$script.="
					${id_rel}_cont=$num;

					function add_${id_rel}(){
						var can = ${id_rel}_cont.toString();
						var rt=true;
						if(typeof window.pre_add_${id_rel} == 'function') {
							rt=pre_add_${id_rel}(can);
						}
						if(rt){
							var htm = $html;
							var con = (${id_rel}_cont+1).toString();
							htm = htm.replace(/<#i#>/g,can);
							htm = htm.replace(/<#o#>/g,con);
							$(\"#__UTPL__\").before(htm);
							${id_rel}_cont=${id_rel}_cont+1;

							if(typeof window.post_add_${id_rel} == 'function') {
								post_add_${id_rel}(can);
							}
						}
					}

					function del_${id_rel}(id){
						var rt=true;
						if(typeof window.pre_del_${id_rel} == 'function') {
							rt=pre_del_${id_rel}(id);
						}
						if(rt){
							id = id.toString();
							$('#tr_${id_rel}_'+id).remove();
							if(typeof window.post_del_${id_rel} == 'function') {
								post_del_${id_rel}(id);
							}
						}
					}";
			}
		}

		$this->script($script,"create");
		$this->script($script,"modify");
		//$this->script($script,"idle");
	}

  function getval($obj){
		if(isset($this->$obj) AND is_subclass_of($this->$obj, 'objField')){
			$name=$this->$obj->name;
			if(empty($this->$obj->rel_id)){
				$requestValue = $this->ci->input->post($name);
				if($requestValue === FALSE AND $this->_dataobject->loaded){
					$requestValue =$this->_dataobject->get($this->$obj->db_name);
					if(empty($requestValue)) $requestValue=FALSE;
				}
				return $requestValue;
			}else{
				return false;
			}
		}else{
			return false;
		}
	}

	function get_from_dataobjetct($db_name){
		if($this->_dataobject->loaded){
			$requestValue =$this->_dataobject->get($db_name);
			if(empty($requestValue)) $requestValue=false;
		}else
			$requestValue=false;
		return $requestValue;
	}
	
	
}
?>