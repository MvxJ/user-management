<?php

declare(strict_types=1);

namespace App\Service;

use App\Entity\Group;
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

    public function getGroup(int $id)
    {
        $group = $this->groupRepository->getGroupById($id);

        if (!$group) {
            return null;
        }

        return [
            'id' => $group->getId(),
            'name' => $group->getName(),
            'users' => $group->getUsers(),
        ];
    }

    public function addGroup(array $data): Group
    {
        $group = new Group();
        $group->setName($data['name']);

        $this->groupRepository->addGroup($group);

        return $group;
    }

    public function editGroup(int $id, array $data): Group
    {
        $group = new Group();
        $group->setId($id);
        $group->setName($data['name']);

        $this->groupRepository->updateGroup($group);

        return $group;
    }

    public function deleteGroup(int $id)
    {
        $this->groupRepository->deleteGroup($id);
    }
}