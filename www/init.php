<?php

require_once __DIR__ . '/../vendor/autoload.php';

$link = mysqli_connect("mysql", "app", "app", "app");
$repo = new \App\Authentication\Repository\UserRepository($link);
$loader = new Twig_Loader_Filesystem('../templates');
$twig = new Twig_Environment($loader, array(
    'auto_reload' => true,
));
