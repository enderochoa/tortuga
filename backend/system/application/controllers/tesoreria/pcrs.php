<?php

class Pcrs extends Controller {
	
	var $titp  = 'Pago de Retenciones de Compromiso de Responsabilidad Social';      
	var $tits  = 'Pago de Retencion de Compromiso de Responsabilidad Social';
	var $url   = 'tesoreria/pcrs/';    
	
	function Pcrs(){

		parent::Controller();
		$this->load->library("rapyd");
		$this->datasis->modulo_id('181',1);
	}
	
	function index(){
		redirect($this->url.'nuevo');
	}
	
	function nuevo(){

 		$this->rapyd->load("dataform");
 		
 		$filter = new DataForm($this->url."vista");
		 
		$filter->fechad = new dateonlyField("Desde", "fechad",'d/m/Y');
		$filter->fechad->db_name     = "fechah";
		$filter->fechad->dbformat    = 'ymd';
		$filter->fechad->insertValue = date("Ymd");
		$filter->fechad->append(' mes/año');
		 
		$filter->fechah = new dateonlyField("Hasta", "fechah",'d/m/Y');
		$filter->fechah->db_name     = "fechad";
		$filter->fechah->dbformat    = 'Ymd';
		$filter->fechah->insertValue = date("Ymd");
		$filter->fechah->append(' mes/año');
		
		$filter->codigoadm = new dropdownField("Estrustura Administrativa","codigoadm");
		$filter->codigoadm ->option("","");
		$filter->codigoadm ->options("SELECT a.codigoadm, CONCAT_WS(' ',a.codigoadm,b.denominacion) FROM presupuesto a JOIN estruadm b ON a.codigoadm = b.codigo GROUP BY a.codigoadm");
		$filter->codigoadm ->style = "width:400px";
		//$filter->codigoadm ->
		
		$filter->fondo = new dropdownField("Fondo","fondo");
		$filter->fondo ->option("","");
		$filter->fondo ->options("SELECT a.tipo, CONCAT_WS(a.tipo,b.descrip) FROM presupuesto a JOIN fondo b ON a.tipo = b.fondo GROUP BY a.tipo");
		$filter->fondo ->style = "width:400px";
		
		$mes=date("m");		
		$ano=date("Y");
	
		if ( date('d') > 15 ){
			$filter->fechad->insertValue = date('Y-m-01'); 
			$filter->fechah->insertValue = date('Y-m-15'); 
		}  else {
			$filter->fechad->insertValue = date("Y-m-d", mktime(0, 0, 0, $mes-1, 16, $ano));
			$filter->fechah->insertValue = date("Y-m-d", mktime(0, 0, 0, $mes, 0, $ano));
		}
		
	  $filter->submit("btnsubmit","Pagar CRS");
	  
//	  $edit->button("back", "ir atras", "$this->url index/filteredgrid", $position="BR");
	  
		$filter->build_form();
	
		$data['content'] = $filter->output;
		$data['title']   = "Pago de CRS";
		$data["head"]    = $this->rapyd->get_head();
		$this->load->view('view_ventanas', $data);
	}
	
	function vista() {
		$this->rapyd->load("datagrid2");

		$f1         = $this->input->post('fechad' );
		$f2         = $this->input->post('fechah' );
		$fechad     = $this->db->escape(human_to_dbdate($f1));
		$fechah     = $this->db->escape(human_to_dbdate($f2));
		$codigoadm  = '';
		$fondo      = '';
		$codigoadm2 = $this->db->escape($this->input->post('codigoadm' ));
		$fondo2     = $this->db->escape($this->input->post('fondo'     ));
		
		function blanco($codigo){
			if($codigo>0)$codigo=str_pad($codigo,8,'0',STR_PAD_LEFT);
			return $codigo;
		}
		
		function union($fecha,$codigo){
			return date('Ym',strtotime($fecha)).str_pad($codigo,8,'0',STR_PAD_LEFT);
			
		}
				
		$uri = anchor($this->url.'dataedit/show/<#numero#>','<str_pad><#numero#>|8|0|STR_PAD_LEFT</str_pad>');
		
		$query = "SELECT COUNT(*) FROM (";
		$query.="
		SELECT '',a.numero,a.crs,c.fdesem,factura,controlfac,fechafac,a.fcrs,c.cod_prov FROM odirect a 
		JOIN pades b ON a.numero = b.pago
		JOIN desem c ON b.desem = c.numero
		WHERE c.status='D2' AND crs >0 AND fdesem>=$fechad AND fdesem<= $fechah AND MID(a.status,1,1)<>'F' AND (a.mcrs IS NULL) ";
		if(!empty($codigoadm))$query.=" AND a.estadmin='$codigoadm'";
		if(!empty($fondo))$query.=" AND a.fondo='$fondo'";
		$query .="
		)a ";
		
		$cant  = $this->datasis->dameval($query);
		
		$this->db->query("DROP TABLE IF EXISTS CRS");
		
		$query = "
		CREATE TABLE CRS SELECT * FROM (";
		
		$query.="
		
		SELECT a.numero,a.crs,c.fdesem,factura,controlfac,fechafac,a.fcrs,c.cod_prov,a.estadmin codigoadm,a.fondo FROM odirect a 
		JOIN pades b ON a.numero = b.pago
		JOIN desem c ON b.desem = c.numero
		WHERE c.status='D2' AND crs >0 AND fdesem>=$fechad AND fdesem<= $fechah AND MID(a.status,1,1)<>'F' AND (a.mcrs IS NULL)";
		if(!empty($codigoadm))$query.=" AND a.estadmin='$codigoadm'";		
		if(!empty($fondo))$query.=" AND a.fondo='$fondo'";
		$query.="
		)a JOIN sprv b ON a.cod_prov = b.proveed ";
		
		$this->db->query($query);

		$grid = new DataGrid2("Lista de ".$this->titp);
		$grid->db->from("CRS");		
		$grid->order_by("numero","desc");
		$grid->per_page = 1000;
		$grid->use_function('substr','str_pad','blanco','union');
		$grid->use_function('sta');

		$grid->column("orden de Pago"    ,"<str_pad><#numero#>|8|0|STR_PAD_LEFT</str_pad>");
		$grid->column("O. Compra"        ,"<blanco><#ocompra#>|8|0|STR_PAD_LEFT</blanco>");
		$grid->column("Emision"          ,"<dbdate_to_human><#fcrs#></dbdate_to_human>"  ,"align='center'" );
		$grid->column("Factura"          ,"factura"                                                         );
		$grid->column("Nro. Control"     ,"controlfac"                                                       );
		$grid->column("Fecha Fac"        ,"<dbdate_to_human><#fechafac#></dbdate_to_human>"  ,"align='center'" );
		$grid->column("Proveedor"        ,"nombre"                                                          );
		$grid->column("RIF"              ,"rif"                                                             );		
		$grid->column("Monto"            ,"<number_format><#crs#>|2|,|.</number_format>","align='right'"  );
		$grid->column("Est. Admin"       ,"codigoadm" );
		$grid->column("Fondo"            ,"fondo" );
		$grid->totalizar('crs');
		
		$grid->build();
		
		$campo="
 		<input size='100' type='hidden' name='fechad' value='$f1'>
 		<input size='100' type='hidden' name='fechah' value='$f2'>
 		<input size='100' type='hidden' name='cant' value='$cant'>
 		<input size='100' type='hidden' name='codigoadm' value='$codigoadm'>
 		<input size='100' type='hidden' name='fondo' value='$fondo'>
 		";
		
		$data['content'] = anchor($this->url."nuevo",'Ir atras').$grid->output.form_open($this->url.'guardar').$campo.form_submit('mysubmit', 'Guardar').form_close();
		$data['title']   = " $this->titp ";
		$data["head"]    = $this->rapyd->get_head();
		$this->load->view('view_ventanas', $data);
	}
	
	function guardar(){
		$this->rapyd->load('dataobject');
		
		$fechad    = $this->input->post('fechad' );
		$fechah    = $this->input->post('fechah' );
		
		$fechad2   = $this->db->escape(human_to_dbdate($fechad));
		$fechah2   = $this->db->escape(human_to_dbdate($fechah));
		
		$cant      = $this->input->post('cant'      );
		$codigoadm = $this->input->post('codigoadm' );
		$fondo     = $this->input->post('fondo'     );
		
		$do = new DataObject("mbanc");

		$error='';
				
		$query = "SELECT numero,crs FROM CRS";
				
		$retenciones = $this->db->query($query);
		$cant2       = $retenciones->num_rows();
		
		$pagos=$compras=array();
		$reten = 0;
		foreach($retenciones->result() AS $row){
			$reten += $row->crs;
			if('' >0)
				$compras[]= '';
			else
				$pagos[]= $row->numero;
		}
		
		if($cant!=$cant2)$error.="<div class='alert'>Los montos Previsualizados y los actuales no parecen ser iguales</div>";
		
		if(empty($error)){
			if($reten > 0){
				$do->set("monto"     ,$reten  );
				$do->set("status"    ,"J"     );
				$do->set("tipo"      ,"C"     );
				$do->set("tipo_doc"  ,"ND"    );
				$do->set("observa"   ,".Se cancelo el Impuesto CRS desde $fechad hasta $fechah actividad $codigoadm fondo $fondo");
				$do->set('rel'       ,implode(',',$pagos).'|'.implode(',',$compras));
				
				$do->save();
				$id = $do->get('id');
				
				if(!$id>0)$error.="Ocurrio error al momento de Crear el movimiento bancario";
				
				if(empty($error)){
					
					$query = "UPDATE odirect a 
					JOIN pades b ON a.numero = b.pago
					JOIN desem c ON b.desem = c.numero
					JOIN sprv p ON c.cod_prov = p.proveed
					SET a.mcrs=$id
					WHERE c.status='D2' AND crs >0 AND fdesem>=$fechad2 AND fdesem<= $fechah2 AND MID(a.status,1,1)<>'F' AND (a.mcrs IS NULL)";				
					if(!empty($codigoadm))$query.=" AND a.estadmin='$codigoadm'";
					if(!empty($fondo))$query.=" AND a.fondo='$fondo'";
				
					$bool = $this->db->query($query);
				
					if(!$bool)$error.="Ocurrio error al momento de actualizar la tabla pagos";
				}
			}else{
				$error.="<div class='alert'><p>No hay CRS por pagar desde $fechad hasta $fechah </p></div>";			
			}
		}
		
		if(empty($error)){
			logusu('CRS',"Creo Movimiento bancario de Pago de CRS $id");
			redirect("tesoreria/mbanc/dataedit/modify/".$id);		
		}else{
			logusu('CRS',"Intento crear Movimiento bancario de Pago de CRS  con error $error");
			$data['content'] = $error.anchor($this->url."nuevo",'Regresar');
			$data['title']   = " ".$this->tits." ";
			$data["head"]    = $this->rapyd->get_head();
			$this->load->view('view_ventanas', $data);
		}
	}
	
}
?>