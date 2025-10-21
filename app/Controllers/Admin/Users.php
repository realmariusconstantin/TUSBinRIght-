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
        $db = \Config\Database::connect();
        $query = $db->query('CALL GetAllUsers()');
        $users = $query->getResultArray();

        return $this->response->setJSON([
            'status' => 'success',
            'users' => $users
        ]);
    }

    // POST /admin/users/update
    public function updateUser()
    {
        $data = $this->request->getJSON(true);
        $db = \Config\Database::connect();

        if (empty($data['id'])) {
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'Missing user ID'
            ])->setStatusCode(400);
        }

        $user = $db->table('users')->where('id', $data['id'])->get()->getRowArray();
        if (!$user) {
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'User not found'
            ])->setStatusCode(404);
        }

        $res = $db->query('CALL UpdateUser(?, ?, ?, ?, ?)', [
            $data['id'],
            $data['user'] ?? $user['name'],
            $data['email'] ?? $user['email'],
            $user['password_hash'],
            $data['user_type_id'] ?? $user['user_type_id']
        ])->getRowArray();

        return $this->response->setJSON($res);
    }

    // POST /admin/users/delete
    public function deleteUser()
    {
        $data = $this->request->getJSON(true);
        $db = \Config\Database::connect();

        if (empty($data['id'])) {
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'Missing user ID'
            ])->setStatusCode(400);
        }

        $res = $db->query('CALL DeleteUser(?)', [$data['id']])->getRowArray();

        return $this->response->setJSON($res);
    }
}