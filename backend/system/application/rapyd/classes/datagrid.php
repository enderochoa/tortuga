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
 * DataGrid base class.
 *
 * @package    rapyd.components
 * @author     Felice Ostuni
 * @access     public
 */
class DataGrid extends DataSet{

  var $cid         = "dg";
  var $systemError = "";
  var $rapyd;
  var $_title = null;
  
 /**
  * PHP4 constructor.
  *
  * @access   public
  * @param string $title
  * @param mixed $data
  */
  function DataGrid($title=null, $data=null){
    
    parent::DataSet($data);
    $this->title($title);
    
    $this->ci =& get_instance();
    $this->rapyd =& $this->ci->rapyd;
    $this->session =& $this->rapyd->session; 
    
    $this->fields = array();
    $this->columns = array();
    $this->output  = "";
  }

	
 /**
  * append a column object.
  *
  * @access   public
  * @param    string  $label         label
  * @param    mixed   $pattern       can be:
  *                                   - a field name of a dataset,
  *                                   - a string with placeholders for field names
  *                                   - a field object, or an extended one (ex. new textField("",fieldname) )
  * @param    string  $headerParams  "td" free attributes
  * @return   void
  */
  function column($label,$pattern,$headerParams=null){
    $column = new DataGridColumn($label,$pattern,$headerParams);
    $column->columnType = "normal";
    $column->url = null;
    $column->orderby_field = null;
    $this->columns[] = $column;
  }


 /**
  * Append a special column that can be used for link another page and pass the current querystring (with i.e. the current datagird-page position).
  *
  * It's fast way to join a dataedit page/section and build a simple list&edit page (see online example: www.rapyd.com/dg_de.php)
  *
  * @access   public
  * @param    string  $label         label
  * @param    mixed   $pattern       can be:
  *                                   - a field name of a dataset,
  *                                   - a string with placeholders for field names
  *                                   - a field object, or an extended one (ex: new textField("",fieldname) )
  * @param    string  $uri           is the "uri" to be pointed
  * @param    string  $headerParams  "td" free attributes (ex:  width="100")
  * @return   void
  */
  function column_detail($label,$pattern,$uri,$headerParams='style="width:60px;padding-right:5px;text-align:right;"'){

    $column = new DataGridColumn($label,$pattern,$headerParams);
    $column->columnType = "detail";

    //compatibility make a uri if a url is passed
    if (site_url("")!="/"){
      $uri = trim(str_replace(site_url(""),"",$uri), "/");
    }else {
      $uri = trim($uri, "/");
    }
    
    
    //append gfid clause 
    if (isset($this->rapyd->uri->gfid))
    {      
      $uri_array = $this->rapyd->uri->explode_uri($uri);
      $uri_array["gfid"] = array("gfid", $this->rapyd->uri->gfid);
      $uri = $this->rapyd->uri->reverse_implode_uri($uri_array,"osp");
    }
    
    
    $column->url = site_url($uri);
    $column->orderby_field = $pattern;
    $this->columns[] = $column;
  }




 /**
  * Append a special column that can be used for link another page and pass the current querystring (with i.e. the current datagird-page position).
  *
  * It's fast way to join a dataedit page/section and build a simple list&edit page (see online example: www.rapyd.com/dg_de.php)
  *
  * @access   public
  * @param    string  $label         label
  * @param    mixed   $pattern       field_name || field-pattern || field-object
  * @param    string  $headerParams  "td" free attributes (ex:  width="100")
  * @return   void
  */
  function column_orderby($label,$pattern,$orderbyfield=null,$headerParams=null){
    $column = new DataGridColumn($label,$pattern,$headerParams);
    $column->columnType = "orderby";

    if (!isset($orderbyfield)) $orderbyfield = $pattern;
    $column->orderby_field = $orderbyfield;
    $this->columns[] = $column;
  }

 /**
  * _buildGrid build all columns output
  *
  * @access   private
  * @return   void
  */
  function _buildGrid(){

		$mypointer = 0;
		$output = "";
		$data["title"] = "";
		$data["container_tr"] = "";
		$data["container_bl"] = "";
		$data["container_br"] = "";
		$data["pager"] = "";
		$data["columns"] = array();
		$data["rows"] = array();

		$this->rapyd->set_view_path();

		//title
		$data["title"] = $this->_title;

		//buttons
		if (count($this->_button_container["TR"])>0){
			$data["container_tr"] = join("&nbsp;", $this->_button_container["TR"]);
		}
		if (count($this->_button_container["TL"])>0){
			$data["container_tl"] = join("&nbsp;", $this->_button_container["TL"]);
		}
		if (count($this->_button_container["BL"])>0){
			$data["container_bl"] = join("&nbsp;", $this->_button_container["BL"]);
		}
		if (count($this->_button_container["BR"])>0){
			$data["container_br"] = join("&nbsp;", $this->_button_container["BR"]);
		}

		//table headers
		foreach ( $this->columns as $column ) {
			$col["label"] = $column->label;
			$col["attributes"] = $column->attributes;
			$col["type"] = $column->columnType;
			$col["orderby_asc_url"] = str_replace("<#field#>", $column->orderby_field, $this->_orderby_asc_url);
			$col["orderby_desc_url"] = str_replace("<#field#>", $column->orderby_field, $this->_orderby_desc_url);
			$data["headers"][] = $col;
		}

		//table rows
		$offset = $this->ci->uri->segment($this->uri_segment);
		$this->_row_id=(int)$offset;
    
		foreach ( $this->data as $tablerow){
			unset($row);
			$this->_row_id++;

			foreach ( $this->columns as $column ) {

				$column->_row_id = $this->_row_id;
				$column->resetPattern();
				
				$column->setDataRow($tablerow);

				$cell["link"]       = $column->link;
				$cell["field"]      = $column->getValue();
				$cell["attributes"] = $column->attributes;
				$cell["type"]       = $column->columnType;

				$row[] = $cell;


			}
			$data["rows"][] = $row;
		}

		//pager
		if ($this->paged){
			$data["pager"] = $this->navigator;
		}

		$data["total_rows"] = $this->recordCount;
    
		$output = $this->ci->load->view('datagrid', $data, true);

		$this->rapyd->reset_view_path();

		return $output;
  }
  

 /**
  * build , main build method
  *
  * @access   public
  * @return   string  datagrid output
  */
  function build(){

    parent::build();

    if ($this->systemError != ""){
      //gestire l'errore in CI
    }

    return $this->output = $this->_buildGrid();
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
 * DataGridColumn
 *
 * @package    rapyd.components
 * @author     Felice Ostuni
 * @access     public
 */
class DataGridColumn{

  var $label = "";
  var $pattern = "";
  var $attributes = array(); //td attributes
  var $columnType = "normal";
  var $url = "";
  var $patternType = null; //fieldName, $fieldObject, $pattern

  var $fieldList = array();
  var $field = null;
  var $fieldName = null;

 /**
  * PHP4 constructor.
  *
  * @access   public
  * @param    string   $label         column label
  * @param    mixed    $pattern       - a field name of a dataset
  *                                   - a string with placeholders for field names (ex: "<#fieldname1#> - <em><#fieldname2#></em>")
  *                                   - a field object, or an extended one (ex. new textField("",fieldname) )
  * @param    string  $attributes     "td" free attributes of datagrid-column (ex: 'style="background-color:#ff1111" width="100"' )
  * @return   void
  */
  function DataGridColumn($label,$pattern,$attributes=""){
    $this->link = "";
    $label = (lang($label)!="") ? lang($label) : $label;
    $this->label = $label;
    $this->pattern = is_object($pattern)?clone($pattern):$pattern;
    $this->rpattern = is_object($pattern)?clone($pattern):$pattern;

    $this->attributes = $attributes;
    $this->_checkType();
  }

 /**
  * detect the pattern type of current column
  *
  * @access   private
  * @return   void
  */
  function _checkType(){
    if ( is_object($this->pattern) AND strpos(strtolower(get_class($this->pattern)),"field")>0){
      $this->patternType = "fieldObject";
      $this->field = $this->pattern;
    } elseif (strpos($this->pattern,"#>")>0){
      $this->patternType = "pattern";
      $this->_parsePattern($this->pattern);
    } else {
      $this->patternType = "fieldName";
      $this->fieldName = $this->pattern;
    }
  }

 /**
  * from a given pattern it fill an array of required fields (fieldList)
  *
  * @access   private
  * @param    string   $pattern column pattern
  * @return   void
  */
  function _parsePattern($pattern){
    $template = $pattern;
    $parsedcount = 0;
    while (strpos($template,"#>")>0) {
      $parsedcount++;
      $parsedfield = substr($template,strpos($template,"<#")+2,strpos($template,"#>")-strpos($template,"<#")-2);

      $this->fieldList[]=$parsedfield;
      $template = str_replace("<#".$parsedfield ."#>","",$template);
    }
  }

 /**
  * prepare the column to acquire a new dataRow
  *
  * @access   public
  * @param    string   $pattern column pattern
  * @return   void
  */
  function resetPattern(){
    $this->rpattern = $this->pattern;
  }


 /**
  * by a passed data-value it set the value/s on the column.
  *
  * @access   public
  * @param    string   $dataRow  is a "dataset item" (see dataset class)
  * @return   void
  */
  function setDataRow($dataRow){

    $dataRow["dg_row_id"] = $this->_row_id;
     switch($this->patternType){
      case "fieldObject":

        if(isset($dataRow[$this->field->db_name])){
					if($dataRow[$this->field->db_name]!='__TOET__'){
						$this->field->value = $dataRow[$this->field->db_name];
						
						if(isset($this->field->grid_name)){
							$this->_parsePattern($this->field->grid_name);
							$nname = $this->field->grid_name;
							foreach ($this->fieldList as $fieldName){
								if (isset($dataRow[$fieldName]))
									$nname = str_replace("<#$fieldName#>",$dataRow[$fieldName],$nname);
							}
							$this->field->name = $nname;
							$this->fieldList = array();
						}
						
					}else{
						$this->field->value = "";
					}
        } else {
          $this->field->value = "";
        }
        break;
      case "pattern":
        foreach ($this->fieldList as $fieldName){
					//echo $this->rpattern.' --- '.$dataRow[$fieldName]."\n";
					if($dataRow[$fieldName]=='__TOET__'){
						$this->rpattern = '';
					}else{
						$this->rpattern = str_replace("<#$fieldName#>",$dataRow[$fieldName],$this->rpattern);
					}
        }
        break;
      case "fieldName":
           if (isset($dataRow["{$this->fieldName}"])){
						if($dataRow[$this->fieldName]!='__TOET__')
							$this->rpattern = $dataRow["{$this->fieldName}"];
						else
						$this->rpattern = '';
           } elseif (array_key_exists($this->fieldName, $dataRow)) {
             $this->rpattern = "";
           }
        break;
    }

    if ($this->url){
      $this->_parsePattern($this->url);
			$link = $this->url;
      foreach ($this->fieldList as $fieldName){
        if (isset($dataRow[$fieldName])){
          $link = str_replace("<#$fieldName#>",$dataRow[$fieldName],$link);
        }
      }
			$this->link = $link;
    }


  }


 /**
  * it return the column output.
  * if the column pattern is a field object it build() the field first.
  *
  * @access   public
  * @return   string column output
  */
  function getValue(){
    switch($this->patternType){
      case "fieldObject":

        $this->field->requestRefill = false;
        //$this->field->status = "show";
        $this->field->build();
        return $this->field->output;
        break;
      case "pattern":
        if ($this->rpattern == ""){
          $this->rpattern = "&nbsp;";
        }
				$this->rpattern  = replaceFunctions($this->rpattern);
        return $this->rpattern;
        break;
      case "fieldName":
        $this->rpattern = nl2br(htmlspecialchars($this->rpattern));
        if ($this->rpattern == ""){
          $this->rpattern = "&nbsp;";
        }
        return $this->rpattern;
        break;
    }
  }
}

?>
