<?php

require_once __DIR__ . '/../vendor/autoload.php';

mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

$connection = mysqli_connect("mysql", "app", "app", "app");
$loader = new Twig_Loader_Filesystem('../templates');
$twig = new Twig_Environment($loader, array(
    'auto_reload' => true,
));
