<?php
require('Fpdf.php');

class Mtable extends Fpdf {
	var $ProcessingTable=false;
	var $aCols=array();
	var $TableX;
	var $HeaderColor;
	var $RowColors;
	var $ColorIndex;
	var $Titulo;
	var $Logo;
	var $totalizar=array();
	var $propiedades=array("HeaderColor"=>array(174,174,174),
					 "HeaderSize"=>12,
			     "color1"=>array(255,255,255),
			     "color2"=>array(239,239,239),
			     "padding"=>2);
	var $Columna=array("color1"=>array(255,255,255),"color2"=>array(239,239,239),"padding"=>2);
	
	
	
	function mtable($tit='Listado',$size=''){
		if(!empty($size))
			$this->propiedades["HeaderSize"]=$size;
		$this->Titulo =$tit;
		$this->Fpdf();
		$this->Open();
		$this->AliasNbPages();
		//$this->Logo   =$logo;
	}
	
	function Header(){
		//Titulo
		if (!empty($this->Logo))
			$this->image($this->Logo,12,7,30);
		$this->SetFont('Arial','',18);
		$this->Cell(0,6,$this->Titulo ,0,1,'C');
		$this->SetFont('Times','I',4);
		$this->Cell(0,10,'Página '.$this->PageNo().'/{nb}',0,0,'R');
		$this->Ln(10);
		//Ensure table header is output
		if($this->ProcessingTable)
			$this->TableHeader();
	}
	
	function Footer(){
		//Pie de Pagina
		$this->Ln();
		$this->SetFont('Arial','B',6);
		$this->Cell(0,6,'Toruga','T',1,'C');
		$this->Ln();
		//Ensure table header is output
		//parent::Footer();
	}

	function TableHeader() {
		$this->SetFont('Arial','B',$this->propiedades["HeaderSize"]);
		$this->SetX($this->TableX);
		$fill=!empty($this->HeaderColor);
		if($fill)
			$this->SetFillColor($this->HeaderColor[0],$this->HeaderColor[1],$this->HeaderColor[2]);
		foreach($this->aCols as $col)
			$this->Cell($col['w'],6,$col['c'],'TB',0,'C',$fill);
		$this->Ln();
	}
	
	function Row($data,$linea=0) {
		$this->SetX($this->TableX);
		$ci=$this->ColorIndex;
		$fill=!empty($this->RowColors[$ci]);
		if($fill)
			$this->SetFillColor($this->RowColors[$ci][0],$this->RowColors[$ci][1],$this->RowColors[$ci][2]);
			$ancho = 1;
		foreach($this->aCols as $col) 
			if ( $col['s'] > $ancho ) $ancho=$col['s'];
	
		foreach($this->aCols as $col) {
			$this->SetFont("Arial",'',$col['s']);
			$this->Cell($col['w'],$ancho/2,$data[$col['f']],$linea,0,$col['a'],$fill);
		}
		$this->Ln();
		$this->ColorIndex=1-$ci;
	}
	
	function CalcWidths($width,$align) {
		//Compute the widths of the columns
		$TableWidth=0;
		foreach($this->aCols as $i=>$col)
		{
			$w=$col['w'];
			if($w==-1)
				$w=$width/count($this->aCols);
			elseif(substr($w,-1)=='%')
				$w=$w/100*$width;
			$this->aCols[$i]['w']=$w;
			$TableWidth+=$w;
		}
		//Compute the abscissa of the table
		if($align=='C')
			$this->TableX=max(($this->w-$TableWidth)/2,0);
		elseif($align=='R')
			$this->TableX=max($this->w-$this->rMargin-$TableWidth,0);
		else
			$this->TableX=$this->lMargin;
	}
	
	function AddCol($field=-1,$width=-1,$caption='',$align='L', $fontsize=11) {
		//Add a column to the table
		if($field==-1)
			$field=count($this->aCols);
		$this->aCols[]=array( 'f'=>$field, 'c'=>$caption, 'w'=>$width, 'a'=>$align, 's'=>$fontsize );
	}
	
	function Table($query) {
		$prop=$this->propiedades;
		$totaliza=$this->totalizar;
		
		$CI = & get_instance();
		//Issue query
		//$res=mysql_query($query) or die('Error: '.mysql_error()."<BR>Query: $query");
		$res = $CI->db->query($query);
		
		//Add all columns if none was specified
		if(count($this->aCols)==0){
			$nb = $res->num_fields();  //mysql_num_fields($res);
			for( $i=0; $i<$nb; $i++ )
				$this->AddCol();
		}
		//Retrieve column names when not specified
		foreach($this->aCols as $i=>$col){
			if($col['c']==''){
				if(is_string($col['f']))
					$this->aCols[$i]['c']=ucfirst($col['f']);
				else {
					$fname  = $res->list_fields($col['f']);
					$nombre = $fname[$col['f']];
					$this->aCols[$i]['c'] = ucfirst($nombre);
					//$this->aCols[$i]['c']=ucfirst(mysql_field_name($res,$col['f']));
				}
			}
		}
		//Handle properties
		if(!isset($prop['width']))
			$prop['width']=0;
		if($prop['width']==0)
			$prop['width']=$this->w-$this->lMargin-$this->rMargin;
		if(!isset($prop['align']))
			$prop['align']='C';
		if(!isset($prop['padding']))
			$prop['padding']=$this->cMargin;
		$cMargin=$this->cMargin;
		$this->cMargin=$prop['padding'];
		if(!isset($prop['HeaderColor']))
			$prop['HeaderColor']=array();
		$this->HeaderColor=$prop['HeaderColor'];
		if(!isset($prop['color1']))
			$prop['color1']=array();
		if(!isset($prop['color2']))
			$prop['color2']=array();
		    
		if(!isset($prop['logo']))
			$this->Logo ="";
		else
			$this->Logo = $prop['logo'];
		$this->RowColors=array($prop['color1'],$prop['color2']);
		//Compute column widths
		$this->CalcWidths($prop['width'],$prop['align']);
		//Print header
		$this->TableHeader();
		//Print rows
		$this->SetFont('Arial','',11);
		$this->ColorIndex=0;
		$this->ProcessingTable=true;
		//	while($row=mysql_fetch_array($res))
		$total=array();
		foreach( $totaliza  as $fila ){
			/*if(is_array ($fila)){
				$campo=array_keys($fila);
				$pre1=array_values($fila)
				
				$fila=$campo[0];
				$label=$label[0];
			}*/
			$total[$fila]=0;
		}
		$encabDB=$res->field_data();
		foreach( $res->result_array() as $row ){
			$key=array_keys($row);
			$i=0;
			foreach( $key  as $fila ){
				if (array_search($fila, $totaliza)>-1) 
					$total[$fila]+=$row[$fila];
				else 
					$total[$fila]=' ';
				if($encabDB[$i]->type=='real')
					$row[$fila]=number_format($row[$fila], 2, ',', '.');
				$i++;
			}
			$this->Row($row);
		}$i=0;
		foreach( $key  as $fila ){
			if($encabDB[$i]->type=='real') $total[$fila]=number_format($total[$fila], 2, ',', '.');
			$i++;
		}
		$this->ln();

		$this->Row($total,'T');
		$this->ProcessingTable=false;
		$this->cMargin=$cMargin;
		$this->aCols=array();
	}
}
?>
