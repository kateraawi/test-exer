<?php

    ini_set('display_errors', '1');
    ini_set('display_startup_errors', '1');
    error_reporting(E_ALL);

    require_once (__DIR__ . "/services/dbconfig.php");
    require_once (__DIR__ . '/models/User.php');

    //$user = new User(null, 'Аааа А. А.');
    //$user->addSelf();

    //$task = new Task(null, 'Задача 9', '2023-04-05','2023-04-06', 5, 4, 0);
    $task = new Task(267);
    $task->dbConstruct();
    print_r($task);
    //$task->description = 'safdsf';
    //$task->updateSelf();
    //$task->dbConstruct();
    //$task->period_quantity = 4;
    //$task->updateSelfGroup();
    //$task->addUser(1);
    //$task->addUser(6);
    //$task->getRepeatedPeriods();
    //$task->deleteSelf();
    //$task->addSelfGroup();
    //print_r($task);
    //$task->users = [1, 5, 7];
    //$task->description = "Задание №1";
    //$task->updateSelf();
    //print_r($task->getAll());

