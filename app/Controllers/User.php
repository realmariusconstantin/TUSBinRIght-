<?php

namespace App\Controllers;

use CodeIgniter\Controller;

class User extends BaseController
{
    protected $db;
    public function __construct()
    {
        helper(['form'], ['url']);
        $this->db = \Config\Database::connect();
        // This is the validation library :)
        $validation = \Config\Services::validation();
    }

    public function login()
    {
        $data = [];
        if ($this->request->is('post')) {

            if (!$this->validate('login')) {
                $data['validation'] = $this->validator;
                echo view('LoginView', $data);
            } else {

                $email = $this->request->getPost('email');
                $password = md5($this->request->getPost('password'));

                // I call the procedure
                $query = $this->db->query("CALL UserLogin(?, ?)", [$email, $password]);

                $user = $query->getRow();

                if ($user) {
                    // Successful login
                    echo view('Success', ['user' => $user]);
                } else {
                    // Login failed
                    $data['login_error'] = 'Invalid email or password';
                    echo view('LoginView', $data);
                }

                $query->freeResult();
            }
        } else {
            echo view('LoginView', $data);
        }
    }

    public function register()
    {
        $data = [];

        if ($this->request->is('post')) {

            // Validate the form
            if (!$this->validate('register')) {
                $data['validation'] = $this->validator;
                echo view('RegisterView', $data);
            } else {
                $forename = $this->request->getPost('forename');
                $surname = $this->request->getPost('surname');
                $email = $this->request->getPost('email');
                $password_hash = md5($this->request->getPost('password'));
                $user_type_id = 1; // normal user

                // I call the stored procedure
                $query = $this->db->query(
                    "CALL CreateUser(?, ?, ?, ?, ?)",
                    [$forename, $surname, $email, $password_hash, $user_type_id]
                );

                $result = $query->getRow();
                $query->freeResult();

                if ($result && $result->status == 1) {
                    $data['success'] = $result->message;
                    echo view('SuccessRegister', $data);
                } else {
                    $data['register_error'] = $result ? $result->message : 'Failed to create user';
                    echo view('RegisterView', $data);
                }
            }
        } else {
            echo view('RegisterView', $data);
        }
    }
}
