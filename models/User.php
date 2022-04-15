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

    function defineSelf(){
        global $link;
        $result = mysqli_query($link, "SELECT * FROM users WHERE id = $this->id");
        $result = mysqli_fetch_assoc($result);
        $this->id = $result['id'];
        $this->name = $result['name'];
        $this->getTasks();
    }

    private function getJoined(){
        global $link;
        return mysqli_query($link, "SELECT * FROM users INNER JOIN users_tasks ON users.id = users_tasks.user_id AND users.id = $this->id  INNER JOIN tasks ON tasks.id = users_tasks.task_id");
    }

    private function getTasks(){
        $tasks = [];
        $result = $this->getJoined();
        while ($row = mysqli_fetch_assoc($result)){
            array_push($tasks, $row['task_id']);
        }
        $this->tasks = $tasks;
    }

    function expandSelf(){
        $tasks = [];
        foreach ($this->tasks as $taskID) {
            $task = new Task($taskID);
            $task->defineSelf();
            array_push($tasks, $task);
        }
        $this->tasks = $tasks;
    }

    function addSelf() {
        global $link;
        mysqli_query($link, "INSERT into users (name) VALUES (" . mysqli_real_escape_string($link, $this->name) . ")");
    }

}