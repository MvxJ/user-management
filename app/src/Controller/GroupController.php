<?php

namespace App\Controller;

use App\Repository\MysqlGroupRepository;
use App\Service\GroupService;

class GroupController
{
    private GroupService $groupService;

    public function __construct(MysqlGroupRepository $mysqlGroupRepository)
    {
        $this->groupService = new GroupService($mysqlGroupRepository);
    }

    public function index()
    {
        $groups = $this->groupService->getGroups();
        $response = [
            'status' => 'success',
            'results' => $groups
        ];

        echo json_encode($response);
    }

    public function getGroup(int $groupId)
    {
        try {
            $user = $this->groupService->getGroup($groupId);
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

    public function addGroup()
    {
        try {
            $data = json_decode(file_get_contents('php://input'), true);
            $group = $this->groupService->addGroup($data);

            http_response_code(200);
            $response['status'] = 'success';
            $response['message'] = 'Group created successfully';
            $response['results'] = ['id' => $group->getId()];

            echo json_encode($response);
        } catch (\Exception $exception) {
            http_response_code(500);
            $response['status'] = 'error';
            $response['message'] = $exception->getMessage();

            echo json_encode($response);
        }
    }

    public function editGroup(int $groupId)
    {
        try {
            $data = json_decode(file_get_contents('php://input'), true);
            $group = $this->groupService->editGroup($groupId, $data);

            http_response_code(200);
            $response['status'] = 'success';
            $response['message'] = 'Group updated successfully';
            $response['results'] = ['id' => $group->getId()];

            echo json_encode($response);
        } catch (\Exception $exception) {
            http_response_code(500);
            $response['status'] = 'error';
            $response['message'] = $exception->getMessage();

            echo json_encode($response);
        }
    }

    public function deleteGroup(int $groupId)
    {
        try {
            $this->groupService->deleteGroup($groupId);

            http_response_code(200);
            $response['status'] = 'success';
            $response['message'] = 'Successfully deleted group';

            echo json_encode($response);
        } catch (\Exception $exception) {
            http_response_code(500);
            $response['status'] = 'error';
            $response['message'] = $exception->getMessage();

            echo json_encode($response);
        }
    }
}