<?php
require_once(BASEPATH.'application/controllers/validaciones.php');
//almacenes
class bi_bienes extends Controller {


	function bi_bienes(){
		parent::Controller();
		$this->load->library("rapyd");
	}

	function index(){
		redirect("bienes/bi_bienes/filteredgrid");
	}

	function filteredgrid(){
		$this->rapyd->load("datafilter","datagrid");
		$this->rapyd->uri->keep_persistence();

		$filter = new DataFilter("Filtro por Expediente","bi_bienes");

		$filter->id = new inputField("C&oacute;digo", "id");
		$filter->id->size=10;

		$filter->nombre = new inputField("Nombre", "nombre");
		$filter->nombre->size=20;

		$filter->descrip = new inputField("Descripci&oacute;n", "descrip");
		$filter->descrip->size=20;

		$filter->modelo = new inputField("Modelo", "modelo");
		$filter->modelo->size=20;

		$filter->color = new inputField("Color", "color");
		$filter->color->size=20;


		$filter->buttons("reset","search");
		$filter->build();


		$uri = anchor('bienes/bi_bienes/dataedit/show/<#id#>','<#id#>');


		$grid = new DataGrid("Lista de muebles");
		$grid->order_by("id","asc");
		$grid->per_page = 20;


		$grid->column_orderby("Codigo",$uri ,"id"    ,"align='center'");
		$grid->column_orderby("Nombren","nombre" ,"nombre"    ,"align='center'");
		$grid->column_orderby("Color"          ,"color","color"                     ,"align='center'");
		$grid->column_orderby("Modelo"    , "modelo" , "moldelo"  ,"align='center'");
		$grid->column_orderby("Descripci&oacute;n"             ,"descrip","descrip"                    ,"align='left'");
		//              $grid->column_orderby("Duplicar"             ,$uri_2                        ,"align='center'");

		$grid->add("bienes/bi_bienes/dataedit/create");
		$grid->build();

		$data['content'] = $filter->output.$grid->output;
		$data['title']   = "Bienes Muebles";
		$data["head"]    = $this->rapyd->get_head();
		$this->load->view('view_ventanas', $data);
	}

	function dataedit()
	{
		$this->rapyd->load("dataobject","dataedit");


		//              $link=site_url('finventario/bi_edificio/ultimo');
		//              $link2=site_url('finventario/bi_edificio/sugerir');

		$edit = new DataEdit("Muebles", "bi_bienes");
		$edit->back_url = site_url("bienes/bi_bienes/filteredgrid");

		$edit->pre_process('insert'  ,'_valida');
		$edit->pre_process('update'  ,'_valida');

		$edit->id= new inputField("Id", "id");
		$edit->id->mode="autohide";
		$edit->id->when=array('show');


		$edit->nombre = new inputField("Nombre", "nombre");
		$edit->nombre->size=30;
		$edit->nombre->maxlength=30;

		$edit->descrip=new textareaField("Descripci&oacute;n", "descrip");
		$edit->descrip->rows=4;
		$edit->descrip->cols=50;

		$edit->modelo=new inputField("Modelo", "modelo");
		$edit->modelo->size=20;
		$edit->modelo->maxlength=30;

		$edit->color=new inputField("Color", "color");
		$edit->color->size=20;
		$edit->color->maxlength=30;


		$edit->buttons("modify", "save", "undo", "delete", "back");
		$edit->build();

		$data['content'] = $edit->output;
		$data['title']   = "Muebles";
		$data["head"]    = script("jquery.pack.js").$this->rapyd->get_head();
		$this->load->view('view_ventanas', $data);
	}


	function _valida($do){
		$error = '';
		$numero = $do->get('id');

		if(empty($numero)){
			$ntransac = $this->datasis->fprox_id('bi_id','bien');
			$do->set('id','bien_'.$ntransac);
			$do->pk    =array('id'=>'bien_'.$ntransac);
		}
	}

	function instalar(){
		$mSQL="CREATE TABLE IF NOT EXISTS `bi_bienes` (
				`nombre` CHAR(30) NOT NULL COLLATE 'utf8_general_ci',
				`color` CHAR(20) NOT NULL COLLATE 'utf8_general_ci',
				`modelo` CHAR(20) NOT NULL COLLATE 'utf8_general_ci',
				`descrip` CHAR(50) NOT NULL COLLATE 'utf8_general_ci',
				`id` CHAR(8) NOT NULL DEFAULT '' COLLATE 'utf8_general_ci',
				PRIMARY KEY (`id`)
			)
			COLLATE='utf8_general_ci'
			ENGINE=MyISAM
			ROW_FORMAT=DEFAULT";
		$this->db->simple_query($mSQL);
	}

}
?>

