<?php
class listacontodoslosprecios extends Controller {

	function listacontodoslosprecios() {
		parent::Controller();
	}

	function index() {
		$this->load->library('mtable');
		$this->load->view('table_def.inc');
		
		$obj = new mtable("Listado con todos los Precios",8);
		$obj->AddPage();
		$obj->AddCol('codigo'  ,20,'Código', 'L',6);
		$obj->AddCol('grupo' ,10,'Grupo', 'L',6);
		$obj->AddCol('descrip'   ,50,'Descripción', 'L',6);
		$obj->AddCol('precio1'  ,15,'Detal','R',6);
		$obj->AddCol('precio2',15,'Contado','R',6);
		$obj->AddCol('precio3',15,'Mayor','R',6);
		$obj->AddCol('existen',15,'Existencia','R',6);
		
		$obj->Table("SELECT codigo,grupo,descrip,precio1,precio2,precio3,existen FROM sinv order by codigo" );
		$obj->Output();
	}
}
 ?>
