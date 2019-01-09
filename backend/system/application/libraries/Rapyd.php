<?php if (!defined('BASEPATH')) exit('No direct script access allowed');
/**
 * Rapyd Components
 *
 * An open source library for CodeIgniter application development framework for PHP 4.3.2 or newer
 *
 * @package		rapyd.components
 * @author		Felice Ostuni
 * @license		http://www.fsf.org/licensing/licenses/lgpl.txt LGPL
 * @version		0.9.5
 * @filesource
 */
 
// ------------------------------------------------------------------------


$obj =& get_instance();
 
if (!defined('RAPYD_PATH')){

  define('RAPYD_VERSION',  "0.9.8");

  define('BASEURL', $obj->config->slash_item('base_url'));

  include(APPPATH.'config/rapyd'.EXT);


	/*****************************************************************************************
	 WARNING : If the application directory is out of the 'CI_root/system/' directory we have
	 to set the application_folder with complete system path in the index.php
	 AND If the application directory is out of the 'CI_root'(BASEURL) directory we have to leave the 
	 images and libraries folder into the apache documents root and set it manualy
	 into the Rapyd config....
	 *****************************************************************************************/
$_FCPATH = (strpos(FCPATH,'index.php')===false) ? FCPATH :dirname(FCPATH);
	 $rapyd_apppath = str_replace( str_replace("\\", "/", realpath($_FCPATH)).'/' ,"", APPPATH );

  define('APPPDIR',  BASEURL.$rapyd_apppath);

  
  define('RAPYD_DIR',  APPPDIR."rapyd/");

  //Now we only fix the elements directory.Each directory path (images and css) are given by the config
	$elemnt = ($rpd["design_elements_path"]!="") ? $rpd["design_elements_path"] : RAPYD_DIR."elements/";
		
	define('RAPYD_ELEMENTS', $elemnt);

  $lib = ($rpd["libraries_path"]!="") ? $rpd["libraries_path"] : RAPYD_DIR."libraries/";
 
  define('RAPYD_LIBRARIES', $lib);
  define('RAPYD_PATH', APPPATH."rapyd/");  
}

//WARNING -- because components use the lang()helpers fonction, we have to load it manualy if the helpers is not loaded..
//I think that this halpers function can be put into an other helpers because it is related only to CI syntax not Rapyd lang....
if(isset($rpd['rapyd_lang_ON']) && $rpd['rapyd_lang_ON']===TRUE)
{
	require_once(RAPYD_PATH.'classes/rapyd_lang'.EXT);
	require_once(RAPYD_PATH.'helpers/lang'.EXT);
}
else
{
	//became a common function....
	function lang( $line_key = '', $args = '' ){
		$obj =& get_instance();
	  return $obj->lang->line($line_key, $args);
	}
}

if(isset($rpd['rapyd_auth_ON']) && $rpd['rapyd_auth_ON']===TRUE)
{
	require_once(RAPYD_PATH.'classes/rapyd_auth'.EXT);
  require_once(RAPYD_PATH.'helpers/auth'.EXT);
}

//MYFW install
if(!isset($rpd['use_rapyd_session_lib']) || $rpd['use_rapyd_session_lib']===TRUE)
{
	require_once(RAPYD_PATH.'classes/rapyd_session'.EXT);
}

unset($rpd);
unset($obj);


/**
 * helpers inclusion
 */
require_once(RAPYD_PATH.'helpers/datehelper'.EXT);
require_once(RAPYD_PATH.'helpers/html'.EXT);
require_once(RAPYD_PATH.'helpers/highlight'.EXT);
require_once(RAPYD_PATH.'classes/rapyd_uri'.EXT);
require_once(RAPYD_PATH."helpers/urlendecode.php");

/**
 * common inclusion
 */
require_once(RAPYD_PATH.'common'.EXT);



/**
 * rapyd library main class
 *
 * @package    rapyd.components
 * @access     public
 */
	class Rapyd {
	
		var $config = array();
		var $load;
		
    var $js = array();
    var $css = array();
    var $script = array();
    var $style = array();
    var $db;
    var $data_conn = '';//Should be set by config 
    var $html		=null;
		
		function Rapyd()
		{
			$this->ci =& get_instance();
      
      $rpd = array();
      if (file_exists(APPPATH.'config/rapyd'.EXT))
      {
        include(APPPATH.'config/rapyd'.EXT);
      }
      
      foreach ($rpd["uri_keywords"] as $key=>$value){
        if (!defined('RAPYD_URI_'.strtoupper($key))){
          define('RAPYD_URI_'.strtoupper($key), $value);
        }
      }
      
      //we load the config data connection....
      $this->data_conn=(isset($rpd['data_conn']))?$rpd['data_conn']:'';
      
      $this->config = new rapyd_config($rpd);

//MYFW install
			if(!isset($rpd['use_rapyd_session_lib']) || $rpd['use_rapyd_session_lib']===TRUE)
			{  
      	$this->session = new rapyd_session($rpd['persistence_duration'],$rpd['persistence_limit']);
      }
      else
      {
				if(!isset($this->ci->session))$this->ci->load->library('session');
				$this->session = & $this->ci->session;      
    	}
      
     	$this->uri = new rapyd_uri($this);       

            
      if(isset($rpd['rapyd_lang_ON']) && $rpd['rapyd_lang_ON']===TRUE)
      {
         $this->language = new rapyd_lang($this);
    	}
  	
    	

      if(isset($rpd['rapyd_auth_ON']) && $rpd['rapyd_auth_ON']===TRUE)
      {
         $this->auth = new rapyd_auth($this);
    	}
      
      $this->load_language();
	$this->html = new Html();
		}
/*
   New Rapyd functions to preload $this->db when we want to preset it (complex query) for DF and DS (DG, DT).
   SEE THE SUPERCRUD CONTROLLER SAMPLE
   
   also functions to switch and reset the rapyd data_conn group.
   For the moment we unset the Rapyd database object when we switch the curent rapyd connection.
   Think that we use this db object only for DF and DS,DG,DT shared work
*/
    //We set the new Rapyd data connection group or reset it to the config value if it use without arg..
    //We can use '' to set the CI default connection group......
  	function set_connection($data_conn=null)
  	{
  		if(!isset($data_conn) || !is_string($data_conn))
  		{
  				$config_conn =$this->config->item("data_conn");
  			  $data_conn=(isset($config_conn) && is_string($config_conn))?$this->config->item("data_conn"):'';
  		}
  		$this->data_conn = $data_conn;
  		unset($this->db);
  	}
  	
  	//If we want to load it before to create the DF or DS component for woking on query first....
  	function load_db()
  	{
  		unset($this->db);
  		$this->db = $this->ci->load->database($this->data_conn,TRUE);
  	}
    
		function load()
		{
			$components = func_get_args();
			foreach($components as $component)
			{
				include_once(RAPYD_PATH.'classes/'.$component.EXT);
			}
			  //$this->load_language();			
		}
		

	  //this function return the current images or css design path according to the current THEME
	  //We have to use this function every where in our code instead of the RAPYD_IMAGES constant....
	  //Specialy in the component template code for the datagrid images for example.
	  //We can change the default name for the assets folder in the config but all theme design have to use the same
	  //asset folder name for each type.....
	  //get_elements_path() without Arg return the images (defaul type) directory, like RAPYD_IMAGES 
		function get_elements_path($elmnts_file='',$elmnts_type = 'image')
		{		  
			  $elemnt_file_path = ($this->config->item("design_elements_path")!="")? $_SERVER["DOCUMENT_ROOT"].substr($this->config->item("design_elements_path"),1): APPPATH."rapyd/elements/";	
		    $design_folder_name = $this->config->item('assets_type_folder_name');
		    
		    $theme_file_url = RAPYD_ELEMENTS.$this->config->item('theme').'/'.$design_folder_name[$elmnts_type].'/'.$elmnts_file;
		    $theme_file_path = $elemnt_file_path.$this->config->item('theme').'/'.$design_folder_name[$elmnts_type].'/'.$elmnts_file;
		    if(!file_exists($theme_file_path))
		    {
		    	$shared_file_url= RAPYD_ELEMENTS.$this->config->item('shared_theme_design').'/'.$design_folder_name[$elmnts_type].'/'.$elmnts_file;
		    	$shared_file_path=$elemnt_file_path.$this->config->item('shared_theme_design').'/'.$design_folder_name[$elmnts_type].'/'.$elmnts_file;

		    	if(file_exists($shared_file_path)&& is_file($shared_file_path)){return $shared_file_url;}
		    }
		    return $theme_file_url;
		}		
		
		/**
		 * load rapyd Language file
		 *
		 * @access public
		 * @return  void
		 */
		function load_language()
		{
			$language = $this->ci->config->item('language');
			
			if (file_exists(RAPYD_PATH.'language/'.$language.EXT))
			{
				include_once(RAPYD_PATH.'language/'.$language.EXT);
			} else {
				show_error("Error, rapyd language not found: ".RAPYD_PATH.'language/'.$language.EXT);
			}
	  }  
	  
		function set_view_path()
		{
			$this->ci->load->_ci_view_path = RAPYD_PATH.'views/'.$this->config->item("theme").'/';
		}
		
		function reset_view_path()
		{
			$this->ci->load->_ci_view_path = APPPATH.'views/';
		}


		function get_head(){
			
			$buffer = "";
			
			//loading the theme components style from css file instead of the css.php version...
			$this->css[]=$this->get_elements_path("rapyd_components.css","css");			
			//css links
			foreach ($this->css as $css){
				$buffer .= $this->html->cssLinkTag($css);
			}
			
			//javascript links
			foreach ($this->js as $js){
				$buffer .= $this->html->javascriptLinkTag($js);
			}

			//javascript in page
			$script = join("\n\n",$this->script)."\n";
			$buffer .= $this->html->javascriptTag($script);

			//style in page
			$style = join("\n\n",$this->style)."\n";
			$buffer .= $this->html->cssTag($style); 

			return $buffer;
		}

	}

/**
 * rapyd config class
 *
 * @package    rapyd.components
 * @access     private
 */
	class rapyd_config{
	
		var $config = array();
		
		function rapyd_config(&$config)
		{
			$this->config = &$config;
      
		}

		function item($item)
		{

			return $this->config[$item];
		}

		function set_item($item, $value)
		{
			$this->config[$item] = $value;
		}
		
	}




?>
