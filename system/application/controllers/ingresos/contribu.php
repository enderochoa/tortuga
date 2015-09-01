<?php
class Contribu extends Controller {

	var $titp='Contribuyentes';
	var $tits='Contribuyente';
	var $url ='ingresos/contribu/';
	var $on_save_redirect=TRUE;
	var $genesal         =true;

	function Contribu(){
		parent::Controller();
		$this->load->library("rapyd");
		$this->datasis->modulo_id(323,1);
	}

	function index(){
		redirect($this->url."dataedit/create");
	}

	function filteredgrid(){
		$this->rapyd->load("datafilter","datagrid","dataobject");

		$filter = new DataFilter("","contribu");

		$filter->codigo = new inputField("C&oacute;digo", 'codigo');
		
		$filter->nombre = new inputField("Nombre", 'nombre');
		
		$filter->rifci = new inputField("RIF / C&eacute;dula", 'rifci');
		
		$filter->nacionali = new inputField("Nacionalidad", 'nacionali');
		$filter->nacionali->option('','');
		$filter->nacionali->option('VENEZOLANA','VENEZOLANA');
		$filter->nacionali->option('EXTRANJERA','EXTRANJERA');
		
		$filter->direccion = new inputField("Direcci&oacute;n", 'direccion');
		
		$filter->telefono = new inputField("Telefono", 'telefono');

		$filter->buttons("reset","search");
		$filter->build();

		$uri = anchor($this->url.'dataedit/show/<raencode><#codigo#></raencode>','<#codigo#>');

		$grid = new DataGrid("");
		$grid->order_by("codigo ");
		$grid->per_page = 40;

		$grid->column_orderby("C&oacute;digo"    ,$uri                             ,"codigo"                       );
		$grid->column_orderby("Nombre"           ,"nombre"                         ,"nombre"    ,"align='left'    ");
		$grid->column_orderby("Nacionalidad"     ,"nacionali"                      ,"numcuent"  ,"align='left'    ");
		$grid->column_orderby("RIF/C&eacute;dula","rifci"                         ,"cedula"    ,"align='left'     ");
		$grid->column_orderby("Direcci&oacute;n" ,"direccion"                      ,"direccion" ,"align='left'    ");
		$grid->column_orderby("Telefono"         ,"telefono"                       ,"direccion" ,"align='left'    ");
		
		$grid->add($this->url."dataedit/create");
		$grid->build();

		//$data['content'] = $filter->output.$grid->output;
		$data['filtro']  = $filter->output;
		$data['content'] = $grid->output;
		$data['script'] = script("jquery.js")."\n";
		$data['title']   = "$this->titp";
		$data["head"]    = $this->rapyd->get_head();
		$this->load->view('view_ventanas', $data);
	}

	function dataedit($status='',$c=''){
		//$this->datasis->modulo_id(101,1);
		$this->rapyd->load("dataobject","dataedit");

		$link8=site_url($this->url.'sugerir/');
		$script='
			$(document).ready(function(){
				sugerir();
				$("#rifci").focus();
				function damenombre(){
					rifci=$("#rifci").val();
					$.post("'.site_url($this->url.'damerne').'",{ cedula:rifci },function(data){
						rne=jQuery.parseJSON(data);
						$("#nombre").val(rne[0].nombre);
						
						if(rne[0].nombre=="E")
						a=1;
						else
						a=0;

						$("#nacionali").prop("selectedIndex",a);
					});
				}
				$("#rifci").change(function(){
						damenombre();
				});
				damenombre();
			});
		
				$(".inputnumc").numeric("0");
				
				function sugerir(){
				
					$.ajax({
						url: "'.$link8.'",
						success: function(msg){
							if(msg){
								$("#codigo").val(msg);
							}
							else{
								alert("No es posible generar otra sugerencia. Coloque el c&oacute;digo manualmente");
							}
						}
					});
				}
		';

		$do = new DataObject("contribu");

		$edit = new DataEdit($this->tits, $do);

		$edit->back_url = site_url($this->url."filteredgrid");
		
		$edit->script($script,"create");
		$edit->script($script,"modify");

		//$edit->pre_process('insert'  ,'_valida');
		$edit->post_process('insert','_post_insert');
		$edit->post_process('update','_post_update');
		$edit->post_process('delete','_post_delete');

		$sugerir='<a href="javascript:sugerir();" title="Sugerir un C&oacute;digo aleatorio">Sugerir C&oacute;digo </a>';
		$edit->codigo = new inputField("C&oacute;digo", 'codigo');
		$edit->codigo->size      = 6;
		$edit->codigo->rule      = "required|unique";
		$edit->codigo->mode      ="autohide";
		$edit->codigo->maxlenght =6;
		$edit->codigo->append($sugerir);
		
		
		$edit->nacionali = new dropdownField("Nacionalidad", 'nacionali');
		$edit->nacionali->option('V','VENEZOLANA');
		$edit->nacionali->option('E','EXTRANJERA');

		$edit->cedula = new inputField("RIF / C&eacute;dula", 'rifci');
		$edit->cedula->size      = 15;
		$edit->cedula->maxlenght = 13;
		$edit->cedula->rule      = "required|unique";
		$edit->cedula->value     =$c;

		$edit->nombre = new inputField("Nombre", 'nombre');
		$edit->nombre->size      = 60;
		$edit->nombre->maxlenght = 100;
		$edit->nombre->rule      = "required";
		 
		$edit->direccion = new textareaField("Direcci&oacute;n", 'direccion');
		$edit->direccion->rows =2;
		$edit->direccion->cols =60;
		
		$edit->telefono = new inputField("Telefonos", 'telefono');
		$edit->telefono->size      = 40;
		$edit->telefono->maxlenght = 50;

		$edit->buttons("add","modify","save","delete","undo", "back");
		$edit->build();

		if($this->genesal){
			$data['content'] = $edit->output;
			$data['title']   = "$this->tits";
			$data["head"]    = script("jquery.js").script("plugins/jquery.numeric.pack.js").script("plugins/jquery.floatnumber.js").$this->rapyd->get_head();
			$this->load->view('view_ventanas', $data);
		}else{
			if(!empty($edit->error_string))
			return $edit->error_string;
			else
			return $edit->_dataobject->get('codigo');
		}
	}
	
	function _valida($do){
		$error='';
		
		if(empty($error)){
			
		}else{
			$edit->error_string=$error;
			$do->error_message_ar['pre_ins']=$error;
			$do->error_message_ar['pre_upd']=$error;
			return false;
		}
	}
	
	function autocompleteui(){
		$query  ="SELECT codigo,nombre, rifci,nacionali,direccion,telefono FROM contribu GROUP BY nombre, rifci,nacionali,direccion,telefono ORDER BY codigo";
		$mSQL   = $this->db->query($query);
		$arreglo= $mSQL->result_array($query);
		foreach($arreglo as $key=>$value)
			foreach($value as $key2=>$value2) 
			$arreglo[$key][$key2] = ($value2);
			
		echo json_encode($arreglo);
	}
	
	function sugerir(){
		$ultimo=$this->datasis->dameval("SELECT MAX(codigo)+1 FROM contribu");
		echo $ultimo;
	}
	
	function damerne(){
		$cedula = $this->input->post("cedula");
		$cedula = str_replace('.','',$cedula);
			
		$arreglo=array();
		if(is_numeric($cedula)){
			
			$query  ="select CONCAT_WS(' ',
			rne.primer_nombre,
			rne.segundo_nombre,
			rne.primer_apellido,
			rne.segundo_apellido) as nombre,
			nacionalidad
			from rne.rne
			 where cedula=$cedula";
			
			$mSQL   = $this->db->query($query);
			$arreglo= $mSQL->result_array($query);
			foreach($arreglo as $key=>$value)
				foreach($value as $key2=>$value2) 
				$arreglo[$key][$key2] = ($value2);
		}
		echo json_encode($arreglo);
	}
	
	function genesal($s=true){
		$this->genesal=$s;
	}
	
	function to_extjs(){
		$contribu=$this->input->post('contribu');
		$contribue=$this->db->escape($contribu);
		$retorna=$this->datasis->damerow("SELECT codigo,rifci,nombre,direccion,telefono FROM contribu WHERE codigo=$contribue LIMIT 1");
		echo json_encode($retorna);
	}

	function _post_insert($do){
		$primary =implode(',',$do->pk);
		logusu($do->table,"Creo $this->tits $primary ");
	}
	function _post_update($do){
		$primary =implode(',',$do->pk);
		logusu($do->table,"Modifico $this->tits $primary ");
	}
	function _post_delete($do){
		$primary =implode(',',$do->pk);
		logusu($do->table,"Elimino $this->tits $primary ");
	}

	function instalar(){
		$query="
		CREATE TABLE `contribu` (
			`codigo` CHAR(6) NULL DEFAULT NULL,
			`nombre` CHAR(100) NULL DEFAULT NULL,
			`rifci` CHAR(13) NULL DEFAULT NULL,
			`nacionali` CHAR(10) NULL DEFAULT NULL,
			`localidad` CHAR(2) NULL DEFAULT NULL,
			`direccion` TEXT NULL DEFAULT NULL,
			`telefono` CHAR(50) NULL DEFAULT NULL,
			PRIMARY KEY (`codigo`)
		)
		COLLATE='utf8_general_ci'
		ENGINE=MyISAM
		ROW_FORMAT=DEFAULT
		";
		$this->db->simple_query($query);
	}

}
