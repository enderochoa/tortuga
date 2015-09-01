<?php

class Welcome extends Controller {

	function Welcome()
	{
		parent::Controller();	
	}
	
	function index()
	{
		$this->load->library('rapyd');
		$this->load->view('welcome_message');
	}
}
?>