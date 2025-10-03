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
    // public $userLogin = [
    //     'username' => 'required',
    //     'password' => 'required',
    //     'pass_confirm' => 'required|matches[password]',
    //     'email' => 'required|valid_email'
    // ];
    public $userLogin = [
        'username' => [
            'rules' => 'required',
            'errors' => [
                'required' => 'You must chose a Username'
            ]
            ],
        'email' => [
            'rules' => 'required|valid_email',
            'errors' => [
                'valid_email' => 'You must check Email field. It does not appear to be valid.'
            ]
        ],
    ];

    public $labValidation = [
        "username" => [
            "label" => "Username",
            "rules" => "required",
            "errors" => [
                "required" => "{field} is a mandatory field"
            ]
            ],
        "fname" => [
            "label" => "First name",
            "rules" => "required|min_length[2]|max_length[15]",
            "errors" => [
                "required" => "{field} is a mandatory",
                "min_length" => "{field} must be more than 1 character in length",
                "max_length" => "{field} must be no more than 15 characters in length"
            ]
            ],
        "lname" => [
            "label" => "Last name",
            "rules" => "required|min_length[3]|max_length[20]",
            "errors" => [
                "required" => "{field} is a mandatory",
                "min_length" => "{field} must be more than 2 character in length",
                "max_length" => "{field} must be no more than 20 characters in length"
            ]
            ],
        "addressline1" => [
            "label" => "Address line 1",
            "rules" => "required",
            "errors" => [
                "required" => "{field} is a mandatory field"
            ]
            ],
        "city" => [
            "label" => "City",
            "rules" => "required|in_list[Cork,Limerick,Galway]",
            "errors" => [
                "required" => "{field} is a mandatory field",
                "in_list" => "{field} must be either Cork, Limerick or Galway"
            ]
            ],
        "email" => [
            "label" => "Email",
            "rules" => "required|regex_match[/^[^@]+@[^@]+\.[^@]+$/]",
            "errors" => [
                "required" => "{field} is a mandatory field",
                "regex_match" => "This is not a valid {field} address"
            ]
            ],
        "type" => [
            "label" => "Type",
            "rules" => "required|in_list[Customer]",
            "errors" => [
                "required" => "{field} is a mandatory field",
                "in_list" => "{field} must be the word Customer"
            ]
            ],
        "password" => [
            "label" => "Password",
            "rules" => "required|alpha_numeric",
            "errors" => [
                "required" => "{field} is a mandatory field",
                "alpha_numeric" => "{field} must be alpha numeric"
            ]
            ],
        "age" => [
            "label" => "Age",
            "rules" => "required|is_natural_no_zero",
            "errors" => [
                "required" => "{field} is a mandatory field",
                "is_natural_no_zero" => "{field} must be an integer excluding 0"
            ]
            ]
    ];
}
