<?php
class Forma11 extends Controller{

	var $_direccion;
	var $codigo    =array();
	var $parametros=array();
	var $lencabp;
	var $lencabm;
	var $lencabu;
	var $lpiep;
	var $lpiem;
	var $lpieu;
	var $columnas=array();
	var $tabla=array();
	var $arreglo=array();
	var $ctotales=array();
	var $fuenteTabla="helvetica";
	var $fuenteArreglo="helvetica";
	var $limite;
	var $cuadros=array();
	var $textoTotales=array();
	var $textoTotalesPag=array();
	var $textoTotalizar=array();
	var $textoViene=array();
	var $totales=array();
	var $llevaUltimo=array();
	var $totalesPag=array();
	var $totalizar=array();
	var $tamPapel="LETTER";
	var $orientacion="";
	var $viene=array();

	function Forma11(){
		parent::Controller();
		$this->load->library("rapyd");
		//$this->load->library("numletra");
	}

	function index(){
		$this->ver();
	}

	function ver(){
		$this->load->library("rapyd");
		$this->parametros= func_get_args();
		if (count($this->parametros)>0){
			$_arch_nombre=implode('-',$this->parametros);
			$_fnombre=array_shift($this->parametros);
			$repo=$this->datasis->dameval("SELECT forma1 FROM formatos WHERE nombre='$_fnombre'");

			if(!$repo){
				echo "Formato No Existe";
				return false;
			}

			$captura=false;
			$lineas=explode("\n",$repo);
			foreach($lineas AS $linea){
				if(preg_match('/\/\/@(?P<funcion>\w+)/', $linea, $match)){
					$func=$match['funcion'];
					$captura=true;
				}elseif(preg_match('/\/\/@@(?P<funcion>\w+)/', $linea, $match)){
					$captura=false;
				}

				if($captura){
					if(isset($this->codigo[$func]))
					$this->codigo[$func] .= str_replace('<?php','',$linea)."\n";
					else
					$this->codigo[$func] = str_replace('<?php','',$linea)."\n";
				}
			}
		}
			
		$this->load->library("pdf");

		$o = new pdf;
		$t = new pdf;
		$this->config($t);
		$this->cuerpo($t,$o);
	}

	function config($obj){
		eval($this->codigo['config']);
	}

	function consultas(){
		eval($this->codigo['consultas']);
	}

	function encab($pdf){
		eval($this->codigo['encab']);
	}

	function encab2($pdf){
		eval($this->codigo['encab2']);
	}

	function encab3($pdf){
		eval($this->codigo['encab3']);
	}

	function pie($pdf){
		eval($this->codigo['pie']);
	}

	function pie2($pdf){
		eval($this->codigo['pie2']);
	}

	function pie3($pdf){
		eval($this->codigo['pie3']);
	}

	function cuerpo($objt,$pdf){
		eval($this->codigo['cuerpo']);
	}

	function forma_header($pdf){
		eval($this->codigo['forma_header']);
	}

	function enc_der($tipo,$numero,$fecha,$re,$pdf,$img=""){
		//$inicio = $this->infor_pdf->getY();
		if($img!=""){
			$pdf->Image(K_PATH_IMAGES.$img, 100, 4, 23);
		}
		eval($this->codigo['enc_der']);
		$pdf->setY($this->lencabp);
		$pdf->SetFont('times', '', 8);
	}
	
	function set_Tabla($tab){
		$this->tabla=$tab;
	}

	function set_Arreglo($a){
		$this->arreglo=$a;
	}

	function agregaCuadro($cuadro){
		$this->cuadros[]=$cuadro;
	}

	/*
	 * Funcion para agregar los campos que se van mostrar en la tabla
	 * Los parametros a agregar en la construccion de la tabla:
	 * $camp=identificador del campo dentro del arrelgo
	 * $name=nombre o titulo a colocar en la cabezera de la columna
	 * $pos=posicion donde debe comenzar a escribir
	 * $tam=ancho de la multicelda
	 * $alto=alto de celda
	 * $alinea=alineacion de cada columna
	 * $tfuen=tamaÃ±o de la fuente a escribir
	 * $estilo=estilo de fuente negrita o normal
	 * $fillColor=lleva color de fondo o no
	 * $reseth = true
	 * $stretch = 0
	 * $autopadding = true,
	 * $valign = 'T',
	 * 
	 * */
	function agrega_col($camp,$name,$pos,$tam=0,$alinea="",$tfuen,$estilo="",$fillColor=0,$reseth=true,$stretch = 0,$autopadding = true,$valign = 'T'){
		
		$this->columnas[]=array('campo'=>$camp,'titulo'=>$name,'pos'=>$pos,
								"ancho"=>$tam,'alinea'=>$alinea,'tfuente'=>$tfuen,
								'estilo'=>$estilo,'borde'=>'T','relleno'=>$fillColor,
								'reseth'=>$reseth,'stretch'=>$stretch,
								'autopadding'=>$autopadding,'valign'=>$valign);
	}
	/*
	 * Funcion Para mostrar una Celda con formato de titulo
	 */
	function titulo($titulo,$pdf,$objt,$fondo="",$tamLetra,$aling="",$actfondo){
		$objt->SetFont($this->fuenteTabla, 'B', $tamLetra);
		if($fondo=="") $fondo=5;
		$objt->setFillColor(0,0,0,$fondo);

		$objt->Cell(0, 4, $titulo, 1, 0, $aling, $actfondo);

		$objt->ln();
		$pdf=clone $objt;
	}
	/*
	 * Funcion para agregar texto en las cadenas de totalizar al final de la tabla
	 */
	function texto_totalizar($cadena='',$pos=15,$tam=0,$alinea="L",$borde=0,$fillColor=0,$tfuente=8,$estilo=""){
		$this->textoTotalizar=array('pos'=>$pos,'ancho'=>$tam,'texto'=>$cadena,
								'borde'=>$borde,'alinea'=>$alinea,'relleno'=>$fillColor,
								'tfuente'=>$tfuente,'estilo'=>$estilo);
	}
	/*
	 * Funcion para agregar los campos a totalizar al final solamente
	 */
	function set_totalizar($camp,$pos,$tam=0,$alinea="",$borde=0,$fillColor=0,$tfuen,$estilo="",$valor=0){
		
		$this->totalizar[]=array('campo'=>$camp,'pos'=>$pos,
								"ancho"=>$tam,'alinea'=>$alinea,'tfuente'=>$tfuen,
								'estilo'=>$estilo,'borde'=>$borde,'relleno'=>$fillColor,
								'valor'=>$valor);
	}
	
	/*
	 * Funcion para agregar texto en las cadenas de subtotales()
	 */
	function texto_total($cadena='',$cadena2='',$pos=15,$tam=0,$alinea="L",$borde=0,$fillColor=0,$tfuente=8,$estilo=""){
		$this->textoTotales=array('pos'=>$pos,'ancho'=>$tam,'texto'=>$cadena,'texto2'=>$cadena2,
								'borde'=>$borde,'alinea'=>$alinea,'relleno'=>$fillColor,
								'tfuente'=>$tfuente,'estilo'=>$estilo);
	}
	/*
	 * Funcion para agregar los campos a totalizar por pagina
	 */
	function set_total($camp,$pos,$tam=0,$alinea="",$borde=0,$fillColor=0,$tfuen,$estilo="",$valor=0){
		
		$this->totales[]=array('campo'=>$camp,'pos'=>$pos,
								"ancho"=>$tam,'alinea'=>$alinea,'tfuente'=>$tfuen,
								'estilo'=>$estilo,'borde'=>$borde,'relleno'=>$fillColor,
								'valor'=>$valor);
	}
	/*
	 * Funcion para agregar texto en las cadenas de totales(por gagina)
	 */
	function texto_totalPag($cadena='',$pos=15,$tam=0,$alinea="L",$borde=0,$fillColor=0,$tfuente=8,$estilo=""){
		$this->textoTotalesPag=array('pos'=>$pos,'ancho'=>$tam,'texto'=>$cadena,
								'borde'=>$borde,'alinea'=>$alinea,'relleno'=>$fillColor,
								'tfuente'=>$tfuente,'estilo'=>$estilo);
	}
	/*
	 * Funcion para agregar los campos a totalizar por pagina
	 */
	function set_totalPag($camp,$pos,$tam=0,$alinea="",$borde=0,$fillColor=0,$tfuen,$estilo="",$valor=0){
		
		$this->totalesPag[]=array('campo'=>$camp,'pos'=>$pos,
								"ancho"=>$tam,'alinea'=>$alinea,'tfuente'=>$tfuen,
								'estilo'=>$estilo,'borde'=>$borde,'relleno'=>$fillColor,
								'valor'=>$valor);
	}
	
	
	/*
	 * Funcion para agregar texto en las cadenas de viene inicial
	 */
	function texto_viene($cadena='',$pos=15,$tam=0,$alinea="L",$borde=0,$fillColor=0,$tfuente=8,$estilo=""){
		$this->textoViene=array('pos'=>$pos,'ancho'=>$tam,'texto'=>$cadena,
								'borde'=>$borde,'alinea'=>$alinea,'relleno'=>$fillColor,
								'tfuente'=>$tfuente,'estilo'=>$estilo);
	}
	
	/*
	 * Funcion para agregar los campos de viene al principio de la tabla
	 */
	function set_viene($pos,$tam=0,$alinea="",$borde=0,$fillColor=0,$tfuen,$estilo="",$valor=0){
		
		$this->viene[]=array('pos'=>$pos,
								"ancho"=>$tam,'alinea'=>$alinea,'tfuente'=>$tfuen,
								'estilo'=>$estilo,'borde'=>$borde,'relleno'=>$fillColor,
								'valor'=>$valor);
	}
	
	/*
	 * Funcion para contruir las tablas
	 * Observaciones:
	 * 1)la construcion de la cabezera y espicificacion de que campos seran mostrados
	 * se realiza con la funcion agrega_col()
	 * 2)Los datos vienen del arreglo asigando en la funcion set_tabla($arreglo)
	 * 3)Los totales en caso de haber se agregan con la funcion set_total()
	 * PARAMETROS:
	 * $pdf,$objt=Son el objeto real y temporal de construccion del Pdf
	 * $limite=especifica hasta que posicion en la coordenada Y puede escribir,
	 * son los limites especificados para los limites de pies de pagina
	 */
	function construyeTabla($pdf,$objt,$limite){
		$bandTot=0;
		$bandViene=0;
		$bandTotalizar=0;
		$bandTotalesPag=0;
		$espCuerpo=5;//numero de espacion entre el cuerpo y el limite
		$objetos=array('pdf','objt');
		$valorCampo=array();
		$objt->SetFont($this->fuenteTabla, 'B', 8);
		
		/*
		 * Construye primera cabezera
		 */
		$objt->setFillColor(0,0,0,5);
		$cancho=0;
		foreach ($this->columnas as $colum){
			if($colum['pos']===true)
				$objt->setX($cancho);
			else{
				$objt->setX($colum['pos']);
				$cancho+=$colum['pos'];
			}
			$cancho+=$colum['ancho'];

			$objt->Cell($colum['ancho'], 4, $colum['titulo'], 1, 0, 'C', 1);
		}
		if(count($this->viene)!=0){
			$objt->ln();
			$this->dibuja_viene($objt);
		}
		
		/*
		 * Verifica si hay que calcular totales al final de la tabla
		 */
		if(count($this->totalizar)!=0){
			$bandTotalizar=1;
		}
		/*
		 * Verifica si hay que calcular totales por pagina
		 */
		if(count($this->totalesPag)!=0){
			$bandTotalesPag=1;
		}
		
		/*
		 * Verifica si hay que calcular subtotales acumulados por pagina
		 */
		if(count($this->totales)!=0){
			$bandTot=1;
			$bandTotalizar=1;
			$espCuerpo= 8;
		}
		
		$objt->ln();
		$pdf=clone $objt;
		$pdf->StartTransaction();

		/*
		 * Recorrido del arreglo a imprimir
		 */
		foreach ($this->tabla AS $items){
			/*
			 * Revisa cual va a ser la altura maxima de la linea
			 */
			
			$mayor=0;
			foreach ($this->columnas as $colum){
				$ncamp=$colum['campo'];
				$valorCampo[$ncamp]=$items[$ncamp];
				$nlinea=$objt->getNumLines($valorCampo[$ncamp],$colum['ancho']);
				$tlineas= 4*$nlinea;
				if ($tlineas>$mayor)$mayor=$tlineas;
			}

			/*
			 * Verifica si la linea a escribir esta dentro del limite predefinido
			 * de no ser asi hace los procesos necesarios para continuar en la proxima pagina
			 */
			if($objt->getY()+$mayor+$espCuerpo >= $this->limite){
				if($bandTot==1)	$this->dibuja_total($objt,1);
				if($bandTotalesPag==1){
					$this->dibuja_totalPag($objt);
					$bandTotalesPag=2;
				}
				
				if($objt->getPage() == 1){
					$objt->setY($objt->lpiep);
					$this->pie($objt);
				}else{
					$objt->setY($objt->lpiem);
					$this->pie2($objt);
				}

				$objt->addPage($this->orientacion,$this->tamPapel);
				$this->limite=$objt->lpiem;
				$pdf->rollbackTransaction(true);
				$pdf=clone $objt;
				$pdf->StartTransaction();

				$objt->SetY($objt->lencabm);
				$this->encab2($objt);

				$pdf->SetY($pdf->lencabu);
				$this->encab3($pdf);
				
				/////LLeva titulo tabla con subTotal viene en caso de tener subtotal
				foreach($objetos as $objT){
					$$objT->setFillColor(0,0,0,5);
					$$objT->SetFont($this->fuenteTabla, 'B', 8);
					$cancho=0;
					foreach ($this->columnas as $colum){
						if($colum['pos']===true)
						$$objT->setX($cancho);
						else{
							$$objT->setX($colum['pos']);
							$cancho+=$colum['pos'];
						}
						$cancho+=$colum['ancho'];

						$$objT->Cell($colum['ancho'], 4, $colum['titulo'], 1, 0, 'C', 1);
					}
					$$objT->ln();
					if($bandTot==1){
						$this->dibuja_total($$objT,0);
						$$objT->ln();
					}
							
				}
					
			}
			
			foreach($objetos as $objT){
				$mayor=$$objT->getY()+4;
				$inicio=$$objT->getY();
				/*
				 * Escribe el contenido de los campos especificados en la funcion agrega_col(),
				 * con los parametros que alli se especificaron
				 */
				$cancho=0;
				foreach ($this->columnas as $colum){
					//$$objT->setXY($colum['pos'],$inicio);
					if($colum['pos']===true)
					$$objT->setXY($cancho,$inicio);
					else{
						$$objT->setXY($colum['pos'],$inicio);
						$cancho+=$colum['pos'];
					}
					$cancho+=$colum['ancho'];
					$ncamp=$colum['campo'];
					$w=$colum['ancho'];
					$borde=$colum['borde'];
					$align=$colum['alinea'];
					$fill=$colum['relleno'];
					$reseth=$colum['reseth'];
					$stretch=$colum['stretch'];
					$autopadding=$colum['autopadding'];
					$valing=$colum['valign'];
					$est=$colum['estilo'];
					$tamLetra=$colum['tfuente'];
					$valor="";
					if (is_numeric($valorCampo[$ncamp]))$valor=nformat($valorCampo[$ncamp]);
					else $valor=$valorCampo[$ncamp];
					$$objT->setFont($this->fuenteTabla,$est , $tamLetra);
					$$objT->MultiCell($w, 4, $valor, $borde,$align,$fill,1,'','',$reseth,$stretch,false,$autopadding,0,$valing);
					$aux=$$objT->getY();
					if($aux>$mayor)$mayor=$aux;
				}
				/*
				 * Para Construir las lineas de las celdas
				 */
				$xf=217;
				$cancho=0;
				foreach($this->columnas as $colum){
					if($colum['pos']===true)
						$colum['pos']=$cancho;
						else{
							$cancho+=$colum['pos'];
						}
						$cancho+=$colum['ancho'];

					$$objT->Line($colum['pos'],$inicio,$colum['pos'],$mayor);
					$xf=$colum['pos']+$colum['ancho'];
					$$objT->Line($colum['pos']+$colum['ancho'],$inicio,$colum['pos']+$colum['ancho'],$mayor);
					$$objT->Line($colum['pos'],$mayor,$xf,$mayor);
				}

				$$objT->setY($mayor);
			}
			/**
			 * Aca acumulamos los totales
			 */
			if ($bandTot==1){
				$i=0;
				foreach ($this->totales as $tot){
					$campTot=$tot['campo'];
					if (isset($valorCampo[$campTot])){
						$this->totales[$i]['valor']+=$valorCampo[$campTot];
					}
					$i++;
				}
			}
			
			if ($bandTotalesPag>0){
				if ($bandTotalesPag==2){
					$j=0;
					foreach ($this->totalesPag as $tot){
						$campTotalesPag=$tot['campo'];
						if (isset($valorCampo[$campTotalesPag])){
							$this->totalesPag[$j]['valor']=0;
						}
						$j++;
					}
					$bandTotalesPag=1;
				}
				$i=0;
				foreach ($this->totalesPag as $tot){
					$campTot=$tot['campo'];
					if (isset($valorCampo[$campTot])){
						$this->totalesPag[$i]['valor']+=$valorCampo[$campTot];
					}
					$i++;
				}
			}
			if ($bandTotalizar==1){
				$i=0;
				foreach ($this->totalizar as $tot){
					$campTotalizar=$tot['campo'];
					if (isset($valorCampo[$campTotalizar])){
						$this->totalizar[$i]['valor']+=$valorCampo[$campTotalizar];
					}
					$i++;
				}
			}
		}
		
		$pdf->commitTransaction();
		if($bandTotalizar==1){
			$this->dibuja_totalizar($pdf);
		}
		if($bandTotalesPag==1){
			$this->dibuja_totalPag($pdf);
		}
		$pdf->ln();
		$this->columnas=array();
		return $pdf;
	}
	
	/*
	 * Funcion usada en caso de existir campos a totalizar
	 * Construye los texto y valores a mostrar
	 */
	function dibuja_total($pdfa,$bandTexto){
		if(count($this->textoTotales)!=0){
			$pos=$this->textoTotales['pos'];
			$ancho=$this->textoTotales['ancho'];
			$borde=$this->textoTotales['borde'];
			$alinea=$this->textoTotales['alinea'];
			$fill=$this->textoTotales['relleno'];
			if($bandTexto==1) $texto=$this->textoTotales['texto'];
			else $texto=$this->textoTotales['texto2'];
			$tfuente=$this->textoTotales['tfuente'];
			$estilo=$this->textoTotales['estilo'];
			$pdfa->setX($pos);
			$pdfa->setFont($this->fuenteTabla, $estilo, $tfuente);
			$pdfa->cell($ancho,4,$texto,$borde,0,$alinea,$fill);
		}
		foreach($this->totales as $total){
			$posTot=$total['pos'];
			$tfuenteTot=$total['tfuente'];
			$estiloTot=$total['estilo'];
			$anchoTot=$total['ancho'];
			$alineaTot=$total['alinea'];
			$bordeTot=$total['borde'];
			$fillTot=$total['relleno'];
			$monto=nformat($total['valor']);
			$pdfa->setX($posTot);
			$pdfa->setFont($this->fuenteTabla, $estiloTot, $tfuenteTot);
			$pdfa->cell($anchoTot,4,$monto,$bordeTot,0,$alineaTot,$fillTot);	
		}
		
	}
	
	/*
	 * Funcion usada en caso de existir campos a totalizar
	 * Construye los texto y valores a mostrar
	 */
	function dibuja_totalPag($pdfa){
		if(count($this->textoTotalesPag)!=0){
			$pos=$this->textoTotalesPag['pos'];
			$ancho=$this->textoTotalesPag['ancho'];
			$borde=$this->textoTotalesPag['borde'];
			$alinea=$this->textoTotalesPag['alinea'];
			$fill=$this->textoTotalesPag['relleno'];
			$texto=$this->textoTotalesPag['texto'];
			$tfuente=$this->textoTotalesPag['tfuente'];
			$estilo=$this->textoTotalesPag['estilo'];
			$pdfa->setX($pos);
			$pdfa->setFont($this->fuenteTabla, $estilo, $tfuente);
			$pdfa->cell($ancho,4,$texto,$borde,0,$alinea,$fill);
		}
		$i=0;
		foreach($this->totalesPag as $total){
			$posTot=$total['pos'];
			$tfuenteTot=$total['tfuente'];
			$estiloTot=$total['estilo'];
			$anchoTot=$total['ancho'];
			$alineaTot=$total['alinea'];
			$bordeTot=$total['borde'];
			$fillTot=$total['relleno'];
			$monto=nformat($total['valor']);
			$pdfa->setX($posTot);
			$pdfa->setFont($this->fuenteTabla, $estiloTot, $tfuenteTot);
			$pdfa->cell($anchoTot,4,$monto,$bordeTot,0,$alineaTot,$fillTot);
			$this->totalesPAg[$i]['valor']=0;
			$i++;	
		}
		
	}
	
	function dibuja_totalizar($pdfa){
		if(count($this->textoTotalizar)!=0){
			$pos=$this->textoTotalizar['pos'];
			$ancho=$this->textoTotalizar['ancho'];
			$borde=$this->textoTotalizar['borde'];
			$alinea=$this->textoTotalizar['alinea'];
			$fill=$this->textoTotalizar['relleno'];
			$texto=$this->textoTotalizar['texto'];
			$tfuente=$this->textoTotalizar['tfuente'];
			$estilo=$this->textoTotalizar['estilo'];
			$pdfa->setX($pos);
			$pdfa->setFont($this->fuenteTabla, $estilo, $tfuente);
			$pdfa->cell($ancho,4,$texto,$borde,0,$alinea,$fill);
		}
		foreach($this->totalizar as $total){
			$posTot=$total['pos'];
			$tfuenteTot=$total['tfuente'];
			$estiloTot=$total['estilo'];
			$anchoTot=$total['ancho'];
			$alineaTot=$total['alinea'];
			$bordeTot=$total['borde'];
			$fillTot=$total['relleno'];
			$monto=nformat($total['valor']);
			$pdfa->setX($posTot);
			$pdfa->setFont($this->fuenteTabla, $estiloTot, $tfuenteTot);
			$pdfa->cell($anchoTot,4,$monto,$bordeTot,0,$alineaTot,$fillTot);	
		}
		
	}
	
	function dibuja_viene($pdfa){
		if(count($this->textoViene)!=0){
			$pos=$this->textoViene['pos'];
			$ancho=$this->textoViene['ancho'];
			$borde=$this->textoViene['borde'];
			$alinea=$this->textoViene['alinea'];
			$fill=$this->textoViene['relleno'];
			$texto=$this->textoViene['texto'];
			$tfuente=$this->textoViene['tfuente'];
			$estilo=$this->textoViene['estilo'];
			$pdfa->setX($pos);
			$pdfa->setFont($this->fuenteTabla, $estilo, $tfuente);
			$pdfa->cell($ancho,4,$texto,$borde,0,$alinea,$fill);
		}
		foreach($this->viene as $total){
			$posTot=$total['pos'];
			$tfuenteTot=$total['tfuente'];
			$estiloTot=$total['estilo'];
			$anchoTot=$total['ancho'];
			$alineaTot=$total['alinea'];
			$bordeTot=$total['borde'];
			$fillTot=$total['relleno'];
			$monto=nformat($total['valor']);
			$pdfa->setX($posTot);
			$pdfa->setFont($this->fuenteTabla, $estiloTot, $tfuenteTot);
			$pdfa->cell($anchoTot,4,$monto,$bordeTot,0,$alineaTot,$fillTot);	
		}
		
	}
	
	
	/*
	 * Funcion para agregar estilos a la funcion dibujaArreglo 
	 */
	function estiloMullti($alinea="",$tfuen,$estilo="",$borde=0,$fillColor=0,$reseth=true,$stretch = 0,$autopadding = true,$valign = 'T'){
		if($tfuen==""){
			$tfuen=$this->fuenteArreglo;
		}
		$estilo=array($alinea,$tfuen,
		$estilo,$borde,$fillColor,
		$reseth,$stretch,
		$autopadding,$valign);
		return $estilo;
	}
	
	/*
	 * Funcion para colocar estilo a la funcion dibujaCuadrosIguales()
	 */
	function estiloCell($tfuen,$estilo='',$w,$h,$borde='1',$salto=0,$align='L',$fill=0,$stretch=0,$calign='T',$valign='B'){
		if($tfuen==""){
			$tfuen=10;
		}
		$estilo=array($tfuen,
		$estilo,$w,$h,$borde,$salto,$align,$fill,
		$stretch,$calign,$valign);
		return $estilo;
	}

	/*
	 * Funcion para dibujar arreglos creados por el usuario
	 * Generalmente se usa en los encabezados
	 */
	function dibujaArreglo($pdf,$objt,$camb=0,$fin=0){
		$objetos=array('pdf','objt');
		$objt=clone $pdf;
		$hasta=$this->limite;
		$totalLineas=0;
		$empieza=$pdf->getY();
		//		echo "aqui comienza $empieza <br>";
		foreach($this->arreglo as $lineas){
			$inicio=$pdf->getY();
			$mayor=$pdf->getY()+4;
			$numLines=0;
			foreach($lineas as $c){
				$numLineas=$pdf->getNumLines($c[3],$c[1])*4;
				$compara=$inicio+$numLineas;
				if($compara>$mayor)$mayor=$compara;
			}
			$totalLineas=$totalLineas+($mayor-$inicio);
			$pdf->setY($mayor);
			//			echo "entro $mayor,$totalLineas<br>";
		}

		$pdf->setY($empieza);
		$llega=$empieza+$totalLineas;
		//		echo"=>>".$llega."<br>";
		if($llega>$this->limite && $camb==1){
			foreach($objetos as $objT){
				if($$objT->getPage() == 1 ){
					$$objT->setY($$objT->lpiep);
					$this->pie($objt);

				}else{
					$$objT->setY($$objT->lpiem);
					$this->pie2($$objT);
				}
				$$objT->addPage($this->orientacion,$this->tamPapel);
				if($fin==0){
					$$objT->SetY($$objT->lencabm);
					$this->encab2($$objT);
					$this->limite=$$objT->lpiem;
				}else{
					$$objT->SetY($$objT->lencabu);
					$this->encab3($$objT);
					$this->limite=$$objT->lpieu;
				}
			}
		}

		foreach($this->arreglo as $lineas){
			foreach($objetos as $objT){

				$cantLineas=0;
				foreach($lineas as $c){
					$numLineas=$$objT->getNumLines($c[3],$c[1])*4;
					if($numLineas>$cantLineas)$cantLineas=$numLineas;
				}
				if(($$objT->getY()+$cantLineas)>$this->limite){

					if($$objT->getPage() == 1 ){
						$$objT->setY($$objT->lpiep);
						$this->pie($objt);

					}else{
						$$objT->setY($$objT->lpiem);
						$this->pie2($$objT);
					}
					$$objT->addPage($this->orientacion,$this->tamPapel);
					if($fin==0){
						$$objT->SetY($$objT->lencabm);
						$this->encab2($$objT);
						$this->limite=$$objT->lpiem;
					}else{
						$$objT->SetY($$objT->lencabu);
						$this->encab3($$objT);
						$this->limite=$$objT->lpieu;
					}

				}
				$inicio=$$objT->getY();
				$mayor=$$objT->getY()+4;
				//				echo "=>".$inicio."<br>";
				foreach($lineas as $c){
					$$objT->setXY($c[0],$inicio);
					$w=$c[1];
					$align=$c[2][0];
					$tamLetra=$c[2][1];
					$est=$c[2][2];
					$borde=$c[2][3];
					$fill=$c[2][4];
					$reseth=$c[2][5];
					$stretch=$c[2][6];
					$autopadding=$c[2][7];
					$$objT->setFont($this->fuenteTabla,$est , $tamLetra);
					$$objT->MultiCell($w, 4, $c[3], $borde,$align,$fill,1,'','',$reseth,$stretch,false,$autopadding,false);
					$aux=$$objT->getY();
					if($aux>$mayor)$mayor=$aux;

				}
				$$objT->setY($mayor);//$$objT->ln();
			}
		}
	}

	/*
	 * Funcion que dibuja cuadros parametrizados con los estilos dados con la funcion estiloCell()
	 * Puede ser usada en la construccion de los pies
	 */
	function dibujaCuadrosIguales($pdf,$objt,$cuadros,$estiloCell){
		$objetos=array('pdf','objt');
		foreach($objetos as $objT){
			//			$$objT->setY($this->limite);
			$$objT->SetFont('helvetica', 'B', 10);
			$ancho=186;
			$tf=$estiloCell[0];
			$ef=$estiloCell[1];
			$h=$estiloCell[3];
			$borde=$estiloCell[4];

			$align=$estiloCell[6];
			$fill=$estiloCell[7];
			$str=$estiloCell[8];
			$calign=$estiloCell[9];
			$valign=$estiloCell[10];
			//			$$objT->setFont($this->fuenteArreglo,$ef,$tf);
			foreach($cuadros as $cuadro){
				$cant=count($cuadro);
				if($estiloCell[2]!=0 && $estiloCell[2]<=186){
					$ancho=$estiloCell[2];
				}

				$w=$ancho/$cant;
				foreach($cuadro as $c){
					$$objT->Cell($w,$h,$c,$borde,0,$align,$fill,'',$str,false,$calign,$valign);

				}
				if($estiloCell[5]==1) $$objT->Ln();
			}
		}
	}

	function instalar(){
		$this->db->simple_query("ALTER TABLE `formatos`  ADD COLUMN `forma1` TEXT NULL AFTER `forma`");
	}

}

?>