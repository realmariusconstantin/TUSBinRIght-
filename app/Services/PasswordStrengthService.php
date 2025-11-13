<?php

namespace App\Services;

class PasswordStrengthService
{
    /**
     * Validate password strength
     * Returns: ['valid' => bool, 'score' => int (0-100), 'message' => string, 'requirements' => array]
     */
    public function validatePassword($password)
    {
        $score = 0;
        $requirements = [
            'length' => strlen($password) >= 8,
            'uppercase' => preg_match('/[A-Z]/', $password),
            'lowercase' => preg_match('/[a-z]/', $password),
            'numbers' => preg_match('/[0-9]/', $password),
            'special' => preg_match('/[!@#$%^&*()_+\-=\[\]{};:\'",.<>?\/\\|`~]/', $password)
        ];

        // Calculate score
        if ($requirements['length']) $score += 20;
        if ($requirements['uppercase']) $score += 20;
        if ($requirements['lowercase']) $score += 20;
        if ($requirements['numbers']) $score += 20;
        if ($requirements['special']) $score += 20;

        // Determine strength level
        $strength = 'weak';
        $message = 'Password is too weak';
        $valid = false;

        if ($score >= 60) {
            $strength = 'fair';
            $message = 'Password is fair';
        }
        if ($score >= 80) {
            $strength = 'good';
            $message = 'Password is good';
        }
        if ($score >= 100) {
            $strength = 'strong';
            $message = 'Password is strong';
            $valid = true;
        }

        // Minimum requirements: at least 8 chars, 1 uppercase, 1 lowercase, 1 number
        if ($requirements['length'] && $requirements['uppercase'] && $requirements['lowercase'] && $requirements['numbers']) {
            $valid = true;
        }

        return [
            'valid' => $valid,
            'score' => $score,
            'strength' => $strength,
            'message' => $message,
            'requirements' => $requirements
        ];
    }

    /**
     * Get password requirements checklist
     */
    public function getRequirements()
    {
        return [
            'length' => 'At least 8 characters',
            'uppercase' => 'At least one uppercase letter (A-Z)',
            'lowercase' => 'At least one lowercase letter (a-z)',
            'numbers' => 'At least one number (0-9)',
            'special' => 'At least one special character (!@#$%^&* etc.)'
        ];
    }
}
