<?php

require_once __DIR__ . '/../vendor/autoload.php';
require_once __DIR__ . '/twigController.php';

use Twig\Extra\CssInliner\CssInlinerExtension;

ini_set('display_errors', '1');
ini_set('display_startup_errors', '1');
error_reporting(E_ALL);

$loader = new \Twig\Loader\FilesystemLoader(__DIR__ . '/templates');
$twig = new \Twig\Environment($loader);
$twig->addExtension(new CssInlinerExtension());

global $em;

if ($_REQUEST){
    $template = $twig->load($_REQUEST['template'].'.html');

    $service = new TwigService();
    $templateMethod = $_REQUEST['template'];
    $data = $service->$templateMethod($_REQUEST);
    //var_dump($data);
    echo $template->render($data);
}