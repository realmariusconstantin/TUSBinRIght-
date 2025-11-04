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
        $data = json_decode($this->request->getBody(), true);

        if (empty($data['id'])) {
            return $this->fail('Missing user ID', 400);
        }

        if (empty($data['user']) || empty($data['email']) || empty($data['user_type_id'])) {
            return $this->fail('Missing required fields', 400);
        }

        try {
            // Fetch current user
            $user = $this->model->getUserById($data['id']);
            if (!$user) {
                return $this->fail('User not found', 404);
            }

            // Update user directly
            $db = \Config\Database::connect();
            $result = $db->table('users')->where('id', $data['id'])->update([
                'name' => $data['user'],
                'email' => $data['email'],
                'user_type_id' => $data['user_type_id']
            ]);

            if ($result) {
                return $this->respond([
                    'status' => 'success',
                    'message' => 'User updated successfully'
                ]);
            } else {
                return $this->fail('Failed to update user', 500);
            }
        } catch (\Exception $e) {
            log_message('error', 'User update failed: ' . $e->getMessage());
            return $this->fail('Error updating user: ' . $e->getMessage(), 500);
        }
    }

    // POST /admin/users/delete
    public function deleteUser()
    {
        $data = json_decode($this->request->getBody(), true);

        if (empty($data['id'])) {
            return $this->fail('Missing user ID', 400);
        }

        try {
            $db = \Config\Database::connect();
            $result = $db->table('users')->where('id', $data['id'])->delete();

            if ($result) {
                return $this->respond([
                    'status' => 'success',
                    'message' => 'User deleted successfully'
                ]);
            } else {
                return $this->fail('Failed to delete user', 500);
            }
        } catch (\Exception $e) {
            log_message('error', 'User delete failed: ' . $e->getMessage());
            return $this->fail('Error deleting user: ' . $e->getMessage(), 500);
        }
    }
}
