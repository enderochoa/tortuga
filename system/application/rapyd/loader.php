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
 * @version		0.7
 * @filesource
 */ 
 


/**
 * this function include/load/prepare a library into rapyd environment
 * i assume that a lib resource can be one or more of these:
 *
 * - a php script file inclusion (include_once)
 * - a javascript file inclusion (link)
 * - a javascript script "in page" 
 * - a css file inclusione (link)
 *
 * @package    rapyd.components
 * @author     Felice Ostuni <felix@rapyd.com>
 * @access     public
 * @version    0.6.1
 */
function rapydlib(){

  $modules = func_get_args();
	$ci =& get_instance();
  $rapyd =& $ci->rapyd;

  foreach($modules as $module){
  strtoupper($module);
    
    if (!defined("RAPYD_MOD_".strtoupper($module))){
    
      define("RAPYD_MOD_".strtoupper($module), true);
  
      switch($module){
      
        ### main js scripts & files
        case "main":
        $rapyd->js[] = RAPYD_LIBRARIES. "js/main.php";
        $rapyd->js[] = RAPYD_LIBRARIES. "js/main.js";
            
            
        ### tinyMCE (wysiwyg pure javascript textarea replacer | LGPL | version 2 | tinymce.moxiecode.com)
        case "tinymce":
          $rapyd->js[] = RAPYD_LIBRARIES. "tinymce/jscripts/tiny_mce/tiny_mce.js";
          $rapyd->script[] = '
          tinyMCE.init({ 
          /*language: "it",*/
          theme : "advanced", 
          mode : "textareas", 
          plugins : "advimage,table",
					editor_selector : "mceEditor",
					theme_advanced_toolbar_location : "top",
          theme_advanced_buttons1 : "bold,italic,underline,separator,justifyleft,justifycenter,justifyright,justifyfull,image,styleselect",
          theme_advanced_buttons2 : "bullist,numlist,separator,outdent,indent,separator,undo,redo,separator,link,unlink,anchor,cleanup,code",
          theme_advanced_buttons3 : "tablecontrols",
          theme_advanced_path_location : "bottom",
          theme_advanced_resize_horizontal : false,
          theme_advanced_resizing : true,
					relative_urls : false,
					file_browser_callback : "fileBrowserCallBack",
          content_css : "'.$GLOBALS["content_css"].'"
          });';

          $rapyd->script[] = '
          function fileBrowserCallBack(field_name, url, type, win) {
            var connector = "'.RAPYD_LIBRARIES.'tinymce/jscripts/tiny_mce/filemanager/browser.html?Connector=connectors/php/connector.php&ServerPath='.$GLOBALS["Editor_UserFilesPath"].'";
          	
            var enableAutoTypeSelection = true;
            
            var cType;
            tinyfck_field = field_name;
            tinyfck = win;
            
            switch (type) {
              case "image":
                cType = "Image";
                break;
              case "flash":
                cType = "Flash";
                break;
              case "file":
                cType = "File";
                break;
            }
            
            if (enableAutoTypeSelection && cType) {
              connector += "&Type=" + cType;
            }
            
            window.open(connector, "tinyfck", "modal,width=600,height=400");
          };';
			
					
          break;
          

        ### jscalendar (DHTML Calendar | LGPL | version 1.0 | www.dynarch.com/projects/calendar)
        case "jscalendar":   

					//MODIF 2 lang 
					//NOW rapyd can follow directly the CI Language that is set before than the previous version
          //$lang = $rapyd->language->language;
          $lang = $ci->config->item('language');
          
          
          $lang = (language_file_exist('calendar', $lang))?$lang:"english";
          $ci->lang->load('calendar', $lang);

          $today = RAPYD_TEMP_LANG_TODAY;
        
          $rapyd->js[]  = RAPYD_LIBRARIES. "jscalendar/calendar.js";
          $rapyd->js[]  = RAPYD_LIBRARIES. "jscalendar/calendar-setup.js";          
          $rapyd->css[] = RAPYD_LIBRARIES. "jscalendar/calendar.css";
          
          //da sostiutire con le costanti di lingua

          $rapyd->script[] = '
          Calendar._DN = new Array("'.$ci->lang->line('cal_sunday').'", "'.$ci->lang->line('cal_monday').'", "'.$ci->lang->line('cal_tuesday').'", "'.$ci->lang->line('cal_wednesday').'", "'.$ci->lang->line('cal_thursday').'", "'.$ci->lang->line('cal_friday').'", "'.$ci->lang->line('cal_saturday').'", "'.$ci->lang->line('cal_sunday').'");
          Calendar._SMN = new Array("'.$ci->lang->line('cal_jan').'", "'.$ci->lang->line('cal_feb').'", "'.$ci->lang->line('cal_mar').'", "'.$ci->lang->line('cal_apr').'", "'.$ci->lang->line('cal_may').'", "'.$ci->lang->line('cal_jun').'", "'.$ci->lang->line('cal_jul').'", "'.$ci->lang->line('cal_aug').'", "'.$ci->lang->line('cal_sep').'", "'.$ci->lang->line('cal_oct').'", "'.$ci->lang->line('cal_nov').'", "'.$ci->lang->line('cal_dec').'");
          Calendar._SDN = new Array("'.$ci->lang->line('cal_s').'", "'.$ci->lang->line('cal_mo').'", "'.$ci->lang->line('cal_tu').'", "'.$ci->lang->line('cal_we').'", "'.$ci->lang->line('cal_th').'", "'.$ci->lang->line('cal_fr').'", "'.$ci->lang->line('cal_sa').'", "'.$ci->lang->line('cal_s').'");
          Calendar._MN = new Array("'.$ci->lang->line('cal_january').'", "'.$ci->lang->line('cal_february').'", "'.$ci->lang->line('cal_march').'", "'.$ci->lang->line('cal_april').'", "'.$ci->lang->line('cal_mayl').'", "'.$ci->lang->line('cal_june').'", "'.$ci->lang->line('cal_july').'", "'.$ci->lang->line('cal_august').'", "'.$ci->lang->line('cal_september').'", "'.$ci->lang->line('cal_october').'", "'.$ci->lang->line('cal_november').'", "'.$ci->lang->line('cal_december').'");
          Calendar._TT = {};
          Calendar._TT["TODAY"] = "'.$today.'";

					';


          
          break;

        ### colorpicker (javascript color picker combobox (dhtml) | free/donationware | version 2 | www.mattkruse.com)
        case "colorpicker":   
          $rapyd->js[]  = RAPYD_LIBRARIES. "colorpicker/ColorPicker2.js";
          $rapyd->script[] = ' var cp2 = new ColorPicker(); // DIV style';
          break;

        ### prototype (javascript framework (ajax) | MIT | version 1.5.0 | prototype.conio.net)
        case "prototype":   
          $rapyd->js[]  = RAPYD_LIBRARIES. "prototype/prototype.js";
          break;
  
      }//switch
      
    }//defined
    
  }//foreach
  
}



?>