<?php

namespace Ovoform\Hook;

use Ovoform\Hook\AdminMenu;
use Ovoform\Lib\VerifiedPlugin;
use Ovoform\Models\Form;

class Hook
{

    public function init()
    {
        add_action('admin_menu', [new AdminMenu, 'menuSetting']);

        add_action('init', [new ExecuteRouter, 'execute']);
        add_filter('template_include', [new ExecuteRouter, 'includeTemplate'], 1000, 1);
        add_action('query_vars', [new ExecuteRouter, 'setQueryVar']);

        $loadAssets = new LoadAssets('admin');
        add_action('admin_enqueue_scripts', [$loadAssets, 'enqueueScripts']);
        add_action('admin_enqueue_scripts', [$loadAssets, 'enqueueStyles']);

        $loadAssets = new LoadAssets('public');
        add_action('wp_enqueue_scripts', [$loadAssets, 'enqueueScripts']);
        add_action('wp_enqueue_scripts', [$loadAssets, 'enqueueStyles']);

        if (VerifiedPlugin::check()) { 
            $this->authHooks();
        }

        add_action('plugin_loaded', function () {
            load_plugin_textdomain(
                'ovoform',
                false,
                dirname(dirname(dirname(plugin_basename(__FILE__)))) . '/languages'
            );
        });

        add_action('wp_dashboard_setup', function () {
            $widget = new Widget();
            $widget->loadWidget();
        });

        add_filter('admin_body_class', function ($classes) {
            if (isset($_GET['page']) && $_GET['page'] == 'ovoform') {
                $classes .= ' vl-admin';
            }
            return $classes;
        });

        add_action('init', function () {
            ob_start();
        });

        add_filter('redirect_canonical', function ($redirect_url) {
            if (is_404()) {
                return false;
            }
            return $redirect_url;
        });


        if (ovoform_admin_plugin_page()) {
            add_filter('admin_body_class', function ($classes) {
                $classes .= ' ' . 'ovoform' . '-admin ';
                return $classes;
            });
        }

        add_action("admin_footer", function () {
            ovoform_include('deactivate_modal');
        });

        add_action('admin_enqueue_scripts', function(){
            wp_enqueue_media();
        });

        add_shortcode('ovoform-form',function($args){
            $attributes = shortcode_atts(array(
                'id' => '',
                'title' => ''
            ), $args);

            $form     = Form::where('id', $attributes['id'])->first();
            if(!$form){
                return '<div class="alert alert-danger">Please provide correct form ID</div>';
            }

            ob_start();
            echo '<div class="row gy-4">';
            ovoform_include('forms', ['attributes' => $attributes]);
            echo '</div>';
            return ob_get_clean();
            
        });
    }

    public function authHooks()
    {
        $authorization = new Authorization;
        add_action('after_setup_theme', [$authorization, 'removeAdminBar']);
        add_action('admin_init', [$authorization, 'redirectHome'], 1);
        add_action('wp_login_failed', [$authorization, 'authFailed']);
        add_filter('authenticate', [$authorization, 'authenticate'], 20, 3);
        add_filter('wp_authenticate_user', [$authorization, 'verifyUser'], 1);
        add_action('edit_user_profile', [$authorization, 'userProfile']);
        add_action('edit_user_profile_update', [$authorization, 'updateUserProfile']);
    }
}
