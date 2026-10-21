<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreCheckpointRequest;
use App\Http\Requests\UpdateCheckpointRequest;
use App\Models\Area;
use App\Models\Checkpoint;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class CheckpointController extends Controller
{
    public function create(Area $area): View
    {
        return view('checkpoints.create', compact('area'));
    }

    public function store(StoreCheckpointRequest $request, Area $area): RedirectResponse
    {
        $data = $request->validated();
        $data['area_id'] = $area->id;

        $checkpoint = Checkpoint::create($data);

        return redirect()
            ->route('areas.show', $area)
            ->with('success', "Checkpoint \"{$checkpoint->checkpoint_name}\" berhasil ditambahkan.");
    }

    public function edit(Area $area, Checkpoint $checkpoint): View
    {
        return view('checkpoints.edit', compact('area', 'checkpoint'));
    }

    public function update(UpdateCheckpointRequest $request, Area $area, Checkpoint $checkpoint): RedirectResponse
    {
        $checkpoint->update($request->validated());

        return redirect()
            ->route('areas.show', $area)
            ->with('success', "Checkpoint \"{$checkpoint->checkpoint_name}\" berhasil diperbarui.");
    }

    public function destroy(Area $area, Checkpoint $checkpoint): RedirectResponse
    {
        // Cek secara efisien dengan exists(), bukan count()
        $isUsedInSchedules = $checkpoint->patrolSchedules()->exists();

        $name = $checkpoint->checkpoint_name;
        $checkpoint->delete();

        $message = "Checkpoint \"{$name}\" berhasil dihapus.";
        if ($isUsedInSchedules) {
            $message .= " (Catatan: Checkpoint ini disembunyikan tapi riwayat jadwal terkait tetap aman).";
        }

        return redirect()
            ->route('areas.show', $area)
            ->with('success', $message);
    }
}
