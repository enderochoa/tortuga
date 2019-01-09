<?php 
$query = $this->db->query("SELECT proteo FROM formatos WHERE nombre='PRESUP'");
if ($query->num_rows() > 0){
   $row = $query->row();
   echo $row->proteo;
}else{
	echo 'Formato no existe';
}
?>