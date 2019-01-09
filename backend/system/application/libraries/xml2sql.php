<?php                                                                  
class xml2sql{

	var $fnombre;

	function xml2sql($filename=NULL){
		if (!empty($filename)){
			$this->fnombre=$filename;
		}
	}
	
	function analizador(){ 
		
	    $file="./uploads/archivos/$this->fnombre";

			$xml_parser = xml_parser_create();
			
			if (!($fp = fopen($file, "r"))) {
			    die ("could not open XML input");
			}
			
			$data = fread($fp, filesize($file));
			fclose($fp);
	    
	    $parser = xml_parser_create();                                     
	    xml_parser_set_option($parser, XML_OPTION_CASE_FOLDING, 0);        
	    xml_parser_set_option($parser, XML_OPTION_SKIP_WHITE, 1);
	    xml_parse_into_struct($parser, $data, $values, $tags);             
	    //print_r($tags);
	    
	    xml_parser_free($parser);                                                                                                                                             
	    
	    foreach ($tags as $key=>$val) {                                    
	        if ($key == "element") {                                      
	            $molranges = $val;                                            
	            for ($i=0; $i < count($molranges); $i+=2) {                
	                $offset = $molranges[$i] + 1;                          
	                $len = $molranges[$i + 1] - $offset;                   
	                $tdb[] = $this->llena(array_slice($values, $offset, $len));
	            }                                                          
	        } else {                                                       
	            continue;
	        }
	    }
	  
	    return $tdb;                                                       
	}                                                                      
	                                                                       
	function llena($mvalues)                                            
	{                                                                      
	    for ($i=0; $i < count($mvalues); $i++) {                           
	        $mol[$mvalues[$i]["tag"]] = $mvalues[$i]["value"];             
	    }                                                                  
	    
	    $arr=array();
	    foreach ($mol as $k=> $v)
	    	 $arr[$k] = $mol[$k];    
			return $arr;
	}
}                                                       
?>