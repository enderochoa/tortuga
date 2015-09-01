<?php
class ejemplo extends Controller {
	var $titp='';
	var $tits='';
	var $url ='ejemplo/';
	function ejemplo(){
		parent::Controller();
		$this->load->library("rapyd");
		//$this->datasis->modulo_id(216,1);
	}
	function index(){
		redirect($this->url."filteredgrid");
	}
	function filteredgrid(){
		$this->rapyd->load('datafilter','datagrid');

		$filter = new DataFilter($this->titp, 'ejemplo');

		$filter->id = new inputField('id','id');
		$filter->id->rule      ='max_length[10]';
		$filter->id->size      =12;
		$filter->id->maxlength =10;

		$filter->nombre = new inputField('nombre','nombre');
		$filter->nombre->rule      ='max_length[100]';
		$filter->nombre->size      =102;
		$filter->nombre->maxlength =100;

		$filter->naci = new dateField('naci','naci');
		$filter->naci->rule      ='chfecha';
		$filter->naci->size      =10;
		$filter->naci->maxlength =8;

		$filter->sexo = new inputField('sexo','sexo');
		$filter->sexo->rule      ='max_length[1]';
		$filter->sexo->size      =3;
		$filter->sexo->maxlength =1;

		$filter->civil = new inputField('civil','civil');
		$filter->civil->rule      ='max_length[1]';
		$filter->civil->size      =3;
		$filter->civil->maxlength =1;

		$filter->usuario = new inputField('usuario','usuario');
		$filter->usuario->rule      ='max_length[12]';
		$filter->usuario->size      =14;
		$filter->usuario->maxlength =12;

		$filter->color = new inputField('color','color');
		$filter->color->rule      ='max_length[12]';
		$filter->color->size      =14;
		$filter->color->maxlength =12;

		$filter->piel = new inputField('piel','piel');
		$filter->piel->rule      ='max_length[1]';
		$filter->piel->size      =3;
		$filter->piel->maxlength =1;

		$filter->trabaja = new inputField('trabaja','trabaja');
		$filter->trabaja->rule      ='max_length[1]';
		$filter->trabaja->size      =3;
		$filter->trabaja->maxlength =1;

		$filter->sueldo = new inputField('sueldo','sueldo');
		$filter->sueldo->rule      ='max_length[19]|numeric';
		$filter->sueldo->css_class ='inputnum';
		$filter->sueldo->size      =21;
		$filter->sueldo->maxlength =19;

		$filter->observa = new textareaField('observa','observa');
		$filter->observa->rule      ='max_length[8]';
		$filter->observa->cols = 70;
		$filter->observa->rows = 4;

		$filter->blog = new textareaField('blog','blog');
		$filter->blog->rule      ='max_length[8]';
		$filter->blog->cols = 70;
		$filter->blog->rows = 4;

		$filter->modifi = new dateField('modifi','modifi');
		$filter->modifi->rule      ='chfecha';
		$filter->modifi->size      =10;
		$filter->modifi->maxlength =8;

		$filter->foto = new inputField('foto','foto');
		$filter->foto->rule      ='max_length[200]';
		$filter->foto->size      =202;
		$filter->foto->maxlength =200;

		$filter->buttons('reset', 'search');
		$filter->build();

		$uri = anchor($this->url.'dataedit/show/<raencode><#id#></raencode>','<#id#>');

		$grid = new DataGrid('');
		$grid->order_by('id');
		$grid->per_page = 40;

		$grid->column_orderby('id',"$uri",'id','align="left"');
		$grid->column_orderby('nombre',"nombre",'nombre','align="left"');
		$grid->column_orderby('naci',"<dbdate_to_human><#naci#></dbdate_to_human>",'naci','align="center"');
		$grid->column_orderby('sexo',"sexo",'sexo','align="left"');
		$grid->column_orderby('civil',"civil",'civil','align="left"');
		$grid->column_orderby('usuario',"usuario",'usuario','align="left"');
		$grid->column_orderby('color',"color",'color','align="left"');
		$grid->column_orderby('piel',"piel",'piel','align="left"');
		$grid->column_orderby('trabaja',"trabaja",'trabaja','align="left"');
		$grid->column_orderby('sueldo',"<nformat><#sueldo#></nformat>",'sueldo','align="right"');
		$grid->column_orderby('observa',"observa",'observa','align="left"');
		$grid->column_orderby('blog',"blog",'blog','align="left"');
		$grid->column_orderby('modifi',"<dbdate_to_human><#modifi#></dbdate_to_human>",'modifi','align="center"');
		$grid->column_orderby('foto',"foto",'foto','align="left"');

		$grid->add($this->url.'dataedit/create');
		$grid->build();

		$data['filtro']  = $filter->output;
		$data['content'] = $grid->output;
		$data['head']    = $this->rapyd->get_head().script('jquery.js');
		$data['title']   = $this->titp;
		$this->load->view('view_ventanas', $data);

	}
	function dataedit(){
		$this->rapyd->load('dataedit');

		$edit = new DataEdit($this->tits, 'ejemplo');

		$edit->back_url = site_url($this->url."filteredgrid");

		$edit->post_process('insert','_post_insert');
		$edit->post_process('update','_post_update');
		$edit->post_process('delete','_post_delete');

		$edit->id = new inputField('id','id');
		$edit->id->rule='max_length[10]';
		$edit->id->size =12;
		$edit->id->maxlength =10;

		$edit->nombre = new inputField('nombre','nombre');
		$edit->nombre->rule='max_length[100]';
		$edit->nombre->size =102;
		$edit->nombre->maxlength =100;

		$edit->naci = new dateField('naci','naci');
		$edit->naci->rule='chfecha';
		$edit->naci->size =10;
		$edit->naci->maxlength =8;

		$edit->sexo = new inputField('sexo','sexo');
		$edit->sexo->rule='max_length[1]';
		$edit->sexo->size =3;
		$edit->sexo->maxlength =1;

		$edit->civil = new inputField('civil','civil');
		$edit->civil->rule='max_length[1]';
		$edit->civil->size =3;
		$edit->civil->maxlength =1;

		$edit->usuario = new autoUpdateField('usuario',$this->session->userdata('usuario'),$this->session->userdata('usuario'));

		$edit->color = new inputField('color','color');
		$edit->color->rule='max_length[12]';
		$edit->color->size =14;
		$edit->color->maxlength =12;

		$edit->piel = new inputField('piel','piel');
		$edit->piel->rule='max_length[1]';
		$edit->piel->size =3;
		$edit->piel->maxlength =1;

		$edit->trabaja = new inputField('trabaja','trabaja');
		$edit->trabaja->rule='max_length[1]';
		$edit->trabaja->size =3;
		$edit->trabaja->maxlength =1;

		$edit->sueldo = new inputField('sueldo','sueldo');
		$edit->sueldo->rule='max_length[19]|numeric';
		$edit->sueldo->css_class='inputnum';
		$edit->sueldo->size =21;
		$edit->sueldo->maxlength =19;

		$edit->observa = new textareaField('observa','observa');
		$edit->observa->rule='max_length[8]';
		$edit->observa->cols = 70;
		$edit->observa->rows = 4;

		$edit->blog = new textareaField('blog','blog');
		$edit->blog->rule='max_length[8]';
		$edit->blog->cols = 70;
		$edit->blog->rows = 4;

		$edit->modifi = new dateField('modifi','modifi');
		$edit->modifi->rule='chfecha';
		$edit->modifi->size =10;
		$edit->modifi->maxlength =8;

		$edit->foto = new inputField('foto','foto');
		$edit->foto->rule='max_length[200]';
		$edit->foto->size =202;
		$edit->foto->maxlength =200;

		$edit->buttons('add','modify', 'save', 'undo', 'delete', 'back');
		$edit->build();
		$data['content'] = $edit->output;
		$data['head']    = $this->rapyd->get_head();
		$data['title']   = $this->tits;
		$this->load->view('view_ventanas', $data);

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
		$mSQL="CREATE TABLE `ejemplo` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `nombre` varchar(100) COLLATE utf8_general_ci NOT NULL,
  `naci` date NOT NULL,
  `sexo` char(1) COLLATE utf8_general_ci NOT NULL,
  `civil` char(1) COLLATE utf8_general_ci NOT NULL,
  `usuario` varchar(12) COLLATE utf8_general_ci NOT NULL,
  `color` varchar(12) COLLATE utf8_general_ci NOT NULL,
  `piel` varchar(1) COLLATE utf8_general_ci NOT NULL,
  `trabaja` char(1) COLLATE utf8_general_ci NOT NULL,
  `sueldo` decimal(19,2) NOT NULL,
  `observa` tinytext COLLATE utf8_general_ci NOT NULL,
  `blog` longtext COLLATE utf8_general_ci NOT NULL,
  `modifi` date NOT NULL,
  `foto` varchar(200) COLLATE utf8_general_ci NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=5 DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci";
		$this->db->simple_query($mSQL);
	}

}
?>
