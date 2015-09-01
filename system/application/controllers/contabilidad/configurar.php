<?php
class Configurar extends Controller {
	
	function Configurar(){
		parent::Controller();
		$this->load->library("rapyd");
		//$this->datasis->modulo_id(605,1);
	}
	
	function index() {		
		redirect('contabilidad/configurar/dataedit/show/1');
	}
	
	function dataedit(){
 		$this->rapyd->load("dataedit");
		$edit = new DataEdit('Parametros Contables',"cemp");
		$edit->back_url = "contabilidad/configurar";
		
		$edit->post_process('insert','_post_insert');
		$edit->post_process('update','_post_update');
		$edit->post_process('delete','_post_delete');
		
		$edit->inicio = new DateonlyField("Desde", "inicio",'d/m/Y');
		$edit->inicio->group = "Ejercicio Fiscal";
		$edit->inicio->rule= "required";
		$edit->inicio->size= 12;
    
		$edit->final = new DateonlyField("Hasta", "final",'d/m/Y');
		$edit->final->group = "Ejercicio Fiscal";
		$edit->final->rule= "required";
		$edit->final->size= 12;
		
		$edit->formato = new inputField("Formato", "formato");
		$edit->formato->group = "Ejercicio Fiscal";
		$edit->formato->maxlength =17;
		$edit->formato->rule='trim|strtoupper|callback_chformato|required';
		$edit->formato->size=22;
		
		$edit->resultado = new inputField("Resultado"  , "resultado");
		$edit->resultado->maxlength =15;
		$edit->resultado->rule='required';
		$edit->resultado->size=20;
				
		$edit->patrimonio = new dropdownField("Patrimonio"  , "patrimo");
		$edit->patrimonio->option("","");  
		$edit->patrimonio->options("SELECT SUBSTRING_INDEX(codigo, '.', 1) cuenta,SUBSTRING_INDEX(codigo, '.', 1) valor  FROM cpla GROUP BY cuenta");
		$edit->patrimonio->style='width:50px';
		$edit->patrimonio->rule='required';
		$edit->patrimonio->group = "Ejercicio Fiscal";
		
		$edit->ordend = new dropdownField("Deudora"  , "ordend");
		$edit->ordend->group = "Cuentas de Orden";
		$edit->ordend->option("","");  
		//$edit->ordend->rule='required';
		$edit->ordend->options("SELECT SUBSTRING_INDEX(codigo, '.', 1) cuenta,SUBSTRING_INDEX(codigo, '.', 1) valor  FROM cpla GROUP BY cuenta");
		$edit->ordend->style='width:50px';
		
		$edit->ordena = new dropdownField("Acreedora", "ordena");
		$edit->ordena->option("","");  
		$edit->ordena->options("SELECT SUBSTRING_INDEX(codigo, '.', 1) cuenta,SUBSTRING_INDEX(codigo, '.', 1) valor  FROM cpla GROUP BY cuenta");
		$edit->ordena->group = "Cuentas de Orden";
		//$edit->ordena->rule='required';
		$edit->ordena->style='width:50px';
		
		$edit->buttons("modify", "save", "undo");
		$edit->build();

		$data['content'] = $edit->output;
		$data["head"]    = $this->rapyd->get_head();
		$data['title']   = 'Configuraci&oacute;n de la Contabilidad';
		$this->load->view('view_ventanas', $data);
	}
	
	function chformato($formato){
		if (preg_match("/^X+(\.X+)*$/", $formato)==0){
			$this->validation->set_message('chformato',"El formato '$formato' introducido no parece valido");
			return FALSE;
		}else {
  		return TRUE;
		}
	}
	function _post_insert($do){
		$codigo=$do->get('codigo');
		$nombre=$do->get('descrip');
		logusu('cpla',"PLAN DE CUENTA $codigo NOMBRE  $nombre CREADO");
	}
	function _post_update($do){
		$codigo=$do->get('codigo');
		$nombre=$do->get('descrip');
		logusu('cpla',"PLAN DE CUENTA $codigo NOMBRE  $nombre  MODIFICADO");
	}
	function _post_delete($do){
		$codigo=$do->get('codigo');
		$nombre=$do->get('descrip');
		logusu('cpla',"PLAN DE CUENTA $codigo NOMBRE  $nombre  ELIMINADO ");
	}
}
?>