<?php
/*
$xls	=new XLSencabezadodetalle($mSQL);
$xls->setHeadValores('TITULO1');
$xls->setSubHeadValores('TITULO2','TITULO3');

$xls->setTitle1("Presupuesto");
$xls->setTitle2("Encabezado");
				
$xls->AddCol('codigo'   ,10 ,'Código'     ,'L',8);
$xls->tcols();
$xls->Header();

$selrow=array(
"Cliente"      =>"$cod_cli",
"RIF/CI"       =>"$rifci",
"Nombre"       =>"$nombre",
"Direccion"    =>"$direccion"
);
$xls->encabezado($selrow);
//$xls->encabezado($row);//todo

$xls->encabezado($selrow,2,3,7);

$xls->detalle();

$xls->Footer();

$xls->Output();
*/

require_once "writeexcel/class.writeexcel_workbookbig.inc.php";
require_once "writeexcel/class.writeexcel_worksheet.inc.php";

class XLSencabezadodetalle extends writeexcel_workbookbig  {
	
	var $ii              =1;
	var $centrar         =array();
  var $SobreTabla      =array();
  var $tituHeader      =array();
  var $tituSubHeader   =array();
  var $tituSubHeader2  =array();
  var $fname           ='';
  var $Title1          =array();
  var $cols            =array();
	var $ccol            =array();
  var $colum           =0;
  var $DBfieldsName;
	var $DBfieldsType;
	var $DBfieldsMax_lengt;
	var $workbook;
	var $align           =array();

	function XLSencabezadodetalle($mSQL=''){
		
		//if(!empty($datos)&&!empty($detalles)){
		
		$this->fname     = tempnam("/tmp", "reporte.xls");
		$this->workbook  = &new writeexcel_workbookbig($this->fname);
		$this->worksheet = $this->workbook->addworksheet();
			
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
		//}
		$this->h1  =& $this->workbook->addformat(array('bold'    => 1, 'border' => 1, 'border_color' => 'silver', 'color'   => 'black','size'    => 18,'fg_color' => 'white','merge'  => 1));
		$this->h2  =& $this->workbook->addformat(array('bold'    => 1, 'border' => 1, 'border_color' => 'silver', 'color'   => 'black','size'    => 16,'fg_color' => 'white','merge'  => 1));
		$this->h3  =& $this->workbook->addformat(array('bold'    => 1, 'border' => 1, 'border_color' => 'silver', 'color'   => 'black','size'    => 12,'fg_color' => 'white','merge'  => 1));
		$this->h4  =& $this->workbook->addformat(array('bold'    => 1, 'border' => 1, 'border_color' => 'silver', 'color'   => 'black','size'    => 8, 'fg_color' => 'white','merge'  => 0));
		$this->h5  =& $this->workbook->addformat(array('bold'    => 1, 'border' => 1, 'border_color' => 'silver', 'color'   => 'black','size'    => 6, 'fg_color' => 'white','merge'  => 0));
		$this->t1  =& $this->workbook->addformat(array("bold" 	 => 1, 'border' => 1, 'border_color' => 'silver',  "size"   => 9,      "merge"   => 0, "fg_color" =>  0x37 ));
		$this->t2  =& $this->workbook->addformat(array("bold" 	 => 1, 'border' => 1, 'border_color' => 'silver',  "size"   => 8,      "merge"   => 0, "fg_color" =>  0x2f ));
		
		$this->enca1 =& $this->workbook->addformat();
		$this->enca1->set_bold('1');
		$this->enca1->set_color('black');
		$this->enca1->set_align('left');
		$this->enca1->set_align('vcenter');
		$this->enca1->set_pattern();
		$this->enca1->set_fg_color(0x2f);
		$this->enca1->set_border(1);
		$this->enca1->set_border_color('silver');
		
	  $this->datal =& $this->workbook->addformat();
		$this->datal->set_bold('0');		
		$this->datal->set_color('black');
		$this->datal->set_align('vcenter');
		$this->datal->set_pattern();
		$this->datal->set_fg_color('white');
		$this->datal->set_border(1);
		$this->datal->set_border_color('silver');
		$this->datal->set_align('left');
		
		$this->datac =& $this->workbook->addformat();
		$this->datac->set_bold('0');
		$this->datac->set_color('black');
		$this->datac->set_align('center');
		$this->datac->set_align('vcenter');
		$this->datac->set_pattern();
		$this->datac->set_fg_color('white');
		$this->datac->set_border(1);
		$this->datac->set_border_color('silver');
		
		$this->datar =& $this->workbook->addformat();
		$this->datar->set_bold('0');
		$this->datar->set_color('black');
		$this->datar->set_align('rigth');
		$this->datar->set_align('vcenter');
		$this->datar->set_pattern();
		$this->datar->set_fg_color('white');
		$this->datar->set_border(1);
		$this->datar->set_border_color('silver');
		
		$this->enca2 =& $this->workbook->addformat();
		$this->enca2->set_bold('0');
		$this->enca2->set_color('black');
		$this->enca2->set_align('left');
		$this->enca2->set_align('vcenter');
		$this->enca2->set_pattern();
		$this->enca2->set_fg_color('white');
		$this->enca2->set_border(1);
		$this->enca2->set_border_color('silver');
	}
	
	function encabezado($data,$etiqueta=0,$valor=1,$linea=0){
		
		if($linea==0){
				$linea=$this->ii;
		}
		foreach($data as $key => $val){			
			$this->worksheet->write($linea, $etiqueta,$key,$this->enca1);//$this->h4
			$this->worksheet->write($linea, $valor,   $val,$this->enca2);//$this->h4			
			$linea++;
		}
		$this->ii=$linea;
		$this->ii+=1;
	}

	function detalle(){
		
		foreach($this->cols AS $h=>$cols){
			$this->worksheet->write($this->ii, $h,$cols['titulo'],$this->t1);
		}		
		$this->ii+=1;

		foreach( $this->DBquery->result_array() as $row ){
			foreach($this->cols AS $h=>$cols){				
				if($this->align[$h]=='C'){
					$this->worksheet->write($this->ii, $h,$row[$cols['campo']],$this->datac);
				}elseif($this->align[$h]=='R'){
						$this->worksheet->write($this->ii, $h,$row[$cols['campo']],$this->datar);
					}else{
						  $this->worksheet->write($this->ii, $h,$row[$cols['campo']],$this->datal);
					}
			}
			$this->ii+=1;
		}
		$this->ii+=1;
		$this->ii+=1;//linea en blanco
	}

	function tcols(){		
		foreach ($this->DBfieldsName as $row){
				$this->AddCol($row,20,$row);
			}
	}	
	
	function Header(){		
		
		
		$cfilas=$this->centrar;
		$ifilas=array('');		
    
		$this->centrar[0]=$this->Title1;
	
		$cfilas[0]=implode(' ',$this->tituHeader);
		$this->worksheet->write_row('A1', $cfilas , $this->h3);
		
		$cfilas[0] = $this->tituSubHeader[0];
		$this->worksheet->write_row('A2', $cfilas, $this->h4);
		
		$cfilas[0] = $this->tituSubHeader[1];
		$this->worksheet->write_row('A3', $cfilas, $this->h4);
		
		$cfilas[0] =' '; 
		$this->worksheet->write_row('A4', $cfilas, $this->h4);
		
		$cfilas[0] = implode(' ',$this->Title1);
		$this->worksheet->write_row('A5', $cfilas, $this->h1);
		
		$cfilas[0] =' '; 
		$this->worksheet->write_row('A6', $cfilas, $this->h4);
		
		$this->ii=$this->ii+5;
	} 
	
	function AddCol($DBnom,$width=-1,$TInom ,$align='L',$tipo=''){
		//Add a column to the table
		if (in_array($DBnom, $this->DBfieldsName)){
			if(is_array($TInom))$TInom=implode(' ',$TInom);
			$this->cols[]=array('titulo'=>$TInom,'campo'=>$DBnom,'tipo'=>$tipo);
			$this->centrar[]='';
			$this->ccols++;
			
			$a=$this->colum;
			$this->align[]=$align;
			$this->worksheet->set_column($a,$a, $width);
			$this->colum++;
		}
	}
	
	function setTitle1($tit=''){		
		$this->Title1[] =$tit;
		
	}
	
	function setTitle2($tit=''){
		$this->ii+=1;//linea en blanco
		$l='A'.($this->ii);
		$ifilas[0] = $tit;
		$this->worksheet->write_row($l, $ifilas, $this->h4);	
	}
	
	function setTitle3($tit=''){
		$this->ii+=1;//linea en blanco
		$l='A'.($this->ii);
		
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
	
	function Footer(){
		$this->ii++;
		$this->centrar[0]=implode(' ',$this->Title1).' :: Sistema Tortuga';
		$filas = $this->centrar;
		$l='A'.($this->ii);
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
}
	
?>