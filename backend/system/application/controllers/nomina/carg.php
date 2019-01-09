<?php
//cargos
class Carg extends Controller {
	
	var $tits="Cargo";
	var $titp="Cargos";
	var $url="nomina/carg";
	
	function carg(){
		parent::Controller(); 
		$this->load->library("rapyd");
  }

   function index(){
  	$this->datasis->modulo_id(46,1);
  	redirect("nomina/carg/filteredgrid");
  }

	function filteredgrid(){
		$this->rapyd->load("datafilter2","datagrid");
		$this->rapyd->uri->keep_persistence();

		$filter = new DataFilter2("",'carg');
		
		$filter->cargo   = new inputField("C&oacute;digo", "cargo");
		$filter->cargo->size=3;
		$filter->cargo->clause = "likerigth";
		
		$filter->descrip = new inputField("Descripci&oacute;n", "descrip");
		$filter->cargo->clause = "likerigth";
		
		$filter->buttons("reset","search");
		$filter->build();
 
		$uri = anchor('nomina/carg/dataedit/show/<raencode><#cargo#></raencode>','<#cargo#>');

		$grid = new DataGrid("");
		$grid->order_by("cargo","asc");
		$grid->per_page = 20;
		
		$grid->column_orderby("Cargo"                     ,$uri                                             ,"cargo"                            );
		$grid->column_orderby("Descripci&oacute;n"        ,"descrip"                                        ,"descrip"     ,"align='left'NOWRAP");
		$grid->column_orderby("Sueldo"                    ,"<number_format><#sueldo#>|2|,|.</number_format>","sueldo"      ,"align='right'     ");
		$grid->column_orderby("Estructura Administrativa" ,"codigoadm"                                      ,"codigoadm"   ,"align='left'      ");
		$grid->column_orderby("Codigo Presupuestario"     ,"codigopres"                                     ,"codigopres"  ,"align='left'      ");
		$grid->column_orderby("F. Financiamiento"         ,"fondo"                                          ,"fondo"       ,"align='left'      ");
		
		$grid->add("nomina/carg/dataedit/create");
		$grid->build();
		
		

		//$data['content'] = $filter->output.$grid->output;
		$data['filtro']  = $filter->output;
		$data['content'] = $grid->output;
		$data['script'] = script("jquery.js")."\n";
		$data['title']   = "Cargos";
		$data["head"]    = $this->rapyd->get_head();
		$this->load->view('view_ventanas', $data);	

	}
		function dataedit(){
 		$this->rapyd->load("dataedit");
 		
 		$mPPLA=array(
			'tabla'   =>'v_presaldo',
			'columnas'=>array(
				'codigo'      =>'C&oacute;digo',
				'denominacion'=>'Denominaci&oacute;n'),
			'filtro'  =>array('codigo'=>'C&oacute;digo','denominacion'=>'Denominaci&oacute;n'),
			'retornar'=>array('codigo'=>'codigopres'),
			'titulo'  =>'Buscar Cuenta',
			'where'=>'movimiento = "S"',
		);
		$bPPLA    =$this->datasis->p_modbus($mPPLA ,'ppla');
  	
		$script ='
		$(function() {
			$(".inputnum").numeric(".");
		});
		';
  	
		$edit = new DataEdit($this->tits,"carg");
		$edit->back_url = "nomina/carg/filteredgrid";
		$edit->script($script, "create");
		$edit->script($script, "modify");
		
		$edit->post_process('insert','_post_insert');
		$edit->post_process('update','_post_update');
		$edit->post_process('delete','_post_delete');
		
		$edit->cargo = new inputField("Cargo", "cargo");
		$edit->cargo->rule= "required|callback_chexiste";
		$edit->cargo->mode="autohide";
		$edit->cargo->maxlength=8;
		$edit->cargo->size=10;
		
		$edit->descrip = new inputField("Descripci&oacute;n", "descrip");
		$edit->descrip->size=40;
		$edit->descrip->maxlength=250;
		$edit->descrip->rule= "strtoupper|required";
		
		$edit->sueldo  = new inputField("Sueldo", "sueldo");
		$edit->sueldo->size     =20;
		$edit->sueldo->rule     = "callback_positivo";
		$edit->sueldo->css_class='inputnum';
		
		$edit->codigoadm = new dropdownField("Estructura Administrativa","codigoadm");
		$edit->codigoadm->option("","Seleccione");
		$edit->codigoadm->options("SELECT b.codigo, CONCAT_WS(' ',b.codigo,b.denominacion) AS val FROM presupuesto AS a JOIN estruadm AS b ON a.codigoadm=b.codigo  GROUP BY b.codigo");
		
		$edit->fondo = new dropdownField("Fondo", "fondo");
		$edit->fondo->style="width:300px;";
		$edit->fondo->options("SELECT tipo,tipo a  FROM presupuesto  GROUP BY tipo");
		
		$edit->codigopres = new inputField("Partida", "codigopres");
		//$edit->codigopres->rule='required';//callback_repetido|
		$edit->codigopres->size=20;
		$edit->codigopres->append($bPPLA);
		
		
		$edit->buttons("modify", "save", "undo", "delete", "back");
		$edit->build();

		$data['content'] = $edit->output;
		$data['title']   = $this->tits;
		$data["head"]    = script("jquery.pack.js").script("plugins/jquery.numeric.pack.js").script("plugins/jquery.floatnumber.js").$this->rapyd->get_head();
		$this->load->view('view_ventanas', $data);  
	}
	
	function _pre_del($do) {
		$codigo=$do->get('cargo');
		$codigo=$this->db->escape($codigo);
		$chek =  $this->datasis->dameval("SELECT COUNT(*) FROM pers WHERE cargo=$codigo");
		if ($chek > 0){
			$do->error_message_ar['pre_del'] = $do->error_message_ar['delete']='Cliente con Movimiento no puede ser Borrado';
			return False;
		}
		return True;
	}
	function _post_insert($do){
		$codigo=$do->get('cargo');
		$nombre=$do->get('descrip');
		logusu('carg',"CARGO $codigo NOMBRE  $nombre CREADO");
	}
	function _post_update($do){
		$codigo=$do->get('cargo');
		$nombre=$do->get('descrip');
		logusu('carg',"CARGO $codigo NOMBRE  $nombre  MODIFICADO");
	}
	function _post_delete($do){
		$codigo=$do->get('cargo');
		$nombre=$do->get('descrip');
		logusu('carg',"CARGO $codigo NOMBRE  $nombre  ELIMINADO ");
	}
	function chexiste($codigo){
		$codigo=$this->input->post('cargo');
		$codigo=$this->db->escape($codigo);
		$chek=$this->datasis->dameval("SELECT COUNT(*) FROM carg WHERE cargo=$codigo");
		if ($chek > 0){
			$cargo=$this->datasis->dameval("SELECT descrip FROM carg WHERE cargo=$codigo");
			$this->validation->set_message('chexiste',"El codigo $codigo ya existe para el cargo $cargo");
			return FALSE;
		}else {
  		return TRUE;
		}	
	}
	function instalar(){
		$mSQL="ALTER TABLE carg ADD PRIMARY KEY (cargo);";
		$this->db->simple_query($mSQL);
		$mSQL="ALTER TABLE `carg` ADD COLUMN `codigoadm` VARCHAR(25) NULL DEFAULT NULL AFTER `sueldo`";
		$this->db->simple_query($mSQL);
		$mSQL="ALTER TABLE `carg` ADD COLUMN `codigopres` VARCHAR(25) NULL DEFAULT NULL AFTER `codigoadm`";
		$this->db->simple_query($mSQL);
		$mSQL="ALTER TABLE `carg`ADD COLUMN `fondo` VARCHAR(25) NULL DEFAULT NULL AFTER `codigopres`";
		$this->db->simple_query($mSQL);
		$mSQL="ALTER TABLE `carg` ADD COLUMN `cantidad` INT NULL DEFAULT '0' AFTER `fondo`";
		$this->db->simple_query($mSQL);
		$mSQL="ALTER TABLE `carg` 	CHANGE COLUMN `descrip` `descrip` TEXT NULL DEFAULT NULL AFTER `cargo`";
		$this->db->simple_query($mSQL);
	}
	
	function positivo($valor){
		if ($valor <= 0){
			$this->validation->set_message('positivo',"El campo Sueldo debe ser positivo");
			return FALSE;
		}
  	return TRUE;
	}
}
?>
