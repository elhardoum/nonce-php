<?php

namespace Nonce\Util;

/**
  * Fast PHP nonce and CSRF tokens tool
  *
  * @author elhardoum <i@elhardoum.com>
  * @version 0.3
  * @link http://github.com/elhardoum/nonce-php
  * @license GPL-3.0
  * @see https://github.com/elhardoum/nonce-php/blob/master/readme.md
  */
class Cookie
{
    // config context
    private static $config;

    /**
      * Pass config context to class
      *
      * @param \Nonce\Config\Base $config configuration class reference
      * @return void
      */

    public static function loadConfig( \Nonce\Config\Base $config )
    {
        self::$config = $config;
    }

    /**
      * Set a browser cookie
      *
      * @param string $name cookie name
      * @param string $value cookie value
      * @param int $expires cookie TTL in seconds
      * @return void
      */

    public static function set( string $name, string $value, int $expires=0 )
    {
        setcookie(
            $name,
            $value,
            $expires > 0 ? ( time() + $expires ) : 0,
            self::$config->getConfig('COOKIE_PATH'),
            self::$config->getConfig('COOKIE_DOMAIN'),
            false,
            true
        );
        $_COOKIE[$name] = $value;
    }

    /**
      * Retrieve a browser cookie
      *
      * @param string $name cookie name
      * @return mixed cookie value
      */

    public static function get( string $name )
    {
        return isset($_COOKIE[$name]) ? trim($_COOKIE[$name]) : null;  
    }

    /**
      * Unset a browser cookie
      *
      * @param string $name cookie name to be deleted
      * @return void
      */

    public static function delete( string $name )
    {
        setcookie(
            $name,
            ' ',
            time() -31536000, // -1 year
            self::$config->getConfig('COOKIE_PATH'),
            self::$config->getConfig('COOKIE_DOMAIN'),
            false,
            true
        );
        unset($_COOKIE[$name]);
    }
}