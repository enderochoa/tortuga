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
 * DataObject impl. Active Record (little bit more classic) based on CI AR driver
 *
 * @package    rapyd.components
 * @author     Felice Ostuni
 * @author     Thierry Rey 
 * @access     public
 */
 

class DataObject{

  var $table  = null;
  var $loaded = false; 
  var $pk = array();
  
  var $fields = array();
  var $field_meta	= array();
  var $data = null;
  var $data_rel = array();  
  
  var $_pre_process_functions = array();
  var $_post_process_functions = array();
  var $post_process_result = null;
	var $pre_process_result = null;
	var $error_message_ar=array('pre_upd'=>'pre process error on Update','pre_ins'=>'pre process error on Insert','pre_del'=>'pre process error on Delete','post_upd'=>'','post_ins'=>'','post_del'=>'');

  var $_rel_type     = array(); //[] 0 one_to_one, 1 one_to_many, 2 many_to_mary [] contador
  var $_rel_fields   = array(); //campos para el join
  var $_one_to_one   = array();
  var $_one_to_many  = array();
  var $_many_to_many = array();
  
 /**
  * PHP4 constructor.
  *
  * @access   public
  * @param    string   $table     database table name
  * @return   void
  */
  function DataObject($table){
  
  
    $this->ci =& get_instance();
    
    $this->rapyd =& $this->ci->rapyd;
    
    $data_conn =(isset($this->rapyd->data_conn))?$this->rapyd->data_conn:'';
    $this->db = $this->ci->load->database($data_conn,TRUE);

    $this->table  = $table;
    $this->fields = $this->db->field_data($table);
		$this->make_rel=true;

    // to support tables with one or more PK
    foreach ($this->fields as $field) {
      
      $this->field_names[] = $field->name;
      $this->field_meta[$field->name] = $field;
			
      if ($field->primary_key) {
        $this->pk[$field->name] = "";
      }
    }
    
    if (count($this->pk)==0){
      //table must have a PK 
      $this->error = "The table $table are no PK";
      exit();
    }
     
  } 

 /**
  * prepare the call to a method inside current controller/method which can prevent $action to be executed.
  * inside "function" definition, the first parameter must be the copy of DO, so in your pre-process function you can have access to current data.
  * from DataForm (so also the extended DataEdit) you can use the same function.
  * 
  * @access   public
  * @param    string    $action can be "insert", "update" or "delete"
  * @param    string    $function is the function/method name to be called
  * @param    array     $arr_values optional and custom array of parameters
  * @return   void
  */
  function pre_process($action,$function,$arr_values=array()){
    $this->_pre_process_functions[$action] = array("name"=>$function, "arr_values"=>$arr_values);
  }

 /**
  * exec the call of pre_process function for $action (if is set)
  * 
  * @access   private
  * @param    string    $action can be "insert", "update" or "delete"
  * @return   void
  */
  function _exec_pre_process_functions($action){
  	$this->pre_process_result = TRUE; 	
    if (isset($this->_pre_process_functions[$action])){
      $function = $this->_pre_process_functions[$action];
      $arr_values = $function["arr_values"];
      (count($arr_values)>0)? array_unshift($arr_values, $this):$arr_values = array($this);
      $this->pre_process_result =  call_user_func_array(array(&$this->ci, $function["name"]), $arr_values );
      return  $this->pre_process_result;
    }
  }

 /**
  * prepare the call to a method inside current controller/method which w'll be executed after $action execution
  * inside "function" definition, the first parameter must be the copy of DO, so in your post-process function you can have access to current data.
  * from DataForm (so also the extended DataEdit) you can use the same function.
  *
  * @access   public
  * @param    string    $action can be "insert", "update" or "delete"
  * @param    string    $function is the function/method name to be called
  * @param    array     $arr_values optional and custom array of parameters
  * @return   boolean
  */
  function post_process($action,$function,$arr_values=array()){
    $this->_post_process_functions[$action] = array("name"=>$function, "arr_values"=>$arr_values);
  }
  
 /**
  * exec the call of post_process function for $action (if is set)
  * 
  * @access   private
  * @param    string    $action can be "insert", "update" or "delete"
  * @return   void
  */
  function _exec_post_process_functions($action){
    if (isset($this->_post_process_functions[$action])){
      $function = $this->_post_process_functions[$action];
      $arr_values = $function["arr_values"];
      (count($arr_values)>0)? array_unshift($arr_values, $this):$arr_values = array($this);      
      $this->post_process_result =  call_user_func_array(array(&$this->ci, $function["name"]), $arr_values); 
      return  $this->post_process_result;
    }
  }

 /**
  * load a record from the DB, receives a parameter that
  * can be either a single key or a multiple field key (using an array)
  * 
  * @access   public
  * @param    mixed    $id
  * @return   boolean
  */
  function load($id){

  	$this->pre_process_result = null;
    // can be an assoc. array:   array(pk1=>value, pk2=>value)
    if ( is_array( $id)) {
      if ( sizeof($id) != sizeof($this->pk) ) {
        show_error("DataObject Error: Not enough parameters to load record");
        return false;
      } else {
        foreach ($this->pk as $keyfield=>$keyvalue){
          $this->pk[$keyfield] = $id[$keyfield];
        }
      }
      
    //can be the value of the PK (the record ID)
    } else {
       $keys = array_keys($this->pk);
       $key = $keys[0];
       $this->pk[$key] = $id;
    }
    
 
    $query = $this->db->getwhere($this->table, $this->pk);
    
    
    
    if ($query->num_rows()>1){
      show_error("DataObject Error: More than one result");
      return false;
    } elseif ($query->num_rows()==1) {
      $results = $query->result_array();     
      $this->bind_data($results[0]);
      $this->loaded = true;
      
      $this->bind_rel();      
           
      return true;
    } else {
      $this->loaded = false;
      return false;
    }
  } 

 /**
  * associates the data from a recordset with the current object
  * 
  * @access   private
  * @param    array   $data is an associative array ($fieldname=>$value, ...)
  * @return   void
  */
  function bind_data( $data){
    $this->data = $data;
  } 


 /**
  * execute sub queries if there are relationships
  * 
  * @access   private
  * @param    array   $data is an associative array ($fieldname=>$value, ...)
  * @return   void
  */
  function bind_rel(){

    if($this->make_rel){
      if (count($this->pk)>1) return;
      
      reset($this->pk);
      list($pk_name, $pk_value) = each($this->pk);
		  $table_dot_pk = $this->table.".".$pk_name;
      $where = array ($table_dot_pk => $pk_value); 
      
      
		  if (count($this->_one_to_one)>0){
      
        foreach($this->_one_to_one as $_one_to_one){
          $this->db->select($_one_to_one["table_alias"].".*");
          $this->db->from($this->table);
          $_one_to_one["on"] = str_replace("<#pk#>", $pk_name, $_one_to_one["on"]);
          $this->db->join($_one_to_one["table"], $_one_to_one["on"]);
          $this->db->where($where);
      
      
          $query = $this->db->get();
          
          if($query->num_rows()>0)  {
           $results = $query->result_array();
           $this->data_rel[$_one_to_one["id"]] = $results[0]; // one-to-one (i need just one record)
           
          }
        }
		  }
      
		  if (count($this->_one_to_many)>0){
		        
        foreach($this->_one_to_many as $_one_to_many){
          $this->db->select($_one_to_many["table_alias"].".*");
          $this->db->from($this->table);
          $_one_to_many["on"] = str_replace("<#pk#>", $pk_name, $_one_to_many["on"]);
          $this->db->join($_one_to_many["table"], $_one_to_many["on"]);
          $this->db->where($where);
          
          $query = $this->db->get();
          if($query->num_rows()>0)  {
           $this->data_rel[$_one_to_many["id"]] = $query->result_array(); // one-to-many (i need all related records)
          }
        }
		  }
      
		  if (count($this->_many_to_many)>0){
		        
        foreach($this->_many_to_many as $_many_to_many){
          $this->db->select($_many_to_many["table_alias"].".*");
          $this->db->from($_many_to_many["rel_table"]);
          $this->db->join($_many_to_many["table"], $_many_to_many["on"],"left");
          $on2 = $_many_to_many["rel_table"].".".$pk_name." = ".$this->table.".".$pk_name;
          $this->db->join($this->table, $on2,"left");				             
          $this->db->where($where);
          
          $query = $this->db->get();
          
          if($query->num_rows()>0)  {
           $this->data_rel[$_many_to_many["id"]] = $query->result_array(); // many-to-many (i need all related records)
          }
        }
			}
		}
  }

/**
  * save the record by executing insert or update command
  * 
  * @access   public   
  * @return   boolean
  */
  function save(){  
    //INSERT
    if (!$this->loaded) {
    
			//by default pk is AutoIncrement and reloaded after an insert, otherwise new value of pk(s) is loaded from user input
  	  $pk_ai = true;
  	  foreach ($this->pk as $keyfield => $keyvalue)
      {
        if(isset($this->data[$keyfield])){
        	$this->pk[$keyfield] = $this->data[$keyfield];
        	$pk_ai = false;
        }
      }
      
      //exec pre process function to escape the insert if it return false
      $escape = $this->_exec_pre_process_functions("insert");
      
      if ($escape !== false)
      {
        $result = $this->db->insert($this->table, $this->data);
        if($result && $pk_ai){
          $keys = array_keys($this->pk);
          $key = $keys[0];
          $this->pk[$key] = $this->insert_id();
          
          $this->data[$key] = $this->pk[$key];
          $this->loaded = true;
          //$this->bind_rel();
         }

         //guarda detalles
         foreach($this->data_rel AS $rel=>$items){
           //hace las equivalencias de las claves primarias
           foreach($this->_rel_fields[$rel] AS $iind){ // $iind[0] encab $iind[1] detalle
             $indiceit=$iind[1];
             $indice =$iind[0];
             $pk_rel[$indiceit]=$this->pk[$indice];
           }
         	
         	 if($this->_rel_type[$rel][0]==0){   //uno a uno
         	 	 $itdata=array_merge($items,$pk_rel);
         	 	 $itresult = $this->db->insert($this->_one_to_one[$rel]['table'], $itdata);
         	 }elseif($this->_rel_type[$rel][0]==1){//uno a muchos
         	 	 foreach($items AS $item){
         	     $itdata=array_merge($item,$pk_rel);
         	 	 	 $itresult = $this->db->insert($this->_one_to_many[$rel]['table'], $itdata);
         	 	 }
         	 }
         }
         //fin del guarda detalle
         
         if($result && $pk_ai){
           $this->bind_rel();
         }
         
         
        //exec post process function and store result in a property
        $this->post_process_result = $this->_exec_post_process_functions("insert");
        return $result;
        
      } else {
        return false;
      }
      
    //UPDATE
    } else {

    	//print_r($this->data);
		  //print_r($this->data_rel);
		  //print_r($this->pk);
		  //print_r(array_merge($this->pk,$this->data_rel['itstra'][1]));
		  //print_r(array_merge($this->pk,$this->data_rel['itstra'][2]));
		  //exit();
    
      $this->db->where($this->pk);
      
      //exec pre process function to escape the insert if it return false
      $escape = $this->_exec_pre_process_functions("update");
      
      //by default pk is AutoIncrement otherwise new value of pk(s) is loaded from user input (after being used to retrieve the record to update)
      foreach ($this->pk as $keyfield => $keyvalue){
        if(isset($this->data[$keyfield])){
          $this->pk[$keyfield] = $this->data[$keyfield];
        }
      }
      
     
      if ($escape !== false)
      {
        $result = $this->db->update($this->table, $this->data);
        
        // guarda detalle
        //$this->db->where($this->pk);
        foreach($this->data_rel AS $rel=>$items){
        	//hace las equivalencias de las claves primarias
        	foreach($this->_rel_fields[$rel] AS $iind){ // $iind[0] encab $iind[1] detalle
             $indiceit=$iind[1];
             $indice =$iind[0];
             $pk_rel[$indiceit]=$this->pk[$indice];
          }
        	$this->db->where($pk_rel);
        	 if($this->_rel_type[$rel][0]==0){   //uno a uno
        	 	 $itresult = $this->db->update($this->_one_to_one[$rel]['table'], $items);
        	 }elseif($this->_rel_type[$rel][0]==1){//uno a muchos
        	 	 $itresult = $this->db->delete($this->_one_to_many[$rel]['table']);
        	 	 foreach($items AS $item){
        	 	 	 $itdata=array_merge($item,$pk_rel);
         	 	 	 $itresult = $this->db->insert($this->_one_to_many[$rel]['table'], $itdata);
        	 	 }
        	 }
        }
        //fin detalle
        
        //exec post process function and store result in a property
        $this->post_process_result = $this->_exec_post_process_functions("update");
        return $result;
        
      } else {
        return false;
      }
      
    } 
  } 

 /**
  * returns the last autonumbering ID inserted. Returns false if function not supported
  * 
  * @access   public
  * @return   variant  integer or false
  */
  function insert_id(){
    return $this->db->insert_id();
  } 

 /**
  * loads a register from the DB, receives 3 parameters 
  * 
  * @access   public
  * @param    string   $field
  * @param    variant  $value   
  * @param    char     $fieldMetaType   (adodb metatype.. can be sniffed.. but.. non cio' voglia..)
  * @return   boolean
  */
  function load_where($field, $value){
    $this->db->where($field, $value);
    
    $query = $this->db->get($this->table);
    
    if ($query->num_rows()>1){
      show_error("DataObject Error: More than one result");
      return false;
    } elseif($query->num_rows()===1)  {
      $results = $query->result_array();     
      $this->bind_data($results[0]);
      
      foreach ($this->pk as $keyfield=>$keyvalue){
        $this->pk[$keyfield] = $results[0][$keyfield];
      }
      
      $this->loaded = true;
      
      $this->bind_rel(); 
      return true;
      
    } else {
      return false;
    }
  }

 /**
  * prevent duplication of a given field
  * 
  * @param   string   $field
  * @param   variant  $value   
  * @return  boolean
  */
  function is_unique($field, $value){
    
    $this->db->where($field, $value);
    $query = $this->db->get($this->table);
   
    if($query->num_rows()>1){
      return false;
    } elseif ($query->num_rows()===1){

      if ($this->loaded){
        return ($this->data[$field] == $value);
      } else {
        return false;
      }
 
    } else {
      return true;
    }
    
  }
  
 /**
  * prevent duplication of given fields on a db row
  * 
  * @param   array    of field=>value to be checked
  * @return  boolean
  */
  function are_unique($field){
  
    if (is_array($field) && count($field)>0){
      foreach($field as $fieldname => $value){
        $this->db->where($fieldname, $value);
      }
    } else {
      return false;
    }
    $query = $this->db->get($this->table);
      
    if ($query->num_rows()>1){
      return false;
    } elseif ($query->num_rows()===1){
        
      if ($this->loaded){
        foreach($field as $fieldname => $value){
          if($this->data[$fieldname] != $value) return false ;
        }
        return true;
      }
      return false;
    } else {
      return true;
    }
  }

 /**
  * get current value of a field
  * 
  * @access   public
  * @param    string  $field  the field name
  * @return   mixed  value of a field or null if not set
  */
  function get($field){
    if (isset($this->data[$field])) {
      return $this->data[$field];
    } else {
      return null;
    } 
  } 
  
  function count_rel($rel_id){
  	if(isset($this->data_rel[$rel_id])){
  		if(is_array($this->data_rel[$rel_id])){
  			return count($this->data_rel[$rel_id]);
  		}
  	}
  	return 0;
  }
  
  function get_rel($rel_id, $field,$id=-1){
    
    if($this->_rel_type[$rel_id][0]==0){
    	if (isset($this->data_rel[$rel_id][$field])) {
    	  return $this->data_rel[$rel_id][$field];
    	} else {
    	  return null;
    	}
    }elseif($this->_rel_type[$rel_id][0]==1){
    	if($id<0) $id=$this->_rel_type[$rel_id][1]; 
      if (isset($this->data_rel[$rel_id][$id][$field])) {
    	  return $this->data_rel[$rel_id][$id][$field];
    	} else {
    	  return null;
    	}	
    }
    return null;
  } 
  
  
 /**
  * get related array (array of related/joined records)
  * 
  * @access   public
  * @param    string  $rel_id  the field name
  * @return   mixed  value of a field or null if not set
  */
  function get_related($rel_id){
    if (isset($this->data_rel[$rel_id])) {
      return $this->data_rel[$rel_id];
    } else {
      return null;
    } 
  }
  

 /**
  * set new value to a field
  * 
  * @access   public
  * @param    string   $field  the field name
  * @param    variant  $value     the new value
  * @return   void
  */
  function set($field, $value)
  { 
		$field_meta = $this->field_meta[$field];
		
		if (in_array($field_meta->type,array("int","date")) && $value=="")
		{
		  $value = null;
		}
		
    $this->data[$field] = $value;
  } 
  
  
  function set_rel($rel_id, $field, $value,$id=-1)
  { 
  	if($this->_rel_type[$rel_id][0]==0){      //uno a uno
    	$this->data_rel[$rel_id][$field] = $value;
    }elseif($this->_rel_type[$rel_id][0]==1){ //uno muchos
    	if($id<0) $id=$this->_rel_type[$rel_id][1]; 
    	$this->data_rel[$rel_id][$id][$field]= $value;
    }
    //$this->data_rel[$rel_id][$field] = $value;
  } 
  
  
  
 /**
  * increment (+1) a field value
  * 
  * @access   public
  * @param    string   $field  the field name
  * @return   void
  */
  function inc($field, $inc=1){ 
    if (isset($this->data[$field])) {
      $this->data[$field] = $this->data[$field]+$inc;
    } else {
      $this->data[$field] = $inc;
    }

  } 

 /**
  * decrement (-1) a field value
  * 
  * @access   public
  * @param    string   $field     the field name
  * @param    int      $dec       decrement factor
  * @param    int      $positive  if value is negative return false;
  * @return   void
  */
  function dec($field, $dec=1, $positive=true){ 
    if (isset($this->data[$field])) {
      if (($this->data[$field]-$dec < 0) && ($positive)){
        return false;
      } else {
        $this->data[$field] = $this->data[$field]-$dec;
      }
    } else {
      if ($positive){
        return false;
      } else {
        $this->data[$field] = 0-$dec;
      }
    }

  } 


 /**
  * get the the array of values of current record
  * 
  * @access   public
  * @return   array    associative array of current record
  */
  function get_all() {
    
    $data = $this->data;    
    $data = array_merge($data, $this->data_rel);

    return $data;
  } 

 /**
  * delete current loaded field  
  * 
  * @access   public
  * @return   boolean
  */
  function delete(){ 
    if ($this->loaded){
      $this->db->where($this->pk);
      
      //exec pre process function to escape the insert if it return false
      $escape = $this->_exec_pre_process_functions("delete");
     
      if ($escape !== false)
      {

        //inicia detalle
        foreach($this->data_rel AS $rel=>$items){
        	//hace las equivalencias de las claves primarias
        	foreach($this->_rel_fields[$rel] AS $iind){ // $iind[0] encab $iind[1] detalle
             $indiceit=$iind[1];
             $indice =$iind[0];
             $pk_rel[$indiceit]=$this->pk[$indice];
          }
        	$this->db->where($pk_rel);
        	 if($this->_rel_type[$rel][0]==0){   //uno a uno
        	 	 $itresult = $this->db->delete($this->_one_to_one[$rel]['table']);
        	 }elseif($this->_rel_type[$rel][0]==1){//uno a muchos
        	 	 $itresult = $this->db->delete($this->_one_to_many[$rel]['table']);
        	 }
        }
        //fin detalle        
        
        $this->db->where($this->pk);
        $result = $this->db->delete($this->table);

        //exec post process function and store result in a property
        $this->post_process_result = $this->_exec_post_process_functions("delete");
        return $result;
       
      } else {
        return false;
      }

    } else {
      return false;
    }
  } 
  
 /**
  * delete a record (non necessary current loaded) 
  * 
  * @access   public
  * @return   boolean
  */
  function delete_where($field,$value){      
  
    $this->db->where($field, $value);
    return $this->db->delete($this->table);

  }
  
  
  function rel_one_to_one ($id, $table, $field_fk="<#pk#>", $field="", $cascade="") {
    if ($field=="") $field = $field_fk;  
    $arr["id"] = $id;
    $arr["table"] = $table;  //table to join  
    if (strpos($table," as")>0) {
      $alias = substr($table,strpos($table," as ")+4);
    } else {
      $alias = $table;
    }
    $arr["table_alias"] = $alias;
    $arr["on"] = $this->_rel_build_on($alias,$field_fk,$id);
    //$arr["on"] = $alias.".".$field." = ".$this->table.".".$field_fk;  //join "on"
    $arr["cascade"] = $cascade;
    $this->_rel_type[$id]=array(0,0);
    $this->_one_to_one[$id] = $arr; 
  }
  

  function rel_one_to_many ($id, $table, $field_fk="<#pk#>", $cascade="") {
    $arr["id"] = $id;
    $arr["table"] = $table;
    if (strpos($table," as")>0) {
      $alias = substr($table,strpos($table," as ")+4);
    } else {
      $alias = $table;
    }
    $arr["table_alias"] = $alias;
    //$arr["on"] = $alias.".".$field_fk." = ".$this->table.".".$field_fk;  //join "on"
    $arr["on"] = $this->_rel_build_on($alias,$field_fk,$id);
    $arr["cascade"] = $cascade; 
    $this->_rel_type[$id]=array(1,0);
    $this->_one_to_many[$id] = $arr; 
  }


  function rel_many_to_many ($id, $table, $rel_table, $field, $cascade="") {
    $arr["id"] = $id;
    $arr["rel_table"] = $rel_table;
    $arr["table"] = $table;
    
    //non sto' capendo piu' niente.. ma qui devo procedere come nelle altre relazioni
    if (strpos($table," as")>0) {
      $alias = substr($table,strpos($table," as ")+4);
    } else {
      $alias = $table;
    }
    $arr["table_alias"] = $alias; 
    
    $arr["on"] = $rel_table.".".$field." = ".$table.".".$field;  //join "on"
    $arr["cascade"] = $cascade;
    $this->_rel_type[$id]=array(2,0);
    $this->_many_to_many[$id]= $arr; 
  }

 /**
  * construye el join 'on' para las relaciones
  * 
  * @access   private
  * @return   string
  */

	function _rel_build_on($alias,$fields,$id){
		if(is_array($fields)){
			$on='';
			foreach($fields AS $encab=>$item){
				if(strlen($on)>0) $on .= ' AND ';
				if(is_int($encab)){
					$on .= $this->table.'.'.$item.'='.$alias.'.'.$item;
					$this->_rel_fields[$id][]=array($item,$item);
				}else{
					$on .= $this->table.'.'.$encab.'='.$alias.'.'.$item;
					$this->_rel_fields[$id][]=array($encab,$item);
				}
				
			}
		}else{
			$on= $this->table.".".$fields." = ".$alias.".".$fields;
			$this->_rel_fields[$id][]=array($fields,$fields);
		}
		return $on;
	}

} 

?>