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
}