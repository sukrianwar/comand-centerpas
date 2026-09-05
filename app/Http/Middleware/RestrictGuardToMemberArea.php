<?php

namespace App\Http\Middleware;

use App\Models\Role;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Symfony\Component\HttpFoundation\Response;

class RestrictGuardToMemberArea
{
    /**
     * Handle an incoming request.
     *
     * Memastikan user dengan role "Security Guard" hanya bisa mengakses
     * route yang diawali /member. Route lain (Command Center) akan
     * di-redirect ke member.dashboard dengan pesan error.
     *
     * Admin & Supervisor: lolos tanpa pembatasan apapun.
     *
     * Catatan: middleware ini wajib dijalankan SETELAH middleware 'auth',
     * sehingga $request->user() dijamin tidak null saat dicek di sini.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        // Hanya proses jika user sudah login dan punya role Security Guard
        $guardRoleId = Cache::remember('security_guard_role_id', 86400, function () {
            return Role::where('role_name', 'Security Guard')->value('id');
        });

        if ($user && $user->role_id === $guardRoleId) {
            // Cek apakah URL yang diakses bukan diawali /member
            if (! $request->is('member*')) {
                return redirect()
                    ->route('member.dashboard')
                    ->with('error', 'Anda tidak memiliki akses ke halaman ini.');
            }
        }

        return $next($request);
    }
}
