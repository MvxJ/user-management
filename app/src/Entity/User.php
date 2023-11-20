<?php

declare(strict_types=1);

namespace App\Entity;

class User
{
    private int $id;
    private string $username;
    private string $password;
    private string $name;
    private string $surname;
    private \DateTime $birthDate;
    private array $groups;

    public function __construct()
    {
        $this->groups = [];
    }

    public function setId(int $id): void
    {
        $this->id = $id;
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getUsername(): string
    {
        return $this->username;
    }

    public function setUsername(string $username): void
    {
        $this->username = $username;
    }

    public function getPassword(): string
    {
        return $this->password;
    }

    public function setPassword(string $password): void
    {
        $this->password = $password;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function setName(string $name): void
    {
        $this->name = $name;
    }

    public function getSurname(): string
    {
        return $this->surname;
    }

    public function setSurname(string $surname): void
    {
        $this->surname = $surname;
    }

    public function getBirthDate(): \DateTime
    {
        return $this->birthDate;
    }

    public function setBirthDate(\DateTime $birthDate): void
    {
        $this->birthDate = $birthDate;
    }

    public function addGroup(Group $group): void
    {
        $groupId = $group->getId();
        if (!isset($this->groups[$groupId])) {
            $this->groups[$groupId] = $group;
        }
    }

    public function removeGroup(Group $group): void
    {
        $groupId = $group->getId();
        if (isset($this->groups[$groupId])) {
            unset($this->groups[$groupId]);
        }
    }

    public function getGroups(): array
    {
        return array_values($this->groups);
    }
}