<?php

namespace App\Models;

use CodeIgniter\Model;

class UserModel extends Model
{
    protected $db;
    protected $table = 'users';
    protected $primaryKey = 'id';
    protected $returnType = 'array';
    protected $allowedFields = ['name', 'email', 'password_hash', 'user_type_id'];

    public function __construct()
    {
        parent::__construct();
        $this->db = \Config\Database::connect();
    }

    // Check if an email exists using stored procedure
    public function checkEmailExists($email)
    {
        $query = $this->db->query('CALL CheckEmailExists(?)', [$email]);
        $result = $query->getRowArray();
        $query->freeResult();
        return $result;
    }

    // Create a new user using stored procedure
    public function createUser($data)
    {
        $query = $this->db->query(
            'CALL CreateUser(?, ?, ?, ?)',
            [
                $data['name'],
                $data['email'],
                $data['password_hash'],
                $data['user_type_id']
            ]
        );
        $result = $query->getRowArray();
        $query->freeResult();
        return $result;
    }

    // Login user using stored procedure
    public function loginUser($email, $password_hash)
    {
        $query = $this->db->query('CALL UserLogin(?, ?)', [$email, $password_hash]);
        $result = $query->getRowArray();
        $query->freeResult();
        return $result;
    }

    // Get all users using stored procedure
    public function getAllUsers()
    {
        $query = $this->db->query('CALL GetAllUsers()');
        $result = $query->getResultArray();
        $query->freeResult();
        return $result;
    }

    // Get a specific user by ID using stored procedure
    public function getUserById($user_id)
    {
        $query = $this->db->query('CALL GetUserById(?)', [$user_id]);
        $result = $query->getRowArray();

        $query->freeResult();
        return $result;
    }

    // Update an existing user using stored procedure
    public function updateUser($id, $name, $email, $password_hash, $user_type_id)
    {
        $query = $this->db->query(
            'CALL UpdateUser(?, ?, ?, ?, ?)',
            [$id, $name, $email, $password_hash, $user_type_id]
        );
        $result = $query->getRowArray();
        $query->freeResult();
        return $result;
    }

    // Delete a user by ID using stored procedure
    public function deleteUser($user_id)
    {
        $query = $this->db->query('CALL DeleteUser(?)', [$user_id]);
        $result = $query->getRowArray();
        $query->freeResult();
        return $result;
    }

    // Get a count of the amount of users using stored procedure
    public function getTotalUsers()
    {
        $query = $this->db->query('CALL GetTotalUsers()');
        $result = $query->getRowArray();
        $query->freeResult();
        return $result['total_users'] ?? 0;
    }
}
