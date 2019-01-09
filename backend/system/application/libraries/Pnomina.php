<?php
class fnomina{

	var $ci;
	var $CODIGO;
	var $fcorte;
	var $contrato;
	var $modo;

	function fnomina(){
		$this->ci     =& get_instance();
		$a =$this->ci->datasis->damerow("SELECT fecha,contrato,modo FROM prenom LIMIT 1");

		$this->fcorte  = (isset($a['fecha'])?$a['fecha']:'');
		$this->contrato= (isset($a['contrato'])?$a['contrato']:'');
		$this->modo    = (isset($a['modo'])?$a['modo']:'');
	}

	function SUELDO_MES(){
		$CODIGO=$this->ci->db->escape($this->CODIGO);
		$SUELDOA = 0;
		$mFRECU  = $this->ci->datasis->dameval("SELECT b.tipo FROM pers a JOIN noco b ON a.contrato=b.codigo WHERE a.codigo=$CODIGO");
		
		if($this->modo==2)
			$query="SELECT b.sueldo FROM pers a JOIN carg b ON a.cargo=b.cargo WHERE codigo=$CODIGO";
		else
			$query="SELECT sueldo FROM pers WHERE codigo=$CODIGO";
		
		$mMONTO  = $this->ci->datasis->dameval($query);

		if($mFRECU == 'O') $mFRECU  = $this->ci->datasis->dameval("SELECT tipo FROM pers WHERE codigo=$CODIGO");
		if($mFRECU == 'S') $SUELDOA = $mMONTO * 52 / 12;
		if($mFRECU == 'B') $SUELDOA = $mMONTO * 26 / 12;
		if($mFRECU == 'Q') $SUELDOA = $mMONTO * 2;
		if($mFRECU == 'M') $SUELDOA = $mMONTO;
		return round($SUELDOA,2);
	}

	function SUELDO_QUI(){
		$CODIGO=$this->ci->db->escape($this->CODIGO);
		$SUELDOA = 0;
		$mFRECU  = $this->ci->datasis->dameval("SELECT b.tipo FROM pers a JOIN noco b ON a.contrato=b.codigo WHERE a.codigo=$CODIGO");
		
		if($this->modo==2)
			$query="SELECT b.sueldo FROM pers a JOIN carg b ON a.cargo=b.cargo WHERE codigo=$CODIGO";
		else
			$query="SELECT sueldo FROM pers WHERE codigo=$CODIGO";
		
		$mMONTO  = $this->ci->datasis->dameval($query);

		if($mFRECU == 'O') $mFRECU = $this->ci->datasis->dameval("SELECT tipo FROM pers WHERE codigo=$CODIGO");
		if($mFRECU == 'S') $SUELDOA = $mMONTO * 52 / 24;
		if($mFRECU == 'B') $SUELDOA = $mMONTO * 26 / 24;
		if($mFRECU == 'Q') $SUELDOA = $mMONTO;
		if($mFRECU == 'M') $SUELDOA = $mMONTO/2;
		return $SUELDOA;
	}

	function SUELDO_SEM(){
		$CODIGO=$this->ci->db->escape($this->CODIGO);
		$SUELDOA = 0;
		$mFRECU  = $this->ci->datasis->dameval("SELECT b.tipo FROM pers a JOIN noco b ON a.contrato=b.codigo WHERE a.codigo=$CODIGO");
	
		if($this->modo==2)
			$query="SELECT b.sueldo FROM pers a JOIN carg b ON a.cargo=b.cargo WHERE codigo=$CODIGO";
		else
			$query="SELECT sueldo FROM pers WHERE codigo=$CODIGO";
		
		$mMONTO  = $this->ci->datasis->dameval($query);

		if($mFRECU == 'O') $mFRECU  = $this->ci->datasis->dameval("SELECT tipo FROM pers WHERE codigo=$CODIGO");
		if($mFRECU == 'S') $SUELDOA = $mMONTO;
		if($mFRECU == 'B') $SUELDOA = $mMONTO/2;
		if($mFRECU == 'Q') $SUELDOA = $mMONTO*24/52;
		if($mFRECU == 'M') $SUELDOA = $mMONTO*12/52 ;
		return $SUELDOA;
	}

	function SUELDO_DIA(){
		$CODIGO=$this->ci->db->escape($this->CODIGO);
		$SUELDOA = 0;
		$mFRECU  = $this->ci->datasis->dameval("SELECT b.tipo FROM pers a JOIN noco b ON a.contrato=b.codigo WHERE a.codigo=$CODIGO");
		
		if($this->modo==2)
			$query="SELECT b.sueldo FROM pers a JOIN carg b ON a.cargo=b.cargo WHERE codigo=$CODIGO";
		else
			$query="SELECT sueldo FROM pers WHERE codigo=$CODIGO";
		
		$mMONTO  = $this->ci->datasis->dameval($query);

		if($mFRECU == 'O') $mFRECU  = $this->ci->datasis->dameval("SELECT tipo FROM pers WHERE codigo=$CODIGO");
		if($mFRECU == 'S') $SUELDOA = $mMONTO/7 ;
		if($mFRECU == 'B') $SUELDOA = $mMONTO/14;
		if($mFRECU == 'Q') $SUELDOA = $mMONTO/15;
		if($mFRECU == 'M') $SUELDOA = $mMONTO/30 ;
		return $SUELDOA;
	}

	function SUELDO_HOR(){
		$SUELDOA = $this->SUELDO_DIA()/8;
		return $SUELDOA;
	}

	function ANTIGUEDAD($mHASTA){
		$CODIGO=$this->ci->db->escape($this->CODIGO);
		$mDESDE  = $this->ci->datasis->dameval("SELECT inicio FROM pers WHERE codigo=$CODIGO");
		//if (empty($mHASTA)) $mHASTA = date();
		//return { mANOS, mMES, mDIAS };
	}

	function TRAESALDO($mmCONC){
		$CODIGO=$this->ci->db->escape($this->CODIGO);
		$mTCONC = $this->ci->datasis->dameval("SELECT COUNT(*) FROM prenom WHERE codigo=$CODIGO AND concepto='$mmCONC' ");
		if ($mTCONC == 1)
			$mTEMPO = $this->ci->datasis->dameval("SELECT valor FROM prenom WHERE codigo=$CODIGO AND concepto='$mmCONC' ");
		return $mTEMPO;
	}

	function TABUSCA($columna){
		$fecha=explode('-',$this->fcorte);
		$ano=$fecha[0];
		$mes=$fecha[1];
		$dia=$fecha[2];
		return $this->ci->db->query("CALL sp_nomi_sueldo_int($this->contrato,$ano,$mes,$dia,$columna)");
	}

	function SUELDO_INT($meses=0){
		return $this->ci->db->query("CALL sp_nomi_sueldo_int($this->CODIGO,$meses,$this->fcorte)");
	}

	function GRUPO($parr){
		return 1;
	}

	function ASIGNA(){
		$codigo = $this->ci->db->escape($this->CODIGO);
		$query="SELECT SUM(valor) FROM prenom WHERE tipo='A' AND formula NOT LIKE '%ASIGNA()%' AND codigo=$codigo";

		return $this->ci->datasis->dameval($query);
	}

	function MONTO_CONCEPTO($concepto){
		$conceptoe=$this->ci->db->escape($concepto);
		$codigo   =$this->ci->db->escape($this->CODIGO);
		$query    ="SELECT valor FROM prenom WHERE concepto=$conceptoe AND codigo=$codigo";
		$valor    =$this->ci->datasis->dameval($query);
		if($valor==null)
		$retorna=0;
		else
		$retorna=$valor;

		return $retorna;
	}
	
	function CANTIDAD_CONCEPTO($concepto){
		$conceptoe=$this->ci->db->escape($concepto);
		$codigo   =$this->ci->db->escape($this->CODIGO);
		
		$query    ="SELECT monto FROM prenom WHERE concepto=$conceptoe AND codigo=$codigo";
		$valor    =$this->ci->datasis->dameval($query);
		if($valor==null)
		$retorna=0;
		else
		$retorna=$valor;

		return $retorna;
	}
	
	function MONTO_CONCEPTO_HIS($concepto,$fdesde,$fhasta){
		$conceptoe=$this->ci->db->escape($concepto);
		$codigo   =$this->ci->db->escape($this->CODIGO);
		
		$fecha    =explode('/',$fdesde);
		$ano      =$fecha[2];
		$mes      =$fecha[1];
		$dia      =$fecha[0];
		$fdesde   =$ano.$mes.$dia;
		
		$fecha    =explode('/',$fhasta);
		$ano      =$fecha[2];
		$mes      =$fecha[1];
		$dia      =$fecha[0];
		$fhasta   =$ano.$mes.$dia;
		
		$query    ="SELECT SUM(valor) FROM nomina WHERE concepto=$conceptoe AND codigo=$codigo AND fecha>=$fdesde AND fecha<=$fhasta ";
		
		$valor    =$this->ci->datasis->dameval($query);
		if($valor==null)
		$retorna=0;
		else
		$retorna=$valor;

		return $retorna;
	}

	function SEMANAS($tipo=NULL){
		if(!$tipo){
			$tipo   = $this->ci->datasis->dameval("SELECT tipo FROM noco WHERE codigo=(SELECT trabaja FROM prenom LIMIT 1) LIMIT 1");

			if(!$tipo)
			$tipo   = $this->ci->datasis->dameval("SELECT tipo FROM noco WHERE codigo=(SELECT contrato FROM prenom LIMIT 1) LIMIT 1");
		}
		$fcorte = $this->fcorte;
		$f      = explode('-',$fcorte);


		$mFECHA=$f;
		//print_r($mFECHA);
		switch($tipo){
			case 'S':
				$perido[0]=date("Y-m-d",mktime(0, 0, 0, $mFECHA[1], $mFECHA[2]-6, $mFECHA[0]));
				break;
			case 'B':
				$perido[0]=date("Y-m-d",mktime(0, 0, 0, $mFECHA[1], $mFECHA[2]-13, $mFECHA[0]));
				break;
			case 'Q':
				if ($mFECHA[2]>15)
				$perido[0]=date("Y-m-d",mktime(0, 0, 0, $mFECHA[1], 16, $mFECHA[0]));
				else
				$perido[0]=date("Y-m-d",mktime(0, 0, 0, $mFECHA[1], 1, $mFECHA[0]));
				break;
			case 'M':
				$perido[0]=date("Y-m-d",mktime(0, 0, 0, $mFECHA[1], 1, $mFECHA[0]));
				break;
			default:
				$perido[0]=$perido[1];
		}
		
		$fechaInicio=$perido[0];

		$fechaInicio  =strtotime( $fechaInicio                    );
		$fcorte       =strtotime( date('d-m-Y',strtotime($fcorte)));

		$c=0;
		for($i=$fechaInicio; $i<=$fcorte; $i+=86400)
			if(date("l", 1*$i)=="Monday")$c++;

		return $c;
	}
}

class Pnomina extends fnomina{

	var $MONTO;
	var $DIA;

	function pnomina(){
		parent::fnomina();
		//$this->CODIGO = $codigo;
		$this->DIA = $this->ci->datasis->dameval("SELECT EXTRACT( DAY FROM fecha) FROM prenom LIMIT 1");
		$this->FCORTE = $this->ci->datasis->dameval("SELECT fecha FROM prenom LIMIT 1");
	}

	function evalform($formula){
		$MONTO  =$this->MONTO;
		$DIA    =$this->DIA;		
		$fformula=$this->_traduce($formula);
		$retorna='$rt='.$fformula.';';

		$a=@eval($retorna);

		$e=error_get_last();
		//$w=tidy_warning_count();
		$w=0;

		if(strpos($retorna,'$MONTO')>0){
				$b=$this->ci->db->escape($retorna);
		}

		if(isset($rt) )
			return $rt;
		 else
		 return false;

		//eval($retorna);
		//return $rt;
	}

	function _traduce($formula){
		$CODIGO=$this->ci->db->escape($this->CODIGO);

		//para los if
		$long=strlen($formula);
		$pos=$long+1;
		while(1){
			$desp=$pos-$long-1;
			if(abs($desp)>=$long-1) break;
			$pos=strrpos($formula,'IF(',$desp);
			if($pos===false) break;
			$ig=null;
			$remp='?';
			for($i=$pos+2; $i<$long;$i++){
				if(preg_match('/[\'"]/',$formula[$i])>0 and is_null($ig)){
					$ig=$formula[$i];
				}elseif($formula[$i]==$ig and is_null($ig)===false){
					$ig=null;
				}elseif(is_null($ig)){
					switch ($formula[$i]) {
						case ',':
							$formula[$i]=$remp;
							$remp=':';
							break;
						case '(':
							$pila[]=$formula[$i];
							break;
						case ')':
							array_pop($pila);
							break;
					}
				}
				if(count($pila)==0) break;
			}
		}
		$formula=str_replace('IF(','(',$formula);
		//fin de if

		$metodos=get_class_methods('fnomina');
		foreach($metodos AS $metodo){
			$formula=str_replace($metodo.'(','$this->'.$metodo.'(',$formula);
		}

		$query = $this->ci->db->query("SELECT pers.*,DATE_FORMAT(ingreso,'%Y%m%d') ingreso FROM pers WHERE codigo=$CODIGO");
		if($query->num_rows() > 0 ){
			$rows = $query->row_array();

			foreach($rows AS $ind=>$valor){
				if($ind!='fnomina'){
					$valor   = trim($valor);
					$ind     = 'X'.strtoupper($ind);
					$formula = str_replace($ind,$valor,$formula);
				}
			}
		}
		//exit($this->modo);
		if($this->modo==2){
				$XSUELDO = $this->ci->datasis->dameval("SELECT b.sueldo FROM pers a JOIN carg b ON a.cargo=b.cargo WHERE codigo=$CODIGO");
		}
		
		
		$FCORTE =$this->FCORTE;
		$FCORTE=str_replace('-' ,''  ,$FCORTE);
		
		$formula=str_replace('XFCORTE' ,"$FCORTE"       ,$formula);
		$formula=str_replace('XMONTO'  ,'$MONTO'        ,$formula);
		$formula=str_replace('XDIA'    ,'$DIA'          ,$formula);
		$formula=str_replace('.AND.'   ,'&&'            ,$formula);
		$formula=str_replace('.OR.'    ,'||'            ,$formula);
		$formula=str_replace('.NOT.'   ,'!'             ,$formula);

		return $formula;
	}
}
?>
