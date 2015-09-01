<?php
class presupgenera extends Controller {
	
	function presupgenera(){
		parent::Controller();
		$this->load->library("rapyd");
		$this->datasis->modulo_id(39,1);
	}
	
	function index() {
		redirect("presupuesto/presupgenera/seleccion");
	}
	function seleccion($ban='0'){
		$this->rapyd->load("dataform","datagrid");

		$form = new dataForm('presupuesto/presupgenera/carga');
		if($ban==0){
			$form->msj = new freeField("","tipò","Cargo");	
		}
		
		$form->estruadm = new dropdownField("Estructura Administrativa","estruadm");
		$form->estruadm->options("SELECT codigo,CONCAT_WS(' ',codigo,denominacion) FROM estruadm WHERE LENGTH(codigo)=(SELECT LENGTH(valor) from valores WHERE nombre='FORMATOESTRU') ORDER BY codigo");
		
		
		$form->tipo = new dropdownField("Tipo de presupuesto","tipo");
		$form->tipo->option("G","Gasto");
		$form->tipo->option("T","Gasto e Ingreso");
		$form->tipo->option("I","Ingreso");
		$form->tipo -> style='width:150px;';
		
		$form->fondo = new dropdownField("Origen de Fondo","fondo");
		$form->fondo->options("SELECT fondo, fondo AS val FROM fondo");
		$form->fondo -> style='width:150px;';
		
		$form->submit("btnsubmit","Generar");
		
		$form->build_form();
		
		$grid = new DataGrid("");
		$grid->db->select(array("codigoadm", "tipo"));
		$grid->db->from('presupuesto');
		$grid->db->orderby('codigoadm','desc');
	
		$grid->db->groupby(array("codigoadm"," tipo"));
		
		//$grid->db->query("SELECT codigoadm, tipo FROM presupuesto GROUP BY codigoadm, tipo");
		
		//$grid->order_by("sect_pres","asc");
		
		$grid->column_orderby("Estructura Administrativa" ,"codigoadm","codigoadm","align='left'");
		$grid->column_orderby("Tipo"                      ,"tipo"     ,"tipo"     ,"align='left'");
		
		$grid->build();
		
		//$data['filtro']  = $form->output;
		$data['content'] = $form->output.$grid->output;
		$data['script'] = script("jquery.js")."\n";
		$data['title']   = "Presupuestos Generados";
		//$data['content'] = $form->output.$grid->output;
		//$data['title']   = " Generar Presupuesto ";
		$data["head"]    = $this->rapyd->get_head();
		$this->load->view('view_ventanas', $data);
	}
	
	function carga(){
		$tipo=$this->input->post('tipo');
		$estruadm=$this->input->post('estruadm');
		$fondo=$this->input->post('fondo');
		
		//$query="INSERT IGNORE INTO presupuesto (tipo, codigoadm,codigopres) VALUES (SELECT '$fondo' tipo,'$estruadm' codigoadm ,codigo codigopres FROM ppla MID(codigo,1,1)=4)";
		$query="INSERT IGNORE INTO presupuesto (tipo,codigoadm, codigopres,asignacion)
SELECT '$fondo' tipo, '$estruadm' ca, codigo,'0' asig FROM ppla";
		if($tipo=='t'){			
		}elseif($tipo=='G'){
			$query.=" WHERE MID(codigo,1,1)=4";
		}elseif($tipo=='I'){
			$query.=" WHERE MID(codigo,1,1)=3";
		}
		//echo $query;
		$val=$this->db->simple_query($query);
		redirect("presupuesto/presupgenera/seleccion/$val");
	}
	function cargaauto(){
		$result = $this->db->query("select codigoadm,tipo from presupuesto group by codigoadm,tipo");
		foreach($result->result() as $row){
			echo $row->codigoadm,$row->tipo."</br>";
			$query="INSERT IGNORE INTO presupuesto (tipo,codigoadm, codigopres,asignacion) SELECT '$row->tipo' tipo, '$row->codigoadm' ca, codigo,'0' asig FROM ppla";
			$this->db->simple_query($query);
		}
	}
}
		
?>