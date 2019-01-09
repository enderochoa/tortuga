<?php  if (!defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Layout Helper
 *
 * @package		YATS 1.2 -- The Layout Library
 * @subpackage	Helpers
 * @category	Template
 * @author		Mario Mariani
 * @copyright	Copyright (c) 2006-2007, mariomariani.net All rights reserved.
 * @license		http://svn.mariomariani.net/yats/trunk/license.txt
 */

// ------------------------------------------------------------------------

/*
 * Layout Helper Constants
 */

//Necesario para la version 1.7 de CI
$_FCPATH = (strpos(FCPATH,'index.php')===false) ? FCPATH :dirname(FCPATH);
//-------

$thisobject =& get_instance();
$thisobject->config->load('layout');
define('ASSETS_URL', base_url() . $thisobject->config->item('layout_assets'));
define('ASSETS_PATH', $_FCPATH . '/' . $thisobject->config->item('layout_assets'));
define('THEME_PATH', $thisobject->config->item('layout_default') . '/');
define('SHARED_PATH', $thisobject->config->item('layout_shared'));
define('STYLES_PATH', $thisobject->config->item('layout_styles'));
define('IMAGES_PATH', $thisobject->config->item('layout_images'));
define('SCRIPT_PATH', $thisobject->config->item('layout_script'));

// ------------------------------------------------------------------------

/**
 * Display
 *
 * Tests and outputs a template variable to display it on a view. Its use
 * is recommended directly in the view files.
 *
 * Prototype: display("template-variable", array("library" => "function"));
 * Example:	  display("hello_user", array("agent" => "is_browser"));
 * Note: to execute the validation correctly all validators must 
 *		 return a boolean value -- if FALSE display() will echo NULL. 
 *
 * @access	public
 * @param	string	template variable i.e. data to display
 * @param	array	validation calls
 * @return	mixed	string with data to display or null
 */ 
function display($item, $validators = null)
{
	if (is_array($validators))
	{
		$thisobject =& get_instance();
		foreach ($validators as $key => $value)
		{
			if ($thisobject->$key->$value() === FALSE) return;
		}
	}

	return $item;
}

// ------------------------------------------------------------------------

/**
 * Dump
 *
 * Tests and returns a template variable to display it on a view. 
 *
 * Prototype: dump("template-variable", array("library" => "function"));
 * Example:	  dump("hello_user", array("agent" => "is_browser"));
 * Note: to execute the validation correctly all validators must 
 *		 return a boolean value -- if FALSE dump() will return NULL. 
 *
 * @access	public
 * @param	string	template variable i.e. data to display
 * @param	array	validation calls
 * @return	mixed	string with data to display or null
 */ 
function dump($item, $validators = null)
{
	if (is_array($validators))
	{
		$thisobject =& get_instance();
		foreach ($validators as $key => $value)
		{
			if ($thisobject->$key->$value() === FALSE) return;
		}
	}

	return $item;
}

// ------------------------------------------------------------------------

/**
 * Property
 *
 * Outputs a template property (those config variables started by 'app_').
 *
 * Prototype: property("template-property");
 * Example:	  property("app_title");
 *
 * @access	public
 * @param	string	the property name
 * @return	mixed	string with the property value or null if it's not found
 */ 
function property($item)
{
	$thisobject =& get_instance();
	return (!empty($item) && strstr($item, 'app_') !== FALSE) ? $thisobject->config->item($item) : null;
}

// ------------------------------------------------------------------------

/**
 * Style
 *
 * Outputs a css link tag
 *
 * Prototype: style("archive.css", additional-attributes);
 * Example:	  style("main.css", array('media'=>'screen', 'charset'=>'utf-8'));
 *
 * @access	public
 * @param	string	the filename inside the template's css folder
 * @param	string	array with miscellaneous tag attributes
 * @return	string	string with the property value 
 */ 
function style($file, $attributes = null)
{
	if (empty($file)) return;

	$thisobject =& get_instance();
	if (file_exists(ASSETS_PATH . SHARED_PATH . STYLES_PATH . $file))
	{
		$path2file = ASSETS_URL . SHARED_PATH . STYLES_PATH . $file;
	}
	else
	{
		if (file_exists(ASSETS_PATH . THEME_PATH . STYLES_PATH . $file))
		{
			$path2file = ASSETS_URL . THEME_PATH . STYLES_PATH . $file;
		}
		else
		{
			$path2file = ASSETS_URL . SHARED_PATH . STYLES_PATH . $file;
			log_message('error', 'Unable to load the requested file: ' . $file);
		}
	}
	if (is_array($attributes))
	{
		$retval = '<link rel="stylesheet" href="'. $path2file .'" type="text/css" ';
		foreach ($attributes as $key => $value)
		{
			$retval .= "$key=\"$value\" ";
		}
		$retval .= "/>\n";
	}
	else
	{
		$retval = '<link rel="stylesheet" href="'. $path2file .'" type="text/css" media="all" />' . "\n";
	}

	return $retval;
}

// ------------------------------------------------------------------------

/**
 * Script
 *
 * Outputs a script tag
 *
 * Prototype: script("archive.js");
 * Example:	  script("main.js");
 *
 * @access	public
 * @param	string	the filename inside the template's css folder
 * @return	string	string with the property value
 */ 
function script($file)
{
	if (empty($file)) return;

	$thisobject =& get_instance();
	if (file_exists(ASSETS_PATH . SHARED_PATH . SCRIPT_PATH . $file))
	{
		$path2file = ASSETS_URL . SHARED_PATH . SCRIPT_PATH . $file;
	}
	else
	{
		if (file_exists(ASSETS_PATH . THEME_PATH . SCRIPT_PATH . $file))
		{
			$path2file = ASSETS_URL . THEME_PATH . SCRIPT_PATH . $file;
		}
		else
		{
			log_message('error', 'Unable to load the requested file: ' . $file);
			$path2file = ASSETS_URL . SHARED_PATH . SCRIPT_PATH . $file;
		}
	}
	$charset=$thisobject->config->item('charset');
	return '<script src="'. $path2file .'" type="text/javascript" charset="'.$charset.'"></script>' . "\n";
}


// ------------------------------------------------------------------------

/**
 * Image
 *
 * Outputs a img tag
 *
 * Prototype: image("image.yyz", "alt/title-attribute", "additional-attributes");
 * Example:	  image("movingpictures.jpg", "Moving Pictures", array('style'=>'border:0;float:right;margin:10px;'));
 *
 * @access	public
 * @param	string	the filename inside the template's image folder
 * @param	string	image description
 * @param	string	array with miscelaneous tag attributes
 * @return	string	string with the property value 
 */ 
function image($file, $alt = '#', $attributes = null)
{
	if (empty($file)) return;

	$thisobject =& get_instance();
	if (file_exists(ASSETS_PATH . SHARED_PATH . IMAGES_PATH . $file))
	{
		$path2file = ASSETS_URL . SHARED_PATH . IMAGES_PATH . $file;
	}
	else
	{
		if (file_exists(ASSETS_PATH . THEME_PATH . IMAGES_PATH . $file))
		{
			$path2file = ASSETS_URL . THEME_PATH . IMAGES_PATH . $file;
		}
		else
		{
			log_message('error', 'Unable to load the requested file: ' . $file);
			$path2file = ASSETS_URL . SHARED_PATH . IMAGES_PATH . $file;
		}
	}
	if (isset($path2file)) list($width, $height, $type, $attr) = @getimagesize($path2file);
	$retval = '<img src="'. (isset($path2file) ? $path2file : $file) .'" '. (isset($attr) ? $attr : null) .' alt="'. $alt .'" title="'. $alt .'" ';
	if (is_array($attributes)) 
	{
		foreach ($attributes as $key => $value) $retval .= "$key=\"$value\" ";
	}
	$retval .= "/>";

	return $retval;
}

/**
* FavIcon
*
* Outputs a favorite icon tag
*
* Prototype: favicon("file.ext");
* Example:	 favicon("site-icon.ico");
*
* @access	public
* @param	string	the filename within the template's images folder
* @return	string	string with the property value
*/
function favicon($file)
{
	if (empty($file)) return;

	$thisobject =& get_instance();
	if (file_exists(ASSETS_PATH . SHARED_PATH . IMAGES_PATH . $file))
	{
		$path2file = ASSETS_URL . SHARED_PATH . IMAGES_PATH . $file;
	}
	else
	{
		if (file_exists(ASSETS_PATH . THEME_PATH . IMAGES_PATH . $file))
		{
			$path2file = ASSETS_URL . THEME_PATH . IMAGES_PATH . $file;
		}
		else
		{
			log_message('error', 'Unable to load the requested file: ' . $file);
			$path2file = ASSETS_URL . SHARED_PATH . IMAGES_PATH . $file;
		}
	}

	return '<link rel="shortcut icon" href="'. $path2file .'" type="image/ico" />' . "\n";
}

/**
* Hyperlink
*
* Outputs a link tag to other places on the web
*
* Prototype: hyperlink("url", "link-title", "target-attribute");
* Example:	 hyperlink("http://www.mariomariani.net", "M2/Blog", "blank");
*
* @access	public
* @param	string	the URL
* @param	string	link's title
* @param	string	target window (no underscore before the target)
*					- blank: all the links will open in new windows
* 					- self: all the links will open in the same frame they where clicked (default)
* 					- parent: all the links will open in the parent frameset
* 					- top: all the links will open in the full body of the window
* @param	string	array with miscelaneous tag attributes
* @return	string	string with the property value
*/
function hyperlink($location, $title, $target = 'self', $attributes = null)
{
	if (empty($location) || empty($title)) return;
	
	$retval = '<a href="'. $location .'" target="_'. $target .'" title="'. $title .'"';
	if (is_array($attributes)) 
	{
		foreach ($attributes as $key => $value) $retval .= " $key=\"$value\"";
	}
	$retval .= ">$title</a>\n";

	return $retval;
}

// EOF
?>