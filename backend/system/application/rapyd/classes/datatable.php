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
 * ancestor
 */
require_once("dataset.php");


/**
 * DataTable base class.
 *
 * @package    rapyd.components
 * @author     Felice Ostuni
 * @access     public
 */
class DataTable extends DataSet{

  var $hasHeaderFooter = true;
 
  var $cid         = "dt";
  var $systemError = "";
  var $rapyd;
  
  var $per_row = 2;
  var $cell_template = "";
  var $cell_attributes = 'style="vertical-align:top;"';


 /**
  * PHP4 constructor.
  *
  * @access   public
  * @param    object  $data   a multidimensional associative array of data
  * @return   void
  */
  function DataTable($title=null, $data=null){
    parent::DataSet($data);
    
		$this->ci =& get_instance();

    $this->fields = array();
    $this->output = "";
    
		$this->title($title);
		
    static $identifier = 0;
    $identifier++;
    $this->cid = $this->cid . (string)$identifier;  
    
  }

    

 /**
  * build , main build method
  *
  * @access   public
  * @return   string  datatable output
  */
  function build(){
  
    parent::build();
  
    if ($this->systemError != ""){
      // ci logerror
    }

    $mypointer = 0;
    $output = "";
		$data["title"] = "";
		$data["container_tr"] = "";
		$data["container_bl"] = "";
		$data["container_br"] = "";
		$data["pager"] = "";
		$data["trs"] = array();
		$trs = array();
		
		$this->rapyd->set_view_path();
		
		//title
		$data["title"] = $this->_title;

		//buttons
		if (count($this->_button_container["TR"])>0){
			$data["container_tr"] = join("&nbsp;", $this->_button_container["TR"]);
		}
		if (count($this->_button_container["BL"])>0){
			$data["container_bl"] = join("&nbsp;", $this->_button_container["BL"]);
		}
		if (count($this->_button_container["BR"])>0){
			$data["container_br"] = join("&nbsp;", $this->_button_container["BR"]);
		}

		$dataset = $this->data;
		$numRows = ceil(count($dataset) / $this->per_row);
		$itrations = $this->per_row;

		//table rows 
		for($i=0; $i< $numRows; $i++){

			unset($tds);

			//table-cells
			for($j=1; $j<= $itrations; $j++){
				if (isset($dataset[0])){
					if (!is_array($dataset[0])) {
						$this->cell_template = "&nbsp;";
					}
				} else {
						$this->cell_template = "&nbsp;";        
				}

				$cell = new DataTableCell($this->cell_template);

				if (isset($dataset[0])){
					$cell->setValue($dataset[0]);
				} else {
					$cell->setValue("");
				}

				$td["attributes"] = $this->cell_attributes;
				$td["content"] = $cell->getValue();
				$tds[] = $td;
				array_shift($dataset);
			}
			$trs[] = $tds;
		}
    $data["trs"] = $trs;
		
		//pager 
		if ($this->paged){
		 $data["pager"] = $this->navigator;
		}
		
		$this->output = $this->ci->load->view('datatable', $data, true);

		$this->rapyd->reset_view_path();

		return  $this->output;
  }
  
 /**
  * simply replace a string in the builded component output
  *
  * @access   public
  * @param    string  $oldstring
  * @param    string  $newstring
  * @return   void
  */
  function replace($oldstring, $newstring){
    $this->output = str_replace($oldstring, $newstring, $this->output);    
  }   

 /**
  * draw , build & print the component
  *
  * @access   public
  * @return   void
  */
  function draw(){
    $this->build();
    echo $this->output;
  } 
  


}

/**
 * DataTableCell
 *
 * @package    rapyd.components
 * @author     Felice Ostuni
 * @access     public
 */
class DataTableCell{

  var $pattern = "";
  var $attributes = array(); //td attributes
  var $fieldList = array();

 /**
  * PHP4 constructor.
  *
  * @access   public
  * @param    string  $pattern  id a string with placeholders for field names
  * @return   void
  */
  function DataTableCell($pattern){
    $this->pattern = $pattern;
    $this->parsePattern();
  }
  
 /**
  * from a given pattern it fill an array of required fields (fieldList)
  *
  * @access   private
  * @param    string   $pattern column pattern
  * @return   void
  */  
  function parsePattern(){
    $template = $this->pattern;
    $arr = array();
    $parsedcount = 0;
    while (strpos($template,"#>")>0) {
      $parsedcount++;
      $parsedfield = substr($template,strpos($template,"<#")+2,strpos($template,"#>")-strpos($template,"<#")-2); 
      if (strpos($parsedfield,"[")!==false){
        $name = substr($parsedfield,0,strpos($parsedfield,"["));
        $item = substr($parsedfield,strpos($parsedfield,"[\"")+2,strpos($parsedfield,"\"]")-strpos($parsedfield,"[\"")-2);
        $arr[$name][$item] = "";

      } else {
        $this->fieldList[]=$parsedfield;
      }
      $template = str_replace("<#".$parsedfield ."#>","",$template);    
    }

    $this->fieldList[]=$arr;
  }
  
 /**
  * by a passed data-value it set the value/s on the cell.
  *
  * @access   public
  * @param    string   $dataRow  is a "dataset item" (see dataset class)
  * @return   void
  */
  function setValue($dataRow){
    foreach ($this->fieldList as $fieldName){
     
      if (is_array($fieldName)){
        $keys = array_keys($fieldName);
       
        foreach ($keys as $key){

          $mainKey = $key;
          $subArr = $fieldName[$mainKey];
         
          foreach ($subArr as $field=>$value){
            $subarrValues = $dataRow[$mainKey][$field];
            $this->pattern = str_replace("<#$mainKey"."[\"$field\"]#>",$subarrValues,$this->pattern);
          }
        }
       
       
      } else {
       
       $replace = (isset($dataRow[$fieldName]))?$dataRow[$fieldName]:"";
       $this->pattern = str_replace("<#$fieldName#>",$replace,$this->pattern);
       
      }
     

    }

  }
  
 /**
  * it return the cell output.
  *
  * @access   public
  * @return   string cell output
  */
  function getValue(){
    $this->pattern  = replaceFunctions($this->pattern);
    return $this->pattern;
  }
}

?>
