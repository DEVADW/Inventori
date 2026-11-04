<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureInventoryAccess
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if (!$user) {
            abort(403, 'Anda tidak memiliki akses ke halaman ini.');
        }

        if ($user->role === 'admin' || $user->akses_inventori) {
            return $next($request);
        }

        abort(403, 'Anda tidak memiliki akses ke halaman inventori ini.');
    }
}
