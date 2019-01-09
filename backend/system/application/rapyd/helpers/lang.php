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
 * helper functions for $this->rapyd->language
 */
function language_links($separator="&nbsp;")
{
	$ci =& get_instance();
  $languages = $ci->rapyd->config->item("languages");

  if (count($languages)>1 ){
  	//MODIF 1.6
    //$current_uri = ($ci->rapyd->config->item('rapyd_ruri_ON')===true)?implode("/",$ci->uri->rsegment_array()):implode("/",$ci->uri->segment_array());
    $current_uri =implode("/",$ci->uri->segment_array());
    
    //remove /process from current uri
    $form_process_regex  = "#^(.+)/process$#";
    if (preg_match($form_process_regex, $current_uri)){
     $current_uri = preg_replace($form_process_regex, '$1', $current_uri);
    }
    
    foreach ($ci->rapyd->language->languages as $lang_uri=>$lang){
      $links[] = ($lang == $ci->rapyd->language->language) ? '<img src="'.$ci->rapyd->get_elements_path('flags/'.$lang.'_current.gif').'" style="border:0" />' : '<a href="'.$ci->config->site_url("$lang_uri/$current_uri").'"><img src="'.$ci->rapyd->get_elements_path('flags/'.$lang.'.gif').'" style="border:0" /></a>';
    }
    return implode($separator,$links);
  } else {
    return "";
  }
}


function sniff_language()
{
	$ci =& get_instance();
  $ci->rapyd->language->sniff_language();
}



function lang( $line_key = '', $args = '' )
{
	$ci =& get_instance();
  return $ci->lang->line($line_key, $args);
}


/**
 * url_helper replacements
 */

function keep_lang($uri = '')
{
	$ci =& get_instance();
  $languages = $ci->rapyd->config->item("languages");
  
  if (count($languages)>1){
    if ($ci->rapyd->config->item("uri_keep_lang")){ 
      //check if the passed uri has a valid language as first segment, otherwise insert current language
      $languages_regex  = "#^(".implode("|",array_keys($languages)).")/(.+)$#";
      $form_process_regex  = "#^(.+)/process$#";
      if (!preg_match($languages_regex, $uri)){
        //remove /process from current uri
        if (preg_match($form_process_regex, $uri)){
         $uri = preg_replace($form_process_regex, '$1', $uri);
        }
        $languages_flipp = array_flip($languages);
        $uri = $languages_flipp[$ci->rapyd->language->language]."/".$uri;
      }
    }
  }
  return $uri;

}




function site_url_lang($uri = '')
{
	$ci =& get_instance();
	return $ci->config->site_url(keep_lang($uri));
}


function anchor_lang($uri = '', $title = '', $attributes = '')
{
  $title = (lang($title))? lang($title) : $title;
  return anchor(keep_lang($uri), $title, $attributes);
}

function anchor_popup_lang($uri = '', $title = '', $attributes = FALSE)
{
  $title = (lang($title))? lang($title) : $title;
  return anchor_popup(keep_lang($uri), $title, $attributes);
}

function redirect_lang($uri = '', $method = 'location')
{
  return redirect(keep_lang($uri), $method);
}

?>