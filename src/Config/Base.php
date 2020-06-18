<?php

namespace Nonce\Config;

interface Base
{
    /**
      * Get a config by key
      *
      * @param string $name class constant (config id)
      * @return mixed $value value for said config
      */

    public function getConfig( string $name );
}