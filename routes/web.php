<?php

use App\Http\Controllers\AreaController;
use App\Http\Controllers\CheckpointController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\IncidentController;
use App\Http\Controllers\MemberDashboardController;
use App\Http\Controllers\PatrolLogController;
use App\Http\Controllers\PatrolScheduleController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ShiftController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

// Root: redirect ke halaman login
Route::get('/', function () {
    return redirect()->route('login');
});

// ══ Command Center — hanya user login + bukan Security Guard ══════════════════
Route::middleware(['auth', 'restrict.guard'])->group(function () {

    // Dashboard utama
    Route::get('/dashboard', [DashboardController::class, 'index'])
        ->name('dashboard');

    // Incidents
    Route::get('/incidents', [IncidentController::class, 'index'])
        ->name('incidents.index');

    // Guards (placeholder view)
    Route::get('/guards', function () {
        return view('guards.index');
    })->name('guards.index');

    Route::get('/settings', function () {
        return view('settings.index');
    })->name('settings.index');

    // ── Resource routes Command Center ──────────────────────────────────────
    Route::resource('patrol-schedules', PatrolScheduleController::class);
    Route::resource('areas', AreaController::class);
    Route::resource('areas.checkpoints', CheckpointController::class)
        ->scoped()
        ->except(['index', 'show']);

    // Shift: CRUD + toggle status
    Route::patch('shifts/{shift}/toggle-status', [ShiftController::class, 'toggleStatus'])
        ->name('shifts.toggle-status');
    Route::resource('shifts', ShiftController::class);

    // Petugas (Users)
    Route::patch('petugas/{petuga}/toggle-status', [UserController::class, 'toggleStatus'])
        ->name('petugas.toggle-status');
    Route::resource('petugas', UserController::class)
        ->parameters(['petugas' => 'petuga']);
});

// ══ Member Area — semua role yang sudah login boleh masuk ════════════════════
// restrict.guard di sini TIDAK menghalangi Security Guard karena URL-nya
// diawali /member, sehingga kondisi di middleware tidak terpenuhi.
Route::middleware(['auth', 'restrict.guard'])->group(function () {
    // ── Halaman Beranda member (dashboard data-driven) ──────────────────────
    Route::get('/member', [MemberDashboardController::class, 'index'])
        ->name('member.dashboard');

    // ── Halaman Patroli (scan checkpoint) ───────────────────────────────────
    Route::get('/member/patrol', [PatrolLogController::class, 'patrol'])
        ->name('member.patrol');

    // Endpoint scan checkpoint — dipanggil via AJAX/fetch dari frontend
    Route::post('/member/patrol/scan', [PatrolLogController::class, 'store'])
        ->name('member.patrol.scan');

    // ── Riwayat scan ────────────────────────────────────────────────────────
    Route::get('/member/riwayat', [PatrolLogController::class, 'index'])
        ->name('member.riwayat');

    // ── Profil ──────────────────────────────────────────────────────────────
    Route::get('/member/profile', function () {
        return view('member.profile', ['user' => auth()->user()->loadMissing('role')]);
    })->name('member.profile');
});

// ══ Profile (Breeze) — semua role yang sudah login ═══════════════════════════
Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
