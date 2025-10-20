<?php

namespace App\Controllers;

use CodeIgniter\RESTful\ResourceController;
use CodeIgniter\Cookie\Cookie;

class AuthController extends ResourceController
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

        if (!$this->validate('register')) {
            return $this->response->setJSON([
                'status' => 'error',
                'errors' => $this->validator->getErrors()
            ])->setStatusCode(400);
        }

        // Check if email exists - use direct query instead of stored procedure
        $checkQuery = $this->db->table('users')
            ->where('email', $data['email'])
            ->countAllResults();

        if ($checkQuery > 0) {
            return $this->response->setJSON([
                'status' => 'error',
                'errors' => ['email' => 'Email already registered']
            ])->setStatusCode(400);
        }

        // Create user with stored procedure (defaults to user_type_id = 1 = student)
        $hashedPassword = password_hash($data['password'], PASSWORD_DEFAULT);
        
        try {
            // Insert user directly instead of using stored procedure
            $this->db->table('users')->insert([
                'name' => $data['name'],
                'email' => $data['email'],
                'password_hash' => $hashedPassword,
                'user_type_id' => 1 // student
            ]);

            // Get the newly created user ID
            $userId = $this->db->insertID();

            // Get the user with role from usertype table
            $user = $this->db->table('users')
                ->select('users.*, usertype.description as role')
                ->join('usertype', 'users.user_type_id = usertype.id')
                ->where('users.id', $userId)
                ->get()
                ->getRow();

            // Automatically log the user in after registration
            $token = generateJWT($user->id, $user->email, $user->name, $user->role);

            // Create HttpOnly cookie
            $cookie = [
                'name'     => 'jwt_token',
                'value'    => $token,
                'expire'   => 3600,
                'path'     => '/',
                'domain'   => '',
                'secure'   => false,
                'httponly' => true,
                'samesite' => 'Lax'
            ];

            $this->response->setCookie($cookie);

            return $this->response->setJSON([
                'status' => 'success',
                'message' => 'User registered successfully',
                'user' => [
                    'id' => $user->id,
                    'name' => $user->name,
                    'email' => $user->email,
                    'role' => $user->role
                ]
            ])->setStatusCode(201);
        } catch (\Exception $e) {
            log_message('error', 'Registration failed: ' . $e->getMessage());
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'Registration failed'
            ])->setStatusCode(500);
        }
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

        // Get user directly from database with role from usertype table
        try {
            $user = $this->db->table('users')
                ->select('users.*, usertype.description as role')
                ->join('usertype', 'users.user_type_id = usertype.id')
                ->where('users.email', $data['email'])
                ->get()
                ->getRow();

            if (!$user || !password_verify($data['password'], $user->password_hash)) {
                return $this->response->setJSON([
                    'status' => 'error',
                    'message' => 'Invalid email or password'
                ])->setStatusCode(401);
            }

            // Role is already fetched from usertype table
            $role = $user->role;

            // Generate JWT token with RS256
            $token = generateJWT($user->id, $user->email, $user->name, $role);

            // Create HttpOnly cookie
            $cookie = [
                'name'     => 'jwt_token',
                'value'    => $token,
                'expire'   => 3600,
                'path'     => '/',
                'domain'   => '',
                'secure'   => false,
                'httponly' => true,
                'samesite' => 'Lax'
            ];

            $this->response->setCookie($cookie);

            return $this->response->setJSON([
                'status' => 'success',
                'message' => 'Login successful',
                'user' => [
                    'id' => $user->id,
                    'name' => $user->name,
                    'email' => $user->email,
                    'role' => $role
                ]
            ])->setStatusCode(200);

        } catch (\Exception $e) {
            log_message('error', 'Login failed: ' . $e->getMessage());
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'Login failed'
            ])->setStatusCode(500);
        }
    }

    public function logout()
    {
        $cookie = [
            'name'     => 'jwt_token',
            'value'    => '',
            'expire'   => 0,
            'path'     => '/',
            'domain'   => '',
            'secure'   => false,
            'httponly' => true,
            'samesite' => 'Lax'
        ];

        $this->response->setCookie($cookie);

        return $this->response->setJSON([
            'status' => 'success',
            'message' => 'Logout successful'
        ])->setStatusCode(200);
    }

    public function profile()
    {
        $user = getAuthenticatedUser();

        if (!$user) {
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'Not authenticated'
            ])->setStatusCode(401);
        }

        // Get full user data from database with role from usertype table
        try {
            $userData = $this->db->table('users')
                ->select('users.*, usertype.description as role')
                ->join('usertype', 'users.user_type_id = usertype.id')
                ->where('users.id', $user->id)
                ->get()
                ->getRow();

            if (!$userData) {
                return $this->response->setJSON([
                    'status' => 'error',
                    'message' => 'User not found'
                ])->setStatusCode(404);
            }

            // Role is already fetched from usertype table
            $role = $userData->role;

            return $this->response->setJSON([
                'status' => 'success',
                'user' => [
                    'id' => $userData->id,
                    'name' => $userData->name,
                    'email' => $userData->email,
                    'role' => $role
                ]
            ])->setStatusCode(200);

        } catch (\Exception $e) {
            log_message('error', 'Profile fetch failed: ' . $e->getMessage());
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'Failed to fetch profile'
            ])->setStatusCode(500);
        }
    }

    public function promote($id)
    {
        // Only admins can promote users
        if (!isAdmin()) {
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'Unauthorized'
            ])->setStatusCode(403);
        }

        try {
            // Update user to admin role (user_type_id = 2)
            $this->db->query('CALL UpdateUser(?, NULL, NULL, NULL, ?)', [$id, 2]);
            $this->db->query('SELECT 1'); // Clear stored procedure result

            return $this->response->setJSON([
                'status' => 'success',
                'message' => 'User promoted to admin'
            ])->setStatusCode(200);

        } catch (\Exception $e) {
            log_message('error', 'Promote failed: ' . $e->getMessage());
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'Failed to promote user'
            ])->setStatusCode(500);
        }
    }

    public function refresh()
    {
        $user = getAuthenticatedUser();

        if (!$user) {
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'Not authenticated'
            ])->setStatusCode(401);
        }

        try {
            // Generate new token
            $token = generateJWT($user->id, $user->email, $user->name, $user->role);

            $cookie = [
                'name'     => 'jwt_token',
                'value'    => $token,
                'expire'   => 3600,
                'path'     => '/',
                'domain'   => '',
                'secure'   => false,
                'httponly' => true,
                'samesite' => 'Lax'
            ];

            $this->response->setCookie($cookie);

            return $this->response->setJSON([
                'status' => 'success',
                'message' => 'Token refreshed successfully'
            ])->setStatusCode(200);

        } catch (\Exception $e) {
            log_message('error', 'Token refresh failed: ' . $e->getMessage());
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'Failed to refresh token'
            ])->setStatusCode(500);
        }
    }
}
