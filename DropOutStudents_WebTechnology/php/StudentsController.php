<?php

class StudentsController extends BaseController {

    function __construct($dbconn) {
		$this->dbconn = $dbconn;
    }

    function index($id = null) {
       	// take all categories  
    	$query_categories = $this->dbconn->prepare("SELECT categories.id, categories.title FROM categories");
		$query_categories->execute();
		$categories = $query_categories->fetchAll();

		// take all subjects 
		$query_subjects = $this->dbconn->prepare("SELECT subjects.id, subjects.title FROM subjects");
		$query_subjects->execute();
		$subjects = $query_subjects->fetchAll();

		//  take oll students
		$query_scores = $this->dbconn->prepare("SELECT students.id, students.name, students.fk_number, subjects.title, categories.title as category, scores.score
												FROM students 
												JOIN scores ON students.id = scores.student_id
												JOIN categories ON categories.id = scores.category_id
												JOIN subjects ON subjects.id = scores.subject_id");
		$query_scores->execute();
		$students = $query_scores->fetchAll();

		$this->display('StudentsIndexTemplate', array(
			'students' => $students, 
			'subjects' => $subjects,
			'categories' => $categories
		));
    }


    function create() {
    	$query_categories = $this->dbconn->prepare("SELECT categories.id, categories.title FROM categories");
		$query_categories->execute();
		$categories = $query_categories->fetchAll();

		// take all subjects 
		$query_subjects = $this->dbconn->prepare("SELECT subjects.id, subjects.title FROM subjects");
		$query_subjects->execute();
		$subjects = $query_subjects->fetchAll();

		if ($_SERVER['REQUEST_METHOD'] === 'POST') {

			$name = $_POST['name'];
			$fk_number = $_POST['fk_number'];
			$subject = $_POST['subject'];
			$category = $_POST['category'];
			$score = $_POST['score'];

			$query_get_student = $this->dbconn->prepare("SELECT students.id FROM students WHERE students.fk_number = '".$fk_number."'");
			$query_get_student->execute();
 			$student_id = $query_get_student->fetchColumn();

 			if ($student_id) {
				// create new score with all data
				$query_create_score = $this->dbconn->prepare("INSERT INTO scores (student_id, subject_id, category_id, score) VALUES (?, ?, ?, ?)");
				$query_create_score->execute(array($student_id, $subject, $category, $score));
 				
 			} else {
				// create and get student 
				$query_create_student = $this->dbconn->prepare("INSERT INTO students (name, fk_number) VALUES (?, ?)");
				$query_create_student->execute(array($name, $fk_number));

				$query_get_student = $this->dbconn->prepare("SELECT students.id FROM students WHERE students.name = '".$fk_number."'");
				$query_get_student->execute();
	 			$student_id = $query_get_student->fetchColumn();
 				
 			}

			//  take oll students
			$query_scores = $this->dbconn->prepare("SELECT students.id, students.name, students.fk_number, subjects.title, categories.title as category, scores.score
													FROM students 
													JOIN scores ON students.id = scores.student_id
													JOIN categories ON categories.id = scores.category_id
													JOIN subjects ON subjects.id = scores.subject_id");
			$query_scores->execute();
			$students = $query_scores->fetchAll();
		
			$this->display('StudentsIndexTemplate', array(
				'students' => $students, 
				'subjects' => $subjects,
				'categories' => $categories
			));
		}
    }

    // function edit() {

    // }

    // function delete() {

    // }
}

?>
