<?php

namespace Ovoform\Hook;

use Ovoform\BackOffice\AdminRequestHandler;
use Ovoform\Models\SubmitForm;

class AdminMenu
{
    public function menuSetting()
    {
        $pendingView = SubmitForm::where('is_viewed',0)->count();
        if ($pendingView) {
            $pendingView = '<span class="ovoform-notification">'.$pendingView.'</span>';
        }else{
            $pendingView = '';
        }
        add_menu_page(
            esc_html__('Ovoform', 'ovoform'),
            esc_html__('Ovoform', 'ovoform').' '.$pendingView,
            'ovoform_forms',
            ovoform_route('admin.forms')->query_string,
            function(){},
            ovoform_get_image(ovoform_assets('images/ovoform.svg')),
            2
        );

        add_submenu_page(
            ovoform_route('admin.forms')->query_string,
            esc_html__('Forms', 'ovoform'),
            esc_html__('Forms', 'ovoform'),
            'manage_options',
            ovoform_route('admin.forms')->query_string,
            [new AdminRequestHandler(), 'handle']
        );
        add_submenu_page(
            ovoform_route('admin.forms')->query_string,
            esc_html__('Submissions', 'ovoform'),
            esc_html__('Submissions', 'ovoform').' '.$pendingView,
            'manage_options',
            ovoform_route('admin.forms.submissions')->query_string,
            [new AdminRequestHandler(), 'handle']
        );

        add_submenu_page(
            ovoform_route('admin.forms')->query_string,
            esc_html__('Captcha Setting', 'ovoform'),
            esc_html__('Captcha Setting', 'ovoform'),
            'manage_options',
            ovoform_route('admin.setting.index')->query_string,
            [new AdminRequestHandler(), 'handle']
        );
    }
}
