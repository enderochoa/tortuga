<?php
require_once(BASEPATH.'application/controllers/presupuesto/common.php');
class Ocompra extends Common {

	var $url   ="presupuesto/ocompra/";
	var $tits  ="Orden de ";
	var $titp  ="Ordenes de ";
	var $ptipos=array();

	function Ocompra(){
		parent::Controller();
		$this->load->library("rapyd");
		$this->formatopres=$this->datasis->traevalor('FORMATOPRES');
		$this->flongpres  =strlen(trim($this->formatopres));
		$this->datasis->modulo_id(70,1);
		
	}

	function index(){
		$this->db->simple_query("ALTER TABLE `itocompra` CHANGE COLUMN `partida` `partida` VARCHAR(25) NULL DEFAULT NULL COMMENT 'Partida Presupuestaria'  ;");
		redirect("presupuesto/ocompra/filteredgrid/search");
	}

	function filteredgrid(){

		$this->rapyd->load("datafilter2","datagrid");
		//$this->rapyd->uri->keep_persistence();

		$mSPRV=array(
				'tabla'   =>'sprv',
				'columnas'=>array(
				'proveed' =>'C&oacute;odigo',
				'nombre'=>'Nombre',
				'rif'     =>'RIF',
				'contacto'=>'Contacto'),
				'filtro'  =>array('proveed'=>'C&oacute;digo','nombre'=>'Nombre'),
				'retornar'=>array('proveed'=>'cod_prov' ),
				'titulo'  =>'Buscar Beneficiario');

		$bSPRV=$this->datasis->p_modbus($mSPRV,"proveed");

		$filter = new DataFilter2("");

		$filter->db->select("d.estado,a.compromiso,b.codigoadm,b.fondo,b.partida,a.numero numero,a.fecha fecha,a.tipo tipo,a.cod_prov cod_prov,a.beneficiario beneficiario,c.nombre proveed");
		$filter->db->from("ocompra a");
		$filter->db->join("(SELECT a.numero,
		IF(a.status='M','Sin Terminar',
			IF(a.status='P','Sin Comprometer',
				IF(a.`status`='A','Anulado',
					IF(SUM(pagos)=0,'Comprometido',
						IF(SUM(pagos)=SUM(compras) ,'Causado',
							IF(SUM(pagos)>0 AND SUM(compras)>SUM(pagos),'Parcialmente Causado','')
						)
					)
				)
			)
		)estado

		FROM ocompra a
		LEFT JOIN v_comproxcausar_s1 b ON a.numero=b.ocompra
		group by a.numero) d","d.numero=a.numero",'LEFT');
		$filter->db->join("itocompra b" ,"a.numero=b.numero","LEFT");
		$filter->db->join("sprv c"       ,"c.proveed=a.cod_prov" ,"LEFT");
		//$filter->db->where("a.tipo IN ('$this->ptipost')");
		$filter->db->groupby("a.numero");

		$filter->numero = new inputField("Numero", 'numero');
		$filter->numero->size = 6;
		$filter->numero->db_name='a.numero';

		$filter->compromiso = new inputField("Compromiso", 'compromiso');
		$filter->compromiso->size   = 6;
		$filter->compromiso->db_name='a.compromiso';

		$filter->tipo = new dropdownField("Orden de ", "tipo");
		$filter->tipo->db_name = 'a.tipo';
		$filter->tipo->option(""    ,""    );
		$filter->tipo->option("Compra"    ,"Compra"    );
		$filter->tipo->option("Trabajo"   ,"Trabajo"   );
		$filter->tipo->option("Servicio"  ,"Servicio"  );
		$filter->tipo->option("Compromiso","Compromiso");
		$filter->tipo->style="width:100px;";
		

		$filter->fecha = new dateonlyField("Fecha", "fecha");
		$filter->fecha->size=12;
		$filter->fecha->dbformat = "Y-m-d";
		$filter->fecha->db_name='a.fecha';

		$filter->uejecutora = new dropdownField("U.Ejecutora", "uejecutora");
		$filter->uejecutora->option("","Seccionar");
		$filter->uejecutora->options("SELECT codigo,nombre FROM uejecutora ORDER BY nombre");
		$filter->uejecutora->onchange = "get_uadmin();";
		//$filter->uejecutora->rule = "required";

		$filter->cod_prov = new inputField("Beneficiario", 'cod_prov');
		$filter->cod_prov->size = 6;
		$filter->cod_prov->append($bSPRV);
		$filter->cod_prov->db_name='a.cod_prov';

		$filter->estadmin = new dropdownField("Est. Administrativa", "estadmin");
		$filter->estadmin->option("","Seccionar");
		$filter->estadmin->options("SELECT b.codigo, CONCAT_WS(' ',b.codigo,b.denominacion) AS val FROM presupuesto AS a JOIN estruadm AS b ON a.codigoadm=b.codigo  GROUP BY b.codigo");
		$filter->estadmin->onchange = "get_uadmin();";
		$filter->estadmin->db_name = 'b.codigoadm';

		$filter->fondo = new dropdownField("Fuente de Financiamiento", "fondo");
		$filter->fondo->option("","");
		$filter->fondo->options("SELECT fondo,CONCAT_WS(' ',fondo,descrip)a FROM fondo");
		$filter->fondo->db_name = 'b.fondo';

		$filter->partida = new inputField("Partida", "partida");
		//$filter->partida-> db_name ="codigopres";
		$filter->partida->clause ="likerigth";
		$filter->partida->size     = 25;

		$filter->reverso = new inputField("Reverso de", "reverso");
		$filter->reverso->size=20;

		$filter->observa = new inputField("Concepto", "observa");
		$filter->observa->size=20;

		$filter->estado = new dropdownField("Estado","estado");
		$filter->estado->option("","");
		$filter->estado->option("Sin Comprometer"       ,"Sin Comprometer"       );
		$filter->estado->option("Comprometido"          ,"Comprometido"          );
		$filter->estado->option("Causado"               ,"Causado"               );
		$filter->estado->option("Parcialmente Causado"  ,"Parcialmente Causado"  );
		$filter->estado->option("Anulado"               ,"Anulado"               );
		$filter->estado->option("Sin Terminar"          ,"Sin Terminar"          );
		$filter->estado->option("Por Modificar"         ,"Por Modificar"         );
		$filter->estado->style="width:150px";
		$filter->estado->db_name=' ';
		$filter->estado->clause =' ';	

		$filter->buttons("reset","search");
		$filter->build();
		
		
		if($this->rapyd->uri->is_set("search") || $this->rapyd->uri->is_set("reset")  || $this->rapyd->uri->is_set("osp")){
			$mSQL=$this->rapyd->db->_compile_select();
			
			
			$estado = $filter->estado->newValue;
			
			$mSQL=" SELECT * FROM ($mSQL)a ";
			if(!empty($estado)){
				$estadoe = $this->db->escape($estado);
				$mSQL.=" WHERE estado=$estadoe";
			}
			
			$mSQL = $this->db->query($mSQL);
			$data = $mSQL->result_array();
			
			
		}

		$uri   = anchor('presupuesto/ocompra/dataedit/show/<#numero#>','<#numero#>');
		$uri_2 = anchor('presupuesto/ocompra/duplicar/<#numero#>','Duplicar');


		$grid = new DataGrid("",$data);
		if($this->datasis->puede(25))
			$grid->order_by("status = 'P',numero ","desc");
		else
			$grid->order_by("numero","desc");

		$grid->per_page = 20;
		$grid->use_function('substr','str_pad');

		$grid->column_orderby("N&uacute;mero"     ,$uri                                          ,"numero"                                 );
		$grid->column_orderby("Tipo"              ,"tipo"                                        ,"tipo"           ,"align='center'"       );
		$grid->column_orderby("Compromiso"        ,"compromiso"                                  ,"compromiso"     ,"align='center'"       );
		$grid->column_orderby("Fecha"             ,"<dbdate_to_human><#fecha#></dbdate_to_human>","fecha"          ,"align='center'"       );
		$grid->column_orderby("Est. Adm"          ,"codigoadm"                                   ,"codigoadm"                              );
		$grid->column_orderby("F. Financiamiento" ,"fondo"                                 ,"fondo"                                  );
		$grid->column_orderby("Partida"           ,"partida"                                     ,"partida"                                );
		$grid->column_orderby("Beneficiario"      ,"proveed"                                     ,"proveed"                                );
		$grid->column_orderby("Estado"            ,"estado"                                      ,"estado"         ,"align='center' "      );
		$grid->column("Duplicar"                  ,$uri_2                                        ,"align='center'"                         );

		if($this->datasis->puede(25)){
			$uri_3 = anchor('presupuesto/ocompra/ingcert/TRUE/modify/<#numero#>','Comprometer');
			$grid->column("Compremeter"             ,$uri_3                                        ,"align='center'"                         );
		}

		if($this->datasis->puede(162) || $this->datasis->essuper())$grid->add("presupuesto/ocompra/dataedit/create");

		$grid->build();

		//echo $grid->db->last_query();

		//$data['content'] = $filter->output.$grid->output;
		$salida='';
		if($this->datasis->traevalor('USASIPRES')=='S')
		$salida = anchor($this->url."sipresocompra","Crear Compromiso basado en SIPRES");

		$data['filtro']  = $filter->output;
		$data['content'] = $salida.$grid->output;
		$data['script']  = script("jquery.js");

		//if($this->datasis->puede(25) || $this->datasis->essuper())
		//	$data['title']   = "Comprometer";
		//
		//	if($this->datasis->puede(162) || $this->datasis->essuper())
		//	$data['title']   = "Ordenes de Compra o Servicio";

		$data['title']   = $this->titp;
		$data["head"]    = $this->rapyd->get_head();
		$this->load->view('view_ventanas', $data);
	}
	

	function dataedit($duplicar='S',$status='',$numero=''){
		//$this->datasis->modulo_id(70,1);
		$this->rapyd->load('dataobject','datadetails');

		$mSPRV=array(
				'tabla'   =>'sprv',
				'columnas'=>array(
				'proveed' =>'C&oacute;odigo',
				'rif'     =>'RIF',
				'nombre'  =>'Nombre',
				'contacto'=>'Contacto'),
				'filtro'  =>array('proveed'=>'C&oacute;digo','nombre'=>'Nombre','rif'=>'RIF'),
				'retornar'=>array('proveed'=>'cod_prov', 'nombre'=>'nombrep','reteiva'=>'reteiva_prov','rif'=>'rif' ),
				'script'  =>array('cal_lislr()','cal_total()'),
				'titulo'  =>'Buscar Beneficiario');

		$bSPRV=$this->datasis->p_modbus($mSPRV,"proveed");
		
		$mCDISP=array(
				'tabla'   =>'cdisp',
				'columnas'=>array(
					'numero'       =>'N&uacute;mero', 
					'fecha'        =>'Fecha',
					'reque'        =>'Requesicion',
					'tdisp'        =>'Total'   
				),
				'filtro'  =>array(
					'numero'     =>'N&uacute;mero',
					'reque'      =>'Requisicion'
				),
				'retornar'=>array(
					'numero'     =>'certificado'
				),
				'order_by'=>'numero desc',
				'titulo'  =>'Buscar Certificado');

		$bCDISP=$this->datasis->p_modbus($mCDISP,"cdisp");

		$modbus=array(
			'tabla'   =>'v_presaldo',
			'columnas'=>array(
				'codigoadm'   =>'Est. Admin',
				//'fondo'       =>'F. Financiamiento',
				'codigo'      =>'Partida',
				'ordinal'     =>'Ordinal',
				'denominacion'=>'Denominaci&oacute;n',
				'apartado'    =>'Pre-Comprometido',
				'saldo'       =>'Saldo'
				),
			'filtro'  =>array(
				'codigoadm'   =>'Est. Admin',
				//'fondo'       =>'F. Financiamiento',
				'codigo'      =>'Partida',
				'ordinal'     =>'Ord',
				'denominacion'=>'Denominaci&oacute;n'
				),
			'retornar'=>array(
				'codigoadm'   =>'itcodigoadm_<#i#>',
				//'fondo'       =>'itfondo_<#i#>',
				'codigo'      =>'partida_<#i#>'),
			'where'=>'movimiento = "S" AND saldo>0 AND fondo=<#fondo#> AND codigo LIKE "4.%"',
			'p_uri'=>array(4=>'<#i#>',5=>'<#fondo#>'),
			'titulo'  =>'Busqueda de partidas',
			'order_by'  =>'codigoadm,codigo'
			);
		//$btn=$this->datasis->p_modbus($modbus,'<#i#>');
		$btn=$this->datasis->p_modbus($modbus,'<#i#>/<#fondo#>');
		$btn='<img src="'.base_url().'assets/default/images/system-search.png"  alt="Busqueda de partidas" title="Busqueda de partidas" border="0" onclick="modbusdepen(<#i#>)"/>';

		$do = new DataObject("ocompra");
		$do->order_by('itocompra','itocompra.id','desc');
		$do->rel_one_to_many('itocompra', 'itocompra', array('numero'=>'numero'));
		$do->pointer('sprv' ,'sprv.proveed=ocompra.cod_prov','sprv.nombre as nombrep, sprv.rif as rif','LEFT');
		$do->order_by('itocompra','itocompra.id',' ');

		$edit = new DataDetails("Orden de Compra", $do);
		$edit->back_url = site_url("presupuesto/ocompra/filteredgrid");
		$edit->set_rel_title('itocompra','Rubro <#o#>');

		$status=$edit->get_from_dataobjetct('status');

		$edit->pre_process('insert'  ,'_valida');
		$edit->pre_process('update'  ,'_valida');
		$edit->pre_process('delete'  ,'_pre_delete');

		//$edit->post_process('insert'  ,'_post');
		//$edit->post_process('update'  ,'_post');

		$edit->post_process('insert','_post_insert');
		$edit->post_process('update','_post_update');
		$edit->post_process('delete','_post_delete');

		if($this->datasis->puede(25) && $status=='P')
			$edit->makerel  = true;

		$a='';
		switch($status){
			case 'P':$a="Sin Comprometer";break;
			case 'C':$a="Comprometida";break;
			case 'T':$a="Causada";break;
			case 'O':$a="Ordenado Pago";break;
			case 'E':$a="Pagado";break;
			case 'E':$a="No Terminada";break;
		}
		$edit->status = new freeField("Estado", 'estado',$a);

		$edit->numero  = new inputField("N&uacute;mero", "numero");
		$edit->numero->mode="autohide";
		$edit->numero->when=array('show');

		$edit->tipo = new dropdownField("Orden de", "tipo");
		$edit->tipo->style="width:100px;";
		$edit->tipo->mode = "autohide";
		if($this->datasis->puede(299))
			$edit->tipo->option('Compra','Compra');
		if($this->datasis->puede(300))
			$edit->tipo->option('Servicio','Servicio');
		if($this->datasis->puede(439))
			$edit->tipo->option('Contrato','Contrato');
		if($this->datasis->puede(440))
			$edit->tipo->option('Trabajo','Trabajo');
		if($this->datasis->puede(301))
			$edit->tipo->option('Compromiso','Compromiso');
		if($this->datasis->puede(442))
			$edit->tipo->option('Ejec.Obra','Ejec.Obra');
		if($this->datasis->puede(441))
			$edit->tipo->option('Cont.Marco','Cont.Marco');	

		if($status =='P' || $status=='p'){
			//$edit->tipo = new inputField("Orden de", "tipo");
			$edit->tipo->readonly = true;
			//$edit->tipo->size     =10;
		}

		$edit->fecha = new  dateonlyField("Fecha",  "fecha");
		$edit->fecha->dbformat ='Ymd';
		$edit->fecha->insertValue = date('Ymd');
		$edit->fecha->size =12;
		if($status=='P')
		$edit->fecha->readonly = true;
		//$edit->fecha->readonly = true;
		//$edit->fecha->mode="autohide";
		//$edit->fecha->when = array("show","modify");
		$edit->fecha->rule = "callback_chfecha";

		$edit->status = new dropdownField("Estado","status");
		$edit->status->option("","");
		$edit->status->option("P","Sin Comprometer");
		$edit->status->option("C","Comprometido");
		$edit->status->option("T","Causado");
		$edit->status->option("O","Ordenado Pago");
		$edit->status->option("E","Pagado");
		$edit->status->option("A","Anulado");
		$edit->status->option("R","Reversado");
		$edit->status->option("M","Sin Terminar");
		$edit->status->option("p","Por Modificar");
		$edit->status->when=array('show');
		if($status=='P')
		$edit->status->readonly = true;
		//$edit->status->readonly = true;

		if($this->datasis->traevalor("USACERTIFICADO")=='S'){
			$edit->certificado  = new inputField("Cert. Disp. Presupuestaria", "certificado");
			$edit->certificado->size=15;
			$edit->certificado->append($bCDISP);
			//$edit->certificado->readonly=true;
			if($status=='O')
			$edit->certificado->mode="autohide";
		}
		
		if($this->datasis->traevalor("USACOMPROMISO")=='S'){
			$edit->compromiso  = new inputField("Nro Compromiso", "compromiso");
			$edit->compromiso->size=15;
			if($status=='O')
			$edit->compromiso->mode="autohide";
		}

        if($this->datasis->traevalor("USAOCOMPRAPROCED",'N','Indica si orden de compra usa el campo proced')=='S'){
			$edit->proced  = new inputField("Procedimiento", "proced");
			$edit->proced->size=25;
		}

		$edit->uejecutora = new dropdownField("Unidad Ejecutora", "uejecutora");
		$edit->uejecutora->options("SELECT codigo, nombre FROM uejecutora ORDER BY nombre");
		$edit->uejecutora->style="width:250px";
		//$edit->uejecutora->onchange = "get_uadmin();";
		//$edit->uejecutora->rule = "required";
		if($status=='P')
		$edit->uejecutora->readonly = true;
		//$edit->uejecutora->readonly = true;

		$edit->usolicita = new dropdownField("Unidad Solicitante", "usolicita");
		$edit->usolicita->options("SELECT codigo, nombre FROM uejecutora ORDER BY nombre");
		$edit->usolicita->style="width:250px";
		if($status=='P')
		$edit->usolicita->readonly = true;
		//$edit->usolicita->readonly = true;

		$unsolofondo=$this->datasis->traevalor('UNSOLOFONDO','S','Indica si se utiliza una sola fuente de financiamiento');
		if($unsolofondo=='S'){
			$edit->fondo = new dropdownField("F. Financiamiento","fondo");
			//$edit->fondo->rule   ='required';
			$edit->fondo->db_name='fondo';
			$edit->fondo->option("","");
			$edit->fondo->options("SELECT tipo,tipo a  FROM presupuesto WHERE tipo<>'' GROUP BY tipo ORDER BY tipo desc");
			$edit->fondo->style="width:300px;";
		}

		$lsnc='<a href="javascript:consulsprv();" title="Proveedor" onclick="">Consulta/Agrega BENEFICIARIO</a>';
		$edit->cod_prov = new inputField("Beneficiario", 'cod_prov');
		$edit->cod_prov->size     = 4;
		$edit->cod_prov->rule     = "trim|required";
		$edit->cod_prov->append($bSPRV);
		//$edit->cod_prov->readonly=true;
		if($status=='P')
		$edit->cod_prov->readonly = true;
		$edit->cod_prov->append($lsnc);
		$edit->cod_prov->onchange = "cal_nprov();";
		//$edit->cod_prov->mode="autohide";

		$edit->nombrep = new inputField("Nombre Beneficiario", 'nombrep');
		$edit->nombrep->size = 20;
		//$edit->nombrep->readonly = true;
		$edit->nombrep->pointer = true;
		if($status=='P')
		$edit->nombrep->readonly = true;
		//$edit->nombrep->readonly = true;

		$edit->reteiva_prov  = new inputField("% R.IVA", "reteiva_prov");
		$edit->reteiva_prov->size=2;
		//$edit->reteiva_prov->mode="autohide";
		$edit->reteiva_prov->when=array('modify','create','show');
		$edit->reteiva_prov->readonly = true;
		if($status=='P')
		$edit->reteiva_prov->readonly = true;

		$edit->rif  = new inputField("RIF", "rif");
		$edit->rif->size=10;
		$edit->rif->pointer = true;
		if($status=='P')
		$edit->rif->readonly = true;

		$edit->creten = new dropdownField("Codigo ISLR: ","creten");
		//$edit->creten->option("","");
		$edit->creten->options("SELECT codigo,CONCAT_WS(' ',codigo,activida) FROM rete ORDER BY codigo");
		$edit->creten->style="width:300px;";
		$edit->creten->onchange ='cal_total();';
		if($status=='P')
		$edit->creten->readonly = true;

		$edit->observa = new textAreaField("Concepto", 'observa');
		$edit->observa->rule = "trim";
		$edit->observa->cols = 25;
		$edit->observa->rows = 3;
		if($status=='P')
		$edit->observa->readonly = true;
		
		$edit->modalidad = new dropdownField("Modalidad","modalidad");
		//$edit->modalidad->rule   ='required';
		$edit->modalidad->db_name='modalidad';
		$edit->modalidad->option("Concurso Cerrado"           ,"Concurso Cerrado"           );
		$edit->modalidad->option("Concurso Abierto"           ,"Concurso Abierto"           );
		$edit->modalidad->option("Consulta de Precio"         ,"Consulta de Precio"         );
		$edit->modalidad->option("Contratación Directa"       ,"Contrataci&oacute;n Directa");
		$edit->modalidad->option("Excluida"                   ,"Excluida"                   );
		$edit->modalidad->style="width:300px;";
		
		$edit->formaentrega = new dropdownField("Forma de Entrega","formaentrega");
		//$edit->formaentrega->rule   ='required';
		$edit->formaentrega->db_name='formaentrega';
		$edit->formaentrega->option("Una Entrega"          ,"Una Entrega"        );
		$edit->formaentrega->option("Dos Entregas"         ,"Dos Entregas"       );
		$edit->formaentrega->option("Tres Entregas"        ,"Tres Entregas"      );
		$edit->formaentrega->option("Cuatro Entregas"      ,"Cuatro Entregas"    );
		$edit->formaentrega->option("Cinco Entregas"       ,"Cinco Entregas"     );
		$edit->formaentrega->option("Seis Entregas"        ,"Seis Entregas"      );
		$edit->formaentrega->style="width:300px;";
		
		$edit->condiciones = new textAreaField("Condiciones Especiales", 'condiciones');
		$edit->condiciones->rule = "trim";
		$edit->condiciones->cols = 25;
		$edit->condiciones->rows = 1;
		
		$edit->lentrega = new textAreaField("Lugar de Entrega", 'lentrega');
		$edit->lentrega->cols     = 25;
		$edit->lentrega->rows     = 3;
		if($status=='P')
		$edit->lentrega->readonly = true;

		if($this->datasis->traevalor("USAOCOMPRAPROCED")=='S'){
			$edit->proced = new inputField("Procedimiento","proced");
		}

		if($this->datasis->traevalor("USACOMPEFP")=='S'){
			$edit->pentret = new dropdownField("Plazo Entrega","pentret");
			$edit->pentret->option("M","Meses");
			$edit->pentret->option("H","Dias Habiles");
			$edit->pentret->option("C","Dias Continuos");
			$edit->pentret->style="width:150px;";
			if($status=='P')
			$edit->pentret->readonly = true;

			$edit->pentrec = new inputField("", 'pentrec');
			$edit->pentrec->size = 5;
			$edit->pentrec->css_class='inputnum';
			$edit->pentrec->rule     ='required|numeric';
			if($status=='P')
			$edit->pentrec->readonly = true;

			$edit->fpagot = new dropdownField("Forma de Pago","fpagot");
			$edit->fpagot->option("X","Parcial");
			$edit->fpagot->option("Z","Total");
			$edit->fpagot->style="width:150px;";
			if($status=='P')
			$edit->fpagot->readonly = true;

			$edit->fpagoc = new inputField("", 'fpagoc');
			$edit->fpagoc->size = 5;
			$edit->fpagoc->css_class='inputnum';
			$edit->fpagoc->rule     ='numeric';
		}

		$edit->subtotal = new inputField("Total Base Imponible", 'subtotal');
		$edit->subtotal->css_class='inputnum';
		$edit->subtotal->size = 8;
		if($status=='P')
		$edit->subtotal->readonly = true;
		//$edit->subtotal->mode="autohide";

		$edit->ivaa = new inputField("IVA Sobre Tasa", 'ivaa');
		$edit->ivaa->css_class='inputnum';
		$edit->ivaa->size = 8;
		if($status=='P')
		$edit->ivaa->readonly = true;
		//$edit->ivaa->mode="autohide";

		$edit->ivag = new inputField("IVA Tasa General", 'ivag');
		$edit->ivag->css_class='inputnum';
		$edit->ivag->size = 8;
		if($status=='P')
		$edit->ivag->readonly = true;
		//$edit->ivag->mode="autohide";

		$edit->ivar = new inputField("IVA Tasa reducida", 'ivar');
		$edit->ivar->css_class='inputnum';
		$edit->ivar->size = 8;
		if($status=='P')
		$edit->ivar->readonly = true;
		//$edit->ivar->mode="autohide";

		$edit->exento = new inputField("Exento", 'exento');
		$edit->exento->css_class='inputnum';
		$edit->exento->size = 8;
		if($status=='P')
		$edit->exento->readonly = true;
		//$edit->exento->mode="autohide";

		$edit->reteiva = new inputField("Retencion de IVA", 'reteiva');
		$edit->reteiva->css_class='inputnum';
		$edit->reteiva->size = 8;
		if($status=='P')
		$edit->reteiva->readonly = true;
		//$edit->reteiva->mode="autohide";

		$edit->reten = new inputField("Retencion de ISLR", 'reten');
		$edit->reten->css_class='inputnum';
		$edit->reten->size = 8;
		if($status=='P')
		$edit->reten->readonly = true;
		//$edit->reten->mode="autohide";

		$edit->total2 = new inputField("Total", 'total2');
		$edit->total2->css_class='inputnum';
		$edit->total2->size = 8;
		if($status=='P')
		$edit->total2->readonly = true;
		//$edit->total2->mode="autohide";

		$edit->simptimbre = new checkboxField("1X1000", "simptimbre", "S","N");
		$edit->simptimbre->insertValue = "N";
		$edit->simptimbre->onchange ='cal_total();';
		if($status=='P')
		$edit->simptimbre->mode="autohide";

		$edit->imptimbre= new inputField("Impuesto 1X1000", 'imptimbre');
		$edit->imptimbre->size = 8;
		$edit->imptimbre->css_class='inputnum';
		$edit->imptimbre->readonly = true;
		if($status=='P')
			$edit->imptimbre->mode="autohide";

		$edit->itesiva = new dropdownField("P.IVA","itesiva_<#i#>");
		$edit->itesiva->rule   ='required';
		$edit->itesiva->db_name='esiva';
		$edit->itesiva->rel_id ='itocompra';
		$edit->itesiva->option("N","No");
		$edit->itesiva->option("S","Si");
		$edit->itesiva->option("A","Auto");
		$edit->itesiva->style="width:45px;";

		if($unsolofondo!='S'){
			$edit->itfondo = new dropdownField("F. Financiamiento","itfondo_<#i#>");
			//$edit->itfondo->rule   ='required';
			$edit->itfondo->db_name='fondo';
			$edit->itfondo->rel_id ='itocompra';
			$edit->itfondo->options("SELECT tipo,tipo a  FROM presupuesto WHERE tipo<>'' GROUP BY tipo ORDER BY tipo desc");
			$edit->itfondo->style="width:100px;";
		}

		$edit->itcodigoadm = new inputField("Estructura	Administrativa","itcodigoadm_<#i#>");
		$edit->itcodigoadm->size        =8;
		$edit->itcodigoadm->db_name     ='codigoadm';
		$edit->itcodigoadm->rel_id      ='itocompra';
		//$edit->itcodigoadm->rule        ='required';
		$edit->itcodigoadm->autocomplete=false;

		$edit->itpartida = new inputField("(<#o#>) Partida", "partida_<#i#>");
		//$edit->itpartida->rule='required';
		$edit->itpartida->size=12;
		$edit->itpartida->append($btn);
		$edit->itpartida->db_name='partida';
		$edit->itpartida->rel_id ='itocompra';
		$edit->itpartida->autocomplete=false;
		//$edit->itpartida->insertValue ="4";

		//$edit->itordinal = new inputField("(<#o#>) Ordinal", "ordinal_<#i#>");
		//$edit->itordinal->db_name  ='ordinal';
		//$edit->itordinal->maxlength=3;
		//$edit->itordinal->size     =1;
		//$edit->itordinal->rel_id   ='itocompra';

		$edit->itdescripcion = new textareaField("(<#o#>) Descripci&oacute;n", "descripcion_<#i#>");
		$edit->itdescripcion->db_name  ='descripcion';
		$edit->itdescripcion->cols=20;
		$edit->itdescripcion->rows=2;
		//$edit->itdescripcion->rule     = 'required';
		$edit->itdescripcion->rel_id   ='itocompra';
		if($status=='P')
		$edit->itdescripcion->readonly = true;
		//$edit->itdescripcion->mode="autohide";

		$edit->itunidad = new dropdownField("(<#o#>) Unidad", "unidad_<#i#>");
		$edit->itunidad->db_name= 'unidad';
		//$edit->itunidad->rule   = 'required';
		$edit->itunidad->rel_id = 'itocompra';
		$edit->itunidad->options("SELECT unidades AS id,unidades FROM unidad ORDER BY unidades");
		$edit->itunidad->style="width:70px";
		if($status=='P')
		$edit->itunidad->readonly = true;
		//$edit->itunidad->mode="autohide";

		$edit->itcantidad = new inputField("(<#o#>) Cantidad", "cantidad_<#i#>");
		$edit->itcantidad->css_class='inputnum';
		$edit->itcantidad->db_name  ='cantidad';
		$edit->itcantidad->rel_id   ='itocompra';
		$edit->itcantidad->rule     ='numeric';
		$edit->itcantidad->onchange ='cal_importe(<#i#>);';
		$edit->itcantidad->size     =4;
		if($status=='P')
		$edit->itcantidad->readonly = true;
		//$edit->itcantidad->mode="autohide";

		$edit->itprecio = new inputField("(<#o#>) Precio", "precio_<#i#>");
		$edit->itprecio->css_class='inputnum';
		$edit->itprecio->db_name  ='precio';
		$edit->itprecio->rel_id   ='itocompra';
		$edit->itprecio->rule     ='callback_positivo';
		$edit->itprecio->onchange ='cal_importe(<#i#>);';
		$edit->itprecio->size     =6;
		if($status=='P')
		$edit->itprecio->readonly = true;
		//$edit->itprecio->mode="autohide";

		$edit->itusaislr = new dropdownField("(<#o#>) Islr", "usaislr_<#i#>");
		$edit->itusaislr->db_name     = 'usaislr';
		$edit->itusaislr->rel_id      = 'itocompra';
		$edit->itusaislr->insertValue = "N";
		$edit->itusaislr->onchange ='cal_total();';
		$edit->itusaislr->option("N","No");
		$edit->itusaislr->option("S","Si");
		$edit->itusaislr->style="width:45px";
		if($status=='P')
		$edit->itusaislr->readonly = true;
		//$edit->itusaislr->mode="autohide";

		$edit->itislr = new inputField("(<#o#>) Islr", "islr_<#i#>");
		$edit->itislr->css_class='inputnum';
		$edit->itislr->db_name  ='islr';
		$edit->itislr->rel_id   ='itocompra';
		$edit->itislr->rule     ='numeric';
		$edit->itislr->readonly =true;
		$edit->itislr->size     =5;
		if($status=='P')
		$edit->itislr->readonly = true;
		//$edit->itislr->mode="autohide";

		if($status=='P'){
			$edit->itiva = new inputField("(<#o#>) IVA", "iva_<#i#>");
			$edit->itiva->db_name  ='iva';
			$edit->itiva->rel_id   ='itocompra';
			$edit->itiva->size     =4;
			$edit->itiva->readonly =true;
		}else{
			$edit->itiva = new dropdownField("(<#o#>) IVA", "iva_<#i#>");
			$edit->itiva->db_name  ='iva';
			$edit->itiva->rel_id   ='itocompra';
			$edit->itiva->onchange ='cal_importe(<#i#>);';
			$edit->itiva->options($this->_ivaplica());
			$edit->itiva->option("0"  ,"0%");
			$edit->itiva->style    ="width:80px";
		}

		$edit->itimporte = new inputField("(<#o#>) Importe", "importe_<#i#>");
		$edit->itimporte->css_class='inputnum';
		$edit->itimporte->db_name  ='importe';
		$edit->itimporte->rel_id   ='itocompra';
		$edit->itimporte->rule     ='numeric';
		$edit->itimporte->onchange ='cal_importep(<#i#>);';
		//$edit->itimporte->readonly =true;
		$edit->itimporte->size     =8;
		if($status=='P')
		$edit->itimporte->readonly = true;
		//$edit->itimporte->mode="autohide";

		$edit->redondear = new dropdownField("Redondear","redondear");
		$edit->redondear->option("R2","Sumar Redondear 2 Decimales");
		$edit->redondear->option("R0","Sumar SIN Redondear 2 Decimales");
		//$edit->redondear->onchange = "cal_total();";
		
		$tipo=$edit->get_from_dataobjetct('tipo');
		
		if($status=='P'){
			if($this->datasis->traevalor('USACOMPROMISO')=='S' ){
				$uri_3 = anchor('presupuesto/ocompra/ingcert/modify/<#numero#>','Comprometer');
				$action = "javascript:window.location='" .site_url('presupuesto/ocompra/ingcert/modify/'.$edit->rapyd->uri->get_edited_id()). "'";
				if($this->datasis->puede(25))$edit->button_status("btn_status",'Comprometer',$action,"TR","show");
			}else{
				
				$action = "javascript:window.location='" .site_url('presupuesto/ocompra/ingcert/NOVALIDA/modify/'.$edit->rapyd->uri->get_edited_id()). "'";
				if($this->datasis->puede(25))$edit->button_status("btn_status",'Comprometer',$action,"TR","show");
			}
			$action = "javascript:window.location='" .site_url('presupuesto/ocompra/anular/'.$edit->rapyd->uri->get_edited_id()). "'";
			if($this->datasis->puede(160))$edit->button_status("btn_anular",'Anular',$action,"TR","show");
			$action = "javascript:btn_noterminada('" .$edit->rapyd->uri->get_edited_id()."')";
			if($this->datasis->puede(213))$edit->button_status("btn_notermina",'Marcar Orden Como NO Terminada',$action,"TR","show");
			$edit->buttons("delete");
		}elseif($status=='C'){
			$action = "javascript:window.location='" .site_url('presupuesto/ocompra/ingcert/NOVALIDA/modify/'.$edit->rapyd->uri->get_edited_id().''). "'";
			if($this->datasis->puede(332))$edit->button_status("btn_modifi",'Modificar Compromiso',$action,"TR","show");
			$action = "javascript:btn_reverf('" .$edit->rapyd->uri->get_edited_id()."')";
			if($this->datasis->puede(160))$edit->button_status("btn_rever",'Reversar Compromiso',$action,"TR","show");
		}elseif($status=='M'){
			$action = "javascript:window.location='" .site_url('presupuesto/ocompra/anular/'.$edit->rapyd->uri->get_edited_id()). "'";
			$edit->button_status("btn_anular",'Anular',$action,"TR","show");
			$action = "javascript:btn_noterminada('" .$edit->rapyd->uri->get_edited_id()."')";
			$action = "javascript:btn_terminada('" .$edit->rapyd->uri->get_edited_id()."')";
			if($this->datasis->puede(213))$edit->button_status("btn_termina",'Marcar Orden Como Terminada',$action,"TR","show");
			
			if($this->datasis->puede(158)){
				if($this->datasis->puede(299) && $tipo=='Compra'){
					$edit->buttons("modify");
					$edit->buttons("delete");
				}
				if($this->datasis->puede(300) && $tipo=='Servicio'){
					$edit->buttons("modify");
					$edit->buttons("delete");
				}
				if($this->datasis->puede(439) && $tipo=='Contrato'){
					$edit->buttons("modify");
					$edit->buttons("delete");
				}
				if($this->datasis->puede(440) && $tipo=='Trabajo'){
					$edit->buttons("modify");
					$edit->buttons("delete");
				}
				if($this->datasis->puede(301) && $tipo=='Compromiso'){
					$edit->buttons("modify");
					$edit->buttons("delete");
				}
				if($this->datasis->puede(442) && $tipo=='Ejec.Obra'){
					$edit->buttons("modify");
					$edit->buttons("delete");
				}
				if($this->datasis->puede(441) && $tipo=='Cont.Marco'){
					$edit->buttons("modify");
					$edit->buttons("delete");
				}
			}
			if($this->datasis->puede(159))$edit->buttons("save");
		}elseif($status=='p'){
			if($this->datasis->puede(158))$edit->buttons("modify");
			if($this->datasis->puede(159))$edit->buttons("save");
		}elseif($status=='A'){
			if($this->datasis->puede(342)){
				if($this->datasis->puede(299) && $tipo=='Compra'){
					$edit->buttons("modify");
					$edit->buttons("delete");
				}
				if($this->datasis->puede(300) && $tipo=='Servicio'){
					$edit->buttons("modify");
					$edit->buttons("delete");
				}
				if($this->datasis->puede(439) && $tipo=='Contrato'){
					$edit->buttons("modify");
					$edit->buttons("delete");
				}
				if($this->datasis->puede(440) && $tipo=='Trabajo'){
					$edit->buttons("modify");
					$edit->buttons("delete");
				}
				if($this->datasis->puede(301) && $tipo=='Compromiso'){
					$edit->buttons("modify");
					$edit->buttons("delete");
				}
				if($this->datasis->puede(442) && $tipo=='Ejec.Obra'){
					$edit->buttons("modify");
					$edit->buttons("delete");
				}
				if($this->datasis->puede(441) && $tipo=='Cont.Marco'){
					$edit->buttons("modify");
					$edit->buttons("delete");
				}
			}
		}else{
			if($this->datasis->puede(159))$edit->buttons("save");
		}		

		$edit->buttons("undo","back","add_rel","add");
		
		$edit->build();

		//SELECT codigo,base1,tari1,pama1 FROM rete
		$query = $this->db->query('SELECT * FROM rete ORDER BY codigo');

		$rt=array();
		foreach ($query->result_array() as $row){
			$pivot=array('tari1'=>$row['tari1'],
			             'pama1'=>$row['pama1'],
			             'tari2'=>$row['tari2'],
			             'pama2'=>$row['pama2'],
			             'tari3'=>$row['tari3'],
			             'pama3'=>$row['pama3'],
			             'porcentsustra'=>$row['porcentsustra']
			             );
			$rt['_'.$row['codigo']]=$pivot;
		}
		$rete=json_encode($rt);

		$conten['rete']=$rete;
		$ivaplica           =$this->ivaplica2();
		$conten['ivar']     = $ivaplica['redutasa'];
		$conten['ivag']     = $ivaplica['tasa'];
		$conten['ivaa']     = $ivaplica['sobretasa'];
		$conten['title2']   = $this->tits;
		$conten['imptimbre']=$this->datasis->traevalor('IMPTIMBRE',0);
		$conten['tipo']     =$tipo;
		$conten['utribuactual']=$this->datasis->dameval('SELECT valor FROM utribu WHERE ano=(SELECT MAX(ano) FROM utribu)');

		$smenu['link']   = barra_menu('110');
		$data['smenu']   = $this->load->view('view_sub_menu', $smenu,true);
		$conten["form"]  = &$edit;
		$data['content'] = $this->load->view('view_ocompra', $conten,true);
		//$data['content'] = $edit->output;
		$data['title']   = $this->tits;

		$data["head"]    = script('jquery.js').script('jquery-ui.js').script('plugins/jquery.numeric.pack.js').script('plugins/jquery.floatnumber.js').script('plugins/jquery.meiomask.js').style('vino/jquery-ui.css').$this->rapyd->get_head();
		$this->load->view('view_ventanas', $data);
	}

	function ivaplica2($mfecha=NULL){
		if(empty($mfecha)) $mfecha=date('Ymd');
		//$CI =& get_instance();
		$qq = $this->db->query("SELECT tasa, redutasa, sobretasa FROM civa WHERE fecha < '$mfecha' ORDER BY fecha DESC LIMIT 1");
		$rr = $qq->row_array();
		//$aa = each($rr);
		return $rr;
	}

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

	function chfecha($fecha){
		return true;
		$date  = DateTime::createFromFormat('d/m/Y',$fecha);
		$fecha = $date->format('Ymd');
		$now   = date('Ymd',now());

		if($fecha > $now){
			$this->validation->set_message('chfecha',"La fecha es incorrecta, No es V�lida una Fecha Futura. </br>La fecha del servidor es:".dbdate_to_human($now));
			return false;
		}

		$f = $this->datasis->dameval("SELECT fecha FROM ocompra WHERE fecha >$fecha ORDER BY fecha DESC LIMIT 1");
		if(!empty($f)){
			$this->validation->set_message('chfecha',"La fecha es incorrecta, debe ser mayor o igual a ".dbdate_to_human($f));
			return false;
		}
	}

	function actualizar($id){
		$this->rapyd->load('dataobject');

		$do = new DataObject("ocompra");
		$do->rel_one_to_many('itocompra', 'itocompra', array('numero'=>'numero'));
		$do->load($id);

		$error       = "";
		$cod_prov    = $do->get('cod_prov');
		$subtotal    = $do->get('subtotal');
		$tipo        = $do->get('tipo');
		$certificado = $do->get('certificado');
		$certificadoe= $this->db->escape($certificado);

		if(empty($cod_prov))
			$error.="<div class='alert'><p>El Campo Beneficiario no puede estar vac&iacute;o</p></div>";

		if($subtotal==0)
			$error.="<div class='alert'><p>El Campo Subtotal no puede ser cero(0)</p></div>";


		if(empty($error)){
			$sta=$do->get('status');
			if($sta=="P"){
				$ivan=0;$importes=array(); $ivas=array(); $admfondo=array();$repetido=array();
				for($i=0;$i < $do->count_rel('itocompra');$i++){
					$codigoadm   = $do->get_rel('itocompra','codigoadm',$i);
					$fondo       = $do->get_rel('itocompra','fondo'    ,$i);
					$codigopres  = $do->get_rel('itocompra','partida'  ,$i);
					$ordinal     = $do->get_rel('itocompra','ordinal'  ,$i);
					$iva         = $do->get_rel('itocompra','iva'      ,$i);
					$importe     = $do->get_rel('itocompra','importe'  ,$i);
					$ivan        = $importe*$iva/100;
					
					if(empty($codigoadm) || empty($codigopres) || empty($fondo))
					$error.="<div class='alert'><p> Los Campos F. Financiemiento ($fondo), Estructura administrativa ($codigoadm) y Codigo Presupuestario ($codigopres) no pueden estar en blanco </p></div>";

					$error.=$this->itpartida($codigoadm,$fondo,$codigopres,$ordinal);

					$cadena = $codigoadm.'_._'.$fondo.'_._'.$codigopres;
					if(array_key_exists($cadena,$importes)){
						$importes[$cadena]+=$importe;
					}else{
						$importes[$cadena]  =$importe;
					}

					if($tipo=='Compromiso'){
						if(array_key_exists($cadena,$repetido)){
							$repetido[$cadena]+=1;
						}else{
							$repetido[$cadena] =1;
						}
					}

					$cadena2 = $codigoadm.'_._'.$fondo;
					$admfondo[$cadena2]=(array_key_exists($cadena2,$admfondo)?$admfondo[$cadena2]+=$ivan:$admfondo[$cadena2] =$ivan);
				}
				
				
				if($this->datasis->traevalor('USACERTIFICADO')=='S'){
					if($certificado>0){
						if(empty($error)){
							$statusc = $this->datasis->dameval("SELECT status FROM cdisp WHERE numero=$certificadoe");
							if($statusc!='C')
								$error.="<div class='alert'><p> ERROR. El Certicado de Disponibilidad $certificadoe no esta Pre-Comprometido</p></div>";
						}
						
						if(empty($error)){
							foreach($importes AS $cadena=>$monto){
								$temp  = explode('_._',$cadena);
								if(count($temp)>0){
									$query = "SELECT SUM(soli) FROM itcdisp WHERE numero=$certificadoe AND codigoadm='".$temp[0]."' AND fondo='".$temp[1]."' AND codigopres='".$temp[2]."'";
									$solicitado = $this->datasis->dameval($query);
									if($monto>$solicitado)
									$error.="<div class='alert'><p> ERROR. El Monto $monto a Comprometer es mayor al del Certificado $certificadoe para la partida $cadena </p></div>";
								}
							}							
						}	
					}
				}					
				

				if(empty($error)){
					foreach($importes AS $cadena=>$monto){
						$temp  = explode('_._',$cadena);
						if(count($temp)>0){
							if($this->datasis->traevalor('USACERTIFICADO')=='S' && !($certificado>0)){
								$error.= $this->chequeapresup($temp[0],$temp[1],$temp[2],'',$monto,0,'round($monto,2) > $disponible=round(($presupuesto-($comprometido+$apartado)),2)','El Monto ($monto) es mayor al disponible ($disponible) para la partida ('.$temp[0].') ('.$temp[1].') ('.$temp[2].') ') ;
							}else{
								$error.= $this->chequeapresup($temp[0],$temp[1],$temp[2],'',$monto,0,'round($monto,2) > $disponible=round(($presupuesto-($comprometido)),2)','El Monto ($monto) es mayor al disponible ($disponible) para la partida ('.$temp[0].') ('.$temp[1].') ('.$temp[2].') ') ;
							}
							
						}
						
					}

				}
				if($tipo=='Compromiso'){
					foreach($repetido AS $cadena=>$cant){
						if($cant>1)
						$error.="<div class='alert'><p> La partida $cadena esta duplicada</p></div>";
					}
				}

				if(empty($error)){
					if($this->datasis->traevalor('USACERTIFICADO')=='S'){
						if($certificado>0)
						$this->cd_finalizar($certificado,false,false);
					}
					
					foreach($importes AS $cadena=>$monto){
						$temp  = explode('_._',$cadena);
						$error.=$this->afectapresup($temp[0],$temp[1],$temp[2],'',$monto,0, 1 ,array("comprometido"));
					}
					
					if(empty($error)){
						$do->set('status','C');
						$do->set('fcomprome',date('Ymd'));
						$do->save();
					}
				}
			}
		}

		if(empty($error)){
			logusu('ocompra',"Comprometio Orden de Compra Nro $id");
			redirect("presupuesto/ocompra/dataedit/show/$id");
		}else{
			logusu('ocompra',"Comprometio Orden de Compra Nro $id. con ERROR:$error ");
			$data['content'] = $error.anchor("presupuesto/ocompra/dataedit/show/$id",'Regresar');
			$data['title']   = " Orden de Compra ";
			$data["head"]    = $this->rapyd->get_head().script('jquery.js').script("plugins/jquery.numeric.pack.js");
			$this->load->view('view_ventanas', $data);
		}
	}

	function reversar($id){
		$error=$this->co_reversar($id);

		if(empty($error)){
			logusu('ocompra',"Descomprometio Orden de Compra Nro $id");
			if($this->redirect)redirect("presupuesto/ocompra/dataedit/show/$id");
		}else{
			logusu('ocompra',"Descomprometio Orden de Compra Nro $id. con ERROR:$error ");
			$data['content'] = $error.anchor("/presupuesto/ocompra/dataedit/show/$id",'Regresar');
			$data['title']   = " Reversar Compromiso ";
			$data["head"]    = $this->rapyd->get_head();
			$this->load->view('view_ventanas', $data);
		}
	}

	function anular($id){
		$this->rapyd->load('dataobject');

		$do = new DataObject("ocompra");
		$do->load($id);
		$do->set('status','A');

		$do->save();

		logusu('ocompra',"Anulo Orden de Compra Nro $id");
		redirect("presupuesto/ocompra/dataedit/show/$id");
	}

	function _ivaplica($mfecha=NULL){
		if(empty($mfecha)) $mfecha=date('Ymd');
		$qq = $this->datasis->damerow("SELECT tasa AS g, redutasa AS r, sobretasa AS a FROM civa WHERE fecha < '$mfecha' ORDER BY fecha DESC LIMIT 1");
		$rr=array();
		$rr['0']='0%';
		foreach ($qq AS $val){
			$rr[$val]=$val.'%';
		}

		return $rr;
	}

	function duplicar($numero){
		$this->rapyd->load('dataobject','datadetails');
		
		
		$do = new DataObject("ocompra");
		$do->order_by('itocompra','itocompra.id','desc');
		$do->rel_one_to_many('itocompra', 'itocompra', array('numero'=>'numero'));
		$do->load($numero);
		
		$donew = new DataObject("ocompra");
		$donew->rel_one_to_many('itocompra', 'itocompra', array('numero'=>'numero'));
		
		$campos = $do->get_all();
		unset($campos['itocompra']);
		foreach($campos as $k=>$v){
			$donew->set($k,$v);
		}
		
		
		$ntransac = $this->datasis->fprox_numero('ntransac');
		$donew->set('numero','_'.$ntransac);
		$donew->set('status','M');
		//$donew->pk    =array('numero'=>'_'.$ntransac);
		
		for($i=0;$i < $do->count_rel('itocompra');$i++){
			$donew->set_rel('itocompra','numero','_'.$ntransac,$i);
			$donew->set_rel('itocompra','id'    ,''           ,$i);
			$donew->set_rel('itocompra','descripcion',$do->get_rel('itocompra','descripcion',$i)          ,$i);
			$donew->set_rel('itocompra','unidad'     ,$do->get_rel('itocompra','unidad'     ,$i)          ,$i);
			$donew->set_rel('itocompra','cantidad'   ,$do->get_rel('itocompra','cantidad'   ,$i)          ,$i);
			$donew->set_rel('itocompra','precio'     ,$do->get_rel('itocompra','precio'     ,$i)          ,$i);
			$donew->set_rel('itocompra','importe'    ,$do->get_rel('itocompra','importe'    ,$i)          ,$i);
			$donew->set_rel('itocompra','iva'        ,$do->get_rel('itocompra','iva'        ,$i)          ,$i);
			$donew->set_rel('itocompra','usaislr'    ,$do->get_rel('itocompra','usaislr'    ,$i)          ,$i);
			$donew->set_rel('itocompra','islr'       ,$do->get_rel('itocompra','islr'       ,$i)          ,$i);
			$donew->set_rel('itocompra','partida'    ,$do->get_rel('itocompra','partida'    ,$i)          ,$i);
			$donew->set_rel('itocompra','preten'     ,$do->get_rel('itocompra','preten'     ,$i)          ,$i);
			$donew->set_rel('itocompra','codigoadm'  ,$do->get_rel('itocompra','codigoadm'  ,$i)          ,$i);
			$donew->set_rel('itocompra','fondo'      ,$do->get_rel('itocompra','fondo'      ,$i)          ,$i);
			$donew->set_rel('itocompra','esiva'      ,$do->get_rel('itocompra','esiva'      ,$i)          ,$i);
		}
		
		$donew->save();
		
		redirect($this->url.'dataedit/modify/_'.$ntransac);
	}

	function validac(&$do){
		$error        = '';
		$rr           = $this->ivaplica2();
		$reteiva_prov = $do->get('reteiva_prov');
		$reten        = $do->get('reten'       );
		$tipo         = $do->get('tipo'        );
		$status       = $do->get('status'      );
		$cod_prov     = $do->get('cod_prov'    );
		$numero       = $do->get('numero'      );
		$fondo        = $do->get('fondo'       );
		$redondear    = $do->get('redondear'   );
		$fondoa       = $do->get('fondo'       );
		$obligapiva   = $this->datasis->traevalor('obligapiva');
		$fondoe       = $this->db->escape($fondo);

		$partidaiva=$this->datasis->dameval("SELECT partidaiva FROM fondo WHERE fondo=$fondoe");
		if(empty($partidaiva))
		$partidaiva   = $this->datasis->traevalor("PARTIDAIVA");

		if(empty($numero)){
			$ntransac = $this->datasis->fprox_numero('ntransac');
			$do->set('numero','_'.$ntransac);
			$do->pk    =array('numero'=>'_'.$ntransac);
		}

		$cod_prov2    = $this->db->escape($cod_prov);
		$pr           = $this->datasis->damerow("SELECT proveed,nombre,reteiva FROM sprv WHERE proveed =$cod_prov2 ");
		if(!empty($pr)){
			if($cod_prov==$pr['proveed']){
				//$do->set_pointer('nombrep',$pr['nombre']);
				if(round($pr['reteiva'],2)==round(75,2)){
					//exit($pr['reteiva']);
					$do->set('reteiva_prov',$pr['reteiva']);
				}else
					$do->set('reteiva_prov',100);
			}else{
				$error = ("El Proveedor ($cod_prov) no esta registrado. Por Favor Registrelo");
				$do->error_message_ar['pre_upd']=$error;
				$do->error_message_ar['pre_ins']=$error;
				return false;
			}
		}else{
			$error = ("El Proveedor ($cod_prov) no esta registrado. Por Favor Registrelo");
			$do->error_message_ar['pre_upd']=$error;
			$do->error_message_ar['pre_ins']=$error;
			return false;
		}

		$usr=$this->session->userdata('usuario');
		$name = $this->datasis->dameval("SELECT us_nombre FROM usuario WHERE us_codigo ='$usr' ");
		$do->set('user',$usr);
		$do->set('username',$name);

		if($tipo == 'Compra' || $tipo=="Compromiso"){
			$do->set('creten','');
			$do->set('reten' ,0);
		}

		$error= '';

		$tretener=$giva=$aiva=$riva=$exento=$reteiva=$subtotal=$mivag=$mivar=$mivaa=$tivag=$tivar=$tivaa=$subt=$ivasm=$totiva=0;

		$admfondo=array();$admfondop=array();$borrarivas=array();$ivasm=0;$totiva=0;

		for($i=0;$i < $do->count_rel('itocompra');$i++){
			if(empty($numero)){
				$do->set_rel('itocompra','numero','_'.$ntransac,$i);
			}

			$codigoadm  = $do->get_rel('itocompra','codigoadm'  ,$i);
			$partida    = $do->get_rel('itocompra','partida'    ,$i);
			$ordinal    = $do->get_rel('itocompra','ordinal'    ,$i);
			$piva       = $do->get_rel('itocompra','iva'        ,$i);
			$esiva      = $do->get_rel('itocompra','esiva'      ,$i);
			$fondo      = $do->get_rel('itocompra','fondo'      ,$i);
			if(empty($fondo)){
				$do->set_rel('itocompra','fondo' ,$fondoa       ,$i);
				$fondo=$fondoa;
			}

			//$importe    = round($importe,2);
			//$cantidad   = round($do->get_rel('itocompra','cantidad'   ,$i),2);
			$cantidad   =$do->get_rel('itocompra','cantidad'   ,$i);
			//$precio     = round($do->get_rel('itocompra','precio'     ,$i),2);
			$precio     = $do->get_rel('itocompra','precio'     ,$i);
			//$importe = round($importe,2);
			if($redondear=='R0'){
				$importe    = $do->get_rel('itocompra','importe'    ,$i);
				$ivan       = ($importe*$piva)/100;
			}
			else{
				$importe    = $do->get_rel('itocompra','importe'    ,$i);
				$ivan       = round($importe*$piva/100,2);
			}

			//if($esiva!='A')
			//$error.=$this->itpartida($codigoadm,$fondo,$partida,$ordinal);

			if($esiva=='S'){
				$ivasm=$importe+$ivasm;
			}elseif($esiva=='A'){
				$borrarivas[$i]=$i;
			}else{
				$totiva+=$ivan;
				$a=$cantidad*$precio;
				if($tipo=='Compromiso'){
					 $do->set_rel('itocompra'  ,'cantidad',1        ,$i);
					 $do->set_rel('itocompra'  ,'precio'  ,$importe ,$i);
					 $do->set_rel('itocompra'  ,'iva'     ,0        ,$i);
				}else{
					if((($a-$importe)>0.05) || (($importe-$a) > 0.05))
					$error.="<div class='alert'>El Importe Introducido es incorrecto.</div>";
				}

				$do->set_rel('itocompra','importe' ,$importe,$i);

				$subtotal =$importe+round($subtotal,2);


				if($redondear=='R0')
				$ivan  = $ivan    ;
				else
				$ivan  = round($ivan,2);

				if($piva==$rr['tasa']     ){
					if($redondear=='R0')
					$giva  = ($rr['tasa'] *$importe)/100 + $giva;
					else
					$giva  = round(($rr['tasa'] *$importe)/100 + $giva,2);

					$mivag = $importe                    + $mivag;
				}
				if($piva==$rr['redutasa'] ){
					if($redondear=='R0')
					$riva  =($rr['redutasa'] *$importe)/100+$riva;
					else
					$riva  =round($rr['redutasa'] *$importe,2)/100+$riva;

					$mivar =$importe                       +$mivar;
				}
				if($piva==$rr['sobretasa']){
					if($redondear=='R0')
					$aiva =($rr['sobretasa']*$importe)/100+$aiva;
					else
					$aiva =round($rr['sobretasa']*$importe,2)/100+$aiva;

					$mivaa=$importe                       +$mivaa;
				}



				if($piva==0)$exento+=$importe;

				

				$presupiva=$this->datasis->dameval("SELECT (aumento+asignacion-disminucion+traslados-(comprometido)) FROM presupuesto WHERE codigoadm='$codigoadm' AND tipo='$fondo' AND codigopres='$partidaiva'");
				//exit($presupiva);
				if($obligapiva=='S')
					$partida2=$partidaiva;
				else {
					if($presupiva>0 )
					$partida2=$partidaiva;
					else
					$partida2=$partida;

				}


//				$ivan=$giva+$riva+$aiva;
				//if($obligapiva!='S'){
					$cadena3 = $codigoadm.'_._'.$fondo.'_._'.$partida2;
					$admfondop[$cadena3]=(array_key_exists($cadena3,$admfondop)?$admfondop[$cadena3]+=$ivan:$admfondop[$cadena3] =$ivan);
				//}else{
					$cadena2 = $codigoadm.'_._'.$fondo;
					$admfondo[$cadena2]=(array_key_exists($cadena2,$admfondo)?$admfondo[$cadena2]+=$ivan:$admfondo[$cadena2] =$ivan);
			}
		}

		if($ivasm>0){
			if(round($totiva,2)!=round($ivasm,2))
				$error.="La suma de las partidas a descontar ($ivasm) el IVA debe ser igual a la suma del IVA del IVA total ($totiva)";
		}else{


			$admfondo2 = ($obligapiva!='S'?$admfondop:$admfondo);
		//print_r($admfondop);
		//exit();
			foreach($admfondo2 AS $cadena=>$monto){

				if($monto>0){

					$temp  = explode('_._',$cadena);
					$fondoe=$this->db->escape($temp[1]);
					if($obligapiva!='S'){
						$p=$temp[2];
					}elseif(strlen($p2=$this->datasis->dameval("SELECT partidaiva FROM fondo WHERE fondo=$fondoe"))>0){
						$p=$p2;
					}else{
						$p=$partidaiva;
					}
					$i++;
					$do->set_rel('itocompra','codigoadm'   ,$temp[0]    ,$i);
					$do->set_rel('itocompra','fondo'       ,$temp[1]    ,$i);
					$do->set_rel('itocompra','partida'     ,$p          ,$i);
					$do->set_rel('itocompra','descripcion' ,'IVA'       ,$i);
					$do->set_rel('itocompra','unidad'      ,'MONTO'     ,$i);
					$do->set_rel('itocompra','iva'         , 0          ,$i);
					$do->set_rel('itocompra','esiva'       ,'A'         ,$i);
					$do->set_rel('itocompra','importe'     ,$monto      ,$i);
					$do->set_rel('itocompra','cantidad'    ,1           ,$i);
					$do->set_rel('itocompra','precio'      ,$monto      ,$i);
					//$error.=$this->itpartida($temp[0],$temp[1],$p);
				}
			}
		}

		//print_r($do->data_rel['itocompra']);
		$borrarivas=array_reverse($borrarivas,true);
		foreach($borrarivas AS $value){
		//	echo $value;
			array_splice($do->data_rel['itocompra'],$value,1);
		//	print_r($do->data_rel['itocompra']);
		}

		if($reteiva_prov!=75)$reteiva_prov=100;

		$reteiva=((round($giva,2)+round($riva,2)+round($aiva,2))*$reteiva_prov)/100;

		$total2=$giva+$riva+$aiva+$subtotal;
		$total =round($total2,2)-round($reteiva,2)-round($reten,2);

		$impm=$impt=0;

		if($tipo=='T' || $tipo=='N'){
			$impm     = 0;
			$impt     = 0;
			$tiva     = 0;
			$giva     = 0;
			$riva     = 0;
			$aiva     = 0;
			$mivag    = 0;
			$mivar    = 0;
			$mivaa    = 0;
			$exento   = 0;
			$reteiva  = 0;
			$total    = $subtotal;
			$total2   = $subtotal;
			$do->set('reten'         , 0    );
			$do->set('reteiva'       , 0    );
			$do->set('factura'       , ''   );
			$do->set('controlfac'    , ''   );
			$do->set('fechafac'      , ''   );
		}

		$impm=$impt=0;

		if($do->get('simptimbre') == 'S')
			$total  -=round($impt=($subtotal *$this->datasis->traevalor('IMPTIMBRE')/100),2);
			
		$fecha = $do->get('fecha');

		$do->set('imptimbre'     ,  $impt               );
		$do->set('ivag'          , $giva                );
		$do->set('ivar'          , $riva                );
		$do->set('ivaa'          , $aiva                );
		$do->set('tivag'         , $rr['tasa']          );
		$do->set('tivar'         , $rr['redutasa']      );
		$do->set('tivaa'         , $rr['sobretasa']     );
		$do->set('mivag'         , $mivag               );
		$do->set('mivar'         , $mivar               );
		$do->set('mivaa'         , $mivaa               );
		if($status!='P')
		$do->set('status'        , "M"                  );
		$do->set('subtotal'      , $subtotal            );
		$do->set('exento'        , $exento              );
		$do->set('reteiva'       , $reteiva             );
		$do->set('total'         , $total               );
		$do->set('total2'        , $total2              );
		$do->set('reten'         , $reten               );
		$do->set('fcomprome'     , $fecha               );
		

		if(!empty($error)){
			return $error;
		}
	}

	function _valida($do){
		$error = $this->validac($do);

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

	function positivo($valor){
		if ($valor <= 0){
			$this->validation->set_message('positivo',"El campo Precio debe ser positivo");
			return FALSE;
		}
		return TRUE;
	}

	function _post($do){
		$id=$do->get('numero');
		redirect("presupuesto/ocompra/actualizar/$id");
	}

	function ordinal(){
		$partida=$this->input->post('partida');
		echo "<option value=''></option>";
		if($partida!==false){

			$query=$this->db->query("SELECT ordinal, ordinal denominacion FROM ordinal WHERE codigopres='$partida'");
			if($query){
				if($query->num_rows()>0){
					//echo "<option value=''>Seleccionar</option>";
					foreach($query->result() AS $fila ){
						echo "<option value='".$fila->ordinal."'>".$fila->denominacion."</option>";
					}
				}
			}
		}
	}

	function terminada($numero,$retorna='ocompra'){
		$this->rapyd->load('dataobject');

		$do = new DataObject("ocompra");
		$do->rel_one_to_many('itocompra', 'itocompra', array('numero'=>'numero'));
		$do->load($numero);
		$numeroa =$numero;
		//echo ">>>>>".$numero.strpos($numero,'_')."<<<<<";
		//exit();
		if(strpos($numero,'_')===0){
			//exit('Por aqui paso');
			$tipo = $do->get('tipo');
			
			switch($tipo){
				case 'Compra'    :{$tabla='nocompra';$prefijo='OC';break;     }
				case 'Servicio'  :{$tabla='nocompraservi';$prefijo='OS';break;}
				case 'Compromiso':{$tabla='nocompracom';$prefijo='CO';break;  }
				case 'Contrato'  :{$tabla='nocompracom';$prefijo='CO';break;  }
				case 'Trabajo'   :{$tabla='nocompratra';$prefijo='OT';break;  }
				case 'Ejec.Obra' :{$tabla='nocompraobra';$prefijo='EO';break;  }
				case 'Cont.Marco':{$tabla='nocompramarco';$prefijo='CM';break;  }
			}

			$contador = $this->datasis->fprox_numero($tabla);
			$do->set('numero',$prefijo.$contador);
			//$do->pk=array('numero'=>$prefijo.$contador);
			for($i=0;$i < $do->count_rel('itocompra');$i++){
				$do->set_rel('itocompra','id'    ,''       ,$i);
				$do->set_rel('itocompra','numero',$prefijo.$contador,$i);
			}
			$this->db->query("DELETE FROM itocompra WHERE numero='$numero'");
			$numero=$prefijo.$contador;

			$this->db->query("UPDATE itfac SET nocompra='".$prefijo.$contador."' WHERE nocompra='$numeroa'");
		}

		$status = $do->get('status');
		if($status=='M'){
			$do->set('status','P');
			$do->save();
		}

		logusu('ocompra',"Marco Orden de Compra como Terminada Nro $numero");
		redirect("presupuesto/$retorna/dataedit/show/$numero");
	}

	function noterminada($numero,$retorna='ocompra'){
		$this->rapyd->load('dataobject');

		$do = new DataObject("ocompra");
		$do->load($numero);
		$status = $do->get('status');
		if($status=='P'){
			$do->set('status','M');
			$do->save();
		}

		logusu('ocompra',"Marco Orden de Compra como no Terminada Nro $numero");
		redirect("presupuesto/$retorna/dataedit/show/$numero");
	}

	function reversarall(){
		$query = $this->db->query("SELECT * FROM ocompra WHERE status = 'C' ");
		$result = $query->result();
		 foreach ($result AS $items){
		 	$numero =$items->numero;
		 	$this->reversar($numero);
		 }
	}

	function actualizarall(){
		$query = $this->db->query("SELECT * FROM ocompra WHERE status = 'P' ");
		$result = $query->result();
		 foreach ($result AS $items){
		 $numero =$items->numero;
		 	$this->actualizar($numero);
		 }
	}

	function creacdisp($numero){

		$ntransac = '_'.$this->datasis->fprox_numero('ntransac');

		$query="INSERT INTO `cdisp` (`numero`,`fecha`,`ano`,`tipo`,`status`) VALUES ('$ntransac',".date('Ymd').",".date('Y').",'O','O')";
		$this->db->query($query);

		$query="
		INSERT INTO itcdisp(numero,codigoadm,fondo,codigopres,ordinal,soli)
			SELECT  '$ntransac',codigoadm, fondo,partida,ordinal,sum(monto) as monto FROM (
				SELECT a.codigoadm, a.fondo,a.partida,a.ordinal,
				sum(a.importe) as monto
				FROM itocompra a JOIN ocompra b ON a.numero=b.numero
				WHERE b.numero = '$numero'
				GROUP BY a.codigoadm, a.fondo ,a.partida,ordinal
			)a
			GROUP BY codigoadm, fondo,partida ,ordinal
		";

		$this->db->query($query);

		redirect("presupuesto/cdisp/dataedit/modify/$ntransac");

	}

	function selformat($numero){
		$this->rapyd->load('dataobject');

		$do = new DataObject("ocompra");
		$do->load($numero);
		$tipo = $do->get('tipo');
		switch($tipo){
		case 'Compra'  :{redirect("/forma/ver/OCOMPRA/$numero");break;}
		case 'Servicio':{redirect("/forma/ver/OCOMPRA/$numero");break;}
		case 'Compromiso':{redirect("/forma/ver/COMPROMISO/$numero");break;}
		case 'Trabajo':{redirect("/forma/ver/OCOMPRA/$numero");break;}
		case 'Contrato':{redirect("/forma/ver/OCOMPRA/$numero");break;}
		case 'Ejec.Obra' :{redirect("/forma/ver/OCOMPRA/$numero");break;  }
		case 'Cont.Marco':{redirect("/forma/ver/OCOMPRA/$numero");break;  }
		}
	}

	function modrete(){
		$this->rapyd->load("dataedit","dataobject");

		$imptimbre = $this->datasis->traevalor('IMPTIMBRE');

		$script='
		$(function(){
			$("#simptimbre").click(function(){
				s=$("#simptimbre").is(":checked");
				if(s==true){
					a=$("#subtotal").val()*'.$imptimbre.'/100;
					$("#imptimbre").val(Math.round(a*100)/100);
				}else{
						$("#imptimbre").val(0);
				}
			});

			$("#creten").change(function(){

			});

			$("#reteiva_prov").change(function(){

				reteiva_prov=parseFloat($("#reteiva_prov").val());

				if((isNaN(reteiva_prov)) || (reteiva_prov==0) || (reteiva_prov=="") || (reteiva_prov==100))
					reteiva_prov=100;
				else
					reteiva_prov=75;

				giva=parseFloat($("#ivag").val());
				riva=parseFloat($("#ivar").val());
				aiva=parseFloat($("#ivaa").val());

				iva=giva+riva+aiva;

				reteiva=Math.round((((iva)*reteiva_prov)/100)*100)/100;

				$("#reteiva").val(reteiva);
			});
		});
		';

		$do = new DataObject("ocompra");
		$do->pointer('sprv' ,'sprv.proveed = ocompra.cod_prov','sprv.nombre as nombrep');

		$edit = new DataEdit("Orden de Compra", $do);
		$edit->back_url = site_url("tesoreria/retenciones/filtrorete");
		$edit->script($script, "modify");

		$edit->numero  = new inputField("N&uacute;mero", "numero");
		$edit->numero->mode  ="autohide";
		$edit->numero->when  =array('show');
		$edit->numero->group = "Datos";

		$edit->factura  = new inputField("Factura", "factura");
		$edit->factura->size  =15;
		$edit->factura->mode  = "autohide";
		$edit->factura->group = "Datos";

		$edit->controlfac  = new inputField("Control Fiscal", "controlfac");
		$edit->controlfac->size=15;
		$edit->controlfac->mode  = "autohide";
		//$edit->controlfac->rule="required";
		$edit->controlfac->group = "Datos";

		$edit->fechafac = new  dateonlyField("Fecha de Factura",  "fechafac");
		$edit->fechafac->insertValue = date('Y-m-d');
		$edit->fechafac->size =12;
		$edit->fechafac->mode  = "autohide";
		//$edit->fechafac->rule="required";
		$edit->fechafac->group = "Datos";

		$edit->cod_prov = new inputField("Beneficiario", 'cod_prov');
		$edit->cod_prov->size  = 6;
		$edit->cod_prov->mode  ="autohide";
		$edit->cod_prov->group = "Datos";

		$edit->nombrep = new inputField("Nombre", 'nombrep');
		$edit->nombrep->mode      = "autohide";
		$edit->nombrep->pointer   = true;
		$edit->nombrep->in        = "cod_prov";
		//$edit->nombrep->group = "Datos";

		$edit->subtotal = new inputField("Sub Total", 'subtotal');
		$edit->subtotal->size = 8;
		//$edit->subtotal->mode ="autohide";
		$edit->subtotal->group    = "Datos";
		$edit->subtotal->readonly =true;

		$edit->ivaa = new inputField("IVA Sobre Tasa", 'ivaa');
		$edit->ivaa->size = 8;
		//$edit->ivaa->mode ="autohide";
		$edit->ivaa->group = "Datos";
		$edit->ivaa->readonly =true;

		$edit->ivag = new inputField("IVA Tasa General", 'ivag');
		$edit->ivag->size = 8;
		//$edit->ivag->mode ="autohide";
		$edit->ivag->group = "Datos";
		$edit->ivag->readonly =true;

		$edit->ivar = new inputField("IVA Tasa reducida", 'ivar');
		$edit->ivar->size = 8;
		//$edit->ivar->mode ="autohide";
		$edit->ivar->group = "Datos";
		$edit->ivar->readonly =true;

		$edit->exento = new inputField("Exento", 'exento');
		$edit->exento->size = 8;
		$edit->exento->mode ="autohide";
		$edit->exento->group = "Datos";

		$edit->total = new inputField("Total a Pagar", 'total');
		$edit->total->size     = 8;
		$edit->total->readonly = true;
		$edit->total->group = "Datos";

		$edit->total2 = new inputField("Total", 'total2');
		$edit->total2->size = 8;
		$edit->total2->mode ="autohide";
		$edit->total2->group = "Datos";

		$edit->simptimbre = new checkboxField("1X1000", "simptimbre", "S","N");
		$edit->simptimbre->insertValue = "N";
		//$edit->simptimbre->onchange    ='cal_total();';
		$edit->simptimbre->group    = "Retenci&oacute;n de Impuesto al Timbre Fiscal";

		$edit->imptimbre= new inputField("Impuesto 1X1000", 'imptimbre');
		$edit->imptimbre->size     = 8;
		$edit->imptimbre->css_class='inputnum';
		$edit->imptimbre->readonly = true;
		$edit->imptimbre->group    = "Retenci&oacute;n de Impuesto al Timbre Fiscal";

		$edit->creten = new dropdownField("C&oacute;digo ISLR","creten");
		//$edit->creten->mode   = "autohide";
		//$edit->creten->option("","");
		$edit->creten->options("SELECT codigo,CONCAT_WS(' ',codigo,activida) FROM rete ORDER BY codigo");
		$edit->creten->style    = "width:300px;";
		//$edit->creten->onchange = 'cal_total();';
		$edit->creten->group    = "Retencion de Impuesto Sobre la renta";

		$edit->reten = new inputField("Retencion de ISLR", 'reten');
		$edit->reten->size     = 8;
		$edit->reten->readonly = true;
		//$edit->reten->mode ="autohide";
		$edit->reten->group    ="Retencion de Impuesto Sobre la renta";

		$edit->reteiva_prov = new dropdownField("Retenci&oacute;n de IVA %","reteiva_prov");
		$edit->reteiva_prov->option("100","100%");
		$edit->reteiva_prov->option("75" ,"75%");
		$edit->reteiva_prov->style    ="width:70px;";
		//$edit->reteiva_prov->onchange ='cal_total();';
		$edit->reteiva_prov->group         = "Retencion de IVA";

		$edit->reteiva = new inputField("Retencion de IVA", 'reteiva');
		$edit->reteiva->size     = 8;
		$edit->reteiva->readonly = true;
		$edit->reteiva->group    = "Retencion de IVA";

		$edit->buttons("modify", "save", "undo", "back");
		$edit->build();

		$data['content'] = $edit->output;
		$data['title']   = "Orden de Compra";
		$data["head"]    = script("jquery.js").script("plugins/jquery.numeric.pack.js").script("plugins/jquery.floatnumber.js").$this->rapyd->get_head();
		$this->load->view('view_ventanas', $data);
	}

	function sipresocompra(){
		$this->rapyd->load("dataform");

		$filter = new DataForm($this->url."siprescrearocompra");

		$filter->compromiso = new inputField("Numero de Compromiso", "compromiso");
		$filter->compromiso->size =10;
		$filter->compromiso->rule ='required';

		$filter->aplica = new inputField("Numero de Documento (indique el numero de OC/OS del sistema tortuga de ser necesario)", "aplica");
		$filter->aplica->size =10;

		$filter->submit("btnsubmit","Crear Compromiso");

		$filter->build_form();

		$data['content'] = $filter->output.anchor($this->url,'Ir atras');
		$data['title']   = "Crear Compromiso  a partir de un compromiso de sipres";
		$data["head"]    = $this->rapyd->get_head();
		$this->load->view('view_ventanas', $data);
	}

	function siprescrearocompra(){

	//reversar compromiso
	//chequear que el status se ejecutado*

		$error       = '';
		$ntransac    ='';
		$abonado     =0;
		$salida      =anchor($this->url.'sipresocompra','Ir a Crear Compromiso a partir de un compromiso de sipres');
		$compromiso  = $this->input->post("compromiso");
		$compromisoe = $this->db->escape($compromiso);
		$cnomi       = $this->datasis->dameval("SELECT numero FROM nomi WHERE compromiso=$compromisoe AND status IN ('O','C','D','E') LIMIT 1");
		$cocompra    = $this->datasis->dameval("SELECT numero FROM ocompra WHERE compromiso=$compromisoe AND status IN ('C','T','O','E') LIMIT 1");
		$cant        = $this->datasis->dameval("SELECT COUNT(*) FROM view_sipres WHERE compromiso=$compromisoe");

		$aplica      = $this->input->post("aplica");
		if(!empty($aplica)){
			$aplicae     = $this->db->escape($aplica);
			$aplicae2    = $this->db->escape($aplica.'%');
			$ccompra     = $this->datasis->dameval("SELECT COUNT(*) FROM ocompra WHERE numero=$aplicae LIMIT 1");
			$ccompra2    = $this->datasis->dameval("SELECT COUNT(*) FROM ocompra WHERE numero LIKE $aplicae2 LIMIT 1");
			if($ccompra==0){
				$error.="El numero $aplicae de OC/OS no existe</br>";
			}else{
				$ntransac=$aplica.'-'.$ccompra2;
			}
		}

		if(strlen($cnomi)>0)
		$error.="El compromiso $compromisoe ya existe para el compromiso de nomina numero $cnomi </br>";

		if(strlen($cocompra)>0)
		$error.="El compromiso $compromisoe ya existe para el compromiso de ordenes numero $cocompra </br>";

		if(empty($error) && $cant>0){
			if(empty($ntransac))
			$ntransac ='CO'.$this->datasis->fprox_numero('nocompracom');
			$this->db->simple_query("INSERT IGNORE INTO sprv (proveed,nombre) values ('00000','Compromiso')");
			$this->db->simple_query("INSERT IGNORE INTO uejecutora (codigo,nombre) values ('0000','Compromiso')");

			if(empty($aplica))
			$query  ="INSERT INTO ocompra (uejecutora,cod_prov,numero,compromiso,fecha,observa,status,fondo,tipo) SELECT '0000','00000','$ntransac',compromiso,fecha,concepto,'P',b.tipo,'Compromiso' FROM view_sipres a LEFT JOIN presupuesto b ON a.codigoadm=b.codigoadm AND a.codigopres=b.codigopres WHERE a.compromiso=$compromisoe GROUP BY compromiso";
			else{
				$abonado=$this->datasis->dameval("SELECT SUM(total2) FROM  ocompra WHERE status IN ('C','T','O','E') AND numero LIKE $aplicae2");
				$abono  =$this->datasis->dameval("SELECT SUM(a.monto)
				FROM ocompra c,view_sipres a
				LEFT JOIN presupuesto b ON a.codigoadm=b.codigoadm AND a.codigopres=b.codigopres
				JOIN estruadm d ON b.codigoadm=d.codigo
				WHERE a.compromiso=$compromisoe AND c.numero=$aplicae
				");
				$totalo  = $this->datasis->dameval("SELECT total2 FROM ocompra WHERE numero=$aplicae ");
				$ar=round($totalo-$abonado,2);
				if(round($totalo-$abonado,2)<round($abono,2))
				$error.="El monto a comprometer $ar es el mayor al monto adeudado $abono para la OS/OS $aplicae";

				$query="INSERT INTO ocompra (uejecutora,cod_prov,numero,compromiso,fecha,observa,status,fondo,tipo,usolicita)
				SELECT d.uejecutora,c.cod_prov,'$ntransac',a.compromiso,a.fecha,c.observa,'P',b.tipo,c.tipo ,c.usolicita
				FROM ocompra c,view_sipres a
				LEFT JOIN presupuesto b ON a.codigoadm=b.codigoadm AND a.codigopres=b.codigopres
				JOIN estruadm d ON b.codigoadm=d.codigo
				WHERE a.compromiso=$compromisoe AND c.numero=$aplicae
				GROUP BY compromiso";
			}
			if(empty($error)){
				if(!$this->db->query($query) )
				$error.="No se Pudo Guardar el Compromiso";
				elseif(!empty($aplica))
				$this->db->query("UPDATE ocompra SET status='Z' WHERE numero=$aplicae AND status='P'");
			}
			if(empty($error)){
				$query  ="INSERT INTO itocompra  (id,numero,codigoadm,fondo,partida,unidad,cantidad,precio,importe,iva,esiva)  SELECT '' id,'$ntransac',b.codigoadm,c.tipo,b.codigopres,'MONTO',1,b.monto,b.monto,0,'N' FROM view_sipres b LEFT JOIN presupuesto c ON b.codigoadm=c.codigoadm AND b.codigopres=c.codigopres WHERE b.compromiso=$compromisoe GROUP BY b.codigoadm,b.codigopres ";
				if(!$this->db->query($query))
				$error.="No se Pudieron guardar los Items del Compromiso";
			}
		}else{
			$error.="El numero de Compromiso no existe &oacute; no ha subido del sistema sipres";
		}

		if(empty($error)){
			$this->rapyd->load("dataobject");

			$do = new DataObject("ocompra");

			$do->rel_one_to_many('itocompra', 'itocompra', array('numero'=>'numero'));
			$do->load($ntransac);

			$error.=$this->validac($do);

			if(empty($error)){
				$do->save();
				redirect($this->url."actualizar/$ntransac");
			}else{
				$salida=anchor($this->url."dataedit/show/$numero",'Ir al Compromiso');
			}
		}

		if(empty($error)){
		}else{
			$data['content'] = "<div class='alert'>$error</div></br>".$salida;
			$data['title']   = "Crear Compromiso de Nomina a partir de un compromiso de sipres";
			$data["head"]    = $this->rapyd->get_head();
			$this->load->view('view_ventanas', $data);
		}
	}

	 function ingcert($valida=true,$status='',$numero=null){
		$this->rapyd->load("dataobject","dataedit2");

		$edit = new DataEdit2("Comprometer", "ocompra");
		
		$edit->back_url        = site_url($this->url."/dataedit/show/$numero");
		$edit->back_cancel     =true;
		$edit->back_cancel_save=true;

		if($valida!='NOVALIDA'){
			$edit->pre_process('update' ,'_valida_ingcert');
		}
		$edit->post_process('update','_post_update_ingcert');

		
		$edit->numero  = new inputField("N&uacute;mero", "numero");
		$edit->numero->mode="autohide";
		
		
		
		$edit->fecha = new  dateonlyField("Fecha",  "fecha");
		$edit->fecha->mode='autohide';

		
		$edit->fcomprome = new  dateonlyField("Fecha de Compromiso",  "fcomprome");
		$edit->fcomprome->size =12;
		//$edit->fcomprome->dbformat ='Ymd';
		//$edit->fcomprome->Value = $edit->getval('fecha');
		
		if($this->datasis->traevalor('USACOMPROMISO')=='S' ){
			$edit->compromiso = new inputField("Compromiso #", 'compromiso');
			$edit->compromiso->size = 40;
		}

		$edit->buttons("undo","back","save");
		$edit->build();

		$data['content'] = $edit->output;
		$data['title']   = "Comprometer";
		$data["head"]    = $this->rapyd->get_head().script('jquery.js').script("plugins/jquery.numeric.pack.js").script("plugins/jquery.json.min.js");
		$this->load->view('view_ventanas', $data);
	}


	function _valida_ingcert($do){
		$error='';

		$compromiso  =$do->get('compromiso');
		$compromisoe =$this->db->escape($compromiso);
		$cnomi       = $this->datasis->dameval("SELECT numero FROM nomi WHERE compromiso=$compromisoe AND status IN ('O','C','D','E') LIMIT 1");
		$cocompra    = $this->datasis->dameval("SELECT numero FROM ocompra WHERE compromiso=$compromisoe AND status IN ('C','T','O','E') LIMIT 1");
		$cant        = $this->datasis->dameval("SELECT COUNT(*) FROM view_sipres WHERE compromiso=$compromisoe");

		if(strlen($cnomi)>0)
		$error.="El compromiso $compromisoe ya existe para el compromiso de nomina numero $cnomi </br>";

		if(strlen($cocompra)>0)
		$error.="El compromiso $compromisoe ya existe para el compromiso de ordenes numero $cocompra </br>";

		if($cant=0)
		$error.="El numero de Compromiso no existe &oacute; no ha subido del sistema sipres";

		if(!empty($error)){
			$do->error_message_ar['pre_ins']=$error;
			$do->error_message_ar['pre_upd']=$error;
			return false;
		}
	}

	function _post_update_ingcert($do){
		$numero   =  $do->get('numero');
		redirect($this->url.'/actualizar/'.$numero);
	}

	function _pre_delete($do){
		$error  ='';
		$numero =$do->get('numero');
		$c      =$this->datasis->dameval("SELECT COUNT(*) FROM pacom WHERE compra='$numero'");

		if($c>0)
		$error.="ERROR. El Registro no puede ser eliminado debido a que tiene un Orden de Pago relacionada";

		if(!empty($error)){
			$do->error_message_ar['pre_del']=$error;
			return false;
		}
	}

	function _post_insert($do){
		$numero = $do->get('numero');
		logusu('ocompra',"Creo Orden de Compra Nro $numero");
		//redirect($this->url."actualizar/$numero");
	}

	function _post_update($do){
		$numero = $do->get('numero');
		logusu('ocompra'," Modifico Orden de Compra Nro $numero");
		//redirect($this->url."actualizar/$numero");
	}
	function _post_delete($do){
		$numero = $do->get('numero');
		logusu('ocompra'," Elimino Orden de Compra Nro $numero");
	}



	function prueba(){
		$total=77760;
		for($i=69000;$i<=$total;++$i){
			//echo "</br>".
			$t=$i+($i*12/100);//
			if($t>=$total)echo $i."</br>";
		}
	}

	function instalar(){
		$this->db->simple_query("ALTER TABLE `itocompra` CHANGE COLUMN `partida` `partida` VARCHAR(25) NULL DEFAULT NULL COMMENT 'Partida Presupuestaria'  ;");
		$this->db->simple_query("ALTER TABLE `itfac`  ADD COLUMN `nocompra` VARCHAR(12) NULL DEFAULT NULL");
		$this->db->simple_query("ALTER TABLE `ocompra`  ADD COLUMN `proced` VARCHAR(100) NULL");
		$this->db->simple_query("ALTER TABLE `ocompra`  ADD COLUMN `aplica` VARCHAR(20) NULL DEFAULT NULL");
		$this->db->simple_query("ALTER TABLE `ocompra`  ADD COLUMN `otrasrete` DECIMAL(19,2) NULL DEFAULT NULL");
		$this->db->simple_query("ALTER TABLE `ocompra`  ADD COLUMN `redondear` VARCHAR(2) NULL");
		$this->db->simple_query("ALTER TABLE `ocompra` ADD COLUMN `modalidad` VARCHAR(50) NULL DEFAULT '' AFTER `redondear`");
		$this->db->simple_query("ALTER TABLE `ocompra` ADD COLUMN `formaentrega` VARCHAR(50) NULL DEFAULT '' AFTER `modalidad`");
		$this->db->simple_query("ALTER TABLE `ocompra` ADD COLUMN `condiciones` TEXT NULL DEFAULT '' AFTER `formaentrega`");
	}
}
?>
