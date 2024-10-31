<?php

namespace Ovoform\Middleware;

class RegisterMiddleware
{
    public $aliasMiddleware = [
        'authorized' => Authorized::class,
        'admin_login' => AdminLogin::class,
        'allow_registration'=>AllowRegistration::class,
        'auth'=>RedirectIfNotLogin::class
    ];

    public $globalMiddleware = [
        VerifyNonce::class
    ];
}
