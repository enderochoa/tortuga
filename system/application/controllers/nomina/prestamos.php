<?php
class prestamos extends Controller {
	
	function prestamos(){
		parent::Controller(); 
		$this->load->library("rapyd");
   }
      function index(){
    	$this->datasis->modulo_id(53,1);
    	redirect("nomina/prestamos/filteredgrid");
    }
	function filteredgrid(){
		$this->rapyd->load("datafilter","datagrid");
		$this->rapyd->uri->keep_persistence();

		$filter = new DataFilter("", 'pres');
		
		$pers=array(
		'tabla'   =>'pers',
		'columnas'=>array(
		'codigo'  =>'Codigo',
		'cedula'  =>'Cedula',
		'nombre'  =>'Nombre',
		'apellido' =>'Apellido'),
		'filtro'  =>array('codigo'=>'C&oacute;digo','cedula'=>'Cedula'),
		'retornar'=>array('codigo'=>'cod_cli'),
		'titulo'  =>'Buscar Personal');
					  
		$boton=$this->datasis->modbus($pers);
		
		$filter->numero = new inputField("N&uacute;mero", "numero");
		$filter->numero->size =10;

		$filter->cod_cli = new inputField("Trabajador","cod_cli");
		$filter->cod_cli->size =5;
		$filter->cod_cli->append($boton);
		
		$filter->buttons("reset","search");
		$filter->build();

		$uri = anchor('nomina/prestamos/dataedit/show/<#cod_cli#>/<#tipo_doc#>/<#numero#>','<#numero#>');

		$grid = new DataGrid("");
		$grid->order_by("numero","asc");
		$grid->per_page = 20;
 
		$grid->column_ORDERBY("N&uacute;mero",$uri,"numero");
		$grid->column_ORDERBY("Enlace"       ,"cod_cli"                                     ,"cod_cli" );
		$grid->column_ORDERBY("C&oacute;digo","codigo"                                      ,"codigo"  );
		$grid->column_ORDERBY("Nombre"       ,"nombre"                                      ,"nombre"  ,"align='left'NOWRAP");
		$grid->column_ORDERBY("Fecha"        ,"<dbdate_to_human><#fecha#></dbdate_to_human>","fecha"   ,"align='center'    ");
		$grid->column_ORDERBY("Tipo"         ,"tipo_doc"                                    ,"tipo_doc");
		$grid->column_ORDERBY("Monto"        ,"monto"                                       ,"monto"   ,"align='right'     ");
		$grid->column_ORDERBY("Cuota"        ,"cuota"                                       ,"cuota"   ,"align='right'     ");
		//$grid->column("Saldo","monto");
//		$grid->column("A partir","apartir");
//		$grid->column("Frecuencia","cadano");
//		$grid->column("Observaciones","observ1");
//		$grid->column(".","oberv2");
//		$grid->column("Pagado","pagado");
						
		$grid->add("nomina/prestamos/dataedit/create");
		$grid->build();
		
		//$data['content'] = $filter->output.$grid->output;
		$data['filtro']  = $filter->output;
		$data['content'] = $grid->output;
		$data['script'] = script("jquery.js")."\n";
		$data['title']   = "Prestamos";
		$data["head"]    = $this->rapyd->get_head();
		$this->load->view('view_ventanas', $data);	

	}
	
	function dataedit() {
 		$this->rapyd->load("dataedit");
 		
		$edit = new DataEdit("Prestamos", "pres");
		$edit->back_url = site_url("nomina/prestamos/filteredgrid");
		
		$edit->post_process('insert','_post_insert');
		$edit->post_process('update','_post_update');
		$edit->post_process('delete','_post_delete');
		
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
		'apellido'=>'Apellido'),
		'filtro'  =>array('codigo'=>'C&oacute;digo','cedula'=>'Cedula'),
		'retornar'=>array('codigo'=>'codigo','nombre'=>'nombre'),
		'titulo'  =>'Buscar Personal');
					  
		$boton1=$this->datasis->modbus($pers);

 		$scli=array(
	  'tabla'   =>'scli',
	  'columnas'=>array(
		'cliente' =>'C&oacute;digo Cliente',
		'nombre'  =>'Nombre',
		'contacto'=>'Contacto'),
	  'filtro'  =>array('cliente'=>'C&oacute;digo Cliente','nombre'=>'Nombre'),
	  'retornar'=>array('cliente'=>'cod_cli'),
	  'titulo'  =>'Buscar Cliente');
		
		$boton=$this->datasis->modbus($scli);
		
		$edit->script($script, "create");
		$edit->script($script, "modify");
		
		$edit->enlase =  new inputField("Enlase","cod_cli");
		$edit->enlase->mode="autohide";
		$edit->enlase->size =7;
		$edit->enlase->maxlength=5;
		$edit->enlase->rule = "required";
		$edit->enlase->append($boton);
		
		$edit->tipo = new dropdownField("Tipo", "tipo_doc");  
		$edit->tipo->option("ND","ND");
		$edit->tipo->option("NC","NC");
	  $edit->tipo->style='width:60px';
		$edit->tipo->mode="autohide";
		$edit->tipo->rule="required";
		
		$edit->numero =  new inputField("N&uacute;mero", "numero");
		$edit->numero->mode="autohide";
		$edit->numero->size =10;
		$edit->numero->maxlength=8;
		$edit->numero->rule = "required|callback_chexiste";
		
		$edit->fecha = new DateField("Fecha","fecha");
		$edit->fecha->size = 12;
		
		$edit->codigo =  new inputField("C&oacute;digo", "codigo");
	  $edit->codigo->size = 15;
	  $edit->codigo->maxlength=15;
	  $edit->codigo->append($boton1);
	  $edit->codigo->rule="required";
		$edit->codigo->group="Trabajador";
		
		$edit->nombre = new inputField("Nombre","nombre");
	  $edit->nombre->size =45;
	  $edit->nombre->maxlength=35;
	  $edit->nombre->group="Trabajador";
	  	
		$edit->monto = new inputField("Saldo","monto");
		$edit->monto->size =17;
		$edit->monto->maxlength=14;
		$edit->monto->css_class='inputnum';
		$edit->monto->rule='numeric';
		$edit->monto->group="Datos de Prestamo";

		$edit->nroctas = new inputField("Nº Cuota","nroctas");
	  $edit->nroctas->size =4;
	  $edit->nroctas->maxlength=2;
	  $edit->nroctas->css_class='inputnum';
		$edit->nroctas->rule='integer';
		$edit->nroctas->group="Datos de Prestamo";

		$edit->cuota = new inputField("Cuota","cuota");
	  $edit->cuota->size = 17;
	  $edit->cuota->maxlength=14;
	  $edit->cuota->css_class='inputnum';
		$edit->cuota->rule='numeric';
		$edit->cuota->group="Datos de Prestamo";
	  	  
		$edit->apartir = new DateonlyField("Cobrar A partir de:","apartir");
    $edit->apartir->size = 12;
    $edit->apartir->group="Datos de Prestamo";
    
		$edit->cadano = new inputField("Frecuencia","cadano");
		$edit->cadano->size =2;
		$edit->cadano->maxlength=1;
		$edit->cadano->group="Datos de Prestamo";
		
		$edit->observ1 = new inputField("Observaciones","observ1");
		$edit->observ1->size =45;
		$edit->observ1->maxlength=46;
		$edit->observ1->group="Datos de Prestamo";		
		
		$edit->observ2 = new inputField("","oberv2");
		$edit->observ2->size = 45;
		$edit->observ2->maxlength=46;
		$edit->observ2->group="Datos de Prestamo";
		
		//$edit->pagado = new inputField("Pagado","pagado");
		//$edit->pagado->size = 1;
									
		$edit->buttons("modify", "save", "undo", "delete", "back");
		$edit->build();

		$data['content'] = $edit->output;           
    $data['title']   = "Prestamos";        
    $data["head"]    = script("jquery.pack.js").script("plugins/jquery.numeric.pack.js").script("plugins/jquery.floatnumber.js").$this->rapyd->get_head();
    $this->load->view('view_ventanas', $data);
	}
	function _post_insert($do){
		$codigo=$do->get('numero');
		$nombre=$do->get('nombre');
		logusu('pres',"PRESTAMO numero $codigo  NOMBRE  $nombre CREADO");
	}
	function _post_update($do){
		$codigo=$do->get('numero');
		$nombre=$do->get('nombre');
		logusu('pres',"PRESTAMO numero $codigo NOMBRE  $nombre  MODIFICADO");
	}
	function _post_delete($do){
		$codigo=$do->get('numero');
		$nombre=$do->get('nombre');
		logusu('pres',"PRESTAMO numero $codigo NOMBRE  $nombre  ELIMINADO ");
	}
	function chexiste($tipo_doc){
		$tipo_doc=$this->input->post('tipo_doc');
		$codigo=$this->input->post('cod_cli');
		$numero=$this->input->post('numero');
		//echo 'numero'.$numero.'codigo'.$codigo.'tipo'.$tipo_doc;
		$chek=$this->datasis->dameval("SELECT COUNT(*) FROM pres WHERE tipo_doc='$tipo_doc' AND numero='$numero' AND cod_cli='$codigo'");
		if ($chek > 0){
			$nombre=$this->datasis->dameval("SELECT nombre FROM pres WHERE cod_cli='$codigo'");
			$this->validation->set_message('chexiste',"Prestamo para $nombre ya existe");
			return FALSE;
		}else {
  		return TRUE;
		}	
	}
}
?>