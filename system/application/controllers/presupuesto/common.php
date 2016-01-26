<?php
class Common extends Controller {
	var $redirect = true;
	function common(){
		parent::Controller();
		$this->load->library("rapyd");
	}

	function itpartida($estadmin,$fondo,$partida,$ordinal=''){
		$estadmin = $this->db->escape($estadmin);
		$fondo    = $this->db->escape($fondo);
		$partida  = $this->db->escape($partida);
		$cana=$this->datasis->dameval("SELECT COUNT(*) FROM presupuesto WHERE codigoadm=$estadmin AND codigopres=$partida AND tipo=$fondo  ");

		if($cana > 0){
			//return true;
		}else{
			return "<div class='alert'>La partida %s ($partida) No pertenece al la estructura administrativa ($estadmin) o al fondo ($fondo) seleccionado</div>";
		}
	}

	function chequeapresup($codigoadm,$fondo,$codigopres,$ordinal='',$importe,$iva,$formula,$msj='',$result=''){
		$this->rapyd->load('dataobject');

		$error='';

		//$partidaiva=$this->datasis->traevalor("PARTIDAIVA");
		//
		//$presup = new DataObject("presupuesto");
		//
		//$pk=array('codigoadm'=>$codigoadm,'tipo'=>$fondo,'codigopres'=>$partidaiva);
		//$presup->load($pk);
	  //
		//$asignacion   =$presup->get("asignacion");
		//
		//if($asignacion>0){
		//	$monto  = $importe;
		//	if($codigopres == 'PARTIDAIVA'){
		//		$codigopres = $partidaiva;
		//	}
		//}elseif($codigopres == 'PARTIDAIVA'){
		//	$monto = 0;
		//}else{
		//	$monto  = $importe+($importe*$iva/100);
		//}
		$monto=$importe;

		$monto = round($monto,2);
		//$monto = abs($monto);
		if($monto > 0){
			$presup = new DataObject("presupuesto");
			$pk=array('codigoadm'=>$codigoadm,'tipo'=>$fondo,'codigopres'=>$codigopres);
			$presup->load($pk);

			$asignacion  =$presup->get("asignacion"  );
			$aumento     =$presup->get("aumento"     );
			$disminucion =$presup->get("disminucion" );
			$traslados   =$presup->get("traslados"   );
			$comprometido=$presup->get("comprometido");
			$causado     =$presup->get("causado"     );
			$opago       =$presup->get("opago"       );
			$pagado      =$presup->get("pagado"      );
			$apartado    =$presup->get("apartado"    );

			$a = $opago -$pagado;

			$presupuesto = (($asignacion+$aumento-$disminucion)+($traslados));
			$disp        =round($presupuesto-($comprometido+$apartado),2);

			$retorna='$rt='.$formula.';$msj2="'.$msj.'";';

			ob_start();
			eval($retorna);
			$_f=ob_get_contents();
			@ob_end_clean();

			if($rt){

				$error.= "<div class='alert'><p>".$msj2."</p></div>";
			}

			if(round($comprometido,2) > round($presupuesto,2))
				$error.="<div class='alert'><p>Error en compromiso de presupuesto para la partida $codigoadm $fondo $codigopres. Por favor contacte al administrador del sistema</p></div>";

			if(round($causado,2)  > round($presupuesto,2))
				$error.="<div class='alert'><p>Error en causacion de presupuesto para la partida $codigoadm $fondo $codigopres. Por favor contacte al administrador del sistema</p></div>";

			if(round($opago,2)  > round($presupuesto,2))
				$error.="<div class='alert'><p>Error en Ordenado Pago de presupuesto para la partida $codigoadm $fondo $codigopres. Por favor contacte al administrador del sistema</p></div>";

			if(round($pagado,2)  > round($presupuesto,2))
				$error.="<div class='alert'><p>Error en pagado de presupuesto para la partida $codigoadm $fondo $codigopres. Por favor contacte al administrador del sistema</p></div>";

			if(round($causado,2)  > round($comprometido,2))
				$error.="<div class='alert'><p>Error en Causado de presupuesto para la partida $codigoadm $fondo $codigopres. Por favor contacte al administrador del sistema</p></div>";

			if(round($opago,2)  > round($causado,2))
				$error.="<div class='alert'><p>Error en Ordenado Pago de presupuesto para la partida $codigoadm $fondo $codigopres. Por favor contacte al administrador del sistema</p></div>";

			if(round($pagado,2)  > round($opago,2))
				$error.="<div class='alert'><p>Error en pagado de presupuesto para la partida $codigoadm $fondo $codigopres. Por favor contacte al administrador del sistema</p></div>";

			if(!empty($ordinal)){
				$ord       = new DataObject("ordinal");
				$pk=array('codigoadm'=>$codigoadm,'fondo'=>$fondo,'codigopres'=>$codigopres,'ordinal'=>$ordinal);

				$ord   ->load($pk);

				$ordi = $ord   ->get('ordinal');

				$asignacion  =$ord->get("asignacion"  );
				$aumento     =$ord->get("aumento"     );
				$disminucion =$ord->get("disminucion" );
				$traslados   =$ord->get("traslados"   );
				$comprometido=$ord->get("comprometido");
				$causado     =$ord->get("causado"     );
				$opago       =$ord->get("opago"       );
				$pagado      =$ord->get("pagado"      );

				$presupuesto = (($asignacion+$aumento-$disminucion)+($traslados));
				$disp    =round((($asignacion+$aumento-$disminucion)+($traslados))-$comprometido,2);

				ob_start();
				eval($retorna);
				$_f=ob_get_contents();
				@ob_end_clean();


				$result2='$msj2="'.$msj.'";';

				ob_start();
				eval($result2);
				$_f=ob_get_contents();
				@ob_end_clean();

				if($rt){

					$error.= "<div class='alert'><p>".$msj2."</p></div>";
				}

				if(round($comprometido,2) > round($presupuesto,2))
					$error.="<div class='alert'><p>Error en compromiso de presupuesto para la partida $codigoadm $fondo $codigopres ordinal $ordinal. Por favor contacte al administrador del sistema</p></div>";

				if(round($causado,2)  > round($presupuesto,2))
					$error.="<div class='alert'><p>Error en causacion de presupuesto para la partida $codigoadm $fondo $codigopres ordinal $ordinal. Por favor contacte al administrador del sistema</p></div>";

				if(round($opago,2)  > round($presupuesto,2))
					$error.="<div class='alert'><p>Error en Ordenado Pago de presupuesto para la partida $codigoadm $fondo $codigopres ordinal $ordinal. Por favor contacte al administrador del sistema</p></div>";

				if(round($pagado,2)  > round($presupuesto,2))
					$error.="<div class='alert'><p>Error en pagado de presupuesto para la partida $codigoadm $fondo $codigopres ordinal $ordinal. Por favor contacte al administrador del sistema</p></div>";

				if(round($causado,2)  > round($comprometido,2))
					$error.="<div class='alert'><p>Error en Causado de presupuesto para la partida $codigoadm $fondo $codigopres ordinal $ordinal. Por favor contacte al administrador del sistema</p></div>";

				if(round($opago,2)  > round($causado,2))
					$error.="<div class='alert'><p>Error en Ordenado Pago de presupuesto para la partida $codigoadm $fondo $codigopres ordinal $ordinal. Por favor contacte al administrador del sistema</p></div>";

				if(round($pagado,2)  > round($opago,2))
					$error.="<div class='alert'><p>Error en pagado de presupuesto para la partida $codigoadm $fondo $codigopres ordinal $ordinal. Por favor contacte al administrador del sistema</p></div>";

			}
		}

		if(empty($error)){

			return '';
		}else{
//		echo "{".$error."}";
			return $error;
		}
	}


	function afectapresup($codigoadm,$fondo,$codigopres,$ordinal='',$importe,$iva, $opera ,$campos){
	//echo "entro";
		$this->rapyd->load('dataobject');

		$error='';

		$monto=$importe;

		if($monto!=0){
			$presup = new DataObject("presupuesto");

			$pk=array('codigoadm'=>$codigoadm,'tipo'=>$fondo,'codigopres'=>$codigopres);

			$presup->load($pk);

			if(($codigoadm != $presup->get('codigoadm')) || ($codigopres != $presup->get('codigopres')) || ($fondo != $presup->get('tipo')))
				$error.="<div class='alert'><p>No se puede cargar la partida ($codigoadm) ($fondo) ($codigopres)</p></div>";

			$lcampos = array("aumento","disminucion","traslados","comprometido","causado","opago","pagado","apartado");
			foreach($lcampos AS $cel=>$campo){
				if(in_array($campo,$campos))
				$presup->set($campo,($presup->get($campo)+($monto*$opera)));
			}

			if(!empty($ordinal)){
				$ban = false;
				$ord    = new DataObject("ordinal");

				$pk=array('codigoadm'=>$codigoadm,'fondo'=>$fondo,'codigopres'=>$codigopres,'ordinal'=>$ordinal);
				$ord ->load($pk);

				$ordi  = $ord->get('ordinal');
				if($ordi==$ordinal){
						$lcampos = array("aumento","disminucion","traslados","comprometido","causado","opago","pagado");
					foreach($lcampos AS $cel=>$campo){
						if(in_array($campo,$campos))
						$ord->set($campo,($ord->get($campo)+($monto*$opera)));
					}
				}
			}
		}

		if(empty($error) && $monto!=0){
			$presup->save();
//			print_r($presup->get_all());
			//if(!empty($ordinal))
			//	$ord->save();

			return '';
		}else{
			return $error;
		}
	}

	function sp_presucalc($codigoadm){
		//$this->db->query("CALL sp_presucalc('$codigoadm')");
		return true;
	}

	function co_reversar($id){

		$this->rapyd->load('dataobject');

		$do = new DataObject("ocompra");
		$do->rel_one_to_many('itocompra', 'itocompra', array('numero'=>'numero'));
		$do->load($id);
		$anular = true;
		$error  = "";

		$fcomprome   = $do->get('fcomprome');
		$certificado = $do->get('certificado');

		$sta=$do->get('status');

		if($sta == "C"){
			$ivan=0;$importes=array(); $ivas=array();$admfondo=array();
			for($i=0;$i < $do->count_rel('itocompra');$i++){
				$codigoadm   = $do->get_rel('itocompra','codigoadm',$i);
				$fondo       = $do->get_rel('itocompra','fondo'    ,$i);
				$codigopres  = $do->get_rel('itocompra','partida'  ,$i);
				$importe     = $do->get_rel('itocompra','importe'  ,$i);
				$iva         = $do->get_rel('itocompra','iva'      ,$i);
				$ordinal     = $do->get_rel('itocompra','ordinal'  ,$i);
				$ivan        = $importe*$iva/100;

				$error.=$this->itpartida($codigoadm,$fondo,$codigopres,$ordinal);

				$cadena = $codigoadm.'_._'.$fondo.'_._'.$codigopres;//.'_._'.$ordinal.'_._'.$iva;

				if(array_key_exists($cadena,$importes)){
					$importes[$cadena] +=$importe;
					//$ivas[$cadena]      =$iva;
				}else{
					$importes[$cadena]  =$importe;
					//$ivas[$cadena]      =$iva;
				}
				$cadena2 = $codigoadm.'_._'.$fondo;
				$admfondo[$cadena2]=(array_key_exists($cadena2,$admfondo)?$admfondo[$cadena2]+=$ivan:$admfondo[$cadena2] =$ivan);
				
			}
			if(empty($error)){
				foreach($importes as $k=>$v){
					$temp  = explode('_._',$k);		
					
					$query ="SELECT SUM(a.pagos) 
					FROM v_comproxcausar_s1 a
					WHERE a.ocompra='".$id."' AND a.codigoadm='".$temp[0]."' AND a.fondo='".$temp[1]."' AND a.codigopres='".$temp[2]."' 
					";
					$totcau=$this->datasis->dameval($query);
					
					if($totcau>0)
					$error.="<div class='alert'><p> ERROR. El Compromiso ya tiene pagos, debe anular los pagos antes de reversar el compromiso</p></div>";
				}
				
				foreach($importes AS $cadena=>$monto){
					$temp  = explode('_._',$cadena);
					//$iva   = $ivas[$cadena];
					//print_r($temp);
					$error.=$this->chequeapresup($temp[0],$temp[1],$temp[2],'',$monto,0,'round($monto,2) > $disponible=round(($comprometido-$causado),2)','El Monto ($monto) es mayor al posible ($disponible) para descomprometer para la partida ('.$temp[0].' ('.$temp[1].') ('.$temp[2].')');
				}
			}

			if(empty($error)){
				log_message('error', 'empty error chequepresup');
				foreach($importes AS $cadena=>$monto){
					$temp  = explode('_._',$cadena);
					//$iva   = $ivas[$cadena];
					$error.=$this->afectapresup($temp[0],$temp[1],$temp[2],'',$monto,0, -1 ,array("comprometido"));
				}

				//if(empty($error)){
				//	foreach($admfondo AS $cadena=>$monto){
				//		$temp  = explode('_._',$cadena);
				//		$error.=$this->afectapresup($temp[0],$temp[1],'PARTIDAIVA','',$monto,0, -1 ,array("comprometido"));
				//	}
				//}

				if(empty($error)){
					log_message('error', 'empty error afectapresup');
					
					if($this->datasis->traevalor('USACERTIFICADO')=='S'){
						log_message('error', 'usacertificado');
						
						if($certificado>0){
							log_message('error', 'certificado>0');	
							$this->cd_precomprometer($certificado,false);
						}
						
					}
					$do->set('status','P');
					$do->save();
				}

				$anular  =false;
			}
		}else{
			$error.="<div class='alert'><p>No se puede realizar la operacion para esta orden de compra $id</p></div>";
		}

		if(empty($error))
			return '';
		else
			return $error;
	}

	function ca_reversar($id){
		$this->rapyd->load('dataobject');

		$ord = new DataObject("ordinal");

		$do = new DataObject("ocompra");
		$do->rel_one_to_many('itocompra', 'itocompra', array('numero'=>'numero'));
		$do->load($id);

		$fcausado    = $do->get('fcausado');
		$error='';
		$sta=$do->get('status');
		if($sta=="T"){

			$importes=array(); $ivas=array();$admfondo=array();
			for($i=0;$i < $do->count_rel('itocompra');$i++){
				$codigoadm   = $do->get_rel('itocompra','codigoadm',$i);
				$fondo       = $do->get_rel('itocompra','fondo'    ,$i);
				$codigopres  = $do->get_rel('itocompra','partida',$i);
				$importe     = $do->get_rel('itocompra','importe',$i);
				$iva         = $do->get_rel('itocompra','iva'    ,$i);
				$ordinal     = $do->get_rel('itocompra','ordinal',$i);
				$ivan        = $importe*$iva/100;

				$error.=$this->itpartida($codigoadm,$fondo,$codigopres,$ordinal);

				$cadena = $codigoadm.'_._'.$fondo.'_._'.$codigopres;//.'_._'.$ordinal;

				if(array_key_exists($cadena,$importes)){
					$importes[$cadena] +=$importe;
					//$ivas[$cadena]      =$iva;
				}else{
					$importes[$cadena]  =$importe;
					//$ivas[$cadena]      =$iva;
				}

				$cadena2 = $codigoadm.'_._'.$fondo;
				$admfondo[$cadena2]=(array_key_exists($cadena2,$admfondo)?$admfondo[$cadena2]+=$ivan:$admfondo[$cadena2] =$ivan);
			}

			if(empty($error)){
				//foreach($admfondo AS $cadena=>$monto){
				//	$temp  = explode('_._',$cadena);
				//	$error.=$this->chequeapresup($temp[0],$temp[1],'PARTIDAIVA','',$monto,0,'round($monto,2) > $disponible=round(($causado-$opago),2)','El Monto ($monto) es mayor al disponible (round(($disponible),2)) para descausar para la partida de IVA, ('.$temp[0].')('.$temp[1].') ');
				//}

				foreach($importes AS $cadena=>$monto){
					$temp  = explode('_._',$cadena);
					//$iva   = $ivas[$cadena];
					$error.=$this->chequeapresup($temp[0],$temp[1],$temp[2],'',$monto,0,'round($monto,2) > $disponible=round(($causado-$opago),2)','El Monto ($monto) es mayor al posible ($disponible) para deshacer causado para la partida ('.$temp[0].' ('.$temp[1].') ('.$temp[2].')');
				}
			}

			if(empty($error)){
				foreach($importes AS $cadena=>$monto){
					$temp  = explode('_._',$cadena);
					//$iva   = $ivas[$cadena];
					$error.=$this->afectapresup($temp[0],$temp[1],$temp[2],'',$monto,0, -1 ,array("causado"));
				}

				//if(empty($error)){
				//	foreach($admfondo AS $cadena=>$monto){
				//		$temp  = explode('_._',$cadena);
				//		$error.=$this->afectapresup($temp[0],$temp[1],'PARTIDAIVA','',$monto,0, -1 ,array("causado"));
				//	}
				//}

				if(empty($error)){
					$do->set('status','C');
					$do->save();
				}
			}

			if(empty($error)){

			}
		}

		if(empty($error)){
			//$this->sp_presucalc($codigoadm);
			return '';
		}
		else
			return $error;
	}

	function ca_anular($id,$redi=true){
		$this->rapyd->load('dataobject');

		$error='';

		$do = new DataObject("ocompra");
		$do->load($id);
		$fcausado = $do->get('fcausado');

		$error.=$this->ca_reversar($id);
		if(empty($error))
			$error.=$this->co_reversar($id);
		if(empty($error)){
			$do->set('status','A');
			$do->set('anulado',date('Ymd'));
			$do->save();
		}
		logusu('ocompra',"Anulo Orden de Compra Nro $id");		

		if(empty($error)){
			logusu('causacion',"Reverso Causacion Orden de Compra Nro $id");
			redirect("presupuesto/causacion/dataedit/show/$id");
		}else{
			logusu('causacion',"reverso Causacion Orden de Compra Nro $id. con ERROR:$error ");
			$data['content'] = $error.anchor("presupuesto/causacion/dataedit/show/$id",'Regresar');
			$data['title']   = " Causar Orden de Compra ";
			$data["head"]    = $this->rapyd->get_head().script('jquery.js').script("plugins/jquery.numeric.pack.js");
			$this->load->view('view_ventanas', $data);
		}
	}

	function op_reversar($id,$creanueva=false){

		$this->rapyd->load('dataobject');

		$odirect  = new DataObject("odirect");
		$odirect -> rel_one_to_many('pacom', 'pacom', array('numero'=>'pago'));
		$odirect -> load($id);

		$ocompra   =  new DataObject("ocompra");
		$ocompra   -> rel_one_to_many('pacom'    , 'pacom'     ,array('numero'=>'compra'));
		$ocompra   -> rel_one_to_many('itocompra', 'itocompra' ,array('numero'=>'numero'));

		$presup = new DataObject("presupuesto");
		$partidaiva=$this->datasis->traevalor("PARTIDAIVA");

		$error   = "";

		$fopago = $odirect->get('fopago');

		$sta=$odirect->get('status');
		if(($sta=="F2")){
			if(empty($error)){
				$p_ivaa=$p_ivag=$p_ivar=$p_reteiva=$p_reten=$p_total=$p_exento=0;
				$ordenes=array();

				for($i=0;$i   < $odirect->count_rel('pacom');$i++){
					$compra     = $odirect->get_rel('pacom','compra'   ,$i);
					$monto      = $odirect->get_rel('pacom','monto'    ,$i);
					$ordenes[]  = $compra;

					$p_total+=$monto;

					$ocompra ->load($compra);

					$ivaa           =  $ocompra->get('ivaa');
					$ivag           =  $ocompra->get('ivag');
					$ivar           =  $ocompra->get('ivar');
					$subtotal       =  $ocompra->get('subtotal');
					$reteiva        =  $ocompra->get('reteiva');
					$reteiva_prov   =  $ocompra->get('reteiva_prov');
					$reten          =  $ocompra->get('reten');
					$creten         =  $ocompra->get('creten');
					$exento         =  $ocompra->get('exento');
					$fcausado       =  $ocompra->get('fcausado');
					$ivan           =  $ivag+$ivar+$ivaa;
					$total          =  ($subtotal - $reten)+($ivan-($reteiva));

					$abonado=0;

					if(true ){//la orden de compra e //0 == $a
						$p_ivaa      -=  $ivaa   ;
						$p_ivag      -=  $ivag   ;
						$p_ivar      -=  $ivar   ;
						$p_reteiva   -=  $reteiva;
						$p_reten     -=  $reten  ;
						$p_exento    -=  $exento ;

						$ivan=0;
						for($j=0;$j    < $ocompra->count_rel('itocompra');$j++){
							$codigoadm   = $ocompra->get_rel('itocompra','codigoadm',$j);
							$fondo       = $ocompra->get_rel('itocompra','fondo'    ,$j);
							$codigopres  = $ocompra->get_rel('itocompra','partida'  ,$j);
							$importe     = $ocompra->get_rel('itocompra','importe'  ,$j);
							$iva         = $ocompra->get_rel('itocompra','iva'      ,$j);
							$ordinal     = $ocompra->get_rel('itocompra','ordinal'  ,$j);
							$ivan        = $importe*$iva/100;

							$error.=$this->itpartida($codigoadm,$fondo,$codigopres,$ordinal);

							$cadena = $codigoadm.'_._'.$fondo.'_._'.$codigopres.'_._'.$ordinal.'_._'.$iva;
							if(array_key_exists($cadena,$importes)){
								$importes[$cadena]+=$importe;
								$ivas[$cadena]     =$iva;
							}else{
								$importes[$cadena]  =$importe;
								$ivas[$cadena]      =$iva;
							}
							$cadena2 = $codigoadm.'_._'.$fondo;
							$admfondo[$cadena2]=(array_key_exists($cadena2,$admfondo)?$admfondo[$cadena2]+=$ivan:$admfondo[$cadena2] =$ivan);
						}
					}
				}
				if(empty($error)){
					//foreach($admfondo AS $cadena=>$monto){
					//	$temp  = explode('_._',$cadena);
					//	$error.=$this->chequeapresup($temp[0],$temp[1],'PARTIDAIVA','',$monto,0,'round($monto,2) > $disponible=round(($opago-$pagado),2)','El Monto ($monto) es mayor al disponible (round(($disponible),2)) para deshacer ordenar pago para la partida de IVA, ('.$temp[0].')('.$temp[1].') ');
					//}

					foreach($importes AS $cadena=>$monto){
						$temp  = explode('_._',$cadena);
						$iva   = $ivas[$cadena];
						$error.=$this->chequeapresup($temp[0],$temp[1],$temp[2],$temp[3],$monto,$iva,'round($monto,2) > $disponible=round(($opago-$pagado),2)','El Monto ($monto) es mayor al posible a deshacer ordenar pago ($disponible) para la partida ('.$temp[0].' ('.$temp[1].') ('.$temp[2].')');
					}
				}

				if(empty($error)){
					foreach($importes AS $cadena=>$monto){
						$temp  = explode('_._',$cadena);
						$iva   = $ivas[$cadena];
						$error.=$this->afectapresup($temp[0],$temp[1],$temp[2],$temp[3],$monto,$iva, -1 ,array("opago"));
					}

					//if(empty($error)){
					//	foreach($admfondo AS $cadena=>$monto){
					//		$temp  = explode('_._',$cadena);
					//		$error.=$this->afectapresup($temp[0],$temp[1],'PARTIDAIVA','',$monto,0, -1 ,array("opago"));
					//	}
					//}
				}

        if(empty($error)){
					$odirect->set('ivag'    , 0   );
					$odirect->set('ivar'    , 0   );
					$odirect->set('ivaa'    , 0   );
					$odirect->set('reten'   , 0   );
					$odirect->set('creten'  , 0   );
					$odirect->set('reteiva' , 0   );
					$odirect->set('exento'  , 0   );
					$odirect->set('status'  , 'F1');

					$odirect->save();
					$ordenes = implode("','",$ordenes);
					$this->db->simple_query("UPDATE ocompra SET status='T' WHERE numero IN ('$ordenes')");
        }

        if(empty($error))
        	return '';
        else
        	return $error;
      }
		}elseif($sta=="H2"){

			if(empty($error)){
				for($i=0;$i   < $odirect->count_rel('pacom');$i++){
					$compra     = $odirect->get_rel('pacom','compra' ,$i);
					$monto      = $odirect->get_rel('pacom','monto'  ,$i);

					$ocompra ->load($compra);

					$abonado   =  $ocompra->get('abonado');

					$this->db->simple_query("UPDATE ocompra SET abonado=abonado-$monto WHERE numero=$compra");
					//$ocompra->set('abonado',$abonado-$monto);
					//$ocompra->save();
				}
			}
			$odirect->set('status' , 'H1' );
			$odirect->save();
		}else{
			$error.="<div class='alert'><p>No se Puede Completar la operacion s</p></div>";
		}

		if(empty($error))
			return '';
		else
			return $error;
	}

	function op_anular($id,$redi=true){
		$this->rapyd->load('dataobject');

		$do  = new DataObject("odirect");
		$do->load($id);
		$status = $do->get('status');
		$do->set('fanulado',date('Ymd'));

		if($status=="F2")
			$error=$this->op_reversar($id,true);

		if(empty($error)){
			$do->set('status','FA');
			$do->save();
			logusu('opago',"Anulo Orden de Pago Nro $id");
			if($redi)redirect("presupuesto/opago/dataedit/show/$id");
		}else{
			logusu('opago',"Anulo Orden de Pago (de Compra) Nro $id. con ERROR:$error ");
			$data['content'] = $error.anchor("presupuesto/opago/dataedit/show/$id",'Regresar');
			$data['title']   = " Orden de Pago ";
			$data["head"]    = $this->rapyd->get_head().script('jquery.js').script("plugins/jquery.numeric.pack.js");
			$this->load->view('view_ventanas', $data);
		}
	}

	function pd_reversar($id){
		$this->rapyd->load('dataobject');

		$do = new DataObject("odirect");
		$do->rel_one_to_many('itodirect', 'itodirect', array('numero'=>'numero'));
		$do->load($id);

		$error      = "";
		$fopago     = $do->get('fopago');

		if(empty($error)){
			$sta=$do->get('status');
			if($sta=="B2"){
				$importes=array(); $ivas=array();$admfondo=array();
				for($i=0;$i  < $do->count_rel('itodirect');$i++){
					$codigoadm   = $do->get_rel('itodirect','codigoadm',$i);
					$fondo       = $do->get_rel('itodirect','fondo'    ,$i);
					$codigopres  = $do->get_rel('itodirect','partida'  ,$i);
					$iva         = $do->get_rel('itodirect','iva'      ,$i);
					$importe     = $do->get_rel('itodirect','importe'  ,$i);
					$ordinal     = $do->get_rel('itodirect','ordinal'  ,$i);
					$ivan        = $importe*$iva/100;

					$error.=$this->itpartida($codigoadm,$fondo,$codigopres,$ordinal);

					$cadena = $codigoadm.'_._'.$fondo.'_._'.$codigopres;

					if(array_key_exists($cadena,$importes)){
						$importes[$cadena]+=$importe;
//						$ivas[$cadena.'_._'.$iva]     =$iva;
					}else{
						$importes[$cadena]  =$importe;
//						$ivas[$cadena.'_._'.$iva]      =$iva;
					}
					$cadena2 = $codigoadm.'_._'.$fondo;
					$admfondo[$cadena2]=(array_key_exists($cadena2,$admfondo)?$admfondo[$cadena2]+=$ivan:$admfondo[$cadena2] =$ivan);
				}
				if(empty($error)){
					//$error.=$this->chequeapresup($codigoadm,$fondo,'PARTIDAIVA','',$ivan,0,'round($monto,2) > round(($opago-$pagado),2)','El Monto ($monto) es mayor al disponible (disponible) para la partida de IVA');
					//foreach($admfondo AS $cadena=>$monto){
					//	$temp  = explode('_._',$cadena);
					//	$error.=$this->chequeapresup($temp[0],$temp[1],'PARTIDAIVA','',$monto,0,'round($monto,2) > $disponible=round(($opago-$pagado),2)','El Monto ($monto) es mayor al disponible (round(($disponible),2)) para deshacer ordenado pago la partida de IVA, ('.$temp[0].')('.$temp[1].') ');
					//}

					foreach($importes AS $cadena=>$monto){
						$temp  = explode('_._',$cadena);
						//$iva   = $ivas[$cadena];
						$error.=$this->chequeapresup($temp[0],$temp[1],$temp[2],'',$monto,0,'round($monto,2) > $disponible=round(($opago-$pagado),2)','El Monto ($monto) es mayor al posible ($disponible) para deshacer ordenado pago para la partida ('.$temp[0].') ('.$temp[1].') ('.$temp[2].')');
					}
				}

				if(empty($error)){
					//$ivan = 0;
					//for($i=0;$i  < $do->count_rel('itodirect');$i++){
					//	$codigopres  = $do->get_rel('itodirect','partida',$i);
					//	$iva         = $do->get_rel('itodirect','iva'    ,$i);
					//	$importe     = $do->get_rel('itodirect','importe',$i);
					//	$ordinal     = $do->get_rel('itodirect','ordinal',$i);
					//	$ivan       += $importe*$iva/100;
					//
					//	//$error.=$this->afectapresup($codigoadm,$fondo,$codigopres,$ordinal,$importe,$iva, -1 ,array("comprometido","causado","opago"));
					//}

					foreach($importes AS $cadena=>$monto){
						$temp  = explode('_._',$cadena);
						//$iva   = $ivas[$cadena];
						$error.=$this->afectapresup($temp[0],$temp[1],$temp[2],'',$monto,0, -1 ,array("comprometido","causado","opago"));
					}
				}

				//if(empty($error)){
				//	foreach($admfondo AS $cadena=>$monto){
				//		$temp  = explode('_._',$cadena);
				//		$error.=$this->afectapresup($temp[0],$temp[1],'PARTIDAIVA','',$monto,0, -1 ,array("comprometido","causado","opago"));
				//	}
				//}

				//if(empty($error)){
				//	if(date('m',strtotime($fopago)) != date('m')){
                //
				//		$odirect  = new DataObject("odirect");
				//		$odirect  = $do;
				//		$odirect->unset_pk();
				//		$odirect->loaded=0;
				//		$odirect->set('status'   ,'BX'       );
				//		$odirect->set('reverso'  ,$id        );
				//		$odirect->set('fopago'   ,date('Ymd'));
				//		$odirect->set('fecha'    ,date('Ymd'));
				//		$n =$this->datasis->fprox_numero('nodirect');
				//		$odirect->set('numero'   ,$n         );
				//		$odirect->save();
				//	}
				//}
			}
		}

		//$this->sp_presucalc($codigoadm);
		if(empty($error))
			return '';
		else
			return $error;
	}

	function pd_anular($id,$redi=true){
		$this->rapyd->load('dataobject');

		$odirect  = new DataObject("odirect");
		$odirect -> load($id);
		$fopago   = $odirect->get('fopago');
		$status   = $odirect->get('status');

		$this->redirect = false;

		if($status=='B2'){
			$odirect->set('fanulado',date('Ymd'));
			$error=$this->pd_reversar($id);
			if(empty($error)){
				$query="UPDATE `ocomrapid` SET `status`='P',`opago`='' WHERE opago='$id'";
				$this->db->simple_query($query);

				//if(date('m',strtotime($fopago)) != date('m')){
				//	$odirect->set('status','BY');
				//}else{
					$odirect->set('status','BA');
				//}

				$odirect->save();
			}
		}elseif($status=='B1'){
			$odirect->set('fanulado',date('Ymd'));
			$odirect->set('status','BA');
			$odirect->save();
		}

		if(empty($error)){
			logusu('odirect',"Anulo Orden de Pago Directo Nro $id");
			if($redi)redirect("presupuesto/odirect/dataedit/show/$id");
		}else{
			logusu('odirect',"Anulo Orden de Pago Directo Nro $id. con ERROR:$error ");
			$data['content'] = $error.anchor("presupuesto/odirect/dataedit/show/$id",'Regresar');
			$data['title']   = " Orden de Pgo Directo ";
			$data["head"]    = $this->rapyd->get_head();
			$this->load->view('view_ventanas', $data);
		}
	}

	function an_reversar($id){
		$this->rapyd->load('dataobject');
		$do  = new DataObject("odirect");
		$do->load($id);

		$error      = "";

		$status = $do->get('status');
		if($status == "G2"){
			$do->set('status'  ,'G1');

			$cajach = $do->get('cajach');
			if(!empty($cajach)){
				$total  = $do->get("total");

				$cchica = new DataObject("cajach");
				$cchica->load($cajach);
				$activo = $cchica->get('activo');
				$maximo = $cchica->get('maximo');
				$saldo  = $cchica->get('saldo' );
				$nombre = $cchica->get('nombre');

				if($activo != 'S')
					$error.="<div class='alert'><p>La caja chica ($cajach) $nombre esta inactivo</p></div>";

				if($total>$maximo-$saldo)
					$error.="<div class='alert'><p>El monto del anticipo ($total) es mayor al disponible (".($saldo).") para la caja chica ($cajach) $nombre</p></div>";

				if(empty($error)){
					$cchica->set('saldo',$saldo+$total);
					$cchica->save();
				}
		}else{
			$error.="<div class='alert'><p>No se puede Realizar la operaci&oacute;n</p></div>";
		}

		if(empty($error)){
			$do->save();
			logusu('anti',"Reverso fondo en avance Nro $id");
			if($this->redirect)redirect("presupuesto/anti/dataedit/show/$id");
		}else{
			$data['content'] = $error.anchor("presupuesto/anti/dataedit/show/$id",'Regresar');
			$data['title']   = " Fondo en Avance ";
			$data["head"]    = $this->rapyd->get_head().script('jquery.js').script("plugins/jquery.numeric.pack.js");
			$this->load->view('view_ventanas', $data);
		}
	}
	}

	function an_anular($id,$redi=true){
		$this->rapyd->load('dataobject');

		$odirect  = new DataObject("odirect");
		$odirect -> load($id);

		$this->redirect = false;
		$this->an_reversar($id);

		$odirect->set('status','GA');
		$odirect->save();

		logusu('anti',"Anulo Fondo en Avance Nro $id");
		if($redi)redirect("presupuesto/anti/dataedit/show/$id");
	}

	function po_reversar($id){
		$this->rapyd->load('dataobject');

		$do  = new DataObject("odirect");
		$do->load($id);
		$obr        = $do->get('obr');
		$factura    = $do->get('factura'      );
		$controlfac = $do->get('controlfac'   );
		$fechafac   = $do->get('fechafac'     );
		$reteiva    = $do->get('reteiva'      );
		$ivaa       = $do->get('ivaa');
		$ivag       = $do->get('ivag');
		$ivar       = $do->get('ivar');
		$iva        = $do->get('iva' );
		$amortiza   = $do->get('amortiza' );
		$total      = $do->get('total'    );
		$total2     = $do->get('total2'   );
		$fopago     = $do->get('fopago'   );

		$obra = new DataObject("obra");
		$obra->load($obr);
		$codigoadm  = $obra->get('codigoadm' );
		$fondo      = $obra->get('fondo'     );
		$codigopres = $obra->get('codigopres');
		$ordinal    = $obra->get('ordinal'   );
		$pagado     = $obra->get('pagado'    );

		$error      = "";

		$presup = new DataObject("presupuesto");

		if(empty($error)){
			$sta=$do->get('status');
			if($sta=="O2"){

				$mont        = $total2-$amortiza;

				$error.=$this->chequeapresup($codigoadm,$fondo,$codigopres,$ordinal,$mont,0,'round($monto,2) > round(($opago-$pagado),2)',"El Monto ($mont) es mayor al disponible para la partida ($codigoadm) ($fondo) ($codigopres)");

				if(empty($error)){
					//if(date('m',strtotime($fopago)) != date('m')){
					//	$odirect  = new DataObject("odirect");
					//	$odirect  = $do;
					//	$odirect->unset_pk();
					//	$odirect->loaded=0;
					//	$odirect->set('status'   ,'OX'       );
					//	$odirect->set('reverso'  ,$id        );
					//	$odirect->set('fopago'   ,date('Ymd'));
					//	$n =$this->datasis->fprox_numero('nodirect');
					//	$odirect->set('numero'   ,$n         );
					//	$odirect->save();
					//}
				}

				if(empty($error))
					$error.=$this->afectapresup($codigoadm,$fondo,$codigopres,$ordinal,$mont,0, -1 ,array("causado","opago"));

				if(empty($error)){
					$existe = $this->datasis->dameval("SELECT COUNT(*) FROM odirect WHERE status = 'O2' AND obr=$obr");
					$obra->set('pagado',$pagado-$mont);
					if($existe == 1){
						$obra->set('status','O2');
					}
					$obra->save();

					$do->set('status','O1');
					$do->save();
				}
			}
		}

		if(empty($error)){
			//$this->sp_presucalc($codigoadm);
			return '';
		}else{
			return $error;
		}
	}

	function po_anular($id){
		$this->rapyd->load('dataobject');

		$error='';

		$odirect  = new DataObject("odirect");
		$odirect -> load($id);
		$fopago   =$odirect->get('fopago');
		$status   =$odirect->get('status');

		if($status=='O2'){
			$this->redirect = false;
			$error .= $this->po_reversar($id);

			$odirect->set('fanulado',date('Ymd'));
			if(empty($error)){
				//if(date('m',strtotime($fopago)) != date('m')){
				//	$odirect->set('status','OY');
				//	logusu('pobra',"Anulo Pago de Obra Nro $id con reverso");
				//}else{
				//	$odirect->set('status','OA');
				//	logusu('pobra',"Anulo Pago de Obra Nro $id");
				//}
				$odirect->set('status','OA');
				$odirect->save();
			}
		
		}elseif($status=='O1'){
			$odirect->set('fanulado',date('Ymd'));
			$odirect->set('status','OA');
			$odirect->save();
		}

		if(empty($error)){
			redirect("presupuesto/pobra/dataedit/show/$id");
		}else{
			logusu('pobra',"Anulo Pago de Obra Nro $id con error $error");
			$data['content'] = $error.anchor("presupuesto/pobra/dataedit/show/$id",'Regresar');
			$data['title']   = " Pago de Obras ";
			$data["head"]    = $this->rapyd->get_head();
			$this->load->view('view_ventanas', $data);
		}
	}

	function riva($nriva,$codigoadm='',$fondo='', $ocompra='',$odirect,$itfact='',$numero,$nfiscal,$ffactura,$clipro,$exento,$tasa,$general,$tasaadic,$adicional,$tasaredu,$reducida,$reiva,$codbanc,$mbanc,$reteiva_prov,$rendi='',$geneimpu=null,$adicimpu=null,$reduimpu=null){
		
		$this->rapyd->load('dataobject');
		$riva = new dataobject("riva");

		$clipro2 = $this->db->escape($clipro );
		//$codbanc2= $this->db->escape($codbanc);
		$sprv   = $this->datasis->damerow("SELECT nombre,rif FROM sprv WHERE proveed = $clipro2     ");
		//$banc   = $this->datasis->damerow("SELECT banco,numcuent FROM banc WHERE codbanc = $codbanc2");

		$nro = $this->datasis->dameval("SELECT nrocomp FROM riva WHERE numero='$numero' AND nfiscal='$nfiscal' AND  status='B' AND clipro='$clipro' and LENGTH(numero)>0 AND odirect<>'$odirect'");

		if($nro >0)return "<div class ='alert'>Ya existe una retencion activa ($nro) para el proveedor".$sprv['nombre']." y factura $numero control $nfiscal</div>";

		if(!$geneimpu)
		$geneimpu  = round($general*$tasa/100       ,2);
		if(!$adicimpu)
		$adicimpu  = round($tasaadic*$adicional/100 ,2);
		if(!$reduimpu)
		$reduimpu  = round($tasaredu*$reducida/100  ,2);

		$riva->set('nrocomp'       , $nriva                           );
		$riva->set('codigoadm'     , $codigoadm                       );
		$riva->set('fondo'         , $fondo                           );
		$riva->set('ocompra'       , $ocompra                         );
		$riva->set('odirect'       , $odirect                         );
		$riva->set('itfac'         , $itfact                          );
		$riva->set('rendi'         , $rendi                           );
		$riva->set('emision'       , date('Ymd')                      );
		$riva->set('periodo'       , date('Ym')                       );
		$riva->set('tipo_doc'      , 'FC'                             );
		$riva->set('fecha'         , date('Ymd')                      );
		$riva->set('numero'        , $numero                          );
		$riva->set('ffactura'      , $ffactura                        );
		$riva->set('nfiscal'       , $nfiscal                         );
		$riva->set('clipro'        , $clipro                          );
		$riva->set('nombre'        , $sprv['nombre']                  );
		$riva->set('rif'           , $sprv['rif']                     );
		$riva->set('exento'        , $exento                          );
		$riva->set('tasa'          , $tasa                            );
		$riva->set('general'       , $general                         );
		$riva->set('geneimpu'      , $geneimpu                        );
		$riva->set('tasaadic'      , $tasaadic                        );
		$riva->set('adicional'     , $adicional                       );
		$riva->set('adicimpu'      , $adicimpu                        );
		$riva->set('tasaredu'      , $tasaredu                        );
		$riva->set('reducida'      , $reducida                        );
		$riva->set('reduimpu'      , $reduimpu                        );
		$riva->set('stotal'        , $general+$adicional+$reducida    );
		$riva->set('impuesto'      , $geneimpu+$adicimpu+$reduimpu    );
		$riva->set('gtotal'        , $general+$adicional+$reducida+$geneimpu+$adicimpu+$reduimpu);
		$riva->set('reiva'         , $reiva                           );
		$riva->set('status'        , 'B'                              );
		//$riva->set('banc'          , $banc['banco']                   );
		//$riva->set('numcuent'      , $banc['numcuent']                );
		//$riva->set('codbanc'       , $codbanc                         );
		//$riva->set('mbanc'         , $mbanc                           );
		$riva->set('reteiva_prov'  , $reteiva_prov                    );

		$riva->save();


		//return $riva->get('nrocomp');
	}

	function chriva($nriva,$codigoadm='',$fondo='', $ocompra='',$odirect,$itfact='',$numero,$nfiscal,$ffactura,$clipro,$exento,$tasa,$general,$tasaadic,$adicional,$tasaredu,$reducida,$reiva,$codbanc,$mbanc,$reteiva_prov,$rendi=''){

		$this->rapyd->load('dataobject');
		$riva = new dataobject("riva");

		$clipro2 = $this->db->escape($clipro );
		//$codbanc2= $this->db->escape($codbanc);
		$sprv   = $this->datasis->damerow("SELECT nombre,rif FROM sprv WHERE proveed = $clipro2     ");
		//$banc   = $this->datasis->damerow("SELECT banco,numcuent FROM banc WHERE codbanc = $codbanc2");

		$nro = $this->datasis->dameval("SELECT nrocomp FROM riva WHERE numero='$numero' AND nfiscal='$nfiscal' AND  status='B' AND clipro='$clipro' AND LENGTH(numero)>0 AND 1*odirect<>1*$odirect ");

		if($nro >0)return "<div class ='alert'>Ya existe una retencion activa ($nro) para el proveedor".$sprv['nombre']." y factura $numero control $nfiscal</div>";

	}

	function pm_anular($id,$redi=true){
		$this->rapyd->load('dataobject');

		$odirect  = new DataObject("odirect");
		$odirect -> load($id);
		$fopago   = $odirect->get('fopago');
		$status   = $odirect->get('status');

		$this->redirect = false;
		$error=$this->pm_reversar($id);

		$odirect->set('fanulado',date('Ymd'));
		if(empty($error)){
			//if(date('m',strtotime($fopago)) != date('m')){
			//	$odirect->set('status','MY');
			//}else{
				$odirect->set('status','MA');
			//}

			$odirect->save();
		}else{
			$error.="<div class='alert'>Ocurrio un Problema al Momento de Reversar La orden</div>";
		}

		if(empty($error)){
			logusu('odirect',"Anulo Orden de Pago Monetario Nro $id");
			if($redi)redirect("presupuesto/pagomonetario/dataedit/show/$id");
		}else{
			logusu('odirect',"Anulo Orden de Pago Monetario Nro $id. con ERROR:$error ");
			$data['content'] = $error.anchor("presupuesto/pagomonetario/dataedit/show/$id",'Regresar');
			$data['title']   = " Orden de Pgo Directo ";
			$data["head"]    = $this->rapyd->get_head();
			$this->load->view('view_ventanas', $data);
		}
	}

	function pm_reversar($id){
		$error      = "";

		$this->rapyd->load('dataobject');

		$do  = new DataObject("odirect");
		$do->load($id);
		//$factura    = $do->get('factura'      );
		//$controlfac = $do->get('controlfac'   );
		//$fechafac   = $do->get('fechafac'     );
		$reteiva    = $do->get('reteiva'      );
		$ivaa       = $do->get('ivaa');
		$ivag       = $do->get('ivag');
		$ivar       = $do->get('ivar');
		$iva        = $do->get('iva' );
		$total      = $do->get('total'    );
		$total2     = $do->get('total2'   );
		$status     = $do->get('status'   );

		//if($reteiva > 0 && (empty($factura) || empty($controlfac) || empty($fechafac)))
		//	$error.="<div class='alert'><p> Los campos Nro. Factura, Nro Control y Fecha factura no pueden estar en blanco</p></div>";

		if(empty($error)){
			if($status=="M2" ){
				$do->set('status','M1');
				$do->save();
			}else{
				$error.="<div class='alert'><p>Este Pago No puede ser Reversado</p></div>";
			}
		}

		if(!empty($error))
			return $error;
	}

	function lislr(){
		$cod_prov = $this->input->post('cod_prov');
		if(!empty($cod_prov)){
			$cod_prov = $this->db->escape($cod_prov);

			$tipo     = $this->datasis->dameval("SELECT tipo FROM sprv WHERE proveed=$cod_prov");

			SWITCH($tipo){
			case 1:$tipo ="JD";break;
			case 2:$tipo ="NR";break;
			case 3:$tipo ="JD";break;
			case 4:$tipo ="NN";break;
			//case 5:$tipo ="Excluido del Libro de Compras";break;
			//case 0:$tipo ="Inactivo";break;
			}

			$query = $this->db->query("SELECT codigo,CONCAT_WS(' ',codigo,activida)descrip FROM rete WHERE tipo='$tipo' ORDER BY codigo");
				if($query){
					if($query->num_rows()>0){
						foreach($query->result() AS $fila ){
							echo "<option value='".$fila->codigo."'>".$fila->descrip."</option>";
						}
					}else{
						echo "<option value=''>No hay registros disponibles</option>";
					}
				}
		}
	}

	function chexiste_factura($numero,$factura,$controlfac,$cod_prov,$d='',&$error){
		$cana=0;

		if(!empty($factura) || !empty($controlfac)){
			$controlfac = $this->db->escape($controlfac );
			$cod_prov   = $this->db->escape($cod_prov   );
			$factura    = $this->db->escape($factura    );
			$numero     = $this->db->escape($numero     );

			$query = "SELECT SUM(a) FROM (
			SELECT COUNT(*)a FROM odirect WHERE (controlfac =$controlfac AND factura=$factura) AND cod_prov=$cod_prov AND MID(status,2,1) IN ('2','3') ".($d=='B'?"AND numero<>$numero":"")."
			UNION ALL
			SELECT COUNT(*) FROM ocompra WHERE (controlfac =$controlfac AND factura=$factura) AND cod_prov=$cod_prov  AND status IN ('T','E','O') ".($d=='F'?"AND numero<>$numero":"")."
			UNION ALL
			SELECT COUNT(*) FROM itfac JOIN odirect ON odirect.numero = itfac.numero WHERE MID(status,2,1) IN ('2','3') AND (itfac.controlfac =$controlfac AND itfac.factura=$factura) AND cod_prov=$cod_prov ".($d=='B'?"AND odirect.numero<>$numero":"")."
			UNION ALL
			SELECT COUNT(*) FROM itrendi a JOIN rendi b ON a.numero=b.numero WHERE (a.controlfac =$controlfac AND a.factura=$factura) AND b.status IN ('C') ".($d=='R'?"AND a.numero<>$numero":"")."
			)a";
			$cana=$this->datasis->dameval($query);
		}

		if($cana>0){
			$nombre = $this->datasis->dameval("SELECT nombre FROM sprv WHERE proveed=$cod_prov");
			$error="La Factura o el Control Fiscal Ya existen para el Beneficiario ($cod_prov) $nombre ";
		}else{
			$error='';
		}
	}

	function creambanc($codbanc,$monto,$cheque,$tipo_doc,$observa,$fecha,$cod_prov,$status,$id,$bcta='',$desem='',$benefi='', $liable=NULL, $fliable=NULL,$destino=NULL,$observa2=NULL){

		$this->rapyd->load('dataobject');
		$mbanc = new DataObject("mbanc");
		$mbanc->set('codbanc' ,$codbanc );
		$mbanc->set('monto'   ,$monto   );
		$mbanc->set('cheque'  ,$cheque  );
		$mbanc->set('tipo_doc',$tipo_doc);
		$mbanc->set('observa' ,$observa );
		$mbanc->set('fecha'   ,$fecha   );
		$mbanc->set('cod_prov',$cod_prov);
		$mbanc->set('status'  ,$status  );
		$mbanc->set('bcta'    ,$bcta    );
		$mbanc->set('desem'   ,$desem   );
		$mbanc->set('benefi'  ,$benefi  );
		$mbanc->set('liable'  ,$liable  );
		$mbanc->set('fliable' ,$fliable );
		$mbanc->set('destino' ,$destino );
		$mbanc->set('observa2',$observa2);
		$mbanc->save();
		return $mbanc->get('id');
	}

	function chbanse($codbanc,$fecha){
		$codbance=$this->db->escape($codbanc);
		$fechae  =$this->db->escape($fecha);


		$ejercicio=$this->datasis->traevalor('EJERCICIO');
		$c =$this->datasis->dameval("SELECT COUNT(*) FROM bancse WHERE codbanc=$codbance AND mes=MONTH($fechae) AND YEAR($fechae)=$ejercicio");
		if(($c>0))
		return 'El mes ya esta cerrado y no se pueden hacer movimientos del mismo. Comuniquese con el Administrador de Bancos';
	}

	function chcasise($fecha){
		$fechae  =$this->db->escape($fecha);

		$c =$this->datasis->dameval("SELECT COUNT(*) FROM casise WHERE mes=MONTH($fechae) AND YEAR($fechae)");
		if(($c>0))
		return 'La Contabilidad del mes ya esta cerrada y no se pueden hacer movimientos del mismo. Comuniquese con el Jefe de Contabilidad';
	}

	function pc_reversar($id){
		$this->rapyd->load('dataobject');

		$do = new DataObject("odirect");
		$do->rel_one_to_many('itodirect', 'itodirect', array('numero'=>'numero'));
		$do->load($id);

		$error      = "";
		$fopago     = $do->get('fopago');

		if(empty($error)){
			$sta=$do->get('status');
			if($sta=="C2"){
				$importes=array(); $ivas=array();$admfondo=array();
				for($i=0;$i  < $do->count_rel('itodirect');$i++){
					$codigoadm   = $do->get_rel('itodirect','codigoadm',$i);
					$fondo       = $do->get_rel('itodirect','fondo'    ,$i);
					$codigopres  = $do->get_rel('itodirect','partida'  ,$i);
					$iva         = $do->get_rel('itodirect','iva'      ,$i);
					$importe     = $do->get_rel('itodirect','importe'  ,$i);
					$ordinal     = $do->get_rel('itodirect','ordinal'  ,$i);
					$ivan        = $importe*$iva/100;

					$error.=$this->itpartida($codigoadm,$fondo,$codigopres,$ordinal);

					$cadena = $codigoadm.'_._'.$fondo.'_._'.$codigopres;

					if(array_key_exists($cadena,$importes)){
						$importes[$cadena]+=$importe;
//						$ivas[$cadena.'_._'.$iva]     =$iva;
					}else{
						$importes[$cadena]  =$importe;
//						$ivas[$cadena.'_._'.$iva]      =$iva;
					}
					$cadena2 = $codigoadm.'_._'.$fondo;
					$admfondo[$cadena2]=(array_key_exists($cadena2,$admfondo)?$admfondo[$cadena2]+=$ivan:$admfondo[$cadena2] =$ivan);
				}
				if(empty($error)){
					//$error.=$this->chequeapresup($codigoadm,$fondo,'PARTIDAIVA','',$ivan,0,'round($monto,2) > round(($opago-$pagado),2)','El Monto ($monto) es mayor al disponible (disponible) para la partida de IVA');
					//foreach($admfondo AS $cadena=>$monto){
					//	$temp  = explode('_._',$cadena);
					//	$error.=$this->chequeapresup($temp[0],$temp[1],'PARTIDAIVA','',$monto,0,'round($monto,2) > $disponible=round(($opago-$pagado),2)','El Monto ($monto) es mayor al disponible (round(($disponible),2)) para deshacer ordenado pago la partida de IVA, ('.$temp[0].')('.$temp[1].') ');
					//}

					foreach($importes AS $cadena=>$monto){
						$temp  = explode('_._',$cadena);
						//$iva   = $ivas[$cadena];
						$error.=$this->chequeapresup($temp[0],$temp[1],$temp[2],'',$monto,0,'round($monto,2) > $disponible=round(($opago-$pagado),2)','El Monto ($monto) es mayor al posible ($disponible) para deshacer ordenado pago para la partida ('.$temp[0].') ('.$temp[1].') ('.$temp[2].')');
					}
				}

				if(empty($error)){
					//$ivan = 0;
					//for($i=0;$i  < $do->count_rel('itodirect');$i++){
					//	$codigopres  = $do->get_rel('itodirect','partida',$i);
					//	$iva         = $do->get_rel('itodirect','iva'    ,$i);
					//	$importe     = $do->get_rel('itodirect','importe',$i);
					//	$ordinal     = $do->get_rel('itodirect','ordinal',$i);
					//	$ivan       += $importe*$iva/100;
					//
					//	//$error.=$this->afectapresup($codigoadm,$fondo,$codigopres,$ordinal,$importe,$iva, -1 ,array("comprometido","causado","opago"));
					//}

					foreach($importes AS $cadena=>$monto){
						$temp  = explode('_._',$cadena);
						//$iva   = $ivas[$cadena];
						$error.=$this->afectapresup($temp[0],$temp[1],$temp[2],'',$monto,0, -1 ,array("causado","opago"));
					}
				}

				//if(empty($error)){
				//	foreach($admfondo AS $cadena=>$monto){
				//		$temp  = explode('_._',$cadena);
				//		$error.=$this->afectapresup($temp[0],$temp[1],'PARTIDAIVA','',$monto,0, -1 ,array("comprometido","causado","opago"));
				//	}
				//}

				if(empty($error)){
					$do->set('status'   ,'C1');
					$do->save();
				}
			}
		}

		//$this->sp_presucalc($codigoadm);
		if(empty($error))
			return '';
		else
			return $error;
	}

	function pc_anular($id,$redi=true){
		$this->rapyd->load('dataobject');

		$odirect  = new DataObject("odirect");
		$odirect -> load($id);
		$fopago   = $odirect->get('fopago');
		$status   = $odirect->get('status');

		$this->redirect = false;

		if($status=='C2'){
			$odirect->set('fanulado',date('Ymd'));
			$error=$this->pc_reversar($id);
			if(empty($error)){
				//if(date('m',strtotime($fopago)) != date('m')){
				//	$odirect->set('status','BY');
				//}else{
					$odirect->set('status','CA');
				//}

				$odirect->save();
			}
		}elseif($status=='C1'){
			$odirect->set('status','CA');
			$odirect->set('fanulado',date('Ymd'));
			$odirect->save();
		}

		if(empty($error)){
			logusu('odirect',"Anulo Orden de Pago Directo Nro $id");
			if($redi)redirect("presupuesto/opagoctemp/dataedit/show/$id");
		}else{
			logusu('odirect',"Anulo Orden de Pago Directo Nro $id. con ERROR:$error ");
			$data['content'] = $error.anchor("presupuesto/odirect/dataedit/show/$id",'Regresar');
			$data['title']   = " Orden de Pgo Directo ";
			$data["head"]    = $this->rapyd->get_head();
			$this->load->view('view_ventanas', $data);
		}
	}
	
	function cd_precomprometer($numero,$redirect=true){
		$this->rapyd->load('dataobject');
		
		$error='';
	
		$do = new DataObject("cdisp");
		$do->rel_one_to_many('itcdisp', 'itcdisp', array('numero'=>'numero'));
		$do->load($numero);
		$status = $do->get('status');
	
		if($status=='P' || $status=='F'){
			$importes=array();
			for($i=0;$i < $do->count_rel('itcdisp');$i++){
				$codigoadm  = $do->get_rel('itcdisp','codigoadm' ,$i);
				$fondo      = $do->get_rel('itcdisp','fondo'     ,$i);
				$codigopres = $do->get_rel('itcdisp','codigopres',$i);
				$importe    = $do->get_rel('itcdisp','importe'   ,$i);
				$soli       = $do->get_rel('itcdisp','soli'      ,$i);
				
				$disponible = $this->datasis->dameval("SELECT saldo FROM v_presaldo WHERE codigoadm='$codigoadm' AND fondo='$fondo' AND codigo='$codigopres'");
				
				$do->set_rel('itcdisp','disp',$disponible,$i);
				
				$cadena = $codigoadm.'_._'.$fondo.'_._'.$codigopres;
				if(array_key_exists($cadena,$importes)){
					$importes[$cadena] +=$soli;
				}else{
					$importes[$cadena]  =$soli;
				}
			}
			
			if(empty($error)){
				foreach($importes AS $cadena=>$monto){
					$temp  = explode('_._',$cadena);
					$error.= $this->chequeapresup($temp[0],$temp[1],$temp[2],'',$monto,0,'round($monto,2) > $disponible=round(($presupuesto-($comprometido+$apartado)),2)','El Monto ($monto) es mayor al disponible ($disponible) para la partida ('.$temp[0].') ('.$temp[1].') ('.$temp[2].') ') ;
				}
			}
			//print_r($importes);
			//exit('Hello World'.$error);
			if(empty($error)){
				foreach($importes AS $cadena=>$monto){
					$temp  = explode('_._',$cadena);
					$error.=$this->afectapresup($temp[0],$temp[1],$temp[2],'',$monto,0, 1 ,array("apartado"));
				}
			}
		}else{
			$error.= "<div class='alert'>No se puede realizar la operacion para el certificado</div>";
		}
		
		if(empty($error)){
			if($status=='P'){
				$numero = $ncdisp = $this->datasis->fprox_numero('ncdisp');
				$do->set('numero',$ncdisp);
				//$do->pk    =array('numero'=>$ncdisp);
				for($i=0;$i < $do->count_rel('itcdisp');$i++){
					$do->set_rel('itcdisp','id'    ,''     ,$i);
					$do->set_rel('itcdisp','numero',$ncdisp,$i);
				}
			}
		}
		
		//print_r($do->get_all());
		
		if(empty($error)){
			$do->set('status','C');
			$do->save();
			logusu('cdisp',"Marco como terminado certificado nro $numero");
			if($redirect)
			redirect("presupuesto/cdisp/dataedit/show/$numero");
		}else{
			logusu('cdisp',"Marco como terminado certificado nro $numero con ERROR $error");
			$data['content'] = $error.anchor($this->url."dataedit/show/$numero",'Regresar');
			$data['title']   = " $this->tits";
			$data["head"]    = $this->rapyd->get_head();
			$this->load->view('view_ventanas', $data);
		}
	}
	
	
	
	function cd_reversar($numero,$anular=false,$redirect=true){
		$this->rapyd->load('dataobject');
		
		$error='';
	
		$do = new DataObject("cdisp");
		$do->rel_one_to_many('itcdisp', 'itcdisp', array('numero'=>'numero'));
		$do->load($numero);
		$status = $do->get('status');
		
		if($status=='C' ){
			$importes=array();
			for($i=0;$i < $do->count_rel('itcdisp');$i++){
				$codigoadm  = $do->get_rel('itcdisp','codigoadm' ,$i);
				$fondo      = $do->get_rel('itcdisp','fondo'     ,$i);
				$codigopres = $do->get_rel('itcdisp','codigopres',$i);
				$importe    = $do->get_rel('itcdisp','importe'   ,$i);
				$soli       = $do->get_rel('itcdisp','soli'      ,$i);
				
				$cadena = $codigoadm.'_._'.$fondo.'_._'.$codigopres;
				if(array_key_exists($cadena,$importes)){
					$importes[$cadena] +=$soli;
				}else{
					$importes[$cadena]  =$soli;
				}
			}
			
			if(empty($error) && $status=='C'){
				foreach($importes AS $cadena=>$monto){
					$temp  = explode('_._',$cadena);
					$error.= $this->chequeapresup($temp[0],$temp[1],$temp[2],'',$monto,0,'round($monto,2) > $disponible=round(($apartado),2)','El Monto ($monto) es mayor al apartado ($disponible) para la partida ('.$temp[0].') ('.$temp[1].') ('.$temp[2].') ') ;
				}
			}
			//print_r($importes);
			//exit('Hello World'.$error);
			if(empty($error)){
				foreach($importes AS $cadena=>$monto){
					$temp  = explode('_._',$cadena);
					$error.=$this->afectapresup($temp[0],$temp[1],$temp[2],'',$monto,0, -1 ,array("apartado"));
				}
			}
		}else{
			$error.= "<div class='alert'>No se puede realizar la operacion para el certificado</div>";
		}
		
		
		if(empty($error)){
			if($anular){
				$do->set('fanulado',date('Ymd'));
				$do->set('status','A');
				logusu('cdisp',"Marco como anulado certificado nro $numero");
			}else{
				$do->set('ffinal',date('Ymd'));
				if($status=='F')
				$do->set('status','C');
				else
				$do->set('status','F');
				
				logusu('cdisp',"Marco como finalizado certificado nro $numero");
				
			}
			$do->save();
			if($redirect)
			redirect($this->url."dataedit/show/$numero");
		}else{
			logusu('cdisp',"Marco como anulado certificado nro $numero con ERROR $error");
			$data['content'] = $error.anchor($this->url."dataedit/show/$numero",'Regresar');
			$data['title']   = " $this->tits";
			$data["head"]    = $this->rapyd->get_head();
			$this->load->view('view_ventanas', $data);
		}
	}
	
	function cd_finalizar($numero,$anular=false,$redirect=true){
		$this->cd_reversar($numero,$anular,$redirect);
	}
	
	function cd_anular($numero){
		$this->cd_reversar($numero,true,true);
	}
}
?>
