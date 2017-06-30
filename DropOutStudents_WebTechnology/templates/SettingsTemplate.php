<?php

class SettingsTemplate{
    function __construct() {
    }

	function display($parameters){

		// navigation
		$html = '<div class="menu">
					<a href="?page=main" class="menu-item">View all</a>
					<a href="?page=students" class="menu-item">Add</a>
					<a href="?page=import" class="menu-item">Import</a>
					<a href="?page=statistics" class="menu-item">Statistics</a>
                    <a href="?page=settings" class="menu-item active">Settings</a>
				</div>';
		
		// content - import form
		$html .= '<div class="container">
                    <div class="settings">
                        <fieldset>
						<legend>Make settings for final score:</legend>
						<div class="error-msg" data-error="'.$parameters['error'].'"><span>'.$parameters['error'].'</span></div>
						<div class="success-msg" data-success="'.$parameters['success'].'"><span>'.$parameters['success'].'</span></div>
                        <form id="chooseSubject" action="" method="POST">
                            <input type="hidden" name="act" value="chooseSubject" />
                            <div class="form-group">
                                <label>Subject</label>
                                <select name="subject" required onchange="this.form.submit()">
                                    <option value="">choose subject</option>';
                        foreach ($parameters['subjects'] as $sub) {
                            $html .= '<option value="'.$sub['id'].'">'.$sub['title'].'</option>';
                        }

                        $html .= '</select>
                            </div>
                        </form>
                        <form id="makeSetting" action="" method="POST">
                            <input type="hidden" name="subject_id" value="'.$parameters['subject_id'].'" />
                            <input type="hidden" name="act" value="chooseCategory" />';
                        foreach ($parameters['categories'] as $cat) {
                            if ($cat['title'] != 'final') {
                            $html .= '<div class="form-group inline">
                                        <label>'.$cat['title'].'</label>
                                        <input type="text" name="coef_cat['.$cat['id'].']" data-cat_title="'.$cat['title'].'" value="" placeholder="0.00 - 1.00"/> %
                                    </div>';
                            }
                        }
                        $html .= '<div class="form-group">
                                    <input type="submit" value="Create formula for final score" />
                                </div>
                        </form> 
						
						</fieldset>
                    </div>
				</div>';

		return $html;
	}
}
