<?php
	class prof extends Controller{
		function prof(){
			parent::Controller();
			$this->load->library("rapyd");
			$this->datasis->modulo_id(65,1);
		}
		function index(){
			redirect("nomina/prof/filteredgrid");	
		}
		function filteredgrid(){
			$this->rapyd->load("datafilter","datagrid");
			
			$filter = new DataFilter("",'prof');

			$filter->codigo= new inputField("Codigo","codigo");
			$filter->codigo->size=20;
			$filter->codigo->maxlength=8;
			
			$filter->profesion= new inputField("Profesion","profesion");
			$filter->profesion->size=20;
			$filter->profesion->maxlength=40;
			
			$filter->buttons("reset","search");
			$filter->build();
			
			$uri = anchor('nomina/prof/dataedit/show/<#codigo#>','<#codigo#>');
			
			$grid = new DataGrid("");
			$grid->per_page=15;
			
			$grid->column_orderby("Codigo"     ,$uri       ,"codigo");
			$grid->column_orderby("Profesiones","profesion","profesion","align='left'NOWRAP");
			
			$grid->add("nomina/prof/dataedit/create");
			$grid->build();
			
			//$data['content'] = $filter->output.$grid->output;
			$data['title']   = "Profesiones";
			$data['filtro']  = $filter->output;
			$data['content'] = $grid->output;
			$data['script'] = script("jquery.js")."\n";
			$data["head"]    = $this->rapyd->get_head();	
			$this->load->view('view_ventanas', $data);	
		}
		function dataedit(){
			$this->rapyd->load("dataedit");
			
			$edit = new DataEdit("Profesiones","prof");
			$edit->back_url = site_url("nomina/prof/");
			
			$edit->post_process('insert','_post_insert');
			$edit->post_process('update','_post_update');
			$edit->post_process('delete','_post_delete');
						
			$edit->codigo = new inputField("Codigo", "codigo");
			$edit->codigo->size =10;
			$edit->codigo->mode="autohide";
			$edit->codigo->rule="strtoupper|required|callback_chexiste";
			$edit->codigo->maxlength =8;

			$edit->profesion = new inputField("Profesion", "profesion");
			$edit->profesion->size =40;
			$edit->profesion->rule="strtoupper|required";
			$edit->profesion->maxlength =40;
					  
			$edit->buttons("add","modify", "save", "undo", "delete", "back");
			$edit->build();
			
			$data['content'] = $edit->output; 		
			$data['title']   = "Profesiones";
			$data["head"]    = $this->rapyd->get_head();
			$this->load->view('view_ventanas', $data);
			}
			
			function _post_insert($do){
				$codigo=$do->get('codigo');
				$nombre=$do->get('profesion');
				logusu('prof',"PROFESION $codigo NOMBRE $nombre CREADA");
			}
			function _post_update($do){
				$codigo=$do->get('codigo');
				$nombre=$do->get('profesion');
				logusu('prof',"PROFESION $codigo NOMBRE $nombre MODIFICADA");
			}
			function _post_delete($do){
				$codigo=$do->get('codigo');
				$nombre=$do->get('profesion');
				logusu('prof',"PROFESION $codigo NOMBRE $nombre ELIMINADA");
	  	}
			function chexiste($codigo){
				$chek=$this->datasis->dameval("SELECT COUNT(*) FROM prof WHERE codigo='$codigo'");
				if ($chek > 0){
					$profesion=$this->datasis->dameval("SELECT profesion FROM prof WHERE codigo='$codigo'");
					$this->validation->set_message('chexiste',"El codigo $codigo ya existe para la profesion $profesion");
					return FALSE;
				}else {
					return TRUE;
				}
			}
			
			function instalar(){
					$this->db->simple_query("ALTER TABLE `prof` CHANGE COLUMN `profesion` `profesion` TEXT NULL DEFAULT NULL AFTER `codigo`");
			}
	}
?>
