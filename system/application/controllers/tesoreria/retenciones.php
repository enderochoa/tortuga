<?php
class retenciones extends Controller {

	var $titp='Retenciones';
	var $tits='Retenciones';
	var $url='tesoreria/retenciones/';

	function retenciones(){
		parent::Controller();
		$this->load->library("rapyd");
		$this->formatopres =$this->datasis->traevalor('FORMATOPRES');
		$this->flongpres   =strlen(trim($this->formatopres));
	//	$this->datasis->modulo_id(209,1);
	}

	function index(){
		redirect($this->url."filteredgrid");
	}

	function filteredgrid($desem){
		//$this->datasis->modulo_id(101,1);
		$this->rapyd->load("datafilter","datagrid");

		$mSPRV=array(
				'tabla'   =>'sprv',
				'columnas'=>array(
				'proveed' =>'C&oacute;odigo',
				'nombre'=>'Nombre',
				'contacto'=>'Contacto'),
				'filtro'  =>array('proveed'=>'C&oacute;digo','nomb_prov'=>''),
				'retornar'=>array('proveed'=>'cod_prov' ),
				'titulo'  =>'Buscar Beneficiario');
    
		$bSPRV=$this->datasis->p_modbus($mSPRV,"proveed");
    

		$filter = new DataFilter("","v_retenciones");
		
		$filter->desembolso = new inputField("Desembolso", "desembolso");
		$filter->desembolso->size  =10;
		
		if(!empty($mbanc))
			$filter->db->where("desembolso",$mbanc);
	
		$filter->build();
		
		$riva   = anchor_popup('formatos/ver/RIVA/<#nrocomp#>','<number_format><#reteiva#>|2|,|.</number_format>');
		$timbre = anchor_popup('formatos/ver/IMPTIMBRE/<#ordpago#>','<number_format><#imptimbre#>|2|,|.</number_format>');
		$municip= anchor_popup('formatos/ver/IMPMUNICIP/<#ordpago#>','<number_format><#impmunicipal#>|2|,|.</number_format>');
		$islr   = anchor_popup('formatos/ver/ISLR3/<#desembolso#>/<#ordpago#>/<#ordcompra#>','<number_format><#reten#>|2|,|.</number_format>');
		///
		function blanco($num){
			if(empty($num)||$num==0){
			 return '';
			}else{
				return number_format($num,2,',','.');
			}
		}
  
		$grid = new DataGrid("");
		$grid->order_by("desembolso","desc");
		$grid->per_page = 30;
		$grid->use_function('substr','str_pad','blanco');
		//$grid->use_function('sta'); $grid->column("Saldo" ,"<blanco><#saldo#></blanco>"   ,"align=right");
  
		 $grid->column("Desembolso"        ,"<str_pad><#desembolso#>|8|0|STR_PAD_LEFT</str_pad>");
		 $grid->column("O.Pago"            ,"<str_pad><#ordpago#>|8|0|STR_PAD_LEFT</str_pad>");
		 $grid->column("O.Compra"          ,"<str_pad><#ordcompra#>|8|0|STR_PAD_LEFT</str_pad>");
		 $grid->column("Beneficiario"         ,"cod_prov"    );
		 $grid->column("Factura"           ,"factura"     );
		 $grid->column("Control Fiscal"    ,"controlfac"  );
		 $grid->column("F.Factura"         ,"fechafac"    );
		 $grid->column("M. Total"         ,"<number_format><#total2#>|2|,|.</number_format>"       ,"align='right'"  );
		 $grid->column("B. Imponible"     ,"<number_format><#subtotal#>|2|,|.</number_format>"       ,"align='right'");
		 $grid->column("Comprobante"       ,"nrocomp"                                              ,"align='right'"  );
		 $grid->column("R.IVA"             ,$riva                                                  ,"align='right'"  );
		 $grid->column("ISLR"              ,$islr                                                  ,"align='right'"  );
		 $grid->column("I.Municipal"       ,$municip                                               ,"align='right'"  );
		 $grid->column("I 1X1000"          ,$timbre                                                ,"align='right'"  );
		 $grid->column("M.Pagar"           ,"<number_format><#total#>|2|,|.</number_format>"       ,"align='right'"  );  
		 
  	
		//$grid->column("Fecha"            ,"<dbdate_to_human><#fecha#></dbdate_to_human>","align='center'");
		
//		$grid->column("Estado"           ,"<sta><#status#></sta>"                         ,"align='center'");
  
		//echo $grid->db->last_query();

		$grid->build();
//echo $grid->db->last_query();
		$data['content'] = $filter->output.$grid->output;
		$data['title']   = "$this->titp";
		$data["head"]    = $this->rapyd->get_head();
		$this->load->view('view_ventanas', $data);
	}
	
	function filteredgrid2(){
		//$this->datasis->modulo_id(101,1);
		$this->rapyd->load("datafilter","datagrid");

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
    
		$filter = new DataFilter("","v_retenciones");	
				
		$filter->pago = new inputField("Orden de Pago", "pago");
		$filter->pago->size  =10;
		
		$filter->compra = new inputField("Orden Compra", "ocompra");
		$filter->compra->size  =10;
		
		$filter->reinte = new inputField("Rendici�n", "rendi");
		$filter->reinte->size  =10;
	      
		$filter->cod_prov = new inputField("Beneficiario", 'cod_prov');
		$filter->cod_prov->size = 6;
		$filter->cod_prov->append($bSPRV);
			
		$filter->buttons("reset","search");

		$filter->build();
		
		$riva   = anchor_popup('formatos/ver/RIVAM/<#pago#>/<#rendi#>' ,'<nformat><#reteiva#></nformat>'     );
		$timbre = anchor_popup('formatos/ver/IMPTIMBRE/<#pago#>'       ,'<nformat><#imptimbre#></normat>'    );
		$municip= anchor_popup('formatos/ver/IMPMUNICIP/<#pago#>'      ,'<nformat><#impmunicipal#></nformat>');
		$crs    = anchor_popup('formatos/ver/IMPCRS/<#pago#>'          ,'<nformat><#crs#></nformat>'         );
		$islr   = anchor_popup('formatos/ver/ISLRM/<#pago#>/<#rendi#>' ,'<nformat><#reten#></nformat>'       );
		///
		function blanco($num){
			if($num > 0){
			 	return str_pad($num,8,'0',STR_PAD_LEFT);
			}else{
				return '';
			}
		}
  
		$grid = new DataGrid("");

		
		$grid->order_by("pago","desc");
		$grid->per_page = 30;
		$grid->use_function('substr','str_pad','blanco');
		//$grid->use_function('sta'); $grid->column("Saldo" ,"<blanco><#saldo#></blanco>"   ,"align=right");
  
	 $grid->column_orderby("O.Pago"            ,"<blanco><#pago#></blanco>"      ,"pago"      );
	 $grid->column_orderby("O.Compra"          ,"<blanco><#ocompra#></blanco>"   ,"ocompra"   );
	 $grid->column_orderby("Rendicion"         ,"<blanco><#rendi#></blanco>"     ,"rendi"     );
	 $grid->column_orderby("Beneficiario"      ,"nombre"                         ,"nombre"    ,"align='left'NOWRAP");
	 $grid->column_orderby("Factura"           ,"factura"                        ,"factura"   );
	 $grid->column_orderby("Control Fiscal"    ,"controlfac"                     ,"controlfac");
	 $grid->column_orderby("F.Factura"         ,"fechafac"                       ,"fechafac"  );
	 $grid->column_orderby("R.IVA"             ,$riva                            ,"pago"      ,"align='right'");
	 $grid->column_orderby("ISLR"              ,$islr                            ,"pago"      ,"align='right'");
	 $grid->column_orderby("I.Municipal"       ,$municip                         ,"pago"      ,"align='right'");
	 $grid->column_orderby("I 1X1000"          ,$timbre                          ,"pago"      ,"align='right'");
	 $grid->column_orderby("I CRS"             ,$crs                             ,"pago"      ,"align='right'");

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
	
	function filteredgrid3(){
		//$this->datasis->modulo_id(101,1);
		$this->rapyd->load("datafilter","datagrid");

		$mSPRV=array(
				'tabla'   =>'sprv',
				'columnas'=>array(
				'proveed' =>'C&oacute;odigo',
				'nombre'  =>'Nombre',
				'rif'     =>'rif'),
				'filtro'  =>array('proveed'=>'C&oacute;digo','nombre'=>'nombre','rif'=>'rif'),
				'retornar'=>array('proveed'=>'cod_prov' ),
				'titulo'  =>'Buscar Beneficiario');
    
		$bSPRV=$this->datasis->p_modbus($mSPRV,"proveed");
    
		$filter = new DataFilter("","v_localizador");	
			
		$filter->desembolso = new inputField("Desembolso", "desembolso");
		$filter->desembolso->size  =10;
		
		$filter->mstatus = new dropdownField("Estado Desembolso","mstatus");
		$filter->mstatus->option("","");
		$filter->mstatus->option("E1","Sin Pagar");
		$filter->mstatus->option("E2","Pagado");
		$filter->mstatus->style="width:150px";
		$filter->mstatus->in   = "desembolso";
		
		$filter->ordpago = new inputField("Orden de Pago", "ordpago");
		$filter->ordpago->size  =10;
		
		$filter->pstatus = new dropdownField("Estado Orden de Pago","pstatus");
		$filter->pstatus->option("","");
		$filter->pstatus->option("B1","PD-Pago Directo  *Sin Ordenar Pago"                   );
		$filter->pstatus->option("B2","PD-Pago Directo  *Ordenado Pago"                      );
		$filter->pstatus->option("B3","PD-Pago Directo  *Pagado"                             );
		$filter->pstatus->option("F1","PC-Pago O.Compras*Sin Ordenar Pago"                   );
		$filter->pstatus->option("F2","PC-Pago O.Compras*Ordenado Pago"                      );
		$filter->pstatus->option("F3","PC-Pago O.Compras*Pagado"                             );
		$filter->pstatus->option("N1","PN-Pago Obras    *Sin Ordenar Pago"                   );
		$filter->pstatus->option("N2","PN-Pago Obras    *Ordenado Pago"                      );
		$filter->pstatus->option("N3","PN-Pago Obras    *Pagado"                             );
		$filter->pstatus->option("I1","RI-Pago IVA      *Sin Ordenar Pago"                   );
		$filter->pstatus->option("I2","RI-Pago IVA      *Ordenado Pago"                      );
		$filter->pstatus->option("I3","RI-Pago IVA      *Pagado"                             );
		$filter->pstatus->option("S1","RR-Pago ISLR     *Sin Ordenar Pago"                   );
		$filter->pstatus->option("S2","RR-Pago ISLR     *Ordenado Pago"                      );
		$filter->pstatus->option("S3","RR-Pago ISLR     *Pagado"                             );		
		$filter->pstatus->option("G1","FA-Pago Fondo Avance *Sin Ordenar Pago"                   );
		$filter->pstatus->option("G2","FA-Pago Fondo Avance *Ordenado Pago"                      );
		$filter->pstatus->option("G3","FA-Pago Fondo Avance *Pagado"                             );
		$filter->pstatus->style="width:300px";
		$filter->pstatus->in   = "ordpago";
		
		$filter->ordcompra = new inputField("Orden Compra", "ordcompra");
		$filter->ordcompra->size  =10;
		
		$filter->ostatus = new dropdownField("Estado Orden Compra","status");
		$filter->ostatus->option("","");
		$filter->ostatus->option("P","Sin Comprometer" );
		$filter->ostatus->option("C","Comprometido"    );
		$filter->ostatus->option("T","Causado"         );
		$filter->ostatus->option("O","Ordenado Pago"   );
		$filter->ostatus->option("E","Pagado"          );		
		$filter->ostatus->style="width:150px";
		$filter->ostatus->in   = "ordcompra";
		
		$filter->nrocomp = new inputField("Nro Comprobante IVA", "nrocomp");
		$filter->nrocomp->size  =10;
		
		$filter->rstatus = new dropdownField("Estado Desembolso","rstatus");
		$filter->rstatus->option("","");
		$filter->rstatus->option("C","Pagado Al tesoro"          );
		$filter->rstatus->option("B","Por Pagar Al Tesoro"       );
		$filter->rstatus->option("A","Sin Ordenar Pago Al Tesoro");
		$filter->rstatus->style="width:300px";
		$filter->rstatus->in   = "nrocomp";
		
		$filter->factura = new inputField("Factura", "factura");
		$filter->factura->size  =10;
		
		$filter->controlfac = new inputField("Control Fiscal", "controlfac");
		$filter->controlfac->size  =10;
	      
		$filter->cod_prov = new inputField("Beneficiario", 'cod_prov');
		$filter->cod_prov->size = 6;
		$filter->cod_prov->append($bSPRV);
		
		
		$filter->buttons("reset","search");

		$filter->build();
		
		$riva = anchor_popup('formatos/ver/RIVA/<#nrocomp#>','<number_format><#reteiva#>|2|,|.</number_format>');
		$timbre = anchor_popup('formatos/ver/IMPTIMBRE/<#ordpago#>','<number_format><#imptimbre#>|2|,|.</number_format>');
		$municip= anchor_popup('formatos/ver/IMPMUNICIP/<#ordpago#>','<number_format><#impmunicipal#>|2|,|.</number_format>');
		$islr = anchor_popup('formatos/ver/ISLR3/<#desembolso#>/<#ordpago#>/<#ordcompra#>','<number_format><#reten#>|2|,|.</number_format>');
		///
		function blanco($num){
			if(empty($num)||$num==0){
			 return '';
			}else{
				return number_format($num,2,',','.');
			}
		}
		
		function msta($status){
			switch($status){
				case "E1":return "Sin pagar";break;
				case "E2":return "Pagado";break;
			}
		}
		
		function psta($status){
			switch($status){
				case "B1":return "PD-Sin Ordenar Pago";break;
				case "B2":return "PD-Ordenado Pago"   ;break;
				case "B3":return "PD-Pagado"          ;break;
				case "F1":return "PC-Sin Ordenar Pago";break;
				case "F2":return "PC-Ordenado Pago"   ;break;
				case "F3":return "PC-Pagado"          ;break;
				case "N1":return "PN-Sin Ordenar Pago";break;
				case "N2":return "PN-Ordenado Pago"   ;break;
				case "N3":return "PN-Pagado"          ;break;
				case "I1":return "RI-Sin Ordenar Pago";break;
				case "I2":return "RI-Ordenado Pago"   ;break;
				case "I3":return "RI-Pagado"          ;break;
				case "S1":return "RR-Sin Ordenar Pago";break;
				case "S2":return "RR-Ordenado Pago"   ;break;
				case "S3":return "RR-Pagado"          ;break;
				case "G1":return "FA-Sin Ordenar Pago";break;
				case "G2":return "FA-Ordenado Pago"   ;break;
				case "G3":return "FA-Pagado"          ;break;
			}
		}
		
		function osta($status){
			switch($status){
				case "P":return "Sin Comprometer";break;
				case "C":return "Comprometido"   ;break;
				case "T":return "Causado"        ;break;
				case "O":return "Ordenado Pago"  ;break;
				case "E":return "Pagado"         ;break;
			}
		}
		
		function rsta($status){
			switch($status){
				case "C":return "Pagado Al tesoro"          ;break;
				case "B":return "Por Pagar Al Tesoro"       ;break;
				case "A":return "Sin Ordenar Pago Al Tesoro";break;
			}
		}
		
		$grid = new DataGrid("");

		
		$grid->order_by("desembolso","desc");
		$grid->per_page = 200;
		$grid->use_function('substr','str_pad','blanco','msta','psta','osta','rsta');
		//$grid->use_function('sta'); $grid->column("Saldo" ,"<blanco><#saldo#></blanco>"   ,"align=right");
  
		$grid->column_orderby("Desembolso"        ,"<#desembolso#>","desembolso");
		$grid->column_orderby("Estado"            ,"<msta><#mstatus#></msta>"                       ,"mstatus"       ,"align='left  'NOWRAP");
		$grid->column_orderby("O.Pago"            ,"<#ordpago#>"                                    ,"ordpago"       ,"align='center'NOWRAP");
		$grid->column_orderby("M. Factura"        ,"itfac"                                          ,"itfac"         ,"align='center'NOWRAP");
		$grid->column_orderby("Estado"            ,"<psta><#pstatus#></psta>"                       ,"pstatus"       ,"align='left  'NOWRAP");
		$grid->column_orderby("O.Compra"          ,"ordcompra"                                      ,"ordcompra"     ,"align='center'NOWRAP");
		$grid->column_orderby("Estado"            ,"<osta><#ostatus#></osta>"                       ,"ostatus"       ,"align='left  'NOWRAP");
		$grid->column_orderby("Beneficiario"      ,"cod_prov"                                       ,"cod_prov"      ,"align='left  'NOWRAP");
		$grid->column_orderby("Factura"           ,"factura"                                        ,"factura"       ,"align='center'NOWRAP");
		$grid->column_orderby("Control Fiscal"    ,"controlfac"                                     ,"controlfac"    ,"align='center'NOWRAP");
		$grid->column_orderby("F.Factura"         ,"<dbdate_to_humam><#fechafac#></dbdate_to_humam>","fechafac"      ,"align='center'NOWRAP");	 
		$grid->column_orderby("M.Pagar"           ,"<nformat><#total#></nformat>"                   ,"total"         ,"align='right'"       );//    	
		$grid->column_orderby("M. Total"          ,"<nformat><#total2#></nformat>"                  ,"total2"        ,"align='right'"       );//
		//$grid->column_orderby("F. Firma"          ,"<dbdate_to_human><#ffirma#></dbdate_to_human>"  ,"ffirma"        ,"align='center'" );
		//$grid->column_orderby("F. Entrega"        ,"<dbdate_to_human><#fentrega#></dbdate_to_human>","fentrega"      ,"align='center'" );
		
		//$grid->column("Fecha"            ,"<dbdate_to_human><#fecha#></dbdate_to_human>","align='center'");
		//$grid->column_orderby("Comprobante"       ,"nrocomp"                                              );//
		//$grid->column("Estado"            ,"<rsta><#rstatus#></rsta>"                                     );//	
		//		$grid->column("Estado"           ,"<sta><#status#></sta>"                         ,"align='center'");

		//echo $grid->db->last_query();

		$grid->build();
		//echo $grid->db->last_query();
		//$data['content'] = $filter->output.$grid->output;
		$data['filtro']  = $filter->output;
		$data['content'] = $grid->output;
		$data['script'] = script("jquery.js")."\n";
		$data['title']   = "Localizador";
		$data["head"]    = $this->rapyd->get_head();
		$this->load->view('view_ventanas', $data);
	}

	function filtrorete(){
		//$this->datasis->modulo_id(101,1);
		$this->rapyd->load("datafilter","datagrid");

		$mSPRV=array(
				'tabla'   =>'sprv',
				'columnas'=>array(
				'proveed' =>'C&oacute;odigo',
				'nombre'=>'Nombre',
				'contacto'=>'Contacto'),
				'filtro'  =>array('proveed'=>'C&oacute;digo','nomb_prov'=>''),
				'retornar'=>array('proveed'=>'cod_prov' ),
				'titulo'  =>'Buscar Beneficiario');

		$bSPRV=$this->datasis->p_modbus($mSPRV,"proveed");

		$filter = new DataFilter("");
		$filter->db->select(array("b.pago pago", "a.numero compra","c.nombre","a.reteiva","a.reten","a.imptimbre","a.factura","a.controlfac","a.fechafac"));
		$filter->db->from("pacom b"                      );
		$filter->db->join("ocompra a","a.numero=b.compra");
		$filter->db->join("sprv c","c.proveed=a.cod_prov");
		$filter->db->where("a.status = 'O'  ");
		
		
		$filter->pago = new inputField("Orden de Pago", "pago");
		$filter->pago->size   = 10;
		$filter->pago->db_name= "b.pago";
		
		$filter->compra = new inputField("Orden de Compra", "compra");
		$filter->compra->size  =10;
		$filter->compra->db_name= "a.numero";
		
		$filter->buttons("reset","search");
		
		$filter->build();
		
		//$riva   = anchor_popup('formatos/ver/RIVA/<#nrocomp#>','<number_format><#reteiva#>|2|,|.</number_format>');
		//$timbre = anchor_popup('formatos/ver/IMPTIMBRE/<#ordpago#>','<number_format><#imptimbre#>|2|,|.</number_format>');
		//$municip= anchor_popup('formatos/ver/IMPMUNICIP/<#ordpago#>','<number_format><#impmunicipal#>|2|,|.</number_format>');
		$uri   = anchor('presupuesto/causacion/dataedit/modify/<#compra#>','<#compra#>');
		///
		function blanco($num){
			if(empty($num)||$num==0){
			 return '';
			}else{
				return number_format($num,2,',','.');
			}
		}
  
		$grid = new DataGrid("");
		$grid->order_by("b.pago","desc");
		$grid->per_page = 30;
		$grid->use_function('substr','str_pad','blanco');
		//$grid->use_function('sta'); $grid->column("Saldo" ,"<blanco><#saldo#></blanco>"   ,"align=right");
  
		 $grid->column_orderby("O.Pago"            ,"<#pago#>"                                        , "pago"       );
		 $grid->column_orderby("O.Compra"          ,$uri                                              , "compra"     );
		 $grid->column_orderby("R. IVA"            ,"reteiva"                                         , "reteiva"    );
		 $grid->column_orderby("R. ISLR"           ,"reten"                                           , "reten"      );
		 $grid->column_orderby("R 1X1000"          ,"imptimbre"                                       , "imptimbre"  );
		 $grid->column_orderby("Beneficiario"      ,"nombre"                                          , "nombre"     );
		 $grid->column_orderby("Factura"           ,"factura"                                         , "factura"    );
		 $grid->column_orderby("Control Fiscal"    ,"controlfac"                                      , "controlfac" );
		 $grid->column_orderby("F.Factura"         ,"fechafac"                                        , "fechafac"   );

		$grid->build();
		$grid->db->last_query();
		$data['content'] = $filter->output.$grid->output;
		$data['title']   = "$this->titp";
		$data["head"]    = $this->rapyd->get_head();
		$this->load->view('view_ventanas', $data);
	}

}