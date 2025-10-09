<?php

namespace Config;

use CodeIgniter\Config\BaseConfig;
use CodeIgniter\Validation\StrictRules\CreditCardRules;
use CodeIgniter\Validation\StrictRules\FileRules;
use CodeIgniter\Validation\StrictRules\FormatRules;
use CodeIgniter\Validation\StrictRules\Rules;

class Validation extends BaseConfig
{
    public array $ruleSets = [
        Rules::class,
        FormatRules::class,
        FileRules::class,
        CreditCardRules::class,
    ];

    public array $templates = [
        'list'   => 'CodeIgniter\Validation\Views\list',
        'single' => 'CodeIgniter\Validation\Views\single',
    ];

    public $login = [
        'email' => [
            'label'  => 'Email',
            'rules'  => 'required|valid_email',
            'errors' => [
                'required'    => '{field} is required',
                'valid_email' => '{field} must be a valid email address',
            ],
        ],
        'password' => [
            'label'  => 'Password',
            'rules'  => 'required|min_length[6]',
            'errors' => [
                'required'    => '{field} is required',
                'min_length'  => '{field} must be at least 6 characters',
            ],
        ],
    ];


    public $register = [
        'name' => [
            'label'  => 'Full Name',
            'rules'  => 'required|min_length[3]|max_length[150]',
            'errors' => [
                'required'   => '{field} is required',
                'min_length' => '{field} must be at least 3 characters',
                'max_length' => '{field} must not exceed 150 characters',
            ],
        ],
        'email' => [
            'label'  => 'Email',
            'rules'  => 'required|valid_email',
            'errors' => [
                'required'    => '{field} is required',
                'valid_email' => '{field} must be a valid email address',
            ],
        ],
        'password' => [
            'label'  => 'Password',
            'rules'  => 'required|min_length[6]',
            'errors' => [
                'required'   => '{field} is required',
                'min_length' => '{field} must be at least 6 characters',
            ],
        ],
        'confirmPassword' => [
            'label'  => 'Confirm Password',
            'rules'  => 'required|matches[password]',
            'errors' => [
                'required' => '{field} is required',
                'matches'  => '{field} must match the Password',
            ],
        ],
    ];
}
