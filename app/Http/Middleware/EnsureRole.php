<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Menggantikan pengecekan `if ($_SESSION['role'] == 'Admin')` yang tersebar
 * di puluhan file pada aplikasi lama. Dipakai di route lewat:
 *   Route::middleware('role:admin')->group(...)
 * Alias 'role' didaftarkan di bootstrap/app.php (lihat README).
 */
class EnsureRole
{
    public function handle(Request $request, Closure $next, string ...$roles): Response
    {
        $userRoleSlug = $request->user()?->role?->slug;

        abort_unless(
            in_array($userRoleSlug, $roles, true),
            403,
            'Anda tidak punya akses di halaman ini.'
        );

        return $next($request);
    }
}
