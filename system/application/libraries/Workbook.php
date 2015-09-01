<?php
require_once "writeexcel/class.writeexcel_workbook.inc.php";
require_once "writeexcel/class.writeexcel_worksheet.inc.php";

class Workbook extends writeexcel_workbook {

	function Workbook($filename) {
	//print_r($filename);
		if(is_array($filename)){
			$file=$filename['fname'];
		}else{
			$file=$filename;
		}
		parent::writeexcel_workbook($file);
	}
}
?>