<?php
class prueba extends Controller{
	
	function prueba(){
		parent::Controller();
		$this->load->library("rapyd");
	}

	function index(){		
		$list = array();
		$list[]=anchor('supervisor/prueba/bprefac','Borrar PreFacturas menores o iguales al d&iacute;a de ayer');
		$list[]=anchor('supervisor/prueba/bmodbus','Vaciar la tabla ModBus');
		$list[]=anchor('supervisor/prueba/centinelas','Centinelas');
		
		$attributes = array(
			'class' => 'boldlist',
			'id'    => 'mylist'
			);

		$out=ul($list, $attributes);
		$data['content'] = $out;
		$data["head"]    = script("jquery.pack.js").script("jquery.treeview.pack.js").$this->rapyd->get_head().style('jquery.treeview.css');
		$data['title']   = ' prueba ';
		$this->load->view('view_ventanas', $data);
	}
	
	function bprefac(){
		$mSQL="DELETE FROM sitems WHERE MID(numa,1,1)='_' AND fecha<CURDATE()";
		$this->db->simple_query($mSQL);
		$mSQL="DELETE FROM sfac WHERE MID(numero,1,1)='_' AND fecha<CURDATE()";
		$this->db->simple_query($mSQL);
		redirect('supervisor/prueba');
	}
	
	function bmodbus(){
		$mSQL="DELETE FROM sitems WHERE MID(numa,1,1)='_' AND fecha<CURDATE()";
		$this->db->simple_query($mSQL);
		$mSQL="TRUNCATE modbus";
		$this->db->simple_query($mSQL);
		redirect('prueba');
	}
	
	function centinelas(){
		$this->load->helper('directory');
		$this->load->library('table');
		$tmpl = array('row_start' => '<tr valign="top">');
		$this->table->set_template($tmpl); 

		$map = directory_map('./system/logs/');
		$lista=array();
		foreach($map AS $file) {
			if($file!='index.html')
				$lista[]="<a href='javascript:void(0)' onclick=\"carga('$file')\" >$file</a> <a href='javascript:void(0)' onclick=\"carga1('$file')\" > Eliminar </a>";

		}
		$copy="<br><a href='javascript:void(0)' class='mininegro'  onclick=\"$.copy('copy this text')\" >Copiar texto</a>";
		$this->table->add_row(ol($lista), '<b id="fnom">Seleccione un archivo de centinela</b><div id="log" class="marco1" style="width: 550px; min-height: 400px" ></div>');
		$link=site_url('supervisor/prueba/vercentinela');
		$link1=site_url('supervisor/prueba/eliminar');
		$data['script']  ="<script>
		  function carga(arch){
		    link='$link'+'/'+arch;
		    //alert(link);
		    $.clipboard(arch);
		    $('#fnom').text(arch);
		    $('#log').load(link);
		  };
		  function carga1(arch){
		    link='$link1'+'/'+arch;
		    //alert(link1);
		    $.clipboard(arch);
		    $('#fnom').text(arch);
		    $('#log').load(link1);
		  };
		</script>";
		$data['content'] = $this->table->generate();
		$data['title']   = " Centinelas ";
		$data["head"]    =  script("jquery.pack.js").script('plugins/jquery.clipboard.pack.js').$this->rapyd->get_head().style('marcos.css').style('estilos.css');
		$this->load->view('view_ventanas', $data);
	}
	
	function vercentinela($file=NULL){
		if(empty($file)) return FALSE;
		$this->load->helper('file');
		$string = read_file("./system/logs/$file");
		$string = "<pre id='ccopy'>".$string.'</pre>';
		echo $string;
	}
	function eliminar($file=NULL){

		if(empty($file)) return FALSE;
		$this->load->helper('file');
		$string = delete_files("./system/logs/$file");
		//redirect('prueba/centinelas');
		echo $string;
	}
}
?>
