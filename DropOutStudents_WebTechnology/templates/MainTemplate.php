<?php

class MainTemplate{
    function __construct() {
    }
	function display($parameters){

		// navigation 
		$html = '<div class="menu">
					<a href="" class="menu-item active">View all</a>
					<a href="?page=students" class="menu-item">Add</a>
					<a href="?page=import" class="menu-item">Import</a>
					<a href="?page=statistics" class="menu-item">Statistics</a>
					<a href="?page=settings" class="menu-item">Settings</a>
				</div>';

		// content
		// create filter form
		$html .= '<div class="container">
					<form id="filerTable" method="POST">
						<input type="hidden" name="act" value="filter"/>
						<div class="message"' . (!$parameters['msg']? 'style="display: none;"': '') . '><span>'.$parameters['msg'].'</span></div>
						<div class="form-group">
							<label>Filter by </label>
							<select name="subject" required>
								<option value="">choose subject</option>';
				foreach ($parameters['subjects'] as $sub) {
					$html .= '<option value="'.$sub['id'].'">'.$sub['title'].'</option>';
				}

				$html .= '</select>
						</div>
						<div class="form-group">
							<input type="submit" value="Filter"/>
						</div>
					</form>';
		// create table with data
		$html .= '<div class="all-data">
					<table>
						<thead>
						<tr>
							<td>id</td>
							<td>name</td>
							<td>fn</td>
							<td>subject</td>';

		
		foreach ($parameters['categories'] as $cat) {
			if ($cat['title'] !== 'final' or ($cat['title'] == 'final' and $parameters['is_final']))
				$html .= '<td>'.$cat['title'].'</td>';
		}
		
		$html .= '<td>action</td></tr></thead><tbody>';
				
		if (!$parameters) {
			$html .= '<tr><td colspan="5" style="text-align: center;">No data!</td></tr>';
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
				$html .= '<td style="text-align:right;" class="'. (($score_array[1] < 3)? 'red': '') .'">'. $score_array[1] . "</td>";
			}
				
			$html .= '<td><a class="edit-button" href="?page=students&id='.$student["id"].'">Edit</a>
					  <form class="clear-form" action="" method="POST">
					  	<input type="hidden" name="act" value="clear"/>
						<input type="hidden" name="id" value="'.$student['id'].'">
						<input type="hidden" name="subject_id" value="'.$parameters['filter_subject_id'].'">						
						<input type="submit" name="remove" value="Clear" id="'.$student['id'].'"/>
					 </form>
					</td></tr>';
					 
		}

		$html .= '</table></div></div>';

		return $html;
	}
}

