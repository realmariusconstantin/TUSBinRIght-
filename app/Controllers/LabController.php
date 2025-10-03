<?php

namespace App\Controllers;

use CodeIgniter\Controller;

class LabController extends BaseController
{
    public function __construct()
    {
        helper(['form'], ['url']);
        // This is the validation library :)
        $validation = \Config\Services::validation();
    }

    public function User()
    {
        $data = [];
        if ($this->request->is('post')) {

            if (!$this->validate('labValidation')) {
                $data['validation'] = $this->validator;
                echo view('UserView', $data);
            } else {
                echo view('Success');
            }
        } else {
            echo view('UserView', $data);
        }
    }
}
