<?php
class fondo extends Controller {
	
	var $titp  = 'Fuentes de Financiamiento';
	var $tits  = 'Fuente de Financiamiento';
	var $url   = 'presupuesto/fondo/';
	
	function fondo(){
		parent::Controller();
		$this->load->library("rapyd");
	}
	
	function  index(){
		redirect("presupuesto/fondo/filteredgrid");
	}
	
	function filteredgrid(){
		$this->datasis->modulo_id(61,1);
		
		$this->rapyd->load("datafilter2","datagrid");//
				
		$filter = new DataFilter2("","fondo");
		
		$filter->fondo = new inputField("F. Financiamiento", "fondo");
		$filter->fondo->size=20;
		$filter->fondo->clause="likerigth";
		
		$filter->descrip = new inputField("Descripci&oacute;n", "descrip");		
		$filter->descrip->size=20;
		$filter->descrip->clause="likerigth";
		
		$filter->buttons("reset","search");
		
		$filter->build();
		
		$uri = anchor('presupuesto/fondo/dataedit/show/<raencode><#fondo#></raencode>','<#fondo#>');
		
		$grid = new DataGrid("");
		
		$grid->order_by("fondo","asc");
		
		$grid->column_orderby("Fondo"             ,$uri         ,"fondo"      ,"align='left'      ");
		$grid->column_orderby("Descripci&oacute;n","descrip"    ,"descrip"    ,"align='left'NOWRAP");
		
		$grid->add("presupuesto/fondo/dataedit/create");
		
		$grid->build();
		
		$data['filtro']  = $filter->output;
		$data['content'] = $grid->output;
		$data['script'] = script("jquery.js")."\n";
		$data['title']   = "$this->titp";//"  ";
		//$data['content'] = $filter->output.$grid->output;
		//$data['title']   = " $this->titp ";
		$data["head"]    = $this->rapyd->get_head();
		$this->load->view('view_ventanas', $data);
	}
	
	function dataedit(){
		//$this->datasis->modulo_id(101,1);
		
		$this->rapyd->load("dataedit");
		
		$script='
			$(".inputnum").numeric(".");
		';
		
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
		$bcplap =$this->datasis->p_modbus($mCPLA,"cuentap");
		$fbcpla  =$this->datasis->p_modbus($mCPLA,"fcuenta" );
		$fbcplap =$this->datasis->p_modbus($mCPLA,"fcuentap");
    
		$edit = new DataEdit($this->tits, "fondo");
		
		$edit->script($script,"create");
		$edit->script($script,"modify");
		
		$edit->back_url = site_url("presupuesto/fondo/filteredgrid");
		
		$edit->fondo = new inputField("F. Financiamiento", "fondo");
		$edit->fondo->size=20;
		$edit->fondo->maxlength=20;
		
		$edit->descrip = new inputField("Descripci&oacute;n", "descrip");		
		$edit->descrip->size      =60;
		$edit->descrip->maxlength =100;
      
		$edit->cuenta = new inputField("Cuenta. Contable", "cuenta");
		$edit->cuenta->rule='callback_chcuentac|trim';
		$edit->cuenta->size =12;    
		$edit->cuenta->readonly=true;
		$edit->cuenta->append($bcpla);
		$edit->cuenta->group='Ordenes de Pago';
		
		
		$edit->cuentap = new inputField("Cuenta. Pasivo", "cuentap");
		$edit->cuentap->rule='callback_chcuentac|trim';
		$edit->cuentap->size =20;    
		$edit->cuentap->readonly=true;
		$edit->cuentap->append($bcplap);
		$edit->cuentap->group='Ordenes de Pago';
		
		$edit->fcuenta = new inputField("Cuenta. Contable", "fcuenta");
		$edit->fcuenta->rule='callback_chcuentac|trim';
		$edit->fcuenta->size =20;    
		//$edit->fcuenta->readonly=true;
		$edit->fcuenta->append($fbcpla);
		$edit->fcuenta->group='Fondo Anticipo';
		
		$edit->fcuentap = new inputField("Cuenta. Pasivo", "fcuentap");
		$edit->fcuentap->rule='callback_chcuentac|trim';
		$edit->fcuentap->size =20;    
		//$edit->fcuentap->readonly=true;
		$edit->fcuentap->append($fbcplap);
		$edit->fcuentap->group='Fondo Anticipo';
		
		$edit->partidaiva = new inputField("Partida Iva", "partidaiva");		
		$edit->partidaiva->size      =25;
		$edit->partidaiva->maxlength =25;
		
		$edit->buttons("modify", "save", "undo", "delete", "back");
		 
		$edit->build();
		
		$data['content'] = $edit->output;
		$data['title']   = "$this->tits";
		$data["head"]    = script("jquery.pack.js").$this->rapyd->get_head();
		$this->load->view('view_ventanas', $data);
	}
	
	function instalar(){
		$query="ALTER TABLE `fondo`  ADD COLUMN `partidaiva` VARCHAR(25) NULL DEFAULT NULL";
		$this->db->simple_query($query);
		$query="ALTER TABLE `fondo`  CHANGE COLUMN `cuenta` `cuenta` VARCHAR(25) NULL DEFAULT NULL ";
		$this->db->simple_query($query);
		$query="ALTER TABLE `fondo` ADD COLUMN `cuentap` VARCHAR(25) NULL DEFAULT NULL ";
		$this->db->simple_query($query);
		$query="ALTER TABLE `fondo`  ADD COLUMN `fcuenta` VARCHAR(25) NULL DEFAULT NULL ,  ADD COLUMN `fcuentap` VARCHAR(25) NULL DEFAULT NULL ";
		$this->db->simple_query($query);
		
	}
}
?>
