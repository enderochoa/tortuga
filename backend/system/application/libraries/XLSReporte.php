<?php
//require_once(RAPYD_PATH."/helpers/datehelper.php");

require_once "writeexcel/class.writeexcel_workbookbig.inc.php";
require_once "writeexcel/class.writeexcel_worksheet.inc.php";

class XLSReporte extends writeexcel_workbookbig  {

	var $fcount=0;
	var $DBquery;
	var $DBfieldsName;
	var $DBfieldsType;
	var $DBfieldsMax_lengt;
	var $workbook;
	var $worksheet;
	var $fname;
	var $cols;
	var $ccols;
	var $crows;
	var $Titulo;
	var $Acumulador=array();
	var $SubTitulo;
	var $SobreTabla;
	var $tituHeader;
	var $tituSubHeader;
	var $centrar=array();
	var $wstring=array("string");
	var $wnumber=array("real","int");
	var $fc=5;
	var $cc=0;
	var $ii=0;
	var $fi=0;
	var $totalizar=array();
	var $ctotalizar;
	var $grupo=array();
	var $cgrupo;
	//var $cgrupos=array();
	var $dRep=TRUE;
	var $grupoLabel;
	var $colum=0;
	var $rows=array();
	var $fCols=array();
  var $sistema='Sistema Tortuga';

	function XLSReporte($mSQL=''){
		$this->ccols=0;
		if(!empty($mSQL)){
			$CI = & get_instance();
			$this->DBquery  = $CI->db->query($mSQL);
			$data=$this->DBquery->field_data();
			foreach ($data as $field){
				$this->DBfieldsName[]                 =$field->name;
				$this->DBfieldsType[$field->name]     =$field->type;
				$this->DBfieldsMax_lengt[$field->name]=$field->max_length;
			}
		}

    //$s=$CI->db->query('SELECT valor FROM valores WHERE nombre="SISTEMA"');
    //$this->sistema=(empty($s)?$this->sistema:$s);

		$this->fname     = tempnam("/tmp", "reporte.xls");
		$this->workbook  = new writeexcel_workbookbig($this->fname);
		$this->worksheet = $this->workbook->addworksheet();

		//estilos encabezados
		$this->h1  =& $this->workbook->addformat(array('bold'    => 1,'color'   => 'black','size'    => 18,'merge'  => 1));
		$this->h2  =& $this->workbook->addformat(array('bold'    => 1,'color'   => 'black','size'    => 16,'merge'  => 1));
		$this->h3  =& $this->workbook->addformat(array('bold'    => 1,'color'   => 'black','size'    => 12,'merge'  => 1,'align' => 'left'));
		$this->h4  =& $this->workbook->addformat(array('bold'    => 1,'color'   => 'black','size'    => 8, 'merge'  => 0,'align' => 'left'));
		$this->h5  =& $this->workbook->addformat(array('bold'    => 1,'color'   => 'black','size'    => 6, 'merge'  => 0));
		$this->t1  =& $this->workbook->addformat(array( "bold" 	 => 1, "size" => 9, "merge" => 0, "fg_color" => 0x37 ));
		$this->t2  =& $this->workbook->addformat(array( "bold" 	 => 1, "size" => 8, "merge" => 0, "fg_color" => 0x2f ));
	}
	function tcols(){
		$this->dRep=false;
		foreach ($this->DBfieldsName as $row){
				$this->AddCol($row,20,$row);
			}
		//$this->grupo=$this->grupos;
		//$this->cgrupo=TRUE;
	}

	function AddCol($DBnom,$width=-1,$TInom ,$align='L', $fontsize=11,$border='T',$height=4,$theight=FALSE,$tipo=''){
		//Add a column to the table
		if (in_array($DBnom, $this->DBfieldsName)){
			if(is_array($TInom))$TInom=implode(' ',$TInom);
			
			
			$this->cols[]=array('titulo'=>$TInom,'campo'=>$DBnom,'tipo'=>$tipo);
			$this->centrar[]='';
			$this->ccols++;

			$a=$this->colum;
			$this->worksheet->set_column($a,$a, $width);
			$this->colum++;
		}
	}

	function Header(){
		$this->ii = 6;

		$cfilas=$this->centrar;
		$ifilas=array('');

		$this->centrar[0]=$this->Titulo;

		$ifilas[0]=implode(' ',$this->tituHeader);
		$this->worksheet->write_row('A1', $ifilas , $this->h3);

		$ifilas[0] = implode(' ',$this->tituSubHeader);
		$this->worksheet->write_row('A2', $ifilas, $this->h4);

		$cfilas[0]=$this->Titulo;
		$this->worksheet->write_row('A4', $cfilas, $this->h1);

		$cfilas[0]=$this->SubTitulo;
		$this->worksheet->write_row('A5', $cfilas, $this->h2);

		$ifilas[0] = $this->SobreTabla;
		$this->worksheet->write_row('A6', $ifilas, $this->h3);
	}

	function Table() {

		if($this->dRep)
			$this->Header();//Encabezado
		//------------campos tabla-------------------------------
		//$this->worksheet->set_column(0,$this->ccols,18);
		foreach($this->cols AS $cl=>$cols){
			$titulo=$cols['titulo'];
			$this->worksheet->write($this->ii, $cl, $titulo,$this->t1);
		}
		$this->ii=$this->ii+2;
		//----------fin campos tabla-----------------------------
		//----------inicializa valores-----------------------------
		if($this->ctotalizar){
			foreach($this->cols  as $i=>$fila ){
				$gtotal[$fila['campo']]= 0;
			}
			$rgtotal=$gtotal;
		}
		$cambio=false;
		if($this->cgrupo){
			foreach($this->grupo as $fila){
				if($this->ctotalizar) $stotal[]=$rstotal[]=$gtotal;
				$bache[$fila] =NULL;
			}
		}
		$one=$this->cgrupo;
		//----------fin inicializa valores--------------------------
		//**--inicio data set, recorre fila a fila --------------------------
		foreach( $this->DBquery->result_array() as $row ){
			//----------Se escriben solo en primer instancia los grupos----------------------
			if($one){
				$one=false;
				foreach($this->grupo as $fila)$bache[$fila]=$row[$fila];
				$this->GroupTableHeader($row,1);
			};
			//----------------------------------------------------------------------
			if($this->cgrupo) $cambio=$this->grupoCambio($bache,$row);
			if($cambio){
				foreach($this->grupo as $fila)$bache[$fila]=$row[$fila];
				if ($this->ctotalizar){
					for($u=0;$u<count($this->grupo)-($cambio-1);$u++){//se recorre por grupos
						foreach($this->cols AS $h=>$cols){							//se recorre por columnas
							$campo=$cols['campo'];
							if(in_array($campo,$this->totalizar))					//se verifica si la columna fue mandada a totalizar
				//----se escribe los totales de grupos----------------------------
								$this->worksheet->write($this->ii-1, $h,$stotal[$u][$campo],$this->t2);
							else
								$this->worksheet->write($this->ii-1, $h,' ',$this->t2);
						}
						foreach($this->cols  as $fila)			//se inicializan totale
							$stotal[$u][$fila['campo']] = 0;
						$this->ii++;
					}
				}
				//------se escribe los titulos de grupos----------------------------
				$this->GroupTableHeader($row,$cambio);
				$cambio=false;
			}


			//------se recorre por columnas para calculo de totales y escritura de datos----------------------------
			foreach($this->cols AS $o=>$cols){
				$campo=$cols['campo'];
				$nf=$row;
				if (preg_match("/^__cC[0-9]+$/", $campo)>0){
					$sal=$this->_parsePattern($this->fCols[$campo]);
					$val=$this->fCols[$campo];
					if (count($sal)>0){
						foreach($sal as $pasa){
							if(!is_numeric($nf[$pasa])) $nf[$pasa]=0;
							$val=str_replace('<#'.$pasa.'#>',$nf[$pasa],$val);
						}
						$col='$val='.$val.';';
						eval($col);
						$row[$campo]=$val;
					}
				}

				if ($this->ctotalizar){
					if (in_array($campo,$this->totalizar)){
						$gtotal[$campo] +=$row[$campo];
						if($this->cgrupo){
							for($u=0;$u<count($this->grupo);$u++){
								$stotal[$u][$campo]+=$row[$campo];
								$rstotal[$u][$campo] =$stotal[$u][$campo];
							}
						}
						$rgtotal[$campo]=$gtotal[$campo];
						//if (in_array($campo, $this->Acumulador)) $row[$campo]=$stotal[$u-1][$campo];
						if (in_array($campo, $this->Acumulador)){
							if($this->cgrupo)
								$row[$campo]=$stotal[0][$campo];
							else
								$row[$campo]=$gtotal[$campo];
						}
					}else{
						$total[$campo]=$gtotal[$campo]=$rtotal[$campo]=$rgtotal[$campo]=' ';
						for($u=0;$u<count($this->grupo);$u++){
					 		$stotal[$u][$campo]=$rstotal[$u][$campo]=' ';
					 	}
					}
				}
				//------se escribe los datos----------------------------
				$tipo=$cols['tipo'];
				
				$l=$this->ii;
				$this->selectWrite($tipo,$l-1, $o,$row[$campo]);
				//------se escribe los datos----------------------------
			}
			$this->ii++;
		}
		//**--fin data set, recorre fila a fila --------------------------

		//--escritura totales finales --------------------------
		if ($this->ctotalizar){
				if ($this->cgrupo){
					for($u=0;$u<count($this->grupo);$u++){
						foreach($this->cols AS $h=>$cols){
							$campo=$cols['campo'];
							if(in_array($campo,$this->totalizar))
								//--------escritura totales finales--------------
								$this->worksheet->write($this->ii-1, $h,$rstotal[$u][$campo],$this->t2);
							else
								$this->worksheet->write($this->ii-1, $h,' ',$this->t2);
						}
						foreach($this->cols  as $i=>$fila ){
							$stotal[$u][$fila['campo']] = 0;
						}
						$this->ii++;
					}

				}
			//--------escritura TOTAL FINAL--------------
			foreach($this->cols AS $h=>$cols){
				$campo=$cols['campo'];
				if(in_array($campo,$this->totalizar))
					$this->worksheet->write($this->ii-1, $h,$rgtotal[$campo],$this->t1);
				else
					$this->worksheet->write($this->ii-1, $h,' ',$this->t1);
			}
		}
		//--fin escritura totales finales --------------------------
		if($this->dRep)
			$this->Footer();

	}
	function setType($campo,$tipo){//relleno
	}
	function setTitulo($tit='Listado',$size='',$font=''){
		$this->Titulo =$tit;
	}
	function setSubTitulo($tit='',$size='',$font=''){
		if(!empty($tit) ) $this->SubTitulo =$tit;
	}
	function setTableTitu($size='',$font=''){

	}
	function setRow($size='',$font=''){

	}
	function setHead($tituHeader='',$size='',$font=''){
	}
	function setSubHead($tituSubHeader='',$size='',$font=''){
	}
	function setHeadValores($param){
		$CI =& get_instance();
		$data= func_get_args();
		foreach($data as $sale)
			$this->tituHeader[]=$CI->datasis->traevalor($sale);
	}
	function setSubHeadValores($param){
		$CI =& get_instance();
		$data= func_get_args();
		foreach($data as $sale)
			$this->tituSubHeader[]=$CI->datasis->traevalor($sale);
	}

		function setAcumulador($param){
		$data= func_get_args();
		foreach($data as $sale){
			if (in_array($sale, $this->DBfieldsName) OR array_key_exists($sale,$this->fCols)){
				$this->Acumulador[]=$sale;
				if (!in_array($sale, $this->totalizar)){
					$this->totalizar[]=$sale;
					$this->ctotalizar=true;
				}
			}
		}
	}


	function setTotalizar($param){
		$data= func_get_args();
		$i=0;
		foreach($data as $sale){
			if (in_array($sale, $this->DBfieldsName) OR array_key_exists($sale,$this->fCols)){
				$this->totalizar[]=$sale;
				$this->ctotalizar=true;
			}
		}
	}
	function setGrupo($param){
		if(is_array($param))
			$data=$param;
		else
			$data= func_get_args();
		foreach($data as $sale){
			if (in_array($sale, $this->DBfieldsName)){
				$this->grupo[]=$sale;
				$this->cgrupo=True;
			}
		}
	}
	function setSobreTabla($SobreTabla,$size=8,$font='Arial'){
		$this->SobreTabla=$SobreTabla;
	}
	function setHeadGrupo($label='',$campo='',$font='',$size='',$type=''){
	}
	function setGrupoLabel($label){
		if(is_array($label))
			$data=$label;
		else
			$data= func_get_args();
		foreach($data as $sale){
			$correcto=true;
			$sal=$this->_parsePattern($sale);
			if (count($sal)>0){
				foreach($sal as $pasa){
					if (!in_array($pasa, $this->DBfieldsName)){
						$correcto=false;
					}
				}
			}else{
				if (!in_array($sale, $this->DBfieldsName)) $correcto=false;
			}
			if($correcto)
				$this->grupoLabel[]=$sale;
			else
				$this->grupoLabel[]=NULL;
		}
	}
	function GroupTableHeader($row,$n=0){
		for($i=$n-1;$i<count($this->grupo);$i++){

				if (!empty($this->grupoLabel[$i])){

				$sal=$this->_parsePattern($this->grupoLabel[$i]);
				if(count($sal)>0){

					$label=$this->grupoLabel[$i];
					foreach($sal as $pasa){

						if($this->DBfieldsType[$pasa]=='date') $row[$pasa]=dbdate_to_human($row[$pasa]);
						$label=str_replace('<#'.$pasa.'#>',$row[$pasa],$label);
					}
				}else
					$label=$this->grupoLabel[$i];
			}else{
				$label=$this->grupo[$i].' '.$row[$this->grupo[$i]];

			}
			$linea='A'.$this->ii;
			$arreglo[0]=$label;
			$this->worksheet->write_row($linea, $arreglo , $this->h4);
			$this->ii++;
		}
	}
	function Row($data,$linea=0,$pinta=1) {
	}
	function CalcWidths($width,$align) {
	}
	function add_fila($param){
	}
	function AddPage(){
	}
	function Footer(){
		$this->centrar[0]=$this->Titulo.' :: '.$this->sistema;
		$filas = $this->centrar;
		$l='A'.($this->ii+2);
		$this->worksheet->write_row($l, $filas, $this->h5);
	}
	function Output(){
		$this->workbook->close();
		header("Content-Type: application/x-msexcel; name=\"reporte.xls\"");
		header("Content-Disposition: inline; filename=\"reporte.xls\"");
		$fh=fopen($this->fname, "rb");
		fpassthru($fh);
		unlink($this->fname);
	}
	function _parsePattern($pattern){
		$template = $pattern;
		$parsedcount = 0;
		$salida=array();
		while (strpos($template,"#>")>0) {
			$parsedcount++;
			$parsedfield = substr($template,strpos($template,"<#")+2,strpos($template,"#>")-strpos($template,"<#")-2);
			$salida[]=$parsedfield;
			$template = str_replace("<#".$parsedfield ."#>","",$template);
		}
		return $salida;
	}
	function selectWrite($tipo,$f,$c,$campo){
		
		if(in_array($tipo,$this->wnumber,FALSE)){
			$this->worksheet->write_number($f, $c, $campo);
		}elseif(in_array($tipo,$this->wstring,FALSE))
			{
				$this->worksheet->write_string($f, $c, $campo);
			}else
			{
				$this->worksheet->write($f, $c, $campo);
			}
		//$this->worksheet->write_string($f, $c, $campo);
	}
	
	function grupoCambio($bache,$row){
		$i=0;
		foreach($this->grupo as $fila) {
			$i++;
			if ($bache[$fila]!=$row[$fila])
				return $i;
		}
		return false;
	}
	function AddCof($field=-1,$width=-1,$caption='',$align='L', $tipo=''){//$fontsize=11
		if(is_array($caption))
			$caption=implode(' ',$caption);
		//Add a column to the table
		if($field!=-1){
			$correcto=false;
			$sal=$this->_parsePattern($field);

			if (count($sal)>0){
				$correcto=true;
				foreach($sal as $pasa){
					if (!in_array($pasa, $this->DBfieldsName)){
						$correcto=false;
					}
				}
			}
			if ($correcto){
				$nname='__cC'.$this->fcount;
				$this->cols[]=array( 'campo'=>$nname, 'titulo'=>$caption,'tipo'=>$tipo);//,'w'=>$width, 'a'=>$align,'s'=>$fontsize
				$this->rows[]=$nname;
				$this->fCols[$nname]=$field;
				$this->fcount++;
				//$this->setType($nname,'real');
			}
		}
	}

  function extra($x=100,$text='',$ln=1,$size=12,$bold='',$align='L',$font="Arial",$border='',$w=100,$h=4){
    //$this->SetX($x);
    //$this->SetFont($font,$bold,$size);
    //$this->worksheet->write($this->ii, $h,$stotal[$u][$campo],$this->t2);
    //$this->Cell($w,$h,$text,$border,$ln,$align,false);
  }

	function mextra($x=100,$text='',$ln=1,$size=12,$bold='',$align='L',$font="Arial",$border='',$w=100,$h=4,$valign='C',$y=null){
	}

  function Lne($t=0){

  }
  
	function GetY(){
		return true;
	}
	function SetY(){
		return true;
	}
	function SetX(){
		return true;
	}
	function SetXY(){
		return true;
	}
  function SetFooterSize($a,$b){
	}

	function Ln($t=0){

	}
	
	function setBackground($image='',$x='',$y='',$w='',$h=''){
	$this->background['image']=$image;
	$this->background['x'    ]=$x;
	$this->background['y'    ]=$y;
	$this->background['w'    ]=$w;
	$this->background['h'    ]=$h;
	}
}



class PDFReporte extends XLSReporte{
	function PDFReporte($mSQL=''){
		$this->XLSReporte($mSQL);
	}
}
?>
