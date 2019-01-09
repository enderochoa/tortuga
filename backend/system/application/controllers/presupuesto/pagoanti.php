<?php
//require_once(BASEPATH.'application/controllers/presupuesto/common.php');
class Pagoanti extends Common {

var $titp  = 'Ordenes de Pago Directo';
var $tits  = 'Orden de Pago Directo';
var $url   = 'presupuesto/odirect/';

function Pagoanti(){
	parent::Controller();
	$this->load->library("rapyd");
	$this->datasis->modulo_id(119,1);
}

	function index(){
		redirect($this->url."filteredgrid");
	}

	function filteredgrid(){
		
		$this->rapyd->load("datafilter2","datagrid");
		$this->rapyd->uri->keep_persistence();
		
		$mSPRV=array(
						'tabla'   =>'sprv',
						'columnas'=>array(
							'proveed' =>'C&oacute;odigo',
							'nombre'=>'Nombre',
							'contacto'=>'Contacto'),
						'filtro'  =>array(
							'proveed'=>'C&oacute;digo',
							'nombre'=>'Nombre'),
						'retornar'=>array(
							'proveed'=>'cod_prov'),
						'titulo'  =>'Buscar Beneficiario');
		
		$bSPRV=$this->datasis->p_modbus($mSPRV,"proveed");
		
		
		$filter = new DataFilter2("Filtro de $this->titp","pacom");
		//$filter->db->where('status !=','F2');
		//$filter->db->where('status !=','F3');
		//$filter->db->where('status !=','F1');
		
		$filter->id= new inputField("N&uacute;mero", "id");
		$filter->id->size   = 12;
		$filter->id->clause = "likerigth";
		
		$filter->cod_prov = new inputField("Beneficiario", 'cod_prov');
		$filter->cod_prov->size = 6;
		$filter->cod_prov->append($bSPRV);
		$filter->cod_prov->rule = "required";
				
		$filter->buttons("reset","search");
		
		$filter->build();
		$uri = anchor($this->url.'dataedit/show/<#id#>','<str_pad><#id#>|8|0|STR_PAD_LEFT</str_pad>');
		
		$grid = new DataGrid("Lista de ".$this->titp);
		$grid->order_by("id","desc");
		$grid->per_page = 20;
		$grid->use_function('substr','str_pad','sta');
		
		$grid->column("N&uacute;mero"    ,$uri);
		$grid->column("Beneficiario"        ,"cod_prov");
		$grid->column("O.Compra"         ,"<number_format><#total2#>|2|,|.</number_format>","align='right'");
		$grid->column("Pago"             ,"<number_format><#total2#>|2|,|.</number_format>","align='right'");
		
		//echo $grid->db->last_query();
		$grid->add($this->url."dataedit/create");
		$grid->build();
		
		$data['content'] = $filter->output.$grid->output;
		$data['title']   = " $this->titp ";
		$data["head"]    = $this->rapyd->get_head();
		$this->load->view('view_ventanas', $data);
	}

	function dataedit(){
		//$this->datasis->modulo_id(119,1);
		$this->rapyd->load("dataform","datagrid");

		$mSPRV=array(
			'tabla'   =>'sprv',
			'columnas'=>array(
			'proveed' =>'C&oacute;odigo',
			'nombre'=>'Nombre',
			'rif'=>'Rif',
			'contacto'=>'Contacto'),
			'filtro'  =>array('proveed'=>'C&oacute;digo','nombre'=>'Nombre','rif'=>'Rif'),
			'p_uri'=>array(4=>'<#i#>',),
			'retornar'=>array('proveed'=>'codprov_<#i#>','nombre'=>'nombrep','reteiva'=>'reteiva_prov'),
			
			//'retornar'=>'ca_total',
			'titulo'  =>'Buscar Beneficiario');
		
		$bSPRV =$this->datasis->p_modbus($mSPRV ,"<#i#>");
		
		$mOCOMPRA=array(
				'tabla'    =>'ocompra',
				'columnas' =>array(
					'numero'     =>'N&uacute;mero',
					'tipo'       =>'Tipo',
					'uejecutora' =>'uejecutora',
					'cod_prov'   =>'Beneficiario'),
				'filtro'  =>array(
					'numero'     =>'N&uacute;mero',
					'tipo'       =>'Tipo',
					'uejecutora' =>'uejecutora',
					'cod_prov'   =>'Beneficiario'),
				'retornar'=>array(
					'numero'     =>'compra_<#i#>',
					),
			'p_uri'=>array(
				  4=>'<#i#>',
				  5=>'<#cod_prov#>'),
			'where' =>'cod_prov = <#cod_prov#> AND ( status = "T" OR status = "C" ) ',//AND ( status = "T" OR status = "P" )
			'script'=>array('debe(<#i#>)'),
				'titulo'  =>'Buscar Ordenes de Compra');

		$pOCOMPRA=$this->datasis->p_modbus($mOCOMPRA,'<#i#>/<#cod_prov#>');

		$form = new dataForm('presupuesto/presupcarga/carga');
		if($ban==0){
			$form->msj = new freeField("","tipò","Cargo");	
		}

		//$edit->pre_process('update'  ,'_valida');
		//$edit->pre_process('insert'  ,'_valida');
		////$edit->post_process('insert'  ,'_post');
		////$edit->post_process('update'  ,'_post');
		//$edit->post_process('insert','_post_insert');
		//$edit->post_process('update','_post_update');
		//$edit->post_process('delete','_post_delete');
			
		$edit->id  = new inputField("N&uacute;mero", "id");
		$edit->id->mode="autohide";
		$edit->id->when=array('show');
			
		$edit->cod_prov = new inputField("Beneficiario", 'cod_prov');
		$edit->cod_prov->size     = 6;
		$edit->cod_prov->rule     = "required";
		$edit->cod_prov->append($bSPRV);
		$edit->cod_prov->readonly=true;
		
		
		$edit->compra = new inputField("(<#o#>) ", "compra");
		$edit->compra->rule     ='callback_repetido|required|callback_itorden';
		$edit->compra->size     =15;
		$edit->compra->db_name  ='compra';
		$edit->compra->readonly =true;
		$edit->compra->append($pOCOMPRA);
		
	
		$status=$edit->get_from_dataobjetct('status');
		if($status=='B1'){
			$action = "javascript:window.location='" .site_url($this->url.'actualizar/'.$edit->rapyd->uri->get_edited_id()). "'";
			$edit->button_status("btn_status",'Actualizar',$action,"TR","show");
			$edit->buttons("modify","save");
		}elseif($status=='B2'){
			//$action = "javascript:window.location='" .site_url($this->url.'reversar/'.$edit->rapyd->uri->get_edited_id()). "'";
			//$edit->button_status("btn_rever",'Reversar',$action,"TR","show");
			$action = "javascript:window.location='" .site_url('presupuesto/common/pd_anular/'.$edit->rapyd->uri->get_edited_id()). "'";
			if($this->datasis->puede('1015'))$edit->button_status("btn_anular",'Anular',$action,"TR","show");	
		}elseif($status=='B3'){
			$multiple=$edit->get_from_dataobjetct('multiple');
			if($multiple=="N"){
				$action = "javascript:window.location='" .site_url($this->url.'camfac/dataedit/modify/'.$edit->rapyd->uri->get_edited_id()). "'";
				$edit->button_status("btn_camfac",'Modificar Factura',$action,"TR","show");
			}
		}else{
			$edit->buttons("save");
		}
		
		$edit->buttons("undo","back","add_rel");
		$edit->build();
				
		$smenu['link']=barra_menu('119');
		//$data['smenu'] = $this->load->view('view_sub_menu', $smenu,true);
		//$conten["form"]  =&  $edit;
		//$data['content'] = $this->load->view('view_odirect', $conten,true);
		$data['content'] = $edit->output;
		$data['title']   = " $this->tits ";
		$data["head"]    = $this->rapyd->get_head().script('jquery.js').script("plugins/jquery.numeric.pack.js").script("plugins/jquery.json.min.js");
		$this->load->view('view_ventanas', $data);
	}
	
	function repetido($orden){
		if(isset($this->__rorden)){
			if(in_array($orden, $this->__rorden)){
				$this->validation->set_message('repetido',"El rublo %s ($orden) esta repetido");
				return false;
			}
		}
		$this->__rorden[]=$orden;
		return true;
	}

	function itorden($orden){
		$cod_prov    = $this->db->escape($this->input->post('cod_prov'));
		$orden       = $this->db->escape($orden);
		$cana=$this->datasis->dameval("SELECT COUNT(*) FROM ocompra WHERE cod_prov=$cod_prov AND numero=$orden");
		if($cana>0){
			return true;
		}else{
			$this->validation->set_message('itorden',"La orden %s ($orden) No pertenece al proveedor ($cod_prov)");
			return false;
		}
	}
}
?>