<?php
//plancuenta
class estruadm extends Controller {
	
	var $titp  = 'Estructura Administrativas';
	var $tits  = 'Estructura Administrativa';
	var $url   = 'presupuesto/estruadm/';
	
	function estruadm(){
		parent::Controller();
		$this->load->library("rapyd");
		$this->datasis->modulo_id(27,1);
	}
	
	function index() {
		$this->rapyd->load("datagrid","datafilter2");
				//$this->rapyd->uri->keep_persistence(); 

		$filter = new DataFilter2("");
		$filter->db->select(array("a.codigo","a.denominacion","b.nombre","b.codigo eje"));
		$filter->db->from("estruadm a");
		$filter->db->join("uejecutora b","a.uejecutora=b.codigo","LEFT");
		
		$filter->cod_ppla   = new inputField("C&oacute;digo","codigo");
		$filter->cod_ppla->size=15;
		//$filter->cod_ppla->clause="likerigth";
		$filter->cod_ppla->db_name='a.codigo';
		
		$filter->titulo = new inputField("Denominaci&oacute;n", "denominacion");
		$filter->titulo->clause="likerigth";
		
		
		
		$filter->buttons("reset","search");
		$filter->build();

		$uri = anchor($this->url.'/dataedit/show/<#codigo#>','<#codigo#>');

		$grid = new DataGrid();
		$veje = $this->input->post('veje');
		//if($veje)
		//$grid->db->();
		
		$grid->order_by("codigo","asc");
		$grid->per_page = 20;
		
		$grid->column_orderby("C&oacute;digo"      ,$uri           ,"codigo");
		$grid->column_orderby("Denominaci&oacute;n","denominacion" ,"denominacion"  ,"align='left' NOWRAP");
		$grid->column_orderby("Cod"                ,"eje"       ,"eje");
		$grid->column_orderby("U. Ejecutora"       ,"nombre"       ,"nombre");
		
		$grid->add($this->url."/dataedit/create");
		$grid->build();
		//echo $grid->db->last_query();
		
		$data['filtro']  = $filter->output;
		$data['content'] = $grid->output;
		$data['script'] = script("jquery.js")."\n";
		$data['title']   = "$this->titp";
		//$data['content'] =$filter->output.$grid->output;
		$data["head"]    = $this->rapyd->get_head();
		//$data['title']   =' Estructura Administrativa ';
		$this->load->view('view_ventanas', $data);
	}
	
	function dataedit(){
 		$this->rapyd->load("dataedit");
 		
		$edit = new DataEdit("Estructura Administrativa","estruadm");
		
		$edit->pre_process('delete'  ,'_pre_delete');
		
		$edit->back_url = "presupuesto/estruadm/index/filteredgrid";

		$edit->codigo = new inputField("C&oacute;digo", "codigo");
		$edit->codigo->rule= "trim|required|callback_chcodigo";
		$edit->codigo->mode="autohide";
		$edit->codigo->size=20;
		$edit->codigo->maxlength =15 ;
		
		$edit->titulo = new inputField("Denominaci&oacute;n", "denominacion");
		$edit->titulo->rule= "required";
		$edit->titulo->size=50;
		$edit->titulo->maxlength =500 ;
		
		//$edit->descrip = new inputField("Tipo", "tipo");
		//$edit->descrip->size = 10;  
		//$edit->descrip->maxlength = 1;
		
		$edit->uejecutora =  new dropdownField("Unidad Ejecutora", "uejecutora");
		$edit->uejecutora->option("","");
		$edit->uejecutora->size = 80;
		$edit->uejecutora->options("SELECT codigo, CONCAT(codigo,' ', nombre) nombre FROM uejecutora ORDER BY nombre");
		
		$edit->descripcion = new textAreaField("Descripcion", "descripcion");
		$edit->descripcion->cols = 80;
		$edit->descripcion->rows = 7;
		
		$edit->buttons("add","modify", "save", "undo", "delete", "back");
		$edit->build();

		$data['content'] = $edit->output;
		$data["head"]    = $this->rapyd->get_head();
		$data['title']   = 'Estructura Administrativa';
		$this->load->view('view_ventanas', $data);
	}
	
	function chcodigo($codigo){
		if (preg_match("/^[0-9]+(\.[0-9]+)*$/",$codigo)>0){
			$formato=$this->datasis->traevalor('FORMATOESTRU');
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
				$cant=$this->datasis->dameval("SELECT COUNT(*) FROM estruadm WHERE codigo='$str'");
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
	
	function autocomplete($cod=''){
	  $formato = $this->datasis->formato_ppla();
    $formato=$this->db->escape($formato);
		$mSQL="SELECT codigo AS c1,denominacion AS c2 FROM estruadm WHERE codigo LIKE $formato";
		$query=$this->db->query($mSQL);
		if($query->num_rows() > 0){
			foreach($query->result() AS $row){
				echo ($row->c1.'|'.$row->c2)."\n";
			}
		}
	}
	
	function autocompleteui(){
		$formato= $this->datasis->formato_ppla();
    $formato=$this->db->escape($formato);
    $query  ="SELECT codigoadm,codigoadm label,fondo,b.denominacion FROM v_presaldo JOIN estruadm b ON v_presaldo.codigoadm=b.codigo  WHERE movimiento='S' GROUP BY codigoadm,fondo ";
		$mSQL   = $this->db->query($query);
		$arreglo= $mSQL->result_array($query);
		foreach($arreglo as $key=>$value)
			foreach($value as $key2=>$value2) 
			$arreglo[$key][$key2] = ($value2);
			 
    echo json_encode($arreglo);
  }
  
  function arregla(){
		$result = $this->db->query("SELECT codigo FROM estruadm");
		foreach($result->result() as $row){
			echo '</br>'.$row->codigo;
			$codigo  = $row->codigo;
			
			$codigos = explode('.',$codigo);
			$max     = count($codigos);
			
			$i=0;
			for($i=0 ; $i<$max-1;$i++){
				$temp = array();
				$j=0;
				for($j=0 ; $j<=$i;$j++){
					$temp[$j]=$codigos[$j];
				}
				$c = $this->db->escape(implode('.',$temp));
				
				$query = "INSERT IGNORE INTO estruadm (`codigo`) values ($c)";
				$this->db->query($query);
			}
		}
	}
	
	function _pre_delete($do){
		$error='';
		$codigo=$do->get('codigo');
		$codigoe=$this->db->escape($codigo);
		$cant  =$this->datasis->dameval("SELECT COUNT(*) FROM presupuesto WHERE codigoadm=$codigoe");
		
		if($cant>0){
			$error='No se Puede Eliminar la Estructura Administrativa debido a que tiene partidas relacionadas';
		}
		$codigo.='_%';
		$codigoe=$this->db->escape($codigo);
		$cant   =$this->datasis->dameval("SELECT COUNT(*) FROM estruadm WHERE codigo LIKE $codigoe");
		
		if($cant>0){
			$error='No se Puede Eliminar la Estructura Administrativa debido a que hay un codigo con tama&ntilde; superior';
		}
		
		if(!empty($error)){
			$do->error_message_ar['pre_del']="<div class='alert'>".$error."</div>";
			return false;	
		}
	}
	
	function instalar(){
			$query="ALTER TABLE `estruadm`
			CHANGE COLUMN `uejecutora` `uejecutora` CHAR(8) NULL DEFAULT NULL COMMENT 'Unidad Ejecutora' AFTER `causado`;";
			$this->db->simple_query($query);
	}
}
