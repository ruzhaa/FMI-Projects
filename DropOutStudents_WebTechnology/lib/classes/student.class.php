<?php

class Student {

    public $id;
    public $name;
    public $fk_number;
    public $creation_date;

    public $dbdata_table;
    public $dbdata_fields;
    
    //constructor
    
    function __construct($dbconn, $id = null) {
    	$this->dbdata_class = get_class($this);
    	$this->dbdata_fields = array('id', 'name', 'fk_number', 'creation_date');
    	$this->dbdata_table = "students";
    	$this->id = $id;
    }
}
