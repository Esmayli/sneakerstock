<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $admin = Role::firstOrCreate(['name' => 'admin', 'guard_name' => 'web']);
        $user = Role::firstOrCreate(['name' => 'user', 'guard_name' => 'web']);

        $adminPermissions = [
            'home',
            'admin.categories.index',
            'admin.categories.create',
            'admin.categories.edit',
            'admin.categories.update',
            'admin.categories.destroy',
            'admin.users.index',
        ];

        foreach ($adminPermissions as $permission) {
            Permission::firstOrCreate(['name' => $permission, 'guard_name' => 'web']);
        }

        $admin->syncPermissions($adminPermissions);
        $user->syncPermissions(['home']);
    }
}
