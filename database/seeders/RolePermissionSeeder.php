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
            'role.view', 'role.create', 'role.edit', 'role.delete',
            'report.view', 'report.create', 'report.edit', 'report.delete', 'report.approval',
            'report-design.view', 'report-design.create', 'report-design.edit', 'report-design.delete',
            'custom-script.view', 'custom-script.create', 'custom-script.edit', 'custom-script.delete'
        ];

        foreach ($permissions as $perm) {
            Permission::firstOrCreate(['name' => $perm]);
        }

        $adminRole = Role::firstOrCreate(['name' => 'admin']);
        $supervisorRole = Role::firstOrCreate(['name' => 'supervisor']);
        $officerRole = Role::firstOrCreate(['name' => 'officer']);

        $adminRole->givePermissionTo(Permission::all());
        
        $supervisorRole->givePermissionTo(['dashboard.view', 
                                           'user.view', 'user.create', 'user.edit',
                                           'report.view', 'report.create', 'report.edit', 'report.delete',
                                           'report-design.view', 'report-design.create', 'report-design.edit', 'report-design.delete',
                                           'custom-script.view', 'report-design.create', 'report-design.edit', 'custom-script.delete'
                                        ]);
                                        
        $officerRole->givePermissionTo(['dashboard.view', 'report.view', 'report.create', 'report.edit', 'report.delete',]);

        $adminUser = User::firstOrCreate(
            ['email' => 'admin@local.com'],
            [
                'name' => 'Administrator',
                'password' => Hash::make('password123'),
            ]
        );

        $supervisorUser = User::firstOrCreate(
            ['email' => 'supervisor@local.com'],
            [
                'name' => 'John Doe',
                'password' => Hash::make('password123'),
            ]
        );
    
        $officerUser = User::firstOrCreate(
            ['email' => 'officer@local.com'],
            [
                'name' => 'Anne Lucene',
                'password' => Hash::make('password123'),
            ]
        );

        $adminUser->assignRole($adminRole);
        $supervisorUser->assignRole($supervisorRole);
        $officerUser->assignRole($officerRole);
    }
}
