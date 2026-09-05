<?php

namespace App\Http\Controllers;
use App\Models\Incident;
use App\Models\PatrolLog;
use App\Models\PatrolSchedule;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DashboardController extends Controller
{
    /**
     * Dashboard Command Center — statistik & aktivitas patroli dari database.
     */
    public function index(Request $request): View
    {
        $today = now()->toDateString();

        // ── Petugas aktif: akun Seceurity Guard dengan status active ─────────
        $activeGuards = User::where('status', 'active')
            ->whereHas('role', fn ($q) => $q->where('role_name', 'Security Guard'))
            ->count();

        // ── Total rute: semua jadwal scheduled/ongoing (berlaku hari ini) ────
        $totalRoutes = PatrolSchedule::whereIn('status', ['scheduled', 'ongoing'])->count();

        // ── Selesai tepat waktu & persentase (scan hari ini, semua petugas) ──
        $logsToday = PatrolLog::whereDate('scanned_at', $today);
        $totalScans = (clone $logsToday)->count();
        $completedOnTime = (clone $logsToday)->where('status', 'on_time')->count();
        $completionPercentage = $totalScans > 0 ? round($completedOnTime / $totalScans * 100) : 0;

        // ── Titik terlewat: sisa checkpoint jadwal aktif yang belum discan ───
        $activeSchedules = PatrolSchedule::withCount('checkpoints')->activeNow()->get();
        $totalCheckpointActive = $activeSchedules->sum('checkpoints_count');
        $scannedCheckpointsToday = PatrolLog::whereDate('scanned_at', $today)
            ->distinct()
            ->count('checkpoint_id');
        $missedCheckpoints = max($totalCheckpointActive - $scannedCheckpointsToday, 0);

        // ── Insiden hari ini ─────────────────────────────────────────────────
        $incidentsToday = Incident::whereDate('reported_at', $today)->count();

        // ── Log patroli terbaru (hari ini) ───────────────────────────────────
        $recentLogs = PatrolLog::with(['user', 'checkpoint'])
            ->whereDate('scanned_at', $today)
            ->orderByDesc('scanned_at')
            ->limit(8)
            ->get();

        // ── Insiden terbaru ──────────────────────────────────────────────────
        $recentIncidents = Incident::with('reportedBy')
            ->orderByDesc('reported_at')
            ->limit(5)
            ->get();

        // ── Label shift dinamis berdasarkan jam ──────────────────────────────
        $hour = (int) now()->format('H');
        $shiftLabel = ($hour >= 17 || $hour < 5) ? 'Shift Malam sedang berjalan' : 'Shift Pagi sedang berjalan';

        return view('dashboard', compact(
            'activeGuards',
            'totalRoutes',
            'completedOnTime',
            'completionPercentage',
            'missedCheckpoints',
            'incidentsToday',
            'recentLogs',
            'recentIncidents',
            'shiftLabel'
        ));
    }
}
