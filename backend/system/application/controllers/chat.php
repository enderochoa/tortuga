<?php

class Chat extends Controller {
	
	function Chat(){
		parent::Controller();
		$this->load->library("rapyd");
	}
	
	function index() {
		
		$this->rapyd->load("phpfreechat");
		if (isset($this->session->userdata['logged_in']) AND $this->session->userdata['logged_in']){
			$this->session->set_userdata('last_activity', time());
			if($this->datasis->essuper('usuario')) $params["isadmin"] = true;
			$params["nick"]     = utf8_encode($this->session->userdata('usuario'));
			$params["language"] = "es_ES";
			$params["title"]    = $this->datasis->traevalor("TITULO1");
			$params["serverid"] = md5('ProteoERP'); // calculate a unique id for this chat
			$params["frozen_nick"]   = false;  
			$params["server_script_url"]  = site_url('/chat');
			$params["data_public_url"]    =site_url('phpfreechat/data/public');   
			$params["quit_on_closedwindow"]=true;
			$params["display_pfc_logo"]=false;
			
			$chat = new phpfreechat($params);
			
			$data['head']    =$chat->printJavascript(true).$chat->printStyle(true);
			$data['content'] = $chat->printChat(true);
			$data['title']   =" Prueba de chat ";
			$this->load->view('view_freechat', $data);
		}else
			echo "window.close()";
	}
	
}
?>
