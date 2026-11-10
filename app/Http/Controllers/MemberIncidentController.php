<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreIncidentRequest;
use App\Models\Incident;
use App\Models\IncidentStatusHistory;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Throwable;

class MemberIncidentController extends Controller
{
    /**
     * Simpan laporan insiden/temuan dari portal member.
     */
    public function store(StoreIncidentRequest $request): JsonResponse
    {
        $this->authorize('create', Incident::class);

        $validated = $request->validated();
        $storedPaths = [];

        try {
            $incident = DB::transaction(function () use ($request, $validated, &$storedPaths) {
                foreach ($request->file('photos') as $photo) {
                    $storedPaths[] = $photo->store('incident-photos', 'public');
                }

                $incident = Incident::create([
                    'user_id' => $request->user()->id,
                    'area_id' => $validated['area_id'],
                    'title' => $validated['title'],
                    'category' => $validated['category'],
                    'description' => $validated['description'],
                    'photo' => $storedPaths[0],
                    'latitude' => $validated['latitude'] ?? null,
                    'longitude' => $validated['longitude'] ?? null,
                    'priority' => $validated['priority'],
                    'status' => 'new',
                    'sync_status' => 'synced',
                    'reported_at' => now(),
                ]);

                $incident->photos()->createMany(
                    array_map(fn (string $path) => ['photo_path' => $path], $storedPaths)
                );

                IncidentStatusHistory::create([
                    'incident_id' => $incident->id,
                    'status' => 'new',
                    'changed_by' => $request->user()->id,
                    'notes' => 'Laporan dibuat melalui portal member.',
                    'changed_at' => now(),
                ]);

                return $incident;
            });
        } catch (Throwable $exception) {
            if ($storedPaths !== []) {
                Storage::disk('public')->delete($storedPaths);
            }

            throw $exception;
        }

        return response()->json([
            'success' => true,
            'message' => 'Laporan insiden berhasil dikirim.',
            'incident' => [
                'id' => $incident->id,
                'code' => '#INC-'.str_pad((string) $incident->id, 3, '0', STR_PAD_LEFT),
                'title' => $incident->title,
            ],
        ], 201);
    }
}
