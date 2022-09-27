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

    function periodicTask($request)
    {
        global $em;

        $task = $em->find(Task::class, $request['id']);

        $head = $em->find(Task::class, $em->createQueryBuilder()->select('MIN(t.id)')
            ->from(Task::class, 't')
            ->where("t.group_id = $task->group_id")
            ->getQuery()
            ->getSingleScalarResult());

        $repeats = $em->getRepository(Task::class)->getGroupRepeats(['id'=>$task->group_id]);

        return ['head' => $task, 'repeats' => $repeats];
    }
}