<?php

namespace Nonce;

class Config
{
    static $CSRF_EXPIRE = 7200; // 2 hrs
    static $SALT = '<r*>iVIF`Mcjj&+fkJ4D2,-geI]:-{^|~97.2p:/~+Q?&J_fe2A0i~H?89SeJ:Ztt>';
    static $CHAR_LIMIT = 22;
    static $TOKEN_HASHER = 'sha512';
    static $NONCE_EXPIRE = 600; // 10 min
    static $COOKIE_PATH = '/';
    static $COOKIE_DOMAIN = '127.0.0.1';
    static $HASH_NAME_LENGTH = 11;
    static $STORE_CTX_SET = ['Nonce\Cookie', 'set'];
    static $STORE_CTX_GET = ['Nonce\Cookie', 'get'];
    static $STORE_CTX_DELETE = ['Nonce\Cookie', 'delete'];

    static function storeContextSet()
    {
        return self::$STORE_CTX_SET;
    }

    static function storeContextGet()
    {
        return self::$STORE_CTX_GET;
    }

    static function storeContextDelete()
    {
        return self::$STORE_CTX_DELETE;
    }
}