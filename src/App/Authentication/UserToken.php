<?php
/**
 * Created by PhpStorm.
 * User: admin
 * Date: 25.05.2018
 * Time: 1:04
 */

namespace App\Authentication;


class UserToken implements UserTokenInterface
{

    /**
     * @var UserInterface|null
     */
    private $user;

    /**
     * UserToken constructor.
     * @param UserInterface|null $user
     */
    public function __construct(?UserInterface $user)
    {
        $this->user = $user;
    }

    /**
     * Метод возвращает соответствующего юзера, если он есть.
     *
     * @return UserInterface|null
     */
    public function getUser(): ?UserInterface
    {
        return ($this->user);
    }

    /**
     * Метод возращает true, если запрос пришел от анонима, иначе false
     *
     * @return bool
     */
    public function isAnonymous()
    {
        if ($this->user == null){
            return true;
        }
        else{
            return false;
        }
    }
}