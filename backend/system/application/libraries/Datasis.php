<?php if (!defined('BASEPATH')) exit('No direct script access allowed');
/**
 * DataSIS Components
 *
 * @author		Andres Hocevar
 * @version		0.1
 * @filesource
 **/

class Datasis {

	function dameval($mpara,$data=array()){
		$CI =& get_instance();
		$qq = $CI->db->query($mpara,$data);
		$rr = $qq->row_array();
		$aa = each($rr);
		return $aa[1];
	}

	function damerow($mSQL,$data=array()){
		$CI =& get_instance();
		$query = $CI->db->query($mSQL,$data);
		$row=array();
		if ($query->num_rows() > 0)
		$row = $query->row_array();
		return $row;
	}

	function traevalor($mvalor,$prede=null,$descrip=''){
		$CI =& get_instance();
		$mvalor = $CI->db->escape($mvalor);
		$prede  = $CI->db->escape($prede);
		$descrip= $CI->db->escape($descrip);
		$CI->db->query("INSERT IGNORE INTO valores SET nombre=$mvalor,valor=$prede,descrip=$descrip");
		$qq = $CI->db->query("SELECT valor FROM valores WHERE nombre=$mvalor");
		$rr = $qq->row_array();
		$aa = each($rr);
		return $aa[1];
	}

	function prox_sql($mcontador){
		$aa=$this->prox_numero($mcontador,'caja');
		return $aa;
	}

	function existetabla($tabla){
		$CI =& get_instance();
		return $CI->db->table_exists($tabla);
	}

	function adia(){
		$dias = array();
		for($i=1;$i<=31;$i++) {
			$ind=str_pad($i, 2, '0', STR_PAD_LEFT);
			$dias[$ind]=$ind;
		}
		return $dias;
	}

	function ames(){
		$mes = array();
		for($i=1;$i<=31;$i++){
			$ind=str_pad($i, 2, '0', STR_PAD_LEFT);
			$mes[$ind]=$ind;
		}
		return $mes;
	}

	function aano(){
		$ano  = array('2004'=>'2004','2005'=>'2005','2006'=>'2006','2007'=>'2007','2008'=>'2008','2009'=>'2009','2010'=>'2010');
		return $ano;
	}

	function agregacol($tabla,$columna,$tipo){
		$CI =& get_instance();
		$existe  = $CI->db->query("DESCRIBE $tabla $columna");
		if ( $existe->num_rows() == 0  )
		$CI->db->query("ALTER TABLE $tabla ADD COLUMN $columna $tipo");
	}
	function login(){
		$CI =& get_instance();
		return $CI->session->userdata('logged_in');
	}

	function essuper(){
		$CI =& get_instance();
		$CI->load->database('default',TRUE);
		if ($CI->session->userdata('logged_in')){
			$usuario = $CI->session->userdata['usuario'];
			// Prueba si es supervisor
			$existe = $CI->datasis->dameval("SELECT COUNT(*) FROM usuario WHERE us_codigo='$usuario' AND supervisor='S'");
			if ( $existe  > 0  )
			return  true;
		}
		return false;
	}

	function puede($id){
		$CI =& get_instance();
		$CI->load->database('default',TRUE);
		if ($CI->session->userdata('logged_in')){
			$usuario = $CI->session->userdata['usuario'];
			$existe = $CI->datasis->dameval("SELECT COUNT(*) FROM intrasida WHERE usuario='$usuario' AND id='$id'");
			if ($existe  > 0 )
			return  true;
		}
		if($CI->datasis->essuper())
			return true;
		
		return false;
	}

	function calendario($forma,$nombre){
		return "<input type=\"text\" name=\"$nombre\" /><a href=\"#\" onclick=\"return getCalendar(document.$forma.$nombre);\"/><img src='calendar.png' border='0' /></a>";
	}

	function jscalendario(){
		return "<script language=\"Javascript\" src=\"calendar.js\"></script>";
	}

	//Identifica el modulo y controla el acceso
	function modulo_id($modulo,$ventana=0){
		if ($this->essuper()) return true;
		$CI =& get_instance();
		$CI->load->database('default',TRUE);
		$CI->session->set_userdata('last_activity', time());
		if($CI->session->userdata('logged_in')){
			$usr=$CI->session->userdata('usuario');
			$mSQL   = "SELECT COUNT(*) FROM intrasida WHERE id = '$modulo' AND  usuario='$usr' AND acceso='S'";
			$cursor = $CI->db->query($mSQL);
			$rr    = $cursor->row_array();
			$sal   = each($rr);
			if ($sal[1] > 0)
			return true;
		}
		$CI->session->set_userdata('estaba', $CI->uri->uri_string());
		if($ventana)
		redirect('/bienvenido/ingresarVentana');
		else
		redirect('/bienvenido/ingresar');
	}

	//Convierte una consulta a un array
	function consularray($mSQL){
		$bote = array();
		$ncampo = array();
		$CI =& get_instance();
		$mc = $CI->db->query($mSQL);
		foreach ($mc->list_fields() as $field)
		array_push($ncampo, $field);
		if ($mc->num_rows() > 0){
			foreach( $mc->result_array() as $row )
			$bote[$row[$ncampo[0]]]=$row[$ncampo[1]];
		}
		return $bote;
	}

	function form2uri($clase,$metodo,$parametros){
		$out='';
		if (is_array($parametros)){
			foreach ($parametros as $value) {
				$out .= "+this.form.$value.value+'/'";
			}
		}else
		$out="+this.form.$parametros.value+'/'";
		$out="'".base_url()."$clase/$metodo/'$out";
		return (" location.href=$out;");
	}

	function ivaplica($mfecha=NULL){
		if(empty($mfecha)) $mfecha=date('Ymd');
		$CI =& get_instance();
		$qq = $CI->db->query("SELECT tasa, redutasa, sobretasa FROM civa ORDER BY fecha DESC LIMIT 1");
		$rr = $qq->row_array();
		//$aa = each($rr);
		return $rr;
	}

	function get_uri(){
		$CI =& get_instance();
		$arr=array('formatos','reportes');
		if(in_array($CI->router->fetch_class(),$arr))
		$uri=$CI->router->fetch_directory().$CI->router->fetch_class().'/'.$CI->router->fetch_method().'/'.$CI->uri->segment(3);
		else
		$uri=$CI->router->fetch_directory().$CI->router->fetch_class().'/'.$CI->router->fetch_method();

		return $uri;
	}


	function modbus($modbus,$id='',$width=800,$height=600,$puri=''){
		$CI =& get_instance();
		$uri=$this->get_uri();
		//$uri  =$CI->uri->uri_string();
		$tabla=$modbus['tabla'];
		$parametros=serialize($modbus);

		$data=array();
		if (empty($id)) $id=$modbus['tabla'];

		$idt=$this->dameval("SELECT id FROM modbus WHERE idm='$id' AND uri='$uri'");
		if (!empty($idt)){
			$mSQL="UPDATE modbus SET parametros = '$parametros' WHERE idm='$id' AND uri='$uri'";
			$CI->db->query($mSQL);
		}else{
			$CI->db->set('uri', $uri);
			$CI->db->set('idm', $id);
			$CI->db->set('parametros', serialize($modbus));
			$CI->db->insert('modbus');
			$idt=$CI->db->insert_id();
		}

		return(
"<a href='javascript:void(0);'
onclick=\"vent=window.open(
	'".site_url("buscar/index/$idt/$puri")."',
	'ventbuscar$id',
	'width=$width,	height=$height,	scrollbars=Yes,	status=Yes,	resizable=Yes,	screenx=5,	screeny=5'
	);
	vent.focus();
document.body.setAttribute(
	'onUnload',
	'vent.close();'
);\">".image('system-search.png',$modbus['titulo'],array('border'=>'0','class'=>'modbus')).'</a>');

	}

	function p_modbus($modbus,$puri='',$width=800,$height=600,$id=''){
		$CI =& get_instance();
		//$uri  =$CI->uri->uri_string();
		$uri=$this->get_uri();
		$tabla=$modbus['tabla'];
		$parametros=serialize($modbus);

		$data=array();
		if(empty($id))
		$id=$modbus['tabla'];
		if(!isset($modbus['title']))
		$modbus['title']=$modbus['titulo'];

		$idt=$this->dameval("SELECT id FROM modbus WHERE idm='$id' AND uri='$uri'");
		if (!empty($idt)){
			$mSQL="UPDATE modbus SET parametros = '$parametros' WHERE idm='$id' AND uri='$uri'";
			$CI->db->query($mSQL);
		}else{
			$CI->db->set('uri', $uri);
			$CI->db->set('idm', $id);
			$CI->db->set('parametros', serialize($modbus));
			$CI->db->insert('modbus');
			$idt=$CI->db->insert_id();
		}
		return(
"<a
	href='javascript:void(0);'
	onclick=\"
		vent=window.open(
			'".site_url("buscar/index/$idt/$puri")."',
			'ventbuscar$id',
			'width=$width,height=$height,scrollbars=Yes,status=Yes,resizable=Yes,screenx=5,screeny=5'
		);
		vent.focus();
		document.body.setAttribute(
			'onUnload',
			'if(typeof(vent)==\'object\') vent.close();'
		);
		
	\"
>".image('system-search.png',$modbus['title'],array('border'=>'0','class'=>'modbus')).'</a>');
		//return("<a href='javascript:void(0);' onclick=\"vent=window.open('".site_url("buscar/index/$idt/$puri")."','ventbuscar$id','width=$width,height=$height,scrollbars=Yes,status=Yes,resizable=Yes,screenx=5,screeny=5'); vent.focus();\">".image('system-search.png',$modbus['titulo'],array('border'=>'0')).'</a>');
	}

	function periodo($mTIPO, $mFECHA ) {
		$perido=array(1 =>$mFECHA);
		$mFECHA=explode('-',$mFECHA);
		
		switch ($mTIPO) {
			case 'S':
				$perido[0]=date("Y-m-d",mktime(0, 0, 0, $mFECHA[1], $mFECHA[2]-6, $mFECHA[0]));
				break;
			case 'B':
				$perido[0]=date("Y-m-d",mktime(0, 0, 0, $mFECHA[1], $mFECHA[2]-13, $mFECHA[0]));
				break;
			case 'Q':
				if ($mFECHA[2]>15)
				$perido[0]=date("Y-m-d",mktime(0, 0, 0, $mFECHA[1], 16, $mFECHA[0]));
				else
				$perido[0]=date("Y-m-d",mktime(0, 0, 0, $mFECHA[1], 1, $mFECHA[0]));
				break;
			case 'M':
				$perido[0]=date("Y-m-d",mktime(0, 0, 0, date("m"), 1, date("Y")));
				break;
			default:
				$perido[0]=$perido[1];
		}
		return $perido;
	}

	//niveles de cpla
	function nivel(){
		$formato=$this->dameval('SELECT formato FROM cemp LIMIT 1');
		$formato=explode('.',$formato);
		return count($formato);
	}

	function formato_cpla(){
		$formato=$this->dameval('SELECT formato FROM cemp LIMIT 0,1');
		$qformato='%';
		for($i=1;$i<substr_count($formato, '.')+1;$i++) $qformato.='.%';
		return $qformato;
	}

	function formato_ppla(){
		$formato=$this->traevalor('FORMATOESTRU');
		$qformato='%';
		for($i=1;$i<substr_count($formato, '.')+1;$i++) $qformato.='.%';
		return $qformato;
	}

	function prox_numero($mcontador,$usr=NULL){
		$CI =& get_instance();
		if (empty($usr))
		$usr=$CI->session->userdata('usuario');
		$query=$CI->db->query("show tables like '$mcontador'");
		if(!($query->num_rows()>0))
		$CI->db->query("CREATE TABLE $mcontador (
			`numero` INT(11) NOT NULL AUTO_INCREMENT,
			`usuario` CHAR(10) NULL DEFAULT NULL,
			`fecha` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
			PRIMARY KEY (`numero`)
			)
			");
			
		$CI->db->query("INSERT INTO $mcontador VALUES(null, '$usr', now() )");
		$aa = $CI->db->insert_id();
		return $aa;
	}

	function fprox_numero($mcontador,$long=8){
		$numero=$this->prox_numero($mcontador);
		return str_pad($numero, $long, "0", STR_PAD_LEFT);
	}

	function fprox_id($mcontador,$tipo,$long=8){
		$CI =& get_instance();
		if (empty($usr))
		$usr=$CI->session->userdata('usuario');
		$query=$CI->db->query("show tables like '$mcontador'");
		if(!($query->num_rows()>0))
		$CI->db->query("CREATE TABLE $mcontador (
			`id` INT(10) NOT NULL AUTO_INCREMENT,
			`usuario` CHAR(10) NULL DEFAULT NULL,
			`tipo` CHAR(10) NULL DEFAULT NULL,
			PRIMARY KEY (`id`)
			)
			");
			
		$CI->db->query("INSERT INTO $mcontador VALUES(null, '$usr', '$tipo' )");
		$aa = $CI->db->insert_id();
		return $aa;
	}
	
	function ip_interno($ip){                                                                                                                                           
		//10.0.0.0    - 10.255.255.255  | 10.0.0.0/8                                                                                                                
		//172.16.0.0  - 172.31.255.255  | 172.16.0.0/12                                                                                                             
		//192.168.0.0 - 192.168.255.255 | 192.168.0.0/16                                                                                                            
		return (preg_match("/^(10\\..+|192\\.168\\..+|172\\.(1[6-9]|2[0-9]|3[01])\\..+)$/", $ip)>0) ? TRUE : FALSE;                                                 
	}  

}
?>
