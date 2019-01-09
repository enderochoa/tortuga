<?php
class gproductos extends Controller {  
	
	function gproductos() {
		parent::Controller();
		$this->load->library("rapyd");
		$this->load->helper('openflash');
	}  
	
	function index(){
		redirect('/inventario/gproductos/filtro');
	}
	
	function filtro(){
		$this->rapyd->load("datagrid2");	  
		$this->rapyd->load("dataform");
		$this->load->helper('openflash');
		
		$sinv=array(
		'tabla'   =>'sinv',
		'columnas'=>array(
		'codigo' =>'Código',
		'descrip'=>'descrip'),
		'filtro'  =>array('codigo' =>'Código','descrip'=>'descrip'),
		'retornar'=>array('codigo'=>'codigo'),
		'titulo'  =>'Buscar Articulo');
  	
		$iboton=$this->datasis->modbus($sinv);
		if($this->uri->segment(4)) $codigo=$this->uri->segment(4); elseif(isset($_POST['codigo']))$codigo=$_POST['codigo'];
		if (empty($codigo))$codigo=$this->datasis->dameval("SELECT codigo FROM sinv");
		if($this->uri->segment(5)) $mes =$this->uri->segment(5); elseif(isset($_POST['mes'] )) $mes =$_POST['mes'] ; else $mes =date('m');
		if($this->uri->segment(6)) $anio=$this->uri->segment(6); elseif(isset($_POST['anio'])) $anio=$_POST['anio']; else $anio=date('Y');

		$script ='
			$(function() {
				$(".inputnum").numeric(".");
			});
			';

		$fechad=$anio.str_pad($mes, 2, "0", STR_PAD_LEFT).'01';
		$fechah=$anio.str_pad($mes, 2, "0", STR_PAD_LEFT).'31';
		
		$filter = new DataForm('ventas/mensuales');
		$filter->attributes=array('onsubmit'=>"this.action='index/'+this.form.mes.value+'/'+this.form.anio.value+'/';return FALSE;");
		$filter->title('Filtro de Ventas por Existencias');
		$filter->script($script, "create");
		$filter->script($script, "modify");
		
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
		$filter->anio->rule = "trim";
		$filter->anio->css_class='inputnum';
		
		$filter->codigo = new inputField("Código", "codigo");
		$filter->codigo->size=15;
		$filter->codigo->append($iboton);
		$filter->codigo->insertValue=$codigo;
		
		$filter->button("btnsubmit", "Buscar", form2uri(site_url('inventario/gproductos/filtro/'),array('codigo','mes','anio')), $position="BL");
		$filter->build_form();
		
		$grid = new DataGrid2();
    $select=array("fecha","codigo","sum(cantidad) cantidad","IF(sum(cantidad)<=salcant,sum(cantidad),salcant)*(salcant>0)as salcant");  
		     		
		$grid->db->select($select);  
		$grid->db->from("costos");
		$grid->db->where("codigo='$codigo' AND fecha>='$fechad' AND fecha<='$fechah' AND  origen='3I'");  
		$grid->db->groupby("fecha");
		$grid->db->orderby("fecha");
		$grid->db->having("cantidad>0");
		
		$grid->column("Fecha", "<dbdate_to_human><#fecha#></dbdate_to_human>",'align=center');         
		$grid->column("Ventas", "cantidad",'align=center');
		$grid->column("Existencias" , "salcant",'align=center');
		
		//$grid->totalizar('grantotal');
		$grid->build();
  	//echo $grid->db->last_query();
  
		$grafico = open_flash_chart_object(680,450, site_url("inventario/gproductos/grafico/$codigo/$mes/$anio/"));
		$data['content']  = $grafico;
		$data['content'] .= $filter->output.$grid->output;
		$data["head"]     = $this->rapyd->get_head();
		$data['title']    = $this->rapyd->get_head()." Ventas por Existencias ";
		$this->load->view('view_ventanas', $data);
	}
	function grafico($codigo='',$mes='',$anio=''){
		$this->load->library('Graph');
		if (empty($mes) and empty($anio)) return; 

		$fechad=$anio.str_pad($mes, 2, "0", STR_PAD_LEFT).'01';
		$fechah=$anio.str_pad($mes, 2, "0", STR_PAD_LEFT).'31';

		$mSQL = "SELECT DATE_FORMAT(fecha, '%d')as fecha, codigo, sum(cantidad) cantidad, IF(sum(cantidad)<=salcant,sum(cantidad),salcant)*(salcant>0)as salcant 
		FROM costos 
		WHERE codigo='$codigo' AND fecha>='$fechad' AND fecha<='$fechah' AND origen='3I'
		GROUP BY fecha HAVING cantidad>0";
   	echo $mSQL;
		$maxval=0;
		$query=$this->db->query($mSQL);
		
		foreach($query->result() as $row ){
		if ($row->cantidad>$maxval) $maxval=$row->cantidad;
		 $fecha[]=$row->fecha;
		 $codigo=$row->codigo;
		 $data_1[]=$row->cantidad;
		 $data_2[]=$row->salcant;

		}
		$fechadd=dbdate_to_human($fechad);
		$fechahh=dbdate_to_human($fechah);
		
		$nombre=$this->datasis->dameval("SELECT descrip FROM sinv WHERE codigo='$codigo'");				
		$om=1;while($maxval/$om>100) $om=$om*10;
		
		$bar_1 = new bar_glass( 55, '#D54C78', '#C31812' );
		$bar_1->key('Ventas', 10 ); 
		                        
		$bar_2 = new line_dot( 3, 5, '#0066CC', 'Downloads', 10);
		$bar_2->key('Existencias',10);
		
    
		for($i=0;$i<count($data_1);$i++ ){
			$bar_1->add_data_tip($data_1[$i]/$om, graph::esc( number_format($data_1[$i],2,',','.')));
			$bar_2->add_data_tip($data_2[$i]/$om, graph::esc( number_format($data_2[$i],2,',','.')));  
		//  $bar_1->links[]= site_url("/inventario/gproductos/mensuales/$anio/".str_replace('/',':slach:',$proveed[$i]));
		//  $bar_1->links[]= site_url("/inventario/gproductos/mensuales/$anio/".raencode($proveed[$i]));
		}
		
		$g = new graph();
		$g->set_is_decimal_separator_comma(1);
		if($maxval>0){
		$g->title('Grafico de '.$nombre.' Desde '.$fechadd.' Hasta '.$fechahh,'{font-size: 16px; color:#0F3054}' );
		$g->data_sets[] = $bar_1;
		$g->data_sets[] = $bar_2;		
		
		$g->set_x_labels($fecha);
		$g->set_x_label_style( 10, '#000000', 2, 1 );
		$g->set_x_axis_steps( 10 );
		$g->set_x_legend('Fecha', 14, '#004381' );        
		
		$g->bg_colour = '#FFFFFF';
		$g->set_tool_tip( '#key#<br>Fecha: #x_label# <br>Cantidad: #tip#' );
		$g->set_y_max(ceil($maxval/$om));
		$g->y_label_steps(5);
//$g->set_y_legend('Ventas x '.number_format($om,0,'','.').' (Bs)', 16, '#004381' );
		}else
		$g->title( 'No existen datos con la informacion seleccionada','{font-size:18px; color: #d01f3c}');
		$g->bg_colour='#FFFFFF';
		 echo utf8_encode($g->render());
	}
}
?>