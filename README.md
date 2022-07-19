# Nonce PHP

Fast PHP nonce and CSRF tokens tool, add tokens to your web forms and validate nonces easily using browser cookies or a cache driver (or anything else).

[![Build Status](https://travis-ci.org/elhardoum/nonce-php.svg?branch=master)](https://travis-ci.org/elhardoum/nonce-php)

## Install

Using [composer](https://getcomposer.org):

```bash
composer require elhardoum/nonce-php
```

## Basic Usage

First, import and initialize the nonce utility class:

```php
// nonce configuration class
$nonceConfig = new \Nonce\Config\Config;

// nonce hash storage, use browser cookies
$nonceStore = new \Nonce\HashStore\Cookie;

// initialize nonce class
$nonceUtil = new \Nonce\Nonce( $nonceConfig, $nonceStore );
```

Then, to create a nonce based on an action name:

```php
// make sure you make this call before starting the output or sending HTTP headers
$nonce = $nonceUtil->create( 'signup-form' );
```

Here you see we used the `signup-form` as an action name and we can use that later to verify the nonce supplied to the user request:

Let's use this in our HTML form:

```html
<form method="post">
    ....
    ....

    <input type="hidden" name="nonce" value="<?php echo htmlentities($nonce); ?>" />
</form>

```

Now the form should appear something like this on the front-end (i.e with the nonce field added):

```html
<form method="post">
    ....
    ....

    <input type="hidden" name="nonce" value="7ad510a2296535d545615d" />
</form>
```

To verify the nonce for this form on submission, we can pass the `nonce` hash to the method `$nonceUtil->verify( string $hash, string $action )`:

```php
if ( isset( $_POST['nonce'] ) && $nonceUtil->verify( $_POST['nonce'], 'signup-form' ) ) {
    # nonce is valid
}
```

## Configuration

When initializing the `Nonce\Nonce` class, you're passing the config class as a first argument:

```php
// nonce configuration class
$nonceConfig = new \Nonce\Config\Config;

// initialize nonce class
$nonceUtil = new \Nonce\Nonce( $nonceConfig, new \Nonce\HashStore\Cookie );
```

You can customize the default configs by calling the `$nonceConfig->setConfig` method or by passing your own config class which implements `Nonce\Config\Base` interface.

```php
$nonceConfig->setConfig( string $config_name, $config_value );
```

This allows you to overwrite the default constants of the config class.

For example, to update the cookie settings:

```php
$nonceConfig->setConfig( 'COOKIE_PATH', '/' );
$nonceConfig->setConfig( 'COOKIE_DOMAIN', 'example.com' );
```

### Available config constants:

Remember to use `$nonceConfig->setConfig` to update any of the following config keys:

```php
// CSRF token cookie name
$nonceConfig::CSRF_COOKIE_NAME = 'CSRF';
```

The CSRF cookie name.

```php
// CSRF cookie expiration in seconds
$nonceConfig::CSRF_COOKIE_TTL = 7200; // 2 hrs
```

The number of seconds in which the CSRF token attached to the browser cookie should expire. This token is important and used to generate and verify the hashes, so it is unique per user.

```php
$nonceConfig::RANDOM_SALT = 'HI5CTp$94deNBCUqIQx63Z8P$T&^_z`dy';
```

Specify a random salt to be used to generate the tokens.

```php
$nonceConfig::NONCE_HASH_CHARACTER_LIMIT = 22;
```

Enter a character limit here. The return value of `$nonceUtil->create(...)` then will be this characters long.

```php
$nonceConfig::TOKEN_HASHER_ALGO = 'sha512';
```

Which algo should be passed to [`hash`](http://php.net/manual/en/function.hash.php) to generate a token.

```php
$nonceConfig::NONCE_DEFAULT_TTL = 600; // 10 min
```

How long should the nonce live once generated? the nonces should have a limited lifespan, otherwise you'd be bloating your browser cookies or cache server with redundant hashes.

The expiration is renewed after you request a hash via `$nonceUtil->create(...)` method, so if a hash is 5 min to expire, the expiration will be reset as we recreate the hash.

```php
$nonceConfig::COOKIE_PATH = '/';
```

Cookies path, set to a web directory name if you use `Nonce` in a subdirectory project, or `/` if on the root domain.

Note: even if you use a cache driver to store the hashes, the cookie is still required to store the `CSRF` token.

```php
$nonceConfig::COOKIE_DOMAIN = '127.0.0.1';
```

The current domain name (host).

```php
$nonceConfig::HASH_ID_CHARACTRER_LIMIT = 11;
```

Enter a character limit here. This is important when you are storing hashes via cookies.

The generated hash becomes long that we actually need to trim it to get only the first few characters for the sake of identification, when you are storing hashes using browser cookies then this would possible result in larger request headers, so we'll try to store tiny hashes instead, and clip the hash as well while verifying the nonces.

## Hash store drivers

The nonces identifier data needs to be stored temporarily to be used for later verification.

### Cookies

A simple temporary storage can be achieved with browser cookies, so you can pass the `\Nonce\HashStore\Cookie` instance as the second argument while initializing the nonce class:

```php
$nonceUtil = new \Nonce\Nonce( $nonceConfig, new \Nonce\HashStore\Cookie );
```

Notice that regardless if you use a different store driver, cookies will still be used to persist the CSRF token for the request users.

### Redis

You can also store the hash data temporarily on your Redis server, by passing an instance of `\Nonce\HashStore\Redis` as the second argument while initializing the nonce class:

```php
// initialize the class passing an instance of Predis as the first argument
$nonceStore = new \Nonce\HashStore\Redis( new \Predis\Client() );

$nonceUtil = new \Nonce\Nonce( $nonceConfig, $nonceStore );
```

Make sure to pass an instance of [`\Predis\Client`](https://github.com/nrk/predis) while instantiating the `\Nonce\HashStore\Redis` class.

### Your Own

You can use any other means of temporary data stores, by passing a class which implements the `\Nonce\HashStore\Store` interface:

```php
<?php

class CustomStore implements \Nonce\HashStore\Store
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
        // ...
    }

    /**
      * Get a key from temporary storage
      *
      * @param string $name key to be retrieved
      * @return string value for stored key or empty string on key unavailable
      */

    public function getKey( string $name ) : string
    {
        // ...
    }

    /**
      * Unset a key from temporary storage
      *
      * @param string $name key to be removed
      * @return bool success/failure
      */

    public function deleteKey( string $name ) : bool
    {
        // ...
    }
}
```
