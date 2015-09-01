<?php
class Lpersonal extends Controller {

	function Lpersonal() {
		parent::Controller();
	}

	function index() {
		$this->load->library('PDFReporte');

		$pdf = new PDFReporte();
		$pdf->setHeadValores('TITULO1');
		$pdf->setSubHeadValores(array('TITULO2','TITULO3'));
		$pdf->setTitulo("Listado de Bancos cambio");
		$pdf->setSubTitulo('Sub-titulo');
		$pdf->AddPage();
		$pdf->setTableTitu(8,'Times');
		$pdf->AddCol('codbanc'  ,10,'Cod.'     ,'C',4);
		$pdf->AddCol('numcuent' ,20,'N.Cuenta', 'C',4);
		$pdf->AddCol('tbanco'   ,20,'C.Banco'  ,'C',4);
		$pdf->AddCol('direccion',90,'Direccion','L',4);
		$pdf->AddCol('banco'    ,20,'Banco'    ,'L',4);
		$pdf->AddCol('saldo'    ,20,'Saldo'    ,'R',4);
		$pdf->totalizar=array('saldo');
		$pdf->Table("SELECT codbanc,numcuent,tbanco,CONCAT_WS(' ',dire1,dire2) direccion, banco, saldo FROM banc");
		$pdf->Output();
		
		//$obj->totalizar=array('saldo'=>'tbanco');            
		//array('CAJ'=>'Caja','BAN'=>'Banco','COR'='Cortizona')
	} 
}

?>
