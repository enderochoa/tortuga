<?php
class tiketimp extends Controller {

	function tiketimp(){
		parent::Controller();
		$this->load->library("rapyd");
		//$this->datasis->modulo_id('50C',1);
	}
		
	function imprimir(){
	
		$this->rapyd->load("dataform");
		
		$scli=array(
		'tabla'   =>'scli',
		'columnas'=>array(
		'cliente' =>'Código Cliente',
		'nombre'  =>'Nombre',
		'contacto'=>'Contacto'),
		'filtro'  =>array('cliente'=>'Código Cliente','nombre'=>'Nombre'),
		'retornar'=>array('cliente'=>'cliente'),
		'titulo'  =>'Buscar Cliente');
		
		$cboton=$this->datasis->modbus($scli);
		
		$filter = new DataForm('/tiketimp/index');
		$filter->title('Filtro de Impresion de Tiket');
		
		$filter->fechad = new dateonlyField("Desde", "fechad",'Ymd');
		$filter->fechah = new dateonlyField("Hasta", "fechah",'Ymd');
		$filter->fechad->clause  =$filter->fechah->clause="where";
		$filter->fechad->db_name =$filter->fechah->db_name="fecha";
		//$filter->fechad->insertValue = $fechad;
		//$filter->fechah->insertValue = $fechah;
		$filter->fechah->size=$filter->fechad->size=10;
		$filter->fechad->operator=">=";
		$filter->fechah->operator="<=";
		
		$filter->cliente = new inputField("Cliente","cliente");
		$filter->cliente->size=10;
		//$filter->cliente->insertValue=$cliente;
		$filter->cliente->maxlength=10;
		$filter->cliente->append($cboton); 
		                                                                     		
		$filter->button("btnsubmit", "Buscar", form2uri(site_url("/formatos/ver/TIKET/"),array('cliente','fechad','fechah')), $position="BL");//
		$filter->build_form();
		
	
		$data['content']=$filter->output;
		$data['title']   = " Imprimir ";
		$data["head"]    = script("jquery.pack.js").script("plugins/jquery.numeric.pack.js").script("plugins/jquery.floatnumber.js").$this->rapyd->get_head();
		$this->load->view('view_ventanas', $data);  
	}
  
} 
?>
  