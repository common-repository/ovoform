<?php

use Ovoform\BackOffice\Abort;
use Ovoform\BackOffice\Facade\DB;
use Ovoform\BackOffice\Facade\Session as FacadeSession;
use Ovoform\Lib\FileManager;
use Ovoform\BackOffice\Request;
use Ovoform\BackOffice\Session;
use Ovoform\BackOffice\System;
use Ovoform\Lib\Captcha;
use Ovoform\Lib\ViserDate;
use Ovoform\Models\Form;
use Ovoform\Models\FormInfo;

if (!function_exists('ovoform_system_details')) {
    function ovoform_system_details()
    {
        $system['prefix'] = 'wp_';
        $system['real_name'] = 'ovoform';
        $system['name'] = $system['prefix'] . 'ovoform';
        $system['version'] = '1.0';
        $system['build_version'] = '1.1.6';
        return $system;
    }
}

if (!function_exists('ovoform_system_instance')) {
    function ovoform_system_instance()
    {
        return System::getInstance();
    }
}

if (!function_exists('ovoform_dd')) {
    function ovoform_dd(...$data)
    {
        foreach ($data as $item) {
            echo "<pre style='background: #001140;color: #00ff4e;padding: 20px;'>";
            print_r($item);
            echo "</pre>";
        }
        exit;
    }
}

if (!function_exists('ovoform_dump')) {
    function ovoform_dump(...$data)
    {
        foreach ($data as $item) {
            echo "<pre style='background: #001140;color: #00ff4e;padding: 20px;'>";
            print_r($item);
            echo "</pre>";
        }
    }
}

if (!function_exists('ovoform_layout')) {
    function ovoform_layout($ovoform_layout)
    {
        global $systemLayout;
        $systemLayout = $ovoform_layout;
    }
}

if (!function_exists('ovoform_route')) {
    function ovoform_route($routeName)
    {
        $route = ovoform_system_instance()->route($routeName);
        return ovoform_to_object($route);
    }
}

if (!function_exists('ovoform_to_object')) {
    function ovoform_to_object($args)
    {
        if (is_array($args)) {
            return (object) array_map(__FUNCTION__, $args);
        } else {
            return $args;
        }
    }
}

if (!function_exists('ovoform_to_array')) {
    function ovoform_to_array($args)
    {
        if (is_object($args)) {
            $args = get_object_vars($args);
        }

        if (is_array($args)) {
            return array_map(__FUNCTION__, $args);
        } else {
            return $args;
        }
    }
}


if (!function_exists('ovoform_redirect')) {
    function ovoform_redirect($url, $notify = null)
    {
        if ($notify) {
            ovoform_set_notify($notify);
        }
        wp_redirect($url);
        exit;
    }
}

if (!function_exists('ovoform_key_to_title')) {
    function ovoform_key_to_title($text)
    {
        return ucfirst(preg_replace("/[^A-Za-z0-9 ]/", ' ', $text));
    }
}

if (!function_exists('ovoform_request')) {
    function ovoform_request()
    {
        return new Request();
    }
}

if (!function_exists('ovoform_session')) {
    function ovoform_session()
    {
        return new Session();
    }
}

if (!function_exists('ovoform_back')) {
    function ovoform_back($notify = null)
    {
        if (isset($_SERVER['HTTP_REFERER']) && filter_var($_SERVER['HTTP_REFERER'], FILTER_VALIDATE_URL)) {
            $url = sanitize_text_field($_SERVER['HTTP_REFERER']);
        } else {
            $url = home_url();
        }
        ovoform_redirect($url, $notify);
    }
}

if (!function_exists('ovoform_old')) {
    function ovoform_old($key)
    {
        return FacadeSession::get('old_input_value_' . $key);
    }
}

if (!function_exists('ovoform_abort')) {
    function ovoform_abort($code = 404, $message = null)
    {
        $abort = new Abort($code, $message);
        $abort->abort();
    }
}

if (!function_exists('ovoads_query_to_url')) {
    function ovoads_query_to_url($arr)
    {
        return esc_url(add_query_arg($arr, sanitize_url(wp_unslash($_SERVER['REQUEST_URI']))));
    }
}
if (!function_exists('ovoform_set_notify')) {
    function ovoform_set_notify($data)
    {
        FacadeSession::flash('notify', $data);
    }
}

if (!function_exists('ovoform_include')) {
    function ovoform_include($view, $data = [])
    {
        extract($data);
        include OVOFORM_ROOT . 'views/' . $view . '.php';
    }
}

if (!function_exists('ovoform_real_ip')) {
    function ovoform_real_ip()
    {
        $ip = filter_var(@$_SERVER['REMOTE_ADDR'], FILTER_VALIDATE_IP);
        //Deep detect ip
        if (filter_var(@$_SERVER['HTTP_FORWARDED'], FILTER_VALIDATE_IP)) {
            $ip = sanitize_text_field($_SERVER['HTTP_FORWARDED']);
        }
        if (filter_var(@$_SERVER['HTTP_FORWARDED_FOR'], FILTER_VALIDATE_IP)) {
            $ip = sanitize_text_field($_SERVER['HTTP_FORWARDED_FOR']);
        }
        if (filter_var(@$_SERVER['HTTP_X_FORWARDED_FOR'], FILTER_VALIDATE_IP)) {
            $ip = sanitize_text_field($_SERVER['HTTP_X_FORWARDED_FOR']);
        }
        if (filter_var(@$_SERVER['HTTP_CLIENT_IP'], FILTER_VALIDATE_IP)) {
            $ip = sanitize_text_field($_SERVER['HTTP_CLIENT_IP']);
        }
        if (filter_var(@$_SERVER['HTTP_X_REAL_IP'], FILTER_VALIDATE_IP)) {
            $ip = sanitize_text_field($_SERVER['HTTP_X_REAL_IP']);
        }
        if (filter_var(@$_SERVER['HTTP_CF_CONNECTING_IP'], FILTER_VALIDATE_IP)) {
            $ip = sanitize_text_field($_SERVER['HTTP_CF_CONNECTING_IP']);
        }
        if ( $ip == '::1') {
            $ip = '127.0.0.1';
        }

        return $ip;
    }
}

if (!function_exists('ovoform_route_link')) {
    function ovoform_route_link($name, $format = true, $pageName = null)
    {
        $route = ovoform_to_array(ovoform_route($name));
        if (array_key_exists('query_string', $route)) {
            if (!$pageName) {
                $pageUrl = menu_page_url('ovoform', false);
            } else {
                $pageUrl = menu_page_url($pageName, false);
            }
            if ($pageName != $route['query_string']) {
                $link = $pageUrl . '&module=' . $route['query_string'];
            } else {
                $link = $pageUrl;
            }
        } else {
            $link = home_url($route['uri']);
        }
        if ($format) {
            return esc_url($link);
        }
        return esc_url($link);
    }
}

if (!function_exists('ovoform_menu_active')) {
    function ovoform_menu_active($routeName, $extra = null)
    {
        $class = 'active';
        if (!is_array($routeName)) {
            $routeName = [$routeName];
        }
        if (is_array($extra)) {
            $routeName =  array_merge($routeName, $extra);
        }
        foreach ($routeName as $key => $value) {
            $route = ovoform_route($value);
            $queryString = $route->query_string;
            $uri = $route->uri ?? '';
            if ($queryString) {
                $currentModule = isset(ovoform_request()->module) ? ovoform_request()->module : null;
                if ($currentModule == $queryString) {
                    return sanitize_html_class($class);
                } else if (!$currentModule) {
                    $currentPage = isset(ovoform_request()->page) ? ovoform_request()->page : null;
                    if ($currentPage ==  $queryString) return sanitize_html_class($class);
                }
            } else {
                $currentUri = get_query_var('ovoform_page');
                if ($currentUri == $uri) {
                    return sanitize_html_class($class);
                }
            }
        }
    }
}

if (!function_exists('ovoform_nonce_field')) {
    function ovoform_nonce_field($routeName, $isPrint = true)
    {
        $nonce = ovoform_nonce($routeName);
        if ($isPrint) {
            echo '<input type="hidden" name="nonce" value="' . sprintf('%s', esc_html($nonce))  . '">';
        } else {
            return '<input type="hidden" name="nonce" value="' . sprintf('%s', esc_html($nonce)) . '">';
        }
    }
}


if (!function_exists('ovoform_nonce')) {
    function ovoform_nonce($routeName)
    {
        $route = ovoform_to_array(ovoform_route($routeName));
        if (array_key_exists('query_string', $route)) {
            $nonceName = $route['query_string'];
        } else {
            $nonceName = $route['uri'];
        }
        return wp_create_nonce($nonceName);
    }
}

// if (!function_exists('ovoform_current_route')) {
//     function ovoform_current_route()
//     {
//         if (ovoform_request()->page ?? null) {
//             if (ovoform_request()->module ?? null) {
//                 return ovoform_request()->module;
//             } else {
//                 return ovoform_request()->page;
//             }
//         } else {
//             return home_url(get_query_var('ovoform_page'));
//         }
//     }
// }

if (!function_exists('ovoform_current_route')) {
    function ovoform_current_route()
    {
        $page = ovoform_request()->page;
        if (isset($page)) {
            $module = ovoform_request()->module;
            if (isset($module)) {
                return ovoform_request()->module;
            } else {
                return ovoform_request()->page;
            }
        } else {
            return home_url(get_query_var('ovoform_page'));
        }
    }
}


if (!function_exists('ovoform_assets')) {
    function ovoform_assets($path)
    {
        $path = 'ovoform' . '/assets/' . $path;
        $path = str_replace('//', '/', $path);
        return plugins_url($path);
    }
}

if (!function_exists('ovoform_get_image')) {
    function ovoform_get_image($image)
    {
        $checkPath = str_replace(plugin_dir_url(dirname(dirname(__FILE__))), plugin_dir_path(dirname(dirname(__FILE__))), $image);
        if (file_exists($checkPath) && is_file($checkPath)) {
            return $image;
        }
        return ovoform_assets('images/default.png');
    }
}

if (!function_exists('ovoform_file_uploader')) {
    function ovoform_file_uploader($file, $location, $size = null, $old = null, $thumb = null)
    {
        $fileManager = new FileManager($file);
        $fileManager->path = $location;
        $fileManager->size = $size;
        $fileManager->old = $old;
        $fileManager->thumb = $thumb;
        $fileManager->upload();
        return $fileManager->filename;
    }
}

if (!function_exists('ovoform_file_manager')) {
    function ovoform_file_manager()
    {
        return new FileManager();
    }
}


if (!function_exists('ovoform_file_path')) {
    function ovoform_file_path($key)
    {
        $dir = plugin_dir_url(dirname(dirname(__FILE__)));
        if (!empty($_FILES) || !empty($_POST)) {
            $dir = plugin_dir_path(dirname(dirname(__FILE__)));
        }
        return $dir . 'assets/' . ovoform_file_manager()->$key()->path;
    }
}

if (!function_exists('ovoform_file_size')) {
    function ovoform_file_size($key)
    {
        return ovoform_file_manager()->$key()->size;
    }
}

if (!function_exists('ovoform_file_ext')) {
    function ovoform_file_ext($key)
    {
        return ovoform_file_manager()->$key()->extensions;
    }
}

if (!function_exists('ovoform_check_empty')) {
    function ovoform_check_empty($data)
    {
        if (is_object($data)) {
            $data = (array) $data;
        }
        return empty($data);
    }
}

if (!function_exists('ovoform_allowed_html')) {
    function ovoform_allowed_html()
    {
        $arr = array(
            'span' => array(
                'class' => []
            ),
            'br' => [],
            'a' => array(
                'href' => true,
                'class' => [],
            ),
            'em' => array(),
            'b' => array(),
            'bold' => array(),
            'blockquote' => array(),
            'p' => array(),
            'li' => array(
                'class' => [],
                'id' => []
            ),
            'ol' => array(),
            'strong' => array(),
            'ul' => array(
                'id' => [],
                'class' => [], 1
            ),
            'div' => array(
                'id' => [],
                'class' => [], 1
            ),
            'img' => array(
                'src' => true
            ),
            'table' => [],
            'tr' => [],
            'td' => [],
            'i' => array(
                'class' => []
            )
        );
        return $arr;
    }
}

if (!function_exists('ovoform_currency')) {
    function ovoform_currency($type = 'text')
    {
        return get_option("ovoform_cur_$type");
    }
}

if (!function_exists('ovoform_get_amount')) {
    function ovoform_get_amount($amount, $length = 2)
    {
        $amount = round($amount, $length);
        return $amount + 0;
    }
}

if (!function_exists('ovoform_show_amount')) {
    function ovoform_show_amount($amount, $decimal = 2, $separate = true, $exceptZeros = false)
    {
        $separator = '';
        if ($separate) {
            $separator = ',';
        }
        $printAmount = number_format($amount, $decimal, '.', $separator);
        if ($exceptZeros) {
            $exp = explode('.', $printAmount);
            if ($exp[1] * 1 == 0) {
                $printAmount = $exp[0];
            } else {
                $printAmount = rtrim($printAmount, '0');
            }
        }
        return $printAmount;
    }
}

if (!function_exists('ovoform_global_notify_short_codes')) {
    function ovoform_global_notify_short_codes()
    {
        $data['site_name'] = 'Name of your site';
        $data['site_currency'] = 'Currency of your site';
        $data['currency_symbol'] = 'Symbol of currency';
        return $data;
    }
}

if (!function_exists('ovoform_show_date_time')) {
    function ovoform_show_date_time($date, $format = 'Y-m-d h:i A')
    {
        return ovoform_date()->parse($date)->toDateTime($format);
    }
}

if (!function_exists('ovoform_diff_for_humans')) {
    function ovoform_diff_for_humans($date, $to = '')
    {
        if (empty($to)) {
            $to = current_time('timestamp');
        }
        $from = strtotime($date);
        return human_time_diff($from, $to) . " ago";
    }
}

if (!function_exists('ovoform_auth')) {
    function ovoform_auth()
    {
        include_once(ABSPATH . 'wp-includes/pluggable.php');
        if (is_user_logged_in()) {
            return (object)[
                'user' => wp_get_current_user(),
                'meta' => get_user_meta(wp_get_current_user()->ID)
            ];
        }
        return false;
    }
}

if (!function_exists('ovoform_trx')) {
    function ovoform_trx($length = 12)
    {
        $characters = 'ABCDEFGHJKMNOPQRSTUVWXYZ123456789';
        $charactersLength = strlen($characters);
        $randomString = '';
        for ($i = 0; $i < $length; $i++) {
            $randomString .= $characters[rand(0, $charactersLength - 1)];
        }
        return $randomString;
    }
}

if (!function_exists('ovoform_asset')) {
    function ovoform_asset($path)
    {
        $path = 'ovoform' . '/assets/' . $path;
        $path = str_replace('//', '/', $path);
        return plugins_url($path);
    }
}

if (!function_exists('ovoform_get_form')) {
    function ovoform_get_form($formId)
    {
        $form = Form::find($formId);
        $formData = [];
        if ($form) {
            $formData = maybe_unserialize($form->form_data);
        }
        extract($formData);
        include OVOFORM_ROOT . 'views/form/form.php';
    }
}

if (!function_exists('ovoform_title_to_key')) {
    function ovoform_title_to_key($text)
    {
        return strtolower(str_replace(' ', '_', $text));
    }
}

if (!function_exists('ovoform_encrypt')) {
    function ovoform_encrypt($string)
    {
        return base64_encode($string);
    }
}

if (!function_exists('ovoform_decrypt')) {
    function ovoform_decrypt($string)
    {
        return base64_decode($string);
    }
}


if (!function_exists('ovoform_paginate')) {
    function ovoform_paginate($num = 20)
    {
        return intval($num);
    }
}


if (!function_exists('ovoform_date')) {
    function ovoform_date()
    {
        return new ViserDate();
    }
}

if (!function_exists('ovoform_re_captcha')) {
    function ovoform_re_captcha()
    {
        return Captcha::reCaptcha();
    }
}

if (!function_exists('ovoform_custom_captcha')) {
    function ovoform_custom_captcha($width = '100%', $height = 46, $bgColor = '#003')
    {
        return Captcha::customCaptcha($width, $height, $bgColor);
    }
}

if (!function_exists('ovoform_verify_captcha')) {
    function ovoform_verify_captcha()
    {
        return Captcha::verify();
    }
}

if (!function_exists('ovoform_str_limit')) {
    function ovoform_str_limit($str, $length = 100, $end = '...')
    {

        if (mb_strwidth($str, 'UTF-8') <= $length) {
            return $str;
        }

        return rtrim(mb_strimwidth($str, 0, $length, '', 'UTF-8')) . $end;
    }
}

if (!function_exists('ovoform_db_prefix')) {
    function ovoform_db_prefix()
    {
        return DB::tablePrefix();
    }
}

if (!function_exists('ovoform_db_wpdb')) {
    function ovoform_db_wpdb()
    {
        return DB::wpdb();
    }
}

if (!function_exists('ovoform_active_user')) {
    function ovoform_active_user($userId)
    {
        $active = get_user_meta($userId, 'ovoform_ban');
        if ($active == 0) {
            return false;
        }
        return 1;
    }
}

if (!function_exists('ovoform_stack')) {
    function ovoform_stack($hookName)
    {
        do_action($hookName);
    }
}

if (!function_exists('ovoform_push')) {
    function ovoform_push($hookName, $param)
    {
        add_action($hookName, function () use ($param) {
            echo wp_kses_post($param);
        });
    }
}

if (!function_exists('ovoform_topnav')) {
    function ovoform_topnav($key, $pageName)
    {
        $json = json_decode(file_get_contents(OVOFORM_ROOT . 'views/admin/partials/topnav.json'));
        $navs = $json->$key;

        $html = '';
        foreach ($navs as $nav) {
            $html .= '<li class="' . ovoform_menu_active($nav->route, isset($nav->extra) ? $nav->extra : null) . '">
                        <a href="' . esc_url(ovoform_route_link($nav->route, pageName: $pageName)) . '">
                            <i class="' . $nav->icon . '"></i>
                            <span class="menu-title">' . printf(esc_html__('%s', 'ovoform'), esc_html($nav->name)) . '</span>
                        </a>
                    </li>';
        }
        ovoform_push('ovoform_topnav', $html);
    }
}

if (!function_exists('ovoform_admin_plugin_page')) {
    function ovoform_admin_plugin_page(){
        $pageName = ovoform_request()->page;
        if ($pageName) {
            $pluginPage = explode('_',$pageName)[0];
            if ($pluginPage == 'ovoform') {
                return true;
            }else{
                return false;
            }
        }
    }
}

if (!function_exists('ovoform_push_breadcrumb')) {
    function ovoform_push_breadcrumb($html)
    {
        add_action('ovoform_breadcrumb_plugins', function () use ($html) {
            echo wp_kses_post($html);
        });
    }
}


if (!function_exists('ovoform_get_media_file')) {
    function ovoform_get_media_file($postId){
        return home_url().'/wp-content/uploads/'.get_post_meta($postId,'_wp_attached_file',true);
    }
}


if (!function_exists('ovoform_get_form_title_by_id')) {
    function ovoform_get_form_title_by_id($id){
        $form=FormInfo::where('id',$id)->first();
        $formName=$form->name;
        return $formName;
    }
}

if (!function_exists('ovoform_admin_top')) {
    function ovoform_admin_top($pageTitle, $html = null){
        ovoform_include('admin/layouts/top', compact('pageTitle', 'html'));
    }
}

if (!function_exists('ovoform_admin_bottom')) {
    function ovoform_admin_bottom(){
        ovoform_include('admin/layouts/bottom');
    }
}