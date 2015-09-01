<?php  if (!defined('BASEPATH')) exit('No direct script access allowed');
class sprm_model extends Model {
	
	function sprm_model(){
		parent::Model();
	}

	function insert($proveed,$tipo,$efectos){
		//print_r($efectos);
		$usr     = $this->session->userdata('usuario');
		$num     = $this->datasis->prox_numero('nabo',$usr);
		$num     = str_pad($num, 8, '0', STR_PAD_LEFT);
		$transac = $this->datasis->prox_numero('ntransa',$usr);
		$transac = str_pad($transac, 8, '0', STR_PAD_LEFT);
		
		//itppro
		$monto=0;
		foreach($efectos AS $efecto){
			$mmSQL="SELECT monto FROM sprm WHERE proveed=? AND tipo_doc=? AND fecha=? AND numero=?";
			$dbmonto=$this->datasis->dameval($mmSQL,array($proveed,$efecto['tipo_doc'],$efecto['fecha'],$efecto['numero']));
			
			$itdata['numppro']  =$num;
			$itdata['tipoppro'] =$tipo;
			$itdata['cod_prv']  =$proveed;
			$itdata['tipo_doc'] =$efecto['tipo_doc'];
			$itdata['numero']   =$efecto['numero'];
			$itdata['fecha']    =$efecto['fecha'];
			$itdata['monto']    =$dbmonto;
			$itdata['abono']    =$efecto['monto'];
			$itdata['ppago']    ='';
			$itdata['reten']    ='';
			$itdata['cambio']   ='';
			$itdata['mora']     ='';
			$itdata['transac']  =$transac;
			$itdata['estampa']  =date('Ymd');
			$itdata['hora']     =date('H:i:s');
			$itdata['usuario']  =$usr;
			$itdata['preten']   =0;
			$itdata['creten']   =0;
			$itdata['breten']   =0;
			$itdata['reteiva']  =0;
			
			$monto+=$monto['monto'];
			echo $this->db->insert_string('itppro', $itdata);
			echo "\n";
		}
		
		//En sprm
		$data['cod_prv']  =$proveed;
		$data['nombre']   =$this->datasis->dameval("SELECT nombre FROM sprv WHERE proveed='$proveed'");
		$data['tipo_doc'] =$tipo;
		$data['numero']   =$num;
		$data['fecha']    ='';
		$data['monto']    =$monto;
		$data['impuesto'] ='';
		$data['abonos']   ='';
		$data['vence']    ='';
		$data['tipo_ref'] ='';
		$data['num_ref']  ='';
		$data['observa1'] ='';
		$data['observa2'] ='';
		$data['banco']    ='';
		$data['tipo_op']  ='';
		$data['comprob']  ='';
		$data['numche']   ='';
		$data['codigo']   ='';
		$data['descrip']  ='';
		$data['ppago']    ='';
		$data['nppago']   ='';
		$data['reten']    ='';
		$data['nreten']   ='';
		$data['mora']     ='';
		$data['posdata']  ='';
		$data['benefi']   ='';
		$data['control']  ='';
		$data['transac']  ='';
		$data['estampa']  ='';
		$data['hora']     ='';
		$data['usuario']  =$usr;
		$data['cambio']   ='';
		$data['pmora']    ='';
		$data['reteiva']  ='';
		$data['id']       ='';
		$data['nfiscal']  ='';
		$data['montasa']  ='';
		$data['monredu']  ='';
		$data['monadic']  ='';
		$data['tasa']     ='';
		$data['reducida'] ='';
		$data['sobretasa']='';
		$data['exento']   ='';
		$data['fecdoc']   ='';
		$data['afecta']   ='';
		$data['fecapl']   ='';
		$data['serie']    ='';
		$data['depto']    ='';
		
	}	
}
?>