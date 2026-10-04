<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckRole
{
    /**
     * Handle an incoming request.
     *
     * @param  Closure(Request): (Response)  $next
     */
     public function handle(Request $request, Closure $next, string ...$jabatan): Response
    {
        $user = $request->user();

        $userJabatan = strtolower(trim((string) ($user?->jabatan ?? '')));
        $allowedJabatan = array_map(static fn (string $value): string => strtolower(trim($value)), $jabatan);

        if (!$user || !in_array($userJabatan, $allowedJabatan, true)) {
            abort(403, 'Anda tidak memiliki akses ke halaman ini.');
        }

        return $next($request);
    }
}
