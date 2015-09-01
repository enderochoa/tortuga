<?php
class informe_pdf extends Controller {
	 
	 function informe_pdf()
    {
        parent::Controller();
    }
    
   function  index(){
			redirect("ingresos/informe_pdf/generar");
		} 
		
		function inicializa(){
			//$this->load->library('pdf');
			
			$this->infor_pdf->SetCreator('Judelvis Rivas');
			$this->infor_pdf->SetAuthor('Judelvis');
			$this->infor_pdf->SetTitle('Prueba1');
			$this->infor_pdf->SetSubject('Para preba');
			$this->infor_pdf->SetKeywords('TCPDF, PDF, example, test, guide');
			$this->infor_pdf->setHeaderFont(Array('helvetica', '', 6));
			$titulo1 = $this->datasis->traevalor('TITULO1');
			$titulo2 = $this->datasis->traevalor('TITULO2');
			$titulo3 = $this->datasis->traevalor('TITULO3');
			$titulo4 = $this->datasis->traevalor('RIF');
			$pie     = $this->datasis->traevalor('SISTEMA');
			$this->infor_pdf->SetHeaderData('logo.jpg',10,$titulo1,$titulo2."\n".$titulo3."\n".$titulo4);
			$this->infor_pdf->SetFont('times', 'B', 10);

			// add a page
			$this->infor_pdf->AddPage('','LETTER');
			$tamano = $this->infor_pdf->getPageHeight();
			$tam1 = $this->infor_pdf->getHeaderMargin();
			$tam2 = $this->infor_pdf->getFooterMargin();
			$this->infor_pdf->linea_pie = $tamano-$tam2-15;
			$this->infor_pdf->linea_pie2 = $tamano-$tam2-5;
			
			
		}
		
		function genera()
    {		//$this->load->library('pdf');
    		$this->load->library('infor_pdf');
    		$this->inicializa();
    		$this->infor_pdf->genera_reporte($this->encab(),$this->cuerpo(),$this->pie());
    }
    
    function generar(){
    	$this->load->library('infor_pdf');	
    	$this->infor_pdf->SetCreator('Judelvis Rivas');
			$this->infor_pdf->SetAuthor('Judelvis');
			$this->infor_pdf->SetTitle('Prueba1');
			$this->infor_pdf->SetSubject('Para preba');
			$this->infor_pdf->SetKeywords('TCPDF, PDF, example, test, guide');
			$this->infor_pdf->setHeaderFont(Array('helvetica', '', 6));
			$titulo1 = $this->datasis->traevalor('TITULO1');
			$titulo2 = $this->datasis->traevalor('TITULO2');
			$titulo3 = $this->datasis->traevalor('TITULO3');
			$titulo4 = $this->datasis->traevalor('RIF');
			$pie     = $this->datasis->traevalor('SISTEMA');
			$this->infor_pdf->SetHeaderData('logo.jpg',10,$titulo1,$titulo2."\n".$titulo3."\n".$titulo4);
			$this->infor_pdf->SetFont('times', 'B', 10);

			// add a page
			$this->infor_pdf->AddPage('','LETTER');
			$tamano = $this->infor_pdf->getPageHeight();
			$tam1 = $this->infor_pdf->getHeaderMargin();
			$tam2 = $this->infor_pdf->getFooterMargin();
			$this->infor_pdf->linea_pie = $tamano-$tam2-15;
			$this->infor_pdf->linea_pie2 = $tamano-$tam2-5;
			$this->infor_pdf->setEncabezado($this->encab());
			$this->infor_pdf->setCuerpo($this->cuerpo());
			//$this->infor_pdf->setPie($this->pie());
			$this->infor_pdf->generar();
    }
    
    function cuerpo(){
			$j=1;
			$this->infor_pdf->setY($this->infor_pdf->linea_encabezado);	
			for($i=0;$i<=200;$i++){	
				$pos = $this->infor_pdf->getY();
				if($pos >=($this->infor_pdf->linea_pie)){
					if($j==1){
						$this->infor_pdf->setPie($this->pie());
						$j++;
					}else{
						$pie2=$this->infor_pdf->setPiem($this->piem());	
					}
					$this->infor_pdf->AddPage('','LETTER');
					$this->infor_pdf->setEncabezadom($this->encab2());
					$this->infor_pdf->ln(2);
					//$pos = $enc2->getY();
					$this->infor_pdf->linea_pie = $this->infor_pdf->linea_pie2;
					
					//$this->pdf->setY($pos);
					
				}
				$this->infor_pdf->Cell(0,4,$i.'MArgenes:'.$this->infor_pdf->getMargins().'     /    Tamaño de pag.:'.$this->infor_pdf->getPageHeight().'     /    Tamano de pie de pagina'.$this->infor_pdf->getFooterMargin(),1,1,'C');
			}
				
			
			//return $this->infor_pdf;
		}
		
		function piem(){
			$this->infor_pdf->setY($this->infor_pdf->linea_pie2);
			
			
			
			$this->infor_pdf->Cell(62, 5, 'NOMBRE', 'LBT', 0, 'C');
			$this->infor_pdf->Cell(62, 5, 'CEDULA', 'LRBT', 0, 'C');
			$this->infor_pdf->Cell(62, 5, 'FECHA', 'RBT', 1, 'C');
			
			return $this->infor_pdf;	
		}
		
		function pie(){
			//$this->load->library('pdf');
			$this->infor_pdf->setY($this->infor_pdf->linea_pie);
			//$this->infor_pdf->addPage();
			$this->infor_pdf->Cell(186, 5, 'RECIBI CONFORME', 1, 1, 'C');
			
			$this->infor_pdf->Cell(62, 5, '', 'LT', 0, 'C');
			$this->infor_pdf->Cell(62, 5, '', 'LRT', 0, 'C');
			$this->infor_pdf->Cell(62, 5, '', 'RT', 1, 'C');
			$this->infor_pdf->Cell(62, 5, 'NOMBRE', 'LB', 0, 'C');
			$this->infor_pdf->Cell(62, 5, 'CEDULA', 'LRB', 0, 'C');
			$this->infor_pdf->Cell(62, 5, 'FECHA', 'RB', 1, 'C');
			
			return $this->infor_pdf;	
		}
		
		function encab(){
			
			
			//$this->infor_pdf->AddPage('','LETTER');
			$this->infor_pdf->SetFont('times', '', 8);
				
			$this->infor_pdf->Cell(124, 4, 'Nombre o Razon Social: ', 0, 0, 'L');
			
			$this->infor_pdf->Cell(60, 4, 'R.I.F. Proveedor: ', 0, 1, 'L');

			$this->infor_pdf->MultiCell(0, 4, 'Direccion: ', 0, 'L', 0,1);
			
			//186Cell($w, $h=0, $txt='', $border=0, $ln=0, $align='', $fill=0, $link='', $stretch=0, $ignore_min_height=false, $calign='T', $valign='M')	
			// MultiCell($w, $h, $txt, $border=0, $align='J', $fill=0, $ln=1, $x='', $y='', $reseth=true, $stretch=0, $ishtml=false, $autopadding=true, $maxh=0)
			
			$aux1 = $this->infor_pdf->getX();
			$aux2 = $this->infor_pdf->getY();
			
			$this->infor_pdf->MultiCell(124, 4, 'Unidad Solicitante:'  , 0, 'L',0,2,'','');
			
			$aux4 = $this->infor_pdf->getY();
			$this->infor_pdf->MultiCell(124 , 4, 'Unidad Contratante: ' , 0, 'L', 0, 2,15,'');
			
			$this->infor_pdf->ln(0);
			$this->infor_pdf->Cell(124, 4, 'Actividad: ',0, 0,'L');
			$this->infor_pdf->Cell(62 , 4, 'Fondo: ', 0, 1, 'L');
			
			$this->infor_pdf->MultiCell(0 , 4, 'Lugar de entraga: ' , 0,'L',0,1);
			
			$this->infor_pdf->MultiCell(0, 4, 'Concepto: ', 0,'L', 0, 1);
			
			$aux3=$this->infor_pdf->getY();
			$this->infor_pdf->setXY($aux1+124,$aux2);
			$this->infor_pdf->Cell(60, 4, 'Cod. Proveedor: ', 0, 0, 'L');
			$this->infor_pdf->setXY($aux1+124,$aux4);
			$this->infor_pdf->Cell(60, 4, 'N.Interno: ', 0, 0, 'L');
			$this->infor_pdf->setY($aux3);
			
			$this->infor_pdf->linea_encabezado = $this->infor_pdf->getY();
			//return $this->infor_pdf;
		}
    function encab2(){
			
			$this->infor_pdf->SetFont('times', '', 8);
				
			//186Cell($w, $h=0, $txt='', $border=0, $ln=0, $align='', $fill=0, $link='', $stretch=0, $ignore_min_height=false, $calign='T', $valign='M')	
			// MultiCell($w, $h, $txt, $border=0, $align='J', $fill=0, $ln=1, $x='', $y='', $reseth=true, $stretch=0, $ishtml=false, $autopadding=true, $maxh=0)
			
			$aux1 = $this->infor_pdf->getX();
			$aux2 = $this->infor_pdf->getY();
			
			$this->infor_pdf->MultiCell(124, 4, 'Unidad Solicitante:'  , 0, 'L',0,2,'','');
			
			$aux4 = $this->infor_pdf->getY();
			$this->infor_pdf->MultiCell(124 , 4, 'Unidad Contratante: ' , 0, 'L', 0, 2,15,'');
			
			$this->infor_pdf->ln(0);
			$this->infor_pdf->Cell(124, 4, 'Actividad: ',0, 0,'L');
			$this->infor_pdf->Cell(62 , 4, 'Fondo: ', 0, 1, 'L');
			
			$this->infor_pdf->MultiCell(0 , 4, 'Lugar de entraga: ' , 0,'L',0,1);
			
			$this->infor_pdf->MultiCell(0, 4, 'Concepto: ', 0,'L', 0, 1);
			//$pos = $this->pdf->getY();
			//$this->pdf->ln(8);
			
			//$this->pdf->ln(8);
			$aux3=$this->infor_pdf->getY();
			$this->infor_pdf->setXY($aux1+124,$aux2);
			$this->infor_pdf->Cell(60, 4, 'Cod. Proveedor: ', 0, 0, 'L');
			$this->infor_pdf->setXY($aux1+124,$aux4);
			$this->infor_pdf->Cell(60, 4, 'N.Interno: ', 0, 0, 'L');
			$this->infor_pdf->setY($aux3);
			return $this->infor_pdf;
		}
    
}
?>