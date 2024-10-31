<?php

namespace Ovoform\Middleware;

class VerifyNonce
{

    protected $exceptVerify = [];

    public function __construct()
    {
        $this->exceptVerify = [
            
        ];
    }

    public function filterRequest()
    {
        if ($this->shouldVerify()) {
            if ($_SERVER['REQUEST_METHOD'] == 'POST') {
                $nonce = ovoform_request()->nonce;

                if (!$nonce) {
                    ovoform_abort(404);
                }
                if (get_query_var('ovoform_page')) {
                    $currentRoute = get_query_var('ovoform_page');
                } else {
                    $currentRoute = ovoform_current_route();
                }
                if (!wp_verify_nonce($nonce, $currentRoute)) {
                    ovoform_abort(404);
                }
            }
        }
    }

    public function shouldVerify()
    {
        if (in_array(get_query_var('ovoform_page'), $this->exceptVerify)) return false;
        return true;
    }
}
