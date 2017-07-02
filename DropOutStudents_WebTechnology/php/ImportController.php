<?php

include LIB_DIR.'classes/PHPExcel/IOFactory.php';
include LIB_DIR.'classes/PHPExcel/Cell.php';

class ImportController extends BaseController
{

    function __construct($dbconn)
    {
        $this->dbconn = $dbconn;
    }

	private function getSubjectdByTitle($title) {
		$query_get_subject = $this->dbconn->prepare("SELECT subjects.id FROM subjects WHERE subjects.title = '".$title."'");
		$query_get_subject->execute();
		$subject_id = $query_get_subject->fetchColumn();
		
		if (!$subject_id) {
			$query_create_subject = $this->dbconn->prepare("INSERT INTO subjects (title) VALUES (?)");
			$query_create_subject->execute(array($title));

			$subject_id = $this->dbconn->lastInsertId();
		}

		return $subject_id;
		
	}

	private function getCategoryIdByTitle($title) {
		$query_get_category = $this->dbconn->prepare("SELECT categories.id FROM categories WHERE categories.title = '".$title."'");
		$query_get_category->execute();
		$category_id = $query_get_category->fetchColumn();

		if (!$category_id) {
			$query_create_category = $this->dbconn->prepare("INSERT INTO categories (title) VALUES (?)");
			$query_create_category->execute(array($title));

			$category_id = $this->dbconn->lastInsertId();
		}

		return $category_id;
		
	}

	private function getStudentIdByNameAndFn($student_name, $student_fn) {
		$query_get_student_id = $this->dbconn->prepare("SELECT students.id FROM students 
														WHERE students.name = '".$student_name."' AND students.fk_number = ".$student_fn);
		$query_get_student_id->execute();
		$student_id = $query_get_student_id->fetchColumn();

		if (!$student_id) {
			$query_get_student_id = $this->dbconn->prepare("INSERT INTO students (name, fk_number) VALUES (?, ?)");
			$query_get_student_id->execute(array($student_name, $student_fn));

			$student_id = $this->dbconn->lastInsertId();
		}

		return $student_id;
	}

	private function insertScore($student_id, $subject_id, $category_id, $score) {
		$query_create_score = $this->dbconn->prepare("INSERT INTO scores (student_id, subject_id, category_id, score) 
		VALUES (?, ?, ?, ?)
		ON DUPLICATE KEY UPDATE  score=?");

		try { 
			$query_create_score->execute(array($student_id, $subject_id, $category_id, $score, $score));
			$success = 'You create successfully new score';
		} catch(PDOException $e) {
			$error = 'Dublicate entry!';
		}
	}

    function index($id = null) {
        $this->display('ImportTemplate', array());
    }

	function upload() {
		$error = '';
		$success = '';		
		
		if ($_SERVER['REQUEST_METHOD'] === 'POST') {

			$inputFileType = 'Excel5';
			$inputFileName = UPLOAD_DIR.$_FILES['file']['name'];
			move_uploaded_file($_FILES["file"]["tmp_name"], $inputFileName);
			// set permissions after successfully uload
			chmod($inputFileName, 0777);
			$objReader = PHPExcel_IOFactory::createReader($inputFileType);

			$objPHPExcel = $objReader->load($inputFileName);
			$sheet_names = ($objPHPExcel->getSheetNames());


			foreach ($sheet_names as $sheet_name) {
				$curr_sheet = $objPHPExcel->getSheetByName($sheet_name);

				$subject_id = $this->getSubjectdByTitle($sheet_name);

				$highestRow = $curr_sheet->getHighestRow();
				$highestColumn = $curr_sheet->getHighestColumn();
				$arrayColumn = range('C', $highestColumn);
				$rowTitleData = $curr_sheet->rangeToArray('C1:'.$highestColumn.'1', null, true, false);
				
				$arrayCategoryId = array();
				
				$category_id = '';
				
				for ($row = 1; $row <= $highestRow; $row++) {
					
					if ($row == 1) {
						for ($col = 0; $col <= count($arrayColumn); $col++) {
							if($rowTitleData[0][$col]){
								$category_id = $this->getCategoryIdByTitle($rowTitleData[0][$col]);
								$arrayCategoryId[] = $category_id;
							}
						}
					} else {
						
						$student_name = $curr_sheet->getCellByColumnAndRow(0, $row)->getValue();
						$student_fn = $curr_sheet->getCellByColumnAndRow(1, $row)->getValue(); 
						
						$student_id = $this->getStudentIdByNameAndFn($student_name, $student_fn);
						$i = 0;
						for ($col = 2; $col <= count($arrayColumn) + 2; $col++) {
							$score = $curr_sheet->getCellByColumnAndRow($col, $row)->getValue();

							if($student_id AND $subject_id AND $arrayCategoryId[$i] AND ($score OR $score == 0 OR $score == '')){
								$this->insertScore($student_id, $subject_id, $arrayCategoryId[$i], $score);
							}
							$i++;
						}
					}
				}
			}
			if ($_FILES['file']['error']) {
				$error = 'Failed to upload file!';
			} else {
				$success = 'The file was successfully uploaded!';
			}

			$this->display('ImportTemplate', array(
				'error' => $error,
				'success' => $success
			));
		}
	}
	
}
