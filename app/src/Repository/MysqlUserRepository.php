<?php

declare(strict_types=1);

namespace App\Repository;

use App\Database\DatabaseConnectionFactory;
use App\Entity\Group;
use App\Entity\User;
use PDO;

class MysqlUserRepository implements UserRepositoryInterface
{
    private ?PDO $pdo;

    public function __construct()
    {
        $this->pdo = DatabaseConnectionFactory::getConnection();
    }
    public function getAllUsers(): array
    {
        $stmt = $this->pdo->query("SELECT * FROM users");

        return $stmt->fetchAll(PDO::FETCH_CLASS, User::class);
    }

    public function getUserById(int $id): ?User
    {
        $stmt = $this->pdo->prepare("SELECT * FROM users WHERE id = :id");
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
        $stmt->setFetchMode(PDO::FETCH_CLASS, User::class);

        return $stmt->fetch();
    }

    public function addUser(User $user): void
    {
        $stmt = $this->pdo->prepare("INSERT INTO users (username, password, name, surname, birth_date) 
                                     VALUES (:username, :password, :name, :surname, :birth_date)");

        $stmt->execute([
            ':username' => $user->getUsername(),
            ':password' => $user->getPassword(),
            ':name' => $user->getName(),
            ':surname' => $user->getSurname(),
            ':birth_date' => $user->getBirthDate()->format('Y-m-d'),
        ]);

        $user->setId((int)$this->pdo->lastInsertId());
        $this->updateUserGroups($user);
    }

    public function updateUser(User $user): void
    {
        $stmt = $this->pdo->prepare("UPDATE users SET username = :username, password = :password, 
                                     name = :name, surname = :surname, birth_date = :birth_date 
                                     WHERE id = :id");

        $stmt->execute([
            ':id' => $user->getId(),
            ':username' => $user->getUsername(),
            ':password' => $user->getPassword(),
            ':name' => $user->getName(),
            ':surname' => $user->getSurname(),
            ':birth_date' => $user->getBirthDate()->format('Y-m-d'),
        ]);

        $this->updateUserGroups($user);
    }

    public function deleteUser(int $id): void
    {
        $stmt = $this->pdo->prepare("DELETE FROM users WHERE id = :id");
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);

        $this->deleteUserFromGroups($id);

        $stmt->execute();
    }

    public function getUserWithGroups(int $id): ?User
    {
        $stmt = $this->pdo->prepare("SELECT * FROM users WHERE id = :id");
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
        $stmt->setFetchMode(PDO::FETCH_CLASS, User::class);

        /** @var User $user */
        $user = $stmt->fetch();
        $user->setBirthDate($user->birth_date);

        if ($user) {
            $groups = $this->getUserGroups($user->getId());

            /** @var Group $group */
            foreach ($groups as $group) {
                $user->addGroup($group);
            }
        }

        return $user;
    }


    private function deleteUserFromGroups(int $userId): void
    {
        $stmt = $this->pdo->prepare("DELETE FROM user_group WHERE user_id = :user_id");
        $stmt->execute([':user_id' => $userId]);
    }

    private function updateUserGroups(User $user): void
    {
        $stmtDelete = $this->pdo->prepare("DELETE FROM user_group WHERE user_id = :user_id");
        $stmtDelete->execute([':user_id' => $user->getId()]);

        /** @var Group $group */
        foreach ($user->getGroups() as $group) {
            $stmtInsert = $this->pdo->prepare(
                "INSERT INTO user_group (user_id, group_id) VALUES (:user_id, :group_id)"
            );
            $stmtInsert->execute([
                ':user_id' => $user->getId(),
                ':group_id' => $group->getId(),
            ]);
        }
    }

    private function getUserGroups(int $userId): array
    {
        $stmt = $this->pdo->prepare("SELECT g.* FROM groups g JOIN user_group ug ON g.id = ug.group_id WHERE ug.user_id = :userId");
        $stmt->bindParam(':userId', $userId, PDO::PARAM_INT);
        $stmt->execute();
        $stmt->setFetchMode(PDO::FETCH_CLASS | PDO::FETCH_PROPS_LATE, Group::class);

        $groups = $stmt->fetchAll();

        return $groups;
    }
}