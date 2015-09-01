<?php //tbanco
class Tban extends Controller {

	function tban() {
		parent::Controller();
		$this->load->library('rapyd');
	}
   
	function index() {
		$this->datasis->modulo_id(43,1);
		redirect('tesoreria/tban/filteredgrid');
	}
  
	function filteredgrid() {
		$this->rapyd->load('datafilter2','datagrid');
		$filter = new DataFilter2('', 'tban');
		
		$filter->codbanc = new inputField('C&oacute;digo', 'cod_banc');
		$filter->codbanc->size=5;
		$filter->codbanc->clause='likerigth';

		$filter->banco = new inputField('Nombre de Banco', 'nomb_banc');
		$filter->banco->size=30;
		$filter->banco->clause='likerigth';

		$filter->buttons('reset','search');
		$filter->build();
		
		$uri = anchor('tesoreria/tban/dataedit/show/<#cod_banc#>','<#cod_banc#>');

		$grid = new DataGrid("");
		$grid->per_page = 20;

		$grid->column_orderby('C&oacute;digo',$uri       ,'cod_banc' );
		$grid->column_orderby('Banco'        ,'nomb_banc','nomb_banc',"align='left' NOWRAP");
		//$grid->column_orderby("Tipo"   ,"tipotra"  ,"tipotra"  );
		//$grid->column_orderby("Formaca","formaca"  ,"formaca"  );

		$grid->add('tesoreria/tban/dataedit/create');
		$grid->build();

		$data['content'] = $filter->output.$grid->output;
		$data['title']   = 'Tabla de Bancos';
		$data['head']    = $this->rapyd->get_head();
		$this->load->view('view_ventanas', $data);
	}

	function dataedit(){
		$this->rapyd->load('dataedit');

		$script ='
		$(function() {
			$(".inputnum").numeric(".");
		});
		';

		$edit = new DataEdit('Tabla de Bancos', 'tban');

		$edit->back_url = site_url('tesoreria/tban/filteredgrid');
		$edit->script($script, 'create');
		$edit->script($script, 'modify');
		
		$edit->post_process('insert','_post_insert');
		$edit->post_process('update','_post_update');
		$edit->post_process('delete','_post_delete');

		$edit->codbanc = new inputField('C&oacute;digo','cod_banc');
		$edit->codbanc->rule = 'required|callback_chexiste';
		$edit->codbanc->mode ='autohide';
		$edit->codbanc->size =5;
		$edit->codbanc->maxlength=3;

		$edit->nombre = new inputField('Nombre del Banco', 'nomb_banc');
		$edit->nombre->size   = 60;
		//$edit->nombre->maxlength=200;
		$edit->nombre->rule = 'strtoupper|required';
		

		$edit->url = new inputField('Direcci&oacute;n WEB','url');
		$edit->url->size =35;
		$edit->url->maxlength=30;
		
		$edit->formatocheque = new inputField("Formato Cheque", "formacheque");
		$edit->formatocheque->size      = 15;
		$edit->formatocheque->maxlenght = 20;
		
		$edit->abreviatura = new inputField("Abreviatura", "abreviatura");
		$edit->abreviatura->size      = 15;
		$edit->abreviatura->maxlenght = 20;

		//$edit->tipo = new dropdownField("Tipo de Transacci&oacute;n", "tipotra");
		//$edit->tipo->option("DE","DE");
		//$edit->tipo->option("NC","NC");
		//$edit->tipo->style='width:80px';
		//
		//$edit->formaca = new dropdownField("Forma de Carga", "formaca");
		//$edit->formaca->option("BRUTA","BRUTA");
		//$edit->formaca->option("NETA","NETA");
		//$edit->formaca->option("NETO","NETO");
		//$edit->formaca->style='width:90px';
		//
		//$edit->tcredito = new inputField("T.Credito","comitc");
		//$edit->tcredito->size =8;
		//$edit->tcredito->maxlength=6;
		//$edit->tcredito->css_class='inputnum';
		//$edit->tcredito->rule='numeric';
		//
		//$edit->tdebito = new inputField("T.Debito","comitd");
		//$edit->tdebito->size =8;
		//$edit->tdebito->maxlength=6;
		//$edit->tdebito->css_class='inputnum';
		//$edit->tdebito->rule='numeric';
		//		
		//$edit->retencion = new inputField("Retenciones","impuesto");
		//$edit->retencion->size =8;
		//$edit->retencion->maxlength=6;
		//$edit->retencion->css_class='inputnum';
		//$edit->retencion->rule='numeric';
		//
		//$edit->idb = new inputField("I.D.B","debito");
		//$edit->idb->size =8;
		//$edit->idb->maxlength=6;
		//$edit->idb->css_class='inputnum';
		//$edit->idb->rule='numeric';

		$edit->buttons('modify', 'save', 'undo', 'delete', 'back');
		$edit->build();

		$data['content'] = $edit->output;
		$data['title']   = 'Tabla de Bancos';
		$data['head']    = script("jquery.pack.js").script("plugins/jquery.numeric.pack.js").script("plugins/jquery.floatnumber.js").$this->rapyd->get_head();
		$this->load->view('view_ventanas', $data);  
	}

	function _post_insert($do){
		$codigo=$do->get('cod_banc');
		$nombre=$do->get('nomb_banc');
		logusu('tban',"BANCO $codigo NOMBRE  $nombre CREADO");
	}

	function _post_update($do){
		$codigo=$do->get('cod_banc');
		$nombre=$do->get('nomb_banc');
		logusu('tban',"BANCO $codigo NOMBRE  $nombre  MODIFICADO");
	}

	function _post_delete($do){
		$codigo=$do->get('cod_banc');
		$nombre=$do->get('nomb_banc');
		logusu('tban',"BANCO $codigo NOMBRE  $nombre  ELIMINADO ");
	}

	function chexiste($codigo){
		$codigo=$this->input->post('cod_banc');
		$chek=$this->datasis->dameval("SELECT COUNT(*) FROM tban WHERE cod_banc='$codigo'");
		if ($chek > 0){
			$banco=$this->datasis->dameval("SELECT nomb_banc FROM tban WHERE cod_banc='$codigo'");
			$this->validation->set_message('chexiste',"El codigo $codigo ya existe para el banco $banco");
			return FALSE;
		}else {
			return TRUE;
		}
	}
	
	function instalar(){
		$query="ALTER TABLE `tban`  ADD COLUMN `formacheque` VARCHAR(25) NULL DEFAULT 'CHEQUE' AFTER `formaca`;";
		$this->db->simple_query($query);
		$query="ALTER TABLE `tban` CHANGE COLUMN `nomb_banc` `nomb_banc` TINYTEXT NULL DEFAULT NULL AFTER `cod_banc`";
		$this->db->simple_query($query);
		$query="ALTER TABLE `tban` 	ADD COLUMN `abreviatura` VARCHAR(25) NULL ";
		$this->db->simple_query($query);
		
	}
}
