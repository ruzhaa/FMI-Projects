<?php

class StatisticsTemplate{
    function __construct() {
    }

	function display($parameters){

        // navigation
		$html = '<div class="menu">
                    <a href="?page=main" class="menu-item">View all</a>
                    <a href="?page=students" class="menu-item">Add</a>
                    <a href="?page=import" class="menu-item">Import</a>
                    <a href="" class="menu-item active">Statistics</a>
                    <a href="?page=settings" class="menu-item">Settings</a>
                </div>';

        // content
        // if get -> show form to make statistic 
        // if post -> show table with statistic
		$html .= '<div class="container">';

        if ($_SERVER['REQUEST_METHOD'] === 'GET') {
			$html .= '<form id="statisticForm" action="" method="POST">
                        <input type="hidden" name="act" value="statistic" />
                        <fieldset>
                        <legend>Make statistic:</legend>
                        <div class="form-group">
                            <label>Subject</label>
                            <select name="subject" required>
                                <option value="">choose subject</option>';

                foreach ($parameters['subjects'] as $sub) {
                    $html .= '<option value="'.$sub['id'].'">'.$sub['title'].'</option>';
                }

                $html .= '</select>
                        </div><div class="form-group">
                            <label>Category</label>
                            <select name="category" required>
                                <option value="">choose category</option>';

                foreach ($parameters['categories'] as $cat) {
                    $html .= '<option value="'.$cat['id'].'">'.$cat['title'].'</option>';
                }

                $html .= '</select>
                        </div>
                        <div class="form-group scores">
                            <label>Score</label>
                            <input name="from_score" type="text" value="" required placeholder="from 0.00"/>
                            <input name="to_score" type="text" value="" required placeholder="to 0.00"/>
                            
                        </div>
                        <div class="form-group">
                            <input type="submit" value="View" />
                        </div>
                        </fieldset>
                    </form>';
        } elseif ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // show legend
            $html .='<div class="legend">
                        <ul>
                            <li class="color red"><span class="info">2 - 2.99</span></li>
                            <li class="color orange"><span class="info">3 - 3.99</span></li>
                            <li class="color blue"><span class="info">4 - 4.99</span></li>
                            <li class="color purpul"><span class="info">5 - 5.99</span></li>
                            <li class="color green"><span class="info">6</span></li>
                        </ul>
                    </div>';
                    
            // show table
            $html .= '<div class="filter-data">
                        <table>
                            <thead>
                            <tr>
                                <td>id</td>
                                <td>name</td>
                                <td>fn</td>
                                <td>subject</td>';

                    foreach ($parameters['categories'] as $cat) {
                        $html .= '<td>'.$cat['title'].'</td>';
                    }

                    $html .= '<td></td></tr></thead><tbody>';
                    if (!$parameters['students']) {
                        $html .= '<tr><td colspan="6" style="text-align: center;">No data!</td></tr>';
                    }
                    foreach($parameters['students'] as $student){
                        $html .= '<tr><td>'. $student['id'] . "</td><td>". 
                                $student['name'] . "</td><td>".
                                $student['fk_number'] . "</td><td>".
                                $student['title'] . "</td>";
                                
                        
                        $scores = explode(',', $student['scores']);
                        sort($scores);

                        foreach($scores as $score){
                            $score_array = explode(":", $score);
                            $html .= '<td style="text-align:right;" class="score" data-score_color="'.$score_array[1].'">'. $score_array[1] . '</td><td class="img" data-score_color="'.$score_array[1].'"><img src=""/></td>';
                        }
                            
                        $html .= '</tr>';        
                    }

                $html .= '</table>
                    </div>';
        }
        $html .= '</div>';

		return $html;
	}
}