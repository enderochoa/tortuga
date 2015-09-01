<?php
class Cajach extends Controller {

	var $titp='Cajas Chicas';
	var $tits='Caja Chica';
	var $url ='tesoreria/cajach/';

	function cajach(){
		parent::Controller();
		$this->load->library("rapyd");
	}

	function index(){
		redirect($this->url."filteredgrid");
	}

	function filteredgrid(){
		$this->datasis->modulo_id(172,1);
		$this->rapyd->load("datafilter","datagrid");
		$this->rapyd->uri->keep_persistence();

		$filter = new DataFilter("Filtro de $this->titp","cajach");

		$filter->numero = new inputField("N&uacute;mero", "numero");
		$filter->numero->size  =10;
		
		$filter->nombre = new inputField("Nombre", "nombre");
		$filter->nombre->size  =10;

		$filter->descrip = new dateonlyField("Descripci&oacute;n", "descrip");
		$filter->descrip->size=12;

		$filter->custodio = new inputField("Custodio", 'custodio');
		$filter->custodio->size = 6;

		$filter->buttons("reset","search");

		$filter->build();
		$uri = anchor($this->url.'dataedit/show/<#numero#>','<str_pad><#numero#>|8|0|STR_PAD_LEFT</str_pad>');
		
		function sta($status){
			switch($status){
				case "N":return "No";break;
				case "S":return "Si";break;
			}
		}

		$grid = new DataGrid("Lista de ".$this->titp);
		$grid->order_by("numero","desc");
		$grid->per_page = 20;
		$grid->use_function('substr','str_pad');

		$grid->column("N&uacute;mero"    ,$uri);
		$grid->column("Nombre"           ,"nombre" );
		$grid->column("F. Apertura"      ,"<dbdate_to_human><#fapertura#></dbdate_to_human>"   ,"align='center'");
		$grid->column("Custodio"         ,"custodio"                                                        );
		$grid->column("Activa"           ,"<sta><#activo#></sta>");
		
		$grid->add($this->url."dataedit/create");
		$grid->build();

		$data['content'] = $filter->output.$grid->output;
		$data['title']   = " $this->titp ";
		$data["head"]    = $this->rapyd->get_head();
		$this->load->view('view_ventanas', $data);
	}

	function dataedit(){
		//$this->datasis->modulo_id(101,1);
		$this->rapyd->load('dataedit','dataobject');
		
		$script='
			$(".inputnum").numeric(".");
		';

		$do = new DataObject("cajach");

		$edit = new DataEdit($this->tits, $do);

		$edit->back_url = site_url($this->url."filteredgrid");

		$edit->script($script,"create");
		$edit->script($script,"modify");

		$edit->pre_process('insert'  ,'_valida');
		$edit->pre_process('update'  ,'_valida');
		$edit->post_process('insert','_post_insert');
		$edit->post_process('update','_post_update');
		$edit->post_process('delete','_post_delete');

		$edit->numero        = new inputField("N&uacute;mero", "numero");
		$edit->numero->mode  = "autohide";
		$edit->numero->when  = array('show');
		
		$edit->fapertura = new  dateonlyField("Fecha Apertura",  "fapertura");
		$edit->fapertura->insertValue = date('Y-m-d');
		$edit->fapertura->size        =12;
		$edit->fapertura->rule        = 'required';
		$edit->fapertura->mode        = "autohide";

		$edit->nombre = new inputField("Nombre", 'nombre');
		$edit->nombre->size     = 40;
		$edit->nombre->rule     = "required";
		
		$edit->descrip = new textAreaField("Descripci&oacute;n", 'descrip');
		$edit->descrip->rows  = 4;
		$edit->descrip->cols = 70;
	  
	  $edit->custodio =  new inputField("Custodio", 'custodio');
	  $edit->custodio-> size  = 40;
	  $edit->custodio->rule     = "required";

		$edit->maximo = new inputField("Monto Maximo", 'maximo');
		$edit->maximo->mode     = "autohide";		
		$edit->maximo->size     = 8;
		$edit->maximo->css_class='inputnum';
		$edit->maximo->rule     = "required|callback_positivo";

		$edit->activo = new dropdownField("Activo","activo");
		$edit->activo->option("S","Si");
		$edit->activo->option("N","No");
		$edit->activo->style="width:150px";

		$edit->buttons("save","modify","undo", "back");
		$edit->build();

		$smenu['link']   = barra_menu('125');
		$data['smenu']   = $this->load->view('view_sub_menu', $smenu,true);
		$data['content'] = $edit->output;
		
    $data['title']   = " $this->tits ";
    $data["head"]    = script("jquery.pack.js").script("plugins/jquery.numeric.pack.js").script("plugins/jquery.floatnumber.js").$this->rapyd->get_head();
    $this->load->view('view_ventanas', $data);
	}
	
	function _valida($do){
		//$do->set('status','P');
		$numero=$do->get('numero');
		$maximo=$do->get('maximo');
		$saldo =$do->set('saldo' );
		if(empty($numero))$do->set('saldo',$maximo);
	}
	
	function _post_insert($do){
		$numero     = $do->get('numero'  );
		$maximo     = $do->get('maximo'  );
		logusu('cajach',"Creo caja Chica $numero maximo $maximo");
		//redirect($this->url."actualizar/$id");
	}
	function _post_update($do){
		$numero     = $do->get('numero'  );
		$placa      = $do->get('placa'   );
		$solicitante= $do->get('solicitante' );
		logusu('cajach',"Modifico caja chica $numero");
		//redirect($this->url."actualizar/$id");
	}
}