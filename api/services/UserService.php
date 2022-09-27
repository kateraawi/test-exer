<?php

namespace TestExer\Services;

ini_set('display_errors', '1');
ini_set('display_startup_errors', '1');
error_reporting(E_ALL);

require_once(__DIR__ . '/../models/User.php');

//use Doctrine\DBAL\Exception;
//use TestExer\Model\Task as Task;
use TestExer\Model\User as User;

class UserService
{
    function addUser($request)
    {
        global $em;
        $em->beginTransaction();
        try {
            //throw new \Exception(1);
            $user = new User;
            $user->name = $request['name'];
            $em->persist($user);
            $em->flush();
            $em->commit();
        } catch (\Exception $exception) {
            $em->rollback();
            http_response_code(500);
            return json_encode(['status' => 500, 'Error message' => $exception->getMessage()]);
        }
        return json_encode(['status' => 200, 'Error message' => '']);
    }

    function getUser($request)
    {
        global $em;

        try {
            $user = new User;
            $em->find(User::class, $request['id']);
            if ($user->id === null){
                throw new \Exception('fsdgdfgdfg', 404);
            }
            return $user->toDto();
        } catch (\Exception $e){
            header('HTTP/1.0 404 Not found', true, 404);
            return ['code'=>$e->getCode(), 'message'=> $e->getMessage()];
        }
    }

    function getAllUsers()
    {
        global $em;
//        $userList = $em->createQueryBuilder()->select('u')
//            ->from(User::class, 'u')
//            ->getQuery()
//            ->getResult();

        $userList = $em->getRepository(User::class)->findAll();

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
        $tasks = $em->getRepository(Task::class)->findBy(['creator' => $request['id']]);
        $user = $em->find(User::class, $request['id']);
        //$taskList = $user->created->getValues();
        foreach ($tasks as $task) {
            $em->remove($task);
        }
        $em->remove($user);
        $em->flush();
    }
}