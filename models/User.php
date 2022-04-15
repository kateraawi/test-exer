<?php

require_once (__DIR__.'/../services/dbconfig.php');
require_once ('Task.php');

class User
{
    public $id;
    public $name;
    public $tasks;

    function __construct($id = null, $name = null){
       $this->id = $id;
       $this->name = $name;
    }

    function defineUser($id){
        global $link;
        $result = mysqli_query($link, "SELECT * FROM users WHERE id=".$id);
        $result = mysqli_fetch_assoc($result);
        //print_r($result);
        $this->id = $result['id'];
        $this->name = $result['name'];
        $this->getTasks();

    }

    function getTasks(){
        global $link;
        $tasks = [];
        $result = mysqli_query($link, "SELECT * FROM users INNER JOIN users_tasks ON users.id = users_tasks.user_id AND users.id = ". $this->id ." INNER JOIN tasks ON tasks.id = users_tasks.task_id");
        while ($row = mysqli_fetch_assoc($result)){
            array_push($tasks, new Task($row['task_id'], $row['descr'], $row['do_from'], $row['do_to'], $row['period_days'], $row['period_qua']));
        }
        $this->tasks = $tasks;
    }

}