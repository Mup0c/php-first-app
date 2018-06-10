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
     * @return null|string
     */
    public function getPostalCode(UserInterface $user): ?string
    {
        return ($this->getData($user))['postal_code'];
    }

    /**
     * @param UserInterface $user
     * @return null|string
     */
    public function getPhone(UserInterface $user): ?string
    {
        return ($this->getData($user))['phone'];
    }

    /**
     * @param UserInterface $user
     * @return null|string
     */
    public function getCity(UserInterface $user): ?string
    {
        return ($this->getData($user))['city'];
    }

    /**
     * @param UserInterface $user
     * @param string $data
     */
    public function setPostalCode(UserInterface $user, string $data)
    {
        $this->setData($user, array('postal_code' => $data));
    }

    /**
     * @param UserInterface $user
     * @param string $data
     */
    public function setPhone(UserInterface $user, string $data)
    {
        $this->setData($user, array('phone' => $data));
    }

    /**
     * @param UserInterface $user
     * @param string $data
     */
    public function setCity(UserInterface $user, string $data)
    {
        $this->setData($user, array('city' => $data));
    }

    /**
     * @param UserInterface $user
     * @return array
     */
    public function getData(UserInterface $user): array
    {
        $stmt = $this->connection->prepare("SELECT * FROM user_profile as up WHERE user_id = ?");
        $stmt->bind_param('i',$user->getId());
        $stmt->execute();
        foreach ($stmt->get_result() as $row) {
            $stmt->close();
            $data = $row;
            unset($data['id'], $data['user_id']);
            return $data;
        }
    }

    /**
     * @param UserInterface $user
     * @param mixed $data
     */
    private function setData(UserInterface $user, $data)
    {
        unset($data['id'], $data['user_id']);
        $query = 'UPDATE user_profile SET ';
        $types = str_repeat('s', count($data)).'i';
        $query .= implode(' = ?,', array_keys($data));
        $query .= ' = ? WHERE user_id = ?';
        $stmt = $this->connection->prepare($query);
        var_dump($query);
        var_dump($types);
        foreach ($data as $value){
            $stmt->bind_param($types, $value, $user->getId());
            $stmt->execute();
            $stmt->close();
            return;
        }
    }
}