<?php
//division
class Divi extends Controller {

	function divi(){
		parent::Controller(); 
		$this->load->library("rapyd");
	}

	function index(){
		$this->datasis->modulo_id(50,1);
		redirect("nomina/divi/filteredgrid");
	}

	function filteredgrid(){
		$this->rapyd->load("datafilter","datagrid");
		$this->rapyd->uri->keep_persistence();

		$filter = new DataFilter("", 'divi');
		
		$filter->division = new inputField("Divisi&oacute;n", "division");
		$filter->division->size=8;

		$filter->descrip = new inputField("Descripcion","descrip");
		$filter->descrip->size=30;
		
		$filter->codigoadm = new inputField("Est. Admin","codigoadm");
		
		$filter->fondo = new inputField("Fondo","fondo");
		
		$filter->buttons("reset","search");
		$filter->build();

		$uri = anchor('nomina/divi/dataedit/show/<#division#>','<#division#>');

		$grid = new DataGrid("");
		$grid->order_by("division","asc");
		$grid->per_page = 20;
		$grid->column_orderby("Divisi&oacute;n"   ,$uri  ,"division","alingn='left'NOWRAP");
		$grid->column_orderby("Descripci&oacute;n"       ,"descrip","descrip" ,"alingn='left'NOWRAP");
		$grid->column_orderby("Estructura Administrativo","codigoadm","codigoadm" ,"alingn='left'NOWRAP");
		$grid->column_orderby("F. Financiamiento"        ,"fondo","fondo" ,"alingn='left'NOWRAP");
		$grid->add("nomina/divi/dataedit/create");
		$grid->build();
		
		//$data['content'] = $filter->output.$grid->output;
		$data['filtro']  = $filter->output;
		$data['content'] = $grid->output;
		$data['script'] = script("jquery.js")."\n";
		$data['title']   = "Divisiones";
		$data["head"]    = $this->rapyd->get_head();
		$this->load->view('view_ventanas', $data);	
	}

	function dataedit()
 	{
		$this->rapyd->load("dataedit2");
		
		$script='
		$(function(){
			$("#codigoadm").change(function(){
				$.post("'.site_url('presupuesto/presupuesto/get_tipo').'",{ codigoadm:$("#codigoadm").val() },function(data){
					$("#fondo").html(data);
	 			});
 			});
		});
		';
		
		$edit = new DataEdit2("Divisi&oacute;n", "divi");
		$edit->back_url = site_url("nomina/divi/filteredgrid");
		$edit->script($script,"create");
		$edit->script($script,"modify");
		
		$edit->post_process('insert','_post_insert');
		$edit->post_process('update','_post_update');
		$edit->post_process('delete','_post_delete');
	
		$edit->division =  new inputField("Divisi&oacute;n", "division");
		$edit->division->rule="required|callback_chexiste";
		$edit->division->mode="autohide";
		$edit->division->maxlength=8;
		$edit->division->size=9;
		
		$edit->descrip =  new inputField("Descripci&oacute;n", "descrip");
		//$edit->descrip->maxlength=30;
		$edit->descrip->size =40;
		$edit->descrip->rule="strtoupper|required";
		
		
		$edit->codigoadm = new dropdownField("Estructura Administrativa","codigoadm");
		$edit->codigoadm->options("SELECT codigo,CONCAT_WS(' ',codigo,denominacion) FROM estruadm WHERE LENGTH(codigo)=(SELECT LENGTH(valor) FROM valores WHERE nombre='FORMATOESTRU') ORDER BY codigo");
		//$edit->codigoadm->mode = "autohide";
		$edit->codigoadm->rule ="required";
		$edit->codigoadm->style ="width:500px;";
		
		$edit->fondo = new dropdownField("Fondo", "fondo");
		$edit->fondo->style="width:300px;";
		$edit->fondo->options("SELECT fondo,descrip a  FROM fondo");
		
		$edit->buttons("add","modify", "save", "undo", "delete", "back");
		$edit->build();

		$data['content'] = $edit->output;           
		$data['title']   = "Divisiones";        
		$data["head"]    = script('jquery.pack.js').script('plugins/jquery.numeric.pack.js').script('plugins/jquery.floatnumber.js').script('plugins/jquery.autocomplete.js').style('jquery.autocomplete.css').$this->rapyd->get_head();
		$this->load->view('view_ventanas', $data);  
	}
	function _pre_del($do) {
		$codigo=$do->get('division');
		$chek =  $this->datasis->dameval("SELECT COUNT(*) FROM pers WHERE divi='$codigo'");
		$chek += $this->datasis->dameval("SELECT COUNT(*) FROM depa WHERE divi='$codigo'");
		if ($chek > 0){
			$do->error_message_ar['pre_del'] = $do->error_message_ar['delete']='Cliente con Movimiento no puede ser Borrado';
			return False;
		}
		return True;
	}
	function _post_insert($do){
		$codigo=$do->get('division');
		$nombre=$do->get('descrip');
		logusu('divi',"DIVISION $codigo NOMBRE  $nombre CREADO");
	}
	function _post_update($do){
		$codigo=$do->get('division');
		$nombre=$do->get('descrip');
		logusu('divi',"DIVISION $codigo NOMBRE  $nombre  MODIFICADO");
	}
	function _post_delete($do){
		$codigo=$do->get('division');
		$nombre=$do->get('descrip');
		logusu('divi',"DIVISION $codigo NOMBRE  $nombre  ELIMINADO ");
	}
	function chexiste($codigo){
		$codigo=$this->input->post('division');
		$chek=$this->datasis->dameval("SELECT COUNT(*) FROM divi WHERE division='$codigo'");
		if ($chek > 0){
			$nombre=$this->datasis->dameval("SELECT descrip FROM divi WHERE division='$codigo'");
			$this->validation->set_message('chexiste',"La division $codigo nombre $nombre ya existe");
			return FALSE;
		}else {
  		return TRUE;
		}	
	}
	
	function instalar(){
			$query="ALTER TABLE `divi` CHANGE COLUMN `descrip` `descrip` TEXT NULL DEFAULT NULL AFTER `division`";
			$this->db->simple_query($query);
	}
}
?>
