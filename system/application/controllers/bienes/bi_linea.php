<?php

//almacenes
class bi_linea extends Controller {

	var $url  ='/bienes/bi_linea';
	var $titp ='Lineas';
	var $tits ='Linea';
	var $tabla='bi_linea';
	
	function bi_linea(){
		parent::Controller();
		$this->load->library("rapyd");
		//$this->datasis->modulo_id(276,1);
	}

	function index(){
		redirect($this->url."/filteredgrid");
	}

	function filteredgrid(){
		$this->rapyd->load("datafilter","datagrid");

		$filter = new DataFilter("Filtro de ".$this->titp,$this->tabla);

		$filter->grupo = new dropdownField("Grupo","grupo");
		$filter->grupo->option("","");
		$filter->grupo->option(2,"2 Muebles");
		$filter->grupo->option(1,"1 Inmuebles");
	
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
		
		$grid->add($this->url."/dataedit/create");
		$grid->build();

		$data['content'] = $filter->output.$grid->output;
		$data['title']   = $this->titp;
		$data["head"]    = $this->rapyd->get_head();
		$this->load->view('view_ventanas', $data);
	}

	function dataedit(){
		$this->rapyd->load("dataobject","dataedit2");
		$link  =site_url("/bienes/common/get_subgrupo/");
		$link2 =site_url("/bienes/common/get_seccion/");

		$script='
		$(function(){
			$(".inputnum").numeric(".");
			$("#grupo").change(function(){
				 $.post("'.$link.'",{ grupo:$(this).val() },function(data){$("#subgrupo").html(data);$("#seccion").html("");})
			});
			
			$("#subgrupo").change(function(){
				 $.post("'.$link2.'",{ grupo:$("#grupo").val(),subgrupo:$("#subgrupo").val() },function(data){$("#seccion").html(data);})
			});
		})
		';

		$edit = new DataEdit2($this->tits, $this->tabla);
		$edit->back_url = site_url($this->url."/filteredgrid");
		
		$edit->script($script, "create");
		$edit->script($script, "modify");

		//$edit->pre_process('insert'  ,'_valida');
		//$edit->pre_process('update'  ,'_valida');
		$edit->post_process('insert','_post_insert');
		$edit->post_process('update','_post_update');
		$edit->post_process('delete','_post_delete');
		
		$edit->grupo = new dropdownField("Grupo","grupo");
		$edit->grupo->rule='required';
		$edit->grupo->option("",""            );
		$edit->grupo->option("2","2 Muebles"  );
		$edit->grupo->option("1","1 Inmuebles");
		$edit->grupo->group="IDENTIFICACION";
		
		
		$grupo   =$edit->getval('grupo');
		$edit->subgrupo = new dropdownField("Sub-Grupo","subgrupo");
		$edit->subgrupo->rule ="required";
		$edit->subgrupo->group="IDENTIFICACION";
		$edit->subgrupo->style='width:650px;';
		if($grupo!==FALSE){
			$edit->subgrupo->options("SELECT codigo, CONCAT_WS(' ',codigo,descrip) v FROM bi_subgrupo WHERE grupo='$grupo' ORDER BY codigo");
		}else{
			$edit->subgrupo->option("","Seleccione un Grupo primero");
		}
		
		$subgrupo=$edit->getval('subgrupo');
		$edit->seccion = new dropdownField("Seccion","seccion");
		$edit->seccion->rule ="required";
		$edit->seccion->group="IDENTIFICACION";
		$edit->seccion->style='width:650px;';
		if($grupo!==FALSE && $subgrupo!==FALSE){
			$edit->seccion->options("SELECT codigo, CONCAT_WS(' ',codigo,descrip) v FROM bi_seccion WHERE grupo='$grupo' AND subgrupo='$subgrupo' ORDER BY codigo");//WHERE 
		}else{
			$edit->seccion->option("","Seleccione un Sub-Grupo primero");
		}

		$edit->codigo= new inputField("C&oacute;digo", "codigo");
		$edit->codigo->mode     ="autohide";
		$edit->codigo->rule     ='required';
		$edit->codigo->maxlength=4;
		$edit->codigo->size     =4;

		$edit->descrip=new textareaField("Descripci&oacute;n", "descrip");
		$edit->descrip->rows=4;
		$edit->descrip->cols=50;
		$edit->descrip->rule="required";
		
		$edit->buttons("modify", "save", "undo", "delete", "back");
		$edit->build();

		$data['content'] = $edit->output;
		$data['title']   = $this->tits;
		$data["head"]    = script('jquery.js').script('jquery-ui.js').script('plugins/jquery.numeric.pack.js').script('plugins/jquery.floatnumber.js').script('plugins/jquery.meiomask.js').style('vino/jquery-ui.css').$this->rapyd->get_head();
		$this->load->view('view_ventanas', $data);
	}

	function _post_insert($do){
		$codigo=$do->get('codigo');
		logusu($this->tabla,$this->tits." $codigo CREADO");
	}
	function _post_update($do){
		$codigo=$do->get('codigo');
		logusu($this->tabla,$this->tits." $codigo  MODIFICADO");
	}
	function _post_delete($do){
		$codigo=$do->get('codigo');
		logusu($this->tabla,$this->tits." $codigo  ELIMINADO ");
	}

	function instalar(){
		$mSQL="
		CREATE TABLE `bi_linea` (
		  `grupo` char(2) NOT NULL,
		  `subgrupo` char(4) NOT NULL,
		  `seccion` char(4) NOT NULL,
		  `codigo` char(4) NOT NULL,
		  `descrip` varchar(45) DEFAULT NULL,
		  PRIMARY KEY (`codigo`)
		) ENGINE=MyISAM DEFAULT CHARSET=utf8
		
		";
		
		var_dump($this->db->simple_query($mSQL));
	}
}
?>

