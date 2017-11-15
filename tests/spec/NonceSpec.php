<?php

namespace spec\Nonce;

use Nonce\Nonce;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Nonce\Config;
use Predis\Client as PredisClient;

class NonceSpec extends ObjectBehavior
{
    private static $redis;

    function it_is_initializable()
    {
        $this->shouldHaveType(Nonce::class);
    }

    function it_is_dump()
    {
        $this->verify(
            Nonce::create('signup-form'),
            'signup-form'
        )->shouldBe(true);
    }

    private static function setupRedis()
    {
        if ( isset(self::$redis) )
            return;

        $redis_client = new PredisClient();

        Config::$STORE_CTX_SET = function($key, $value, $expire) use ($redis_client) {
            $redis_client->set( $key, $value );
            $redis_client->expire( $key, $expire );
        };

        Config::$STORE_CTX_GET = function($key) use ($redis_client) {
            return $redis_client->get( $key );
        };

        Config::$STORE_CTX_DELETE = function($key) use ($redis_client) {
            return $redis_client->del( $key );
        };

        self::$redis = 1;
    }

    function it_can_redis_nonce_hash_store()
    {
        self::setupRedis();

        $nonce = Nonce::create('login-form', 1);

        $this->verify($nonce, 'login-form')->shouldBe(true);
    }

    function it_can_test_nonce_expiration()
    {
        self::setupRedis();

        // should be avail for 1 sec
        $nonce = Nonce::create('some-action', 1);

        // expire it
        sleep(1);

        // verify it
        $this->verify($nonce, 'some-action')->shouldBe(false);
    }

    function it_can_delete_hash()
    {
        self::setupRedis();

        $nonce = Nonce::create('some-action-2', 1);

        Nonce::deleteHash($nonce);

        $this->verify($nonce, 'some-action-2')->shouldBe(false);
    }

    function it_can_delete_action()
    {
        self::setupRedis();

        $nonce = Nonce::create('some-action-3', 1);

        Nonce::delete('some-action-3');

        $this->verify($nonce, 'some-action-3')->shouldBe(false);
    }
}
