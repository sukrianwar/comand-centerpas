<?php

use App\Models\Area;
use App\Models\Checkpoint;
use App\Models\Incident;
use App\Models\PatrolLog;
use App\Models\PatrolSchedule;
use App\Models\Role;
use App\Models\User;
use Carbon\Carbon;

afterEach(function () {
    Carbon::setTestNow();
});

function dashboardUserWithRole(string $roleName): User
{
    $role = Role::firstOrCreate(
        ['role_name' => $roleName],
        ['description' => $roleName]
    );

    return User::factory()->create(['role_id' => $role->id]);
}

function dashboardArea(string $code = 'AREA-D1'): Area
{
    return Area::create([
        'area_name' => 'Area '.$code,
        'area_code' => $code,
        'description' => 'Area pengujian dashboard.',
    ]);
}

function dashboardSchedule(Area $area, array $overrides = []): PatrolSchedule
{
    return PatrolSchedule::create(array_merge([
        'schedule_name' => 'Rute Dashboard',
        'area_id' => $area->id,
        'start_time' => '08:00:00',
        'end_time' => '17:00:00',
        'status' => 'scheduled',
    ], $overrides));
}

function dashboardCheckpoint(Area $area, string $name): Checkpoint
{
    return Checkpoint::create([
        'area_id' => $area->id,
        'checkpoint_name' => $name,
        'status' => 'active',
    ]);
}

function dashboardAttachCheckpoint(PatrolSchedule $schedule, Checkpoint $checkpoint, int $sequence): void
{
    $schedule->checkpoints()->attach($checkpoint->id, ['sequence_number' => $sequence]);
}

function dashboardLog(
    User $guard,
    PatrolSchedule $schedule,
    Checkpoint $checkpoint,
    string $status = 'on_time',
    ?Carbon $scannedAt = null,
): PatrolLog {
    return PatrolLog::create([
        'patrol_schedule_id' => $schedule->id,
        'checkpoint_id' => $checkpoint->id,
        'user_id' => $guard->id,
        'scanned_at' => $scannedAt ?? now(),
        'photo_path' => 'patrol-photos/test.jpg',
        'status' => $status,
    ]);
}

test('non guard user can view the admin dashboard', function () {
    $admin = dashboardUserWithRole('Admin');

    $this->actingAs($admin)
        ->get(route('dashboard'))
        ->assertOk();
});

test('completed on time is calculated per unique checkpoint', function () {
    Carbon::setTestNow('2026-11-08 12:00:00');

    $admin = dashboardUserWithRole('Admin');
    $guardA = dashboardUserWithRole('Security Guard');
    $guardB = dashboardUserWithRole('Security Guard');
    $area = dashboardArea();
    $schedule = dashboardSchedule($area, ['status' => 'ongoing']);

    $checkpointA = dashboardCheckpoint($area, 'Pos 1');
    $checkpointB = dashboardCheckpoint($area, 'Pos 2');
    dashboardAttachCheckpoint($schedule, $checkpointA, 1);
    dashboardAttachCheckpoint($schedule, $checkpointB, 2);

    dashboardLog($guardA, $schedule, $checkpointA, 'on_time');
    dashboardLog($guardB, $schedule, $checkpointA, 'on_time');
    dashboardLog($guardA, $schedule, $checkpointB, 'late');

    $this->actingAs($admin)
        ->get(route('dashboard'))
        ->assertOk()
        ->assertViewHas('totalRoutes', 1)
        ->assertViewHas('completedOnTime', 1)
        ->assertViewHas('completionPercentage', 50);
});

test('missed checkpoints only count schedules that already ended', function () {
    Carbon::setTestNow('2026-11-08 18:00:00');

    $admin = dashboardUserWithRole('Admin');
    $guard = dashboardUserWithRole('Security Guard');
    $area = dashboardArea();

    $ended = dashboardSchedule($area, [
        'schedule_name' => 'Rute Selesai',
        'start_time' => '08:00:00',
        'end_time' => '12:00:00',
    ]);
    $checkpointA = dashboardCheckpoint($area, 'Pos A');
    $checkpointB = dashboardCheckpoint($area, 'Pos B');
    dashboardAttachCheckpoint($ended, $checkpointA, 1);
    dashboardAttachCheckpoint($ended, $checkpointB, 2);
    dashboardLog($guard, $ended, $checkpointA, 'on_time');

    $upcoming = dashboardSchedule($area, [
        'schedule_name' => 'Rute Nanti',
        'start_time' => '19:00:00',
        'end_time' => '23:00:00',
    ]);
    $checkpointC = dashboardCheckpoint($area, 'Pos C');
    dashboardAttachCheckpoint($upcoming, $checkpointC, 1);

    $this->actingAs($admin)
        ->get(route('dashboard'))
        ->assertOk()
        ->assertViewHas('totalRoutes', 2)
        ->assertViewHas('missedCheckpoints', 1);
});

test('incidents today counts all statuses and excludes other dates', function () {
    Carbon::setTestNow('2026-11-08 12:00:00');

    $admin = dashboardUserWithRole('Admin');
    $guard = dashboardUserWithRole('Security Guard');
    $area = dashboardArea();

    $base = [
        'user_id' => $guard->id,
        'area_id' => $area->id,
        'title' => 'Temuan',
        'category' => 'lainnya',
        'description' => 'Deskripsi temuan.',
        'priority' => 'medium',
    ];

    Incident::create($base + ['status' => 'new', 'reported_at' => now()]);
    Incident::create($base + ['status' => 'closed', 'reported_at' => now()]);
    Incident::create($base + ['status' => 'new', 'reported_at' => now()->subDay()]);

    $this->actingAs($admin)
        ->get(route('dashboard'))
        ->assertOk()
        ->assertViewHas('incidentsToday', 2);
});

test('dashboard renders when a recent log checkpoint is soft deleted', function () {
    Carbon::setTestNow('2026-11-08 12:00:00');

    $admin = dashboardUserWithRole('Admin');
    $guard = dashboardUserWithRole('Security Guard');
    $area = dashboardArea();
    $schedule = dashboardSchedule($area, ['status' => 'ongoing']);
    $checkpoint = dashboardCheckpoint($area, 'Pos Dihapus');
    dashboardAttachCheckpoint($schedule, $checkpoint, 1);
    dashboardLog($guard, $schedule, $checkpoint);

    $checkpoint->delete();

    $this->actingAs($admin)
        ->get(route('dashboard'))
        ->assertOk();
});

test('route progress includes scanned counts and status states', function () {
    Carbon::setTestNow('2026-11-08 12:00:00');

    $admin = dashboardUserWithRole('Admin');
    $guard = dashboardUserWithRole('Security Guard');
    $area = dashboardArea();

    $completed = dashboardSchedule($area, [
        'schedule_name' => 'Rute Selesai',
        'start_time' => '08:00:00',
        'end_time' => '17:00:00',
    ]);
    $checkpointA = dashboardCheckpoint($area, 'Pos 1');
    dashboardAttachCheckpoint($completed, $checkpointA, 1);
    dashboardLog($guard, $completed, $checkpointA, 'on_time');

    $ongoing = dashboardSchedule($area, [
        'schedule_name' => 'Rute Berjalan',
        'start_time' => '11:00:00',
        'end_time' => '13:00:00',
    ]);
    $checkpointB = dashboardCheckpoint($area, 'Pos 2');
    $checkpointC = dashboardCheckpoint($area, 'Pos 3');
    dashboardAttachCheckpoint($ongoing, $checkpointB, 1);
    dashboardAttachCheckpoint($ongoing, $checkpointC, 2);
    dashboardLog($guard, $ongoing, $checkpointB, 'on_time');

    $missed = dashboardSchedule($area, [
        'schedule_name' => 'Rute Terlewat',
        'start_time' => '06:00:00',
        'end_time' => '08:00:00',
    ]);
    $checkpointD = dashboardCheckpoint($area, 'Pos 4');
    dashboardAttachCheckpoint($missed, $checkpointD, 1);

    $upcoming = dashboardSchedule($area, [
        'schedule_name' => 'Rute Nanti',
        'start_time' => '18:00:00',
        'end_time' => '20:00:00',
    ]);
    $checkpointE = dashboardCheckpoint($area, 'Pos 5');
    dashboardAttachCheckpoint($upcoming, $checkpointE, 1);

    $this->actingAs($admin)
        ->get(route('dashboard'))
        ->assertOk()
        ->assertViewHas('routeProgress', function (array $routes) {
            $byName = collect($routes)->keyBy('name');

            return $byName['Rute Selesai']['state'] === 'completed'
                && $byName['Rute Selesai']['percent'] === 100
                && $byName['Rute Berjalan']['state'] === 'ongoing'
                && $byName['Rute Berjalan']['scanned'] === 1
                && $byName['Rute Berjalan']['total'] === 2
                && $byName['Rute Berjalan']['percent'] === 50
                && $byName['Rute Terlewat']['state'] === 'missed'
                && $byName['Rute Nanti']['state'] === 'upcoming';
        });
});

test('dashboard builds a seven day scan and incident trend', function () {
    Carbon::setTestNow('2026-11-08 12:00:00');

    $admin = dashboardUserWithRole('Admin');
    $guard = dashboardUserWithRole('Security Guard');
    $area = dashboardArea();
    $schedule = dashboardSchedule($area, ['status' => 'ongoing']);
    $checkpoint = dashboardCheckpoint($area, 'Pos Tren');
    dashboardAttachCheckpoint($schedule, $checkpoint, 1);

    dashboardLog($guard, $schedule, $checkpoint, 'on_time', now());
    dashboardLog($guard, $schedule, $checkpoint, 'on_time', now()->subDay());
    dashboardLog($guard, $schedule, $checkpoint, 'on_time', now()->subDays(8));

    $baseIncident = [
        'user_id' => $guard->id,
        'area_id' => $area->id,
        'title' => 'Temuan tren',
        'category' => 'lainnya',
        'description' => 'Deskripsi.',
        'priority' => 'medium',
        'status' => 'new',
    ];

    Incident::create($baseIncident + ['reported_at' => now()->subDays(2)]);
    Incident::create($baseIncident + ['reported_at' => now()->subDays(8)]);

    $this->actingAs($admin)
        ->get(route('dashboard'))
        ->assertOk()
        ->assertViewHas('trend', function (array $trend) {
            return count($trend) === 7
                && $trend[6]['scans'] === 1
                && $trend[5]['scans'] === 1
                && $trend[4]['scans'] === 0
                && $trend[4]['incidents'] === 1
                && $trend[6]['incidents'] === 0
                && array_sum(array_column($trend, 'scans')) === 2;
        });
});

test('dashboard builds daily route completion for the last seven days', function () {
    Carbon::setTestNow('2026-11-08 12:00:00');

    $admin = dashboardUserWithRole('Admin');
    $guardA = dashboardUserWithRole('Security Guard');
    $guardB = dashboardUserWithRole('Security Guard');
    $area = dashboardArea();

    $routeA = dashboardSchedule($area, ['schedule_name' => 'Rute A', 'status' => 'ongoing']);
    $checkpointA1 = dashboardCheckpoint($area, 'A1');
    $checkpointA2 = dashboardCheckpoint($area, 'A2');
    dashboardAttachCheckpoint($routeA, $checkpointA1, 1);
    dashboardAttachCheckpoint($routeA, $checkpointA2, 2);

    $routeB = dashboardSchedule($area, ['schedule_name' => 'Rute B', 'status' => 'ongoing']);
    $checkpointB1 = dashboardCheckpoint($area, 'B1');
    dashboardAttachCheckpoint($routeB, $checkpointB1, 1);

    // Rute tanpa checkpoint tidak boleh masuk perhitungan.
    dashboardSchedule($area, ['schedule_name' => 'Rute Kosong', 'status' => 'ongoing']);

    // Hari ini: Rute A lengkap, Rute B belum.
    dashboardLog($guardA, $routeA, $checkpointA1, 'on_time', now());
    dashboardLog($guardB, $routeA, $checkpointA2, 'on_time', now());

    // Kemarin: Rute A sebagian, Rute B lengkap.
    dashboardLog($guardA, $routeA, $checkpointA1, 'on_time', now()->subDay());
    dashboardLog($guardA, $routeB, $checkpointB1, 'on_time', now()->subDay());

    $this->actingAs($admin)
        ->get(route('dashboard'))
        ->assertOk()
        ->assertViewHas('dailyCompletion', function (array $days) {
            return count($days) === 7
                && $days[6]['completed'] === 1
                && $days[6]['incomplete'] === 1
                && $days[6]['percent'] === 50
                && $days[5]['completed'] === 1
                && $days[5]['incomplete'] === 1
                && $days[4]['completed'] === 0
                && $days[4]['incomplete'] === 2
                && $days[4]['percent'] === 0;
        });
});
