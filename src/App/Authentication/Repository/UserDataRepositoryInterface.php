<?php
/**
 * Created by PhpStorm.
 * User: admin
 * Date: 08.06.2018
 * Time: 1:24
 */

namespace App\Authentication\Repository;

use App\Authentication\UserInterface;

interface UserDataRepositoryInterface
{
    /**
     * @param UserInterface $user
     * @return null|string
     */
    public function getPostalCode(UserInterface $user): ?string;

    /**
     * @param UserInterface $user
     * @return null|string
     */
    public function getPhone(UserInterface $user): ?string;

    /**
     * @param UserInterface $user
     * @return null|string
     */
    public function getCity(UserInterface $user): ?string;

    /**
     * @param UserInterface $user
     * @param string $data
     */
    public function setPostalCode(UserInterface $user, string $data);


    /**
     * @param UserInterface $user
     * @param string $data
     */
    public function setPhone(UserInterface $user, string $data);


    /**
     * @param UserInterface $user
     * @param string $data
     */
    public function setCity(UserInterface $user, string $data);

}