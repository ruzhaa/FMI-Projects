<?php

class IndexTemplate {
    function __construct() {
    }

	public function display($parameters){


		return "
			".$parameters['header']."
			".$parameters['body']."
			".$parameters['footer'];
	}
}

