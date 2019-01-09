<?php

class Gcontab extends Controller {
	
	var $titp  = 'Balance General';      
	var $tits  = 'Balance General';    
	
	function Gcontab(){

		parent::Controller();
		$this->load->library("rapyd");
		$this->datasis->modulo_id(187);
	}
	
	function index() {
		$this->rapyd->load("dataform");

		$filter = new DataForm("contabilidad/gcontab/index/process");
	
		$filter->fecha = new dateonlyField("Fecha", "fecha");
		$filter->fecha->size=12;
		
		$filter->submit("btnsubmit","Ver balance General");	

		$filter->build_form();
		
		if($filter->on_success()){
		
			$fecha = $filter->fecha->newValue;
			redirect("formatos/ver/BALANCE/$fecha");
		}
		
		$salida = anchor('contabilidad/gcontab/genera','Generar Contabilidad'); 
				
		$data['content'] = $filter->output.$salida;
		$data['title']   = "$this->titp";
		$data["head"]    = $this->rapyd->get_head();
		$this->load->view('view_ventanas', $data);
	}
	
	function genera(){
		$this->db->query("call sp_contab()");
		redirect('contabilidad/gcontab');
	}
}
?>