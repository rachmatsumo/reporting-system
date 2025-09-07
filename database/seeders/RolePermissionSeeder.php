<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class RolePermissionSeeder extends Seeder
{
    public function run()
    {
        $permissions = [
            'dashboard.view',
            'activity-log.view',
            'user.view', 'user.create', 'user.edit',
            'menu.view', 'menu.create', 'menu.edit', 'menu.delete',
            'permission.view', 'permission.create', 'permission.edit', 'permission.delete',
            'role.view', 'role.create', 'role.edit', 'role.delete'
        ];

        foreach ($permissions as $perm) {
            Permission::firstOrCreate(['name' => $perm]);
        }

        $adminRole = Role::firstOrCreate(['name' => 'admin']);
        $userRole = Role::firstOrCreate(['name' => 'user']);

        $adminRole->givePermissionTo(Permission::all());
        $userRole->givePermissionTo(['dashboard.view']);

        $adminUser = User::firstOrCreate(
            ['email' => 'admin@local.com'],
            [
                'name' => 'Administrator',
                'password' => Hash::make('password123'),
            ]
        );

        $userUser = User::firstOrCreate(
            ['email' => 'user@local.com'],
            [
                'name' => 'John Doe',
                'password' => Hash::make('password123'),
            ]
        );

        $adminUser->assignRole($adminRole);
        $userUser->assignRole($userRole);
    }
}
