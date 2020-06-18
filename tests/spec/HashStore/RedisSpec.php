<?php

namespace spec\Nonce\HashStore;

use Nonce\Nonce;
use Nonce\HashStore\Redis;
use Nonce\Config\Config;
use PhpSpec\ObjectBehavior;

use \Predis\Client;

class RedisSpec extends ObjectBehavior
{
    private function defaultConstruct()
    {
        $this->beConstructedWith(new Client('tcp://cache:6379'));
    }

    function it_is_initializable()
    {
        $this->defaultConstruct();

        $this->shouldHaveType(Redis::class);
    }

    function it_tests_redis_functionality()
    {
        $this->defaultConstruct();

        $this->setKey('test', '1', 3)->shouldBe(true);
        $this->getKey('test')->shouldBe('1');

        sleep(4);
        $this->getKey('test')->shouldBe('');
    }

    function it_tests_nonce_implementation()
    {
        $this->defaultConstruct();

        $nonce = new Nonce( new Config, $redis=new Redis(new Client('tcp://cache:6379')) );

        $nonce_key = $nonce->trimNonceIdHash($nonce->create('authentication-form'));

        $this->getKey($nonce_key)->shouldBe('1');
        
        $this->getKey(str_shuffle($nonce_key))->shouldBe('');
    }
}
