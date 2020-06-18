<?php

namespace Nonce\HashStore;

use Nonce\Util\Cookie as CookieUtil;

class Cookie implements Store
{
    public function setKey( string $name, string $value, int $expire_seconds=0 ) : bool
    {
        return CookieUtil::set( $name, $value, $expire_seconds ) || true;
    }

    public function getKey( string $name ) : string
    {
        return (string) CookieUtil::get( $name );
    }

    public function deleteKey( string $name ) : bool
    {
        return CookieUtil::delete( $name ) || true;
    }
}