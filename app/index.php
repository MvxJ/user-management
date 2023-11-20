<?php

require_once __DIR__ . '/vendor/autoload.php';

use App\Controller\UserController;
use App\Controller\GroupController;
use App\Repository\MysqlUserRepository;
use App\Repository\MysqlGroupRepository;

$loader = require __DIR__ . '/vendor/autoload.php';

$loader->addPsr4('App\\', __DIR__);

$requestPath = $_SERVER['REQUEST_URI'];
$method = $_SERVER['REQUEST_METHOD'];

if (strpos($requestPath, '/api/users') === 0) {
    $userRepository = new MysqlUserRepository();
    $userController = new UserController($userRepository);

    switch ($method) {
        case 'GET':
            if ($requestPath === '/api/users') {
                $userController->index();
            } else {
                $userId = intval(substr($requestPath, strlen('/api/users/')));
                $userController->getUser($userId);
            }
            break;

        case 'POST':
            $userController->addUser();
            break;

        case 'PUT':
            $userId = intval(substr($requestPath, strlen('/api/users/')));
            $userController->editUser($userId);
            break;

        case 'DELETE':
            $userId = intval(substr($requestPath, strlen('/api/users/')));
            $userController->deleteUser($userId);
            break;

        default:
            http_response_code(405);
            echo json_encode(['error' => 'Method not allowed']);
            break;
    }
} elseif (strpos($requestPath, '/api/groups') === 0) {
    $groupRepository = new MysqlGroupRepository();
    $groupController = new GroupController($groupRepository);

    switch ($method) {
        case 'GET':
            if ($requestPath === '/api/groups') {
                $groupController->index();
            } else {
                $userId = intval(substr($requestPath, strlen('/api/groups/')));
                $groupController->getGroup($userId);
            }
            break;

        case 'POST':
            $groupController->addGroup();
            break;

        case 'PUT':
            $userId = intval(substr($requestPath, strlen('/api/groups/')));
            $groupController->editGroup($userId);
            break;

        case 'DELETE':
            $userId = intval(substr($requestPath, strlen('/api/groups/')));
            $groupController->deleteGroup($userId);
            break;

        default:
            http_response_code(405);
            echo json_encode(['error' => 'Method not allowed']);
            break;
    }
} else {
    http_response_code(404);
    echo json_encode(['error' => 'Endpoint not found']);
}