<?php
//define('FPDF_FONTPATH','font/');
require('Mtable.php');
class Mmtable extends Mtable {
	/*
	function Mmtable(){
		$this->Open();   
		$this->AddPage();
	}*/
	function Header(){
		//Titulo
		if (!empty($this->Logo))
		$this->image($this->Logo,12,7,30);
		$this->SetFont('Arial','',18);
		$this->Cell(0,6,$this->Titulo ,0,1,'C');
		$this->Ln(10);
		//Ensure table header is output
		parent::Header();
	}
	function Footer(){
		//Pie de Pagina
		$this->Ln();
		$this->SetFont('Arial','B',6);
		$this->Cell(0,6,'ProteERP','T',1,'C');
		$this->Ln();
		//Ensure table header is output
		parent::Footer();
	}
}
?>