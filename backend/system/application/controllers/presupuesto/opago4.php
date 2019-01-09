<?php
class opago extends Controller {

	var $titp='Ordenes de Pago';
	var $tits='Orden de pago';
	var $url='presupuesto/opago/';

	function opago(){
		parent::Controller();
		$this->load->library("rapyd");
		//$this->datasis->modulo_id(302,1);
	}
	function index(){
		redirect($this->url."/filteredgrid");
	}

	function filteredgrid(){
		//$this->datasis->modulo_id(103,1);
		$this->rapyd->load("datafilter","datagrid");

		$mSPRV=array(
				'tabla'   =>'sprv',
				'columnas'=>array(
				'proveed' =>'C&oacute;odigo',
				'nombre'=>'Nombre',
				'contacto'=>'Contacto'),
				'filtro'  =>array('proveed'=>'C&oacute;digo','nombre'=>'Nombre'),
				'retornar'=>array('proveed'=>'cod_prov'),
				'titulo'  =>'Buscar Beneficiario');

		$bSPRV=$this->datasis->p_modbus($mSPRV,"proveed");

		$filter = new DataFilter("Filtro de ".$this->titp);
		
		$filter->db->select("a.numero numero,a.fecha fecha,a.tipo tipo,a.status status,a.cod_prov cod_prov,a.beneficiario beneficiario,b.nombre uejecuta2,c.nombre proveed");
		$filter->db->from("ocompra a");                  
		$filter->db->join("uejecutora b" ,"a.uejecutora=b.codigo");
		$filter->db->join("sprv c"       ,"c.proveed=a.cod_prov");
		$filter->db->where("a.status !=", "P");
		$filter->db->where("a.status !=", "C");

		$filter->tipo = new dropdownField("Orden de ", "tipo");
		$filter->tipo->db_name = 'a.tipo';
		$filter->tipo->option("","");
		$filter->tipo->option("Compra"  ,"Compra");
		$filter->tipo->option("Servicio","Servicio");
		$filter->tipo->style="width:100px;";
		
		$filter->fecha = new dateonlyField("Fecha", "fecha");
		$filter->fecha->size=12;
		
		$filter->uejecutora = new dropdownField("U.Ejecutora", "uejecutora");
		$filter->uejecutora->option("","Seccionar");
		$filter->uejecutora->options("SELECT codigo,nombre FROM uejecutora ORDER BY nombre");
		$filter->uejecutora->onchange = "get_uadmin();";
		$filter->uejecutora->rule = "required";
		
		$filter->cod_prov = new inputField("Beneficiario", 'cod_prov');
		$filter->cod_prov->size = 6;
		$filter->cod_prov->append($bSPRV);
		$filter->cod_prov->rule = "required";
		
		$filter->beneficiario = new inputField("Beneficiario", "beneficiario");
		$filter->beneficiario->size=60;
		
		$filter->status = new dropdownField("Estado","status");
		$filter->status->option("","");
		$filter->status->option("T","Causado");
		$filter->status->option("O","Ordenado Pago");
		$filter->status->style="width:150px";
		
		$filter->buttons("reset","search");
		$filter->build();
		$uri = anchor($this->url.'dataedit/modify/<#numero#>','<str_pad><#numero#>|8|0|STR_PAD_LEFT</str_pad>');

		function sta($status){
			switch($status){
				case "T":return "Causado";break;
				case "C":return "Comprometido";break;
				case "O":return "Ordenado Pago";break;
				//case "A":return "Anulado";break;
			}
		}

		$grid = new DataGrid("Lista de ".$this->titp);

		$grid->order_by("numero","desc");//status='P'
		$grid->per_page = 20;
		$grid->use_function('substr','str_pad');
		$grid->use_function('sta');

		$grid->column("N&uacute;mero"   ,$uri);
		$grid->column("Fecha"           ,"<dbdate_to_human><#fecha#></dbdate_to_human>","align='center'");
		$grid->column("Tipo"            ,"tipo"                                        ,"align='center'");
		$grid->column("Unidad Ejecutora","uejecuta2");
		$grid->column("Beneficiario"       ,"proveed");
		$grid->column("Beneficiario"    ,"beneficiario");
		$grid->column("Estado"          ,"<sta><#status#></sta>"                       ,"align='center'");

		$grid->db->where("status !=",'P');
		$grid->db->where("status !=",'C');


		$grid->build();
		//echo "asasa".$grid->db->last_query();

		$data['content'] = $filter->output.$grid->output;
		$data['title']   = " ".$this->titp." ";
		$data["head"]    = $this->rapyd->get_head();
		$this->load->view('view_ventanas', $data);
	}

	function dataedit(){
		//$this->datasis->modulo_id(101,1);
		$this->rapyd->load('dataobject','dataedit');

		$do   = new dataObject("ocompra");

		$edit = new DataEdit($this->tits, $do);

		$do->set('fechapago',date('Y-m-d'));

		$edit->back_url = site_url($this->url."filteredgrid");

		$edit->pre_process('update'   ,'_valida');
		$edit->post_process('update'  ,'_post');

		$edit->odirect = new  inputField("Numero O. Pago",  "odirect");
		$edit->odirect->mode ="autohide";
		$edit->odirect->group="Pago";

		$edit->fechapago = new  dateonlyField("Fecha de Pago",  "fechapago");
		$edit->fechapago->size =12;
		$edit->fechapago->rule ="required";
		$edit->fechapago->group="Pago";

		$edit->factura = new  inputField("Factura",  "factura");
		$edit->factura->mode ="autohide";
		$edit->factura->group="Causaci&oacute;n";

		$edit->controlfac = new  inputField("Control Fiscal",  "controlfac");
		$edit->controlfac->mode ="autohide";
		$edit->controlfac->group="Causaci&oacute;n";

		$edit->fechafac = new  inputField("Fecha Causaci&oacute;n",  "fechafac");
		$edit->fechafac->mode ="autohide";
		$edit->fechafac->group="Causaci&oacute;n";

		$edit->numero  = new inputField("N&uacute;mero O. Compra", "numero");
		$edit->numero->mode  ="autohide";
		$edit->numero->when  =array('show');
		$edit->numero->group ="Orden De Compra";

		$edit->tipo = new inputField("Orden de", "tipo");
		$edit->tipo->mode ="autohide";
		$edit->tipo->group="Orden De Compra";

		$edit->fecha = new  inputField("Fecha O. Compra",  "fecha");
		$edit->fecha->mode   ="autohide";
		$edit->fecha->group  ="Orden De Compra";

		$grupo='Datos';
		$edit->uejecutora = new inputField("Unidad Ejecutora", "uejecutora");
		$edit->uejecutora->mode = "autohide";
		$edit->uejecutora->group=$grupo;

		$edit->estadmin = new dropdownField("Estructura Administrativa","estadmin");
		$edit->estadmin->mode = "autohide";
		$edit->estadmin->group=$grupo;

		$edit->fondo = new dropdownField("Fondo","fondo");
		$edit->fondo->mode = "autohide";
		$edit->fondo->group=$grupo;

		$edit->cod_prov = new inputField("Beneficiario", 'cod_prov');
		$edit->cod_prov->size     = 6;
		$edit->cod_prov->group = $grupo;
		$edit->cod_prov->mode = "autohide";

		$edit->nombre = new inputField("Nombre Beneficiario", 'nombre');
		$edit->nombre->size = 50;
		$edit->nombre->group = $grupo;
		$edit->nombre->mode = "autohide";

		$edit->beneficiario = new inputField("Beneficiario", 'beneficiario');
		$edit->beneficiario->size = 50;
		$edit->beneficiario->mode = "autohide";
		$edit->beneficiario->group = $grupo;
		//$edit->beneficiario->rule = "required";

		$edit->observa = new textAreaField("Observaciones", 'observa');
		$edit->observa->mode = "autohide";
		$edit->observa->group = $grupo;

		$edit->reteiva_prov  = new inputField("Retencion IVA Beneficiario", "reteiva_prov");
		$edit->reteiva_prov->mode = "autohide";
		$edit->reteiva_prov->group="Retenci&oacute;n Iva";

		$edit->reteiva = new inputField("Retencion de IVA", 'reteiva');
		$edit->reteiva->size = 8;
		$edit->reteiva->mode = "autohide";
		$edit->reteiva->group="Retenci&oacute;n Iva";

		$edit->creten = new inputField("Codigo ISLR","creten");
		$edit->creten->mode = "autohide";
		$edit->creten->group="Impuesto Sobre la Renta";

		$edit->reten = new inputField("Retencion de ISLR", 'reten');
		$edit->reten->size = 8;
		$edit->reten->mode = "autohide";
		$edit->reten->group="Impuesto Sobre la Renta";

		$gtotal="Montos Totales";
		$edit->subtotal = new inputField("Sub Total", 'subtotal');
		$edit->subtotal->size = 8;
		$edit->subtotal->mode = "autohide";
		$edit->subtotal->group=$gtotal;

		$edit->ivaa = new inputField("IVA Sobre Tasa", 'ivaa');
		$edit->ivaa->size = 8;
		$edit->ivaa->mode = "autohide";
		$edit->ivaa->group=$gtotal;

		$edit->ivag = new inputField("IVA Tasa General", 'ivag');
		$edit->ivag->size = 8;
		$edit->ivag->mode = "autohide";
		$edit->ivag->group=$gtotal;

		$edit->ivar = new inputField("IVA Tasa reducida", 'ivar');
		$edit->ivar->size = 8;
		$edit->ivar->mode = "autohide";
		$edit->ivar->group=$gtotal;

		$edit->exento = new inputField("Exento", 'exento');
		$edit->exento->size = 8;
		$edit->exento->mode = "autohide";
		$edit->exento->group=$gtotal;

		$edit->total = new inputField("Total", 'total');
		$edit->total->size = 8;
		$edit->total->mode = "autohide";
		$edit->total->group=$gtotal;

		$n=$edit->_dataobject->get('numero');

		$status=$edit->_dataobject->get("status");
		if($status=='T'){
			//$action = "javascript:window.location='" .site_url($this->url.'actualizar/'.$edit->rapyd->uri->get_edited_id()). "'";
			//$action = "javascript:window.location='" .site_url($this->url.'actualizar/'.$n)."'";
			//$edit->button_status("btn_status",'Ordenar Pago',$action,"TR","show");
			$edit->buttons("modify","save");
		}elseif($status=='O'){
			$action = "javascript:window.location='" .site_url($this->url.'reversar/'.$edit->rapyd->uri->get_edited_id()). "'";
			//$action = "javascript:window.location='" .site_url($this->url.'reversar/'.$n). "'";
			$edit->button_status("btn_rever",'Deshacer Ordenar Pago',$action,"TR","show");
		}else{
			$edit->buttons("save");
		}

		$edit->buttons("undo", "back");
		$edit->build();

		$data['content'] = $edit->output;
    $data['title']   = " $this->tits ";
		//$data['content'] = $edit->output;
    //$data['title']   = " $this->tits ";
    $data["head"]    = script("jquery.pack.js").script("plugins/jquery.numeric.pack.js").script("plugins/jquery.floatnumber.js").$this->rapyd->get_head();
    $this->load->view('view_ventanas', $data);
	}

	function _valida($do){
		$this->rapyd->load('dataobject');

		//$do = new DataObject("ocompra");
		//$do->rel_one_to_many('itocompra', 'itocompra', array('numero'=>'numero'));
		//$do->load($id);
		$odirect=$do->get('odirect');

		$opago = new DataObject("odirect");
		//$opago->rel_one_to_many('itodirect', 'itodirect', array('numero'=>'numero'));

		if(!empty($odirect))$opago->load($odirect);

		$opago->set('fecha'       ,$do->get('fecha'       ));
		$opago->set('tipo'        ,$do->get('tipo'        ));
		//$opago->set('ujecutora'   ,$do->get('ujecutora'   ));
		$opago->set('estadmin'    ,$do->get('estadmin'    ));
		$opago->set('fondo'       ,$do->get('fondo'       ));
		$opago->set('cod_prov'    ,$do->get('cod_prov'    ));
		$opago->set('nombre'      ,$do->get('nombre'      ));
		$opago->set('beneficiario',$do->get('beneficiario'));
		$opago->set('factura'     ,$do->get('factura'     ));
		$opago->set('controlfac'  ,$do->get('controlfac'  ));
		$opago->set('fechafac'    ,$do->get('fechafac'    ));
		$opago->set('subtotal'    ,$do->get('subtotal'    ));
		$opago->set('exento'      ,$do->get('exento'      ));
		//$opago->set('odirect'     ,$do->get('odirect'     ));
		$opago->set('ivag'        ,$do->get('ivag'        ));
		$opago->set('ivar'        ,$do->get('ivar'        ));
		$opago->set('ivaa'        ,$do->get('ivaa'        ));
		$opago->set('total'       ,$do->get('total'       ));
		$opago->set('creten'      ,$do->get('creten'      ));
		$opago->set('reten'       ,$do->get('reten'       ));
		$opago->set('observa'     ,$do->get('observa'     ));
		$opago->set('reteiva_prov',$do->get('reteiva_prov'));
		$opago->set('status'      ,'T');

		$opago->save();
		$numero=$opago->get('numero');
		
		//print_r($opago->get_all());
		//exit;

		$do->set('odirect',$numero);
		//print_r($opago->get_all());
		//exit;
		//$do->save();
		
		//redirect($this->url."actualizar/$numero");
	}

	function actualizar($id,$odirect){

		$this->rapyd->load('dataobject');

		$do = new DataObject("ocompra");
		$do->rel_one_to_many('itocompra', 'itocompra', array('numero'=>'numero'));
		$do->load($id);
		
		$do2 = new DataObject("odirect");
		$do2->load($odirect);
		$status = $do2->get('status');
		
		$error      = "";
		$codigoadm  = $do->get('estadmin');
		$fondo      = $do->get('fondo');
		$numero     = $do->get('numero');

		$presup = new DataObject("presupuesto");
		$pk=array('codigoadm'=>$codigoadm,'tipo'=>$fondo);

		$partidaiva=$this->datasis->traevalor("PARTIDAIVA");
		$pk['codigopres'] = $partidaiva;
		$presup->load($pk);

		if(empty($error)){
			$sta=$do->get('status');
			if(($sta=="T") AND $status=="T"){
				
				//for($i=0;$i < $do->count_rel('itocompra');$i++){
				//	$codigopres  = $do->get_rel('itocompra','partida',$i);
				//	$importe     = $do->get_rel('itocompra','importe',$i);
        //
				//	$pk['codigopres'] = $codigopres;
				//	$presup->load($pk);
        //
				//	$causado =$presup->get("causado");
        //
				//	//if($importe > $causado)
				//	//	$error.="<div class='alert'><p>No se Puede Completar la Transaccion debido a que el monto de la orden de pago ($importe) es mayor al monto causado($causado) para la partida: $codigopres</p></div>";
				//}

				if(empty($error)){
					for($i=0;$i < $do->count_rel('itocompra');$i++){					
						$codigopres  = $do->get_rel('itocompra','partida',$i);
						$importe     = $do->get_rel('itocompra','importe',$i);
						$iva         = $do->get_rel('itocompra','iva'    ,$i);
						//$mont        = $importe*(($iva+100)/100);
						$mont        = $importe;

						$pk['codigopres'] = $codigopres;

						$presup->load($pk);
						$opago  =  $presup->get("opago");
						$opago  =  $opago+$mont;

						$presup->set("opago",$opago);

						$presup->save();
					}

					$ivaa  =  $do->get('ivaa');
					$ivag  =  $do->get('ivag');
					$ivar  =  $do->get('ivar');
					$ivan  =  $ivag+$ivar+$ivaa;

					$pk['codigopres'] = $partidaiva;
					$presup->load($pk);

					$opago =$presup->get("opago");
					$opago+=$ivan;
					$presup->set("opago",$opago);
					$presup->save();

					$do->set('status','O');
					$do->save();
					
					$do2->set('status','O');
					$do2->save();
				}
			}else{
				$error.="<div class='alert'><p>No se Puede Completar la operacion</p></div>";
			}
		}

		if(empty($error)){
			redirect($this->url."dataedit/show/$id");
		}else{
			$data['content'] = $error.anchor($this->url."/dataedit/show/$id",'Regresar');
			$data['title']   = " ".$this->tits." ";
			$data["head"]    = $this->rapyd->get_head().script('jquery.js').script("plugins/jquery.numeric.pack.js");
			$this->load->view('view_ventanas', $data);
		}
	}

	function reversar($id){
		$this->rapyd->load('dataobject');

		$do = new DataObject("ocompra");
		$do->rel_one_to_many('itocompra', 'itocompra', array('numero'=>'numero'));
		$do->load($id);
		$odirect=$do->get("odirect");
		
		$do2 = new DataObject("odirect");
		$do2->load($odirect);
		$status = $do2->get('status');
		
		$error      = "";
		$codigoadm  = $do->get('estadmin');
		$fondo      = $do->get('fondo');
		$numero     = $do->get('numero');

		$presup = new DataObject("presupuesto");
		$pk=array('codigoadm'=>$codigoadm,'tipo'=>$fondo);

		$partidaiva=$this->datasis->traevalor("PARTIDAIVA");
		$pk['codigopres'] = $partidaiva;
		$presup->load($pk);

		if(empty($error)){

			$sta=$do->get('status');			
			if(($sta=="O") AND $status=="O"){
				
				//for($i=0;$i < $do->count_rel('itocompra');$i++){
				//	$codigopres  = $do->get_rel('itocompra','partida',$i);
				//	$importe     = $do->get_rel('itocompra','importe',$i);
        //
				//	$pk['codigopres'] = $codigopres;
				//	$presup->load($pk);
        //
				//	$causado =$presup->get("causado");
        //
				//	//if($importe > $causado)
				//	//	$error.="<div class='alert'><p>No se Puede Completar la Transaccion debido a que el monto de la orden de pago ($importe) es mayor al monto causado($causado) para la partida: $codigopres</p></div>";
				//}

				if(empty($error)){
					for($i=0;$i < $do->count_rel('itocompra');$i++){					
						$codigopres  = $do->get_rel('itocompra','partida',$i);
						$importe     = $do->get_rel('itocompra','importe',$i);
						$iva         = $do->get_rel('itocompra','iva'    ,$i);
						//$mont        = $importe*(($iva+100)/100);
						$mont        = $importe;

						$pk['codigopres'] = $codigopres;

						$presup->load($pk);
						$opago  =  $presup->get("opago");
						$opago  =  $opago-$mont;

						$presup->set("opago",$opago);

						$presup->save();
					}

					$ivaa  =  $do->get('ivaa');
					$ivag  =  $do->get('ivag');
					$ivar  =  $do->get('ivar');
					$ivan  =  $ivag+$ivar+$ivaa;

					$pk['codigopres'] = $partidaiva;
					$presup->load($pk);

					$opago =$presup->get("opago");
					$opago-=$ivan;
					$presup->set("opago",$opago);
					$presup->save();

					$do->set('status','T');
					$do->save();
					
					$do2->set('status','T');
					$do2->save();
				}
			}else{
				$error.="<div class='alert'><p>No se Puede Completar la operacion</p></div>";
			}
		}

		if(empty($error)){
			redirect($this->url."dataedit/show/$id");
		}else{
			$data['content'] = $error.anchor($this->url."/dataedit/show/$id",'Regresar');
			$data['title']   = " ".$this->tits." ";
			$data["head"]    = $this->rapyd->get_head().script('jquery.js').script("plugins/jquery.numeric.pack.js");
			$this->load->view('view_ventanas', $data);
		}
	}

	function _post($do){
		$id=$do->get("numero");
		$odirect=$do->get("odirect");
		
		//echo $id."a".$odirect;
		//exit;
		redirect($this->url."actualizar/$id/$odirect");
	}
}