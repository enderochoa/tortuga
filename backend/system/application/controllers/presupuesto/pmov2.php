<?php
class pmov2 extends Controller {

	var $titp   = 'Fondos en Anticipo';
	var $tits   = 'Fondo en Anticipo';
	var $url    = 'presupuesto/pmov2/';
	var $id_rel = 'item';

	function pmov2(){
		parent::Controller();
		$this->load->library("rapyd");
		$this->formatopres =$this->datasis->traevalor('FORMATOPRES');
		$this->flongpres   =strlen(trim($this->formatopres));
	}

	function index(){
		redirect($this->url."filteredgrid");
	}

	function filteredgrid(){
		//$this->datasis->modulo_id(101,1);
		$this->rapyd->load("datafilter","datagrid");
		$mBANC=array(
				'tabla'   =>'banc',
				'columnas'=>array(
				'codbanc' =>'C&oacute;odigo',
				'banco'=>'Banco',
				'numcuent'=>'Cuenta'),
				'filtro'  =>array(
				 'codbanc' =>'C&oacute;odigo',
				'banco'=>'Banco',
				'numcuent'=>'Cuenta'),
				'retornar'=>array('codbanc'=>'codbanc'),
				'titulo'  =>'Buscar Banco');

		$bBANC=$this->datasis->p_modbus($mBANC,"banc");

		$filter = new DataFilter("Filtro de $this->titp","pmov");

		$filter->numero = new inputField("Numero", "numero");
		$filter->numero->size=15;

		$filter->orden = new inputField("Orden de Pago", "orden");
		$filter->orden->size=15;

		$filter->fecha2 = new dateonlyField("Fecha Deposito", "fecha2");
		$filter->fecha2->size=12;

		$filter->buttons("reset","search");
		
		$filter->db->or_where('status','C');
		$filter->db->or_where('status','T');
		
		$filter->build();

		$uri = anchor($this->url.'dataedit/show/<#numero#>','<str_pad><#numero#>|8|0|'.STR_PAD_LEFT.'</str_pad>');

		$grid = new DataGrid("Lista de ".$this->titp);
		$grid->order_by("numero","desc");
		$grid->per_page = 20;
		$grid->use_function('substr','str_pad');

		$grid->column("N&uacute;mero"    ,$uri);
		$grid->column("Orden de Pago"    ,"orden"                                          ,"align='center'");
		$grid->column("Banco Emisor"     ,"bancemi"                                                         );
		$grid->column("Cheque"           ,"cheque"                                                          );
		//$grid->column("Fecha cheque"     ,"<dbdate_to_human><#fecha1#></dbdate_to_human>"  ,"align='center'");
		$grid->column("Monto"            ,"<number_format><#monto#>|2|,|.</number_format>" ,"align='right'" );
		$grid->column("Banco deposito"   ,"codban"                                                          );
		$grid->column("Deposito Nº"      ,"deposito"                                                        );
		$grid->column("Fecha Deposito"   ,"<dbdate_to_human><#fecha2#></dbdate_to_human>"  ,"align='center'");

		//$grid->add($this->url."dataedit/create");
		$grid->build();

		$data['content'] = $filter->output.$grid->output;
		$data['title']   = " Asignaci&oacute;n de Fondos ";
		$data["head"]    = $this->rapyd->get_head();
		$this->load->view('view_ventanas', $data);
	}

	function dataedit(){
		//$this->datasis->modulo_id(101,1);
		$this->rapyd->load('dataobject','datadetails');

		$modbus=array(
			'tabla'   =>'v_presaldo',
			'columnas'=>array(
				'codigo'      =>'C&oacute;digo',
				'denominacion'=>'Denominaci&oacute;n',
				'saldo'       =>'Saldo'
				),
			'filtro'  =>array('codigo' =>'C&oacute;digo','denominacion'=>'Denominaci&oacute;n'),
			'retornar'=>array('codigo'=>'partida_<#i#>','denominacion'=>'denomi_<#i#>'),//
			'p_uri'=>array(4=>'<#i#>',5=>'<#fondo#>',6=>'<#estadmin#>',),
			'where'=>'fondo=<#fondo#> AND codigoadm=<#estadmin#> AND LENGTH(codigo)='.$this->flongpres,
			'titulo'  =>'Busqueda de partidas');

		$btn=$this->datasis->p_modbus($modbus,'<#i#>/<#fondo#>/<#estadmin#>');

		$do = new DataObject("pmov");
		
		$do->rel_one_to_many($this->id_rel, 'itpmov', array('numero'=>'numero'));

		$edit = new DataDetails($this->tits, $do);
		$edit->back_url = site_url($this->url."/filteredgrid");
		$edit->set_rel_title($this->id_rel,'Rubro <#o#>');

		//$edit->pre_process('insert'  ,'_valida');
		//$edit->pre_process('update'  ,'_valida');
		//$edit->post_process('insert'  ,'_paiva');
		//$edit->post_process('update'  ,'_paiva');

		$edit->numero  = new inputField("N&uacute;mero", "numero");
		$edit->numero->mode="autohide";
		$edit->numero->when=array('show');

		$edit->fecha = new  dateonlyField("Fecha",  "fecha");
		$edit->fecha->insertValue = date('Y-m-d');
		$edit->fecha->size =12;

    $edit->estadmin = new dropdownField("Estructura Administrativa","estadmin");
		$edit->estadmin->option("","Seleccione");
		$edit->estadmin->rule='required';
		$edit->estadmin->options("SELECT b.codigo, CONCAT_WS(' ',b.codigo,b.denominacion) AS val FROM presupuesto AS a JOIN estruadm AS b ON a.codigoadm=b.codigo  GROUP BY b.codigo");

		$edit->fondo = new dropdownField("Fondo", "fondo");
		$edit->fondo->rule = "required";
		$estadmin=$edit->getval('estadmin');
		if($estadmin!==false){
			$edit->fondo->options("SELECT tipo,tipo a  FROM presupuesto WHERE codigoadm='$estadmin' GROUP BY tipo");
		}else{
			$edit->fondo->option("","Seleccione una estructura administrativa primero");
		}
		
		$edit->observa = new textAreaField("Observaciones", 'observa');
		$edit->observa->cols = 80;
		$edit->observa->rows = 3;

		$edit->monto = new inputField("Total", 'monto');
		$edit->monto->css_class='inputnum';
		$edit->monto->size = 10;

		$edit->itpartida = new inputField("(<#o#>) Partida", "partida_<#i#>");
		$edit->itpartida->rule        = 'callback_repetido|required|callback_itpartida';
		$edit->itpartida->size        = 15;
		$edit->itpartida->db_name     = 'partida';
		$edit->itpartida->rel_id      = $this->id_rel;
		//$edit->itpartida->readonly  = true;
		$edit->itpartida->append('<img src="/tortuga/assets/default/images/system-search.png"  alt="Busqueda de partidas" title="Busqueda de partidas" border="0" onclick="modbusdepen(<#i#>)"/>');
		
		$edit->itdenomi = new inputField("(<#o#>) Denominaci&oacute;n", "denomi_<#i#>");
		$edit->itdenomi->db_name  ='denomi';
		$edit->itdenomi->maxlength=250;
		$edit->itdenomi->size     =40;
		//$edit->itdenomi->rule     = 'required';
		$edit->itdenomi->rel_id   =$this->id_rel;

    $edit->itmonto = new inputField("(<#o#>) Monto", "monto_<#i#>");
		$edit->itmonto->css_class = 'inputnum';
		$edit->itmonto->db_name   = 'monto';
		$edit->itmonto->rel_id    = $this->id_rel;
		$edit->itmonto->rule      = 'numeric';
		$edit->itmonto->size      = 8;
		$edit->itmonto->onchange  = 'cal_total(<#i#>);';

		$status=$edit->get_from_dataobjetct('status');
		if($status=='C'){
			$action = "javascript:window.location='" .site_url($this->url.'actualizar/'.$edit->rapyd->uri->get_edited_id()). "'";
			$edit->button_status("btn_status",'Actualizar',$action,"TR","show");
			$edit->buttons("modify","save");
		}elseif($status=='T'){
			$action = "javascript:window.location='" .site_url($this->url.'reversar/'.$edit->rapyd->uri->get_edited_id()). "'";
			$edit->button_status("btn_rever",'Reversar',$action,"TR","show");
		}else{
			$edit->buttons("save");
		}

		$edit->buttons("undo","back","add_rel");
		$edit->build();

		$smenu['link']     = barra_menu('101');
		$data['smenu']     = $this->load->view('view_sub_menu', $smenu,true);
		$conten["form"]    =&  $edit;
		$data['content']   = $this->load->view('view_pmov2', $conten,true);
		//$data['content'] = $edit->output;
		$data['title']     = " $this->tits ";
		$data["head"]      = $this->rapyd->get_head().script('jquery.js').script("plugins/jquery.numeric.pack.js").script("plugins/jquery.json.min.js");
		$this->load->view('view_ventanas', $data);
	}
/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
////////////////////////////////////////                                                                        /////////////////////////////////////////////
////////////////////////////////////////        FIN DATAEDIT                                                    /////////////////////////////////////////////
////////////////////////////////////////        INICION FUNCIONES OPERATIVAS                                    /////////////////////////////////////////////
////////////////////////////////////////                                                                        /////////////////////////////////////////////
/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
	function repetido($partida){
		if(isset($this->__rpartida)){
			if(in_array($partida, $this->__rpartida)){
				$this->validation->set_message('repetido',"El rublo %s ($partida) esta repetido");
				return false;
			}
		}
		$this->__rpartida[]=$partida;
		return true;
	}

	function itpartida($partida){
		$estadmin = $this->db->escape($this->input->post('estadmin'));
		$fondo    = $this->db->escape($this->input->post('fondo'));
		$partida  = $this->db->escape($partida);
		$cana=$this->datasis->dameval("SELECT COUNT(*) FROM presupuesto WHERE codigoadm=$estadmin AND codigopres=$partida AND tipo=$fondo");
		if($cana>0){
			return true;
		}else{
			$this->validation->set_message('itpartida',"La partida %s ($partida) No pertenece al la estructura administrativa o al fondo seleccionado");
			return false;
		}
	}

	function actualizar($id){
		$this->rapyd->load('dataobject');

		$do = new DataObject("pmov");
		$do->rel_one_to_many('itpmov', 'itpmov', array('numero'=>'numero'));
		$do->load($id);

		$codigoadm   = $do->get('estadmin');
		$fondo       = $do->get('fondo');
		$montot      = $do->get('monto');

		$presup = new DataObject("presupuesto");
		$pk=array('codigoadm'=>$codigoadm,'tipo'=>$fondo);
		$error='';
		$tot=0;
		$sta=$do->get('status');
		if($sta=="C"){
			for($i=0;$i <  $do->count_rel('itpmov');$i++){
				$codigopres  = $do->get_rel('itpmov','partida',$i);
				$monto       = $do->get_rel('itpmov','monto'  ,$i);

				$tot += $monto;

				$pk['codigopres'] = $codigopres;
				$presup->load($pk);
				
				$recibido     = $presup->get("recibido");

				if($recibido < 0)
					$error.="<div class='alert'><p>partida ($codigopre):El monto debe ser positivo</p></div>";
			}    
			
			if($montot != $tot)
				$error.="<div class='alert'><p>La suma de los montos de las partidas es diferente al monto del deposito</p></div>";
			
			if(empty($error)){
				$tot=0;
				for($i=0;$i < $do->count_rel('itpmov');$i++){
					$codigopres  = $do->get_rel('itpmov','partida',$i);
					$monto       = $do->get_rel('itpmov','monto'  ,$i);
        	
					$tot += $monto;
        	
					$pk['codigopres'] = $codigopres;
					$presup->load($pk);
					
					$recibido     = $presup->get("recibido");
        	
					echo "a".$recibido+=$monto;
			  	
					$presup->set("recibido",$recibido);
					$presup->save();
				}
				$do->set('status','T');
				$do->save();
			}
		}

		if(empty($error)){
			redirect($this->url."/dataedit/show/$id");
		}else{
			$data['content'] = $error.anchor($this->url."/dataedit/show/$id",'Regresar');
			$data['title']   = " $this->tits ";
			$data["head"]    = $this->rapyd->get_head().script('jquery.js').script("plugins/jquery.numeric.pack.js");
			$this->load->view('view_ventanas', $data);
		}
	}

	function reversar($id){
		$this->rapyd->load('dataobject');

		$do = new DataObject("pmov");
		$do->rel_one_to_many('itpmov', 'itpmov', array('numero'=>'numero'));
		$do->load($id);

		$codigoadm   = $do->get('estadmin');
		$fondo       = $do->get('fondo');

		$presup = new DataObject("presupuesto");
		$pk=array('codigoadm'=>$codigoadm,'tipo'=>$fondo);
		$error='';
		$tot=0;
		$sta=$do->get('status');
		if($sta=="T"){
			for($i=0;$i <  $do->count_rel('itpmov');$i++){
				$codigopres  = $do->get_rel('itpmov','partida',$i);
				$monto       = $do->get_rel('itpmov','monto'  ,$i);

				$tot += $monto;

				$pk['codigopres'] = $codigopres;
				$presup->load($pk);
				
				$recibido     = $presup->get("recibido");

				if($recibido < 0)
					$error.="<div class='alert'><p>partida ($codigopre):El monto debe ser positivo</p></div>";
			}
			
			if(empty($error)){
				$tot=0;
				for($i=0;$i < $do->count_rel('itpmov');$i++){
					$codigopres  = $do->get_rel('itpmov','partida',$i);
					$monto       = $do->get_rel('itpmov','monto'  ,$i);
        	
					$tot += $monto;
        	
					$pk['codigopres'] = $codigopres;
					$presup->load($pk);
					
					$recibido     = $presup->get("recibido");
        	
					$recibido-=$monto;
			  	
					$presup->set("recibido",$recibido);
					$presup->save();
				}

				$do->set('status','C');
				$do->save();
			}
		}

		if(empty($error)){
			redirect($this->url."/dataedit/show/$id");
		}else{
			$data['content'] = $error.anchor($this->url."/dataedit/show/$id",'Regresar');
			$data['title']   = " $this->tits ";
			$data["head"]    = $this->rapyd->get_head().script('jquery.js').script("plugins/jquery.numeric.pack.js");
			$this->load->view('view_ventanas', $data);
		}
	}

	function _valida($do){		
		$tot=0;
		for($i=0;$i <   $do->count_rel('itpmov');$i++){
			$monto        = $do->get_rel('itpmov','monto'      ,$i);
			$tot+=$monto;
		}
		$do->set('total'     ,    $tot );
	}
	function positivo($valor){
		if ($valor <= 0){
			$this->validation->set_message('positivo',"El campo monto ($valor) debe ser positivo");
			return FALSE;
		}
  	return TRUE;
	}
}
?>


