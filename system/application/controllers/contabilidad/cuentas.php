<?php
class cuentas extends Controller {
	var $titp='Relacionar Cuentas';
	var $tits='Relacion de Cuentas';
	var $url ='contabilidad/cuentas/';
	function cuentas(){
		parent::Controller();
		$this->load->library("rapyd");
		//$this->datasis->modulo_id(123,1);
	}
	function index(){
		redirect($this->url."filteredgrid");
	}
	function filteredgrid(){
		$this->rapyd->load('datafilter','datagrid');

		$filter = new DataFilter($this->titp, 'cuentas');

		$filter->id = new inputField('id','id');
		$filter->id->size      =13;
		$filter->id->maxlength =11;

		$filter->tipo = new inputField('tipo','tipo');
		$filter->tipo->size      =22;
		$filter->tipo->maxlength =20;

		$filter->codigo1 = new inputField('Codigo 1','codigo1');
		$filter->codigo1->size      =22;
		$filter->codigo1->maxlength =20;

		$filter->codigo2 = new inputField('Codigo 2','codigo2');
		$filter->codigo2->size      =22;
		$filter->codigo2->maxlength =20;

		$filter->cuenta1 = new inputField('Cuenta 1','cuenta1');
		$filter->cuenta1->size      =27;
		$filter->cuenta1->maxlength =25;

		$filter->cuenta2 = new inputField('Cuenta 2','cuenta2');
		$filter->cuenta2->size      =27;
		$filter->cuenta2->maxlength =25;

		$filter->buttons('reset', 'search');
		$filter->build();

		$uri = anchor($this->url.'dataedit/show/<raencode><#id#></raencode>','<#id#>');

		$grid = new DataGrid('');
		$grid->order_by('id');
		$grid->per_page = 40;

		$grid->column_orderby('id'     ,"$uri"   ,'id'     ,'align="left"');
		$grid->column_orderby('tipo'   ,"tipo"   ,'tipo'   ,'align="left"');
		$grid->column_orderby('codigo1',"codigo1",'codigo1','align="left"');
		$grid->column_orderby('codigo2',"codigo2",'codigo2','align="left"');
		$grid->column_orderby('cuenta1',"cuenta1",'cuenta1','align="left"');
		$grid->column_orderby('cuenta2',"cuenta2",'cuenta2','align="left"');

		$grid->add($this->url.'dataedit/create');
		$grid->build();

		$data['filtro']  = $filter->output;
		$data['content'] = $grid->output;
		$data['head']    = $this->rapyd->get_head().script('jquery.js');
		$data['title']   = $this->titp;
		$this->load->view('view_ventanas', $data);

	}
	function dataedit(){
		$this->rapyd->load('dataedit');

		$qformato=$this->qformato=$this->datasis->formato_cpla(); 

               $mCPLA=array(                         
                 'tabla'   =>'cpla',          
                 'columnas'=>array(           
                 'codigo' =>'C&oacute;digo',
                 'denominacion'=>'Descripci&oacute;n'),                                       
                 'filtro'  =>array('codigo'=>'C&oacute;digo','denominacion'=>'Denominacion'),   
                 'retornar'=>array('codigo'=>'<#i#>'),                  
                 'titulo'  =>'Buscar Cuenta',                           
                 'where'   =>"codigo LIKE \"$qformato\"",          
                 'p_uri'   =>array(4=>'<#i#>'),               
                 );                                      

		$cuenta1  =$this->datasis->p_modbus($mCPLA,"cuenta1" );    
		$cuenta2  =$this->datasis->p_modbus($mCPLA,"cuenta2");
		
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
					'codbanc'=>'codigo2_banc'),
				'where'=>'activo = "S"',
				'titulo'  =>'Buscar Bancos'
				);

		$bBANC=$this->datasis->p_modbus($mBANC,"banc");
		
		$script='
			$(document).ready(function() {
				selecciona();
			});
			
			function selecciona(){
			
				tipo=$("#tipo").val();
				if(tipo=="ANTI"){
					$("#tr_codigo2_banc").hide();
					$("#tr_codigo2_ueje").show();
					$("#tr_codigo1").show();
				}
				if(tipo=="COMI"){
					$("#tr_codigo2_banc").show();
					$("#tr_codigo2_ueje").hide();
					$("#tr_codigo1").hide();
				}
				if(tipo=="COMI2"){
					$("#tr_codigo2_banc").show();
					$("#tr_codigo2_ueje").hide();
					$("#tr_codigo1").hide();
				}
			}
		';

		$edit = new DataEdit($this->tits, 'cuentas');

		$edit->back_url = site_url($this->url."filteredgrid");
		
		$edit->script($script,"create");
		$edit->script($script,"modify");

		$edit->pre_process('insert','_valida');
		$edit->pre_process('update','_valida');

		$edit->post_process('insert','_post_insert');
		$edit->post_process('update','_post_update');
		$edit->post_process('delete','_post_delete');

		$edit->id = new inputField('id','id');
		$edit->id->when =array('show','modify');
		$edit->id->mode ='autohide';

		$edit->tipo = new dropdownField('Tipo','tipo' );
		$edit->tipo->option('ANTI' ,'Fonde En Anticipo');
		$edit->tipo->option('COMI' ,'Comision de banco ejercicio actual');
		$edit->tipo->option('COMI2','Comision de banco A&ntilde;os anteriores');
		$edit->tipo->onchange = "selecciona();";
		
		$edit->codigo1 = new dropdownField('Fuente de Financiamiento','codigo1');
		$edit->codigo1->options("SELECT fondo,CONCAT_WS(' ',fondo,descrip) a FROM fondo");
		
		$edit->codigo2 = new inputField('Codigo2','codigo2');
		$edit->codigo2->when=array("show");
		
		$edit->codigo2_ueje = new dropdownField('Unidad Ejecutora','codigo2_ueje');
		$edit->codigo2_ueje->options("SELECT codigo,CONCAT_WS(' ',codigo,nombre) a FROM uejecutora ORDER BY nombre");
		$edit->codigo2_ueje->when=array("create","modify");
		/*$edit->codigo2->rule='max_length[20]';
		$edit->codigo2->size =22;
		$edit->codigo2->maxlength =20;
		*/
		
		$edit->codigo2_banc = new inputField('Banco','codigo2_banc');
		$edit->codigo2_banc->size      =27;
		$edit->codigo2_banc->maxlength =25;
		$edit->codigo2_banc->rule      ='trim';
		$edit->codigo2_banc->append($bBANC);
		$edit->codigo2_banc->when=array("create","modify");

		$edit->cuenta1 = new inputField('Cuenta 1','cuenta1');
		$edit->cuenta1->size =27;
		$edit->cuenta1->maxlength =25;
		$edit->cuenta1->append($cuenta1);

		$edit->cuenta2 = new inputField('Cuenta 2','cuenta2');
		$edit->cuenta2->size =27;
		$edit->cuenta2->maxlength =25;
		$edit->cuenta2->append($cuenta2);

		$edit->buttons('add','modify', 'save', 'undo', 'delete', 'back');
		$edit->build();
		$data['content'] = $edit->output;
		$data["head"]    = script("jquery.pack.js").script("plugins/jquery.numeric.pack.js").script("plugins/jquery.floatnumber.js").$this->rapyd->get_head();
		$data['title']   = $this->tits;
		$this->load->view('view_ventanas', $data);

	}
	
	function _valida($do){
		$error='';
		$tipo          =$do->get('tipo');
		$codigo2_banc  =$this->input->post('codigo2_banc');
		$codigo2_ueje  =$this->input->post('codigo2_ueje');
		
		switch($tipo){
			case 'ANTI':{
				if(empty($codigo2_ueje))
				$error.="El Campo Unidad Ejecutora no puede estar vacio";
				else
				$do->set('codigo2',$codigo2_ueje);
			}
			break;
			case 'COMI':{
				if(empty($codigo2_banc))
				$error.="El Campo Banco no puede estar vacio";
				else
				$do->set('codigo2',$codigo2_banc);
			}
			break;
			case 'COMI2':{
				if(empty($codigo2_banc))
				$error.="El Campo Banco no puede estar vacio";
				else
				$do->set('codigo2',$codigo2_banc);
			}
			break;
		}
		
		
		if(!empty($error)){
			$edit->error_string             ="<div class='alert'>".$error."</div>";
			$do->error_message_ar['pre_ins']="<div class='alert'>".$error."</div>";
			$do->error_message_ar['pre_upd']="<div class='alert'>".$error."</div>";
			return false;
		}
	}

	function _post_insert($do){
		$primary =implode(',',$do->pk);
		logusu($do->table,"Creo $this->tits $primary ");
	}
	function _post_update($do){
		$primary =implode(',',$do->pk);
		logusu($do->table,"Modifico $this->tits $primary ");
	}
	function _post_delete($do){
		$primary =implode(',',$do->pk);
		logusu($do->table,"Elimino $this->tits $primary ");
	}

	function instalar(){
		$mSQL="CREATE TABLE `cuentas` (
		`id` int(11) NOT NULL AUTO_INCREMENT,
		`tipo` varchar(20) DEFAULT NULL,
		`codigo1` varchar(20) DEFAULT NULL,
		`codigo2` varchar(20) DEFAULT NULL,
		`cuenta1` varchar(25) DEFAULT NULL,
		`cuenta2` varchar(25) DEFAULT NULL,
		 PRIMARY KEY (`id`)
		) ENGINE=MyISAM DEFAULT CHARSET=utf8";
		$this->db->simple_query($mSQL);
	}
}
?>
