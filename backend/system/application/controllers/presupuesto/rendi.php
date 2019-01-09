<?php
require_once(BASEPATH.'application/controllers/presupuesto/common.php');
class Rendi extends Common {

        var $titp  = 'Rendici&oacute;n de Cuentas';
        var $tits  = 'Rendici&oacute;n de Cuenta';
        var $url   = 'presupuesto/rendi/';

        function rendi(){
                parent::Controller();
                $this->load->library("rapyd");
                $this->formatopres =$this->datasis->traevalor('FORMATOPRES');
                $this->flongpres   =strlen(trim($this->formatopres));
        }

        function index(){
                redirect($this->url."filteredgrid");
        }

        function filteredgrid(){
                $this->datasis->modulo_id(23,1);
                $this->rapyd->load("datafilter","datagrid");

                $mSPRV=array(
                                'tabla'   =>'sprv',
                                'columnas'=>array(
                                'proveed' =>'C&oacute;odigo',
                                'nombre'=>'Nombre',
                                'contacto'=>'Contacto'),
                                'filtro'  =>array('proveed'=>'C&oacute;digo','nombre'=>'Nombre'),
                                'retornar'=>array('proveed'=>'cod_prov' ),
                                'titulo'  =>'Buscar Beneficiario');

                $bSPRV=$this->datasis->p_modbus($mSPRV,"proveed");

                $filter = new DataFilter("");
                $filter->db->select(array("a.status status","a.anticipo anticipo","a.fecha fecha","a.numero numero","a.total total","cod_prov"));
                $filter->db->from("rendi a");
                $filter->db->where("status ='C' OR status='P' OR status='A'");

                $filter->anticipo = new inputField("Anticipo", "anticipo");
                $filter->anticipo->size = 12;
                
                $filter->fecha = new dateonlyField("Fecha", "fecha");
                $filter->fecha->size=12;
                $filter->fecha->dbformat = "Y-m-d";

                $filter->buttons("reset","search");

                $filter->build();
                $uri = anchor($this->url.'dataedit/show/<#numero#>','<str_pad><#numero#>|8|0|STR_PAD_LEFT</str_pad>');

		function sta($status){
			switch($status){
				case 'P':return 'Pendiente';
				case 'C':return 'Terminado';
			}
		}

                $grid = new DataGrid("");
                $grid->order_by("numero","desc");
                $grid->per_page = 20;
                $grid->use_function('substr','str_pad','sta');

                $grid->column_orderby("N&uacute;mero"    ,$uri                                               ,"numero");
                $grid->column_orderby("Proveedor"        ,"cod_prov"                                        ,"cod_prov"      ,"align='center'");
                $grid->column_orderby("Fecha"            ,"<dbdate_to_human><#fecha#></dbdate_to_human>"     ,"fecha"        ,"align='center'");
                $grid->column_orderby("Pago"             ,"<number_format><#total#>|2|,|.</number_format>"   ,"total"        ,"align='right'");
                $grid->column_orderby("Estado"           ,"<sta><#status#></sta>"                            ,"status"       ,"align='center'");
                //$grid->column("Devoluci&oacute;n","<number_format><#devo#>|2|,|.</number_format>","align='rigth'");
                //echo $grid->db->last_query();
                $grid->add($this->url."dataedit/create");
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
                $this->rapyd->load('dataobject','datadetails');

                $mSPRV=array(
                        'tabla'   =>'sprv',
                        'columnas'=>array(
                        'proveed' =>'C&oacute;odigo',
                        'nombre'=>'Nombre',
                        'rif'=>'Rif',
                        'contacto'=>'Contacto'),
                        'filtro'  =>array('proveed'=>'C&oacute;digo','nombre'=>'Nombre','rif'=>'Rif'),
                        'retornar'=>array('proveed'=>'cod_prov','nombre'=>'nombrep'),
                        'titulo'  =>'Buscar Beneficiario');
                
                $bSPRV =$this->datasis->p_modbus($mSPRV ,"sprv");

                $modbus=array(
                        'tabla'   =>'v_presaldo',
                        'columnas'=>array(
                                'codigoadm'  =>'Est Admin',
                                'fondo'      =>'Fondo',
                                'codigo'     =>'C&oacute;digo',
                                'ordinal'    =>'Ordinal',
                                'denominacion'=>'Denominaci&oacute;n',
                                'saldo'       =>'Saldo'
                                ),
                        'filtro'  =>array(
                                'codigoadm'  =>'Est Admin',
                                'fondo'      =>'Fondo',
                                'codigo'      =>'C&oacute;digo',
                                ),
                        'retornar'=>array(
                                'codigo'    =>'codigopres_<#i#>',
                                'fondo'    =>'fondo_<#i#>',
                                'codigoadm' =>'codigoadm_<#i#>',
                                'ordinal'   =>'ordinal_<#i#>'),
                        'p_uri'=>array(4=>'<#i#>',),
                        'where'=>'movimiento = "S" AND saldo > 0 ',
                        'titulo'  =>'Busqueda de partidas');
                                
                $mod=$this->datasis->p_modbus($modbus,'<#i#>');

                $do = new DataObject("rendi");
                $do->pointer('sprv' ,'sprv.proveed=rendi.cod_prov','sprv.nombre as nombrep');
                $do->rel_one_to_many('itrendi', 'itrendi', array('numero'=>'numero'));

                $edit = new DataDetails($this->tits, $do);
                $edit->back_url = site_url($this->url."filteredgrid");
                $edit->set_rel_title('itrendi','Rubro <#o#>');

                $edit->pre_process('insert'  ,'_valida');
                $edit->pre_process('update'  ,'_valida');
                //$edit->post_process('insert'  ,'_paiva');
                //$edit->post_process('update'  ,'_paiva');
                $edit->post_process('insert','_post_insert');
                $edit->post_process('update','_post_update');

                $edit->numero  = new inputField("N&uacute;mero", "numero");
                $edit->numero->mode="autohide";
                $edit->numero->when=array('show');

                $edit->cod_prov = new inputField("Beneficiario", 'cod_prov');
                $edit->cod_prov->db_name  = "cod_prov";
                $edit->cod_prov->size     = 4;         
                $edit->cod_prov->rule     = "required";
                $edit->cod_prov->readonly =true;       
                $edit->cod_prov->append($bSPRV);       
                
                $edit->nombrep = new inputField("Nombre", 'nombrep');
                $edit->nombrep->size     = 20;
                $edit->nombrep->readonly = true;
                $edit->nombrep->pointer  = TRUE;
                $edit->nombrep->in       = "codprov";
                
                $edit->fecha = new  dateonlyField("Fecha",  "fecha");
                $edit->fecha->insertValue = date('Y-m-d');
                $edit->fecha->size =12;

                $edit->reteiva_prov  = new inputField("reteiva_prov", "reteiva_prov");
                $edit->reteiva_prov->size=1;
                //$edit->reteiva_prov->mode="autohide";
                $edit->reteiva_prov->when=array('modify','create');

                $edit->creten = new dropdownField("Codigo ISLR","creten");
                $edit->creten->option("","");
                $edit->creten->options("SELECT codigo,CONCAT_WS(' ',codigo,activida) FROM rete ORDER BY codigo");
                $edit->creten->style="width:300px;";
                $edit->creten->onchange ='cal_islr();';

                $edit->observa = new textAreaField("Observaciones", 'observa');
                $edit->observa->cols = 106;
                $edit->observa->rows = 3;

                $edit->subtotal = new inputField("Sub Total", 'subtotal');
                $edit->subtotal->size = 8;
                $edit->subtotal->readonly=true;
                $edit->subtotal->css_class='inputnum';

                $edit->iva = new inputField("IVA", 'iva');
                $edit->iva->css_class='inputnum';;
                $edit->iva->size = 8;
                $edit->iva->readonly=true;

                $edit->reten = new inputField("Retencion de ISLR", 'reten');
                $edit->reten->css_class='inputnum';
                $edit->reten->size = 8;

                $edit->total = new inputField("Total", 'total');
                $edit->total->readonly=true;
                $edit->total->size = 8;
                $edit->total->rule     ='numeric';
                $edit->total->css_class='inputnum';
                
                $edit->itesiva = new dropdownField("P.IVA","itesiva_<#i#>");
                $edit->itesiva->rule   ='required';
                $edit->itesiva->db_name='esiva';
                $edit->itesiva->rel_id ='itrendi';
                $edit->itesiva->option("N","No");
                $edit->itesiva->option("S","Si");
                $edit->itesiva->option("A","Auto");
                $edit->itesiva->style="width:45px;";
                
                $edit->itcodigoadm = new inputField("(<#o#>) Partida", "codigoadm_<#i#>");              
                $edit->itcodigoadm->size=10;            
                $edit->itcodigoadm->db_name='codigoadm';
                $edit->itcodigoadm->rel_id ='itrendi';
                $edit->itcodigoadm->append($mod);

                $edit->itfondo = new inputField("(<#o#>) Descripci&oacute;n", "fondo_<#i#>");
                $edit->itfondo->db_name  ='fondo';
                $edit->itfondo->maxlength= 80;
                $edit->itfondo->size     = 8;
                $edit->itfondo->rule     = 'required';
                $edit->itfondo->rel_id   ='itrendi';
                
                $edit->itcodigopres = new inputField("(<#o#>) Partida", "codigopres_<#i#>");
                $edit->itcodigopres->rule='required';
                $edit->itcodigopres->size=10;           
                $edit->itcodigopres->db_name='codigopres';
                $edit->itcodigopres->rel_id ='itrendi';
                
                $edit->itordinal = new inputField("(<#o#>) Partida", "ordinal_<#i#>");
                
                $edit->itordinal->size=10;              
                $edit->itordinal->db_name='ordinal';
                $edit->itordinal->rel_id ='itrendi';
                
                $edit->itdescripcion = new inputField("(<#o#>) Factura", "descripcion_<#i#>");
                $edit->itdescripcion->db_name  ='descripcion';
                $edit->itdescripcion->size     = 20;
                //$edit->itdescripcion->rule     = 'required';
                $edit->itdescripcion->rel_id   ='itrendi';
                                
                $edit->itfactura = new inputField("(<#o#>) Factura", "factura_<#i#>");
                $edit->itfactura->db_name  ='factura';
                $edit->itfactura->size     =8;
                $edit->itfactura->rel_id   ='itrendi';
                
                $edit->itcontrolfac = new inputField("(<#o#>) Factura", "controlfac_<#i#>");
                $edit->itcontrolfac->db_name  ='controlfac';
                $edit->itcontrolfac->size     =8;
                $edit->itcontrolfac->rel_id   ='itrendi';

                $edit->itfechafac = new dateonlyField("(<#o#>) Fecha", "fechafac_<#i#>");
                $edit->itfechafac->db_name  = 'fechafac';
                $edit->itfechafac->size     = 8;
                //$edit->itfechafac->rule     = 'required';
                $edit->itfechafac->rel_id   = 'itrendi';
                //$edit->itfechafac->append($bSPRV);

                $edit->itsubtotal = new inputField("(<#o#>) Total", "subtotal_<#i#>");
                $edit->itsubtotal->css_class='inputnum';
                $edit->itsubtotal->db_name  ='subtotal';
                $edit->itsubtotal->rel_id   ='itrendi';
                $edit->itsubtotal->rule     ='numeric|callback_positivo';
                $edit->itsubtotal->size     =8;
                $edit->itsubtotal->onchange ='cal_total();';
                $edit->itsubtotal->insertValue=0;
                
                $edit->itiva = new inputField("(<#o#>) Total", "iva_<#i#>");
                $edit->itiva->css_class='inputnum';
                $edit->itiva->db_name  ='iva';
                $edit->itiva->rel_id   ='itrendi';
                $edit->itiva->rule     ='numeric';
                $edit->itiva->size     =8;
                $edit->itiva->onchange ='cal_total();';
                $edit->itiva->insertValue=0;
                
                $edit->ittotal = new inputField("(<#o#>) Total", "total_<#i#>");
                $edit->ittotal->readonly=true;
                $edit->ittotal->db_name  ='total';
                $edit->ittotal->rel_id   ='itrendi';
                $edit->ittotal->size     =8;
                $edit->ittotal->rule     ='numeric';
                $edit->ittotal->insertValue=0;

                $status=$edit->get_from_dataobjetct('status');
                if($status=='P'){
                        $action = "javascript:window.location='" .site_url($this->url.'actualizar/'.$edit->rapyd->uri->get_edited_id()). "'";
                        $edit->button_status("btn_status",'Actualizar',$action,"TR","show");
                        $edit->buttons("modify","delete","save");
                }elseif($status=='C'){
                        $action = "javascript:btn_anulaf('".$edit->rapyd->uri->get_edited_id()."')";
                        $edit->button_status("btn_rever",'Anular',$action,"TR","show");
                        
                        $action = "javascript:window.location='" .site_url($this->url.'creaanti/'.$edit->rapyd->uri->get_edited_id()). "'";
                        $edit->button_status("btn_creaanti",'Crear Pago de Reintegro de Caja Chica',$action,"TR","show");
                        
                        $action = "javascript:window.location='" .site_url($this->url.'creariva/'.$edit->rapyd->uri->get_edited_id()). "'";
                        $edit->button_status("btn_creariva",'Crear Retenciones de IVA',$action,"TR","show");
                }else{
                        $edit->buttons("save");
                }

                $edit->buttons("undo","back","add_rel");
                $edit->build();

                $smenu['link']=barra_menu('101');
                $data['smenu'] = $this->load->view('view_sub_menu', $smenu,true);
                $conten["form"]  =&  $edit;
                $data['content'] = $this->load->view('view_rendi', $conten,true);
                //$data['content'] = $edit->output;
                $data['title']   = "$this->tits";
                $data["head"]    = $this->rapyd->get_head().script('jquery.js').script("plugins/jquery.numeric.pack.js").script("plugins/jquery.json.min.js");
                $this->load->view('view_ventanas', $data);
        }

        function actualizar($id){

                $this->rapyd->load('dataobject');

                $error='';

                $do = new DataObject("rendi");
                $do->rel_one_to_many('itrendi', 'itrendi', array('numero'=>'numero'));
                $do->load($id);

                $anticipo    = $do->get('anticipo');
                $ttotal      = $do->get('total'   );
                $cod_prov    = $do->get('cod_prov');
                
                $SPRV= new DataObject("sprv");
                $SPRV->load($cod_prov);

                $anti   = $SPRV->get('anti'  );
                $maximo = $SPRV->get('maximo');
                $demos  = $SPRV->get('demos' );
                $nombre = $SPRV->get('nombre');
                $saldo  = ($anti - $demos    );
                
                if(($ttotal)>($saldo))
                        $error.="<div class='alert'><p>El monto del $this->tits ($ttotal) es mayor que el monto adeudado ($saldo) del proveedor $nombre</p></div>";
        
                $tiva =0;
                $tmont=0;       

                $sta=$do->get('status');
                if(empty($error)){
                        if($sta=="P"){
                                $tiva=$tsub=$ttot=0;
                                $ivan=0;$importes=array(); $ivas=array();
                                for($i=0;$i < $do->count_rel('itrendi');$i++){
                                        $codigopres  = $do->get_rel('itrendi','codigopres',$i);                         
                                        $codigoadm   = $do->get_rel('itrendi','codigoadm' ,$i);
                                        $fondo       = $do->get_rel('itrendi','fondo'     ,$i);
                                        $importe = $subtotal  = $do->get_rel('itrendi','subtotal'  ,$i);
                                        $iva         = $do->get_rel('itrendi','iva'       ,$i);
                                        $factura     = $do->get_rel('itrendi','factura'   ,$i);
                                        $ivan         += $iva;
                                        
                                        //$error.= $this->chequeapresup($codigoadm,$fondo,$codigopres,'',$subtotal,0,'round($monto2) > $disponible=round(($presupuesto-($comprometido+$apartado)),2)','El Monto ($monto) es mayor al disponible ($disponible) para la partida ('.$codigoadm.') ('.$fondo.') ('.$codigopres.') ') ;
//                                        $error.=$this->chequeapresup($codigoadm,$fondo,$codigopres,null,$subtotal,0,'round($monto,2) > round(($presupuesto-$comprometido),2)',"El Monto ($subtotal) es mayor al disponible para la partida ($codigoadm) ($fondo) ($codigopres)");//
                                        $cadena = $codigoadm.'_._'.$fondo.'_._'.$codigopres;
                                        if(array_key_exists($cadena,$importes)){
                                                $importes[$cadena]+=$importe;
                                        }else{
                                                $importes[$cadena]  =$importe;
                                        }
                                }
                                
                                //print_r($importes);
                                foreach($importes AS $cadena=>$monto){
                                        $temp  = explode('_._',$cadena);
                                        $error.=$this->chequeapresup($temp[0],$temp[1],$temp[2],'',$monto,0,'round($monto,2) > $disponible=round(($presupuesto-$comprometido),2)','El Monto ($monto) es mayor al disponible ($disponible) del presupuesto ($presupuesto),asignacion ($asignacion),disminucion($disminucion),traslados ($traslados) para la partida ('.$temp[0].' ('.$temp[1].') ('.$temp[2].') ');
                                }
                                
                                

                                //exit($error);
                                
                                //if(empty($error))
                                //      $error.=$this->chequeapresup($codigoadm,$fondo,'PARTIDAIVA','',$ivan,0,'round($monto,2) > round(($presupuesto-$comprometido),2)','El Monto ($monto) es mayor al disponible para la partida de IVA');

                                if(empty($error)){
                                        //$tiva=$tsub=$ttot=0;$ivan=0;
                                        //for($i=0;$i < $do->count_rel('itrendi');$i++){
                                        //
                                        //      $codigopres    = $do->get_rel('itrendi','codigopres',$i);                               
                                        //      $codigoadm     = $do->get_rel('itrendi','codigoadm' ,$i);
                                        //      $fondo         = $do->get_rel('itrendi','fondo'     ,$i);
                                        //      $ordinal       = $do->get_rel('itrendi','ordinal'   ,$i);
                                        //      $subtotal      = $do->get_rel('itrendi','subtotal'  ,$i);
                                        //      $iva           = $do->get_rel('itrendi','iva'       ,$i);
                                        //      $ttot=$total   = $do->get_rel('itrendi','total'     ,$i);
                                        //      $factura       = $do->get_rel('itrendi','factura'   ,$i);
                                        //      $ivan         += $iva;
                                        //      
                                        //      $error.=$this->afectapresup($codigoadm,$fondo,$codigopres,$ordinal,$subtotal,0, 1 ,array("comprometido","causado","opago","pagado"));
                                        //}
                                        
                                        foreach($importes AS $cadena=>$monto){
                                                $temp  = explode('_._',$cadena);
                                                $error.=$this->afectapresup($temp[0],$temp[1],$temp[2],null,$monto,0, 1 ,array("comprometido","causado","opago","pagado"));
                                        }
                                
                                        //if(empty($error))
                                        //      $error.=$this->afectapresup($codigoadm,$fondo,'PARTIDAIVA','',$ivan,0, 1 ,array("comprometido","causado","opago","pagado"));
                                        
                                        if(empty($error)){
                                                $SPRV ->set('demos',$demos+$ttotal);
                                                $SPRV ->save();
                                                $do->set('status' ,'C'        );
                                                $do->set('frendi' ,date('Ymd'));
                                                $do->save();
                                        }
                                }
                        }
                }

                if(empty($error)){
                        redirect($this->url."/dataedit/show/$id");
                        logusu('rendi',"Actualizo rendicion Nro $id");
                }else{
                        logusu('rendi',"Intento Actualizar rendicion Nro $id con $error ");
                        $data['content'] = $error.anchor($this->url."/dataedit/show/$id",'Regresar');
                        $data['title']   = " $this->tits ";
                        $data["head"]    = $this->rapyd->get_head().script('jquery.js').script("plugins/jquery.numeric.pack.js");
                        $this->load->view('view_ventanas', $data);
                }
        }

        function anular($id){
                $this->rapyd->load('dataobject');

                $error='';

                $do = new DataObject("rendi");
                $do->rel_one_to_many('itrendi', 'itrendi', array('numero'=>'numero'));
                $do->load($id);
                
                $ttotal      = $do->get('total'   );
                $cod_prov    = $do->get('cod_prov');
                
                $SPRV= new DataObject("sprv");
                $SPRV->load($cod_prov);

                $anti   = $SPRV->get('anti'  );
                $maximo = $SPRV->get('maximo');
                $demos  = $SPRV->get('demos' );
                $nombre = $SPRV->get('nombre');
                $saldo  = ($demos    );
                
                if(($ttotal)>($saldo))
                        $error.="<div class='alert'><p>El monto del $this->tits ($ttotal) es mayor que el monto demostrado ($saldo) del proveedor $nombre</p></div>";
                
                $tiva =0;
                $tmont=0;

                $sta=$do->get('status');

                if($sta=="C"){
                        $tiva=$tsub=$ttot=$ivan=0;$importes=array(); $ivas=array();
                        for($i=0;$i < $do->count_rel('itrendi');$i++){
                                $codigopres  = $do->get_rel('itrendi','codigopres',$i);                         
                                $codigoadm   = $do->get_rel('itrendi','codigoadm' ,$i);
                                $fondo       = $do->get_rel('itrendi','fondo'     ,$i);
                                $importe = $subtotal    = $do->get_rel('itrendi','subtotal'  ,$i);
                                $iva         = $do->get_rel('itrendi','iva'       ,$i);
                                $factura     = $do->get_rel('itrendi','factura'   ,$i);
                                $ordinal     = $do->get_rel('itrendi','ordinal'   ,$i);
                                $ivan         += $iva;
                                
                                //$error.=$this->chequeapresup($codigoadm,$fondo,$codigopres,$ordinal,$subtotal,0,'false',"El Monto ($subtotal) es mayor al disponible para descausar para la partida ($codigopres)");
                                $cadena = $codigoadm.'_._'.$fondo.'_._'.$codigopres.'_._'.$ordinal;
                                if(array_key_exists($cadena,$importes)){
                                        $importes[$cadena]+=$importe;
                                }else{
                                        $importes[$cadena]  =$importe;
                                }
                        }
                        
                        foreach($importes AS $cadena=>$monto){
                                $temp  = explode('_._',$cadena);
                                $error.=$this->chequeapresup($temp[0],$temp[1],$temp[2],$temp[3],$monto,0,'false',"El Monto es mayor al disponible para la partida ($temp[0]) ($temp[1]) ($temp[2])");
                        }
                        
                        //$error.=$this->chequeapresup($codigoadm,$fondo,'PARTIDAIVA','',$ivan,0,'false','El Monto ($monto) es mayor al disponible  para descausar para la partida de IVA');
      
                        if(empty($error)){
                                //$tiva=$tsub=$ttot=$ivan=0;
                                //for($i=0;$i < $do->count_rel('itrendi');$i++){
                                //
                                //      $codigopres    = $do->get_rel('itrendi','codigopres',$i);                               
                                //      $codigoadm     = $do->get_rel('itrendi','codigoadm' ,$i);
                                //      $fondo         = $do->get_rel('itrendi','fondo'     ,$i);
                                //      $subtotal      = $do->get_rel('itrendi','subtotal'  ,$i);
                                //      $iva           = $do->get_rel('itrendi','iva'       ,$i);
                                //      $ttot=$total   = $do->get_rel('itrendi','total'     ,$i);
                                //      $factura       = $do->get_rel('itrendi','factura'   ,$i);
                                //      $ordinal       = $do->get_rel('itrendi','ordinal'   ,$i);
                                //      $ivan         += $iva;
                                //      
                                //      $error.=$this->afectapresup($codigoadm,$fondo,$codigopres,$ordinal,$subtotal,0, -1 ,array("comprometido","causado","opago","pagado"));
                                //}
                                
                                foreach($importes AS $cadena=>$monto){
                                        $temp  = explode('_._',$cadena);
                                        $error.=$this->afectapresup($temp[0],$temp[1],$temp[2],$temp[3],$monto,0, -1 ,array("comprometido","causado","opago","pagado"));
                                }
                                
                                //if(empty($error))
                                //      $error.=$this->afectapresup($codigoadm,$fondo,'PARTIDAIVA','',$ivan,0, -1 ,array("comprometido","causado","opago","pagado"));

                                if(empty($error)){
                                        $SPRV ->set('demos',$demos-$ttotal);
                                        $SPRV ->save();
                                        $do->set('status' ,'A');
                                        $do->save();
                                }
                        }
                }

                if(empty($error)){
                        logusu('rendi',"Reverso rendicion de cuentas numero $id");
                        redirect($this->url."/dataedit/show/$id");
                }else{
                        logusu('rendi',"Reverso rendicion de cuentas numero $id con $error");
                        $data['content'] = $error.anchor($this->url."/dataedit/show/$id",'Regresar');
                        $data['title']   = " $this->tits ";
                        $data["head"]    = $this->rapyd->get_head().script('jquery.js').script("plugins/jquery.numeric.pack.js");
                        $this->load->view('view_ventanas', $data);
                }
        }

        function _valida($do){

                $error   ='';
                $siva    =$ssub=$stot=0;
                $numero  = $do->get('numero');
                $cod_prov= $do->get('cod_prov');
                
                $obligapiva   = $this->datasis->traevalor('obligapiva');
                $partidaiva   = $this->datasis->traevalor("PARTIDAIVA");
                $admfondop=$admfondo=$borrarivas=array();
                $totiva=$ivasm=0;
                for($i=0;$i < $do->count_rel('itrendi');$i++){
                        $subtotal     = $do->get_rel('itrendi','subtotal'   ,$i);
                        $iva          = $do->get_rel('itrendi','iva'        ,$i);
                        $codigoadm    = $do->get_rel('itrendi','codigoadm'  ,$i);
                        $fondo        = $do->get_rel('itrendi','fondo'      ,$i);
                        $codigopres   = $do->get_rel('itrendi','codigopres' ,$i);
                        $factura      = $do->get_rel('itrendi','factura'    ,$i);
                        $controlfac   = $do->get_rel('itrendi','controlfac' ,$i);
                        $esiva        = $do->get_rel('itrendi','esiva'      ,$i);
                        $total        = $subtotal+($iva * $subtotal/100);
                        $ivan         = $subtotal*$iva/100;
                        
                        if($esiva!='A')
                        $error.=$this->itpartida($codigoadm,$fondo,$codigopres);

                        if($esiva=='S'){
                                $ivasm=$importe+$ivasm;
                        }elseif($esiva=='A'){
                                $borrarivas[$i]=$i;
                        }else{
                                $totiva+=$ivan;
                        }
                        
                        $presupiva=$this->datasis->dameval("SELECT (aumento+asignacion-disminucion+traslados-(comprometido)) FROM presupuesto WHERE codigoadm='$codigoadm' AND tipo='$fondo' AND codigopres='$partidaiva'");
                        if($presupiva>0 && $obligapiva=='S')
                                $partida2=$partidaiva;
                        else 
                                $partida2=$codigopres;
                        
                        $cadena3 = $codigoadm.'_._'.$fondo.'_._'.$partida2;
                        $admfondop[$cadena3]=(array_key_exists($cadena3,$admfondop)?$admfondop[$cadena3]+=$ivan:$admfondop[$cadena3] =$ivan);
                        $cadena2 = $codigoadm.'_._'.$fondo;
                        $admfondo[$cadena2]=(array_key_exists($cadena2,$admfondo)?$admfondo[$cadena2]+=$ivan:$admfondo[$cadena2] =$ivan);

                        $existe = $this->datasis->dameval("SELECT COUNT(*) FROM v_presaldo WHERE codigoadm = '$codigoadm' AND fondo = '$fondo' AND codigo='$codigopres' AND movimiento = 'S' AND saldo >0 ");
                        if($existe <= 0)
                        $error.="<div class='alert'><p>La partida no existe: $codigoadm $fondo $codigopres</p></div>";

                        $siva += $iva;
                        $stot += $total;
                        $ssub += $subtotal;
                        
                        $do->set_rel('itrendi','total', $total ,$i);
                        
                        if(strlen($numero) > 0 && $iva>0){
                                $this->chexiste_factura($numero,$factura,$controlfac,$cod_prov,'R',$e);
                                $error.=$e;
                        }
                }
		
		$borrarivas=array_reverse($borrarivas,true);
		foreach($borrarivas AS $value){
		//	echo $value;
			array_splice($do->data_rel['itrendi'],$value,1);
		//	print_r($do->data_rel['itocompra']);	
		}
                
                if($ivasm>0){
                        if(round($totiva,2)!=round($ivasm,2))
                                $error.="La suma de las partidas a descontar ($ivasm) el IVA debe ser igual a la suma del IVA del IVA total ($totiva)";
                }else{
                
                
                        $admfondo2 = ($obligapiva!='S'?$admfondop:$admfondo);
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
                                        $do->set_rel('itrendi','codigoadm'   ,$temp[0]    ,$i);
                                        $do->set_rel('itrendi','fondo'       ,$temp[1]    ,$i);
                                        $do->set_rel('itrendi','codigopres'  ,$p          ,$i);
                                        $do->set_rel('itrendi','descripcion' ,'IVA'       ,$i);
                                        $do->set_rel('itrendi','esiva'       ,'A'         ,$i);
                                        $do->set_rel('itrendi','subtotal'    ,$monto      ,$i);
                                        $do->set_rel('itrendi','iva'         ,0           ,$i);
                                        $do->set_rel('itrendi','total'       ,$monto      ,$i);
                                        //$error.=$this->itpartida($temp[0],$temp[1],$p);
                                }
                        }
                }
                
                
                if(!empty($error)){
                        $do->error_message_ar['pre_upd']=$error;
                        $do->error_message_ar['pre_ins']=$error;                                
                        return false;           
                }

                $do->set('iva'       ,    $iva  );
                $do->set('subtotal'  ,    $ssub );
                $do->set('total'     ,    $stot );
                $do->set('status'    ,'P'       );
        }
        
        function creaanti($id=''){
                $this->rapyd->load('dataobject');
                
                $do = new DataObject("rendi");
                $do->load($id);
                
                $observan = "Reintegro de Rendicion de Fondo en Avance Nro ($id)";
                foreach($do->get_all() AS $key=>$value)
                        $$key = $value;
                        
                $ueje = $this->datasis->dameval("SELECT uejecutora FROM odirect WHERE cod_prov=".$this->db->escape($cod_prov)." ORDER BY fecha desc LIMIT 1");
                
                $data = array(
                "codprov"    => $cod_prov,
                "total"      => $total   ,
                "subtotal"   => $total   ,
                "total2"     => $total   ,
                "status"     => 'G1'     ,
                "observa"    => $observan,
                "fecha"      => dbdate_to_human(date('Ymd')),
                "uejecutora" => $ueje
                );
                
                $error = http_post_fields(site_url()."presupuesto/anti/exterin/insert",$data);
                
                if(strpos($error,'<p>')>0)
                        $error=substr($error,(-1*(strlen($error)-strpos($error,'<p>'))));
                else
                        $error='';
                
                if(empty($error)){
                        $atts = array(
                         'width'      => '800',
                         'height'     => '600',
                         'scrollbars' => 'yes',
                         'status'     => 'yes',
                         'resizable'  => 'yes',
                         'screenx'    => '0',
                         'screeny'    => '0'
                        );
                 
                        logusu('rendi',"Creo Fondo en avance de rendicion ($id)");
                        $salida = "Se creo Fondo en Avance de la rendicion ($id)</br>Entre por el modulo de Fondo en Avance para ejecutarlo</br>";
                        $salida.=anchor($this->url."dataedit/show/$id",'Regresar').'</br>';
                        $salida.=anchor_popup("presupuesto/anti",'Ir a Fondo en Avance',$atts);
                        $data['content'] = $salida;
                }else{
                        logusu('rendi',"Intento crear fondo en avance de rendicion($id) con error $error");
                        $data['content'] = "<div class='alert'>".$error.'</div>'.anchor($this->url."dataedit/show/$id",'Regresar');
                }
                $data['title']   = " $this->tits ";
                $data["head"]    = $this->rapyd->get_head();
                $this->load->view('view_ventanas', $data);
        }
        
        function creariva($id){
                $this->rapyd->load('dataobject');

                $error='';

                $do = new DataObject("rendi");
                $do->pointer('sprv' ,'sprv.proveed=rendi.cod_prov','sprv.reteiva as reteivap');
                $do->rel_one_to_many('itrendi', 'itrendi', array('numero'=>'numero'));
                $do->load($id);
                
                $sta          = $do->get('status');
                $cod_prov     = $do->get('cod_prov');
                $reteiva_prov = $do->get_pointer('reteivap');
                
        
                $qq       = $this->db->query("SELECT tasa, redutasa, sobretasa FROM civa  ORDER BY fecha DESC LIMIT 1");
                $ivaplica = $qq->row_array();
        
                $ivaplica['redutasa'];
                $ivaplica['tasa'];
                $ivaplica['sobretasa'];

                if($sta=="C"){
                        for($i=0;$i < $do->count_rel('itrendi');$i++){
                                $codigoadm   = $do->get_rel('itrendi','codigoadm' ,$i);
                                $fondo       = $do->get_rel('itrendi','fondo'     ,$i);
                                $subtotal    = $do->get_rel('itrendi','subtotal'  ,$i);
                                $iva         = $do->get_rel('itrendi','iva'       ,$i);
                                $factura     = $do->get_rel('itrendi','factura'   ,$i);
                                $controlfac  = $do->get_rel('itrendi','controlfac',$i);
                                $fechafac    = $do->get_rel('itrendi','fechafac'  ,$i);
                                
                                
                                $tivag=$mivag=$tivaa=$ivaa=$tivar=$mivar=0;
                                $tasaiva = $iva*100/$subtotal;
                                if(round($tasaiva,2)==round($ivaplica['redutasa'],2)){
                                        $tivar=$ivaplica['redutasa'];
                                        $mivar=$iva;
                                }elseif(round($tasaiva,2)==round($ivaplica['sobretasa'],2)){
                                        $tivaa=$ivaplica['sobretasa'];
                                        $mivaa=$iva;
                                }elseif(round($tasaiva,2)==round($ivaplica['tasa'],2)){
                                        $tivag=$ivaplica['tasa'];
                                        $mivag=$iva;
                                }else{
                                        $error = "El Monto de IVA no corresponde con alguno de las tasas actuales";
                                }
                        }
                        
                        $ide = $this->db->query($id);
                        $r   =$this->datasis->dameval("SELECT SUM(iva*(SELECT reteiva_prov FROM sprv WHERE proveed=(SELECT cod_prov FROM rendi WHERE numero=$ide))/100) FROM itrendi WHERE numero=$ide");
                        if($r>0)
                        $nriva = $this->datasis->fprox_numero('nriva');
                        
                        if(empty($error)){
                                for($i=0;$i < $do->count_rel('itrendi');$i++){
                                        $codigoadm   = $do->get_rel('itrendi','codigoadm' ,$i);
                                        $fondo       = $do->get_rel('itrendi','fondo'     ,$i);
                                        $subtotal    = $do->get_rel('itrendi','subtotal'  ,$i);
                                        $iva         = $do->get_rel('itrendi','iva'       ,$i);
                                        $factura     = $do->get_rel('itrendi','factura'   ,$i);
                                        $controlfac  = $do->get_rel('itrendi','controlfac',$i);
                                        $fechafac    = $do->get_rel('itrendi','fechafac'  ,$i);
                                        
                                        $tivag=$mivag=$tivaa=$ivaa=$tivar=$mivar=0;
                                        $tasaiva = $iva*100/$subtotal;
                                        if(round($tasaiva,2)==round($ivaplica['redutasa'],2)){
                                                $tivar=$ivaplica['redutasa'];
                                                $mivar=$iva;
                                        }elseif(round($tasaiva,2)==round($ivaplica['sobretasa'],2)){
                                                $tivaa=$ivaplica['sobretasa'];
                                                $mivaa=$iva;
                                        }elseif(round($tasaiva,2)==round($ivaplica['tasa'],2)){
                                                $tivag=$ivaplica['tasa'];
                                                $mivag=$iva;
                                        }
                                        
                                        $reteiva = $iva * $reteiva_prov/100;
                                        if($reteiva>0)
                                                $error = $this->riva($nriva,$codigoadm,$fondo,'','','',$factura,$controlfac,$fechafac,$cod_prov,0,$tivag,$mivag,$tivaa,$ivaa,$tivar,$mivar,$reteiva,'','',$reteiva_prov,$id);
                                }
                        }
                }
                if(1*$error > 0){
                        $salida = "Se creo retencion de IVA numero $error";
                }else{
                        $salida=$error;
                }
                
                $data['content'] = $salida."</br>".anchor($this->url."dataedit/show/$id",'Regresar');
                $data['title']   = ' Crear Retencion de IVA ';
                $data["head"]    = $this->rapyd->get_head();
                $this->load->view('view_ventanas', $data);
        }

        function positivo($valor){
                if ($valor <= 0){
                        $this->validation->set_message('positivo',"El Subtotal debe ser positivo");
                        return FALSE;
                }
                return TRUE;
        }
        
        function  instalar(){
			$query="CREATE TABLE `itrendi` (
				`id` INT(11) NOT NULL AUTO_INCREMENT,
				`numero` INT(11) NULL DEFAULT NULL COMMENT 'Numero de la Orden',
				`codigoadm` VARCHAR(12) NULL DEFAULT NULL,
				`fondo` VARCHAR(20) NULL DEFAULT NULL,
				`codigopres` VARCHAR(17) NULL DEFAULT NULL,
				`ordinal` CHAR(3) NULL DEFAULT NULL,
				`descripcion` VARCHAR(80) NULL DEFAULT NULL,
				`unidad` VARCHAR(10) NULL DEFAULT NULL,
				`cantidad` DECIMAL(10,2) NULL DEFAULT NULL,
				`precio` DECIMAL(19,2) NULL DEFAULT NULL,
				`importe` DECIMAL(19,2) NULL DEFAULT NULL,
				`iva` DECIMAL(6,2) NULL DEFAULT '0.00' COMMENT 'Tasa de IVA',
				`devo` DECIMAL(19,2) NULL DEFAULT NULL,
				`factura` CHAR(12) NULL DEFAULT NULL,
				`controlfac` VARCHAR(12) NULL DEFAULT NULL,
				`fechafac` DATE NULL DEFAULT NULL,
				`cod_prov` VARCHAR(16) NULL DEFAULT NULL,
				`subtotal` DECIMAL(19,2) UNSIGNED NULL DEFAULT NULL,
				`total` DECIMAL(19,2) UNSIGNED NULL DEFAULT NULL,
				`iva2` DECIMAL(19,2) NULL DEFAULT NULL,
				PRIMARY KEY (`id`)
			)
			COLLATE='utf8_general_ci'
			ENGINE=MyISAM";
			$this->db->simple_query($query);



                $query="ALTER TABLE `itrendi` CHANGE COLUMN `codigopres` `codigopres` VARCHAR(25) NULL DEFAULT NULL";
                $this->db->simple_query($query);
                $query="ALTER TABLE `itrendi` ADD COLUMN `esiva` CHAR(1) NULL DEFAULT NULL ";
                $this->db->simple_query($query);
        }
        function _post_insert($do){
                $numero     = $do->get('numero'  );
                logusu('rendi',"Creo rendicion de cuentas $numero");
                //redirect($this->url."actualizar/$id");
        }
        
        function _post_update($do){
                $numero     = $do->get('numero'  );
                logusu('rendi' ,"Modifico rendicion de cuentas $numero");
                //redirect($this->url."actualizar/$id");
        }
}
?>
