<?php
//retenciones
 class Rete extends Controller {
	
	var $data_type = null;
	var $data = null;
	 
	function rete (){
		parent::Controller(); 
		//required helpers for samples
		$this->load->helper('url');
		$this->load->helper('text');
		//rapyd library
		$this->load->library("rapyd");
		define ("THISFILE",   APPPATH."controllers/nomina". $this->uri->segment(2).EXT);
   }
   function index(){
    	$this->datasis->modulo_id(21,1);
    	redirect("presupuesto/rete/filteredgrid");
    }
 	function filteredgrid(){
		$this->rapyd->load("datafilter","datagrid");
		$this->rapyd->uri->keep_persistence();
	
		$filter = new DataFilter("Filtro por C&oacute;digo", 'rete');
		$filter->codigo = new inputField("C&oacute;digo", "codigo");
		$filter->codigo->size=15;
		
		$filter->activida = new inputField('Activida','activida');
		$filter->activida->rule      ='trim';
		$filter->activida->cols      = 70;
		$filter->activida->rows      = 4;
		
		$filter->seniat = new inputField('Seniat','seniat');
		$filter->seniat->rule      ='trim';
		$filter->seniat->size      =6;
		$filter->seniat->maxlength =4;
		
		$filter->buttons("reset","search");
		$filter->build();

		$uri = anchor('presupuesto/rete/dataedit/show/<#codigo#>','<#codigo#>');

		$grid = new DataGrid("Lista de Retenciones");
		$grid->order_by("codigo","asc");
		$grid->per_page = 20;

		$grid->column_orderby('Codigo'            ,"$uri"                                             ,'codigo'       ,'align="left"');
		$grid->column_orderby('Seniat'            ,"seniat"                                           ,'seniat'       ,'align="left"');
		$grid->column_orderby('Actividad'         ,"activida"                                         ,'activida'     ,'align="left"');
		$grid->column_orderby('Tari1'             ,"<nformat><#tari1#></nformat>"                     ,'tari1'        ,'align="right"');
		$grid->column_orderby('Pama1'             ,"<nformat><#pama1#></nformat>"                     ,'pama1'        ,'align="right"');
		$grid->column_orderby('Tari2'             ,"<nformat><#tari2#></nformat>"                     ,'tari2'        ,'align="right"');
		$grid->column_orderby('Pama2'             ,"<nformat><#pama2#></nformat>"                     ,'pama2'        ,'align="right"');
		$grid->column_orderby('Tari3'             ,"<nformat><#tari3#></nformat>"                     ,'tari3'        ,'align="right"');
		$grid->column_orderby('Pama3'             ,"<nformat><#pama3#></nformat>"                     ,'pama3'        ,'align="right"');
		$grid->column_orderby('Tipo'              ,"tipo"                                             ,'tipo'         ,'align="left"');
		$grid->column_orderby('Porcentsustra'     ,"<nformat><#porcentsustra#></nformat>"             ,'porcentsustra','align="right"');
				  	  						
		$grid->add("presupuesto/rete/dataedit/create");
		$grid->build();
		
		$data['content'] = $filter->output.$grid->output;
		$data['title']   = " Retenciones ";
		$data["head"]    = $this->rapyd->get_head();
		$this->load->view('view_ventanas', $data);	
	}
	function dataedit()
 	{
		$this->rapyd->load("dataedit");
		
		$script ='
		$(function() {
			$(".inputnum").numeric(".");
		});
		';
		
		$edit = new DataEdit("Retenciones", "rete");
		$edit->back_url = site_url("presupuesto/rete/filteredgrid");
		$edit->script($script, "create");
		$edit->script($script, "modify");
		
		$edit->post_process('insert','_post_insert');
		$edit->post_process('update','_post_update');
		$edit->post_process('delete','_post_delete');

		$edit->codigo = new inputField("Codigo", "codigo");
		$edit->codigo->mode="autohide";
		$edit->codigo->size =7;
		$edit->codigo->maxlength=4;
		$edit->codigo->rule ="required|callback_chexiste";
		
		$edit->activida = new inputField("Pago de", "activida");
		$edit->activida->size =80;
		$edit->activida->rule= "strtoupper|required";
		
		$edit->tipo =  new dropdownField("Tipo", "tipo");
		$edit->tipo->option("NR"  ,"Persona Natural Residente");
		$edit->tipo->option("NNR" ,"Persona Natural No Residente");
		$edit->tipo->option("JD"  ,"Persona Jurídica Domiciliada");
		$edit->tipo->option("JND" ,"Persona Jurídica No Domiciliada");
		$edit->tipo->option("JNCD","Persona Jurídica No Constituida Domiciliada");
		$edit->tipo->style='width:200px';
		
		//$edit->base1 = new inputField("Base Imponible", "base1");
		//$edit->base1->size =13;
		//$edit->base1->maxlength=9;
		//$edit->base1->css_class='inputnum';
		//$edit->base1->rule='numeric';
		
		//$edit->pama1 = new inputField("Para pagos mayores a", "pama1");
		//$edit->pama1->size =13;
		//$edit->pama1->maxlength=13;
		//$edit->pama1->css_class='inputnum';
		//$edit->pama1->rule='numeric';
		
		//$edit->aux = new inputField("Codigo Seniat", "aux");
		//$edit->aux->size =13;
		//$edit->aux->maxlength=13;
		
		$edit->tari1 =new inputField("Porcentaje 1 ", "tari1");
		$edit->tari1->size =15;
		$edit->tari1->maxlength=10;
		$edit->tari1->css_class='inputnum';
		$edit->tari1->rule='numeric';
		$edit->tari1->value=0;
		
		$edit->pama1 =new inputField("Pagos Mayores 1", "pama1");
		$edit->pama1->size =15;
		$edit->pama1->maxlength=10;
		$edit->pama1->css_class='inputnum';
		$edit->pama1->rule='numeric';
		$edit->pama1->value=0;
		
		$edit->tari2 =new inputField("Porcentaje 2 ", "tari2");
		$edit->tari2->size =15;
		$edit->tari2->maxlength=10;
		$edit->tari2->css_class='inputnum';
		$edit->tari2->rule='numeric';
		$edit->tari2->value=0;
		
		$edit->pama2 =new inputField("Pagos Mayores 2", "pama2");
		$edit->pama2->size =15;
		$edit->pama2->maxlength=10;
		$edit->pama2->css_class='inputnum';
		$edit->pama2->rule='numeric';
		$edit->pama2->value=0;
		
		$edit->tari3 =new inputField("Porcentaje 3 ", "tari3");
		$edit->tari3->size =15;
		$edit->tari3->maxlength=10;
		$edit->tari3->css_class='inputnum';
		$edit->tari3->rule='numeric';
		$edit->tari3->value=0;
		
		$edit->pama3 =new inputField("Pagos Mayores 3", "pama3");
		$edit->pama3->size =15;
		$edit->pama3->maxlength=10;
		$edit->pama3->css_class='inputnum';
		$edit->pama3->rule='numeric';
		$edit->pama3->value=0;
		
		$edit->porcentsustra =new inputField("Porcentaje Sustraendo", "porcentsustra");
		$edit->porcentsustra->size =15;
		$edit->porcentsustra->maxlength=10;
		$edit->porcentsustra->css_class='inputnum';
		$edit->porcentsustra->rule='numeric';
		$edit->porcentsustra->value=0;
		
		$edit->buttons("add","modify", "save", "undo", "delete", "back");
		$edit->build();
 

		$smenu['link']=barra_menu('515');
		$data['smenu'] = $this->load->view('view_sub_menu', $smenu,true);
		$data['content'] = $edit->output;           
    $data['title']   = " Retenciones ";        
    $data["head"]    = script("jquery.pack.js").script("plugins/jquery.numeric.pack.js").script("plugins/jquery.floatnumber.js").$this->rapyd->get_head();
    $this->load->view('view_ventanas', $data);  
 	}

	function chexiste($codigo){
		$codigo=$this->input->post('codigo');
		$chek=$this->datasis->dameval("SELECT COUNT(*) FROM rete WHERE codigo='$codigo'");
		if ($chek > 0){
			$activida=$this->datasis->dameval("SELECT activida FROM rete WHERE codigo='$codigo'");
			$this->validation->set_message('chexiste',"La retencion $codigo ya existe para la actividad $activida");
			return FALSE;
		}else {
  		return TRUE;
		}	
	}
	
	function instalar(){
			$this->db->simple_query("ALTER TABLE `rete` ADD COLUMN `auxi` CHAR(15) NULL DEFAULT NULL                    ");
			$this->db->simple_query("ALTER TABLE `rete` CHANGE COLUMN `activida` `activida` TINYTEXT NULL DEFAULT NULL  ");
			$this->db->simple_query("ALTER TABLE `rete 	CHANGE COLUMN `base1` `base1` DECIMAL(9,2) NULL DEFAULT '0'     ");
			$this->db->simple_query("ALTER TABLE `rete` CHANGE COLUMN `tari1` `tari1` DECIMAL(10,2) NULL DEFAULT '0'    ");
			$this->db->simple_query("ALTER TABLE `rete` CHANGE COLUMN `pama1` `pama1` DECIMAL(13,2) NULL DEFAULT '0'    ");
			$this->db->simple_query("ALTER TABLE `rete` ADD COLUMN `tari2` `tari2` DECIMAL(13,2) NULL DEFAULT '0'       ");
			$this->db->simple_query("ALTER TABLE `rete` ADD COLUMN `hasta2` `hasta2` DECIMAL(13,2) NULL DEFAULT '0'     ");
			$this->db->simple_query("ALTER TABLE `rete` ADD COLUMN `tari3` `tari3` DECIMAL(13,2) NULL DEFAULT '0'       ");
			$this->db->simple_query("ALTER TABLE `rete` ADD COLUMN `hasta3` `hasta3` DECIMAL(13,2) NULL DEFAULT '0'     ");
			$this->db->simple_query("ALTER TABLE `rete` ADD COLUMN `porcentsustra` DECIMAL(9,4) NULL DEFAULT '0.00'     ");
			$this->db->simple_query("ALTER TABLE `rete` ADD COLUMN `seniat` VARCHAR(4) NULL DEFAULT NULL                ");
	}
}
?>
