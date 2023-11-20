<?php

declare(strict_types=1);

namespace App\Repository;

use App\Entity\Group;

interface GroupRepositoryInterface
{
    public function getAllGroups(): array;

    public function getGroupById(int $id): ?Group;

    public function updateGroup(Group $group): void;

    public function deleteGroup(int $id): void;

    public function addGroup(Group $group): void;
}