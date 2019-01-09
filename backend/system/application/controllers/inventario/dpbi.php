<?php require_once(BASEPATH.'application/controllers/validaciones.php');
class Dpbi extends validaciones{
	 
	var $url = "inventario/dpbi/";
	var $titp= "Departamentos de Bienes";
	var $tits= "Departamento de Bienes";
	 
	function Dpbi(){
		parent::Controller(); 
		$this->load->library("rapyd");
	 // $this->datasis->modulo_id(309,1);
	}
    function index(){
    	redirect($this->url."filteredgrid");
    }
 
	function filteredgrid(){
		$this->rapyd->load("datafilter","datagrid");
		$this->rapyd->uri->keep_persistence();

		$filter = new DataFilter("Filtro de Departamentos","dpbi");
		
		$filter->codigo = new inputField("C&oacute;digo Departamento", "codigo");
		$filter->codigo->size=20;
		
		$filter->nombre = new inputField("Nombre Departamento", "nombre");
		$filter->nombre->size=20;
    
		$filter->buttons("reset","search");
		$filter->build();

		$uri = anchor($this->url.'dataedit/show/<#id#>','<#codigo#>');
		$uri_2 = anchor($this->url.'dataedit/create/<#id#>','Duplicar');

		$grid = new DataGrid("Lista de Departamentos");
		$grid->order_by("codigo","asc");
		$grid->per_page = 20;

		$grid->column("C&oacute;digo Departamento"  ,$uri      ,"align='center'");
		$grid->column("Nombre"                      ,"nombre"  ,"align='left'"  );
		$grid->column("Descripci&oacute;n"          ,"descrip" ,"align='left'"  );
		$grid->column("Cuenta"                      ,"cuenta"  ,"align='center'");
		$grid->column("Duplicar"                    ,$uri_2    ,"align='center'");

		$grid->add($this->url."dataedit/create");
		$grid->build();
	
	  $data['content'] = $filter->output.$grid->output;
		$data['title']   = " $this->titp ";
		$data["head"]    = $this->rapyd->get_head();
		$this->load->view('view_ventanas', $data);	
	}
	
	function dataedit($status='',$id=''){
		$this->rapyd->load("dataobject","dataedit");

		$qformato=$this->qformato=$this->datasis->formato_cpla();
		$link2=site_url('inventario/common/sugerir_dpto');
		
		$script='
		function sugerir(){
			$.ajax({
					url: "'.$link2.'",
					success: function(msg){
						if(msg){
							$("#codigo").val(msg);
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

		$cuenta  = $this->datasis->p_modbus($modbus,'cpla' );
		
		$do = new DataObject("dpbi");
		if($status=="create" && !empty($id)){
			$do->load($id);
			$do->set('codigo', '');
			$do->set('id'    , '');
		}
		
		$edit = new DataEdit("Departamento", $do);
		$edit->back_url = site_url($this->url."filteredgrid");
		$edit->script($script, "create");
		$edit->script($script, "modify");
		
		$edit->post_process('insert','_post_insert');
		$edit->post_process('update','_post_update');
		$edit->post_process('delete','_post_delete');
		$edit->pre_process('delete','_pre_del');
		
		$edit->id = new inputField("", "id");
		$edit->id->mode  ="autohide";
		$edit->id->when  =array('');
		
		$sugerir='<a href="javascript:sugerir();" title="Sugerir un C&oacute;digo aleatorio">Sugerir C&oacute;digo </a>';
		$edit->codigo = new inputField("C&oacute;digo Departamento", "codigo");
		//$edit->codigo->mode="autohide";
		$edit->codigo->size     =15;
		$edit->codigo->maxlength=15;
		$edit->codigo->rule     ="strtoupper|required|callback_chexiste|trim";
		$edit->codigo->append($sugerir);

		$edit->nombre = new inputField("Nombre", "nombre");
		$edit->nombre->size     =50;
		$edit->nombre->maxlength=50;
		$edit->nombre->rule ="required|trim";
		
		$edit->descrip = new textareaField("Descripci&oacute;n", "descrip");
		$edit->descrip->rows     =5;
		$edit->descrip->cols     =60;
		//$edit->descrip->rule ="required|trim";
		
		$edit->cuenta =new inputField("Cuenta Contable", "cuenta");
		$edit->cuenta->size     = 18;
		$edit->cuenta->maxlength=15;
		$edit->cuenta->rule     = "trim|callback_chcuentac";
		$edit->cuenta->append($cuenta);

		$edit->buttons("modify","delete", "save", "undo", "back");
		$edit->build();
 
		$data['content'] = $edit->output;           
		$data['title']   = " $this->tits ";        
		$data["head"]    = script("jquery.pack.js").$this->rapyd->get_head();//script("plugins/jquery.numeric.pack.js").script("plugins/jquery.floatnumber.js").
		$this->load->view('view_ventanas', $data);  
	}
	
	function _post_insert($do){
		$id    =$do->get('id');
		$codigo=$do->get('codigo');
		$nombre=$do->get('nombre');
		logusu('dpbi',"DEPARTAMENTO $codigo NOMBRE  $nombre CREADO id $id");
	}
	function _post_update($do){
		$id    =$do->get('id');
		$codigo=$do->get('codigo');
		$nombre=$do->get('nombre');
		logusu('dpbi',"DEPARTAMENTO $codigo NOMBRE  $nombre  MODIFICADO id $id");
	}
	function _post_delete($do){
		$id    =$do->get('id');
		$codigo=$do->get('codigo');
		$nombre=$do->get('nombre');
		logusu('dpbi',"DEPARTAMENTO $codigo NOMBRE  $nombre  ELIMINADO id $id");
	}
	function chexiste($codigo){
		$id = $this->input->post('id');
		$codigo=$this->db->escape($this->input->post('codigo'));
		if($id >0)
			$chek=$this->datasis->dameval("SELECT id FROM dpbi WHERE codigo=$codigo AND id<>$id LIMIT 1");
		else
			$chek=$this->datasis->dameval("SELECT id FROM dpbi WHERE codigo=$codigo LIMIT 1");
		if ($chek > 0){
			$depto=$this->datasis->dameval("SELECT nombre FROM dpbi WHERE codigo=$codigo LIMIT 1");
			$this->validation->set_message('chexiste',"El codigo $codigo ya existe para el departamento $depto");
			return FALSE;
		}else {
  		return TRUE;
		}	
	}
	
	function _pre_del($do) {
		$codigo=$this->db->escape($do->get('codigo'));
		$chek =  $this->datasis->dameval("SELECT COUNT(*) FROM grbi WHERE depto=$codigo");
		if ($chek > 0){
			$do->error_message_ar['pre_del'] = $do->error_message_ar['delete']='El departamento contiene grupos, por ello no puede ser eliminado. Elimine primero todas los grupos que pertenezcan a este departamento';
			return False;
		}
		return True;
	}
}
?>
