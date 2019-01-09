<?php
//cambioiva
class Civa extends Controller {
	var $data_type = null;
	var $data = null;
	 
	function civa(){
		parent::Controller(); 
		$this->load->helper('url');
		$this->load->helper('text');
		$this->load->library("rapyd");
		define ("THISFILE",   APPPATH."controllers/nomina". $this->uri->segment(2).EXT);
	}
	function index(){
	  $this->datasis->modulo_id(63,1);
	      redirect("tesoreria/civa/filteredgrid");
	 }
	function filteredgrid(){
		$this->rapyd->load("datafilter","datagrid");
		$this->rapyd->uri->keep_persistence();

		$filter = new DataFilter("", 'civa');
		
		$filter->fecha = new dateonlyField("Fecha", "fecha",'d/m/Y');
		$filter->fecha->clause  =$filter->fecha->clause="where";
		$filter->fecha->dbformat = "Y-m-d";
		$filter->fecha->size=12;
		
		$filter->tasa= new inputField("Tasa","Tasa");
		$filter->tasa->size=12;
		$filter->tasa->maxlength=6;
	 	
		$filter->buttons("reset","search");
		$filter->build();

		$uri = anchor('tesoreria/civa/dataedit/show/<#fecha#>','<dbdate_to_human><#fecha#></dbdate_to_human>');

		$grid = new DataGrid("");
		$grid->order_by("fecha","asc");
		$grid->per_page = 20;

		$grid->column_orderby("Fecha"         ,$uri       ,"fecha"    ,"align='center'");
		$grid->column_orderby("Tasa"          ,"tasa"     ,"tasa"     ,"align='right' ");
		$grid->column_orderby("Tasa Reducida ","redutasa" ,"redutasa" ,"align='right' ");
		$grid->column_orderby("Tasa Adicional","sobretasa","sobretasa","align='right' ");
			  	  						
		$grid->add("tesoreria/civa/dataedit/create");
		$grid->build();
		//echo $grid->db->last_query();
		
		//$data['content'] = $filter->output.$grid->output;
		$data['filtro']  = $filter->output;
		$data['content'] = $grid->output;
		$data['script'] = script("jquery.js")."\n";
		$data['title']   = "Tasa de IVA";
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

		$edit = new DataEdit("Cambio de IVA", "civa");
		$edit->back_url = site_url("tesoreria/civa/filteredgrid");
		$edit->script($script, "create");
		$edit->script($script, "modify");
		
		$edit->pre_process('insert','_valida');
		$edit->pre_process('update','_valida');
		$edit->post_process('insert','_post_insert');
		$edit->post_process('update','_post_update');
		$edit->post_process('delete','_post_delete');

		$edit->fecha = new DateField("Fecha", "fecha");
		$edit->fecha->mode="autohide";
		$edit->fecha->rule= 'required';
		//$edit->fecha->rule= 'required|callback_chexiste';
		$edit->fecha->size = 12;
		
		$edit->tasa= new inputField("Tasa", "tasa");
		$edit->tasa->size =8;
		$edit->tasa->maxlength=6;
		$edit->tasa->rule= "required|numeric|trim";
		$edit->tasa->css_class='inputnum';
		
		$edit->redutasa = new inputField("Tasa Reducida", "redutasa");
		$edit->redutasa->size =8;
		$edit->redutasa->maxlength=6;
		$edit->redutasa->css_class='inputnum';
		$edit->redutasa->rule='numeric|trim';
		
		$edit->sobretasa =new inputField("Tasa Adicional", "sobretasa");
		$edit->sobretasa->size =8;
		$edit->sobretasa->maxlength=6;
		$edit->sobretasa->css_class='inputnum';
		$edit->sobretasa->rule='numeric|trim';
		
		$edit->buttons("modify", "save", "undo", "delete", "back");
		$edit->build();
 
		$data['content'] = $edit->output;           
		$data['title']   = "Cambio de Iva";
		$data["head"]    = script("jquery.pack.js").script("plugins/jquery.numeric.pack.js").script("plugins/jquery.floatnumber.js").$this->rapyd->get_head();
		$this->load->view('view_ventanas', $data);  
	}
	
	function _valida($do){
		print_r($_POST);
		exit();
	}
	
	function _post_insert($do){
		$fecha=$do->get('fecha');
		$tasa=$do->get('tasa');
		logusu('civa',"CAMBIO DE IVA $fecha TASA $tasa CREADO");
	}
	function _post_update($do){
		$fecha=$do->get('fecha');
		$tasa=$do->get('tasa');
		logusu('civa',"CAMBIO DE IVA $fecha TASA $tasa MODIFICADO");
	}
	function _post_delete($do){
		$fecha=$do->get('fecha');
		$tasa=$do->get('tasa');
		logusu('civa',"CAMBIO DE IVA $fecha TASA $tasa  ELIMINADO ");
	}
	function chexiste($fecha){
		$fecha=$this->input->post('fecha');
		//echo 'aquiii'.$fecha;
		$chek=$this->datasis->dameval("SELECT COUNT(*) FROM civa WHERE fecha='$fecha'");
		if ($chek > 0){
			$tasa=$this->datasis->dameval("SELECT tasa FROM civa WHERE fecha='$fecha'");
			$this->validation->set_message('chexiste',"La fecha $fecha ya existe para la tasa $tasa");
			return FALSE;
		}else {
  		return TRUE;
		}	
	}
}
?>