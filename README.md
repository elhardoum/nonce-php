# Nonce PHP

Fast PHP nonce and CSRF tokens tool, add tokens to your web forms and validate nonces easily using browser cookies or a cache driver (or anything else).

## Install

```bash
composer require elhardoum/nonce-php
```

## Basic Usage

First, you'll want to configure the Nonce class, and you can use `Config` for this:

1. Import the class

```php
use Nonce\{Nonce,Config};
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
Config::$COOKIE_DOMAIN = 'nonce.dev';
```

3. Let's add the token field to our form:
```html
<form method="post">
    ....
    ....

    <input type="hidden" name="nonce" value="<?php echo Nonce::create('signup-form'); ?>" />
</form>

```

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