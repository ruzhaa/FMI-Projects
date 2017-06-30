<?php

class StudentsController extends BaseController {

    function __construct($dbconn) {
		$this->dbconn = $dbconn;
    }

	private function getAllCategories() {
    	$query_categories = $this->dbconn->prepare("SELECT categories.id, categories.title FROM categories");
		$query_categories->execute();
		$categories = $query_categories->fetchAll();

		return $categories;
	}

	private function getAllSubjects() {
		$query_subjects = $this->dbconn->prepare("SELECT subjects.id, subjects.title FROM subjects");
		$query_subjects->execute();
		$subjects = $query_subjects->fetchAll();

		return $subjects;
	}

	private function getAllStudents() {
		$query_scores = $this->dbconn->prepare("SELECT students.id, students.name, students.fk_number, subjects.title, categories.title as category, scores.score 
		                                        FROM students 
		                                        JOIN scores ON students.id = scores.student_id 
		                                        JOIN categories ON categories.id = scores.category_id 
		                                        JOIN subjects ON subjects.id = scores.subject_id");
		$query_scores->execute();
		$students = $query_scores->fetchAll();

		return $students;
	}

	private function getStudentByFkNumber($fk_number) {
		$query_get_student = $this->dbconn->prepare("SELECT students.id FROM students WHERE students.fk_number = '".$fk_number."'");
		$query_get_student->execute();
		$student_id = $query_get_student->fetchColumn();

		return $student_id;
	}

	private function getStudentDataById($id) {
		$query_get_student = $this->dbconn->prepare("SELECT students.name, students.fk_number FROM students WHERE students.id = '".$id."'");
		$query_get_student->execute();
		$student = $query_get_student->fetchAll();

		return $student;
	}



    function index($id = null) {
		if ($id) { 
			$student = $this->getStudentDataById($_GET['id']);
			$name = $student[0]['name'];
			$fk_number = $student[0]['fk_number'];
			$this->edit();
			
			$this->display('StudentsIndexTemplate', array(
				'id' => $id,
				'name' => $name,
				'fk_number' => $fk_number,
				'subjects' => $this->getAllSubjects(),
				'categories' => $this->getAllCategories()
			));
		} else {
			$this->display('StudentsIndexTemplate', array(
				'subjects' => $this->getAllSubjects(),
				'categories' => $this->getAllCategories()
			));
		}
    }

    function create() {
		$categories = $this->getAllCategories();
		$subjects = $this->getAllSubjects();

		if ($_SERVER['REQUEST_METHOD'] === 'POST') {
			$name = $_POST['name'];
			$fk_number = $_POST['fk_number'];
			$subject_id = $_POST['subject'];
			$category_id = $_POST['category'];
			$score = $_POST['score'];
			$error = '';
			$success = '';

 			$student_id = $this->getStudentByFkNumber($fk_number);
			// var_dump($category_id);

			if (!$student_id) {
				// create and get student 
				$query_create_student = $this->dbconn->prepare("INSERT INTO students (name, fk_number) VALUES (?, ?)");
				$query_create_student->execute(array($name, $fk_number));

				$student_id = $this->getStudentByFkNumber($fk_number);
			}

			// create new score with all data
			$query_create_score = $this->dbconn->prepare("INSERT INTO scores (student_id, subject_id, category_id, score) VALUES (?, ?, ?, ?)");

			try { 
				$query_create_score->execute(array($student_id, $subject_id, $category_id, $score));
				$success = 'You create successfully new score to '.$name;
			} catch(PDOException $e) {
				$error = 'Dublicate entry!';
			}
		
			$this->display('StudentsIndexTemplate', array(
				'subjects' => $subjects,
				'categories' => $categories,
				'error' => $error,
				'success' => $success
			));
		}
    }

    function edit() {
		$student_id = $_GET['id'];
		$student = $this->getStudentDataById($student_id);
		$name = $student[0]['name'];
		$fk_number = $student[0]['fk_number'];
		
		if ($_SERVER['REQUEST_METHOD'] === 'POST') {
			$subject_id = $_POST['subject'];
			$category_id = $_POST['category'];
			$score = $_POST['score'];
			$error = '';
			$success = '';

			$query_update_score = $this->dbconn->prepare("UPDATE IGNORE scores SET subject_id=?, category_id=?, score=? WHERE student_id=?");

			try {
				$query_update_score->execute(array($subject_id, $category_id, $score, $student_id));
				$success = 'You update successfully score to '.$name;
			} catch(PDOException $e) {
				$error = 'Oop! Something went wrong! ;(';
			}

			$this->display('StudentsIndexTemplate', array(
				'id' => $student_id,
				'name' => $name,
				'fk_number' => $fk_number,
				'subjects' => $this->getAllSubjects(),
				'categories' => $this->getAllCategories(),
				'error' => $error,
				'success' => $success
			));
		}
    }

    function delete() {
		echo 'delete'.time();
    }
}

?>
