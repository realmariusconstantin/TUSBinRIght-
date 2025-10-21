<?php

namespace App\Filters;

use CodeIgniter\Filters\FilterInterface;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;

/**
 * JWT Authentication Filter
 * 
 * This filter protects routes by verifying the JWT token from the cookie.
 * If the token is invalid or missing, it returns a 401 Unauthorized response.
 */
class JWTAuth implements FilterInterface
{
    /**
     * Check if the user is authenticated via JWT before allowing access
     *
     * @param RequestInterface $request
     * @param mixed|null $arguments
     * @return RequestInterface|ResponseInterface|string|void
     */
    public function before(RequestInterface $request, $arguments = null)
    {
        $token = $request->getCookie('jwt_token');

        if (!$token) {
            return \Config\Services::response()
                ->setJSON([
                    'status' => 'error',
                    'message' => 'No authentication token provided'
                ])
                ->setStatusCode(401);
        }

        $decoded = verifyJWT($token);

        if (!$decoded) {
            return \Config\Services::response()
                ->setJSON([
                    'status' => 'error',
                    'message' => 'Invalid or expired token'
                ])
                ->setStatusCode(401);
        }

        // Store user data in request (accessible in controllers via getAuthenticatedUser())
        return $request;
    }

    /**
     * Allows after filters to run after controller execution
     *
     * @param RequestInterface $request
     * @param ResponseInterface $response
     * @param mixed|null $arguments
     * @return ResponseInterface|void
     */
    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
    {
        // No action needed after the request
    }
}