<?php

namespace App\Http\Middleware;

use Illuminate\Cookie\Middleware\EncryptCookies as Middleware;

class EncryptCookies extends Middleware
{
    /**
     * Nomes de cookies que não devem ser criptografados.
     */
    protected $except = [
        //
    ];
}
