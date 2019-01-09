<?php
class Linventario extends Controller {

	function Linventario() {
		parent::Controller();
		$this->load->library("rapyd");
	}
 
	function index() {
		$this->load->library('PDFReporte');
		$this->rapyd->load("datafilter");
		$mSQL= "SELECT proteo FROM reportes WHERE nombre='SINVENT'";
		$mc  = $this->datasis->dameval($mSQL);
		eval($mc);

	}
}
?>
