<?php
require_once(BASEPATH.'application/controllers/validaciones.php');
class proveedores extends validaciones {
	
	function proveedores(){
		parent::Controller();
		$this->load->library("rapyd");
	}
	
	function  index(){
		redirect("presupuesto/proveedores/filteredgrid");
	}
	
	function filteredgrid(){
		//$this->datasis->modulo_id(101,1);
		
		$this->rapyd->load("datafilter","datagrid");
		
		$filter = new DataFilter("Filtro por Beneficiarioes","proveedores");
		
		$filter->cod_prov = new inputField("C&oacute;digo", "cod_prov");
		$filter->cod_prov->size=5;
		
		$filter->nomb_prov = new inputField("Nombre", "nomb_prov");
		$filter->nomb_prov->size=5;
		
		$filter->direc1_prov = new inputField("Direcci&oacute;n","direc1_prov");
		$filter->direc1_prov->size=40;
		
		$filter->tlf1_prov = new inputField("Telefono","tlf1_prov");
		$filter->tlf1_prov->size=40;
		
		$filter->rif_prov = new inputField("R.I.F.", "rif_prov");
		$filter->rif_prov->size=5;
		
		$filter->pretiva_prov = new dropdownField("Porcentaje de Retenci&oacute;n","pretiva_prov");
		$filter->pretiva_prov->option("0","0%");
		$filter->pretiva_prov->option("75","75%");
		$filter->pretiva_prov->option("100","100%");
		$filter->pretiva_prov -> style='width:60px;';
		
		$filter->buttons("reset","search");
		
		$filter->build(); 
		
		$uri = anchor('presupuesto/proveedores/dataedit/show/<#cod_prov#>','<#cod_prov#>');
		
		$grid = new DataGrid("Lista de Sectores");
		
		$grid->order_by("nomb_prov","asc");
		$grid->per_page = 20;
		
		$grid->column("C&oacute;digo"                 ,$uri            ,"align='left'");
		$grid->column("Nombre"                        ,"nomb_prov"     ,"align='left'");
		$grid->column("Direcci&oacute;n"              ,"direc1_prov"   ,"align='left'");
		$grid->column("Telefono"                      ,"tlf1_prov"     ,"align='left'");
		$grid->column("R.I.F."                        ,"rif_prov"      ,"align='left'");
		$grid->column("Retenci&oacute;n de I.V.A."    ,"pretiva_prov"  ,"align='left'");
		
		$grid->add("presupuesto/proveedores/dataedit/create");
		
		$grid->build();
		
		$data['content'] = $filter->output.$grid->output;
		$data['title']   = " Beneficiarioes ";
		$data["head"]    = $this->rapyd->get_head();
		$this->load->view('view_ventanas', $data);
	}
	
	function dataedit(){
		//$this->datasis->modulo_id(101,1);
		
		$this->rapyd->load("dataedit");
		
		$consulrif=$this->datasis->traevalor('CONSULRIF');
		$link=site_url('presupuesto/proveedores/sugerir_prov');
			
		$script='
			$(".inputnum").numeric(".");
			function consulrif(){
					vrif=$("#rit_prov").val();
					if(vrif.length==0){
						alert("Debe introducir primero un RIF");
					}else{
						vrif=vrif.toUpperCase();
						$("#rif").val(vrif);
						window.open("'.$consulrif.'"+"?p_rif="+vrif,"CONSULRIF","height=350,width=410");
					}
			}
			
			function sugerir(){
			$.ajax({
					url: "'.$link.'",
					success: function(msg){
						if(msg){
							
							$("#cod_prov").val(msg);
						}
						else{
							alert("No es posible generar otra sugerencia. Coloque el c&oacute;digo manualmente");
						}
					}
				});
		}
		';
		
		$edit = new DataEdit("Beneficiario", "proveedores");
		$edit->script($script,"create");
		
		$edit->back_url = site_url("presupuesto/proveedores/filteredgrid");
		
		$sugerir='<a href="javascript:sugerir();" title="Sugerir un C&oacute;digo aleatorio">Sugerir C&oacute;digo </a>';
		$edit->cod_prov = new inputField("C&oacute;digo", "cod_prov");
		$edit->cod_prov->size=5;
		$edit->cod_prov->maxlength=5;
		$edit->cod_prov->mode="autohide";
		$edit->cod_prov->rule='required|callback_chrif|';
		$edit->cod_prov->append($sugerir);
		
		$lrit_prov='<a href="javascript:consulrif();" title="Consultar RIF en el SENIAT" onclick="">Consultar RIF en el SENIAT</a>';
		$edit->rit_prov = new inputField("R.I.F.", "rit_prov");
		$edit->rit_prov->size=20;
		$edit->rit_prov->maxlength=15;
		$edit->rit_prov->rule='required';
		$edit->rit_prov->append($lrit_prov);
		 
		$edit->nomb_prov = new inputField("Nombre", "nomb_prov");
		$edit->nomb_prov->size=40;
		$edit->nomb_prov->maxlength=100;
		$edit->nomb_prov->rule='required';
		
		$edit->direc1_prov = new inputField("Direcci&oacute;n", "direc1_prov");
		$edit->direc1_prov->size=40;
		$edit->direc1_prov->maxlength=70;
		$edit->direc1_prov->rule='required';
		
		$edit->direc2_prov = new inputField("", "direc2_prov");
		$edit->direc2_prov->size=40;
		$edit->direc2_prov->maxlength=60;
		
		$edit->tlf1_prov = new inputField("Telefono", "tlf1_prov");
		$edit->tlf1_prov->size=20;
		$edit->tlf1_prov->maxlength=16;
		
		$edit->tlf2_prov = new inputField("", "tlf2_prov");
		$edit->tlf2_prov->size=20;
		$edit->tlf2_prov->maxlength=16;
		
		$edit->pretiva_prov = new dropdownField("Porcentaje de Retenci&oacute;n","pretiva_prov");
		$edit->pretiva_prov->option("0","0%");
		$edit->pretiva_prov->option("75","75%");
		$edit->pretiva_prov->option("100","100%");
		$edit->pretiva_prov-> style='width:60px;';
		
		$edit->buttons("modify", "save", "undo", "delete", "back");
		
		$edit->build();

		$data['content'] = $edit->output;
		$data['title']   = " Beneficiario ";
		$data["head"]    = script("jquery.pack.js").$this->rapyd->get_head();
		$this->load->view('view_ventanas', $data);
	}
	
	function sugerir_prov(){
		$ultimo=$this->datasis->dameval("SELECT LPAD(hexa,4,0) FROM serie LEFT JOIN proveedores ON LPAD(cod_prov,4,0)=LPAD(hexa,4,0) WHERE valor<65535 AND cod_prov IS NULL LIMIT 1");
		echo $ultimo;		
	}

}
?>