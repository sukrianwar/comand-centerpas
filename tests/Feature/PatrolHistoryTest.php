<?php

use App\Exports\PatrolHistoryExport;
use App\Models\Area;
use App\Models\Checkpoint;
use App\Models\PatrolLog;
use App\Models\PatrolSchedule;
use App\Models\Role;
use App\Models\Shift;
use App\Models\User;
use Carbon\Carbon;
use Maatwebsite\Excel\Facades\Excel;

function patrolHistoryUserWithRole(string $roleName): User
{
    $role = Role::firstOrCreate(
        ['role_name' => $roleName],
        ['description' => $roleName]
    );

    return User::factory()->create(['role_id' => $role->id]);
}

function patrolHistoryArea(string $code = 'AREA-H1'): Area
{
    return Area::create([
        'area_name' => 'Area '.$code,
        'area_code' => $code,
        'description' => 'Area pengujian riwayat patroli.',
    ]);
}

function patrolHistorySchedule(Area $area, string $name = 'Rute Pagi'): PatrolSchedule
{
    $shift = Shift::firstOrCreate(
        ['shift_name' => 'Pagi'],
        ['start_time' => '07:00:00', 'end_time' => '15:00:00', 'status' => 'active']
    );

    return PatrolSchedule::create([
        'schedule_name' => $name,
        'area_id' => $area->id,
        'shift_id' => $shift->id,
        'round_number' => 1,
        'start_time' => '08:00:00',
        'end_time' => '12:00:00',
        'status' => 'scheduled',
    ]);
}

function patrolHistoryCheckpoint(Area $area, string $name = 'Pos 1'): Checkpoint
{
    return Checkpoint::create([
        'area_id' => $area->id,
        'checkpoint_name' => $name,
        'status' => 'active',
    ]);
}

function patrolHistoryLog(
    User $guard,
    Area $area,
    string $checkpointName,
    Carbon $scannedAt,
    string $status = 'on_time',
    ?PatrolSchedule $schedule = null,
): PatrolLog {
    $schedule ??= patrolHistorySchedule($area);
    $checkpoint = patrolHistoryCheckpoint($area, $checkpointName);

    $log = PatrolLog::create([
        'patrol_schedule_id' => $schedule->id,
        'checkpoint_id' => $checkpoint->id,
        'user_id' => $guard->id,
        'scanned_at' => $scannedAt,
        'photo_path' => 'patrol-photos/'.$checkpoint->id.'.jpg',
        'status' => $status,
        'notes' => 'Catatan '.$checkpointName,
    ]);

    foreach (range(1, 3) as $index) {
        $log->photos()->create([
            'photo_path' => 'patrol-photos/'.$checkpoint->id.'-'.$index.'.jpg',
        ]);
    }

    return $log;
}

test('admin can view patrol history monitoring', function () {
    $admin = patrolHistoryUserWithRole('Admin');
    $guard = patrolHistoryUserWithRole('Security Guard');
    $area = patrolHistoryArea();
    patrolHistoryLog($guard, $area, 'Pos Alpha', now());

    $this->actingAs($admin)
        ->get(route('patrol-history.index'))
        ->assertOk()
        ->assertSee('Riwayat Patroli')
        ->assertSee('Pos Alpha')
        ->assertSee($guard->name)
        ->assertSee('Export Excel');
});

test('supervisor and super admin can view patrol history', function (string $roleName) {
    $user = patrolHistoryUserWithRole($roleName);
    $guard = patrolHistoryUserWithRole('Security Guard');
    $area = patrolHistoryArea();
    patrolHistoryLog($guard, $area, 'Pos Beta', now());

    $this->actingAs($user)
        ->get(route('patrol-history.index'))
        ->assertOk()
        ->assertSee('Pos Beta');
})->with(['Supervisor', 'Super Admin']);

test('auditor can not view patrol history', function () {
    $auditor = patrolHistoryUserWithRole('Auditor');

    $this->actingAs($auditor)
        ->get(route('patrol-history.index'))
        ->assertForbidden();
});

test('security guard is redirected from patrol history', function () {
    $guard = patrolHistoryUserWithRole('Security Guard');

    $this->actingAs($guard)
        ->get(route('patrol-history.index'))
        ->assertRedirect(route('member.dashboard'));
});

test('patrol history defaults to today only', function () {
    $admin = patrolHistoryUserWithRole('Admin');
    $guard = patrolHistoryUserWithRole('Security Guard');
    $area = patrolHistoryArea();

    patrolHistoryLog($guard, $area, 'Pos Hari Ini', now());
    patrolHistoryLog($guard, $area, 'Pos Kemarin', now()->subDay());

    $this->actingAs($admin)
        ->get(route('patrol-history.index'))
        ->assertOk()
        ->assertSee('Pos Hari Ini')
        ->assertDontSee('Pos Kemarin')
        ->assertViewHas('stats', fn (array $stats) => $stats['total'] === 1);
});

test('patrol history can be filtered by area and status', function () {
    $admin = patrolHistoryUserWithRole('Admin');
    $guardA = patrolHistoryUserWithRole('Security Guard');
    $guardB = patrolHistoryUserWithRole('Security Guard');
    $areaA = patrolHistoryArea('AREA-F1');
    $areaB = patrolHistoryArea('AREA-F2');
    $scheduleA = patrolHistorySchedule($areaA, 'Rute Alpha');
    $scheduleB = patrolHistorySchedule($areaB, 'Rute Beta');

    patrolHistoryLog($guardA, $areaA, 'Pos Alpha', now(), 'on_time', $scheduleA);
    patrolHistoryLog($guardB, $areaB, 'Pos Beta', now(), 'late', $scheduleB);

    $this->actingAs($admin)
        ->get(route('patrol-history.index', ['area_id' => $areaA->id]))
        ->assertOk()
        ->assertSee('Pos Alpha')
        ->assertDontSee('Pos Beta')
        ->assertViewHas('stats', fn (array $stats) => $stats['total'] === 1);

    $this->actingAs($admin)
        ->get(route('patrol-history.index', ['status' => 'late']))
        ->assertOk()
        ->assertSee('Pos Beta')
        ->assertDontSee('Pos Alpha');

    $this->actingAs($admin)
        ->get(route('patrol-history.index', ['search' => 'Alpha']))
        ->assertOk()
        ->assertSee('Pos Alpha')
        ->assertDontSee('Pos Beta');
});

test('patrol history detail data includes photo and shift', function () {
    $admin = patrolHistoryUserWithRole('Admin');
    $guard = patrolHistoryUserWithRole('Security Guard');
    $area = patrolHistoryArea();
    $log = patrolHistoryLog($guard, $area, 'Pos Detail', now());

    $this->actingAs($admin)
        ->get(route('patrol-history.index'))
        ->assertOk()
        ->assertViewHas('logsJson', function (array $logs) use ($log, $area) {
            $detail = collect($logs)->firstWhere('id', $log->id);

            return $detail !== null
                && $detail['checkpoint'] === 'Pos Detail'
                && $detail['area'] === $area->area_name
                && $detail['shift'] === 'Pagi'
                && count($detail['photo_urls']) === 3;
        });
});

test('patrol history excel export does not include username column', function () {
    $headings = (new PatrolHistoryExport(PatrolLog::query()))->headings();

    expect($headings)->toBe([
        'No',
        'Waktu Scan',
        'Petugas',
        'Checkpoint',
        'Rute',
        'Area',
        'Shift',
        'Status',
        'Catatan',
        'Link Foto',
    ]);
});

test('admin can export patrol history to excel', function () {
    $admin = patrolHistoryUserWithRole('Admin');
    $guard = patrolHistoryUserWithRole('Security Guard');
    $area = patrolHistoryArea();
    patrolHistoryLog($guard, $area, 'Pos Export', now());

    $this->actingAs($admin)
        ->get(route('patrol-history.export'))
        ->assertOk()
        ->assertDownload();
});

test('patrol history export respects active filters', function () {
    $this->travelTo(Carbon::parse('2026-10-23 10:00:00'));

    $admin = patrolHistoryUserWithRole('Admin');
    $guard = patrolHistoryUserWithRole('Security Guard');
    $area = patrolHistoryArea();

    patrolHistoryLog($guard, $area, 'Pos Tepat', now(), 'on_time');
    patrolHistoryLog($guard, $area, 'Pos Terlambat', now(), 'late');

    Excel::fake();

    $this->actingAs($admin)
        ->get(route('patrol-history.export', ['status' => 'late']))
        ->assertOk();

    Excel::assertDownloaded(
        'riwayat-patroli-20261023-100000.xlsx',
        fn (PatrolHistoryExport $export) => $export->query()->count() === 1
    );
});

test('auditor can not export patrol history', function () {
    $auditor = patrolHistoryUserWithRole('Auditor');

    $this->actingAs($auditor)
        ->get(route('patrol-history.export'))
        ->assertForbidden();
});

test('dashboard links to patrol history monitoring', function () {
    $admin = patrolHistoryUserWithRole('Admin');

    $this->actingAs($admin)
        ->get(route('dashboard'))
        ->assertOk()
        ->assertSee(route('patrol-history.index'), false);
});

test('auditor does not see the patrol history link on dashboard', function () {
    $auditor = patrolHistoryUserWithRole('Auditor');

    $this->actingAs($auditor)
        ->get(route('dashboard'))
        ->assertOk()
        ->assertDontSee(route('patrol-history.index'), false);
});
