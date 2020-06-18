
## Todo

 - [ ] update readme
 - [ ] code comments
 - [ ] unit tests: cookie, core, redis
 
# Nonce PHP

Fast PHP nonce and CSRF tokens tool, add tokens to your web forms and validate nonces easily using browser cookies or a cache driver (or anything else).

[![Build Status](https://travis-ci.org/elhardoum/nonce-php.svg?branch=master)](https://travis-ci.org/elhardoum/nonce-php)

## Install

Using [composer](https://getcomposer.org):

```bash
composer require elhardoum/nonce-php
```

Without composer, download or clone the repo and load the files in the `src/` directory:

```php
require __DIR__ . '/nonce-php/src/Nonce.php';
require __DIR__ . '/nonce-php/src/Cookie.php';
require __DIR__ . '/nonce-php/src/Config.php';
require __DIR__ . '/nonce-php/src/HashStore.php';
```

## Basic Usage

First, you'll want to configure the Nonce class, and you can use `Config` for this:

1. Import the class

```php
use Nonce\{Nonce,Config};

require __DIR__ . '/vendor/autoload.php';
```

2. Let's make a basic config to use cookies:

```php
// Set here a random salt which can be used along with a random string to generate the CSRF token.
Config::$SALT = 'wL`i%aQh4e|0Pg`7Nr`v|8cx(wzH>4+B<7GHNO]|1wXQ8XETfx+/ZnSklrr&YK~W';

// Set the cookie path ( to which the cookies are saved ).
// If you run this on the root domain, then set `/`
// Otherwise if used on a subdirectory, then enter the directory name (e.g /my-custom-site/ for http://example.com/my-custom-site/)
Config::$COOKIE_PATH = '/test/';

// Cookies host: enter your host (domain name)
Config::$COOKIE_DOMAIN = 'example.com';
```

3. Let's add the token field to our form:
```php
// somewhere before HTML output starts
$nonce = Nonce::create('signup-form');
```

```html
<form method="post">
    ....
    ....

    <input type="hidden" name="nonce" value="<?php echo $nonce; ?>" />
</form>

```

To set a custom expiration for the token, pass the number of seconds to the second argument: `Nonce::create('signup-form', 60)`, otherwise `Config::$NONCE_EXPIRE` will be in use.

Now the form should appear something like this on the front-end:

```html
<form method="post">
    ....
    ....

    <input type="hidden" name="nonce" value="7ad510a2296535d545615d" />
</form>
```

To verify the nonce for this form on submission, we can pass the `nonce` hash to the method `Nonce::verify( String $hash, String $action )`:

```php
if ( isset( $_POST['nonce'] ) && Nonce::verify( $_POST['nonce'], 'signup-form' ) ) {
    # nonce is valid
}
```

## Configuration

```php
Config::$CSRF_EXPIRE = 7200; // 2 hrs
```

The number of seconds in which the CSRF token attached to the browser cookie should expire. This token is important and used to generate and verify the hashes, so it is unique per user.

```php
static $SALT = '<r*>iVIF`Mcjj&+fkJ4D2,-geI]:-{^|~97.2p:/~+Q?&J_fe2A0i~H?89SeJ:Ztt>';
```

Specify a random salt to be used to generate the tokens.

```php
static $CHAR_LIMIT = 22;
```

Enter a character limit here. The return value of `Nonce::create()` then will be X characters long.

```php
static $TOKEN_HASHER = 'sha512';
```

Which algo should be passed to [`hash`](http://php.net/manual/en/function.hash.php) to generate a token.

```php
static $NONCE_EXPIRE = 600; // 10 min
```

How long should the nonce live once generated? the nonces should have a precise lifespan to expire, otherwise you'd be bloating your browser cookies or cache server with hashes.

The expiration is renewed after you request a hash via `Nonce::create()` method, so if a hash is 5 min to expire, the expiration will be reset as we recreate the hash.

```php
static $COOKIE_PATH = '/';
```

Cookies path, set to a web directory name if you use `Nonce` in a subdirectory project, or `/` if on the root domain.

Note: even if you use a cache driver to store the hashes, the cookie is still required to store the `CSRF` token.

```php
static $COOKIE_DOMAIN = '127.0.0.1';
```

The current domain name (host).

```php
static $HASH_NAME_LENGTH = 11;
```

Enter a character limit here. This is important when you are storing hashes via cookies.

The generated hash becomes very long that we actually need to trim it to get only the first XX characters, when you are storing hashes via browser cookies then this will for sure cause a problem (large headers), so we'll try to store tiny hashes instead, and clip the hash as well while verifying the nonces.

```php
static $STORE_CTX_GET = ['Nonce\Cookie', 'get'];
```

A callable function or method that should retrieve us the hashes.

1. `String $name` - the x (= `Config::$HASH_NAME_LENGTH`) characters long token identifier.

```php
static $STORE_CTX_SET = ['Nonce\Cookie', 'set'];
```

A callable function or method that should store us the hashes. The passed arguments are:

1. `String $name` - the x (= `Config::$HASH_NAME_LENGTH`) characters long token identifier.
2. `Integer $value` - 1. (simple huh)
3. `Integer $expires` - the number of seconds that this hash should live.


```php
static $STORE_CTX_DELETE = ['Nonce\Cookie', 'delete'];
```

A callable function or method that should delete a given hash from the hash store. The passed arguments are:

1. `String $name` - the x (= `Config::$HASH_NAME_LENGTH`) characters long token identifier.

## Redis Case

You can use Redis to store the hashes as it is super fast and efficient, here's a sample case using [`Predis\Client`](https://github.com/nrk/predis) as a Redis PHP client:

```php
use Nonce\Config;
use Predis\Client as PredisClient;

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
```