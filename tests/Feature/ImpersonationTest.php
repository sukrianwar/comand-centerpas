<?php

use App\Models\Role;
use App\Models\User;

function impersonationUserWithRole(string $roleName, array $attributes = []): User
{
    $role = Role::firstOrCreate(
        ['role_name' => $roleName],
        ['description' => $roleName]
    );

    return User::factory()->create(array_merge([
        'role_id' => $role->id,
    ], $attributes));
}

test('super admin can impersonate an active admin', function () {
    $superAdmin = impersonationUserWithRole('Super Admin');
    $target = impersonationUserWithRole('Admin');

    $response = $this->actingAs($superAdmin)
        ->post(route('petugas.impersonate', $target));

    $response->assertRedirect(route('dashboard'));
    $this->assertAuthenticatedAs($target);
    $response->assertSessionHas('impersonator_id', $superAdmin->id);
});

test('super admin impersonating a security guard is redirected to member area', function () {
    $superAdmin = impersonationUserWithRole('Super Admin');
    $guard = impersonationUserWithRole('Security Guard');

    $response = $this->actingAs($superAdmin)
        ->post(route('petugas.impersonate', $guard));

    $response->assertRedirect(route('member.dashboard'));
    $this->assertAuthenticatedAs($guard);
});

test('admin can not impersonate another user', function () {
    $admin = impersonationUserWithRole('Admin');
    $target = impersonationUserWithRole('Security Guard');

    $this->actingAs($admin)
        ->post(route('petugas.impersonate', $target))
        ->assertForbidden();

    $this->assertAuthenticatedAs($admin);
    $this->assertFalse(session()->has('impersonator_id'));
});

test('super admin can not impersonate themselves', function () {
    $superAdmin = impersonationUserWithRole('Super Admin');

    $this->actingAs($superAdmin)
        ->post(route('petugas.impersonate', $superAdmin))
        ->assertForbidden();

    $this->assertAuthenticatedAs($superAdmin);
});

test('super admin can not impersonate an inactive user', function () {
    $superAdmin = impersonationUserWithRole('Super Admin');
    $target = impersonationUserWithRole('Admin', ['status' => 'inactive']);

    $this->actingAs($superAdmin)
        ->post(route('petugas.impersonate', $target))
        ->assertForbidden();

    $this->assertAuthenticatedAs($superAdmin);
});

test('super admin can not impersonate another super admin', function () {
    $superAdmin = impersonationUserWithRole('Super Admin');
    $otherSuperAdmin = impersonationUserWithRole('Super Admin');

    $this->actingAs($superAdmin)
        ->post(route('petugas.impersonate', $otherSuperAdmin))
        ->assertForbidden();

    $this->assertAuthenticatedAs($superAdmin);
});

test('super admin can not start impersonation while already impersonating', function () {
    $superAdmin = impersonationUserWithRole('Super Admin');
    $target = impersonationUserWithRole('Admin');

    $this->actingAs($superAdmin)
        ->withSession(['impersonator_id' => $superAdmin->id])
        ->post(route('petugas.impersonate', $target))
        ->assertForbidden();
});

test('super admin can stop impersonating and return to their account', function () {
    $superAdmin = impersonationUserWithRole('Super Admin');
    $target = impersonationUserWithRole('Admin');

    $this->actingAs($superAdmin)
        ->post(route('petugas.impersonate', $target))
        ->assertRedirect(route('dashboard'));

    $this->post(route('impersonation.stop'))
        ->assertRedirect(route('petugas.index'))
        ->assertSessionMissing('impersonator_id');

    $this->assertAuthenticatedAs($superAdmin);
});

test('impersonated security guard can return to the super admin account', function () {
    $superAdmin = impersonationUserWithRole('Super Admin');
    $guard = impersonationUserWithRole('Security Guard');

    $this->actingAs($superAdmin)
        ->post(route('petugas.impersonate', $guard))
        ->assertRedirect(route('member.dashboard'));

    $this->post(route('impersonation.stop'))
        ->assertRedirect(route('petugas.index'));

    $this->assertAuthenticatedAs($superAdmin);
});

test('stop logs out when the original super admin is no longer valid', function () {
    $superAdmin = impersonationUserWithRole('Super Admin');
    $target = impersonationUserWithRole('Admin');

    $this->actingAs($superAdmin)
        ->post(route('petugas.impersonate', $target));

    $superAdmin->update(['status' => 'inactive']);

    $this->post(route('impersonation.stop'))
        ->assertRedirect(route('login'));

    $this->assertGuest();
    $this->assertFalse(session()->has('impersonator_id'));
});

test('impersonate button is only shown to super admin for valid targets', function () {
    $superAdmin = impersonationUserWithRole('Super Admin');
    $admin = impersonationUserWithRole('Admin');
    $target = impersonationUserWithRole('Security Guard');
    $inactive = impersonationUserWithRole('Security Guard', ['status' => 'inactive']);

    $this->actingAs($superAdmin)
        ->get('/petugas')
        ->assertSee(route('petugas.impersonate', $target), false)
        ->assertDontSee(route('petugas.impersonate', $inactive), false)
        ->assertDontSee(route('petugas.impersonate', $superAdmin), false);

    $this->actingAs($admin)
        ->get('/petugas')
        ->assertDontSee(route('petugas.impersonate', $target), false);
});

test('impersonation banner is shown in command center and member area', function () {
    $superAdmin = impersonationUserWithRole('Super Admin');
    $admin = impersonationUserWithRole('Admin');
    $guard = impersonationUserWithRole('Security Guard');

    $this->actingAs($superAdmin)
        ->post(route('petugas.impersonate', $admin));

    $this->get(route('dashboard'))
        ->assertOk()
        ->assertSee('Mode impersonasi aktif')
        ->assertSee('Kembali ke '.$superAdmin->name);

    $this->post(route('impersonation.stop'));

    $this->actingAs($superAdmin)
        ->post(route('petugas.impersonate', $guard));

    $this->get(route('member.dashboard'))
        ->assertOk()
        ->assertSee('Mode impersonasi aktif')
        ->assertSee('Kembali ke '.$superAdmin->name);
});

test('impersonation banner disappears after returning to the super admin', function () {
    $superAdmin = impersonationUserWithRole('Super Admin');
    $target = impersonationUserWithRole('Admin');

    $this->actingAs($superAdmin)
        ->post(route('petugas.impersonate', $target));

    $this->post(route('impersonation.stop'));

    $this->get(route('dashboard'))
        ->assertOk()
        ->assertDontSee('Mode impersonasi aktif');
});

test('guest can not stop impersonation', function () {
    $this->post(route('impersonation.stop'))
        ->assertRedirect(route('login'));
});
