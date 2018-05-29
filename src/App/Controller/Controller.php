<?php
/**
 * Created by PhpStorm.
 * User: admin
 * Date: 25.05.2018
 * Time: 11:12
 */

namespace App\Controller;


use App\Authentication\Encoder\UserPasswordEncode;
use App\Authentication\Encoder\UserPasswordEncoderInterface;
use App\Authentication\Repository\UserRepositoryInterface;
use App\Authentication\Service\AuthenticationServiceInterface;
use App\Authentication\Service\AuthenticationService;
use App\Authentication\UserTokenInterface;

class Controller
{
    /**
     * @var \Twig_Environment
     */
    private $twig;

    /**
     * @var UserRepositoryInterface
     */
    private $repo;

    /**
     * @var AuthenticationServiceInterface
     */
    private $authService;

    /**
     * @var UserTokenInterface
     */
    private $userToken;

    /**
     * @var UserPasswordEncoderInterface
     */
    private $passEncode;

    /**
     * Controller constructor.
     * @param $twig
     * @param $repo
     */
    public function __construct($twig, $repo)
    {
        $this->twig = $twig;
        $this->repo = $repo;
        $this->passEncode = new UserPasswordEncode();
        $this->authService = new AuthenticationService($this->repo);
        $this->userToken = $this->authService->authenticate($_COOKIE['auth_cookie']);
    }

    /**
     * @throws \Twig_Error_Loader
     * @throws \Twig_Error_Runtime
     * @throws \Twig_Error_Syntax
     */
    public function signInAction()
    {
        if (!$this->userToken->isAnonymous()) {
            header("Location: /");
            return;
        }
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $login = $_POST['login'];
            $password = $this->passEncode->encodePassword($_POST['password']);
            $token = $this->authService->authenticate_by_password($login, $password);
            if (!$token->isAnonymous()){
                $credentials = $this->authService->generateCredentials($token->getUser());
                setcookie('auth_cookie',$credentials,time() + 300);
                header("Location: /");
                return;
            }
        }

        $template = $this->twig->load('signIn.html.twig');
        $template->display();
    }

    public function signUpAction()
    {
        echo("регистрация");

    }

    public function logoutAction()
    {
        setcookie('auth_cookie',false,time() - 3600);
        header("Location: /");
        return;
    }

    public function notFoundAction()
    {
        echo("404");

    }

    public function HomeAction()
    {
        if (!$this->userToken->isAnonymous()){
            echo('Дороу, '.$this->userToken->getUser()->getLogin());
        } else {
            echo('go signin');
        }
    }
}