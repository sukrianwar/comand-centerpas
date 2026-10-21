<?php

namespace App\Http\Controllers;

use App\Models\PatrolLog;
use App\Models\PatrolSchedule;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\View\View;

class MemberDashboardController extends Controller
{
    /**
     * Halaman beranda member (Security Guard) — data diambil dari backend:
     * jadwal aktif, statistik scan hari ini, dan jadwal mendatang.
     */
    public function index(Request $request): View
    {
        $user = auth()->user();
        $now = Carbon::now();
        $today = $now->toDateString();

        // ── Sapaan dinamis berdasarkan jam ───────────────────────────────────
        $hour = (int) $now->format('H');
        $greeting = match (true) {
            $hour >= 5 && $hour < 11 => 'Selamat Pagi 👋',
            $hour >= 11 && $hour < 15 => 'Selamat Siang 👋',
            $hour >= 15 && $hour < 18 => 'Selamat Sore 👋',
            default => 'Selamat Malam 👋',
        };

        // ── Jadwal aktif sekarang (global, berlaku harian) ───────────────────
        $activeSchedules = PatrolSchedule::with(['checkpoints' => function ($q) {
            $q->orderByPivot('sequence_number', 'asc');
        }, 'area', 'shift'])
            ->activeNow()
            ->orderBy('start_time')
            ->get();

        // Progress scan per jadwal, dihitung per petugas yang login HARI INI
        // (jadwal berlaku harian — scan kemarin tidak dihitung ulang). Satu
        // query untuk semua jadwal aktif agar tidak terjadi N+1.
        $scannedPerSchedule = PatrolLog::where('user_id', $user->id)
            ->whereDate('scanned_at', $today)
            ->whereIn('patrol_schedule_id', $activeSchedules->pluck('id'))
            ->get(['patrol_schedule_id', 'checkpoint_id'])
            ->groupBy('patrol_schedule_id');

        $activeSchedules->each(function ($schedule) use ($scannedPerSchedule) {
            $schedule->scannedCheckpointIds = $scannedPerSchedule
                ->get($schedule->id, collect())
                ->pluck('checkpoint_id')
                ->toArray();
        });

        // ── Statistik hari ini (berbasis jadwal aktif) ───────────────────────
        $totalCheckpointsActive = $activeSchedules->sum(fn ($s) => $s->checkpoints->count());
        $cpSelesai = PatrolLog::where('user_id', $user->id)
            ->whereDate('scanned_at', $today)
            ->count();

        $stats = [
            'jadwal_total' => $activeSchedules->count(),
            'cp_selesai' => $cpSelesai,
            'cp_terlewat' => max($totalCheckpointsActive - $cpSelesai, 0),
        ];

        // ── Jadwal mendatang (belum masuk jam, urut waktu mulai) ──────────────
        $upcomingSchedules = PatrolSchedule::with('area')
            ->whereIn('status', ['scheduled', 'ongoing'])
            ->where('start_time', '>', $now->format('H:i:s'))
            ->orderBy('start_time')
            ->limit(3)
            ->get();

        // ── Badge shift di header: ambil dari jadwal aktif pertama ───────────
        $currentBadge = null;
        if ($activeSchedules->isNotEmpty()) {
            $first = $activeSchedules->first();
            $currentBadge = sprintf(
                '%s • Shift %s (%s–%s)',
                $user->role?->role_name ?? 'Petugas',
                $first->shift?->shift_name ?? '-',
                Carbon::parse($first->start_time)->format('H:i'),
                Carbon::parse($first->end_time)->format('H:i')
            );
        }

        return view('member.dashboard', compact(
            'user',
            'greeting',
            'activeSchedules',
            'stats',
            'upcomingSchedules',
            'currentBadge'
        ));
    }
}
