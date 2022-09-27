<?php

namespace TestExer\Controllers;

ini_set('display_errors', '1');
ini_set('display_startup_errors', '1');
error_reporting(E_ALL);

require_once(__DIR__ . '/../services/TaskService.php');

use TestExer\Services\TaskService as TaskService;

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