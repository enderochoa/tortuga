<?php
class Prueba extends Controller {
	var $join;
	
	function Prueba(){
		parent::Controller(); 
		//$this->load->plugin('numletra');
	}

	function index($pertenece=0){
		$pws=bindec('==**');
		#$pws='hola como estas ';
		//echo $pws;
		echo date('Y-m-01');
		//echo base64_decode($pws);
		//$this->load->helper('openflash');
		//$grafico = open_flash_chart_object(680,450, 'http://192.168.0.99/ofc/php5-ofc-library/examples/area-hollow.php');
		//$areglo=arr_menu();
		//$amenu='';
		//foreach($areglo AS $data){
		//	$link=site_url("/prueba/index/$data[modulo]");
		//	$menu[] ="<a href='$link' onMouseover='expandcontent(\"sc$data[modulo]\", this)'>$data[titulo]</a>";
		//	$amenu .="<div id='sc$data[modulo]' class='tabcontent'>$data[mensaje]</div>\n";
		//}
		//$data['menu']=ul($menu); //Menu principal
		//$data['amenu']='';   //Ayudas de Menu
		//$data['contenido']=$grafico;
		//$data['smenu']=$this->_acordeon($pertenece);  //Submenu
		//$data['copyright'] = "Copyright (c) 2006-2007 Inversiones DREMANVA, C.A.<br>Telf: 58 (274) 2711922 MERIDA - VENEZUELA"; 
		//$this->load->view('view_bienvenido', $data);
	}

	function nletras(){
		$this->load->plugin('numletra');
		echo numletra(115000.5);
	}

	function reportes(){
		$this->load->library("XLSReporte");		
		$mSQL='SELECT * FROM muro order by envia,recibe,codigo';
		$xls = new XLSReporte($mSQL);				
		$xls->setTitulo("TARJETA");
		$xls->setSubTitulo("Sub Titulo de Tarjeta");
		$xls->setSobreTabla("Este es el titulo d la tabla");
		$xls->setHeadValores('TITULO1');
		$xls->setSubHeadValores('TITULO2','TITULO3');

		//$xls->AddCol('estampa','Estampa' ); 
		$xls->AddCol('envia'  ,5,'Envia'   ,'L');
		$xls->AddCol('codigo' ,50,'Codigo'  ,'L');
		$xls->AddCol('recibe' ,20,'Recibe'  ,'L');
		$xls->AddCol('mensaje',100,'Mensaje' ,'L');
		//$xls->AddCol('',100,'Mensaje' ,'L');

		$xls->setTotalizar('codigo','envia','recibe');
		$xls->setGrupoLabel('Agrupado por la persona que envia:<#envia#>','y tamnien por la que recibe:<#recibe#>');
		$xls->setGrupo('envia','recibe');
		$xls->Table();
		$xls->Output();
	}
	
	function consola(){
		//echo ord(urldecode('%26'));
		$mk=mktime(0, 0 , 0, date("n")-12,date("j"), date("Y"));
		echo date('d/m/Y',$mk);
	}
	
	function regular(){
	$cadena='<> 0987654l321.0000';
	//if(
	
	echo preg_match("/^([<>=]|[><]=|<>) +[0-9yYoO]+([0-9]|\.[0-9]+)$/",$cadena);
	//)
	//echo "esta fino" ;
	//else
	//echo "la cague";
	
	}
	
	function pru(){
	$query="select a.codigo,a.codigo ad from 
	uejecutora a 
	LEFT join estruadm b on a.codigo =b.uejecutora
	WHERE b.uejecutora IS NULL AND a.codigo <>'0000'";
$arr =$this->datasis->consularray($query);
echo implode("','",$arr);
	}
	
	function prue(){
		//echo system("cp /home/ender/www/htdocs/hola.txt /hola2.txt");
		echo system("/tunel        ");
		
	}
}   
?>