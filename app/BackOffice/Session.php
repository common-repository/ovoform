<?php

namespace Ovoform\BackOffice;

class Session{
    public function __construct()
    {
        if (session_status() != PHP_SESSION_ACTIVE) {
            session_start();
        }
    }

    public function has($key)
    {
        $flashKey = $key.'.____flash';
        if (isset($_SESSION[$flashKey])) {
            return true;
        }
        if (isset($_SESSION[$key])) {
            return true;
        }
        return false;
    }

    public function put($key,$value)
    {
        $_SESSION[$key] = $value;
    }

    public function get($key)
    {
        if (session_status() != PHP_SESSION_ACTIVE) {
            session_start();
        }
        $flashKey = $key.'.____flash';
        $hasFlash = false;
        if (isset($_SESSION[$flashKey])) {
            $hasFlash = true;
            $key = $flashKey;
        }
        if (isset($_SESSION[$key])) {
            $sessionValue = $this->sanitizeSession($_SESSION[$key]);
        }
        if ($hasFlash) {
            $this->forget($key);
        }
        if (isset($sessionValue)) {
            return $sessionValue;
        }
    }

    private function sanitizeSession($value)
    {
        return is_array($value) ? array_map([$this, 'sanitizeSession'], $value) : htmlspecialchars($value, ENT_QUOTES, 'UTF-8');
    }

    public function flash($key,$value)
    {
        $this->put($key.'.____flash',$value);
    }

    public function forget($key)
    {
        unset($_SESSION[$key]);
    }
}