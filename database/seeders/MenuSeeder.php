<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Menu;

class MenuSeeder extends Seeder
{
    public function run()
    {
        Menu::insert([
            ['title' => 'Dashboard', 'icon' => 'grid-fill', 'route' => 'dashboard', 'permission' => 'dashboard.view', 'parent_id' => null, 'order' => 1],
            ['title' => 'Report', 'icon' => 'folder2-open', 'route' => 'reports.index', 'permission' => 'report.view', 'parent_id' => null, 'order' => 2],
            ['title' => 'Design Report', 'icon' => 'palette', 'route' => 'report-designs.index', 'permission' => 'report-design.view', 'parent_id' => null, 'order' => 3],
            ['title' => 'Custom Script', 'icon' => 'code-slash', 'route' => 'custom-scripts.index', 'permission' => 'custom-script.view', 'parent_id' => null, 'order' => 4],
            
            ['title' => 'Admin', 'icon' => 'shield', 'route' => null, 'permission' => null, 'parent_id' => null, 'order' => 5],
                ['title' => 'Menu', 'icon' => null, 'route' => 'menus.index', 'permission' => 'menu.view', 'parent_id' => 5, 'order' => 1],
                ['title' => 'Users', 'icon' => null, 'route' => 'users.index', 'permission' => 'user.view', 'parent_id' => 5, 'order' => 2],
                ['title' => 'Access Control', 'icon' => null, 'route' => null, 'permission' => null, 'parent_id' => 5, 'order' => 2],
                    ['title' => 'Activity Log', 'icon' => null, 'route' => 'activity-log.index', 'permission' => 'activity-log.view', 'parent_id' => 8, 'order' => 1],
                    ['title' => 'Permission', 'icon' => null, 'route' => 'permissions.index', 'permission' => 'permission.view', 'parent_id' => 8, 'order' => 2],
                    ['title' => 'Roles', 'icon' => null, 'route' => 'roles.index', 'permission' => 'role.view', 'parent_id' => 8, 'order' => 3],
        ]);
    }
}
