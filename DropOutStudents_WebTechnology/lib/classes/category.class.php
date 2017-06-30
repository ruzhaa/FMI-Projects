<?php

class Category {
    public $id;
    public $title;

    public $dbdata_table;
    public $dbdata_fields;
    
    //constructor
    
    function __construct($dbconn, $id = null) {
    	$this->dbdata_class = get_class($this);
    	$this->dbdata_fields = array('id', 'title');
    	$this->dbdata_table = "categories";
    	$this->id = $id;

    }
}