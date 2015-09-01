<?php
//plancuenta
class Cpla extends Controller {
	
	function cpla(){
		parent::Controller();
		$this->load->library("rapyd");
		$this->datasis->modulo_id(22,1);
	}
	
	function index(){
		$this->rapyd->load("datagrid","datafilter2");
		$this->rapyd->uri->keep_persistence();
		
		$filter = new DataFilter2("",'cpla');		

		$filter->codigo   = new inputField("C&oacute;digo","codigo");
		$filter->codigo->size=15;
		//$filter->codigo->clause="likerigth";
		
		$filter->denominacion = new inputField("Denominaci&oacute;n", "denominacion");
		
		$filter->buttons("reset","search");
		$filter->build();

		$uri = anchor('contabilidad/cpla/dataedit/show/<#codigo#>','<#codigo#>');

		$grid = new DataGrid();
		$grid->order_by("codigo","asc");
		$grid->per_page = 20;
		
		$grid->column_orderby("C&oacute;digo"     ,$uri             ,"codigo"      ,"align='left'  NOWRAP");
		$grid->column_orderby("Denomina&oacute;n" ,"denominacion"   ,"denominacion","align='left'  NOWRAP");
		//$grid->column("Usa Departamento"   ,"departa"       ,"align='center'");
		//$grid->column("Cuenta Monetaria"   ,"moneta"        ,"align='center'");
		
		$grid->add("contabilidad/cpla/dataedit/create");
		$grid->build();
		
		//$data['content'] =$filter->output.$grid->output;
		$data['filtro']  = $filter->output;        
		$data['content'] = $grid->output; 
		$data["head"]    = $this->rapyd->get_head();
		$data['script'] = script("jquery.js")."\n";
		$data['title']   ='Plan Patrimonial';
		$this->load->view('view_ventanas', $data);
	}
	function dataedit(){

 		$this->rapyd->load('dataobject','dataedit');
 		
		$edit = new DataEdit("Plan de cuenta","cpla");
		
		$edit->back_url = "contabilidad/cpla/index";
		$edit->pre_process('delete','_pre_del');
		$edit->post_process('insert','_post_insert');
		$edit->post_process('update','_post_update');
		$edit->post_process('delete','_post_delete');
		
		$edit->codigo = new inputField("C&oacute;digo", "codigo");
		$edit->codigo->rule= "required|callback_chcodigo";
//		$edit->codigo->mode="autohide";
		$edit->codigo->size=20;
		$edit->codigo->maxlength =25 ;
		
		$edit->denominacion = new inputField("Descripci&oacute;n", "denominacion");
		$edit->denominacion->rule= "required";
		$edit->denominacion->size=45;
		$edit->denominacion->maxlength =250;
		
		$edit->fcreacion = new  dateonlyField("Fecha Creacion",  "fcreacion");
		$edit->fcreacion->insertValue = date('Y-m-d');
		$edit->fcreacion->size    =12;
		$edit->fcreacion->rule    = 'required';
		$edit->fcreacion->group   = "Transaccion";

		$edit->felimina = new  dateonlyField("Fecha Eliminar",  "felimina");
		$edit->felimina->insertValue = '2021-02-02';
		$edit->felimina->size    =12;
		$edit->felimina->rule    = 'required';
		$edit->felimina->group   = "Transaccion";
		
		$edit->grupo = new inputField("Grupo", "grupo");
		$edit->grupo->rule= "strtoupper";//|required
		$edit->grupo->size=5;
		$edit->grupo->maxlength =5;
		
		//$edit->departa = new dropdownField("Usa departamento", "departa");  
		//$edit->departa->option("N","No");
		//$edit->departa->option("S","Si");
		//$edit->departa->style='width:80px';
		//
		//$edit->moneta = new dropdownField("Cuenta Monetaria", "moneta");  
		//$edit->moneta->option("N","No");
		//$edit->moneta->option("S","Si");
		//$edit->moneta->style='width:80px';
		
		$edit->buttons("modify", "save", "undo", "delete", "back","add");
		$edit->build();

		$data['content'] = $edit->output;
		$data["head"]    = $this->rapyd->get_head();
		$data['title']   = 'Plan de Cuentas';
		$this->load->view('view_ventanas', $data);
	}
	
	function chcodigo($codigo){
		if (preg_match("/^[0-9]+(\.[0-9]+)*$/",$codigo)>0){
			$formato=$this->datasis->traevalor('FORMATOPATRI');
			$farr=explode('.',$formato);
			$carr=explode('.',$codigo);
			$max =count($carr);
			$mmac=count($farr);
			if($mmac>=$max){
				for($i=0;$i<$max;$i++){
					if(strlen($farr[$i])!=strlen($carr[$i])){
						$this->validation->set_message('chcodigo',"El c&oacute;digo dado no coincide con el formato: $formato");
						return false;
					}
				}
			}else{
				$this->validation->set_message('chcodigo',"El c&oacute;digo dado no coincide con el formato: $formato");
				return false;
			}
			$pos=strrpos($codigo,'.');
			if($pos!==false){
				$str=substr($codigo,0,$pos);
				$cant=$this->datasis->dameval("SELECT COUNT(*) FROM cpla WHERE codigo='$str'");
				if($cant==0){
					$this->validation->set_message('chcodigo',"No existe la cuenta padre ($str) para registrar esa cuenta");
					return false;
				}
			}
		}else{
			$this->validation->set_message('chcodigo',"El c&oacute;digo parece tener formato invalido");
			return false;		
		}
		return true;
	}

	function autocomplete(){
		$formato=$this->datasis->traevalor('FORMATOPATRI');
		$formato=str_replace('X','%',$formato);
		$formato=$this->db->escape($formato);
		$query  ="SELECT codigo label,denominacion FROM cpla WHERE codigo LIKE $formato";
		
		$mSQL   = $this->db->query($query);
		$arreglo= $mSQL->result_array($query);
		foreach($arreglo as $key=>$value)
			foreach($value as $key2=>$value2) 
			$arreglo[$key][$key2] = ($value2);
			     
		echo json_encode($arreglo);
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
	function _pre_del($do){
		$codigo=$do->get('codigo');
		$chek =   $this->datasis->dameval("SELECT COUNT(*) FROM cpla WHERE codigo LIKE '$codigo.%'");
		$chek +=  $this->datasis->dameval("SELECT COUNT(*) FROM itcasi WHERE cuenta='$codigo'");
				echo $chek;
		if ($chek > 0){
			$do->error_message_ar['pre_del'] = $do->error_message_ar['delete']='Plan de Cuenta tiene derivados o movimientos';
			return False;
		}
		return True;
	}
	
}
?>
