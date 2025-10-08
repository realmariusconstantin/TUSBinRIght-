<?php

namespace App\Controllers;

use CodeIgniter\Controller;
use CodeIgniter\API\ResponseTrait;

class User extends BaseController
{
    use ResponseTrait;

    protected $db;
    protected $validation;

    public function __construct()
    {
        helper(['form', 'url']);
        $this->db = \Config\Database::connect();
        $this->validation = \Config\Services::validation();
    }

    // 🟢 API: Register user (for Vue)
    public function register()
    {
        // Decode JSON request body
        $data = $this->request->getJSON(true);

        if (!$data) {
            return $this->fail('Invalid or missing JSON data', 400);
        }

        // Simple backend validation (you can improve rules in Validation.php)
        $rules = [
            'username' => 'required|min_length[3]',
            'email' => 'required|valid_email',
            'password' => 'required|min_length[6]',
            'confirmPassword' => 'required|matches[password]'
        ];

        if (!$this->validate($rules)) {
            return $this->failValidationErrors($this->validator->getErrors());
        }

        // Extract & hash password
        $forename = $data['username']; // adjust to your SQL SP parameters
        $surname = '';                 // optional if your DB procedure needs it
        $email = $data['email'];
        $password_hash = md5($data['password']); // keep your SP compatibility
        $user_type_id = 1;

        // Call stored procedure
        $query = $this->db->query(
            "CALL CreateUser(?, ?, ?, ?, ?)",
            [$forename, $surname, $email, $password_hash, $user_type_id]
        );

        $result = $query->getRow();
        $query->freeResult();

        if ($result && isset($result->status) && $result->status == 1) {
            return $this->respondCreated([
                'status' => 'success',
                'message' => $result->message ?? 'User registered successfully',
            ]);
        }

        return $this->fail($result->message ?? 'Failed to create user');
    }

    // 🟢 API: Login user (for Vue)
    public function login()
    {
        $data = $this->request->getJSON(true);

        if (!$data) {
            return $this->fail('Invalid or missing JSON data', 400);
        }

        $rules = [
            'email' => 'required|valid_email',
            'password' => 'required|min_length[6]',
        ];

        if (!$this->validate($rules)) {
            return $this->failValidationErrors($this->validator->getErrors());
        }

        $email = $data['email'];
        $password = md5($data['password']);

        $query = $this->db->query("CALL UserLogin(?, ?)", [$email, $password]);
        $user = $query->getRow();
        $query->freeResult();

        if ($user) {
            // optional: generate token if you want
            return $this->respond([
                'status' => 'success',
                'message' => 'Login successful',
                'user' => [
                    'id' => $user->id ?? null,
                    'email' => $user->email ?? $email,
                    'username' => $user->forename ?? $user->username ?? 'Unknown'
                ]
            ]);
        }

        return $this->failUnauthorized('Invalid email or password');
    }
}
