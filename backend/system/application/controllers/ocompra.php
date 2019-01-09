<?php
class Ocompra extends Controller {

	function Ocompra(){
		parent::Controller();
		$this->load->library("rapyd");
		$this->formatopres=$this->datasis->traevalor('FORMATOPRES');
		$this->flongpres  =strlen(trim($this->formatopres));
		//$this->datasis->modulo_id(302,1);
	}
	function index(){
		redirect("presupuesto/ocompra/filteredgrid");
	}


	function filteredgrid(){
		$this->rapyd->load("datafilter","datagrid");
		
		$filter = new DataFilter("Filtro","ocompra");

		$filter->numero = new inputField("N&uacute;mero", "numero");
		$filter->numero->size=15;
		
		$filter->fecha = new dateonlyField("Fecha", "fecha");
		$filter->fecha->size=12;
		
		$filter->uejecutora = new inputField("Unidad Ejecutora", "uejecutora");
		$filter->uejecutora->size=40;
		
		$filter->beneficiario = new inputField("Beneficiario", "beneficiario");
		$filter->beneficiario->size=40;
		
		$filter->buttons("reset","search");
		$filter->build();
		$uri = anchor('presupuesto/ocompra/dataedit/show/<#numero#>','<str_pad><#numero#>|8|0|STR_PAD_LEFT</str_pad>');
		
		$grid = new DataGrid("Ordenes de compras");
		$grid->order_by("numero","desc");
		$grid->per_page = 20;
		$grid->use_function('substr','str_pad');
		
		$grid->column("N&uacute;mero"   ,$uri);
		$grid->column("Fecha"           ,"<dbdate_to_human><#fecha#></dbdate_to_human>","align='center'");
		$grid->column("Unidad Ejecutora","uejecutora");
		$grid->column("Beneficiario"    ,"beneficiario");
		//echo $grid->db->last_query();
		$grid->add("presupuesto/ocompra/dataedit/create");
		$grid->build();
		
		$data['content'] = $filter->output.$grid->output;
		$data['title']   = " Ordenes de Compra ";
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
				'contacto'=>'Contacto'),
				'filtro'  =>array('proveed'=>'C&oacute;digo','nombre'=>'Nombre'),
				'retornar'=>array('proveed'=>'cod_prov'),
				'titulo'  =>'Buscar Beneficiario');
			
		$bSPRV=$this->datasis->p_modbus($mSPRV,"proveed");
		
		$modbus=array(
			'tabla'   =>'ppla',
			'columnas'=>array(
				'codigo'      =>'C&oacute;digo',
				'denominacion'=>'Denominaci&oacute;n'),
			'filtro'  =>array('codigo' =>'C&oacute;digo','denominacion'=>'Denominaci&oacute;n'),
			'retornar'=>array('codigo'=>'partida_<#i#>'),
			'p_uri'=>array(4=>'<#i#>',5=>'<#fondo#>',6=>'<#estadmin#>',),
			'where'=>'tipo=<#fondo#> AND codigoadm=<#estadmin#> AND LENGTH(ppla.codigo)='.$this->flongpres,
			'join' =>array('presupuesto','presupuesto.codigopres=ppla.codigo',''),
			'titulo'  =>'Busqueda de partidas');
		$btn=$this->datasis->p_modbus($modbus,'<#i#>/<#fondo#>/<#estadmin#>');
		
		$script='
			
					
			function cal_importe(i){
				id=i.toString();
				cana  =parseFloat($("#cantidad_"+id).val());
				precio=parseFloat($("#precio_"+id).val());
				op=cana*precio;
				if(!isNaN(op))
					$("#importe_"+id).val(cana*precio);
				$("#iva_"+id).val();	
				
			}
			';

		$do = new DataObject("ocompra");
		$do->rel_one_to_many('itocompra', 'itocompra', array('numero'=>'numero'));

		$edit = new DataDetails("Orden de Compra", $do);
		$edit->back_url = site_url("presupuesto/ocompra/filteredgrid");
		$edit->set_rel_title('itocompra','Rubro <#o#>');
		$edit->script($script,'create');
		$edit->script($script,'modify');

		$edit->numero  = new inputField("N&uacute;mero", "numero");
		$edit->numero->mode="autohide";
		$edit->numero->when=array('show');

		$edit->fecha = new  dateonlyField("Fecha",  "fecha");
		$edit->fecha->insertValue = date('Y-m-d');
		$edit->fecha->size =12;

		$edit->uejecutora = new dropdownField("Unidad Ejecutora", "uejecutora");
		$edit->uejecutora->option("","Seccionar");
		$edit->uejecutora->options("SELECT codigo, nombre FROM uejecutora ORDER BY nombre");
		//$edit->uejecutora->onchange = "get_uadmin();";
		$edit->uejecutora->rule = "required";
		
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
		
		$edit->cod_prov = new inputField("Beneficiario", 'cod_prov');
		$edit->cod_prov->size = 6;
		$edit->cod_prov->append($bSPRV);
		$edit->cod_prov->rule = "required";

		$edit->beneficiario = new inputField("Beneficiario", 'beneficiario');
		$edit->beneficiario->size = 50;
		$edit->beneficiario->rule = "required";


		$edit->observa = new textAreaField("Observaciones", 'observa');
		$edit->observa->rows = 4;
		$edit->observa->cols = 60;
		
		
		//$edit->tcantidad = new inputField("tcantidad", 'tcantidad');
		//$edit->tcantidad->size = 8;
		
		$edit->subtotal = new inputField("Sub Total", 'subtotal');
		$edit->subtotal->size = 8;
		
		$edit->ivaa = new inputField("IVA aumentado", 'ivaa');
		$edit->ivaa->size = 8;
		
		$edit->ivag = new inputField("IVA general", 'ivag');
		$edit->ivag->size = 8;
		
		$edit->ivar = new inputField("IVA reducido", 'ivar');
		$edit->ivar->size = 8;
		
		$edit->total = new inputField("Total", 'total');
		$edit->total->size = 8;
		
		/*$edit->uadministra = new dropdownField("Unidad Administrativa", "uadministra");
		$edit->uadministra->option("","Ninguna");
		$ueje=$edit->getval('uejecuta');
		if($ueje!==false){
			$edit->uadministra->options("SELECT codigo,nombre FROM uadministra WHERE codigoejec='$ueje' ORDER BY nombre");
		}else{
			$edit->uadministra->option("","Seleccione una unidad ejecutora primero");
		}*/
		
		$edit->itpartida = new inputField("(<#o#>) Partida", "partida_<#i#>");
		$edit->itpartida->rule='callback_repetido|required';
		$edit->itpartida->size=15;
		$edit->itpartida->append('<img src="/tortuga/assets/default/images/system-search.png"  alt="Busqueda de partidas" title="Busqueda de partidas" border="0" onclick="modbusdepen(<#i#>)"/>');
		$edit->itpartida->db_name='partida';
		$edit->itpartida->rel_id ='itocompra';
		
		$edit->itdescripcion = new inputField("(<#o#>) Descripci&oacute;n", "descripcion_<#i#>");
		$edit->itdescripcion->db_name='descripcion';
		$edit->itdescripcion->maxlength=80;
		$edit->itdescripcion->size=30;
		$edit->itdescripcion->rule   = 'required';
		$edit->itdescripcion->rel_id ='itocompra';
		
		$edit->itunidad = new dropdownField("(<#o#>) Unidad", "unidad_<#i#>");
		$edit->itunidad->db_name= 'unidad';
		$edit->itunidad->rule   = 'required';
		$edit->itunidad->rel_id = 'itocompra';
		$edit->itunidad->options("SELECT unidades AS id,unidades FROM unidad ORDER BY unidades");
		$edit->itunidad->style="width:80px";
		
		$edit->itcantidad = new inputField("(<#o#>) Cantidad", "cantidad_<#i#>");
		$edit->itcantidad->css_class='inputnum';
		$edit->itcantidad->db_name  ='cantidad';
		$edit->itcantidad->rel_id   ='itocompra';
		$edit->itcantidad->rule     ='numeric';
		$edit->itcantidad->onchange ='cal_importe(<#i#>);';
		$edit->itcantidad->size     =4;
    
		$edit->itprecio = new inputField("(<#o#>) Precio", "precio_<#i#>");
		$edit->itprecio->css_class='inputnum';
		$edit->itprecio->db_name  ='precio';
		$edit->itprecio->rel_id   ='itocompra';
		$edit->itprecio->rule     ='numeric';
		$edit->itprecio->onchange ='cal_importe(<#i#>);';
		$edit->itprecio->size     =8;
		
		$ivas=$this->_ivaplica();
		$edit->itiva = new dropdownField("(<#o#>) IVA", "iva_<#i#>");
		$edit->itiva->db_name  ='iva';
		$edit->itiva->rel_id   ='itocompra';
		$edit->itiva->onchange ='cal_importe(<#i#>);';
		$edit->itiva->options($ivas);
		$edit->itiva->onchange = "cal_importe(<#i#>)";
		$edit->itiva->style    ="width:80px";
    
		$edit->itimporte = new inputField("(<#o#>) Importe", "importe_<#i#>");
		$edit->itimporte->css_class='inputnum';
		$edit->itimporte->db_name  ='importe';
		$edit->itimporte->rel_id   ='itocompra';
		$edit->itimporte->rule     ='numeric';
		$edit->itimporte->readonly =true;
		$edit->itimporte->size     =8;
				
		$edit->buttons("modify", "save", "undo", "delete", "back","add_rel"); 
		$edit->build();

		$data[''];
		$smenu['link']=barra_menu('101');
		$data['smenu'] = $this->load->view('view_sub_menu', $smenu,true);
		$conten["form"]  =&  $edit;
		$data['content'] = $this->load->view('view_ocompra', $conten,true); 
		//$data['content'] = $edit->output;
		$data['title']   = " Orden de Compra ";
		$data["head"]    = $this->rapyd->get_head().script('jquery.js').script("plugins/jquery.numeric.pack.js");
		$this->load->view('view_ventanas', $data);
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

}
?>
