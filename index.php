<?php

    ini_set('display_errors', '1');
    ini_set('display_startup_errors', '1');
    error_reporting(E_ALL);

    require_once (__DIR__ . "/services/dbconfig.php");
    require_once (__DIR__ . '/models/User.php');

    $user = new User(1);
    $user->dbConstruct();
    //print_r($user);

    $task = new Task(1);
    $task->dbConstruct();
    print_r($task);

