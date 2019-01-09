<?php
require_once(BASEPATH.'application/controllers/presupuesto/common.php');
class Opagof extends Common {
	var $titp='Ordenes de Pago ';
	var $tits='Orden de Pago ';
	var $url ='presupuesto/opagof/';

	function Opagof(){
		parent::Controller();
		$this->load->library("rapyd");
	}

	function index(){
		redirect($this->url."filteredgrid");
	}

	function filteredgrid(){
		$this->rapyd->load("datafilter2","datagrid");
		//$this->rapyd->uri->keep_persistence();

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

		$filter = new DataFilter2("");
		$filter->db->select(array("b.ocompra","a.reverso","b.codigoadm","b.fondo","b.partida","b.ordinal","a.numero","a.fecha","a.tipo","a.compra","a.uejecutora","a.estadmin","a.fondo","a.cod_prov","a.nombre","a.beneficiario","a.pago","a.total2","a.status","MID(a.observa,1,50) observa","c.nombre nombre2"));
		$filter->db->from("odirect a");
		$filter->db->join("itodirect b" ,"a.numero=b.numero","LEFT");
		$filter->db->join("sprv c"      ,"c.proveed =a.cod_prov","LEFT");
		$filter->db->groupby("a.numero");

		$filter->numero = new inputField("N&uacute;mero", "numero");
		$filter->numero->size=12;
		//$filter->numero->clause="likerigth";
		$filter->numero->db_name = 'a.numero';
		
		$filter->ocompra = new inputField("Compromiso Ref", "ocompra");
		$filter->ocompra->size=12;
		$filter->ocompra->db_name = 'b.ocompra';

		$filter->tipo = new dropdownField("Orden de ", "a.tipo");
		$filter->tipo->option("","");
		$filter->tipo->option("Compra"  ,"Compra");
		$filter->tipo->option("Servicio","Servicio");
		$filter->tipo->style="width:100px;";
		$filter->tipo->db_name = 'a.tipo';

		$filter->fecha = new dateonlyField("Fecha", "fecha");
		$filter->fecha->size=12;
		$filter->fecha->db_name = 'a.fecha';
		$filter->fecha->dbformat='Y-m-d';

		$filter->cod_prov = new inputField("Beneficiario", 'cod_prov');
		$filter->cod_prov->size = 6;
		$filter->cod_prov->append($bSPRV);
		$filter->cod_prov->db_name = 'a.cod_prov';
		$filter->cod_prov->clause   = 'where';
		$filter->cod_prov->operator = '=';

		$filter->codigo = new inputField("Codigo Presupuestario", "codigo");
		$filter->codigo->db_name = 'CONCAT(b.codigoadm,".",b.partida)';

		$filter->fondo = new dropdownField("Fondo", "fondo");
		$filter->fondo->option("","");
		$filter->fondo->options("SELECT fondo,CONCAT_WS(' ',fondo,descrip)a FROM fondo");
		$filter->fondo->db_name = 'b.fondo';

		$filter->observa = new inputField("Observacion", "observa");
		$filter->observa->size=20;
		$filter->observa->db_name='a.observa';

		$filter->total2 = new inputField("Monto", "total2");
		$filter->total2->size=20;

		$filter->tipo = new dropdownField("Tipo O.P.","tipo");
		$filter->tipo->option("","");
		$filter->tipo->option("M","O.P Sin Imputacion");
		$filter->tipo->option("C","O.P General");
		$filter->tipo->option("B","O.P Directo");
		$filter->tipo->option("K","O.P Nomina");
		$filter->tipo->style="width:150px";
		$filter->tipo->db_name = 'MID(a.status,1,1)';

		$filter->status = new dropdownField("Estado","status");
		$filter->status->option("","");
		$filter->status->option("2","Causado");
		$filter->status->option("1","Por Causar");
		$filter->status->option("3","Pagado");
		$filter->status->option("A","Anulado");
		$filter->status->style="width:150px";
		$filter->status->db_name = 'MID(a.status,2,1)';

		$filter->buttons("reset","search");

		$filter->build();
		$uri = anchor($this->url.'dataedit/show/<#numero#>','<#numero#>');
		$uri_2 = anchor($this->url.'dataedit/create/<#numero#>','Duplicar');

		function sta($status){
			switch(substr($status,1,1)){
				case "1":return "Por Causar";break;
				case "2":return "Causado";break;
				case "3":return "Pagado";break;
				case "A":return "Anulado";break;
			}
		}

		function tipo($tipo){
			switch($tipo){
				case "C":return "Contrato";break;
				case "T":return "Transferencia";break;
				case "N":return "N&oacute;mina";break;
			}
		}
		
		function select_url($status,$numero){
			$atts2 = array(
			'width'     =>'1024',
			'height'    =>'768',
			'scrollbars'=>'yes',
			'status'    =>'yes',
			'resizable' =>'yes',
			'screenx'   =>'0',
			'screeny'   =>'0' );
			switch(substr($status,0,1)){
				case "M":return anchor_popup('presupuesto/pagomonetario/dataedit/opagof/show/'.$numero  ,$numero,$atts2);break;
				case "C":return anchor_popup('presupuesto/opagoc/dataedit/opagof/show/'.$numero         ,$numero,$atts2);break;
				case "K":return anchor_popup('presupuesto/pagonom/dataedit/opagof/show/'.$numero        ,$numero,$atts2);break;
				case "B":return anchor_popup('presupuesto/odirect/dataedit/opagof/show/'.$numero        ,$numero,$atts2);break;
				case "F":return anchor_popup('presupuesto/opago/dataedit/opagof/show/'.$numero          ,$numero,$atts2);break;
			}	
				
		}

		$grid = new DataGrid($this->titp);
		$grid->order_by("numero","desc");
		$grid->per_page = 20;
		$grid->use_function('substr','str_pad','sta','tipo','select_url');

		$grid->column_orderby("N&uacute;mero"    ,"<select_url><#status#>|<#numero#></select_url>" ,"numero"                            );
		$grid->column_orderby("Tipo"             ,"tipo"                                           ,"tipo"           ,"align='center'"  );
		$grid->column_orderby("Fecha"            ,"fecha"                                          ,"fecha"          ,"align='center'"  );
		$grid->column_orderby("Compromiso Ref"   ,"ocompra"                                        ,"ocompra"        ,"NOWRAP"          );
		$grid->column_orderby("Est. Adm"         ,"codigoadm"                                      ,"estamdin"       ,"NOWRAP"          );
		$grid->column_orderby("Fondo"            ,"fondo"                                          ,"fondo"          ,"NOWRAP"          );
		$grid->column_orderby("Partida"          ,"partida"                                        ,"partida"        ,"NOWRAP"          );
		$grid->column_orderby("Beneficiario"     ,"nombre2"                                        ,"c.nombre"                          );//,"NOWRAP"
		$grid->column_orderby("Observacion"      ,"observa"                                        ,"observa"               );//,"NOWRAP"
		$grid->column_orderby("Monto"            ,"total2"                                         ,"total2"         ,"align='right'"   );
		$grid->column_orderby("Estado"           ,"<sta><#status#></sta>"                          ,"status"         ,"align='center' " );//NOWRAP

		$action = "javascript:window.open('" .site_url('presupuesto/opagoc/selectoc/'). "','_blank','width=1024, height=768,scrollbars=yes,status=yes,resizable=yes,screenx=0,screeny=0')";
		$grid->button("add_opagoc","Orden de Pago",$action,"TR");
		$action = "javascript:window.open('" .site_url('presupuesto/odirect/dataedit/create'). "','_blank','width=1024, height=768,scrollbars=yes,status=yes,resizable=yes,screenx=0,screeny=0')";
		$grid->button("add_odirect","O.P. Directo",$action,"TR");
		$action = "javascript:window.open('" .site_url('presupuesto/pagomonetario/dataedit/create'). "','_blank','width=1024, height=768,scrollbars=yes,status=yes,resizable=yes,screenx=0,screeny=0')";
		$grid->button("add_opsinimpu","O.P. Sin Imputacion",$action,"TR");	
		$action = "javascript:window.open('" .site_url('presupuesto/pagonom/'). "','_blank','width=1024, height=768,scrollbars=yes,status=yes,resizable=yes,screenx=0,screeny=0')";
		$grid->button("add_pagonom","O.P. Nomina",$action,"TR");
		
		$grid->build();
//		echo $grid->db->last_query();

		$data['filtro']  = $filter->output;
		$data['content'] = $grid->output;
		$data['script'] = script("jquery.js")."\n";
		$data['title']   = "";//" $this->titp ";
		//$data['content'] = $filter->output.$grid->output;
		$data['title']   = "$this->titp";
		$data["head"]    = $this->rapyd->get_head();
		$this->load->view('view_ventanas', $data);
	}
	
	
	
}
