<?php

namespace spec\Nonce;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Predis\Client as PredisClient;

use Nonce\Nonce;
use Nonce\Config\Config;
use Nonce\HashStore\Cookie;

class NonceSpec extends ObjectBehavior
{
    function it_is_initializable()
    {
        $this->beConstructedWith(
            new \Nonce\Config\Config,
            new \Nonce\HashStore\Cookie
        );

        $this->shouldHaveType(Nonce::class);
    }

    function it_is_configurable()
    {
        $config = new \Nonce\Config\Config;
        $config->setConfig('CSRF_COOKIE_NAME', 'csrf-token');
        $config->getConfig('CSRF_COOKIE_NAME')->shouldBe('csrf-token');
    }
}
