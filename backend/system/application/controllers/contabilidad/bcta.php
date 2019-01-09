<?php
//plancuenta
class Bcta extends Controller {
	
	var $url ="contabilidad/bcta/";
	var $titp="Otros Conceptos Bancarios";
	var $tits="Concepto Bancario";

	function bcta(){
		parent::Controller();
		$this->load->library("rapyd");
		$this->datasis->modulo_id(167,1);
		$this->datasis->modulo_id(252,1);
	}
	
	function index() {		
		$this->rapyd->load("datagrid","datafilter2");
		
		$filter = new DataFilter2("",'bcta');
		
		$filter->codigo   = new inputField("C&oacute;digo","codigo");
		$filter->codigo->size=15;
		$filter->codigo->clause="likerigth";
		
		$filter->denominacion = new inputField("Denominaci&oacute;n", "denominacion");
		
		$filter->cuenta = new inputField("Cuenta", "cuenta");
		
		$filter->buttons("reset","search");
		$filter->build();

		$uri = anchor($this->url.'dataedit/show/<#codigo#>','<#codigo#>');

		$grid = new DataGrid();
		$grid->order_by("codigo","asc");
		$grid->per_page = 20;
		
		$grid->column_orderby("C&oacute;digo"     ,$uri            ,"codigo"                           );
		$grid->column_orderby("Denominaci&oacute;n" ,"denominacion"  ,"denominacion","aling='left'NOWRAP");		
		$grid->column_orderby("Cuenta Contable"   ,"cuenta"        ,"cuenta"      ,"align='left'NOWRAP");
		$grid->column_orderby("Descripci&oacute;n","descrip"       ,"descrip"                          );
		
		$grid->add($this->url."dataedit/create");
		$grid->build();
		
		//$data['content'] =$filter->output.$grid->output;
		
		$data["head"]    = $this->rapyd->get_head();
		$data['filtro']  = $filter->output;
		$data['content'] = $grid->output;
		$data['script'] = script("jquery.js")."\n";
		$data['title']   ="$this->titp";
		$this->load->view('view_ventanas', $data);
	}
	function dataedit(){

 		$this->rapyd->load('dataobject','dataedit');
 		
		$qformato=$this->qformato=$this->datasis->formato_cpla();   
		
 		$modbus=array(
			'tabla'   =>'cpla',
			'columnas'=>array(
				'codigo' =>'C&oacute;digo',
				'denominacion'=>'Denominaci&oacute;n'),
			'filtro'  =>array('codigo'=>'C&oacute;digo','denominacion'=>'Denominacion'),
			'retornar'=>array('codigo'=>'cuenta'),//,'departa'=>'ccosto_<#i#>'
			'titulo'  =>'Buscar Cuenta',
			'where' => "codigo LIKE \"$qformato\"",			
			);
		
		$btn=$this->datasis->p_modbus($modbus,"cpla");
 		
		$script='
			$(".inputnum").numeric(".");
		';
		
		$edit = new DataEdit($this->tits,"bcta");
		
		$edit->back_url = $this->url."index/osp";
		$edit->pre_process('delete','_pre_del');
		$edit->post_process('insert','_post_insert');
		$edit->post_process('update','_post_update');
		$edit->post_process('delete','_post_delete');
		
		$edit->script($script,"create");
		$edit->script($script,"modify");

		$edit->codigo = new inputField("C&oacute;digo", "codigo");
		$edit->codigo->rule= "unique";//|callback_chcodigo";
//		$edit->codigo->mode="autohide";
		$edit->codigo->when =array('create','show','modify');
		$edit->codigo->size=20;

		$edit->denominacion = new inputField("Denominaci&oacute;n", "denominacion");
		$edit->denominacion->rule= "required";//strtoupper|
		$edit->denominacion->size=45;
		$edit->denominacion->maxlenght =250;

		$edit->cuenta = new inputField("Cuenta Contable","cuenta");
		$edit->cuenta->rule     ="callback_chcodigo";
		$edit->cuenta->size     = 12;
		$edit->cuenta->append($btn);
		
		$edit->descrip = new textareaField("Descripcion","descrip");
		$edit->descrip->rows=3;
		$edit->descrip->cols=80;
		
		if($this->datasis->puede(321)){
			$edit->tipo = new dropdownField("Tipo","tipo");
			$edit->tipo->option("O","Otro");
			$edit->tipo->option("P","Por Pagar");
			
			$edit->deuda = new inputField("Deuda", 'deuda');
			$edit->deuda ->css_class ="inputnum";
			$edit->deuda->size = 20;
			
			$edit->saldo = new inputField("Deuda", 'deuda');
			$edit->saldo->size = 20;
			$edit->saldo->mode ="autohide";
			$edit->saldo->when =array("show","modify");
		}
 		
		$edit->buttons("modify", "save", "undo", "back","delete","add");//, "delete"
		$edit->build();

		$data['content'] = $edit->output;
		$data["head"]    = script("jquery.pack.js").script("plugins/jquery.numeric.pack.js").script("plugins/jquery.floatnumber.js").$this->rapyd->get_head();
		$data['title']   = "$this->tits";
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
	
	
 function _post_insert($do){
		$codigo=$do->get('codigo');
		$nombre=$do->get('denominacion');
		logusu('bcta',"OTROS INGRESOS BANCARIOS $codigo NOMBRE  $nombre CREADO");
	}
	function _post_update($do){
		$codigo=$do->get('codigo');
		$nombre=$do->get('denominacion');
		logusu('bcta',"OTROS INGRESOS BANCARIOS $codigo NOMBRE  $nombre  MODIFICADO");
	}
	function _post_delete($do){
		$codigo=$do->get('codigo');
		$nombre=$do->get('denominacion');
		logusu('bcta',"OTROS INGRESOS BANCARIOS $codigo NOMBRE  $nombre  ELIMINADO ");
	}	
	
	function _pre_del($do) {
		$codigo=$do->get('codigo');
		$chek =   $this->datasis->dameval("SELECT COUNT(*) FROM cpla WHERE codigo LIKE '$codigo.%'");
		$chek +=  $this->datasis->dameval("SELECT COUNT(*) FROM itcasi WHERE cuenta='$codigo'");
		
		if ($chek > 0){
			$do->error_message_ar['pre_del'] = $do->error_message_ar['delete']='Plan de Cuenta tiene derivados o movimientos';
			return False;
		}
		return True;
	}
	
	function instalar(){
	$query="ALTER TABLE `bcta`  ADD COLUMN `descrip` TEXT NULL DEFAULT NULL";
	$this->db->simple_query($query);
	$query="ALTER TABLE `bcta`  ADD COLUMN `tipo` CHAR(1) NULL DEFAULT 'O'";
	$this->db->simple_query($query);
	$query="ALTER TABLE `bcta`  ADD COLUMN `deuda` DECIMAL(19,2) NULL DEFAULT '0'";
	$this->db->simple_query($query);
	$query="ALTER TABLE `bcta`  ADD COLUMN `saldo` DECIMAL(19,2) NULL DEFAULT '0'";
	$this->db->simple_query($query);
	}
}
?>