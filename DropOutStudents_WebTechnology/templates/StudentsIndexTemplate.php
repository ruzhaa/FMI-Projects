<?php

class StudentsIndexTemplate {
    function __construct() {
    }
	function display($parameters){

		$html = '<div class="menu">
			<a href="?page=main" class="menu-item">View all</a>
			<a href="?page=students" class="menu-item '; if (!$parameters['id']) { $html .= 'active'; } $html .= '">Add</a>';
			if ($parameters['id']) { $html .= '<a href="?page=students&id='.$parameters['id'].'" class="menu-item active">Edit</a>'; }
			$html .= '<a href="?page=import" class="menu-item">Import</a>
			<a href="?page=statistics" class="menu-item">Statistics</a>
		</div>
		<div class="container">
			<form  id="createForm" action="" method="POST">';
				if ($parameters['id']) {
					$html .= '<input type="hidden" name="act" value="edit"/>
							  <input type="hidden" name="id" value="'.$parameters['id'].'">';
				} else {
					$html .= '<input type="hidden" name="act" value="create"/>';
				}

				$html .= '<fieldset>
				<legend>';
				if ($parameters['id']) { $html .= 'Edit '; } 
				else { $html .= 'Add '; }
				$html .= 'score for student:</legend>
				<div class="error-msg" data-error="'.$parameters['error'].'"><span>'.$parameters['error'].'</span></div>
				<div class="success-msg" data-success="'.$parameters['success'].'"><span>'.$parameters['success'].'</span></div>
				<div class="form-group">
					<label>Name</label>
					<input name="name" type="text" value="'.$parameters['name'].'" required/>
				</div>
				<div class="form-group">
					<label>FN</label>
					<input name="fk_number" type="text" value="'.$parameters['fk_number'].'" required/>
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
					<input type="submit" value="';
				if ($parameters['id']) { $html .= 'Edit '; } 
				else { $html .= 'Add '; }
				$html .= '" />
				</div>
				</fieldset>
			</form>
		</div>';
		
		return $html;
	}
}
