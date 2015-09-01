<?php
class Trabajo extends Controller {
	
	function Trabajo(){
		parent::Controller(); 
		$this->load->library("rapyd");
		$this->load->library("menues");
		//$this->datasis->modulo_id(907,1);
	}
	function index(){ 
		redirect("supervisor/trabajo/filteredgrid");
	}
	function filteredgrid(){

		$this->rapyd->load("dataform","datagrid");

 		$modbus=array(
			'tabla'   =>'usuario',
			'columnas'=>array(
				'us_codigo' =>'C&oacute;digo',
				'us_nombre'=>'Nombre'),
			'filtro'  =>array('us_nombre'=>'nombre'),
			'retornar'=>array('us_codigo'=>'usuario'),
			'titulo'  =>'Buscar Usuario');
		
		$filter = new DataForm('/supervisor/trabajo/filteredgrid/search/osp');
		
		$filter->mes = new dateonlyField("Mes", "mes",'m/Y');
		$filter->mes->clause  ="where";
		$filter->mes->operator="=";
		$filter->mes->size=8;
		$filter->mes->dbformat='Ym';
		$filter->mes->insertValue = date("Y-m-d");
		
		$filter->usuario = new inputField("C&oacute;digo de usuario", "usuario");
		$filter->usuario->size=11;
		$filter->usuario->append($this->datasis->modbus($modbus));
		$filter->submit('manda','Buscar');
		$filter->build_form();

		/*
		$mSQL="(SELECT fecha, count(*) AS cant FROM bitacora WHERE usuario='DAVID' GROUP BY fecha ) UNION
		(SELECT a.fecha, 0 AS cant FROM dim_tiempo AS a LEFT JOIN  bitacora as b ON a.fecha=b.fecha AND b.usuario='DAVID'
		WHERE a.fecha BETWEEN (SELECT MIN(fecha) FROM bitacora) AND (SELECT MAX(fecha) FROM bitacora) AND b.fecha IS NULL) ORDER BY fecha";
		*/
		
		$content='';
		if($this->input->post('manda')){
			
			$usr=($this->input->post('usuario')) ? ' AND b.usuario="'.$this->input->post('usuario').'"' : '';
			
			$fechad='(SELECT MIN(fecha) FROM bitacora)';
			$fechah='(SELECT MAX(fecha) FROM bitacora)';
			if ($this->input->post('mes')){
				$acum=explode('/',$filter->mes->value);
				$fechad=$acum[1].$acum[0].'01';
				$fechah=$acum[1].$acum[0].'31';
			}
			
			$mSQL="SELECT a.fecha, semana FROM dim_tiempo AS a LEFT JOIN  bitacora as b ON a.fecha=b.fecha $usr WHERE a.fecha BETWEEN $fechad AND $fechah AND b.fecha IS NULL";
			//echo $mSQL;
			
			$sema=array('Lunes','Martes','Miercoles','Jueves','Viernes','Sabado','Domingo');
			$query = $this->db->query($mSQL);
			$data=array();
			foreach ($query->result() as $row){
				$pasa=array();
				$pasa['fecha'] = $row->fecha;
				if ($row->semana>5)
					$pasa['cant']  = $sema[$row->semana-1];
				else
					$pasa['cant']  = '<b>'.$sema[$row->semana-1].'</b>';
				$data[]=$pasa;
			}
			$query->free_result();
			
			//grid
			$grid = new DataGrid("Lista de Bitacora",$data);
			$grid->per_page = 31;
			$grid->column("Fecha","<dbdate_to_human><#fecha#></dbdate_to_human>");
			$grid->column("Semana","<#cant#>");
			$grid->build();
			
			$content = $grid->output;
		}

		$data['content'] = $filter->output.$content;
		$data["head"]    = $this->rapyd->get_head();
		$data['title']   =' Dias NO trabajados ';
		$this->load->view('view_ventanas', $data);
	}
}
?>