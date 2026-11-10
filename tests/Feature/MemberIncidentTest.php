<?php

use App\Models\Area;
use App\Models\Incident;
use App\Models\Role;
use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

function memberIncidentUserWithRole(string $roleName): User
{
    $role = Role::firstOrCreate(
        ['role_name' => $roleName],
        ['description' => $roleName]
    );

    return User::factory()->create(['role_id' => $role->id]);
}

function memberIncidentArea(string $code = 'AREA-01'): Area
{
    return Area::create([
        'area_name' => 'Area Test '.$code,
        'area_code' => $code,
        'description' => 'Area untuk pengujian laporan insiden.',
    ]);
}

function memberIncidentPhotos(int $count = 3): array
{
    return array_map(
        fn (int $index) => UploadedFile::fake()->image("bukti-insiden-{$index}.png", 800, 600),
        range(1, $count)
    );
}

function memberIncidentPayload(Area $area, array $overrides = []): array
{
    return array_merge([
        'title' => 'Kebocoran pipa di area parkir',
        'category' => 'kebocoran',
        'priority' => 'high',
        'area_id' => $area->id,
        'description' => 'Pipa bocor dan air menggenangi area parkir kendaraan operasional.',
        'photos' => memberIncidentPhotos(),
        'latitude' => -6.2000000,
        'longitude' => 106.8166667,
    ], $overrides);
}

test('member can submit an incident report with photo and initial history', function () {
    Storage::fake('public');

    $guard = memberIncidentUserWithRole('Security Guard');
    $area = memberIncidentArea();

    $response = $this->actingAs($guard)
        ->post(route('member.incidents.store'), memberIncidentPayload($area), [
            'Accept' => 'application/json',
        ]);

    $response->assertCreated()
        ->assertJsonPath('success', true)
        ->assertJsonStructure(['incident' => ['id', 'code', 'title']]);

    $incident = Incident::firstOrFail();

    expect($incident->user_id)->toBe($guard->id);
    expect($incident->area_id)->toBe($area->id);
    expect($incident->status)->toBe('new');
    expect($incident->sync_status)->toBe('synced');
    expect($incident->photo)->toStartWith('incident-photos/');
    expect($incident->reported_at)->not->toBeNull();

    expect($incident->photos)->toHaveCount(3);
    expect($incident->photo)->toBe($incident->photos->first()->photo_path);
    $incident->photos->each(
        fn ($photo) => Storage::disk('public')->assertExists($photo->photo_path)
    );

    $this->assertDatabaseCount('incident_photos', 3);

    $this->assertDatabaseHas('incident_status_history', [
        'incident_id' => $incident->id,
        'status' => 'new',
        'changed_by' => $guard->id,
    ]);

    $response->assertJsonPath('incident.code', '#INC-'.str_pad((string) $incident->id, 3, '0', STR_PAD_LEFT));
});

test('photo is required to submit an incident report', function () {
    Storage::fake('public');

    $guard = memberIncidentUserWithRole('Security Guard');
    $area = memberIncidentArea();
    $payload = memberIncidentPayload($area);
    unset($payload['photos']);

    $this->actingAs($guard)
        ->post(route('member.incidents.store'), $payload, ['Accept' => 'application/json'])
        ->assertStatus(422)
        ->assertJsonValidationErrors('photos');

    expect(Incident::count())->toBe(0);
});

test('incident report requires at least three photos', function () {
    Storage::fake('public');

    $guard = memberIncidentUserWithRole('Security Guard');
    $area = memberIncidentArea();

    $this->actingAs($guard)
        ->post(route('member.incidents.store'), memberIncidentPayload($area, [
            'photos' => memberIncidentPhotos(2),
        ]), ['Accept' => 'application/json'])
        ->assertStatus(422)
        ->assertJsonValidationErrors('photos');

    expect(Incident::count())->toBe(0);
});

test('incident report accepts at most five photos', function () {
    Storage::fake('public');

    $guard = memberIncidentUserWithRole('Security Guard');
    $area = memberIncidentArea();

    $this->actingAs($guard)
        ->post(route('member.incidents.store'), memberIncidentPayload($area, [
            'photos' => memberIncidentPhotos(6),
        ]), ['Accept' => 'application/json'])
        ->assertStatus(422)
        ->assertJsonValidationErrors('photos');

    expect(Incident::count())->toBe(0);
});

test('incident report rejects invalid category and priority', function () {
    Storage::fake('public');

    $guard = memberIncidentUserWithRole('Security Guard');
    $area = memberIncidentArea();

    $this->actingAs($guard)
        ->post(route('member.incidents.store'), memberIncidentPayload($area, [
            'category' => 'tidak_valid',
            'priority' => 'super_urgent',
        ]), ['Accept' => 'application/json'])
        ->assertStatus(422)
        ->assertJsonValidationErrors(['category', 'priority']);
});

test('incident report rejects a deleted area', function () {
    Storage::fake('public');

    $guard = memberIncidentUserWithRole('Security Guard');
    $area = memberIncidentArea();
    $area->delete();

    $this->actingAs($guard)
        ->post(route('member.incidents.store'), memberIncidentPayload($area), ['Accept' => 'application/json'])
        ->assertStatus(422)
        ->assertJsonValidationErrors('area_id');
});

test('non security guard roles can not submit an incident report', function (string $roleName) {
    Storage::fake('public');

    $user = memberIncidentUserWithRole($roleName);
    $area = memberIncidentArea();

    $this->actingAs($user)
        ->post(route('member.incidents.store'), memberIncidentPayload($area), ['Accept' => 'application/json'])
        ->assertForbidden();

    expect(Incident::count())->toBe(0);
})->with(['Admin', 'Supervisor', 'Auditor']);

test('super admin bypasses the incident create policy', function () {
    Storage::fake('public');

    $superAdmin = memberIncidentUserWithRole('Super Admin');
    $area = memberIncidentArea();

    $this->actingAs($superAdmin)
        ->post(route('member.incidents.store'), memberIncidentPayload($area), ['Accept' => 'application/json'])
        ->assertCreated();
});

test('admin and supervisor can view the incident list', function (string $roleName) {
    $user = memberIncidentUserWithRole($roleName);

    $this->actingAs($user)
        ->get(route('incidents.index'))
        ->assertOk();
})->with(['Admin', 'Supervisor']);

test('auditor can not view the incident list', function () {
    $auditor = memberIncidentUserWithRole('Auditor');

    $this->actingAs($auditor)
        ->get(route('incidents.index'))
        ->assertForbidden();
});

test('incident report trigger is hidden for admin', function () {
    $admin = memberIncidentUserWithRole('Admin');
    memberIncidentArea();

    $this->actingAs($admin)
        ->get(route('member.dashboard'))
        ->assertOk()
        ->assertDontSee('open-incident-modal', false)
        ->assertDontSee(route('member.incidents.store'), false);
});

test('guest can not submit an incident report', function () {
    $area = memberIncidentArea();

    $this->post(route('member.incidents.store'), memberIncidentPayload($area))
        ->assertRedirect(route('login'));

    expect(Incident::count())->toBe(0);
});

test('member layout exposes the incident report trigger and modal', function () {
    $guard = memberIncidentUserWithRole('Security Guard');
    memberIncidentArea();

    $this->actingAs($guard)
        ->get(route('member.dashboard'))
        ->assertOk()
        ->assertSee('Lapor Insiden')
        ->assertSee('Laporkan Insiden / Temuan')
        ->assertSee('open-incident-modal', false)
        ->assertSee(route('member.incidents.store'), false);
});
