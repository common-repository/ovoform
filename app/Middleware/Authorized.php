<?php

namespace Ovoform\Middleware;

class Authorized{
    public function filterRequest()
    {
        if (is_user_logged_in()) {
            ovoform_redirect(home_url());
            exit;
        }
    }
}