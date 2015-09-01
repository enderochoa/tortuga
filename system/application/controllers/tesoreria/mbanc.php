<?php
require_once(BASEPATH.'application/controllers/presupuesto/common.php');
class mbanc extends Common {

	var $titp='Movimientos Bancarios';
	var $tits='Movimiento Bancario';
	var $url ='tesoreria/mbanc/';
	var $on_save_redirect=TRUE;
	var $genesal=true;

	function mbanc(){
		parent::Controller();
		$this->load->library("rapyd");
	}

	function index(){
		redirect($this->url."filteredgrid");
	}

	function filteredgrid(){
		$this->datasis->modulo_id(35,1);
		$this->rapyd->load("datafilter","datagrid");
		//$this->rapyd->uri->keep_persistence();

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

		$mBANC=array(
				'tabla'   =>'banc',
				'columnas'=>array(
					'codbanc' =>'C&oacute;odigo',
					'banco'=>'Banco',
					'numcuent'=>'Cuenta',
					'saldo'=>'Saldo'),
				'filtro'  =>array(
					'codbanc' =>'C&oacute;odigo',
					'banco'=>'Banco',
					'saldo'=>'Saldo'),
				'retornar'=>array(
					'codbanc'=>'codbanc' ),
				'where'=>'activo = "S"',
				'titulo'  =>'Buscar Bancos'
				);

		$bBANC=$this->datasis->p_modbus($mBANC,"banc");

		$filter = new DataFilter("");

		$filter->db->select(array("a.observa","a.multiple","tipo_doc","a.cheque cheque","a.codbanc","a.id id","a.fecha fecha","a.tipo tipo","a.status status","a.cod_prov cod_prov","a.benefi benefi","a.monto monto" ,"IF(paid >0,paid,IF(LENGTH(pcodbanc)>0,'P',IF(deid>0,deid,''))) tras"));
		$filter->db->from("mbanc a");
		$filter->db->join("banc b","b.codbanc=a.codbanc","left");
		//$filter->db->where("a.status != ", "E1");
		//$filter->db->where("a.status != ", "E2");
		//$filter->db->where("a.status != ", "E3");


		$filter->id = new inputField("Referencia", "id");
		$filter->id->db_name ="a.id";
		$filter->id->size    =10;
		
		$filter->multiple = new inputField("Multiple", "multiple");
		$filter->multiple->db_name ="a.multiple";
		$filter->multiple->size    =10;

		$filter->cheque = new inputField("Transaccion", "cheque");
		$filter->cheque->db_name="a.cheque";
		$filter->cheque->size  =10;

		$filter->fecha = new dateonlyField("Fecha", "fecha");
		$filter->fecha->dbformat = "Y-m-d";
		$filter->fecha->size=12;

		$filter->cod_prov = new inputField("Beneficiario", 'cod_prov');
		$filter->cod_prov->size = 6;
		$filter->cod_prov->append($bSPRV);

		$filter->bcta = new inputField("Motivo Movimiento", 'bcta');
		$filter->bcta->size = 6;

		$filter->tipocta = new dropdownField("Tipo de Cuenta", "tipocta");
		$filter->tipocta->style ="width:100px;";
		$filter->tipocta->option("","");
		$mf=$this->datasis->puede(333);
		$mo=$this->datasis->puede(334);
		if(!($mf && $mo))
		$filter->tipocta->options(array("K"=>"Caja","C"=>"Corriente","A" =>"Ahorros","P"=>"Plazo Fijo" ));
		elseif($mf && $mo){
		    $filter->tipocta->option("F","FideComiso");
		    $filter->tipocta->options(array("K"=>"Caja","C"=>"Corriente","A" =>"Ahorros","P"=>"Plazo Fijo" ));
		}elseif($mf){
		    $filter->db->where("tipocta","F");
		    $filter->tipocta->option("F","FideComiso");
		}elseif($mo){
		    $filter->db->where("tipocta <>","F");
		    $filter->tipocta->options(array("K"=>"Caja","C"=>"Corriente","A" =>"Ahorros","P"=>"Plazo Fijo" ));
		}

		$filter->codbanc = new inputField("Banco", 'codbanc');
		$filter->codbanc->size = 6;
		$filter->codbanc->append($bBANC);
		$filter->codbanc->db_name="a.codbanc";

		$filter->monto = new inputField("Monto", 'monto');
		$filter->monto->size = 6;

        $filter->benefi = new inputField("A nombre de", 'benefi');
		$filter->benefi->size = 20;
		
		$filter->observa = new inputField("Concepto", 'observa');
		$filter->observa->size = 20;

		$filter->tipo_doc = new dropdownField("Tipo Doc","tipo_doc");
		$filter->tipo_doc->option("","");
		$filter->tipo_doc->option("ND","Nota de Debito");
		$filter->tipo_doc->option("NC","Nota de Credito");
		$filter->tipo_doc->option("CH","Cheque");
		$filter->tipo_doc->option("DP","Deposito");	
	

		$filter->status = new dropdownField("Estado","status");
		$filter->status->option("","");
		$filter->status->option("J1","Sin Ejecutar");
		$filter->status->option("J2","Ejecutado");
		$filter->status->option("A" ,"Anulado");
		$filter->status->style="width:150px";

		$filter->buttons("reset","search");

		$filter->build();
		$uri = anchor($this->url.'dataedit/show/<#id#>','<#id#>');
		$uri2= anchor($this->url.'modifica/modify/<#id#>','Modificar');
		$uri3= anchor($this->url.'multiple/show/<#multiple#>','<#multiple#>');
		$uri4= anchor($this->url.'modifica3/modify/<#id#>','Modificar');

		function sta($status){
			switch($status){
				case "J1":return "Sin Ejecutar";break;
				case "J2":return "Ejecutado";break;
				case "A":return "Anulado";break;
				case "AN":return "Anulado";break;
				case "NC":return "NC Anulacion de cheque";break;
				case "A2":return "Cheque Reverso";break;
			}
		}

		function tras($id,$puede,$tras=''){
			if($tras=='P'){
				if($puede=='S')
				return anchor("tesoreria/mbanc/trasla/$id","Recibir");
				else
				return "Por Recibir";

			}elseif($tras>0){
				return anchor("tesoreria/mbanc/dataedit/show/$tras",$tras);
			}else{
				return '';
			}
		}

		$grid = new DataGrid("");
		$grid->order_by('id','desc');
		$grid->per_page = 20;
		$grid->use_function('substr','str_pad');
		$grid->use_function('sta','tras');

		$grid->column_orderby("Referencia"       ,$uri                                            ,"id"                                );
		$grid->column_orderby("Multiple"         ,$uri3                                           ,"multiple"                              );
		$grid->column_orderby("Cheque"           ,"<wordwrap><#cheque#>|50|\n|true</wordwrap>"    ,"cheque"                                );
		$grid->column_orderby("Tipo"             ,"tipo_doc"                                      ,"tipo_doc"                              );
		$grid->column_orderby("Fecha"            ,"<dbdate_to_human><#fecha#></dbdate_to_human>"  ,"fecha"          ,"align='center'"      );
		$grid->column_orderby("Banco"            ,"codbanc"                                       ,"fecha"          ,"align='center'"      );
		$grid->column_orderby("Beneficiario"     ,"cod_prov"                                      ,"proveed"        ,"align='left'  "      );
		$grid->column_orderby("A nombre de"      ,"benefi"                                        ,"proveed"        ,"align='left'  "      );
		$grid->column_orderby("Pago"             ,"<nformat><#monto#>|2|,|.</nformat>","monto"    ,"align='right'"                         );
		$grid->column_orderby("Estado"           ,"<sta><#status#></sta>"                         ,"status"         ,"align='center'"      );
		$a=($this->datasis->puede(337)?'S':'N');
		$grid->column_orderby("Traslado"         ,"<tras><#id#>|$a|<#tras#></tras>"                         ,"tras"           ,"align='center'"      );

		if($this->datasis->puede(339) )
		$grid->column(""                 ,$uri2                                         ,"align='center'");
		if($this->datasis->puede(338) )
		$grid->column(""                 ,$uri4                                         ,"align='center'");


		if($this->datasis->puede(336)){
			$grid->add($this->url."dataedit/create");
			$grid->add($this->url."multiple/create","Agregar Multiple");
		}

		$grid->build();

		//$data['content'] = $filter->output.$grid->output;
		$data['filtro']  = $filter->output;
		$data['content'] = $grid->output;
		$data['script'] = script("jquery.js")."\n";
		$data['title']   = "$this->titp";
		$data["head"]    = $this->rapyd->get_head();
		$this->load->view('view_ventanas', $data);
	}

	function dataedit(){
		//$this->datasis->modulo_id(101,1);
		$this->rapyd->load('dataedit','dataobject');

		$where='activo = "S"';
		$mf=$this->datasis->puede(333);
		$mo=$this->datasis->puede(334);
		if($mf && $mo){

		}elseif($mf){
		    $where.=' AND tipocta="F"';
		}elseif($mo){
		    $where.=' AND tipocta<>"F"';
		}

		$mBANC=array(
				'tabla'   =>'banc',
				'columnas'=>array(
					'codbanc' =>'C&oacute;odigo',
					'banco'=>'Banco',
					'numcuent'=>'Cuenta',
					'saldo'=>'Saldo'),
				'filtro'  =>array(
					'codbanc' =>'C&oacute;odigo',
					'banco'=>'Banco',
					'saldo'=>'Saldo',
					'numcuent'=>'Cuenta'),
				'retornar'=>array(
					'codbanc'=>'<#i#>',
					'banco'  =>'<#j#>'),
				'where'=>$where,
				'p_uri'   =>array(4=>'<#i#>',5=>'<#j#>'),
				'titulo'  =>'Buscar Bancos'
				);

		$bBANC =$this->datasis->p_modbus($mBANC,"codbanc/nombreb",800,600,'banc'   );

		$pmBANC=array(
				'tabla'   =>'banc',
				'join'    =>array(
					array('tban','banc.tbanco=tban.cod_banc','LEFT')
					),
				'columnas'=>array(
					'codbanc' =>'C&oacute;odigo',
					'banco'=>'Banco',
					'numcuent'=>'Cuenta',
					'saldo'=>'Saldo'),
				'filtro'  =>array(
					'codbanc' =>'C&oacute;odigo',
					'banco'=>'Banco',
					'saldo'=>'Saldo',
					'numcuent'=>'Cuenta'),
				'retornar'=>array(
					'codbanc'=>'<#i#>',
					'banco'  =>'<#j#>',
					'numcuent' =>'cuentab',
					'nomb_banc'=>'nom_bancb'),
				'where'=>$where,
				'script'  =>array('conctrasla()'),
				'p_uri'   =>array(4=>'<#i#>',5=>'<#j#>'),
				'titulo'  =>'Buscar Bancos'
				);
		$bpBANC=$this->datasis->p_modbus($pmBANC,"pcodbanc/pnombreb",800,600,'pbanc'  );

		$mSPRV=array(
				'tabla'   =>'sprv',
				'columnas'=>array(
				'proveed' =>'C&oacute;digo',
				'nombre'=>'Nombre',
				'rif'=>'Rif',
				'contacto'=>'Contacto',
				'grupo'   =>'Grupo'),
				'filtro'  =>array('proveed'=>'C&oacute;digo','nombre'=>'Nombre','rif'=>'Rif'),
				'retornar'=>array('proveed'=>'cod_prov','nombre'=>'nombrep','concepto'=>'observa','contacto'=>'nombret','grupo'=>'tsprv'),
				'script'  =>array('copiabenefi()'),
				'titulo'  =>'Buscar Beneficiario');

		$bSPRV=$this->datasis->p_modbus($mSPRV,"proveed");

		$mBCTA=array(
                    'tabla'   =>'bcta',
                    'columnas'=>array(
                            'codigo'       =>'C&oacute;odigo',
                            'denominacion' =>'Denominacion',
                            'cuenta'       =>'Cuenta'),
                    'filtro'  =>array(
                            'codigo'       =>'C&oacute;odigo',
                            'denominacion' =>'Denominacion',
                            'cuenta'       =>'Cuenta'),
                    'retornar'=>array(
                            'codigo'       =>'bcta',
                            'denominacion' =>'bctad'),
                    'titulo'  =>'Buscar Otros Ingresos'
                    );

		$bBCTA=$this->datasis->p_modbus($mBCTA,"bcta");
		$link8=site_url($this->url.'sugerirch/');
		$script='
			$(".inputnum").numeric(".");

            $(document).ready(function() {
				$("#tr_nombret").hide();
				$("#tr_tsprv").hide();
			});

			function copiabenefi(){
				tsprv=$("#tsprv").val();
				nombret = $("#nombret").val();
				benefi = $("#nombrep").val();

				if(tsprv=="0001"){
					$("#benefi").val(" "+nombret);
				}else{
					$("#benefi").val(" "+benefi);

				}
			}

			function conctrasla(){
				observa =$("#observa").val();
				pnombreb = $("#pnombreb").val();
				cuentab = $("#cuentab").val();
				nom_bancb = $("#nom_bancb").val();

				$("#observa").val(observa+" UNICAMENTE PARA SER DEPOSITADO EN "+pnombreb+" CUENTA NRO "+cuentab+" DEL BANCO "+nom_bancb);
			}

			function ultimoch(){
				$.ajax({
					type: "POST",
					url: "'.site_url("tesoreria/desem/ultimoch").'",
					data:"codbanc="+$("#codbanc").val(),
					success: function(msg){
						$("#cheque").val(msg);
					}
				});
			}

			function sugerir(){

				$.ajax({
					url: "'.$link8.'",
					success: function(msg){
						if(msg){
							$("#cheque").val(msg);
						}
						else{
							alert("No es posible generar otra sugerencia. Coloque el c&oacute;digo manualmente");
						}
					}
				});
			}
		';

		$do = new DataObject("mbanc");
		$do->pointer('sprv'   ,'sprv.proveed = mbanc.cod_prov','sprv.nombre as nombrep'     ,'LEFT');
		$do->pointer('bcta'   ,'bcta.codigo =  mbanc.bcta'    ,'bcta.denominacion as bctad ','LEFT');
		$do->pointer('banc'   ,'banc.codbanc=mbanc.codbanc'   ,'banc.banco as nombreb'      ,'LEFT');
		$do->pointer('banc c' ,'c.codbanc=mbanc.pcodbanc'     ,'c.banco as pnombreb'        ,'LEFT');

		$edit = new DataEdit($this->tits, $do);

		$edit->back_url = site_url($this->url."filteredgrid");
		$edit->on_save_redirect=$this->on_save_redirect;

		$edit->script($script,"create");
		$edit->script($script,"modify");

		$edit->pre_process('insert'  ,'_valida');
		$edit->pre_process('update'  ,'_valida');
		$edit->post_process('insert','_post_insert');
		$edit->post_process('update','_post_update');
		$edit->post_process('delete','_post_delete');

		$tipo = $edit->_dataobject->get('tipo');

		$edit->id        = new inputField("N&uacute;mero", "id");
		$edit->id->mode  = "autohide";
		$edit->id->when  = array('show');

		//$edit->fechapago = new  dateonlyField("Fecha Movimiento",  "fechapago");
		//$edit->fechapago->insertValue = date('Y-m-d');
		//$edit->fechapago->size        =12;
		//$edit->fechapago->rule        = 'required';

		$edit->tipo = new inputField("","tipo");
		$edit->tipo-> insertValue = "E";
		$edit->tipo->mode         = "autohide";
		$edit->tipo->when=array('');

		$edit->cod_prov = new inputField("Beneficiario", 'cod_prov');
		$edit->cod_prov->size     = 6;
		//$edit->cod_prov->rule     = "required";
		$edit->cod_prov->append($bSPRV);
		//$edit->cod_prov->readonly=true;

		$edit->nombrep = new inputField("Nombre", 'nombrep');
		$edit->nombrep->size      = 50;
		$edit->nombrep->readonly  = true;
		$edit->nombrep->pointer   = true;
		$edit->nombrep->in        = "cod_prov";

		$edit->nombret = new inputField("nombre temporal", 'nombret');
		$edit->nombret->size      = 50;
		$edit->nombret->db_name   =' ';
		$edit->nombret->when      =array("create","modify");

		$edit->tsprv = new inputField("", 'tsprv');
		$edit->tsprv->size      = 50;
		$edit->tsprv->db_name   =' ';
		$edit->tsprv->when      =array("create","modify");

		$edit->bcta = new inputField("Motivo Movimiento", 'bcta');
		$edit->bcta->size     = 6;
		//$edit->bcta->rule     = "required";
		$edit->bcta->append($bBCTA);
		$edit->bcta->readonly=true;
		//$edit->bcta->group = "Deposito";

		$edit->bctad = new inputField("", 'bctad');
		$edit->bctad->size        = 50;
		//$edit->bctad->group       = "Deposito";
		$edit->bctad->in          = "bcta";
		$edit->bctad->pointer     = true;
		$edit->bctad->readonly    = true;

		$edit->codbanc =  new inputField("Banco", 'codbanc');
		$edit->codbanc-> size     = 3;
		$edit->codbanc-> rule     = "required|callback_chexiste_codbanc";
		$edit->codbanc-> append($bBANC);
		//$edit->codbanc-> readonly=true;
		$edit->codbanc->group    = "Transaccion";

		$edit->nombreb = new inputField("Nombre","nombreb");
		$edit->nombreb->size     = 50;
		$edit->nombreb->readonly = true;
		$edit->nombreb->pointer  = true;
		$edit->nombreb->in       = "codbanc";
		$edit->nombreb->group    = "Transaccion";

		$edit->tipo_doc = new dropdownField("Tipo Documento","tipo_doc"             );
		if($this->datasis->puede(288))$edit->tipo_doc->option("CH","Cheque"         );
		if($this->datasis->puede(289))$edit->tipo_doc->option("NC","Nota de Credito");
		if($this->datasis->puede(290))$edit->tipo_doc->option("ND","Nota de Debito" );
		if($this->datasis->puede(291))$edit->tipo_doc->option("DP","Deposito"       );
		$edit->tipo_doc->style  ="width:180px";
		$edit->tipo_doc->group  =  "Transaccion";
		$edit->tipo_doc->rule   = 'required';


		$rule='required';
		$sugerir='<a href="javascript:sugerir();" title="Sugerir un numero aleatorio">Sugerir Numero </a>';
		$edit->cheque =  new textareaField("Nro. Transacci&oacute;n", 'cheque');
		$edit->cheque->rows   = 3;
		$edit->cheque->cols   = 80;
		$edit->cheque->rule   = $rule;
		$edit->cheque->group  = "Transaccion";
		$edit->cheque->append($sugerir);

		$edit->fecha = new  dateonlyField("Fecha Transacci&oacute;n",  "fecha");
		//$edit->fecha->insertValue = date('Y-m-d');
		$edit->fecha->size    =12;
		$edit->fecha->rule    = 'required';
		$edit->fecha->group   = "Transaccion";

		$edit->monto = new inputField("Monto", 'monto');
		$edit->monto->size     = 20;
		$edit->monto->group    = "Transaccion";
		$edit->monto->css_class='inputnum';
		$edit->monto->rule     = 'callback_positivo|required';
		if($tipo=='I')
		$edit->monto->mode     = "autohide";

		$edit->benefi =  new inputField("A nombre de", 'benefi');
		$edit->benefi-> size  = 100;
		$edit->benefi->rule   = "required";
		$edit->benefi->group  = "Transaccion";

		$edit->observa = new textAreaField("Concepto", 'observa');
		$edit->observa->cols = 80;
		$edit->observa->rows = 5;
		$edit->observa->group    = "Transaccion";

		$edit->destino = new dropdownField("Destino","destino");
		$edit->destino->option("C","Caja"    );
		$edit->destino->option("I","Interno" );
		$edit->destino->style="width:100px";

		$edit->rel = new textAreaField("Relaciones", 'rel');
		$edit->rel->cols  = 60;
		$edit->rel->rows  = 3;
		$edit->rel->group = "Transaccion";
		$edit->rel->mode  = "autohide";
		$edit->rel->when  =array("show");

		$edit->status = new textAreaField("Estado", 'status');
		$edit->status->cols  = 60;
		$edit->status->rows  = 3;
		$edit->status->group = "Transaccion";
		$edit->status->mode  = "autohide";
		$edit->status->when  =array("show");

		$edit->fecha2          = new  dateonlyField("Fecha Aux",  "fecha2");
		$edit->fecha2->group   = "Otros";

		$edit->multiple =  new inputField("Multiple", 'multiple');
		$edit->multiple->group  = "Otros";
		$edit->multiple->when   =array('show');

		$edit->pcodbanc =  new inputField("Para Banco", 'pcodbanc');
		$edit->pcodbanc-> size     = 3;
		$edit->pcodbanc-> rule     = "callback_chexiste_codbanc";
		$edit->pcodbanc-> append($bpBANC);
		$edit->pcodbanc->group = "Transaccion";

		$edit->pnombreb = new inputField("Nombre","pnombreb");
		$edit->pnombreb->size     = 50;
		$edit->pnombreb->readonly = true;
		$edit->pnombreb->pointer  = true;
		$edit->pnombreb->in       = "pcodbanc";
		$edit->pnombreb->group    = "Transaccion";

		$edit->cuentab = new hiddenField("","cuentab");
		$edit->cuentab->db_name=' ';

		$edit->nom_bancb = new hiddenField("","nom_bancb");
		$edit->nom_bancb->db_name=' ';


		$status  =$edit->_dataobject->get("status"  );
		$tipo_doc=$edit->_dataobject->get("tipo_doc");
        $staing  =$edit->_dataobject->get("staing"  );
		if($status=='J1'){
			$action = "javascript:window.location='" .site_url($this->url.'actualizar/'.$edit->rapyd->uri->get_edited_id()). "'";
			$edit->button_status("btn_status",'Actualizar',$action,"TR","show");
			if($this->datasis->puede(337))
			$edit->buttons("modify","save");
		}elseif($status=='J2'){
			if(in_array($tipo_doc,array('CH','ND'))){
				$action = "javascript:window.location='" .site_url($this->url.'creanc/'.$edit->rapyd->uri->get_edited_id()). "'";
				$edit->button_status("btn_creanc",'Crear NC',$action,"TR","show");
			}
		}elseif($status=='J'){
//			if($this->datasis->puede(337))
			$edit->buttons("modify","save");
		}else{
			$edit->buttons("save");
		}

		if($status !='AN' && $status!='A' && $status!='A2' && $status!='NC' && $staing!='C'){
			$action = "javascript:window.location='" .site_url($this->url.'anular/'.$edit->rapyd->uri->get_edited_id()). "'";
			$edit->button_status("btn_rever",'Anular',$action,"TR","show");
		}

		$edit->buttons("undo", "back");

		if($this->datasis->puede(313))
		$edit->buttons("delete");

		if($this->datasis->puede(336))
		$edit->buttons("add");

		$edit->build();

		if($this->genesal){
			$smenu['link']   = barra_menu('204');
			$data['smenu']   = $this->load->view('view_sub_menu', $smenu,true);
			$data['content'] = $edit->output;
			$data['title']   = "$this->tits";
			$data["head"]    = script("jquery.pack.js").script("plugins/jquery.numeric.pack.js").script("plugins/jquery.floatnumber.js").$this->rapyd->get_head();
			$this->load->view('view_ventanas', $data);
		}else{
			if(!empty($edit->error_string))
			return $edit->error_string;
			else
			return $edit->_dataobject->get('id');
		}
	}

	function modifica($status,$id){
		//$this->datasis->modulo_id(101,1);
		$this->rapyd->load('dataedit','dataobject');

		$mBCTA=array(
                    'tabla'   =>'bcta',
                    'columnas'=>array(
                            'codigo'       =>'C&oacute;odigo',
                            'denominacion' =>'Denominacion',
                            'cuenta'       =>'Cuenta'),
                    'filtro'  =>array(
                            'codigo'       =>'C&oacute;odigo',
                            'denominacion' =>'Denominacion',
                            'cuenta'       =>'Cuenta'),
                    'retornar'=>array(
                            'codigo'       =>'bcta',
                            'denominacion' =>'bctad'),
                    'titulo'  =>'Buscar Otros Ingresos'
                    );

		$bBCTA=$this->datasis->p_modbus($mBCTA,"bcta");


		$do = new DataObject("mbanc");

		$edit = new DataEdit($this->tits, $do);

		$edit->back_cancel_save =true;
		$edit->back_cancel      =true;
		$edit->back_save        =true;

		$edit->back_url = site_url($this->url."filteredgrid");

		$edit->pre_process('insert'  ,'_valida_modifica');
		$edit->pre_process('update'  ,'_valida_modifica');
		$edit->post_process('update','_post_m_update');

		$edit->id        = new inputField("N&uacute;mero", "id");
		$edit->id->mode  = "autohide";
		$edit->id->when  = array('show');

		$edit->tipo = new inputField("","tipo");
		$edit->tipo-> insertValue = "E";
		$edit->tipo->mode         = "autohide";
		$edit->tipo->when=array('');

		$edit->cod_prov = new inputField("Beneficiario", 'cod_prov');
		$edit->cod_prov->size     = 6;
		$edit->cod_prov->rule     = "required";
		$edit->cod_prov->mode     = "autohide";

		$edit->nombrep = new inputField("Nombre", 'nombrep');
		$edit->nombrep->size      = 150;
		$edit->nombrep->readonly  = true;
		$edit->nombrep->pointer   = true;
		$edit->nombrep->in        = "cod_prov";
		$edit->nombrep->mode      = "autohide";

		$edit->bcta = new inputField("Motivo Movimiento", 'bcta');
		$edit->bcta->size     = 6;
		$edit->bcta->append($bBCTA);
		//$edit->bcta->rule     = "required";
		$edit->bcta->readonly =true;
		//if($bcta=='N')
		//$edit->bcta->mode     = "autohide";
		$edit->bcta->group = "Deposito";

		$edit->bctad = new inputField("", 'bctad');
		$edit->bctad->size        = 50;
		//$edit->bctad->group       = "Deposito";
		$edit->bctad->in          = "bcta";
		$edit->bctad->pointer     = true;
		$edit->bctad->readonly    = true;
		//$edit->bctad->mode        = "autohide";

		$edit->codbanc =  new inputField("Banco", 'codbanc');
		$edit->codbanc-> size     = 3;
		$edit->codbanc-> rule     = "required";
		$edit->codbanc-> readonly =true;
		$edit->codbanc->group     = "Transaccion";
		$edit->codbanc->mode      = "autohide";

		$edit->nombreb = new inputField("Nombre","nombreb");
		$edit->nombreb->size     = 20;
		$edit->nombreb->readonly = true;
		$edit->nombreb->pointer  = true;
		$edit->nombreb->in       = "codbanc";
		$edit->nombreb->group    = "Transaccion";
		$edit->nombreb->mode     = "autohide";

		$edit->tipo_doc = new dropdownField("Tipo Documento","tipo_doc");
		if($this->datasis->puede(288))$edit->tipo_doc->option("CH","Cheque"         );
		if($this->datasis->puede(289))$edit->tipo_doc->option("NC","Nota de Credito");
		if($this->datasis->puede(290))$edit->tipo_doc->option("ND","Nota de Debito" );
		if($this->datasis->puede(291))$edit->tipo_doc->option("DP","Deposito"       );
		$edit->tipo_doc->style    = "width:180px";
		$edit->tipo_doc->group    =  "Transaccion";
		$edit->tipo_doc->mode     = "autohide";

		$edit->cheque =  new inputField("Nro. Transacci&oacute;n", 'cheque');
		$edit->cheque-> size  = 20;
		$edit->cheque->rule   = "required";//callback_chexiste_cheque|
		$edit->cheque->group  = "Transaccion";
		if(!$this->datasis->puede(339))
		$edit->cheque->mode   = "autohide";

		$edit->fecha = new  dateonlyField("Fecha Transacci&oacute;n",  "fecha");
		//$edit->fecha->insertValue = date('Y-m-d');
		$edit->fecha->size    =12;
		$edit->fecha->rule    = 'required';
		$edit->fecha->group   = "Transaccion";
		if(!$this->datasis->puede(339))
		$edit->fecha->mode   = "autohide";

		$edit->monto = new inputField("Monto", 'monto');
		$edit->monto->size     = 15;
		$edit->monto->group    = "Transaccion";
		$edit->monto->css_class='inputnum';
		$edit->monto->rule     = 'callback_positivo';
		$edit->monto->mode     = "autohide";

		$edit->benefi =  new inputField("A nombre de", 'benefi');
                $edit->benefi-> size  = 80;
                //$edit->benefi->rule   = "required";
                $edit->benefi->group  = "Transaccion";
		if(!$this->datasis->puede(339))
		$edit->benefi->mode   = "autohide";

		$edit->observa = new textAreaField("Concepto", 'observa');
		$edit->observa->cols = 60;
		$edit->observa->rows = 3;
		$edit->observa->group    = "Transaccion";
		if(!$this->datasis->puede(339))
		$edit->observa->mode   = "autohide";

		$edit->destino = new dropdownField("Destino","destino");
		$edit->destino->option("C","Caja"    );
		$edit->destino->option("I","Interno" );
		$edit->destino->style="width:50px";

		$edit->rel = new textAreaField("Pago", 'rel');
		$edit->rel->cols  = 60;
		$edit->rel->rows  = 3;
		$edit->rel->group = "Transaccion";
		$edit->rel->mode  = "autohide";
		$edit->rel->when  =array("show");
		
		$edit->fecha2          = new  dateonlyField("Fecha Aux",  "fecha2");
		if(!$this->datasis->puede(339))
		$edit->fecha2->mode   = "autohide";
		
		

		$edit->buttons("modify","save","undo", "back");

		$edit->build();

		$smenu['link']   = barra_menu('204');
		$data['smenu']   = $this->load->view('view_sub_menu', $smenu,true);
		$data['content'] = $edit->output;
		$data['title']   = "$this->tits";
		$data["head"]    = script("jquery.pack.js").script("plugins/jquery.numeric.pack.js").script("plugins/jquery.floatnumber.js").$this->rapyd->get_head();
		$this->load->view('view_ventanas', $data);
	}


	function _post_m_update($do){
		$primary =implode(',',$do->pk);
		$cheque=$do->get('cheque');
		logusu($do->table,"Modifico movimiento $primary  con cheque $cheque");
	}

	function modifica3($status,$id){
		//$this->datasis->modulo_id(101,1);
		$this->rapyd->load('dataedit','dataobject');

		$mBCTA=array(
                    'tabla'   =>'bcta',
                    'columnas'=>array(
                            'codigo'       =>'C&oacute;odigo',
                            'denominacion' =>'Denominacion',
                            'cuenta'       =>'Cuenta'),
                    'filtro'  =>array(
                            'codigo'       =>'C&oacute;odigo',
                            'denominacion' =>'Denominacion',
                            'cuenta'       =>'Cuenta'),
                    'retornar'=>array(
                            'codigo'       =>'bcta',
                            'denominacion' =>'bctad'),
                    'titulo'  =>'Buscar Otros Ingresos'
                    );

		$bBCTA=$this->datasis->p_modbus($mBCTA,"bcta");

		$do = new DataObject("mbanc");

		$edit = new DataEdit($this->tits, $do);

		$edit->back_cancel_save =true;
		$edit->back_cancel      =true;
		$edit->back_save        =true;

		$edit->back_url = site_url($this->url."filteredgrid");

		$edit->post_process('update' ,'_post_m_update'   );

		$edit->id        = new inputField("N&uacute;mero", "id");
		$edit->id->mode  = "autohide";
		$edit->id->when  = array('show');

		$edit->tipo = new inputField("","tipo");
		$edit->tipo-> insertValue = "E";
		$edit->tipo->mode         = "autohide";
		$edit->tipo->when=array('');

		$edit->cod_prov = new inputField("Beneficiario", 'cod_prov');
		$edit->cod_prov->size     = 6;
		$edit->cod_prov->rule     = "required";
		$edit->cod_prov->mode     = "autohide";

		$edit->nombrep = new inputField("Nombre", 'nombrep');
		$edit->nombrep->size      = 150;
		$edit->nombrep->readonly  = true;
		$edit->nombrep->pointer   = true;
		$edit->nombrep->in        = "cod_prov";
		$edit->nombrep->mode      = "autohide";

		$edit->bcta = new inputField("Motivo Movimiento", 'bcta');
		$edit->bcta->size     = 6;
		$edit->bcta->append($bBCTA);
		//$edit->bcta->readonly =true;
		$edit->bcta->group = "Deposito";

		$edit->bctad = new inputField("", 'bctad');
		$edit->bctad->size        = 50;
		$edit->bctad->in          = "bcta";
		$edit->bctad->pointer     = true;
		$edit->bctad->readonly    = true;
		//$edit->bctad->mode        = "autohide";

		$edit->codbanc =  new inputField("Banco", 'codbanc');
		$edit->codbanc-> size     = 3;
		$edit->codbanc-> rule     = "required";
		$edit->codbanc-> readonly =true;
		$edit->codbanc->group     = "Transaccion";
		$edit->codbanc->mode      = "autohide";

		$edit->nombreb = new inputField("Nombre","nombreb");
		$edit->nombreb->size     = 20;
		$edit->nombreb->readonly = true;
		$edit->nombreb->pointer  = true;
		$edit->nombreb->in       = "codbanc";
		$edit->nombreb->group    = "Transaccion";
		$edit->nombreb->mode     = "autohide";

		$edit->tipo_doc = new dropdownField("Tipo Documento","tipo_doc");
		if($this->datasis->puede(288))$edit->tipo_doc->option("CH","Cheque"         );
		if($this->datasis->puede(289))$edit->tipo_doc->option("NC","Nota de Credito");
		if($this->datasis->puede(290))$edit->tipo_doc->option("ND","Nota de Debito" );
		if($this->datasis->puede(291))$edit->tipo_doc->option("DP","Deposito"       );
		$edit->tipo_doc->style    = "width:180px";
		$edit->tipo_doc->group    =  "Transaccion";
		$edit->tipo_doc->mode     = "autohide";

		$edit->cheque =  new inputField("Nro. Transacci&oacute;n", 'cheque');
		$edit->cheque-> size  = 20;
		$edit->cheque->rule   = "required";//callback_chexiste_cheque|
		$edit->cheque->group  = "Transaccion";
		$edit->cheque->mode   = "autohide";

		$edit->fecha = new  dateonlyField("Fecha Transacci&oacute;n",  "fecha");
		//$edit->fecha->insertValue = date('Y-m-d');
		$edit->fecha->size    =12;
		$edit->fecha->rule    = 'required';
		$edit->fecha->group   = "Transaccion";
		$edit->fecha->mode   = "autohide";

		$edit->monto = new inputField("Monto", 'monto');
		$edit->monto->size     = 15;
		$edit->monto->group    = "Transaccion";
		$edit->monto->css_class='inputnum';
		$edit->monto->rule     = 'callback_positivo';
		$edit->monto->mode     = "autohide";

		$edit->benefi =  new inputField("A nombre de", 'benefi');
                $edit->benefi-> size  = 80;
                //$edit->benefi->rule   = "required";
                $edit->benefi->group  = "Transaccion";
		$edit->benefi->mode   = "autohide";

		$edit->observa = new textAreaField("Concepto", 'observa');
		$edit->observa->cols = 60;
		$edit->observa->rows = 3;
		$edit->observa->group    = "Transaccion";
		$edit->observa->mode   = "autohide";

		$edit->destino = new dropdownField("Destino","destino");
		$edit->destino->option("C","Caja"    );
		$edit->destino->option("I","Interno" );
		$edit->destino->style="width:50px";
		$edit->destino->mode   = "autohide";

		$edit->rel = new textAreaField("Pago", 'rel');
		$edit->rel->cols  = 60;
		$edit->rel->rows  = 3;
		$edit->rel->group = "Transaccion";
		$edit->rel->mode  = "autohide";
		$edit->rel->when  =array("show");

		$edit->buttons("modify","save","undo", "back");

		$edit->build();

		$smenu['link']   = barra_menu('204');
		$data['smenu']   = $this->load->view('view_sub_menu', $smenu,true);
		$data['content'] = $edit->output;
		$data['title']   = "$this->tits";
		$data["head"]    = script("jquery.pack.js").script("plugins/jquery.numeric.pack.js").script("plugins/jquery.floatnumber.js").$this->rapyd->get_head();
		$this->load->view('view_ventanas', $data);
	}

	function modificac(){
		//$this->datasis->modulo_id(101,1);
		$this->rapyd->load('dataedit','dataobject');

		$tipo=($this->datasis->puede(321)?'("O","P")':'("O")');

		$mBCTA=array(
                    'tabla'   =>'bcta',
                    'columnas'=>array(
                            'codigo'       =>'C&oacute;odigo',
                            'denominacion' =>'Denominacion',
                            'cuenta'       =>'Cuenta'),
                    'filtro'  =>array(
                            'codigo'       =>'C&oacute;odigo',
                            'denominacion' =>'Denominacion',
                            'cuenta'       =>'Cuenta'),
                    'retornar'=>array(
                            'codigo'       =>'bcta',
                            'denominacion' =>'bctad'),
                    'titulo'  =>'Buscar Otros Ingresos',
		    'where'   =>"tipo IN $tipo",
		    'script'  =>array('copiabcta()'),
                    );

		$bBCTA=$this->datasis->p_modbus($mBCTA,"bcta");

		$script='
			function copiabcta(){
				benefi = $("#bctad").val();
                                    $("#observa").val(benefi);
			}
		';


		$do = new DataObject("mbanc");

		$edit = new DataEdit($this->tits, $do);

		$edit->back_cancel_save =true;
		$edit->back_cancel      =true;
		$edit->back_save        =true;

		$edit->back_url = site_url($this->url."filteredgrid");

		$edit->script($script,"create");
		$edit->script($script,"modify");

		$edit->pre_process('insert'  ,'_pre_validac');
		$edit->pre_process('update'  ,'_pre_validac');
		$edit->post_process('update','_post_modificac');
		//$edit->post_process('delete','_post_delete');

		$edit->id        = new inputField("N&uacute;mero", "id");
		$edit->id->mode  = "autohide";
		$edit->id->when  = array('show');

		$edit->tipo = new inputField("","tipo");
		$edit->tipo-> insertValue = "E";
		$edit->tipo->mode         = "autohide";
		$edit->tipo->when=array('');

		$edit->cod_prov = new inputField("Beneficiario", 'cod_prov');
		$edit->cod_prov->size     = 6;
		$edit->cod_prov->rule     = "required";
		$edit->cod_prov->mode     = "autohide";

		$edit->nombrep = new inputField("Nombre", 'nombrep');
		$edit->nombrep->size      = 150;
		$edit->nombrep->readonly  = true;
		$edit->nombrep->pointer   = true;
		$edit->nombrep->in        = "cod_prov";
		$edit->nombrep->mode      = "autohide";

		$edit->bcta = new inputField("Motivo Movimiento", 'bcta');
		$edit->bcta->size     = 6;
		//$edit->bcta->rule     = "required";
		$edit->bcta->readonly =true;
		//$edit->bcta->mode     = "autohide";
		//$edit->bcta->group = "Deposito";
		$edit->bcta->append($bBCTA);

		$edit->bctad = new inputField("", 'bctad');
		$edit->bctad->size        = 50;
		//$edit->bctad->group       = "Deposito";
		$edit->bctad->in          = "bcta";
		$edit->bctad->pointer     = true;
		$edit->bctad->readonly    = true;
		//$edit->bctad->mode        = "autohide";

		$edit->codbanc =  new inputField("Banco", 'codbanc');
		$edit->codbanc->size      = 3;
		$edit->codbanc->rule      = "required";
		$edit->codbanc->readonly  = true;
		$edit->codbanc->group     = "Transaccion";
		$edit->codbanc->mode      = "autohide";

		$edit->nombreb = new inputField("Nombre","nombreb");
		$edit->nombreb->size     = 20;
		$edit->nombreb->readonly = true;
		$edit->nombreb->pointer  = true;
		$edit->nombreb->in       = "codbanc";
		$edit->nombreb->group    = "Transaccion";
		$edit->nombreb->mode     = "autohide";

		$edit->tipo_doc = new dropdownField("Tipo Documento","tipo_doc");
		if($this->datasis->puede(288))$edit->tipo_doc->option("CH","Cheque"         );
		if($this->datasis->puede(289))$edit->tipo_doc->option("NC","Nota de Credito");
		if($this->datasis->puede(290))$edit->tipo_doc->option("ND","Nota de Debito" );
		if($this->datasis->puede(291))$edit->tipo_doc->option("DP","Deposito"       );
		$edit->tipo_doc->style    = "width:180px";
		$edit->tipo_doc->group    =  "Transaccion";
		$edit->tipo_doc->mode     = "autohide";

		$edit->cheque =  new inputField("Nro. Transacci&oacute;n", 'cheque');
		$edit->cheque-> size  = 20;
		$edit->cheque->rule   = "required";//callback_chexiste_cheque|
		$edit->cheque->group  = "Transaccion";
		$edit->cheque->mode   = "autohide";


		$edit->fecha = new  dateonlyField("Fecha Transacci&oacute;n",  "fecha");
		//$edit->fecha->insertValue = date('Y-m-d');
		$edit->fecha->size    =12;
		$edit->fecha->rule    = 'required';
		$edit->fecha->group   = "Transaccion";
		//$edit->fecha->mode    = "autohide";

		$edit->monto = new inputField("Monto", 'monto');
		$edit->monto->size     = 15;
		$edit->monto->group    = "Transaccion";
		$edit->monto->css_class='inputnum';
		$edit->monto->rule     = 'callback_positivo';
		$edit->monto->mode     = "autohide";

		$edit->benefi =  new inputField("A nombre de", 'benefi');
		$edit->benefi-> size  = 80;
		//$edit->benefi->rule   = "required";
		$edit->benefi->group  = "Transaccion";
		$edit->benefi->mode     = "autohide";

		$edit->observa = new textAreaField("Concepto", 'observa');
		$edit->observa->cols = 60;
		$edit->observa->rows = 3;
		$edit->observa->group    = "Transaccion";

		$edit->destino = new dropdownField("Destino","destino");
		$edit->destino->option("C","Caja"    );
		$edit->destino->option("I","Interno" );
		$edit->destino->style="width:50px";
		$edit->destino->mode ="autohide";

		$edit->rel = new textAreaField("Pago", 'rel');
		$edit->rel->cols  = 60;
		$edit->rel->rows  = 3;
		$edit->rel->group = "Transaccion";
		$edit->rel->mode  = "autohide";
		$edit->rel->when  =array("show");

		$edit->buttons("modify","save","undo", "back");
		$edit->build();

		$smenu['link']   = barra_menu('204');
		$data['smenu']   = $this->load->view('view_sub_menu', $smenu,true);
		$data['content'] = $edit->output;
		$data['title']   = "$this->tits";
		$data["head"]    = script("jquery.pack.js").script("plugins/jquery.numeric.pack.js").script("plugins/jquery.floatnumber.js").$this->rapyd->get_head();
		$this->load->view('view_ventanas', $data);
	}

	function _pre_validac($do){
		$codbanc     = $do->get('codbanc'     );
		$fecha       = $do->get('fecha'       );
		$error  .=$this->chbanse($codbanc,$fecha);

		if(!empty($error)){
			$do->error_message_ar['pre_ins']=$error;
			$do->error_message_ar['pre_upd']=$error;
			return false;
		}
	}

	function _post_modificac($do){
		redirect($this->url."dataedit/create");
	}

	function modifica2_h($id){
		$desem = $this->datasis->dameval("SELECT desem FROM mbanc WHERE id=$id");
		redirect("tesoreria/desem/dataedit/show/$desem");
	}

	function modifica2($m='',$status='',$id=''){
		//$this->datasis->modulo_id(101,1);
		$this->rapyd->load('dataedit','dataobject');

		$do = new DataObject("mbanc");

		$edit = new DataEdit($this->tits, $do);
		$edit->back_cancel_save =true;
		$edit->back_cancel      =true;
		$edit->back_save        =true;

		$edit->back_url = site_url($this->url."modifica2_h/$id");

		$edit->pre_process('update'  ,'_valida_modifica2');
		//$edit->post_process('insert','_post_insert');
		//$edit->post_process('update','_post_update');
		//$edit->post_process('delete','_post_delete');

		$edit->id        = new inputField("N&uacute;mero", "id");
		$edit->id->mode  = "autohide";
		$edit->id->when  = array('show');

		$edit->tipo = new inputField("","tipo");
		$edit->tipo-> insertValue = "E";
		$edit->tipo->mode         = "autohide";
		$edit->tipo->when         =array('');

		$edit->cod_prov = new inputField("Beneficiario", 'cod_prov');
		$edit->cod_prov->size     = 6;
		$edit->cod_prov->rule     = "required";
		$edit->cod_prov->mode     = "autohide";

		$edit->nombrep = new inputField("Nombre", 'nombrep');
		$edit->nombrep->size      = 150;
		$edit->nombrep->readonly  = true;
		$edit->nombrep->pointer   = true;
		$edit->nombrep->in        = "cod_prov";
		$edit->nombrep->mode      = "autohide";

		$edit->bcta = new inputField("Motivo Movimiento", 'bcta');
		$edit->bcta->size     = 6;
		//$edit->bcta->rule     = "required";
		$edit->bcta->readonly =true;
		$edit->bcta->mode     = "autohide";
		//$edit->bcta->group = "Deposito";

		$edit->bctad = new inputField("", 'bctad');
		$edit->bctad->size        = 50;
		//$edit->bctad->group       = "Deposito";
		$edit->bctad->in          = "bcta";
		$edit->bctad->pointer     = true;
		$edit->bctad->readonly    = true;
		$edit->bctad->mode        = "autohide";

		$edit->codbanc =  new inputField("Banco", 'codbanc');
		$edit->codbanc-> size     = 3;
		$edit->codbanc-> rule     = "required";
		$edit->codbanc-> readonly =true;
		$edit->codbanc->group     = "Transaccion";
		$edit->codbanc->mode      = "autohide";

		$edit->nombreb = new inputField("Nombre","nombreb");
		$edit->nombreb->size     = 100;
		$edit->nombreb->readonly = true;
		$edit->nombreb->pointer  = true;
		$edit->nombreb->in       = "codbanc";
		$edit->nombreb->group    = "Transaccion";
		$edit->nombreb->mode     = "autohide";

		$edit->tipo_doc = new dropdownField("Tipo Documento","tipo_doc");
		if($this->datasis->puede(288))$edit->tipo_doc->option("CH","Cheque"         );
		if($this->datasis->puede(289))$edit->tipo_doc->option("NC","Nota de Credito");
		if($this->datasis->puede(290))$edit->tipo_doc->option("ND","Nota de Debito" );
		if($this->datasis->puede(291))$edit->tipo_doc->option("DP","Deposito"       );
		$edit->tipo_doc->style    = "width:180px";
		$edit->tipo_doc->group    =  "Transaccion";
		$edit->tipo_doc->mode     = "autohide";

		$edit->cheque =  new inputField("Nro. Transacci&oacute;n", 'cheque');
		$edit->cheque-> size  = 20;
		$edit->cheque->rule   = "required";//callback_chexiste_cheque|
		$edit->cheque->group  = "Transaccion";
		$edit->cheque->mode   = "autohide";

		$edit->fecha = new  dateonlyField("Fecha Transacci&oacute;n",  "fecha");
		//$edit->fecha->insertValue = date('Y-m-d');
		$edit->fecha->size    =12;
		$edit->fecha->rule    = 'required';
		$edit->fecha->group   = "Transaccion";

		$edit->monto = new inputField("Monto", 'monto');
		$edit->monto->size     = 8;
		$edit->monto->group    = "Transaccion";
		$edit->monto->css_class='inputnum';
		$edit->monto->rule     = 'callback_positivo';
		$edit->monto->mode     = "autohide";

		$edit->benefi =  new inputField("A nombre de", 'benefi');
		$edit->benefi-> size  = 80;
		//$edit->benefi->rule   = "required";
		$edit->benefi->group  = "Transaccion";

		$edit->observa = new textAreaField("Concepto", 'observa');
		$edit->observa->cols = 60;
		$edit->observa->rows = 3;
		$edit->observa->group    = "Transaccion";

		$edit->destino = new dropdownField("Destino","destino");
		$edit->destino->db_name = 'destino';
		$edit->destino->option("C","Caja"           );
		$edit->destino->option("I","Interno"        );
		$edit->destino->style="width:50px";

		$edit->rel = new textAreaField("Pago", 'rel');
		$edit->rel->cols  = 60;
		$edit->rel->rows  = 3;
		$edit->rel->group = "Transaccion";
		$edit->rel->mode  = "autohide";
		$edit->rel->when  =array("show");

		$edit->buttons("modify","save","undo", "back");
		$edit->build();

		$smenu['link']   = barra_menu('204');
		$data['smenu']   = $this->load->view('view_sub_menu', $smenu,true);
		$data['content'] = $edit->output;
                $data['title']   = "$this->tits";
                $data["head"]    = $this->rapyd->get_head();
                $this->load->view('view_ventanas', $data);
	}

	function _valida_modifica2($do){
		$error = "";

		$fecha       = $do->get('fecha'       );
		$codbanc     = $do->get('codbanc'     );

		$error  .=$this->chbanse($codbanc,$fecha);

		if(empty($error)){
		}else{
			$edit->error_string=$error;
			$do->error_message_ar['pre_ins']=$error;
			$do->error_message_ar['pre_upd']=$error;
			return false;
		}
	}

	function _valida_modifica($do){
		$error = "";

		$fecha       = $do->get('fecha'       );
		$codbanc     = $do->get('codbanc'     );

		$error  .=$this->chbanse($codbanc,$fecha);

		if(empty($error)){
		}else{
			$edit->error_string=$error;
			$do->error_message_ar['pre_ins']=$error;
			$do->error_message_ar['pre_upd']=$error;
			return false;
		}
	}


	function _valida($do){
		
		$error='';
		$error .= $this->valida($do);

		if(empty($error)){
		
		}else{
			$do->error_message_ar['pre_ins']=$error;
			$do->error_message_ar['pre_upd']=$error;
			return false;
		}
	}
	
	function valida($do){
		$this->rapyd->load('dataobject');
		$error = "";

		$bctatrasla  = $this->datasis->traevalor("bctatrasla","");
		$status      = $do->get('status'      );
		$monto       = $do->get('monto'       );
		$tipo_doc    = $do->get('tipo_doc'    );
		$fechapago   = $do->get('fechapago'   );
		$observa     = $do->get('observa'     );
		$cheque      = $do->get('cheque'      );
		$codbanc     = $do->get('codbanc'     );
		$fecha       = $do->get('fecha'       );
		$bcta        = $do->get('bcta'        );
		$pcodbanc    = $do->get('pcodbanc'    );
		$multiple    = $do->get('multiple'    );

		$codbance    = $this->db->escape($codbanc);
		$chequee     = $this->db->escape($cheque );
		$tipo_doce   = $this->db->escape($tipo_doc);
		
		if($this->datasis->traevalor('CHEXISTEMBANC')=='S'){
			$sql='';
			if($multiple)
			$sql=" AND multiple <>$multiple ";
			
			$cana=$this->datasis->dameval("SELECT id FROM mbanc WHERE cheque=$chequee AND tipo_doc=$tipo_doce AND codbanc=$codbance $sql");

			if($cana>0)
				$error.="La Transaccion $cheque  ya Existe con la referencia $cana";
		}
		
		$error  .=$this->chbanse($codbanc,$fecha);
		$do->set('liable','S');
		$do->set('status','J1');

		$mbancnoc_monto = $this->datasis->dameval("SELECT SUM(monto) FROM mbancnoc WHERE codbanc=$codbance AND  tipo_doc='$tipo_doc' AND cheque=$chequee");
		if($mbancnoc_monto>0){
			if($mbancnoc_monto<>$monto)
			$error.="ERROR. el monto introducido $monto no coincide con el Credito o Debito No Contabilizado $mbancnoc_monto";
			else
			$this->db->query("UPDATE mbancnoc SET fconcilia=$fecha WHERE codbanc=$codbance AND  tipo_doc='$tipo_doc' AND cheque=$chequee");
		}


		if(!empty($bctatrasla) &&  in_array($bcta,explode(',',$bctatrasla)) && empty($pcodbanc) && in_array($tipo_doc,array("CH","ND")))
		$error.="Por Favor Seleccione el Banco destino para el traslado";

		$do2 = new DataObject("banc");
		$do2->load($codbanc);

		$saldo    = $do2->get('saldo' );
		$activo   = $do2->get('activo');


		if($activo!='S')
			$error.="<div class='alert'><p>El Banco ($codbanc) esta inactivo</p></div>";

		$BLOQCHFECHANUEVO=$this->datasis->traevalor('BLOQCHFECHANUEVO','N','Bloquea hacer un cheque cuando existe un cheque con fecha superior a la introducida');
		if($BLOQCHFECHANUEVO=='S'){
			if(is_numeric($cheque)){
				$codbance=$this->db->escape($codbanc);
				$cant=$this->datasis->dameval("SELECT COUNT(*) FROM mbanc WHERE codbanc=$codbance AND fecha>$fecha AND cheque<$cheque");
				if($cant>0)$error.="No se puede introducir un cheque mayor con fecha anterior";
			}
		}
		
		if(!empty($error)){
			return $error;
		}
	}

	function actualizar($id,$genesal=true){

		$this->rapyd->load('dataobject');

		$error = "";
		$do = new DataObject("mbanc");
		$do->load($id);

		$codbanc     = $do->get('codbanc'     );
		$status      = $do->get('status'      );
		$monto       = $do->get('monto'       );
		$tipo_doc    = $do->get('tipo_doc'    );
		$fechapago   = $do->get('fechapago'   );
		$observa     = $do->get('observa'     );
		$fecha       = $do->get('fecha'       );

		if($status=="J1"){
			$do2 = new DataObject("banc");
			$do2->load($codbanc);

			$saldo    = $do2->get('saldo' );
			$activo   = $do2->get('activo');

			$error='';

			if($activo!='S')
				$error.="<div class='alert'><p>El Banco ($codbanc) esta inactivo</p></div>";

			if(($tipo_doc=="ND" || $tipo_doc=="CH") && ($monto > $saldo))
				$error.="<div class='alert'><p>El Monto es Mayor Al Saldo del Banco</p></div>";

			if(empty($error)){
				if($tipo_doc=="ND" || $tipo_doc=="CH")$saldo-=$monto;
				if($tipo_doc=="DP" || $tipo_doc=="NC")$saldo+=$monto;

				$do2->set('saldo',$saldo);
				$do2->save();
			}
		}else{
			$error.="<div class='alert'><p>No se puede realizar la  operaci&oacute;n para este movimiento</p></div>";
		}

		if(empty($error)){
			$tipo=$do->get('tipo');
			if(empty($tipo))
			$do->set('tipo'  ,'M' );
			$do->set('status','J2');
			$do->save();

			logusu('mbanc',"Actualizo movimiento Nro $id");
			if($genesal)
			redirect($this->url."dataedit/show/$id");

		}else{
			logusu('mbanc',"Actualizo movimiento Nro $id con $error ");
			if($genesal===true){
				$data['content'] = $error.anchor($this->url."/dataedit/show/$id",'Regresar');
				$data['title']   = "$this->tits";
				$data["head"]    = $this->rapyd->get_head().script('jquery.js').script("plugins/jquery.numeric.pack.js");
				$this->load->view('view_ventanas', $data);
			}else{
				return $error;
			}
		}
	}

	function anular($id){

		$this->rapyd->load('dataobject');

		$do = new DataObject("mbanc");
		$do->load($id);

		$error = "";

		$codbanc     = $do->get('codbanc'   );
		$status      = $do->get('status'    );
		$monto       = $do->get('monto'     );
		$tipo_doc    = $do->get('tipo_doc'  );
		$fechapago   = $do->get('fechapago' );
		$observa     = $do->get('observa'   );
		$fecha       = $do->get('fecha'     );
		$rel         = $do->get('rel'       );
		$tipo        = $do->get('tipo'      );
		$concilia    = $do->get('concilia'  );
		$cheque      = $do->get('cheque'    );
		$cod_prov    = $do->get('cod_prov'  );
		$id          = $do->get('id'        );
		$benefi      = $do->get('benefi'    );
		$bcta        = $do->get('bcta'      );
		$destino     = $do->get('destino'   );
		$relch       = $do->get('relch'     );
		$id_nc       =null;
		$id_an       =null;

		if($concilia=='S')$error.="<div class='alert'><p>".($tipo_doc=='CH'?"Cheque:$cheque ya conciliado":($tipo_doc=='ND'?"Nota de Debito:$cheque ya conciliada":'')).". Por favor informe de la accion a realizar a la persona encargada de conciliaciones bancarias</p></div>";

		if($status=="J2" and empty($error)){
			$do2 = new DataObject("banc");
			$do2->load($codbanc);

			$saldo    = $do2->get('saldo' );
			$activo   = $do2->get('activo');

			if($activo!='S')
				$error.="<div class='alert'><p>El Banco ($codbanc) esta inactivo</p></div>";

			if(($tipo_doc=="DP" || $tipo_doc=="NC") && ($monto > $saldo))
				$error.="<div class='alert'><p>El Monto es Mayor Al Saldo del Banco</p></div>";

			if($tipo_doc=='CH' ){
				if(empty($error) && $this->datasis->traevalor('MBANCCREANC','S','Indica si el modulo mbanc dbe crear NC al anular')=='S'){
					if($this->datasis->traevalor('CREANCDIA')=='S'){
						if(1*date('Ymd') != 1*date('Ymd',strtotime($fecha)) ){
							$id_nc=$this->creambanc($codbanc,$monto,$cheque,'NC',"Anulacion de cheque $cheque",date('Ymd'),$cod_prov,'NC',$id,$bcta,'',$benefi,'N',NULL,$destino);

							$do->set('status' ,'A2');
							$do->set('anulado',date('Ymd'));
							$do->set('fliable' ,date('Ymd'));
						}else{
							$do->set('status' ,'AN');
							$do->set('anulado',date('Ymd'));
							$id_an=$id;
						}

					}elseif($this->datasis->traevalor('CREANCRELCH')=='S' ){
						if(strlen($relch)>0){
							$do->set('status' ,'A2');
							$do->set('anulado',date('Ymd'));
							$do->set('fliable' ,date('Ymd'));
							$id_nc=$this->creambanc($codbanc,$monto,$cheque,'NC',"Anulacion de cheque $cheque",date('Ymd'),$cod_prov,'NC',$id,$bcta,'',$benefi,'N',NULL,$destino);
						}else{
							$do->set('status' ,'AN');
							$do->set('anulado',date('Ymd'));
							$id_an=$id;
						}

					}elseif(1*date('m') != 1*date('m',strtotime($fecha)) ){
						$id_nc=$this->creambanc($codbanc,$monto,$cheque,'NC',"Anulacion de cheque $cheque",date('Ymd'),$cod_prov,'NC',$id,$bcta,'',$benefi,'N',NULL,$destino);

						$do->set('status' ,'A2');
						$do->set('anulado',date('Ymd'));
						$do->set('fliable' ,date('Ymd'));

					}else{
						$do->set('status' ,'AN');
						$do->set('anulado',date('Ymd'));
						$id_an=$id;
					}
				}else{
					$do->set('status','AN');
				}
			}else{

				if(empty($error) && $this->datasis->traevalor('MBANCCREAND','S','Indica si el modulo mbanc dbe crear NC al anular')=='S'){

					if($this->datasis->traevalor('CREANDDIA')=='S'){

						if(1*date('Ymd') != 1*date('Ymd',strtotime($fecha)) ){
							$id_nc=$this->creambanc($codbanc,$monto,$cheque,'ND',"Anulacion de $tipo_doc $cheque",date('Ymd'),$cod_prov,'ND',$id,$bcta,'',$benefi,'N',NULL,$destino);

							$do->set('status' ,'J2');
							$do->set('anulado',date('Ymd'));
							$do->set('fliable' ,date('Ymd'));
						}elseif(1*date('Ymd') == 1*date('Ymd',strtotime($fecha)) ){
							$do->set('status' ,'AN');
						}
					}

				}else{

					$do->set('status','AN');
					$do->set('anulado',date('Ymd'));
					$id_an=$id;
				}
			}
			if(empty($error)){
				if($tipo_doc=="ND" || $tipo_doc=="CH")$saldo+=$monto;
				if($tipo_doc=="DP" || $tipo_doc=="NC")$saldo-=$monto;

				$do2->set('saldo',$saldo);
				$do2->save();
			}
		}elseif($status=='J' || $status=='J1'){

		}else{
			$error.="<div class='alert'><p>No se puede realizar la  operaci&oacute;n para este movimiento</p></div>";
		}

		if(empty($error)){
			$rels    = explode('|',$rel);
			$pagos   = explode(',',$rels[0]);
			$compras = explode(',',$rels[1]);

			switch($tipo){
				case 'I':{
					$query = "UPDATE riva SET status = 'B',pagado='' WHERE pagado ='$id'  AND status = 'C' AND tipo_doc<>'AN'";
					$this->db->simple_query($query);
					break;
				}
				case 'T':{
					if(count($pagos)>0){
						$query = "UPDATE odirect SET mtimbre=null WHERE numero in ($rels[0])";
						$this->db->simple_query($query);
					}
					if(count($compras)>0){
						$query = "UPDATE ocompra SET mtimbre=null WHERE numero in ($rels[1])";
						$this->db->simple_query($query);
					}
				}
				case 'N':{
					$query = "UPDATE retenomi SET status='D' WHERE numero in ($rel)";
					$this->db->simple_query($query);
				}
				case 'R':{
					if(count($pagos)>0){
						$query = "UPDATE odirect SET mislr=null WHERE numero in ($rels[0])";
						$this->db->simple_query($query);
					}
					if(count($compras)>0){
						$query = "UPDATE ocompra SET mislr=null WHERE numero in ($rels[1])";
						$this->db->simple_query($query);
					}
				}
				case 'M':{
					if(count($pagos)>0){
						$query = "UPDATE odirect SET mmuni=null WHERE numero in ($rels[0])";
						$this->db->simple_query($query);
					}
					if(count($compras)>0){
						$query = "UPDATE ocompra SET mmuni=null WHERE numero in ($rels[1])";
						$this->db->simple_query($query);
					}
				}
				case 'C':{
					if(count($pagos)>0){
						$query = "UPDATE odirect SET mcrs=null WHERE numero in ($rels[0])";
						$this->db->simple_query($query);
					}
					if(count($compras)>0){
						$query = "UPDATE ocompra SET mcrs=null WHERE numero in ($rels[1])";
						$this->db->simple_query($query);
					}
				}
			}
		}

		if(empty($error)){
			$do->save();

			logusu('mbanc',"Anulo movimiento Nro $id");
			if($id_nc)
				redirect($this->url."modificac/modify/$id_nc");

			if($id_an)
				redirect($this->url."modificac/modify/$id_an");

			redirect($this->url."dataedit/show/$id");
		}else{
			logusu('mbanc',"Anulo movimiento Nro $id con $error ");
			$data['content'] = $error.anchor($this->url."/dataedit/show/$id",'Regresar');
			$data['title']   = "$this->tits";
			$data["head"]    = $this->rapyd->get_head().script('jquery.js').script("plugins/jquery.numeric.pack.js");
			$this->load->view('view_ventanas', $data);
		}
	}

	function positivo($valor){
		if ($valor < 0){
			$this->validation->set_message('positivo',"El campo monto debe ser positivo");
			return FALSE;
		}
	return TRUE;
	}

	function prueba(){
		$rel = '|';
			$rels    = explode('|',$rel);
			$pagos   = explode(',',$rels[0]);
			$compras = explode(',',$rels[1]);

			print_r($pagos);
			print_r($compras);
	}

	function chexiste_codbanc($codbanc){
		$codbance  = $this->db->escape($codbanc);
		$cana=$this->datasis->dameval("SELECT COUNT(*) FROM banc WHERE codbanc=$codbance");
		if($cana>0)
		return true;
		else{
			$this->validation->set_message('chexiste_codbanc',"El Banco $codbance no existe");
			return false;
		}
	}

	function _post_insert($do){
		$tipo_doc   = $do->get('tipo_doc');
		$cheque     = $do->get('cheque');
		$id         = $do->get('id');
		logusu('mbanc',"Creo $tipo_doc Nro $cheque movimento $id");
		if($this->genesal)
		redirect($this->url."actualizar/$id");
	}

	function _post_update($do){
		$tipo_doc   = $do->get('tipo_doc');
		$cheque     = $do->get('cheque');
		$id         = $do->get('id');
		logusu('mbanc',"modifico $tipo_doc Nro $cheque movimento $id");
		if($this->genesal)
		redirect($this->url."actualizar/$id");
	}

	function _post_delete($do){
		$tipo_doc   = $do->get('tipo_doc');
		$cheque     = $do->get('cheque');
		$id         = $do->get('id');
		logusu('mbanc',"elimino $tipo_doc Nro $cheque movimento $id");
		$this->db->query("CALL sp_banc_recalculo()");
	}

	function prueba2(){
		$a=$this->datasis->consularray("SELECT id,id FROM mbanc WHERE desem IN (SELECT desem FROM mbanc WHERE tipo_doc='NC' AND status='NC') AND status IN ('NC','A2')");
		echo implode(',',$a);
	}

	function sugerirch(){
		$ultimo=$this->datasis->dameval("SELECT valor FROM serie
		LEFT JOIN
		(SELECT MID(1*cheque,1,4) cheque FROM mbanc GROUP BY MID(1*cheque,1,4))t ON t.cheque=valor
		WHERE valor<9999 AND t.cheque IS NULL LIMIT 1");
		echo $ultimo;
	}

	function multiple(){
                $this->rapyd->load('dataobject','datadetails');

                $this->rapyd->uri->keep_persistence();

		$mBANC=array(
			'tabla'   =>'banc',
			'columnas'=>array(
				'codbanc' =>'C&oacute;digo',
				'fondo' =>'Clasificacion',
				'banco'=>'Banco',
				'saldo'=>'Saldo',
				'numcuent'=>'Cuenta'),
			'filtro'  =>array(
				'codbanc' =>'C&oacute;odigo',
				'banco'=>'Banco',
				'saldo'=>'Saldo',
				'numcuent'=>'Cuenta'),
			'p_uri'=>array(
			  4=>'<#i#>'),
			'retornar'=>array(
				'codbanc'=>'codbancm_<#i#>'
				 ),
			'where'=>'activo = "S"',
			//'script'=>array('ultimoch(<#i#>)','cal_nombrech(<#i#>)','cal_totalch()'),
			'titulo'  =>'Buscar Bancos');

                $bBANC=$this->datasis->p_modbus($mBANC,"<#i#>");

                $mSPRV=array(
                                'tabla'   =>'sprv',
                                'columnas'=>array(
                                'proveed' =>'C&oacute;odigo',
                                'nombre'=>'Nombre',
                    'rif'=>'Rif',
                                'contacto'=>'Contacto'),
                                'filtro'  =>array('proveed'=>'C&oacute;digo','nombre'=>'Nombre','rif'=>'Rif'),
                                'retornar'=>array('proveed'=>'cod_prov'     , 'nombre'=>'nombrep'),
                                'titulo'  =>'Buscar Beneficiario');

                $bSPRV=$this->datasis->p_modbus($mSPRV,"proveed");

                $do = new DataObject("mbancm");

                $do->rel_one_to_many('mbanc', 'mbanc', array('numero'=>'multiple'));
		$do->order_by('mbanc','id');

                $edit = new DataDetails($this->tits, $do);
                $edit->back_url = site_url($this->url."filteredgrid");
                $edit->set_rel_title('pades','Rubro <#o#>');

                $edit->pre_process('insert'  ,'_validam');
                $edit->pre_process('update'  ,'_validam');
                //$edit->post_process('insert','_post_insert');
                //$edit->post_process('update','_post_update');
                //$edit->post_process('delete','_post_delete');

                $status=$edit->get_from_dataobjetct('status');
                //**************************INICIO ENCABEZADO********************************************************************
                $edit->numero  = new inputField("N&uacute;mero", "numero");
                $edit->numero->mode="autohide";
                $edit->numero->when=array('show');

		 $edit->totalch  = new inputField("Total en Cheques Activos", "totalch");
                $edit->totalch->size     = 15;
                $edit->totalch->readonly = true;
                $edit->totalch->css_class='inputnum';
                $edit->totalch->rule     ='numeric';

                //**************************INICIO DETALLE DE DE MOVIMIENTOS BANCARIOS*******************************************

		$edit->itidm =  new inputField("(<#o#>) Referencia", 'idm_<#i#>');
                $edit->itidm->db_name   = 'id';
                $edit->itidm->rel_id    ='mbanc';
                $edit->itidm->when      =array('show');

                $edit->itstatusm =  new dropdownField("(<#o#>) Banco", 'statusm_<#i#>');
                if($edit->_status=='show')$edit->itstatusm->option("NC","Nota de Cr&eacute;dito"   );
                $edit->itstatusm->option("J1","Pendiente" );
                $edit->itstatusm->option("J2","Ejecutado" );
                $edit->itstatusm->option("AN","Anulado"   );
                $edit->itstatusm->option("A2","Anulado."  );
                $edit->itstatusm->db_name   = 'status';
                $edit->itstatusm-> size     = 3;
                $edit->itstatusm->rel_id    ='mbanc';
                $edit->itstatusm->style     ="width:100px;";
                $edit->itstatusm->onchange  = "cal_totalch();";
                $edit->itstatusm->when=array('show');

                //$edit->itstatusm->pointer = true;

                $edit->itcodbancm =  new inputField("(<#o#>) Banco", 'codbancm_<#i#>');
                $edit->itcodbancm->db_name   = 'codbanc';
                $edit->itcodbancm-> size     = 3;
                $edit->itcodbancm-> readonly =true;
                $edit->itcodbancm->rel_id    ='mbanc';
                $edit->itcodbancm->rule       = "required|callback_banco";
                $edit->itcodbancm->append($bBANC);
                //$edit->itcodbancm->pointer = true;

		$edit->itdestino = new dropdownField("(<#o#>) Destino","destino_<#i#>");
		$edit->itdestino->db_name = 'destino';
		$edit->itdestino->option("I","Interno" );
                $edit->itdestino->option("C","Caja"    );
                $edit->itdestino->style="width:50px";
                $edit->itdestino->rel_id   ='mbanc';

                $edit->ittipo_docm = new dropdownField("(<#o#>) Tipo Documento","tipo_docm_<#i#>");
                $edit->ittipo_docm->db_name   = 'tipo_doc';
		$edit->ittipo_docm->option("DP","Deposito"         );
                $edit->ittipo_docm->option("CH","Cheque"         );
                $edit->ittipo_docm->option("NC","Nota de Credito");
                $edit->ittipo_docm->option("ND","Nota de Debito" );

                $edit->ittipo_docm->rel_id   ='mbanc';
                $edit->ittipo_docm->style="width:130px;";
                //$edit->ittipo_docm->pointer = true;

                $edit->itfecham = new  dateonlyField("(<#o#>) Fecha Cheque",  "fecham_<#i#>");
                $edit->itfecham->db_name   ='fecha';
                $edit->itfecham->size        =10;
                $edit->itfecham->rule        = 'required';
                $edit->itfecham->rel_id   ='mbanc';
                $edit->itfecham->insertValue = date('Ymd');
                //$edit->itfecham->pointer = true;

		$edit->itbenefim = new inputField("(<#o#>) A Nombre de", 'benefim_<#i#>');
                $edit->itbenefim->db_name   = 'benefi';
                $edit->itbenefim->size      = 15;
                $edit->itbenefim->maxlenght = 40;
                $edit->itbenefim->rel_id    = 'mbanc';

		$edit->itfecha2m = new  dateonlyField("(<#o#>) Fecha Documento",  "fecha2m_<#i#>");
                $edit->itfecha2m->db_name    ='fecha2';
                $edit->itfecha2m->size        =10;
                $edit->itfecha2m->rule        = 'required';
                $edit->itfecha2m->rel_id      ='mbanc';
                $edit->itfecha2m->insertValue = date('Ymd');
                //$edit->itfecham->pointer = true;

		$edit->itchequem =  new inputField("(<#o#>) Cheque", 'chequem_<#i#>');
                $edit->itchequem->db_name   ='cheque';
                $edit->itchequem-> size  = 10;
                $edit->itchequem->rule   = "required";//callback_chexiste_cheque|
                $edit->itchequem->rel_id   ='mbanc';
                //$edit->itchequem->pointer = true;

                $edit->itmontom = new inputField("(<#o#>) Total", 'montom_<#i#>');
                $edit->itmontom->db_name   ='monto';
                //$edit->itmontom->mode      = 'autohide';
                //$edit->itmontom->when     = array('show');
                $edit->itmontom->size      = 15;
                //$edit->itmontom->rule      ='callback_positivo';
                $edit->itmontom->rel_id    ='mbanc';
                $edit->itmontom->css_class ='inputnum';
                $edit->itmontom->onchange  = "cal_totalch();";
                //$edit->itmontom->pointer = true;

                $edit->itobservam = new textAreaField("(<#o#>) Observaciones", 'observam_<#i#>');
                $edit->itobservam->db_name   ='observa';
                $edit->itobservam->cols = 30;
                $edit->itobservam->rows = 1;
                $edit->itobservam->rel_id   ='mbanc';
                //$edit->itobservam->pointer = true;

                //************************** FIN   DETALLE DE DE MOVIMIENTOS BANCARIOS*******************************************

                $edit->button_status("btn_add_mbanc" ,'Agregar Cheque/Nota de Debito',"javascript:add_mbanc()","MB",'modify',"button_add_rel");
                $edit->button_status("btn_add_mbanc2",'Agregar Cheque/Nota de Debito',"javascript:add_mbanc()","MB",'create',"button_add_rel");
                $edit->buttons("undo","back","add","modify","save","delete");

                $edit->build();

                //$smenu['link']   = barra_menu('208');
                //$data['smenu']   = $this->load->view('view_sub_menu', $smenu,true);
                $conten["form"]  =&  $edit;
                $data['content'] = $this->load->view('view_mbanc', $conten,true);
                //$data['content'] = $edit->output;
                $data['title']   = "$this->tits";
                $data["head"]    = $this->rapyd->get_head().script('jquery.js').script("plugins/jquery.numeric.pack.js").script("plugins/jquery.json.min.js");
                $this->load->view('view_ventanas', $data);
        }

	function _validam($do){
		$this->rapyd->load("dataobject");
		
		$this->genesal=false;
		$this->on_save_redirect=FALSE;
		$error='';
		
		/*
		 * VALIDA ANTES DE GUARDAR
		 */
		$mbanc = new DataObject("mbanc");
		$errort='';
		for($i=0;$i < $do->count_rel('mbanc');$i++){


			$mbanc->set('id'      , $do->get_rel('mbanc','id'       ,$i));
			$mbanc->set('tipo'    , $do->get_rel('mbanc','tipo'     ,$i));
			$mbanc->set('cod_prov', $do->get_rel('mbanc','cod_prov' ,$i));
			$mbanc->set('bcta'    , $do->get_rel('mbanc','bcta'     ,$i));
			$mbanc->set('destino' , $do->get_rel('mbanc','destino'  ,$i));
			$mbanc->set('codbanc' , $do->get_rel('mbanc','codbanc'  ,$i));
			$mbanc->set('tipo_doc', $do->get_rel('mbanc','tipo_doc' ,$i));
			$mbanc->set('cheque'  , $do->get_rel('mbanc','cheque'   ,$i));
			$mbanc->set('monto'   , $do->get_rel('mbanc','monto'    ,$i));
			$mbanc->set('benefi'  , $do->get_rel('mbanc','benefi'   ,$i));
			$mbanc->set('observa' , $do->get_rel('mbanc','observa'  ,$i));
			$mbanc->set('fecha'   , $do->get_rel('mbanc','fecha'    ,$i));
			$mbanc->set('fecha2'  , $do->get_rel('mbanc','fecha2'   ,$i));
			$mbanc->set('status'  , $do->get_rel('mbanc','status'   ,$i));
			
			$status  = $do->get_rel('mbanc','status'   ,$i);
			$codbanc = $do->get_rel('mbanc','codbanc'  ,$i);
			$fecha   = dbdate_to_human($do->get_rel('mbanc','fecha'    ,$i));

			if($status=='J1' || empty($status)){
				$error .=$this->chbanse($codbanc,$fecha);
				
			}			
			
			if(empty($error)){
					$error.=$this->valida($mbanc);
			}
		}
		
		
		/*
		 * GUARDA ITEM A ITEM
		 */
		 if(empty($error)){
			 echo "vacio";
			$numero=$do->get('numero');
			if(empty($numero)){
				$numero=$this->datasis->prox_numero('mbancm');
				$do->pk    =array('numero'=>$numero);
				//$do->loaded=1;
			}else{
				//$this->db->query("DELETE FROM mbanc WHERE multiple=$numero ");
			}
			$saved=array();$errort='';$ids=array();
			for($i=0;$i < $do->count_rel('mbanc');$i++){

				$do->set_rel('mbanc','numero','',$i);

				$_POST['id'      ] = $do->get_rel('mbanc','id'       ,$i);
				$_POST['tipo'    ] = $do->get_rel('mbanc','tipo'     ,$i);
				$_POST['cod_prov'] = $do->get_rel('mbanc','cod_prov' ,$i);
				$_POST['bcta'    ] = $do->get_rel('mbanc','bcta'     ,$i);
				$_POST['destino' ] = $do->get_rel('mbanc','destino'  ,$i);
				$_POST['codbanc' ] = $do->get_rel('mbanc','codbanc'  ,$i);
				$_POST['tipo_doc'] = $do->get_rel('mbanc','tipo_doc' ,$i);
				$_POST['cheque'  ] = $do->get_rel('mbanc','cheque'   ,$i);
				$_POST['fecha'   ] = dbdate_to_human($do->get_rel('mbanc','fecha'    ,$i));
				$_POST['fecha2'  ] = dbdate_to_human($do->get_rel('mbanc','fecha2'   ,$i));
				$_POST['monto'   ] = $do->get_rel('mbanc','monto'    ,$i);
				$_POST['benefi'  ] = $do->get_rel('mbanc','benefi'   ,$i);
				$_POST['observa' ] = $do->get_rel('mbanc','observa'  ,$i);
				$_POST['multiple'] = $numero;
				$status            = $do->get_rel('mbanc','status'  ,$i);

				if($status=='J1' || empty($status)){
					$errort =$this->dataedit();
					if(strlen($do->get_rel('mbanc','id'       ,$i))>0)
					$ids[]             = $do->get_rel('mbanc','id'       ,$i);
				}
				$do->set_rel('mbanc','id'    ,'',$i);

				if($errort>0)
				$saved[]=$errort;
				else
				$error.=$errort;
				
				
			}
			
			if(empty($error)){
				foreach($saved as $id){
					$error.=$this->actualizar($id,false);
				}
			}
			
			$this->on_save_redirect=TRUE;
			$this->genesal=true;

			if(count($ids)>0)
			$this->db->query("DELETE FROM mbanc WHERE id IN (".implode(',',$ids).") ");
			
		}
	

		

		if(!empty($error)){
			$do->error_message_ar['pre_ins']=$error;
			$do->error_message_ar['pre_upd']=$error;
			return false;
		}else{
			redirect("tesoreria/mbanc/multiple/show/$numero");
		}
	}

	function trasla($deid){
		$this->rapyd->load('dataobject');

		$error='';
		$do = new DataObject("mbanc");
		$do->load($deid);

		$cheque    =$do->get("cheque"    );
		$tipo_doc  =$do->get("tipo_doc"  );
		$monto     =$do->get("monto"     );
		$observa   =$do->get("observa"   );
		$benefi    =$do->get("benefi"    );
		$bcta      =$do->get("bcta"      );
		$status    =$do->get("status"    );
		$pcodbanc  =$do->get("pcodbanc"  );

		if(substr($status,1,1)!=2)
		$error.="El Movimiento no esta finalizado";

		if(!in_array($bcta,explode(',',$this->datasis->traevalor('bctatrasla'))))
		$error.="El motivo de Movimiento no es traslado";

		if(!in_array($tipo_doc,array('CH','ND')))
		$error.="El Movimiento debe ser un cheque o nota de debito";

		if($this->datasis->dameval("SELECT COUNT(*) FROM mbanc WHERE deid=$deid AND MID(status,2,1)='2'")>0){
			$error.="Ya existe un Movimiento relacionado";
		}

		$tipo_docd=($tipo_doc=='CH'?'DP':'NC');
		if(empty($error)){
			$do2 = new DataObject("mbanc");
			$do2->set('cheque'    ,$cheque      );
			$do2->set('tipo_doc'  ,$tipo_docd   );
			$do2->set('monto'     ,$monto       );
			$do2->set('observa'   ,$observa     );
			$do2->set('benefi'    ,$benefi      );
			$do2->set('bcta'      ,$bcta        );
			$do2->set('status'    ,'J2'         );
			$do2->set('codbanc'   ,$pcodbanc    );
			$do2->set('fecha'     ,date('Y-m-d'));
			$do2->set('deid'      ,$deid        );
			$do2->save();
			$paid=$do2->insert_id();
			$do->set("paid",$paid);
			$do->save();
		}

		if(empty($error)){
			logusu('mbanc',"Creo Movimiento Nro $paid");
			redirect($this->url."modifica/modify/$paid");
		}else{
			logusu('mbanc',"Creo movimiento con $error ");
			$data['content'] = $error.anchor($this->url."/filteredgrid",'Regresar');
			$data['title']   = "$this->tits";
			$data["head"]    = $this->rapyd->get_head().script('jquery.js').script("plugins/jquery.numeric.pack.js");
			$this->load->view('view_ventanas', $data);
		}
	}

	function creanc($deid){
		$this->rapyd->load('dataobject');

		$error='';
		$do = new DataObject("mbanc");
		$do->load($deid);

		$cheque    =$do->get("cheque"    );
		$tipo_doc  =$do->get("tipo_doc"  );
		$monto     =$do->get("monto"     );
		$observa   =$do->get("observa"   );
		$benefi    =$do->get("benefi"    );
		$bcta      =$do->get("bcta"      );
		$status    =$do->get("status"    );
		$codbanc   =$do->get("codbanc"   );

		if(substr($status,1,1)!=2)
		$error.="El Movimiento no esta finalizado";

		if(!in_array($tipo_doc,array('CH','ND')))
		$error.="El Movimiento debe ser un cheque o nota de debito";

		if(empty($error)){
			$do2 = new DataObject("mbanc");
			$do2->set('cheque'    ,$cheque      );
			$do2->set('tipo_doc'  ,'NC'         );
			$do2->set('monto'     ,$monto       );
			$do2->set('observa'   ,"ANULACION"  );
			$do2->set('benefi'    ,$benefi      );
			$do2->set('bcta'      ,$bcta        );
			$do2->set('status'    ,'J2'         );
			$do2->set('codbanc'   ,$codbanc    );
			$do2->set('fecha'     ,date('Y-m-d'));
			$do2->save();
			$paid=$do2->insert_id();

			$do3 = new DataObject("banc");
			$do3->load($codbanc);
			$saldo    = $do3->get('saldo' );
			if(empty($error)){
				if($tipo_doc=="ND" || $tipo_doc=="CH")$saldo-=$monto;
				if($tipo_doc=="DP" || $tipo_doc=="NC")$saldo+=$monto;

				$do3->set('saldo',$saldo);
				$do3->save();
			}
		}

		if(empty($error)){
			logusu('mbanc',"Creo Movimiento Nro $paid");
			redirect($this->url."modifica/modify/$paid");
		}else{
			logusu('mbanc',"Creo movimiento con $error ");
			$data['content'] = $error.anchor($this->url."/filteredgrid",'Regresar');
			$data['title']   = "$this->tits";
			$data["head"]    = $this->rapyd->get_head().script('jquery.js').script("plugins/jquery.numeric.pack.js");
			$this->load->view('view_ventanas', $data);
		}
	}

	function instalar(){
		$this->db->simple_query("ALTER TABLE `mbanc` CHANGE COLUMN `benefi` `benefi` VARCHAR(100) NULL ");
		$this->db->simple_query("ALTER TABLE `mbanc` ADD COLUMN `liable` CHAR(1) NULL DEFAULT 'S'");
		$this->db->simple_query("ALTER TABLE `mbanc` ADD COLUMN `fliable` DATE NULL");
		$this->db->simple_query("ALTER TABLE `mbanc` CHANGE COLUMN `cheque` `cheque` TEXT NULL DEFAULT NULL");
        $this->db->simple_query("ALTER TABLE `mbanc` ADD COLUMN `staing` CHAR(1) NULL DEFAULT NULL");
		$this->db->simple_query("ALTER TABLE `mbanc` ADD COLUMN `observa2` TEXT NULL");
		$this->db->simple_query("ALTER TABLE `mbanc` ADD COLUMN `fecha2` DATE NULL DEFAULT NULL");
		$this->db->simple_query("ALTER TABLE `mbanc` ADD COLUMN `multiple` INT NULL DEFAULT NULL");
		$this->db->simple_query("
		CREATE TABLE `mbancm` (
			`numero` INT(10) NOT NULL AUTO_INCREMENT,
			PRIMARY KEY (`numero`)
		)
		COLLATE='utf8_general_ci'
		ENGINE=MyISAM
		ROW_FORMAT=DEFAULT
		AUTO_INCREMENT=1");

		$this->db->simple_query("ALTER TABLE `mbancm`  ADD COLUMN `usuario` VARCHAR(50) NULL AFTER `numero`,  ADD COLUMN `fecha` TIMESTAMP NULL");
		$this->db->simple_query("ALTER TABLE `mbanc`  ADD COLUMN `caduco` CHAR(1) NULL DEFAULT 'N'");
		$this->db->simple_query("ALTER TABLE `mbanc`  CHANGE COLUMN `concilia` `concilia` CHAR(1) NULL DEFAULT 'N'");
		$this->db->simple_query("ALTER TABLE `mbanc`  ADD COLUMN `pcodbanc` VARCHAR(10) NULL DEFAULT NULL");
		$this->db->simple_query("ALTER TABLE `mbanc`  ADD COLUMN `deid` INT NULL DEFAULT NULL");
		$this->db->simple_query("ALTER TABLE `mbanc`  ADD COLUMN `paid` INT(11) NULL DEFAULT NULL");
		$this->db->simple_query("ALTER TABLE `mbanc`  ADD COLUMN `coding` INT(11) NULL DEFAULT NULL");
		$this->db->simple_query("ALTER TABLE `mbanc` CHANGE COLUMN `tipo` `tipo` CHAR(2) NULL COMMENT 'A (pmov) B (odirect)  C(movi) D (devo) E (ppro)  F (opago)  R (reinte)' ");
	}
}
