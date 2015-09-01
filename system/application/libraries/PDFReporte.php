<?php
require_once(RAPYD_PATH."helpers/datehelper.php");

require_once('pdf.php');

class PDFReporte extends pdf{

	var $bits=0;
	var $ProcessingTable=false;
	var $TableWidth;
	var $aCols=array();
	var $fCols=array();
	var $fcount=0;
	var $TableX;
	var $HeaderColor;
	var $RowColors;
	var $ColorIndex;
	var $Titulo;
	var $Acumulador=array();
	var $SobreTabla;
	var $SubTitulo;
	var $Logo;
	var $rows=array();
	var $grupo='';
	var $grupoHead;
	var $grupoField;
	var $grupoLabel;
	var $cgrupo=False;
	var $DBquery;
	var $DBfieldsName;
	var $DBfieldsType;
	var $DBfieldsMax_lengt;
	var $DBieldsNum;
	var $totalizar=array();
	var $ctotalizar=false;
	var $propiedades  =array(
			"HeaderColor"=>array(174,174,174),
			"color1"     =>array(255,255,255),
			"color2"     =>array(239,239,239),
			"padding"    =>2,
      );//"width"      =>24

	var $Columna=array("color1"=>array(255,255,255),"color2"=>array(239,239,239),"padding"=>2);
	var $tituHeader='';
	var $tituSubHeader='';
	var $view=array(
		'TituSize'      =>14,
		'TituFont'      =>'Helvetica',
		'TituType'      =>'U',
		'SubTituSize'   =>10,
		'SubTituFont'   =>'Helvetica',
		'SubTituType'   =>'',
		'TableTituSize' =>8,
		'TableTituFont' =>'Helvetica',
		'TableRowSize'  =>4,
		'TableRowFont'  =>'Helvetica',
		'HeadSize'      =>10,
		'HeadFont'      =>'Helvetica',
		'SubHeadSize'   =>4,
		'SubHeadFont'   =>'Helvetica',
		'GroupHeadSize' =>8,
		'GroupHeadFont' =>'Helvetica',
		'GroupHeadType' =>'',
		'GroupHeadBorder' =>'B',
		'StablaTituFont'=>'Helvetica',
		'StablaTituSize'=>8
		);

  var $endpage;
  var $format;
  var $totalizarLabelAlign=array();
  var $pintareporte='';
  var $verline=true;
  var $showpage=true;
  var $showfooter=true;
  var $PageI=FALSE;
  var $showfemi=FALSE;
  var $Footer2=FALSE;
  var $Lmargin=20;
  var $HeadTmargin=0;
  var $background=array(
	'image'=>'',
	'x'=>'',
	'y'=>'',
	'w'=>'',
	'h'=>''
	);
	var $showpagef=false;

/*##########################################################################################################
# orientation: Orientaci?n de p?gina por defecto. Los posibles valores son (case insensitive)              #
#         * P o Portrait (DEFECTO)                                                                         #
#         * L o Landscape (apaisado)                                                                       #
# format" El formato usado por las p?ginas. Es puede ser uno de los siguientes valores (case insensitive)  #
#         A3,A4,A5,Letter,Legal                                                                            #
# unit: pt: punto ; mm: milimetro ; cm: centimetro ; in: pulgada                                           #
##########################################################################################################*/


	function PDFReporte($mSQL='',$orientation='P',$format='LETTER',$unit='mm'){
		if(!empty($mSQL)){
			$CI = & get_instance();
			$this->DBquery  = $CI->db->query($mSQL);
			$data=$this->DBquery->field_data();
			foreach ($data as $field){
				$this->DBfieldsName[]=$field->name;
				$this->DBfieldsType[$field->name]     =$field->type;
				$this->DBfieldsMax_lengt[$field->name]=$field->max_length;
			}
			$this->DBieldsNum=count($this->DBfieldsName);

      $this->__construct();

      $this->setPageFormat($format, $orientation);
      $this->pdfunit=$unit;

      $this->AliasNbPages();

	$s=$CI->datasis->traevalor('SISTEMA');
	$pintareporte=$CI->datasis->traevalor('PINTAREPORTE','S');
	$lineareporte=$CI->datasis->traevalor('LINEAREPORTE','S');
	if($pintareporte=='S'){
		$this->pintareporte=0;
	}else{
		$this->pintareporte='T';
		$this->propiedades["color2"] =array(255,255,255);
	}

	($this->pintareporte=='S'?'T':'0');
      $this->sistema=(empty($s)?$this->sistema:$s);
      $this->SetLeftMargin($this->Lmargin);
      //$this->lMargin=10;
      //$this->rMargin=10;
      //$this->cMargin=1;
      $this->format = $format;
      $this->setCellPadding( 0.5, '', 0.5, '');

      //$this->SetAutoPageBreak(TRUE, 10);

		}
	}


	function setType($campo,$tipo){
		$this->DBfieldsType[$campo]=$tipo;
	}

	function setTitulo($tit='Listado',$size='',$font=''){
		if(!empty($size)) $this->view['TituSize'] =$size;
		if(!empty($font)) $this->view['TituFont'] =$font;
		//$this->Titulo =utf8_decode($tit);
		$this->Titulo =$tit;
	}

	function setSubTitulo($tit='',$size='',$font=''){
		if(!empty($size)) $this->view['SubTituSize'] =$size;
		if(!empty($font)) $this->view['SubTituFont'] =$font;
		if(!empty($tit) ) $this->SubTitulo =$tit;
		//if(!empty($tit) ) $this->SubTitulo =utf8_decode($tit);
	}

	function setTableTitu($size='',$font=''){
		if(!empty($size)) $this->view['TableTituSize'] =$size;
		if(!empty($font)) $this->view['TableTituFont'] =$font;
	}

	function setRow($size='',$font=''){
		if(!empty($size)) $this->view['TableRowSize'] =$size;
		if(!empty($font)) $this->view['TableRowFont'] =$font;
	}

	function setHead($tituHeader='',$size='',$font=''){
		if(!empty($tituHeader)) $this->tituHeader=$tituHeader;
		if(!empty($size)) $this->view['HeadSize'] =$size;
		if(!empty($font)) $this->view['HeadFont'] =$font;
	}

	function setSubHead($tituSubHeader='',$size='',$font=''){
		if(!empty($tituSubHeader)) $this->tituSubHeader[]=$tituSubHeader;
		if(!empty($size)) $this->view['SubHeadSize'] =$size;
		if(!empty($font)) $this->view['SubHeadFont'] =$font;
	}

	function setHeadValores($param){
		$CI =& get_instance();
		$data= func_get_args();
		foreach($data as $sale){
			$valor=$CI->datasis->traevalor($sale);
			$this->tituHeader[]=(strlen($valor)>0 ?$valor:$sale);
		}
	}

	function setSubHeadValores($param){
		$CI =& get_instance();
		$data= func_get_args();
		foreach($data as $sale){
			$valor=$CI->datasis->traevalor($sale);
			$this->tituSubHeader[]=(strlen($valor)>0 ?$valor:$sale);
		}
	}

	function setTotalizar($param){
		$data= func_get_args();
		foreach($data as $sale){
			if (in_array($sale, $this->DBfieldsName) OR array_key_exists($sale,$this->fCols)){
				$this->totalizar[]=$sale;
				$this->ctotalizar=true;
			}
		}
	}

	function setAcumulador($param){
		$data= func_get_args();
		foreach($data as $sale){
			if (in_array($sale, $this->DBfieldsName) OR array_key_exists($sale,$this->fCols)){
				$this->Acumulador[]=$sale;
			}
			if (!in_array($sale, $this->totalizar)){
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

	function setSobreTabla($param,$size=8,$font='Helvetica'){
		$this->view['StablaTituFont']=$font;
		$this->view['StablaTituSize']=$size;
		$this->SobreTabla=$param;
	}

	function setHeadGrupo($label='',$campo='',$font='',$size='',$type=''){
		//$salecoment= substr($template,strpos($template,"<#")+2,strpos($template,"#>")-strpos($template,"<#")-2);
		$this->grupoHead=$label;
		if (in_array($campo, $this->DBfieldsName)){
			$this->grupoField=$campo;
		}
		if(!empty($size)) $this->GroupHeadSize=$size;
		if(!empty($font)) $this->GroupHeadFont=$font;
		if(!empty($type)) $this->GroupHeadType=$type;
	}

	function setGrupoLabel($label){
		if(is_array($label))
			$data=$label;
		else
			$data= func_get_args();

		foreach($data as $sale){
			$correcto=true;
			$sal=$this->_parsePattern($sale);
			//print_r($sal);
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

	function Header(){
		$left=$this->lMargin;
		if(strlen($this->background['image'])>0)
		$this->image(K_PATH_IMAGES.$this->background['image'],$this->background['x'],$this->background['y'],$this->background['w'],$this->background['h']);

		$this->ln($this->HeadTmargin);

		if (!empty($this->Logo)){
			$this->image(K_PATH_IMAGES.$this->Logo,$left+5,'',20);
			$left+=30;
		}

		$this->SetFont('Times','I',8);
		//$this->Cell(0,0,'gina '.$this->PageNo().'/{nb}',0,1,'R');

		if($this->showpage){
			if(is_numeric($this->PageI))
			$page=$this->PageI=$this->PageI+1;
			else
			$page=$this->PageNo().'/{nb}';

			$this->Cell(0,0,'Página '.$page,0,1,'R');
		}


		//Head
		$this->SetFont($this->view['HeadFont'],'',$this->view['HeadSize']);
		if( is_array($this->tituHeader)){
			foreach($this->tituHeader as $headtitu){
				$this->SetX($left);
				$this->Cell(0,$this->view['HeadSize']/2,$headtitu,0,1,'L');
			}
		}elseif (!empty($this->tituHeader)){
			$this->SetX($left);
			$this->Cell(0,$this->view['HeadSize']/2,$this->tituHeader,0,1,'L');
		}

		//SubHead
		$this->SetFont($this->view['SubHeadFont'],'',$this->view['SubHeadSize']);
		if( is_array($this->tituSubHeader) ){
			foreach($this->tituSubHeader as $headtitu){
				$this->SetX($left);
				$this->Cell(0,$this->view['SubHeadSize']/2,$headtitu,0,1,'L');
			}
		}elseif (!empty($this->tituSubHeader)){
			$this->SetX($left);
			$this->Cell(0,$this->view['SubHeadSize']/2,$this->tituSubHeader,0,1,'L');
		}

		//Titulo
		$this->SetFont($this->view['TituFont'],'',$this->view['TituSize']);
		$this->Cell(0,$this->view['TituSize']/2,$this->Titulo ,0,1,'C');

		//SubTitulo
		if(!empty($this->SubTitulo)){
			$this->SetFont($this->view['SubTituFont'],'',$this->view['SubTituSize']);
			//$this->Cell(0,$this->view['SubTituSize']/2,$this->SubTitulo ,0,1,'C');
			$this->MultiCell(0, $this->view['SubTituSize']/2, $this->SubTitulo, '', 'C', 0, 1, '' ,'', true, 0,true,false,0,'B');
		}
		$this->Ln(5);

		//Sobre Tabla
		if (!empty($this->SobreTabla)){
			$this->SetFont($this->view['StablaTituFont'],'',$this->view['StablaTituSize']);
			$this->Cell(0,$this->view['TableTituSize']/2,$this->SobreTabla,0,1,'L');
		}
		$this->tMargin=$this->GetY();

		//Ensure table header is output
		if($this->ProcessingTable)
			$this->TableHeader();
	else{
	  $mar = $this->getPageSizeFromFormat($this->format);
	  $p=round(($mar[1]/72)*2.54,2)*10;
	  $this->endpage=$p-$this->footer_margin;//$this->tMargin
	}
	}

	function Footer(){
		$this->Ln();
		$this->SetFont('Helvetica','B',8);

		if($this->showfooter){
			if($this->Footer2){
				$this->MultiCell(0, 1,$this->Footer2, 0, 'C', 0, 1, '' ,'', true, 0,true,0,0,'M');
			}
			$sal='';
			if($this->showfemi)
			$sal.='Fecha de Emision:'.date('d/m/Y').'               ';
			$sal.=$this->Titulo.'::'.$this->sistema;
			$this->Cell(0,6,$sal,'T',0,'C');
			
			if($this->showpagef){
				if(is_numeric($this->PageI))
				$page=$this->PageI=$this->PageI+1;
				else
				$page=$this->PageNo().'/{nb}';

				$this->Cell(0,0,'Página '.$page,0,1,'R');
			}
		}
		$this->Ln();
		//Ensure table header is output
		//parent::Footer();
	}

	function TableHeader() {
		$this->SetFont($this->view['TableTituFont'],'B',$this->view['TableTituSize']);
		$this->SetX($this->TableX);
		$fill=!empty($this->HeaderColor);
		$mAncho = $this->view['TableTituSize']/2;
		if($fill)
			$this->SetFillColor($this->HeaderColor[0],$this->HeaderColor[1],$this->HeaderColor[2]);
		foreach($this->aCols as $col)
			if (is_array($col['c'])) {
				$m = count($col['c']);
				$mX = $this->GetX();
				$mY = $this->GetY();
				foreach ( $col['c'] as $mPar )
				{
					if ( $m > 1 ){
						$this->SetXY( $mX, $mY+$mAncho*($m-1) );
					}
					//$this->Cell($col['w'],$mAncho, utf8_decode($mPar),'',0,'C',$fill);
					$this->Cell($col['w'],$mAncho, $mPar,'',0,'C',$fill);
					if ( $m == 1 ){
						$mX1 = $this->GetX();
						$mY1 = $this->GetY();
					}
					$m++;

				}
				$this->SetXY($mX+$col['w'], $mY);
				
			} else {
				if($col['th'])
				$this->MultiCell($col['w'],$col['th'], $col['c'], 'TB', 'C', $fill, 0, '' ,'', true, 0,true,false,0,'C');
				else
				$this->Cell($col['w']     , $mAncho, $col['c'],'TB',0,'C',$fill);

				//$this->Cell($col['w'], $mAncho, utf8_decode($col['c']),'TB',0,'C',$fill);
				$m = 2;
			}
			$this->ln(1);
		for( $i=1; $i<$m; $i++) $this->Ln();
		$this->tMargin=$this->GetY();
	}

	function TableWidth(){
		$TableWidth=0;
		foreach($this->aCols as $i=>$col){
			$w=$col['w'];
			if($w==-1)
				$w=$width/count($this->aCols);
			elseif(substr($w,-1)=='%')
				$w=$w/100*$width;
			$this->aCols[$i]['w']=$w;
			$TableWidth+=$w;
		}
		$this->TableWidth=$TableWidth;
		return $TableWidth;
	}

	function GroupTableHeader($row,$n=0){
		$this->SetFont($this->view['GroupHeadFont'],'',$this->view['GroupHeadSize']);
		if (empty($this->TableWidth)) $TableWidth = $this->TableWidth(); else $TableWidth = $this->TableWidth;
		for($i=$n-1;$i<count($this->grupo);$i++){
			if (!empty($this->grupoLabel[$i])){
				$sal=$this->_parsePattern($this->grupoLabel[$i]);
        //print_r($sal);
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

			$this->SetX($this->TableX);
			$this->SetFont($this->view['GroupHeadFont'],'',$this->view['GroupHeadSize']-$i);
			$this->MultiCell($TableWidth, 0, $label, $this->view['GroupHeadBorder'], '', 0, 1, '' ,'', true, 0,true,false,0,'B');
			//$this->Cell($TableWidth,$this->view['GroupHeadSize']/2,$label,'B',1,'',0);
			//$this->line();
		}
	}

	function Row($data,$linea=0,$pinta=1,$align=false) {
//	echo '</br>$this->Tablex'.$this->Tablex;
		$this->SetX($this->TableX);
		$ci=$this->ColorIndex;
		$fill=!empty($this->RowColors[$ci]);
		if($fill)
			$this->SetFillColor($this->RowColors[$ci][0],$this->RowColors[$ci][1],$this->RowColors[$ci][2]);
		if($pinta==0) $fill=0;
		$ancho = 1;


    $h=0;
    foreach($this->aCols as $col){
      $this->SetFont("Helvetica",'',$col['s']);
      $b=$this->getCellHeightRatio();
      $a=$this->getNumLines($data[$col['f']],$col['w'],true,false,'',$col['b']);
      $h=($a*($b+1.7)>$h?$a*($b+1.7):$h);
    }

    $page_start = $this->getPage();
    $y_start = $this->GetY();
    $ccols=count($this->aCols);
    $ccol=1;

		foreach($this->aCols as $col) {

      if ( $col['s'] > $ancho ) $ancho=$col['s'];
      $this->SetFont("Helvetica",'',$col['s']);
      $page_end_1 = $this->getPage();
      $y_end_1 = $this->GetY();
      $this->setPage($page_start);
      $this->MultiCell($col['w'],
		       ($h>$col['h']?$h:$col['h']),
		       $data[$col['f']],
		       $col['b'],
		       ($align?$align[$col['f']]:$col['a']),
		       $fill,
		       ($ccols==$ccol?1:2),
		       $this->GetX() ,
		       $y_start,
		       true,
		       0,
		       false,
		       0,
		       0,
		       'B');
      $page_end_2 = $this->getPage();
      $y_end_2 = $this->GetY();
      // set the new row position by case
      if (max($page_end_1,$page_end_2) == $page_start) {
        $ynew = max($y_end_1, $y_end_2);
      } elseif ($page_end_1 == $page_end_2) {
        $ynew = max($y_end_1, $y_end_2);
      } elseif ($page_end_1 > $page_end_2) {
        $ynew = $y_end_1;
      } else {
        $ynew = $y_end_2;
      }
      $this->setPage(max($page_end_1,$page_end_2));
      $this->SetXY($this->GetX(),$ynew);
      $ccol++;
    }
		$this->ColorIndex=1-$ci;
	}

	function line(){
		if($this->verline){
			$this->SetX($this->TableX);
			if (empty($this->TableWidth)) $TableWidth = $this->TableWidth(); else $TableWidth = $this->TableWidth;
			$this->Cell($TableWidth,0.35,'','T',1);
		}
	}

	function CalcWidths($width,$align){
		//Compute the widths of the columns
		if (empty($this->TableWidth)) $TableWidth = $this->TableWidth(); else $TableWidth = $this->TableWidth;
		//Compute the abscissa of the table
		if($align=='C')
			$this->TableX=max(($this->w-$TableWidth)/2,0);
		elseif($align=='R')
			$this->TableX=max($this->w-$this->rMargin-$TableWidth,0);
		else
			$this->TableX=$this->lMargin;
	}

	function AddCol($field=-1,$width=-1,$caption='',$align='L', $fontsize=11,$border='T',$height=4,$theight=FALSE,$tipo=''){
		//Add a column to the table
		if($this->pintareporte==0)

		if($field!=-1){
			if(in_array($field, $this->DBfieldsName)){
				$this->aCols[$field]=array('f'=>$field, 'c'=>$caption,'w'=>$width, 'a'=>$align,'s'=>$fontsize,'b'=>$border,'h'=>$height,'th'=>$theight );
				$this->rows[]=$field;
			}
		}

	}

	function AddCof($field=-1,$width=-1,$caption='',$align='L', $fontsize=11,$border='T'){
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
				$this->aCols[]=array( 'f'=>$nname, 'c'=>$caption,'w'=>$width, 'a'=>$align,'s'=>$fontsize,'b'=>$border );
				$this->rows[]=$nname;
				$this->fCols[$nname]=$field;
				$this->fcount++;
				$this->setType($nname,'real');
			}
		}
	}

	function grupoCambio($bache,$row){
		$i=0;
		foreach($this->grupo as $fila) {
			$i++;
			if ($bache[$fila]!=$row[$fila]) return $i;
		}
		return false;
	}

	function Table(){
		$prop = $this->propiedades;
		$res  = $this->DBquery;

		//Add all columns if none was specified
		if(count($this->aCols)==0){
			for( $i=0; $i<$this->DBieldsNum; $i++ )
				$this->AddCol();
		}
		//Retrieve column names when not specified
		foreach($this->aCols as $i=>$col){
			if($col['c']==''){
				if(is_string($col['f']))
					$this->aCols[$i]['c']=ucfirst($col['f']);
				else {
					$nombre = $this->DBfieldsName[$i];
					$this->aCols[$i]['c'] = ucfirst($nombre);
				}
			}
		}


//		echo '</br>$this->w      :'.$this->w      ;
//			echo '</br>$this->lMargin:'.$this->lMargin;
//				echo '</br>$this->rMargin:'.$this->rMargin;
//
//			echo '</br>$this->cMargin'.$this->cMargin;



		if(!isset($prop['width']))  $prop['width']=0;
		if($prop['width']==0)       $prop['width']=$this->w-$this->lMargin-$this->rMargin;
		if(!isset($prop['align']))  $prop['align']='C';
		if(!isset($prop['padding']))$prop['padding']=$this->cMargin;

		$cMargin=$this->cMargin;
		$this->cMargin=$prop['padding'];

		if(!isset($prop['HeaderColor'])) $prop['HeaderColor']=array();
		$this->HeaderColor=$prop['HeaderColor'];
		if(!isset($prop['color1'])) $prop['color1']=array();
		if(!isset($prop['color2'])) $prop['color2']=array();

		//if(!isset($prop['logo'])) $this->Logo =""; else $this->Logo = $prop['logo'];
		$this->RowColors=array($prop['color1'],$prop['color2']);

		//Compute column widths
		$this->CalcWidths($prop['width'],$prop['align']);

		//Print header
		$this->TableHeader();
		//Print rows
		$this->SetFont('Helvetica','',11);
		$this->ColorIndex=0;
		$this->ProcessingTable=true;

		if($this->ctotalizar){
			foreach( $this->aCols  as $i=>$fila ){
				$gtotal[$fila['f']]= 0 ;
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
		foreach( $res->result_array() as $row ){


			if($one){ $one=false;
				foreach($this->grupo as $fila) $bache[$fila]=$row[$fila];
				$this->GroupTableHeader($row,1);
			}
			if($this->cgrupo) $cambio=$this->grupoCambio($bache,$row);


			if($cambio){
				foreach($this->grupo as $fila) $bache[$fila]=$row[$fila];
				if ($this->ctotalizar){
					for($u=0;$u<count($this->grupo)-($cambio-1);$u++){
						if(isset($this->totalizarLabel)){
							//print_r($this->totalizarLabel[$this->grupo[$u]]);
							$labels=$this->totalizarLabel[$this->grupo[$u]];
							//print_r($labels);
							foreach($this->totalizarLabel[$this->grupo[$u]] AS $r=>$f){
									//$labels[$r]=$f['label'];
								if(count($sal=$this->_parsePattern($f['label']))>0)
								foreach($sal as $pasa)
									$labels[$r]['label']=str_replace('<#'.$pasa.'#>',$row[$pasa],$labels[$r]['label']);
			
								if(count($sal=$this->_parsePattern($f['label'],'~'))>0)
								foreach($sal as $pasa)
									$labels[$r]['label']=str_replace('<~'.$pasa.'~>',nformat($rstotal[$u][$pasa]),$labels[$r]['label']);
							}
							//print_r($labels);
							$this->Row2($labels,'T',1);
						}else{
						   $labels=$rstotal[$u];
						   
						    foreach( $this->aCols  as $i=>$fila ){
								$key=$fila['f'];
								if($this->DBfieldsType[$key]=='real')      $labels[$key]=(nformat($labels[$key])=='-0,00'?nformat(0):nformat($labels[$key]));
								elseif($this->DBfieldsType[$key]=='date')  $labels[$key]=dbdate_to_human($labels[$key]);
								elseif($this->DBfieldsType[$key]=='realb') $labels[$key]=(is_numeric($labels[$key])?(nformat($labels[$key])=='-0,00'?nformat(0):nformat($labels[$key])):'');
								elseif($this->DBfieldsType[$key]=='real*') $labels[$key]=(is_numeric($labels[$key])?(nformat($labels[$key])=='-0,00'?nformat(0):nformat($labels[$key])):'*');
								
							}
						   
						   $this->Row($labels,'T',0);
						}

						foreach( $this->aCols  as $i=>$fila ) $stotal[$u][$fila['f']] = 0;
					}
				}else{
					$this->line();
				}
				$this->ln(3);
				$this->GroupTableHeader($row,$cambio);
				$cambio=false;
			}
			$nf=$row;
			foreach( $this->aCols  as $i=>$fila ){
				$key=$fila['f'];
				if (preg_match("/^__cC[0-9]+$/", $key)>0){
					$sal=$this->_parsePattern($this->fCols[$key]);
					$val=$this->fCols[$key];
					if (count($sal)>0){
						foreach($sal as $pasa){
							if(!is_numeric($nf[$pasa])) $nf[$pasa]=0;
							$val=str_replace('<#'.$pasa.'#>',$nf[$pasa],$val);
						}

						$col='$val='.$val.';';
						//echo '--->'.$col;
						eval($col);
						$row[$key]=$val;

					}
				}

				if ($this->ctotalizar){
					if (in_array($key, $this->totalizar)){
						$gtotal[$key] +=$row[$key];
						if($this->cgrupo){
							$cangrup=count($this->grupo);
							for($u=0;$u<$cangrup;$u++){
								$stotal[$u][$key]+=$row[$key];
								$rstotal[$u][$key] =$stotal[$u][$key];
							}
						}



						$rgtotal[$key] =$gtotal[$key];

						if (in_array($key, $this->Acumulador)){
							if($this->cgrupo){
								$row[$key]=$stotal[0][$key];
							}else{
								if(empty($gtotal[$key]))
								$row[$key]=0;
								else
								$row[$key]=$gtotal[$key];

							}

						}
					}else{
						$total[$key]=$gtotal[$key]=$rtotal[$key]=$rgtotal[$key]=' ';
						$cangrup=count($this->grupo);
						for($u=0;$u<$cangrup;$u++){
							$stotal[$u][$key]=$rstotal[$u][$key]=' ';
						}
					}

				}

				if($this->DBfieldsType[$key]=='real')      $row[$key]=(nformat($row[$key])=='-0,00'?nformat(0):nformat($row[$key]));
				elseif($this->DBfieldsType[$key]=='date')  $row[$key]=dbdate_to_human($row[$key]);
				elseif($this->DBfieldsType[$key]=='dateb')  $row[$key]=(empty($row[$key])?'':dbdate_to_human($row[$key]));
				elseif($this->DBfieldsType[$key]=='realb') $row[$key]=(is_numeric($row[$key])?(nformat($row[$key])=='-0,00'?nformat(0):nformat($row[$key])):'');
				elseif($this->DBfieldsType[$key]=='real*') $row[$key]=(is_numeric($row[$key])?(nformat($row[$key])=='-0,00'?nformat(0):nformat($row[$key])):'*');
			}
			$this->Row($row,$this->pintareporte);
		}

		if ($this->ctotalizar){
				if ($this->cgrupo){
					for($u=0;$u<count($this->grupo);$u++){
						//$rstotal[];
						//print_r($rstotal[$u]);
						//print_r($this->totalizarLabel[$u]);
						//$this->Row2($this->totalizarLabel[$this->grupo[$u]],'T',0);
						//foreach( $this->aCols  as $i=>$fila ) $stotal[$u][$fila['f']] = 0;
						if(isset($this->totalizarLabel)){
							//print_r($this->totalizarLabel[$this->grupo[$u]]);
							$labels=$this->totalizarLabel[$this->grupo[$u]];
							//print_r($labels);
							foreach($this->totalizarLabel[$this->grupo[$u]] AS $r=>$f){
								//$labels[$r]=$f['label'];

								if(count($sal=$this->_parsePattern($f['label']))>0)
								foreach($sal as $pasa)
								$labels[$r]['label']=str_replace('<#'.$pasa.'#>',$row[$pasa],$labels[$r]['label']);

								if(count($sal=$this->_parsePattern($f['label'],'~'))>0)
								foreach($sal as $pasa)
								$labels[$r]['label']=str_replace('<~'.$pasa.'~>',nformat($rstotal[$u][$pasa]),$labels[$r]['label']);
							}
							//print_r($labels);
							$this->Row2($labels,0,0);
						}else{
							
							
							$labels=$rstotal[$u];
							
							foreach( $this->aCols  as $i=>$fila ){
								$key=$fila['f'];
								if($this->DBfieldsType[$key]=='real')      $labels[$key]=(nformat($labels[$key])=='-0,00'?nformat(0):nformat($labels[$key]));
								elseif($this->DBfieldsType[$key]=='date')  $labels[$key]=dbdate_to_human($labels[$key]);
								elseif($this->DBfieldsType[$key]=='realb') $labels[$key]=(is_numeric($labels[$key])?(nformat($labels[$key])=='-0,00'?nformat(0):nformat($labels[$key])):'');
								elseif($this->DBfieldsType[$key]=='real*') $labels[$key]=(is_numeric($labels[$key])?(nformat($labels[$key])=='-0,00'?nformat(0):nformat($labels[$key])):'*');
							}

							$this->Row($labels,0,0);
						}
					}
				}
			$this->ln();

			foreach($this->totalizar as $key)
			if(array_key_exists($key,$this->aCols)){
					if($this->DBfieldsType[$key]=='real')      $rgtotal[$key]=(nformat($rgtotal[$key])=='-0,00'?nformat(0):nformat($rgtotal[$key]));
					elseif($this->DBfieldsType[$key]=='realb') $rgtotal[$key]=(is_numeric($rgtotal[$key])?(nformat($rgtotal[$key])=='-0,00'?nformat(0):nformat($rgtotal[$key])):'');
					elseif($this->DBfieldsType[$key]=='real*') $rgtotal[$key]=(is_numeric($rgtotal[$key])?(nformat($rgtotal[$key])=='-0,00'?nformat(0):nformat($rgtotal[$key])):'*');
			}
			

			$this->Row($rgtotal,0,0);
		}else{
			$this->ln(5);
		}

		$this->ProcessingTable=false;
		$this->cMargin=$cMargin;
		//$this->aCols=array();
	}

	function _parsePattern($pattern,$marca='#'){

		$template = $pattern;
		$parsedcount = 0;
		$salida=array();
		while (strpos($template,"$marca>")>0) {
			$parsedcount++;
			$parsedfield = substr($template,strpos($template,"<$marca")+2,strpos($template,"$marca>")-strpos($template,"<$marca")-2);
			$salida[]=$parsedfield;
			$template = str_replace("<$marca".$parsedfield ."$marca>","",$template);
		}
		return $salida;
	}

	function add_fila($param){
		$data= func_get_args();
		$fila= array();
		foreach( $this->rows  as $i=>$key ){
			if(array_key_exists($i, $data  ))
				$fila[$key]=$data[$i];
			else
				$fila[$key]=' ';
		}
		$this->Row($fila,'T',0);
	}

	function extra($x=100,$text='',$ln=1,$size=12,$bold='',$align='L',$font="Helvetica",$border='',$w=100,$h=4,$valign='C',$y=null){
		$this->SetX($x);
		$this->SetFont($font,$bold,$size);
		$this->Cell($w,$h,$text,$border,$ln,$align,false,'',0,false,'T',$valign);
	}

	function mextra($x=100,$text='',$ln=1,$size=12,$bold='',$align='L',$font="Helvetica",$border='',$w=100,$h=4,$valign='C',$y=null){
		$this->SetX($x);
		$this->SetFont($font,$bold,$size);
		$this->MultiCell($w,$h,$text,$border);
	}

  function Lne($t=0){
    $this->ln($t);
  }

  function setTotalizarLabel($grup,$col,$label,$align='L',$size='',$font='Helvetica',$widht='',$line=0){
    $size=(empty($size)?$this->view['GroupHeadSize']:$size);
    $font=(empty($size)?$this->view['GroupHeadFont']:$font);

    $this->totalizarLabel[$grup][$col]['label'] =$label;
    $this->totalizarLabel[$grup][$col]['align'] =$align;
    $this->totalizarLabel[$grup][$col]['size' ] =$size;
    $this->totalizarLabel[$grup][$col]['font' ] =$font;
    $this->totalizarLabel[$grup][$col]['widht'] =$widht;
    $this->totalizarLabel[$grup][$col]['line' ] =$line;
    //print_r($this->totalizarLabel);
  }

  function Row2($data){

    $this->SetX($this->TableX);
    $ancho = 1;

    $h=0;
    foreach($this->aCols as $col){

      $colum =$col['f'];
      if(array_key_exists($colum,$data)){
        $b=$this->getCellHeightRatio();
        $a=$this->getNumLines($data[$colum]['label'],(empty($data[$colum]['widht'])?$col['w']:$data[$colum]['widht']),true,false,'',$data[$colum]['line']);
        $h=($a*($b+1.7)>$h?$a*($b+1.7):$h);
      }
    }

    $page_start = $this->getPage();
    $y_start    = $this->GetY();
    $ccols      = count($this->aCols);
    $ccol       = 1;

    foreach($this->aCols as $col) {
      $colum =$col['f'];
      if(array_key_exists($colum,$data)){

        $page_end_1 = $this->getPage();
        $y_end_1    = $this->GetY();
        $this->setPage($page_start);
        //echo "</br>".$data[$colum]['label'];

        $this->MultiCell((empty($data[$colum]['widht'])?$col['w']:$data[$colum]['widht']), $h, $data[$colum]['label'], 0, $data[$colum]['align'], 0, ($ccols==$ccol?1:2), $this->GetX() ,$y_start, true, 0,false,0,0,'M');

        $page_end_2 = $this->getPage();
        $y_end_2    = $this->GetY();
        // set the new row position by case
        if (max($page_end_1,$page_end_2) == $page_start) {
          $ynew = max($y_end_1, $y_end_2);
        } elseif ($page_end_1 == $page_end_2) {
          $ynew = max($y_end_1, $y_end_2);
        } elseif ($page_end_1 > $page_end_2) {
          $ynew = $y_end_1;
        } else {
          $ynew = $y_end_2;
        }
        $this->setPage(max($page_end_1,$page_end_2));
        $this->SetXY($this->GetX(),$ynew);
      }
      $ccol++;
    }
    //$this->ColorIndex=1-$ci;
  }


	function SetFooterSize($break,$margin){
		$this->SetAutoPageBreak(true, $break);
		$this->SetFooterMargin($margin);
	}

	function setBackground($image='',$x='',$y='',$w='',$h=''){
		$this->background['image']=$image;
		$this->background['x'    ]=$x;
		$this->background['y'    ]=$y;
		$this->background['w'    ]=$w;
		$this->background['h'    ]=$h;
	}
}
?>
