<?php

require('Fpdf.php');

class Lpdf extends Fpdf
{
	
	var $caja ;
	var $fecha ;

function Lpdf($param)
{
	$this->Fpdf();
	$this->fecha = $param['fecha'];
	$this->caja = $param['caja'];
}

//Cabecera de pgina
function Header()
{
	$CI = & get_instance();
	//Logo
	$this->Image($_SERVER['DOCUMENT_ROOT'].base_url().'images/logotipo.jpg',10,8,33);

	//Arial bold 15
	$this->SetFont('Arial','B',15);
	//Movernos a la derecha
	$sucu = "PRINCIPAL_";
	if ( substr($this->caja,0,1) == '0') $sucu = "PRINCIPAL";
	if ( substr($this->caja,0,1) == '1') $sucu = "SUCURSAL 1";
	if ( substr($this->caja,0,1) == '2') $sucu = "SUCURSAL 2";
	if ( substr($this->caja,0,1) == '3') $sucu = "SUCURSAL 3";

	$fecha = substr($this->fecha,6,2). "/". substr($this->fecha,4,2). "/". substr($this->fecha,0,4);

	$this->SetFont('Arial','B',15);
	$this->Cell(0,10,"Ventas por Caja en $sucu",0,0,'C');
	$this->Ln(7);

	$this->Cell(0,10," Caja ".$this->caja."  Fecha ".$fecha,0,0,'C');
	$this->Ln(5);

	$this->SetFont('Arial','B',8);
	$this->Cell(0,5,'     RIF:'.$CI->datasis->traevalor('RIF'),0,0,'L');
	//$this->Ln(5);

	//Salto de lnea
	$this->SetFillColor(230,230,230);
	$this->Ln(10);
	$this->SetFont('Arial','B',10);
	$this->cell(15,5,"Numero", "BT", 0, 'C',1);
	$this->cell(18,5,"Ced/RIF", "BT", 0, 'C',1);
	$this->cell(45,5,"Nombre", "BT", 0, 'C',1);
	$this->cell(24,5,"Exento", "BT", 0, 'C',1);
	$this->cell(24,5,"Base","BT", 0, 'C',1);
	$this->cell(23,5,"Impuesto", "BT", 0, 'C',1);
	$this->cell(20,5,"Ret. IVA", "BT", 0, 'C',1);
	$this->cell(24,5,"Total","BT", 0, 'C',1);
	$this->ln(6);

}

//Pie de pgina
function Footer()
{
	//Posicin: a 1,5 cm del final
	$this->SetY(-15);
	//Arial italic 8
	$this->SetFont('Arial','I',8);
	//Nmero de pgina
	$this->Cell(0,10,'Pagina '.$this->PageNo().'/{nb}          Fecha de emision '.date('d/m/Y  h:ia'),0,0,'C');
}
}
?>