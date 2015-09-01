<?php
class Fisicos extends Controller {
	
	function Fisicos(){
		parent::Controller(); 
		$this->load->helper('text');
		$this->load->library("rapyd");
		//$this->rapyd->load_db();
	}
	
	function index(){
		$this->datasis->modulo_id(317,1);
		redirect("inventario/fisicos/filteredgrid");
	}
 
	function filteredgrid(){
		$this->rapyd->load('datafilter','datagrid2');

		$modbus=array(
			'tabla'   =>'sinv',
			'columnas'=>array(
				'codigo' =>'Código',
				'descrip'=>'Descripción',
				'precio1'=>'Precio 1',
				'precio2'=>'Precio 2',
				'precio3'=>'Precio 3',
				'precio4'=>'Precio 4'),
			'filtro'  =>array('codigo'=>'Código','descrip'=>'Descripción'),
			'retornar'=>array('codigo'=>'codigo'),
			'titulo'  =>'Buscar en inventario');
		
		$boton=$this->datasis->modbus($modbus);
		
		$opciones=array();
		$mSQL='SELECT SQL_BIG_RESULT fecha FROM maesfisico GROUP BY fecha ORDER BY fecha DESC LIMIT 5';
		$query = $this->db->query($mSQL);
		foreach ($query->result() as $row)
			$opciones[$row->fecha]= dbdate_to_human($row->fecha);
		
		$filter = new DataFilter("Kardex de Inventario");
		//$filter->codigo = new inputField("Código De Producto", "codigo");
		//$filter->codigo->append($boton);  

		$filter->ubica = new dropdownField("Almacen", "ubica");  
		$filter->ubica->option("","Todos");
		$filter->ubica->db_name='a.ubica';
		$filter->ubica->options("SELECT ubica,CONCAT(ubica,' ',ubides) descrip FROM caub WHERE gasto='N' ");
		$filter->ubica->operator="=";
		$filter->ubica->clause="where";
		
		$filter->fecha = new dropdownField("Fecha", "fecha");  
		$filter->fecha->db_name='a.fecha';
		//$filter->fecha->options("SELECT SQL_BIG_RESULT fecha,fecha descrip FROM maesfisico GROUP BY fecha ORDER BY fecha DESC LIMIT 5 ");
		$filter->fecha->operator="=";
		$filter->fecha->clause="where";
		
		$filter->buttons("reset","search");
		$filter->build();

		$data['lista'] =  $filter->output;
		$data['forma'] ='';
		
		$data['titulo'] = $this->rapyd->get_head()."<center><h2>Kardex de Inventario</h2></center>";
		$this->layout->buildPage('ventas/view_ventas', $data);

	}
	
}
?>
