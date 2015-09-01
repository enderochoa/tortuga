<?php
class Formatos extends Controller{

	var $_direccion;
	function Formatos(){
		parent::Controller();
		$this->load->library("rapyd");
		//$this->load->library("numletra");
		$this->load->plugin('numletra');
		$this->load->helper('string');
		$protocolo=explode('/',$_SERVER['SERVER_PROTOCOL']);
		$this->_direccion=$protocolo[0].'://'.$_SERVER['SERVER_NAME'].'/'.trim_slashes($this->config->item('base_url'));
	}

	function index(){

	}

	function ver(){
		$parametros= func_get_args();
		if (count($parametros)>0){
			$_arch_nombre=implode('-',$parametros);
			$_fnombre=array_shift($parametros);
			$this->_direccion='http://localhost/'.trim_slashes($this->config->item('base_url'));
			$this->load->plugin('html2pdf');
			//$this->rapyd->load('dataobject');
			$this->db->_escape_char='';
			$this->db->_protect_identifiers=false;
			$query = $this->db->query("SELECT proteo FROM formatos WHERE nombre='$_fnombre'");
			if ($query->num_rows() > 0){
				$row = $query->row();
				ob_start();
					echo eval('?>'.preg_replace("/;*\s*\?>/", "; ?>", str_replace('<?=', '<?php echo ', $row->proteo)).'<?php ');
					$_html=ob_get_contents();
				@ob_end_clean();
				pdf_create($_html, $_arch_nombre);
			}else{
				echo 'Formato no existe';
			}
		}else{
			echo 'Faltan parametros';
		}
	}
	
	function descargar(){
		$this->load->helper('download');
		
		$parametros= func_get_args();
		if (count($parametros)>0){
			$_arch_nombre=implode('-',$parametros);
			$_fnombre=array_shift($parametros);
			
			
			//$this->rapyd->load('dataobject');
			$this->db->_escape_char='';
			$this->db->_protect_identifiers=false;
			$query = $this->db->query("SELECT proteo FROM formatos WHERE nombre='$_fnombre'");
			if ($query->num_rows() > 0){
				$row = $query->row();
				ob_start();
					echo eval('?>'.preg_replace("/;*\s*\?>/", "; ?>", str_replace('<?=', '<?php echo ', $row->proteo)).'<?php ');
					$_html=ob_get_contents();
				@ob_end_clean();				
				force_download($_arch_nombre.".tortuga",$_html);		
			}else{
				echo 'Formato no existe';
			}
		}else{
			echo 'Faltan parametros';
		}
	}

	function ver2(){
		$parametros= func_get_args();
		$this->_direccion='http://localhost/'.trim_slashes($this->config->item('base_url'));
		if (count($parametros)>0){
			$_arch_nombre=implode('-',$parametros);
			$_fnombre=array_shift($parametros);
			$this->load->library('dompdf/cidompdf');
			$query = $this->db->query("SELECT proteo FROM formatos WHERE nombre='$_fnombre'");
			if ($query->num_rows() > 0){
				$row = $query->row();
				ob_start();
				echo eval('?>'.preg_replace("/;*\s*\?>/", "; ?>", str_replace('<?=', '<?php echo ', $row->proteo)).'<?php ');
				$_html=ob_get_contents();
				@ob_end_clean();
				if(strlen($_html)>0)
					$this->cidompdf->html2pdf($_html,$_arch_nombre);
				else
					echo 'Formato no definido';
			}else{
				echo 'Formato no existe';
			}
		}else{
			echo 'Faltan parametros';
		}
	}

	function verhtml(){
		$parametros= func_get_args();
		if (count($parametros)>0){
			$_fnombre=array_shift($parametros);
			$query = $this->db->query("SELECT proteo FROM formatos WHERE nombre='$_fnombre'");
			if ($query->num_rows() > 0){
				$row = $query->row();
				eval('?>'.preg_replace("/;*\s*\?>/", "; ?>", str_replace('<?=', '<?php echo ', $row->proteo)).'<?php ');
			}else{
				echo 'Formato no existe';
			}
		}else{
			echo 'Faltan parametros';
		}
	}
	
	function verpdf(){
		//$parametros= func_get_args();
		//if (count($parametros)>0){
			//$_fnombre=array_shift($parametros);
			$query = $this->db->query("SELECT proteo2 FROM formatos_copy WHERE nombre='OCOMPRA'");
			$row = $query->row();
			eval('?>'.preg_replace("/;*\s*\?>/", "; ?>", str_replace('<?=', '<?php echo ', $row->proteo2)).'<?php ');
				//print_R($row);

	}
	
	function instalar(){
//		$mSQL="ALTER TABLE `formatos` ADD `proteo3` TEXT NULL AFTER `forma`";
//		$this->db->simple_query($mSQL);
//		$mSQL="ALTER TABLE `formatos` ADD `proteo2` TEXT NULL AFTER `forma`";
//		$this->db->simple_query($mSQL);
		$mSQL="ALTER TABLE `formatos` ADD `proteo` TEXT NULL AFTER `forma`";
		$this->db->simple_query($mSQL);
		$mSQL="ALTER TABLE `formatos` ADD `harbour` TEXT NULL AFTER `proteo`";
		$this->db->simple_query($mSQL);
	}
}
?>




