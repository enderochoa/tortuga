<?php
require_once "writeexcel/class.writeexcel_workbookbig.inc.php";
require_once "writeexcel/class.writeexcel_worksheet.inc.php";

class Workbookbig extends writeexcel_workbookbig {

	function Workbookbig($filename) {
		if(is_array($filename)){
			$file=$filename['fname'];
		}else{
			$file=$filename;
		}
		parent::writeexcel_workbookbig($filename);
	}
}
?>