<?php
class lnmov extends Controller {
 
	function Lpersonal() {
		parent::Controller();
	}
	function index() {
		$this->load->library('mtable');
		$this->load->view('table_def.inc');
		
		$obj = new mtable("Listado de Productos estancados",8);
		$obj->AddPage();
		$obj->AddCol('codigo'  ,30,'Codigo', 'C',8);
		$obj->AddCol('descrip' ,100,'Descripcion', 'L',6);
		$obj->AddCol('prov1'   ,20,'Beneficiario', 'C',8);
		$obj->AddCol('fechav'  ,20,'Ultima Venta','C',8);
		$obj->Table("SELECT codigo,concat(descrip,descrip2) descrip,prov1,fechav FROM sinv WHERE fechav<=DATE_ADD(CURDATE(), INTERVAL -180 DAY )order by fechav" );
		$obj->Output();
	}
}
?>
