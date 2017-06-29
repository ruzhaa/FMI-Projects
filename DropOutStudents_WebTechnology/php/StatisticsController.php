<?php

class StatisticsController extends BaseController
{

    function __construct($dbconn) {
        $this->dbconn = $dbconn;
    }

	private function getAllCategories() {
    	$query_categories = $this->dbconn->prepare("SELECT categories.id, categories.title FROM categories");
		$query_categories->execute();
		$categories = $query_categories->fetchAll();

		return $categories;
	}

	private function getCategoryTitleById($category_id) {
    	$query_category = $this->dbconn->prepare("SELECT categories.title FROM categories WHERE categories.id=".$category_id);
		$query_category->execute();
		$category = $query_category->fetchAll();

		return $category;
	}

	private function getAllSubjects() {
		$query_subjects = $this->dbconn->prepare("SELECT subjects.id, subjects.title FROM subjects");
		$query_subjects->execute();
		$subjects = $query_subjects->fetchAll();

		return $subjects;
	}

    private function filterStudents($subject_id, $category_id, $from_score, $to_score) {
        $query_scores = $this->dbconn->prepare(" SELECT st.id, st.name, st.fk_number, sj.title, group_concat(s.category_id,':',s.score) as scores 
												FROM scores s 
												LEFT JOIN students st ON s.student_id = st.id 
												LEFT JOIN subjects sj ON s.subject_id = sj.id 
												LEFT JOIN categories c ON s.category_id = c.id
												WHERE sj.id =".$subject_id."
                                                AND c.id =".$category_id." 
                                                AND s.score >=".$from_score."
                                                AND s.score <=".$to_score."
												GROUP BY st.id");

		$query_scores->execute();
		$students = $query_scores->fetchAll();

		return $students;
    }

    function index($id = null) {
        $categories = $this->getAllCategories();
		$subjects = $this->getAllSubjects();

        $this->display('StatisticsTemplate', array(
            'subjects' => $subjects,
			'categories' => $categories,
        ));
    }
	
    function statistic() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
			$subject_id = $_POST['subject'];
			$category_id = $_POST['category'];
			$from_score = $_POST['from_score'];
			$to_score = $_POST['to_score'];

            $categories = $this->getCategoryTitleById($category_id);
		    $students = $this->filterStudents($subject_id, $category_id, $from_score, $to_score);

            $this->display('StatisticsTemplate', array(
                'students'=>$students, 
                'categories'=>$categories,
                'subjects'=>$subjects
            ));
        }
    }
}
