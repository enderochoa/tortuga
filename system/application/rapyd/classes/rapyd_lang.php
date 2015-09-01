<?php  if (!defined('BASEPATH')) exit('No direct script access allowed');
/*
|==========================================================
| Rapyd Multi-Language & ip-to-country Support Class
|
| Support app. internationalization by a persistence system (sessions) plus an optional ip-to-country detection
|==========================================================
*/
 

class rapyd_lang{
 
	var $object;

	var $ci;
	var $rapyd;

	var $db;
	var $session;
	var $languages;
	var $language;

	//ip-to-country
	var $prefix1;
	var $prefix2;
	var $country;
	
	function rapyd_lang(&$rapyd)
	{
		$this->ci =& get_instance();
		$this->rapyd =& $rapyd;
		$this->session =& $rapyd->session;
    
    //var_dump($this->rapyd);
    //die();
    
		$this->languages = $this->rapyd->config->item("languages");
    
		//get current language from current session or rapyd config file
		$this->get_language();
	
	}
 
	/**
	 * get current language (form session or config file)
	 * and load rapyd language file
	 *
	 * @access public 
	 * @return  current luanguage
	 */
	// We add here the uri switching lang clause detection and traitment=> auto sniff language....
	function get_language()
	{
		$uri_language = $this->get_language_from_uri();
		$sess_language = ($uri_language===FALSE)?$this->session->get("language","rapyd"):null;
		
		if (isset($uri_language) && in_array($uri_language,array_keys($this->languages)))
		{
			$this->language = $this->languages[$uri_language];
			$this->session->save("language", $this->language, "rapyd");
			
		//languase is already in session
		}elseif (isset($sess_language) && in_array($sess_language,array_values($this->languages)))
		{

			$this->language = $sess_language;
      
    ## browser-to-language
		} elseif ($this->rapyd->config->item("browser-detect")){

      $this->language = $this->get_language_from_browser();
      
    ## ip-to-country-to-language
		} elseif ($this->rapyd->config->item("ip-detect")){

      //load needed libraries 
      if (!isset($this->ci->db))
      {
        $this->ci->load->database();
      }
      $this->db =& $this->ci->db;

      $this->country = $this->ip_to_country($_SERVER["REMOTE_ADDR"]);
      $this->language = $this->get_language_from_country($this->country["prefix2"]);
    
    } else {
      $this->language = $this->rapyd->config->item("language");
    }	
     
    $this->ci->config->set_item('language', $this->language);
    return $this->language;
    
  }


	/**
	 * seve current detected language in session
	 *
	 * @access public 
	 * @return  void
	 */
	function save_language()
	{
		$this->set_language($this->language);
  }

	/**
	 * clear current stored language in session
	 *
	 * @access public 
	 * @return  void
	 */
	function clear_language()
	{
		$this->session->clear("language","rapyd");
  }

	/**
	 * set a new language env
	 *
	 * @access public 
	 * @param  string  $language  new language ("english", "italian", etc..)
	 * @return  void
	 */
	function set_language($language)
	{
		//languase is already in session
		if (isset($language) && in_array($language,array_values($this->languages)))
		{
			$this->language = $language;
		} else {
			$this->language = $this->rapyd->config->item("language");
		}	
		$this->session->save("language", $this->language, "rapyd");
		
		$this->ci->config->set_item('language',$this->language);
   
  }
 
 

  function get_language_from_uri()
  {	

    $segments = $this->ci->uri->segments;

    if (count($segments)>0){

        if (in_array($segments[1],array_keys($this->rapyd->config->item("languages"))))
        {
          $this->clear_language();
          $language = $segments[1];
          //MODIF 0 URI
          $segments = array_slice($segments,1);
          //reindexing
          $i = 1;
          foreach ($segments as $val) { $segments[$i++] = $val; }
          unset($segments[0]);
          $this->ci->uri->segments = $segments;


					//like the CI_router Lib is buggy whe have to remove the switching language URI clause if it is also
					//Present into rsegment.
        	$rsegments = $this->ci->uri->rsegments;
	        if ($rsegments[1]==$language)
	        {
							$rsegments = array_slice($rsegments,1);
		          //reindexing
		          $i = 1;
		          foreach ($rsegments as $val) { $rsegments[$i++] = $val; }
		          unset($rsegments[0]);				
		          $this->ci->uri->rsegments = $rsegments;        	
	        }

          return $language;
        }
    }
    return false;
  }
  
//empty alias function to back compatibility with old controller
	function sniff_language()
	{
	   return;
	}
	

############ browser-to-language functions #############
  
  //inspired to Language Detect Library by Roland Blochberger


  function get_language_from_browser()
  {
    $language = $this->rapyd->config->item("language");  
    
    $accept_langs = $this->ci->input->server('HTTP_ACCEPT_LANGUAGE');
    if ($accept_langs !== false)
    {
        //explode languages into array
        $accept_langs = strtolower($accept_langs);
        $accept_langs = explode(",", $accept_langs);

      foreach ($accept_langs as $lang)
      {

          $pos = strpos($lang,';');
          if ($pos !== false)
          { $lang = substr($lang,0,$pos); }
          // get CI language directory
          $check_lang = $this->check_lang($lang);
					
          // finish search if we support that language
          if ($check_lang !== false) { 
					  $language = $check_lang;
					  break; 
					}
      }
    }
		return $language;    
  }


  function check_lang(&$lang)
  {
  
    if (!array_key_exists($lang, $this->rapyd->config->item("browser-to-language")))
    {
      if (strlen($lang) == 2)
      {
        // we had already the base language: not found so give up
        return false;
      }
      else
      {
        // try base language
        $lang = substr($lang, 0, 2);
        if (!array_key_exists($lang, $this->rapyd->config->item("browser-to-language")))
        {
          return false;
        }
      }
    }
    $languages = $this->rapyd->config->item("browser-to-language");
    return $languages[$lang];
  }



############ ip-to-country functions #############

	/**
	 * convert an IP to a network address (to be compared with ip-to-country db)
	 * note: mysql and some dbs have this function builtin!
	 *
	 * @param  string  $ip of current user
	 * @return  string network address 
	 */
	function inet_aton($ip)
	{
		$iparr = explode(".",$ip);
		return ($iparr[0] * pow(256,3)) + ($iparr[1] * pow(256,2)) + ($iparr[2] * 256) + $iparr[3]; 
	}


	function ip_to_country($ip)
	{
		$address = $this->inet_aton($ip);

		$sql = "SELECT * FROM net_blocks WHERE $address>=ip_from AND $address<=ip_to";
		$query = $this->ci->db->query($sql);

		if ($query->num_rows() > 0)
		{
			$country = $query->row_array();
			return $country; 
		} else {
			return false;
		}
	}

	function get_language_from_country($country)
  {
    $ctl = $this->rapyd->config->item("country-to-language");
    if (array_key_exists($country, $ctl))
    {
      return $ctl[$country];
    } else {
      return $this->rapyd->config->item("language");
    }
    
  }


}

?>