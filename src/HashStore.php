<?php

namespace Nonce;

class HashStore
{
    static function set($name, $value, $expires=null)
    {
        return call_user_func_array(Config::storeContextSet(), func_get_args());
    }

    static function get($name)
    {
        return call_user_func_array(Config::storeContextGet(), func_get_args());
    }

    static function delete($name)
    {
        return call_user_func_array(Config::storeContextDelete(), func_get_args());
    }
}