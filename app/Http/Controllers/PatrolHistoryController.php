<?php

namespace App\Http\Controllers;

use App\Exports\PatrolHistoryExport;
use App\Models\Area;
use App\Models\PatrolLog;
use App\Models\PatrolSchedule;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;
use Maatwebsite\Excel\Facades\Excel;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class PatrolHistoryController extends Controller
{
    /**
     * Halaman monitoring seluruh riwayat patroli.
     */
    public function index(Request $request): View
    {
        $this->authorize('viewAny', PatrolLog::class);

        $filters = $this->filters($request);
        $baseQuery = $this->buildQuery($filters);

        $stats = [
            'total' => (clone $baseQuery)->count(),
            'guards' => (clone $baseQuery)->distinct()->count('user_id'),
            'on_time' => (clone $baseQuery)->where('status', 'on_time')->count(),
            'late' => (clone $baseQuery)->where('status', 'late')->count(),
        ];

        $logs = (clone $baseQuery)
            ->with(['user', 'checkpoint', 'photos', 'patrolSchedule.area', 'patrolSchedule.shiftMaster'])
            ->orderByDesc('scanned_at')
            ->orderByDesc('id')
            ->paginate(20)
            ->withQueryString();

        $guards = User::query()
            ->whereHas('role', fn ($query) => $query->where('role_name', 'Security Guard'))
            ->orderBy('name')
            ->get(['id', 'name', 'username']);

        $areas = Area::orderBy('area_name')->get(['id', 'area_name']);
        $schedules = PatrolSchedule::orderBy('schedule_name')->get(['id', 'schedule_name']);

        $logsJson = $logs->getCollection()
            ->map(fn (PatrolLog $log) => [
                'id' => $log->id,
                'scanned_at' => $log->scanned_at?->format('d M Y, H:i'),
                'guard' => $log->user?->name ?? '-',
                'guard_username' => $log->user?->username,
                'checkpoint' => $log->checkpoint?->checkpoint_name ?? '-',
                'schedule' => $log->patrolSchedule?->schedule_name ?? '-',
                'area' => $log->patrolSchedule?->area?->area_name ?? '-',
                'shift' => $log->patrolSchedule?->shiftMaster?->shift_name ?? '-',
                'status' => $log->status === 'late' ? 'Terlambat' : 'Tepat Waktu',
                'notes' => $log->notes,
                'photo_urls' => $this->photoUrls($log),
            ])
            ->values()
            ->all();

        return view('patrol-history.index', compact(
            'logs',
            'stats',
            'guards',
            'areas',
            'schedules',
            'filters',
            'logsJson'
        ));
    }

    /**
     * Export hasil filter ke file Excel (.xlsx).
     */
    public function export(Request $request): BinaryFileResponse
    {
        $this->authorize('viewAny', PatrolLog::class);

        $filters = $this->filters($request);

        $query = $this->buildQuery($filters)
            ->with(['user', 'checkpoint', 'photos', 'patrolSchedule.area', 'patrolSchedule.shiftMaster'])
            ->orderByDesc('scanned_at')
            ->orderByDesc('id');

        $filename = 'riwayat-patroli-'.now()->format('Ymd-His').'.xlsx';

        return Excel::download(new PatrolHistoryExport($query), $filename);
    }

    /**
     * Kumpulkan URL seluruh foto bukti (fallback ke kolom lama bila relasi kosong).
     */
    private function photoUrls(PatrolLog $log): array
    {
        $paths = $log->photos->pluck('photo_path')->filter()->all();

        if ($paths === [] && $log->photo_path) {
            $paths = [$log->photo_path];
        }

        return array_map(
            fn (string $path) => Storage::disk('public')->url($path),
            $paths
        );
    }

    /**
     * Ambil dan validasi filter, sekaligus terapkan default hari ini.
     */
    private function filters(Request $request): array
    {
        $hasFilters = $request->hasAny([
            'date_from',
            'date_to',
            'user_id',
            'area_id',
            'patrol_schedule_id',
            'status',
            'search',
        ]);

        $validated = $request->validate([
            'date_from' => ['nullable', 'date_format:Y-m-d'],
            'date_to' => ['nullable', 'date_format:Y-m-d', 'after_or_equal:date_from'],
            'user_id' => ['nullable', 'integer', 'exists:users,id'],
            'area_id' => ['nullable', 'integer', 'exists:areas,id'],
            'patrol_schedule_id' => ['nullable', 'integer', 'exists:patrol_schedules,id'],
            'status' => ['nullable', 'in:on_time,late'],
            'search' => ['nullable', 'string', 'max:150'],
        ]);

        return [
            'date_from' => $validated['date_from'] ?? ($hasFilters ? null : now()->toDateString()),
            'date_to' => $validated['date_to'] ?? ($hasFilters ? null : now()->toDateString()),
            'user_id' => $validated['user_id'] ?? null,
            'area_id' => $validated['area_id'] ?? null,
            'patrol_schedule_id' => $validated['patrol_schedule_id'] ?? null,
            'status' => $validated['status'] ?? null,
            'search' => isset($validated['search']) ? trim($validated['search']) : null,
        ];
    }

    /**
     * Query dasar yang dipakai halaman monitoring dan export.
     */
    private function buildQuery(array $filters): Builder
    {
        return PatrolLog::query()
            ->when($filters['date_from'], fn (Builder $query, string $date) => $query->whereDate('scanned_at', '>=', $date))
            ->when($filters['date_to'], fn (Builder $query, string $date) => $query->whereDate('scanned_at', '<=', $date))
            ->when($filters['user_id'], fn (Builder $query, $userId) => $query->where('user_id', $userId))
            ->when($filters['area_id'], fn (Builder $query, $areaId) => $query->whereHas(
                'patrolSchedule',
                fn (Builder $scheduleQuery) => $scheduleQuery->where('area_id', $areaId)
            ))
            ->when($filters['patrol_schedule_id'], fn (Builder $query, $scheduleId) => $query->where('patrol_schedule_id', $scheduleId))
            ->when($filters['status'], fn (Builder $query, string $status) => $query->where('status', $status))
            ->when($filters['search'], function (Builder $query, string $search) {
                $query->where(function (Builder $nestedQuery) use ($search) {
                    $nestedQuery
                        ->whereHas('user', fn (Builder $userQuery) => $userQuery->where('name', 'like', "%{$search}%"))
                        ->orWhereHas('checkpoint', fn (Builder $checkpointQuery) => $checkpointQuery->where('checkpoint_name', 'like', "%{$search}%"))
                        ->orWhereHas('patrolSchedule', fn (Builder $scheduleQuery) => $scheduleQuery->where('schedule_name', 'like', "%{$search}%"));
                });
            });
    }
}
