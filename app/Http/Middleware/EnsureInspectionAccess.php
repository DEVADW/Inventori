<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureInspectionAccess
{
    public function handle(Request $request, Closure $next, string $module): Response
    {
        $user = $request->user();

        if (!$user) {
            abort(403, 'Anda tidak memiliki akses ke halaman ini.');
        }

        if ($user->role === 'admin' || in_array($module, $user->allowedInspectionModules(), true)) {
            return $next($request);
        }

        abort(403, 'Anda tidak memiliki akses ke halaman inspeksi ini.');
    }
}
