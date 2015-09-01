<?php
class recalculo extends Controller {

	var $partidaiva;
	var $url="recalculo/";
	function recalculo(){
		parent::Controller(); 
		//$this->load->plugin('numletra');
		$this->partidaiva = $this->datasis->traevalor("PARTIDAIVA");
		$this->formatoestru=$this->datasis->traevalor('FORMATOESTRU');
		$this->flongestru  =strlen(trim($this->formatoestru));
	}

	function index(){
		//redirect($this->url."cero");
		$this->cero();
	}
	
	function cero(){
		//echo "</br>presupuesto cero".$this->db->query("
		//UPDATE presupuesto SET asignacion=IF(asignacion>0 OR asignacion < 0,asignacion,0),comprometido=IF(comprometido>0 OR comprometido < 0,comprometido,0),causado=IF(causado>0 OR causado < 0,causado,0),opago=IF(opago>0 OR opago < 0,opago,0),pagado=IF(pagado>0 OR pagado < 0,pagado,0),aumento=IF(aumento>0 OR aumento < 0,aumento,0),disminucion=IF(disminucion>0 OR disminucion < 0,disminucion,0),traslados=IF(traslados>0 OR traslados < 0,traslados,0)
		//");
		//echo "</br>ordinal cero".$this->db->query("
		//UPDATE ordinal SET asignacion=IF(asignacion>0 OR asignacion < 0,asignacion,0),comprometido=IF(comprometido>0 OR comprometido < 0,comprometido,0),causado=IF(causado>0 OR causado < 0,causado,0),opago=IF(opago>0 OR opago < 0,opago,0),pagado=IF(pagado>0 OR pagado < 0,pagado,0),aumento=IF(aumento>0 OR aumento < 0,aumento,0),disminucion=IF(disminucion>0 OR disminucion < 0,disminucion,0),traslados=IF(traslados>0 OR traslados < 0,traslados,0)
		//");
		
		echo "</br>presupuesto cero".$this->db->query("
		UPDATE presupuesto SET comprometido=0,causado=0,opago=0,pagado=0");
		echo "</br>ordinal cero".$this->db->query("
		UPDATE ordinal SET comprometido=0,causado=0,opago=0,pagado=0");
		
		$this->ocompra_so();
		$this->ocompra_co();
		$this->odirect_so();
		$this->odirect_co();
		$this->calc();        
	}
	
	function ocompra_so(){//actualiza presupuesto de ocompra con itocompra sin ordinal con partidaiva
	echo "OCOMPRA_SO</BR>";
		$query = $this->db->query("SELECT codigoadm FROM presupuesto WHERE LENGTH(codigoadm)=$this->flongestru GROUP BY codigoadm");
		foreach($query->result() AS $fila ){
			$query2 = $this->db->query("SELECT tipo FROM presupuesto WHERE codigoadm ='$fila->codigoadm' GROUP BY tipo");
			foreach($query2->result() AS $fila2 ){
				echo $asignacion = $this->datasis->dameval("SELECT asignacion FROM presupuesto WHERE codigoadm='$fila->codigoadm' AND tipo='$fila2->tipo' AND codigopres ='$this->partidaiva' ");
				if($asignacion > 0){
					echo "</br>act presupuesto a.estadmin='$fila->codigoadm'  AND a.fondo ='$fila2->tipo' de ocompra y itocompra sin ordinales:".$this->db->query("
						UPDATE presupuesto a JOIN (
						SELECT a.estadmin , a.fondo , b.partida,'' ordinal,SUM((importe*((a.status='C')+(a.status='T')+(a.status='O')+(a.status='E')))) comprometido,SUM((importe*((a.status='T')+(a.status='O')+(a.status='E')))) causado,
						SUM((importe*((a.status='O')+(a.status='E')))) opago,
						SUM((importe*((a.status='E')))) pagado 
						FROM ocompra a 
						JOIN itocompra b ON a.numero=b.numero 
						WHERE b.ordinal=''
						) b ON a.codigoadm= b.estadmin AND a.tipo = b.fondo AND a.codigopres = b.partida 
						SET a.comprometido=a.comprometido+b.comprometido , a.causado=a.causado+b.causado , a.opago=a.opago+b.opago, a.pagado=a.pagado+b.pagado
					");
				
					$row = $this->datasis->damerow("
							SELECT a.estadmin , a.fondo , b.partida,'' ordinal,
							SUM(((importe*(b.iva)/100)*((a.status='C')+(a.status='T')+(a.status='O')+(a.status='E')))) comprometido,
							SUM(((importe*(b.iva)/100)*((a.status='T')+(a.status='O')+(a.status='E')))) causado,
							SUM((((importe*(b.iva)/100)-(((importe*(b.iva)/100)*a.reteiva_prov)/ 100))*((a.status='O')+(a.status='E')))) opago,
							SUM((((importe*(b.iva)/100)-(((importe*(b.iva)/100)*a.reteiva_prov)/ 100))*((a.status='E')))) pagado 
							FROM ocompra a 
							JOIN itocompra b ON a.numero=b.numero
							WHERE b.ordinal='' AND a.estadmin = '$fila->codigoadm' AND a.fondo = '$fila2->tipo' GROUP BY a.estadmin,a.fondo 
					");
					echo "</br>act presupuesto a.estadmin='$fila->codigoadm'  AND a.fondo ='$fila2->tipo' ocompra y itocompra sin ordinales partidaiva:".$this->db->query("
						UPDATE presupuesto a 
						SET a.comprometido=a.comprometido+".$row['comprometido']." , a.causado=a.causado+".$row['causado']." , a.opago=a.opago+".$row['opago'].", a.pagado=a.pagado+".$row['pagado']."
						WHERE a.codigoadm='$fila->codigoadm'  AND a.tipo ='$fila2->tipo'  AND a.codigopres = '$this->partidaiva'
					");
				}else{
					echo "</br>act presupuesto  a.estadmin='$fila->codigoadm'  AND a.fondo ='$fila2->tipo' de ocompra y itocompra sin ordinales:".$this->db->query("
						UPDATE presupuesto a JOIN (
						SELECT a.estadmin , a.fondo , b.partida,'' ordinal,
						SUM(((importe+((importe*(b.iva)/100)-((importe*(b.iva)/100)*reteiva_prov)/100))*((a.status='C')+(a.status='T')+(a.status='O')+(a.status='E')))) comprometido,
						SUM(((importe+((importe*(b.iva)/100)-((importe*(b.iva)/100)*reteiva_prov)/100))*((a.status='T')+(a.status='O')+(a.status='E')))) causado,
						SUM(((importe+((importe*(b.iva)/100)-((importe*(b.iva)/100)*reteiva_prov)/100))*((a.status='O')+(a.status='E')))) opago,
						SUM(((importe+((importe*(b.iva)/100)-((importe*(b.iva)/100)*reteiva_prov)/100))*((a.status='E')))) pagado
						
						FROM ocompra a 
						JOIN itocompra b ON a.numero=b.numero 
						WHERE b.ordinal='' AND a.estadmin='$fila->codigoadm'  AND a.fondo ='$fila2->tipo'
						) b ON a.codigoadm= b.estadmin AND a.tipo = b.fondo AND a.codigopres = b.partida 
						SET a.comprometido=a.comprometido+b.comprometido , a.causado=a.causado+b.causado , a.opago=a.opago+b.opago, a.pagado=a.pagado+b.pagado
					");
				}
			}
		}
		
	}
	
	function ocompra_co(){//actualiza presupuesto de ocompra con itocompra sin ordinal con partidaiva
	echo "OCOMPRA_CO</BR>";
		$query = $this->db->query("SELECT codigoadm FROM presupuesto WHERE LENGTH(codigoadm)=$this->flongestru GROUP BY codigoadm");
		foreach($query->result() AS $fila ){
			$query2 = $this->db->query("SELECT tipo FROM presupuesto WHERE codigoadm ='$fila->codigoadm' GROUP BY tipo");
			foreach($query2->result() AS $fila2 ){
				echo $asignacion = $this->datasis->dameval("SELECT asignacion FROM presupuesto WHERE codigoadm='$fila->codigoadm' AND tipo='$fila2->tipo' AND codigopres ='$this->partidaiva' ");
				if($asignacion > 0){
				
					echo "</br>act ordinal a.estadmin='$fila->codigoadm'  AND a.fondo ='$fila2->tipo' de ocompra y itocompra con ordinales:".$this->db->query("
						UPDATE ordinal a JOIN(
						SELECT a.estadmin , a.fondo , b.partida,ordinal,SUM((importe*((a.status='C')+(a.status='T')+(a.status='O')+(a.status='E')))) comprometido,SUM((importe*((a.status='T')+(a.status='O')+(a.status='E')))) causado,
						SUM((importe*((a.status='O')+(a.status='E')))) opago,
						SUM((importe*((a.status='E')))) pagado 
						FROM ocompra a 
						JOIN itocompra b ON a.numero=b.numero 
						WHERE  b.ordinal<>'' 
						) b ON a.codigoadm= b.estadmin AND a.fondo = b.fondo AND a.codigopres = b.partida AND a.ordinal = b.ordinal 
						SET a.comprometido=b.comprometido , a.causado=b.causado , a.opago=b.opago, a.pagado=b.pagado
					");
					
					$row = $this->datasis->damerow("
							SELECT a.estadmin , a.fondo , b.partida,'' ordinal,
							SUM(((importe*(b.iva)/100)*((a.status='C')+(a.status='T')+(a.status='O')+(a.status='E')))) comprometido,
							SUM(((importe*(b.iva)/100)*((a.status='T')+(a.status='O')+(a.status='E')))) causado,
							SUM((((importe*(b.iva)/100)-((((importe*(b.iva)/100)*a.reteiva_prov)/ 100)))*((a.status='O')+(a.status='E')))) opago,
							SUM((((importe*(b.iva)/100)-((((importe*(b.iva)/100)*a.reteiva_prov)/ 100)))*((a.status='E')))) pagado
							FROM ocompra a
							JOIN itocompra b ON a.numero=b.numero 							
							WHERE b.ordinal<>'' AND a.estadmin = '$fila->codigoadm' AND a.fondo = '$fila2->tipo' GROUP BY a.estadmin,a.fondo 
					");
					
					echo "</br>act presupuesto a.estadmin='$fila->codigoadm'  AND a.fondo ='$fila2->tipo' de ocompra y itocompra con ordinales partidaiva:".$this->db->query("
						UPDATE presupuesto a 
						SET a.comprometido=a.comprometido+".$row['comprometido']." , a.causado=a.causado+".$row['causado']." , a.opago=a.opago+".$row['opago'].", a.pagado=a.pagado+".$row['pagado']."
						WHERE codigoadm='$fila->codigoadm'  AND a.tipo ='$fila2->tipo'  AND a.codigopres = '$this->partidaiva'
					");
				
				}else{
					echo "</br>act ordinal a.estadmin='$fila->codigoadm'  AND a.fondo ='$fila2->tipo' de ocompra y itocompra con ordinales:".$this->db->query("
						UPDATE ordinal a JOIN(
						SELECT a.estadmin , a.fondo , b.partida,ordinal,
						SUM(((importe+((importe*(b.iva)/100)-((importe*(b.iva)/100)*reteiva_prov)/100))*((a.status='C')+(a.status='T')+(a.status='O')+(a.status='E')))) comprometido,
						SUM(((importe+((importe*(b.iva)/100)-((importe*(b.iva)/100)*reteiva_prov)/100))*((a.status='T')+(a.status='O')+(a.status='E')))) causado,
						SUM(((importe+((importe*(b.iva)/100)-((importe*(b.iva)/100)*reteiva_prov)/100))*((a.status='O')+(a.status='E')))) opago,
						SUM(((importe+((importe*(b.iva)/100)-((importe*(b.iva)/100)*reteiva_prov)/100))*((a.status='E')))) pagado 
						FROM ocompra a 
						JOIN itocompra b ON a.numero=b.numero 
						WHERE  b.ordinal<>''  AND a.estadmin='$fila->codigoadm'  AND a.fondo ='$fila2->tipo' 
						) b ON a.codigoadm= b.estadmin AND a.fondo = b.fondo AND a.codigopres = b.partida AND a.ordinal = b.ordinal 
						SET a.comprometido=b.comprometido , a.causado=b.causado , a.opago=b.opago, a.pagado=b.pagado
					");
				}
			}
		}
		
	}
	
	function odirect_so(){//actualiza presupuesto de ocompra con itocompra sin ordinal con partidaiva
	echo "ODIRECT_SO</BR>";
	$query = $this->db->query("SELECT codigoadm FROM presupuesto WHERE LENGTH(codigoadm)=$this->flongestru GROUP BY codigoadm");
		foreach($query->result() AS $fila ){
			$query2 = $this->db->query("SELECT tipo FROM presupuesto WHERE codigoadm ='$fila->codigoadm' GROUP BY tipo");
			foreach($query2->result() AS $fila2 ){
				echo $asignacion = $this->datasis->dameval("SELECT asignacion FROM presupuesto WHERE codigoadm='$fila->codigoadm' AND tipo='$fila2->tipo' AND codigopres ='$this->partidaiva' ");
				if($asignacion > 0){
					echo "</br>act presupuesto a.estadmin='$fila->codigoadm'  AND a.fondo ='$fila2->tipo' de odirect y itodirect sin ordinales:".$this->db->query("
						UPDATE presupuesto a JOIN (
						SELECT a.estadmin , a.fondo , b.partida,'' ordinal,  
						SUM((importe*((a.status='B2')+(a.status='B3')))) comprometido, 
						SUM((importe*((a.status='B2')+(a.status='B3')))) causado, 
						SUM((importe*((a.status='B2')+(a.status='B3')))) opago,
						SUM((importe*((a.status='B3')))) pagado 
						FROM odirect a 
						JOIN itodirect b ON a.numero=b.numero 
						WHERE (a.status = 'B2' OR a.status = 'B3') AND b.ordinal='' 
						) b ON a.codigoadm= b.estadmin AND a.tipo = b.fondo AND a.codigopres = b.partida 
						SET a.comprometido=a.comprometido+b.comprometido , a.causado=a.causado+b.causado , a.opago=a.opago+b.opago, a.pagado=a.pagado+b.pagado
					");			
										
					$row = $this->datasis->damerow("
							SELECT a.estadmin , a.fondo , b.partida,'' ordinal,
							SUM((importe*(b.iva)/100)*                                                     ((a.status='B2')+(a.status='B3')) ) comprometido,
							SUM((importe*(b.iva)/100)*                                                     ((a.status='B2')+(a.status='B3')) ) causado, 
							SUM( (((importe*(b.iva)/100)-(((importe*(b.iva)/100)*a.reteiva_prov)))/ 100)*  ((a.status='B2')+(a.status='B3')) ) opago,
							SUM( (((importe*(b.iva)/100)-(((importe*(b.iva)/100)*a.reteiva_prov)))/ 100)*  ((a.status='B3')                ) ) pagado 
							FROM odirect a 
							JOIN itodirect b ON a.numero=b.numero
							WHERE b.ordinal='' AND a.estadmin = '$fila->codigoadm' AND a.fondo = '$fila2->tipo' GROUP BY a.estadmin,a.fondo 
					");
					echo "</br>act presupuesto a.estadmin='$fila->codigoadm'  AND a.fondo ='$fila2->tipo' ocompra y itocompra sin ordinales partidaiva:".$this->db->query("
						UPDATE presupuesto a 
						SET a.comprometido=a.comprometido+".$row['comprometido']." , a.causado=a.causado+".$row['causado']." , a.opago=a.opago+".$row['opago'].", a.pagado=a.pagado+".$row['pagado']."
						WHERE a.codigoadm='$fila->codigoadm'  AND a.tipo ='$fila2->tipo'  AND a.codigopres = '$this->partidaiva'
					");
				}else{
									
					echo "</br>act ordinal a.estadmin='$fila->codigoadm'  AND a.fondo ='$fila2->tipo' de ocompra y itocompra con ordinales:".$this->db->query("
						UPDATE presupuesto a JOIN(
						SELECT a.estadmin , a.fondo , b.partida,ordinal,
						SUM(((importe+((importe*(b.iva)/100)-((importe*(b.iva)/100)*reteiva_prov)/100))* ((a.status='B2')+(a.status='B3')))) comprometido,
						SUM(((importe+((importe*(b.iva)/100)-((importe*(b.iva)/100)*reteiva_prov)/100))* ((a.status='B2')+(a.status='B3')))) causado,     
						SUM(((importe+((importe*(b.iva)/100)-((importe*(b.iva)/100)*reteiva_prov)/100))* ((a.status='B2')+(a.status='B3')))) opago,       
						SUM(((importe+((importe*(b.iva)/100)-((importe*(b.iva)/100)*reteiva_prov)/100))* ((a.status='B3')))) pagado                       
						FROM odirect a 
						JOIN itodirect b ON a.numero=b.numero 
						WHERE  b.ordinal='' AND a.estadmin='$fila->codigoadm'  AND a.fondo ='$fila2->tipo' AND (a.status = 'B2' OR a.status = 'B3') 
						) b ON a.codigoadm= b.estadmin AND a.tipo = b.fondo AND a.codigopres = b.partida AND b.ordinal='' 
						SET a.comprometido=b.comprometido , a.causado=b.causado , a.opago=b.opago, a.pagado=b.pagado
					");
				}
			}
		}
		
		
	}
	
	function odirect_co(){//actualiza presupuesto de ocompra con itocompra sin ordinal con partidaiva
	echo "ODIRECT_CO</BR>";
		$query = $this->db->query("SELECT codigoadm FROM presupuesto WHERE LENGTH(codigoadm)=$this->flongestru GROUP BY codigoadm");
		foreach($query->result() AS $fila ){
			$query2 = $this->db->query("SELECT tipo FROM presupuesto WHERE codigoadm ='$fila->codigoadm' GROUP BY tipo");
			foreach($query2->result() AS $fila2 ){
				echo $asignacion = $this->datasis->dameval("SELECT asignacion FROM presupuesto WHERE codigoadm='$fila->codigoadm' AND tipo='$fila2->tipo' AND codigopres ='$this->partidaiva' ");
				if($asignacion > 0){
					echo "</br>act ordinal  a.estadmin='$fila->codigoadm'  AND a.fondo ='$fila2->tipo'   de odirect y itodirect con ordinales:".$this->db->query("
						UPDATE ordinal a JOIN (
						SELECT a.estadmin , a.fondo , b.partida,'' ordinal,  
						SUM((importe*((a.status='B2')+(a.status='B3')))) comprometido, 
						SUM((importe*((a.status='B2')+(a.status='B3')))) causado, 
						SUM((importe*((a.status='B2')+(a.status='B3')))) opago,
						SUM((importe*((a.status='B3')))) pagado 
						FROM odirect a 
						JOIN itodirect b ON a.numero=b.numero 
						WHERE (a.status = 'B2' OR a.status = 'B3') AND b.ordinal<>'' 
						) b ON a.codigoadm= b.estadmin AND a.fondo = b.fondo AND a.codigopres = b.partida 
						SET a.comprometido=a.comprometido+b.comprometido , a.causado=a.causado+b.causado , a.opago=a.opago+b.opago, a.pagado=a.pagado+b.pagado
					");
				
					$row = $this->datasis->damerow("
							SELECT a.estadmin , a.fondo , b.partida,'' ordinal,
							SUM((importe*(b.iva)/100)*                                                     ((a.status='B2')+(a.status='B3')) ) comprometido,
							SUM((importe*(b.iva)/100)*                                                     ((a.status='B2')+(a.status='B3')) ) causado, 
							SUM( (((importe*(b.iva)/100)-(((importe*(b.iva)/100)*a.reteiva_prov)))/ 100)*  ((a.status='B2')+(a.status='B3')) ) opago,
							SUM( (((importe*(b.iva)/100)-(((importe*(b.iva)/100)*a.reteiva_prov)))/ 100)*  ((a.status='B3')                ) ) pagado 
							FROM odirect a 
							JOIN itodirect b ON a.numero=b.numero
							WHERE b.ordinal='' AND a.estadmin = '$fila->codigoadm' AND a.fondo = '$fila2->tipo' GROUP BY a.estadmin,a.fondo 
					");
					echo "</br>act ordinal a.estadmin='$fila->codigoadm'  AND a.fondo ='$fila2->tipo' ocompra y itocompra sin ordinales partidaiva:".$this->db->query("
						UPDATE presupuesto a 
						SET a.comprometido=a.comprometido+".$row['comprometido']." , a.causado=a.causado+".$row['causado']." , a.opago=a.opago+".$row['opago'].", a.pagado=a.pagado+".$row['pagado']."
						WHERE a.codigoadm='$fila->codigoadm'  AND a.tipo ='$fila2->tipo'  AND a.codigopres = '$this->partidaiva'
					");
				}else{
									
					echo "</br>act ordinal a.estadmin='$fila->codigoadm'  AND a.fondo ='$fila2->tipo' de ocompra y itocompra con ordinales:".$this->db->query("
						UPDATE ordinal a JOIN(
						SELECT a.estadmin , a.fondo , b.partida,ordinal,
						SUM(((importe+((importe*(b.iva)/100)-((importe*(b.iva)/100)*reteiva_prov)/100))* ((a.status='B2')+(a.status='B3')))) comprometido,
						SUM(((importe+((importe*(b.iva)/100)-((importe*(b.iva)/100)*reteiva_prov)/100))* ((a.status='B2')+(a.status='B3')))) causado,     
						SUM(((importe+((importe*(b.iva)/100)-((importe*(b.iva)/100)*reteiva_prov)/100))* ((a.status='B2')+(a.status='B3')))) opago,       
						SUM(((importe+((importe*(b.iva)/100)-((importe*(b.iva)/100)*reteiva_prov)/100))* ((a.status='B3')))) pagado                       
						FROM odirect a 
						JOIN itodirect b ON a.numero=b.numero 
						WHERE  b.ordinal<>'' AND a.estadmin='$fila->codigoadm'  AND a.fondo ='$fila2->tipo' AND (a.status = 'B2' OR a.status = 'B3') 
						) b ON a.codigoadm= b.estadmin AND a.fondo = b.fondo AND a.codigopres = b.partida AND a.ordinal = b.ordinal 
						SET a.comprometido=b.comprometido , a.causado=b.causado , a.opago=b.opago, a.pagado=b.pagado
					");
				}
			}
		}
		
	}
	

	
	function calc(){
		$query = $this->db->query("SELECT codigoadm FROM presupuesto WHERE LENGTH(codigoadm)=$this->flongestru GROUP BY codigoadm");
		foreach($query->result() AS $fila ){
			$ban = $this->db->query("CALL sp_presucalc('$fila->codigoadm')");
			if($ban)echo "true";else echo "false";
		}
	}
}


////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////


