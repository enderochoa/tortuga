<?php
require_once('../dompdf_config.inc.php');

  $html = '<html><head><style> </style></head><body>Pa bailar con tigo</body></html>';
  $paper='leter';
  $orien='portrait'; //landscape

  $dompdf = new DOMPDF();
  $dompdf->load_html($html);
  $dompdf->set_paper($paper, $orien);
  $dompdf->render();

  $dompdf->stream('dompdf_out.pdf', array('Attachment' => false));
?>
