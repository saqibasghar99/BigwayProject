<?php

namespace App\Http\Middleware;

use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken as Middleware;

class VerifyCsrfToken extends Middleware
{
    protected $except = [
        '/api/v1/signupguardian',
        '/api/v1/signupdriver',
        '/api/v1/signupcaretaker',
        '/api/v1/signupvendor',
        '/api/v1/signupschool'
    ];
}
