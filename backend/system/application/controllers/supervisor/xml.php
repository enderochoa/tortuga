<?php
class xml extends Controller {
 	var $upload_path;
	function xml(){
		parent::Controller();
		$this->load->library("rapyd");
		$this->load->helper('download');
		$this->load->library("path");
		$path=new Path();
		$path->setPath($this->config->item('uploads_dir'));
		$path->append('/archivos');
		$this->upload_path =$path->getPath().'/';
	}
	function crear(){
		$this->load->dbutil();
		
		$config = array (
                  'root'    => 'root',
                  'element' => 'element',
                  'newline' => "\n",
                  'tab'     => "\t"
                );
		
		$query = $this->db->query("select * FROM gser where numero='000002' and proveed='FLORO'");
		$cvs=$this->dbutil->csv_from_result($query);
		$name = 'Archivo.cvs';
		//$data = 'Hola katy';
		//write_file('./uploads/archivos/$name', $data);
		force_download($name,$cvs);		
	} 
	function carga(){
		
		$this->rapyd->load('dataform');  
		                                 
		$form = new DataForm('supervisor/xml/carga/resp');
		
	  $form->archivo = new uploadField("Archivo","archivo");          
		$form->archivo->upload_path   = $this->upload_path;    
		$form->archivo->allowed_types = "xml";                 
		$form->archivo->delete_file   =false;
		         		                      				 
    $form->submit("btnsubmit","Subir");  
    $form->build_form();
      
    $data['content'] = $form->output;
    $data['title']   = " Subir Archivo ";
    $data["head"]    = $this->rapyd->get_head();
    $this->load->view('view_ventanas', $data);
	}
	function resp(){
		echo 'hola';
	}
	
	function ver($file='Archivo1.xml'){
		//$file = $this->input->post('archivo');
		//echo 'Archivo='.$file;
		if(empty($file)) return FALSE;
		$this->load->helper('file');
		$string = read_file("./uploads/archivos/$file");
		$string = $string;
		echo $string;
	}
	function insertar($filename='Archivo1.xml',$table='gser'){
		$this->load->library("xml2sql");	
		$xml= new xml2sql($filename);
		$db=$xml->analizador();
		//echo $filename;
		foreach ($db as $key => $val) {
			  echo "<pre>";
		    print_r($val);
		    echo "</pre>";
		$this->db->insert($table, $val);
		echo $key;
			
		}
	}
}   
?>
