<?php if (!defined('BASEPATH')) exit('No direct script access allowed');
date_default_timezone_set('America/Caracas');
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
   * transform a local ICU code to a php dateformat (for short date)
   *    
   * @param string local ICU code
   * @return string date format
   * @todo extend to other data types, but a complete ICU helper must be implemented
   */
  function locale_to_format($locale, $type="short"){
  
    $fmts["short"] = array(
      'eu' => 'd/m/Y', //back compatibility (non standard ICU, and so deprecated)
      'us' => 'n/j/y', //back compatibility (non standard ICU, and so deprecated)

      'en_US' => 'n/j/y',
      'en_GB' => 'd/m/Y',
      'it_IT' => 'd/m/y',
      'it_CH' => 'd.m.y',
      'fr_FR' => 'd/m/y',
      'fr_CH' => 'd.m.y',
      'fr_BE' => 'j/m/y',
      'de_DE' => 'd.m.y',
      'sk_SK' => 'j.n.Y',
      'pl_PL' => 'y-m-d',
      'hu_HU' => 'Y.m.d.',
      'sv_SE' => 'Y-m-d'
    );
    
    return (array_key_exists($locale,$fmts[$type]))?$fmts[$type][$locale]:$locale;
  }


  /**
   * returns a timestamp from human date
   * idea from P4A framework (GPL)
   *    
   * @param string human date
   * @param string PHP format string according to date() function
   * @return int
   * @todo implement other codes
   */
  function timestampFromInputDate($date, $format=RAPYD_DATE_FORMAT) {
    
    $format = locale_to_format($format);
    
    $regexp = "";
    $map = array();
    $nucleus_counter = 1;

    $iso = array();
    $iso['year']   = 0;
    $iso['month']  = 0;
    $iso['day']    = 0;
    $iso['hour']   = 0;
    $iso['minute'] = 0;
    $iso['second'] = 0;

    for($strpos = 0; $strpos < strlen($format); $strpos++) {
      $char = substr($format,$strpos,1);

      switch ($char) {
        // codes
        case "d":   // Day of the month, 2 digits with leading zeros; 01 to 31
          $regexp .= '([0-9]{1,2})';
          $map['day'] = $nucleus_counter++;
          break;
        case "j":   // Day of the month without leading zeros; 1 to 31
          $regexp .= '([0-9]{1,2})';
          $map['day'] = $nucleus_counter++;
          break;
        case "m":   // Numeric representation of a month, with leading zeros; 01 to 12
          $regexp .= '([0-9]{1,2})';
          $map['month'] = $nucleus_counter++;
          break;
        case "n":   // Numeric representation of a month, without leading zeros; 1 to 12
          $regexp .= '([0-9]{1,2})';
          $map['month'] = $nucleus_counter++;
          break;
        case "Y":   // A full numeric representation of a year, 4 digits
          $regexp .= '([0-9]{1,4})';
          $map['year'] = $nucleus_counter++;
          break;
        case "y":   // A two digit representation of a year
          $regexp .= '([0-9]{1,4})';
          $map['year'] = $nucleus_counter++;
          break;
        case "H":
          $regexp .= '([0-9]{1,2})';
          $map['hour'] = $nucleus_counter++;
          break;
        case "i":
          $regexp .= '([0-9]{1,2})';
          $map['minute'] = $nucleus_counter++;
          break;
        case "s":
          $regexp .= '([0-9]{1,2})';
          $map['second'] = $nucleus_counter++;
          break;
        // possible separators
        case "/":
        case "-":
          $regexp .= '[\/-]';
          break;
        case ".":
        case ":":
          $regexp .= '[\.:]';
          break;
        case " ":
          $regexp .= '\s*';
          break;
        // other unimplemeted
        default:
          //$regexp .= $char;
          return false;
          break;
      }
    }
    $regexp = trim($regexp);

    if (preg_match("/$regexp/", $date, $res)) {
      foreach ($map as $key=>$nucleus) {
        $iso[$key] += $res[$nucleus];
      }
      if ($iso['month'] == 0) $iso['month'] = 1;
      if ($iso['day'] == 0) $iso['day'] = 1;
      $iso['month'] = str_pad($iso['month'], 2, 0, STR_PAD_LEFT);
      $iso['day'] = str_pad($iso['day'], 2, 0, STR_PAD_LEFT);
      
      return mktime($iso['hour'],$iso['minute'],$iso['second'],$iso['month'],$iso['day'],$iso['year']);
    }
    else
      return false;
    
  }
  
  /**
   * returns a human date from timestamp
   *    
   * @param int
   * @param string PHP format string according to date() function
   * @return string human date
   */
  function inputDateFromTimestamp($timestamp, $format=RAPYD_DATE_FORMAT){
   
   $format = locale_to_format($format);
    
    if (!$timestamp){
      return "";
    } else {
      return date($format,$timestamp);
    }

  }
  
  //normally the db-date is an ISO-DATE: YYYY-MM-DD [hh:mm:ss]
  
  function timestampFromDBDate($date) { 
    if ((strpos($date,"0000-00-00")!==false) || ($date=="")){
      return false;
    } else {
      return strtotime($date);
    }
  }  

  function dbDateFromTimestamp($timestamp){
    if ($timestamp<1)
    {
      return "";
    } else {
      return date("Y-m-d H:i:s",$timestamp);
    }
  }
  
  
  #######  final functions
  
  //get a human date "from" the DB field (assume that is ISO: YYYY-MM-DD [hh:mm:ss])
  function dbdate_to_human($date,$format=RAPYD_DATE_FORMAT) {
    return inputDateFromTimestamp(timestampFromDBDate($date),$format);
  }

  //prepare a human date "to" the DB field (assume that is ISO: YYYY-MM-DD [hh:mm:ss])
  function human_to_dbdate($date,$format=RAPYD_DATE_FORMAT) {
    //adodb have DBDate() 
    return dbDateFromTimestamp(timestampFromInputDate($date,$format));
  }
  
  /**
   * convert PHP date format string to JScalendar date format string.
   * utility function to be used in jscalendar.
   *
   * @param string
   * @return string
   * @todo codes must be implemented in timestampFromInputDate()
   */
  function datestamp_from_format($format=RAPYD_DATE_FORMAT)
  {
    $format = locale_to_format($format);
    
    //$phpCodes = array('d', 'm', 'M', 'Y', 'y', 'D', 'l', 'F', 'j', 'w');
    //$jsCodes  = array('%d', '%m', '%b', '%Y', '%y', '%a', '%A', '%B', '%e', '%w');

    // reduced set without text representation of day and month; only numbers
    $phpCodes = array('d', 'm', 'n', 'Y', 'y', 'j', 'H', 'i', 's');
    $jsCodes  = array('%d', '%m', '%m', '%Y', '%y', '%e', '%H', '%M', '%S');

    $jsDateFormat = str_replace($phpCodes, $jsCodes, $format);

    return $jsDateFormat;
  }
  
  
  
  if(!function_exists('date_add')){
      function date_add($isodate,$days,$months=0,$years=0) { 

         $tms = timestampFromDBDate($date);
         $datearr = getdate($tms);
         $day = $datearr["mday"]+$days;
         $month = $datearr["mon"]+$months;
         $year = $datearr["year"]+$years;
         
         $newtms = mktime(0,0,0, $month,$day,$year);
     
         return dbDateFromTimestamp($newtms);
      }
  }
  
  
?>
