<?php

namespace App\Controllers\Api;

use CodeIgniter\RESTful\ResourceController;
use App\Services\PasswordStrengthService;

class PasswordValidator extends ResourceController
{
    protected $format = 'json';

    /**
     * GET /api/password-strength?password=xyz
     * Real-time password strength validation
     */
    public function strength()
    {
        $password = $this->request->getGet('password');

        if (!$password) {
            return $this->fail('Password parameter required', 400);
        }

        $service = new PasswordStrengthService();
        $validation = $service->validatePassword($password);

        return $this->respond([
            'valid' => $validation['valid'],
            'score' => $validation['score'],
            'strength' => $validation['strength'],
            'message' => $validation['message'],
            'requirements' => $validation['requirements'],
            'needed' => $service->getRequirements()
        ]);
    }
}
