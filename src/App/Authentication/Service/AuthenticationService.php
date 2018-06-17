<?php
/**
 * Created by PhpStorm.
 * User: admin
 * Date: 25.05.2018
 * Time: 1:00
 */

namespace App\Authentication\Service;


use App\Authentication\Repository\UserRepositoryInterface;
use App\Authentication\UserInterface;
use App\Authentication\UserToken;
use App\Authentication\UserTokenInterface;

/**
 * Class AuthenticationService
 * @package App\Authentication\Service
 *
 * Тесты!
 */
class AuthenticationService implements AuthenticationServiceInterface
{
    /**
     * @var UserRepositoryInterface
     */
    private $repo;

    /**
     * AuthenticationService constructor.
     * @param UserRepositoryInterface $repo
     */
    public function __construct(UserRepositoryInterface $repo)
    {
        $this->repo = $repo;
    }

    /**
     * Метод аутентифицирует пользователя на основании authentication credentials запроса
     *
     * @param mixed $credentials
     * @return UserTokenInterface
     */
    public function authenticate($credentials)
    {
        if (empty($credentials)) {
            return UserToken::anonymous();
        }

        $data = explode('/',$credentials, 2);
        if (count($data) !== 2) {
            return UserToken::anonymous();
        }

        list($id, $password) = $data;
        if (empty($id) || empty($password)) {
            return UserToken::anonymous();
        }

        $user = $this->repo->findById($id);

        if ($user != null && $user->getPassword() == $password) {
            return new UserToken($user);
        }

        return UserToken::anonymous();
    }


    /**
     * Метод генерирует authentication credentials
     *
     * @param UserInterface $user
     * @return mixed
     */
    public function generateCredentials(UserInterface $user)
    {
        return ($user->getId()."/".$user->getPassword());
    }

    /**
     * Метод аутентифицирует пользователя на основании логина и пароля
     *
     * @param string $login
     * @param string $password
     * @return UserTokenInterface
     */
    public function authenticate_by_password($login, $password)
    {
        $user = $this->repo->findByLogin($login);
        // Общий метод
        if ($user != null && $user->getPassword() == $password) {
            return new UserToken($user);
        }

        return UserToken::anonymous();
    }
}