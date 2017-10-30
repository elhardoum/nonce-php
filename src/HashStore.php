<?php

namespace Nonce;

/**
  * Fast PHP nonce and CSRF tokens tool
  *
  * @author Samuel Elh <samelh.com/contact>
  * @version 0.1
  * @link http://github.com/elhardoum/nonce-php
  * @link https://samelh.com
  * @license GPL-3.0
  * @see https://github.com/elhardoum/nonce-php/blob/master/readme.md
  */
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