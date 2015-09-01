<?php
class pdf_test extends Controller {

    function pdf_test()
    {
        parent::Controller();
    }

    function tcpdf()
    {
        $this->load->library('pdf');

        // set document information
        $this->pdf->SetSubject('TCPDF Tutorial');
        $this->pdf->SetKeywords('TCPDF, PDF, example, test, guide');
        $this->pdf->setHeaderFont(Array('helvetica', '', 6));
			$titulo1 = $this->datasis->traevalor('TITULO1');
			$titulo2 = $this->datasis->traevalor('TITULO2');
			$titulo3 = $this->datasis->traevalor('TITULO3');
			$titulo4 = $this->datasis->traevalor('RIF');
			$pie     = $this->datasis->traevalor('SISTEMA');
			//$this->pdf->setFooter("epale");
			$this->pdf->SetHeaderData('logo.jpg',10,$titulo1,$titulo2."\n".$titulo3."\n".$titulo4);
        
        // set font
        $this->pdf->SetFont('times', 'BI', 16);
        
        // add a page
        $this->pdf->AddPage();
        
        // print a line using Cell()
        $this->pdf->Cell(0, 12, 'Example 001 - €� èéìòù', 1, 1, 'C');
        
        //Close and output PDF document
        $this->pdf->Output('example_001.pdf', 'I');        
    }
    function  index(){
			redirect("ingresos/pdf_test/prueba");
		}
		
		function prueba(){
			
			//consulta de datos
			
			//if(count($parametros)==0)
 				//show_error('Faltan parametros ');
			$numero=43;
			$mSQL_1 = $this->db->query("SELECT  lentrega,usolicita,username, creten,reten,reteiva,exento,tipo,status,fecha,total2,uejecutora,estadmin,fondo,cod_prov,beneficiario,subtotal,ivag,ivar,ivaa,total,observa,reverso FROM ocompra WHERE numero='$numero' ");
			$mSQL_2 = $this->db->query("SELECT ordinal,descripcion,unidad,cantidad,precio,importe,iva,partida FROM itocompra WHERE numero='$numero'");
			$row = $mSQL_1->row();

			$fecha       =$row->fecha;
			$usolicita  =$row->usolicita;
			$lentrega  =$row->lentrega;
			$uejecutora  =$row->uejecutora;
			$estadmin    =$row->estadmin;
			$username    =$row->username;
			$total2    =$row->total2;
			$fondo       =$row->fondo;
			$cod_prov    =$row->cod_prov;
			$beneficiario=$row->beneficiario;
			$subtotal    =$row->subtotal;
			$ivag        =$row->ivag;
			$ivar        =$row->ivar;
			$ivaa        =$row->ivaa;
			$total       =$row->total;
			$observa     =$row->observa;
			$tipo        =$row->tipo;
			$status      =$row->status;
			$creten      =$row->creten;
			$reten       =$row->reten; 
			$reteiva     =$row->reteiva;
			$exento      =$row->exento;
			$reverso      =$row->reverso;
			
			$uejecutora2=$this->datasis->dameval("SELECT nombre FROM uejecutora WHERE codigo='$uejecutora'");
			$usolicita2    =$this->datasis->dameval("SELECT nombre FROM uejecutora WHERE codigo='$usolicita'");
			$fondo      =$this->datasis->dameval("SELECT descrip FROM fondo WHERE fondo='$fondo'");
			$estadmin2  =$this->datasis->dameval("SELECT denominacion FROM estruadm WHERE codigo='$estadmin'");
			$pr         =$this->datasis->damerow("SELECT nombre,CONCAT_WS(' ',direc1,direc2) direccion,rif FROM sprv WHERE proveed='$cod_prov'");
			
			$creten2    =$this->datasis->dameval("SELECT activida FROM rete WHERE codigo='$creten'");
			
			$detalle =$mSQL_2->result();
			$query = "SELECT  estadmin, fondo,partida,ordinal,sum(monto) as monto FROM (
			SELECT b.estadmin, b.fondo,a.partida,a.ordinal,
			sum(a.importe) as monto
			FROM itocompra a JOIN ocompra b ON a.numero=b.numero 
			WHERE b.status NOT IN ('P','A','M') AND b.numero = $numero
			GROUP BY b.estadmin, b.fondo ,a.partida,ordinal
			UNION ALL
			SELECT b.estadmin, b.fondo,a.partida,a.ordinal,
			sum(a.importe*a.iva/100) as monto
			FROM itocompra a JOIN ocompra b ON a.numero=b.numero 
			WHERE b.status NOT IN ('P','A','M') AND b.numero = $numero AND (SELECT asignacion FROM presupuesto d WHERE d.codigoadm=b.estadmin AND d.tipo=b.fondo AND d.codigopres=(SELECT valor FROM valores WHERE nombre='PARTIDAIVA') )=0
			GROUP BY b.estadmin, b.fondo ,a.partida,ordinal
			UNION ALL
			SELECT b.estadmin, b.fondo, (SELECT valor FROM valores WHERE nombre='PARTIDAIVA') partida,a.ordinal,
			sum(a.importe*a.iva/100) as monto
			FROM itocompra a JOIN ocompra b ON a.numero=b.numero 
			WHERE b.status NOT IN ('P','A','M') AND b.numero = $numero AND (SELECT asignacion FROM presupuesto d WHERE d.codigoadm=b.estadmin AND d.tipo=b.fondo AND d.codigopres=(SELECT valor FROM valores WHERE nombre='PARTIDAIVA') )>0 
			GROUP BY b.estadmin, b.fondo,a.partida ,ordinal)a GROUP BY estadmin, fondo,partida ,ordinal";
			
			$mSQL_3 = $this->db->query($query);

			$detalle2 =$mSQL_3->result();
			
			// fin consulta
			
			$this->load->library('pdf');
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
			
			
			
			
			$est = "";
			if($status=="P")
				$est = "Sin Comprometer";
			elseif($status=="C")
				$est = "Comprometido";
			elseif($status=="A")
				$est = "Anulado";
			elseif($status=="O")
				$est = "Ordenado Pago";
			elseif($status=="E")
				$est = "Pagado";
			elseif($status=="T")
				$est = "Causado";
			elseif($status=="X")
				$est = "Reversado";
			elseif($status=="M")
				$est = "Orden NO terminada";
			
			
			$this->pdf->SetFont('times', '', 8);
				
			$this->pdf->Cell(124, 4, 'Nombre o Razon Social: '.$pr["nombre"], 0, 0, 'L');
			$re=(empty($reverso))? '': str_pad($reverso,8,'0',STR_PAD_LEFT);
			$this->pdf->Cell(60, 4, 'R.I.F. Proveedor: '.$pr['rif'], 0, 1, 'L');
			
			$this->enc_der($tipo,$numero,$fecha,$re);
					
			$this->pdf->MultiCell(0, 4, 'Direccion: '.$pr["direccion"], 0, 'L', 0,1);
			
			//186Cell($w, $h=0, $txt='', $border=0, $ln=0, $align='', $fill=0, $link='', $stretch=0, $ignore_min_height=false, $calign='T', $valign='M')	
			// MultiCell($w, $h, $txt, $border=0, $align='J', $fill=0, $ln=1, $x='', $y='', $reseth=true, $stretch=0, $ishtml=false, $autopadding=true, $maxh=0)
			
			$aux1 = $this->pdf->getX();
			$aux2 = $this->pdf->getY();
			
			$this->pdf->MultiCell(124, 4, 'Unidad Solicitante:'.$usolicita2  , 0, 'L',0,2,'','');
			
			$aux4 = $this->pdf->getY();
			$this->pdf->MultiCell(124 , 4, 'Unidad Contratante: '.$uejecutora2 , 0, 'L', 0, 2,15,'');
			
			$this->pdf->ln(0);
			$this->pdf->Cell(124, 4, 'Actividad: '.$estadmin.' '.$estadmin2,0, 0,'L');
			$this->pdf->Cell(62 , 4, 'Fondo: '.$fondo, 0, 1, 'L');
			
			$this->pdf->MultiCell(0 , 4, 'Lugar de entraga: '.$lentrega , 0,'L',0,1);
			
			$this->pdf->MultiCell(0, 4, 'Concepto: '.$observa, 0,'L', 0, 1);
			
			$this->pdf->ln(8);
			$aux3=$this->pdf->getY();
			$this->pdf->setXY($aux1+124,$aux2);
			$this->pdf->Cell(60, 4, 'Cod. Proveedor: ', 0, 0, 'L');
			$this->pdf->setXY($aux1+124,$aux4);
			$this->pdf->Cell(60, 4, 'N.Interno: ', 0, 0, 'L');
			
			$this->pdf->setY($aux3);
			$this->pdf->SetFont('times', 'B', 8);
			$this->pdf->setFillColor(0,130,180);
			$this->pdf->Cell(20, 4, 'Partida ', 1, 0, 'C',1);
			$this->pdf->Cell(10, 4, 'Ord. ', 1, 0, 'C',1);
			$this->pdf->Cell(76, 4, 'Descripcion ', 1, 0, 'C',1);
			$this->pdf->Cell(20, 4, 'Unidad ', 1, 0, 'C',1);
			$this->pdf->Cell(15, 4, 'Cant. ', 1, 0, 'C',1);
			$this->pdf->Cell(15, 4, 'Precio ', 1, 0, 'C',1);
			$this->pdf->Cell(15, 4, 'IVA ', 1, 0, 'C',1);
			$this->pdf->Cell(15, 4, 'Importe ', 1, 1, 'C',1);
				
			//Construye la tabla
			$mod=FALSE;
			$i=0; 
			$sub=0;
			foreach ($detalle AS $items){ $i++;
				//Variables a mostrar	
	  	  //echo $items->partida;echo $items->ordinal;echo $items->descripcion;echo $items->unidad;echo $items->cantidad;echo $items->precio;echo $items->iva;echo $items->importe;
	  	  	$items->cantidad = number_format($items->cantidad, 2, ',', ' ');   	  
	  	    $items->precio = number_format($items->precio, 2, ',', ' ');
	  	    $items->iva = number_format($items->iva, 2, ',', ' ');
	  	    $items->importe = number_format($items->importe, 2, ',', ' ');    	   
	  	    $this->pdf->SetFont('times', '', 8);
					$this->pdf->Cell(20, 4, $items->partida, 'L', 0, 'L');
					$this->pdf->Cell(10, 4, $items->ordinal, 'LR', 0, 'C');
					$this->pdf->Cell(76, 4, $items->descripcion, 'R', 0, 'L');
					$this->pdf->Cell(20, 4, $items->unidad, 'R', 0, 'C');
					$this->pdf->Cell(15, 4, $items->cantidad,'R', 0, 'R');
					$this->pdf->Cell(15, 4, $items->precio, 'R', 0, 'R');
					$this->pdf->Cell(15, 4, $items->iva, 'R', 0, 'R');
					$this->pdf->Cell(15, 4, $items->importe, 'R', 1, 'R');
	  	}
			//Fin de tabla
			$this->pdf->SetFont('times', 'B', 10);
			$subtotal = number_format($subtotal, 2, ',', ' ');
			$this->pdf->Cell(156, 4,'Subtotal', 'T', 0, 'R');
			$this->pdf->Cell(30, 4,$subtotal, 'T', 1, 'R');
			$this->pdf->SetFont('times', '', 8);
				
			$a=number_format($reten, 2, ',', ' ');
			$ivar = number_format($ivar, 2, ',', ' ');
			$this->pdf->Cell(20, 4,'Codigo ISLR:', 0, 0, 'L');
			$this->pdf->Cell(76, 4,$creten.' '.$creten2, 0, 0, 'L');
			$this->pdf->Cell(30, 4,'Retencion de ISLR:', 0, 0, 'R');
			$this->pdf->Cell(15, 4, $a, 0, 0, 'R');
			$this->pdf->Cell(30, 4,'IVA tasa reducida:', 0, 0, 'R');
			$this->pdf->Cell(15, 4,$ivar, 0, 1, 'R');
				
			$a = number_format($exento, 2, ',', ' ');
			$ivag = number_format($ivag, 2, ',', ' ');
			$a = number_format($reteiva, 2, ',', ' ');
			$ivaa = number_format($ivaa, 2, ',', ' ');
			
			//$this->pdf->Cell(56, 4,'', 0, 0, 'R');
			$this->pdf->Cell(15, 4,'Exento:', 0, 0, 'L');
			$this->pdf->Cell(15, 4, $a, 0, 0, 'L');
			$this->pdf->Cell(51, 4,'Retencion de IVA:', 0, 0, 'R');
			$this->pdf->Cell(15, 4, $a, 0, 0, 'R');
			$this->pdf->Cell(30, 4,'IVA tasa aumentada:', 0, 0, 'R');
			$this->pdf->Cell(15, 4,$ivaa, 0, 0, 'R');
			$this->pdf->Cell(30, 4,'IVA tasa general:', 0, 0, 'R');
			$this->pdf->Cell(15, 4,$ivag, 0, 1, 'R');
				
			
			
			
			$this->pdf->SetFont('times', 'B', 12);
			$total2 = number_format($total2, 2, ',', ' ');
			$this->pdf->Cell(0, 5, 'MONTO TOTAL Bs.'.$total2, 1, 1, 'C', 1, '', 3);
			
			$this->pdf->SetFont('times', 'B', 14);
			$this->pdf->Cell(0, 4, 'RESUMEN DE PARTIDAS', 0, 1, 'C', 0, '', 3);
			
			$this->pdf->SetFont('times', 'B', 8);
			$this->pdf->Cell(30, 4, 'Est.Administrativa ', 1, 0, 'C',1);
			$this->pdf->Cell(20, 4, 'Fondo', 1, 0, 'C',1);
			$this->pdf->Cell(40, 4, 'Partida ', 1, 0, 'C',1);
			$this->pdf->Cell(15, 4, 'Ordinal', 1, 0, 'C',1);
			$this->pdf->Cell(61, 4, 'Denominacion', 1, 0, 'C',1);
			$this->pdf->Cell(20, 4, 'Monto', 1, 1, 'C',1);
				
				//
				
			$mod=FALSE; $i=0; $tot = 0;
    	foreach ($detalle2 AS $items){ $i++;
    	  		$tot+=1*$items->monto;
    	  		$ad=$items->estadmin;
	  	      $fn=$items->fondo;
	  	      $par=$items->partida;
	  	      $or=$items->ordinal;
	  	      $mt = number_format($items->monto, 2, ',', ' ');
	  	      if(!empty($items->ordinal)){
    	      		$de= $this->datasis->dameval("SELECT denominacion FROM ordinal WHERE codigoadm='".$items->estadmin."' AND fondo='".$items->fondo."' AND codigopres='".$items->partida."' AND ordinal='".$items->ordinal."'");
    	      		
								$this->pdf->Cell(30, 4, $ad, 1, 0, 'C');
								$this->pdf->Cell(20, 4, $fn, 1, 0, 'C');
								$this->pdf->Cell(40, 4, $par, 1, 0, 'C');
								$this->pdf->Cell(15, 4, $or, 1, 0, 'C');
								$this->pdf->Cell(61, 4, $de, 1, 0, 'L');
						}else{
    	      		$de= $this->datasis->dameval("SELECT denominacion FROM ppla WHERE codigo='".$items->partida."'");
    	      		
								$this->pdf->Cell(30, 4, $ad, 1, 0, 'C');
								$this->pdf->Cell(20, 4, $fn, 1, 0, 'C');
								$this->pdf->Cell(40, 4, $par, 1, 0, 'C');
								$this->pdf->Cell(15, 4, $or, 1, 0, 'C');
								$this->pdf->Cell(61, 4, $de, 1, 0, 'L');
								
    	      }
	  	     $this->pdf->Cell(20, 4, $mt, 1, 1, 'R');
	  	}
	  	$t1 =  number_format($tot, 2, ',', ' ');
	  	$this->pdf->Cell(0, 4, $t1, 0, 1, 'R');
	  	 
	  	$this->pdf->ln(8);
	  	 
	  	$linea = $this->pdf->getY();
	  	if($linea >230){
	  		$this->pdf->AddPage('','LETTER');
	  	}
	  	
	  	$user=$this->session->userdata('usuario');
			$user=$this->datasis->dameval("SELECT us_nombre FROM usuario WHERE us_codigo =".$this->db->escape($user));
    	$texto[0]="ELABORADO POR: ". $user;
  		$texto[1]="JEFE DE COMPRAS:".$this->datasis->traevalor('DIRCOMPRAS');
  			
  		$this->pdf->setY(230);
  		$this->pdf->MultiCell(93, 15, '', 'TL','L', 0, 0);
  		$this->pdf->MultiCell(93, 15, '', 'TLR','L', 0, 1);
			$this->pdf->Cell(93, 5, $texto[0], 'LB', 0, 'C');
			$this->pdf->Cell(93, 5, $texto[1], 'LRB', 1, 'C');
				
			$this->pdf->ln(1);
				
			$this->pdf->Cell(186, 5, 'RECIBI CONFORME', 1, 1, 'C');
			
			$this->pdf->Cell(62, 5, '', 'LT', 0, 'C');
			$this->pdf->Cell(62, 5, '', 'LRT', 0, 'C');
			$this->pdf->Cell(62, 5, '', 'RT', 1, 'C');
			$this->pdf->Cell(62, 5, 'NOMBRE', 'LB', 0, 'C');
			$this->pdf->Cell(62, 5, 'CEDULA', 'LRB', 0, 'C');
			$this->pdf->Cell(62, 5, 'FECHA', 'RB', 1, 'C');
				
				
			 $this->pdf->Output('prueba.pdf', 'I');
				
			}
			
			function enc_der($tipo,$numero,$fecha,$re){
				$inicio = $this->pdf->getY();
				$this->pdf->SetFont('helvetica', 'B', 9);
				$this->pdf->setXY(150,4);     
				$this->pdf->Cell(40, 4, 'Orden de '.$tipo, 0, 1, 'L');
				$this->pdf->setX(150);     
				$this->pdf->Cell(40, 4,'Numero ' .$numero, 0, 1, 'L');
				$this->pdf->setX(150);     
				$this->pdf->Cell(40 , 4, 'Fecha: '.$fecha , 0, 1, 'L');
				if($re != ""){
					$this->pdf->setX(150);     
					$this->pdf->Cell(40 , 4, 'Reversado Por: '.$re , 0, 0, 'L');
				}
				$this->pdf->setY($inicio);
				$this->pdf->SetFont('times', '', 8);
				}
		}
?>