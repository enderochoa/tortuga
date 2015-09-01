<?php
class ordinalante extends Controller {
	
	var $titp  = 'Ordinales';
	var $tits  = 'Ordinal';
	var $url   = 'presupuesto/ordinalante/';
	
	function ordinalante(){
		parent::Controller();
		$this->load->library("rapyd");
		$this->formatopres=$this->datasis->traevalor('FORMATOPRES');
		$this->flongpres  =strlen(trim($this->formatopres));
	}
	
	function  index(){
		redirect($this->url."filteredgrid");
	}
	
	function filteredgrid(){
		//$this->datasis->modulo_id(111,1);
		
		$this->rapyd->load("datafilter2","datagrid");//
		
		$filter = new DataFilter2("Filtro por ".$this->titp,"ordinalante");

		$filter->codigopres = new dropdownField("Clasificador Presupuestario","codigopres");		
		$filter->codigopres->rule='required';
		$filter->codigopres->option("","");
		$filter->codigopres->options("SELECT codigo, denominacion FROM ppla ORDER BY codigo");
		
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
		
		$grid = new DataGrid("Lista de ".$this->titp);
		
		$grid->order_by("codigopres","asc");
		
		$grid->column(""                            ,$uri         ,"align='left'");
		$grid->column("Estructura Administrativa"   ,"codigoadm"   ,"align='left'");
		$grid->column("Fondo"                       ,"fondo"      ,"align='left'");
		$grid->column("Clasificador Presupuestario" ,"codigopres"       ,"align='left'");
		$grid->column("Ordinal"                     ,"ordinal"    ,"align='left'");
		$grid->column("Descripci&oacute;n"          ,"denominacion"    ,"align='left'");
		
		$grid->add($this->url."dataedit/create");
		
		$grid->build();
		
		$data['content'] = $filter->output.$grid->output;
		$data['title']   = " $this->titp ";
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
		
		/*$modbus=array(
			'tabla'   =>'v_presaldo',
			'columnas'=>array(
				'codigo'      =>'C&oacute;digo',
				'denominacion'=>'Denominaci&oacute;n'
				),
			'filtro'  =>array('codigo' =>'C&oacute;digo','denominacion'=>'Denominaci&oacute;n'),
			'retornar'=>array('codigo'=>'ppla'),//,'denominacion'=>'denomi_<#i#>'
			//'p_uri'=>array(4=>'<#i#>',5=>'<#fondo#>',6=>'<#estadmin#>',),
			'where'=>'LENGTH(codigo) = '.$this->flongpres,			
			//'script'=>array('ordinal(<#i#>)'),
			'titulo'  =>'Busqueda de partidas');
*/
		$modbus=array(
			'tabla'   =>'ppla',
			'columnas'=>array(
				'codigo' =>'C&oacute;digo',
				'denominacion'=>'Denominaci&oacute;n'),
			'filtro'  =>array('codigo'=>'C&oacute;digo','denominacion'=>'Denominacion'),
			'retornar'=>array('codigo'=>'codigopres'),//,'departa'=>'ccosto_<#i#>'
			'titulo'  =>'Buscar Cuenta',

			);

		$btn=$this->datasis->p_modbus($modbus,'ppla');
		
		
		$edit = new DataEdit2($this->tits, "ordinalante");
		$edit->script($script,"create");
		$edit->script($script,"modify");
		
		$edit->back_url = site_url($this->url."filteredgrid");
		
		$edit->codigoadm = new dropdownField("Estructura Administrativa","codigoadm");
		$edit->codigoadm->option("","Seleccione");
		$edit->codigoadm->rule='required';
		$edit->codigoadm->options("SELECT b.codigo, CONCAT_WS(' ',b.codigo,b.denominacion) AS val FROM presupuestoante AS a JOIN estruadm AS b ON a.codigoadm=b.codigo  GROUP BY b.codigo");
		
		$edit->fondo = new dropdownField("Fondo", "fondo");
		$edit->fondo->rule = "required";
		echo $estadmin=$edit->getval('codigoadm');
		if($estadmin!==false){
			$edit->fondo->options("SELECT tipo,tipo a  FROM presupuestoante WHERE codigoadm='$estadmin' GROUP BY tipo");
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
		$data['title']   = " $this->tits ";
		$data["head"]    = script("jquery.pack.js").$this->rapyd->get_head();
		$this->load->view('view_ventanas', $data);
		
	}

}
?>
