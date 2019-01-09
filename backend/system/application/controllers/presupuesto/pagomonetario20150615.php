<?php
class pagomonetario extends Controller {

	var $titp  = 'Ordenes de Pago Sin Imputacion Presupuestaria';
	var $tits  = 'Orden de Pago Sin Imputacion Presupuestaria';
	var $url   = 'presupuesto/pagomonetario/';


	function pagomonetario(){
		parent::Controller();
		$this->load->library("rapyd");
		$this->datasis->modulo_id(178,1);
	}

	function index(){
		redirect($this->url."filteredgrid");
	}

	function filteredgrid(){
		
		$this->rapyd->load("datafilter2","datagrid");
		
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
			'titulo'  =>'Buscar Beneficiario'
		);
		
		$bSPRV=$this->datasis->p_modbus($mSPRV,"proveed");
		
		
		$filter = new DataFilter2("");
		$filter->db->select("a.reverso reverso,a.total2,a.numero numero,a.fecha fecha,a.tipo tipo,a.status status,a.cod_prov cod_prov,a.beneficiario beneficiario,b.nombre uejecuta2,c.nombre proveed");
		$filter->db->from("odirect a");                  
		$filter->db->join("uejecutora b" ,"a.uejecutora=b.codigo",'LEFT');
		$filter->db->join("sprv c"       ,"c.proveed=a.cod_prov",'LEFT');
		$filter->db->where('MID(status,1,1) ','M');
				
		$filter->numero = new inputField("N&uacute;mero", "numero");
		$filter->numero->size=12;
		//$filter->numero->clause="likerigth";
		
		//$filter->tipo = new dropdownField("Orden de ", "tipo");
		//$filter->tipo->option("","");
		//$filter->tipo->option("Compra"  ,"Compra");
		//$filter->tipo->option("Servicio","Servicio");
		//$filter->tipo->style="width:100px;";
		
		$filter->fecha = new dateonlyField("Fecha", "fecha");
		$filter->fecha->dbformat = "Y-m-d";
		$filter->fecha->size=12;
		
		$filter->uejecutora = new inputField("Unidad Ejecutora", "uejecutora");
		$filter->uejecutora->size=12;
		
		$filter->cod_prov = new inputField("Beneficiario", 'cod_prov');
		$filter->cod_prov->size = 6;
		$filter->cod_prov->append($bSPRV);
		$filter->cod_prov->rule = "required";
		$filter->cod_prov->db_name='a.cod_prov';
		
		$filter->status = new dropdownField("Estado","status");		
		$filter->status->option("","");
		$filter->status->option("M2","Actualizado");
		$filter->status->option("M1","Sin Actualizar");		
		$filter->status->option("M3","Pagado");
		$filter->status->style="width:150px";
		
		$filter->buttons("reset","search");
		
		$filter->build();
		$uri = anchor($this->url.'dataedit/show/<#numero#>','<str_pad><#numero#>|8|0|STR_PAD_LEFT</str_pad>');
		
		function sta($status){
			switch($status){
				case "M1":return "Sin Actualizar";break;
				case "M2":return "Actualizado";break;
				case "M3":return "Pagado";break;
				//case "O":return "Ordenado Pago";break;
				case "MY":return "Reverso";break;
				case "MA":return "Anulado";break;
			}
		}
		
		$grid = new DataGrid("");
		
		
		
		$grid->order_by("numero","desc");
		$grid->per_page = 20;
		$grid->use_function('substr','str_pad','sta');
		
		$grid->column_orderby("N&uacute;mero"    ,$uri                                             ,"numero");
		//$grid->column_orderby("Tipo"             ,"tipo"                                           ,"tipo"          ,"align='center'");
		$grid->column_orderby("Fecha"            ,"<dbdate_to_human><#fecha#></dbdate_to_human>"   ,"fecha"         ,"align='center'");
		$grid->column_orderby("Unidad Ejecutora" ,"uejecuta2"                                      ,"uejecuta2"     ,"align='left'NOWRAP");
		$grid->column_orderby("Beneficiario"     ,"proveed"                                        ,"proveed"       ,"align='left'NOWRAP");
		$grid->column_orderby("Pago"             ,"<number_format><#total2#>|2|,|.</number_format>","total2"        ,"align='right'NOWRAP");
		$grid->column_orderby("Estado"           ,"<sta><#status#></sta>"                          ,"status"        ,"align='center'NOWRAP");
		
		
		$grid->add($this->url."dataedit/create");
		$grid->build();
		
		//echo $grid->db->last_query();
		
		//$data['content'] = $filter->output.$grid->output;
		$data['filtro']  = $filter->output;
		$data['content'] = $grid->output;
		$data['script'] = script("jquery.js")."\n";
		$data['title']   = "$this->titp";
		$data["head"]    = $this->rapyd->get_head();
		$this->load->view('view_ventanas', $data);
	}

	function dataedit($back=''){
		$this->rapyd->load('dataobject','datadetails');
		
		$mSPRV=array(
			'tabla'   =>'sprv',
			'columnas'=>array(
			'proveed' =>'C&oacute;odigo',
			'nombre'=>'Nombre',
			'rif'=>'Rif',
			'contacto'=>'Contacto'),
			'filtro'  =>array('proveed'=>'C&oacute;digo','nombre'=>'Nombre','rif'=>'Rif'),
			'retornar'=>array('proveed'=>'cod_prov','nombre'=>'nombrep'),//,'reteiva'=>'reteiva_prov'
			//'retornar'=>'ca_total',
			'titulo'  =>'Buscar Beneficiario');
			
		$bSPRV=$this->datasis->p_modbus($mSPRV ,"sprv");
		
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
					'numero'                                 =>'compra'       ,
					'cod_prov'                               =>'cod_prov'     ,
					'total2'                                 =>'montocontrato',
					'CONCAT("Pago de Anticipo ",observa)'    =>'observa'      ,
					'CONCAT("50")'                           =>'porcent'      ,
					'subtotal'                               =>'montob'
					),
			'p_uri'=>array(
				  4=>'<#cod_prov#>'),
			'where' =>'( status = "C" ) AND IF(<#cod_prov#> = ".....", cod_prov LIKE "%" ,cod_prov = <#cod_prov#>)',
			'script'=>array('cal_total()'),
			'titulo'  =>'Buscar Ordenes de Compra');

		$pOCOMPRA=$this->datasis->p_modbus($mOCOMPRA,'<#cod_prov#>');
		
		$rr        = $this->ivaplica2();
		$pimpm = $this->datasis->traevalor('IMPMUNICIPAL');
		$pimpt = $this->datasis->traevalor('IMPTIMBRE');
		$pcrs  = $this->datasis->traevalor('CRS');
		$site_url = site_url('presupuesto/pobra/islr');
		
		$uri  =$this->datasis->get_uri();
		$idt=$this->datasis->dameval("SELECT id FROM modbus WHERE idm='ocompra' AND uri='$uri'");
		$modblink2=site_url('/buscar/index/'.$idt);
		
		
		$script='
			$(".inputnum").numeric(".");
			$(function() {
			
				function cal(){
					subtotal = parseFloat($("#subtotal").val());
					ivag     = 1 * $("#ivag"    ).val();
					ivar     = 1 * $("#ivar"    ).val();
					ivaa     = 1 * $("#ivaa"    ).val();
					iva      = parseFloat(ivaa) + parseFloat(ivag) + parseFloat(ivar);
					
					reteiva_prov = parseFloat($("#reteiva_prov").val());
					
					if(		(isNaN(reteiva_prov)) || (reteiva_prov==0) || (reteiva_prov == "") || (reteiva_prov == 100) )
						reteiva_prov = 100;
					else
						reteiva_prov = 75;

					reteiva  = iva * parseFloat( reteiva_prov/ 100);
					reten    = parseFloat($("#reten").val());
					total2   = subtotal + iva ;
					total    = total2 - reteiva - reten ;

					$("#reteiva_prov" ).val(reteiva_prov);
					$("#reteiva" ).val(reteiva);
					$("#total2" ).val(total2);
					$("#total" ).val(total);
					$("#iva" ).val(iva);
					
				}
				
				$("#civag").change(function(){
					if($("#civag").attr("checked")==true)
						$("#ivag").val($("#subtotal").val() * '.$rr['tasa'].'/100);
					else
						$("#ivag").val("0");
						cal();
				});
				
				$("#civar").change(function(){
					if($("#civar").attr("checked")==true)
						$("#ivar").val($("#subtotal").val() * '.$rr['redutasa'].'/100);
					else
						$("#ivar").val("0");
					cal();
				});
				
				$("#civaa").change(function(){
					if($("#civaa").attr("checked")==true)
						$("#ivaa").val($("#subtotal").val() * '.$rr['sobretasa'].'/100);
					else
						$("#ivaa").val("0");
					cal();
				});
												
				$("#creten").change(function(){				
					$.post("'.$site_url.'",{ creten:$("#creten").val(),subtotal:$("#subtotal").val() },function(data){
						$("#reten").val(data);
					})
					
					cal();
				});
				
				$("#subtotal").change(function(){
					cal();
				});
				
			});
			$(document).ready(function() {
				
				$("#porcent").keypress(function(){
					cal_total();
				});
			});
			
			function cal_total(){
					montob=$("#montob").val();
					porcent=$("#porcent").val();
					
					if(porcent>=100){
						porcent=50;
						$("#porcent").val(50);
					}
					total=montob*porcent/100;
					$("#total").val(Math.round(total*100)/100);
				}
			
			function modbusdepen(){
				
				var cod_prov =$("#cod_prov").val();
				if(cod_prov=="")cod_prov=".....";
				
				var link="'.$modblink2.'"+"/"+cod_prov;
				
				vent=window.open(link,"ventbuscarocompra","width=800,height=600,scrollbars=Yes,status=Yes,resizable=Yes,screenx=5,screeny=5");
				vent.focus();

				document.body.setAttribute("onUnload","vent=window.open(\"about:blank\",\"ventbuscarocompra\");vent.close();");
			}
			
			function btn_anulaf(i){
				if(!confirm("Esta Seguro que desea Anular la Orden de Compra Causada ?"))
					return false;
				else
					window.location="'.site_url('presupuesto/common/pm_anular').'/"+i
			}
		';
				
		$do = new DataObject("odirect");
		$do->pointer('sprv' ,'sprv.proveed=odirect.cod_prov','sprv.nombre as nombrep','LEFT');
		
		$edit = new DataDetails($this->tits, $do);
		if($back=='opagof')
		$edit->back_url = site_url("presupuesto/opagof/filteredgrid");
		else
		$edit->back_url = site_url($this->url."filteredgrid/index");
		
		$edit->set_rel_title('itodirect','Rubro <#o#>');
		
		$edit->script($script,"create");
		$edit->script($script,"modify");
		$edit->script($script,"show");
	
		$edit->pre_process('update'  ,'_valida');
		$edit->pre_process('insert'  ,'_valida');
		//$edit->post_process('insert'  ,'_post');
		//$edit->post_process('update'  ,'_post');
		$edit->post_process('insert','_post_insert');
		$edit->post_process('update','_post_update');
		$edit->post_process('delete','_post_delete');
	
		$edit->numero  = new inputField("N&uacute;mero", "numero");
		$edit->numero->rule = 'unique';
		if($this->datasis->traevalor('USANODIRECT')=='S'){
			$edit->numero->when=array('show');
		}else{
			$edit->numero->when=array('show','create','modify');
		}
	
		$edit->fecha = new  dateonlyField("Fecha",  "fecha");
		$edit->fecha->insertValue = date('Y-m-d');
		$edit->fecha->size =12;
		
		//$edit->tipo = new dropdownField("Orden de ", "tipo");
		//$edit->tipo->option("Compra"  ,"Compra");
		//$edit->tipo->option("Servicio","Servicio");
		//$edit->tipo->option("T","Transferencia");
		//$edit->tipo->style="width:100px;";
		
		//$edit->uejecutora = new dropdownField("Unidad Ejecutora", "uejecutora");
		//$edit->uejecutora->option("","Seccionar");
		//$edit->uejecutora->options("SELECT codigo, nombre FROM uejecutora ORDER BY nombre");
		////$edit->uejecutora->onchange = "get_uadmin();";
		//$edit->uejecutora->rule = "required";
		//$edit->uejecutora->style = "width:400px";

		$edit->cod_prov = new inputField("Beneficiario", 'cod_prov');
		$edit->cod_prov->size     = 4;
		$edit->cod_prov->rule     = "required";
		$edit->cod_prov->readonly =true;
		$edit->cod_prov->append($bSPRV);
		
		$edit->nombrep = new inputField("Nombre", 'nombrep');
		$edit->nombrep->size     = 50;
		$edit->nombrep->readonly = true;
		$edit->nombrep->pointer  = TRUE;
		$edit->nombrep->in       = "cod_prov";
		
		$edit->observa = new textAreaField("Observaciones", 'observa');
		$edit->observa->cols = 60;
		$edit->observa->rows = 3;
    
		$edit->total= new inputField("Monto a Pagar", 'total');
		$edit->total->size = 8;
		$edit->total->css_class='inputnum';
		$edit->total->value = 0;
		
		$ganticipo="Datos para Anticipos de Contratos";
		$edit->compra = new inputField("Compromiso", 'compra');
		$edit->compra->size     = 10;
		//$edit->compra->rule     = "required";
		$edit->compra->readonly =true;
		$edit->compra->append('<img src="/tortuga/assets/default/images/system-search.png"  alt="Busqueda de Ordenes de Compromisos" title="Busqueda de Ordenes de Compromisos" border="0" onclick="modbusdepen()"/>');
		$edit->compra->group = $ganticipo;
		
		$edit->porcent= new inputField("Porcentaje", 'porcent');
		$edit->porcent->size = 10;
		$edit->porcent->css_class='inputnum';
		$edit->porcent->value = 0;
		$edit->porcent->group = $ganticipo;
		
		$edit->montocontrato= new inputField("Monto Contrato", 'montocontrato');
		$edit->montocontrato->size = 10;
		$edit->montocontrato->css_class='inputnum';
		$edit->montocontrato->value = 0;
		$edit->montocontrato->readonly =true;
		$edit->montocontrato->group = $ganticipo;
		
		$edit->montob= new hiddenField("", 'montob');
		$edit->montob->size = 10;
		$edit->montob->css_class='inputnum';
		$edit->montob->value = 0;
		$edit->montob->readonly =true;
		$edit->montob->group = $ganticipo;
		
	
		$status=$edit->get_from_dataobjetct('status');
		if($status=='M1'){
			$action = "javascript:window.location='" .site_url($this->url.'actualizar/'.$edit->rapyd->uri->get_edited_id()). "'";
			$edit->button_status("btn_status",'Actualizar',$action,"TR","show");
			$edit->buttons("modify","delete","save");
		}elseif($status=='M2'){
			$action = "javascript:btn_anulaf('".$edit->rapyd->uri->get_edited_id(). "')";
			$edit->button_status("btn_anular",'Anular',$action,"TR","show");
		}elseif($status=='M'){
			$edit->buttons("modify","save");
		}elseif($status=='MA'){
			$edit->buttons("delete");
		
		}else{
			$edit->buttons("save");
		}
	
		$edit->buttons("undo","back","add");
		$edit->build();
    
		$smenu['link']=barra_menu('104');
		$data['smenu'] = $this->load->view('view_sub_menu', $smenu,true);
		//$conten["form"]  =&  $edit;
		//$data['content'] = $this->load->view('view_odirect', $conten,true);
		$data['content'] = $edit->output;
		$data['title']   = "$this->tits";
		$data["head"]    = $this->rapyd->get_head().script('jquery.js').script("plugins/jquery.numeric.pack.js").script("plugins/jquery.json.min.js");
		$this->load->view('view_ventanas', $data);
	}

	function ivaplica2($mfecha=NULL){
		if(empty($mfecha)) $mfecha=date('Ymd');
		$CI =& get_instance();
		$qq = $CI->db->query("SELECT tasa, redutasa, sobretasa FROM civa WHERE fecha < '$mfecha' ORDER BY fecha DESC LIMIT 1");
		$rr = $qq->row_array();
		//$aa = each($rr);
		return $rr;
	}

	function actualizar($id){	
		$error      = "";
		$this->rapyd->load('dataobject');
		
		$do  = new DataObject("odirect");
		$do->load($id);
		$status     = $do->get('status'   );
    				
		if(empty($error)){
			if($status == "M1" ){
				$do->set('status','M2');
				$do->save();
			}else{
				$error.="<div class='alert'><p>Este Pago No puede ser Actualizado</p></div>";
			}
		}
    		
		if(empty($error)){
		  logusu('pagomonetario','Actualizo pago monetario numero $id');
			redirect($this->url."dataedit/show/$id");
		}else{
			$data['content'] = $error.anchor($this->url."dataedit/show/$id",'Regresar');
			$data['title']   = " $this->tits ";
			$data["head"]    = $this->rapyd->get_head().script('jquery.js').script("plugins/jquery.numeric.pack.js");
			$this->load->view('view_ventanas', $data);
		}
	}
  
  function anular($id){ 
    $error      = "";
    $this->rapyd->load('dataobject');
    
    $do  = new DataObject("odirect");
    $do->load($id);
    $status     = $do->get('status'   );
            
    if(empty($error)){
      if($status == "M2" ){
        $do->set('status','MA');
        $do->save();
      }else{
        $error.="<div class='alert'><p>Este Pago No puede ser Anulado</p></div>";
      }
    }
        
    if(empty($error)){
      logusu('pagomonetario','anulo pago monetario numero $id');
      redirect($this->url."dataedit/show/$id");
    }else{
      $data['content'] = $error.anchor($this->url."dataedit/show/$id",'Regresar');
      $data['title']   = " $this->tits ";
      $data["head"]    = $this->rapyd->get_head().script('jquery.js').script("plugins/jquery.numeric.pack.js");
      $this->load->view('view_ventanas', $data);
    }
  }


	function _valida($do){
		
		$total = $do->get('total');
		$numero= $do->get('numero');
		$do->set('total2' ,$total );
		$do->set('status' ,'M1' );
		
		if(empty($error)){
			if(empty($error) && empty($do->loaded)){
				if(empty($numero)){
					if($this->datasis->traevalor('USANODIRECT')=='S'){
						$nodirect = $this->datasis->fprox_numero('nodirect');
						$do->set('numero',$nodirect);
						$do->pk=array('numero'=>$nodirect);
					}else
						$error.="Debe introducir un numero de orden de pago</br>";
				}elseif($this->datasis->traevalor('USANODIRECT')!='S'){
					$numeroe = $this->db->escape($numero);
					$chk     = $this->datasis->dameval("SELECT COUNT(*) FROM odirect WHERE numero=$numeroe");
					if($chk>0)
						$error.="Error el numero de orden de pago ya existe</br>";
				}
			}
		}
		
		if(!empty($error)){
			$do->error_message_ar['pre_ins']=$error;
			$do->error_message_ar['pre_upd']=$error;
			return false;
		}
	}
	
	function _post_insert($do){
		$numero = $do->get('numero');
		logusu('pagomonetario',"ingreso pago monetario numero $numero");
	}
	
	  function _post_update($do){
		$numero = $do->get('numero');
		logusu('pagomonetario',"modifico pago monetario numero $numero");
	  }
  
	function instalar(){
		$query="	ALTER TABLE `odirect` CHANGE COLUMN `compra` `compra` VARCHAR(10) NULL DEFAULT NULL";
		$this->db->simple_query($query);
		$query="ALTER TABLE `odirect` ADD COLUMN `montocontrato` DECIMAL(19,2) UNSIGNED NULL DEFAULT '0.00' AFTER `retenomina`";
		$this->db->simple_query($query);
	}
}
?>

