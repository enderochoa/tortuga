<?php require_once('dompdf_config.inc.php');

class cidompdf {
	var $paper;
	var $orien;

	function cidompdf(){
		$this->paper = 'leter';
		$this->orien = 'portrait'; //landscape
	}

	function html2pdf($html,$nombre='formato.pdf'){
		$dompdf = new DOMPDF();
		$dompdf->load_html($html);
		$dompdf->set_paper($this->paper, $this->orien);
		$dompdf->render();
		$dompdf->stream($nombre, array('Attachment' => false));
	}
}