<?php

declare(strict_types=1);

namespace App\Service;

use App\Entity\User;
use App\Repository\UserRepositoryInterface;

class UserService
{
    private UserRepositoryInterface $userRepository;

    public function __construct(UserRepositoryInterface $userRepository)
    {
        $this->userRepository = $userRepository;
    }

    public function getUsers(): array
    {
        $usersArray = [];
        $users = $this->userRepository->getAllUsers();

        /** @var User $user */
        foreach ($users as $user) {
            $usersArray[] = [
                'id' => $user->getId(),
                'name' => $user->getName(),
                'surname' => $user->getSurname(),
                'username' => $user->getUsername()
            ];
        }

        return $usersArray;
    }

    public function getUser(int $id): ?array
    {
        $user = $this->userRepository->getUserById($id);

        if (!$user) {
            return null;
        }

        return [
            'id' => $user->getId(),
            'name' => $user->getName(),
            'surname' => $user->getSurname(),
            'username' => $user->getUsername(),
            'birthDate' => $user->getBirthDate(),
            'password' => $user->getPassword(),
            'groups' => $user->getGroups()
        ];
    }

    public function editUser(int $userId, array $data): User
    {
        $user = new User();
        $user->setId($userId);
        $user->setUsername($data['username']);
        $user->setPassword($data['password']);
        $user->setName($data['name']);
        $user->setSurname($data['surname']);
        $user->setBirthDate(new \DateTime($data['birthDate']));

        $this->userRepository->updateUser($user);

        return $user;
    }

    public function deleteUser(int $id): void
    {
        $this->userRepository->deleteUser($id);
    }

    public function addUser(array $data): User
    {
        $user = new User();
        $user->setUsername($data['username']);
        $user->setPassword($data['password']);
        $user->setName($data['name']);
        $user->setSurname($data['surname']);
        $user->setBirthDate(new \DateTime($data['birthDate']));

        $this->userRepository->addUser($user);

        return $user;
    }
}