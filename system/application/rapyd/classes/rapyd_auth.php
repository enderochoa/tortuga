<?php  if (!defined('BASEPATH')) exit('No direct script access allowed');
/*
|==========================================================
| Rapyd User Authentication Class
|
| (Basic User-Role/User-Permissions autentication class)
| based on "sentry" library by Chris Schletter http://codeigniter.com/wiki/sentry/ 
| 
|==========================================================
*/

/*
// basic DB structure needed


*/

/**
 * rapyd_auth id insired by auth
 *
 * @package    rapyd.components
 * @author     Felice Ostuni
 * @version    0.0.2
 * @access     public
 */
class rapyd_auth{
 
  var $auth_namespace = "rapyd_auth";
  var $field_username = "user_name";  //you can switch with email if login is email based (i.e. gmail login)
  var $password_encrypted = true;
  var $cookie_expiration = 31536000; //one year
  var $initialized = false;
  var $db = null;
	
  function rapyd_auth(&$rapyd)
  {
    $this->ci =& get_instance();
    $this->rapyd =& $rapyd;
    $this->session =& $this->rapyd->session;
  }
 
  function init()
  {
    if (!$this->initialized)
    {
      //load needed libraries     
      $this->ci->load->database();
      $this->ci->load->library('encrypt');
      $this->ci->load->helper('cookie');
      $this->initialized = true;
    }
    $this->db =& $this->ci->db;
  }
 
 
 
  /**
   * Try to validate a login, set user session data, and optionally store a persistence cookie (to autologin)
   *
   * @param  string  $username  Username to login
   * @param  string  $password  Password to match user
   * @param  bool  $session (true)  Set session data here. False to set your own
   * @param  int   $max_role  is the max role_id needed to save cookie (1: save for all users, 3: only for web,
   */
  function trylogin($username, $password, $cookie = true, $max_role=1)
  {
    $this->init();

    // Check details in DB
    $this->db->where($this->field_username, $username);
    $password_hash = ($this->password_encrypted)? $this->ci->encrypt->hash($password, 'md5'): $password;
    $this->db->where("password", $password_hash);
    $this->db->where("active", "y");
    $query = $this->db->get("users", 1);
  
    // If user/pass is OK then should return 1 row containing username,fullname
    $return = $query->num_rows();
    $row = $query->row();
    
    if($return)
    {
      // update last login datetime
      $this->db->set("lastlogin", date("Y-m-d H:i:s"));
      $this->db->where($this->field_username, $username);
      $this->db->update("users");
        
      // Set session data array
      $this->session->save_enc("user_name", $row->user_name, $this->auth_namespace);
      $this->session->save_enc("email",     $row->email,     $this->auth_namespace);
      $this->session->save_enc("name",      $row->name,      $this->auth_namespace);
      $this->session->save_enc("role_id",   $row->role_id,   $this->auth_namespace);
      $this->session->save_enc("user_id",   $row->user_id,   $this->auth_namespace);
      $this->session->save_enc("ip_address", $this->ci->input->ip_address(),   $this->auth_namespace);
      
      if( $cookie == true && $max_role <= $row->role_id){
         $this->_set_cookie($username,$password);
      }
      return true;
      
    } else {
      return false;
    }
  }
 
  /**
   * Try to login by cookie
   *
   * @return  void
   */
  function trylogin_bycookie()
  {
    $this->init();
    
    $cookie = get_cookie($this->auth_namespace);
    if (!$this->is_logged() && $cookie){
      $auth_fields = unserialize(stripslashes($cookie));
      return $this->trylogin( $auth_fields['username'] , $auth_fields['password'], true);
    }
    return false;
  }
 
 
  /**
   * Logout user and reset session data
   */
  function logout(){
    $this->_unset_cookie();
    $this->session->clear(null, $this->auth_namespace);
  }
   
 
    /**
   * set login cookie
   *
   * @return  void
   */
  function _set_cookie($username,$password)
  {
    $this->init();
    
    $auth_fields = array();
    $auth_fields['username'] = $username;
    $auth_fields['password'] = $password;
    $auth_data = serialize($auth_fields);
    
    $cookie = array('name' => $this->auth_namespace, 'value' => $auth_data, 'expire' => $this->cookie_expiration);
    set_cookie($cookie);
  }

  /**
   * unset login cookie
   *
   * @return  void
   */
  function _unset_cookie()
  {
    $this->init();
    
    $cookie = array('name' => $this->auth_namespace, 'value' => '', 'expire' => '');
    set_cookie($cookie);
  }
 

  /**
   * Check stored user_id  (user is logged)
   *
   * @return  bool  user is logged
   */
  function is_logged()
  {
    $this->init();
    
    $user_id = $this->session->get_dec('user_id',$this->auth_namespace);
    $ip_address = $this->session->get_dec('ip_address',$this->auth_namespace);
    
    if(!$user_id || !$ip_address) return false; //no valid session available;
    
    if($ip_address!=$this->ci->input->ip_address()){//hacking attemp;
      $this->logout();
      return false; 
    }
    return isset($user_id);
  }


  /**
   * Get stored user role
   *
   * @return  int role_id
   */
  function get_role()
  {
    return $this->session->get_dec('role_id',$this->auth_namespace);
  }
 
  /**
   * Get stored user_id
   *
   * @return  int user_id
   */
  function get_user_id()
  {
    return $this->session->get_dec('user_id',$this->auth_namespace);
  }
 
  /**
   * Get stored user data 
   *
   * @return  mixed an array of logged user data, or the single value for the given key (i.e. get_user_data("user_name"))
   */
  function get_user_data($key=null)
  {
    return $this->session->get_dec($key,$this->auth_namespace);
  }
  
  /**
   * Check user role
   *
   * @param   int  $role_id 
   * @param   bool $strict ("root" is also "admin", "operator" etc..)
   * @return  bool user has the role_id (or, if strict==false) or his role is more important 
   */
  function check_role($role_id, $strict=false)
  {
    $this->init();
    
    //not logged  
    if (!$this->is_logged())  return false;
    $rid = $this->session->get_dec('role_id', $this->auth_namespace);
    if (($strict && ($rid == $role_id)) || (!$strict && ($rid <= $role_id))){
      return true;
    } else {
      return false;
   }
  } 
 

  /**
   * Checks if user exist
   *
   * @param  string  $username
   * @return  bool  user exist
   */
  function user_exists($username)
  {
    $this->init();
    
    $this->db->select("user_id");
    $this->db->from("users");
    $this->db->where($this->field_username,$username);
    $query = $this->db->get();
    return $query->num_rows();
  }
 
  
  /**
   * Check if account is active
   *
   * @param  string  $username 
   * @return  bool  active
   */
  function is_active($username)
  {
    $this->init();
    
    $this->db->select("active");
    $this->db->from("users");
    $this->db->where($this->field_username,$username);
    $this->db->where("active","y");
    $query = $this->db->get();
    return $query->num_rows();
  }
 

  /**
   * Check if user has a permission
   *
   * @param  int  $permission_id
   * @return  bool  has permission
   */
  function has_permission($permission_id)
  { 
    $this->init();
    
    //not logged  
    if (!$this->is_logged())  return false;

    //is root
    if ($this->check_role(1)) return true;

    //security
    $role = $this->db->escape($this->get_role());
    $permission = $this->db->escape($permission_id);
    $uid = $this->db->escape($this->get_user_id());
    
    //role-permission
    $role_permission = false; //by default we assume that it's not allowed
    $query = $this->db->query("SELECT allow_deny FROM security_role_permission WHERE (role_id=$role AND permission_id=$permission) OR (role_id=$role AND permission_id=1)");
    if ($query->num_rows())
    {
      $row = $query->row();
      $role_permission = (bool)$row->allow_deny;
    }
    
    //user-permission (allow-deny)
    $query = $this->db->query("SELECT allow_deny FROM security_user_permission WHERE (user_id=$uid AND permission_id=$permission) OR (user_id=$uid AND permission_id=1)");
    if ($query->num_rows())
    {
      $row = $query->row();
      $user_permission = (bool)$row->allow_deny;
      return $user_permission;
    }
    
    return $role_permission;

  }

 
}

?>