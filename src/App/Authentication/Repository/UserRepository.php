<?php
/**
 * Created by PhpStorm.
 * User: admin
 * Date: 12.05.2018
 * Time: 12:19
 */

namespace App\Authentication\Repository;

use App\Authentication\User;
use App\Authentication\UserInterface;

class UserRepository implements UserRepositoryInterface
{

    /**
     * @var \mysqli
     */
    private $connection;

    /**
     * UserRepository constructor.
     * @param \mysqli $connection
     */
    public function __construct(\mysqli $connection)
    {
        $this->connection = $connection;
    }

    /**
     * Метод ищет пользователя по индентификатору, возвращает UserInterface если пользователь существует, иначе null
     *
     * @param int $id
     * @return UserInterface|null
     */
    public function findById(int $id): ?UserInterface
    {
        $stmt = $this->connection->prepare("SELECT id, login, password, salt FROM users WHERE id = ?");
        $stmt->bind_param('i', $id);
        $stmt->execute();

        $user = null;
        foreach ($stmt->get_result() as $row) {
            $user = new User($row['id'], $row['login'], $row['password'], $row['salt']);
        }

        $stmt->close();
        return $user;
    }

    /**
     * Метод ищет пользователя по login, возвращает UserInterface если пользователь существует, иначе null
     *
     * @param string $login
     * @return UserInterface|null
     */
    public function findByLogin(string $login): ?UserInterface
    {
        $stmt = $this->connection->prepare("SELECT id, login, password, salt FROM users WHERE login = ?");
        $stmt->bind_param('s', $login);
        $stmt->execute();

        foreach ($stmt->get_result() as $row) {
            $stmt->close();
            return new User($row['id'], $row['login'], $row['password'], $row['salt']);
        }

        return null;
    }

    /**
     * Метод сохраняет пользоваля в хранилище
     *
     * @param UserInterface $user
     */
    public function save(UserInterface $user)
    {
        //begin_transaction commit
        $stmt = $this->connection->prepare("INSERT INTO users (login, password, salt) VALUES (?, ?, ?)");
        $stmt->bind_param('sss',$user->getLogin(),$user->getPassword(), $user->getSalt());
        $stmt->execute();
        $saved_user_id = $this->connection->insert_id;
        $stmt = $this->connection->prepare("INSERT INTO user_profile (user_id) VALUES (?)");
        $stmt->bind_param('i',$saved_user_id);
        $stmt->execute();
        $stmt->close();
    }
}