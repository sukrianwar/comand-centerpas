<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class ImpersonationController extends Controller
{
    /**
     * Login sebagai akun petugas (khusus Super Admin).
     */
    public function store(Request $request, User $petuga): RedirectResponse
    {
        $impersonator = $request->user();

        abort_unless($impersonator->isSuperAdmin(), 403, 'Hanya Super Admin yang dapat login sebagai petugas.');
        abort_if($impersonator->is($petuga), 403, 'Anda tidak dapat login sebagai akun sendiri.');
        abort_if($petuga->isSuperAdmin(), 403, 'Akun Super Admin tidak dapat di-impersonate.');
        abort_unless($petuga->status === 'active', 403, 'Akun petugas tidak aktif.');
        abort_if($request->session()->has('impersonator_id'), 403, 'Anda sudah berada dalam mode impersonasi.');

        $impersonatorId = $impersonator->id;

        Auth::guard('web')->login($petuga);
        $request->session()->regenerate();
        $request->session()->put('impersonator_id', $impersonatorId);

        Log::info('Impersonation started', [
            'impersonator_id' => $impersonatorId,
            'impersonator_username' => $impersonator->username,
            'target_id' => $petuga->id,
            'target_username' => $petuga->username,
            'ip' => $request->ip(),
        ]);

        $targetRoute = $petuga->hasRole('Security Guard')
            ? 'member.dashboard'
            : 'dashboard';

        return redirect()
            ->route($targetRoute)
            ->with('success', "Anda sekarang login sebagai {$petuga->name}.");
    }

    /**
     * Kembali ke akun Super Admin asal.
     */
    public function destroy(Request $request): RedirectResponse
    {
        $impersonatorId = $request->session()->pull('impersonator_id');

        if (! $impersonatorId) {
            return redirect()
                ->route('dashboard')
                ->with('error', 'Anda tidak sedang dalam mode impersonasi.');
        }

        $impersonator = User::find($impersonatorId);

        if (! $impersonator || ! $impersonator->isSuperAdmin() || $impersonator->status !== 'active') {
            Log::warning('Impersonation ended because the impersonator is no longer valid', [
                'impersonator_id' => $impersonatorId,
                'target_id' => Auth::id(),
                'ip' => $request->ip(),
            ]);

            Auth::guard('web')->logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();

            return redirect()
                ->route('login')
                ->with('error', 'Sesi Super Admin asal tidak valid. Silakan login kembali.');
        }

        Log::info('Impersonation ended', [
            'impersonator_id' => $impersonator->id,
            'target_id' => Auth::id(),
            'target_username' => Auth::user()?->username,
            'ip' => $request->ip(),
        ]);

        Auth::guard('web')->login($impersonator);
        $request->session()->regenerate();

        return redirect()
            ->route('petugas.index')
            ->with('success', "Anda kembali login sebagai {$impersonator->name}.");
    }
}
