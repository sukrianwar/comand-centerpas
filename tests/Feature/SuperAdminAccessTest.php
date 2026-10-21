<?php

use App\Models\PatrolSchedule;
use App\Models\Role;
use App\Models\User;
use Illuminate\Support\Facades\Gate;

function makeUserWithRole(string $roleName): User
{
    $role = Role::firstOrCreate(
        ['role_name' => $roleName],
        ['description' => $roleName]
    );

    return User::factory()->create(['role_id' => $role->id]);
}

test('super admin can access command center and master data pages', function () {
    $superAdmin = makeUserWithRole('Super Admin');

    $this->actingAs($superAdmin)
        ->get('/dashboard')
        ->assertOk();

    $this->actingAs($superAdmin)
        ->get('/patrol-schedules')
        ->assertOk();

    $this->actingAs($superAdmin)
        ->get('/petugas')
        ->assertOk();

    $this->actingAs($superAdmin)
        ->get('/member')
        ->assertOk()
        ->assertSee('Super Admin');

    $this->actingAs($superAdmin)
        ->get('/member/profile')
        ->assertOk()
        ->assertSee($superAdmin->name)
        ->assertSee('Super Admin');
});

test('super admin bypasses all policy checks', function () {
    $superAdmin = makeUserWithRole('Super Admin');

    expect(Gate::forUser($superAdmin)->allows('create', PatrolSchedule::class))->toBeTrue();
    expect(Gate::forUser($superAdmin)->allows('delete', new PatrolSchedule))->toBeTrue();
    expect($superAdmin->isSuperAdmin())->toBeTrue();
});

test('admin and supervisor can manage master data but are not super admin', function () {
    $admin = makeUserWithRole('Admin');
    $supervisor = makeUserWithRole('Supervisor');

    expect($admin->canManageMasterData())->toBeTrue();
    expect($supervisor->canManageMasterData())->toBeTrue();
    expect($admin->isSuperAdmin())->toBeFalse();
    expect($supervisor->isSuperAdmin())->toBeFalse();
});

test('security guard cannot access command center', function () {
    $guard = makeUserWithRole('Security Guard');

    $this->actingAs($guard)
        ->get('/dashboard')
        ->assertRedirect(route('member.dashboard'));
});
