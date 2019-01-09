<?php
class listadepesos extends Controller {

	function listadepesos() {
		parent::Controller();
	}
	function index() {
		$this->load->library('mtable');
		$this->load->view('table_def.inc');
		
		$obj = new mtable("Listado de Pesos",8);
		$obj->AddPage();
		$obj->AddCol('codigo'  ,20,'Código', 'L',6);
		$obj->AddCol('grupo' ,10,'Grupo', 'L',6);
		$obj->AddCol('descrip'   ,50,'Descripción', 'L',6);
		$obj->AddCol('peso'  ,15,'Peso','R',6);
		$obj->AddCol('fracci',15,'Embalaje','R',6);
		$obj->AddCol('existen',15,'Existencia','R',6);
				
		$obj->Table("SELECT codigo,grupo,descrip,peso,fracci,existen FROM sinv order by codigo" );
		$obj->Output();
	}
}
?>
