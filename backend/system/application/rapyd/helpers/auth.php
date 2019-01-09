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
 * helper functions to use $this->rapyd->auth method directly in your views as global functions
 */
function is_logged()
{
	$ci =& get_instance();
	return $ci->rapyd->auth->is_logged();
}

function check_role($role_id, $strict=false)
{
	$ci =& get_instance();
	return $ci->rapyd->auth->check_role($role_id, $strict);
}

function get_role()
{
	$ci =& get_instance();
	return $ci->rapyd->auth->get_role();
}

function get_user_id()
{
	$ci =& get_instance();
	return $ci->rapyd->auth->get_user_id();
}

function get_user_data($key=null)
{
	$ci =& get_instance();
	return $ci->rapyd->auth->get_user_data($key);
}

function has_permission($permission_id)
{
	$ci =& get_instance();
	return $ci->rapyd->auth->has_permission($permission_id);
}

?>