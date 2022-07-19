<?php

namespace Nonce;

use Nonce\Config\Base as Config;
use Nonce\HashStore\Store;
use Nonce\Util\Cookie as CookieUtil;

/**
  * Fast PHP nonce and CSRF tokens tool
  *
  * @author elhardoum <i@elhardoum.com>
  * @version 0.3
  * @link http://github.com/elhardoum/nonce-php
  * @license GPL-3.0
  * @see https://github.com/elhardoum/nonce-php/blob/master/readme.md
  */

class Nonce
{
    private $config;
    private $store;

    /**
      * Instantiate class
      *
      * @param Config $config configuration context
      * @param Store $config temporary store context
      * @return void
      */

    public function __construct(Config $config, Store $store)
    {
        $this->config = $config;
        $this->store = $store;

        // pass cookie configuration to cookie class
        CookieUtil::loadConfig( $config );
    }

    /**
      * Get or create a CSRF token for the current HTTP request
      *
      * @return string new or existing CSRF token
      */

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

    /**
      * Create a nonce based on an action string
      *
      * @param string $action an action for the nonce
      * @param int $expire_seconds TTL seconds for the product hash
      * @param bool $skip_storage store or don't store the product hash 
      * @return string generated nonce
      */

    public function create(string $action, int $expire_seconds=0, bool $skip_storage=false) : string
    {
        $hash = $this->encryptUserString($action);

        if ( ! $skip_storage && $hash ) {
            $expire = $expire_seconds > 0 ? $expire_seconds : $this->config->getConfig('NONCE_DEFAULT_TTL');
            $this->store->setKey($this->trimNonceIdHash($hash), 1, $expire);
        }

        return $hash;
    }

    /**
      * Encrypt a string using configured encryption algorithm seeded with the request CSRF token
      *
      * @param string $value string to be encrypted
      * @return string encrypted characters
      */

    protected function encryptUserString(string $value) : string
    {
        $hash = hash($this->config->getConfig('TOKEN_HASHER_ALGO'), $value . $this->getOrGenerateUserHashToken());
        return substr($hash, 0, $this->config->getConfig('NONCE_HASH_CHARACTER_LIMIT'));
    }

    /**
      * Verifies nonces authenticity and validity
      *
      * @param string $nonce nonce to be verified
      * @param string $action action name (like a password) for said nonce
      * @return bool verification outcome
      */

    public function verify(string $nonce, string $action) : bool
    {
        $hash = $this->create( $action, 0, true );

        if ( $hash !== $nonce )
            return false;

        return (bool) $this->store->getKey($this->trimNonceIdHash($hash));
    }

    /**
      * Delete a hash from temporary storage
      *
      * @param string $hash hash to be deleted
      * @return mixed implemented store return type
      */

    public function deleteHashFromStore(string $hash)
    {
        return $this->store->deleteKey($this->trimNonceIdHash($hash));
    }

    /**
      * Delete a hash from temporary storage using an action string
      *
      * @param string $action action to retrieve hash for
      * @return mixed implemented store return type
      */

    public function delete(string $action)
    {
        return $this->deleteHashFromStore( $this->encryptUserString($action) );
    }

    /**
      * Delimit the product hash to configured characters length
      *
      * @param string $hash hash to be trimmed
      * @return string trimmed hash
      */

    public function trimNonceIdHash(string $hash) : string
    {
        return $this->config->getConfig('HASH_ID_CHARACTRER_LIMIT') <= 0
            ? $hash
            : substr($hash, 0, $this->config->getConfig('HASH_ID_CHARACTRER_LIMIT'));
    }

    /**
      * Generate a random character of X characters length
      *
      * @param int $length characters length
      * @return string generated random character
      */

    public function getRandomCharacter(int $length=16) : string
    {
        $factory = new \RandomLib\Factory;

        $generator = $factory->getGenerator(new \SecurityLib\Strength(
            \SecurityLib\Strength::MEDIUM
        ));
        
        return $generator->generateString($length);
    }
}