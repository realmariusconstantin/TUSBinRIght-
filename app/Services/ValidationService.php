<?php

namespace App\Services;

class ValidationService
{
    /**
     * Validate email format and additional checks
     */
    public function validateEmail($email)
    {
        $errors = [];

        // Basic validation
        if (empty($email)) {
            $errors[] = 'Email is required';
            return ['valid' => false, 'errors' => $errors];
        }

        // Check email format
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $errors[] = 'Email must be a valid email address';
            return ['valid' => false, 'errors' => $errors];
        }

        // Check email length
        if (strlen($email) > 255) {
            $errors[] = 'Email must not exceed 255 characters';
            return ['valid' => false, 'errors' => $errors];
        }

        // Check for suspicious patterns (basic SQL injection prevention)
        if (preg_match('/[;\'"`]/', $email)) {
            $errors[] = 'Email contains invalid characters';
            return ['valid' => false, 'errors' => $errors];
        }

        return ['valid' => true, 'errors' => []];
    }

    /**
     * Validate name/username
     */
    public function validateName($name)
    {
        $errors = [];

        if (empty($name)) {
            $errors[] = 'Name is required';
            return ['valid' => false, 'errors' => $errors];
        }

        if (strlen($name) < 3) {
            $errors[] = 'Name must be at least 3 characters';
            return ['valid' => false, 'errors' => $errors];
        }

        if (strlen($name) > 150) {
            $errors[] = 'Name must not exceed 150 characters';
            return ['valid' => false, 'errors' => $errors];
        }

        // Check for valid characters (alphanumeric, spaces, hyphens, apostrophes)
        if (!preg_match('/^[a-zA-Z0-9\s\-\'\.]+$/', $name)) {
            $errors[] = 'Name contains invalid characters';
            return ['valid' => false, 'errors' => $errors];
        }

        // Prevent excessive spacing
        if (preg_match('/\s{2,}/', $name)) {
            $errors[] = 'Name contains excessive spacing';
            return ['valid' => false, 'errors' => $errors];
        }

        return ['valid' => true, 'errors' => []];
    }

    /**
     * Validate password format (minimum requirements only)
     * Full strength validation should be done with PasswordStrengthService
     */
    public function validatePasswordFormat($password)
    {
        $errors = [];

        if (empty($password)) {
            $errors[] = 'Password is required';
            return ['valid' => false, 'errors' => $errors];
        }

        if (strlen($password) < 8) {
            $errors[] = 'Password must be at least 8 characters';
            return ['valid' => false, 'errors' => $errors];
        }

        if (strlen($password) > 128) {
            $errors[] = 'Password must not exceed 128 characters';
            return ['valid' => false, 'errors' => $errors];
        }

        // Check for common weak patterns
        if ($this->isCommonPassword($password)) {
            $errors[] = 'Password is too common. Please choose a more unique password';
            return ['valid' => false, 'errors' => $errors];
        }

        return ['valid' => true, 'errors' => []];
    }

    /**
     * Check if password matches a list of common passwords
     */
    private function isCommonPassword($password)
    {
        $commonPasswords = [
            'password', '123456', '12345678', 'qwerty', 'abc123', 
            'monkey', '1234567', 'letmein', 'trustno1', 'dragon',
            'baseball', '111111', 'iloveyou', 'master', 'sunshine',
            'ashley', 'bailey', 'passw0rd', 'shadow', '123123',
            '654321', 'superman', 'qazwsx', 'michael', 'football',
            'password123', 'admin', 'admin123', 'root', 'toor'
        ];

        return in_array(strtolower($password), $commonPasswords, true);
    }

    /**
     * Validate that two passwords match
     */
    public function validatePasswordsMatch($password, $confirmPassword)
    {
        if ($password !== $confirmPassword) {
            return ['valid' => false, 'error' => 'Passwords do not match'];
        }
        return ['valid' => true, 'error' => ''];
    }

    /**
     * Sanitize input to prevent XSS
     */
    public function sanitizeInput($input)
    {
        return htmlspecialchars(trim($input), ENT_QUOTES, 'UTF-8');
    }

    /**
     * Check if email is already registered
     */
    public function isEmailRegistered($email)
    {
        $db = \Config\Database::connect();
        $result = $db->table('users')
            ->where('email', $email)
            ->countAllResults();
        
        return $result > 0;
    }

    /**
     * Validate credentials exist and are in correct format
     */
    public function validateLoginInput($email, $password)
    {
        $errors = [];

        // Validate email
        $emailValidation = $this->validateEmail($email);
        if (!$emailValidation['valid']) {
            $errors['email'] = $emailValidation['errors'][0];
        }

        // Validate password is not empty
        if (empty($password)) {
            $errors['password'] = 'Password is required';
        } elseif (strlen($password) < 6) {
            $errors['password'] = 'Invalid email or password';
        }

        return [
            'valid' => empty($errors),
            'errors' => $errors
        ];
    }
}
