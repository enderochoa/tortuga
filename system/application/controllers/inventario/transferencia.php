 <?php
class Transferencia extends Controller {
	var $modbus=array(
			'tabla'   =>'caub',
			'columnas'=>array(
				'ubica' =>'C&oacute;digo',
				'ubides'=>'Descripci&oacute;n',
				'gasto' =>'Gastos'),
			'filtro'  =>array('ubides'=>'Descripci&oacute;n'),
			'retornar'=>array('ubica'=>'<#retorno#>'),
			'titulo'  =>'Buscar Almac&eacute;n',
			'p_uri'=>array(4=>'<#retorno#>'));
	
	
	function Transferencia(){
		parent::Controller();
		$this->load->library("rapyd");
		//$this->datasis->modulo_id(604,1);
	}
	
	function index() {		
		$this->rapyd->load("datagrid","datafilter");
		
		$filter = new DataFilter("Filtro de Transferencia");
		$filter->db->select("numero,fecha,envia,recibe, CONCAT_WS(' ',TRIM(observ1),TRIM(observ2)) AS observa");
		$filter->db->from('stra');
		
		$filter->fechad = new dateonlyField("Desde", "fechad",'d/m/Y');
		$filter->fechah = new dateonlyField("Hasta", "fechah",'d/m/Y');
		$filter->fechad->clause  =$filter->fechah->clause="where";
		$filter->fechad->db_name =$filter->fechah->db_name="fecha";
		$filter->fechad->insertValue = date("Y-m-d"); 
		$filter->fechah->insertValue = date("Y-m-d"); 
		$filter->fechah->size=$filter->fechad->size=10;
		$filter->fechad->operator=">="; 
		$filter->fechah->operator="<=";
		
		$filter->numero = new inputField("N&uacute;mero"    , "numero");
		
		$filter->envia  = new inputField("Env&iacute;a", "envia");
		$filter->envia->append($this->datasis->p_modbus($this->modbus,'envia'));
		$filter->envia->size=10;
		
		$filter->recibe = new inputField("Recibe"      , "recibe");
		$filter->recibe->append($this->datasis->p_modbus($this->modbus,'recibe'));
		$filter->recibe->size=10;
		
		$filter->buttons("reset","search");
		$filter->build();
    
		$uri = anchor('inventario/transferencia/dataedit/show/<#numero#>','<#numero#>');
    
		$grid = new DataGrid();
		$grid->order_by("numero","desc");
		$grid->per_page = 20;
		
		$grid->column("N&uacute;mero",$uri);
		$grid->column("Fecha","<dbdate_to_human><#fecha#></dbdate_to_human>","align='center'");
		$grid->column("Envia"  ,"envia"  ,"align='right'");
		$grid->column("Recibe" ,"recibe" ,"align='right'");
		$grid->column("Observaci&oacute;n" ,"observa");
		
		$grid->add("inventario/transferencia/dataedit/create");
		$grid->build();
		//echo $grid->db->last_query();
		
		$data['content'] =$filter->output.$grid->output;
		$data["head"]    = $this->rapyd->get_head();
		$data['title']   =' Asientos ';
		$this->load->view('view_ventanas', $data);
	}
	
	function dataedit(){
 		$this->rapyd->load("dataedit","datadetalle");

		$modbus=array(
			'tabla'   =>'sinv',
			'columnas'=>array(
				'codigo' =>'Código',
				'descrip'=>'descrip'),
			'filtro'  =>array('codigo' =>'Código','descrip'=>'descrip'),
			'retornar'=>array('codigo'=>'codigo<#i#>','precio1'=>'precio1<#i#>','precio2'=>'precio2<#i#>','precio3'=>'precio3<#i#>','precio4'=>'precio4<#i#>','iva'=>'iva<#i#>','pond'=>'costo<#i#>'),
			'p_uri'=>array(4=>'<#i#>'),
			'titulo'  =>'Buscar Articulo');
 		
 		
		$edit = new DataEdit("Transferencia","stra");

		$edit->_dataobject->db->set('usuario', $this->session->userdata('usuario'));
		$edit->_dataobject->db->set('hora'   , 'CURRENT_TIME()', FALSE);
		$edit->_dataobject->db->set('estampa', 'NOW()', FALSE);
		
		$edit->post_process("insert","_guarda_detalle");
		$edit->post_process("update","_actualiza_detalle");
		$edit->post_process("delete","_borra_detalle");
		$edit->pre_process('delete','_pre_del');
		$edit->pre_process('insert','_pre_insert');

		
		$edit->back_url = "inventario/transferencia/";
		
		$edit->fecha = new DateonlyField("Fecha", "fecha","d/m/Y");
		$edit->fecha->insertValue = date("Y-m-d");
		$edit->fecha->size = 10;
		
		$edit->numero = new inputField2("N&uacute;mero", "numero");
		$edit->numero->size = 10;
		$edit->numero->mode="autohide";
		$edit->numero->maxlength=8;
		$edit->numero->readonly=TRUE;
		
		$edit->observ1  = new inputField("Observaci&oacute;n", "observ1");
		$edit->observ1->maxlength=30;
		$edit->observ1->size =40;
		
		$edit->observ2  = new inputField("Observaci&oacute;n", "observ2");
		$edit->observ2->maxlength=30;
		$edit->observ2->size =40;
		
		$edit->envia  = new inputField("Env&iacute;a", "envia");
		$edit->envia->append($this->datasis->p_modbus($this->modbus,'envia'));
		$edit->envia->size=7;
		$edit->envia->maxlength=4;
		
		$edit->recibe = new inputField("Recibe"      , "recibe");
		$edit->recibe->append($this->datasis->p_modbus($this->modbus,'recibe'));
		$edit->recibe->size=7;
		$edit->recibe->maxlength=4;
		

		$numero=$edit->_dataobject->get('numero');
		
		$detalle = new DataDetalle($edit->_status);
		
			//Campos para el detalle
			$detalle->db->select('codigo,descrip,cantidad, precio1,precio2,precio3,precio4,iva,costo');
			$detalle->db->from('itstra');
			$detalle->db->where("numero='$numero'");
			
			$detalle->codigo = new inputField2("Codigo", "codigo<#i#>");
			$detalle->codigo->size=11;
			$detalle->codigo->db_name='codigo';
			$detalle->codigo->append($this->datasis->p_modbus($modbus,'<#i#>'));
			$detalle->codigo->readonly=TRUE;
			
			$detalle->descrip = new inputField("Referencia", "descrip<#i#>");
			$detalle->descrip->size=15;
			$detalle->descrip->db_name='descrip';
			$detalle->descrip->maxlength=12;
			
			$detalle->cantidad = new inputField("Monto", "cantidad<#i#>");
			$detalle->cantidad->css_class='inputnum';
			$detalle->cantidad->size=20;
			$detalle->cantidad->db_name='cantidad';
			
			for($i=1;$i<=4;$i++){
				$objeto="precio$i";
				$detalle->$objeto = new inputField2("Precio", "$objeto<#i#>");
				$detalle->$objeto->type='hidden';
				$detalle->$objeto->db_name=$objeto;
			}
			$detalle->iva = new inputField2("IVA", "iva<#i#>");
			$detalle->iva->type='hidden';
			$detalle->iva->db_name='iva';
			
			$detalle->costo = new inputField2("Costo", "costo<#i#>");
			$detalle->costo->type='hidden';
			$detalle->costo->db_name='costo';
    	
			//fin de campos para detalle
			
			//Columnas del detalle
			$detalle->column("C&oacute;digo"     ,"<#codigo#><#precio1#><#precio2#><#precio3#><#precio4#><#iva#><#costo#>");
			$detalle->column("Descripci&oacute;n","<#descrip#>");
			$detalle->column("Cantidad"          ,"<#cantidad#>");
			$detalle->build();	
			
		$edit->detalle=new freeField("detalle", 'detalle',$detalle->output);

		$edit->buttons( "save", "undo","delete", "back");
		$edit->build();
		
		$conten["form"]  =&  $edit;
		$data['content'] = $this->load->view('view_tranferencia', $conten,true); 
		$data["head"]    = script("tabber.js").script("prototype.js").$this->rapyd->get_head().script("scriptaculous.js").script("effects.js");
		$data['title']   = ' Transferencia de Inventario ';
		$this->load->view('view_ventanas', $data);
	}
	
	function _guarda_detalle($do) {
		$cant=$this->input->post('cant_0');
		$i=$o=0;
		while($o<$cant){
			if (isset($_POST["codigo$i"])){
				if($this->input->post("codigo$i")){
						
					$sql = "INSERT INTO itstra (numero,codigo,descrip,cantidad,precio1,precio2,precio3,precio4,iva,costo) VALUES(?,?,?,?,?,?,?,?,?,?)";
					$llena=array(
							0 =>$do->get('numero'),
							1 =>$this->input->post("codigo$i"),
							2 =>$this->input->post("descrip$i"),
							3 =>$this->input->post("cantidad$i"),
							4 =>$this->input->post("precio1$i"),
							5 =>$this->input->post("precio2$i"),
							6 =>$this->input->post("precio3$i"),
							7 =>$this->input->post("precio4$i"),
							8 =>$this->input->post("iva$i"),
							9 =>$this->input->post("costo$i"));

					$this->db->query($sql,$llena);
				}
				$o++;
			}
			$i++;
		}
	}
	
	function _actualiza_detalle($do){
		$this->_borra_detalle($do);
		$this->_guarda_detalle($do);
	}
	
	function _borra_detalle($do){
		$numero=$do->get('numero');
		$sql = "DELETE FROM itstra WHERE numero='$numero'";
		$this->db->query($sql);
	}
	
	function _pre_del($do) {
		return False;
	}
	
	function _pre_insert($do){

		$sql    = 'INSERT INTO ntransa (usuario,fecha) VALUES ("'.$this->session->userdata('usuario').'",NOW())';
    $query  =$this->db->query($sql);
    $transac=$this->db->insert_id();
    
		$sql    = 'INSERT INTO nstra (usuario,fecha) VALUES ("'.$this->session->userdata('usuario').'",NOW())';
    $query  =$this->db->query($sql);
    $numero =str_pad($this->db->insert_id(),8, "0", STR_PAD_LEFT) ;
    
		$do->set('transac', $transac);
		$do->set('numero' , $numero);
	}
}
?>