<?php

class Frames extends Controller
{
	var $cargo=0;
	
	function Frames(){
		parent::Controller();
		$this->load->library("rapyd");
	}

	function index(){
		$data['pre']='sfac';
		$this->load->view('view_repoframe',$data);
	}
	
	function cabeza(){
		$this->load->view('view_repoCabeza');
	}

}

?>
