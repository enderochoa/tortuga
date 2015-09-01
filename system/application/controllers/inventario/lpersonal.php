<?php
class Lpersonal extends Controller {

	function Lpersonal() {
		parent::Controller();
	}

	function index() {
		$this->load->library('mtable');
		$this->load->view('table_def.inc');
		
		$obj = new mtable("Listado de Bancos",6);
		$obj->AddPage();
		$obj->AddCol('codbanc' ,20,'Cod.', 'C',4);
		$obj->AddCol('numcuent',20,'Numero de cuenta', 'C',4);
		$obj->AddCol('tbanco'  ,20,'C.Banco', 'C',4);
		$obj->AddCol('dire1'   ,20,'Direccion 1', 'C',4);
		$obj->AddCol('dire2'   ,20,'Direccion 2', 'C',4);
		$obj->AddCol('banco'   ,40,'Banco','L',4);
		$obj->AddCol('saldo'   ,40,'Saldo','R',4);
		$obj->totalizar=array('saldo');
		$obj->Table("SELECT codbanc,numcuent,tbanco,dire1,dire2, banco, saldo FROM banc");
		$obj->Output();
	}
}

?>
