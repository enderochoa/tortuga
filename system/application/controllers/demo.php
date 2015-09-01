<?php
class Demo extends Controller {  
  
	function Demo() {
	  parent::Controller();
	  $this->load->library("rapyd");
	  $this->load->helper('openflash');
	}  
	
	function index($parametros='nada',$algo='nada'){
		
		$grafico = open_flash_chart_object(800,300, site_url("demo/grafico/Grafico"));
		
		$data['content'] =$grafico;
		$data["head"]    = '';
		$data['title']   =' Demo ';
		$this->load->view('view_ventanas', $data);
		
		/*$data['titulo1']=$parametros;
		$this->layout->buildPage('bienvenido/home', $data);	*/
	}
	
	function grafico($titulo){
		$this->load->library('Graph');
		
		$mSQL_1 = "SELECT cod_cli,nombre,   
			sum(totalg*IF(tipo_doc='D', -1, 1)) AS grantotal, 
			sum(totalg*(referen IN ('E', 'M'))*IF(tipo_doc='D', -1, 1)) AS contado,
			sum(totalg*(referen NOT IN ('E', 'M'))*IF(tipo_doc='D', -1, 1)) AS credito 
			FROM sfac
			WHERE tipo_doc<>'X' AND fecha>='20070101' AND fecha<='20071231' 
			GROUP BY cod_cli ORDER BY grantotal DESC LIMIT 10";
		
		
		// generate some random data
		srand((double)microtime()*1000000);
		
		$bar_1 = new bar_fade(50, '#209B2C' );
		$bar_1->key( 'Contado', 10 );
		
		$bar_2 = new bar_fade( 50, '#9933CC' );
		$bar_2->key( 'Credito', 10 );
		
		$bar_3 = new bar_fade( 50, '#639F45' );
		$bar_3->key( 'Total', 10 );
		
		$maxval=0;
		$label=$tips=array();
		$query = $this->db->query($mSQL_1);
		foreach($query->result() as $row ){
			if ($row->grantotal>$maxval) $maxval=$row->grantotal;
			$bar_1->data[]=$row->contado;
			$bar_2->data[]=$row->credito;
			$bar_3->data[]=$row->grantotal;
			//$bar_1->tips = $data_tips_3;
			$tips[]=$row->nombre;
			$label[]=$row->cod_cli;
		}

		$g = new graph();
		$g->title( $titulo, '{font-size: 26px;}' );
		
		$g->data_sets[] = $bar_1;
		$g->data_sets[] = $bar_2;
		$g->data_sets[] = $bar_3;
		
		
		$g->set_x_labels($label);
		$g->set_x_label_style( 10, '#9933CC', 2, 1 );
		$g->set_x_axis_steps( 10 );
		
		$g->bg_colour = '#FFFFFF';
		$g->set_tool_tip( '#key#<br>Codigo de Clinte: #x_label# <br>Monto: #tip#' );
		$g->tips=$tips;
		$g->set_y_max($maxval);
		$g->y_label_steps( 3 );
		$g->set_y_legend( 'Open Flash Chart', 12, '0x736AFF' );
		echo utf8_encode($g->render());
				
	}
}
?>