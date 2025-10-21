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
        return $query->getRowArray();
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
        return $query->getRowArray();
    }

    // Login user using stored procedure
    public function loginUser($email, $password_hash)
    {
        $query = $this->db->query('CALL UserLogin(?, ?)', [$email, $password_hash]);
        return $query->getRowArray();
    }
}
