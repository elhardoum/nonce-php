<?php

namespace Nonce\HashStore;

use Predis\Client as Predis;

class Redis implements Store
{
    // redis connection client
    protected $client;

    public function __construct( \Predis\Client $client )
    {
        $this->client = $client;
    }

    public function setKey( string $name, string $value, int $expire_seconds=0 ) : bool
    {
        $args = [ $name, $value ];
        $expire_seconds > 0 && ($args = array_merge($args, ['EX', $expire_seconds]));
        return !! $this->client->set( ...$args );
    }

    public function getKey( string $name ) : string
    {
        return $this->client->get( $name );
    }

    public function deleteKey( string $name ) : bool
    {
        return !! $this->client->del( $name );
    }
}