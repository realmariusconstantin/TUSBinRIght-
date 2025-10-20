<?php

namespace Config;

use CodeIgniter\Config\BaseConfig;
use CodeIgniter\Filters\CSRF;
use CodeIgniter\Filters\DebugToolbar;
use CodeIgniter\Filters\Honeypot;
use App\Filters\Cors;
use App\Filters\JWTAuth;

class Filters extends BaseConfig
{
    public array $aliases = [
        'csrf'     => CSRF::class,
        'toolbar'  => DebugToolbar::class,
        'honeypot' => Honeypot::class,
        'cors'     => Cors::class,
        'jwtauth'  => JWTAuth::class,
    ];

    public array $globals = [
        'before' => [
            'cors', // CORS must run first
        ],
        'after'  => [
            'toolbar',
        ],
    ];

    public array $methods = [];

    public array $filters = [];
}
