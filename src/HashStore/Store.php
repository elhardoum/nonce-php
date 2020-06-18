<?php

namespace Nonce\HashStore;

interface Store
{
    public function setKey( string $name, string $value, int $expire_seconds=0 ) : bool;
    public function getKey( string $name ) : string;
    public function deleteKey( string $name ) : bool;
}