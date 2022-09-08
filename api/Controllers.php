<?php

require_once './Services.php';

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

    function getAllTasks($request)
    {
       return $this->taskService->getAllTasks($request);
    }

    //Изменить на Dto
    function getUsers($request)
    {
        return $this->taskService->getUsers($request);
    }

    function getUserTasks($request)
    {
        return $this->taskService->getUserTasks(request: $request);
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

    function deleteTask($request)
    {
        return $this->taskService->deleteTask($request);
    }

}