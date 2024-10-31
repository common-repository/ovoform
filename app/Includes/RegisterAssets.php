<?php

namespace Ovoform\Includes;


class RegisterAssets
{
    public static $styles = [
        'admin' => [
            'bootstrap.min.css',
            'all.min.css',
            'line-awesome.min.css',
            'app.css',
        ],
        'global' => [
        ],
        'public' => [
            'ovoform_public.css',
        ]
    ];
    public static $scripts = [
        'admin' => [
            'bootstrap.bundle.min.js',
            'vendor/bootstrap-toggle.min.js',
            'app.js',
        ],
        'global' => [
        ],
        'public' => []
    ];
}
