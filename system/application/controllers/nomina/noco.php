<?php
class noco extends Controller {

var $titp  = 'Contratos Laborales';
var $tits  = 'Contrato laboral';
var $url   = 'nomina/noco/';

function noco(){
	parent::Controller();
	$this->load->library("rapyd");
	
	$this->datasis->modulo_id(59,1);
}

	function index(){
		redirect($this->url."filteredgrid");
	}

	function filteredgrid(){
		
		$this->rapyd->load("datafilter2","datagrid");
		
		$filter = new DataFilter2("","noco");
				
		$filter->codigo = new inputField("Codigo", "codigo");
		$filter->codigo->size=12;
		$filter->codigo->clause="likerigth";
		
		$filter->tipo = new dropdownField("Orden de ", "tipo");
		$filter->tipo->option("","");
		$filter->tipo->option("Q","Quincenal");
		$filter->tipo->option("M","mensual"  );
		$filter->tipo->option("S","Semanal"  );
		$filter->tipo->style="width:100px;";
				
		$filter->nombre = new inputField("Nombre", "nombre");
		$filter->nombre->size=12;
		
		$filter->observa1 = new inputField("Observacion", 'observa1');
		$filter->observa1->size = 6;
		
		$filter->observa2 = new inputField("", 'observa2');
		$filter->observa2->size = 6;
		
		$filter->buttons("reset","search");
		
		$filter->build();
		$uri = anchor($this->url.'dataedit/show/<#codigo#>','<#codigo#>');
				
		function tipo($tipo){
			switch($tipo){
				case "S":return "Semanal"    ;break;
				case "M":return "Mensual"    ;break;
				case "Q":return "Quincenal"  ;break;
				case "O":return "Otro"       ;break;
			}
		}
		
		$grid = new DataGrid("");
		$grid->order_by("codigo","desc");
		$grid->per_page = 20;
		$grid->use_function('tipo');
		
		$grid->column_orderby("C&oacute;digo",$uri                   ,"codigo" );
		$grid->column_orderby("Tipo"         ,"<tipo><#tipo#></tipo>","tipo"    ,"align='center'       ");
		$grid->column_orderby("nombre"       ,"nombre"               ,"nombre"  ,"align='left'NOWRAP   ");
		$grid->column_orderby("Observa"      ,"observa1"             ,"observa1","align='left'NOWRAP   ");
		$grid->column_orderby(""             ,"observa2"             ,"observa2","align='left'NOWRAP   ");
		
		//echo $grid->db->last_query();
		$grid->add($this->url."dataedit/create");
		$grid->build();
		
		//$data['content'] = $filter->output.$grid->output;
		$data['filtro']  = $filter->output;
		$data['content'] = $grid->output;
		$data['script'] = script("jquery.js")."\n";
		$data['title']   = " $this->titp ";
		$data["head"]    = $this->rapyd->get_head();
		$this->load->view('view_ventanas', $data);
	}
	
	function dataedit(){
		
		$this->rapyd->load('dataobject','datadetails');
		
		$modbus=array(
			'tabla'   =>'conc',
			'columnas'=>array(
				'concepto'      =>'C&oacute;digo',
				'descrip'     =>'Descripci&oacute;n',
				'tipo'     =>'Tipo',
				'formula'  =>'Formula'
				),
			'filtro'  =>array(
				'concepto'=>'C&oacute;digo',
				'descrip' =>'Descripci&oacute;n'
				),
			'retornar'=>array(
				'concepto' =>'concepto_<#i#>',
				'descrip'  =>'descripp_<#i#>',
				'tipo'     =>'tipop_<#i#>',
				'formula'  =>'formulap_<#i#>'
			),
			'p_uri'=>array(
				4=>'<#i#>'
			),
			'script'=>array(
				'post_modbus(<#i#>)'
			),
			'titulo'  =>'Busqueda de partidas');

		$btn=$this->datasis->p_modbus($modbus,'<#i#>');
		
		$link = site_url('presupuesto/presupuesto/get_tipo');
		
		$do = new DataObject("noco");
		$do->rel_one_to_many('itnoco', 'itnoco', array('codigo'=>'codigo'));
		$do->rel_pointer('itnoco','conc' ,'itnoco.concepto = conc.concepto','conc.descrip descripp,conc.formula formulap,conc.tipo tipop');
		$do->order_by('itnoco','itnoco.orden',' ');

		$edit = new DataDetails($this->tits, $do );
		$edit->back_url = site_url($this->url."/index");
		$edit->set_rel_title('itnoco','Rubro <#o#>');
			
		//$edit->pre_process('update'  ,'_valida');
		//$edit->pre_process('insert'  ,'_valida');
		//$edit->post_process('insert'  ,'_post');
		//$edit->post_process('update'  ,'_post');
	
		$edit->codigo  = new inputField("C&oacute;digo", "codigo");
		$edit->codigo->maxlength = 5;
		$edit->codigo->size      = 5;
		$edit->codigo->rule      = "trim|required|callback_chexiste";
		$edit->codigo->mode="autohide";
		//$edit->codigo->when=array('show','create');
	
		$edit->tipo = new dropdownField("Tipo ", "tipo");
		$edit->tipo->option("Q","Quincenal");
		$edit->tipo->option("M","mensual"  );
		$edit->tipo->option("S","Semanal"  );
		$edit->tipo->option("O","Otro");
		$edit->tipo->style="width:100px;";
	
		$edit->nombre = new inputField("Nombre", "nombre");
		$edit->nombre->size=60;
		$edit->nombre->maxlength = 40;
		 
		$edit->observa1 = new inputField("Observacion", 'observa1');
		$edit->observa1->size = 40;
		$edit->observa1->maxlength = 40;
		 
		$edit->observa2 = new inputField("", 'observa2');
		$edit->observa2->size = 40;
		$edit->observa2->maxlength = 40;
		
		$edit->itorden = new dropDownField("Orden", "orden_<#i#>");
		$edit->itorden->option(str_pad(21,2,0,STR_PAD_LEFT),21);
		for($i=1;$i<=20;$i++){
			$edit->itorden->option(str_pad($i,2,0,STR_PAD_LEFT),$i);
		}
		
		$edit->itorden->db_name='orden';
		$edit->itorden->readonly=TRUE;
		$edit->itorden->rel_id       ='itnoco';
		$edit->itorden->style="width:50px;";
		$edit->itorden->onchange="ordenar()";
		
		$edit->itconcepto = new inputField("(<#o#>) Concepto", "concepto_<#i#>");
		$edit->itconcepto->rule      = "callback_itconcepto";
		$edit->itconcepto->size=12;
		$edit->itconcepto->append($btn);
		$edit->itconcepto->db_name='concepto';
		$edit->itconcepto->rel_id ='itnoco';
		$edit->itconcepto->type    ='inputhidden';
		
		$edit->itdescripp = new inputField("(<#o#>) Descripcion", "descripp_<#i#>");
		$edit->itdescripp->size    =20;
		$edit->itdescripp->db_name ='descripp';
		$edit->itdescripp->rel_id  ='itnoco';
		$edit->itdescripp->pointer = true;
		$edit->itdescripp->readonly=true;
		$edit->itdescripp->type    ='inputhidden';

		$edit->ittipop = new inputField("(<#o#>) Tipo", "tipop_<#i#>");
		$edit->ittipop->size    =2;
		$edit->ittipop->db_name ='tipop';
		$edit->ittipop->rel_id  ='itnoco';
		$edit->ittipop->pointer = true;
		$edit->ittipop->readonly=true;
		$edit->ittipop->type    ='inputhidden';

		$edit->itformulap = new inputField("(<#o#>) Formula", "formulap_<#i#>");
		$edit->itformulap->size    = 20;
		$edit->itformulap->db_name = 'formulap';
		$edit->itformulap->rel_id  = 'itnoco';
		$edit->itformulap->pointer = true;
		$edit->itformulap->readonly= true;
		$edit->itformulap->type    ='inputhidden';
						
		$edit->buttons("save","modify","delete","undo","back","add_rel");
		$edit->build();
		
		$smenu['link']=barra_menu('415');
		$data['smenu'] = $this->load->view('view_sub_menu', $smenu,true);
		$conten["form"]  =&  $edit;
		$data['content'] = $this->load->view('view_noco', $conten,true);
		//$data['content'] = $edit->output;
		$data['title']   = " $this->tits ";
		$data["head"]    = $this->rapyd->get_head().script('jquery.js').script("plugins/jquery.numeric.pack.js");
		$this->load->view('view_ventanas', $data);
	}

	function itconcepto($concepto){
		$concepto   = $this->db->escape($concepto);
		$cana=$this->datasis->dameval("SELECT COUNT(*) FROM conc WHERE concepto = $concepto  ");
		if($cana > 0){
			return true;
		}else{
			$this->validation->set_message('itconcepto',"El Concepto ($concepto) No exsite");
			return false;
		}
	}
	
	function chexiste($codigo){
		$codigo     = $this->db->escape($codigo  );
		$cana=$this->datasis->dameval("SELECT COUNT(*) FROM noco WHERE codigo = $codigo  ");
		if($cana <= 0){
			return true;
		}else{
			$this->validation->set_message('chexiste',"El Codigo del contrato ya existe");
			return false;
		}
	}
	
	function tipo(){
		$contrato = $this->input->post('con');
		$contratoe=$this->db->escape($contrato);
		$query = $this->db->query("SELECT codigo, nombre denominacion FROM noco WHERE tipo<>'O' AND (SELECT COUNT(*) FROM noco WHERE tipo='O' AND codigo=$contratoe)>0");
		
		if($query->num_rows()>0){
			foreach($query->result() AS $fila ){
				echo "<option value='".$fila->codigo."'>".$fila->codigo.' '.$fila->denominacion."</option>";
			}
		}else{
			echo "<option value=''>No hay registros disponibles</option>";
		}
	}
	
	function instalar(){
			$query="ALTER TABLE `noco` ADD COLUMN `modo` CHAR(1) NULL DEFAULT '1' COMMENT '1 es normal, 2 los montos y partidas los toma a partir de los cargos' AFTER `fondo`";
			$this->db->simple_query($query);
			$query="ALTER TABLE itnoco ADD orden INTEGER";
			$this->db->simple_query($query);
	}
}
?>

