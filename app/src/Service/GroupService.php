<?php

declare(strict_types=1);

namespace App\Service;

use App\Entity\Group;
use App\Entity\User;
use App\Repository\GroupRepositoryInterface;

class GroupService
{
    private GroupRepositoryInterface $groupRepository;

    public function __construct(GroupRepositoryInterface $groupRepository)
    {
        $this->groupRepository = $groupRepository;
    }

    public function getGroups(): array
    {
        $groupsArray = [];
        $groups = $this->groupRepository->getAllGroups();

        /** @var Group $group */
        foreach ($groups as $group) {
            $groupsArray[] = [
                'id' => $group->getId(),
                'name' => $group->getName()
            ];
        }

        return $groupsArray;
    }

    public function getGroup(int $id): ?array
    {
        $group = $this->groupRepository->getGroupWithUsers($id);

        if (!$group) {
            return null;
        }

        $users = [];
        /** @var User $user */
        foreach ($group->getUsers() as $user) {
            $users[] = [
                'id' => $user->getId(),
                'name' => $user->getName(),
                'surname' => $user->getSurname()
            ];
        }

        return [
            'id' => $group->getId(),
            'name' => $group->getName(),
            'users' => $users,
        ];
    }

    public function addGroup(array $data): Group
    {
        $this->checkGroupData($data);

        $group = new Group();
        $group->setName($data['name']);

        foreach ($data['users'] as $userId) {
            $userObj = new User();
            $userObj->setId((int)$userId);

            $group->addUser($userObj);
        }

        $this->groupRepository->addGroup($group);

        return $group;
    }

    public function editGroup(int $id, array $data): Group
    {
        $this->checkGroupData($data);

        $group = new Group();
        $group->setId($id);
        $group->setName($data['name']);


        foreach ($data['users'] as $userId) {
            $userObj = new User();
            $userObj->setId((int)$userId);

            $group->addUser($userObj);
        }

        $this->groupRepository->updateGroup($group);

        return $group;
    }

    public function deleteGroup(int $id)
    {
        $this->groupRepository->deleteGroup($id);
    }

    private function checkGroupData(array $data): void
    {
        if (!array_key_exists('name', $data) || strlen($data['name']) === 0) {
            throw new \Exception('Name is required');
        }
    }
}