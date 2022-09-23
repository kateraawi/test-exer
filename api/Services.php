<?php

require_once (__DIR__.'/models/User.php');
require_once (__DIR__.'/models/Task.php');
require_once (__DIR__.'/vendor/autoload.php');

use TestExer\Model\Task as Task;
use TestExer\Model\User as User;

class TaskService
{

    function addTask($request = null, $task = null){

        global $em;

        if ($request)
        {
            $task = new Task;
            $task->setDescription($request['description']);
            $task->setPeriod(
                date('Y-m-d', strtotime($request['do_from'])),
                date('Y-m-d', strtotime($request['do_to'])),
                0,
                0);
            $task->setCompleted(0);
            $task->created_at = date('Y-m-d');
            $task->updated_at = date('Y-m-d');

            $creator = $em->find(User::class, $request['creator']);
            $task->creator = $creator;
        }

        $em->persist($task);
        $em->flush();

        return $task->toDto();
    }

    function addTaskGroup($request) {
        global $em;
        $itask = new Task;

        $itask->setDescription($request['description']);
        $itask->setPeriod(date('Y-m-d', strtotime($request['do_from'])),
            date('Y-m-d', strtotime($request['do_to'])),
            (int)$request['period_days'],
            (int)$request['period_quantity']);

        $itask->created_at = date('Y-m-d');
        $itask->updated_at = date('Y-m-d');

        $creator = $em->find(User::class, $request['creator']);
        $itask->creator = $creator;

        $group_id = $itask->getNewGroupId();

        $dtoArr = [];
        foreach ($itask->getRepeatedPeriods() as $period){

            $task = new Task;
            $task->setDescription($itask->description);
            $task->setPeriod($period[0], $period[1], $itask->period_days, $itask->period_quantity);
            $task->setCompleted(0);
            $task->setGroupId($group_id);
            $task->created_at = date('Y-m-d');
            $task->updated_at = date('Y-m-d');
            $task->creator = $creator;

            $dtoArr[] = $this->addTask(task: $task);
        }

        return $dtoArr;
    }

    function getAllTasks() {
        global $em;

        $taskList = $em->getRepository("Task")->findAll();

        $dtos = [];
        foreach ($taskList as $task) {
            $dtos[] = $task->toDto();
        }

        return $dtos;
    }

    function getTask($request) {
        global $em;

        $task = $em->find(Task::class, $request['id']);

        return $task->toDto();
    }

    function getUsers($request)
    {
        global $em;

        $userList = $em->find("Task", $request['task_id'])->users->getValues();

        foreach ($userList as $user){
            $user = $user->toDto();
        }

        return $userList;
    }

    function getUserTasks($request)
    {
        global $em;

        $taskList = $em->find(User::class, $request['user_id'])->tasks->getValues();

        $dtos = [];
        foreach ($taskList as $task){
            $dtos[] = $task->toDto();
        }

        return $dtos;
    }

    function getUserGridTasks($request)
    {
        global $em;

        $taskList = $em->getRepository(Task::class)->getUserGridTasks($request);

        return $taskList;
    }

    function updateTask($request)
    {

        global $em;

        $task = $em->find(Task::class, 366);
        $task->setDescription($request['description']);
        $task->updated_at = date('Y-m-d');
        $em->persist($task);
        $em->flush();

        return $task->toDto();

    }

    function updateTaskGroup($request)
    {
        global $em;

        $thisTask = $em->find(Task::class, $request['id']);
        $group = $thisTask->getSelfGroup();
        $newInGroup = [];

        if ($request['period_quantity'] < $thisTask->period_quantity) {
            $delArr = array_slice($group, $request['period_quantity'] - $thisTask->period_quantity);
            $group = array_slice($group, 0, $request['period_quantity']);
            foreach ($delArr as $delTask){
                $em->remove($delTask);
            }
        }
        if ($request['period_quantity'] > $thisTask->period_quantity) {
            for ($i = 0; $i < $request['period_quantity'] - $thisTask->period_quantity; $i++) {
                $task = new Task;
                $task->setDescription($request['description']);
                $task->setCompleted(0);
                $task->setGroupId($thisTask->group_id);
                $task->creator = $thisTask->creator;
                $task->created_at = date('Y-m-d');
                array_push($group, $task);
                array_push($newInGroup, $task);
            }
        }

        $group[0]->setPeriod($group[0]->do_from, $group[0]->do_to, $group[0]->period_days, count($group));
        $periods = $group[0]->getRepeatedPeriods();

        foreach ($group as $elementKey => $task){
            $task->setDescription($request['description']);
            $task->setPeriod($periods[$elementKey][0], $periods[$elementKey][1], $group[0]->period_days, count($group));
            $task->updated_at = date('Y-m-d');
            $em->persist($task);

        }

        foreach ($newInGroup as $elementKey => $task) {
            foreach ($group[0]->getUsers() as $user) {
                $task->addUser($user);
                //print_r($user->id);
            }
        }

        $em->flush();

        foreach ($group as $task){
            $task = $task->toDto();
        }

        return $group;
    }

    function completeTask($request)
    {
        global $em;
        $task=$em->find(Task::class,  $request['id']);
        $task->setCompleted(!$task->completed);
        $em->persist($task);
        $em->flush();
        return $task->toDto();
    }

    function addUser($request)
    {
        global $em;
        $user = $em->find(User::class, $request['user_id']);
        $task = $em->find(Task::class, $request['task_id']);
        $task->addUser($user);
        return $task->toDto();
    }

    function addUserGroup($request)
    {
        global $em;
        $user = $em->find(User::class, $request['user_id']);
        $task = $em->find(Task::class, $request['task_id']);
        $task->addUserGroup($user);
        return $task->toDto();
    }

    function unlinkUser($request)
    {
        global $em;
        $user = $em->find(User::class, $request['user_id']);
        $task = $em->find(Task::class, $request['task_id']);
        $task->unlinkUser($user);
        return $task->toDto();
    }

    function unlinkUserGroup($request)
    {
        global $em;
        $user = $em->find(User::class, $request['user_id']);
        $task = $em->find(Task::class, $request['task_id']);
        $task->unlinkUserGroup($user);
        return $task->toDto();
    }

    function deleteTask($request)
    {
        global $em;

        if ($request) {
            $this->id = (int)$request['id'];
        }

        $thisTask = $em->find('Task', $request['id']);

        if ($thisTask->group_id !== null){
            $group = $thisTask->getSelfGroup();

            if ($thisTask->id !== $group[0]->id){

                $foundPositionInGroup = false;
                $oldGroup = [];

                foreach ($group as $elementKey => $task) {

                    if (!$foundPositionInGroup) {
                        if ($task->id !== $thisTask->id) $oldGroup[] = $task;
                        unset($group[$elementKey]);
                    }

                    if ($task->id === $thisTask->id) {
                        $foundPositionInGroup = true;
                    }

                }

                $em->remove($thisTask);

                $taskForGroupId = new Task();
                $groupId = $taskForGroupId->getNewGroupId();

                foreach ($group as $elementKey => $task) {
                    $task->setGroupId($groupId);
                    $task->setPeriod($task->do_from, $task->do_to, $task->period_days, count($group));
                    $em->persist($task);
                }

                foreach ($oldGroup as $elementKey => $task) {
                    $task->setPeriod($task->do_from, $task->do_to, $task->period_days, count($oldGroup));
                    $em->persist($task);
                }

            } else {
                $em->remove($thisTask);
            }
            $em->flush();
        }

        foreach ($group as $task){
            $task = $task->toDto();
        }

        foreach ($oldGroup as $task){
            $task = $task->toDto();
        }

        return [$oldGroup, $group];
    }

}

class UserService
{
    function addUser($request)
    {
        global $em;
        $user = new User;
        $user->name = $request['name'];
        $em->persist($user);
        $em->flush();

        return $user->toDto();
    }

    function getAllUsers()
    {
        global $em;
        $userList = $em->createQueryBuilder()->select('u')
            ->from(User::class, 'u')
            ->getQuery()
            ->getResult();

        foreach ($userList as $user){
            $user = $user->toDto();
        }

        return $userList;

    }

    function addTask($request)
    {
        global $em;
        $user = $em->find(User::class, $request['user_id']);
        $task = $em->find(Task::class, $request['task_id']);
        $user->addTask($task);

    }

    function addTaskGroup($request)
    {
        global $em;
        $user = $em->find(User::class, $request['user_id']);
        $task = $em->find(Task::class, $request['task_id']);
        $user->adTaskrGroup($task);
    }

    function unlinkTask($request)
    {
        global $em;
        $user = $em->find(User::class, $request['user_id']);
        $task = $em->find(Task::class, $request['task_id']);
        $user->unlinkTask($task);
    }

    function unlinkTaskGroup($request)
    {
        global $em;
        $user = $em->find(User::class, $request['user_id']);
        $task = $em->find(Task::class, $request['task_id']);
        $user->unlinkTaskGroup($task);
    }

    function deleteUser($request)
    {
        global $em;
        $user = $em->find(User::class, $request['id']);
        $em->remove($user);
        $em->flush();
    }
}