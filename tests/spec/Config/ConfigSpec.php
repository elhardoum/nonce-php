<?php

namespace spec\Nonce\Config;

use Nonce\Config\Config;
use PhpSpec\ObjectBehavior;

class ConfigSpec extends ObjectBehavior
{
    function it_is_initializable()
    {
        $this->shouldHaveType(Config::class);
    }

    function it_is_configurable()
    {
        $this->setConfig('CSRF_COOKIE_NAME', 'csrf-token');
        $this->getConfig('CSRF_COOKIE_NAME')->shouldBe('csrf-token');
    }
}
