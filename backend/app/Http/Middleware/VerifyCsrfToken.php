<?php

namespace App\Http\Middleware;

use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken as Middleware;

class VerifyCsrfToken extends Middleware
{
    /**
     * URLs que devem ser ignoradas pelo CSRF.
     */
    protected $except = [
        // exemplo: 'api/*'
    ];
}
