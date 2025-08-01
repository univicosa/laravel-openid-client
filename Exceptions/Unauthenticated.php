<?php

namespace Modules\Exceptions;

use Exception;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class Unauthenticated extends Exception
{
    /**
     * Report the exception.
     */
    public function report(): void
    {
        //
    }

    /**
     * Render the exception as an HTTP response.
     */
    public function render(Request $request): Response
    {
        //


        if ($request->expectsJson()) {
            return response()->json(['error' => 'Unauthenticated.'], 401);
        }

        if (env('APP_ENV') === 'local') {
            return redirect()->guest(route('login'));
        }

        return redirect()->guest(config('openid.server') . '/login?continue=' . $request->url());
    }
}
