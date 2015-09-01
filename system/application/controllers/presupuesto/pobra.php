<?php
require_once(BASEPATH.'application/controllers/presupuesto/common.php');
class Pobra extends Common {

var $titp  = 'Ordenes de Pago de Obras';
var $tits  = 'Orden de Pago de Obra';
var $url   = 'presupuesto/pobra/';

	function pobra(){
		parent::Controller();
		$this->load->library("rapyd");
		$this->datasis->modulo_id(137,1);
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
						'titulo'  =>'Buscar Beneficiario');
		
		$bSPRV=$this->datasis->p_modbus($mSPRV,"proveed");
		
		
//		$filter = new DataFilter2("","odirect");
//		$filter->db->where('MID(status,1,1) ','O');
		$filter = new DataFilter2("");
		$filter->db->select("a.reverso reverso,a.total2,a.numero numero,a.fecha fecha,a.tipo tipo,a.obr,a.status status,a.cod_prov cod_prov,a.beneficiario beneficiario,b.nombre uejecuta2,c.nombre proveed");
		$filter->db->from("odirect a");                  
		$filter->db->join("uejecutora b" ,"a.uejecutora=b.codigo","left");
		$filter->db->join("sprv c"       ,"c.proveed=a.cod_prov","left");
		$filter->db->where('MID(status,1,1) ','O');
		
		$filter->numero = new inputField("N&uacute;mero", "numero");
		$filter->numero->size=12;
		//$filter->numero->clause="likerigth";
		
//		$filter->tipo = new dropdownField("Orden de ", "tipo");
//		$filter->tipo->option("","");
//		$filter->tipo->option("Compra"  ,"Compra");
//		$filter->tipo->option("Servicio","Servicio");
//		$filter->tipo->style="width:100px;";
		
		$filter->fecha = new dateonlyField("Fecha", "fecha");
		$filter->fecha->dbformat = "Y-m-d";
		$filter->fecha->size=12;
		
		$filter->uejecutora = new inputField("Unidad Ejecutora", "uejecutora");
		$filter->uejecutora->size=12;
		
		$filter->cod_prov = new inputField("Beneficiario", 'cod_prov');
		$filter->cod_prov->size = 6;
		$filter->cod_prov->append($bSPRV);
		$filter->cod_prov->rule = "required";
		$filter->cod_prov->clause   = 'where';
		$filter->cod_prov->operator = '=';
		
		$filter->obra = new inputField("Obra", "obr");
		$filter->obra->size=6;
		
		$filter->status = new dropdownField("Estado","status");		
		$filter->status->option("","");
		$filter->status->option("O2","Actualizado");
		$filter->status->option("O1","Sin Actualizar");		
		$filter->status->option("O3","Pagado");
		$filter->status->option("OY","Anulado");
		$filter->status->style="width:100px";
		
		$filter->buttons("reset","search");
		
		$filter->build();
		$uri = anchor($this->url.'dataedit/show/<#numero#>','<str_pad><#numero#>|8|0|STR_PAD_LEFT</str_pad>');
		
		function sta($status){
			switch($status){
				case "O1":return "Sin Actualizar";break;
				case "O2":return "Actualizado";break;
				case "O3":return "Pagado";break;
				//case "O":return "Ordenado Pago";break;
				//case "E":return "Pagado";break;
				case "A":return "Anulado";break;
			}
		}
		
		$grid = new DataGrid("");
		$grid->order_by("numero","desc");
		$grid->per_page = 20;
		$grid->use_function('substr','str_pad','sta');
		
		$grid->column_orderby("N&uacute;mero"    ,$uri,"numero");
		//$grid->column_orderby("Tipo"             ,"tipo"                                           ,"tipo"          ,"align='center'");
		$grid->column_orderby("Fecha"            ,"<dbdate_to_human><#fecha#></dbdate_to_human>"   ,"fecha"         ,"align='center'");
		//$grid->column_orderby("Unidad Ejecutora" ,"uejecutora"                                     ,"uejecutora"    ,"align='left'NOWRAP");
		$grid->column_orderby("Beneficiario"     ,"proveed"                                        ,"proveed"       ,"align='left'  ");
		$grid->column_orderby("Obra"             ,"obr"                                            ,"obr"           ,"align='center'");
		$grid->column_orderby("Pago"             ,"<number_format><#total2#>|2|,|.</number_format>","total2"        ,"align='right'");
		$grid->column_orderby("Estado"           ,"<sta><#status#></sta>"                          ,"status"        ,"align='center'");
		
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
			'retornar'=>array('proveed'=>'cod_prov','nombre'=>'nombre','reteiva'=>'reteiva_prov'),
			'titulo'  =>'Buscar Beneficiario');	
			
		
		$bSPRV2=$this->datasis->p_modbus($mSPRV ,"sprv");
		
		$mOBRA=array(
			'tabla'   =>'obra',
			'columnas'=>array(
				'numero'     =>'Numero',
				'contrato'   =>'Contrato',
				'codigoadm'  =>'Est. Admin',
				'fondo'      =>'Fondo',
				'codigopres' =>'Partida',
				'ordinal'    =>'Ordinal'),
			'filtro'  =>array(
				'numero'     =>'Numero',
				'contrato'   =>'Contrato',
				'codigoadm'  =>'Est. Admin',
				'fondo'      =>'Fondo',
				'codigopres' =>'Partida',
				'ordinal'    =>'Ordinal'),
			'retornar'=>array(
				'numero'       =>'obr',
				'cod_prov'     =>'cod_prov',
				'reteiva_prov' =>'reteiva_prov',
				'porcent'      =>'porcent',
				'monto'        =>'monto',  
			),
			'script'=>array('cal_amortiza()'),
			//'retornar'=>'ca_total',
			'titulo'  =>'Buscar Beneficiario');
		
		$bOBRA=$this->datasis->p_modbus($mOBRA ,"obra");
		
		
		$rr        = $this->ivaplica2();
		$pimpm = $this->datasis->traevalor('IMPMUNICIPAL');
		$pimpt = $this->datasis->traevalor('IMPTIMBRE');
		$pcrs  = $this->datasis->traevalor('CRS');
		$site_url = site_url('presupuesto/pobra/islr');
		
		$script='
			function cal_amortiza(){
				monto   = $("#monto" ).val();
				iva     = $("#iva"   ).val();
				porcent = $("#porcent" ).val();
				total2=parseFloat($("#total2" ).val());
				//alert(porcent);
			
				if($("#camortiza").attr("checked")){
					a = total2 *parseFloat(porcent)/100;
					$("#amortiza" ).val(a);
				}else{
					$("#amortiza").val("0");
				}
			}
			
			function cal_rprov(codigo){
				$.ajax({
					type: "POST",
					url: "'.site_url("presupuesto/sprv/rprov").'",
					data:"cod_prov="+codigo,
					success: function(data){
						$("#reteiva_prov").val(data);
					}
				});
			}
		
			$(".inputnum").numeric(".");
			$(function() {
				function cal(){
				
				
					if($("#civag").attr("checked")){
	
						$("#ivag").val(Math.round(
							($("#subtotal").val() * '.$rr['tasa'].'/100)*100)/100
						);
					}else
						$("#ivag").val("0");
						
					if($("#civar").attr("checked"))
						$("#ivar").val(Math.round(
						($("#subtotal").val() * '.$rr['redutasa'].'/100)*100)/100
						);
					else
						$("#ivar").val("0");
						
					if($("#civaa").attr("checked"))
						$("#ivaa").val(Math.round(
						($("#subtotal").val() * '.$rr['sobretasa'].'/100)*100)/100
						);
					else
						$("#ivaa").val("0");
						
					if($("#cimpt").attr("checked"))
						$("#imptimbre").val(Math.round(
						($("#subtotal").val() * '.$pimpt.' / 100)*100)/100 
						);
					else
						$("#imptimbre").val("0");
						
					if($("#cimpm").attr("checked")){
						$("#impmunicipal").val(Math.round(
						($("#subtotal").val() * '.$pimpm.' / 100)*100)/100
						);
					}else{
						$("#impmunicipal").val("0");
						}
					if($("#ccrs").attr("checked"))
						$("#crs").val(Math.round(
						($("#monto").val() * '.$pcrs.' / 100)*100)/100
						);
					else
						$("#crs").val("0");
					
					$.post("'.$site_url.'",{ creten:$("#creten").val(),subtotal:$("#subtotal").val() },function(data){
						$("#reten").val(data);
					})
					
					otrasrete = parseFloat($("#otrasrete").val());
				
					subtotal = parseFloat($("#subtotal").val());
					ivag     = 1 * $("#ivag"    ).val();
					ivar     = 1 * $("#ivar"    ).val();
					ivaa     = 1 * $("#ivaa"    ).val();
					iva      = parseFloat(ivaa) + parseFloat(ivag) + parseFloat(ivar);
										
					reteiva  = iva * parseFloat($("#reteiva_prov" ).val()) / 100;
					total2   = subtotal + iva ;
					total    = total2 - parseFloat($("#reteiva" ).val())-parseFloat($("#reten" ).val())-parseFloat($("#impmunicipal" ).val())-parseFloat($("#imptimbre" ).val())-parseFloat($("#crs" ).val());

					$("#reteiva" ).val(Math.round(reteiva*100)/100);
					$("#total2" ).val(Math.round(total2*100)/100);
					$("#total" ).val(Math.round(total*100)/100);
					$("#iva" ).val(Math.round(iva*100)/100);
					
					monto   = $("#monto" ).val();
					porcent = $("#porcent" ).val();
					total2=$("#total2" ).val();
					
					if($("#camortiza").attr("checked")){
						a = (parseFloat(total2)) *parseFloat(porcent)/100;
						$("#amortiza" ).val(a);
					}else{
						$("#amortiza").val("0");
					}
					
				}
				
				$("#subtotal").change(function(){
					cal();
				});
				
				$("#exento").change(function(){
					cal();
				});
				
				$("#civag").change(function(){
					cal();
				});
				
				$("#civar").change(function(){
					cal();
				});
				
				$("#civaa").change(function(){					
					cal();
				});
				
				$("#cimpt").change(function(){
					cal();
				});
				
				$("#cimpm").change(function(){	
					cal();
				});
				
				$("#ccrs").change(function(){
					cal();
				});
				
				$("#creten").change(function(){
					cal();
				});
				
				$("#camortiza").change(function(){
					cal();
				});
				
				$("#otrasrete").change(function(){
					cal();
				});
				
			});
			$(document).ready(function() {
				$("#tr_porcent").hide();
				$("#tr_monto").hide();
			});
		';
				
		$do = new DataObject("odirect");
		$do->pointer('sprv' ,'sprv.proveed=odirect.cod_prov','sprv.nombre as nombre','LEFT');
		
		$edit = new DataDetails($this->tits, $do);
		$edit->back_url = site_url($this->url."/index");
		$edit->set_rel_title('itodirect','Rubro <#o#>');
		
		$edit->script($script,"create");
		$edit->script($script,"modify");
	
		$edit->pre_process('update'  ,'_valida');
		$edit->pre_process('insert'  ,'_valida');
		//$edit->post_process('insert'  ,'_post');
		//$edit->post_process('update'  ,'_post');
	
		$edit->numero  = new inputField("N&uacute;mero", "numero");
		$edit->numero->mode="autohide";
		$edit->numero->when=array('show');
		
		$edit->obr  = new inputField("Obra", "obr");
		$edit->obr->size = 5;
		$edit->obr->append($bOBRA);
		$edit->obr->readonly = true;
		//$edit->obr->when=array('show');
		
		$edit->cod_prov = new inputField("Beneficiario", 'cod_prov');
		$edit->cod_prov->size     = 4;
		$edit->cod_prov->rule     = "required";
		$edit->cod_prov->append($bSPRV2);
		
		$edit->porcent  = new inputField("Porcentaje", "porcent");
		$edit->porcent->size = 5;
		$edit->porcent->when=array('modify');
		
		$edit->monto  = new inputField("monto", "monto");
		$edit->monto->db_name = " ";
		$edit->monto->size = 5;
		$edit->monto->when=array('modify');
		$edit->monto->value = 0;
	
		$edit->fecha = new  dateonlyField("Fecha",  "fecha");
		$edit->fecha->insertValue = date('Y-m-d');
		$edit->fecha->size =12;	
		
		$edit->observa = new textAreaField("Observaciones", 'observa');
		$edit->observa->cols = 60;
		$edit->observa->rows = 3;
		
		$edit->factura  = new inputField("Factura", "factura");
		$edit->factura->size=15;
		//$edit->factura->rule="required";
		$edit->factura->group = "Datos Factura";

		$edit->controlfac  = new inputField("Control Fiscal", "controlfac");
		$edit->controlfac->size=15;
		//$edit->controlfac->rule="required";
		$edit->controlfac->group = "Datos Factura";

		$edit->fechafac = new  dateonlyField("Fecha de Factura",  "fechafac");
		$edit->fechafac->insertValue = date('Y-m-d');
		$edit->fechafac->size =12;
		$edit->fechafac->rule="required";
		$edit->fechafac->group = "Datos Factura";
	
		$edit->subtotal = new inputField("Sub Total", 'subtotal');
		$edit->subtotal->css_class='inputnum';
		$edit->subtotal->size = 8;
		$edit->subtotal->group = "Datos Factura";
		$edit->subtotal->rule="required";
		$edit->subtotal->value =0;
		
		$edit->exento = new inputField("Exento", 'exento');
		$edit->exento->css_class='inputnum';
		$edit->exento->size = 8;
		$edit->exento->group = "Datos Factura";
		$edit->exento->value = 0;
		
		$edit->iva = new inputField("IVA", 'iva');
		$edit->iva->css_class='inputnum';
		$edit->iva->size = 8;
		$edit->iva->group = "Datos Factura";
		$edit->iva->readonly = true;
		$edit->iva->value =0;
		
		$edit->ivag2 = new freeField("Free Field","free","General");
		$edit->ivag2 ->in = "iva";
		
		$edit->ivag = new inputField("IVA General", 'ivag');
		$edit->ivag->css_class='inputnum';
		$edit->ivag->size = 8;
		$edit->ivag ->in = "iva";
		$edit->ivag->readonly = true;
		$edit->ivag->value = 0;
		
		$edit->civag = new checkboxField("I.Municipal", "civag" ,".");
		$edit->civag->db_name = " ";   
		$edit->civag->value = ($edit->get_from_dataobjetct('ivag')!=0) ? "." : "" ;
		$edit->civag ->in = "iva";
		
		$edit->ivar2 = new freeField("Free Field","free","Reducido");
		$edit->ivar2 ->in = "iva";
		
		$edit->ivar = new inputField("IVA Reducido", 'ivar');
		$edit->ivar->css_class='inputnum';
		$edit->ivar->size = 8;
		$edit->ivar ->in = "iva";
		$edit->ivar->readonly = true;
		$edit->ivar->value = 0;
		
		$edit->civar = new checkboxField("I.Municipal", "civar" ,".");
		$edit->civar->db_name = " ";   
		$edit->civar->value = ($edit->get_from_dataobjetct('ivar')!=0) ? "." : "" ;
		$edit->civar ->in = "iva";
		
		$edit->ivaa2 = new freeField("Free Field","free","Adicional");
		$edit->ivaa2 ->in = "iva";
		
		$edit->ivaa = new inputField("IVA Adicional", 'ivaa');
		$edit->ivaa->css_class='inputnum';
		$edit->ivaa->size = 8;
		$edit->ivaa ->in = "iva";
		$edit->ivaa->readonly = true;
		$edit->ivaa->value = 0;
		
		$edit->civaa = new checkboxField("I.Municipal", "civaa" ,".");
		$edit->civaa->db_name = " ";
		$edit->civaa->value = ($edit->get_from_dataobjetct('ivaa')!=0) ? "." : "" ;
		$edit->civaa ->in = "iva";
		
		$edit->total2 = new inputField("Total", 'total2');
		$edit->total2->css_class='inputnum';
		$edit->total2->size = 8;
		$edit->total2->group = "Datos Factura";
		$edit->total2->readonly = true;
		$edit->total2->value = 0;
	
		$edit->reteiva = new inputField("Retencion IVA", 'reteiva');
		$edit->reteiva->css_class='inputnum';
		$edit->reteiva->size = 8;
		$edit->reteiva->group = "Descuentos";
		$edit->reteiva->readonly = true;
		$edit->reteiva->value = 0;
		
		$edit->reteiva_prov2 = new freeField("Free Field","free","% Retencion");
		$edit->reteiva_prov2 ->in = "reteiva";
		
		$edit->reteiva_prov  = new inputField("Porcentaje de IVA", "reteiva_prov");
		$edit->reteiva_prov->size = 5;
		$edit->reteiva_prov ->in = "reteiva";
		$edit->reteiva_prov->readonly = true;
		$edit->reteiva_prov->value=100;
		
		$edit->reten = new inputField("Retenci&oacute;n ISLR", 'reten');
		$edit->reten->css_class='inputnum';
		$edit->reten->size = 8;
		$edit->reten->group = "Descuentos";
		$edit->reten->readonly = true;
		$edit->reten->value = 0;
		
		$edit->creten = new dropdownField("Codigo ISLR","creten");
//		$edit->creten->option("","");
		$edit->creten->options("SELECT codigo,CONCAT_WS(' ',codigo,activida) FROM rete ORDER BY codigo");
		$edit->creten->style="width:500px;";
		$edit->creten->in = "reten";		
		
		$edit->imptimbre= new inputField("Impuesto 1X1000", 'imptimbre');
		$edit->imptimbre->size = 8;
		$edit->imptimbre->css_class='inputnum';
		$edit->imptimbre->group = "Descuentos";
		$edit->imptimbre->value = 0;

		$edit->cimpt = new checkboxField("I.Municipal", "cimpt" ,".");
		$edit->cimpt->db_name = " ";   
		$edit->cimpt->value = ".";
		$edit->cimpt ->in = "imptimbre";
		$edit->cimpt->value = ($edit->get_from_dataobjetct('imptimbre')!=0) ? "." : "" ;
		
		$edit->impmunicipal= new inputField("Impuesto Municipal", 'impmunicipal');
		$edit->impmunicipal->size = 8;
		$edit->impmunicipal->css_class='inputnum';
		$edit->impmunicipal->group = "Descuentos";
		$edit->impmunicipal->readonly = true;
		$edit->impmunicipal->value = 0;
		
		$edit->cimpm = new checkboxField("I.Municipal", "cimpm" ,".");
		$edit->cimpm->db_name = " ";   
		$edit->cimpm->value = ".";
		$edit->cimpm ->in = "impmunicipal";
		$edit->cimpm->value = ($edit->get_from_dataobjetct('impmunicipal')!=0) ? "." : "" ;
		
		$edit->crs= new inputField("Compromiso de Responsabilidad Social", 'crs');
		$edit->crs->size = 8;
		$edit->crs->css_class='inputnum';
		$edit->crs->group = "Descuentos";
		//$edit->crs->readonly = true;
		$edit->crs->value = 0;
		
		$edit->ccrs = new checkboxField("I.Municipal", "ccrs" ,".");
		$edit->ccrs->db_name = " ";   
		$edit->ccrs->value = "";
		$edit->ccrs ->in = "crs";
		$edit->ccrs->value = ($edit->get_from_dataobjetct('crs')!=0) ? "." : "" ;		
		
		$edit->amortiza  = new inputField("Amortizacion", "amortiza");
		$edit->amortiza->size = 8;
		$edit->amortiza->group = "Descuentos";
//		$edit->amortiza->readonly = true;
		$edit->amortiza->value = 0;
		
		$edit->otrasrete = new inputField("Otras Deducciones", 'otrasrete');
		$edit->otrasrete->css_class='inputnum';
		$edit->otrasrete->size = 8;
		$edit->otrasrete->insertValue=0;
		$edit->otrasrete->group = "Descuentos";
		$edit->otrasrete->value=0;
		//$edit->otrasrete->onchange ='cal_total();';
		
		$edit->camortiza = new checkboxField("", "camortiza" ,".");
		$edit->camortiza->db_name = " ";
		$edit->camortiza->value = "";
		$edit->camortiza ->in = "amortiza";
		$edit->camortiza->value = ($edit->get_from_dataobjetct('crs')!=0) ? "." : "" ;
		
		$edit->total= new inputField("Monto a Pagar", 'total');
		$edit->total->size = 8;
		$edit->total->css_class='inputnum';
		$edit->total->readonly = true;
		$edit->total->value = 0;
	
		$status=$edit->get_from_dataobjetct('status');
		
		if($status=='O1'){
			$action = "javascript:window.location='" .site_url('presupuesto/common/po_anular/'.$edit->rapyd->uri->get_edited_id()). "'";
			$edit->button_status("btn_anular",'Anular',$action,"TR","show");
		
			$action = "javascript:window.location='" .site_url($this->url.'actualizar/'.$edit->rapyd->uri->get_edited_id()). "'";
			$edit->button_status("btn_status",'Actualizar',$action,"TR","show");
			$edit->buttons("modify","save");
			if($this->datasis->puede(386))
			$edit->buttons("delete");
		}elseif($status=='O2'){
			$action = "javascript:window.location='" .site_url('presupuesto/common/po_anular/'.$edit->rapyd->uri->get_edited_id()). "'";
			$edit->button_status("btn_anular",'Anular',$action,"TR","show");
			//$action = "javascript:window.location='" .site_url($this->url.'reversar/'.$edit->rapyd->uri->get_edited_id()). "'";
			//$edit->button_status("btn_rever",'Reversar',$action,"TR","show");
			//if($this->datasis->puede('1015'))
			
		}else{
			$edit->buttons("save");
		}
		
		$edit->buttons("undo","back");
		$edit->build();
	
		//SELECT codigo,base1,tari1,pama1 FROM rete
		$query = $this->db->query('SELECT codigo,base1,tari1,pama1 FROM rete');
	
		$rt=array();
		foreach ($query->result_array() as $row){
			$pivot=array('base1'=>$row['base1'],
			             'tari1'=>$row['tari1'],
			             'pama1'=>$row['pama1']);
			$rt['_'.$row['codigo']]=$pivot;
		}
		$rete=json_encode($rt);

		$conten['rete']=$rete;
		$ivaplica=$this->ivaplica2();
		$conten['ivar']=$ivaplica['redutasa'];
		$conten['ivag']=$ivaplica['tasa'];
		$conten['ivaa']=$ivaplica['sobretasa'];
		$conten['imptimbre']=$this->datasis->traevalor('IMPTIMBRE');
		$conten['impmunicipal']=$this->datasis->traevalor('IMPMUNICIPAL');
		
		$smenu['link']=barra_menu('172');
		$data['smenu'] = $this->load->view('view_sub_menu', $smenu,true);
		//$conten["form"]  =&  $edit;
		//$data['content'] = $this->load->view('view_odirect', $conten,true);
		$data['content'] = $edit->output;
		$data['title']   = "$this->tits";
		$data["head"]    = $this->rapyd->get_head().script('jquery.js').script("plugins/jquery.numeric.pack.js");
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

	function itpartida($partida){
		$estadmin = $this->db->escape($this->input->post('estadmin'));
		$fondo    = $this->db->escape($this->input->post('fondo'));
		$partida    = $this->db->escape($partida);
		$partidaiva = $this->datasis->traevalor("PARTIDAIVA");
		$cana=$this->datasis->dameval("SELECT COUNT(*) FROM presupuesto WHERE (asignacion+aumento-disminucion+(traslados))>0 AND codigoadm=$estadmin AND codigopres=$partida AND tipo=$fondo  ");
		if($cana > 0){
			return true;
		}else{
			$this->validation->set_message('itpartida',"La partida %s ($partida) No pertenece al la estructura administrativa o al fondo seleccionado");
			return false;
		}
	}

	function actualizar($id){	
		$error      = "";
		$this->rapyd->load('dataobject');
		
		$do  = new DataObject("odirect");
		$do->load($id);
		$obr        = $do->get('obr');
		$factura    = $do->get('factura'      );
		$controlfac = $do->get('controlfac'   );
		$fechafac   = $do->get('fechafac'     );
		$reteiva    = $do->get('reteiva'      );
		$ivaa       = $do->get('ivaa'         );
		$ivag       = $do->get('ivag'         );
		$ivar       = $do->get('ivar'         );
		$iva        = $do->get('iva'          );
		$amortiza   = $do->get('amortiza'     );
		$total      = $do->get('total'        );
		$total2     = $do->get('total2'       );

		$obra = new DataObject("obra");
		$obra->load($obr);
		$codigoadm  = $obra->get('codigoadm' );
		$fondo      = $obra->get('fondo'     );
		$codigopres = $obra->get('codigopres');
		$ordinal    = $obra->get('ordinal'   );
		$monto      = $obra->get('monto'     );
		$pagado     = $obra->get('pagado'    );
		$demostrado = $obra->get('demostrado');
		$o_status   = $obra->get('status'    );
		$pagoviejo  = $obra->get('pagoviejo' );
		
		$mont       = $total2-$amortiza;
		
		if($o_status=="O1")
			$error.="<div class='alert'><p>No se pueden hacer pagos para la obra $obr </p></div>";
			
		if(round($mont,2) > round($monto- ($pagado+$pagoviejo),2))
			$error.="<div class='alert'><p>El Monto($mont)  de la orden de pago es mayor al monto adeudado (".( $monto - ($pagado+$pagoviejo)).") para la obra $obr </p></div>";
		
		if($reteiva > 0 && (empty($factura) || empty($controlfac) || empty($fechafac)))
			$error.="<div class='alert'><p> Los campos Nro. Factura, Nro Control y Fecha factura no pueden estar en blanco</p></div>";

		if(empty($error)){
			$sta=$do->get('status');
			if($sta=="O1" ){
				$mont    = $total2-$amortiza;
				
				$error.=$this->chequeapresup($codigoadm,$fondo,$codigopres,$ordinal,$mont,0,'round($monto,2) > round(($comprometido-$causado),2)',"El Monto ($mont) es mayor al disponible para la partida ($codigoadm) ($fondo) ($codigopres) ($ordinal)");

				if(empty($error)){
					$error.=$this->afectapresup($codigoadm,$fondo,$codigopres,$ordinal,$mont,0, 1 ,array("causado","opago"));

					$obra->set('pagado',$pagado+$mont);
					$do->set('fopago',date('Ymd'));
					$obra->set('status','O4');
					$obra->save();
				}
			}
		}

		if(empty($error)){
			$do->set('status','O2');
			$do->set('fopago',date('Ymd'));
			$do->save();
			logusu('pobra',"Actualizo Pago de Obra $id");
			redirect($this->url."dataedit/show/$id");
		}else{
			logusu('pobra',"Actualizo Pago de Obra $id con error $error");
			$data['content'] = $error.anchor($this->url."dataedit/show/$id",'Regresar');
			$data['title']   = " $this->tits ";
			$data["head"]    = $this->rapyd->get_head().script('jquery.js').script("plugins/jquery.numeric.pack.js");
			$this->load->view('view_ventanas', $data);
		}
	}

	function _ivaplica($mfecha=NULL){
		if(empty($mfecha)) $mfecha=date('Ymd');
		$qq = $this->datasis->damerow("SELECT tasa AS g, redutasa AS r, sobretasa AS a FROM civa WHERE fecha < '$mfecha' ORDER BY fecha DESC LIMIT 1");
		$rr=array();
		foreach ($qq AS $val){
			$rr[$val]=$val.'%';
		}
		$rr['0']='0%';
		return $rr;
	}

	function _valida($do){
		
		$this->rapyd->load('dataobject');	

		$obr          = $do->get('obr'   );

		$obra         = new Dataobject('obra');
		$obra->load($obr);
		
		$rr        = $this->ivaplica2();

		$ivaa         = $do->get('ivaa'      );
		$ivag         = $do->get('ivag'      );
		$ivar         = $do->get('ivar'      );
		$exento       = $do->get('exento'    );
		$subtotal     = $do->get('subtotal'  );
		$cod_prov     = $do->get('cod_prov'   );
		$otrasrete    = $do->get('otrasrete'  );
		
		$ivag = ($ivag!=0) ? $subtotal*$rr['tasa']     /100  : 0; 
		$ivar = ($ivar!=0) ? $subtotal*$rr['redutasa'] /100  : 0; 
		$ivaa = ($ivaa!=0) ? $subtotal*$rr['sobretasa']/100  : 0; 
		
		$do->set('ivaa',$ivaa      );
		$do->set('ivag',$ivag      );
		$do->set('ivar',$ivar      );
		
		$iva          = $ivag+$ivar+$ivaa;
		
		//$cod_prov     = $obra->get('cod_prov'  );
		$porcent      = $obra->get('porcent'   );
		$tipo         = $obra->get('tipo'      );
		$do->set('estadmin',$obra->get('codigoadm' ));
		$do->set('fondo'   ,$obra->get('fondo'     ));
		
		$creten       = $do->get('creten');

		$reteiva_prov = $this->datasis->dameval("SELECT reteiva FROM sprv WHERE proveed = '$cod_prov'");
				
		if($tipo == 'Compra'){
			$do->set('creten','');
			$do->set('reten' ,0);
		}
		
		$rete=$this->datasis->damerow("SELECT base1,tari1,pama1 FROM rete WHERE codigo='$creten'");
		if($reteiva_prov != 75)$reteiva_prov=100;
				
		$error   = '';
		$reteiva=$mivag=$mivar=$mivaa=0;
		
		$reten = 0;
		if(!empty($cod_prov)){
			$reteiva=(($iva)*$reteiva_prov)/100;
			if($rete){
				if(substr($creten,0,1)=='1')$reten=round($subtotal*$rete['base1']*$rete['tari1']/10000,2);
				else $reten=round(($subtotal-$rete['pama1'])*$rete['base1']*$rete['tari1']/10000,2);		
				if($reten < 0)$reten=0;
				$do->set('reten'     ,    $reten     );
			}
		}else{
			$reteiva=0;
		}
		
		$impt=$impm=0;
		$pimpm = $this->datasis->traevalor('IMPMUNICIPAL');
		$pimpt = $this->datasis->traevalor('IMPTIMBRE');
		$pcrs  = $this->datasis->traevalor('CRS');
		
		if($do->get('imptimbre')!=0)
			$impt=($subtotal * $pimpt/100);
		
		if($do->get('impmunicipal')!=0)
			$impm= ($subtotal * $pimpm/100);
		
		$crs = 0;
		
		if($do->get('crs')!=0)
			$crs= ($obra->get('monto') * $pcrs/100);
		
		$total2   = $iva+$subtotal;
		$amortiza=$do->get('amortiza');
		//if($do->get('amortiza')!=0)
		//	$amortiza = $total2 * $porcent / 100;
				
		
		
		$total    = $total2-$reteiva-$reten-$amortiza-$otrasrete-$impt-$impm-$crs;
		
		//echo "</br>".$total2;
		//echo "</br>".$amortiza;
		//echo "</br>".$reten;
		//echo "</br>".$reteiva;
		//echo "</br>".$impt;
		//echo "</br>".$impm;
		//echo "</br>".$crs;
		//echo "</br>".$total;
		//exit();
				
		$do->set('impmunicipal'  , $impm                );
		$do->set('imptimbre'     , $impt                );
		$do->set('crs'           , $crs                 );
		$do->set('pimpmunicipal' , $pimpm               );
		$do->set('pimptimbre'    , $pimpt               );
		$do->set('pcrs'          , $pcrs                );	
		$do->set('iva'           , $iva                 );
		$do->set('tivag'         , $rr['tasa']          );
		$do->set('tivar'         , $rr['redutasa']      );
		$do->set('tivaa'         , $rr['sobretasa']     );
		$do->set('mivag'         , ($ivag>0) ? $ivag * 100/$rr['tasa']       : 0 );
		$do->set('mivar'         , ($ivar>0) ? $ivar * 100/$rr['redutasa']   : 0 );
		$do->set('mivaa'         , ($ivaa>0) ? $ivaa * 100/$rr['sobretasa']  : 0 );
		//$do->set('subtotal'      , $subtotal            );
		$do->set('exento'        , $exento              );
		$do->set('reteiva'       , $reteiva             );
		$do->set('total'         , $total               );
		$do->set('total2'        , $total2              );
		$do->set('status'        , 'O1'                 );
		$do->set('cod_prov'      , $cod_prov            );
		$do->set('breten'        ,$rete['tari1']        );
		$do->set('amortiza'      ,$amortiza             );
		$do->set('porcent'       ,$porcent              );
		$do->set('multiple'      ,'N'                   );
		
		if(empty($error)){
			if(empty($do->loaded)){
				$nodirect=$this->datasis->fprox_numero('nodirect');
				$do->set('numero',$nodirect);
				$do->pk=array('numero'=>$nodirect);
			}
		}
		
		if(!empty($error)){
			$do->error_message_ar['pre_ins']=$error;
			$do->error_message_ar['pre_upd']=$error;
			return false;
		}

	}

	function tari1(){
		$creten=$this->db->escape($this->input->post('creten'));
		$a=$this->datasis->damerow("SELECT base1,tari1,pama1 FROM rete WHERE codigo=$creten");
		echo json_encode($a);
	}

	function sp_presucalc($codigoadm){
		//$this->db->simple_query("CALL sp_presucalc($codigoadm)");
		return true;
	}
	
	function _post($do){
		$numero = $do->get('numero');
		logusu('anti',"Cambio/creo Fondo en Avance $id");
		redirect($this->url."actualizar/$numero");
	}
	
	function islr(){
	
		$subtotal = $this->input->post('subtotal');
		$creten   = $this->input->post('creten');
		
		$rete=$this->datasis->damerow("SELECT base1,tari1,pama1 FROM rete WHERE codigo='$creten'");
		$reten = 0;
		if($rete){
			if(substr($creten,0,1)=='1')$reten=round($subtotal*$rete['base1']*$rete['tari1']/10000,2);
			else $reten=round(($subtotal-$rete['pama1'])*$rete['base1']*$rete['tari1']/10000,2);		
			if($reten < 0)$reten=0;
			echo $reten;
		}
	}

}
?>


