<?php
class r_conc extends Controller {
	var $titp='Conceptos de Recaudacion';
	var $tits='Concepto de Recaudacion';
	var $url ='recaudacion/r_conc/';
	function r_conc(){
		parent::Controller();
		$this->load->library("rapyd");
		$this->datasis->modulo_id(408,1);
	}
	function index(){
		redirect($this->url."filteredgrid");
	}
	function filteredgrid(){
		$this->rapyd->load('datafilter','datagrid');

		$filter = new DataFilter($this->titp, 'r_conc');
		$filter->db->select(array('r_conc.id','r_presup.partida','r_conc.denomi','r_presup.denomi denomi_part','GROUP_CONCAT(r_concit.denomi) denomiit'));
		$filter->db->join("r_concit","r_concit.id_conc=r_conc.id");
		$filter->db->join("r_presup","r_presup.id=r_conc.id_presup");
		$filter->db->groupby("r_conc.id");

		$filter->id = new inputField('Ref. Detalle','id');
		$filter->id->rule      ='max_length[11]';
		$filter->id->size      =13;
		$filter->id->maxlength =11;
		$filter->id->db_name   ='r_concit.id';
		
		$filter->partida = new inputField('Partida','partida');
		$filter->partida->rule      ='max_length[80]';
		$filter->partida->size      =40;
		$filter->partida->maxlength =40;
		$filter->partida->db_name='r_presup.partida';

		$filter->denomi = new inputField('Denominacion','denomi');
		$filter->denomi->rule      ='max_length[80]';
		$filter->denomi->size      =40;
		$filter->denomi->maxlength =40;
		$filter->denomi->db_name='r_conc.denomi';
		
		$filter->denomiit = new inputField('Denominacion Detalle','denomiit');
		$filter->denomiit->rule      ='max_length[80]';
		$filter->denomiit->size      =40;
		$filter->denomiit->maxlength =40;
		$filter->denomiit->db_name='r_concit.denomi';

		$filter->buttons('reset', 'search');
		$filter->build();

		$uri = anchor($this->url.'dataedit/show/<raencode><#id#></raencode>','<#id#>');

		$grid = new DataGrid('');
		$grid->order_by('r_conc.id');
		$grid->per_page = 80;

		$grid->column_orderby('Ref.'                     ,"$uri"         ,'r_conc.id'       ,'align="left"');
		$grid->column_orderby('Denominacion'             ,"denomi"       ,'r_conc.denomi'   ,'align="left"');
		$grid->column_orderby('Denominacion Detalle'     ,"denomiit"     ,'r_concit.denomi' ,'align="left"');
		$grid->column_orderby('Partida'                  ,"partida"      ,'r_presup.partida','align="left"');
		$grid->column_orderby('Denominacion Partida'     ,"denomi_part"  ,'r_presup.denomi' ,'align="left"');
		

		$grid->add($this->url.'dataedit/create');
		$grid->build();

		$data['filtro']  = $filter->output;
		$data['content'] = $grid->output;
		$data['head']    = $this->rapyd->get_head().script('jquery.js');
		$data['title']   = $this->titp;
		$this->load->view('view_ventanas', $data);

	}
	function dataedit(){
		$this->rapyd->load('datadetails','dataobject');
		
		$do = new DataObject("r_conc");
		$do->rel_one_to_many('r_concit'   , 'r_concit'   , array('id' =>'id_conc'));
		$do->order_by('r_concit',"ano",' ');
		$do->order_by('r_concit',"freval",' ');

		$edit = new DataDetails($this->tits, $do);

		$edit->back_url = site_url($this->url."filteredgrid");

		$edit->post_process('insert','_post_insert');
		$edit->post_process('update','_post_update');
		$edit->post_process('delete','_post_delete');

		$edit->id = new inputField('Ref.','id');
		$edit->id->rule='max_length[11]';
		$edit->id->size =13;
		$edit->id->maxlength =11;
		$edit->id->mode='autohide';
		$edit->id->when=array('show','modify');

		$edit->id_presup = new dropDownField('Partida','id_presup');
		$edit->id_presup->options("SELECT id,CONCAT(partida,'-',denomi) FROM r_presup ORDER BY partida");
		$edit->id_presup->style="width:500px;";
		$edit->id_presup->rule='required';

		$edit->denomi = new inputField('Denominacion','denomi');
		$edit->denomi->rule='max_length[80]';
		$edit->denomi->size =50;
		$edit->denomi->maxlength =80;
		$edit->denomi->rule='required';
		
		/*
		 * DETALLE
		 * */
		 
		$edit->itid =  new inputField("(<#o#>) Referencia", 'id_<#i#>');
		$edit->itid->db_name   = 'id';
		$edit->itid->rel_id    ='r_concit';
		//$edit->itid->when      =array('show');
		$edit->itid->readonly=true;
		$edit->itid->size=1;
		
		$edit->itexpira = new dropDownField('(<#o#>) Exp','expira_<#i#>');
		$edit->itexpira->option('N','Nunca'  );
		$edit->itexpira->option('M','Mensual');
		$edit->itexpira->option('A','Anual');
		$edit->itexpira->option('2','Semestral'    );
		$edit->itexpira->option('T','Trimestral');
		$edit->itexpira->style="width:40px;";
		$edit->itexpira->db_name='expira';
        $edit->itexpira->rel_id ='r_concit';
		
		$edit->itano = new dropDownField('(<#o#>) A&ntilde;o','ano_<#i#>');
		$edit->itano->option('0','');
		for($i=1990;$i<=2016;$i++)
		$edit->itano->option($i,$i);
		$edit->itano->style="width:40px;";
		$edit->itano->db_name='ano';
        $edit->itano->rel_id ='r_concit';
        
        $edit->itfrecuencia = new dropDownField('(<#o#>) frec','frecuencia_<#i#>');
		$edit->itfrecuencia->option('0','Independiente');
		$edit->itfrecuencia->option('1','Anual'        );
		$edit->itfrecuencia->option('2','Semestral'    );
		$edit->itfrecuencia->option('3','Trimestral'   );
		$edit->itfrecuencia->option('4','Mensual'      );
		$edit->itfrecuencia->style="width:40px;";
		$edit->itfrecuencia->db_name='frecuencia';
        $edit->itfrecuencia->rel_id ='r_concit';
        
        $edit->itfreval = new dropDownField('(<#o#>) Val Frec','freval_<#i#>');
		$edit->itfreval->option('0','');
		for($i=1;$i<=12;$i++)
		$edit->itfreval->option($i,$i);
		$edit->itfreval->style="width:40px;";
		$edit->itfreval->db_name='freval';
        $edit->itfreval->rel_id ='r_concit';
        
		$edit->itacronimo = new inputField('(<#o#>) Acronimo','acronimo_<#i#>');
		$edit->itacronimo->rule='max_length[50]';
		$edit->itacronimo->size =5;
		$edit->itacronimo->maxlength =50;
		$edit->itacronimo->db_name='acronimo';
        $edit->itacronimo->rel_id ='r_concit';
        
        
		$edit->itdenomi = new inputField('(<#o#>) Denominacion','denomi_<#i#>');
		$edit->itdenomi->rule='required';
		$edit->itdenomi->size =25;
		$edit->itdenomi->maxlength =80;
		$edit->itdenomi->db_name='denomi';
        $edit->itdenomi->rel_id ='r_concit';
        
        $edit->itrequiere = new dropDownField('(<#o#>) Requiere','requiere_<#i#>');
		$edit->itrequiere->option('','');
		$edit->itrequiere->option('INMUEBLE'  ,'INMUEBLE'   );
		$edit->itrequiere->option('VEHICULO'  ,'VEHICULO'   );
		$edit->itrequiere->option('PATENTE'   ,'PATENTE'    );
		$edit->itrequiere->option('MANUAL'    ,'MANUAL'     );
		$edit->itrequiere->option('INTERESES' ,'INTERESES'  );
		$edit->itrequiere->option('BASE'      ,'BASE'       );
		$edit->itrequiere->option('PUBLICIDAD','PUBLICIDAD' );
		$edit->itrequiere->option('DESCUENTO' ,'DESCUENTO'  );
		$edit->itrequiere->style="width:150px;";
		$edit->itrequiere->db_name='requiere';
        $edit->itrequiere->rel_id ='r_concit';
        
        $edit->itmodo = new dropDownField('(<#o#>) Modo','modo_<#i#>');
		$edit->itmodo->option('MANUAL'    ,'MANUAL'     );
		$edit->itmodo->option('BASE'      ,'BASE'       );
		$edit->itmodo->option('CALCULADO' ,'CALCULADO'  );
		$edit->itmodo->style="width:150px;";
		$edit->itmodo->db_name='modo';
        $edit->itmodo->rel_id ='r_concit';
        
		$edit->itformula = new textareaField('(<#o#>) Formula','formula_<#i#>');
		//$edit->itformula->rule='max_length[8]';
		$edit->itformula->cols = 40;
		$edit->itformula->rows = 2;
		$edit->itformula->db_name='formula';
        $edit->itformula->rel_id ='r_concit';
        
		$edit->buttons('add_rel','add','modify', 'save', 'undo', 'delete', 'back');
		$edit->build();
		#04247094489
		
		$conten["form"]  =&  $edit;
		$data['content'] = $this->load->view('recaudacion/r_conc', $conten,true);
		$data["head"]    = $this->rapyd->get_head().script('jquery.js').script('jquery-ui.js').script("plugins/jquery.numeric.pack.js").script('plugins/jquery.meiomask.js').style('vino/jquery-ui.css');
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
		$mSQL="CREATE TABLE `r_conc` (
		  `id` int(11) NOT NULL,
		  `id_presup` int(11) NOT NULL,
		  `denomi` varchar(80) DEFAULT NULL,
		  PRIMARY KEY (`id`),
		  KEY `FK__r_presup` (`id_presup`)
		) ENGINE=InnoDB DEFAULT CHARSET=utf8";
		$this->db->simple_query($mSQL);
		$query="ALTER TABLE `r_conc`	CHANGE COLUMN `id` `id` INT(11) NOT NULL AUTO_INCREMENT FIRST";
		$this->db->simple_query($query);

		$query="
		CREATE TABLE `r_concit` (
			`id` INT(11) NOT NULL AUTO_INCREMENT,
			`id_conc` INT(11) NOT NULL,
			`ano` INT(11) NOT NULL,
			`acronimo` VARCHAR(50) NOT NULL,
			`denomi` VARCHAR(80) NOT NULL,
			`formula` TEXT NOT NULL,
			`requiere` VARCHAR(50) NULL DEFAULT NULL,
			`deleted` BIT(1) NULL DEFAULT b'0',
			PRIMARY KEY (`id`),
			INDEX `FK__r_conc` (`id_conc`)
		)
		COLLATE='utf8_general_ci'
		ENGINE=InnoDB
		AUTO_INCREMENT=1
		";
		$this->db->simple_query($query);
		
	
		$query="ALTER TABLE `r_concit` ADD COLUMN `frecuencia` SMALLINT NULL DEFAULT '0' AFTER `ano`   ";
		$this->db->simple_query($query);
		$query="ALTER TABLE `r_concit`ADD COLUMN `freval` SMALLINT NULL DEFAULT NULL AFTER `frecuencia`";
		$this->db->simple_query($query);
		$query="ALTER TABLE `r_concit` 	ADD COLUMN `modo` VARCHAR(10) NULL DEFAULT NULL AFTER `requiere`";
		$this->db->simple_query($query);
		$query="ALTER TABLE `r_concit` 	ADD COLUMN `expira` CHAR(1) NULL DEFAULT NULL AFTER `modo`";
		$this->db->simple_query($query);
	}

}
?>
