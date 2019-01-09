<?php

//almacenes
class alma extends Controller {

	var $url  ='bienes/alma';
	var $titp ='Ubicaciones';
	var $tits ='Ubicaci&oacute;n';
	
	function alma(){
		parent::Controller();
		$this->load->library("rapyd");
		$this->datasis->modulo_id(260,1);
	}

	function index(){
		$this->db->query("INSERT IGNORE INTO alma (`codigo`,`descrip`) VALUES ('0000','Sin Asignar')");
		redirect($this->url."/filteredgrid");
	}

	function filteredgrid(){
		$this->rapyd->load("datafilter","datagrid");

		$filter = new DataFilter("Filtro de ".$this->titp,"alma");

		$filter->codigo = new inputField("C&oacute;digo", "codigo");
		$filter->codigo->size=10;

		$filter->descrip = new inputField("Descripci&oacute;n", "descrip");
		$filter->descrip->size=20;

		$filter->buttons("reset","search");
		$filter->build();

		$uri = anchor($this->url.'/dataedit/show/<raencode><#codigo#></raencode>','<#codigo#>');

		$grid = new DataGrid("Lista de ".$this->titp);
		$grid->order_by("codigo","asc");
		$grid->per_page = 20;

		$grid->column_orderby("C&oacute;digo"       ,$uri       ,"codigo"    );
		$grid->column_orderby("Descripci&oacute;n"  ,"descrip"  ,"descrip"   );
		$grid->column_orderby("Cuenta Contable"     ,"cuenta"   ,"cuenta"    );
		
		$grid->add($this->url."/dataedit/create");
		$grid->build();

		$data['content'] = $filter->output.$grid->output;
		$data['title']   = $this->titp;
		$data["head"]    = $this->rapyd->get_head();
		$this->load->view('view_ventanas', $data);
	}

	function dataedit(){
		$modbus=array(
			'tabla'   =>'cpla',
			'columnas'=>array(
				'codigo' =>'C&oacute;digo',
				'denominacion'=>'Denominaci&oacute;n'),
			'filtro'  =>array('codigo'=>'C&oacute;digo','denominacion'=>'Denominacion'),
			'retornar'=>array('codigo'=>'cuenta'),//,'departa'=>'ccosto_<#i#>'
			'titulo'  =>'Buscar Cuenta',
			'where' => 'nivel = 5',	
			);
		
		$btn=$this->datasis->p_modbus($modbus,"cpla");
		
		$this->rapyd->load("dataobject","dataedit");

		$edit = new DataEdit($this->tits, "alma");
		$edit->back_url = site_url($this->url."/filteredgrid");

		//$edit->pre_process('insert'  ,'_valida');
		//$edit->pre_process('update'  ,'_valida');
		$edit->post_process('insert','_post_insert');
		$edit->post_process('update','_post_update');
		$edit->post_process('delete','_post_delete');

		$edit->codigo= new inputField("C&oacute;digo", "codigo");
		$edit->codigo->mode     ="autohide";
		$edit->codigo->rule     ='required';
		$edit->codigo->maxlength=4;
		$edit->codigo->size     =4;

		$edit->descrip=new textareaField("Descripci&oacute;n", "descrip");
		$edit->descrip->rows=4;
		$edit->descrip->cols=50;
		
		$edit->uejecuta = new dropdownField("Unidad Ejecutora", "uejecuta");
		$edit->uejecuta->options("SELECT codigo, nombre FROM uejecutora ORDER BY nombre");
		$edit->uejecuta->style="width:250px";
		
		$edit->direc=new textareaField("Direcci&oacute;n", "direc");
		$edit->direc->rows=4;
		$edit->direc->cols=50;
		
		$edit->cuenta = new inputField("Cuenta Contable","cuenta");
		$edit->cuenta->rule ="callback_chcodigo";
		$edit->cuenta->size     = 12;
		$edit->cuenta->append($btn);

		$edit->buttons("modify", "save", "undo", "delete", "back");
		$edit->build();

		$data['content'] = $edit->output;
		$data['title']   = $this->tits;
		$data["head"]    = script("jquery.js").$this->rapyd->get_head();
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

	function _valida($do){
		
	}

	function _post_insert($do){
		$codigo=$do->get('codigo');
		logusu('alma',$this->tits." $codigo CREADO");
	}
	function _post_update($do){
		$codigo=$do->get('codigo');
		logusu('alma',$this->tits." $codigo  MODIFICADO");
	}
	function _post_delete($do){
		$codigo=$do->get('codigo');
		logusu('alma',$this->tits." $codigo  ELIMINADO ");
	}

	function instalar(){
		$mSQL="
		CREATE TABLE `alma` (
		  `codigo` varchar(4) NOT NULL,
		  `descrip` varchar(200) DEFAULT NULL,
		  `uadministra` varchar(4) DEFAULT NULL,
		  `cuenta` varchar(45) DEFAULT NULL,
		  PRIMARY KEY (`codigo`)
		) ENGINE=MyISAM DEFAULT CHARSET=utf8
		";
		$this->db->simple_query($mSQL);
	}
}
?>

