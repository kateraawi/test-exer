<?php

require_once "vendor/autoload.php";

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\ORMSetup;

$paths = array(__DIR__ . "/models", __DIR__ . "/repository");
$isDevMode = true;

// the connection configuration
$dbParams = array(
    'driver' => 'pdo_mysql',
    'user' => 'root',
    'password' => '',
    'dbname' => 'test-exer',
);

$config = ORMSetup::createAnnotationMetadataConfiguration($paths, $isDevMode, __DIR__."/proxies");
//$config->setProxyDir(__DIR__.'/proxies');
//$config->setProxyNamespace('TestExer\Proxies');
$config->addEntityNamespace('', 'TestExer');
$em = EntityManager::create($dbParams, $config);