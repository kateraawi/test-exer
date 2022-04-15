<?php

    ini_set('display_errors', '1');
    ini_set('display_startup_errors', '1');
    error_reporting(E_ALL);

    require_once ('./services/dbconfig.php');
    require_once ('./models/User.php');

    $user = new User();
    $user->defineUser(1);
    print_r($user);
	echo "ggg";
