<?php require_once(BASEPATH.'application/controllers/validaciones.php');

class Grup extends validaciones {
	
	function grup(){
		parent::Controller();
		$this->load->library("rapyd");
	  $this->datasis->modulo_id(68,1);
	}
	
	function index(){
		redirect("suministros/grup/filteredgrid");
	}

	function filteredgrid(){
		$this->rapyd->load("datafilter2","datagrid");
		$this->rapyd->uri->keep_persistence();
		
		function blanco($num){
			if(empty($num)||$num==0){
			 return '';
			}else{
				return number_format($num,2,',','.');
			}
		}
		
		$filter = new DataFilter2("Filtro de Grupo de Inventario");
		
		$filter->db->select("a.grupo AS grupo, a.nom_grup AS nom_grup, a.cu_cost AS cu_cos,b.descrip AS linea");
		$filter->db->from("grup AS a");
		$filter->db->join("line AS b","a.linea=b.linea","LEFT");
		
		$filter->grupo = new inputField("Grupo","grupo");
		$filter->grupo->size=20;
		
		$filter->nombre = new inputField("Descripci&oacute;n","nom_grup");
		$filter->nombre->size=20;
		
		$filter->linea = new inputField("Linea","linea");
		$filter->linea->size=20;
		
		$filter->buttons("reset","search");
		$filter->build();

		$uri = anchor('suministros/grup/dataedit/show/<raencode><#grupo#></raencode>','<#grupo#>');
		$uri_2 = anchor('suministros/grup/dataedit/create/<raencode><#grupo#></raencode>','Duplicar');

		$grid = new DataGrid("Lista de Grupos de Inventario");
		$grid->order_by("grupo","asc");
		$grid->per_page = 20;
		$grid->use_function('blanco');

		$grid->column_orderby("Grupo"              ,$uri        ,"grupo"           ,"align='center'      ");
		$grid->column_orderby("Descripci&oacute;n" ,"nom_grup"  ,"nom_grup"        ,"align='left'  NOWRAP");		
		$grid->column_orderby("Linea"       ,"linea"   ,"linea"         ,"align='center'NOWRAP");	
		$grid->column("Duplicar"           ,$uri_2      ,"align='center'");

		$grid->add("suministros/grup/dataedit/create");
		$grid->build();
		
		//$data['content'] = $filter->output.$grid->output;
		$data['filtro']  = $filter->output;
		$data['content'] = $grid->output;
		$data['script'] = script("jquery.js")."\n";
		$data['title']   = "Grupos de Inventario";
		$data["head"]    = $this->rapyd->get_head();
		$this->load->view('view_ventanas', $data);	
	}

	function dataedit($status='',$id='')
 	{
		$this->rapyd->load("dataobject","dataedit");
		
		$qformato=$this->qformato=$this->datasis->formato_cpla();
		$link=site_url('suministros/grup/ultimo');
		$link2=site_url('inventario/common/sugerir_grup');
		
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
								$("#grupo").val(msg);
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
			'filtro'  =>array('codigo'=>'C&oacute;digo','denominacion'=>'Descripci&oacute;n'),
			'retornar'=>array('codigo'=>'<#i#>'),
			'titulo'  =>'Buscar Cuenta',
			'where'=>"codigo LIKE \"$qformato\"",
			'p_uri'=>array(4=>'<#i#>')
		);

		$bcu_cost  = $this->datasis->p_modbus($modbus,'cu_cost' );

		$do = new DataObject("grup");
		$do->set('tipo', 'I');
		if($status=="create" && !empty($id)){
			$do->load($id);
			$do->set('grupo', '');
		}

		$edit = new DataEdit("Grupos de Inventario",$do);
		$edit->back_url = site_url("suministros/grup/filteredgrid");
		$edit->script($script, "modify");
		$edit->script($script, "create");
		
		$ultimo='<a href="javascript:ultimo();" title="Consultar ultimo c&oacute;digo ingresado"> Consultar ultimo c&oacute;digo</a>';
		$sugerir='<a href="javascript:sugerir();" title="Sugerir un C&oacute;digo aleatorio">Sugerir C&oacute;digo </a>';
		$edit->grupo =  new inputField("C&oacute;digo Grupo", "grupo");
		$edit->grupo->mode="autohide";
		$edit->grupo->maxlength=4;
		$edit->grupo->size =6;
		$edit->grupo->rule ="strtoupper|required|callback_chexiste|trim";
		$edit->grupo->append($sugerir);
		$edit->grupo->append($ultimo);
		
		$edit->nom_grup =  new inputField("Nombre del Grupo", "nom_grup");
		$edit->nom_grup->size     =60;
//		$edit->nom_grup->maxlength=30;
		$edit->nom_grup->rule = "strtoupper|required|trim";
		
		$edit->linea = new dropdownField("L&iacute;nea","linea");
		$edit->linea->rule ="required";
		$edit->linea->options("SELECT linea, descrip FROM line ORDER BY descrip");

//		$edit->cu_cost =new inputField("Cuenta Costo", "cu_cost");
//		$edit->cu_cost->size = 18;
//		$edit->cu_cost->maxlength=15;
//		$edit->cu_cost->rule ="trim|callback_chcuentac";
//		$edit->cu_cost->append($bcu_cost);
			
		$edit->buttons("add","modify", "save", "undo", "delete", "back");
		$edit->build();
		
		$link=site_url('suministros/grup/get_linea');

		$data['content'] = $edit->output;
		$data['title']   = "Grupos de Inventario";        
		$data["head"]    = script("jquery.pack.js").script("plugins/jquery.numeric.pack.js").script("plugins/jquery.floatnumber.js").$this->rapyd->get_head();
		$this->load->view('view_ventanas', $data);  
	}
	
	function chexiste($codigo){
		$codigo=$this->input->post('grupo');
		$chek=$this->datasis->dameval("SELECT COUNT(*) FROM grup WHERE grupo='$codigo'");
		if ($chek > 0){
			$grupo=$this->datasis->dameval("SELECT nom_grup FROM grup WHERE grupo='$codigo'");
			$this->validation->set_message('chexiste',"El codigo $codigo ya existe para el grupo $grupo");
			return FALSE;
		}else {
  		return TRUE;
		}	
	}
	
	function _pre_del($do) {
		$codigo=$do->get('grupo');
		$chek =  $this->datasis->dameval("SELECT COUNT(*) FROM sinv WHERE grupo='$codigo'");
		if ($chek > 0){
			$do->error_message_ar['pre_del'] = $do->error_message_ar['delete']='El grupo contiene productos, por ello no puede ser eliminado. Elimine primero todos los productos que pertenezcan a este grupo';
			return False;
		}
		return True;
	}

	function ultimo(){
		$ultimo=$this->datasis->dameval("SELECT grupo FROM grup ORDER BY grupo DESC");
		echo $ultimo;
	}
	
	

	
}
?>