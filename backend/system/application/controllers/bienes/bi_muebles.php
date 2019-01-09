<?php
//almacenes
class bi_muebles extends Controller {
	 
	var $tits='Bien Mueble';

	function bi_muebles(){
		parent::Controller(); 
		$this->load->library("rapyd");
		$this->datasis->modulo_id(261,1);
	}

	function index(){
		redirect("bienes/bi_muebles/filteredgrid");
	}

	function filteredgrid(){
		$this->rapyd->load("datafilter","datagrid");
		$this->rapyd->uri->keep_persistence();

		$filter = new DataFilter("Filtro por Expediente","bi_muebles");

		$filter->id  = new inputField("ID", "id");
		$filter->id->size=10;
		
		$filter->codigo = new inputField("Codigo", "codigo");
		$filter->codigo->size=20;
		
		$filter->grupo = new inputField("Grupo", "grupo");
		$filter->grupo->size=20;
		
		$filter->subgrupo = new inputField("Sub-Grupo", "subgrupo");
		$filter->subgrupo->size=20;
		
		$filter->seccion = new inputField("Secci&oacute;n", "seccion");
		$filter->seccion->size=20;
		
		$filter->numero = new inputField("Numero", "numero");
		$filter->numero->size=20;
		
		$filter->descrip = new inputField("Descripci&oacute;n", "descrip");
		$filter->descrip->size=20;
		
		$filter->alma = new dropdownField("Almacen", "alma");
		$filter->alma->option("","");
		$filter->alma->options("SELECT codigo,CONCAT_WS(' ',codigo,descrip) valor FROM alma ");
		
		$filter->buttons("reset","search");
		$filter->build();
		
		$uri = anchor('bienes/bi_muebles/dataedit/show/<#id#>','<#id#>');
		$uri_2 = anchor('bienes/bi_muebles/dataedit/S/create/<#id#>','Duplicar');
		
		$grid = new DataGrid("Lista de muebles");
		$grid->order_by("id","asc");
		$grid->per_page = 20;
		$grid->use_function('si_no');

		$grid->column_orderby("ID"                ,$uri      ,'id'             );
		$grid->column_orderby("C&oacute;digo"     ,"codigo"  ,"codigo"         );
		$grid->column_orderby("Grupo"             ,"grupo"   ,"grupo"          );
		$grid->column_orderby("Sub-Grupo"         ,"subgrupo","subgrupo"       );
		$grid->column_orderby("Numero"            ,"numero"  ,"numero"         );
		$grid->column_orderby("Descripci&oacute;n","descrip" ,"descrip"        );
		$grid->column_orderby("Almacen"           ,"alma"    ,"alma"           );
		$grid->column(        "Duplicar"          ,$uri_2    ,"align='center'" );

		$grid->add("bienes/bi_muebles/dataedit/create");
		$grid->build();
		
		$data['filtro']  = $filter->output;
		$data['content'] = $grid->output;
		$data['script']  = script("jquery.js");
		$data['title']   = "Bienes Muebles";
		$data["head"]    = $this->rapyd->get_head();
		$this->load->view('view_ventanas', $data);	
	}
	
	function dataedit($duplicar='S',$status='',$numero=''){
		$this->rapyd->load('dataobject','dataedit2');
		
		$link  =site_url("/bienes/common/get_subgrupo/");
		$link2 =site_url("/bienes/common/get_seccion/");
		$link3 =site_url("/bienes/common/get_linea/");

		$script='
		$(function(){
			$("#grupo").change(function(){
				 $.post("'.$link.'",{ grupo:$(this).val() },function(data){$("#subgrupo").html(data);$("#seccion").html("");$("#linea").html("");})
			});
			
			$("#subgrupo").change(function(){
				 $.post("'.$link2.'",{ grupo:$("#grupo").val(),subgrupo:$("#subgrupo").val() },function(data){$("#seccion").html(data);$("#linea").html("");})
			});
			
			$("#seccion").change(function(){
				 $.post("'.$link3.'",{ grupo:$("#grupo").val(),subgrupo:$("#subgrupo").val(),seccion:$("#seccion").val() },function(data){$("#linea").html(data);})
			});
		});
		';
		
		$do = new DataObject("bi_muebles");
		if($status=="create" && !empty($numero) && $duplicar=='S'){
			$do->load($numero);
			$do->set('id', '');
			$do->pk    =array('id'=>'');
			//$do->loaded=0;
		}
		
		$edit = new DataEdit2("Bienes Muebles", $do);
		$edit->back_url = site_url("bienes/bi_muebles/filteredgrid");
		
		$edit->script($script, "create");
		$edit->script($script, "modify");
		
		$edit->pre_process('insert'  ,'_valida');
		$edit->pre_process('update'  ,'_valida');
		$edit->post_process('insert','_post_insert');
		$edit->post_process('update','_post_update');
		$edit->post_process('delete','_post_delete');
		
		$edit->id= new inputField("Id", "id");
		$edit->id->mode="autohide";
		$edit->id->when=array('show');
		$edit->id->group="IDENTIFICACION";
		
		$edit->codigo = new inputField("Codigo", "codigo");
		$edit->codigo->size     =20;
		$edit->codigo->maxlength=20;
		$edit->codigo->group="IDENTIFICACION";
		$edit->codigo->rule = "callback_chexiste";
		
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
			//$edit->subgrupo->options("SELECT codigo, CONCAT_WS(' ',codigo,descrip) v FROM bi_subgrupo  ORDER BY codigo");
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
			//$edit->seccion->options("SELECT codigo, CONCAT_WS(' ',codigo,descrip) v FROM bi_seccion  ORDER BY codigo");
			$edit->seccion->option("","Seleccione un Sub-Grupo primero");
		}
		
		$seccion =$edit->getval('seccion');
		$edit->linea = new dropdownField("Linea","linea");
		//$edit->linea->rule ="required";
		$edit->linea->group="IDENTIFICACION";
		$edit->linea->style='width:650px;';
		if($grupo!==FALSE && $subgrupo!==FALSE && $seccion!==FALSE){
			$edit->linea->options("SELECT codigo, CONCAT_WS(' ',codigo,descrip) v FROM bi_linea WHERE seccion='$seccion' AND grupo='$grupo' AND subgrupo='$subgrupo' AND seccion='$seccion' ORDER BY codigo");
		}else{
			//$edit->linea->options("SELECT codigo, CONCAT_WS(' ',codigo,descrip) v FROM bi_linea  ORDER BY codigo");
			$edit->linea->option("","Seleccione una Seccion primero");
		}
		
		$edit->numero = new inputField("Numero", "numero");
		$edit->numero->size     =5;
		$edit->numero->maxlength=4;
		$edit->numero->group="IDENTIFICACION";
		
		$edit->descrip=new textareaField("Descripci&oacute;n", "descrip");
		$edit->descrip->rows=4;
		$edit->descrip->cols=50;
		
		$edit->alma = new dropdownField("Almacen", "alma");
		$edit->alma->options("SELECT codigo,CONCAT_WS(' ',codigo,descrip) valor FROM alma WHERE codigo='0000' ");
		$edit->alma->mode = "autohide";
		
		$edit->monto = new inputField("Monto", "monto");
		$edit->monto->size     =15;
		$edit->monto->maxlength=15;
		$edit->monto->css_class ='inputnum';
		$edit->monto->rule      ='numeric';
		
		$edit->buttons("modify", "save", "undo", "delete", "back"); 
		$edit->build();
		
		$smenu['link']=barra_menu('101');
		$data['smenu'] = $this->load->view('view_sub_menu', $smenu,true); 
		$data['content'] = $edit->output;
		$data['title']   = " Bienes Muebles";
		$data["head"]    = script("jquery.pack.js").script("plugins/jquery.numeric.pack.js").script("plugins/jquery.floatnumber.js").$this->rapyd->get_head();
		$this->load->view('view_ventanas', $data);
	}
	
	function _valida($do){
		$error = '';
		$numero = $do->get('id');

		if(empty($numero)){
			$ntransac = $this->datasis->fprox_id('bi_id','bien');
			$do->set('id','B'.$ntransac);
			$do->pk    =array('id'=>'B'.$ntransac);
		}
	}
	
	function chexiste($codigo){
		$codigo  = $this->input->post('codigo');
		$codigoe = $this->db->escape($codigo);
		$chek=$this->datasis->dameval("SELECT COUNT(*) FROM bi_muebles WHERE codigo=$codigoe");
		if ($chek > 0){
			$descrip=$this->datasis->dameval("SELECT descrip FROM bi_muebles WHERE codigo=$codigoe limit 1");
			$this->validation->set_message('chexiste',"El codigo $codigo ya existe para el bien mueble $descrip");
			return FALSE;
		}else {
  		return TRUE;
		}	
	}
	
	function _post_insert($do){
		$id    =$do->get('id'    );
		logusu('alma',$this->tits." $id     CREADO");
	}
	function _post_update($do){
		$id    =$do->get('id'    );
		logusu('alma',$this->tits." $id      MODIFICADO");
	}
	function _post_delete($do){
		$id    =$do->get('id'    );
		logusu('alma',$this->tits." $id      ELIMINADO ");
	}
	
	function instalar(){
		$mSQL="
			CREATE TABLE `bi_muebles` (
			  `id` varchar(8) NOT NULL,
			  `codigo` varchar(20) DEFAULT NULL,
			  `grupo` varchar(4) DEFAULT NULL,
			  `subgrupo` varchar(4) DEFAULT NULL,
			  `seccion` varchar(4) DEFAULT NULL,
			  `numero` varchar(8) DEFAULT NULL,
			  `descrip` tinytext,
			  `alma` varchar(4) DEFAULT NULL,
			  PRIMARY KEY (`id`)
			) ENGINE=MyISAM DEFAULT CHARSET=utf8
		";
		$this->db->simple_query($mSQL);
	}	
} 
?>