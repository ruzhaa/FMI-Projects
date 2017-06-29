<?php

class ImportTemplate{
    function __construct() {
    }

	function display($parameters){
		$html = '<div class="menu">
			<a href="?page=main" class="menu-item">View all</a>
			<a href="?page=students" class="menu-item">Add</a>
			<a href="?page=import" class="menu-item active">Import</a>
			<a href="?page=statistics" class="menu-item">Statistics</a>
		</div>
		<div class="container">
			<form  id="uplaodFile" action="" method="POST" enctype="multipart/form-data">
				<input type="hidden" name="act" value="upload" />
				<fieldset>
				<legend>Import excel table:</legend>
				<div class="form-group">
					<label>File</label>
					<input name="file" type="file" value="" required/>
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
