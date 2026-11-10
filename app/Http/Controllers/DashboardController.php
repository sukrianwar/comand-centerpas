<?php

namespace App\Http\Controllers;

use App\Models\Incident;
use App\Models\PatrolLog;
use App\Models\PatrolSchedule;
use App\Models\Shift;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\View\View;

class DashboardController extends Controller
{
    /**
     * Dashboard Command Center — statistik & aktivitas patroli dari database.
     */
    public function index(Request $request): View
    {
        $now = Carbon::now();
        $today = $now->toDateString();

        // ── Petugas aktif: akun Security Guard dengan status active ──────────
        $activeGuards = User::where('status', 'active')
            ->whereHas('role', fn ($q) => $q->where('role_name', 'Security Guard'))
            ->count();

        // ── Jadwal yang berlaku hari ini (global, berulang setiap hari) ──────
        $todaySchedules = PatrolSchedule::with(['area', 'shiftMaster'])
            ->withCount('checkpoints')
            ->whereIn('status', ['scheduled', 'ongoing'])
            ->orderBy('start_time')
            ->get();

        $scheduleIds = $todaySchedules->pluck('id')->all();
        $totalRoutes = $todaySchedules->count();
        $totalExpectedCheckpoints = (int) $todaySchedules->sum('checkpoints_count');

        // ── Pasangan rute–checkpoint unik yang sudah discan hari ini ─────────
        $scannedPairsToday = $this->scannedPairsToday($scheduleIds, $today);
        $completedOnTimePairs = $this->scannedPairsToday($scheduleIds, $today, 'on_time');

        $completedOnTime = $completedOnTimePairs->count();
        $completionPercentage = $totalExpectedCheckpoints > 0
            ? min((int) round($completedOnTime / $totalExpectedCheckpoints * 100), 100)
            : 0;

        // ── Titik terlewat: checkpoint pada rute yang SUDAH BERAKHIR tetapi
        //    belum ada scan hari ini (scan petugas mana pun dihitung sekali) ─
        $endedScheduleIds = $todaySchedules
            ->filter(fn (PatrolSchedule $schedule) => $this->hasEnded($schedule, $now))
            ->pluck('id')
            ->all();

        $expectedEndedCheckpoints = (int) $todaySchedules
            ->whereIn('id', $endedScheduleIds)
            ->sum('checkpoints_count');

        $scannedEndedPairs = $scannedPairsToday
            ->whereIn('patrol_schedule_id', $endedScheduleIds)
            ->count();

        $missedCheckpoints = max($expectedEndedCheckpoints - $scannedEndedPairs, 0);

        // ── Progres tiap rute untuk panel monitoring ─────────────────────────
        $scannedPairsBySchedule = $scannedPairsToday->groupBy('patrol_schedule_id');

        $lastScans = PatrolLog::query()
            ->whereDate('scanned_at', $today)
            ->whereIn('patrol_schedule_id', $scheduleIds)
            ->selectRaw('patrol_schedule_id, MAX(scanned_at) as last_scanned_at')
            ->groupBy('patrol_schedule_id')
            ->get()
            ->keyBy('patrol_schedule_id');

        $routeProgress = $todaySchedules->map(function (PatrolSchedule $schedule) use ($scannedPairsBySchedule, $lastScans, $now) {
            $total = (int) $schedule->checkpoints_count;
            $scanned = $scannedPairsBySchedule->get($schedule->id, collect())->count();
            $percent = $total > 0 ? min((int) round($scanned / $total * 100), 100) : 0;
            $lastScannedAt = $lastScans->get($schedule->id)?->last_scanned_at;

            return [
                'id' => $schedule->id,
                'name' => $schedule->schedule_name,
                'area' => $schedule->area?->area_name ?? '-',
                'shift' => $schedule->shiftMaster?->shift_name ?? '-',
                'start_time' => Carbon::parse($schedule->start_time)->format('H:i'),
                'end_time' => Carbon::parse($schedule->end_time)->format('H:i'),
                'scanned' => $scanned,
                'total' => $total,
                'percent' => $percent,
                'last_scanned_at' => $lastScannedAt ? Carbon::parse($lastScannedAt)->format('H:i') : null,
                'state' => $this->routeState($schedule, $scanned, $total, $now),
            ];
        })->values()->all();

        // ── Chart: tren scan & insiden 7 hari terakhir ───────────────────────
        $trendDays = collect(range(6, 0))->map(
            fn (int $daysAgo) => $now->copy()->subDays($daysAgo)->toDateString()
        );

        $trendStart = $now->copy()->subDays(6)->startOfDay();
        $trendEnd = $now->copy()->endOfDay();

        $scanCountsByDay = PatrolLog::query()
            ->whereBetween('scanned_at', [$trendStart, $trendEnd])
            ->selectRaw('DATE(scanned_at) as day, COUNT(*) as total')
            ->groupBy('day')
            ->pluck('total', 'day');

        $incidentCountsByDay = Incident::query()
            ->whereBetween('reported_at', [$trendStart, $trendEnd])
            ->selectRaw('DATE(reported_at) as day, COUNT(*) as total')
            ->groupBy('day')
            ->pluck('total', 'day');

        $trend = $trendDays->map(function (string $day) use ($scanCountsByDay, $incidentCountsByDay) {
            return [
                'label' => Carbon::parse($day)->translatedFormat('d M'),
                'scans' => (int) ($scanCountsByDay[$day] ?? 0),
                'incidents' => (int) ($incidentCountsByDay[$day] ?? 0),
            ];
        })->values()->all();

        // ── Chart: rute selesai 100% vs belum per hari ───────────────────────
        $completionRoutes = PatrolSchedule::with('checkpoints')
            ->whereIn('status', ['scheduled', 'ongoing'])
            ->get()
            ->filter(fn (PatrolSchedule $schedule) => $schedule->checkpoints->isNotEmpty());

        $scannedCheckpointsByDay = PatrolLog::query()
            ->whereBetween('scanned_at', [$trendStart, $trendEnd])
            ->selectRaw('DATE(scanned_at) as day, patrol_schedule_id, checkpoint_id')
            ->distinct()
            ->get()
            ->groupBy('day');

        $dailyCompletion = $trendDays->map(function (string $day) use ($completionRoutes, $scannedCheckpointsByDay) {
            $scannedBySchedule = $scannedCheckpointsByDay
                ->get($day, collect())
                ->groupBy('patrol_schedule_id');

            $totalRoutes = $completionRoutes->count();

            $completedRoutes = $completionRoutes->filter(function (PatrolSchedule $schedule) use ($scannedBySchedule) {
                $scannedCheckpointIds = $scannedBySchedule
                    ->get($schedule->id, collect())
                    ->pluck('checkpoint_id');

                return $schedule->checkpoints->pluck('id')->diff($scannedCheckpointIds)->isEmpty();
            })->count();

            return [
                'label' => Carbon::parse($day)->translatedFormat('d M'),
                'completed' => $completedRoutes,
                'incomplete' => max($totalRoutes - $completedRoutes, 0),
                'percent' => $totalRoutes > 0
                    ? (int) round($completedRoutes / $totalRoutes * 100)
                    : 0,
            ];
        })->values()->all();

        // ── Insiden hari ini (semua status) ──────────────────────────────────
        $incidentsToday = Incident::whereDate('reported_at', $today)->count();

        // ── Log patroli terbaru (hari ini) ───────────────────────────────────
        $recentLogs = PatrolLog::with([
            'user' => fn ($query) => $query->withTrashed(),
            'checkpoint' => fn ($query) => $query->withTrashed(),
        ])
            ->whereDate('scanned_at', $today)
            ->orderByDesc('scanned_at')
            ->limit(8)
            ->get();

        // ── Insiden terbaru ──────────────────────────────────────────────────
        $recentIncidents = Incident::with(['reportedBy' => fn ($query) => $query->withTrashed()])
            ->orderByDesc('reported_at')
            ->limit(5)
            ->get();

        // ── Label shift dinamis berdasarkan master shift yang sedang berjalan ─
        $time = now()->format('H:i:s');
        $currentShift = Shift::active()
            ->where(function ($q) use ($time) {
                $q->where(function ($q2) use ($time) {
                    $q2->whereRaw('start_time <= end_time')
                        ->whereTime('start_time', '<=', $time)
                        ->whereTime('end_time', '>=', $time);
                })->orWhere(function ($q2) use ($time) {
                    $q2->whereRaw('end_time < start_time')
                        ->where(function ($q3) use ($time) {
                            $q3->whereTime('start_time', '<=', $time)
                                ->orWhereTime('end_time', '>=', $time);
                        });
                });
            })
            ->orderBy('start_time')
            ->first();

        $shiftLabel = $currentShift
            ? "Shift {$currentShift->shift_name} sedang berjalan"
            : 'Tidak ada shift aktif saat ini';

        return view('dashboard', compact(
            'activeGuards',
            'totalRoutes',
            'completedOnTime',
            'completionPercentage',
            'missedCheckpoints',
            'incidentsToday',
            'recentLogs',
            'recentIncidents',
            'shiftLabel',
            'routeProgress',
            'trend',
            'dailyCompletion'
        ));
    }

    /**
     * Pasangan (rute, checkpoint) unik yang discan pada tanggal tertentu.
     *
     * @param  array<int, int>  $scheduleIds
     * @return Collection<int, PatrolLog>
     */
    private function scannedPairsToday(array $scheduleIds, string $today, ?string $status = null): Collection
    {
        if ($scheduleIds === []) {
            return collect();
        }

        return PatrolLog::query()
            ->whereDate('scanned_at', $today)
            ->whereIn('patrol_schedule_id', $scheduleIds)
            ->when($status, fn ($query) => $query->where('status', $status))
            ->select('patrol_schedule_id', 'checkpoint_id')
            ->distinct()
            ->get();
    }

    /**
     * Apakah jendela jadwal hari ini sudah berakhir? Mendukung shift yang
     * melewati tengah malam (mis. 23:00 → 07:00).
     */
    private function hasEnded(PatrolSchedule $schedule, Carbon $now): bool
    {
        $date = $now->toDateString();
        $start = Carbon::parse($date.' '.$schedule->start_time);
        $end = Carbon::parse($date.' '.$schedule->end_time);

        if ($end->lessThanOrEqualTo($start)) {
            return $now->greaterThanOrEqualTo($end) && $now->lessThan($start);
        }

        return $now->greaterThan($end);
    }

    /**
     * Apakah sekarang berada di dalam jam operasional jadwal? Mendukung
     * shift yang melewati tengah malam.
     */
    private function isWithinWindow(PatrolSchedule $schedule, Carbon $now): bool
    {
        $date = $now->toDateString();
        $start = Carbon::parse($date.' '.$schedule->start_time);
        $end = Carbon::parse($date.' '.$schedule->end_time);

        if ($end->lessThanOrEqualTo($start)) {
            return $now->greaterThanOrEqualTo($start) || $now->lessThanOrEqualTo($end);
        }

        return $now->between($start, $end);
    }

    /**
     * Status tampilan sebuah rute pada panel progres.
     */
    private function routeState(PatrolSchedule $schedule, int $scanned, int $total, Carbon $now): string
    {
        if ($total > 0 && $scanned >= $total) {
            return 'completed';
        }

        if ($schedule->status === 'ongoing' || $this->isWithinWindow($schedule, $now)) {
            return 'ongoing';
        }

        if ($this->hasEnded($schedule, $now)) {
            return 'missed';
        }

        return 'upcoming';
    }
}
