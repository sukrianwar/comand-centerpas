<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreShiftRequest;
use App\Http\Requests\UpdateShiftRequest;
use App\Models\Shift;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\View\View;

class ShiftController extends Controller
{
    /**
     * Halaman daftar shift dengan filter, search, statistik, dan pagination.
     */
    public function index(Request $request): View
    {
        Gate::authorize('viewAny', Shift::class);

        $query = Shift::withCount('patrolSchedules')->orderBy('start_time');

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('shift_name', 'like', "%{$search}%")
                  ->orWhere('shift_code', 'like', "%{$search}%");
            });
        }

        if ($request->filled('status') && in_array($request->status, ['active', 'inactive'])) {
            $query->where('status', $request->status);
        }

        $shifts = $query->paginate(10)->withQueryString();

        // Statistik dihitung dari seluruh shift (tidak terpengaruh filter)
        $allShifts = Shift::all();
        $stats = [
            'total'        => $allShifts->count(),
            'active'       => $allShifts->where('status', 'active')->count(),
            'inactive'     => $allShifts->where('status', 'inactive')->count(),
            'avg_duration' => $allShifts->isNotEmpty()
                ? round($allShifts->avg(fn (Shift $shift) => $shift->duration_hours), 1)
                : 0,
        ];

        return view('shifts.index', compact('shifts', 'stats'));
    }

    public function create(): View
    {
        Gate::authorize('create', Shift::class);

        return view('shifts.create');
    }

    public function store(StoreShiftRequest $request): RedirectResponse
    {
        Gate::authorize('create', Shift::class);

        $data = $request->validated();
        $data['shift_code'] = $this->resolveCode($data['shift_code'] ?? null);

        $shift = Shift::create($data);

        return redirect()
            ->route('shifts.index')
            ->with('success', "Shift \"{$shift->shift_name}\" berhasil ditambahkan.");
    }

    public function show(Shift $shift): RedirectResponse
    {
        Gate::authorize('view', $shift);

        return redirect()->route('shifts.edit', $shift);
    }

    public function edit(Shift $shift): View
    {
        Gate::authorize('update', $shift);

        return view('shifts.edit', compact('shift'));
    }

    public function update(UpdateShiftRequest $request, Shift $shift): RedirectResponse
    {
        Gate::authorize('update', $shift);

        $data = $request->validated();
        $data['shift_code'] = $this->resolveCode($data['shift_code'] ?? null, $shift);

        $shift->update($data);

        return redirect()
            ->route('shifts.index')
            ->with('success', "Shift \"{$shift->shift_name}\" berhasil diperbarui.");
    }

    public function destroy(Shift $shift): RedirectResponse
    {
        Gate::authorize('delete', $shift);

        // Cegah hapus jika shift masih dipakai jadwal patroli.
        if ($shift->patrolSchedules()->exists()) {
            return redirect()
                ->route('shifts.index')
                ->with('error', "Shift \"{$shift->shift_name}\" masih dipakai jadwal patroli. Nonaktifkan saja agar data jadwal tetap aman.");
        }

        $name = $shift->shift_name;
        $shift->delete();

        return redirect()
            ->route('shifts.index')
            ->with('success', "Shift \"{$name}\" berhasil dihapus.");
    }

    /**
     * Ubah status aktif/nonaktif tanpa menghapus shift.
     */
    public function toggleStatus(Shift $shift): RedirectResponse
    {
        Gate::authorize('update', $shift);

        $shift->update([
            'status' => $shift->status === 'active' ? 'inactive' : 'active',
        ]);

        return redirect()
            ->route('shifts.index')
            ->with('success', "Status shift \"{$shift->shift_name}\" diubah menjadi {$shift->status}.");
    }

    /**
     * Gunakan kode yang diisi; bila kosong, buat otomatis (SFT-01, SFT-02, ...).
     */
    private function resolveCode(?string $code, ?Shift $shift = null): string
    {
        $code = strtoupper(trim((string) $code));

        if ($code !== '') {
            return $code;
        }

        if ($shift && $shift->shift_code) {
            return $shift->shift_code;
        }

        $next = ((int) Shift::withTrashed()->max('id')) + 1;

        do {
            $candidate = 'SFT-'.str_pad((string) $next, 2, '0', STR_PAD_LEFT);
            $exists = Shift::withTrashed()->where('shift_code', $candidate)->exists();
            $next++;
        } while ($exists);

        return $candidate;
    }
}
