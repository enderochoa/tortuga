<?php if (!defined('BASEPATH')) exit('No direct script access allowed');
require_once('Datasis.php'); 
/**
 * DataSIS Components
 *
 * @author		Andres Hocevar
 * @version		0.1
 * @filesource
 **/

class Extjs extends Datasis {
	
	function combo($data){
		$sal=array();
		if(!is_array($data))
		$data=$this->consularray($data);
		foreach ($data as $key=>$value)
			$sal[]="[".$this->js_escape($key).",".$this->js_escape($value)."]";
		
		return implode(' , ',$sal);
	}
	
	function js_escape($string){
		$string=str_replace("\r",'',$string);
		$string=str_replace("\n",'',$string);
		$string=preg_replace('/\s\s+/', ' ', $string);
		$string=addslashes($string);
		$string=str_replace('<','\<',$string);
		$string=str_replace('>','\>',$string);
		$string=str_replace(';','\;',$string);
		//$string=str_replace('<',"'+String.fromCharCode(60)+'",$string);
		//$string=str_replace('>',"'+String.fromCharCode(62)+'",$string);
		$string='\''.$string.'\'';
		return $string;
	}
}

class Grid extends Extjs {
	var $sql;
	var $filter=null;
	var $select=array();
	var $s=array();
	var $from='';
	var $join=array();
	var $order=array();
	var $limit=50;
	var $start=0;
	var $where=array();
	
	function Grid(){
		$this->sql =& get_instance();
	}
	
	function select($select){
		$this->select=$select;
		$this->sql->db->select($select);
	}
	
	function from($table){
		$this->sql->db->from($table);
		$this->from=$table;
	}
	
	function join($table,$join,$opt=''){
		$this->sql->db->join($table,$join,$opt);
		$this->join[]=array('t'=>$table,'j'=>$join,'o'=>$opt);
	}
	
	function order_by($field,$ord='asc'){
		$this->sql->db->order_by($field,$ord);
		$this->order[]=array('f'=>$field,'s'=>$ord);
	}
	
	function limit($limit,$start=null){
		$this->sql->db->limit($limit,$start);
		$this->limit=$limit;
		$this->start=$start;
	}
	
	function where($where){
		$this->sql->db->where($where);
		$this->where[]=$where;
	}
	
	function filter($filter){
		$this->filter=$filter;
	}
	
	function output(){
		
		if ($this->filter){
			$filter = json_decode($this->filter, true);
			if (is_array($filter)) {
				$this->sql->db->limit(1);
				$query=$this->sql->db->get();
				$lfields=$query->list_fields();
				$c=0;
				foreach ($lfields as $field)
				{
				   $this->s[$field]=array('db'=>$this->select[$c]);
				   $c++;
				}
				
				//Dummy Where.
				$where = array();
				
				for ($i=0;$i<count($filter);$i++){
					switch($filter[$i]['type']){
					case 'string' :
					
					$where[]= $this->s[$filter[$i]['field']]['db']." LIKE '%".$filter[$i]['value']."%'"; 
						Break;
					//case 'list' :
					//	if (strstr($filter[$i]['value'],',')){
					//		$fi = explode(',',$filter[$i]['value']);
					//		for ($q=0;$q<count($fi);$q++){
					//			$fi[$q] = "'".$fi[$q]."'";
					//		}
					//		$filter[$i]['value'] = implode(',',$fi);
					//			$qs .= " AND pers.".$filter[$i]['field']." IN (".$filter[$i]['value'].")";
					//	}else{
					//		$qs .= " AND pers.".$filter[$i]['field']." = '".$filter[$i]['value']."'";
					//	}
					//	Break;
					//case 'boolean' : $qs .= " AND pers.".$filter[$i]['field']." = ".($filter[$i]['value']); 
					//	Break;
					//case 'numeric' :
					//	switch ($filter[$i]['comparison']) {
					//		case 'ne' : $qs .= " AND pers.".$filter[$i]['field']." != ".$filter[$i]['value']; 
					//			Break;
					//		case 'eq' : $qs .= " AND pers.".$filter[$i]['field']." = ".$filter[$i]['value']; 
					//			Break;
					//		case 'lt' : $qs .= " AND pers.".$filter[$i]['field']." < ".$filter[$i]['value']; 
					//			Break;
					//		case 'gt' : $qs .= " AND pers.".$filter[$i]['field']." > ".$filter[$i]['value']; 
					//			Break;
					//	}
					//	Break;
					//case 'date' :
					//	switch ($filter[$i]['comparison']) {
					//		case 'ne' : $qs .= " AND pers.".$filter[$i]['field']." != '".date('Y-m-d',strtotime($filter[$i]['value']))."'"; 
					//			Break;
					//		case 'eq' : $qs .= " AND pers.".$filter[$i]['field']." = '".date('Y-m-d',strtotime($filter[$i]['value']))."'"; 
					//			Break;
					//		case 'lt' : $qs .= " AND pers.".$filter[$i]['field']." < '".date('Y-m-d',strtotime($filter[$i]['value']))."'"; 
					//			Break;
					//		case 'gt' : $qs .= " AND pers.".$filter[$i]['field']." > '".date('Y-m-d',strtotime($filter[$i]['value']))."'"; 
					//			Break;
					//	}
					//	Break;
					}
				}
				
				foreach($where as $value)
				$this->sql->db->where($value);
			}
		}
		
		if($this->filter){
			foreach($this->where as $value)
			$this->sql->db->where($value);
			
			$this->sql->db->limit($this->limit,$this->start);
			foreach($this->order as $r)
			$this->sql->db->order_by($r['f'],$r['s']);
			
			$this->sql->db->select($this->select);
			
			$this->sql->db->from($this->from);
			
			foreach($this->join as $r)
			$this->sql->db->join($r['t'],$r['j'],$r['o']);
		}
		
		$query=$this->sql->db->get();
		
		$arr = array();
		$results=0;
		foreach ($query->result_array() as $row)
		{
			$meco = array();
			foreach( $row as $idd=>$campo ) {
				$meco[$idd] = utf8_encode($campo);
			}
			$arr[] = $meco;
			$results++;
		}
		
		return '{success:true, message:"Loaded data" ,results:'. $results.', data:'.json_encode($arr).'}';
	}
}
?>