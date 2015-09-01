<?php require_once(BASEPATH.'application/controllers/validaciones.php');
class repotra extends validaciones{

	function repotra(){
		parent::Controller();
		$this->load->library("rapyd");
		//$this->load->library("menues");
		$this->load->database();
	}

	function index(){
		redirect("supervisor/repotra/filteredgrid");
	}

	function filteredgrid(){
		$this->rapyd->load("datafilter","datagrid");
		
		function ractivo($cobrado,$numero){
		 if($cobrado=='S'){
		 		$retorna= array(
    			'name'        => $numero,
    			'id'          => $cobrado,
    			'value'       => 'accept',
    			'checked'     => TRUE,
    		);
		 	
			}else{
				$retorna = array(
    			'name'        => $numero,
    			'id'          => $cobrado,
    			'value'       => 'accept',
    			'checked'     => FALSE,
    		);
			}
			return form_checkbox($retorna);
		}

		$filter = new DataFilter("Filtro de Reportes de Trabajo", 'repotra');

		$filter->fechad = new dateField("Desde", "fechad",'d/m/Y');
		$filter->fechad->clause  ="where";
		$filter->fechad->db_name ="fecha";
		$filter->fechad->operator=">=";
		
		$filter->fechah = new dateField("Hasta", "fechah",'d/m/Y');
		$filter->fechah->clause="where";
		$filter->fechah->db_name="fecha";
		$filter->fechah->operator="<=";
		
		$filter->empresa = new inputField("Empresa", "empresa");
		$filter->empresa->size=30;
		
		$filter->tecnico= new inputField("Tecnico","tecnico1");
		$filter->tecnico->size=30;
						
		$filter->buttons("reset","search");
		$filter->build();
   //formatos/ver/repotra/<#5#>
		$uri = anchor('supervisor/repotra/dataedit/show/<#id#>','<#id#>');
		$uri2 = anchor('formatos/ver/repotra/<#id#>',"Imprimir");

		$grid = new DataGrid("Lista de Reportes de Trabajo");
		$grid->order_by("id","desc");
		$grid->use_function('ractivo');
		$grid->per_page=15;
		
		$grid->column("Numero",$uri);
		$grid->column("Empresa","empresa");
		$grid->column("Tecnico","tecnico1");
    $grid->column("Fecha"			,"<dbdate_to_human><#fecha#></dbdate_to_human>","align='center'");
		$grid->column("Reporte",$uri2);
		$grid->column("Cobrado", "<ractivo><#cobrado#>|<#id#></ractivo>",'align="center"');
		
		$grid->add("supervisor/repotra/dataedit/create");
		$grid->build();
		
		$script='';
		$url=site_url('supervisor/repotra/activar');
		$data['script']='<script type="text/javascript">
			$(document).ready(function() {
				$("form :checkbox").click(function () {
    	       $.ajax({
						  type: "POST",
						  url: "'.$url.'",
						  data: "numero="+this.name+"&cobrado="+this.id,
						  success: function(msg){
						  //alert(msg);						  	
						  }
						});
    	    }).change();
			});
			</script>';
		
		$data['content'] =$filter->output.form_open('').$grid->output.form_close().$script;
		$data['title']   = "Reporte de Trabajo";
		$data["head"]    =  script("jquery-1.2.6.pack.js");
		$data["head"]    .= script("plugins/jquery.checkboxes.pack.js").$this->rapyd->get_head();
		$this->load->view('view_ventanas', $data);
	}
	
	function activar(){
		$numero=$this->input->post('numero');	
		$cobrado=$this->input->post('cobrado');
		//if($cobrado=='S'){	
			$mSQL="UPDATE repotra SET cobrado='S' WHERE id='$numero'";
		//}else{
		//	$mSQL="UPDATE repotra SET cobrado='S' WHERE id='$numero'";
		//}
		$this->db->simple_query($mSQL);			
	}
	function dataedit(){ 
		$this->rapyd->load("dataedit");
		
		$mSCLId=array(
			'tabla'   =>'scli',
			'columnas'=>array(
			'cliente' =>'C&oacute;digo Cliente',
			'nombre'=>'Nombre', 
			'cirepre'=>'Rif/Cedula',
			'dire11'=>'Direcci&oacute;n'),
			'filtro'  =>array('cliente'=>'C&oacute;digo Cliente','nombre'=>'Nombre'),
			'retornar'=>array('cliente'=>'empresa','nombre'=>'nombre'),
			'titulo'  =>'Buscar Cliente');
		
		$boton =$this->datasis->modbus($mSCLId);
		
		$edit = new DataEdit("Agregar", "repotra");
		$edit->back_url = site_url("supervisor/repotra/filteredgrid");
	
		$edit->fecha = new DateonlyField("Fecha","fecha");
		$edit->fecha->rule = "trim|strtoupper|required";
		$edit->fecha->size = 12;
		$edit->fecha->insertValue = date("Y-m-d"); 
										
		$edit->t1horae  = new inputField("Hora Entrada", "t1horae");
		$edit->t1horae->maxlength=8;
		$edit->t1horae->size=10;
		//$edit->t1horae->rule='required';
		$edit->t1horae->append('hh:mm');
		$edit->t1horae->in='t1tipoe';

		      
		$edit->t1tipoe = new dropdownField("","t1tipoe");
	  $edit->t1tipoe->option("","");
		$edit->t1tipoe->option("AM","AM");
		$edit->t1tipoe->option("PM","PM");
		$edit->t1tipoe->style="width:70px";
		$edit->t1tipoe->append(" Turno 1");
						
		$edit->t1horas  = new inputField("Hora Salida", "t1horas");
		$edit->t1horas->maxlength=8;
		$edit->t1horas->size=10;
		//$edit->t1horas->rule='required';
		//$edit->t1horas->rule='trim|callback_chhora';
		$edit->t1horas->append('hh:mm');
		$edit->t1horas->in='t1tipos';
		  
		$edit->t1tipos = new dropdownField("","t1tipos");
		$edit->t1tipos->option("","");
		$edit->t1tipos->option("AM","AM");
		$edit->t1tipos->option("PM","PM");
		$edit->t1tipos->style="width:70px";
		
		$edit->t2horae  = new inputField("Hora Entrada", "t2horae");
		$edit->t2horae->maxlength=8;
		$edit->t2horae->size=10;
		//$edit->t2horae->rule='required';
		$edit->t2horae->append('hh:mm');
		$edit->t2horae->in='t2tipoe';

		$edit->t2tipoe = new dropdownField("","t2tipoe");
		$edit->t2tipoe->option("","");
		$edit->t2tipoe->option("AM","AM");
		$edit->t2tipoe->option("PM","PM");
		$edit->t2tipoe->style="width:70px";
		$edit->t2tipoe->append(" Turno 2");
	
		$edit->t2horas  = new inputField("Hora Salida", "t2horas");
		$edit->t2horas->maxlength=8;
		$edit->t2horas->size=10;
		//$edit->t2horas->rule='required';
		//$edit->horas->rule='trim|callback_chhora';
		$edit->t2horas->append('hh:mm');
		$edit->t2horas->in='t2tipos';

		$edit->t2tipos = new dropdownField("","t2tipos");
		$edit->t2tipos->option("","");
		$edit->t2tipos->option("AM","AM");
		$edit->t2tipos->option("PM","PM");
		$edit->t2tipos->style="width:70px";
				
		$edit->empresa = new inputField("Cliente", "empresa");
		$edit->empresa->rule = "trim|strtoupper|required";
		$edit->empresa->size = 12;  
		$edit->empresa->maxlength = 60;
		$edit->empresa->append($boton);
		
		$edit->nombre = new inputField("Nombre","nombre");
		$edit->nombre->rule = "trim|strtoupper|required";
		$edit->nombre->size = 60;     
		$edit->nombre->maxlength = 50;

		
		$edit->tecnico1 = new inputField("Realizado por","tecnico1");
		$edit->tecnico1->rule = "trim|strtoupper|required";
		$edit->tecnico1->size = 50;     
		$edit->tecnico1->maxlength = 50;
               
				
		$edit->tecnico2 = new inputField("Realizado por","tecnico2");
		$edit->tecnico2->rule = "trim|strtoupper";
		$edit->tecnico2->size = 50;     
		$edit->tecnico2->maxlength = 50;
					
		$edit->tecnico3 = new inputField("Realizado por","tecnico3");
		$edit->tecnico3->rule = "trim|strtoupper";
		$edit->tecnico3->size = 50;     
		$edit->tecnico3->maxlength = 50;
		
		$edit->informe = new textareaField("Actividad","informe");  
		$edit->informe->cols = 80;                                   
		$edit->informe->rows = 30;                                   
		$edit->informe->rule = "trim|strtoupper|required";
		
		$edit->observa = new textareaField("Observaciones","observa");  
		$edit->observa->cols = 80;                                   
		$edit->observa->rows = 3;                                   
		$edit->observa->rule = "trim|strtoupper";
		
		$edit->cobrado = new dropdownField("Cobrado","cobrado");
		$edit->cobrado->option("N","N");
		$edit->cobrado->option("S","S");
		$edit->cobrado->style="width:70px";
  				    
		$edit->buttons("modify", "save", "undo", "delete", "back");
		$edit->build();
		
		$smenu['link']=barra_menu('912');		
		$data['content'] = $edit->output;
		$data['smenu']   = $this->load->view('view_sub_menu', $smenu,true);
		$data['title']   = "Reporte de Trabajo";
		$data["head"]    = script("jquery.pack.js").script("plugins/jquery.numeric.pack.js").script("plugins/jquery.floatnumber.js").$this->rapyd->get_head();
		$this->load->view('view_ventanas', $data);
	}
	function instalar(){
		$mSQL="CREATE TABLE `matbar`.`repotra` (`id` TINYINT AUTO_INCREMENT, `estampa` TIMESTAMP, `fecha` DATE, `t1horae` VARCHAR (8),`t2horae` VARCHAR (8), `t1horas` VARCHAR (8),`t2horas` VARCHAR (8),`empresa` VARCHAR (50), `tecnico1` VARCHAR (50), `tecnico2` VARCHAR (50), `tecnico3` VARCHAR (50), `informe` TEXT,`observa` TEXT, `t1tipos` VARCHAR(10),`t2tipos` VARCHAR(10),`t1tipoe` VARCHAR(10),`t2tipoe` VARCHAR(10),`nombre` VARCHAR(60),PRIMARY KEY(`id`)) TYPE = MyISAM";
		$this->db->simple_query($mSQL);
	}
}
?>