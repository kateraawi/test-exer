<?php

require_once './Services.php';

use TestExer\Services\TaskService as TaskService;
use TestExer\Services\UserService as UserService;

class TaskController
{
    private $taskService;

    function __construct()
    {
        $this->taskService = new TaskService;
    }

    function addTask($request)
    {
        return $this->taskService->addTask(request: $request);
    }

    function addTaskGroup($request)
    {
        return $this->taskService->addTaskGroup(request: $request);
    }

    function getTask($request)
    {
        /*if(!isset($request['user_id'])) {
            throw new Exception("Нет дотупа");
        }*/
        return $this->taskService->getTask($request);
    }

    function getAllTasks()
    {
        /*if(!isset($request['user_id'])) {
            throw new Exception("Нет дотупа");
        }*/
       return $this->taskService->getAllTasks();
    }

    function getUsers($request)
    {
        return $this->taskService->getUsers($request);
    }

    function getUserTasks($request)
    {
        return $this->taskService->getUserTasks(request: $request);
    }

    function getUserGridTasks($request)
    {
        return $this->taskService->getUserGridTasks(request: $request);
    }

    function getUserCreatedGridTasks($request)
    {
        return $this->taskService->getUserCreatedGridTasks(request: $request);
    }

    function updateTask($request)
    {
        return $this->taskService->updateTask($request);
    }

    function updateTaskGroup($request)
    {
        return $this->taskService->updateTaskGroup($request);
    }

    function completeTask($request)
    {
        return $this->taskService->completeTask($request);
    }

    function addUser($request)
    {
        return $this->taskService->addUser($request);
    }

    function addUserGroup($request)
    {
        return $this->taskService->addUserGroup($request);
    }

    function unlinkUser($request)
    {
        return $this->taskService->unlinkUser($request);
    }

    function unlinkUserGroup($request)
    {
        return $this->taskService->unlinkUserGroup($request);
    }

    /**
     * Удаление задачи
     */
    function deleteTask($request)
    {
        return $this->taskService->deleteTask($request);
    }

}

class UserController
{
    private $userService;

    function __construct()
    {
        $this->userService = new UserService;
    }

    function getAllUsers()
    {
        return $this->userService->getAllUsers();
    }

    function getUser($request)
    {
        //echo $this->userService->getUser($request);
        return $this->userService->getUser($request);
    }

    function addUser($request)
    {
        return $this->userService->addUser($request);
    }

    function addTask($request)
    {
        return $this->userService->addTask($request);
    }

    function addTaskGroup($request)
    {
        return $this->userService->addTaskGroup($request);
    }

    function unlinkTask($request)
    {
        return $this->userService->unlinkTask($request);
    }

    function unlinkTaskGroup($request)
    {
        return $this->userService->unlinkTaskGroup($request);
    }

    function deleteUser($request)
    {
        return $this->userService->deleteUser($request);
    }
}