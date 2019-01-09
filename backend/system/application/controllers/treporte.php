<?php 
$base_process_uri= $this->rapyd->uri->implode_uri("base_uri","gfid","orderby");

$mSPRV=array(
				'tabla'   =>'sprv',
				'columnas'=>array(
				'proveed' =>'C&oacute;odigo',
				'nombre'=>'Nombre',
		    'rif'=>'Rif',
				'contacto'=>'Contacto'),
				'filtro'  =>array('proveed'=>'C&oacute;digo','nombre'=>'Nombre','rif'=>'Rif'),
				'retornar'=>array('proveed'=>'cod_prov' ),
				'titulo'  =>'Buscar Proveedor');
		

$bSPRV=$this->datasis->p_modbus($mSPRV,"proveed");

$script='
		$(function() {
				$("#estadmin").change(function(){
					$.post("'.site_url('presupuesto/presupuesto/get_tipo').'",{ codigoadm:$("#estadmin").val() },function(data){$("#fondo").html(data);})
				});
		});
		';

$filter = new DataFilter2($this->rapyd->uri->add_clause($base_process_uri, "search"));
$filter->db->from('view_pres');
$filter->script($script);

$filter->title('');
$filter->attributes=array('onsubmit'=>'is_loaded()');

$ng1 = 'CAMPOS A FILTRAR';
$filter->fechah = new dateonlyField("Hasta", "fechah",'d/m/Y');
$filter->fechah->clause=" ";
$filter->fechah->insertValue = date("Y-m-d");
$filter->fechah->db_name="fecha";
$filter->fechah->operator=" ";
$filter->fechah->dbformat='Y-m-d';
$filter->fechah->group   =$ng1;

$filter->estadmin = new dropdownField("Estructura Administrativa","estadmin");
$filter->estadmin->db_name = 'codigoadm';
$filter->estadmin->option("","Seleccione");
$filter->estadmin->options("SELECT b.codigo, CONCAT_WS(' ',b.codigo,b.denominacion) AS val FROM presupuesto AS a JOIN estruadm AS b ON a.codigoadm=b.codigo  GROUP BY b.codigo");
//$filter->estadmin->style="width:600px";
$filter->estadmin->group   =$ng1;

$filter->fondo = new dropdownField("Fondo", "fondo");
$filter->fondo->option("","");
$filter->fondo->options("SELECT fondo,CONCAT_WS(' ',fondo,descrip)a FROM fondo");
$filter->fondo->group   =$ng1;

$filter->partida = new inputField("Partida", "partida");
$filter->partida-> db_name ="codigopres";
$filter->partida->clause   = "likeonly";
$filter->partida->size     = 25;
$filter->partida->append("Use el caracter porcentaje '%' si desea hacer consultas de partidas multiples EJEMPLO 4.01.01%");
$filter->partida->group   =$ng1;

$filter->ordinal = new inputField("Ordinal", "ordinal");
$filter->ordinal->size     = 5;
$filter->ordinal->append(" Use tres(3) Digitos por EJEMPLO 001");
$filter->ordinal->group   =$ng1;

$filter->cod_prov = new inputField("Proveedor", 'cod_prov');
$filter->cod_prov->size = 6;
$filter->cod_prov->append($bSPRV);
$filter->cod_prov->group   =$ng1;

$ng2 = "SELECCIONE LOS CAMPOS A MOSTRAR EN EL REPORTE";
$filter->vnombre = new checkboxField("Ver Proveedor", "vnombre",'S','N');
$filter->vnombre->insertValue ='N';
$filter->vnombre->clause      = "";
$filter->vnombre->group       = $ng2;

$filter->fnombre =new freeField("fnombre","fnombre","Tama&ntilde;o");
$filter->fnombre->in ="vnombre";
$filter->fnombre->clause = "";

$filter->tnombre = new dropdownField("Tnombre", "tnombre");
$filter->tnombre->option("60","60");
for($i=20;$i<=150;$i+=5)
$filter->tnombre->option("$i","$i");
$filter->tnombre->style="width:60px";
$filter->tnombre->in="vnombre";
$filter->tnombre->clause = "";

$filter->vconcepto = new checkboxField("Ver Concepto", "vconcepto",'S','N');
$filter->vconcepto->insertValue='N';
$filter->vconcepto->clause = "";
$filter->vconcepto->group       = $ng2;

$filter->fconcepto =new freeField("fconcepto","fconcepto","Tama&ntilde;o");
$filter->fconcepto->in ="vconcepto";
$filter->fconcepto->clause = "";

$filter->tconcepto = new dropdownField("Tconcepto", "tconcepto");
$filter->tconcepto->option("60","60");
for($i=20;$i<=150;$i+=5)
$filter->tconcepto->option("$i","$i");
$filter->tconcepto->style="width:60px";
$filter->tconcepto->in="vconcepto";
$filter->tconcepto->clause = "";

$filter->vdenopart = new checkboxField("Ver Denominaci&oacute;n de la Partida", "vdenopart",'S','N');
$filter->vdenopart->insertValue='N';
$filter->vdenopart->clause = "";
$filter->vdenopart->group       = $ng2;

$filter->fdenopart =new freeField("fdenopart","fdenopart","Tama&ntilde;o");
$filter->fdenopart->in ="vdenopart";
$filter->fdenopart->clause = "";

$filter->tdenopart = new dropdownField("Tdenopart", "tdenopart");
$filter->tdenopart->option("60","60");
for($i=20;$i<=150;$i+=5)
$filter->tdenopart->option("$i","$i");
$filter->tdenopart->style="width:60px";
$filter->tdenopart->in="vdenopart";
$filter->tdenopart->clause = "";

$filter->vmodo = new checkboxField("Ver Tipo de Movimiento", "vmodo",'S','N');
$filter->vmodo->insertValue='N';
$filter->vmodo->clause = "";
$filter->vmodo->group       = $ng2;

$filter->vmodo2 = new containerField('vmodo2',"<span class='littletableheader'>Ver Numero de Movimiento<span>");
$filter->vmodo2->in = "vmodo";
$filter->vmodo2->clause = "";

$filter->vnumero = new checkboxField("Ver Numero de Movimiento", "vnumero",'S','N');
$filter->vnumero->insertValue='N';
$filter->vnumero->clause = "";
$filter->vnumero->in ="vmodo";

$filter->vfecha2 = new containerField('vfecha2',"<span style='width:350px;' class='littletableheader'>Ver Fecha</span>");
$filter->vfecha2->in ="vmodo";
$filter->vfecha2->clause = "";

$filter->vfecha = new checkboxField("Ver Fecha", "vfecha",'S','N');
$filter->vfecha->insertValue='N';
$filter->vfecha->clause = "";
$filter->vfecha->in ="vmodo";

$filter->vasignacion = new checkboxField("Ver Asignaci&oacute;n", "vasignacion",'S','N');
$filter->vasignacion->insertValue='N';
$filter->vasignacion->clause = "";
$filter->vasignacion->group       = $ng2;

$filter->vaumento2 = new containerField('vaumento2',"<span class='littletableheader'>Ver Aumento</span>");
$filter->vaumento2->in ="vasignacion";
$filter->vaumento2->clause = "";

$filter->vaumento = new checkboxField("", "vaumento",'S','N');
$filter->vaumento->insertValue='N';
$filter->vaumento->clause = "";
$filter->vaumento->in ="vasignacion";

$filter->vdisminucion2 = new containerField('vdisminucion2',"<span class='littletableheader'>Ver Disminuci&oacute;n</span>");
$filter->vdisminucion2->in ="vasignacion";
$filter->vdisminucion2->clause = "";

$filter->vdisminucion = new checkboxField("", "vdisminucion",'S','N');
$filter->vdisminucion->insertValue='N';
$filter->vdisminucion->clause = "";
$filter->vdisminucion->in ="vasignacion";

$filter->vtraslados2 = new containerField('vtraslados2',"<span class='littletableheader'>Ver Traslados</span>");
$filter->vtraslados2->in ="vasignacion";
$filter->vtraslados2->clause = "";

$filter->vtraslados = new checkboxField("", "vtraslados",'S','N');
$filter->vtraslados->insertValue='N';
$filter->vtraslados->clause = "";
$filter->vtraslados->in ="vasignacion";

$filter->vmodificado2 = new containerField('vmodificado2',"<span class='littletableheader'>Ver Modificado</span>");
$filter->vmodificado2->in ="vasignacion";
$filter->vmodificado2->clause = "";

$filter->vmodificado = new checkboxField("", "vmodificado",'S','N');
$filter->vmodificado->insertValue='N';
$filter->vmodificado->clause = "";
$filter->vmodificado->in ="vasignacion";

$filter->vcomprometido2 = new containerField('vcomprometido2',"<span  class='littletableheader'>Ver Compromiso</span>");
$filter->vcomprometido2->in ="vasignacion";
$filter->vcomprometido2->clause = "";

$filter->vcomprometido = new checkboxField("Ver Compromiso", "vcomprometido",'S','N');
$filter->vcomprometido->insertValue='N';
$filter->vcomprometido->clause = "";
$filter->vcomprometido->in ="vasignacion";

$filter->vcausado2 = new containerField('vcausado2',"<span  class='littletableheader'>Ver Causado</span>");
$filter->vcausado2->in ="vasignacion";
$filter->vcausado2->clause = "";

$filter->vcausado = new checkboxField("Ver Causado", "vcausado",'S','N');
$filter->vcausado->insertValue='N';
$filter->vcausado->clause = "";
$filter->vcausado->in ="vasignacion";

$filter->vopago2 = new containerField('vopago2',"<span style='width:350px;' class='littletableheader'>Ver Ordenado Pago</span>");
$filter->vopago2->in ="vasignacion";
$filter->vopago2->clause = "";

$filter->vopago = new checkboxField("Ver Ordenado Pago", "vopago",'S','N');
$filter->vopago->insertValue='N';
$filter->vopago->clause = "";
$filter->vopago->in ="vasignacion";

$filter->vpagado2 = new containerField('vpagado2',"<span  class='littletableheader'>Ver Pagado</span>");
$filter->vpagado2->in ="vasignacion";
$filter->vpagado2->clause = "";

$filter->vpagado = new checkboxField("Ver Pagado", "vpagado",'S','N');
$filter->vpagado->insertValue='N';
$filter->vpagado->clause = "";
$filter->vpagado->in ="vasignacion";

$filter->vdisponible2 = new containerField('vdisponible2',"<span  class='littletableheader'>Ver Disponible</span>");
$filter->vdisponible2->in ="vasignacion";
$filter->vdisponible2->clause = "";

$filter->vdisponible = new checkboxField("Ver Disponible", "vdisponible",'S','N');
$filter->vdisponible->insertValue='N';
$filter->vdisponible->clause = "";
$filter->vdisponible->in ="vasignacion";

$filter->vacumulado2 = new containerField('vacumulado2',"<span  class='littletableheader'>Ver Acumulado</span>");
$filter->vacumulado2->in ="vasignacion";
$filter->vacumulado2->clause = "";

$filter->vacumulado = new checkboxField("Ver Acumulado", "vacumulado",'S','N');
$filter->vacumulado->insertValue='N';
$filter->vacumulado->clause = "";
$filter->vacumulado->in ="vasignacion";

$filter->agrupara = new checkboxField("Estructura Administrativa", "agrupara",'S','N');
$filter->agrupara->insertValue ='N';
$filter->agrupara->clause      = "";
$filter->agrupara->group       = "AGRUPAR POR:";

$filter->agruparf2 = new containerField('agruparf2',"<span  class='littletableheader'>Fuente de Financiamiento (FONDO)</span>");
$filter->agruparf2->in ="agrupara";
$filter->agruparf2->clause = "";

$filter->agruparf = new checkboxField("Fuente de Financiamiento (FONDO)", "agruparf",'S','N');
$filter->agruparf->insertValue ='N';
$filter->agruparf->clause      = "";
$filter->agruparf->group       = "AGRUPAR POR:";
$filter->agruparf->in ="agrupara";

$filter->agruparp2 = new containerField('agruparf2',"<span  class='littletableheader'>Partida Presupuestaria</span>");
$filter->agruparp2->in ="agrupara";
$filter->agruparp2->clause = "";

$filter->agruparp = new checkboxField("Partida Presupuestaria", "agruparp",'S','N');
$filter->agruparp->insertValue ='N';
$filter->agruparp->clause      = "";
$filter->agruparp->group       = "AGRUPAR POR:";
$filter->agruparp->in ="agrupara";

$filter->agruparo2 = new containerField('agruparo2',"<span  class='littletableheader'>Ordinal</span>");
$filter->agruparo2->in ="agrupara";
$filter->agruparo2->clause = "";

$filter->agruparo = new checkboxField("Ordinal", "agruparo",'S','N');
$filter->agruparo->insertValue ='N';
$filter->agruparo->clause      = "";
$filter->agruparo->group       = "AGRUPAR POR:";
$filter->agruparo->in ="agrupara";

$lista = array(
"fecha"        =>"Documento",
"fcomprome"    =>"Compromiso",
"fcausado"     =>"Causaci&oacute;n",
"fopago"       =>"Ordenado Pago",
"fpagado"      =>"Pagado"
);

$ng3="CONFIGURACIONES GENERALES DEL REPORTE";
$filter->cfecha = new radiogroupField("Usar Fecha de ", "cfecha", $lista,"fecha");
$filter->cfecha->clause = "";
$filter->cfecha->rule   ="required";
$filter->cfecha->group  = $ng3;

$filter->agruparv = new checkboxField("Ver Solo Partidas", "agruparv",'S','N');
$filter->agruparv->insertValue ='N';
$filter->agruparv->clause      = "";
$filter->agruparv->group       = $ng3;

$filter->tpagina = new dropdownField("Tama&ntilde;o de la pagina", "tpagina");
$filter->tpagina->option("Letter","Carta");
$filter->tpagina->option("Legal" ,"Oficio");
$filter->tpagina->option("A3","A3");
$filter->tpagina->option("A4","A4");
$filter->tpagina->option("A5","A5");
$filter->tpagina->style="width:150px";
$filter->tpagina->clause = "";
$filter->tpagina->group   =$ng3;

$filter->titulo = new inputField("Titulo del Reporte", "titulo");
$filter->titulo->clause   = "";
$filter->titulo->size     = 60;
$filter->titulo->group   =$ng3;

$filter->salformat = new radiogroupField("Formato de salida","salformat");
$filter->salformat->options($this->opciones);
$filter->salformat->insertValue ='PDF';
$filter->salformat->clause = '';
$filter->salformat->group   =$ng3;

$filter->buttons("search");
$filter->build();


if($this->rapyd->uri->is_set("search")){

	$subtitu='';
  if(isset($_POST['fechah'])) $subtitu.=' Hasta '.$_POST['fechah'];
  $post=array("vdisponible","vacumulado","vmodificado","vcomprometido","vcausado","vopago","vpagado","vnombre","tnombre","vconcepto","tconcepto","fechah","tpagina","cfecha","titulo","agrupara","agruparf","agruparp","agruparo","agruparv","vasignacion","vdenopart","tdenopart","vmodo","vnumero","vfecha","vaumento","vdisminucion","vtraslados");
	foreach($post AS $value){
		if(isset($_POST[$value]))
			$$value = $_POST[$value];
		else
			$$value = NULL;
	}
	
	if(!$titulo)$titulo="Movientos de Partida";
	
	$date   = DateTime::createFromFormat('d/m/Y',$fechah);
	$fechah = $date->format('Ymd');
	
	//$this->db->query("");
	
	$lista = array(
		"fecha"        =>"ccomprometido",
		"fcomprome"    =>"ccomprometido",
		"fcausado"     =>"ccausado",        
		"fopago"       =>"copago",       
		"fpagado"      =>"cpagado" 
	);
	
	if(!$cfecha)
		$cfecha = "fecha";
	
	$mSQL="
	SELECT SUM(asignacion+aumento-disminucion+traslados-".substr($lista[$cfecha],1).") disponible,IF(modo='AUMENTO' OR modo='DISMINUCION',faudis,IF(modo='Traslado',ftrasla,IF(modo='Asignacion','',$cfecha))) fmostrar,SUM(asignacion+aumento-disminucion+traslados) modificado,fecha,des,observa,cod_prov,numero,status,modo,codigoadm,fondo,codigopres,ordinal,faudis,ftrasla,fcomprome,fcausado,fopago,fpagado,frendi,SUM(comprometido)comprometido,SUM(causado)causado,SUM(opago)opago,SUM(pagado)pagado,SUM(aumento)aumento,SUM(disminucion)disminucion,SUM(traslados)traslados,SUM(asignacion)asignacion,denopart,denoadm,denofondo,nombre,SUM(ccomprometido)ccomprometido,SUM(ccausado)ccausao,SUM(copago)copago,SUM(cpagado)cpagado FROM (
	SELECT * FROM view_pres WHERE asignacion >0 ";
	
	$mSQL2=$this->rapyd->db->_compile_select();

	$pos=strripos($mSQL2,"WHERE");
	
	if($pos>0){	
		$where=substr($mSQL2,$pos+5);
		$mSQL2.=" AND (ftrasla<= $fechah OR faudis <= $fechah OR frendi <= $fechah OR ";
		$mSQL.=" AND ".$where;
	}else
		$mSQL2.=" WHERE (ftrasla<= $fechah OR faudis <= $fechah OR frendi <= $fechah OR ";
	

	$mSQL2.=" $cfecha <= $fechah )";
	
	$mSQL.= " UNION ALL ";
	$mSQL.=$mSQL2;
	
	$mSQL.=" 
	)reporte
	GROUP BY ".($agruparv  ?' codigoadm,fondo,codigopres,ordinal':"modo,numero,codigoadm,fondo,codigopres,ordinal")."
	ORDER BY codigoadm,fondo,codigopres,ordinal,fecha,modo='asignacion' 
	";
	//echo "-->".$agruparv."<--";
	
	
	
	memowrite($mSQL,'MOVI');
	$pdf = new PDFReporte($mSQL,'L',$tpagina);
	$pdf->setHeadValores('TITULO1');
	$pdf->setSubHeadValores('TITULO2','TITULO3');
	$pdf->setTitulo($titulo);

  $pdf->setSubTitulo($subtitu);
	$pdf->AddPage();
	$pdf->setTableTitu(9,'Times');

	if(!$agrupara)
		$pdf->AddCol('codigoadm'    ,25         ,'Est. Administrativa'    ,'L'   ,8);
	if(!$agruparf)
		$pdf->AddCol('fondo'        ,20         ,'Fondo'                  ,'L'   ,8);
	if(!$agruparp)
		$pdf->AddCol('codigopres'   ,25         ,'Partida'                ,'L'   ,8);
	if(!$agruparp)
		$pdf->AddCol('ordinal'      ,25         ,'Ordinal'                ,'L'   ,8);
	if($vmodo) 
		$pdf->AddCol('modo'         ,25         ,'Documento'              ,'L'   ,8);
	if($vnumero) 
		$pdf->AddCol('numero'       ,15         ,'Numero'                 ,'L'   ,8);
	if($vfecha)
	$pdf->AddCol('fmostrar'       ,15         ,'Fecha'                  ,'C'   ,8);	
	if($vconcepto)
		$pdf->AddCol('observa'      ,$tconcepto ,'Concepto'               ,'L'   ,8);
	if($vnombre)
		$pdf->AddCol('nombre'       ,$tnombre   ,'Beneficiario'           ,'L'   ,8);
  if($vdenopart)
  	$pdf->AddCol('denopart'     ,$tdenopart ,'Denominacón'            ,'L'   ,8);
  if($vasignacion){
		$pdf->AddCol('asignacion'   ,20         ,'Asignacion'             ,'R'   ,8);
		$pdf->setTotalizar('asignacion');
  }
  if($vaumento){
		$pdf->AddCol('aumento'      ,20         ,'Aumento'                ,'R'   ,8);
		$pdf->setTotalizar('aumento');
  }
	if($vdisminucion){
		$pdf->AddCol('disminucion'  ,20         ,'Disminución'            ,'R'   ,8);
		$pdf->setTotalizar('disminucion');
	}
	if($vtraslados){
		$pdf->AddCol('traslados'    ,20         ,'Traslados'              ,'R'   ,8);
		$pdf->setTotalizar('traslados');
	}
	if($vmodificado){
		$pdf->AddCol('modificado'   ,20         ,'Modificado'             ,'R'   ,8);
		$pdf->setTotalizar('modificado');
	}
  if($vcomprometido){
  	$pdf->AddCol('comprometido' ,20         ,'Comprometido'           ,'R'   ,8);
  	$pdf->setTotalizar('comprometido');
  }
  if($vcausado){
  	$pdf->AddCol('causado'      ,20         ,'Causado'                ,'R'   ,8);
  	$pdf->setTotalizar('causado');
  }
  if($vopago){
		$pdf->AddCol('opago'        ,20         ,'O. Pago'                ,'R'   ,8);
		$pdf->setTotalizar('opago');
  }
	if($vpagado){
		$pdf->AddCol('pagado'       ,20         ,'Pagado'                 ,'R'   ,8);
		$pdf->setTotalizar('pagado');
	}
	if($vdisponible){
		$pdf->AddCol('disponible'   ,20         ,'Disponible'             ,'R'   ,8);
		$pdf->setTotalizar('disponible');
	}	
	if($vacumulado){
		$pdf->AddCol($lista[$cfecha]  ,20         ,'Acumulado'              ,'R'   ,8);
		$pdf->setAcumulador($lista[$cfecha]);
	}
	
	
	//$pdf->setTotalizar('ccomprometido');
	
	

	$gl = $g =array();
	if($agrupara){
		$pdf->setGrupoLabel('<#codigoadm#> <#denoadm#>'); 
		$pdf->setGrupo('codigoadm');	
	}
	
	if($agruparf){
		$pdf->setGrupoLabel('     <#fondo#> <#fondonom#>'); 
		$pdf->setGrupo('fondo');
	}
	
	if($agruparp){
		$pdf->setGrupoLabel('          <#codigopres#> <#denomip#>'); 
		$pdf->setGrupo('codigopres');
	}
	
	if($agruparo){
		$pdf->setGrupoLabel('<#ordinal#> <#denomip#>'); 
		$pdf->setGrupo('ordinal');
	}
	
	$pdf->Table();
	$pdf->Output();

}else{
	$data["filtro"] = $filter->output;
	$data["titulo"] = '<h2 class="mainheader">Reporte de Partidas Configurable<h2>';
	$data["head"] = script("jquery.js").$this->rapyd->get_head();
	$this->load->view('view_freportes', $data);
}
