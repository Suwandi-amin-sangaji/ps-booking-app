<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AdminMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        if (!Auth::check()) {
            return redirect()->route('login'); // Belum login? Redirect ke login
        }

        if (Auth::user()->role !== 'admin') {
            return abort(403, 'Unauthorized'); // Bukan admin? Kasih error 403, JANGAN redirect balik!
        }

        return $next($request);
    }
}
