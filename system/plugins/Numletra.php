<?php
/**
 * OEOG Class para convertir numeros en palabras 
 * 
 * 
 * @version   $Id: CNumeroaLetra.php,v 1.0.1 2004-10-29 13:20 ortizom Exp $
 * @author    Omar Eduardo Ortiz Garza <ortizom@siicsa.com>
 *                    gracias a Alberto Gonzalez por su contribucion para corregir
 *                    dos errores
 * @copyright (c) 2004-2005 Omar Eduardo Ortiz Garza
 * @since     Friday, October 29, 2004
 **/
/***************************************************************************
 *
 *  Este programa es software libre; puedes redistribuir y/o modificar
 *  bajo los terminos de la GNU General Public License como se publico por
 *  la Free Software Foundation; version 2 de la Licencia, o cualquier
 *  (a tu eleccion) version posterior.
 *
 ***************************************************************************/

class numletra{
/***************************************************************************
 *
 *	Propiedades:
 *	$numero:	Es la cantidad a ser convertida a letras maximo 999,999,999,999.99
 *	$genero:	0 para femenino y 1 para masculino, es util dependiendo de la
 *				lcentimo ej: cuatrocientos pesos / cuatrocientas pesetas
 *	$lcentimo:	nombre de la lcentimo
 *	$prefijo:	texto a imprimir antes de la cantidad 
 *	$sufijo:	texto a imprimir despues de la cantidad
 *				tanto el $sufijo como el $prefijo en la impresion de cheques o
 *				facturas, para impedir que se altere la cantidad
 *	$mayusculas: 0 para minusculas, 1 para mayusculas indica como debe 
 *				mostrarse el texto
 *	$textos_posibles: contiene todas las posibles palabras a usar
 *	$aTexto:	es el arreglo de los textos que se usan de acuerdo al genero 
 *				seleccionado
 *
 ***************************************************************************/

	private $numero=0;
	private $genero=1;
	private $lcentimo=" BOLIVARES CON ";
	private $prefijo="";
	private $sufijo="CENTIMOS";
	private $mayusculas=0;
	//textos
	private $textos_posibles= array(
	0 => array ('una ','dos ','tres ','cuatro ','cinco ','seis ','siete ','ocho ','nueve ','un '),
	1 => array ('once ','doce ','trece ','catorce ','quince ','dieciseis ','diecisiete ','dieciocho ','diecinueve ',''),
	2 => array ('diez ','veinte ','treinta ','cuarenta ','cincuenta ','sesenta ','setenta ','ochenta ','noventa ','veinti'),
	3 => array ('cien ','doscientas ','trescientas ','cuatrocientas ','quinientas ','seiscientas ','setecientas ','ochocientas ','novecientas ','ciento '),
  4 => array ('cien ','doscientos ','trescientos ','cuatrocientos ','quinientos ','seiscientos ','setecientos ','ochocientos ','novecientos ','ciento '),
	5 => array ('mil ','millon ','millones ','cero ','y ','uno ','dos ','con ','','')
	);
	private $aTexto;

/***************************************************************************
 *
 *	Metodos:
 *	_construct:	Inicializa textos
 *	setNumero:	Asigna el numero a convertir a letra
 *  setPrefijo:	Asigna el prefijo
 *	setSufijo:	Asiga el sufijo
 *	setMoneda:	Asigna la lcentimo
 *	setGenero:	Asigan genero 
 *	setMayusculas:	Asigna uso de mayusculas o minusculas
 *	letra:		Convierte numero en letra
 *	letraUnidad: Convierte unidad en letra, asigna miles y millones
 *	letraDecena: Contiene decena en letra
 *	letraCentena: Convierte centena en letra
 *
 ***************************************************************************/	
	function __construct(){
		for($i=0; $i<6;$i++)
   			for($j=0;$j<10;$j++)
				$this->aTexto[$i][$j]=$this->textos_posibles[$i][$j];
	}

	function setNumero($num){
		$this->numero=(double)$num;
	}

	function setPrefijo($pre){
		$this->prefijo=$pre;
	}

	function setSufijo($sub){
		$this->sufijo=$sub;
	}

	function setLcentimo($mon){
		$this->lcentimo=$mon;
	}

	function setGenero($gen){
		$this->genero=(int)$gen;
	}

	function setMayusculas($may){
		$this->mayusculas=(int)$may;
	}

	function letra(){
		if($this->genero==1){ //masculino
			$this->aTexto[0][0]=$this->textos_posibles[5][5];
			for($j=0;$j<9;$j++)
            	$this->aTexto[3][$j]= $this->aTexto[4][$j];

		}else{//femenino
			$this->aTexto[0][0]=$this->textos_posibles[0][0];
			for($j=0;$j<9;$j++)
            	$this->aTexto[3][$j]= $this->aTexto[3][$j];
		}

		$cnumero=sprintf("%015.2f",$this->numero);
		$texto="";
		if(strlen($cnumero)>15){
			$texto="Excede tamaño permitido";
		}else{
			$hay_significativo=false;
			for ($pos=0; $pos<12; $pos++){
				// Control existencia Dígito significativo 
   				if (!($hay_significativo)&&(substr($cnumero,$pos,1) == '0')) ;
   				else $hay_dignificativo = true;

   				// Detectar Tipo de Dígito 
   				switch($pos % 3) {
   					case 0: $texto.=$this->letraCentena($pos,$cnumero); break;
   					case 1: $texto.=$this->letraDecena($pos,$cnumero); break;
   					case 2: $texto.=$this->letraUnidad($pos,$cnumero); break;
				}
			}
   			// Detectar caso 0 
   			if ($texto == '') $texto = $this->aTexto[5][3];
			if($this->mayusculas){//mayusculas
				$texto=strtoupper($this->prefijo.$texto." ".$this->lcentimo." ".substr($cnumero,-2)."/100 ".$this->sufijo);	
			}else{//minusculas
				$texto=strtolower($this->prefijo.$texto." ".$this->lcentimo." ".substr($cnumero,-2)."/100 ".$this->sufijo);	
			}
		}
		return $texto;

	}

	public function __toString() {
		return $this->letra();
	}

	//traducir letra a unidad
	private function letraUnidad($pos,$cnumero){
		$unidad_texto="";
   		if( !((substr($cnumero,$pos,1) == '0') || 
               (substr($cnumero,$pos - 1,1) == '1') ||
               ((substr($cnumero, $pos - 2, 3) == '001') &&  (($pos == 2) || ($pos == 8)) ) 
             )
		  ){ 
			if((substr($cnumero,$pos,1) == '1') && ($pos <= 6)){
   				$unidad_texto.=$this->aTexto[0][9]; 
			}else{
				$unidad_texto.=$this->aTexto[0][substr($cnumero,$pos,1) - 1];
			}
		}
   		if((($pos == 2) || ($pos == 8)) && 
		   (substr($cnumero, $pos - 2, 3) != '000')){//miles
			if(substr($cnumero,$pos,1)=='1'){
				if($pos <= 6){
					$unidad_texto=substr($unidad_texto,0,-1)." ";
				}else{
					$unidad_texto=substr($unidad_texto,0,-2)." ";
				}
				$unidad_texto.= $this->aTexto[5][0]; 
			}else{
				$unidad_texto.=$this->aTexto[5][0]; 
			}
		}
        if($pos == 5 && substr($cnumero, 0, 6) != '000000'){
			if(substr($cnumero, 0, 6) == '000001'){//millones
			  $unidad_texto.=$this->aTexto[5][1];
			}else{
				$unidad_texto.=$this->aTexto[5][2];
			}
		}
		return $unidad_texto;
	}
	//traducir digito a decena
	private function letraDecena($pos,$cnumero){
		$decena_texto="";
   		if (substr($cnumero,$pos,1) == '0'){
			return;
		}else if(substr($cnumero,$pos + 1,1) == '0'){ 
   			$decena_texto.=$this->aTexto[2][substr($cnumero,$pos,1)-1];
		}else if(substr($cnumero,$pos,1) == '1'){ 
   			$decena_texto.=$this->aTexto[1][substr($cnumero,$pos+ 1,1)- 1];
		}else if(substr($cnumero,$pos,1) == '2'){
   			$decena_texto.=$this->aTexto[2][9];
		}else{
   			$decena_texto.=$this->aTexto[2][substr($cnumero,$pos,1)- 1] . $this->aTexto[5][4];
		}
		return $decena_texto;
   	}
	//traducir digito centena
   	private function letraCentena($pos,$cnumero){
		$centena_texto="";
   		if (substr($cnumero,$pos,1) == '0') return;
   		$pos2 = 3;
		if((substr($cnumero,$pos,1) == '1') && (substr($cnumero,$pos+ 1, 2) != '00')){
   			$centena_texto.=$this->aTexto[$pos2][9];
   		}else{
   			$centena_texto.=$this->aTexto[$pos2][substr($cnumero,$pos,1) - 1];
		}
		return $centena_texto;
	}

}
