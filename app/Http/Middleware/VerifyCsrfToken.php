<?php

namespace App\Http\Middleware;

use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken as Middleware;

class VerifyCsrfToken extends Middleware
{
    /**
     * The URIs that should be excluded from CSRF verification.
     *
     * @var array<int, string>
     */
    protected $except = [
        # For API-only projects, you might list all API routes here, e.g., 'api/*'
        # Or remove this middleware entirely from the 'web' group in Kernel.php if no web routes need it.
    ];
}
