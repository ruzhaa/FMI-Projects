<?php

class MainController extends BaseController {

    function __construct($dbconn) {
		$this->dbconn = $dbconn;
    }

    function index($id = null) {
		//take all categories

    	$query_categories = $this->dbconn->prepare("SELECT categories.title FROM categories");
		$query_categories->execute();
		$categories = $query_categories->fetchAll();

		// take all scores

		$query_scores = $this->dbconn->prepare("SELECT st.id, st.name, st.fk_number, sj.title, group_concat(c.title,'\:',s.score) as scores
												FROM scores s 
												LEFT JOIN students st ON s.student_id = st.id
												LEFT JOIN subjects sj ON s.subject_id = sj.id
												LEFT JOIN categories c ON s.category_id = c.id
												GROUP BY sj.title");
		$query_scores->execute();
		$students = $query_scores->fetchAll();

		$clean_data = array();

		foreach ($students as $student) {
			$clean_data[$student['fk_number']] = array(
				'subject' => $student['title'],
				);

			$scores = explode(',', $student['scores']);
			$scores_array = array();
			foreach ($scores as $key => $value) {
				$score_cat = explode(':', $value);
				$scores_array[$score_cat[0]] = $score_cat[1];
				// var_dump($scores_array);
				array_push($clean_data[$student['fk_number']], $scores_array);
			}
			// die();
			// echo $scores.'->'.$cat;
			# code...
			// var_dump($clean_data[$student['fk_number']]);
		}
		$this->display('MainTemplate', array('students'=>$students, 'categories'=>$categories));
    }
}

?>
