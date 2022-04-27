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

    function dbConstruct(){
        global $connection;
        $result = mysqli_query($connection, "SELECT * FROM users WHERE id = $this->id");
        $result = mysqli_fetch_assoc($result);
        $this->id = $result['id'];
        $this->name = $result['name'];
        $this->tasks = $this->getTasks();
    }

    private function getTasks(){
        global $connection;
        $tasks = [];
        $stmt = $connection->prepare(file_get_contents(__DIR__.'/../SQL/getJoinedTasksFromUser.sql'));
        $stmt->bind_param("i", $this->id);
        $stmt->execute();
        $result = $stmt->get_result();
        while ($row = $result->fetch_assoc()){
            array_push($tasks, $row['user_id']);
        }

        return $tasks;
    }

    function addSelf() {
        global $connection;
        try {
            $connection->begin_transaction();
            $stmt = $connection->prepare(file_get_contents(__DIR__ . '/../SQL/addUser.sql'));
            $stmt->bind_param("s", $this->name);
            $stmt->execute();
        } catch (Exception $e) {
            $connection->rollBack();
            echo "Ошибка: " . $e->getMessage();
        }

    }

    function addTask($taskId){
        global $connection;
        $result = mysqli_query($connection, "SELECT * FROM users_tasks WHERE user_id=$this->id AND task_id=$taskId");
        $result = mysqli_fetch_assoc($result);
        if ($result == null) {
            mysqli_query($connection, "INSERT INTO users_tasks (user_id, task_id) VALUES ($this->id, $taskId)");
        }
    }

    function unlinkTask($taskId){
        global $connection;
        try {
            $connection->begin_transaction();
            $stmt = $connection->prepare("DELETE FROM users_tasks WHERE user_id=$this->id AND task_id=$taskId");
            $stmt->execute();
            $connection->commit();
        } catch (Exception $e) {
            $connection->rollBack();
            echo "Ошибка: " . $e->getMessage();
        }

    }

}

//getNamesByTaskId
//
//CRUD
