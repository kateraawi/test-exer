<?php

    ini_set('display_errors', '1');
    ini_set('display_startup_errors', '1');
    error_reporting(E_ALL);

    require_once ('./services/dbconfig.php');
    require_once ('./models/User.php');

    $user = new User(1);
    $user->defineSelf();
    $user->expandSelf();
    print_r($user);

    $task = $user->tasks[0];
    $task->expandSelf();
    print_r($task);