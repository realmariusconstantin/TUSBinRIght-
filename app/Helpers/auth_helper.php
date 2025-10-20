<?php

use Firebase\JWT\JWT;
use Firebase\JWT\Key;

if (!function_exists('generateJWT')) {
    function generateJWT(int $userId, string $email, string $name, string $role): string
    {
        $algo = getenv('JWT_ALGO') ?: 'RS256';
        
        if ($algo === 'RS256') {
            $privateKeyPath = APPPATH . getenv('JWT_PRIVATE_KEY_PATH');
            
            if (!file_exists($privateKeyPath)) {
                throw new \Exception('Private key file not found: ' . $privateKeyPath);
            }
            
            $privateKey = file_get_contents($privateKeyPath);
            
            if (!$privateKey) {
                throw new \Exception('Failed to read private key');
            }
        } else {
            $privateKey = getenv('JWT_SECRET');
            
            if (!$privateKey) {
                throw new \Exception('JWT_SECRET not configured');
            }
        }

        $issuedAt = time();
        $expirationTime = $issuedAt + 3600;
        
        $payload = [
            'iat' => $issuedAt,
            'exp' => $expirationTime,
            'iss' => base_url(),
            'data' => [
                'id' => $userId,
                'email' => $email,
                'name' => $name,
                'role' => $role
            ]
        ];

        return JWT::encode($payload, $privateKey, $algo);
    }
}

if (!function_exists('verifyJWT')) {
    function verifyJWT(string $token): ?object
    {
        try {
            $algo = getenv('JWT_ALGO') ?: 'RS256';
            
            if ($algo === 'RS256') {
                $publicKeyPath = APPPATH . getenv('JWT_PUBLIC_KEY_PATH');
                
                if (!file_exists($publicKeyPath)) {
                    log_message('error', 'Public key file not found: ' . $publicKeyPath);
                    return null;
                }
                
                $publicKey = file_get_contents($publicKeyPath);
                
                if (!$publicKey) {
                    log_message('error', 'Failed to read public key');
                    return null;
                }
            } else {
                $publicKey = getenv('JWT_SECRET');
                
                if (!$publicKey) {
                    log_message('error', 'JWT_SECRET not configured');
                    return null;
                }
            }

            $decoded = JWT::decode($token, new Key($publicKey, $algo));
            return $decoded;
            
        } catch (\Firebase\JWT\ExpiredException $e) {
            log_message('info', 'JWT expired: ' . $e->getMessage());
            return null;
        } catch (\Firebase\JWT\SignatureInvalidException $e) {
            log_message('warning', 'JWT signature invalid: ' . $e->getMessage());
            return null;
        } catch (\Exception $e) {
            log_message('error', 'JWT verification failed: ' . $e->getMessage());
            return null;
        }
    }
}

if (!function_exists('getAuthenticatedUser')) {
    function getAuthenticatedUser(): ?object
    {
        $request = \Config\Services::request();
        $token = $request->getCookie('jwt_token');
        
        if (!$token) {
            return null;
        }

        $decoded = verifyJWT($token);
        
        if (!$decoded) {
            return null;
        }

        return $decoded->data ?? null;
    }
}

if (!function_exists('isAdmin')) {
    function isAdmin(): bool
    {
        $user = getAuthenticatedUser();
        return $user && ($user->role ?? '') === 'admin';
    }
}

if (!function_exists('isStudent')) {
    function isStudent(): bool
    {
        $user = getAuthenticatedUser();
        return $user && ($user->role ?? '') === 'student';
    }
}