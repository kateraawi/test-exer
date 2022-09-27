<?php

require_once __DIR__ . '/../vendor/autoload.php';
require_once __DIR__ . '/twigController.php';
require_once __DIR__ . '/fpdfHTML.php';
require_once __DIR__ . '/fpdf.php';

//use Twig\Extra\CssInliner\CssInlinerExtension;

ini_set('display_errors', '1');
ini_set('display_startup_errors', '1');
error_reporting(E_ALL);

$loader = new \Twig\Loader\FilesystemLoader(__DIR__ . '/templates');
$twig = new \Twig\Environment($loader, );
//$twig->addExtension(new CssInlinerExtension());

global $em;

if ($_REQUEST){
    $template = $twig->load($_REQUEST['template'].'.html');

    $service = new TwigService();
    $templateMethod = $_REQUEST['template'];
    $data = $service->$templateMethod($_REQUEST);
    //var_dump($data);
    $pdf = new PDF_HTML();
    $pdf->addPage('horizontal');
    $pdf->addFont('DejaVuSerif', '', 'DejaVuSerif.php');
    // $pdf->addFont('DejaVuSerif-Bold', 'B', 'DejaVuSerif-Bold.php');
    $title = $template->renderBlock('title', $data);
    $pdf->SetTitle($title, true);
    $pdf->SetFont('DejaVuSerif', '',8);
    //$pdf->SetFont('DejaVuSerif', 'B');
    $html = $template->renderBlock('body', $data);
    $encoded = mb_convert_encoding($html, 'cp1251', 'UTF-8');
    $pdf->WriteHTML($encoded);
    $pdf->Output('', $title, true);

//    $pdf = new FPDF();
//    $pdf->addPage();
//    $pdf->SetTitle($template->renderBlock('title', $data));
//    $pdf->SetFont('Times');
//    $html = mb_convert_encoding($template->render($data), 'ISO-8859-1', 'UTF-8');
//    $pdf->Output();
   // echo $html;
}
