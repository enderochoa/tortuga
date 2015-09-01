<?php require_once(BASEPATH.'application/controllers/validaciones.php');
 class seriales extends validaciones{
	
	function seriales(){
		parent::Controller(); 
		$this->load->library("rapyd");
		$this->datasis->modulo_id(308,1);
	
 }
 	 
    function index(){
    	redirect("inventario/seriales/filteredgrid");
    }

	function filteredgrid(){
		$this->rapyd->load("datafilter","datagrid");
		$this->rapyd->uri->keep_persistence();

		$filter = new DataFilter("Filtro de Seriales", 'seri');
		
		$filter->codigo = new inputField("C&oacute;digo", "codigo");
		$filter->codigo->size=20;
		
		$filter->serial = new inputField("Serial", "serial");
		$filter->serial->size=20;
		
		$filter->buttons("reset","search");
		$filter->build();

		$uri =   anchor('inventario/seriales/dataedit/show/<raencode><#codigo#></raencode>','<#codigo#>');
		$uri_2 = anchor('inventario/seriales/dataedit/create/<raencode><#codigo#></raencode>/<raencode><#serial#></raencode>','Duplicar');

		$grid = new DataGrid("Lista de Seriales");
		$grid->order_by("codigo","asc");
		$grid->per_page = 20;

		$grid->column("Código"      ,$uri);
		$grid->column("Serial"      ,"serial");
		$grid->column("Descripción" ,"descrip");
 		$grid->column("Fecha C."    ,"<dbdate_to_human><#fechac#></dbdate_to_human>","align='center'");
		$grid->column("Compra"      ,"compra");
		$grid->column("Beneficiario"   ,"proveed");
		$grid->column("Fecha V."    ,"<dbdate_to_human><#fechav#></dbdate_to_human>","align='center'");
		$grid->column("Venta"       ,"venta");
		$grid->column("Cliente"     ,"cliente");
		$grid->column("Duplicar"    ,$uri_2                                         ,"align='center'");
										
		$grid->add("inventario/seriales/dataedit/create");
		$grid->build();
		
		$data['content'] = $filter->output.$grid->output;
		$data['title']   = " Seriales ";
		$data["head"]    = $this->rapyd->get_head();
		$this->load->view('view_ventanas', $data);	

	}

	function dataedit($status='',$id='',$id2='')
 	{
		$this->rapyd->load("dataobject","dataedit");
		
		$id=radecode($id);
		$id2=radecode($id2);
		$link =site_url('inventario/seriales/ultimo');
		$link2=site_url('inventario/seriales/sugerir');

		$script ='
		$(function() {
			$(".inputnum").numeric(".");
		});
		
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
			'tabla'   =>'sprv',
			'columnas'=>array(
			'proveed' =>'Código Beneficiario',
			'nombre'=>'Nombre',
			'rif'=>'RIF'),
			'filtro'  =>array('proveed'=>'Código Beneficiario','nombre'=>'Nombre'),
			'retornar'=>array('proveed'=>'proveed'),
			'titulo'  =>'Buscar Beneficiario');
		
		$mSCLId=array(
			'tabla'   =>'scli',
			'columnas'=>array(
			'cliente' =>'Código Cliente',
			'nombre'=>'Nombre',
			'contacto'=>'Contacto'),
			'filtro'  =>array('cliente'=>'Código Cliente','nombre'=>'Nombre'),
			'retornar'=>array('cliente'=>'clienten'),
			'titulo'  =>'Buscar Cliente');
			
		$mSINV=array(
			'tabla'   =>'sinv',
			'columnas'=>array(
			'codigo' =>'Código',
			'grupo'=>'Grupo',
			'descrip'=>'Descripción'),
			'filtro'  =>array('codigo'=>'Código','grupo'=>'Grupo','descrip'=>'Descripción'),
			'retornar'=>array('cliente'=>'clienten'),
			'titulo'  =>'Buscar Cliente');
		
		$bsclid =$this->datasis->modbus($mSCLId);
		$boton  =$this->datasis->modbus($modbus);
		
		$do = new DataObject("seri");
		if(($status=="create") && (!empty($id)) && (!empty($id2))){
			$do->load($id);
			$do->load_where("serial", $id2);
		}
		
		$edit = new DataEdit("Seriales", $do);
		$edit->back_url = site_url("inventario/seriales/filteredgrid");
		
		$edit->script($script, "create");
		$edit->script($script, "modify");
		
		$edit->post_process('insert','_post_insert');
		$edit->post_process('update','_post_update');
		$edit->post_process('delete','_post_delete');
		
		$edit->codigo = new inputField("Código", "codigo");
		$edit->codigo->mode="autohide";
		$edit->codigo->size=20;
		$edit->codigo->maxlength=15;
		$edit->codigo->rule ="required|callback_chexiste|trim";
		
		$edit->serial = new inputField("Serial", "serial");
		$edit->serial->size=40;
		$edit->serial->maxlength=35;
		$edit->serial->rule ="required";
		
		$edit->descrip = new inputField("Descripción", "descrip");
		$edit->descrip->size =50;
		$edit->descrip->maxlength=40;               
		$edit->descrip->rule ="strtoupper|trim";
		$edit->descrip->when = array("show");
    
		$edit->modelo  =new inputField("Modelo", "modelo");
		$edit->modelo->size = 25;
		$edit->modelo->maxlength=20;
		$edit->modelo->rule ="strtoupper|trim";
		$edit->modelo->when = array("show");

		$edit->marca =  new inputField("Marca", "marca");
		$edit->marca->size = 23;
		$edit->marca->maxlength=20;
		$edit->marca->rule ="strtoupper|trim";
		$edit->marca->when = array("show");
		
		$edit->clave = new inputField("Clave", "clave");
		$edit->clave->size =12;
    $edit->clave->maxlength=8;
    $edit->clave->rule ="strtoupper|trim";
    $edit->clave->when = array("show");
    
    $edit->unidad =new inputField("Unidad","unidad");
    $edit->unidad->size =12;
    $edit->unidad->maxlength=8;
    $edit->unidad->rule ="strtoupper|trim";
    $edit->unidad->when = array("show");
    
    $edit->fechac = new DateField("Fecha de Compra", "fechac");
    $edit->fechac->size = 12;
		
    $edit->compra =new inputField("Compra", "compra");
		$edit->compra->size = 10;
		$edit->compra->maxlength=8;
		$edit->compra->rule ="strtoupper|trim";
		
		$edit->proveed = new inputField("Beneficiario", "proveed");
		$edit->proveed->size =8;
    $edit->proveed->maxlength=5;
    $edit->proveed->rule ="strtoupper|trim";
    $edit->proveed->append($boton);
    
    $edit->fechav =  new DateField("Fecha de Venta","fechav");
    $edit->fechav->size = 12;  

    $edit->venta = new inputField("Venta","venta");
    $edit->venta->size = 10;
    $edit->venta->maxlength=8;
    $edit->venta->rule ="strtoupper|trim";
    
    $edit->cliente =  new inputField("Cliente","cliente");
    $edit->cliente->size =8;   
    $edit->cliente->maxlength=5;
    $edit->cliente->rule ="strtoupper|trim";
    $edit->cliente->append($bsclid);
    
		$edit->buttons("modify", "save", "undo", "delete", "back");
		$edit->build();
 
		$data['content'] = $edit->output;           
    $data['title']   = " Seriales ";        
    $data["head"]    = script("jquery.pack.js").script("plugins/jquery.numeric.pack.js").script("plugins/jquery.floatnumber.js").$this->rapyd->get_head();
    $this->load->view('view_ventanas', $data);  
	}
	function _post_insert($do){
		$codigo=$do->get('codigo');
		$nombre=$do->get('serial');
		logusu('seri',"SERIAL $codigo $nombre CREADO");
	}
	function _post_update($do){
		$codigo=$do->get('codigo');
		$nombre=$do->get('serial');
		logusu('seri',"SERIAL $codigo $nombre  MODIFICADO");
	}
	function _post_delete($do){
		$codigo=$do->get('codigo');
		$nombre=$do->get('serial');
		logusu('seri',"SERIAL $codigo $nombre  ELIMINADO ");
	}
	function chexiste($codigo){
		$codigo=$this->input->post('codigo');
		$chek=$this->datasis->dameval("SELECT COUNT(*) FROM seri WHERE codigo='$codigo'");
		if ($chek > 0){
			$serial=$this->datasis->dameval("SELECT serial FROM seri WHERE codigo='$codigo'");
			$this->validation->set_message('chexiste',"El codigo $codigo ya existe para el serial $serial");
			return FALSE;
		}else {
  		return TRUE;
		}	
	}
}
?>