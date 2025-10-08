<?php
namespace App\Controllers\Api;

use App\Models\UserModel;
use CodeIgniter\RESTful\ResourceController;

class Auth extends ResourceController
{
    protected $format = 'json';

    public function register()
    {
        $data = $this->request->getJSON(true);
        $rules = [
            'username' => 'required|min_length[3]',
            'email'    => 'required|valid_email|is_unique[users.email]',
            'password' => 'required|min_length[6]',
        ];

        if (!$this->validate($rules)) {
            return $this->failValidationErrors($this->validator->getErrors());
        }

        $userModel = new UserModel();
        $userModel->insert([
            'username' => $data['username'],
            'email'    => $data['email'],
            'password' => password_hash($data['password'], PASSWORD_BCRYPT),
            'role'     => 'user'
        ]);

        return $this->respondCreated(['message' => 'User registered successfully']);
    }

    public function login()
    {
        $data = $this->request->getJSON(true);
        if (!$data || !isset($data['email'],$data['password'])) {
            return $this->failValidationErrors(['email' => 'Required', 'password' => 'Required']);
        }

        $user = (new UserModel())->where('email', $data['email'])->first();
        if (!$user || !password_verify($data['password'], $user['password'])) {
            return $this->failUnauthorized('Invalid credentials');
        }

        helper('jwt'); // see helper below
        $token = create_jwt(['id'=>$user['id'],'email'=>$user['email'],'role'=>$user['role'] ?? 'user']);

        return $this->respond(['token' => $token, 'user' => ['id'=>$user['id'], 'username'=>$user['username'], 'email'=>$user['email']]], 200);
    }

    public function me()
    {
        helper('jwt');
        $auth = $this->request->getServer('HTTP_AUTHORIZATION');
        if (!$auth || !str_starts_with($auth, 'Bearer ')) {
            return $this->failUnauthorized('Missing token');
        }
        try {
            $payload = decode_jwt(substr($auth, 7));
            return $this->respond(['user' => $payload->data], 200);
        } catch (\Throwable $e) {
            return $this->failUnauthorized('Invalid token');
        }
    }
}
