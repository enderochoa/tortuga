<?php
class Cierre extends Controller {
	
	function Cierre(){
		parent::Controller();
		$this->load->library("rapyd");
		$this->datasis->modulo_id(606,1);
	}

	function index() {		
		$this->rapyd->load("datagrid","dataform");

		$fecha=$this->uri->segment(4);
		$form = new DataForm();  
		$form->title('Fecha para la ejecuci&oacute;n');
		$form->fecha = new dateonlyField("Fecha de Cierre", "fecha","d/m/Y");
		$form->fecha->size = 10;
		$form->fecha->insertValue = ($fecha ? $fecha : date("Ymd", mktime  (0, 0, 0,12,31,date('Y')-1 )));
		$form->submit("btnsubmit","Cerrar");
		$form->build_form();
		$link=site_url('contabilidad/cierre/ejecutar');

		$data['script']="<script type='text/javascript'>
		function generar(){
			$('#preloader').show();
			$('#contenido').hide();
			$.ajax({
				type: 'POST',
				url: '$link',
				data: $('input,select').serialize(),
				success: function(msg){
					$('#preloader').hide();
					$('#contenido').show('slow');
					alert(msg);
				}
			});
		}

		$(document).ready(function(){
			$('#preloader').hide();
			$('form').submit(function() {
				generar();
				return false;
			});
		});
		</script>";

		$data['extras']="<div id='preloader' style='display: none;	position:absolute; left:40%; top:40%; font-family:Verdana, Arial, Helvetica, sans-serif;'>
			<center>".image("loading4.gif")."<br>".image("loadingBarra.gif")."<br>
			<b>Generando . . . </b>
			</center>
		</div>";
		$data['content'] =$form->output;
		$data["head"]    = script("jquery.pack.js").$this->rapyd->get_head();
		$data['title']   =' Cierre Contable ';
		$this->load->view('view_ventanas', $data);
	}

	function ejecutar(){
		$mfinal  =$this->input->post('fecha');
		if($mfinal==FALSE) redirec('contabilidad/cierre');

		$mfinal  = date("Ymd",timestampFromInputDate($mfinal));
		$ano     = substr($mfinal,2,2);
    $comprob = "ZIERRE$ano";

    $this->db->query("DELETE FROM itcasi WHERE comprob='$comprob'");
    $this->db->query("DELETE FROM casi   WHERE comprob='$comprob'");

    $mSQL = "INSERT INTO casi SET comprob='$comprob', fecha=$mfinal, descrip='ASIENTO DE CIERRE DEL EJERCICIO', total = 0, debe=0, haber=0, estampa=now(),tipo='INDETERMIN',status='A',origen='MANUAL'";
    $this->db->query($mSQL);

    $mSQL = "INSERT INTO itcasi 
	    SELECT $mfinal fecha, 
	    '$comprob' comp, 'MANUAL' origen,
	    cuenta, 'CIERRE ".substr($mfinal,0,2)."' referen, 
	    'CIERRE DE CUENTAS DE RESULTADO EJERCICIO ".substr($mfinal,0,2)."' concepto,
	    sum(haber) debe, sum(debe) haber, 0 ccosto, 0 sucu 
	    FROM itcasi WHERE cuenta>='4' AND fecha<=$mfinal 
	    GROUP BY cuenta ";
    $this->db->query($mSQL);

    $mSQL = "INSERT INTO itcasi 
       SELECT fecha, comprob, origen, 
	    (SELECT resultado FROM cemp limit 1) cuenta, 
	    referen, 
	    concepto, 
	    if(sum(debe-haber)>0,0,sum(haber-debe)) debe, 
	    if(sum(debe-haber)>0,sum(debe-haber),0) haber, 0 ccosto, 0 sucu 
	    FROM itcasi WHERE comprob='$comprob' group by comprob ";
    $this->db->query($mSQL);
    $this->db->query("DELETE FROM itcasi WHERE debe=haber AND comprob='$comprob'");

    echo "Cierre realizado $comprob";
  }
}
?>