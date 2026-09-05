<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreCheckpointRequest;
use App\Http\Requests\UpdateCheckpointRequest;
use App\Models\Area;
use App\Models\Checkpoint;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\View\View;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

class CheckpointController extends Controller
{
    public function index(Request $request): View
    {
        // Optimasi: withCount('patrolSchedules') menggantikan full load relasi
        // with('area') untuk mendapatkan area_name
        $query = Checkpoint::with('area')->withCount('patrolSchedules')
            ->orderBy('created_at', 'desc');

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('checkpoint_name', 'like', "%{$search}%")
                  ->orWhere('qr_code', 'like', "%{$search}%");
            });
        }

        if ($request->filled('area_id')) {
            $query->where('area_id', $request->area_id);
        }

        if ($request->filled('status') && in_array($request->status, ['active', 'inactive'])) {
            $query->where('status', $request->status);
        }

        $checkpoints = $query->paginate(15)->withQueryString();

        // Select spesifik untuk dropdown filter area agar lebih cepat
        $areas = Area::select('id', 'area_name')->orderBy('area_name')->get();

        return view('checkpoints.index', compact('checkpoints', 'areas'));
    }

    public function create(): View
    {
        // Select spesifik untuk dropdown form
        $areas = Area::select('id', 'area_name')->orderBy('area_name')->get();
        return view('checkpoints.create', compact('areas'));
    }

    public function store(StoreCheckpointRequest $request): RedirectResponse
    {
        $data = $request->validated();
        
        // Auto-generate QR code
        $data['qr_code'] = Checkpoint::generateUniqueQrCode();
        
        // Set default radius jika kosong
        if (!isset($data['radius'])) {
            $data['radius'] = 20;
        }

        $checkpoint = Checkpoint::create($data);

        return redirect()
            ->route('checkpoints.index')
            ->with('success', "Checkpoint \"{$checkpoint->checkpoint_name}\" berhasil ditambahkan.");
    }

    public function show(Checkpoint $checkpoint): RedirectResponse
    {
        return redirect()->route('checkpoints.edit', $checkpoint);
    }

    public function edit(Checkpoint $checkpoint): View
    {
        $areas = Area::select('id', 'area_name')->orderBy('area_name')->get();
        return view('checkpoints.edit', compact('checkpoint', 'areas'));
    }

    public function update(UpdateCheckpointRequest $request, Checkpoint $checkpoint): RedirectResponse
    {
        $data = $request->validated();
        
        // Pastikan default radius ter-set jika user mengosongkannya saat edit
        if (!isset($data['radius'])) {
            $data['radius'] = 20;
        }
        
        // qr_code tidak di-update dari form

        $checkpoint->update($data);

        return redirect()
            ->route('checkpoints.index')
            ->with('success', "Checkpoint \"{$checkpoint->checkpoint_name}\" berhasil diperbarui.");
    }

    public function destroy(Checkpoint $checkpoint): RedirectResponse
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
            ->route('checkpoints.index')
            ->with('success', $message);
    }

    /**
     * Gambar QR Code checkpoint — isi QR = nilai qr_code checkpoint.
     * Format SVG (murni PHP, tanpa ekstensi imagick). Dipakai untuk
     * ditampilkan, diunduh, dan dicetak petugas di lokasi.
     */
    public function showQr(Checkpoint $checkpoint): Response
    {
        $svg = QrCode::format('svg')
            ->size(300)
            ->margin(1)
            ->errorCorrection('M')
            ->generate($checkpoint->qr_code);

        return response($svg, 200, [
            'Content-Type' => 'image/svg+xml; charset=utf-8',
            'Cache-Control' => 'public, max-age=86400',
        ]);
    }

    /**
     * Halaman cetak massal QR semua checkpoint aktif dalam satu area.
     */
    public function printQr(Request $request): View
    {
        $area = Area::findOrFail($request->integer('area_id'));

        $checkpoints = Checkpoint::with('area')
            ->where('area_id', $area->id)
            ->where('status', 'active')
            ->orderBy('checkpoint_name')
            ->get(['id', 'area_id', 'checkpoint_name', 'qr_code']);

        return view('checkpoints.qr-print', compact('area', 'checkpoints'));
    }
}
