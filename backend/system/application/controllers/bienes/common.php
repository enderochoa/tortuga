<?php
class Common extends controller {
	function get_subgrupo(){
		$grupo    =$this->db->escape($this->input->post('grupo'));
		$mSQL=$this->db->query("SELECT codigo,CONCAT_WS(' ',codigo,descrip) as valor FROM bi_subgrupo WHERE grupo=$grupo");
		echo "<option value=''></option>";
		if($mSQL){
			foreach($mSQL->result() AS $fila ){
				echo "<option value='".$fila->codigo."'>".$fila->valor."</option>";
			}
		}
	}
	
	function get_seccion(){
		$grupo    =$this->db->escape($this->input->post('grupo'));
		$subgrupo =$this->db->escape($this->input->post('subgrupo'));
		$mSQL=$this->db->query("SELECT codigo,CONCAT_WS(' ',codigo,descrip) as valor FROM bi_seccion WHERE grupo=$grupo AND subgrupo=$subgrupo");//
		echo "<option value=''></option>"; 
		if($mSQL){
			foreach($mSQL->result() AS $fila ){
				echo "<option value='".$fila->codigo."'>".$fila->valor."</option>";
			}
		}
	}
	
	function get_linea(){
		$grupo    =$this->db->escape($this->input->post('grupo'));
		$subgrupo =$this->db->escape($this->input->post('subgrupo'));
		$seccion  =$this->db->escape($this->input->post('seccion'));
		$mSQL=$this->db->query("SELECT codigo,CONCAT_WS(' ',codigo,descrip) as valor FROM bi_linea WHERE grupo=$grupo AND subgrupo=$subgrupo AND seccion=$seccion");
		echo "<option value=''></option>";
		if($mSQL){
			foreach($mSQL->result() AS $fila ){
				echo "<option value='".$fila->codigo."'>".$fila->valor."</option>";
			}
		}
	}
}
?>