<?php
class aumentosueldo extends Controller {
	
	function aumentosueldo(){
		parent::Controller(); 
		$this->load->library("rapyd");
   }

    function index(){
    	$this->datasis->modulo_id(48,1);	
    	redirect("nomina/aumentosueldo/filteredgrid");
    }

	function filteredgrid(){
		$this->rapyd->load("datafilter2","datagrid");
		$this->rapyd->uri->keep_persistence();

		$pers=array(
		'tabla'   =>'pers',
		'columnas'=>array(
		'codigo'  =>'Codigo',
		'cedula'  =>'Cedula',
		'nombre'  =>'Nombre',
		'apellido' =>'Apellido'),
		'filtro'  =>array('codigo'=>'C&oacute;digo','cedula'=>'Cedula'),
		'retornar'=>array('codigo'=>'codigo'),
		'titulo'  =>'Buscar Personal');
					  
		$boton=$this->datasis->modbus($pers);


		$filter = new DataFilter2("", 'ausu');
		
		$filter->codigo = new inputField("Codigo","codigo");
		$filter->codigo->size=15;
		$filter->codigo->append($boton);
		$filter->codigo->clause = "likerigth";
		
		$filter->fecha = new DateonlyField("Fecha","fecha");
		$filter->fecha->dbformat = "Y-m-d";
		$filter->fecha->size=12;
		
		$filter->buttons("reset","search");
		$filter->build();

		$uri = anchor('nomina/aumentosueldo/dataedit/show/<#codigo#>/<raencode><#fecha#></raencode>','<#codigo#>');

		$grid = new DataGrid("");
		$grid->order_by("codigo","asc");
		$grid->per_page = 20;

		$grid->column_orderby("C&oacute;digo"  ,$uri,"codigo");
		$grid->column_orderby("Nombre"         ,"nombre"                                          ,"nombre" ,"align='left'NOWRAP");
		$grid->column_orderby("Fecha"          ,"<dbdate_to_human><#fecha#></dbdate_to_human>"    ,"fecha"  ,"align='center'    ");
		$grid->column_orderby("Sueldo anterior","<number_format><#sueldoa#>|2|,|.</number_format>","sueldoa","align='right'     ");
		$grid->column_orderby("Sueldo nuevo"   ,"<number_format><#sueldo#>|2|,|.</number_format>" ,"sueldo" ,"align='right'     ");
		$grid->column_orderby("Observaciones"  ,"observ1"                                         ,"observ1","align='left'NOWRAP");
		$grid->column_orderby(".."             ,"oberv2"                                          ,"oberv2" ,"align='left'NOWRAP");
			
		$grid->add("nomina/aumentosueldo/dataedit/create");
		$grid->build();
		
		//$data['content'] = $filter->output.$grid->output;
		$data['filtro']  = $filter->output;
		$data['content'] = $grid->output;
		$data['script'] = script("jquery.js")."\n";
		$data['title']   = "Aumentos de Sueldo";
		$data["head"]    = $this->rapyd->get_head();
		$this->load->view('view_ventanas', $data);

	}

	function dataedit()
 	{
		$this->rapyd->load("dataedit");
		
		$script ='
		$(function() {
			$(".inputnum").numeric(".");
		});
		';
		
		$pers=array(
		'tabla'   =>'pers',
		'columnas'=>array(
		'codigo'  =>'Codigo',
		'cedula'  =>'Cedula',
		'nombre'  =>'Nombre',
		'apellido' =>'Apellido'),
		'filtro'  =>array('codigo'=>'C&oacute;digo','cedula'=>'Cedula'),
		'retornar'=>array('codigo'=>'codigo','nombre'=>'nombre'),
		'titulo'  =>'Buscar Personal');
					  
		$boton=$this->datasis->modbus($pers);
		
		$edit = new DataEdit("Aumentos de Sueldo", "ausu");
		$edit->back_url = site_url("nomina/aumentosueldo/filteredgrid");
		$edit->script($script, "create");
		$edit->script($script, "modify");
		
		$edit->post_process('insert','_post_insert');
		$edit->post_process('update','_post_update');
		$edit->post_process('delete','_post_delete');
		//$edit->post_process('insert','_post');
		//$edit->post_process('update','_post');
		//$edit->post_process('delete','_post');
	  		
		$edit->codigo =   new inputField("Codigo","codigo");
		$edit->codigo->size = 15;
		$edit->codigo->append($boton);
		$edit->codigo->mode="autohide";
		$edit->codigo->maxlength=15;
		$edit->codigo->rule="required|callback_chexiste";
		$edit->codigo->group="Trabajador";
		
		$edit->nombre =  new inputField("Nombre", "nombre");
		$edit->nombre->size =40;
		$edit->nombre->maxlength=30;
		$edit->nombre->group="Trabajador";		
		
		$edit->fecha = new dateField("Apartir de la nomina", "fecha","d/m/Y");
		$edit->fecha->mode="autohide";
		$edit->fecha->size = 12;
		$edit->fecha->dbformat    = 'Ymd';
		$edit->fecha->rule ="required|callback_fpositiva";
		
		$edit->sueldoa =   new inputField("Sueldo anterior", "sueldoa");
		$edit->sueldoa->size = 14;
		$edit->sueldoa->css_class='inputnum';
		$edit->sueldoa->rule='callback_positivoa';
		$edit->sueldoa->maxlength=11;
		
		$edit->sueldo =   new inputField("Sueldo nuevo", "sueldo");
		$edit->sueldo->size = 14;
		$edit->sueldo->css_class='inputnum';
		$edit->sueldo->rule='callback_positivo';
		$edit->sueldo->maxlength=11;
		
		$edit->observ1 =   new inputField("Observaciones", "observ1");
		$edit->observ1->size = 51;
		$edit->observ1->maxlength=46;
		
		$edit->oberv2 = new inputField("", "oberv2");
		$edit->oberv2->size =51;
		$edit->oberv2->maxlength=46;
		
		$edit->buttons("modify", "save", "undo", "delete", "back");
		$edit->build();

		$data['content'] = $edit->output;           
    $data['title']   = "Aumentos de Sueldo";        
    $data["head"]    = script("jquery.pack.js").script("plugins/jquery.numeric.pack.js").script("plugins/jquery.floatnumber.js").$this->rapyd->get_head();
    $this->load->view('view_ventanas', $data);  
	}
	function _post_insert($do){
		$codigo=$do->get('codigo');
		$nombre=$do->get('nombre');
		$fecha=$do->get('fecha');
		logusu('ausu',"AUMENTO DE SUELDO A $codigo NOMBRE  $nombre FECHA $fecha CREADO");
	}
	function _post_update($do){
		$codigo=$do->get('codigo');
		$nombre=$do->get('nombre');
		$fecha=$do->get('fecha');
		logusu('ausu',"AUMENTO DE SUELDO A $codigo NOMBRE  $nombre FECHA $fecha MODIFICADO");
	}
	function _post_delete($do){
		$codigo=$do->get('codigo');
		$nombre=$do->get('nombre');
		$fecha=$do->get('fecha');
		logusu('ausu',"AUMENTO DE SUELDO A $codigo NOMBRE  $nombre FECHA $fecha ELIMINADO ");
	}
	function chexiste($fecha){
		$fecha=$this->input->post('fecha');
		$codigo=$this->input->post('codigo');
		
		$chek=$this->datasis->dameval("SELECT COUNT(*) FROM ausu WHERE codigo='$codigo' AND fecha='$fecha'");
		if ($chek > 0){
			$nombre=$this->datasis->dameval("SELECT nombre FROM ausu WHERE codigo='$codigo'");
			$this->validation->set_message('chexiste',"El aumento para $codigo $nombre fecha $fecha ya existe");
			return FALSE;
		}else {
  		return TRUE;
		}	
	}
	
	function positivo($valor){
		if ($valor <= 0){
			$this->validation->set_message('positivo',"El campo Sueldo Nuevo debe ser positivo");
			return FALSE;
		}
  	return TRUE;
	}
	
	function positivoa($valor){
		if ($valor <= 0){
			$this->validation->set_message('positivoa',"El campo Sueldo Anterior debe ser positivo");
			return FALSE;
		}
  	return TRUE;
	}
	
	function fpositiva($valor){
		if ($valor < date('Ymd')){
			$this->validation->set_message('fpositiva',"El campo Apartir de la nomina, Debe ser una nomina futura");
			return FALSE;
		}
  	return TRUE;
	}
	
	function _post($do){
	
		$codigo=$do->get('codigo');
		$fecha =$do->get('fecha');
		redirect('nomina/aumentosueldo/dataedit/show/'.$codigo.'/'.raencode($fecha));
		echo 'nomina/aumentosueldo/dataedit/show/'.$codigo.'/'.raencode($fecha);
		exit;
	}
	
	function instalar(){
		$mSQL="ALTER TABLE ausu ADD PRIMARY KEY (codigo,fecha);";
		$this->db->simple_query($mSQL);	
	}
}
?>