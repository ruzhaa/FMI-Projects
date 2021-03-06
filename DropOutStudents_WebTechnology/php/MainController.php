<?php

class MainController extends BaseController {

    function __construct($dbconn) {
		$this->dbconn = $dbconn;
    }

	private function getAllCategories() {
    	$query_categories = $this->dbconn->prepare("SELECT categories.title FROM categories order by id");
		$query_categories->execute();
		$categories = $query_categories->fetchAll();

		return $categories;
	}

	private function getIdFinalCategory() {
        $query_get_category = $this->dbconn->prepare("SELECT categories.id FROM categories WHERE categories.title = 'final'");
		$query_get_category->execute();
		$final_category_id = $query_get_category->fetchColumn();

		return $final_category_id;
    }

	private function checkFinalScores($subject_id, $final_category_id) {
		$query = $this->dbconn->prepare("SELECT scores.score 
										FROM scores 
										LEFT JOIN subjects ON subjects.id = ". $subject_id ." LEFT JOIN categories ON categories.id='". $final_category_id."' WHERE scores.category_id='".$final_category_id."' AND scores.subject_id=".$subject_id);
		$query->execute();
		$is_final = $query->fetchAll();

		return $is_final;
	}

	private function getFirstSubject() {
		$query_get_subject = $this->dbconn->prepare("SELECT subjects.id, subjects.title FROM subjects");
		$query_get_subject->execute();
		$subject = $query_get_subject->fetchColumn();

		return $subject;
	}

	private function getSubjectById($id) {
		$query_get_subject = $this->dbconn->prepare("SELECT subjects.title FROM subjects WHERE subjects.id=".$id);
		$query_get_subject->execute();
		$subject = $query_get_subject->fetchAll();

		return $subject;
	}

	private function getAllSubjects() {
		$query_get_subjects = $this->dbconn->prepare("SELECT subjects.id, subjects.title FROM subjects");
		$query_get_subjects->execute();
		$subjects = $query_get_subjects->fetchAll();

		return $subjects;
	}

	private function getStudentsAllScores($id) {
		$query_scores = $this->dbconn->prepare("SELECT st.id, st.name, st.fk_number, sj.title, group_concat(s.category_id,':',s.score) as scores 
												FROM scores s 
												LEFT JOIN students st ON s.student_id = st.id 
												LEFT JOIN subjects sj ON s.subject_id = sj.id 
												LEFT JOIN categories c ON s.category_id = c.id
												WHERE sj.id =".$id."
												GROUP BY st.id");

		$query_scores->execute();
		$students = $query_scores->fetchAll();

		return $students;
	}
	
    function index($id = null) {
		$categories = $this->getAllCategories();
		$subjects = $this->getAllSubjects();
		$final_category_id = $this->getIdFinalCategory();

		if($categories AND $subjects) {
			$is_final = FALSE;
			$first_subject = $this->getFirstSubject();
			$students = $this->getStudentsAllScores($first_subject);

			$is_final_cat = $this->checkFinalScores($first_subject, $final_category_id);
			if ($is_final_cat) {
				$is_final = TRUE;
			}

			$msg = "Students are filter by ".$this->getSubjectById($first_subject)[0]['title'];

			$this->display('MainTemplate', array(
				'msg'=>$msg,
				'students'=>$students, 
				'categories'=>$categories,
				'subjects'=>$subjects,
				'filter_subject_id'=>$first_subject,
				'is_final'=>$is_final
			));

		} else {
			$this->display('MainTemplate');
		}

    }

	function filter() {
		if ($_SERVER['REQUEST_METHOD'] === 'POST') {
			$subject_id = $_POST['subject'];
			$categories = $this->getAllCategories();
			$subjects = $this->getAllSubjects();
			$students = $this->getStudentsAllScores($subject_id);
			$final_category_id = $this->getIdFinalCategory();
			$is_final = FALSE;

			$is_final_cat = $this->checkFinalScores($subject_id, $final_category_id);
			if ($is_final_cat) {
				$is_final = TRUE;
			}

			$msg = "Students are filter by ".$this->getSubjectById($subject_id)[0]['title'];

			$this->display('MainTemplate', array(
				'msg'=>$msg,
				'students'=>$students, 
				'categories'=>$categories,
				'subjects'=>$subjects,
				'filter_subject_id'=>$subject_id,
				'is_final'=>$is_final
			));
		}
	}
	function clear(){
		if ($_SERVER['REQUEST_METHOD'] === 'POST') {
			$subject_id = $_POST['subject_id'];

			$remove_id_student = $_REQUEST['id'];
			$query_clear_data = $this->dbconn->prepare("UPDATE IGNORE scores SET score='0' WHERE student_id=?");
			$query_clear_data->execute(array($remove_id_student));

			$categories = $this->getAllCategories();
			$subjects = $this->getAllSubjects();
			$students = $this->getStudentsAllScores($subject_id);

			$msg = "Students are filter by ".$this->getSubjectById($subject_id)[0]['title'];

			$this->display('MainTemplate', array(
				'msg'=>$msg,
				'students'=>$students, 
				'categories'=>$categories,
				'subjects'=>$subjects,
				'filter_subject_id'=>$subject_id
			));
		}
	}
}

?>
