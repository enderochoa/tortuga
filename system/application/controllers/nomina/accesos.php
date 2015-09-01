<?php require_once(BASEPATH.'application/controllers/validaciones.php');
class Accesos extends validaciones{
	 
	var $_direccion;

	function Accesos(){
		parent::Controller(); 
		$this->load->library("rapyd");
	}
	
	function index(){
    $this->datasis->modulo_id(54,1);
		redirect("nomina/accesos/filteredgrid");
		
	}

	function filteredgrid(){
	
		$protocolo=explode('/',$_SERVER['SERVER_PROTOCOL']);
		$this->_direccion=$protocolo[0].'://'.$_SERVER['SERVER_NAME'].'/fnomina';
		
		$this->rapyd->load("datafilter","datagrid");
		$this->rapyd->uri->keep_persistence();
		$atts = array(
              'width'      => '400',
              'height'     => '320',
              'scrollbars' => 'yes',
              'status'     => 'yes',
              'resizable'  => 'yes',
              'screenx'    => '10',
              'screeny'    => '10'
            );
	
		$filter = new DataFilter("");
		
		$filter->codigo = new inputField("C&oacute;digo", "codigo");
		$filter->codigo->dbname='cacc.codigo';
		$filter->codigo->size = 15;

		$filter->cedula = new inputField("Cedula","cedula");
		$filter->cedula->dbname='pers.cedula';
		$filter->cedula->size = 15;

		$filter->nombre = new inputField("Nombre","nombre");
		$filter->nombre->dbname='pers.nombre';
		$filter->nombre->size = 15;		

		$filter->buttons("reset","search");
		$filter->build();
		
		$ima=$this->_direccion."/<#archivo#>";
		
		$furi = site_url('/nomina/accesos/foto/<#archivo#>');
		$uri  = anchor('nomina/accesos/dataedit/show/<#codigo#>/<#fecha#>/<#hora#>','<#codigo#>');
		$grid = new DataGrid("Lista de Control de Accesos");
		
		$select=array("codigo","fecha","hora","cedula","CONCAT(codigo,DATE_FORMAT(fecha,'-%Y%m%d'),DATE_FORMAT(hora,'%H%i%s'),'.jpg') AS archivo",
		"CONCAT_WS('-',nacional,cedula) AS ci");
		$grid->db->select($select);
		$grid->db->from('cacc');
		$grid->db->orderby('fecha','desc');
		
		$grid->per_page = 20;
		$grid->column_orderby("C&oacute;digo",$uri,"codigo");
		$grid->column_orderby("C&eacute;dula","cedula","cedula");
		$grid->column_orderby("Fecha","<dbdate_to_human><#fecha#></dbdate_to_human>","fecha","align='center'");
		$grid->column_orderby("Hora","hora","hora","align='center'");
		$grid->column("Foto",anchor_popup($ima,"<img src='$ima' width='100' border='0'/>",$atts),'align="center"');
		$grid->add("nomina/accesos/dataedit/create");
		$grid->build();
		
		//$data['content'] = $filter->output.$grid->output;
		$data['filtro']  = $filter->output;
		$data['content'] = $grid->output;
		$data['script'] = script("jquery.js")."\n";
		$data['title']   = " Control de Accesos ";
		$data["head"]    = $this->rapyd->get_head();
		$this->load->view('view_ventanas', $data);
	
	}
	
	function dataedit(){
		$this->rapyd->load("dataedit");
		$edit = new DataEdit("Accesos", "cacc");
		$edit->back_url = site_url("nomina/accesos/filteredgrid");
		
		$edit->post_process('insert','_post_insert');
		$edit->post_process('update','_post_update');
		$edit->post_process('delete','_post_delete');
		
		$pers=array(
		'tabla'   =>'pers',
		'columnas'=>array(
		'codigo'  =>'Codigo',
		'cedula'  =>'Cedula',
		'nombre'  =>'Nombre',
		'apellido' =>'Apellido'),
		'filtro'  =>array('codigo'=>'C&oacute;digo','cedula'=>'Cedula'),
		'retornar'=>array('codigo'=>'codigo','cedula'=>'cedula'),
		'titulo'  =>'Buscar Personal');
					  
		$boton=$this->datasis->modbus($pers);
		
		$edit->codigo   = new inputField("C&oacute;digo", "codigo");
		$edit->codigo->mode="autohide";
		$edit->codigo->maxlength =15;
		$edit->codigo->size =15;
		$edit->codigo->append($boton);
		$edit->codigo->rule = "required|callback_chexiste";
		
		$edit->nacional = new dropdownField("Nacionalidad", "nacional");
		$edit->nacional->style = "width:110px;";
		$edit->nacional->option("V","Venezolano");
		$edit->nacional->option("E","Extranjero");
			  
	  $edit->cedula = new inputField("Cedula Identidad", "cedula");
		$edit->cedula->rule = "strtoupper|callback_chci";
		$edit->cedula->maxlength =13;
		$edit->cedula->size =18;
	  
	  $edit->fecha    = new DateonlyField("Fecha","fecha");
	  $edit->fecha->mode="autohide";
	  $edit->fecha->size =12;
	  $edit->fecha->rule = "required";
	  
		$edit->hora  = new inputField("Hora", "hora");
		$edit->hora->maxlength=8;
		$edit->hora->size=10;
		$edit->hora->mode="autohide";
		$edit->hora->rule='required|callback_chhora';
		$edit->hora->append('hh:mm:ss');

		$edit->buttons("modify", "save", "undo", "delete", "back");
		$edit->build();
	
		$data['content'] = $edit->output;           
    $data['title']   = " Control de Accesos ";        
    $data["head"]    = $this->rapyd->get_head();
    $this->load->view('view_ventanas', $data);  
	}

	function foto(){
		//$archivo=$this->uri->segment(4);
		//if (isset($archivo) and file_exists("/usr/samba/fnomina/$archivo")){
		//	Header("Content-type: image/jpeg");
		//	Header("Pragma: No-cache");
		//	readfile("/usr/samba/fnomina/$archivo");
		//}
		//Header("Content-type: image/gif");
		//Header("Pragma: No-cache");
		//$dir=dirname($_SERVER["SCRIPT_FILENAME"]);
		//readfile("$dir/images/ndisp.gif");
	}
	function _post_insert($do){
		$codigo=$do->get('codigo');
		$cedula=$do->get('cedula');
		logusu('cacc',"ACCESO PARA $codigo CEDULA $cedula CREADO");
	}
	function _post_update($do){
		$codigo=$do->get('codigo');
		$cedula=$do->get('cedula');
		logusu('cacc',"ACCESO PARA $codigo CEDULA $cedula MODIFICADO");
	}
	function _post_delete($do){
		$codigo=$do->get('codigo');
		$cedula=$do->get('cedula');
		logusu('cacc',"ACCESO PARA $codigo CEDULA $cedula ELIMINADO ");
	}
	function chexiste($codigo){
		$codigo=$this->input->post('codigo');
		$fecha=$this->input->post('fecha');
		$hora=$this->input->post('hora');
		$codigo.'codigo'.$fecha.'tipo'.$hora;
		$chek=$this->datasis->dameval("SELECT COUNT(*) FROM cacc WHERE codigo='$codigo' AND fecha='$fecha' AND hora='$hora'");
		if ($chek > 0){
			$nombre=$this->datasis->dameval("SELECT cedula FROM cacc WHERE codigo='$codigo' AND fecha='$fecha' AND hora='$hora'");
			$this->validation->set_message('chexiste',"Acceso para $cedula CODIGO $codigo FECHA $fecha HORA $hora ya existe");
			return FALSE;
		}else {
  		return TRUE;
		}	
	}
}
?>