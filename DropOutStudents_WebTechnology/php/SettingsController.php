<?php

class SettingsController extends BaseController
{
    function __construct($dbconn) {
        $this->dbconn = $dbconn;
    }

    private function getAllSubjects() {
		$query_get_subjects = $this->dbconn->prepare("SELECT subjects.id, subjects.title FROM subjects");
		$query_get_subjects->execute();
		$subjects = $query_get_subjects->fetchAll();

		return $subjects;
	}

    private function getCategoriesBySubject($subject_id) {
        $query_get_categories = $this->dbconn->prepare("SELECT DISTINCT categories.id, categories.title FROM categories LEFT JOIN scores ON subject_id=".$subject_id);
		$query_get_categories->execute();
		$categories = $query_get_categories->fetchAll();

		return $categories;
    }

    private function createFinalCategoryAndGetId() {
        $query_get_category = $this->dbconn->prepare("SELECT categories.id FROM categories WHERE categories.title = 'final'");
		$query_get_category->execute();
		$category_id = $query_get_category->fetchColumn();

		if (!$category_id) {
			$query_create_category = $this->dbconn->prepare("INSERT INTO categories (title) VALUES ('final')");
			$query_create_category->execute();

			$category_id = $this->dbconn->lastInsertId();
		}

		return $category_id;
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

    private function insertFinalScore($student_id, $subject_id, $category_id, $score) {
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
        $subjects = $this->getAllSubjects();

        $this->display('SettingsTemplate', array(
            'subjects' => $subjects
        ));
    }

    function chooseSubject() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $subject_id = $_POST['subject'];
            $subjects = $this->getAllSubjects();
            $categories = $this->getCategoriesBySubject($subject_id);

            $this->display('SettingsTemplate', array(
                'subjects' => $subjects,
                'categories' => $categories,
                'subject_id' => $subject_id

            ));
        }
    }

    function chooseCategory($id = null) {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $final_category_id = $this->createFinalCategoryAndGetId();
            $subject_id = $_POST['subject_id'];
            $categories_coef = $_POST['coef_cat'];
            
            $students = $this->getStudentsAllScores($subject_id);
            
            foreach ($students as $student) {
                $final_score = 0.0;
                $scores = explode(',', $student['scores']);
                foreach($scores as $score){
				    $score_array = explode(":", $score);
                    $category = $score_array[0];
                    $score = $score_array[1];
                    $coef = $categories_coef[$category];
                    $final_score += $coef*$score;                
                }
                $this->insertFinalScore($student['id'], $subject_id, $final_category_id, $final_score);                  
            }

            $subjects = $this->getAllSubjects();

            $this->display('SettingsTemplate', array(
                'subjects' => $subjects,
                'subject_id' => $subject_id

            ));

        }
    }
}