<?php
require_once(BASEPATH.'application/controllers/presupuesto/common.php');
class Desem extends Common {

        var $titp='Desembolsos';
        var $tits='Desembolso';
        var $url ='tesoreria/desem/';

        function desem(){
                parent::Controller();
                $this->load->library("rapyd");
        }

        function index(){
            $this->instalar();
            redirect($this->url."filteredgrid");
        }

        function filteredgrid(){
                $this->datasis->modulo_id(115,1);
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

                $filter->db->select("b.benefi,b.cheque cheque,a.numero numero,a.fdesem fdesem,a.status status,a.cod_prov cod_prov,a.total total, d.pago pago");
                $filter->db->from("desem a");
                $filter->db->join("pades d"    ,"d.desem=a.numero");
                $filter->db->join("mbanc b"    ,"a.numero=b.desem"   );
                $filter->db->groupby("a.numero");
                $filter->db->order_by("a.numero","desc");

                $filter->numero = new inputField("N&uacute;mero", "numero");
                $filter->numero->db_name="a.numero";
                $filter->numero->size=12;
                $filter->numero->clause="likerigth";

                $filter->fdesem = new dateonlyField("Fecha", "fdesem");
                $filter->fdesem->db_name="a.fdesem";
                $filter->fdesem->dbformat = "Y-m-d";
                $filter->fdesem->size=12;

                $filter->cheque = new inputField("Cheque", "cheque");
                $filter->cheque->db_name="b.cheque";
                $filter->cheque->size  =10;

                $filter->id = new inputField("Ref.", "id");
                $filter->id->db_name="b.id";
                $filter->id->size  =10;

                $filter->pago = new inputField("Orden de Pago", "pago");
                $filter->pago->db_name="d.pago";
                $filter->pago->size  =10;

                $filter->cod_prov = new inputField("Beneficiario", 'cod_prov');
                $filter->cod_prov->db_name="a.cod_prov";
                $filter->cod_prov->size = 6;
                $filter->cod_prov->append($bSPRV);
                $filter->cod_prov->rule = "required";

                $filter->total = new inputField("Monto Total", "monto");
                $filter->total->db_name="a.total";
                $filter->total->size  =10;

                $filter->monto = new inputField("Monto Cheque", "monto");
                $filter->monto->db_name="b.monto";
                $filter->monto->size  =10;

                $filter->status = new dropdownField("Estado","status");
                $filter->status->db_name="a.status";
                $filter->status->option("","");
                $filter->status->option("D2","Ejecutado");
                $filter->status->option("D1","Sin Ejecutar");
                $filter->status->option("D3","Pagado");
                $filter->status->option("DA","Anulado");
                $filter->status->style="width:150px";

                $filter->buttons("reset","search");

                $filter->build();
                $uri = anchor($this->url.'dataedit/show/<#numero#>','<str_pad><#numero#>|8|0|STR_PAD_LEFT</str_pad>');

                function sta($status){
                        switch($status){
                                case "D1":return "Cheque Por Emitir";break;
                                case "D2":return "Cheque Emitido";break;
                                case "DA":return "Anulado";break;
                        }
                        return $status;
                }

                $grid = new DataGrid("");

                $grid->per_page = 20;
                $grid->use_function('substr','str_pad','sta','action');

                $grid->column_orderby("N&uacute;mero"    ,$uri          ,"numero");
                $grid->column_orderby("O.Pago"           ,"pago"                                         ,"pago"          ,"align='left'        ");
                $grid->column_orderby("Cheque"           ,"cheque"                                        ,"cheque"       ,"align='left'        ");
                $grid->column_orderby("Fecha"            ,"<dbdate_to_human><#fdesem#></dbdate_to_human>" ,"fdesem"       ,"align='center'      ");
                $grid->column_orderby("Beneficiario"     ,"benefi"                                      ,"proveed2"     ,"align='left'        ");
                $grid->column_orderby("Monto"            ,"<number_format><#total#>|2|,|.</number_format>","total"        ,"align='right'       ");
                $grid->column_orderby("Estado"           ,"<sta><#status#></sta>"                         ,"status"       ,"align='center'NOWRAP");
                //$grid->column(" "                ,"<action><#status#>|<#numero#></action>"      ,"align='center'");


                if($this->datasis->puede(370))
                $grid->add($this->url."dataedit/create");
                $grid->build();

                //echo $grid->db->last_query();

                //$data['content'] = $filter->output.$grid->output;
                $data['filtro']  = $filter->output;
                $data['content'] = $grid->output;
                $data['script']  = script("jquery.js")."\n";
                $data['title']   = "$this->titp";
                $data["head"]    = $this->rapyd->get_head();
                $this->load->view('view_ventanas', $data);
        }

        function dataedit($estado='',$id=''){
                $this->datasis->modulo_id(115,1);
                $this->rapyd->load('dataobject','datadetails');

                $this->rapyd->uri->keep_persistence();

                        $mBANC=array(
                                'tabla'   =>'banc',
                                'columnas'=>array(
                                        'codbanc' =>'C&oacute;odigo',
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
                                'script'=>array('ultimoch(<#i#>)','cal_nombrech(<#i#>)','cal_totalch()'),
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

                $modbus=array(
                        'tabla'   =>'v_pagos_encab',
                        'columnas'=>array(
                                'numero'  =>'N&uacute;mero',
                                'fondo'   =>'F. Financiamiento',
                                'fecha'   =>'Fecha',
                                'cod_prov'=>'Codigo',
                                'nombre'  =>'Beneficiario',
                                'total2'  =>'Total'

                                ),
                        'filtro'  =>array(
                                'numero'  =>'N&uacute;mero',
                                'nombre'  =>'Beneficiario',
                                'total2'  =>'Total'
                                ),
                        'retornar'=>array(
                            'numero'       =>'pago_<#i#>',
                            'imptimbre'    =>'imptimbreo_<#i#>',
                            'impmunicipal' =>'impmunicipalo_<#i#>',
                            'crs'          =>'crso_<#i#>',
                            'total'        =>'totalo_<#i#>',
							'total2'       =>'total2o_<#i#>',
							'reten'        =>'reteno_<#i#>',
							'reteiva'      =>'reteivao_<#i#>',
							'crs'          =>'crso_<#i#>',
							'otrasrete'    =>'otrasreteo_<#i#>',
							'cod_prov'     =>'cod_prov',
							'observa'      =>'temp'
                                        ),
                        'p_uri'=>array(
                                  4=>'<#i#>',
                                  5=>'<#cod_prov#>'),
                        'where' =>'(status="C2" OR status="O2" OR status = "H2" OR status = "M2" OR status = "N2" OR status = "F2" OR status = "B2" OR status = "R2" OR status = "G2" OR status = "I2" OR status = "S2" OR status="K2") AND IF(<#cod_prov#>=".....",cod_prov LIKE "%",cod_prov = <#cod_prov#>)',//
                        'script'=>array('cal_observa()','cal_nprov()'),
                        'titulo'=>'Busqueda de Ordenes de Pago');

                $btn=$this->datasis->p_modbus($modbus,'<#i#>/<#cod_prov#>');

                $do = new DataObject("desem");

                $do->rel_one_to_many('pades', 'pades', array('numero'=>'desem'));
                $do->rel_one_to_many('mbanc', 'mbanc', array('numero'=>'desem'));
                $do->rel_pointer('pades','odirect' ,'pades.pago=odirect.numero',"odirect.total AS totalo,odirect.total2 AS total2o,odirect.reteiva AS reteivao,odirect.reten AS reteno,odirect.imptimbre AS imptimbreo, odirect.impmunicipal AS impmunicipalo,odirect.crs AS crso,odirect.otrasrete AS otrasreteo ");
                $do->pointer('sprv' ,'sprv.proveed=desem.cod_prov','sprv.nombre AS nombrep');
                //$do->load($id);

                $edit = new DataDetails($this->tits, $do);
                $edit->back_url = site_url($this->url."filteredgrid");
                $edit->set_rel_title('pades','Rubro <#o#>');

                $edit->pre_process('insert'  ,'_valida');
                $edit->pre_process('update'  ,'_valida');
                $edit->pre_process('delete'  ,'_pre_del');
                $edit->post_process('insert','_post_insert');
                $edit->post_process('update','_post_update');
                $edit->post_process('delete','_post_delete');


                $status=$edit->get_from_dataobjetct('status');
                //**************************INICIO ENCABEZADO********************************************************************
                $edit->numero  = new inputField("N&uacute;mero", "numero");
                $edit->numero->mode="autohide";
                $edit->numero->when=array('show');

                $edit->fdesem = new  dateonlyField("Fecha",  "fdesem");
                $edit->fdesem->rule     = 'required|chfecha';
                $edit->fdesem->insertValue = date('Ymd');
                $edit->fdesem->size =12;

                $edit->cod_prov = new inputField("Beneficiario", 'cod_prov');
                $edit->cod_prov->rule     = 'required';
                $edit->cod_prov->db_name  = "cod_prov";
                $edit->cod_prov->size     = 5;
                $edit->cod_prov->append($bSPRV);
                $edit->cod_prov->onchange = "cal_nprov();";
                //print_r($edit->_dataobject);

                //echo "sddsds".$edit->_dataobject->_pointer_data['nombrep'];
                $edit->nombrep = new inputField("Nombre Beneficiario", 'nombrep');
                $edit->nombrep->size = 50;
                $edit->nombrep->readonly = true;
                $edit->nombrep->pointer = true;

                $edit->total  = new inputField("Totales", "total");
                $edit->total->size = 13;
                $edit->total->readonly = true;
                $edit->total->css_class='inputnum';
                $edit->total->rule     ='numeric';

                $edit->totalch  = new inputField("Total en Cheques Activos", "totalch");
                $edit->totalch->size     = 15;
                $edit->totalch->readonly = true;
                $edit->totalch->css_class='inputnum';
                $edit->totalch->rule     ='numeric';

                $edit->tcrs  = new inputField("Total a I.C.R.S", "tcrs");
                $edit->tcrs->size = 13;
                $edit->tcrs->readonly = true;
                $edit->tcrs->css_class='inputnum';
                $edit->tcrs->rule     ='numeric';

                $edit->totrasrete  = new inputField("Total Otras Retenciones", "totrasrete");
                $edit->totrasrete->size = 13;
                $edit->totrasrete->readonly = true;
                $edit->totrasrete->css_class='inputnum';
                $edit->totrasrete->rule     ='numeric';

                $edit->ttimbre  = new inputField("Total Timbre", "ttimbre");
                $edit->ttimbre->size = 13;
                $edit->ttimbre->readonly = true;
                $edit->ttimbre->css_class='inputnum';
                $edit->ttimbre->rule     ='numeric';

                $edit->tmunicipal  = new inputField("Total Timbre", "tmunicipal");
                $edit->tmunicipal->size     = 13;
                $edit->tmunicipal->readonly = true;
                $edit->tmunicipal->css_class='inputnum';
                $edit->tmunicipal->rule     ='numeric';

                $edit->tislr  = new inputField("Total a Pagar", "tislr");
                $edit->tislr->size = 13;
                $edit->tislr->readonly = true;
                $edit->tislr->css_class='inputnum';
                $edit->tislr->rule     ='numeric';

                $edit->triva  = new inputField("Total a Pagar", "triva");
                $edit->triva->size = 13;
                $edit->triva->readonly = true;
                $edit->triva->css_class='inputnum';
                $edit->triva->rule     ='numeric';

                $edit->total2  = new inputField("Total a Pagar", "total2");
                $edit->total2->size = 13;
                $edit->total2->readonly = true;
                $edit->total2->css_class='inputnum';
                $edit->total2->rule     ='numeric';

                $edit->temp      = new inputField("temp", "temp");
                $edit->temp->when=array('modify','create');

                //************************** FIN   ENCABEZADO********************************************************************

                //**************************INICIO DETALLE DE ORDENES DEPAGO*****************************************************
                $edit->itpago = new inputField("(<#o#>) ", "pago_<#i#>");
                $edit->itpago->rule     ='callback_repetido|required|callback_itorden';
                $edit->itpago->size     =13;
                $edit->itpago->db_name  ='pago';
                $edit->itpago->rel_id   ='pades';
                //$edit->itpago->readonly =true;
                if($status == 'D2' || $status == 'D3')$edit->itpago->mode     = "autohide";
                $edit->itpago->append('<img src="'.base_url().'assets/default/images/system-search.png"  alt="Busqueda de Ordenes de Pago" title="Busqueda de Ordenes de Pago" border="0" onclick="modbusdepen(<#i#>)"/>');

                $campos = array('total2o','otrasreteo','crso','imptimbreo','impmunicipalo','reteno','reteivao','totalo');
                foreach($campos AS $campo=>$objeto){
                        $objeto2 = 'it'.$objeto;
                        $edit->$objeto2 = new inputField("(<#o#>) Total", $objeto."_<#i#>");
                        $edit->$objeto2->db_name  = $objeto;
                        $edit->$objeto2->rel_id   = 'pades';
                        $edit->$objeto2->size     = 13;
                        $edit->$objeto2->readonly = true;
                        $edit->$objeto2->pointer  = true;
                        $edit->$objeto2->css_class= 'inputnum';
                        if($status == 'D2' || $status == 'D3')$edit->$objeto2->mode     = "autohide";
                }
                //************************** FIN   DETALLE DE ORDENES DEPAGO*****************************************************

                //**************************INICIO DETALLE DE DE MOVIMIENTOS BANCARIOS*******************************************

                $edit->itstatusm =  new dropdownField("(<#o#>) Banco", 'statusm_<#i#>');
                if($edit->_status=='show')$edit->itstatusm->option("NC","Nota de Cr&eacute;dito"   );
                $edit->itstatusm->option("E1","Pendiente" );
                $edit->itstatusm->option("E2","Activo"    );
                $edit->itstatusm->option("AN","Anulado"   );
                $edit->itstatusm->option("A2","Anulado." );
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
                $edit->itdestino->option("C","Caja"    );
                $edit->itdestino->option("I","Interno" );
                $edit->itdestino->style="width:50px";
                $edit->itdestino->rel_id   ='mbanc';

                $edit->ittipo_docm = new dropdownField("(<#o#>) Tipo Documento","tipo_docm_<#i#>");
                $edit->ittipo_docm->db_name   = 'tipo_doc';
                $edit->ittipo_docm->option("CH","Cheque"         );
                if($edit->_status=='show')$edit->ittipo_docm->option("NC","Nota de Credito");
                $edit->ittipo_docm->option("ND","Nota de Debito" );
                $edit->ittipo_docm->option("DP","Deposito"       );
                $edit->ittipo_docm->option("CH","Cheque"         );
                $edit->ittipo_docm->style="width:180px";
                $edit->ittipo_docm->rel_id   ='mbanc';
                $edit->ittipo_docm->style="width:130px;";
                //$edit->ittipo_docm->pointer = true;

                $edit->itchequem =  new inputField("(<#o#>) Cheque", 'chequem_<#i#>');
                $edit->itchequem->db_name   ='cheque';
                $edit->itchequem-> size  = 10;
                $edit->itchequem->rule   = "required";//callback_chexiste_cheque|
                $edit->itchequem->rel_id   ='mbanc';
                //$edit->itchequem->pointer = true;

                $edit->itfecham = new  dateonlyField("(<#o#>) Fecha Cheque",  "fecham_<#i#>");
                $edit->itfecham->db_name   ='fecha';
                $edit->itfecham->size        =10;
                $edit->itfecham->rule        = 'required';
                $edit->itfecham->rel_id   ='mbanc';
                $edit->itfecham->insertValue = date('Ymd');
                //$edit->itfecham->pointer = true;

                $edit->itmontom = new inputField("(<#o#>) Total", 'montom_<#i#>');
                $edit->itmontom->db_name   ='monto';
                //$edit->itmontom->mode      = 'autohide';
                //$edit->itmontom->when     = array('show');
                $edit->itmontom->size      = 15;
                $edit->itmontom->rule      ='callback_positivo';
                $edit->itmontom->rel_id    ='mbanc';
                $edit->itmontom->css_class ='inputnum';
                $edit->itmontom->onchange  = "cal_totalch();";
                //$edit->itmontom->pointer = true;

                $edit->itbenefim = new inputField("(<#o#>) A Nombre de", 'benefim_<#i#>');
                $edit->itbenefim->db_name   = 'benefi';
                $edit->itbenefim->size      = 15;
                $edit->itbenefim->maxlenght = 40;
                $edit->itbenefim->rel_id    = 'mbanc';

                $edit->itobservam = new textAreaField("(<#o#>) Observaciones", 'observam_<#i#>');
                $edit->itobservam->db_name   ='observa';
                $edit->itobservam->cols = 30;
                $edit->itobservam->rows = 1;
                $edit->itobservam->rel_id   ='mbanc';
                //$edit->itobservam->pointer = true;

                //$edit->itidm =  new inputField("(<#o#>) Cheque", 'idm_<#i#>');
                //$edit->itidm->db_name   ='id';
                 //$edit->itidm-> size  = 1;
                //$edit->itidm->rel_id   ='mbanc';
                //$edit->itchequem->pointer = true;

                //$edit->itcontainer = new containerField("alert",$uri);
                //$edit->itcontainer->when = array("show");
                //$edit->itcontainer->rel_id   ='mbanc';

                //************************** FIN   DETALLE DE DE MOVIMIENTOS BANCARIOS*******************************************

                $sta=$edit->get_from_dataobjetct('sta');
                if($status=='D1'){
                  		if($this->datasis->puede(372))
            			$edit->buttons("delete");
                        $action = "javascript:window.location='" .site_url($this->url.'actualizar/'.$edit->rapyd->uri->get_edited_id()). "'";
                        $edit->button_status("btn_status",'Emitir',$action,"TR","show","button");

                        $action = "javascript:btn_anulaf('".$edit->rapyd->uri->get_edited_id()."')";
                        $edit->button_status("btn_anular",'Anular',$action,"TR","show","button");
        		if($this->datasis->puede(371))
                        $edit->buttons("modify","save");
                }elseif($status == 'D2' && $sta!='E22' & $sta!='E23'){
                        $action = "javascript:btn_anulaf('".$edit->rapyd->uri->get_edited_id()."')";
                        $edit->button_status("btn_anular",'Anular',$action,"TR","show","button_add_rel");
                }elseif($status == 'DA'){
            		if($this->datasis->puede(372))
            		$edit->buttons("delete");
                }else{
                        $edit->buttons("save");
                }

                $edit->button_status("btn_add_mbanc" ,'Agregar Cheque/Nota de Debito',"javascript:add_mbanc()","MB",'modify',"button_add_rel");
                $edit->button_status("btn_add_mbanc2",'Agregar Cheque/Nota de Debito',"javascript:add_mbanc()","MB",'create',"button_add_rel");
                $edit->button_status("btn_add_pades" ,'Agregar O. de Pago',"javascript:add_pades()","PA","create","button_add_rel");
                $edit->button_status("btn_add_pades2",'Agregar O. de Pago',"javascript:add_pades()","PA","modify","button_add_rel");
                $edit->button_status("btn_op_desde_hasta" ,'Agregar Multiples Ordenes de Pago',"javascript:op_desde_hasta()","TL","create","TL");
                $edit->button_status("btn_op_desde_hasta2",'Agregar Multiples Ordenes de Pago',"javascript:op_desde_hasta()","TL","modify","TL");
                
                $edit->buttons("undo","back");
                if($this->datasis->puede(370))
                $edit->buttons("add");

                $edit->build();

                $smenu['link']   = barra_menu('208');
                $data['smenu']   = $this->load->view('view_sub_menu', $smenu,true);
                $conten["form"]  =&  $edit;
                $data['content'] = $this->load->view('view_desem', $conten,true);
                //$data['content'] = $edit->output;
                $data['title']   = "$this->tits";
                $data["head"]    = $this->rapyd->get_head().script('jquery.js').script('jquery-ui.js').script("plugins/jquery.numeric.pack.js").style('vino/jquery-ui.css');
                $this->load->view('view_ventanas', $data);
        }

        function _pre_del($do){
		$error='';
		for($i=0;$i < $do->count_rel('mbanc');$i++){
                        $fecha   = $do->get_rel('mbanc','fecha'  ,$i);
                        $codbanc = $do->get_rel('mbanc','codbanc'  ,$i);
                        $error.=$this->chbanse($codbanc,$fecha);
                }

                if(!empty($error)){
                        $do->error_message_ar['pre_del']="<div class='alert'>".$error."</div>";
                        return false;
                }
        }

        function itorden($orden){
                $cod_prov    = $this->db->escape($this->input->post('cod_prov'));
                $orden      = $this->db->escape($orden);
                if(!empty($cod_prov)){
                        $cana=$this->datasis->dameval("SELECT COUNT(*) FROM odirect WHERE cod_prov=$cod_prov AND numero=$orden");// AND status='O'
                }else{
                        $cana=$this->datasis->dameval("SELECT COUNT(*) FROM odirect WHERE numero=$orden");// AND status='O'
                }

                if($cana>0){
                        return true;
                }else{
                        $this->validation->set_message('itorden',"La orden ($orden) No pertenece al proveedor ($cod_prov)");
                        return false;
                }
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

        function positivo($valor){
                if ($valor <= 0){
                        $this->validation->set_message('positivo',"El campo Monto debe ser positivo");
                        return FALSE;
                }
          return TRUE;
        }


        function banco($valor){
                $valor = $this->db->escape($valor);

                $cana=$this->datasis->dameval("SELECT COUNT(*) FROM banc WHERE codbanc=$valor AND activo='S'");// AND status='O'

                if($cana>0){
                        return true;
                }else{
                        $this->validation->set_message('banco',"El banco ($valor)  no existe o esta inactivo");
                        return false;
                }
        }

        function _valida($do){
                $this->rapyd->load('dataobject');

                $odirect = new DataObject("odirect");

                $numero   = $do->get('numero'  );
                $cod_prov = $do->get('cod_prov');
                $do->set('status','D1'         );
                $user     = $this->session->userdata('usuario');
				$do->count_rel('pades');
                $tot2=$tot=0;$error='';$pades=array();
                for($i=0;$i < $do->count_rel('pades');$i++){
                        $pades[] = $pago = $do->get_rel('pades','pago'   ,$i);

                        $odirect->load($pago);

                        $status = '';
                        $total     = $odirect->get('total');
                        $total2    = $odirect->get('total2');
                        $status    = $odirect->get('status');
                        if(substr($status,1,1)!='2')
                                $error.="<div class='alert'><p>No de puede realizar la operaci&oacute,n para la orden de pago ($pago)</p></div>";
                        $tot      += $total;
                        $tot2     += $total2;
                        
                        
                }

                $do->set('total',$tot);

                $totch=0;$b=array();$bancos=array();
				for($i=0;$i < $do->count_rel('mbanc');$i++){
					$do->set_rel('mbanc','usuario',$user         ,$i);
					$do->set_rel('mbanc','estampa',date('YmdHis'),$i);
					$monto     = $do->get_rel('mbanc','monto'    ,$i);

					$do->set_rel('mbanc','cod_prov',$cod_prov    ,$i);

					$mstatus   = 'E1';
					$do->set_rel('mbanc','status'  ,$mstatus     ,$i);

					$codbanc   = $do->get_rel('mbanc','codbanc'  ,$i);
					$tipo_doc  = $do->get_rel('mbanc','tipo_doc' ,$i);
					$fecha     = $do->get_rel('mbanc','fecha'    ,$i);
					$cheque    = $do->get_rel('mbanc','cheque'   ,$i);
					$id        = $do->get_rel('mbanc','id'       ,$i);
					$codbance  =$this->db->escape($codbanc);
					$bancos[]  =$codbanc;

					if($tipo_doc=='CH' || $tipo_doc=='ND')
					$do->set_rel('mbanc','liable','S',$i);

					$refe = $this->datasis->dameval("SELECT refe FROM banc WHERE codbanc=$codbance");
					if($refe=='E')
					$do->set_rel('mbanc','cheque2',$cheque,$i);

					//if(in_array($codbanc,$b))
					//      $b[$codbanc] +=1;
					//else
					//      $b[$codbanc]  =1;

					//if($status=='E1')
					
					if($tipo_doc=='DP' || $tipo_doc=='NC')
						$totch-=$monto;
					else
						$totch+=$monto;
					
					//$totch      += $monto;
					
					$this->chexiste_cheque($codbanc,$cheque,$tipo_doc,$id,$e);
					$error.=$e;
				}

                if($this->datasis->traevalor('DESEM_UNSOLOFONDO')=='S'){
            	    if(count($pades)>1){
            		$padesi=implode(',',$pades);
            		$cfondo=$this->datasis->dameval("SELECT COUNT(*) FROM (SELECT fondo FROM v_pagossolo WHERE numero IN ($padesi) GROUP BY fondo)a");
            		if($cfondo>1)
            		$error.="No se pueden mezclar ordenes de pago de Fuentes de Financiamiento distintas</br>";
		    }

		    if($this->datasis->traevalor('DESEM_OBLIGABANCFOND','N','OBLIGA QUE LOS CHEQUES DEL BANCO PERTENECEN AL FONDO DEL PAGO SE USA CON DESEM_UNSOLOFONDO')=='S'){
            		$padesi=implode(',',$pades);

            		$cfondo=$this->datasis->dameval("SELECT fondo FROM v_pagossolo WHERE numero IN ($padesi) GROUP BY fondo LIMIT 1");

            		foreach($bancos AS $k=>$v){
            			$ve=$this->db->escape($v);
            			$bfondo=$this->datasis->dameval("SELECT fondo2  FROM banc WHERE codbanc=$ve LIMIT 1");

            			if(strlen($cfondo)>0){
            				if($cfondo<>$bfondo)
					$error.="Error. No se pueden pagar las ordenes por ese banco</br>";
				}
			}
		    }
                }

                $do->set('totalch',$totch);

                if($this->datasis->traevalor('OBLIGACHEQUES','N','Obliga a que la suma de los cheques sea igual al total con retenciones de ordenes de pago')=='S')
                    $t=$tot2;
                else
                    $t=$tot;

                if(round($t,2) != round($totch,2))
                        $error.="<div class='alert'><p>La Suma de los totales de ordenes de pago es diferente a la suma de los cheque activos</p></div>";

                if(!empty($error)){
                        $do->error_message_ar['pre_ins']=$error;
                        $do->error_message_ar['pre_upd']=$error;
                        return false;
                }
        }

        function chexiste_cheque($codbanc,$cheque,$tipo_doc,$id,&$error){
                $error      = "";
                $cana=0;
                if($tipo_doc!='ND'){
                        $cheque     = $this->db->escape($cheque       );
                        $tipo_doc   = $this->db->escape($tipo_doc     );
                        $codbanc    = $this->db->escape($codbanc      );
                        if($id>0)$query="SELECT id FROM mbanc WHERE codbanc=$codbanc AND cheque=$cheque AND tipo_doc=$tipo_doc AND id<>$id  AND MID(status,2,1)=2";
                        else $query="SELECT id FROM mbanc WHERE codbanc=$codbanc AND cheque=$cheque AND tipo_doc=$tipo_doc AND MID(status,2,1)=2 ";
                        $cana=$this->datasis->dameval($query);
                }


                if($cana>0){
                        $pago = '';
                        //$pago = $this->datasis->dameval("SELECT b.pago FROM mbanc a JOIN pambanc b ON a.id = b.mbanc WHERE b.mbanc=$cana LIMIT 1");
                        $error="Cheque Emitido, por favor cambie el numero de cheque";
                }
        }

        function actpresup($id){
                $this->rapyd->load('dataobject');

                $error='';

                $do = new DataObject("desem");

                $do->rel_one_to_many('pades', 'pades', array('numero'=>'desem'));
                $do ->load($id);
                $fdesem=$do->get('fdesem');
                $fdesem=str_replace('-','',$fdesem);

                $odirect = new DataObject("odirect");
                $odirect -> rel_one_to_many('pacom'    , 'pacom'    , array('numero'=>'pago'));
                $odirect -> rel_one_to_many('itodirect', 'itodirect', array('numero'=>'numero'));
                $odirect -> rel_one_to_many('itfac'    , 'itfac'    , array('numero'=>'numero'));

                $ocompra = new DataObject("ocompra");
                $ocompra->rel_one_to_many('itocompra', 'itocompra', array('numero'=>'numero'));

                for($i=0;$i < $do->count_rel('pades');$i++){
                        $pago     = $do->get_rel('pades','pago'      ,$i);

                        $odirect->load($pago);
                        //print_r($odirect->get_all());

                        $status      =  $odirect->get('status'          );
                        $factura     =  $odirect->get('factura'         );
                        $fechafac    =  $odirect->get('fechafac'        );
                        $controlfac  =  $odirect->get('controlfac'      );
                        $cod_prov    =  $odirect->get('cod_prov'        );
                        $exento      =  $odirect->get('exento'          );
                        $tivag       =  $odirect->get('tivag'           );
                        $mivag       =  $odirect->get('mivag'           );
                        $ivag        =  $odirect->get('ivag'            );
                        $tivaa       =  $odirect->get('tivaa'           );
                        $mivag       =  $odirect->get('mivag'           );
                        $ivaa        =  $odirect->get('ivaa'            );
                        $tivar       =  $odirect->get('tivar'           );
                        $mivar       =  $odirect->get('mivar'           );
                        $ivar        =  $odirect->get('ivar'            );
                        $subtotal    =  $odirect->get('subtotal'        );
                        $reteiva     =  $odirect->get('reteiva'         );
                        $reteiva_prov=  $odirect->get('reteiva_prov'    );
                        $codigoadm   =  $odirect->get('estadmin'        );
                        $fondo       =  $odirect->get('fondo'           );

                        if($status == "N2" ){
                        }elseif($status == "F2" ){
                                for($j=0;$j < $odirect->count_rel('pacom');$j++){
                                        $p_t       = $odirect->get_rel('pacom','total' ,$j);
                                        $p_compra  = $odirect->get_rel('pacom','compra',$j);

                                        $ocompra->load($p_compra);

                                        $status        = $ocompra->get('status'      );
                                        $creten        = $ocompra->get('creten'      );
                                        $fechafac      = $ocompra->get('fechafac'    );
                                        $factura       = $ocompra->get('factura'     );
                                        $controlfac    = $ocompra->get('controlfac'  );
                                        $exento        = $ocompra->get('exento'      );
                                        $tivaa         = $ocompra->get('tivaa'       );
                                        $tivag         = $ocompra->get('tivag'       );
                                        $tivar         = $ocompra->get('tivar'       );
                                        $ivaa          = $ocompra->get('ivaa'        );
                                        $ivag          = $ocompra->get('ivag'        );
                                        $ivar          = $ocompra->get('ivar'        );
                                        $mivaa         = $ocompra->get('mivaa'       );
                                        $mivag         = $ocompra->get('mivag'       );
                                        $mivar         = $ocompra->get('mivar'       );
                                        $subtotal      = $ocompra->get('subtotal'    );
                                        $reteiva       = $ocompra->get('reteiva'     );
                                        $tislr=$reten  = $ocompra->get('reten'       );
                                        $total         = $ocompra->get('total'       );
                                        $reteiva_prov  = $ocompra->get('reteiva_prov');
                                        $ivan          = $ivag+$ivar+$ivaa;

                                        if(true){//$total==$pagado
                                                $ivan=0;$admfondo=array();$importes=array(); $ivas=array();
                                                for($k=0;$k < $ocompra->count_rel('itocompra');$k++){
                                                        $codigoadm   = $ocompra->get_rel('itocompra','codigoadm',$k);
                                                        $fondo       = $ocompra->get_rel('itocompra','fondo'    ,$k);
                                                        $codigopres  = $ocompra->get_rel('itocompra','partida'  ,$k);
                                                        $ordinal     = $ocompra->get_rel('itocompra','ordinal'  ,$k);
                                                        $iva         = $ocompra->get_rel('itocompra','iva'      ,$k);
                                                        $importe     = $ocompra->get_rel('itocompra','importe'  ,$k);
                                                        $ivan        = $importe*$iva/100;

                                                        $cadena = $codigoadm.'_._'.$fondo.'_._'.$codigopres;//.'_._'.$ordinal.'_._'.$iva;
                                                        if(array_key_exists($cadena,$importes)){
                                                                $importes[$cadena]+=$importe;
                                                                //$ivas[$cadena]     =$iva;
                                                        }else{
                                                                $importes[$cadena]  =$importe;
                                                                //$ivas[$cadena]      =$iva;
                                                        }
                                                        $cadena2 = $codigoadm.'_._'.$fondo;
                                                        $admfondo[$cadena2]=(array_key_exists($cadena2,$admfondo)?$admfondo[$cadena2]+=$ivan:$admfondo[$cadena2] =$ivan);
                                                }

                                                //foreach($admfondo AS $cadena=>$monto){
                                                //      $temp  = explode('_._',$cadena);
                                                //      $error.=$this->chequeapresup($temp[0],$temp[1],'PARTIDAIVA','',$monto,0,'round($monto,2) > $disponible=round(($opago-$pagado),2)','El Monto ($monto) es mayor al disponible (round(($disponible),2)) para pagar para la partida de IVA, ('.$temp[0].')('.$temp[1].') ');
                                                //}

                                                foreach($importes AS $cadena=>$monto){
                                                        $temp  = explode('_._',$cadena);
                                                        //$iva   = $ivas[$cadena];
                                                        $error.=$this->chequeapresup($temp[0],$temp[1],$temp[2],'',$monto,0,'round($monto,2) > $disponible=round(($opago-$pagado),2)','El Monto ($monto) es mayor al posible a pagar ($disponible) para la partida ('.$temp[0].' ('.$temp[1].') ('.$temp[2].')');
                                                }
                                        }
                                        if($reteiva > 0){
                                                $p_comprae = $this->db->escape($p_compra);
                                                $cant=$this->datasis->dameval("SELECT COUNT(*) FROM itfac WHERE nocompra=$p_comprae");

                                                if($cant>0){

                                                $itfac2 = new DataObject("ocompra");
                                                $itfac2-> rel_one_to_many('itfac' , 'itfac' , array('numero'=>'nocompra'));
                                                $itfac2->load($p_compra);

                                                for($b=0;$b < $itfac2->count_rel('itfac');$b++){
                                                        $itfact         = $itfac2->get_rel('itfac','id'              ,$b);
                                                        $factura        = $itfac2->get_rel('itfac','factura'         ,$b);
                                                        $controlfac     = $itfac2->get_rel('itfac','controlfac'      ,$b);
                                                        $fechafac       = $itfac2->get_rel('itfac','fechafac'        ,$b);
                                                        $ivaa           = $itfac2->get_rel('itfac','ivaa'            ,$b);
                                                        $ivar           = $itfac2->get_rel('itfac','ivar'            ,$b);
                                                        $ivag           = $itfac2->get_rel('itfac','ivag'            ,$b);
                                                        $exento         = $itfac2->get_rel('itfac','exento'          ,$b);
                                                        $reteiva        = $itfac2->get_rel('itfac','reteiva'         ,$b);
                                                        $mivag          = $ivag*100/$tivag;
                                                        $mivar          = $ivar*100/$tivar;
                                                        $mivaa          = $ivaa*100/$tivaa;

                                                        $error.=$this->chriva('',$codigoadm,$fondo,$p_compra,$pago,$itfact,$factura,$controlfac,$fechafac,$cod_prov,$exento,$tivag,$mivag,$tivaa,$ivaa,$tivar,$mivar,$reteiva,'',$id,$reteiva_prov);
                                                        }
                                                }else{
                                                        $error.=$this->chriva('',$codigoadm,$fondo,$p_compra,$pago,$itfact='',$factura,$controlfac,$fechafac,$cod_prov,$exento,$tivag,$mivag,$tivaa,$ivaa,$tivar,$mivar,$reteiva,'',$id,$reteiva_prov);
                                                }
                                        }
                                }
                        }elseif($status == "B2"){

                                $totiva = 0;$ivan=0;$importes=array(); $ivas=array();$admfondo=array();
                                for($g=0;$g   <  $odirect->count_rel('itodirect');$g++){
                                        $codigoadm   = $odirect->get_rel('itodirect','codigoadm',$g);
                                        $fondo       = $odirect->get_rel('itodirect','fondo'    ,$g);
                                        $codigopres  = $odirect->get_rel('itodirect','partida'  ,$g);
                                        $importe     = $odirect->get_rel('itodirect','importe'  ,$g);
                                        $ordinal     = $odirect->get_rel('itodirect','ordinal'  ,$g);
                                        $iva         = $odirect->get_rel('itodirect','iva'      ,$g);
                                        $ivan        = $importe*$iva/100;

                                        $error.=$this->itpartida($codigoadm,$fondo,$codigopres,$ordinal);

                                        $cadena = $codigoadm.'_._'.$fondo.'_._'.$codigopres;//.'_._'.$ordinal.'_._'.$iva;
                                        if(array_key_exists($cadena,$importes)){
                                                $importes[$cadena]+=$importe;
                                                //$ivas[$cadena]     =$iva;
                                        }else{
                                                $importes[$cadena]  =$importe;
                                                //$ivas[$cadena]      =$iva;
                                        }
                                        $cadena2 = $codigoadm.'_._'.$fondo;
                                        $admfondo[$cadena2]=(array_key_exists($cadena2,$admfondo)?$admfondo[$cadena2]+=$ivan:$admfondo[$cadena2] =$ivan);
                                }

                                if(empty($error)){
                                        //foreach($admfondo AS $cadena=>$monto){
                                        //      $temp  = explode('_._',$cadena);
                                        //      $error.=$this->chequeapresup($temp[0],$temp[1],'PARTIDAIVA','',$monto,0,'round($monto,2) > $disponible=round(($opago-$pagado),2)','El Monto ($monto) es mayor al disponible (round(($disponible),2)) para pagar para la partida de IVA, ('.$temp[0].')('.$temp[1].') ');
                                        //}

                                        foreach($importes AS $cadena=>$monto){
                                                $temp  = explode('_._',$cadena);
                                                //$iva   = $ivas[$cadena];
                                                $error.=$this->chequeapresup($temp[0],$temp[1],$temp[2],'',$monto,0,'round($monto,2) > $disponible=round(($opago-$pagado),2)','El Monto ($monto) es mayor al disponible ($disponible) para pagar para la partida ('.$temp[0].') ('.$temp[1].') ('.$temp[2].') ');
                                        }
                                }
                                if(empty($error)){
                                        if($reteiva >0){
                                                if($odirect->get('multiple')=='N'){
                                                        $error.=$this->chriva('',$codigoadm,$fondo,'',$pago,$itfact='',$factura,$controlfac,$fechafac,$cod_prov,$exento,$tivag,$mivag,$tivaa,$ivaa,$tivar,$mivar,$reteiva,'',$id,$reteiva_prov);
                                                }elseif($odirect->get('multiple')=='S' && empty($error)){
                                                        for($l=0;$l < $odirect->count_rel('itfac');$l++){
                                                                $iditfac     = $odirect->get_rel('itfac','id',$l        );
                                                                $factura     = $odirect->get_rel('itfac','factura'   ,$l);
                                                                $fechafac    = $odirect->get_rel('itfac','fechafac'  ,$l);
                                                                $controlfac  = $odirect->get_rel('itfac','controlfac',$l);
                                                                $exento      = $odirect->get_rel('itfac','exento'    ,$l);
                                                                $ivag        = $odirect->get_rel('itfac','ivag'      ,$l);
                                                                $ivaa        = $odirect->get_rel('itfac','ivaa'      ,$l);
                                                                $ivar        = $odirect->get_rel('itfac','ivar'      ,$l);
                                                                $reteiva     = $odirect->get_rel('itfac','reteiva'   ,$l);
                                                                $mivag       = $ivag*100/$tivag;
                                                                $mivar       = $ivar*100/$tivar;
                                                                $mivaa       = $ivaa*100/$tivaa;
                                                                if($reteiva>0){
                                                                        $error.=$this->chriva('',$codigoadm,$fondo,'',$pago,$iditfac,$factura,$controlfac,$fechafac,$cod_prov,$exento,$tivag,$mivag,$tivaa,$ivaa,$tivar,$mivar,$reteiva,'',$id,$reteiva_prov);
                                                                }
                                                        }
                                                }
                                        }
                                }


                        }elseif($status == "C2"){

                                $totiva = 0;$ivan=0;$importes=array(); $ivas=array();$admfondo=array();
                                for($g=0;$g   <  $odirect->count_rel('itodirect');$g++){
                                        $codigoadm   = $odirect->get_rel('itodirect','codigoadm',$g);
                                        $fondo       = $odirect->get_rel('itodirect','fondo'    ,$g);
                                        $codigopres  = $odirect->get_rel('itodirect','partida'  ,$g);
                                        $importe     = $odirect->get_rel('itodirect','importe'  ,$g);
                                        $ordinal     = $odirect->get_rel('itodirect','ordinal'  ,$g);
                                        $iva         = $odirect->get_rel('itodirect','iva'      ,$g);
                                        $ivan        = $importe*$iva/100;

                                        $error.=$this->itpartida($codigoadm,$fondo,$codigopres,$ordinal);

                                        $cadena = $codigoadm.'_._'.$fondo.'_._'.$codigopres;//.'_._'.$ordinal.'_._'.$iva;
                                        if(array_key_exists($cadena,$importes)){
                                                $importes[$cadena]+=$importe;
                                                //$ivas[$cadena]     =$iva;
                                        }else{
                                                $importes[$cadena]  =$importe;
                                                //$ivas[$cadena]      =$iva;
                                        }
                                        $cadena2 = $codigoadm.'_._'.$fondo;
                                        $admfondo[$cadena2]=(array_key_exists($cadena2,$admfondo)?$admfondo[$cadena2]+=$ivan:$admfondo[$cadena2] =$ivan);
                                }

                                if(empty($error)){
                                        //foreach($admfondo AS $cadena=>$monto){
                                        //      $temp  = explode('_._',$cadena);
                                        //      $error.=$this->chequeapresup($temp[0],$temp[1],'PARTIDAIVA','',$monto,0,'round($monto,2) > $disponible=round(($opago-$pagado),2)','El Monto ($monto) es mayor al disponible (round(($disponible),2)) para pagar para la partida de IVA, ('.$temp[0].')('.$temp[1].') ');
                                        //}

                                        foreach($importes AS $cadena=>$monto){
                                                $temp  = explode('_._',$cadena);
                                                //$iva   = $ivas[$cadena];
                                                $error.=$this->chequeapresup($temp[0],$temp[1],$temp[2],'',$monto,0,'round($monto,2) > $disponible=round(($opago-$pagado),2)','El Monto ($monto) es mayor al disponible ($disponible) para pagar para la partida ('.$temp[0].') ('.$temp[1].') ('.$temp[2].') ');
                                        }
                                }
                                if(empty($error)){
                                        if($reteiva >0){
                                                
											for($l=0;$l < $odirect->count_rel('itfac');$l++){
														$iditfac     = $odirect->get_rel('itfac','id',$l        );
														$factura     = $odirect->get_rel('itfac','factura'   ,$l);
														$fechafac    = $odirect->get_rel('itfac','fechafac'  ,$l);
														$controlfac  = $odirect->get_rel('itfac','controlfac',$l);
														$exento      = $odirect->get_rel('itfac','exento'    ,$l);
														$ivag        = $odirect->get_rel('itfac','ivag'      ,$l);
														$ivaa        = $odirect->get_rel('itfac','ivaa'      ,$l);
														$ivar        = $odirect->get_rel('itfac','ivar'      ,$l);
														$reteiva     = $odirect->get_rel('itfac','reteiva'   ,$l);
														$subtotal    = $odirect->get_rel('itfac','subtotal'  ,$l);
														
														$d=0;
														if($ivag>0)
														$d++;
														if($ivar>0)
														$d++;
														if($ivaa>0)
														$d++;
														
														if($d>1){
															$mivag       = $ivag*100/$tivag;
															$mivar       = $ivar*100/$tivar;
															$mivaa       = $ivaa*100/$tivaa;
														}else{
															$mivag       = 0;
															$mivar       = 0;
															$mivaa       = 0;
															
															if($ivag>0)
															$mivag       = $subtotal;
															if($ivar>0)
															$mivar       = $subtotal;
															if($ivaa>0)
															$mivaa       = $subtotal;
														}
														
														if($reteiva>0){
																$error.=$this->chriva('',$codigoadm,$fondo,'',$pago,$iditfac,$factura,$controlfac,$fechafac,$cod_prov,$exento,$tivag,$mivag,$tivaa,$ivaa,$tivar,$mivar,$reteiva,'',$id,$reteiva_prov);
														}
												}												
                                        }
                                }


                        }elseif($status == "K2"){
                                //exit("llego");
                                //echo "lego a k2 de valida:$pago</br>";

                                $totiva = 0;$ivan=0;$importes=array();$admfondo=array();
                                for($g=0;$g   <  $odirect->count_rel('itodirect');$g++){
                                        $codigoadm   = $odirect->get_rel('itodirect','codigoadm',$g);
                                        $fondo       = $odirect->get_rel('itodirect','fondo'    ,$g);
                                        $codigopres  = $odirect->get_rel('itodirect','partida'  ,$g);
                                        $importe     = $odirect->get_rel('itodirect','importe'  ,$g);
                                        $ordinal     = $odirect->get_rel('itodirect','ordinal'  ,$g);
                                        $iva         = $odirect->get_rel('itodirect','iva'      ,$g);
                                        $ivan        = $importe*$iva/100;

                                        $error.=$this->itpartida($codigoadm,$fondo,$codigopres,$ordinal);

                                        $cadena = $codigoadm.'_._'.$fondo.'_._'.$codigopres;//.'_._'.$ordinal.'_._'.$iva;
                                        if(array_key_exists($cadena,$importes)){
                                                $importes[$cadena]+=$importe;
                                                //$ivas[$cadena]     =$iva;
                                        }else{
                                                $importes[$cadena]  =$importe;
                                                //$ivas[$cadena]      =$iva;
                                        }
                                        $cadena2 = $codigoadm.'_._'.$fondo;
                                        $admfondo[$cadena2]=(array_key_exists($cadena2,$admfondo)?$admfondo[$cadena2]+=$ivan:$admfondo[$cadena2] =$ivan);
                                }

                                if(empty($error)){
                                        foreach($admfondo AS $cadena=>$monto){
                                                $temp  = explode('_._',$cadena);
                                                $error.=$this->chequeapresup($temp[0],$temp[1],'PARTIDAIVA','',$monto,0,'round($monto,2) > $disponible=round(($opago-$pagado),2)','El Monto ($monto) es mayor al disponible (round(($disponible),2)) para pagar para la partida de IVA, ('.$temp[0].')('.$temp[1].') ');
                                        }

                                        foreach($importes AS $cadena=>$monto){
                                                $temp  = explode('_._',$cadena);
                                                //$iva   = $ivas[$cadena];
                                                $error.=$this->chequeapresup($temp[0],$temp[1],$temp[2],'',$monto,0,'round($monto,2) > $disponible=round(($opago-$pagado),2)','El Monto ($monto) es mayor al disponible ($disponible) para  pagar  para la partida ('.$temp[0].') ('.$temp[1].') ('.$temp[2].') ');
                                        }
                                }

                        }elseif($status == "I2"){

                        }elseif($status == "M2"){

                        }elseif($status=='S2'){
                        }
                        elseif($status=='R2'){
                        }elseif($status=='G2'){
                        }elseif($status=='H2'){
                        }elseif($status=='O2'){
                                $obr     = $odirect->get('obr'     );
                                $iva     = $odirect->get('iva'     );
                                $total2  = $odirect->get('total2'  );
                                $amortiza= $odirect->get('amortiza');
                                $mont    = $total2-$amortiza;

                                $obra = new DataObject("obra");
                                $obra->load($obr);

                                $codigoadm  = $obra->get('codigoadm' );
                                $fondo      = $obra->get('fondo'     );
                                $codigopres = $obra->get('codigopres');
                                $ordinal    = $obra->get('ordinal'   );

                                $error.=$this->chequeapresup($codigoadm,$fondo,$codigopres,$ordinal,$mont,0,'round($monto,2) > round(($opago-$pagado),2)',"El Monto ($mont) es mayor al disponible para la partida ($codigoadm) ($fondo) ($codigopres)");

                                //if($reteiva>0)
                                //      $this->riva($nriva,$codigoadm,$fondo,'',$pago,$itfact='',$factura,$controlfac,$fechafac,$cod_prov,$exento,$tivag,$mivag,$tivaa,$ivaa,$tivar,$mivar,$reteiva,'',$id,$reteiva_prov);

                        }else{
                                $error.="<div class='alert'><p>No se puede realizar la operacion para la orden de pago ($pago)</p></div>";
                        }
                        if(!empty($error))
                                return $error;
                }
                //print_r($odirect->get_all());
                //exit();

                if(empty($error)){
            		$nriva=null;
                        for($i=0;$i < $do->count_rel('pades');$i++){
                                $pago     = $do->get_rel('pades','pago'   ,$i);

                                $odirect ->load($pago);

                                $status      =  $odirect->get('status'          );
                                $factura     =  $odirect->get('factura'         );
                                $fechafac    =  $odirect->get('fechafac'        );
                                $controlfac  =  $odirect->get('controlfac'      );
                                $cod_prov    =  $odirect->get('cod_prov'        );
                                $exento      =  $odirect->get('exento'          );
                                $tivag       =  $odirect->get('tivag'           );
                                $mivag       =  $odirect->get('mivag'           );
                                $ivag        =  $odirect->get('ivag'            );
                                $tivaa       =  $odirect->get('tivaa'           );
                                $mivag       =  $odirect->get('mivag'           );
                                $ivaa        =  $odirect->get('ivaa'            );
                                $tivar       =  $odirect->get('tivar'           );
                                $mivar       =  $odirect->get('mivar'           );
                                $ivar        =  $odirect->get('ivar'            );
                                $subtotal    =  $odirect->get('subtotal'        );
                                $reteiva     =  $odirect->get('reteiva'         );
                                $reteiva_prov=  $odirect->get('reteiva_prov'    );
                                $codigoadm   =  $odirect->get('estadmin'        );
                                $fondo       =  $odirect->get('fondo'           );
                                //echo "llego a ejecuta:$pago status $status</br>";

                                if($reteiva>0 && $nriva===NULL)
                                        $nriva = $this->datasis->fprox_numero('nriva');

                                if($status == "N2" ){
                                        //$odirect->set('status','N3');
//                                        $nriva = $this->datasis->fprox_numero('nriva');
                                        $this->riva($nriva,$codigoadm,$fondo,'',$pago,$itfact='',$factura,$controlfac,$fechafac,$cod_prov,$exento,$tivag,$mivag,$tivaa,$ivaa,$tivar,$mivar,$reteiva,'',$id,$reteiva_prov);
                                        $this->db->query("UPDATE odirect SET status='N3',fpagado=DATE_FORMAT(NOW(),'%Y%m%d') WHERE numero='$pago'");
                                        //$odirect->save();
                                }elseif($status == "F2" ){
                                        for($j=0;$j < $odirect->count_rel('pacom');$j++){
                                                $p_t       = $odirect->get_rel('pacom','total' ,$j);
                                                $p_compra  = $odirect->get_rel('pacom','compra',$j);

                                                $ocompra->load($p_compra);

                                                $status        = $ocompra->get('status'      );
                                                $creten        = $ocompra->get('creten'      );
                                                $fechafac      = $ocompra->get('fechafac'    );
                                                $factura       = $ocompra->get('factura'     );
                                                $controlfac    = $ocompra->get('controlfac'  );
                                                $exento        = $ocompra->get('exento'      );
                                                $tivaa         = $ocompra->get('tivaa'       );
                                                $tivag         = $ocompra->get('tivag'       );
                                                $tivar         = $ocompra->get('tivar'       );
                                                $ivaa          = $ocompra->get('ivaa'        );
                                                $ivag          = $ocompra->get('ivag'        );
                                                $ivar          = $ocompra->get('ivar'        );
                                                $mivaa         = $ocompra->get('mivaa'       );
                                                $mivag         = $ocompra->get('mivag'       );
                                                $mivar         = $ocompra->get('mivar'       );
                                                $subtotal      = $ocompra->get('subtotal'    );
                                                $reteiva       = $ocompra->get('reteiva'     );
                                                $tislr=$reten  = $ocompra->get('reten'       );
                                                $total         = $ocompra->get('total'       );
                                                $reteiva_prov  = $ocompra->get('reteiva_prov');
                                                $ivan          = $ivag+$ivar+$ivaa;

                                                if(true){
                                                        if(empty($error)){
                                                                $ivan=0;$admfondo=array();$importes=array();$ivas=array();
                                                                for($k=0;$k < $ocompra->count_rel('itocompra');$k++){
                                                                        $codigoadm   = $ocompra->get_rel('itocompra','codigoadm',$k);
                                                                        $fondo       = $ocompra->get_rel('itocompra','fondo'    ,$k);
                                                                        $codigopres  = $ocompra->get_rel('itocompra','partida'  ,$k);
                                                                        $importe     = $ocompra->get_rel('itocompra','importe'  ,$k);
                                                                        $ordinal     = $ocompra->get_rel('itocompra','ordinal'  ,$k);
                                                                        $iva         = $ocompra->get_rel('itocompra','iva'      ,$k);
                                                                        $ivan        = $importe*$iva/100;

                                                                        $cadena = $codigoadm.'_._'.$fondo.'_._'.$codigopres;//.'_._'.$ordinal.'_._'.$iva;
                                                                        if(array_key_exists($cadena,$importes)){
                                                                                $importes[$cadena]+=$importe;
                                                                                //$ivas[$cadena]     =$iva;
                                                                        }else{
                                                                                $importes[$cadena]  =$importe;
                                                                                //$ivas[$cadena]      =$iva;
                                                                        }
                                                                        $cadena2 = $codigoadm.'_._'.$fondo;
                                                                        $admfondo[$cadena2]=(array_key_exists($cadena2,$admfondo)?$admfondo[$cadena2]+=$ivan:$admfondo[$cadena2] =$ivan);
                                                                }

                                                                if(empty($error)){
                                                                        foreach($importes AS $cadena=>$monto){
                                                                                $temp  = explode('_._',$cadena);
                                                                                //$iva   = $ivas[$cadena];
                                                                                $error.=$this->afectapresup($temp[0],$temp[1],$temp[2],'',$monto,0, 1 ,array("pagado"));
                                                                        }
                                                                }

                                                                if(empty($error)){

                                                                        //$ocompra->set('status','E');
                                                                        //$ocompra->save();
                                                                        $this->db->simple_query("UPDATE ocompra SET status='E' WHERE numero='$p_compra'");
                                                                }
                                                        }

                                                        if(empty($error)){
                                                                if($reteiva > 0){
                                                                        $p_comprae = $this->db->escape($p_compra);
                                                                        $cant=$this->datasis->dameval("SELECT COUNT(*) FROM itfac WHERE nocompra=$p_comprae");

                                                                        if($cant>0){

                                                                                $itfac2 = new DataObject("ocompra");
                                                                                $itfac2-> rel_one_to_many('itfac' , 'itfac' , array('numero'=>'nocompra'));
                                                                                $itfac2->load($p_compra);

                                                                                for($b=0;$b < $itfac2->count_rel('itfac');$b++){
                                                                                        $itfact         = $itfac2->get_rel('itfac','id'              ,$b);
                                                                                        $factura        = $itfac2->get_rel('itfac','factura'         ,$b);
                                                                                        $controlfac     = $itfac2->get_rel('itfac','controlfac'      ,$b);
                                                                                        $fechafac       = $itfac2->get_rel('itfac','fechafac'        ,$b);
                                                                                        $ivaa           = $itfac2->get_rel('itfac','ivaa'            ,$b);
                                                                                        $ivar           = $itfac2->get_rel('itfac','ivar'            ,$b);
                                                                                        $ivag           = $itfac2->get_rel('itfac','ivag'            ,$b);
                                                                                        $exento         = $itfac2->get_rel('itfac','exento'          ,$b);
                                                                                        $reteiva        = $itfac2->get_rel('itfac','reteiva'         ,$b);
                                                                                        $mivag          = $ivag*100/$tivag;
                                                                                        $mivar          = $ivar*100/$tivar;
                                                                                        $mivaa          = $ivaa*100/$tivaa;

                                                                                        $error.=$this->riva($nriva,$codigoadm,$fondo,$p_compra,$pago,$itfact,$factura,$controlfac,$fechafac,$cod_prov,$exento,$tivag,$mivag,$tivaa,$ivaa,$tivar,$mivar,$reteiva,'',$id,$reteiva_prov);
                                                                                }
                                                                        }else{

                                                                                $error.=$this->riva($nriva,$codigoadm,$fondo,$p_compra,$pago,$itfact='',$factura,$controlfac,$fechafac,$cod_prov,$exento,$tivag,$mivag,$tivaa,$ivaa,$tivar,$mivar,$reteiva,'',$id,$reteiva_prov);
                                                                                //exit("reteiva<=0");
                                                                        }
                                                                }
                                                        }
                                                }
                                        }
                                        if(empty($error)){
                                                $odirect->set('status','F3');
                                                //exit("UPDATE odirect SET status='F3',fpagado=$fdesem,fapagado=null WHERE numero='$pago'");
                                                $this->db->query("UPDATE odirect SET status='F3',fpagado=$fdesem,fapagado=null WHERE numero='$pago'");
                                                //$odirect->save();
                                        }
                                }elseif($status == "B2"){
                                        if(empty($error)){
                                                $ivan=0;$importes=array(); $ivas=array();$admfondo=array();
                                                for($g=0;$g < $odirect->count_rel('itodirect');$g++){
                                                        $codigoadm   = $odirect->get_rel('itodirect','codigoadm',$g);
                                                        $fondo       = $odirect->get_rel('itodirect','fondo'    ,$g);
                                                        $codigopres  = $odirect->get_rel('itodirect','partida'  ,$g);
                                                        $importe     = $odirect->get_rel('itodirect','importe'  ,$g);
                                                        $ordinal     = $odirect->get_rel('itodirect','ordinal'  ,$g);
                                                        $iva         = $odirect->get_rel('itodirect','iva'      ,$g);
                                                        $ivan        = $importe*$iva/100;

                                                        $error.=$this->itpartida($codigoadm,$fondo,$codigopres,$ordinal);

                                                        $cadena = $codigoadm.'_._'.$fondo.'_._'.$codigopres;//.'_._'.$ordinal.'_._'.$iva;
                                                        if(array_key_exists($cadena,$importes)){
                                                                $importes[$cadena]+=$importe;
                                                                //$ivas[$cadena]     =$iva;
                                                        }else{
                                                                $importes[$cadena]  =$importe;
                                                                //$ivas[$cadena]      =$iva;
                                                        }
                                                        $cadena2 = $codigoadm.'_._'.$fondo;
                                                        $admfondo[$cadena2]=(array_key_exists($cadena2,$admfondo)?$admfondo[$cadena2]+=$ivan:$admfondo[$cadena2] =$ivan);
                                                }

                                                if(empty($error)){
                                                        foreach($importes AS $cadena=>$monto){
                                                                $temp  = explode('_._',$cadena);
                                                                //$iva   = $ivas[$cadena];
                                                                $error.=$this->afectapresup($temp[0],$temp[1],$temp[2],'',$monto,0, 1 ,array("pagado"));
                                                        }
                                                        //if(empty($error)){
                                                        //      foreach($admfondo AS $cadena=>$monto){
                                                        //              $temp  = explode('_._',$cadena);
                                                        //              $error.=$this->afectapresup($temp[0],$temp[1],'PARTIDAIVA','',$monto,0, 1 ,array("pagado"));
                                                        //      }
                                                        //}
                                                }
                                        }
                                        if(empty($error)){
                                                if($reteiva >0){
                                                        if($odirect->get('multiple')=='N'){
                                                                        $error.=$this->riva($nriva,$codigoadm,$fondo,'',$pago,$itfact='',$factura,$controlfac,$fechafac,$cod_prov,$exento,$tivag,$mivag,$tivaa,$ivaa,$tivar,$mivar,$reteiva,'',$id,$reteiva_prov);
                                                        }elseif($odirect->get('multiple')=='S' && empty($error)){
                                                                for($l=0;$l < $odirect->count_rel('itfac');$l++){
                                                                        $iditfac     = $odirect->get_rel('itfac','id',$l        );
                                                                        $factura     = $odirect->get_rel('itfac','factura'   ,$l);
                                                                        $fechafac    = $odirect->get_rel('itfac','fechafac'  ,$l);
                                                                        $controlfac  = $odirect->get_rel('itfac','controlfac',$l);
                                                                        $exento      = $odirect->get_rel('itfac','exento'    ,$l);
                                                                        $ivag        = $odirect->get_rel('itfac','ivag'      ,$l);
                                                                        $ivaa        = $odirect->get_rel('itfac','ivaa'      ,$l);
                                                                        $ivar        = $odirect->get_rel('itfac','ivar'      ,$l);
                                                                        $reteiva     = $odirect->get_rel('itfac','reteiva'   ,$l);
                                                                        $mivag       = $ivag*100/$tivag;
                                                                        $mivar       = $ivar*100/$tivar;
                                                                        $mivaa       = $ivaa*100/$tivaa;
                                                                        if($reteiva>0){
                                                                                $this->riva($nriva,$codigoadm,$fondo,'',$pago,$iditfac,$factura,$controlfac,$fechafac,$cod_prov,$exento,$tivag,$mivag,$tivaa,$ivaa,$tivar,$mivar,$reteiva,'',$id,$reteiva_prov);
                                                                        }
                                                                }
                                                        }
                                                }
                                        }

                                        if(empty($error)){
                                                $odirect->set('status','B3');
                                                $this->db->simple_query("UPDATE odirect SET status='B3',fpagado=$fdesem,fapagado=null WHERE numero='$pago'");
                                                $this->sp_presucalc($codigoadm);
                                        }

                                }elseif($status == "C2"){
                                        if(empty($error)){
                                                $ivan=0;$importes=array(); $ivas=array();$admfondo=array();
                                                for($g=0;$g < $odirect->count_rel('itodirect');$g++){
                                                        $codigoadm   = $odirect->get_rel('itodirect','codigoadm',$g);
                                                        $fondo       = $odirect->get_rel('itodirect','fondo'    ,$g);
                                                        $codigopres  = $odirect->get_rel('itodirect','partida'  ,$g);
                                                        $importe     = $odirect->get_rel('itodirect','importe'  ,$g);
                                                        $ordinal     = $odirect->get_rel('itodirect','ordinal'  ,$g);
                                                        $iva         = $odirect->get_rel('itodirect','iva'      ,$g);
                                                        $ivan        = $importe*$iva/100;

                                                        $error.=$this->itpartida($codigoadm,$fondo,$codigopres,$ordinal);

                                                        $cadena = $codigoadm.'_._'.$fondo.'_._'.$codigopres;//.'_._'.$ordinal.'_._'.$iva;
                                                        if(array_key_exists($cadena,$importes)){
                                                                $importes[$cadena]+=$importe;
                                                                //$ivas[$cadena]     =$iva;
                                                        }else{
                                                                $importes[$cadena]  =$importe;
                                                                //$ivas[$cadena]      =$iva;
                                                        }
                                                        $cadena2 = $codigoadm.'_._'.$fondo;
                                                        $admfondo[$cadena2]=(array_key_exists($cadena2,$admfondo)?$admfondo[$cadena2]+=$ivan:$admfondo[$cadena2] =$ivan);
                                                }

                                                if(empty($error)){
                                                        foreach($importes AS $cadena=>$monto){
                                                                $temp  = explode('_._',$cadena);
                                                                //$iva   = $ivas[$cadena];
                                                                $error.=$this->afectapresup($temp[0],$temp[1],$temp[2],'',$monto,0, 1 ,array("pagado"));
                                                        }
                                                        //if(empty($error)){
                                                        //      foreach($admfondo AS $cadena=>$monto){
                                                        //              $temp  = explode('_._',$cadena);
                                                        //              $error.=$this->afectapresup($temp[0],$temp[1],'PARTIDAIVA','',$monto,0, 1 ,array("pagado"));
                                                        //      }
                                                        //}
                                                }
                                        }
                                        if(empty($error)){
                                                if($reteiva >0){
													for($l=0;$l < $odirect->count_rel('itfac');$l++){
														$iditfac     = $odirect->get_rel('itfac','id',$l        );
														$factura     = $odirect->get_rel('itfac','factura'   ,$l);
														$fechafac    = $odirect->get_rel('itfac','fechafac'  ,$l);
														$controlfac  = $odirect->get_rel('itfac','controlfac',$l);
														$exento      = $odirect->get_rel('itfac','exento'    ,$l);
														$ivag        = $odirect->get_rel('itfac','ivag'      ,$l);
														$ivaa        = $odirect->get_rel('itfac','ivaa'      ,$l);
														$ivar        = $odirect->get_rel('itfac','ivar'      ,$l);
														$reteiva     = $odirect->get_rel('itfac','reteiva'   ,$l);
														$subtotal    = $odirect->get_rel('itfac','subtotal'  ,$l);
														
														$d=0;
														if($ivag>0)
														$d++;
														if($ivar>0)
														$d++;
														if($ivaa>0)
														$d++;
														
														if($d>1){
															$mivag       = $ivag*100/$tivag;
															$mivar       = $ivar*100/$tivar;
															$mivaa       = $ivaa*100/$tivaa;
														}else{
															$mivag       = 0;
															$mivar       = 0;
															$mivaa       = 0;
															
															if($ivag>0)
															$mivag       = $subtotal;
															if($ivar>0)
															$mivar       = $subtotal;
															if($ivaa>0)
															$mivaa       = $subtotal;
														}
														
														if($reteiva>0){
															
																$this->riva($nriva,$codigoadm,$fondo,'',$pago,$iditfac,$factura,$controlfac,$fechafac,$cod_prov,$exento,$tivag,$mivag,$tivaa,$ivaa,$tivar,$mivar,$reteiva,'',$id,$reteiva_prov,'',$ivag,$ivaa,$ivar);
														}
													}
                                                }
                                        }

                                        if(empty($error)){
                                                $odirect->set('status','C3');
                                                $this->db->simple_query("UPDATE odirect SET status='C3',fpagado=$fdesem,fapagado=null WHERE numero='$pago'");
                                                //$this->sp_presucalc($codigoadm);
                                        }
                                }elseif($status == "K2"){
                                //echo "entro a status=='K2' de ejecuta</br>";

                                        if(empty($error)){
                                                $ivan=0;
                                                for($g=0;$g < $odirect->count_rel('itodirect');$g++){
                                                        $codigoadm   = $odirect->get_rel('itodirect','codigoadm',$g);
                                                        $fondo       = $odirect->get_rel('itodirect','fondo'    ,$g);
                                                        $codigopres  = $odirect->get_rel('itodirect','partida'  ,$g);
                                                        $importe     = $odirect->get_rel('itodirect','importe'  ,$g);
                                                        $ordinal     = $odirect->get_rel('itodirect','ordinal'  ,$g);
                                                        $iva         = $odirect->get_rel('itodirect','iva'      ,$g);
                                                        $ivan       += $importe*$iva/100;

                                                        $error.=$this->afectapresup($codigoadm,$fondo,$codigopres,$ordinal,$importe,$iva, 1 ,array("pagado"));
                                                }

                                                //if(empty($error))
                                                //      $error.=$this->afectapresup($codigoadm,$fondo,'PARTIDAIVA','',$ivan,0, 1 ,array("pagado"));
                                        }

                                        if(empty($error)){
                                                $nomina = $odirect->get('nomina');
                                                $nomi = new DataObject("nomi");
                                                $nomi->rel_one_to_many('retenomi', 'retenomi', array('numero'=>'numero'));
                                                $nomi->load($nomina);
                                                for($ii=0;$ii < $nomi->count_rel('retenomi');$ii++){
                                                        $nomi->set_rel('retenomi','status' , 'D',$ii);
                                                }
                                                $nomi->set('status','E');
                                                $nomi->save();
                                        }

                                        if(empty($error)){
                                                $odirect->set('status','K3');
                                                $this->db->query("UPDATE odirect SET status='K3',fpagado=$fdesem,fapagado=null WHERE numero='$pago'");

                                        }
                                }elseif($status == "I2"){
                                        $odirect->set('status','I3');
                                        //$odirect->save();
                                }elseif($status == "M2"){
                                                $odirect->set('status','M3');
                                                $this->db->simple_query("UPDATE odirect SET status='M3',fpagado=$fdesem,fapagado=null WHERE numero='$pago'");
                                                //$odirect->save();
                                }elseif($status=='S2'){
                                        $odirect->set('status','S3');
                                        //$odirect->save();
                                }
                                elseif($status=='R2'){
                                        $odirect->set('status','R3');
                                        //$odirect->save();
                                }elseif($status=='G2'){
                                        $odirect->set('status','G3');
                                        $this->db->simple_query("UPDATE odirect SET status='G3',fpagado=$fdesem,fapagado=null WHERE numero='$pago'");
                                        //$odirect->save();
                                }elseif($status=='H2'){
                                        $odirect->set('status','H3');
                                        //$odirect->save();
                                }elseif($status=='O2'){
                                        $obr     = $odirect->get('obr'     );
                                        $iva     = $odirect->get('iva'     );
                                        $total2  = $odirect->get('total2'  );
                                        $amortiza= $odirect->get('amortiza');
                                        $mont    = $total2-$amortiza;

                                        $obra = new DataObject("obra");
                                        $obra->load($obr);

                                        $codigoadm  = $obra->get('codigoadm' );
                                        $fondo      = $obra->get('fondo'     );
                                        $codigopres = $obra->get('codigopres');
                                        $ordinal    = $obra->get('ordinal'   );

                                        //$error.=$this->chequeapresup($codigoadm,$fondo,$codigopres,$ordinal,$mont,0,'$monto > ($opago-$pagado)',"El Monto ($mont) es mayor al disponible para la partida ($codigoadm) ($fondo) ($codigopres)");

                                        if(empty($error))
                                                $error.=$this->afectapresup($codigoadm,$fondo,$codigopres,$ordinal,$mont,0, 1 ,array("pagado"));

                                        if(empty($error)){
                                                if($reteiva>0)
                                                        $this->riva($nriva,$codigoadm,$fondo,'',$pago,$itfact='',$factura,$controlfac,$fechafac,$cod_prov,$exento,$tivag,$mivag,$tivaa,$ivaa,$tivar,$mivar,$reteiva,'',$id,$reteiva_prov);

                                                $odirect->set('status','O3');
                                                $this->db->simple_query("UPDATE odirect SET status='O3',fpagado=$fdesem,fapagado=null WHERE numero='$pago'");
                                                //$odirect->save();
                                                //$this->sp_presucalc($codigoadm);
                                        }
                                }else{
                                        $error.="<div class='alert'><p>2No de puede realizar la operacion para la orden de pago ($pago)</p></div>";
                                }
                                if(!empty($error))
                                        return $error;
                                //if(empty($error))
                                //$this->db->simple_query("UPDATE odirect SET status='N3' WHERE numero=$pago");
                                //$odirect->save();

                        }

                }
        }

        function reverpresup($id){
                $this->rapyd->load('dataobject');

                $error='';

                $do = new DataObject("desem");

                $do->rel_one_to_many('pades', 'pades', array('numero'=>'desem'));
                $do ->load($id);

                $odirect = new DataObject("odirect");
                $odirect -> rel_one_to_many('pacom'    , 'pacom'    , array('numero'=>'pago'));
                $odirect -> rel_one_to_many('itodirect', 'itodirect', array('numero'=>'numero'));
                $odirect -> rel_one_to_many('itfac'    , 'itfac'    , array('numero'=>'numero'));

                $ocompra = new DataObject("ocompra");
                $ocompra->rel_one_to_many('itocompra', 'itocompra', array('numero'=>'numero'));

                for($i=0; $i < $do->count_rel('pades'); $i++){
                        $pago     = $do->get_rel('pades','pago'   ,$i);
                        $odirect ->load($pago);

                        $status      =  $odirect->get('status'          );
                        $factura     =  $odirect->get('factura'         );
                        $fechafac    =  $odirect->get('fechafac'        );
                        $controlfac  =  $odirect->get('controlfac'      );
                        $cod_prov    =  $odirect->get('cod_prov'        );
                        $exento      =  $odirect->get('exento'          );
                        $tivag       =  $odirect->get('tivag'           );
                        $mivag       =  $odirect->get('mivag'           );
                        $ivag        =  $odirect->get('ivag'            );
                        $tivaa       =  $odirect->get('tivaa'           );
                        $mivag       =  $odirect->get('mivag'           );
                        $ivaa        =  $odirect->get('ivaa'            );
                        $tivar       =  $odirect->get('tivar'           );
                        $mivar       =  $odirect->get('mivar'           );
                        $ivar        =  $odirect->get('ivar'            );
                        $subtotal    =  $odirect->get('subtotal'        );
                        $reteiva     =  $odirect->get('reteiva'         );
                        $reteiva_prov=  $odirect->get('reteiva_prov'    );

                        if($status == "F3" ){
                                for($j=0;$j < $odirect->count_rel('pacom');$j++){

                                        $p_t       = $odirect->get_rel('pacom','total' ,$j);
                                        $p_compra  = $odirect->get_rel('pacom','compra',$j);

                                        $ocompra->load($p_compra);

                                        $status        = $ocompra->get('status'      );
                                        $creten        = $ocompra->get('creten'      );
                                        $fechafac      = $ocompra->get('fechafac'    );
                                        $factura       = $ocompra->get('factura'     );
                                        $controlfac    = $ocompra->get('controlfac'  );
                                        $exento        = $ocompra->get('exento'      );
                                        $tivaa         = $ocompra->get('tivaa'       );
                                        $tivag         = $ocompra->get('tivag'       );
                                        $tivar         = $ocompra->get('tivar'       );
                                        $ivaa          = $ocompra->get('ivaa'        );
                                        $ivag          = $ocompra->get('ivag'        );
                                        $ivar          = $ocompra->get('ivar'        );
                                        $mivaa         = $ocompra->get('mivaa'       );
                                        $mivag         = $ocompra->get('mivag'       );
                                        $mivar         = $ocompra->get('mivar'       );
                                        $subtotal      = $ocompra->get('subtotal'    );
                                        $reteiva       = $ocompra->get('reteiva'     );
                                        $tislr=$reten  = $ocompra->get('reten'       );
                                        $total         = $ocompra->get('total'       );
                                        $reteiva_prov  = $ocompra->get('reteiva_prov');
                                        $ivan          = $ivag+$ivar+$ivaa;

                                        if(true){//$total==$pagado
                                                $ivan=0;$importes=array(); $ivas=array();$admfondo=array();
                                                for($k=0;$k < $ocompra->count_rel('itocompra');$k++){
                                                        $codigoadm   = $ocompra->get_rel('itocompra','codigoadm',$k);
                                                        $fondo       = $ocompra->get_rel('itocompra','fondo'    ,$k);
                                                        $codigopres  = $ocompra->get_rel('itocompra','partida'  ,$k);
                                                        $ordinal     = $ocompra->get_rel('itocompra','ordinal'  ,$k);
                                                        $iva         = $ocompra->get_rel('itocompra','iva'      ,$k);
                                                        $importe     = $ocompra->get_rel('itocompra','importe'  ,$k);
                                                        $ivan        = $importe*$iva/100;

                                                        $error.=$this->itpartida($codigoadm,$fondo,$codigopres,$ordinal);

                                                        $cadena = $codigoadm.'_._'.$fondo.'_._'.$codigopres;//.'_._'.$ordinal.'_._'.$iva;
                                                        if(array_key_exists($cadena,$importes)){
                                                                $importes[$cadena]+=$importe;
                                                                //$ivas[$cadena]     =$iva;
                                                        }else{
                                                                $importes[$cadena]  =$importe;
                                                                //$ivas[$cadena]      =$iva;
                                                        }
                                                        $cadena2 = $codigoadm.'_._'.$fondo;
                                                        $admfondo[$cadena2]=(array_key_exists($cadena2,$admfondo)?$admfondo[$cadena2]+=$ivan:$admfondo[$cadena2] =$ivan);

                                                }

                                                if(empty($error)){
                                                        foreach($importes AS $cadena=>$monto){
                                                                $temp  = explode('_._',$cadena);
                                                                //$iva   = $ivas[$cadena];
                                                                $error.=$this->chequeapresup($temp[0],$temp[1],$temp[2],'',$monto,0,'round($monto,2) > $disponible=$pagado','El Monto ($monto) es mayor al disponible ($disponible) para deshacer pago para la partida ('.$temp[0].') ('.$temp[1].') ('.$temp[2].') ');
                                                        }
                                                }

                                                if(empty($error)){
                                                        if($reteiva > 0)
                                                                $error.=$this->chriva_an($p_compra,$pago,$itfact='');

                                                }

                                                if(empty($error)){
                                                    $this->riva_an($p_compra,$pago,$itfact='');
                                                }

                                                if(empty($error)){

                                                        foreach($importes AS $cadena=>$monto){
                                                                $temp  = explode('_._',$cadena);
                                                                //$iva   = $ivas[$cadena];
                                                                $error.=$this->afectapresup($temp[0],$temp[1],$temp[2],'',$monto,0, -1 ,array("pagado"));
                                                        }

                                                        if(empty($error)){
                                                                $ocompra->set('status','O');
                                                                $ocompra->save();
                                                        }
                                                }

                                        }
                                }
                                if(empty($error)){
                                        //$odirect->set('fpagado',NULL);
                                        $fapagado=date('Y-m-d');
                                        $odirect->set('fapagado',$fapagado);
                                        $odirect->set('status','F2');
                                        
                                        $odirect->save();
                                        
                                }

                        }elseif($status == "B3" || $status=="K3"){
                                $totiva = 0;$ivan=0;$importes=array(); $ivas=array();$admfondo=array();
                                for($g=0;$g   <  $odirect->count_rel('itodirect');$g++){
                                        $codigoadm   = $odirect->get_rel('itodirect','codigoadm',$g);
                                        $fondo       = $odirect->get_rel('itodirect','fondo'    ,$g);
                                        $codigopres  = $odirect->get_rel('itodirect','partida'  ,$g);
                                        $importe     = $odirect->get_rel('itodirect','importe'  ,$g);
                                        $ordinal     = $odirect->get_rel('itodirect','ordinal'  ,$g);
                                        $iva         = $odirect->get_rel('itodirect','iva'      ,$g);
                                        $ivan        = $importe*$iva/100;

                                        $error.=$this->itpartida($codigoadm,$fondo,$codigopres,$ordinal);

                                        $cadena = $codigoadm.'_._'.$fondo.'_._'.$codigopres;//.'_._'.$ordinal.'_._'.$iva;
                                        if(array_key_exists($cadena,$importes)){
                                                $importes[$cadena]+=$importe;
                                                //$ivas[$cadena]     =$iva;
                                        }else{
                                                $importes[$cadena]  =$importe;
                                                //$ivas[$cadena]      =$iva;
                                        }
                                        $cadena2 = $codigoadm.'_._'.$fondo;
                                        $admfondo[$cadena2]=(array_key_exists($cadena2,$admfondo)?$admfondo[$cadena2]+=$ivan:$admfondo[$cadena2] =$ivan);
                                }

                                if(empty($error)){
                                        foreach($importes AS $cadena=>$monto){
                                                $temp  = explode('_._',$cadena);
                                                //$iva   = $ivas[$cadena];
                                                $error.=$this->chequeapresup($temp[0],$temp[1],$temp[2],'',$monto,0,'round($monto,2) > $disponible=$pagado','El Monto ($monto) es mayor al disponible ($disponible) para la partida ('.$temp[0].') ('.$temp[1].') ('.$temp[2].') ');
                                        }
                                }


                                if(empty($error)){
                                        $reteiva  = $odirect->get('reteiva');
                                        if($reteiva >0){
                                                if($odirect->get('multiple')=='N'){
                                                                $error.= $this->chriva_an('',$pago,$itfact='');
                                                }elseif($odirect->get('multiple')=='S'){
                                                        for($l=0;$l < $odirect->count_rel('itfac');$l++){
                                                                $iditfac    = $odirect->get_rel('itfac','id'      ,$l );
                                                                $itreteiva  = $odirect->get_rel('itfac','reteiva' ,$l );
                                                                if($itreteiva>0)
                                                                        $error.=$this->chriva_an('',$pago,$iditfac);
                                                        }
                                                }
                                        }
                                }

                                if(empty($error)){
                                        foreach($importes AS $cadena=>$monto){
                                                $temp  = explode('_._',$cadena);
                                                //$iva   = $ivas[$cadena];
                                                $error.=$this->afectapresup($temp[0],$temp[1],$temp[2],'',$monto,0, -1 ,array("pagado"));
                                        }
                                }

                                if(empty($error) && $status=='K3'){

                                        $nomina = $odirect->get('nomina');
                                        $nomi = new DataObject("nomi");

                                        $nomi->rel_one_to_many('retenomi', 'retenomi', array('numero'=>'numero'));
                                        $nomi->load($nomina);

                                        for($m=0;$m < $nomi->count_rel('retenomi');$m++){
                                                $nomi->set_rel('retenomi','status' , 'O',$m);
                                        }

                                        $nomi->set('status','O');

                                        $nomi->save();

                                }

                                if(empty($error)){
                                        $reteiva  = $odirect->get('reteiva');
                                        if($reteiva >0){
                                                if($odirect->get('multiple')=='N'){
                                                                $this->riva_an('',$pago,$itfact='');
                                                }elseif($odirect->get('multiple')=='S'){
                                                        for($l=0;$l < $odirect->count_rel('itfac');$l++){
                                                                $iditfac     = $odirect->get_rel('itfac','id'        ,$l);
                                                                $factura     = $odirect->get_rel('itfac','factura'   ,$l);
                                                                $fechafac    = $odirect->get_rel('itfac','fechafac'  ,$l);
                                                                $controlfac  = $odirect->get_rel('itfac','controlfac',$l);
                                                                $exento      = $odirect->get_rel('itfac','exento'    ,$l);
                                                                $ivag        = $odirect->get_rel('itfac','ivag'      ,$l);
                                                                $ivaa        = $odirect->get_rel('itfac','ivaa'      ,$l);
                                                                $ivar        = $odirect->get_rel('itfac','ivar'      ,$l);
                                                                $reteiva     = $odirect->get_rel('itfac','reteiva'   ,$l);
                                                                $mivag       = $ivag*100/$tivag;
                                                                $mivar       = $ivar*100/$tivar;
                                                                $mivaa       = $ivaa*100/$tivaa;
                                                                if($reteiva>0){
                                                                        $this->riva_an('',$pago,$iditfac);
                                                                }
                                                        }
                                                }
                                        }
                                }

                                if(empty($error)){
                                        $odirect->set('status','B2');
                                        if($status=="K3")
                                                $odirect->set('status','K2');
                                                
                                        $fapagado=date('Y-m-d');
                                        $odirect->set('fapagado',$fapagado);

                                        //$odirect->set('fpagado',NULL);

                                        $odirect->save();
                                        //$this->sp_presucalc($codigoadm);
                                }
                        }elseif($status == "C3"){
                                $totiva = 0;$ivan=0;$importes=array(); $ivas=array();$admfondo=array();
                                for($g=0;$g   <  $odirect->count_rel('itodirect');$g++){
                                        $codigoadm   = $odirect->get_rel('itodirect','codigoadm',$g);
                                        $fondo       = $odirect->get_rel('itodirect','fondo'    ,$g);
                                        $codigopres  = $odirect->get_rel('itodirect','partida'  ,$g);
                                        $importe     = $odirect->get_rel('itodirect','importe'  ,$g);
                                        $ordinal     = $odirect->get_rel('itodirect','ordinal'  ,$g);
                                        $iva         = $odirect->get_rel('itodirect','iva'      ,$g);
                                        $ivan        = $importe*$iva/100;

                                        $error.=$this->itpartida($codigoadm,$fondo,$codigopres,$ordinal);

                                        $cadena = $codigoadm.'_._'.$fondo.'_._'.$codigopres;//.'_._'.$ordinal.'_._'.$iva;
                                        if(array_key_exists($cadena,$importes)){
                                                $importes[$cadena]+=$importe;
                                                //$ivas[$cadena]     =$iva;
                                        }else{
                                                $importes[$cadena]  =$importe;
                                                //$ivas[$cadena]      =$iva;
                                        }
                                        $cadena2 = $codigoadm.'_._'.$fondo;
                                        $admfondo[$cadena2]=(array_key_exists($cadena2,$admfondo)?$admfondo[$cadena2]+=$ivan:$admfondo[$cadena2] =$ivan);
                                }

                                if(empty($error)){
                                        foreach($importes AS $cadena=>$monto){
                                                $temp  = explode('_._',$cadena);
                                                //$iva   = $ivas[$cadena];
                                                $error.=$this->chequeapresup($temp[0],$temp[1],$temp[2],'',$monto,0,'round($monto,2) > $disponible=$pagado','El Monto ($monto) es mayor al disponible ($disponible) para la partida ('.$temp[0].') ('.$temp[1].') ('.$temp[2].') ');
                                        }
                                }
                                
                                if(empty($error)){
                                        $reteiva  = $odirect->get('reteiva');
                                        if($reteiva >0){
										   for($l=0;$l < $odirect->count_rel('itfac');$l++){
													$iditfac    = $odirect->get_rel('itfac','id'      ,$l );
													$itreteiva  = $odirect->get_rel('itfac','reteiva' ,$l );
													if($itreteiva>0)
															$error.=$this->chriva_an('',$pago,$iditfac);
											}
                                        }
                                }


                                if(empty($error)){
                                        foreach($importes AS $cadena=>$monto){
                                                $temp  = explode('_._',$cadena);
                                                //$iva   = $ivas[$cadena];
                                                $error.=$this->afectapresup($temp[0],$temp[1],$temp[2],'',$monto,0, -1 ,array("pagado"));
                                        }
                                }
                                
                                if(empty($error)){
                                        $reteiva  = $odirect->get('reteiva');
                                        if($reteiva >0){
										   for($l=0;$l < $odirect->count_rel('itfac');$l++){
													$iditfac    = $odirect->get_rel('itfac','id'      ,$l );
													$itreteiva  = $odirect->get_rel('itfac','reteiva' ,$l );
													if($itreteiva>0)
															$error.=$this->riva_an('',$pago,$iditfac);
											}
                                        }
                                }


                                

                                if(empty($error)){
                                        $odirect->set('status','C2');

                                        $fapagado=date('Y-m-d');
                                        $odirect->set('fapagado',$fapagado);
                                        //$odirect->set('fpagado',NULL);

                                        $odirect->save();
                                        //$this->sp_presucalc($codigoadm);
                                }
                        }elseif($status == "I3"){
                                $odirect->set('status','I2');
                                //$odirect->set('fpagado',NULL);
                                $fapagado=date('Y-m-d');
                                $odirect->set('fapagado',$fapagado);
                                $odirect->save();
                        }elseif($status == "M3"){
                                $odirect->set('status','M2');
                                //$odirect->set('fpagado',NULL);
								$fapagado=date('Y-m-d');
								$odirect->set('fapagado',$fapagado);
                                $odirect->save();
                        }elseif($status=='S3'){
                                $odirect->set('status','S2');
                                //$odirect->set('fpagado',NULL);
                                $fapagado=date('Y-m-d');
								$odirect->set('fapagado',$fapagado);
                                $odirect->save();
                        }
                        elseif($status=='R3'){
                                $odirect->set('status','R2');
                                //$odirect->set('fpagado',NULL);
                                $fapagado=date('Y-m-d');
								$odirect->set('fapagado',$fapagado);
                                $odirect->save();
                        }elseif($status=='G3'){
                                $odirect->set('status','G2');
                                //$odirect->set('fpagado',NULL);
                                $fapagado=date('Y-m-d');
								$odirect->set('fapagado',$fapagado);
                                $odirect->save();
                        }elseif($status=='H3'){
                                $odirect->set('status','H2');
                                //$odirect->set('fpagado',NULL);
                                $fapagado=date('Y-m-d');
								$odirect->set('fapagado',$fapagado);
                                $odirect->save();
                        }elseif($status=='O3'){
                                $obr     = $odirect->get('obr'     );
                                $iva     = $odirect->get('iva'     );
                                $total2  = $odirect->get('total2'  );
                                $amortiza= $odirect->get('amortiza');
                                $mont    = $total2-$amortiza;

                                $obra = new DataObject("obra");
                                $obra->load($obr);

                                $codigoadm  = $obra->get('codigoadm' );
                                $fondo      = $obra->get('fondo'     );
                                $codigopres = $obra->get('codigopres');
                                $ordinal    = $obra->get('ordinal'   );

                                $error.=$this->chequeapresup($codigoadm,$fondo,$codigopres,$ordinal,$mont,0,'$monto >$pagado',"El Monto ($mont) es mayor al disponible para la partida ($codigoadm) ($fondo) ($codigopres)");

                                if(empty($error)){
                                        if($reteiva>0)
                                                $error.=$this->chriva_an('',$pago,$itfact='');
                                }

                                if(empty($error))
                                        $error.=$this->afectapresup($codigoadm,$fondo,$codigopres,$ordinal,$mont,0, -1 ,array("pagado"));

                                if(empty($error)){
                                        if($reteiva>0)
                                                $error.=$this->riva_an('',$pago,$itfact='');
                                }
                                if(empty($error)){
                                        $odirect->set('status','O2');
                                        //$odirect->set('fpagado',NULL);
                                        $fapagado=date('Y-m-d');
										$odirect->set('fapagado',$fapagado);
                                        $odirect->save();
                                        //$this->sp_presucalc($codigoadm);
                                }
                        }elseif($status=='N3'){
                                if($reteiva>0){
                                        $this->riva_an('',$pago,$iditfac);
                                }
                                $odirect->set('status','N2');
                                //$odirect->set('fpagado',NULL);
                                $fapagado=date('Y-m-d');
								$odirect->set('fapagado',$fapagado);
                                $odirect->save();
                        }else{
                                $error.="<div class='alert'><p>No de puede realizar la operacion para la orden de pago ($pago)</p></div>";
                        }


                }
                if(empty($error)){
                        return '';
                }else{
                        return $error;
                }
        }

        function actualizar($id){
                $this->rapyd->load('dataobject');

                $this->db->query("UPDATE mbanc x
                JOIN (
                SELECT a.id,GROUP_CONCAT(1*c.pago) opago
                FROM mbanc a
                JOIN desem b ON a.desem=b.numero
                JOIN pades c ON b.numero=c.desem
                JOIN odirect d ON c.pago=d.numero
                WHERE  a.desem=$id
                GROUP BY a.id
                )y ON x.id=y.id
                SET x.observa2 =y.opago");

                $error='';
                $do = new DataObject("desem");
                $do->rel_one_to_many('mbanc', 'mbanc', array('numero'=>'desem'));
                $do ->load($id);

                $banc = new DataObject("banc");

                $b = array();
                for($i=0;$i < $do->count_rel('mbanc'); $i++){
                        $mstatus     = $do->get_rel('mbanc','status'    ,$i  );
                        $codbanc     = $do->get_rel('mbanc','codbanc'   ,$i  );
                        $tipo_doc    = $do->get_rel('mbanc','tipo_doc'  ,$i  );
                        $fecha       = $do->get_rel('mbanc','fecha'     ,$i  );
                        $monto       = $do->get_rel('mbanc','monto'     ,$i  );
                        $cheque      = $do->get_rel('mbanc','cheque'    ,$i  );
                        $mid         = $do->get_rel('mbanc','id'        ,$i  );

                        $this->chexiste_cheque($codbanc,$cheque,$tipo_doc,$mid,$e);
                        $error.=$e;

						$error  .=$this->chbanse($codbanc,$fecha);

                        //print_r($b);
                        if(in_array($codbanc,$b))
                                $b[$codbanc] += $monto;
                        else
                                $b[$codbanc]  = $monto;
                }

                if(empty($error) && ($mstatus=='E1')){
                        foreach($b AS $codbanc=>$monto){
                                $banc->load($codbanc);

                                $saldo  = $banc->get('saldo');
                                $activo = $banc->get('activo');
                                $banco  = $banc->get('banco' );

                                if($activo != 'S' )$error.="<div class='alert'><p>El banco ($banco) esta inactivo</p></div>";

                                if($monto > $saldo )$error.="<div class='alert'><p>La suma de los Montos de los cheques ($monto) es mayor al disponible ($saldo) en el banco ($banco)</p></div>";
                        }
                }

                if(empty($error))$error.=$this->actpresup($id);

                if(empty($error)){
                        for($i=0;$i < $do->count_rel('mbanc');$i++){
                                $mstatus     = $do->get_rel('mbanc','status'    ,$i  );
                                $codbanc     = $do->get_rel('mbanc','codbanc'   ,$i  );
                                $tipo_doc    = $do->get_rel('mbanc','tipo_doc'  ,$i  );
                                $fecha       = $do->get_rel('mbanc','fecha'     ,$i  );
                                $monto       = $do->get_rel('mbanc','monto'     ,$i  );
                                $cheque      = $do->get_rel('mbanc','cheque'    ,$i  );
                                $mid         = $do->get_rel('mbanc','mid'       ,$i  );

                                $do->set_rel('mbanc','status' ,'E2'   ,$i  );

                                $banc->load($codbanc);
                                $saldo  = $banc->get('saldo');
                                $saldo -= $monto;
                                $banc->set('saldo',$saldo);
                                $banc->save();
                        }
                }

                if(empty($error)){
                        $do->set('status','D2');
                        $do->save();

                        logusu('desem',"Actualizo desembolso Nro $id");
                        redirect($this->url."dataedit/show/$id");
                }else{
                        logusu('desem',"Actualizo desembolso Nro $id con error $error");
                        $data['content'] = $error.anchor($this->url."/dataedit/show/$id",'Regresar');
                        $data['title']   = "$this->tits";
                        $data["head"]    = $this->rapyd->get_head();
                        $this->load->view('view_ventanas', $data);
                }
        }

        function anular($id){
                $this->rapyd->load('dataobject');

                $error='';
                        //exit('as');
                $do = new DataObject("desem");
                $do->rel_one_to_many('mbanc', 'mbanc', array('numero'=>'desem'));
                $do ->load($id);
                $status = $do->get('status');

                if($status=='D1'){
                        $do->set('status','DA');
                        $do->save();

                        logusu('desem',"Anulo desembolso Nro $id");
                        redirect($this->url."dataedit/show/$id");
                }

                $cod_prov = $do->get('cod_prov');

                $banc = new DataObject("banc");
                $existecaja  = $this->datasis->traevalor('EXISTECAJA');

                for($i=0;$i < $do->count_rel('mbanc');$i++){
                        $mstatus     = $do->get_rel('mbanc','status'    ,$i  );
                        $codbanc     = $do->get_rel('mbanc','codbanc'   ,$i  );
                        $tipo_doc    = $do->get_rel('mbanc','tipo_doc'  ,$i  );
                        $fecha       = $do->get_rel('mbanc','fecha'     ,$i  );
                        $monto       = $do->get_rel('mbanc','monto'     ,$i  );
                        $cheque      = $do->get_rel('mbanc','cheque'    ,$i  );
                        $mid         = $do->get_rel('mbanc','id'        ,$i  );
                        $concilia    = $do->get_rel('mbanc','concilia'  ,$i  );

                        //if($concilia=='S')$error.=''.($tipo_doc=='CH'?"Cheque:$cheque ya conciliado":($tipo_doc=='ND'?"Nota de Debito:$cheque ya conciliada":'')).". Por favor informe de la accion a realizar a la persona encargada de conciliaciones bancarias";
                        
                        if($existecaja == "S"){
                                $fcajrecibe  =$do->get_rel('mbanc','fcajrecibe',$i);
                                $fcajdevo    =$do->get_rel('mbanc','fcajdevo'  ,$i);

                                if(!empty($fcajrecibe) && empty($fcajdevo))
                                        $error.="ERROR: el cheque ($cheque) del banco ($codbanc) se encuentra en caja";
                        }

                        if(empty($error) && ($mstatus=='E2')){
                                $banc->load($codbanc);
                                $saldo  = $banc->get('saldo' );
                                $activo = $banc->get('activo'  );
                                $banco  = $banc->get('banco'   );

                                if($activo != 'S' )$error.="<div class='alert'><p>El banco ($banco) esta inactivo</p></div>";
                        }
                }

                if(empty($error))$error.=$this->reverpresup($id);

                if(empty($error)){
                        for($i=0;$i < $do->count_rel('mbanc');$i++){
                                $mstatus     = $do->get_rel('mbanc','status'    ,$i  );
                                $codbanc     = $do->get_rel('mbanc','codbanc'   ,$i  );
                                $tipo_doc    = $do->get_rel('mbanc','tipo_doc'  ,$i  );
                                $fecha       = $do->get_rel('mbanc','fecha'     ,$i  );
                                $monto       = $do->get_rel('mbanc','monto'     ,$i  );
                                $cheque      = $do->get_rel('mbanc','cheque'    ,$i  );
                                $mid         = $do->get_rel('mbanc','id'        ,$i  );
                                $benefi      = $do->get_rel('mbanc','benefi'    ,$i  );
                                $relch       = $do->get_rel('mbanc','relch'     ,$i  );

                                if(($tipo_doc=='CH' || $tipo_doc=='ND') && ($mstatus=='E2')){
                                        if($this->datasis->traevalor('CREANCDIA')=='S'){
                                                if(1*date('Ymd') != 1*date('Ymd',strtotime($fecha)) )
                                                $this->creambanc($codbanc,$monto,$cheque,'NC',"Anulacion de cheque $cheque",date('Ymd'),$cod_prov,'NC',$mid,'',$id,$benefi,'N');
                                                $this->db->query("UPDATE mbanc SET status='A2',anulado='".date('Ymd')."',fliable='".date('Ymd')."' WHERE id=$mid");
                                        }elseif(($this->datasis->traevalor('CREANCRELCH')=='S' AND strlen($relch)>0)){
                                                $this->creambanc($codbanc,$monto,$cheque,'NC',"Anulacion de cheque $cheque",date('Ymd'),$cod_prov,'NC',$mid,'',$id,$benefi,'N');
                                                $this->db->query("UPDATE mbanc SET status='A2',anulado='".date('Ymd')."',fliable='".date('Ymd')."' WHERE id=$mid");
                                        }elseif(1*date('m') != 1*date('m',strtotime($fecha)) ){
                                                $this->creambanc($codbanc,$monto,$cheque,'NC',"Anulacion de cheque $cheque",date('Ymd'),$cod_prov,'NC',$mid,'',$id,$benefi,'N');
                                                $this->db->query("UPDATE mbanc SET status='A2',anulado='".date('Ymd')."',fliable='".date('Ymd')."' WHERE id=$mid");
                                        }else{
                                                $this->db->query("UPDATE mbanc SET status='AN',anulado='".date('Ymd')."' WHERE id=$mid");
                                        }


                                        //$do->set_rel('mbanc','status' ,'AN'          ,$i  );
                                        //$do->set_rel('mbanc','anulado',date('Ymd')   ,$i  );

                                        $banc->load($codbanc);
                                        $saldo  = $banc->get('saldo');
                                        $saldo += $monto;
                                        $banc->set('saldo',$saldo);
                                        $banc->save();
                                }
                        }
//exit('hello world');
                }

                if(empty($error)){
                        //$do->set('status','DA');
                        //$do->save();
                        $this->db->simple_query("UPDATE desem SET status='DA' WHERE numero=$id");

                        logusu('desem',"Anulo desembolso Nro $id");
                        redirect($this->url."dataedit/show/$id");
                }else{
                        logusu('desem',"anulo desembolso Nro $id con error $error");
                        $data['content'] = "<div class='alert'>".$error."</div>".anchor($this->url."/dataedit/show/$id",'Regresar');
                        $data['title']   = "$this->tits";
                        $data["head"]    = $this->rapyd->get_head();
                        $this->load->view('view_ventanas', $data);
                }
        }



        function cambcheque($var1,$id){

                $this->datasis->modulo_id(115,1);
                $this->rapyd->load('dataedit2');

                $mBANC=array(
                                'tabla'   =>'banc',
                                'columnas'=>array(
                                        'codbanc' =>'C&oacute;odigo',
                                        'banco'   =>'Banco',
                                        'numcuent'=>'Cuenta',
                                        'saldo'=>'Saldo'),
                                'filtro'  =>array(
                                        'codbanc' =>'C&oacute;odigo',
                                        'banco'=>'Banco',
                                        'numcuent'=>'Cuenta',
                                        'saldo'=>'Saldo'),//39, 40
                                'retornar'=>array(
                                        'codbanc'=>'codbanc','banco'=>'nombreb'
                                         ),
                                'where'=>'activo = "S"',
                                'titulo'  =>'Buscar Bancos');

                $bBANC=$this->datasis->p_modbus($mBANC,"banc");

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
                                'titulo'  =>'Buscar Otros Conceptos');

                $bBCTA=$this->datasis->p_modbus($mBCTA,"bcta");

                $script='
                        $(".inputnum").numeric(".");

                        $(function() {
                                //$("#anulado").change(function(){
                                //      if($("#anulado").attr("checked")==true){
                                //              $("#tr_codbanc").show();
                                //              $("#tr_tipo_doc").show();
                                //              $("#tr_bcta").show();
                                //      }else{
                                //              $("#tr_codbanc").hide();
                                //              $("#tr_tipo_doc").hide();
                                //              $("#tr_bcta").hide();
                                //      }
                                //});
                                $(document).ready(function() {
                                        //if($("#anulado").attr("checked")==true){
                                        //      $("#tr_codbanc").show();
                                        //      $("#tr_tipo_doc").show();
                                        //      $("#tr_bcta").show();
                                        //}else{
                                        //      $("#tr_codbanc").hide();
                                        //      $("#tr_tipo_doc").hide();
                                        //      $("#tr_bcta").hide();
                                        //}
                                });
                        });
                ';

                $do2 = new DataObject("mbanc");
                //$do2->pointer('banc' ,'banc.codbanc=mbanc.codbanc','banc.banco as nombreb,banc.banco as nombrebt');

                $do2->load($id);

                $do = new DataObject("mbanc");
                $do->pointer('banc' ,'banc.codbanc=mbanc.codbanc','banc.banco as nombreb,banc.banco as nombrebt');
                $do->pointer('bcta' ,'bcta.codigo =  mbanc.bcta','bcta.denominacion as bctad ','LEFT'           );

                $edit = new DataEdit2("Cambiar Cheque", $do);

                $edit->back_url = site_url($this->url."filteredgrid/index");

                $edit->script($script,"create");
                $edit->script($script,"modify");

                $edit->pre_process('update'  ,'_validacheque');
                $edit->post_process('update' ,'_postcheque'  );

                $edit->codbanct =  new inputField("Banco", 'codbanct');
                $edit->codbanct->db_name = " ";
                $edit->codbanct-> size     = 5;
                $edit->codbanct->mode    = "autohide";
                $edit->codbanct-> value    = $do2->get('codbanc');
                $edit->codbanct->group   = "Datos Cheque Actual";

                $edit->nombrebt = new inputField("Nombre", 'nombrebt');
                $edit->nombrebt->size      = 50;
                $edit->nombrebt->in        = "codbanct";
                $edit->nombrebt->pointer  = true;
                $edit->nombrebt->mode    = "autohide";
                $edit->nombrebt->group   = "Datos Cheque Actual";

                $edit->tipo_doct = new dropdownField("Tipo Documento","tipo_doct");
                $edit->tipo_doct->option("CH","Cheque"         );
                $edit->tipo_doct->option("ND","Nota de Debito" );
                $edit->tipo_doct->option("DP","Deposito"         );
                $edit->tipo_doct->style   = "width:200px";
                $edit->tipo_doct->mode    = "autohide";
                $edit->tipo_doct->group   = "Datos Cheque Actual";
                $edit->tipo_doct->value   = $do2->get('tipo_doc');
                $edit->tipo_doct->db_name = " ";

                $edit->chequet = new inputField("Cheque Actual Nro.", 'chequet');
                $edit->chequet->db_name = " ";
                $edit->chequet->mode    = "autohide";
                $edit->chequet->value   = $do2->get('cheque');
                $edit->chequet->group   = "Datos Cheque Actual";

                $edit->benefit = new inputField("A nombre de ", 'benefit');
                $edit->benefit->db_name = " ";
                $edit->benefit->size      = 25;
                $edit->benefit->rule      = "required";//|callback_chexiste_cheque
                $edit->benefit->maxlength = 40;
                $edit->benefit->mode    = "autohide";
                $edit->benefit->value   = $do2->get('benefi');
                $edit->benefit->group     = "Datos Cheque Actual";

                $edit->fechat = new  dateonlyField("Fecha Cheque",  "fechat");
                $edit->fechat->db_name = " ";
                $edit->fechat->mode    = "autohide";
                $edit->fechat->value   = $do2->get('fecha');
                $edit->fechat->group   = "Datos Cheque Actual";

                $edit->montot = new inputField("Monto Nro.", 'montot');
                $edit->montot->db_name = " ";
                $edit->montot->mode    = "autohide";
                $edit->montot->value   = $do2->get('monto');
                $edit->montot->group   = "Datos Cheque Actual";

                $edit->cheque = new inputField("Cheque Nuevo Nro.", 'cheque');
                $edit->cheque->size      = 25;
                $edit->cheque->rule      = "required";//|callback_chexiste_cheque
                $edit->cheque->maxlength = 40;
                $edit->cheque->group     = "Datos Cheque Nuevo";

                $edit->benefi = new inputField("A nombre de ", 'benefi');
                $edit->benefi->size      = 25;
                $edit->benefi->rule      = "required";//|callback_chexiste_cheque
                $edit->benefi->maxlength = 40;
                $edit->benefi->group     = "Datos Cheque Nuevo";

                $edit->codbanc =  new inputField("Banco", 'codbanc');
                $edit->codbanc-> size     = 5;
                $edit->codbanc-> rule     = "required";
                $edit->codbanc-> append($bBANC);
                $edit->codbanc->group   = "Datos Cheque Nuevo";
                //$edit->codbanc->mode    = "autohide";

                $edit->tipo_doc = new dropdownField("Tipo Documento","tipo_doc");
                $edit->tipo_doc->option("CH","Cheque"         );
                $edit->tipo_doc->option("ND","Nota de Debito" );
                //$edit->tipo_doc->option("DP","Deposito"         );
                $edit->tipo_doc->style   = "width:220px";
                $edit->tipo_doc->group   = "Datos Cheque Nuevo";
                $edit->tipo_doc->rule     = "required";
                //$edit->tipo_doc->mode    = "autohide";

                $edit->nombreb = new inputField("Nombre", 'nombreb');
                $edit->nombreb->size      = 50;
                $edit->nombreb->in        = "codbanc";
                $edit->nombreb->pointer   = true;
                $edit->nombreb->group     = "Datos Cheque Nuevo";
                $edit->nombreb->rule     = "required";
                //$edit->nombreb->mode    = "autohide";

                $edit->fecha = new  dateonlyField("Fecha Cheque",  "fecha");
                //$edit->fecha->mode    = "autohide";
                $edit->fecha->group   = "Datos Cheque Nuevo";
                $edit->fecha->rule     = "required|chfecha";

                $edit->destino = new dropdownField("Destino","destino");
                $edit->destino->option("C","Caja"    );
                $edit->destino->option("I","Interno" );
                $edit->destino->style  ="width:100px";
                $edit->destino->group  = "Datos Cheque Nuevo";

                $edit->observa = new textAreaField("Observaci&oacute;nes", 'observa');
                //$edit->observa->mode    = "autohide";
                $edit->observa->rows    = 4;
                $edit->observa->cols    = 70;
                $edit->observa->group   = "Datos Cheque Nuevo";
                $edit->observa->rule     = "required";

                $edit->monto = new inputField("Monto", 'monto');
                $edit->monto ->mode ="autohide";
                $edit->monto ->css_class ="inputnum";
                $edit->monto->size       = 15;
                $edit->monto->group      = "Datos Cheque Nuevo";
                $edit->monto->rule     = "required";

                //$edit->anulado = new checkboxField("Cambiar Cheque", "anulado" ,"S");
                //$edit->anulado->value = "S";
                //$edit->anulado->group   = "Datos Cheque Actual";

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

                $edit->buttons("modify","save","undo", "back");
                $edit->build();

                $data['content'] = $edit->output;
                $data['title']   = "Cambiar Cheque";
                $data["head"]    = script("jquery.pack.js").script("plugins/jquery.numeric.pack.js").script("plugins/jquery.floatnumber.js").$this->rapyd->get_head();
                $this->load->view('view_ventanas', $data);
        }

        function _validacheque($do){
                $this->rapyd->load('dataobject');
                $error = '';

                $cod_prov=$do->get('cod_prov');
                $codbanc =$do->get('codbanc' );
                $monto   =$do->get('monto'   );
                $cheque  =$do->get('cheque'  );
                $tipo_doc=$do->get('tipo_doc');
                $observa =$do->get('observa' );
                $id      =$do->get('id'      );
                $fecha   =$do->get('fecha'   );
                $bcta    =$do->get('bcta'    );
                $desem   =$do->get('desem'   );
                $benefi  =$do->get('benefi'  );
                $destino =$do->get('destino' );
                $observa2=$do->get('observa2');

				$error  .=$this->chbanse($codbanc,$fecha);

                $existecaja  = $this->datasis->traevalor('EXISTECAJA');

                $mbanc = new DataObject("mbanc");
                $mbanc->load($id);

                $tcodbanc =$mbanc->get('codbanc' );
                $tmonto   =$mbanc->get('monto'   );
                $tcheque  =$mbanc->get('cheque'  );
                $ttipo_doc=$mbanc->get('tipo_doc');
                $tobserva =$mbanc->get('observa' );
                $tfecha   =$mbanc->get('fecha'   );
                $tbenefi  =$mbanc->get('benefi'  );
                $tdestino =$mbanc->get('destino' );
                $trelch   =$mbanc->get('relch'   );
                $tobserva2=$mbanc->get('observa2');

                $desem    =$mbanc->get('desem'   );
                $mbanc->set('desem',$desem);

                if($existecaja == "S"){
                        $fcajrecibe  =$do->get('fcajrecibe');
                        $fcajdevo    =$do->get('fcajdevo'  );
                        if(!empty($fcajrecibe) && empty($fcajdevo))
                        $error.="ERROR: El cheque ($tcheque) del banco ($tcodbanc) se encuentra en caja. Caja tiene el cheque fisicamente, el cheque debe ser devuelto de caja al emisor de cheques";
                }

                $do->set('fecha',$tfecha);

                if(empty($error)){
                        if(true){

                                if(($this->datasis->traevalor('CREANCDIA')=='S' AND (1*date('Ymd') != 1*date('Ymd',strtotime($fecha)))) || date('m',strtotime($fecha)) != date('m',strtotime($tfecha)) || ($this->datasis->traevalor('CREANCRELCH')=='S' AND strlen($trelch)>0)){

                                        if($codbanc != $tcodbanc){
                                                $this->chexiste_cheque($codbanc,$cheque,$tipo_doc,$id,$e);
                                                $error.=$e;

                                                $banc   = new DataObject("banc");
                                                $banc   ->load($codbanc        );
                                                $saldo  = $banc->get('saldo'   );
                                                $activo = $banc->get('activo'  );
                                                $banco  = $banc->get('banco'   );

                                                if($activo != 'S' )
                                                        $error.="<div class='alert'><p>El banco ($banco) esta inactivo</p></div>";

                                                if($monto > $saldo )
                                                        $error.="<div class='alert'><p>El Monto ($monto) del cheque es mayor al disponible ($saldo) en el banco ($banco)</p></div>";

                                                if(empty($error)){

                                                        $banc->set('saldo',$saldo-$monto);
                                                        $banc->save();

                                                        $banc   ->load($tcodbanc);
                                                        $saldo  = $banc->get('saldo');
                                                        $banc->set('saldo',$saldo+$monto);
                                                        $banc->save();

                                                        if(empty($error)){

                                                                $do->set('tipo_doc',$ttipo_doc);
                                                                $do->set('cheque'  ,$tcheque  );
                                                                $do->set('codbanc' ,$tcodbanc );
                                                                $do->set('benefi'  ,$tbenefi  );
                                                                $do->set('destino' ,$tdestino );
                                                                $do->set('observa' ,$tobserva );
                                                                $do->set('observa2',$tobserva2);
                                                                $do->set('fliable' ,date('Y-m-d'));
                                                                $do->set('status','A2');

                                                                $this->creambanc($codbanc,$monto,$cheque,$tipo_doc,$observa,$fecha,$cod_prov,'E2',$id,$bcta                                          ,$desem,$benefi,'S',NULL,$destino,$observa2);

                                                                $this->creambanc($tcodbanc,$monto,'NC'.$tcheque,'NC',"1-Creada para respaldar cambio de cheque $tcheque",$fecha,$cod_prov,'NC',$id,'',$desem,$tbenefi,'N',NULL,$destino,$observa2);
                                                        }
                                                }
                                        }else{

                                                $this->chexiste_cheque($codbanc,$cheque,$tipo_doc,$id,$e);
                                                $error.=$e;

                                                if(empty($error)){
                                                //exit('7');
                                                        $do->set('tipo_doc',$ttipo_doc);
                                                        $do->set('cheque'  ,$tcheque  );
                                                        $do->set('codbanc' ,$tcodbanc );
                                                        $do->set('benefi'  ,$tbenefi  );
                                                        $do->set('destino' ,$tdestino );
                                                        $do->set('observa' ,$tobserva );
                                                        $do->set('observa2',$tobserva2);
                                                        $do->set('status','A2');

                                                        $this->creambanc($codbanc,$monto,$cheque,$tipo_doc,$observa,$fecha,$cod_prov,'E2',$id,'',$desem,$benefi,'S',NULL,$destino,$observa2);

                                                        $this->creambanc($codbanc,$monto,$tcheque,'NC',"Anulacion de cheque $tcheque",$fecha,$cod_prov,'NC',$id,'',$desem,$tbenefi,'N',NULL,$tdestino,$observa2);
                                                }
                                                //exit("a".$error);
                                        }
                                }else{
                                        if($codbanc != $tcodbanc){
                                                $this->chexiste_cheque($codbanc,$cheque,$tipo_doc,$id,$e);
                                                $error.=$e;

                                                $banc   = new DataObject("banc");
                                                $banc   ->load($codbanc);
                                                $saldo  = $banc->get('saldo');
                                                $activo = $banc->get('activo');
                                                $banco  = $banc->get('banco');

                                                if($activo != 'S' )
                                                        $error.="<div class='alert'><p>El banco ($banco) esta inactivo</p></div>";

                                                if($monto > $saldo )
                                                        $error.="<div class='alert'><p>El Monto ($monto) del cheque es mayor al disponible ($saldo) en el banco ($banco)</p></div>";

                                                if(empty($error)){
                                                //exit('10');
                                                        $banc->set('saldo',$saldo-$monto);
                                                        $banc->save();

                                                        $banc   ->load($tcodbanc);
                                                        $saldo  = $banc->get('saldo');
                                                        $banc->set('saldo',$saldo+$monto);
                                                        $banc->save();

                                                        if(empty($error)){
                                                        //exit('11');
                                                                $do->set('tipo_doc',$ttipo_doc);
                                                                $do->set('cheque'  ,$tcheque  );
                                                                $do->set('codbanc' ,$tcodbanc );
                                                                $do->set('benefi'  ,$tbenefi  );
                                                                $do->set('observa' ,$tobserva );
                                                                $do->set('observa2',$tobserva2);
                                                                $do->set('status','AN');

                                                                $this->creambanc($codbanc,$monto,$cheque,$tipo_doc,$observa,$fecha,$cod_prov,'E2',$id,'',$desem,$benefi,'S',NULL,$destino,$observa2);
                                                        }
                                                }
                                        }else{
                                                $this->chexiste_cheque($codbanc,$cheque,$tipo_doc,$id,$e);
                                                $error.=$e;

                                                if(empty($error)){
                                                print_r($do->get_all());

//                                                exit('21');

                                                        $do->set('tipo_doc',$ttipo_doc);
                                                        $do->set('cheque'  ,$tcheque );
                                                        $do->set('codbanc' ,$tcodbanc);
                                                        $do->set('benefi'  ,$tbenefi);
                                                        $do->set('observa' ,$tobserva);
                                                        $do->set('observa2',$tobserva2);
                                                        $do->set('status'  ,'AN');
                                                }

                                                $this->creambanc($codbanc,$monto,$cheque,$tipo_doc,$observa,$fecha,$cod_prov,'E2',$id,'',$desem,$benefi,'S',NULL,$destino,$observa2);
                                        }
                                }
                        }else{

                                $this->chexiste_cheque($tcodbanc,$tcheque,$ttipo_doc,$id,$e);
                                $error.=$e;
                        }
                }

                if(empty($error)){
                        logusu('desem',"cambio datos cheque/banco $ttipo_doc Nro $tcheque por $tipo_doc Nro $cheque movimento $id");
                }else{
                        $do->error_message_ar['pre_ins']=$error;
                        $do->error_message_ar['pre_upd']=$error;
                        logusu('desem',"cambio datos cheque/banco $ttipo_doc Nro $tcheque por $tipo_doc Nro $cheque movimento $id con error $error");
                        return false;
                }
        }

        function _postcheque($do){
                $desem = $do->get('desem');
                redirect($this->url."dataedit/show/$desem");
        }

        function riva_an($ocompra='',$odirect,$itfac=''){
                $query="UPDATE riva SET tipo_doc='AN',status='AN' WHERE odirect=$odirect AND status='B' ";
                if(!empty($ocompra))
                        $query.=" AND ocompra='$ocompra'";
                elseif(!empty($itfac))
                        $query.=" AND itfac=$itfac";

                $this->db->query($query);
        }

        function chriva_an($ocompra='',$odirect,$itfac=''){
                $query="SELECT status,LPAD(nrocomp,8,'0') nrocomp,REPLACE(emision,'-','') emision FROM riva WHERE odirect=$odirect ";
                if(!empty($ocompra))
                        $query.=" AND ocompra='$ocompra'";
                elseif(!empty($itfac))
                        $query.=" AND itfac=$itfac";

                $error  = '';
                $row    = $this->datasis->damerow($query);
                $status = $row['status' ];
                $nrocomp= $row['nrocomp'];
                $emision= $row['emision'];

                if($status == 'B'){
                        return '';
                }elseif($status == "C" ){
                        return $error.="<div class='alert'><p>La Retenci&oacute;n (".date('Ym',strtotime($emision)).$nrocomp." ) ya fue declarada</p></div>";
                }elseif($status == "AN"){
                        return $error.="<div class='alert'><p>La Retenci&oacute;n (".date('Ym',strtotime($emision)).$nrocomp." ) ya fue anulada</p></div>";
                }
        }

        function cheque($pago){
                $id=$this->datasis->dameval("SELECT id FROM mbanc a JOIN pambanc b ON a.id=b.mbanc WHERE b.pago=".$pago." AND a.status='E2'");
                redirect("formatos/ver/CHEQUE2/$id");
        }

        function rivaa($pago){

            $cant = $this->datasis->dameval("SELECT COUNT(*) FROM riva WHERE odirect=$pago AND status='B'");
            if($cant==1){
                    $nrocomp = $this->datasis->dameval("SELECT nrocomp FROM riva WHERE odirect=$pago AND status='B'");
                    redirect("formatos/ver/RIVA/$nrocomp");
            }elseif($cant>1){
                    redirect("formatos/ver/RIVAM/$pago");
            }else{
                    $data['content'] = "No Existen retenciones de IVA para la Orden de Pago ($pago)";
                    $data['title']   = "  ";
                    $data["head"]    = $this->rapyd->get_head();
                    $this->load->view('view_ventanas', $data);
            }
        }

        function ultimoch(){
                $codbanc = $this->db->escape($this->input->post('codbanc'));
                $cheque = $this->datasis->dameval("SELECT cheque FROM mbanc WHERE codbanc=$codbanc ORDER BY cheque DESC LIMIT 1");
                $cheque = (1*$cheque)+1;
                echo (1+$cheque);
        }

		 function autocompleteopagopp(){
			$term      =$this->input->post('term');
			$cod_prov  =$this->input->post('cod_prov');
			$cod_prove =$this->db->escape($cod_prov);
			$terme     =$this->db->escape('%'.$term.'%');
			$termed    =$this->db->escape($term.'%');

			$where='';
			if(strlen($cod_prov)>0)
			$where  .=" AND cod_prov=$cod_prove ";

			$query  ="SELECT odirect.*, odirect.nombre label
					FROM odirect
					WHERE numero LIKE $terme AND MID(status,2,1)=2 $where
					ORDER BY numero NOT LIKE $termed ";

			$mSQL   = $this->db->query($query);
			$arreglo= $mSQL->result_array($query);

			foreach($arreglo as $key=>$value)
				foreach($value as $key2=>$value2)
					$arreglo[$key][$key2] = ($value2);

			header ('Content-type: text/html; charset=utf-8');
			echo json_encode($arreglo);
		}
		
		function op_desde_hasta(){
			$desde    = $this->input->post('desde'   );
			$hasta    = $this->input->post('hasta'   );
			$cod_prov = $this->input->post('cod_prov');
			
			$select=array('numero','imptimbre','impmunicipal','crs','total','total2','reten','reteiva','otrasrete','cod_prov','observa'); 
			$this->db->select($select);
			$this->db->from('odirect');
			$this->db->where('MID(status,2,1)','2');
			if($desde>0)
			$this->db->where("numero >= $desde");
			if($hasta>0)
			$this->db->where("numero <= $hasta");
			if($cod_prov)
			$this->db->where('cod_prov =',$cod_prov);			
			$this->db->order_by('numero');
			
			$query = $this->db->get();
			$data  =$query->result_array();
			
			header ('Content-type: text/html; charset=utf-8');
			echo json_encode($data);
		
		}

        function _post_insert($do){
                $tipo_doc   = $do->get('tipo_doc');
                $cheque     = $do->get('cheque');
                $id         = $do->get('id');
                logusu('desem',"Creo $tipo_doc Nro $cheque movimento $id");
                //redirect($this->url."actualizar/$id");
        }
        function _post_update($do){
                $tipo_doc   = $do->get('tipo_doc');
                $cheque     = $do->get('cheque');
                $id         = $do->get('id');
                logusu('desem',"modifico $tipo_doc Nro $cheque movimento $id");
                //redirect($this->url."actualizar/$id");
        }
        function _post_delete($do){
                $tipo_doc   = $do->get('tipo_doc');
                $cheque     = $do->get('cheque');
                $id         = $do->get('id');
                logusu('desem',"modifico $tipo_doc Nro $cheque movimento $id");
        }

        function instalar(){
                $query="ALTER TABLE `pades` CHANGE COLUMN `pago` `pago` VARCHAR(12) NOT NULL  ";
                $this->db->simple_query($query);
                $query="ALTER TABLE `riva`  CHANGE COLUMN `nrocomp` `nrocomp` VARCHAR(8) NOT NULL FIRST";
                $this->db->simple_query($query);
                $query="ALTER TABLE `riva`  DROP PRIMARY KEY";
                $this->db->simple_query($query);
                $query="ALTER TABLE `riva`  ADD COLUMN `id` INT(11) NOT NULL AUTO_INCREMENT AFTER `nrocomp`,  ADD PRIMARY KEY (`id`)";
                $this->db->simple_query($query);

                //repara tablas
                $query="ALTER TABLE `anoprox`  COLLATE='utf8_general_ci',  CHANGE COLUMN `numero` `numero` VARCHAR(9) NOT NULL DEFAULT '' FIRST,  CHANGE COLUMN `uejecuta` `uejecuta` CHAR(4) NULL DEFAULT NULL AFTER `fecha`,  CHANGE COLUMN `uadministra` `uadministra` CHAR(4) NULL DEFAULT NULL AFTER `uejecuta`,  CHANGE COLUMN `concepto` `concepto` TINYTEXT NULL AFTER `uadministra`,  CHANGE COLUMN `responsable` `responsable` VARCHAR(250) NULL DEFAULT NULL AFTER `concepto`,  CHANGE COLUMN `status` `status` CHAR(2) NULL DEFAULT NULL AFTER `responsable`,  CHANGE COLUMN `usuario` `usuario` VARCHAR(12) NULL DEFAULT NULL COMMENT 'aa' AFTER `status`";
                $this->db->simple_query($query);

                $query="ALTER TABLE `asignomi`  COLLATE='utf8_general_ci',  CHANGE COLUMN `codigoadm` `codigoadm` VARCHAR(15) NULL DEFAULT NULL AFTER `numero`,  CHANGE COLUMN `fondo` `fondo` VARCHAR(20) NULL DEFAULT NULL AFTER `codigoadm`,  CHANGE COLUMN `codigopres` `codigopres` VARCHAR(17) NULL DEFAULT NULL AFTER `fondo`,  CHANGE COLUMN `ordinal` `ordinal` CHAR(3) NULL DEFAULT NULL AFTER `codigopres`";
                $this->db->simple_query($query);
                $query="ALTER TABLE `bcta`  COLLATE='utf8_general_ci',  CHANGE COLUMN `denominacion` `denominacion` VARCHAR(200) NULL DEFAULT NULL AFTER `codigo`;";
                $this->db->simple_query($query);
                $query="ALTER TABLE `desem` ADD COLUMN `tcrs` DECIMAL(19,2) NULL AFTER `id`            ";
                $this->db->simple_query($query);
                $query="ALTER TABLE `desem` ADD COLUMN `ttimbre` DECIMAL(19,2) NULL AFTER `tcrs`       ";
                $this->db->simple_query($query);
                $query="ALTER TABLE `desem` ADD COLUMN `tmunicipal` DECIMAL(19,2) NULL AFTER `ttimbre` ";
                $this->db->simple_query($query);
                $query="ALTER TABLE `desem` ADD COLUMN `tislr` DECIMAL(19,2) NULL AFTER `tmunicipal`   ";
                $this->db->simple_query($query);
                $query="ALTER TABLE `desem` ADD COLUMN `triva` DECIMAL(19,2) NULL AFTER `tislr`         ";
                $this->db->simple_query($query);
                $query="ALTER TABLE `desem`  ADD COLUMN `total2` DECIMAL(19,2) NULL";
                $this->db->simple_query($query);
                $query="ALTER TABLE `mbanc`  ADD INDEX `desem` (`desem`)";
                $this->db->simple_query($query);
                $query="ALTER TABLE `desem`  ADD COLUMN `otrasrete` DECIMAL(19,2) NULL DEFAULT 0";
                $this->db->simple_query($query);
                $query="ALTER TABLE `mbanc`  ADD COLUMN `cheque2` VARCHAR(50) NULL DEFAULT NULL";
                $this->db->simple_query($query);
                $query="ALTER TABLE `riva` CHANGE COLUMN `ffactura` `ffactura` DATE NULL DEFAULT '0000-00-00' AFTER `reteiva_prov`";
                $this->db->simple_query($query);
                
        }
}
