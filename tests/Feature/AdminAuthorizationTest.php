<?php

use App\Models\User;
use Database\Seeders\RoleSeeder;
use Illuminate\Support\Facades\Route;
use Spatie\Permission\Models\Role;

test('guests are redirected from the admin area', function () {
    $this->get(route('admin.categories.index'))
        ->assertRedirect(route('login'));
});

test('regular users cannot access the admin area', function () {
    $role = Role::create(['name' => 'user']);
    $user = User::factory()->create();
    $user->assignRole($role);

    $this->actingAs($user)
        ->get(route('admin.categories.index'))
        ->assertForbidden();
});

test('admins can access the admin area', function () {
    $role = Role::create(['name' => 'admin']);
    $user = User::factory()->create();
    $user->assignRole($role);

    $this->actingAs($user)
        ->get(route('admin.categories.index'))
        ->assertOk();
});

test('admins can view the user list without unsupported actions', function () {
    $role = Role::create(['name' => 'admin']);
    $user = User::factory()->create();
    $user->assignRole($role);

    $this->actingAs($user)
        ->get(route('admin.users.index'))
        ->assertOk();

    expect(Route::has('admin.users.create'))->toBeFalse();
    expect(Route::has('admin.categories.show'))->toBeFalse();
});

test('role seeding is repeatable and only admins receive admin permissions', function () {
    $this->seed(RoleSeeder::class);
    $this->seed(RoleSeeder::class);

    $admin = Role::query()->where('name', 'admin')->firstOrFail();
    $user = Role::query()->where('name', 'user')->firstOrFail();

    expect($admin->hasPermissionTo('admin.categories.index'))->toBeTrue();
    expect($user->hasPermissionTo('admin.categories.index'))->toBeFalse();
});
