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
    private $auth_service;

    /**
     * @var UserTokenInterface
     */
    private $user_token;

    /**
     * @var UserPasswordEncoderInterface
     */
    private $pass_encode;

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
        $this->pass_encode = new UserPasswordEncode();
        $this->auth_service = new AuthenticationService($this->user_repo);
        $this->user_token = $this->auth_service->authenticate($_COOKIE['auth_cookie']);
    }

    /**
     * @throws \Twig_Error_Loader
     * @throws \Twig_Error_Runtime
     * @throws \Twig_Error_Syntax
     */
    public function signInAction()
    {
        if (!$this->user_token->isAnonymous()) {
            header("Location: /");
            return;
        }
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $login = $_POST['login'];
            $password = $this->pass_encode->encodePassword($_POST['password']);
            $token = $this->auth_service->authenticate_by_password($login, $password);
            if (!$token->isAnonymous()){
                $credentials = $this->auth_service->generateCredentials($token->getUser());
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
        if (!$this->user_token->isAnonymous()) {
            header("Location: /");
            return;
        }
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $login = $_POST['login'];
            $password = $this->pass_encode->encodePassword($_POST['password']);
            if (!empty($this->user_repo->findByLogin($login))) {
                echo('<h2> Логин уже занят!</h2>');
            } else {
                $this->user_repo->save(new User(null, $login, $password, null));
                $token = $this->auth_service->authenticate_by_password($login, $password);
                if (!$token->isAnonymous()) {
                    $credentials = $this->auth_service->generateCredentials($token->getUser());
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
        header("HTTP/1.0 404 Not Found");
        // ??  header('Status: 404 Not Found');

        return;
    }

    public function homeAction()
    {
        if (!$this->user_token->isAnonymous()){
            echo('<a href="/logout">Выход</a> <a href="/profile">Профиль</a>');
            echo('<h1><br> Дороу, '.$this->user_token->getUser()->getLogin());
        } else {
            echo('<a href="/signIn">Вход</a>');
        }
        return;
    }

    /**
     * @throws \Twig_Error_Loader
     * @throws \Twig_Error_Runtime
     * @throws \Twig_Error_Syntax
     */
    public function profileAction()
    {
        if ($this->user_token->isAnonymous()) {
            header("Location: /signIn");
            return;
        }
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {

            var_dump($_POST);
            $this->user_data_repo->setPhone($this->user_token->getUser(), $_POST['phone']);
            $this->user_data_repo->setPostalCode($this->user_token->getUser(), $_POST['postal_code']);
            $this->user_data_repo->setCity($this->user_token->getUser(), $_POST['city']);

            return;
        }
        if ($_GET["edit"] == 'true') {
            $template = $this->twig->load('profile_edit.html.twig');
            $template->display(array(
                'user_data' => $this->user_data_repo->getData($this->user_token->getUser())
            ));

            var_dump($this->user_data_repo->getData($this->user_token->getUser()));

            return;
        }
        $template = $this->twig->load('profile.html.twig');
        $template->display(array(
            'user_data' => $this->user_data_repo->getData($this->user_token->getUser())
        ));
        return;
    }

    /**
     * @param string $login
     */
    public function apiAction($login)
    {
        $user = $this->user_repo->findByLogin($login);
        if (!empty($user)) {
            echo json_encode($this->user_data_repo->getData($user));
        } else {
            $this->notFoundAction();
        }
    }
}