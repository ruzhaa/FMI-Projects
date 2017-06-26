<?php

class BaseController {

    function __construct() {
    }

    function display($template, $parameters = array()) {
	
		$header = HeaderTemplate::display();	
		$footer = FooterTemplate::display();	
		$current_template = $template::display($parameters);

		$result = IndexTemplate::display(array('header'=>$header,'footer'=>$footer,'body'=>$current_template));	
		
		echo $result;
    }

}

