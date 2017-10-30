<?php

namespace Nonce;

use RandomLib\Factory as RandomLibFactory;
use SecurityLib\Strength;

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
class Nonce
{
    private static $verifying;

    private static function token()
    {
        if ( $csrf = Cookie::get('CSRF') ) {
            return $csrf . Config::$SALT;
        } else {
            $csrf = self::randChar(33);
            Cookie::set('CSRF', $csrf, Config::$CSRF_EXPIRE);
            return $csrf . Config::$SALT;
        }
    }

    static function create($action, $expire=null)
    {
        $hash = self::getHash($action);

        if ( !self::$verifying && $hash ) {
            $expire = (int) $expire ? (int) $expire : Config::$NONCE_EXPIRE;
            HashStore::set(self::name($hash), 1, $expire);
        }

        return $hash;
    }

    private static function getHash($action)
    {
        $hash = hash(Config::$TOKEN_HASHER, $action . self::token());
        return substr($hash, 0, Config::$CHAR_LIMIT);
    }

    static function verify($nonce, $action)
    {
        self::$verifying = true;
        $hash = self::create($action);
        self::$verifying = null;

        if ( $hash != $nonce )
            return false;

        return (bool) HashStore::get(self::name($hash));
    }

    static function deleteHash($hash)
    {
        return HashStore::delete(self::name($hash));
    }

    static function delete($action)
    {
        return self::deleteHash( self::getHash($action) );
    }

    static function instance($user=null)
    {
        static $instance = null;
        
        if ( null === $instance ) {
            $instance = new Nonce;
        }

        return $instance;
    }

    static function name($hash)
    {
        return Config::$HASH_NAME_LENGTH <= 0 ? $hash : substr($hash, 0, Config::$HASH_NAME_LENGTH);
    }

    private static function randChar($length=16)
    {
        $factory = new RandomLibFactory;
        $generator = $factory->getGenerator(new Strength(Strength::MEDIUM));
        
        return $generator->generateString($length);
    }
}