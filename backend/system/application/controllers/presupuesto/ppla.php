<?php
//plancuenta
require_once(BASEPATH.'application/controllers/validaciones.php');
class ppla extends validaciones {
	
	var $qformato;

	function ppla(){
		parent::Controller();
		$this->load->library("rapyd");
		
	}
	
	function index() {
		$this->datasis->modulo_id(41,1);
		$this->rapyd->load("datagrid","datafilter2");
		
		$filter = new DataFilter2("",'ppla');
		
		$filter->codigo   = new inputField("C&oacute;digo","codigo");
		$filter->codigo->size   = 15;
		//$filter->codigo->clause = "likerigth";
		
		$filter->denominacion = new inputField("Denominaci&oacute;n", "denominacion");
		//$filter->denominacion->clause="likerigth";
		
		$filter->descrip = new inputField("Aplicaci&oacute;n", "aplicacion");
		//$filter->descrip->clause="likerigth";
		
		//$filter->ordinal = new dropdownField("Ordinal","ordinal");		
		//$filter->ordinal->option("N","No");
		//$filter->ordinal->option("S","Si");
		
		$filter->buttons("reset","search");
		$filter->build();

		$uri = anchor('presupuesto/ppla/dataedit/show/<#codigo#>','<#codigo#>');

		$grid = new DataGrid();
		$grid->order_by("codigo","asc");
		$grid->per_page = 20;
		
		$grid->column_orderby("C&oacute;digo"       ,$uri          ,"codigo"       );
		$grid->column_orderby("Denominaci&oacute;n" ,"denominacion","denominacion" ,"align='left' NOWRAP");
		$grid->column_orderby("Aplicacion"          ,"aplicacion"  ,"aplicacion"   ,"align='left' NOWRAP");
		//$grid->column_orderby("Ordinal"             ,"ordina"     ,"ordinal"      );
		
		$grid->add("presupuesto/ppla/dataedit/create");
		$grid->build();
		
		$data['filtro']  = $filter->output;
		$data['content'] = $grid->output;
		$data['script'] = script("jquery.js")."\n";
		$data['title']   = "Clasificador de Presupuesto";
		//$data['content'] =$filter->output.$grid->output;
		$data["head"]    = $this->rapyd->get_head();
		//$data['title']   =' Clasificador Presupuestario ';
		$this->load->view('view_ventanas', $data);
	}
	
	function dataedit(){
		$this->datasis->modulo_id(41,1);
 		$this->rapyd->load("dataedit");
 		
 		$formato=$this->datasis->dameval('SELECT formato FROM cemp LIMIT 0,1');
 		$qformato='%';
 		for($i=1;$i<substr_count($formato, '.')+1;$i++) $qformato.='.%';
 		$this->qformato=$qformato;
 		$this->qformato=$qformato=$this->datasis->formato_cpla();
 		
 		$modbus=array(
			'tabla'   =>'cpla',
			'columnas'=>array(
				'codigo' =>'C&oacute;digo',
				'denominacion'=>'Descripci&oacute;n'),
			'filtro'  =>array('codigo'=>'C&oacute;digo','denominacion'=>'Descripci&oacute;n'),
			'retornar'=>array('codigo'=>'contable'),//,'denominacion'=>'concepto'
			'titulo'  =>'Buscar Cuenta',
			'where'=>"codigo LIKE \"$qformato\"",
			'p_uri'=>array(4=>'<#i#>')
		);
 		
		$button  = $this->datasis->p_modbus($modbus,'cpla' );
		
		$edit = new DataEdit("Clasificador Presupuestario","ppla");
		
		$edit->back_url = "presupuesto/ppla/index/filteredgrid";

		$edit->cod_ppla = new inputField("C&oacute;digo", "codigo");
		$edit->cod_ppla->rule= "trim|required|callback_chcodigo|callback_chexiste";
		$edit->cod_ppla->mode="autohide";
		$edit->cod_ppla->size=20;
		$edit->cod_ppla->maxlength =15 ;
		
		$edit->titulo = new inputField("Denominaci&oacute;n", "denominacion");
		$edit->titulo->rule= "required";
		$edit->titulo->size=40;
		$edit->titulo->maxlength =500 ;
		
		//$edit->descrip = new textareaField("Aplicaci&oacute;n", "aplicacion");
		//$edit->descrip->cols = 70;  
		//$edit->descrip->rows = 6;
		////$edit->descrip->rule= "required";
		//
		//$edit->ordinal = new dropdownField("Ordinal","ordinal");		
		//$edit->ordinal->option("N","No");
		//$edit->ordinal->option("S","Si");
		//
		$edit->contable = new inputField("Cuenta Contable ", "contable");
		$edit->contable->rule     ='callback_chcuentac';//|callback_itorden |callback_repetido|
		$edit->contable->size     =11;
		$edit->contable->append($button);
		
		$edit->buttons("modify", "save", "undo", "delete", "back");
		$edit->build();

		$data['content'] = $edit->output;
		$data["head"]    = $this->rapyd->get_head();
		$data['title']   = 'Clasificador Presupuestario';
		$this->load->view('view_ventanas', $data);
	}
	
	function chcodigo($codigo){
		if (preg_match("/^[0-9]+(\.[0-9]+)*$/",$codigo)>0){
			$formato=$this->datasis->traevalor('FORMATOPRES');
			//$formato='X.XX.XX.X';
			$farr=explode('.',$formato);
			$carr=explode('.',$codigo);
			$max =count($carr);
			$mmac=count($farr);
			if($mmac>=$max){
				for($i=0;$i<$max;$i++){
					if(strlen($farr[$i])!=strlen($carr[$i])){
						$this->validation->set_message('chcodigo',"El c&oacute;digo dado no coincide con el formato: $formato");
						return false;
					}
				}
			}else{
				$this->validation->set_message('chcodigo',"El c&oacute;digo dado no coincide con el formato: $formato");
				return false;
			}
			$pos=strrpos($codigo,'.');
			if($pos!==false){
				$str=substr($codigo,0,$pos);
				$cant=$this->datasis->dameval("SELECT COUNT(*) FROM ppla WHERE codigo='$str'");
				if($cant==0){
					$this->validation->set_message('chcodigo',"No existe la cuenta padre ($str) para registrar esa cuenta");
					return false;
				}
			}
		}else{
			$this->validation->set_message('chcodigo',"El c&oacute;digo parece tener formato invalido");
			return false;
		}
		return true;
	}
	
	function chexiste($codigo){
		$codigo = $this->db->escape($codigo);
		$cant  = $this->datasis->dameval("SELECT COUNT(*) FROM ppla WHERE codigo=$codigo");
		if($cant > 0){
			$this->validation->set_message('chexiste',"La cuenta ya se encuentra registrada");
			return false;
		}
		
	}
	
	function denomi(){
		$codigo = $this->input->post('codigo');
		$codigo = $this->db->escape($codigo);
		$val    = $this->datasis->dameval("SELECT denominacion FROM ppla WHERE codigo=$codigo");
		$val    = ($val);
		echo $val;
		
	}
	
	function autocomplete($cod=FALSE){
		if($cod!==false){
			$mSQL="SELECT denominacion FROM ppla WHERE $campo LIKE '$cod%' AND movimiento = 'S'";
			$query=$this->db->query($mSQL);
			if($query->num_rows() > 0){
				foreach($query->result() AS $row){
					$salida=$row->$campo."\n";
				}
			}
			echo ($salida);
		}
	}
	
	function autocomplete2($cod=false){
		if($cod!==false){
			$mSQL="SELECT codigo AS c1,denominacion AS c2 FROM ppla WHERE codigo LIKE '$cod%' AND movimiento = 'S'";
						
			$query=$this->db->query($mSQL);
			$salida = '';
			if($query->num_rows() > 0){
				foreach($query->result() AS $row){
					$salida.=$row->c1.'|'.$row->c2."\n";
				}
			}
			echo ($salida);
		}
	}
  
  function autocomplete3($codigoadm=false,$cod=false){
    //if($cod!==false){
       $mSQL="SELECT codigo AS c1,denominacion AS c2 FROM v_presaldo WHERE movimiento = 'S' AND codigo LIKE '$cod%'";//
       if($codigoadm)
         $mSQL.=" AND codigoadm = ".$this->db->escape($codigoadm);
    //}
    
    $b=$this->db->escape($mSQL);
    $this->db->simple_query("UPDATE valores SET valor=$b where nombre='TITULO5'");
    $query=$this->db->query($mSQL);
    $salida = '';
    if($query->num_rows() > 0){
      foreach($query->result() AS $row){
        $salida.=$row->c1.'|'.$row->c2."\n";
      }
    }
    echo ($salida);
  }
  
  function autocomplete4($saldo=NULL){
    $query  ="SELECT codigoadm,fondo ,codigo codigopres,codigo label,ordinal,denominacion FROM v_presaldo";
		if($saldo)$query.=" WHERE saldo>0";
		$mSQL   = $this->db->query($query);
		$arreglo= $mSQL->result_array($query);
		foreach($arreglo as $key=>$value)
			foreach($value as $key2=>$value2) 
			$arreglo[$key][$key2] = ($value2);
			 
    echo json_encode($arreglo);
  }
  
  function autocompleteppla(){
    $query  ="SELECT codigo,denominacion,codigo label FROM ppla WHERE codigo LIKE '4%' ORDER BY codigo";
		
		$mSQL   = $this->db->query($query);
		$arreglo= $mSQL->result_array($query);
		foreach($arreglo as $key=>$value)
			foreach($value as $key2=>$value2)
			$arreglo[$key][$key2] = ($value2);
//$arreglo[$key][$key2] = $value2;
		//header('Content-type: application/jsonrequest; charset='.$this->config->item('charset'));
		echo json_encode($arreglo);
	}
	
}
