<?php
require_once(BASEPATH.'application/controllers/presupuesto/common.php');
class mbancnoc extends Common {

	var $titp='Debitos y Creditos no Contabilizados';
	var $tits='Debitos &oacute; Credito no Contabilizado';
	var $url ='tesoreria/mbancnoc/';

	function mbancnoc(){
		parent::Controller();
		$this->load->library("rapyd");
	}

	function index(){
		redirect($this->url."filteredgrid");
	}

	function filteredgrid(){
		//$this->datasis->modulo_id(35,1);
		$this->rapyd->load("datafilter","datagrid");
		//$this->rapyd->uri->keep_persistence();

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
					'codbanc'=>'codbanc',
					'banco'=>'nombreb' ),
				'where'=>$where,
				'titulo'  =>'Buscar Bancos'
				);

		$bBANC=$this->datasis->p_modbus($mBANC,"banc");

		$filter = new DataFilter("");

		$filter->db->from("mbancnoc a");

		$filter->id = new inputField("N&uacute;mero", "id");
		$filter->id->db_name="a.id";
		$filter->id->size  =10;

		$filter->cheque = new inputField("Cheque", "cheque");
		$filter->cheque->db_name="a.cheque";
		$filter->cheque->size  =10;

		$filter->fecha = new dateonlyField("Fecha", "fecha");
		$filter->fecha->dbformat = "Y-m-d";
		$filter->fecha->size=12;
		
		$filter->fconcilia = new dateonlyField("F. Conciliado", "fconcilia");
		$filter->fconcilia->dbformat = "Y-m-d";
		$filter->fconcilia->size=12;

		$filter->codbanc = new inputField("Banco", 'codbanc');
		$filter->codbanc->size = 6;
		$filter->codbanc->append($bBANC);

		$filter->monto = new inputField("Monto", 'monto');
		$filter->monto->size = 6;

                $filter->benefi = new inputField("A nombre de", 'benefi');
		$filter->benefi->size = 20;

		$filter->tipo_doc = new dropdownField("Tipo Doc","tipo_doc");
		$filter->tipo_doc->option("","");
		$filter->tipo_doc->option("ND","Nota de Debito" );
		$filter->tipo_doc->option("NC","Nota de Credito");
		$filter->tipo_doc->option("CH","Cheque"  );
		$filter->tipo_doc->option("DP","Deposito");
		
		$filter->vnoc = new dropdownField("Ver", "vnoc" );
		$filter->vnoc->clause  =" ";
		$filter->vnoc->db_name = " "; 
		$filter->vnoc->option('' ,'TODOS');
		$filter->vnoc->option('S' ,'CONCILIADOS');
		$filter->vnoc->option('N','NO CONCILIADOS');

		$filter->buttons("reset","search");

		$filter->build();
		$uri = anchor($this->url.'dataedit/show/<#id#>','<str_pad><#id#>|8|0|STR_PAD_LEFT</str_pad>');
		$uri2= anchor($this->url.'fconci/modify/<#id#>','Conciliar');
		
		
		if($filter->is_valid()){
			$q1       = $filter->db->_compile_select();
			$vnoc   = $filter->vnoc->newValue;
			
			$q="SELECT todo.* FROM (";
			$q.=$q1;
			$q.=")todo";
			if($vnoc=='S')
				$q.=" WHERE fconcilia IS NOT NULL";
				
			if($vnoc=='N')
				$q.=" WHERE fconcilia IS NULL";
			

			$qa=$this->db->query($q);
			$qa=$qa->result_array($qa);

			$grid = new DataGrid("No Contabilizados",$qa);
			$grid->order_by("id","desc");
			$grid->per_page = 20;
			$grid->use_function('substr','str_pad');

			$grid->column_orderby("N&uacute;mero"    ,$uri                                                ,"numero"                        );
			$grid->column_orderby("Cheque"           ,"cheque"                                            ,"cheque"                        );
			$grid->column_orderby("Tipo"             ,"tipo_doc"                                          ,"tipo_doc"                      );
			$grid->column_orderby("Fecha"            ,"<dbdate_to_human><#fecha#></dbdate_to_human>"      ,"fecha"    ,"align='center'"      );
			$grid->column_orderby("Banco"            ,"codbanc"                                           ,"fecha"    ,"align='center'"      );
			$grid->column_orderby("Monto"            ,"<nformat><#monto#>|2|,|.</nformat>"                ,"monto"    ,"align='right'"       );
			$grid->column_orderby("F. Conciliado"    ,"<dbdate_to_human><#fconcilia#></dbdate_to_human>"  ,"fconcilia","align='center'"      );
			$grid->column_orderby("Conciliar"        ,$uri2                                               ,"id"                            );
			
			$grid->add($this->url."dataedit/create");
			$grid->build();
			$ggrid=$grid->output;

		}else{
			$ggrid='';
		}

		//$data['content'] = $filter->output.$grid->output;
		$data['filtro']  = $filter->output;
		$data['content'] = $ggrid;
		$data['script'] = script("jquery.js")."\n";
		$data['title']   = "$this->titp";
		$data["head"]    = $this->rapyd->get_head();
		$this->load->view('view_ventanas', $data);
	}

	function dataedit($duplicar='S',$status='',$numero=''){
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
					'codbanc'=>'codbanc',
					'banco'=>'nombreb' ),
				'where'=>$where,
				'titulo'  =>'Buscar Bancos'
				);

		$bBANC=$this->datasis->p_modbus($mBANC,"banc");

		$script='
			$(".inputnum").numeric(".");

                        $(document).ready(function() {
				$("#tr_nombret").hide();
			});

			function copiabenefi(){
				benefi = $("#nombrep").val();
                                nombret= $("#nombret").val();
                                if(nombret==""){
                                    $("#benefi").val(benefi);
                                }else{
                                    $("#benefi").val(nombret);
                                }
			}
		';
		
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
                            'codigo'       =>'bcta'),
                    'titulo'  =>'Buscar Otros Ingresos'
                    );

		$bBCTA=$this->datasis->p_modbus($mBCTA,"bcta");
		
		$do = new DataObject("mbancnoc");
		$do->pointer('banc' ,'banc.codbanc=mbancnoc.codbanc','banc.banco as nombreb'     ,'LEFT');

		if($status=="create" && !empty($numero) && $duplicar=='S'){
			$do->load($numero);
			$do->set('cheque', '');
			$do->set('id', '');
			$do->set('monto', '');
			$do->pk  =array('id'=>'');
		}
		
		

		$edit = new DataEdit($this->tits, $do);

		$edit->back_url = site_url($this->url."filteredgrid");

		$edit->script($script,"create");
		$edit->script($script,"modify");

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

		$edit->nombret = new inputField("nombre temporal", 'nombret');
		$edit->nombret->size      = 50;
		$edit->nombret->db_name   =' ';
		$edit->nombret->when      =array("create","modify");

		$edit->codbanc =  new inputField("Banco", 'codbanc');
		$edit->codbanc-> size     = 3;
		$edit->codbanc-> rule     = "required";
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
		$edit->tipo_doc->option("NC","Nota de Credito");
		$edit->tipo_doc->option("ND","Nota de Debito" );
		$edit->tipo_doc->option("DP","Deposito" );
		$edit->tipo_doc->style  ="width:180px";
		$edit->tipo_doc->group  =  "Transaccion";
		$edit->tipo_doc->rule   = 'required';
		
		$edit->fecha = new  dateonlyField("Fecha Transacci&oacute;n",  "fecha");
		//$edit->fecha->insertValue = date('Y-m-d');
		$edit->fecha->size    =12;
		$edit->fecha->rule    = 'required';
		$edit->fecha->group   = "Transaccion";

		$edit->cheque =  new inputField("Nro. Transacci&oacute;n", 'cheque');
		$edit->cheque->rows   = 2;
		$edit->cheque->cols   = 80;
		$edit->cheque->rule   = "required";//callback_chexiste_cheque|
		$edit->cheque->group  = "Transaccion";
		
		$edit->monto = new inputField("Monto", 'monto');
		$edit->monto->size     = 20;
		$edit->monto->group    = "Transaccion";
		$edit->monto->css_class='inputnum';
		$edit->monto->rule     = 'callback_positivo';
		if($tipo=='I')
		$edit->monto->mode     = "autohide";

		//$edit->benefi =  new inputField("A nombre de", 'benefi');
		//$edit->benefi-> size  = 100;
		//$edit->benefi->rule   = "required";
		//$edit->benefi->group  = "Transaccion";

		$edit->observa = new textAreaField("Concepto", 'observa');
		$edit->observa->cols = 80;
		$edit->observa->rows = 5;
		$edit->observa->group    = "Transaccion";
		
		$edit->bcta = new inputField("Motivo Movimiento", 'bcta');
		$edit->bcta->size     = 6;
		$edit->bcta->append($bBCTA);
		$edit->bcta->readonly=true;
		$edit->bcta->group   = "Otros";
		
		$edit->fecha2          = new  dateonlyField("Fecha Aux",  "fecha2");
		$edit->fecha2->group   = "Otros";

		$action = "javascript:window.location='" .site_url($this->url.'/dataedit/S/create/'.$edit->rapyd->uri->get_edited_id()). "'";
		$edit->button_status("btn_anular",'Duplicar',$action,"TL","show");
		
		$action = "javascript:window.location='" .site_url($this->url.'/dataedit/S/create/'.$edit->rapyd->uri->get_edited_id()). "'";
		$edit->button_status("btn_anularm",'Duplicar',$action,"TL","modify");

		$edit->buttons("undo", "back","add","modify","save","delete");
		$edit->build();

		//$smenu['link']   = barra_menu('204');
		//$data['smenu']   = $this->load->view('view_sub_menu', $smenu,true);
		$data['content'] = $edit->output;
		$data['title']   = "$this->tits";
		$data["head"]    = script("jquery.pack.js").script("plugins/jquery.numeric.pack.js").script("plugins/jquery.floatnumber.js").$this->rapyd->get_head();
		$this->load->view('view_ventanas', $data);
	}
	
	function fconci(){
		$this->rapyd->load("dataobject","dataedit");
	
		$edit = new DataEdit("Conciliar No Contabilizado", "mbancnoc");
		$edit->back_url        = site_url($this->url."filteredgrid");
		$edit->back_cancel     =true;
		$edit->back_cancel_save=true;
		
		$edit->post_process('update','_fconci_post_update');
                
		$edit->id  = new inputField("Ref.", "id");
		$edit->id->mode="autohide";
		
		$edit->codbanc =  new inputField("Banco", 'codbanc');
		$edit->codbanc-> size     = 3;
		$edit->codbanc-> rule     = "required";
		$edit->codbanc->mode      = "autohide";   

		$edit->tipo_doc = new dropdownField("Tipo Documento","tipo_doc"  );
		$edit->tipo_doc->option("NC","Nota de Credito");
		$edit->tipo_doc->option("ND","Nota de Debito" );
		$edit->tipo_doc->mode      = "autohide";

		$edit->cheque =  new inputField("Nro. Transacci&oacute;n", 'cheque');
		$edit->cheque->mode      = "autohide";

		$edit->fecha = new  dateonlyField("Fecha Transacci&oacute;n",  "fecha");
		$edit->fecha->mode      = "autohide";

		$edit->monto = new inputField("Monto", 'monto');
		$edit->monto->mode     = "autohide";

		$edit->observa = new textAreaField("Concepto", 'observa');
		$edit->observa->mode      = "autohide";
		
		$edit->fconcilia = new  dateonlyField("Fecha Conciliacion",  "fconcilia");
		$edit->fconcilia->insertValue = date('Y-m-d');
		$edit->fconcilia->size        = 12;
		//$edit->fconcilia->rule        = 'required';
		
		$edit->fecha2      = new  dateonlyField("Fecha Aux",  "fecha2");
		
		$edit->concepto = new textAreaField("Concepto Movimiento Bancario", 'concepto');
		$edit->concepto->rows      = 2;
		
		$edit->buttons("undo","back","save","modify");
		$edit->build();

		$data['content'] = $edit->output;
		$data['title']   = "Fecha Conciliacion";
		$data["head"]    = $this->rapyd->get_head().script('jquery.js').script("plugins/jquery.numeric.pack.js").script("plugins/jquery.json.min.js");
		$this->load->view('view_ventanas', $data);
	}
	
	function _fconci_post_update($do){
		$this->rapyd->load('dataobject');
		
		$fconci      = $do->get('fconcilia'   );
		$monto       = $do->get('monto'       );
		$tipo_doc    = $do->get('tipo_doc'    );
		$observa     = $do->get('observa'     );
		$cheque      = $do->get('cheque'      );
		$codbanc     = $do->get('codbanc'     );
		$fecha       = $do->get('fecha'       );
		$concepto    = $do->get('concepto'    );
		
		
		
		if($fconci==19691231){
			$do->set('fconcilia','NULL');	
		}else{
			$MBANCNOCCREAMBANCALCONCI=$this->datasis->traevalor("MBANCNOCCREAMBANCALCONCI",'N',"creditos y debitos no contabilizados  crea movimiento bancario al conciliar");
			if($MBANCNOCCREAMBANCALCONCI=='S'){
				$titulo1 = $this->datasis->traevalor("TITULO1");
				$id_mbanc = $do->get('id_mbanc');
				$mbanc = new DataObject("mbanc");
				
				
				if($id_mbanc>0)
				$mbanc->load($id_mbanc);
				
				
				
				$mbanc->set('codbanc' ,$codbanc );
				$mbanc->set('monto'   ,$monto   );
				$mbanc->set('cheque'  ,$cheque  );
				$mbanc->set('tipo_doc',$tipo_doc);
				$mbanc->set('observa' ,$concepto);
				$mbanc->set('fecha'   ,$fconci  );
				$mbanc->set('status'  ,'J2'     );				
				$mbanc->set('benefi' ,$titulo1  );
				$mbanc->set('liable' ,'S'       );
				$mbanc->set('fliable',$fconci   );
				$mbanc->save();
				$id_mbanc=$mbanc->get('id');
				$do->set('id_mbanc',$id_mbanc);
				
				$this->db->query("CALL sp_banc_recalculo()");
			}
			
		}
	}

	function positivo($valor){
		if($valor < 0){
			$this->validation->set_message('positivo',"El campo monto debe ser positivo");
			return FALSE;
		}
		return TRUE;
	}

	function _post_insert($do){
		$status      = $do->get('status'      );
		$monto       = $do->get('monto'       );
		$tipo_doc    = $do->get('tipo_doc'    );
		$cheque      = $do->get('cheque'      );
		$codbanc     = $do->get('codbanc'     );
		$fecha       = $do->get('fecha'       );
		$id          = $do->get('id'          );
		
		$codbance    = $this->db->escape($codbanc);
		$chequee     = $this->db->escape($cheque );
		$fechae      = $this->db->escape($fecha  );
		
		if($tipo_doc='NC'){
			$mbanc = $this->datasis->damerow("SELECT count(*) cant,fecha FROM mbanc WHERE codbanc=$codbance AND  tipo_doc IN ('NC','DP') AND cheque=$chequee AND fecha2=$fechae AND monto=$monto ");
		}else{
			$mbanc = $this->datasis->damerow("SELECT count(*) cant,fecha FROM mbanc WHERE codbanc=$codbance AND  tipo_doc='$tipo_doc' AND cheque=$chequee AND fecha2=$fechae AND monto=$monto ");
		}
		
		if($mbanc['cant']>0){
			$mbanc_fecha  =  $mbanc['fecha'];
			$mbanc_fechae = $this->db->escape($mbanc_fecha);
			$this->db->query("UPDATE mbancnoc SET fconcilia=$mbanc_fechae WHERE id=$id");
		}
		
		$primary =implode(',',$do->pk);
		logusu($do->table,"Creo NC/ND no contabilizados $primary ");
		redirect($this->url.'/dataedit/S/create/'.$do->get('id'));
	}
	
	function _post_update($do){
		$primary =implode(',',$do->pk);
		logusu($do->table,"Creo NC/ND no contabilizados $primary ");
		redirect($this->url.'/dataedit/S/create/'.$do->get('id'));
	}
	
	function _post_delete($do){
		$primary =implode(',',$do->pk);
		logusu($do->table,"Creo NC/ND no contabilizados $primary ");
	}

	function instalar(){
		$this->db->simple_query("
		CREATE TABLE `mbancnoc` (
		`id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
		`codbanc` VARCHAR(10) NULL DEFAULT NULL,
		`tipo_doc` CHAR(2) NULL DEFAULT NULL,
		`cheque` TEXT NULL,
		`fecha` DATE NOT NULL DEFAULT '0000-00-00',
		`monto` DECIMAL(17,2) NULL DEFAULT NULL,
		`observa` TEXT NULL,
		`status` CHAR(2) NULL DEFAULT NULL,
		`usuario` VARCHAR(4) NULL DEFAULT NULL,
		`estampa` TIMESTAMP NULL DEFAULT NULL,
		`concilia` CHAR(1) NULL DEFAULT NULL,
		`fconcilia` DATE NULL DEFAULT NULL,
		PRIMARY KEY (`id`)
		)
		COLLATE='utf8_general_ci'
		ENGINE=MyISAM
		ROW_FORMAT=DEFAULT
		AUTO_INCREMENT=1");
		
		$query = "ALTER TABLE `mbancnoc` ADD COLUMN `bcta` INT NULL DEFAULT NULL ";
		$this->db->simple_query($query);
		
		$query = "ALTER TABLE `mbancnoc` ADD COLUMN `fecha2` DATE NULL DEFAULT NULL ";
		$this->db->simple_query($query);
		
		$query="ALTER TABLE `mbancnoc`	ADD COLUMN `concepto` TEXT NULL DEFAULT NULL AFTER `fecha2`";
		$this->db->simple_query($query);
		$query="ALTER TABLE `mbancnoc` ADD COLUMN `id_mbanc` INT NULL DEFAULT NULL AFTER `concepto`";
		$this->db->simple_query($query);
	}
}
