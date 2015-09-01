<?
class mpru extends Controller {
	 
	function mpru(){
		parent::Controller(); 
		$this->load->library("rapyd");
	}
	
	function dataedit(){
 		$this->rapyd->load("dataedit");
 		$edit = new DataEdit("", "prueba");
 		
 		$edit->id = new inputField("C&oacute;digo", "id");
		$edit->id->rule = "required|trim";
		$edit->id->size = 16;		
		$edit->id->maxlength = 15;
 		
 		
 		$edit->html = new editorField("Contenido", "html");
		$edit->html->rule = "required";
		$edit->html->rows = 20;
		//$edit->contenido->upload_path  = $this->upload_path;
		$edit->html->cols=90;    
		$edit->html->when = array("modify");  
		//
		//$edit->html2 = new htmlField("Codice HTML", "html");  
   	//$edit->html2->cols = 70;  
   	//$edit->html2->rows = 10;  
		
		$edit->iframe = new iframeField("related", "mpru/a","500");
		  
   	
   	$edit->iframe->when = array("show");  
   	//$edit->iframe->group = "Related Records";  
		
		//$edit->container = new containerField("alert","html");  
		//$edit->container->when = array("show");
		//
		//$edit->free = new freeField("Free Field","html","html");
		
		$edit->buttons("modify", "save", "undo", "delete", "back");
		$edit->build();
		
		$data['content'] =$edit->output;
		$data["head"]    = script("tabber.js").script("prototype.js").$this->rapyd->get_head().script("scriptaculous.js").script("effects.js");
		$data['title']   = ' Catalogos de Inventarios ';
		$this->load->view('view_ventanas', $data);
	}
	function a(){
		$query = $this->db->query("select id,html from prueba where id=964654");
		foreach ($query->result_array() as $row)
					echo $row['html'];                  
		echo "<img src='http://www.ingresosdesdesuhogar.com/carro.jpg' width='100' border='0'/>";
	}
	
	
}
?>