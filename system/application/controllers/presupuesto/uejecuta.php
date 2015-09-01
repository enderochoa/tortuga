<?php
class uejecuta extends Controller {
	
	var $url="presupuesto/uejecuta";
	
	function uejecuta(){
		parent::Controller();
		$this->load->library("rapyd");
	}
	
	function  index(){
		redirect("presupuesto/uejecuta/filteredgrid");
	}
	
	function filteredgrid(){
		$this->datasis->modulo_id(40,1);
		
		$this->rapyd->load("datafilter2","datagrid");//
		
		$filter = new DataFilter2("","uejecutora");
		
		$filter->codigo = new inputField("C&oacute;digo", "codigo");
		$filter->codigo->size=5;
		//$filter->codigo->clause="likerigth";
		
		$filter->nombre = new inputField("Nombre", "nombre");
		$filter->nombre->size=40;
		$filter->nombre->clause="likerigth";
		
		//$filter->director = new inputField("Director", "director");		
		
		$filter->buttons("reset","search");
		
		$filter->build();
		
		$uri = anchor('presupuesto/uejecuta/dataedit/show/<#codigo#>','<#codigo#>');
		
		$grid = new DataGrid("");
		
		$grid->order_by("codigo","asc");
		
		$grid->column_orderby("Unidades Ejecutoras" ,$uri        ,"codigo"     ,"align='left'      ");
		//$grid->column_orderby("Codigo administrativo","codigoadm"    ,"codigoadm"     ,"align='left'NOWRAP");
		$grid->column_orderby("Nombre"              ,"nombre"    ,"nombre"     ,"align='left'NOWRAP");
		$grid->column_orderby("Director"            ,"director"  ,"director"   ,"align='left'NOWRAP");
		
		$grid->add("presupuesto/uejecuta/dataedit/create");
		
		$grid->build();
		
		$data['content'] = $grid->output;
		$data['filtro']  = $filter->output;
		$data['title']   = "Unidades Ejecutoras"; //"  ";
		$data["script"]  = script("jquery.js")."\n"; 
		$data["head"]    = $this->rapyd->get_head();
		$this->load->view('view_ventanas', $data);
	}
	
	function dataedit(){
		$this->datasis->modulo_id(40,1);
		
		$this->rapyd->load("dataedit");
		
		$qformato=$this->qformato=$this->datasis->formato_cpla();
		
		$mCPLA=array(
		  'tabla'   =>'cpla',
		  'columnas'=>array(
		    'codigo' =>'C&oacute;digo',
		    'denominacion'=>'Descripci&oacute;n'),
		  'filtro'  =>array('codigo'=>'C&oacute;digo','denominacion'=>'Denominacion'),
		  'retornar'=>array('codigo'=>'<#i#>'),
		  'titulo'  =>'Buscar Cuenta',
		  'where'   =>"codigo LIKE \"$qformato\"",
		  'p_uri'   =>array(4=>'<#i#>'),
		  );
		$bcpla  =$this->datasis->p_modbus($mCPLA,"cuenta" );
		
		$link2=site_url($this->url.'/sugerir');
		$script='
		$(".inputnum").numeric(".");
		
		function sugerir(){
			$.ajax({
					url: "'.$link2.'",
					success: function(msg){
						if(msg){
							$("#codigo").val(msg);
						}
						else{
							alert("No es posible generar otra sugerencia. Coloque el c&oacute;digo manualmente");
						}
					}
				});
			}
		';
		
		$edit = new DataEdit("Unidad Ejecutora", "uejecutora");
		$edit->script($script,"create");
		
		$edit->pre_process('delete'  ,'_pre_delete');
		
		$edit->back_url = site_url("presupuesto/uejecuta/filteredgrid");
		
		$sugerir='<a href="javascript:sugerir();" title="Sugerir un Codigo aleatorio">Sugerir C&oacute;digo </a>';
		$edit->codigo = new inputField("C&oacute;digo", "codigo");
		$edit->codigo->size      = 8;
		$edit->codigo->maxlength = 8;
		$edit->codigo->mode      = "autohide";
		$edit->codigo->css_class = 'inputnum';
		$edit->codigo->rule      = 'required';
		$edit->codigo->append($sugerir);
		 
		 
		//$edit->codigoadm = new inputField("C&oacute;digo Administrativo", "codigoadm");
		//$edit->codigoadm->size   = 20;
		//$edit->codigo->maxlength = 20;
//		$edit->codigo->css_class = 'inputnum';
//		$edit->codigo->rule      = 'required';
		 
		$edit->nombre = new inputField("Nombre", "nombre");
		$edit->nombre->size=40;
		$edit->nombre->maxlength=80;
		$edit->nombre->rule='required';
		
		$edit->director = new inputField("Director", "director");
		$edit->director->size=60;
		$edit->director->maxlength=100;
		
		$edit->funciones =new textareaField("Funciones","funciones");
		$edit->funciones->rows=8;
		$edit->funciones->cols=60;
		
		$edit->cuenta = new inputField("Cuenta. Contable", "cuenta");
		$edit->cuenta->rule    ='callback_chcuentac|trim';
		$edit->cuenta->size    =12;    
		$edit->cuenta->readonly=true;
		$edit->cuenta->append($bcpla);
		
		$edit->cuenta = new inputField("Cuenta. Contable", "cuenta");
		$edit->cuenta->rule='callback_chcuentac|trim';
		$edit->cuenta->size =20;    
		$edit->cuenta->readonly=true;
		$edit->cuenta->append($bcpla);
		$edit->cuenta->group='Fondo Anticipo';
		
		$edit->buttons("modify", "save", "undo", "delete", "back","add");
		
		$edit->build();

		$data['content'] = $edit->output;
		$data['title']   = "Unidad Ejecutora";
		$data["head"]    = script("jquery.js").$this->rapyd->get_head();
		$this->load->view('view_ventanas', $data);
		
	}

	function sugerir(){
		$ultimo=$this->datasis->dameval("SELECT LPAD(hexa,4,0) FROM serie LEFT JOIN uejecutora ON LPAD(codigo,4,0)=LPAD(hexa,4,0) WHERE valor<65535 AND codigo IS NULL LIMIT 1");
		echo $ultimo;
	}
	
	function _pre_delete($do){
		$codigo=$do->get('codigo');
		$codigo=$this->db->escape($codigo);
		$cant  =$this->datasis->dameval("SELECT COUNT(*) FROM presupuesto WHERE uejecutora=$codigo");
		if($cant>0){
			$error='No se Puede Eliminar la Unidad ejecutora debido a que tiene partidas relacionadas';
			$do->error_message_ar['pre_del']="<div class='alert'>".$error."</div>";
			return false;
		}
	}
	
	function instalar(){
		$query="ALTER TABLE `uejecutora`  ADD COLUMN `cuenta` VARCHAR(25) NULL";
		$this->db->simple_query($query);
		$query="ALTER TABLE `uejecutora` CHANGE COLUMN `codigo` `codigo` CHAR(8) NOT NULL DEFAULT '' FIRST";
		$this->db->simple_query($query);
	}
}
?>
