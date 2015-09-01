<?php
class r_recibo extends Controller {
	var $titp='Recibos';
	var $tits='Recibos';
	var $url ='ingresos/r_recibo/';
	function clase(){
		parent::Controller();
		$this->load->library("rapyd");
		//$this->datasis->modulo_id(348,1);
	}
	
	function damemonto($rconcit_id=null,$id=null){
		$XX=array();
		$query="SELECT formula FROM r_conc_it WHERE id=$rconcit_id";
		$row=$this->datasis->damerow($query);
		$formula =$row['formula'];
		$anoe    =$this->db->escape($row['formula']);
		
		if(strpos( $formula,'XX_UTRIBUACTUAL')>=0){
			$ejercicio=$this->datasis->traevalor('EJERCICIO');
			$XX['XX_UTRIBUACTUAL']=$this->datasis->dameval("SELECT valor FROM utribu WHERE ano=$ejercicio");
		}
		
		if(strpos( $formula,'XX_UTRIBUANO')>=0){
			$XX['XX_UTRIBUANO']=$this->datasis->dameval("SELECT valor FROM utribu WHERE ano=$anoe");
		}
		
		
		if(strpos( $formula,'XX_INMUEBLE_')>=0){
			$query="SELECT etapa,techo,mt2,monto FROM r_inmueble WHERE id=$id";
			$row=$this->datasis->damerow($query);
			foreach($ow as $k=>$v)
				$XX["XX_INMUEBLE_".strtoupper($k)]=$v;
		}
					
		if(strpos( $formula,'XX_VEHICULO_')>=0){
			$query="SELECT a.capacidad,a.ejes,a.ano,a.peso,b.monto clase_monto
			FROM vehiculo a
			JOIN clase b ON a.clase=b.codigo
			WHERE a.id=$id";
			$row=$this->datasis->damerow($query);
			foreach($row as $k=>$v)
				$XX["XX_VEHICULO_".strtoupper($k)]=$v;
		}
		
		foreach($XX as $k=>$v){
			$formula=str_replace($k,'$'.$k,$formula);
			$$k=$v;
		}
		
		echo $monto=eval($formula);
	}
}
?>
