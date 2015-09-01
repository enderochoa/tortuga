<?php
class Usuarios extends Controller {
	
	function Usuarios(){
		parent::Controller(); 
		$this->load->library("rapyd");
		$this->load->library("menues");
		//I use THISFILE, instead __FILE__ to prevent some documented php-bugs with higlight_syntax()&__FILE__
		define ("THISFILE",   APPPATH."controllers/ventas/". $this->uri->segment(2).EXT);
		$this->datasis->modulo_id(1,1);
	}
	
	function index(){
		redirect("supervisor/usuarios/filteredgrid");
	}
	
	function filteredgrid(){
		$this->rapyd->load("datafilter","datagrid");
		
		$filter = new DataFilter("",'usuario');
		
		$filter->us_codigo = new inputField("C&oacute;digo Usuario", "us_codigo");
		$filter->us_codigo->size=15;
		
		$filter->us_nombre = new inputField("Nombre", "us_nombre");
		$filter->us_nombre->size=15;
		
		$filter->buttons("reset","search");
		$filter->build();
		
		$uri  = anchor('supervisor/usuarios/dataedit/show/<#us_codigo#>','<#us_codigo#>');
		$uri2 = anchor('supervisor/usuarios/cclave/modify/<#us_codigo#>','Cambiar clave');
		$uri3 = anchor('accesos/crear/<#us_codigo#>','Asignar Accesos');
		
		$grid = new DataGrid("");
		$grid->order_by("us_codigo","asc");
		$grid->per_page = 20;
		
		$grid->column_ORDERBY("C&oacute;digo",$uri        ,"us_codigo ");
		$grid->column_ORDERBY("Nombre"       ,"us_nombre" ,"us_nombre ","align='left'NOWRAP");
		$grid->column_ORDERBY("Supervisor"   ,"supervisor","supervisor",'align="center"    ');
		$grid->column("Cambio clave"         ,$uri2       ,'align="center"');
		$grid->column("Asignar Accesos"      ,$uri3       ,'align="center"');
		
		$grid->add("supervisor/usuarios/dataedit/create");
		$grid->build();
		
		//$data['content'] =$filter->output.$grid->output;        
		$data['filtro']  = $filter->output;
		$data['content'] = $grid->output;
		$data['script'] = script("jquery.js")."\n";
		 $data['title']   = " Usuarios ";        
		 $data["head"]    = $this->rapyd->get_head();
		 $this->load->view('view_ventanas', $data); 
	}
	
	function dataedit($status=''){ 
		$this->rapyd->load("dataedit");
		
		$edit = new DataEdit("Usuarios", "usuario");
		$edit->back_url = site_url("supervisor/usuarios/filteredgrid");
		
		$edit->pre_process( 'delete','_pre_delete');
		$edit->post_process('delete','_pos_delete');
		$edit->post_process('insert','_pos_insert');
		$edit->post_process('update','_pos_update');
				
		$edit->us_codigo = new inputField("C&oacute;digo de Usuario", "us_codigo");
		$edit->us_codigo->rule = "strtoupper|required";
		$edit->us_codigo->mode = "autohide";
		$edit->us_codigo->size = 20;
		$edit->us_codigo->maxlength = 15;
		
		$edit->us_nombre = new inputField("Nombre", "us_nombre");
		$edit->us_nombre->rule = "strtoupper|required";
		$edit->us_nombre->size = 45;
		
		$edit->us_clave = new inputField("Clave","us_clave");
		if($status=='create')
		$edit->us_clave->rule = "required|matches[us_clave1]";
		$edit->us_clave->type= "password";
		$edit->us_clave->when = array("create","idle");  
		$edit->us_clave->size = 12;
		$edit->us_clave->maxlength = 15;
		
		$edit->us_clave1 = new inputField("Confirmar Clave","us_clave1");
		if($status=='create')
		$edit->us_clave1->rule = "required";
		$edit->us_clave1->type= "password";
		$edit->us_clave1->when = array("create","idle");
		$edit->us_clave1->size = 12;
		$edit->us_clave1->maxlength  = 15;
		
		$edit->supervisor = new dropdownField("Es Supervisor", "supervisor");
		$edit->supervisor->rule = "required";
		$edit->supervisor->option("N","No");
		$edit->supervisor->option("S","Si");
		$edit->supervisor->style="width:80px";
		
		$edit->buttons("modify", "save", "undo", "back","delete");
		$edit->build();
				
		$data['content'] =$edit->output;        
		$data['title']   = " Usuarios ";
		$data["head"]    = $this->rapyd->get_head();
		$this->load->view('view_ventanas', $data); 
	}

	function accesos($usr){
		$this->rapyd->load("datagrid2");
		$mSQL="SELECT a.modulo,a.titulo, IFNULL(b.acceso,'N') AS acceso,a.panel,MID(a.modulo,1,1) AS pertenece 
			FROM intramenu AS a 
			LEFT JOIN intrasida AS b ON a.modulo=b.modulo AND b.usuario='$usr' 
			WHERE MID(a.modulo,1,1)!=0 ORDER BY MID(a.modulo,1,1), a.panel,a.modulo";
		$select=array('a.modulo','a.titulo', "IFNULL(b.acceso,'N') AS acceso",'a.panel',"MID(a.modulo,1,1) AS pertenece");
		
		//$grid = new DataGrid2("Accesos del Usuario $usr");
		//$grid->agrupar('Panel: ', 'panel');
		//$grid->use_function('convierte','number_format','str_replace');
		//$grid->db->select($select);
		//$grid->db->from('intramenu AS a');
		//$grid->db->join('intrasida AS b',"a.modulo=b.modulo AND b.usuario='$usr'",'LEFT');
		//$grid->db->where('MID(a.modulo,1,1)!=0');
		//$grid->db->orderby('a.modulo, a.panel');
		////$grid->per_page = 20;
		//$grid->column("Titulo" ,"titulo");
		//$grid->column("Modulo" ,"modulo",'align=left');
		//$grid->column("Acceso" ,"acceso",'align=right');
		//$grid->build();

		$mc = $this->db->query($mSQL);
		$tabla=form_open('accesos/guardar').form_hidden('usuario',$usr).'<div id=\'ContenedoresDeData\'><table width=100% cellspacing="0">';
		$i=0;
		$panel = '';
		foreach( $mc->result() as $row ){
			if(strlen($row->modulo)==1) {
				$tabla .= '<tr><th colspan=2>'.$row->titulo.'</th></tr>';
				$panel = '';
			}
				
			elseif( strlen($row->modulo)==3 ) {
				if ($panel <> $row->panel ) {
				    $tabla .= '<tr><td colspan=2 bgcolor="#CCDDCC">'.$row->panel.'</td></tr>';
				    $panel = $row->panel ;
				};
				
				$tabla .= '<tr><td>'.$row->titulo.'</td><td>'.form_checkbox('accesos['.$i.']',$row->modulo,$row->acceso).'</td></tr>';
				$i++;
			}else{
				$tabla .= '<tr><td><b>&nbsp;&nbsp;-&nbsp;</b>'.$row->titulo.'</td><td>'.form_checkbox('accesos['.$i.']',$row->modulo,$row->acceso).'</td></tr>';
				$i++;
			}
		}
		$tabla.='</table></div>';
		$tabla.=form_hidden('usuario',$usr).form_submit('pasa','Guardar').form_close();

		$data['content'] = $tabla;
		$data['title']   = " Asignar Accesos ";
		$data["head"]    = style("estilos.css").$this->rapyd->get_head();
		$this->load->view('view_ventanas', $data);
		
	}

	function cclave(){ 
		$this->rapyd->load("dataedit");
		
		$edit = new DataEdit("Cambio de clave de usuario", "usuario");
		$edit->back_url = site_url("supervisor/usuarios/filteredgrid");
		$edit->post_process('update','_pos_updatec');
		
		$edit->us_codigo = new inputField("C&oacute;digo de Usuario", "us_codigo");
		$edit->us_codigo->mode = "autohide";
		$edit->us_codigo->when = array("show");
		
		$edit->us_clave = new inputField("Clave","us_clave");
		$edit->us_clave->rule = "required|matches[us_clave1]";
		$edit->us_clave->type= "password";
		$edit->us_clave->when = array("modify","idle");  
		$edit->us_clave->size = 12;
		$edit->us_clave->maxlength = 15;
		
		$edit->us_clave1 = new inputField("Confirmar Clave","us_clave1");
		$edit->us_clave1->rule = "required";
		$edit->us_clave1->type= "password";
		$edit->us_clave1->when = array("modify","idle");
		$edit->us_clave1->size = 12;
		$edit->us_clave1->maxlength  = 15;
		
		$edit->buttons("modify", "save", "undo", "back","delete");
		$edit->build();
				
		$data['content'] =$edit->output;        
		$data['title']   = " Cambio de clave ";
		$data["head"]    = $this->rapyd->get_head();
		$this->load->view('view_ventanas', $data); 
	}
	
	function _pre_delete($do) {
		$codigo=$do->get('us_codigo');
		if ($codigo==$this->session->userdata('usuario')){
			$do->error_message_ar['pre_del'] = 'No se puede borrar usted mismo';
			return FALSE;
		}
		return TRUE;
	}
	
	function _pos_delete($do){
		$codigo=$do->get('us_codigo');
		$mSQL="DELETE FROM intrasida WHERE usuario='$codigo'";
		$this->db->query($mSQL);
		logusu('USUARIOS',"BORRADO EL USUARIO $codigo");
		return TRUE;
	}
	
	function _pos_insert($do){
		$codigo=$do->get('us_codigo');
		$superv=$do->get('supervisor');
		logusu('USUARIOS',"CREADO EL USUARIO $codigo, SUPERVISOR $superv");
		return TRUE;
	}
	
	function _pos_update($do){
		$codigo=$do->get('us_codigo');
		$superv=$do->get('supervisor');
		logusu('USUARIOS',"MODIFICADO EL USUARIO $codigo, SUPERVISOR $superv");
		return TRUE;
	}

	function _pos_updatec($do){
		$codigo=$do->get('us_codigo');
		$superv=$do->get('supervisor');
		logusu('USUARIOS',"CAMBIO DE CLAVE DEL USUARIO $codigo");
		return TRUE;
	}
	
	function instalar(){
		$query="ALTER TABLE `usuario`  ADD COLUMN `internet` CHAR(1) NULL DEFAULT 'N'";
		$this->db->simple_query($query);
		$mSQL="ALTER TABLE `usuario` ADD COLUMN `caja` INT NULL DEFAULT NULL";
		$this->db->simple_query($mSQL);
	}
}
?>
