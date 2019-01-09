<?php
class gprestamos extends Controller {  
  
	function gprestamos() {
		parent::Controller();
		$this->load->library("rapyd");
		$this->load->helper('openflash');
	}  
	
	function index() {
	redirect ('nomina/gprestamos/anuales');
	}	
	
	function anuales(){
		$this->rapyd->load("datagrid2");
		$this->rapyd->load("dataform");
		$this->load->helper('openflash');
		                            
		if($this->uri->segment(4))$anio=$this->uri->segment(4);	elseif(isset($_POST['anio']))$anio=$_POST['anio'];
		if (empty($anio))$anio=date("Y");
		
		$fechai=$anio.'0101';
		$fechaf=$anio.'1231';
		
		$filter = new DataForm('nomina/gprestamos/anuales');
		$filter->title('Filtro de Prestamos Anuales');
				
		$filter->anio = new inputField("A&ntilde;o", "anio");
		$filter->anio->size=4;
		$filter->anio->insertValue=$anio;
		$filter->anio->maxlength=4; 
		
		$filter->button("btnsubmit", "Buscar", form2uri(site_url('nomina/gprestamos/anuales'),array('anio')), $position="BL");
		$filter->build_form();
		
		$grid = new DataGrid2();
		$select=array("fecha","DATE_FORMAT(fecha,'%m/%Y')AS mes",
    "SUM(monto)AS grantotal",
  	"COUNT(*) AS numfac"); 
		         		
		$grid->db->select($select);  
		$grid->db->from("pres ");
		$grid->db->where('fecha >= ', $fechai);  
		$grid->db->where('fecha <= ',$fechaf); 
		$grid->db->groupby("MONTH(fecha)");
				
		$grid->column("Mes"     ,"mes","align='left'");
		$grid->column("Total"     , "<number_format><#grantotal#>|2|,|.</number_format>",'align=right');
		$grid->column("Cant. Prest", "numfac"   ,'align=right');
		
		$grid->totalizar('grantotal');
		$grid->build();
		
		$grafico = open_flash_chart_object(700,450, site_url("nomina/gprestamos/ganuales/$anio/"));
		$data['content']  = $grafico;
		$data['content'] .= $filter->output.$grid->output;
		$data["head"]     = $this->rapyd->get_head();
		$data['title']    = $this->rapyd->get_head()." Prestamos Anuales ";
		$this->load->view('view_ventanas', $data);
	}
	
	function mensuales($anio='',$mes=''){
		$this->rapyd->load("datagrid2");
		$this->rapyd->load("dataform");
		$this->load->helper('openflash');
		
		if($this->uri->segment(4)) $mes =$this->uri->segment(4); elseif(isset($_POST['mes'] )) $mes =$_POST['mes'] ;
		if($this->uri->segment(5)) $anio=$this->uri->segment(5); elseif(isset($_POST['anio'])) $anio=$_POST['anio'];
		
		if (empty($mes)) $mes =date("m");
		if (empty($anio))$anio=date("Y");

		$fechai=$anio.str_pad($mes, 2, "0", STR_PAD_LEFT).'01';
		$fechaf=$anio.str_pad($mes, 2, "0", STR_PAD_LEFT).'31';
		
		$filter = new DataForm('nomina/gprestamos/mensuales');
		$filter->attributes=array('onsubmit'=>"this.action='index/'+this.form.mes.value+'/'+this.form.anio.value+'/';return FALSE;");
		$filter->title('Filtro de Prestamos mensuales');
		
		$filter->mes = new dropdownField("Mes/A&ntilde;o", "mes");  
		for($i=1;$i<13;$i++) 
		$filter->mes->option(str_pad($i, 2, '0', STR_PAD_LEFT),str_pad($i, 2, '0', STR_PAD_LEFT));  
		$filter->mes->size=2;
		$filter->mes->style='';
		$filter->mes->insertValue=$mes;	
		
		$filter->anio = new inputField("A&ntilde;o", "anio");
		$filter->anio->in='mes';
		$filter->anio->size=4;
		$filter->anio->insertValue=$anio;
		$filter->anio->maxlength=4; 
		
		$filter->button("btnsubmit", "Buscar", form2uri(site_url('nomina/gprestamos/mensuales/'),array('mes','anio')), $position="BL");
		$filter->build_form();
		
		$grid = new DataGrid2();
		$select=array("fecha","DAYOFMONTH(fecha)AS dia",
    "SUM(monto)AS grantotal",
  	"COUNT(*) AS numfac"); 
		         		
		$grid->db->select($select);  
		$grid->db->from("pres ");
		$grid->db->where('fecha >= ', $fechai);  
		$grid->db->where('fecha <= ',$fechaf);   
		$grid->db->groupby("fecha");
				
		$grid->column("Dia"      ,"dia","align='left'");
		$grid->column("Total"      , "<number_format><#grantotal#>|2|,|.</number_format>",'align=right');
		$grid->column("Cant. Prest", "numfac"   ,'align=right');
		
		$grid->totalizar('grantotal');
		$grid->build();
		
		$grafico = open_flash_chart_object(680,350, site_url("nomina/gprestamos/gmensuales/$mes/$anio"));
		$data['content']  =$grafico;
		$data['content'] .=  $filter->output.$grid->output;
		$data["head"]     = $this->rapyd->get_head();
		$data['title']    = $this->rapyd->get_head()." Prestamos Mensuales ";
		$this->load->view('view_ventanas', $data);
	}
	function ganuales($anio=''){
		$this->load->library('Graph');
		$this->lang->load('calendar');
		if (empty($anio)) return;
		
		$fechai=$anio.'0101';
		$fechaf=$anio.'1231';
		
		$mSQL = "SELECT MONTHNAME(fecha)AS mes,
    SUM(monto)AS grantotal
    FROM  pres 
    WHERE fecha>='$fechai' AND fecha<='$fechaf'
    GROUP BY MONTH(fecha) ORDER BY fecha,grantotal DESC LIMIT 10";    
		echo $mSQL;
				
		$maxval=0;$query = $this->db->query($mSQL);
		
		foreach($query->result() as $row ){ if ($row->grantotal>$maxval) $maxval=$row->grantotal;
			$nmes[]=$this->lang->line('cal_'.strtolower($row->mes));
    	$data_1[]=$row->grantotal;
		
		}
	
		$om=1;while($maxval/$om>100) $om=$om*10;
		
		$bar_1 = new bar(75, '#329B98');	
		$bar_1->key('Total',10);

		for($i=0;$i<count($data_1);$i++ ){
			$bar_1->add_data_tip($data_1[$i]/$om, graph::esc( number_format($data_1[$i],2,',','.')));
			$mes=$i+1;
		  $bar_1->links[]= site_url("/nomina/gprestamos/mensuales/$mes/$anio");
	 	                                                                                  
		} 			 
		
		$g = new graph();
		$g->set_is_decimal_separator_comma(1);
		if($maxval>0){
		$g->title('Prestamos en el a&ntilde;o '.$anio,'{font-size: 16px; color:#0F3054}' );
		$g->data_sets[] = $bar_1;

		$g->set_x_labels($nmes);
		$g->set_x_label_style( 9, '#000000', 3, 1 );
		$g->set_x_axis_steps( 8 );
		$g->set_x_legend('Meses', 16, '#004381' );  
		
		$g->bg_colour = '#FFFFFF';
		$g->set_tool_tip( '#key#<br>Mes: #x_label# <br>Monto: #tip#' );
		$g->set_y_max(ceil($maxval/$om));
		$g->y_label_steps(5);
		$g->set_y_legend('Prestamos x '.number_format($om,0,'','.').' (Bs)', 16, '#004381' );
		}else
		$g->title( 'No existen prestamos en el a&ntilde;o seleccionado','{font-size:18px; color: #d01f3c}');
	  $g->bg_colour='#FFFFFF';                                                                 
		echo utf8_encode($g->render());
	}
	function gmensuales($mes='',$anio=''){
		$this->load->library('Graph');
		$this->lang->load('calendar');
		
		if (empty($anio) or empty($mes)) return;
		
		$fechai=$anio.str_pad($mes, 2, "0", STR_PAD_LEFT).'01';
		$fechaf=$anio.str_pad($mes, 2, "0", STR_PAD_LEFT).'31';
		
    $mSQL = "SELECT DAYOFMONTH(fecha)AS dia,
    SUM(monto)AS grantotal
    FROM  pres 
    WHERE fecha>='$fechai' AND fecha<='$fechaf'
    GROUP BY fecha";  
		//echo $mSQL;
		
		$maxval=0; $query = $this->db->query($mSQL);
		  
		foreach($query->result() as $row ){ if ($row->grantotal>$maxval) $maxval=$row->grantotal;
		  $fecha[]=$row->dia;
			$data_1[]=$row->grantotal;
		
		}
		
		$om=1;while($maxval/$om>100) $om=$om*10;
			
			$bar_1 = new bar(75, '#329B98'); 
		  $bar_1->key('Total',10);

		  for($i=0;$i<count($data_1);$i++ ){
		 	
			$bar_1->add_data_tip($data_1[$i]/$om, graph::esc( number_format($data_1[$i],2,',','.')));
			
		} 			 
		
		$g = new graph();  
		$g->set_is_decimal_separator_comma(1);
		if($maxval>0){
		$g->title( 'Prestamos de el mes '.$mes.'/'.$anio,'{font-size: 16px; color:#0F3054}' );
		$g->data_sets[] = $bar_1;
				
		$g->set_x_labels($fecha);
		$g->set_x_label_style( 10, '#000000', 3, 1 );
		$g->set_x_axis_steps( 10 );
		$g->set_x_legend('Dias', 16, '#004381' );
		
		$g->bg_colour = '#FFFFFF';
		$g->set_tool_tip( '#key#<br>Dia: #x_label# <br>Monto: #tip#' );
		$g->set_y_max(ceil($maxval/$om));
		$g->y_label_steps(5);
		$g->set_y_legend('Prestamos x '.number_format($om,0,'','.').' (Bs)', 16, '#004381' );
		}else                                                                                              
		$g->title( 'No existen prestamos con los datos seleccionados','{font-size:18px; color: #d01f3c}');
		$g->bg_colour='#FFFFFF';                                                                 
		echo utf8_encode($g->render());
	}
	
}
?>