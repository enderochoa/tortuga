<?php
class listadepreciosdetal extends Controller {

	function listadepreciosdetal() {
		parent::Controller();
	}
 	function index() {
		$this->load->library('PDFReporte');
		//$this->load->view('table_def.inc');
		
		$pdf = new PDFReporte();
		$pdf->setTitulo("Listado de Precios Detal",8);
		$pdf->AddPage();
		$pdf->AddCol('codigo'  ,20,'Código'     ,'L',3);
		$pdf->AddCol('grupo'   ,10,'Grupo'      ,'L',3);
		$pdf->AddCol('descrip' ,50,'Descripción','L',3);
		$pdf->AddCol('tipo'    ,10,'Tipo'       ,'R',3);
		$pdf->AddCol('unidad'  ,10,'Unidad'     ,'R',3);
		$pdf->AddCol('peso'    ,10,'Peso'       ,'R',3);
		$pdf->AddCol('prepro1' ,15,'Precio'     ,'R',3);
		$pdf->AddCol('marca'   ,15,'Marca'      ,'R',3);	
		$pdf->Table("SELECT codigo,grupo,descrip,tipo,unidad,peso,prepro1,marca FROM sinv order by codigo" );
		$pdf->Output();
	}
}
 ?>
