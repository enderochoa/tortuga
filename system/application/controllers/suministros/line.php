<?php require_once(BASEPATH.'application/controllers/validaciones.php');
//lineasinventario
 class Line extends validaciones {
	
	function line(){
		parent::Controller(); 
		$this->load->library("rapyd");
	  //$this->datasis->modulo_id(306,1);
	}
    function index(){
    	
    	redirect("suministros/line/filteredgrid");
    }
 
	function filteredgrid(){
		$this->rapyd->load("datafilter","datagrid");
		$this->rapyd->uri->keep_persistence();
		
		$filter = new DataFilter("Filtro por Linea");
		
		$filter->db->select("linea, a.descrip AS descrip, b.descrip AS depto, a.cu_inve AS cu_inve, a.cu_venta AS cu_venta, a.cu_cost AS cu_cost, a.cu_devo AS cu_devo");
		$filter->db->from("line AS a");
		$filter->db->join("dpto AS b","a.depto=b.depto");
		
		$filter->linea = new inputField("C&oacute;digo L&iacute;nea", "linea");
		$filter->linea->size=20;
		
		$filter->nombre = new inputField("Descripci&oacute;n","descrip");
		$filter->nombre->size=20;
		
		$filter->depto = new inputField("Departamento","b.descrip");
		$filter->depto->size=20;
		
		$filter->buttons("reset","search");
		$filter->build();

		$uri = anchor('suministros/line/dataedit/show/<raencode><#linea#></raencode>','<#linea#>');
		$uri_2 = anchor('suministros/line/dataedit/create/<raencode><#linea#></raencode>','Duplicar');

		$grid = new DataGrid("Lista de Lineas de Inventario");
		$grid->order_by("linea","asc");
		$grid->per_page = 20;

		$grid->column_orderby("C&oacute;digo Linea"       ,$uri       ,"codigo"  ,"align='center'");
		$grid->column_orderby("Descripci&oacute;n"               ,"descrip"  ,"descrip" ,"align='left'");
		$grid->column_orderby("Departamento"              ,"depto"    ,"depto"   ,"align='left'");
//		$grid->column_orderby("Cuenta Costo"              ,"cu_cost"  ,"cu_cost" ,"align='center'");
//		$grid->column_orderby("Cuenta Inventario"         ,"cu_inve"  ,"cu_inve" ,"align='center'");
//		$grid->column_orderby("Cuenta Venta"              ,"cu_venta" ,"cu_venta","align='center'");
//		$grid->column_orderby("Cuenta Devoluci&oacute;n"  ,"cu_devo"  ,"cu_devo" ,"align='center'");
		$grid->column("Duplicar"                  ,$uri_2     ,"align='center'");


		$grid->add("suministros/line/dataedit/create");
		$grid->build();
		
		$data['content'] = $filter->output.$grid->output;
		$data['title']   = "Lineas de Inventario";
		$data["head"]    = $this->rapyd->get_head();
		$this->load->view('view_ventanas', $data);	
	}
	
	function dataedit($status='',$id='')
 	{
		$this->rapyd->load("dataobject","dataedit");

		$qformato=$this->qformato=$this->datasis->formato_cpla();
		$link=site_url('suministros/line/ultimo');
		$link2=site_url('inventario/common/sugerir_line');

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
							$("#linea").val(msg);
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
		
		$mdepto=array(
			'tabla'   =>'dept',
			'columnas'=>array(
			'codigo' =>'C&oacute;odigo',
			'departam'=>'Nombre'),
			'filtro'  =>array('codigo'=>'C&oacute;digo','nombre'=>'Nombre'),
			'retornar'=>array('codigo'=>'depto'),
			'titulo'  =>'Buscar Departamento');
			
		$boton=$this->datasis->modbus($mdepto);
		
		$do = new DataObject("line");
		if($status=="create" && !empty($id)){
			$do->load($id);
			$do->set('linea', '');
		}

		$edit = new DataEdit("Linea de Inventario", $do);
		$edit->back_url = site_url("suministros/line/filteredgrid");
		$edit->script($script, "create");
		$edit->script($script, "modify");
		
		$edit->pre_process('delete','_pre_del');
		$edit->post_process('insert','_post_insert');
		$edit->post_process('update','_post_update');
		$edit->post_process('delete','_post_delete');
		
		$edit->dpto = new dropdownField("Departamento", "depto");
		$edit->dpto->option("","");
		$edit->dpto->options("SELECT depto, descrip FROM dpto WHERE tipo='I' ORDER BY depto");
		$edit->dpto->rule ="required";
		$edit->dpto->style='width:250px;';
		
		$ultimo='<a href="javascript:ultimo();" title="Consultar ultimo c&oacute;digo ingresado"> Consultar ultimo c&oacute;digo</a>';
		$sugerir='<a href="javascript:sugerir();" title="Sugerir un C&oacute;digo aleatorio">Sugerir C&oacute;digo </a>';
		$edit->linea =  new inputField("C&oacute;digo Linea", "linea");
		$edit->linea->mode="autohide";
		$edit->linea->size =4;
		$edit->linea->rule ="trim|strtoupper|required|callback_chexiste";
		$edit->linea->maxlength=2;
		$edit->linea->append($sugerir);
		$edit->linea->append($ultimo);
				
		$edit->descrip =  new inputField("Descripci&oacute;n", "descrip");
		$edit->descrip->size = 35;
		$edit->descrip->rule= "trim|strtoupper|required";
		$edit->descrip->maxlength=30;
		
//		$edit->cu_inve =new inputField("Cuenta Inventario", "cu_inve");
//		$edit->cu_inve->size = 18;
//		$edit->cu_inve->maxlength=15;
//		$edit->cu_inve->rule ="trim|callback_chcuentac";
//		$edit->cu_inve->append($bcu_inve);
//		
//		$edit->cu_cost =new inputField("Cuenta Costo", "cu_cost");
//		$edit->cu_cost->size = 18;
//		$edit->cu_cost->maxlength=15;
//		$edit->cu_cost->rule ="trim|callback_chcuentac";
//		$edit->cu_cost->append($bcu_cost);
//		
//		$edit->cu_venta  =new inputField("Cuenta Venta", "cu_venta");
//		$edit->cu_venta->size =18;
//		$edit->cu_venta->maxlength=15;
//		$edit->cu_venta->rule ="trim|callback_chcuentac";
//		$edit->cu_venta->append($bcu_venta);
//		
//		$edit->cu_devo = new inputField("Cuenta Devoluci&oacute;n","cu_devo");
//		$edit->cu_devo->size = 18;
//		$edit->cu_devo->maxlength=15;
//		$edit->cu_devo->rule ="trim|callback_chcuentac";
//		$edit->cu_devo->append($bcu_devo);
    	 		   	
		$edit->buttons("add","modify", "save", "undo", "delete", "back");
		$edit->build();
 
		$data['content'] = $edit->output;           
		$data['title']   = "Lineas de Inventario";        
		$data["head"]    = script("jquery.pack.js").$this->rapyd->get_head();//.script("plugins/jquery.numeric.pack.js").script("plugins/jquery.floatnumber.js").
		$this->load->view('view_ventanas', $data);  
	}
	function _post_insert($do){
		$codigo=$do->get('linea');
		$nombre=$do->get('descrip');
		logusu('line',"LINEA DE INVENTARIO $codigo NOMBRE  $nombre CREADO");
	}
	function _post_update($do){
		$codigo=$do->get('linea');
		$nombre=$do->get('descrip');
		logusu('line',"LINEA DE INVENTARIO $codigo NOMBRE  $nombre  MODIFICADO");
	}
	function _post_delete($do){
		$codigo=$do->get('linea');
		$nombre=$do->get('descrip');
		logusu('line',"LINEA DE INVENTARIO $codigo NOMBRE  $nombre  ELIMINADO ");
	}
	
	function chexiste($codigo){
		$codigo=$this->input->post('linea');
		$chek=$this->datasis->dameval("SELECT COUNT(*) FROM line WHERE linea='$codigo'");
		if ($chek > 0){
			$linea=$this->datasis->dameval("SELECT descrip FROM line WHERE linea='$codigo'");
			$this->validation->set_message('chexiste',"El codigo $codigo ya existe para la linea $linea");
			return FALSE;
		}else {
  		return TRUE;
		}	
	}
	
	function _pre_del($do) {
		$codigo=$do->get('line');
		$chek =  $this->datasis->dameval("SELECT COUNT(*) FROM grup WHERE linea='$codigo'");
		if ($chek > 0){
			$do->error_message_ar['pre_del'] = $do->error_message_ar['delete']='La l&iacute;nea contiene grupos, por ello no puede ser eliminada. Elimine primero todos los grupos que pertenezcan a esta l&iacute;nea';
			return False;
		}
		return True;
	}
	
	function ultimo(){
		$ultimo=$this->datasis->dameval("SELECT linea FROM line ORDER BY linea DESC");
		echo $ultimo;
	}
}
?>