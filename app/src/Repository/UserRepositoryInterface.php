<?php

declare(strict_types=1);

namespace App\Repository;

use App\Entity\User;

interface UserRepositoryInterface
{
    public function getAllUsers(): array;

    public function getUserById(int $id): ?User;

    public function addUser(User $user): void;

    public function updateUser(User $user): void;

    public function deleteUser(int $id): void;
}