<?php

declare(strict_types=1);

namespace App\Service;

use App\Entity\Group;
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
        $user = $this->userRepository->getUserWithGroups($id);

        if (!$user) {
            return null;
        }

        $groups = [];
        foreach ($user->getGroups() as $group) {
            $groups[] = [
                'id' => $group->getId(),
                'name' => $group->getName()
            ];
        }

        return [
            'id' => $user->getId(),
            'name' => $user->getName(),
            'surname' => $user->getSurname(),
            'username' => $user->getUsername(),
            'birthDate' => $user->getBirthDate(),
            'password' => $user->getPassword(),
            'groups' => $groups
        ];
    }

    public function editUser(int $userId, array $data): User
    {
        $userFromDbo = $this->userRepository->getUserById($userId);

        if (!$userFromDbo) {
            throw new \Exception('User not found');
        }

        $this->checkUserData($data, 'edit');

        $user = new User();
        $user->setId($userId);
        $user->setUsername($data['username']);
        $user->setName($data['name']);
        $user->setSurname($data['surname']);
        $user->setBirthDate($data['birthDate']);

        if (array_key_exists('newPassword', $data) && array_key_exists('oldPassword', $data)) {
            if (password_verify($data['oldPassword'], $userFromDbo->getPassword())) {
                $user->setPassword(password_hash($data['newPassword'], PASSWORD_DEFAULT));
            } else {
                throw new \Exception("Password doesn't match previous password.");
            }
        } else {
            $user->setPassword($userFromDbo->getPassword());
        }

        foreach ($data['groups'] as $groupId){
            $groupObj = new Group();
            $groupObj->setId((int)$groupId);

            $user->addGroup($groupObj);
        }

        $this->userRepository->updateUser($user);

        return $user;
    }

    public function deleteUser(int $id): void
    {
        $this->userRepository->deleteUser($id);
    }

    public function addUser(array $data): User
    {
        $this->checkUserData($data, 'add');

        $user = new User();
        $user->setUsername($data['username']);
        $user->setPassword(password_hash($data['password'], PASSWORD_DEFAULT));
        $user->setName($data['name']);
        $user->setSurname($data['surname']);
        $user->setBirthDate($data['birthDate']);

        foreach ($data['groups'] as $groupId){
            $groupObj = new Group();
            $groupObj->setId((int)$groupId);

            $user->addGroup($groupObj);
        }

        $this->userRepository->addUser($user);

        return $user;
    }

    private function checkUserData(array $data, string $action = 'edit'): void
    {
        if (
            !array_key_exists('name', $data) ||
            !array_key_exists('surname', $data) ||
            !array_key_exists('username', $data) ||
            !array_key_exists('birthDate', $data)
        ) {
            throw new \Exception('Bad form please fill all data.');
        }

        if (
            strlen($data['birthDate']) == 0 ||
            strlen($data['name']) == 0 ||
            strlen($data['surname']) == 0 ||
            strlen($data['birthDate']) == 0
        ) {
            throw new \Exception('Bad form please fill all data.');
        }

        if ($action === 'add' && (!array_key_exists('password', $data) || strlen($data['password']) == 0)) {
            throw new \Exception('Password is required.');
        }
    }
}