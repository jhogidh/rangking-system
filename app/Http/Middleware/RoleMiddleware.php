<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class RoleMiddleware
{
    /**
     * Handle an incoming request.
     * Parameter $role akan kita kirim dari route (misal: 'admin' atau 'guru')
     */
    public function handle(Request $request, Closure $next, string $role): Response
    {
        if (!Auth::check()) {
            return redirect()->route('login');
        }

        // Cek apakah role user sesuai dengan yang diminta
        if (Auth::user()->role !== $role) {
            // Jika user mencoba akses halaman admin tapi dia guru -> 403 Forbidden
            abort(403, 'Anda tidak memiliki akses ke halaman ini.');
        }

        return $next($request);
    }
}
