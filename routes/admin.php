<?php
// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

use Ovoform\BackOffice\Router\Router;
use Ovoform\Controllers\Admin\FormController;
use Ovoform\Controllers\Admin\GeneralSettingController;

$router = new Router;

//setting
$router->router([
    'admin.setting.index' => [
        'method'       => 'get',
        'query_string' => 'ovoform_settings',
        'action'       => [GeneralSettingController::class, 'index'],
    ],
]);




//Forms routes
$router->router([
    'admin.forms' => [
        'method'       => 'get',
        'query_string' => 'ovoform_forms',
        'action'       => [FormController::class, 'forms'],
    ],
]);

$router->router([
    'admin.forms.create' => [
        'method'       => 'get',
        'query_string' => 'ovoform_forms_create',
        'action'       => [FormController::class, 'formCreate'],
    ],
]);

$router->router([
    'admin.forms.edit' => [
        'method'       => 'get',
        'query_string' => 'ovoform_forms_edit',
        'action'       => [FormController::class, 'formEdit'],
    ],
]);

$router->router([
    'admin.forms.store' => [
        'method'       => 'post',
        'query_string' => 'ovoform_forms_store',
        'action'       => [FormController::class, 'formStore'],
    ],
]);

$router->router([
    'admin.forms.update' => [
        'method'       => 'post',
        'query_string' => 'ovoform_forms_update',
        'action'       => [FormController::class, 'formUpdate'],
    ],
]);

$router->router([
    'admin.forms.delete' => [
        'method'       => 'post',
        'query_string' => 'ovoform_forms_delete',
        'action'       => [FormController::class, 'formDelete'],
    ],
]);

$router->router([
    'admin.forms.submissions' => [
        'method'       => 'get',
        'query_string' => 'ovoform_forms_submissions',
        'action'       => [FormController::class, 'formSubmissions'],
    ],
]);
$router->router([
    'admin.forms.attachment.download' => [
        'method'       => 'get',
        'query_string' => 'ovoform_forms_attachment_download',
        'action'       => [FormController::class, 'attachmentDownload'],
    ],
]);
$router->router([
    'admin.forms.unread.submissions' => [
        'method'       => 'get',
        'query_string' => 'ovoform_forms_unread_submissions',
        'action'       => [FormController::class, 'unreadSubmissions'],
    ],
]);
$router->router([
    'admin.forms.submission.details' => [
        'method'       => 'get',
        'query_string' => 'ovoform_forms_submission_details',
        'action'       => [FormController::class, 'submissionDetails'],
    ],
]);
$router->router([
    'admin.forms.submissions.delete' => [
        'method'       => 'post',
        'query_string' => 'ovoform_forms_submission_delete',
        'action'       => [FormController::class, 'submissionDelete'],
    ],
]);

//extension

$router->router([
'admin.extension.update' => [
    'method'       => 'post',
    'query_string' => 'ovoform_extension_update',
    'action'       => [GeneralSettingController::class, 'extensionUpdate'],
],
]);

