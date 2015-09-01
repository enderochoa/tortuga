<?php
class Retenomi extends Controller {

	var $url  ="nomina/retenomi/";
	var $titp ="Retenciones de Nomina";
	var $tits ="Retencion de Nomina"; 

	function Retenomi(){
		parent::Controller();
		$this->load->library("rapyd");

		$this->datasis->modulo_id(175,1);
	}
	function index(){
		if($this->datasis->traevalor('USANOMINA')=='N')
			redirect($this->url."filteredgrid");
		else
			redirect($this->url."filteredgrid2");
	}
	
	function filteredgrid(){
		$this->rapyd->load("datafilter2","datagrid");
				
		$filter = new DataFilter2("");

		$filter->nomi = new inputField("N&uacute;mero", "nomi");
		$filter->nomi->size=15;
		//$filter->numero->clause="likerigth";
		
		$filter->denomi = new inputField("Denominacion", "denomi");
		$filter->denomi->size=15;
		
		$filter->fecha = new dateonlyField("Fecha", "fecha");
		$filter->fecha->dbformat = "Y-m-d";
		$filter->fecha->size=12;
		
		$filter->buttons("reset","search");    
		$filter->build();
		
		$uri = anchor($this->url.'/dataedit/show/<#nomi#>','<str_pad><#nomi#>|8|0|STR_PAD_LEFT</str_pad>');
		
		$grid = new DataGrid("");
		
		$grid->db->select(array("a.nomi","a.numero","a.contrato","a.fecha","total"));
		$grid->db->from('nomina a');
		//$grid->db->join('noco b' ,'a.contrato = b.codigo','LEFT' );
		//$grid->db->groupby('numero');
		$grid->db->orderby('fecha','desc');
		$grid->use_function('substr','str_pad');
		
		$grid->per_page = 20;
		$grid->column_orderby("N&uacute;mero"   ,$uri                                             ,"nomi" );
		$grid->column_orderby("Fecha"           ,"<dbdate_to_human><#fecha#></dbdate_to_human>"   ,"fecha","align='center'");
		$grid->column_orderby("Total"           ,"<number_format><#total#>|2|,|.</number_format>" ,"total","align='right'");
		//$grid->column("Contrato"        ,"contrato"                  );
		//$grid->column("Contrato"        ,"nombre"                                         ,'align="left"');
		
		
		$grid->add($this->url."dataedit/create");

		$grid->build();

		//$data['content'] = $filter->output.$grid->output;
		$data['filtro']  = $filter->output;
		$data['content'] = $grid->output;
		$data['script'] = script("jquery.js")."\n";
		$data['title']   = "Deducciones de Nomina ";
		$data["head"]    = script("jquery.js").$this->rapyd->get_head();
		$this->load->view('view_ventanas', $data);	
	}
	
	function filteredgrid2(){
		$this->rapyd->load("datafilter2","datagrid");
				
		$mSPRV=array(
			'tabla'   =>'sprv',
			'columnas'=>array(
			'proveed' =>'C&oacute;odigo',
			'nombre'=>'Nombre',
			'rif'=>'Rif',
			'contacto'=>'Contacto'),
			'filtro'  =>array('proveed'=>'C&oacute;digo','nombre'=>'Nombre','rif'=>'Rif'),
			'retornar'=>array('proveed'=>'cod_prov'),
			
			//'retornar'=>'ca_total',
			'titulo'  =>'Buscar Beneficiario');
		
		$bSPRV =$this->datasis->p_modbus($mSPRV ,"sprv");
		
		$filter = new DataFilter2($this->titp);

		$filter->nomi = new inputField("N&uacute;mero", "nomina");
		$filter->nomi->size=15;
		//$filter->numero->clause="likerigth";
				
		$filter->cod_prov = new inputField("Proveedor", "cod_prov");
		$filter->cod_prov->rule='required';
		$filter->cod_prov->size=5;
		$filter->cod_prov->append($bSPRV);
		
		$filter->buttons("reset","search");    
		$filter->build();
		
		$uri = anchor($this->url.'/dataedit/show/<#nomina#>','<str_pad><#nomina#>|8|0|STR_PAD_LEFT</str_pad>');
		
		$grid = new DataGrid("Lista de Deducciones de N&oacute;mina");
		
		$grid->db->select(array("nomina","b.nombre","SUM(monto)total"));
		$grid->db->from('retenomi');
		$grid->db->join('sprv b' ,'retenomi.cod_prov = b.proveed','LEFT' );
		$grid->db->groupby('nomina');
		$grid->db->orderby('nomina','desc');
		$grid->use_function('substr','str_pad');
		
		$grid->per_page = 20;
		$grid->column("N&uacute;mero"   ,$uri                        );
		$grid->column("Proveedor"       ,"nombre"                                         ,'align="left"');
		$grid->column("Total"           ,"<number_format><#total#>|2|,|.</number_format>" ,'align="right"');
		
		$grid->add($this->url."dataedit/create");

		$grid->build();

		$data['content'] = $filter->output.$grid->output;
		$data['title']   = " $this->titp ";
		$data["head"]    = script("jquery.js").$this->rapyd->get_head();
		$this->load->view('view_ventanas', $data);	
	}

	
	function dataedit(){
		$this->rapyd->load('dataobject','datadetails');
		
		$mSPRV=array(
			'tabla'   =>'sprv',
			'columnas'=>array(
			'proveed' =>'C&oacute;odigo',
			'nombre'=>'Nombre',
			'rif'=>'Rif',
			'contacto'=>'Contacto'),
			'filtro'  =>array('proveed'=>'C&oacute;digo','nombre'=>'Nombre','rif'=>'Rif'),
			'p_uri'=>array(4=>'<#i#>',),
			'retornar'=>array('proveed'=>'cod_prov_<#i#>','nombre'=>'nombrep_<#i#>'),
			
			//'retornar'=>'ca_total',
			'titulo'  =>'Buscar Beneficiario');
		
		$bSPRV =$this->datasis->p_modbus($mSPRV ,"<#i#>");
		
		$do = new DataObject("nomina");
		$do->rel_one_to_many('retenomi', 'retenomi', array('nomi'=>'nomina'));
		//$do->pointer('noco','noco.codigo = nomina.contrato','nombre AS nombrec');
		$do->rel_pointer('retenomi','sprv','retenomi.cod_prov = sprv.proveed','sprv.nombre AS nombrep');
		
		$edit = new DataDetails($this->tits, $do);
		
		if($this->datasis->traevalor('USANOMINA')=='N')
			$edit->back_url = site_url($this->url."filteredgrid");
		else
			$edit->back_url = site_url($this->url."filteredgrid2");
		//$edit->pre_process('update' ,'_pre_process');
		//$edit->pre_process('delete' ,'_pre_process');
		$edit->set_rel_title('retenomi','Rubro <#o#>');
		
		$edit->pre_process('update'  ,'_valida');
		$edit->pre_process('insert'  ,'_nodoble');
				
		$edit->nomi = new inputField("N&uacute;mero", "nomi");
		//$edit->nomi->rule ="callback_chexiste";
		$edit->nomi->size =12;
		$edit->nomi->when =array('show','create');
		
		$edit->fecha = new  dateonlyField("Fecha",  "fecha");
		$edit->fecha->insertValue = date('Y-m-d');
		$edit->fecha->size =12;

		$edit->total = new inputField("Total", "total");
		$edit->total->size=15;
		$edit->total->readonly = true;
		//$edit->valor->mode="autohide";
		//$edit->valor->when=array('show','modify');
		//
		$edit->denomi = new inputField("Descripci&oacute;n", "denomi");
		$edit->denomi->size=80;

		$edit->itcod_prov = new inputField("(<#o#>) Proveedor", "cod_prov_<#i#>");
		$edit->itcod_prov->rule='required';
		$edit->itcod_prov->size=5;
		$edit->itcod_prov->append($bSPRV);
		$edit->itcod_prov->db_name='cod_prov';
		$edit->itcod_prov->rel_id ='retenomi';
		
		$edit->itnombrep = new inputField("(<#o#>)Nombre", 'nombrep_<#i#>');
		$edit->itnombrep->db_name  = 'nombrep';
		$edit->itnombrep->size     = 30;
		$edit->itnombrep->readonly = true;
		$edit->itnombrep->pointer  = true;
		$edit->itnombrep->rel_id   ='retenomi';
		
		$edit->itmonto = new inputField("(<#o#>) monto", "monto_<#i#>");
		$edit->itmonto->rule='required|callback_positivo';
		$edit->itmonto->db_name='monto';
		$edit->itmonto->rel_id ='retenomi';
		$edit->itmonto->size=15;
		$edit->itmonto->css_class='inputnum';
		$edit->itmonto->onchange ='cal_total(<#i#>);';
		
		///$status=$edit->get_from_dataobjetct('status');
		///if($status=='P'){
		///	$action = "javascript:window.location='" .site_url('presupuesto/audis/actualizar/'.$edit->rapyd->uri->get_edited_id()). "'";
		///	$edit->button_status("btn_status",'Actualizar',$action,"TR","show");
		///	$edit->buttons("modify","delete","save");
		///}elseif($status=='C'){
		///	$action = "javascript:window.location='" .site_url('presupuesto/audis/reversar/'.$edit->rapyd->uri->get_edited_id()). "'";
		///	$edit->button_status("btn_rever",'Anular',$action,"TR","show");        
		///}else{
		///	$edit->buttons("save");
		///}	
		
		$edit->buttons("modify","delete","save","undo" , "back","add_rel"); 
		$edit->build();


		//$smenu['link']   =barra_menu('104');
		//$data['smenu']   = $this->load->view('view_sub_menu', $smenu,true);
		$conten["form"]  =&  $edit;
		$data['content'] = $this->load->view('view_retenomi', $conten,true); 
		//$data['content'] = $edit->output;
		$data['title']   = " $this->tits ";
		$data["head"]    = $this->rapyd->get_head().script('jquery.js').script("plugins/jquery.numeric.pack.js");
		$this->load->view('view_ventanas', $data);
	}
	
	function chexiste($nomi){
	
		$chek=$this->datasis->dameval("SELECT COUNT(*) FROM nomina WHERE nomi=$nomi");
		if ($chek > 0){
			$this->validation->set_message('chexiste',"El n&uacute;mero $nomi de nomina ya existe");
			return FALSE;
		}else {
  		return TRUE;
		}	
	}
	
	function _pre_process($do){
		$error='Documento ya procesado, no se puede modificar';
		$do->error_message_ar['pre_upd']=$error;
		$do->error_message_ar['pre_del']=$error;
		$status=$do->get('status');
		if($status!='P'){
			return false;	
		}
	}



	function actualizar($id){
		$this->rapyd->load('dataobject');
		
		$do = new DataObject("audis");
		$do->rel_one_to_many('itaudis', 'itaudis', array('numero'=>'numero'));
		$do->load($id);
		
		$sta=$do->get('status');
		if($sta=='P'){			
			$tipo     =$do->get('tipo');
			$campo    =($tipo=='AUMENTO') ? 'aumento':'disminucion';
			$factor   = 1;
			
			$error='';
			if($tipo!='AUMENTO'){
				for($i=0;$i < $do->count_rel('itaudis');$i++){
					$codigopres       = $do->get_rel('itaudis','codigopres' ,$i);
					$monto            = $do->get_rel('itaudis','monto'      ,$i);
					$ordinal          = $do->get_rel('itaudis','ordinal'    ,$i);
					$codigoadm        = $do->get_rel('itaudis','codigoadm'  ,$i);
					$fondo            = $do->get_rel('itaudis','fondo'      ,$i);
					
					$error.=$this->chequeapresup($codigoadm,$fondo,$codigopres,$ordinal,$monto,0,'round($monto,2) > round(($presupuesto-$comprometido),2)',"El Monto ($monto) es mayor al disponible para la partida ($codigoadm) ($fondo) ($codigopres)");
				}
				if(empty($error))
					$factor = -1;
			}
			
			if(empty($error)){
				for($i=0;$i < $do->count_rel('itaudis');$i++){
					$codigopres       = $do->get_rel('itaudis','codigopres' ,$i);
					$monto            = $do->get_rel('itaudis','monto'      ,$i);
					$ordinal          = $do->get_rel('itaudis','ordinal'    ,$i);
					$codigoadm        = $do->get_rel('itaudis','codigoadm'  ,$i);
					$fondo            = $do->get_rel('itaudis','fondo'      ,$i);
					
					$error.=$this->afectapresup($codigoadm,$fondo,$codigopres,$ordinal,$monto,0, (1*$factor) ,array($campo));
				}
				if(empty($error)){
					$do->set('status','C');
					$do->set('faudis',date('Ymd'));
					$do->save();
					$this->sp_presucalc($codigoadm);
				}
			}
		}else{
			$error.="<div class='alert'><p>No se puede realizar la operacion para este aumento ó disminucion</p></div>";
		}
		
		if(empty($error)){
			logusu('retenomi',"actualizo ");
			redirect("presupuesto/audis/dataedit/show/$id");
		}else{
			logusu('audis',"actualizo $campo numero $id con error $error");
			$data['content'] = $error.anchor("presupuesto/audis/dataedit/show/$id",'Regresar');
			$data['title']   = " Aumentos y Disminuciones ";
			$data["head"]    = $this->rapyd->get_head();
			$this->load->view('view_ventanas', $data);
		}
	}
	
	function reversar($id){
		$this->rapyd->load('dataobject');
		
		$do = new DataObject("audis");
		$do->rel_one_to_many('itaudis', 'itaudis', array('numero'=>'numero'));
		$do->load($id);
		
		$sta=$do->get('status');
		if($sta=='C'){			
			$tipo     =$do->get('tipo');
			$campo    =($tipo=='AUMENTO') ? 'aumento':'disminucion';
			$factor   = 1;
			
			$error='';
			if($tipo=='AUMENTO'){
				for($i=0;$i < $do->count_rel('itaudis');$i++){
					$codigopres       = $do->get_rel('itaudis','codigopres' ,$i);
					$monto            = $do->get_rel('itaudis','monto'      ,$i);
					$ordinal          = $do->get_rel('itaudis','ordinal'    ,$i);
					$codigoadm        = $do->get_rel('itaudis','codigoadm'  ,$i);
					$fondo            = $do->get_rel('itaudis','fondo'      ,$i);
					
					$error.=$this->chequeapresup($codigoadm,$fondo,$codigopres,$ordinal,$monto,0,'$monto > ($presupuesto-$comprometido)',"El Monto ($monto) es mayor al disponible para la partida ($codigoadm) ($fondo) ($codigopres)");
				}
				if(empty($error))
					$factor = -1;
			}
			
			if(empty($error)){
				for($i=0;$i < $do->count_rel('itaudis');$i++){
					$codigopres       = $do->get_rel('itaudis','codigopres' ,$i);
					$monto            = $do->get_rel('itaudis','monto'      ,$i);
					$ordinal          = $do->get_rel('itaudis','ordinal'    ,$i);
					$codigoadm        = $do->get_rel('itaudis','codigoadm'  ,$i);
					$fondo            = $do->get_rel('itaudis','fondo'      ,$i);
					
					$error.=$this->afectapresup($codigoadm,$fondo,$codigopres,$ordinal,$monto,0, (1*$factor) ,array($campo));
				}
				if(empty($error)){
					$do->set('status','A');
					$do->save();
					$this->sp_presucalc($codigoadm);
				}
			}
		}else{
			$error.="<div class='alert'><p>No se puede realizar la operacion para este aumento ó disminucion</p></div>";
		}
		
		if(empty($error)){
			logusu('audis',"reverso $campo numero $id");
			redirect("presupuesto/audis/dataedit/show/$id");
		}else{
			logusu('audis',"reverso $campo numero $id con error $error");
			$data['content'] = $error.anchor("presupuesto/audis/dataedit/show/$id",'Regresar');
			$data['title']   = " Aumentos y Disminuciones ";
			$data["head"]    = $this->rapyd->get_head();
			$this->load->view('view_ventanas', $data);
		}
	}
	
	
	function nodoble($do){
		$nomi  = $do->get('nomi');
		$error='';
		if($nomi !=''){
			$chek=$this->datasis->dameval("SELECT COUNT(*) FROM nomina WHERE nomi=$nomi");
			if ($chek > 0){
				$error.="<div class='alert'><p> El n&uacute;mero $nomi de nomina ya existe</p></div>";
				$do->error_message_ar['pre_ins']=$error;
				$do->error_message_ar['pre_upd']=$error;
				return FALSE;
			}else{
	  		return TRUE;
			}
		}
		$this->_valida($do);
	}
	
	function _valida($do){
		$this->rapyd->load('dataobject');
		
		$odirect = new DataObject("odirect");
	
		$total=0;
		for($i=0;$i < $do->count_rel('retenomi');$i++){
			$total+=$monto= $do->get_rel('retenomi','monto' ,$i);
		}
		
		$nomi  = $do->get('nomi');
		$error = '';
		
		if(empty($error)){
			$odirect->load_where('nomina',$nomi);
			$totala = $odirect->get('total');
			$statusa= $odirect->get('status');
			
			if(substr($statusa,1,1)=='3')$error.="<div class='alert'><p>No se pude modificar las retenciones, debido a que ya este desembolsada</p></div>";
			if(empty($error)){
				$odirect->set('retenomina'   ,$total);
				$odirect->set('total'        ,$totala-$total);
				
				$odirect->save();
			}
		}
		
		if(!empty($error)){
			$do->error_message_ar['pre_ins']=$error;
			$do->error_message_ar['pre_upd']=$error;
			return false;
		}
	}
	
	function positivo($valor){
		if ($valor < 0){
			$this->validation->set_message('positivo',"El campo monto debe ser positivo");
			return FALSE;
		}
  	return TRUE;
	}
	
	function chstatus($status){
		$this->validation->set_message('chstatus',"No lo puedes cambiar pq no quiero");
		return false;
	}
} 
?>