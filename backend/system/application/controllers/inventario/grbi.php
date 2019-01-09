<?php require_once(BASEPATH.'application/controllers/validaciones.php');

class grbi extends validaciones {
	
	function grbi(){
		parent::Controller();
		$this->load->library("rapyd");
	  
	}
	
	function index(){		
		redirect("inventario/grbi/filteredgrid");
	}

	function filteredgrid(){
		$this->datasis->modulo_id(85,1);
		$this->rapyd->load("datafilter2","datagrid");
		$this->rapyd->uri->keep_persistence();
		
		function blanco($num){
			if(empty($num)||$num==0){
			 return '';
			}else{
				return number_format($num,2,',','.');
			}
		}
		
		$filter = new DataFilter2("","grbi");
		
		$filter->id_grbi = new inputField("Grupo","grupo");
		$filter->id_grbi->size=20;
		
		$filter->grbi_nombre = new inputField("Nombre","nombre");
		$filter->grbi_nombre->size=20;
		
		$filter->buttons("reset","search");
		$filter->build();

		$uri = anchor('inventario/grbi/dataedit/show/<raencode><#grupo#></raencode>','<#grupo#>');
		$uri_2 = anchor('inventario/grbi/dataedit/create/<raencode><#grupo#></raencode>','Duplicar');

		$grid = new DataGrid("Lista de Grupos de Inventario");
		$grid->order_by("grupo","asc");
		$grid->per_page = 20;
		$grid->use_function('blanco');

		$grid->column_orderby("Grupo"              ,$uri        ,"grupo"      ,"align='center'    ");
		$grid->column_orderby("Descripci&oacute;n" ,"nombre"    ,"nombre"     ,"align='left'NOWRAP");
		$grid->column_orderby("Cuenta Costo"       ,"cuenta"    ,"cuenta"     ,"align='center'");	
		$grid->column("Duplicar"                   ,$uri_2      ,"align='center'");

		$grid->add("inventario/grbi/dataedit/create");
		$grid->build();
		
		//$data['content'] = $filter->output.$grid->output;
		$data['filtro']  = $filter->output;        
		$data['content'] = $grid->output;          
		$data['script'] = script("jquery.js")."\n";

		$data['title']   = "Grupos de Bienes";
		$data["head"]    = $this->rapyd->get_head();
		$this->load->view('view_ventanas', $data);	
	}

	function dataedit($status='',$id='')
 	{
 		$this->datasis->modulo_id(85,1);
		$this->rapyd->load("dataobject","dataedit");
		
		$qformato=$this->qformato=$this->datasis->formato_cpla();
		$link=site_url('inventario/grbi/ultimo');
		$link2=site_url('inventario/grbi/sugerir_grbi');
		
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
			'retornar'=>array('codigo'=>'cuenta'),
			'titulo'  =>'Buscar Cuenta',
			'where'=>"codigo LIKE \"$qformato\"",
			'p_uri'=>array(4=>'<#i#>')
		);

		$bcu_cost  = $this->datasis->p_modbus($modbus,'cu_cost' );

		$do = new DataObject("grbi");
		if($status=="create" && !empty($id)){
			$do->load($id);
			$do->set('grupo', '');
		}

		$edit = new DataEdit("Grupos de Bienes",$do);
		$edit->back_url = site_url("inventario/grbi/filteredgrid");
		$edit->script($script, "modify");
		$edit->script($script, "create");
				
		$edit->pre_process('delete','_pre_del');

		$ultimo='<a href="javascript:ultimo();" title="Consultar ultimo codigo ingresado"> Consultar ultimo c&oacute;digo</a>';
		$sugerir='<a href="javascript:sugerir();" title="Sugerir un Codigo aleatorio">Sugerir C&oacute;digo </a>';
		$edit->grupo = new inputField("C&oacute;digo Grupo", "grupo");
		$edit->grupo->mode="autohide";
		$edit->grupo->maxlength=4;
		$edit->grupo->size =6;
		$edit->grupo->rule ="required|callback_chexiste|trim";
		$edit->grupo->append($sugerir);
		$edit->grupo->append($ultimo);
		
		$edit->nombre =  new inputField("Nombre del Grupo", "nombre");
		$edit->nombre->size = 35;
		$edit->nombre->maxlength=50;
		$edit->nombre->rule = "strtoupper|required|trim";
		

		$edit->cuenta =new inputField("Cuenta Costo", "cuenta");
		$edit->cuenta->size = 18;
		$edit->cuenta->maxlength=15;
		$edit->cuenta->rule ="trim|callback_chcuentac";
		$edit->cuenta->append($bcu_cost);
			
		$edit->buttons("modify", "save", "undo", "delete", "back");
		$edit->build();
		
		$data['content'] = $edit->output;
		$data['title']   = " Grupos de Bienes ";        
		$data["head"]    = script("jquery.pack.js").$this->rapyd->get_head();
		$this->load->view('view_ventanas', $data);  
	}
	
	function chexiste($codigo){
		$codigo=$this->input->post('grupo');
		$chek=$this->datasis->dameval("SELECT COUNT(*) FROM grbi WHERE grupo='$codigo'");
		if ($chek > 0){
			$grupo=$this->datasis->dameval("SELECT nombre FROM grbi WHERE grupo='$codigo'");
			$this->validation->set_message('chexiste',"El codigo $codigo ya existe para el grupo $grupo");
			return FALSE;
		}else {
  		return TRUE;
		}	
	}
	
	function _pre_del($do) {
		$codigo=$do->get('grupo');
		$chek =  $this->datasis->dameval("SELECT COUNT(*) FROM bienes WHERE grupo='$codigo'");
		if ($chek > 0){
			$do->error_message_ar['pre_del'] = $do->error_message_ar['delete']='El grupo contiene productos, por ello no puede ser eliminado. Elimine primero todos los productos que pertenezcan a este grupo';
			return False;
		}
		return True;
	}

	function ultimo(){
		$ultimo=$this->datasis->dameval("SELECT grupo FROM grbi ORDER BY grupo DESC");
		echo $ultimo;
	}
	
	function sugerir_grbi(){
		$ultimo=$this->datasis->dameval("SELECT LPAD(hexa,4,0) FROM serie LEFT JOIN grup ON LPAD(grupo,4,0)=LPAD(hexa,4,0) WHERE valor<65535 AND grupo IS NULL LIMIT 1");
		echo $ultimo;
	}
}
?>