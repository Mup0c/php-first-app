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
use App\Authentication\Repository\UserDataRepository;
use App\Authentication\Repository\UserDataRepositoryInterface;
use App\Authentication\Repository\UserRepository;
use App\Authentication\Repository\UserRepositoryInterface;
use App\Authentication\Service\AuthenticationServiceInterface;
use App\Authentication\Service\AuthenticationService;
use App\Authentication\UserTokenInterface;
use App\Authentication\User;

class Controller
{
    /**
     * @var \Twig_Environment
     */
    private $twig;

    /**
     * @var UserRepositoryInterface
     */
    private $user_repo;

    /**
     * @var UserDataRepositoryInterface
     */
    private $user_data_repo;

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
     * @param $connection
     */
    public function __construct($twig, $connection)
    {
        $this->twig = $twig;
        $this->user_repo = new UserRepository($connection);
        $this->user_data_repo = new UserDataRepository($connection);
        $this->passEncode = new UserPasswordEncode();
        $this->authService = new AuthenticationService($this->user_repo);
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
            echo('<h2> Неверный логин или пароль!</h2>');

        }

        $template = $this->twig->load('signIn.html.twig');
        $template->display();
    }

    /**
     * @throws \Twig_Error_Loader
     * @throws \Twig_Error_Runtime
     * @throws \Twig_Error_Syntax
     */
    public function signUpAction()
    {
        if (!$this->userToken->isAnonymous()) {
            header("Location: /");
            return;
        }
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $login = $_POST['login'];
            $password = $this->passEncode->encodePassword($_POST['password']);
            if (!empty($this->user_repo->findByLogin($login))) {
                echo('<h2> Логин уже занят!</h2>');
            } else {
                $this->user_repo->save(new User(null, $login, $password, null));
                $token = $this->authService->authenticate_by_password($login, $password);
                if (!$token->isAnonymous()) {
                    $credentials = $this->authService->generateCredentials($token->getUser());
                    setcookie('auth_cookie', $credentials, time() + 300);
                    header("Location: /");
                    return;
                }
            }
        }

        $template = $this->twig->load('signUp.html.twig');
        $template->display();

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
            var_dump($this->user_data_repo->getPostalCode($this->userToken->getUser()));
            echo('<h1> Дороу, '.$this->userToken->getUser()->getLogin());
            echo('</h1><br><a href="/logout">Выход</a>');
        } else {
            echo('<a href="/signIn">Вход</a>');
        }
    }
}