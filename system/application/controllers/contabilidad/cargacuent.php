<?php
require_once(BASEPATH.'application/controllers/validaciones.php');
class cargacuent extends validaciones {
	var $formatopres;
	var $flongpres;
	var $formatoadm;
	var $flongadm;
	var $url    = '/contabilidad/cargacuent/';
	var $long;
	
	function cargacuent(){		
		parent::Controller(); 
		$this->load->library("rapyd");
		$this->formatopres=$this->datasis->traevalor('FORMATOPRES');
		$this->flongpres  =strlen(trim($this->formatopres));
		$this->long = strlen($this->formatopres);

		
	}
	
	function index() {
		//$this->datasis->modulo_id(101,1);
		redirect($this->url."carga");
	}
	
	function carga(){
	
		$this->rapyd->load('datagrid','fields','datafilter2');
		
		$formato=$this->datasis->dameval('SELECT formato FROM cemp LIMIT 0,1');
 		$qformato='%';
 		for($i=1;$i<substr_count($formato, '.')+1;$i++) $qformato.='.%';
 		$this->qformato=$qformato;
 		$this->qformato=$qformato=$this->datasis->formato_cpla();

		$modbus=array(
			'tabla'   =>'cpla',
			'columnas'=>array(
				'codigo' =>'C&oacute;digo',
				'denominacion'=>'Denominaci&oacute;n'),
			'filtro'  =>array('codigo'=>'C&oacute;digo','denominacion'=>'Denominacion'),
			'retornar'=>array('codigo'=>'con<#codigo#>'),//,'departa'=>'ccosto_<#i#>'
			'titulo'  =>'Buscar Cuenta',
			'where' => 'nivel = 3',
			'p_uri'=>array(4=>"<#codigo#>"),
			
			);
		
			//'where'=>" codigo LIKE \"$qformato\"",
		$btn=$this->datasis->p_modbus($modbus,"<#codigo#>");
		
		$modbus2=array(
			'tabla'   =>'ppla',
			'columnas'=>array(
				'codigo' =>'C&oacute;digo',
				'denominacion'=>'Denominaci&oacute;n'),
			'filtro'  =>array('codigo'=>'C&oacute;digo','denominacion'=>'Denominacion'),
			'retornar'=>array('codigo'=>'codigo'),//,'departa'=>'ccosto_<#i#>'
			
			'titulo'  =>'Buscar Cuenta',
			
			);
		
			//'where'=>" codigo LIKE \"$qformato\"",
		$btn2=$this->datasis->p_modbus($modbus2,"ppla");

		$error='';
		if($this->input->post('pros') !== FALSE){
			
			foreach($_POST AS $cod=>$cant){
			
				if(substr($cod,0,3)=="con" && $cant >0){
					$cod = substr($cod,3,20);
					$cod = $this->db->escape($cod.'%');
					$cod = str_replace('_','.',$cod);
					
					//$cant = $this->db->escape($cant);
					
					if(!$this->datasis->dameval("SELECT COUNT(*) FROM cpla WHERE codigo='$cant' ")){
						$error.=" La cuenta contable ($cant) es inv&aacute;lida<br>";
					}else{
						$data  = array('contable' => $cant);
						$where = "codigo like $cod  ";
						$mSQL  = $this->db->update_string('ppla', $data, $where);
						
						$this->db->simple_query($mSQL);
					}
				}
			}
		}

		$filter = new DataFilter2("&nbsp;", 'ppla');
		$filter->error_string=$error;

		$filter->codigo = new inputField("C&oacute;digo Presupuestario", "codigo");
		//$filter->codigo->option("","Seleccionar");
		//$filter->codigo->options("SELECT codigo, CONCAT_WS(' ',codigo,denominacion) FROM ppla WHERE LENGTH(codigo) < ($this->long) ORDER BY codigo  ");
		$filter->codigo->clause  ="likerigth";
		$filter->codigo->rule    = "required";
		$filter->codigo->size = 20;
		$filter->codigo->append($btn2);

		$filter->buttons("reset","search");
		$filter->build();
		
		$ggrid='';
		if ($filter->is_valid()){		
			
			$ggrid =form_open('contabilidad/cargacuent/carga/search/osp');
			$ggrid.=form_hidden('codigo', $filter->codigo->newValue);

			$contable = new inputField("Cuenta Contable","con<#codigo#>");//, "contable[<#codigo#>]"
			$contable->grid_name= "contable[<#codigo#>]";
			$contable->status   = 'modify';
			$contable->size     = 12;
			$contable->css_class= 'inputnum';
			$contable->append($btn);
			$contable->build();

			$grid = new DataGrid("Clasificador Presupuestario (".$filter->codigo->newValue.")") ;
			//$grid->db->where('concepto','015');
			//$grid->per_page = $filter->db->num_rows() ;
			$grid->order_by("codigo","asc");
			$grid->column("C&oacute;digo"      , "codigo");
			$grid->column("Denominaci&oacute;n", "denominacion");
			$grid->column("Cuenta Contable"    , "contable");
			$grid->column("Cuenta Contable"    , $contable->output,'align=\'right\'');
			$grid->submit('pros'               , 'Guardar',"BR");
			$grid->build();
			$ggrid.=$grid->output;
			$ggrid.=form_close();
			//echo $grid->db->last_query();
			
		}    
		$script ='
		<script type="text/javascript">
		$(function() {
			$(".inputnum").numeric(".");
		});
		</script>';
		$data['content'] = $filter->output.$ggrid;
		$data['title']   = 'Asignaci&oacute;n de Cuentas';
		$data['script']  = $script;
		$data["head"]    = $this->rapyd->get_head().script("jquery.pack.js").script("plugins/jquery.numeric.pack.js").script("plugins/jquery.floatnumber.js");
		$this->load->view('view_ventanas', $data); 
	}
	
	
}
