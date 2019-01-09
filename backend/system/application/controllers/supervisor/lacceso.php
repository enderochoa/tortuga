<?php
class Lacceso extends Controller {
	var $cargo=0;
	
	function Lacceso() {
		parent::Controller();
		$this->load->library("rapyd");
	}
	function index() {
		
		$this->load->library('PDFReporte');
		//$this->rapyd->load("datafilter");

//*********************		
$this->rapyd->load("datafilter");
$this->rapyd->load("datatable");


$filter = new DataFilter("Filtro de Usuarios");
$filter->attributes=array('onsubmit'=>'is_loaded()');

$filter->db->select('usuario, modulo, acceso');
$filter->db->from('usuario');
$filter->db->orderby('usuario,acceso');

$filter->usuario = new dropdownField("Acceso de Usuario", "usuario");
$filter->usuario->option("","");  
$filter->usuario->options("SELECT us_codigo, us_nombre FROM usuario"); 

$filter->buttons("search");
$filter->build();

if($this->rapyd->uri->is_set("search")){

	$mSQL=$this->rapyd->db->_compile_select();
	//echo $mSQL;


	$pdf = new PDFReporte($mSQL);
	$pdf->setHeadValores('TITULO1');
	$pdf->setSubHeadValores('TITULO2','TITULO3');
	$pdf->setTitulo("Reporte de Sida");
	//$pdf->setSubTitulo($subtitu);
	//$pdf->setSobreTabla($sobretabla);
	$pdf->AddPage();
	$pdf->setTableTitu(8,'Times');

	$pdf->AddCol('us_codigo' ,20 ,'us_codigo'  ,'L',8);
	$pdf->AddCol('us_nombre' ,60 ,'us_nombre'  ,'L',8);
	$pdf->AddCol('supervisor'  ,20 ,'supervisor','C',8);
	$pdf->setGrupoLabel('Usuario: <#usuario#>');
	$pdf->setGrupo('acceso');
	$pdf->Table();
	$pdf->Output();

}else{
	$data["filtro"] = $filter->output;
	$data["titulo"] = '<h2 class="mainheader">Acceso de Usuario<h2>';
	$data["head"]   = $this->rapyd->get_head();
	$this->load->view('view_freportes', $data);
}
//*********************			
	}
	function consulstatus(){
		return 	$this->cargo;
	}
}
?>
