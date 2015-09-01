<?php
class selpres extends Controller {
	
	function selpres(){
		parent::Controller();
		$this->load->library("rapyd");
		$this->load->library('session');
	}
	
	function  index(){
		redirect("presupuesto/selpres/sel");
	}
	
	function sel(){
		$this->rapyd->load("dataform");
		
		$tipo=$this->input->post('tipo');
		
		$form = new dataForm('presupuesto/selpres/sel');
		
		if($tipo!==false){
			$this->session->set_userdata(array("tipo"=>$tipo));
		}
		$form->pres = new freeField("Presupuesto Activo","tipò",$this->session->userdata('tipo'));
		
		$form->tipo =new dropdownField('Presupuesto','tipo');
		$form->tipo->options("SELECT tipo,tipo as valor FROM tipopres ORDER BY tipo");
		
		$form->submit("btnsubmit","Seleccionar");
		
		$form->build_form();
		
		$data['content'] = $form->output;
		$data['title']   = " Presupuesto Activo ";
		$data["head"]    = $this->rapyd->get_head();
		$this->load->view('view_ventanas', $data);  
	}
}