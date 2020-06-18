<?php

namespace Nonce\HashStore;

use Nonce\Util\Cookie as CookieUtil;

class Cookie implements Store
{
    /**
      * Store a key temporarily
      *
      * @param string $name key to be stored
      * @param string $value value to be stored for the given key
      * @param int $expire_seconds expire the data after X seconds (data TTL)
      * @return bool success/failure
      */

    public function setKey( string $name, string $value, int $expire_seconds=0 ) : bool
    {
        return CookieUtil::set( $name, $value, $expire_seconds ) || true;
    }

    /**
      * Get a key from temporary storage
      *
      * @param string $name key to be retrieved
      * @return string value for stored key or empty string on key unavailable
      */

    public function getKey( string $name ) : string
    {
        return (string) CookieUtil::get( $name );
    }

    /**
      * Unset a key from temporary storage
      *
      * @param string $name key to be removed
      * @return bool success/failure
      */

    public function deleteKey( string $name ) : bool
    {
        return CookieUtil::delete( $name ) || true;
    }
}