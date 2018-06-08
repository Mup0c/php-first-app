<?php
/**
 * Created by PhpStorm.
 * User: admin
 * Date: 08.06.2018
 * Time: 1:23
 */

namespace App\Authentication\Repository;

use App\Authentication\UserInterface;

class UserDataRepository implements UserDataRepositoryInterface
{

    /**
     * @var \mysqli
     */
    private $connection;

    /**
     * UserDataRepository constructor.
     * @param \mysqli $connection
     */
    public function __construct(\mysqli $connection)
    {
        $this->connection = $connection;
    }

    /**
     * @param UserInterface $user
     * @return int|null
     */
    public function getPostalCode(UserInterface $user): ?int
    {
        return $this->getData($user, 'postal_code');
    }

    /**
     * @param UserInterface $user
     * @return int|null
     */
    public function getPhone(UserInterface $user): ?int
    {
        return $this->getData($user, 'phone');
    }

    /**
     * @param UserInterface $user
     * @return null|string
     */
    public function getCity(UserInterface $user): ?string
    {
        return $this->getData($user, 'city');
    }

    /**
     * @param UserInterface $user
     * @param int $data
     */
    public function setPostalCode(UserInterface $user, int $data)
    {
        $this->setData($user, 'postal_code', $data);
    }

    /**
     * @param UserInterface $user
     * @param int $data
     */
    public function setPhone(UserInterface $user, int $data)
    {
        $this->setData($user, 'phone', $data);
    }

    /**
     * @param UserInterface $user
     * @param string $data
     */
    public function setCity(UserInterface $user, string $data)
    {
        $this->setData($user, 'city', $data);
    }

    /**
     * @param UserInterface $user
     * @param string $column
     * @return mixed
     */
    private function getData(UserInterface $user, string $column)
    {
        $stmt = $this->connection->prepare("SELECT * FROM 
                                                   users as u INNER JOIN user_profile as up ON u.id = up.user_id WHERE u.id = ?");
        $stmt->bind_param('i',$user->getId());
        $stmt->execute();

        foreach ($stmt->get_result() as $row) {
            $stmt->close();
            return $row[$column];
        }

        return null;
    }

    /**
     * @param UserInterface $user
     * @param string $column
     * @param mixed $data
     */
    private function setData(UserInterface $user, string $column, $data)
    {
        $stmt = $this->connection->prepare("UPDATE user_profile SET ".$column." = ? WHERE user_id = ?");
        $stmt->bind_param('si', $data, $user->getId());
        $stmt->execute();
        $stmt->close();
    }
}