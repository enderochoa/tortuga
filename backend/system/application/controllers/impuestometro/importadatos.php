<?php
class Importadatos extends Controller {
	
	
	function Importadatos(){
		parent::Controller();
		//$this->load->library("rapyd");
		//$this->datasis->modulo_id(216,1);
		
	}
	
	function traedia(){
		$DB1 = $this->load->database('impu', TRUE);
		$query="
		Select distinct  convert(varchar, c.fec_cob, 112) fec_cob,ltrim(rtrim(sl.subl_des)) subl_des, SUM(c.monto) monto
		from	cobros c inner join reng_cob rc on
						rc.cob_num = c.cob_num
					inner join factura f on
						f.fact_num = rc.doc_num
						and rc.tp_doc_cob = 'FACT'
					inner join reng_fac rf on
						rf.fact_num = f.fact_num
					inner join art a on
						a.co_art = rf.co_art
					inner join sub_lin sl on
						sl.co_subl = a.co_subl
		where	monto != 0 and c.anulado  = 0 and f.anulada = 0
				and c.fec_cob = (Select distinct  max(fec_cob)	from	cobros )
		GROUP BY c.fec_cob,sl.subl_des
		Order by 1,2
		";
	
		$msql   = $DB1->query($query);
		
		foreach($msql->result() as $row){
			$query="INSERT INTO imp_ingresos (fecha,concepto,monto) VALUES (".$row->fec_cob.",'".$row->subl_des."',".$row->monto.")
			ON DUPLICATE KEY UPDATE 
			fecha=".$row->fec_cob.",
			concepto='".$row->subl_des."',
			monto=".$row->monto."
			";
			$this->db->query($query);
		}
	}
	
	function cargatodo(){
		$DB1 = $this->load->database('impu', TRUE);
		$query="
		Select distinct  convert(varchar, c.fec_cob, 112) fec_cob,ltrim(rtrim(sl.subl_des)) subl_des, SUM(c.monto) monto
		from	cobros c inner join reng_cob rc on
						rc.cob_num = c.cob_num
					inner join factura f on
						f.fact_num = rc.doc_num
						and rc.tp_doc_cob = 'FACT'
					inner join reng_fac rf on
						rf.fact_num = f.fact_num
					inner join art a on
						a.co_art = rf.co_art
					inner join sub_lin sl on
						sl.co_subl = a.co_subl
		where	monto != 0 and c.anulado  = 0 and f.anulada = 0
		GROUP BY c.fec_cob,sl.subl_des
		Order by 1,2
		";
		//and c.fec_cob between '20140530' and '20140530'	
		
		$msql   = $DB1->query($query);
		foreach($msql->result() as $row){
			
			$query="INSERT INTO imp_ingresos (fecha,concepto,monto) VALUES (".$row->fec_cob.",'".$row->subl_des."',".$row->monto.")
			ON DUPLICATE KEY UPDATE 
			fecha=".$row->fec_cob.",
			concepto='".$row->subl_des."',
			monto=".$row->monto."
			";
			$this->db->query($query);
		}
		echo "paso";
	}
}
?>
