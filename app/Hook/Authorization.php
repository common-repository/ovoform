<?php

namespace Ovoform\Hook;

class Authorization{
    public function removeAdminBar()
    {
        if (!current_user_can('administrator') && !is_admin()) {
            show_admin_bar(false);
        }
    }

    public function redirectHome()
    {
        if ((!defined('DOING_AJAX') || !DOING_AJAX) && !current_user_can('administrator') && !current_user_can('editor')) {
            wp_redirect( home_url('/') );
            exit;
        }
    }

    public function restrictWpLogin()
    {
        if (!isset($_REQUEST) || empty($_REQUEST) || @$_GET['action'] == 'register') {
            wp_redirect( home_url() );
            exit;
        }
    }

    public function redirectLogin($loginUrl, $redirect)
    {
        $loginUrl = ovoform_route_link('user.login');

        if (!empty($redirect)) {
            //prevent duplicate redirect_to parameters
            $duplicate_redirect = substr_count($redirect, 'redirect_to');
            if ($duplicate_redirect >= 1) {
                $redirect = substr($redirect, 0, (strrpos($redirect, '?')));
            }

            $loginUrl = add_query_arg('redirect_to', rawurlencode($redirect), $loginUrl);
        } else {
            $loginUrl = add_query_arg('redirect_to', rawurlencode(home_url('/')), $loginUrl);
        }
        return $loginUrl;
    }

    function authFailed($username)
    {
        //Redirect login page if login failed
        $referrer = wp_get_referer();

        if ($referrer == ovoform_route_link('user.login') ) $referrer = $referrer . '?redirect_to=' . home_url('/'.get_option('ovoform_user_panel_prefix')); // in rare case where user access /login/ page directly

        if (!empty($referrer) && !strstr($referrer, 'wp-login') && !strstr($referrer, 'wp-admin') && (!defined('DOING_AJAX') || !DOING_AJAX)) {
            //notify unverified users to activate their account
            $userdata = get_user_by('login', $username);
            $verify = get_user_meta(@$userdata->ID, '_Verify Email', true);
            //user with verified email do not have this usermeta field
            
            $cleanUrl = add_query_arg(['login' => 'failed'], $referrer);

            if ($verify != '') {
                $cleanUrl = add_query_arg(['login' => 'failed', 'email' => 'unverified'], $referrer);
            }
            wp_safe_redirect($cleanUrl);
            exit;
        }

        // Redirect if login failed 
        wp_safe_redirect(add_query_arg(['login' => 'failed'], $referrer));
        exit;
    }

    function authenticate($user, $username, $password)
    {
        //Allow login using email
        if (is_email($username)) {
            $user = get_user_by('email', $username);
            if ($user) $username = $user->user_login;
            return wp_authenticate_username_password(null, $username, $password);
        }

        return $user;
    }


    function verifyUser($userdata)
    {
        //Check whether user verified their email
        $verify = get_user_meta($userdata->ID, '_Verify Email', true);
        //user with verified email do not have this usermeta field

        if ($verify != '') {
            ovoform_redirect( home_url('login?email=unverified') );
        }
        return $userdata;
    }

    function userProfile($user)
    {
        if ('' != $verify_email = get_the_author_meta('_Verify Email', $user->ID)) {
            $verification_link = '';?>
            <table class="form-table">
                <tr>
                    <th><label for="emailverify"><?php esc_html_e('Email Verification Link', 'ovoform');?></label></th>
                    <td>
                        <?php $verification_link .= sprintf('%s?email=verify&login=%s&key=%s', ovoform_route_link('user.login'), rawurlencode($user->user_login), $verify_email); ?>
                        <input type="text" name="_Verify_Email" id="_Verify_Email" value="<?php echo esc_url($verification_link); ?>" class="regular-text" /><br />
                        <span class="description"><?php esc_html_e('Leave blank to allow user to login without email verification.', 'ovoform');?></span>
                    </td>
                </tr>
            </table>
        <?php
        }
    }


    function updateUserProfile($user_id)
    {
        if (!$_POST['_Verify_Email']) {
            delete_user_meta($user_id, '_Verify Email');
        }
    }
}