<?php 
class promediosueldos extends Controller {  
  
	function promediosueldos() {
	  parent::Controller();
	  $this->load->library("rapyd");
	  $this->load->helper('openflash');
	}  
	  function index(){
	  //$this->rapyd->load("datagrid");  
	  $this->rapyd->load("dataform");
		$this->load->helper('openflash');
		                            
		if($this->uri->segment(4))$anio=$this->uri->segment(4); elseif(isset($_POST['anio'])) $anio=$_POST['anio'];
		if (empty($anio))$anio=date("Y");
		$fechai=$anio.'0101';
		$fechaf=$anio.'1231';
		
		$filter = new DataForm('nomina/promediosueldos');
		$filter->title('Filtro de Promedio de Sueldos');
				
		$filter->anio = new inputField("A&ntilde;o", "anio");
		$filter->anio->size=4;
		$filter->anio->insertValue=$anio;
		$filter->anio->rule = "max_length[4]"; 
		
		$filter->button("btnsubmit", "Buscar", form2uri(site_url('nomina/promediosueldos /index'),array('anio')), $position="BL");
		$filter->build_form();
		/*
		$grid = new DataGrid();
		$select=array( "fecha","MONTH(fecha)as mes",                                            
		"SUM(montonet*IF(tipo_doc='D', -1, 1)) AS anterior",                             
		"SUM(credito*IF(tipo_doc='D', -1, 1)) as contado",        
		"SUM(inicial*IF(tipo_doc='D', -1, 1)) as credito", 
    "FORMAT(sum(montotot*IF(tipo_doc='D',-1,1)),2) AS subtotal", 
    "FORMAT(sum(montoiva*IF(tipo_doc='D',-1,1)),2) AS impuesto", 
    "COUNT(*) AS numfac"); 
             		
		$grid->db->select($select);  
		$grid->db->from("scst");
		$grid->db->where('a.tipo<>','X');
		$grid->db->where('fecha >= ', $fechai);  
		$grid->db->where('fecha <= ',$fechaf); 
		$grid->db->groupby("mes");
		
		$grid->column("Fecha"          , "fecha"    );
		$grid->column("Sub-Total"      , "subtotal" ,'align=right');
		$grid->column("Impuesto"       , "impuesto" ,'align=right');
		$grid->column("Total"          , "anterior",'align=right');
		$grid->column("Contado"        , "contado"  ,'align=right');
		$grid->column("Credito"        , "credito"  ,'align=right');
		$grid->column("N&uacute;mero"  , "numfac"   ,'align=right');
		$grid->build();*/
   	
		$grafico = open_flash_chart_object(760,300, site_url("nomina/promediosueldos/grafico/$anio/"));
		$data['content']  = $grafico;
		$data['content'] .= $filter->output;
		$data["head"]     = $this->rapyd->get_head();
		$data['title']    = $this->rapyd->get_head()." Nomina ";
		$this->load->view('view_ventanas', $data);
	}
	
	function grafico($anio=''){
		$this->load->library('Graph');
		$this->lang->load('calendar');
		if (empty($anio)) return;
		
		$fechai=$anio.'0101';
		$fechaf=$anio.'1231';
		
		$mSQL = "SELECT MONTHNAME(fecha)AS mes,
    AVG (sueldoa) as anterior,
    AVG (sueldo) as actual
    FROM ausu
    WHERE fecha>='$fechai' AND fecha<='$fechaf'
    GROUP BY mes ORDER BY fecha";                  

    echo $mSQL;
    		
		$maxval=0;
		$query = $this->db->query($mSQL);
		foreach($query->result() as $row ){
			if ($row->anterior>$maxval) $maxval=$row->anterior;
			$meses[]=$row->mes;
			$data_1[]=$row->anterior;
			$data_2[]=$row->actual;
			$data_3[]=$row->actual;
		}
		
		$om=1;while($maxval/$om>100) $om=$om*10;
		//$meses=$this->lang->line('cal_january');
		$bar_1 = new bar(75, '#0053A4');
		$bar_2 = new bar(75, '#9933CC');
		$bar_3 = new bar(75, '#639F45');
		
		$bar_1->key('Sueldo Anterior',10);
		$bar_2->key('Sueldo Actual',10);
		$bar_3->key('Total'  ,10);
		
		for($i=0;$i<count($data_1);$i++ ){
			$bar_1->add_data_tip($data_1[$i]/$om, graph::esc( number_format($data_1[$i],2,',','.')));
			$bar_2->add_data_tip($data_2[$i]/$om, graph::esc( number_format($data_2[$i],2,',','.')));
			$bar_3->add_data_tip($data_3[$i]/$om, graph::esc( number_format($data_3[$i],2,',','.')));
      
      $mes=$i+1;
      $bar_1->links[]= site_url("/compras/mensuales/index/$mes/$anio");
			$bar_2->links[]= site_url("/compras/mensuales/index/$mes/$anio");
			$bar_3->links[]= site_url("/compras/mensuales/index/$mes/$anio");			
							 	                                                                                  
		} 			 
	
		$g = new graph();
		$g->title( 'Promedio de sueldos en el a&ntilde;o '.$anio,'{font-size: 22px; color:##00264A}' );
		$g->set_is_decimal_separator_comma(1);
		
		$g->data_sets[] = $bar_1;
		$g->data_sets[] = $bar_2;
		//$g->data_sets[] = $bar_3;

		$g->set_x_labels($meses);
		$g->set_x_label_style( 9, '#000000', 3, 1 );
		$g->set_x_axis_steps( 8 );
		$g->set_x_legend('Meses ', 16, '#004381' );  
		
		$g->bg_colour = '#FFFFFF';
		$g->set_tool_tip( '#key#<br>Mes: #x_label# <br>Monto: #tip#' );
		$g->set_y_max(ceil($maxval/$om));
		$g->y_label_steps(5);
		$g->set_y_legend('Sueldo x '.number_format($om,0,'','.').' (Bs)', 16, '#004381' );
		
		echo utf8_encode($g->render());
	}
	}
?>