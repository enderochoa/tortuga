<?php
class aumentoempleados extends Controller {  
  
	function aumentoempleados() {
	  parent::Controller();
	  $this->load->library("rapyd");
	}  
	
	  function index(){
	  //$this->rapyd->load("datagrid");
	  $this->rapyd->load("dataform");
		$this->load->helper('openflash');
		
		if(isset($_POST['anio']) AND empty($anio)) $anio=$_POST['anio'];
		if(isset($_POST['codigo']) AND empty($codigo)) $codigo=$_POST['codigo'];		
		
		if (empty($anio))$anio=date("Y");
		//if(empty($codigo)) redirect('ventas/anioventcli/');

		$fechai=$anio.'0101';
		$fechaf=$anio.'1231';
		
	  $scli=array(
	  'tabla'   =>'pers',
	  'columnas'=>array(
	  'codigo' =>'C&oacute;digo Empleado',
	  'nombre'  =>'Nombre'),
	  'filtro'  =>array('codigo'=>'C&oacute;digo codigo','nombre'=>'Nombre'),
	  'retornar'=>array('codigo'=>'codigo'),
	  'titulo'  =>'Buscar Empleado');
	  	  
	  $cboton=$this->datasis->modbus($scli);
		
		$filter = new DataForm('ventas/aumentoempleados');
		$filter->title('Filtro de Aumento de Sueldo ');
				
		$filter->anio = new inputField("A&ntilde;o", "anio");
		$filter->anio->size=4;
		$filter->anio->insertValue=$anio;
		$filter->anio->rule = "max_length[4]"; 
		
		$filter->codigo = new inputField("C&oacute;digo", "codigo");
		$filter->codigo->size=10;
		$filter->codigo->insertValue=$codigo;
		$filter->codigo->rule = "max_length[4]"; 
		$filter->codigo->append($cboton); 

		$filter->button("btnsubmit", "Buscar", form2uri(site_url('ventas/aumentoempleados/index'),array('anio','vd')), $position="BL");
		$filter->build_form();
   	/*
   	$grid = new DataGrid();
		$select=array("vd", "fecha",                                            
		"SUM(totalg*IF(tipo_doc='D', -1, 1)) AS sueldoa","MONTH(fecha) as mes",                              
		"SUM(totalg*(referen IN ('E', 'M'))*IF(tipo_doc='D', -1, 1)) as contado",        
		"SUM(totalg*(referen NOT IN ('E', 'M'))*IF(tipo_doc='D', -1, 1)) as credito", 
    "FORMAT(sum(totals*IF(tipo_doc='D',-1,1)),2) AS subtotal", 
    "FORMAT(sum(iva*IF(tipo_doc='D',-1,1)),2) AS impuesto", 
    "COUNT(*) AS numfac");  
         		
		$grid->db->select($select);  
		$grid->db->from("sfac");
		$grid->db->where('tipo_doc<>','X');
		$grid->db->where('fecha >= ',$fechai);  
		$grid->db->where('fecha <= ',$fechaf);  
		$grid->db->where('vd',$codigo);  
		$grid->db->groupby("mes");
		
		$grid->column("Fecha"          , "fecha"    );
		$grid->column("Sub-Total"      , "subtotal" ,'align=right');
		$grid->column("Impuesto"       , "impuesto" ,'align=right');
		$grid->column("Total"          , "sueldoa",'align=right');
		$grid->column("Contado"        , "contado"  ,'align=right');
		$grid->column("Credito"        , "credito"  ,'align=right');
		$grid->column("N&uacute;mero"  , "numfac"   ,'align=right');
		$grid->build();*/
		
		$grafico = open_flash_chart_object(800,300, site_url("nomina/aumentoempleados/grafico/$anio/$codigo"));
		$data['content']  =$grafico;
		$data['content'] .= $filter->output;
		$data["head"]     = $this->rapyd->get_head();
		$data['title']    = $this->rapyd->get_head()." Nomina ";
		$this->load->view('view_ventanas', $data);
	}
	
	  function grafico($anio='',$codigo=''){
	  $this->load->library('Graph');
		
		if (empty($anio) or empty($codigo)) return;
		
		$fechai=$anio.'0101';
		$fechaf=$anio.'1231';
		
		$mSQL = "SELECT MONTHNAME(fecha)AS mes,codigo,nombre,sueldoa,sueldo FROM ausu
    WHERE fecha>='$fechai' AND fecha<='$fechaf'AND codigo='$codigo'
    ORDER BY fecha";                
    //echo $mSQL;
	  
   	  $maxval=0; $query = $this->db->query($mSQL);
  	  
  	  foreach($query->result() as $row ){ if ($row->sueldoa>$maxval) $maxval=$row->sueldoa;
      $nombre=$row->nombre;
      $meses[]=$row->mes;
      $data_1[]=$row->sueldoa;
			$data_2[]=$row->sueldo;
			//$data_3[]=$row->sueldoa;

		}
		
		$om=1;while($maxval/$om>100) $om=$om*10;
			
			$bar_1 = new bar(75, '#0053A4');
		  $bar_2 = new bar(75, '#9933CC');
		  //$bar_3 = new bar(75, '#639F45');
		  
		  $bar_1->key('Sueldo Anterior',10);
		  $bar_2->key('Sueldo con Aumento',10);
		  //$bar_3->key('Total'  ,10);
		  
		  for($i=0;$i<count($data_1);$i++ ){
		  	
			$bar_1->add_data_tip($data_1[$i]/$om, graph::esc( number_format($data_1[$i],2,',','.')));
			$bar_2->add_data_tip($data_2[$i]/$om, graph::esc( number_format($data_2[$i],2,',','.')));
			//$bar_3->add_data_tip($data_3[$i]/$om, graph::esc( number_format($data_3[$i],2,',','.')));
					
		} 			 
		
  	$g = new graph();  
    $g->title( 'Aumento de '.$nombre.' en el a&ntilde;o '.$anio,'{font-size: 20px; color:##00264A}' );
		$g->set_is_decimal_separator_comma(1);
		
		$g->data_sets[] = $bar_1;
		$g->data_sets[] = $bar_2;
		//$g->data_sets[] = $bar_3;
		
		
    $g->set_x_labels($meses);
		$g->set_x_label_style( 10, '#000000', 3, 1 );
		$g->set_x_axis_steps( 10 );
		$g->set_x_legend('Meses', 16, '#004381' );
		
		$g->bg_colour = '#FFFFFF';
		$g->set_tool_tip( '#key#<br>Mes: #x_label# <br>Monto: #tip#' );
		$g->set_y_max(ceil($maxval/$om));
		$g->y_label_steps(5);
		$g->set_y_legend('Ventas x '.number_format($om,0,'','.').' (Bs)', 16, '#004381' );
		
		echo utf8_encode($g->render());
	}
}
?>