<?php

require_once (__DIR__.'/../services/dbconfig.php');

class Task
{
    public $id;
    public $descr;
    public $do_from;
    public $do_to;
    public $period_days;
    public $period_qua;
    public $completed;
    public $users;
    public $repeats;

    function __construct($id = null, $descr  = null, $do_from  = null, $do_to = null, $period_days  = null, $period_qua  = null, $completed = 0){
        $this->id = $id;
        $this->descr = $descr;
        $this->do_from = $do_from;
        $this->do_to = $do_to;
        $this->period_days = $period_days;
        $this->period_qua = $period_qua;
        $this->completed = $completed;
    }

    function defineSelf(){
        global $link;
        $result = mysqli_query($link, "SELECT * FROM tasks WHERE id = $this->id");
        $result = mysqli_fetch_assoc($result);
        $this->id = $result['id'];
        $this->descr = $result['descr'];
        $this->do_from = $result['do_from'];
        $this->do_to = $result['do_to'];
        $this->period_days = $result['period_days'];
        $this->period_qua = $result['period_qua'];
        $this->completed = $result['completed'];
        $this->users = $this->getUsers();
        $this->repeats = $this->getRepeats();
    }

    function getJoined() {
        global $link;
        return mysqli_query($link, "SELECT * FROM tasks INNER JOIN users_tasks ON tasks.id = users_tasks.task_id AND tasks.id = $this->id INNER JOIN users ON users.id = users_tasks.user_id");
    }

    private function getUsers(){
        $users = [];
        $result = $this->getJoined();
        while ($row = mysqli_fetch_assoc($result)){
            array_push($users, $row['user_id']);
        }

        return $users;
    }

    function expandSelf(){
        $users = [];
        foreach ($this->users as $userID) {
            $user = new User($userID);
            $user->defineSelf();
            array_push($users, $user);
        }

        $this->users = $users;
    }

    function addSelf() {
        global $link;
        $descr = mysqli_real_escape_string($link, $this->descr);
        $do_from = mysqli_real_escape_string($link, $this->do_from);
        $do_to = mysqli_real_escape_string($link, $this->do_to);
        $period_days = mysqli_real_escape_string($link, $this->period_days);
        $period_qua = mysqli_real_escape_string($link, $this->period_qua);
        $completed = mysqli_real_escape_string($link, $this->completed);
        mysqli_query($link, "INSERT into tasks (descr, do_from, do_to, period_days, period_qua, completed) VALUES ( $descr, $do_from, $do_to, $period_days, $period_qua, $completed)");
    }

    function getRepeats(){

        $do_from = $this->do_from;
        $do_to = $this->do_to;

        $repeats = [];

        for ($i = 0; $i < $this->period_qua; $i++) {
            array_push($repeats,
                [$do_from, $do_to]
            );
            $do_from = date('Y-m-d', strtotime($do_from . "+$this->period_days days"));
            $do_to = date('Y-m-d', strtotime($do_to . "+$this->period_days days"));
        }

        return $repeats;

    }
}