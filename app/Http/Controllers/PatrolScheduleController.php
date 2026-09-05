<?php

namespace App\Http\Controllers;

use App\Http\Requests\StorePatrolScheduleRequest;
use App\Http\Requests\UpdatePatrolScheduleRequest;
use App\Models\Area;
use App\Models\Checkpoint;
use App\Models\PatrolSchedule;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Support\Facades\Gate;

class PatrolScheduleController extends Controller
{
    /**
     * Halaman daftar jadwal patroli dengan filter, search, dan pagination.
     */
    public function index(Request $request): View
    {
        Gate::authorize('viewAny', PatrolSchedule::class);

        $query = PatrolSchedule::with(['area', 'checkpoints'])
            ->orderBy('created_at', 'desc')
            ->orderBy('start_time', 'asc');

        // ── Filter berdasarkan area ──────────────────────────────────────────
        if ($request->filled('area_id')) {
            $query->where('area_id', $request->area_id);
        }

        // ── Filter berdasarkan shift ─────────────────────────────────────────
        if ($request->filled('shift') && in_array($request->shift, ['pagi', 'malam'])) {
            $query->where('shift', $request->shift);
        }

        // ── Filter berdasarkan status ────────────────────────────────────────
        if ($request->filled('status') && in_array($request->status, ['scheduled', 'ongoing', 'completed', 'missed'])) {
            $query->where('status', $request->status);
        }

        // ── Pencarian (schedule_name, area_name) ────────────────────────────
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('schedule_name', 'like', "%{$search}%")
                  ->orWhereHas('area', fn ($q) => $q->where('area_name', 'like', "%{$search}%"));
            });
        }

        // ── Pagination (pertahankan query string) ───────────────────────────
        $schedules = $query->paginate(10)->withQueryString();

        // ── Statistik card (query terpisah agar tidak terpengaruh filter) ───
        $stats = [
            'total'  => PatrolSchedule::count(),
            'pagi'   => PatrolSchedule::where('shift', 'pagi')->count(),
            'malam'  => PatrolSchedule::where('shift', 'malam')->count(),
        ];

        // ── Data untuk dropdown filter area ─────────────────────────────────
        $areas = Area::orderBy('area_name')->get(['id', 'area_name']);

        return view('patrol-schedules.index', compact('schedules', 'stats', 'areas'));
    }

    /**
     * Halaman form buat jadwal baru.
     */
    public function create(): View
    {
        Gate::authorize('create', PatrolSchedule::class);

        $areas       = Area::orderBy('area_name')->get(['id', 'area_name', 'area_code']);
        $checkpoints = Checkpoint::where('status', 'active')
                                 ->with('area:id,area_name')
                                 ->orderBy('checkpoint_name')
                                 ->get(['id', 'checkpoint_name', 'area_id']);

        return view('patrol-schedules.create', compact('areas', 'checkpoints'));
    }

    /**
     * Simpan jadwal baru ke database.
     */
    public function store(StorePatrolScheduleRequest $request): RedirectResponse
    {
        Gate::authorize('create', PatrolSchedule::class);

        $schedule = PatrolSchedule::create($request->safe()->except('checkpoints'));

        // Sync relasi checkpoints ke pivot dengan sequence_number berurutan
        $checkpointSync = [];
        foreach ($request->checkpoints as $sequence => $checkpointId) {
            $checkpointSync[$checkpointId] = ['sequence_number' => $sequence + 1];
        }
        $schedule->checkpoints()->sync($checkpointSync);

        return redirect()
            ->route('patrol-schedules.index')
            ->with('success', "Jadwal patroli \"{$schedule->schedule_name}\" berhasil dibuat.");
    }

    /**
     * Halaman detail jadwal (opsional — view belum dibuat, redirect ke edit).
     */
    public function show(PatrolSchedule $patrolSchedule): RedirectResponse
    {
        Gate::authorize('view', $patrolSchedule);
        return redirect()->route('patrol-schedules.edit', $patrolSchedule);
    }

    /**
     * Halaman form edit jadwal yang sudah ada.
     */
    public function edit(PatrolSchedule $patrolSchedule): View
    {
        Gate::authorize('update', $patrolSchedule);

        // Load relasi yang dibutuhkan form edit (termasuk pivot sequence_number)
        $patrolSchedule->load(['area', 'checkpoints']);

        $areas       = Area::orderBy('area_name')->get(['id', 'area_name', 'area_code']);
        $checkpoints = Checkpoint::where('status', 'active')
                                 ->with('area:id,area_name')
                                 ->orderBy('checkpoint_name')
                                 ->get(['id', 'checkpoint_name', 'area_id']);

        // ID checkpoint yang sudah dipilih (untuk pre-select di form)
        $selectedCheckpoints = $patrolSchedule->checkpoints->pluck('id')->toArray();

        return view('patrol-schedules.edit', compact(
            'patrolSchedule',
            'areas',
            'checkpoints',
            'selectedCheckpoints'
        ));
    }

    /**
     * Update jadwal yang sudah ada.
     */
    public function update(UpdatePatrolScheduleRequest $request, PatrolSchedule $patrolSchedule): RedirectResponse
    {
        Gate::authorize('update', $patrolSchedule);

        $patrolSchedule->update($request->safe()->except('checkpoints'));

        // Sync ulang relasi checkpoints dengan sequence_number baru
        $checkpointSync = [];
        foreach ($request->checkpoints as $sequence => $checkpointId) {
            $checkpointSync[$checkpointId] = ['sequence_number' => $sequence + 1];
        }
        $patrolSchedule->checkpoints()->sync($checkpointSync);

        return redirect()
            ->route('patrol-schedules.index')
            ->with('success', "Jadwal patroli \"{$patrolSchedule->schedule_name}\" berhasil diperbarui.");
    }

    /**
     * Soft delete jadwal patroli.
     */
    public function destroy(PatrolSchedule $patrolSchedule): RedirectResponse
    {
        Gate::authorize('delete', $patrolSchedule);

        $name = $patrolSchedule->schedule_name;
        $patrolSchedule->delete(); // SoftDeletes: mengisi deleted_at, data tidak benar-benar dihapus

        return redirect()
            ->route('patrol-schedules.index')
            ->with('success', "Jadwal patroli \"{$name}\" berhasil dihapus.");
    }
}
