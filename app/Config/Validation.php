<?php

namespace Config;

use CodeIgniter\Config\BaseConfig;
use CodeIgniter\Validation\StrictRules\CreditCardRules;
use CodeIgniter\Validation\StrictRules\FileRules;
use CodeIgniter\Validation\StrictRules\FormatRules;
use CodeIgniter\Validation\StrictRules\Rules;

class Validation extends BaseConfig
{
    // --------------------------------------------------------------------
    // Setup
    // --------------------------------------------------------------------

    /**
     * Stores the classes that contain the
     * rules that are available.
     *
     * @var list<string>
     */
    public array $ruleSets = [
        Rules::class,
        FormatRules::class,
        FileRules::class,
        CreditCardRules::class,
    ];

    /**
     * Specifies the views that are used to display the
     * errors.
     *
     * @var array<string, string>
     */
    public array $templates = [
        'list'   => 'CodeIgniter\Validation\Views\list',
        'single' => 'CodeIgniter\Validation\Views\single',
    ];

    // --------------------------------------------------------------------
    // Rules
    // --------------------------------------------------------------------

    public $login = [
        'email' => [
            'label' => 'Email',
            'rules' => 'required|valid_email',
            'errors' => [
                'required' => '{field} is required',
                'valid_email' => '{field} must be a valid email address'
            ]
        ],
        'password' => [
            'label' => 'Password',
            'rules' => 'required',
            'errors' => [
                'required' => '{field} is required'
            ]
        ]
    ];

    public $register = [
        'forename' => [
            'label' => 'Forename',
            'rules' => 'required|min_length[2]|max_length[50]',
            'errors' => [
                'required' => '{field} is required',
                'min_length' => '{field} must be at least 2 characters',
                'max_length' => '{field} must not exceed 50 characters'
            ]
        ],
        'surname' => [
            'label' => 'Surname',
            'rules' => 'required|min_length[2]|max_length[50]',
            'errors' => [
                'required' => '{field} is required',
                'min_length' => '{field} must be at least 2 characters',
                'max_length' => '{field} must not exceed 50 characters'
            ]
        ],
        'email' => [
            'label' => 'Email',
            'rules' => 'required|valid_email',
            'errors' => [
                'required' => '{field} is required',
                'valid_email' => '{field} must be a valid email'
            ]
        ],
        'password' => [
            'label' => 'Password',
            'rules' => 'required|min_length[6]',
            'errors' => [
                'required' => '{field} is required',
                'min_length' => '{field} must be at least 6 characters'
            ]
        ],
        'pass_confirm' => [
            'label' => 'Confirm Password',
            'rules' => 'required',
            'errors' => [
                'required' => '{field} is required',
            ]
        ]
    ];
}
