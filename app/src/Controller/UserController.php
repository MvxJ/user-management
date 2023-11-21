<?php

declare(strict_types=1);

namespace App\Controller;

use App\Repository\MysqlUserRepository;
use App\Service\UserService;

class UserController
{
    private UserService $userService;

    public function __construct(MysqlUserRepository $userRepository)
    {
        $this->userService = new UserService($userRepository);
    }

    public function index()
    {
        $users = $this->userService->getUsers();
        $response = [
            'status' => 'success',
            'results' => $users
        ];

        echo json_encode($response);
    }

    public function getUser(int $userId)
    {
        try {
            $user = $this->userService->getUser($userId);
            $response  = [];

            if (!$user) {
                http_response_code(404);
                $response['status'] = 'not found';
                $response['results'] = null;
            } else {
                http_response_code(200);
                $response['status'] = 'success';
                $response['results'] = $user;
            }

            echo json_encode($response);
        } catch (\Exception $exception) {
            http_response_code(500);
            $response['status'] = 'error';
            $response['results'] = null;
            $response['message'] = $exception->getMessage();

            echo json_encode($response);
        }
    }

    public function addUser()
    {
        try {
            $data = json_decode(file_get_contents('php://input'), true);
            $user = $this->userService->addUser($data);

            http_response_code(201);
            $response['status'] = 'success';
            $response['message'] = 'User created successfully';
            $response['results'] = ['id' => $user->getId()];

            echo json_encode($response);
        } catch (\Exception $exception) {
            http_response_code(500);
            $response['status'] = 'error';
            $response['message'] = $exception->getMessage();

            echo json_encode($response);
        }
    }

    public function editUser(int $userId)
    {
        try {
            $data = json_decode(file_get_contents('php://input'), true);
            $user = $this->userService->editUser($userId, $data);

            http_response_code(200);
            $response['status'] = 'success';
            $response['message'] = 'User updated successfully';
            $response['results'] = ['id' => $user->getId()];

            echo json_encode($response);
        } catch (\Exception $exception) {
            http_response_code(500);
            $response['status'] = 'error';
            $response['message'] = $exception->getMessage();

            echo json_encode($response);
        }
    }

    public function deleteUser(int $userId)
    {
        try {
            $this->userService->deleteUser($userId);

            http_response_code(200);
            $response['status'] = 'success';
            $response['message'] = 'Successfully deleted user';

            echo json_encode($response);
        } catch (\Exception $exception) {
            http_response_code(500);
            $response['status'] = 'error';
            $response['message'] = $exception->getMessage();

            echo json_encode($response);
        }
    }
}