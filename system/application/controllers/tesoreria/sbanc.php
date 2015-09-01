<?php
class Sbanc extends Controller {

	var $titp='Saldos en Bancos';
	var $tits='Saldo en Banco';
	var $url ='tesoreria/sbanc/';

	function sbanc(){
		parent::Controller();
		$this->load->library("rapyd");
		//$this->datasis->modulo_id(216,1);
	}

	function index(){
		redirect($this->url."filteredgrid");
	}

	function filteredgrid(){
		$this->rapyd->load("datafilter","datagrid","dataobject");

		$where='activo = "S"';
		$mf=$this->datasis->puede(333);
		$mo=$this->datasis->puede(334);
		if($mf && $mo){
		    
		}elseif($mf){
		    $where.=' AND tipocta="F"';
		}elseif($mo){
		    $where.=' AND tipocta<>"F"';
		}
		$mBANC=array(
				'tabla'   =>'banc',
				'columnas'=>array(
					'codbanc' =>'C&oacute;odigo',
					'banco'=>'Banco',
					'numcuent'=>'Cuenta',
					'saldo'=>'Saldo'),
				'filtro'  =>array(
					'codbanc' =>'C&oacute;odigo',
					'banco'=>'Banco',
					'saldo'=>'Saldo',
					'numcuent'=>'Cuenta'),
				'retornar'=>array(
					'codbanc'=>'codbanc',
					'banco'=>'nombreb' ),
				'where'=>$where,
				'titulo'  =>'Buscar Bancos'
				);

		$bBANC=$this->datasis->p_modbus($mBANC,"banc");

		$filter = new DataFilter("");
		$filter->db->select(array("a.saldo","a.codbanc","a.mes","a.ano","b.banco","b.numcuent","a.fecha"));
		$filter->db->from("sbanc a");
		$filter->db->join("banc b ","a.codbanc=b.codbanc");

		$filter->ano = new inputField("A&ntilde;o", 'ano');
		$filter->mes = new inputField("Mes", 'mes');

		$filter->codbanc =  new inputField("Banco", 'codbanc');
		$filter->codbanc-> size     = 5;
		$filter->codbanc-> append($bBANC);
		$filter->codbanc->db_name   ='a.codbanc';
		$filter->codbanc->readnoly  =true;

		$filter->buttons("reset","search");
		$filter->build();

		$uri = anchor($this->url.'dataedit/show/<raencode><#codbanc#></raencode>/<raencode><#fecha#></raencode>','<#codbanc#><#fecha#>');

		$grid = new DataGrid("");
		$grid->order_by("ano desc,mes desc,a.codbanc ");
		$grid->per_page = 20;
		$grid->use_function('substr','str_pad');

		$grid->column_orderby("C&oacute;digo",$uri,"codbanc");
		$grid->column_orderby("C&oacute;digo banco" ,"codbanc"                                ,"codbanc"     ,"align='left'    ");
		$grid->column_orderby("Fecha"        ,"<dbdate_to_human><#fecha#></dbdate_to_human>"                                           ,"fecha"       ,"align='center'    ");
		$grid->column_orderby("Nombre Cuenta","banco"                                         ,"banco"     ,"align='left'    ");
		$grid->column_orderby("Cuenta"       ,"numcuent"                                      ,"numcuent"  ,"align='left'    ");
		
		//$grid->column_orderby("A&ntilde;o"   ,"ano"                                           ,"ano"       ,"align='left'    ");
		//$grid->column_orderby("Mes"          ,"mes"                                           ,"mes"       ,"align='left'    ");
		$grid->column_orderby("Saldo"        ,"<nformat><#saldo#></nformat>"                  ,"saldo"     ,"align='right'   ");

		$grid->add($this->url."dataedit/create");
		$grid->build();

	
		$data['filtro']  = $filter->output;
		$data['content'] = $grid->output;
		$data['script'] = script("jquery.js")."\n";
		$data['title']   = "$this->titp";
		$data["head"]    = $this->rapyd->get_head();
		$this->load->view('view_ventanas', $data);
	}

	function dataedit($duplicar='S',$status='',$codbanc='',$fecha=''){
		//$this->datasis->modulo_id(101,1);
		$this->rapyd->load("dataobject","dataedit");

		$where='activo = "S"';
		$mf=$this->datasis->puede(333);
		$mo=$this->datasis->puede(334);
		if($mf && $mo){
		    
		}elseif($mf){
		    $where.=' AND tipocta="F"';
		}elseif($mo){
		    $where.=' AND tipocta<>"F"';
		}
		$mBANC=array(
				'tabla'   =>'banc',
				'columnas'=>array(
					'codbanc' =>'C&oacute;odigo',
					'banco'=>'Banco',
					'numcuent'=>'Cuenta',
					'saldo'=>'Saldo'),
				'filtro'  =>array(
					'codbanc' =>'C&oacute;odigo',
					'banco'=>'Banco',
					'saldo'=>'Saldo',
					'numcuent'=>'Cuenta'),
				'retornar'=>array(
					'codbanc'=>'codbanc',
					'banco'=>'nombreb' ),
				'where'=>$where,
				'titulo'  =>'Buscar Bancos'
				);

		$bBANC=$this->datasis->p_modbus($mBANC,"banc");

		$script='
			$(".inputnum").numeric(".");
		';

		$do = new DataObject("sbanc");
		//$do->pointer('banc' ,'banc.codbanc=sbanc.codbanc','banc.banco as nombreb','LEFT');

		if($status=="create" && !empty($codbanc) && !empty($fecha) && $duplicar=='S'){
			$do->load(array('codbanc'=>$codbanc,'fecha'=>$fecha));
			$do->set('codbanc', '');
			//$do->set('nombreb', '');
			$do->set('saldo', '');
			//$do->pk  =array('codbanc'=>'','fecha'=>'');
		}

		$edit = new DataEdit($this->tits, $do);

		$edit->back_url = site_url($this->url."filteredgrid");

		$edit->script($script,"create");
		$edit->script($script,"modify");

		//$edit->pre_process('insert'  ,'_valida');
		$edit->post_process('insert','_post_insert');
		$edit->post_process('update','_post_update');
		$edit->post_process('delete','_post_delete');

		$edit->codbanc =  new inputField("Banco", 'codbanc');
		$edit->codbanc->dbname   = "sbanc.codbanc";
		$edit->codbanc-> size    = 5;
		$edit->codbanc-> rule    = "required";
		$edit->codbanc->mode     ="autohide";
		//$edit->codbanc->readonly =true;
		$edit->codbanc-> append($bBANC);
		
		$edit->fecha = new  dateonlyField("Fecha",  "fecha");
		$edit->fecha->dbname   = "sbanc.fecha";
		$edit->fecha->size        =12;
		$edit->fecha->rule        = 'required';
		$edit->fecha->mode        ="autohide";

		$edit->nombreb = new inputField("Nombre", 'nombreb');
		$edit->nombreb->size      = 50;
		$edit->nombreb->in        = "codbanc";

		//$edit->ano = new inputField("A&ntilde;o", 'ano');
		//$edit->ano->size       = 4;
		//$edit->ano->mode     ="autohide";
		//$edit->ano->insertValue= $this->datasis->traevalor("EJERCICIO");
		//$edit->ano-> rule    = "required";

		//$edit->mes = new dropdownField("Mes", 'mes');
		//for($i=1; $i<=12; ++$i)
		//	$edit->mes->option(str_pad($i,2 ,"0" ,STR_PAD_LEFT),str_pad($i,2 ,"0" ,STR_PAD_LEFT));
		//$edit->mes->mode     ="autohide";
		//$edit->mes-> rule    = "required";

		$edit->saldo = new inputField("Saldo", 'saldo');
		$edit->saldo ->css_class ="inputnum";
		$edit->saldo->size = 20;

		$edit->buttons("add","modify","save","delete","undo", "back");
		$edit->build();

		$data['content'] = $edit->output;
		$data['title']   = "$this->tits";
		$data["head"]    = script("jquery.pack.js").script("plugins/jquery.numeric.pack.js").script("plugins/jquery.floatnumber.js").$this->rapyd->get_head();
		$this->load->view('view_ventanas', $data);
	}



	function _post_insert($do){
		$primary =implode(',',$do->pk);
		logusu($do->table,"Creo saldo en banco $primary ");
		redirect($this->url.'dataedit/S/create/'.$do->get('codbanc').'/'.$do->get('fecha'));
	}
	function _post_update($do){
		$primary =implode(',',$do->pk);
		logusu($do->table,"Modifico en banco $primary ");
		redirect($this->url.'dataedit/S/create/'.$do->get('codbanc').'/'.$do->get('fecha'));
	}
	function _post_delete($do){
		$primary =implode(',',$do->pk);
		logusu($do->table,"Elimino en banco $primary ");
	}

	function instala(){
		$query="CREATE TABLE `sbanc` (
			`codbanc` VARCHAR(5) NOT NULL DEFAULT '',
			`ano` CHAR(4) NULL DEFAULT NULL,
			`mes` CHAR(2) NULL DEFAULT NULL,
			`saldo` DECIMAL(19,2) NULL DEFAULT NULL,
			PRIMARY KEY (`codbanc`)
		)
		COLLATE='utf8_general_ci'
		ENGINE=MyISAM
		ROW_FORMAT=DEFAULT
		";
		$this->db->simple_query($query);

		$query="ALTER TABLE `sbanc`  DROP PRIMARY KEY,  ADD PRIMARY KEY (`codbanc`, `ano`, `mes`)";
		$this->db->simple_query($query);
		
		$query="ALTER TABLE `sbanc` ADD COLUMN `fecha` DATE NULL DEFAULT NULL ";
		$this->db->simple_query($query);
		
		$query="ALTER TABLE `sbanc` DROP PRIMARY KEY,	ADD PRIMARY KEY (`codbanc`, `fecha`)";
		$this->db->simple_query($query);
		$query="ALTER TABLE `sbanc` 	ADD COLUMN `fecha` DATE NULL DEFAULT NULL";
		$this->db->simple_query($query);
	}
}
