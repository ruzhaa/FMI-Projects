<?php

abstract class DBData {

    public $id;
    public $comments;
    public $comments_count;

    /**
     * 
     * Връзка с базата
     * @var ADODB_mysql
     */
    protected $dbconn;
    protected $dbdata_class;
    protected $dbdata_table;
    protected $dbdata_fields;
    protected $err;

    function __construct($dbconn) {
        $this->dbdata_class = 'DBData';
        $this->dbconn = $dbconn;
        $this->err = null;
    }

    protected function seterr($s) {
        $this->err = $s;
        $q = "insert into errors (error_msg) values(?)";
        $st = $this->dbconn->Prepare($q);
        $this->dbconn->Execute($st, array($s));
    }

    public function err() {
        return ($this->err);
    }

//======FUNCTION FETCH
    public function fetch($id = null) {

        if ($id == null)
            $id = $this->id;
        if ($id == null) {
            $this->seterr("No $dbdata_class ID specified");
            return (false);
        }

        $q = "SELECT " . implode(",", $this->dbdata_fields) . " FROM " . $this->dbdata_table . " WHERE id = ? ";

        $st = $this->dbconn->Prepare($q);
        if (!$st) {
            $_SESSION['hidden_error'] = "Could not prepare the $this->dbdata_class fetch statement! " . $this->dbconn->ErrorMsg();
            return (false);
        }
        $r = $this->dbconn->GetRow($st, array($id));
        if (!$r) {
            $_SESSION['hidden_error'] = "Could not fetch the $this->dbdata_class record! " . $this->dbconn->ErrorMsg();
            return (false);
        }
        foreach ($this->dbdata_fields as $value) {
            if (!in_array($value, array('joke', 'description', 'comment', 'created', 'published', 'news', 'html', 'youtube', 'message', 'client_address', 'conditions', 'value_html', 'value', 'activate_code', 'box_text'))) {
                $this->$value = htmlspecialchars($r[$value]);
                 
            } elseif (in_array($value, array('prepaid_start', 'prepaid_stop', 'created', 'published',))) {
                $this->$value = date('d.m.Y', strtotime($r[$value]));
                $time_val = $value . "_time";
                $this->$time_val = date('H:i', strtotime($r[$value]));
            }
            else
                $this->$value = $r[$value];
            
        }

        //Взимаме снимката за всеки обект от таблицата files
        $this->getMainPicture();

        return true;
    }

    /**
     * Зарежда обектът с данните за първия намерен запис при подаден филтър
     * 
     * Филтърът е масив с ключове 'filter' съдържа текстовия филтър
     * и ключ 'values', което е масив със стойностите нужни за филтъра
     * 
     * Ако има намерени и заредени данни се връща true, в противен случай false
     * 
     * @param array $filter
     * @return boolean
     */
    function fetchByFilter($filter = false) {

        if (!$filter) {
            $filter = array('filter' => '', 'values' => array());
        }

        $q = "SELECT " . implode(",", $this->dbdata_fields) . " FROM " . $this->dbdata_table . ' ' . $filter['filter'];

        $st = $this->dbconn->Prepare($q);
        if (!$st) {
            $_SESSION['hidden_error'] = "Could not prepare the $this->dbdata_class fetch statement! " . $this->dbconn->ErrorMsg();
            return (false);
        }
        $r = $this->dbconn->GetRow($st, $filter['values']);
        if (!$r) {
            $_SESSION['hidden_error'] = "Could not fetch the $this->dbdata_class record! " . $this->dbconn->ErrorMsg();
            return (false);
        }

        foreach ($this->dbdata_fields as $value) {
            if (!in_array($value, array('creation_date', 'description', 'created', 'published', 'news', 'html', 'address', 'youtube', 'message', 'client_address', 'conditions', 'value_html', 'value')))
                $this->$value = htmlspecialchars($r[$value]);
            elseif (in_array($value, array('creation_date', 'prepaid_start', 'prepaid_stop', 'created', 'published'))) {
                $this->$value = date('d.m.Y', strtotime($r[$value]));
                $time_val = $value . "_time";
                $this->$time_val = date('H:i', strtotime($r[$value]));
            }
            else
                $this->$value = $r[$value];
        }

        return true;
    }

    /**
     * Изтрива обектите при подаден филтър
     * 
     * Филтърът е масив с ключове 'filter' съдържа текстовия филтър
     * и ключ 'values', което е масив със стойностите нужни за филтъра
     * 
     * Ако заявката се изпълни коректно връща true, в противен случай false
     * 
     * @param array $filter
     * @return boolean 
     */
    function delByFilter($filter = false) {

        if (!$filter) {
            $filter = array('filter' => '', 'values' => array());
        }

        $q = "DELETE FROM " . $this->dbdata_table . ' ' . $filter['filter'];

        $st = $this->dbconn->Prepare($q);
        if (!$st) {
            $_SESSION['hidden_error'] = "Could not prepare the $this->dbdata_class fetch statement! " . $this->dbconn->ErrorMsg();
            return (false);
        }
        $r = $this->dbconn->Execute($st, $filter['values']);
        if (!$r) {
            $_SESSION['hidden_error'] = "Could not execute the delete query! " . $this->dbconn->ErrorMsg();
            return (false);
        }



        return true;
    }

    /**
     * Връща масив от елементи от текущия клас
     * Филтърът е масив с ключове 'filter' съдържа текстовия филтър
     * и ключ 'values', което е масив със стойностите нужни за филтъра
     * 
     * 
     * @param array $filter
     * @param string $primary_key - оказва кое поле трябва да се върне и да се използва при последващо филтриране
     * @return boolean|array
     */
    function fetchAll($filter = false, $primary_key = 'id') {
        if (!$filter) {
            $filter = array('filter' => '', 'values' => array());
        }

        $obj_array = array();

        $query = 'select ' . $primary_key . ' from ' . $this->dbdata_table . ' ' . $filter['filter'];
        $st = $this->dbconn->Prepare($query);
        if (!$st) {
            $_SESSION['hidden_error'] = "Could not prepare the $this->dbdata_class fetch statement! " . $this->dbconn->ErrorMsg();
            return (false);
        }
        $r = $this->dbconn->GetAll($st, $filter['values']);
        if (!$r) {
            $_SESSION['hidden_error'] = "Could not fetch the $this->dbdata_class record! " . $this->dbconn->ErrorMsg();
            return (false);
        }

        if ($r)
            foreach ($r as $id) {
                $obj_row = new $this->dbdata_class($this->dbconn, $id[0]);
                $obj_row->fetch();
                $obj_row->fetch_params();
                $obj_array[] = $obj_row;
            }

        return $obj_array;
    }

    /**
     * Връща масив от елементи от текущия клас
     * Филтърът е масив с ключове 'filter' съдържа текстовия филтър
     * и ключ 'values', което е масив със стойностите нужни за филтъра
     * 
     * Връща една страница от резултатите
     * 
     * @param array $filter
     * @param string $primary_key - оказва кое поле трябва да се върне и да се използва при последващо филтриране
     * @return boolean|array
     */
    function fetchAllByPages($filter = false, $primary_key = 'id', $page_num = 1, $page_count = 10) {
        if ($page_num <= 0)
            $page_num = 1;
        if (!$filter) {
            $filter = array('filter' => '', 'values' => array());
        }

        $obj_array = array();

        $query = 'select ' . $primary_key . ' from ' . $this->dbdata_table . ' ' . $filter['filter'] . 'limit ' . (($page_num - 1) * $page_count) . ' , ' . $page_count;
        $st = $this->dbconn->Prepare($query);
        if (!$st) {
            $_SESSION['hidden_error'] = "Could not prepare the $this->dbdata_class fetch statement! " . $this->dbconn->ErrorMsg();
            return (false);
        }
        $r = $this->dbconn->GetAll($st, $filter['values']);
        if (!$r) {
            $_SESSION['hidden_error'] = "Could not fetch the $this->dbdata_class record! " . $this->dbconn->ErrorMsg();
            return (false);
        }

        foreach ($r as $id) {
            $obj_row = new $this->dbdata_class($this->dbconn, $id[0]);
            $obj_row->fetch();
            $obj_row->fetch_params();
            $obj_array[] = $obj_row;
        }

        return $obj_array;
    }

    /**
     * Връща броя на  от резултатите според филтъра
     * Функцията се ползва в комбинация с fetchAllByPages
     * 
     * @param array $filter
     * @param string $primary_key - оказва кое поле трябва да се върне и да се използва при последващо филтриране
     * @return boolean|integer
     */
    function getCountAllResults($filter = false, $primary_key = 'id') {
        if (!$filter) {
            $filter = array('filter' => '', 'values' => array());
        }

        $obj_array = array();

        $query = 'select ' . $primary_key . ' from ' . $this->dbdata_table . ' ' . $filter['filter'];
        $st = $this->dbconn->Prepare($query);
        if (!$st) {
            $_SESSION['hidden_error'] = "Could not prepare the $this->dbdata_class fetch statement! " . $this->dbconn->ErrorMsg();
            return (false);
        }
        $r = $this->dbconn->GetAll($st, $filter['values']);
        if (!$r) {
            $_SESSION['hidden_error'] = "Could not fetch the $this->dbdata_class record! " . $this->dbconn->ErrorMsg();
            return (false);
        }

        return count($r);
    }

    /**
     * Проверява дали всички задължители полета имат стойност в подадения масив.
     * Целта е директно да се подават $_POST, $_GET, $_REQUEST и автоматично да се връща проверка.
     * Ако е нужно преди това тези масиви могат да се манипулират
     * 
     * Ако има липсващи стойности се връщат имената на колоните, за който няма стойности в масив,
     * ако всичко е наред се връща false
     * 
     * @param array $data
     * @param array $required_fields - ако не е подадено автоматично се попълва със стойността на $this->dbdata_required
     * @return multitype:array|boolean
     */
    public function checkRequiredData($data, $required_fields = false) {
        if (!$required_fields) {
            if (isset($this->dbdata_required)) {
                $required_fields = $this->dbdata_required;
            } else {
                $required_fields = array();
            }
        }

        $fields = array();
        $errors = array();
        foreach ($required_fields as $required) {
            if (empty($data[$required])) {
                $fields[] = $required;
                $errors[] = 'Полето е задължително!';
            }
        }
        $return = array();

        if (!empty($fields)) {
            $return = array($fields, $errors);
        }
        return $return;
    }

    /**
     * Генерира масив с ключове 
     * 'columns' - имена на колони от таблицата
     * 'valuse' - стойности за колините от таблицата
     * 
     * Целта е като входни параметри да се подават $_POST, $_GET, $_REQUEST,
     * а върнатото директно да се подава към $this->insert и $this->update
     * 
     * @param array $data
     * @param array $columns - ако не е подадено се използва $this->dbdata_fields
     * @param boolean $add_empty - оказва дали колоните за които няма стойности да се връщата
     * @return array
     */
    public function generateColumnsValues($data, $columns = array(), $add_empty = false) {

        if (empty($columns)) {
            $columns = $this->dbdata_fields;
        }

        $return = array();


        foreach ($columns as $column) {


            //Когато за линковете дадена стойност е 0 , това значи че може да пишем ръчно URL
            if ($_REQUEST[$column] == '0') {
                //Ако няма рекуест от $column_value процедираме както си е нормално
                if ($_REQUEST[$column . '_value'])
                    $data[$column] = $_REQUEST[$column . '_value'];
                else
                    $data[$column] = $_REQUEST[$column];
            }
            else
                $data[$column] = $_REQUEST[$column];


            //ако имаме параметър за език
            if ($column == 'lang_id') {
                $data[$column] = $_SESSION['lang_id'];
            }
            
             if($column == 'update_date') {
                $data[$column] = date('Y-m-d H:i:s');
            }


            if (!isset($data[$column])) {
                if ($add_empty) {
                    $return['columns'][] = $column;
                    $return['values'][] = '';
                }
            } else {

                $return['columns'][] = $column;
                $return['values'][] = $data[$column];
            }
        }

        return $return;
    }

    /**
     * Генерира филтър от подадени данни
     * 
     * Целта е като входни параметри да се подават $_POST, $_GET, $_REQUEST,
     * а върнатото директно да се подава към $this->fetchAll или $this->fetchByFilter
     * 
     * @param array $data
     * @param array $columns - ако не е подадено се използва $this->dbdata_fields
     * @param boolean $add_empty - оказва дали колоните за които няма стойности да се включат във филтъра
     * @return Ambigous <multitype:string multitype: , unknown>
     */
    public function generateFilter($data, $columns = array(), $add_empty = false) {
        if (empty($columns)) {
            $columns = $this->dbdata_fields;
        }

        $return = array('filter' => 'where 1 = 1', 'values' => array());

        foreach ($columns as $column) {
            if (empty($data[$column])) {
                if ($add_empty) {
                    $return['filter'] .= ' and ' . $column . " = ''";
                }
            } else {
                $return['filter'] .= ' and ' . $column . " = ?";
                $return['values'][] = $data[$column];
            }
        }

        return $return;
    }

    /**
     * Изпълнява подадения селект, като връща резултата от изпълнението
     * 
     * Филтърът е масив с ключове 'filter' съдържа текстовия филтър
     * и ключ 'values', което е масив със стойностите нужни за филтъра
     * 
     * Задължително трябва да се подават заявки само за select
     * 
     * При успешно изпълнение се връяа array, а при грешка се
     * връща false
     * 
     * @param string $query - задължително трябва да е select ...
     * @param array $filter
     * @return mixed: false|array
     */
    function select($query, $filter = false) {

        if (!$filter) {
            $filter = array('filter' => '', 'values' => array());
        }

        $query .= ' ' . $filter['filter'];


        $st = $this->dbconn->Prepare($query);

        if (!$st) {
            $_SESSION['hidden_error'] = "Could not prepare the $this->dbdata_class fetch statement! " . $this->dbconn->ErrorMsg();
            return (false);
        }

        $r = $this->dbconn->GetAll($st, $filter['values']);
        if (!$r) {
            $_SESSION['hidden_error'] = "Could not fetch the $this->dbdata_class record! " . $this->dbconn->ErrorMsg();
            return (false);
        }

        return empty($r) ? false : $r;
    }

//======FUNCTION INSERT	
    public function insert($cols, $vars) {

        $q = "INSERT INTO " . $this->dbdata_table . "(" . implode(",", $cols) . ") VALUES (" . str_repeat("?,", count($vars) - 1) . "?)";
        $st = $this->dbconn->Prepare($q);
        $r = $this->dbconn->Execute($st, $vars);
        if (!$r) {
            $_SESSION['hidden_error'] = "Could not fetch the $dbdata_class record! " . $this->dbconn->ErrorMsg();
            return (false);
        }

        //get last inserted id
        $q = "SELECT id from " . $this->dbdata_table . " order by id desc limit 1";
        $r = $this->dbconn->GetAll($q);
        $this->id = $r[0][0];
        $id = $r[0][0];

        return $id;
    }

//======FUNCTION UPDATE	
    public function update($cols, $vars) {
        $q = "UPDATE " . $this->dbdata_table . " SET " . implode("=?,", $cols) . "=? WHERE id=?";
        $st = $this->dbconn->Prepare($q);
        $vars[] = $this->id;
        $r = $this->dbconn->Execute($st, $vars);
        if (!$r) {
            $_SESSION['hidden_error'] = "Could not fetch the $dbdata_class record! " . $this->dbconn->ErrorMsg();
            return (false);
        }

        return (true);
    }

//======FUNCTION DELETE
    public function del() {
        $q = "DELETE FROM  " . $this->dbdata_table . " WHERE id=? ";
        $st = $this->dbconn->Prepare($q);
        $r = $this->dbconn->Execute($st, array($this->id));
        if (!$r) {
            $_SESSION['hidden_error'] = "Could not fetch the $dbdata_class record! " . $this->dbconn->ErrorMsg();
            return (false);
        }

        return (true);
    }

    function fetch_params() {

        return true;
    }

    /**
     * генерира тъмб от дадена картинка по зададени параметри
     *
     * @return true|false
     */
    function generateThumb($source_pic, $thumb_name, $thumb_width, $thumb_height) {
        $source_pic_path = "../images/" . $this->dbdata_class . "/" . $source_pic;
        $old_thumb_pic_path = "../images/" . $this->dbdata_class . "/" . $thumb_name . "_" . $source_pic;

        if (!file_exists($source_pic_path))
            return false;

        unlink($old_thumb_pic_path);

        $image = new SimpleImage();
        $image->load($source_pic_path);
        if ($thumb_width > 0 and $thumb_height > 0) {
            $image->resize($thumb_width, $thumb_height);
        } elseif ($thumb_width > 0) {
            $image->resizeToWidth($thumb_width);
        } elseif ($thumb_height > 0) {
            $image->resizeToHeight($thumb_height);
        }
        else
            return false;

        $thumb_file = $thumb_name . "_" . $source_pic;
        $thumb_pic_path = "../images/" . $this->dbdata_class . "/" . $thumb_file;
        $image->save($thumb_pic_path);
        chmod($thumb_pic_path, 0755);

        $cols = array($thumb_name);
        $vars = array($thumb_file);
        $this->update($cols, $vars);

        return true;
    }

    /**
     * генерира тъмб от дадена картинка по зададени параметри
     *
     * @return true|false
     */
    function resize($source_pic, $thumb_width, $thumb_height) {
        $source_pic_path = "../images/" . $this->dbdata_class . "/" . $source_pic;

        if (!file_exists($source_pic_path))
            return false;



        $image = new SimpleImage();
        $image->load($source_pic_path);
        if ($thumb_width > 0 and $thumb_height > 0) {
            $image->resize($thumb_width, $thumb_height);
        } elseif ($thumb_width > 0) {
            $image->resizeToWidth($thumb_width);
        } elseif ($thumb_height > 0) {
            $image->resizeToHeight($thumb_height);
        }
        else
            return false;


        $image->save($source_pic_path);
        chmod($source_pic_path, 0755);


        return true;
    }

    public function getRaiting() {
        $model_raiting = new Raiting($this->dbconn);
        $model_raiting->fetchByFilter(array('filter' => ' WHERE tbl_name=? and row_id=? ', 'values' => array($this->dbdata_table, $this->id)));


        if (!$model_raiting->id) {
            //създава ред в таблицата с рейтинга
            $cols = array('tbl_name', 'row_id', 'positive', 'negative');
            $vars = array($this->dbdata_table, $this->id, 0, 0);
            $id = $model_raiting->insert($cols, $vars);
            $model_raiting = new Raiting($this->dbconn, $id);
            $model_raiting->fetch();
        };
        $this->positive_rate = $model_raiting->positive;
        $this->negative_rate = $model_raiting->negative;
        return $model_raiting;
    }

    public function setRaiting($raiting_type) {

        $raiting = $this->getRaiting();
        if ($raiting_type == 'negative')
            return $raiting->rateNegative();
        elseif ($raiting_type == 'positive')
            return $raiting->ratePositive();

        return false;
    }

    public function getComments() {
        $model_comment = new Comment($this->dbconn);
        $comments = $model_comment->fetchAll(array('filter' => ' WHERE tbl_name=? and row_id=? and (parent_id is null or parent_id=?) ', 'values' => array($this->dbdata_table, $this->id, 0)));
        $comments_count = 0;
        foreach ($comments as $comment) {
            if ($comment->id > 0) {
                $user = new User($this->dbconn, $comment->user_id);
                $user->fetch();
                $user->loadAllParametersValues();

                $comment->user = $user;

                $comment->getSubcomments();

                $comments_count++;
            }
        }


        $this->comments = $comments;
        $this->comments_count = $comments_count;

        return $comments;
    }

    /**
     * Упдейтва номера на показване да е равен на ИД-то,
     * само ако ИД-то е равно на 0
     *
     * 
     */
    function UpdateMove() {
        $q = "update " . $this->dbdata_table . " set show_num=id where show_num=? and lang_id=?";
        $st = $this->dbconn->Prepare($q);
        $this->dbconn->Execute($st, array(0, $_SESSION['lang_id']));
        if ($this->show_num == 0)
            $this->show_num = $this->id;
    }

    /**
     * Намира най-близкия горен елемент по номер на показване 
     * и го разменя с текущия
     *
     * @return true|false
     */
    function moveUp() {
        $this->UpdateMove();

        if ($this->parent_id != NULL) {
            //Ако има категоризация            
            $q = "select id, show_num from " . $this->dbdata_table . " where  show_num<? and lang_id=? and parent_id=? order by show_num desc limit 1";
            $st = $this->dbconn->Prepare($q);
            $r = $this->dbconn->getAll($st, array($this->show_num, $_SESSION['lang_id'], $this->parent_id));
        } else {
            //Няма категории
            $q = "select id, show_num from " . $this->dbdata_table . " where  show_num<? and lang_id=? order by show_num desc limit 1";
            $st = $this->dbconn->Prepare($q);
            $r = $this->dbconn->getAll($st, array($this->show_num, $_SESSION['lang_id']));
        }


        if (!$r)
            return false;

        $other_id = $r[0][0];
        $other_show_num = $r[0][1];
        $current_id = $this->id;
        $current_show_num = $this->show_num;


        $q = "update " . $this->dbdata_table . " set show_num=? where id=?";
        $st = $this->dbconn->Prepare($q);

        //update current stage
        $this->dbconn->Execute($st, array($current_show_num, $other_id));

        //update other stage
        $this->dbconn->Execute($st, array($other_show_num, $current_id));

        return true;
    }

    /**
     * Намира най-близкия долен елемент по номер на показване 
     * и го разменя с текущия
     *
     * @return true|false
     */
    public function moveDown() {
        $this->UpdateMove();

        if ($this->parent_id != NULL) {
            //Ако има категоризация            
            $q = "select id, show_num from " . $this->dbdata_table . " where  show_num>? and lang_id=? and parent_id=? order by show_num  limit 1";
            $st = $this->dbconn->Prepare($q);
            $r = $this->dbconn->getAll($st, array($this->show_num, $_SESSION['lang_id'], $this->parent_id));
        } else {
            //Няма категории
            $q = "select id, show_num from " . $this->dbdata_table . " where  show_num>? and lang_id=? order by show_num  limit 1";
            $st = $this->dbconn->Prepare($q);
            $r = $this->dbconn->getAll($st, array($this->show_num, $_SESSION['lang_id']));
        }


        if (!$r)
            return false;

        $other_id = $r[0][0];
        $other_show_num = $r[0][1];
        $current_id = $this->id;
        $current_show_num = $this->show_num;

        $q = "update " . $this->dbdata_table . " set show_num=? where id=?";
        $st = $this->dbconn->Prepare($q);

        //update current stage
        $this->dbconn->Execute($st, array($current_show_num, $other_id));

        //update other stage
        $this->dbconn->Execute($st, array($other_show_num, $current_id));

        return true;
    }

    /**
     * Трие картинка и тъмб по зададено име в базата 
     */
    public function delImage($image) {
        $object = new $this->dbdata_class($this->dbconn, $this->id);
        $object->fetch();

        $q = "UPDATE " . $this->dbdata_table . " SET " . $image . "=NULL WHERE id=? ";
        $st = $this->dbconn->Prepare($q);
        $r = $this->dbconn->Execute($st, array($this->id));


        if (!$r) {
            $this->seterr("Could not fetch the $this->dbdata_class record! " . $this->dbconn->ErrorMsg());
            return (false);
        }

        return (true);
    }

    /**
     * Добавя картинки към всеки модул с галерия.   
     * 
     * @return boolean
     */
    function addPicture($user_id = 0) {
        $comment = $_REQUEST['comment'];
        $file = $_FILES['file']['name'];
        $uploadedfile = $_FILES['file']['tmp_name'];
        $uploadedfile_thumb = $_FILES['file_thumb']['tmp_name'];
        $pic_arr = explode(".", $file);
        end($pic_arr);

        $pic_suff = current($pic_arr);
        $database_pic = $this->dbdata_class . "_" . $this->id . "_" . session_id() . "_" . time() . "." . $pic_suff;
        $database_thumb_pic = $this->dbdata_class . "_" . $this->id . "_" . session_id() . "_" . time() . "_thumb." . $pic_suff;
        $new_pic = "../images/" . $this->dbdata_class . "/" . $database_pic;
        $new_pic_thumb = "../images/" . $this->dbdata_class . "/" . $database_thumb_pic;

        if (!file_exists("../images")) {
            mkdir("../images");
            chmod("../images", 0755);
        }
        if (!file_exists("../images/" . $this->dbdata_class)) {
            mkdir("../images/" . $this->dbdata_class);
            chmod("../images/" . $this->dbdata_class, 0755);
        }


        if (!move_uploaded_file($uploadedfile, $new_pic)) {
            $_SESSION['system_error'] = "Неуспешно качване на файл! Обадете се на администратор!";
        } else {
            move_uploaded_file($uploadedfile_thumb, $new_pic_thumb);
            chmod($new_pic, 0644);
            chmod($new_pic_thumb, 0644);

            $image = new SimpleImage();
            $image->load($new_pic);
            $image->resizeToWidth(285);
            $image->save($new_pic_thumb);
            chmod($new_pic_thumb, 0644);
            $image->load($new_pic);
            // $image->resizeToWidth(300);
            $image->save($new_pic);
            chmod($new_pic, 0644);
            $q = "insert into pictures(tbl_name,row,picture,comment,state, thumb) values(?,?,?,?,?,?)";
            $st = $this->dbconn->Prepare($q);
            if ($this->dbconn->Execute($st, array($this->dbdata_table, $this->id, $database_pic, $comment, 1, $database_thumb_pic)))
                return true;
            return false;
        };
    }

    /**
     * Взима всички картинки за даден обект и ги запазва в масив pictures
     * @return boolean
     */
    public function getPictures() {
        $q = "select id, picture, comment, creation_date, thumb from pictures where tbl_name=? and row=? and state=? order by id";
        $st = $this->dbconn->Prepare($q);
        $r = $this->dbconn->GetAll($st, array($this->dbdata_table, $this->id, 1));
        $i = 1;
        $pictures = array();
        if ($r)
            foreach ($r as $row) {
                $row['number'] = $i;
                $row['creation_date'] = substr($row['creation_date'], 0, -3);
                $i++;
                $pictures[] = $row;
            }

        $this->pictures = $pictures;
        return true;
    }

    /**
     * Трие картинка според ид
     * @param type $picture_id
     * @return boolean
     */
    public function delPicture($picture_id) {

        $pic_q = "select picture,thumb from pictures where id=?";
        $pic_st = $this->dbconn->Prepare($pic_q);
        $pic_r = $this->dbconn->GetAll($pic_st, array($picture_id));

        unlink("../images/" . $this->dbdata_class . "/" . $pic_r[0][0]);
        unlink("../images/" . $this->dbdata_class . "/" . $pic_r[0][1]);

        $q = "delete from pictures where id=?";
        $st = $this->dbconn->Prepare($q);
        $r = $this->dbconn->Execute($st, array($picture_id));


        return true;
    }

    /**
     * Генерира автоматично URL за СЕО
     * @param type $page_key
     * @param type $field
     */
    public function generateSEOURL($page_key, $field = 'title') {
        if ($this->seo_url != '')
            return false;
        $bg_az = array("А", "Б", "В", "Г", "Д", "Е", "Ж", "З", "И", "Й", "К", "Л", "М", "Н", "О", "П", "Р", "С", "Т", "У", "Ф", "Х", "Ц", "Ч", "Ш", "Щ", "Ъ", "Ь", "Ю", "Я", "а", "б", "в", "г", "д", "е", "ж", "з", "и", "й", "к", "л", "м", "н", "о", "п", "р", "с", "т", "у", "ф", "х", "ц", "ч", "ш", "щ", "ъ", "ь", "ю", "я");
        $en_az = array("A", "B", "V", "G", "D", "E", "Zh", "Z", "I", "I", "K", "L", "M", "N", "O", "P", "R", "S", "T", "U", "F", "H", "C", "Ch", "Sh", "St", "Y", "Y", "Yu", "Ya", "a", "b", "v", "g", "d", "e", "zh", "z", "i", "y", "k", "l", "m", "n", "o", "p", "r", "s", "t", "u", "f", "h", "c", "ch", "sh", "st", "y", "y", "yu", "ya");

        $string = $this->$field;

        $string = strtolower($string);
        $string = str_replace("quot", '', $string);
        $string = preg_replace('/[^0-9a-zA-Zа-яА-Я ]/u', '', $string);
        $string = preg_replace('{ +}', ' ', $string);
        //  $string = str_replace($bg_az, $en_az, $string);

        $string = trim($string);
        $string = str_replace(array(' ', '.', '/'), "-", $string);

        $this->seo_title = $string;
        $this->seo_url = $string . "-" . $page_key . $this->id;
    }

    /**
     * Взима main_pic от базата на файлс ако съответния клас има този атрибут
     */
    function getMainPicture() {
        if ($this->main_pic) {
            $model_file = new File($this->dbconn);
            $model_file->fetchByFilter(array('filter' => ' WHERE id=? ', 'values' => array($this->main_pic)));

            $this->picture = $model_file;
        }

        if ($this->logo) {
            $model_file = new File($this->dbconn);
            $model_file->fetchByFilter(array('filter' => ' WHERE id=? ', 'values' => array($this->logo)));

            $this->logo_pic = $model_file;
        }

        return true;
    }

    public function getSocialShare() {
        $social_share = new SocialShare($this->dbconn);
        $shares = $social_share->fetchAll(array('filter' => 'where object_id=? ', 'values' => array($this->id)), 'id');

        $social = new Social($this->dbconn);
        $social->getFirst();

        $this->social = $social;

        $this->share = $shares[0];

        if (!$shares)
            return false;

        return true;
    }

    public function addSocialShare() {
        $this->getSocialShare();

        $share_button = $_REQUEST['share_button'];
        $like_status = $_REQUEST['like_status'];
        $send_status = $_REQUEST['send_status'];
        $like_box_status = $_REQUEST['like_box_status'];

        if (!$like_status)
            $like_status = 0;
        if (!$send_status)
            $send_status = 0;
        if (!$like_box_status)
            $like_box_status = 0;



        if ($this->share) {
            $this->share->update(array('share_button', 'like_status', 'send_status', 'like_box_status'), array($share_button, $like_status, $send_status, $like_box_status));
        } else {
            $social_share = new SocialShare($this->dbconn);
            $share_id = $social_share->insert(array('type', 'object_id', 'share_button', 'like_status', 'send_status', 'like_box_status'), array(get_class($this), $this->id, $share_button, $like_status, $send_status, $like_box_status));
        }
    }

    function getFiles() {
        $q = "select file_id from attachments where row_id=? and tbl_name=?";
        $st = $this->dbconn->Prepare($q);
        $r = $this->dbconn->Execute($st, array($this->id, $this->dbdata_table));


        if (!$r)
            return false;

        $files = array();
        foreach ($r as $id) {
            $file = new File($this->dbconn, $id[0]);
            $file->fetch();
            $file_ids .= $file->id . ',';
            $file->size = '~' . $file->size / 1000 . 'kB';

            $extension_arr = explode('.', $file->file);
            end($extension_arr);
            $file->extension = current($extension_arr);
//            if(ceil($file->size/1000) < 1)
//                $file->size = '~' . $file->size . 'B';
//            else 
//                $file->size = '~' . ceil($file->size/1000) . 'kB';

            $files[] = $file;
        }

        $this->file_ids = $file_ids;
        $this->files = $files;
        return true;
    }

    function deleteFiles() {
        $q = "delete from attachments where row_id=? and tbl_name=?";
        $st = $this->dbconn->Prepare($q);
        $r = $this->dbconn->Execute($st, array($this->id, $this->dbdata_table));

        if (!$r)
            return false;

        return true;
    }

    function deleteFile($file_id) {
        $q = "delete from attachments where file_id=? and row_id=? and tbl_name=?";
        $st = $this->dbconn->Prepare($q);
        $r = $this->dbconn->Execute($st, array($file_id, $this->id, $this->dbdata_table));

        if (!$r)
            return false;

        return true;
    }

    function insertFiles($file_id) {
        $q = "select id from attachments where  row_id=? and tbl_name=? and file_id=?";
        $st = $this->dbconn->Prepare($q);
        $r = $this->dbconn->getAll($st, array($this->id, $this->dbdata_table, $file_id));
        if (!$r) {
            $q = "insert into attachments (row_id, tbl_name, file_id) VALUES(?,?,?)";
            $st = $this->dbconn->Prepare($q);
            $r = $this->dbconn->Execute($st, array($this->id, $this->dbdata_table, $file_id));
        }
        if (!$r)
            return false;

        return true;
    }

}

?>
