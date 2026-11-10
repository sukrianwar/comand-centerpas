<?php

use App\Models\Area;
use App\Models\Checkpoint;
use App\Models\PatrolLog;
use App\Models\PatrolSchedule;
use App\Models\Role;
use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

function patrolScanUserWithRole(string $roleName): User
{
    $role = Role::firstOrCreate(
        ['role_name' => $roleName],
        ['description' => $roleName]
    );

    return User::factory()->create(['role_id' => $role->id]);
}

function patrolScanPhotos(int $count = 3): array
{
    return array_map(
        fn (int $index) => UploadedFile::fake()->image("bukti-patroli-{$index}.png", 800, 600),
        range(1, $count)
    );
}

function patrolScanSetup(): array
{
    $area = Area::create([
        'area_name' => 'Area Patroli Test',
        'area_code' => 'AREA-PS',
        'description' => 'Area pengujian scan patroli.',
    ]);

    $schedule = PatrolSchedule::create([
        'schedule_name' => 'Rute Malam',
        'area_id' => $area->id,
        'start_time' => '00:00:00',
        'end_time' => '23:59:59',
        'status' => 'ongoing',
    ]);

    $checkpoint = Checkpoint::create([
        'area_id' => $area->id,
        'checkpoint_name' => 'Pos Utama',
        'status' => 'active',
    ]);

    $schedule->checkpoints()->attach($checkpoint->id, ['sequence_number' => 1]);

    return [$schedule, $checkpoint];
}

function patrolScanPayload(PatrolSchedule $schedule, Checkpoint $checkpoint, array $overrides = []): array
{
    return array_merge([
        'patrol_schedule_id' => $schedule->id,
        'checkpoint_id' => $checkpoint->id,
        'photos' => patrolScanPhotos(),
        'notes' => 'Kondisi aman.',
    ], $overrides);
}

test('guard can submit a checkpoint scan with three photos', function () {
    Storage::fake('public');

    $guard = patrolScanUserWithRole('Security Guard');
    [$schedule, $checkpoint] = patrolScanSetup();

    $this->actingAs($guard)
        ->post(route('member.patrol.scan'), patrolScanPayload($schedule, $checkpoint), [
            'Accept' => 'application/json',
        ])
        ->assertOk()
        ->assertJsonPath('success', true)
        ->assertJsonCount(3, 'photo_urls');

    $log = PatrolLog::firstOrFail();

    expect($log->user_id)->toBe($guard->id);
    expect($log->photos)->toHaveCount(3);
    expect($log->photo_path)->toBe($log->photos->first()->photo_path);

    $this->assertDatabaseCount('patrol_log_photos', 3);

    $log->photos->each(
        fn ($photo) => Storage::disk('public')->assertExists($photo->photo_path)
    );
});

test('checkpoint scan requires at least three photos', function () {
    Storage::fake('public');

    $guard = patrolScanUserWithRole('Security Guard');
    [$schedule, $checkpoint] = patrolScanSetup();

    $this->actingAs($guard)
        ->post(route('member.patrol.scan'), patrolScanPayload($schedule, $checkpoint, [
            'photos' => patrolScanPhotos(2),
        ]), ['Accept' => 'application/json'])
        ->assertStatus(422)
        ->assertJsonValidationErrors('photos');

    expect(PatrolLog::count())->toBe(0);
    $this->assertDatabaseCount('patrol_log_photos', 0);
});

test('checkpoint scan accepts at most five photos', function () {
    Storage::fake('public');

    $guard = patrolScanUserWithRole('Security Guard');
    [$schedule, $checkpoint] = patrolScanSetup();

    $this->actingAs($guard)
        ->post(route('member.patrol.scan'), patrolScanPayload($schedule, $checkpoint, [
            'photos' => patrolScanPhotos(6),
        ]), ['Accept' => 'application/json'])
        ->assertStatus(422)
        ->assertJsonValidationErrors('photos');

    expect(PatrolLog::count())->toBe(0);
    $this->assertDatabaseCount('patrol_log_photos', 0);
});
