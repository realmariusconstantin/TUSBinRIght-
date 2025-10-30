<?php

namespace App\Controllers\Admin;

use CodeIgniter\RESTful\ResourceController;
use App\Models\UserModel;

class Users extends ResourceController
{
    protected $model;
    protected $format = 'json';

    public function __construct()
    {
        $this->model = new UserModel();
    }

    // GET /admin/users
    public function getUsers()
    {
        $users = $this->model->getAllUsers();

        return $this->response->setJSON([
            'status' => 'success',
            'users' => $users
        ]);
    }

    // POST /admin/users/update
    public function updateUser()
    {
        $data = $this->request->getJSON(true);

        if (empty($data['id'])) {
            return $this->fail('Missing user ID', 400);
        }

        // Fetch current user
        $user = $this->model->getUserById($data['id']);
        if (!$user) {
            return $this->fail('User not found', 404);
        }

        $res = $this->model->updateUser($user['id'], $data['user'], $data['email'], $user['password_hash'], $data['user_type_id']);

        return $this->respond($res);
    }

    // POST /admin/users/delete
    public function deleteUser()
    {
        $data = $this->request->getJSON(true);

        if (empty($data['id'])) {
            return $this->fail('Missing user ID', 400);
        }

        $res = $this->model->deleteUser($data['id']);

        return $this->respond($res);
    }
}
