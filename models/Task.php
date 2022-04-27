<?php

require_once (__DIR__.'/../services/dbconfig.php');
require_once ('User.php');

class Task
{
    public $id;
    public $description;
    public $do_from;
    public $do_to;
    public $period_days;
    public $period_quantity;
    public $completed;
    public $users;
    public $repeats;

    function __construct($id = null, $description  = null, $do_from  = null, $do_to = null, $period_days  = null, $period_quantity  = null, $completed = 0){
        $this->id = $id;
        $this->description = $description;
        $this->do_from = $do_from;
        $this->do_to = $do_to;
        $this->period_days = $period_days;
        $this->period_quantity = $period_quantity;
        $this->completed = $completed;
    }

    function dbConstruct()
    {
        global $connection;
        $result = mysqli_query($connection, "SELECT * FROM tasks WHERE id = $this->id");
        $result = mysqli_fetch_assoc($result);
        $this->id = $result['id'];
        $this->description = $result['descr'];
        $this->do_from = $result['do_from'];
        $this->do_to = $result['do_to'];
        $this->period_days = $result['period_days'];
        $this->period_quantity = $result['period_qua'];
        $this->completed = $result['completed'];
        $this->users = $this->getUsers();
        $this->repeats = $this->getRepeatedPeriods();
    }

    private function getUsers()
    {
        global $connection;
        $users = [];
        $stmt = $connection->prepare(file_get_contents(__DIR__.'/../SQL/getJoinedUsersFromTask.sql'));
        $stmt->bind_param("i", $this->id);
        $stmt->execute();
        $result = $stmt->get_result();
        while ($row = $result->fetch_assoc()){
            array_push($users, $row['user_id']);
        }

        return $users;
    }

    private function getRepeatedPeriods()
    {

        $do_from = $this->do_from;
        $do_to = $this->do_to;

        $repeats = [];

        for ($i = 0; $i < $this->period_quantity; $i++) {
            array_push($repeats,
                [$do_from, $do_to]
            );
            $do_from = date('Y-m-d', strtotime($do_from . "+$this->period_days days"));
            $do_to = date('Y-m-d', strtotime($do_to . "+$this->period_days days"));
        }

        return $repeats;
    }

    function getAll()
    {
        global $connection;
        try {

            $tasks = [];

            $connection->begin_transaction();
            $stmt = $connection->prepare("SELECT * FROM tasks");

            $stmt->execute();

            $result = $stmt->get_result();
            while ($row = $result->fetch_assoc()){
                $task = new Task($row['id']);
                $task->dbConstruct();
                array_push($tasks, $task);
            }

            return $tasks;

        } catch (Exception $e) {
            $connection->rollBack();
            echo "Ошибка: " . $e->getMessage();
        }
    }

    function addSelf()
    {
        global $connection;
        try {
            $connection->begin_transaction();
            $stmt = $connection->prepare(file_get_contents(__DIR__ . '/../SQL/addTask.sql'));
            $stmt->bind_param("sssiii", $this->description, $this->do_from, $this->do_to, $this->period_days, $this->period_quantity, $this->completed);
            $stmt->execute();
        } catch (Exception $e) {
            $connection->rollBack();
            echo "Ошибка: " . $e->getMessage();
        }

    }

    function updateSelf()
    {
        global $connection;

        try {
            $connection->begin_transaction();
            $stmt = $connection->prepare(file_get_contents(__DIR__ . '/../SQL/updateTask.sql'));
            $stmt->bind_param("sssiiii", $this->description, $this->do_from, $this->do_to, $this->period_days, $this->period_quantity, $this->completed, $this->id);
            $stmt->execute();

            $inBaseUsers = [];
            $stmt = $connection->prepare("SELECT * FROM users_tasks WHERE task_id=?");
            $stmt->bind_param('i', $this->id);
            $stmt->execute();
            $result = $stmt->get_result();
            while ($row = $result->fetch_assoc()){
                array_push($inBaseUsers, $row['user_id']);
            }

            foreach ($inBaseUsers as $userId) {
                $this->unlinkUser($userId);
            }

            foreach ($this->users as $userId) {
                $this->addUser($userId);
            }

            $connection->commit();

            $this->getUsers();

        } catch (Exception $e) {
            $connection->rollBack();
            echo "Ошибка: " . $e->getMessage();
        }
    }

    function deleteSelf()
    {
        global $connection;

        try {
            $connection->begin_transaction();
            $stmt = $connection->prepare("DELETE FROM tasks WHERE id=$this->id");
            $stmt->execute();
            $connection->commit();
        } catch (Exception $e) {
            $connection->rollBack();
            echo "Ошибка: " . $e->getMessage();
        }
    }

    function addUser($user_id)
    {
        global $connection;

        $result = mysqli_query($connection, "SELECT * FROM users_tasks WHERE user_id=$user_id AND task_id=$this->id");
        $result = mysqli_fetch_assoc($result);
        if ($result === null) {
            mysqli_query($connection, "INSERT INTO users_tasks (user_id, task_id) VALUES ($user_id, $this->id)");
        }
    }

    function unlinkUser($user_id)
    {
        global $connection;

        try {
            $connection->begin_transaction();
            $stmt = $connection->prepare("DELETE FROM users_tasks WHERE user_id=$user_id AND task_id=$this->id");
            $stmt->execute();
            $connection->commit();

            /*if(in_array($user_id, $this->users)) {
                foreach (array_keys($this->users, $user_id, true) as $key) {
                    unset($this->users[$key]);
                }
            }*/

        } catch (Exception $e) {
            $connection->rollBack();
            echo "Ошибка: " . $e->getMessage();
        }

    }
}

//getAll
//delete
//update
//фамилии
