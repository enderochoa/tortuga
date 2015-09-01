<?php
class Pagonom extends Controller {

	function Pagonom(){
		parent::Controller();
		$this->load->library("rapyd");
		$this->titulo='Pagar N&oacute;mina';
	}

	function index(){
		$this->rapyd->load("dataform","datagrid");
		$form = new DataForm('tesoreria/pagonom/index/process');

		$form->contrato = new dropdownField("Contrato", "contrato");
		$form->contrato->option("","Seleccionar");
		$form->contrato->options("SELECT a.contrato,CONCAT_ws(' ',a.contrato,' *',b.nombre)nombre FROM (SELECT contrato FROM prenom GROUP BY contrato )a JOIN noco b ON a.contrato = b.codigo");
		$form->contrato->rule='required';

		$form->submit("btnsubmit","Pagar Nomina");
		$form->build_form();

		if ($form->on_success()){
			$mSQL  = "
			SELECT IF((SELECT numero FROM nomina GROUP BY contrato,fecha ORDER BY numero desc)=0,1,((1*numero) + 1)) numero FROM nomina GROUP BY contrato,fecha ORDER BY numero desc
			";
			
			$contrato2=$form->contrato->newValue;
			$contrato = $this->db->escape($contrato2);
			$mSQL = $this->db->query($mSQL);
			
			$cant = $mSQL->num_rows();
			
			$mSQL  = "
			INSERT INTO nomina SELECT ".($cant==0 ? 1:$this->datasis->dameval("SELECT ((1*numero) + 1) numero FROM nomina GROUP BY contrato,fecha ORDER BY numero desc"))." numero,b.tipo frecuencia ,a.contrato,'' depto,a.codigo,a.nombre,a.concepto,a.tipo,a.descrip,a.grupo,a.formula,a.monto,a.fecha,a.cuota,a.cuotat,a.valor,now('Ymd') estampa,'' usuario,'' transac,'' hora,a.fechap,a.trabaja,'' nomi,'' denomi,'' total 
			FROM prenom a
			JOIN noco b ON a.contrato = b.codigo WHERE a.contrato=$contrato
			";

			$this->db->query($mSQL);
			$nomi    = $this->db->insert_id();
			$nomi    = $this->datasis->dameval("SELECT numero FROM nomina WHERE nomi=$nomi");
			$this->db->query("DELETE FROM prenom WHERE contrato=$contrato");
			
			if($this->datasis->traevalor('USANOMINA')=='S')$this->creapago($contrato2,$nomi);
			
		}
		
		$grid = new DataGrid("Lista de Nominas Pagadas");
		
		$grid->db->select(array("a.numero","a.contrato","b.nombre","a.fecha","SUM(valor) valor"));
		$grid->db->from('nomina a');
		$grid->db->join('noco b' ,'a.contrato = b.codigo' );
		$grid->db->groupby('numero');
		$grid->db->orderby('fecha','desc');
		
		$grid->per_page = 20;
		$grid->column("N&uacute;mero"   ,"numero"                    );
		$grid->column("Contrato"        ,"contrato"                  );
		$grid->column("Contrato"        ,"nombre"                                         ,'align="left"');
		$grid->column("Fecha"           ,"<dbdate_to_human><#fecha#></dbdate_to_human>"   ,'align="center"');
		$grid->column("Monto"           ,"valor"                                          ,'align="right"');

		$grid->build();

		$data['content'] = $form->output.$grid->output;
		$data['title']   = ' '.$this->titulo.' ';
		$data["head"]    = $this->rapyd->get_head();
		$this->load->view('view_ventanas', $data);
	}
	
	function pago(){
		//$this->rapyd->load("dataform","datagrid");
		
		$c = $this->datasis->dameval("SELECT COUNT(*) FROM prenom");
		
		if($c > 0){
			$mSQL  = "
			SELECT IF((SELECT numero FROM nomina GROUP BY contrato,fecha ORDER BY numero desc LIMIT 1)=0,1,((1*numero) + 1) ) numero FROM nomina GROUP BY contrato,fecha ORDER BY numero desc LIMIT 1
			";
			
			$cant = $this->datasis->dameval($mSQL);
			
			//echo $cant = $mSQL->num_rows();
			
			$mSQL  = "
			INSERT INTO nomina SELECT ".($cant<=0 ? 1:$this->datasis->dameval("SELECT ((1*numero) + 1) numero FROM nomina GROUP BY contrato,fecha ORDER BY numero desc LIMIT 1"))." numero,b.tipo frecuencia ,a.contrato,'' depto,a.codigo,a.nombre,a.concepto,a.tipo,a.descrip,a.grupo,a.formula,a.monto,a.fecha,a.cuota,a.cuotat,a.valor,now('Ymd') estampa,'' usuario,'' transac,'' hora,a.fechap,a.trabaja,'' nomi,'' denomi,'' total 
			FROM prenom a
			JOIN noco b ON a.contrato = b.codigo
			";
	
			$this->db->query($mSQL);
			$nomi    = $this->db->insert_id();
			
			$this->db->simple_query("TRUNCATE prenom");
			
			$nomi    = $this->datasis->dameval("SELECT numero FROM nomina WHERE nomi=$nomi");
			//$this->db->query("DELETE FROM prenom WHERE contrato=$contrato");
			
			if($this->datasis->traevalor('USANOMINA')=='S')$salida = $this->creapago($nomi);
		}else{
			$salida="<div class='alert'>No hay prenomina generada</div>";
		}
		
		$data['content'] = $salida;
		$data['title']   = ' '.$this->titulo.' ';
		$data["head"]    = $this->rapyd->get_head();
		$this->load->view('view_ventanas', $data);
	}
	
	function creapago($nomi){
		$query="INSERT INTO odirect 
		(`numero`,`nomina`,`fecha`,`tipo`,`estadmin`,`fondo`,`subtotal`,`exento`,`total`, `total2`,`observa`,`status` ,`retenomina`)  
		(SELECT '' numero,a.numero nomina,now('Ymd') fecha,'N' tipo,b.codigoadm estadmin,b.fondo,SUM((a.tipo='A') *a.valor)subtotal,SUM((a.tipo='A') *a.valor) exento,((SUM((a.tipo='A') *a.valor))-(-1*SUM((a.tipo='D') *a.valor)))total, SUM((a.tipo='A') *a.valor)total2,b.nombre observa,'B' status ,SUM(-1*(a.tipo='D') *a.valor)retenomina FROM nomina a JOIN noco b ON a.contrato = b.codigo GROUP BY numero)";
		
		if($this->db->query($query)){
		
			$numero  = $this->db->insert_id();
				
		
			$salida  = "<p>Se creo la Orden de Pago numero:".str_pad($numero,8,'0',STR_PAD_LEFT)."</p>"; 
			$query="INSERT INTO itodirect
			(numero,partida,ordinal,descripcion,unidad,cantidad,precio,importe)
			(SELECT $numero,d.codigopres,d.ordinal,d.descrip,'MONTO' unidad,1 cantidad,SUM(a.valor) precio,SUM(a.valor) importe FROM nomina a JOIN conc d ON a.concepto = d.concepto WHERE a.tipo = 'A' GROUP BY d.codigopres)";
			
			if($this->db->query($query)){
				$query="INSERT INTO retenomi 
				(`nomina`,`cod_prov`,`monto`)
				(SELECT $nomi,d.cod_prov,SUM(-1*a.valor) monto FROM nomina a JOIN conc d ON a.concepto = d.concepto WHERE a.tipo = 'D' GROUP BY d.codigopres)";
				
				if($this->db->query($query))
					$salida.= "<p>Se crearon las deducciones de n&oacute;mina para la n&oacute;mina $nomi</p>";
			}
			return $salida;
		}else{
			return $salida  = "<p>No se pudo crear la orden de pago</p>";
		}
	}
}