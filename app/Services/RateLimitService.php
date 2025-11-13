<?php

namespace App\Services;

class RateLimitService
{
    protected $db;
    protected $maxAttempts = 5;
    protected $lockoutDurationMinutes = 15;

    public function __construct()
    {
        $this->db = \Config\Database::connect();
    }

    /**
     * Check if an email/IP is rate limited
     * Returns: ['limited' => bool, 'message' => string, 'remainingTime' => int]
     */
    public function isRateLimited($email, $ipAddress)
    {
        $attempt = $this->db->table('login_attempts')
            ->where('email', $email)
            ->where('ip_address', $ipAddress)
            ->get()
            ->getRow();

        if (!$attempt) {
            return ['limited' => false, 'message' => '', 'remainingTime' => 0];
        }

        // Check if lockout has expired
        if ($attempt->locked_until) {
            $lockedUntil = strtotime($attempt->locked_until);
            $now = time();

            if ($now < $lockedUntil) {
                $remainingSeconds = $lockedUntil - $now;
                $remainingMinutes = ceil($remainingSeconds / 60);
                return [
                    'limited' => true,
                    'message' => "Account temporarily locked. Try again in {$remainingMinutes} minute(s).",
                    'remainingTime' => $remainingSeconds
                ];
            } else {
                // Unlock and reset attempts
                $this->db->table('login_attempts')
                    ->where('email', $email)
                    ->where('ip_address', $ipAddress)
                    ->update([
                        'attempts' => 0,
                        'locked_until' => null,
                        'updated_at' => date('Y-m-d H:i:s')
                    ]);
                return ['limited' => false, 'message' => '', 'remainingTime' => 0];
            }
        }

        return ['limited' => false, 'message' => '', 'remainingTime' => 0];
    }

    /**
     * Record a failed login attempt
     */
    public function recordFailedAttempt($email, $ipAddress)
    {
        $attempt = $this->db->table('login_attempts')
            ->where('email', $email)
            ->where('ip_address', $ipAddress)
            ->get()
            ->getRow();

        $now = date('Y-m-d H:i:s');
        $attempts = $attempt ? $attempt->attempts + 1 : 1;

        $lockoutUntil = null;
        if ($attempts >= $this->maxAttempts) {
            $lockoutUntil = date('Y-m-d H:i:s', time() + ($this->lockoutDurationMinutes * 60));
        }

        if ($attempt) {
            $this->db->table('login_attempts')
                ->where('email', $email)
                ->where('ip_address', $ipAddress)
                ->update([
                    'attempts' => $attempts,
                    'locked_until' => $lockoutUntil,
                    'updated_at' => $now
                ]);
        } else {
            $this->db->table('login_attempts')->insert([
                'email' => $email,
                'ip_address' => $ipAddress,
                'attempts' => 1,
                'locked_until' => $lockoutUntil,
                'created_at' => $now,
                'updated_at' => $now
            ]);
        }

        $remaining = $this->maxAttempts - $attempts;
        return [
            'attempts' => $attempts,
            'remaining' => max(0, $remaining),
            'locked' => $attempts >= $this->maxAttempts
        ];
    }

    /**
     * Clear attempts on successful login
     */
    public function clearAttempts($email, $ipAddress)
    {
        $this->db->table('login_attempts')
            ->where('email', $email)
            ->where('ip_address', $ipAddress)
            ->delete();
    }
}
