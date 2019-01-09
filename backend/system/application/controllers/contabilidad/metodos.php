<?php
class Metodos extends Controller {

	//Genera las consulta para la contabilidad
	function _hace_regla($modulo, $mCONTROL, $mGRUPO ) {
		
		$query=$this->db->query("SELECT mbanc_id,modulo,regla,tabla,fecha,comprob,origen,condicion,agrupar,cuenta,referen,concepto,debe,haber,ccosto,sucursal,control FROM `reglascont` WHERE modulo='$modulo'  ORDER BY tabla,regla");
		foreach ($query->result_array() as $fila){
			if ( $fila['tabla'] == "ITCASI" ) {
				$select ="
				$fila[fecha]    fecha, 
				$fila[comprob]  comprob, 
				'$fila[modulo]$fila[regla]' clave, 
				$fila[cuenta]   cuenta, 
				$fila[referen]  referen, 
				$fila[concepto] concepto, 
				$fila[debe]     debe, 
				$fila[haber]    haber, 
				$fila[sucursal] sucursal,";
				$select.= (empty($fila['ccosto']) ? "'' ccosto," : $fila['ccosto'].' ccosto,') ;
				$select.= (empty($fila['mbanc_id']) ? "NULL mbanc_id" : $fila['mbanc_id'].' mbanc_id') ;
			}else{
				$select ="
				$fila[comprob] comprob,
				$fila[fecha] fecha, 
				$fila[concepto] concepto, 
				'$modulo' origen ";
			}
			$mCONTROL=$fila['control'];
			$cwhere = " $mCONTROL='$mGRUPO'";
			$from    =$fila['origen'];
			$where   = (empty($fila['condicion'])? $cwhere:"$cwhere AND  $fila[condicion] ");
			$groupby = $fila['agrupar'];

			$data ="SELECT $select FROM $from WHERE $where";
			$data.= (empty($groupby)? " ":" GROUP BY $groupby");
			if ( $fila['tabla'] == "ITCASI" )
				$itcasi[]  =$data;
			else
				$casi[]=$data;
		}
		$areglo['casi']  =$casi;
		$areglo['itcasi']=$itcasi;
		return $areglo;
	}
}
?>