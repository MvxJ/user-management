<?php

declare(strict_types=1);

namespace App\Repository;

use App\Database\DatabaseConnectionFactory;
use App\Entity\Group;
use App\Entity\User;
use PDO;

class MysqlGroupRepository implements GroupRepositoryInterface
{
    private ?PDO $pdo;

    /**
     * @throws \Exception
     */
    public function __construct()
    {
        $this->pdo = DatabaseConnectionFactory::getConnection();
    }

    public function getAllGroups(): array
    {
        $stmt = $this->pdo->query("SELECT * FROM groups");
        return $stmt->fetchAll(PDO::FETCH_CLASS, Group::class);
    }

    public function getGroupById(int $id): ?Group
    {
        $stmt = $this->pdo->prepare("SELECT * FROM groups WHERE id = :id");
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
        $stmt->setFetchMode(PDO::FETCH_CLASS, Group::class);

        return $stmt->fetch();
    }

    public function addGroup(Group $group): void
    {
        $stmt = $this->pdo->prepare("INSERT INTO groups (name) VALUES (:name)");
        $stmt->execute([':name' => $group->getName()]);

        $groupId = (int)$this->pdo->lastInsertId();
        $group->setId($groupId);

        $this->updateGroupUsers($group);
    }

    public function updateGroup(Group $group): void
    {
        $stmt = $this->pdo->prepare("UPDATE groups SET name = :name WHERE id = :id");
        $stmt->execute([
            ':id' => $group->getId(),
            ':name' => $group->getName(),
        ]);

        $this->updateGroupUsers($group);
    }

    public function deleteGroup(int $id): void
    {
        $stmt = $this->pdo->prepare("DELETE FROM groups WHERE id = :id");
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);

        $this->deleteGroupFromUsers($id);

        $stmt->execute();
    }

    public function getGroupWithUsers(int $id): ?Group
    {
        $stmt = $this->pdo->prepare("SELECT * FROM groups WHERE id = :id");
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
        $stmt->setFetchMode(PDO::FETCH_CLASS, Group::class);

        /** @var Group $group */
        $group = $stmt->fetch();

        if ($group) {
            $users = $this->getGroupUsers($group->getId());

            /** @var User $user */
            foreach ($users as $user) {
                $group->addUser($user);
            }
        }

        return $group;
    }

    private function deleteGroupFromUsers(int $groupId): void
    {
        $stmt = $this->pdo->prepare("DELETE FROM user_group WHERE group_id = :group_id");
        $stmt->execute([':group_id' => $groupId]);
    }

    private function updateGroupUsers(Group $group): void
    {
        $stmtDelete = $this->pdo->prepare("DELETE FROM user_group WHERE group_id = :group_id");
        $stmtDelete->execute([':group_id' => $group->getId()]);

        /** @var User $user */
        foreach ($group->getUsers() as $user) {
            $stmtInsert = $this->pdo->prepare(
                "INSERT INTO user_group (user_id, group_id) VALUES (:user_id, :group_id)"
            );
            $stmtInsert->execute([
                ':user_id' => $user->getId(),
                ':group_id' => $group->getId(),
            ]);
        }
    }

    private function getGroupUsers(int $groupId): array
    {
        $stmt = $this->pdo->prepare("SELECT u.* FROM users u JOIN user_group ug ON u.id = ug.user_id WHERE ug.group_id = :groupId");
        $stmt->bindParam(':groupId', $groupId, PDO::PARAM_INT);
        $stmt->execute();
        $stmt->setFetchMode(PDO::FETCH_CLASS | PDO::FETCH_PROPS_LATE, User::class);

        $users = $stmt->fetchAll();

        return $users;
    }
}