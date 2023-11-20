<?php

declare(strict_types=1);

namespace App\Entity;

class Group
{
    private int $id;
    private string $name;
    private array $users;

    public function __construct()
    {
        $this->users = [];
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function setId(int $id): void
    {
        $this->id = $id;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function setName(string $name): void
    {
        $this->name = $name;
    }

    public function getUsers(): array
    {
        return array_values($this->users);
    }

    public function addUser(User $user): void
    {
        $userId = $user->getId();

        if (!isset($this->users[$userId])) {
            $this->users[$userId] = $user;
        }
    }

    public function removeUser(User $user): void
    {
        $userId = $user->getId();
        if (isset($this->users[$userId])) {
            unset($this->users[$userId]);
        }
    }
}