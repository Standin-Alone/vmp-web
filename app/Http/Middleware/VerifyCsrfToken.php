<?php

namespace App\Http\Middleware;

use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken as Middleware;

class VerifyCsrfToken extends Middleware
{
    /**
     * The URIs that should be excluded from CSRF verification.
     *
     * @var array
     */
    protected $except = [
        //

        '/api/sign_in',
        '/api/get_voucher_info',
        '/api/resend-otp',
        '/api/submit-voucher',
        '/api/get-scanned-vouchers',
        'api'
    ];
}
