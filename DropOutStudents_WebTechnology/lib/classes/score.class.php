<?php

class Score {
    public $id;
    public $student_id;
    public $subject_id;
    public $category_id;
    public $score;

    public $dbdata_table;
    public $dbdata_fields;
    
    //constructor
    
    function __construct($dbconn, $id = null) {
    	$this->dbdata_class = get_class($this);
    	$this->dbdata_fields = array('id', 'student_id', 'subject_id', 'category_id', 'score');
    	$this->dbdata_table = "scores";
    	$this->id = $id;

    }
}