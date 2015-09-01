<?php
require_once(BASEPATH.'application/controllers/validaciones.php');
//almacenes
class Caub extends validaciones {
	 
	var $data_type = null;
	var $data = null;
	
	function caub(){
		parent::Controller(); 

		$this->load->helper('url');
		$this->load->helper('text');
		$this->datasis->modulo_id(307,1);

		$this->load->library("rapyd");
		define ("THISFILE",   APPPATH."controllers/nomina". $this->uri->segment(2).EXT);
   }
 
    function index(){
    	$this->datasis->modulo_id(307,1);
    	$ajus=$this->db->simple_query("INSERT INTO caub (ubica,ubides,gasto,invfis) VALUES ('AJUS','AJUSTES','S','N')ON DUPLICATE KEY UPDATE ubides='AJUSTES', gasto='S',invfis='N'");
    	$infi=$this->db->simple_query("INSERT INTO caub (ubica,ubides,gasto,invfis) VALUES ('INFI','INVENTARIO FISICO','S','S')ON DUPLICATE KEY UPDATE ubides='INVENTARIO FISICO', gasto='S',invfis='S'");
    	redirect("inventario/caub/filteredgrid");
    }
  
	function filteredgrid(){
		$this->rapyd->load("datafilter","datagrid");
		$this->rapyd->uri->keep_persistence();

		$filter = new DataFilter("Filtro por Ubicación");

		$filter->db->select("ubica, ubides,gasto,a.cu_caja AS cu_caja, a.cu_cost AS cu_cost,invfis,a.sucursal as codigo");//b.sucursal AS sucursal,
		$filter->db->from("caub AS a");
		//$filter->db->join("sucu AS b","a.sucursal=b.codigo","LEFT");

		$filter->ubica = new inputField("Ubicaci&oacute;n", "ubica");
		$filter->ubica->size=20;
		
		$filter->descrip = new inputField("Descripci&oacute;n", "ubides");
		$filter->descrip->size=20;
		
		$filter->gasto=new dropdownField("Gasto","gasto");
		$filter->gasto->option("","");
		$filter->gasto->option("S","Si");
		$filter->gasto->option("N","No");
		$filter->gasto->style='width:150px;';

		$filter->invfis=new dropdownField("Inventario F&iacute;sico","invfis");
		$filter->invfis->option("","");
		$filter->invfis->option("S","Si");
		$filter->invfis->option("N","No");
		$filter->invfis->style='width:150px;';
		
		//$filter->sucursal = new dropdownField("Sucursal","codigo");
		//$filter->sucursal->option("","");
		//$filter->sucursal->options("SELECT codigo, sucursal FROM sucu ORDER BY sucursal");
		//$filter->sucursal->style='width:150px;';
				
		$filter->buttons("reset","search");
		$filter->build();
		
		function si_no($valor){
			if($valor=='S'){
				return 'Si';
			}elseif($valor=='N'){
				return 'No';
			}	
		}

		$uri = anchor('inventario/caub/dataedit/show/<#ubica#>','<#ubica#>');
		$uri_2 = anchor('inventario/departamentos/dataedit/create/<raencode><#ubica#></raencode>','Duplicar');

		$grid = new DataGrid("Lista de Almacenes");
		$grid->order_by("ubica","asc");
		$grid->per_page = 20;
		$grid->use_function('si_no');

		$grid->column("Ubicación"            ,$uri                          ,"align='center'");
		$grid->column("Descripción"          ,"ubides"                      ,"align='left'");
		$grid->column("Gasto"                ,"<si_no><#gasto#></si_no>"    ,"align='center'");
		$grid->column("Cuenta Almac&eacute;n","cu_cost"                     ,"align='center'");
		$grid->column("Cuenta Caja"          ,"cu_caja"                     ,"align='center'");
		$grid->column("Inventario Físico"    , "<si_no><#invfis#></si_no>"  ,"align='center'");
		$grid->column("Sucursal"             ,"sucursal"                    ,"align='left'");
		$grid->column("Duplicar"             ,$uri_2                        ,"align='center'");
								
		$grid->add("inventario/caub/dataedit/create");
		$grid->build();
		
    $data['content'] = $filter->output.$grid->output;
		$data['title']   = " Almacenes ";
		$data["head"]    = $this->rapyd->get_head();
		$this->load->view('view_ventanas', $data);	
	}

	function dataedit($status='',$id='')
 	{
		$this->rapyd->load("dataobject","dataedit");
		
		$qformato=$this->qformato=$this->datasis->formato_cpla();
		$link=site_url('inventario/caub/ultimo');
		$link2=site_url('inventario/caub/sugerir');
		
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
							$("#ubica").val(msg);
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
				'descrip'=>'Descripci&oacute;n'),
			'filtro'  =>array('codigo'=>'C&oacute;digo','descrip'=>'Descripci&oacute;n'),
			'retornar'=>array('codigo'=>'<#i#>'),
			'titulo'  =>'Buscar Cuenta',
			'where'=>"codigo LIKE \"$qformato\"",
			'p_uri'=>array(4=>'<#i#>')
		);
		
		$bcu_cost  =$this->datasis->modbus($modbus ,'cu_cost');
		$bcu_caja  =$this->datasis->modbus($modbus ,'cu_caja');
		
		$do = new DataObject("caub");
		if($status=="create" && !empty($id)){
			$do->load($id);
			$do->set('ubica', '');
		}

		$edit = new DataEdit("Almacenes", $do);
		$edit->back_url = site_url("inventario/caub/filteredgrid");
		$edit->script($script,"create");
		$edit->script($script,"modify");

		$edit->post_process('insert','_post_insert');
		$edit->post_process('update','_post_update');
		$edit->post_process('delete','_post_delete');
		$edit->pre_process('delete','_pre_del');
		
		$ultimo='<a href="javascript:ultimo();" title="Consultar ultimo c&oacute;digo ingresado"> Consultar ultimo c&oacute;digo </a>';
		$sugerir='<a href="javascript:sugerir();" title="Sugerir un C&oacute;digo aleatorio">Sugerir C&oacute;digo </a>';
		$edit->ubica = new inputField("Almacen", "ubica");
		$edit->ubica->mode="autohide";
		$edit->ubica->size = 6;
		$edit->ubica->maxlength=4;
		$edit->ubica->rule ="required|callback_chexiste|trim";
		$edit->ubica->append($sugerir);
		$edit->ubica->append($ultimo);
		
		$edit->ubides = new inputField("Nombre", "ubides");
		$edit->ubides->size =35;
		$edit->ubides->maxlength=30;
		$edit->ubides->rule= "strtoupper|required|trim";
		
		$edit->gasto = new dropdownField("Gasto", "gasto");
		$edit->gasto->option("N","No");
		$edit->gasto->option("S","Si");		
		$edit->gasto->style='width:60px';
		
		$edit->invfis=new dropdownField("Inventario Físico", "invfis");
		$edit->invfis->option("N","No");
		$edit->invfis->option("S","Si");		
		$edit->invfis->style='width:60px';
		
		//$edit->sucursal = new dropdownField("Sucursal","sucursal");
		//$edit->sucursal->option("","");
		//$edit->sucursal->options("SELECT codigo, sucursal FROM sucu ORDER BY sucursal");
		//$edit->sucursal->style='width:135px;';
		
		//$edit->cu_cost=new inputField("Cuenta Almacen", "cu_cost");
		//$edit->cu_cost->size = 18;
		//$edit->cu_cost->maxlength=15;
		//$edit->cu_cost->rule="callback_chcuentac|trim";
		//$edit->cu_cost->append($bcu_cost);
		//
		//$edit->cu_caja =new inputField("Cuenta Caja", "cu_caja");
		//$edit->cu_caja->size = 18;
		//$edit->cu_caja->maxlength=15;
		//$edit->cu_caja->rule="callback_chcuentac|trim";
		//$edit->cu_caja->append($bcu_caja);
    
    //$edit->sucursal=new inputField("Sucursal","sucursal");
    //$edit->sucursal->size =4;
    //$edit->sucursal->maxlength=2;
    //$edit->sucursal->rule="trim";
    
		$edit->buttons("modify", "save", "undo", "delete", "back");
		$edit->build();
 
		$data['content'] = $edit->output;
    $data['title']   = " Almacenes ";
    $data["head"]    = script("jquery.pack.js").$this->rapyd->get_head();
    $this->load->view('view_ventanas', $data);
	}
	function _post_insert($do){
		$codigo=$do->get('ubica');
		$nombre=$do->get('ubides');
		logusu('caub',"ALMACEN  $codigo NOMBRE  $nombre CREADO");
	}
	function _post_update($do){
		$codigo=$do->get('ubica');
		$nombre=$do->get('ubides');
		logusu('caub',"ALMACEN $codigo NOMBRE  $nombre  MODIFICADO");
	}
	function _post_delete($do){
		$codigo=$do->get('ubica');
		$nombre=$do->get('ubides');
		logusu('caub',"ALMACEN $codigo NOMBRE  $nombre  ELIMINADO ");
	}
	function chexiste($codigo){
		$codigo=$this->input->post('ubica');
		$chek=$this->datasis->dameval("SELECT COUNT(*) FROM caub WHERE ubica='$codigo'");
		if ($chek > 0){
			$almacen=$this->datasis->dameval("SELECT ubides FROM caub WHERE ubica='$codigo'");
			$this->validation->set_message('chexiste',"El codigo $codigo ya existe para el almacen $almacen");
			return FALSE;
		}else {
  		return TRUE;
		}	
	}
	function ultimo(){
		$ultimo=$this->datasis->dameval("SELECT ubica FROM caub WHERE ubica!='AJUS' AND ubica!='INFI' ORDER BY ubica DESC");
		echo $ultimo;
	}
	
	function _pre_del($do) {
		$codigo=$do->get('ubica');
		$chek =  $this->datasis->dameval("SELECT COUNT(*) FROM itsinv WHERE alma='$codigo' AND existen>0");
		if ($chek > 0){
			$do->error_message_ar['pre_del'] = $do->error_message_ar['delete']='El almac&eacute;n contiene pruductos, por ello no puede ser eliminado. Transfiera primero todos los productos de este almac&eacute;n a otro';
			return False;
		}
		return True;
	}
	
	function sugerir(){
		$ultimo=$this->datasis->dameval("SELECT LPAD(hexa,2,0) FROM serie LEFT JOIN caub ON LPAD(ubica,2,0)=LPAD(hexa,2,0) WHERE valor<255 AND ubica IS NULL LIMIT 1");
		echo $ultimo;
	}
}
?>