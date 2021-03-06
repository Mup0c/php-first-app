<?php

namespace App\Routing;

use App\Controller\Controller;

/**
 * Class Router
 * @package App\Routing
 */
class Router
{
    /**
     * @var string
     */
    private $link;

    /**
     * @var array
     */
    private $parsed_url;

    /**
     * @var Controller
     */
    private $controller;

    /**
     * Router constructor.
     * @param $twig
     * @param $con
     */
    public function __construct($twig, $con)
    {
        $this->link = "http://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";
        $this->parsed_url = parse_url($this->link);
        $this->controller = new Controller($twig, $con);
    }

    /**
     * @throws \Twig_Error_Loader
     * @throws \Twig_Error_Runtime
     * @throws \Twig_Error_Syntax
     */
    public function route()
    {
        $path = $this->parsed_url["path"];
        switch ($path) {
            case '/':
                $this->controller->homeAction();
                return;
            case '/signIn':
                $this->controller->signInAction();
                return;
            case '/signUp':
                $this->controller->signUpAction();
                return;
            case '/logout':
                $this->controller->logoutAction();
                return;
            case '/profile':
                $this->controller->profileAction();
                return;
            case preg_match('#^/API/([^/]*$)#', $path) ? $path : !$path:
                $this->controller->apiAction(explode('/', $path)[2]);
                return;
            default:
                $this->controller->notFoundAction();
                return;
        }
    }
}