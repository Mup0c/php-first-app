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
     * @return int|null
     */
    public function getPostalCode(UserInterface $user): ?int;

    /**
     * @param UserInterface $user
     * @return int|null
     */
    public function getPhone(UserInterface $user): ?int;

    /**
     * @param UserInterface $user
     * @return null|string
     */
    public function getCity(UserInterface $user): ?string;


}