<?php
class infor_pdf extends Controller {
		
		var $linea_encabezado;
		var $linea_pie;
		var $linea_encabezado2;
		var $linea_pie2;
    
    function infor_pdf()
    {
        parent::Controller();
    }

    function genera()
    {
        $this->load->library('pdf');
				
				$this->encab();
				$this->pdf->ln(2);
				
				$pos = $this->pdf->getY();
				$this->linea_encabezado= $pos;
				$tamano = $this->pdf->getPageHeight();
				$tam1 = $this->pdf->getHeaderMargin();
				$tam2 = $this->pdf->getFooterMargin();
				$this->linea_pie = $tamano-$tam2-25;
				$this->linea_pie2 = $tamano-$tam2-5;
				
				
				
				//$enc->setY($pos);
				
				$this->cuerpo();
				
			
        
        $this->pdf->Output('example_001.pdf', 'I');        
    }
    
    function  index(){
			redirect("ingresos/infor_pdf/genera");
		}
		
		function cuerpo(){
			$j=1;
			$this->pdf->setY($this->linea_encabezado);	
			for($i=0;$i<=200;$i++){	
				$pos = $this->pdf->getY();
				if($pos >=($this->linea_pie)){
					if($j==1){
						$this->pie();
						$j++;
					}else{
						$pie2=$this->piem();	
					}
					$this->pdf->AddPage('','LETTER');
					$this->encab2();
					$this->pdf->ln(2);
					//$pos = $enc2->getY();
					$this->linea_pie = $this->linea_pie2;
					
					//$this->pdf->setY($pos);
					
				}
				$this->pdf->Cell(0,4,'MArgenes:'.$this->pdf->getMargins().'     /    Tamaño de pag.:'.$this->pdf->getPageHeight().'     /    Tamano de pie de pagina'.$this->pdf->getFooterMargin(),1,1,'C');
			}
				
			
			//return $this->pdf;
		}
		
		function piem(){
			$this->pdf->setY($this->linea_pie2);
			
			
			
			$this->pdf->Cell(62, 5, 'NOMBRE', 'LBT', 0, 'C');
			$this->pdf->Cell(62, 5, 'CEDULA', 'LRBT', 0, 'C');
			$this->pdf->Cell(62, 5, 'FECHA', 'RBT', 1, 'C');
			
			//return $this->pdf;	
		}
		
		function pie(){
			$this->pdf->setY($this->linea_pie);
			$this->pdf->Cell(186, 5, 'RECIBI CONFORME', 1, 1, 'C');
			
			$this->pdf->Cell(62, 5, '', 'LT', 0, 'C');
			$this->pdf->Cell(62, 5, '', 'LRT', 0, 'C');
			$this->pdf->Cell(62, 5, '', 'RT', 1, 'C');
			$this->pdf->Cell(62, 5, 'NOMBRE', 'LB', 0, 'C');
			$this->pdf->Cell(62, 5, 'CEDULA', 'LRB', 0, 'C');
			$this->pdf->Cell(62, 5, 'FECHA', 'RB', 1, 'C');
			
			//return $this->pdf;	
		}
		
		function encab(){
			
			$this->pdf->SetCreator('Judelvis Rivas');
			$this->pdf->SetAuthor('Judelvis');
			$this->pdf->SetTitle('Prueba1');
			$this->pdf->SetSubject('Para preba');
			$this->pdf->SetKeywords('TCPDF, PDF, example, test, guide');
			//echo FCPATH.'image/';
			$this->pdf->setHeaderFont(Array('helvetica', '', 6));
			$titulo1 = $this->datasis->traevalor('TITULO1');
			$titulo2 = $this->datasis->traevalor('TITULO2');
			$titulo3 = $this->datasis->traevalor('TITULO3');
			$titulo4 = $this->datasis->traevalor('RIF');
			$pie     = $this->datasis->traevalor('SISTEMA');
			//$this->pdf->setFooter("epale");
			$this->pdf->SetHeaderData('logo.jpg',10,$titulo1,$titulo2."\n".$titulo3."\n".$titulo4);
			$this->pdf->SetFont('times', 'B', 10);

			// add a page
			$this->pdf->AddPage('','LETTER');
			$this->pdf->SetFont('times', '', 8);
				
			$this->pdf->Cell(124, 4, 'Nombre o Razon Social: ', 0, 0, 'L');
			
			$this->pdf->Cell(60, 4, 'R.I.F. Proveedor: ', 0, 1, 'L');
			
			
					
			$this->pdf->MultiCell(0, 4, 'Direccion: ', 0, 'L', 0,1);
			
			//186Cell($w, $h=0, $txt='', $border=0, $ln=0, $align='', $fill=0, $link='', $stretch=0, $ignore_min_height=false, $calign='T', $valign='M')	
			// MultiCell($w, $h, $txt, $border=0, $align='J', $fill=0, $ln=1, $x='', $y='', $reseth=true, $stretch=0, $ishtml=false, $autopadding=true, $maxh=0)
			
			$aux1 = $this->pdf->getX();
			$aux2 = $this->pdf->getY();
			
			$this->pdf->MultiCell(124, 4, 'Unidad Solicitante:'  , 0, 'L',0,2,'','');
			
			$aux4 = $this->pdf->getY();
			$this->pdf->MultiCell(124 , 4, 'Unidad Contratante: ' , 0, 'L', 0, 2,15,'');
			
			$this->pdf->ln(0);
			$this->pdf->Cell(124, 4, 'Actividad: ',0, 0,'L');
			$this->pdf->Cell(62 , 4, 'Fondo: ', 0, 1, 'L');
			
			$this->pdf->MultiCell(0 , 4, 'Lugar de entraga: ' , 0,'L',0,1);
			
			$this->pdf->MultiCell(0, 4, 'Concepto: ', 0,'L', 0, 1);
			//$pos = $this->pdf->getY();
			//$this->pdf->ln(8);
			
			//$this->pdf->ln(8);
			$aux3=$this->pdf->getY();
			$this->pdf->setXY($aux1+124,$aux2);
			$this->pdf->Cell(60, 4, 'Cod. Proveedor: ', 0, 0, 'L');
			$this->pdf->setXY($aux1+124,$aux4);
			$this->pdf->Cell(60, 4, 'N.Interno: ', 0, 0, 'L');
			$this->pdf->setY($aux3);
			//return $this->pdf;
		}
		
		function encab2(){
			
			
			$this->pdf->SetFont('times', '', 8);
				
			//186Cell($w, $h=0, $txt='', $border=0, $ln=0, $align='', $fill=0, $link='', $stretch=0, $ignore_min_height=false, $calign='T', $valign='M')	
			// MultiCell($w, $h, $txt, $border=0, $align='J', $fill=0, $ln=1, $x='', $y='', $reseth=true, $stretch=0, $ishtml=false, $autopadding=true, $maxh=0)
			
			$aux1 = $this->pdf->getX();
			$aux2 = $this->pdf->getY();
			
			$this->pdf->MultiCell(124, 4, 'Unidad Solicitante:'  , 0, 'L',0,2,'','');
			
			$aux4 = $this->pdf->getY();
			$this->pdf->MultiCell(124 , 4, 'Unidad Contratante: ' , 0, 'L', 0, 2,15,'');
			
			$this->pdf->ln(0);
			$this->pdf->Cell(124, 4, 'Actividad: ',0, 0,'L');
			$this->pdf->Cell(62 , 4, 'Fondo: ', 0, 1, 'L');
			
			$this->pdf->MultiCell(0 , 4, 'Lugar de entraga: ' , 0,'L',0,1);
			
			$this->pdf->MultiCell(0, 4, 'Concepto: ', 0,'L', 0, 1);
			//$pos = $this->pdf->getY();
			//$this->pdf->ln(8);
			
			//$this->pdf->ln(8);
			$aux3=$this->pdf->getY();
			$this->pdf->setXY($aux1+124,$aux2);
			$this->pdf->Cell(60, 4, 'Cod. Proveedor: ', 0, 0, 'L');
			$this->pdf->setXY($aux1+124,$aux4);
			$this->pdf->Cell(60, 4, 'N.Interno: ', 0, 0, 'L');
			$this->pdf->setY($aux3);
			//return $this->pdf;
		}
		

}
?>