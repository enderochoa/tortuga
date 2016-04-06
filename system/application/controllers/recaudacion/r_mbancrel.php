<?php
class R_mbancrel extends Controller {
	var $titp='Relacionar Movimientos Bancarios';
	var $tits='Relacionar Movimientos Bancarios';
	var $url ='recaudacion/r_mbancrel/';
	function R_mbancrel(){
		parent::Controller();
		$this->load->library("rapyd");
		$this->datasis->modulo_id(471,1);
	}
	
	function index(){
		redirect($this->url."filteredgrid");
	}
	
	function filteredgrid(){
		$this->rapyd->load('datafilter','datagrid');
		
		$mBANC=array(
				'tabla'   =>'banc',
				'columnas'=>array(
					'codbanc' =>'C&oacute;odigo',
					'banco'   =>'Banco',
					'numcuent'=>'Cuenta',
					'saldo'   =>'Saldo'
				),
				'filtro'  =>array(
					'codbanc' =>'C&oacute;odigo',
					'banco'   =>'Banco',
					'numcuent'=>'Cuenta'
				),
				'retornar'=>array(
					'codbanc'=>'codbanc' 
				),
				'where'=>'activo = "S"',
				'titulo'  =>'Buscar Bancos'
				);

		$bBANC=$this->datasis->modbus($mBANC);

		$filter = new DataFilter($this->titp, 'r_mbancrel a');
		$filter->db->select(array('a.id','a.codbanc','a.tipo_doc','a.cheque','a.monto','a.total','a.fecha','a.fechaing','a.concepto'));
		$filter->db->join('r_mbanc b','a.id=b.id_mbancrel');
		$filter->db->group_by('a.id');

		$filter->id = new inputField('Id','id');
		$filter->id->rule      ='trim';
		$filter->id->size      =13;
		$filter->id->maxlength =11;
		$filter->id->db_name   ='a.id';

		$filter->codbanc = new inputField('Codbanc','codbanc');
		$filter->codbanc->rule      ='trim';
		$filter->codbanc->size      =12;
		$filter->codbanc->maxlength =10;
		$filter->codbanc->append($bBANC);
		$filter->codbanc->db_name   ='a.codbanc';

		$filter->tipo_doc = new dropdownField("Tipo Doc","tipo_doc");
		$filter->tipo_doc->option("","");
		$filter->tipo_doc->option("ND","Nota de Debito");
		$filter->tipo_doc->option("NC","Nota de Credito");
		$filter->tipo_doc->option("CH","Cheque");
		$filter->tipo_doc->option("DP","Deposito");	
		$filter->tipo_doc->db_name   ='a.tipo_doc';

		$filter->cheque = new inputField('Transaccion','cheque');
		$filter->cheque->rule      ='trim';
		$filter->cheque->size      =10;
		$filter->cheque->db_name   ='a.cheque';

		$filter->monto = new inputField('Monto','monto');
		$filter->monto->rule      ='trim|numeric';
		$filter->monto->css_class ='inputnum';
		$filter->monto->size      =21;
		$filter->monto->maxlength =19;
		$filter->monto->db_name   ='a.monto';

		$filter->total = new inputField('Total Items','total');
		$filter->total->rule      ='trim|numeric';
		$filter->total->css_class ='inputnum';
		$filter->total->size      =21;
		$filter->total->maxlength =19;
		$filter->total->db_name   ='a.total';

		$filter->fecha = new dateField('Fecha Transaccion','fecha');
		$filter->fecha->db_name   ='a.fecha';
		$filter->fecha->rule      ='chfecha';
		$filter->fecha->size      =10;
		$filter->fecha->maxlength =8;
		$filter->fecha->db_name   ='fecha';

		$filter->fechaing = new dateField('Fecha Ingreso','fechaing');
		$filter->fechaing->rule      ='chfecha';
		$filter->fechaing->size      =10;
		$filter->fechaing->maxlength =8;
		$filter->fechaing->db_name   ='fechaing';

		$filter->concepto = new inputField('Concepto','concepto');
		$filter->concepto->rule      ='trim';
		$filter->concepto->size      =20;
		$filter->concepto->db_name   ='a.concepto';
		
		$filter->transacciones = new inputField('Transaccion Detalle','transacciones');
		$filter->transacciones->rule      ='trim';
		$filter->transacciones->size      =10;
		$filter->transacciones->db_name   ='b.cheque';

		$filter->buttons('reset', 'search');
		$filter->build();

		$uri = anchor($this->url.'dataedit/show/<raencode><#id#></raencode>','<#id#>');

		$grid = new DataGrid('');
		$grid->order_by('a.id','desc');
		$grid->per_page = 10000;

		$grid->column_orderby('Id'                ,"$uri"                                             ,'id'           ,'align="left"'  );
		$grid->column_orderby('Codbanc'           ,"codbanc"                                          ,'codbanc'      ,'align="left"'  );
		$grid->column_orderby('Tipo_doc'          ,"tipo_doc"                                         ,'tipo_doc'     ,'align="left"'  );
		$grid->column_orderby('Cheque'            ,"cheque"                                           ,'cheque'       ,'align="left"'  );
		$grid->column_orderby('Monto'             ,"<nformat><#monto#></nformat>"                     ,'monto'        ,'align="right"' );
		$grid->column_orderby('Total'             ,"<nformat><#total#></nformat>"                     ,'total'        ,'align="right"' );
		$grid->column_orderby('Fecha'             ,"<dbdate_to_human><#fecha#></dbdate_to_human>"     ,'fecha'        ,'align="center"');
		$grid->column_orderby('Fecha Ingreso'     ,"<dbdate_to_human><#fechaing#></dbdate_to_human>"  ,'fechaing'     ,'align="center"');
		$grid->column_orderby('Concepto'          ,"concepto"                                         ,'concepto'     ,'align="left"'  );

		$action = "javascript:window.location='" .site_url($this->url.'reparafecha'). "'";
		$grid->button("reperafecha","Reparar Fechas de Efectivo",$action,"TL");

		$grid->add($this->url.'selectr_mbanc');
		$grid->build();

		$data['filtro']  = $filter->output;
		$data['content'] = $grid->output;
		$data['head']    = $this->rapyd->get_head().script('jquery.js');
		$data['title']   = $this->titp;
		$this->load->view('view_ventanas', $data);

	}
	
	function dataedit(){
		$this->rapyd->load('dataobject','datadetails');

		$mBANC=array(
				'tabla'   =>'banc',
				'columnas'=>array(
					'codbanc' =>'C&oacute;odigo',
					'banco'   =>'Banco',
					'numcuent'=>'Cuenta',
					'saldo'   =>'Saldo'
				),
				'filtro'  =>array(
					'codbanc' =>'C&oacute;odigo',
					'banco'   =>'Banco',
					'numcuent'=>'Cuenta'
				),
				'retornar'=>array(
					'codbanc'=>'codbanc' 
				),
				'where'=>'activo = "S"',
				'titulo'  =>'Buscar Bancos'
				);

		$bBANC=$this->datasis->modbus($mBANC);

		$script='
			$(document).ready(function(){
				$(".inputnum").numeric(".");
			});
			';

		$do = new DataObject('r_mbancrel');
		$do->rel_one_to_many('r_mbanc', 'r_mbanc', array('id'=>'id_mbancrel'));

		$edit = new DataDetails($this->tits, $do);

		$edit->back_url = site_url($this->url."filteredgrid");

		$edit->pre_process('insert','_valida');
		$edit->pre_process('update','_valida');
		$edit->pre_process('delete','_pre_delete');
		$edit->post_process('insert','_post_insert');
		$edit->post_process('update','_post_update');
		$edit->post_process('delete','_post_delete');

		$edit->id = new inputField('Id','id');
		$edit->id->rule     ='trim';
		$edit->id->size      =13;
		$edit->id->maxlength =11;
		$edit->id->mode      = 'autohide';
		$edit->id->when      =array('show','modify');

		$edit->codbanc = new inputField('Codbanc','codbanc');
		$edit->codbanc->rule     ='trim|required';
		$edit->codbanc->size      =12;
		$edit->codbanc->maxlength =10;
		$edit->codbanc->append($bBANC);
		
		$edit->tipo_doc = new dropdownField("Tipo Documento","tipo_doc"             );
		$edit->tipo_doc->option(""  ,""               );
		$edit->tipo_doc->option("CH","Cheque"         );
		$edit->tipo_doc->option("NC","Nota de Credito");
		$edit->tipo_doc->option("ND","Nota de Debito" );
		$edit->tipo_doc->option("DP","Deposito"       );
		$edit->tipo_doc->style  ="width:180px";
		$edit->tipo_doc->rule   = 'required';

		$edit->cheque = new inputField('Transaccion','cheque');
		$edit->cheque->rule     ='trim';
		$edit->cheque->size     =20;

		$edit->monto = new inputField('Monto','monto');
		$edit->monto->rule     ='trim|numeric';
		$edit->monto->css_class='inputnum';
		$edit->monto->size      =21;
		$edit->monto->maxlength =19;

		$edit->total = new inputField('Total','total');
		$edit->total->rule     ='trim|numeric';
		$edit->total->css_class='inputnum';
		$edit->total->size      =21;
		$edit->total->maxlength =19;
		$edit->total->mode      ='autohide';

		$edit->fecha = new dateField('Fecha Trasaccion','fecha');
		$edit->fecha->rule     ='chfecha';
		$edit->fecha->size      =10;
		$edit->fecha->maxlength =8;

		$edit->fechaing = new dateField('Fecha Ingreso','fechaing');
		$edit->fechaing->rule     ='chfecha';
		$edit->fechaing->size      =10;
		$edit->fechaing->maxlength =8;

		$edit->concepto = new textareaField('Concepto','concepto');
		$edit->concepto->rule     ='trim';
		$edit->concepto->cols      = 60;
		$edit->concepto->rows      = 3;
		
		/*
		 * DETALLE
		 * */
		 
		$edit->itid = new hiddenField('Id','id_<#i#>');
		$edit->itid->rel_id ='r_mbanc';
		$edit->itid->db_name='id';
		
		$edit->itabono = new hiddenField('Abono','abono_<#i#>');
		$edit->itabono->rel_id ='r_mbanc';
		$edit->itabono->db_name='abono';
               
		$edit->itcodmbanc = new hiddenField('Codmbanc','codmbanc_<#i#>');
		$edit->itcodmbanc->rel_id ='r_mbanc';
		$edit->itcodmbanc->db_name='codmbanc';
               
		$edit->itcodbanc = new inputField('Codbanc','codbanc_<#i#>');
		$edit->itcodbanc->rel_id    ='r_mbanc';
		$edit->itcodbanc->db_name   ='codbanc';
		$edit->itcodbanc->type      ="inputhidden";
               
		$edit->ittipo_doc = new inputField('Tipo_doc','tipo_doc_<#i#>');
		$edit->ittipo_doc->rel_id    ='r_mbanc';
		$edit->ittipo_doc->db_name   ='tipo_doc';
		$edit->ittipo_doc->type      ="inputhidden";
		       
		$edit->itfecha = new inputField('Fecha','fecha_<#i#>');
		$edit->itfecha->rel_id    ='r_mbanc';
		$edit->itfecha->db_name   ='fecha';
		$edit->itfecha->type      ="inputhidden";
               
		$edit->itcheque = new inputField('Cheque','cheque_<#i#>');
		$edit->itcheque->rel_id    ='r_mbanc';
		$edit->itcheque->db_name   ='cheque';
		$edit->itcheque->type      ="inputhidden";
               
		$edit->itmonto = new inputField('Monto','monto_<#i#>');
		$edit->itmonto->rel_id    ='r_mbanc';
		$edit->itmonto->db_name   ='monto';
		$edit->itmonto->type      ="inputhidden";
		
		

		$action = "javascript:window.location='" .site_url($this->url.'eliminar/'.$edit->rapyd->uri->get_edited_id()). "'";
		$edit->button_status("btn_status",'Eliminar',$action,"TR","show","button");

		$edit->buttons('modify', 'save', 'undo', 'back');
		$edit->build();
		
		$conten["form"]  =&  $edit;
		$data['content'] = $this->load->view('recaudacion/r_mbancrel'  , $conten,true);
		$data['title']   = "$this->tits";
		$data["head"]    = script('jquery.js').script('jquery-ui.js').script('plugins/jquery.numeric.pack.js').script('plugins/jquery.floatnumber.js').script('plugins/jquery.meiomask.js').style('vino/jquery-ui.css').$this->rapyd->get_head();
		$this->load->view('view_ventanas', $data);
	}
	
	function eliminar($id){
		$query="UPDATE r_mbanc SET id_mbancrel=null WHERE id_mbancrel=$id";
		$this->db->query($query);
		
		$query="DELETE FROM r_mbancrel WHERE id=$id";
		$this->db->query($query);
		
		redirect($this->url);
		
	}
	
	function selectr_mbanc(){
		//$this->datasis->modulo_id(71,1);
		$this->rapyd->load("datafilter","datagrid");
		$this->load->helper('form');
		//$this->rapyd->uri->keep_persistence();
		
		$mBANC=array(
				'tabla'   =>'banc',
				'columnas'=>array(
					'codbanc' =>'C&oacute;odigo',
					'banco'=>'Banco',
					'numcuent'=>'Cuenta'),
				'filtro'  =>array(
					'codbanc' =>'C&oacute;odigo',
					'banco'=>'Banco',
					'numcuent'=>'Cuenta'),
				'retornar'=>array(
					'codbanc'=>'codbanc' ),
				'where'=>'activo = "S"',
				'titulo'  =>'Buscar Bancos'
				);

		$bBANC=$this->datasis->p_modbus($mBANC,"banc");

		$filter = new DataFilter("");
		$filter->db->select(array("a.id","a.abono","a.codmbanc","a.codbanc","a.tipo_doc","a.cheque","a.monto","a.fecha","a.concepto","a.id_mbancrel","b.numcuent","b.banco"));
		$filter->db->from("r_mbanc a");
		$filter->db->join("banc b","b.codbanc=a.codbanc","left");
		$filter->db->where("(LENGTH(a.id_mbancrel)=0 OR a.id_mbancrel IS NULL OR a.id_mbancrel=0)");//OR a.id_mbancrel=0
		#$filter->db->where("LENGTH(a.id_mbancrel )=0 OR a.id_mbancrel IS NULL");
		
		//$filter->db->orderby("a.cheque");
		//$filter->db->where("a.tipo =", "Trabajo");

		$filter->fecha = new dateonlyField("Fecha", "fecha");
		$filter->fecha->size=12;
		$filter->fecha->clause='where';
		$filter->fecha->operator='=';
		
		$filter->codbanc = new inputField("Banco", 'codbanc');
		$filter->codbanc->size = 6;
		$filter->codbanc->append($bBANC);
		$filter->codbanc->db_name="a.codbanc";
		
		$filter->tipo_doc = new dropdownField("Tipo Documento","tipo_doc");
		$filter->tipo_doc->db_name   = 'tipo_doc';
		$filter->tipo_doc->style     ="width:130px;";
		$filter->tipo_doc->option(""  ,""                     );
		$filter->tipo_doc->option("EF","Efectivo"             );
		$filter->tipo_doc->option("DP","Deposito"             );
		$filter->tipo_doc->option("DB","Tarjeta D&eacute;bito");
		$filter->tipo_doc->option("CR","Tarjeta Credito"      );
		$filter->tipo_doc->option("DF","Diferencia"           );

		$filter->buttons("reset","search");
		$filter->build();
		
		$total = new inputField("Total", "total");
		$total->status="create";
		$total->size  =15;
		$total->build();
		$salida=$total->label.$total->output;
		
		$iralfiltropagoc = anchor($this->url.'filteredgrid','Ir al Filtro');
		$grid = new DataGrid($iralfiltropagoc);

		function sel($numero){
			return form_checkbox('data[]', $numero);
		}
		
		$monto = new inputField("Monto", "monto");
		$monto->grid_name='monto<#id#>';
		$monto->status   ='modify';
		$monto->size     =12;
		$monto->css_class='inputnum';
		$monto->readonly=true;

		$data = array(
			'name'        => 'todo',
			'id'          => 'todo',
			//'value'       => 'accept',
			'checked'     => FALSE,
			'style'       => 'margin:10px',
			);

		$salida1=form_checkbox($data);

		$grid = new DataGrid($iralfiltropagoc."</br>".$salida);
		$grid->order_by("cheque","asc");

		$grid->per_page = 1000;
		$grid->use_function('substr','str_pad','sel','nformat');

		$grid->column($salida1              ,"<sel><#id#></sel>");
		$grid->column_orderby("Cod. Banco"      ,"codbanc"                                     ,"codbanc"    ,"align='left'  ");
		$grid->column_orderby("Banco"           ,"banco"                                       ,"banco"      ,"align='left'  ");
		$grid->column_orderby("Cuenta"          ,"numcuent"                                    ,"numcuent"   ,"align='left'  ");
		$grid->column_orderby("Transaccion"     ,"cheque"                                      ,"cheque"     ,"align='left'  ");
		$grid->column_orderby("Fecha"           ,"<dbdate_to_human><#fecha#></dbdate_to_human>","fecha"      ,"align='center'");
		$grid->column_orderby("Tipo Doc"        ,"tipo_doc"                                    ,"tipo_doc"   ,"align='center'");
		$grid->column_orderby("Monto"           ,$monto                                        ,"monto"      ,"align='right' ");

		$grid->build();
		$grid->db->last_query();

		$salida =form_open($this->url.'guarda');
		$salida.=$grid->output;
		$salida.=form_submit('Crear Relacion', 'Crear Relacion');
		$salida.=form_close();

		$data['filtro']  = $filter->output;
		$data['content'] = $salida;
		$data['script']  = script("jquery.js")."\n";
		$data['script']  = '<script language="javascript" type="text/javascript">';
		$data['script'] .= '
		function suma(){
			t=0;
			$(":checkbox").each(function(i,val){
				name =val.name;
				console.log("substr"+name.substring(0,4));
				if(name.substring(0,4)=="data"){
				
					if(val.checked==true){
						monto=parseFloat($("#monto"+val.value).val());
						tipo =val.name.substr(2,2);
						t=t+monto;
					}
				}
			});
			$("#total").val(Math.round(t*100)/100);
		}

		$(document).ready(function(){
			suma();
			$("#todo").change(function(){
				
				console.log("aaa");
				var ch=$(this).is(":checked");
				console.log("aaa"+ch);
				$(":checkbox").each(function(i,val){
					if(ch==true){
						val.checked=true;
					}else{
						val.checked=false;
					}
					
				});
			});
			
			
			$(":checkbox").change(function(){
				suma();
			});
		});';
		$data['script'] .= '</script>';
		$data['title']   = "Seleccione las Movimientos Bancarios ";
		$data['head']    = $this->rapyd->get_head().script('jquery.js');
		$this->load->view('view_ventanas', $data);
	}
	
	function reparafecha(){
		
		$ABONOCODBANCDEFECTO  = $this->datasis->traevalor('ABONOCODBANCDEFECTO');
		$ABONOCODBANCDEFECTOE = $this->db->escape($ABONOCODBANCDEFECTO);
		
		$query="
		UPDATE r_recibo a
		JOIN r_abonosit b ON a.id=b.recibo
		JOIN r_mbanc c ON b.abono=c.abono
		SET c.fecha=a.fecha
		WHERE a.fecha<>c.fecha AND tipo_doc IN ('EF','DF')
		";
		
		$this->db->query($query);

		redirect($this->url.'filteredgrid');
	}

	function guarda(){
		$error='';
		$data    =$this->input->post('data');

		$query="INSERT INTO r_mbancrel(id) values('')";
		$this->db->query($query);
		$id_mbancrel = $this->db->insert_id();
		
		if($id_mbancrel>0){
			$query="UPDATE r_mbanc SET id_mbancrel=$id_mbancrel WHERE id  IN (".implode(",",$data).")";
			$this->db->query($query);
			
			$query="
			REPLACE INTO r_mbancrel (id,concepto,fechaing,total,codbanc)
			SELECT $id_mbancrel,GROUP_CONCAT(cheque SEPARATOR ' ') cheque ,fecha,SUM(monto) total,codbanc FROM r_mbanc WHERE id_mbancrel=$id_mbancrel
			";
			
			$this->db->query($query);
		}else{
				$error.="Disculpe, se esta presentando un problema, intentelo de nuevo por favor";
		}
		
		if(empty($error)){
			logusu('r_mbancrel',"Creo Relacionar de cuentas $id_mbancrel "."(".implode(",",$data).")");
			redirect($this->url.'dataedit/modify/'.$id_mbancrel);
		}else{
			logusu('r_mbancrel',"Creo Relacionar de cuentas $id_mbancrel "."(".implode(",",$data).") con error $error" );
			$data['content'] = $error.anchor($this->url."filteredgrid",'Regresar');
			$data['title']   = " $this->tits ";
			$data["head"]    = $this->rapyd->get_head().script('jquery.js').script("plugins/jquery.numeric.pack.js");
			$this->load->view('view_ventanas', $data);
		}
	}

	function _valida($do){
		$error = '';

		if(!empty($error)){
			$do->error_message_ar['pre_ins']=$error;
			$do->error_message_ar['pre_upd']=$error;
			return false;
		}
	}

	function _pre_delete($do){
		$error = '';
		if(!empty($error)){
			$do->error_message_ar['pre_ins']=$error;
			$do->error_message_ar['pre_upd']=$error;
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
		$query="CREATE TABLE `r_mbancrel` (
		  `id` int(11) NOT NULL AUTO_INCREMENT,
		  `codbanc` varchar(10) NOT NULL,
		  `tipo_doc` char(2) NOT NULL,
		  `cheque` text NOT NULL,
		  `monto` decimal(19,2) NOT NULL,
		  `total` decimal(19,2) NOT NULL,
		  `fecha` date NOT NULL,
		  `fechaing` date NOT NULL,
		  `concepto` text NOT NULL,
		  PRIMARY KEY (`id`)
		) ENGINE=MyISAM DEFAULT CHARSET=utf8";
		$this->db->simple_query($query);
		
		$query="ALTER TABLE `r_mbancrel` ADD COLUMN `id_mbanc` INT NULL DEFAULT NULL AFTER `concepto`";
		$this->db->simple_query($query);
	}
}
?>
