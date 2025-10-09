<?php

namespace Config;

use CodeIgniter\Config\BaseConfig;
use CodeIgniter\Filters\CSRF;
use CodeIgniter\Filters\DebugToolbar;
use CodeIgniter\Filters\Honeypot;
use App\Filters\Cors;

class Filters extends BaseConfig
{
    public array $aliases = [
        'csrf'      => CSRF::class,
        'toolbar'   => DebugToolbar::class,
        'honeypot'  => Honeypot::class,
        'cors'      => Cors::class, // 
    ];

    public array $globals = [
        'before' => [
            'cors', // 
       
        ],
        'after'  => [
            'cors',
            'toolbar',
        ],
    ];

    public array $methods = [];

    public array $filters = [];
}
