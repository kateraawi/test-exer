<?php

namespace TestExer\Controllers;

ini_set('display_errors', '1');
ini_set('display_startup_errors', '1');
error_reporting(E_ALL);

require_once(__DIR__ . '/../services/UserService.php');

use TestExer\Services\UserService as UserService;

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