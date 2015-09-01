<?php

/*** 
 * Written By: Santiago Zarate <santiago@zarate.net.ve> 
 */

/** What's going to be used to replace the \n's */
define('NEWLINE_REPLACEMENT', '-999999');

/** What's going to be used to replace the \t's */
define('TABULATION_REPLACEMENT', '');

/** Do we need the dimension? (May be when working with Ndimensional stuff.. 
 *  then we'll have to group data.. which may be sexier
 * 
 */

if(!defined('NO_DIMENSIONS')){
	
	define('NO_DIMENSIONS', false);
	
}

/**
 * @classDescription Class to parse an XMLA Tabular Query from a mondrian Provider.
 * @author Santiago Zarate <santiago@zarate.net.ve>
 * @category Olap
 * @package Olap
 * @version 0.1-endast
 */
class Xmlarequest {

	/**
	 * @var The XMLA Response from the curl call
	 */	
	private $mondrianResponse;
	
	/**
	 * @var The Request sent to the server
	 */
	private $SoapRequest;
	
	/**
	 * @var the kind of query we're performing, either tabular or multidimensional
	 */
	private $queryFormat;
	
	/**
	 * 
	 * @var Where the mondrian XMLA provider should connect to 
	 * 
	 */
	public $dataSource;
	
	/**
	 * @var Which catalog (Cube) will be Using
	 */
	
	public $Catalog;
	
	/**
	 * @var The Query that's being to be sent to the server
	 */
	public $mdxQuery;
	
	/**
	 * @var the full url to the XMLA provider
	 */
	public $xmlaProvider;


	/* Kind of getters: */
	
	/**
	 * 
	 * @return returns the format of the query, triggers an error if the 
	 * queryFormat property is not set. 
	 **/
	 
	public function Xmlarequest(){
		
	}
	
	public function getQueryFormat(){
		
		if (!empty($this->queryFormat) or !isset($this->queryFormat)){
			
			return $this->queryFormat;
			
		} else {
			
			trigger_error('La propiedad queryFormat no tiene un valor valido', E_USER_ERROR);
			
		}
		
	}
	
	/**
	 * 
	 * @param string $queryFormat Type of query to execute, for now only tabular and multidimensional are supported 
	 * @return void
	 */
	public function setQueryFormat($queryFormat){
		
		if(in_array(trim(strtolower($queryFormat)), array('tabular', 'multidimensional'))){
			
			$this->queryFormat = $queryFormat;
			
		} else {
			
			trigger_error('La propiedad queryFormat solo acepta "tabular" y "multidimensional" como valores');
			
		}
		
	}
		
	/**
	 * 
	 * @return Executes a MDX Query to a Mondrian Datasource, returns true or triggers an error.
	 */
		
	public function executeCurl(){
		
		$curlSession = curl_init();
		$curlOptions = array(
            CURLOPT_URL => $this->xmlaProvider,
            CURLOPT_RETURNTRANSFER => true,
        );
		/** curl_setopt_array no deja hacer esto :/*/
		
		curl_setopt($curlSession, CURLOPT_POST, true);
		curl_setopt($curlSession, CURLOPT_POSTFIELDS, 'postURL=xmla.jsp&SOAPRequest='.$this->soapRequest); // Tied to mondrian
		curl_setopt_array($curlSession, $curlOptions);

		if( $this->mondrianResponse = curl_exec($curlSession)) { // may need cleanup
			
			return true;
			
		} else {
			
			
				
				trigger_error(array('Respuesta CURL' => curl_error($curlSession), 'Respuesta MONDRIAN' => $this->mondrianResponse)
							, E_USER_ERROR);
				return false;
				
			}
			
				
		curl_close($curlSession);
		
	}
	
	
	/**
	 * 
	 * @return calls the executeCurl Method, if everything is working, returns the XMLthat the mondrian XMLA server gives. 
	 */
	
	public function getMondrianResponse(){
		/** In the future... this could be a soap request... or so :D */
		/** Need something to chech that there are no errors... meanwhile everything works D: */
		$this->executeCurl();
		return $this->mondrianResponse;	
		
	}
	
	/**
	 * 
	 * @param string $MDXQuery The MDX Query
	 * @return void it only sets the $soapRequest property
	 */
	
	public function mdxQuery($MDXQuery){
		
		$this->mdxQuery = $MDXQuery;
		
		$this->soapRequest =  
		'<Execute xmlns="urn:schemas-microsoft-com:xml-analysis">
		  <Command>
		    <Statement>
				'.$this->mdxQuery.'
		    </Statement>
		  </Command>
		  <Properties>
		    <PropertyList>
		      <Catalog>'.$this->Catalog.'</Catalog>
		      <DataSourceInfo>'.$this->dataSource.'</DataSourceInfo>
		      <Format>'.$this->queryFormat.'</Format>
		      <AxisFormat>TupleFormat</AxisFormat>
		    </PropertyList>
		  </Properties>
		</Execute>';
		
	}
	
	/**
	 * 
	 * @param object $mondrianXMLAResponse
	 * @return An Array Containing the whole Array of data under the <row></row> element of the XMLA
	 */
	
	public static function getXMLASetOfRows($mondrianXMLAResponse){
		$xmlaRows = '';	
		
		$xmlaResponse = str_replace(array("\n","\t"), array(NEWLINE_REPLACEMENT, TABULATION_REPLACEMENT), $mondrianXMLAResponse);
		preg_match_all('{<row[^>]*>(?<rows>.*?)</row>}', $xmlaResponse, $xmlaRows);
		$rows = array_map('XMLARequest::cleanXMLATag', $xmlaRows['rows']);
		$rows = array_map('XMLARequest::parseClearReturnRowSet', $rows); 
		return $rows; 
		
	}
	
	/**
	 * 
	 * @param string $tag
	 * @return string a string without braces, without spaces (They're _ instead)
	 *  
	 */

	public static function cleanXMLATag($tag){
	
		$search = array('_x005b_', '_x005d_', '_x0020_','.member_caption');
		$replace = array('','','_','');
		$tag = str_ireplace($search, $replace, trim($tag));
		if($tag != null AND $tag != ''){
		
			return $tag;	
			
		} 
		
	}
	
	/**
	 * 
	 * @param string $tag to be stripped down
	 * @return an array, of what's left after the regexp matching with the [Dimension] => [Member] format 
	 */
	
	public static function getArrayFromXMLA($tag){
		if($tag != ''){
			
			preg_match('{^<(?<tag>[^>]+?)>(?<fila>.*?)</.*>$}', $tag, $datos);
			/** Clean the attributes */
			
			if(strstr($datos['tag'], ' ')){
			
				list($datos['tag'], $a) = explode(' ', $datos['tag']);	
				
			}
			/** If we dont want dimensions */
			if(NO_DIMENSIONS){ 
					list($a,$datos['tag']) = explode('.', $datos['tag']);
				}
				
			return array(strtolower($datos['tag']) => $datos['fila']);
		}
	}
		
	/**
	 * 
	 * @param Array $Rowset An array containing a set of nodes (XMLA) then cleans it.
	 * @return Array containing each item of the <row></row> tree. 
	 */
	
	public static function parseClearReturnRowSet($Rowset){
		
		$rows = explode(NEWLINE_REPLACEMENT, $Rowset);
		$rows = array_map('XMLARequest::getArrayFromXMLA',array_map('trim', $rows));
		
		/** 
		 * Usually the information is consistent... but one never knows...
		 * cleanup someday? perhaps, next version
		 *
		 * 
		 */
		
		array_shift($rows);
		array_pop($rows); 
		 
		return $rows;
	
	}
	
	
}

?>