<?php

namespace Nonce\Config;

class Config implements Base
{
    // default configs
    const CSRF_COOKIE_NAME = 'CSRF';
    const CSRF_COOKIE_TTL = 7200;
    const TOKEN_HASHER_ALGO = 'sha512';
    const NONCE_HASH_CHARACTER_LIMIT = 22;
    const NONCE_DEFAULT_TTL = 600;
    const HASH_ID_CHARACTRER_LIMIT = 11;
    const COOKIE_PATH = '/';
    const COOKIE_DOMAIN = '';

    protected $user_custom = [];

    public function setConfig( string $name, $value ) : self
    {
        $this->user_custom[ $name ] = $value;
        return $this;
    }

    public function getConfig( string $name )
    {
        if ( array_key_exists($name, $this->user_custom) ) {
            return $this->user_custom[ $name ];
        } else if ( defined( $constant = __CLASS__ . '::' . $name ) ) {
            return constant( $constant );
        }
    }
}