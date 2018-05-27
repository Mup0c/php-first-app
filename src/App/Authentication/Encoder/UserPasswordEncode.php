<?php
/**
 * Created by PhpStorm.
 * User: admin
 * Date: 12.05.2018
 * Time: 13:22
 */

namespace App\Authentication\Encoder;


class UserPasswordEncode implements UserPasswordEncoderInterface
{

    /**
     * Метод принимает чистый пароль и соль (опциональна) и возвращает в зашифрованном виде.
     *
     * @param string $rawPassword
     * @param null|string $salt
     * @return string
     */
    public function encodePassword(string $rawPassword, ?string $salt = null): string
    {
        return md5($rawPassword.$salt);
    }
}