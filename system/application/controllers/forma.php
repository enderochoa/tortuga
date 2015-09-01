<?php
class Forma extends Controller{

	var $_direccion;
	var $codigo    =array();
	var $parametros=array();
	var $lencabp;
	var $lencabm;
	var $lencabu;
	var $lpiep;
	var $lpiem;
	var $lpieu;

	function Forma(){
		parent::Controller();
		$this->load->library("rapyd");
		//$this->load->library("numletra");
	}

	function index(){
		$this->ver2();
	}

	function ver2(){
		$this->parametros= func_get_args();
		if (count($this->parametros)>0){
			$_arch_nombre=implode('-',$this->parametros);
			$_fnombre=array_shift($this->parametros);
			$repo=file_get_contents('/srv/www/htdocs/tortuga/system/application/controllers/tforma.php');
			
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
			
		//print "<pre>";
		//print_r($this->codigo);
		//print "</pre>";
		$this->load->library("infor_pdf");

		$o = new infor_pdf;
		$t = new infor_pdf;
		$this->config($t);
		//		$t->SetCreator('Judelvis Rivas');
		//		$t->SetAuthor('Judelvis');
		//		$t->SetTitle('Prueba1');
		//		$t->SetSubject('Para preba');
		//		$t->SetKeywords('TCPDF, PDF, example, test, guide');
		//		$t->setHeaderFont(Array('helvetica', '', 6));
		//
		//		$titulo1 = $this->datasis->traevalor('TITULO1');
		//		$titulo2 = $this->datasis->traevalor('TITULO2');
		//		$titulo3 = $this->datasis->traevalor('TITULO3');
		//		$titulo4 = $this->datasis->traevalor('RIF');
		//		$pie     = $this->datasis->traevalor('SISTEMA');
		//		$t->SetHeaderData('logo.jpg',10,$titulo1,$titulo2."\n".$titulo3."\n".$titulo4);
		//		$t->SetFont('times', 'B', 10);
		//
		//
		//		$t->AddPage('','LETTER');

		//$o->StartTransaction();
		//		for($i=0;$i<=200;$i++){
		//			if($t->getY() >= 240){
		//				$t->addPage('','LETTER');
		//				$o=clone $t;
		//				$o->setY(100);
		//				$o->rollbackTransaction(true);
		//				$o->StartTransaction();
		//				$t->setY(50);
		//			}
		//			$t->Cell(86, 4, "Pagina t: ".$t->getPage()." Linea:".$t->getY().' fila:'.$i, 0, 1, 'R');
		//			$o->Cell(86, 4, "Pagina t: ".$t->getPage()." Linea:".$t->getY().' fila:'.$i, 0, 1, 'R');
		//		}
		//		$o->commitTransaction();
		$this->cuerpo($t,$o);
		//$this->infor_pdf->imprime($o);
	}

	function ver(){
		$this->load->library("rapyd");
		$this->parametros= func_get_args();
		if (count($this->parametros)>0){
			$_arch_nombre=implode('-',$this->parametros);
			$_fnombre=array_shift($this->parametros);
			$repo=$this->datasis->dameval("SELECT forma FROM formatos WHERE nombre='$_fnombre'");
				
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
			
		$this->load->library("infor_pdf");

		$o = new infor_pdf;
		$t = new infor_pdf;
		$this->config($t);
		$this->cuerpo($t,$o);
	}



	function encabezados(){
		$np = $this->infor_pdf->getNumPages();
		for($i = 1;$i <= $np;$i++){
			$this->infor_pdf->setPage($i,true);
			if(empty($this->codigo['encab1']) && empty($this->codigo['encab2'])){
				$this->encab();
			}else{
				if($i == 1){$this->encab();}
				elseif($i == $np){
					$this->encab3();
				}
				else{
					$this->encab2();
				}
			}
		}
	}


	function pies(){
		$np = $this->infor_pdf->getNumPages();
		if($np==1){
			$this->pie3();
		}else{
			for($i = 1;$i <= $np;$i++){
				$this->infor_pdf->setPage($i,true);
				if($i == 1){
					$this->pie();
				}
				elseif($i == $np){
					$this->pie3();
				}
				else{
					$this->pie2();
				}
			}
		}
		//$this->infor_pdf->setPage(2,false);
		//$this->pie();
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
		
	function enc_der($tipo,$numero,$fecha,$re,$pdf,$img=""){
		//$inicio = $this->infor_pdf->getY();
		if($img!=""){
			$pdf->Image(K_PATH_IMAGES.$img, 100, 4, 23);
		}
		eval($this->codigo['enc_der']);
		$this->infor_pdf->setY($this->lencabp);
		$this->infor_pdf->SetFont('times', '', 8);
	}

	function forma_header($pdf){
		eval($this->codigo['forma_header']);
	}

	
	function posicion($t){
		$this->infor_pdf->SetAutoPageBreak(false);
		if($t == 1){$this->infor_pdf->setY($this->infor_pdf->linea_pie);}
		elseif($t == 2){$this->infor_pdf->setY($this->infor_pdf->linea_pie2);}
		else{$this->infor_pdf->setY($this->infor_pdf->linea_pie3);}
	}

	function valE(){
		$np = $this->infor_pdf->getNumPages();
		if($this->infor_pdf->getPage()  !=  1){
			$this->infor_pdf->setPage(1,true);
			//$this->infor_pdf->SetTopMargin(60);
		}
	}


}
//////////////////////////
?>