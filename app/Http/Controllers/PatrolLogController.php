<?php

namespace App\Http\Controllers;

use App\Http\Requests\StorePatrolLogRequest;
use App\Models\Checkpoint;
use App\Models\PatrolLog;
use App\Models\PatrolSchedule;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class PatrolLogController extends Controller
{
    // ─────────────────────────────────────────────────────────────────────────
    // patrol() — Halaman pencatatan checkpoint, kirim data jadwal aktif ke view
    // ─────────────────────────────────────────────────────────────────────────

    public function patrol(): View
    {
        $user = auth()->user();

        // Jadwal patroli bersifat global (berlaku setiap hari, tanpa petugas
        // dan tanggal tertentu). Ambil semua jadwal yang sedang aktif.
        $activeSchedules = PatrolSchedule::with(['checkpoints' => function ($q) {
            $q->orderByPivot('sequence_number', 'asc');
        }, 'area'])
            ->activeNow()
            ->orderBy('start_time')
            ->get();

        // Progress scan per jadwal, dihitung per petugas yang login HARI INI
        // (jadwal berlaku harian — scan kemarin tidak dihitung ulang)
        $today = now()->toDateString();
        $activeSchedules->each(function ($schedule) use ($user, $today) {
            $schedule->scannedCheckpointIds = PatrolLog::where('patrol_schedule_id', $schedule->id)
                ->where('user_id', $user->id)
                ->whereDate('scanned_at', $today)
                ->pluck('checkpoint_id')
                ->toArray();
        });

        // Semua jadwal hari ini (aktif + mendatang) agar petugas selalu
        // melihat jadwal yang sudah diinput admin, meski belum masuk jamnya.
        $todaySchedules = PatrolSchedule::with('area')
            ->whereIn('status', ['scheduled', 'ongoing'])
            ->orderBy('start_time')
            ->get();

        $activeScheduleIds = $activeSchedules->pluck('id')->all();

        return view('member.patrol', compact('activeSchedules', 'todaySchedules', 'activeScheduleIds'));
    }

    // ─────────────────────────────────────────────────────────────────────────
    // store() — Simpan hasil pencatatan checkpoint (dipanggil via AJAX/fetch)
    // ─────────────────────────────────────────────────────────────────────────

    public function store(StorePatrolLogRequest $request): JsonResponse
    {
        $user = $request->user();

        // 1. Ambil Checkpoint dan PatrolSchedule
        $checkpoint = Checkpoint::findOrFail($request->checkpoint_id);
        $schedule = PatrolSchedule::with('checkpoints')->findOrFail($request->patrol_schedule_id);

        // 2. Pastikan jadwal masih dalam periode aktif (global, berlaku harian).
        //    Jadwal yang statusnya bukan scheduled/ongoing atau di luar jam
        //    aktifnya tidak boleh dicatat.
        $now = Carbon::now();
        $scheduleDate = $now->toDateString();
        $startTime = Carbon::createFromFormat('Y-m-d H:i:s', $scheduleDate.' '.$schedule->start_time);
        $endTime = Carbon::createFromFormat('Y-m-d H:i:s', $scheduleDate.' '.$schedule->end_time);

        // Tangani kasus shift malam yang melewati tengah malam (end < start)
        if ($endTime->lessThanOrEqualTo($startTime)) {
            $endTime->addDay();
        }

        // Status 'ongoing' selalu aktif; status 'scheduled' harus masih
        // berada di dalam window jam aktifnya.
        $inWindow = $now->between($startTime, $endTime);

        if (! in_array($schedule->status, ['scheduled', 'ongoing']) || ($schedule->status === 'scheduled' && ! $inWindow)) {
            return response()->json([
                'success' => false,
                'message' => 'Jadwal patroli ini sedang tidak aktif (di luar jam atau sudah berakhir).',
            ], 422);
        }

        // 3. Pastikan checkpoint ini memang bagian dari rute jadwal ini
        $checkpointIds = $schedule->checkpoints->pluck('id')->toArray();
        if (! in_array($checkpoint->id, $checkpointIds)) {
            return response()->json([
                'success' => false,
                'message' => 'Checkpoint ini bukan bagian dari rute jadwal Anda.',
            ], 422);
        }

        // 4. Cek apakah checkpoint ini SUDAH pernah discan oleh petugas ini
        //    di jadwal yang sama HARI INI (progress scan per petugas per hari,
        //    jadwal dipakai bersama dan berlaku harian)
        $alreadyScanned = PatrolLog::where('patrol_schedule_id', $schedule->id)
            ->where('user_id', $user->id)
            ->where('checkpoint_id', $checkpoint->id)
            ->whereDate('scanned_at', $now->toDateString())
            ->exists();
        if ($alreadyScanned) {
            return response()->json([
                'success' => false,
                'message' => 'Checkpoint ini sudah dicatat sebelumnya dalam jadwal ini.',
            ], 422);
        }

        // 5. Simpan foto ke storage/app/public/patrol-photos/
        $photoPath = $request->file('photo')->store('patrol-photos', 'public');

        // 6. Status. Pencatatan hanya lolos bila jadwal 'ongoing' (selalu aktif)
        //    atau 'scheduled' yang masih di dalam window jam aktifnya (lihat
        //    langkah 2). Karena di luar window sudah ditolak, semua catatan
        //    yang tersimpan di sini selalu tepat waktu ('on_time').
        //    Nilai enum 'late' disediakan untuk kebutuhan sistem lain (mis. impor
        //    data historis) dan tidak dipakai oleh alur pencatatan saat ini.
        $status = 'on_time';

        // 7. Simpan PatrolLog
        $log = PatrolLog::create([
            'patrol_schedule_id' => $schedule->id,
            'checkpoint_id' => $checkpoint->id,
            'user_id' => $user->id,
            'scanned_at' => $now,
            'photo_path' => $photoPath,
            'status' => $status,
            'notes' => $request->notes,
        ]);

        // 8. Hitung progress terkini: berapa checkpoint sudah dicatat hari ini
        //    oleh petugas ini di jadwal ini
        $totalCheckpoints = count($checkpointIds);
        $scannedLogs = PatrolLog::where('patrol_schedule_id', $schedule->id)
            ->where('user_id', $user->id)
            ->whereDate('scanned_at', $now->toDateString())
            ->pluck('checkpoint_id');
        $scannedCount = $scannedLogs->count();
        $scannedIds = $scannedLogs->toArray();
        $isComplete = $scannedCount >= $totalCheckpoints;

        return response()->json([
            'success' => true,
            'message' => $isComplete
                ? 'Patroli selesai! Semua checkpoint sudah dicatat.'
                : "Checkpoint berhasil dicatat! ({$scannedCount}/{$totalCheckpoints})",
            'status' => $status,
            'scanned_count' => $scannedCount,
            'total_checkpoints' => $totalCheckpoints,
            'scanned_ids' => $scannedIds,
            'is_complete' => $isComplete,
            'photo_url' => Storage::disk('public')->url($photoPath),
        ]);
    }

    // ─────────────────────────────────────────────────────────────────────────
    // index() — Halaman riwayat scan milik user yang login
    // ─────────────────────────────────────────────────────────────────────────

    public function index(Request $request): View
    {
        $userId = auth()->id();

        $query = PatrolLog::with(['checkpoint', 'patrolSchedule.area'])
            ->where('user_id', $userId)
            ->orderBy('scanned_at', 'desc');

        // Filter tanggal dari
        if ($request->filled('date_from')) {
            $query->whereDate('scanned_at', '>=', $request->date_from);
        }

        // Filter tanggal sampai
        if ($request->filled('date_to')) {
            $query->whereDate('scanned_at', '<=', $request->date_to);
        }

        // Filter status
        if ($request->filled('status') && in_array($request->status, ['on_time', 'late'])) {
            $query->where('status', $request->status);
        }

        $logs = $query->paginate(15)->withQueryString();

        return view('member.riwayat.index', compact('logs'));
    }
}
