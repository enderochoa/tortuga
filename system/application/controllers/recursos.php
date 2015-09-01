<?php
class recursos extends Controller {

	function recursos(){
		parent::Controller();	
		$this->load->library('rapyd');
	}
	
	function index(){
		echo '';
	}
	
	function scripts($scripts=null){
		switch ($scripts) {
    	case 'nformat.js':
				$data['centimos'] = (is_null(constant("RAPYD_DECIMALS"))) ? ',' : RAPYD_DECIMALS;
				$data['miles']    = (is_null(constant("RAPYD_THOUSANDS")))? '.' : RAPYD_THOUSANDS;
				$data['num']      = (is_null(constant("RAPYD_NUM")))      ?  2  : RAPYD_NUM;
			
				$this->load->view('scripts/view_nformat_js',$data);
			break;
			default:
				echo '';
		}
	}
}
?>