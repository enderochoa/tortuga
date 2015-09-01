<?php
class ac_visitasweb extends Controller {
	var $titp='Control de Visitas';
	var $tits='Control de Visitas';
	var $url ='atencion/ac_visitasweb/';
	function ac_visitasweb(){
		parent::Controller();
		$this->load->library("rapyd");
	}
	function index(){
		redirect($this->url."filteredgrid");
	}
	function filteredgrid(){
		$this->rapyd->load('datafilter','datagrid');

		$filter = new DataFilter($this->titp);
		$filter->db->select(array('ac_visitas.control','ac_visitas.tipo','ac_visitas.id','ac_visitas.cedula','ac_visitas.estampa','ac_visitas.user','ac_visitas.observa','CONCAT_WS(" ",vi_personas.nombre1,vi_personas.apellido1) nombre'));
		$filter->db->from('ac_visitas');
		$filter->db->join('vi_personas','ac_visitas.cedula=vi_personas.cedula');

		$filter->cedula = new inputField('C&eacute;dula','cedula');
		$filter->cedula->rule      ='max_length[11]';
		$filter->cedula->size      =13;
		$filter->cedula->maxlength =11;
		$filter->cedula->db_name   ='ac_visitas.cedula';

		$filter->buttons('reset', 'search');
		$filter->build();

		function stipo($status){
			switch($status){
				case "S":return "Solicitud"         ;
				case "E":return "Entrevista"        ;
				case "I":return "Inspeccci&oacute;n";
				case "F":return "Informaci&oacute;n";
			}
		}

		$grid = new DataGrid('');
		$grid->order_by('id','desc');
		$grid->per_page = 10;
		$grid->use_function('stipo','scontrol');

		$grid->column_orderby('C&eacute;dula'       ,"cedula"                                  ,'cedula'    ,'align="right"');
		$grid->column_orderby('Nombre'              ,"nombre"                                  ,'nombre'   ,'align="left"');
		$grid->column_orderby('Observaci&oacute;n'  ,"observa"                                 ,'observa'   ,'align="left"');
		$grid->column_orderby('Fecha'               ,"<dbdate_to_human><#estampa#></dbdate_to_human>"        ,'estampa'   ,'align="left"');
		$grid->column_orderby('Tipo'                ,"<stipo><#tipo#></stipo>"                 ,'tipo'      ,'align="left"');

		$grid->build();

		//$data['filtro']  = $filter->output;
		$data['content'] = $grid->output;
		$data['head']    = $this->rapyd->get_head();
		$data['title']   = "";
		$this->load->view('view_ventanas_sola', $data);

	}
}
?>
