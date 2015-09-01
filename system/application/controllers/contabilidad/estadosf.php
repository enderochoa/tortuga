<?php
class Estadosf extends Controller {
	
	function Estadosf(){
		parent::Controller();
		$this->load->library("rapyd");
		//$this->datasis->modulo_id(606,1);
	}

	function index() {		
		$this->rapyd->load("datagrid","dataform");

		$data['extras']="";
		$data['content'] ='en construccion';
		$data["head"]    = script("jquery.pack.js").$this->rapyd->get_head();
		$data['title']   ='Estados Finacieros';
		$this->load->view('view_ventanas', $data);
	}

	function crear(){
		$this->rapyd->load("datagrid","dataform");
		$link=anchor('','+');
		
		$add='Descripci&oacute;n'.form_input('descrip', '').' Cantidad'.form_input('cant', '');
		
		$img1=image('list_plus.png','agregar',array('id'=>'aactlist'));
		$img2=image('list_plus.png','agregar',array('id'=>'apaslist'));
		$img3=image('list_plus.png','agregar',array('id'=>'acaplist'));
		
		$list = array(
			$img1.'<b>ACTIVOS</b><ul id="actlist"></ul>',
			$img2.'<b>PASIVOS</b><ul id="paslist"></ul>',
			$img3.'<b>CAPITAL</b><ul id="caplist"></ul>');

		$attributes = array('id'    => 'lbalances' );

	$data['script']="<script type='text/javascript'>
		function agregar(){
			$('#actlist').append('<strong>Hello</strong>');
		}

		$(document).ready(function(){
			$('#aactlist').click(function () { $('#actlist').append('<li>Descripci&oacute;n<input type=\"text\" name=\"descrip\" value=\"\"  /> Cantidad<input type=\"text\" name=\"cant\" value=\"\"  /></li>'); });
			$('#apaslist').click(function () { $('#paslist').append('<li>Descripci&oacute;n<input type=\"text\" name=\"descrip\" value=\"\"  /> Cantidad<input type=\"text\" name=\"cant\" value=\"\"  /></li>'); });
			$('#acaplist').click(function () { $('#caplist').append('<li>Descripci&oacute;n<input type=\"text\" name=\"descrip\" value=\"\"  /> Cantidad<input type=\"text\" name=\"cant\" value=\"\"  /></li>'); });
		});
		</script>";

		$data['extras']="";
		$data['content'] =form_open('email/send').ul($list, $attributes).form_close();
		$data["head"]    = script("jquery.pack.js").$this->rapyd->get_head();
		$data['title']   ='Estados Finacieros';
		$this->load->view('view_ventanas', $data);

  }
}
?>