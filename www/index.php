<?php

require_once './init.php';


//setcookie("name","valMEMue",time()+3600);
//$repo = new \App\Authentication\Repository\UserRepository($link);
//$repo->save(new \App\Authentication\User(5,'guest2',7072, null));
//var_dump($repo->findById(4));
$router = new \App\Routing\Router($twig);
try {
    $router->route();
} catch (Twig_Error_Loader $e) {
} catch (Twig_Error_Runtime $e) {
} catch (Twig_Error_Syntax $e) {
}

echo $_COOKIE["name"];
