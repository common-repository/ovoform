<?php

namespace Ovoform\Lib;

use Ovoform\Models\Extension;

class Captcha{
    
    /*
    |--------------------------------------------------------------------------
    | Captcha
    |--------------------------------------------------------------------------
    |
    | This class is using verify and show captcha. Here is currently available
    | custom captcha and google recaptcha2. Developer can use verify method
    | to verify all captcha or can use separately if required
    |
    */

    /**
    * Google recaptcha2 script
    *
    * @return string
    */
    public static function reCaptcha(){
        $reCaptcha = Extension::where('act', 'google-recaptcha2')->where('status', 1)->first();
        if ($reCaptcha) {
            $script = $reCaptcha->script;
            $shortCodes = json_decode($reCaptcha->shortcode);
            foreach ($shortCodes as $key => $item) {
                $script = str_replace('{{' . $key . '}}', $item->value, $script);
            }
            return $script;
        }
        return null;
        return $reCaptcha ? $reCaptcha->generateScript() : null;
    }

    /**
    * Custom captcha script
    *
    * @return string
    */
    public static function customCaptcha($width = '100%', $height = 46, $bgColor = '#003'){

        $textColor = 'red';
        $captcha = Extension::where('act', 'custom-captcha')->where('status', 1)->first();
        if (!$captcha) {
            return 0;
        }
        $code = rand(100000, 999999);
        $char = str_split($code);
        $shortCodes = json_decode($captcha->shortcode);
        $ret = '<link href="https://fonts.googleapis.com/css?family=Henny+Penny&display=swap" rel="stylesheet">';
        $ret .= '<div style="height: ' . $height . 'px; line-height: ' . $height . 'px; width:' . $width . '; text-align: center; background-color: ' . $bgColor . '; color: ' . $textColor . '; font-size: ' . ($height - 20) . 'px; font-weight: bold; letter-spacing: 20px; font-family: \'Henny Penny\', cursive;  -webkit-user-select: none; -moz-user-select: none;-ms-user-select: none;user-select: none;  display: flex; justify-content: center;">';
        foreach ($char as $value) {
            $ret .= '<span style="    float:left;     -webkit-transform: rotate(' . rand(-60, 60) . 'deg);">' . $value . '</span>';
        }
        $ret .= '</div>';
        $captchaSecret = hash_hmac('sha256', $code, $shortCodes->random_key->value);
        $ret .= '<input type="hidden" name="captcha_secret" value="' . $captchaSecret . '">';
        return $ret;
    }

    /**
    * Verify all captcha
    *
    * @return boolean
    */
    public static function verify(){
        $gCaptchaPass = self::verifyGoogleCaptcha();
        $cCaptchaPass = self::verifyCustomCaptcha();
        if ($gCaptchaPass && $cCaptchaPass) {
            return true;
        }
        return false;
    }

    /**
    * Verify google recaptcha2
    *
    * @return boolean
    */
    public static function verifyGoogleCaptcha() {
        $pass = true;
        $googleCaptcha = Extension::where('act', 'google-recaptcha2')->where('status', 1)->first();
    
        if ($googleCaptcha) {
            $secretKey = json_decode($googleCaptcha->shortcode)->secret_key->value;
            $recaptchaResponse = ovoform_request()->{'g-recaptcha-response'};
            $remoteIp = ovoform_real_ip();
    
            $url = "https://www.google.com/recaptcha/api/siteverify?secret={$secretKey}&response={$recaptchaResponse}&remoteip={$remoteIp}";
    
            $response = wp_remote_get($url);
    
            if (!is_wp_error($response)) {
                $body = wp_remote_retrieve_body($response);
                $resp = json_decode($body, true);
    
                if (!$resp['success']) {
                    $pass = false;
                }
            }
        }
    
        return $pass;
    }
    

    /**
    * Verify custom captcha
    *
    * @return boolean
    */
    public static function verifyCustomCaptcha(){
        $pass = true;
        $customCaptcha = Extension::where('act', 'custom-captcha')->where('status', 1)->first();
        if ($customCaptcha) {
            $captchaSecret = hash_hmac('sha256', ovoform_request()->captcha, json_decode($customCaptcha->shortcode)->random_key->value);
            if ($captchaSecret != ovoform_request()->captcha_secret) {
                $pass = false;
            }
        }
        return $pass;
    }

}
