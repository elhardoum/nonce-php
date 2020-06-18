<?php

namespace Nonce;

use Nonce\Config\Base as Config;
use Nonce\HashStore\Store;
use Nonce\Util\Cookie as CookieUtil;

/**
  * Fast PHP nonce and CSRF tokens tool
  *
  * @author Samuel Elh <i@elhardoum.com>
  * @version 0.2
  * @link http://github.com/elhardoum/nonce-php
  * @link https://samelh.com
  * @license GPL-3.0
  * @see https://github.com/elhardoum/nonce-php/blob/master/readme.md
  */

class Nonce
{
    private $config;
    private $store;

    public function __construct(Config $config, Store $store)
    {
        $this->config = $config;
        $this->store = $store;

        // pass cookie configuration to cookie class
        CookieUtil::loadConfig( $config );
    }

    public function getOrGenerateUserHashToken() : string
    {
        if ( $csrf = CookieUtil::get( $this->config->getConfig('CSRF_COOKIE_NAME') ) ) {
            return $csrf . $this->config->getConfig('RANDOM_SALT');
        } else {
            // generate a random character
            $csrf = $this->getRandomCharacter(33);

            // store CSRF temporarily in a browser cookie
            CookieUtil::set($this->config->getConfig('CSRF_COOKIE_NAME'), $csrf, $this->config->getConfig('CSRF_COOKIE_TTL'));

            return $csrf . $this->config->getConfig('RANDOM_SALT');
        }
    }

    public function create(string $action, int $expire_seconds=0, bool $skip_storage=false) : string
    {
        $hash = $this->encryptUserString($action);

        if ( ! $skip_storage && $hash ) {
            $expire = $expire_seconds > 0 ? $expire_seconds : $this->config->getConfig('NONCE_DEFAULT_TTL');
            $this->store->setKey($this->trimNonceIdHash($hash), 1, $expire);
        }

        return $hash;
    }

    protected function encryptUserString(string $value) : string
    {
        $hash = hash($this->config->getConfig('TOKEN_HASHER_ALGO'), $value . $this->getOrGenerateUserHashToken());
        return substr($hash, 0, $this->config->getConfig('NONCE_HASH_CHARACTER_LIMIT'));
    }

    public function verify(string $nonce, string $action) : bool
    {
        $hash = $this->create( $action, 0, true );

        if ( $hash !== $nonce )
            return false;

        return (bool) $this->store->getKey($this->trimNonceIdHash($hash));
    }

    public function deleteHashFromStore(string $hash)
    {
        return $this->store->deleteKey($this->trimNonceIdHash($hash));
    }

    public function delete(string $action)
    {
        return $this->deleteHashFromStore( $this->encryptUserString($action) );
    }

    public function trimNonceIdHash(string $hash) : string
    {
        return $this->config->getConfig('HASH_ID_CHARACTRER_LIMIT') <= 0
            ? $hash
            : substr($hash, 0, $this->config->getConfig('HASH_ID_CHARACTRER_LIMIT'));
    }

    protected function getRandomCharacter(int $length=16) : string
    {
        $factory = new \RandomLib\Factory;

        $generator = $factory->getGenerator(new \SecurityLib\Strength(
            \SecurityLib\Strength::MEDIUM
        ));
        
        return $generator->generateString($length);
    }
}