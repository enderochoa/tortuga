<?php
class Recaudacion {
	
	var $ci;
	var $R_RECIBO_WHERECONTRIBU_DEUDAMIGRA=NULL;
	
	function Recaudacion(){
		$this->ci     =& get_instance();
		$R_RECIBO_WHERECONTRIBU_DEUDAMIGRA = $this->ci->datasis->traevalor('R_RECIBO_WHERECONTRIBU_DEUDAMIGRA','S','USA CONDICION WHERE DE CONTRIBUYENTE EN CONSULTAS DE DEDUDA PARA LOS RECIBOS CREADOS');
		
	}
	
	function calculamonto($formula,$ano=null,$id=null,$id_contribu=null,$base=null){
		$XX=array();
		$anoe=$this->ci->db->escape($ano);
		
		if(!(strpos( $formula,'XX_UTRIBUACTUAL')===false)){
			$XX['XX_UTRIBUACTUAL']=$this->ci->datasis->dameval("SELECT valor FROM utribu WHERE ano=(SELECT MAX(ano) FROM utribu)");
		}
		
		if(!(strpos( $formula,'XX_UTRIBUANO')===false)){
			$XX['XX_UTRIBUANO']=$this->ci->datasis->dameval("SELECT valor FROM utribu WHERE ano=$anoe");
		}
		
		if(!(strpos( $formula,'XX_INMUEBLE_')===false)){
			$query="SELECT zona,techo,mt2,monto,zona_monto,clase_monto,tipoi,clasea_monto,clase_monto2,clasea_monto2,negocio_monto,negocio_monto2  FROM r_v_inmueble WHERE id=$id";
			$row=$this->ci->datasis->damerow($query);
			foreach($row as $k=>$v)
				$XX["XX_INMUEBLE_".strtoupper($k)]=$v;
		}

		if(!(strpos( $formula,'XX_VEHICULO_')===false)){
			$query="SELECT a.capacidad,a.ejes,a.ano,a.peso,b.monto clase_monto,b.monto2 clase_monto2
			FROM r_vehiculo a
			JOIN rv_clase b ON a.id_clase=b.id
			WHERE a.id=$id";
			$row=$this->ci->datasis->damerow($query);
			foreach($row as $k=>$v)
				$XX["XX_VEHICULO_".strtoupper($k)]=$v;
		}
		
		if(!(strpos( $formula,'XX_PUBLICIDAD_')===false)){
			$query="SELECT alto,ancho,dimension,monto tipo_monto,rp_tipos.codigo tipo_codigo FROM r_publicidad JOIN rp_tipos ON  r_publicidad.id_tipo=rp_tipos.id WHERE r_publicidad.id=$id";
			$row=$this->ci->datasis->damerow($query);
			foreach($row as $k=>$v){
				$XX["XX_PUBLICIDAD_".strtoupper($k)]=$v;
			}
		}
		
		if(!(strpos( $formula,'XX_CONTRIBU_')===false) && $id_contribu>0){
			$query="SELECT id_negocio,negocio_monto,negocio_monto2 FROM r_v_contribu WHERE id=$id_contribu";
			$row=$this->ci->datasis->damerow($query);
			foreach($row as $k=>$v)
				$XX["XX_CONTRIBU_".strtoupper($k)]=$v;
		}
		
		if(!(strpos( $formula,'XX_BASE')===false)){
				$XX["XX_BASE"]=$base;
		}
		
		$monto=$this->evaluaformula($formula,$XX);
		return $monto;
	}
	
	function evaluaformula($formula,$XX){
		foreach($XX as $k=>$v){
			$formula=str_replace($k,'$'.$k,$formula);
			$formula=str_replace("$$","$",$formula);
			$$k=$v;
		}
		return eval($formula);
	}
	
	function damedeuda($id_contribu,$tipo,$idreq){
		
		$querys=array();
		
		if($idreq>0)
			$idreq=$idreq;
		else
			$idreq=null;
		
		if($tipo=='INMUEBLE' || empty($tipo))
			$querys[]=$this->damedeudainmueble($id_contribu,$idreq);
			
		if($tipo=='PATENTE' || empty($tipo))
			$querys[]=$this->damedeudapatente($id_contribu);
		
		if($tipo=='VEHICULO' || empty($tipo))
			$querys[]=$this->damedeudavehiculo($id_contribu,$idreq);
			
		if($tipo=='PUBLICIDAD' || empty($tipo))
			$querys[]=$this->damedeudapublicidad($id_contribu,$idreq);
			
		if($tipo=='TODOS' || empty($tipo))
			$querys[]=$this->damedeudatodos($id_contribu);
		
		$query=implode(" UNION ALL ",$querys);
		
		$query.=" ORDER BY id_inmueble,id_vehiculo,ano,frecuencia, freval ";
		
		$mSQL   = $this->ci->db->query($query);
		$arreglo= $mSQL->result_array($query);
		foreach($arreglo as $key=>$row){
			$id=null;
			switch($row['requiere']){
					case 'INMUEBLE'  :$id=$row['id_inmueble'];break;
					case 'VEHICULO'  :$id=$row['id_vehiculo'];break;
					case 'PUBLICIDAD':$id=$row['id_publicidad'];break;
			}
			
			$arreglo[$key]['monto']=$this->calculamonto($row['formula'],$row['ano'],$id,$id_contribu);
		}
		
		foreach($arreglo as $key=>$value)
			foreach($value as $key2=>$value2) 
			$arreglo[$key][$key2] = ($value2);

		return $arreglo;
	}
	
	function damedeudainmueble($id_contribu,$idreq){
		
		$where ='';
		$where1='';
		if($idreq>0){
			$where .=" AND id_inmueble=$idreq ";
			$where1.=" AND a.id=$idreq ";
		}
		
		$query="
		select b.id_conc,`b`.`id` AS `id`,`b`.`ano` AS `ano`,`b`.`acronimo` AS `acronimo`,`b`.`denomi` AS `denomi`,`b`.`requiere` AS `requiere`,`a`.`id` AS `id_inmueble`,`a`.`catastro` AS `catastro`,NULL AS `id_vehiculo`,NULL AS `placa`,`a`.`id_contribu` AS `id_contribu`,CONCAT_WS('',a.dir1,dir2,dir3,dir4) AS `observa`,`b`.`formula` AS `formula` ,b.frecuencia,b.freval,b.modo
		from `r_inmueble` `a` 
		join `r_concit` `b` on 1 = 1
		LEFT JOIN (
			SELECT id_inmueble,id_conc ,MAX(ano) ano,MAX(CONCAT(ano,LPAD(freval,2,0))) anofreval
			FROM r_reciboit
			JOIN r_recibo ON r_reciboit.id_recibo=r_recibo.id
			WHERE id_inmueble>0  $where ";
			
		if($this->R_RECIBO_WHERECONTRIBU_DEUDAMIGRA=='S')
			$query.=" AND id_contribu=$id_contribu ";
			
		$query.=" 
			GROUP BY  id_inmueble,id_conc
		)maximo ON b.id_conc=maximo.id_conc AND a.id=maximo.id_inmueble 
		where ((`b`.`requiere` = 'INMUEBLE') and (CONCAT(b.ano,LPAD(b.freval,2,0)) > 0) ) 
		AND a.id_contribu=$id_contribu
		AND b.deleted=0
		AND (maximo.ano>0 OR maximo.ano IS NULL)
		AND CONCAT(b.ano,LPAD(b.freval,2,0))>IF(maximo.id_inmueble>0,maximo.anofreval,0)
		$where1
		";
		return $query;
	}
	
	function damedeudapublicidad($id_contribu,$idreq){
		
		$where ='';
		$where1='';
		if($idreq>0){
			$where .=" AND id_publicidad=$idreq ";
			$where1.=" AND a.id=$idreq ";
		}
		
		$query="
		SELECT b.id_conc,`b`.`id` AS `id`,`b`.`ano` AS `ano`,`b`.`acronimo` AS `acronimo`,`b`.`denomi` AS 
		`denomi`,`b`.`requiere` AS `requiere`,null AS `id_inmueble`,null AS `catastro`,NULL AS
		`id_vehiculo`,NULL AS `placa`,`a`.`id_contribu` AS `id_contribu`,CONCAT_WS(' ',a.ancho,'X',a.alto) AS `observa`,`b`.`formula` AS `formula` ,b.frecuencia,b.freval,b.modo,a.id id_publicidad
		from `r_publicidad` `a` 
		join `r_concit` `b` on 1 = 1
		LEFT JOIN (
			SELECT id_publicidad,id_conc ,MAX(ano) ano,MAX(CONCAT(ano,LPAD(freval,2,0))) anofreval
			FROM r_reciboit
			JOIN r_recibo ON r_reciboit.id_recibo=r_recibo.id
			WHERE id_publicidad>0 $where ";
			
		if($this->R_RECIBO_WHERECONTRIBU_DEUDAMIGRA=='S')
			$query.=" AND id_contribu=$id_contribu ";
			
		$query.=" 
			
			GROUP BY  id_publicidad,id_conc
		)maximo ON b.id_conc=maximo.id_conc AND a.id=maximo.id_publicidad
		where ((`b`.`requiere` = 'PUBLICIDAD') and (CONCAT(b.ano,LPAD(b.freval,2,0)) > 0) ) 
		AND a.id_contribu=$id_contribu
		AND b.deleted=0
		AND (maximo.ano>0 OR maximo.ano IS NULL)
		AND CONCAT(b.ano,LPAD(b.freval,2,0))>IF(maximo.id_publicidad>0,maximo.anofreval,0)
		$where1
		";
		
		return $query;
	}
	
	function damedeudavehiculo($id_contribu,$idreq=null){
		
		$where ='';
		$where1='';
		if($idreq>0){
			$where .=" AND id_vehiculo=$idreq ";
			$where1.=" AND a.id=$idreq ";
		}
		
		$query="
		select b.id_conc,b.id AS id,b.ano AS ano,b.acronimo AS acronimo,b.denomi AS denomi,b.requiere AS requiere,NULL AS id_inmueble,NULL AS catastro,a.id AS id_vehiculo,a.placa AS placa,a.id_contribu AS id_contribu,'' AS observa,b.formula AS formula ,b.frecuencia,b.freval,b.modo,null id_publicidad
		from r_vehiculo a 
		join r_concit b on 1 = 1
		LEFT JOIN (
			SELECT id_vehiculo,id_conc ,MAX(ano) ano,MAX(CONCAT(ano,LPAD(freval,2,0))) anofreval
			FROM r_reciboit
			JOIN r_recibo ON r_reciboit.id_recibo=r_recibo.id
			WHERE id_vehiculo>0  $where ";
			
		if($this->R_RECIBO_WHERECONTRIBU_DEUDAMIGRA=='S')
			$query.=" AND id_contribu=$id_contribu ";
			
		$query.="  
			GROUP BY  id_vehiculo,id_conc
		)maximo ON b.id_conc=maximo.id_conc AND a.id=maximo.id_vehiculo 
		where ((b.requiere = 'VEHICULO') and (CONCAT(b.ano,LPAD(b.freval,2,0)) > 0) ) 
		AND b.ano >= a.ano
		AND a.id_contribu=$id_contribu
		AND b.deleted=0
		AND maximo.ano>0
		AND CONCAT(b.ano,LPAD(b.freval,2,0))>IF(maximo.id_vehiculo>0,maximo.anofreval,0)
		$where1
		
		UNION ALL 
		
		select b.id_conc,b.id AS id,b.ano AS ano,b.acronimo AS acronimo,b.denomi AS denomi,b.requiere AS requiere,NULL AS id_inmueble,NULL AS catastro,a.id AS id_vehiculo,a.placa AS placa,a.id_contribu AS id_contribu,'' AS observa,b.formula AS formula ,b.frecuencia,b.freval,b.modo,null id_publicidad
		from r_vehiculo a 
		join r_concit b on 1 = 1
		where b.requiere = 'VEHICULO'
		AND a.id_contribu=$id_contribu
		AND b.deleted=0
		AND (SELECT count(*) FROM r_reciboit WHERE r_reciboit.id_vehiculo=a.id)=0
		AND (b.ano=0 OR b.ano=(SELECT valor FROM valores WHERE nombre='EJERCICIO'))
		$where1
		";
		
		return $query;
	}
	
	function damedeudapatente($id_contribu){
		$query="
		select b.id_conc,`b`.`id` AS `id`,`b`.`ano` AS `ano`,`b`.`acronimo` AS `acronimo`,`b`.`denomi` AS `denomi`,`b`.`requiere` AS `requiere`,NULL AS `id_inmueble`,NULL AS `catastro`,NULL AS `id_vehiculo`,NULL AS `placa`,`a`.`id` AS `id_contribu`,'' AS `observa`,`b`.`formula` AS `formula` ,b.frecuencia,b.freval,b.modo,null id_publicidad
		from (((`r_contribu` `a` 
		join `r_concit` `b` on((1 = 1))) 
		left join `r_reciboit` `c` on((`b`.`id` = `c`.`id_concit`))) 
		left join `r_recibo` `d` on(((`c`.`id_recibo` = `d`.`id`) and (`a`.`id` = `d`.`id_contribu`)))) 
		where ((`b`.`requiere` = 'PETENTE') and (`b`.`ano` > 0) and isnull(`d`.`id`) and a.patente='S')
		AND a.id=$id_contribu
		AND b.deleted=0
		";
		return $query;
	}
	
	function damedeudatodos($id_contribu){
		$query="select b.id_conc,`b`.`id` AS `id`,`b`.`ano` AS `ano`,`b`.`acronimo` AS `acronimo`,`b`.`denomi` AS `denomi`,`b`.`requiere` AS `requiere`,NULL AS `id_inmueble`,NULL AS `catastro`,NULL AS `id_vehiculo`,NULL AS `placa`,`a`.`id` AS `id_contribu`,'' AS `observa`,`b`.`formula` AS `formula` ,b.frecuencia,b.freval,b.modo,null id_publicidad
		from (((`r_contribu` `a` 
		join `r_concit` `b` on((1 = 1))) 
		left join `r_reciboit` `c` on((`b`.`id` = `c`.`id_concit`))) 
		left join `r_recibo` `d` on(((`c`.`id_recibo` = `d`.`id`) and (`a`.`id` = `d`.`id_contribu`)))) 
		where ((length(`b`.`requiere`) = 0) and (`b`.`ano` > 0) and isnull(`d`.`id`))
		AND a.id=$id_contribu
		AND b.deleted=0";
		return $query;
	}
	
	function ultimo_pago($id_conc,$id_inmueble=null,$id_vehiculo=null){
		$id_conce    = $this->db->escape($id_conc    );
		$id_inmueblee= $this->db->escape($id_inmueble);
		$id_vehiculoe= $this->db->escape($id_vehiculo);
		
		$where       = '';
				
		$query="SELECT MAX(LAST_DAY(1*CONCAT(a.ano,LPAD(a.freval,2,0),'01'))) 
				FROM r_reciboit a
				JOIN r_abonosit b ON a.id_recibo=b.recibo
				WHERE  a.id_conc=$id_conce ";
				
		if($id_inmueble)
		$query.=$where=" AND a.id_inmueble=$id_inmueblee ";
		if($id_vehiculo)
		$query.=$where=" AND a.id_vehiculo=$id_vehiculoe ";
		
		$val = $this->ci->datasis->dameval($query);
		
		if(!$val){
			$query="SELECT MIN(LAST_DAY(1*CONCAT(ano,LPAD(freval,2,0),'01'))) FROM r_concit WHERE id_conc=$id_conce ";
			$val = $this->ci->datasis->dameval($query);
		}
		
		return $val;
	}
	
	function dameinteres($base,$frecibo,$ano,$frecuencia,$freval,$modo=1){
		/*
		 * Modo 1: Calcula Intereses Sobre Intereses
		 * Modo 2: Calcula Sin Intereses Sobre intereses
		 * */
		switch($frecuencia){
			case 1:{
				$tasas="
				SELECT ano,ROUND(AVG(monto*1.2),2) tasa,MAX(LAST_DAY(CONCAT(ano,'-',mes,'-01'))) fin
				FROM r_interes
				GROUP BY ano
				HAVING fin<$frecibo AND fin >='$ano-12-31'
				ORDER BY ano
				";
				$tasas    =$this->ci->db->query($tasas);
				$tasas    =$tasas->result_array();
			
				$impuesto=0;
				foreach($tasas as $row){
					if($modo==1)
						$impuesto+=($base+$impuesto)*$row['tasa']/100;
					elseif($modo==2)
						$impuesto+=($base)*$row['tasa']/100;
				}
			}break;
			case 2:{
				$tasas="
				SELECT ano,IF(mes>=1 AND mes<=6,1,2) semestre,ROUND(AVG(monto*1.2)/2,2) tasa,MAX(LAST_DAY(CONCAT(ano,'-',mes,'-01'))) fin
				FROM r_interes
				GROUP BY ano,semestre
				HAVING fin<$frecibo AND fin >=IF($freval=2,'$ano-12-31','$ano-06-30')
				ORDER BY ano,semestre
				";
				
				$tasas    =$this->ci->db->query($tasas);
				$tasas    =$tasas->result_array();
				
				
				$impuesto=0;
				foreach($tasas as $row){
					if($modo==1){
						//echo "$ano	SEMESTRE $freval	AL ".$row['fin'].";$base; $impuesto;".($row['tasa']/100).";".(($base+$impuesto)*$row['tasa']/100)."</br>";
						$impuesto+=($base+$impuesto)*$row['tasa']/100;
						
					}elseif($modo==2){
						$impuesto+=($base)*$row['tasa']/100;
					}
				}
			}break;
			case 3:{
			}break;
			case 4:{
				$tasas="
				SELECT ano,mes,ROUND(monto*1.2,2) tasa,LAST_DAY(CONCAT(ano,'-',mes,'-01')) fin
				FROM r_interes
				HAVING fin<$frecibo AND fin >='$ano-12-31'
				ORDER BY ano,mes
				";
				$tasas    =$this->ci->db->query($tasas);
				$tasas    =$tasas->result_array();
				
				$impuesto=0;
				foreach($tasas as $row){
					if($modo==1)
						$impuesto+=($base+$impuesto)*$row['tasa']/100;
					elseif($modo==2)
						$impuesto+=($base)*$row['tasa']/100;
				}
			}break;
		}
		
		return $impuesto;
	}
	
	function dameconc(){
		$query  ="SELECT id,ano,acronimo,denomi,requiere,modo,frecuencia,IF(frecuencia=1,'Año',IF(frecuencia=2,'Semestre',IF(frecuencia=3,'Trimestre',IF(frecuencia=4,'MES','')))) frecuenciatexto
		,freval FROM r_v_conc";
		$mSQL   = $this->ci->db->query($query);
		$arreglo= $mSQL->result_array($query);
		
		foreach($arreglo as $key=>$value)
			foreach($value as $key2=>$value2) 
			$arreglo[$key][$key2] = ($value2);

		return $arreglo;
	}
	
	function inmueble_cant($id_contribu){
		$id_contribue = $this->ci->db->escape($id_contribu);
		$query="SELECT COUNT(*) FROM r_inmueble WHERE id_contribu=$id_contribue ";
		return $this->ci->datasis->dameval($query);
	}
	
	function inmueble_get($id_contribu){
		
		$id_contribue = $this->ci->db->escape($id_contribu);
		$query="SELECT id,catastro,CONCAT_WS(' ',dir1,dir2,dir3,dir4) direccion FROM r_inmueble WHERE id_contribu=$id_contribue ";
		$query=$this->ci->db->query($query);
		$arreglo= $query->result_array();
		
		foreach($arreglo as $key=>$value)
			foreach($value as $key2=>$value2) 
			$arreglo[$key][$key2] = ($value2);

		return $arreglo;
	}
	
	function vehiculo_cant($id_contribu){
		
		$id_contribue = $this->ci->db->escape($id_contribu);
		$query="SELECT COUNT(*) FROM r_vehiculo WHERE id_contribu=$id_contribue ";
		return $this->ci->datasis->dameval($query);
	}
	
	function vehiculo_get($id_contribu){
		
		$id_contribue = $this->ci->db->escape($id_contribu);
		$query="SELECT id,placa FROM r_vehiculo WHERE id_contribu=$id_contribue ";
		$query=$this->ci->db->query($query);
		$arreglo= $query->result_array();
		
		foreach($arreglo as $key=>$value)
			foreach($value as $key2=>$value2) 
			$arreglo[$key][$key2] = ($value2);

		return $arreglo;
	}
	
	function actdeuda($id_cxc){
		$id_cxce = $this->ci->db->escape($id_cxc);
		$this->ci->db->trans_start();

		$query="INSERT INTO r_recibo(id,id_contribu,fecha,numero,rifci,nombre,telefono,monto,id_parroquia,parroquia,id_zona,zona,dir1,dir2,dir3,dir4,razon,solvencia,solvenciab,licores,caja)
		SELECT '',id_contribu,19870602,numero,rifci,nombre,telefono,monto,id_parroquia,parroquia,id_zona,zona,dir1,dir2,dir3,dir4,razon,solvencia,solvenciab,licores,'ACT'
		FROM r_cxc
		WHERE id=$id_cxc";
		
		$this->ci->db->query($query);
		$id_recibo=$this->ci->db->insert_id();
		
		$query="
		INSERT INTO r_reciboit(id,id_recibo,id_concit,id_conc,id_cxcit,id_vehiculo,id_inmueble,id_publicidad,ano,frecuencia,freval,base,monto,observa,acronimo,denomi,i_id_parroquia,i_parroquia,i_id_zona,i_zona,i_dir1,i_dir2,i_dir3,i_dir4,v_placa,i_catastro,requiere,modo,partida,v_marca,v_modelo,partida_denomi,conc_denomi,p_id_tipo,p_tipo_descrip)
		SELECT '', $id_recibo ,id_concit,id_conc,id_cxc,id_vehiculo,id_inmueble,id_publicidad,ano,frecuencia,freval,base,monto,observa,acronimo,denomi,i_id_parroquia,i_parroquia,i_id_zona,i_zona,i_dir1,i_dir2,i_dir3,i_dir4,v_placa,i_catastro,requiere,modo,partida,v_marca,v_modelo,partida_denomi,conc_denomi,p_id_tipo,p_tipo_descrip
		FROM r_cxcit
		WHERE id_cxc=$id_cxc
		";
		
		$this->ci->db->query($query);
		
		$query="INSERT INTO r_abonos(id,estampa) values('',19870602)";
		$this->ci->db->query($query);
		
		$id_abono=$this->ci->db->insert_id();
		
		$query="INSERT INTO r_abonosit(id,abono,recibo) VALUES('',$id_abono,$id_recibo)";
		$this->ci->db->query($query);
		
		$query="INSERT INTO r_mbanc (id,abono,codbanc,tipo_doc,fecha) VALUES ('',$id_abono,'ACT','EF',19870602)";
		$this->ci->db->query($query);
		
		$this->ci->db->trans_complete();
	}
	
	function trae_conc_interes(){
		$query="SELECT a.id id_concit,a.id_conc,a.denomi,a.formula,b.id_presup,b.denomi conc_denomi,c.partida,c.denomi partida_denomi,0 monto,a.requiere
		FROM r_concit a
		JOIN r_conc b ON a.id_conc=b.id
		JOIN r_presup c ON b.id_presup=c.id
		WHERE a.requiere='INTERESES' AND deleted=0";
		
		$intereses=$this->ci->db->query($query);
		
		return $intereses->result_array();
	}
	
	function trae_conc_descuento(){
		$query="SELECT a.id id_concit,a.id_conc,a.denomi,a.formula,b.id_presup,b.denomi conc_denomi,c.partida,c.denomi partida_denomi,0 monto,a.requiere
		FROM r_concit a
		JOIN r_conc b ON a.id_conc=b.id
		JOIN r_presup c ON b.id_presup=c.id
		WHERE a.requiere='DESCUENTO' AND deleted=0";
		
		$descuentos=$this->ci->db->query($query);
		return $descuentos->result_array();
	}
}
?>
