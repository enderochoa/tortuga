<?php
class Navegador extends Controller {
	function Navegador() {
		parent::Controller();
                $this->datasis->modulo_id(341,1);
	}

        
	function index($url="http://www.google.com") {
            $this->load->helper('form');
            $urlp=$this->input->post('urlf');
            if($urlp)
            $url=$urlp;
            
            $data = array(
              'name'        => 'urlf',
              'id'          => 'urlf',
              'size'        => '90%',
              'style'       => 'width:90%',
            );
            
            $salida =form_open('supervisor/navegador/index');
            $salida.=form_input($data);
            $salida.=form_submit();
            $salida.=form_close();
            echo $salida;
            echo "<iframe src='$url' width='100%' height=95%'></iframe>";
	}
}