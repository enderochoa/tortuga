<?php
class Docu extends Controller{
	var $niveles;
	
	function Docu(){
		parent::Controller();
		$this->load->library("rapyd");
	}

	function index(){
		
		$list = array();
		$list[]=anchor('doc/modulos','Modulos documentados');
		$list[]=anchor('doc/tablas','Tablas documentadas');
		
		$attributes = array(
			'class' => 'boldlist',
			'id'    => 'mylist'
			);

		$out=ul($list, $attributes);
		$data['content'] = $out;
		$data["head"]    = script("jquery.pack.js").script("jquery.treeview.pack.js").$this->rapyd->get_head().style('jquery.treeview.css');
		$data['title']   = 'Documentaci&oacute;n';
		$this->load->view('view_ventanas', $data);
		
	}
}
?>