<?php

class StudentsIndexTemplate{
    function __construct() {
    }
	function display($parameters){

		$html = '<div class="menu">
			<a href="?page=main" class="menu-item">View all</a>
			<a href="" class="menu-item active">Add</a>
			<a href="" class="menu-item">Edit</a>
			<a href="" class="menu-item">Delete</a>
		</div>
		<div class="container">
			<form  id="createForm" action="" method="POST">
				<input type="hidden" name="act" value="create" />
				<fieldset>
				<legend>Add score for student:</legend>
				<div class="form-group">
					<label>Name</label>
					<input name="name" type="text" value="" required/>
				</div>
				<div class="form-group">
					<label>FN</label>
					<input name="fk_number" type="text" value="" required/>
				</div>
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
				<div class="form-group">
					<label>Score</label>
					<input name="score" type="text" value="" required/>
				</div>
				<div class="form-group">
					<input type="submit" value="Add" />
				</div>
				</fieldset>
			</form>
			<table>
				<tbody>
				<tr>
					<td>id</td>
					<td>name</td>
					<td>fn</td>
					<td>subject</td>';

		foreach ($parameters['categories'] as $cat) {
			$html .= '<td>'.$cat['title'].'</td>';
		}

		$html .= '</tr>';
				
		foreach($parameters['students'] as $student){
			$html .= '<tr><td>'. $student['id'] . "</td><td>". 
					 $student['name'] . "</td><td>".
					 $student['fk_number'] . "</td><td>".
					 $student['title'] . "</td><td>".
					 $student['category'] . "</td><td>".
					 $student['score'] .
					 "</td></tr>";
		}

		$html .= '</table></div>';
		return $html;
	}
}
