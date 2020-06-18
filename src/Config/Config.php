<?php

namespace Nonce\Config;

class Config implements Base
{
    /**
      * Default configs
      */

    // random salt for token generation
    const RANDOM_SALT = 'HI5CTp$94deNBCUqIQx63Z8P$T&^_z`dy';

    // CSRF cookie name
    const CSRF_COOKIE_NAME = 'CSRF';

    // CSRF cookie expiration in seconds
    const CSRF_COOKIE_TTL = 7200;

    // encryption algorithm to be used to generate tokens
    const TOKEN_HASHER_ALGO = 'sha512';

    // nonce hash characters limit
    const NONCE_HASH_CHARACTER_LIMIT = 22;

    // lifespan of the hashes in seconds
    const NONCE_DEFAULT_TTL = 600;

    // nonce id character limit
    const HASH_ID_CHARACTRER_LIMIT = 11;

    // cookies path
    const COOKIE_PATH = '/';

    // cookies domain
    const COOKIE_DOMAIN = '127.0.0.1';

    // custom configs key/value pairs
    protected $user_custom = [];

    /**
      * Overwrite a default config (class constant) directly
      *
      * @param string $name class constant (config id)
      * @param mixed $value value for the config
      * @return self class
      */

    public function setConfig( string $name, $value ) : self
    {
        $this->user_custom[ $name ] = $value;
        return $this;
    }

    /**
      * Get a config by key, check for custom configs first and
      * fallback to default configs
      *
      * @param string $name class constant (config id)
      * @return mixed $value value for said config
      */

    public function getConfig( string $name )
    {
        if ( array_key_exists($name, $this->user_custom) ) {
            return $this->user_custom[ $name ];
        } else if ( defined( $constant = __CLASS__ . '::' . $name ) ) {
            return constant( $constant );
        }
    }
}