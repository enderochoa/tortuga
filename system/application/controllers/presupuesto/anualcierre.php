<?php
class anualcierre extends Controller {
	var $formatopres;
	var $flongpres;
	var $formatoadm;
	var $flongadm;
	
	function anualcierre(){		
		parent::Controller(); 
		$this->load->library("rapyd");
		$this->formatopres=$this->datasis->traevalor('FORMATOPRES');
		$this->flongpres  =strlen(trim($this->formatopres));
		$this->formatoadm=$this->datasis->traevalor('FORMATOESTRU');
		$this->flongadm  =strlen(trim($this->formatoadm));
		
	}
	
	function index() {
		$this->datasis->modulo_id(67,1);
		redirect("presupuesto/anualcierre/sel");
	}
	
	function sel(){
		$this->rapyd->load("dataform");
		//echo $this->flongpres;
		$script='
		$(function() {
				$("#codigoadm").change(function(){
					$.post("'.site_url('presupuesto/presupuesto/get_tipo').'",{ codigoadm:$("#codigoadm").val() },function(data){$("#tipo").html(data);})
				});

				
				
		});
		';
		
		$flong=$this->flongpres;
		$rlong=$this->flongadm;
		
		$filter = new DataForm("presupuesto/anualcierre/sel/process");
		
		$filter->script($script);

		$filter->codigoadm = new dropdownField("Estructura Administrativa","codigoadm");
		$filter->codigoadm->option("","Seleccione");
		$filter->codigoadm->rule='required';
		$filter->codigoadm->options("SELECT b.codigo, CONCAT_WS(' ',b.codigo,b.denominacion) AS val FROM presupuesto AS a JOIN estruadm AS b ON a.codigoadm=b.codigo  GROUP BY b.codigo");
		
		$filter->tipo =new dropdownField('Origen de fondos','tipo');
		$filter->tipo->option("","Seleccione una Estructura Administrativa");
		$filter->tipo->rule='required';
		
		//$filter->codigopres = new dropdownField("Presupuesto","codigopres");
		//$filter->codigopres->option("","Seleccione un presupuesto");
		
		//$filter->ano = new inputField("A&ntilde;o","ano");
		//$filter->ano->size=5;
		//$filter->ano->maxlength=4;
		
		$filter->submit("btnsubmit","Buscar");		
		$filter->build_form();
		
		if ($filter->on_success()){
			$tipo  = $filter->tipo->newValue;
			$codigoadm = $filter->codigoadm->newValue;
			//$codigopres = $filter->codigopres->newValue;
			//$ano = $filter->ano->newValue;

			redirect("presupuesto/anualcierre/cerrar/$tipo/$codigoadm/$codigopres");
		} 

		$data['content'] = $filter->output;
		$data['title']   = "Cierre de Presupuestos";
		$data["head"]    = script("jquery.js").$this->rapyd->get_head();
		$this->load->view('view_ventanas', $data);
	}
	
	function cerrar($tipo='',$codigoadm=''){
		
		$ano=$this->datasis->traevalor("EJERCICIO");
		
		$mSQL="INSERT INTO prescerrado SELECT a.tipo,a.codigoadm,a.codigopres,a.asignacion,a.aumento,a.disminucion,a.comprometido,a.causado,a.recibido,a.opago,a.pagado,a.nivel,a.check_pres,'$ano' ano
		FROM presupuesto a
		JOIN estruadm b ON b.codigo=a.codigoadm
		JOIN ppla c ON c.codigo=a.codigopres
		JOIN fondo d ON d.fondo= a.tipo
		 WHERE a.tipo='$tipo' AND a.codigoadm='$codigoadm'";
		
		if($this->db->simple_query($mSQL)){
			$mSQL="DELETE FROM presupuesto WHERE codigoadm='$codigoadm' AND tipo='$tipo'";
			if($this->db->simple_query($mSQL)){
				$salida="Se ha cerrado correctamente el presupuesto";
			}else{
				$salida="No elimino";
			}	
		}else{
			$salida="En este momento no se puede completar la operacion";
		}
		
		$data['content'] = $salida." ".anchor('presupuesto/anualcierre','regresar');
		$data['title']   = "Cierre Anual";
		$data["head"]    = script("jquery.js").$this->rapyd->get_head();
		$this->load->view('view_ventanas', $data);
	}
}
	?>