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
if (!defined('RAPYD_FIELD_SYMBOL_NULL')){
//MODIF 3 design path
$ci =& get_instance();
define('RAPYD_FIELD_SYMBOL_NULL',           '<em>[NULO]</em>');
define('RAPYD_FIELD_SYMBOL_TRUE',           '<img src="'.$ci->rapyd->get_elements_path('true.gif').'" />');
define('RAPYD_FIELD_SYMBOL_FALSE',          '<img src="'.$ci->rapyd->get_elements_path('false.gif').'" />');
define('RAPYD_FIELD_SYMBOL_REQUIRED',       '*');
}

/**
 * objField, normally you must to operate only with his descendant.
 *
 * @package    rapyd.components
 * @author     Felice Ostuni
 * @access     public
 */
class objField {

	//main properties
	var $type = 'field';

	var $label;
	var $name;
	var $id;
	var $data; //rapid dataobject
	var $db; //ci AR driver

	var $options = array(); //associative&multidim. array ($value => $description)
	var $operator = '';  //default operator in datafilter
	var $clause = 'like';
	var $like_side='both';


	//field actions & field status
	var $status = 'show';  //can be also: create/modify
	var $action = 'idle';  //can be also: insert/update
	var $when = null;
	var $mode = null;
	var $apply_rules = true;
	var $_required = '';

	//data settings
	var $newValue;
	var $insertValue = null;
	var $updateValue = null;
	var $requestRefill = true;
	var $is_refill  = false;
	var $save_error = null;
	var $parsed_fields = null;
	var $rel_id  = null;

	//other attributes
	var $maxlength;
	var $size;
	var $onclick;
	var $onchange;
	var $style;
	var $extra_output;
	var $css_class;
	var $title=null;
	var $tabindex=null;
	var $valid_error=''; //Para los errores de validacion

	//unused
	var $externalTable;
	var $externalJoinField;
	var $externalReplaceField;

	// layout
	var $layout = array('fieldSeparator'  => '<br />',
						'optionSeparator' => '');
	var $winWidth  = '500';
	var $winHeight = '400';
	var $winParams = 'toolbar=no,location=no,status=no,menubar=no,scrollbars=yes,resizable=yes';

	// configurations
	var $config = array('optionSerializeSeparator' => '|');
	var $output = '';
	var $pointer=false; //indica si es un apuntador

	var $ind=-1; //indice al cual pertenece en caso de una relacion 1:n
	var $showformat=null; //Puede ser numeric;4

	/**
	* PHP4 constructor.
	*
	* @access   public
	* @param    string   $label   the field label
	* @param    string   $name    the field name/identifier
	* @return   void
	*/
	function objField($label, $name){

		if(empty($this->id))$this->id = $name;
		$this->ci =& get_instance();
		$this->rapyd =& $this->ci->rapyd;

		//load needed libraries
		if (!isset($this->ci->validation)) {
			$this->ci->load->library('validation');
		}
		if (!isset($this->ci->upload)) {
			$this->ci->load->library('upload');
		}
		if (!isset($this->ci->image_lib)) {
			$this->ci->load->library('image_lib');
		}

		//load needed helpers
		if (!isset($this->ci->load->helpers['form_helper'])) {
			$this->ci->load->helper('form_helper');
		}
		if (!isset($this->ci->load->helpers['url'])) {
			$this->ci->load->helper('url');
		}
		if (!isset($this->ci->load->helpers['date'])) {
			$this->ci->load->helper('date');
		}
		if (!isset($this->ci->load->helpers['text'])) {
			$this->ci->load->helper('text');
		}


		$this->validation =& $this->ci->validation;
		$this->upload =& $this->ci->upload;
		$this->input =& $this->ci->input;
		$this->uri =& $this->ci->uri;
		$this->image_lib =& $this->ci->image_lib;

		static $id = 0;
		$this->identifier = 'field'.$id++;
		$this->request = $_POST;

		$this->name = str_replace('.','_',$name);
		$this->db_name = $name;

		$label = (lang($label)!='') ? lang($label) : $label;
		$this->label = $label;
		$this->value = null;

	}

 /**
	* duplicate function from DataGrid:
	* from a given pattern it fill an array of required fields (fieldList)
	*
	* @access   private
	* @param    string   $pattern column pattern
	* @return   void
	*/
	function _parsePattern($pattern){
		$template = $pattern;
		$parsedcount = 0;
		while (strpos($template,'#>')>0) {
			$parsedcount++;
			$parsedfield = substr($template,strpos($template,'<#')+2,strpos($template,'#>')-strpos($template,'<#')-2);

			$this->parsed_fields[]=$parsedfield;
			$template = str_replace('<#'.$parsedfield .'#>','',$template);
		}
	}


 /**
	* it get the current value of field
	*
	* - if a default value is setted (insertValue, updateValue..)
	* - or if a data source (dataobject) exist.
	*
	* @access   private
	* @return   void
	*/
	function _getValue(){
		if (($this->requestRefill == true) && isset($this->request[$this->name]))
		{
			$requestValue = $this->input->post($this->name);
			//if (get_magic_quotes_gpc()) $requestValue = stripslashes($requestValue);
			$this->value = $requestValue;
			$this->is_refill = true;
		} elseif (($this->status == 'create') && ($this->insertValue != null))
		{
			$this->value = $this->insertValue;
		} elseif (($this->status == 'modify') && ($this->updateValue != null))
		{
			$this->value = $this->updateValue;
		} elseif ( (isset($this->data)) && ($this->data->loaded) && (!isset($this->request[$this->name])) && (isset($this->db_name)) )
		{

			if($this->pointer){
				if (isset($this->rel_id)){
					$this->value = $this->data->get_rel_pointer($this->rel_id, $this->db_name,$this->ind);
				} else {
					$this->value = $this->data->get_pointer($this->db_name);
				}
			}else{
				if (isset($this->rel_id)){
					$this->value = $this->data->get_rel($this->rel_id, $this->db_name,$this->ind);
				} else {
					$this->value = $this->data->get($this->db_name);
				}
			}

		}
		$this->_getMode();
	}

 /**
	* if detect a $_POST["fieldname"] it acquire the new value
	*
	* or if the field action is forced to 'insert' or 'update'
	* note: in descendant classes you can override this method for "formatting" purposes
	*
	* @access   private
	* @return   void
	*/
	function _getNewValue(){
		if (isset($this->request[$this->name])){
			if ($this->status == 'create'){
				$this->action = 'insert';
			} elseif ($this->status == 'modify'){
				$this->action = 'update';
			}
			$requestValue = $this->input->post($this->name);
			if (get_magic_quotes_gpc()) $requestValue = stripslashes($requestValue);

			/*
			 todo.. add prepping functions  & callbacks
			 if(isset($this->prep)) = call_user_func_array(array(&$ci, $method), $arguments);
			 $result = call_user_func_array($function, $arguments);
			*/

		 $this->newValue = $requestValue;
		} elseif( ($this->action == 'insert') && ($this->insertValue != null)) {
			$this->newValue = $this->insertValue;
		} elseif( ($this->action == 'update') && ($this->updateValue != null)) {
			$this->newValue = $this->updateValue;
		} else {
			$this->action = 'idle';
		}
	}

 /**
	* change field status for manage special fields (hiddens, read only, and so on)
	*
	* @access   private
	* @return   void
	*/
	function _getMode(){
		switch ($this->mode){

			case 'autohide':
				if (($this->status == 'modify')||($this->action == 'update')){
					$this->status = 'show';
					$this->apply_rules = false;
				}

				break;
			case 'readonly':
				$this->status = 'show';
				$this->apply_rules = false;
				break;

			case 'optionalupdate':
				if ($this->action == 'update'){
					if(!isset($this->request[$this->name.'CheckBox'])){
						//$this->status = 'show';
						$this->apply_rules = false;
					}
				}
			case 'show':
				break;
			default:
		}

		if (isset($this->when)){
			//$this->when[] = 'idle';
			if (!in_array($this->status,$this->when)){
				$this->status = 'hidden';
				$this->label = ' ';
				$this->apply_rules = false;
			}
		}
	}


	/**
	* options are necessary for multiple value inputs (like select, radio.. )
	* this method get an associative array of possible options by a given SQL string
	*
	* @access   public
	* @param    mixed   $options  can be a "select" query or a multidim. array of values
	* @return   void
	*/
	function options($options,$data_conn=null){

		if (is_array($options)){

			foreach ($options as $key=>$value){
					$this->option($key, $value);
			}

		} else {
			//Options can use an other connetion than the rapyd current one!!!!! to retreive data from an other DB
			//If rapyd use an other conn group than the default one you can use the default one by giving $data_conn=''
			if(!isset($data_conn) || !is_string($data_conn)  )
			{
					$data_conn =(isset($this->rapyd->data_conn))?$this->rapyd->data_conn:'';
			}
			$this->db = $this->ci->load->database($data_conn,TRUE);

			$query = $this->db->query($options);

			$result = $query->result_array();

			$new_options = array();

			if ($query->num_rows() > 0){

				foreach ($result as $row){
					$values = array_values($row);
					if (count($values)===2){
						$this->option($values[0], $values[1]);
					}
				}
			}
		}

	}


	function option($value,$description){
		$this->options[$value] = $description;
	}


	/**
	* one of the most important methods
	* when it's called, the data source of field (a dataobject) set the eventual new value.
	*
	* @access   public
	* @param    string  $save  if true, the dataobject is forced to save/store new value.
	* @return   bool
	*/
	function autoUpdate($save=false){

		$this->_getValue();
		$this->_getNewValue();

		if (is_object($this->data)&& isset($this->db_name) && $this->pointer==false){

			if (isset($this->rel_id)){
				if (isset($this->newValue)){
					$this->data->set_rel($this->rel_id, $this->db_name, $this->newValue,$this->ind);
				} else {
					$this->data->set_rel($this->rel_id, $this->db_name, $this->value,$this->ind);
				}

			} else {

				if (isset($this->newValue)){
					$this->data->set($this->db_name, $this->newValue);
				} else {
					$this->data->set($this->db_name, $this->value);
				}
				if($save){
					return $this->data->save();
				}
			}
		}
		return true;
	}

	/**
	* build (only) the field (widhout labels or borders)
	*
	* @access   public
	* @return   string  the field output
	*/
	function build(){
		$this->_getValue();

		switch ($this->status){
			case 'show':
			 if(substr_count($this->showformat,'decimal')>0){
					$output = nformat($this->value);
				}else{
					$output = $this->value;
				}
				break;

			default:
		}
		$out=$output.$this->extra_output."\n";
		if(!empty($this->valid_error)) $out.=br().$this->valid_error;
		return $this->output = $out;
	}


 /**
	* append text to field output
	*
	* @access   public
	* @access   string  $text (or html to be appended)
	* @return   void
	*/
	function append($text){
		$text = (lang($text)!='') ? lang($text) : $text;
		$this->extra_output .= '<span class="micro">'.$text.'</span>';
	}


 /**
	* draw, build & print the component
	*
	* @access   public
	* @return   void
	*/
	function draw(){
		$this->buildRow();
		echo $this->output;
	}

}


/**
 * all the extended fields
 *
 */
include_once(RAPYD_PATH.'fields/input.php');
include_once(RAPYD_PATH.'fields/dropdown.php');
include_once(RAPYD_PATH.'fields/textarea.php');
include_once(RAPYD_PATH.'fields/checkbox.php');
include_once(RAPYD_PATH.'fields/datetime.php');
include_once(RAPYD_PATH.'fields/date.php');
include_once(RAPYD_PATH.'fields/input2.php');
include_once(RAPYD_PATH.'fields/editor.php');
include_once(RAPYD_PATH.'fields/autoupdate.php');
include_once(RAPYD_PATH.'fields/submit.php');
include_once(RAPYD_PATH.'fields/reset.php');
include_once(RAPYD_PATH.'fields/button.php');
include_once(RAPYD_PATH.'fields/upload.php');
include_once(RAPYD_PATH.'fields/hidden.php');

include_once(RAPYD_PATH.'fields/free.php');
include_once(RAPYD_PATH.'fields/container.php');
include_once(RAPYD_PATH.'fields/iframe.php');
include_once(RAPYD_PATH.'fields/colorpicker.php');
include_once(RAPYD_PATH.'fields/html.php');
include_once(RAPYD_PATH.'fields/password.php');
include_once(RAPYD_PATH.'fields/radiogroup.php');
include_once(RAPYD_PATH.'fields/captcha.php');
//MYFW install
include_once(RAPYD_PATH.'fields/myiframe.php');
