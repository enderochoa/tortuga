<?php  if (!defined('BASEPATH')) exit('No direct script access allowed');
/*
function open_flash_chart_object( $width, $height, $url, $use_swfobject=true )
{
    //
    // return the HTML as a string
    //
    return _ofc( $width, $height, $url, $use_swfobject, site_url('images').'/' );
}

function open_flash_chart_object_echo( $width, $height, $url, $use_swfobject=true, $base='' )
{
    //
    // stream the HTML into the page
    //
    echo _ofc( $width, $height, $url, $use_swfobject, $base );
}

function _ofc( $width, $height, $url, $use_swfobject, $base )
{
    //
    // I think we may use swfobject for all browsers,
    // not JUST for IE...
    //
    //$ie = strstr(getenv('HTTP_USER_AGENT'), 'MSIE');
    
    //
    // escape the & and stuff:
    //
    $url = urlencode($url);
    
    //
    // output buffer
    //
    $out = array();
    
    //
    // check for http or https:
    //
    if (isset ($_SERVER['HTTPS']))
    {
        if (strtoupper ($_SERVER['HTTPS']) == 'ON')
        {
            $protocol = 'https';
        }
        else
        {
            $protocol = 'http';
        }
    }
    else
    {
        $protocol = 'http';
    }
    
    //
    // if there are more than one charts on the
    // page, give each a different ID
    //
    global $open_flash_chart_seqno;
    $obj_id = 'chart';
    $div_name = 'flashcontent';
    
    //$out[] = '<script type="text/javascript" src="'. $base .'js/ofc.js"></script>';
    $out[] = script('ofc.js');
    
    if( !isset( $open_flash_chart_seqno ) )
    {
        $open_flash_chart_seqno = 1;
        //$out[] = '<script type="text/javascript" src="'. $base .'js/swfobject.js"></script>';
    }
    else
    {
        $open_flash_chart_seqno++;
        $obj_id .= '_'. $open_flash_chart_seqno;
        $div_name .= '_'. $open_flash_chart_seqno;
    }
    
    if( $use_swfobject )
    {
	// Using library for auto-enabling Flash object on IE, disabled-Javascript proof  
        $out[] = '<div id="'. $div_name .'" onmouseout="onrollout();"><center><b>Nota:</b> Es necesario actualizar el Flash Player <br> Para visualizar el gr&aacute;fico</center></div>';
	$out[] = '<script type="text/javascript">';
	$out[] = 'var so = new SWFObject("'. $base .'open-flash-chart.swf", "'. $obj_id .'", "'. $width . '", "' . $height . '", "9", "#FFFFFF");';
	$out[] = 'so.addVariable("width", "' . $width . '");';
	$out[] = 'so.addVariable("height", "' . $height . '");';
	$out[] = 'so.addVariable("data", "'. $url . '");';
	$out[] = 'so.addParam("allowScriptAccess", "sameDomain");';
	$out[] = 'so.write("'. $div_name .'");';
	$out[] = '</script>';
	$out[] = '<noscript>';
    }

    $out[] = '<object classid="clsid:d27cdb6e-ae6d-11cf-96b8-444553540000" codebase="' . $protocol . '://fpdownload.macromedia.com/pub/shockwave/cabs/flash/swflash.cab#version=8,0,0,0" ';
    $out[] = 'width="' . $width . '" height="' . $height . '" id="ie_'. $obj_id .'" align="middle">';
    $out[] = '<param name="allowScriptAccess" value="sameDomain" />';
    $out[] = '<param name="movie" value="'. $base .'open-flash-chart.swf?width='. $width .'&height='. $height . '&data='. $url .'" />';
    $out[] = '<param name="quality" value="high" />';
    $out[] = '<param name="bgcolor" value="#FFFFFF" />';
    $out[] = '<embed src="'. $base .'open-flash-chart.swf?data=' . $url .'" quality="high" bgcolor="#FFFFFF" width="'. $width .'" height="'. $height .'" name="'. $obj_id .'" align="middle" allowScriptAccess="sameDomain" ';
    $out[] = 'type="application/x-shockwave-flash" pluginspage="' . $protocol . '://www.macromedia.com/go/getflashplayer" id="'. $obj_id .'"/>';
    $out[] = '</object>';

    if ( $use_swfobject ) {
	$out[] = '</noscript>';
    }
    
    return implode("\n",$out);
}
*/
function open_flash_chart_object( $width, $height, $url, $use_swfobject=true ){
	$sale='';

	$url = urlencode($url);
	global $open_flash_chart_seqno;
	$obj_id = 'chart';
	$div_name = 'flashcontent';
	
	if( !isset( $open_flash_chart_seqno ) ){
		$open_flash_chart_seqno = 1;
		$sale .= script('swfobject.js');
	}else {
		$open_flash_chart_seqno++;
		$obj_id   .= '_'. $open_flash_chart_seqno;
		$div_name .= '_'. $open_flash_chart_seqno;
	}
	
	if( $use_swfobject ){
		// Using library for auto-enabling Flash object on IE, disabled-Javascript proof
		$sale .= '<div id="'.$div_name.'"><center><b>Nota:</b> Es necesario actualizar el Flash Player <br> Para visualizar el gr&aacute;fico</center></div>';
		$sale .= '<script type="text/javascript">';
		$sale .= 'var so = new SWFObject("'.base_url().'images/open-flash-chart.swf", "ofc", "'. $width . '", "' . $height . '", "9", "#FFFFFF");';
		$sale .= 'so.addVariable("width", "' . $width . '");';
		$sale .= 'so.addVariable("height", "' . $height . '");';
		$sale .= 'so.addVariable("data", "'. $url . '");';
		$sale .= 'so.addParam("allowScriptAccess", "sameDomain");';
		$sale .= 'so.write("'.$div_name.'");';
		$sale .= '</script>';
		$sale .= '<noscript>';
	}
	$sale .= '<object classid="clsid:d27cdb6e-ae6d-11cf-96b8-444553540000" codebase="http://fpdownload.macromedia.com/pub/shockwave/cabs/flash/swflash.cab#version=8,0,0,0" ';
	$sale .= 'width="' . $width . '" height="' . $height . '" id="ie_'. $obj_id .'" align="middle">';
	$sale .= '<param name="allowScriptAccess" value="sameDomain" />';
	$sale .= '<param name="movie" value="open-flash-chart.swf?width='. $width .'&height='. $height . '&data='. $url .'" />';
	$sale .= '<param name="quality" value="high" />';
	$sale .= '<param name="bgcolor" value="#FFFFFF" />';
	$sale .= '<embed src="'.base_url().'images/open-flash-chart.swf?data='.$url.'" quality="high" bgcolor="#FFFFFF" width="'.$width.'" height="'.$height.'" name="open-flash-chart" align="middle" allowScriptAccess="sameDomain" ';
	$sale .= 'type="application/x-shockwave-flash" pluginspage="http://www.macromedia.com/go/getflashplayer" id="'. $obj_id .'"/>';
	$sale .= '</object>';
	
	if ( $use_swfobject )
		$sale .= '</noscript>';
	
	return $sale;
}

?>