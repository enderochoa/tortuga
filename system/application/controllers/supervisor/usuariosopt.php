<?php
class Usuariosopt extends Controller {
	var $titp='';
	var $tits='';
	var $url ='supervisor/usuariosopt/';
	function Usuariosopt(){
		parent::Controller();
		$this->load->library("rapyd");
		//$this->datasis->modulo_id(426,1);
	}
	
	function camclave(){
		$user       = $this->session->userdata('usuario');
		redirect($this->url."camcl/dataedit/modify/$user");
	}
	
	function camcl(){
		$this->rapyd->load("dataedit","dataobject");
		
		$user       = $this->session->userdata('usuario');
		
		$do = new DataObject('usuario');
		$do->load($user);
		
		$edit = new DataEdit("Cambio de Clave", $do);
		$edit->back_url = site_url("supervisor/usuarios/filteredgrid");
		
		$edit->pre_process('update','_valida');
				
		$edit->us_codigo = new inputField("C&oacute;digo de Usuario", "us_codigo");
		$edit->us_codigo->rule = "strtoupper|required";
		$edit->us_codigo->mode = "autohide";
		$edit->us_codigo->size = 20;
		$edit->us_codigo->maxlength = 15;
		$edit->us_codigo->type ='inputhidden';
		
		$edit->us_nombre = new inputField("Nombre", "us_nombre");
		$edit->us_nombre->rule = "strtoupper|required";
		$edit->us_nombre->size = 45;
		$edit->us_nombre->type ='inputhidden';
		
		$edit->an_clave = new inputField("Clave Actual","an_clave");
		$edit->an_clave->rule = "required";
		$edit->an_clave->type= "password";
		$edit->an_clave->size = 12;
		$edit->an_clave->maxlength = 15;
		$edit->an_clave->db_name==' ';
		$edit->an_clave->when = array("modify","idle");
		
		$edit->us_clave = new inputField("Clave Nueva","us_clave");
		$edit->us_clave->rule = "required|matches[us_clave1]";
		$edit->us_clave->type= "password";
		$edit->us_clave->size = 12;
		$edit->us_clave->maxlength = 15;
		$edit->us_clave->when = array("modify","idle");
		
		$edit->us_clave1 = new inputField("Confirmar Clave","us_clave1");
		$edit->us_clave1->rule = "required";
		$edit->us_clave1->type= "password";
		$edit->us_clave1->size = 12;
		$edit->us_clave1->maxlength  = 15;
		$edit->us_clave1->when = array("modify","idle");
		
		$edit->buttons("modify", "save", "undo");
		$edit->build();
				
		$data['content'] =$edit->output;        
		$data['title']   = " Usuarios ";
		$data["head"]    = $this->rapyd->get_head();
		$this->load->view('view_ventanas', $data); 
	}
	
	function _valida($do){
		$error='';
		
		$an_clave=$this->input->post('an_clave');
		
		$us_codigo =$do->get('us_codigo');
		$us_codigoe=$this->db->escape($us_codigo);
		
		
		$clave=$this->datasis->dameval("SELECT us_clave FROM usuario WHERE us_codigo=$us_codigoe");

		if($an_clave!=$clave)
		$error.="<div class='alert' >ERROR. La CLave Introducida es erronea</div>";

		if(empty($error)){
		
		}else{
			$do->error_message_ar['pre_ins']=$error;
			$do->error_message_ar['pre_upd']=$error;
			return false;
		}
	}

	function _post_update($do){
		$primary =implode(',',$do->pk);
		logusu($do->table,"Modifico $this->tits $primary ");
	}
}
?>
