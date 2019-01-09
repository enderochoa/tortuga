<?php
class Minfra extends Controller {
	
	
	function minfra(){
		parent::Controller();
		$this->load->library("rapyd");
		$this->load->helper('download');
		$this->load->helper('file');
		
	}
	function index(){
		redirect ('nomina/minfra/crear');	
	}
	function crear($fechad='',$fechah=''){
		$this->load->dbutil();		
		$mSQL="SELECT nombre,apellido,nacional,cedula,sexo,DATE_FORMAT(nacimi,'%d%m%Y') as nacimi,cargo,IF(status='A',DATE_FORMAT(ingreso,'%d%m%Y'),DATE_FORMAT(retiro,'%d%m%Y'))as ingreso,status,IF(tipo='B',sueldo*2,IF(tipo='Q',sueldo*2,IF(tipo='M',sueldo*1,IF(tipo='S',sueldo*4,sueldo*0))))AS sueldo,contrato FROM pers WHERE retiro>='$fechad' AND retiro<='$fechah' OR status='A'";
		$query=$this->db->query($mSQL);
		$line="1ER_NOMBRE;2DO_NOMBRE;1ER_APELLIDO;2DO_APELLIDO;NACIONALIDAD;CEDULA;SEXO;FECHA_NACIMIENTO;CARGO;TIPO_TRABAJADOR;FECHA_INGRESO;ESTADO_EMPLEADO;SALARIO";
		$line.="\r\n";
		foreach($query->result_array() as $row){
								
			//$temp=eregi_replace('[:blank:]]{2,}',' ',trim($row["nombre"]));
			$temp=preg_replace('/\s\s+/', ' ', trim($row["nombre"]));
			$temp=str_replace('¥','&ntilde;',$temp);
			$temp=explode(" ",$temp);
			$ban=true;
			$nombre1=$nombre2='';
			foreach($temp AS $token){
				if($ban){
					//if (eregi("^(l[aeo]s|D[aieo]|D?el|la)$",$token)){
					if (preg_match("/^([lL][aeoAEO][sS]|[Dd][aieoAIEO]|[dD]?[eE][lL]|[lL][aA])$/",$token)>0){
						$nombre1.=$token.' ';
					}else{
						$nombre1.=$token;
						$ban=false;
					}
				}else{
					$nombre2.=$token.' ';
				}
			}
			$line.=rtrim($nombre1).";".rtrim($nombre2).";";
      
			//$temp=eregi_replace('[:blank:]]{2,}',' ',trim($row["apellido"]));
			$temp=preg_replace('/\s\s+/', ' ', trim($row["apellido"]));
			$temp=str_replace('¥','&ntilde;',$temp);
			$temp=explode(" ",$temp);
			$ban=true;
			$nombre1=$nombre2='';
			foreach($temp AS $token){
				if($ban){
					//if (eregi("^(l[aeo]s|D[aieo]|D?el|la)$",$token)){
					if (preg_match("/^([lL][aeoAEO][sS]|[Dd][aieoAIEO]|[dD]?[eE][lL]|[lL][aA])$/",$token)>0){
						$nombre1.=$token.' ';
					}else{
						$nombre1.=$token;
						$ban=false;
					}
				}else{
					$nombre2.=$token.' ';
				}
			}
			$line.=rtrim($nombre1).";".rtrim($nombre2).";";
			
			//$temp=split(' ',trim($row["nombre"]));
			//if(count($temp)==1)$line.=$temp[0].";;";
			//if(count($temp)==2)$line.=$temp[0].";".$temp[1].";";
      //
			//$temp=split(' ',trim($row["apellido"]));
			//if(count($temp)==1)$line.=$temp[0].";;";
			//if(count($temp)==2)$line.=$temp[0].";".$temp[1].";";

			if(trim($row["nacional"])=='V')$line.="1;";
			if(trim($row["nacional"])=='E')$line.="2;";
			
			$line.=trim($row["cedula"]).";";
			
			if(trim($row["sexo"])=='M')$line.="1;";
			if(trim($row["sexo"])=='F')$line.="2;";			
			
			$line.=$row["nacimi"].";";
			
			$temp=trim($row["cargo"]);
			$cargo=$this->datasis->dameval("SELECT descrip FROM carg WHERE cargo='$temp'");
			$carg=str_replace(' ','',$cargo);
			$carg1=str_replace('¥','&ntilde;',$carg);
		  $line.=$carg1.";";
			
			$temp=$row["contrato"];
			$temp=$this->datasis->dameval("SELECT tipo FROM noco WHERE codigo='$temp'");
			if((trim($temp)=='Q')||(trim($temp)=='M'))$line.="1;";
			if((trim($temp)=='S')||(trim($temp)=='B'))$line.="2;";
			
			$line.=$row["ingreso"].";";
			
			if(trim($row["status"])=='R')$line.="2;";
				else
					$line.="1;";
			
			$line.=number_format($row["sueldo"],2,'','');
			
			$line.="\r\n";
		}	
		//echo $mSQL;
		$name = 'Archivo.txt';		
		force_download($name,$line);		
	}

	function faovtxt($fechad='',$fechah=''){
		$this->load->dbutil();
		
		$mSQL="SELECT a.codigo, a.concepto, a.monto, SUM(a.valor*(a.tipo='A' AND MID(a.concepto,1,1)<>'9' )) asignacion,
		 SUM(a.valor*(a.concepto IN ('620', '621' ))) retencion,
		 SUM(a.valor*(a.concepto IN ('920', '921' ))) aporte, 
		 SUM(a.valor*(a.concepto IN ('620', '621' ))) +SUM(a.valor*(a.concepto IN ('920', '921' ))) as total, 
		 CONCAT(RTRIM(b.nombre), ' ',RTRIM(b.apellido)) nombre, c.descrip, 
		 a.fecha, a.contrato, d.nombre contnom, b.sexo,
     b.nacional,b.cedula,b.nombre,b.apellido,b.sueldo,DATE_FORMAT(b.ingreso,'%w%m%Y')AS ingreso,DATE_FORMAT(b.retiro,'%w%m%Y')AS retiro 
		 FROM (nomina a) JOIN pers as b ON a.codigo=b.codigo 
		 JOIN conc as c ON a.concepto=c.concepto 
		 LEFT JOIN noco d ON a.contrato=d.codigo 
		 WHERE a.valor<>0 AND a.fecha >= '$fechad' AND a.fecha <= '$fechah' 
		 GROUP BY EXTRACT( YEAR_MONTH FROM a.fecha ), a.codigo 
		 HAVING retencion<>0";
		
                //echo $mSQL;
		$query=$this->db->query($mSQL);
		$line=$error='';
		$line="NACIONAL;CEDULA;1ER_NOMBRE;2DO_NOMBRE;1ER_APELLIDO;2DO_APELLIDO;SALARIO;INGRESO;RETIRO";
		$line.="\r\n";
		if ($query->num_rows() > 0){
			$rem=array('.','-');
			foreach($query->result_array() as $row){
		

			if(trim($row["nacional"])=='V')$line.="V;";
			if(trim($row["nacional"])=='E')$line.="E;";
			$line.=trim($row["cedula"]).";";
								
			//$temp=eregi_replace('[:blank:]]{2,}',' ',trim($row["nombre"]));
			$temp=preg_replace('/\s\s+/', ' ', trim($row["nombre"]));
			
			$temp=str_replace('¥','&ntilde;',$temp);
			$temp=explode(" ",$temp);
			$ban=true;
			$nombre1=$nombre2='';
			foreach($temp AS $token){
				if($ban){
					//if (eregi("^(l[aeo]s|D[aieo]|D?el|la)$",$token)){
					if (preg_match("/^([lL][aeoAEO][sS]|[Dd][aieoAIEO]|[dD]?[eE][lL]|[lL][aA])$/",$token)>0){
						$nombre1.=$token.' ';
					}else{
						$nombre1.=$token;
						$ban=false;
					}
				}else{
					$nombre2.=$token.' ';
				}
			}
			$line.=rtrim($nombre1).";".rtrim($nombre2).";";
      
			//$temp=eregi_replace('[:blank:]]{2,}',' ',trim($row["apellido"]));
			$temp=preg_replace('/\s\s+/', ' ', trim($row["apellido"]));
			$temp=str_replace('¥','&ntilde;',$temp);
			$temp=explode(" ",$temp);
			$ban=true;
			$nombre1=$nombre2='';
			foreach($temp AS $token){
				if($ban){
					//if (eregi("^(l[aeo]s|D[aieo]|D?el|la)$",$token)){
					if (preg_match("/^([lL][aeoAEO][sS]|[Dd][aieoAIEO]|[dD]?[eE][lL]|[lL][aA])$/",$token)>0){
						$nombre1.=$token.' ';
					}else{
						$nombre1.=$token;
						$ban=false;
					}
				}else{
					$nombre2.=$token.' ';
				}
			}
			$line.=rtrim($nombre1).";".rtrim($nombre2).";";
			$line.=number_format($row["asignacion"],2,'','').";";
			$line.=$row["ingreso"].";";		
			$line.=$row["retiro"].";";
			$line.="\r\n";
			}
		}
		$name = 'Archivo.txt';		
		force_download($name,$line);
	}
}
?>