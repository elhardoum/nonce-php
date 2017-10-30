# Nonce PHP

Fast PHP nonce and CSRF tokens tool, add tokens to your web forms and validate nonces easily using browser cookies or a cache driver (or anything else).

## Install

```bash
composer require elhardoum/nonce-php
```

## Basic Usage

First, you'll want to configure the Nonce class, and you can use `Config` for this:

```php
use Nonce\{Nonce,Config};

Config::$SALT = 'wL`i%aQh4e|0Pg`7Nr`v|8cx(wzH>4+B<7GHNO]|1wXQ8XETfx+/ZnSklrr&YK~W';
```