<?php
class ordinal extends Controller {
	
	var $titp  = 'Ordinales';
	var $tits  = 'Ordinal';
	var $url   = 'presupuesto/ordinal/';
	
	function ordinal(){
		parent::Controller();
		$this->load->library("rapyd");
		$this->formatopres=$this->datasis->traevalor('FORMATOPRES');
		$this->flongpres  =strlen(trim($this->formatopres));
	}
	
	function  index(){
		redirect($this->url."filteredgrid");
	}
	
	function filteredgrid(){
		$this->datasis->modulo_id(128,1);
		
		$this->rapyd->load("datafilter2","datagrid");//
		
		$filter = new DataFilter2("","ordinal");

		$filter->codigoadm  = new inputField("Estructura Administrativa"  ,"codigoadm" );
		$filter->fondo      = new inputField("F. Financiamiento"          ,"fondo"     );		
		$filter->codigopres = new inputField("Clasificador Presupuestario","codigopres");
		
		$filter->ordinal = new inputField("Ordinal", "ordinal");
		$filter->ordinal->size=5;
		$filter->ordinal->maxlenght=2;
		$filter->ordinal->clause="likerigth";
		
		$filter->denominacion = new inputField("Descripci&oacute;n", "denominacion");
		$filter->denominacion->size=50;
		$filter->denominacion->maxlenght=100;
		$filter->denominacion->clause="likerigth";	
		
		$filter->buttons("reset","search");
		
		$filter->build();
		
		$uri = anchor($this->url.'/dataedit/show/<#codigoadm#>/<#fondo#>/<#codigopres#>/<#ordinal#>','<#codigoadm#><#fondo#><#codigopres#><#ordinal#>');
		
		$grid = new DataGrid("");
		
		$grid->order_by("codigopres","asc");
		
		$grid->column_orderby(""                            ,$uri,"codigoadm"        ,"align='left'");
		$grid->column_orderby("Estructura Administrativa"   ,"codigoadm", "codigoadm"   ,"align='left'");
		$grid->column_orderby("Fondo"                       ,"fondo"   ,"fondo"   ,"align='left'");
		$grid->column_orderby("Clasificador Presupuestario" ,"codigopres" ,"codigopres"       ,"align='left'");
		$grid->column_orderby("Ordinal"                     ,"ordinal"    ,"ordinal"    ,"align='left'");
		$grid->column_orderby("Descripci&oacute;n"          ,"denominacion"     ,"denominacion"    ,"align='left' NOWRAP");
		
		$grid->add($this->url."dataedit/create");
		
		$grid->build();
		
		$data['filtro']  = $filter->output;
		$data['content'] = $grid->output;
		$data['script'] = script("jquery.js")."\n";
		$data['title']   = "$this->titp";
		//$data['content'] = $filter->output.$grid->output;
		//$data['title']   = " $this->titp ";
		$data["head"]    = $this->rapyd->get_head();
		$this->load->view('view_ventanas', $data);
	}
	
	function dataedit(){
		//$this->datasis->modulo_id(111,1);
		
		$this->rapyd->load("dataedit2");
		//$(".inputnum").numeric(".");
		$script='
			
			$(function() {
							
					$("#codigoadm").change(function(){
						$.post("'.site_url('presupuesto/presupuesto/get_tipo').'",{ codigoadm:$("#codigoadm").val() },function(data){$("#fondo").html(data);})
						
					});
				});
		';
		
		//$modbus=array(
		//	'tabla'   =>'v_presaldo',
		//	'columnas'=>array(
		//		'codigo'      =>'C&oacute;digo',
		//		'denominacion'=>'Denominaci&oacute;n'
		//		),
		//	'filtro'  =>array('codigo' =>'C&oacute;digo','denominacion'=>'Denominaci&oacute;n'),
		//	'retornar'=>array('codigo'=>'ppla'),//,'denominacion'=>'denomi_<#i#>'
		//	'p_uri'=>array(4=>'<#i#>',5=>'<#fondo#>',6=>'<#estadmin#>',),
		//	'where'=>'LENGTH(codigo) = '.$this->flongpres,			
		//	//'script'=>array('ordinal(<#i#>)'),
		//	'titulo'  =>'Busqueda de partidas');
				
		//		$modbus=array(
		//	'tabla'   =>'v_presaldo',
		//	'columnas'=>array(
		//		'codigo'      =>'C&oacute;digo',
		//		//'ordinal'     =>'Ord',
		//		'denominacion'=>'Denominaci&oacute;n',
		//		//'saldo'       =>'Saldo'
		//		),
		//	'filtro'  =>array('codigo' =>'C&oacute;digo','denominacion'=>'Denominaci&oacute;n'),
		//	'retornar'=>array('codigo'=>'ppla','ordinal'=>'ordinal_<#i#>'),//,'denominacion'=>'denomi_<#i#>'
		//	//'p_uri'=>array(4=>'<#i#>',5=>'<#fondo#>',6=>'<#estadmin#>',),
		//	'where'=>'movimiento = "S"',//fondo = <#fondo#> AND codigoadm = <#estadmin#> AND  
		//	'titulo'  =>'Busqueda de partidas');

		$modbus=array(
			'tabla'   =>'ppla',
			'columnas'=>array(
				'codigo' =>'C&oacute;digo',
				'denominacion'=>'Denominaci&oacute;n'),
			'filtro'  =>array('codigo'=>'C&oacute;digo','denominacion'=>'Denominacion'),
			'retornar'=>array('codigo'=>'codigopres'),//,'departa'=>'ccosto_<#i#>'
			'titulo'  =>'Buscar Cuenta',
    
			);

		$btn=$this->datasis->p_modbus($modbus,'ppla');//<#i#>/<#fondo#>/<#codigoadm#>
		
		
		$edit = new DataEdit2($this->tits, "ordinal");
		$edit->script($script,"create");
		$edit->script($script,"modify");
		
		$edit->back_url = site_url($this->url."filteredgrid");
		
		$edit->codigoadm = new dropdownField("Estructura Administrativa","codigoadm");
		$edit->codigoadm->option("","Seleccione");
		$edit->codigoadm->rule='required';
		$edit->codigoadm->options("SELECT b.codigo, CONCAT_WS(' ',b.codigo,b.denominacion) AS val FROM presupuesto AS a JOIN estruadm AS b ON a.codigoadm=b.codigo  GROUP BY b.codigo");
		
		$edit->fondo = new dropdownField("Fondo", "fondo");
		$edit->fondo->rule = "required";
		$estadmin=$edit->getval('codigoadm');
		if($estadmin!==false){
			$edit->fondo->options("SELECT fondo,CONCAT_WS(' ',fondo,descrip) a FROM fondo");
		}else{
			$edit->fondo->option("","Seleccione una estructura administrativa primero");
		}
		      
		$edit->codigopres = new inputField("Clasificador Presupuestario","codigopres");
		$edit->codigopres->rule='required';
		
		$edit->codigopres->append($btn);
		
		
		$edit->ordinal = new inputField("Ordinal", "ordinal");
		$edit->ordinal->size     = 5;
		$edit->ordinal->maxlenght= 2;
		$edit->ordinal->required = true;
		$edit->ordinal->rule='required';
		      
		$edit->denominacion = new inputField("Denominacion", "denominacion");
		$edit->denominacion->size=50;
		$edit->denominacion->maxlenght=100;
		$edit->denominacion->required = true;
		//$edit->descrip->rule='required';
		
		$edit->asignacion = new inputField("Asignacion", "asignacion");			
		
		$edit->buttons("modify", "save", "undo", "delete", "back");
		 
		$edit->build();

		$data['content'] = $edit->output;
		$data['title']   = "$this->tits";
		$data["head"]    = script("jquery.pack.js").$this->rapyd->get_head();
		$this->load->view('view_ventanas', $data);
		
	}
	
	function instalar(){
		$this->db->simple_query("ALTER TABLE `ordinal`  CHANGE COLUMN `asignacion` `asignacion` DECIMAL(19,2) UNSIGNED NULL DEFAULT '0' AFTER `denominacion`,  CHANGE COLUMN `aumento` `aumento` DECIMAL(19,2) UNSIGNED NULL DEFAULT '0' AFTER `asignacion`,  CHANGE COLUMN `disminucion` `disminucion` DECIMAL(19,2) UNSIGNED NULL DEFAULT '0' AFTER `aumento`,  CHANGE COLUMN `traslados` `traslados` DECIMAL(19,2) NULL DEFAULT '0' AFTER `disminucion`,  CHANGE COLUMN `comprometido` `comprometido` DECIMAL(19,2) UNSIGNED NULL DEFAULT '0' AFTER `traslados`,  CHANGE COLUMN `causado` `causado` DECIMAL(19,2) UNSIGNED NULL DEFAULT '0' AFTER `comprometido`,  CHANGE COLUMN `opago` `opago` DECIMAL(19,2) UNSIGNED NULL DEFAULT '0' AFTER `causado`,  CHANGE COLUMN `pagado` `pagado` DECIMAL(19,2) UNSIGNED NULL DEFAULT '0' AFTER `opago`");
	}

}
?>
