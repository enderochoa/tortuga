<?php require_once(BASEPATH.'application/controllers/validaciones.php');
class dpto extends validaciones{
	 
	function Dpto(){
		parent::Controller(); 
		$this->load->library("rapyd");
	 // $this->datasis->modulo_id(309,1);
	}
    function index(){
    	$this->db->simple_query("INSERT INTO dpto (depto,tipo,descrip) VALUES ('99','G','INVERSION EN ACTIVOS')ON DUPLICATE KEY UPDATE depto='99', tipo='G',descrip='INVERSION EN ACTIVOS'");
			$this->db->simple_query("INSERT INTO dpto (depto,tipo,descrip) VALUES ('98','G','GASTOS FINANCIEROS')ON DUPLICATE KEY UPDATE depto='98', tipo='G',descrip='GASTOS FINANCIEROS'");
			$this->db->simple_query("INSERT INTO dpto (depto,tipo,descrip) VALUES ('97','G','GASTOS DE ADMINISTRACION')ON DUPLICATE KEY UPDATE depto='97', tipo='G',descrip='GASTOS DE ADMINISTRACION'");
			$this->db->simple_query("INSERT INTO dpto (depto,tipo,descrip) VALUES ('96','G','GASTOS DE VENTA')ON DUPLICATE KEY UPDATE depto='96', tipo='G',descrip='GASTOS DE VENTA'");
			$this->db->simple_query("INSERT INTO dpto (depto,tipo,descrip) VALUES ('95','G','GASTOS DE COMPRA')ON DUPLICATE KEY UPDATE depto='95', tipo='G',descrip='GASTOS DE COMPRA'");
    	redirect("suministros/dpto/filteredgrid");
    }
 
	function filteredgrid(){
		$this->rapyd->load("datafilter","datagrid");
		$this->rapyd->uri->keep_persistence();

		$filter = new DataFilter("Filtro por Departamento");
		
		$filter->db->select("tipo,depto,descrip,cu_venta,cu_inve,cu_devo,cu_cost");
		$filter->db->from("dpto");
		$filter->db->where("tipo = 'I'");
		
		$filter->depto = new inputField("C&oacute;digo Departamento", "depto");
		$filter->depto->size=20;
		
		$filter->descrip = new inputField("Descripci&oacute;n", "descrip");
		$filter->descrip->size=20;
    
		$filter->buttons("reset","search");
		$filter->build();

		$uri = anchor('suministros/dpto/dataedit/show/<raencode><#depto#></raencode>','<#depto#>');
		$uri_2 = anchor('suministros/dpto/dataedit/create/<raencode><#depto#></raencode>','Duplicar');

		$grid = new DataGrid("Lista de Departamentos");
		$grid->order_by("depto","asc");
		$grid->per_page = 20;

		$grid->column_orderby("C&oacute;digo Departamento",$uri      ,"depto"  ,"align='center'");
		$grid->column_orderby("Descripci&oacute;n"        ,"descrip" ,"descrip","align='left'");
//		$grid->column("Cuenta Venta"                ,"cu_venta","align='center'");
//		$grid->column("Cuenta Inventario"           ,"cu_inve" ,"align='center'");
//		$grid->column("Cuenta Costo"                ,"cu_cost" ,"align='center'");
//		$grid->column("Cuenta Devoluci&oacute;n"    ,"cu_devo" ,"align='center'");
		$grid->column("Duplicar"                    ,$uri_2    ,"align='center'");

		$grid->add("suministros/dpto/dataedit/create");
		$grid->build();
	
	  $data['content'] = $filter->output.$grid->output;
		$data['title']   = " Departamentos ";
		$data["head"]    = $this->rapyd->get_head();
		$this->load->view('view_ventanas', $data);	
	}
	
	function dataedit($status='',$id=''){
		$this->rapyd->load("dataobject","dataedit");

		$qformato=$this->qformato=$this->datasis->formato_cpla();
		$link=site_url('suministros/dpto/ultimo');
		$link2=site_url('inventario/common/sugerir_dpto');
		
		$script='
		function ultimo(){
			$.ajax({
				url: "'.$link.'",
				success: function(msg){
				  alert( "El ultimo codigo ingresado fue: " + msg );
				}
			});
		}
		
		function sugerir(){
			$.ajax({
					url: "'.$link2.'",
					success: function(msg){
						if(msg){
							$("#depto").val(msg);
						}
						else{
							alert("No es posible generar otra sugerencia. Coloque el c&oacute;digo manualmente");
						}
					}
				});
		}		
		';
		
		$modbus=array(
			'tabla'   =>'cpla',
			'columnas'=>array(
				'codigo' =>'C&oacute;digo',
				'denominacion'=>'Descripci&oacute;n'),
			'filtro'  =>array('codigo'=>'C&oacute;digo','descrip'=>'Descripci&oacute;n'),
			'retornar'=>array('codigo'=>'<#i#>'),
			'titulo'  =>'Buscar Cuenta',
			'where'=>"codigo LIKE \"$qformato\"",
			'p_uri'=>array(4=>'<#i#>')
		);

		$bcu_venta = $this->datasis->p_modbus($modbus,'cu_venta');
		$bcu_inve  = $this->datasis->p_modbus($modbus,'cu_inve' );
		$bcu_cost  = $this->datasis->p_modbus($modbus,'cu_cost' );
		$bcu_devo  = $this->datasis->p_modbus($modbus,'cu_devo' );
		
		$do = new DataObject("dpto");
		$do->set('tipo', 'I');
		if($status=="create" && !empty($id)){
			$do->load($id);
			$do->set('depto', '');
		}
		
		$edit = new DataEdit("Departamento", $do);
		$edit->back_url = site_url("suministros/dpto/filteredgrid");
		$edit->script($script, "create");
		$edit->script($script, "modify");
		
		$edit->post_process('insert','_post_insert');
		$edit->post_process('update','_post_update');
		$edit->post_process('delete','_post_delete');
		$edit->pre_process('delete','_pre_del');
		
		$ultimo='<a href="javascript:ultimo();" title="Consultar ultimo c&oacute;digo ingresado"> Consultar ultimo c&oacute;digo</a>';
		$sugerir='<a href="javascript:sugerir();" title="Sugerir un C&oacute;digo aleatorio">Sugerir C&oacute;digo </a>';
		$edit->depto = new inputField("C&oacute;digo Departamento", "depto");
		$edit->depto->mode="autohide";
		$edit->depto->size=5;
		$edit->depto->maxlength=2;
		$edit->depto->rule ="strtoupper|required|callback_chexiste|trim";
		$edit->depto->append($sugerir);
		$edit->depto->append($ultimo);

		$edit->descrip = new inputField("Descripci&oacute;n", "descrip");
		$edit->descrip->size =35;
		$edit->descrip->maxlength=30;
		$edit->descrip->rule ="required|strtoupper|trim";
		
		//$edit->tipo = new dropdownField("Tipo","tipo");
	  //$edit->tipo->style='width:140px;';
		//$edit->tipo->option("I","Inventario" );
		//$edit->tipo->option("G","Gasto"  );
		
//		$edit->cu_inve =new inputField("Cuenta Inventario", "cu_inve");
//		$edit->cu_inve->size = 18;
//		$edit->cu_inve->maxlength=15;
//		$edit->cu_inve->rule ="trim|callback_chcuentac";
//		$edit->cu_inve->append($bcu_inve);
		
//		$edit->cu_cost =new inputField("Cuenta Costo", "cu_cost");
//		$edit->cu_cost->size = 18;
//		$edit->cu_cost->maxlength=15;
//		$edit->cu_cost->rule ="trim|callback_chcuentac";
//		$edit->cu_cost->append($bcu_cost);
		
//		$edit->cu_venta  =new inputField("Cuenta Venta", "cu_venta");
//		$edit->cu_venta->size =18;
//		$edit->cu_venta->maxlength=15;
//		$edit->cu_venta->rule ="trim|callback_chcuentac";
//		$edit->cu_venta->append($bcu_venta);
		
//		$edit->cu_devo = new inputField("Cuenta Devoluci&oacute;n","cu_devo");
//		$edit->cu_devo->size = 18;
//		$edit->cu_devo->maxlength=15;
//		$edit->cu_devo->rule ="trim|callback_chcuentac";
//		$edit->cu_devo->append($bcu_devo);
    
		$edit->buttons("add","modify","delete", "save", "undo", "back");
		$edit->build();
 
		$data['content'] = $edit->output;           
		$data['title']   = " Departamentos ";        
		$data["head"]    = script("jquery.pack.js").$this->rapyd->get_head();//script("plugins/jquery.numeric.pack.js").script("plugins/jquery.floatnumber.js").
		$this->load->view('view_ventanas', $data);  
	}
	
	function _post_insert($do){
		$codigo=$do->get('depto');
		$nombre=$do->get('descrip');
		logusu('dpto',"DEPARTAMENTO $codigo NOMBRE  $nombre CREADO");
	}
	function _post_update($do){
		$codigo=$do->get('depto');
		$nombre=$do->get('descrip');
		logusu('dpto',"DEPARTAMENTO $codigo NOMBRE  $nombre  MODIFICADO");
	}
	function _post_delete($do){
		$codigo=$do->get('depto');
		$nombre=$do->get('descrip');
		logusu('dpto',"DEPARTAMENTO $codigo NOMBRE  $nombre  ELIMINADO ");
	}
	function chexiste($codigo){
		$codigo=$this->input->post('depto');
		$chek=$this->datasis->dameval("SELECT COUNT(*) FROM dpto WHERE depto='$codigo'");
		if ($chek > 0){
			$depto=$this->datasis->dameval("SELECT descrip FROM dpto WHERE depto='$codigo'");
			$this->validation->set_message('chexiste',"El codigo $codigo ya existe para el departamento $depto");
			return FALSE;
		}else {
  		return TRUE;
		}	
	}
	
	function _pre_del($do) {
		$codigo=$do->get('depto');
		$chek =  $this->datasis->dameval("SELECT COUNT(*) FROM line WHERE depto='$codigo'");
		if ($chek > 0){
			$do->error_message_ar['pre_del'] = $do->error_message_ar['delete']='El departamento contiene lineas, por ello no puede ser eliminado. Elimine primero todas las l&iacute;neas que pertenezcan a este departamento';
			return False;
		}
		return True;
	}

	function ultimo(){
		$ultimo=$this->datasis->dameval("SELECT depto FROM dpto WHERE depto<95 ORDER BY depto DESC");
		echo $ultimo;
	}
}
?>
