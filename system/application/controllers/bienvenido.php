<?php
class Bienvenido extends Controller {
	function Bienvenido(){
		parent::Controller();
	}

	function index(){
		$this->session->set_userdata('panel', $this->uri->segment(3));
		$data['titulo1'] = '';
		// llamamos el menu
		$data['titulo1'] ='
		<div id="tumblelog">
        <div class="story col2">
            <h2>Logo de Tortuga</h2>';
		$data["titulo1"] .= image("portada50.png").$this->uri->segment(1).$this->uri->segment(2).$this->uri->segment(3); 
		$data['titulo1'] .='
            <p>La isla de La Tortuga marca nuestra frontera mar&iacute;tima con las islas de Aruba y Curazao, permitiendo a la naci&oacute;n ejercer la soberania sobre esa parte del mar Caribe. As&iacute; como la isla de La Tortuga, el sistema Tortuga 
			fortalece nuestra soberan&iacute;a en el &aacute;rea de la Informaci&oacute;n, ya que est&aacute; totalmente hecho en Software Libre, dando cumplimiento a lo indicado
			en el Decreto 3.390.</p>
        </div>
        <div class="story col1">
            <blockquote>
                <h2>GNU</h2>
            </blockquote>';
		$data["titulo1"] .= image("gnu.png"); 
		$data['titulo1'] .='
            <p>El sistema Tortuga est&aacute; desarrollado en Software Libre, bajo Licencia GPL. </p>
        </div>
        <div class="story col2">
            <h2>Las cuatro libertades del Software Libre son:</h2>
            <ul>
               <li>"0" Libertad de ejecutar el programa con cualquier prop&oacute;sito (privado o p&uacute;blico, educativo, comercial o militar, etc.)</li>
               <li>"1" Libertad para estudiar y modificar el programa (para lo cual es necesario poder acceder al c&oacute;digo fuente)</li>
               <li>"2" Libertad para copiar y distribuir el programa, de manera que se pueda ayudar a otros.</li>
			   <li>"3" Libertad para mejorar el programa y publicar las mejoras.</li>
            </ul>
        </div>
        <div class="story col1">
			'.image("tux-con-bandera-vzla.png").'
        </div>
		</div> <!-- /#tumblelog -->'."\n";
	
		if ($this->datasis->login())
		//$data['titulo1']  .= "<p><a href='javascript:void(0);' onclick=\"window.open('/proteoerp/chat', 'wchat', 'width=580,height=600,scrollbars=yes,status=yes,resizable=yes,screenx='+((screen.availWidth/2)-290)+',screeny='+((screen.availHeight/2)-300)+'');\">Chat</a></p>";
		$data['titulo1']  .= "</center><br>";
		$this->layout->buildPage('bienvenido/home', $data);
	}

	
	function autentificar(){
		
		$ip=$_SERVER['REMOTE_ADDR'];
		
		$usr=sha1($_POST['user']);
		$pws=sha1($_POST['pws']);
		
		if (!preg_match("/^[^'\"]+$/", $usr)){
			$sess_data = array('logged_in'=> FALSE);
			$this->session->set_userdata($sess_data);
			redirect($this->session->userdata('estaba'));
		}
		
		if($this->datasis->ip_interno($ip))
		$query ="SELECT us_nombre FROM usuario WHERE SHA(us_codigo)='$usr' AND SHA(us_clave)='$pws' ";
		else
		$query ="SELECT us_nombre FROM usuario WHERE SHA(us_codigo)='$usr' AND SHA(us_clave)='$pws' AND internet='S' ";
		
		$cursor=$this->db->query($query);
		
		if($cursor->num_rows() > 0){
			$rr = $cursor->row_array();
			$sal = each($rr);
			$sess_data = array('usuario' => $_POST['user'],'nombre'  => $sal[1],'logged_in'=> TRUE );
		} else {
			$sess_data = array('logged_in'=> FALSE);
		}
		$this->session->set_userdata($sess_data);
		redirect($this->session->userdata('estaba'));
	}

	function cese(){
		$this->session->sess_destroy();
		redirect();
	}
	function ingresar(){
		$viene=$this->session->userdata('estaba');
		$attributes  = array('name' => 'ingresar_form');
		$data['titulo1'] = form_open('bienvenido/autentificar',$attributes);
		$attributes  = array('name' => 'user','size' => '6');
		$data['titulo1'] .='<table><tr><td>Usuario: </td><td>'.form_input($attributes).'</td></tr>';
		$attributes  = array('name' => 'pws','size' => '6','type' => 'password');
		$data['titulo1'] .='<tr><td> Clave:  </td><td>'.form_input($attributes).'</td></tr>';
		$data['titulo1'] .='<tr><td></td><td>'.form_submit('usr_submit', 'Enviar').form_close().'</td></tr></table>';
		// Build the thing
		$this->layout->buildPage('bienvenido/ingresar', $data);
	}

	function ingresarVentana(){
		$viene=$this->session->userdata('estaba');
		$data['estilos'] = style("estilos.css");
		$attributes  = array('name' => 'ingresar_form');
		$data['cuerpo'] = form_open('bienvenido/autentificar',$attributes);
		$attributes  = array('name' => 'user','size' => '6');
		$data['cuerpo'] .='<table><tr><td>Usuario: </td><td>'.form_input($attributes).'</td></tr>';
		$attributes  = array('name' => 'pws','size' => '6','type' => 'password');
		$data['cuerpo'] .='<tr><td> Clave:  </td><td>'.form_input($attributes).'</td></tr>';
		$data['cuerpo'] .='<tr><td></td><td>'.form_submit('usr_submit', 'Entrar').form_close().'</td></tr></table>';
		// Build the thing
		
		$this->load->view('ingreso', $data);
	}

	function accordion($pertenece=NULL){
		if(empty($pertenece)) return;
		$out='';
		$arreglo=arr_menu(2,$pertenece);
		$arreglo=arr2panel($arreglo);

		if (count($arreglo)>0){
			$out ='<div id=\'accordion\'>';
			foreach($arreglo as $panel => $opciones ){
				$out .="<div class='myAccordion-declencheur'><h1>".($panel)."</h1></h1></div>\n";
				$out .= "<div class='myAccordion-content'><table width='100%' cellspacing='0' border='0'>\n";
				$color = "#FFFFFF";
				foreach ($opciones as $opcion) {
					$out .= "<tr bgcolor='$color'><td>";
					$out .= arr2link($opcion);
					$out .= "</td></tr>\n";
					if ( $color == "#FFFFFF" ) $color = "#F4F4F4"; else  $color = "#FFFFFF";
				}$out .="</table></div>\n";
			}$out .='</div>';
		}
		echo $out;
	}

	function error(){
		$this->layout->buildPage('bienvenido/error');
	}

	function cargapanel($pertenece=NULL) {
		if(empty($pertenece)) return;
		$out='';
		$arreglo=arr_menu(2,$pertenece);
		$arreglo=arr2panel($arreglo);
		if (count($arreglo)>0){
			$desca  = $this->datasis->dameval("SELECT mensaje FROM intramenu WHERE modulo='".$pertenece."' ");
			$imagen = $this->datasis->dameval("SELECT imagen  FROM intramenu WHERE modulo='".$pertenece."' ");
			$desca  = ($desca);
			$out .= "<div>";
			$out .= "<table ><tr><td>".image($imagen)."</td><td>";
			$out .= "<h2>".$desca."</h2></td></tr></table>";
			$out .= "</div>";
			$out .= "<div id='maso'>";
			foreach($arreglo as $panel => $opciones ){
				$out .="<div class='box col1'><h3>".($panel)."</h3>\n";
				$out .= "<table width='100%' cellspacing='1' border='0'>\n";
				$color = "#FFFFFF";
				foreach ($opciones as $opcion) {
					$out .= "<tr bgcolor='$color'><td>";
					$out .= arr2link($opcion);
					$out .= "</td></tr>\n";
					if ( $color == "#FFFFFF" ) $color = "#F4F4F4"; else  $color = "#FFFFFF";
				}$out .="</table></div>\n";
			}
			$out .= '</div>';
		}
		echo $out;
	}
}
