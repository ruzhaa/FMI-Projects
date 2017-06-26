<?php
 
include('inc/config.inc.php');


$page = isset($_GET['page'])?$_GET['page']:'main';
$act = isset($_REQUEST['act'])?$_REQUEST['act']:'index';
$id = isset($_REQUEST['id'])?$_REQUEST['id']:null;

//create current page object
if (isset($page)) {
    $page_object_name = ucfirst($page) . "Controller";
	if (!file_exists('php/' . $page_object_name . '.php')) {
		$page_object_name = "NopageController";
	}
	$controller = ucfirst($page_object_name);
        $page_object = new $controller($dbconn);
        if (($act) && (method_exists($page_object, $act))) {
            $page_object->$act($id);
        } else {
            $page_object->index($id);
        }
} 

?> 
