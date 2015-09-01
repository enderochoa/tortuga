<?php
//departamento
class Depa extends Controller {
	
	function depa(){
		parent::Controller(); 
		$this->load->library("rapyd");
	}
 
	function index(){
		$this->datasis->modulo_id(51,1);
		redirect("nomina/depa/filteredgrid");
	}

	function filteredgrid(){
		$this->rapyd->load("datafilter","datagrid");
		$this->rapyd->uri->keep_persistence();
		
		$filter = new DataFilter("", 'depa');

		$filter->division = new inputField("Departamento","departa");
		$filter->division->size=8;

		$filter->depadesc = new inputField("Descripcion","depadesc");
		$filter->depadesc->size=30;

		$filter->buttons("reset","search");
		$filter->build();
		 
		$uri = anchor('nomina/depa/dataedit/show/<#division#>/<#departa#>','<#division#>');
		
		$grid = new DataGrid("");
		$grid->order_by("division","asc");
		$grid->per_page = 20;
		
		$grid->column_orderby("Divisi&oacute;n"   ,$uri      ,"division");
		$grid->column_orderby("Descripci&oacute;n","descrip" ,"descrip" ,"align='left'NOWRAP");
		$grid->column_orderby("Departamento"      ,"departa" ,"departa" ,"align='left'NOWRAP");
		$grid->column_orderby("Descripci&oacute;n","depadesc","depadesc","align='left'NOWRAP");
		$grid->add("nomina/depa/dataedit/create");
		$grid->build();
		
		//$data['content'] = $filter->output.$grid->output;
		$data['filtro']  = $filter->output;
		$data['content'] = $grid->output;
		$data['script'] = script("jquery.js")."\n";
		$data['title']   = "Departamentos";
		$data["head"]    = $this->rapyd->get_head();
		$this->load->view('view_ventanas', $data);	
	}
	
	function dataedit(){
		$this->rapyd->load("dataedit");
		$edit = new DataEdit("Departamento", "depa");
		$edit->back_url = site_url("nomina/depa/filteredgrid");
		
		$edit->pre_process('delete' ,'_pre_del');
		$edit->post_process('insert','_post_insert');
		$edit->post_process('update','_post_update');
		$edit->post_process('delete','_post_delete');
	
	   $div=array(
	  'tabla'   =>'divi',
	  'columnas'=>array(
		'division' =>'C&oacute;digo de Division',
		'descrip' =>'Descripcion'),
	  'filtro'  =>array('division'=>'C&oacute;digo de Division','descrip'=>'Descripcion'),
	  'retornar'=>array('division'=>'division','descrip'=>'descrip'),
	  'titulo'  =>'Buscar Division');
		
		$boton=$this->datasis->modbus($div);
		
		$depto=array(
	  'tabla'   =>'dept',
	  'columnas'=>array(
		'codigo' =>'C&oacute;digo de Enlase',
		'departam' =>'Descripcion'),
	  'filtro'  =>array('division'=>'C&oacute;digo de Enlase','departam'=>'Descripcion'),
	  'retornar'=>array('codigo'=>'enlase'),
	  'titulo'  =>'Buscar Enlase');
		
		$boton1=$this->datasis->modbus($depto);
	
		$edit->division =  new inputField("Divisi&oacute;n", "division");
		$edit->division->mode="autohide";
		$edit->division->maxlength=8;
		$edit->division->size=9;
		$edit->division->rule="required|callback_chexiste";
		$edit->division->append($boton);	
		
		$edit->descrip =  new inputField("Descripci&oacute;n", "descrip");
		$edit->descrip->maxlength=30;
		$edit->descrip->size =35;
		$edit->descrip->rule="strtoupper|required";
		
		$edit->departa =  new inputField("Departamento", "departa");
		$edit->departa->rule="required";
		$edit->departa->mode="autohide";
		$edit->departa->maxlength=8;
		$edit->departa->size=9;
		
		$edit->depadesc =  new inputField("Descripci&oacute;n", "depadesc");
		$edit->depadesc->maxlength=30;
		$edit->depadesc->size =35;
		$edit->depadesc->rule="strtoupper|required";
		
		$edit->enlase =  new inputField("Enlase","enlase");
		$edit->enlase->maxlength=3;
		$edit->enlase->size=5;
		$edit->enlase->append($boton1);	
		
		$edit->buttons("modify", "save", "undo", "delete", "back");
		$edit->build();
		
		$data['content'] = $edit->output;           
    $data['title']   = "Departamentos";        
    $data["head"]    = $this->rapyd->get_head();
    $this->load->view('view_ventanas', $data); 
	}
	function _pre_del($do) {
		$codigo=$do->get('departa');
		$chek =  $this->datasis->dameval("SELECT COUNT(*) FROM pers WHERE depto='$codigo'");
		if ($chek > 0){
			$do->error_message_ar['pre_del'] = $do->error_message_ar['delete']='Cliente con Movimiento no puede ser Borrado';
			return False;
		}
		return True;
	}
	function _post_insert($do){
		$division=$do->get('division');
		$codigo=$do->get('departa');
		$nombre=$do->get('depadesc');
		logusu('depa',"DIVISION $division DEPARTAMENTO $codigo NOMBRE  $nombre CREADO");
	}
	function _post_update($do){
		$division=$do->get('division');
		$codigo=$do->get('departa');
		$nombre=$do->get('depadesc');
		logusu('depa',"DIVISION $division DEPARTAMENTO $codigo NOMBRE  $nombre  MODIFICADO");
	}
	function _post_delete($do){
		$division=$do->get('division');
		$codigo=$do->get('departa');
		$nombre=$do->get('depadesc');
		logusu('depa',"DIVISION $division DEPARTAMENTO $codigo NOMBRE  $nombre  ELIMINADO ");
	}
	function chexiste($division){
		$departa=$this->input->post('departa');
		$chek=$this->datasis->dameval("SELECT COUNT(*) FROM depa WHERE division='$division' AND departa='$departa'");
		if ($chek > 0){
			$nombre=$this->datasis->dameval("SELECT depadesc FROM depa WHERE division='$division' AND departa='$departa'");
			$this->validation->set_message('chexiste',"La division $division departamento $departa nombre $nombre ya existe");
			return FALSE;
		}else {
  		return TRUE;
		}	
	}
}
?>