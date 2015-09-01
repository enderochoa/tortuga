<?php
class listadepreciosgeneral extends Controller {

	function listadepreciosgeneral() {
		parent::Controller();
	}

	function index() {
		$this->load->library('mtable');
		$this->load->view('table_def.inc');
		
		$obj = new mtable("Listado de Precios General",8);
		$obj->AddPage();
		$obj->AddCol('codigo'  ,20,'Código', 'L',6);
		$obj->AddCol('grupo' ,10,'Grupo', 'L',6);
		$obj->AddCol('descrip'   ,50,'Descripción', 'L',6);
		$obj->AddCol('unidad'  ,15,'Unidad','R',6);
		$obj->AddCol('precio1',15,'Precio','R',6);
		$obj->AddCol('pond',15,'Costo','R',6);
		$obj->AddCol('existen',15,'Existencia','R',6);
		$obj->AddCol('ventatotal',15,'Precio Inv','R',6);
	  $obj->AddCol('costo',15,'Costo Inv','R',6);
		$obj->Table("SELECT codigo,grupo,descrip,unidad,precio1,pond, existen*pond costo, existen*base1 ventatotal ,existen FROM sinv order by codigo" );
		$obj->Output();
	}
}
?>
