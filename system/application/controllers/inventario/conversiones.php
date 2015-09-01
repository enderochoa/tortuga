<?php
//inventario
class conversiones extends Controller {
	
	function conversiones(){
		parent::Controller();
		$this->load->library("rapyd");
		$this->datasis->modulo_id(201,1);
	}
	function index() {		
		redirect('inventario/conversiones/datafilter');
	}	
	function datafilter(){
$this->rapyd->load("datafilter","datagrid");
		$this->rapyd->uri->keep_persistence();
		
		$filter = new DataFilter("Filtro de Conversiones", 'conv');
		
		$filter->fecha = new dateonlyField("Fecha", "fecha");	
		$filter->fecha->size=15;
		$filter->fecha->maxlength=15;
		$filter->fecha->rule="trim";
		
		$filter->numero = new inputField("Número", "numero");
		$filter->numero->size=15;
		
		$filter->almacen = new inputField("Almacen", "almacen");
		$filter->almacen->size=15;
		
		$filter->buttons("reset","search");
		$filter->build();
		
		$uri = anchor('inventario/conversiones/dataedit/show/<#numero#>','<#numero#>');
		
		$grid = new DataGrid("Lista de Conversiones");
		$grid->use_function('dbdate_to_human');
		$grid->order_by("numero","asc");
		$grid->per_page = 20;
		
		$grid->column("Número", $uri);
		$grid->column("Fecha","<dbdate_to_human><#fecha#></dbdate_to_human>");
		$grid->column("Almacen","almacen");
		$grid->column("Usuario", "usuario");
				
		$grid->add("inventario/conversiones/dataedit/create");
		$grid->build();
		
		$data['content'] = $filter->output.$grid->output;
		$data['title']   = " Conversiones ";
		$data["head"]    = $this->rapyd->get_head();
		$this->load->view('view_ventanas', $data);	
	}
			
	function dataedit(){
 		$this->rapyd->load("dataedit","datadetalle","fields","datagrid");
 		
 		$modbus=array(
		'tabla'   =>'sinv',
		'columnas'=>array(
		'codigo' =>'Código',
		'descrip'=>'descrip'),
		'filtro'  =>array('codigo' =>'Código','descrip'=>'descrip'),
		//'retornar'=>array('codigo'=>'codigo<#i#>','precio1'=>'precio1<#i#>','precio2'=>'precio2<#i#>','precio3'=>'precio3<#i#>','precio4'=>'precio4<#i#>','iva'=>'iva<#i#>','pond'=>'costo<#i#>'),
		'retornar'=>array('codigo'=>'codigo<#i#>','descrip'=>'descrip<#i#>'),
		'p_uri'=>array(4=>'<#i#>'),
		'titulo'  =>'Buscar Articulo');
 				
		$edit = new DataEdit("Conversiones","conv");
		$edit->back_url = "inventario/conversiones";
		
		$edit->post_process("insert","_guarda_detalle");
		$edit->post_process("update","_actualiza_detalle");
		$edit->post_process("delete","_borra_detalle");
		$edit->pre_process('delete','_pre_del');
		$edit->pre_process('insert','_pre_insert');
			
		$edit->fecha = new DateonlyField("Fecha", "fecha","d/m/Y");
		$edit->fecha->insertValue = date("Y-m-d");
		$edit->fecha->mode="autohide";
		$edit->fecha->size = 10;
		
		$edit->numero = new inputField("N&uacute;mero", "numero");
		$edit->numero->size = 10;
		$edit->numero->rule= "required";
		$edit->numero->mode="autohide";
		$edit->numero->maxlength=8;
		
		$edit->observ1 = new inputField("Observaciones","observ1");
		$edit->observ1->size = 38;        
		$edit->observ1->maxlength=35;
		
		$edit->observ2 = new inputField("Observaciones","observ2");
		$edit->observ2->size = 38;        
		$edit->observ2->maxlength=35;
		
		$edit->almacen = new inputField("Almacen","almacen");
		$edit->almacen->size =6;        
		$edit->almacen->maxlength=4;
						
		$numero=$edit->_dataobject->get('numero');
		
		$detalle = new DataDetalle($edit->_status);
		
		//Campos para el detalle
			
		$detalle->db->select('numero,codigo,descrip,entrada,salida');
		$detalle->db->from('itconv');
		$detalle->db->where("numero='$numero'");
		
		$detalle->codigo = new inputField("Código", "codigo<#i#>");
		$detalle->codigo->size=15;
		$detalle->codigo->db_name='codigo';
		$detalle->codigo->append($this->datasis->p_modbus($modbus,'<#i#>'));
		$detalle->codigo->readonly=TRUE;
		
		$detalle->descripcion = new inputField("Descripción", "descrip<#i#>");
		$detalle->descripcion->size=30;
		$detalle->descripcion->db_name='descrip';
		$detalle->descripcion->maxlength=30;
		
		$detalle->entrada = new inputField("Cantidad", "entrada<#i#>");
		$detalle->entrada->size=10;
		$detalle->entrada->db_name='entrada';
		$detalle->entrada->maxlength=10;
		$detalle->entrada->css_class='inputnum';
		
		$detalle->salida = new inputField("Salida", "salida<#i#>");
		$detalle->salida->css_class='inputnum';
		$detalle->salida->size=20;
		$detalle->salida->db_name='salida';
		
		//fin de campos para detalle
		
		$detalle->onDelete('totalizar()');
		$detalle->onAdd('totalizar()');
		//$detalle->script($script);
		$detalle->style="width:110px";
		
		//Columnas del detalle
		$detalle->column("Código"     , "<#codigo#>");
		$detalle->column("Descripción", "<#descripcion#>");
		$detalle->column("Entrada"    , "<#entrada#>");
		$detalle->column("Salidad"    , "<#salida#>");
	
		$detalle->build();
		$conten["detalle"] = $detalle->output;
		
		$edit->detalle=new freeField("detalle", 'detalle',$detalle->output);

		$edit->buttons("save", "undo", "delete", "back");
		$edit->build();
		
		//$smenu['link']=barra_menu('201');
		///$data['smenu'] = $this->load->view('view_sub_menu', $smenu,true);
		$conten["form"]  =&  $edit;
		$data['content'] = $this->load->view('view_conversiones', $conten,true); 
		$data["head"]    = script("tabber.js").script("prototype.js").$this->rapyd->get_head().script("scriptaculous.js").script("effects.js");
		$data['title']   = ' Conversiones ';
		$this->load->view('view_ventanas', $data);
	}
	
	function dpto() {		
		$this->rapyd->load("dataform");
		$campo='ccosto'.$this->uri->segment(4);
 		$script='
 		function pasar(){
			if($F("departa")!="-!-"){
				window.opener.document.getElementById("'.$campo.'").value = $F("departa");
				window.close();
			}else{
				alert("Debe elegir un departamento");
			}
		}';
		
		$form = new DataForm('');
		$form->script($script);
		$form->fdepar = new dropdownField("Departamento", "departa");
		$form->fdepar->option('-!-','Seleccion un departamento');
		$form->fdepar->options("SELECT depto,descrip FROM dpto WHERE tipo='G' ORDER BY descrip");
		$form->fdepar->onchange='pasar()';
		$form->build_form();
		
		$data['content'] =$form->output;
		$data["head"]    =script('prototype.js').$this->rapyd->get_head();
		$data['title']   =' Seleccione un departamento ';
		$this->load->view('view_detalle', $data);
	}

	function _guarda_detalle($do) {
		$cant=$this->input->post('cant_0');
		$i=$o=0;
		while($o<$cant){
			if (isset($_POST["codigo$i"])){
				if($this->input->post("codigo$i")){
						
					$sql = "INSERT INTO itconversiones (fecha,numero,proveed,depo,codigo,descrip) VALUES(?,?,?,?,?,?)";

					//$haber=($this->input->post("monto$i") < 0)? $this->input->post("monto$i")*(-1) : 0;
					
					$llena=array(
							0=>$do->get('fecha'),
							1=>$do->get('numero'),
							2=>$do->get('proveed'),
							3=>$do->get('depo'),
							4=>$this->input->post("codigo$i"),
							5=>$this->input->post("descrip$i"),
							6=>$this->input->post("codigo$i"),

							);
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
		$control=$do->get('control');
		$sql = "DELETE FROM itconversiones WHERE control='$control'";
		$this->db->query($sql);
	}

	function _pre_del($do){
		$codigo=$do->get('comprob');
		$chek =   $this->datasis->dameval("SELECT COUNT(*) FROM cpla WHERE codigo LIKE '$codigo.%'");
		$chek +=  $this->datasis->dameval("SELECT COUNT(*) FROM itcasi WHERE cuenta='$codigo'");
		
		if ($chek > 0){
			$do->error_message_ar['pre_del'] = $do->error_message_ar['delete']='Plan de Cuenta tiene derivados o movimientos';
			return False;
		}
		return True;
	}
	
	function _pre_insert($do){
		$sql    = 'INSERT INTO ntransa (usuario,fecha) VALUES ("'.$this->session->userdata('usuario').'",NOW())';
    $query  =$this->db->query($sql);
    $transac=$this->db->insert_id();
    
		$sql    = 'INSERT INTO nconversiones (usuario,fecha) VALUES ("'.$this->session->userdata('usuario').'",NOW())';
    $query  =$this->db->query($sql);
    $control =str_pad($this->db->insert_id(),8, "0", STR_PAD_LEFT);
    
    $do->set('control', $control);
		$do->set('transac', $transac);
		$do->set('estampa', 'CURDATE()', FALSE);
		$do->set('hora'   , 'CURRENT_TIME()', FALSE);
		$do->set('usuario', $this->session->userdata('usuario'));
	}
}
?>