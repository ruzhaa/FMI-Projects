<?php
include ("PHPExcel/IOFactory.php");
class ImportController extends BaseController {

    function __construct($dbconn) {
		$this->dbconn = $dbconn;
    }

    function index($id = null) {
		if ($_SERVER['REQUEST_METHOD'] === 'POST') {
			$file = $_POST['file'];
			var $objPHPExcel = PHPExcel_IOFactory::load('example.xsl');
			echo $file;
			// foreach ($objPHPExcel->getWorksheetIterator() as $worksheet) {
			// 	$highestRow = $worksheet->getHighestRow();
			// 	for ($row=2; $row<=$highestRow; $row++) {
			// 		$name = mysqli_real_escape_string($this->dbconn, $worksheet.getCellByColumnAndRow(0, $row)->getValue());
			// 		$fn = mysqli_real_escape_string($this->dbconn, $worksheet.getCellByColumnAndRow(1, $row)->getValue());
			// 		$control = mysqli_real_escape_string($this->dbconn, $worksheet.getCellByColumnAndRow(2, $row)->getValue());
			// 		$project = mysqli_real_escape_string($this->dbconn, $worksheet.getCellByColumnAndRow(3, $row)->getValue());
			// 		$exam = mysqli_real_escape_string($this->dbconn, $worksheet.getCellByColumnAndRow(4, $row)->getValue());

			// 		$query_import_data = $this->dbconn->prepare("INSERT INTO test_import (name, fn, control, project, exam) VALUES (?, ?, ?, ?, ?)");
			// 		$query_import_data->execute(array($name, $fn, $control, $project, $exam));
			// 	}
			// }
		}

		
		$this->display('ImportTemplate', array());
    }
}

?>
