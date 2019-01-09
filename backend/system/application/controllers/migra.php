<?php
class Migra extends Controller {
	
	function Migra(){
		parent::Controller();
		
	}
	function index(){
		
	

		$tablas=array('banc                 ',
		'bcta                 ',
		'carg                 ',
		'caub                 ',
		'civa                 ',
		'clase                ',
		'claseo               ',
		'clasevehi            ',
		'conc                 ',
		'contribu             ',
		'contribuyente        ',
		'cpla                 ',
		'depa                 ',
		'dept                 ',
		'divi                 ',
		'dpto                 ',
		'estruadm             ',
		'fondo                ',
		'formatos             ',
		'grpr                 ',
		'grup                 ',
		'inmueble             ',
		'intramenu            ',
		'intrarepo            ',
		'intrasida            ',
		'line                 ',
		'local                ',
		'marc                 ',
		'marca                ',
		'marcavehi            ',
		'pers                 ',
		'ppla                 ',
		'prof                 ',
		'reportes             ',
		'rete                 ',
		'sinv                 ',
		'sprv                 ',
		'sumi                 ',
		'tban                 ',
		'tingresos            ',
		'tipo                 ',
		'tipoe                ',
		'tipoin               ',
		'tipot                ',
		'tpersonas            ',
		'uadministra          ',
		'uejecutora           ',
		'usuario              ',
		'valores              ',
		'vehiculo             ');

		foreach($tablas as $tabla){
		$campos=$this->datasis->consularray("describe $tabla");	
		
		$campos=array_flip($campos);
		
		echo $query="
		INSERT INTO tortuga2013.$tabla (".implode($campos,',').") </br>
		SELECT ".implode($campos,',')." FROM tortuga2012.$tabla ;
		</br>
		";
		}
	}
}

?>
