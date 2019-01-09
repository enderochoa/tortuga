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
require_once("datagrid.php");


/**
 * DataGrid2 base class.
 *
 * @package    rapyd.components
 * @author     Andres Hocevar
 * @access     public
 */
class DataGrid2 extends DataGrid{
	var $agrupar= '';
	var $gvalor = '';
	var $tgrupo = '';
	var $totales=array();
	
 /**
  * PHP4 constructor.
  *
  * @access   public
  * @param string $title
  * @param mixed $data
  */
	
  function DataGrid2($title=null, $data=null){
    parent::DataGrid($title, $data);
  }
  function agrupar($tgrupo=null, $agrupar=null){
  	if(!empty($agrupar)){
  		$this->agrupar=$agrupar;
  		if (!is_array($this->data))
  			$this->db->orderby($agrupar);
  	}
  	if(!empty($tgrupo))
  		$this->tgrupo=$tgrupo;
  	else
  		$this->tgrupo=$agrupar;
  }
  
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
			$col["orderby_asc_url"]  = str_replace("<#field#>", $column->orderby_field, $this->_orderby_asc_url );
			$col["orderby_desc_url"] = str_replace("<#field#>", $column->orderby_field, $this->_orderby_desc_url);
			$data["headers"][] = $col;

		}

		//table rows
		$offset = $this->ci->uri->segment($this->uri_segment);
		$this->_row_id=(int)$offset;
		$this->gvalor='';
		foreach ( $this->data as $tablerow){
		  unset($row);
      $this->_row_id++;

      $llena=array();
			foreach ( $this->columns as $column ) {
				
				//##########
				if(!empty($this->agrupar) AND $this->gvalor!=$tablerow[$this->agrupar]){
					$expande=count($this->columns);
					$campo  =$this->tgrupo.' '.$tablerow[$this->agrupar];
					$llena[]=array('link'=>'','field'=>$campo,'attributes'=>"colspan=$expande class='grup'",'type'=>'normal');
					$data["rows"][] = $llena;
					$this->gvalor=$tablerow[$this->agrupar];
				}
				//##########
				
				$column->_row_id = $this->_row_id;
				$column->resetPattern();
				
				$column->setDataRow($tablerow);	


				$cell["link"] = $column->link;
				$cell["field"] = $column->getValue();
				$cell["attributes"] = $column->attributes;
				$cell["type"] = $column->columnType;
				
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

	function totalizar($param){
		$this->totales= func_get_args();
	}

   /**
  * build , main build method
  *
  * @access   public
  * @return   string  datagrid output
  */
	function build(){
		parent::build();
		if (count($this->totales)>0 AND count($this->data)>0){
			foreach ($this->totales as $indice) $tot[$indice]=0;
			foreach ($this->data as $tablerow){
				foreach ($this->totales as $indice)
						$tot[$indice] += $tablerow[$indice];
			}
			foreach (array_keys($this->data[0]) as $indice){
				if (!array_key_exists($indice,$tot))
					$tot[$indice]='__TOET__';
			}
			$this->data[]=$tot;
		}
		//print_r($this->data);

		return $this->output = $this->_buildGrid();
	}
}

?>
