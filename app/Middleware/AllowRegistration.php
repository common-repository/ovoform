<?php

namespace Ovoform\Middleware;

class AllowRegistration{
    public function filterRequest()
    {
        if (!get_option('users_can_register')) {
            wp_redirect(home_url('/login/?registration=disabled'));
            exit;
        }
    }
}