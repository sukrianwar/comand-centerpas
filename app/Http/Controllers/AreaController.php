<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreAreaRequest;
use App\Http\Requests\UpdateAreaRequest;
use App\Models\Area;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AreaController extends Controller
{
    public function index(Request $request): View
    {
        $query = Area::withCount(['checkpoints', 'patrolSchedules'])
            ->orderBy('area_name', 'asc');

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('area_name', 'like', "%{$search}%")
                  ->orWhere('area_code', 'like', "%{$search}%");
            });
        }

        $areas = $query->paginate(10)->withQueryString();

        return view('areas.index', compact('areas'));
    }

    public function create(): View
    {
        return view('areas.create');
    }

    public function store(StoreAreaRequest $request): RedirectResponse
    {
        $area = Area::create($request->validated());

        return redirect()
            ->route('areas.index')
            ->with('success', "Area \"{$area->area_name}\" berhasil ditambahkan.");
    }

    public function show(Area $area): RedirectResponse
    {
        return redirect()->route('areas.edit', $area);
    }

    public function edit(Area $area): View
    {
        return view('areas.edit', compact('area'));
    }

    public function update(UpdateAreaRequest $request, Area $area): RedirectResponse
    {
        $area->update($request->validated());

        return redirect()
            ->route('areas.index')
            ->with('success', "Area \"{$area->area_name}\" berhasil diperbarui.");
    }

    public function destroy(Area $area): RedirectResponse
    {
        $area->delete();

        return redirect()
            ->route('areas.index')
            ->with('success', "Area berhasil dihapus. Data checkpoint/jadwal yang terkait tetap tersimpan.");
    }
}
