<?php

namespace App\Http\Middleware;

use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken as Middleware;
use Illuminate\Support\Facades\Log;

class VerifyCsrfToken extends Middleware
{
    /**
     * The URIs that should be excluded from CSRF verification.
     *
     * @var array<int, string>
     */
    protected $except = [
        //
        //'api/*',
        'admin/wallets/processCredit',

    ];

    protected function tokensMatch($request)
    {
        Log::error('CSRF CHECK', [
            'session_token' => $request->session()->token(),
            'request_token' => $request->input('_token'),
            'headers' => $request->headers->all(),
            'cookies' => $request->cookies->all(),
        ]);

        return parent::tokensMatch($request);
    }
}
