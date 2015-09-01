<?php
require(APPPATH.'config/tcpdf'.EXT);
require_once('pdf.php');
class infor_pdf extends pdf{


	var $datos_pdf;
	var $narchivo;

	function generar()
	{
	}
	function imprime($o){
		$o->Output($this->narchivo, 'I');
	}
		
	function cuerpo(){
	
	}

	function piem(){
	
	}

	function pie(){
	
	}

	function encab($encab){
			
		$this->setPage(1,true);
		$encab;

			
	}

	function setCuerpo($c){
		$this->cuerpo = $c;
	}


	function setEncabezadom($enc){
		$this->encabezadom = $enc;
	}

	function setEncabezadof($enc){
		$this->encabezadof = $enc;
	}

	function setEncabezado($enc){
		$this->encabezado = $enc;
	}

	function setPiem($pie){
		$this->piem = $pie;
	}

	function setPief($pie){
		$this->pief = $pie;
	}

	function setPie($pie){
		$this->pie = $pie;
	}

}
?>