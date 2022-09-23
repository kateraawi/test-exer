<?php

require_once __DIR__ . '/../services.php';

use TestExer\Services\TaskService as TaskService;
use TestExer\Services\UserService as UserService;

use TestExer\Model\User as User;
use TestExer\Model\Task as Task;

class TwigService
{
    private $userService;
    private $taskService;

    function __construct()
    {
        $this->userService = new UserService;
        $this->taskService = new TaskService;
    }

    function userTasks($request)
    {
        global $em;

        $user = $em->find(User::class, $request['user_id']);
        $taskList = $this->taskService->getUserTasks($request);

        $newTaskList = [];
        foreach ($taskList as $task)
        {
            $newTaskList[] = (array)$task;
        }

        return ['user'=>(array)$user, 'tasks'=>$newTaskList];
    }

    function createdTasks($request)
    {
        global $em;

        $user = $em->find(User::class, $request['user_id']);
        $taskList = $em->find(User::class, $request['user_id'])->created->getValues();

        $newTaskList = [];
        foreach ($taskList as $task)
        {
            $newTaskList[] = (array)$task;
        }

        return ['user'=>(array)$user, 'tasks'=>$newTaskList];
    }
}