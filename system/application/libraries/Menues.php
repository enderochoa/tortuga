<?php
class menues
{
	var $CI;
	
	function menues(){
		$this->CI=& get_instance();
	}
	
	function sub_menu($tabla=NULL){
		if ($tabla!=NULL){
			if ($this->CI->datasis->essuper())
				$mSQL = "SELECT ejecutar, titulo, mensaje,target  FROM intramenu  WHERE  modulo LIKE '$tabla%' AND modulo<>'$tabla' ORDER BY titulo";
			else
				$mSQL = "SELECT ejecutar, titulo, mensaje,target  FROM intramenu  WHERE  modulo LIKE '$tabla%' AND modulo<>'$tabla' ORDER BY titulo";
			$query = $this->CI->db->query($mSQL);
			$content['cant']=$query->num_rows();
			
			if ($query->num_rows() > 0){
				foreach ($query->result() as $row){
					$content["ejecutar"][]= $row->ejecutar;
					$content["titulo"][]  = $row->titulo;
					$content["mensaje"][] = $row->mensaje;
					if($row->target=='popu')
					$ejecutar=anchor_popup($row->ejecutar, $row->titulo, $att);
				elseif($row->target=='javascript')
					$ejecutar="<a href='javascript:$row->ejecutar' title='$row->titulo'>$row->titulo</a> ";
				else
					$ejecutar=anchor($row->ejecutar, $row->titulo);
				$content["link"][] = $ejecutar;
				}
			}
			return ($this->CI->load->view('view_sub_menu', $content,TRUE));
		}
	}
}
?>