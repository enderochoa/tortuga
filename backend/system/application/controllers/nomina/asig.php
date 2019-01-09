<?php
//asignacion
class Asig extends Controller {
	
function asig(){
		parent::Controller(); 
		$this->load->library("rapyd");
	}
		
	function index(){
		$this->datasis->modulo_id(47,1);
		redirect("nomina/asig/filteredgrid");
	}

	function filteredgrid(){
		$this->rapyd->load("datafilter2","datagrid");
		$this->rapyd->uri->keep_persistence();

		$filter = new DataFilter2("", 'asig');
		
		$filter->codigo = new inputField("Codigo","codigo");
		$filter->codigo->size=15;
		$filter->codigo->clause = "likerigth";
		
		$filter->buttons("reset","search");
		$filter->build();

		$uri = anchor('nomina/asig/dataedit/show/<#codigo#>','<#codigo#>');
		
		function sta($status){
			switch($status){
				case "A":return "Asignaci&oacute;n";break;
				case "O":return "Otros";break;
				case "D":return "Devoluciones";break;
			}
		}
		
		$grid = new DataGrid("");
		$grid->order_by("codigo","asc");
		$grid->per_page = 20;
		$grid->use_function('sta');

		$grid->column_orderby("C&oacute;digo"     ,$uri,"codigo");
		$grid->column_orderby("Nombre"            ,"nombre"                                         ,"nombre"  ,"align='left'NOWRAP  ");
		$grid->column_orderby("Concepto"          ,"concepto"                                       ,"concepto","align='left'NOWRAP  ");
		$grid->column_orderby("Tipo"              ,"<sta><#tipo#></sta>"                            ,"tipo"    ,"align='center'NOWRAP");
		$grid->column_orderby("Descripci&oacute;n","descrip"                                        ,"descrip" ,"align='left'NOWRAP  ");
		$grid->column_orderby("F&oacute;rmula"    ,"formula"                                        ,"formula" ,"align='left'NOWRAP  ");
		$grid->column_orderby("Monto"             ,"<number_format><#monto#>|2|,|.</number_format>" ,"monto"   ,"align='right'");
		$grid->column_orderby("Fecha"             ,"<dbdate_to_human><#fecha#></dbdate_to_human>"   ,"fecha"   ,"align='center'");
		$grid->column_orderby("Cuota"             ,"<number_format><#cuota#>|2|,|.</number_format>" ,"cuota"   ,"align='right'");
		$grid->column_orderby("Cuota Total"       ,"<number_format><#cuotat#>|2|,|.</number_format>","cuotat"  ,"align='right'");
				
		$grid->add("nomina/asig/dataedit/create");
		$grid->build();
		
		//$data['content'] = $filter->output.$grid->output;
		$data['filtro']  = $filter->output;
		$data['content'] = $grid->output;
		$data['script'] = script("jquery.js")."\n";
		$data['title']   = "Asignaciones";
		$data["head"]    = $this->rapyd->get_head();
		$this->load->view('view_ventanas', $data);	

	}
	function dataedit()
 	{
		$this->rapyd->load("dataedit");
		$edit = new DataEdit("Asignaciones", "asig");
		
		$script ='
		$(function() {
			$(".inputnum").numeric(".");
		});
		';
		
		$edit->back_url = site_url("nomina/asig/filteredgrid");
		$edit->script($script, "create");
		$edit->script($script, "modify");
		
		$edit->post_process('insert','_post_insert');
		$edit->post_process('update','_post_update');
		$edit->post_process('delete','_post_delete');
		
		$edit->codigo =  new inputField("C&oacute;digo","codigo");
		$edit->codigo->mode="autohide";
		$edit->codigo->maxlength=15;
		$edit->codigo->size =20;
		$edit->codigo->rule ="required|callback_chexiste";
		
		$edit->nombre =  new inputField("Nombre", "nombre");
		$edit->nombre->rule ="strtoupper|required";
		$edit->nombre->maxlength=30;
		$edit->nombre->size =40;
				
		$edit->concepto = new dropdownField("Concepto", "concepto");
	  $edit->concepto->options("SELECT concepto, descrip FROM conc ORDER BY CONCEPTO");
		$edit->concepto->style ="width:300px;";
		
		$edit->tipo = new dropdownField("Tipo", "tipo");
		$edit->tipo->style ="width:100px;";
		$edit->tipo->options(array("A"=> "Asignaci&oacute;n","O"=>"Otros","D"=> "Deducci&oacute;n"));
		
		$edit->descrip =   new inputField("Descripci&oacute;n", "descrip");
		$edit->descrip->size =45;
		$edit->descrip->maxlength=35;
		$edit->descrip->rule ="strtoupper";
		
		$edit->formula =   new inputField("F&oacute;rmula", "formula");
		$edit->formula->size =80;
		$edit->formula->maxlength=150;
		
		$edit->monto = new inputField("Monto", "monto");
		$edit->monto->size = 13;
		$edit->monto->css_class='inputnum';
		$edit->monto->rule='callback_positivo';
		
		$edit->fecha = new DateField("Fecha", "fecha");
		$edit->fecha->size = 12;
		
		$edit->cuota =  new inputField("Cuotas", "cuota");
		$edit->cuota->size = 13;
		$edit->cuota->maxlength=11;
		$edit->cuota->css_class='inputnum';
		$edit->cuota->rule='integer';
				
		$edit->cuotat = new inputField("Total de cuotas", "cuotat");
		$edit->cuotat->size =13;
		$edit->cuotat->maxlength=11;
		$edit->cuotat->css_class='inputnum';
		$edit->cuotat->rule='integer';
		
		$edit->buttons("modify", "save", "undo", "delete", "back");
		$edit->build();
 
		$data['content'] = $edit->output;           
    $data['title']   = "Asignaci&oacute;n";        
   	$data["head"]    = script("jquery.pack.js").script("plugins/jquery.numeric.pack.js").script("plugins/jquery.floatnumber.js").$this->rapyd->get_head();
    $this->load->view('view_ventanas', $data);  
	}
	function _post_insert($do){
		$codigo=$do->get('codigo');
		$nombre=$do->get('nombre');
		logusu('asig',"ASIGNACION $codigo NOMBRE  $nombre CREADO");
	}
	function _post_update($do){
		$codigo=$do->get('codigo');
		$nombre=$do->get('nombre');
		logusu('asig',"ASIGNACION $codigo NOMBRE  $nombre  MODIFICADO");
	}
	function _post_delete($do){
		$codigo=$do->get('codigo');
		$nombre=$do->get('nombre');
		logusu('asig',"ASIGNACION $codigo NOMBRE  $nombre  ELIMINADO ");
	}
	function chexiste($codigo){
		$codigo=$this->input->post('codigo');
		$chek=$this->datasis->dameval("SELECT COUNT(*) FROM asig WHERE codigo='$codigo'");
		if ($chek > 0){
			$nombre=$this->datasis->dameval("SELECT descrip FROM asig WHERE codigo='$codigo'");
			$this->validation->set_message('chexiste',"El codigo $codigo ya existe para la asignacion $nombre");
			return FALSE;
		}else {
  		return TRUE;
		}	
	}
		
	function positivo($valor){
		if ($valor <= 0){
			$this->validation->set_message('positivo',"El campo Monto debe ser positivo");
			return FALSE;
		}
  	return TRUE;
	}
	
	function instalar(){
		$mSQL="ALTER TABLE asig ADD PRIMARY KEY (codigo);";
		$this->db->simple_query($mSQL);	
	}
}
?>