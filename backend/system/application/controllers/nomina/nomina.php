<?php
class Nomina extends Controller {
	
	function Nomina(){
		parent::Controller(); 
		$this->load->library("rapyd");
   }

	function index(){
		$this->datasis->modulo_id(45,1);
		redirect("nomina/nomina/filteredgrid");
	}

	function filteredgrid(){
		$this->rapyd->load("datafilter","datagrid");
		$this->rapyd->uri->keep_persistence();

		$filter = new DataFilter("", 'nomina');
		
		$filter->nombre = new inputField("Nombre", "nombre");
		
		$filter->descrip = new inputField("Descripci&oacute;n", "descrip");
		
		$filter->buttons("reset","search");
		$filter->build();

		$uri = anchor('nomina/nomina/dataedit/show/<#numero#>/<#codigo#>/<#concepto#>/<#fecha#>','<#numero#>');

		$grid = new DataGrid("");
		$grid->order_by("numero","asc");
		$grid->per_page = 20;
		$grid->column_orderby("N&uacute;mero"     ,$uri     ,"numero");
		$grid->column_orderby("Nombre"                                     ,"nombre" ,"nombre" ,"align='left'NOWRAP  ");
		$grid->column_orderby("Descripci&oacute;n"                         ,"descrip","descrip","align='left'NOWRAP  ");
		$grid->column_orderby("Formula"                                    ,"formula","formula","align='left'NOWRAP  ");
		$grid->column_orderby("Fecha","<dbdate_to_human><#fecha#></dbdate_to_human>" ,"fecha"  ,"align='center'NOWRAP");
		$grid->add("nomina/nomina/dataedit/create");
		$grid->build();
		
		//$data['content'] = $filter->output.$grid->output;
		$data['filtro']  = $filter->output;
		$data['content'] = $grid->output;
		$data['script'] = script("jquery.js")."\n";
		$data['title']   = " Nomina ";
		$data["head"]    = $this->rapyd->get_head();
		$this->load->view('view_ventanas', $data);	
	}
	
	function dataedit()
 	{
 		$script ='
		$(function() {
			$(".inputnum").numeric(".");
		});
		';
		$this->rapyd->load("dataedit");
		$edit = new DataEdit("clientes", "nomina");
		$edit->back_url = site_url("nomina/nomina/filteredgrid");
		$edit->script($script, "create");
		$edit->script($script, "modify");
		
		$edit->post_process('insert','_post_insert');
		$edit->post_process('update','_post_update');
		$edit->post_process('delete','_post_delete');
		
		$script ='
		$(function() {
			$(".inputnum").numeric(".");
		});
		';
		
		$edit->numero =  new inputField("N&uacute;mero", "numero");
		$edit->numero->mode="autohide";
		$edit->numero->maxlength=8;
		$edit->numero->size =10;
		$edit->numero->rule ="required";
		
		$edit->frecuencia = new dropdownField("Tipo de N&oacute;mina", "frecuencia");
		$edit->frecuencia->option("","");
		$edit->frecuencia->options(array("Q"=> "Quincenal","M"=>"Mensual","S"=>"Semanal"));
		$edit->frecuencia->style = "width:100px;";
		
		$edit->contrato = new dropdownField("Contrato", "contrato");
		$edit->contrato->option("","");
		$edit->contrato->options('SELECT codigo, nombre FROM noco');
		$edit->contrato->style = "width:300px;";

		$edit->depto = new dropdownField("Departamento", "depto");
		$edit->depto->option("","");
		$edit->depto->options('SELECT departa,descrip FROM depa');
		$edit->depto->style = "width:200px;";

		$edit->codigo = new dropdownField("C&oacute;digo", "codigo"); 
		//$edit->codigo->_dataobject->db_name="trim(codigo)";  
		$edit->codigo->option("","");
		$edit->codigo->options("SELECT codigo,concat(trim(apellido),' ',trim(nombre)) nombre FROM pers ORDER BY apellido");
		$edit->codigo->style = "width:100px;";
		$edit->codigo->mode="autohide";
		
		$edit->nombre =  new inputField("Nombre", "nombre");
    $edit->nombre->mode="autohide";
    $edit->nombre->maxlength=30;
    $edit->nombre->size=40;                    
		
		$edit->concepto = new dropdownField("Concepto", "concepto");
		$edit->concepto->option("","");
		$edit->concepto->options('SELECT concepto,descrip FROM conc ORDER BY descrip');
		$edit->concepto->style = "width:200px;";
		
		$edit->tipo =  new inputField("Tipo","tipo");
		$edit->tipo->option("A","A");
		$edit->tipo->option("D","D");
		$edit->tipo->mode="autohide";
		$edit->tipo->style = "width:50px;";
  
		$edit->descrip =  new inputField("T. Descripci&oacute;n", "descrip");
		$edit->descrip->mode="autohide";
		$edit->descrip->maxlength=35;
		$edit->descrip->size =45;
		
		$edit->grupo =  new inputField("Grupo", "grupo");
		$edit->grupo->maxlength=4;
		$edit->grupo->size =6;
		
		$edit->formula =  new inputField("Formula", "formula");
		$edit->formula->maxlength=120;
		$edit->formula->size =80;
		
		$edit->monto = new inputField("Monto","monto");
		$edit->monto->size =17;
		$edit->monto->maxlength=14;
		$edit->monto->css_class='inputnum';
		$edit->monto->rule='numeric';
			
		$edit->fecha =  new DateonlyField("fecha", "fecha","d/m/Y");
		$edit->fecha->size = 12;
	
		$edit->cuota =  new inputField("Cuota", "cuota");
		$edit->cuota->maxlength=11;
		$edit->cuota->size =13;
		$edit->cuota->css_class='inputnum';
		$edit->cuota->rule='integer';
				
		$edit->cuotat =  new inputField("Cuota Total", "cuotat");
		$edit->cuotat->maxlength=11;
		$edit->cuotat->size =13;
		$edit->cuotat->css_class='inputnum';
		$edit->cuotat->rule='integer';
		
		$edit->valor =  new inputField("Valor", "valor");
		$edit->valor->maxlength=17;
		$edit->valor->size =20;
		$edit->valor->css_class='inputnum';
		$edit->valor->rule='numeric';
		
		$edit->buttons("modify", "save", "undo", "delete", "back");
		$edit->build();
		
		$data['content'] = $edit->output;           
    $data['title']   = " Nomina ";        
    $data["head"]    = script("jquery.pack.js").script("plugins/jquery.numeric.pack.js").script("plugins/jquery.floatnumber.js").$this->rapyd->get_head();
    $this->load->view('view_ventanas', $data);  
	}
	function _post_insert($do){
		$codigo=$do->get('numero');
		$nombre=$do->get('nombre');
		logusu('nomina',"NOMINA $codigo NOMBRE  $nombre CREADO");
	}
	function _post_update($do){
		$codigo=$do->get('numero');
		$nombre=$do->get('nombre');
		logusu('nomina',"NOMINA $codigo NOMBRE  $nombre  MODIFICADO");
	}
	function _post_delete($do){
		$codigo=$do->get('numero');
		$nombre=$do->get('nombre');
		logusu('nomina',"NOMINA $codigo NOMBRE  $nombre  ELIMINADO ");
	}
	function chexiste($codigo){
		$codigo=$this->input->post('codigo');
		$chek=$this->datasis->dameval("SELECT COUNT(*) FROM pers WHERE codigo='$codigo'");
		if ($chek > 0){
			$nombre=$this->datasis->dameval("SELECT nombre FROM pers WHERE codigo='$codigo'");
			$this->validation->set_message('chexiste',"Personal con el codigo $codigo nombre $nombre ya existe");
			return FALSE;
		}else {
  		return TRUE;
		}	
	}
}
?>