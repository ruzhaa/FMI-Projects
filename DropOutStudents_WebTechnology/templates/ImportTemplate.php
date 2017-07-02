<?php

class ImportTemplate{
    function __construct() {
    }

	function display($parameters){

		// navigation
		$html = '<div class="menu">
					<a href="?page=main" class="menu-item">View all</a>
					<a href="?page=students" class="menu-item">Add</a>
					<a href="?page=import" class="menu-item active">Import</a>
					<a href="?page=statistics" class="menu-item">Statistics</a>
					<a href="?page=settings" class="menu-item">Settings</a>
				</div>';
		
		// content - import form
		$html .= '<div class="container">
					<form  id="uploadFile" action="" method="POST" enctype="multipart/form-data">
						<input type="hidden" name="act" value="upload" />
						<fieldset>
						<legend>Import excel table:</legend>
						<div class="error-msg" data-error="'.$parameters['error'].'"><span>'.$parameters['error'].'</span></div>
						<div class="success-msg" data-success="'.$parameters['success'].'"><span>'.$parameters['success'].'</span></div>
						<div class="form-group">
							<label>File</label>
							<input id="file" name="file" type="file" value="" required/>
						</div>
						<div class="form-group">
							<input type="submit" value="Add" />
						</div>
						</fieldset>
					</form>
				</div>';

		return $html;
	}
}
