<?php
class bancse extends Controller {
	var $titp='Cerrar Meses';
	var $tits='Cerrar Mes';
	var $url ='tesoreria/bancse/';
	function bancse(){
		parent::Controller();
		$this->load->library("rapyd");
		$this->datasis->modulo_id(335,1);
	}
	function index(){
		redirect($this->url."filteredgrid");
	}
	function filteredgrid(){
		$this->rapyd->load('datafilter','datagrid');
                
        $where='activo = "S"';
		-$mf=$this->datasis->puede(333);
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
					'codbanc'=>'codbanc'),
				'where'=>$where,
				'titulo'  =>'Buscar Bancos'
				);

		$bBANC=$this->datasis->p_modbus($mBANC,"banc");

		$filter = new DataFilter($this->titp);
                $filter->db->from("bancse a");
		$filter->db->join("banc b ","a.codbanc=b.codbanc");

		$filter->codbanc = new inputField('codbanc','codbanc');
		$filter->codbanc->size      =6;
		$filter->codbanc-> append($bBANC);
		$filter->codbanc->db_name   ='a.codbanc';

		$filter->mes = new inputField('mes','mes');
		$filter->mes->rule      ='max_length[2]';
		$filter->mes->size      =4;
		$filter->mes->maxlength =2;

		$filter->buttons('reset', 'search');
		$filter->build();

		$uri = anchor($this->url.'dataedit/show/<raencode><#id#></raencode>','<#id#>');

		$grid = new DataGrid('');
		$grid->order_by('id');
		$grid->per_page = 40;

		$grid->column_orderby('Referencia'   ,$uri       ,'id'        ,'align="left"');
		$grid->column_orderby('Banco'        ,"codbanc"  ,'codbanc'   ,'align="left"');
        $grid->column_orderby("Nombre Cuenta","banco"    ,"banco"     ,"align='left'");
		$grid->column_orderby("Cuenta"       ,"numcuent" ,"numcuent"  ,"align='left'");
		$grid->column_orderby('Mes'          ,"mes"      ,'mes'       ,'align="left"');

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
					'codbanc'=>'codbanc'),
				'titulo'  =>'Buscar Bancos'
				);

		$bBANC=$this->datasis->p_modbus($mBANC,"banc");

		$edit = new DataEdit($this->tits, 'bancse');

		$edit->back_url = site_url($this->url."filteredgrid");

		$edit->post_process('insert','_post_insert');
		$edit->post_process('update','_post_update');
		$edit->post_process('delete','_post_delete');

		$edit->id = new inputField('Referencia','id');
		$edit->id->mode ='autohide';
		$edit->id->when =array('show');

		$edit->codbanc = new inputField('Banco','codbanc');
		$edit->codbanc->size      =10;
		$edit->codbanc->maxlength =5;
		$edit->codbanc->append($bBANC);

		$edit->mes = new dropdownField("Mes", 'mes');
		for($i=1; $i<=12; ++$i)
			$edit->mes->option(str_pad($i,2 ,"0" ,STR_PAD_LEFT),str_pad($i,2 ,"0" ,STR_PAD_LEFT));
		$edit->mes->mode     ="autohide";
		$edit->mes-> rule    = "required";

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
		$mSQL="CREATE TABLE `bancse` (
                `id` int(11) NOT NULL AUTO_INCREMENT,
                `codbanc` varchar(5) NOT NULL DEFAULT '',
                `mes` char(2) NOT NULL DEFAULT '',
                PRIMARY KEY (`id`)
              ) ENGINE=MyISAM DEFAULT CHARSET=utf8";
		$this->db->simple_query($mSQL);
		
	}

}
?>
