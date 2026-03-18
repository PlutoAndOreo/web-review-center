<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class CsrfTokenController extends Controller
{
    /**
     * Get a fresh CSRF token for the authenticated user.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function token(Request $request)
    {
        return response()->json([
            'token' => csrf_token(),
        ]);
    }
}

