<?php
// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

use Ovoform\BackOffice\Router\Router;
use Ovoform\Controllers\FormController;

$router = new Router;


$router->router([
    'form.submit'=> [
        'method'     => 'post',
        'uri'        => get_option('ovoform_user_panel_prefix','ovoform-form').'/form-submit',
        'action'     => [FormController::class, 'submitForm'],
    ]
]);