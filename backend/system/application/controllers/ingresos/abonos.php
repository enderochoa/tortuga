<?php
class abonos extends Controller {
	var $titp='Cobranza';
	var $tits='Cobranza';
	var $url ='ingresos/abonos/';
	function abonos(){
		parent::Controller();
		$this->load->library("rapyd");
		//$this->datasis->modulo_id(216,1);
	}
	function index(){
		redirect($this->url."filteredgrid");
	}
	
	function filteredgrid(){
		$this->rapyd->load('datafilter','datagrid');

		$filter = new DataFilter($this->titp);
		$filter->db->select(array('a.id','c.nombre','GROUP_CONCAT(c.numero) numero'
		,'a.estampa','c.nombre','c.rifci','c.fecha','c.id id_recibo'));
		//,'(SELECT GROUP_CONCAT(cheque) FROM  sfpa d WHERE a.id=d.abono GROUP BY a.id) cheque'
		$filter->db->from('abonos a'                  );
		$filter->db->join('itabonos b','a.id=b.abono' );
		$filter->db->join('recibo c','b.recibo=c.id'   );
		$filter->db->groupby('a.id'                    );
		

		$filter->id = new inputField('Ref','id');
		$filter->id->rule      ='max_length[11]';
		$filter->id->size      =13;
		$filter->id->maxlength =11;
		$filter->id->db_name="a.id";
		
		$filter->numero = new inputField('Recibo','numero');
		$filter->numero->rule      ='max_length[11]';
		$filter->numero->size      =13;
		
		$filter->id2 = new inputField('Ref Recibo','id2');
		$filter->id2->rule      ='max_length[11]';
		$filter->id2->size      =13;
		$filter->id2->maxlength =11;
		$filter->id2->db_name="c.id";
		
		/*
		$filter->cheque = new inputField('Transacci&oacute;n','cheque');
		$filter->cheque->rule      ='max_length[11]';
		$filter->cheque->size      =13;
*/
		$filter->estampa = new inputField('Estampa','estampa');
		$filter->estampa->size      =10;
		$filter->estampa->db_name="a.estampa";
		
		$filter->fecha = new dateonlyField('Fecha Recibo','fecha');
		$filter->fecha->rule      ='chfecha';
		$filter->fecha->size      =10;
		$filter->fecha->maxlength =8;
		$filter->fecha->db_name='c.fecha';
		$filter->fecha->clause='where';
		$filter->fecha->operator='=';
		
		$filter->nombre = new inputField('Nombre','nombre');
		$filter->nombre->size      =20;
		$filter->nombre->group     ="Contribuyente";

		$filter->rifci = new inputField('Rif/Ced','rifci');
		$filter->rifci->rule      ='max_length[13]';
		$filter->rifci->size      =15;
		$filter->rifci->maxlength =13;
		$filter->rifci->group     ="Contribuyente";
		
		$filter->monto = new inputField('Monto Recibo','monto');
		$filter->monto->rule      ='max_length[13]';
		$filter->monto->size      =15;
		$filter->monto->maxlength =13;
		$filter->monto->db_name="c.monto";

		$filter->buttons('reset', 'search');
		$filter->build();

		$uri = anchor($this->url.'dataedit/show/<raencode><#id#></raencode>','<#id#>');

		$grid = new DataGrid('');
		$grid->order_by('a.id ','desc');
		$grid->per_page = 40;

		$grid->column_orderby('Ref.'          ,"$uri"                                           ,'id'         ,'align="left"');
		$grid->column_orderby('Recibo'        ,"<wordwrap><#numero#>|50|\n|true</wordwrap>"     ,'numero'     ,'align="left"');
		$grid->column_orderby('Nombre'        ,"nombre"                                         ,'nombre'     ,'align="left"');
		$grid->column_orderby('Ref Recibo'    ,"id_recibo"                                      ,'id_recibo'     ,'align="left"');
		//$grid->column_orderby('Transacciones' ,"<wordwrap><#cheque#>|50|\n|true</wordwrap>"     ,'cheque'     ,'align="left"');
		$grid->column_orderby('Estampa'       ,"estampa"                                        ,'estampa'    ,'align="left"');

		$grid->add($this->url.'dataedit/create');
		$grid->build();

		$data['filtro']  = $filter->output;
		$data['content'] = $grid->output;
		$data['head']    = $this->rapyd->get_head().script('jquery.js');
		$data['title']   = $this->titp;
		$this->load->view('view_ventanas', $data);

	}
	
	function dataedit(){
		$this->rapyd->load('datadetails','dataobject');
		$this->load->helper('form');

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
					'numcuent'=>'Cuenta',
					'saldo'=>'Saldo'),
				'p_uri'=>array(
				  4=>'<#i#>'),
				'retornar'=>array(
					'codbanc'=>'codbanc_<#i#>'
					 ),
				'where'=>'activo = "S" ',
				//'script'=>array('ultimoch(<#i#>)','cal_nombrech(<#i#>)'),
				'titulo'  =>'Buscar Bancos');
		$bBANC=$this->datasis->p_modbus($mBANC,"<#i#>");
		
		$mRECIBO=array(
				'tabla'   =>'recibo',
				'columnas'=>array(
					'id'       =>'Ref.',
					'numero'   =>'Numero',
					'fecha'    =>'Fecha',
					'monto'    =>'Monto',
					'rifci'    =>'RIF/CI',
					'nombre'   =>'Nombre',
					'observa'  =>'Observa'
					),
				'filtro'  =>array(
					'id'       =>'Ref.',
					'numero'   =>'Numero',
					'fecha'    =>'Fecha',
					'monto'    =>'Monto',
					'rifci'    =>'RIF/CI',
					'nombre'   =>'Nombre',
					'observa'  =>'Observa'
					),
				'p_uri'=>array(
				  4=>'<#i#>'),
				'retornar'=>array(
					'id'                              =>'recibo_<#i#>',
					'numero'                          =>'numerop_<#i#>',
					'DATE_FORMAT(fecha,"%d/%m/%Y")'   =>'fechap_<#i#>',
					'monto'                           =>'montop_<#i#>',
					'nombre'                          =>'nombrep_<#i#>',
					'observa'                         =>'observap_<#i#>'
					),
				'where'=>'status = "P" ',
				'script'=>array('cal_totr()'),
				'titulo'  =>'Buscar Recibos por Pagar');
		$bRECIBO=$this->datasis->p_modbus($mRECIBO,"<#i#>");

		$do = new DataObject("abonos");
		$do->rel_one_to_many('itabonos', 'itabonos', array('id'=>'abono'));
		$do->rel_one_to_many('sfpa'    , 'sfpa'    , array('id'=>'abono'));
		$do->rel_pointer('itabonos' ,'recibo' ,'itabonos.recibo=recibo.id'  ,'recibo.numero AS numerop,recibo.fecha AS fechap,recibo.monto AS montop,recibo.observa AS observap,recibo.tipo AS tipop,recibo.nombre AS nombrep,recibo.id AS idp','LEFT');

		$edit = new DataDetails($this->tits, $do);
		$edit->back_url = site_url($this->url."filteredgrid");
		$edit->set_rel_title('itabonos','Rubro <#o#>');
		$edit->set_rel_title('sfpa','Rubro <#o#>');

		$edit->back_url = site_url($this->url."filteredgrid");

		$edit->pre_process('insert' ,'_valida');
		$edit->pre_process('update' ,'_valida');
		$edit->post_process('insert','_post_insert');
		$edit->post_process('update','_post_update');
		$edit->post_process('delete','_post_delete');

		$edit->id = new inputField('id','id');
		$edit->id->rule='max_length[11]';
		$edit->id->size =13;
		$edit->id->maxlength =11;
		$edit->id->mode='autohide';
		$edit->id->when=array('show','modify');
		
		$edit->totr =  new inputField("Total Recibos", 'totr');
		$edit->totr-> size     = 10;
		$edit->totr->readonly  =true;
		$edit->totr->css_class ='inputnum';
		
		$edit->totb =  new inputField("Total Bancos", 'totb');
		$edit->totb-> size     = 10;
		$edit->totb->readonly  =true;
		$edit->totb->css_class ='inputnum';

		$edit->estampa = new autoUpdateField('estampa' ,date('Ymd'), date('Ymd'));

/******** RECIBOS *************/
		$edit->itrecibo =  new inputField("(<#o#>) Ref. Recibo", 'recibo_<#i#>');
		$edit->itrecibo->db_name   = 'recibo';
		$edit->itrecibo->size      = 5;
		$edit->itrecibo->rel_id    ='itabonos';
		$edit->itrecibo->readonly  =true;
		$edit->itrecibo->append($bRECIBO);

		$edit->itnumerop =  new inputField("(<#o#>) Numero", 'numerop_<#i#>');
		$edit->itnumerop->db_name   = 'numerop';
		$edit->itnumerop->size      = 10;
		$edit->itnumerop->rel_id    ='itabonos';
		$edit->itnumerop->pointer   =true;
		$edit->itnumerop->readonly  =true;
		
		$edit->itfechap =  new inputField("(<#o#>) Fecha", 'fechap_<#i#>');
		$edit->itfechap->db_name   = 'fechap';
		$edit->itfechap-> size     = 10;
		$edit->itfechap->rel_id    ='itabonos';
		$edit->itfechap->pointer   =true;
		$edit->itfechap->readonly  =true;
		
		$edit->itmontop =  new inputField("(<#o#>) Monto", 'montop_<#i#>');
		$edit->itmontop->db_name   = 'montop';
		$edit->itmontop-> size     = 10;
		$edit->itmontop->rel_id    ='itabonos';
		$edit->itmontop->pointer   =true;
		$edit->itmontop->readonly  =true;
		$edit->itmontop->value     =0;
		
		$edit->itnombrep =  new inputField("(<#o#>) Nombre", 'nombrep_<#i#>');
		$edit->itnombrep->db_name   = 'nombrep';
		$edit->itnombrep-> size     = 20;
		$edit->itnombrep->rel_id    ='itabonos';
		$edit->itnombrep->pointer   =true;
		$edit->itnombrep->readonly  =true;
		
		$edit->itobservap =  new inputField("(<#o#>) Observaci&oacute;n", 'observap_<#i#>');
		$edit->itobservap->db_name   = 'observap';
		$edit->itobservap-> size     = 30;
		$edit->itobservap->rel_id    ='itabonos';
		$edit->itobservap->pointer   =true;
		$edit->itobservap->readonly  =true;

/****** CHEQUES *********************/
		$edit->itcodbanc =  new inputField("(<#o#>) Banco", 'codbanc_<#i#>');
		$edit->itcodbanc->db_name   = 'codbanc';
		$edit->itcodbanc-> size     = 4;
		$edit->itcodbanc->rel_id    ='sfpa';
		$edit->itcodbanc->rule      = "required|callback_banco";
		$edit->itcodbanc->append($bBANC);
		$edit->itcodbanc->value     = $this->datasis->traevalor('ABONOCODBANCDEFECTO');

		$edit->ittipo_doc = new dropdownField("(<#o#>) Tipo Documento","tipo_doc_<#i#>");
		$edit->ittipo_doc->db_name   = 'tipo_doc';
		$edit->ittipo_doc->rel_id    ='sfpa';
		$edit->ittipo_doc->style     ="width:130px;";
		
		if($this->datasis->traevalor('ABONOS_EF_DEFECTO')=='S'){
			$edit->ittipo_doc->option("EF","Efectivo");
			$edit->ittipo_doc->option("DP","Deposito"       );
			$edit->ittipo_doc->option("DB","Tarjeta D&eacute;bito");
			$edit->ittipo_doc->option("DF","Diferencia");
		}else{
			$edit->ittipo_doc->option("DP","Deposito"       );
			$edit->ittipo_doc->option("DB","Tarjeta D&eacute;bito");
			$edit->ittipo_doc->option("DF","Diferencia");
			$edit->ittipo_doc->option("EF","Efectivo");
		}
		
		

		$edit->itcheque =  new inputField("(<#o#>) Transacci&oacute;n", 'cheque_<#i#>');
		$edit->itcheque->db_name   ='cheque';
		$edit->itcheque->size      = 20;
		$edit->itcheque->rel_id    ='sfpa';

		$edit->itfecha = new  dateonlyField("(<#o#>) Fecha Cheque",  "fecha_<#i#>");
		$edit->itfecha->db_name     ='fecha';
		$edit->itfecha->size        =10;
		$edit->itfecha->rel_id      ='sfpa';
		$edit->itfecha->insertValue = date('Ymd');
		$edit->itfecha->rule        ='required';

		$edit->itmonto = new inputField("(<#o#>) Total", 'monto_<#i#>');
		$edit->itmonto->db_name   ='monto';
		$edit->itmonto->size      = 10;
		$edit->itmonto->rel_id    ='sfpa';
//		$edit->itmonto->css_class ='inputnum';
		$edit->itmonto->onchange  = "cal_totm();";
		$edit->itmonto->value     =0;
		
/**************** POR COBRAR ******************************************/
		$porcobrar=$this->db->query("SELECT a.id,a.numero,a.fecha,a.monto,a.nombre,a.observa FROM recibo a WHERE status='P' ORDER BY estampa");
		$porcobrar=$porcobrar->result_array();

		$edit->button_status("btn_add_sfpa"     ,'Agregar Pago',"javascript:add_sfpa()","MB",'modify',"button_add_rel");
		$edit->button_status("btn_add_sfpa2"    ,'Agregar Pago',"javascript:add_sfpa()","MB",'create',"button_add_rel");
		$edit->button_status("btn_add_itabonos" ,'Agregar Recibo',"javascript:add_itabonos()","PA",'modify',"button_add_rel");
		$edit->button_status("btn_add_itabonos2",'Agregar Recibo',"javascript:add_itabonos()","PA",'create',"button_add_rel");

		if($this->datasis->puede(388))
		$edit->buttons('modify');
		
		$edit->buttons('add', 'save', 'undo', 'delete', 'back');
		$edit->build();
		
		$conten["form"]  =&$edit;
		//$smenu['link']   =barra_menu('80B');
		//$data['smenu']   = $this->load->view('view_sub_menu', $smenu,true);
		$conten['porcobrar'] =$porcobrar;
		foreach($porcobrar as $key=>$value)
		$porcobrar[$key]['fecha']=dbdate_to_human($value['fecha']);
		$conten['porcobrarj']=json_encode($porcobrar);
		$data['content']    = $this->load->view('view_abonos'  , $conten,true);
		$data['title']      = $this->tits;
		$data["head"]       = script('jquery.js').script('jquery-ui.js').script('plugins/jquery.numeric.pack.js').script('plugins/jquery.floatnumber.js').script('plugins/jquery.meiomask.js').style('vino/jquery-ui.css').$this->rapyd->get_head();
		$this->load->view('view_ventanas', $data);

	}
	
	function _valida($do){
		$error='';
		$t=0;
		for($i=0;$i<$do->count_rel('itabonos');$i++){
			
			$m=$this->input->post('montop_'.$i);
			$t+=$m;
		}
		
		$t2=0;
		for($i=0;$i<$do->count_rel('sfpa');$i++){
			$m2=$this->input->post('monto_'.$i);
			$t2+=$m2;
			$cheque    =$do->get_rel('sfpa','cheque'  ,$i);
			$tipo_doc  =$do->get_rel('sfpa','tipo_doc',$i);
			$codbanc   =$do->get_rel('sfpa','codbanc' ,$i);
		}
		
//		if(round($t,2)<>round($t2,2))
//		$error.="ERROR. Los Montos en Bancos y de recibos son diferentes";
		
		if(!empty($error)){
			$do->error_message_ar['pre_ins']="<div class='alert'>".$error."</div>";
			$do->error_message_ar['pre_upd']="<div class='alert'>".$error."</div>";
			return false;
		}
	}
	
	function existe($codbanc,$tipo_doc,$cheque){
		$error='';
		$codbance=$this->db->escape($codbanc);
		$chequee =$this->db->escape($cheque);
		if(in_array($tipo_doc,array('DP'))){
			$c=$this->datasis->dameval("SELECT COUNT(*) FROM sfpa WHERE codbanc=$codbance AND cheque=$chequee AND tipo_doc='$tipo_doc'");
			if($c>0)
			$error.="Error. el $tipo_doc del Banco $codbanc ya existe";
		}
		return $error;
	}
	
	function _post_save($do){
		$id = $do->get('id');
		
		$this->db->query("
		UPDATE recibo a
		JOIN itabonos b ON a.id=b.recibo
		SET a.`status`='P' 
		WHERE b.abono=$id");
		
		for($i=0;$i<$do->count_rel('itabonos');$i++){
			$recibo=$do->get_rel('itabonos','recibo',$i);
			
			$this->db->where('id', $recibo);
			$this->db->update('recibo', array('status'=>'C'));
		}
	}
	
	function _post_del($do){
		for($i=0;$i<$do->count_rel('itabonos');$i++){
			$recibo=$do->get_rel('itabonos','recibo',$i);
			$this->db->where('id', $recibo);
			$this->db->update('recibo', array('status'=>'P'));
		}
	}

	function _post_insert($do){
		$this->_post_save($do);
		$primary =implode(',',$do->pk);
		logusu($do->table,"Creo $this->tits $primary ");
	}
	function _post_update($do){
		$this->_post_save($do);
		$primary =implode(',',$do->pk);
		logusu($do->table,"Modifico $this->tits $primary ");
	}
	function _post_delete($do){
		$this->_post_del($do);
		$primary =implode(',',$do->pk);
		logusu($do->table,"Elimino $this->tits $primary ");
	}

	function instalar(){
		$mSQL="CREATE TABLE `abonos` (
			`id` int(11) NOT NULL AUTO_INCREMENT,
			`estampa` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
			PRIMARY KEY (`id`)
		      ) ENGINE=MyISAM DEFAULT CHARSET=utf8";
		$this->db->simple_query($mSQL);
		
		$query="
		CREATE TABLE `itabonos` (
			`id` INT(11) NOT NULL AUTO_INCREMENT,
			`abono` INT(11) NOT NULL,
			`recibo` INT(11) NULL DEFAULT NULL,
			PRIMARY KEY (`id`)
		)
		COLLATE='utf8_general_ci'
		ENGINE=MyISAM
		AUTO_INCREMENT=1";
		$this->db->simple_query($mSQL);
		

		$query="
		CREATE TABLE `sfpa` (
			`id` INT(11) NOT NULL AUTO_INCREMENT,
			`abono` INT(11) NOT NULL,
			`codmbanc` INT(11) NOT NULL,
			`codbanc` VARCHAR(10) NOT NULL,
			`tipo_doc` CHAR(2) NOT NULL,
			`cheque` TEXT NOT NULL,
			`monto` DECIMAL(19,2) NOT NULL,
			`fecha` DATE NOT NULL,
			`observa` TEXT NOT NULL,
			PRIMARY KEY (`id`)
		)
		COLLATE='utf8_general_ci'
		ENGINE=MyISAM
		AUTO_INCREMENT=1";
		$this->db->simple_query($query);
		
		

	}

}
?>
