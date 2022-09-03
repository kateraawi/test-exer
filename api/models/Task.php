<?php

require_once (__DIR__.'/../dbconfig.php');
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
    public $group_id;
    public $users;
    public $repeats;

    function __construct($id = null, $description  = null, $do_from  = null, $do_to = null, $period_days  = null, $period_quantity  = null, $completed = 0, $group_id = null, $users = [])
    {
        $this->id = $id;
        $this->description = $description;
        $this->do_from = $do_from;
        $this->do_to = $do_to;
        $this->period_days = $period_days;
        $this->period_quantity = $period_quantity;
        $this->completed = $completed;
        $this->group_id = $group_id;
        $this->users = $users;
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
        $this->group_id = $result['group_id'];
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

    public function getRepeatedPeriods()
    {

        $do_from = $this->do_from;
        $do_to = $this->do_to;

        $repeats = [];

        $do_for = ((new DateTime())->setTimestamp(0)->add(date_diff(new DateTime($do_from), new DateTime($do_to)))->getTimestamp())/86400;

        for ($i = 0; $i < $this->period_quantity; $i++) {
            array_push($repeats,
                [$do_from, $do_to]
            );

            $do_from = date('Y-m-d', strtotime($do_to . "+$this->period_days days"));
            $do_to = date('Y-m-d', strtotime($do_from . "+$do_for days"));
        }

        return $repeats;
    }

    public function getAll()
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

    function addSelf($request = null)
    {
        global $connection;
        try {

            if ($request) {
                $this->description = $request['description'];
                $this->do_from = date('Y-m-d', strtotime($request['do_from']));
                $this->do_to = date('Y-m-d', strtotime($request['do_to']));
                //$this->do_from = $request['do_from'];
                //$this->do_to = $request['do_to'];
                $this->period_days = 0;
                $this->period_quantity = 0;
                $this->completed = 0;
            }

            $stmt = $connection->prepare(file_get_contents(__DIR__ . '/../SQL/addTask.sql'));
            $stmt->bind_param("sssiiii", $this->description, $this->do_from, $this->do_to, $this->period_days, $this->period_quantity, $this->completed, $this->group_id);

            $stmt->execute();
            $this->id = $stmt->insert_id;

            foreach ($this->users as $user){
                $this->addUser($user);
                //print $user.'_____';
            }

            //mysqli_query($connection, "INSERT into tasks (descr, do_from, do_to, period_days, period_qua, completed, group_id) VALUES ($this->description, '2020-01-12', '2020-01-12', 0, 0, 0, null)");

        } catch (Exception $e) {
            $connection->rollBack();
            echo "Ошибка: " . $e->getMessage();
        }

    }

    function addSelfGroup($request = null) {
        global $connection;
        try {

            if ($request) {
                $this->description = $request['description'];
                $this->do_from = date('Y-m-d', strtotime($request['do_from']));
                $this->do_to = date('Y-m-d', strtotime($request['do_to']));
                $this->period_days = (int)$request['period_days'];
                $this->period_quantity = (int)$request['period_quantity'];
                $this->completed = 0;
            }

            $connection->begin_transaction();

            $stmtGroup = $connection->prepare(file_get_contents(__DIR__ . '/../SQL/sortByGroupId.sql'));
            $stmtGroup->execute();
            $result = $stmtGroup->get_result();
            $row = $result->fetch_assoc();
            $this->group_id = $row['group_id'] + 1;

            foreach ($this->getRepeatedPeriods() as $period){
                $task = new Task(null, $this->description, $period[0], $period[1], $this->period_days, $this->period_quantity, $this->completed, $this->group_id, $this->users);
                $task->addSelf();
            }
            $connection->commit();

        } catch (Exception $e) {
            $connection->rollBack();
            echo "Ошибка: " . $e->getMessage();
        }
    }

    function updateSelf($request = null)
    {
        global $connection;

        try {

            if ($request) {
                $this->id = $request['id'];
                $this->dbConstruct();
                $this->description = $request['description'];
            } else {
                //$this->dbConstruct();
            }

            $connection->begin_transaction();

            $initTask = new Task($this->id);
            $initTask->dbConstruct();

            $stmt = $connection->prepare(file_get_contents(__DIR__ . '/../SQL/updateTask.sql'));


            $stmt->bind_param("sssiiiii", $this->description, $this->do_from, $this->do_to, $this->period_days, $this->period_quantity, $this->completed, $this->group_id, $this->id);
            $stmt->execute();

            $inBaseUsers = $initTask->users;
            $stmt = $connection->prepare("SELECT * FROM users_tasks WHERE task_id=?");
            $stmt->bind_param('i', $this->id);
            $stmt->execute();
            $result = $stmt->get_result();

            if ($this->users !== $inBaseUsers) {
                while ($row = $result->fetch_assoc()) {
                    array_push($inBaseUsers, $row['user_id']);
                }

                foreach ($inBaseUsers as $userId) {
                    $this->unlinkUser($userId);
                }

                foreach ($this->users as $userId) {
                    $this->addUser($userId);
                }
            }

            $connection->commit();

            $this->getUsers();

        } catch (Exception $e) {
            $connection->rollBack();
            echo "Ошибка: " . $e->getMessage();
        }
    }

    public function updateSelfGroup($request = null)
    {
        global $connection;
        try {

            if ($request) {
                $this->id = $request['id'];
                $this->dbConstruct();
                $this->description = $request['description'];
                //$this->period_days = $request['period_days'];
                $this->period_quantity = $request['period_quantity'];
            } else {
                //$this->dbConstruct();
            }

            $connection->begin_transaction();

            $initTask = new Task($this->id);
            $initTask->dbConstruct();

            //print_r($initTask);

            if ($this->period_quantity < $initTask->period_quantity) {

                $stmt = $connection->prepare(file_get_contents(__DIR__ . '/../SQL/selectByGroupIdDesc.sql'));
                $stmt->bind_param("i", $this->group_id);
                $stmt->execute();
                $result = $stmt->get_result();


                for ($i = 0; $i < $initTask->period_quantity - $this->period_quantity; $i++) {
                    $row = mysqli_fetch_assoc($result);
                    //print_r($row);
                    $delTask = new Task($row['id']);
                    $delTask->deleteSelf();
                }

            }

            $stmt = $connection->prepare(file_get_contents(__DIR__ . '/../SQL/selectByGroupId.sql'));
            $stmt->bind_param("i", $this->group_id);
            $stmt->execute();
            $result = $stmt->get_result();

            while ($row = $result->fetch_assoc()) {
                $task = new Task($row['id']);
                $task->dbConstruct();
                $task->description = $this->description;
                $task->users = $this->users;
                $task->period_quantity = $this->period_quantity;
                $task->updateSelf();
            }

            //print $this->period_quantity;
            //print $initTask->period_quantity;

            if ($this->period_quantity > $initTask->period_quantity) {
                //print 123;
                $periods = $this->getRepeatedPeriods();
                for ($i = 0; $i < ($this->period_quantity - $initTask->period_quantity); $i++) {
                    for ($i=$initTask->period_quantity;$i<$this->period_quantity;$i++){
                        //print_r($this->users);
                        $newTask = new Task(null, $this->description, $periods[$i][0], $periods[$i][1], $this->period_days, $this->period_quantity, 0, $this->group_id, $this->users);
                        //print_r($newTask->users);
                        $newTask->addSelf();
                        print_r($newTask);
                    }
                }
            }

            $connection->commit();

        } catch (Exception $e){
            $connection->rollBack();
            echo "Ошибка: " . $e->getMessage();
        }

    }

    function completeSelf($request = null)
    {
        if ($request) {
          $this->id = (int)$request['id'];
          $this->dbConstruct();
        }

        $this->completed = (int)!boolval($this->completed);
        $this->updateSelf();
    }

    function deleteSelf($request = null)
    {
        global $connection;

        try {
            $connection->begin_transaction();

            if ($request) {
                $this->id = (int)$request['id'];
            }

            $this->dbConstruct();

            if ($this->group_id !== null){

                $stmt = $connection->prepare(file_get_contents(__DIR__ . '/../SQL/selectByGroupIdDesc.sql'));
                $stmt->bind_param("i", $this->group_id);
                $stmt->execute();
                $result = $stmt->get_result();
                $row = $result->fetch_assoc();
                //print_r($row);

                if ((int)$row['id'] !== (int)$this->id) {

                    $newGroupTasks = [$row];

                    $stmtGroup = $connection->prepare(file_get_contents(__DIR__ . '/../SQL/sortByGroupId.sql'));
                    $stmtGroup->execute();
                    $resultStmtGroup = $stmtGroup->get_result();
                    $rowStmtGroup = $resultStmtGroup->fetch_assoc();
                    $newGroupId = $rowStmtGroup['group_id'] + 1;

                    $task = new Task($row['id']);
                    $task->dbConstruct();
                    $task->group_id = $newGroupId;
                    $task->updateSelf();

                    while ($row = $result->fetch_assoc()) {
                        if ((int)$row['id'] === (int)$this->id) {
                            break;
                        }
                        array_push($newGroupTasks, $row);

                        $task = new Task($row['id']);
                        $task->dbConstruct();
                        $task->group_id = $newGroupId;
                        $task->updateSelf();
                    }

                    $stmt = $connection->prepare(file_get_contents(__DIR__ . '/../SQL/selectByGroupId.sql'));
                    $stmt->bind_param("i", $this->group_id);
                    $stmt->execute();
                    $result = $stmt->get_result();
                    print_r($result);

                    //$oldGroupTasks = [];

                    while ($row = $result->fetch_assoc()) {
                        if ((int)$row['id'] === (int)$this->id) {
                            break;
                        }

                        $task = new Task($row['id']);
                        $task->dbConstruct();
                        $task->period_quantity = mysqli_num_rows($result) - 1;
                        $task->updateSelf();
                    }

                    foreach ($newGroupTasks as $taskRow) {
                        $task = new Task($taskRow['id']);
                        $task->dbConstruct();
                        $task->period_quantity = count($newGroupTasks);
                        $task->updateSelf();
                    }

                    //print $newGroupId;

                } else {
                    print 'no';
                }
            }

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
            $connection->begin_transaction();
            $stmt = $connection->prepare("INSERT INTO users_tasks (user_id, task_id) VALUES (?, ?)");
            $stmt->bind_param("ii", $user_id, $this->id);
            $stmt->execute();
            $connection->commit();
        }
    }

    function addUserGroup($request = null, $user_id = null)
    {
        global $connection;
        $connection->begin_transaction();
        //var_dump($user_id);
        if ($request){
            $task = new Task($request['task_id']);
            $task->dbConstruct();
            $group_id = $task->group_id;
            $user_id = $request['user_id'];
        } else {
            $group_id = $this->group_id;
            if ($user_id === null){
                return;
            }
        }

        $stmt = $connection->prepare(file_get_contents(__DIR__ . '/../SQL/selectByGroupId.sql'));
        $stmt->bind_param("i", $group_id);
        $stmt->execute();
        $result = $stmt->get_result();

        while ($row = $result->fetch_assoc()) {
            $task = new Task($row['id']);
            //$task->dbConstruct();
            $task->addUser($user_id);
        }

        $connection->commit();
    }

    function unlinkUser($user_id)
    {
        global $connection;

        try {
            $connection->begin_transaction();
            $stmt = $connection->prepare("DELETE FROM users_tasks WHERE user_id=$user_id AND task_id=$this->id");
            $stmt->execute();
            $connection->commit();

            $this->users = $this->getUsers();


        } catch (Exception $e) {
            $connection->rollBack();
            echo "Ошибка: " . $e->getMessage();
        }

    }
}

