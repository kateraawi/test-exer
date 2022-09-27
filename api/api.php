<?php

require_once ('dbconfig.php');

/*
require_once (__DIR__.'/models/Task.php');
*/

require_once (__DIR__.'/models/User.php');
require_once (__DIR__.'/Controllers/TaskController.php');
require_once (__DIR__.'/Controllers/UserController.php');

//use TestExer\Controllers\TaskController as TaskController;
//use TestExer\Controllers\UserController as UserController;

ini_set('display_errors', '1');
ini_set('display_startup_errors', '1');
error_reporting(E_ALL);

header('Access-Control-Allow-Origin: http://localhost:1841');
header('Access-Control-Allow-Methods: GET, POST');
header("Access-Control-Allow-Headers: X-Requested-With");

header('Content-Type: application/json');

$b = "TestExer\Controllers\\" . $_REQUEST['act'];
$a = new $b;
$method = $_REQUEST['method'];
$result = $a->$method($_REQUEST);
echo json_encode($result, JSON_UNESCAPED_UNICODE);
