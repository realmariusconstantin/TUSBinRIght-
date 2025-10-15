<?php

namespace App\Controllers;

use CodeIgniter\RESTful\ResourceController;

class User extends ResourceController
{
    protected $db;
    protected $format = 'json';

    public function __construct()
    {
        $this->db = \Config\Database::connect();
    }

    public function register()
    {
        $data = $this->request->getJSON(true);

        // Validate input using the "register" rule group from Validation.php
        if (!$this->validate('register')) {
            // Return backend validation errors in JSON
            return $this->response->setJSON([
                'status' => 'error',
                'errors' => $this->validator->getErrors()
            ])->setStatusCode(400);
        }

        // Check if email already exists
        $exists = $this->db->table('users')->where('email', $data['email'])->get()->getRow();
        if ($exists) {
            return $this->response->setJSON([
                'status' => 'error',
                'errors' => ['email' => 'Email already registered']
            ])->setStatusCode(400);
        }

        // Insert new user
        $this->db->table('users')->insert([
            'name' => $data['name'],
            'email' => $data['email'],
            'password_hash' => password_hash($data['password'], PASSWORD_DEFAULT),
        ]);

        return $this->response->setJSON([
            'status' => 'success',
            'message' => 'User registered successfully'
        ])->setStatusCode(201);
    }


    public function login()
    {
        $data = $this->request->getJSON(true);

        if (!$this->validate('login')) {
            return $this->response->setJSON([
                'status' => 'error',
                'errors' => $this->validator->getErrors()
            ])->setStatusCode(400);
        }

        $user = $this->db->table('users')->where('email', $data['email'])->get()->getRow();

        if (!$user || !password_verify($data['password'], $user->password_hash)) {
            return $this->response->setJSON([
                'status' => 'error',
                'errors' => ['email' => 'Invalid email or password']
            ])->setStatusCode(401);
        }

        return $this->response->setJSON([
            'status' => 'success',
            'message' => 'Login successful',
            'user' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
            ]
        ]);
    }
}



