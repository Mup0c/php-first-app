<?php

namespace App\Routing;

use App\Controller\Controller;

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
     */
    public function __construct($twig)
    {
        $this->link = "http://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";
        $this->parsed_url = parse_url($this->link);
        $this->controller = new Controller($twig);
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
            case '/signIn':
                $this->controller->signInAction();
                return;
            case '/signUp':
                $this->controller->signUpAction();
                return;
            case '/logout':
                $this->controller->logoutAction();
                return;
            default:
                $this->controller->notFoundAction();
                return;
        }
    }
}