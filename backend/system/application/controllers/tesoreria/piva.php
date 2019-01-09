<?

class Piva extends Controller {
	
	var $titp  = 'Pago de Retenciones de IVA';
	var $tits  = 'Pago de Retencion de IVA';
	var $url   = 'tesoreria/piva/';    
	
	function Piva(){

		parent::Controller();
		$this->load->library("rapyd");
		$this->datasis->modulo_id('118',1);
	}
		
	function index(){
		redirect($this->url.'filteredgrid');
	}
	
	function filteredgrid(){

		$this->rapyd->load("datafilter","datagrid");
		$this->rapyd->uri->keep_persistence();

		$mSPRV=array(
				'tabla'   =>'sprv',
				'columnas'=>array(
				'proveed' =>'C&oacute;odigo',
				'nombre'=>'Nombre',
		    'rif'=>'Rif',
				'contacto'=>'Contacto'),
				'filtro'  =>array('proveed'=>'C&oacute;digo','nombre'=>'Nombre','rif'=>'Rif'),
				'retornar'=>array('proveed'=>'cod_prov' ),
				'titulo'  =>'Buscar Beneficiario');
		

		$bSPRV=$this->datasis->p_modbus($mSPRV,"proveed");

		$filter = new DataFilter("");

		$filter->db->select("a.cheque cheque,a.id id,a.fecha fecha,a.tipo tipo,a.status status,a.cod_prov cod_prov,a.benefi benefi,a.monto monto,b.nombre proveed");
		$filter->db->from("mbanc a");
		$filter->db->join("sprv b","b.proveed=a.cod_prov", "LEFT");
		$filter->db->where("a.status != ", "E1");
		$filter->db->where("a.status != ", "E2");
		$filter->db->where("a.status != ", "E3");
		$filter->db->where("a.status != ", "J1");
		$filter->db->where("a.status != ", "J2");
		$filter->db->where("a.status != ", "J3");

		$filter->id = new inputField("N&uacute;mero", "id");
		$filter->id->db_name="a.id";
		$filter->id->size  =10;
		
		$filter->cheque = new inputField("Cheque", "cheque");
		$filter->cheque->db_name="a.cheque";
		$filter->cheque->size  =10;

		$filter->fecha = new dateonlyField("Fecha", "fecha");
		$filter->fecha->dbformat = "Y-m-d";
		$filter->fecha->size=12;

		$filter->cod_prov = new inputField("Beneficiario", 'cod_prov');
		$filter->cod_prov->size = 6;
		$filter->cod_prov->append($bSPRV);

		$filter->status = new dropdownField("Estado","status");
		$filter->status->option("","");
		$filter->status->option("I1","Sin Ejecutar");
		$filter->status->option("I2","Ejecutado");
		$filter->status->option("A" ,"Anulado");
		$filter->status->style="width:150px";

		$filter->buttons("reset","search");

		$filter->build();
		$uri = anchor('tesoreria/mbanc/dataedit/show/<#id#>','<str_pad><#id#>|8|0|STR_PAD_LEFT</str_pad>');
		

		function sta($status){
			switch($status){
				case "I1":return "Sin Ejecutar";break;
				case "I2":return "Ejecutado";break;
				case "A":return "Anulado";break;
			}
		}

		$grid = new DataGrid("");
		$grid->order_by("id","desc");
		$grid->per_page = 20;
		$grid->use_function('substr','str_pad');
		$grid->use_function('sta');

		$grid->column_orderby("N&uacute;mero"    ,$uri                                            ,"numero" );
		$grid->column_orderby("Cheque"           ,"cheque"                                        ,"cheque" );
		$grid->column_orderby("Fecha"            ,"<dbdate_to_human><#fecha#></dbdate_to_human>"  ,"fecha"  ,"align='center'"      );
		$grid->column_orderby("Beneficiario"     ,"proveed"                                       ,"proveed","align='left'  NOWRAP");
		$grid->column_orderby("Pago"             ,"<number_format><#monto#>|2|,|.</number_format>","monto"  ,"align='right'"       );
		$grid->column_orderby("Estado"           ,"<sta><#status#></sta>"                         ,"status" ,"align='center'NOWRAP");
		//$grid->column(""                 ,$uri2                                         ,"align='center'");

		//echo $grid->db->last_query();
		$grid->add($this->url."nuevo/");
		$grid->build();

		//$data['content'] = $filter->output.$grid->output;
		$data['filtro']  = $filter->output;
		$data['content'] = $grid->output;
		$data['script'] = script("jquery.js")."\n";
		$data['title']   = "$this->titp";
		$data["head"]    = $this->rapyd->get_head();
		$this->load->view('view_ventanas', $data);
	}
	
	
	function dataedit(){

 		$this->rapyd->load("dataedit");
		
 		$edit = new DataEdit($this->tits,"odirect");
		
		$edit->back_url = $this->url."index/filteredgrid";
		
		$edit->post_process('insert','_post_insert');
		$edit->post_process('update','_post_update');
		$edit->post_process('delete','_post_delete');
		
		$edit->numero =  new inputField("Orden de Pago", 'numero');
	  $edit->numero->when =array('show');
				
		$edit->total =  new inputField("Monto", 'total');
	  $edit->total->when =array('show');
		
	  $edit->observa =  new inputField("Observaciones", 'observa');
	  $edit->observa->when =array('show');
		 
		$edit->buttons("undo", "back");
		
		$edit->build();
	
		$data['content'] = $edit->output;
		$data['title']   = "$this->tits";
		$data["head"]    = script("jquery.js").$this->rapyd->get_head();
		$this->load->view('view_ventanas', $data);
	}
	
	function nuevo(){

 		$this->rapyd->load("dataform");

 		$filter = new DataForm($this->url."vista");
		 
		$filter->fechad = new dateonlyField("Desde", "fechad",'d/m/Y');
		$filter->fechad->db_name     = "fechah";
		$filter->fechad->dbformat    = 'ymd';
		$filter->fechad->insertValue = date("Ymd");
		$filter->fechad->append(' mes/a&ntilde;o');
		 
		$filter->fechah = new dateonlyField("Hasta", "fechah",'d/m/Y');
		$filter->fechah->db_name     = "fechad";
		$filter->fechah->dbformat    = 'Ymd';
		$filter->fechah->insertValue = date("Ymd");
		$filter->fechah->append(' mes/a&ntilde;o');
		
		//$filter->codigoadm = new dropdownField("Estrustura Administrativa","codigoadm");
		//$filter->codigoadm ->option("","");
		//$filter->codigoadm ->options("SELECT a.codigoadm, CONCAT_WS(' ',a.codigoadm,b.denominacion) FROM presupuesto a JOIN estruadm b ON a.codigoadm = b.codigo GROUP BY a.codigoadm");
		//$filter->codigoadm ->style = "width:400px";
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
		
	  $filter->submit("btnsubmit","Pagar IVA");
	  
//	  $edit->button("back", "ir atras", "$this->url index/filteredgrid", $position="BR");
	  
		$filter->build_form();
	
		$data['content'] = $filter->output.anchor($this->url."index/filteredgrid",'Ir atras');
		$data['title']   = "$this->tits";
		$data["head"]    = script("jquery.js").$this->rapyd->get_head();
		$this->load->view('view_ventanas', $data);
	}
	
	function vista() {
		$this->rapyd->load("datagrid2");

		$f1 = $this->input->post('fechad' );
		$f2 = $this->input->post('fechah' );
		
		$codigoadm = $this->input->post('codigoadm' );
		$fondo     = $this->input->post('fondo'     );
		
		
		$d  = explode('/',$this->input->post('fechad' ));
		$h  = explode('/',$this->input->post('fechah' ));
		
		$fechad  = implode('',array($d[2],$d[1],$d[0]));
		$fechah  = implode('',array($h[2],$h[1],$h[0]));
		
		function blanco($codigo){
			if($codigo>0)return $codigo=str_pad($codigo,8,'0',STR_PAD_LEFT);
			
		}
		
		function union($fecha,$codigo){
			return date('Ym',strtotime($fecha)).str_pad($codigo,8,'0',STR_PAD_LEFT);
		}
				
		$uri = anchor($this->url.'dataedit/show/<#numero#>','<str_pad><#numero#>|8|0|STR_PAD_LEFT</str_pad>');
		
		$query = "SELECT COUNT(*) FROM riva WHERE status='B' AND tipo_doc<>'AN' AND emision >= $fechad AND emision<=$fechah";
		
		if(!empty($codigoadm))$query.=" AND codigoadm='$codigoadm'";
		
		if(!empty($fondo))$query.=" AND fondo='$fondo'";
		
		$cant = $this->datasis->dameval($query);

		$grid = new DataGrid2("Lista de ".$this->titp);
		$grid->db->from("riva");
		$grid->db->where("status","B");
		$grid->db->where("tipo_doc <>","AN");
		$grid->db->where("emision >=",$fechad);
		$grid->db->where("emision <=",$fechah);
		if(!empty($codigoadm))
		$grid->db->where("codigoadm",$codigoadm);
		if(!empty($fondo))
		$grid->db->where("fondo",$fondo);
		$grid->order_by("nrocomp","desc");
		$grid->per_page = 1000;
		$grid->use_function('substr','str_pad','blanco','union');
		$grid->use_function('sta');

		$grid->column("N&uacute;mero"    ,"<union><#emision#>|<#nrocomp#></union>");
		$grid->column("O. Compra"        ,"<blanco><#ocompra#>|8|0|STR_PAD_LEFT</blanco>");
		$grid->column("O. Pago"          ,"<blanco><#odirect#>|8|0|STR_PAD_LEFT</blanco>");
		$grid->column("Multiple"         ,"<blanco><#itfac#>|8|0|STR_PAD_LEFT</blanco>"  );
		$grid->column("Emision"          ,"<dbdate_to_human><#emision#></dbdate_to_human>"  ,"align='center'" );
		$grid->column("Tipo"             ,"tipo_doc"                                                        );
		$grid->column("Factura"          ,"numero"                                                          );
		$grid->column("Nro. Control"     ,"nfiscal"                                                         );
		$grid->column("Fecha Fac"        ,"<dbdate_to_human><#ffactura#></dbdate_to_human>"  ,"align='center'" );
		$grid->column("RIF"              ,"rif"                                                             );
		$grid->column("Monto"            ,"<number_format><#reiva#>|2|,|.</number_format>","align='right'"  );
		$grid->column("Est. Admin"       ,"codigoadm" );
		$grid->column("Fondo"            ,"fondo" );
		$grid->totalizar('reiva');
		
		$grid->build();
		
		
			$campo="
 		<input size='100' type='hidden' name='fechad' value='$fechad'>
 		<input size='100' type='hidden' name='fechah' value='$fechah'>
 		<input size='100' type='hidden' name='cant' value='$cant'>
 		<input size='100' type='hidden' name='codigoadm' value='$codigoadm'>
 		<input size='100' type='hidden' name='fondo' value='$fondo'>
 		";
		
		//$salida = anchor("tesoreria/piva/guardar/$fechad/$fechah/$cant","Crear orden de Pago");
		$data['content'] = anchor($this->url."nuevo",'Ir atras').$grid->output.form_open($this->url.'guardar').$campo.form_submit('mysubmit', 'Guardar').form_close();
		$data['title']   = " $this->titp ";
		$data["head"]    = $this->rapyd->get_head();
		$this->load->view('view_ventanas', $data);
	}
	
	function _post_insert($do){
		$numero = $do->get('numero');
		logusu('ocompra',"Creo Orden de Compra Nro $numero");
		//redirect($this->url."actualizar/$numero");
	}
	
	function _post_update($do){
		$numero = $do->get('numero');
		logusu('ocompra'," Modifico Orden de Compra Nro $numero");
		//redirect($this->url."actualizar/$numero");
	}
	function _post_delete($do){
		$numero = $do->get('numero');
		logusu('ocompra'," Elimino Orden de Compra Nro $numero");
	}
	
	function guardar(){
		$this->rapyd->load('dataobject');
		
		$fechad = $this->input->post('fechad' );
		$fechah = $this->input->post('fechah' );
		$cant   = $this->input->post('cant'   );
		
		$codigoadm = $this->input->post('codigoadm' );
		$fondo     = $this->input->post('fondo'     );
				
		$do = new DataObject("mbanc");

		$error='';
		
		$query = "SELECT nrocomp FROM riva WHERE status='B' AND tipo_doc<>'AN' AND emision >= $fechad AND emision<=$fechah";
		
		if(!empty($codigoadm))$query.=" AND codigoadm='$codigoadm'";
		
		if(!empty($fondo))$query.=" AND fondo='$fondo'";
		
		$rivas = $this->db->query($query);
		$cant2 = $rivas->num_rows();
		
		$numeros=array();
		foreach($rivas->result() AS $row){
			$numeros[]= $row->nrocomp;
		}
		
		if($cant!=$cant2)$error.="<div class='alert'>Los montos Previsualizados y los actuales no parecen ser iguales</div>";

		$query = "SELECT SUM(reiva) FROM riva WHERE status='B' AND tipo_doc<>'AN' AND emision >= $fechad AND emision<=$fechah";
		
		if(!empty($codigoadm))$query.=" AND codigoadm='$codigoadm'";
		
		if(!empty($fondo))$query.=" AND fondo='$fondo'";
					
		$reteiva    = $this->datasis->dameval($query);
		$partidaiva = $this->datasis->traevalor('PARTIDAIVA');	
		if(empty($error)){
			if($reteiva > 0){
				$do->set("monto"     ,$reteiva);
				$do->set("status"    ,"J"     );
				$do->set("tipo"      ,"I"     );
				$do->set("tipo_doc"  ,"ND"    );
				$do->set("observa"   ,". Se cancelo el IVA desde ".date('d/m/Y',$fechad)." hasta ".date('d/m/Y',$fechah)." actividad ".$codigoadm." fondo ".$fondo);
				$do->set('rel'       ,implode(',',$numeros));
				//$do->save();
				$do->save();
				$id = $do->get('id');
				if(!$id>0)$error.="Ocurrio error al momento de Crear el Movimiento";
				if(empty($error)){
					$query = "UPDATE riva SET status = 'C',pagado=$id WHERE emision >= $fechad AND emision <= $fechah  AND status = 'B' AND tipo_doc<>'AN'";
					if(!empty($codigoadm))$query.=" AND codigoadm='$codigoadm'";
		
					if(!empty($fondo))$query.=" AND fondo='$fondo'";
					$bool = $this->db->simple_query($query);
				}
				
				if(!$bool)$error.="Ocurrio error al momento de actualizar la tabla RIVA";
					
			}else{
				$f1 = $this->input->post('fechad' );
				$f2 = $this->input->post('fechah' );
				
				$error.="<div class='alert'><p>No hay IVA por pagar desde $f1 hasta $f2 </p></div>";			
			}
		}

		if(empty($error)){
			logusu('MBANC',"Creo Movimiento de Pago de IVA $id");
			redirect("tesoreria/mbanc/dataedit/show/".$id);
		}else{
			logusu('MBANC',"Creo Movimiento de Pago de IVA  con error $error");
			$data['content'] = $error.anchor($this->url."nuevo",'Regresar');
			$data['title']   = " ".$this->tits." ";
			$data["head"]    = $this->rapyd->get_head();
			$this->load->view('view_ventanas', $data);
		}
	}
	
}
?>