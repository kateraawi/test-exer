<?php

require_once (__DIR__.'/../dbconfig.php');
require_once ('Task.php');

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="users")
 */
class User
{
    /** @ORM\Id @ORM\Column(type="integer") @ORM\GeneratedValue */
    public $id;

    /** @ORM\Column(type="string") */
    public $name;

    /**
     * @ORM\ManyToMany(targetEntity="Task")
     * @ORM\JoinTable(name="users_tasks")
     */
    public $tasks;

    function __construct(){

        $this->tasks = new ArrayCollection();
    }

    function dbConstruct(){
        /*global $connection;
        $result = mysqli_query($connection, "SELECT * FROM users WHERE id = $this->id");
        $result = mysqli_fetch_assoc($result);
        $this->id = $result['id'];
        $this->name = $result['name'];
        $this->tasks = $this->getTasks();*/
    }

    public function getTasks($request = null){
        return $this->tasks;
        /*global $connection;

        if ($request) {
            $this->id = (int)$request['user_id'];
        }

        $tasks = [];

        $stmt = $connection->prepare(file_get_contents(__DIR__.'/../SQL/getJoinedTasksFromUser.sql'));
        $stmt->bind_param("i", $this->id);
        $stmt->execute();
        $result = $stmt->get_result();

        while ($row = $result->fetch_assoc()){
            $task = new Task($row['task_id']);
            $task->dbConstruct();
            array_push($tasks, $task);
        }*/

    }

    public function getAll()
    {
        global $em;
        return $em->createQueryBuilder()->select('u')
            ->from('User', 'u')
            ->getQuery()
            ->getResult();

        /*global $connection;
        try {

            $users = [];

            $connection->begin_transaction();
            $stmt = $connection->prepare("SELECT * FROM users");

            $stmt->execute();

            $result = $stmt->get_result();
            while ($row = $result->fetch_assoc()){
                $user = new User($row['id']);
                $user->dbConstruct();
                array_push($users, $user);
            }

            return $users;

        } catch (Exception $e) {
            $connection->rollBack();
            echo "Ошибка: " . $e->getMessage();
        }*/
    }

    function addSelf($request = null) {

        global $em;

        if ($request) {
            $this->name = $request['name'];
        }

        $em->persist($this);
        $em->flush();

    }

    function addTask($request){
        global $em;

        $user = $em->find('User', $request['user_id']);
        $task = $em->find('Task', $request['task_id']);

        $user->tasks[] = $task;
        $em->persist($user);
        $em->flush();
        /*global $connection;
        $result = mysqli_query($connection, "SELECT * FROM users_tasks WHERE user_id=$this->id AND task_id=$taskId");
        $result = mysqli_fetch_assoc($result);
        if ($result == null) {
            mysqli_query($connection, "INSERT INTO users_tasks (user_id, task_id) VALUES ($this->id, $taskId)");
        }*/
    }

    function addTaskGroup(Task $task){

        $group = $task->getSelfGroup();
        foreach ($group as $taskInGroup) {
            $this->addTask($taskInGroup);
        }

        /*global $connection;
        $result = mysqli_query($connection, "SELECT * FROM users_tasks WHERE user_id=$this->id AND task_id=$taskId");
        $result = mysqli_fetch_assoc($result);
        if ($result == null) {
            mysqli_query($connection, "INSERT INTO users_tasks (user_id, task_id) VALUES ($this->id, $taskId)");
        }*/
    }

    function unlinkTask($taskId){
        global $em;


        /*global $connection;
        try {
            $connection->begin_transaction();
            $stmt = $connection->prepare("DELETE FROM users_tasks WHERE user_id=$this->id AND task_id=$taskId");
            $stmt->execute();
            $connection->commit();
        } catch (Exception $e) {
            $connection->rollBack();
            echo "Ошибка: " . $e->getMessage();
        }*/

    }

}

//getNamesByTaskId
//
//CRUD
