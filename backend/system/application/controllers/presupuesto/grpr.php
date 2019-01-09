<?php 
/**
* PROYECTO TORTUGA  
* Grupo de Proveedores
*  
**/
require_once(BASEPATH.'application/controllers/validaciones.php');

	class Grpr extends validaciones {

    function grpr(){
		parent::Controller(); 
		$this->load->library("rapyd");
		$this->datasis->modulo_id(42,1);
	}

	function index(){
		redirect("presupuesto/grpr/filteredgrid");
	}
	
	function filteredgrid(){
		
		$this->rapyd->load("datafilter","datagrid");
		$this->rapyd->uri->keep_persistence();

		$filter = new DataFilter("Filtro de Grupos de Provedores", 'grpr');
		
		$filter->grupo = new inputField("Grupo", "grupo");
		$filter->grupo->size=5;
	
		$filter->nombre = new inputField("Nombre", "gr_desc");
		$filter->nombre->size=25;
		$filter->nombre->maxlength=25;
		
		$filter->buttons("reset","search");
		$filter->build();
		
		$uri = anchor('presupuesto/grpr/dataedit/show/<#grupo#>','<#grupo#>');

		$grid = new DataGrid("Lista de Grupos de Provedores");
		$grid->order_by("gr_desc","asc");
		$grid->per_page = 20;
		
		$grid->column("Grupo",$uri);
		$grid->column("Nombre","gr_desc","gr_desc");
		$grid->column("Cuenta","cuenta");
		
		$grid->add("presupuesto/grpr/dataedit/create");
		$grid->build();
		
		$data['content'] = $filter->output.$grid->output;
		$data['title']   = " Grupos de Beneficiarios ";
		$data["head"]    = $this->rapyd->get_head();
		$this->load->view('view_ventanas', $data);	
	}
	function dataedit()
	{
		$this->rapyd->load("dataedit");
		$link=site_url('presupuesto/grpr/ugrupoprv');
		$script ='
		function ultimo(){
			$.ajax({
				url: "'.$link.'",
				success: function(msg){
				  alert( "El ultimo numero ingresado fue: " + msg );
				}
			});
		}		
		';
						
		$edit = new DataEdit("Grupo de Provedores", "grpr");
		$edit->script($script, "create");
		$edit->script($script, "modify");
		$edit->back_url = site_url("presupuesto/grpr/filteredgrid");
		
		$edit->pre_process("delete",'_pre_delete');
		$edit->post_process('insert','_post_insert');
		$edit->post_process('update','_post_update');
		$edit->post_process('delete','_post_delete');
		
		
		$lgrup='<a href="javascript:ultimo();" title="Consultar ultimo grupo ingresado" onclick="">Consultar ultimo grupo</a>';
		$edit->grupo = new inputField("Grupo", "grupo");
		$edit->grupo->mode ="autohide";
		$edit->grupo->size=7;
		$edit->grupo->maxlength =4;
		$edit->grupo->rule = "required|callback_chexiste|trim";
		$edit->grupo->append($lgrup);
		
		$edit->gr_desc = new inputField("Descripcion", "gr_desc");
		$edit->gr_desc->size=35;
		$edit->gr_desc->maxlength =25;
		$edit->gr_desc->rule = "strtoupper|required|trim";
		
		$edit->cuenta = new inputField("Cta. Contable", "cuenta");
		$edit->cuenta->size=18;
		$edit->cuenta->maxlength =15;
		$edit->cuenta->rule ="callback_chcuentac|trim";
		
		$edit->buttons("modify", "save", "undo", "delete", "back");
		$edit->build();
		
		$data['content'] = $edit->output;           
    $data['title']   = " Grupos de Beneficiarios ";        
    $data["head"]    = script("jquery.pack.js").$this->rapyd->get_head();
    $this->load->view('view_ventanas', $data); 
	}
	function _pre_delete($do) {
		$do->error_message_ar['pre_del']="No se puede borrar el registro ya que hay proveedores que pertenecen a este grupo";
		$grupo=$do->data['grupo'];
		$resulta=$this->datasis->dameval("SELECT count(*) FROM sprv WHERE grupo='$grupo'");
		if ($resulta==0)
			return True;
		else
			return False;
	}
	function _post_insert($do){
		$codigo=$do->get('grupo');
		$nombre=$do->get('gr_desc');
		logusu('grpr',"GRUPO $codigo NOMBRE $nombre CREADO");
	}
	function _post_update($do){
		$codigo=$do->get('grupo');
		$nombre=$do->get('gr_desc');
		logusu('grpr',"GRUPO $codigo NOMBRE $nombre MODIFICADO");
	}
	function _post_delete($do){
		$codigo=$do->get('grupo');
		$nombre=$do->get('gr_desc');
		logusu('grpr',"GRUPO $codigo NOMBRE $nombre ELIMINADO");
	}
	function chexiste($codigo){
		$codigo=$this->input->post('grupo');
		$chek=$this->datasis->dameval("SELECT COUNT(*) FROM grpr WHERE grupo='$codigo'");
		if ($chek > 0){
			$nombre=$this->datasis->dameval("SELECT gr_desc FROM grpr WHERE grupo='$codigo'");
			$this->validation->set_message('chexiste',"El codigo $codigo ya existe para el grupo $nombre");
			return FALSE;
		}else {
  		return TRUE;
		}
	}
	function ugrupoprv(){		
		$consulgrupo=$this->datasis->dameval("SELECT grupo FROM grpr ORDER BY grupo DESC");
		echo $consulgrupo;		
	}
}
?>