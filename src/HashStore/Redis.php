<?php

namespace Nonce\HashStore;

use Predis\Client as Predis;

class Redis implements Store
{
    // redis connection client
    protected $client;

    /**
      * Instantiate class
      *
      * @param \Predis\Client $client redis connection client instance
      */

    public function __construct( \Predis\Client $client )
    {
        $this->client = $client;
    }

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
        $args = [ $name, $value ];
        $expire_seconds > 0 && ($args = array_merge($args, ['EX', $expire_seconds]));
        return !! $this->client->set( ...$args );
    }

    /**
      * Get a key from temporary storage
      *
      * @param string $name key to be retrieved
      * @return string value for stored key or empty string on key unavailable
      */

    public function getKey( string $name ) : string
    {
        return (string) $this->client->get( $name );
    }

    /**
      * Unset a key from temporary storage
      *
      * @param string $name key to be removed
      * @return bool success/failure
      */

    public function deleteKey( string $name ) : bool
    {
        return !! $this->client->del( $name );
    }
}