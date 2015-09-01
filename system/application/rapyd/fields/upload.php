<?php
/**
 * uploadFieldfield - extends objField for manage file-uploads  
 *
 * Important note: if it is used in a DataEdit or in conjunction with a DataObject it do Not store the "binary version of the file" in the table field,
 * but just the "filename.ext" (with the extension).
 * It exec uploads, unlinks... can build a preview (if the file is an image).. etc..
 *
 * @file
 * @package rapyd.components.fields
 * @author Felice Ostuni <felix@rapyd.com>
 * @license http://www.fsf.org/licensing/licenses/lgpl.txt LGPL
 * @copyright Copyright (c) 2006 Felice Ostuni - http://www.rapyd.com
 * @version 1.0
 */


/**
 * uploadField 
 *
 * @package    rapyd.components.fields
 * @author     Felice Ostuni <felix@rapyd.com>
 * @access     public
 * @version    1.0.1
 */
class uploadField extends objField{

  var $type = "upload";
  var $contentType;
  var $filePathName;
  var $css_class = "password";
  var $delete_file = true;
  
  //user preferences
  var $www_path;
  var $upload_path;
  var $upload_root;
  var $preview;
  var $overwrite = false;
  var $remove_spaces = true;
  
  var $upload_data; //array with all data of uploaded file
  var $upload_error; //error messages
  
  var $FileName;
  var $NewFileName;
  var $uploadName;
  var $errorMessage;
  
  var $thumbnail; //array($maxwidth, $maxheight) for thumbnail creation (when file is a jpg)

  var $allowed_types;
  var $max_size;
  var $max_width;
  var $max_height;



  function _getValue(){
    parent::_getValue();
  }


  function serverPath($docroot){
    $base = ($docroot != "")?$docroot:$_SERVER["DOCUMENT_ROOT"];
    return $base . $this->upload_path; 
  }
  

  function draw_link(){
  
    if (isset($this->preview)){
    
      return $this->draw_preview_link();
    
    } elseif (isset($this->thumb)) {
      
      $this->preview = $this->thumb;
      return $this->draw_preview_link();
    
    }
    return $this->draw_upload_link();
  }


  function draw_upload_link(){
    if ($this->www_path=="") {
      $this->www_path = $this->upload_path;
    }
    $action = "javascript:window.open('".$this->www_path.$this->value."?".time()."','".$this->name."','".$this->winParams.",width=".$this->winWidth.",height=".$this->winHeight."');";
    return  '<a onclick="'.$action.'" href="javascript:void(0);"><img src="'.filetype_icon($this->value).'" align="absmiddle" border="0">&nbsp;'.$this->value.'</a>';
  }

  function draw_preview_link(){
    if (is_array($this->preview)){
      $width  = $this->preview[0];
      $height = $this->preview[1];
    } else {
      $width  = $this->preview;
      $height = "";
    }
    if ($this->www_path=="") {
      $this->www_path = $this->upload_path;
    }
    $action = "javascript:window.open('".$this->www_path.$this->value."?".time()."','".$this->name."','".$this->winParams.",width=".$this->winWidth.",height=".$this->winHeight."');";
    $webPath = $this->www_path.$this->value;
    if (file_exists($this->serverPath($this->upload_root)."/".thumb_name($this->value))){
        $webPath = $this->www_path.thumb_name($this->value);
    } else {
        $webPath = $this->www_path.$this->value;    
    }
    return  '<a onclick="'.$action.'" href="javascript:void(0);"><img src="'.$webPath.'" width="'.$width.'" border="0" /></a>';
  }




  function execUpload(){
         
    $this->_getValue();
    
    $config['upload_path'] = $this->serverPath($this->upload_root); //$this->upload_path;
    $config['overwrite'] = $this->overwrite;
    $config['remove_spaces'] = $this->remove_spaces;
    
    if (isset($this->allowed_types)) $config['allowed_types'] = $this->allowed_types;
    if (isset($this->max_size)) $config['max_size']	= $this->max_size; 
    if (isset($this->max_width)) $config['max_width']  = $this->max_width;
    if (isset($this->max_height))  $config['max_height']  = $this->max_height;
  
    if (isset($this->file_name)){
     
      if (strpos($this->file_name,"#>")>0){
        $this->_parsePattern($this->file_name);
        
        if(count($this->parsed_fields)>0)
        {
          if(isset($this->data))
          {
            $data = $this->data->get_all();
            foreach ($this->parsed_fields as $field_name)
            {
              if(isset($data[$field_name]))
              {
                $this->file_name = str_replace("<#$field_name#>",$data[$field_name],$this->file_name);
              }
            }
          }
        }

      }
      $_FILES[$this->name."UserFile"]["name"] = $this->file_name;

    }
    
     

    $config['quality']  = 100;
    
    $this->upload->initialize($config);
    

 
    if ($this->upload->do_upload($this->name."UserFile")){
      $this->upload_data = $this->upload->data();
      

      if (isset($this->thumb)){
        if (is_array($this->thumb)){
		      $config['width'] = $this->thumb[0];
		      $config['height'] = $this->thumb[1];
        } else {
		      $config['width'] = $this->thumb;
        }
		    $config['image_library'] = 'GD2';
		    $config['source_image'] = $this->serverPath($this->upload_root).'/'.$this->upload_data["file_name"];
		    $config['create_thumb'] = TRUE;
		    $config['maintain_ratio'] = TRUE;

		    $this->image_lib->initialize($config);
		    $this->image_lib->resize();
		  }

      if (isset($this->chmod)) {
        chmod($this->serverPath($this->upload_root).'/'.$this->upload_data["file_name"], $this->chmod);
      }

      /*$this->image_lib->clear();*/
      return true;
    
    } else {
      $this->save_error = $this->label .": ".$this->upload->display_errors();
      return false;
    }
    
    
    
  }
  
  function execUnlink(){
  
     $this->_getValue();
     
     if ($this->delete_file)
     {
       $filename = $this->value;
       @unlink($this->serverPath($this->upload_root)."/".$filename);
       @unlink($this->serverPath($this->upload_root)."/".thumb_name($filename));
     }
  }  
  

  function autoUpdate($store=false){

      $this->_getValue();
			
			
			//required
			if ( ($_POST[$this->name] == "") && ($_FILES[$this->name."UserFile"]["name"] == "") ||
         ((isset($_POST[$this->name."CheckBox"])) && ($_POST[$this->name."CheckBox"] == "True")) )
			{

				if (isset($this->rule) && ($this->rule=="required"))
				{
					$this->save_error = sprintf($this->ci->lang->line('isset'), $this->label);
					return false;
				}
			}
			
      if ((($this->action == "update") || ($this->action =="insert")) ){
      
            
        if ($_FILES[$this->name."UserFile"]["name"]==""){ 
          if ( isset($_POST[$this->name."CheckBox"]) ){
            if ($_POST[$this->name."CheckBox"] == "True"){
              $this->execUnlink();
              $this->newValue = null;
            }
          } else {
            $this->newValue = $this->value;
          }
        } else {
         
          if ($this->execUpload()){
            $this->newValue = $this->upload_data["file_name"];
          } else {
            return false;
          }
        }
        
        if (is_object($this->data)){
           $this->data->set($this->name,$this->newValue);
           if($store){ $this->data->store(); }
        }
        
      }
      
      return true;
  }



  function build(){
	
    if(!isset($this->style)){
			$this->style = "width:290px;";
    }
		
    $this->_getValue();
    $output = "";
  
    switch ($this->status){
    
      case "show":
      case "disabled":  
      
        if ( (!isset($this->value)) || ($this->value == "") ){
          $output = RAPYD_FIELD_SYMBOL_NULL;
        } else {
          $output = $this->draw_link();
        }
        break;

      case "create":
      case "modify":

        $output = '<div style="">';
        if (!(!isset($this->value) || ($this->value == "")) ){
          
          $output .= $this->draw_link();
          $output .= "&nbsp;-&nbsp;";
            $attributes = array(
                    'name'        => $this->name . "CheckBox",
                    'id'          => $this->name . "CheckBox",
                    'value'       => 'True',
                    'checked'     => false,
                    'style'       => "vertical-align:middle;");
          $output .= form_checkbox($attributes)." ".RAPYD_FIELD_TEXT_UPLOAD_REMOVE."<br />\n";
        } 
        
       $output .= RAPYD_FIELD_TEXT_UPLOAD_BROWSE."<br />\n";
       $output .= form_hidden($this->name, $this->value);  
       
          $attributes = array(
            'name'        => $this->name . "UserFile",
            'id'          => $this->name . "UserFile",
            'size'        => $this->size,
            'onclick'     => $this->onclick,
            'onchange'    => $this->onchange,
            'class'       => $this->css_class,
            'style'       => $this->style);
        $output .= form_upload($attributes);
        $output .= "</div>" .$this->extra_output;
        
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