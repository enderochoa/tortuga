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
 * DataDetalle class.
 *
 * @package    rapyd.components
 * @author     Andres Hocevar
 * @access     public
 */
class DataDetalle{

  var $cid         = "dg";
  var $systemError = "";
  var $rapyd;
  var $_title = null;
  var $data;
  var $id = 0;
  var $_script='';
  var $fila=array();
  var $ondelete='';
  var $onadd='';
  var $_fields = array();
  var $_status;
  var $_borra_fila;
	var $mSQL;
	var $objimp=array();
	var $objimpname=array();
	var $db;
  var $html=null;
  
 /**
  * PHP4 constructor.
  *0
  * @access   public
  */
  function DataDetalle($status){
    
    $this->html = new Html();
    static $id=0;
    $this->id=$id;
    $id++;
    
		$this->_status=$status;
    //$this->title($title);
    
    $this->ci    =& get_instance();
    $this->ci->load->library('Msql');
    
    $this->db=new msql();
    
    $this->rapyd =& $this->ci->rapyd;
    
    $this->fields  = array();
    $this->columns = array();
    $this->output  = "";
    $this->_borra_fila='<a href=# onclick=\'borrar_fila_'.$this->id.'("tr_detalle_'.$this->id.'_<#i#>");\'>'.RAPYD_BUTTON_DELETE.'</a>';
  }
  
  function title($title){
    $title = (lang($title)!="") ? lang($title) : $title;
    $this->_title = $title;
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
		$cajaobj=$this->_parsePattern($pattern);
		$fila=$pattern;
		foreach($cajaobj AS $obj){
			if(is_object($this->$obj)){
				if(is_subclass_of($this->$obj, 'objField')){
					if ($this->_status=='idle')
						$this->$obj->status = 'create';
					else
						$this->$obj->status = $this->_status;
					$this->$obj->requestRefill=false;
					if($this->_status=='modify' or $this->_status=='show' or $this->_status=='idle')
						$this->$obj->value='!--'.$this->$obj->db_name.'--!';
					$this->$obj->build();
					$fila=str_replace('<#'.$obj.'#>',$this->$obj->output,$fila);
					$fila=str_replace('!--','<#',$fila);
					$fila=str_replace('--!','#>',$fila);
					$this->objimp[]=$this->$obj->db_name;
					$this->objimpname[]=str_replace('<#i#>','',$this->$obj->name);
				}
			}else{
				//Se toma como un string
				$fila=str_replace('<#'.$obj.'#>',$this->$obj,$fila);
			}
		}
		$pattern=$fila;
		$column = new DDataGridColumn($label,$pattern,$headerParams);
		$column->columnType = "normal";
		$column->url = null;
		$column->orderby_field = null;
		$this->columns[] = $column;
		$this->fila[strtolower($label)]=$pattern;
	}

 	function onDelete($string){
		$this->ondelete=$this->ondelete.$string.';';
	}
	function onAdd($string){
		$this->onadd=$this->onadd.$string.';';
	}

 /**
  * _buildGrid build all columns output
  *
  * @access   private
  * @return   void
  */
 	function _buildGrid(){
 		$id=$this->id;
		if($this->_status=='modify' or $this->_status=='create'  or $this->_status=='idle')
			$this->column('Fila','<#_borra_fila#>');
		
		$mypointer = 0;
		$output = "";
		$data["title"] = "";
		$data["container_tr"] = "";
		$data["container_bl"] = "";
		$data["container_br"] = "";
		$data["pager"]   = "";
		$data["columns"] = array();
		$data["rows"]    = array();
		$data["id"]      = $id;

		$this->rapyd->set_view_path();

		//title
		$data["title"] = $this->_title;

		//table headers
		foreach ( $this->columns as $column ) {
			$col["label"]      = $column->label;
			$col["attributes"] = $column->attributes;
			$col["type"]       = $column->columnType;
			$data["headers"][] = $col;

		}
		if($this->_status=='modify' or $this->_status=='show'){
			$query=$this->ci->db->query($this->db->compile_select());
			if ($query->num_rows() > 0){
				foreach($query->result_array() as $row)
					$this->data[]=$row;
				foreach($row as $clave=>$valor)
					$nrow[$clave]='';
				$this->data[]=$nrow;
			}else{
				//cuando no tiene detalle
				foreach($this->objimpname as $indice)
					$pivote[$indice]='';
				$this->data[]=$pivote;
			}
			
		}elseif($this->_status=='idle'){
			//print_r($this->objimp);
			$cant=$this->ci->input->post('cant_'.$this->id);
			$i=$o=$real=0;
			while(1){
				$pivote=array();
				if (isset($_POST[$this->objimp[0].$i])){
					if($this->ci->input->post($this->objimp[0].$i)){ 
						$real++;
						foreach($this->objimpname as $indice)
							$pivote[$indice]=$this->ci->input->post($indice.$i);
						$this->data[]=$pivote;
					}
					$o++;
				}
				if($o>=$cant) break;
				$i++;
			}
			$pivote=array();
			foreach($this->objimp as $indice)
				$pivote[$indice]='';
			$this->data[]=$pivote;
			if (count($this->data)==1) $this->data[1]=$this->data[0];
			
		}else{
			$this->data[0]=array();
			$this->data[1]=array();
		}
		
		foreach ( $this->data as $tablerow){
		  unset($row);
      $this->_row_id++;
			foreach ( $this->columns as $column ) {

				$column->_row_id = $this->_row_id;
				$column->resetPattern();
				$column->setDataRow($tablerow);

				$cell["link"]  = $column->link;
				$cell["field"] = $column->getValue();
				$cell["attributes"] = $column->attributes;
				$cell["type"] = $column->columnType;

				$row[] = $cell;

			}
			$data["rows"][] = $row;
		}
		if($this->_status=='modify' or $this->_status=='create' or $this->_status=='idle')
			$data["pager"] = "<a href=# onclick='Agregar_fila_$id();'>".RAPYD_BUTTON_ADD.'</a>';
		else
			$data["pager"] = '';

		$data["total_rows"]=1;

    $conten['row']=array_pop($data["rows"]);
		$conten["id"]=$this->id;
		$fila = trim($this->ci->load->view('datadetallefila', $conten, true));
		$fila = preg_replace('/\n/i', '', $fila);
		
		
    $script="
    var i_$id=".(count($data["rows"])-1).";
    var borrado= new Array();
		function Agregar_fila_$id(){
			$(\"cant_$id\").value=parseInt($(\"cant_$id\").value)+1;
			o=i_$id;i_$id++;
			var row=\"".str_replace('"', '\"', $fila)."\";
			row=row.gsub('<#i#>', i_$id.toString());
			new Insertion.Before('tr_detalle_pie_$id',row);
			$this->onadd
		}
		function borrar_fila_$id(id){
			$(\"cant_$id\").value=parseInt($(\"cant_$id\").value)-1;
			borrado.push(id);
			$(id).remove();
			$this->ondelete
		}";
    
    if (!empty($this->_script))
    	$script .= $this->_script;
    
    $data["form_scripts"] = $this->html->javascriptTag($script);
    
    if($this->ci->input->post('cant_'.$this->id))
    	$cant=$real;
    else if($this->_status=='modify' or $this->_status=='show')
    	$cant=$query->num_rows();
    else
    	$cant=1;
    	
    
		$hattributes = array(
			'name' => "cant_$id",
			'id'   => "cant_$id",
			'type' => "hidden",
			'value'=> $cant);
    $data["cant"]=form_input($hattributes);
		$output = $this->ci->load->view('datadetalle', $data, true);
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

    //parent::build();

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

  function script($script){
     $this->_script .= $script;
  }

 /**
  * detect $form->field from properties, and populate an array
  *
  * @access   private
  * @return   void
  */ 
  function _sniff_fields(){
    $this->_fields = array();
    $object = (get_object_vars($this));
    foreach ($object as $property_name=>$property){
     	
      if (is_object($property)){
        if (is_subclass_of($property, 'objField')){
          if ($property->type == "upload") {
            $this->_multipart = true;
          }

          if (isset($this->_dataobject)){

            $fields = $this->_dataobject->field_names;

            if (in_array($this->$property_name->db_name,$fields)||!$this->$property_name->db_name ){

              $this->$property_name->data =& $this->_dataobject;
              
            }
            
            if (isset($this->$property_name->rel_id))
            {
              $this->$property_name->data =& $this->_dataobject;
            }
            
          }
          $this->$property_name->status = $this->_status;
          
          if (isset($this->default_group) && !isset($this->$property_name->group)){
            $this->$property_name->group = $this->default_group;
          }
          
          if (isset($this->$property_name->rule)){
            if ((strpos($this->$property_name->rule,"required")!==false) && !isset($this->$property_name->no_star) ){
              $this->$property_name->_required = "*";
            }
          }
          $this->$property_name->build();
          $this->_fields[$property_name] =& $this->$property_name;
        }
      }
    }
  }
	function _parsePattern($pattern){
    $template = $pattern;
    $parsedcount = 0;
    $salida=array();
    while (strpos($template,"#>")>0) {
      $parsedcount++;
      $parsedfield = substr($template,strpos($template,"<#")+2,strpos($template,"#>")-strpos($template,"<#")-2);

      $salida[]=$parsedfield;
      $template = str_replace("<#".$parsedfield ."#>","",$template);
    }
    return $salida;
  }
}


/**
 * DDataGridColumn
 *
 * @package    rapyd.components
 * @author     Felice Ostuni
 * @access     public
 */
class DDataGridColumn{

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
  function DDataGridColumn($label,$pattern,$attributes=""){
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
    if (is_object($this->pattern) AND strpos(strtolower(get_class($this->pattern)),"field")>0){
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

        if(isset($dataRow[$this->field->name])){

          $this->field->value = $dataRow[$this->field->name];
        } else {
          $this->field->value = "";
        }
        break;
      case "pattern":
        foreach ($this->fieldList as $fieldName){
					if(array_key_exists($fieldName,$dataRow))
          	$this->rpattern = str_replace("<#$fieldName#>",$dataRow[$fieldName],$this->rpattern);
        }
        break;
      case "fieldName":
           if (isset($dataRow["{$this->fieldName}"])){
             $this->rpattern = $dataRow["{$this->fieldName}"];
           } elseif (array_key_exists($this->fieldName, $dataRow)) {
             $this->rpattern = "";
           }
        break;
    }
/*
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
*/

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
        $this->field->status = "show";
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
