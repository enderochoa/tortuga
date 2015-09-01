<?php require_once(BASEPATH.'application/controllers/validaciones.php');

class bienes extends validaciones {
	
	function bienes(){
		parent::Controller();
		$this->load->library("rapyd");
	  
	}
	
	function index(){		
		redirect("bienes/bienes/filteredgrid");
	}

	function filteredgrid(){
		$this->datasis->modulo_id(79,1);
		$this->rapyd->load("datafilter2","datagrid");
		$this->rapyd->uri->keep_persistence();
		
		function blanco($num){
			if(empty($num)||$num==0){
			 return '';
			}else{
				return number_format($num,2,',','.');
			}
		}
		
		$filter = new DataFilter2("","bienes");
		
		//$filter->codigo = new inputField("C&oacute;digo","codigo");
		//$filter->codigo->size=20;
		//
		//$filter->nombre = new inputField("Nombre","nombre");
		//$filter->nombre->size=20;
		//
		//$filter->grupo =new dropdownField("Grupo","grupo");
		//$filter->grupo->option("","");
		//$filter->grupo->options("SELECT grupo,CONCAT_WS(' ',grupo,nombre) FROM grbi");
		
		$filter->barras = new inputField("Cod. Barras","barras");
		$filter->barras->size=20;
		
		$filter->buttons("reset","search");
		$filter->build();

		$uri = anchor('bienes/bienes/dataedit/show/<raencode><#codigo#></raencode>','<#codigo#>');
		$uri_2 = anchor('bienes/bienes/dataedit/create/<raencode><#codigo#></raencode>','Duplicar');

		$grid = new DataGrid("");
		//$grid->order_by("codigo","asc");
		$grid->per_page = 20;

		$grid->column_orderby("C&oacute;digo"      ,$uri      ,"codigo"        ,"align='center'    ");
		$grid->column_orderby("Grupo"              ,"grupo"   ,"grupo"         ,"align='center'    ");
		$grid->column_orderby("Nombre"             ,"nombre"  ,"nombre"        ,"align='left'NOWRAP");	
		$grid->column_orderby("Costo"              ,"costo"   ,"costo"         ,"align='rigth'");
			
		$grid->column("Duplicar"           ,$uri_2    ,"align='center'");

		$grid->add("bienes/bienes/dataedit/create");
		$grid->build();
		
		//$data['content'] = $filter->output.$grid->output;
		$data['filtro']  = $filter->output;
		$data['content'] = $grid->output;
		$data['script'] = script("jquery.js")."\n";
		$data['title']   = "Bienes Nacionales";
		$data["head"]    = $this->rapyd->get_head();
		$this->load->view('view_ventanas', $data);	
	}

	function dataedit($status='',$id='')
 	{
 		//$this->datasis->modulo_id(502,1);
		$this->rapyd->load("dataobject","dataedit");
		
	
		$link=site_url('bienes/bienes/ultimo');
		$link2=site_url('bienes/bienes/sugerir_bienes');
		
		$script ='
			$(function() {
				$(".inputnum").numeric(".");
			});
			
			function ultimo(){
				$.ajax({
					url: "'.$link.'",
					success: function(msg){
					  alert( "El ultimo codigo ingresado fue: " + msg );
					}
				});
			}
		
			function sugerir(){
				$.ajax({
						url: "'.$link2.'",
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

		$do = new DataObject("bienes");
		if($status=="create" && !empty($id)){
			$do->load($id);
			$do->set('codigo', '');
			$do->set('barras', '');
		}
		
		$modbus=array(  'tabla'=> 'grbi',
				'columnas' => array('grupo'=>'Codigo', 'nombre'=>'Nombre' ),
				'filtro'   => array('grupo'=>'Codigo', 'nombre'=>'Nombre' ),
				'retornar' => array('grupo'=>'grupo' ),
				'titulo'   => 'Buscar Grupo de Bienes'			
		);
		
		$bmodbus = $this->datasis->modbus($modbus);

		$edit = new DataEdit("Bien &oacute; Suministro",$do);
		$edit->back_url = site_url("bienes/bienes/filteredgrid");
		$edit->script($script, "modify");
		$edit->script($script, "create");
		
		$ultimo='<a href="javascript:ultimo();" title="Consultar ultimo codigo ingresado"> Consultar ultimo c&oacute;digo</a>';
		$sugerir='<a href="javascript:sugerir();" title="Sugerir un Codigo aleatorio">Sugerir C&oacute;digo </a>';
		$edit->codigo = new inputField("C&oacute;digo", "codigo");
		$edit->codigo->mode="autohide";
		$edit->codigo->maxlength=15;
		$edit->codigo->size =10;
		$edit->codigo->rule ="required|callback_chexiste|trim";
		$edit->codigo->append($sugerir);
		$edit->codigo->append($ultimo);
		
		$edit->barras = new inputField("C&oacute;digo de barras","barras");
		$edit->barras->size = 30;
		$edit->barras->maxlength=15;
		
		$edit->grupo = new inputField("Grupo de Bienes","grupo");
		$edit->grupo->size = 10;
		$edit->grupo->maxlength=6;
		$edit->grupo->append($bmodbus);
				
		$edit->costo =new inputField("Costo", "costo");
		$edit->costo->size = 18;
		$edit->costo->maxlength=15;
		$edit->costo->css_class='inputnum';
		
		$edit->nombre =  new inputField("Nombre", "nombre");
		$edit->nombre->size = 60;
		$edit->nombre->maxlength=60;
		$edit->nombre->rule = "required|trim";

		$edit->descripcion = new textareaField("Descripci&oacute;n", "descripcion");  
		$edit->descripcion->cols = 70;
		$edit->descripcion->rows = 4;
		
		for($i=1;$i<=3;$i++){
			$obj="serial".$i;
			$edit->$obj = new inputField("Serial $i",$obj);
			$edit->$obj->size = 30;
			$edit->$obj->maxlength=30;
		}
		
		$edit->marca =new dropdownField("Marca","marca");
		$edit->marca->option("","");
		$edit->marca->options("SELECT marca,marca a FROM marc");
		$edit->marca->style="width:200px";
		
		$edit->modelo = new inputField("Modelo","modelo");
		$edit->modelo->size = 30;
		$edit->modelo->maxlength=50;
		
		$edit->peso = new inputField("Peso","peso");
		$edit->peso->size = 30;
		$edit->peso->maxlength=15;
		$edit->peso->css_class='inputnum';
		
		$edit->color = new inputField("Color","color");
		$edit->color->size = 30;
		$edit->color->maxlength=30;
		
		$edit->vidautil = new inputField("Vida Util","vidautil");
		$edit->vidautil->size = 30;
		$edit->vidautil->maxlength=8;
		$edit->vidautil->css_class='inputnum';

		
//		$edit->cuenta->rule ="trim|callback_chcuentac";
//		$edit->cuenta->append($cost);
			
		$edit->buttons("modify", "save", "undo", "delete", "back");
		$edit->build();
		
		$data['content'] = $edit->output;
		$data['title']   = "Bienes y Suministros";        
		$data["head"]    = script("jquery.pack.js").$this->rapyd->get_head();
		$this->load->view('view_ventanas', $data);  
	}
	
	function chexiste($codigo){
		$codigo=$this->input->post('codigo');
		$chek=$this->datasis->dameval("SELECT COUNT(*) FROM bienes WHERE codigo='$codigo'");
		if ($chek > 0){
			$nombre=$this->datasis->dameval("SELECT nombre FROM bienes WHERE codigo='$codigo'");
			$this->validation->set_message('chexiste',"El codigo $codigo ya existe para $nombre");
			return FALSE;
		}else {
  		return TRUE;
		}	
	}
	
	function ultimo(){
		$ultimo=$this->datasis->dameval("SELECT codigo FROM bienes ORDER BY codigo DESC");
		echo $ultimo;
	}	
	
	function sugerir_bienes(){
		$ultimo=$this->datasis->dameval("SELECT LPAD(hexa,4,0) FROM serie LEFT JOIN bienes ON LPAD(codigo,4,0)=LPAD(hexa,4,0) WHERE valor<65535 AND codigo IS NULL LIMIT 1");
		echo $ultimo;
	}
}
?>