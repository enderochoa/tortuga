<?php if (!defined('BASEPATH')) exit('No direct script access allowed');
/**
 * Rapyd Components
 *
 * An open source library for CodeIgniter application development framework for PHP 4.3.2 or newer
 *
 * @package		rapyd.components
 * @author		Felice Ostuni <felix@rapyd.com>
 * @license		http://www.fsf.org/licensing/licenses/lgpl.txt LGPL
 * @copyright	Copyright (c) 2006 Felice Ostuni - http://www.rapyd.com
 * @version		0.9
 * @filesource
 */
 
 
 
	include_once("loader.php");

  if (version_compare(phpversion(), '5.0') < 0 && !function_exists('clone')) {
    eval(' function clone($object) { return $object; }');
  }

  /**
   * Questa funzione consente di utilizzare una stringa per effettuare chiamate a funzioni php.
   * La ricorsivita' consente anche funzioni innestate.
   *
   * E' utilizzabile nei casi in cui si vuole fare e si puo' fare a meno di eval 
   * 
   * Consente una formattazione veloce dei campi di rapyd
   *
   * <code>
   *   $pattern  = 'e si... <strtolower><substr>Io Sono fesso|0|8</substr>Bravo</strtolower> <strtoupper>Davvero!</strtoupper>';
   *   echo htmlspecialchars(replaceFunctions($pattern)); 
   * </code>
   */
  function replaceFunctions($content){
    $ci =& get_instance();
		$rapyd =& $ci->rapyd;
		
    //allowed function
    $functions = $rapyd->config->item("replace_functions");
    //parameter separator
    $arg_sep = $rapyd->config->item("argument_separator");
  
    foreach ($functions as $function){
      $tagName = $function;
      $beginTag = "<".$tagName.">";
      $beginLen = strlen($beginTag);    
      $endTag   = "</".$tagName.">";
      $endLen   = strlen($endTag);
      $beginPos = strpos($content, $beginTag);
      $endPos   = strpos($content, $endTag);
      
      $subcontent = "";
      
      if($endPos>0){
      
        $subcontent = substr($content, $beginPos + $beginLen, $endPos - $beginPos - $beginLen);
        
        foreach ($functions as $nestedfunction){
          
          $nestedTag   = "</".$nestedfunction.">";
          if (strpos($subcontent, $nestedTag)>0){
            $subcontent = replaceFunctions($subcontent);
          }
          
        }
        //???double barre a repercuter dans code ou faire une synthax identique pour les noms de fonctions
        if (strpos($subcontent,$arg_sep)===false){

          if (substr($function,0,9)=="callback_"){
            $method = substr($function,9);
            $result = $ci->$method($subcontent);
          } else {
            $result = $function($subcontent);
          }
          
        } else {
        	 //???double barre 
          $arguments = explode($arg_sep,$subcontent);
					for($i=0;$i<count($arguments);$i++){
						if(defined($arguments[$i]))
							$arguments[$i]=constant($arguments[$i]);
					}
					//print_r($arguments);

          if (substr($function,0,9)=="callback_"){
            $method = substr($function,9);
            $result = call_user_func_array(array(&$ci, $method), $arguments);
            
          } else {
          	//if($function == "htmlspecialchars")echo print_r($arguments,true);
						//print_r($arguments);
            $result = call_user_func_array($function, $arguments);
          }
           
        }
        
        $content = substr($content, 0, $beginPos) . $result . substr($content, $endPos + $endLen);
        
        $endPos  = strpos($content, $endTag);
        if($endPos>0){
          $content = replaceFunctions($content);
        }
      }
    
    } 

    return $content;
  }
  
  
  
    //spostare in Html
  function filetype_icon($filename){
    $ci =& get_instance();
		$rapyd =& $ci->rapyd;
	
    if ($filename=="") return "";
    $filename = strtolower($filename);
    
    $arrfilename = explode (".",$filename);
    $extension = array_pop($arrfilename);

    switch($extension) {


      case "bmp": 
                  $icon = "image.gif";
                  break;
      case "jpg": 
      case "jpeg":       
                  $icon = "jpg.gif";
                  break;
      case "gif": 
                  $icon = "gif.gif";
                  break;
      case "tif": 
      case "tiff": 
                  $icon = "tiff.gif";
                  break;
      case "dwg": 
                  $icon = "dwg.gif";
                  break;
      case "dwf": 
                  $icon = "dwf.gif";
                  break;
      case "dot": 
      case "doc": 
                  $icon = "doc.gif";
                  break;
      case "xls": 
                  $icon = "xls.gif";
                  break;
      case "pdf": 
                  $icon = "pdf.gif";
                  break;
      case "xml": 
                  $icon = "icons.gif";
                  break;
      case "txt": 
                  $icon = "txt.gif";
                  break;  
      case "mov": 
                  $icon = "mov.gif";
                  break;  
      case "html": 
      case "htm": 
                  $icon = "htm.gif";
                  break;
      case "exe": 
                  $icon = "exe.gif";
                  break;
      case "zip": 
      case "tar":       
      case "rar":       
      case "ark":             
                  $icon = "zip.gif";
                  break;                  
      default:
                  $icon = "txt.gif";
                  break;
    }
    //MODIF 3 design path 
    //return RAPYD_IMAGES."tree/".$icon;
    return $rapyd->get_elements_path("tree/".$icon);
    
  }
  
  
  function language_file_exist($langfile,$idiom){

    $langfile = str_replace(EXT, '', str_replace('_lang.', '', $langfile)).'_lang'.EXT;
    if (file_exists(APPPATH.'language/'.$idiom.'/'.$langfile))
    {
      return true;
    }
    else
    {		
      if (file_exists(BASEPATH.'language/'.$idiom.'/'.$langfile))
      {
        return true;
      }
      else
      {
        return false;
      }
    }
  }
  
  function thumb_name($filename, $thumb_postfix="_thumb")
	{

	  $arrfilename = explode(".",$filename);
    $extension = array_pop($arrfilename);
    $thumbname = join(".", $arrfilename).$thumb_postfix.".".$extension;
    return $thumbname;
  }
  
  
  ## to be refactored ##
    
  function word_limiter_html($str, $n = 100, $end_char = '…')
  {
      if (strlen($str) < $n)
      {
          return closeTags($str);
      }
      
      $words = explode(' ', preg_replace("/\s+/", ' ', preg_replace("/(\r\n|\r|\n)/", " ", $str)));
      
      if (count($words) <= $n)
      {
          return closeTags($str);
      }

      $str = '';
      for ($i = 0; $i < $n; $i++)
      {
          $str .= $words[$i].' ';
      }
      
      $str = closeTags($str);
      return trim($str).$end_char;
  }

  function closeTags($string)
  {
    // coded by Constantin Gross <connum at googlemail dot com> / 3rd of June, 2006
    // (Tiny little change by Sarre a.k.a. Thijsvdv)
    $donotclose=array('br','img','input'); //Tags that are not to be closed
    
    //prepare vars and arrays
    $tagstoclose='';
    $tags=array();
    
    //put all opened tags into an array  /<(([A-Z]|[a-z]).*)(( )|(>))/isU
    preg_match_all("/<(([A-Z]|[a-z]).*)(( )|(>))/isU",$string,$result);
    $openedtags=$result[1];
    // Next line escaped by Sarre, otherwise the order will be wrong
    // $openedtags=array_reverse($openedtags);
    
    //put all closed tags into an array
    preg_match_all("/<\/(([A-Z]|[a-z]).*)(( )|(>))/isU",$string,$result2);
    $closedtags=$result2[1];
    
    //look up which tags still have to be closed and put them in an array
    for ($i=0;$i<count($openedtags);$i++) {
       if (in_array($openedtags[$i],$closedtags)) { unset($closedtags[array_search($openedtags[$i],$closedtags)]); }
           else array_push($tags, $openedtags[$i]);
    }
    
    $tags=array_reverse($tags); //now this reversion is done again for a better order of close-tags
    
    //prepare the close-tags for output
    for($x=0;$x<count($tags);$x++) {
      $add=strtolower(trim($tags[$x]));
      if(!in_array($add,$donotclose)) $tagstoclose.='</'.$add.'>';
    }

    //and finally
    return $string . $tagstoclose;
  }  
  

  
?>