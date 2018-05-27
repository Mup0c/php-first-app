<?php
/**
 * Created by PhpStorm.
 * User: admin
 * Date: 25.05.2018
 * Time: 11:12
 */

namespace App\Controller;


class Controller
{
    /**
     * @var \Twig_Environment
     */
    private $twig;

    /**
     * Controller constructor.
     * @param $twig
     */
    public function __construct($twig)
    {
        $this->twig = $twig;
    }

    /**
     * @throws \Twig_Error_Loader
     * @throws \Twig_Error_Runtime
     * @throws \Twig_Error_Syntax
     */
    public function signInAction()
    {
        $template = $this->twig->load('signIn.html.twig');
        $template->display();
    }

    public function signUpAction()
    {
        echo("регистрация");

    }

    public function logoutAction()
    {
        echo("выход");

    }

    public function notFoundAction()
    {
        echo("404");

    }
}