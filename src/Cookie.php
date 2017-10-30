<?php

namespace Nonce;

class Cookie
{
    static function set($name, $value, $expires=null)
    {
        setcookie(
            $name,
            $value,
            $expires ? (time()+$expires) : 0,
            Config::$COOKIE_PATH,
            Config::$COOKIE_DOMAIN,
            null,
            true
        );
        $_COOKIE[$name] = $value;
    }

    static function get($name)
    {
        return isset($_COOKIE[$name]) ? trim($_COOKIE[$name]) : null;  
    }

    static function delete($name)
    {
        setcookie(
            $name,
            ' ',
            time() -31536000, // -1 yr
            Config::$COOKIE_PATH,
            Config::$COOKIE_DOMAIN,
            null,
            true
        );
        unset($_COOKIE[$name]);
    }
}