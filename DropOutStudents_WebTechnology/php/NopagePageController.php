<?php

class NopagePageController extends BaseController {

    function index($id = null) {
        header('HTTP/1.0 404 Not Found');
        $this->display('NopagePageController/404.tpl');
        exit();
    }

}

?>
