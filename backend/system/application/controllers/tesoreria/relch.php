<?php
//tbanco
class Relch extends Controller {

	var $url='tesoreria/relch';

	function Relch() {
		parent::Controller();
		$this->load->library('rapyd');
	}

	function index() {
		$this->datasis->modulo_id(256,1);
		redirect("tesoreria/relch/filteredgrid");
	}

	function filteredgrid() {

		$this->rapyd->load("datafilter2","datagrid");
		$filter = new DataFilter2("", "relch");
		$filter->db->select(array("relch.status status2","relch.id","relch.numero","relch.usuario","relch.fecha","IF(relch.destino='C','Caja','Interno') destino","mbanc.cheque"));
		$filter->db->join("mbanc","relch.id=mbanc.relch");
		$filter->db->groupby("relch.numero");

		$filter->id = new inputField("Ref. Relacion","id");
		$filter->id->db_name = "relch.id";

		$filter->numero = new inputField("Numero","numero");
		$filter->numero->db_name = "relch.numero";

		$filter->usuario = new dropdownField("Usuario", "usuario");
		$filter->usuario->db_name="relch.usuario";
		$filter->usuario->option("","");
		$filter->usuario->options("SELECT us_codigo,CONCAT_WS(' ',us_codigo,us_nombre) FROM usuario");
		$filter->usuario->style = "width:200px;";

		$filter->fecha = new dateonlyField("Fecha", 'fecha','d/m/Y');
		$filter->fecha->db_name="mbanc.fecha";
		$filter->fecha->dbformat='Y-m-d';

		$filter->cheque = new inputField("Cheque","cheque");
		$filter->cheque->db_name="mbanc.cheque";

		$filter->buttons("reset","search");
		$filter->build();

		$uri = anchor('tesoreria/relch/dataedit/modify/<#id#>','<#id#>');
		$uri2 = anchor('tesoreria/relch/busca/<#id#>','Modificar');

		$grid = new DataGrid("");
		$grid->per_page = 20;
		$grid->order_by("relch.id","desc");

		$grid->column_orderby("id"          ,$uri        ,"relch.id"       );
		$grid->column_orderby("Numero"      ,"numero"    ,"numero"   );
		$grid->column_orderby("Cheque"      ,"cheque"    ,"cheque"   );
		$grid->column_orderby("Usuario"     ,"usuario"   ,"usuario"  );
		$grid->column_orderby("Fecha"       ,"fecha"     ,"fecha"    );
		$grid->column_orderby("Destino"     ,"destino"   ,"destino"  );
		$grid->column_orderby("Fondo"       ,"fondo"     ,"fondo"  );
		$grid->column_orderby("Estado"      ,"status2"   ,"status2"  );
		$grid->column("Modificar Cheques"   ,$uri2               );

		$grid->build();

		//$grid->db->last_query();

		$atts3 = array(
		'width'     =>'640',
		'height'    =>'480',
		'scrollbars'=>'yes',
		'status'    =>'yes',
		'resizable' =>'yes',
		'screenx'   =>'5',
		'screeny'   =>'5',
		'id'        =>'recibo' );

		$salida=anchor('tesoreria/relch/busca/',"Crear Relacion");
		$salida.='</br>';
		$salida.=anchor_popup('reportes/ver/relch/',"Ordenes Pagadas",$atts3);
		$salida.='</br>';
		$salida.=anchor_popup('reportes/ver/relch2/',"Cheques Emitidos",$atts3);
		$salida.='</br>';
		$salida.=anchor_popup('reportes/ver/relch3/',"Anulacion y Reposicion",$atts3);

		$data['content'] = $filter->output.$salida.$grid->output;
		$data['title']   = "Relaciones de Ordenes Pagadas";
		$data["head"]    = $this->rapyd->get_head();
		$this->load->view('view_ventanas', $data);
	}
	function dataedit(){
		$this->rapyd->load("dataedit");

		$script ='
		$(function() {
			$(".inputnum").numeric(".");
		});
		';

		$edit = new DataEdit("Relacion de Cheques", "relch");

		$edit->back_url = site_url("tesoreria/relch/filteredgrid");
		$edit->script($script, "create");
		$edit->script($script, "modify");

		$edit->post_process('insert','_post_insert');
		$edit->post_process('update','_post_update');
		$edit->post_process('delete','_post_delete');

		$edit->id = new inputField("Id", "id");
		$edit->id->mode="autohide";
		$edit->id->when=array('show');
		$edit->id->size =5;

		$edit->numero = new inputField("Numero", "numero");
		//$edit->numero->mode='autohide';

		$edit->usuario = new inputField("Usuario", "usuario");
		$edit->usuario->mode='autohide';

		$edit->fecha = new dateonlyField("Fecha", 'fecha');
		//$edit->fecha->mode='autohide';

		$edit->destino= new dropdownField('Destino','destino');
		$edit->destino->mode='autohide';
		$edit->destino->option('C','Caja');
		$edit->destino->option('I','Interno');

		$edit->status = new inputField("Estado", 'status');
		$edit->status->mode='autohide';

		$edit->fondo = new dropdownField("Clasificacion", "fondo");
		$edit->fondo->style="width:300px;";
		$edit->fondo->option("","");
		$edit->fondo->options("SELECT fondo,fondo a FROM banc GROUP BY fondo UNION ALL SELECT CONCAT('REPO.',fondo),CONCAT('REPO.',fondo) a FROM banc GROUP BY fondo");

		$status=$edit->_dataobject->get('status');

		if($status=='P'){
			$action = "javascript:window.location='" .site_url($this->url.'/busca/'.$edit->rapyd->uri->get_edited_id()). "'";
			$edit->button_status("btn_anular",'Modificar Cheques',$action,"TR","show");
			$action = "javascript:window.location='" .site_url($this->url.'/actualizar/'.$edit->rapyd->uri->get_edited_id()). "'";
			$edit->button_status("btn_anular",'Cerrar Relacion',$action,"TR","show");
		}elseif($status=='C'){
			$action = "javascript:window.location='" .site_url($this->url.'/anular/'.$edit->rapyd->uri->get_edited_id()). "'";
			$edit->button_status("btn_rever",'Anular',$action,"TR","show");
		}else{
			//$edit->buttons("save");
		}
		$edit->buttons("modify","save");
		$edit->buttons( "undo", "back");
		$edit->build();

		$data['content'] = $edit->output;
		$data['title']   = "Relacion de Ordenes Pagadas";
		$data["head"]    = script("jquery.pack.js").script("plugins/jquery.numeric.pack.js").script("plugins/jquery.floatnumber.js").$this->rapyd->get_head();
		$this->load->view('view_ventanas', $data);
	}

	function busca($numero=''){
		$this->rapyd->load("dataform");

		$mBANC=array(
				'tabla'   =>'banc',
				'columnas'=>array(
					'codbanc' =>'C&oacute;odigo',
					'banco'=>'Banco',
					'numcuent'=>'Cuenta',
					'saldo'=>'Saldo'),
				'filtro'  =>array(
					'codbanc' =>'C&oacute;odigo',
					'banco'   =>'Banco',
					'numcuent'=>'Cuenta',
					'saldo'   =>'Saldo'),
				'retornar'=>array(
					'codbanc'=>'codbanc' ),
				'where'=>'activo = "S"',
				'titulo'  =>'Buscar Bancos',
				'script'  =>array('ultimoch()'));

		$bBANC=$this->datasis->p_modbus($mBANC,"banc");

		$filter = new DataForm("tesoreria/relch/selecciona/$numero");
		if(empty($numero)){
			$filter->fecha = new dateonlyField("Fecha", "fecha",'d/m/Y');
			$filter->fecha->db_name     = "fecha";
			$filter->fecha->dbformat    = 'Ymd';
			$filter->fecha->insertValue = date("Y-m-d");
			$filter->fecha->append(' mes/a&ntilde;o');
			$filter->fecha->groupby="Filtro";

			$filter->fondo = new dropdownField("Clasificacion", "fondo");
			$filter->fondo->style="width:300px;";
			$filter->fondo->option("","");
			$filter->fondo->options("SELECT fondo,fondo a FROM banc GROUP BY fondo ");
			}

		$filter->destino= new dropdownField('Destino','destino');
		$filter->destino->option('M','Mixto');
		$filter->destino->option('C','Caja');
		$filter->destino->option('I','Interno');
		$filter->destino->style = "width:200px;";

		$filter->usuario = new dropdownField("Usuario", "usuario");
		$filter->usuario->option("","");
		$filter->usuario->option("TRO","TRO");
		$filter->usuario->options("SELECT us_codigo,CONCAT_WS(' ',us_codigo,us_nombre) FROM usuario");
		$filter->usuario->style = "width:200px;";

		$filter->codbanc =  new inputField("Banco", 'codbanc');
		$filter->codbanc-> size     = 3;
		$filter->codbanc-> append($bBANC);

		$filter->destino2= new dropdownField('Destino Colocar','destino2');
		$filter->destino2->option('C','Caja');
		$filter->destino2->option('I','Interno');

		$filter->fondo2 = new dropdownField("Clasificacion a colocar", "fondo2");
		$filter->fondo2->style="width:300px;";
		$filter->fondo2->option("","");
		$filter->fondo2->options("SELECT fondo,fondo a FROM banc GROUP BY fondo UNION ALL SELECT CONCAT('REPO.',fondo),CONCAT('REPO.',fondo) a FROM banc GROUP BY fondo");$filter->destino2->style = "width:200px;";

		$filter->submit("btnsubmit","Buscar");
		$filter->build_form();

		$salida=anchor("tesoreria/relch","Atras");

		$data['content'] = $filter->output.$salida;
		$data['title']   = "Creacion de Relacion de Cheques";
		$data["head"]    = $this->rapyd->get_head();
		$this->load->view('view_ventanas', $data);
	}

	function selecciona($numero=''){
		$this->rapyd->load("datagrid","dataobject","fields");

		$fecha     =  $this->input->post('fecha'    );
		$fondo     =  $this->input->post('fondo'    );
		$fondo2    =  $this->input->post('fondo2'   );
		$destino   =  $this->input->post('destino'  );
		$destino2  =  $this->input->post('destino2'  );
		$usuario   =  $this->input->post('usuario'  );
		$fecha     = human_to_dbdate($fecha);

		if(!empty($numero)){
			$numeroe = $this->db->escape($numero);
			$row     = $this->datasis->damerow("SELECT fecha,destino,fondo FROM  relch WHERE id=$numero");

			$fecha   = $row['fecha'];
			$fondo   = $row['fondo'];
			//$destino = $row['destino'];
		}

		function asigna($id,$value="A"){
			$campo = new dropdownField("Title", "gt[$id]");
			$campo->status  = "modify";
			$campo->option("A","Agregar"     );
			$campo->option("P","Pendiente"    );
			$campo->value   = $value;
			$campo->style="width:100px";
			$campo->build();

			return $campo->output;
		}

		$tabla=form_open("tesoreria/relch/carga/$numero");

		$ddata = array(
		'fecha'   =>$fecha,
		'usuario' =>$usuario,
		'destino' =>$destino,
		'destino2'=>$destino2,
		'fondo2'  =>$fondo2,
		'fondo'   =>$fondo
		);

		$grid = new DataGrid("Lista de Cheque por Relacionar");

		$grid->db->select(array("a.id","a.cheque","b.banco","a.fecha","IF(a.tipo_doc='CH','Cheque',IF(a.tipo_doc='ND','Nota de Debito',a.tipo_doc)) tipo_doc","a.benefi","a.monto"));
		$grid->db->from('mbanc a');
		$grid->db->join('banc b','a.codbanc=b.codbanc');
		if(empty($numero))
		$grid->db->where("relch IS NULL"      );
		else
		$grid->db->where("(relch IS NULL OR relch=$numeroe)" );
		$grid->db->where("status in ('E2','J2','A2','NC')"   );
		$grid->db->where("tipo_doc in ('NC','CH','ND')");
		$grid->db->where("tipo_doc in ('NC','CH','ND')" );
		$grid->db->where("fecha "    ,$fecha  );
		if($destino=='C' || $destino=='I')
		$grid->db->where("destino "  ,$destino);
		if(!empty($fondo))
		$grid->db->where("fondo "    ,$fondo  );
		if(!empty($usuario))
		$grid->db->where("usuario "  ,$usuario);
		$grid->use_function('asigna');

		$grid->column("Banco"          ,"banco"                               ,'align=left'    );
		$grid->column("Tipo Documento" ,"tipo_doc"                            ,'align=left'    );
		$grid->column("Documento"      ,"cheque"                              ,'align=left'    );
		$grid->column("Fecha"          ,"fecha"                               ,'align=left'    );
		$grid->column("A nombre de"    ,"benefi"                              ,'align=left'    );
		$grid->column("Monto"          ,"<nformat><#monto#>|2|,|.</nformat>"  ,"align='right'" );
		$grid->column("Accio&oacute;n" ,"<asigna><#id#></asigna>"             ,"align='right'" );
		$grid->build();
		
		$tabla.=$salida = anchor("tesoreria/relch/busca/$numero",'Regresar');
		$tabla.=$grid->output.form_submit('mysubmit', 'Guardar').form_hidden($ddata);
		$tabla.=form_close();

		if($grid->recordCount==0){
			$tabla="<p>No hay cheques por relacionar</p>";
			$tabla.=$salida = anchor("tesoreria/relch/busca/$numero",'Regresar');
		}

		$data['content'] = $tabla;
		$data['title']   = " Creacion de Relacion de Ordenes Pagadas ";
		$data["head"]    = $this->rapyd->get_head();
		$this->load->view('view_ventanas', $data);
	}

	function carga($numero=''){
		$this->rapyd->load("dataobject");
		$fecha  = $this->input->post('fecha'  );
		$fecha2 =dbdate_to_human($fecha);
		$f      = explode('/',$fecha2);
		$fecha2 =str_replace('/','',$fecha2);

		$usuario = $this->input->post('usuario' );
		$destino = $this->input->post('destino' );
		$destino2= $this->input->post('destino2');
		$fondo   = $this->input->post('fondo'   );
		$fondo2  = $this->input->post('fondo2'  );

		$fondoe = $this->db->escape($fondo);
		$fechae = $this->db->escape($fecha);
		if(empty($numero)){
			$cant  = $this->datasis->dameval("SELECT COUNT(*) FROM relch WHERE fecha=$fechae AND fondo=$fondoe AND destino='$destino2'");
			$cant +=1;
			$relch = new DataObject("relch");
			$relch->set('fecha'  ,$fecha  );
			$relch->set('usuario',$usuario);
			$relch->set('fondo'  ,(empty($fondo2)?$fondo:$fondo2));
			$relch->set('destino',$destino2);
			$relch->set('numero' ,$destino2.'-'.(empty($fondo2)?$fondo:$fondo2).'-'.substr($f[2],2,2).$f[1].$f[0].'-'.$cant);
			$relch->set('status' ,'P');
			$relch->save();
			$insert_id = $relch->db->insert_id();
		}else{
			$insert_id=$numero;
		}

		//$relch->set('');
		$do = new DataObject("mbanc");

		$data=$this->input->post('gt');

		if(count($data)>0){
			foreach($data AS $id=>$value){
				$do->load($id);
				if($value=='A')
				$do->set('relch',$insert_id);
				else
				$do->set('relch',null);

				$do->save();
			}
			$salida="Se listaron Cheques Seleccionados";

		}else
			$salida="No hay regitro para listar";

		logusu('relch',$salida);

		$data['content'] = $salida.'<p>'.anchor('tesoreria/relch','Regresar').'</p>';
		$data['title']   = " Creacion de Relacion de Cheques ";
		$data["head"]    = $this->rapyd->get_head();
		$this->load->view('view_ventanas', $data);
	}

	function actualizar($id){
		$this->rapyd->load("dataobject");
		$do    = new DataObject("relch");
		$do->load($id);
		$status= $do->get('status');
		$id2   = $this->db->escape($id);
		if($status=='P'){
			$do->set('status','C');
			$do->save();
		}
		redirect($this->url."/dataedit/show/$id");
	}

	function anular($id){
		$this->rapyd->load("dataobject");
		$do    = new DataObject("relch");
		$do->load($id);
		$status= $do->get('status');
		$id2   = $this->db->escape($id);
		if($status=='C'){
			$this->db->query("UPDATE mbanc SET relch=NULL WHERE relch=$id2");
			$do->set('status','P');
			$do->save();
		}
		redirect($this->url."/dataedit/show/$id");
	}

	function instalar(){
		$query="ALTER TABLE `relch`  ADD COLUMN `fondo` VARCHAR(25) NULL DEFAULT NULL";
		$this->db->simple_query($query);
	}
}
?>
