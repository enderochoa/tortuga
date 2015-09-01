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
 
 
ini_set('session.use_only_cookies', 1);

/**
 * rapyd_session id derived from PHP Native session library for CI, 
 * It has flash vars and a "page-persistence" system
 * usage:
 *
 * $this->rapyd->session->get("myvalue", "namespace"); // get $_SESSION["namespace"]["myvalue"];
 * $this->rapyd->session->get(); // get $_SESSION["rapyd"];
 * $this->rapyd->session->save("id", 500); // do $_SESSION["rapyd"]["id"] = 500;
 *
 * $key = $this->rapyd->session->save_persistence("cntr/funct/param"); 
 * //save few info about page status
 * //$page = array {
 * //    "back_post" => $_POST,
 * //    "back_uri" => $ci->uri->uri_string(),
 * //    "parent_uri" => $parent_uri
 * //  }
 * //$_SESSION["rapyd"][{passed_uri}][{microtimekey}] = $page;
 * //and return {microtimekey}  so you can use:
 * 
 * $page = $this->rapyd->session->get_persistence("cntr/funct/param", $key); 
 * to retrieve the saved page status.
 *
 *
 * @package    rapyd.components
 * @author     Felice Ostuni
 * @version    0.0.1
 * @access     public
 */
  class rapyd_session {

    var $persistence_duration = 25600; //max seconds before persistence expire
    var $persistence_limit = 1; //max page-status stored for each URI (olders are shifted out)

    // constructor
    function rapyd_session($duration=null,$limit=null)
    {
        if (session_id() == "") session_start();
        if(isset($duration))$this->persistence_duration = $duration; //max seconds before persistence expire
      	if(isset($limit))$this->persistence_limit = $limit; //max page-status stored for each URI (olders are shifted out)
    }

    function save($var, $val, $namespace = 'rapyd')
    {
      if ($var == null)
      {
        $_SESSION[$namespace] = $val;
      } else {
        $_SESSION[$namespace][$var] = $val;
      }
    }

    function save_enc($var, $val, $namespace = 'rapyd')
    {
      $ci =& get_instance();
      $val = $ci->encrypt->encode($val, $ci->rapyd->config->item("encryption_key"));
      $this->save($var, $val, $namespace);
    }


    function get($var = null, $namespace = 'rapyd')
    {
      if(isset($var))
      {
        return isset($_SESSION[$namespace][$var]) ? $_SESSION[$namespace][$var] : null;
      } else {
        return isset($_SESSION[$namespace]) ? $_SESSION[$namespace] : null;
      }
    }
    
    function get_dec($var=null, $namespace = 'rapyd')
    {
      if(isset($var))
      {
        $val = $this->get($var, $namespace);
        $ci =& get_instance();
        return $ci->encrypt->decode($val, $ci->rapyd->config->item("encryption_key"));
      } else {
        $values = $this->get(null, $namespace);
        foreach ($values as $val){
          $dec_values[] = $ci->encrypt->decode($val, $ci->rapyd->config->item("encryption_key"));
        }
        return $dec_values;
      }

    }
    
    
    function clear($var = null, $namespace = 'rapyd')
    {
      if (isset($_SESSION[$namespace]))
      {
        if(isset($var) && ($var !== null))
          unset($_SESSION[$namespace][$var]);
        else
          unset($_SESSION[$namespace]);
      }
    }
    

    ### persistence
    
    
    function persistence_sweeper($uri, $namespace)
    {
      if (!isset($_SESSION[$namespace][$uri])) return;
     
      //session persistence sort
      ksort($_SESSION[$namespace][$uri]);
      reset($_SESSION[$namespace][$uri]);
      
      //keep only a bit of sessions
      if (count($_SESSION[$namespace][$uri])>= $this->persistence_limit)
      {
        $_SESSION[$namespace][$uri] = array_slice($_SESSION[$namespace][$uri], -($this->persistence_limit-1), $this->persistence_limit-1);
      }
      
      //clear old sessions
      foreach($_SESSION[$namespace][$uri] as $session_key=>$session_data)
      {
        if (!isset($session_data["expire"]) || (time() > $session_data["expire"]))
        {
          unset($_SESSION[$namespace][$uri][$session_key]);
        }
      }
    
    }
    

    function save_persistence($uri, $page=null, $microtime=null, $parent_uri=null, $namespace = 'rapyd')
    {
      $ci =& get_instance();
      
      //key-microtime generation (uri friendly)
      if (!isset($microtime)){
        
        if ((float)phpversion() >= 5.0) {
          $microtime = microtime(1);
        } else {
          list($usec, $sec) = explode(' ', microtime());
          $microtime = ((float)$sec + (float)$usec); 
        }
        //final format is time(). 2 digit (cents/second)
        $microtime = str_replace(".","",round($microtime,2));
        $digits = preg_split('//', (string)$microtime, -1, PREG_SPLIT_NO_EMPTY);
        $microtime = implode("",array_map("num_to_alpha", $digits));
        
        
        if (!isset($page)){
          $page = array (
            "back_post" => serialize($_POST),
            "back_uri" => $ci->uri->uri_string(),
          );
        }

      }
      
      $expire = time() + $this->persistence_duration;
      $page["expire"] = $expire;
      
      $this->persistence_sweeper($uri, $namespace);
      
      //finally we can store new persistence
      $_SESSION[$namespace][$uri][$microtime] = $page;
      
      return $microtime;
    }
    
    
    function get_persistence($uri=null, $microtime=null, $namespace = 'rapyd')
    {
      if (isset($uri) && isset($microtime) && isset($_SESSION[$namespace][$uri][$microtime]))
      {
        return  $_SESSION[$namespace][$uri][$microtime];
        
      } elseif (!isset($uri) && isset($microtime) && isset($_SESSION[$namespace]) && count($_SESSION[$namespace])>0) {
      
        foreach ($_SESSION[$namespace] as $_uri => $_microtime) {
          
          if ($_microtime[0] == $microtime)
          {
            return  $_SESSION[$namespace][$_uri][$microtime];
          }
        }
      
      } elseif (isset($uri)) {
      
        if (isset($_SESSION[$namespace][$uri]) && count($_SESSION[$namespace][$uri])>0)
        {
        
          ksort($_SESSION[$namespace][$uri], SORT_STRING);
          end($_SESSION[$namespace][$uri]);
          list($key, $value) = each($_SESSION[$namespace][$uri]);
          return $value;
          
        } else {
        
          return null;
        }
        
      }
    }
    
    function clear_persistence($uri, $microtime, $namespace = 'rapyd')
    {
      $this->persistence_sweeper($uri, $namespace);
      
      if (isset($_SESSION[$namespace][$uri][$microtime])){
      
        unset($_SESSION[$namespace][$uri][$microtime]);
        
      }
      

    }


  }

function num_to_alpha($n)
{
  $keymap = "abcdefghil";
  return $keymap[$n];
}

function alpha_to_num($a)
{
  $keymap = "abcdefghil";
  return strpos($keymap,$a);
}
?>