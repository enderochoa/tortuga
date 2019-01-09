<?php
class  rpers extends Controller {
	
	
	
	function rpers(){
		parent::Controller(); 
		$this->load->library("rapyd");
	}
	function index() {

		$this->rapyd->load("dataform","datatable",'datagrid');
		$this->load->library('table');
				
		//$mSQL=$this->db->query("SHOW tables");
		//$row = $mSQL->result_array();
		//$tables =$row;
		
		//$base_process_uri= $this->rapyd->uri->implode_uri("base_uri","gfid","orderby");
		
		//$filter = new DataForm($this->rapyd->uri->add_clause($base_process_uri, "search"));
		
		$filter = new DataForm('nomina/rpers/process');
		$filter->title('Filtro del Reporte');
		//$filter->attributes=array('onsubmit'=>'is_loaded()');
		
		$filter->tabla=new dropdownField("Tabla","tabla");
		$filter->tabla->option("pers","Personal");
		$filter->tabla->clause="";
		
		$filter->obra = new dropdownField("Obra", "depto"); 
		$filter->obra->db_name='depto';
		$filter->obra->clause="where"; 
		$filter->obra->option(" ","Todos");  
		$filter->obra->options("SELECT depto, descrip FROM dpto ORDER BY depto ");  
		$filter->obra->operator="=";
		
		$filter->status = new dropdownField("Status","status");
		$filter->status->option("","Todos");
		$filter->status->option("A","Activos");
		$filter->status->option("I","Inactivos");
		$filter->status->style='width:100px';
				
				    
		//if($this->rapyd->uri->is_set("search")){
			
					$mSQL='DESCRIBE pers';			
					$query = $this->db->query($mSQL);		            
					if ($query->num_rows() > 0){                    
						foreach ($query->result_array() as $row){     
							$data[]=$row;                               
						}                                             
					}
					
					function ractivo($field){
						$data2 = array(
						    'name'        => 'campos[]',
						    'id'          => 'c'.$field,
						    'value'       => $field,
						    'checked'     => FALSE,
						    'style'       => 'margin:5px',
						   );
						
						$retorna = form_checkbox($data2);
						return $retorna ;
					}                                            
												
					$grid = new DataGrid("Resultados",$data);
					$grid->use_function('ractivo');                       
		    	
					$grid->column("Campos"    , 'Field'); 
					$grid->column("Mostrar", "<ractivo><#Field#></ractivo>",'align="center"');      

					$grid->build();
					$tabla=$grid->output;
			
	 	//$filter->button("btnsubmit", "Consultar",'', $position="BL");
	  //$filter->button("btnsubmit", "Traer Tikets", form2uri(site_url("/supervisor/tiket/traertiket"),array('cliente')), $position="BL");//
	  //$filter->button("btnsubmit", "Traer Tikets", form2uri(site_url("/supervisor/tiket/traertiket"), $position="BL");//    
		$filter->build_form();
		
		
		$obra='hola';			
		$data['content'] = $filter->output.form_open("nomina/rpers/crear/$obra").$tabla.form_submit('mysubmit', 'Generar').form_close();
		$data['title']   = " Reporte ";
		$data["head"]    = script("jquery.js").$this->rapyd->get_head();
		$this->load->view('view_ventanas', $data);
				
	}
	
	function crear(){
		//echo $obra;
		$this->load->library('encrypt');
		
		$campos=$this->input->post('campos');
			
		$line='SELECT ';
		foreach ($campos as $key => $val) {
			$line.=$val.",";
		} 
		$line=substr($line,0,-1);
		$line.=' FROM pers';		
		//echo $obra;          
		//echo $line;
		                 
		$mSQL = $this->encrypt->encode($line);
		  
		$generar="<form action='/../../proteoerp/xlsauto/repoauto2/'; method='post'>
 		<input size='100' type='hidden' name='mSQL' value='$mSQL'>
 		<input type='submit' value='Descargar a Excel' name='boton'/>
 		</form>";
 		  
 		$data['content'] = $generar;
		$data['title']   = " Reporte ";
		$data["head"]    = script("jquery.js").$this->rapyd->get_head();
		$this->load->view('view_ventanas', $data);
		  
	}	   
}     
?>