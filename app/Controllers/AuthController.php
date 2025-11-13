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

        // Initialize validation and security services
        $validationService = new \App\Services\ValidationService();
        $passwordStrengthService = new \App\Services\PasswordStrengthService();
        $rateLimitService = new \App\Services\RateLimitService();

        // Get client IP for rate limiting registrations
        $ipAddress = $this->request->getIPAddress();
        
        $errors = [];

        // Validate name
        $nameValidation = $validationService->validateName($data['name'] ?? '');
        if (!$nameValidation['valid']) {
            $errors['name'] = $nameValidation['errors'][0];
        }

        // Validate email format
        $emailValidation = $validationService->validateEmail($data['email'] ?? '');
        if (!$emailValidation['valid']) {
            $errors['email'] = $emailValidation['errors'][0];
        }

        // Check if email already registered (only if email is valid)
        if (empty($errors['email']) && $validationService->isEmailRegistered($data['email'])) {
            $errors['email'] = 'Email is already registered. Please use a different email or log in.';
        }

        // Validate password format
        $passwordFormatValidation = $validationService->validatePasswordFormat($data['password'] ?? '');
        if (!$passwordFormatValidation['valid']) {
            $errors['password'] = $passwordFormatValidation['errors'][0];
        }

        // Validate passwords match
        if (!empty($data['password']) && !empty($data['confirmPassword'])) {
            $passwordMatchValidation = $validationService->validatePasswordsMatch(
                $data['password'],
                $data['confirmPassword']
            );
            if (!$passwordMatchValidation['valid']) {
                $errors['confirmPassword'] = $passwordMatchValidation['error'];
            }
        } elseif (empty($data['confirmPassword'])) {
            $errors['confirmPassword'] = 'Confirm password is required';
        }

        // Return early if basic validation fails
        if (!empty($errors)) {
            return $this->response->setJSON([
                'status' => 'error',
                'errors' => $errors
            ])->setStatusCode(400);
        }

        // Validate password strength
        $passwordValidation = $passwordStrengthService->validatePassword($data['password']);

        if (!$passwordValidation['valid']) {
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'Password does not meet security requirements',
                'password' => [
                    'score' => $passwordValidation['score'],
                    'strength' => $passwordValidation['strength'],
                    'requirements' => $passwordValidation['requirements'],
                    'needed' => $passwordStrengthService->getRequirements()
                ]
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
                'name' => 'jwt_token',
                'value' => $token,
                'expire' => 3600,
                'path' => '/',
                'domain' => '',
                'secure' => false,
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

        // Initialize validation and security services
        $validationService = new \App\Services\ValidationService();
        $rateLimitService = new \App\Services\RateLimitService();

        // Get client IP address
        $ipAddress = $this->request->getIPAddress();
        $email = $data['email'] ?? '';
        $password = $data['password'] ?? '';

        // Validate login input
        $loginValidation = $validationService->validateLoginInput($email, $password);
        if (!$loginValidation['valid']) {
            return $this->response->setJSON([
                'status' => 'error',
                'errors' => $loginValidation['errors']
            ])->setStatusCode(400);
        }

        // Check rate limiting BEFORE attempting authentication
        $rateLimitStatus = $rateLimitService->isRateLimited($email, $ipAddress);

        if ($rateLimitStatus['limited']) {
            return $this->response->setJSON([
                'status' => 'error',
                'message' => $rateLimitStatus['message'],
                'rateLimited' => true,
                'remaining_time' => $rateLimitStatus['remainingTime']
            ])->setStatusCode(429); // Too Many Requests
        }

        // Get user directly from database with role from usertype table
        try {
            $user = $this->db->table('users')
                ->select('users.*, usertype.description as role')
                ->join('usertype', 'users.user_type_id = usertype.id')
                ->where('users.email', $email)
                ->get()
                ->getRow();

            if (!$user || !password_verify($data['password'], $user->password_hash)) {
                // Record failed attempt
                $failureResult = $rateLimitService->recordFailedAttempt($email, $ipAddress);
                
                $remaining = $failureResult['remaining'];
                $message = $failureResult['locked'] 
                    ? "Account locked after 5 failed attempts. Try again in 15 minutes."
                    : "Invalid email or password. Attempts remaining: {$remaining}";

                return $this->response->setJSON([
                    'status' => 'error',
                    'message' => $message,
                    'attemptsRemaining' => $remaining,
                    'accountLocked' => $failureResult['locked']
                ])->setStatusCode(401);
            }

            // Successful login - clear rate limit
            $rateLimitService->clearAttempts($email, $ipAddress);

            // Role is already fetched from usertype table
            $role = $user->role;

            // Generate JWT token with RS256
            $token = generateJWT($user->id, $user->email, $user->name, $role);

            // Create HttpOnly cookie
            $cookie = [
                'name' => 'jwt_token',
                'value' => $token,
                'expire' => 3600,
                'path' => '/',
                'domain' => '',
                'secure' => false,
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
            'name' => 'jwt_token',
            'value' => '',
            'expire' => 0,
            'path' => '/',
            'domain' => '',
            'secure' => false,
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

    public function recyclingSummary()
    {
        $user = getAuthenticatedUser();

        if (!$user) {
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'Not authenticated'
            ])->setStatusCode(401);
        }

        try {
            // Get recycling stats grouped by item type
            $stats = $this->db->table('userscan')
                ->select('itemtype.id, itemtype.description, COUNT(userscan.id) as count')
                ->join('itemtype', 'userscan.item_type_id = itemtype.id')
                ->where('userscan.user_id', $user->id)
                ->groupBy('itemtype.id, itemtype.description')
                ->get()
                ->getResultArray();

            // Initialize counters
            $summary = [
                'can' => 0,
                'plastic' => 0,
                'paper' => 0,
                'glass' => 0,
                'total' => 0
            ];

            // Map results to summary
            foreach ($stats as $stat) {
                $type = strtolower($stat['description']);
                if (isset($summary[$type])) {
                    $summary[$type] = (int)$stat['count'];
                }
                $summary['total'] += (int)$stat['count'];
            }

            return $this->response->setJSON([
                'status' => 'success',
                'summary' => $summary
            ])->setStatusCode(200);

        } catch (\Exception $e) {
            log_message('error', 'Recycling summary fetch failed: ' . $e->getMessage());
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'Failed to fetch recycling summary'
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
                'name' => 'jwt_token',
                'value' => $token,
                'expire' => 3600,
                'path' => '/',
                'domain' => '',
                'secure' => false,
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

    /**
     * Update user email
     * PUT /profile/email
     */
    public function updateEmail()
    {
        $user = getAuthenticatedUser();

        if (!$user) {
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'Not authenticated'
            ])->setStatusCode(401);
        }

        $data = $this->request->getJSON(true);

        if (empty($data['email'])) {
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'Email is required'
            ])->setStatusCode(400);
        }

        // Validate email format
        if (!filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'Invalid email format'
            ])->setStatusCode(400);
        }

        // Check if email already exists (excluding current user)
        $existingEmail = $this->db->table('users')
            ->where('email', $data['email'])
            ->where('id !=', $user->id)
            ->countAllResults();

        if ($existingEmail > 0) {
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'Email already in use'
            ])->setStatusCode(400);
        }

        try {
            $this->db->table('users')
                ->where('id', $user->id)
                ->update(['email' => $data['email']]);

            return $this->response->setJSON([
                'status' => 'success',
                'message' => 'Email updated successfully'
            ])->setStatusCode(200);

        } catch (\Exception $e) {
            log_message('error', 'Email update failed: ' . $e->getMessage());
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'Failed to update email'
            ])->setStatusCode(500);
        }
    }

    /**
     * Update user password
     * PUT /profile/password
     */
    public function updatePassword()
    {
        $user = getAuthenticatedUser();

        if (!$user) {
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'Not authenticated'
            ])->setStatusCode(401);
        }

        $data = $this->request->getJSON(true);

        if (empty($data['currentPassword']) || empty($data['newPassword'])) {
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'Current password and new password are required'
            ])->setStatusCode(400);
        }

        try {
            // Get current user password hash from database
            $currentUser = $this->db->table('users')
                ->where('id', $user->id)
                ->get()
                ->getRow();

            if (!$currentUser) {
                return $this->response->setJSON([
                    'status' => 'error',
                    'message' => 'User not found'
                ])->setStatusCode(404);
            }

            // Verify current password
            if (!password_verify($data['currentPassword'], $currentUser->password_hash)) {
                return $this->response->setJSON([
                    'status' => 'error',
                    'message' => 'Current password is incorrect'
                ])->setStatusCode(401);
            }

            // Validate new password (at least 6 characters)
            if (strlen($data['newPassword']) < 6) {
                return $this->response->setJSON([
                    'status' => 'error',
                    'message' => 'New password must be at least 6 characters long'
                ])->setStatusCode(400);
            }

            // Hash new password
            $hashedPassword = password_hash($data['newPassword'], PASSWORD_DEFAULT);

            // Update password
            $this->db->table('users')
                ->where('id', $user->id)
                ->update(['password_hash' => $hashedPassword]);

            return $this->response->setJSON([
                'status' => 'success',
                'message' => 'Password updated successfully'
            ])->setStatusCode(200);

        } catch (\Exception $e) {
            log_message('error', 'Password update failed: ' . $e->getMessage());
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'Failed to update password'
            ])->setStatusCode(500);
        }
    }
}