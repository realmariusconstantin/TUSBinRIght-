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

        if (!isset($data['name'], $data['email'], $data['password'], $data['confirmPassword'])) {
            return $this->fail(['message' => 'All fields are required'], 400);
        }

        if ($data['password'] !== $data['confirmPassword']) {
            return $this->fail(['message' => 'Passwords do not match'], 400);
        }

        $exists = $this->db->table('users')->where('email', $data['email'])->get()->getRow();
        if ($exists) {
            return $this->fail(['message' => 'Email already registered'], 400);
        }

        $this->db->table('users')->insert([
            'name' => $data['name'],
            'email' => $data['email'],
            'password_hash' => password_hash($data['password'], PASSWORD_DEFAULT),
        ]);

        return $this->respondCreated(['message' => 'User registered successfully']);
    }

    public function login()
    {
        $data = $this->request->getJSON(true);

        if (!isset($data['email'], $data['password'])) {
            return $this->fail(['message' => 'Email and password are required'], 400);
        }

        $user = $this->db->table('users')->where('email', $data['email'])->get()->getRow();

        if (!$user || !password_verify($data['password'], $user->password_hash)) {
            return $this->fail(['message' => 'Invalid email or password'], 401);
        }

        return $this->respond([
            'message' => 'Login successful',
            'user' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
            ],
        ]);
    }

    public function testDB()
    {
        try {
            echo '<pre>';
            print_r(\Config\Database::connect());
            die();

            $db = \Config\Database::connect();
            if ($db->connID) {
                return $this->respond(['success' => true, 'message' => 'CodeIgniter DB connected successfully!']);
            } else {
                return $this->fail(['success' => false, 'message' => 'Connection failed!']);
            }
        } catch (\Throwable $e) {
            return $this->fail(['error' => $e->getMessage()]);
        }
    }

}
