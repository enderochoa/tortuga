<?php  if (!defined('BASEPATH')) exit('No direct script access allowed');

class Layout_model extends Model {
	var $theme;
	
	function Layout_model(){
		parent::Model();
		$this->layout =& get_instance();
		$this->layout->config->load('layout');
		$this->common = $this->layout->config->item('layout_default').'/'.$this->layout->config->item('layout_commons');
		$this->theme  = $this->layout->config->item('layout_default').'/'.$this->layout->config->item('layout_content');
	}

	// --------------------------------------------------------------------------------------------------------
	//  Bellow this point editing is recommended
	// --------------------------------------------------------------------------------------------------------

	//Menu principal
	function menu(){
		$pertenece=$this->session->userdata['panel'];
		//echo '--->'.$pertenece;
		if($pertenece===FALSE)
			$pertenece=0;
		$attr=array('name'=>'_mp');
		$areglo=arr_menu();
		$amenu='';
		$menu=array();
		foreach($areglo AS $data){
			$link=site_url("/bienvenido/index/$data[modulo]");
			if($data['modulo']==$pertenece){
				$attr['class']='current';
			}else{
				$attr['class']='';
			}
			
			$attr['title']=$data['mensaje'];
			$menu[]=anchor("/bienvenido/index/$data[modulo]",$data['titulo'],$attr);
			$amenu .="<div id='sc$data[modulo]' class='tabcontent'>$data[mensaje]</div>\n";
		}
		if ($this->session->userdata('logged_in')){
			$link=site_url("/bienvenido/cese");
			$menu[] ="<a href='$link' title='Cerrar Sessi&oacute;n'>Salir</a>";
			$amenu .="<div id='salir_s' class='tabcontent'  >Cerrar Session</div>\n";
		}
		$data['menu'] = ul($menu);
		$data['amenu']= ''; //$amenu;
		return $this->layout->load->view($this->common.'menu', $data, true);
	}

	//Sub menu acordion	
	function smenu(){
		$pertenece=$this->session->userdata['panel'];
		if($pertenece===FALSE)
			$pertenece=0;
		$out='';
		$arreglo=arr_menu(2,$pertenece);
		$arreglo=arr2panel($arreglo);
		
		if (count($arreglo)>0){
			$out ='<div id=\'accordion\'>';
			foreach($arreglo as $panel => $opciones ){
				$out .="<div class='myAccordion-declencheur'><h1>".$panel."</h1></div>\n";
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
		$data['smenu']=$out;
		return $this->layout->load->view($this->common.'smenu', $data, true);
	}

	function idus(){
		if ($this->session->userdata('logged_in')){
			$retval['idus']=$this->session->userdata('nombre');
		}else{
			$attributes  = array('name' => 'user_form');
			$retval['idus'] = form_open('bienvenido/autentificar',$attributes);
			$attributes  = array('name' => 'user','size' => '6');
			$retval['idus'] .='<label>Usuario: </label>'.form_input($attributes);
			$attributes  = array('name' => 'pws','size' => '6','type' => 'password');
			$retval['idus'] .='<label> Clave:  </label>'.form_input($attributes);
			$retval['idus'] .=form_submit('usr_submit', 'Enviar').form_close();
		}
		return $this->layout->load->view($this->common . "idus", $retval, true);
	}

	function copyright(){
		$data['copyright'] = "Copyright (c) 2006-2007 Inversiones DREMANVA, C.A.<br>Telf: 58 (274) 2711922 MERIDA - VENEZUELA"; 
		return $this->layout->load->view($this->common . "copyright", $data, true);
	}
}

?>
