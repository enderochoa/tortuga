<?php  if (!defined('BASEPATH')) exit('No direct script access allowed');

function sp_costos(){
	$CI = & get_instance();
	
	$coscero = $CI->datasis->dameval("SELECT COUNT(*) FROM COSTOS");
	if($coscero>0)
	$coscero=0;
	else
	$coscero=1;
	
	
	
	$query  = "SELECT $coscero+(SELECT COUNT(*) FROM suminr WHERE estampa>(SELECT MAX(estampa) FROM COSTOS) ) + (SELECT COUNT(*) FROM sumine WHERE estampa>@d:=(SELECT MAX(estampa) FROM COSTOS))";
	$cambio = $CI->datasis->dameval($query);
	
	if($cambio>0){
		
		$CI->db->query("TRUNCATE COSTOS");
		
		$sumi=$CI->datasis->consularray("SELECT codigo,codigo a FROM sumi");

		foreach($sumi as $k=>$v){
			$ke=$CI->db->escape($k);
			$query="
			INSERT INTO COSTOS (id,numero,alma,tipo,fecha,codigo,cantidad,acumulado,precio,promedio,cant_anteri)
			SELECT '' id,'' numero,'' alma,'' tipo,19000101 fecha,$ke codigo,0 cantidad,@Xc:=0 acumulado,0 precio,@Xprom:=0 promedio,@Xca:=0 cant_anteri
			UNION ALL
			SELECT '' id,numero,alma,tipo,fecha,codigo,cantidad,@Xc:=round(cantidad+@Xc,2) acumulado,precio
			,round(IF(tipo='R' AND cantidad>0,@Xprom:=(((@Xca*@Xprom)+(cantidad*precio))/@Xc),@Xprom),2) promedio
			,@Xca:=round(cantidad+@Xca,2) cant_anteri FROM (
			
			SELECT b.alma,b.numero,'R' tipo,b.fecha,a.codigo,a.cantidad,a.precio,a.total
			FROM itsuminr a
			JOIN suminr b ON a.numero=b.numero
			WHERE b.status='C' 	AND a.codigo=$ke
			
			UNION ALL
			
			SELECT d.alma,d.numero,'E' tipo,d.fecha,e.codigo,-1*e.cantidad,0 ,0
			FROM itsumine e
			JOIN sumine d ON e.numero=d.numero
			WHERE d.status='C' 	AND e.codigo=$ke
			ORDER BY fecha,tipo<>'R',numero
			
			)todo";

			$CI->db->query($query);

		}
	}
	
}
