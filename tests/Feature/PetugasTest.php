<?php

use App\Models\Role;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

function petugasRole(string $roleName): Role
{
    return Role::firstOrCreate(
        ['role_name' => $roleName],
        ['description' => $roleName]
    );
}

function petugasUserWithRole(string $roleName): User
{
    return User::factory()->create(['role_id' => petugasRole($roleName)->id]);
}

test('super admin can view petugas index', function () {
    $superAdmin = petugasUserWithRole('Super Admin');

    $this->actingAs($superAdmin)
        ->get('/petugas')
        ->assertOk()
        ->assertSee('Data Petugas');
});

test('admin can create a petugas with phone and hashed password', function () {
    $admin = petugasUserWithRole('Admin');
    $role = petugasRole('Security Guard');

    $response = $this->actingAs($admin)->post('/petugas', [
        'name' => 'Petugas Baru',
        'username' => 'petugas_baru',
        'email' => 'petugas@prakarsa.com',
        'phone' => '081234567890',
        'role_id' => $role->id,
        'status' => 'active',
        'password' => 'password123',
        'password_confirmation' => 'password123',
    ]);

    $response->assertRedirect(route('petugas.index'));

    $user = User::where('username', 'petugas_baru')->firstOrFail();
    expect($user->phone)->toBe('081234567890');
    expect(Hash::check('password123', $user->password))->toBeTrue();
});

test('store validates unique username', function () {
    $admin = petugasUserWithRole('Admin');
    $existing = User::factory()->create(['username' => 'duplikat']);
    $role = petugasRole('Security Guard');

    $response = $this->actingAs($admin)->post('/petugas', [
        'name' => 'Duplikat',
        'username' => $existing->username,
        'role_id' => $role->id,
        'status' => 'active',
        'password' => 'password123',
        'password_confirmation' => 'password123',
    ]);

    $response->assertSessionHasErrors('username');
});

test('supervisor can update a petugas without changing the password', function () {
    $supervisor = petugasUserWithRole('Supervisor');
    $role = petugasRole('Security Guard');
    $petugas = User::factory()->create(['role_id' => $role->id, 'name' => 'Nama Lama']);
    $oldPassword = $petugas->password;

    $this->actingAs($supervisor)->put(route('petugas.update', $petugas), [
        'name' => 'Nama Baru',
        'username' => $petugas->username,
        'email' => $petugas->email,
        'phone' => '081200000000',
        'role_id' => $role->id,
        'status' => 'active',
        'password' => '',
        'password_confirmation' => '',
    ])->assertRedirect(route('petugas.index'));

    $petugas->refresh();
    expect($petugas->name)->toBe('Nama Baru');
    expect($petugas->password)->toBe($oldPassword);
});

test('role without master data access is forbidden', function () {
    $auditor = petugasUserWithRole('Auditor');

    $this->actingAs($auditor)
        ->get('/petugas')
        ->assertForbidden();
});

test('security guard is redirected to member area', function () {
    $guard = petugasUserWithRole('Security Guard');

    $this->actingAs($guard)
        ->get('/petugas')
        ->assertRedirect(route('member.dashboard'));
});

test('guest is redirected to login', function () {
    $this->get('/petugas')->assertRedirect(route('login'));
});

test('toggle status changes the petugas status', function () {
    $superAdmin = petugasUserWithRole('Super Admin');
    $petugas = User::factory()->create(['status' => 'active']);

    $response = $this->actingAs($superAdmin)
        ->patchJson(route('petugas.toggle-status', $petugas));

    $response->assertOk()->assertJson(['success' => true, 'new_status' => 'inactive']);
    expect($petugas->refresh()->status)->toBe('inactive');
});

test('user can not toggle their own status', function () {
    $superAdmin = petugasUserWithRole('Super Admin');

    $this->actingAs($superAdmin)
        ->patchJson(route('petugas.toggle-status', $superAdmin))
        ->assertForbidden()
        ->assertJson(['success' => false]);
});

test('user can not delete their own account', function () {
    $superAdmin = petugasUserWithRole('Super Admin');

    $this->actingAs($superAdmin)
        ->from(route('petugas.index'))
        ->delete(route('petugas.destroy', $superAdmin))
        ->assertRedirect(route('petugas.index'))
        ->assertSessionHas('error');

    expect(User::find($superAdmin->id))->not->toBeNull();
});

test('deleting a petugas frees the username for reuse', function () {
    $superAdmin = petugasUserWithRole('Super Admin');
    $role = petugasRole('Security Guard');
    $petugas = User::factory()->create([
        'username' => 'budi',
        'role_id' => $role->id,
    ]);

    $this->actingAs($superAdmin)
        ->delete(route('petugas.destroy', $petugas))
        ->assertRedirect(route('petugas.index'));

    expect(User::withTrashed()->find($petugas->id)->trashed())->toBeTrue();

    $this->actingAs($superAdmin)->post('/petugas', [
        'name' => 'Budi Baru',
        'username' => 'budi',
        'role_id' => $role->id,
        'status' => 'active',
        'password' => 'password123',
        'password_confirmation' => 'password123',
    ])->assertRedirect(route('petugas.index'));

    expect(User::where('username', 'budi')->whereNull('deleted_at')->first())->not->toBeNull();
});
