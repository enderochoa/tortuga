<?php
class Requisicion extends Controller {

	function Requisicion(){
		parent::Controller();
		$this->load->library("rapyd");
		
		$this->formatopres=$this->datasis->traevalor('FORMATOPRES');
		$this->flongpres  =strlen(trim($this->formatopres));
	}
	function index(){
		redirect("presupuesto/requisicion/filteredgrid");
	}

	function filteredgrid(){
		$this->datasis->modulo_id(24,1);
		$this->rapyd->load("datafilter","datagrid");
		
		$link=site_url('presupuesto/requisicion/getadmin');
		$script='
			$(function() {
				$(".inputnum").numeric(".");
			});
		
			function get_uadmin(){
				$.post("'.$link.'",{ uejecuta:$("#uejecuta").val() },function(data){$("#td_uadministra").html(data);})
			}
			';

		$filter = new DataFilter("");
		
		$filter->script($script);
		
		$filter->db->select("a.numero,a.numero numero,a.fecha fecha,a.uejecuta uejecuta,a.uadministra,a.responsable,a.objetivo,a.tcantidad,a.timporte,a.status,a.ocompra,b.nombre uejecuta2,c.nombre uadministra2");
		$filter->db->from("requi a");                  
		$filter->db->join("uejecutora b" ,"a.uejecuta=b.codigo");
		$filter->db->join("uadministra c","a.uadministra=b.codigo","LEFT");
		
		$filter->numero = new inputField("N&uacute;mero", "numero");
		$filter->numero->size=15;
		
		$filter->fecha = new dateonlyField("Fecha", "fecha");
		$filter->fecha->size=12;
		
		$filter->uejecuta = new dropdownField("U.Ejecutora", "uejecuta");
		$filter->uejecuta->option("","Seccionar");
		$filter->uejecuta->options("SELECT codigo,nombre FROM uejecutora ORDER BY nombre");
		$filter->uejecuta->onchange = "get_uadmin();";
		$filter->uejecuta->rule = "required";
		
		$filter->ocompra = new inputField("O. Compra", "ocompra");
		$filter->ocompra->size=15;
		
		//$filter->uadministra = new dropdownField("U.Administrativa", "uadministra");
		//$filter->uadministra->option("","Ninguna");
		
		$filter->buttons("reset","search");    
		$filter->build();
		$uri  = anchor('presupuesto/requisicion/dataedit/show/<#numero#>'  ,'<str_pad><#numero#>|8|0|STR_PAD_LEFT</str_pad>');
		//$uri2 = anchor('presupuesto/requisicion/asig_part/modify/<#id#>','<str_pad><#id#>|8|0|STR_PAD_LEFT</str_pad>');
		
		$grid = new DataGrid("");
		$grid->order_by("numero","desc");
		$grid->per_page = 20;
		$grid->use_function('substr','str_pad');
		
		$grid->column_orderby("N&uacute;mero"         ,$uri                                             ,"numero"        );
		$grid->column_orderby("Fecha"                 ,"<dbdate_to_human><#fecha#></dbdate_to_human>"   ,"fecha"         ,"align='center'"     );
		$grid->column_orderby("Unidad Ejecutora"      ,"uejecuta2"                                      ,"uejecuta2"     ,"align='left' NOWRAP");
		$grid->column_orderby("Unidad Administrativa" ,"uadministra2"                                   ,"uadministra2"  ,"align='left' NOWRAP");
		$grid->column_orderby("O.Compra"              ,'<str_pad><#ocompra#>|8|0|STR_PAD_LEFT</str_pad>',"ocompra"       );
		$grid->column_orderby("Estado"                ,"status"                                         ,"status"        );
		$grid->column_orderby("Responsable"           ,"responsable"                                    ,"responsable"   );
		$grid->column_orderby("Objetivo"              ,"objetivo"                                       ,"objetivo"      );
		//$grid->column("Asig. Partida"         ,$uri2);
		
		$grid->add("presupuesto/requisicion/dataedit/create");
		$grid->build();
		
		//$ingrid = '<script type="text/javascript">
		//$(document).ready(
		//	function() {
		//		$("#tablagrid").ingrid({ 
		//			url: "remote.html",
		//			height: 350
		//		});
		//	}
		//); 
		//</script> ';
		//		
		
		//$data['style']   = style("ingrid.css");
		$data['filtro']  = $filter->output;
		$data['content'] = $grid->output;
		$data['title']   = "Requisiciones";//"";
		$data['script']  = script("jquery.js")."\n"; //.script("jquery.ingrid.js")."\n".$ingrid;
		$data["head"]    = $this->rapyd->get_head();
		$this->load->view('view_ventanas', $data);	
	}
	
	function filteredgrid2(){
		
		$this->rapyd->load("datafilter","datagrid");
		
		$this->datasis->modulo_id('116',1);
		
		$filter = new DataFilter("");

		$filter->db->select("a.numero,a.numero numero,a.fecha fecha,a.uejecuta uejecuta,a.uadministra,a.responsable,a.objetivo,a.tcantidad,a.timporte,a.status,a.ocompra,b.nombre uejecuta2,c.nombre uadministra2");
		$filter->db->from("requi a");                  
		$filter->db->join("uejecutora b" ,"a.uejecuta=b.codigo");
		$filter->db->join("uadministra c","codigoejec=b.codigo","LEFT");
		
		$filter->numero = new inputField("N&uacute;mero", "numero");
		$filter->numero->size=15;
		
		$filter->fecha = new dateonlyField("Fecha", "fecha");
		$filter->fecha->dbformat = "Y-m-d";
		$filter->fecha->size=12;
		
		$filter->uejecuta = new dropdownField("U.Ejecutora", "uejecuta");
		$filter->uejecuta->option("","Seccionar");
		$filter->uejecuta->options("SELECT codigo,nombre FROM uejecutora ORDER BY nombre");
		$filter->uejecuta->onchange = "get_uadmin();";
		$filter->uejecuta->rule = "required";
		
		$filter->ocompra = new inputField("O. Compra", "ocompra");
		$filter->ocompra->size=15;
		
		$filter->buttons("reset","search");    
		$filter->build();
		
		$atts = array(
              'width'      => '800',
              'height'     => '600',
              'scrollbars' => 'yes',
              'status'     => 'yes',
              'resizable'  => 'yes',
              'screenx'    => '0',
              'screeny'    => '0'
            );
		$uri2 = anchor('presupuesto/requisicion/asig_part/modify/<#numero#>','<str_pad><#numero#>|8|0|STR_PAD_LEFT</str_pad>');
		$uri3 = anchor_popup('presupuesto/requisicion/convertir/<#numero#>','O.Compra',$atts);
		
		$grid = new DataGrid("");
		$grid->order_by("numero","desc");
		$grid->per_page = 20;
		$grid->use_function('substr','str_pad');
		
		$grid->column_orderby("N&uacute;mero"    ,$uri2                                             ,"numero"      );
		$grid->column_orderby("Fecha"            ,"<dbdate_to_human><#fecha#></dbdate_to_human>"    ,"fecha"       ,"align='center'     ");
		$grid->column_orderby("U. Ejecutora"     ,"uejecuta2"                                       ,"uejecuta2"   ,"align='left' NOWRAP");
		$grid->column_orderby("O.Compra"         ,'<str_pad><#ocompra#>|8|0|STR_PAD_LEFT</str_pad>' ,"ocompra"     );
		$grid->column_orderby("Responsable"      ,"responsable"                                     ,"responsable" ,"align='left' NOWRAP");
		$grid->column_orderby("Objetivo"         ,"objetivo"                                        ,"objetivo"    ,"align='left' NOWRAP");
		$grid->column("Convertir a"      ,$uri3);
		//$grid->column("Estado"           ,"status");  
		
		$grid->build();
		
		$data['filtro']  = $filter->output;
		$data['content'] = $grid->output;
		$data['script'] = script("jquery.js")."\n";
		$data['title']   = "Requisiciones";
		//$data['content'] = $filter->output.$grid->output;
		$data["head"]    = $this->rapyd->get_head();
		$this->load->view('view_ventanas', $data);	
	}

	function dataedit(){
		$this->datasis->modulo_id(212,1);
		$this->rapyd->load('dataobject','datadetails');

		$link=site_url('presupuesto/requisicion/getadmin');
		$script='
			$(function() {
				$(".inputnum").numeric(".");
			});
		
			function get_uadmin(){
				$.post("'.$link.'",{ uejecuta:$("#uejecuta").val() },function(data){$("#td_uadministra").html(data);})
			}
			function cal_importe(i){
				id=i.toString();
				cana  =parseFloat($("#cantidad_"+id).val());
				precio=parseFloat($("#precio_"+id).val());
				op=cana*precio;
				if(!isNaN(op))
					$("#importe_"+id).val(cana*precio);
			}';

		$do = new DataObject("requi");
		$do->rel_one_to_many('itrequi', 'itrequi', array('numero'=>'numero'));

		$edit = new DataDetails("Datos de la Requisici&oacute;n", $do);
		$edit->back_url = site_url("presupuesto/requisicion/filteredgrid");
		$edit->set_rel_title('itrequi','Rubro <#o#>');
		$edit->script($script,'create');
		$edit->script($script,'modify');

		$edit->numero   = new inputField("N&uacute;mero", "numero");
		$edit->numero->mode="autohide";
		$edit->numero->when=array('show');

		$edit->fecha = new  dateonlyField("Fecha",  "fecha");
		$edit->fecha->insertValue = date('Y-m-d');
		$edit->fecha->size =12;

		$edit->responsable   = new inputField("Responsable", "responsable");
		$edit->responsable -> size=20;
		//$edit->responsable->rule = "required";

		$edit->objetivo  = new  textareaField("Objetivo", "objetivo");
		$edit->objetivo->rows     = 4;  
		$edit->objetivo->cols     = 70;
		$edit->objetivo->rule     = 'required';

		$edit->uejecuta = new dropdownField("U.Ejecutora", "uejecuta");
		$edit->uejecuta->option("","Seccionar");
		$edit->uejecuta->options("SELECT codigo,nombre FROM uejecutora ORDER BY nombre");
		$edit->uejecuta->onchange = "get_uadmin();";
		$edit->uejecuta->rule = "required";

		$edit->uadministra = new dropdownField("U.Administrativa", "uadministra");
		$edit->uadministra->option("","Ninguna");
		$ueje=$edit->getval('uejecuta');
		if($ueje!==false){
			$edit->uadministra->options("SELECT codigo,nombre FROM uadministra WHERE codigoejec='$ueje' ORDER BY nombre");
		}else{
			$edit->uadministra->option("","Seleccione una unidad ejecutora primero");
		}

		$edit->tcantidad = new inputField("Cantidad total", "tcantidad");
		$edit->tcantidad->css_class='inputnum';
		$edit->tcantidad->readonly =true;
		$edit->tcantidad->rule     ='numeric';
		$edit->tcantidad->size     =10;
		
		$edit->timporte = new inputField("Total", "timporte");
		$edit->timporte->css_class='inputnum';
		$edit->timporte->readonly =true;
		$edit->timporte->rule     ='numeric';
		$edit->timporte->size     =10;

		//comienza el detalle
		$edit->itunidad = new dropdownField("(<#o#>) Unidad", "unidad_<#i#>");
		$edit->itunidad->db_name= 'unidad';
		$edit->itunidad->rule   = 'required';
		$edit->itunidad->rel_id = 'itrequi';
		$edit->itunidad->option("","Seccionar");
		$edit->itunidad->style="width:80px";
		$edit->itunidad->options("SELECT unidades AS id,unidades FROM unidad ORDER BY unidades");

		$edit->itdescrip = new inputField("(<#o#>) Descripcion", "descrip_<#i#>");
		$edit->itdescrip->size   = 20;
		$edit->itdescrip->db_name='descrip';
		$edit->itdescrip->rel_id ='itrequi';

		$edit->itcantidad = new inputField("(<#o#>) Cantidad", "cantidad_<#i#>");
		$edit->itcantidad->css_class='inputnum';
		$edit->itcantidad->db_name  ='cantidad';
		$edit->itcantidad->rel_id   ='itrequi';
		$edit->itcantidad->rule     ='numeric';
		$edit->itcantidad->onchange ='cal_importe(<#i#>);';
		$edit->itcantidad->size     =5;

		$edit->itprecio = new inputField("(<#o#>) Precio", "precio_<#i#>");
		$edit->itprecio->css_class='inputnum';
		$edit->itprecio->db_name  ='precio';
		$edit->itprecio->rel_id   ='itrequi';
		$edit->itprecio->rule     ='numeric';
		$edit->itprecio->onchange ='cal_importe(<#i#>);';
		$edit->itprecio->size     =8;

		$edit->itimporte = new inputField("(<#o#>) Importe", "importe_<#i#>");
		$edit->itimporte->css_class='inputnum';
		$edit->itimporte->db_name  ='importe';
		$edit->itimporte->rel_id   ='itrequi';
		$edit->itimporte->rule     ='numeric';
		$edit->itimporte->readonly =true;
		$edit->itimporte->size     =10;
		//Termina el detalle
		
		$status=$edit->get_from_dataobjetct('status');
		if($status != 'X')
			$edit->buttons("modify", "save", "undo", "delete", "back","add_rel");
		else
		$edit->buttons("undo","back"); 
		
		$edit->build();

		$smenu['link']   = barra_menu('168');
		$data['smenu']   = $this->load->view('view_sub_menu', $smenu,true);
		$conten["form"]  =&  $edit;
		$data['content'] = $this->load->view('view_requisicion', $conten,true); 
		//$data['content'] = $edit->output;
		$data['title']   = "Requisici&oacute;n";
		$data["head"]    = $this->rapyd->get_head().script('jquery.js').script("plugins/jquery.numeric.pack.js");
		$this->load->view('view_ventanas', $data);
	}
	
	function asig_part(){
		$this->datasis->modulo_id('116',1);
		$this->rapyd->load('dataobject','datadetails');

		$link=site_url('presupuesto/requisicion/getadmin');
				
		$partidaiva=$this->datasis->traevalor('PARTIDAIVA');
		$modbus=array(
			'tabla'   =>'v_presaldo',
			'columnas'=>array(
				'codigo'      =>'C&oacute;digo',
				'ordinal'     =>'Ord',
				'denominacion'=>'Denominaci&oacute;n',
				'saldo'       =>'Saldo'
				),
			'filtro'  =>array('codigo' =>'C&oacute;digo','denominacion'=>'Denominaci&oacute;n'),
			'retornar'=>array('codigo'=>'partida_<#i#>'),//,'denominacion'=>'denomi_<#i#>'
			'p_uri'=>array(4=>'<#i#>',5=>'<#fondo#>',6=>'<#estadmin#>',),
			'where'=>'fondo = <#fondo#> AND codigoadm = <#estadmin#> AND movimiento = "S" AND saldo > 0',
			'titulo'  =>'Busqueda de partidas');
		$btn=$this->datasis->p_modbus($modbus,'<#i#>/<#fondo#>/<#estadmin#>');

		$do = new DataObject("requi");
		$do->rel_one_to_many('itrequi', 'itrequi', array('numero'=>'numero'));
		
		$do = new DataObject("requi");
		$do->rel_one_to_many('itrequi', 'itrequi', array('numero'=>'numero'));

		$edit = new DataDetails("Datos de la Requisici&oacute;n", $do);
		$edit->back_url = site_url("presupuesto/requisicion/filteredgrid2");
		$edit->set_rel_title('itrequi','Rubro <#o#>');
		$edit->makerel  = false;
				
		$edit->post_process('insert','_post_insert');
		$edit->post_process('update','_post_update');
		$edit->post_process('delete','_post_delete');
		
		$edit->numero   = new inputField("N&uacute;mero", "numero");
		$edit->numero->mode="autohide";
		$edit->numero->when=array('show');
		
		$edit->fecha = new  dateonlyField("Fecha",  "fecha");
		$edit->fecha->size =12;
		$edit->fecha->mode="autohide";

		$edit->responsable   = new inputField("Responsable", "responsable");
		$edit->responsable->size=50;
		$edit->responsable->mode="autohide";

		$edit->objetivo  = new  textareaField("Objetivo", "objetivo");
		$edit->objetivo->rows = 4;  
		$edit->objetivo->cols = 100;
		$edit->objetivo->mode="autohide";
		$edit->objetivo->rule="required";
		
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
		
		$edit->uejecuta = new dropdownField("U.Ejecutora", "uejecuta");
		$edit->uejecuta->option("","Seccionar");
		$edit->uejecuta->options("SELECT codigo,nombre FROM uejecutora ORDER BY nombre");
		$edit->uejecuta->onchange = "get_uadmin();";
		$edit->uejecuta->rule = "required";
		//$edit->uejecuta->readonly = true;
		$edit->uejecuta->mode = "autohide";
		

		$edit->uadministra = new dropdownField("U.Administrativa", "uadministra");
		$edit->uadministra->option("","Ninguna");
		$edit->uadministra->mode="autohide";
		$ueje=$edit->getval('uejecuta');
		if($ueje!==false){
			$edit->uadministra->options("SELECT codigo,nombre FROM uadministra WHERE codigoejec='$ueje' ORDER BY nombre");
		}else{
			$edit->uadministra->option("","Seleccione una unidad ejecutora primero");
		}
		
		$edit->tcantidad   = new inputField("Total", "tcantidad");
		$edit->tcantidad->size=50;
		//$edit->tcantidad->readonly = true;
		$edit->tcantidad->mode="autohide";
		
		$edit->timporte   = new inputField("Importe", "timporte");
		$edit->timporte->size=50;
		//$edit->timporte->readonly = true;
		$edit->timporte->mode="autohide";
		
		$edit->itunidad   = new inputField("Unidad", "itunidad_<#i#>");
		$edit->itunidad->db_name= 'unidad'; 
		$edit->itunidad->rel_id = 'itrequi';
		$edit->itunidad->size=10;
		$edit->itunidad->readonly = true;
		//$edit->itunidad->mode="autohide";
				
		$edit->itdescrip = new inputField("(<#o#>) Descripcion", "descrip_<#i#>");
		$edit->itdescrip->size     =15;
		$edit->itdescrip->db_name='descrip';
		$edit->itdescrip->rel_id ='itrequi';
		$edit->itdescrip->readonly = true;
		//$edit->itdescrip->mode="autohide";

		$edit->itcantidad = new inputField("(<#o#>) Cantidad", "cantidad_<#i#>");
		$edit->itcantidad->db_name  ='cantidad';
		$edit->itcantidad->rel_id   ='itrequi';
		$edit->itcantidad->size     =8;
		$edit->itcantidad->readonly = true;
		//$edit->itcantidad->mode="autohide";

		$edit->itprecio = new inputField("(<#o#>) Precio", "precio_<#i#>");
		$edit->itprecio->db_name  ='precio';
		$edit->itprecio->rel_id   ='itrequi';
		$edit->itprecio->size     =8;
		$edit->itprecio->readonly = true;
		//$edit->itprecio->mode="autohide";

		$edit->itimporte = new inputField("(<#o#>) Importe", "importe_<#i#>");
		$edit->itimporte->db_name  = 'importe';
		$edit->itimporte->rel_id   = 'itrequi';
		$edit->itimporte->size     = 8;
		$edit->itimporte->readonly = true;
		//$edit->itimporte->mode     = "autohide";
		
		$edit->itpartida = new inputField("(<#o#>) Partida", "partida_<#i#>");
		$edit->itpartida->rule     = 'callback_itpartida';
		$edit->itpartida->db_name  = 'partida';
		$edit->itpartida->rel_id   = 'itrequi';
		//$edit->itpartida->mode     = 'autohide';
		$edit->itpartida->size     = 15;
		//$edit->itpartida->append($btn);
		$edit->itpartida->append('<img src="/tortuga/assets/default/images/system-search.png"  alt="Busqueda de partidas" title="Busqueda de partidas" border="0" onclick="modbusdepen(<#i#>)"/>');
		
		//Termina el detalle
		
		$status=$edit->get_from_dataobjetct('status');
		if($status != 'X')
			$edit->buttons("modify", "save", "undo", "delete", "back");
		else
		$edit->buttons("undo","back"); 
		
		$edit->build();

		$smenu['link']=barra_menu('116');
		$data['smenu'] = $this->load->view('view_sub_menu', $smenu,true);
		$conten["form"]  =&  $edit;
		$data['content'] = $this->load->view('view_asig_part', $conten,true); 
		//$data['content'] = $edit->output;
		$data['title']   = " Clasificaci&oacute;n ";
		$data["head"]    = $this->rapyd->get_head().script('jquery.js').script("plugins/jquery.numeric.pack.js");
		$this->load->view('view_ventanas', $data);
	}
	
	function getadmin(){
		$this->rapyd->load("fields");
		$uadministra = new dropdownField("Unidad Administrativa", "uadministra");
		$uadministra->status = "modify";
		$uadministra->option("","Ninguna");
		$ueje=$this->input->post('uejecuta');
		if($ueje===false || empty($ueje)){
			$uadministra->option("","Seleccione una unidad ejecutora primero");
		}else{
			$uadministra->options("SELECT codigo,nombre FROM uadministra WHERE codigoejec='$ueje' ORDER BY nombre");
		}
		$uadministra->build();
		echo $uadministra->output; 
	}
		
	function convertir($numero){
		$this->rapyd->load('dataobject');
		
		$requi = new DataObject("requi");
		$requi->rel_one_to_many('itrequi', 'itrequi', array('numero'=>'numero'));
		$requi->load($numero);
		
		$ocompra=$requi->get('ocompra');
		$do = new DataObject("ocompra");
		$do->rel_one_to_many('itocompra', 'itocompra', array('numero'=>'numero'));
		
		if(!empty($ocompra))redirect("presupuesto/ocompra/dataedit/show/$ocompra");;
		$do->set('uejecutora'      ,$requi->get('uejecuta'    ));
		$do->set('observa'         ,$requi->get('objetivo'       ));
		//$do->set('responsable'     ,$requi->get('responsable'   ));
		$iva=$this->datasis->dameval("SELECT tasa FROM civa ORDER BY fecha DESC LIMIT 1");
		for($i=0;$i < $requi->count_rel('itrequi');$i++){
			
			$do->set_rel('itocompra','cantidad'   ,$requi->get_rel('itrequi','cantidad'   ,$i),$i);
			$do->set_rel('itocompra','unidad'     ,$requi->get_rel('itrequi','unidad'     ,$i),$i);
			$do->set_rel('itocompra','descripcion',$requi->get_rel('itrequi','descrip'    ,$i),$i);
			$do->set_rel('itocompra','precio'     ,$requi->get_rel('itrequi','precio'     ,$i),$i);
			$do->set_rel('itocompra','importe'    ,$requi->get_rel('itrequi','importe'    ,$i),$i);
			$do->set_rel('itocompra','partida'    ,$requi->get_rel('itrequi','partida'    ,$i),$i);
			$do->set_rel('itocompra','iva'        ,$iva                                       ,$i);
		}
		$do->set('estadmin',$requi->get('estadmin'));
		$do->set('fondo'   ,$requi->get('fondo'   ));
		$do->set('fecha',date('Ymd'));
		$do->save();
		$numero=$do->get('numero');
		$requi->set('ocompra',$numero);
		$requi->set('status','X');
		$requi->save();
		
		redirect("presupuesto/ocompra/dataedit/modify/$numero");
	}
	
	function prueba($id){
		$this->rapyd->load('dataobject');
		
		//$this->db->query("lock tables `requi` write");
		//$this->db->query("lock tables `itrequi` write");
		//$row = $this->datasis->damerow("SELECT '',numero,fecha,uejecuta,uadministra,responsable,objetivo,tcantidad,timporte,'p',ocompra,estadmin,fondo FROM requi WHERE id=$id");
		//echo implode($row,"','");
		//$this->db->query("INSERT INTO requi SELECT '',numero,fecha,uejecuta,uadministra,responsable,objetivo,tcantidad,timporte,'p',ocompra,estadmin,fondo FROM requi WHERE id=$id");
		//$last_id = $this->db->insert_id();
		//$this->db->query("INSERT INTO itrequi SELECT '' id,$last_id,cantidad,unidad,descrip,precio,importe,partida FROM itrequi WHERE id_comp=$id");
		//$this->db->query("unlock tables");
		
		$requi = new DataObject("requi");
		$requi->rel_one_to_many('itrequi', 'itrequi', array('numero'=>'numero'));
		$requi->load($numero);
		
		$r    = new DataObject("requi");
		$r = $requi;
		print_r($r);
		//$r->pk=array('id'=>'');
		//$r->loaded=0;
		//$r->set('id','');
		//for($i=0;$i < $requi->count_rel('itrequi');$i++){
		//	$r->set_rel('itrequi','id','',$i);			
		//	$r->set_rel('itrequi','id_comp','',$i);			
		//}
		//$r->save();
	}
	
	function _post_insert($do){
		$numero = $do->get('numero');
		logusu('requisiciones',"Creo requisicion Nro $numero");
		//redirect($this->url."actualizar/$numero");
	}
	
	function _post_update($do){
		$numero = $do->get('numero');
		logusu('requisiciones'," Modifico requisicion Nro $numero");
		//redirect($this->url."actualizar/$numero");
	}
	function _post_delete($do){
		$numero = $do->get('numero');
		logusu('requisiciones'," Elimino requisicion Nro $numero");
	}
}
?>
