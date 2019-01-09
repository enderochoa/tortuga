<?
$select="tipo_doc, numero, vd, cod_cli, b.nombre, 
if(tipo_doc='F',totalg,-totalg)*if(tipo_doc='X',0,1) as vtotal, 
if(tipo_doc IN ('F','D') AND (referen='E' OR referen='M' ),totalg,0)*if(tipo_doc='X',0,1)*IF(tipo_doc='D',-1,1) as contado, 
if((tipo_doc IN ('F','D') AND referen='C'),totalg,0)*if(tipo_doc='X',0,1)*IF(tipo_doc='D',-1,1) as credito, 
if(tipo_doc='X',totalg,0.00) as anulado ";

$filter = new DataFilter("Filtro de Reporte");
$filter->db->select($select);
$filter->db->from('sfac a');
$filter->db->join("scaj as b" ,"a.cajero=b.cajero","LEFT");
$filter->db->join("scli AS c","a.cod_cli=c.cliente","LEFT");
$filter->db->where("referen<>'P'");


$filter->fechad = new dateField("Desde", "fechad",'d/m/Y');
$filter->fechah = new dateField("Hasta", "fechah",'d/m/Y');
$filter->fechad->clause  =$filter->fechah->clause="where";
$filter->fechad->db_name =$filter->fechah->db_name="a.fecha";
$filter->fechad->operator=">="; 
$filter->fechah->operator="<="; 

//$filter->grupo = new dropdownField("Grupo", "grupo");
//$filter->grupo->db_name = 'b.grupo';
//$filter->grupo->option("","");                                  
//$filter->grupo->options("SELECT grupo, nom_grup FROM grga");

$filter->buttons("search");
$filter->build();

if($this->rapyd->uri->is_set("search")){
	$mSQL=$this->rapyd->db->_compile_select();
	echo $mSQL;
	//$pdf = new PDFReporte($mSQL);
	//$pdf->setHeadValores('TITULO1');
	//$pdf->setSubHeadValores('TITULO2','TITULO3');
	//$pdf->setTitulo("Gastos por Cuenta agrupado y resumido");
	//$pdf->AddPage();
	//$pdf->setTableTitu(8,'Times');
  //
  //
	//$pdf->AddCol('descrip' ,40,'Descripción','L',4);
	//$pdf->AddCol('m01' ,14 ,'Enero'      ,'R',4);
	//$pdf->AddCol('m02' ,14 ,'Febrero'    ,'R',4);
	//$pdf->AddCol('m03' ,14 ,'Marzo'      ,'R',4);
	//$pdf->AddCol('m04' ,14 ,'Abril'      ,'R',4);
	//$pdf->AddCol('m05' ,14 ,'Mayo'       ,'R',4);
	//$pdf->AddCol('m06' ,14 ,'Junio'      ,'R',4);
	//$pdf->AddCol('m07' ,14 ,'Julio'      ,'R',4);
	//$pdf->AddCol('m08' ,14 ,'Agosto'     ,'R',4);
	//$pdf->AddCol('m09' ,14 ,'Septiembre' ,'R',4);
	//$pdf->AddCol('m10' ,14 ,'Octubre'    ,'R',4);
	//$pdf->AddCol('m11' ,14 ,'Noviembre'  ,'R',4);
	//$pdf->AddCol('m12' ,14 ,'Diciembre'  ,'R',4);
	//
	//$pdf->setGrupoLabel('Departamento: <#departa#>','Grupo: <#grupo#>');
	//$pdf->setGrupo('departa','grupo');
	//$pdf->setTotalizar('m01','m02','m03','m04','m05','m06','m07','m08','m09','m10','m11','m12');
	//$pdf->Table();
	//$pdf->Output();
			
}else{
	$data["filtro"] = $filter->output;
	$data["titulo"] = '<h2 class="mainheader">Gastos por Grupo/Me<h2>';
	$data["head"]   = $this->rapyd->get_head();
	$this->load->view('view_freportes', $data);
}