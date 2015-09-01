<?php
class anualapert extends Controller {
	var $formatopres;
	var $flongpres;
	var $formatoadm;
	var $flongadm;
	
	function anualapert(){		
		parent::Controller(); 
		$this->load->library("rapyd");
		$this->formatopres=$this->datasis->traevalor('FORMATOPRES');
		$this->flongpres  =strlen(trim($this->formatopres));
		$this->formatoadm=$this->datasis->traevalor('FORMATOESTRU');
		$this->flongadm  =strlen(trim($this->formatoadm));
		
	}
	
	function index() {
		$this->datasis->modulo_id(84,1);
		redirect("presupuesto/anualapert/sel");
	}
	
	function sel(){
		$this->rapyd->load("dataform");
		//echo $this->flongpres;
		$script='
		$(function() {
				$("#codigoadm").change(function(){
					$.post("'.site_url('presupuesto/presusol/get_tipo').'",{ codigoadm:$("#codigoadm").val() },function(data){$("#tipo").html(data);})
				});
		});
		';
		
		$flong=$this->flongpres;
		$rlong=$this->flongadm;
		
		$filter = new DataForm("presupuesto/anualapert/sel/process");
		
		$filter->script($script);

		$filter->codigoadm = new dropdownField("Estructura Administrativa","codigoadm");
		$filter->codigoadm->option("","Seleccione");
		$filter->codigoadm->rule='required';
		$filter->codigoadm->options("SELECT b.codigo, CONCAT_WS(' ',b.codigo,b.denominacion) AS val FROM presusol AS a JOIN estruadm AS b ON a.codigoadm=b.codigo  GROUP BY b.codigo");
		
		$filter->tipo =new dropdownField('Origen de fondos','tipo');
		$filter->tipo->option("","Seleccione una Estructura Administrativa");
		$filter->tipo->rule='required';
		
		//$filter->codigopres = new dropdownField("Presupuesto","codigopres");
		//$filter->codigopres->option("","Seleccione un presupuesto");
		
		$filter->ano = new inputField("A&ntilde;o","ano");
		$filter->ano->size=5;
		$filter->ano->maxlength=4;
		$filter->ano->insertValue=(date('Y'));
		
		$filter->submit("btnsubmit","Aperturar");
		$filter->build_form();
		
		if ($filter->on_success()){
			$tipo  = $filter->tipo->newValue;
			$codigoadm = $filter->codigoadm->newValue;
			//$codigopres = $filter->codigopres->newValue;
			$ano = $filter->ano->newValue;

			redirect("presupuesto/anualapert/apertura/$tipo/$codigoadm/$ano");
		} 

		$data['content'] = $filter->output;
		$data['title']   = "Apertura de Presupuestos";
		$data["head"]    = script("jquery.js").$this->rapyd->get_head();
		$this->load->view('view_ventanas', $data);
	}
	
	function apertura($tipo='',$codigoadm='',$ano=''){
		
		$mSQL="SELECT COUNT(tipo) FROM presupuesto WHERE tipo='$tipo' AND codigoadm='$codigoadm'";
		$result=$this->datasis->dameval($mSQL);
//		$result2=$this->datasis->traevalor("");
		$this->db->simple_query("UPDATE valores SET valor='$ano' WHERE nombre='EJERCICIO'");
		if($result<=0){
			$mSQL="INSERT INTO presupuesto ( codigoadm, tipo, codigopres, asignacion, aumento, disminucion, traslados, comprometido, causado, recibido, opago, pagado, nivel,denominacion )
			SELECT a.codigoadm,a.tipo,a.codigopres,a.asignacion,0 aumento,0 disminucion,0 traslados,0 comprometido,0 causado,0 recibido,0 opago,0 pagado,a.nivel,a.denominacion 
			FROM presusol a
			JOIN estruadm b ON b.codigo=a.codigoadm
			JOIN fondo d ON d.fondo= a.tipo
			WHERE a.tipo='$tipo' AND a.codigoadm='$codigoadm'";
			 
			if($this->db->simple_query($mSQL)){
				$salida="Sa aperturado el presupuesto";
			}else{
				$salida="no paso de presusol a presupuesto";
			}

			if($this->db->simple_query("CALL sp_nivelar()")){
				$salida+=" ";
			}else{
				$salida+=" No ejecuto Nivelar";
			}
			
		}else{
			$salida="Debe hacer cierre primero";
			
		}
		
		$data['content'] = $salida." ".anchor('presupuesto/anualapert','regresar');
		$data['title']   = "Apertura Anual";
		$data["head"]    = script("jquery.js").$this->rapyd->get_head();
		$this->load->view('view_ventanas', $data);
	}
}
	?>