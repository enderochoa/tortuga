<?php
//banco`
require_once(BASEPATH.'application/controllers/validaciones.php');
class Banc extends Validaciones {
    var $pre_functions = array("delete" => "banco_delete" );
    var $pos_functions = array();

    function banc() {
      parent::Controller();

      $this->load->helper('form');
      $this->load->helper('url');
      $this->load->helper('text');
      $this->load->library('rapyd');
      define("THISFILE", APPPATH."controllers/nomina".$this->uri->segment(2).EXT);
   }

   function index() {
      $this->datasis->modulo_id(44,1);
    	redirect("tesoreria/banc/filteredgrid");
   }
   function setup()
   {
      $content["content"] = $this->load->view('rapyd/setup', null, true);
      $content["code"] = "";
      $content["rapyd_head"] = "";
      $this->load->view('rapyd/banco_template', $content);
   }

   function banco_delete($llave) {
      //echo   "ELIMINADO $llave";
      return false;
   }
   function filteredgrid() {

		$this->rapyd->load("datafilter","datagrid");
		$filter = new DataFilter("");
		$filter->db->from("banc a");
		$filter->db->join("tban b","a.tbanco=b.cod_banc");

		$filter->codbanc = new inputField("C&oacute;digo", "codbanc");
		$filter->codbanc->size=12;

		$filter->banco = new inputField("Nombre de la Cuenta", "banco");
		$filter->banco->size=12;

		$filter->nom_banc = new inputField("Entidad Bancaria", "nomb_banc");
		$filter->nom_banc->db_name="b.nomb_banc";
		$filter->nom_banc->size=12;

		$filter->numcuent = new inputField("Numero de Cuenta", "numcuent");
		$filter->numcuent->db_name="a.numcuent";
		$filter->numcuent->size=12;
		
		$filter->tipocta = new dropdownField("Tipo de Cuenta", "tipocta");
		$filter->tipocta->style ="width:100px;";
		$filter->tipocta->option("","");
		$mf=$this->datasis->puede(333);
		$mo=$this->datasis->puede(334);
		if(!($mf && $mo))
		$filter->tipocta->options(array("K"=>"Caja","C"=>"Corriente","A" =>"Ahorros","P"=>"Plazo Fijo" ));
		elseif($mf && $mo){
		    $filter->tipocta->option("F","FideComiso");
		    $filter->tipocta->options(array("K"=>"Caja","C"=>"Corriente","A" =>"Ahorros","P"=>"Plazo Fijo" ));
		}elseif($mf){
		    $filter->db->where("tipocta","F");
		    $filter->tipocta->option("F","FideComiso");    
		}elseif($mo){
		    $filter->db->where("tipocta <>","F");
		    $filter->tipocta->options(array("K"=>"Caja","C"=>"Corriente","A" =>"Ahorros","P"=>"Plazo Fijo" ));
		    
		}
		
		$filter->buttons("reset","search");
		$filter->build();

		$uri = anchor('tesoreria/banc/dataedit/show/<#codbanc#>','<#codbanc#>');

		$grid = new DataGrid(anchor('tesoreria/banc/recalcular','Recalcular Saldo en Bancos'));
		$grid->order_by("codbanc","desc");
		$grid->use_function("number_format");
		$grid->per_page = 20;

		$grid->column_orderby("C&oacute;digo"      ,$uri                              ,"codbanc");
		$grid->column_orderby("Nombre de la Cuenta","banco"                           ,"banco");
		$grid->column_orderby("Entidad bancaria"   ,"nomb_banc"                       ,"nom_banc");
		$grid->column_orderby("Nro Cuenta"         ,"numcuent"                        ,"numcuent");
		$grid->column_orderby("Saldo"              ,"<nformat><#saldo#>|2</nformat>"  ,"saldo","align='right' ");

		$grid->add("tesoreria/banc/dataedit/create");
		$grid->build();
		//echo $grid->db->last_query();

		//$data['content'] = $filter->output.$grid->output;
		
		$data['filtro']  = $filter->output;
		$data['content'] = $grid->output;
		$data['script'] = script("jquery.js")."\n";
		$data['title']   = "Bancos";
		$data["head"]    = $this->rapyd->get_head();
		$this->load->view('view_ventanas', $data);
	}
  function dataedit(){
		$this->rapyd->load("dataedit");

		$atts = array(
				'width'     =>'800',
				'height'    =>'600',
				'scrollbars'=>'yes',
				'status'    =>'yes',
				'resizable' =>'yes',
				'screenx'   =>'5',
				'screeny'   =>'5');

		$qformato=$this->qformato=$this->datasis->formato_cpla();

		$mCPLA=array(
			'tabla'   =>'cpla',
			'columnas'=>array(
				'codigo' =>'C&oacute;digo',
				'denominacion'=>'Descripci&oacute;n'),
			'filtro'  =>array('codigo'=>'C&oacute;digo','denominacion'=>'Denominaci&oacute;n'),
			'retornar'=>array('codigo'=>'<#i#>'),
			'titulo'  =>'Buscar Cuenta',
			'p_uri'   =>array(4=>'<#i#>'),
			'where'=>"codigo LIKE \"$qformato\"",
			);

		$bcpla  =$this->datasis->p_modbus($mCPLA,'cuenta');
		$bcpla2 =$this->datasis->p_modbus($mCPLA,'cuentaac');

		$modbus=array(
			'tabla'   =>'sprv',
			'columnas'=>array(
			'proveed' =>'C�digo Beneficiario',
			'nombre'=>'Nombre',
			'rif'=>'RIF'),
			'filtro'  =>array('proveed'=>'C�digo Beneficiario','nombre'=>'Nombre'),
			'retornar'=>array('proveed'=>'codprv'),
			'titulo'  =>'Buscar Beneficiario');

		$boton=$this->datasis->modbus($modbus);

		$mTBAN=array(
			'tabla'   =>'tban',
			'columnas'=>array(
				'cod_banc' =>'C&oacute;digo',
				'nomb_banc'=>'Banco'),
			'filtro'  =>array('cod_banc'=>'C&oacute;digo','nomb_banc'=>'Banco'),
			'retornar'=>array('cod_banc'=>'tbanco'),
			'titulo'  =>'Buscar Banco'
			);

		$bTBAN =$this->datasis->modbus($mTBAN);

		$link=site_url('tesoreria/banc/ubanc');
		$script ='
		function gasto(){
			a=parseInt(dbporcen.value);
			if(a>0 && a<100){
				$("#tr_gastoidb").show();
			}else{
				$("#tr_gastoidb").hide();
			}
		}

		function ultimo(){
			$.ajax({
				url: "'.$link.'",
				success: function(msg){
				  alert( "El ultimo codigo ingresado fue: " + msg );
				}
			});
		}
		$(function() {
			gasto();
			$(".inputnum").numeric(".");
		});
		';

		$edit = new DataEdit("Banco", "banc");
		$edit->back_url = site_url("tesoreria/banc/filteredgrid");
		$edit->script($script, "create");
		$edit->script($script, "modify");

		$edit->post_process('insert','_post_insert');
		$edit->post_process('update','_post_update');
		$edit->post_process('delete','_post_delete');

		$lultimo='<a href="javascript:ultimo();" title="Consultar ultimo codigo ingresado" onclick="">Consultar ultimo codigo</a>';
		$edit->codbanc = new inputField("C&oacute;digo", "codbanc");
		$edit->codbanc->rule = "required|callback_chexiste|trim";
		$edit->codbanc->mode="autohide";
		$edit->codbanc->maxlength=5;
		$edit->codbanc->size =6;
		$edit->codbanc->append($lultimo);

		$edit->tbanco = new inputField("Caja/Banco", "tbanco");
		$edit->tbanco->size =12;
		$edit->tbanco->maxlength =3;
		$edit->tbanco->rule="trim|required";
		$edit->tbanco->readonly=true;
		$edit->tbanco->append($bTBAN);

		$edit->banco = new textareaField("Nombre de la Cuenta", "banco");
		$edit->banco->rows =2;
		$edit->banco->cols=60;
		$edit->banco->rule="trim|required";
		//$edit->banco->readonly=true;
		
		$edit->titular = new inputField("Titular de la Cuenta", "titular");
		$edit->titular->size=40;
		$edit->titular->rule="trim";

		$edit->numcuent = new inputField("Nro. de Cuenta", "numcuent");
		$edit->numcuent->rule='trim';
		$edit->numcuent->size = 25;
		$edit->numcuent->maxlength=25;

		$edit->dire1 = new inputField("Direcci&oacute;n", "dire1");
		$edit->dire1->rule='trim';
		$edit->dire1->size =50;
		$edit->dire1->maxlength=40;

		$edit->dire2 = new inputField("", "dire2");
		$edit->dire2->rule='trim';
		$edit->dire2->size =50;
		$edit->dire2->maxlength=40;

		$edit->telefono = new inputField("Tel&eacute;fono", "telefono");
		$edit->telefono->rule='trim';
		$edit->telefono->size =25;
		$edit->telefono->maxlength=40;

		$edit->nombre = new inputField("Nombre del Gerente", "nombre");
		$edit->nombre->rule='trim';
		$edit->nombre->size =25;
		$edit->nombre->maxlength=40;

		//$edit->moneda = new inputField("Moneda", "moneda");
		//$edit->moneda->size =25;
		//$edit->moneda->maxlength=40;

		//$edit->moneda = new dropdownField("Moneda","moneda");
		//$edit->moneda->options("SELECT moneda, descrip FROM mone ORDER BY moneda");
		//$edit->moneda->style ="width:100px;";

		$edit->tipocta = new dropdownField("Tipo de Cuenta", "tipocta");
		$edit->tipocta->style ="width:100px;";
		$mf=$this->datasis->puede(333);
		$mo=$this->datasis->puede(334);
		if(!($mf && $mo))
		$edit->tipocta->options(array("K"=>"Caja","C"=>"Corriente","A" =>"Ahorros","P"=>"Plazo Fijo" ));
		elseif($mf && $mo){
		    $edit->tipocta->options(array("K"=>"Caja","C"=>"Corriente","A" =>"Ahorros","P"=>"Plazo Fijo" ));
		    $edit->tipocta->option("F","FideComiso");
		}elseif($mf){
		    $edit->db->where("tipocta","F");
		    $edit->tipocta->option("F","FideComiso");    
		}elseif($mo){
		    $edit->db->where("tipocta <>","F");
		    $edit->tipocta->options(array("K"=>"Caja","C"=>"Corriente","A" =>"Ahorros","P"=>"Plazo Fijo" ));
		    
		}

		//$edit->proxch = new inputField("Proximo Cheque", "proxch");
		//$edit->proxch->rule='trim';
		//$edit->proxch->size =12;
		//$edit->proxch->maxlength=12;

		//$edit->saldo = new inputField("Saldo Actual", "saldo");
		//$edit->saldo->size =12;
		//$edit->saldo->readonly=true;

		//$edit->dbporcen = new inputField("Porcentaje de debito", "dbporcen");
		//$edit->dbporcen->rule='trim';
		//$edit->dbporcen->size =12;
		//$edit->dbporcen->maxlength=5;
		//$edit->dbporcen->rule = "callback_chporcent";
		//$edit->dbporcen->onchange="gasto()";

		$lcuent=anchor_popup("/contabilidad/cpla/dataedit/create","Agregar Cuenta Contable",$atts);
		$edit->cuenta = new inputField("Cuenta. Contable", "cuenta");
		$edit->cuenta->rule='callback_chcuentac|trim';
		$edit->cuenta->size =20;
		//$edit->cuenta->readonly=true;
		$edit->cuenta->append($bcpla);
		$edit->cuenta->append($lcuent);

		$edit->cuentaac = new inputField("Cuenta. Acreedora", "cuentaac");
		$edit->cuentaac->rule='callback_chcuentaac|trim';
		$edit->cuentaac->size =20;
		//$edit->cuentaac->readonly=true;
		$edit->cuentaac->append($bcpla2);
		$edit->cuentaac->append($lcuent);

		$edit->fapertura = new  dateonlyField("Fecha de Apertura",  "fapertura");
		$edit->fapertura->insertValue = date('Y-m-d');
		$edit->fapertura->size =12;

		$edit->activo = new dropdownField("Activo", "activo");
		$edit->activo->option("S","Activo");
		$edit->activo->option("N","Inactivo");
		$edit->activo->style = "width:150px";

		$edit->fcierre = new  dateonlyField("Fecha de Cierre de la Cuenta","fcierre");
		$edit->fcierre->insertValue = date('Y-m-d');
		$edit->fcierre->size =12;

		$edit->fondo = new inputField("Clasificaci&oacute;n", "fondo");
		$edit->fondo->size      = 12;
		$edit->fondo->maxlenght = 12;
		//$edit->fondo->style="width:300px;";
		//$edit->fondo->option("","");
		//$edit->fondo->options("SELECT fondo,CONCAT_WS(' ',fondo,descrip) a FROM fondo");

		$edit->refe = new dropdownField("Referencia", "refe");
		$edit->refe->option('N','Cuentas Nomina');
		$edit->refe->option('T','Cuentas Tercero');
		$edit->refe->option('E','Cuentas Especiales');
		$edit->refe->option('P','Cuentas Presupuesto');
		$edit->refe->size  = 60;

		$edit->fondo2 = new dropdownField("F.Financiamiento", "fondo2");
		$edit->fondo2->style  ="width:300px;";
//		$edit->fondo2->db_name=" ";
		$edit->fondo2->option("","");
		$edit->fondo2->options("SELECT fondo,CONCAT(fondo,' ',descrip) a FROM fondo");
//		$edit->fondo2->when = array("modify","create");
//		$edit->fondo2->in   ="fondo";

		
		$edit->intervenido = new textareaField("Concepto por Intervencion", "intervenido");
		$edit->intervenido->rows =4;
		$edit->intervenido->cols=60;

		$edit->buttons("add","modify", "save", "undo", "delete", "back");
		$edit->build();

		$data['content'] = $edit->output;
		$data['title']   = "Bancos";
		$data["head"]    = script("jquery.pack.js").script("plugins/jquery.numeric.pack.js").script("plugins/jquery.floatnumber.js").$this->rapyd->get_head();
		$this->load->view('view_ventanas', $data);
	}

	function chexiste($codigo){
		$codigo=$this->input->post('codbanc');
		$chek=$this->datasis->dameval("SELECT COUNT(*) FROM banc WHERE codbanc='$codigo'");
		if ($chek > 0){
			$banco=$this->datasis->dameval("SELECT banco FROM banc WHERE codbanc='$codigo'");
			$this->validation->set_message('chexiste',"El codigo $codigo ya existe para el banco $banco");
			return FALSE;
		}else {
			return TRUE;
		}
	}

	function ubanc(){
		$consul=$this->datasis->dameval("SELECT codbanc FROM banc ORDER BY codbanc DESC");
		echo $consul;
	}
	
	function recalcular(){
	    $this->db->query("CALL sp_banc_recalculo()");
	    redirect('tesoreria/banc');
	}

	function _post_insert($do){
		$numero = $do->get('codbanc');
		logusu('banc',"Creo Banco Nro $numero");
		//redirect($this->url."actualizar/$numero");
	}

	function _post_update($do){
		$numero = $do->get('codbanc');
		logusu('banc',"Modifico Banco Nro $numero");
		//redirect($this->url."actualizar/$numero");
	}

	function _post_delete($do){
		$numero = $do->get('codbanc');
		logusu('banc',"Elimino Banco Nro $numero");
	}

	function instalar(){
		$query="ALTER TABLE `banc`  ADD COLUMN `fondo` VARCHAR(25) NULL DEFAULT NULL";
		$this->db->simple_query($query);
		$query="ALTER TABLE `banc`  CHANGE COLUMN `banco` `banco` VARCHAR(200) NULL DEFAULT NULL";
		$this->db->simple_query($query);
		$query="ALTER TABLE `mbanc`  CHANGE COLUMN `codbanc` `codbanc` VARCHAR(10) NULL DEFAULT NULL";
		$this->db->simple_query($query);
		$query="ALTER TABLE `banc`  CHANGE COLUMN `cuenta` `cuenta` VARCHAR(25) NULL DEFAULT NUL";
		$this->db->simple_query($query);
		$query="ALTER TABLE `banc` ADD COLUMN `cuentaac` VARCHAR(25) NULL DEFAULT NULL";
		$this->db->simple_query($query);
		$query="ALTER TABLE `banc`  ADD COLUMN `refe` TEXT NULL DEFAULT NULL";
		$this->db->simple_query($query);
		$query="ALTER TABLE `banc`  ADD COLUMN `intervenido` TEXT NULL DEFAULT NULL";
		$this->db->simple_query($query);
		$query="ALTER TABLE `banc`  ADD COLUMN `fondo2` VARCHAR(25) NULL DEFAULT NULL AFTER `fondo`";
		$this->db->simple_query($query);
		$query="ALTER TABLE `banc` 	CHANGE COLUMN `cuenta` `cuenta` VARCHAR(50) NULL DEFAULT NULL AFTER `saldo`";
		$this->db->simple_query($query);
		$query="ALTER TABLE `banc`CHANGE COLUMN `cuentaac` `cuentaac` VARCHAR(50) NULL DEFAULT NULL AFTER `fondo2`";
		$this->db->simple_query($query);
		$query="ALTER TABLE `banc` 	ADD COLUMN `titular` MEDIUMTEXT NULL AFTER `intervenido`";
		$this->db->simple_query($query);
		$query="ALTER TABLE `banc` 	ADD COLUMN `lmayanchocheque` DECIMAL(19,2) NULL DEFAULT '24'";
		$this->db->simple_query($query);
	}
}
?>
