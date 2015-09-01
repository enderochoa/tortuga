<?php
/**
 * textField - is common input field (type=text)
 * 
 * @package rapyd.components.fields
 * @author Felice Ostuni 
 * @license http://www.fsf.org/licensing/licenses/lgpl.txt LGPL
 * @version 0.9.6
 */


/**
 * textField
 * 
 * @package rapyd.components.fields
 * @author Andres Hocevar 
 * @access public 
 */
class hiddenField extends objField{

	var $type = 'text';
	var $readonly = FALSE;
	var $autocomplete = TRUE;
	var $css_class = 'input';

	function _getValue(){
		parent :: _getValue();
	}

	 function _getNewValue(){
		parent :: _getNewValue();
	 }

	 function build(){
		if(!isset($this -> size)){
			$this -> size = 45;
		}
		$this->_getValue();

		$output = "";

		switch ($this->status){
			case 'disabled':
			case 'show':
			case 'create':
			case 'modify':
			case 'hidden':
			if(empty($this -> value)){
				$this->value=$this->insertValue;
			}
			$attributes = array(
				'name'  => $this -> name,
				'id'    => $this -> name,
				'type'  => 'hidden',
				'value' => $this -> value);
			$output = form_input($attributes);

			break;
			default:
		}
		$this -> output = $output;
	}
}
?>