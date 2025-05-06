<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class ApiSessionAuth
{
    public function handle(Request $request, Closure $next)
    {
        if (!session()->has('token') || !session()->has('user')) {
            return redirect('/')->withErrors(['message' => 'Unauthorized']);
        }

        return $next($request);
    }
}
